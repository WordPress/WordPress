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

;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js
/**
 * WordPress dependencies
 */


const check = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"
  })
});
/* harmony default export */ const library_check = (check);

;// CONCATENATED MODULE: external ["wp","a11y"]
const external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/reducer.js
/**
 * WordPress dependencies
 */


/**
 * Reducer returning the defaults for user preferences.
 *
 * This is kept intentionally separate from the preferences
 * themselves so that defaults are not persisted.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
function defaults(state = {}, action) {
  if (action.type === 'SET_PREFERENCE_DEFAULTS') {
    const {
      scope,
      defaults: values
    } = action;
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

/**
 * Higher order reducer that does the following:
 * - Merges any data from the persistence layer into the state when the
 *   `SET_PERSISTENCE_LAYER` action is received.
 * - Passes any preferences changes to the persistence layer.
 *
 * @param {Function} reducer The preferences reducer.
 *
 * @return {Function} The enhanced reducer.
 */
function withPersistenceLayer(reducer) {
  let persistenceLayer;
  return (state, action) => {
    // Setup the persistence layer, and return the persisted data
    // as the state.
    if (action.type === 'SET_PERSISTENCE_LAYER') {
      const {
        persistenceLayer: persistence,
        persistedData
      } = action;
      persistenceLayer = persistence;
      return persistedData;
    }
    const nextState = reducer(state, action);
    if (action.type === 'SET_PREFERENCE_VALUE') {
      persistenceLayer?.set(nextState);
    }
    return nextState;
  };
}

/**
 * Reducer returning the user preferences.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
const preferences = withPersistenceLayer((state = {}, action) => {
  if (action.type === 'SET_PREFERENCE_VALUE') {
    const {
      scope,
      name,
      value
    } = action;
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
/* harmony default export */ const reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  defaults,
  preferences
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/actions.js
/**
 * Returns an action object used in signalling that a preference should be
 * toggled.
 *
 * @param {string} scope The preference scope (e.g. core/edit-post).
 * @param {string} name  The preference name.
 */
function toggle(scope, name) {
  return function ({
    select,
    dispatch
  }) {
    const currentValue = select.get(scope, name);
    dispatch.set(scope, name, !currentValue);
  };
}

/**
 * Returns an action object used in signalling that a preference should be set
 * to a value
 *
 * @param {string} scope The preference scope (e.g. core/edit-post).
 * @param {string} name  The preference name.
 * @param {*}      value The value to set.
 *
 * @return {Object} Action object.
 */
function set(scope, name, value) {
  return {
    type: 'SET_PREFERENCE_VALUE',
    scope,
    name,
    value
  };
}

/**
 * Returns an action object used in signalling that preference defaults should
 * be set.
 *
 * @param {string}            scope    The preference scope (e.g. core/edit-post).
 * @param {Object<string, *>} defaults A key/value map of preference names to values.
 *
 * @return {Object} Action object.
 */
function setDefaults(scope, defaults) {
  return {
    type: 'SET_PREFERENCE_DEFAULTS',
    scope,
    defaults
  };
}

/** @typedef {() => Promise<Object>} WPPreferencesPersistenceLayerGet */
/** @typedef {(Object) => void} WPPreferencesPersistenceLayerSet */
/**
 * @typedef WPPreferencesPersistenceLayer
 *
 * @property {WPPreferencesPersistenceLayerGet} get An async function that gets data from the persistence layer.
 * @property {WPPreferencesPersistenceLayerSet} set A function that sets data in the persistence layer.
 */

/**
 * Sets the persistence layer.
 *
 * When a persistence layer is set, the preferences store will:
 * - call `get` immediately and update the store state to the value returned.
 * - call `set` with all preferences whenever a preference changes value.
 *
 * `setPersistenceLayer` should ideally be dispatched at the start of an
 * application's lifecycle, before any other actions have been dispatched to
 * the preferences store.
 *
 * @param {WPPreferencesPersistenceLayer} persistenceLayer The persistence layer.
 *
 * @return {Object} Action object.
 */
async function setPersistenceLayer(persistenceLayer) {
  const persistedData = await persistenceLayer.get();
  return {
    type: 'SET_PERSISTENCE_LAYER',
    persistenceLayer,
    persistedData
  };
}

;// CONCATENATED MODULE: external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/selectors.js
/**
 * WordPress dependencies
 */

const withDeprecatedKeys = originalGet => (state, scope, name) => {
  const settingsToMoveToCore = ['allowRightClickOverrides', 'distractionFree', 'editorMode', 'fixedToolbar', 'focusMode', 'hiddenBlockTypes', 'inactivePanels', 'keepCaretInsideBlock', 'mostUsedBlocks', 'openPanels', 'showBlockBreadcrumbs', 'showIconLabels', 'showListViewByDefault', 'isPublishSidebarEnabled', 'isComplementaryAreaVisible', 'pinnedItems'];
  if (settingsToMoveToCore.includes(name) && ['core/edit-post', 'core/edit-site'].includes(scope)) {
    external_wp_deprecated_default()(`wp.data.select( 'core/preferences' ).get( '${scope}', '${name}' )`, {
      since: '6.5',
      alternative: `wp.data.select( 'core/preferences' ).get( 'core', '${name}' )`
    });
    return originalGet(state, 'core', name);
  }
  return originalGet(state, scope, name);
};

/**
 * Returns a boolean indicating whether a prefer is active for a particular
 * scope.
 *
 * @param {Object} state The store state.
 * @param {string} scope The scope of the feature (e.g. core/edit-post).
 * @param {string} name  The name of the feature.
 *
 * @return {*} Is the feature enabled?
 */
const get = withDeprecatedKeys((state, scope, name) => {
  const value = state.preferences[scope]?.[name];
  return value !== undefined ? value : state.defaults[scope]?.[name];
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/preferences';

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





/**
 * Store definition for the preferences namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/components/preference-toggle-menu-item/index.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


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
  const isActive = (0,external_wp_data_namespaceObject.useSelect)(select => !!select(store).get(scope, name), [scope, name]);
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const speakMessage = () => {
    if (isActive) {
      const message = messageDeactivated || (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: preference name, e.g. 'Fullscreen mode' */
      (0,external_wp_i18n_namespaceObject.__)('Preference deactivated - %s'), label);
      (0,external_wp_a11y_namespaceObject.speak)(message);
    } else {
      const message = messageActivated || (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: preference name, e.g. 'Fullscreen mode' */
      (0,external_wp_i18n_namespaceObject.__)('Preference activated - %s'), label);
      (0,external_wp_a11y_namespaceObject.speak)(message);
    }
  };
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, {
    icon: isActive && library_check,
    isSelected: isActive,
    onClick: () => {
      onToggle();
      if (handleToggling) {
        toggle(scope, name);
      }
      speakMessage();
    },
    role: "menuitemcheckbox",
    info: info,
    shortcut: shortcut,
    disabled: disabled,
    children: label
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/components/index.js


;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/components/preference-base-option/index.js
/**
 * WordPress dependencies
 */



function BaseOption({
  help,
  label,
  isChecked,
  onChange,
  children
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
    className: "preference-base-option",
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToggleControl, {
      __nextHasNoMarginBottom: true,
      help: help,
      label: label,
      checked: isChecked,
      onChange: onChange
    }), children]
  });
}
/* harmony default export */ const preference_base_option = (BaseOption);

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/components/preference-toggle-control/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



function PreferenceToggleControl(props) {
  const {
    scope,
    featureName,
    onToggle = () => {},
    ...remainingProps
  } = props;
  const isChecked = (0,external_wp_data_namespaceObject.useSelect)(select => !!select(store).get(scope, featureName), [scope, featureName]);
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const onChange = () => {
    onToggle();
    toggle(scope, featureName);
  };
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(preference_base_option, {
    onChange: onChange,
    isChecked: isChecked,
    ...remainingProps
  });
}
/* harmony default export */ const preference_toggle_control = (PreferenceToggleControl);

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/components/preferences-modal/index.js
/**
 * WordPress dependencies
 */



function PreferencesModal({
  closeModal,
  children
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Modal, {
    className: "preferences-modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Preferences'),
    onRequestClose: closeModal,
    children: children
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/components/preferences-modal-section/index.js


const Section = ({
  description,
  title,
  children
}) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("fieldset", {
  className: "preferences-modal__section",
  children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("legend", {
    className: "preferences-modal__section-legend",
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h2", {
      className: "preferences-modal__section-title",
      children: title
    }), description && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
      className: "preferences-modal__section-description",
      children: description
    })]
  }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
    className: "preferences-modal__section-content",
    children: children
  })]
});
/* harmony default export */ const preferences_modal_section = (Section);

;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */


/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps}                                 props icon is the SVG component to render
 *                                                          size is a number specifiying the icon size in pixels
 *                                                          Other props will be passed to wrapped SVG component
 * @param {import('react').ForwardedRef<HTMLElement>} ref   The forwarded ref to the SVG element.
 *
 * @return {JSX.Element}  Icon component
 */
function Icon({
  icon,
  size = 24,
  ...props
}, ref) {
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props,
    ref
  });
}
/* harmony default export */ const icon = ((0,external_wp_element_namespaceObject.forwardRef)(Icon));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-left.js
/**
 * WordPress dependencies
 */


const chevronLeft = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"
  })
});
/* harmony default export */ const chevron_left = (chevronLeft);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-right.js
/**
 * WordPress dependencies
 */


const chevronRight = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"
  })
});
/* harmony default export */ const chevron_right = (chevronRight);

;// CONCATENATED MODULE: external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.', '@wordpress/preferences');

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/components/preferences-modal-tabs/index.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



const {
  Tabs
} = unlock(external_wp_components_namespaceObject.privateApis);
const PREFERENCES_MENU = 'preferences-menu';
function PreferencesModalTabs({
  sections
}) {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');

  // This is also used to sync the two different rendered components
  // between small and large viewports.
  const [activeMenu, setActiveMenu] = (0,external_wp_element_namespaceObject.useState)(PREFERENCES_MENU);
  /**
   * Create helper objects from `sections` for easier data handling.
   * `tabs` is used for creating the `Tabs` and `sectionsContentMap`
   * is used for easier access to active tab's content.
   */
  const {
    tabs,
    sectionsContentMap
  } = (0,external_wp_element_namespaceObject.useMemo)(() => {
    let mappedTabs = {
      tabs: [],
      sectionsContentMap: {}
    };
    if (sections.length) {
      mappedTabs = sections.reduce((accumulator, {
        name,
        tabLabel: title,
        content
      }) => {
        accumulator.tabs.push({
          name,
          title
        });
        accumulator.sectionsContentMap[name] = content;
        return accumulator;
      }, {
        tabs: [],
        sectionsContentMap: {}
      });
    }
    return mappedTabs;
  }, [sections]);
  let modalContent;
  // We render different components based on the viewport size.
  if (isLargeViewport) {
    modalContent = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "preferences__tabs",
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(Tabs, {
        defaultTabId: activeMenu !== PREFERENCES_MENU ? activeMenu : undefined,
        onSelect: setActiveMenu,
        orientation: "vertical",
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Tabs.TabList, {
          className: "preferences__tabs-tablist",
          children: tabs.map(tab => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Tabs.Tab, {
            tabId: tab.name,
            className: "preferences__tabs-tab",
            children: tab.title
          }, tab.name))
        }), tabs.map(tab => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Tabs.TabPanel, {
          tabId: tab.name,
          className: "preferences__tabs-tabpanel",
          focusable: false,
          children: sectionsContentMap[tab.name] || null
        }, tab.name))]
      })
    });
  } else {
    modalContent = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalNavigatorProvider, {
      initialPath: "/",
      className: "preferences__provider",
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
        path: "/",
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Card, {
          isBorderless: true,
          size: "small",
          children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.CardBody, {
            children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalItemGroup, {
              children: tabs.map(tab => {
                return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalNavigatorButton, {
                  path: `/${tab.name}`,
                  as: external_wp_components_namespaceObject.__experimentalItem,
                  isAction: true,
                  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalHStack, {
                    justify: "space-between",
                    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.FlexItem, {
                      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalTruncate, {
                        children: tab.title
                      })
                    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.FlexItem, {
                      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(icon, {
                        icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_left : chevron_right
                      })
                    })]
                  })
                }, tab.name);
              })
            })
          })
        })
      }), sections.length && sections.map(section => {
        return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
          path: `/${section.name}`,
          children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.Card, {
            isBorderless: true,
            size: "large",
            children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.CardHeader, {
              isBorderless: false,
              justify: "left",
              size: "small",
              gap: "6",
              children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalNavigatorBackButton, {
                icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_right : chevron_left,
                label: (0,external_wp_i18n_namespaceObject.__)('Back')
              }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalText, {
                size: "16",
                children: section.tabLabel
              })]
            }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.CardBody, {
              children: section.content
            })]
          })
        }, `${section.name}-menu`);
      })]
    });
  }
  return modalContent;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/private-apis.js
/**
 * Internal dependencies
 */






const privateApis = {};
lock(privateApis, {
  PreferenceBaseOption: preference_base_option,
  PreferenceToggleControl: preference_toggle_control,
  PreferencesModal: PreferencesModal,
  PreferencesModalSection: preferences_modal_section,
  PreferencesModalTabs: PreferencesModalTabs
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/index.js




(window.wp = window.wp || {}).preferences = __webpack_exports__;
/******/ })()
;