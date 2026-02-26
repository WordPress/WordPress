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

// package-external:@wordpress/data
var require_data = __commonJS({
  "package-external:@wordpress/data"(exports, module) {
    module.exports = window.wp.data;
  }
});

// package-external:@wordpress/api-fetch
var require_api_fetch = __commonJS({
  "package-external:@wordpress/api-fetch"(exports, module) {
    module.exports = window.wp.apiFetch;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
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

// routes/connectors-home/stage.tsx
var import_components4 = __toESM(require_components());
var import_data = __toESM(require_data());
var import_element3 = __toESM(require_element());
var import_i18n3 = __toESM(require_i18n());
import {
  privateApis as connectorsPrivateApis
} from "@wordpress/connectors";

// routes/connectors-home/style.scss
if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='2ca9f0b249']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "2ca9f0b249");
  style.appendChild(document.createTextNode(".connectors-page{margin:0 auto;max-width:680px;padding:24px;width:100%}.connectors-page .components-item{background:#fff;border:1px solid #ddd;border-radius:8px;overflow:hidden;padding:20px}.connectors-page .connector-settings .components-text-control__input{font-family:monospace}.connectors-page>p{color:#949494;text-align:center}"));
  document.head.appendChild(style);
}

// routes/connectors-home/default-connectors.tsx
var import_components3 = __toESM(require_components());
var import_i18n2 = __toESM(require_i18n());
import {
  __experimentalRegisterConnector as registerConnector,
  __experimentalConnectorItem as ConnectorItem,
  __experimentalDefaultConnectorSettings as DefaultConnectorSettings
} from "@wordpress/connectors";

// routes/connectors-home/use-connector-plugin.ts
var import_api_fetch = __toESM(require_api_fetch());
var import_element2 = __toESM(require_element());
var import_i18n = __toESM(require_i18n());
function useConnectorPlugin({
  pluginSlug,
  settingName
}) {
  const [pluginStatus, setPluginStatus] = (0, import_element2.useState)("checking");
  const [isExpanded, setIsExpanded] = (0, import_element2.useState)(false);
  const [isBusy, setIsBusy] = (0, import_element2.useState)(false);
  const [currentApiKey, setCurrentApiKey] = (0, import_element2.useState)("");
  const isConnected = pluginStatus === "active" && currentApiKey !== "" && currentApiKey !== "invalid_key";
  const fetchApiKey = (0, import_element2.useCallback)(async () => {
    try {
      const settings = await (0, import_api_fetch.default)({
        path: `/wp/v2/settings?_fields=${settingName}`
      });
      const key = settings[settingName] || "";
      setCurrentApiKey(key === "invalid_key" ? "" : key);
    } catch {
    }
  }, [settingName]);
  (0, import_element2.useEffect)(() => {
    const checkPluginStatus = async () => {
      try {
        const plugins = await (0, import_api_fetch.default)({
          path: "/wp/v2/plugins"
        });
        const plugin = plugins.find(
          (p) => p.plugin === `${pluginSlug}/plugin`
        );
        if (!plugin) {
          setPluginStatus("not-installed");
        } else if (plugin.status === "active") {
          await fetchApiKey();
          setPluginStatus("active");
        } else {
          setPluginStatus("inactive");
        }
      } catch {
        setPluginStatus("not-installed");
      }
    };
    checkPluginStatus();
  }, [pluginSlug, fetchApiKey]);
  const installPlugin = async () => {
    setIsBusy(true);
    try {
      await (0, import_api_fetch.default)({
        method: "POST",
        path: "/wp/v2/plugins",
        data: { slug: pluginSlug, status: "active" }
      });
      setPluginStatus("active");
      await fetchApiKey();
      setIsExpanded(true);
    } catch {
    } finally {
      setIsBusy(false);
    }
  };
  const activatePlugin = async () => {
    setIsBusy(true);
    try {
      await (0, import_api_fetch.default)({
        method: "PUT",
        path: `/wp/v2/plugins/${pluginSlug}/plugin`,
        data: { status: "active" }
      });
      setPluginStatus("active");
      await fetchApiKey();
      setIsExpanded(true);
    } catch {
    } finally {
      setIsBusy(false);
    }
  };
  const handleButtonClick = () => {
    if (pluginStatus === "not-installed") {
      installPlugin();
    } else if (pluginStatus === "inactive") {
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
    try {
      const result = await (0, import_api_fetch.default)({
        method: "POST",
        path: `/wp/v2/settings?_fields=${settingName}`,
        data: {
          [settingName]: apiKey
        }
      });
      if (apiKey && result[settingName] === currentApiKey) {
        throw new Error(
          "It was not possible to connect to the provider using this key."
        );
      }
      setCurrentApiKey(result[settingName] || "");
    } catch (error) {
      console.error("Failed to save API key:", error);
      throw error;
    }
  };
  const removeApiKey = async () => {
    try {
      await (0, import_api_fetch.default)({
        method: "POST",
        path: `/wp/v2/settings?_fields=${settingName}`,
        data: {
          [settingName]: ""
        }
      });
      setCurrentApiKey("");
    } catch (error) {
      console.error("Failed to remove API key:", error);
      throw error;
    }
  };
  return {
    pluginStatus,
    isExpanded,
    setIsExpanded,
    isBusy,
    isConnected,
    currentApiKey,
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
    xmlns: "http://www.w3.org/2000/svg"
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
    xmlns: "http://www.w3.org/2000/svg"
  },
  /* @__PURE__ */ React.createElement(
    "path",
    {
      d: "M6.2 21.024L12.416 17.536L12.52 17.232L12.416 17.064H12.112L11.072 17L7.52 16.904L4.44 16.776L1.456 16.616L0.704 16.456L0 15.528L0.072 15.064L0.704 14.64L1.608 14.72L3.608 14.856L6.608 15.064L8.784 15.192L12.008 15.528H12.52L12.592 15.32L12.416 15.192L12.28 15.064L9.176 12.96L5.816 10.736L4.056 9.456L3.104 8.808L2.624 8.2L2.416 6.872L3.28 5.92L4.44 6L4.736 6.08L5.912 6.984L8.424 8.928L11.704 11.344L12.184 11.744L12.376 11.608L12.4 11.512L12.184 11.152L10.4 7.928L8.496 4.648L7.648 3.288L7.424 2.472C7.344 2.136 7.288 1.856 7.288 1.512L8.272 0.176L8.816 0L10.128 0.176L10.68 0.656L11.496 2.52L12.816 5.456L14.864 9.448L15.464 10.632L15.784 11.728L15.904 12.064H16.112V11.872L16.28 9.624L16.592 6.864L16.896 3.312L17 2.312L17.496 1.112L18.48 0.464L19.248 0.832L19.88 1.736L19.792 2.32L19.416 4.76L18.68 8.584L18.2 11.144H18.48L18.8 10.824L20.096 9.104L22.272 6.384L23.232 5.304L24.352 4.112L25.072 3.544H26.432L27.432 5.032L26.984 6.568L25.584 8.344L24.424 9.848L22.76 12.088L21.72 13.88L21.816 14.024L22.064 14L25.824 13.2L27.856 12.832L30.28 12.416L31.376 12.928L31.496 13.448L31.064 14.512L28.472 15.152L25.432 15.76L20.904 16.832L20.848 16.872L20.912 16.952L22.952 17.144L23.824 17.192H25.96L29.936 17.488L30.976 18.176L31.6 19.016L31.496 19.656L29.896 20.472L27.736 19.96L22.696 18.76L20.968 18.328H20.728V18.472L22.168 19.88L24.808 22.264L28.112 25.336L28.28 26.096L27.856 26.696L27.408 26.632L24.504 24.448L23.384 23.464L20.848 21.328H20.68V21.552L21.264 22.408L24.352 27.048L24.512 28.472L24.288 28.936L23.488 29.216L22.608 29.056L20.8 26.52L18.936 23.664L17.432 21.104L17.248 21.208L16.36 30.768L15.944 31.256L14.984 31.624L14.184 31.016L13.76 30.032L14.184 28.088L14.696 25.552L15.112 23.536L15.488 21.032L15.712 20.2L15.696 20.144L15.512 20.168L13.624 22.76L10.752 26.64L8.48 29.072L7.936 29.288L6.992 28.8L7.08 27.928L7.608 27.152L10.752 23.152L12.648 20.672L13.872 19.24L13.864 19.032H13.792L5.44 24.456L3.952 24.648L3.312 24.048L3.392 23.064L3.696 22.744L6.208 21.016L6.2 21.024Z",
      fill: "#D97757"
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
    xmlns: "http://www.w3.org/2000/svg"
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
function ProviderConnector({
  label,
  description,
  pluginSlug,
  settingName,
  helpUrl,
  helpLabel,
  Logo
}) {
  const {
    pluginStatus,
    isExpanded,
    setIsExpanded,
    isBusy,
    isConnected,
    currentApiKey,
    handleButtonClick,
    getButtonLabel,
    saveApiKey,
    removeApiKey
  } = useConnectorPlugin({
    pluginSlug,
    settingName
  });
  return /* @__PURE__ */ React.createElement(
    ConnectorItem,
    {
      className: `connector-item--${pluginSlug}`,
      icon: /* @__PURE__ */ React.createElement(Logo, null),
      name: label,
      description,
      actionArea: /* @__PURE__ */ React.createElement(import_components3.__experimentalHStack, { spacing: 3, expanded: false }, isConnected && /* @__PURE__ */ React.createElement(ConnectedBadge, null), /* @__PURE__ */ React.createElement(
        import_components3.Button,
        {
          variant: isExpanded || isConnected ? "tertiary" : "secondary",
          size: isExpanded || isConnected ? void 0 : "compact",
          onClick: handleButtonClick,
          disabled: pluginStatus === "checking" || isBusy,
          isBusy,
          "aria-expanded": isExpanded
        },
        getButtonLabel()
      ))
    },
    isExpanded && pluginStatus === "active" && /* @__PURE__ */ React.createElement(
      DefaultConnectorSettings,
      {
        key: isConnected ? "connected" : "setup",
        initialValue: currentApiKey,
        helpUrl,
        helpLabel,
        readOnly: isConnected,
        onRemove: removeApiKey,
        onSave: async (apiKey) => {
          await saveApiKey(apiKey);
          setIsExpanded(false);
        }
      }
    )
  );
}
function OpenAIConnector(props) {
  return /* @__PURE__ */ React.createElement(
    ProviderConnector,
    {
      ...props,
      pluginSlug: "ai-provider-for-openai",
      settingName: "connectors_ai_openai_api_key",
      helpUrl: "https://platform.openai.com",
      helpLabel: "platform.openai.com",
      Logo: OpenAILogo
    }
  );
}
function ClaudeConnector(props) {
  return /* @__PURE__ */ React.createElement(
    ProviderConnector,
    {
      ...props,
      pluginSlug: "ai-provider-for-anthropic",
      settingName: "connectors_ai_anthropic_api_key",
      helpUrl: "https://console.anthropic.com",
      helpLabel: "console.anthropic.com",
      Logo: ClaudeLogo
    }
  );
}
function GeminiConnector(props) {
  return /* @__PURE__ */ React.createElement(
    ProviderConnector,
    {
      ...props,
      pluginSlug: "ai-provider-for-google",
      settingName: "connectors_ai_google_api_key",
      helpUrl: "https://aistudio.google.com",
      helpLabel: "aistudio.google.com",
      Logo: GeminiLogo
    }
  );
}
function registerDefaultConnectors() {
  registerConnector("core/openai", {
    label: (0, import_i18n2.__)("OpenAI"),
    description: (0, import_i18n2.__)(
      "Text, image, and code generation with GPT and DALL-E."
    ),
    render: OpenAIConnector
  });
  registerConnector("core/claude", {
    label: (0, import_i18n2.__)("Claude"),
    description: (0, import_i18n2.__)("Writing, research, and analysis with Claude."),
    render: ClaudeConnector
  });
  registerConnector("core/gemini", {
    label: (0, import_i18n2.__)("Gemini"),
    description: (0, import_i18n2.__)(
      "Content generation, translation, and vision with Google's Gemini."
    ),
    render: GeminiConnector
  });
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
  const connectors = (0, import_data.useSelect)(
    (select) => unlock(select(store)).getConnectors(),
    []
  );
  return /* @__PURE__ */ React.createElement(
    page_default,
    {
      title: (0, import_i18n3.__)("Connectors"),
      subTitle: (0, import_i18n3.__)(
        "All of your API keys and credentials are stored here and shared across plugins. Configure once and use everywhere."
      )
    },
    /* @__PURE__ */ React.createElement("div", { className: "connectors-page" }, /* @__PURE__ */ React.createElement(import_components4.__experimentalVStack, { spacing: 3 }, connectors.map((connector) => {
      if (connector.render) {
        return /* @__PURE__ */ React.createElement(
          connector.render,
          {
            key: connector.slug,
            slug: connector.slug,
            label: connector.label,
            description: connector.description
          }
        );
      }
      return null;
    })), /* @__PURE__ */ React.createElement("p", null, (0, import_element3.createInterpolateElement)(
      (0, import_i18n3.__)(
        "Find more connectors in <a>the plugin directory</a>"
      ),
      {
        a: (
          // eslint-disable-next-line jsx-a11y/anchor-has-content
          /* @__PURE__ */ React.createElement("a", { href: "plugin-install.php" })
        )
      }
    )))
  );
}
function Stage() {
  return /* @__PURE__ */ React.createElement(ConnectorsPage, null);
}
var stage = Stage;
export {
  stage
};
