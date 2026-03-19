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

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// package-external:@wordpress/block-editor
var require_block_editor = __commonJS({
  "package-external:@wordpress/block-editor"(exports, module) {
    module.exports = window.wp.blockEditor;
  }
});

// package-external:@wordpress/blocks
var require_blocks = __commonJS({
  "package-external:@wordpress/blocks"(exports, module) {
    module.exports = window.wp.blocks;
  }
});

// routes/navigation-edit/stage.tsx
import { useParams } from "@wordpress/route";

// packages/admin-ui/build-module/breadcrumbs/index.mjs
var import_i18n = __toESM(require_i18n(), 1);
var import_components = __toESM(require_components(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
import { Link } from "@wordpress/route";
var BreadcrumbItem = ({
  item: { label, to }
}) => {
  if (!to) {
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("li", { children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalHeading, { level: 1, truncate: true, children: label }) });
  }
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("li", { children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(Link, { to, children: label }) });
};
var Breadcrumbs = ({ items }) => {
  if (!items.length) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("nav", { "aria-label": (0, import_i18n.__)("Breadcrumbs"), children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
    import_components.__experimentalHStack,
    {
      as: "ul",
      className: "admin-ui-breadcrumbs__list",
      spacing: 0,
      justify: "flex-start",
      alignment: "center",
      children: items.map((item, index) => /* @__PURE__ */ (0, import_jsx_runtime.jsx)(BreadcrumbItem, { item }, index))
    }
  ) });
};
var breadcrumbs_default = Breadcrumbs;

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
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var NavigableRegion = (0, import_element.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
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

// packages/icons/build-module/library/chevron-down.mjs
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
var chevron_down_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives.Path, { d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z" }) });

// packages/icons/build-module/library/chevron-up.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var chevron_up_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives2.Path, { d: "M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z" }) });

// packages/icons/build-module/library/more-vertical.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
var more_vertical_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives3.Path, { d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z" }) });

// packages/ui/build-module/stack/stack.mjs
var import_element2 = __toESM(require_element(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='71d20935c2']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "71d20935c2");
  style.appendChild(document.createTextNode("@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._19ce0419607e1896__stack{display:flex}}"));
  document.head.appendChild(style);
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
var Stack = (0, import_element2.forwardRef)(function Stack2({ direction, gap, align, justify, wrap, render, ...props }, ref) {
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

// packages/admin-ui/build-module/page/sidebar-toggle-slot.mjs
var import_components2 = __toESM(require_components(), 1);
var { Fill: SidebarToggleFill, Slot: SidebarToggleSlot } = (0, import_components2.createSlotFill)("SidebarToggle");

// packages/admin-ui/build-module/page/header.mjs
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
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
  return /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(
    Stack,
    {
      direction: "column",
      className: "admin-ui-page__header",
      render: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)("header", {}),
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(Stack, { direction: "row", justify: "space-between", gap: "sm", children: [
          /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(Stack, { direction: "row", gap: "sm", align: "center", justify: "start", children: [
            showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
              SidebarToggleSlot,
              {
                bubblesVirtually: true,
                className: "admin-ui-page__sidebar-toggle-slot"
              }
            ),
            title && /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(HeadingTag, { className: "admin-ui-page__header-title", children: title }),
            breadcrumbs,
            badges
          ] }),
          /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
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
        subTitle && /* @__PURE__ */ (0, import_jsx_runtime6.jsx)("p", { className: "admin-ui-page__header-subtitle", children: subTitle })
      ]
    }
  );
}

// packages/admin-ui/build-module/page/index.mjs
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
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
  return /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(navigable_region_default, { className: classes, ariaLabel: title, children: [
    (title || breadcrumbs || badges) && /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
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
    hasPadding ? /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("div", { className: "admin-ui-page__content has-padding", children }) : children
  ] });
}
Page.SidebarToggleFill = SidebarToggleFill;
var page_default = Page;

// routes/navigation-edit/stage.tsx
var import_data3 = __toESM(require_data());
var import_core_data2 = __toESM(require_core_data());
var import_i18n3 = __toESM(require_i18n());
var import_html_entities = __toESM(require_html_entities());

// routes/navigation-edit/editor/index.tsx
var import_element4 = __toESM(require_element());
var import_block_editor3 = __toESM(require_block_editor());
var import_blocks2 = __toESM(require_blocks());
var import_components4 = __toESM(require_components());
import { useEditorAssets } from "@wordpress/lazy-editor";

// routes/navigation-edit/editor/style.scss
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='023c02af3d']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "023c02af3d");
  style.appendChild(document.createTextNode(".navigation-edit-editor__hidden-blocks{display:none}"));
  document.head.appendChild(style);
}

// routes/navigation-edit/editor/content.tsx
var import_block_editor2 = __toESM(require_block_editor());
var import_data2 = __toESM(require_data());
var import_blocks = __toESM(require_blocks());
var import_element3 = __toESM(require_element());
var import_core_data = __toESM(require_core_data());

// routes/lock-unlock.ts
var import_private_apis = __toESM(require_private_apis());
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/routes"
);

// routes/navigation-edit/editor/leaf-more-menu.tsx
var import_components3 = __toESM(require_components());
var import_data = __toESM(require_data());
var import_i18n2 = __toESM(require_i18n());
var import_block_editor = __toESM(require_block_editor());
var POPOVER_PROPS = {
  className: "block-editor-block-settings-menu__popover",
  placement: "bottom-start"
};
function LeafMoreMenu({
  block,
  ...props
}) {
  const { clientId } = block;
  const { moveBlocksDown, moveBlocksUp, removeBlocks } = (0, import_data.useDispatch)(import_block_editor.store);
  const removeLabel = (0, import_i18n2.sprintf)(
    /* translators: %s: block name */
    (0, import_i18n2.__)("Remove %s"),
    (0, import_block_editor.BlockTitle)({ clientId, maximumLength: 25 })
  );
  const rootClientId = (0, import_data.useSelect)(
    (select) => {
      const { getBlockRootClientId } = select(import_block_editor.store);
      return getBlockRootClientId(clientId);
    },
    [clientId]
  );
  return /* @__PURE__ */ React.createElement(
    import_components3.DropdownMenu,
    {
      icon: more_vertical_default,
      label: (0, import_i18n2.__)("Options"),
      className: "block-editor-block-settings-menu",
      popoverProps: POPOVER_PROPS,
      noIcons: true,
      ...props
    },
    ({ onClose }) => /* @__PURE__ */ React.createElement(React.Fragment, null, /* @__PURE__ */ React.createElement(import_components3.MenuGroup, null, /* @__PURE__ */ React.createElement(
      import_components3.MenuItem,
      {
        icon: chevron_up_default,
        onClick: () => {
          moveBlocksUp([clientId], rootClientId);
          onClose();
        }
      },
      (0, import_i18n2.__)("Move up")
    ), /* @__PURE__ */ React.createElement(
      import_components3.MenuItem,
      {
        icon: chevron_down_default,
        onClick: () => {
          moveBlocksDown([clientId], rootClientId);
          onClose();
        }
      },
      (0, import_i18n2.__)("Move down")
    )), /* @__PURE__ */ React.createElement(import_components3.MenuGroup, null, /* @__PURE__ */ React.createElement(
      import_components3.MenuItem,
      {
        onClick: () => {
          removeBlocks([clientId], false);
          onClose();
        }
      },
      removeLabel
    )))
  );
}

// routes/navigation-edit/editor/content.tsx
var { PrivateListView } = unlock(import_block_editor2.privateApis);
var MAX_PAGE_COUNT = 100;
var PAGES_QUERY = [
  "postType",
  "page",
  {
    per_page: MAX_PAGE_COUNT,
    _fields: ["id", "link", "menu_order", "parent", "title", "type"],
    // TODO: When https://core.trac.wordpress.org/ticket/39037 REST API support for multiple orderby
    // values is resolved, update 'orderby' to [ 'menu_order', 'post_title' ] to provide a consistent
    // sort.
    orderby: "menu_order",
    order: "asc"
  }
];
function NavigationMenuContent({
  rootClientId
}) {
  const { listViewRootClientId, isLoading } = (0, import_data2.useSelect)(
    (select) => {
      const {
        areInnerBlocksControlled,
        getBlockName,
        getBlockCount,
        getBlockOrder
      } = select(import_block_editor2.store);
      const { isResolving } = select(import_core_data.store);
      const blockClientIds = getBlockOrder(rootClientId);
      const hasOnlyPageListBlock = blockClientIds.length === 1 && getBlockName(blockClientIds[0]) === "core/page-list";
      const pageListHasBlocks = hasOnlyPageListBlock && getBlockCount(blockClientIds[0]) > 0;
      const isLoadingPages = isResolving(
        "getEntityRecords",
        PAGES_QUERY
      );
      return {
        listViewRootClientId: pageListHasBlocks ? blockClientIds[0] : rootClientId,
        // This is a small hack to wait for the navigation block
        // to actually load its inner blocks.
        isLoading: !areInnerBlocksControlled(rootClientId) || isLoadingPages
      };
    },
    [rootClientId]
  );
  const { replaceBlock, __unstableMarkNextChangeAsNotPersistent } = (0, import_data2.useDispatch)(import_block_editor2.store);
  const offCanvasOnselect = (0, import_element3.useCallback)(
    (block) => {
      if (block.name === "core/navigation-link" && !block.attributes.url) {
        __unstableMarkNextChangeAsNotPersistent();
        replaceBlock(
          block.clientId,
          (0, import_blocks.createBlock)("core/navigation-link", block.attributes)
        );
      }
    },
    [__unstableMarkNextChangeAsNotPersistent, replaceBlock]
  );
  return /* @__PURE__ */ React.createElement(React.Fragment, null, !isLoading && /* @__PURE__ */ React.createElement(
    PrivateListView,
    {
      rootClientId: listViewRootClientId,
      onSelect: offCanvasOnselect,
      blockSettingsMenu: LeafMoreMenu,
      showAppender: false,
      isExpanded: true
    }
  ), /* @__PURE__ */ React.createElement("div", { className: "navigation-edit-editor__hidden-blocks" }, /* @__PURE__ */ React.createElement(import_block_editor2.BlockList, null)));
}

// routes/navigation-edit/editor/index.tsx
var noop = () => {
};
function NavigationMenuEditor({ id }) {
  const { isReady: assetsReady } = useEditorAssets();
  const blocks = (0, import_element4.useMemo)(() => {
    if (!assetsReady || !id) {
      return [];
    }
    return [(0, import_blocks2.createBlock)("core/navigation", { ref: id })];
  }, [assetsReady, id]);
  if (!assetsReady || !blocks.length) {
    return /* @__PURE__ */ React.createElement(
      "div",
      {
        style: {
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          height: "100vh"
        }
      },
      /* @__PURE__ */ React.createElement(import_components4.Spinner, null)
    );
  }
  return /* @__PURE__ */ React.createElement(
    import_block_editor3.BlockEditorProvider,
    {
      settings: {},
      value: blocks,
      onChange: noop,
      onInput: noop
    },
    /* @__PURE__ */ React.createElement(NavigationMenuContent, { rootClientId: blocks[0].clientId })
  );
}

// routes/navigation-edit/stage.tsx
var NAVIGATION_POST_TYPE = "wp_navigation";
function NavigationEditStage() {
  const { id } = useParams({ from: "/navigation/edit/$id" });
  const navigationId = parseInt(id);
  const { navigationMenu } = (0, import_data3.useSelect)(
    (select) => {
      const { getEntityRecord } = select(import_core_data2.store);
      return {
        navigationMenu: getEntityRecord(
          "postType",
          NAVIGATION_POST_TYPE,
          navigationId
        )
      };
    },
    [navigationId]
  );
  if (!navigationMenu) {
    return;
  }
  const menuTitle = navigationMenu.title?.rendered || navigationMenu.title?.raw || "";
  return /* @__PURE__ */ React.createElement(
    page_default,
    {
      breadcrumbs: /* @__PURE__ */ React.createElement(
        breadcrumbs_default,
        {
          items: [
            {
              label: (0, import_i18n3.__)("Navigation"),
              to: "/navigation/list"
            },
            {
              label: (0, import_html_entities.decodeEntities)(menuTitle)
            }
          ]
        }
      ),
      hasPadding: true
    },
    /* @__PURE__ */ React.createElement(NavigationMenuEditor, { id: navigationId })
  );
}
var stage = NavigationEditStage;
export {
  stage
};
