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
  PluginBlockSettingsMenuItem: () => (/* reexport */ PluginBlockSettingsMenuItem),
  PluginDocumentSettingPanel: () => (/* reexport */ PluginDocumentSettingPanel),
  PluginMoreMenuItem: () => (/* reexport */ PluginMoreMenuItem),
  PluginPostPublishPanel: () => (/* reexport */ PluginPostPublishPanel),
  PluginPostStatusInfo: () => (/* reexport */ PluginPostStatusInfo),
  PluginPrePublishPanel: () => (/* reexport */ PluginPrePublishPanel),
  PluginSidebar: () => (/* reexport */ PluginSidebar),
  PluginSidebarMoreMenuItem: () => (/* reexport */ PluginSidebarMoreMenuItem),
  __experimentalFullscreenModeClose: () => (/* reexport */ fullscreen_mode_close_default),
  __experimentalMainDashboardButton: () => (/* binding */ __experimentalMainDashboardButton),
  __experimentalPluginPostExcerpt: () => (/* reexport */ __experimentalPluginPostExcerpt),
  initializeEditor: () => (/* binding */ initializeEditor),
  reinitializeEditor: () => (/* binding */ reinitializeEditor),
  store: () => (/* reexport */ store)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  __experimentalSetPreviewDeviceType: () => (__experimentalSetPreviewDeviceType),
  __unstableCreateTemplate: () => (__unstableCreateTemplate),
  closeGeneralSidebar: () => (closeGeneralSidebar),
  closeModal: () => (closeModal),
  closePublishSidebar: () => (closePublishSidebar),
  hideBlockTypes: () => (hideBlockTypes),
  initializeMetaBoxes: () => (initializeMetaBoxes),
  metaBoxUpdatesFailure: () => (metaBoxUpdatesFailure),
  metaBoxUpdatesSuccess: () => (metaBoxUpdatesSuccess),
  openGeneralSidebar: () => (openGeneralSidebar),
  openModal: () => (openModal),
  openPublishSidebar: () => (openPublishSidebar),
  removeEditorPanel: () => (removeEditorPanel),
  requestMetaBoxUpdates: () => (requestMetaBoxUpdates),
  setAvailableMetaBoxesPerLocation: () => (setAvailableMetaBoxesPerLocation),
  setIsEditingTemplate: () => (setIsEditingTemplate),
  setIsInserterOpened: () => (setIsInserterOpened),
  setIsListViewOpened: () => (setIsListViewOpened),
  showBlockTypes: () => (showBlockTypes),
  switchEditorMode: () => (switchEditorMode),
  toggleDistractionFree: () => (toggleDistractionFree),
  toggleEditorPanelEnabled: () => (toggleEditorPanelEnabled),
  toggleEditorPanelOpened: () => (toggleEditorPanelOpened),
  toggleFeature: () => (toggleFeature),
  toggleFullscreenMode: () => (toggleFullscreenMode),
  togglePinnedPluginItem: () => (togglePinnedPluginItem),
  togglePublishSidebar: () => (togglePublishSidebar),
  updatePreferredStyleVariations: () => (updatePreferredStyleVariations)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  __experimentalGetInsertionPoint: () => (__experimentalGetInsertionPoint),
  __experimentalGetPreviewDeviceType: () => (__experimentalGetPreviewDeviceType),
  areMetaBoxesInitialized: () => (areMetaBoxesInitialized),
  getActiveGeneralSidebarName: () => (getActiveGeneralSidebarName),
  getActiveMetaBoxLocations: () => (getActiveMetaBoxLocations),
  getAllMetaBoxes: () => (getAllMetaBoxes),
  getEditedPostTemplate: () => (getEditedPostTemplate),
  getEditorMode: () => (getEditorMode),
  getHiddenBlockTypes: () => (getHiddenBlockTypes),
  getMetaBoxesPerLocation: () => (getMetaBoxesPerLocation),
  getPreference: () => (getPreference),
  getPreferences: () => (getPreferences),
  hasMetaBoxes: () => (hasMetaBoxes),
  isEditingTemplate: () => (isEditingTemplate),
  isEditorPanelEnabled: () => (isEditorPanelEnabled),
  isEditorPanelOpened: () => (isEditorPanelOpened),
  isEditorPanelRemoved: () => (isEditorPanelRemoved),
  isEditorSidebarOpened: () => (isEditorSidebarOpened),
  isFeatureActive: () => (isFeatureActive),
  isInserterOpened: () => (isInserterOpened),
  isListViewOpened: () => (isListViewOpened),
  isMetaBoxLocationActive: () => (isMetaBoxLocationActive),
  isMetaBoxLocationVisible: () => (isMetaBoxLocationVisible),
  isModalActive: () => (isModalActive),
  isPluginItemPinned: () => (isPluginItemPinned),
  isPluginSidebarOpened: () => (isPluginSidebarOpened),
  isPublishSidebarOpened: () => (isPublishSidebarOpened),
  isSavingMetaBoxes: () => (selectors_isSavingMetaBoxes)
});

;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// external ["wp","blockLibrary"]
const external_wp_blockLibrary_namespaceObject = window["wp"]["blockLibrary"];
;// external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// external ["wp","preferences"]
const external_wp_preferences_namespaceObject = window["wp"]["preferences"];
;// external ["wp","widgets"]
const external_wp_widgets_namespaceObject = window["wp"]["widgets"];
;// external ["wp","editor"]
const external_wp_editor_namespaceObject = window["wp"]["editor"];
;// ./node_modules/clsx/dist/clsx.mjs
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const dist_clsx = (clsx);
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


;// external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// external ["wp","plugins"]
const external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// ./node_modules/@wordpress/icons/build-module/library/chevron-up.js


var chevron_up_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/chevron-down.js


var chevron_down_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z" }) });


;// external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
;// external ["wp","commands"]
const external_wp_commands_namespaceObject = window["wp"]["commands"];
;// external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// external ["wp","htmlEntities"]
const external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// ./node_modules/@wordpress/icons/build-module/library/wordpress.js


var wordpress_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "-2 -2 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/arrow-up-left.js


var arrow_up_left_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M14 6H6v8h1.5V8.5L17 18l1-1-9.5-9.5H14V6Z" }) });


;// ./node_modules/@wordpress/edit-post/build-module/components/back-button/fullscreen-mode-close.js










const siteIconVariants = {
  edit: {
    clipPath: "inset(0% round 0px)"
  },
  hover: {
    clipPath: "inset( 22% round 2px )"
  },
  tap: {
    clipPath: "inset(0% round 0px)"
  }
};
const toggleHomeIconVariants = {
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
function FullscreenModeClose({ showTooltip, icon, href, initialPost }) {
  const { isRequestingSiteIcon, postType, siteIconUrl } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { getCurrentPostType } = select(external_wp_editor_namespaceObject.store);
      const { getEntityRecord, getPostType, isResolving } = select(external_wp_coreData_namespaceObject.store);
      const siteData = getEntityRecord("root", "__unstableBase", void 0) || {};
      const _postType = initialPost?.type || getCurrentPostType();
      return {
        isRequestingSiteIcon: isResolving("getEntityRecord", [
          "root",
          "__unstableBase",
          void 0
        ]),
        postType: getPostType(_postType),
        siteIconUrl: siteData.site_icon_url
      };
    },
    [initialPost?.type]
  );
  const disableMotion = (0,external_wp_compose_namespaceObject.useReducedMotion)();
  const transition = {
    duration: disableMotion ? 0 : 0.2
  };
  if (!postType) {
    return null;
  }
  let siteIconContent;
  if (isRequestingSiteIcon && !siteIconUrl) {
    siteIconContent = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "edit-post-fullscreen-mode-close-site-icon__image" });
  } else if (siteIconUrl) {
    siteIconContent = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      "img",
      {
        className: "edit-post-fullscreen-mode-close-site-icon__image",
        alt: (0,external_wp_i18n_namespaceObject.__)("Site Icon"),
        src: siteIconUrl
      }
    );
  } else {
    siteIconContent = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.Icon,
      {
        className: "edit-post-fullscreen-mode-close-site-icon__icon",
        icon: wordpress_default,
        size: 48
      }
    );
  }
  const buttonIcon = icon ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Icon, { size: "36px", icon }) : /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "edit-post-fullscreen-mode-close-site-icon", children: siteIconContent });
  const classes = dist_clsx("edit-post-fullscreen-mode-close", {
    "has-icon": siteIconUrl
  });
  const buttonHref = href ?? (0,external_wp_url_namespaceObject.addQueryArgs)("edit.php", {
    post_type: postType.slug
  });
  const buttonLabel = postType?.labels?.view_items ?? (0,external_wp_i18n_namespaceObject.__)("Back");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
    external_wp_components_namespaceObject.__unstableMotion.div,
    {
      className: "edit-post-fullscreen-mode-close__view-mode-toggle",
      animate: "edit",
      initial: "edit",
      whileHover: "hover",
      whileTap: "tap",
      transition,
      children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.Button,
          {
            __next40pxDefaultSize: true,
            className: classes,
            href: buttonHref,
            label: buttonLabel,
            showTooltip,
            tooltipPosition: "middle right",
            children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__unstableMotion.div, { variants: !disableMotion && siteIconVariants, children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "edit-post-fullscreen-mode-close__view-mode-toggle-icon", children: buttonIcon }) })
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.__unstableMotion.div,
          {
            className: dist_clsx(
              "edit-post-fullscreen-mode-close__back-icon",
              {
                "has-site-icon": siteIconUrl
              }
            ),
            variants: !disableMotion && toggleHomeIconVariants,
            children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Icon, { icon: arrow_up_left_default })
          }
        )
      ]
    }
  );
}
var fullscreen_mode_close_default = FullscreenModeClose;


;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/edit-post/build-module/lock-unlock.js

const { lock, unlock } = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/edit-post"
);


;// ./node_modules/@wordpress/edit-post/build-module/components/back-button/index.js





const { BackButton: BackButtonFill } = unlock(external_wp_editor_namespaceObject.privateApis);
const slideX = {
  hidden: { x: "-100%" },
  distractionFreeInactive: { x: 0 },
  hover: { x: 0, transition: { type: "tween", delay: 0.2 } }
};
function BackButton({ initialPost }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(BackButtonFill, { children: ({ length }) => length <= 1 && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.__unstableMotion.div,
    {
      variants: slideX,
      transition: { type: "tween", delay: 0.8 },
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        fullscreen_mode_close_default,
        {
          showTooltip: true,
          initialPost
        }
      )
    }
  ) });
}
var back_button_default = BackButton;


;// ./node_modules/@wordpress/edit-post/build-module/store/constants.js
const STORE_NAME = "core/edit-post";
const VIEW_AS_LINK_SELECTOR = "#wp-admin-bar-view a";
const VIEW_AS_PREVIEW_LINK_SELECTOR = "#wp-admin-bar-preview a";


;// ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/listener-hooks.js





const useUpdatePostLinkListener = () => {
  const { isViewable, newPermalink } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getPostType } = select(external_wp_coreData_namespaceObject.store);
    const { getCurrentPost, getEditedPostAttribute } = select(external_wp_editor_namespaceObject.store);
    const postType = getPostType(getEditedPostAttribute("type"));
    return {
      isViewable: postType?.viewable,
      newPermalink: getCurrentPost().link
    };
  }, []);
  const nodeToUpdateRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    nodeToUpdateRef.current = document.querySelector(VIEW_AS_PREVIEW_LINK_SELECTOR) || document.querySelector(VIEW_AS_LINK_SELECTOR);
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!newPermalink || !nodeToUpdateRef.current) {
      return;
    }
    if (!isViewable) {
      nodeToUpdateRef.current.style.display = "none";
      return;
    }
    nodeToUpdateRef.current.style.display = "";
    nodeToUpdateRef.current.setAttribute("href", newPermalink);
  }, [newPermalink, isViewable]);
};


;// ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/index.js

function EditorInitialization() {
  useUpdatePostLinkListener();
  return null;
}


;// external ["wp","keyboardShortcuts"]
const external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
;// ./node_modules/@wordpress/edit-post/build-module/store/reducer.js

function isSavingMetaBoxes(state = false, action) {
  switch (action.type) {
    case "REQUEST_META_BOX_UPDATES":
      return true;
    case "META_BOX_UPDATES_SUCCESS":
    case "META_BOX_UPDATES_FAILURE":
      return false;
    default:
      return state;
  }
}
function mergeMetaboxes(metaboxes = [], newMetaboxes) {
  const mergedMetaboxes = [...metaboxes];
  for (const metabox of newMetaboxes) {
    const existing = mergedMetaboxes.findIndex(
      (box) => box.id === metabox.id
    );
    if (existing !== -1) {
      mergedMetaboxes[existing] = metabox;
    } else {
      mergedMetaboxes.push(metabox);
    }
  }
  return mergedMetaboxes;
}
function metaBoxLocations(state = {}, action) {
  switch (action.type) {
    case "SET_META_BOXES_PER_LOCATIONS": {
      const newState = { ...state };
      for (const [location, metaboxes] of Object.entries(
        action.metaBoxesPerLocation
      )) {
        newState[location] = mergeMetaboxes(
          newState[location],
          metaboxes
        );
      }
      return newState;
    }
  }
  return state;
}
function metaBoxesInitialized(state = false, action) {
  switch (action.type) {
    case "META_BOXES_INITIALIZED":
      return true;
  }
  return state;
}
const metaBoxes = (0,external_wp_data_namespaceObject.combineReducers)({
  isSaving: isSavingMetaBoxes,
  locations: metaBoxLocations,
  initialized: metaBoxesInitialized
});
var reducer_default = (0,external_wp_data_namespaceObject.combineReducers)({
  metaBoxes
});


;// external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// ./node_modules/@wordpress/edit-post/build-module/utils/meta-boxes.js
const getMetaBoxContainer = (location) => {
  const area = document.querySelector(
    `.edit-post-meta-boxes-area.is-${location} .metabox-location-${location}`
  );
  if (area) {
    return area;
  }
  return document.querySelector("#metaboxes .metabox-location-" + location);
};


;// ./node_modules/@wordpress/edit-post/build-module/store/actions.js










const { interfaceStore } = unlock(external_wp_editor_namespaceObject.privateApis);
const openGeneralSidebar = (name) => ({ registry }) => {
  registry.dispatch(interfaceStore).enableComplementaryArea("core", name);
};
const closeGeneralSidebar = () => ({ registry }) => registry.dispatch(interfaceStore).disableComplementaryArea("core");
const openModal = (name) => ({ registry }) => {
  external_wp_deprecated_default()("select( 'core/edit-post' ).openModal( name )", {
    since: "6.3",
    alternative: "select( 'core/interface').openModal( name )"
  });
  return registry.dispatch(interfaceStore).openModal(name);
};
const closeModal = () => ({ registry }) => {
  external_wp_deprecated_default()("select( 'core/edit-post' ).closeModal()", {
    since: "6.3",
    alternative: "select( 'core/interface').closeModal()"
  });
  return registry.dispatch(interfaceStore).closeModal();
};
const openPublishSidebar = () => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).openPublishSidebar", {
    since: "6.6",
    alternative: "dispatch( 'core/editor').openPublishSidebar"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).openPublishSidebar();
};
const closePublishSidebar = () => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).closePublishSidebar", {
    since: "6.6",
    alternative: "dispatch( 'core/editor').closePublishSidebar"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).closePublishSidebar();
};
const togglePublishSidebar = () => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).togglePublishSidebar", {
    since: "6.6",
    alternative: "dispatch( 'core/editor').togglePublishSidebar"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).togglePublishSidebar();
};
const toggleEditorPanelEnabled = (panelName) => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).toggleEditorPanelEnabled", {
    since: "6.5",
    alternative: "dispatch( 'core/editor').toggleEditorPanelEnabled"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).toggleEditorPanelEnabled(panelName);
};
const toggleEditorPanelOpened = (panelName) => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).toggleEditorPanelOpened", {
    since: "6.5",
    alternative: "dispatch( 'core/editor').toggleEditorPanelOpened"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).toggleEditorPanelOpened(panelName);
};
const removeEditorPanel = (panelName) => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).removeEditorPanel", {
    since: "6.5",
    alternative: "dispatch( 'core/editor').removeEditorPanel"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).removeEditorPanel(panelName);
};
const toggleFeature = (feature) => ({ registry }) => registry.dispatch(external_wp_preferences_namespaceObject.store).toggle("core/edit-post", feature);
const switchEditorMode = (mode) => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).switchEditorMode", {
    since: "6.6",
    alternative: "dispatch( 'core/editor').switchEditorMode"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).switchEditorMode(mode);
};
const togglePinnedPluginItem = (pluginName) => ({ registry }) => {
  const isPinned = registry.select(interfaceStore).isItemPinned("core", pluginName);
  registry.dispatch(interfaceStore)[isPinned ? "unpinItem" : "pinItem"]("core", pluginName);
};
function updatePreferredStyleVariations() {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).updatePreferredStyleVariations", {
    since: "6.6",
    hint: "Preferred Style Variations are not supported anymore."
  });
  return { type: "NOTHING" };
}
const showBlockTypes = (blockNames) => ({ registry }) => {
  unlock(registry.dispatch(external_wp_editor_namespaceObject.store)).showBlockTypes(blockNames);
};
const hideBlockTypes = (blockNames) => ({ registry }) => {
  unlock(registry.dispatch(external_wp_editor_namespaceObject.store)).hideBlockTypes(blockNames);
};
function setAvailableMetaBoxesPerLocation(metaBoxesPerLocation) {
  return {
    type: "SET_META_BOXES_PER_LOCATIONS",
    metaBoxesPerLocation
  };
}
const requestMetaBoxUpdates = () => async ({ registry, select, dispatch }) => {
  dispatch({
    type: "REQUEST_META_BOX_UPDATES"
  });
  if (window.tinyMCE) {
    window.tinyMCE.triggerSave();
  }
  const baseFormData = new window.FormData(
    document.querySelector(".metabox-base-form")
  );
  const postId = baseFormData.get("post_ID");
  const postType = baseFormData.get("post_type");
  const post = registry.select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord("postType", postType, postId);
  const additionalData = [
    post.comment_status ? ["comment_status", post.comment_status] : false,
    post.ping_status ? ["ping_status", post.ping_status] : false,
    post.sticky ? ["sticky", post.sticky] : false,
    post.author ? ["post_author", post.author] : false
  ].filter(Boolean);
  const activeMetaBoxLocations = select.getActiveMetaBoxLocations();
  const formDataToMerge = [
    baseFormData,
    ...activeMetaBoxLocations.map(
      (location) => new window.FormData(getMetaBoxContainer(location))
    )
  ];
  const formData = formDataToMerge.reduce((memo, currentFormData) => {
    for (const [key, value] of currentFormData) {
      memo.append(key, value);
    }
    return memo;
  }, new window.FormData());
  additionalData.forEach(
    ([key, value]) => formData.append(key, value)
  );
  try {
    await external_wp_apiFetch_default()({
      url: window._wpMetaBoxUrl,
      method: "POST",
      body: formData,
      parse: false
    });
    dispatch.metaBoxUpdatesSuccess();
  } catch {
    dispatch.metaBoxUpdatesFailure();
  }
};
function metaBoxUpdatesSuccess() {
  return {
    type: "META_BOX_UPDATES_SUCCESS"
  };
}
function metaBoxUpdatesFailure() {
  return {
    type: "META_BOX_UPDATES_FAILURE"
  };
}
const __experimentalSetPreviewDeviceType = (deviceType) => ({ registry }) => {
  external_wp_deprecated_default()(
    "dispatch( 'core/edit-post' ).__experimentalSetPreviewDeviceType",
    {
      since: "6.5",
      version: "6.7",
      hint: "registry.dispatch( editorStore ).setDeviceType"
    }
  );
  registry.dispatch(external_wp_editor_namespaceObject.store).setDeviceType(deviceType);
};
const setIsInserterOpened = (value) => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).setIsInserterOpened", {
    since: "6.5",
    alternative: "dispatch( 'core/editor').setIsInserterOpened"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).setIsInserterOpened(value);
};
const setIsListViewOpened = (isOpen) => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).setIsListViewOpened", {
    since: "6.5",
    alternative: "dispatch( 'core/editor').setIsListViewOpened"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).setIsListViewOpened(isOpen);
};
function setIsEditingTemplate() {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).setIsEditingTemplate", {
    since: "6.5",
    alternative: "dispatch( 'core/editor').setRenderingMode"
  });
  return { type: "NOTHING" };
}
function __unstableCreateTemplate() {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).__unstableCreateTemplate", {
    since: "6.5"
  });
  return { type: "NOTHING" };
}
let actions_metaBoxesInitialized = false;
const initializeMetaBoxes = () => ({ registry, select, dispatch }) => {
  const isEditorReady = registry.select(external_wp_editor_namespaceObject.store).__unstableIsEditorReady();
  if (!isEditorReady) {
    return;
  }
  if (actions_metaBoxesInitialized) {
    return;
  }
  const postType = registry.select(external_wp_editor_namespaceObject.store).getCurrentPostType();
  if (window.postboxes.page !== postType) {
    window.postboxes.add_postbox_toggles(postType);
  }
  actions_metaBoxesInitialized = true;
  (0,external_wp_hooks_namespaceObject.addAction)(
    "editor.savePost",
    "core/edit-post/save-metaboxes",
    async (post, options) => {
      if (!options.isAutosave && select.hasMetaBoxes()) {
        await dispatch.requestMetaBoxUpdates();
      }
    }
  );
  dispatch({
    type: "META_BOXES_INITIALIZED"
  });
};
const toggleDistractionFree = () => ({ registry }) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).toggleDistractionFree", {
    since: "6.6",
    alternative: "dispatch( 'core/editor').toggleDistractionFree"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).toggleDistractionFree();
};
const toggleFullscreenMode = () => ({ registry }) => {
  const isFullscreen = registry.select(external_wp_preferences_namespaceObject.store).get("core/edit-post", "fullscreenMode");
  registry.dispatch(external_wp_preferences_namespaceObject.store).toggle("core/edit-post", "fullscreenMode");
  registry.dispatch(external_wp_notices_namespaceObject.store).createInfoNotice(
    isFullscreen ? (0,external_wp_i18n_namespaceObject.__)("Fullscreen mode deactivated.") : (0,external_wp_i18n_namespaceObject.__)("Fullscreen mode activated."),
    {
      id: "core/edit-post/toggle-fullscreen-mode/notice",
      type: "snackbar",
      actions: [
        {
          label: (0,external_wp_i18n_namespaceObject.__)("Undo"),
          onClick: () => {
            registry.dispatch(external_wp_preferences_namespaceObject.store).toggle(
              "core/edit-post",
              "fullscreenMode"
            );
          }
        }
      ]
    }
  );
};


;// ./node_modules/@wordpress/edit-post/build-module/store/selectors.js






const { interfaceStore: selectors_interfaceStore } = unlock(external_wp_editor_namespaceObject.privateApis);
const EMPTY_ARRAY = [];
const EMPTY_OBJECT = {};
const getEditorMode = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => () => select(external_wp_preferences_namespaceObject.store).get("core", "editorMode") ?? "visual"
);
const isEditorSidebarOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => () => {
    const activeGeneralSidebar = select(selectors_interfaceStore).getActiveComplementaryArea("core");
    return ["edit-post/document", "edit-post/block"].includes(
      activeGeneralSidebar
    );
  }
);
const isPluginSidebarOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => () => {
    const activeGeneralSidebar = select(selectors_interfaceStore).getActiveComplementaryArea("core");
    return !!activeGeneralSidebar && !["edit-post/document", "edit-post/block"].includes(
      activeGeneralSidebar
    );
  }
);
const getActiveGeneralSidebarName = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => () => {
    return select(selectors_interfaceStore).getActiveComplementaryArea("core");
  }
);
function convertPanelsToOldFormat(inactivePanels, openPanels) {
  const panelsWithEnabledState = inactivePanels?.reduce(
    (accumulatedPanels, panelName) => ({
      ...accumulatedPanels,
      [panelName]: {
        enabled: false
      }
    }),
    {}
  );
  const panels = openPanels?.reduce((accumulatedPanels, panelName) => {
    const currentPanelState = accumulatedPanels?.[panelName];
    return {
      ...accumulatedPanels,
      [panelName]: {
        ...currentPanelState,
        opened: true
      }
    };
  }, panelsWithEnabledState ?? {});
  return panels ?? panelsWithEnabledState ?? EMPTY_OBJECT;
}
const getPreferences = (0,external_wp_data_namespaceObject.createRegistrySelector)((select) => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).getPreferences`, {
    since: "6.0",
    alternative: `select( 'core/preferences' ).get`
  });
  const corePreferences = ["editorMode", "hiddenBlockTypes"].reduce(
    (accumulatedPrefs, preferenceKey) => {
      const value = select(external_wp_preferences_namespaceObject.store).get(
        "core",
        preferenceKey
      );
      return {
        ...accumulatedPrefs,
        [preferenceKey]: value
      };
    },
    {}
  );
  const inactivePanels = select(external_wp_preferences_namespaceObject.store).get(
    "core",
    "inactivePanels"
  );
  const openPanels = select(external_wp_preferences_namespaceObject.store).get("core", "openPanels");
  const panels = convertPanelsToOldFormat(inactivePanels, openPanels);
  return {
    ...corePreferences,
    panels
  };
});
function getPreference(state, preferenceKey, defaultValue) {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).getPreference`, {
    since: "6.0",
    alternative: `select( 'core/preferences' ).get`
  });
  const preferences = getPreferences(state);
  const value = preferences[preferenceKey];
  return value === void 0 ? defaultValue : value;
}
const getHiddenBlockTypes = (0,external_wp_data_namespaceObject.createRegistrySelector)((select) => () => {
  return select(external_wp_preferences_namespaceObject.store).get("core", "hiddenBlockTypes") ?? EMPTY_ARRAY;
});
const isPublishSidebarOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => () => {
    external_wp_deprecated_default()(`select( 'core/edit-post' ).isPublishSidebarOpened`, {
      since: "6.6",
      alternative: `select( 'core/editor' ).isPublishSidebarOpened`
    });
    return select(external_wp_editor_namespaceObject.store).isPublishSidebarOpened();
  }
);
const isEditorPanelRemoved = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, panelName) => {
    external_wp_deprecated_default()(`select( 'core/edit-post' ).isEditorPanelRemoved`, {
      since: "6.5",
      alternative: `select( 'core/editor' ).isEditorPanelRemoved`
    });
    return select(external_wp_editor_namespaceObject.store).isEditorPanelRemoved(panelName);
  }
);
const isEditorPanelEnabled = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, panelName) => {
    external_wp_deprecated_default()(`select( 'core/edit-post' ).isEditorPanelEnabled`, {
      since: "6.5",
      alternative: `select( 'core/editor' ).isEditorPanelEnabled`
    });
    return select(external_wp_editor_namespaceObject.store).isEditorPanelEnabled(panelName);
  }
);
const isEditorPanelOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, panelName) => {
    external_wp_deprecated_default()(`select( 'core/edit-post' ).isEditorPanelOpened`, {
      since: "6.5",
      alternative: `select( 'core/editor' ).isEditorPanelOpened`
    });
    return select(external_wp_editor_namespaceObject.store).isEditorPanelOpened(panelName);
  }
);
const isModalActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, modalName) => {
    external_wp_deprecated_default()(`select( 'core/edit-post' ).isModalActive`, {
      since: "6.3",
      alternative: `select( 'core/interface' ).isModalActive`
    });
    return !!select(selectors_interfaceStore).isModalActive(modalName);
  }
);
const isFeatureActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, feature) => {
    return !!select(external_wp_preferences_namespaceObject.store).get("core/edit-post", feature);
  }
);
const isPluginItemPinned = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, pluginName) => {
    return select(selectors_interfaceStore).isItemPinned("core", pluginName);
  }
);
const getActiveMetaBoxLocations = (0,external_wp_data_namespaceObject.createSelector)(
  (state) => {
    return Object.keys(state.metaBoxes.locations).filter(
      (location) => isMetaBoxLocationActive(state, location)
    );
  },
  (state) => [state.metaBoxes.locations]
);
const isMetaBoxLocationVisible = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => (state, location) => {
    return isMetaBoxLocationActive(state, location) && getMetaBoxesPerLocation(state, location)?.some(({ id }) => {
      return select(external_wp_editor_namespaceObject.store).isEditorPanelEnabled(
        `meta-box-${id}`
      );
    });
  }
);
function isMetaBoxLocationActive(state, location) {
  const metaBoxes = getMetaBoxesPerLocation(state, location);
  return !!metaBoxes && metaBoxes.length !== 0;
}
function getMetaBoxesPerLocation(state, location) {
  return state.metaBoxes.locations[location];
}
const getAllMetaBoxes = (0,external_wp_data_namespaceObject.createSelector)(
  (state) => {
    return Object.values(state.metaBoxes.locations).flat();
  },
  (state) => [state.metaBoxes.locations]
);
function hasMetaBoxes(state) {
  return getActiveMetaBoxLocations(state).length > 0;
}
function selectors_isSavingMetaBoxes(state) {
  return state.metaBoxes.isSaving;
}
const __experimentalGetPreviewDeviceType = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => () => {
    external_wp_deprecated_default()(
      `select( 'core/edit-site' ).__experimentalGetPreviewDeviceType`,
      {
        since: "6.5",
        version: "6.7",
        alternative: `select( 'core/editor' ).getDeviceType`
      }
    );
    return select(external_wp_editor_namespaceObject.store).getDeviceType();
  }
);
const isInserterOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)((select) => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isInserterOpened`, {
    since: "6.5",
    alternative: `select( 'core/editor' ).isInserterOpened`
  });
  return select(external_wp_editor_namespaceObject.store).isInserterOpened();
});
const __experimentalGetInsertionPoint = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => () => {
    external_wp_deprecated_default()(
      `select( 'core/edit-post' ).__experimentalGetInsertionPoint`,
      {
        since: "6.5",
        version: "6.7"
      }
    );
    return unlock(select(external_wp_editor_namespaceObject.store)).getInserter();
  }
);
const isListViewOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)((select) => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isListViewOpened`, {
    since: "6.5",
    alternative: `select( 'core/editor' ).isListViewOpened`
  });
  return select(external_wp_editor_namespaceObject.store).isListViewOpened();
});
const isEditingTemplate = (0,external_wp_data_namespaceObject.createRegistrySelector)((select) => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isEditingTemplate`, {
    since: "6.5",
    alternative: `select( 'core/editor' ).getRenderingMode`
  });
  return select(external_wp_editor_namespaceObject.store).getCurrentPostType() === "wp_template";
});
function areMetaBoxesInitialized(state) {
  return state.metaBoxes.initialized;
}
const getEditedPostTemplate = (0,external_wp_data_namespaceObject.createRegistrySelector)(
  (select) => () => {
    const { id: postId, type: postType } = select(external_wp_editor_namespaceObject.store).getCurrentPost();
    const templateId = unlock(select(external_wp_coreData_namespaceObject.store)).getTemplateId(
      postType,
      postId
    );
    if (!templateId) {
      return void 0;
    }
    return select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(
      "postType",
      "wp_template",
      templateId
    );
  }
);


;// ./node_modules/@wordpress/edit-post/build-module/store/index.js





const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer_default,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);


;// ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js





function KeyboardShortcuts() {
  const { toggleFullscreenMode } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const { registerShortcut } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: "core/edit-post/toggle-fullscreen",
      category: "global",
      description: (0,external_wp_i18n_namespaceObject.__)("Enable or disable fullscreen mode."),
      keyCombination: {
        modifier: "secondary",
        character: "f"
      }
    });
  }, []);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)("core/edit-post/toggle-fullscreen", () => {
    toggleFullscreenMode();
  });
  return null;
}
var keyboard_shortcuts_default = KeyboardShortcuts;


;// ./node_modules/@wordpress/edit-post/build-module/components/init-pattern-modal/index.js






function InitPatternModal() {
  const { editPost } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_editor_namespaceObject.store);
  const [syncType, setSyncType] = (0,external_wp_element_namespaceObject.useState)(void 0);
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)("");
  const { postType, isNewPost } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getEditedPostAttribute, isCleanNewPost } = select(external_wp_editor_namespaceObject.store);
    return {
      postType: getEditedPostAttribute("type"),
      isNewPost: isCleanNewPost()
    };
  }, []);
  const [isModalOpen, setIsModalOpen] = (0,external_wp_element_namespaceObject.useState)(
    () => isNewPost && postType === "wp_block"
  );
  if (postType !== "wp_block" || !isNewPost) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: isModalOpen && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Modal,
    {
      title: (0,external_wp_i18n_namespaceObject.__)("Create pattern"),
      onRequestClose: () => {
        setIsModalOpen(false);
      },
      overlayClassName: "reusable-blocks-menu-items__convert-modal",
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        "form",
        {
          onSubmit: (event) => {
            event.preventDefault();
            setIsModalOpen(false);
            editPost({
              title,
              meta: {
                wp_pattern_sync_status: syncType
              }
            });
          },
          children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, { spacing: "5", children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_components_namespaceObject.TextControl,
              {
                label: (0,external_wp_i18n_namespaceObject.__)("Name"),
                value: title,
                onChange: setTitle,
                placeholder: (0,external_wp_i18n_namespaceObject.__)("My pattern"),
                className: "patterns-create-modal__name-input",
                __nextHasNoMarginBottom: true,
                __next40pxDefaultSize: true
              }
            ),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_components_namespaceObject.ToggleControl,
              {
                __nextHasNoMarginBottom: true,
                label: (0,external_wp_i18n_namespaceObject._x)("Synced", "pattern (singular)"),
                help: (0,external_wp_i18n_namespaceObject.__)(
                  "Sync this pattern across multiple locations."
                ),
                checked: !syncType,
                onChange: () => {
                  setSyncType(
                    !syncType ? "unsynced" : void 0
                  );
                }
              }
            ),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalHStack, { justify: "right", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_components_namespaceObject.Button,
              {
                __next40pxDefaultSize: true,
                variant: "primary",
                type: "submit",
                disabled: !title,
                accessibleWhenDisabled: true,
                children: (0,external_wp_i18n_namespaceObject.__)("Create")
              }
            ) })
          ] })
        }
      )
    }
  ) });
}


;// ./node_modules/@wordpress/edit-post/build-module/components/browser-url/index.js




function getPostEditURL(postId) {
  return (0,external_wp_url_namespaceObject.addQueryArgs)("post.php", { post: postId, action: "edit" });
}
function BrowserURL() {
  const [historyId, setHistoryId] = (0,external_wp_element_namespaceObject.useState)(null);
  const { postId, postStatus } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getCurrentPost } = select(external_wp_editor_namespaceObject.store);
    const post = getCurrentPost();
    let { id, status, type } = post;
    const isTemplate = ["wp_template", "wp_template_part"].includes(
      type
    );
    if (isTemplate) {
      id = post.wp_id;
    }
    return {
      postId: id,
      postStatus: status
    };
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (postId && postId !== historyId && postStatus !== "auto-draft") {
      window.history.replaceState(
        { id: postId },
        "Post " + postId,
        getPostEditURL(postId)
      );
      setHistoryId(postId);
    }
  }, [postId, postStatus, historyId]);
  return null;
}


;// ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js






function MetaBoxesArea({ location }) {
  const container = (0,external_wp_element_namespaceObject.useRef)(null);
  const formRef = (0,external_wp_element_namespaceObject.useRef)(null);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    formRef.current = document.querySelector(
      ".metabox-location-" + location
    );
    if (formRef.current) {
      container.current.appendChild(formRef.current);
    }
    return () => {
      if (formRef.current) {
        document.querySelector("#metaboxes").appendChild(formRef.current);
      }
    };
  }, [location]);
  const isSaving = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    return select(store).isSavingMetaBoxes();
  }, []);
  const classes = dist_clsx("edit-post-meta-boxes-area", `is-${location}`, {
    "is-loading": isSaving
  });
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: classes, children: [
    isSaving && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {}),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      "div",
      {
        className: "edit-post-meta-boxes-area__container",
        ref: container
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "edit-post-meta-boxes-area__clear" })
  ] });
}
var meta_boxes_area_default = MetaBoxesArea;


;// ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-box-visibility.js



function MetaBoxVisibility({ id }) {
  const isVisible = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      return select(external_wp_editor_namespaceObject.store).isEditorPanelEnabled(
        `meta-box-${id}`
      );
    },
    [id]
  );
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const element = document.getElementById(id);
    if (!element) {
      return;
    }
    if (isVisible) {
      element.classList.remove("is-hidden");
    } else {
      element.classList.add("is-hidden");
    }
  }, [id, isVisible]);
  return null;
}


;// ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js





function MetaBoxes({ location }) {
  const metaBoxes = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(store).getMetaBoxesPerLocation(location),
    [location]
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    (metaBoxes ?? []).map(({ id }) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(MetaBoxVisibility, { id }, id)),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(meta_boxes_area_default, { location })
  ] });
}


;// external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// ./node_modules/@wordpress/edit-post/build-module/components/more-menu/manage-patterns-menu-item.js






function ManagePatternsMenuItem() {
  const url = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { canUser } = select(external_wp_coreData_namespaceObject.store);
    const defaultUrl = (0,external_wp_url_namespaceObject.addQueryArgs)("edit.php", {
      post_type: "wp_block"
    });
    const patternsUrl = (0,external_wp_url_namespaceObject.addQueryArgs)("site-editor.php", {
      p: "/pattern"
    });
    return canUser("create", {
      kind: "postType",
      name: "wp_template"
    }) ? patternsUrl : defaultUrl;
  }, []);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, { role: "menuitem", href: url, children: (0,external_wp_i18n_namespaceObject.__)("Manage patterns") });
}
var manage_patterns_menu_item_default = ManagePatternsMenuItem;


;// ./node_modules/@wordpress/edit-post/build-module/components/more-menu/welcome-guide-menu-item.js





function WelcomeGuideMenuItem() {
  const isEditingTemplate = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(external_wp_editor_namespaceObject.store).getCurrentPostType() === "wp_template",
    []
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_preferences_namespaceObject.PreferenceToggleMenuItem,
    {
      scope: "core/edit-post",
      name: isEditingTemplate ? "welcomeGuideTemplate" : "welcomeGuide",
      label: (0,external_wp_i18n_namespaceObject.__)("Welcome Guide")
    }
  );
}


;// ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/enable-custom-fields.js









const { PreferenceBaseOption } = unlock(external_wp_preferences_namespaceObject.privateApis);
function submitCustomFieldsForm() {
  const customFieldsForm = document.getElementById(
    "toggle-custom-fields-form"
  );
  customFieldsForm.querySelector('[name="_wp_http_referer"]').setAttribute("value", (0,external_wp_url_namespaceObject.getPathAndQueryString)(window.location.href));
  customFieldsForm.submit();
}
function CustomFieldsConfirmation({ willEnable }) {
  const [isReloading, setIsReloading] = (0,external_wp_element_namespaceObject.useState)(false);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-post-preferences-modal__custom-fields-confirmation-message", children: (0,external_wp_i18n_namespaceObject.__)(
      "A page reload is required for this change. Make sure your content is saved before reloading."
    ) }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.Button,
      {
        __next40pxDefaultSize: true,
        variant: "secondary",
        isBusy: isReloading,
        accessibleWhenDisabled: true,
        disabled: isReloading,
        onClick: () => {
          setIsReloading(true);
          submitCustomFieldsForm();
        },
        children: willEnable ? (0,external_wp_i18n_namespaceObject.__)("Show & Reload Page") : (0,external_wp_i18n_namespaceObject.__)("Hide & Reload Page")
      }
    )
  ] });
}
function EnableCustomFieldsOption({ label }) {
  const areCustomFieldsEnabled = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    return !!select(external_wp_editor_namespaceObject.store).getEditorSettings().enableCustomFields;
  }, []);
  const [isChecked, setIsChecked] = (0,external_wp_element_namespaceObject.useState)(areCustomFieldsEnabled);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    PreferenceBaseOption,
    {
      label,
      isChecked,
      onChange: setIsChecked,
      children: isChecked !== areCustomFieldsEnabled && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(CustomFieldsConfirmation, { willEnable: isChecked })
    }
  );
}


;// ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/enable-panel.js





const { PreferenceBaseOption: enable_panel_PreferenceBaseOption } = unlock(external_wp_preferences_namespaceObject.privateApis);
function EnablePanelOption(props) {
  const { toggleEditorPanelEnabled } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_editor_namespaceObject.store);
  const { isChecked, isRemoved } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { isEditorPanelEnabled, isEditorPanelRemoved } = select(external_wp_editor_namespaceObject.store);
      return {
        isChecked: isEditorPanelEnabled(props.panelName),
        isRemoved: isEditorPanelRemoved(props.panelName)
      };
    },
    [props.panelName]
  );
  if (isRemoved) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    enable_panel_PreferenceBaseOption,
    {
      isChecked,
      onChange: () => toggleEditorPanelEnabled(props.panelName),
      ...props
    }
  );
}


;// ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/meta-boxes-section.js









const { PreferencesModalSection } = unlock(external_wp_preferences_namespaceObject.privateApis);
function MetaBoxesSection({
  areCustomFieldsRegistered,
  metaBoxes,
  ...sectionProps
}) {
  const thirdPartyMetaBoxes = metaBoxes.filter(
    ({ id }) => id !== "postcustom"
  );
  if (!areCustomFieldsRegistered && thirdPartyMetaBoxes.length === 0) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(PreferencesModalSection, { ...sectionProps, children: [
    areCustomFieldsRegistered && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(EnableCustomFieldsOption, { label: (0,external_wp_i18n_namespaceObject.__)("Custom fields") }),
    thirdPartyMetaBoxes.map(({ id, title }) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      EnablePanelOption,
      {
        label: title,
        panelName: `meta-box-${id}`
      },
      id
    ))
  ] });
}
var meta_boxes_section_default = (0,external_wp_data_namespaceObject.withSelect)((select) => {
  const { getEditorSettings } = select(external_wp_editor_namespaceObject.store);
  const { getAllMetaBoxes } = select(store);
  return {
    // This setting should not live in the block editor's store.
    areCustomFieldsRegistered: getEditorSettings().enableCustomFields !== void 0,
    metaBoxes: getAllMetaBoxes()
  };
})(MetaBoxesSection);


;// ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/index.js






const { PreferenceToggleControl } = unlock(external_wp_preferences_namespaceObject.privateApis);
const { PreferencesModal } = unlock(external_wp_editor_namespaceObject.privateApis);
function EditPostPreferencesModal() {
  const extraSections = {
    general: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(meta_boxes_section_default, { title: (0,external_wp_i18n_namespaceObject.__)("Advanced") }),
    appearance: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      PreferenceToggleControl,
      {
        scope: "core/edit-post",
        featureName: "themeStyles",
        help: (0,external_wp_i18n_namespaceObject.__)("Make the editor look like your theme."),
        label: (0,external_wp_i18n_namespaceObject.__)("Use theme styles")
      }
    )
  };
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(PreferencesModal, { extraSections });
}


;// ./node_modules/@wordpress/edit-post/build-module/components/more-menu/index.js










const { ToolsMoreMenuGroup, ViewMoreMenuGroup } = unlock(external_wp_editor_namespaceObject.privateApis);
const MoreMenu = () => {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("large");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    isLargeViewport && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(ViewMoreMenuGroup, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_preferences_namespaceObject.PreferenceToggleMenuItem,
      {
        scope: "core/edit-post",
        name: "fullscreenMode",
        label: (0,external_wp_i18n_namespaceObject.__)("Fullscreen mode"),
        info: (0,external_wp_i18n_namespaceObject.__)("Show and hide the admin user interface"),
        messageActivated: (0,external_wp_i18n_namespaceObject.__)("Fullscreen mode activated."),
        messageDeactivated: (0,external_wp_i18n_namespaceObject.__)(
          "Fullscreen mode deactivated."
        ),
        shortcut: external_wp_keycodes_namespaceObject.displayShortcut.secondary("f")
      }
    ) }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(ToolsMoreMenuGroup, { children: [
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(manage_patterns_menu_item_default, {}),
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideMenuItem, {})
    ] }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(EditPostPreferencesModal, {})
  ] });
};
var more_menu_default = MoreMenu;


;// ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/image.js

function WelcomeGuideImage({ nonAnimatedSrc, animatedSrc }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("picture", { className: "edit-post-welcome-guide__image", children: [
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


;// ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/default.js







function WelcomeGuideDefault() {
  const { toggleFeature } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Guide,
    {
      className: "edit-post-welcome-guide",
      contentLabel: (0,external_wp_i18n_namespaceObject.__)("Welcome to the editor"),
      finishButtonText: (0,external_wp_i18n_namespaceObject.__)("Get started"),
      onFinish: () => toggleFeature("welcomeGuide"),
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
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0,external_wp_i18n_namespaceObject.__)("Welcome to the editor") }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0,external_wp_i18n_namespaceObject.__)(
              "In the WordPress editor, each paragraph, image, or video is presented as a distinct \u201Cblock\u201D of content."
            ) })
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
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0,external_wp_i18n_namespaceObject.__)("Customize each block") }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0,external_wp_i18n_namespaceObject.__)(
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
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0,external_wp_i18n_namespaceObject.__)("Explore all blocks") }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0,external_wp_element_namespaceObject.createInterpolateElement)(
              (0,external_wp_i18n_namespaceObject.__)(
                "All of the blocks available to you live in the block library. You\u2019ll find it wherever you see the <InserterIconImage /> icon."
              ),
              {
                InserterIconImage: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                  "img",
                  {
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
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0,external_wp_i18n_namespaceObject.__)("Learn more") }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0,external_wp_element_namespaceObject.createInterpolateElement)(
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


;// ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/template.js






function WelcomeGuideTemplate() {
  const { toggleFeature } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Guide,
    {
      className: "edit-template-welcome-guide",
      contentLabel: (0,external_wp_i18n_namespaceObject.__)("Welcome to the template editor"),
      finishButtonText: (0,external_wp_i18n_namespaceObject.__)("Get started"),
      onFinish: () => toggleFeature("welcomeGuideTemplate"),
      pages: [
        {
          image: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            WelcomeGuideImage,
            {
              nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.svg",
              animatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.gif"
            }
          ),
          content: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0,external_wp_i18n_namespaceObject.__)("Welcome to the template editor") }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0,external_wp_i18n_namespaceObject.__)(
              "Templates help define the layout of the site. You can customize all aspects of your posts and pages using blocks and patterns in this editor."
            ) })
          ] })
        }
      ]
    }
  );
}


;// ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/index.js





function WelcomeGuide({ postType }) {
  const { isActive, isEditingTemplate } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { isFeatureActive } = select(store);
      const _isEditingTemplate = postType === "wp_template";
      const feature = _isEditingTemplate ? "welcomeGuideTemplate" : "welcomeGuide";
      return {
        isActive: isFeatureActive(feature),
        isEditingTemplate: _isEditingTemplate
      };
    },
    [postType]
  );
  if (!isActive) {
    return null;
  }
  return isEditingTemplate ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideTemplate, {}) : /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideDefault, {});
}


;// ./node_modules/@wordpress/icons/build-module/library/fullscreen.js


var fullscreen_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M6 4a2 2 0 0 0-2 2v3h1.5V6a.5.5 0 0 1 .5-.5h3V4H6Zm3 14.5H6a.5.5 0 0 1-.5-.5v-3H4v3a2 2 0 0 0 2 2h3v-1.5Zm6 1.5v-1.5h3a.5.5 0 0 0 .5-.5v-3H20v3a2 2 0 0 1-2 2h-3Zm3-16a2 2 0 0 1 2 2v3h-1.5V6a.5.5 0 0 0-.5-.5h-3V4h3Z" }) });


;// ./node_modules/@wordpress/edit-post/build-module/commands/use-commands.js






function useCommands() {
  const { isFullscreen } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { get } = select(external_wp_preferences_namespaceObject.store);
    return {
      isFullscreen: get("core/edit-post", "fullscreenMode")
    };
  }, []);
  const { toggle } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const { createInfoNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: "core/toggle-fullscreen-mode",
    label: isFullscreen ? (0,external_wp_i18n_namespaceObject.__)("Exit fullscreen") : (0,external_wp_i18n_namespaceObject.__)("Enter fullscreen"),
    icon: fullscreen_default,
    callback: ({ close }) => {
      toggle("core/edit-post", "fullscreenMode");
      close();
      createInfoNotice(
        isFullscreen ? (0,external_wp_i18n_namespaceObject.__)("Fullscreen off.") : (0,external_wp_i18n_namespaceObject.__)("Fullscreen on."),
        {
          id: "core/edit-post/toggle-fullscreen-mode/notice",
          type: "snackbar",
          actions: [
            {
              label: (0,external_wp_i18n_namespaceObject.__)("Undo"),
              onClick: () => {
                toggle("core/edit-post", "fullscreenMode");
              }
            }
          ]
        }
      );
    }
  });
}


;// ./node_modules/@wordpress/edit-post/build-module/components/layout/use-padding-appender.js




const CSS = ':root :where(.editor-styles-wrapper)::after {content: ""; display: block; height: 40vh;}';
function usePaddingAppender(enabled) {
  const registry = (0,external_wp_data_namespaceObject.useRegistry)();
  const effect = (0,external_wp_compose_namespaceObject.useRefEffect)(
    (node) => {
      function onMouseDown(event) {
        if (event.target !== node && // Tests for the parent element because in the iframed editor if the click is
        // below the padding the target will be the parent element (html) and should
        // still be treated as intent to append.
        event.target !== node.parentElement) {
          return;
        }
        const lastChild = node.lastElementChild;
        if (!lastChild) {
          return;
        }
        const lastChildRect = lastChild.getBoundingClientRect();
        if (event.clientY < lastChildRect.bottom) {
          return;
        }
        event.preventDefault();
        const blockOrder = registry.select(external_wp_blockEditor_namespaceObject.store).getBlockOrder("");
        const lastBlockClientId = blockOrder[blockOrder.length - 1];
        const lastBlock = registry.select(external_wp_blockEditor_namespaceObject.store).getBlock(lastBlockClientId);
        const { selectBlock, insertDefaultBlock } = registry.dispatch(external_wp_blockEditor_namespaceObject.store);
        if (lastBlock && (0,external_wp_blocks_namespaceObject.isUnmodifiedDefaultBlock)(lastBlock)) {
          selectBlock(lastBlockClientId);
        } else {
          insertDefaultBlock();
        }
      }
      const { ownerDocument } = node;
      ownerDocument.addEventListener("pointerdown", onMouseDown);
      return () => {
        ownerDocument.removeEventListener("pointerdown", onMouseDown);
      };
    },
    [registry]
  );
  return enabled ? [effect, CSS] : [];
}


;// ./node_modules/@wordpress/edit-post/build-module/components/layout/use-should-iframe.js





const isGutenbergPlugin =  false ? 0 : false;
function useShouldIframe() {
  return (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getEditorSettings, getCurrentPostType, getDeviceType } = select(external_wp_editor_namespaceObject.store);
    return (
      // If the theme is block based and the Gutenberg plugin is active,
      // we ALWAYS use the iframe for consistency across the post and site
      // editor.
      isGutenbergPlugin && getEditorSettings().__unstableIsBlockBasedTheme || // We also still want to iframe all the special
      // editor features and modes such as device previews, zoom out, and
      // template/pattern editing.
      getDeviceType() !== "Desktop" || ["wp_template", "wp_block"].includes(getCurrentPostType()) || unlock(select(external_wp_blockEditor_namespaceObject.store)).isZoomOut() || // Finally, still iframe the editor if all blocks are v3 (which means
      // they are marked as iframe-compatible).
      select(external_wp_blocks_namespaceObject.store).getBlockTypes().every((type) => type.apiVersion >= 3)
    );
  }, []);
}


;// ./node_modules/@wordpress/edit-post/build-module/hooks/use-navigate-to-entity-record.js



function useNavigateToEntityRecord(initialPostId, initialPostType, defaultRenderingMode) {
  const [postHistory, dispatch] = (0,external_wp_element_namespaceObject.useReducer)(
    (historyState, { type, post: post2, previousRenderingMode: previousRenderingMode2 }) => {
      if (type === "push") {
        return [...historyState, { post: post2, previousRenderingMode: previousRenderingMode2 }];
      }
      if (type === "pop") {
        if (historyState.length > 1) {
          return historyState.slice(0, -1);
        }
      }
      return historyState;
    },
    [
      {
        post: { postId: initialPostId, postType: initialPostType }
      }
    ]
  );
  const { post, previousRenderingMode } = postHistory[postHistory.length - 1];
  const { getRenderingMode } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_editor_namespaceObject.store);
  const { setRenderingMode } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_editor_namespaceObject.store);
  const onNavigateToEntityRecord = (0,external_wp_element_namespaceObject.useCallback)(
    (params) => {
      dispatch({
        type: "push",
        post: { postId: params.postId, postType: params.postType },
        // Save the current rendering mode so we can restore it when navigating back.
        previousRenderingMode: getRenderingMode()
      });
      setRenderingMode(defaultRenderingMode);
    },
    [getRenderingMode, setRenderingMode, defaultRenderingMode]
  );
  const onNavigateToPreviousEntityRecord = (0,external_wp_element_namespaceObject.useCallback)(() => {
    dispatch({ type: "pop" });
    if (previousRenderingMode) {
      setRenderingMode(previousRenderingMode);
    }
  }, [setRenderingMode, previousRenderingMode]);
  return {
    currentPost: post,
    onNavigateToEntityRecord,
    onNavigateToPreviousEntityRecord: postHistory.length > 1 ? onNavigateToPreviousEntityRecord : void 0
  };
}


;// ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/use-meta-box-initialization.js




const useMetaBoxInitialization = (enabled) => {
  const isEnabledAndEditorReady = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => enabled && select(external_wp_editor_namespaceObject.store).__unstableIsEditorReady(),
    [enabled]
  );
  const { initializeMetaBoxes } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isEnabledAndEditorReady) {
      initializeMetaBoxes();
    }
  }, [isEnabledAndEditorReady, initializeMetaBoxes]);
};


;// ./node_modules/@wordpress/edit-post/build-module/components/layout/index.js


































const { getLayoutStyles } = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const { useCommandContext } = unlock(external_wp_commands_namespaceObject.privateApis);
const { Editor, FullscreenMode } = unlock(external_wp_editor_namespaceObject.privateApis);
const { BlockKeyboardShortcuts } = unlock(external_wp_blockLibrary_namespaceObject.privateApis);
const DESIGN_POST_TYPES = [
  "wp_template",
  "wp_template_part",
  "wp_block",
  "wp_navigation"
];
function useEditorStyles(...additionalStyles) {
  const { hasThemeStyleSupport, editorSettings } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    return {
      hasThemeStyleSupport: select(store).isFeatureActive("themeStyles"),
      editorSettings: select(external_wp_editor_namespaceObject.store).getEditorSettings()
    };
  }, []);
  const addedStyles = additionalStyles.join("\n");
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    const presetStyles = editorSettings.styles?.filter(
      (style) => style.__unstableType && style.__unstableType !== "theme"
    ) ?? [];
    const defaultEditorStyles = [
      ...editorSettings?.defaultEditorStyles ?? [],
      ...presetStyles
    ];
    const hasThemeStyles = hasThemeStyleSupport && presetStyles.length !== (editorSettings.styles?.length ?? 0);
    if (!editorSettings.disableLayoutStyles && !hasThemeStyles) {
      defaultEditorStyles.push({
        css: getLayoutStyles({
          style: {},
          selector: "body",
          hasBlockGapSupport: false,
          hasFallbackGapSupport: true,
          fallbackGapValue: "0.5em"
        })
      });
    }
    const baseStyles = hasThemeStyles ? editorSettings.styles ?? [] : defaultEditorStyles;
    if (addedStyles) {
      return [...baseStyles, { css: addedStyles }];
    }
    return baseStyles;
  }, [
    editorSettings.defaultEditorStyles,
    editorSettings.disableLayoutStyles,
    editorSettings.styles,
    hasThemeStyleSupport,
    addedStyles
  ]);
}
function MetaBoxesMain({ isLegacy }) {
  const [isOpen, openHeight, hasAnyVisible] = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { get } = select(external_wp_preferences_namespaceObject.store);
    const { isMetaBoxLocationVisible } = select(store);
    return [
      !!get("core/edit-post", "metaBoxesMainIsOpen"),
      get("core/edit-post", "metaBoxesMainOpenHeight"),
      isMetaBoxLocationVisible("normal") || isMetaBoxLocationVisible("advanced") || isMetaBoxLocationVisible("side")
    ];
  }, []);
  const { set: setPreference } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const metaBoxesMainRef = (0,external_wp_element_namespaceObject.useRef)();
  const isShort = (0,external_wp_compose_namespaceObject.useMediaQuery)("(max-height: 549px)");
  const [{ min, max }, setHeightConstraints] = (0,external_wp_element_namespaceObject.useState)(() => ({}));
  const effectSizeConstraints = (0,external_wp_compose_namespaceObject.useRefEffect)((node) => {
    const container = node.closest(
      ".interface-interface-skeleton__content"
    );
    if (!container) {
      return;
    }
    const noticeLists = container.querySelectorAll(
      ":scope > .components-notice-list"
    );
    const resizeHandle = container.querySelector(
      ".edit-post-meta-boxes-main__presenter"
    );
    const deriveConstraints = () => {
      const fullHeight = container.offsetHeight;
      let nextMax = fullHeight;
      for (const element of noticeLists) {
        nextMax -= element.offsetHeight;
      }
      const nextMin = resizeHandle.offsetHeight;
      setHeightConstraints({ min: nextMin, max: nextMax });
    };
    const observer = new window.ResizeObserver(deriveConstraints);
    observer.observe(container);
    for (const element of noticeLists) {
      observer.observe(element);
    }
    return () => observer.disconnect();
  }, []);
  const resizeDataRef = (0,external_wp_element_namespaceObject.useRef)({});
  const separatorRef = (0,external_wp_element_namespaceObject.useRef)();
  const separatorHelpId = (0,external_wp_element_namespaceObject.useId)();
  const applyHeight = (candidateHeight = "auto", isPersistent, isInstant) => {
    if (candidateHeight === "auto") {
      isPersistent = false;
    } else {
      candidateHeight = Math.min(max, Math.max(min, candidateHeight));
    }
    if (isPersistent) {
      setPreference(
        "core/edit-post",
        "metaBoxesMainOpenHeight",
        candidateHeight
      );
    } else if (!isShort) {
      separatorRef.current.ariaValueNow = getAriaValueNow(candidateHeight);
    }
    if (isInstant) {
      metaBoxesMainRef.current.updateSize({
        height: candidateHeight,
        // Oddly, when the event that triggered this was not from the mouse (e.g. keydown),
        // if `width` is left unspecified a subsequent drag gesture applies a fixed
        // width and the pane fails to widen/narrow with parent width changes from
        // sidebars opening/closing or window resizes.
        width: "auto"
      });
    }
  };
  const getRenderValues = (0,external_wp_compose_namespaceObject.useEvent)(() => ({ isOpen, openHeight, min }));
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const fresh = getRenderValues();
    if (fresh.min !== void 0 && metaBoxesMainRef.current) {
      const usedOpenHeight = isShort ? "auto" : fresh.openHeight;
      const usedHeight = fresh.isOpen ? usedOpenHeight : fresh.min;
      applyHeight(usedHeight, false, true);
    }
  }, [isShort]);
  if (!hasAnyVisible) {
    return;
  }
  const contents = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
    "div",
    {
      className: "edit-post-layout__metaboxes edit-post-meta-boxes-main__liner",
      hidden: !isLegacy && !isOpen,
      children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(MetaBoxes, { location: "normal" }),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(MetaBoxes, { location: "advanced" })
      ]
    }
  );
  if (isLegacy) {
    return contents;
  }
  const isAutoHeight = openHeight === void 0;
  const getAriaValueNow = (height) => Math.round((height - min) / (max - min) * 100);
  const usedAriaValueNow = max === void 0 || isAutoHeight ? 50 : getAriaValueNow(openHeight);
  const persistIsOpen = (to = !isOpen) => setPreference("core/edit-post", "metaBoxesMainIsOpen", to);
  const onSeparatorKeyDown = (event) => {
    const delta = { ArrowUp: 20, ArrowDown: -20 }[event.key];
    if (delta) {
      const pane = metaBoxesMainRef.current.resizable;
      const fromHeight = isAutoHeight ? pane.offsetHeight : openHeight;
      const nextHeight = delta + fromHeight;
      applyHeight(nextHeight, true, true);
      persistIsOpen(nextHeight > min);
      event.preventDefault();
    }
  };
  const paneLabel = (0,external_wp_i18n_namespaceObject.__)("Meta Boxes");
  const toggle = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
    "button",
    {
      "aria-expanded": isOpen,
      onClick: ({ detail }) => {
        const { isToggleInferred } = resizeDataRef.current;
        if (isShort || !detail || isToggleInferred) {
          persistIsOpen();
          const usedOpenHeight = isShort ? "auto" : openHeight;
          const usedHeight = isOpen ? min : usedOpenHeight;
          applyHeight(usedHeight, false, true);
        }
      },
      ...isShort && {
        onMouseDown: (event) => event.stopPropagation(),
        onTouchStart: (event) => event.stopPropagation()
      },
      children: [
        paneLabel,
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Icon, { icon: isOpen ? chevron_up_default : chevron_down_default })
      ]
    }
  );
  const separator = !isShort && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Tooltip, { text: (0,external_wp_i18n_namespaceObject.__)("Drag to resize"), children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      "button",
      {
        ref: separatorRef,
        role: "separator",
        "aria-valuenow": usedAriaValueNow,
        "aria-label": (0,external_wp_i18n_namespaceObject.__)("Drag to resize"),
        "aria-describedby": separatorHelpId,
        onKeyDown: onSeparatorKeyDown
      }
    ) }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.VisuallyHidden, { id: separatorHelpId, children: (0,external_wp_i18n_namespaceObject.__)(
      "Use up and down arrow keys to resize the meta box panel."
    ) })
  ] });
  const paneProps = (
    /** @type {Parameters<typeof ResizableBox>[0]} */
    {
      as: navigable_region_default,
      ref: metaBoxesMainRef,
      className: "edit-post-meta-boxes-main",
      defaultSize: { height: isOpen ? openHeight : 0 },
      minHeight: min,
      maxHeight: max,
      enable: { top: true },
      handleClasses: { top: "edit-post-meta-boxes-main__presenter" },
      handleComponent: {
        top: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
          toggle,
          separator
        ] })
      },
      // Avoids hiccups while dragging over objects like iframes and ensures that
      // the event to end the drag is captured by the target (resize handle)
      // whether or not it’s under the pointer.
      onPointerDown: ({ pointerId, target }) => {
        if (separatorRef.current?.parentElement.contains(target)) {
          target.setPointerCapture(pointerId);
        }
      },
      onResizeStart: ({ timeStamp }, direction, elementRef) => {
        if (isAutoHeight) {
          applyHeight(elementRef.offsetHeight, false, true);
        }
        elementRef.classList.add("is-resizing");
        resizeDataRef.current = { timeStamp, maxDelta: 0 };
      },
      onResize: (event, direction, elementRef, delta) => {
        const { maxDelta } = resizeDataRef.current;
        const newDelta = Math.abs(delta.height);
        resizeDataRef.current.maxDelta = Math.max(maxDelta, newDelta);
        applyHeight(metaBoxesMainRef.current.state.height);
      },
      onResizeStop: (event, direction, elementRef) => {
        elementRef.classList.remove("is-resizing");
        const duration = event.timeStamp - resizeDataRef.current.timeStamp;
        const wasSeparator = event.target === separatorRef.current;
        const { maxDelta } = resizeDataRef.current;
        const isToggleInferred = maxDelta < 1 || duration < 144 && maxDelta < 5;
        if (isShort || !wasSeparator && isToggleInferred) {
          resizeDataRef.current.isToggleInferred = true;
        } else {
          const { height } = metaBoxesMainRef.current.state;
          const nextIsOpen = height > min;
          persistIsOpen(nextIsOpen);
          if (nextIsOpen) {
            applyHeight(height, true);
          }
        }
      }
    }
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.ResizableBox, { "aria-label": paneLabel, ...paneProps, children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("meta", { ref: effectSizeConstraints }),
    contents
  ] });
}
function Layout({
  postId: initialPostId,
  postType: initialPostType,
  settings,
  initialEdits
}) {
  useCommands();
  const shouldIframe = useShouldIframe();
  const { createErrorNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const {
    currentPost: { postId: currentPostId, postType: currentPostType },
    onNavigateToEntityRecord,
    onNavigateToPreviousEntityRecord
  } = useNavigateToEntityRecord(
    initialPostId,
    initialPostType,
    "post-only"
  );
  const isEditingTemplate = currentPostType === "wp_template";
  const {
    mode,
    isFullscreenActive,
    hasResolvedMode,
    hasActiveMetaboxes,
    hasBlockSelected,
    showIconLabels,
    isDistractionFree,
    showMetaBoxes,
    isWelcomeGuideVisible,
    templateId,
    enablePaddingAppender,
    isDevicePreview
  } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { get } = select(external_wp_preferences_namespaceObject.store);
      const { isFeatureActive, hasMetaBoxes } = select(store);
      const { canUser, getPostType, getTemplateId } = unlock(
        select(external_wp_coreData_namespaceObject.store)
      );
      const supportsTemplateMode = settings.supportsTemplateMode;
      const isViewable = getPostType(currentPostType)?.viewable ?? false;
      const canViewTemplate = canUser("read", {
        kind: "postType",
        name: "wp_template"
      });
      const { getBlockSelectionStart, isZoomOut } = unlock(
        select(external_wp_blockEditor_namespaceObject.store)
      );
      const {
        getEditorMode,
        getRenderingMode,
        getDefaultRenderingMode,
        getDeviceType
      } = unlock(select(external_wp_editor_namespaceObject.store));
      const isRenderingPostOnly = getRenderingMode() === "post-only";
      const isNotDesignPostType = !DESIGN_POST_TYPES.includes(currentPostType);
      const isDirectlyEditingPattern = currentPostType === "wp_block" && !onNavigateToPreviousEntityRecord;
      const _templateId = getTemplateId(currentPostType, currentPostId);
      const defaultMode = getDefaultRenderingMode(currentPostType);
      return {
        mode: getEditorMode(),
        isFullscreenActive: isFeatureActive("fullscreenMode"),
        hasActiveMetaboxes: hasMetaBoxes(),
        hasResolvedMode: defaultMode === "template-locked" ? !!_templateId : defaultMode !== void 0,
        hasBlockSelected: !!getBlockSelectionStart(),
        showIconLabels: get("core", "showIconLabels"),
        isDistractionFree: get("core", "distractionFree"),
        showMetaBoxes: isNotDesignPostType && !isZoomOut() || isDirectlyEditingPattern,
        isWelcomeGuideVisible: isFeatureActive("welcomeGuide"),
        templateId: supportsTemplateMode && isViewable && canViewTemplate && !isEditingTemplate ? _templateId : null,
        enablePaddingAppender: !isZoomOut() && isRenderingPostOnly && isNotDesignPostType,
        isDevicePreview: getDeviceType() !== "Desktop"
      };
    },
    [
      currentPostType,
      currentPostId,
      isEditingTemplate,
      settings.supportsTemplateMode,
      onNavigateToPreviousEntityRecord
    ]
  );
  useMetaBoxInitialization(hasActiveMetaboxes && hasResolvedMode);
  const [paddingAppenderRef, paddingStyle] = usePaddingAppender(
    enablePaddingAppender
  );
  const commandContext = hasBlockSelected ? "block-selection-edit" : "entity-edit";
  useCommandContext(commandContext);
  const editorSettings = (0,external_wp_element_namespaceObject.useMemo)(
    () => ({
      ...settings,
      onNavigateToEntityRecord,
      onNavigateToPreviousEntityRecord,
      defaultRenderingMode: "post-only"
    }),
    [settings, onNavigateToEntityRecord, onNavigateToPreviousEntityRecord]
  );
  const styles = useEditorStyles(paddingStyle);
  if (showIconLabels) {
    document.body.classList.add("show-icon-labels");
  } else {
    document.body.classList.remove("show-icon-labels");
  }
  const navigateRegionsProps = (0,external_wp_components_namespaceObject.__unstableUseNavigateRegions)();
  const className = dist_clsx("edit-post-layout", "is-mode-" + mode, {
    "has-metaboxes": hasActiveMetaboxes
  });
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
  const { createSuccessNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const onActionPerformed = (0,external_wp_element_namespaceObject.useCallback)(
    (actionId, items) => {
      switch (actionId) {
        case "move-to-trash":
          {
            document.location.href = (0,external_wp_url_namespaceObject.addQueryArgs)("edit.php", {
              trashed: 1,
              post_type: items[0].type,
              ids: items[0].id
            });
          }
          break;
        case "duplicate-post":
          {
            const newItem = items[0];
            const title = typeof newItem.title === "string" ? newItem.title : newItem.title?.rendered;
            createSuccessNotice(
              (0,external_wp_i18n_namespaceObject.sprintf)(
                // translators: %s: Title of the created post or template, e.g: "Hello world".
                (0,external_wp_i18n_namespaceObject.__)('"%s" successfully created.'),
                (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title) || (0,external_wp_i18n_namespaceObject.__)("(no title)")
              ),
              {
                type: "snackbar",
                id: "duplicate-post-action",
                actions: [
                  {
                    label: (0,external_wp_i18n_namespaceObject.__)("Edit"),
                    onClick: () => {
                      const postId = newItem.id;
                      document.location.href = (0,external_wp_url_namespaceObject.addQueryArgs)("post.php", {
                        post: postId,
                        action: "edit"
                      });
                    }
                  }
                ]
              }
            );
          }
          break;
      }
    },
    [createSuccessNotice]
  );
  const initialPost = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return {
      type: initialPostType,
      id: initialPostId
    };
  }, [initialPostType, initialPostId]);
  const backButton = (0,external_wp_compose_namespaceObject.useViewportMatch)("medium") && isFullscreenActive ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(back_button_default, { initialPost }) : null;
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.SlotFillProvider, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_editor_namespaceObject.ErrorBoundary, { canCopyContent: true, children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuide, { postType: currentPostType }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      "div",
      {
        className: navigateRegionsProps.className,
        ...navigateRegionsProps,
        ref: navigateRegionsProps.ref,
        children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
          Editor,
          {
            settings: editorSettings,
            initialEdits,
            postType: currentPostType,
            postId: currentPostId,
            templateId,
            className,
            styles,
            forceIsDirty: hasActiveMetaboxes,
            contentRef: paddingAppenderRef,
            disableIframe: !shouldIframe,
            autoFocus: !isWelcomeGuideVisible,
            onActionPerformed,
            extraSidebarPanels: showMetaBoxes && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(MetaBoxes, { location: "side" }),
            extraContent: !isDistractionFree && showMetaBoxes && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              MetaBoxesMain,
              {
                isLegacy: !shouldIframe || isDevicePreview
              }
            ),
            children: [
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PostLockedModal, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(EditorInitialization, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(FullscreenMode, { isActive: isFullscreenActive }),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(BrowserURL, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.UnsavedChangesWarning, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.AutosaveMonitor, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.LocalAutosaveMonitor, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(keyboard_shortcuts_default, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.EditorKeyboardShortcutsRegister, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(BlockKeyboardShortcuts, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(InitPatternModal, {}),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_plugins_namespaceObject.PluginArea, { onError: onPluginAreaError }),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(more_menu_default, {}),
              backButton,
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.EditorSnackbars, {})
            ]
          }
        )
      }
    )
  ] }) });
}
var layout_default = Layout;


;// ./node_modules/@wordpress/edit-post/build-module/deprecated.js





const { PluginPostExcerpt } = unlock(external_wp_editor_namespaceObject.privateApis);
const isSiteEditor = (0,external_wp_url_namespaceObject.getPath)(window.location.href)?.includes(
  "site-editor.php"
);
const deprecateSlot = (name) => {
  external_wp_deprecated_default()(`wp.editPost.${name}`, {
    since: "6.6",
    alternative: `wp.editor.${name}`
  });
};
function PluginBlockSettingsMenuItem(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot("PluginBlockSettingsMenuItem");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginBlockSettingsMenuItem, { ...props });
}
function PluginDocumentSettingPanel(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot("PluginDocumentSettingPanel");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginDocumentSettingPanel, { ...props });
}
function PluginMoreMenuItem(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot("PluginMoreMenuItem");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginMoreMenuItem, { ...props });
}
function PluginPrePublishPanel(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot("PluginPrePublishPanel");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginPrePublishPanel, { ...props });
}
function PluginPostPublishPanel(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot("PluginPostPublishPanel");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginPostPublishPanel, { ...props });
}
function PluginPostStatusInfo(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot("PluginPostStatusInfo");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginPostStatusInfo, { ...props });
}
function PluginSidebar(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot("PluginSidebar");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginSidebar, { ...props });
}
function PluginSidebarMoreMenuItem(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot("PluginSidebarMoreMenuItem");
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginSidebarMoreMenuItem, { ...props });
}
function __experimentalPluginPostExcerpt() {
  if (isSiteEditor) {
    return null;
  }
  external_wp_deprecated_default()("wp.editPost.__experimentalPluginPostExcerpt", {
    since: "6.6",
    hint: "Core and custom panels can be access programmatically using their panel name.",
    link: "https://developer.wordpress.org/block-editor/reference-guides/slotfills/plugin-document-setting-panel/#accessing-a-panel-programmatically"
  });
  return PluginPostExcerpt;
}


;// ./node_modules/@wordpress/edit-post/build-module/index.js











const {
  BackButton: __experimentalMainDashboardButton,
  registerCoreBlockBindingsSources
} = unlock(external_wp_editor_namespaceObject.privateApis);
function initializeEditor(id, postType, postId, settings, initialEdits) {
  const isMediumOrBigger = window.matchMedia("(min-width: 782px)").matches;
  const target = document.getElementById(id);
  const root = (0,external_wp_element_namespaceObject.createRoot)(target);
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults("core/edit-post", {
    fullscreenMode: true,
    themeStyles: true,
    welcomeGuide: true,
    welcomeGuideTemplate: true
  });
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults("core", {
    allowRightClickOverrides: true,
    editorMode: "visual",
    editorTool: "edit",
    fixedToolbar: false,
    hiddenBlockTypes: [],
    inactivePanels: [],
    openPanels: ["post-status"],
    showBlockBreadcrumbs: true,
    showIconLabels: false,
    showListViewByDefault: false,
    enableChoosePatternModal: true,
    isPublishSidebarEnabled: true
  });
  if (window.__experimentalMediaProcessing) {
    (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults("core/media", {
      requireApproval: true,
      optimizeOnUpload: true
    });
  }
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).reapplyBlockTypeFilters();
  if (isMediumOrBigger && (0,external_wp_data_namespaceObject.select)(external_wp_preferences_namespaceObject.store).get("core", "showListViewByDefault") && !(0,external_wp_data_namespaceObject.select)(external_wp_preferences_namespaceObject.store).get("core", "distractionFree")) {
    (0,external_wp_data_namespaceObject.dispatch)(external_wp_editor_namespaceObject.store).setIsListViewOpened(true);
  }
  (0,external_wp_blockLibrary_namespaceObject.registerCoreBlocks)();
  registerCoreBlockBindingsSources();
  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetBlock)({ inserter: false });
  (0,external_wp_widgets_namespaceObject.registerWidgetGroupBlock)({ inserter: false });
  if (false) {}
  const documentMode = document.compatMode === "CSS1Compat" ? "Standards" : "Quirks";
  if (documentMode !== "Standards") {
    console.warn(
      "Your browser is using Quirks Mode. \nThis can cause rendering issues such as blocks overlaying meta boxes in the editor. Quirks Mode can be triggered by PHP errors or HTML code appearing before the opening <!DOCTYPE html>. Try checking the raw page source or your site's PHP error log and resolving errors there, removing any HTML before the doctype, or disabling plugins."
    );
  }
  const isIphone = window.navigator.userAgent.indexOf("iPhone") !== -1;
  if (isIphone) {
    window.addEventListener("scroll", (event) => {
      const editorScrollContainer = document.getElementsByClassName(
        "interface-interface-skeleton__body"
      )[0];
      if (event.target === document) {
        if (window.scrollY > 100) {
          editorScrollContainer.scrollTop = editorScrollContainer.scrollTop + window.scrollY;
        }
        if (document.getElementsByClassName("is-mode-visual")[0]) {
          window.scrollTo(0, 0);
        }
      }
    });
  }
  window.addEventListener("dragover", (e) => e.preventDefault(), false);
  window.addEventListener("drop", (e) => e.preventDefault(), false);
  root.render(
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_element_namespaceObject.StrictMode, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      layout_default,
      {
        settings,
        postId,
        postType,
        initialEdits
      }
    ) })
  );
  return root;
}
function reinitializeEditor() {
  external_wp_deprecated_default()("wp.editPost.reinitializeEditor", {
    since: "6.2",
    version: "6.3"
  });
}





(window.wp = window.wp || {}).editPost = __webpack_exports__;
/******/ })()
;