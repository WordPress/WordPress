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

// package-external:@wordpress/data
var require_data = __commonJS({
  "package-external:@wordpress/data"(exports, module) {
    module.exports = window.wp.data;
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

// package-external:@wordpress/element
var require_element = __commonJS({
  "package-external:@wordpress/element"(exports, module) {
    module.exports = window.wp.element;
  }
});

// package-external:@wordpress/i18n
var require_i18n = __commonJS({
  "package-external:@wordpress/i18n"(exports, module) {
    module.exports = window.wp.i18n;
  }
});

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
  }
});

// packages/connectors/build-module/api.mjs
var import_data2 = __toESM(require_data(), 1);

// packages/connectors/build-module/store.mjs
var import_data = __toESM(require_data(), 1);

// packages/connectors/build-module/lock-unlock.mjs
var import_private_apis = __toESM(require_private_apis(), 1);
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/connectors"
);

// packages/connectors/build-module/store.mjs
var STORE_NAME = "core/connectors";
var DEFAULT_STATE = {
  connectors: {}
};
var actions = {
  registerConnector(slug, config) {
    return {
      type: "REGISTER_CONNECTOR",
      slug,
      config
    };
  }
};
function reducer(state = DEFAULT_STATE, action) {
  switch (action.type) {
    case "REGISTER_CONNECTOR":
      return {
        ...state,
        connectors: {
          ...state.connectors,
          [action.slug]: {
            slug: action.slug,
            ...action.config
          }
        }
      };
    default:
      return state;
  }
}
var selectors = {
  getConnectors(state) {
    return Object.values(state.connectors);
  },
  getConnector(state, slug) {
    return state.connectors[slug];
  }
};
var store = (0, import_data.createReduxStore)(STORE_NAME, {
  reducer
});
(0, import_data.register)(store);
unlock(store).registerPrivateActions(actions);
unlock(store).registerPrivateSelectors(selectors);

// packages/connectors/build-module/api.mjs
function registerConnector(slug, config) {
  unlock((0, import_data2.dispatch)(store)).registerConnector(slug, config);
}

// packages/connectors/build-module/connector-item.mjs
var import_components = __toESM(require_components(), 1);
var import_element = __toESM(require_element(), 1);
var import_i18n = __toESM(require_i18n(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
function ConnectorItem({
  className,
  icon,
  name,
  description,
  actionArea,
  children
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalItem, { className, children: /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_components.__experimentalVStack, { spacing: 4, children: [
    /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_components.__experimentalHStack, { alignment: "center", spacing: 4, children: [
      icon,
      /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.FlexBlock, { children: /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_components.__experimentalVStack, { spacing: 0, children: [
        /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalText, { weight: 600, size: 15, children: name }),
        /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalText, { variant: "muted", size: 12, children: description })
      ] }) }),
      actionArea
    ] }),
    children
  ] }) });
}
function DefaultConnectorSettings({
  onSave,
  onRemove,
  initialValue = "",
  helpUrl,
  helpLabel,
  readOnly = false
}) {
  const [apiKey, setApiKey] = (0, import_element.useState)(initialValue);
  const [isSaving, setIsSaving] = (0, import_element.useState)(false);
  const [saveError, setSaveError] = (0, import_element.useState)(null);
  const helpLinkLabel = helpLabel || helpUrl?.replace(/^https?:\/\//, "");
  const helpLink = helpUrl ? /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_jsx_runtime.Fragment, { children: [
    (0, import_i18n.__)("Get your API key at"),
    " ",
    /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.ExternalLink, { href: helpUrl, children: helpLinkLabel })
  ] }) : void 0;
  const getHelp = () => {
    if (readOnly) {
      return /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_jsx_runtime.Fragment, { children: [
        (0, import_i18n.__)(
          "Your API key is stored securely. You can reset it at"
        ),
        " ",
        helpUrl ? /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.ExternalLink, { href: helpUrl, children: helpLinkLabel }) : void 0
      ] });
    }
    if (saveError) {
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("span", { style: { color: "#cc1818" }, children: saveError });
    }
    return helpLink;
  };
  const handleSave = async () => {
    setSaveError(null);
    setIsSaving(true);
    try {
      await onSave?.(apiKey);
    } catch (error) {
      setSaveError(
        error instanceof Error ? error.message : (0, import_i18n.__)(
          "It was not possible to connect to the provider using this key."
        )
      );
    } finally {
      setIsSaving(false);
    }
  };
  return /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(
    import_components.__experimentalVStack,
    {
      spacing: 4,
      className: "connector-settings",
      style: readOnly ? {
        "--wp-components-color-background": "#f0f0f0"
      } : void 0,
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
          import_components.TextControl,
          {
            __nextHasNoMarginBottom: true,
            __next40pxDefaultSize: true,
            label: (0, import_i18n.__)("API Key"),
            value: apiKey,
            onChange: (value) => {
              if (!readOnly) {
                setSaveError(null);
                setApiKey(value);
              }
            },
            placeholder: "YOUR_API_KEY",
            disabled: readOnly || isSaving,
            help: getHelp()
          }
        ),
        readOnly ? /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.Button, { variant: "link", isDestructive: true, onClick: onRemove, children: (0, import_i18n.__)("Remove and replace") }) : /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalHStack, { justify: "flex-start", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
          import_components.Button,
          {
            __next40pxDefaultSize: true,
            variant: "primary",
            disabled: !apiKey || isSaving,
            accessibleWhenDisabled: true,
            isBusy: isSaving,
            onClick: handleSave,
            children: (0, import_i18n.__)("Save")
          }
        ) })
      ]
    }
  );
}

// packages/connectors/build-module/private-apis.mjs
var privateApis = {};
lock(privateApis, { store, STORE_NAME });
export {
  ConnectorItem as __experimentalConnectorItem,
  DefaultConnectorSettings as __experimentalDefaultConnectorSettings,
  registerConnector as __experimentalRegisterConnector,
  privateApis
};