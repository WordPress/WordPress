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
  __experimentalFullscreenModeClose: () => (/* reexport */ fullscreen_mode_close),
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

;// CONCATENATED MODULE: external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","blockLibrary"]
const external_wp_blockLibrary_namespaceObject = window["wp"]["blockLibrary"];
;// CONCATENATED MODULE: external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","preferences"]
const external_wp_preferences_namespaceObject = window["wp"]["preferences"];
;// CONCATENATED MODULE: external ["wp","widgets"]
const external_wp_widgets_namespaceObject = window["wp"]["widgets"];
;// CONCATENATED MODULE: external ["wp","editor"]
const external_wp_editor_namespaceObject = window["wp"]["editor"];
;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// CONCATENATED MODULE: external ["wp","commands"]
const external_wp_commands_namespaceObject = window["wp"]["commands"];
;// CONCATENATED MODULE: ./node_modules/clsx/dist/clsx.mjs
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const dist_clsx = (clsx);
;// CONCATENATED MODULE: external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: external ["wp","plugins"]
const external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
;// CONCATENATED MODULE: external ["wp","coreCommands"]
const external_wp_coreCommands_namespaceObject = window["wp"]["coreCommands"];
;// CONCATENATED MODULE: external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wp","htmlEntities"]
const external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// CONCATENATED MODULE: external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/wordpress.js
/**
 * WordPress dependencies
 */


const wordpress = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z"
  })
});
/* harmony default export */ const library_wordpress = (wordpress);

;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/reducer.js
/**
 * WordPress dependencies
 */


/**
 * Reducer keeping track of the meta boxes isSaving state.
 * A "true" value means the meta boxes saving request is in-flight.
 *
 *
 * @param {boolean} state  Previous state.
 * @param {Object}  action Action Object.
 *
 * @return {Object} Updated state.
 */
function isSavingMetaBoxes(state = false, action) {
  switch (action.type) {
    case 'REQUEST_META_BOX_UPDATES':
      return true;
    case 'META_BOX_UPDATES_SUCCESS':
    case 'META_BOX_UPDATES_FAILURE':
      return false;
    default:
      return state;
  }
}
function mergeMetaboxes(metaboxes = [], newMetaboxes) {
  const mergedMetaboxes = [...metaboxes];
  for (const metabox of newMetaboxes) {
    const existing = mergedMetaboxes.findIndex(box => box.id === metabox.id);
    if (existing !== -1) {
      mergedMetaboxes[existing] = metabox;
    } else {
      mergedMetaboxes.push(metabox);
    }
  }
  return mergedMetaboxes;
}

/**
 * Reducer keeping track of the meta boxes per location.
 *
 * @param {boolean} state  Previous state.
 * @param {Object}  action Action Object.
 *
 * @return {Object} Updated state.
 */
function metaBoxLocations(state = {}, action) {
  switch (action.type) {
    case 'SET_META_BOXES_PER_LOCATIONS':
      {
        const newState = {
          ...state
        };
        for (const [location, metaboxes] of Object.entries(action.metaBoxesPerLocation)) {
          newState[location] = mergeMetaboxes(newState[location], metaboxes);
        }
        return newState;
      }
  }
  return state;
}

/**
 * Reducer tracking whether meta boxes are initialized.
 *
 * @param {boolean} state
 * @param {Object}  action
 *
 * @return {boolean} Updated state.
 */
function metaBoxesInitialized(state = false, action) {
  switch (action.type) {
    case 'META_BOXES_INITIALIZED':
      return true;
  }
  return state;
}
const metaBoxes = (0,external_wp_data_namespaceObject.combineReducers)({
  isSaving: isSavingMetaBoxes,
  locations: metaBoxLocations,
  initialized: metaBoxesInitialized
});
/* harmony default export */ const reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  metaBoxes
}));

;// CONCATENATED MODULE: external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/utils/meta-boxes.js
/**
 * Function returning the current Meta Boxes DOM Node in the editor
 * whether the meta box area is opened or not.
 * If the MetaBox Area is visible returns it, and returns the original container instead.
 *
 * @param {string} location Meta Box location.
 *
 * @return {string} HTML content.
 */
const getMetaBoxContainer = location => {
  const area = document.querySelector(`.edit-post-meta-boxes-area.is-${location} .metabox-location-${location}`);
  if (area) {
    return area;
  }
  return document.querySelector('#metaboxes .metabox-location-' + location);
};

;// CONCATENATED MODULE: external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.', '@wordpress/edit-post');

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


const {
  interfaceStore
} = unlock(external_wp_editor_namespaceObject.privateApis);

/**
 * Returns an action object used in signalling that the user opened an editor sidebar.
 *
 * @param {?string} name Sidebar name to be opened.
 */
const openGeneralSidebar = name => ({
  registry
}) => {
  registry.dispatch(interfaceStore).enableComplementaryArea('core', name);
};

/**
 * Returns an action object signalling that the user closed the sidebar.
 */
const closeGeneralSidebar = () => ({
  registry
}) => registry.dispatch(interfaceStore).disableComplementaryArea('core');

/**
 * Returns an action object used in signalling that the user opened a modal.
 *
 * @deprecated since WP 6.3 use `core/interface` store's action with the same name instead.
 *
 *
 * @param {string} name A string that uniquely identifies the modal.
 *
 * @return {Object} Action object.
 */
const openModal = name => ({
  registry
}) => {
  external_wp_deprecated_default()("select( 'core/edit-post' ).openModal( name )", {
    since: '6.3',
    alternative: "select( 'core/interface').openModal( name )"
  });
  return registry.dispatch(interfaceStore).openModal(name);
};

/**
 * Returns an action object signalling that the user closed a modal.
 *
 * @deprecated since WP 6.3 use `core/interface` store's action with the same name instead.
 *
 * @return {Object} Action object.
 */
const closeModal = () => ({
  registry
}) => {
  external_wp_deprecated_default()("select( 'core/edit-post' ).closeModal()", {
    since: '6.3',
    alternative: "select( 'core/interface').closeModal()"
  });
  return registry.dispatch(interfaceStore).closeModal();
};

/**
 * Returns an action object used in signalling that the user opened the publish
 * sidebar.
 * @deprecated
 *
 * @return {Object} Action object
 */
const openPublishSidebar = () => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).openPublishSidebar", {
    since: '6.6',
    alternative: "dispatch( 'core/editor').openPublishSidebar"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).openPublishSidebar();
};

/**
 * Returns an action object used in signalling that the user closed the
 * publish sidebar.
 * @deprecated
 *
 * @return {Object} Action object.
 */
const closePublishSidebar = () => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).closePublishSidebar", {
    since: '6.6',
    alternative: "dispatch( 'core/editor').closePublishSidebar"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).closePublishSidebar();
};

/**
 * Returns an action object used in signalling that the user toggles the publish sidebar.
 * @deprecated
 *
 * @return {Object} Action object
 */
const togglePublishSidebar = () => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).togglePublishSidebar", {
    since: '6.6',
    alternative: "dispatch( 'core/editor').togglePublishSidebar"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).togglePublishSidebar();
};

/**
 * Returns an action object used to enable or disable a panel in the editor.
 *
 * @deprecated
 *
 * @param {string} panelName A string that identifies the panel to enable or disable.
 *
 * @return {Object} Action object.
 */
const toggleEditorPanelEnabled = panelName => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).toggleEditorPanelEnabled", {
    since: '6.5',
    alternative: "dispatch( 'core/editor').toggleEditorPanelEnabled"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).toggleEditorPanelEnabled(panelName);
};

/**
 * Opens a closed panel and closes an open panel.
 *
 * @deprecated
 *
 * @param {string} panelName A string that identifies the panel to open or close.
 */
const toggleEditorPanelOpened = panelName => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).toggleEditorPanelOpened", {
    since: '6.5',
    alternative: "dispatch( 'core/editor').toggleEditorPanelOpened"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).toggleEditorPanelOpened(panelName);
};

/**
 * Returns an action object used to remove a panel from the editor.
 *
 * @deprecated
 *
 * @param {string} panelName A string that identifies the panel to remove.
 *
 * @return {Object} Action object.
 */
const removeEditorPanel = panelName => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).removeEditorPanel", {
    since: '6.5',
    alternative: "dispatch( 'core/editor').removeEditorPanel"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).removeEditorPanel(panelName);
};

/**
 * Triggers an action used to toggle a feature flag.
 *
 * @param {string} feature Feature name.
 */
const toggleFeature = feature => ({
  registry
}) => registry.dispatch(external_wp_preferences_namespaceObject.store).toggle('core/edit-post', feature);

/**
 * Triggers an action used to switch editor mode.
 *
 * @deprecated
 *
 * @param {string} mode The editor mode.
 */
const switchEditorMode = mode => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).switchEditorMode", {
    since: '6.6',
    alternative: "dispatch( 'core/editor').switchEditorMode"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).switchEditorMode(mode);
};

/**
 * Triggers an action object used to toggle a plugin name flag.
 *
 * @param {string} pluginName Plugin name.
 */
const togglePinnedPluginItem = pluginName => ({
  registry
}) => {
  const isPinned = registry.select(interfaceStore).isItemPinned('core', pluginName);
  registry.dispatch(interfaceStore)[isPinned ? 'unpinItem' : 'pinItem']('core', pluginName);
};

/**
 * Returns an action object used in signaling that a style should be auto-applied when a block is created.
 *
 * @deprecated
 */
function updatePreferredStyleVariations() {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).updatePreferredStyleVariations", {
    since: '6.6',
    hint: 'Preferred Style Variations are not supported anymore.'
  });
  return {
    type: 'NOTHING'
  };
}

/**
 * Update the provided block types to be visible.
 *
 * @param {string[]} blockNames Names of block types to show.
 */
const showBlockTypes = blockNames => ({
  registry
}) => {
  unlock(registry.dispatch(external_wp_editor_namespaceObject.store)).showBlockTypes(blockNames);
};

/**
 * Update the provided block types to be hidden.
 *
 * @param {string[]} blockNames Names of block types to hide.
 */
const hideBlockTypes = blockNames => ({
  registry
}) => {
  unlock(registry.dispatch(external_wp_editor_namespaceObject.store)).hideBlockTypes(blockNames);
};

/**
 * Stores info about which Meta boxes are available in which location.
 *
 * @param {Object} metaBoxesPerLocation Meta boxes per location.
 */
function setAvailableMetaBoxesPerLocation(metaBoxesPerLocation) {
  return {
    type: 'SET_META_BOXES_PER_LOCATIONS',
    metaBoxesPerLocation
  };
}

/**
 * Update a metabox.
 */
const requestMetaBoxUpdates = () => async ({
  registry,
  select,
  dispatch
}) => {
  dispatch({
    type: 'REQUEST_META_BOX_UPDATES'
  });

  // Saves the wp_editor fields.
  if (window.tinyMCE) {
    window.tinyMCE.triggerSave();
  }

  // We gather the base form data.
  const baseFormData = new window.FormData(document.querySelector('.metabox-base-form'));
  const postId = baseFormData.get('post_ID');
  const postType = baseFormData.get('post_type');

  // Additional data needed for backward compatibility.
  // If we do not provide this data, the post will be overridden with the default values.
  // We cannot rely on getCurrentPost because right now on the editor we may be editing a pattern or a template.
  // We need to retrieve the post that the base form data is referring to.
  const post = registry.select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord('postType', postType, postId);
  const additionalData = [post.comment_status ? ['comment_status', post.comment_status] : false, post.ping_status ? ['ping_status', post.ping_status] : false, post.sticky ? ['sticky', post.sticky] : false, post.author ? ['post_author', post.author] : false].filter(Boolean);

  // We gather all the metaboxes locations.
  const activeMetaBoxLocations = select.getActiveMetaBoxLocations();
  const formDataToMerge = [baseFormData, ...activeMetaBoxLocations.map(location => new window.FormData(getMetaBoxContainer(location)))];

  // Merge all form data objects into a single one.
  const formData = formDataToMerge.reduce((memo, currentFormData) => {
    for (const [key, value] of currentFormData) {
      memo.append(key, value);
    }
    return memo;
  }, new window.FormData());
  additionalData.forEach(([key, value]) => formData.append(key, value));
  try {
    // Save the metaboxes.
    await external_wp_apiFetch_default()({
      url: window._wpMetaBoxUrl,
      method: 'POST',
      body: formData,
      parse: false
    });
    dispatch.metaBoxUpdatesSuccess();
  } catch {
    dispatch.metaBoxUpdatesFailure();
  }
};

/**
 * Returns an action object used to signal a successful meta box update.
 *
 * @return {Object} Action object.
 */
function metaBoxUpdatesSuccess() {
  return {
    type: 'META_BOX_UPDATES_SUCCESS'
  };
}

/**
 * Returns an action object used to signal a failed meta box update.
 *
 * @return {Object} Action object.
 */
function metaBoxUpdatesFailure() {
  return {
    type: 'META_BOX_UPDATES_FAILURE'
  };
}

/**
 * Action that changes the width of the editing canvas.
 *
 * @deprecated
 *
 * @param {string} deviceType
 */
const __experimentalSetPreviewDeviceType = deviceType => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).__experimentalSetPreviewDeviceType", {
    since: '6.5',
    version: '6.7',
    hint: 'registry.dispatch( editorStore ).setDeviceType'
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).setDeviceType(deviceType);
};

/**
 * Returns an action object used to open/close the inserter.
 *
 * @deprecated
 *
 * @param {boolean|Object} value Whether the inserter should be opened (true) or closed (false).
 */
const setIsInserterOpened = value => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).setIsInserterOpened", {
    since: '6.5',
    alternative: "dispatch( 'core/editor').setIsInserterOpened"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).setIsInserterOpened(value);
};

/**
 * Returns an action object used to open/close the list view.
 *
 * @deprecated
 *
 * @param {boolean} isOpen A boolean representing whether the list view should be opened or closed.
 */
const setIsListViewOpened = isOpen => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).setIsListViewOpened", {
    since: '6.5',
    alternative: "dispatch( 'core/editor').setIsListViewOpened"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).setIsListViewOpened(isOpen);
};

/**
 * Returns an action object used to switch to template editing.
 *
 * @deprecated
 */
function setIsEditingTemplate() {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).setIsEditingTemplate", {
    since: '6.5',
    alternative: "dispatch( 'core/editor').setRenderingMode"
  });
  return {
    type: 'NOTHING'
  };
}

/**
 * Create a block based template.
 *
 * @deprecated
 */
function __unstableCreateTemplate() {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).__unstableCreateTemplate", {
    since: '6.5'
  });
  return {
    type: 'NOTHING'
  };
}
let actions_metaBoxesInitialized = false;

/**
 * Initializes WordPress `postboxes` script and the logic for saving meta boxes.
 */
const initializeMetaBoxes = () => ({
  registry,
  select,
  dispatch
}) => {
  const isEditorReady = registry.select(external_wp_editor_namespaceObject.store).__unstableIsEditorReady();
  if (!isEditorReady) {
    return;
  }
  // Only initialize once.
  if (actions_metaBoxesInitialized) {
    return;
  }
  const postType = registry.select(external_wp_editor_namespaceObject.store).getCurrentPostType();
  if (window.postboxes.page !== postType) {
    window.postboxes.add_postbox_toggles(postType);
  }
  actions_metaBoxesInitialized = true;

  // Save metaboxes on save completion, except for autosaves.
  (0,external_wp_hooks_namespaceObject.addFilter)('editor.__unstableSavePost', 'core/edit-post/save-metaboxes', (previous, options) => previous.then(() => {
    if (options.isAutosave) {
      return;
    }
    if (!select.hasMetaBoxes()) {
      return;
    }
    return dispatch.requestMetaBoxUpdates();
  }));
  dispatch({
    type: 'META_BOXES_INITIALIZED'
  });
};

/**
 * Action that toggles Distraction free mode.
 * Distraction free mode expects there are no sidebars, as due to the
 * z-index values set, you can't close sidebars.
 *
 * @deprecated
 */
const toggleDistractionFree = () => ({
  registry
}) => {
  external_wp_deprecated_default()("dispatch( 'core/edit-post' ).toggleDistractionFree", {
    since: '6.6',
    alternative: "dispatch( 'core/editor').toggleDistractionFree"
  });
  registry.dispatch(external_wp_editor_namespaceObject.store).toggleDistractionFree();
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */

const {
  interfaceStore: selectors_interfaceStore
} = unlock(external_wp_editor_namespaceObject.privateApis);
const EMPTY_ARRAY = [];
const EMPTY_OBJECT = {};

/**
 * Returns the current editing mode.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Editing mode.
 */
const getEditorMode = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  var _select$get;
  return (_select$get = select(external_wp_preferences_namespaceObject.store).get('core', 'editorMode')) !== null && _select$get !== void 0 ? _select$get : 'visual';
});

/**
 * Returns true if the editor sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the editor sidebar is opened.
 */
const isEditorSidebarOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  const activeGeneralSidebar = select(selectors_interfaceStore).getActiveComplementaryArea('core');
  return ['edit-post/document', 'edit-post/block'].includes(activeGeneralSidebar);
});

/**
 * Returns true if the plugin sidebar is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the plugin sidebar is opened.
 */
const isPluginSidebarOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  const activeGeneralSidebar = select(selectors_interfaceStore).getActiveComplementaryArea('core');
  return !!activeGeneralSidebar && !['edit-post/document', 'edit-post/block'].includes(activeGeneralSidebar);
});

/**
 * Returns the current active general sidebar name, or null if there is no
 * general sidebar active. The active general sidebar is a unique name to
 * identify either an editor or plugin sidebar.
 *
 * Examples:
 *
 *  - `edit-post/document`
 *  - `my-plugin/insert-image-sidebar`
 *
 * @param {Object} state Global application state.
 *
 * @return {?string} Active general sidebar name.
 */
const getActiveGeneralSidebarName = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  return select(selectors_interfaceStore).getActiveComplementaryArea('core');
});

/**
 * Converts panels from the new preferences store format to the old format
 * that the post editor previously used.
 *
 * The resultant converted data should look like this:
 * {
 *     panelName: {
 *         enabled: false,
 *         opened: true,
 *     },
 *     anotherPanelName: {
 *         opened: true
 *     },
 * }
 *
 * @param {string[] | undefined} inactivePanels An array of inactive panel names.
 * @param {string[] | undefined} openPanels     An array of open panel names.
 *
 * @return {Object} The converted panel data.
 */
function convertPanelsToOldFormat(inactivePanels, openPanels) {
  var _ref;
  // First reduce the inactive panels.
  const panelsWithEnabledState = inactivePanels?.reduce((accumulatedPanels, panelName) => ({
    ...accumulatedPanels,
    [panelName]: {
      enabled: false
    }
  }), {});

  // Then reduce the open panels, passing in the result of the previous
  // reduction as the initial value so that both open and inactive
  // panel state is combined.
  const panels = openPanels?.reduce((accumulatedPanels, panelName) => {
    const currentPanelState = accumulatedPanels?.[panelName];
    return {
      ...accumulatedPanels,
      [panelName]: {
        ...currentPanelState,
        opened: true
      }
    };
  }, panelsWithEnabledState !== null && panelsWithEnabledState !== void 0 ? panelsWithEnabledState : {});

  // The panels variable will only be set if openPanels wasn't `undefined`.
  // If it isn't set just return `panelsWithEnabledState`, and if that isn't
  // set return an empty object.
  return (_ref = panels !== null && panels !== void 0 ? panels : panelsWithEnabledState) !== null && _ref !== void 0 ? _ref : EMPTY_OBJECT;
}

/**
 * Returns the preferences (these preferences are persisted locally).
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Preferences Object.
 */
const getPreferences = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).getPreferences`, {
    since: '6.0',
    alternative: `select( 'core/preferences' ).get`
  });
  const corePreferences = ['editorMode', 'hiddenBlockTypes'].reduce((accumulatedPrefs, preferenceKey) => {
    const value = select(external_wp_preferences_namespaceObject.store).get('core', preferenceKey);
    return {
      ...accumulatedPrefs,
      [preferenceKey]: value
    };
  }, {});

  // Panels were a preference, but the data structure changed when the state
  // was migrated to the preferences store. They need to be converted from
  // the new preferences store format to old format to ensure no breaking
  // changes for plugins.
  const inactivePanels = select(external_wp_preferences_namespaceObject.store).get('core', 'inactivePanels');
  const openPanels = select(external_wp_preferences_namespaceObject.store).get('core', 'openPanels');
  const panels = convertPanelsToOldFormat(inactivePanels, openPanels);
  return {
    ...corePreferences,
    panels
  };
});

/**
 *
 * @param {Object} state         Global application state.
 * @param {string} preferenceKey Preference Key.
 * @param {*}      defaultValue  Default Value.
 *
 * @return {*} Preference Value.
 */
function getPreference(state, preferenceKey, defaultValue) {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).getPreference`, {
    since: '6.0',
    alternative: `select( 'core/preferences' ).get`
  });

  // Avoid using the `getPreferences` registry selector where possible.
  const preferences = getPreferences(state);
  const value = preferences[preferenceKey];
  return value === undefined ? defaultValue : value;
}

/**
 * Returns an array of blocks that are hidden.
 *
 * @return {Array} A list of the hidden block types
 */
const getHiddenBlockTypes = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  var _select$get2;
  return (_select$get2 = select(external_wp_preferences_namespaceObject.store).get('core', 'hiddenBlockTypes')) !== null && _select$get2 !== void 0 ? _select$get2 : EMPTY_ARRAY;
});

/**
 * Returns true if the publish sidebar is opened.
 *
 * @deprecated
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the publish sidebar is open.
 */
const isPublishSidebarOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isPublishSidebarOpened`, {
    since: '6.6',
    alternative: `select( 'core/editor' ).isPublishSidebarOpened`
  });
  return select(external_wp_editor_namespaceObject.store).isPublishSidebarOpened();
});

/**
 * Returns true if the given panel was programmatically removed, or false otherwise.
 * All panels are not removed by default.
 *
 * @deprecated
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is removed.
 */
const isEditorPanelRemoved = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, panelName) => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isEditorPanelRemoved`, {
    since: '6.5',
    alternative: `select( 'core/editor' ).isEditorPanelRemoved`
  });
  return select(external_wp_editor_namespaceObject.store).isEditorPanelRemoved(panelName);
});

/**
 * Returns true if the given panel is enabled, or false otherwise. Panels are
 * enabled by default.
 *
 * @deprecated
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is enabled.
 */
const isEditorPanelEnabled = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, panelName) => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isEditorPanelEnabled`, {
    since: '6.5',
    alternative: `select( 'core/editor' ).isEditorPanelEnabled`
  });
  return select(external_wp_editor_namespaceObject.store).isEditorPanelEnabled(panelName);
});

/**
 * Returns true if the given panel is open, or false otherwise. Panels are
 * closed by default.
 *
 * @deprecated
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is open.
 */
const isEditorPanelOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, panelName) => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isEditorPanelOpened`, {
    since: '6.5',
    alternative: `select( 'core/editor' ).isEditorPanelOpened`
  });
  return select(external_wp_editor_namespaceObject.store).isEditorPanelOpened(panelName);
});

/**
 * Returns true if a modal is active, or false otherwise.
 *
 * @deprecated since WP 6.3 use `core/interface` store's selector with the same name instead.
 *
 * @param {Object} state     Global application state.
 * @param {string} modalName A string that uniquely identifies the modal.
 *
 * @return {boolean} Whether the modal is active.
 */
const isModalActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, modalName) => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isModalActive`, {
    since: '6.3',
    alternative: `select( 'core/interface' ).isModalActive`
  });
  return !!select(selectors_interfaceStore).isModalActive(modalName);
});

/**
 * Returns whether the given feature is enabled or not.
 *
 * @param {Object} state   Global application state.
 * @param {string} feature Feature slug.
 *
 * @return {boolean} Is active.
 */
const isFeatureActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, feature) => {
  return !!select(external_wp_preferences_namespaceObject.store).get('core/edit-post', feature);
});

/**
 * Returns true if the plugin item is pinned to the header.
 * When the value is not set it defaults to true.
 *
 * @param {Object} state      Global application state.
 * @param {string} pluginName Plugin item name.
 *
 * @return {boolean} Whether the plugin item is pinned.
 */
const isPluginItemPinned = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, pluginName) => {
  return select(selectors_interfaceStore).isItemPinned('core', pluginName);
});

/**
 * Returns an array of active meta box locations.
 *
 * @param {Object} state Post editor state.
 *
 * @return {string[]} Active meta box locations.
 */
const getActiveMetaBoxLocations = (0,external_wp_data_namespaceObject.createSelector)(state => {
  return Object.keys(state.metaBoxes.locations).filter(location => isMetaBoxLocationActive(state, location));
}, state => [state.metaBoxes.locations]);

/**
 * Returns true if a metabox location is active and visible
 *
 * @param {Object} state    Post editor state.
 * @param {string} location Meta box location to test.
 *
 * @return {boolean} Whether the meta box location is active and visible.
 */
const isMetaBoxLocationVisible = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, location) => {
  return isMetaBoxLocationActive(state, location) && getMetaBoxesPerLocation(state, location)?.some(({
    id
  }) => {
    return select(external_wp_editor_namespaceObject.store).isEditorPanelEnabled(state, `meta-box-${id}`);
  });
});

/**
 * Returns true if there is an active meta box in the given location, or false
 * otherwise.
 *
 * @param {Object} state    Post editor state.
 * @param {string} location Meta box location to test.
 *
 * @return {boolean} Whether the meta box location is active.
 */
function isMetaBoxLocationActive(state, location) {
  const metaBoxes = getMetaBoxesPerLocation(state, location);
  return !!metaBoxes && metaBoxes.length !== 0;
}

/**
 * Returns the list of all the available meta boxes for a given location.
 *
 * @param {Object} state    Global application state.
 * @param {string} location Meta box location to test.
 *
 * @return {?Array} List of meta boxes.
 */
function getMetaBoxesPerLocation(state, location) {
  return state.metaBoxes.locations[location];
}

/**
 * Returns the list of all the available meta boxes.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} List of meta boxes.
 */
const getAllMetaBoxes = (0,external_wp_data_namespaceObject.createSelector)(state => {
  return Object.values(state.metaBoxes.locations).flat();
}, state => [state.metaBoxes.locations]);

/**
 * Returns true if the post is using Meta Boxes
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether there are metaboxes or not.
 */
function hasMetaBoxes(state) {
  return getActiveMetaBoxLocations(state).length > 0;
}

/**
 * Returns true if the Meta Boxes are being saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the metaboxes are being saved.
 */
function selectors_isSavingMetaBoxes(state) {
  return state.metaBoxes.isSaving;
}

/**
 * Returns the current editing canvas device type.
 *
 * @deprecated
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Device type.
 */
const __experimentalGetPreviewDeviceType = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  external_wp_deprecated_default()(`select( 'core/edit-site' ).__experimentalGetPreviewDeviceType`, {
    since: '6.5',
    version: '6.7',
    alternative: `select( 'core/editor' ).getDeviceType`
  });
  return select(external_wp_editor_namespaceObject.store).getDeviceType();
});

/**
 * Returns true if the inserter is opened.
 *
 * @deprecated
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the inserter is opened.
 */
const isInserterOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isInserterOpened`, {
    since: '6.5',
    alternative: `select( 'core/editor' ).isInserterOpened`
  });
  return select(external_wp_editor_namespaceObject.store).isInserterOpened();
});

/**
 * Get the insertion point for the inserter.
 *
 * @deprecated
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The root client ID, index to insert at and starting filter value.
 */
const __experimentalGetInsertionPoint = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).__experimentalGetInsertionPoint`, {
    since: '6.5',
    version: '6.7'
  });
  return unlock(select(external_wp_editor_namespaceObject.store)).getInsertionPoint();
});

/**
 * Returns true if the list view is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the list view is opened.
 */
const isListViewOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isListViewOpened`, {
    since: '6.5',
    alternative: `select( 'core/editor' ).isListViewOpened`
  });
  return select(external_wp_editor_namespaceObject.store).isListViewOpened();
});

/**
 * Returns true if the template editing mode is enabled.
 *
 * @deprecated
 */
const isEditingTemplate = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isEditingTemplate`, {
    since: '6.5',
    alternative: `select( 'core/editor' ).getRenderingMode`
  });
  return select(external_wp_editor_namespaceObject.store).getCurrentPostType() === 'wp_template';
});

/**
 * Returns true if meta boxes are initialized.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether meta boxes are initialized.
 */
function areMetaBoxesInitialized(state) {
  return state.metaBoxes.initialized;
}

/**
 * Retrieves the template of the currently edited post.
 *
 * @return {Object?} Post Template.
 */
const getEditedPostTemplate = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  const {
    id: postId,
    type: postType,
    slug
  } = select(external_wp_editor_namespaceObject.store).getCurrentPost();
  const {
    getSite,
    getEditedEntityRecord,
    getEntityRecords
  } = select(external_wp_coreData_namespaceObject.store);
  const siteSettings = getSite();
  // First check if the current page is set as the posts page.
  const isPostsPage = +postId === siteSettings?.page_for_posts;
  if (isPostsPage) {
    const defaultTemplateId = select(external_wp_coreData_namespaceObject.store).getDefaultTemplateId({
      slug: 'home'
    });
    return getEditedEntityRecord('postType', 'wp_template', defaultTemplateId);
  }
  const currentTemplate = select(external_wp_editor_namespaceObject.store).getEditedPostAttribute('template');
  if (currentTemplate) {
    const templateWithSameSlug = getEntityRecords('postType', 'wp_template', {
      per_page: -1
    })?.find(template => template.slug === currentTemplate);
    if (!templateWithSameSlug) {
      return templateWithSameSlug;
    }
    return getEditedEntityRecord('postType', 'wp_template', templateWithSameSlug.id);
  }
  let slugToCheck;
  // In `draft` status we might not have a slug available, so we use the `single`
  // post type templates slug(ex page, single-post, single-product etc..).
  // Pages do not need the `single` prefix in the slug to be prioritized
  // through template hierarchy.
  if (slug) {
    slugToCheck = postType === 'page' ? `${postType}-${slug}` : `single-${postType}-${slug}`;
  } else {
    slugToCheck = postType === 'page' ? 'page' : `single-${postType}`;
  }
  const defaultTemplateId = select(external_wp_coreData_namespaceObject.store).getDefaultTemplateId({
    slug: slugToCheck
  });
  return defaultTemplateId ? select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord('postType', 'wp_template', defaultTemplateId) : null;
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/edit-post';

/**
 * CSS selector string for the admin bar view post link anchor tag.
 *
 * @type {string}
 */
const VIEW_AS_LINK_SELECTOR = '#wp-admin-bar-view a';

/**
 * CSS selector string for the admin bar preview post link anchor tag.
 *
 * @type {string}
 */
const VIEW_AS_PREVIEW_LINK_SELECTOR = '#wp-admin-bar-preview a';

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





/**
 * Store definition for the edit post namespace.
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/back-button/fullscreen-mode-close.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */


function FullscreenModeClose({
  showTooltip,
  icon,
  href,
  initialPost
}) {
  var _postType$labels$view;
  const {
    isActive,
    isRequestingSiteIcon,
    postType,
    siteIconUrl
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getCurrentPostType
    } = select(external_wp_editor_namespaceObject.store);
    const {
      isFeatureActive
    } = select(store);
    const {
      getEntityRecord,
      getPostType,
      isResolving
    } = select(external_wp_coreData_namespaceObject.store);
    const siteData = getEntityRecord('root', '__unstableBase', undefined) || {};
    const _postType = initialPost?.type || getCurrentPostType();
    return {
      isActive: isFeatureActive('fullscreenMode'),
      isRequestingSiteIcon: isResolving('getEntityRecord', ['root', '__unstableBase', undefined]),
      postType: getPostType(_postType),
      siteIconUrl: siteData.site_icon_url
    };
  }, []);
  const disableMotion = (0,external_wp_compose_namespaceObject.useReducedMotion)();
  if (!isActive || !postType) {
    return null;
  }
  let buttonIcon = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Icon, {
    size: "36px",
    icon: library_wordpress
  });
  const effect = {
    expand: {
      scale: 1.25,
      transition: {
        type: 'tween',
        duration: '0.3'
      }
    }
  };
  if (siteIconUrl) {
    buttonIcon = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__unstableMotion.img, {
      variants: !disableMotion && effect,
      alt: (0,external_wp_i18n_namespaceObject.__)('Site Icon'),
      className: "edit-post-fullscreen-mode-close_site-icon",
      src: siteIconUrl
    });
  }
  if (isRequestingSiteIcon) {
    buttonIcon = null;
  }

  // Override default icon if custom icon is provided via props.
  if (icon) {
    buttonIcon = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Icon, {
      size: "36px",
      icon: icon
    });
  }
  const classes = dist_clsx({
    'edit-post-fullscreen-mode-close': true,
    'has-icon': siteIconUrl
  });
  const buttonHref = href !== null && href !== void 0 ? href : (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
    post_type: postType.slug
  });
  const buttonLabel = (_postType$labels$view = postType?.labels?.view_items) !== null && _postType$labels$view !== void 0 ? _postType$labels$view : (0,external_wp_i18n_namespaceObject.__)('Back');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__unstableMotion.div, {
    whileHover: "expand",
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
      className: classes,
      href: buttonHref,
      label: buttonLabel,
      showTooltip: showTooltip,
      children: buttonIcon
    })
  });
}
/* harmony default export */ const fullscreen_mode_close = (FullscreenModeClose);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/back-button/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



const {
  BackButton: BackButtonFill
} = unlock(external_wp_editor_namespaceObject.privateApis);
const slideX = {
  hidden: {
    x: '-100%'
  },
  distractionFreeInactive: {
    x: 0
  },
  hover: {
    x: 0,
    transition: {
      type: 'tween',
      delay: 0.2
    }
  }
};
function BackButton({
  initialPost
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(BackButtonFill, {
    children: ({
      length
    }) => length <= 1 && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__unstableMotion.div, {
      variants: slideX,
      transition: {
        type: 'tween',
        delay: 0.8
      },
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(fullscreen_mode_close, {
        showTooltip: true,
        initialPost: initialPost
      })
    })
  });
}
/* harmony default export */ const back_button = (BackButton);

;// CONCATENATED MODULE: external ["wp","keyboardShortcuts"]
const external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */

function KeyboardShortcuts() {
  const {
    toggleFeature
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const {
    registerShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: 'core/edit-post/toggle-fullscreen',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Toggle fullscreen mode.'),
      keyCombination: {
        modifier: 'secondary',
        character: 'f'
      }
    });
  }, []);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-fullscreen', () => {
    toggleFeature('fullscreenMode');
  });
  return null;
}
/* harmony default export */ const keyboard_shortcuts = (KeyboardShortcuts);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/init-pattern-modal/index.js
/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */





const {
  ReusableBlocksRenameHint
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function InitPatternModal() {
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_editor_namespaceObject.store);
  const [isModalOpen, setIsModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  const [syncType, setSyncType] = (0,external_wp_element_namespaceObject.useState)(undefined);
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)('');
  const {
    postType,
    isNewPost
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute,
      isCleanNewPost
    } = select(external_wp_editor_namespaceObject.store);
    return {
      postType: getEditedPostAttribute('type'),
      isNewPost: isCleanNewPost()
    };
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isNewPost && postType === 'wp_block') {
      setIsModalOpen(true);
    }
    // We only want the modal to open when the page is first loaded.
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
  if (postType !== 'wp_block' || !isNewPost) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: isModalOpen && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Modal, {
      title: (0,external_wp_i18n_namespaceObject.__)('Create pattern'),
      onRequestClose: () => {
        setIsModalOpen(false);
      },
      overlayClassName: "reusable-blocks-menu-items__convert-modal",
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("form", {
        onSubmit: event => {
          event.preventDefault();
          setIsModalOpen(false);
          editPost({
            title,
            meta: {
              wp_pattern_sync_status: syncType
            }
          });
        },
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, {
          spacing: "5",
          children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.TextControl, {
            label: (0,external_wp_i18n_namespaceObject.__)('Name'),
            value: title,
            onChange: setTitle,
            placeholder: (0,external_wp_i18n_namespaceObject.__)('My pattern'),
            className: "patterns-create-modal__name-input",
            __nextHasNoMarginBottom: true,
            __next40pxDefaultSize: true
          }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ReusableBlocksRenameHint, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToggleControl, {
            label: (0,external_wp_i18n_namespaceObject._x)('Synced', 'pattern (singular)'),
            help: (0,external_wp_i18n_namespaceObject.__)('Sync this pattern across multiple locations.'),
            checked: !syncType,
            onChange: () => {
              setSyncType(!syncType ? 'unsynced' : undefined);
            }
          }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalHStack, {
            justify: "right",
            children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
              variant: "primary",
              type: "submit",
              disabled: !title,
              __experimentalIsFocusable: true,
              children: (0,external_wp_i18n_namespaceObject.__)('Create')
            })
          })]
        })
      })
    })
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/browser-url/index.js
/**
 * WordPress dependencies
 */





/**
 * Returns the Post's Edit URL.
 *
 * @param {number} postId Post ID.
 *
 * @return {string} Post edit URL.
 */
function getPostEditURL(postId) {
  return (0,external_wp_url_namespaceObject.addQueryArgs)('post.php', {
    post: postId,
    action: 'edit'
  });
}

/**
 * Returns the Post's Trashed URL.
 *
 * @param {number} postId   Post ID.
 * @param {string} postType Post Type.
 *
 * @return {string} Post trashed URL.
 */
function getPostTrashedURL(postId, postType) {
  return (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
    trashed: 1,
    post_type: postType,
    ids: postId
  });
}
class BrowserURL extends external_wp_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.state = {
      historyId: null
    };
  }
  componentDidUpdate(prevProps) {
    const {
      postId,
      postStatus,
      postType,
      isSavingPost,
      hasHistory
    } = this.props;
    const {
      historyId
    } = this.state;

    // Posts are still dirty while saving so wait for saving to finish
    // to avoid the unsaved changes warning when trashing posts.
    if (postStatus === 'trash' && !isSavingPost) {
      this.setTrashURL(postId, postType);
      return;
    }
    if ((postId !== prevProps.postId || postId !== historyId) && postStatus !== 'auto-draft' && postId && !hasHistory) {
      this.setBrowserURL(postId);
    }
  }

  /**
   * Navigates the browser to the post trashed URL to show a notice about the trashed post.
   *
   * @param {number} postId   Post ID.
   * @param {string} postType Post Type.
   */
  setTrashURL(postId, postType) {
    window.location.href = getPostTrashedURL(postId, postType);
  }

  /**
   * Replaces the browser URL with a post editor link for the given post ID.
   *
   * Note it is important that, since this function may be called when the
   * editor first loads, the result generated `getPostEditURL` matches that
   * produced by the server. Otherwise, the URL will change unexpectedly.
   *
   * @param {number} postId Post ID for which to generate post editor URL.
   */
  setBrowserURL(postId) {
    window.history.replaceState({
      id: postId
    }, 'Post ' + postId, getPostEditURL(postId));
    this.setState(() => ({
      historyId: postId
    }));
  }
  render() {
    return null;
  }
}
/* harmony default export */ const browser_url = ((0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getCurrentPost,
    isSavingPost
  } = select(external_wp_editor_namespaceObject.store);
  const post = getCurrentPost();
  let {
    id,
    status,
    type
  } = post;
  const isTemplate = ['wp_template', 'wp_template_part'].includes(type);
  if (isTemplate) {
    id = post.wp_id;
  }
  return {
    postId: id,
    postStatus: status,
    postType: type,
    isSavingPost: isSavingPost()
  };
})(BrowserURL));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/**
 * Render metabox area.
 *
 * @param {Object} props          Component props.
 * @param {string} props.location metabox location.
 * @return {Component} The component to be rendered.
 */


function MetaBoxesArea({
  location
}) {
  const container = (0,external_wp_element_namespaceObject.useRef)(null);
  const formRef = (0,external_wp_element_namespaceObject.useRef)(null);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    formRef.current = document.querySelector('.metabox-location-' + location);
    if (formRef.current) {
      container.current.appendChild(formRef.current);
    }
    return () => {
      if (formRef.current) {
        document.querySelector('#metaboxes').appendChild(formRef.current);
      }
    };
  }, [location]);
  const isSaving = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return select(store).isSavingMetaBoxes();
  }, []);
  const classes = dist_clsx('edit-post-meta-boxes-area', `is-${location}`, {
    'is-loading': isSaving
  });
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
    className: classes,
    children: [isSaving && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "edit-post-meta-boxes-area__container",
      ref: container
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "edit-post-meta-boxes-area__clear"
    })]
  });
}
/* harmony default export */ const meta_boxes_area = (MetaBoxesArea);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-box-visibility.js
/**
 * WordPress dependencies
 */



class MetaBoxVisibility extends external_wp_element_namespaceObject.Component {
  componentDidMount() {
    this.updateDOM();
  }
  componentDidUpdate(prevProps) {
    if (this.props.isVisible !== prevProps.isVisible) {
      this.updateDOM();
    }
  }
  updateDOM() {
    const {
      id,
      isVisible
    } = this.props;
    const element = document.getElementById(id);
    if (!element) {
      return;
    }
    if (isVisible) {
      element.classList.remove('is-hidden');
    } else {
      element.classList.add('is-hidden');
    }
  }
  render() {
    return null;
  }
}
/* harmony default export */ const meta_box_visibility = ((0,external_wp_data_namespaceObject.withSelect)((select, {
  id
}) => ({
  isVisible: select(external_wp_editor_namespaceObject.store).isEditorPanelEnabled(`meta-box-${id}`)
}))(MetaBoxVisibility));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */






function MetaBoxes({
  location
}) {
  const registry = (0,external_wp_data_namespaceObject.useRegistry)();
  const {
    metaBoxes,
    areMetaBoxesInitialized,
    isEditorReady
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      __unstableIsEditorReady
    } = select(external_wp_editor_namespaceObject.store);
    const {
      getMetaBoxesPerLocation,
      areMetaBoxesInitialized: _areMetaBoxesInitialized
    } = select(store);
    return {
      metaBoxes: getMetaBoxesPerLocation(location),
      areMetaBoxesInitialized: _areMetaBoxesInitialized(),
      isEditorReady: __unstableIsEditorReady()
    };
  }, [location]);
  const hasMetaBoxes = !!metaBoxes?.length;

  // When editor is ready, initialize postboxes (wp core script) and metabox
  // saving. This initializes all meta box locations, not just this specific
  // one.
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isEditorReady && hasMetaBoxes && !areMetaBoxesInitialized) {
      registry.dispatch(store).initializeMetaBoxes();
    }
  }, [isEditorReady, hasMetaBoxes, areMetaBoxesInitialized]);
  if (!areMetaBoxesInitialized) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [(metaBoxes !== null && metaBoxes !== void 0 ? metaBoxes : []).map(({
      id
    }) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(meta_box_visibility, {
      id: id
    }, id)), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(meta_boxes_area, {
      location: location
    })]
  });
}

;// CONCATENATED MODULE: external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/more-menu/manage-patterns-menu-item.js
/**
 * WordPress dependencies
 */






function ManagePatternsMenuItem() {
  const url = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      canUser
    } = select(external_wp_coreData_namespaceObject.store);
    const defaultUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
      post_type: 'wp_block'
    });
    const patternsUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('site-editor.php', {
      path: '/patterns'
    });

    // The site editor and templates both check whether the user has
    // edit_theme_options capabilities. We can leverage that here and not
    // display the manage patterns link if the user can't access it.
    return canUser('create', 'templates') ? patternsUrl : defaultUrl;
  }, []);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, {
    role: "menuitem",
    href: url,
    children: (0,external_wp_i18n_namespaceObject.__)('Manage patterns')
  });
}
/* harmony default export */ const manage_patterns_menu_item = (ManagePatternsMenuItem);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/more-menu/welcome-guide-menu-item.js
/**
 * WordPress dependencies
 */





function WelcomeGuideMenuItem() {
  const isEditingTemplate = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_editor_namespaceObject.store).getCurrentPostType() === 'wp_template', []);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    name: isEditingTemplate ? 'welcomeGuideTemplate' : 'welcomeGuide',
    label: (0,external_wp_i18n_namespaceObject.__)('Welcome Guide')
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/enable-custom-fields.js
/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */




const {
  PreferenceBaseOption
} = unlock(external_wp_preferences_namespaceObject.privateApis);
function submitCustomFieldsForm() {
  const customFieldsForm = document.getElementById('toggle-custom-fields-form');

  // Ensure the referrer values is up to update with any
  customFieldsForm.querySelector('[name="_wp_http_referer"]').setAttribute('value', (0,external_wp_url_namespaceObject.getPathAndQueryString)(window.location.href));
  customFieldsForm.submit();
}
function CustomFieldsConfirmation({
  willEnable
}) {
  const [isReloading, setIsReloading] = (0,external_wp_element_namespaceObject.useState)(false);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
      className: "edit-post-preferences-modal__custom-fields-confirmation-message",
      children: (0,external_wp_i18n_namespaceObject.__)('A page reload is required for this change. Make sure your content is saved before reloading.')
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
      className: "edit-post-preferences-modal__custom-fields-confirmation-button",
      variant: "secondary",
      isBusy: isReloading,
      __experimentalIsFocusable: true,
      disabled: isReloading,
      onClick: () => {
        setIsReloading(true);
        submitCustomFieldsForm();
      },
      children: willEnable ? (0,external_wp_i18n_namespaceObject.__)('Show & Reload Page') : (0,external_wp_i18n_namespaceObject.__)('Hide & Reload Page')
    })]
  });
}
function EnableCustomFieldsOption({
  label,
  areCustomFieldsEnabled
}) {
  const [isChecked, setIsChecked] = (0,external_wp_element_namespaceObject.useState)(areCustomFieldsEnabled);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(PreferenceBaseOption, {
    label: label,
    isChecked: isChecked,
    onChange: setIsChecked,
    children: isChecked !== areCustomFieldsEnabled && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(CustomFieldsConfirmation, {
      willEnable: isChecked
    })
  });
}
/* harmony default export */ const enable_custom_fields = ((0,external_wp_data_namespaceObject.withSelect)(select => ({
  areCustomFieldsEnabled: !!select(external_wp_editor_namespaceObject.store).getEditorSettings().enableCustomFields
}))(EnableCustomFieldsOption));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/enable-panel.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */

const {
  PreferenceBaseOption: enable_panel_PreferenceBaseOption
} = unlock(external_wp_preferences_namespaceObject.privateApis);
/* harmony default export */ const enable_panel = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, {
  panelName
}) => {
  const {
    isEditorPanelEnabled,
    isEditorPanelRemoved
  } = select(external_wp_editor_namespaceObject.store);
  return {
    isRemoved: isEditorPanelRemoved(panelName),
    isChecked: isEditorPanelEnabled(panelName)
  };
}), (0,external_wp_compose_namespaceObject.ifCondition)(({
  isRemoved
}) => !isRemoved), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  panelName
}) => ({
  onChange: () => dispatch(external_wp_editor_namespaceObject.store).toggleEditorPanelEnabled(panelName)
})))(enable_panel_PreferenceBaseOption));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/meta-boxes-section.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */






const {
  PreferencesModalSection
} = unlock(external_wp_preferences_namespaceObject.privateApis);
function MetaBoxesSection({
  areCustomFieldsRegistered,
  metaBoxes,
  ...sectionProps
}) {
  // The 'Custom Fields' meta box is a special case that we handle separately.
  const thirdPartyMetaBoxes = metaBoxes.filter(({
    id
  }) => id !== 'postcustom');
  if (!areCustomFieldsRegistered && thirdPartyMetaBoxes.length === 0) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(PreferencesModalSection, {
    ...sectionProps,
    children: [areCustomFieldsRegistered && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(enable_custom_fields, {
      label: (0,external_wp_i18n_namespaceObject.__)('Custom fields')
    }), thirdPartyMetaBoxes.map(({
      id,
      title
    }) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(enable_panel, {
      label: title,
      panelName: `meta-box-${id}`
    }, id))]
  });
}
/* harmony default export */ const meta_boxes_section = ((0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getEditorSettings
  } = select(external_wp_editor_namespaceObject.store);
  const {
    getAllMetaBoxes
  } = select(store);
  return {
    // This setting should not live in the block editor's store.
    areCustomFieldsRegistered: getEditorSettings().enableCustomFields !== undefined,
    metaBoxes: getAllMetaBoxes()
  };
})(MetaBoxesSection));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/index.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



const {
  PreferenceToggleControl
} = unlock(external_wp_preferences_namespaceObject.privateApis);
const {
  PreferencesModal
} = unlock(external_wp_editor_namespaceObject.privateApis);
function EditPostPreferencesModal() {
  const extraSections = {
    general: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(meta_boxes_section, {
      title: (0,external_wp_i18n_namespaceObject.__)('Advanced')
    }),
    appearance: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(PreferenceToggleControl, {
      scope: "core/edit-post",
      featureName: "themeStyles",
      help: (0,external_wp_i18n_namespaceObject.__)('Make the editor look like your theme.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Use theme styles')
    })
  };
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(PreferencesModal, {
    extraSections: extraSections
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/more-menu/index.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */







const {
  ToolsMoreMenuGroup,
  ViewMoreMenuGroup
} = unlock(external_wp_editor_namespaceObject.privateApis);
const MoreMenu = () => {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('large');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [isLargeViewport && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ViewMoreMenuGroup, {
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
        scope: "core/edit-post",
        name: "fullscreenMode",
        label: (0,external_wp_i18n_namespaceObject.__)('Fullscreen mode'),
        info: (0,external_wp_i18n_namespaceObject.__)('Show and hide the admin user interface'),
        messageActivated: (0,external_wp_i18n_namespaceObject.__)('Fullscreen mode activated'),
        messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Fullscreen mode deactivated'),
        shortcut: external_wp_keycodes_namespaceObject.displayShortcut.secondary('f')
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(ToolsMoreMenuGroup, {
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(manage_patterns_menu_item, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideMenuItem, {})]
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(EditPostPreferencesModal, {})]
  });
};
/* harmony default export */ const more_menu = (MoreMenu);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/image.js


function WelcomeGuideImage({
  nonAnimatedSrc,
  animatedSrc
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("picture", {
    className: "edit-post-welcome-guide__image",
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("source", {
      srcSet: nonAnimatedSrc,
      media: "(prefers-reduced-motion: reduce)"
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("img", {
      src: animatedSrc,
      width: "312",
      height: "240",
      alt: ""
    })]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/default.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





function WelcomeGuideDefault() {
  const {
    toggleFeature
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Guide, {
    className: "edit-post-welcome-guide",
    contentLabel: (0,external_wp_i18n_namespaceObject.__)('Welcome to the block editor'),
    finishButtonText: (0,external_wp_i18n_namespaceObject.__)('Get started'),
    onFinish: () => toggleFeature('welcomeGuide'),
    pages: [{
      image: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.gif"
      }),
      content: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", {
          className: "edit-post-welcome-guide__heading",
          children: (0,external_wp_i18n_namespaceObject.__)('Welcome to the block editor')
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
          className: "edit-post-welcome-guide__text",
          children: (0,external_wp_i18n_namespaceObject.__)('In the WordPress editor, each paragraph, image, or video is presented as a distinct “block” of content.')
        })]
      })
    }, {
      image: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-editor.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-editor.gif"
      }),
      content: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", {
          className: "edit-post-welcome-guide__heading",
          children: (0,external_wp_i18n_namespaceObject.__)('Make each block your own')
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
          className: "edit-post-welcome-guide__text",
          children: (0,external_wp_i18n_namespaceObject.__)('Each block comes with its own set of controls for changing things like color, width, and alignment. These will show and hide automatically when you have a block selected.')
        })]
      })
    }, {
      image: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-library.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-library.gif"
      }),
      content: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", {
          className: "edit-post-welcome-guide__heading",
          children: (0,external_wp_i18n_namespaceObject.__)('Get to know the block library')
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
          className: "edit-post-welcome-guide__text",
          children: (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.__)('All of the blocks available to you live in the block library. You’ll find it wherever you see the <InserterIconImage /> icon.'), {
            InserterIconImage: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("img", {
              alt: (0,external_wp_i18n_namespaceObject.__)('inserter'),
              src: "data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 18 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='18' height='18' rx='2' fill='%231E1E1E'/%3E%3Cpath d='M9.22727 4V14M4 8.77273H14' stroke='white' stroke-width='1.5'/%3E%3C/svg%3E%0A"
            })
          })
        })]
      })
    }, {
      image: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.gif"
      }),
      content: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", {
          className: "edit-post-welcome-guide__heading",
          children: (0,external_wp_i18n_namespaceObject.__)('Learn how to use the block editor')
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("p", {
          className: "edit-post-welcome-guide__text",
          children: [(0,external_wp_i18n_namespaceObject.__)('New to the block editor? Want to learn more about using it? '), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ExternalLink, {
            href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/documentation/article/wordpress-block-editor/'),
            children: (0,external_wp_i18n_namespaceObject.__)("Here's a detailed guide.")
          })]
        })]
      })
    }]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/template.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */





function WelcomeGuideTemplate() {
  const {
    toggleFeature
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Guide, {
    className: "edit-template-welcome-guide",
    contentLabel: (0,external_wp_i18n_namespaceObject.__)('Welcome to the template editor'),
    finishButtonText: (0,external_wp_i18n_namespaceObject.__)('Get started'),
    onFinish: () => toggleFeature('welcomeGuideTemplate'),
    pages: [{
      image: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.gif"
      }),
      content: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", {
          className: "edit-post-welcome-guide__heading",
          children: (0,external_wp_i18n_namespaceObject.__)('Welcome to the template editor')
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
          className: "edit-post-welcome-guide__text",
          children: (0,external_wp_i18n_namespaceObject.__)('Templates help define the layout of the site. You can customize all aspects of your posts and pages using blocks and patterns in this editor.')
        })]
      })
    }]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function WelcomeGuide() {
  const {
    isActive,
    isEditingTemplate
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isFeatureActive
    } = select(store);
    const {
      getCurrentPostType
    } = select(external_wp_editor_namespaceObject.store);
    const _isEditingTemplate = getCurrentPostType() === 'wp_template';
    const feature = _isEditingTemplate ? 'welcomeGuideTemplate' : 'welcomeGuide';
    return {
      isActive: isFeatureActive(feature),
      isEditingTemplate: _isEditingTemplate
    };
  }, []);
  if (!isActive) {
    return null;
  }
  return isEditingTemplate ? /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideTemplate, {}) : /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuideDefault, {});
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/fullscreen.js
/**
 * WordPress dependencies
 */


const fullscreen = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M6 4a2 2 0 0 0-2 2v3h1.5V6a.5.5 0 0 1 .5-.5h3V4H6Zm3 14.5H6a.5.5 0 0 1-.5-.5v-3H4v3a2 2 0 0 0 2 2h3v-1.5Zm6 1.5v-1.5h3a.5.5 0 0 0 .5-.5v-3H20v3a2 2 0 0 1-2 2h-3Zm3-16a2 2 0 0 1 2 2v3h-1.5V6a.5.5 0 0 0-.5-.5h-3V4h3Z"
  })
});
/* harmony default export */ const library_fullscreen = (fullscreen);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/commands/use-commands.js
/**
 * WordPress dependencies
 */






function useCommands() {
  const {
    isFullscreen
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    return {
      isFullscreen: get('core/edit-post', 'fullscreenMode')
    };
  }, []);
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const {
    createInfoNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/toggle-fullscreen-mode',
    label: isFullscreen ? (0,external_wp_i18n_namespaceObject.__)('Exit fullscreen') : (0,external_wp_i18n_namespaceObject.__)('Enter fullscreen'),
    icon: library_fullscreen,
    callback: ({
      close
    }) => {
      toggle('core/edit-post', 'fullscreenMode');
      close();
      createInfoNotice(isFullscreen ? (0,external_wp_i18n_namespaceObject.__)('Fullscreen off.') : (0,external_wp_i18n_namespaceObject.__)('Fullscreen on.'), {
        id: 'core/edit-post/toggle-fullscreen-mode/notice',
        type: 'snackbar',
        actions: [{
          label: (0,external_wp_i18n_namespaceObject.__)('Undo'),
          onClick: () => {
            toggle('core/edit-post', 'fullscreenMode');
          }
        }]
      });
    }
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/use-padding-appender.js
/**
 * WordPress dependencies
 */




function usePaddingAppender() {
  const registry = (0,external_wp_data_namespaceObject.useRegistry)();
  return (0,external_wp_compose_namespaceObject.useRefEffect)(node => {
    function onMouseDown(event) {
      if (event.target !== node) {
        return;
      }
      const {
        ownerDocument
      } = node;
      const {
        defaultView
      } = ownerDocument;
      const paddingBottom = defaultView.parseInt(defaultView.getComputedStyle(node).paddingBottom, 10);
      if (!paddingBottom) {
        return;
      }

      // Only handle clicks under the last child.
      const lastChild = node.lastElementChild;
      if (!lastChild) {
        return;
      }
      const lastChildRect = lastChild.getBoundingClientRect();
      if (event.clientY < lastChildRect.bottom) {
        return;
      }
      event.preventDefault();
      const blockOrder = registry.select(external_wp_blockEditor_namespaceObject.store).getBlockOrder('');
      const lastBlockClientId = blockOrder[blockOrder.length - 1];

      // Do nothing when only default block appender is present.
      if (!lastBlockClientId) {
        return;
      }
      const lastBlock = registry.select(external_wp_blockEditor_namespaceObject.store).getBlock(lastBlockClientId);
      const {
        selectBlock,
        insertDefaultBlock
      } = registry.dispatch(external_wp_blockEditor_namespaceObject.store);
      if ((0,external_wp_blocks_namespaceObject.isUnmodifiedDefaultBlock)(lastBlock)) {
        selectBlock(lastBlockClientId);
      } else {
        insertDefaultBlock();
      }
    }
    node.addEventListener('mousedown', onMouseDown);
    return () => {
      node.removeEventListener('mousedown', onMouseDown);
    };
  }, [registry]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/use-should-iframe.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

const isGutenbergPlugin =  false ? 0 : false;
function useShouldIframe() {
  const {
    isBlockBasedTheme,
    hasV3BlocksOnly,
    isEditingTemplate,
    hasMetaBoxes
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings,
      getCurrentPostType
    } = select(external_wp_editor_namespaceObject.store);
    const {
      getBlockTypes
    } = select(external_wp_blocks_namespaceObject.store);
    const editorSettings = getEditorSettings();
    return {
      isBlockBasedTheme: editorSettings.__unstableIsBlockBasedTheme,
      hasV3BlocksOnly: getBlockTypes().every(type => {
        return type.apiVersion >= 3;
      }),
      isEditingTemplate: getCurrentPostType() === 'wp_template',
      hasMetaBoxes: select(store).hasMetaBoxes()
    };
  }, []);
  return (hasV3BlocksOnly || isGutenbergPlugin && isBlockBasedTheme) && !hasMetaBoxes || isEditingTemplate;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */














/**
 * Internal dependencies
 */















const {
  getLayoutStyles
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const {
  useCommands: layout_useCommands
} = unlock(external_wp_coreCommands_namespaceObject.privateApis);
const {
  useCommandContext
} = unlock(external_wp_commands_namespaceObject.privateApis);
const {
  EditorInterface,
  FullscreenMode,
  Sidebar
} = unlock(external_wp_editor_namespaceObject.privateApis);
const {
  BlockKeyboardShortcuts
} = unlock(external_wp_blockLibrary_namespaceObject.privateApis);
const DESIGN_POST_TYPES = ['wp_template', 'wp_template_part', 'wp_block', 'wp_navigation'];
function useEditorStyles() {
  const {
    hasThemeStyleSupport,
    editorSettings,
    isZoomedOutView,
    hasMetaBoxes,
    renderingMode,
    postType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      __unstableGetEditorMode
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      getCurrentPostType,
      getRenderingMode
    } = select(external_wp_editor_namespaceObject.store);
    const _postType = getCurrentPostType();
    return {
      hasThemeStyleSupport: select(store).isFeatureActive('themeStyles'),
      editorSettings: select(external_wp_editor_namespaceObject.store).getEditorSettings(),
      isZoomedOutView: __unstableGetEditorMode() === 'zoom-out',
      hasMetaBoxes: select(store).hasMetaBoxes(),
      renderingMode: getRenderingMode(),
      postType: _postType
    };
  }, []);

  // Compute the default styles.
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    var _editorSettings$style, _editorSettings$style2, _editorSettings$style3;
    const presetStyles = (_editorSettings$style = editorSettings.styles?.filter(style => style.__unstableType && style.__unstableType !== 'theme')) !== null && _editorSettings$style !== void 0 ? _editorSettings$style : [];
    const defaultEditorStyles = [...editorSettings.defaultEditorStyles, ...presetStyles];

    // Has theme styles if the theme supports them and if some styles were not preset styles (in which case they're theme styles).
    const hasThemeStyles = hasThemeStyleSupport && presetStyles.length !== ((_editorSettings$style2 = editorSettings.styles?.length) !== null && _editorSettings$style2 !== void 0 ? _editorSettings$style2 : 0);

    // If theme styles are not present or displayed, ensure that
    // base layout styles are still present in the editor.
    if (!editorSettings.disableLayoutStyles && !hasThemeStyles) {
      defaultEditorStyles.push({
        css: getLayoutStyles({
          style: {},
          selector: 'body',
          hasBlockGapSupport: false,
          hasFallbackGapSupport: true,
          fallbackGapValue: '0.5em'
        })
      });
    }
    const baseStyles = hasThemeStyles ? (_editorSettings$style3 = editorSettings.styles) !== null && _editorSettings$style3 !== void 0 ? _editorSettings$style3 : [] : defaultEditorStyles;

    // Add a constant padding for the typewriter effect. When typing at the
    // bottom, there needs to be room to scroll up.
    if (!isZoomedOutView && !hasMetaBoxes && renderingMode === 'post-only' && !DESIGN_POST_TYPES.includes(postType)) {
      return [...baseStyles, {
        // Should override global styles padding, so ensure 0-1-0
        // specificity.
        css: ':root :where(body){padding-bottom: 40vh}'
      }];
    }
    return baseStyles;
  }, [editorSettings.defaultEditorStyles, editorSettings.disableLayoutStyles, editorSettings.styles, hasThemeStyleSupport, postType]);
}
function Layout({
  initialPost
}) {
  layout_useCommands();
  useCommands();
  const paddingAppenderRef = usePaddingAppender();
  const shouldIframe = useShouldIframe();
  const {
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const {
    mode,
    isFullscreenActive,
    hasActiveMetaboxes,
    hasBlockSelected,
    showIconLabels,
    isDistractionFree,
    showMetaBoxes,
    hasHistory,
    isEditingTemplate,
    isWelcomeGuideVisible
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    const {
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);
    const {
      isFeatureActive
    } = select(store);
    return {
      mode: select(external_wp_editor_namespaceObject.store).getEditorMode(),
      isFullscreenActive: select(store).isFeatureActive('fullscreenMode'),
      hasActiveMetaboxes: select(store).hasMetaBoxes(),
      hasBlockSelected: !!select(external_wp_blockEditor_namespaceObject.store).getBlockSelectionStart(),
      showIconLabels: get('core', 'showIconLabels'),
      isDistractionFree: get('core', 'distractionFree'),
      showMetaBoxes: select(external_wp_editor_namespaceObject.store).getRenderingMode() === 'post-only',
      hasHistory: !!getEditorSettings().onNavigateToPreviousEntityRecord,
      isEditingTemplate: select(external_wp_editor_namespaceObject.store).getCurrentPostType() === 'wp_template',
      isWelcomeGuideVisible: isFeatureActive('welcomeGuide')
    };
  }, []);

  // Set the right context for the command palette
  const commandContext = hasBlockSelected ? 'block-selection-edit' : 'entity-edit';
  useCommandContext(commandContext);
  const styles = useEditorStyles();

  // We need to add the show-icon-labels class to the body element so it is applied to modals.
  if (showIconLabels) {
    document.body.classList.add('show-icon-labels');
  } else {
    document.body.classList.remove('show-icon-labels');
  }
  const className = dist_clsx('edit-post-layout', 'is-mode-' + mode, {
    'has-metaboxes': hasActiveMetaboxes
  });
  function onPluginAreaError(name) {
    createErrorNotice((0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: plugin name */
    (0,external_wp_i18n_namespaceObject.__)('The "%s" plugin has encountered an error and cannot be rendered.'), name));
  }
  const {
    createSuccessNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const onActionPerformed = (0,external_wp_element_namespaceObject.useCallback)((actionId, items) => {
    switch (actionId) {
      case 'move-to-trash':
        {
          document.location.href = (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
            trashed: 1,
            post_type: items[0].type,
            ids: items[0].id
          });
        }
        break;
      case 'duplicate-post':
        {
          const newItem = items[0];
          const title = typeof newItem.title === 'string' ? newItem.title : newItem.title?.rendered;
          createSuccessNotice((0,external_wp_i18n_namespaceObject.sprintf)(
          // translators: %s: Title of the created post e.g: "Post 1".
          (0,external_wp_i18n_namespaceObject.__)('"%s" successfully created.'), (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title)), {
            type: 'snackbar',
            id: 'duplicate-post-action',
            actions: [{
              label: (0,external_wp_i18n_namespaceObject.__)('Edit'),
              onClick: () => {
                const postId = newItem.id;
                document.location.href = (0,external_wp_url_namespaceObject.addQueryArgs)('post.php', {
                  post: postId,
                  action: 'edit'
                });
              }
            }]
          });
        }
        break;
    }
  }, [createSuccessNotice]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(FullscreenMode, {
      isActive: isFullscreenActive
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(browser_url, {
      hasHistory: hasHistory
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.UnsavedChangesWarning, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.AutosaveMonitor, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.LocalAutosaveMonitor, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(keyboard_shortcuts, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.EditorKeyboardShortcutsRegister, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(BlockKeyboardShortcuts, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuide, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(InitPatternModal, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_plugins_namespaceObject.PluginArea, {
      onError: onPluginAreaError
    }), !isDistractionFree && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Sidebar, {
      onActionPerformed: onActionPerformed,
      extraPanels: !isEditingTemplate && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(MetaBoxes, {
        location: "side"
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(more_menu, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(back_button, {
      initialPost: initialPost
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.EditorSnackbars, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(EditorInterface, {
      className: className,
      styles: styles,
      forceIsDirty: hasActiveMetaboxes,
      contentRef: paddingAppenderRef,
      disableIframe: !shouldIframe
      // We should auto-focus the canvas (title) on load.
      // eslint-disable-next-line jsx-a11y/no-autofocus
      ,
      autoFocus: !isWelcomeGuideVisible,
      children: !isDistractionFree && showMetaBoxes && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
        className: "edit-post-layout__metaboxes",
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(MetaBoxes, {
          location: "normal"
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(MetaBoxes, {
          location: "advanced"
        })]
      })
    })]
  });
}
/* harmony default export */ const layout = (Layout);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/listener-hooks.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/**
 * This listener hook monitors any change in permalink and updates the view
 * post link in the admin bar.
 */
const useUpdatePostLinkListener = () => {
  const {
    newPermalink
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    newPermalink: select(external_wp_editor_namespaceObject.store).getCurrentPost().link
  }), []);
  const nodeToUpdate = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    nodeToUpdate.current = document.querySelector(VIEW_AS_PREVIEW_LINK_SELECTOR) || document.querySelector(VIEW_AS_LINK_SELECTOR);
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!newPermalink || !nodeToUpdate.current) {
      return;
    }
    nodeToUpdate.current.setAttribute('href', newPermalink);
  }, [newPermalink]);
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/index.js
/**
 * Internal dependencies
 */


/**
 * Data component used for initializing the editor and re-initializes
 * when postId changes or on unmount.
 *
 * @return {null} This is a data component so does not render any ui.
 */
function EditorInitialization() {
  useUpdatePostLinkListener();
  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/use-navigate-to-entity-record.js
/**
 * WordPress dependencies
 */




/**
 * A hook that records the 'entity' history in the post editor as a user
 * navigates between editing a post and editing the post template or patterns.
 *
 * Implemented as a stack, so a little similar to the browser history API.
 *
 * Used to control displaying UI elements like the back button.
 *
 * @param {number} initialPostId        The post id of the post when the editor loaded.
 * @param {string} initialPostType      The post type of the post when the editor loaded.
 * @param {string} defaultRenderingMode The rendering mode to switch to when navigating.
 *
 * @return {Object} An object containing the `currentPost` variable and
 *                 `onNavigateToEntityRecord` and `onNavigateToPreviousEntityRecord` functions.
 */
function useNavigateToEntityRecord(initialPostId, initialPostType, defaultRenderingMode) {
  const [postHistory, dispatch] = (0,external_wp_element_namespaceObject.useReducer)((historyState, {
    type,
    post,
    previousRenderingMode
  }) => {
    if (type === 'push') {
      return [...historyState, {
        post,
        previousRenderingMode
      }];
    }
    if (type === 'pop') {
      // Try to leave one item in the history.
      if (historyState.length > 1) {
        return historyState.slice(0, -1);
      }
    }
    return historyState;
  }, [{
    post: {
      postId: initialPostId,
      postType: initialPostType
    }
  }]);
  const {
    post,
    previousRenderingMode
  } = postHistory[postHistory.length - 1];
  const {
    getRenderingMode
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_editor_namespaceObject.store);
  const {
    setRenderingMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_editor_namespaceObject.store);
  const onNavigateToEntityRecord = (0,external_wp_element_namespaceObject.useCallback)(params => {
    dispatch({
      type: 'push',
      post: {
        postId: params.postId,
        postType: params.postType
      },
      // Save the current rendering mode so we can restore it when navigating back.
      previousRenderingMode: getRenderingMode()
    });
    setRenderingMode(defaultRenderingMode);
  }, [getRenderingMode, setRenderingMode, defaultRenderingMode]);
  const onNavigateToPreviousEntityRecord = (0,external_wp_element_namespaceObject.useCallback)(() => {
    dispatch({
      type: 'pop'
    });
    if (previousRenderingMode) {
      setRenderingMode(previousRenderingMode);
    }
  }, [setRenderingMode, previousRenderingMode]);
  return {
    currentPost: post,
    onNavigateToEntityRecord,
    onNavigateToPreviousEntityRecord: postHistory.length > 1 ? onNavigateToPreviousEntityRecord : undefined
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/editor.js
/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */







const {
  ExperimentalEditorProvider
} = unlock(external_wp_editor_namespaceObject.privateApis);
function Editor({
  postId: initialPostId,
  postType: initialPostType,
  settings,
  initialEdits,
  ...props
}) {
  const {
    currentPost,
    onNavigateToEntityRecord,
    onNavigateToPreviousEntityRecord
  } = useNavigateToEntityRecord(initialPostId, initialPostType, 'post-only');
  const {
    post,
    template
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getPostType$viewable;
    const {
      getEditedPostTemplate
    } = select(store);
    const {
      getEntityRecord,
      getPostType,
      canUser
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);
    const postObject = getEntityRecord('postType', currentPost.postType, currentPost.postId);
    const supportsTemplateMode = getEditorSettings().supportsTemplateMode;
    const isViewable = (_getPostType$viewable = getPostType(currentPost.postType)?.viewable) !== null && _getPostType$viewable !== void 0 ? _getPostType$viewable : false;
    const canViewTemplate = canUser('read', 'templates');
    return {
      template: supportsTemplateMode && isViewable && canViewTemplate && currentPost.postType !== 'wp_template' ? getEditedPostTemplate() : null,
      post: postObject
    };
  }, [currentPost.postType, currentPost.postId]);
  const editorSettings = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    ...settings,
    onNavigateToEntityRecord,
    onNavigateToPreviousEntityRecord,
    defaultRenderingMode: 'post-only'
  }), [settings, onNavigateToEntityRecord, onNavigateToPreviousEntityRecord]);
  const initialPost = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return {
      type: initialPostType,
      id: initialPostId
    };
  }, [initialPostType, initialPostId]);
  if (!post) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.SlotFillProvider, {
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(ExperimentalEditorProvider, {
      settings: editorSettings,
      post: post,
      initialEdits: initialEdits,
      useSubRegistry: false,
      __unstableTemplate: template,
      ...props,
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_editor_namespaceObject.ErrorBoundary, {
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_commands_namespaceObject.CommandMenu, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(EditorInitialization, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(layout, {
          initialPost: initialPost
        })]
      }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PostLockedModal, {})]
    })
  });
}
/* harmony default export */ const editor = (Editor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/deprecated.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


const {
  PluginPostExcerpt
} = unlock(external_wp_editor_namespaceObject.privateApis);
const isSiteEditor = (0,external_wp_url_namespaceObject.getPath)(window.location.href)?.includes('site-editor.php');
const deprecateSlot = name => {
  external_wp_deprecated_default()(`wp.editPost.${name}`, {
    since: '6.6',
    alternative: `wp.editor.${name}`
  });
};

/* eslint-disable jsdoc/require-param */
/**
 * @see PluginBlockSettingsMenuItem in @wordpress/editor package.
 */
function PluginBlockSettingsMenuItem(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot('PluginBlockSettingsMenuItem');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginBlockSettingsMenuItem, {
    ...props
  });
}

/**
 * @see PluginDocumentSettingPanel in @wordpress/editor package.
 */
function PluginDocumentSettingPanel(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot('PluginDocumentSettingPanel');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginDocumentSettingPanel, {
    ...props
  });
}

/**
 * @see PluginMoreMenuItem in @wordpress/editor package.
 */
function PluginMoreMenuItem(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot('PluginMoreMenuItem');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginMoreMenuItem, {
    ...props
  });
}

/**
 * @see PluginPrePublishPanel in @wordpress/editor package.
 */
function PluginPrePublishPanel(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot('PluginPrePublishPanel');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginPrePublishPanel, {
    ...props
  });
}

/**
 * @see PluginPostPublishPanel in @wordpress/editor package.
 */
function PluginPostPublishPanel(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot('PluginPostPublishPanel');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginPostPublishPanel, {
    ...props
  });
}

/**
 * @see PluginPostStatusInfo in @wordpress/editor package.
 */
function PluginPostStatusInfo(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot('PluginPostStatusInfo');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginPostStatusInfo, {
    ...props
  });
}

/**
 * @see PluginSidebar in @wordpress/editor package.
 */
function PluginSidebar(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot('PluginSidebar');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginSidebar, {
    ...props
  });
}

/**
 * @see PluginSidebarMoreMenuItem in @wordpress/editor package.
 */
function PluginSidebarMoreMenuItem(props) {
  if (isSiteEditor) {
    return null;
  }
  deprecateSlot('PluginSidebarMoreMenuItem');
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_editor_namespaceObject.PluginSidebarMoreMenuItem, {
    ...props
  });
}

/**
 * @see PluginPostExcerpt in @wordpress/editor package.
 */
function __experimentalPluginPostExcerpt() {
  if (isSiteEditor) {
    return null;
  }
  external_wp_deprecated_default()('wp.editPost.__experimentalPluginPostExcerpt', {
    since: '6.6',
    hint: 'Core and custom panels can be access programmatically using their panel name.',
    link: 'https://developer.wordpress.org/block-editor/reference-guides/slotfills/plugin-document-setting-panel/#accessing-a-panel-programmatically'
  });
  return PluginPostExcerpt;
}

/* eslint-enable jsdoc/require-param */

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/index.js
/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */



const {
  BackButton: __experimentalMainDashboardButton
} = unlock(external_wp_editor_namespaceObject.privateApis);

/**
 * Initializes and returns an instance of Editor.
 *
 * @param {string}  id           Unique identifier for editor instance.
 * @param {string}  postType     Post type of the post to edit.
 * @param {Object}  postId       ID of the post to edit.
 * @param {?Object} settings     Editor settings object.
 * @param {Object}  initialEdits Programmatic edits to apply initially, to be
 *                               considered as non-user-initiated (bypass for
 *                               unsaved changes prompt).
 */
function initializeEditor(id, postType, postId, settings, initialEdits) {
  const isMediumOrBigger = window.matchMedia('(min-width: 782px)').matches;
  const target = document.getElementById(id);
  const root = (0,external_wp_element_namespaceObject.createRoot)(target);
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults('core/edit-post', {
    fullscreenMode: true,
    themeStyles: true,
    welcomeGuide: true,
    welcomeGuideTemplate: true
  });
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults('core', {
    allowRightClickOverrides: true,
    editorMode: 'visual',
    fixedToolbar: false,
    hiddenBlockTypes: [],
    inactivePanels: [],
    openPanels: ['post-status'],
    showBlockBreadcrumbs: true,
    showIconLabels: false,
    showListViewByDefault: false,
    isPublishSidebarEnabled: true
  });
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).reapplyBlockTypeFilters();

  // Check if the block list view should be open by default.
  // If `distractionFree` mode is enabled, the block list view should not be open.
  // This behavior is disabled for small viewports.
  if (isMediumOrBigger && (0,external_wp_data_namespaceObject.select)(external_wp_preferences_namespaceObject.store).get('core', 'showListViewByDefault') && !(0,external_wp_data_namespaceObject.select)(external_wp_preferences_namespaceObject.store).get('core', 'distractionFree')) {
    (0,external_wp_data_namespaceObject.dispatch)(external_wp_editor_namespaceObject.store).setIsListViewOpened(true);
  }
  (0,external_wp_blockLibrary_namespaceObject.registerCoreBlocks)();
  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetBlock)({
    inserter: false
  });
  (0,external_wp_widgets_namespaceObject.registerWidgetGroupBlock)({
    inserter: false
  });
  if (false) {}

  // Show a console log warning if the browser is not in Standards rendering mode.
  const documentMode = document.compatMode === 'CSS1Compat' ? 'Standards' : 'Quirks';
  if (documentMode !== 'Standards') {
    // eslint-disable-next-line no-console
    console.warn("Your browser is using Quirks Mode. \nThis can cause rendering issues such as blocks overlaying meta boxes in the editor. Quirks Mode can be triggered by PHP errors or HTML code appearing before the opening <!DOCTYPE html>. Try checking the raw page source or your site's PHP error log and resolving errors there, removing any HTML before the doctype, or disabling plugins.");
  }

  // This is a temporary fix for a couple of issues specific to Webkit on iOS.
  // Without this hack the browser scrolls the mobile toolbar off-screen.
  // Once supported in Safari we can replace this in favor of preventScroll.
  // For details see issue #18632 and PR #18686
  // Specifically, we scroll `interface-interface-skeleton__body` to enable a fixed top toolbar.
  // But Mobile Safari forces the `html` element to scroll upwards, hiding the toolbar.

  const isIphone = window.navigator.userAgent.indexOf('iPhone') !== -1;
  if (isIphone) {
    window.addEventListener('scroll', event => {
      const editorScrollContainer = document.getElementsByClassName('interface-interface-skeleton__body')[0];
      if (event.target === document) {
        // Scroll element into view by scrolling the editor container by the same amount
        // that Mobile Safari tried to scroll the html element upwards.
        if (window.scrollY > 100) {
          editorScrollContainer.scrollTop = editorScrollContainer.scrollTop + window.scrollY;
        }
        // Undo unwanted scroll on html element, but only in the visual editor.
        if (document.getElementsByClassName('is-mode-visual')[0]) {
          window.scrollTo(0, 0);
        }
      }
    });
  }

  // Prevent the default browser action for files dropped outside of dropzones.
  window.addEventListener('dragover', e => e.preventDefault(), false);
  window.addEventListener('drop', e => e.preventDefault(), false);
  root.render( /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(editor, {
    settings: settings,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits
  }));
  return root;
}

/**
 * Used to reinitialize the editor after an error. Now it's a deprecated noop function.
 */
function reinitializeEditor() {
  external_wp_deprecated_default()('wp.editPost.reinitializeEditor', {
    since: '6.2',
    version: '6.3'
  });
}





(window.wp = window.wp || {}).editPost = __webpack_exports__;
/******/ })()
;