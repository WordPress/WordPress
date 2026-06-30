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

// package-external:@wordpress/data
var require_data = __commonJS({
  "package-external:@wordpress/data"(exports, module) {
    module.exports = window.wp.data;
  }
});

// node_modules/fast-deep-equal/es6/index.js
var require_es6 = __commonJS({
  "node_modules/fast-deep-equal/es6/index.js"(exports, module) {
    "use strict";
    module.exports = function equal(a2, b2) {
      if (a2 === b2) return true;
      if (a2 && b2 && typeof a2 == "object" && typeof b2 == "object") {
        if (a2.constructor !== b2.constructor) return false;
        var length, i2, keys;
        if (Array.isArray(a2)) {
          length = a2.length;
          if (length != b2.length) return false;
          for (i2 = length; i2-- !== 0; )
            if (!equal(a2[i2], b2[i2])) return false;
          return true;
        }
        if (a2 instanceof Map && b2 instanceof Map) {
          if (a2.size !== b2.size) return false;
          for (i2 of a2.entries())
            if (!b2.has(i2[0])) return false;
          for (i2 of a2.entries())
            if (!equal(i2[1], b2.get(i2[0]))) return false;
          return true;
        }
        if (a2 instanceof Set && b2 instanceof Set) {
          if (a2.size !== b2.size) return false;
          for (i2 of a2.entries())
            if (!b2.has(i2[0])) return false;
          return true;
        }
        if (ArrayBuffer.isView(a2) && ArrayBuffer.isView(b2)) {
          length = a2.length;
          if (length != b2.length) return false;
          for (i2 = length; i2-- !== 0; )
            if (a2[i2] !== b2[i2]) return false;
          return true;
        }
        if (a2.constructor === RegExp) return a2.source === b2.source && a2.flags === b2.flags;
        if (a2.valueOf !== Object.prototype.valueOf) return a2.valueOf() === b2.valueOf();
        if (a2.toString !== Object.prototype.toString) return a2.toString() === b2.toString();
        keys = Object.keys(a2);
        length = keys.length;
        if (length !== Object.keys(b2).length) return false;
        for (i2 = length; i2-- !== 0; )
          if (!Object.prototype.hasOwnProperty.call(b2, keys[i2])) return false;
        for (i2 = length; i2-- !== 0; ) {
          var key = keys[i2];
          if (!equal(a2[key], b2[key])) return false;
        }
        return true;
      }
      return a2 !== a2 && b2 !== b2;
    };
  }
});

// package-external:@wordpress/date
var require_date = __commonJS({
  "package-external:@wordpress/date"(exports, module) {
    module.exports = window.wp.date;
  }
});

// node_modules/deepmerge/dist/cjs.js
var require_cjs = __commonJS({
  "node_modules/deepmerge/dist/cjs.js"(exports, module) {
    "use strict";
    var isMergeableObject = function isMergeableObject2(value) {
      return isNonNullObject(value) && !isSpecial(value);
    };
    function isNonNullObject(value) {
      return !!value && typeof value === "object";
    }
    function isSpecial(value) {
      var stringValue = Object.prototype.toString.call(value);
      return stringValue === "[object RegExp]" || stringValue === "[object Date]" || isReactElement(value);
    }
    var canUseSymbol = typeof Symbol === "function" && Symbol.for;
    var REACT_ELEMENT_TYPE = canUseSymbol ? /* @__PURE__ */ Symbol.for("react.element") : 60103;
    function isReactElement(value) {
      return value.$$typeof === REACT_ELEMENT_TYPE;
    }
    function emptyTarget(val) {
      return Array.isArray(val) ? [] : {};
    }
    function cloneUnlessOtherwiseSpecified(value, options) {
      return options.clone !== false && options.isMergeableObject(value) ? deepmerge(emptyTarget(value), value, options) : value;
    }
    function defaultArrayMerge(target, source, options) {
      return target.concat(source).map(function(element) {
        return cloneUnlessOtherwiseSpecified(element, options);
      });
    }
    function getMergeFunction(key, options) {
      if (!options.customMerge) {
        return deepmerge;
      }
      var customMerge = options.customMerge(key);
      return typeof customMerge === "function" ? customMerge : deepmerge;
    }
    function getEnumerableOwnPropertySymbols(target) {
      return Object.getOwnPropertySymbols ? Object.getOwnPropertySymbols(target).filter(function(symbol) {
        return Object.propertyIsEnumerable.call(target, symbol);
      }) : [];
    }
    function getKeys(target) {
      return Object.keys(target).concat(getEnumerableOwnPropertySymbols(target));
    }
    function propertyIsOnObject(object, property) {
      try {
        return property in object;
      } catch (_) {
        return false;
      }
    }
    function propertyIsUnsafe(target, key) {
      return propertyIsOnObject(target, key) && !(Object.hasOwnProperty.call(target, key) && Object.propertyIsEnumerable.call(target, key));
    }
    function mergeObject(target, source, options) {
      var destination = {};
      if (options.isMergeableObject(target)) {
        getKeys(target).forEach(function(key) {
          destination[key] = cloneUnlessOtherwiseSpecified(target[key], options);
        });
      }
      getKeys(source).forEach(function(key) {
        if (propertyIsUnsafe(target, key)) {
          return;
        }
        if (propertyIsOnObject(target, key) && options.isMergeableObject(source[key])) {
          destination[key] = getMergeFunction(key, options)(target[key], source[key], options);
        } else {
          destination[key] = cloneUnlessOtherwiseSpecified(source[key], options);
        }
      });
      return destination;
    }
    function deepmerge(target, source, options) {
      options = options || {};
      options.arrayMerge = options.arrayMerge || defaultArrayMerge;
      options.isMergeableObject = options.isMergeableObject || isMergeableObject;
      options.cloneUnlessOtherwiseSpecified = cloneUnlessOtherwiseSpecified;
      var sourceIsArray = Array.isArray(source);
      var targetIsArray = Array.isArray(target);
      var sourceAndTargetTypesMatch = sourceIsArray === targetIsArray;
      if (!sourceAndTargetTypesMatch) {
        return cloneUnlessOtherwiseSpecified(source, options);
      } else if (sourceIsArray) {
        return options.arrayMerge(target, source, options);
      } else {
        return mergeObject(target, source, options);
      }
    }
    deepmerge.all = function deepmergeAll(array, options) {
      if (!Array.isArray(array)) {
        throw new Error("first argument should be an array");
      }
      return array.reduce(function(prev, next) {
        return deepmerge(prev, next, options);
      }, {});
    };
    var deepmerge_1 = deepmerge;
    module.exports = deepmerge_1;
  }
});

// package-external:@wordpress/notices
var require_notices = __commonJS({
  "package-external:@wordpress/notices"(exports, module) {
    module.exports = window.wp.notices;
  }
});

// packages/admin-ui/build-module/breadcrumbs/index.mjs
var import_i18n2 = __toESM(require_i18n(), 1);
import { Link as RouterLink } from "@wordpress/route";

// node_modules/clsx/dist/clsx.mjs
function r(e2) {
  var t2, f2, n2 = "";
  if ("string" == typeof e2 || "number" == typeof e2) n2 += e2;
  else if ("object" == typeof e2) if (Array.isArray(e2)) {
    var o2 = e2.length;
    for (t2 = 0; t2 < o2; t2++) e2[t2] && (f2 = r(e2[t2])) && (n2 && (n2 += " "), n2 += f2);
  } else for (f2 in e2) e2[f2] && (n2 && (n2 += " "), n2 += f2);
  return n2;
}
function clsx() {
  for (var e2, t2, f2 = 0, n2 = "", o2 = arguments.length; f2 < o2; f2++) (e2 = arguments[f2]) && (t2 = r(e2)) && (n2 && (n2 += " "), n2 += t2);
  return n2;
}
var clsx_default = clsx;

// packages/ui/build-module/badge/badge.mjs
var import_element2 = __toESM(require_element(), 1);

// node_modules/@base-ui/utils/esm/useControlled.js
var React2 = __toESM(require_react(), 1);

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
  name,
  state = "value"
}) {
  const {
    current: isControlled
  } = React2.useRef(controlled !== void 0);
  const [valueState, setValue] = React2.useState(defaultProp);
  const value = isControlled ? controlled : valueState;
  if (true) {
    React2.useEffect(() => {
      if (isControlled !== (controlled !== void 0)) {
        error([`A component is changing the ${isControlled ? "" : "un"}controlled ${state} state of ${name} to be ${isControlled ? "un" : ""}controlled.`, "Elements should not switch from uncontrolled to controlled (or vice versa).", `Decide between using a controlled or uncontrolled ${name} element for the lifetime of the component.`, "The nature of the state is determined during the first render. It's considered controlled if the value is not `undefined`.", "More info: https://fb.me/react-controlled-components"].join("\n"));
      }
    }, [state, name, controlled]);
    const {
      current: defaultValue
    } = React2.useRef(defaultProp);
    React2.useEffect(() => {
      if (!isControlled && serializeToDevModeString(defaultValue) !== serializeToDevModeString(defaultProp)) {
        error([`A component is changing the default ${state} state of an uncontrolled ${name} after being initialized. To suppress this warning opt to use a controlled ${name}.`].join("\n"));
      }
    }, [defaultProp]);
  }
  const setValueIfUncontrolled = React2.useCallback((newValue) => {
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

// node_modules/@base-ui/utils/esm/useStableCallback.js
var React4 = __toESM(require_react(), 1);

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
var useInsertionEffect = React4[`useInsertionEffect${Math.random().toFixed(1)}`.slice(0, -3)];
var useSafeInsertionEffect = (
  // React 17 doesn't have useInsertionEffect.
  useInsertionEffect && // Preact replaces useInsertionEffect with useLayoutEffect and fires too late.
  useInsertionEffect !== React4.useLayoutEffect ? useInsertionEffect : (fn) => fn()
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
var React5 = __toESM(require_react(), 1);
var noop = () => {
};
var useIsoLayoutEffect = typeof document !== "undefined" ? React5.useLayoutEffect : noop;

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

// node_modules/@base-ui/react/esm/internals/useRenderElement.js
var React8 = __toESM(require_react(), 1);

// node_modules/@base-ui/utils/esm/useMergedRefs.js
function useMergedRefs(a2, b2, c2, d2) {
  const forkRef = useRefWithInit(createForkRef).current;
  if (didChange(forkRef, a2, b2, c2, d2)) {
    update(forkRef, [a2, b2, c2, d2]);
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
function didChange(forkRef, a2, b2, c2, d2) {
  return forkRef.refs[0] !== a2 || forkRef.refs[1] !== b2 || forkRef.refs[2] !== c2 || forkRef.refs[3] !== d2;
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
      for (let i2 = 0; i2 < refs.length; i2 += 1) {
        const ref = refs[i2];
        if (ref == null) {
          continue;
        }
        switch (typeof ref) {
          case "function": {
            const refCleanup = ref(instance);
            if (typeof refCleanup === "function") {
              cleanupCallbacks[i2] = refCleanup;
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
        for (let i2 = 0; i2 < refs.length; i2 += 1) {
          const ref = refs[i2];
          if (ref == null) {
            continue;
          }
          switch (typeof ref) {
            case "function": {
              const cleanupCallback = cleanupCallbacks[i2];
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
function mergeObjects(a2, b2) {
  if (a2 && !b2) {
    return a2;
  }
  if (!a2 && b2) {
    return b2;
  }
  if (a2 || b2) {
    return {
      ...a2,
      ...b2
    };
  }
  return void 0;
}

// node_modules/@base-ui/utils/esm/empty.js
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
function mergeProps(a2, b2, c2, d2, e2) {
  if (!c2 && !d2 && !e2 && !a2) {
    return createInitialMergedProps(b2);
  }
  let merged = createInitialMergedProps(a2);
  if (b2) {
    merged = mergeInto(merged, b2);
  }
  if (c2) {
    merged = mergeInto(merged, c2);
  }
  if (d2) {
    merged = mergeInto(merged, d2);
  }
  if (e2) {
    merged = mergeInto(merged, e2);
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
  for (let i2 = 1; i2 < props.length; i2 += 1) {
    merged = mergeInto(merged, props[i2]);
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
function evaluateRenderProp(element, render4, props, state) {
  if (render4) {
    if (typeof render4 === "function") {
      if (true) {
        warnIfRenderPropLooksLikeComponent(render4);
      }
      return render4(props, state);
    }
    const mergedProps = mergeProps(props, render4.props);
    mergedProps.ref = props.ref;
    let newElement = render4;
    if (newElement?.$$typeof === REACT_LAZY_TYPE) {
      const children = React8.Children.toArray(render4);
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

// node_modules/@base-ui/react/esm/collapsible/root/useCollapsibleRoot.js
var React13 = __toESM(require_react(), 1);

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
      for (let i2 = 0; i2 < currentCallbacks.length; i2 += 1) {
        currentCallbacks[i2]?.(timestamp);
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

// node_modules/@base-ui/react/esm/collapsible/root/useCollapsibleRoot.js
function useCollapsibleRoot(parameters) {
  const {
    open: openParam,
    defaultOpen,
    onOpenChange,
    disabled: disabled2
  } = parameters;
  const isControlled = openParam !== void 0;
  const [open, setOpen] = useControlled({
    controlled: openParam,
    default: defaultOpen,
    name: "Collapsible",
    state: "open"
  });
  const {
    mounted,
    setMounted,
    transitionStatus
  } = useTransitionStatus(open, true, true);
  const [visible, setVisible] = React13.useState(open);
  const [{
    height,
    width
  }, setDimensions] = React13.useState({
    height: void 0,
    width: void 0
  });
  const defaultPanelId = useBaseUiId();
  const [panelIdState, setPanelIdState] = React13.useState();
  const panelId = panelIdState ?? defaultPanelId;
  const [hiddenUntilFound, setHiddenUntilFound] = React13.useState(false);
  const [keepMounted, setKeepMounted] = React13.useState(false);
  const abortControllerRef = React13.useRef(null);
  const animationTypeRef = React13.useRef(null);
  const transitionDimensionRef = React13.useRef(null);
  const panelRef = React13.useRef(null);
  const runOnceAnimationsFinish = useAnimationsFinished(panelRef, false);
  const handleTrigger = useStableCallback((event) => {
    const nextOpen = !open;
    const eventDetails = createChangeEventDetails(reason_parts_exports.triggerPress, event.nativeEvent);
    onOpenChange(nextOpen, eventDetails);
    if (eventDetails.isCanceled) {
      return;
    }
    const panel = panelRef.current;
    if (animationTypeRef.current === "css-animation" && panel != null) {
      panel.style.removeProperty("animation-name");
    }
    if (!hiddenUntilFound && !keepMounted) {
      if (animationTypeRef.current != null && animationTypeRef.current !== "css-animation") {
        if (!mounted && nextOpen) {
          setMounted(true);
        }
      }
      if (animationTypeRef.current === "css-animation") {
        if (!visible && nextOpen) {
          setVisible(true);
        }
        if (!mounted && nextOpen) {
          setMounted(true);
        }
      }
    }
    setOpen(nextOpen);
    if (animationTypeRef.current === "none" && mounted && !nextOpen) {
      setMounted(false);
    }
  });
  useIsoLayoutEffect(() => {
    if (isControlled && animationTypeRef.current === "none" && !open) {
      setMounted(false);
    }
  }, [isControlled, open, openParam, setMounted]);
  return React13.useMemo(() => ({
    abortControllerRef,
    animationTypeRef,
    disabled: disabled2,
    handleTrigger,
    height,
    mounted,
    open,
    panelId,
    panelRef,
    runOnceAnimationsFinish,
    setDimensions,
    setHiddenUntilFound,
    setKeepMounted,
    setMounted,
    setOpen,
    setPanelIdState,
    setVisible,
    transitionDimensionRef,
    transitionStatus,
    visible,
    width
  }), [abortControllerRef, animationTypeRef, disabled2, handleTrigger, height, mounted, open, panelId, panelRef, runOnceAnimationsFinish, setDimensions, setHiddenUntilFound, setKeepMounted, setMounted, setOpen, setVisible, transitionDimensionRef, transitionStatus, visible, width]);
}

// node_modules/@base-ui/react/esm/collapsible/root/CollapsibleRootContext.js
var React14 = __toESM(require_react(), 1);
var CollapsibleRootContext = /* @__PURE__ */ React14.createContext(void 0);
if (true) CollapsibleRootContext.displayName = "CollapsibleRootContext";
function useCollapsibleRootContext() {
  const context = React14.useContext(CollapsibleRootContext);
  if (context === void 0) {
    throw new Error(true ? "Base UI: CollapsibleRootContext is missing. Collapsible parts must be placed within <Collapsible.Root>." : formatErrorMessage_default(15));
  }
  return context;
}

// node_modules/@base-ui/react/esm/collapsible/panel/CollapsiblePanelDataAttributes.js
var CollapsiblePanelDataAttributes = (function(CollapsiblePanelDataAttributes2) {
  CollapsiblePanelDataAttributes2["open"] = "data-open";
  CollapsiblePanelDataAttributes2["closed"] = "data-closed";
  CollapsiblePanelDataAttributes2[CollapsiblePanelDataAttributes2["startingStyle"] = TransitionStatusDataAttributes.startingStyle] = "startingStyle";
  CollapsiblePanelDataAttributes2[CollapsiblePanelDataAttributes2["endingStyle"] = TransitionStatusDataAttributes.endingStyle] = "endingStyle";
  return CollapsiblePanelDataAttributes2;
})({});

// node_modules/@base-ui/react/esm/collapsible/trigger/CollapsibleTriggerDataAttributes.js
var CollapsibleTriggerDataAttributes = /* @__PURE__ */ (function(CollapsibleTriggerDataAttributes2) {
  CollapsibleTriggerDataAttributes2["panelOpen"] = "data-panel-open";
  return CollapsibleTriggerDataAttributes2;
})({});

// node_modules/@base-ui/react/esm/utils/collapsibleOpenStateMapping.js
var PANEL_OPEN_HOOK = {
  [CollapsiblePanelDataAttributes.open]: ""
};
var PANEL_CLOSED_HOOK = {
  [CollapsiblePanelDataAttributes.closed]: ""
};
var triggerOpenStateMapping = {
  open(value) {
    if (value) {
      return {
        [CollapsibleTriggerDataAttributes.panelOpen]: ""
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
          const message2 = "A component that acts as a button expected a native <button> because the `nativeButton` prop is true. Rendering a non-<button> removes native button semantics, which can impact forms and accessibility. Use a real <button> in the `render` prop, or set `nativeButton` to `false`.";
          error(`${message2}${ownerStackMessage}`);
        }
      } else if (isButtonTag) {
        const ownerStackMessage = SafeReact.captureOwnerStack?.() || "";
        const message2 = "A component that acts as a button expected a non-<button> because the `nativeButton` prop is false. Rendering a <button> keeps native behavior while Base UI applies non-native attributes and handlers, which can add unintended extra attributes (such as `role` or `aria-disabled`). Use a non-<button> in the `render` prop, or set `nativeButton` to `true`.";
        error(`${message2}${ownerStackMessage}`);
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

// node_modules/@base-ui/react/esm/collapsible/panel/useCollapsiblePanel.js
var React18 = __toESM(require_react(), 1);

// node_modules/@base-ui/utils/esm/addEventListener.js
function addEventListener(target, type, listener, options) {
  target.addEventListener(type, listener, options);
  return () => {
    target.removeEventListener(type, listener, options);
  };
}

// node_modules/@base-ui/react/esm/accordion/root/AccordionRootDataAttributes.js
var AccordionRootDataAttributes = /* @__PURE__ */ (function(AccordionRootDataAttributes2) {
  AccordionRootDataAttributes2["disabled"] = "data-disabled";
  AccordionRootDataAttributes2["orientation"] = "data-orientation";
  return AccordionRootDataAttributes2;
})({});

// node_modules/@base-ui/react/esm/collapsible/panel/useCollapsiblePanel.js
function useCollapsiblePanel(parameters) {
  const {
    abortControllerRef,
    animationTypeRef,
    externalRef,
    height,
    hiddenUntilFound,
    keepMounted,
    id: idParam,
    mounted,
    onOpenChange,
    open,
    panelRef,
    runOnceAnimationsFinish,
    setDimensions,
    setMounted,
    setOpen,
    setVisible,
    transitionDimensionRef,
    visible,
    width
  } = parameters;
  const isBeforeMatchRef = React18.useRef(false);
  const latestAnimationNameRef = React18.useRef(null);
  const shouldCancelInitialOpenAnimationRef = React18.useRef(open);
  const shouldCancelInitialOpenTransitionRef = React18.useRef(open);
  const endingStyleFrame = useAnimationFrame();
  const hidden = React18.useMemo(() => {
    if (animationTypeRef.current === "css-animation") {
      return !visible;
    }
    return !open && !mounted;
  }, [open, mounted, visible, animationTypeRef]);
  const handlePanelRef = useStableCallback((element) => {
    if (!element) {
      return void 0;
    }
    if (animationTypeRef.current == null || transitionDimensionRef.current == null) {
      const panelStyles = getComputedStyle(element);
      const hasAnimation = panelStyles.animationName !== "none" && panelStyles.animationName !== "";
      const hasTransition = panelStyles.transitionDuration !== "0s" && panelStyles.transitionDuration !== "";
      if (hasAnimation && hasTransition) {
        if (true) {
          warn("CSS transitions and CSS animations both detected on Collapsible or Accordion panel.", "Only one of either animation type should be used.");
        }
      } else if (panelStyles.animationName === "none" && panelStyles.transitionDuration !== "0s") {
        animationTypeRef.current = "css-transition";
      } else if (panelStyles.animationName !== "none" && panelStyles.transitionDuration === "0s") {
        animationTypeRef.current = "css-animation";
      } else {
        animationTypeRef.current = "none";
      }
      if (element.getAttribute(AccordionRootDataAttributes.orientation) === "horizontal" || panelStyles.transitionProperty.indexOf("width") > -1) {
        transitionDimensionRef.current = "width";
      } else {
        transitionDimensionRef.current = "height";
      }
    }
    if (animationTypeRef.current !== "css-transition") {
      return void 0;
    }
    if (height === void 0 || width === void 0) {
      setDimensions({
        height: element.scrollHeight,
        width: element.scrollWidth
      });
      if (shouldCancelInitialOpenTransitionRef.current) {
        element.style.setProperty("transition-duration", "0s");
      }
    }
    let frame = -1;
    let nextFrame = -1;
    frame = AnimationFrame.request(() => {
      shouldCancelInitialOpenTransitionRef.current = false;
      nextFrame = AnimationFrame.request(() => {
        setTimeout(() => {
          element.style.removeProperty("transition-duration");
        });
      });
    });
    return () => {
      AnimationFrame.cancel(frame);
      AnimationFrame.cancel(nextFrame);
    };
  });
  const mergedPanelRef = useMergedRefs(externalRef, panelRef, handlePanelRef);
  useIsoLayoutEffect(() => {
    if (animationTypeRef.current !== "css-transition") {
      return void 0;
    }
    const panel = panelRef.current;
    if (!panel) {
      return void 0;
    }
    let resizeFrame = -1;
    if (abortControllerRef.current != null) {
      abortControllerRef.current.abort();
      abortControllerRef.current = null;
    }
    if (open) {
      const originalLayoutStyles = {
        "justify-content": panel.style.justifyContent,
        "align-items": panel.style.alignItems,
        "align-content": panel.style.alignContent,
        "justify-items": panel.style.justifyItems
      };
      Object.keys(originalLayoutStyles).forEach((key) => {
        panel.style.setProperty(key, "initial", "important");
      });
      if (!shouldCancelInitialOpenTransitionRef.current && !keepMounted) {
        panel.setAttribute(CollapsiblePanelDataAttributes.startingStyle, "");
      }
      setDimensions({
        height: panel.scrollHeight,
        width: panel.scrollWidth
      });
      resizeFrame = AnimationFrame.request(() => {
        Object.entries(originalLayoutStyles).forEach(([key, value]) => {
          if (value === "") {
            panel.style.removeProperty(key);
          } else {
            panel.style.setProperty(key, value);
          }
        });
      });
    } else {
      if (panel.scrollHeight === 0 && panel.scrollWidth === 0) {
        return void 0;
      }
      setDimensions({
        height: panel.scrollHeight,
        width: panel.scrollWidth
      });
      const abortController = new AbortController();
      abortControllerRef.current = abortController;
      const signal = abortController.signal;
      let attributeObserver = null;
      const endingStyleAttribute = CollapsiblePanelDataAttributes.endingStyle;
      attributeObserver = new MutationObserver((mutationList) => {
        const hasEndingStyle = mutationList.some((mutation) => mutation.type === "attributes" && mutation.attributeName === endingStyleAttribute);
        if (hasEndingStyle) {
          attributeObserver?.disconnect();
          attributeObserver = null;
          runOnceAnimationsFinish(() => {
            setDimensions({
              height: 0,
              width: 0
            });
            panel.style.removeProperty("content-visibility");
            setMounted(false);
            if (abortControllerRef.current === abortController) {
              abortControllerRef.current = null;
            }
          }, signal);
        }
      });
      attributeObserver.observe(panel, {
        attributes: true,
        attributeFilter: [endingStyleAttribute]
      });
      return () => {
        attributeObserver?.disconnect();
        endingStyleFrame.cancel();
        if (abortControllerRef.current === abortController) {
          abortController.abort();
          abortControllerRef.current = null;
        }
      };
    }
    return () => {
      AnimationFrame.cancel(resizeFrame);
    };
  }, [abortControllerRef, animationTypeRef, endingStyleFrame, hiddenUntilFound, keepMounted, mounted, open, panelRef, runOnceAnimationsFinish, setDimensions, setMounted]);
  useIsoLayoutEffect(() => {
    if (animationTypeRef.current !== "css-animation") {
      return;
    }
    const panel = panelRef.current;
    if (!panel) {
      return;
    }
    latestAnimationNameRef.current = panel.style.animationName || latestAnimationNameRef.current;
    panel.style.setProperty("animation-name", "none");
    setDimensions({
      height: panel.scrollHeight,
      width: panel.scrollWidth
    });
    if (!shouldCancelInitialOpenAnimationRef.current && !isBeforeMatchRef.current) {
      panel.style.removeProperty("animation-name");
    }
    if (open) {
      if (abortControllerRef.current != null) {
        abortControllerRef.current.abort();
        abortControllerRef.current = null;
      }
      setMounted(true);
      setVisible(true);
    } else {
      abortControllerRef.current = new AbortController();
      runOnceAnimationsFinish(() => {
        setMounted(false);
        setVisible(false);
        abortControllerRef.current = null;
      }, abortControllerRef.current.signal);
    }
  }, [abortControllerRef, animationTypeRef, open, panelRef, runOnceAnimationsFinish, setDimensions, setMounted, setVisible, visible]);
  useOnMount(() => {
    const frame = AnimationFrame.request(() => {
      shouldCancelInitialOpenAnimationRef.current = false;
    });
    return () => AnimationFrame.cancel(frame);
  });
  useIsoLayoutEffect(() => {
    if (!hiddenUntilFound) {
      return void 0;
    }
    const panel = panelRef.current;
    if (!panel) {
      return void 0;
    }
    let frame = -1;
    let nextFrame = -1;
    if (open && isBeforeMatchRef.current) {
      panel.style.transitionDuration = "0s";
      setDimensions({
        height: panel.scrollHeight,
        width: panel.scrollWidth
      });
      frame = AnimationFrame.request(() => {
        isBeforeMatchRef.current = false;
        nextFrame = AnimationFrame.request(() => {
          setTimeout(() => {
            panel.style.removeProperty("transition-duration");
          });
        });
      });
    }
    return () => {
      AnimationFrame.cancel(frame);
      AnimationFrame.cancel(nextFrame);
    };
  }, [hiddenUntilFound, open, panelRef, setDimensions]);
  useIsoLayoutEffect(() => {
    const panel = panelRef.current;
    if (panel && hiddenUntilFound && hidden) {
      panel.setAttribute("hidden", "until-found");
      if (animationTypeRef.current === "css-transition") {
        panel.setAttribute(CollapsiblePanelDataAttributes.startingStyle, "");
      }
    }
  }, [hiddenUntilFound, hidden, animationTypeRef, panelRef]);
  React18.useEffect(function registerBeforeMatchListener() {
    const panel = panelRef.current;
    if (!panel) {
      return void 0;
    }
    function handleBeforeMatch(event) {
      isBeforeMatchRef.current = true;
      setOpen(true);
      onOpenChange(true, createChangeEventDetails(reason_parts_exports.none, event));
    }
    return addEventListener(panel, "beforematch", handleBeforeMatch);
  }, [onOpenChange, panelRef, setOpen]);
  return React18.useMemo(() => ({
    props: {
      hidden,
      id: idParam,
      ref: mergedPanelRef
    }
  }), [hidden, idParam, mergedPanelRef]);
}

// node_modules/@base-ui/react/esm/internals/useOpenChangeComplete.js
var React19 = __toESM(require_react(), 1);
function useOpenChangeComplete(parameters) {
  const {
    enabled = true,
    open,
    ref,
    onComplete: onCompleteParam
  } = parameters;
  const onComplete = useStableCallback(onCompleteParam);
  const runOnceAnimationsFinish = useAnimationsFinished(ref, open, false);
  React19.useEffect(() => {
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

// node_modules/@base-ui/react/esm/collapsible/index.parts.js
var index_parts_exports = {};
__export(index_parts_exports, {
  Panel: () => CollapsiblePanel,
  Root: () => CollapsibleRoot,
  Trigger: () => CollapsibleTrigger
});

// node_modules/@base-ui/react/esm/collapsible/root/CollapsibleRoot.js
var React20 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/collapsible/root/stateAttributesMapping.js
var collapsibleStateAttributesMapping = {
  ...collapsibleOpenStateMapping,
  ...transitionStatusMapping
};

// node_modules/@base-ui/react/esm/collapsible/root/CollapsibleRoot.js
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
var CollapsibleRoot = /* @__PURE__ */ React20.forwardRef(function CollapsibleRoot2(componentProps, forwardedRef) {
  const {
    render: render4,
    className,
    defaultOpen = false,
    disabled: disabled2 = false,
    onOpenChange: onOpenChangeProp,
    open,
    style,
    ...elementProps
  } = componentProps;
  const onOpenChange = useStableCallback(onOpenChangeProp);
  const collapsible = useCollapsibleRoot({
    open,
    defaultOpen,
    onOpenChange,
    disabled: disabled2
  });
  const state = React20.useMemo(() => ({
    open: collapsible.open,
    disabled: collapsible.disabled,
    transitionStatus: collapsible.transitionStatus
  }), [collapsible.open, collapsible.disabled, collapsible.transitionStatus]);
  const contextValue = React20.useMemo(() => ({
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
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(CollapsibleRootContext.Provider, {
    value: contextValue,
    children: element
  });
});
if (true) CollapsibleRoot.displayName = "CollapsibleRoot";

// node_modules/@base-ui/react/esm/collapsible/trigger/CollapsibleTrigger.js
var React21 = __toESM(require_react(), 1);
var stateAttributesMapping = {
  ...triggerOpenStateMapping,
  ...transitionStatusMapping
};
var CollapsibleTrigger = /* @__PURE__ */ React21.forwardRef(function CollapsibleTrigger2(componentProps, forwardedRef) {
  const {
    panelId,
    open,
    handleTrigger,
    state,
    disabled: contextDisabled
  } = useCollapsibleRootContext();
  const {
    className,
    disabled: disabled2 = contextDisabled,
    id,
    render: render4,
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
  const props = React21.useMemo(() => ({
    "aria-controls": open ? panelId : void 0,
    "aria-expanded": open,
    onClick: handleTrigger
  }), [panelId, open, handleTrigger]);
  const element = useRenderElement("button", componentProps, {
    state,
    ref: [forwardedRef, buttonRef],
    props: [props, elementProps, getButtonProps],
    stateAttributesMapping
  });
  return element;
});
if (true) CollapsibleTrigger.displayName = "CollapsibleTrigger";

// node_modules/@base-ui/react/esm/collapsible/panel/CollapsiblePanel.js
var React22 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/collapsible/panel/CollapsiblePanelCssVars.js
var CollapsiblePanelCssVars = /* @__PURE__ */ (function(CollapsiblePanelCssVars2) {
  CollapsiblePanelCssVars2["collapsiblePanelHeight"] = "--collapsible-panel-height";
  CollapsiblePanelCssVars2["collapsiblePanelWidth"] = "--collapsible-panel-width";
  return CollapsiblePanelCssVars2;
})({});

// node_modules/@base-ui/react/esm/collapsible/panel/CollapsiblePanel.js
var CollapsiblePanel = /* @__PURE__ */ React22.forwardRef(function CollapsiblePanel2(componentProps, forwardedRef) {
  const {
    className,
    hiddenUntilFound: hiddenUntilFoundProp,
    keepMounted: keepMountedProp,
    render: render4,
    id: idProp,
    style,
    ...elementProps
  } = componentProps;
  if (true) {
    useIsoLayoutEffect(() => {
      if (hiddenUntilFoundProp && keepMountedProp === false) {
        warn("The `keepMounted={false}` prop on a Collapsible will be ignored when using `hiddenUntilFound` since it requires the Panel to remain mounted even when closed.");
      }
    }, [hiddenUntilFoundProp, keepMountedProp]);
  }
  const {
    abortControllerRef,
    animationTypeRef,
    height,
    mounted,
    onOpenChange,
    open,
    panelId,
    panelRef,
    runOnceAnimationsFinish,
    setDimensions,
    setHiddenUntilFound,
    setKeepMounted,
    setMounted,
    setPanelIdState,
    setOpen,
    setVisible,
    state,
    transitionDimensionRef,
    visible,
    width,
    transitionStatus
  } = useCollapsibleRootContext();
  const hiddenUntilFound = hiddenUntilFoundProp ?? false;
  const keepMounted = keepMountedProp ?? false;
  useIsoLayoutEffect(() => {
    if (idProp) {
      setPanelIdState(idProp);
      return () => {
        setPanelIdState(void 0);
      };
    }
    return void 0;
  }, [idProp, setPanelIdState]);
  useIsoLayoutEffect(() => {
    setHiddenUntilFound(hiddenUntilFound);
  }, [setHiddenUntilFound, hiddenUntilFound]);
  useIsoLayoutEffect(() => {
    setKeepMounted(keepMounted);
  }, [setKeepMounted, keepMounted]);
  const {
    props
  } = useCollapsiblePanel({
    abortControllerRef,
    animationTypeRef,
    externalRef: forwardedRef,
    height,
    hiddenUntilFound,
    id: panelId,
    keepMounted,
    mounted,
    onOpenChange,
    open,
    panelRef,
    runOnceAnimationsFinish,
    setDimensions,
    setMounted,
    setOpen,
    setVisible,
    transitionDimensionRef,
    visible,
    width
  });
  useOpenChangeComplete({
    open: open && transitionStatus === "idle",
    ref: panelRef,
    onComplete() {
      if (!open) {
        return;
      }
      setDimensions({
        height: void 0,
        width: void 0
      });
    }
  });
  const panelState = React22.useMemo(() => ({
    ...state,
    transitionStatus
  }), [state, transitionStatus]);
  const element = useRenderElement("div", componentProps, {
    state: panelState,
    ref: [forwardedRef, panelRef],
    props: [props, {
      style: {
        [CollapsiblePanelCssVars.collapsiblePanelHeight]: height === void 0 ? "auto" : `${height}px`,
        [CollapsiblePanelCssVars.collapsiblePanelWidth]: width === void 0 ? "auto" : `${width}px`
      }
    }, elementProps],
    stateAttributesMapping: collapsibleStateAttributesMapping
  });
  const shouldRender = keepMounted || hiddenUntilFound || mounted;
  if (!shouldRender) {
    return null;
  }
  return element;
});
if (true) CollapsiblePanel.displayName = "CollapsiblePanel";

// node_modules/@base-ui/react/esm/use-render/useRender.js
function useRender(params) {
  return useRenderElement(params.defaultTagName ?? "div", params, params);
}

// packages/ui/build-module/text/text.mjs
var import_element = __toESM(require_element(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='4130d64bea']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "4130d64bea");
  style.appendChild(document.createTextNode('@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._83ed8a8da5dd50ea__text{margin:0}._14437cfb77831647__heading-2xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-2xl,32px);font-size:var(--wpds-typography-font-size-2xl,32px);line-height:var(--wpds-typography-line-height-2xl,40px)}._14437cfb77831647__heading-2xl,._3c78b7fa9b4072dd__heading-xl{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-medium,499)}._3c78b7fa9b4072dd__heading-xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-xl,20px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-md,24px)}.aa58f227716bcde2__heading-lg{--_gcd-heading-font-size:var(--wpds-typography-font-size-lg,15px);font-size:var(--wpds-typography-font-size-lg,15px)}.aa58f227716bcde2__heading-lg,.fc4da56d8dfe52c4__heading-md{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-medium,499);line-height:var(--wpds-typography-line-height-sm,20px)}.fc4da56d8dfe52c4__heading-md{--_gcd-heading-font-size:var(--wpds-typography-font-size-md,13px);font-size:var(--wpds-typography-font-size-md,13px)}.a9b78c7c82e8dff7__heading-sm{--_gcd-heading-font-size:var(--wpds-typography-font-size-xs,11px);font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-medium,499);line-height:var(--wpds-typography-line-height-xs,16px);text-transform:uppercase}._305ff559e52180d5__body-xl{--_gcd-p-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-p-line-height:var(--wpds-typography-line-height-xl,32px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-xl,32px)}._305ff559e52180d5__body-xl,.ca1aa3fc2029e958__body-lg{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-regular,400)}.ca1aa3fc2029e958__body-lg{--_gcd-p-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-p-line-height:var(--wpds-typography-line-height-md,24px);font-size:var(--wpds-typography-font-size-lg,15px);line-height:var(--wpds-typography-line-height-md,24px)}._131101940be12424__body-md{--_gcd-p-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px)}._0e8d87a42c1f75fa__body-sm,._131101940be12424__body-md{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-regular,400)}._0e8d87a42c1f75fa__body-sm{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}'));
  document.head.appendChild(style);
}
var style_default = { "text": "_83ed8a8da5dd50ea__text", "heading-2xl": "_14437cfb77831647__heading-2xl", "heading-xl": "_3c78b7fa9b4072dd__heading-xl", "heading-lg": "aa58f227716bcde2__heading-lg", "heading-md": "fc4da56d8dfe52c4__heading-md", "heading-sm": "a9b78c7c82e8dff7__heading-sm", "body-xl": "_305ff559e52180d5__body-xl", "body-lg": "ca1aa3fc2029e958__body-lg", "body-md": "_131101940be12424__body-md", "body-sm": "_0e8d87a42c1f75fa__body-sm" };
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='1fb29d3a3c']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "1fb29d3a3c");
  style.appendChild(document.createTextNode("._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,#0000);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 #0000);color:var(--_gcd-input-color,var(--wpds-color-fg-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,#0000);border-color:var(--_gcd-input-border-color-disabled,#0000);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid #0000)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-fg-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-medium,499));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid #0000);transition:var(--_gcd-a-transition,none)}"));
  document.head.appendChild(style);
}
var global_css_defense_default = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
var Text = (0, import_element.forwardRef)(function Text2({ variant = "body-md", render: render4, className, ...props }, ref) {
  const element = useRender({
    render: render4,
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
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='d6a685e1aa']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "d6a685e1aa");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._96e6251aad1a6136__badge{border-radius:var(--wpds-border-radius-lg,8px);padding-block:var(--wpds-dimension-padding-xs,4px);padding-inline:var(--wpds-dimension-padding-sm,8px)}._99f7158cb520f750__is-high-intent{background-color:var(--wpds-color-bg-surface-error,#f6e6e3);color:var(--wpds-color-fg-content-error,#470000)}.c20ebef2365bc8b7__is-medium-intent{background-color:var(--wpds-color-bg-surface-warning,#fde6be);color:var(--wpds-color-fg-content-warning,#2e1900)}._365e1626c6202e52__is-low-intent{background-color:var(--wpds-color-bg-surface-caution,#fee995);color:var(--wpds-color-fg-content-caution,#281d00)}._33f8198127ddf4ef__is-stable-intent{background-color:var(--wpds-color-bg-surface-success,#c6f7cd);color:var(--wpds-color-fg-content-success,#002900)}._04c1aca8fc449412__is-informational-intent{background-color:var(--wpds-color-bg-surface-info,#deebfa);color:var(--wpds-color-fg-content-info,#001b4f)}._90726e69d495ec19__is-draft-intent{background-color:var(--wpds-color-bg-surface-neutral-weak,#f4f4f4);color:var(--wpds-color-fg-content-neutral,#1e1e1e)}._898f4a544993bd39__is-none-intent{background-color:var(--wpds-color-bg-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);color:var(--wpds-color-fg-content-neutral,#1e1e1e);padding-block:calc(var(--wpds-dimension-padding-xs, 4px) - var(--wpds-border-width-xs, 1px));padding-inline:calc(var(--wpds-dimension-padding-sm, 8px) - var(--wpds-border-width-xs, 1px))}}"));
  document.head.appendChild(style);
}
var style_default2 = { "badge": "_96e6251aad1a6136__badge", "is-high-intent": "_99f7158cb520f750__is-high-intent", "is-medium-intent": "c20ebef2365bc8b7__is-medium-intent", "is-low-intent": "_365e1626c6202e52__is-low-intent", "is-stable-intent": "_33f8198127ddf4ef__is-stable-intent", "is-informational-intent": "_04c1aca8fc449412__is-informational-intent", "is-draft-intent": "_90726e69d495ec19__is-draft-intent", "is-none-intent": "_898f4a544993bd39__is-none-intent" };
var Badge = (0, import_element2.forwardRef)(function Badge2({ intent = "none", className, ...props }, ref) {
  return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
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

// packages/ui/build-module/icon/icon.mjs
var import_element3 = __toESM(require_element(), 1);
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
var Icon = (0, import_element3.forwardRef)(function Icon2({ icon, size = 24, ...restProps }, ref) {
  return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
    import_primitives.SVG,
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

// packages/ui/build-module/card/index.mjs
var card_exports = {};
__export(card_exports, {
  Content: () => Content,
  FullBleed: () => FullBleed,
  Header: () => Header,
  Root: () => Root,
  Title: () => Title
});

// packages/ui/build-module/card/root.mjs
var import_element4 = __toESM(require_element(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='e3ae230cea']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "e3ae230cea");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}"));
  document.head.appendChild(style);
}
var resets_default = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='14f5e9ddeb']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "14f5e9ddeb");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._02872bf298eadc43__root{--wp-ui-card-padding:var(--wpds-dimension-padding-2xl,24px);--wp-ui-card-header-content-gap:var(--wpds-dimension-gap-xl,24px);--wp-ui-card-header-content-margin:calc(var(--wp-ui-card-header-content-gap) - var(--wp-ui-card-padding));background-color:var(--wpds-color-bg-surface-neutral-strong,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral-weak,#e4e4e4);border-radius:var(--wpds-border-radius-lg,8px);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-direction:column;overflow:clip}._5dffdaf2a6e669ac__content,.bbccc92e6ba5662d__header{padding:var(--wp-ui-card-padding);&:not(:first-child):not(:last-child){padding-block-end:0}}.bbccc92e6ba5662d__header+._5dffdaf2a6e669ac__content{margin-block-start:var(--wp-ui-card-header-content-margin);padding-block-start:0}.c1fa192587e1b4a6__fullbleed{margin-inline:calc(var(--wp-ui-card-padding)*-1);width:calc(100% + var(--wp-ui-card-padding)*2)}}"));
  document.head.appendChild(style);
}
var style_default3 = { "root": "_02872bf298eadc43__root", "header": "bbccc92e6ba5662d__header", "content": "_5dffdaf2a6e669ac__content", "fullbleed": "c1fa192587e1b4a6__fullbleed" };
var Root = (0, import_element4.forwardRef)(function Card({ render: render4, ...restProps }, ref) {
  const mergedClassName = clsx_default(style_default3.root, resets_default["box-sizing"]);
  const element = useRender({
    defaultTagName: "div",
    render: render4,
    ref,
    props: mergeProps({ className: mergedClassName }, restProps)
  });
  return element;
});

// packages/ui/build-module/card/header.mjs
var import_element5 = __toESM(require_element(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='14f5e9ddeb']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "14f5e9ddeb");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._02872bf298eadc43__root{--wp-ui-card-padding:var(--wpds-dimension-padding-2xl,24px);--wp-ui-card-header-content-gap:var(--wpds-dimension-gap-xl,24px);--wp-ui-card-header-content-margin:calc(var(--wp-ui-card-header-content-gap) - var(--wp-ui-card-padding));background-color:var(--wpds-color-bg-surface-neutral-strong,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral-weak,#e4e4e4);border-radius:var(--wpds-border-radius-lg,8px);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-direction:column;overflow:clip}._5dffdaf2a6e669ac__content,.bbccc92e6ba5662d__header{padding:var(--wp-ui-card-padding);&:not(:first-child):not(:last-child){padding-block-end:0}}.bbccc92e6ba5662d__header+._5dffdaf2a6e669ac__content{margin-block-start:var(--wp-ui-card-header-content-margin);padding-block-start:0}.c1fa192587e1b4a6__fullbleed{margin-inline:calc(var(--wp-ui-card-padding)*-1);width:calc(100% + var(--wp-ui-card-padding)*2)}}"));
  document.head.appendChild(style);
}
var style_default4 = { "root": "_02872bf298eadc43__root", "header": "bbccc92e6ba5662d__header", "content": "_5dffdaf2a6e669ac__content", "fullbleed": "c1fa192587e1b4a6__fullbleed" };
var Header = (0, import_element5.forwardRef)(
  function CardHeader({ render: render4, ...props }, ref) {
    const element = useRender({
      defaultTagName: "div",
      render: render4,
      ref,
      props: mergeProps({ className: style_default4.header }, props)
    });
    return element;
  }
);

// packages/ui/build-module/card/content.mjs
var import_element6 = __toESM(require_element(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='14f5e9ddeb']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "14f5e9ddeb");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._02872bf298eadc43__root{--wp-ui-card-padding:var(--wpds-dimension-padding-2xl,24px);--wp-ui-card-header-content-gap:var(--wpds-dimension-gap-xl,24px);--wp-ui-card-header-content-margin:calc(var(--wp-ui-card-header-content-gap) - var(--wp-ui-card-padding));background-color:var(--wpds-color-bg-surface-neutral-strong,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral-weak,#e4e4e4);border-radius:var(--wpds-border-radius-lg,8px);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-direction:column;overflow:clip}._5dffdaf2a6e669ac__content,.bbccc92e6ba5662d__header{padding:var(--wp-ui-card-padding);&:not(:first-child):not(:last-child){padding-block-end:0}}.bbccc92e6ba5662d__header+._5dffdaf2a6e669ac__content{margin-block-start:var(--wp-ui-card-header-content-margin);padding-block-start:0}.c1fa192587e1b4a6__fullbleed{margin-inline:calc(var(--wp-ui-card-padding)*-1);width:calc(100% + var(--wp-ui-card-padding)*2)}}"));
  document.head.appendChild(style);
}
var style_default5 = { "root": "_02872bf298eadc43__root", "header": "bbccc92e6ba5662d__header", "content": "_5dffdaf2a6e669ac__content", "fullbleed": "c1fa192587e1b4a6__fullbleed" };
var Content = (0, import_element6.forwardRef)(
  function CardContent({ render: render4, ...props }, ref) {
    const element = useRender({
      defaultTagName: "div",
      render: render4,
      ref,
      props: mergeProps({ className: style_default5.content }, props)
    });
    return element;
  }
);

// packages/ui/build-module/card/full-bleed.mjs
var import_element7 = __toESM(require_element(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='14f5e9ddeb']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "14f5e9ddeb");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._02872bf298eadc43__root{--wp-ui-card-padding:var(--wpds-dimension-padding-2xl,24px);--wp-ui-card-header-content-gap:var(--wpds-dimension-gap-xl,24px);--wp-ui-card-header-content-margin:calc(var(--wp-ui-card-header-content-gap) - var(--wp-ui-card-padding));background-color:var(--wpds-color-bg-surface-neutral-strong,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral-weak,#e4e4e4);border-radius:var(--wpds-border-radius-lg,8px);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-direction:column;overflow:clip}._5dffdaf2a6e669ac__content,.bbccc92e6ba5662d__header{padding:var(--wp-ui-card-padding);&:not(:first-child):not(:last-child){padding-block-end:0}}.bbccc92e6ba5662d__header+._5dffdaf2a6e669ac__content{margin-block-start:var(--wp-ui-card-header-content-margin);padding-block-start:0}.c1fa192587e1b4a6__fullbleed{margin-inline:calc(var(--wp-ui-card-padding)*-1);width:calc(100% + var(--wp-ui-card-padding)*2)}}"));
  document.head.appendChild(style);
}
var style_default6 = { "root": "_02872bf298eadc43__root", "header": "bbccc92e6ba5662d__header", "content": "_5dffdaf2a6e669ac__content", "fullbleed": "c1fa192587e1b4a6__fullbleed" };
var FullBleed = (0, import_element7.forwardRef)(
  function CardFullBleed({ render: render4, ...props }, ref) {
    const element = useRender({
      defaultTagName: "div",
      render: render4,
      ref,
      props: mergeProps(
        { className: style_default6.fullbleed },
        props
      )
    });
    return element;
  }
);

// packages/ui/build-module/card/title.mjs
var import_element8 = __toESM(require_element(), 1);
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var DEFAULT_TAG = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)("div", {});
var Title = (0, import_element8.forwardRef)(
  function CardTitle({ render: render4 = DEFAULT_TAG, children, ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
      Text,
      {
        ref,
        variant: "heading-lg",
        render: render4,
        ...props,
        children
      }
    );
  }
);

// packages/ui/build-module/collapsible/panel.mjs
var import_element9 = __toESM(require_element(), 1);
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
var Panel = (0, import_element9.forwardRef)(
  function CollapsiblePanel3(props, forwardedRef) {
    return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(index_parts_exports.Panel, { ref: forwardedRef, ...props });
  }
);

// packages/ui/build-module/collapsible/root.mjs
var import_element10 = __toESM(require_element(), 1);
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
var Root2 = (0, import_element10.forwardRef)(
  function CollapsibleRoot3(props, forwardedRef) {
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(index_parts_exports.Root, { ref: forwardedRef, ...props });
  }
);

// packages/ui/build-module/collapsible/trigger.mjs
var import_element11 = __toESM(require_element(), 1);
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
var Trigger = (0, import_element11.forwardRef)(
  function CollapsibleTrigger3(props, forwardedRef) {
    return /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(index_parts_exports.Trigger, { ref: forwardedRef, ...props });
  }
);

// packages/ui/build-module/collapsible-card/index.mjs
var collapsible_card_exports = {};
__export(collapsible_card_exports, {
  Content: () => Content2,
  Header: () => Header2,
  HeaderDescription: () => HeaderDescription,
  Root: () => Root3
});

// packages/ui/build-module/collapsible-card/root.mjs
var import_element12 = __toESM(require_element(), 1);
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
var Root3 = (0, import_element12.forwardRef)(
  function CollapsibleCardRoot({ render: render4, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
      Root2,
      {
        ref,
        render: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(Root, { render: render4 }),
        ...restProps
      }
    );
  }
);

// packages/ui/build-module/collapsible-card/header.mjs
var import_element14 = __toESM(require_element(), 1);

// packages/icons/build-module/library/chevron-down.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
var chevron_down_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives2.Path, { d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z" }) });

// packages/icons/build-module/library/close-small.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
var close_small_default = /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives3.Path, { d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z" }) });

// packages/icons/build-module/library/envelope.mjs
var import_primitives4 = __toESM(require_primitives(), 1);
var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
var envelope_default = /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives4.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M3 7c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Zm2-.5h14c.3 0 .5.2.5.5v1L12 13.5 4.5 7.9V7c0-.3.2-.5.5-.5Zm-.5 3.3V17c0 .3.2.5.5.5h14c.3 0 .5-.2.5-.5V9.8L12 15.4 4.5 9.8Z" }) });

// packages/icons/build-module/library/error.mjs
var import_primitives5 = __toESM(require_primitives(), 1);
var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
var error_default = /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives5.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M12.218 5.377a.25.25 0 0 0-.436 0l-7.29 12.96a.25.25 0 0 0 .218.373h14.58a.25.25 0 0 0 .218-.372l-7.29-12.96Zm-1.743-.735c.669-1.19 2.381-1.19 3.05 0l7.29 12.96a1.75 1.75 0 0 1-1.525 2.608H4.71a1.75 1.75 0 0 1-1.525-2.608l7.29-12.96ZM12.75 17.46h-1.5v-1.5h1.5v1.5Zm-1.5-3h1.5v-5h-1.5v5Z" }) });

// packages/icons/build-module/library/link.mjs
var import_primitives6 = __toESM(require_primitives(), 1);
var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
var link_default = /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives6.Path, { d: "M10 17.389H8.444A5.194 5.194 0 1 1 8.444 7H10v1.5H8.444a3.694 3.694 0 0 0 0 7.389H10v1.5ZM14 7h1.556a5.194 5.194 0 0 1 0 10.39H14v-1.5h1.556a3.694 3.694 0 0 0 0-7.39H14V7Zm-4.5 6h5v-1.5h-5V13Z" }) });

// packages/icons/build-module/library/mobile.mjs
var import_primitives7 = __toESM(require_primitives(), 1);
var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
var mobile_default = /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives7.Path, { d: "M15 4H9c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H9c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h6c.3 0 .5.2.5.5v12zm-4.5-.5h2V16h-2v1.5z" }) });

// packages/icons/build-module/library/pencil.mjs
var import_primitives8 = __toESM(require_primitives(), 1);
var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
var pencil_default = /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives8.Path, { d: "m19 7-3-3-8.5 8.5-1 4 4-1L19 7Zm-7 11.5H5V20h7v-1.5Z" }) });

// packages/icons/build-module/library/seen.mjs
var import_primitives9 = __toESM(require_primitives(), 1);
var import_jsx_runtime16 = __toESM(require_jsx_runtime(), 1);
var seen_default = /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives9.Path, { d: "M3.99961 13C4.67043 13.3354 4.6703 13.3357 4.67017 13.3359L4.67298 13.3305C4.67621 13.3242 4.68184 13.3135 4.68988 13.2985C4.70595 13.2686 4.7316 13.2218 4.76695 13.1608C4.8377 13.0385 4.94692 12.8592 5.09541 12.6419C5.39312 12.2062 5.84436 11.624 6.45435 11.0431C7.67308 9.88241 9.49719 8.75 11.9996 8.75C14.502 8.75 16.3261 9.88241 17.5449 11.0431C18.1549 11.624 18.6061 12.2062 18.9038 12.6419C19.0523 12.8592 19.1615 13.0385 19.2323 13.1608C19.2676 13.2218 19.2933 13.2686 19.3093 13.2985C19.3174 13.3135 19.323 13.3242 19.3262 13.3305L19.3291 13.3359C19.3289 13.3357 19.3288 13.3354 19.9996 13C20.6704 12.6646 20.6703 12.6643 20.6701 12.664L20.6697 12.6632L20.6688 12.6614L20.6662 12.6563L20.6583 12.6408C20.6517 12.6282 20.6427 12.6108 20.631 12.5892C20.6078 12.5459 20.5744 12.4852 20.5306 12.4096C20.4432 12.2584 20.3141 12.0471 20.1423 11.7956C19.7994 11.2938 19.2819 10.626 18.5794 9.9569C17.1731 8.61759 14.9972 7.25 11.9996 7.25C9.00203 7.25 6.82614 8.61759 5.41987 9.9569C4.71736 10.626 4.19984 11.2938 3.85694 11.7956C3.68511 12.0471 3.55605 12.2584 3.4686 12.4096C3.42484 12.4852 3.39142 12.5459 3.36818 12.5892C3.35656 12.6108 3.34748 12.6282 3.34092 12.6408L3.33297 12.6563L3.33041 12.6614L3.32948 12.6632L3.32911 12.664C3.32894 12.6643 3.32879 12.6646 3.99961 13ZM11.9996 16C13.9326 16 15.4996 14.433 15.4996 12.5C15.4996 10.567 13.9326 9 11.9996 9C10.0666 9 8.49961 10.567 8.49961 12.5C8.49961 14.433 10.0666 16 11.9996 16Z" }) });

// packages/icons/build-module/library/unseen.mjs
var import_primitives10 = __toESM(require_primitives(), 1);
var import_jsx_runtime17 = __toESM(require_jsx_runtime(), 1);
var unseen_default = /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives10.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives10.Path, { d: "M20.7 12.7s0-.1-.1-.2c0-.2-.2-.4-.4-.6-.3-.5-.9-1.2-1.6-1.8-.7-.6-1.5-1.3-2.6-1.8l-.6 1.4c.9.4 1.6 1 2.1 1.5.6.6 1.1 1.2 1.4 1.6.1.2.3.4.3.5v.1l.7-.3.7-.3Zm-5.2-9.3-1.8 4c-.5-.1-1.1-.2-1.7-.2-3 0-5.2 1.4-6.6 2.7-.7.7-1.2 1.3-1.6 1.8-.2.3-.3.5-.4.6 0 0 0 .1-.1.2s0 0 .7.3l.7.3V13c0-.1.2-.3.3-.5.3-.4.7-1 1.4-1.6 1.2-1.2 3-2.3 5.5-2.3H13v.3c-.4 0-.8-.1-1.1-.1-1.9 0-3.5 1.6-3.5 3.5s.6 2.3 1.6 2.9l-2 4.4.9.4 7.6-16.2-.9-.4Zm-3 12.6c1.7-.2 3-1.7 3-3.5s-.2-1.4-.6-1.9L12.4 16Z" }) });

// packages/ui/build-module/collapsible-card/context.mjs
var import_element13 = __toESM(require_element(), 1);
var HeaderDescriptionIdContext = (0, import_element13.createContext)({
  setDescriptionId: () => {
  }
});

// packages/ui/build-module/collapsible-card/header.mjs
var import_jsx_runtime18 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='b49ef575a8']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "b49ef575a8");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{.cab17c7a373cb60d__header-content{flex:1;min-width:0}.dd89d27c4f15912d__header-trigger-positioner{align-self:center;flex-shrink:0;max-height:0;overflow:visible}.bcfab5f2448bafef__header-trigger-wrapper{border-radius:var(--wpds-border-radius-sm,2px);display:flex;translate:0 -50%}._3106f8d2b0330faa__header-trigger{@media not (prefers-reduced-motion){transition:rotate .15s ease-out}}._5d2dfcb4085c6d0f__header[data-panel-open] ._3106f8d2b0330faa__header-trigger{rotate:180deg}._5d2dfcb4085c6d0f__header[data-disabled] ._3106f8d2b0330faa__header-trigger{color:var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d)}.e34cf37ccd0d81e0__content{height:var(--collapsible-panel-height);margin-block-start:var(--wp-ui-card-header-content-margin);overflow:hidden;&._165c4572592944b2__overflowVisible{overflow:visible}&[hidden]:not([hidden=until-found]){display:none}&[data-ending-style],&[data-starting-style]{height:0}@media not (prefers-reduced-motion){transition:all .15s ease-out}}}@layer wp-ui-compositions{._41bfdbf7b6c087c2__content-inner{padding-block-start:0}._5d2dfcb4085c6d0f__header{align-items:stretch;display:flex;flex-direction:row;gap:var(--wpds-dimension-gap-sm,8px);outline:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}}}"));
  document.head.appendChild(style);
}
var style_default7 = { "header-content": "cab17c7a373cb60d__header-content", "header-trigger-positioner": "dd89d27c4f15912d__header-trigger-positioner", "header-trigger-wrapper": "bcfab5f2448bafef__header-trigger-wrapper", "header-trigger": "_3106f8d2b0330faa__header-trigger", "header": "_5d2dfcb4085c6d0f__header", "content": "e34cf37ccd0d81e0__content", "overflowVisible": "_165c4572592944b2__overflowVisible", "content-inner": "_41bfdbf7b6c087c2__content-inner" };
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='1fb29d3a3c']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "1fb29d3a3c");
  style.appendChild(document.createTextNode("._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,#0000);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 #0000);color:var(--_gcd-input-color,var(--wpds-color-fg-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,#0000);border-color:var(--_gcd-input-border-color-disabled,#0000);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid #0000)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-fg-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-medium,499));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid #0000);transition:var(--_gcd-a-transition,none)}"));
  document.head.appendChild(style);
}
var global_css_defense_default2 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='2a5ab8f3a7']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "2a5ab8f3a7");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._08e8a2e44959f892__outset-ring--focus,._970d04df7376df67__outset-ring--focus-within-except-active,.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible,.cd83dfc2126a0846__outset-ring--focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active,.ecadb9e080e2dfa5__outset-ring--focus-parent-visible{@media not (prefers-reduced-motion){--_gcd-a-transition:outline 0.1s ease-out;transition:outline .1s ease-out}outline:0 solid #0000;outline-offset:1px}._08e8a2e44959f892__outset-ring--focus:focus,._970d04df7376df67__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible:focus-within:has(:focus-visible),.cd83dfc2126a0846__outset-ring--focus-within:focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible:focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active:focus:not(:active),:focus-visible .ecadb9e080e2dfa5__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9))}}"));
  document.head.appendChild(style);
}
var focus_default = { "outset-ring--focus": "_08e8a2e44959f892__outset-ring--focus", "outset-ring--focus-except-active": "e25b2bdd7aa21721__outset-ring--focus-except-active", "outset-ring--focus-visible": "d0541bc9dd9dc7b6__outset-ring--focus-visible", "outset-ring--focus-within": "cd83dfc2126a0846__outset-ring--focus-within", "outset-ring--focus-within-except-active": "_970d04df7376df67__outset-ring--focus-within-except-active", "outset-ring--focus-within-visible": "c5cb3ee4bddaa8e4__outset-ring--focus-within-visible", "outset-ring--focus-parent-visible": "ecadb9e080e2dfa5__outset-ring--focus-parent-visible" };
var Header2 = (0, import_element14.forwardRef)(
  function CollapsibleCardHeader({ children, className, render: render4, ...restProps }, ref) {
    const [descriptionId, setDescriptionId] = (0, import_element14.useState)();
    const contextValue = (0, import_element14.useMemo)(
      () => ({ setDescriptionId }),
      [setDescriptionId]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(HeaderDescriptionIdContext.Provider, { value: contextValue, children: /* @__PURE__ */ (0, import_jsx_runtime18.jsxs)(
      Trigger,
      {
        className: clsx_default(style_default7.header, className),
        render: /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
          Header,
          {
            ref,
            render: render4,
            ...restProps
          }
        ),
        nativeButton: false,
        "aria-describedby": descriptionId,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime18.jsx)("div", { className: style_default7["header-content"], children }),
          /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
            "div",
            {
              className: clsx_default(
                style_default7["header-trigger-positioner"]
              ),
              children: /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
                "div",
                {
                  className: clsx_default(
                    style_default7["header-trigger-wrapper"],
                    global_css_defense_default2.div,
                    // While the interactive trigger element is the whole header,
                    // the focus ring will be displayed only on the icon to visually
                    // emulate it being the button.
                    focus_default["outset-ring--focus-parent-visible"]
                  ),
                  children: /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
                    Icon,
                    {
                      icon: chevron_down_default,
                      className: style_default7["header-trigger"]
                    }
                  )
                }
              )
            }
          )
        ]
      }
    ) });
  }
);

// packages/ui/build-module/collapsible-card/header-description.mjs
var import_element15 = __toESM(require_element(), 1);
var import_jsx_runtime19 = __toESM(require_jsx_runtime(), 1);
var HeaderDescription = (0, import_element15.forwardRef)(function CollapsibleCardHeaderDescription({ children, className, ...restProps }, ref) {
  const descriptionId = (0, import_element15.useId)();
  const { setDescriptionId } = (0, import_element15.useContext)(HeaderDescriptionIdContext);
  (0, import_element15.useEffect)(() => {
    setDescriptionId(descriptionId);
    return () => setDescriptionId(void 0);
  }, [descriptionId, setDescriptionId]);
  return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
    "div",
    {
      ref,
      id: descriptionId,
      "aria-hidden": "true",
      className,
      ...restProps,
      children
    }
  );
});

// packages/ui/build-module/collapsible-card/content.mjs
var import_element16 = __toESM(require_element(), 1);
var import_jsx_runtime20 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='b49ef575a8']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "b49ef575a8");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{.cab17c7a373cb60d__header-content{flex:1;min-width:0}.dd89d27c4f15912d__header-trigger-positioner{align-self:center;flex-shrink:0;max-height:0;overflow:visible}.bcfab5f2448bafef__header-trigger-wrapper{border-radius:var(--wpds-border-radius-sm,2px);display:flex;translate:0 -50%}._3106f8d2b0330faa__header-trigger{@media not (prefers-reduced-motion){transition:rotate .15s ease-out}}._5d2dfcb4085c6d0f__header[data-panel-open] ._3106f8d2b0330faa__header-trigger{rotate:180deg}._5d2dfcb4085c6d0f__header[data-disabled] ._3106f8d2b0330faa__header-trigger{color:var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d)}.e34cf37ccd0d81e0__content{height:var(--collapsible-panel-height);margin-block-start:var(--wp-ui-card-header-content-margin);overflow:hidden;&._165c4572592944b2__overflowVisible{overflow:visible}&[hidden]:not([hidden=until-found]){display:none}&[data-ending-style],&[data-starting-style]{height:0}@media not (prefers-reduced-motion){transition:all .15s ease-out}}}@layer wp-ui-compositions{._41bfdbf7b6c087c2__content-inner{padding-block-start:0}._5d2dfcb4085c6d0f__header{align-items:stretch;display:flex;flex-direction:row;gap:var(--wpds-dimension-gap-sm,8px);outline:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}}}"));
  document.head.appendChild(style);
}
var style_default8 = { "header-content": "cab17c7a373cb60d__header-content", "header-trigger-positioner": "dd89d27c4f15912d__header-trigger-positioner", "header-trigger-wrapper": "bcfab5f2448bafef__header-trigger-wrapper", "header-trigger": "_3106f8d2b0330faa__header-trigger", "header": "_5d2dfcb4085c6d0f__header", "content": "e34cf37ccd0d81e0__content", "overflowVisible": "_165c4572592944b2__overflowVisible", "content-inner": "_41bfdbf7b6c087c2__content-inner" };
var Content2 = (0, import_element16.forwardRef)(
  function CollapsibleCardContent({ className, render: render4, children, hiddenUntilFound = true, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
      Panel,
      {
        ref,
        className: (state) => clsx_default(
          style_default8.content,
          state.open && state.transitionStatus === "idle" && style_default8.overflowVisible,
          className
        ),
        hiddenUntilFound,
        ...restProps,
        children: /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
          Content,
          {
            className: style_default8["content-inner"],
            render: render4,
            children
          }
        )
      }
    );
  }
);

// packages/ui/build-module/stack/stack.mjs
var import_element17 = __toESM(require_element(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='b51ff41489']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "b51ff41489");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._19ce0419607e1896__stack{display:flex}}"));
  document.head.appendChild(style);
}
var style_default9 = { "stack": "_19ce0419607e1896__stack" };
var gapTokens = {
  xs: "var(--wpds-dimension-gap-xs, 4px)",
  sm: "var(--wpds-dimension-gap-sm, 8px)",
  md: "var(--wpds-dimension-gap-md, 12px)",
  lg: "var(--wpds-dimension-gap-lg, 16px)",
  xl: "var(--wpds-dimension-gap-xl, 24px)",
  "2xl": "var(--wpds-dimension-gap-2xl, 32px)",
  "3xl": "var(--wpds-dimension-gap-3xl, 40px)"
};
var Stack = (0, import_element17.forwardRef)(function Stack2({ direction, gap, align, justify, wrap, render: render4, ...props }, ref) {
  const style = {
    gap: gap && gapTokens[gap],
    alignItems: align,
    justifyContent: justify,
    flexDirection: direction,
    flexWrap: wrap
  };
  const element = useRender({
    render: render4,
    ref,
    props: mergeProps(props, { style, className: style_default9.stack })
  });
  return element;
});

// packages/ui/build-module/link/link.mjs
var import_element18 = __toESM(require_element(), 1);
var import_i18n = __toESM(require_i18n(), 1);
var import_jsx_runtime21 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='e3ae230cea']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "e3ae230cea");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}"));
  document.head.appendChild(style);
}
var resets_default2 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='2a5ab8f3a7']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "2a5ab8f3a7");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._08e8a2e44959f892__outset-ring--focus,._970d04df7376df67__outset-ring--focus-within-except-active,.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible,.cd83dfc2126a0846__outset-ring--focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active,.ecadb9e080e2dfa5__outset-ring--focus-parent-visible{@media not (prefers-reduced-motion){--_gcd-a-transition:outline 0.1s ease-out;transition:outline .1s ease-out}outline:0 solid #0000;outline-offset:1px}._08e8a2e44959f892__outset-ring--focus:focus,._970d04df7376df67__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible:focus-within:has(:focus-visible),.cd83dfc2126a0846__outset-ring--focus-within:focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible:focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active:focus:not(:active),:focus-visible .ecadb9e080e2dfa5__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9))}}"));
  document.head.appendChild(style);
}
var focus_default2 = { "outset-ring--focus": "_08e8a2e44959f892__outset-ring--focus", "outset-ring--focus-except-active": "e25b2bdd7aa21721__outset-ring--focus-except-active", "outset-ring--focus-visible": "d0541bc9dd9dc7b6__outset-ring--focus-visible", "outset-ring--focus-within": "cd83dfc2126a0846__outset-ring--focus-within", "outset-ring--focus-within-except-active": "_970d04df7376df67__outset-ring--focus-within-except-active", "outset-ring--focus-within-visible": "c5cb3ee4bddaa8e4__outset-ring--focus-within-visible", "outset-ring--focus-parent-visible": "ecadb9e080e2dfa5__outset-ring--focus-parent-visible" };
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='3cfc19ad21']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "3cfc19ad21");
  style.appendChild(document.createTextNode('@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{.d4250949359b05ce__link{text-decoration-thickness:from-font;text-underline-offset:.2em}.c6055659b8e2cd2c__is-brand,.c6055659b8e2cd2c__is-brand:visited{--_gcd-a-color:var(--wpds-color-fg-interactive-brand,var(--wp-admin-theme-color,#3858e9));color:var(--wpds-color-fg-interactive-brand,var(--wp-admin-theme-color,#3858e9))}.c6055659b8e2cd2c__is-brand:active,.c6055659b8e2cd2c__is-brand:hover{--_gcd-a-color:var(--wpds-color-fg-interactive-brand-active,var(--wp-admin-theme-color,#3858e9));color:var(--wpds-color-fg-interactive-brand-active,var(--wp-admin-theme-color,#3858e9))}._92e0dfcaeee15b88__is-neutral,._92e0dfcaeee15b88__is-neutral:visited{--_gcd-a-color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);text-decoration-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d)}._92e0dfcaeee15b88__is-neutral:active,._92e0dfcaeee15b88__is-neutral:hover{--_gcd-a-color:var(--wpds-color-fg-interactive-neutral-active,#1e1e1e);color:var(--wpds-color-fg-interactive-neutral-active,#1e1e1e)}.cf122a9bf1035d42__is-unstyled{--_gcd-a-color:inherit;color:inherit;text-decoration:none}._0cb411afac4c86c7__link-icon{display:inline-block;font-weight:var(--wpds-typography-font-weight-regular,400);margin-inline-start:var(--wpds-dimension-padding-xs,4px);text-decoration:none}._0cb411afac4c86c7__link-icon:after{content:"\\2197"}._0cb411afac4c86c7__link-icon:dir(rtl):after{content:"\\2196"}}'));
  document.head.appendChild(style);
}
var style_default10 = { "link": "d4250949359b05ce__link", "is-brand": "c6055659b8e2cd2c__is-brand", "is-neutral": "_92e0dfcaeee15b88__is-neutral", "is-unstyled": "cf122a9bf1035d42__is-unstyled", "link-icon": "_0cb411afac4c86c7__link-icon" };
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='1fb29d3a3c']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "1fb29d3a3c");
  style.appendChild(document.createTextNode("._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,#0000);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 #0000);color:var(--_gcd-input-color,var(--wpds-color-fg-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,#0000);border-color:var(--_gcd-input-border-color-disabled,#0000);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid #0000)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-fg-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-medium,499));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid #0000);transition:var(--_gcd-a-transition,none)}"));
  document.head.appendChild(style);
}
var global_css_defense_default3 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
var Link = (0, import_element18.forwardRef)(function Link2({
  children,
  variant = "default",
  tone = "brand",
  openInNewTab = false,
  render: render4,
  className,
  ...props
}, ref) {
  const element = useRender({
    render: render4,
    defaultTagName: "a",
    ref,
    props: mergeProps(props, {
      className: clsx_default(
        global_css_defense_default3.a,
        resets_default2["box-sizing"],
        focus_default2["outset-ring--focus"],
        variant !== "unstyled" && style_default10.link,
        variant !== "unstyled" && style_default10[`is-${tone}`],
        variant === "unstyled" && style_default10["is-unstyled"],
        className
      ),
      target: openInNewTab ? "_blank" : void 0,
      children: /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)(import_jsx_runtime21.Fragment, { children: [
        children,
        openInNewTab && /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
          "span",
          {
            className: style_default10["link-icon"],
            role: "img",
            "aria-label": (
              /* translators: accessibility text appended to link text */
              (0, import_i18n.__)("(opens in a new tab)")
            )
          }
        )
      ] })
    })
  });
  return element;
});

// packages/admin-ui/build-module/breadcrumbs/index.mjs
var import_jsx_runtime22 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='846edc217c']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "846edc217c");
  style.appendChild(document.createTextNode("._5f4053eba32ce092__list{list-style:none;margin:0;padding:0}._5f4053eba32ce092__list>li{flex-shrink:0}._5f4053eba32ce092__list>li:last-child{flex-shrink:1;min-width:0}.d1978551971a3360__current{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}._6d9152063d443376__separator{color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);margin:0 var(--wpds-dimension-gap-sm,8px);user-select:none}"));
  document.head.appendChild(style);
}
var style_default11 = { "list": "_5f4053eba32ce092__list", "current": "d1978551971a3360__current", "separator": "_6d9152063d443376__separator" };
var Breadcrumbs = ({ items }) => {
  if (!items.length) {
    return null;
  }
  const precedingItems = items.slice(0, -1);
  const lastItem = items[items.length - 1];
  if (true) {
    const invalidItem = precedingItems.find((item) => !item.to);
    if (invalidItem) {
      throw new Error(
        `Breadcrumbs: item "${invalidItem.label}" is missing a \`to\` prop. All items except the last one must have a \`to\` prop.`
      );
    }
  }
  return /* @__PURE__ */ (0, import_jsx_runtime22.jsx)("nav", { "aria-label": (0, import_i18n2.__)("Breadcrumbs"), children: /* @__PURE__ */ (0, import_jsx_runtime22.jsxs)(
    Stack,
    {
      render: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)("ul", {}),
      direction: "row",
      align: "center",
      className: style_default11.list,
      children: [
        precedingItems.map((item, index) => /* @__PURE__ */ (0, import_jsx_runtime22.jsxs)("li", { children: [
          /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
            Text,
            {
              variant: "body-lg",
              render: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
                Link,
                {
                  tone: "neutral",
                  render: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(RouterLink, { to: item.to })
                }
              ),
              children: item.label
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
            Text,
            {
              variant: "body-lg",
              "aria-hidden": "true",
              className: style_default11.separator,
              children: "/"
            }
          )
        ] }, index)),
        /* @__PURE__ */ (0, import_jsx_runtime22.jsx)("li", { children: lastItem.to ? /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
          Text,
          {
            variant: "body-lg",
            render: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
              Link,
              {
                tone: "neutral",
                render: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(RouterLink, { to: lastItem.to })
              }
            ),
            children: lastItem.label
          }
        ) : /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
          Text,
          {
            variant: "heading-lg",
            render: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)("h1", {}),
            className: style_default11.current,
            children: lastItem.label
          }
        ) })
      ]
    }
  ) });
};
var breadcrumbs_default = Breadcrumbs;

// packages/admin-ui/build-module/navigable-region/index.mjs
var import_element19 = __toESM(require_element(), 1);
var import_jsx_runtime23 = __toESM(require_jsx_runtime(), 1);
var NavigableRegion = (0, import_element19.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
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
var import_jsx_runtime24 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='aa9c241ccc']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "aa9c241ccc");
  style.appendChild(document.createTextNode("._956b6df0898efed0__page{text-wrap:pretty;background-color:var(--wpds-color-bg-surface-neutral,#fcfcfc);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-flow:column;height:100%;position:relative;z-index:1}._0625b55e82a0d93d__header{background:var(--wpds-color-bg-surface-neutral-strong,#fff);border-block-end:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral-weak,#e4e4e4);inset-block-start:0;padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px);position:sticky;z-index:1}.a43c44d5ae28b2e8__header-content{min-height:calc(var(--wpds-dimension-base, 4px)*8)}.b7cb5b9daf3a3b25__header-actions{flex-shrink:0}._8113be94e7caf73c__header-title{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}._9a776c7f70996f61__header-visual{display:grid;flex-shrink:0;grid-template-columns:1fr;grid-template-rows:1fr;height:calc(var(--wpds-dimension-base, 4px)*6);width:calc(var(--wpds-dimension-base, 4px)*6);>*{grid-column:1/-1;grid-row:1/-1;max-height:100%;max-width:100%}}.d5e0920cd15d35bc__sidebar-toggle-slot:empty{display:none}._60fea2f6bf5319cd__header-subtitle{color:var(--wpds-color-fg-content-neutral-weak,#707070);padding-block-end:var(--wpds-dimension-padding-xs,4px)}.be5e57d029ec4036__content{display:flex;flex-direction:column;flex-grow:1;overflow:auto;&._128806d0b26e3a50__has-padding{padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px)}}"));
  document.head.appendChild(style);
}
var style_default12 = { "page": "_956b6df0898efed0__page", "header": "_0625b55e82a0d93d__header", "header-content": "a43c44d5ae28b2e8__header-content", "header-actions": "b7cb5b9daf3a3b25__header-actions", "header-title": "_8113be94e7caf73c__header-title", "header-visual": "_9a776c7f70996f61__header-visual", "sidebar-toggle-slot": "d5e0920cd15d35bc__sidebar-toggle-slot", "header-subtitle": "_60fea2f6bf5319cd__header-subtitle", "content": "be5e57d029ec4036__content", "has-padding": "_128806d0b26e3a50__has-padding" };
function Header3({
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
  return /* @__PURE__ */ (0, import_jsx_runtime24.jsxs)(
    Stack,
    {
      direction: "column",
      className: style_default12.header,
      render: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)("header", {}),
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime24.jsxs)(
          Stack,
          {
            className: style_default12["header-content"],
            direction: "row",
            gap: "sm",
            justify: "space-between",
            children: [
              /* @__PURE__ */ (0, import_jsx_runtime24.jsxs)(Stack, { direction: "row", gap: "sm", align: "center", justify: "start", children: [
                showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
                  SidebarToggleSlot,
                  {
                    bubblesVirtually: true,
                    className: style_default12["sidebar-toggle-slot"]
                  }
                ),
                visual && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
                  "div",
                  {
                    className: style_default12["header-visual"],
                    "aria-hidden": "true",
                    children: visual
                  }
                ),
                title && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
                  Text,
                  {
                    className: style_default12["header-title"],
                    render: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(HeadingTag, {}),
                    variant: "heading-lg",
                    children: title
                  }
                ),
                breadcrumbs,
                badges
              ] }),
              actions && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
                Stack,
                {
                  align: "center",
                  className: style_default12["header-actions"],
                  direction: "row",
                  gap: "sm",
                  children: actions
                }
              )
            ]
          }
        ),
        subTitle && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
          Text,
          {
            render: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)("p", {}),
            variant: "body-md",
            className: style_default12["header-subtitle"],
            children: subTitle
          }
        )
      ]
    }
  );
}

// packages/admin-ui/build-module/page/index.mjs
var import_jsx_runtime25 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='aa9c241ccc']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "aa9c241ccc");
  style.appendChild(document.createTextNode("._956b6df0898efed0__page{text-wrap:pretty;background-color:var(--wpds-color-bg-surface-neutral,#fcfcfc);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-flow:column;height:100%;position:relative;z-index:1}._0625b55e82a0d93d__header{background:var(--wpds-color-bg-surface-neutral-strong,#fff);border-block-end:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral-weak,#e4e4e4);inset-block-start:0;padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px);position:sticky;z-index:1}.a43c44d5ae28b2e8__header-content{min-height:calc(var(--wpds-dimension-base, 4px)*8)}.b7cb5b9daf3a3b25__header-actions{flex-shrink:0}._8113be94e7caf73c__header-title{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}._9a776c7f70996f61__header-visual{display:grid;flex-shrink:0;grid-template-columns:1fr;grid-template-rows:1fr;height:calc(var(--wpds-dimension-base, 4px)*6);width:calc(var(--wpds-dimension-base, 4px)*6);>*{grid-column:1/-1;grid-row:1/-1;max-height:100%;max-width:100%}}.d5e0920cd15d35bc__sidebar-toggle-slot:empty{display:none}._60fea2f6bf5319cd__header-subtitle{color:var(--wpds-color-fg-content-neutral-weak,#707070);padding-block-end:var(--wpds-dimension-padding-xs,4px)}.be5e57d029ec4036__content{display:flex;flex-direction:column;flex-grow:1;overflow:auto;&._128806d0b26e3a50__has-padding{padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px)}}"));
  document.head.appendChild(style);
}
var style_default13 = { "page": "_956b6df0898efed0__page", "header": "_0625b55e82a0d93d__header", "header-content": "a43c44d5ae28b2e8__header-content", "header-actions": "b7cb5b9daf3a3b25__header-actions", "header-title": "_8113be94e7caf73c__header-title", "header-visual": "_9a776c7f70996f61__header-visual", "sidebar-toggle-slot": "d5e0920cd15d35bc__sidebar-toggle-slot", "header-subtitle": "_60fea2f6bf5319cd__header-subtitle", "content": "be5e57d029ec4036__content", "has-padding": "_128806d0b26e3a50__has-padding" };
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
  const classes = clsx_default(style_default13.page, className);
  const effectiveAriaLabel = ariaLabel ?? (typeof title === "string" ? title : "");
  return /* @__PURE__ */ (0, import_jsx_runtime25.jsxs)(navigable_region_default, { className: classes, ariaLabel: effectiveAriaLabel, children: [
    (title || breadcrumbs || badges || actions || visual) && /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
      Header3,
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
    hasPadding ? /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
      "div",
      {
        className: clsx_default(
          style_default13.content,
          style_default13["has-padding"]
        ),
        children
      }
    ) : children
  ] });
}
Page.SidebarToggleFill = SidebarToggleFill;
var page_default = Page;

// routes/taxonomy-edit/stage.tsx
var import_components26 = __toESM(require_components());
var import_compose4 = __toESM(require_compose());
var import_core_data = __toESM(require_core_data());
var import_data = __toESM(require_data());

// packages/dataviews/build-module/constants.mjs
var import_i18n3 = __toESM(require_i18n(), 1);
var OPERATOR_IS_ANY = "isAny";
var OPERATOR_IS_NONE = "isNone";
var OPERATOR_IS_ALL = "isAll";
var OPERATOR_IS_NOT_ALL = "isNotAll";
var OPERATOR_BETWEEN = "between";
var OPERATOR_IN_THE_PAST = "inThePast";
var OPERATOR_OVER = "over";
var OPERATOR_IS = "is";
var OPERATOR_IS_NOT = "isNot";
var OPERATOR_LESS_THAN = "lessThan";
var OPERATOR_GREATER_THAN = "greaterThan";
var OPERATOR_LESS_THAN_OR_EQUAL = "lessThanOrEqual";
var OPERATOR_GREATER_THAN_OR_EQUAL = "greaterThanOrEqual";
var OPERATOR_BEFORE = "before";
var OPERATOR_AFTER = "after";
var OPERATOR_BEFORE_INC = "beforeInc";
var OPERATOR_AFTER_INC = "afterInc";
var OPERATOR_CONTAINS = "contains";
var OPERATOR_NOT_CONTAINS = "notContains";
var OPERATOR_STARTS_WITH = "startsWith";
var OPERATOR_ON = "on";
var OPERATOR_NOT_ON = "notOn";
var sortLabels = {
  asc: (0, import_i18n3.__)("Sort ascending"),
  desc: (0, import_i18n3.__)("Sort descending")
};

// packages/dataviews/build-module/lock-unlock.mjs
var import_private_apis = __toESM(require_private_apis(), 1);
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/dataviews"
);

// packages/dataviews/build-module/hooks/use-elements.mjs
var import_element20 = __toESM(require_element(), 1);
var EMPTY_ARRAY2 = [];
function useElements({
  elements,
  getElements
}) {
  const staticElements = Array.isArray(elements) && elements.length > 0 ? elements : EMPTY_ARRAY2;
  const [records, setRecords] = (0, import_element20.useState)(staticElements);
  const [isLoading, setIsLoading] = (0, import_element20.useState)(false);
  (0, import_element20.useEffect)(() => {
    if (!getElements) {
      setRecords(staticElements);
      return;
    }
    let cancelled = false;
    setIsLoading(true);
    getElements().then((fetchedElements) => {
      if (!cancelled) {
        const dynamicElements = Array.isArray(fetchedElements) && fetchedElements.length > 0 ? fetchedElements : staticElements;
        setRecords(dynamicElements);
      }
    }).catch(() => {
      if (!cancelled) {
        setRecords(staticElements);
      }
    }).finally(() => {
      if (!cancelled) {
        setIsLoading(false);
      }
    });
    return () => {
      cancelled = true;
    };
  }, [getElements, staticElements]);
  return {
    elements: records,
    isLoading
  };
}

// packages/dataviews/node_modules/date-fns/constants.js
var daysInYear = 365.2425;
var maxTime = Math.pow(10, 8) * 24 * 60 * 60 * 1e3;
var minTime = -maxTime;
var millisecondsInWeek = 6048e5;
var millisecondsInDay = 864e5;
var secondsInHour = 3600;
var secondsInDay = secondsInHour * 24;
var secondsInWeek = secondsInDay * 7;
var secondsInYear = secondsInDay * daysInYear;
var secondsInMonth = secondsInYear / 12;
var secondsInQuarter = secondsInMonth * 3;
var constructFromSymbol = /* @__PURE__ */ Symbol.for("constructDateFrom");

// packages/dataviews/node_modules/date-fns/constructFrom.js
function constructFrom(date, value) {
  if (typeof date === "function") return date(value);
  if (date && typeof date === "object" && constructFromSymbol in date)
    return date[constructFromSymbol](value);
  if (date instanceof Date) return new date.constructor(value);
  return new Date(value);
}

// packages/dataviews/node_modules/date-fns/toDate.js
function toDate(argument, context) {
  return constructFrom(context || argument, argument);
}

// packages/dataviews/node_modules/date-fns/addDays.js
function addDays(date, amount, options) {
  const _date = toDate(date, options?.in);
  if (isNaN(amount)) return constructFrom(options?.in || date, NaN);
  if (!amount) return _date;
  _date.setDate(_date.getDate() + amount);
  return _date;
}

// packages/dataviews/node_modules/date-fns/addMonths.js
function addMonths(date, amount, options) {
  const _date = toDate(date, options?.in);
  if (isNaN(amount)) return constructFrom(options?.in || date, NaN);
  if (!amount) {
    return _date;
  }
  const dayOfMonth = _date.getDate();
  const endOfDesiredMonth = constructFrom(options?.in || date, _date.getTime());
  endOfDesiredMonth.setMonth(_date.getMonth() + amount + 1, 0);
  const daysInMonth = endOfDesiredMonth.getDate();
  if (dayOfMonth >= daysInMonth) {
    return endOfDesiredMonth;
  } else {
    _date.setFullYear(
      endOfDesiredMonth.getFullYear(),
      endOfDesiredMonth.getMonth(),
      dayOfMonth
    );
    return _date;
  }
}

// packages/dataviews/node_modules/date-fns/_lib/defaultOptions.js
var defaultOptions = {};
function getDefaultOptions() {
  return defaultOptions;
}

// packages/dataviews/node_modules/date-fns/startOfWeek.js
function startOfWeek(date, options) {
  const defaultOptions2 = getDefaultOptions();
  const weekStartsOn = options?.weekStartsOn ?? options?.locale?.options?.weekStartsOn ?? defaultOptions2.weekStartsOn ?? defaultOptions2.locale?.options?.weekStartsOn ?? 0;
  const _date = toDate(date, options?.in);
  const day = _date.getDay();
  const diff = (day < weekStartsOn ? 7 : 0) + day - weekStartsOn;
  _date.setDate(_date.getDate() - diff);
  _date.setHours(0, 0, 0, 0);
  return _date;
}

// packages/dataviews/node_modules/date-fns/startOfISOWeek.js
function startOfISOWeek(date, options) {
  return startOfWeek(date, { ...options, weekStartsOn: 1 });
}

// packages/dataviews/node_modules/date-fns/getISOWeekYear.js
function getISOWeekYear(date, options) {
  const _date = toDate(date, options?.in);
  const year = _date.getFullYear();
  const fourthOfJanuaryOfNextYear = constructFrom(_date, 0);
  fourthOfJanuaryOfNextYear.setFullYear(year + 1, 0, 4);
  fourthOfJanuaryOfNextYear.setHours(0, 0, 0, 0);
  const startOfNextYear = startOfISOWeek(fourthOfJanuaryOfNextYear);
  const fourthOfJanuaryOfThisYear = constructFrom(_date, 0);
  fourthOfJanuaryOfThisYear.setFullYear(year, 0, 4);
  fourthOfJanuaryOfThisYear.setHours(0, 0, 0, 0);
  const startOfThisYear = startOfISOWeek(fourthOfJanuaryOfThisYear);
  if (_date.getTime() >= startOfNextYear.getTime()) {
    return year + 1;
  } else if (_date.getTime() >= startOfThisYear.getTime()) {
    return year;
  } else {
    return year - 1;
  }
}

// packages/dataviews/node_modules/date-fns/_lib/getTimezoneOffsetInMilliseconds.js
function getTimezoneOffsetInMilliseconds(date) {
  const _date = toDate(date);
  const utcDate = new Date(
    Date.UTC(
      _date.getFullYear(),
      _date.getMonth(),
      _date.getDate(),
      _date.getHours(),
      _date.getMinutes(),
      _date.getSeconds(),
      _date.getMilliseconds()
    )
  );
  utcDate.setUTCFullYear(_date.getFullYear());
  return +date - +utcDate;
}

// packages/dataviews/node_modules/date-fns/_lib/normalizeDates.js
function normalizeDates(context, ...dates) {
  const normalize = constructFrom.bind(
    null,
    context || dates.find((date) => typeof date === "object")
  );
  return dates.map(normalize);
}

// packages/dataviews/node_modules/date-fns/startOfDay.js
function startOfDay(date, options) {
  const _date = toDate(date, options?.in);
  _date.setHours(0, 0, 0, 0);
  return _date;
}

// packages/dataviews/node_modules/date-fns/differenceInCalendarDays.js
function differenceInCalendarDays(laterDate, earlierDate, options) {
  const [laterDate_, earlierDate_] = normalizeDates(
    options?.in,
    laterDate,
    earlierDate
  );
  const laterStartOfDay = startOfDay(laterDate_);
  const earlierStartOfDay = startOfDay(earlierDate_);
  const laterTimestamp = +laterStartOfDay - getTimezoneOffsetInMilliseconds(laterStartOfDay);
  const earlierTimestamp = +earlierStartOfDay - getTimezoneOffsetInMilliseconds(earlierStartOfDay);
  return Math.round((laterTimestamp - earlierTimestamp) / millisecondsInDay);
}

// packages/dataviews/node_modules/date-fns/startOfISOWeekYear.js
function startOfISOWeekYear(date, options) {
  const year = getISOWeekYear(date, options);
  const fourthOfJanuary = constructFrom(options?.in || date, 0);
  fourthOfJanuary.setFullYear(year, 0, 4);
  fourthOfJanuary.setHours(0, 0, 0, 0);
  return startOfISOWeek(fourthOfJanuary);
}

// packages/dataviews/node_modules/date-fns/addWeeks.js
function addWeeks(date, amount, options) {
  return addDays(date, amount * 7, options);
}

// packages/dataviews/node_modules/date-fns/addYears.js
function addYears(date, amount, options) {
  return addMonths(date, amount * 12, options);
}

// packages/dataviews/node_modules/date-fns/isDate.js
function isDate(value) {
  return value instanceof Date || typeof value === "object" && Object.prototype.toString.call(value) === "[object Date]";
}

// packages/dataviews/node_modules/date-fns/isValid.js
function isValid(date) {
  return !(!isDate(date) && typeof date !== "number" || isNaN(+toDate(date)));
}

// packages/dataviews/node_modules/date-fns/startOfMonth.js
function startOfMonth(date, options) {
  const _date = toDate(date, options?.in);
  _date.setDate(1);
  _date.setHours(0, 0, 0, 0);
  return _date;
}

// packages/dataviews/node_modules/date-fns/startOfYear.js
function startOfYear(date, options) {
  const date_ = toDate(date, options?.in);
  date_.setFullYear(date_.getFullYear(), 0, 1);
  date_.setHours(0, 0, 0, 0);
  return date_;
}

// packages/dataviews/node_modules/date-fns/locale/en-US/_lib/formatDistance.js
var formatDistanceLocale = {
  lessThanXSeconds: {
    one: "less than a second",
    other: "less than {{count}} seconds"
  },
  xSeconds: {
    one: "1 second",
    other: "{{count}} seconds"
  },
  halfAMinute: "half a minute",
  lessThanXMinutes: {
    one: "less than a minute",
    other: "less than {{count}} minutes"
  },
  xMinutes: {
    one: "1 minute",
    other: "{{count}} minutes"
  },
  aboutXHours: {
    one: "about 1 hour",
    other: "about {{count}} hours"
  },
  xHours: {
    one: "1 hour",
    other: "{{count}} hours"
  },
  xDays: {
    one: "1 day",
    other: "{{count}} days"
  },
  aboutXWeeks: {
    one: "about 1 week",
    other: "about {{count}} weeks"
  },
  xWeeks: {
    one: "1 week",
    other: "{{count}} weeks"
  },
  aboutXMonths: {
    one: "about 1 month",
    other: "about {{count}} months"
  },
  xMonths: {
    one: "1 month",
    other: "{{count}} months"
  },
  aboutXYears: {
    one: "about 1 year",
    other: "about {{count}} years"
  },
  xYears: {
    one: "1 year",
    other: "{{count}} years"
  },
  overXYears: {
    one: "over 1 year",
    other: "over {{count}} years"
  },
  almostXYears: {
    one: "almost 1 year",
    other: "almost {{count}} years"
  }
};
var formatDistance = (token, count, options) => {
  let result;
  const tokenValue = formatDistanceLocale[token];
  if (typeof tokenValue === "string") {
    result = tokenValue;
  } else if (count === 1) {
    result = tokenValue.one;
  } else {
    result = tokenValue.other.replace("{{count}}", count.toString());
  }
  if (options?.addSuffix) {
    if (options.comparison && options.comparison > 0) {
      return "in " + result;
    } else {
      return result + " ago";
    }
  }
  return result;
};

// packages/dataviews/node_modules/date-fns/locale/_lib/buildFormatLongFn.js
function buildFormatLongFn(args) {
  return (options = {}) => {
    const width = options.width ? String(options.width) : args.defaultWidth;
    const format6 = args.formats[width] || args.formats[args.defaultWidth];
    return format6;
  };
}

// packages/dataviews/node_modules/date-fns/locale/en-US/_lib/formatLong.js
var dateFormats = {
  full: "EEEE, MMMM do, y",
  long: "MMMM do, y",
  medium: "MMM d, y",
  short: "MM/dd/yyyy"
};
var timeFormats = {
  full: "h:mm:ss a zzzz",
  long: "h:mm:ss a z",
  medium: "h:mm:ss a",
  short: "h:mm a"
};
var dateTimeFormats = {
  full: "{{date}} 'at' {{time}}",
  long: "{{date}} 'at' {{time}}",
  medium: "{{date}}, {{time}}",
  short: "{{date}}, {{time}}"
};
var formatLong = {
  date: buildFormatLongFn({
    formats: dateFormats,
    defaultWidth: "full"
  }),
  time: buildFormatLongFn({
    formats: timeFormats,
    defaultWidth: "full"
  }),
  dateTime: buildFormatLongFn({
    formats: dateTimeFormats,
    defaultWidth: "full"
  })
};

// packages/dataviews/node_modules/date-fns/locale/en-US/_lib/formatRelative.js
var formatRelativeLocale = {
  lastWeek: "'last' eeee 'at' p",
  yesterday: "'yesterday at' p",
  today: "'today at' p",
  tomorrow: "'tomorrow at' p",
  nextWeek: "eeee 'at' p",
  other: "P"
};
var formatRelative = (token, _date, _baseDate, _options) => formatRelativeLocale[token];

// packages/dataviews/node_modules/date-fns/locale/_lib/buildLocalizeFn.js
function buildLocalizeFn(args) {
  return (value, options) => {
    const context = options?.context ? String(options.context) : "standalone";
    let valuesArray;
    if (context === "formatting" && args.formattingValues) {
      const defaultWidth = args.defaultFormattingWidth || args.defaultWidth;
      const width = options?.width ? String(options.width) : defaultWidth;
      valuesArray = args.formattingValues[width] || args.formattingValues[defaultWidth];
    } else {
      const defaultWidth = args.defaultWidth;
      const width = options?.width ? String(options.width) : args.defaultWidth;
      valuesArray = args.values[width] || args.values[defaultWidth];
    }
    const index = args.argumentCallback ? args.argumentCallback(value) : value;
    return valuesArray[index];
  };
}

// packages/dataviews/node_modules/date-fns/locale/en-US/_lib/localize.js
var eraValues = {
  narrow: ["B", "A"],
  abbreviated: ["BC", "AD"],
  wide: ["Before Christ", "Anno Domini"]
};
var quarterValues = {
  narrow: ["1", "2", "3", "4"],
  abbreviated: ["Q1", "Q2", "Q3", "Q4"],
  wide: ["1st quarter", "2nd quarter", "3rd quarter", "4th quarter"]
};
var monthValues = {
  narrow: ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"],
  abbreviated: [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec"
  ],
  wide: [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  ]
};
var dayValues = {
  narrow: ["S", "M", "T", "W", "T", "F", "S"],
  short: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
  abbreviated: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
  wide: [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday"
  ]
};
var dayPeriodValues = {
  narrow: {
    am: "a",
    pm: "p",
    midnight: "mi",
    noon: "n",
    morning: "morning",
    afternoon: "afternoon",
    evening: "evening",
    night: "night"
  },
  abbreviated: {
    am: "AM",
    pm: "PM",
    midnight: "midnight",
    noon: "noon",
    morning: "morning",
    afternoon: "afternoon",
    evening: "evening",
    night: "night"
  },
  wide: {
    am: "a.m.",
    pm: "p.m.",
    midnight: "midnight",
    noon: "noon",
    morning: "morning",
    afternoon: "afternoon",
    evening: "evening",
    night: "night"
  }
};
var formattingDayPeriodValues = {
  narrow: {
    am: "a",
    pm: "p",
    midnight: "mi",
    noon: "n",
    morning: "in the morning",
    afternoon: "in the afternoon",
    evening: "in the evening",
    night: "at night"
  },
  abbreviated: {
    am: "AM",
    pm: "PM",
    midnight: "midnight",
    noon: "noon",
    morning: "in the morning",
    afternoon: "in the afternoon",
    evening: "in the evening",
    night: "at night"
  },
  wide: {
    am: "a.m.",
    pm: "p.m.",
    midnight: "midnight",
    noon: "noon",
    morning: "in the morning",
    afternoon: "in the afternoon",
    evening: "in the evening",
    night: "at night"
  }
};
var ordinalNumber = (dirtyNumber, _options) => {
  const number = Number(dirtyNumber);
  const rem100 = number % 100;
  if (rem100 > 20 || rem100 < 10) {
    switch (rem100 % 10) {
      case 1:
        return number + "st";
      case 2:
        return number + "nd";
      case 3:
        return number + "rd";
    }
  }
  return number + "th";
};
var localize = {
  ordinalNumber,
  era: buildLocalizeFn({
    values: eraValues,
    defaultWidth: "wide"
  }),
  quarter: buildLocalizeFn({
    values: quarterValues,
    defaultWidth: "wide",
    argumentCallback: (quarter) => quarter - 1
  }),
  month: buildLocalizeFn({
    values: monthValues,
    defaultWidth: "wide"
  }),
  day: buildLocalizeFn({
    values: dayValues,
    defaultWidth: "wide"
  }),
  dayPeriod: buildLocalizeFn({
    values: dayPeriodValues,
    defaultWidth: "wide",
    formattingValues: formattingDayPeriodValues,
    defaultFormattingWidth: "wide"
  })
};

// packages/dataviews/node_modules/date-fns/locale/_lib/buildMatchFn.js
function buildMatchFn(args) {
  return (string, options = {}) => {
    const width = options.width;
    const matchPattern = width && args.matchPatterns[width] || args.matchPatterns[args.defaultMatchWidth];
    const matchResult = string.match(matchPattern);
    if (!matchResult) {
      return null;
    }
    const matchedString = matchResult[0];
    const parsePatterns = width && args.parsePatterns[width] || args.parsePatterns[args.defaultParseWidth];
    const key = Array.isArray(parsePatterns) ? findIndex(parsePatterns, (pattern) => pattern.test(matchedString)) : (
      // [TODO] -- I challenge you to fix the type
      findKey(parsePatterns, (pattern) => pattern.test(matchedString))
    );
    let value;
    value = args.valueCallback ? args.valueCallback(key) : key;
    value = options.valueCallback ? (
      // [TODO] -- I challenge you to fix the type
      options.valueCallback(value)
    ) : value;
    const rest = string.slice(matchedString.length);
    return { value, rest };
  };
}
function findKey(object, predicate) {
  for (const key in object) {
    if (Object.prototype.hasOwnProperty.call(object, key) && predicate(object[key])) {
      return key;
    }
  }
  return void 0;
}
function findIndex(array, predicate) {
  for (let key = 0; key < array.length; key++) {
    if (predicate(array[key])) {
      return key;
    }
  }
  return void 0;
}

// packages/dataviews/node_modules/date-fns/locale/_lib/buildMatchPatternFn.js
function buildMatchPatternFn(args) {
  return (string, options = {}) => {
    const matchResult = string.match(args.matchPattern);
    if (!matchResult) return null;
    const matchedString = matchResult[0];
    const parseResult = string.match(args.parsePattern);
    if (!parseResult) return null;
    let value = args.valueCallback ? args.valueCallback(parseResult[0]) : parseResult[0];
    value = options.valueCallback ? options.valueCallback(value) : value;
    const rest = string.slice(matchedString.length);
    return { value, rest };
  };
}

// packages/dataviews/node_modules/date-fns/locale/en-US/_lib/match.js
var matchOrdinalNumberPattern = /^(\d+)(th|st|nd|rd)?/i;
var parseOrdinalNumberPattern = /\d+/i;
var matchEraPatterns = {
  narrow: /^(b|a)/i,
  abbreviated: /^(b\.?\s?c\.?|b\.?\s?c\.?\s?e\.?|a\.?\s?d\.?|c\.?\s?e\.?)/i,
  wide: /^(before christ|before common era|anno domini|common era)/i
};
var parseEraPatterns = {
  any: [/^b/i, /^(a|c)/i]
};
var matchQuarterPatterns = {
  narrow: /^[1234]/i,
  abbreviated: /^q[1234]/i,
  wide: /^[1234](th|st|nd|rd)? quarter/i
};
var parseQuarterPatterns = {
  any: [/1/i, /2/i, /3/i, /4/i]
};
var matchMonthPatterns = {
  narrow: /^[jfmasond]/i,
  abbreviated: /^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)/i,
  wide: /^(january|february|march|april|may|june|july|august|september|october|november|december)/i
};
var parseMonthPatterns = {
  narrow: [
    /^j/i,
    /^f/i,
    /^m/i,
    /^a/i,
    /^m/i,
    /^j/i,
    /^j/i,
    /^a/i,
    /^s/i,
    /^o/i,
    /^n/i,
    /^d/i
  ],
  any: [
    /^ja/i,
    /^f/i,
    /^mar/i,
    /^ap/i,
    /^may/i,
    /^jun/i,
    /^jul/i,
    /^au/i,
    /^s/i,
    /^o/i,
    /^n/i,
    /^d/i
  ]
};
var matchDayPatterns = {
  narrow: /^[smtwf]/i,
  short: /^(su|mo|tu|we|th|fr|sa)/i,
  abbreviated: /^(sun|mon|tue|wed|thu|fri|sat)/i,
  wide: /^(sunday|monday|tuesday|wednesday|thursday|friday|saturday)/i
};
var parseDayPatterns = {
  narrow: [/^s/i, /^m/i, /^t/i, /^w/i, /^t/i, /^f/i, /^s/i],
  any: [/^su/i, /^m/i, /^tu/i, /^w/i, /^th/i, /^f/i, /^sa/i]
};
var matchDayPeriodPatterns = {
  narrow: /^(a|p|mi|n|(in the|at) (morning|afternoon|evening|night))/i,
  any: /^([ap]\.?\s?m\.?|midnight|noon|(in the|at) (morning|afternoon|evening|night))/i
};
var parseDayPeriodPatterns = {
  any: {
    am: /^a/i,
    pm: /^p/i,
    midnight: /^mi/i,
    noon: /^no/i,
    morning: /morning/i,
    afternoon: /afternoon/i,
    evening: /evening/i,
    night: /night/i
  }
};
var match = {
  ordinalNumber: buildMatchPatternFn({
    matchPattern: matchOrdinalNumberPattern,
    parsePattern: parseOrdinalNumberPattern,
    valueCallback: (value) => parseInt(value, 10)
  }),
  era: buildMatchFn({
    matchPatterns: matchEraPatterns,
    defaultMatchWidth: "wide",
    parsePatterns: parseEraPatterns,
    defaultParseWidth: "any"
  }),
  quarter: buildMatchFn({
    matchPatterns: matchQuarterPatterns,
    defaultMatchWidth: "wide",
    parsePatterns: parseQuarterPatterns,
    defaultParseWidth: "any",
    valueCallback: (index) => index + 1
  }),
  month: buildMatchFn({
    matchPatterns: matchMonthPatterns,
    defaultMatchWidth: "wide",
    parsePatterns: parseMonthPatterns,
    defaultParseWidth: "any"
  }),
  day: buildMatchFn({
    matchPatterns: matchDayPatterns,
    defaultMatchWidth: "wide",
    parsePatterns: parseDayPatterns,
    defaultParseWidth: "any"
  }),
  dayPeriod: buildMatchFn({
    matchPatterns: matchDayPeriodPatterns,
    defaultMatchWidth: "any",
    parsePatterns: parseDayPeriodPatterns,
    defaultParseWidth: "any"
  })
};

// packages/dataviews/node_modules/date-fns/locale/en-US.js
var enUS = {
  code: "en-US",
  formatDistance,
  formatLong,
  formatRelative,
  localize,
  match,
  options: {
    weekStartsOn: 0,
    firstWeekContainsDate: 1
  }
};

// packages/dataviews/node_modules/date-fns/getDayOfYear.js
function getDayOfYear(date, options) {
  const _date = toDate(date, options?.in);
  const diff = differenceInCalendarDays(_date, startOfYear(_date));
  const dayOfYear = diff + 1;
  return dayOfYear;
}

// packages/dataviews/node_modules/date-fns/getISOWeek.js
function getISOWeek(date, options) {
  const _date = toDate(date, options?.in);
  const diff = +startOfISOWeek(_date) - +startOfISOWeekYear(_date);
  return Math.round(diff / millisecondsInWeek) + 1;
}

// packages/dataviews/node_modules/date-fns/getWeekYear.js
function getWeekYear(date, options) {
  const _date = toDate(date, options?.in);
  const year = _date.getFullYear();
  const defaultOptions2 = getDefaultOptions();
  const firstWeekContainsDate = options?.firstWeekContainsDate ?? options?.locale?.options?.firstWeekContainsDate ?? defaultOptions2.firstWeekContainsDate ?? defaultOptions2.locale?.options?.firstWeekContainsDate ?? 1;
  const firstWeekOfNextYear = constructFrom(options?.in || date, 0);
  firstWeekOfNextYear.setFullYear(year + 1, 0, firstWeekContainsDate);
  firstWeekOfNextYear.setHours(0, 0, 0, 0);
  const startOfNextYear = startOfWeek(firstWeekOfNextYear, options);
  const firstWeekOfThisYear = constructFrom(options?.in || date, 0);
  firstWeekOfThisYear.setFullYear(year, 0, firstWeekContainsDate);
  firstWeekOfThisYear.setHours(0, 0, 0, 0);
  const startOfThisYear = startOfWeek(firstWeekOfThisYear, options);
  if (+_date >= +startOfNextYear) {
    return year + 1;
  } else if (+_date >= +startOfThisYear) {
    return year;
  } else {
    return year - 1;
  }
}

// packages/dataviews/node_modules/date-fns/startOfWeekYear.js
function startOfWeekYear(date, options) {
  const defaultOptions2 = getDefaultOptions();
  const firstWeekContainsDate = options?.firstWeekContainsDate ?? options?.locale?.options?.firstWeekContainsDate ?? defaultOptions2.firstWeekContainsDate ?? defaultOptions2.locale?.options?.firstWeekContainsDate ?? 1;
  const year = getWeekYear(date, options);
  const firstWeek = constructFrom(options?.in || date, 0);
  firstWeek.setFullYear(year, 0, firstWeekContainsDate);
  firstWeek.setHours(0, 0, 0, 0);
  const _date = startOfWeek(firstWeek, options);
  return _date;
}

// packages/dataviews/node_modules/date-fns/getWeek.js
function getWeek(date, options) {
  const _date = toDate(date, options?.in);
  const diff = +startOfWeek(_date, options) - +startOfWeekYear(_date, options);
  return Math.round(diff / millisecondsInWeek) + 1;
}

// packages/dataviews/node_modules/date-fns/_lib/addLeadingZeros.js
function addLeadingZeros(number, targetLength) {
  const sign = number < 0 ? "-" : "";
  const output = Math.abs(number).toString().padStart(targetLength, "0");
  return sign + output;
}

// packages/dataviews/node_modules/date-fns/_lib/format/lightFormatters.js
var lightFormatters = {
  // Year
  y(date, token) {
    const signedYear = date.getFullYear();
    const year = signedYear > 0 ? signedYear : 1 - signedYear;
    return addLeadingZeros(token === "yy" ? year % 100 : year, token.length);
  },
  // Month
  M(date, token) {
    const month = date.getMonth();
    return token === "M" ? String(month + 1) : addLeadingZeros(month + 1, 2);
  },
  // Day of the month
  d(date, token) {
    return addLeadingZeros(date.getDate(), token.length);
  },
  // AM or PM
  a(date, token) {
    const dayPeriodEnumValue = date.getHours() / 12 >= 1 ? "pm" : "am";
    switch (token) {
      case "a":
      case "aa":
        return dayPeriodEnumValue.toUpperCase();
      case "aaa":
        return dayPeriodEnumValue;
      case "aaaaa":
        return dayPeriodEnumValue[0];
      case "aaaa":
      default:
        return dayPeriodEnumValue === "am" ? "a.m." : "p.m.";
    }
  },
  // Hour [1-12]
  h(date, token) {
    return addLeadingZeros(date.getHours() % 12 || 12, token.length);
  },
  // Hour [0-23]
  H(date, token) {
    return addLeadingZeros(date.getHours(), token.length);
  },
  // Minute
  m(date, token) {
    return addLeadingZeros(date.getMinutes(), token.length);
  },
  // Second
  s(date, token) {
    return addLeadingZeros(date.getSeconds(), token.length);
  },
  // Fraction of second
  S(date, token) {
    const numberOfDigits = token.length;
    const milliseconds = date.getMilliseconds();
    const fractionalSeconds = Math.trunc(
      milliseconds * Math.pow(10, numberOfDigits - 3)
    );
    return addLeadingZeros(fractionalSeconds, token.length);
  }
};

// packages/dataviews/node_modules/date-fns/_lib/format/formatters.js
var dayPeriodEnum = {
  am: "am",
  pm: "pm",
  midnight: "midnight",
  noon: "noon",
  morning: "morning",
  afternoon: "afternoon",
  evening: "evening",
  night: "night"
};
var formatters = {
  // Era
  G: function(date, token, localize2) {
    const era = date.getFullYear() > 0 ? 1 : 0;
    switch (token) {
      // AD, BC
      case "G":
      case "GG":
      case "GGG":
        return localize2.era(era, { width: "abbreviated" });
      // A, B
      case "GGGGG":
        return localize2.era(era, { width: "narrow" });
      // Anno Domini, Before Christ
      case "GGGG":
      default:
        return localize2.era(era, { width: "wide" });
    }
  },
  // Year
  y: function(date, token, localize2) {
    if (token === "yo") {
      const signedYear = date.getFullYear();
      const year = signedYear > 0 ? signedYear : 1 - signedYear;
      return localize2.ordinalNumber(year, { unit: "year" });
    }
    return lightFormatters.y(date, token);
  },
  // Local week-numbering year
  Y: function(date, token, localize2, options) {
    const signedWeekYear = getWeekYear(date, options);
    const weekYear = signedWeekYear > 0 ? signedWeekYear : 1 - signedWeekYear;
    if (token === "YY") {
      const twoDigitYear = weekYear % 100;
      return addLeadingZeros(twoDigitYear, 2);
    }
    if (token === "Yo") {
      return localize2.ordinalNumber(weekYear, { unit: "year" });
    }
    return addLeadingZeros(weekYear, token.length);
  },
  // ISO week-numbering year
  R: function(date, token) {
    const isoWeekYear = getISOWeekYear(date);
    return addLeadingZeros(isoWeekYear, token.length);
  },
  // Extended year. This is a single number designating the year of this calendar system.
  // The main difference between `y` and `u` localizers are B.C. years:
  // | Year | `y` | `u` |
  // |------|-----|-----|
  // | AC 1 |   1 |   1 |
  // | BC 1 |   1 |   0 |
  // | BC 2 |   2 |  -1 |
  // Also `yy` always returns the last two digits of a year,
  // while `uu` pads single digit years to 2 characters and returns other years unchanged.
  u: function(date, token) {
    const year = date.getFullYear();
    return addLeadingZeros(year, token.length);
  },
  // Quarter
  Q: function(date, token, localize2) {
    const quarter = Math.ceil((date.getMonth() + 1) / 3);
    switch (token) {
      // 1, 2, 3, 4
      case "Q":
        return String(quarter);
      // 01, 02, 03, 04
      case "QQ":
        return addLeadingZeros(quarter, 2);
      // 1st, 2nd, 3rd, 4th
      case "Qo":
        return localize2.ordinalNumber(quarter, { unit: "quarter" });
      // Q1, Q2, Q3, Q4
      case "QQQ":
        return localize2.quarter(quarter, {
          width: "abbreviated",
          context: "formatting"
        });
      // 1, 2, 3, 4 (narrow quarter; could be not numerical)
      case "QQQQQ":
        return localize2.quarter(quarter, {
          width: "narrow",
          context: "formatting"
        });
      // 1st quarter, 2nd quarter, ...
      case "QQQQ":
      default:
        return localize2.quarter(quarter, {
          width: "wide",
          context: "formatting"
        });
    }
  },
  // Stand-alone quarter
  q: function(date, token, localize2) {
    const quarter = Math.ceil((date.getMonth() + 1) / 3);
    switch (token) {
      // 1, 2, 3, 4
      case "q":
        return String(quarter);
      // 01, 02, 03, 04
      case "qq":
        return addLeadingZeros(quarter, 2);
      // 1st, 2nd, 3rd, 4th
      case "qo":
        return localize2.ordinalNumber(quarter, { unit: "quarter" });
      // Q1, Q2, Q3, Q4
      case "qqq":
        return localize2.quarter(quarter, {
          width: "abbreviated",
          context: "standalone"
        });
      // 1, 2, 3, 4 (narrow quarter; could be not numerical)
      case "qqqqq":
        return localize2.quarter(quarter, {
          width: "narrow",
          context: "standalone"
        });
      // 1st quarter, 2nd quarter, ...
      case "qqqq":
      default:
        return localize2.quarter(quarter, {
          width: "wide",
          context: "standalone"
        });
    }
  },
  // Month
  M: function(date, token, localize2) {
    const month = date.getMonth();
    switch (token) {
      case "M":
      case "MM":
        return lightFormatters.M(date, token);
      // 1st, 2nd, ..., 12th
      case "Mo":
        return localize2.ordinalNumber(month + 1, { unit: "month" });
      // Jan, Feb, ..., Dec
      case "MMM":
        return localize2.month(month, {
          width: "abbreviated",
          context: "formatting"
        });
      // J, F, ..., D
      case "MMMMM":
        return localize2.month(month, {
          width: "narrow",
          context: "formatting"
        });
      // January, February, ..., December
      case "MMMM":
      default:
        return localize2.month(month, { width: "wide", context: "formatting" });
    }
  },
  // Stand-alone month
  L: function(date, token, localize2) {
    const month = date.getMonth();
    switch (token) {
      // 1, 2, ..., 12
      case "L":
        return String(month + 1);
      // 01, 02, ..., 12
      case "LL":
        return addLeadingZeros(month + 1, 2);
      // 1st, 2nd, ..., 12th
      case "Lo":
        return localize2.ordinalNumber(month + 1, { unit: "month" });
      // Jan, Feb, ..., Dec
      case "LLL":
        return localize2.month(month, {
          width: "abbreviated",
          context: "standalone"
        });
      // J, F, ..., D
      case "LLLLL":
        return localize2.month(month, {
          width: "narrow",
          context: "standalone"
        });
      // January, February, ..., December
      case "LLLL":
      default:
        return localize2.month(month, { width: "wide", context: "standalone" });
    }
  },
  // Local week of year
  w: function(date, token, localize2, options) {
    const week = getWeek(date, options);
    if (token === "wo") {
      return localize2.ordinalNumber(week, { unit: "week" });
    }
    return addLeadingZeros(week, token.length);
  },
  // ISO week of year
  I: function(date, token, localize2) {
    const isoWeek = getISOWeek(date);
    if (token === "Io") {
      return localize2.ordinalNumber(isoWeek, { unit: "week" });
    }
    return addLeadingZeros(isoWeek, token.length);
  },
  // Day of the month
  d: function(date, token, localize2) {
    if (token === "do") {
      return localize2.ordinalNumber(date.getDate(), { unit: "date" });
    }
    return lightFormatters.d(date, token);
  },
  // Day of year
  D: function(date, token, localize2) {
    const dayOfYear = getDayOfYear(date);
    if (token === "Do") {
      return localize2.ordinalNumber(dayOfYear, { unit: "dayOfYear" });
    }
    return addLeadingZeros(dayOfYear, token.length);
  },
  // Day of week
  E: function(date, token, localize2) {
    const dayOfWeek = date.getDay();
    switch (token) {
      // Tue
      case "E":
      case "EE":
      case "EEE":
        return localize2.day(dayOfWeek, {
          width: "abbreviated",
          context: "formatting"
        });
      // T
      case "EEEEE":
        return localize2.day(dayOfWeek, {
          width: "narrow",
          context: "formatting"
        });
      // Tu
      case "EEEEEE":
        return localize2.day(dayOfWeek, {
          width: "short",
          context: "formatting"
        });
      // Tuesday
      case "EEEE":
      default:
        return localize2.day(dayOfWeek, {
          width: "wide",
          context: "formatting"
        });
    }
  },
  // Local day of week
  e: function(date, token, localize2, options) {
    const dayOfWeek = date.getDay();
    const localDayOfWeek = (dayOfWeek - options.weekStartsOn + 8) % 7 || 7;
    switch (token) {
      // Numerical value (Nth day of week with current locale or weekStartsOn)
      case "e":
        return String(localDayOfWeek);
      // Padded numerical value
      case "ee":
        return addLeadingZeros(localDayOfWeek, 2);
      // 1st, 2nd, ..., 7th
      case "eo":
        return localize2.ordinalNumber(localDayOfWeek, { unit: "day" });
      case "eee":
        return localize2.day(dayOfWeek, {
          width: "abbreviated",
          context: "formatting"
        });
      // T
      case "eeeee":
        return localize2.day(dayOfWeek, {
          width: "narrow",
          context: "formatting"
        });
      // Tu
      case "eeeeee":
        return localize2.day(dayOfWeek, {
          width: "short",
          context: "formatting"
        });
      // Tuesday
      case "eeee":
      default:
        return localize2.day(dayOfWeek, {
          width: "wide",
          context: "formatting"
        });
    }
  },
  // Stand-alone local day of week
  c: function(date, token, localize2, options) {
    const dayOfWeek = date.getDay();
    const localDayOfWeek = (dayOfWeek - options.weekStartsOn + 8) % 7 || 7;
    switch (token) {
      // Numerical value (same as in `e`)
      case "c":
        return String(localDayOfWeek);
      // Padded numerical value
      case "cc":
        return addLeadingZeros(localDayOfWeek, token.length);
      // 1st, 2nd, ..., 7th
      case "co":
        return localize2.ordinalNumber(localDayOfWeek, { unit: "day" });
      case "ccc":
        return localize2.day(dayOfWeek, {
          width: "abbreviated",
          context: "standalone"
        });
      // T
      case "ccccc":
        return localize2.day(dayOfWeek, {
          width: "narrow",
          context: "standalone"
        });
      // Tu
      case "cccccc":
        return localize2.day(dayOfWeek, {
          width: "short",
          context: "standalone"
        });
      // Tuesday
      case "cccc":
      default:
        return localize2.day(dayOfWeek, {
          width: "wide",
          context: "standalone"
        });
    }
  },
  // ISO day of week
  i: function(date, token, localize2) {
    const dayOfWeek = date.getDay();
    const isoDayOfWeek = dayOfWeek === 0 ? 7 : dayOfWeek;
    switch (token) {
      // 2
      case "i":
        return String(isoDayOfWeek);
      // 02
      case "ii":
        return addLeadingZeros(isoDayOfWeek, token.length);
      // 2nd
      case "io":
        return localize2.ordinalNumber(isoDayOfWeek, { unit: "day" });
      // Tue
      case "iii":
        return localize2.day(dayOfWeek, {
          width: "abbreviated",
          context: "formatting"
        });
      // T
      case "iiiii":
        return localize2.day(dayOfWeek, {
          width: "narrow",
          context: "formatting"
        });
      // Tu
      case "iiiiii":
        return localize2.day(dayOfWeek, {
          width: "short",
          context: "formatting"
        });
      // Tuesday
      case "iiii":
      default:
        return localize2.day(dayOfWeek, {
          width: "wide",
          context: "formatting"
        });
    }
  },
  // AM or PM
  a: function(date, token, localize2) {
    const hours = date.getHours();
    const dayPeriodEnumValue = hours / 12 >= 1 ? "pm" : "am";
    switch (token) {
      case "a":
      case "aa":
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "abbreviated",
          context: "formatting"
        });
      case "aaa":
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "abbreviated",
          context: "formatting"
        }).toLowerCase();
      case "aaaaa":
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "narrow",
          context: "formatting"
        });
      case "aaaa":
      default:
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "wide",
          context: "formatting"
        });
    }
  },
  // AM, PM, midnight, noon
  b: function(date, token, localize2) {
    const hours = date.getHours();
    let dayPeriodEnumValue;
    if (hours === 12) {
      dayPeriodEnumValue = dayPeriodEnum.noon;
    } else if (hours === 0) {
      dayPeriodEnumValue = dayPeriodEnum.midnight;
    } else {
      dayPeriodEnumValue = hours / 12 >= 1 ? "pm" : "am";
    }
    switch (token) {
      case "b":
      case "bb":
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "abbreviated",
          context: "formatting"
        });
      case "bbb":
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "abbreviated",
          context: "formatting"
        }).toLowerCase();
      case "bbbbb":
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "narrow",
          context: "formatting"
        });
      case "bbbb":
      default:
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "wide",
          context: "formatting"
        });
    }
  },
  // in the morning, in the afternoon, in the evening, at night
  B: function(date, token, localize2) {
    const hours = date.getHours();
    let dayPeriodEnumValue;
    if (hours >= 17) {
      dayPeriodEnumValue = dayPeriodEnum.evening;
    } else if (hours >= 12) {
      dayPeriodEnumValue = dayPeriodEnum.afternoon;
    } else if (hours >= 4) {
      dayPeriodEnumValue = dayPeriodEnum.morning;
    } else {
      dayPeriodEnumValue = dayPeriodEnum.night;
    }
    switch (token) {
      case "B":
      case "BB":
      case "BBB":
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "abbreviated",
          context: "formatting"
        });
      case "BBBBB":
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "narrow",
          context: "formatting"
        });
      case "BBBB":
      default:
        return localize2.dayPeriod(dayPeriodEnumValue, {
          width: "wide",
          context: "formatting"
        });
    }
  },
  // Hour [1-12]
  h: function(date, token, localize2) {
    if (token === "ho") {
      let hours = date.getHours() % 12;
      if (hours === 0) hours = 12;
      return localize2.ordinalNumber(hours, { unit: "hour" });
    }
    return lightFormatters.h(date, token);
  },
  // Hour [0-23]
  H: function(date, token, localize2) {
    if (token === "Ho") {
      return localize2.ordinalNumber(date.getHours(), { unit: "hour" });
    }
    return lightFormatters.H(date, token);
  },
  // Hour [0-11]
  K: function(date, token, localize2) {
    const hours = date.getHours() % 12;
    if (token === "Ko") {
      return localize2.ordinalNumber(hours, { unit: "hour" });
    }
    return addLeadingZeros(hours, token.length);
  },
  // Hour [1-24]
  k: function(date, token, localize2) {
    let hours = date.getHours();
    if (hours === 0) hours = 24;
    if (token === "ko") {
      return localize2.ordinalNumber(hours, { unit: "hour" });
    }
    return addLeadingZeros(hours, token.length);
  },
  // Minute
  m: function(date, token, localize2) {
    if (token === "mo") {
      return localize2.ordinalNumber(date.getMinutes(), { unit: "minute" });
    }
    return lightFormatters.m(date, token);
  },
  // Second
  s: function(date, token, localize2) {
    if (token === "so") {
      return localize2.ordinalNumber(date.getSeconds(), { unit: "second" });
    }
    return lightFormatters.s(date, token);
  },
  // Fraction of second
  S: function(date, token) {
    return lightFormatters.S(date, token);
  },
  // Timezone (ISO-8601. If offset is 0, output is always `'Z'`)
  X: function(date, token, _localize) {
    const timezoneOffset = date.getTimezoneOffset();
    if (timezoneOffset === 0) {
      return "Z";
    }
    switch (token) {
      // Hours and optional minutes
      case "X":
        return formatTimezoneWithOptionalMinutes(timezoneOffset);
      // Hours, minutes and optional seconds without `:` delimiter
      // Note: neither ISO-8601 nor JavaScript supports seconds in timezone offsets
      // so this token always has the same output as `XX`
      case "XXXX":
      case "XX":
        return formatTimezone(timezoneOffset);
      // Hours, minutes and optional seconds with `:` delimiter
      // Note: neither ISO-8601 nor JavaScript supports seconds in timezone offsets
      // so this token always has the same output as `XXX`
      case "XXXXX":
      case "XXX":
      // Hours and minutes with `:` delimiter
      default:
        return formatTimezone(timezoneOffset, ":");
    }
  },
  // Timezone (ISO-8601. If offset is 0, output is `'+00:00'` or equivalent)
  x: function(date, token, _localize) {
    const timezoneOffset = date.getTimezoneOffset();
    switch (token) {
      // Hours and optional minutes
      case "x":
        return formatTimezoneWithOptionalMinutes(timezoneOffset);
      // Hours, minutes and optional seconds without `:` delimiter
      // Note: neither ISO-8601 nor JavaScript supports seconds in timezone offsets
      // so this token always has the same output as `xx`
      case "xxxx":
      case "xx":
        return formatTimezone(timezoneOffset);
      // Hours, minutes and optional seconds with `:` delimiter
      // Note: neither ISO-8601 nor JavaScript supports seconds in timezone offsets
      // so this token always has the same output as `xxx`
      case "xxxxx":
      case "xxx":
      // Hours and minutes with `:` delimiter
      default:
        return formatTimezone(timezoneOffset, ":");
    }
  },
  // Timezone (GMT)
  O: function(date, token, _localize) {
    const timezoneOffset = date.getTimezoneOffset();
    switch (token) {
      // Short
      case "O":
      case "OO":
      case "OOO":
        return "GMT" + formatTimezoneShort(timezoneOffset, ":");
      // Long
      case "OOOO":
      default:
        return "GMT" + formatTimezone(timezoneOffset, ":");
    }
  },
  // Timezone (specific non-location)
  z: function(date, token, _localize) {
    const timezoneOffset = date.getTimezoneOffset();
    switch (token) {
      // Short
      case "z":
      case "zz":
      case "zzz":
        return "GMT" + formatTimezoneShort(timezoneOffset, ":");
      // Long
      case "zzzz":
      default:
        return "GMT" + formatTimezone(timezoneOffset, ":");
    }
  },
  // Seconds timestamp
  t: function(date, token, _localize) {
    const timestamp = Math.trunc(+date / 1e3);
    return addLeadingZeros(timestamp, token.length);
  },
  // Milliseconds timestamp
  T: function(date, token, _localize) {
    return addLeadingZeros(+date, token.length);
  }
};
function formatTimezoneShort(offset, delimiter = "") {
  const sign = offset > 0 ? "-" : "+";
  const absOffset = Math.abs(offset);
  const hours = Math.trunc(absOffset / 60);
  const minutes = absOffset % 60;
  if (minutes === 0) {
    return sign + String(hours);
  }
  return sign + String(hours) + delimiter + addLeadingZeros(minutes, 2);
}
function formatTimezoneWithOptionalMinutes(offset, delimiter) {
  if (offset % 60 === 0) {
    const sign = offset > 0 ? "-" : "+";
    return sign + addLeadingZeros(Math.abs(offset) / 60, 2);
  }
  return formatTimezone(offset, delimiter);
}
function formatTimezone(offset, delimiter = "") {
  const sign = offset > 0 ? "-" : "+";
  const absOffset = Math.abs(offset);
  const hours = addLeadingZeros(Math.trunc(absOffset / 60), 2);
  const minutes = addLeadingZeros(absOffset % 60, 2);
  return sign + hours + delimiter + minutes;
}

// packages/dataviews/node_modules/date-fns/_lib/format/longFormatters.js
var dateLongFormatter = (pattern, formatLong2) => {
  switch (pattern) {
    case "P":
      return formatLong2.date({ width: "short" });
    case "PP":
      return formatLong2.date({ width: "medium" });
    case "PPP":
      return formatLong2.date({ width: "long" });
    case "PPPP":
    default:
      return formatLong2.date({ width: "full" });
  }
};
var timeLongFormatter = (pattern, formatLong2) => {
  switch (pattern) {
    case "p":
      return formatLong2.time({ width: "short" });
    case "pp":
      return formatLong2.time({ width: "medium" });
    case "ppp":
      return formatLong2.time({ width: "long" });
    case "pppp":
    default:
      return formatLong2.time({ width: "full" });
  }
};
var dateTimeLongFormatter = (pattern, formatLong2) => {
  const matchResult = pattern.match(/(P+)(p+)?/) || [];
  const datePattern = matchResult[1];
  const timePattern = matchResult[2];
  if (!timePattern) {
    return dateLongFormatter(pattern, formatLong2);
  }
  let dateTimeFormat;
  switch (datePattern) {
    case "P":
      dateTimeFormat = formatLong2.dateTime({ width: "short" });
      break;
    case "PP":
      dateTimeFormat = formatLong2.dateTime({ width: "medium" });
      break;
    case "PPP":
      dateTimeFormat = formatLong2.dateTime({ width: "long" });
      break;
    case "PPPP":
    default:
      dateTimeFormat = formatLong2.dateTime({ width: "full" });
      break;
  }
  return dateTimeFormat.replace("{{date}}", dateLongFormatter(datePattern, formatLong2)).replace("{{time}}", timeLongFormatter(timePattern, formatLong2));
};
var longFormatters = {
  p: timeLongFormatter,
  P: dateTimeLongFormatter
};

// packages/dataviews/node_modules/date-fns/_lib/protectedTokens.js
var dayOfYearTokenRE = /^D+$/;
var weekYearTokenRE = /^Y+$/;
var throwTokens = ["D", "DD", "YY", "YYYY"];
function isProtectedDayOfYearToken(token) {
  return dayOfYearTokenRE.test(token);
}
function isProtectedWeekYearToken(token) {
  return weekYearTokenRE.test(token);
}
function warnOrThrowProtectedError(token, format6, input) {
  const _message = message(token, format6, input);
  console.warn(_message);
  if (throwTokens.includes(token)) throw new RangeError(_message);
}
function message(token, format6, input) {
  const subject = token[0] === "Y" ? "years" : "days of the month";
  return `Use \`${token.toLowerCase()}\` instead of \`${token}\` (in \`${format6}\`) for formatting ${subject} to the input \`${input}\`; see: https://github.com/date-fns/date-fns/blob/master/docs/unicodeTokens.md`;
}

// packages/dataviews/node_modules/date-fns/format.js
var formattingTokensRegExp = /[yYQqMLwIdDecihHKkms]o|(\w)\1*|''|'(''|[^'])+('|$)|./g;
var longFormattingTokensRegExp = /P+p+|P+|p+|''|'(''|[^'])+('|$)|./g;
var escapedStringRegExp = /^'([^]*?)'?$/;
var doubleQuoteRegExp = /''/g;
var unescapedLatinCharacterRegExp = /[a-zA-Z]/;
function format(date, formatStr, options) {
  const defaultOptions2 = getDefaultOptions();
  const locale = options?.locale ?? defaultOptions2.locale ?? enUS;
  const firstWeekContainsDate = options?.firstWeekContainsDate ?? options?.locale?.options?.firstWeekContainsDate ?? defaultOptions2.firstWeekContainsDate ?? defaultOptions2.locale?.options?.firstWeekContainsDate ?? 1;
  const weekStartsOn = options?.weekStartsOn ?? options?.locale?.options?.weekStartsOn ?? defaultOptions2.weekStartsOn ?? defaultOptions2.locale?.options?.weekStartsOn ?? 0;
  const originalDate = toDate(date, options?.in);
  if (!isValid(originalDate)) {
    throw new RangeError("Invalid time value");
  }
  let parts = formatStr.match(longFormattingTokensRegExp).map((substring) => {
    const firstCharacter = substring[0];
    if (firstCharacter === "p" || firstCharacter === "P") {
      const longFormatter = longFormatters[firstCharacter];
      return longFormatter(substring, locale.formatLong);
    }
    return substring;
  }).join("").match(formattingTokensRegExp).map((substring) => {
    if (substring === "''") {
      return { isToken: false, value: "'" };
    }
    const firstCharacter = substring[0];
    if (firstCharacter === "'") {
      return { isToken: false, value: cleanEscapedString(substring) };
    }
    if (formatters[firstCharacter]) {
      return { isToken: true, value: substring };
    }
    if (firstCharacter.match(unescapedLatinCharacterRegExp)) {
      throw new RangeError(
        "Format string contains an unescaped latin alphabet character `" + firstCharacter + "`"
      );
    }
    return { isToken: false, value: substring };
  });
  if (locale.localize.preprocessor) {
    parts = locale.localize.preprocessor(originalDate, parts);
  }
  const formatterOptions = {
    firstWeekContainsDate,
    weekStartsOn,
    locale
  };
  return parts.map((part) => {
    if (!part.isToken) return part.value;
    const token = part.value;
    if (!options?.useAdditionalWeekYearTokens && isProtectedWeekYearToken(token) || !options?.useAdditionalDayOfYearTokens && isProtectedDayOfYearToken(token)) {
      warnOrThrowProtectedError(token, formatStr, String(date));
    }
    const formatter = formatters[token[0]];
    return formatter(originalDate, token, locale.localize, formatterOptions);
  }).join("");
}
function cleanEscapedString(input) {
  const matched = input.match(escapedStringRegExp);
  if (!matched) {
    return input;
  }
  return matched[1].replace(doubleQuoteRegExp, "'");
}

// packages/dataviews/node_modules/date-fns/subDays.js
function subDays(date, amount, options) {
  return addDays(date, -amount, options);
}

// packages/dataviews/node_modules/date-fns/subMonths.js
function subMonths(date, amount, options) {
  return addMonths(date, -amount, options);
}

// packages/dataviews/node_modules/date-fns/subWeeks.js
function subWeeks(date, amount, options) {
  return addWeeks(date, -amount, options);
}

// packages/dataviews/node_modules/date-fns/subYears.js
function subYears(date, amount, options) {
  return addYears(date, -amount, options);
}

// packages/dataviews/build-module/utils/operators.mjs
var import_i18n4 = __toESM(require_i18n(), 1);
var import_element21 = __toESM(require_element(), 1);
var import_date = __toESM(require_date(), 1);
var import_jsx_runtime26 = __toESM(require_jsx_runtime(), 1);
var filterTextWrappers = {
  Name: /* @__PURE__ */ (0, import_jsx_runtime26.jsx)("span", { className: "dataviews-filters__summary-filter-text-name" }),
  Value: /* @__PURE__ */ (0, import_jsx_runtime26.jsx)("span", { className: "dataviews-filters__summary-filter-text-value" })
};
function getRelativeDate(value, unit) {
  switch (unit) {
    case "days":
      return subDays(/* @__PURE__ */ new Date(), value);
    case "weeks":
      return subWeeks(/* @__PURE__ */ new Date(), value);
    case "months":
      return subMonths(/* @__PURE__ */ new Date(), value);
    case "years":
      return subYears(/* @__PURE__ */ new Date(), value);
    default:
      return /* @__PURE__ */ new Date();
  }
}
var isNoneOperatorDefinition = {
  /* translators: DataViews operator name */
  label: (0, import_i18n4.__)("Is none of"),
  filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
    (0, import_i18n4.sprintf)(
      /* translators: 1: Filter name (e.g. "Author"). 2: Filter value (e.g. "Admin"): "Author is none of: Admin, Editor". */
      (0, import_i18n4.__)("<Name>%1$s is none of: </Name><Value>%2$s</Value>"),
      filter.name,
      activeElements.map((element) => element.label).join(", ")
    ),
    filterTextWrappers
  ),
  filter: ((item, field, filterValue) => {
    if (!filterValue?.length) {
      return true;
    }
    const fieldValue = field.getValue({ item });
    if (Array.isArray(fieldValue)) {
      return !filterValue.some(
        (fv) => fieldValue.includes(fv)
      );
    } else if (typeof fieldValue === "string") {
      return !filterValue.includes(fieldValue);
    }
    return false;
  }),
  selection: "multi"
};
var OPERATORS = [
  {
    name: OPERATOR_IS_ANY,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Includes"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Author"). 2: Filter value (e.g. "Admin"): "Author is any: Admin, Editor". */
        (0, import_i18n4.__)("<Name>%1$s includes: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements.map((element) => element.label).join(", ")
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (!filterValue?.length) {
        return true;
      }
      const fieldValue = field.getValue({ item });
      if (Array.isArray(fieldValue)) {
        return filterValue.some(
          (fv) => fieldValue.includes(fv)
        );
      } else if (typeof fieldValue === "string") {
        return filterValue.includes(fieldValue);
      }
      return false;
    },
    selection: "multi"
  },
  {
    name: OPERATOR_IS_NONE,
    ...isNoneOperatorDefinition
  },
  {
    name: OPERATOR_IS_ALL,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Includes all"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Author"). 2: Filter value (e.g. "Admin"): "Author includes all: Admin, Editor". */
        (0, import_i18n4.__)("<Name>%1$s includes all: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements.map((element) => element.label).join(", ")
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (!filterValue?.length) {
        return true;
      }
      return filterValue.every((value) => {
        return field.getValue({ item })?.includes(value);
      });
    },
    selection: "multi"
  },
  {
    name: OPERATOR_IS_NOT_ALL,
    ...isNoneOperatorDefinition
  },
  {
    name: OPERATOR_BETWEEN,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Between (inc)"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Item count"). 2: Filter value min. 3: Filter value max. e.g.: "Item count between (inc): 10 and 180". */
        (0, import_i18n4.__)(
          "<Name>%1$s between (inc): </Name><Value>%2$s and %3$s</Value>"
        ),
        filter.name,
        activeElements[0].label[0],
        activeElements[0].label[1]
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (!Array.isArray(filterValue) || filterValue.length !== 2 || filterValue[0] === void 0 || filterValue[1] === void 0) {
        return true;
      }
      const fieldValue = field.getValue({ item });
      if (typeof fieldValue === "number" || fieldValue instanceof Date || typeof fieldValue === "string") {
        return fieldValue >= filterValue[0] && fieldValue <= filterValue[1];
      }
      return false;
    },
    selection: "custom"
  },
  {
    name: OPERATOR_IN_THE_PAST,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("In the past"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Date"). 2: Filter value (e.g. "7 days"): "Date is in the past: 7 days". */
        (0, import_i18n4.__)(
          "<Name>%1$s is in the past: </Name><Value>%2$s</Value>"
        ),
        filter.name,
        `${activeElements[0].value.value} ${activeElements[0].value.unit}`
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue?.value === void 0 || filterValue?.unit === void 0) {
        return true;
      }
      const targetDate = getRelativeDate(
        filterValue.value,
        filterValue.unit
      );
      const fieldValue = (0, import_date.getDate)(field.getValue({ item }));
      return fieldValue >= targetDate && fieldValue <= /* @__PURE__ */ new Date();
    },
    selection: "custom"
  },
  {
    name: OPERATOR_OVER,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Over"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Date"). 2: Filter value (e.g. "7 days"): "Date is over: 7 days". */
        (0, import_i18n4.__)("<Name>%1$s is over: </Name><Value>%2$s</Value>"),
        filter.name,
        `${activeElements[0].value.value} ${activeElements[0].value.unit}`
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue?.value === void 0 || filterValue?.unit === void 0) {
        return true;
      }
      const targetDate = getRelativeDate(
        filterValue.value,
        filterValue.unit
      );
      const fieldValue = (0, import_date.getDate)(field.getValue({ item }));
      return fieldValue < targetDate;
    },
    selection: "custom"
  },
  {
    name: OPERATOR_IS,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Is"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Author"). 2: Filter value (e.g. "Admin"): "Author is: Admin". */
        (0, import_i18n4.__)("<Name>%1$s is: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      return filterValue === field.getValue({ item }) || filterValue === void 0;
    },
    selection: "single"
  },
  {
    name: OPERATOR_IS_NOT,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Is not"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Author"). 2: Filter value (e.g. "Admin"): "Author is not: Admin". */
        (0, import_i18n4.__)("<Name>%1$s is not: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      return filterValue !== field.getValue({ item });
    },
    selection: "single"
  },
  {
    name: OPERATOR_LESS_THAN,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Less than"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Count"). 2: Filter value (e.g. "10"): "Count is less than: 10". */
        (0, import_i18n4.__)("<Name>%1$s is less than: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const fieldValue = field.getValue({ item });
      return fieldValue < filterValue;
    },
    selection: "single"
  },
  {
    name: OPERATOR_GREATER_THAN,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Greater than"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Count"). 2: Filter value (e.g. "10"): "Count is greater than: 10". */
        (0, import_i18n4.__)(
          "<Name>%1$s is greater than: </Name><Value>%2$s</Value>"
        ),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const fieldValue = field.getValue({ item });
      return fieldValue > filterValue;
    },
    selection: "single"
  },
  {
    name: OPERATOR_LESS_THAN_OR_EQUAL,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Less than or equal"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Count"). 2: Filter value (e.g. "10"): "Count is less than or equal to: 10". */
        (0, import_i18n4.__)(
          "<Name>%1$s is less than or equal to: </Name><Value>%2$s</Value>"
        ),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const fieldValue = field.getValue({ item });
      return fieldValue <= filterValue;
    },
    selection: "single"
  },
  {
    name: OPERATOR_GREATER_THAN_OR_EQUAL,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Greater than or equal"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Count"). 2: Filter value (e.g. "10"): "Count is greater than or equal to: 10". */
        (0, import_i18n4.__)(
          "<Name>%1$s is greater than or equal to: </Name><Value>%2$s</Value>"
        ),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const fieldValue = field.getValue({ item });
      return fieldValue >= filterValue;
    },
    selection: "single"
  },
  {
    name: OPERATOR_BEFORE,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Before"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Date"). 2: Filter value (e.g. "2024-01-01"): "Date is before: 2024-01-01". */
        (0, import_i18n4.__)("<Name>%1$s is before: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const filterDate = (0, import_date.getDate)(filterValue);
      const fieldDate = (0, import_date.getDate)(field.getValue({ item }));
      return fieldDate < filterDate;
    },
    selection: "single"
  },
  {
    name: OPERATOR_AFTER,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("After"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Date"). 2: Filter value (e.g. "2024-01-01"): "Date is after: 2024-01-01". */
        (0, import_i18n4.__)("<Name>%1$s is after: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const filterDate = (0, import_date.getDate)(filterValue);
      const fieldDate = (0, import_date.getDate)(field.getValue({ item }));
      return fieldDate > filterDate;
    },
    selection: "single"
  },
  {
    name: OPERATOR_BEFORE_INC,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Before (inc)"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Date"). 2: Filter value (e.g. "2024-01-01"): "Date is on or before: 2024-01-01". */
        (0, import_i18n4.__)(
          "<Name>%1$s is on or before: </Name><Value>%2$s</Value>"
        ),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const filterDate = (0, import_date.getDate)(filterValue);
      const fieldDate = (0, import_date.getDate)(field.getValue({ item }));
      return fieldDate <= filterDate;
    },
    selection: "single"
  },
  {
    name: OPERATOR_AFTER_INC,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("After (inc)"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Date"). 2: Filter value (e.g. "2024-01-01"): "Date is on or after: 2024-01-01". */
        (0, import_i18n4.__)(
          "<Name>%1$s is on or after: </Name><Value>%2$s</Value>"
        ),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const filterDate = (0, import_date.getDate)(filterValue);
      const fieldDate = (0, import_date.getDate)(field.getValue({ item }));
      return fieldDate >= filterDate;
    },
    selection: "single"
  },
  {
    name: OPERATOR_CONTAINS,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Contains"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Title"). 2: Filter value (e.g. "Hello"): "Title contains: Hello". */
        (0, import_i18n4.__)("<Name>%1$s contains: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const fieldValue = field.getValue({ item });
      return typeof fieldValue === "string" && filterValue && fieldValue.toLowerCase().includes(String(filterValue).toLowerCase());
    },
    selection: "single"
  },
  {
    name: OPERATOR_NOT_CONTAINS,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Doesn't contain"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Title"). 2: Filter value (e.g. "Hello"): "Title doesn't contain: Hello". */
        (0, import_i18n4.__)(
          "<Name>%1$s doesn't contain: </Name><Value>%2$s</Value>"
        ),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const fieldValue = field.getValue({ item });
      return typeof fieldValue === "string" && filterValue && !fieldValue.toLowerCase().includes(String(filterValue).toLowerCase());
    },
    selection: "single"
  },
  {
    name: OPERATOR_STARTS_WITH,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Starts with"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Title"). 2: Filter value (e.g. "Hello"): "Title starts with: Hello". */
        (0, import_i18n4.__)("<Name>%1$s starts with: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const fieldValue = field.getValue({ item });
      return typeof fieldValue === "string" && filterValue && fieldValue.toLowerCase().startsWith(String(filterValue).toLowerCase());
    },
    selection: "single"
  },
  {
    name: OPERATOR_ON,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("On"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Date"). 2: Filter value (e.g. "2024-01-01"): "Date is: 2024-01-01". */
        (0, import_i18n4.__)("<Name>%1$s is: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const filterDate = (0, import_date.getDate)(filterValue);
      const fieldDate = (0, import_date.getDate)(field.getValue({ item }));
      return filterDate.getTime() === fieldDate.getTime();
    },
    selection: "single"
  },
  {
    name: OPERATOR_NOT_ON,
    /* translators: DataViews operator name */
    label: (0, import_i18n4.__)("Not on"),
    filterText: (filter, activeElements) => (0, import_element21.createInterpolateElement)(
      (0, import_i18n4.sprintf)(
        /* translators: 1: Filter name (e.g. "Date"). 2: Filter value (e.g. "2024-01-01"): "Date is not: 2024-01-01". */
        (0, import_i18n4.__)("<Name>%1$s is not: </Name><Value>%2$s</Value>"),
        filter.name,
        activeElements[0].label
      ),
      filterTextWrappers
    ),
    filter(item, field, filterValue) {
      if (filterValue === void 0) {
        return true;
      }
      const filterDate = (0, import_date.getDate)(filterValue);
      const fieldDate = (0, import_date.getDate)(field.getValue({ item }));
      return filterDate.getTime() !== fieldDate.getTime();
    },
    selection: "single"
  }
];
var getOperatorByName = (name) => OPERATORS.find((op) => op.name === name);
var getAllOperatorNames = () => OPERATORS.map((op) => op.name);

// packages/dataviews/build-module/components/dataform-controls/checkbox.mjs
var import_components2 = __toESM(require_components(), 1);
var import_element22 = __toESM(require_element(), 1);

// packages/dataviews/build-module/components/dataform-controls/utils/get-custom-validity.mjs
function getCustomValidity(isValid2, validity) {
  let customValidity;
  if (isValid2?.required && validity?.required) {
    customValidity = validity?.required?.message ? validity.required : void 0;
  } else if (isValid2?.pattern && validity?.pattern) {
    customValidity = validity.pattern;
  } else if (isValid2?.min && validity?.min) {
    customValidity = validity.min;
  } else if (isValid2?.max && validity?.max) {
    customValidity = validity.max;
  } else if (isValid2?.minLength && validity?.minLength) {
    customValidity = validity.minLength;
  } else if (isValid2?.maxLength && validity?.maxLength) {
    customValidity = validity.maxLength;
  } else if (isValid2?.elements && validity?.elements) {
    customValidity = validity.elements;
  } else if (validity?.custom) {
    customValidity = validity.custom;
  }
  return customValidity;
}

// packages/dataviews/build-module/components/dataform-controls/checkbox.mjs
var import_jsx_runtime27 = __toESM(require_jsx_runtime(), 1);
var { ValidatedCheckboxControl } = unlock(import_components2.privateApis);
function Checkbox({
  field,
  onChange,
  data,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const { getValue, setValue, label, description, isValid: isValid2 } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  const onChangeControl = (0, import_element22.useCallback)(() => {
    onChange(
      setValue({ item: data, value: !getValue({ item: data }) })
    );
  }, [data, getValue, onChange, setValue]);
  return /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
    ValidatedCheckboxControl,
    {
      required: !!field.isValid?.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      hidden: hideLabelFromVision,
      label,
      help: description,
      checked: getValue({ item: data }),
      onChange: onChangeControl,
      disabled: disabled2
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/combobox.mjs
var import_components3 = __toESM(require_components(), 1);
var import_element23 = __toESM(require_element(), 1);
var import_jsx_runtime28 = __toESM(require_jsx_runtime(), 1);
var { ValidatedComboboxControl } = unlock(import_components3.privateApis);
function Combobox({
  data,
  field,
  onChange,
  hideLabelFromVision,
  validity
}) {
  const { label, description, placeholder, getValue, setValue, isValid: isValid2 } = field;
  const value = getValue({ item: data }) ?? "";
  const onChangeControl = (0, import_element23.useCallback)(
    (newValue) => onChange(setValue({ item: data, value: newValue ?? "" })),
    [data, onChange, setValue]
  );
  const { elements, isLoading } = useElements({
    elements: field.elements,
    getElements: field.getElements
  });
  if (isLoading) {
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(import_components3.Spinner, {});
  }
  return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
    ValidatedComboboxControl,
    {
      required: !!field.isValid?.required,
      customValidity: getCustomValidity(isValid2, validity),
      label,
      value,
      help: description,
      placeholder,
      options: elements,
      onChange: onChangeControl,
      hideLabelFromVision,
      allowReset: true,
      expandOnFocus: true
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/datetime.mjs
var import_components5 = __toESM(require_components(), 1);
var import_element26 = __toESM(require_element(), 1);
var import_i18n6 = __toESM(require_i18n(), 1);
var import_date3 = __toESM(require_date(), 1);

// packages/dataviews/build-module/components/dataform-controls/utils/relative-date-control.mjs
var import_components4 = __toESM(require_components(), 1);
var import_element24 = __toESM(require_element(), 1);
var import_i18n5 = __toESM(require_i18n(), 1);
var import_jsx_runtime29 = __toESM(require_jsx_runtime(), 1);
var TIME_UNITS_OPTIONS = {
  [OPERATOR_IN_THE_PAST]: [
    { value: "days", label: (0, import_i18n5.__)("Days") },
    { value: "weeks", label: (0, import_i18n5.__)("Weeks") },
    { value: "months", label: (0, import_i18n5.__)("Months") },
    { value: "years", label: (0, import_i18n5.__)("Years") }
  ],
  [OPERATOR_OVER]: [
    { value: "days", label: (0, import_i18n5.__)("Days ago") },
    { value: "weeks", label: (0, import_i18n5.__)("Weeks ago") },
    { value: "months", label: (0, import_i18n5.__)("Months ago") },
    { value: "years", label: (0, import_i18n5.__)("Years ago") }
  ]
};
function RelativeDateControl({
  className,
  data,
  field,
  onChange,
  hideLabelFromVision,
  operator
}) {
  const options = TIME_UNITS_OPTIONS[operator === OPERATOR_IN_THE_PAST ? "inThePast" : "over"];
  const { id, label, description, getValue, setValue } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  const fieldValue = getValue({ item: data });
  const { value: relValue = "", unit = options[0].value } = fieldValue && typeof fieldValue === "object" ? fieldValue : {};
  const onChangeValue = (0, import_element24.useCallback)(
    (newValue) => onChange(
      setValue({
        item: data,
        value: { value: Number(newValue), unit }
      })
    ),
    [onChange, setValue, data, unit]
  );
  const onChangeUnit = (0, import_element24.useCallback)(
    (newUnit) => onChange(
      setValue({
        item: data,
        value: { value: relValue, unit: newUnit }
      })
    ),
    [onChange, setValue, data, relValue]
  );
  return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
    import_components4.BaseControl,
    {
      id,
      className: clsx_default(className, "dataviews-controls__relative-date"),
      label,
      hideLabelFromVision,
      help: description,
      children: /* @__PURE__ */ (0, import_jsx_runtime29.jsxs)(Stack, { direction: "row", gap: "sm", children: [
        /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
          import_components4.__experimentalNumberControl,
          {
            __next40pxDefaultSize: true,
            className: "dataviews-controls__relative-date-number",
            spinControls: "none",
            min: 1,
            step: 1,
            value: relValue,
            onChange: onChangeValue,
            disabled: disabled2
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
          import_components4.SelectControl,
          {
            className: "dataviews-controls__relative-date-unit",
            __next40pxDefaultSize: true,
            label: (0, import_i18n5.__)("Unit"),
            value: unit,
            options,
            onChange: onChangeUnit,
            hideLabelFromVision: true,
            disabled: disabled2
          }
        )
      ] })
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/utils/use-disabled-date-matchers.mjs
var import_element25 = __toESM(require_element(), 1);
function useDisabledDateMatchers(isValid2, parseDateFn) {
  const minConstraint = typeof isValid2.min?.constraint === "string" ? isValid2.min.constraint : void 0;
  const maxConstraint = typeof isValid2.max?.constraint === "string" ? isValid2.max.constraint : void 0;
  const disabledMatchers = (0, import_element25.useMemo)(() => {
    const matchers = [];
    if (minConstraint) {
      const minDate = parseDateFn(minConstraint);
      if (minDate) {
        matchers.push({ before: minDate });
      }
    }
    if (maxConstraint) {
      const maxDate = parseDateFn(maxConstraint);
      if (maxDate) {
        matchers.push({ after: maxDate });
      }
    }
    return matchers.length > 0 ? matchers : void 0;
  }, [minConstraint, maxConstraint, parseDateFn]);
  return { minConstraint, maxConstraint, disabledMatchers };
}

// packages/dataviews/build-module/field-types/utils/parse-date-time.mjs
var import_date2 = __toESM(require_date(), 1);
function parseDateTime(dateTimeString) {
  if (!dateTimeString) {
    return null;
  }
  const parsed = (0, import_date2.getDate)(dateTimeString);
  return parsed && isValid(parsed) ? parsed : null;
}

// packages/dataviews/build-module/components/dataform-controls/datetime.mjs
var import_jsx_runtime30 = __toESM(require_jsx_runtime(), 1);
var { DateCalendar, ValidatedInputControl } = unlock(import_components5.privateApis);
var formatDateTime = (value) => {
  if (!value) {
    return "";
  }
  return (0, import_date3.dateI18n)("Y-m-d\\TH:i", (0, import_date3.getDate)(value));
};
function CalendarDateTimeControl({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity,
  config
}) {
  const { compact } = config || {};
  const { id, label, description, setValue, getValue, isValid: isValid2 } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  const fieldValue = getValue({ item: data });
  const value = typeof fieldValue === "string" ? fieldValue : void 0;
  const [calendarMonth, setCalendarMonth] = (0, import_element26.useState)(() => {
    const parsedDate = parseDateTime(value);
    return parsedDate || /* @__PURE__ */ new Date();
  });
  const inputControlRef = (0, import_element26.useRef)(null);
  const validationTimeoutRef = (0, import_element26.useRef)(void 0);
  const previousFocusRef = (0, import_element26.useRef)(null);
  const { minConstraint, maxConstraint, disabledMatchers } = useDisabledDateMatchers(isValid2, parseDateTime);
  const onChangeCallback = (0, import_element26.useCallback)(
    (newValue) => onChange(setValue({ item: data, value: newValue })),
    [data, onChange, setValue]
  );
  (0, import_element26.useEffect)(() => {
    return () => {
      if (validationTimeoutRef.current) {
        clearTimeout(validationTimeoutRef.current);
      }
    };
  }, []);
  const onSelectDate = (0, import_element26.useCallback)(
    (newDate) => {
      let dateTimeValue;
      if (newDate) {
        const wpDate = (0, import_date3.dateI18n)("Y-m-d", newDate);
        let wpTime;
        if (value) {
          wpTime = (0, import_date3.dateI18n)("H:i", (0, import_date3.getDate)(value));
        } else {
          wpTime = (0, import_date3.dateI18n)("H:i", newDate);
        }
        const finalDateTime = (0, import_date3.getDate)(`${wpDate}T${wpTime}`);
        dateTimeValue = finalDateTime.toISOString();
        onChangeCallback(dateTimeValue);
        if (validationTimeoutRef.current) {
          clearTimeout(validationTimeoutRef.current);
        }
      } else {
        onChangeCallback(void 0);
      }
      previousFocusRef.current = inputControlRef.current && inputControlRef.current.ownerDocument.activeElement;
      validationTimeoutRef.current = setTimeout(() => {
        if (inputControlRef.current) {
          inputControlRef.current.focus();
          inputControlRef.current.blur();
          onChangeCallback(dateTimeValue);
          if (previousFocusRef.current && previousFocusRef.current instanceof HTMLElement) {
            previousFocusRef.current.focus();
          }
        }
      }, 0);
    },
    [onChangeCallback, value]
  );
  const handleManualDateTimeChange = (0, import_element26.useCallback)(
    (newValue) => {
      if (newValue) {
        const dateTime = (0, import_date3.getDate)(newValue);
        onChangeCallback(dateTime.toISOString());
        const parsedDate = parseDateTime(dateTime.toISOString());
        if (parsedDate) {
          setCalendarMonth(parsedDate);
        }
      } else {
        onChangeCallback(void 0);
      }
    },
    [onChangeCallback]
  );
  const { format: fieldFormat } = field;
  const weekStartsOn = fieldFormat.weekStartsOn ?? (0, import_date3.getSettings)().l10n.startOfWeek;
  const {
    timezone: { string: timezoneString }
  } = (0, import_date3.getSettings)();
  let displayLabel = label;
  if (isValid2?.required && !markWhenOptional && !hideLabelFromVision) {
    displayLabel = `${label} (${(0, import_i18n6.__)("Required")})`;
  } else if (!isValid2?.required && markWhenOptional && !hideLabelFromVision) {
    displayLabel = `${label} (${(0, import_i18n6.__)("Optional")})`;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
    import_components5.BaseControl,
    {
      id,
      label: displayLabel,
      help: description,
      hideLabelFromVision,
      children: /* @__PURE__ */ (0, import_jsx_runtime30.jsxs)(Stack, { direction: "column", gap: "lg", children: [
        /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
          ValidatedInputControl,
          {
            ref: inputControlRef,
            __next40pxDefaultSize: true,
            required: !!isValid2?.required,
            customValidity: getCustomValidity(isValid2, validity),
            type: "datetime-local",
            label: (0, import_i18n6.__)("Date time"),
            hideLabelFromVision: true,
            value: formatDateTime(value),
            onChange: handleManualDateTimeChange,
            disabled: disabled2,
            min: minConstraint ? formatDateTime(minConstraint) : void 0,
            max: maxConstraint ? formatDateTime(maxConstraint) : void 0
          }
        ),
        !compact && /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
          DateCalendar,
          {
            style: { width: "100%" },
            selected: value ? parseDateTime(value) || void 0 : void 0,
            onSelect: onSelectDate,
            month: calendarMonth,
            onMonthChange: setCalendarMonth,
            timeZone: timezoneString || void 0,
            weekStartsOn,
            disabled: disabled2 || disabledMatchers
          }
        )
      ] })
    }
  );
}
function DateTime({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  operator,
  validity,
  config
}) {
  if (operator === OPERATOR_IN_THE_PAST || operator === OPERATOR_OVER) {
    return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
      RelativeDateControl,
      {
        className: "dataviews-controls__datetime",
        data,
        field,
        onChange,
        hideLabelFromVision,
        operator
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
    CalendarDateTimeControl,
    {
      data,
      field,
      onChange,
      hideLabelFromVision,
      markWhenOptional,
      validity,
      config
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/date.mjs
var import_components6 = __toESM(require_components(), 1);
var import_element27 = __toESM(require_element(), 1);
var import_i18n7 = __toESM(require_i18n(), 1);
var import_date4 = __toESM(require_date(), 1);
var import_jsx_runtime31 = __toESM(require_jsx_runtime(), 1);
var { DateCalendar: DateCalendar2, DateRangeCalendar } = unlock(import_components6.privateApis);
var DATE_PRESETS = [
  {
    id: "today",
    label: (0, import_i18n7.__)("Today"),
    getValue: () => (0, import_date4.getDate)(null)
  },
  {
    id: "yesterday",
    label: (0, import_i18n7.__)("Yesterday"),
    getValue: () => {
      const today = (0, import_date4.getDate)(null);
      return subDays(today, 1);
    }
  },
  {
    id: "past-week",
    label: (0, import_i18n7.__)("Past week"),
    getValue: () => {
      const today = (0, import_date4.getDate)(null);
      return subDays(today, 7);
    }
  },
  {
    id: "past-month",
    label: (0, import_i18n7.__)("Past month"),
    getValue: () => {
      const today = (0, import_date4.getDate)(null);
      return subMonths(today, 1);
    }
  }
];
var DATE_RANGE_PRESETS = [
  {
    id: "last-7-days",
    label: (0, import_i18n7.__)("Last 7 days"),
    getValue: () => {
      const today = (0, import_date4.getDate)(null);
      return [subDays(today, 7), today];
    }
  },
  {
    id: "last-30-days",
    label: (0, import_i18n7.__)("Last 30 days"),
    getValue: () => {
      const today = (0, import_date4.getDate)(null);
      return [subDays(today, 30), today];
    }
  },
  {
    id: "month-to-date",
    label: (0, import_i18n7.__)("Month to date"),
    getValue: () => {
      const today = (0, import_date4.getDate)(null);
      return [startOfMonth(today), today];
    }
  },
  {
    id: "last-year",
    label: (0, import_i18n7.__)("Last year"),
    getValue: () => {
      const today = (0, import_date4.getDate)(null);
      return [subYears(today, 1), today];
    }
  },
  {
    id: "year-to-date",
    label: (0, import_i18n7.__)("Year to date"),
    getValue: () => {
      const today = (0, import_date4.getDate)(null);
      return [startOfYear(today), today];
    }
  }
];
var parseDate = (dateString) => {
  if (!dateString) {
    return null;
  }
  const parsed = (0, import_date4.getDate)(dateString);
  return parsed && isValid(parsed) ? parsed : null;
};
var formatDate = (date) => {
  if (!date) {
    return "";
  }
  return typeof date === "string" ? date : format(date, "yyyy-MM-dd");
};
function ValidatedDateControl({
  field,
  validity,
  inputRefs,
  isTouched,
  setIsTouched,
  children
}) {
  const { isValid: isValid2 } = field;
  const [customValidity, setCustomValidity] = (0, import_element27.useState)(void 0);
  const validateRefs = (0, import_element27.useCallback)(() => {
    const refs = Array.isArray(inputRefs) ? inputRefs : [inputRefs];
    for (const ref of refs) {
      const input = ref.current;
      if (input && !input.validity.valid) {
        setCustomValidity({
          type: "invalid",
          message: input.validationMessage
        });
        return;
      }
    }
    setCustomValidity(void 0);
  }, [inputRefs]);
  (0, import_element27.useEffect)(() => {
    const refs = Array.isArray(inputRefs) ? inputRefs : [inputRefs];
    const result = validity ? getCustomValidity(isValid2, validity) : void 0;
    for (const ref of refs) {
      const input = ref.current;
      if (input) {
        input.setCustomValidity(
          result?.type === "invalid" && result.message ? result.message : ""
        );
      }
    }
  }, [inputRefs, isValid2, validity]);
  (0, import_element27.useEffect)(() => {
    const refs = Array.isArray(inputRefs) ? inputRefs : [inputRefs];
    const handleInvalid = (event) => {
      event.preventDefault();
      setIsTouched(true);
    };
    for (const ref of refs) {
      ref.current?.addEventListener("invalid", handleInvalid);
    }
    return () => {
      for (const ref of refs) {
        ref.current?.removeEventListener("invalid", handleInvalid);
      }
    };
  }, [inputRefs, setIsTouched]);
  (0, import_element27.useEffect)(() => {
    if (!isTouched) {
      return;
    }
    const result = validity ? getCustomValidity(isValid2, validity) : void 0;
    if (result) {
      setCustomValidity(result);
    } else {
      validateRefs();
    }
  }, [isTouched, isValid2, validity, validateRefs]);
  const onBlur = (event) => {
    if (isTouched) {
      return;
    }
    if (!event.relatedTarget || !event.currentTarget.contains(event.relatedTarget)) {
      setIsTouched(true);
    }
  };
  return /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)("div", { onBlur, children: [
    children,
    /* @__PURE__ */ (0, import_jsx_runtime31.jsx)("div", { "aria-live": "polite", children: customValidity && /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
      "p",
      {
        className: clsx_default(
          "components-validated-control__indicator",
          customValidity.type === "invalid" ? "is-invalid" : void 0
        ),
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
            import_components6.Icon,
            {
              className: "components-validated-control__indicator-icon",
              icon: error_default,
              size: 16,
              fill: "currentColor"
            }
          ),
          customValidity.message
        ]
      }
    ) })
  ] });
}
function CalendarDateControl({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const {
    id,
    label,
    description,
    setValue,
    getValue,
    isValid: isValid2,
    format: fieldFormat
  } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  const [selectedPresetId, setSelectedPresetId] = (0, import_element27.useState)(
    null
  );
  const weekStartsOn = fieldFormat.weekStartsOn ?? (0, import_date4.getSettings)().l10n.startOfWeek;
  const fieldValue = getValue({ item: data });
  const value = typeof fieldValue === "string" ? fieldValue : void 0;
  const [calendarMonth, setCalendarMonth] = (0, import_element27.useState)(() => {
    const parsedDate = parseDate(value);
    return parsedDate || /* @__PURE__ */ new Date();
  });
  const [isTouched, setIsTouched] = (0, import_element27.useState)(false);
  const validityTargetRef = (0, import_element27.useRef)(null);
  const { minConstraint, maxConstraint, disabledMatchers } = useDisabledDateMatchers(isValid2, parseDate);
  const onChangeCallback = (0, import_element27.useCallback)(
    (newValue) => onChange(setValue({ item: data, value: newValue })),
    [data, onChange, setValue]
  );
  const onSelectDate = (0, import_element27.useCallback)(
    (newDate) => {
      const dateValue = newDate ? format(newDate, "yyyy-MM-dd") : void 0;
      onChangeCallback(dateValue);
      setSelectedPresetId(null);
      setIsTouched(true);
    },
    [onChangeCallback]
  );
  const handlePresetClick = (0, import_element27.useCallback)(
    (preset) => {
      const presetDate = preset.getValue();
      const dateValue = formatDate(presetDate);
      setCalendarMonth(presetDate);
      onChangeCallback(dateValue);
      setSelectedPresetId(preset.id);
      setIsTouched(true);
    },
    [onChangeCallback]
  );
  const handleManualDateChange = (0, import_element27.useCallback)(
    (newValue) => {
      onChangeCallback(newValue);
      if (newValue) {
        const parsedDate = parseDate(newValue);
        if (parsedDate) {
          setCalendarMonth(parsedDate);
        }
      }
      setSelectedPresetId(null);
      setIsTouched(true);
    },
    [onChangeCallback]
  );
  const {
    timezone: { string: timezoneString }
  } = (0, import_date4.getSettings)();
  let displayLabel = label;
  if (isValid2?.required && !markWhenOptional) {
    displayLabel = `${label} (${(0, import_i18n7.__)("Required")})`;
  } else if (!isValid2?.required && markWhenOptional) {
    displayLabel = `${label} (${(0, import_i18n7.__)("Optional")})`;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
    ValidatedDateControl,
    {
      field,
      validity,
      inputRefs: validityTargetRef,
      isTouched,
      setIsTouched,
      children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
        import_components6.BaseControl,
        {
          id,
          className: "dataviews-controls__date",
          label: displayLabel,
          help: description,
          hideLabelFromVision,
          children: /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(Stack, { direction: "column", gap: "lg", children: [
            /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
              Stack,
              {
                direction: "row",
                gap: "sm",
                wrap: "wrap",
                justify: "flex-start",
                children: [
                  DATE_PRESETS.map((preset) => {
                    const isSelected = selectedPresetId === preset.id;
                    return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                      import_components6.Button,
                      {
                        className: "dataviews-controls__date-preset",
                        variant: "tertiary",
                        isPressed: isSelected,
                        size: "small",
                        disabled: disabled2,
                        accessibleWhenDisabled: true,
                        onClick: () => handlePresetClick(preset),
                        children: preset.label
                      },
                      preset.id
                    );
                  }),
                  /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                    import_components6.Button,
                    {
                      className: "dataviews-controls__date-preset",
                      variant: "tertiary",
                      isPressed: !selectedPresetId,
                      size: "small",
                      disabled: !!selectedPresetId || disabled2,
                      accessibleWhenDisabled: true,
                      children: (0, import_i18n7.__)("Custom")
                    }
                  )
                ]
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
              import_components6.__experimentalInputControl,
              {
                __next40pxDefaultSize: true,
                ref: validityTargetRef,
                type: "date",
                label: (0, import_i18n7.__)("Date"),
                hideLabelFromVision: true,
                value,
                onChange: handleManualDateChange,
                required: !!field.isValid?.required,
                disabled: disabled2,
                min: minConstraint,
                max: maxConstraint
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
              DateCalendar2,
              {
                style: { width: "100%" },
                selected: value ? parseDate(value) || void 0 : void 0,
                onSelect: onSelectDate,
                month: calendarMonth,
                onMonthChange: setCalendarMonth,
                timeZone: timezoneString || void 0,
                weekStartsOn,
                disabled: disabled2 || disabledMatchers,
                disableNavigation: disabled2
              }
            )
          ] })
        }
      )
    }
  );
}
function CalendarDateRangeControl({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const {
    id,
    label,
    description,
    getValue,
    setValue,
    isValid: isValid2,
    format: fieldFormat
  } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  let value;
  const fieldValue = getValue({ item: data });
  if (Array.isArray(fieldValue) && fieldValue.length === 2 && fieldValue.every((date) => typeof date === "string")) {
    value = fieldValue;
  }
  const weekStartsOn = fieldFormat.weekStartsOn ?? (0, import_date4.getSettings)().l10n.startOfWeek;
  const { minConstraint, maxConstraint, disabledMatchers } = useDisabledDateMatchers(isValid2, parseDate);
  const onChangeCallback = (0, import_element27.useCallback)(
    (newValue) => {
      onChange(
        setValue({
          item: data,
          value: newValue
        })
      );
    },
    [data, onChange, setValue]
  );
  const [selectedPresetId, setSelectedPresetId] = (0, import_element27.useState)(
    null
  );
  const selectedRange = (0, import_element27.useMemo)(() => {
    if (!value) {
      return { from: void 0, to: void 0 };
    }
    const [from, to] = value;
    return {
      from: parseDate(from) || void 0,
      to: parseDate(to) || void 0
    };
  }, [value]);
  const [calendarMonth, setCalendarMonth] = (0, import_element27.useState)(() => {
    return selectedRange.from || /* @__PURE__ */ new Date();
  });
  const [isTouched, setIsTouched] = (0, import_element27.useState)(false);
  const fromInputRef = (0, import_element27.useRef)(null);
  const toInputRef = (0, import_element27.useRef)(null);
  const updateDateRange = (0, import_element27.useCallback)(
    (fromDate, toDate2) => {
      if (fromDate && toDate2) {
        onChangeCallback([
          formatDate(fromDate),
          formatDate(toDate2)
        ]);
      } else if (!fromDate && !toDate2) {
        onChangeCallback(void 0);
      }
    },
    [onChangeCallback]
  );
  const onSelectCalendarRange = (0, import_element27.useCallback)(
    (newRange) => {
      updateDateRange(newRange?.from, newRange?.to);
      setSelectedPresetId(null);
      setIsTouched(true);
    },
    [updateDateRange]
  );
  const handlePresetClick = (0, import_element27.useCallback)(
    (preset) => {
      const [startDate, endDate] = preset.getValue();
      setCalendarMonth(startDate);
      updateDateRange(startDate, endDate);
      setSelectedPresetId(preset.id);
      setIsTouched(true);
    },
    [updateDateRange]
  );
  const handleManualDateChange = (0, import_element27.useCallback)(
    (fromOrTo, newValue) => {
      const [currentFrom, currentTo] = value || [
        void 0,
        void 0
      ];
      const updatedFrom = fromOrTo === "from" ? newValue : currentFrom;
      const updatedTo = fromOrTo === "to" ? newValue : currentTo;
      updateDateRange(updatedFrom, updatedTo);
      if (newValue) {
        const parsedDate = parseDate(newValue);
        if (parsedDate) {
          setCalendarMonth(parsedDate);
        }
      }
      setSelectedPresetId(null);
      setIsTouched(true);
    },
    [value, updateDateRange]
  );
  const { timezone } = (0, import_date4.getSettings)();
  let displayLabel = label;
  if (field.isValid?.required && !markWhenOptional) {
    displayLabel = `${label} (${(0, import_i18n7.__)("Required")})`;
  } else if (!field.isValid?.required && markWhenOptional) {
    displayLabel = `${label} (${(0, import_i18n7.__)("Optional")})`;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
    ValidatedDateControl,
    {
      field,
      validity,
      inputRefs: [fromInputRef, toInputRef],
      isTouched,
      setIsTouched,
      children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
        import_components6.BaseControl,
        {
          id,
          className: "dataviews-controls__date",
          label: displayLabel,
          help: description,
          hideLabelFromVision,
          children: /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(Stack, { direction: "column", gap: "lg", children: [
            /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
              Stack,
              {
                direction: "row",
                gap: "sm",
                wrap: "wrap",
                justify: "flex-start",
                children: [
                  DATE_RANGE_PRESETS.map((preset) => {
                    const isSelected = selectedPresetId === preset.id;
                    return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                      import_components6.Button,
                      {
                        className: "dataviews-controls__date-preset",
                        variant: "tertiary",
                        isPressed: isSelected,
                        size: "small",
                        disabled: disabled2,
                        accessibleWhenDisabled: true,
                        onClick: () => handlePresetClick(preset),
                        children: preset.label
                      },
                      preset.id
                    );
                  }),
                  /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                    import_components6.Button,
                    {
                      className: "dataviews-controls__date-preset",
                      variant: "tertiary",
                      isPressed: !selectedPresetId,
                      size: "small",
                      accessibleWhenDisabled: true,
                      disabled: !!selectedPresetId || disabled2,
                      children: (0, import_i18n7.__)("Custom")
                    }
                  )
                ]
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
              Stack,
              {
                direction: "row",
                gap: "sm",
                justify: "space-between",
                className: "dataviews-controls__date-range-inputs",
                children: [
                  /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                    import_components6.__experimentalInputControl,
                    {
                      __next40pxDefaultSize: true,
                      ref: fromInputRef,
                      type: "date",
                      label: (0, import_i18n7.__)("From"),
                      hideLabelFromVision: true,
                      value: value?.[0],
                      onChange: (newValue) => handleManualDateChange("from", newValue),
                      required: !!field.isValid?.required,
                      disabled: disabled2,
                      min: minConstraint,
                      max: maxConstraint
                    }
                  ),
                  /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                    import_components6.__experimentalInputControl,
                    {
                      __next40pxDefaultSize: true,
                      ref: toInputRef,
                      type: "date",
                      label: (0, import_i18n7.__)("To"),
                      hideLabelFromVision: true,
                      value: value?.[1],
                      onChange: (newValue) => handleManualDateChange("to", newValue),
                      required: !!field.isValid?.required,
                      disabled: disabled2,
                      min: minConstraint,
                      max: maxConstraint
                    }
                  )
                ]
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
              DateRangeCalendar,
              {
                style: { width: "100%" },
                selected: selectedRange,
                onSelect: onSelectCalendarRange,
                month: calendarMonth,
                onMonthChange: setCalendarMonth,
                timeZone: timezone.string || void 0,
                weekStartsOn,
                disabled: disabled2 || disabledMatchers
              }
            )
          ] })
        }
      )
    }
  );
}
function DateControl({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  operator,
  validity
}) {
  if (operator === OPERATOR_IN_THE_PAST || operator === OPERATOR_OVER) {
    return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
      RelativeDateControl,
      {
        className: "dataviews-controls__date",
        data,
        field,
        onChange,
        hideLabelFromVision,
        operator
      }
    );
  }
  if (operator === OPERATOR_BETWEEN) {
    return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
      CalendarDateRangeControl,
      {
        data,
        field,
        onChange,
        hideLabelFromVision,
        markWhenOptional,
        validity
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
    CalendarDateControl,
    {
      data,
      field,
      onChange,
      hideLabelFromVision,
      markWhenOptional,
      validity
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/select.mjs
var import_components7 = __toESM(require_components(), 1);
var import_element28 = __toESM(require_element(), 1);
var import_jsx_runtime32 = __toESM(require_jsx_runtime(), 1);
var { ValidatedSelectControl } = unlock(import_components7.privateApis);
function Select({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const { type, label, description, getValue, setValue, isValid: isValid2 } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  const isMultiple = type === "array";
  const value = getValue({ item: data }) ?? (isMultiple ? [] : "");
  const onChangeControl = (0, import_element28.useCallback)(
    (newValue) => onChange(setValue({ item: data, value: newValue })),
    [data, onChange, setValue]
  );
  const { elements, isLoading } = useElements({
    elements: field.elements,
    getElements: field.getElements
  });
  if (isLoading) {
    return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(import_components7.Spinner, {});
  }
  return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
    ValidatedSelectControl,
    {
      required: !!field.isValid?.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      label,
      value,
      help: description,
      options: elements,
      onChange: onChangeControl,
      __next40pxDefaultSize: true,
      hideLabelFromVision,
      multiple: isMultiple,
      disabled: disabled2
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/adaptive-select.mjs
var import_jsx_runtime33 = __toESM(require_jsx_runtime(), 1);
var ELEMENTS_THRESHOLD = 10;
function AdaptiveSelect(props) {
  const { field } = props;
  const { elements } = useElements({
    elements: field.elements,
    getElements: field.getElements
  });
  if (elements.length >= ELEMENTS_THRESHOLD) {
    return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(Combobox, { ...props });
  }
  return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(Select, { ...props });
}

// packages/dataviews/build-module/components/dataform-controls/email.mjs
var import_components9 = __toESM(require_components(), 1);

// packages/dataviews/build-module/components/dataform-controls/utils/validated-input.mjs
var import_components8 = __toESM(require_components(), 1);
var import_element29 = __toESM(require_element(), 1);
var import_jsx_runtime34 = __toESM(require_jsx_runtime(), 1);
var { ValidatedInputControl: ValidatedInputControl2 } = unlock(import_components8.privateApis);
function ValidatedText({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  type,
  prefix,
  suffix,
  validity
}) {
  const { label, placeholder, description, getValue, setValue, isValid: isValid2 } = field;
  const value = getValue({ item: data });
  const disabled2 = field.isDisabled({ item: data, field });
  const onChangeControl = (0, import_element29.useCallback)(
    (newValue) => onChange(
      setValue({
        item: data,
        value: newValue
      })
    ),
    [data, setValue, onChange]
  );
  return /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
    ValidatedInputControl2,
    {
      required: !!isValid2.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      label,
      placeholder,
      value: value ?? "",
      help: description,
      onChange: onChangeControl,
      hideLabelFromVision,
      type,
      prefix,
      suffix,
      disabled: disabled2,
      pattern: isValid2.pattern ? isValid2.pattern.constraint : void 0,
      minLength: isValid2.minLength ? isValid2.minLength.constraint : void 0,
      maxLength: isValid2.maxLength ? isValid2.maxLength.constraint : void 0,
      __next40pxDefaultSize: true
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/email.mjs
var import_jsx_runtime35 = __toESM(require_jsx_runtime(), 1);
function Email({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
    ValidatedText,
    {
      ...{
        data,
        field,
        onChange,
        hideLabelFromVision,
        markWhenOptional,
        validity,
        type: "email",
        prefix: /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(import_components9.__experimentalInputControlPrefixWrapper, { variant: "icon", children: /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(import_components9.Icon, { icon: envelope_default }) })
      }
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/telephone.mjs
var import_components10 = __toESM(require_components(), 1);
var import_jsx_runtime36 = __toESM(require_jsx_runtime(), 1);
function Telephone({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
    ValidatedText,
    {
      ...{
        data,
        field,
        onChange,
        hideLabelFromVision,
        markWhenOptional,
        validity,
        type: "tel",
        prefix: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components10.__experimentalInputControlPrefixWrapper, { variant: "icon", children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components10.Icon, { icon: mobile_default }) })
      }
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/url.mjs
var import_components11 = __toESM(require_components(), 1);
var import_jsx_runtime37 = __toESM(require_jsx_runtime(), 1);
function Url({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
    ValidatedText,
    {
      ...{
        data,
        field,
        onChange,
        hideLabelFromVision,
        markWhenOptional,
        validity,
        type: "url",
        prefix: /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(import_components11.__experimentalInputControlPrefixWrapper, { variant: "icon", children: /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(import_components11.Icon, { icon: link_default }) })
      }
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/utils/validated-number.mjs
var import_components12 = __toESM(require_components(), 1);
var import_element30 = __toESM(require_element(), 1);
var import_i18n8 = __toESM(require_i18n(), 1);
var import_jsx_runtime38 = __toESM(require_jsx_runtime(), 1);
var { ValidatedNumberControl } = unlock(import_components12.privateApis);
function toNumberOrEmpty(value) {
  if (value === "" || value === void 0) {
    return "";
  }
  const number = Number(value);
  return Number.isFinite(number) ? number : "";
}
function BetweenControls({
  value,
  onChange,
  hideLabelFromVision,
  step
}) {
  const [min = "", max = ""] = value;
  const onChangeMin = (0, import_element30.useCallback)(
    (newValue) => onChange([toNumberOrEmpty(newValue), max]),
    [onChange, max]
  );
  const onChangeMax = (0, import_element30.useCallback)(
    (newValue) => onChange([min, toNumberOrEmpty(newValue)]),
    [onChange, min]
  );
  return /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
    import_components12.BaseControl,
    {
      help: (0, import_i18n8.__)("The max. value must be greater than the min. value."),
      children: /* @__PURE__ */ (0, import_jsx_runtime38.jsxs)(import_components12.Flex, { direction: "row", gap: 4, children: [
        /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
          import_components12.__experimentalNumberControl,
          {
            label: (0, import_i18n8.__)("Min."),
            value: min,
            max: max ? Number(max) - step : void 0,
            onChange: onChangeMin,
            __next40pxDefaultSize: true,
            hideLabelFromVision,
            step
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
          import_components12.__experimentalNumberControl,
          {
            label: (0, import_i18n8.__)("Max."),
            value: max,
            min: min ? Number(min) + step : void 0,
            onChange: onChangeMax,
            __next40pxDefaultSize: true,
            hideLabelFromVision,
            step
          }
        )
      ] })
    }
  );
}
function ValidatedNumber({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  operator,
  validity
}) {
  const decimals = field.format?.decimals ?? 0;
  const step = Math.pow(10, Math.abs(decimals) * -1);
  const { label, description, getValue, setValue, isValid: isValid2 } = field;
  const value = getValue({ item: data }) ?? "";
  const disabled2 = field.isDisabled({ item: data, field });
  const onChangeControl = (0, import_element30.useCallback)(
    (newValue) => {
      onChange(
        setValue({
          item: data,
          // Do not convert an empty string or undefined to a number,
          // otherwise there's a mismatch between the UI control (empty)
          // and the data relied by onChange (0).
          value: ["", void 0].includes(newValue) ? void 0 : Number(newValue)
        })
      );
    },
    [data, onChange, setValue]
  );
  const onChangeBetweenControls = (0, import_element30.useCallback)(
    (newValue) => {
      onChange(
        setValue({
          item: data,
          value: newValue
        })
      );
    },
    [data, onChange, setValue]
  );
  if (operator === OPERATOR_BETWEEN) {
    let valueBetween = ["", ""];
    if (Array.isArray(value) && value.length === 2 && value.every(
      (element) => typeof element === "number" || element === ""
    )) {
      valueBetween = value;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
      BetweenControls,
      {
        value: valueBetween,
        onChange: onChangeBetweenControls,
        hideLabelFromVision,
        step
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
    ValidatedNumberControl,
    {
      required: !!isValid2.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      label,
      help: description,
      value,
      onChange: onChangeControl,
      __next40pxDefaultSize: true,
      hideLabelFromVision,
      step,
      min: isValid2.min ? isValid2.min.constraint : void 0,
      max: isValid2.max ? isValid2.max.constraint : void 0,
      disabled: disabled2
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/integer.mjs
var import_jsx_runtime39 = __toESM(require_jsx_runtime(), 1);
function Integer(props) {
  return /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(ValidatedNumber, { ...props });
}

// packages/dataviews/build-module/components/dataform-controls/number.mjs
var import_jsx_runtime40 = __toESM(require_jsx_runtime(), 1);
function Number2(props) {
  return /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(ValidatedNumber, { ...props });
}

// packages/dataviews/build-module/components/dataform-controls/radio.mjs
var import_components13 = __toESM(require_components(), 1);
var import_element31 = __toESM(require_element(), 1);
var import_jsx_runtime41 = __toESM(require_jsx_runtime(), 1);
var { ValidatedRadioControl } = unlock(import_components13.privateApis);
function Radio({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const { label, description, getValue, setValue, isValid: isValid2 } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  const { elements, isLoading } = useElements({
    elements: field.elements,
    getElements: field.getElements
  });
  const value = getValue({ item: data });
  const onChangeControl = (0, import_element31.useCallback)(
    (newValue) => onChange(setValue({ item: data, value: newValue })),
    [data, onChange, setValue]
  );
  if (isLoading) {
    return /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(import_components13.Spinner, {});
  }
  return /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(
    ValidatedRadioControl,
    {
      required: !!field.isValid?.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      label,
      help: description,
      onChange: onChangeControl,
      options: elements,
      selected: value,
      hideLabelFromVision,
      disabled: disabled2
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/text.mjs
var import_element32 = __toESM(require_element(), 1);
var import_jsx_runtime42 = __toESM(require_jsx_runtime(), 1);
function Text3({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  config,
  validity
}) {
  const { prefix, suffix } = config || {};
  return /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
    ValidatedText,
    {
      ...{
        data,
        field,
        onChange,
        hideLabelFromVision,
        markWhenOptional,
        validity,
        prefix: prefix ? (0, import_element32.createElement)(prefix) : void 0,
        suffix: suffix ? (0, import_element32.createElement)(suffix) : void 0
      }
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/toggle.mjs
var import_components14 = __toESM(require_components(), 1);
var import_element33 = __toESM(require_element(), 1);
var import_jsx_runtime43 = __toESM(require_jsx_runtime(), 1);
var { ValidatedToggleControl } = unlock(import_components14.privateApis);
function Toggle({
  field,
  onChange,
  data,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const { label, description, getValue, setValue, isValid: isValid2 } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  const onChangeControl = (0, import_element33.useCallback)(() => {
    onChange(
      setValue({ item: data, value: !getValue({ item: data }) })
    );
  }, [onChange, setValue, data, getValue]);
  return /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(
    ValidatedToggleControl,
    {
      required: !!isValid2.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      hidden: hideLabelFromVision,
      label,
      help: description,
      checked: getValue({ item: data }),
      onChange: onChangeControl,
      disabled: disabled2
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/textarea.mjs
var import_components15 = __toESM(require_components(), 1);
var import_element34 = __toESM(require_element(), 1);
var import_jsx_runtime44 = __toESM(require_jsx_runtime(), 1);
var { ValidatedTextareaControl } = unlock(import_components15.privateApis);
function Textarea({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  config,
  validity
}) {
  const { rows = 4 } = config || {};
  const disabled2 = field.isDisabled({ item: data, field });
  const { label, placeholder, description, setValue, isValid: isValid2 } = field;
  const value = field.getValue({ item: data });
  const onChangeControl = (0, import_element34.useCallback)(
    (newValue) => onChange(setValue({ item: data, value: newValue })),
    [data, onChange, setValue]
  );
  return /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(
    ValidatedTextareaControl,
    {
      required: !!isValid2.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      label,
      placeholder,
      value: value ?? "",
      help: description,
      onChange: onChangeControl,
      rows,
      disabled: disabled2,
      minLength: isValid2.minLength ? isValid2.minLength.constraint : void 0,
      maxLength: isValid2.maxLength ? isValid2.maxLength.constraint : void 0,
      __next40pxDefaultSize: true,
      hideLabelFromVision
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/toggle-group.mjs
var import_components16 = __toESM(require_components(), 1);
var import_element35 = __toESM(require_element(), 1);
var import_jsx_runtime45 = __toESM(require_jsx_runtime(), 1);
var { ValidatedToggleGroupControl } = unlock(import_components16.privateApis);
function ToggleGroup({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const { getValue, setValue, isValid: isValid2 } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  const value = getValue({ item: data });
  const onChangeControl = (0, import_element35.useCallback)(
    (newValue) => onChange(setValue({ item: data, value: newValue })),
    [data, onChange, setValue]
  );
  const { elements, isLoading } = useElements({
    elements: field.elements,
    getElements: field.getElements
  });
  if (isLoading) {
    return /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(import_components16.Spinner, {});
  }
  if (elements.length === 0) {
    return null;
  }
  const selectedOption = elements.find((el) => el.value === value);
  return /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
    ValidatedToggleGroupControl,
    {
      required: !!field.isValid?.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      __next40pxDefaultSize: true,
      isBlock: true,
      label: field.label,
      help: selectedOption?.description || field.description,
      onChange: onChangeControl,
      value,
      hideLabelFromVision,
      children: elements.map((el) => /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
        import_components16.__experimentalToggleGroupControlOption,
        {
          label: el.label,
          value: el.value,
          disabled: disabled2
        },
        el.value
      ))
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/array.mjs
var import_components17 = __toESM(require_components(), 1);
var import_element36 = __toESM(require_element(), 1);
var import_jsx_runtime46 = __toESM(require_jsx_runtime(), 1);
var { ValidatedFormTokenField } = unlock(import_components17.privateApis);
function ArrayControl({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const { label, placeholder, description, getValue, setValue, isValid: isValid2 } = field;
  const value = getValue({ item: data });
  const disabled2 = field.isDisabled({ item: data, field });
  const { elements, isLoading } = useElements({
    elements: field.elements,
    getElements: field.getElements
  });
  const arrayValueAsElements = (0, import_element36.useMemo)(
    () => Array.isArray(value) ? value.map((token) => {
      const element = elements?.find(
        (suggestion) => suggestion.value === token
      );
      return element || { value: token, label: token };
    }) : [],
    [value, elements]
  );
  const onChangeControl = (0, import_element36.useCallback)(
    (tokens) => {
      const valueTokens = tokens.map((token) => {
        if (typeof token === "object" && "value" in token) {
          return token.value;
        }
        return token;
      });
      onChange(setValue({ item: data, value: valueTokens }));
    },
    [onChange, setValue, data]
  );
  if (isLoading) {
    return /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(import_components17.Spinner, {});
  }
  return /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(
    ValidatedFormTokenField,
    {
      required: !!isValid2?.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      label: hideLabelFromVision ? void 0 : label,
      value: arrayValueAsElements,
      onChange: onChangeControl,
      placeholder,
      suggestions: elements?.map((element) => element.value),
      disabled: disabled2,
      __experimentalValidateInput: (token) => {
        if (field.isValid?.elements && elements) {
          return elements.some(
            (element) => element.value === token || element.label === token
          );
        }
        return true;
      },
      __experimentalExpandOnFocus: elements && elements.length > 0,
      help: description ?? (field.isValid?.elements ? "" : void 0),
      displayTransform: (token) => {
        if (typeof token === "object" && "label" in token) {
          return token.label;
        }
        if (typeof token === "string" && elements) {
          const element = elements.find(
            (el) => el.value === token
          );
          return element?.label || token;
        }
        return token;
      },
      __experimentalRenderItem: ({ item }) => {
        if (typeof item === "string" && elements) {
          const element = elements.find(
            (el) => el.value === item
          );
          return /* @__PURE__ */ (0, import_jsx_runtime46.jsx)("span", { children: element?.label || item });
        }
        return /* @__PURE__ */ (0, import_jsx_runtime46.jsx)("span", { children: item });
      }
    }
  );
}

// node_modules/colord/index.mjs
var r2 = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) };
var t = function(r3) {
  return "string" == typeof r3 ? r3.length > 0 : "number" == typeof r3;
};
var n = function(r3, t2, n2) {
  return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = Math.pow(10, t2)), Math.round(n2 * r3) / n2 + 0;
};
var e = function(r3, t2, n2) {
  return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = 1), r3 > n2 ? n2 : r3 > t2 ? r3 : t2;
};
var u = function(r3) {
  return (r3 = isFinite(r3) ? r3 % 360 : 0) > 0 ? r3 : r3 + 360;
};
var a = function(r3) {
  return { r: e(r3.r, 0, 255), g: e(r3.g, 0, 255), b: e(r3.b, 0, 255), a: e(r3.a) };
};
var o = function(r3) {
  return { r: n(r3.r), g: n(r3.g), b: n(r3.b), a: n(r3.a, 3) };
};
var i = /^#([0-9a-f]{3,8})$/i;
var s = function(r3) {
  var t2 = r3.toString(16);
  return t2.length < 2 ? "0" + t2 : t2;
};
var h = function(r3) {
  var t2 = r3.r, n2 = r3.g, e2 = r3.b, u2 = r3.a, a2 = Math.max(t2, n2, e2), o2 = a2 - Math.min(t2, n2, e2), i2 = o2 ? a2 === t2 ? (n2 - e2) / o2 : a2 === n2 ? 2 + (e2 - t2) / o2 : 4 + (t2 - n2) / o2 : 0;
  return { h: 60 * (i2 < 0 ? i2 + 6 : i2), s: a2 ? o2 / a2 * 100 : 0, v: a2 / 255 * 100, a: u2 };
};
var b = function(r3) {
  var t2 = r3.h, n2 = r3.s, e2 = r3.v, u2 = r3.a;
  t2 = t2 / 360 * 6, n2 /= 100, e2 /= 100;
  var a2 = Math.floor(t2), o2 = e2 * (1 - n2), i2 = e2 * (1 - (t2 - a2) * n2), s2 = e2 * (1 - (1 - t2 + a2) * n2), h2 = a2 % 6;
  return { r: 255 * [e2, i2, o2, o2, s2, e2][h2], g: 255 * [s2, e2, e2, i2, o2, o2][h2], b: 255 * [o2, o2, s2, e2, e2, i2][h2], a: u2 };
};
var g = function(r3) {
  return { h: u(r3.h), s: e(r3.s, 0, 100), l: e(r3.l, 0, 100), a: e(r3.a) };
};
var d = function(r3) {
  return { h: n(r3.h), s: n(r3.s), l: n(r3.l), a: n(r3.a, 3) };
};
var f = function(r3) {
  return b((n2 = (t2 = r3).s, { h: t2.h, s: (n2 *= ((e2 = t2.l) < 50 ? e2 : 100 - e2) / 100) > 0 ? 2 * n2 / (e2 + n2) * 100 : 0, v: e2 + n2, a: t2.a }));
  var t2, n2, e2;
};
var c = function(r3) {
  return { h: (t2 = h(r3)).h, s: (u2 = (200 - (n2 = t2.s)) * (e2 = t2.v) / 100) > 0 && u2 < 200 ? n2 * e2 / 100 / (u2 <= 100 ? u2 : 200 - u2) * 100 : 0, l: u2 / 2, a: t2.a };
  var t2, n2, e2, u2;
};
var l = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var p = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var v = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var m = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var y = { string: [[function(r3) {
  var t2 = i.exec(r3);
  return t2 ? (r3 = t2[1]).length <= 4 ? { r: parseInt(r3[0] + r3[0], 16), g: parseInt(r3[1] + r3[1], 16), b: parseInt(r3[2] + r3[2], 16), a: 4 === r3.length ? n(parseInt(r3[3] + r3[3], 16) / 255, 2) : 1 } : 6 === r3.length || 8 === r3.length ? { r: parseInt(r3.substr(0, 2), 16), g: parseInt(r3.substr(2, 2), 16), b: parseInt(r3.substr(4, 2), 16), a: 8 === r3.length ? n(parseInt(r3.substr(6, 2), 16) / 255, 2) : 1 } : null : null;
}, "hex"], [function(r3) {
  var t2 = v.exec(r3) || m.exec(r3);
  return t2 ? t2[2] !== t2[4] || t2[4] !== t2[6] ? null : a({ r: Number(t2[1]) / (t2[2] ? 100 / 255 : 1), g: Number(t2[3]) / (t2[4] ? 100 / 255 : 1), b: Number(t2[5]) / (t2[6] ? 100 / 255 : 1), a: void 0 === t2[7] ? 1 : Number(t2[7]) / (t2[8] ? 100 : 1) }) : null;
}, "rgb"], [function(t2) {
  var n2 = l.exec(t2) || p.exec(t2);
  if (!n2) return null;
  var e2, u2, a2 = g({ h: (e2 = n2[1], u2 = n2[2], void 0 === u2 && (u2 = "deg"), Number(e2) * (r2[u2] || 1)), s: Number(n2[3]), l: Number(n2[4]), a: void 0 === n2[5] ? 1 : Number(n2[5]) / (n2[6] ? 100 : 1) });
  return f(a2);
}, "hsl"]], object: [[function(r3) {
  var n2 = r3.r, e2 = r3.g, u2 = r3.b, o2 = r3.a, i2 = void 0 === o2 ? 1 : o2;
  return t(n2) && t(e2) && t(u2) ? a({ r: Number(n2), g: Number(e2), b: Number(u2), a: Number(i2) }) : null;
}, "rgb"], [function(r3) {
  var n2 = r3.h, e2 = r3.s, u2 = r3.l, a2 = r3.a, o2 = void 0 === a2 ? 1 : a2;
  if (!t(n2) || !t(e2) || !t(u2)) return null;
  var i2 = g({ h: Number(n2), s: Number(e2), l: Number(u2), a: Number(o2) });
  return f(i2);
}, "hsl"], [function(r3) {
  var n2 = r3.h, a2 = r3.s, o2 = r3.v, i2 = r3.a, s2 = void 0 === i2 ? 1 : i2;
  if (!t(n2) || !t(a2) || !t(o2)) return null;
  var h2 = (function(r4) {
    return { h: u(r4.h), s: e(r4.s, 0, 100), v: e(r4.v, 0, 100), a: e(r4.a) };
  })({ h: Number(n2), s: Number(a2), v: Number(o2), a: Number(s2) });
  return b(h2);
}, "hsv"]] };
var N = function(r3, t2) {
  for (var n2 = 0; n2 < t2.length; n2++) {
    var e2 = t2[n2][0](r3);
    if (e2) return [e2, t2[n2][1]];
  }
  return [null, void 0];
};
var x = function(r3) {
  return "string" == typeof r3 ? N(r3.trim(), y.string) : "object" == typeof r3 && null !== r3 ? N(r3, y.object) : [null, void 0];
};
var M = function(r3, t2) {
  var n2 = c(r3);
  return { h: n2.h, s: e(n2.s + 100 * t2, 0, 100), l: n2.l, a: n2.a };
};
var H = function(r3) {
  return (299 * r3.r + 587 * r3.g + 114 * r3.b) / 1e3 / 255;
};
var $ = function(r3, t2) {
  var n2 = c(r3);
  return { h: n2.h, s: n2.s, l: e(n2.l + 100 * t2, 0, 100), a: n2.a };
};
var j = (function() {
  function r3(r4) {
    this.parsed = x(r4)[0], this.rgba = this.parsed || { r: 0, g: 0, b: 0, a: 1 };
  }
  return r3.prototype.isValid = function() {
    return null !== this.parsed;
  }, r3.prototype.brightness = function() {
    return n(H(this.rgba), 2);
  }, r3.prototype.isDark = function() {
    return H(this.rgba) < 0.5;
  }, r3.prototype.isLight = function() {
    return H(this.rgba) >= 0.5;
  }, r3.prototype.toHex = function() {
    return r4 = o(this.rgba), t2 = r4.r, e2 = r4.g, u2 = r4.b, i2 = (a2 = r4.a) < 1 ? s(n(255 * a2)) : "", "#" + s(t2) + s(e2) + s(u2) + i2;
    var r4, t2, e2, u2, a2, i2;
  }, r3.prototype.toRgb = function() {
    return o(this.rgba);
  }, r3.prototype.toRgbString = function() {
    return r4 = o(this.rgba), t2 = r4.r, n2 = r4.g, e2 = r4.b, (u2 = r4.a) < 1 ? "rgba(" + t2 + ", " + n2 + ", " + e2 + ", " + u2 + ")" : "rgb(" + t2 + ", " + n2 + ", " + e2 + ")";
    var r4, t2, n2, e2, u2;
  }, r3.prototype.toHsl = function() {
    return d(c(this.rgba));
  }, r3.prototype.toHslString = function() {
    return r4 = d(c(this.rgba)), t2 = r4.h, n2 = r4.s, e2 = r4.l, (u2 = r4.a) < 1 ? "hsla(" + t2 + ", " + n2 + "%, " + e2 + "%, " + u2 + ")" : "hsl(" + t2 + ", " + n2 + "%, " + e2 + "%)";
    var r4, t2, n2, e2, u2;
  }, r3.prototype.toHsv = function() {
    return r4 = h(this.rgba), { h: n(r4.h), s: n(r4.s), v: n(r4.v), a: n(r4.a, 3) };
    var r4;
  }, r3.prototype.invert = function() {
    return w({ r: 255 - (r4 = this.rgba).r, g: 255 - r4.g, b: 255 - r4.b, a: r4.a });
    var r4;
  }, r3.prototype.saturate = function(r4) {
    return void 0 === r4 && (r4 = 0.1), w(M(this.rgba, r4));
  }, r3.prototype.desaturate = function(r4) {
    return void 0 === r4 && (r4 = 0.1), w(M(this.rgba, -r4));
  }, r3.prototype.grayscale = function() {
    return w(M(this.rgba, -1));
  }, r3.prototype.lighten = function(r4) {
    return void 0 === r4 && (r4 = 0.1), w($(this.rgba, r4));
  }, r3.prototype.darken = function(r4) {
    return void 0 === r4 && (r4 = 0.1), w($(this.rgba, -r4));
  }, r3.prototype.rotate = function(r4) {
    return void 0 === r4 && (r4 = 15), this.hue(this.hue() + r4);
  }, r3.prototype.alpha = function(r4) {
    return "number" == typeof r4 ? w({ r: (t2 = this.rgba).r, g: t2.g, b: t2.b, a: r4 }) : n(this.rgba.a, 3);
    var t2;
  }, r3.prototype.hue = function(r4) {
    var t2 = c(this.rgba);
    return "number" == typeof r4 ? w({ h: r4, s: t2.s, l: t2.l, a: t2.a }) : n(t2.h);
  }, r3.prototype.isEqual = function(r4) {
    return this.toHex() === w(r4).toHex();
  }, r3;
})();
var w = function(r3) {
  return r3 instanceof j ? r3 : new j(r3);
};

// packages/dataviews/build-module/components/dataform-controls/color.mjs
var import_components18 = __toESM(require_components(), 1);
var import_element37 = __toESM(require_element(), 1);
var import_i18n9 = __toESM(require_i18n(), 1);
var import_jsx_runtime47 = __toESM(require_jsx_runtime(), 1);
var { ValidatedInputControl: ValidatedInputControl3 } = unlock(import_components18.privateApis);
var ColorPickerDropdown = ({
  color,
  onColorChange,
  disabled: disabled2
}) => {
  const validColor = color && w(color).isValid() ? color : "#ffffff";
  return /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(
    import_components18.Dropdown,
    {
      className: "dataviews-controls__color-picker-dropdown",
      popoverProps: { resize: false },
      renderToggle: ({ onToggle }) => /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(
        import_components18.Button,
        {
          onClick: onToggle,
          "aria-label": (0, import_i18n9.__)("Open color picker"),
          size: "small",
          disabled: disabled2,
          accessibleWhenDisabled: true,
          icon: () => /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(import_components18.ColorIndicator, { colorValue: validColor })
        }
      ),
      renderContent: () => /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(import_components18.__experimentalDropdownContentWrapper, { paddingSize: "none", children: /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(
        import_components18.ColorPicker,
        {
          color: validColor,
          onChange: onColorChange,
          enableAlpha: true
        }
      ) })
    }
  );
};
function Color({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const { label, placeholder, description, setValue, isValid: isValid2 } = field;
  const disabled2 = field.isDisabled({ item: data, field });
  const value = field.getValue({ item: data }) || "";
  const handleColorChange = (0, import_element37.useCallback)(
    (newColor) => {
      onChange(setValue({ item: data, value: newColor }));
    },
    [data, onChange, setValue]
  );
  const handleInputChange = (0, import_element37.useCallback)(
    (newValue) => {
      onChange(setValue({ item: data, value: newValue || "" }));
    },
    [data, onChange, setValue]
  );
  return /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(
    ValidatedInputControl3,
    {
      required: !!field.isValid?.required,
      markWhenOptional,
      customValidity: getCustomValidity(isValid2, validity),
      label,
      placeholder,
      value,
      help: description,
      onChange: handleInputChange,
      hideLabelFromVision,
      type: "text",
      disabled: disabled2,
      prefix: /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(import_components18.__experimentalInputControlPrefixWrapper, { variant: "control", children: /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(
        ColorPickerDropdown,
        {
          color: value,
          onColorChange: handleColorChange,
          disabled: disabled2
        }
      ) })
    }
  );
}

// packages/dataviews/build-module/components/dataform-controls/password.mjs
var import_components19 = __toESM(require_components(), 1);
var import_element38 = __toESM(require_element(), 1);
var import_i18n10 = __toESM(require_i18n(), 1);
var import_jsx_runtime48 = __toESM(require_jsx_runtime(), 1);
function Password({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const [isVisible, setIsVisible] = (0, import_element38.useState)(false);
  const disabled2 = field.isDisabled({ item: data, field });
  const toggleVisibility = (0, import_element38.useCallback)(() => {
    setIsVisible((prev) => !prev);
  }, []);
  return /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(
    ValidatedText,
    {
      ...{
        data,
        field,
        onChange,
        hideLabelFromVision,
        markWhenOptional,
        validity,
        type: isVisible ? "text" : "password",
        suffix: /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(import_components19.__experimentalInputControlSuffixWrapper, { variant: "control", children: /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(
          import_components19.Button,
          {
            icon: isVisible ? unseen_default : seen_default,
            onClick: toggleVisibility,
            size: "small",
            label: isVisible ? (0, import_i18n10.__)("Hide password") : (0, import_i18n10.__)("Show password"),
            disabled: disabled2,
            accessibleWhenDisabled: true
          }
        ) })
      }
    }
  );
}

// packages/dataviews/build-module/field-types/utils/has-elements.mjs
function hasElements(field) {
  return Array.isArray(field.elements) && field.elements.length > 0 || typeof field.getElements === "function";
}

// packages/dataviews/build-module/components/dataform-controls/index.mjs
var import_jsx_runtime49 = __toESM(require_jsx_runtime(), 1);
var FORM_CONTROLS = {
  adaptiveSelect: AdaptiveSelect,
  array: ArrayControl,
  checkbox: Checkbox,
  color: Color,
  combobox: Combobox,
  datetime: DateTime,
  date: DateControl,
  email: Email,
  telephone: Telephone,
  url: Url,
  integer: Integer,
  number: Number2,
  password: Password,
  radio: Radio,
  select: Select,
  text: Text3,
  toggle: Toggle,
  textarea: Textarea,
  toggleGroup: ToggleGroup
};
function isEditConfig(value) {
  return value && typeof value === "object" && typeof value.control === "string";
}
function createConfiguredControl(config) {
  const { control, ...controlConfig } = config;
  const BaseControlType = getControlByType(control);
  if (BaseControlType === null) {
    return null;
  }
  return function ConfiguredControl(props) {
    return /* @__PURE__ */ (0, import_jsx_runtime49.jsx)(BaseControlType, { ...props, config: controlConfig });
  };
}
function getControl(field, fallback) {
  if (typeof field.Edit === "function") {
    return field.Edit;
  }
  if (typeof field.Edit === "string") {
    return getControlByType(field.Edit);
  }
  if (isEditConfig(field.Edit)) {
    return createConfiguredControl(field.Edit);
  }
  if (hasElements(field) && field.type !== "array") {
    return getControlByType("adaptiveSelect");
  }
  if (fallback === null) {
    return null;
  }
  return getControlByType(fallback);
}
function getControlByType(type) {
  if (Object.keys(FORM_CONTROLS).includes(type)) {
    return FORM_CONTROLS[type];
  }
  return null;
}

// packages/dataviews/build-module/field-types/utils/get-filter-by.mjs
function getFilterBy(field, defaultOperators, validOperators) {
  if (field.filterBy === false) {
    return false;
  }
  const operators = field.filterBy?.operators?.filter(
    (op) => validOperators.includes(op)
  ) ?? defaultOperators;
  if (operators.length === 0) {
    return false;
  }
  return {
    isPrimary: !!field.filterBy?.isPrimary,
    operators
  };
}
var get_filter_by_default = getFilterBy;

// packages/dataviews/build-module/field-types/utils/get-value-from-id.mjs
var getValueFromId = (id) => ({ item }) => {
  const path = id.split(".");
  let value = item;
  for (const segment of path) {
    if (value.hasOwnProperty(segment)) {
      value = value[segment];
    } else {
      value = void 0;
    }
  }
  return value;
};
var get_value_from_id_default = getValueFromId;

// packages/dataviews/build-module/field-types/utils/set-value-from-id.mjs
var setValueFromId = (id) => ({ value }) => {
  const path = id.split(".");
  const result = {};
  let current = result;
  for (const segment of path.slice(0, -1)) {
    current[segment] = {};
    current = current[segment];
  }
  current[path.at(-1)] = value;
  return result;
};
var set_value_from_id_default = setValueFromId;

// packages/dataviews/build-module/field-types/email.mjs
var import_i18n11 = __toESM(require_i18n(), 1);

// packages/dataviews/build-module/field-types/utils/render-from-elements.mjs
function RenderFromElements({
  item,
  field
}) {
  const { elements, isLoading } = useElements({
    elements: field.elements,
    getElements: field.getElements
  });
  const value = field.getValue({ item });
  if (isLoading) {
    return value;
  }
  if (elements.length === 0) {
    return value;
  }
  return elements?.find((element) => element.value === value)?.label || field.getValue({ item });
}

// packages/dataviews/build-module/field-types/utils/render-default.mjs
var import_jsx_runtime50 = __toESM(require_jsx_runtime(), 1);
function render({
  item,
  field
}) {
  if (field.hasElements) {
    return /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(RenderFromElements, { item, field });
  }
  return field.getValueFormatted({ item, field });
}

// packages/dataviews/build-module/field-types/utils/sort-text.mjs
var sort_text_default = (a2, b2, direction) => {
  return direction === "asc" ? a2.localeCompare(b2) : b2.localeCompare(a2);
};

// packages/dataviews/build-module/field-types/utils/is-valid-required.mjs
function isValidRequired(item, field) {
  const value = field.getValue({ item });
  return ![void 0, "", null].includes(value);
}

// packages/dataviews/build-module/field-types/utils/is-valid-min-length.mjs
function isValidMinLength(item, field) {
  if (typeof field.isValid.minLength?.constraint !== "number") {
    return false;
  }
  const value = field.getValue({ item });
  if ([void 0, "", null].includes(value)) {
    return true;
  }
  return String(value).length >= field.isValid.minLength.constraint;
}

// packages/dataviews/build-module/field-types/utils/is-valid-max-length.mjs
function isValidMaxLength(item, field) {
  if (typeof field.isValid.maxLength?.constraint !== "number") {
    return false;
  }
  const value = field.getValue({ item });
  if ([void 0, "", null].includes(value)) {
    return true;
  }
  return String(value).length <= field.isValid.maxLength.constraint;
}

// packages/dataviews/build-module/field-types/utils/is-valid-pattern.mjs
function isValidPattern(item, field) {
  if (field.isValid.pattern?.constraint === void 0) {
    return true;
  }
  try {
    const regexp = new RegExp(field.isValid.pattern.constraint);
    const value = field.getValue({ item });
    if ([void 0, "", null].includes(value)) {
      return true;
    }
    return regexp.test(String(value));
  } catch {
    return false;
  }
}

// packages/dataviews/build-module/field-types/utils/is-valid-elements.mjs
function isValidElements(item, field) {
  const elements = field.elements ?? [];
  const validValues = elements.map((el) => el.value);
  if (validValues.length === 0) {
    return true;
  }
  const value = field.getValue({ item });
  return [].concat(value).every((v2) => validValues.includes(v2));
}

// packages/dataviews/build-module/field-types/utils/get-value-formatted-default.mjs
function getValueFormatted({
  item,
  field
}) {
  return field.getValue({ item });
}
var get_value_formatted_default_default = getValueFormatted;

// packages/dataviews/build-module/field-types/email.mjs
var emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
function isValidCustom(item, field) {
  const value = field.getValue({ item });
  if (![void 0, "", null].includes(value) && !emailRegex.test(value)) {
    return (0, import_i18n11.__)("Value must be a valid email address.");
  }
  return null;
}
var email_default = {
  type: "email",
  render,
  Edit: "email",
  sort: sort_text_default,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [OPERATOR_IS_ANY, OPERATOR_IS_NONE],
  validOperators: [
    OPERATOR_IS,
    OPERATOR_IS_NOT,
    OPERATOR_CONTAINS,
    OPERATOR_NOT_CONTAINS,
    OPERATOR_STARTS_WITH,
    // Multiple selection
    OPERATOR_IS_ANY,
    OPERATOR_IS_NONE,
    OPERATOR_IS_ALL,
    OPERATOR_IS_NOT_ALL
  ],
  format: {},
  getValueFormatted: get_value_formatted_default_default,
  validate: {
    required: isValidRequired,
    pattern: isValidPattern,
    minLength: isValidMinLength,
    maxLength: isValidMaxLength,
    elements: isValidElements,
    custom: isValidCustom
  }
};

// packages/dataviews/build-module/field-types/integer.mjs
var import_i18n12 = __toESM(require_i18n(), 1);

// packages/dataviews/build-module/field-types/utils/sort-number.mjs
var sort_number_default = (a2, b2, direction) => {
  return direction === "asc" ? a2 - b2 : b2 - a2;
};

// packages/dataviews/build-module/field-types/utils/is-valid-min.mjs
function isValidMin(item, field) {
  if (typeof field.isValid.min?.constraint !== "number") {
    return false;
  }
  const value = field.getValue({ item });
  if ([void 0, "", null].includes(value)) {
    return true;
  }
  return Number(value) >= field.isValid.min.constraint;
}

// packages/dataviews/build-module/field-types/utils/is-valid-max.mjs
function isValidMax(item, field) {
  if (typeof field.isValid.max?.constraint !== "number") {
    return false;
  }
  const value = field.getValue({ item });
  if ([void 0, "", null].includes(value)) {
    return true;
  }
  return Number(value) <= field.isValid.max.constraint;
}

// packages/dataviews/build-module/field-types/integer.mjs
var format2 = {
  separatorThousand: ","
};
function getValueFormatted2({
  item,
  field
}) {
  let value = field.getValue({ item });
  if (value === null || value === void 0) {
    return "";
  }
  value = Number(value);
  if (!Number.isFinite(value)) {
    return String(value);
  }
  let formatInteger;
  if (field.type !== "integer") {
    formatInteger = format2;
  } else {
    formatInteger = field.format;
  }
  const { separatorThousand } = formatInteger;
  const integerValue = Math.trunc(value);
  if (!separatorThousand) {
    return String(integerValue);
  }
  return String(integerValue).replace(
    /\B(?=(\d{3})+(?!\d))/g,
    separatorThousand
  );
}
function isValidCustom2(item, field) {
  const value = field.getValue({ item });
  if (![void 0, "", null].includes(value) && !Number.isInteger(value)) {
    return (0, import_i18n12.__)("Value must be an integer.");
  }
  return null;
}
var integer_default = {
  type: "integer",
  render,
  Edit: "integer",
  sort: sort_number_default,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [
    OPERATOR_IS,
    OPERATOR_IS_NOT,
    OPERATOR_LESS_THAN,
    OPERATOR_GREATER_THAN,
    OPERATOR_LESS_THAN_OR_EQUAL,
    OPERATOR_GREATER_THAN_OR_EQUAL,
    OPERATOR_BETWEEN
  ],
  validOperators: [
    // Single-selection
    OPERATOR_IS,
    OPERATOR_IS_NOT,
    OPERATOR_LESS_THAN,
    OPERATOR_GREATER_THAN,
    OPERATOR_LESS_THAN_OR_EQUAL,
    OPERATOR_GREATER_THAN_OR_EQUAL,
    OPERATOR_BETWEEN,
    // Multiple-selection
    OPERATOR_IS_ANY,
    OPERATOR_IS_NONE,
    OPERATOR_IS_ALL,
    OPERATOR_IS_NOT_ALL
  ],
  format: format2,
  getValueFormatted: getValueFormatted2,
  validate: {
    required: isValidRequired,
    min: isValidMin,
    max: isValidMax,
    elements: isValidElements,
    custom: isValidCustom2
  }
};

// packages/dataviews/build-module/field-types/number.mjs
var import_i18n13 = __toESM(require_i18n(), 1);
var format3 = {
  separatorThousand: ",",
  separatorDecimal: ".",
  decimals: 2
};
function getValueFormatted3({
  item,
  field
}) {
  let value = field.getValue({ item });
  if (value === null || value === void 0) {
    return "";
  }
  value = Number(value);
  if (!Number.isFinite(value)) {
    return String(value);
  }
  let formatNumber;
  if (field.type !== "number") {
    formatNumber = format3;
  } else {
    formatNumber = field.format;
  }
  const { separatorThousand, separatorDecimal, decimals } = formatNumber;
  const fixedValue = value.toFixed(decimals);
  const [integerPart, decimalPart] = fixedValue.split(".");
  const formattedInteger = separatorThousand ? integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, separatorThousand) : integerPart;
  return decimals === 0 ? formattedInteger : formattedInteger + separatorDecimal + decimalPart;
}
function isEmpty(value) {
  return value === "" || value === void 0 || value === null;
}
function isValidCustom3(item, field) {
  const value = field.getValue({ item });
  if (!isEmpty(value) && !Number.isFinite(value)) {
    return (0, import_i18n13.__)("Value must be a number.");
  }
  return null;
}
var number_default = {
  type: "number",
  render,
  Edit: "number",
  sort: sort_number_default,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [
    OPERATOR_IS,
    OPERATOR_IS_NOT,
    OPERATOR_LESS_THAN,
    OPERATOR_GREATER_THAN,
    OPERATOR_LESS_THAN_OR_EQUAL,
    OPERATOR_GREATER_THAN_OR_EQUAL,
    OPERATOR_BETWEEN
  ],
  validOperators: [
    // Single-selection
    OPERATOR_IS,
    OPERATOR_IS_NOT,
    OPERATOR_LESS_THAN,
    OPERATOR_GREATER_THAN,
    OPERATOR_LESS_THAN_OR_EQUAL,
    OPERATOR_GREATER_THAN_OR_EQUAL,
    OPERATOR_BETWEEN,
    // Multiple-selection
    OPERATOR_IS_ANY,
    OPERATOR_IS_NONE,
    OPERATOR_IS_ALL,
    OPERATOR_IS_NOT_ALL
  ],
  format: format3,
  getValueFormatted: getValueFormatted3,
  validate: {
    required: isValidRequired,
    min: isValidMin,
    max: isValidMax,
    elements: isValidElements,
    custom: isValidCustom3
  }
};

// packages/dataviews/build-module/field-types/text.mjs
var text_default = {
  type: "text",
  render,
  Edit: "text",
  sort: sort_text_default,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [OPERATOR_IS_ANY, OPERATOR_IS_NONE],
  validOperators: [
    // Single selection
    OPERATOR_IS,
    OPERATOR_IS_NOT,
    OPERATOR_CONTAINS,
    OPERATOR_NOT_CONTAINS,
    OPERATOR_STARTS_WITH,
    // Multiple selection
    OPERATOR_IS_ANY,
    OPERATOR_IS_NONE,
    OPERATOR_IS_ALL,
    OPERATOR_IS_NOT_ALL
  ],
  format: {},
  getValueFormatted: get_value_formatted_default_default,
  validate: {
    required: isValidRequired,
    pattern: isValidPattern,
    minLength: isValidMinLength,
    maxLength: isValidMaxLength,
    elements: isValidElements
  }
};

// packages/dataviews/build-module/field-types/datetime.mjs
var import_date7 = __toESM(require_date(), 1);

// packages/dataviews/build-module/field-types/utils/is-valid-date-boundary.mjs
var import_date6 = __toESM(require_date(), 1);
function parseDateLike(value) {
  if (!value) {
    return null;
  }
  if (!isValid(new Date(value))) {
    return null;
  }
  const parsed = (0, import_date6.getDate)(value);
  return parsed && isValid(parsed) ? parsed : null;
}
function validateDateLikeBoundary(item, field, boundary) {
  const constraint = field.isValid[boundary]?.constraint;
  if (typeof constraint !== "string") {
    return false;
  }
  const value = field.getValue({ item });
  const boundaryValue = Array.isArray(value) ? value[boundary === "min" ? 0 : value.length - 1] : value;
  if (boundaryValue === void 0 || boundaryValue === null || boundaryValue === "") {
    return true;
  }
  const parsedConstraint = parseDateLike(constraint);
  const parsedValue = parseDateLike(String(boundaryValue));
  return !!parsedConstraint && !!parsedValue && (boundary === "min" ? parsedValue.getTime() >= parsedConstraint.getTime() : parsedValue.getTime() <= parsedConstraint.getTime());
}
function isValidMinDate(item, field) {
  return validateDateLikeBoundary(item, field, "min");
}
function isValidMaxDate(item, field) {
  return validateDateLikeBoundary(item, field, "max");
}

// packages/dataviews/build-module/field-types/datetime.mjs
var format4 = {
  datetime: (0, import_date7.getSettings)().formats.datetime,
  weekStartsOn: (0, import_date7.getSettings)().l10n.startOfWeek
};
function getValueFormatted4({
  item,
  field
}) {
  const value = field.getValue({ item });
  if (["", void 0, null].includes(value)) {
    return "";
  }
  let formatDatetime;
  if (field.type !== "datetime") {
    formatDatetime = format4;
  } else {
    formatDatetime = field.format;
  }
  return (0, import_date7.dateI18n)(formatDatetime.datetime, (0, import_date7.getDate)(value));
}
var sort = (a2, b2, direction) => {
  const timeA = new Date(a2).getTime();
  const timeB = new Date(b2).getTime();
  return direction === "asc" ? timeA - timeB : timeB - timeA;
};
var datetime_default = {
  type: "datetime",
  render,
  Edit: "datetime",
  sort,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [
    OPERATOR_ON,
    OPERATOR_NOT_ON,
    OPERATOR_BEFORE,
    OPERATOR_AFTER,
    OPERATOR_BEFORE_INC,
    OPERATOR_AFTER_INC,
    OPERATOR_IN_THE_PAST,
    OPERATOR_OVER
  ],
  validOperators: [
    OPERATOR_ON,
    OPERATOR_NOT_ON,
    OPERATOR_BEFORE,
    OPERATOR_AFTER,
    OPERATOR_BEFORE_INC,
    OPERATOR_AFTER_INC,
    OPERATOR_IN_THE_PAST,
    OPERATOR_OVER
  ],
  format: format4,
  getValueFormatted: getValueFormatted4,
  validate: {
    required: isValidRequired,
    elements: isValidElements,
    min: isValidMinDate,
    max: isValidMaxDate
  }
};

// packages/dataviews/build-module/field-types/date.mjs
var import_date8 = __toESM(require_date(), 1);
var format5 = {
  date: (0, import_date8.getSettings)().formats.date,
  weekStartsOn: (0, import_date8.getSettings)().l10n.startOfWeek
};
function getValueFormatted5({
  item,
  field
}) {
  const value = field.getValue({ item });
  if (["", void 0, null].includes(value)) {
    return "";
  }
  let formatDate2;
  if (field.type !== "date") {
    formatDate2 = format5;
  } else {
    formatDate2 = field.format;
  }
  return (0, import_date8.dateI18n)(formatDate2.date, (0, import_date8.getDate)(value));
}
var sort2 = (a2, b2, direction) => {
  const timeA = new Date(a2).getTime();
  const timeB = new Date(b2).getTime();
  return direction === "asc" ? timeA - timeB : timeB - timeA;
};
var date_default = {
  type: "date",
  render,
  Edit: "date",
  sort: sort2,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [
    OPERATOR_ON,
    OPERATOR_NOT_ON,
    OPERATOR_BEFORE,
    OPERATOR_AFTER,
    OPERATOR_BEFORE_INC,
    OPERATOR_AFTER_INC,
    OPERATOR_IN_THE_PAST,
    OPERATOR_OVER,
    OPERATOR_BETWEEN
  ],
  validOperators: [
    OPERATOR_ON,
    OPERATOR_NOT_ON,
    OPERATOR_BEFORE,
    OPERATOR_AFTER,
    OPERATOR_BEFORE_INC,
    OPERATOR_AFTER_INC,
    OPERATOR_IN_THE_PAST,
    OPERATOR_OVER,
    OPERATOR_BETWEEN
  ],
  format: format5,
  getValueFormatted: getValueFormatted5,
  validate: {
    required: isValidRequired,
    elements: isValidElements,
    min: isValidMinDate,
    max: isValidMaxDate
  }
};

// packages/dataviews/build-module/field-types/boolean.mjs
var import_i18n14 = __toESM(require_i18n(), 1);

// packages/dataviews/build-module/field-types/utils/is-valid-required-for-bool.mjs
function isValidRequiredForBool(item, field) {
  const value = field.getValue({ item });
  return value === true;
}

// packages/dataviews/build-module/field-types/boolean.mjs
function getValueFormatted6({
  item,
  field
}) {
  const value = field.getValue({ item });
  if (value === true) {
    return (0, import_i18n14.__)("True");
  }
  if (value === false) {
    return (0, import_i18n14.__)("False");
  }
  return "";
}
function isValidCustom4(item, field) {
  const value = field.getValue({ item });
  if (![void 0, "", null].includes(value) && ![true, false].includes(value)) {
    return (0, import_i18n14.__)("Value must be true, false, or undefined");
  }
  return null;
}
var sort3 = (a2, b2, direction) => {
  const boolA = Boolean(a2);
  const boolB = Boolean(b2);
  if (boolA === boolB) {
    return 0;
  }
  if (direction === "asc") {
    return boolA ? 1 : -1;
  }
  return boolA ? -1 : 1;
};
var boolean_default = {
  type: "boolean",
  render,
  Edit: "checkbox",
  sort: sort3,
  validate: {
    required: isValidRequiredForBool,
    elements: isValidElements,
    custom: isValidCustom4
  },
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [OPERATOR_IS, OPERATOR_IS_NOT],
  validOperators: [OPERATOR_IS, OPERATOR_IS_NOT],
  format: {},
  getValueFormatted: getValueFormatted6
};

// packages/dataviews/build-module/field-types/media.mjs
var media_default = {
  type: "media",
  render: () => null,
  Edit: null,
  sort: () => 0,
  enableSorting: false,
  enableGlobalSearch: false,
  defaultOperators: [],
  validOperators: [],
  format: {},
  getValueFormatted: get_value_formatted_default_default,
  // cannot validate any constraint, so
  // the only available validation for the field author
  // would be providing a custom validator.
  validate: {}
};

// packages/dataviews/build-module/field-types/array.mjs
var import_i18n15 = __toESM(require_i18n(), 1);

// packages/dataviews/build-module/field-types/utils/is-valid-required-for-array.mjs
function isValidRequiredForArray(item, field) {
  const value = field.getValue({ item });
  return Array.isArray(value) && value.length > 0 && value.every(
    (element) => ![void 0, "", null].includes(element)
  );
}

// packages/dataviews/build-module/field-types/array.mjs
function getValueFormatted7({
  item,
  field
}) {
  const value = field.getValue({ item });
  const arr = Array.isArray(value) ? value : [];
  return arr.join(", ");
}
function render2({ item, field }) {
  return getValueFormatted7({ item, field });
}
function isValidCustom5(item, field) {
  const value = field.getValue({ item });
  if (![void 0, "", null].includes(value) && !Array.isArray(value)) {
    return (0, import_i18n15.__)("Value must be an array.");
  }
  if (!value.every((v2) => typeof v2 === "string")) {
    return (0, import_i18n15.__)("Every value must be a string.");
  }
  return null;
}
var sort4 = (a2, b2, direction) => {
  const arrA = Array.isArray(a2) ? a2 : [];
  const arrB = Array.isArray(b2) ? b2 : [];
  if (arrA.length !== arrB.length) {
    return direction === "asc" ? arrA.length - arrB.length : arrB.length - arrA.length;
  }
  const joinedA = arrA.join(",");
  const joinedB = arrB.join(",");
  return direction === "asc" ? joinedA.localeCompare(joinedB) : joinedB.localeCompare(joinedA);
};
var array_default = {
  type: "array",
  render: render2,
  Edit: "array",
  sort: sort4,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [OPERATOR_IS_ANY, OPERATOR_IS_NONE],
  validOperators: [
    OPERATOR_IS_ANY,
    OPERATOR_IS_NONE,
    OPERATOR_IS_ALL,
    OPERATOR_IS_NOT_ALL
  ],
  format: {},
  getValueFormatted: getValueFormatted7,
  validate: {
    required: isValidRequiredForArray,
    elements: isValidElements,
    custom: isValidCustom5
  }
};

// packages/dataviews/build-module/field-types/password.mjs
function getValueFormatted8({
  item,
  field
}) {
  return field.getValue({ item }) ? "\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022" : "";
}
var password_default = {
  type: "password",
  render,
  Edit: "password",
  sort: () => 0,
  // Passwords should not be sortable for security reasons
  enableSorting: false,
  enableGlobalSearch: false,
  defaultOperators: [],
  validOperators: [],
  format: {},
  getValueFormatted: getValueFormatted8,
  validate: {
    required: isValidRequired,
    pattern: isValidPattern,
    minLength: isValidMinLength,
    maxLength: isValidMaxLength,
    elements: isValidElements
  }
};

// packages/dataviews/build-module/field-types/telephone.mjs
var telephone_default = {
  type: "telephone",
  render,
  Edit: "telephone",
  sort: sort_text_default,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [OPERATOR_IS_ANY, OPERATOR_IS_NONE],
  validOperators: [
    OPERATOR_IS,
    OPERATOR_IS_NOT,
    OPERATOR_CONTAINS,
    OPERATOR_NOT_CONTAINS,
    OPERATOR_STARTS_WITH,
    // Multiple selection
    OPERATOR_IS_ANY,
    OPERATOR_IS_NONE,
    OPERATOR_IS_ALL,
    OPERATOR_IS_NOT_ALL
  ],
  format: {},
  getValueFormatted: get_value_formatted_default_default,
  validate: {
    required: isValidRequired,
    pattern: isValidPattern,
    minLength: isValidMinLength,
    maxLength: isValidMaxLength,
    elements: isValidElements
  }
};

// packages/dataviews/build-module/field-types/color.mjs
var import_i18n16 = __toESM(require_i18n(), 1);
var import_jsx_runtime51 = __toESM(require_jsx_runtime(), 1);
function render3({ item, field }) {
  if (field.hasElements) {
    return /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(RenderFromElements, { item, field });
  }
  const value = get_value_formatted_default_default({ item, field });
  if (!value || !w(value).isValid()) {
    return value;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime51.jsxs)("div", { style: { display: "flex", alignItems: "center", gap: "8px" }, children: [
    /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(
      "div",
      {
        style: {
          width: "16px",
          height: "16px",
          borderRadius: "50%",
          backgroundColor: value,
          border: "1px solid #ddd",
          flexShrink: 0
        }
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime51.jsx)("span", { children: value })
  ] });
}
function isValidCustom6(item, field) {
  const value = field.getValue({ item });
  if (![void 0, "", null].includes(value) && !w(value).isValid()) {
    return (0, import_i18n16.__)("Value must be a valid color.");
  }
  return null;
}
var sort5 = (a2, b2, direction) => {
  const colorA = w(a2);
  const colorB = w(b2);
  if (!colorA.isValid() && !colorB.isValid()) {
    return 0;
  }
  if (!colorA.isValid()) {
    return direction === "asc" ? 1 : -1;
  }
  if (!colorB.isValid()) {
    return direction === "asc" ? -1 : 1;
  }
  const hslA = colorA.toHsl();
  const hslB = colorB.toHsl();
  if (hslA.h !== hslB.h) {
    return direction === "asc" ? hslA.h - hslB.h : hslB.h - hslA.h;
  }
  if (hslA.s !== hslB.s) {
    return direction === "asc" ? hslA.s - hslB.s : hslB.s - hslA.s;
  }
  return direction === "asc" ? hslA.l - hslB.l : hslB.l - hslA.l;
};
var color_default = {
  type: "color",
  render: render3,
  Edit: "color",
  sort: sort5,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [OPERATOR_IS_ANY, OPERATOR_IS_NONE],
  validOperators: [
    OPERATOR_IS,
    OPERATOR_IS_NOT,
    OPERATOR_IS_ANY,
    OPERATOR_IS_NONE
  ],
  format: {},
  getValueFormatted: get_value_formatted_default_default,
  validate: {
    required: isValidRequired,
    elements: isValidElements,
    custom: isValidCustom6
  }
};

// packages/dataviews/build-module/field-types/url.mjs
var url_default = {
  type: "url",
  render,
  Edit: "url",
  sort: sort_text_default,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [OPERATOR_IS_ANY, OPERATOR_IS_NONE],
  validOperators: [
    OPERATOR_IS,
    OPERATOR_IS_NOT,
    OPERATOR_CONTAINS,
    OPERATOR_NOT_CONTAINS,
    OPERATOR_STARTS_WITH,
    // Multiple selection
    OPERATOR_IS_ANY,
    OPERATOR_IS_NONE,
    OPERATOR_IS_ALL,
    OPERATOR_IS_NOT_ALL
  ],
  format: {},
  getValueFormatted: get_value_formatted_default_default,
  validate: {
    required: isValidRequired,
    pattern: isValidPattern,
    minLength: isValidMinLength,
    maxLength: isValidMaxLength,
    elements: isValidElements
  }
};

// packages/dataviews/build-module/field-types/no-type.mjs
var sort6 = (a2, b2, direction) => {
  if (typeof a2 === "number" && typeof b2 === "number") {
    return sort_number_default(a2, b2, direction);
  }
  return sort_text_default(a2, b2, direction);
};
var no_type_default = {
  // type: no type for this one
  render,
  Edit: null,
  sort: sort6,
  enableSorting: true,
  enableGlobalSearch: false,
  defaultOperators: [OPERATOR_IS, OPERATOR_IS_NOT],
  validOperators: getAllOperatorNames(),
  format: {},
  getValueFormatted: get_value_formatted_default_default,
  validate: {
    required: isValidRequired,
    elements: isValidElements
  }
};

// packages/dataviews/build-module/field-types/utils/get-is-valid.mjs
function supportsNumericRangeConstraint(type) {
  return type === "integer" || type === "number";
}
function supportsDateRangeConstraint(type) {
  return type === "date" || type === "datetime";
}
function normalizeRangeRule(value, fieldType, key) {
  const validator = fieldType.validate[key];
  if (validator && (typeof value === "number" && supportsNumericRangeConstraint(fieldType.type) || typeof value === "string" && supportsDateRangeConstraint(fieldType.type))) {
    return { constraint: value, validate: validator };
  }
  return void 0;
}
function getIsValid(field, fieldType) {
  const rules = field.isValid;
  let required;
  if (rules?.required === true && fieldType.validate.required !== void 0) {
    required = {
      constraint: true,
      validate: fieldType.validate.required
    };
  }
  let elements;
  if ((rules?.elements === true || // elements is enabled unless the field opts-out
  rules?.elements === void 0 && (!!field.elements || !!field.getElements)) && fieldType.validate.elements !== void 0) {
    elements = {
      constraint: true,
      validate: fieldType.validate.elements
    };
  }
  const min = normalizeRangeRule(rules?.min, fieldType, "min");
  const max = normalizeRangeRule(rules?.max, fieldType, "max");
  const minLengthValue = rules?.minLength;
  let minLength;
  if (typeof minLengthValue === "number" && fieldType.validate.minLength !== void 0) {
    minLength = {
      constraint: minLengthValue,
      validate: fieldType.validate.minLength
    };
  }
  const maxLengthValue = rules?.maxLength;
  let maxLength;
  if (typeof maxLengthValue === "number" && fieldType.validate.maxLength !== void 0) {
    maxLength = {
      constraint: maxLengthValue,
      validate: fieldType.validate.maxLength
    };
  }
  const patternValue = rules?.pattern;
  let pattern;
  if (patternValue !== void 0 && fieldType.validate.pattern !== void 0) {
    pattern = {
      constraint: patternValue,
      validate: fieldType.validate.pattern
    };
  }
  const custom = rules?.custom ?? fieldType.validate.custom;
  return {
    required,
    elements,
    min,
    max,
    minLength,
    maxLength,
    pattern,
    custom
  };
}

// packages/dataviews/build-module/field-types/utils/get-filter.mjs
function getFilter(fieldType) {
  return fieldType.validOperators.reduce((accumulator, operator) => {
    const operatorObj = getOperatorByName(operator);
    if (operatorObj?.filter) {
      accumulator[operator] = operatorObj.filter;
    }
    return accumulator;
  }, {});
}

// packages/dataviews/build-module/field-types/utils/get-format.mjs
function getFormat(field, fieldType) {
  return {
    ...fieldType.format,
    ...field.format
  };
}
var get_format_default = getFormat;

// packages/dataviews/build-module/field-types/index.mjs
function getFieldTypeByName(type) {
  const found = [
    email_default,
    integer_default,
    number_default,
    text_default,
    datetime_default,
    date_default,
    boolean_default,
    media_default,
    array_default,
    password_default,
    telephone_default,
    color_default,
    url_default
  ].find((fieldType) => fieldType?.type === type);
  if (!!found) {
    return found;
  }
  return no_type_default;
}
function normalizeFields(fields) {
  return fields.map((field) => {
    const fieldType = getFieldTypeByName(field.type);
    const getValue = field.getValue || get_value_from_id_default(field.id);
    const sort7 = function(a2, b2, direction) {
      const aValue = getValue({ item: a2 });
      const bValue = getValue({ item: b2 });
      return field.sort ? field.sort(aValue, bValue, direction) : fieldType.sort(aValue, bValue, direction);
    };
    return {
      id: field.id,
      label: field.label || field.id,
      header: field.header || field.label || field.id,
      description: field.description,
      placeholder: field.placeholder,
      getValue,
      setValue: field.setValue || set_value_from_id_default(field.id),
      elements: field.elements,
      getElements: field.getElements,
      hasElements: hasElements(field),
      isVisible: field.isVisible,
      isDisabled: typeof field.isDisabled === "function" ? field.isDisabled : () => !!field.isDisabled,
      enableHiding: field.enableHiding ?? true,
      readOnly: field.readOnly ?? false,
      // The type provides defaults for the following props
      type: fieldType.type,
      render: field.render ?? fieldType.render,
      Edit: getControl(field, fieldType.Edit),
      sort: sort7,
      enableSorting: field.enableSorting ?? fieldType.enableSorting,
      enableGlobalSearch: field.enableGlobalSearch ?? fieldType.enableGlobalSearch,
      isValid: getIsValid(field, fieldType),
      filterBy: get_filter_by_default(
        field,
        fieldType.defaultOperators,
        fieldType.validOperators
      ),
      filter: getFilter(fieldType),
      format: get_format_default(field, fieldType),
      getValueFormatted: field.getValueFormatted ?? fieldType.getValueFormatted
    };
  });
}

// packages/dataviews/build-module/dataform/index.mjs
var import_element50 = __toESM(require_element(), 1);

// packages/dataviews/build-module/components/dataform-context/index.mjs
var import_element39 = __toESM(require_element(), 1);
var import_jsx_runtime52 = __toESM(require_jsx_runtime(), 1);
var DataFormContext = (0, import_element39.createContext)({
  fields: []
});
DataFormContext.displayName = "DataFormContext";
function DataFormProvider({
  fields,
  children
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime52.jsx)(DataFormContext.Provider, { value: { fields }, children });
}
var dataform_context_default = DataFormContext;

// packages/dataviews/build-module/components/dataform-layouts/data-form-layout.mjs
var import_element49 = __toESM(require_element(), 1);

// packages/dataviews/build-module/components/dataform-layouts/regular/index.mjs
var import_element40 = __toESM(require_element(), 1);
var import_components20 = __toESM(require_components(), 1);

// packages/dataviews/build-module/components/dataform-layouts/normalize-form.mjs
var import_i18n17 = __toESM(require_i18n(), 1);
var DEFAULT_LAYOUT = {
  type: "regular",
  labelPosition: "top"
};
var normalizeCardSummaryField = (sum) => {
  if (typeof sum === "string") {
    return [{ id: sum, visibility: "when-collapsed" }];
  }
  return sum.map((item) => {
    if (typeof item === "string") {
      return { id: item, visibility: "when-collapsed" };
    }
    return { id: item.id, visibility: item.visibility };
  });
};
function normalizeLayout(layout) {
  let normalizedLayout = DEFAULT_LAYOUT;
  if (layout?.type === "regular") {
    normalizedLayout = {
      type: "regular",
      labelPosition: layout?.labelPosition ?? "top"
    };
  } else if (layout?.type === "panel") {
    const summary = layout.summary ?? [];
    const normalizedSummary = Array.isArray(summary) ? summary : [summary];
    const openAs = layout?.openAs;
    let normalizedOpenAs;
    if (typeof openAs === "object" && openAs.type === "modal") {
      normalizedOpenAs = {
        type: "modal",
        applyLabel: openAs.applyLabel?.trim() || (0, import_i18n17.__)("Apply"),
        cancelLabel: openAs.cancelLabel?.trim() || (0, import_i18n17.__)("Cancel")
      };
    } else if (openAs === "modal") {
      normalizedOpenAs = {
        type: "modal",
        applyLabel: (0, import_i18n17.__)("Apply"),
        cancelLabel: (0, import_i18n17.__)("Cancel")
      };
    } else {
      normalizedOpenAs = { type: "dropdown" };
    }
    normalizedLayout = {
      type: "panel",
      labelPosition: layout?.labelPosition ?? "side",
      openAs: normalizedOpenAs,
      summary: normalizedSummary,
      editVisibility: layout?.editVisibility ?? "on-hover"
    };
  } else if (layout?.type === "card") {
    if (layout.withHeader === false) {
      normalizedLayout = {
        type: "card",
        withHeader: false,
        isOpened: true,
        summary: [],
        isCollapsible: false
      };
    } else {
      const summary = layout.summary ?? [];
      normalizedLayout = {
        type: "card",
        withHeader: true,
        isOpened: typeof layout.isOpened === "boolean" ? layout.isOpened : true,
        summary: normalizeCardSummaryField(summary),
        isCollapsible: layout.isCollapsible === void 0 ? true : layout.isCollapsible
      };
    }
  } else if (layout?.type === "row") {
    normalizedLayout = {
      type: "row",
      alignment: layout?.alignment ?? "center",
      styles: layout?.styles ?? {}
    };
  } else if (layout?.type === "details") {
    normalizedLayout = {
      type: "details",
      summary: layout?.summary ?? ""
    };
  }
  return normalizedLayout;
}
function normalizeForm(form) {
  const normalizedFormLayout = normalizeLayout(form?.layout);
  const normalizedFields = (form.fields ?? []).map(
    (field) => {
      if (typeof field === "string") {
        return {
          id: field,
          layout: normalizedFormLayout
        };
      }
      const fieldLayout = field.layout ? normalizeLayout(field.layout) : normalizedFormLayout;
      return {
        id: field.id,
        layout: fieldLayout,
        ...!!field.label && { label: field.label },
        ...!!field.description && {
          description: field.description
        },
        ..."children" in field && Array.isArray(field.children) && {
          children: normalizeForm({
            fields: field.children,
            layout: DEFAULT_LAYOUT
          }).fields
        }
      };
    }
  );
  return {
    layout: normalizedFormLayout,
    fields: normalizedFields
  };
}
var normalize_form_default = normalizeForm;

// packages/dataviews/build-module/components/dataform-layouts/regular/index.mjs
var import_jsx_runtime53 = __toESM(require_jsx_runtime(), 1);
function Header4({ title }) {
  return /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
    Stack,
    {
      direction: "column",
      className: "dataforms-layouts-regular__header",
      gap: "lg",
      children: /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(Stack, { direction: "row", align: "center", children: /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(import_components20.__experimentalHeading, { level: 2, size: 13, children: title }) })
    }
  );
}
function FormRegularField({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const { fields } = (0, import_element40.useContext)(dataform_context_default);
  const layout = field.layout;
  const form = (0, import_element40.useMemo)(
    () => ({
      layout: DEFAULT_LAYOUT,
      fields: !!field.children ? field.children : []
    }),
    [field]
  );
  if (!!field.children) {
    return /* @__PURE__ */ (0, import_jsx_runtime53.jsxs)(import_jsx_runtime53.Fragment, { children: [
      !hideLabelFromVision && field.label && /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(Header4, { title: field.label }),
      /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
        DataFormLayout,
        {
          data,
          form,
          onChange,
          validity: validity?.children
        }
      )
    ] });
  }
  const labelPosition = layout.labelPosition;
  const fieldDefinition = fields.find(
    (fieldDef) => fieldDef.id === field.id
  );
  if (!fieldDefinition || !fieldDefinition.Edit) {
    return null;
  }
  if (labelPosition === "side") {
    return /* @__PURE__ */ (0, import_jsx_runtime53.jsxs)(
      Stack,
      {
        direction: "row",
        className: "dataforms-layouts-regular__field",
        gap: "sm",
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
            "div",
            {
              className: clsx_default(
                "dataforms-layouts-regular__field-label",
                `dataforms-layouts-regular__field-label--label-position-${labelPosition}`
              ),
              children: /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(import_components20.BaseControl.VisualLabel, { children: fieldDefinition.label })
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime53.jsx)("div", { className: "dataforms-layouts-regular__field-control", children: fieldDefinition.readOnly === true ? /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
            fieldDefinition.render,
            {
              item: data,
              field: fieldDefinition
            }
          ) : /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
            fieldDefinition.Edit,
            {
              data,
              field: fieldDefinition,
              onChange,
              hideLabelFromVision: true,
              markWhenOptional,
              validity
            },
            fieldDefinition.id
          ) })
        ]
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime53.jsx)("div", { className: "dataforms-layouts-regular__field", children: fieldDefinition.readOnly === true ? /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(import_jsx_runtime53.Fragment, { children: /* @__PURE__ */ (0, import_jsx_runtime53.jsxs)(import_jsx_runtime53.Fragment, { children: [
    !hideLabelFromVision && labelPosition !== "none" && /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(import_components20.BaseControl.VisualLabel, { children: fieldDefinition.label }),
    /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
      fieldDefinition.render,
      {
        item: data,
        field: fieldDefinition
      }
    )
  ] }) }) : /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
    fieldDefinition.Edit,
    {
      data,
      field: fieldDefinition,
      onChange,
      hideLabelFromVision: labelPosition === "none" ? true : hideLabelFromVision,
      markWhenOptional,
      validity
    }
  ) });
}

// packages/dataviews/build-module/components/dataform-layouts/panel/modal.mjs
var import_deepmerge2 = __toESM(require_cjs(), 1);
var import_components23 = __toESM(require_components(), 1);
var import_element45 = __toESM(require_element(), 1);
var import_compose2 = __toESM(require_compose(), 1);

// packages/dataviews/build-module/components/dataform-layouts/panel/summary-button.mjs
var import_components22 = __toESM(require_components(), 1);
var import_i18n18 = __toESM(require_i18n(), 1);
var import_compose = __toESM(require_compose(), 1);
var import_element41 = __toESM(require_element(), 1);

// packages/dataviews/build-module/components/dataform-layouts/panel/utils/get-label-classname.mjs
function getLabelClassName(labelPosition, showError) {
  return clsx_default(
    "dataforms-layouts-panel__field-label",
    `dataforms-layouts-panel__field-label--label-position-${labelPosition}`,
    { "has-error": showError }
  );
}
var get_label_classname_default = getLabelClassName;

// packages/dataviews/build-module/components/dataform-layouts/panel/utils/get-label-content.mjs
var import_components21 = __toESM(require_components(), 1);
var import_jsx_runtime54 = __toESM(require_jsx_runtime(), 1);
function getLabelContent(showError, errorMessage, fieldLabel) {
  return showError ? /* @__PURE__ */ (0, import_jsx_runtime54.jsx)(import_components21.Tooltip, { text: errorMessage, placement: "top", children: /* @__PURE__ */ (0, import_jsx_runtime54.jsxs)("span", { className: "dataforms-layouts-panel__field-label-error-content", children: [
    /* @__PURE__ */ (0, import_jsx_runtime54.jsx)(import_components21.Icon, { icon: error_default, size: 16 }),
    fieldLabel
  ] }) }) : fieldLabel;
}
var get_label_content_default = getLabelContent;

// packages/dataviews/build-module/components/dataform-layouts/panel/utils/get-first-validation-error.mjs
function getFirstValidationError(validity) {
  if (!validity) {
    return void 0;
  }
  const validityRules = Object.keys(validity).filter(
    (key) => key !== "children"
  );
  for (const key of validityRules) {
    const rule = validity[key];
    if (rule === void 0) {
      continue;
    }
    if (rule.type === "invalid") {
      if (rule.message) {
        return rule.message;
      }
      if (key === "required") {
        return "A required field is empty";
      }
      return "Unidentified validation error";
    }
  }
  if (validity.children) {
    for (const childValidity of Object.values(validity.children)) {
      const childError = getFirstValidationError(childValidity);
      if (childError) {
        return childError;
      }
    }
  }
  return void 0;
}
var get_first_validation_error_default = getFirstValidationError;

// packages/dataviews/build-module/components/dataform-layouts/panel/summary-button.mjs
var import_jsx_runtime55 = __toESM(require_jsx_runtime(), 1);
function SummaryButton({
  data,
  field,
  fieldLabel,
  summaryFields,
  validity,
  touched,
  disabled: disabled2,
  onClick,
  "aria-expanded": ariaExpanded
}) {
  const { labelPosition, editVisibility } = field.layout;
  const errorMessage = get_first_validation_error_default(validity);
  const showError = touched && !!errorMessage;
  const labelClassName = get_label_classname_default(labelPosition, showError);
  const labelContent = get_label_content_default(showError, errorMessage, fieldLabel);
  const className = clsx_default(
    "dataforms-layouts-panel__field-trigger",
    `dataforms-layouts-panel__field-trigger--label-${labelPosition}`,
    {
      "is-disabled": disabled2,
      "dataforms-layouts-panel__field-trigger--edit-always": editVisibility === "always"
    }
  );
  const controlId = (0, import_compose.useInstanceId)(
    SummaryButton,
    "dataforms-layouts-panel__field-control"
  );
  const ariaLabel = showError ? (0, import_i18n18.sprintf)(
    // translators: %s: Field name.
    (0, import_i18n18._x)("Edit %s (has errors)", "field"),
    fieldLabel || ""
  ) : (0, import_i18n18.sprintf)(
    // translators: %s: Field name.
    (0, import_i18n18._x)("Edit %s", "field"),
    fieldLabel || ""
  );
  const rowRef = (0, import_element41.useRef)(null);
  const handleRowClick = () => {
    const selection = rowRef.current?.ownerDocument.defaultView?.getSelection();
    if (selection && selection.toString().length > 0) {
      return;
    }
    onClick();
  };
  const handleKeyDown = (event) => {
    if (event.target === event.currentTarget && (event.key === "Enter" || event.key === " ")) {
      event.preventDefault();
      onClick();
    }
  };
  return /* @__PURE__ */ (0, import_jsx_runtime55.jsxs)(
    "div",
    {
      ref: rowRef,
      className,
      onClick: !disabled2 ? handleRowClick : void 0,
      onKeyDown: !disabled2 ? handleKeyDown : void 0,
      children: [
        labelPosition !== "none" && /* @__PURE__ */ (0, import_jsx_runtime55.jsx)("span", { className: labelClassName, children: labelContent }),
        labelPosition === "none" && showError && /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(import_components22.Tooltip, { text: errorMessage, placement: "top", children: /* @__PURE__ */ (0, import_jsx_runtime55.jsx)("span", { className: "dataforms-layouts-panel__field-label-error-content", children: /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(import_components22.Icon, { icon: error_default, size: 16 }) }) }),
        /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
          "span",
          {
            id: `${controlId}`,
            className: "dataforms-layouts-panel__field-control",
            children: summaryFields.length > 1 ? /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
              "span",
              {
                style: {
                  display: "flex",
                  flexDirection: "column",
                  alignItems: "flex-start",
                  width: "100%",
                  gap: "2px"
                },
                children: summaryFields.map((summaryField) => /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
                  "span",
                  {
                    style: { width: "100%" },
                    children: /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
                      summaryField.render,
                      {
                        item: data,
                        field: summaryField
                      }
                    )
                  },
                  summaryField.id
                ))
              }
            ) : summaryFields.map((summaryField) => /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
              summaryField.render,
              {
                item: data,
                field: summaryField
              },
              summaryField.id
            ))
          }
        ),
        !disabled2 && /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
          import_components22.Button,
          {
            className: "dataforms-layouts-panel__field-trigger-icon",
            label: ariaLabel,
            icon: pencil_default,
            size: "small",
            "aria-expanded": ariaExpanded,
            "aria-haspopup": "dialog",
            "aria-describedby": `${controlId}`
          }
        )
      ]
    }
  );
}

// packages/dataviews/build-module/hooks/use-form-validity.mjs
var import_deepmerge = __toESM(require_cjs(), 1);
var import_es6 = __toESM(require_es6(), 1);
var import_element42 = __toESM(require_element(), 1);
var import_i18n19 = __toESM(require_i18n(), 1);
function isFormValid(formValidity) {
  if (!formValidity) {
    return true;
  }
  return Object.values(formValidity).every((fieldValidation) => {
    return Object.entries(fieldValidation).every(
      ([key, validation]) => {
        if (key === "children" && validation && typeof validation === "object") {
          return isFormValid(validation);
        }
        return validation.type !== "invalid" && validation.type !== "validating";
      }
    );
  });
}
function getFormFieldsToValidate(form, fields) {
  const normalizedForm = normalize_form_default(form);
  if (normalizedForm.fields.length === 0) {
    return [];
  }
  const fieldsMap = /* @__PURE__ */ new Map();
  fields.forEach((field) => {
    fieldsMap.set(field.id, field);
  });
  function processFormField(formField) {
    if ("children" in formField && Array.isArray(formField.children)) {
      const processedChildren = formField.children.map(processFormField).filter((child) => child !== null);
      if (processedChildren.length === 0) {
        return null;
      }
      const fieldDef2 = fieldsMap.get(formField.id);
      if (fieldDef2) {
        const [normalizedField2] = normalizeFields([
          fieldDef2
        ]);
        return {
          id: formField.id,
          children: processedChildren,
          field: normalizedField2
        };
      }
      return {
        id: formField.id,
        children: processedChildren
      };
    }
    const fieldDef = fieldsMap.get(formField.id);
    if (!fieldDef) {
      return null;
    }
    const [normalizedField] = normalizeFields([fieldDef]);
    return {
      id: formField.id,
      children: [],
      field: normalizedField
    };
  }
  const toValidate = normalizedForm.fields.map(processFormField).filter((field) => field !== null);
  return toValidate;
}
function setValidityAtPath(formValidity, fieldValidity, path) {
  if (!formValidity) {
    formValidity = {};
  }
  if (path.length === 0) {
    return formValidity;
  }
  const result = { ...formValidity };
  let current = result;
  for (let i2 = 0; i2 < path.length - 1; i2++) {
    const segment = path[i2];
    if (!current[segment]) {
      current[segment] = {};
    }
    current[segment] = { ...current[segment] };
    current = current[segment];
  }
  const finalKey = path[path.length - 1];
  current[finalKey] = {
    ...current[finalKey] || {},
    ...fieldValidity
  };
  return result;
}
function removeValidationProperty(formValidity, path, property) {
  if (!formValidity || path.length === 0) {
    return formValidity;
  }
  const result = { ...formValidity };
  let current = result;
  for (let i2 = 0; i2 < path.length - 1; i2++) {
    const segment = path[i2];
    if (!current[segment]) {
      return formValidity;
    }
    current[segment] = { ...current[segment] };
    current = current[segment];
  }
  const finalKey = path[path.length - 1];
  if (!current[finalKey]) {
    return formValidity;
  }
  const fieldValidity = { ...current[finalKey] };
  delete fieldValidity[property];
  if (Object.keys(fieldValidity).length === 0) {
    delete current[finalKey];
  } else {
    current[finalKey] = fieldValidity;
  }
  if (Object.keys(result).length === 0) {
    return void 0;
  }
  return result;
}
function handleElementsValidationAsync(promise, formField, promiseHandler) {
  const { elementsCounterRef, setFormValidity, path, item } = promiseHandler;
  const currentToken = (elementsCounterRef.current[formField.id] || 0) + 1;
  elementsCounterRef.current[formField.id] = currentToken;
  promise.then((result) => {
    if (currentToken !== elementsCounterRef.current[formField.id]) {
      return;
    }
    if (!Array.isArray(result)) {
      setFormValidity((prev) => {
        const newFormValidity = setValidityAtPath(
          prev,
          {
            elements: {
              type: "invalid",
              message: (0, import_i18n19.__)("Could not validate elements.")
            }
          },
          [...path, formField.id]
        );
        return newFormValidity;
      });
      return;
    }
    if (formField.field?.isValid.elements && !formField.field.isValid.elements.validate(item, {
      ...formField.field,
      elements: result
    })) {
      setFormValidity((prev) => {
        const newFormValidity = setValidityAtPath(
          prev,
          {
            elements: {
              type: "invalid",
              message: (0, import_i18n19.__)(
                "Value must be one of the elements."
              )
            }
          },
          [...path, formField.id]
        );
        return newFormValidity;
      });
    } else {
      setFormValidity((prev) => {
        return removeValidationProperty(
          prev,
          [...path, formField.id],
          "elements"
        );
      });
    }
  }).catch((error2) => {
    if (currentToken !== elementsCounterRef.current[formField.id]) {
      return;
    }
    let errorMessage;
    if (error2 instanceof Error) {
      errorMessage = error2.message;
    } else {
      errorMessage = String(error2) || (0, import_i18n19.__)(
        "Unknown error when running elements validation asynchronously."
      );
    }
    setFormValidity((prev) => {
      const newFormValidity = setValidityAtPath(
        prev,
        {
          elements: {
            type: "invalid",
            message: errorMessage
          }
        },
        [...path, formField.id]
      );
      return newFormValidity;
    });
  });
}
function handleCustomValidationAsync(promise, formField, promiseHandler) {
  const { customCounterRef, setFormValidity, path } = promiseHandler;
  const currentToken = (customCounterRef.current[formField.id] || 0) + 1;
  customCounterRef.current[formField.id] = currentToken;
  promise.then((result) => {
    if (currentToken !== customCounterRef.current[formField.id]) {
      return;
    }
    if (result === null) {
      setFormValidity((prev) => {
        return removeValidationProperty(
          prev,
          [...path, formField.id],
          "custom"
        );
      });
      return;
    }
    if (typeof result === "string") {
      setFormValidity((prev) => {
        const newFormValidity = setValidityAtPath(
          prev,
          {
            custom: {
              type: "invalid",
              message: result
            }
          },
          [...path, formField.id]
        );
        return newFormValidity;
      });
      return;
    }
    setFormValidity((prev) => {
      const newFormValidity = setValidityAtPath(
        prev,
        {
          custom: {
            type: "invalid",
            message: (0, import_i18n19.__)("Validation could not be processed.")
          }
        },
        [...path, formField.id]
      );
      return newFormValidity;
    });
  }).catch((error2) => {
    if (currentToken !== customCounterRef.current[formField.id]) {
      return;
    }
    let errorMessage;
    if (error2 instanceof Error) {
      errorMessage = error2.message;
    } else {
      errorMessage = String(error2) || (0, import_i18n19.__)(
        "Unknown error when running custom validation asynchronously."
      );
    }
    setFormValidity((prev) => {
      const newFormValidity = setValidityAtPath(
        prev,
        {
          custom: {
            type: "invalid",
            message: errorMessage
          }
        },
        [...path, formField.id]
      );
      return newFormValidity;
    });
  });
}
function validateFormField(item, formField, promiseHandler) {
  if (formField.field?.isValid.required && !formField.field.isValid.required.validate(item, formField.field)) {
    return {
      required: { type: "invalid" }
    };
  }
  if (formField.field?.isValid.pattern && !formField.field.isValid.pattern.validate(item, formField.field)) {
    return {
      pattern: {
        type: "invalid",
        message: (0, import_i18n19.__)("Value does not match the required pattern.")
      }
    };
  }
  if (formField.field?.isValid.min && !formField.field.isValid.min.validate(item, formField.field)) {
    return {
      min: {
        type: "invalid",
        message: (0, import_i18n19.__)("Value is below the minimum.")
      }
    };
  }
  if (formField.field?.isValid.max && !formField.field.isValid.max.validate(item, formField.field)) {
    return {
      max: {
        type: "invalid",
        message: (0, import_i18n19.__)("Value is above the maximum.")
      }
    };
  }
  if (formField.field?.isValid.minLength && !formField.field.isValid.minLength.validate(item, formField.field)) {
    return {
      minLength: {
        type: "invalid",
        message: (0, import_i18n19.__)("Value is too short.")
      }
    };
  }
  if (formField.field?.isValid.maxLength && !formField.field.isValid.maxLength.validate(item, formField.field)) {
    return {
      maxLength: {
        type: "invalid",
        message: (0, import_i18n19.__)("Value is too long.")
      }
    };
  }
  if (formField.field?.isValid.elements && formField.field.hasElements && !formField.field.getElements && Array.isArray(formField.field.elements) && !formField.field.isValid.elements.validate(item, formField.field)) {
    return {
      elements: {
        type: "invalid",
        message: (0, import_i18n19.__)("Value must be one of the elements.")
      }
    };
  }
  let customError;
  if (!!formField.field && formField.field.isValid.custom) {
    try {
      const value = formField.field.getValue({ item });
      customError = formField.field.isValid.custom(
        (0, import_deepmerge.default)(
          item,
          formField.field.setValue({
            item,
            value
          })
        ),
        formField.field
      );
    } catch (error2) {
      let errorMessage;
      if (error2 instanceof Error) {
        errorMessage = error2.message;
      } else {
        errorMessage = String(error2) || (0, import_i18n19.__)("Unknown error when running custom validation.");
      }
      return {
        custom: {
          type: "invalid",
          message: errorMessage
        }
      };
    }
  }
  if (typeof customError === "string") {
    return {
      custom: {
        type: "invalid",
        message: customError
      }
    };
  }
  const fieldValidity = {};
  if (!!formField.field && formField.field.isValid.elements && formField.field.hasElements && typeof formField.field.getElements === "function") {
    handleElementsValidationAsync(
      formField.field.getElements(),
      formField,
      promiseHandler
    );
    fieldValidity.elements = {
      type: "validating",
      message: (0, import_i18n19.__)("Validating\u2026")
    };
  }
  if (customError instanceof Promise) {
    handleCustomValidationAsync(customError, formField, promiseHandler);
    fieldValidity.custom = {
      type: "validating",
      message: (0, import_i18n19.__)("Validating\u2026")
    };
  }
  if (Object.keys(fieldValidity).length > 0) {
    return fieldValidity;
  }
  if (formField.children.length > 0) {
    const result = {};
    formField.children.forEach((child) => {
      result[child.id] = validateFormField(item, child, {
        ...promiseHandler,
        path: [...promiseHandler.path, formField.id, "children"]
      });
    });
    const filteredResult = {};
    Object.entries(result).forEach(([key, value]) => {
      if (value !== void 0) {
        filteredResult[key] = value;
      }
    });
    if (Object.keys(filteredResult).length === 0) {
      return void 0;
    }
    return {
      children: filteredResult
    };
  }
  return void 0;
}
function getFormFieldValue(formField, item) {
  const fieldValue = formField?.field?.getValue({ item });
  if (formField.children.length === 0) {
    return fieldValue;
  }
  const childrenValues = formField.children.map(
    (child) => getFormFieldValue(child, item)
  );
  if (!childrenValues) {
    return fieldValue;
  }
  return {
    value: fieldValue,
    children: childrenValues
  };
}
function useFormValidity(item, fields, form) {
  const [formValidity, setFormValidity] = (0, import_element42.useState)();
  const customCounterRef = (0, import_element42.useRef)({});
  const elementsCounterRef = (0, import_element42.useRef)({});
  const previousValuesRef = (0, import_element42.useRef)({});
  const validate = (0, import_element42.useCallback)(() => {
    const promiseHandler = {
      customCounterRef,
      elementsCounterRef,
      setFormValidity,
      path: [],
      item
    };
    const formFieldsToValidate = getFormFieldsToValidate(form, fields);
    if (formFieldsToValidate.length === 0) {
      setFormValidity(void 0);
      return;
    }
    const newFormValidity = {};
    const untouchedFields = [];
    formFieldsToValidate.forEach((formField) => {
      const value = getFormFieldValue(formField, item);
      if (previousValuesRef.current.hasOwnProperty(formField.id) && (0, import_es6.default)(
        previousValuesRef.current[formField.id],
        value
      )) {
        untouchedFields.push(formField.id);
        return;
      }
      previousValuesRef.current[formField.id] = value;
      const fieldValidity = validateFormField(
        item,
        formField,
        promiseHandler
      );
      if (fieldValidity !== void 0) {
        newFormValidity[formField.id] = fieldValidity;
      }
    });
    setFormValidity((existingFormValidity) => {
      let validity = {
        ...existingFormValidity,
        ...newFormValidity
      };
      const fieldsToKeep = [
        ...untouchedFields,
        ...Object.keys(newFormValidity)
      ];
      Object.keys(validity).forEach((key) => {
        if (validity && !fieldsToKeep.includes(key)) {
          delete validity[key];
        }
      });
      if (Object.keys(validity).length === 0) {
        validity = void 0;
      }
      const areEqual = (0, import_es6.default)(existingFormValidity, validity);
      if (areEqual) {
        return existingFormValidity;
      }
      return validity;
    });
  }, [item, fields, form]);
  (0, import_element42.useEffect)(() => {
    validate();
  }, [validate]);
  return {
    validity: formValidity,
    isValid: isFormValid(formValidity)
  };
}
var use_form_validity_default = useFormValidity;

// packages/dataviews/build-module/hooks/use-report-validity.mjs
var import_element43 = __toESM(require_element(), 1);
function useReportValidity(ref, shouldReport) {
  (0, import_element43.useEffect)(() => {
    if (shouldReport && ref.current) {
      const inputs = ref.current.querySelectorAll(
        "input, textarea, select"
      );
      inputs.forEach((input) => {
        input.reportValidity();
      });
    }
  }, [shouldReport, ref]);
}

// packages/dataviews/build-module/components/dataform-layouts/panel/utils/use-field-from-form-field.mjs
var import_element44 = __toESM(require_element(), 1);

// packages/dataviews/build-module/components/dataform-layouts/get-summary-fields.mjs
function extractSummaryIds(summary) {
  if (Array.isArray(summary)) {
    return summary.map(
      (item) => typeof item === "string" ? item : item.id
    );
  }
  return [];
}
var getSummaryFields = (summaryField, fields) => {
  if (Array.isArray(summaryField) && summaryField.length > 0) {
    const summaryIds = extractSummaryIds(summaryField);
    return summaryIds.map(
      (summaryId) => fields.find((_field) => _field.id === summaryId)
    ).filter((_field) => _field !== void 0);
  }
  return [];
};

// packages/dataviews/build-module/components/dataform-layouts/panel/utils/use-field-from-form-field.mjs
var getFieldDefinition = (field, fields) => {
  const fieldDefinition = fields.find((_field) => _field.id === field.id);
  if (!fieldDefinition) {
    return fields.find((_field) => {
      if (!!field.children) {
        const simpleChildren = field.children.filter(
          (child) => !child.children
        );
        if (simpleChildren.length === 0) {
          return false;
        }
        return _field.id === simpleChildren[0].id;
      }
      return _field.id === field.id;
    });
  }
  return fieldDefinition;
};
function useFieldFromFormField(field) {
  const { fields } = (0, import_element44.useContext)(dataform_context_default);
  const layout = field.layout;
  const summaryFields = getSummaryFields(layout.summary, fields);
  const fieldDefinition = getFieldDefinition(field, fields);
  const fieldLabel = !!field.children ? field.label : fieldDefinition?.label;
  if (summaryFields.length === 0) {
    return {
      summaryFields: fieldDefinition ? [fieldDefinition] : [],
      fieldDefinition,
      fieldLabel
    };
  }
  return {
    summaryFields,
    fieldDefinition,
    fieldLabel
  };
}
var use_field_from_form_field_default = useFieldFromFormField;

// packages/dataviews/build-module/components/dataform-layouts/panel/modal.mjs
var import_jsx_runtime56 = __toESM(require_jsx_runtime(), 1);
function ModalContent({
  data,
  field,
  onChange,
  fieldLabel,
  onClose,
  touched
}) {
  const { openAs } = field.layout;
  const { applyLabel, cancelLabel } = openAs;
  const { fields } = (0, import_element45.useContext)(dataform_context_default);
  const [changes, setChanges] = (0, import_element45.useState)({});
  const modalData = (0, import_element45.useMemo)(() => {
    return (0, import_deepmerge2.default)(data, changes, {
      arrayMerge: (target, source) => source
    });
  }, [data, changes]);
  const form = (0, import_element45.useMemo)(
    () => ({
      layout: DEFAULT_LAYOUT,
      fields: !!field.children ? field.children : (
        // If not explicit children return the field id itself.
        [{ id: field.id, layout: DEFAULT_LAYOUT }]
      )
    }),
    [field]
  );
  const fieldsAsFieldType = fields.map((f2) => ({
    ...f2,
    Edit: f2.Edit === null ? void 0 : f2.Edit,
    isValid: {
      required: f2.isValid.required?.constraint,
      elements: f2.isValid.elements?.constraint,
      min: f2.isValid.min?.constraint,
      max: f2.isValid.max?.constraint,
      pattern: f2.isValid.pattern?.constraint,
      minLength: f2.isValid.minLength?.constraint,
      maxLength: f2.isValid.maxLength?.constraint
    }
  }));
  const { validity } = use_form_validity_default(modalData, fieldsAsFieldType, form);
  const onApply = () => {
    onChange(changes);
    onClose();
  };
  const handleOnChange = (newValue) => {
    setChanges(
      (prev) => (0, import_deepmerge2.default)(prev, newValue, {
        arrayMerge: (target, source) => source
      })
    );
  };
  const focusOnMountRef = (0, import_compose2.useFocusOnMount)("firstInputElement");
  const contentRef = (0, import_element45.useRef)(null);
  const mergedRef = (0, import_compose2.useMergeRefs)([focusOnMountRef, contentRef]);
  useReportValidity(contentRef, touched);
  return /* @__PURE__ */ (0, import_jsx_runtime56.jsxs)(
    import_components23.Modal,
    {
      className: "dataforms-layouts-panel__modal",
      onRequestClose: onClose,
      isFullScreen: false,
      title: fieldLabel,
      size: "medium",
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime56.jsx)("div", { ref: mergedRef, children: /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
          DataFormLayout,
          {
            data: modalData,
            form,
            onChange: handleOnChange,
            validity,
            children: (FieldLayout, childField, childFieldValidity, markWhenOptional) => /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
              FieldLayout,
              {
                data: modalData,
                field: childField,
                onChange: handleOnChange,
                hideLabelFromVision: form.fields.length < 2,
                markWhenOptional,
                validity: childFieldValidity
              },
              childField.id
            )
          }
        ) }),
        /* @__PURE__ */ (0, import_jsx_runtime56.jsxs)(
          Stack,
          {
            direction: "row",
            className: "dataforms-layouts-panel__modal-footer",
            gap: "md",
            children: [
              /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(import_components23.__experimentalSpacer, { style: { flex: 1 } }),
              /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
                import_components23.Button,
                {
                  variant: "tertiary",
                  onClick: onClose,
                  __next40pxDefaultSize: true,
                  children: cancelLabel
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
                import_components23.Button,
                {
                  variant: "primary",
                  onClick: onApply,
                  __next40pxDefaultSize: true,
                  children: applyLabel
                }
              )
            ]
          }
        )
      ]
    }
  );
}
function PanelModal({
  data,
  field,
  onChange,
  validity
}) {
  const [touched, setTouched] = (0, import_element45.useState)(false);
  const [isOpen, setIsOpen] = (0, import_element45.useState)(false);
  const { fieldDefinition, fieldLabel, summaryFields } = use_field_from_form_field_default(field);
  if (!fieldDefinition) {
    return null;
  }
  const handleClose = () => {
    setIsOpen(false);
    setTouched(true);
  };
  return /* @__PURE__ */ (0, import_jsx_runtime56.jsxs)(import_jsx_runtime56.Fragment, { children: [
    /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
      SummaryButton,
      {
        data,
        field,
        fieldLabel,
        summaryFields,
        validity,
        touched,
        disabled: fieldDefinition.readOnly === true,
        onClick: () => setIsOpen(true),
        "aria-expanded": isOpen
      }
    ),
    isOpen && /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
      ModalContent,
      {
        data,
        field,
        onChange,
        fieldLabel: fieldLabel ?? "",
        onClose: handleClose,
        touched
      }
    )
  ] });
}
var modal_default = PanelModal;

// packages/dataviews/build-module/components/dataform-layouts/panel/dropdown.mjs
var import_components24 = __toESM(require_components(), 1);
var import_i18n20 = __toESM(require_i18n(), 1);
var import_element46 = __toESM(require_element(), 1);
var import_compose3 = __toESM(require_compose(), 1);
var import_jsx_runtime57 = __toESM(require_jsx_runtime(), 1);
function DropdownHeader({
  title,
  onClose
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
    Stack,
    {
      direction: "column",
      className: "dataforms-layouts-panel__dropdown-header",
      gap: "lg",
      children: /* @__PURE__ */ (0, import_jsx_runtime57.jsxs)(Stack, { direction: "row", gap: "sm", align: "center", children: [
        title && /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(import_components24.__experimentalHeading, { level: 2, size: 13, children: title }),
        /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(import_components24.__experimentalSpacer, { style: { flex: 1 } }),
        onClose && /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
          import_components24.Button,
          {
            label: (0, import_i18n20.__)("Close"),
            icon: close_small_default,
            onClick: onClose,
            size: "small"
          }
        )
      ] })
    }
  );
}
function DropdownContentWithValidation({
  touched,
  children
}) {
  const ref = (0, import_element46.useRef)(null);
  useReportValidity(ref, touched);
  return /* @__PURE__ */ (0, import_jsx_runtime57.jsx)("div", { ref, children });
}
function PanelDropdown({
  data,
  field,
  onChange,
  validity
}) {
  const [touched, setTouched] = (0, import_element46.useState)(false);
  const [popoverAnchor, setPopoverAnchor] = (0, import_element46.useState)(
    null
  );
  const popoverProps = (0, import_element46.useMemo)(
    () => ({
      // Anchor the popover to the middle of the entire row so that it doesn't
      // move around when the label changes.
      anchor: popoverAnchor,
      placement: "left-start",
      offset: 36,
      shift: true
    }),
    [popoverAnchor]
  );
  const [dialogRef, dialogProps] = (0, import_compose3.__experimentalUseDialog)({
    focusOnMount: "firstInputElement"
  });
  const form = (0, import_element46.useMemo)(
    () => ({
      layout: DEFAULT_LAYOUT,
      fields: !!field.children ? field.children : (
        // If not explicit children return the field id itself.
        [{ id: field.id, layout: DEFAULT_LAYOUT }]
      )
    }),
    [field]
  );
  const formValidity = (0, import_element46.useMemo)(() => {
    if (validity === void 0) {
      return void 0;
    }
    if (!!field.children) {
      return validity?.children;
    }
    return { [field.id]: validity };
  }, [validity, field]);
  const { fieldDefinition, fieldLabel, summaryFields } = use_field_from_form_field_default(field);
  if (!fieldDefinition) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
    "div",
    {
      ref: setPopoverAnchor,
      className: "dataforms-layouts-panel__field-dropdown-anchor",
      children: /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
        import_components24.Dropdown,
        {
          contentClassName: "dataforms-layouts-panel__field-dropdown",
          popoverProps,
          focusOnMount: false,
          onToggle: (willOpen) => {
            if (!willOpen) {
              setTouched(true);
            }
          },
          renderToggle: ({ isOpen, onToggle }) => /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
            SummaryButton,
            {
              data,
              field,
              fieldLabel,
              summaryFields,
              validity,
              touched,
              disabled: fieldDefinition.readOnly === true,
              onClick: onToggle,
              "aria-expanded": isOpen
            }
          ),
          renderContent: ({ onClose }) => /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(DropdownContentWithValidation, { touched, children: /* @__PURE__ */ (0, import_jsx_runtime57.jsxs)("div", { ref: dialogRef, ...dialogProps, children: [
            /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
              DropdownHeader,
              {
                title: fieldLabel,
                onClose
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
              DataFormLayout,
              {
                data,
                form,
                onChange,
                validity: formValidity,
                children: (FieldLayout, childField, childFieldValidity, markWhenOptional) => /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
                  FieldLayout,
                  {
                    data,
                    field: childField,
                    onChange,
                    hideLabelFromVision: (form?.fields ?? []).length < 2,
                    markWhenOptional,
                    validity: childFieldValidity
                  },
                  childField.id
                )
              }
            )
          ] }) })
        }
      )
    }
  );
}
var dropdown_default = PanelDropdown;

// packages/dataviews/build-module/components/dataform-layouts/panel/index.mjs
var import_jsx_runtime58 = __toESM(require_jsx_runtime(), 1);
function FormPanelField({
  data,
  field,
  onChange,
  validity
}) {
  const layout = field.layout;
  if (layout.openAs.type === "modal") {
    return /* @__PURE__ */ (0, import_jsx_runtime58.jsx)(
      modal_default,
      {
        data,
        field,
        onChange,
        validity
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime58.jsx)(
    dropdown_default,
    {
      data,
      field,
      onChange,
      validity
    }
  );
}

// packages/dataviews/build-module/components/dataform-layouts/card/index.mjs
var import_element47 = __toESM(require_element(), 1);

// packages/dataviews/build-module/components/dataform-layouts/validation-badge.mjs
var import_i18n21 = __toESM(require_i18n(), 1);
var import_jsx_runtime59 = __toESM(require_jsx_runtime(), 1);
function countInvalidFields(validity) {
  if (!validity) {
    return 0;
  }
  let count = 0;
  const validityRules = Object.keys(validity).filter(
    (key) => key !== "children"
  );
  for (const key of validityRules) {
    const rule = validity[key];
    if (rule?.type === "invalid") {
      count++;
    }
  }
  if (validity.children) {
    for (const childValidity of Object.values(validity.children)) {
      count += countInvalidFields(childValidity);
    }
  }
  return count;
}
function ValidationBadge({
  validity
}) {
  const invalidCount = countInvalidFields(validity);
  if (invalidCount === 0) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime59.jsx)(Badge, { intent: "high", children: (0, import_i18n21.sprintf)(
    /* translators: %d: Number of fields that need attention */
    (0, import_i18n21._n)(
      "%d field needs attention",
      "%d fields need attention",
      invalidCount
    ),
    invalidCount
  ) });
}

// packages/dataviews/build-module/components/dataform-layouts/card/index.mjs
var import_jsx_runtime60 = __toESM(require_jsx_runtime(), 1);
function isSummaryFieldVisible(summaryField, summaryConfig, isOpen) {
  if (!summaryConfig || Array.isArray(summaryConfig) && summaryConfig.length === 0) {
    return false;
  }
  const summaryConfigArray = Array.isArray(summaryConfig) ? summaryConfig : [summaryConfig];
  const fieldConfig = summaryConfigArray.find((config) => {
    if (typeof config === "string") {
      return config === summaryField.id;
    }
    if (typeof config === "object" && "id" in config) {
      return config.id === summaryField.id;
    }
    return false;
  });
  if (!fieldConfig) {
    return false;
  }
  if (typeof fieldConfig === "string") {
    return true;
  }
  if (typeof fieldConfig === "object" && "visibility" in fieldConfig) {
    return fieldConfig.visibility === "always" || fieldConfig.visibility === "when-collapsed" && !isOpen;
  }
  return true;
}
function HeaderContent({
  data,
  fields,
  label,
  layout,
  isOpen,
  touched,
  validity
}) {
  const summaryFields = getSummaryFields(layout.summary, fields);
  const visibleSummaryFields = summaryFields.filter(
    (summaryField) => isSummaryFieldVisible(summaryField, layout.summary, isOpen)
  );
  const hasBadge = touched && layout.isCollapsible;
  const hasSummary = visibleSummaryFields.length > 0 && layout.withHeader;
  return /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)(
    Stack,
    {
      align: "center",
      justify: "space-between",
      className: "dataforms-layouts-card__field-header-content",
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(card_exports.Title, { children: label }),
        (hasBadge || hasSummary) && /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)(collapsible_card_exports.HeaderDescription, { className: "dataforms-layouts-card__field-header-content-description", children: [
          hasBadge && /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(ValidationBadge, { validity }),
          hasSummary && /* @__PURE__ */ (0, import_jsx_runtime60.jsx)("div", { className: "dataforms-layouts-card__field-summary", children: visibleSummaryFields.map((summaryField) => /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
            summaryField.render,
            {
              item: data,
              field: summaryField
            },
            summaryField.id
          )) })
        ] })
      ]
    }
  );
}
function BodyContent({
  data,
  field,
  form,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity,
  withHeader
}) {
  if (field.children) {
    return /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)(import_jsx_runtime60.Fragment, { children: [
      field.description && /* @__PURE__ */ (0, import_jsx_runtime60.jsx)("div", { className: "dataforms-layouts-card__field-description", children: field.description }),
      /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
        DataFormLayout,
        {
          data,
          form,
          onChange,
          validity: validity?.children
        }
      )
    ] });
  }
  const SingleFieldLayout = getFormFieldLayout("regular")?.component;
  if (!SingleFieldLayout) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
    SingleFieldLayout,
    {
      data,
      field,
      onChange,
      hideLabelFromVision: hideLabelFromVision || withHeader,
      markWhenOptional,
      validity
    }
  );
}
function FormCardField({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const { fields } = (0, import_element47.useContext)(dataform_context_default);
  const layout = field.layout;
  const contentRef = (0, import_element47.useRef)(null);
  const form = (0, import_element47.useMemo)(
    () => ({
      layout: DEFAULT_LAYOUT,
      fields: field.children ?? []
    }),
    [field]
  );
  const { isOpened, isCollapsible } = layout;
  const [isOpen, setIsOpen] = (0, import_element47.useState)(isOpened);
  const [touched, setTouched] = (0, import_element47.useState)(false);
  (0, import_element47.useEffect)(() => {
    setIsOpen(isOpened);
  }, [isOpened]);
  const handleOpenChange = (0, import_element47.useCallback)((open) => {
    if (!open) {
      setTouched(true);
    }
    setIsOpen(open);
  }, []);
  const handleBlur = (0, import_element47.useCallback)(() => {
    setTouched(true);
  }, []);
  useReportValidity(
    contentRef,
    (isCollapsible ? isOpen : true) && touched
  );
  let label = field.label;
  let withHeader;
  if (field.children) {
    withHeader = !!label && layout.withHeader;
  } else {
    const fieldDefinition = fields.find(
      (fieldDef) => fieldDef.id === field.id
    );
    if (!fieldDefinition || !fieldDefinition.Edit) {
      return null;
    }
    label = fieldDefinition.label;
    withHeader = !!label && layout.withHeader;
  }
  const bodyContent = /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
    BodyContent,
    {
      data,
      field,
      form,
      onChange,
      hideLabelFromVision,
      markWhenOptional,
      validity,
      withHeader
    }
  );
  const headerContent = /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
    HeaderContent,
    {
      data,
      fields,
      label,
      layout,
      isOpen: isCollapsible ? !!isOpen : true,
      touched,
      validity
    }
  );
  if (withHeader && isCollapsible) {
    return /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)(
      collapsible_card_exports.Root,
      {
        className: "dataforms-layouts-card__field",
        open: isOpen,
        onOpenChange: handleOpenChange,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(collapsible_card_exports.Header, { children: headerContent }),
          /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
            collapsible_card_exports.Content,
            {
              ref: contentRef,
              onBlur: handleBlur,
              children: bodyContent
            }
          )
        ]
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)(card_exports.Root, { className: "dataforms-layouts-card__field", children: [
    withHeader && /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(card_exports.Header, { children: headerContent }),
    /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(card_exports.Content, { ref: contentRef, onBlur: handleBlur, children: bodyContent })
  ] });
}

// packages/dataviews/build-module/components/dataform-layouts/row/index.mjs
var import_components25 = __toESM(require_components(), 1);
var import_jsx_runtime61 = __toESM(require_jsx_runtime(), 1);
function Header5({ title }) {
  return /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(
    Stack,
    {
      direction: "column",
      className: "dataforms-layouts-row__header",
      gap: "lg",
      children: /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(Stack, { direction: "row", align: "center", children: /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(import_components25.__experimentalHeading, { level: 2, size: 13, children: title }) })
    }
  );
}
var EMPTY_WRAPPER = ({ children }) => /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(import_jsx_runtime61.Fragment, { children });
function FormRowField({
  data,
  field,
  onChange,
  hideLabelFromVision,
  markWhenOptional,
  validity
}) {
  const layout = field.layout;
  if (!!field.children) {
    const form = {
      layout: DEFAULT_LAYOUT,
      fields: field.children
    };
    return /* @__PURE__ */ (0, import_jsx_runtime61.jsxs)("div", { className: "dataforms-layouts-row__field", children: [
      !hideLabelFromVision && field.label && /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(Header5, { title: field.label }),
      /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(Stack, { direction: "row", align: layout.alignment, gap: "lg", children: /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(
        DataFormLayout,
        {
          data,
          form,
          onChange,
          validity: validity?.children,
          as: EMPTY_WRAPPER,
          children: (FieldLayout, childField, childFieldValidity) => /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(
            "div",
            {
              className: "dataforms-layouts-row__field-control",
              style: layout.styles[childField.id],
              children: /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(
                FieldLayout,
                {
                  data,
                  field: childField,
                  onChange,
                  hideLabelFromVision,
                  markWhenOptional,
                  validity: childFieldValidity
                }
              )
            },
            childField.id
          )
        }
      ) })
    ] });
  }
  const RegularLayout = getFormFieldLayout("regular")?.component;
  if (!RegularLayout) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(import_jsx_runtime61.Fragment, { children: /* @__PURE__ */ (0, import_jsx_runtime61.jsx)("div", { className: "dataforms-layouts-row__field-control", children: /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(
    RegularLayout,
    {
      data,
      field,
      onChange,
      markWhenOptional,
      validity
    }
  ) }) });
}

// packages/dataviews/build-module/components/dataform-layouts/details/index.mjs
var import_element48 = __toESM(require_element(), 1);
var import_i18n22 = __toESM(require_i18n(), 1);
var import_jsx_runtime62 = __toESM(require_jsx_runtime(), 1);
function FormDetailsField({
  data,
  field,
  onChange,
  validity
}) {
  const { fields } = (0, import_element48.useContext)(dataform_context_default);
  const detailsRef = (0, import_element48.useRef)(null);
  const contentRef = (0, import_element48.useRef)(null);
  const [touched, setTouched] = (0, import_element48.useState)(false);
  const [isOpen, setIsOpen] = (0, import_element48.useState)(false);
  const form = (0, import_element48.useMemo)(
    () => ({
      layout: DEFAULT_LAYOUT,
      fields: field.children ?? []
    }),
    [field]
  );
  (0, import_element48.useEffect)(() => {
    const details = detailsRef.current;
    if (!details) {
      return;
    }
    const handleToggle = () => {
      const nowOpen = details.open;
      if (!nowOpen) {
        setTouched(true);
      }
      setIsOpen(nowOpen);
    };
    details.addEventListener("toggle", handleToggle);
    return () => {
      details.removeEventListener("toggle", handleToggle);
    };
  }, []);
  useReportValidity(contentRef, isOpen && touched);
  const handleBlur = (0, import_element48.useCallback)(() => {
    setTouched(true);
  }, []);
  if (!field.children) {
    return null;
  }
  const summaryFieldId = field.layout.summary ?? "";
  const summaryField = summaryFieldId ? fields.find((fieldDef) => fieldDef.id === summaryFieldId) : void 0;
  let summaryContent;
  if (summaryField && summaryField.render) {
    summaryContent = /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(summaryField.render, { item: data, field: summaryField });
  } else {
    summaryContent = field.label || (0, import_i18n22.__)("More details");
  }
  return /* @__PURE__ */ (0, import_jsx_runtime62.jsxs)(
    "details",
    {
      ref: detailsRef,
      className: "dataforms-layouts-details__details",
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime62.jsx)("summary", { className: "dataforms-layouts-details__summary", children: /* @__PURE__ */ (0, import_jsx_runtime62.jsxs)(
          Stack,
          {
            direction: "row",
            align: "center",
            gap: "md",
            className: "dataforms-layouts-details__summary-content",
            children: [
              summaryContent,
              touched && /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(ValidationBadge, { validity })
            ]
          }
        ) }),
        /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
          "div",
          {
            ref: contentRef,
            className: "dataforms-layouts-details__content",
            onBlur: handleBlur,
            children: /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
              DataFormLayout,
              {
                data,
                form,
                onChange,
                validity: validity?.children
              }
            )
          }
        )
      ]
    }
  );
}

// packages/dataviews/build-module/components/dataform-layouts/index.mjs
var import_jsx_runtime63 = __toESM(require_jsx_runtime(), 1);
var FORM_FIELD_LAYOUTS = [
  {
    type: "regular",
    component: FormRegularField,
    wrapper: ({ children }) => /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
      Stack,
      {
        direction: "column",
        className: "dataforms-layouts__wrapper",
        gap: "lg",
        children
      }
    )
  },
  {
    type: "panel",
    component: FormPanelField,
    wrapper: ({ children }) => /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
      Stack,
      {
        direction: "column",
        className: "dataforms-layouts__wrapper",
        gap: "md",
        children
      }
    )
  },
  {
    type: "card",
    component: FormCardField,
    wrapper: ({ children }) => /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
      Stack,
      {
        direction: "column",
        className: "dataforms-layouts__wrapper",
        gap: "xl",
        children
      }
    )
  },
  {
    type: "row",
    component: FormRowField,
    wrapper: ({
      children,
      layout
    }) => /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
      Stack,
      {
        direction: "column",
        className: "dataforms-layouts__wrapper",
        gap: "lg",
        children: /* @__PURE__ */ (0, import_jsx_runtime63.jsx)("div", { className: "dataforms-layouts-row__field", children: /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
          Stack,
          {
            direction: "row",
            gap: "lg",
            align: layout.alignment,
            children
          }
        ) })
      }
    )
  },
  {
    type: "details",
    component: FormDetailsField
  }
];
function getFormFieldLayout(type) {
  return FORM_FIELD_LAYOUTS.find((layout) => layout.type === type);
}

// packages/dataviews/build-module/components/dataform-layouts/data-form-layout.mjs
var import_jsx_runtime64 = __toESM(require_jsx_runtime(), 1);
var DEFAULT_WRAPPER = ({ children }) => /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(Stack, { direction: "column", className: "dataforms-layouts__wrapper", gap: "lg", children });
function DataFormLayout({
  data,
  form,
  onChange,
  validity,
  children,
  as
}) {
  const { fields: fieldDefinitions } = (0, import_element49.useContext)(dataform_context_default);
  const markWhenOptional = (0, import_element49.useMemo)(() => {
    const requiredCount = fieldDefinitions.filter(
      (f2) => !!f2.isValid?.required
    ).length;
    const optionalCount = fieldDefinitions.length - requiredCount;
    return requiredCount > optionalCount;
  }, [fieldDefinitions]);
  function getFieldDefinition2(field) {
    return fieldDefinitions.find(
      (fieldDefinition) => fieldDefinition.id === field.id
    );
  }
  const Wrapper = as ?? getFormFieldLayout(form.layout.type)?.wrapper ?? DEFAULT_WRAPPER;
  return /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(Wrapper, { layout: form.layout, children: form.fields.map((formField) => {
    const FieldLayout = getFormFieldLayout(formField.layout.type)?.component;
    if (!FieldLayout) {
      return null;
    }
    const fieldDefinition = !formField.children ? getFieldDefinition2(formField) : void 0;
    if (fieldDefinition && fieldDefinition.isVisible && !fieldDefinition.isVisible(data)) {
      return null;
    }
    if (children) {
      return children(
        FieldLayout,
        formField,
        validity?.[formField.id],
        markWhenOptional
      );
    }
    return /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(
      FieldLayout,
      {
        data,
        field: formField,
        onChange,
        markWhenOptional,
        validity: validity?.[formField.id]
      },
      formField.id
    );
  }) });
}

// packages/dataviews/build-module/dataform/index.mjs
var import_jsx_runtime65 = __toESM(require_jsx_runtime(), 1);
function DataForm({
  data,
  form,
  fields,
  onChange,
  validity
}) {
  const normalizedForm = (0, import_element50.useMemo)(() => normalize_form_default(form), [form]);
  const normalizedFields = (0, import_element50.useMemo)(
    () => normalizeFields(fields),
    [fields]
  );
  if (!form.fields) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime65.jsx)(DataFormProvider, { fields: normalizedFields, children: /* @__PURE__ */ (0, import_jsx_runtime65.jsx)(
    DataFormLayout,
    {
      data,
      form: normalizedForm,
      onChange,
      validity
    }
  ) });
}

// routes/taxonomy-edit/stage.tsx
var import_element51 = __toESM(require_element());
var import_i18n23 = __toESM(require_i18n());
var import_notices = __toESM(require_notices());
import { useNavigate, useParams } from "@wordpress/route";
import {
  addNewItemLabelField,
  addOrRemoveItemsField,
  allItemsField,
  backToItemsField,
  BLANK_RECORD,
  chooseFromMostUsedField,
  descriptionField,
  editItemField,
  generalForm,
  hierarchicalField,
  labelsActionsField,
  labelsForm,
  menuNameField,
  newItemNameField,
  notFoundField,
  parentItemColonField,
  parentItemField,
  pluralLabelField,
  popularItemsField,
  publicField,
  searchItemsField,
  separateItemsField,
  serializeForSave,
  singularLabelField,
  statusField,
  toFormData,
  updateItemField,
  useObjectTypeField,
  useSlugField,
  viewItemField
} from "@wordpress/user-taxonomies";

// routes/taxonomy-edit/taxonomy-form.scss
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='eda8453059']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "eda8453059");
  style.appendChild(document.createTextNode(".taxonomy-form{box-sizing:border-box;margin:0 auto;max-width:680px;padding:24px;width:100%}.taxonomy-form__help-text{color:#757575}"));
  document.head.appendChild(style);
}

// routes/taxonomy-edit/stage.tsx
var NEW_ID = "new";
var USER_TAXONOMY_POST_TYPE = "wp_user_taxonomy";
function TaxonomyEditStage() {
  const { id } = useParams({ from: "/edit/$id" });
  const navigate = useNavigate();
  const isAddMode = id === NEW_ID;
  const taxonomyId = parseInt(id, 10);
  const initialData = (0, import_data.useSelect)(
    (select) => {
      if (isAddMode) {
        return BLANK_RECORD;
      }
      const record = select(
        import_core_data.store
      ).getEntityRecord(
        "postType",
        USER_TAXONOMY_POST_TYPE,
        taxonomyId
      );
      return toFormData(record);
    },
    [isAddMode, taxonomyId]
  );
  const title = isAddMode ? (0, import_i18n23.__)("Add taxonomy") : initialData.title.raw;
  const commonProps = { initialData, title };
  const taxonomyPageProps = isAddMode ? {
    ...commonProps,
    isAddMode: true,
    breadcrumbLabel: (0, import_i18n23.__)("Add new"),
    subTitle: (0, import_i18n23.__)(
      "Define a new taxonomy. Fill in the essentials under General; expand Labels to customize."
    ),
    onSaved: (saved) => navigate({ to: `/edit/${saved.id}` })
  } : {
    ...commonProps,
    isAddMode: false,
    breadcrumbLabel: title,
    subTitle: (0, import_i18n23.__)(
      "Edit this taxonomy. Expand the Labels section to adjust labels."
    )
  };
  return /* @__PURE__ */ React.createElement(TaxonomyPage, { key: id, ...taxonomyPageProps });
}
function TaxonomyPage({
  isAddMode,
  initialData,
  title,
  breadcrumbLabel,
  subTitle,
  onSaved
}) {
  const [data, setData] = (0, import_element51.useState)(initialData);
  const [isSaving, setIsSaving] = (0, import_element51.useState)(false);
  const originalSlug = !isAddMode ? initialData.slug : void 0;
  const slugField = useSlugField(originalSlug, data.slug);
  const objectTypeField = useObjectTypeField();
  const fields = (0, import_element51.useMemo)(
    () => [
      // General
      pluralLabelField,
      singularLabelField,
      slugField,
      descriptionField,
      objectTypeField,
      publicField,
      hierarchicalField,
      statusField,
      // Labels
      labelsActionsField,
      menuNameField,
      allItemsField,
      editItemField,
      viewItemField,
      updateItemField,
      addNewItemLabelField,
      newItemNameField,
      searchItemsField,
      notFoundField,
      backToItemsField,
      parentItemField,
      popularItemsField,
      separateItemsField,
      parentItemColonField,
      addOrRemoveItemsField,
      chooseFromMostUsedField
    ],
    [slugField, objectTypeField]
  );
  const form = (0, import_element51.useMemo)(
    () => ({
      layout: { type: "card", isCollapsible: true },
      fields: [
        {
          id: "general",
          label: (0, import_i18n23.__)("General"),
          description: (0, import_i18n23.__)(
            "Core identity, post types, and activation."
          ),
          layout: {
            type: "card",
            isCollapsible: true,
            isOpened: true
          },
          children: generalForm.fields
        },
        {
          id: "labels",
          label: (0, import_i18n23.__)("Labels"),
          layout: {
            type: "card",
            isCollapsible: true,
            isOpened: false
          },
          children: labelsForm.fields
        }
      ]
    }),
    []
  );
  const { validity, isValid: isValid2 } = use_form_validity_default(data, fields, form);
  const formId = (0, import_compose4.useInstanceId)(TaxonomyPage, "taxonomy-form");
  const { saveEntityRecord } = (0, import_data.useDispatch)(import_core_data.store);
  const { createSuccessNotice, createErrorNotice } = (0, import_data.useDispatch)(import_notices.store);
  async function onSave() {
    if (isSaving || !isValid2) {
      return;
    }
    setIsSaving(true);
    try {
      const saved = await saveEntityRecord(
        "postType",
        USER_TAXONOMY_POST_TYPE,
        serializeForSave(data),
        { throwOnError: true }
      );
      const successMessage = isAddMode ? (0, import_i18n23.sprintf)(
        /* translators: %s: taxonomy plural label. */
        (0, import_i18n23.__)('"%s" taxonomy created.'),
        data.title.raw
      ) : (0, import_i18n23.sprintf)(
        /* translators: %s: taxonomy plural label. */
        (0, import_i18n23.__)('"%s" taxonomy updated.'),
        data.title.raw
      );
      createSuccessNotice(successMessage, { type: "snackbar" });
      if (saved?.id !== void 0) {
        onSaved?.({ ...data, id: saved.id });
      }
    } catch (error2) {
      let errorMessage;
      if (error2?.message && error2?.code !== "unknown_error") {
        errorMessage = error2.message;
      } else if (isAddMode) {
        errorMessage = (0, import_i18n23.__)("Failed to create taxonomy.");
      } else {
        errorMessage = (0, import_i18n23.__)("Failed to update taxonomy.");
      }
      createErrorNotice(errorMessage, { type: "snackbar" });
    } finally {
      setIsSaving(false);
    }
  }
  return /* @__PURE__ */ React.createElement(
    page_default,
    {
      ariaLabel: title,
      breadcrumbs: /* @__PURE__ */ React.createElement(
        breadcrumbs_default,
        {
          items: [
            { label: (0, import_i18n23.__)("Taxonomies"), to: "/" },
            { label: breadcrumbLabel }
          ]
        }
      ),
      subTitle,
      actions: /* @__PURE__ */ React.createElement(
        import_components26.Button,
        {
          __next40pxDefaultSize: true,
          variant: "primary",
          size: "compact",
          type: "submit",
          form: formId,
          isBusy: isSaving,
          disabled: isSaving,
          accessibleWhenDisabled: true
        },
        isAddMode ? (0, import_i18n23.__)("Create") : (0, import_i18n23.__)("Save")
      )
    },
    /* @__PURE__ */ React.createElement(
      Stack,
      {
        direction: "column",
        gap: "md",
        className: "taxonomy-form",
        render: /* @__PURE__ */ React.createElement(
          "form",
          {
            id: formId,
            onSubmit: (event) => {
              event.preventDefault();
              onSave();
            }
          }
        )
      },
      /* @__PURE__ */ React.createElement(
        DataForm,
        {
          data,
          fields,
          form,
          validity,
          onChange: (edits) => setData(
            (prev) => ({
              ...prev,
              ...edits
            })
          )
        }
      )
    )
  );
}
var stage = TaxonomyEditStage;
export {
  stage
};
