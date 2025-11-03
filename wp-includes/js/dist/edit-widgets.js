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
  initialize: () => (/* binding */ initialize),
  initializeEditor: () => (/* binding */ initializeEditor),
  reinitializeEditor: () => (/* binding */ reinitializeEditor),
  store: () => (/* reexport */ store_store)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  closeModal: () => (closeModal),
  disableComplementaryArea: () => (disableComplementaryArea),
  enableComplementaryArea: () => (enableComplementaryArea),
  openModal: () => (openModal),
  pinItem: () => (pinItem),
  setDefaultComplementaryArea: () => (setDefaultComplementaryArea),
  setFeatureDefaults: () => (setFeatureDefaults),
  setFeatureValue: () => (setFeatureValue),
  toggleFeature: () => (toggleFeature),
  unpinItem: () => (unpinItem)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  getActiveComplementaryArea: () => (getActiveComplementaryArea),
  isComplementaryAreaLoading: () => (isComplementaryAreaLoading),
  isFeatureActive: () => (isFeatureActive),
  isItemPinned: () => (isItemPinned),
  isModalActive: () => (isModalActive)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-widgets/build-module/store/actions.js
var store_actions_namespaceObject = {};
__webpack_require__.r(store_actions_namespaceObject);
__webpack_require__.d(store_actions_namespaceObject, {
  closeGeneralSidebar: () => (closeGeneralSidebar),
  lockWidgetSaving: () => (lockWidgetSaving),
  moveBlockToWidgetArea: () => (moveBlockToWidgetArea),
  persistStubPost: () => (persistStubPost),
  saveEditedWidgetAreas: () => (saveEditedWidgetAreas),
  saveWidgetArea: () => (saveWidgetArea),
  saveWidgetAreas: () => (saveWidgetAreas),
  setIsInserterOpened: () => (setIsInserterOpened),
  setIsListViewOpened: () => (setIsListViewOpened),
  setIsWidgetAreaOpen: () => (setIsWidgetAreaOpen),
  setWidgetAreasOpenState: () => (setWidgetAreasOpenState),
  setWidgetIdForClientId: () => (setWidgetIdForClientId),
  unlockWidgetSaving: () => (unlockWidgetSaving)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-widgets/build-module/store/resolvers.js
var resolvers_namespaceObject = {};
__webpack_require__.r(resolvers_namespaceObject);
__webpack_require__.d(resolvers_namespaceObject, {
  getWidgetAreas: () => (getWidgetAreas),
  getWidgets: () => (getWidgets)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-widgets/build-module/store/selectors.js
var store_selectors_namespaceObject = {};
__webpack_require__.r(store_selectors_namespaceObject);
__webpack_require__.d(store_selectors_namespaceObject, {
  __experimentalGetInsertionPoint: () => (__experimentalGetInsertionPoint),
  canInsertBlockInWidgetArea: () => (canInsertBlockInWidgetArea),
  getEditedWidgetAreas: () => (getEditedWidgetAreas),
  getIsWidgetAreaOpen: () => (getIsWidgetAreaOpen),
  getParentWidgetAreaBlock: () => (getParentWidgetAreaBlock),
  getReferenceWidgetBlocks: () => (getReferenceWidgetBlocks),
  getWidget: () => (getWidget),
  getWidgetAreaForWidgetId: () => (getWidgetAreaForWidgetId),
  getWidgetAreas: () => (selectors_getWidgetAreas),
  getWidgets: () => (selectors_getWidgets),
  isInserterOpened: () => (isInserterOpened),
  isListViewOpened: () => (isListViewOpened),
  isSavingWidgetAreas: () => (isSavingWidgetAreas),
  isWidgetSavingLocked: () => (isWidgetSavingLocked)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-widgets/build-module/store/private-selectors.js
var private_selectors_namespaceObject = {};
__webpack_require__.r(private_selectors_namespaceObject);
__webpack_require__.d(private_selectors_namespaceObject, {
  getInserterSidebarToggleRef: () => (getInserterSidebarToggleRef),
  getListViewToggleRef: () => (getListViewToggleRef)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/index.js
var widget_area_namespaceObject = {};
__webpack_require__.r(widget_area_namespaceObject);
__webpack_require__.d(widget_area_namespaceObject, {
  metadata: () => (block_namespaceObject),
  name: () => (widget_area_name),
  settings: () => (settings)
});

;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","blockLibrary"]
const external_wp_blockLibrary_namespaceObject = window["wp"]["blockLibrary"];
;// external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// external ["wp","widgets"]
const external_wp_widgets_namespaceObject = window["wp"]["widgets"];
;// external ["wp","preferences"]
const external_wp_preferences_namespaceObject = window["wp"]["preferences"];
;// external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// ./node_modules/@wordpress/edit-widgets/build-module/store/reducer.js

function widgetAreasOpenState(state = {}, action) {
  const { type } = action;
  switch (type) {
    case "SET_WIDGET_AREAS_OPEN_STATE": {
      return action.widgetAreasOpenState;
    }
    case "SET_IS_WIDGET_AREA_OPEN": {
      const { clientId, isOpen } = action;
      return {
        ...state,
        [clientId]: isOpen
      };
    }
    default: {
      return state;
    }
  }
}
function blockInserterPanel(state = false, action) {
  switch (action.type) {
    case "SET_IS_LIST_VIEW_OPENED":
      return action.isOpen ? false : state;
    case "SET_IS_INSERTER_OPENED":
      return action.value;
  }
  return state;
}
function listViewPanel(state = false, action) {
  switch (action.type) {
    case "SET_IS_INSERTER_OPENED":
      return action.value ? false : state;
    case "SET_IS_LIST_VIEW_OPENED":
      return action.isOpen;
  }
  return state;
}
function listViewToggleRef(state = { current: null }) {
  return state;
}
function inserterSidebarToggleRef(state = { current: null }) {
  return state;
}
function widgetSavingLock(state = {}, action) {
  switch (action.type) {
    case "LOCK_WIDGET_SAVING":
      return { ...state, [action.lockName]: true };
    case "UNLOCK_WIDGET_SAVING": {
      const { [action.lockName]: removedLockName, ...restState } = state;
      return restState;
    }
  }
  return state;
}
var reducer_default = (0,external_wp_data_namespaceObject.combineReducers)({
  blockInserterPanel,
  inserterSidebarToggleRef,
  listViewPanel,
  listViewToggleRef,
  widgetAreasOpenState,
  widgetSavingLock
});


;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
;// ./node_modules/clsx/dist/clsx.mjs
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const dist_clsx = (clsx);
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// ./node_modules/@wordpress/icons/build-module/library/check.js


var check_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M16.5 7.5 10 13.9l-2.5-2.4-1 1 3.5 3.6 7.5-7.6z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/star-filled.js


var star_filled_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/star-empty.js


var star_empty_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
  external_wp_primitives_namespaceObject.Path,
  {
    fillRule: "evenodd",
    d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
    clipRule: "evenodd"
  }
) });


;// external ["wp","viewport"]
const external_wp_viewport_namespaceObject = window["wp"]["viewport"];
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","plugins"]
const external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// ./node_modules/@wordpress/icons/build-module/library/close-small.js


var close_small_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z" }) });


;// ./node_modules/@wordpress/interface/build-module/store/deprecated.js

function normalizeComplementaryAreaScope(scope) {
  if (["core/edit-post", "core/edit-site"].includes(scope)) {
    external_wp_deprecated_default()(`${scope} interface scope`, {
      alternative: "core interface scope",
      hint: "core/edit-post and core/edit-site are merging.",
      version: "6.6"
    });
    return "core";
  }
  return scope;
}
function normalizeComplementaryAreaName(scope, name) {
  if (scope === "core" && name === "edit-site/template") {
    external_wp_deprecated_default()(`edit-site/template sidebar`, {
      alternative: "edit-post/document",
      version: "6.6"
    });
    return "edit-post/document";
  }
  if (scope === "core" && name === "edit-site/block-inspector") {
    external_wp_deprecated_default()(`edit-site/block-inspector sidebar`, {
      alternative: "edit-post/block",
      version: "6.6"
    });
    return "edit-post/block";
  }
  return name;
}


;// ./node_modules/@wordpress/interface/build-module/store/actions.js



const setDefaultComplementaryArea = (scope, area) => {
  scope = normalizeComplementaryAreaScope(scope);
  area = normalizeComplementaryAreaName(scope, area);
  return {
    type: "SET_DEFAULT_COMPLEMENTARY_AREA",
    scope,
    area
  };
};
const enableComplementaryArea = (scope, area) => ({ registry, dispatch }) => {
  if (!area) {
    return;
  }
  scope = normalizeComplementaryAreaScope(scope);
  area = normalizeComplementaryAreaName(scope, area);
  const isComplementaryAreaVisible = registry.select(external_wp_preferences_namespaceObject.store).get(scope, "isComplementaryAreaVisible");
  if (!isComplementaryAreaVisible) {
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, "isComplementaryAreaVisible", true);
  }
  dispatch({
    type: "ENABLE_COMPLEMENTARY_AREA",
    scope,
    area
  });
};
const disableComplementaryArea = (scope) => ({ registry }) => {
  scope = normalizeComplementaryAreaScope(scope);
  const isComplementaryAreaVisible = registry.select(external_wp_preferences_namespaceObject.store).get(scope, "isComplementaryAreaVisible");
  if (isComplementaryAreaVisible) {
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, "isComplementaryAreaVisible", false);
  }
};
const pinItem = (scope, item) => ({ registry }) => {
  if (!item) {
    return;
  }
  scope = normalizeComplementaryAreaScope(scope);
  item = normalizeComplementaryAreaName(scope, item);
  const pinnedItems = registry.select(external_wp_preferences_namespaceObject.store).get(scope, "pinnedItems");
  if (pinnedItems?.[item] === true) {
    return;
  }
  registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, "pinnedItems", {
    ...pinnedItems,
    [item]: true
  });
};
const unpinItem = (scope, item) => ({ registry }) => {
  if (!item) {
    return;
  }
  scope = normalizeComplementaryAreaScope(scope);
  item = normalizeComplementaryAreaName(scope, item);
  const pinnedItems = registry.select(external_wp_preferences_namespaceObject.store).get(scope, "pinnedItems");
  registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, "pinnedItems", {
    ...pinnedItems,
    [item]: false
  });
};
function toggleFeature(scope, featureName) {
  return function({ registry }) {
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).toggleFeature`, {
      since: "6.0",
      alternative: `dispatch( 'core/preferences' ).toggle`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).toggle(scope, featureName);
  };
}
function setFeatureValue(scope, featureName, value) {
  return function({ registry }) {
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).setFeatureValue`, {
      since: "6.0",
      alternative: `dispatch( 'core/preferences' ).set`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, featureName, !!value);
  };
}
function setFeatureDefaults(scope, defaults) {
  return function({ registry }) {
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).setFeatureDefaults`, {
      since: "6.0",
      alternative: `dispatch( 'core/preferences' ).setDefaults`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).setDefaults(scope, defaults);
  };
}
function openModal(name) {
  return {
    type: "OPEN_MODAL",
    name
  };
}
function closeModal() {
  return {
    type: "CLOSE_MODAL"
  };
}


;// ./node_modules/@wordpress/interface/build-module/store/selectors.js




const getActiveComplementaryArea = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, scope) => {
    scope = normalizeComplementaryAreaScope(scope);
    const isComplementaryAreaVisible = select(external_wp_preferences_namespaceObject.store).get(
      scope,
      "isComplementaryAreaVisible"
    );
    if (isComplementaryAreaVisible === void 0) {
      return void 0;
    }
    if (isComplementaryAreaVisible === false) {
      return null;
    }
    return state?.complementaryAreas?.[scope];
  }
);
const isComplementaryAreaLoading = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, scope) => {
    scope = normalizeComplementaryAreaScope(scope);
    const isVisible = select(external_wp_preferences_namespaceObject.store).get(
      scope,
      "isComplementaryAreaVisible"
    );
    const identifier = state?.complementaryAreas?.[scope];
    return isVisible && identifier === void 0;
  }
);
const isItemPinned = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, scope, item) => {
    scope = normalizeComplementaryAreaScope(scope);
    item = normalizeComplementaryAreaName(scope, item);
    const pinnedItems = select(external_wp_preferences_namespaceObject.store).get(
      scope,
      "pinnedItems"
    );
    return pinnedItems?.[item] ?? true;
  }
);
const isFeatureActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, scope, featureName) => {
    external_wp_deprecated_default()(
      `select( 'core/interface' ).isFeatureActive( scope, featureName )`,
      {
        since: "6.0",
        alternative: `select( 'core/preferences' ).get( scope, featureName )`
      }
    );
    return !!select(external_wp_preferences_namespaceObject.store).get(scope, featureName);
  }
);
function isModalActive(state, modalName) {
  return state.activeModal === modalName;
}


;// ./node_modules/@wordpress/interface/build-module/store/reducer.js

function complementaryAreas(state = {}, action) {
  switch (action.type) {
    case "SET_DEFAULT_COMPLEMENTARY_AREA": {
      const { scope, area } = action;
      if (state[scope]) {
        return state;
      }
      return {
        ...state,
        [scope]: area
      };
    }
    case "ENABLE_COMPLEMENTARY_AREA": {
      const { scope, area } = action;
      return {
        ...state,
        [scope]: area
      };
    }
  }
  return state;
}
function activeModal(state = null, action) {
  switch (action.type) {
    case "OPEN_MODAL":
      return action.name;
    case "CLOSE_MODAL":
      return null;
  }
  return state;
}
var reducer_reducer_default = (0,external_wp_data_namespaceObject.combineReducers)({
  complementaryAreas,
  activeModal
});


;// ./node_modules/@wordpress/interface/build-module/store/constants.js
const STORE_NAME = "core/interface";


;// ./node_modules/@wordpress/interface/build-module/store/index.js





const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer_reducer_default,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);


;// ./node_modules/@wordpress/interface/build-module/components/complementary-area-toggle/index.js





function roleSupportsCheckedState(role) {
  return [
    "checkbox",
    "option",
    "radio",
    "switch",
    "menuitemcheckbox",
    "menuitemradio",
    "treeitem"
  ].includes(role);
}
function ComplementaryAreaToggle({
  as = external_wp_components_namespaceObject.Button,
  scope,
  identifier: identifierProp,
  icon: iconProp,
  selectedIcon,
  name,
  shortcut,
  ...props
}) {
  const ComponentToUse = as;
  const context = (0,external_wp_plugins_namespaceObject.usePluginContext)();
  const icon = iconProp || context.icon;
  const identifier = identifierProp || `${context.name}/${name}`;
  const isSelected = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(store).getActiveComplementaryArea(scope) === identifier,
    [identifier, scope]
  );
  const { enableComplementaryArea, disableComplementaryArea } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    ComponentToUse,
    {
      icon: selectedIcon && isSelected ? selectedIcon : icon,
      "aria-controls": identifier.replace("/", ":"),
      "aria-checked": roleSupportsCheckedState(props.role) ? isSelected : void 0,
      onClick: () => {
        if (isSelected) {
          disableComplementaryArea(scope);
        } else {
          enableComplementaryArea(scope, identifier);
        }
      },
      shortcut,
      ...props
    }
  );
}


;// ./node_modules/@wordpress/interface/build-module/components/complementary-area-header/index.js




const ComplementaryAreaHeader = ({
  children,
  className,
  toggleButtonProps
}) => {
  const toggleButton = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(ComplementaryAreaToggle, { icon: close_small_default, ...toggleButtonProps });
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
    "div",
    {
      className: dist_clsx(
        "components-panel__header",
        "interface-complementary-area-header",
        className
      ),
      tabIndex: -1,
      children: [
        children,
        toggleButton
      ]
    }
  );
};
var complementary_area_header_default = ComplementaryAreaHeader;


;// ./node_modules/@wordpress/interface/build-module/components/action-item/index.js



const noop = () => {
};
function ActionItemSlot({
  name,
  as: Component = external_wp_components_namespaceObject.MenuGroup,
  fillProps = {},
  bubblesVirtually,
  ...props
}) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Slot,
    {
      name,
      bubblesVirtually,
      fillProps,
      children: (fills) => {
        if (!external_wp_element_namespaceObject.Children.toArray(fills).length) {
          return null;
        }
        const initializedByPlugins = [];
        external_wp_element_namespaceObject.Children.forEach(
          fills,
          ({
            props: { __unstableExplicitMenuItem, __unstableTarget }
          }) => {
            if (__unstableTarget && __unstableExplicitMenuItem) {
              initializedByPlugins.push(__unstableTarget);
            }
          }
        );
        const children = external_wp_element_namespaceObject.Children.map(fills, (child) => {
          if (!child.props.__unstableExplicitMenuItem && initializedByPlugins.includes(
            child.props.__unstableTarget
          )) {
            return null;
          }
          return child;
        });
        return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Component, { ...props, children });
      }
    }
  );
}
function ActionItem({ name, as: Component = external_wp_components_namespaceObject.Button, onClick, ...props }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Fill, { name, children: ({ onClick: fpOnClick }) => {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      Component,
      {
        onClick: onClick || fpOnClick ? (...args) => {
          (onClick || noop)(...args);
          (fpOnClick || noop)(...args);
        } : void 0,
        ...props
      }
    );
  } });
}
ActionItem.Slot = ActionItemSlot;
var action_item_default = ActionItem;


;// ./node_modules/@wordpress/interface/build-module/components/complementary-area-more-menu-item/index.js





const PluginsMenuItem = ({
  // Menu item is marked with unstable prop for backward compatibility.
  // They are removed so they don't leak to DOM elements.
  // @see https://github.com/WordPress/gutenberg/issues/14457
  __unstableExplicitMenuItem,
  __unstableTarget,
  ...restProps
}) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, { ...restProps });
function ComplementaryAreaMoreMenuItem({
  scope,
  target,
  __unstableExplicitMenuItem,
  ...props
}) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    ComplementaryAreaToggle,
    {
      as: (toggleProps) => {
        return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          action_item_default,
          {
            __unstableExplicitMenuItem,
            __unstableTarget: `${scope}/${target}`,
            as: PluginsMenuItem,
            name: `${scope}/plugin-more-menu`,
            ...toggleProps
          }
        );
      },
      role: "menuitemcheckbox",
      selectedIcon: check_default,
      name: target,
      scope,
      ...props
    }
  );
}


;// ./node_modules/@wordpress/interface/build-module/components/pinned-items/index.js



function PinnedItems({ scope, ...props }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Fill, { name: `PinnedItems/${scope}`, ...props });
}
function PinnedItemsSlot({ scope, className, ...props }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Slot, { name: `PinnedItems/${scope}`, ...props, children: (fills) => fills?.length > 0 && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    "div",
    {
      className: dist_clsx(
        className,
        "interface-pinned-items"
      ),
      children: fills
    }
  ) });
}
PinnedItems.Slot = PinnedItemsSlot;
var pinned_items_default = PinnedItems;


;// ./node_modules/@wordpress/interface/build-module/components/complementary-area/index.js
















const ANIMATION_DURATION = 0.3;
function ComplementaryAreaSlot({ scope, ...props }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Slot, { name: `ComplementaryArea/${scope}`, ...props });
}
const SIDEBAR_WIDTH = 280;
const variants = {
  open: { width: SIDEBAR_WIDTH },
  closed: { width: 0 },
  mobileOpen: { width: "100vw" }
};
function ComplementaryAreaFill({
  activeArea,
  isActive,
  scope,
  children,
  className,
  id
}) {
  const disableMotion = (0,external_wp_compose_namespaceObject.useReducedMotion)();
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium", "<");
  const previousActiveArea = (0,external_wp_compose_namespaceObject.usePrevious)(activeArea);
  const previousIsActive = (0,external_wp_compose_namespaceObject.usePrevious)(isActive);
  const [, setState] = (0,external_wp_element_namespaceObject.useState)({});
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    setState({});
  }, [isActive]);
  const transition = {
    type: "tween",
    duration: disableMotion || isMobileViewport || !!previousActiveArea && !!activeArea && activeArea !== previousActiveArea ? 0 : ANIMATION_DURATION,
    ease: [0.6, 0, 0.4, 1]
  };
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Fill, { name: `ComplementaryArea/${scope}`, children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__unstableAnimatePresence, { initial: false, children: (previousIsActive || isActive) && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.__unstableMotion.div,
    {
      variants,
      initial: "closed",
      animate: isMobileViewport ? "mobileOpen" : "open",
      exit: "closed",
      transition,
      className: "interface-complementary-area__fill",
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        "div",
        {
          id,
          className,
          style: {
            width: isMobileViewport ? "100vw" : SIDEBAR_WIDTH
          },
          children
        }
      )
    }
  ) }) });
}
function useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall) {
  const previousIsSmallRef = (0,external_wp_element_namespaceObject.useRef)(false);
  const shouldOpenWhenNotSmallRef = (0,external_wp_element_namespaceObject.useRef)(false);
  const { enableComplementaryArea, disableComplementaryArea } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isActive && isSmall && !previousIsSmallRef.current) {
      disableComplementaryArea(scope);
      shouldOpenWhenNotSmallRef.current = true;
    } else if (
      // If there is a flag indicating the complementary area should be
      // enabled when we go from small to big window size and we are going
      // from a small to big window size.
      shouldOpenWhenNotSmallRef.current && !isSmall && previousIsSmallRef.current
    ) {
      shouldOpenWhenNotSmallRef.current = false;
      enableComplementaryArea(scope, identifier);
    } else if (
      // If the flag is indicating the current complementary should be
      // reopened but another complementary area becomes active, remove
      // the flag.
      shouldOpenWhenNotSmallRef.current && activeArea && activeArea !== identifier
    ) {
      shouldOpenWhenNotSmallRef.current = false;
    }
    if (isSmall !== previousIsSmallRef.current) {
      previousIsSmallRef.current = isSmall;
    }
  }, [
    isActive,
    isSmall,
    scope,
    identifier,
    activeArea,
    disableComplementaryArea,
    enableComplementaryArea
  ]);
}
function ComplementaryArea({
  children,
  className,
  closeLabel = (0,external_wp_i18n_namespaceObject.__)("Close plugin"),
  identifier: identifierProp,
  header,
  headerClassName,
  icon: iconProp,
  isPinnable = true,
  panelClassName,
  scope,
  name,
  title,
  toggleShortcut,
  isActiveByDefault
}) {
  const context = (0,external_wp_plugins_namespaceObject.usePluginContext)();
  const icon = iconProp || context.icon;
  const identifier = identifierProp || `${context.name}/${name}`;
  const [isReady, setIsReady] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    isLoading,
    isActive,
    isPinned,
    activeArea,
    isSmall,
    isLarge,
    showIconLabels
  } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const {
        getActiveComplementaryArea,
        isComplementaryAreaLoading,
        isItemPinned
      } = select(store);
      const { get } = select(external_wp_preferences_namespaceObject.store);
      const _activeArea = getActiveComplementaryArea(scope);
      return {
        isLoading: isComplementaryAreaLoading(scope),
        isActive: _activeArea === identifier,
        isPinned: isItemPinned(scope, identifier),
        activeArea: _activeArea,
        isSmall: select(external_wp_viewport_namespaceObject.store).isViewportMatch("< medium"),
        isLarge: select(external_wp_viewport_namespaceObject.store).isViewportMatch("large"),
        showIconLabels: get("core", "showIconLabels")
      };
    },
    [identifier, scope]
  );
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium", "<");
  useAdjustComplementaryListener(
    scope,
    identifier,
    activeArea,
    isActive,
    isSmall
  );
  const {
    enableComplementaryArea,
    disableComplementaryArea,
    pinItem,
    unpinItem
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isActiveByDefault && activeArea === void 0 && !isSmall) {
      enableComplementaryArea(scope, identifier);
    } else if (activeArea === void 0 && isSmall) {
      disableComplementaryArea(scope, identifier);
    }
    setIsReady(true);
  }, [
    activeArea,
    isActiveByDefault,
    scope,
    identifier,
    isSmall,
    enableComplementaryArea,
    disableComplementaryArea
  ]);
  if (!isReady) {
    return;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    isPinnable && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(pinned_items_default, { scope, children: isPinned && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      ComplementaryAreaToggle,
      {
        scope,
        identifier,
        isPressed: isActive && (!showIconLabels || isLarge),
        "aria-expanded": isActive,
        "aria-disabled": isLoading,
        label: title,
        icon: showIconLabels ? check_default : icon,
        showTooltip: !showIconLabels,
        variant: showIconLabels ? "tertiary" : void 0,
        size: "compact",
        shortcut: toggleShortcut
      }
    ) }),
    name && isPinnable && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      ComplementaryAreaMoreMenuItem,
      {
        target: name,
        scope,
        icon,
        identifier,
        children: title
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
      ComplementaryAreaFill,
      {
        activeArea,
        isActive,
        className: dist_clsx("interface-complementary-area", className),
        scope,
        id: identifier.replace("/", ":"),
        children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            complementary_area_header_default,
            {
              className: headerClassName,
              closeLabel,
              onClose: () => disableComplementaryArea(scope),
              toggleButtonProps: {
                label: closeLabel,
                size: "compact",
                shortcut: toggleShortcut,
                scope,
                identifier
              },
              children: header || /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
                /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h2", { className: "interface-complementary-area-header__title", children: title }),
                isPinnable && !isMobileViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                  external_wp_components_namespaceObject.Button,
                  {
                    className: "interface-complementary-area__pin-unpin-item",
                    icon: isPinned ? star_filled_default : star_empty_default,
                    label: isPinned ? (0,external_wp_i18n_namespaceObject.__)("Unpin from toolbar") : (0,external_wp_i18n_namespaceObject.__)("Pin to toolbar"),
                    onClick: () => (isPinned ? unpinItem : pinItem)(
                      scope,
                      identifier
                    ),
                    isPressed: isPinned,
                    "aria-expanded": isPinned,
                    size: "compact"
                  }
                )
              ] })
            }
          ),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Panel, { className: panelClassName, children })
        ]
      }
    )
  ] });
}
ComplementaryArea.Slot = ComplementaryAreaSlot;
var complementary_area_default = ComplementaryArea;


;// ./node_modules/@wordpress/admin-ui/build-module/navigable-region/index.js



const NavigableRegion = (0,external_wp_element_namespaceObject.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      Tag,
      {
        ref,
        className: dist_clsx("admin-ui-navigable-region", className),
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


;// ./node_modules/@wordpress/interface/build-module/components/interface-skeleton/index.js







const interface_skeleton_ANIMATION_DURATION = 0.25;
const commonTransition = {
  type: "tween",
  duration: interface_skeleton_ANIMATION_DURATION,
  ease: [0.6, 0, 0.4, 1]
};
function useHTMLClass(className) {
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const element = document && document.querySelector(`html:not(.${className})`);
    if (!element) {
      return;
    }
    element.classList.toggle(className);
    return () => {
      element.classList.toggle(className);
    };
  }, [className]);
}
const headerVariants = {
  hidden: { opacity: 1, marginTop: -60 },
  visible: { opacity: 1, marginTop: 0 },
  distractionFreeHover: {
    opacity: 1,
    marginTop: 0,
    transition: {
      ...commonTransition,
      delay: 0.2,
      delayChildren: 0.2
    }
  },
  distractionFreeHidden: {
    opacity: 0,
    marginTop: -60
  },
  distractionFreeDisabled: {
    opacity: 0,
    marginTop: 0,
    transition: {
      ...commonTransition,
      delay: 0.8,
      delayChildren: 0.8
    }
  }
};
function InterfaceSkeleton({
  isDistractionFree,
  footer,
  header,
  editorNotices,
  sidebar,
  secondarySidebar,
  content,
  actions,
  labels,
  className
}, ref) {
  const [secondarySidebarResizeListener, secondarySidebarSize] = (0,external_wp_compose_namespaceObject.useResizeObserver)();
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium", "<");
  const disableMotion = (0,external_wp_compose_namespaceObject.useReducedMotion)();
  const defaultTransition = {
    type: "tween",
    duration: disableMotion ? 0 : interface_skeleton_ANIMATION_DURATION,
    ease: [0.6, 0, 0.4, 1]
  };
  useHTMLClass("interface-interface-skeleton__html-container");
  const defaultLabels = {
    /* translators: accessibility text for the top bar landmark region. */
    header: (0,external_wp_i18n_namespaceObject._x)("Header", "header landmark area"),
    /* translators: accessibility text for the content landmark region. */
    body: (0,external_wp_i18n_namespaceObject.__)("Content"),
    /* translators: accessibility text for the secondary sidebar landmark region. */
    secondarySidebar: (0,external_wp_i18n_namespaceObject.__)("Block Library"),
    /* translators: accessibility text for the settings landmark region. */
    sidebar: (0,external_wp_i18n_namespaceObject._x)("Settings", "settings landmark area"),
    /* translators: accessibility text for the publish landmark region. */
    actions: (0,external_wp_i18n_namespaceObject.__)("Publish"),
    /* translators: accessibility text for the footer landmark region. */
    footer: (0,external_wp_i18n_namespaceObject.__)("Footer")
  };
  const mergedLabels = { ...defaultLabels, ...labels };
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
    "div",
    {
      ref,
      className: dist_clsx(
        className,
        "interface-interface-skeleton",
        !!footer && "has-footer"
      ),
      children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "interface-interface-skeleton__editor", children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__unstableAnimatePresence, { initial: false, children: !!header && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            navigable_region_default,
            {
              as: external_wp_components_namespaceObject.__unstableMotion.div,
              className: "interface-interface-skeleton__header",
              "aria-label": mergedLabels.header,
              initial: isDistractionFree && !isMobileViewport ? "distractionFreeHidden" : "hidden",
              whileHover: isDistractionFree && !isMobileViewport ? "distractionFreeHover" : "visible",
              animate: isDistractionFree && !isMobileViewport ? "distractionFreeDisabled" : "visible",
              exit: isDistractionFree && !isMobileViewport ? "distractionFreeHidden" : "hidden",
              variants: headerVariants,
              transition: defaultTransition,
              children: header
            }
          ) }),
          isDistractionFree && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "interface-interface-skeleton__header", children: editorNotices }),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "interface-interface-skeleton__body", children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__unstableAnimatePresence, { initial: false, children: !!secondarySidebar && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              navigable_region_default,
              {
                className: "interface-interface-skeleton__secondary-sidebar",
                ariaLabel: mergedLabels.secondarySidebar,
                as: external_wp_components_namespaceObject.__unstableMotion.div,
                initial: "closed",
                animate: "open",
                exit: "closed",
                variants: {
                  open: { width: secondarySidebarSize.width },
                  closed: { width: 0 }
                },
                transition: defaultTransition,
                children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
                  external_wp_components_namespaceObject.__unstableMotion.div,
                  {
                    style: {
                      position: "absolute",
                      width: isMobileViewport ? "100vw" : "fit-content",
                      height: "100%",
                      left: 0
                    },
                    variants: {
                      open: { x: 0 },
                      closed: { x: "-100%" }
                    },
                    transition: defaultTransition,
                    children: [
                      secondarySidebarResizeListener,
                      secondarySidebar
                    ]
                  }
                )
              }
            ) }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              navigable_region_default,
              {
                className: "interface-interface-skeleton__content",
                ariaLabel: mergedLabels.body,
                children: content
              }
            ),
            !!sidebar && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              navigable_region_default,
              {
                className: "interface-interface-skeleton__sidebar",
                ariaLabel: mergedLabels.sidebar,
                children: sidebar
              }
            ),
            !!actions && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              navigable_region_default,
              {
                className: "interface-interface-skeleton__actions",
                ariaLabel: mergedLabels.actions,
                children: actions
              }
            )
          ] })
        ] }),
        !!footer && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          navigable_region_default,
          {
            className: "interface-interface-skeleton__footer",
            ariaLabel: mergedLabels.footer,
            children: footer
          }
        )
      ]
    }
  );
}
var interface_skeleton_default = (0,external_wp_element_namespaceObject.forwardRef)(InterfaceSkeleton);


;// ./node_modules/@wordpress/interface/build-module/components/index.js








;// ./node_modules/@wordpress/interface/build-module/index.js




;// external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// ./node_modules/@wordpress/edit-widgets/build-module/store/transformers.js


function transformWidgetToBlock(widget) {
  if (widget.id_base === "block") {
    const parsedBlocks = (0,external_wp_blocks_namespaceObject.parse)(widget.instance.raw.content, {
      __unstableSkipAutop: true
    });
    if (!parsedBlocks.length) {
      return (0,external_wp_widgets_namespaceObject.addWidgetIdToBlock)(
        (0,external_wp_blocks_namespaceObject.createBlock)("core/paragraph", {}, []),
        widget.id
      );
    }
    return (0,external_wp_widgets_namespaceObject.addWidgetIdToBlock)(parsedBlocks[0], widget.id);
  }
  let attributes;
  if (widget._embedded.about[0].is_multi) {
    attributes = {
      idBase: widget.id_base,
      instance: widget.instance
    };
  } else {
    attributes = {
      id: widget.id
    };
  }
  return (0,external_wp_widgets_namespaceObject.addWidgetIdToBlock)(
    (0,external_wp_blocks_namespaceObject.createBlock)("core/legacy-widget", attributes, []),
    widget.id
  );
}
function transformBlockToWidget(block, relatedWidget = {}) {
  let widget;
  const isValidLegacyWidgetBlock = block.name === "core/legacy-widget" && (block.attributes.id || block.attributes.instance);
  if (isValidLegacyWidgetBlock) {
    widget = {
      ...relatedWidget,
      id: block.attributes.id ?? relatedWidget.id,
      id_base: block.attributes.idBase ?? relatedWidget.id_base,
      instance: block.attributes.instance ?? relatedWidget.instance
    };
  } else {
    widget = {
      ...relatedWidget,
      id_base: "block",
      instance: {
        raw: {
          content: (0,external_wp_blocks_namespaceObject.serialize)(block)
        }
      }
    };
  }
  delete widget.rendered;
  delete widget.rendered_form;
  return widget;
}


;// ./node_modules/@wordpress/edit-widgets/build-module/store/utils.js
const KIND = "root";
const WIDGET_AREA_ENTITY_TYPE = "sidebar";
const POST_TYPE = "postType";
const buildWidgetAreaPostId = (widgetAreaId) => `widget-area-${widgetAreaId}`;
const buildWidgetAreasPostId = () => `widget-areas`;
function buildWidgetAreasQuery() {
  return {
    per_page: -1
  };
}
function buildWidgetsQuery() {
  return {
    per_page: -1,
    _embed: "about"
  };
}
const createStubPost = (id, blocks) => ({
  id,
  slug: id,
  status: "draft",
  type: "page",
  blocks,
  meta: {
    widgetAreaId: id
  }
});


;// ./node_modules/@wordpress/edit-widgets/build-module/store/constants.js
const constants_STORE_NAME = "core/edit-widgets";


;// ./node_modules/@wordpress/edit-widgets/build-module/store/actions.js









const persistStubPost = (id, blocks) => ({ registry }) => {
  const stubPost = createStubPost(id, blocks);
  registry.dispatch(external_wp_coreData_namespaceObject.store).receiveEntityRecords(
    KIND,
    POST_TYPE,
    stubPost,
    { id: stubPost.id },
    false
  );
  return stubPost;
};
const saveEditedWidgetAreas = () => async ({ select, dispatch, registry }) => {
  const editedWidgetAreas = select.getEditedWidgetAreas();
  if (!editedWidgetAreas?.length) {
    return;
  }
  try {
    await dispatch.saveWidgetAreas(editedWidgetAreas);
    registry.dispatch(external_wp_notices_namespaceObject.store).createSuccessNotice((0,external_wp_i18n_namespaceObject.__)("Widgets saved."), {
      type: "snackbar"
    });
  } catch (e) {
    registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(
      /* translators: %s: The error message. */
      (0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)("There was an error. %s"), e.message),
      {
        type: "snackbar"
      }
    );
  }
};
const saveWidgetAreas = (widgetAreas) => async ({ dispatch, registry }) => {
  try {
    for (const widgetArea of widgetAreas) {
      await dispatch.saveWidgetArea(widgetArea.id);
    }
  } finally {
    await registry.dispatch(external_wp_coreData_namespaceObject.store).finishResolution(
      "getEntityRecord",
      KIND,
      WIDGET_AREA_ENTITY_TYPE,
      buildWidgetAreasQuery()
    );
  }
};
const saveWidgetArea = (widgetAreaId) => async ({ dispatch, select, registry }) => {
  const widgets = select.getWidgets();
  const post = registry.select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(
    KIND,
    POST_TYPE,
    buildWidgetAreaPostId(widgetAreaId)
  );
  const areaWidgets = Object.values(widgets).filter(
    ({ sidebar }) => sidebar === widgetAreaId
  );
  const usedReferenceWidgets = [];
  const widgetsBlocks = post.blocks.filter((block) => {
    const { id } = block.attributes;
    if (block.name === "core/legacy-widget" && id) {
      if (usedReferenceWidgets.includes(id)) {
        return false;
      }
      usedReferenceWidgets.push(id);
    }
    return true;
  });
  const deletedWidgets = [];
  for (const widget of areaWidgets) {
    const widgetsNewArea = select.getWidgetAreaForWidgetId(widget.id);
    if (!widgetsNewArea) {
      deletedWidgets.push(widget);
    }
  }
  const batchMeta = [];
  const batchTasks = [];
  const sidebarWidgetsIds = [];
  for (let i = 0; i < widgetsBlocks.length; i++) {
    const block = widgetsBlocks[i];
    const widgetId = (0,external_wp_widgets_namespaceObject.getWidgetIdFromBlock)(block);
    const oldWidget = widgets[widgetId];
    const widget = transformBlockToWidget(block, oldWidget);
    sidebarWidgetsIds.push(widgetId);
    if (oldWidget) {
      registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord(
        "root",
        "widget",
        widgetId,
        {
          ...widget,
          sidebar: widgetAreaId
        },
        { undoIgnore: true }
      );
      const hasEdits = registry.select(external_wp_coreData_namespaceObject.store).hasEditsForEntityRecord("root", "widget", widgetId);
      if (!hasEdits) {
        continue;
      }
      batchTasks.push(
        ({ saveEditedEntityRecord }) => saveEditedEntityRecord("root", "widget", widgetId)
      );
    } else {
      batchTasks.push(
        ({ saveEntityRecord }) => saveEntityRecord("root", "widget", {
          ...widget,
          sidebar: widgetAreaId
        })
      );
    }
    batchMeta.push({
      block,
      position: i,
      clientId: block.clientId
    });
  }
  for (const widget of deletedWidgets) {
    batchTasks.push(
      ({ deleteEntityRecord }) => deleteEntityRecord("root", "widget", widget.id, {
        force: true
      })
    );
  }
  const records = await registry.dispatch(external_wp_coreData_namespaceObject.store).__experimentalBatch(batchTasks);
  const preservedRecords = records.filter(
    (record) => !record.hasOwnProperty("deleted")
  );
  const failedWidgetNames = [];
  for (let i = 0; i < preservedRecords.length; i++) {
    const widget = preservedRecords[i];
    const { block, position } = batchMeta[i];
    post.blocks[position].attributes.__internalWidgetId = widget.id;
    const error = registry.select(external_wp_coreData_namespaceObject.store).getLastEntitySaveError("root", "widget", widget.id);
    if (error) {
      failedWidgetNames.push(block.attributes?.name || block?.name);
    }
    if (!sidebarWidgetsIds[position]) {
      sidebarWidgetsIds[position] = widget.id;
    }
  }
  if (failedWidgetNames.length) {
    throw new Error(
      (0,external_wp_i18n_namespaceObject.sprintf)(
        /* translators: %s: List of widget names */
        (0,external_wp_i18n_namespaceObject.__)("Could not save the following widgets: %s."),
        failedWidgetNames.join(", ")
      )
    );
  }
  registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord(
    KIND,
    WIDGET_AREA_ENTITY_TYPE,
    widgetAreaId,
    {
      widgets: sidebarWidgetsIds
    },
    { undoIgnore: true }
  );
  dispatch(trySaveWidgetArea(widgetAreaId));
  registry.dispatch(external_wp_coreData_namespaceObject.store).receiveEntityRecords(KIND, POST_TYPE, post, void 0);
};
const trySaveWidgetArea = (widgetAreaId) => ({ registry }) => {
  registry.dispatch(external_wp_coreData_namespaceObject.store).saveEditedEntityRecord(
    KIND,
    WIDGET_AREA_ENTITY_TYPE,
    widgetAreaId,
    {
      throwOnError: true
    }
  );
};
function setWidgetIdForClientId(clientId, widgetId) {
  return {
    type: "SET_WIDGET_ID_FOR_CLIENT_ID",
    clientId,
    widgetId
  };
}
function setWidgetAreasOpenState(widgetAreasOpenState) {
  return {
    type: "SET_WIDGET_AREAS_OPEN_STATE",
    widgetAreasOpenState
  };
}
function setIsWidgetAreaOpen(clientId, isOpen) {
  return {
    type: "SET_IS_WIDGET_AREA_OPEN",
    clientId,
    isOpen
  };
}
function setIsInserterOpened(value) {
  return {
    type: "SET_IS_INSERTER_OPENED",
    value
  };
}
function setIsListViewOpened(isOpen) {
  return {
    type: "SET_IS_LIST_VIEW_OPENED",
    isOpen
  };
}
const closeGeneralSidebar = () => ({ registry }) => {
  registry.dispatch(store).disableComplementaryArea(constants_STORE_NAME);
};
const moveBlockToWidgetArea = (clientId, widgetAreaId) => async ({ dispatch, select, registry }) => {
  const sourceRootClientId = registry.select(external_wp_blockEditor_namespaceObject.store).getBlockRootClientId(clientId);
  const widgetAreas = registry.select(external_wp_blockEditor_namespaceObject.store).getBlocks();
  const destinationWidgetAreaBlock = widgetAreas.find(
    ({ attributes }) => attributes.id === widgetAreaId
  );
  const destinationRootClientId = destinationWidgetAreaBlock.clientId;
  const destinationInnerBlocksClientIds = registry.select(external_wp_blockEditor_namespaceObject.store).getBlockOrder(destinationRootClientId);
  const destinationIndex = destinationInnerBlocksClientIds.length;
  const isDestinationWidgetAreaOpen = select.getIsWidgetAreaOpen(
    destinationRootClientId
  );
  if (!isDestinationWidgetAreaOpen) {
    dispatch.setIsWidgetAreaOpen(destinationRootClientId, true);
  }
  registry.dispatch(external_wp_blockEditor_namespaceObject.store).moveBlocksToPosition(
    [clientId],
    sourceRootClientId,
    destinationRootClientId,
    destinationIndex
  );
};
function unlockWidgetSaving(lockName) {
  return {
    type: "UNLOCK_WIDGET_SAVING",
    lockName
  };
}
function lockWidgetSaving(lockName) {
  return {
    type: "LOCK_WIDGET_SAVING",
    lockName
  };
}


;// ./node_modules/@wordpress/edit-widgets/build-module/store/resolvers.js





const getWidgetAreas = () => async ({ dispatch, registry }) => {
  const query = buildWidgetAreasQuery();
  const widgetAreas = await registry.resolveSelect(external_wp_coreData_namespaceObject.store).getEntityRecords(KIND, WIDGET_AREA_ENTITY_TYPE, query);
  const widgetAreaBlocks = [];
  const sortedWidgetAreas = widgetAreas.sort((a, b) => {
    if (a.id === "wp_inactive_widgets") {
      return 1;
    }
    if (b.id === "wp_inactive_widgets") {
      return -1;
    }
    return 0;
  });
  for (const widgetArea of sortedWidgetAreas) {
    widgetAreaBlocks.push(
      (0,external_wp_blocks_namespaceObject.createBlock)("core/widget-area", {
        id: widgetArea.id,
        name: widgetArea.name
      })
    );
    if (!widgetArea.widgets.length) {
      dispatch(
        persistStubPost(
          buildWidgetAreaPostId(widgetArea.id),
          []
        )
      );
    }
  }
  const widgetAreasOpenState = {};
  widgetAreaBlocks.forEach((widgetAreaBlock, index) => {
    widgetAreasOpenState[widgetAreaBlock.clientId] = index === 0;
  });
  dispatch(setWidgetAreasOpenState(widgetAreasOpenState));
  dispatch(
    persistStubPost(buildWidgetAreasPostId(), widgetAreaBlocks)
  );
};
const getWidgets = () => async ({ dispatch, registry }) => {
  const query = buildWidgetsQuery();
  const widgets = await registry.resolveSelect(external_wp_coreData_namespaceObject.store).getEntityRecords("root", "widget", query);
  const groupedBySidebar = {};
  for (const widget of widgets) {
    const block = transformWidgetToBlock(widget);
    groupedBySidebar[widget.sidebar] = groupedBySidebar[widget.sidebar] || [];
    groupedBySidebar[widget.sidebar].push(block);
  }
  for (const sidebarId in groupedBySidebar) {
    if (groupedBySidebar.hasOwnProperty(sidebarId)) {
      dispatch(
        persistStubPost(
          buildWidgetAreaPostId(sidebarId),
          groupedBySidebar[sidebarId]
        )
      );
    }
  }
};


;// ./node_modules/@wordpress/edit-widgets/build-module/store/selectors.js






const EMPTY_INSERTION_POINT = {
  rootClientId: void 0,
  insertionIndex: void 0
};
const selectors_getWidgets = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (0,external_wp_data_namespaceObject.createSelector)(
    () => {
      const widgets = select(external_wp_coreData_namespaceObject.store).getEntityRecords(
        "root",
        "widget",
        buildWidgetsQuery()
      );
      return (
        // Key widgets by their ID.
        widgets?.reduce(
          (allWidgets, widget) => ({
            ...allWidgets,
            [widget.id]: widget
          }),
          {}
        ) ?? {}
      );
    },
    () => [
      select(external_wp_coreData_namespaceObject.store).getEntityRecords(
        "root",
        "widget",
        buildWidgetsQuery()
      )
    ]
  )
);
const getWidget = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, id) => {
    const widgets = select(constants_STORE_NAME).getWidgets();
    return widgets[id];
  }
);
const selectors_getWidgetAreas = (0,external_wp_data_namespaceObject.createRegistrySelector)((select) => () => {
  const query = buildWidgetAreasQuery();
  return select(external_wp_coreData_namespaceObject.store).getEntityRecords(
    KIND,
    WIDGET_AREA_ENTITY_TYPE,
    query
  );
});
const getWidgetAreaForWidgetId = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, widgetId) => {
    const widgetAreas = select(constants_STORE_NAME).getWidgetAreas();
    return widgetAreas.find((widgetArea) => {
      const post = select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(
        KIND,
        POST_TYPE,
        buildWidgetAreaPostId(widgetArea.id)
      );
      const blockWidgetIds = post.blocks.map(
        (block) => (0,external_wp_widgets_namespaceObject.getWidgetIdFromBlock)(block)
      );
      return blockWidgetIds.includes(widgetId);
    });
  }
);
const getParentWidgetAreaBlock = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, clientId) => {
    const { getBlock, getBlockName, getBlockParents } = select(external_wp_blockEditor_namespaceObject.store);
    const blockParents = getBlockParents(clientId);
    const widgetAreaClientId = blockParents.find(
      (parentClientId) => getBlockName(parentClientId) === "core/widget-area"
    );
    return getBlock(widgetAreaClientId);
  }
);
const getEditedWidgetAreas = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, ids) => {
    let widgetAreas = select(constants_STORE_NAME).getWidgetAreas();
    if (!widgetAreas) {
      return [];
    }
    if (ids) {
      widgetAreas = widgetAreas.filter(
        ({ id }) => ids.includes(id)
      );
    }
    return widgetAreas.filter(
      ({ id }) => select(external_wp_coreData_namespaceObject.store).hasEditsForEntityRecord(
        KIND,
        POST_TYPE,
        buildWidgetAreaPostId(id)
      )
    ).map(
      ({ id }) => select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(
        KIND,
        WIDGET_AREA_ENTITY_TYPE,
        id
      )
    );
  }
);
const getReferenceWidgetBlocks = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, referenceWidgetName = null) => {
    const results = [];
    const widgetAreas = select(constants_STORE_NAME).getWidgetAreas();
    for (const _widgetArea of widgetAreas) {
      const post = select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(
        KIND,
        POST_TYPE,
        buildWidgetAreaPostId(_widgetArea.id)
      );
      for (const block of post.blocks) {
        if (block.name === "core/legacy-widget" && (!referenceWidgetName || block.attributes?.referenceWidgetName === referenceWidgetName)) {
          results.push(block);
        }
      }
    }
    return results;
  }
);
const isSavingWidgetAreas = (0,external_wp_data_namespaceObject.createRegistrySelector)((select) => () => {
  const widgetAreasIds = select(constants_STORE_NAME).getWidgetAreas()?.map(({ id }) => id);
  if (!widgetAreasIds) {
    return false;
  }
  for (const id of widgetAreasIds) {
    const isSaving = select(external_wp_coreData_namespaceObject.store).isSavingEntityRecord(
      KIND,
      WIDGET_AREA_ENTITY_TYPE,
      id
    );
    if (isSaving) {
      return true;
    }
  }
  const widgetIds = [
    ...Object.keys(select(constants_STORE_NAME).getWidgets()),
    void 0
    // account for new widgets without an ID
  ];
  for (const id of widgetIds) {
    const isSaving = select(external_wp_coreData_namespaceObject.store).isSavingEntityRecord(
      "root",
      "widget",
      id
    );
    if (isSaving) {
      return true;
    }
  }
  return false;
});
const getIsWidgetAreaOpen = (state, clientId) => {
  const { widgetAreasOpenState } = state;
  return !!widgetAreasOpenState[clientId];
};
function isInserterOpened(state) {
  return !!state.blockInserterPanel;
}
function __experimentalGetInsertionPoint(state) {
  if (typeof state.blockInserterPanel === "boolean") {
    return EMPTY_INSERTION_POINT;
  }
  return state.blockInserterPanel;
}
const canInsertBlockInWidgetArea = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, blockName) => {
    const widgetAreas = select(external_wp_blockEditor_namespaceObject.store).getBlocks();
    const [firstWidgetArea] = widgetAreas;
    return select(external_wp_blockEditor_namespaceObject.store).canInsertBlockType(
      blockName,
      firstWidgetArea.clientId
    );
  }
);
function isListViewOpened(state) {
  return state.listViewPanel;
}
function isWidgetSavingLocked(state) {
  return Object.keys(state.widgetSavingLock).length > 0;
}


;// ./node_modules/@wordpress/edit-widgets/build-module/store/private-selectors.js
function getListViewToggleRef(state) {
  return state.listViewToggleRef;
}
function getInserterSidebarToggleRef(state) {
  return state.inserterSidebarToggleRef;
}


;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/edit-widgets/build-module/lock-unlock.js

const { lock, unlock } = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/edit-widgets"
);


;// ./node_modules/@wordpress/edit-widgets/build-module/store/index.js









const storeConfig = {
  reducer: reducer_default,
  selectors: store_selectors_namespaceObject,
  resolvers: resolvers_namespaceObject,
  actions: store_actions_namespaceObject
};
const store_store = (0,external_wp_data_namespaceObject.createReduxStore)(constants_STORE_NAME, storeConfig);
(0,external_wp_data_namespaceObject.register)(store_store);
external_wp_apiFetch_default().use(function(options, next) {
  if (options.path?.indexOf("/wp/v2/types/widget-area") === 0) {
    return Promise.resolve({});
  }
  return next(options);
});
unlock(store_store).registerPrivateSelectors(private_selectors_namespaceObject);


;// external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// ./node_modules/@wordpress/edit-widgets/build-module/filters/move-to-widget-area.js







const withMoveToWidgetAreaToolbarItem = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(
  (BlockEdit) => (props) => {
    const { clientId, name: blockName } = props;
    const { widgetAreas, currentWidgetAreaId, canInsertBlockInWidgetArea } = (0,external_wp_data_namespaceObject.useSelect)(
      (select) => {
        if (blockName === "core/widget-area") {
          return {};
        }
        const selectors = select(store_store);
        const widgetAreaBlock = selectors.getParentWidgetAreaBlock(clientId);
        return {
          widgetAreas: selectors.getWidgetAreas(),
          currentWidgetAreaId: widgetAreaBlock?.attributes?.id,
          canInsertBlockInWidgetArea: selectors.canInsertBlockInWidgetArea(blockName)
        };
      },
      [clientId, blockName]
    );
    const { moveBlockToWidgetArea } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
    const hasMultipleWidgetAreas = widgetAreas?.length > 1;
    const isMoveToWidgetAreaVisible = blockName !== "core/widget-area" && hasMultipleWidgetAreas && canInsertBlockInWidgetArea;
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(BlockEdit, { ...props }, "edit"),
      isMoveToWidgetAreaVisible && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockControls, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        external_wp_widgets_namespaceObject.MoveToWidgetArea,
        {
          widgetAreas,
          currentWidgetAreaId,
          onSelect: (widgetAreaId) => {
            moveBlockToWidgetArea(
              props.clientId,
              widgetAreaId
            );
          }
        }
      ) })
    ] });
  },
  "withMoveToWidgetAreaToolbarItem"
);
(0,external_wp_hooks_namespaceObject.addFilter)(
  "editor.BlockEdit",
  "core/edit-widgets/block-edit",
  withMoveToWidgetAreaToolbarItem
);

;// external ["wp","mediaUtils"]
const external_wp_mediaUtils_namespaceObject = window["wp"]["mediaUtils"];
;// ./node_modules/@wordpress/edit-widgets/build-module/filters/replace-media-upload.js


const replaceMediaUpload = () => external_wp_mediaUtils_namespaceObject.MediaUpload;
(0,external_wp_hooks_namespaceObject.addFilter)(
  "editor.MediaUpload",
  "core/edit-widgets/replace-media-upload",
  replaceMediaUpload
);

;// ./node_modules/@wordpress/edit-widgets/build-module/filters/index.js



;// ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/block.json
const block_namespaceObject = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"core/widget-area","title":"Widget Area","category":"widgets","attributes":{"id":{"type":"string"},"name":{"type":"string"}},"supports":{"html":false,"inserter":false,"customClassName":false,"reusable":false,"__experimentalToolbar":false,"__experimentalParentSelector":false,"__experimentalDisableBlockOverlay":true},"editorStyle":"wp-block-widget-area-editor","style":"wp-block-widget-area"}');
;// ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/edit/use-is-dragging-within.js

const useIsDraggingWithin = (elementRef) => {
  const [isDraggingWithin, setIsDraggingWithin] = (0,external_wp_element_namespaceObject.useState)(false);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const { ownerDocument } = elementRef.current;
    function handleDragStart(event) {
      handleDragEnter(event);
    }
    function handleDragEnd() {
      setIsDraggingWithin(false);
    }
    function handleDragEnter(event) {
      if (elementRef.current.contains(event.target)) {
        setIsDraggingWithin(true);
      } else {
        setIsDraggingWithin(false);
      }
    }
    ownerDocument.addEventListener("dragstart", handleDragStart);
    ownerDocument.addEventListener("dragend", handleDragEnd);
    ownerDocument.addEventListener("dragenter", handleDragEnter);
    return () => {
      ownerDocument.removeEventListener("dragstart", handleDragStart);
      ownerDocument.removeEventListener("dragend", handleDragEnd);
      ownerDocument.removeEventListener("dragenter", handleDragEnter);
    };
  }, []);
  return isDraggingWithin;
};
var use_is_dragging_within_default = useIsDraggingWithin;


;// ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/edit/inner-blocks.js






function WidgetAreaInnerBlocks({ id }) {
  const [blocks, onInput, onChange] = (0,external_wp_coreData_namespaceObject.useEntityBlockEditor)(
    "root",
    "postType"
  );
  const innerBlocksRef = (0,external_wp_element_namespaceObject.useRef)();
  const isDraggingWithinInnerBlocks = use_is_dragging_within_default(innerBlocksRef);
  const shouldHighlightDropZone = isDraggingWithinInnerBlocks;
  const innerBlocksProps = (0,external_wp_blockEditor_namespaceObject.useInnerBlocksProps)(
    { ref: innerBlocksRef },
    {
      value: blocks,
      onInput,
      onChange,
      templateLock: false,
      renderAppender: external_wp_blockEditor_namespaceObject.InnerBlocks.ButtonBlockAppender
    }
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    "div",
    {
      "data-widget-area-id": id,
      className: dist_clsx(
        "wp-block-widget-area__inner-blocks block-editor-inner-blocks editor-styles-wrapper",
        {
          "wp-block-widget-area__highlight-drop-zone": shouldHighlightDropZone
        }
      ),
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { ...innerBlocksProps })
    }
  );
}


;// ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/edit/index.js









function WidgetAreaEdit({
  clientId,
  attributes: { id, name }
}) {
  const isOpen = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(store_store).getIsWidgetAreaOpen(clientId),
    [clientId]
  );
  const { setIsWidgetAreaOpen } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const wrapper = (0,external_wp_element_namespaceObject.useRef)();
  const setOpen = (0,external_wp_element_namespaceObject.useCallback)(
    (openState) => setIsWidgetAreaOpen(clientId, openState),
    [clientId]
  );
  const isDragging = useIsDragging(wrapper);
  const isDraggingWithin = use_is_dragging_within_default(wrapper);
  const [openedWhileDragging, setOpenedWhileDragging] = (0,external_wp_element_namespaceObject.useState)(false);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!isDragging) {
      setOpenedWhileDragging(false);
      return;
    }
    if (isDraggingWithin && !isOpen) {
      setOpen(true);
      setOpenedWhileDragging(true);
    } else if (!isDraggingWithin && isOpen && openedWhileDragging) {
      setOpen(false);
    }
  }, [isOpen, isDragging, isDraggingWithin, openedWhileDragging]);
  const blockProps = (0,external_wp_blockEditor_namespaceObject.useBlockProps)();
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { ...blockProps, children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Panel, { ref: wrapper, children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.PanelBody,
    {
      title: name,
      opened: isOpen,
      onToggle: () => {
        setIsWidgetAreaOpen(clientId, !isOpen);
      },
      scrollAfterOpen: !isDragging,
      children: ({ opened }) => (
        // This is required to ensure LegacyWidget blocks are not
        // unmounted when the panel is collapsed. Unmounting legacy
        // widgets may have unintended consequences (e.g.  TinyMCE
        // not being properly reinitialized)
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.__unstableDisclosureContent,
          {
            className: "wp-block-widget-area__panel-body-content",
            visible: opened,
            children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_coreData_namespaceObject.EntityProvider,
              {
                kind: "root",
                type: "postType",
                id: `widget-area-${id}`,
                children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WidgetAreaInnerBlocks, { id })
              }
            )
          }
        )
      )
    }
  ) }) });
}
const useIsDragging = (elementRef) => {
  const [isDragging, setIsDragging] = (0,external_wp_element_namespaceObject.useState)(false);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const { ownerDocument } = elementRef.current;
    function handleDragStart() {
      setIsDragging(true);
    }
    function handleDragEnd() {
      setIsDragging(false);
    }
    ownerDocument.addEventListener("dragstart", handleDragStart);
    ownerDocument.addEventListener("dragend", handleDragEnd);
    return () => {
      ownerDocument.removeEventListener("dragstart", handleDragStart);
      ownerDocument.removeEventListener("dragend", handleDragEnd);
    };
  }, []);
  return isDragging;
};


;// ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/index.js



const { name: widget_area_name } = block_namespaceObject;
const settings = {
  title: (0,external_wp_i18n_namespaceObject.__)("Widget Area"),
  description: (0,external_wp_i18n_namespaceObject.__)("A widget area container."),
  __experimentalLabel: ({ name: label }) => label,
  edit: WidgetAreaEdit
};


;// ./node_modules/@wordpress/edit-widgets/build-module/components/error-boundary/index.js







function CopyButton({ text, children }) {
  const ref = (0,external_wp_compose_namespaceObject.useCopyToClipboard)(text);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, { __next40pxDefaultSize: true, variant: "secondary", ref, children });
}
function ErrorBoundaryWarning({ message, error }) {
  const actions = [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(CopyButton, { text: error.stack, children: (0,external_wp_i18n_namespaceObject.__)("Copy Error") }, "copy-error")
  ];
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.Warning, { className: "edit-widgets-error-boundary", actions, children: message });
}
class ErrorBoundary extends external_wp_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.state = {
      error: null
    };
  }
  componentDidCatch(error) {
    (0,external_wp_hooks_namespaceObject.doAction)("editor.ErrorBoundary.errorLogged", error);
  }
  static getDerivedStateFromError(error) {
    return { error };
  }
  render() {
    if (!this.state.error) {
      return this.props.children;
    }
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      ErrorBoundaryWarning,
      {
        message: (0,external_wp_i18n_namespaceObject.__)(
          "The editor has encountered an unexpected error."
        ),
        error: this.state.error
      }
    );
  }
}


;// external ["wp","patterns"]
const external_wp_patterns_namespaceObject = window["wp"]["patterns"];
;// external ["wp","keyboardShortcuts"]
const external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
;// external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcuts/index.js







function KeyboardShortcuts() {
  const { redo, undo } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const { saveEditedWidgetAreas } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)("core/edit-widgets/undo", (event) => {
    undo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)("core/edit-widgets/redo", (event) => {
    redo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)("core/edit-widgets/save", (event) => {
    event.preventDefault();
    saveEditedWidgetAreas();
  });
  return null;
}
function KeyboardShortcutsRegister() {
  const { registerShortcut } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: "core/edit-widgets/undo",
      category: "global",
      description: (0,external_wp_i18n_namespaceObject.__)("Undo your last changes."),
      keyCombination: {
        modifier: "primary",
        character: "z"
      }
    });
    registerShortcut({
      name: "core/edit-widgets/redo",
      category: "global",
      description: (0,external_wp_i18n_namespaceObject.__)("Redo your last undo."),
      keyCombination: {
        modifier: "primaryShift",
        character: "z"
      },
      // Disable on Apple OS because it conflicts with the browser's
      // history shortcut. It's a fine alias for both Windows and Linux.
      // Since there's no conflict for Ctrl+Shift+Z on both Windows and
      // Linux, we keep it as the default for consistency.
      aliases: (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? [] : [
        {
          modifier: "primary",
          character: "y"
        }
      ]
    });
    registerShortcut({
      name: "core/edit-widgets/save",
      category: "global",
      description: (0,external_wp_i18n_namespaceObject.__)("Save your changes."),
      keyCombination: {
        modifier: "primary",
        character: "s"
      }
    });
    registerShortcut({
      name: "core/edit-widgets/keyboard-shortcuts",
      category: "main",
      description: (0,external_wp_i18n_namespaceObject.__)("Display these keyboard shortcuts."),
      keyCombination: {
        modifier: "access",
        character: "h"
      }
    });
    registerShortcut({
      name: "core/edit-widgets/next-region",
      category: "global",
      description: (0,external_wp_i18n_namespaceObject.__)("Navigate to the next part of the editor."),
      keyCombination: {
        modifier: "ctrl",
        character: "`"
      },
      aliases: [
        {
          modifier: "access",
          character: "n"
        }
      ]
    });
    registerShortcut({
      name: "core/edit-widgets/previous-region",
      category: "global",
      description: (0,external_wp_i18n_namespaceObject.__)("Navigate to the previous part of the editor."),
      keyCombination: {
        modifier: "ctrlShift",
        character: "`"
      },
      aliases: [
        {
          modifier: "access",
          character: "p"
        },
        {
          modifier: "ctrlShift",
          character: "~"
        }
      ]
    });
  }, [registerShortcut]);
  return null;
}
KeyboardShortcuts.Register = KeyboardShortcutsRegister;
var keyboard_shortcuts_default = KeyboardShortcuts;


;// ./node_modules/@wordpress/edit-widgets/build-module/hooks/use-last-selected-widget-area.js





const useLastSelectedWidgetArea = () => (0,external_wp_data_namespaceObject.useSelect)((select) => {
  const { getBlockSelectionEnd, getBlockName } = select(external_wp_blockEditor_namespaceObject.store);
  const selectionEndClientId = getBlockSelectionEnd();
  if (getBlockName(selectionEndClientId) === "core/widget-area") {
    return selectionEndClientId;
  }
  const { getParentWidgetAreaBlock } = select(store_store);
  const widgetAreaBlock = getParentWidgetAreaBlock(selectionEndClientId);
  const widgetAreaBlockClientId = widgetAreaBlock?.clientId;
  if (widgetAreaBlockClientId) {
    return widgetAreaBlockClientId;
  }
  const { getEntityRecord } = select(external_wp_coreData_namespaceObject.store);
  const widgetAreasPost = getEntityRecord(
    KIND,
    POST_TYPE,
    buildWidgetAreasPostId()
  );
  return widgetAreasPost?.blocks[0]?.clientId;
}, []);
var use_last_selected_widget_area_default = useLastSelectedWidgetArea;


;// ./node_modules/@wordpress/edit-widgets/build-module/constants.js
const ALLOW_REUSABLE_BLOCKS = false;
const ENABLE_EXPERIMENTAL_FSE_BLOCKS = false;


;// ./node_modules/@wordpress/edit-widgets/build-module/components/widget-areas-block-editor-provider/index.js

















const { ExperimentalBlockEditorProvider } = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const { PatternsMenuItems } = unlock(external_wp_patterns_namespaceObject.privateApis);
const { BlockKeyboardShortcuts } = unlock(external_wp_blockLibrary_namespaceObject.privateApis);
const EMPTY_ARRAY = [];
function WidgetAreasBlockEditorProvider({
  blockEditorSettings,
  children,
  ...props
}) {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium");
  const {
    hasUploadPermissions,
    reusableBlocks,
    isFixedToolbarActive,
    keepCaretInsideBlock,
    pageOnFront,
    pageForPosts
  } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { canUser, getEntityRecord, getEntityRecords } = select(external_wp_coreData_namespaceObject.store);
    const siteSettings = canUser("read", {
      kind: "root",
      name: "site"
    }) ? getEntityRecord("root", "site") : void 0;
    return {
      hasUploadPermissions: canUser("create", {
        kind: "postType",
        name: "attachment"
      }) ?? true,
      reusableBlocks: ALLOW_REUSABLE_BLOCKS ? getEntityRecords("postType", "wp_block") : EMPTY_ARRAY,
      isFixedToolbarActive: !!select(external_wp_preferences_namespaceObject.store).get(
        "core/edit-widgets",
        "fixedToolbar"
      ),
      keepCaretInsideBlock: !!select(external_wp_preferences_namespaceObject.store).get(
        "core/edit-widgets",
        "keepCaretInsideBlock"
      ),
      pageOnFront: siteSettings?.page_on_front,
      pageForPosts: siteSettings?.page_for_posts
    };
  }, []);
  const { setIsInserterOpened } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const settings = (0,external_wp_element_namespaceObject.useMemo)(() => {
    let mediaUploadBlockEditor;
    if (hasUploadPermissions) {
      mediaUploadBlockEditor = ({ onError, ...argumentsObject }) => {
        (0,external_wp_mediaUtils_namespaceObject.uploadMedia)({
          wpAllowedMimeTypes: blockEditorSettings.allowedMimeTypes,
          onError: ({ message }) => onError(message),
          ...argumentsObject
        });
      };
    }
    return {
      ...blockEditorSettings,
      __experimentalReusableBlocks: reusableBlocks,
      hasFixedToolbar: isFixedToolbarActive || !isLargeViewport,
      keepCaretInsideBlock,
      mediaUpload: mediaUploadBlockEditor,
      templateLock: "all",
      __experimentalSetIsInserterOpened: setIsInserterOpened,
      pageOnFront,
      pageForPosts,
      editorTool: "edit"
    };
  }, [
    hasUploadPermissions,
    blockEditorSettings,
    isFixedToolbarActive,
    isLargeViewport,
    keepCaretInsideBlock,
    reusableBlocks,
    setIsInserterOpened,
    pageOnFront,
    pageForPosts
  ]);
  const widgetAreaId = use_last_selected_widget_area_default();
  const [blocks, onInput, onChange] = (0,external_wp_coreData_namespaceObject.useEntityBlockEditor)(
    KIND,
    POST_TYPE,
    { id: buildWidgetAreasPostId() }
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.SlotFillProvider, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(keyboard_shortcuts_default.Register, {}),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(BlockKeyboardShortcuts, {}),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
      ExperimentalBlockEditorProvider,
      {
        value: blocks,
        onInput,
        onChange,
        settings,
        useSubRegistry: false,
        ...props,
        children: [
          children,
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(PatternsMenuItems, { rootClientId: widgetAreaId })
        ]
      }
    )
  ] });
}


;// ./node_modules/@wordpress/icons/build-module/library/drawer-left.js


var drawer_left_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
  external_wp_primitives_namespaceObject.Path,
  {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM8.5 18.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h2.5v13zm10-.5c0 .3-.2.5-.5.5h-8v-13h8c.3 0 .5.2.5.5v12z"
  }
) });


;// ./node_modules/@wordpress/icons/build-module/library/drawer-right.js


var drawer_right_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
  external_wp_primitives_namespaceObject.Path,
  {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4 14.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h8v13zm4.5-.5c0 .3-.2.5-.5.5h-2.5v-13H18c.3 0 .5.2.5.5v12z"
  }
) });


;// ./node_modules/@wordpress/icons/build-module/library/block-default.js


var block_default_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M19 8h-1V6h-5v2h-2V6H6v2H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm.5 10c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-8c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v8z" }) });


;// external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// external ["wp","dom"]
const external_wp_dom_namespaceObject = window["wp"]["dom"];
;// ./node_modules/@wordpress/edit-widgets/build-module/components/sidebar/widget-areas.js










function WidgetAreas({ selectedWidgetAreaId }) {
  const widgetAreas = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(store_store).getWidgetAreas(),
    []
  );
  const selectedWidgetArea = (0,external_wp_element_namespaceObject.useMemo)(
    () => selectedWidgetAreaId && widgetAreas?.find(
      (widgetArea) => widgetArea.id === selectedWidgetAreaId
    ),
    [selectedWidgetAreaId, widgetAreas]
  );
  let description;
  if (!selectedWidgetArea) {
    description = (0,external_wp_i18n_namespaceObject.__)(
      // eslint-disable-next-line no-restricted-syntax -- 'sidebar' is a common web design term for layouts
      "Widget Areas are global parts in your site\u2019s layout that can accept blocks. These vary by theme, but are typically parts like your Sidebar or Footer."
    );
  } else if (selectedWidgetAreaId === "wp_inactive_widgets") {
    description = (0,external_wp_i18n_namespaceObject.__)(
      "Blocks in this Widget Area will not be displayed in your site."
    );
  } else {
    description = selectedWidgetArea.description;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "edit-widgets-widget-areas", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "edit-widgets-widget-areas__top-container", children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockIcon, { icon: block_default_default }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { children: [
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        "p",
        {
          dangerouslySetInnerHTML: {
            __html: (0,external_wp_dom_namespaceObject.safeHTML)(description)
          }
        }
      ),
      widgetAreas?.length === 0 && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { children: (0,external_wp_i18n_namespaceObject.__)(
        "Your theme does not contain any Widget Areas."
      ) }),
      !selectedWidgetArea && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        external_wp_components_namespaceObject.Button,
        {
          __next40pxDefaultSize: true,
          href: (0,external_wp_url_namespaceObject.addQueryArgs)("customize.php", {
            "autofocus[panel]": "widgets",
            return: window.location.pathname
          }),
          variant: "tertiary",
          children: (0,external_wp_i18n_namespaceObject.__)("Manage with live preview")
        }
      )
    ] })
  ] }) });
}


;// ./node_modules/@wordpress/edit-widgets/build-module/components/sidebar/index.js








const SIDEBAR_ACTIVE_BY_DEFAULT = external_wp_element_namespaceObject.Platform.select({
  web: true,
  native: false
});
const BLOCK_INSPECTOR_IDENTIFIER = "edit-widgets/block-inspector";
const WIDGET_AREAS_IDENTIFIER = "edit-widgets/block-areas";



const { Tabs } = unlock(external_wp_components_namespaceObject.privateApis);
function SidebarHeader({ selectedWidgetAreaBlock }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(Tabs.TabList, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Tabs.Tab, { tabId: WIDGET_AREAS_IDENTIFIER, children: selectedWidgetAreaBlock ? selectedWidgetAreaBlock.attributes.name : (0,external_wp_i18n_namespaceObject.__)("Widget Areas") }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Tabs.Tab, { tabId: BLOCK_INSPECTOR_IDENTIFIER, children: (0,external_wp_i18n_namespaceObject.__)("Block") })
  ] });
}
function SidebarContent({
  hasSelectedNonAreaBlock,
  currentArea,
  isGeneralSidebarOpen,
  selectedWidgetAreaBlock
}) {
  const { enableComplementaryArea } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (hasSelectedNonAreaBlock && currentArea === WIDGET_AREAS_IDENTIFIER && isGeneralSidebarOpen) {
      enableComplementaryArea(
        "core/edit-widgets",
        BLOCK_INSPECTOR_IDENTIFIER
      );
    }
    if (!hasSelectedNonAreaBlock && currentArea === BLOCK_INSPECTOR_IDENTIFIER && isGeneralSidebarOpen) {
      enableComplementaryArea(
        "core/edit-widgets",
        WIDGET_AREAS_IDENTIFIER
      );
    }
  }, [hasSelectedNonAreaBlock, enableComplementaryArea]);
  const tabsContextValue = (0,external_wp_element_namespaceObject.useContext)(Tabs.Context);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    complementary_area_default,
    {
      className: "edit-widgets-sidebar",
      header: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Tabs.Context.Provider, { value: tabsContextValue, children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        SidebarHeader,
        {
          selectedWidgetAreaBlock
        }
      ) }),
      headerClassName: "edit-widgets-sidebar__panel-tabs",
      title: (0,external_wp_i18n_namespaceObject.__)("Settings"),
      closeLabel: (0,external_wp_i18n_namespaceObject.__)("Close Settings"),
      scope: "core/edit-widgets",
      identifier: currentArea,
      icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? drawer_left_default : drawer_right_default,
      isActiveByDefault: SIDEBAR_ACTIVE_BY_DEFAULT,
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(Tabs.Context.Provider, { value: tabsContextValue, children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          Tabs.TabPanel,
          {
            tabId: WIDGET_AREAS_IDENTIFIER,
            focusable: false,
            children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              WidgetAreas,
              {
                selectedWidgetAreaId: selectedWidgetAreaBlock?.attributes.id
              }
            )
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          Tabs.TabPanel,
          {
            tabId: BLOCK_INSPECTOR_IDENTIFIER,
            focusable: false,
            children: hasSelectedNonAreaBlock ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockInspector, {}) : (
              // Pretend that Widget Areas are part of the UI by not
              // showing the Block Inspector when one is selected.
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("span", { className: "block-editor-block-inspector__no-blocks", children: (0,external_wp_i18n_namespaceObject.__)("No block selected.") })
            )
          }
        )
      ] })
    }
  );
}
function Sidebar() {
  const {
    currentArea,
    hasSelectedNonAreaBlock,
    isGeneralSidebarOpen,
    selectedWidgetAreaBlock
  } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getSelectedBlock, getBlock, getBlockParentsByBlockName } = select(external_wp_blockEditor_namespaceObject.store);
    const { getActiveComplementaryArea } = select(store);
    const selectedBlock = getSelectedBlock();
    const activeArea = getActiveComplementaryArea(store_store.name);
    let currentSelection = activeArea;
    if (!currentSelection) {
      if (selectedBlock) {
        currentSelection = BLOCK_INSPECTOR_IDENTIFIER;
      } else {
        currentSelection = WIDGET_AREAS_IDENTIFIER;
      }
    }
    let widgetAreaBlock;
    if (selectedBlock) {
      if (selectedBlock.name === "core/widget-area") {
        widgetAreaBlock = selectedBlock;
      } else {
        widgetAreaBlock = getBlock(
          getBlockParentsByBlockName(
            selectedBlock.clientId,
            "core/widget-area"
          )[0]
        );
      }
    }
    return {
      currentArea: currentSelection,
      hasSelectedNonAreaBlock: !!(selectedBlock && selectedBlock.name !== "core/widget-area"),
      isGeneralSidebarOpen: !!activeArea,
      selectedWidgetAreaBlock: widgetAreaBlock
    };
  }, []);
  const { enableComplementaryArea } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const onTabSelect = (0,external_wp_element_namespaceObject.useCallback)(
    (newSelectedTabId) => {
      if (!!newSelectedTabId) {
        enableComplementaryArea(
          store_store.name,
          newSelectedTabId
        );
      }
    },
    [enableComplementaryArea]
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    Tabs,
    {
      selectedTabId: isGeneralSidebarOpen ? currentArea : null,
      onSelect: onTabSelect,
      selectOnMove: false,
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        SidebarContent,
        {
          hasSelectedNonAreaBlock,
          currentArea,
          isGeneralSidebarOpen,
          selectedWidgetAreaBlock
        }
      )
    }
  );
}


;// ./node_modules/@wordpress/icons/build-module/library/plus.js


var plus_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M11 12.5V17.5H12.5V12.5H17.5V11H12.5V6H11V11H6V12.5H11Z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/list-view.js


var list_view_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M3 6h11v1.5H3V6Zm3.5 5.5h11V13h-11v-1.5ZM21 17H10v1.5h11V17Z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/undo.js


var undo_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/redo.js


var redo_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z" }) });


;// ./node_modules/@wordpress/edit-widgets/build-module/components/header/undo-redo/undo.js








function UndoButton(props, ref) {
  const hasUndo = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(external_wp_coreData_namespaceObject.store).hasUndo(),
    []
  );
  const { undo } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Button,
    {
      ...props,
      ref,
      icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? undo_default : redo_default,
      label: (0,external_wp_i18n_namespaceObject.__)("Undo"),
      shortcut: external_wp_keycodes_namespaceObject.displayShortcut.primary("z"),
      "aria-disabled": !hasUndo,
      onClick: hasUndo ? undo : void 0,
      size: "compact"
    }
  );
}
var undo_undo_default = (0,external_wp_element_namespaceObject.forwardRef)(UndoButton);


;// ./node_modules/@wordpress/edit-widgets/build-module/components/header/undo-redo/redo.js








function RedoButton(props, ref) {
  const shortcut = (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? external_wp_keycodes_namespaceObject.displayShortcut.primaryShift("z") : external_wp_keycodes_namespaceObject.displayShortcut.primary("y");
  const hasRedo = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(external_wp_coreData_namespaceObject.store).hasRedo(),
    []
  );
  const { redo } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Button,
    {
      ...props,
      ref,
      icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? redo_default : undo_default,
      label: (0,external_wp_i18n_namespaceObject.__)("Redo"),
      shortcut,
      "aria-disabled": !hasRedo,
      onClick: hasRedo ? redo : void 0,
      size: "compact"
    }
  );
}
var redo_redo_default = (0,external_wp_element_namespaceObject.forwardRef)(RedoButton);


;// ./node_modules/@wordpress/edit-widgets/build-module/components/header/document-tools/index.js












function DocumentTools() {
  const isMediumViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium");
  const {
    isInserterOpen,
    isListViewOpen,
    inserterSidebarToggleRef,
    listViewToggleRef
  } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const {
      isInserterOpened,
      getInserterSidebarToggleRef,
      isListViewOpened,
      getListViewToggleRef
    } = unlock(select(store_store));
    return {
      isInserterOpen: isInserterOpened(),
      isListViewOpen: isListViewOpened(),
      inserterSidebarToggleRef: getInserterSidebarToggleRef(),
      listViewToggleRef: getListViewToggleRef()
    };
  }, []);
  const { setIsInserterOpened, setIsListViewOpened } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const toggleListView = (0,external_wp_element_namespaceObject.useCallback)(
    () => setIsListViewOpened(!isListViewOpen),
    [setIsListViewOpened, isListViewOpen]
  );
  const toggleInserterSidebar = (0,external_wp_element_namespaceObject.useCallback)(
    () => setIsInserterOpened(!isInserterOpen),
    [setIsInserterOpened, isInserterOpen]
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
    external_wp_blockEditor_namespaceObject.NavigableToolbar,
    {
      className: "edit-widgets-header-toolbar",
      "aria-label": (0,external_wp_i18n_namespaceObject.__)("Document tools"),
      variant: "unstyled",
      children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.ToolbarItem,
          {
            ref: inserterSidebarToggleRef,
            as: external_wp_components_namespaceObject.Button,
            className: "edit-widgets-header-toolbar__inserter-toggle",
            variant: "primary",
            isPressed: isInserterOpen,
            onMouseDown: (event) => {
              event.preventDefault();
            },
            onClick: toggleInserterSidebar,
            icon: plus_default,
            label: (0,external_wp_i18n_namespaceObject._x)(
              "Block Inserter",
              "Generic label for block inserter button"
            ),
            size: "compact"
          }
        ),
        isMediumViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarItem, { as: undo_undo_default }),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarItem, { as: redo_redo_default }),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.ToolbarItem,
            {
              as: external_wp_components_namespaceObject.Button,
              className: "edit-widgets-header-toolbar__list-view-toggle",
              icon: list_view_default,
              isPressed: isListViewOpen,
              label: (0,external_wp_i18n_namespaceObject.__)("List View"),
              onClick: toggleListView,
              ref: listViewToggleRef,
              size: "compact"
            }
          )
        ] })
      ]
    }
  );
}
var document_tools_default = DocumentTools;


;// ./node_modules/@wordpress/edit-widgets/build-module/components/save-button/index.js





function SaveButton() {
  const { hasEditedWidgetAreaIds, isSaving, isWidgetSaveLocked } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const {
        getEditedWidgetAreas,
        isSavingWidgetAreas,
        isWidgetSavingLocked
      } = select(store_store);
      return {
        hasEditedWidgetAreaIds: getEditedWidgetAreas()?.length > 0,
        isSaving: isSavingWidgetAreas(),
        isWidgetSaveLocked: isWidgetSavingLocked()
      };
    },
    []
  );
  const { saveEditedWidgetAreas } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const isDisabled = isWidgetSaveLocked || isSaving || !hasEditedWidgetAreaIds;
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Button,
    {
      variant: "primary",
      isBusy: isSaving,
      "aria-disabled": isDisabled,
      onClick: isDisabled ? void 0 : saveEditedWidgetAreas,
      size: "compact",
      children: isSaving ? (0,external_wp_i18n_namespaceObject.__)("Saving\u2026") : (0,external_wp_i18n_namespaceObject.__)("Update")
    }
  );
}
var save_button_default = SaveButton;


;// ./node_modules/@wordpress/icons/build-module/library/more-vertical.js


var more_vertical_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/external.js


var external_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z" }) });


;// ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcut-help-modal/config.js

const textFormattingShortcuts = [
  {
    keyCombination: { modifier: "primary", character: "b" },
    description: (0,external_wp_i18n_namespaceObject.__)("Make the selected text bold.")
  },
  {
    keyCombination: { modifier: "primary", character: "i" },
    description: (0,external_wp_i18n_namespaceObject.__)("Make the selected text italic.")
  },
  {
    keyCombination: { modifier: "primary", character: "k" },
    description: (0,external_wp_i18n_namespaceObject.__)("Convert the selected text into a link.")
  },
  {
    keyCombination: { modifier: "primaryShift", character: "k" },
    description: (0,external_wp_i18n_namespaceObject.__)("Remove a link.")
  },
  {
    keyCombination: { character: "[[" },
    description: (0,external_wp_i18n_namespaceObject.__)("Insert a link to a post or page.")
  },
  {
    keyCombination: { modifier: "primary", character: "u" },
    description: (0,external_wp_i18n_namespaceObject.__)("Underline the selected text.")
  },
  {
    keyCombination: { modifier: "access", character: "d" },
    description: (0,external_wp_i18n_namespaceObject.__)("Strikethrough the selected text.")
  },
  {
    keyCombination: { modifier: "access", character: "x" },
    description: (0,external_wp_i18n_namespaceObject.__)("Make the selected text inline code.")
  },
  {
    keyCombination: {
      modifier: "access",
      character: "0"
    },
    aliases: [
      {
        modifier: "access",
        character: "7"
      }
    ],
    description: (0,external_wp_i18n_namespaceObject.__)("Convert the current heading to a paragraph.")
  },
  {
    keyCombination: { modifier: "access", character: "1-6" },
    description: (0,external_wp_i18n_namespaceObject.__)(
      "Convert the current paragraph or heading to a heading of level 1 to 6."
    )
  },
  {
    keyCombination: { modifier: "primaryShift", character: "SPACE" },
    description: (0,external_wp_i18n_namespaceObject.__)("Add non breaking space.")
  }
];


;// ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcut-help-modal/shortcut.js



function KeyCombination({ keyCombination, forceAriaLabel }) {
  const shortcut = keyCombination.modifier ? external_wp_keycodes_namespaceObject.displayShortcutList[keyCombination.modifier](
    keyCombination.character
  ) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_wp_keycodes_namespaceObject.shortcutAriaLabel[keyCombination.modifier](
    keyCombination.character
  ) : keyCombination.character;
  const shortcuts = Array.isArray(shortcut) ? shortcut : [shortcut];
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    "kbd",
    {
      className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-key-combination",
      "aria-label": forceAriaLabel || ariaLabel,
      children: shortcuts.map((character, index) => {
        if (character === "+") {
          return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_element_namespaceObject.Fragment, { children: character }, index);
        }
        return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          "kbd",
          {
            className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-key",
            children: character
          },
          index
        );
      })
    }
  );
}
function Shortcut({ description, keyCombination, aliases = [], ariaLabel }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-description", children: description }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-term", children: [
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        KeyCombination,
        {
          keyCombination,
          forceAriaLabel: ariaLabel
        }
      ),
      aliases.map((alias, index) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        KeyCombination,
        {
          keyCombination: alias,
          forceAriaLabel: ariaLabel
        },
        index
      ))
    ] })
  ] });
}
var shortcut_default = Shortcut;


;// ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js




function DynamicShortcut({ name }) {
  const { keyCombination, description, aliases } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const {
        getShortcutKeyCombination,
        getShortcutDescription,
        getShortcutAliases
      } = select(external_wp_keyboardShortcuts_namespaceObject.store);
      return {
        keyCombination: getShortcutKeyCombination(name),
        aliases: getShortcutAliases(name),
        description: getShortcutDescription(name)
      };
    },
    [name]
  );
  if (!keyCombination) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    shortcut_default,
    {
      keyCombination,
      description,
      aliases
    }
  );
}
var dynamic_shortcut_default = DynamicShortcut;


;// ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcut-help-modal/index.js









const ShortcutList = ({ shortcuts }) => (
  /*
   * Disable reason: The `list` ARIA role is redundant but
   * Safari+VoiceOver won't announce the list otherwise.
   */
  /* eslint-disable jsx-a11y/no-redundant-roles */
  /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    "ul",
    {
      className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-list",
      role: "list",
      children: shortcuts.map((shortcut, index) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        "li",
        {
          className: "edit-widgets-keyboard-shortcut-help-modal__shortcut",
          children: typeof shortcut === "string" ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(dynamic_shortcut_default, { name: shortcut }) : /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(shortcut_default, { ...shortcut })
        },
        index
      ))
    }
  )
);
const ShortcutSection = ({ title, shortcuts, className }) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
  "section",
  {
    className: dist_clsx(
      "edit-widgets-keyboard-shortcut-help-modal__section",
      className
    ),
    children: [
      !!title && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h2", { className: "edit-widgets-keyboard-shortcut-help-modal__section-title", children: title }),
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(ShortcutList, { shortcuts })
    ]
  }
);
const ShortcutCategorySection = ({
  title,
  categoryName,
  additionalShortcuts = []
}) => {
  const categoryShortcuts = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      return select(external_wp_keyboardShortcuts_namespaceObject.store).getCategoryShortcuts(
        categoryName
      );
    },
    [categoryName]
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    ShortcutSection,
    {
      title,
      shortcuts: categoryShortcuts.concat(additionalShortcuts)
    }
  );
};
function KeyboardShortcutHelpModal({
  isModalActive,
  toggleModal
}) {
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)("core/edit-widgets/keyboard-shortcuts", toggleModal, {
    bindGlobal: true
  });
  if (!isModalActive) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
    external_wp_components_namespaceObject.Modal,
    {
      className: "edit-widgets-keyboard-shortcut-help-modal",
      title: (0,external_wp_i18n_namespaceObject.__)("Keyboard shortcuts"),
      onRequestClose: toggleModal,
      children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          ShortcutSection,
          {
            className: "edit-widgets-keyboard-shortcut-help-modal__main-shortcuts",
            shortcuts: ["core/edit-widgets/keyboard-shortcuts"]
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          ShortcutCategorySection,
          {
            title: (0,external_wp_i18n_namespaceObject.__)("Global shortcuts"),
            categoryName: "global"
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          ShortcutCategorySection,
          {
            title: (0,external_wp_i18n_namespaceObject.__)("Selection shortcuts"),
            categoryName: "selection"
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          ShortcutCategorySection,
          {
            title: (0,external_wp_i18n_namespaceObject.__)("Block shortcuts"),
            categoryName: "block",
            additionalShortcuts: [
              {
                keyCombination: { character: "/" },
                description: (0,external_wp_i18n_namespaceObject.__)(
                  "Change the block type after adding a new paragraph."
                ),
                /* translators: The forward-slash character. e.g. '/'. */
                ariaLabel: (0,external_wp_i18n_namespaceObject.__)("Forward-slash")
              }
            ]
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          ShortcutSection,
          {
            title: (0,external_wp_i18n_namespaceObject.__)("Text formatting"),
            shortcuts: textFormattingShortcuts
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          ShortcutCategorySection,
          {
            title: (0,external_wp_i18n_namespaceObject.__)("List View shortcuts"),
            categoryName: "list-view"
          }
        )
      ]
    }
  );
}


;// ./node_modules/@wordpress/edit-widgets/build-module/components/more-menu/tools-more-menu-group.js


const { Fill: ToolsMoreMenuGroup, Slot } = (0,external_wp_components_namespaceObject.createSlotFill)(
  "EditWidgetsToolsMoreMenuGroup"
);
ToolsMoreMenuGroup.Slot = ({ fillProps }) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Slot, { fillProps, children: (fills) => fills.length > 0 && fills });
var tools_more_menu_group_default = ToolsMoreMenuGroup;


;// ./node_modules/@wordpress/edit-widgets/build-module/components/more-menu/index.js











function MoreMenu() {
  const [
    isKeyboardShortcutsModalActive,
    setIsKeyboardShortcutsModalVisible
  ] = (0,external_wp_element_namespaceObject.useState)(false);
  const toggleKeyboardShortcutsModal = () => setIsKeyboardShortcutsModalVisible(!isKeyboardShortcutsModalActive);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)(
    "core/edit-widgets/keyboard-shortcuts",
    toggleKeyboardShortcutsModal
  );
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.DropdownMenu,
      {
        icon: more_vertical_default,
        label: (0,external_wp_i18n_namespaceObject.__)("Options"),
        popoverProps: {
          placement: "bottom-end",
          className: "more-menu-dropdown__content"
        },
        toggleProps: {
          tooltipPosition: "bottom",
          size: "compact"
        },
        children: (onClose) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
          isLargeViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuGroup, { label: (0,external_wp_i18n_namespaceObject._x)("View", "noun"), children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_preferences_namespaceObject.PreferenceToggleMenuItem,
            {
              scope: "core/edit-widgets",
              name: "fixedToolbar",
              label: (0,external_wp_i18n_namespaceObject.__)("Top toolbar"),
              info: (0,external_wp_i18n_namespaceObject.__)(
                "Access all block and document tools in a single place"
              ),
              messageActivated: (0,external_wp_i18n_namespaceObject.__)(
                "Top toolbar activated"
              ),
              messageDeactivated: (0,external_wp_i18n_namespaceObject.__)(
                "Top toolbar deactivated"
              )
            }
          ) }),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.MenuGroup, { label: (0,external_wp_i18n_namespaceObject.__)("Tools"), children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_components_namespaceObject.MenuItem,
              {
                onClick: () => {
                  setIsKeyboardShortcutsModalVisible(true);
                },
                shortcut: external_wp_keycodes_namespaceObject.displayShortcut.access("h"),
                children: (0,external_wp_i18n_namespaceObject.__)("Keyboard shortcuts")
              }
            ),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_preferences_namespaceObject.PreferenceToggleMenuItem,
              {
                scope: "core/edit-widgets",
                name: "welcomeGuide",
                label: (0,external_wp_i18n_namespaceObject.__)("Welcome Guide")
              }
            ),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
              external_wp_components_namespaceObject.MenuItem,
              {
                role: "menuitem",
                icon: external_default,
                href: (0,external_wp_i18n_namespaceObject.__)(
                  "https://wordpress.org/documentation/article/block-based-widgets-editor/"
                ),
                target: "_blank",
                rel: "noopener noreferrer",
                children: [
                  (0,external_wp_i18n_namespaceObject.__)("Help"),
                  /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.VisuallyHidden, {
                    as: "span",
                    /* translators: accessibility text */
                    children: (0,external_wp_i18n_namespaceObject.__)("(opens in a new tab)")
                  })
                ]
              }
            ),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              tools_more_menu_group_default.Slot,
              {
                fillProps: { onClose }
              }
            )
          ] }),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.MenuGroup, { label: (0,external_wp_i18n_namespaceObject.__)("Preferences"), children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_preferences_namespaceObject.PreferenceToggleMenuItem,
              {
                scope: "core/edit-widgets",
                name: "keepCaretInsideBlock",
                label: (0,external_wp_i18n_namespaceObject.__)(
                  "Contain text cursor inside block"
                ),
                info: (0,external_wp_i18n_namespaceObject.__)(
                  "Aids screen readers by stopping text caret from leaving blocks."
                ),
                messageActivated: (0,external_wp_i18n_namespaceObject.__)(
                  "Contain text cursor inside block activated"
                ),
                messageDeactivated: (0,external_wp_i18n_namespaceObject.__)(
                  "Contain text cursor inside block deactivated"
                )
              }
            ),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_preferences_namespaceObject.PreferenceToggleMenuItem,
              {
                scope: "core/edit-widgets",
                name: "themeStyles",
                info: (0,external_wp_i18n_namespaceObject.__)(
                  "Make the editor look like your theme."
                ),
                label: (0,external_wp_i18n_namespaceObject.__)("Use theme styles")
              }
            ),
            isLargeViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_preferences_namespaceObject.PreferenceToggleMenuItem,
              {
                scope: "core/edit-widgets",
                name: "showBlockBreadcrumbs",
                label: (0,external_wp_i18n_namespaceObject.__)("Display block breadcrumbs"),
                info: (0,external_wp_i18n_namespaceObject.__)(
                  "Shows block breadcrumbs at the bottom of the editor."
                ),
                messageActivated: (0,external_wp_i18n_namespaceObject.__)(
                  "Display block breadcrumbs activated"
                ),
                messageDeactivated: (0,external_wp_i18n_namespaceObject.__)(
                  "Display block breadcrumbs deactivated"
                )
              }
            )
          ] })
        ] })
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      KeyboardShortcutHelpModal,
      {
        isModalActive: isKeyboardShortcutsModalActive,
        toggleModal: toggleKeyboardShortcutsModal
      }
    )
  ] });
}


;// ./node_modules/@wordpress/edit-widgets/build-module/components/header/index.js












function Header() {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium");
  const blockToolbarRef = (0,external_wp_element_namespaceObject.useRef)();
  const { hasFixedToolbar } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => ({
      hasFixedToolbar: !!select(external_wp_preferences_namespaceObject.store).get(
        "core/edit-widgets",
        "fixedToolbar"
      )
    }),
    []
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "edit-widgets-header", children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "edit-widgets-header__navigable-toolbar-wrapper", children: [
      isLargeViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-widgets-header__title", children: (0,external_wp_i18n_namespaceObject.__)("Widgets") }),
      !isLargeViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        external_wp_components_namespaceObject.VisuallyHidden,
        {
          as: "h1",
          className: "edit-widgets-header__title",
          children: (0,external_wp_i18n_namespaceObject.__)("Widgets")
        }
      ),
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(document_tools_default, {}),
      hasFixedToolbar && isLargeViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "selected-block-tools-wrapper", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockToolbar, { hideDragHandle: true }) }),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.Popover.Slot,
          {
            ref: blockToolbarRef,
            name: "block-toolbar"
          }
        )
      ] })
    ] }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "edit-widgets-header__actions", children: [
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(pinned_items_default.Slot, { scope: "core/edit-widgets" }),
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(save_button_default, {}),
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(MoreMenu, {})
    ] })
  ] }) });
}
var header_default = Header;


;// ./node_modules/@wordpress/edit-widgets/build-module/components/notices/index.js




const MAX_VISIBLE_NOTICES = -3;
function Notices() {
  const { removeNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const { notices } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    return {
      notices: select(external_wp_notices_namespaceObject.store).getNotices()
    };
  }, []);
  const dismissibleNotices = notices.filter(
    ({ isDismissible, type }) => isDismissible && type === "default"
  );
  const nonDismissibleNotices = notices.filter(
    ({ isDismissible, type }) => !isDismissible && type === "default"
  );
  const snackbarNotices = notices.filter(({ type }) => type === "snackbar").slice(MAX_VISIBLE_NOTICES);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.NoticeList,
      {
        notices: nonDismissibleNotices,
        className: "edit-widgets-notices__pinned"
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.NoticeList,
      {
        notices: dismissibleNotices,
        className: "edit-widgets-notices__dismissible",
        onRemove: removeNotice
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.SnackbarList,
      {
        notices: snackbarNotices,
        className: "edit-widgets-notices__snackbar",
        onRemove: removeNotice
      }
    )
  ] });
}
var notices_default = Notices;


;// ./node_modules/@wordpress/edit-widgets/build-module/components/widget-areas-block-editor-content/index.js








function WidgetAreasBlockEditorContent({
  blockEditorSettings
}) {
  const hasThemeStyles = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => !!select(external_wp_preferences_namespaceObject.store).get(
      "core/edit-widgets",
      "themeStyles"
    ),
    []
  );
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium");
  const styles = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return hasThemeStyles ? blockEditorSettings.styles : [];
  }, [blockEditorSettings, hasThemeStyles]);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "edit-widgets-block-editor", children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(notices_default, {}),
    !isLargeViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockToolbar, { hideDragHandle: true }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_blockEditor_namespaceObject.BlockTools, { children: [
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(keyboard_shortcuts_default, {}),
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        external_wp_blockEditor_namespaceObject.__unstableEditorStyles,
        {
          styles,
          scope: ":where(.editor-styles-wrapper)"
        }
      ),
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockSelectionClearer, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.WritingFlow, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockList, { className: "edit-widgets-main-block-list" }) }) })
    ] })
  ] });
}


;// ./node_modules/@wordpress/edit-widgets/build-module/hooks/use-widget-library-insertion-point.js





const useWidgetLibraryInsertionPoint = () => {
  const firstRootId = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getEntityRecord } = select(external_wp_coreData_namespaceObject.store);
    const widgetAreasPost = getEntityRecord(
      KIND,
      POST_TYPE,
      buildWidgetAreasPostId()
    );
    return widgetAreasPost?.blocks[0]?.clientId;
  }, []);
  return (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const {
        getBlockRootClientId,
        getBlockSelectionEnd,
        getBlockOrder,
        getBlockIndex
      } = select(external_wp_blockEditor_namespaceObject.store);
      const insertionPoint = select(store_store).__experimentalGetInsertionPoint();
      if (insertionPoint.rootClientId) {
        return insertionPoint;
      }
      const clientId = getBlockSelectionEnd() || firstRootId;
      const rootClientId = getBlockRootClientId(clientId);
      if (clientId && rootClientId === "") {
        return {
          rootClientId: clientId,
          insertionIndex: getBlockOrder(clientId).length
        };
      }
      return {
        rootClientId,
        insertionIndex: getBlockIndex(clientId) + 1
      };
    },
    [firstRootId]
  );
};
var use_widget_library_insertion_point_default = useWidgetLibraryInsertionPoint;


;// ./node_modules/@wordpress/edit-widgets/build-module/components/secondary-sidebar/inserter-sidebar.js







function InserterSidebar() {
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium", "<");
  const { rootClientId, insertionIndex } = use_widget_library_insertion_point_default();
  const { setIsInserterOpened } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const closeInserter = (0,external_wp_element_namespaceObject.useCallback)(() => {
    return setIsInserterOpened(false);
  }, [setIsInserterOpened]);
  const libraryRef = (0,external_wp_element_namespaceObject.useRef)();
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "edit-widgets-layout__inserter-panel", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "edit-widgets-layout__inserter-panel-content", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_blockEditor_namespaceObject.__experimentalLibrary,
    {
      showInserterHelpPanel: true,
      shouldFocusBlock: isMobileViewport,
      rootClientId,
      __experimentalInsertionIndex: insertionIndex,
      ref: libraryRef,
      onClose: closeInserter
    }
  ) }) });
}


;// ./node_modules/@wordpress/edit-widgets/build-module/components/secondary-sidebar/list-view-sidebar.js











function ListViewSidebar() {
  const { setIsListViewOpened } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const { getListViewToggleRef } = unlock((0,external_wp_data_namespaceObject.useSelect)(store_store));
  const [dropZoneElement, setDropZoneElement] = (0,external_wp_element_namespaceObject.useState)(null);
  const focusOnMountRef = (0,external_wp_compose_namespaceObject.useFocusOnMount)("firstElement");
  const closeListView = (0,external_wp_element_namespaceObject.useCallback)(() => {
    setIsListViewOpened(false);
    getListViewToggleRef().current?.focus();
  }, [getListViewToggleRef, setIsListViewOpened]);
  const closeOnEscape = (0,external_wp_element_namespaceObject.useCallback)(
    (event) => {
      if (event.keyCode === external_wp_keycodes_namespaceObject.ESCAPE && !event.defaultPrevented) {
        event.preventDefault();
        closeListView();
      }
    },
    [closeListView]
  );
  return (
    // eslint-disable-next-line jsx-a11y/no-static-element-interactions
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
      "div",
      {
        className: "edit-widgets-editor__list-view-panel",
        onKeyDown: closeOnEscape,
        children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "edit-widgets-editor__list-view-panel-header", children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("strong", { children: (0,external_wp_i18n_namespaceObject.__)("List View") }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_components_namespaceObject.Button,
              {
                icon: close_small_default,
                label: (0,external_wp_i18n_namespaceObject.__)("Close"),
                onClick: closeListView,
                size: "compact"
              }
            )
          ] }),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            "div",
            {
              className: "edit-widgets-editor__list-view-panel-content",
              ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([focusOnMountRef, setDropZoneElement]),
              children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.__experimentalListView, { dropZoneElement })
            }
          )
        ]
      }
    )
  );
}


;// ./node_modules/@wordpress/edit-widgets/build-module/components/secondary-sidebar/index.js





function SecondarySidebar() {
  const { isInserterOpen, isListViewOpen } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { isInserterOpened, isListViewOpened } = select(store_store);
    return {
      isInserterOpen: isInserterOpened(),
      isListViewOpen: isListViewOpened()
    };
  }, []);
  if (isInserterOpen) {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(InserterSidebar, {});
  }
  if (isListViewOpen) {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(ListViewSidebar, {});
  }
  return null;
}


;// ./node_modules/@wordpress/edit-widgets/build-module/components/layout/interface.js












const interfaceLabels = {
  /* translators: accessibility text for the widgets screen top bar landmark region. */
  header: (0,external_wp_i18n_namespaceObject.__)("Widgets top bar"),
  /* translators: accessibility text for the widgets screen content landmark region. */
  body: (0,external_wp_i18n_namespaceObject.__)("Widgets and blocks"),
  /* translators: accessibility text for the widgets screen settings landmark region. */
  sidebar: (0,external_wp_i18n_namespaceObject.__)("Widgets settings"),
  /* translators: accessibility text for the widgets screen footer landmark region. */
  footer: (0,external_wp_i18n_namespaceObject.__)("Widgets footer")
};
function Interface({ blockEditorSettings }) {
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium", "<");
  const isHugeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("huge", ">=");
  const { setIsInserterOpened, setIsListViewOpened, closeGeneralSidebar } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    hasBlockBreadCrumbsEnabled,
    hasSidebarEnabled,
    isInserterOpened,
    isListViewOpened
  } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => ({
      hasSidebarEnabled: !!select(
        store
      ).getActiveComplementaryArea(store_store.name),
      isInserterOpened: !!select(store_store).isInserterOpened(),
      isListViewOpened: !!select(store_store).isListViewOpened(),
      hasBlockBreadCrumbsEnabled: !!select(external_wp_preferences_namespaceObject.store).get(
        "core/edit-widgets",
        "showBlockBreadcrumbs"
      )
    }),
    []
  );
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (hasSidebarEnabled && !isHugeViewport) {
      setIsInserterOpened(false);
      setIsListViewOpened(false);
    }
  }, [hasSidebarEnabled, isHugeViewport]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if ((isInserterOpened || isListViewOpened) && !isHugeViewport) {
      closeGeneralSidebar();
    }
  }, [isInserterOpened, isListViewOpened, isHugeViewport]);
  const secondarySidebarLabel = isListViewOpened ? (0,external_wp_i18n_namespaceObject.__)("List View") : (0,external_wp_i18n_namespaceObject.__)("Block Library");
  const hasSecondarySidebar = isListViewOpened || isInserterOpened;
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    interface_skeleton_default,
    {
      labels: {
        ...interfaceLabels,
        secondarySidebar: secondarySidebarLabel
      },
      header: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(header_default, {}),
      secondarySidebar: hasSecondarySidebar && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(SecondarySidebar, {}),
      sidebar: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(complementary_area_default.Slot, { scope: "core/edit-widgets" }),
      content: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        WidgetAreasBlockEditorContent,
        {
          blockEditorSettings
        }
      ) }),
      footer: hasBlockBreadCrumbsEnabled && !isMobileViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "edit-widgets-layout__footer", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockBreadcrumb, { rootLabelText: (0,external_wp_i18n_namespaceObject.__)("Widgets") }) })
    }
  );
}
var interface_default = Interface;


;// ./node_modules/@wordpress/edit-widgets/build-module/components/layout/unsaved-changes-warning.js




function UnsavedChangesWarning() {
  const isDirty = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getEditedWidgetAreas } = select(store_store);
    const editedWidgetAreas = getEditedWidgetAreas();
    return editedWidgetAreas?.length > 0;
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const warnIfUnsavedChanges = (event) => {
      if (isDirty) {
        event.returnValue = (0,external_wp_i18n_namespaceObject.__)(
          "You have unsaved changes. If you proceed, they will be lost."
        );
        return event.returnValue;
      }
    };
    window.addEventListener("beforeunload", warnIfUnsavedChanges);
    return () => {
      window.removeEventListener("beforeunload", warnIfUnsavedChanges);
    };
  }, [isDirty]);
  return null;
}


;// ./node_modules/@wordpress/edit-widgets/build-module/components/welcome-guide/index.js







function WelcomeGuide() {
  const isActive = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => !!select(external_wp_preferences_namespaceObject.store).get(
      "core/edit-widgets",
      "welcomeGuide"
    ),
    []
  );
  const { toggle } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const widgetAreas = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(store_store).getWidgetAreas({ per_page: -1 }),
    []
  );
  if (!isActive) {
    return null;
  }
  const isEntirelyBlockWidgets = widgetAreas?.every(
    (widgetArea) => widgetArea.id === "wp_inactive_widgets" || widgetArea.widgets.every(
      (widgetId) => widgetId.startsWith("block-")
    )
  );
  const numWidgetAreas = widgetAreas?.filter(
    (widgetArea) => widgetArea.id !== "wp_inactive_widgets"
  ).length ?? 0;
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Guide,
    {
      className: "edit-widgets-welcome-guide",
      contentLabel: (0,external_wp_i18n_namespaceObject.__)("Welcome to block Widgets"),
      finishButtonText: (0,external_wp_i18n_namespaceObject.__)("Get started"),
      onFinish: () => toggle("core/edit-widgets", "welcomeGuide"),
      pages: [
        {
          image: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            WelcomeGuideImage,
            {
              nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.svg",
              animatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.gif"
            }
          ),
          content: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-widgets-welcome-guide__heading", children: (0,external_wp_i18n_namespaceObject.__)("Welcome to block Widgets") }),
            isEntirelyBlockWidgets ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0,external_wp_i18n_namespaceObject.sprintf)(
              // Translators: %s: Number of block areas in the current theme.
              (0,external_wp_i18n_namespaceObject._n)(
                "Your theme provides %s \u201Cblock\u201D area for you to add and edit content.\xA0Try adding a search bar, social icons, or other types of blocks here and see how they\u2019ll look on your site.",
                "Your theme provides %s different \u201Cblock\u201D areas for you to add and edit content.\xA0Try adding a search bar, social icons, or other types of blocks here and see how they\u2019ll look on your site.",
                numWidgetAreas
              ),
              numWidgetAreas
            ) }) }) : /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0,external_wp_i18n_namespaceObject.__)(
                "You can now add any block to your site\u2019s widget areas. Don\u2019t worry, all of your favorite widgets still work flawlessly."
              ) }),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("p", { className: "edit-widgets-welcome-guide__text", children: [
                /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("strong", { children: (0,external_wp_i18n_namespaceObject.__)(
                  "Want to stick with the old widgets?"
                ) }),
                " ",
                /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                  external_wp_components_namespaceObject.ExternalLink,
                  {
                    href: (0,external_wp_i18n_namespaceObject.__)(
                      "https://wordpress.org/plugins/classic-widgets/"
                    ),
                    children: (0,external_wp_i18n_namespaceObject.__)(
                      "Get the Classic Widgets plugin."
                    )
                  }
                )
              ] })
            ] })
          ] })
        },
        {
          image: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            WelcomeGuideImage,
            {
              nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-editor.svg",
              animatedSrc: "https://s.w.org/images/block-editor/welcome-editor.gif"
            }
          ),
          content: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-widgets-welcome-guide__heading", children: (0,external_wp_i18n_namespaceObject.__)("Customize each block") }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0,external_wp_i18n_namespaceObject.__)(
              "Each block comes with its own set of controls for changing things like color, width, and alignment. These will show and hide automatically when you have a block selected."
            ) })
          ] })
        },
        {
          image: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            WelcomeGuideImage,
            {
              nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-library.svg",
              animatedSrc: "https://s.w.org/images/block-editor/welcome-library.gif"
            }
          ),
          content: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-widgets-welcome-guide__heading", children: (0,external_wp_i18n_namespaceObject.__)("Explore all blocks") }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0,external_wp_element_namespaceObject.createInterpolateElement)(
              (0,external_wp_i18n_namespaceObject.__)(
                "All of the blocks available to you live in the block library. You\u2019ll find it wherever you see the <InserterIconImage /> icon."
              ),
              {
                InserterIconImage: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                  "img",
                  {
                    className: "edit-widgets-welcome-guide__inserter-icon",
                    alt: (0,external_wp_i18n_namespaceObject.__)("inserter"),
                    src: "data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 18 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='18' height='18' rx='2' fill='%231E1E1E'/%3E%3Cpath d='M9.22727 4V14M4 8.77273H14' stroke='white' stroke-width='1.5'/%3E%3C/svg%3E%0A"
                  }
                )
              }
            ) })
          ] })
        },
        {
          image: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            WelcomeGuideImage,
            {
              nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.svg",
              animatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.gif"
            }
          ),
          content: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-widgets-welcome-guide__heading", children: (0,external_wp_i18n_namespaceObject.__)("Learn more") }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0,external_wp_element_namespaceObject.createInterpolateElement)(
              (0,external_wp_i18n_namespaceObject.__)(
                "New to the block editor? Want to learn more about using it? <a>Here's a detailed guide.</a>"
              ),
              {
                a: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                  external_wp_components_namespaceObject.ExternalLink,
                  {
                    href: (0,external_wp_i18n_namespaceObject.__)(
                      "https://wordpress.org/documentation/article/wordpress-block-editor/"
                    )
                  }
                )
              }
            ) })
          ] })
        }
      ]
    }
  );
}
function WelcomeGuideImage({ nonAnimatedSrc, animatedSrc }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("picture", { className: "edit-widgets-welcome-guide__image", children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      "source",
      {
        srcSet: nonAnimatedSrc,
        media: "(prefers-reduced-motion: reduce)"
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("img", { src: animatedSrc, width: "312", height: "240", alt: "" })
  ] });
}


;// ./node_modules/@wordpress/edit-widgets/build-module/components/layout/index.js












function Layout({ blockEditorSettings }) {
  const { createErrorNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  function onPluginAreaError(name) {
    createErrorNotice(
      (0,external_wp_i18n_namespaceObject.sprintf)(
        /* translators: %s: plugin name */
        (0,external_wp_i18n_namespaceObject.__)(
          'The "%s" plugin has encountered an error and cannot be rendered.'
        ),
        name
      )
    );
  }
  const navigateRegionsProps = (0,external_wp_components_namespaceObject.__unstableUseNavigateRegions)();
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(ErrorBoundary, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    "div",
    {
      className: navigateRegionsProps.className,
      ...navigateRegionsProps,
      ref: navigateRegionsProps.ref,
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
        WidgetAreasBlockEditorProvider,
        {
          blockEditorSettings,
          children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(interface_default, { blockEditorSettings }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Sidebar, {}),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_plugins_namespaceObject.PluginArea, { onError: onPluginAreaError }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(UnsavedChangesWarning, {}),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuide, {})
          ]
        }
      )
    }
  ) });
}
var layout_default = Layout;


;// ./node_modules/@wordpress/edit-widgets/build-module/index.js














const disabledBlocks = [
  "core/more",
  "core/freeform",
  "core/template-part",
  ...ALLOW_REUSABLE_BLOCKS ? [] : ["core/block"]
];
function initializeEditor(id, settings) {
  const target = document.getElementById(id);
  const root = (0,external_wp_element_namespaceObject.createRoot)(target);
  const coreBlocks = (0,external_wp_blockLibrary_namespaceObject.__experimentalGetCoreBlocks)().filter((block) => {
    return !(disabledBlocks.includes(block.name) || block.name.startsWith("core/post") || block.name.startsWith("core/query") || block.name.startsWith("core/site") || block.name.startsWith("core/navigation"));
  });
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults("core/edit-widgets", {
    fixedToolbar: false,
    welcomeGuide: true,
    showBlockBreadcrumbs: true,
    themeStyles: true
  });
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).reapplyBlockTypeFilters();
  (0,external_wp_blockLibrary_namespaceObject.registerCoreBlocks)(coreBlocks);
  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetBlock)();
  if (false) {}
  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetVariations)(settings);
  registerBlock(widget_area_namespaceObject);
  (0,external_wp_widgets_namespaceObject.registerWidgetGroupBlock)();
  settings.__experimentalFetchLinkSuggestions = (search, searchOptions) => (0,external_wp_coreData_namespaceObject.__experimentalFetchLinkSuggestions)(search, searchOptions, settings);
  (0,external_wp_blocks_namespaceObject.setFreeformContentHandlerName)("core/html");
  root.render(
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_element_namespaceObject.StrictMode, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(layout_default, { blockEditorSettings: settings }) })
  );
  return root;
}
const initialize = initializeEditor;
function reinitializeEditor() {
  external_wp_deprecated_default()("wp.editWidgets.reinitializeEditor", {
    since: "6.2",
    version: "6.3"
  });
}
const registerBlock = (block) => {
  if (!block) {
    return;
  }
  const { metadata, settings, name } = block;
  if (metadata) {
    (0,external_wp_blocks_namespaceObject.unstable__bootstrapServerSideBlockDefinitions)({ [name]: metadata });
  }
  (0,external_wp_blocks_namespaceObject.registerBlockType)(name, settings);
};



(window.wp = window.wp || {}).editWidgets = __webpack_exports__;
/******/ })()
;