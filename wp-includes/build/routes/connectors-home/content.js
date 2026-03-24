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

// packages/ui/build-module/stack/stack.mjs
var import_element3 = __toESM(require_element(), 1);
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
    props: mergeProps(props, { style, className: style_default2.stack })
  });
  return element;
});

// packages/admin-ui/build-module/page/sidebar-toggle-slot.mjs
var import_components = __toESM(require_components(), 1);
var { Fill: SidebarToggleFill, Slot: SidebarToggleSlot } = (0, import_components.createSlotFill)("SidebarToggle");

// packages/admin-ui/build-module/page/header.mjs
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
function Header({
  headingLevel = 2,
  breadcrumbs,
  badges,
  title,
  subTitle,
  actions,
  showSidebarToggle = true
}) {
  const HeadingTag = `h${headingLevel}`;
  return /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(
    Stack,
    {
      direction: "column",
      className: "admin-ui-page__header",
      render: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("header", {}),
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(Stack, { direction: "row", justify: "space-between", gap: "sm", children: [
          /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(Stack, { direction: "row", gap: "sm", align: "center", justify: "start", children: [
            showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
              SidebarToggleSlot,
              {
                bubblesVirtually: true,
                className: "admin-ui-page__sidebar-toggle-slot"
              }
            ),
            title && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(HeadingTag, { className: "admin-ui-page__header-title", children: title }),
            breadcrumbs,
            badges
          ] }),
          /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
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
        subTitle && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("p", { className: "admin-ui-page__header-subtitle", children: subTitle })
      ]
    }
  );
}

// packages/admin-ui/build-module/page/index.mjs
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
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
  return /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(navigable_region_default, { className: classes, ariaLabel: title, children: [
    (title || breadcrumbs || badges) && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
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
    hasPadding ? /* @__PURE__ */ (0, import_jsx_runtime3.jsx)("div", { className: "admin-ui-page__content has-padding", children }) : children
  ] });
}
Page.SidebarToggleFill = SidebarToggleFill;
var page_default = Page;

// routes/connectors-home/stage.tsx
var import_components4 = __toESM(require_components());
var import_data3 = __toESM(require_data());
var import_element7 = __toESM(require_element());
var import_i18n4 = __toESM(require_i18n());
var import_core_data3 = __toESM(require_core_data());
import {
  privateApis as connectorsPrivateApis
} from "@wordpress/connectors";

// routes/connectors-home/style.scss
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='1b00f16b8d']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "1b00f16b8d");
  style.appendChild(document.createTextNode(".connectors-page{box-sizing:border-box;margin:0 auto;max-width:680px;padding:24px;width:100%}.connectors-page .components-item{background:#fff;border:1px solid #ddd;border-radius:8px;overflow:hidden;padding:20px;scroll-margin-top:120px}.connectors-page .connector-settings__error{color:#cc1818}.connectors-page .connector-settings .components-text-control__input{font-family:monospace;scroll-margin-top:120px}.connectors-page--empty{align-items:center;display:flex;flex-direction:column;flex-grow:1;gap:32px;justify-content:center;text-align:center}.connectors-page .ai-plugin-callout{background:linear-gradient(90deg,#fff9,#fff9),linear-gradient(90deg,#89dcdc,#c7eb5c 46.15%,#a920c1);border-radius:8px;overflow:hidden;padding:24px;padding-inline-end:220px;position:relative}[dir=rtl] .connectors-page .ai-plugin-callout{background:linear-gradient(270deg,#fff9,#fff9),linear-gradient(270deg,#89dcdc,#c7eb5c 46.15%,#a920c1)}.connectors-page .ai-plugin-callout__content{align-items:flex-start;display:flex;flex-direction:column;gap:12px;padding-top:2px}.connectors-page .ai-plugin-callout__content p{font-size:13px;line-height:20px;margin:0}.connectors-page .ai-plugin-callout__decoration{height:248px;inset-inline-end:8px;position:absolute;top:-15px;width:248px}.connectors-page>p{color:#949494;text-align:center}@media (max-width:680px){.connectors-page .ai-plugin-callout{padding:12px;padding-inline-end:84px}.connectors-page .ai-plugin-callout__decoration{height:134px;inset-inline-end:4px;top:-8px;width:134px}}@media (max-width:480px){.connectors-page{padding:8px}.connectors-page .components-item{padding:12px}.connectors-page .components-item>.components-v-stack>.components-h-stack:first-child svg{height:32px;width:32px}.connectors-page .components-item>.components-v-stack>.components-h-stack:first-child>.components-h-stack:last-child{align-items:flex-end;flex-direction:column}}"));
  document.head.appendChild(style);
}

// routes/connectors-home/ai-plugin-callout.tsx
var import_components3 = __toESM(require_components());
var import_core_data2 = __toESM(require_core_data());
var import_data2 = __toESM(require_data());
var import_element6 = __toESM(require_element());
var import_i18n3 = __toESM(require_i18n());
var import_url = __toESM(require_url());
import { speak as speak2 } from "@wordpress/a11y";

// routes/connectors-home/default-connectors.tsx
var import_components2 = __toESM(require_components());
var import_element5 = __toESM(require_element());
var import_i18n2 = __toESM(require_i18n());
import {
  __experimentalRegisterConnector as registerConnector,
  __experimentalConnectorItem as ConnectorItem,
  __experimentalDefaultConnectorSettings as DefaultConnectorSettings
} from "@wordpress/connectors";

// routes/connectors-home/use-connector-plugin.ts
var import_core_data = __toESM(require_core_data());
var import_data = __toESM(require_data());
var import_element4 = __toESM(require_element());
var import_i18n = __toESM(require_i18n());
import { speak } from "@wordpress/a11y";
function useConnectorPlugin({
  pluginSlug,
  settingName,
  connectorName,
  isInstalled,
  isActivated,
  keySource = "none",
  initialIsConnected = false
}) {
  const [isExpanded, setIsExpanded] = (0, import_element4.useState)(false);
  const [isBusy, setIsBusy] = (0, import_element4.useState)(false);
  const [connectedState, setConnectedState] = (0, import_element4.useState)(initialIsConnected);
  const [pluginStatusOverride, setPluginStatusOverride] = (0, import_element4.useState)(null);
  const {
    derivedPluginStatus,
    canManagePlugins,
    currentApiKey,
    canInstallPlugins
  } = (0, import_data.useSelect)(
    (select) => {
      const store2 = select(import_core_data.store);
      const siteSettings = store2.getEntityRecord("root", "site");
      const apiKey = siteSettings?.[settingName] ?? "";
      const canCreate = !!store2.canUser("create", {
        kind: "root",
        name: "plugin"
      });
      if (!pluginSlug) {
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
      const pluginId = `${pluginSlug}/plugin`;
      const plugin = store2.getEntityRecord(
        "root",
        "plugin",
        pluginId
      );
      const hasFinished = store2.hasFinishedResolution(
        "getEntityRecord",
        ["root", "plugin", pluginId]
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
        return {
          derivedPluginStatus: plugin.status === "active" ? "active" : "inactive",
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
    [pluginSlug, settingName, isInstalled, isActivated]
  );
  const pluginStatus = pluginStatusOverride ?? derivedPluginStatus;
  const canActivatePlugins = canManagePlugins;
  const isConnected = pluginStatus === "active" && connectedState || // After install/activate, if settings re-fetch reveals an existing key,
  // update connected state (mirrors what the server would report on page load).
  pluginStatusOverride === "active" && !!currentApiKey;
  const { saveEntityRecord, invalidateResolution } = (0, import_data.useDispatch)(import_core_data.store);
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
      speak(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Plugin for %s installed and activated successfully."),
          connectorName
        )
      );
    } catch {
      speak(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Failed to install plugin for %s."),
          connectorName
        ),
        "assertive"
      );
    } finally {
      setIsBusy(false);
    }
  };
  const activatePlugin = async () => {
    if (!pluginSlug) {
      return;
    }
    setIsBusy(true);
    try {
      await saveEntityRecord(
        "root",
        "plugin",
        { plugin: `${pluginSlug}/plugin`, status: "active" },
        { throwOnError: true }
      );
      setPluginStatusOverride("active");
      invalidateResolution("getEntityRecord", ["root", "site"]);
      setIsExpanded(true);
      speak(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Plugin for %s activated successfully."),
          connectorName
        )
      );
    } catch {
      speak(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Failed to activate plugin for %s."),
          connectorName
        ),
        "assertive"
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
      speak(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("%s connected successfully."),
          connectorName
        )
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
      speak(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("%s disconnected."),
          connectorName
        )
      );
    } catch (error) {
      console.error("Failed to remove API key:", error);
      speak(
        (0, import_i18n.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n.__)("Failed to disconnect %s."),
          connectorName
        ),
        "assertive"
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
function getConnectorData() {
  try {
    const parsed = JSON.parse(
      document.getElementById(
        "wp-script-module-data-options-connectors-wp-admin"
      )?.textContent ?? ""
    );
    return parsed?.connectors ?? {};
  } catch {
    return {};
  }
}
var CONNECTOR_LOGOS = {
  google: GeminiLogo,
  openai: OpenAILogo,
  anthropic: ClaudeLogo
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
  const pluginSlug = plugin?.slug;
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
    pluginSlug,
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
  const actionButtonRef = (0, import_element5.useRef)(null);
  const pendingFocusRef = (0, import_element5.useRef)(false);
  (0, import_element5.useEffect)(() => {
    if (pendingFocusRef.current && !isBusy) {
      pendingFocusRef.current = false;
      actionButtonRef.current?.focus();
    }
  }, [isBusy, isExpanded, isConnected]);
  const handleActionClick = () => {
    if (pluginStatus === "not-installed" || pluginStatus === "inactive") {
      pendingFocusRef.current = true;
    }
    handleButtonClick();
  };
  return /* @__PURE__ */ React.createElement(
    ConnectorItem,
    {
      className: pluginSlug ? `connector-item--${pluginSlug}` : void 0,
      logo,
      name,
      description,
      actionArea: /* @__PURE__ */ React.createElement(import_components2.__experimentalHStack, { spacing: 3, expanded: false }, isConnected && /* @__PURE__ */ React.createElement(ConnectedBadge, null), showUnavailableBadge && /* @__PURE__ */ React.createElement(UnavailableActionBadge, null), showActionButton && /* @__PURE__ */ React.createElement(
        import_components2.Button,
        {
          ref: actionButtonRef,
          variant: isExpanded || isConnected ? "tertiary" : "secondary",
          size: "compact",
          onClick: handleActionClick,
          disabled: pluginStatus === "checking" || isBusy,
          isBusy
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
          pendingFocusRef.current = true;
          try {
            await removeApiKey();
          } catch {
            pendingFocusRef.current = false;
          }
        },
        onSave: async (apiKey) => {
          await saveApiKey(apiKey);
          pendingFocusRef.current = true;
          setIsExpanded(false);
        }
      }
    )
  );
}
function registerDefaultConnectors() {
  const connectors = getConnectorData();
  const sanitize = (s) => s.replace(/[^a-z0-9-_]/gi, "-");
  for (const [connectorId, data] of Object.entries(connectors)) {
    const { authentication } = data;
    const connectorName = sanitize(connectorId);
    const args = {
      name: data.name,
      description: data.description,
      logo: getConnectorLogo(connectorId, data.logoUrl),
      authentication,
      plugin: data.plugin
    };
    if (data.type === "ai_provider" && authentication.method === "api_key") {
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
  const [isBusy, setIsBusy] = (0, import_element6.useState)(false);
  const [justActivated, setJustActivated] = (0, import_element6.useState)(false);
  const actionButtonRef = (0, import_element6.useRef)(null);
  (0, import_element6.useEffect)(() => {
    if (justActivated) {
      actionButtonRef.current?.focus();
    }
  }, [justActivated]);
  const initialHasConnectedProvider = (0, import_element6.useRef)(
    connectorDataValues.some(
      (c) => c.type === "ai_provider" && c.authentication.method === "api_key" && c.authentication.isConnected
    )
  ).current;
  const {
    pluginStatus,
    canInstallPlugins,
    canManagePlugins,
    hasConnectedProvider
  } = (0, import_data2.useSelect)((select) => {
    const store2 = select(import_core_data2.store);
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
  const { saveEntityRecord } = (0, import_data2.useDispatch)(import_core_data2.store);
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
      speak2((0, import_i18n3.__)("AI plugin installed and activated successfully."));
    } catch {
      speak2((0, import_i18n3.__)("Failed to install the AI plugin."), "assertive");
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
      speak2((0, import_i18n3.__)("AI plugin activated successfully."));
    } catch {
      speak2((0, import_i18n3.__)("Failed to activate the AI plugin."), "assertive");
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
  if (pluginStatus === "not-installed" && canInstallPlugins === false) {
    return null;
  }
  if (pluginStatus === "inactive" && canManagePlugins === false) {
    return null;
  }
  const isActiveNoProvider = pluginStatus === "active" && !hasConnectedProvider;
  const isJustConnected = pluginStatus === "active" && hasConnectedProvider && (!initialHasConnectedProvider || justActivated);
  const showInstallActivate = pluginStatus === "not-installed" || pluginStatus === "inactive";
  const getMessage = () => {
    if (isJustConnected) {
      return (0, import_i18n3.__)(
        "The <strong>AI plugin</strong> is ready to use. You can use it to generate featured images, alt text, titles, excerpts and more. <a>Learn more</a>"
      );
    }
    if (isActiveNoProvider) {
      return (0, import_i18n3.__)(
        "The <strong>AI plugin</strong> is installed. Connect a provider below to generate featured images, alt text, titles, excerpts, and more. <a>Learn more</a>"
      );
    }
    return (0, import_i18n3.__)(
      "The <strong>AI plugin</strong> can use your connectors to generate featured images, alt text, titles, excerpts and more. <a>Learn more</a>"
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
  return /* @__PURE__ */ React.createElement("div", { className: "ai-plugin-callout" }, /* @__PURE__ */ React.createElement("div", { className: "ai-plugin-callout__content" }, /* @__PURE__ */ React.createElement("p", null, (0, import_element6.createInterpolateElement)(getMessage(), {
    strong: /* @__PURE__ */ React.createElement("strong", null),
    // @ts-ignore children are injected by createInterpolateElement at runtime.
    a: /* @__PURE__ */ React.createElement(import_components3.ExternalLink, { href: AI_PLUGIN_URL })
  })), showInstallActivate ? /* @__PURE__ */ React.createElement(
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
        page: AI_PLUGIN_SLUG
      })
    },
    (0, import_i18n3.__)("Control features in the AI plugin")
  )), /* @__PURE__ */ React.createElement(WpLogoDecoration, null));
}

// routes/lock-unlock.ts
var import_private_apis = __toESM(require_private_apis());
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/routes"
);

// routes/connectors-home/stage.tsx
var { store } = unlock(connectorsPrivateApis);
registerDefaultConnectors();
function ConnectorsPage() {
  const { connectors, canInstallPlugins } = (0, import_data3.useSelect)(
    (select) => ({
      connectors: unlock(select(store)).getConnectors(),
      canInstallPlugins: select(import_core_data3.store).canUser("create", {
        kind: "root",
        name: "plugin"
      })
    }),
    []
  );
  const renderableConnectors = connectors.filter(
    (connector) => connector.render
  );
  const isEmpty = renderableConnectors.length === 0;
  return /* @__PURE__ */ React.createElement(
    page_default,
    {
      title: (0, import_i18n4.__)("Connectors"),
      headingLevel: 1,
      subTitle: (0, import_i18n4.__)(
        "All of your API keys and credentials are stored here and shared across plugins. Configure once and use everywhere."
      )
    },
    /* @__PURE__ */ React.createElement(
      "div",
      {
        className: `connectors-page${isEmpty ? " connectors-page--empty" : ""}`
      },
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
      ) : /* @__PURE__ */ React.createElement(import_components4.__experimentalVStack, { spacing: 3 }, /* @__PURE__ */ React.createElement(AiPluginCallout, null), connectors.map((connector) => {
        if (connector.render) {
          return /* @__PURE__ */ React.createElement(
            connector.render,
            {
              key: connector.slug,
              slug: connector.slug,
              name: connector.name,
              description: connector.description,
              logo: connector.logo,
              authentication: connector.authentication,
              plugin: connector.plugin
            }
          );
        }
        return null;
      })),
      canInstallPlugins && /* @__PURE__ */ React.createElement("p", null, (0, import_element7.createInterpolateElement)(
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
