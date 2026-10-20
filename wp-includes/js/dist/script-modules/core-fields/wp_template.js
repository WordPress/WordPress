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

// package-external:@wordpress/core-data
var require_core_data = __commonJS({
  "package-external:@wordpress/core-data"(exports, module) {
    module.exports = window.wp.coreData;
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

// vendor-external:react
var require_react = __commonJS({
  "vendor-external:react"(exports, module) {
    module.exports = window.React;
  }
});

// package-external:@wordpress/i18n
var require_i18n = __commonJS({
  "package-external:@wordpress/i18n"(exports, module) {
    module.exports = window.wp.i18n;
  }
});

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

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

// packages/core-fields/build-module/wp_template/author/view.mjs
var import_element4 = __toESM(require_element(), 1);
var import_data = __toESM(require_data(), 1);
var import_core_data = __toESM(require_core_data(), 1);

// packages/icons/build-module/library/comment-author-avatar.mjs
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
var comment_author_avatar_default = /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
  /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M8 18.0476V16C8 14.8954 8.89543 14 10 14H14C15.1046 14 16 14.8954 16 16V18.0476" }),
  /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M12 4.75C7.99594 4.75 4.75 7.99593 4.75 12C4.75 16.0041 7.99593 19.25 12 19.25C16.0041 19.25 19.25 16.0041 19.25 12C19.25 7.99594 16.0041 4.75 12 4.75Z", strokeLinejoin: "round" }),
  /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M13.75 10C13.75 10.9665 12.9665 11.75 12 11.75C11.0335 11.75 10.25 10.9665 10.25 10C10.25 9.0335 11.0335 8.25 12 8.25C12.9665 8.25 13.75 9.0335 13.75 10Z" })
] });

// packages/icons/build-module/library/globe.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var globe_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M12 4.75C8.87453 4.75 6.21099 6.72773 5.19257 9.5M18.8074 14.5C19.0937 13.7207 19.25 12.8786 19.25 12C19.25 11.1214 19.0937 10.2793 18.8074 9.5C17.789 6.72773 15.1255 4.75 12 4.75M12 4.75C11 5.5 9 8.63218 9 12C9 15.3678 11 18.5 12 19.25M12 4.75C13 5.5 15 8.63218 15 12C15 15.3678 13 18.5 12 19.25M5.19257 9.5C4.90628 10.2793 4.75 11.1214 4.75 12C4.75 12.8786 4.90629 13.7207 5.19257 14.5C6.21099 17.2723 8.87453 19.25 12 19.25M12 19.25C15.1255 19.25 17.789 17.2723 18.8074 14.5M5.19257 9.5L18.8074 9.5M5.19257 14.5L18.8074 14.5", strokeLinejoin: "round" }) });

// packages/icons/build-module/library/layout.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
var layout_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M9.75 19.25H18C18.6904 19.25 19.25 18.6904 19.25 18V9.75M9.75 19.25H6C5.30964 19.25 4.75 18.6904 4.75 18V9.75M9.75 19.25V9.75M19.25 9.75V6C19.25 5.30964 18.6904 4.75 18 4.75H6C5.30964 4.75 4.75 5.30964 4.75 6V9.75M19.25 9.75H9.75M9.75 9.75H4.75" }) });

// packages/icons/build-module/library/plugins.mjs
var import_primitives4 = __toESM(require_primitives(), 1);
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var plugins_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M15.3995 11.5711L12.2175 8.38909L10.273 6.44454L7.67678 9.04074C7.3017 9.41581 7.09099 9.92452 7.09099 10.455L7.09099 13.455C7.09099 13.7202 6.98563 13.9745 6.7981 14.1621L5.14645 15.8137C4.95118 16.009 4.95118 16.3256 5.14645 16.5208L7.26777 18.6421C7.46303 18.8374 7.77961 18.8374 7.97487 18.6421L9.62652 16.9905C9.81406 16.8029 10.0684 16.6976 10.3336 16.6976H13.3336C13.8641 16.6976 14.3728 16.4869 14.7478 16.1118L17.344 13.5156L15.3995 11.5711ZM12.2175 8.38909L16.1066 4.5M15.3995 11.5711L19.2886 7.68198" }) });

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
    stateAttributesMapping,
    enabled = true
  } = params;
  const className = enabled ? resolveClassName(classNameProp, state) : void 0;
  const style = enabled ? resolveStyle(styleProp, state) : void 0;
  const stateProps = enabled ? getStateAttributesProps(state, stateAttributesMapping) : EMPTY_OBJECT;
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
function unwrapLazyRenderProp(render2) {
  if (render2?.$$typeof !== REACT_LAZY_TYPE) {
    return render2;
  }
  const unwrapped = React4.Children.toArray(render2)[0];
  return /* @__PURE__ */ React4.isValidElement(unwrapped) ? unwrapped : render2;
}
function evaluateRenderProp(element, render2, props, state) {
  if (render2) {
    if (typeof render2 === "function") {
      if (true) {
        warnIfRenderPropLooksLikeComponent(render2);
      }
      return render2(props, state);
    }
    const mergedProps = mergeProps(props, render2.props);
    mergedProps.ref = props.ref;
    if (true) {
      if (!/* @__PURE__ */ React4.isValidElement(render2)) {
        throw new Error(["Base UI: The `render` prop was provided an invalid React element as `React.isValidElement(render)` is `false`.", "A valid React element must be provided to the `render` prop because it is cloned with props to replace the default element.", "https://base-ui.com/r/invalid-render-prop"].join("\n"));
      }
    }
    return /* @__PURE__ */ React4.cloneElement(render2, mergedProps);
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

// packages/ui/build-module/icon/icon.mjs
var import_element = __toESM(require_element(), 1);
var import_primitives5 = __toESM(require_primitives(), 1);
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
var Icon = (0, import_element.forwardRef)(
  function UnforwardedIcon({ icon, size = 24, style, ...restProps }, ref) {
    const mergedStyle = icon.props.style || style ? { ...icon.props.style, ...style } : void 0;
    return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
      import_primitives5.SVG,
      {
        ref,
        ...icon.props,
        ...restProps,
        ...mergedStyle ? { style: mergedStyle } : {},
        width: size,
        height: size
      }
    );
  }
);

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/use-render/useRender.mjs
function useRender(params) {
  return useRenderElement(params.defaultTagName ?? "div", params, params);
}

// packages/ui/build-module/text/text.mjs
var import_element2 = __toESM(require_element(), 1);
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
var Text = (0, import_element2.forwardRef)(
  function UnforwardedText({ variant = "body-md", render: render2, className, ...props }, ref) {
    const element = useRender({
      render: render2,
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

// packages/ui/build-module/stack/stack.mjs
var import_element3 = __toESM(require_element(), 1);
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
var Stack = (0, import_element3.forwardRef)(
  function UnforwardedStack({ direction, gap, align, justify, wrap, render: render2, ...props }, ref) {
    const style = {
      gap: gap && gapTokens[gap],
      alignItems: align,
      justifyContent: justify,
      flexDirection: direction,
      flexWrap: wrap
    };
    const element = useRender({
      render: render2,
      ref,
      props: mergeProps(props, {
        style,
        className: style_default2.stack
      })
    });
    return element;
  }
);

// packages/core-fields/build-module/wp_template/author/view.mjs
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle3("e26bdc0796", ".eddbc0a77c8a621a__avatar{align-items:center;block-size:24px;display:flex;flex-shrink:0;inline-size:24px;justify-content:start;overflow:hidden}.eddbc0a77c8a621a__avatar img{block-size:16px;border-radius:100%;inline-size:16px;object-fit:cover;opacity:0}@media not (prefers-reduced-motion){.eddbc0a77c8a621a__avatar img{transition:opacity .1s linear}}.eddbc0a77c8a621a__avatar._5e74dacabd67dfdf__is-loaded img{opacity:1}.c0d40ce1da6dacc2__icon{block-size:24px;display:flex;flex-shrink:0;inline-size:24px}.c0d40ce1da6dacc2__icon svg{fill:currentColor;margin-inline-start:-4px}._9e337be7fe946fd4__name{overflow:hidden;text-overflow:ellipsis}");
}
var style_default3 = { "avatar": "eddbc0a77c8a621a__avatar", "is-loaded": "_5e74dacabd67dfdf__is-loaded", "icon": "c0d40ce1da6dacc2__icon", "name": "_9e337be7fe946fd4__name" };
function getIconForSource(originalSource) {
  switch (originalSource) {
    case "theme":
      return layout_default;
    case "plugin":
      return plugins_default;
    case "site":
      return globe_default;
    default:
      return comment_author_avatar_default;
  }
}
function TemplateAuthorView({ item }) {
  const [isImageLoaded, setIsImageLoaded] = (0, import_element4.useState)(false);
  const originalSource = item.original_source;
  const icon = getIconForSource(originalSource);
  const text = item.author_text;
  const authorId = item.author;
  const imageUrl = (0, import_data.useSelect)(
    (select) => {
      if (originalSource === "site") {
        const siteData = select(import_core_data.store).getEntityRecord("root", "__unstableBase");
        const logoId = siteData?.site_logo;
        if (!logoId) {
          return void 0;
        }
        return select(import_core_data.store).getEntityRecord("postType", "attachment", logoId)?.source_url;
      }
      if (originalSource !== "user" || !authorId) {
        return void 0;
      }
      return select(import_core_data.store).getUser(authorId)?.avatar_urls?.[48];
    },
    [originalSource, authorId]
  );
  return /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(Stack, { direction: "row", align: "center", children: [
    imageUrl && /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
      "div",
      {
        className: clsx_default(style_default3.avatar, {
          [style_default3["is-loaded"]]: isImageLoaded
        }),
        children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
          "img",
          {
            onLoad: () => setIsImageLoaded(true),
            alt: "",
            src: imageUrl
          }
        )
      }
    ),
    !imageUrl && /* @__PURE__ */ (0, import_jsx_runtime6.jsx)("div", { className: style_default3.icon, children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(Icon, { icon }) }),
    /* @__PURE__ */ (0, import_jsx_runtime6.jsx)("span", { className: style_default3.name, children: text })
  ] });
}

// packages/core-fields/build-module/wp_template/author/get-author-elements.mjs
var import_data2 = __toESM(require_data(), 1);
var import_core_data2 = __toESM(require_core_data(), 1);
async function getAuthorElements(postType) {
  const records = await (0, import_data2.resolveSelect)(import_core_data2.store).getEntityRecords(
    "postType",
    postType,
    { per_page: -1, _fields: "id,author_text" }
  );
  const seen = /* @__PURE__ */ new Set();
  const elements = [];
  for (const record of records ?? []) {
    const value = record.author_text;
    if (value && !seen.has(value)) {
      seen.add(value);
      elements.push({ value, label: value });
    }
  }
  return elements;
}

// packages/core-fields/build-module/wp_template/author/field.mjs
var fieldExtensions = {
  getValue: ({ item }) => item.author_text,
  render: TemplateAuthorView,
  getElements: () => getAuthorElements("wp_template")
};

// packages/core-fields/build-module/wp_template/description/utils.mjs
var import_html_entities = __toESM(require_html_entities(), 1);
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
var isCustomTemplate = (item) => item.source === "custom" && !item.has_theme_file && !!item.is_custom;
var getValue = ({ item }) => (0, import_html_entities.decodeEntities)(item.description || "");
var render = ({ item }) => {
  const { description } = item;
  return description ? /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(Text, { children: (0, import_html_entities.decodeEntities)(description) }) : null;
};

// packages/core-fields/build-module/wp_template/description/field.mjs
var fieldExtensions2 = {
  getValue,
  render,
  isVisible: isCustomTemplate
};

// packages/core-fields/build-module/wp_template/description_readonly/field.mjs
var fieldExtensions3 = {
  getValue,
  render,
  // Shown when the description cannot be edited and there is one.
  isVisible: (item) => !isCustomTemplate(item) && !!item.description
};

// packages/core-fields/build-module/wp_template/default_comment_status/field.mjs
var import_i18n = __toESM(require_i18n(), 1);
var fieldExtensions4 = {
  getValue: ({ item }) => item.default_comment_status || "",
  setValue: ({ value }) => ({
    default_comment_status: value || null
  }),
  render: ({ item }) => item.default_comment_status === "open" ? (0, import_i18n.__)("Comments open") : (0, import_i18n.__)("Comments closed")
};

// packages/core-fields/build-module/wp_template/posts_page_title/field.mjs
var fieldExtensions5 = {
  getValue: ({ item }) => {
    const title = item.title;
    if (typeof title === "string") {
      return title;
    }
    return title?.raw ?? "";
  },
  setValue: ({ value }) => ({ title: value })
};

// packages/core-fields/build-module/wp_template/posts_per_page/field.mjs
var fieldExtensions6 = {
  getValue: ({ item }) => item.posts_per_page ?? 1,
  setValue: ({ value }) => ({ posts_per_page: value })
};

// packages/core-fields/build-module/shared/title/get-item-title.mjs
var import_html_entities2 = __toESM(require_html_entities(), 1);
var import_i18n2 = __toESM(require_i18n(), 1);
function getItemTitle(item, fallback = (0, import_i18n2.__)("(no title)")) {
  let title = "";
  if (typeof item.title === "string") {
    title = (0, import_html_entities2.decodeEntities)(item.title);
  } else if (item.title && "rendered" in item.title) {
    title = (0, import_html_entities2.decodeEntities)(item.title.rendered ?? "");
  } else if (item.title && "raw" in item.title) {
    title = (0, import_html_entities2.decodeEntities)(item.title.raw ?? "");
  }
  return title || fallback;
}

// packages/core-fields/build-module/shared/title/view.mjs
var import_i18n3 = __toESM(require_i18n(), 1);
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle4("cd9f81046b", ".f17c7d5418aade3c__title>span:first-child{display:block;flex-grow:0;overflow:hidden;text-decoration:none;text-overflow:ellipsis;white-space:nowrap}._86b9872383a1ab5e__pattern-title>span:first-child{flex:1}");
}
var style_default4 = { "title": "f17c7d5418aade3c__title", "pattern-title": "_86b9872383a1ab5e__pattern-title" };
function BaseTitleView({
  item,
  className,
  children
}) {
  const renderedTitle = getItemTitle(item);
  return /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(
    Stack,
    {
      direction: "row",
      align: "center",
      justify: "flex-start",
      gap: "sm",
      className: clsx_default("fields-field__title", style_default4.title, className),
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime8.jsx)("span", { children: renderedTitle || (0, import_i18n3.__)("(no title)") }),
        children
      ]
    }
  );
}
function TitleView({ item }) {
  return /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(BaseTitleView, { item });
}

// packages/core-fields/build-module/wp_template/title/field.mjs
var fieldExtensions7 = {
  getValue: ({ item }) => getItemTitle(item),
  render: TitleView
};

// packages/core-fields/build-module/wp_template/index.mjs
var fields = {
  author: fieldExtensions,
  description: fieldExtensions2,
  description_readonly: fieldExtensions3,
  posts_page_title: fieldExtensions5,
  posts_per_page: fieldExtensions6,
  default_comment_status: fieldExtensions4,
  title: fieldExtensions7
};
var wp_template_default = fields;
export {
  wp_template_default as default
};
