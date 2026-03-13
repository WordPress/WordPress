"use strict";
var wp;
(wp ||= {}).preferences = (() => {
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
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

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

  // package-external:@wordpress/a11y
  var require_a11y = __commonJS({
    "package-external:@wordpress/a11y"(exports, module) {
      module.exports = window.wp.a11y;
    }
  });

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
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

  // packages/preferences/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    PreferenceToggleMenuItem: () => PreferenceToggleMenuItem,
    privateApis: () => privateApis,
    store: () => store
  });

  // packages/preferences/build-module/components/preference-toggle-menu-item/index.js
  var import_data3 = __toESM(require_data());
  var import_components = __toESM(require_components());
  var import_i18n = __toESM(require_i18n());

  // packages/icons/build-module/icon/index.js
  var import_element = __toESM(require_element());
  var icon_default = (0, import_element.forwardRef)(
    ({ icon, size = 24, ...props }, ref) => {
      return (0, import_element.cloneElement)(icon, {
        width: size,
        height: size,
        ...props,
        ref
      });
    }
  );

  // packages/icons/build-module/library/check.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var check_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M16.5 7.5 10 13.9l-2.5-2.4-1 1 3.5 3.6 7.5-7.6z" }) });

  // packages/icons/build-module/library/chevron-left.js
  var import_primitives2 = __toESM(require_primitives());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  var chevron_left_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z" }) });

  // packages/icons/build-module/library/chevron-right.js
  var import_primitives3 = __toESM(require_primitives());
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  var chevron_right_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z" }) });

  // packages/preferences/build-module/components/preference-toggle-menu-item/index.js
  var import_a11y = __toESM(require_a11y());

  // packages/preferences/build-module/store/index.js
  var import_data2 = __toESM(require_data());

  // packages/preferences/build-module/store/reducer.js
  var import_data = __toESM(require_data());
  function defaults(state = {}, action) {
    if (action.type === "SET_PREFERENCE_DEFAULTS") {
      const { scope, defaults: values } = action;
      return {
        ...state,
        [scope]: {
          ...state[scope],
          ...values
        }
      };
    }
    return state;
  }
  function withPersistenceLayer(reducer) {
    let persistenceLayer;
    return (state, action) => {
      if (action.type === "SET_PERSISTENCE_LAYER") {
        const { persistenceLayer: persistence, persistedData } = action;
        persistenceLayer = persistence;
        return persistedData;
      }
      const nextState = reducer(state, action);
      if (action.type === "SET_PREFERENCE_VALUE") {
        persistenceLayer?.set(nextState);
      }
      return nextState;
    };
  }
  var preferences = withPersistenceLayer((state = {}, action) => {
    if (action.type === "SET_PREFERENCE_VALUE") {
      const { scope, name, value } = action;
      return {
        ...state,
        [scope]: {
          ...state[scope],
          [name]: value
        }
      };
    }
    return state;
  });
  var reducer_default = (0, import_data.combineReducers)({
    defaults,
    preferences
  });

  // packages/preferences/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    set: () => set,
    setDefaults: () => setDefaults,
    setPersistenceLayer: () => setPersistenceLayer,
    toggle: () => toggle
  });
  function toggle(scope, name) {
    return function({ select, dispatch }) {
      const currentValue = select.get(scope, name);
      dispatch.set(scope, name, !currentValue);
    };
  }
  function set(scope, name, value) {
    return {
      type: "SET_PREFERENCE_VALUE",
      scope,
      name,
      value
    };
  }
  function setDefaults(scope, defaults2) {
    return {
      type: "SET_PREFERENCE_DEFAULTS",
      scope,
      defaults: defaults2
    };
  }
  async function setPersistenceLayer(persistenceLayer) {
    const persistedData = await persistenceLayer.get();
    return {
      type: "SET_PERSISTENCE_LAYER",
      persistenceLayer,
      persistedData
    };
  }

  // packages/preferences/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    get: () => get
  });
  var import_deprecated = __toESM(require_deprecated());
  var withDeprecatedKeys = (originalGet) => (state, scope, name) => {
    const settingsToMoveToCore = [
      "allowRightClickOverrides",
      "distractionFree",
      "editorMode",
      "fixedToolbar",
      "focusMode",
      "hiddenBlockTypes",
      "inactivePanels",
      "keepCaretInsideBlock",
      "mostUsedBlocks",
      "openPanels",
      "showBlockBreadcrumbs",
      "showIconLabels",
      "showListViewByDefault",
      "isPublishSidebarEnabled",
      "isComplementaryAreaVisible",
      "pinnedItems"
    ];
    if (settingsToMoveToCore.includes(name) && ["core/edit-post", "core/edit-site"].includes(scope)) {
      (0, import_deprecated.default)(
        `wp.data.select( 'core/preferences' ).get( '${scope}', '${name}' )`,
        {
          since: "6.5",
          alternative: `wp.data.select( 'core/preferences' ).get( 'core', '${name}' )`
        }
      );
      return originalGet(state, "core", name);
    }
    return originalGet(state, scope, name);
  };
  var get = withDeprecatedKeys(
    (state, scope, name) => {
      const value = state.preferences[scope]?.[name];
      return value !== void 0 ? value : state.defaults[scope]?.[name];
    }
  );

  // packages/preferences/build-module/store/constants.js
  var STORE_NAME = "core/preferences";

  // packages/preferences/build-module/store/index.js
  var store = (0, import_data2.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports
  });
  (0, import_data2.register)(store);

  // packages/preferences/build-module/components/preference-toggle-menu-item/index.js
  var import_jsx_runtime4 = __toESM(require_jsx_runtime());
  function PreferenceToggleMenuItem({
    scope,
    name,
    label,
    info,
    messageActivated,
    messageDeactivated,
    shortcut,
    handleToggling = true,
    onToggle = () => null,
    disabled = false
  }) {
    const isActive = (0, import_data3.useSelect)(
      (select) => !!select(store).get(scope, name),
      [scope, name]
    );
    const { toggle: toggle2 } = (0, import_data3.useDispatch)(store);
    const speakMessage = () => {
      if (isActive) {
        const message = messageDeactivated || (0, import_i18n.sprintf)(
          /* translators: %s: preference name, e.g. 'Fullscreen mode' */
          (0, import_i18n.__)("Preference deactivated - %s"),
          label
        );
        (0, import_a11y.speak)(message);
      } else {
        const message = messageActivated || (0, import_i18n.sprintf)(
          /* translators: %s: preference name, e.g. 'Fullscreen mode' */
          (0, import_i18n.__)("Preference activated - %s"),
          label
        );
        (0, import_a11y.speak)(message);
      }
    };
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
      import_components.MenuItem,
      {
        icon: isActive ? check_default : null,
        isSelected: isActive,
        onClick: () => {
          onToggle();
          if (handleToggling) {
            toggle2(scope, name);
          }
          speakMessage();
        },
        role: "menuitemcheckbox",
        info,
        shortcut,
        disabled,
        children: label
      }
    );
  }

  // packages/preferences/build-module/components/preference-base-option/index.js
  var import_components2 = __toESM(require_components());
  var import_jsx_runtime5 = __toESM(require_jsx_runtime());
  function BaseOption({
    help,
    label,
    isChecked,
    onChange,
    children
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime5.jsxs)("div", { className: "preference-base-option", children: [
      /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
        import_components2.ToggleControl,
        {
          __nextHasNoMarginBottom: true,
          help,
          label,
          checked: isChecked,
          onChange
        }
      ),
      children
    ] });
  }
  var preference_base_option_default = BaseOption;

  // packages/preferences/build-module/components/preference-toggle-control/index.js
  var import_data4 = __toESM(require_data());
  var import_jsx_runtime6 = __toESM(require_jsx_runtime());
  function PreferenceToggleControl(props) {
    const {
      scope,
      featureName,
      onToggle = () => {
      },
      ...remainingProps
    } = props;
    const isChecked = (0, import_data4.useSelect)(
      (select) => !!select(store).get(scope, featureName),
      [scope, featureName]
    );
    const { toggle: toggle2 } = (0, import_data4.useDispatch)(store);
    const onChange = () => {
      onToggle();
      toggle2(scope, featureName);
    };
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
      preference_base_option_default,
      {
        ...remainingProps,
        onChange,
        isChecked
      }
    );
  }
  var preference_toggle_control_default = PreferenceToggleControl;

  // packages/preferences/build-module/components/preferences-modal/index.js
  var import_components3 = __toESM(require_components());
  var import_i18n2 = __toESM(require_i18n());
  var import_jsx_runtime7 = __toESM(require_jsx_runtime());
  function PreferencesModal({
    closeModal,
    children
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
      import_components3.Modal,
      {
        className: "preferences-modal",
        title: (0, import_i18n2.__)("Preferences"),
        onRequestClose: closeModal,
        children
      }
    );
  }

  // packages/preferences/build-module/components/preferences-modal-section/index.js
  var import_jsx_runtime8 = __toESM(require_jsx_runtime());
  var Section = ({ description, title, children }) => /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)("fieldset", { className: "preferences-modal__section", children: [
    /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)("legend", { className: "preferences-modal__section-legend", children: [
      /* @__PURE__ */ (0, import_jsx_runtime8.jsx)("h2", { className: "preferences-modal__section-title", children: title }),
      description && /* @__PURE__ */ (0, import_jsx_runtime8.jsx)("p", { className: "preferences-modal__section-description", children: description })
    ] }),
    /* @__PURE__ */ (0, import_jsx_runtime8.jsx)("div", { className: "preferences-modal__section-content", children })
  ] });
  var preferences_modal_section_default = Section;

  // packages/preferences/build-module/components/preferences-modal-tabs/index.js
  var import_compose = __toESM(require_compose());
  var import_components4 = __toESM(require_components());
  var import_element2 = __toESM(require_element());
  var import_i18n3 = __toESM(require_i18n());

  // packages/preferences/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/preferences"
  );

  // packages/preferences/build-module/components/preferences-modal-tabs/index.js
  var import_jsx_runtime9 = __toESM(require_jsx_runtime());
  var { Tabs } = unlock(import_components4.privateApis);
  var PREFERENCES_MENU = "preferences-menu";
  function PreferencesModalTabs({
    sections
  }) {
    const isLargeViewport = (0, import_compose.useViewportMatch)("medium");
    const [activeMenu, setActiveMenu] = (0, import_element2.useState)(PREFERENCES_MENU);
    const { tabs, sectionsContentMap } = (0, import_element2.useMemo)(() => {
      let mappedTabs = {
        tabs: [],
        sectionsContentMap: {}
      };
      if (sections.length) {
        mappedTabs = sections.reduce(
          (accumulator, { name, tabLabel: title, content }) => {
            accumulator.tabs.push({ name, title });
            accumulator.sectionsContentMap[name] = content;
            return accumulator;
          },
          { tabs: [], sectionsContentMap: {} }
        );
      }
      return mappedTabs;
    }, [sections]);
    let modalContent;
    if (isLargeViewport) {
      modalContent = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)("div", { className: "preferences__tabs", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(
        Tabs,
        {
          defaultTabId: activeMenu !== PREFERENCES_MENU ? activeMenu : void 0,
          onSelect: setActiveMenu,
          orientation: "vertical",
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(Tabs.TabList, { className: "preferences__tabs-tablist", children: tabs.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
              Tabs.Tab,
              {
                tabId: tab.name,
                className: "preferences__tabs-tab",
                children: tab.title
              },
              tab.name
            )) }),
            tabs.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
              Tabs.TabPanel,
              {
                tabId: tab.name,
                className: "preferences__tabs-tabpanel",
                focusable: false,
                children: sectionsContentMap[tab.name] || null
              },
              tab.name
            ))
          ]
        }
      ) });
    } else {
      modalContent = /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(import_components4.Navigator, { initialPath: "/", className: "preferences__provider", children: [
        /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components4.Navigator.Screen, { path: "/", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components4.Card, { isBorderless: true, size: "small", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components4.CardBody, { children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components4.__experimentalItemGroup, { children: tabs.map((tab) => {
          return (
            // @ts-expect-error: Navigator.Button is currently typed in a way that prevents Item from being passed in
            /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
              import_components4.Navigator.Button,
              {
                path: `/${tab.name}`,
                as: import_components4.__experimentalItem,
                isAction: true,
                children: /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(import_components4.__experimentalHStack, { justify: "space-between", children: [
                  /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components4.FlexItem, { children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components4.__experimentalTruncate, { children: tab.title }) }),
                  /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components4.FlexItem, { children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
                    icon_default,
                    {
                      icon: (0, import_i18n3.isRTL)() ? chevron_left_default : chevron_right_default
                    }
                  ) })
                ] })
              },
              tab.name
            )
          );
        }) }) }) }) }),
        sections.length && sections.map((section) => {
          return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
            import_components4.Navigator.Screen,
            {
              path: `/${section.name}`,
              children: /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(import_components4.Card, { isBorderless: true, size: "large", children: [
                /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(
                  import_components4.CardHeader,
                  {
                    isBorderless: false,
                    justify: "left",
                    size: "small",
                    gap: "6",
                    as: "div",
                    children: [
                      /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
                        import_components4.Navigator.BackButton,
                        {
                          icon: (0, import_i18n3.isRTL)() ? chevron_right_default : chevron_left_default,
                          label: (0, import_i18n3.__)("Back")
                        }
                      ),
                      /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components4.__experimentalText, { size: "16", children: section.tabLabel })
                    ]
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components4.CardBody, { children: section.content })
              ] })
            },
            `${section.name}-menu`
          );
        })
      ] });
    }
    return modalContent;
  }

  // packages/preferences/build-module/private-apis.js
  var privateApis = {};
  lock(privateApis, {
    PreferenceBaseOption: preference_base_option_default,
    PreferenceToggleControl: preference_toggle_control_default,
    PreferencesModal,
    PreferencesModalSection: preferences_modal_section_default,
    PreferencesModalTabs
  });
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
