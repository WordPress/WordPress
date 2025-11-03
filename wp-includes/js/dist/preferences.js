/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  PreferenceToggleMenuItem: () => (/* reexport */ PreferenceToggleMenuItem),
  privateApis: () => (/* reexport */ privateApis),
  store: () => (/* reexport */ store)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/preferences/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  set: () => (set),
  setDefaults: () => (setDefaults),
  setPersistenceLayer: () => (setPersistenceLayer),
  toggle: () => (toggle)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/preferences/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  get: () => (get)
});

;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// ./node_modules/@wordpress/icons/build-module/library/check.js


var check_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M16.5 7.5 10 13.9l-2.5-2.4-1 1 3.5 3.6 7.5-7.6z" }) });


;// external ["wp","a11y"]
const external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// ./node_modules/@wordpress/preferences/build-module/store/reducer.js

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
const preferences = withPersistenceLayer((state = {}, action) => {
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
var reducer_default = (0,external_wp_data_namespaceObject.combineReducers)({
  defaults,
  preferences
});


;// ./node_modules/@wordpress/preferences/build-module/store/actions.js
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
function setDefaults(scope, defaults) {
  return {
    type: "SET_PREFERENCE_DEFAULTS",
    scope,
    defaults
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


;// external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// ./node_modules/@wordpress/preferences/build-module/store/selectors.js

const withDeprecatedKeys = (originalGet) => (state, scope, name) => {
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
    external_wp_deprecated_default()(
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
const get = withDeprecatedKeys((state, scope, name) => {
  const value = state.preferences[scope]?.[name];
  return value !== void 0 ? value : state.defaults[scope]?.[name];
});


;// ./node_modules/@wordpress/preferences/build-module/store/constants.js
const STORE_NAME = "core/preferences";


;// ./node_modules/@wordpress/preferences/build-module/store/index.js





const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer_default,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);


;// ./node_modules/@wordpress/preferences/build-module/components/preference-toggle-menu-item/index.js







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
  const isActive = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => !!select(store).get(scope, name),
    [scope, name]
  );
  const { toggle } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const speakMessage = () => {
    if (isActive) {
      const message = messageDeactivated || (0,external_wp_i18n_namespaceObject.sprintf)(
        /* translators: %s: preference name, e.g. 'Fullscreen mode' */
        (0,external_wp_i18n_namespaceObject.__)("Preference deactivated - %s"),
        label
      );
      (0,external_wp_a11y_namespaceObject.speak)(message);
    } else {
      const message = messageActivated || (0,external_wp_i18n_namespaceObject.sprintf)(
        /* translators: %s: preference name, e.g. 'Fullscreen mode' */
        (0,external_wp_i18n_namespaceObject.__)("Preference activated - %s"),
        label
      );
      (0,external_wp_a11y_namespaceObject.speak)(message);
    }
  };
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.MenuItem,
    {
      icon: isActive && check_default,
      isSelected: isActive,
      onClick: () => {
        onToggle();
        if (handleToggling) {
          toggle(scope, name);
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


;// ./node_modules/@wordpress/preferences/build-module/components/index.js



;// ./node_modules/@wordpress/preferences/build-module/components/preference-base-option/index.js


function BaseOption({ help, label, isChecked, onChange, children }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "preference-base-option", children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.ToggleControl,
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


;// ./node_modules/@wordpress/preferences/build-module/components/preference-toggle-control/index.js




function PreferenceToggleControl(props) {
  const {
    scope,
    featureName,
    onToggle = () => {
    },
    ...remainingProps
  } = props;
  const isChecked = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => !!select(store).get(scope, featureName),
    [scope, featureName]
  );
  const { toggle } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const onChange = () => {
    onToggle();
    toggle(scope, featureName);
  };
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    preference_base_option_default,
    {
      onChange,
      isChecked,
      ...remainingProps
    }
  );
}
var preference_toggle_control_default = PreferenceToggleControl;


;// ./node_modules/@wordpress/preferences/build-module/components/preferences-modal/index.js



function PreferencesModal({ closeModal, children }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Modal,
    {
      className: "preferences-modal",
      title: (0,external_wp_i18n_namespaceObject.__)("Preferences"),
      onRequestClose: closeModal,
      children
    }
  );
}


;// ./node_modules/@wordpress/preferences/build-module/components/preferences-modal-section/index.js

const Section = ({ description, title, children }) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("fieldset", { className: "preferences-modal__section", children: [
  /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("legend", { className: "preferences-modal__section-legend", children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h2", { className: "preferences-modal__section-title", children: title }),
    description && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "preferences-modal__section-description", children: description })
  ] }),
  /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "preferences-modal__section-content", children })
] });
var preferences_modal_section_default = Section;


;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// ./node_modules/@wordpress/icons/build-module/icon/index.js

var icon_default = (0,external_wp_element_namespaceObject.forwardRef)(
  ({ icon, size = 24, ...props }, ref) => {
    return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
      width: size,
      height: size,
      ...props,
      ref
    });
  }
);


;// ./node_modules/@wordpress/icons/build-module/library/chevron-left.js


var chevron_left_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/chevron-right.js


var chevron_right_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z" }) });


;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/preferences/build-module/lock-unlock.js

const { lock, unlock } = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/preferences"
);


;// ./node_modules/@wordpress/preferences/build-module/components/preferences-modal-tabs/index.js







const { Tabs } = unlock(external_wp_components_namespaceObject.privateApis);
const PREFERENCES_MENU = "preferences-menu";
function PreferencesModalTabs({ sections }) {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium");
  const [activeMenu, setActiveMenu] = (0,external_wp_element_namespaceObject.useState)(PREFERENCES_MENU);
  const { tabs, sectionsContentMap } = (0,external_wp_element_namespaceObject.useMemo)(() => {
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
    modalContent = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "preferences__tabs", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
      Tabs,
      {
        defaultTabId: activeMenu !== PREFERENCES_MENU ? activeMenu : void 0,
        onSelect: setActiveMenu,
        orientation: "vertical",
        children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Tabs.TabList, { className: "preferences__tabs-tablist", children: tabs.map((tab) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            Tabs.Tab,
            {
              tabId: tab.name,
              className: "preferences__tabs-tab",
              children: tab.title
            },
            tab.name
          )) }),
          tabs.map((tab) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
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
    modalContent = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.Navigator, { initialPath: "/", className: "preferences__provider", children: [
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Navigator.Screen, { path: "/", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Card, { isBorderless: true, size: "small", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.CardBody, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalItemGroup, { children: tabs.map((tab) => {
        return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.Navigator.Button,
          {
            path: `/${tab.name}`,
            as: external_wp_components_namespaceObject.__experimentalItem,
            isAction: true,
            children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalHStack, { justify: "space-between", children: [
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.FlexItem, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalTruncate, { children: tab.title }) }),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.FlexItem, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                icon_default,
                {
                  icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_left_default : chevron_right_default
                }
              ) })
            ] })
          },
          tab.name
        );
      }) }) }) }) }),
      sections.length && sections.map((section) => {
        return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.Navigator.Screen,
          {
            path: `/${section.name}`,
            children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.Card, { isBorderless: true, size: "large", children: [
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
                external_wp_components_namespaceObject.CardHeader,
                {
                  isBorderless: false,
                  justify: "left",
                  size: "small",
                  gap: "6",
                  children: [
                    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                      external_wp_components_namespaceObject.Navigator.BackButton,
                      {
                        icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_right_default : chevron_left_default,
                        label: (0,external_wp_i18n_namespaceObject.__)("Back")
                      }
                    ),
                    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalText, { size: "16", children: section.tabLabel })
                  ]
                }
              ),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.CardBody, { children: section.content })
            ] })
          },
          `${section.name}-menu`
        );
      })
    ] });
  }
  return modalContent;
}


;// ./node_modules/@wordpress/preferences/build-module/private-apis.js






const privateApis = {};
lock(privateApis, {
  PreferenceBaseOption: preference_base_option_default,
  PreferenceToggleControl: preference_toggle_control_default,
  PreferencesModal: PreferencesModal,
  PreferencesModalSection: preferences_modal_section_default,
  PreferencesModalTabs: PreferencesModalTabs
});


;// ./node_modules/@wordpress/preferences/build-module/index.js





(window.wp = window.wp || {}).preferences = __webpack_exports__;
/******/ })()
;