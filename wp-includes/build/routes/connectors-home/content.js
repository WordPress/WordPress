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

// package-external:@wordpress/components
var require_components = __commonJS({
  "package-external:@wordpress/components"(exports, module) {
    module.exports = window.wp.components;
  }
});

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
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

// package-external:@wordpress/primitives
var require_primitives = __commonJS({
  "package-external:@wordpress/primitives"(exports, module) {
    module.exports = window.wp.primitives;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
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

// packages/admin-ui/build-module/navigable-region/index.mjs
var import_element = __toESM(require_element(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
var NavigableRegion = (0, import_element.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
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

// node_modules/@base-ui/react/esm/utils/useRenderElement.js
var React5 = __toESM(require_react(), 1);

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
var React4 = __toESM(require_react(), 1);

// node_modules/@base-ui/utils/esm/reactVersion.js
var React3 = __toESM(require_react(), 1);
var majorVersion = parseInt(React3.version, 10);
function isReactVersionAtLeast(reactVersionToCheck) {
  return majorVersion >= reactVersionToCheck;
}

// node_modules/@base-ui/utils/esm/getReactElementRef.js
function getReactElementRef(element) {
  if (!/* @__PURE__ */ React4.isValidElement(element)) {
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

// node_modules/@base-ui/react/esm/utils/getStateAttributesProps.js
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
  let merged = {
    ...resolvePropsGetter(a, EMPTY_PROPS)
  };
  if (b) {
    merged = mergeOne(merged, b);
  }
  if (c) {
    merged = mergeOne(merged, c);
  }
  if (d) {
    merged = mergeOne(merged, d);
  }
  if (e) {
    merged = mergeOne(merged, e);
  }
  return merged;
}
function mergePropsN(props) {
  if (props.length === 0) {
    return EMPTY_PROPS;
  }
  if (props.length === 1) {
    return resolvePropsGetter(props[0], EMPTY_PROPS);
  }
  let merged = {
    ...resolvePropsGetter(props[0], EMPTY_PROPS)
  };
  for (let i = 1; i < props.length; i += 1) {
    merged = mergeOne(merged, props[i]);
  }
  return merged;
}
function mergeOne(merged, inputProps) {
  if (isPropsGetter(inputProps)) {
    return inputProps(merged);
  }
  return mutablyMergeInto(merged, inputProps);
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
    return theirHandler;
  }
  return (event) => {
    if (isSyntheticEvent(event)) {
      const baseUIEvent = event;
      makeEventPreventable(baseUIEvent);
      const result2 = theirHandler(baseUIEvent);
      if (!baseUIEvent.baseUIHandlerPrevented) {
        ourHandler?.(baseUIEvent);
      }
      return result2;
    }
    const result = theirHandler(event);
    ourHandler?.(event);
    return result;
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

// node_modules/@base-ui/utils/esm/empty.js
var EMPTY_ARRAY = Object.freeze([]);
var EMPTY_OBJECT = Object.freeze({});

// node_modules/@base-ui/react/esm/utils/useRenderElement.js
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
    stateAttributesMapping,
    enabled = true
  } = params;
  const className = enabled ? resolveClassName(classNameProp, state) : void 0;
  const style = enabled ? resolveStyle(styleProp, state) : void 0;
  const stateProps = enabled ? getStateAttributesProps(state, stateAttributesMapping) : EMPTY_OBJECT;
  const outProps = enabled ? mergeObjects(stateProps, Array.isArray(props) ? mergePropsN(props) : props) ?? EMPTY_OBJECT : EMPTY_OBJECT;
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
function evaluateRenderProp(element, render, props, state) {
  if (render) {
    if (typeof render === "function") {
      return render(props, state);
    }
    const mergedProps = mergeProps(props, render.props);
    mergedProps.ref = props.ref;
    return /* @__PURE__ */ React5.cloneElement(render, mergedProps);
  }
  if (element) {
    if (typeof element === "string") {
      return renderTag(element, props);
    }
  }
  throw new Error(true ? "Base UI: Render element or function are not defined." : formatErrorMessage(8));
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
  return /* @__PURE__ */ React5.createElement(Tag, props);
}

// node_modules/@base-ui/react/esm/use-render/useRender.js
function useRender(params) {
  return useRenderElement(params.defaultTagName ?? "div", params, params);
}

// packages/ui/build-module/badge/badge.mjs
var import_element2 = __toESM(require_element(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='244b5c59c0']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "244b5c59c0");
  style.appendChild(document.createTextNode('@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._96e6251aad1a6136__badge{border-radius:var(--wpds-border-radius-lg,8px);font-family:var(--wpds-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-font-size-sm,12px);font-weight:var(--wpds-font-weight-regular,400);line-height:var(--wpds-font-line-height-xs,16px);padding-block:var(--wpds-dimension-padding-xs,4px);padding-inline:var(--wpds-dimension-padding-sm,8px)}._99f7158cb520f750__is-high-intent{background-color:var(--wpds-color-bg-surface-error,#f6e6e3);color:var(--wpds-color-fg-content-error,#470000)}.c20ebef2365bc8b7__is-medium-intent{background-color:var(--wpds-color-bg-surface-warning,#fde6bd);color:var(--wpds-color-fg-content-warning,#2e1900)}._365e1626c6202e52__is-low-intent{background-color:var(--wpds-color-bg-surface-caution,#fee994);color:var(--wpds-color-fg-content-caution,#281d00)}._33f8198127ddf4ef__is-stable-intent{background-color:var(--wpds-color-bg-surface-success,#c5f7cc);color:var(--wpds-color-fg-content-success,#002900)}._04c1aca8fc449412__is-informational-intent{background-color:var(--wpds-color-bg-surface-info,#deebfa);color:var(--wpds-color-fg-content-info,#001b4f)}._90726e69d495ec19__is-draft-intent{background-color:var(--wpds-color-bg-surface-neutral-weak,#f0f0f0);color:var(--wpds-color-fg-content-neutral,#1e1e1e)}._898f4a544993bd39__is-none-intent{background-color:var(--wpds-color-bg-surface-neutral,#f8f8f8);color:var(--wpds-color-fg-content-neutral-weak,#6d6d6d)}}'));
  document.head.appendChild(style);
}
var style_default = { "badge": "_96e6251aad1a6136__badge", "is-high-intent": "_99f7158cb520f750__is-high-intent", "is-medium-intent": "c20ebef2365bc8b7__is-medium-intent", "is-low-intent": "_365e1626c6202e52__is-low-intent", "is-stable-intent": "_33f8198127ddf4ef__is-stable-intent", "is-informational-intent": "_04c1aca8fc449412__is-informational-intent", "is-draft-intent": "_90726e69d495ec19__is-draft-intent", "is-none-intent": "_898f4a544993bd39__is-none-intent" };
var Badge = (0, import_element2.forwardRef)(function Badge2({ children, intent = "none", render, className, ...props }, ref) {
  const element = useRender({
    render,
    defaultTagName: "span",
    ref,
    props: mergeProps(props, {
      className: clsx_default(
        style_default.badge,
        style_default[`is-${intent}-intent`],
        className
      ),
      children
    })
  });
  return element;
});

// packages/ui/build-module/icon/icon.mjs
var import_element3 = __toESM(require_element(), 1);
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var Icon = (0, import_element3.forwardRef)(function Icon2({ icon, size = 24, ...restProps }, ref) {
  return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
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

// packages/icons/build-module/library/caution.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
var caution_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives2.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M5.5 12a6.5 6.5 0 1 0 13 0 6.5 6.5 0 0 0-13 0ZM12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm-.75 12v-1.5h1.5V16h-1.5Zm0-8v5h1.5V8h-1.5Z" }) });

// packages/icons/build-module/library/error.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var error_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives3.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M12.218 5.377a.25.25 0 0 0-.436 0l-7.29 12.96a.25.25 0 0 0 .218.373h14.58a.25.25 0 0 0 .218-.372l-7.29-12.96Zm-1.743-.735c.669-1.19 2.381-1.19 3.05 0l7.29 12.96a1.75 1.75 0 0 1-1.525 2.608H4.71a1.75 1.75 0 0 1-1.525-2.608l7.29-12.96ZM12.75 17.46h-1.5v-1.5h1.5v1.5Zm-1.5-3h1.5v-5h-1.5v5Z" }) });

// packages/icons/build-module/library/info.mjs
var import_primitives4 = __toESM(require_primitives(), 1);
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
var info_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives4.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M5.5 12a6.5 6.5 0 1 0 13 0 6.5 6.5 0 0 0-13 0ZM12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm.75 4v1.5h-1.5V8h1.5Zm0 8v-5h-1.5v5h1.5Z" }) });

// packages/icons/build-module/library/published.mjs
var import_primitives5 = __toESM(require_primitives(), 1);
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
var published_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives5.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M12 18.5a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13ZM4 12a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm11.53-1.47-1.06-1.06L11 12.94l-1.47-1.47-1.06 1.06L11 15.06l4.53-4.53Z" }) });

// packages/ui/build-module/stack/stack.mjs
var import_element4 = __toESM(require_element(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='71d20935c2']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "71d20935c2");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._19ce0419607e1896__stack{display:flex}}"));
  document.head.appendChild(style);
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
var Stack = (0, import_element4.forwardRef)(function Stack2({ direction, gap, align, justify, wrap, render, ...props }, ref) {
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

// packages/ui/build-module/notice/index.mjs
var notice_exports = {};
__export(notice_exports, {
  Description: () => Description,
  Root: () => Root
});

// packages/ui/build-module/notice/root.mjs
var import_element5 = __toESM(require_element(), 1);
import { speak } from "@wordpress/a11y";
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='671ebfc62d']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "671ebfc62d");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}"));
  document.head.appendChild(style);
}
var resets_default = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='a66a881fc5']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "a66a881fc5");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._4145abab73d17514__notice{--icon-height:24px;--text-vertical-padding:calc((var(--icon-height) - var(--wpds-font-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-bg-surface-neutral-weak,#f0f0f0);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#d8d8d8);--wp-ui-notice-text-color:var(--wpds-color-fg-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description{text-wrap:pretty;color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-info-weak,#f2f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#9fbcdc);--wp-ui-notice-text-color:var(--wpds-color-fg-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-warning-weak,#fff7e0);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#d0b381);--wp-ui-notice-text-color:var(--wpds-color-fg-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-warning-weak,#926300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-success-weak,#eaffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#8ac894);--wp-ui-notice-text-color:var(--wpds-color-fg-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-success-weak,#007f30)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-error-weak,#fff6f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#daa39b);--wp-ui-notice-text-color:var(--wpds-color-fg-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-error-weak,#cc1818)}}"));
  document.head.appendChild(style);
}
var style_default3 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "description": "_1904b570a89bb815__description", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error" };
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
    return (0, import_element5.renderToString)(message);
  } catch {
    return void 0;
  }
}
function useSpokenMessage(message, politeness) {
  const spokenMessage = safeRenderToString(message);
  (0, import_element5.useEffect)(() => {
    if (spokenMessage) {
      speak(spokenMessage, politeness);
    }
  }, [spokenMessage, politeness]);
}
var Root = (0, import_element5.forwardRef)(function Notice({
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
    style_default3.notice,
    style_default3[`is-${intent}`],
    resets_default["box-sizing"]
  );
  const element = useRender({
    defaultTagName: "div",
    render,
    ref,
    props: mergeProps(
      {
        className: mergedClassName,
        children: /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(import_jsx_runtime7.Fragment, { children: [
          children,
          iconElement && /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
            Icon,
            {
              className: style_default3.icon,
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

// packages/ui/build-module/notice/description.mjs
var import_element7 = __toESM(require_element(), 1);

// packages/ui/build-module/text/text.mjs
var import_element6 = __toESM(require_element(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='6675f7d310']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "6675f7d310");
  style.appendChild(document.createTextNode('@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._83ed8a8da5dd50ea__text{margin:0}._14437cfb77831647__heading-2xl{font-size:var(--wpds-font-size-2xl,32px);line-height:var(--wpds-font-line-height-2xl,40px)}._14437cfb77831647__heading-2xl,._3c78b7fa9b4072dd__heading-xl{font-family:var(--wpds-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-font-weight-medium,499)}._3c78b7fa9b4072dd__heading-xl{font-size:var(--wpds-font-size-xl,20px);line-height:var(--wpds-font-line-height-md,24px)}.aa58f227716bcde2__heading-lg{font-size:var(--wpds-font-size-lg,15px)}.aa58f227716bcde2__heading-lg,.fc4da56d8dfe52c4__heading-md{font-family:var(--wpds-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-font-weight-medium,499);line-height:var(--wpds-font-line-height-sm,20px)}.fc4da56d8dfe52c4__heading-md{font-size:var(--wpds-font-size-md,13px)}.a9b78c7c82e8dff7__heading-sm{font-family:var(--wpds-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-font-size-xs,11px);font-weight:var(--wpds-font-weight-medium,499);line-height:var(--wpds-font-line-height-xs,16px);text-transform:uppercase}._305ff559e52180d5__body-xl{font-size:var(--wpds-font-size-xl,20px);line-height:var(--wpds-font-line-height-xl,32px)}._305ff559e52180d5__body-xl,.ca1aa3fc2029e958__body-lg{font-family:var(--wpds-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-font-weight-regular,400)}.ca1aa3fc2029e958__body-lg{font-size:var(--wpds-font-size-lg,15px);line-height:var(--wpds-font-line-height-md,24px)}._131101940be12424__body-md{font-size:var(--wpds-font-size-md,13px);line-height:var(--wpds-font-line-height-sm,20px)}._0e8d87a42c1f75fa__body-sm,._131101940be12424__body-md{font-family:var(--wpds-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-font-weight-regular,400)}._0e8d87a42c1f75fa__body-sm{font-size:var(--wpds-font-size-sm,12px);line-height:var(--wpds-font-line-height-xs,16px)}}'));
  document.head.appendChild(style);
}
var style_default4 = { "text": "_83ed8a8da5dd50ea__text", "heading-2xl": "_14437cfb77831647__heading-2xl", "heading-xl": "_3c78b7fa9b4072dd__heading-xl", "heading-lg": "aa58f227716bcde2__heading-lg", "heading-md": "fc4da56d8dfe52c4__heading-md", "heading-sm": "a9b78c7c82e8dff7__heading-sm", "body-xl": "_305ff559e52180d5__body-xl", "body-lg": "ca1aa3fc2029e958__body-lg", "body-md": "_131101940be12424__body-md", "body-sm": "_0e8d87a42c1f75fa__body-sm" };
var Text = (0, import_element6.forwardRef)(function Text2({ variant = "body-md", render, className, ...props }, ref) {
  const element = useRender({
    render,
    defaultTagName: "span",
    ref,
    props: mergeProps(props, {
      className: clsx_default(style_default4.text, style_default4[variant], className)
    })
  });
  return element;
});

// packages/ui/build-module/notice/description.mjs
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='a66a881fc5']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "a66a881fc5");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._4145abab73d17514__notice{--icon-height:24px;--text-vertical-padding:calc((var(--icon-height) - var(--wpds-font-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-bg-surface-neutral-weak,#f0f0f0);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#d8d8d8);--wp-ui-notice-text-color:var(--wpds-color-fg-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description{text-wrap:pretty;color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-info-weak,#f2f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#9fbcdc);--wp-ui-notice-text-color:var(--wpds-color-fg-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-warning-weak,#fff7e0);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#d0b381);--wp-ui-notice-text-color:var(--wpds-color-fg-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-warning-weak,#926300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-success-weak,#eaffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#8ac894);--wp-ui-notice-text-color:var(--wpds-color-fg-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-success-weak,#007f30)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-error-weak,#fff6f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#daa39b);--wp-ui-notice-text-color:var(--wpds-color-fg-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-error-weak,#cc1818)}}"));
  document.head.appendChild(style);
}
var style_default5 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "description": "_1904b570a89bb815__description", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error" };
var Description = (0, import_element7.forwardRef)(
  function NoticeDescription({ className, ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
      Text,
      {
        ref,
        variant: "body-md",
        className: clsx_default(style_default5.description, className),
        ...props
      }
    );
  }
);

// packages/admin-ui/build-module/page/sidebar-toggle-slot.mjs
var import_components = __toESM(require_components(), 1);
var { Fill: SidebarToggleFill, Slot: SidebarToggleSlot } = (0, import_components.createSlotFill)("SidebarToggle");

// packages/admin-ui/build-module/page/header.mjs
var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
function Header({
  headingLevel = 1,
  breadcrumbs,
  badges,
  title,
  subTitle,
  actions,
  showSidebarToggle = true
}) {
  const HeadingTag = `h${headingLevel}`;
  return /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(Stack, { direction: "column", className: "admin-ui-page__header", children: [
    /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(Stack, { direction: "row", justify: "space-between", gap: "sm", children: [
      /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(Stack, { direction: "row", gap: "sm", align: "center", justify: "start", children: [
        showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
          SidebarToggleSlot,
          {
            bubblesVirtually: true,
            className: "admin-ui-page__sidebar-toggle-slot"
          }
        ),
        title && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(HeadingTag, { className: "admin-ui-page__header-title", children: title }),
        breadcrumbs,
        badges
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        Stack,
        {
          direction: "row",
          gap: "sm",
          style: { width: "auto", flexShrink: 0 },
          className: "admin-ui-page__header-actions",
          align: "center",
          children: actions
        }
      )
    ] }),
    subTitle && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)("p", { className: "admin-ui-page__header-subtitle", children: subTitle })
  ] });
}

// packages/admin-ui/build-module/page/index.mjs
var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
function Page({
  headingLevel,
  breadcrumbs,
  badges,
  title,
  subTitle,
  children,
  className,
  actions,
  hasPadding = false,
  showSidebarToggle = true
}) {
  const classes = clsx_default("admin-ui-page", className);
  return /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(navigable_region_default, { className: classes, ariaLabel: title, children: [
    (title || breadcrumbs || badges) && /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
      Header,
      {
        headingLevel,
        breadcrumbs,
        badges,
        title,
        subTitle,
        actions,
        showSidebarToggle
      }
    ),
    hasPadding ? /* @__PURE__ */ (0, import_jsx_runtime10.jsx)("div", { className: "admin-ui-page__content has-padding", children }) : children
  ] });
}
Page.SidebarToggleFill = SidebarToggleFill;
var page_default = Page;

// routes/connectors-home/stage.tsx
var import_components4 = __toESM(require_components());
var import_data4 = __toESM(require_data());
var import_element11 = __toESM(require_element());
var import_i18n4 = __toESM(require_i18n());
var import_core_data3 = __toESM(require_core_data());
import {
  privateApis as connectorsPrivateApis2
} from "@wordpress/connectors";

// routes/connectors-home/style.scss
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='eb5f96e519']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "eb5f96e519");
  style.appendChild(document.createTextNode(".connectors-page{box-sizing:border-box;margin:0 auto;max-width:680px;padding:24px;width:100%}.connectors-page .components-item{background:#fff;border:1px solid #ddd;border-radius:8px;overflow:hidden;padding:20px;scroll-margin-top:120px}.connectors-page .connector-settings__error{color:#cc1818}.connectors-page .connector-settings .components-text-control__input{font-family:monospace;scroll-margin-top:120px}.connectors-page__file-mods-notice{margin-bottom:16px}.connectors-page--empty{align-items:center;display:flex;flex-direction:column;flex-grow:1;gap:32px;justify-content:center;text-align:center}.connectors-page .ai-plugin-callout{background-color:#e7d4e4;background-image:radial-gradient(ellipse 70% 120% at 18% 115%,#ca9ec6bf 0,#ca9ec600 60%),radial-gradient(ellipse 55% 110% at 92% -15%,#d0afd9b3 0,#d0afd900 65%),radial-gradient(ellipse 40% 85% at 58% -10%,#aa82b873 0,#aa82b800 70%);border-radius:8px;overflow:hidden;padding:24px;padding-inline-end:150px;position:relative}[dir=rtl] .connectors-page .ai-plugin-callout{background-image:radial-gradient(ellipse 70% 120% at 82% 115%,#ca9ec6bf 0,#ca9ec600 60%),radial-gradient(ellipse 55% 110% at 8% -15%,#d0afd9b3 0,#d0afd900 65%),radial-gradient(ellipse 40% 85% at 42% -10%,#aa82b873 0,#aa82b800 70%)}.connectors-page .ai-plugin-callout__content{align-items:flex-start;display:flex;flex-direction:column;gap:12px;padding-top:2px}.connectors-page .ai-plugin-callout__content p{font-size:13px;line-height:20px;margin:0}.connectors-page .ai-plugin-callout__decoration{height:110px;inset-inline-end:16px;position:absolute;top:12px;width:110px}.connectors-page>p{color:#949494}@media (max-width:680px){.connectors-page .ai-plugin-callout{padding:12px;padding-inline-end:100px}.connectors-page .ai-plugin-callout__decoration{height:75px;inset-inline-end:8px;top:8px;width:75px}}@media (max-width:480px){.connectors-page{padding:8px}.connectors-page .ai-plugin-callout{padding-inline-end:130px}.connectors-page .components-item{padding:12px}.connectors-page .components-item>.components-v-stack>.components-h-stack:first-child svg{height:32px;width:32px}.connectors-page .components-item>.components-v-stack>.components-h-stack:first-child>.components-h-stack:last-child{align-items:flex-end;flex-direction:column}}"));
  document.head.appendChild(style);
}

// routes/connectors-home/ai-plugin-callout.tsx
var import_components3 = __toESM(require_components());
var import_core_data2 = __toESM(require_core_data());
var import_data3 = __toESM(require_data());
var import_element10 = __toESM(require_element());
var import_i18n3 = __toESM(require_i18n());
var import_notices2 = __toESM(require_notices());
var import_url = __toESM(require_url());

// routes/connectors-home/default-connectors.tsx
var import_components2 = __toESM(require_components());
var import_element9 = __toESM(require_element());
var import_data2 = __toESM(require_data());
var import_i18n2 = __toESM(require_i18n());
import {
  __experimentalRegisterConnector as registerConnector,
  __experimentalConnectorItem as ConnectorItem,
  __experimentalDefaultConnectorSettings as DefaultConnectorSettings,
  privateApis as connectorsPrivateApis
} from "@wordpress/connectors";

// routes/lock-unlock.ts
var import_private_apis = __toESM(require_private_apis());
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/routes"
);

// routes/connectors-home/use-connector-plugin.ts
var import_core_data = __toESM(require_core_data());
var import_data = __toESM(require_data());
var import_element8 = __toESM(require_element());
var import_i18n = __toESM(require_i18n());
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
  const [isExpanded, setIsExpanded] = (0, import_element8.useState)(false);
  const [isBusy, setIsBusy] = (0, import_element8.useState)(false);
  const [connectedState, setConnectedState] = (0, import_element8.useState)(initialIsConnected);
  const [pluginStatusOverride, setPluginStatusOverride] = (0, import_element8.useState)(null);
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
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Plugin for %s installed and activated successfully."),
          connectorName
        ),
        {
          id: "connector-plugin-install-success",
          type: "snackbar"
        }
      );
    } catch {
      createErrorNotice(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Failed to install plugin for %s."),
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
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Plugin for %s activated successfully."),
          connectorName
        ),
        {
          id: "connector-plugin-activate-success",
          type: "snackbar"
        }
      );
    } catch {
      createErrorNotice(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Failed to activate plugin for %s."),
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
      return pluginStatus === "not-installed" ? (0, import_i18n.__)("Installing\u2026") : (0, import_i18n.__)("Activating\u2026");
    }
    if (isExpanded) {
      return (0, import_i18n.__)("Cancel");
    }
    if (isConnected) {
      return (0, import_i18n.__)("Edit");
    }
    switch (pluginStatus) {
      case "checking":
        return (0, import_i18n.__)("Checking\u2026");
      case "not-installed":
        return (0, import_i18n.__)("Install");
      case "inactive":
        return (0, import_i18n.__)("Activate");
      case "active":
        return (0, import_i18n.__)("Set up");
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
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("%s connected successfully."),
          connectorName
        ),
        {
          id: "connector-connect-success",
          type: "snackbar"
        }
      );
    } catch (error) {
      console.error("Failed to save API key:", error);
      throw error;
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
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("%s disconnected."),
          connectorName
        ),
        {
          id: "connector-disconnect-success",
          type: "snackbar"
        }
      );
    } catch (error) {
      console.error("Failed to remove API key:", error);
      createErrorNotice(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Failed to disconnect %s."),
          connectorName
        ),
        {
          id: "connector-disconnect-error",
          type: "snackbar"
        }
      );
      throw error;
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
var { store: connectorsStore } = unlock(connectorsPrivateApis);
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
  (0, import_i18n2.__)("Connected")
);
var PluginDirectoryLink = ({ slug }) => /* @__PURE__ */ React.createElement(
  import_components2.ExternalLink,
  {
    href: (0, import_i18n2.sprintf)(
      /* translators: %s: plugin slug. */
      (0, import_i18n2.__)("https://wordpress.org/plugins/%s/"),
      slug
    )
  },
  (0, import_i18n2.__)("Learn more")
);
var UnavailableActionBadge = () => /* @__PURE__ */ React.createElement(Badge, null, (0, import_i18n2.__)("Not available"));
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
  const actionButtonRef = (0, import_element9.useRef)(null);
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
    const existing = unlock((0, import_data2.select)(connectorsStore)).getConnector(
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
        href: "data:image/png;base64,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",
        width: "248",
        height: "248",
        style: { mixBlendMode: "multiply" }
      }
    )
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
  const [isBusy, setIsBusy] = (0, import_element10.useState)(false);
  const [justActivated, setJustActivated] = (0, import_element10.useState)(false);
  const actionButtonRef = (0, import_element10.useRef)(null);
  (0, import_element10.useEffect)(() => {
    if (justActivated) {
      actionButtonRef.current?.focus();
    }
  }, [justActivated]);
  const initialHasConnectedProvider = (0, import_element10.useRef)(
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
        (0, import_i18n3.__)("AI plugin installed and activated successfully."),
        {
          id: "ai-plugin-install-success",
          type: "snackbar"
        }
      );
    } catch {
      createErrorNotice((0, import_i18n3.__)("Failed to install the AI plugin."), {
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
      createSuccessNotice((0, import_i18n3.__)("AI plugin activated successfully."), {
        id: "ai-plugin-activate-success",
        type: "snackbar"
      });
    } catch {
      createErrorNotice((0, import_i18n3.__)("Failed to activate the AI plugin."), {
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
      return (0, import_i18n3.__)(
        "The <strong>AI plugin</strong> is ready to use. You can use it to generate featured images, alt text, titles, excerpts and more. <a>Learn more</a>"
      );
    }
    if (isActiveNoProvider) {
      return (0, import_i18n3.__)(
        "The <strong>AI plugin</strong> is installed. Connect an AI provider below to generate featured images, alt text, titles, excerpts, and more. <a>Learn more</a>"
      );
    }
    return (0, import_i18n3.__)(
      "The <strong>AI plugin</strong> can use your AI connectors to generate featured images, alt text, titles, excerpts and more. <a>Learn more</a>"
    );
  };
  const getPrimaryButtonProps = () => {
    if (pluginStatus === "not-installed") {
      return {
        label: isBusy ? (0, import_i18n3.__)("Installing\u2026") : (0, import_i18n3.__)("Install the AI plugin"),
        disabled: isBusy,
        onClick: isBusy ? void 0 : installPlugin
      };
    }
    return {
      label: isBusy ? (0, import_i18n3.__)("Activating\u2026") : (0, import_i18n3.__)("Activate the AI plugin"),
      disabled: isBusy,
      onClick: isBusy ? void 0 : activatePlugin
    };
  };
  return /* @__PURE__ */ React.createElement("div", { className: "ai-plugin-callout" }, /* @__PURE__ */ React.createElement("div", { className: "ai-plugin-callout__content" }, /* @__PURE__ */ React.createElement("p", null, (0, import_element10.createInterpolateElement)(getMessage(), {
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
    (0, import_i18n3.__)("Control features in the AI plugin")
  ))), /* @__PURE__ */ React.createElement(WpLogoDecoration, null));
}

// routes/connectors-home/stage.tsx
var { store } = unlock(connectorsPrivateApis2);
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
        connectors: unlock(select2(store)).getConnectors(),
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
  const showFileModsNotice = manualInstallPluginSlugs.length > 0 && (isFileModDisabled || !canInstallPlugins);
  const fileModsNoticeMessage = isFileModDisabled ? (0, import_i18n4.__)(
    "Plugins cannot be installed here due to your site configuration. Install them manually using your normal deployment workflow."
  ) : (0, import_i18n4.__)(
    "You do not have permission to install plugins. Please ask a site administrator to install them for you."
  );
  return /* @__PURE__ */ React.createElement(
    page_default,
    {
      title: (0, import_i18n4.__)("Connectors"),
      subTitle: (0, import_i18n4.__)(
        "All of your API keys and credentials are stored here and shared across plugins. Configure once and use everywhere."
      )
    },
    /* @__PURE__ */ React.createElement(
      "div",
      {
        className: `connectors-page${isEmpty ? " connectors-page--empty" : ""}`
      },
      showFileModsNotice && /* @__PURE__ */ React.createElement(
        notice_exports.Root,
        {
          intent: "info",
          className: "connectors-page__file-mods-notice"
        },
        /* @__PURE__ */ React.createElement(notice_exports.Description, null, fileModsNoticeMessage)
      ),
      isEmpty ? /* @__PURE__ */ React.createElement(
        import_components4.__experimentalVStack,
        {
          alignment: "center",
          spacing: 3,
          style: { maxWidth: 480 }
        },
        /* @__PURE__ */ React.createElement(import_components4.__experimentalVStack, { alignment: "center", spacing: 2 }, /* @__PURE__ */ React.createElement(import_components4.__experimentalHeading, { level: 2, size: 15, weight: 600 }, (0, import_i18n4.__)("No connectors yet")), /* @__PURE__ */ React.createElement(import_components4.__experimentalText, { size: 12 }, (0, import_i18n4.__)(
          "Connectors appear here when you install plugins that use external services. Each plugin registers the API keys it needs, and you manage them all in one place."
        ))),
        /* @__PURE__ */ React.createElement(import_components4.Button, { variant: "secondary", href: "plugin-install.php" }, (0, import_i18n4.__)("Learn more"))
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
      canInstallPlugins && !isFileModDisabled && /* @__PURE__ */ React.createElement("p", null, (0, import_element11.createInterpolateElement)(
        (0, import_i18n4.__)(
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
