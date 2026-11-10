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

// package-external:@wordpress/warning
var require_warning = __commonJS({
  "package-external:@wordpress/warning"(exports, module) {
    module.exports = window.wp.warning;
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

// packages/core-fields/build-module/wp_template/author/view.mjs
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

// packages/ui/build-module/avatar/index.mjs
var avatar_exports = {};
__export(avatar_exports, {
  Fallback: () => Fallback,
  Image: () => Image,
  Initials: () => Initials,
  Root: () => Root
});

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/index.parts.mjs
var index_parts_exports = {};
__export(index_parts_exports, {
  Fallback: () => AvatarFallback,
  Image: () => AvatarImage,
  Root: () => AvatarRoot
});

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/root/AvatarRoot.mjs
var React6 = __toESM(require_react(), 1);

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

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/root/AvatarRootContext.mjs
var React5 = __toESM(require_react(), 1);
var AvatarRootContext = /* @__PURE__ */ React5.createContext(void 0);
if (true) AvatarRootContext.displayName = "AvatarRootContext";
function useAvatarRootContext() {
  const context = React5.useContext(AvatarRootContext);
  if (context === void 0) {
    throw new Error(true ? "Base UI: AvatarRootContext is missing. Avatar parts must be placed within <Avatar.Root>." : formatErrorMessage_default(13));
  }
  return context;
}

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/root/stateAttributesMapping.mjs
var avatarStateAttributesMapping = {
  imageLoadingStatus: () => null
};

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/root/AvatarRoot.mjs
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
var AvatarRoot = /* @__PURE__ */ React6.forwardRef(function AvatarRoot2(componentProps, forwardedRef) {
  const {
    className,
    render: render2,
    style,
    ...elementProps
  } = componentProps;
  const [imageLoadingStatus, setImageLoadingStatus] = React6.useState("idle");
  const state = {
    imageLoadingStatus
  };
  const contextValue = React6.useMemo(() => ({
    imageLoadingStatus,
    setImageLoadingStatus
  }), [imageLoadingStatus, setImageLoadingStatus]);
  const element = useRenderElement("span", componentProps, {
    state,
    ref: forwardedRef,
    props: elementProps,
    stateAttributesMapping: avatarStateAttributesMapping
  });
  return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(AvatarRootContext.Provider, {
    value: contextValue,
    children: element
  });
});
if (true) AvatarRoot.displayName = "AvatarRoot";

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/image/AvatarImage.mjs
var React13 = __toESM(require_react(), 1);

// node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/safeReact.mjs
var React7 = __toESM(require_react(), 1);
var SafeReact = {
  ...React7
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
var React8 = __toESM(require_react(), 1);
var noop = () => {
};
var useIsoLayoutEffect = typeof document !== "undefined" ? React8.useLayoutEffect : noop;

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useOpenChangeComplete.mjs
var React10 = __toESM(require_react(), 1);

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useAnimationsFinished.mjs
var ReactDOM = __toESM(require_react_dom(), 1);

// node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useOnMount.mjs
var React9 = __toESM(require_react(), 1);
function useOnMount(fn) {
  React9.useEffect(fn, EMPTY_ARRAY);
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
    const index = id - this.startId;
    if (index < 0 || index >= this.callbacks.length) {
      return;
    }
    if (this.callbacks[index] === null) {
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
    open,
    ref,
    batch = false,
    onComplete: onCompleteParam
  } = parameters;
  const onComplete = useStableCallback(onCompleteParam);
  const runOnceAnimationsFinish = useAnimationsFinished(ref, open, batch);
  React10.useEffect(() => {
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
var React11 = __toESM(require_react(), 1);
function useTransitionStatus(open, enableIdleState = false, deferEndingState = false, animateInitialOpen = false) {
  const [transitionStatus, setTransitionStatus] = React11.useState(open && enableIdleState ? "idle" : void 0);
  const [mounted, setMounted] = React11.useState(open && !animateInitialOpen);
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
    if (!open || enableIdleState || transitionStatus === void 0) {
      return void 0;
    }
    const frame = AnimationFrame.request(() => {
      setTransitionStatus(void 0);
    });
    return () => {
      AnimationFrame.cancel(frame);
    };
  }, [enableIdleState, open, transitionStatus]);
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

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/image/useImageLoadingStatus.mjs
var React12 = __toESM(require_react(), 1);
function useImageLoadingStatus(src, {
  referrerPolicy,
  crossOrigin,
  sizes,
  srcSet
}, enabled) {
  const state = React12.useState("idle");
  const setLoadingStatus = state[1];
  useIsoLayoutEffect(() => {
    if (!enabled) {
      return NOOP;
    }
    if (!src && !srcSet) {
      setLoadingStatus("error");
      return NOOP;
    }
    let isMounted = true;
    const image = new window.Image();
    const updateStatus = (status) => () => {
      if (!isMounted) {
        return;
      }
      setLoadingStatus(status);
    };
    setLoadingStatus("loading");
    image.onload = updateStatus("loaded");
    image.onerror = updateStatus("error");
    if (referrerPolicy) {
      image.referrerPolicy = referrerPolicy;
    }
    image.crossOrigin = crossOrigin ?? null;
    if (sizes) {
      image.sizes = sizes;
    }
    if (srcSet) {
      image.srcset = srcSet;
    }
    if (src) {
      image.src = src;
    }
    if (image.complete) {
      setLoadingStatus(image.naturalWidth > 0 ? "loaded" : "error");
    }
    return () => {
      isMounted = false;
    };
  }, [enabled, src, srcSet, sizes, crossOrigin, referrerPolicy, setLoadingStatus]);
  return state;
}

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/image/AvatarImage.mjs
var stateAttributesMapping = {
  ...avatarStateAttributesMapping,
  ...transitionStatusMapping
};
var AvatarImage = /* @__PURE__ */ React13.forwardRef(function AvatarImage2(componentProps, forwardedRef) {
  const {
    className,
    render: render2,
    onLoadingStatusChange: onLoadingStatusChangeProp,
    keepMounted = false,
    style,
    // Split out so they can be applied after every other prop. React 17 and 18 set attributes in
    // props order, and Safari and Firefox start fetching as soon as `src` lands, ignoring a
    // `loading` or `srcSet` that arrives after it. React 19 orders these itself.
    sizes,
    srcSet,
    src,
    ...elementProps
  } = componentProps;
  const {
    setImageLoadingStatus: setRootImageLoadingStatus
  } = useAvatarRootContext();
  const [imageLoadingStatus, setImageLoadingStatus] = useImageLoadingStatus(src, componentProps, !keepMounted);
  const isVisible = imageLoadingStatus === "loaded";
  const {
    mounted,
    transitionStatus,
    setMounted
  } = useTransitionStatus(isVisible);
  const imageRef = React13.useRef(null);
  const initialCommitRef = React13.useRef(true);
  useIsoLayoutEffect(() => {
    if (!keepMounted) {
      return;
    }
    const isInitialCommit = initialCommitRef.current;
    initialCommitRef.current = false;
    const image = imageRef.current;
    if (!image) {
      return;
    }
    if (!image.complete) {
      setImageLoadingStatus("loading");
      return;
    }
    const status = image.naturalWidth > 0 ? "loaded" : "error";
    setImageLoadingStatus(status);
    if (status === "loaded" && isInitialCommit) {
      setMounted(true);
    }
  }, [keepMounted, src, srcSet, sizes, elementProps.crossOrigin, elementProps.referrerPolicy, render2, setImageLoadingStatus, setMounted]);
  const renderedStatusProps = keepMounted ? {
    // Presence no longer implies the image loaded, so the not-loaded states need their own
    // styling hooks. Scoped to `keepMounted` so the default mode, where the element only
    // exists once loaded, doesn't pick them up while it animates out.
    "data-loading": imageLoadingStatus === "loading" ? "" : void 0,
    "data-error": imageLoadingStatus === "error" ? "" : void 0,
    // Until the image is displayable, the fallback owns the accessible name; without this
    // both would be exposed to assistive technology at once (including in server HTML).
    "aria-hidden": imageLoadingStatus !== "loaded" || void 0,
    onLoad() {
      setImageLoadingStatus("loaded");
    },
    onError() {
      setImageLoadingStatus("error");
    }
  } : void 0;
  const handleLoadingStatusChange = useStableCallback((status) => {
    onLoadingStatusChangeProp?.(status);
    setRootImageLoadingStatus(status);
  });
  useIsoLayoutEffect(() => {
    if (imageLoadingStatus !== "idle") {
      handleLoadingStatusChange(imageLoadingStatus);
    }
  }, [imageLoadingStatus, handleLoadingStatusChange]);
  useIsoLayoutEffect(() => {
    return () => setRootImageLoadingStatus("idle");
  }, [setRootImageLoadingStatus]);
  useOpenChangeComplete({
    enabled: !isVisible,
    open: isVisible,
    ref: imageRef,
    onComplete() {
      if (!isVisible) {
        setMounted(false);
      }
    }
  });
  const state = {
    imageLoadingStatus,
    // The element never unmounts with `keepMounted`, so an exit transition would play and then
    // reverse itself once the status is cleared. `data-loading`/`data-error` cover that state.
    transitionStatus: keepMounted && transitionStatus === "ending" ? void 0 : transitionStatus
  };
  const shouldRender = keepMounted || mounted;
  const sourceProps = {};
  if (sizes !== void 0) {
    sourceProps.sizes = sizes;
  }
  if (srcSet !== void 0) {
    sourceProps.srcSet = srcSet;
  }
  if (src !== void 0) {
    sourceProps.src = src;
  }
  const element = useRenderElement("img", componentProps, {
    state,
    ref: [forwardedRef, imageRef],
    props: [renderedStatusProps, elementProps, sourceProps],
    stateAttributesMapping,
    enabled: shouldRender
  });
  if (!shouldRender) {
    return null;
  }
  return element;
});
if (true) AvatarImage.displayName = "AvatarImage";

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/fallback/AvatarFallback.mjs
var React14 = __toESM(require_react(), 1);

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

// node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/avatar/fallback/AvatarFallback.mjs
var AvatarFallback = /* @__PURE__ */ React14.forwardRef(function AvatarFallback2(componentProps, forwardedRef) {
  const {
    className,
    render: render2,
    delay = 0,
    style,
    ...elementProps
  } = componentProps;
  const {
    imageLoadingStatus
  } = useAvatarRootContext();
  const [delayPassed, setDelayPassed] = React14.useState(delay === 0);
  const timeout = useTimeout();
  React14.useEffect(() => {
    if (delay > 0) {
      timeout.start(delay, () => setDelayPassed(true));
    } else {
      setDelayPassed(true);
    }
    return timeout.clear;
  }, [timeout, delay]);
  const state = {
    imageLoadingStatus
  };
  const element = useRenderElement("span", componentProps, {
    state,
    ref: forwardedRef,
    props: elementProps,
    stateAttributesMapping: avatarStateAttributesMapping,
    enabled: imageLoadingStatus !== "loaded" && (delay === 0 || delayPassed)
  });
  return element;
});
if (true) AvatarFallback.displayName = "AvatarFallback";

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

// packages/ui/build-module/avatar/root.mjs
var import_element = __toESM(require_element(), 1);

// packages/ui/build-module/avatar/dev-warnings.mjs
var import_warning = __toESM(require_warning(), 1);
function warnAvatarColor(color) {
  if (color === void 0) {
    return;
  }
  const tokens = color.match(
    /\/\*[\s\S]*?(?:\*\/|$)|"(?:\\[\s\S]|[^"\\])*"|'(?:\\[\s\S]|[^'\\])*'|(?:[-\w\u0080-\uFFFF]|\\(?:[\da-fA-F]{1,6}[ \t\r\n\f]?|[^\r\n\f]))+/g
  );
  if (tokens?.some((token) => token.toLowerCase() === "currentcolor")) {
    (0, import_warning.default)(
      "Avatar.Root: `color` contains `currentColor`, which can produce insufficient contrast in colored fallbacks. Use a color that does not depend on the foreground."
    );
    return;
  }
  if (typeof CSS === "undefined" || !CSS.supports) {
    return;
  }
  if (!CSS.supports("color", color)) {
    (0, import_warning.default)(
      "Avatar.Root: The browser does not support the `color` value. Use a valid CSS color supported by this browser."
    );
  } else if (
    // Browsers without relative colors retain the neutral fallback.
    CSS.supports("color", "rgb(from red r g b / 1)") && !CSS.supports("color", `rgb(from ${color} r g b / 1)`)
  ) {
    (0, import_warning.default)(
      "Avatar.Root: `color` cannot be used as a colored fallback fill. Use an explicit CSS color instead of a CSS-wide keyword such as `inherit`."
    );
  }
}

// packages/ui/build-module/avatar/root.mjs
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle("37477070fb", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.ccc29f9575c4c758__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
}
var resets_default = { "box-sizing": "ccc29f9575c4c758__box-sizing" };
if (typeof process === "undefined" || true) {
  registerStyle("726f348f3f", "._78c10a0773e51a91__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}._5b33aa2d48624b4f__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._980b3b4776ac8f81__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._43e478b4279a076c__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._698099e25b0fb1a6__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6)._326aee4184d261fd__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}.e9576240835601ba__a,.e9576240835601ba__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}._3f05789653864a73__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._3c72ff72abe57859__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._79d16267c15d9eb2__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
}
var global_css_defense_default = { "button": "_78c10a0773e51a91__button", "input": "_5b33aa2d48624b4f__input", "textarea": "_980b3b4776ac8f81__textarea", "div": "_43e478b4279a076c__div", "p": "_698099e25b0fb1a6__p", "heading": "_326aee4184d261fd__heading", "a": "e9576240835601ba__a", "ol": "_3f05789653864a73__ol", "li": "_3c72ff72abe57859__li", "span": "_79d16267c15d9eb2__span" };
if (typeof process === "undefined" || true) {
  registerStyle("2657a3ab0c", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.a9728400af96db77__root{--_wp-ui-avatar-size:var(--wpds-dimension-size-md,32px);--_gcd-span-position:relative;--_gcd-span-display:inline-flex;--_gcd-span-font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);--_gcd-span-font-size:var(--_wp-ui-avatar-font-size);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-sm,12px);align-items:center;background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);block-size:var(--_wp-ui-avatar-size);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:50%;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:inline-flex;flex-shrink:0;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--_wp-ui-avatar-font-size);font-weight:var(--wpds-typography-font-weight-emphasis,600);inline-size:var(--_wp-ui-avatar-size);justify-content:center;line-height:1;position:relative;vertical-align:middle}._81a33c129d7cbe31__has-color:after{border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--_wp-ui-avatar-color);border-radius:inherit;content:"";inset:calc(var(--wpds-border-width-xs, 1px)*-1);pointer-events:none;position:absolute}._32e65dea77285874__is-square{border-radius:var(--wpds-border-radius-sm,2px)}._7d2020337343df5a__is-xs{--_wp-ui-avatar-size:var(--wpds-dimension-size-2xs,16px);--_wp-ui-avatar-font-size:calc(var(--_wp-ui-avatar-size)/2)}._216fb78a075c5ab4__is-sm{--_wp-ui-avatar-size:var(--wpds-dimension-size-sm,24px);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-xs,11px)}.b552ab04a3b94ae9__is-lg{--_wp-ui-avatar-size:var(--wpds-dimension-size-lg,40px);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-md,13px)}.f8a7646462ff80d1__is-xl{--_wp-ui-avatar-size:calc(var(--wpds-dimension-size-md, 32px)*2);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-xl,20px)}._411c43ac028fe4be__image,._7883f856336224e2__fallback{block-size:100%;border-radius:inherit;inline-size:100%;inset:0;position:absolute}._411c43ac028fe4be__image{object-fit:cover;@media not (prefers-reduced-motion){transition:opacity var(--wpds-motion-duration-sm,.1s) var(--wpds-motion-easing-subtle,cubic-bezier(.15,0,.15,1));&[data-error],&[data-loading],&[data-starting-style]{opacity:0}}&[data-ending-style],&[data-error],&[data-loading]{visibility:hidden}}._7883f856336224e2__fallback{--_gcd-span-position:absolute;--_gcd-span-display:flex;align-items:center;display:flex;justify-content:center;overflow:hidden;white-space:nowrap;>svg{block-size:100%;flex-shrink:0;inline-size:100%}>.dc9b5131d4746aa6__person-icon{block-size:110%;color:var(--wpds-color-stroke-surface-neutral-strong,#8d8d8d);inline-size:110%;translate:0 15%}}@media not (forced-colors:active){@supports (color:rgb(from green r g b/1)) and (color:color(from green srgb-linear round(up,r) g b/1)){._81a33c129d7cbe31__has-color:has(>._7883f856336224e2__fallback){border:none}._81a33c129d7cbe31__has-color:has(>._7883f856336224e2__fallback):after{content:none}._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback{--_wp-ui-avatar-background:rgb(from var(--_wp-ui-avatar-color) clamp(0,r,255) clamp(0,g,255) clamp(0,b,255)/1);--_wp-ui-avatar-contrast-target:4.55;background-color:var(--_wp-ui-avatar-background)}._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback>.dc9b5131d4746aa6__person-icon{--_wp-ui-avatar-contrast-target:3.05}._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback,._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback>.dc9b5131d4746aa6__person-icon{--_wp-ui-avatar-luminance:calc(0.2126 * r + 0.7152 * g + 0.0722 * b);--_wp-ui-avatar-light-foreground:round(up,clamp(0,calc(0.17913 - var(--_wp-ui-avatar-luminance)),1));--_wp-ui-avatar-dark-tint:clamp(0,calc(((var(--_wp-ui-avatar-luminance) + 0.05)/var(--_wp-ui-avatar-contrast-target) - 0.05)/max(0.000001, var(--_wp-ui-avatar-luminance))),1);--_wp-ui-avatar-light-tint:clamp(0,calc((1.05 - var(--_wp-ui-avatar-contrast-target)*(var(--_wp-ui-avatar-luminance) + 0.05))/max(0.000001, 1 - var(--_wp-ui-avatar-luminance))),1);--_wp-ui-avatar-tint:calc(var(--_wp-ui-avatar-light-foreground)*var(--_wp-ui-avatar-light-tint) + (1 - var(--_wp-ui-avatar-light-foreground))*var(--_wp-ui-avatar-dark-tint));color:color(from var(--_wp-ui-avatar-background) srgb-linear calc(var(--_wp-ui-avatar-light-foreground) + (r - var(--_wp-ui-avatar-light-foreground)) * var(--_wp-ui-avatar-tint)) calc(var(--_wp-ui-avatar-light-foreground) + (g - var(--_wp-ui-avatar-light-foreground)) * var(--_wp-ui-avatar-tint)) calc(var(--_wp-ui-avatar-light-foreground) + (b - var(--_wp-ui-avatar-light-foreground)) * var(--_wp-ui-avatar-tint))/1)}}}}}');
}
var style_default = { "root": "a9728400af96db77__root", "has-color": "_81a33c129d7cbe31__has-color", "is-square": "_32e65dea77285874__is-square", "is-xs": "_7d2020337343df5a__is-xs", "is-sm": "_216fb78a075c5ab4__is-sm", "is-lg": "b552ab04a3b94ae9__is-lg", "is-xl": "f8a7646462ff80d1__is-xl", "image": "_411c43ac028fe4be__image", "fallback": "_7883f856336224e2__fallback", "person-icon": "dc9b5131d4746aa6__person-icon" };
var Root = (0, import_element.forwardRef)(
  function AvatarRoot3({ size = "md", shape = "circle", color, className, style, ...props }, ref) {
    if (true) {
      warnAvatarColor(color);
    }
    const customProperties = color === void 0 ? style : { ...style, "--_wp-ui-avatar-color": color };
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
      index_parts_exports.Root,
      {
        ref,
        style: customProperties,
        className: clsx_default(
          resets_default["box-sizing"],
          global_css_defense_default.span,
          style_default.root,
          style_default[`is-${size}`],
          shape === "square" && style_default["is-square"],
          color !== void 0 && style_default["has-color"],
          className
        ),
        ...props
      }
    );
  }
);

// packages/ui/build-module/avatar/image.mjs
var import_element2 = __toESM(require_element(), 1);
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle2("2657a3ab0c", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.a9728400af96db77__root{--_wp-ui-avatar-size:var(--wpds-dimension-size-md,32px);--_gcd-span-position:relative;--_gcd-span-display:inline-flex;--_gcd-span-font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);--_gcd-span-font-size:var(--_wp-ui-avatar-font-size);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-sm,12px);align-items:center;background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);block-size:var(--_wp-ui-avatar-size);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:50%;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:inline-flex;flex-shrink:0;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--_wp-ui-avatar-font-size);font-weight:var(--wpds-typography-font-weight-emphasis,600);inline-size:var(--_wp-ui-avatar-size);justify-content:center;line-height:1;position:relative;vertical-align:middle}._81a33c129d7cbe31__has-color:after{border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--_wp-ui-avatar-color);border-radius:inherit;content:"";inset:calc(var(--wpds-border-width-xs, 1px)*-1);pointer-events:none;position:absolute}._32e65dea77285874__is-square{border-radius:var(--wpds-border-radius-sm,2px)}._7d2020337343df5a__is-xs{--_wp-ui-avatar-size:var(--wpds-dimension-size-2xs,16px);--_wp-ui-avatar-font-size:calc(var(--_wp-ui-avatar-size)/2)}._216fb78a075c5ab4__is-sm{--_wp-ui-avatar-size:var(--wpds-dimension-size-sm,24px);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-xs,11px)}.b552ab04a3b94ae9__is-lg{--_wp-ui-avatar-size:var(--wpds-dimension-size-lg,40px);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-md,13px)}.f8a7646462ff80d1__is-xl{--_wp-ui-avatar-size:calc(var(--wpds-dimension-size-md, 32px)*2);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-xl,20px)}._411c43ac028fe4be__image,._7883f856336224e2__fallback{block-size:100%;border-radius:inherit;inline-size:100%;inset:0;position:absolute}._411c43ac028fe4be__image{object-fit:cover;@media not (prefers-reduced-motion){transition:opacity var(--wpds-motion-duration-sm,.1s) var(--wpds-motion-easing-subtle,cubic-bezier(.15,0,.15,1));&[data-error],&[data-loading],&[data-starting-style]{opacity:0}}&[data-ending-style],&[data-error],&[data-loading]{visibility:hidden}}._7883f856336224e2__fallback{--_gcd-span-position:absolute;--_gcd-span-display:flex;align-items:center;display:flex;justify-content:center;overflow:hidden;white-space:nowrap;>svg{block-size:100%;flex-shrink:0;inline-size:100%}>.dc9b5131d4746aa6__person-icon{block-size:110%;color:var(--wpds-color-stroke-surface-neutral-strong,#8d8d8d);inline-size:110%;translate:0 15%}}@media not (forced-colors:active){@supports (color:rgb(from green r g b/1)) and (color:color(from green srgb-linear round(up,r) g b/1)){._81a33c129d7cbe31__has-color:has(>._7883f856336224e2__fallback){border:none}._81a33c129d7cbe31__has-color:has(>._7883f856336224e2__fallback):after{content:none}._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback{--_wp-ui-avatar-background:rgb(from var(--_wp-ui-avatar-color) clamp(0,r,255) clamp(0,g,255) clamp(0,b,255)/1);--_wp-ui-avatar-contrast-target:4.55;background-color:var(--_wp-ui-avatar-background)}._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback>.dc9b5131d4746aa6__person-icon{--_wp-ui-avatar-contrast-target:3.05}._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback,._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback>.dc9b5131d4746aa6__person-icon{--_wp-ui-avatar-luminance:calc(0.2126 * r + 0.7152 * g + 0.0722 * b);--_wp-ui-avatar-light-foreground:round(up,clamp(0,calc(0.17913 - var(--_wp-ui-avatar-luminance)),1));--_wp-ui-avatar-dark-tint:clamp(0,calc(((var(--_wp-ui-avatar-luminance) + 0.05)/var(--_wp-ui-avatar-contrast-target) - 0.05)/max(0.000001, var(--_wp-ui-avatar-luminance))),1);--_wp-ui-avatar-light-tint:clamp(0,calc((1.05 - var(--_wp-ui-avatar-contrast-target)*(var(--_wp-ui-avatar-luminance) + 0.05))/max(0.000001, 1 - var(--_wp-ui-avatar-luminance))),1);--_wp-ui-avatar-tint:calc(var(--_wp-ui-avatar-light-foreground)*var(--_wp-ui-avatar-light-tint) + (1 - var(--_wp-ui-avatar-light-foreground))*var(--_wp-ui-avatar-dark-tint));color:color(from var(--_wp-ui-avatar-background) srgb-linear calc(var(--_wp-ui-avatar-light-foreground) + (r - var(--_wp-ui-avatar-light-foreground)) * var(--_wp-ui-avatar-tint)) calc(var(--_wp-ui-avatar-light-foreground) + (g - var(--_wp-ui-avatar-light-foreground)) * var(--_wp-ui-avatar-tint)) calc(var(--_wp-ui-avatar-light-foreground) + (b - var(--_wp-ui-avatar-light-foreground)) * var(--_wp-ui-avatar-tint))/1)}}}}}');
}
var style_default2 = { "root": "a9728400af96db77__root", "has-color": "_81a33c129d7cbe31__has-color", "is-square": "_32e65dea77285874__is-square", "is-xs": "_7d2020337343df5a__is-xs", "is-sm": "_216fb78a075c5ab4__is-sm", "is-lg": "b552ab04a3b94ae9__is-lg", "is-xl": "f8a7646462ff80d1__is-xl", "image": "_411c43ac028fe4be__image", "fallback": "_7883f856336224e2__fallback", "person-icon": "dc9b5131d4746aa6__person-icon" };
var Image = (0, import_element2.forwardRef)(
  function AvatarImage3({ className, ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
      index_parts_exports.Image,
      {
        ref,
        className: clsx_default(style_default2.image, className),
        ...props
      }
    );
  }
);

// packages/ui/build-module/avatar/fallback.mjs
var import_element5 = __toESM(require_element(), 1);
var import_primitives6 = __toESM(require_primitives(), 1);

// packages/ui/build-module/icon/icon.mjs
var import_element3 = __toESM(require_element(), 1);
var import_primitives5 = __toESM(require_primitives(), 1);
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
var Icon = (0, import_element3.forwardRef)(
  function UnforwardedIcon({ icon, size = 24, style, ...restProps }, ref) {
    const mergedStyle = icon.props.style || style ? { ...icon.props.style, ...style } : void 0;
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
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

// packages/ui/build-module/avatar/fallback-with-loading-delay.mjs
var import_element4 = __toESM(require_element(), 1);
var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
function FallbackElement({
  elementProps,
  render: render2,
  state,
  enabled
}) {
  return useRender({
    defaultTagName: "span",
    // Forward the original state without adding useRender's automatic data attributes.
    render: typeof render2 === "function" ? (props) => render2(props, state) : render2,
    props: { ...elementProps },
    enabled
  });
}
var FallbackWithLoadingDelay = (0, import_element4.forwardRef)(function AvatarFallbackWithLoadingDelay({ delay = 0, render: render2, ...props }, ref) {
  const [delayPassed, setDelayPassed] = (0, import_element4.useState)(delay === 0);
  (0, import_element4.useEffect)(() => {
    if (delay === 0) {
      setDelayPassed(true);
      return;
    }
    const timeout = setTimeout(() => setDelayPassed(true), delay);
    return () => clearTimeout(timeout);
  }, [delay]);
  return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
    index_parts_exports.Fallback,
    {
      ...props,
      ref,
      delay: 0,
      render: (elementProps, state) => /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        FallbackElement,
        {
          elementProps,
          render: render2,
          state,
          enabled: state.imageLoadingStatus !== "loading" || delayPassed
        }
      )
    }
  );
});

// packages/ui/build-module/avatar/fallback.mjs
var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle3("726f348f3f", "._78c10a0773e51a91__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}._5b33aa2d48624b4f__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._980b3b4776ac8f81__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._43e478b4279a076c__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._698099e25b0fb1a6__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6)._326aee4184d261fd__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}.e9576240835601ba__a,.e9576240835601ba__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}._3f05789653864a73__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._3c72ff72abe57859__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._79d16267c15d9eb2__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
}
var global_css_defense_default2 = { "button": "_78c10a0773e51a91__button", "input": "_5b33aa2d48624b4f__input", "textarea": "_980b3b4776ac8f81__textarea", "div": "_43e478b4279a076c__div", "p": "_698099e25b0fb1a6__p", "heading": "_326aee4184d261fd__heading", "a": "e9576240835601ba__a", "ol": "_3f05789653864a73__ol", "li": "_3c72ff72abe57859__li", "span": "_79d16267c15d9eb2__span" };
if (typeof process === "undefined" || true) {
  registerStyle3("2657a3ab0c", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.a9728400af96db77__root{--_wp-ui-avatar-size:var(--wpds-dimension-size-md,32px);--_gcd-span-position:relative;--_gcd-span-display:inline-flex;--_gcd-span-font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);--_gcd-span-font-size:var(--_wp-ui-avatar-font-size);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-sm,12px);align-items:center;background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);block-size:var(--_wp-ui-avatar-size);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:50%;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:inline-flex;flex-shrink:0;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--_wp-ui-avatar-font-size);font-weight:var(--wpds-typography-font-weight-emphasis,600);inline-size:var(--_wp-ui-avatar-size);justify-content:center;line-height:1;position:relative;vertical-align:middle}._81a33c129d7cbe31__has-color:after{border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--_wp-ui-avatar-color);border-radius:inherit;content:"";inset:calc(var(--wpds-border-width-xs, 1px)*-1);pointer-events:none;position:absolute}._32e65dea77285874__is-square{border-radius:var(--wpds-border-radius-sm,2px)}._7d2020337343df5a__is-xs{--_wp-ui-avatar-size:var(--wpds-dimension-size-2xs,16px);--_wp-ui-avatar-font-size:calc(var(--_wp-ui-avatar-size)/2)}._216fb78a075c5ab4__is-sm{--_wp-ui-avatar-size:var(--wpds-dimension-size-sm,24px);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-xs,11px)}.b552ab04a3b94ae9__is-lg{--_wp-ui-avatar-size:var(--wpds-dimension-size-lg,40px);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-md,13px)}.f8a7646462ff80d1__is-xl{--_wp-ui-avatar-size:calc(var(--wpds-dimension-size-md, 32px)*2);--_wp-ui-avatar-font-size:var(--wpds-typography-font-size-xl,20px)}._411c43ac028fe4be__image,._7883f856336224e2__fallback{block-size:100%;border-radius:inherit;inline-size:100%;inset:0;position:absolute}._411c43ac028fe4be__image{object-fit:cover;@media not (prefers-reduced-motion){transition:opacity var(--wpds-motion-duration-sm,.1s) var(--wpds-motion-easing-subtle,cubic-bezier(.15,0,.15,1));&[data-error],&[data-loading],&[data-starting-style]{opacity:0}}&[data-ending-style],&[data-error],&[data-loading]{visibility:hidden}}._7883f856336224e2__fallback{--_gcd-span-position:absolute;--_gcd-span-display:flex;align-items:center;display:flex;justify-content:center;overflow:hidden;white-space:nowrap;>svg{block-size:100%;flex-shrink:0;inline-size:100%}>.dc9b5131d4746aa6__person-icon{block-size:110%;color:var(--wpds-color-stroke-surface-neutral-strong,#8d8d8d);inline-size:110%;translate:0 15%}}@media not (forced-colors:active){@supports (color:rgb(from green r g b/1)) and (color:color(from green srgb-linear round(up,r) g b/1)){._81a33c129d7cbe31__has-color:has(>._7883f856336224e2__fallback){border:none}._81a33c129d7cbe31__has-color:has(>._7883f856336224e2__fallback):after{content:none}._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback{--_wp-ui-avatar-background:rgb(from var(--_wp-ui-avatar-color) clamp(0,r,255) clamp(0,g,255) clamp(0,b,255)/1);--_wp-ui-avatar-contrast-target:4.55;background-color:var(--_wp-ui-avatar-background)}._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback>.dc9b5131d4746aa6__person-icon{--_wp-ui-avatar-contrast-target:3.05}._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback,._81a33c129d7cbe31__has-color>._7883f856336224e2__fallback>.dc9b5131d4746aa6__person-icon{--_wp-ui-avatar-luminance:calc(0.2126 * r + 0.7152 * g + 0.0722 * b);--_wp-ui-avatar-light-foreground:round(up,clamp(0,calc(0.17913 - var(--_wp-ui-avatar-luminance)),1));--_wp-ui-avatar-dark-tint:clamp(0,calc(((var(--_wp-ui-avatar-luminance) + 0.05)/var(--_wp-ui-avatar-contrast-target) - 0.05)/max(0.000001, var(--_wp-ui-avatar-luminance))),1);--_wp-ui-avatar-light-tint:clamp(0,calc((1.05 - var(--_wp-ui-avatar-contrast-target)*(var(--_wp-ui-avatar-luminance) + 0.05))/max(0.000001, 1 - var(--_wp-ui-avatar-luminance))),1);--_wp-ui-avatar-tint:calc(var(--_wp-ui-avatar-light-foreground)*var(--_wp-ui-avatar-light-tint) + (1 - var(--_wp-ui-avatar-light-foreground))*var(--_wp-ui-avatar-dark-tint));color:color(from var(--_wp-ui-avatar-background) srgb-linear calc(var(--_wp-ui-avatar-light-foreground) + (r - var(--_wp-ui-avatar-light-foreground)) * var(--_wp-ui-avatar-tint)) calc(var(--_wp-ui-avatar-light-foreground) + (g - var(--_wp-ui-avatar-light-foreground)) * var(--_wp-ui-avatar-tint)) calc(var(--_wp-ui-avatar-light-foreground) + (b - var(--_wp-ui-avatar-light-foreground)) * var(--_wp-ui-avatar-tint))/1)}}}}}');
}
var style_default3 = { "root": "a9728400af96db77__root", "has-color": "_81a33c129d7cbe31__has-color", "is-square": "_32e65dea77285874__is-square", "is-xs": "_7d2020337343df5a__is-xs", "is-sm": "_216fb78a075c5ab4__is-sm", "is-lg": "b552ab04a3b94ae9__is-lg", "is-xl": "f8a7646462ff80d1__is-xl", "image": "_411c43ac028fe4be__image", "fallback": "_7883f856336224e2__fallback", "person-icon": "dc9b5131d4746aa6__person-icon" };
var person = /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(
  import_primitives6.SVG,
  {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    style: { fill: "none" },
    stroke: "currentColor",
    strokeWidth: 1.5,
    children: [
      /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives6.Circle, { cx: 12, cy: 8, r: 5 }),
      /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives6.Path, { d: "M20 21a8 8 0 0 0-16 0" })
    ]
  }
);
var Fallback = (0, import_element5.forwardRef)(
  function AvatarFallback3({
    className,
    children = /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
      Icon,
      {
        icon: person,
        role: "presentation",
        className: style_default3["person-icon"]
      }
    ),
    ...props
  }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
      FallbackWithLoadingDelay,
      {
        ref,
        className: clsx_default(
          style_default3.fallback,
          global_css_defense_default2.span,
          className
        ),
        ...props,
        children
      }
    );
  }
);

// packages/ui/build-module/avatar/initials.mjs
var import_element6 = __toESM(require_element(), 1);
var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
var Initials = (0, import_element6.forwardRef)(
  function AvatarInitials({ name, locale, ...props }, ref) {
    const segmenter = new Intl.Segmenter(locale, {
      granularity: "grapheme"
    });
    const words = name?.trim().split(/\s+/) ?? [];
    const initials = words.filter((_, index) => index === 0 || index === words.length - 1).map(
      (word) => segmenter.segment(word).containing(0)?.segment ?? ""
    ).join("").toLocaleUpperCase(locale);
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(Fallback, { ...props, ref, children: initials || void 0 });
  }
);

// packages/ui/build-module/stack/stack.mjs
var import_element7 = __toESM(require_element(), 1);
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
  registerStyle4("67591fa198", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._8e8e79c1a2eec43b__stack{display:flex}}}");
}
var style_default4 = { "stack": "_8e8e79c1a2eec43b__stack" };
var gapTokens = {
  xs: "var(--wpds-dimension-gap-xs, 4px)",
  sm: "var(--wpds-dimension-gap-sm, 8px)",
  md: "var(--wpds-dimension-gap-md, 12px)",
  lg: "var(--wpds-dimension-gap-lg, 16px)",
  xl: "var(--wpds-dimension-gap-xl, 24px)",
  "2xl": "var(--wpds-dimension-gap-2xl, 32px)",
  "3xl": "var(--wpds-dimension-gap-3xl, 40px)"
};
var Stack = (0, import_element7.forwardRef)(
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
        className: style_default4.stack
      })
    });
    return element;
  }
);

// packages/core-fields/build-module/shared/author/avatar.mjs
var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle5("efeba968e6", "._4d84143be9dae462__icon,.dd9b5d817b3207dc__avatar{align-items:center;block-size:1lh;display:flex;flex-shrink:0;inline-size:var(--wpds-dimension-size-2xs,16px)}._4d84143be9dae462__icon svg{fill:currentColor;margin-inline:calc((var(--wpds-dimension-size-2xs, 16px) - var(--wpds-dimension-size-sm, 24px))/2)}.de468aa290326111__name{overflow:hidden;text-overflow:ellipsis}");
}
var style_default5 = { "avatar": "dd9b5d817b3207dc__avatar", "icon": "_4d84143be9dae462__icon", "name": "de468aa290326111__name" };
function AuthorAvatar({ src }) {
  return /* @__PURE__ */ (0, import_jsx_runtime12.jsx)("span", { className: style_default5.avatar, children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(avatar_exports.Root, { size: "xs", "aria-hidden": "true", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(avatar_exports.Image, { src, alt: "", keepMounted: true }) }) });
}

// packages/core-fields/build-module/wp_template/author/view.mjs
var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle6("efeba968e6", "._4d84143be9dae462__icon,.dd9b5d817b3207dc__avatar{align-items:center;block-size:1lh;display:flex;flex-shrink:0;inline-size:var(--wpds-dimension-size-2xs,16px)}._4d84143be9dae462__icon svg{fill:currentColor;margin-inline:calc((var(--wpds-dimension-size-2xs, 16px) - var(--wpds-dimension-size-sm, 24px))/2)}.de468aa290326111__name{overflow:hidden;text-overflow:ellipsis}");
}
var style_default6 = { "avatar": "dd9b5d817b3207dc__avatar", "icon": "_4d84143be9dae462__icon", "name": "de468aa290326111__name" };
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
  return /* @__PURE__ */ (0, import_jsx_runtime13.jsxs)(Stack, { direction: "row", gap: "xs", align: "flex-start", children: [
    imageUrl && /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(AuthorAvatar, { src: imageUrl }),
    !imageUrl && /* @__PURE__ */ (0, import_jsx_runtime13.jsx)("div", { className: style_default6.icon, children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(Icon, { icon }) }),
    /* @__PURE__ */ (0, import_jsx_runtime13.jsx)("span", { className: style_default6.name, children: text })
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
var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
var isCustomTemplate = (item) => item.source === "custom" && !item.has_theme_file && !!item.is_custom;
var getValue = ({ item }) => (0, import_html_entities.decodeEntities)(item.description || "");
var render = ({ item }) => {
  const { description } = item;
  return description ? /* @__PURE__ */ (0, import_jsx_runtime14.jsx)("span", { children: (0, import_html_entities.decodeEntities)(description) }) : null;
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
var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle7("a4585ee5f7", "._6f88745c59f7b1df__title>span:first-child{display:block;flex-grow:0;overflow:hidden;text-decoration:none;text-overflow:ellipsis;white-space:nowrap}.bfcb43aba334e5f0__pattern-title>span:first-child{flex:1}");
}
var style_default7 = { "title": "_6f88745c59f7b1df__title", "pattern-title": "bfcb43aba334e5f0__pattern-title" };
function BaseTitleView({
  item,
  className,
  children
}) {
  const renderedTitle = getItemTitle(item);
  return /* @__PURE__ */ (0, import_jsx_runtime15.jsxs)(
    Stack,
    {
      direction: "row",
      align: "center",
      justify: "flex-start",
      gap: "sm",
      className: clsx_default("fields-field__title", style_default7.title, className),
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime15.jsx)("span", { children: renderedTitle || (0, import_i18n3.__)("(no title)") }),
        children
      ]
    }
  );
}
function TitleView({ item }) {
  return /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(BaseTitleView, { item });
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
