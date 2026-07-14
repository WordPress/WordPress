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
  },
  unregisterConnector(slug) {
    return {
      type: "UNREGISTER_CONNECTOR",
      slug
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
            ...state.connectors[action.slug],
            slug: action.slug,
            ...action.config
          }
        }
      };
    case "UNREGISTER_CONNECTOR": {
      if (!state.connectors[action.slug]) {
        return state;
      }
      const { [action.slug]: _, ...rest } = state.connectors;
      return {
        ...state,
        connectors: rest
      };
    }
    default:
      return state;
  }
}
var selectors = {
  getConnectors: (0, import_data.createSelector)(
    (state) => Object.values(state.connectors),
    (state) => [state.connectors]
  ),
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
function unregisterConnector(slug) {
  unlock((0, import_data2.dispatch)(store)).unregisterConnector(slug);
}

// packages/connectors/build-module/connector-item.mjs
var import_components = __toESM(require_components(), 1);
var import_element = __toESM(require_element(), 1);
var import_i18n = __toESM(require_i18n(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
function ConnectorItem({
  className,
  logo,
  name,
  description,
  actionArea,
  children
}) {
  const headingId = (0, import_element.useId)();
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalItem, { className, children: /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_components.__experimentalVStack, { spacing: 4, role: "group", "aria-labelledby": headingId, children: [
    /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_components.__experimentalHStack, { alignment: "center", spacing: 4, wrap: true, children: [
      logo,
      /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.FlexBlock, { children: /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_components.__experimentalVStack, { spacing: 0, children: [
        /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
          import_components.__experimentalText,
          {
            weight: "var(--wpds-typography-font-weight-emphasis, 600)",
            size: 15,
            id: headingId,
            as: "h2",
            children: name
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalText, { variant: "muted", size: 12, children: description })
      ] }) }),
      actionArea
    ] }),
    children
  ] }) });
}
function getHelpLinkLabel(helpUrl, helpLabel) {
  if (helpLabel) {
    return helpLabel;
  }
  if (!helpUrl) {
    return void 0;
  }
  try {
    return new URL(helpUrl).hostname;
  } catch {
    return helpUrl;
  }
}
function createConnectorHelpLink(helpUrl, helpLabel, message) {
  if (!helpUrl) {
    return void 0;
  }
  return (0, import_element.createInterpolateElement)(
    (0, import_i18n.sprintf)(message, "<a></a>"),
    {
      a: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.ExternalLink, { href: helpUrl, children: getHelpLinkLabel(helpUrl, helpLabel) })
    }
  );
}
function useConnectorSettingsSave(onSave, fallbackErrorMessage) {
  const [isSaving, setIsSaving] = (0, import_element.useState)(false);
  const [saveError, setSaveError] = (0, import_element.useState)(null);
  const handleSave = async (value) => {
    setSaveError(null);
    setIsSaving(true);
    try {
      await onSave?.(value);
    } catch (error) {
      setSaveError(
        error instanceof Error ? error.message : fallbackErrorMessage
      );
    } finally {
      setIsSaving(false);
    }
  };
  return {
    isSaving,
    saveError,
    setSaveError,
    handleSave
  };
}
function ConnectorSettingsFrame({
  readOnly,
  children
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
    import_components.__experimentalVStack,
    {
      spacing: 4,
      className: "connector-settings",
      style: readOnly ? {
        "--wp-components-color-background": "#f0f0f0"
      } : void 0,
      children
    }
  );
}
function ConnectorSettingsFooter({
  readOnly,
  onRemove,
  canSave,
  isSaving,
  onSave
}) {
  if (readOnly) {
    if (!onRemove) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalHStack, { justify: "flex-start", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.Button, { variant: "link", isDestructive: true, onClick: onRemove, children: (0, import_i18n.__)("Remove and replace") }) });
  }
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalHStack, { justify: "flex-start", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
    import_components.Button,
    {
      __next40pxDefaultSize: true,
      variant: "primary",
      disabled: !canSave || isSaving,
      accessibleWhenDisabled: true,
      isBusy: isSaving,
      onClick: onSave,
      children: (0, import_i18n.__)("Save")
    }
  ) });
}
function DefaultConnectorSettings({
  onSave,
  onRemove,
  initialValue = "",
  helpUrl,
  helpLabel,
  readOnly = false,
  keySource
}) {
  const [apiKey, setApiKey] = (0, import_element.useState)(initialValue);
  const { isSaving, saveError, setSaveError, handleSave } = useConnectorSettingsSave(
    onSave,
    (0, import_i18n.__)(
      "It was not possible to connect to the provider using this key."
    )
  );
  const helpLink = createConnectorHelpLink(
    helpUrl,
    helpLabel,
    /* translators: %s: Link to provider settings. */
    (0, import_i18n.__)("Get your API key at %s")
  );
  const isExternallyConfigured = keySource === "env" || keySource === "constant";
  const getHelp = () => {
    if (isExternallyConfigured) {
      if (keySource === "env") {
        return (0, import_i18n.__)(
          "This API key is configured using an environment variable."
        );
      }
      if (keySource === "constant") {
        return (0, import_i18n.__)("This API key is configured as a constant.");
      }
    }
    if (readOnly) {
      return helpUrl ? createConnectorHelpLink(
        helpUrl,
        helpLabel,
        /* translators: %s: Link to provider settings. */
        (0, import_i18n.__)(
          "Your API key is stored securely. You can manage it at %s"
        )
      ) : (0, import_i18n.__)("Your API key is stored securely.");
    }
    if (saveError) {
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("span", { role: "alert", className: "connector-settings__error", children: saveError });
    }
    return helpLink;
  };
  return /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(ConnectorSettingsFrame, { readOnly, children: [
    /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      import_components.TextControl,
      {
        label: (0, import_i18n.__)("API Key"),
        value: apiKey,
        onChange: (value) => {
          if (!readOnly) {
            setSaveError(null);
            setApiKey(value);
          }
        },
        placeholder: (0, import_i18n.__)("Enter your API key"),
        disabled: readOnly || isSaving,
        autoComplete: "off",
        help: getHelp()
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      ConnectorSettingsFooter,
      {
        readOnly,
        onRemove,
        canSave: !!apiKey,
        isSaving,
        onSave: () => handleSave(apiKey)
      }
    )
  ] });
}
function ApplicationPasswordConnectorSettings({
  onSave,
  onRemove,
  initialUsername = "",
  helpUrl,
  helpLabel,
  readOnly = false,
  keySource
}) {
  const [username, setUsername] = (0, import_element.useState)(initialUsername);
  const [applicationPassword, setApplicationPassword] = (0, import_element.useState)("");
  const { isSaving, saveError, setSaveError, handleSave } = useConnectorSettingsSave(
    onSave,
    (0, import_i18n.__)("It was not possible to save these credentials.")
  );
  const help = createConnectorHelpLink(
    helpUrl,
    helpLabel,
    /* translators: %s: Link to the remote site's application passwords screen. */
    (0, import_i18n.__)("Create an application password at %s")
  );
  let applicationPasswordHelp = help;
  if (keySource === "env") {
    applicationPasswordHelp = (0, import_i18n.__)(
      "These credentials are configured using an environment variable."
    );
  } else if (keySource === "constant") {
    applicationPasswordHelp = (0, import_i18n.__)(
      "These credentials are configured as a constant."
    );
  } else if (readOnly) {
    applicationPasswordHelp = (0, import_i18n.__)(
      "Your application password is stored securely."
    );
  }
  if (saveError) {
    applicationPasswordHelp = /* @__PURE__ */ (0, import_jsx_runtime.jsx)("span", { role: "alert", className: "connector-settings__error", children: saveError });
  }
  return /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(ConnectorSettingsFrame, { readOnly, children: [
    /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      import_components.TextControl,
      {
        label: (0, import_i18n.__)("Username"),
        value: username,
        onChange: (value) => {
          if (!readOnly) {
            setSaveError(null);
            setUsername(value);
          }
        },
        placeholder: (0, import_i18n.__)("Enter your username"),
        disabled: readOnly || isSaving,
        autoComplete: "username"
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      import_components.TextControl,
      {
        label: (0, import_i18n.__)("Application password"),
        value: readOnly ? "••••••••••••••••" : applicationPassword,
        onChange: (value) => {
          if (!readOnly) {
            setSaveError(null);
            setApplicationPassword(value);
          }
        },
        type: "password",
        placeholder: (0, import_i18n.__)("Enter your application password"),
        disabled: readOnly || isSaving,
        autoComplete: "new-password",
        help: applicationPasswordHelp
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      ConnectorSettingsFooter,
      {
        readOnly,
        onRemove,
        canSave: !!username.trim() && !!applicationPassword,
        isSaving,
        onSave: () => handleSave({
          username: username.trim(),
          applicationPassword
        })
      }
    )
  ] });
}

// packages/connectors/build-module/private-apis.mjs
var privateApis = {};
lock(privateApis, { store, STORE_NAME });
export {
  ApplicationPasswordConnectorSettings as __experimentalApplicationPasswordConnectorSettings,
  ConnectorItem as __experimentalConnectorItem,
  DefaultConnectorSettings as __experimentalDefaultConnectorSettings,
  registerConnector as __experimentalRegisterConnector,
  unregisterConnector as __experimentalUnregisterConnector,
  privateApis
};
