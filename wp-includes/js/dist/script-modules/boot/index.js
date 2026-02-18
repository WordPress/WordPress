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

// package-external:@wordpress/compose
var require_compose = __commonJS({
  "package-external:@wordpress/compose"(exports, module) {
    module.exports = window.wp.compose;
  }
});

// package-external:@wordpress/primitives
var require_primitives = __commonJS({
  "package-external:@wordpress/primitives"(exports, module) {
    module.exports = window.wp.primitives;
  }
});

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// package-external:@wordpress/keycodes
var require_keycodes = __commonJS({
  "package-external:@wordpress/keycodes"(exports, module) {
    module.exports = window.wp.keycodes;
  }
});

// package-external:@wordpress/commands
var require_commands = __commonJS({
  "package-external:@wordpress/commands"(exports, module) {
    module.exports = window.wp.commands;
  }
});

// package-external:@wordpress/url
var require_url = __commonJS({
  "package-external:@wordpress/url"(exports, module) {
    module.exports = window.wp.url;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
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

// package-external:@wordpress/theme
var require_theme = __commonJS({
  "package-external:@wordpress/theme"(exports, module) {
    module.exports = window.wp.theme;
  }
});

// packages/boot/build-module/components/app/index.mjs
var import_element15 = __toESM(require_element(), 1);
var import_data11 = __toESM(require_data(), 1);

// packages/boot/build-module/components/app/router.mjs
var import_i18n11 = __toESM(require_i18n(), 1);
var import_element14 = __toESM(require_element(), 1);

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

// packages/admin-ui/build-module/page/header.mjs
var import_components2 = __toESM(require_components(), 1);

// packages/admin-ui/build-module/page/sidebar-toggle-slot.mjs
var import_components = __toESM(require_components(), 1);
var { Fill: SidebarToggleFill, Slot: SidebarToggleSlot } = (0, import_components.createSlotFill)("SidebarToggle");

// packages/admin-ui/build-module/page/header.mjs
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
function Header({
  breadcrumbs,
  badges,
  title,
  subTitle,
  actions,
  showSidebarToggle = true
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components2.__experimentalVStack, { className: "admin-ui-page__header", as: "header", children: [
    /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components2.__experimentalHStack, { justify: "space-between", spacing: 2, children: [
      /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components2.__experimentalHStack, { spacing: 2, justify: "left", children: [
        showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
          SidebarToggleSlot,
          {
            bubblesVirtually: true,
            className: "admin-ui-page__sidebar-toggle-slot"
          }
        ),
        title && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_components2.__experimentalHeading, { as: "h2", level: 3, weight: 500, truncate: true, children: title }),
        breadcrumbs,
        badges
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
        import_components2.__experimentalHStack,
        {
          style: { width: "auto", flexShrink: 0 },
          spacing: 2,
          className: "admin-ui-page__header-actions",
          children: actions
        }
      )
    ] }),
    subTitle && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("p", { className: "admin-ui-page__header-subtitle", children: subTitle })
  ] });
}

// packages/admin-ui/build-module/page/index.mjs
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
function Page({
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

// packages/boot/build-module/components/app/router.mjs
var import_data10 = __toESM(require_data(), 1);
var import_core_data6 = __toESM(require_core_data(), 1);
import {
  privateApis as routePrivateApis6
} from "@wordpress/route";

// packages/boot/build-module/components/root/index.mjs
var import_notices = __toESM(require_notices(), 1);
var import_compose4 = __toESM(require_compose(), 1);
var import_components15 = __toESM(require_components(), 1);
import { privateApis as routePrivateApis5 } from "@wordpress/route";

// packages/icons/build-module/icon/index.mjs
var import_element2 = __toESM(require_element(), 1);
var icon_default = (0, import_element2.forwardRef)(
  ({ icon, size = 24, ...props }, ref) => {
    return (0, import_element2.cloneElement)(icon, {
      width: size,
      height: size,
      ...props,
      ref
    });
  }
);

// packages/icons/build-module/library/arrow-up-left.mjs
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var arrow_up_left_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives.Path, { d: "M14 6H6v8h1.5V8.5L17 18l1-1-9.5-9.5H14V6Z" }) });

// packages/icons/build-module/library/check.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
var check_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives2.Path, { d: "M16.5 7.5 10 13.9l-2.5-2.4-1 1 3.5 3.6 7.5-7.6z" }) });

// packages/icons/build-module/library/chevron-down-small.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
var chevron_down_small_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives3.Path, { d: "m15.99 10.889-3.988 3.418-3.988-3.418.976-1.14 3.012 2.582 3.012-2.581.976 1.139Z" }) });

// packages/icons/build-module/library/chevron-left-small.mjs
var import_primitives4 = __toESM(require_primitives(), 1);
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
var chevron_left_small_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives4.Path, { d: "m13.1 16-3.4-4 3.4-4 1.1 1-2.6 3 2.6 3-1.1 1z" }) });

// packages/icons/build-module/library/chevron-left.mjs
var import_primitives5 = __toESM(require_primitives(), 1);
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
var chevron_left_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives5.Path, { d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z" }) });

// packages/icons/build-module/library/chevron-right-small.mjs
var import_primitives6 = __toESM(require_primitives(), 1);
var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
var chevron_right_small_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives6.Path, { d: "M10.8622 8.04053L14.2805 12.0286L10.8622 16.0167L9.72327 15.0405L12.3049 12.0286L9.72327 9.01672L10.8622 8.04053Z" }) });

// packages/icons/build-module/library/chevron-right.mjs
var import_primitives7 = __toESM(require_primitives(), 1);
var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
var chevron_right_default = /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives7.Path, { d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z" }) });

// packages/icons/build-module/library/menu.mjs
var import_primitives8 = __toESM(require_primitives(), 1);
var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
var menu_default = /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives8.Path, { d: "M5 5v1.5h14V5H5zm0 7.8h14v-1.5H5v1.5zM5 19h14v-1.5H5V19z" }) });

// packages/icons/build-module/library/search.mjs
var import_primitives9 = __toESM(require_primitives(), 1);
var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
var search_default = /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives9.Path, { d: "M13 5c-3.3 0-6 2.7-6 6 0 1.4.5 2.7 1.3 3.7l-3.8 3.8 1.1 1.1 3.8-3.8c1 .8 2.3 1.3 3.7 1.3 3.3 0 6-2.7 6-6S16.3 5 13 5zm0 10.5c-2.5 0-4.5-2-4.5-4.5s2-4.5 4.5-4.5 4.5 2 4.5 4.5-2 4.5-4.5 4.5z" }) });

// packages/icons/build-module/library/wordpress.mjs
var import_primitives10 = __toESM(require_primitives(), 1);
var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
var wordpress_default = /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives10.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "-2 -2 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives10.Path, { d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z" }) });

// packages/boot/build-module/components/root/index.mjs
var import_element13 = __toESM(require_element(), 1);
var import_i18n10 = __toESM(require_i18n(), 1);

// packages/boot/build-module/components/site-hub/index.mjs
var import_data3 = __toESM(require_data(), 1);
var import_components4 = __toESM(require_components(), 1);
var import_i18n2 = __toESM(require_i18n(), 1);
var import_core_data2 = __toESM(require_core_data(), 1);
var import_html_entities = __toESM(require_html_entities(), 1);
var import_keycodes = __toESM(require_keycodes(), 1);
var import_commands = __toESM(require_commands(), 1);
var import_url = __toESM(require_url(), 1);

// packages/boot/build-module/components/site-icon-link/index.mjs
var import_components3 = __toESM(require_components(), 1);
import { Link, privateApis as routePrivateApis } from "@wordpress/route";

// packages/boot/build-module/lock-unlock.mjs
var import_private_apis = __toESM(require_private_apis(), 1);
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/boot"
);

// packages/boot/build-module/components/site-icon/index.mjs
var import_data = __toESM(require_data(), 1);
var import_i18n = __toESM(require_i18n(), 1);
var import_core_data = __toESM(require_core_data(), 1);
var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='01cf8dea05']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "01cf8dea05");
  style.appendChild(document.createTextNode(".boot-site-icon{display:flex}.boot-site-icon__icon{fill:var(--wpds-color-fg-content-neutral,#1e1e1e);height:32px;width:32px}.boot-site-icon__image{aspect-ratio:1/1;border-radius:var(--wpds-border-radius-md,4px);height:32px;object-fit:cover;width:32px}"));
  document.head.appendChild(style);
}
function SiteIcon({ className }) {
  const { isRequestingSite, siteIconUrl } = (0, import_data.useSelect)((select) => {
    const { getEntityRecord } = select(import_core_data.store);
    const siteData = getEntityRecord(
      "root",
      "__unstableBase",
      void 0
    );
    return {
      isRequestingSite: !siteData,
      siteIconUrl: siteData?.site_icon_url
    };
  }, []);
  let icon = null;
  if (isRequestingSite && !siteIconUrl) {
    icon = /* @__PURE__ */ (0, import_jsx_runtime14.jsx)("div", { className: "boot-site-icon__image" });
  } else {
    icon = siteIconUrl ? /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
      "img",
      {
        className: "boot-site-icon__image",
        alt: (0, import_i18n.__)("Site Icon"),
        src: siteIconUrl
      }
    ) : /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
      icon_default,
      {
        className: "boot-site-icon__icon",
        icon: wordpress_default,
        size: 48
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime14.jsx)("div", { className: clsx_default(className, "boot-site-icon"), children: icon });
}
var site_icon_default = SiteIcon;

// packages/boot/build-module/components/site-icon-link/index.mjs
var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='82d29bdbd2']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "82d29bdbd2");
  style.appendChild(document.createTextNode(".boot-site-icon-link{align-items:center;background:var(--wpds-color-bg-surface-neutral-weak,#f0f0f0);display:inline-flex;height:64px;justify-content:center;text-decoration:none;width:64px}@media not (prefers-reduced-motion){.boot-site-icon-link{transition:outline .1s ease-out}}.boot-site-icon-link:focus:not(:active){outline:var(--wpds-border-width-focus,2px) solid var(--wpds-color-stroke-focus-brand,#0073aa);outline-offset:calc(var(--wpds-border-width-focus, 2px)*-1)}"));
  document.head.appendChild(style);
}
var { useCanGoBack, useRouter } = unlock(routePrivateApis);
function SiteIconLink({
  to,
  isBackButton,
  ...props
}) {
  const router = useRouter();
  const canGoBack = useCanGoBack();
  return /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_components3.Tooltip, { text: props["aria-label"], placement: "right", children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
    Link,
    {
      to,
      "aria-label": props["aria-label"],
      className: "boot-site-icon-link",
      onClick: (event) => {
        if (canGoBack && isBackButton) {
          event.preventDefault();
          router.history.back();
        }
      },
      children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(site_icon_default, {})
    }
  ) });
}
var site_icon_link_default = SiteIconLink;

// packages/boot/build-module/store/index.mjs
var import_data2 = __toESM(require_data(), 1);

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
var store = (0, import_data2.createReduxStore)(STORE_NAME, {
  reducer,
  actions: actions_exports,
  selectors: selectors_exports
});
(0, import_data2.register)(store);

// packages/boot/build-module/components/site-hub/index.mjs
var import_jsx_runtime16 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='a9d10ee383']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "a9d10ee383");
  style.appendChild(document.createTextNode(".boot-site-hub{align-items:center;background-color:var(--wpds-color-bg-surface-neutral-weak,#f0f0f0);display:grid;flex-shrink:0;grid-template-columns:60px 1fr auto;padding-right:16px;position:sticky;top:0;z-index:1}.boot-site-hub__actions{flex-shrink:0}.boot-site-hub__title{align-items:center;display:flex;text-decoration:none}.boot-site-hub__title .components-external-link__contents{margin-inline-start:4px;max-width:140px;overflow:hidden;text-decoration:none}.boot-site-hub__title .components-external-link__icon{opacity:0;transition:opacity .1s ease-out}.boot-site-hub__title:hover .components-external-link__icon{opacity:1}@media not (prefers-reduced-motion){.boot-site-hub__title{transition:outline .1s ease-out}}.boot-site-hub__title:focus:not(:active){outline:var(--wpds-border-width-focus,2px) solid var(--wpds-color-stroke-focus-brand,#0073aa);outline-offset:calc(var(--wpds-border-width-focus, 2px)*-1)}.boot-site-hub__title-text{color:var(--wpds-color-fg-content-neutral,#1e1e1e);font-size:13px;font-weight:499}.boot-site-hub__title-text,.boot-site-hub__url{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.boot-site-hub__url{color:var(--wpds-color-fg-content-neutral-weak,#757575);font-size:12px}"));
  document.head.appendChild(style);
}
function SiteHub() {
  const { dashboardLink, homeUrl, siteTitle } = (0, import_data3.useSelect)((select) => {
    const { getEntityRecord } = select(import_core_data2.store);
    const _base = getEntityRecord(
      "root",
      "__unstableBase"
    );
    return {
      dashboardLink: select(store).getDashboardLink(),
      homeUrl: _base?.home,
      siteTitle: !_base?.name && !!_base?.url ? (0, import_url.filterURLForDisplay)(_base?.url) : _base?.name
    };
  }, []);
  const { open: openCommandCenter } = (0, import_data3.useDispatch)(import_commands.store);
  return /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)("div", { className: "boot-site-hub", children: [
    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
      site_icon_link_default,
      {
        to: dashboardLink || "/",
        "aria-label": (0, import_i18n2.__)("Go to the Dashboard")
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(
      import_components4.ExternalLink,
      {
        href: homeUrl ?? "/",
        className: "boot-site-hub__title",
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime16.jsx)("div", { className: "boot-site-hub__title-text", children: siteTitle && (0, import_html_entities.decodeEntities)(siteTitle) }),
          /* @__PURE__ */ (0, import_jsx_runtime16.jsx)("div", { className: "boot-site-hub__url", children: (0, import_url.filterURLForDisplay)(homeUrl ?? "") })
        ]
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_components4.__experimentalHStack, { className: "boot-site-hub__actions", children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
      import_components4.Button,
      {
        variant: "tertiary",
        icon: search_default,
        onClick: () => openCommandCenter(),
        size: "compact",
        label: (0, import_i18n2.__)("Open command palette"),
        shortcut: import_keycodes.displayShortcut.primary("k")
      }
    ) })
  ] });
}
var site_hub_default = SiteHub;

// packages/boot/build-module/components/navigation/index.mjs
var import_element6 = __toESM(require_element(), 1);
var import_data6 = __toESM(require_data(), 1);

// packages/boot/build-module/components/navigation/navigation-item/index.mjs
var import_components7 = __toESM(require_components(), 1);

// packages/boot/build-module/components/navigation/router-link-item.mjs
var import_element3 = __toESM(require_element(), 1);
var import_components5 = __toESM(require_components(), 1);
import { privateApis as routePrivateApis2 } from "@wordpress/route";
var import_jsx_runtime17 = __toESM(require_jsx_runtime(), 1);
var { createLink } = unlock(routePrivateApis2);
function AnchorOnlyItem(props, forwardedRef) {
  return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_components5.__experimentalItem, { as: "a", ref: forwardedRef, ...props });
}
var RouterLinkItem = createLink((0, import_element3.forwardRef)(AnchorOnlyItem));
var router_link_item_default = RouterLinkItem;

// packages/boot/build-module/components/navigation/items.mjs
var import_element4 = __toESM(require_element(), 1);
var import_components6 = __toESM(require_components(), 1);
var import_primitives11 = __toESM(require_primitives(), 1);
var import_jsx_runtime18 = __toESM(require_jsx_runtime(), 1);
function isSvg(element) {
  return (0, import_element4.isValidElement)(element) && (element.type === import_primitives11.SVG || element.type === "svg");
}
function wrapIcon(icon, shouldShowPlaceholder = true) {
  if (isSvg(icon)) {
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_components6.Icon, { icon });
  }
  if (typeof icon === "string" && icon.startsWith("dashicons-")) {
    const iconKey = icon.replace(
      /^dashicons-/,
      ""
    );
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
      import_components6.Dashicon,
      {
        style: { padding: "2px" },
        icon: iconKey,
        "aria-hidden": "true"
      }
    );
  }
  if (typeof icon === "string" && icon.startsWith("data:")) {
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
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
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
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
var import_jsx_runtime19 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='4e28db6d3d']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "4e28db6d3d");
  style.appendChild(document.createTextNode('.boot-navigation-item.components-item{align-items:center;border:none;color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);display:flex;font-family:-apple-system,"system-ui",Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif;font-size:13px;font-weight:400;line-height:20px;margin-block-end:4px;margin-inline:12px;min-height:32px;padding-block:0;padding-inline:4px;width:calc(100% - 24px)}.boot-dropdown-item__children .boot-navigation-item.components-item{min-height:24px}.boot-navigation-item.components-item{border-radius:var(--wpds-border-radius-sm,2px)}.boot-navigation-item.components-item.active,.boot-navigation-item.components-item:focus,.boot-navigation-item.components-item:hover,.boot-navigation-item.components-item[aria-current=true]{color:var(--wpds-color-fg-interactive-brand-active,#0073aa)}.boot-navigation-item.components-item.active{font-weight:499}.boot-navigation-item.components-item svg:last-child{padding:4px}.boot-navigation-item.components-item[aria-current=true]{color:var(--wpds-color-fg-interactive-brand-active,#0073aa);font-weight:499}.boot-navigation-item.components-item:focus-visible{transform:translateZ(0)}.boot-navigation-item.components-item.with-suffix{padding-right:16px}'));
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
  const content = /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(import_components7.__experimentalHStack, { justify: "flex-start", spacing: 2, style: { flexGrow: "1" }, children: [
    wrapIcon(icon, shouldShowPlaceholder),
    /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_components7.FlexBlock, { children })
  ] });
  if (isExternal) {
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
      import_components7.__experimentalItem,
      {
        as: "a",
        href: to,
        className: clsx_default("boot-navigation-item", className),
        children: content
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
    router_link_item_default,
    {
      to,
      className: clsx_default("boot-navigation-item", className),
      children: content
    }
  );
}

// packages/boot/build-module/components/navigation/drilldown-item/index.mjs
var import_components8 = __toESM(require_components(), 1);
var import_i18n3 = __toESM(require_i18n(), 1);
var import_jsx_runtime20 = __toESM(require_jsx_runtime(), 1);
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
  return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
    import_components8.__experimentalItem,
    {
      className: clsx_default("boot-navigation-item", className),
      onClick: handleClick,
      children: /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(
        import_components8.__experimentalHStack,
        {
          justify: "flex-start",
          spacing: 2,
          style: { flexGrow: "1" },
          children: [
            wrapIcon(icon, shouldShowPlaceholder),
            /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(import_components8.FlexBlock, { children }),
            /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(import_components8.Icon, { icon: (0, import_i18n3.isRTL)() ? chevron_left_small_default : chevron_right_small_default })
          ]
        }
      )
    }
  );
}

// packages/boot/build-module/components/navigation/dropdown-item/index.mjs
var import_components9 = __toESM(require_components(), 1);
var import_compose = __toESM(require_compose(), 1);
var import_data4 = __toESM(require_data(), 1);
var import_jsx_runtime21 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='8edb8a7fe3']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "8edb8a7fe3");
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
  const menuItems = (0, import_data4.useSelect)(
    (select) => (
      // @ts-ignore
      select(STORE_NAME).getMenuItems()
    ),
    []
  );
  const items = menuItems.filter((item) => item.parent === id);
  const disableMotion = (0, import_compose.useReducedMotion)();
  return /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)("div", { className: "boot-dropdown-item", children: [
    /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
      import_components9.__experimentalItem,
      {
        className: clsx_default("boot-navigation-item", className),
        onClick: (e) => {
          e.preventDefault();
          e.stopPropagation();
          onToggle();
        },
        onMouseDown: (e) => e.preventDefault(),
        children: /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)(
          import_components9.__experimentalHStack,
          {
            justify: "flex-start",
            spacing: 2,
            style: { flexGrow: "1" },
            children: [
              wrapIcon(icon, false),
              /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_components9.FlexBlock, { children }),
              /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
                import_components9.Icon,
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
    /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_components9.__unstableAnimatePresence, { initial: false, children: isExpanded && /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
      import_components9.__unstableMotion.div,
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
        children: items.map((item, index) => /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
          NavigationItem,
          {
            to: item.to,
            shouldShowPlaceholder: false,
            children: item.label
          },
          index
        ))
      }
    ) })
  ] });
}

// packages/boot/build-module/components/navigation/navigation-screen/index.mjs
var import_components10 = __toESM(require_components(), 1);
var import_i18n4 = __toESM(require_i18n(), 1);
var import_compose2 = __toESM(require_compose(), 1);
var import_jsx_runtime22 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='494512e18f']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "494512e18f");
  style.appendChild(document.createTextNode(".boot-navigation-screen{padding-block-end:4px}.boot-navigation-screen .components-text{color:var(--wpds-color-fg-content-neutral,#1e1e1e)}.boot-navigation-screen__title-icon{padding:12px 16px 8px;position:sticky;top:0}.boot-navigation-screen__title{flex-grow:1;overflow-wrap:break-word}.boot-navigation-screen__title.boot-navigation-screen__title,.boot-navigation-screen__title.boot-navigation-screen__title .boot-navigation-screen__title{color:var(--wpds-color-fg-content-neutral,#1e1e1e);line-height:32px}.boot-navigation-screen__actions{display:flex;flex-shrink:0}"));
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
  const icon = (0, import_i18n4.isRTL)() ? chevron_right_default : chevron_left_default;
  const disableMotion = (0, import_compose2.useReducedMotion)();
  const handleBackClick = (e) => {
    e.preventDefault();
    onNavigate({ id: backMenuItem, direction: "backward" });
  };
  return /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
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
      children: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(import_components10.__unstableAnimatePresence, { initial: false, children: /* @__PURE__ */ (0, import_jsx_runtime22.jsxs)(
        import_components10.__unstableMotion.div,
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
            /* @__PURE__ */ (0, import_jsx_runtime22.jsxs)(
              import_components10.__experimentalHStack,
              {
                spacing: 2,
                className: "boot-navigation-screen__title-icon",
                children: [
                  !isRoot && /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
                    import_components10.Button,
                    {
                      ref: backButtonRef,
                      icon,
                      onClick: handleBackClick,
                      label: (0, import_i18n4.__)("Back"),
                      size: "small",
                      variant: "tertiary"
                    }
                  ),
                  /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
                    import_components10.__experimentalHeading,
                    {
                      className: "boot-navigation-screen__title",
                      level: 1,
                      size: "15px",
                      children: title
                    }
                  ),
                  actions && /* @__PURE__ */ (0, import_jsx_runtime22.jsx)("div", { className: "boot-navigation-screen__actions", children: actions })
                ]
              }
            ),
            description && /* @__PURE__ */ (0, import_jsx_runtime22.jsx)("div", { className: "boot-navigation-screen__description", children: description }),
            content
          ]
        },
        navigationKey
      ) })
    }
  );
}

// packages/boot/build-module/components/navigation/use-sidebar-parent.mjs
var import_element5 = __toESM(require_element(), 1);
var import_data5 = __toESM(require_data(), 1);
import { privateApis as routePrivateApis3 } from "@wordpress/route";

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
var { useRouter: useRouter2, useMatches } = unlock(routePrivateApis3);
function useSidebarParent() {
  const matches = useMatches();
  const router = useRouter2();
  const menuItems = (0, import_data5.useSelect)(
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
  const [parentId, setParentId] = (0, import_element5.useState)(
    findDrilldownParent(currentMenuItem?.id, menuItems)
  );
  const [parentDropdownId, setParentDropdownId] = (0, import_element5.useState)(findDropdownParent(currentMenuItem?.id, menuItems));
  (0, import_element5.useEffect)(() => {
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
var import_jsx_runtime23 = __toESM(require_jsx_runtime(), 1);
function Navigation() {
  const backButtonRef = (0, import_element6.useRef)(null);
  const [animationDirection, setAnimationDirection] = (0, import_element6.useState)(null);
  const [parentId, setParentId, parentDropdownId, setParentDropdownId] = useSidebarParent();
  const menuItems = (0, import_data6.useSelect)(
    (select) => (
      // @ts-ignore
      select(STORE_NAME).getMenuItems()
    ),
    []
  );
  const parent = (0, import_element6.useMemo)(
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
  const items = (0, import_element6.useMemo)(
    () => menuItems.filter((item) => item.parent === parentId),
    [menuItems, parentId]
  );
  const hasRealIcons = items.some((item) => !!item.icon);
  return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
    NavigationScreen,
    {
      isRoot: !parent,
      title: parent ? parent.label : "",
      backMenuItem: parent?.parent,
      backButtonRef,
      animationDirection: animationDirection || void 0,
      navigationKey,
      onNavigate: handleNavigate,
      content: /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_jsx_runtime23.Fragment, { children: items.map((item) => {
        if (item.parent_type === "dropdown") {
          return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
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
          return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
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
        return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
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
var import_element8 = __toESM(require_element(), 1);
var import_data8 = __toESM(require_data(), 1);
var import_i18n6 = __toESM(require_i18n(), 1);
var import_core_data4 = __toESM(require_core_data(), 1);
var import_keycodes2 = __toESM(require_keycodes(), 1);
var import_editor2 = __toESM(require_editor(), 1);
var import_components11 = __toESM(require_components(), 1);

// packages/boot/build-module/components/save-panel/use-save-shortcut.mjs
var import_element7 = __toESM(require_element(), 1);
var import_keyboard_shortcuts = __toESM(require_keyboard_shortcuts(), 1);
var import_i18n5 = __toESM(require_i18n(), 1);
var import_data7 = __toESM(require_data(), 1);
var import_core_data3 = __toESM(require_core_data(), 1);
var import_editor = __toESM(require_editor(), 1);
var shortcutName = "core/boot/save";
function useSaveShortcut({
  openSavePanel
}) {
  const { __experimentalGetDirtyEntityRecords, isSavingEntityRecord } = (0, import_data7.useSelect)(import_core_data3.store);
  const { hasNonPostEntityChanges, isPostSavingLocked } = (0, import_data7.useSelect)(import_editor.store);
  const { savePost } = (0, import_data7.useDispatch)(import_editor.store);
  const { registerShortcut, unregisterShortcut } = (0, import_data7.useDispatch)(
    import_keyboard_shortcuts.store
  );
  (0, import_element7.useEffect)(() => {
    registerShortcut({
      name: shortcutName,
      category: "global",
      description: (0, import_i18n5.__)("Save your changes."),
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
var import_jsx_runtime24 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='8c18be4b4f']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "8c18be4b4f");
  style.appendChild(document.createTextNode(".boot-save-button{width:100%}"));
  document.head.appendChild(style);
}
function SaveButton() {
  const [isSaveViewOpen, setIsSaveViewOpened] = (0, import_element8.useState)(false);
  const { isSaving, dirtyEntityRecordsCount } = (0, import_data8.useSelect)((select) => {
    const { isSavingEntityRecord, __experimentalGetDirtyEntityRecords } = select(import_core_data4.store);
    const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();
    return {
      isSaving: dirtyEntityRecords.some(
        (record) => isSavingEntityRecord(record.kind, record.name, record.key)
      ),
      dirtyEntityRecordsCount: dirtyEntityRecords.length
    };
  }, []);
  const [showSavedState, setShowSavedState] = (0, import_element8.useState)(false);
  (0, import_element8.useEffect)(() => {
    if (isSaving) {
      setShowSavedState(true);
    }
  }, [isSaving]);
  const hasChanges = dirtyEntityRecordsCount > 0;
  (0, import_element8.useEffect)(() => {
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
  const disabled = isSaving || isInSavedState;
  const getLabel = () => {
    if (isInSavedState) {
      return (0, import_i18n6.__)("Saved");
    }
    return (0, import_i18n6.sprintf)(
      // translators: %d: number of unsaved changes (number).
      (0, import_i18n6._n)(
        "Review %d change\u2026",
        "Review %d changes\u2026",
        dirtyEntityRecordsCount
      ),
      dirtyEntityRecordsCount
    );
  };
  const label = getLabel();
  return /* @__PURE__ */ (0, import_jsx_runtime24.jsxs)(import_jsx_runtime24.Fragment, { children: [
    /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
      import_components11.Tooltip,
      {
        text: hasChanges ? label : void 0,
        shortcut: import_keycodes2.displayShortcut.primary("s"),
        children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
          import_components11.Button,
          {
            variant: "primary",
            size: "compact",
            onClick: () => setIsSaveViewOpened(true),
            onBlur: hideSavedState,
            disabled,
            accessibleWhenDisabled: true,
            isBusy: isSaving,
            "aria-keyshortcuts": import_keycodes2.rawShortcut.primary("s"),
            className: "boot-save-button",
            icon: isInSavedState ? check_default : void 0,
            children: label
          }
        )
      }
    ),
    isSaveViewOpen && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
      import_components11.Modal,
      {
        title: (0, import_i18n6.__)("Review changes"),
        onRequestClose: () => setIsSaveViewOpened(false),
        size: "small",
        children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
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
var import_jsx_runtime25 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='fcaf61698a']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "fcaf61698a");
  style.appendChild(document.createTextNode(".boot-sidebar__scrollable{display:flex;flex-direction:column;height:100%;overflow:auto;position:relative}.boot-sidebar__content{contain:content;flex-grow:1;position:relative}.boot-sidebar__footer{padding:16px 8px 8px 16px}"));
  document.head.appendChild(style);
}
function Sidebar() {
  return /* @__PURE__ */ (0, import_jsx_runtime25.jsxs)("div", { className: "boot-sidebar__scrollable", children: [
    /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(site_hub_default, {}),
    /* @__PURE__ */ (0, import_jsx_runtime25.jsx)("div", { className: "boot-sidebar__content", children: /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(navigation_default, {}) }),
    /* @__PURE__ */ (0, import_jsx_runtime25.jsx)("div", { className: "boot-sidebar__footer", children: /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(SaveButton, {}) })
  ] });
}

// packages/boot/build-module/components/save-panel/index.mjs
var import_element9 = __toESM(require_element(), 1);
var import_components12 = __toESM(require_components(), 1);
var import_editor3 = __toESM(require_editor(), 1);
var import_i18n7 = __toESM(require_i18n(), 1);
var import_jsx_runtime26 = __toESM(require_jsx_runtime(), 1);
function SavePanel() {
  const [isOpen, setIsOpen] = (0, import_element9.useState)(false);
  useSaveShortcut({
    openSavePanel: () => setIsOpen(true)
  });
  if (!isOpen) {
    return false;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
    import_components12.Modal,
    {
      className: "edit-site-save-panel__modal",
      onRequestClose: () => setIsOpen(false),
      title: (0, import_i18n7.__)("Review changes"),
      size: "small",
      children: /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
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
var import_element11 = __toESM(require_element(), 1);

// packages/boot/build-module/components/canvas/index.mjs
var import_element10 = __toESM(require_element(), 1);
var import_components14 = __toESM(require_components(), 1);
import { useNavigate } from "@wordpress/route";

// packages/boot/build-module/components/canvas/back-button.mjs
var import_components13 = __toESM(require_components(), 1);
var import_compose3 = __toESM(require_compose(), 1);
var import_i18n8 = __toESM(require_i18n(), 1);
var import_jsx_runtime27 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='2321fdae3b']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "2321fdae3b");
  style.appendChild(document.createTextNode(".boot-canvas-back-button{height:64px;left:0;position:absolute;top:0;width:64px;z-index:100}.boot-canvas-back-button__container{height:100%;position:relative;width:100%}.boot-canvas-back-button__link.components-button{align-items:center;background:var(--wpds-color-bg-surface-neutral-weak);border-radius:0;display:inline-flex;height:64px;justify-content:center;padding:0;text-decoration:none;width:64px}@media not (prefers-reduced-motion){.boot-canvas-back-button__link.components-button{transition:outline .1s ease-out}}.boot-canvas-back-button__link.components-button:focus:not(:active){outline:var(--wpds-border-width-focus) solid var(--wpds-color-stroke-focus-brand);outline-offset:calc(var(--wpds-border-width-focus)*-1)}.boot-canvas-back-button__icon{align-items:center;background-color:#ccc;display:flex;height:64px;justify-content:center;left:0;pointer-events:none;position:absolute;top:0;width:64px}.boot-canvas-back-button__icon svg{fill:currentColor}.boot-canvas-back-button__icon.has-site-icon{-webkit-backdrop-filter:saturate(180%) blur(15px);backdrop-filter:saturate(180%) blur(15px);background-color:#fff9}.interface-interface-skeleton__header{margin-top:0!important}"));
  document.head.appendChild(style);
}
var toggleHomeIconVariants = {
  edit: {
    opacity: 0,
    scale: 0.2
  },
  hover: {
    opacity: 1,
    scale: 1,
    clipPath: "inset( 22% round 2px )"
  }
};
function BootBackButton({ length }) {
  const disableMotion = (0, import_compose3.useReducedMotion)();
  const handleBack = () => {
    window.history.back();
  };
  if (length > 1) {
    return null;
  }
  const transition = {
    duration: disableMotion ? 0 : 0.3
  };
  return /* @__PURE__ */ (0, import_jsx_runtime27.jsxs)(
    import_components13.__unstableMotion.div,
    {
      className: "boot-canvas-back-button",
      animate: "edit",
      initial: "edit",
      whileHover: "hover",
      whileTap: "tap",
      transition,
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
          import_components13.Button,
          {
            className: "boot-canvas-back-button__link",
            onClick: handleBack,
            "aria-label": (0, import_i18n8.__)("Go back"),
            __next40pxDefaultSize: true,
            children: /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(site_icon_default, {})
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
          import_components13.__unstableMotion.div,
          {
            className: "boot-canvas-back-button__icon",
            variants: toggleHomeIconVariants,
            children: /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(import_components13.Icon, { icon: arrow_up_left_default })
          }
        )
      ]
    }
  );
}

// packages/boot/build-module/components/canvas/index.mjs
var import_jsx_runtime28 = __toESM(require_jsx_runtime(), 1);
function Canvas({ canvas }) {
  const [Editor, setEditor] = (0, import_element10.useState)(null);
  const navigate = useNavigate();
  (0, import_element10.useEffect)(() => {
    import("@wordpress/lazy-editor").then((module) => {
      setEditor(() => module.Editor);
    }).catch((error) => {
      console.error("Failed to load lazy editor:", error);
    });
  }, []);
  if (!Editor) {
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      "div",
      {
        style: {
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          height: "100%",
          padding: "2rem"
        },
        children: /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(import_components14.Spinner, {})
      }
    );
  }
  const backButton = !canvas.isPreview ? ({ length }) => /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(BootBackButton, { length }) : void 0;
  return /* @__PURE__ */ (0, import_jsx_runtime28.jsxs)("div", { style: { height: "100%", position: "relative" }, children: [
    /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      "div",
      {
        style: { height: "100%" },
        inert: canvas.isPreview ? "true" : void 0,
        children: /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
          Editor,
          {
            postType: canvas.postType,
            postId: canvas.postId,
            settings: { isPreviewMode: canvas.isPreview },
            backButton
          }
        )
      }
    ),
    canvas.isPreview && canvas.editLink && /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
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
var import_jsx_runtime29 = __toESM(require_jsx_runtime(), 1);
function CanvasRenderer({
  canvas,
  routeContentModule
}) {
  const [CustomCanvas, setCustomCanvas] = (0, import_element11.useState)(null);
  (0, import_element11.useEffect)(() => {
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
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(CustomCanvas, {});
  }
  return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(Canvas, { canvas });
}

// packages/boot/build-module/components/app/use-route-title.mjs
var import_element12 = __toESM(require_element(), 1);
var import_data9 = __toESM(require_data(), 1);
var import_core_data5 = __toESM(require_core_data(), 1);
var import_i18n9 = __toESM(require_i18n(), 1);
var import_html_entities2 = __toESM(require_html_entities(), 1);
import { speak } from "@wordpress/a11y";
import { privateApis as routePrivateApis4 } from "@wordpress/route";
var { useLocation, useMatches: useMatches2 } = unlock(routePrivateApis4);
function useRouteTitle() {
  const location = useLocation();
  const matches = useMatches2();
  const currentMatch = matches[matches.length - 1];
  const routeTitle = currentMatch?.loaderData?.title;
  const siteTitle = (0, import_data9.useSelect)(
    (select) => select(import_core_data5.store).getEntityRecord(
      "root",
      "__unstableBase"
    )?.name,
    []
  );
  const isInitialLocationRef = (0, import_element12.useRef)(true);
  (0, import_element12.useEffect)(() => {
    isInitialLocationRef.current = false;
  }, [location]);
  (0, import_element12.useEffect)(() => {
    if (isInitialLocationRef.current) {
      return;
    }
    if (routeTitle && typeof routeTitle === "string" && siteTitle && typeof siteTitle === "string") {
      const decodedRouteTitle = (0, import_html_entities2.decodeEntities)(routeTitle);
      const decodedSiteTitle = (0, import_html_entities2.decodeEntities)(siteTitle);
      const formattedTitle = (0, import_i18n9.sprintf)(
        /* translators: Admin document title. 1: Admin screen name, 2: Site name. */
        (0, import_i18n9.__)("%1$s \u2039 %2$s \u2014 WordPress"),
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

// packages/boot/build-module/components/user-theme-provider/index.mjs
var import_theme = __toESM(require_theme(), 1);
var import_jsx_runtime30 = __toESM(require_jsx_runtime(), 1);
var ThemeProvider = unlock(import_theme.privateApis).ThemeProvider;
var THEME_PRIMARY_COLORS = /* @__PURE__ */ new Map([
  ["light", "#0085ba"],
  ["modern", "#3858e9"],
  ["blue", "#096484"],
  ["coffee", "#46403c"],
  ["ectoplasm", "#523f6d"],
  ["midnight", "#e14d43"],
  ["ocean", "#627c83"],
  ["sunrise", "#dd823b"]
]);
function getAdminThemePrimaryColor() {
  const theme = document.body.className.match(/admin-color-([a-z]+)/)?.[1];
  return theme && THEME_PRIMARY_COLORS.get(theme);
}
function UserThemeProvider({
  color,
  ...restProps
}) {
  const primary = getAdminThemePrimaryColor();
  return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(ThemeProvider, { ...restProps, color: { primary, ...color } });
}

// packages/boot/build-module/components/root/index.mjs
var import_jsx_runtime31 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='3605662001']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "3605662001");
  style.appendChild(document.createTextNode(".boot-layout{background:var(--wpds-color-bg-surface-neutral-weak,#f0f0f0);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-direction:row;height:100%;isolation:isolate;width:100%}.boot-layout__sidebar-backdrop{background-color:#00000080;bottom:0;cursor:pointer;left:0;position:fixed;right:0;top:0;z-index:100002}.boot-layout__sidebar{flex-shrink:0;height:100%;overflow:hidden;position:relative;width:240px}.boot-layout__sidebar.is-mobile{background:var(--wpds-color-bg-surface-neutral-weak,#f0f0f0);bottom:0;box-shadow:2px 0 8px #0003;left:0;max-width:85vw;position:fixed;top:0;width:300px;z-index:100003}.boot-layout__mobile-sidebar-drawer{left:0;position:fixed;right:0;top:0}.boot-layout--single-page .boot-layout__mobile-sidebar-drawer{top:46px}.boot-layout__mobile-sidebar-drawer{align-items:center;background:var(--wpds-color-bg-surface-neutral,#fff);border-bottom:1px solid var(--wpds-color-stroke-surface-neutral-weak,#ddd);display:flex;justify-content:flex-start;padding:16px;z-index:3}.boot-layout__canvas.has-mobile-drawer{padding-top:65px;position:relative}.boot-layout__surfaces{display:flex;flex-grow:1;gap:8px;margin:0}@media (min-width:782px){.boot-layout__surfaces{margin:8px}.boot-layout--single-page .boot-layout__surfaces{margin-left:0;margin-top:0}}.boot-layout__inspector,.boot-layout__stage{background:var(--wpds-color-bg-surface-neutral,#fff);border-radius:0;bottom:0;color:var(--wpds-color-fg-content-neutral,#1e1e1e);flex:1;height:100vh;left:0;margin:0;overflow-y:auto;position:relative;position:fixed;right:0;top:0;width:100vw}.boot-layout--single-page .boot-layout__inspector,.boot-layout--single-page .boot-layout__stage{height:calc(100vh - 46px);top:46px}@media (min-width:782px){.boot-layout__inspector,.boot-layout__stage{border-radius:8px;height:auto;margin:0;position:static;width:auto}}.boot-layout__stage{z-index:2}@media (min-width:782px){.boot-layout__stage{z-index:auto}}.boot-layout__inspector{z-index:3}@media (min-width:782px){.boot-layout__inspector{z-index:auto}}.boot-layout__canvas{background:var(--wpds-color-bg-surface-neutral,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral-weak,#ddd);border-radius:0;bottom:0;box-shadow:0 1px 3px #0000001a;color:var(--wpds-color-fg-content-neutral,#1e1e1e);flex:1;height:100vh;left:0;margin:0;overflow-y:auto;position:relative;position:fixed;right:0;top:0;width:100vw;z-index:1}.boot-layout--single-page .boot-layout__canvas{height:calc(100vh - 46px);top:46px}@media (min-width:782px){.boot-layout__canvas{border-radius:8px;height:auto;position:static;width:auto;z-index:auto}.boot-layout.has-canvas .boot-layout__stage,.boot-layout__inspector{max-width:400px}}.boot-layout__canvas .interface-interface-skeleton{height:100%;left:0!important;position:relative;top:0!important}.boot-layout.has-full-canvas .boot-layout__surfaces{gap:0;margin:0}.boot-layout.has-full-canvas .boot-layout__inspector,.boot-layout.has-full-canvas .boot-layout__stage{display:none}.boot-layout.has-full-canvas .boot-layout__canvas{border:none;border-radius:0;bottom:0;box-shadow:none;left:0;margin:0;max-width:none;overflow:hidden;position:fixed;right:0;top:0}.boot-layout--single-page .boot-layout.has-full-canvas .boot-layout__canvas{top:46px}@media (min-width:782px){.boot-layout--single-page .boot-layout.has-full-canvas .boot-layout__canvas{top:32px}}"));
  document.head.appendChild(style);
}
var { useLocation: useLocation2, useMatches: useMatches3, Outlet } = unlock(routePrivateApis5);
function Root() {
  const matches = useMatches3();
  const location = useLocation2();
  const currentMatch = matches[matches.length - 1];
  const canvas = currentMatch?.loaderData?.canvas;
  const routeContentModule = currentMatch?.loaderData?.routeContentModule;
  const isFullScreen = canvas && !canvas.isPreview;
  useRouteTitle();
  const isMobileViewport = (0, import_compose4.useViewportMatch)("medium", "<");
  const [isMobileSidebarOpen, setIsMobileSidebarOpen] = (0, import_element13.useState)(false);
  const disableMotion = (0, import_compose4.useReducedMotion)();
  (0, import_element13.useEffect)(() => {
    setIsMobileSidebarOpen(false);
  }, [location.pathname, isMobileViewport]);
  return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components15.SlotFillProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(UserThemeProvider, { isRoot: true, color: { bg: "#f8f8f8" }, children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(UserThemeProvider, { color: { bg: "#1d2327" }, children: /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
    "div",
    {
      className: clsx_default("boot-layout", {
        "has-canvas": !!canvas || canvas === null,
        "has-full-canvas": isFullScreen
      }),
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(SavePanel, {}),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_notices.SnackbarNotices, { className: "boot-notices__snackbar" }),
        isMobileViewport && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(page_default.SidebarToggleFill, { children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
          import_components15.Button,
          {
            icon: menu_default,
            onClick: () => setIsMobileSidebarOpen(true),
            label: (0, import_i18n10.__)("Open navigation panel"),
            size: "compact"
          }
        ) }),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components15.__unstableAnimatePresence, { children: isMobileViewport && isMobileSidebarOpen && !isFullScreen && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
          import_components15.__unstableMotion.div,
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
                setIsMobileSidebarOpen(false);
              }
            },
            role: "button",
            tabIndex: -1,
            "aria-label": (0, import_i18n10.__)(
              "Close navigation panel"
            )
          }
        ) }),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components15.__unstableAnimatePresence, { children: isMobileViewport && isMobileSidebarOpen && !isFullScreen && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
          import_components15.__unstableMotion.div,
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
            children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(Sidebar, {})
          }
        ) }),
        !isMobileViewport && !isFullScreen && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)("div", { className: "boot-layout__sidebar", children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(Sidebar, {}) }),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)("div", { className: "boot-layout__surfaces", children: /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(UserThemeProvider, { color: { bg: "#ffffff" }, children: [
          /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(Outlet, {}),
          (canvas || canvas === null) && /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
            "div",
            {
              className: clsx_default(
                "boot-layout__canvas",
                {
                  "has-mobile-drawer": canvas?.isPreview && isMobileViewport
                }
              ),
              children: [
                canvas?.isPreview && isMobileViewport && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)("div", { className: "boot-layout__mobile-sidebar-drawer", children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                  import_components15.Button,
                  {
                    icon: menu_default,
                    onClick: () => setIsMobileSidebarOpen(
                      true
                    ),
                    label: (0, import_i18n10.__)(
                      "Open navigation panel"
                    ),
                    size: "compact"
                  }
                ) }),
                /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                  CanvasRenderer,
                  {
                    canvas,
                    routeContentModule
                  }
                )
              ]
            }
          )
        ] }) })
      ]
    }
  ) }) }) });
}

// packages/boot/build-module/components/app/router.mjs
var import_jsx_runtime32 = __toESM(require_jsx_runtime(), 1);
var {
  createLazyRoute,
  createRouter,
  createRootRoute,
  createRoute,
  RouterProvider,
  createBrowserHistory,
  parseHref,
  useLoaderData
} = unlock(routePrivateApis6);
function NotFoundComponent() {
  return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("div", { className: "boot-layout__stage", children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(page_default, { title: (0, import_i18n11.__)("Route not found"), hasPadding: true, children: (0, import_i18n11.__)("The page you're looking for does not exist") }) });
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
        (0, import_data10.resolveSelect)(import_core_data6.store).getEntityRecord(
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
        return /* @__PURE__ */ (0, import_jsx_runtime32.jsxs)(import_jsx_runtime32.Fragment, { children: [
          Stage && /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("div", { className: "boot-layout__stage", children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(Stage, {}) }),
          Inspector && showInspector && /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("div", { className: "boot-layout__inspector", children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(Inspector, {}) })
        ] });
      }
    });
  });
  return tanstackRoute;
}
function createRouteTree(routes, rootComponent = Root) {
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
  rootComponent = Root
}) {
  const router = (0, import_element14.useMemo)(() => {
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
  return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(RouterProvider, { router });
}

// packages/boot/build-module/components/root/single-page.mjs
var import_notices2 = __toESM(require_notices(), 1);
var import_components16 = __toESM(require_components(), 1);
import { privateApis as routePrivateApis7 } from "@wordpress/route";
var import_jsx_runtime33 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='3605662001']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "3605662001");
  style.appendChild(document.createTextNode(".boot-layout{background:var(--wpds-color-bg-surface-neutral-weak,#f0f0f0);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-direction:row;height:100%;isolation:isolate;width:100%}.boot-layout__sidebar-backdrop{background-color:#00000080;bottom:0;cursor:pointer;left:0;position:fixed;right:0;top:0;z-index:100002}.boot-layout__sidebar{flex-shrink:0;height:100%;overflow:hidden;position:relative;width:240px}.boot-layout__sidebar.is-mobile{background:var(--wpds-color-bg-surface-neutral-weak,#f0f0f0);bottom:0;box-shadow:2px 0 8px #0003;left:0;max-width:85vw;position:fixed;top:0;width:300px;z-index:100003}.boot-layout__mobile-sidebar-drawer{left:0;position:fixed;right:0;top:0}.boot-layout--single-page .boot-layout__mobile-sidebar-drawer{top:46px}.boot-layout__mobile-sidebar-drawer{align-items:center;background:var(--wpds-color-bg-surface-neutral,#fff);border-bottom:1px solid var(--wpds-color-stroke-surface-neutral-weak,#ddd);display:flex;justify-content:flex-start;padding:16px;z-index:3}.boot-layout__canvas.has-mobile-drawer{padding-top:65px;position:relative}.boot-layout__surfaces{display:flex;flex-grow:1;gap:8px;margin:0}@media (min-width:782px){.boot-layout__surfaces{margin:8px}.boot-layout--single-page .boot-layout__surfaces{margin-left:0;margin-top:0}}.boot-layout__inspector,.boot-layout__stage{background:var(--wpds-color-bg-surface-neutral,#fff);border-radius:0;bottom:0;color:var(--wpds-color-fg-content-neutral,#1e1e1e);flex:1;height:100vh;left:0;margin:0;overflow-y:auto;position:relative;position:fixed;right:0;top:0;width:100vw}.boot-layout--single-page .boot-layout__inspector,.boot-layout--single-page .boot-layout__stage{height:calc(100vh - 46px);top:46px}@media (min-width:782px){.boot-layout__inspector,.boot-layout__stage{border-radius:8px;height:auto;margin:0;position:static;width:auto}}.boot-layout__stage{z-index:2}@media (min-width:782px){.boot-layout__stage{z-index:auto}}.boot-layout__inspector{z-index:3}@media (min-width:782px){.boot-layout__inspector{z-index:auto}}.boot-layout__canvas{background:var(--wpds-color-bg-surface-neutral,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral-weak,#ddd);border-radius:0;bottom:0;box-shadow:0 1px 3px #0000001a;color:var(--wpds-color-fg-content-neutral,#1e1e1e);flex:1;height:100vh;left:0;margin:0;overflow-y:auto;position:relative;position:fixed;right:0;top:0;width:100vw;z-index:1}.boot-layout--single-page .boot-layout__canvas{height:calc(100vh - 46px);top:46px}@media (min-width:782px){.boot-layout__canvas{border-radius:8px;height:auto;position:static;width:auto;z-index:auto}.boot-layout.has-canvas .boot-layout__stage,.boot-layout__inspector{max-width:400px}}.boot-layout__canvas .interface-interface-skeleton{height:100%;left:0!important;position:relative;top:0!important}.boot-layout.has-full-canvas .boot-layout__surfaces{gap:0;margin:0}.boot-layout.has-full-canvas .boot-layout__inspector,.boot-layout.has-full-canvas .boot-layout__stage{display:none}.boot-layout.has-full-canvas .boot-layout__canvas{border:none;border-radius:0;bottom:0;box-shadow:none;left:0;margin:0;max-width:none;overflow:hidden;position:fixed;right:0;top:0}.boot-layout--single-page .boot-layout.has-full-canvas .boot-layout__canvas{top:46px}@media (min-width:782px){.boot-layout--single-page .boot-layout.has-full-canvas .boot-layout__canvas{top:32px}}"));
  document.head.appendChild(style);
}
var { useMatches: useMatches4, Outlet: Outlet2 } = unlock(routePrivateApis7);
function RootSinglePage() {
  const matches = useMatches4();
  const currentMatch = matches[matches.length - 1];
  const canvas = currentMatch?.loaderData?.canvas;
  const routeContentModule = currentMatch?.loaderData?.routeContentModule;
  const isFullScreen = canvas && !canvas.isPreview;
  useRouteTitle();
  return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components16.SlotFillProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(UserThemeProvider, { isRoot: true, color: { bg: "#f8f8f8" }, children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(UserThemeProvider, { color: { bg: "#1d2327" }, children: /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(
    "div",
    {
      className: clsx_default(
        "boot-layout boot-layout--single-page",
        {
          "has-canvas": !!canvas || canvas === null,
          "has-full-canvas": isFullScreen
        }
      ),
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(SavePanel, {}),
        /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_notices2.SnackbarNotices, { className: "boot-notices__snackbar" }),
        /* @__PURE__ */ (0, import_jsx_runtime33.jsx)("div", { className: "boot-layout__surfaces", children: /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(UserThemeProvider, { color: { bg: "#ffffff" }, children: [
          /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(Outlet2, {}),
          (canvas || canvas === null) && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)("div", { className: "boot-layout__canvas", children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
            CanvasRenderer,
            {
              canvas,
              routeContentModule
            }
          ) })
        ] }) })
      ]
    }
  ) }) }) });
}

// packages/boot/build-module/components/app/index.mjs
var import_jsx_runtime34 = __toESM(require_jsx_runtime(), 1);
function App({ rootComponent }) {
  const routes = (0, import_data11.useSelect)((select) => select(store).getRoutes(), []);
  return /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(Router, { routes, rootComponent });
}
async function init({
  mountId,
  menuItems,
  routes,
  initModules,
  dashboardLink
}) {
  (menuItems ?? []).forEach((menuItem) => {
    (0, import_data11.dispatch)(store).registerMenuItem(menuItem.id, menuItem);
  });
  (routes ?? []).forEach((route) => {
    (0, import_data11.dispatch)(store).registerRoute(route);
  });
  if (dashboardLink) {
    (0, import_data11.dispatch)(store).setDashboardLink(dashboardLink);
  }
  for (const moduleId of initModules ?? []) {
    const module = await import(moduleId);
    await module.init();
  }
  const rootElement = document.getElementById(mountId);
  if (rootElement) {
    const root = (0, import_element15.createRoot)(rootElement);
    root.render(
      /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_element15.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(App, {}) })
    );
  }
}
async function initSinglePage({
  mountId,
  routes
}) {
  (routes ?? []).forEach((route) => {
    (0, import_data11.dispatch)(store).registerRoute(route);
  });
  const rootElement = document.getElementById(mountId);
  if (rootElement) {
    const root = (0, import_element15.createRoot)(rootElement);
    root.render(
      /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_element15.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(App, { rootComponent: RootSinglePage }) })
    );
  }
}

// packages/boot/build-module/index.mjs
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='cb8c847fbc']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "cb8c847fbc");
  style.appendChild(document.createTextNode(':root{--wpds-border-radius-lg:8px;--wpds-border-radius-md:4px;--wpds-border-radius-sm:2px;--wpds-border-radius-xs:1px;--wpds-border-width-focus:2px;--wpds-border-width-lg:8px;--wpds-border-width-md:4px;--wpds-border-width-sm:2px;--wpds-border-width-xs:1px;--wpds-color-bg-interactive-brand-strong:#3858e9;--wpds-color-bg-interactive-brand-strong-active:#2e49d9;--wpds-color-bg-interactive-brand-weak:#0000;--wpds-color-bg-interactive-brand-weak-active:#e6eaf4;--wpds-color-bg-interactive-error:#0000;--wpds-color-bg-interactive-error-active:#fff6f4;--wpds-color-bg-interactive-error-strong:#cc1818;--wpds-color-bg-interactive-error-strong-active:#b90000;--wpds-color-bg-interactive-error-weak:#0000;--wpds-color-bg-interactive-error-weak-active:#f6e6e3;--wpds-color-bg-interactive-neutral-strong:#2d2d2d;--wpds-color-bg-interactive-neutral-strong-active:#1e1e1e;--wpds-color-bg-interactive-neutral-strong-disabled:#e2e2e2;--wpds-color-bg-interactive-neutral-weak:#0000;--wpds-color-bg-interactive-neutral-weak-active:#eaeaea;--wpds-color-bg-interactive-neutral-weak-disabled:#0000;--wpds-color-bg-surface-brand:#ecf0f9;--wpds-color-bg-surface-caution:#fee994;--wpds-color-bg-surface-caution-weak:#fff9c9;--wpds-color-bg-surface-error:#f6e6e3;--wpds-color-bg-surface-error-weak:#fff6f4;--wpds-color-bg-surface-info:#deebfa;--wpds-color-bg-surface-info-weak:#f2f9ff;--wpds-color-bg-surface-neutral:#f8f8f8;--wpds-color-bg-surface-neutral-strong:#fff;--wpds-color-bg-surface-neutral-weak:#f0f0f0;--wpds-color-bg-surface-success:#c5f7cc;--wpds-color-bg-surface-success-weak:#eaffed;--wpds-color-bg-surface-warning:#fde6bd;--wpds-color-bg-surface-warning-weak:#fff7e0;--wpds-color-bg-thumb-brand:#3858e9;--wpds-color-bg-thumb-brand-active:#3858e9;--wpds-color-bg-thumb-neutral-disabled:#d8d8d8;--wpds-color-bg-thumb-neutral-weak:#8a8a8a;--wpds-color-bg-thumb-neutral-weak-active:#6c6c6c;--wpds-color-bg-track-neutral:#d8d8d8;--wpds-color-bg-track-neutral-weak:#e0e0e0;--wpds-color-fg-content-caution:#281d00;--wpds-color-fg-content-caution-weak:#826a00;--wpds-color-fg-content-error:#470000;--wpds-color-fg-content-error-weak:#cc1818;--wpds-color-fg-content-info:#001b4f;--wpds-color-fg-content-info-weak:#006bd7;--wpds-color-fg-content-neutral:#1e1e1e;--wpds-color-fg-content-neutral-weak:#6d6d6d;--wpds-color-fg-content-success:#002900;--wpds-color-fg-content-success-weak:#007f30;--wpds-color-fg-content-warning:#2e1900;--wpds-color-fg-content-warning-weak:#926300;--wpds-color-fg-interactive-brand:#3858e9;--wpds-color-fg-interactive-brand-active:#3858e9;--wpds-color-fg-interactive-brand-strong:#eff0f2;--wpds-color-fg-interactive-brand-strong-active:#eff0f2;--wpds-color-fg-interactive-error:#cc1818;--wpds-color-fg-interactive-error-active:#cc1818;--wpds-color-fg-interactive-error-strong:#f2efef;--wpds-color-fg-interactive-error-strong-active:#f2efef;--wpds-color-fg-interactive-neutral:#1e1e1e;--wpds-color-fg-interactive-neutral-active:#1e1e1e;--wpds-color-fg-interactive-neutral-disabled:#8a8a8a;--wpds-color-fg-interactive-neutral-strong:#f0f0f0;--wpds-color-fg-interactive-neutral-strong-active:#f0f0f0;--wpds-color-fg-interactive-neutral-strong-disabled:#8a8a8a;--wpds-color-fg-interactive-neutral-weak:#6d6d6d;--wpds-color-fg-interactive-neutral-weak-disabled:#8a8a8a;--wpds-color-stroke-focus-brand:#3858e9;--wpds-color-stroke-interactive-brand:#3858e9;--wpds-color-stroke-interactive-brand-active:#2337c8;--wpds-color-stroke-interactive-error:#cc1818;--wpds-color-stroke-interactive-error-active:#9d0000;--wpds-color-stroke-interactive-error-strong:#cc1818;--wpds-color-stroke-interactive-neutral:#8a8a8a;--wpds-color-stroke-interactive-neutral-active:#6c6c6c;--wpds-color-stroke-interactive-neutral-disabled:#d8d8d8;--wpds-color-stroke-interactive-neutral-strong:#6c6c6c;--wpds-color-stroke-surface-brand:#a3b1d4;--wpds-color-stroke-surface-brand-strong:#3858e9;--wpds-color-stroke-surface-error:#daa39b;--wpds-color-stroke-surface-error-strong:#cc1818;--wpds-color-stroke-surface-info:#9fbcdc;--wpds-color-stroke-surface-info-strong:#006bd7;--wpds-color-stroke-surface-neutral:#d8d8d8;--wpds-color-stroke-surface-neutral-strong:#8a8a8a;--wpds-color-stroke-surface-neutral-weak:#e0e0e0;--wpds-color-stroke-surface-success:#8ac894;--wpds-color-stroke-surface-success-strong:#007f30;--wpds-color-stroke-surface-warning:#d0b381;--wpds-color-stroke-surface-warning-strong:#926300;--wpds-dimension-base:4px;--wpds-dimension-gap-2xl:32px;--wpds-dimension-gap-3xl:40px;--wpds-dimension-gap-lg:16px;--wpds-dimension-gap-md:12px;--wpds-dimension-gap-sm:8px;--wpds-dimension-gap-xl:24px;--wpds-dimension-gap-xs:4px;--wpds-dimension-padding-2xl:24px;--wpds-dimension-padding-3xl:32px;--wpds-dimension-padding-lg:16px;--wpds-dimension-padding-md:12px;--wpds-dimension-padding-sm:8px;--wpds-dimension-padding-xl:20px;--wpds-dimension-padding-xs:4px;--wpds-elevation-lg:0 5px 15px 0 #00000014,0 15px 27px 0 #00000012,0 30px 36px 0 #0000000a,0 50px 43px 0 #00000005;--wpds-elevation-md:0 2px 3px 0 #0000000d,0 4px 5px 0 #0000000a,0 12px 12px 0 #00000008,0 16px 16px 0 #00000005;--wpds-elevation-sm:0 1px 2px 0 #0000000d,0 2px 3px 0 #0000000a,0 6px 6px 0 #00000008,0 8px 8px 0 #00000005;--wpds-elevation-xs:0 1px 1px 0 #00000008,0 1px 2px 0 #00000005,0 3px 3px 0 #00000005,0 4px 4px 0 #00000003;--wpds-font-family-body:-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif;--wpds-font-family-heading:-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif;--wpds-font-family-mono:"Menlo","Consolas",monaco,monospace;--wpds-font-line-height-2xl:40px;--wpds-font-line-height-lg:28px;--wpds-font-line-height-md:24px;--wpds-font-line-height-sm:20px;--wpds-font-line-height-xl:32px;--wpds-font-line-height-xs:16px;--wpds-font-size-2xl:32px;--wpds-font-size-lg:15px;--wpds-font-size-md:13px;--wpds-font-size-sm:12px;--wpds-font-size-xl:20px;--wpds-font-size-xs:11px;--wpds-font-weight-medium:499;--wpds-font-weight-regular:400}[data-wpds-theme-provider-id][data-wpds-density=compact]{--wpds-dimension-gap-2xl:24px;--wpds-dimension-gap-3xl:32px;--wpds-dimension-gap-lg:12px;--wpds-dimension-gap-md:8px;--wpds-dimension-gap-sm:4px;--wpds-dimension-gap-xl:20px;--wpds-dimension-gap-xs:4px;--wpds-dimension-padding-2xl:20px;--wpds-dimension-padding-3xl:24px;--wpds-dimension-padding-lg:12px;--wpds-dimension-padding-md:8px;--wpds-dimension-padding-sm:4px;--wpds-dimension-padding-xl:16px;--wpds-dimension-padding-xs:4px}[data-wpds-theme-provider-id][data-wpds-density=comfortable]{--wpds-dimension-gap-2xl:40px;--wpds-dimension-gap-3xl:48px;--wpds-dimension-gap-lg:20px;--wpds-dimension-gap-md:16px;--wpds-dimension-gap-sm:12px;--wpds-dimension-gap-xl:32px;--wpds-dimension-gap-xs:8px;--wpds-dimension-padding-2xl:32px;--wpds-dimension-padding-3xl:40px;--wpds-dimension-padding-lg:20px;--wpds-dimension-padding-md:16px;--wpds-dimension-padding-sm:12px;--wpds-dimension-padding-xl:24px;--wpds-dimension-padding-xs:8px}[data-wpds-theme-provider-id][data-wpds-density=default]{--wpds-dimension-base:4px;--wpds-dimension-gap-2xl:32px;--wpds-dimension-gap-3xl:40px;--wpds-dimension-gap-lg:16px;--wpds-dimension-gap-md:12px;--wpds-dimension-gap-sm:8px;--wpds-dimension-gap-xl:24px;--wpds-dimension-gap-xs:4px;--wpds-dimension-padding-2xl:24px;--wpds-dimension-padding-3xl:32px;--wpds-dimension-padding-lg:16px;--wpds-dimension-padding-md:12px;--wpds-dimension-padding-sm:8px;--wpds-dimension-padding-xl:20px;--wpds-dimension-padding-xs:4px}@media (-webkit-min-device-pixel-ratio:2),(min-resolution:192dpi){:root{--wpds-border-width-focus:1.5px}}.admin-ui-page{text-wrap:pretty;background-color:#fff;color:#2f2f2f;display:flex;flex-flow:column;height:100%;position:relative;z-index:1}.admin-ui-page__header{background:#fff;border-bottom:1px solid #f0f0f0;padding:16px 24px;position:sticky;top:0;z-index:1}.admin-ui-page__sidebar-toggle-slot:empty{display:none}.admin-ui-page__header-subtitle{color:#757575;font-family:-apple-system,"system-ui",Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif;font-size:13px;font-weight:400;line-height:20px;margin:0;padding-block-end:8px}.admin-ui-page__content{display:flex;flex-direction:column;flex-grow:1;overflow:auto}.admin-ui-page__content.has-padding{padding:16px 24px}.show-icon-labels .admin-ui-page__header-actions .components-button.has-icon{padding:0 8px;width:auto}.show-icon-labels .admin-ui-page__header-actions .components-button.has-icon svg{display:none}.show-icon-labels .admin-ui-page__header-actions .components-button.has-icon:after{content:attr(aria-label);font-size:12px}.admin-ui-breadcrumbs__list{font-size:15px;font-weight:500;gap:0;list-style:none;margin:0;min-height:32px;padding:0}.admin-ui-breadcrumbs__list li:not(:last-child):after{content:"/";margin:0 8px}.admin-ui-breadcrumbs__list h1{font-size:inherit;line-height:inherit}@media (min-width:600px){.boot-layout-container .boot-layout{bottom:0;left:0;min-height:calc(100vh - 46px);position:absolute;right:0;top:0}}@media (min-width:782px){.boot-layout-container .boot-layout{min-height:calc(100vh - 32px)}body:has(.boot-layout.has-full-canvas) .boot-layout-container .boot-layout{min-height:100vh}}.boot-layout-container .boot-layout img{height:auto;max-width:100%}.boot-layout .boot-notices__snackbar{align-items:center;bottom:24px;box-sizing:border-box;display:flex;flex-direction:column;left:50%;max-width:calc(100vw - 32px);position:fixed;right:auto;transform:translateX(-50%);width:max-content}'));
  document.head.appendChild(style);
}
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='3f9bd815b3']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "3f9bd815b3");
  style.appendChild(document.createTextNode("@media (max-width:782px){*{view-transition-name:none!important}}::view-transition-new(root),::view-transition-old(root){animation-duration:.25s}@media not (prefers-reduced-motion:reduce){.boot-layout__canvas .interface-interface-skeleton__header{view-transition-name:boot--canvas-header}.boot-layout__canvas .interface-interface-skeleton__sidebar{view-transition-name:boot--canvas-sidebar}.boot-layout.has-full-canvas .boot-layout__canvas .boot-site-icon-link,.boot-layout:not(.has-full-canvas) .boot-site-hub .boot-site-icon-link{view-transition-name:boot--site-icon-link}.boot-layout__stage{view-transition-name:boot--stage}.boot-layout__inspector{view-transition-name:boot--inspector}.boot-layout__canvas.is-full-canvas .interface-interface-skeleton__content,.boot-layout__canvas:not(.is-full-canvas){view-transition-name:boot--canvas}@supports (-webkit-hyphens:none) and (not (-moz-appearance:none)){.boot-layout__stage{view-transition-name:boot-safari--stage}.boot-layout__inspector{view-transition-name:boot-safari--inspector}.boot-layout__canvas.is-full-canvas .interface-interface-skeleton__content,.boot-layout__canvas:not(.is-full-canvas){view-transition-name:boot-safari--canvas}}.components-popover:first-of-type{view-transition-name:boot--components-popover}}::view-transition-group(boot--canvas),::view-transition-group(boot--canvas-header),::view-transition-group(boot--canvas-sidebar),::view-transition-group(boot-safari--canvas){z-index:1}::view-transition-group(boot--site-icon-link){z-index:2}::view-transition-new(boot--site-icon-link),::view-transition-old(boot--site-icon-link){animation:none}::view-transition-new(boot-safari--canvas),::view-transition-new(boot-safari--inspector),::view-transition-new(boot-safari--stage),::view-transition-old(boot-safari--canvas),::view-transition-old(boot-safari--inspector),::view-transition-old(boot-safari--stage){width:auto}::view-transition-new(boot--canvas),::view-transition-new(boot--inspector),::view-transition-new(boot--stage),::view-transition-old(boot--canvas),::view-transition-old(boot--inspector),::view-transition-old(boot--stage){background:#fff;border-radius:8px;height:100%;object-fit:none;object-position:left top;overflow:hidden;width:100%}::view-transition-new(boot--canvas),::view-transition-old(boot--canvas){object-position:center top}::view-transition-old(boot--inspector):only-child,::view-transition-old(boot--stage):only-child,::view-transition-old(boot-safari--inspector):only-child,::view-transition-old(boot-safari--stage):only-child{animation-name:zoomOut;will-change:transform,opacity}::view-transition-new(boot--inspector):only-child,::view-transition-new(boot--stage):only-child,::view-transition-new(boot-safari--inspector):only-child,::view-transition-new(boot-safari--stage):only-child{animation-name:zoomIn;will-change:transform,opacity}@keyframes zoomOut{0%{opacity:1;transform:scale(1)}to{opacity:0;transform:scale(.9)}}@keyframes zoomIn{0%{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}::view-transition-new(boot--canvas):only-child,::view-transition-new(boot-safari--canvas):only-child{animation-name:slideFromRight;will-change:transform}::view-transition-old(boot--canvas):only-child,::view-transition-old(boot-safari--canvas):only-child{animation-name:slideToRight;will-change:transform}@keyframes slideFromRight{0%{transform:translateX(100vw)}to{transform:translateX(0)}}@keyframes slideToRight{0%{transform:translateX(0)}to{transform:translateX(100vw)}}::view-transition-new(boot--canvas-header):only-child{animation-name:slideHeaderFromTop;will-change:transform}::view-transition-old(boot--canvas-header):only-child{animation-name:slideHeaderToTop;will-change:transform}@keyframes slideHeaderFromTop{0%{transform:translateY(-100%)}}@keyframes slideHeaderToTop{to{transform:translateY(-100%)}}::view-transition-new(boot--canvas-sidebar):only-child{animation-name:slideSidebarFromRight;will-change:transform}::view-transition-old(boot--canvas-sidebar):only-child{animation-name:slideSidebarToRight;will-change:transform}@keyframes slideSidebarFromRight{0%{transform:translateX(100%)}}@keyframes slideSidebarToRight{to{transform:translateX(100%)}}"));
  document.head.appendChild(style);
}
export {
  init,
  initSinglePage,
  store
};