/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 4403:
/***/ (function(module, exports) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;
	var nativeCodeString = '[native code]';

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
					classes.push(arg.toString());
					continue;
				}

				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "PluginBlockSettingsMenuItem": function() { return /* reexport */ plugin_block_settings_menu_item; },
  "PluginDocumentSettingPanel": function() { return /* reexport */ plugin_document_setting_panel; },
  "PluginMoreMenuItem": function() { return /* reexport */ plugin_more_menu_item; },
  "PluginPostPublishPanel": function() { return /* reexport */ plugin_post_publish_panel; },
  "PluginPostStatusInfo": function() { return /* reexport */ plugin_post_status_info; },
  "PluginPrePublishPanel": function() { return /* reexport */ plugin_pre_publish_panel; },
  "PluginSidebar": function() { return /* reexport */ PluginSidebarEditPost; },
  "PluginSidebarMoreMenuItem": function() { return /* reexport */ PluginSidebarMoreMenuItem; },
  "__experimentalFullscreenModeClose": function() { return /* reexport */ fullscreen_mode_close; },
  "__experimentalMainDashboardButton": function() { return /* reexport */ main_dashboard_button; },
  "initializeEditor": function() { return /* binding */ initializeEditor; },
  "reinitializeEditor": function() { return /* binding */ reinitializeEditor; },
  "store": function() { return /* reexport */ store_store; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "disableComplementaryArea": function() { return disableComplementaryArea; },
  "enableComplementaryArea": function() { return enableComplementaryArea; },
  "pinItem": function() { return pinItem; },
  "setDefaultComplementaryArea": function() { return setDefaultComplementaryArea; },
  "setFeatureDefaults": function() { return setFeatureDefaults; },
  "setFeatureValue": function() { return setFeatureValue; },
  "toggleFeature": function() { return toggleFeature; },
  "unpinItem": function() { return unpinItem; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "getActiveComplementaryArea": function() { return getActiveComplementaryArea; },
  "isFeatureActive": function() { return isFeatureActive; },
  "isItemPinned": function() { return isItemPinned; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
var store_actions_namespaceObject = {};
__webpack_require__.r(store_actions_namespaceObject);
__webpack_require__.d(store_actions_namespaceObject, {
  "__experimentalSetPreviewDeviceType": function() { return __experimentalSetPreviewDeviceType; },
  "__unstableCreateTemplate": function() { return __unstableCreateTemplate; },
  "__unstableSwitchToTemplateMode": function() { return __unstableSwitchToTemplateMode; },
  "closeGeneralSidebar": function() { return closeGeneralSidebar; },
  "closeModal": function() { return closeModal; },
  "closePublishSidebar": function() { return closePublishSidebar; },
  "hideBlockTypes": function() { return hideBlockTypes; },
  "initializeMetaBoxes": function() { return initializeMetaBoxes; },
  "metaBoxUpdatesFailure": function() { return metaBoxUpdatesFailure; },
  "metaBoxUpdatesSuccess": function() { return metaBoxUpdatesSuccess; },
  "openGeneralSidebar": function() { return openGeneralSidebar; },
  "openModal": function() { return openModal; },
  "openPublishSidebar": function() { return openPublishSidebar; },
  "removeEditorPanel": function() { return removeEditorPanel; },
  "requestMetaBoxUpdates": function() { return requestMetaBoxUpdates; },
  "setAvailableMetaBoxesPerLocation": function() { return setAvailableMetaBoxesPerLocation; },
  "setIsEditingTemplate": function() { return setIsEditingTemplate; },
  "setIsInserterOpened": function() { return setIsInserterOpened; },
  "setIsListViewOpened": function() { return setIsListViewOpened; },
  "showBlockTypes": function() { return showBlockTypes; },
  "switchEditorMode": function() { return switchEditorMode; },
  "toggleEditorPanelEnabled": function() { return toggleEditorPanelEnabled; },
  "toggleEditorPanelOpened": function() { return toggleEditorPanelOpened; },
  "toggleFeature": function() { return actions_toggleFeature; },
  "togglePinnedPluginItem": function() { return togglePinnedPluginItem; },
  "togglePublishSidebar": function() { return togglePublishSidebar; },
  "updatePreferredStyleVariations": function() { return updatePreferredStyleVariations; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
var store_selectors_namespaceObject = {};
__webpack_require__.r(store_selectors_namespaceObject);
__webpack_require__.d(store_selectors_namespaceObject, {
  "__experimentalGetInsertionPoint": function() { return __experimentalGetInsertionPoint; },
  "__experimentalGetPreviewDeviceType": function() { return __experimentalGetPreviewDeviceType; },
  "areMetaBoxesInitialized": function() { return areMetaBoxesInitialized; },
  "getActiveGeneralSidebarName": function() { return getActiveGeneralSidebarName; },
  "getActiveMetaBoxLocations": function() { return getActiveMetaBoxLocations; },
  "getAllMetaBoxes": function() { return getAllMetaBoxes; },
  "getEditedPostTemplate": function() { return getEditedPostTemplate; },
  "getEditorMode": function() { return getEditorMode; },
  "getHiddenBlockTypes": function() { return getHiddenBlockTypes; },
  "getMetaBoxesPerLocation": function() { return getMetaBoxesPerLocation; },
  "getPreference": function() { return getPreference; },
  "getPreferences": function() { return getPreferences; },
  "hasMetaBoxes": function() { return hasMetaBoxes; },
  "isEditingTemplate": function() { return selectors_isEditingTemplate; },
  "isEditorPanelEnabled": function() { return isEditorPanelEnabled; },
  "isEditorPanelOpened": function() { return isEditorPanelOpened; },
  "isEditorPanelRemoved": function() { return isEditorPanelRemoved; },
  "isEditorSidebarOpened": function() { return isEditorSidebarOpened; },
  "isFeatureActive": function() { return selectors_isFeatureActive; },
  "isInserterOpened": function() { return isInserterOpened; },
  "isListViewOpened": function() { return isListViewOpened; },
  "isMetaBoxLocationActive": function() { return isMetaBoxLocationActive; },
  "isMetaBoxLocationVisible": function() { return isMetaBoxLocationVisible; },
  "isModalActive": function() { return isModalActive; },
  "isPluginItemPinned": function() { return isPluginItemPinned; },
  "isPluginSidebarOpened": function() { return isPluginSidebarOpened; },
  "isPublishSidebarOpened": function() { return isPublishSidebarOpened; },
  "isSavingMetaBoxes": function() { return selectors_isSavingMetaBoxes; }
});

;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","blocks"]
var external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","blockLibrary"]
var external_wp_blockLibrary_namespaceObject = window["wp"]["blockLibrary"];
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","hooks"]
var external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: external ["wp","preferences"]
var external_wp_preferences_namespaceObject = window["wp"]["preferences"];
;// CONCATENATED MODULE: external ["wp","mediaUtils"]
var external_wp_mediaUtils_namespaceObject = window["wp"]["mediaUtils"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/components/index.js
/**
 * WordPress dependencies
 */



const replaceMediaUpload = () => external_wp_mediaUtils_namespaceObject.MediaUpload;

(0,external_wp_hooks_namespaceObject.addFilter)('editor.MediaUpload', 'core/edit-post/replace-media-upload', replaceMediaUpload);

;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
function _extends() {
  _extends = Object.assign ? Object.assign.bind() : function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return _extends.apply(this, arguments);
}
;// CONCATENATED MODULE: external "lodash"
var external_lodash_namespaceObject = window["lodash"];
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","compose"]
var external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/validate-multiple-use/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








const enhance = (0,external_wp_compose_namespaceObject.compose)(
/**
 * For blocks whose block type doesn't support `multiple`, provides the
 * wrapped component with `originalBlockClientId` -- a reference to the
 * first block of the same type in the content -- if and only if that
 * "original" block is not the current one. Thus, an inexisting
 * `originalBlockClientId` prop signals that the block is valid.
 *
 * @param {WPComponent} WrappedBlockEdit A filtered BlockEdit instance.
 *
 * @return {WPComponent} Enhanced component with merged state data props.
 */
(0,external_wp_data_namespaceObject.withSelect)((select, block) => {
  const multiple = (0,external_wp_blocks_namespaceObject.hasBlockSupport)(block.name, 'multiple', true); // For block types with `multiple` support, there is no "original
  // block" to be found in the content, as the block itself is valid.

  if (multiple) {
    return {};
  } // Otherwise, only pass `originalBlockClientId` if it refers to a different
  // block from the current one.


  const blocks = select(external_wp_blockEditor_namespaceObject.store).getBlocks();
  const firstOfSameType = (0,external_lodash_namespaceObject.find)(blocks, _ref => {
    let {
      name
    } = _ref;
    return block.name === name;
  });
  const isInvalid = firstOfSameType && firstOfSameType.clientId !== block.clientId;
  return {
    originalBlockClientId: isInvalid && firstOfSameType.clientId
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, _ref2) => {
  let {
    originalBlockClientId
  } = _ref2;
  return {
    selectFirst: () => dispatch(external_wp_blockEditor_namespaceObject.store).selectBlock(originalBlockClientId)
  };
}));
const withMultipleValidation = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => {
  return enhance(_ref3 => {
    let {
      originalBlockClientId,
      selectFirst,
      ...props
    } = _ref3;

    if (!originalBlockClientId) {
      return (0,external_wp_element_namespaceObject.createElement)(BlockEdit, props);
    }

    const blockType = (0,external_wp_blocks_namespaceObject.getBlockType)(props.name);
    const outboundType = getOutboundType(props.name);
    return [(0,external_wp_element_namespaceObject.createElement)("div", {
      key: "invalid-preview",
      style: {
        minHeight: '60px'
      }
    }, (0,external_wp_element_namespaceObject.createElement)(BlockEdit, _extends({
      key: "block-edit"
    }, props))), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.Warning, {
      key: "multiple-use-warning",
      actions: [(0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
        key: "find-original",
        variant: "secondary",
        onClick: selectFirst
      }, (0,external_wp_i18n_namespaceObject.__)('Find original')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
        key: "remove",
        variant: "secondary",
        onClick: () => props.onReplace([])
      }, (0,external_wp_i18n_namespaceObject.__)('Remove')), outboundType && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
        key: "transform",
        variant: "secondary",
        onClick: () => props.onReplace((0,external_wp_blocks_namespaceObject.createBlock)(outboundType.name, props.attributes))
      }, (0,external_wp_i18n_namespaceObject.__)('Transform into:'), " ", outboundType.title)]
    }, (0,external_wp_element_namespaceObject.createElement)("strong", null, blockType === null || blockType === void 0 ? void 0 : blockType.title, ": "), (0,external_wp_i18n_namespaceObject.__)('This block can only be used once.'))];
  });
}, 'withMultipleValidation');
/**
 * Given a base block name, returns the default block type to which to offer
 * transforms.
 *
 * @param {string} blockName Base block name.
 *
 * @return {?Object} The chosen default block type.
 */

function getOutboundType(blockName) {
  // Grab the first outbound transform.
  const transform = (0,external_wp_blocks_namespaceObject.findTransform)((0,external_wp_blocks_namespaceObject.getBlockTransforms)('to', blockName), _ref4 => {
    let {
      type,
      blocks
    } = _ref4;
    return type === 'block' && blocks.length === 1;
  } // What about when .length > 1?
  );

  if (!transform) {
    return null;
  }

  return (0,external_wp_blocks_namespaceObject.getBlockType)(transform.blocks[0]);
}

(0,external_wp_hooks_namespaceObject.addFilter)('editor.BlockEdit', 'core/edit-post/validate-multiple-use/with-multiple-validation', withMultipleValidation);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/index.js
/**
 * Internal dependencies
 */



;// CONCATENATED MODULE: external ["wp","primitives"]
var external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/external.js


/**
 * WordPress dependencies
 */

const external = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
}));
/* harmony default export */ var library_external = (external);

;// CONCATENATED MODULE: external ["wp","plugins"]
var external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// CONCATENATED MODULE: external ["wp","url"]
var external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wp","notices"]
var external_wp_notices_namespaceObject = window["wp"]["notices"];
;// CONCATENATED MODULE: external ["wp","editor"]
var external_wp_editor_namespaceObject = window["wp"]["editor"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/copy-content-menu-item/index.js


/**
 * WordPress dependencies
 */






function CopyContentMenuItem() {
  const {
    createNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const getText = (0,external_wp_data_namespaceObject.useSelect)(select => () => select(external_wp_editor_namespaceObject.store).getEditedPostAttribute('content'), []);

  function onSuccess() {
    createNotice('info', (0,external_wp_i18n_namespaceObject.__)('All content copied.'), {
      isDismissible: true,
      type: 'snackbar'
    });
  }

  const ref = (0,external_wp_compose_namespaceObject.useCopyToClipboard)(getText, onSuccess);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    ref: ref
  }, (0,external_wp_i18n_namespaceObject.__)('Copy all blocks'));
}

;// CONCATENATED MODULE: external ["wp","keycodes"]
var external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/reducer.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Reducer storing the list of all programmatically removed panels.
 *
 * @param {Array}  state  Current state.
 * @param {Object} action Action object.
 *
 * @return {Array} Updated state.
 */

function removedPanels() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REMOVE_PANEL':
      if (!(0,external_lodash_namespaceObject.includes)(state, action.panelName)) {
        return [...state, action.panelName];
      }

  }

  return state;
}
/**
 * Reducer for storing the name of the open modal, or null if no modal is open.
 *
 * @param {Object} state  Previous state.
 * @param {Object} action Action object containing the `name` of the modal
 *
 * @return {Object} Updated state
 */

function activeModal() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_MODAL':
      return action.name;

    case 'CLOSE_MODAL':
      return null;
  }

  return state;
}
function publishSidebarActive() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_PUBLISH_SIDEBAR':
      return true;

    case 'CLOSE_PUBLISH_SIDEBAR':
      return false;

    case 'TOGGLE_PUBLISH_SIDEBAR':
      return !state;
  }

  return state;
}
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

function isSavingMetaBoxes() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

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
/**
 * Reducer keeping track of the meta boxes per location.
 *
 * @param {boolean} state  Previous state.
 * @param {Object}  action Action Object.
 *
 * @return {Object} Updated state.
 */

function metaBoxLocations() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_META_BOXES_PER_LOCATIONS':
      return action.metaBoxesPerLocation;
  }

  return state;
}
/**
 * Reducer returning the editing canvas device type.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function deviceType() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'Desktop';
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_PREVIEW_DEVICE_TYPE':
      return action.deviceType;
  }

  return state;
}
/**
 * Reducer to set the block inserter panel open or closed.
 *
 * Note: this reducer interacts with the list view panel reducer
 * to make sure that only one of the two panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function blockInserterPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen ? false : state;

    case 'SET_IS_INSERTER_OPENED':
      return action.value;
  }

  return state;
}
/**
 * Reducer to set the list view panel open or closed.
 *
 * Note: this reducer interacts with the inserter panel reducer
 * to make sure that only one of the two panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function listViewPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_INSERTER_OPENED':
      return action.value ? false : state;

    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen;
  }

  return state;
}
/**
 * Reducer tracking whether the inserter is open.
 *
 * @param {boolean} state
 * @param {Object}  action
 */

function isEditingTemplate() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_EDITING_TEMPLATE':
      return action.value;
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


function metaBoxesInitialized() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

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
/* harmony default export */ var reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  activeModal,
  metaBoxes,
  publishSidebarActive,
  removedPanels,
  deviceType,
  blockInserterPanel,
  listViewPanel,
  isEditingTemplate
}));

;// CONCATENATED MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(4403);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js


/**
 * WordPress dependencies
 */

const check = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"
}));
/* harmony default export */ var library_check = (check);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/star-filled.js


/**
 * WordPress dependencies
 */

const starFilled = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"
}));
/* harmony default export */ var star_filled = (starFilled);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/star-empty.js


/**
 * WordPress dependencies
 */

const starEmpty = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
  clipRule: "evenodd"
}));
/* harmony default export */ var star_empty = (starEmpty);

;// CONCATENATED MODULE: external ["wp","viewport"]
var external_wp_viewport_namespaceObject = window["wp"]["viewport"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close-small.js


/**
 * WordPress dependencies
 */

const closeSmall = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
}));
/* harmony default export */ var close_small = (closeSmall);

;// CONCATENATED MODULE: external ["wp","deprecated"]
var external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/actions.js
/**
 * WordPress dependencies
 */


/**
 * Set a default complementary area.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 *
 * @return {Object} Action object.
 */

const setDefaultComplementaryArea = (scope, area) => ({
  type: 'SET_DEFAULT_COMPLEMENTARY_AREA',
  scope,
  area
});
/**
 * Enable the complementary area.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 */

const enableComplementaryArea = (scope, area) => _ref => {
  let {
    registry,
    dispatch
  } = _ref;

  // Return early if there's no area.
  if (!area) {
    return;
  }

  const isComplementaryAreaVisible = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');

  if (!isComplementaryAreaVisible) {
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'isComplementaryAreaVisible', true);
  }

  dispatch({
    type: 'ENABLE_COMPLEMENTARY_AREA',
    scope,
    area
  });
};
/**
 * Disable the complementary area.
 *
 * @param {string} scope Complementary area scope.
 */

const disableComplementaryArea = scope => _ref2 => {
  let {
    registry
  } = _ref2;
  const isComplementaryAreaVisible = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');

  if (isComplementaryAreaVisible) {
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'isComplementaryAreaVisible', false);
  }
};
/**
 * Pins an item.
 *
 * @param {string} scope Item scope.
 * @param {string} item  Item identifier.
 *
 * @return {Object} Action object.
 */

const pinItem = (scope, item) => _ref3 => {
  let {
    registry
  } = _ref3;

  // Return early if there's no item.
  if (!item) {
    return;
  }

  const pinnedItems = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems'); // The item is already pinned, there's nothing to do.

  if ((pinnedItems === null || pinnedItems === void 0 ? void 0 : pinnedItems[item]) === true) {
    return;
  }

  registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'pinnedItems', { ...pinnedItems,
    [item]: true
  });
};
/**
 * Unpins an item.
 *
 * @param {string} scope Item scope.
 * @param {string} item  Item identifier.
 */

const unpinItem = (scope, item) => _ref4 => {
  let {
    registry
  } = _ref4;

  // Return early if there's no item.
  if (!item) {
    return;
  }

  const pinnedItems = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems');
  registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'pinnedItems', { ...pinnedItems,
    [item]: false
  });
};
/**
 * Returns an action object used in signalling that a feature should be toggled.
 *
 * @param {string} scope       The feature scope (e.g. core/edit-post).
 * @param {string} featureName The feature name.
 */

function toggleFeature(scope, featureName) {
  return function (_ref5) {
    let {
      registry
    } = _ref5;
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).toggleFeature`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).toggle`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).toggle(scope, featureName);
  };
}
/**
 * Returns an action object used in signalling that a feature should be set to
 * a true or false value
 *
 * @param {string}  scope       The feature scope (e.g. core/edit-post).
 * @param {string}  featureName The feature name.
 * @param {boolean} value       The value to set.
 *
 * @return {Object} Action object.
 */

function setFeatureValue(scope, featureName, value) {
  return function (_ref6) {
    let {
      registry
    } = _ref6;
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).setFeatureValue`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).set`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, featureName, !!value);
  };
}
/**
 * Returns an action object used in signalling that defaults should be set for features.
 *
 * @param {string}                  scope    The feature scope (e.g. core/edit-post).
 * @param {Object<string, boolean>} defaults A key/value map of feature names to values.
 *
 * @return {Object} Action object.
 */

function setFeatureDefaults(scope, defaults) {
  return function (_ref7) {
    let {
      registry
    } = _ref7;
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).setFeatureDefaults`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).setDefaults`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).setDefaults(scope, defaults);
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/selectors.js
/**
 * WordPress dependencies
 */



/**
 * Returns the complementary area that is active in a given scope.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Item scope.
 *
 * @return {string | null | undefined} The complementary area that is active in the given scope.
 */

const getActiveComplementaryArea = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope) => {
  var _state$complementaryA;

  const isComplementaryAreaVisible = select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible'); // Return `undefined` to indicate that the user has never toggled
  // visibility, this is the vanilla default. Other code relies on this
  // nuance in the return value.

  if (isComplementaryAreaVisible === undefined) {
    return undefined;
  } // Return `null` to indicate the user hid the complementary area.


  if (!isComplementaryAreaVisible) {
    return null;
  }

  return state === null || state === void 0 ? void 0 : (_state$complementaryA = state.complementaryAreas) === null || _state$complementaryA === void 0 ? void 0 : _state$complementaryA[scope];
});
/**
 * Returns a boolean indicating if an item is pinned or not.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Scope.
 * @param {string} item  Item to check.
 *
 * @return {boolean} True if the item is pinned and false otherwise.
 */

const isItemPinned = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope, item) => {
  var _pinnedItems$item;

  const pinnedItems = select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems');
  return (_pinnedItems$item = pinnedItems === null || pinnedItems === void 0 ? void 0 : pinnedItems[item]) !== null && _pinnedItems$item !== void 0 ? _pinnedItems$item : true;
});
/**
 * Returns a boolean indicating whether a feature is active for a particular
 * scope.
 *
 * @param {Object} state       The store state.
 * @param {string} scope       The scope of the feature (e.g. core/edit-post).
 * @param {string} featureName The name of the feature.
 *
 * @return {boolean} Is the feature enabled?
 */

const isFeatureActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope, featureName) => {
  external_wp_deprecated_default()(`select( 'core/interface' ).isFeatureActive( scope, featureName )`, {
    since: '6.0',
    alternative: `select( 'core/preferences' ).get( scope, featureName )`
  });
  return !!select(external_wp_preferences_namespaceObject.store).get(scope, featureName);
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

function complementaryAreas() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_DEFAULT_COMPLEMENTARY_AREA':
      {
        const {
          scope,
          area
        } = action; // If there's already an area, don't overwrite it.

        if (state[scope]) {
          return state;
        }

        return { ...state,
          [scope]: area
        };
      }

    case 'ENABLE_COMPLEMENTARY_AREA':
      {
        const {
          scope,
          area
        } = action;
        return { ...state,
          [scope]: area
        };
      }
  }

  return state;
}
/* harmony default export */ var store_reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  complementaryAreas
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/interface';

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Store definition for the interface namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: store_reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-context/index.js
/**
 * WordPress dependencies
 */

/* harmony default export */ var complementary_area_context = ((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon,
    identifier: ownProps.identifier || `${context.name}/${ownProps.name}`
  };
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-toggle/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function ComplementaryAreaToggle(_ref) {
  let {
    as = external_wp_components_namespaceObject.Button,
    scope,
    identifier,
    icon,
    selectedIcon,
    name,
    ...props
  } = _ref;
  const ComponentToUse = as;
  const isSelected = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getActiveComplementaryArea(scope) === identifier, [identifier]);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return (0,external_wp_element_namespaceObject.createElement)(ComponentToUse, _extends({
    icon: selectedIcon && isSelected ? selectedIcon : icon,
    onClick: () => {
      if (isSelected) {
        disableComplementaryArea(scope);
      } else {
        enableComplementaryArea(scope, identifier);
      }
    }
  }, props));
}

/* harmony default export */ var complementary_area_toggle = (complementary_area_context(ComplementaryAreaToggle));

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-header/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const ComplementaryAreaHeader = _ref => {
  let {
    smallScreenTitle,
    children,
    className,
    toggleButtonProps
  } = _ref;
  const toggleButton = (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, _extends({
    icon: close_small
  }, toggleButtonProps));
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "components-panel__header interface-complementary-area-header__small"
  }, smallScreenTitle && (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "interface-complementary-area-header__small-title"
  }, smallScreenTitle), toggleButton), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('components-panel__header', 'interface-complementary-area-header', className),
    tabIndex: -1
  }, children, toggleButton));
};

/* harmony default export */ var complementary_area_header = (ComplementaryAreaHeader);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/action-item/index.js



/**
 * WordPress dependencies
 */



const noop = () => {};

function ActionItemSlot(_ref) {
  let {
    name,
    as: Component = external_wp_components_namespaceObject.ButtonGroup,
    fillProps = {},
    bubblesVirtually,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, {
    name: name,
    bubblesVirtually: bubblesVirtually,
    fillProps: fillProps
  }, fills => {
    if (!external_wp_element_namespaceObject.Children.toArray(fills).length) {
      return null;
    } // Special handling exists for backward compatibility.
    // It ensures that menu items created by plugin authors aren't
    // duplicated with automatically injected menu items coming
    // from pinnable plugin sidebars.
    // @see https://github.com/WordPress/gutenberg/issues/14457


    const initializedByPlugins = [];
    external_wp_element_namespaceObject.Children.forEach(fills, _ref2 => {
      let {
        props: {
          __unstableExplicitMenuItem,
          __unstableTarget
        }
      } = _ref2;

      if (__unstableTarget && __unstableExplicitMenuItem) {
        initializedByPlugins.push(__unstableTarget);
      }
    });
    const children = external_wp_element_namespaceObject.Children.map(fills, child => {
      if (!child.props.__unstableExplicitMenuItem && initializedByPlugins.includes(child.props.__unstableTarget)) {
        return null;
      }

      return child;
    });
    return (0,external_wp_element_namespaceObject.createElement)(Component, props, children);
  });
}

function ActionItem(_ref3) {
  let {
    name,
    as: Component = external_wp_components_namespaceObject.Button,
    onClick,
    ...props
  } = _ref3;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, {
    name: name
  }, _ref4 => {
    let {
      onClick: fpOnClick
    } = _ref4;
    return (0,external_wp_element_namespaceObject.createElement)(Component, _extends({
      onClick: onClick || fpOnClick ? function () {
        (onClick || noop)(...arguments);
        (fpOnClick || noop)(...arguments);
      } : undefined
    }, props));
  });
}

ActionItem.Slot = ActionItemSlot;
/* harmony default export */ var action_item = (ActionItem);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-more-menu-item/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const PluginsMenuItem = _ref => {
  let {
    // Menu item is marked with unstable prop for backward compatibility.
    // They are removed so they don't leak to DOM elements.
    // @see https://github.com/WordPress/gutenberg/issues/14457
    __unstableExplicitMenuItem,
    __unstableTarget,
    ...restProps
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, restProps);
};

function ComplementaryAreaMoreMenuItem(_ref2) {
  let {
    scope,
    target,
    __unstableExplicitMenuItem,
    ...props
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, _extends({
    as: toggleProps => {
      return (0,external_wp_element_namespaceObject.createElement)(action_item, _extends({
        __unstableExplicitMenuItem: __unstableExplicitMenuItem,
        __unstableTarget: `${scope}/${target}`,
        as: PluginsMenuItem,
        name: `${scope}/plugin-more-menu`
      }, toggleProps));
    },
    role: "menuitemcheckbox",
    selectedIcon: library_check,
    name: target,
    scope: scope
  }, props));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/pinned-items/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PinnedItems(_ref) {
  let {
    scope,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, _extends({
    name: `PinnedItems/${scope}`
  }, props));
}

function PinnedItemsSlot(_ref2) {
  let {
    scope,
    className,
    ...props
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, _extends({
    name: `PinnedItems/${scope}`
  }, props), fills => (fills === null || fills === void 0 ? void 0 : fills.length) > 0 && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()(className, 'interface-pinned-items')
  }, fills));
}

PinnedItems.Slot = PinnedItemsSlot;
/* harmony default export */ var pinned_items = (PinnedItems);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */








function ComplementaryAreaSlot(_ref) {
  let {
    scope,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, _extends({
    name: `ComplementaryArea/${scope}`
  }, props));
}

function ComplementaryAreaFill(_ref2) {
  let {
    scope,
    children,
    className
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, {
    name: `ComplementaryArea/${scope}`
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: className
  }, children));
}

function useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall) {
  const previousIsSmall = (0,external_wp_element_namespaceObject.useRef)(false);
  const shouldOpenWhenNotSmall = (0,external_wp_element_namespaceObject.useRef)(false);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // If the complementary area is active and the editor is switching from a big to a small window size.
    if (isActive && isSmall && !previousIsSmall.current) {
      // Disable the complementary area.
      disableComplementaryArea(scope); // Flag the complementary area to be reopened when the window size goes from small to big.

      shouldOpenWhenNotSmall.current = true;
    } else if ( // If there is a flag indicating the complementary area should be enabled when we go from small to big window size
    // and we are going from a small to big window size.
    shouldOpenWhenNotSmall.current && !isSmall && previousIsSmall.current) {
      // Remove the flag indicating the complementary area should be enabled.
      shouldOpenWhenNotSmall.current = false; // Enable the complementary area.

      enableComplementaryArea(scope, identifier);
    } else if ( // If the flag is indicating the current complementary should be reopened but another complementary area becomes active,
    // remove the flag.
    shouldOpenWhenNotSmall.current && activeArea && activeArea !== identifier) {
      shouldOpenWhenNotSmall.current = false;
    }

    if (isSmall !== previousIsSmall.current) {
      previousIsSmall.current = isSmall;
    }
  }, [isActive, isSmall, scope, identifier, activeArea]);
}

function ComplementaryArea(_ref3) {
  let {
    children,
    className,
    closeLabel = (0,external_wp_i18n_namespaceObject.__)('Close plugin'),
    identifier,
    header,
    headerClassName,
    icon,
    isPinnable = true,
    panelClassName,
    scope,
    name,
    smallScreenTitle,
    title,
    toggleShortcut,
    isActiveByDefault,
    showIconLabels = false
  } = _ref3;
  const {
    isActive,
    isPinned,
    activeArea,
    isSmall,
    isLarge
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getActiveComplementaryArea,
      isItemPinned
    } = select(store);

    const _activeArea = getActiveComplementaryArea(scope);

    return {
      isActive: _activeArea === identifier,
      isPinned: isItemPinned(scope, identifier),
      activeArea: _activeArea,
      isSmall: select(external_wp_viewport_namespaceObject.store).isViewportMatch('< medium'),
      isLarge: select(external_wp_viewport_namespaceObject.store).isViewportMatch('large')
    };
  }, [identifier, scope]);
  useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall);
  const {
    enableComplementaryArea,
    disableComplementaryArea,
    pinItem,
    unpinItem
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isActiveByDefault && activeArea === undefined && !isSmall) {
      enableComplementaryArea(scope, identifier);
    }
  }, [activeArea, isActiveByDefault, scope, identifier, isSmall]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isPinnable && (0,external_wp_element_namespaceObject.createElement)(pinned_items, {
    scope: scope
  }, isPinned && (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, {
    scope: scope,
    identifier: identifier,
    isPressed: isActive && (!showIconLabels || isLarge),
    "aria-expanded": isActive,
    label: title,
    icon: showIconLabels ? library_check : icon,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  })), name && isPinnable && (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaMoreMenuItem, {
    target: name,
    scope: scope,
    icon: icon
  }, title), isActive && (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaFill, {
    className: classnames_default()('interface-complementary-area', className),
    scope: scope
  }, (0,external_wp_element_namespaceObject.createElement)(complementary_area_header, {
    className: headerClassName,
    closeLabel: closeLabel,
    onClose: () => disableComplementaryArea(scope),
    smallScreenTitle: smallScreenTitle,
    toggleButtonProps: {
      label: closeLabel,
      shortcut: toggleShortcut,
      scope,
      identifier
    }
  }, header || (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("strong", null, title), isPinnable && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "interface-complementary-area__pin-unpin-item",
    icon: isPinned ? star_filled : star_empty,
    label: isPinned ? (0,external_wp_i18n_namespaceObject.__)('Unpin from toolbar') : (0,external_wp_i18n_namespaceObject.__)('Pin to toolbar'),
    onClick: () => (isPinned ? unpinItem : pinItem)(scope, identifier),
    isPressed: isPinned,
    "aria-expanded": isPinned
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Panel, {
    className: panelClassName
  }, children)));
}

const ComplementaryAreaWrapped = complementary_area_context(ComplementaryArea);
ComplementaryAreaWrapped.Slot = ComplementaryAreaSlot;
/* harmony default export */ var complementary_area = (ComplementaryAreaWrapped);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/fullscreen-mode/index.js
/**
 * WordPress dependencies
 */


const FullscreenMode = _ref => {
  let {
    isActive
  } = _ref;
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    let isSticky = false; // `is-fullscreen-mode` is set in PHP as a body class by Gutenberg, and this causes
    // `sticky-menu` to be applied by WordPress and prevents the admin menu being scrolled
    // even if `is-fullscreen-mode` is then removed. Let's remove `sticky-menu` here as
    // a consequence of the FullscreenMode setup.

    if (document.body.classList.contains('sticky-menu')) {
      isSticky = true;
      document.body.classList.remove('sticky-menu');
    }

    return () => {
      if (isSticky) {
        document.body.classList.add('sticky-menu');
      }
    };
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isActive) {
      document.body.classList.add('is-fullscreen-mode');
    } else {
      document.body.classList.remove('is-fullscreen-mode');
    }

    return () => {
      if (isActive) {
        document.body.classList.remove('is-fullscreen-mode');
      }
    };
  }, [isActive]);
  return null;
};

/* harmony default export */ var fullscreen_mode = (FullscreenMode);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/interface-skeleton/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






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

function InterfaceSkeleton(_ref, ref) {
  let {
    footer,
    header,
    sidebar,
    secondarySidebar,
    notices,
    content,
    drawer,
    actions,
    labels,
    className,
    shortcuts
  } = _ref;
  const navigateRegionsProps = (0,external_wp_components_namespaceObject.__unstableUseNavigateRegions)(shortcuts);
  useHTMLClass('interface-interface-skeleton__html-container');
  const defaultLabels = {
    /* translators: accessibility text for the nav bar landmark region. */
    drawer: (0,external_wp_i18n_namespaceObject.__)('Drawer'),

    /* translators: accessibility text for the top bar landmark region. */
    header: (0,external_wp_i18n_namespaceObject.__)('Header'),

    /* translators: accessibility text for the content landmark region. */
    body: (0,external_wp_i18n_namespaceObject.__)('Content'),

    /* translators: accessibility text for the secondary sidebar landmark region. */
    secondarySidebar: (0,external_wp_i18n_namespaceObject.__)('Block Library'),

    /* translators: accessibility text for the settings landmark region. */
    sidebar: (0,external_wp_i18n_namespaceObject.__)('Settings'),

    /* translators: accessibility text for the publish landmark region. */
    actions: (0,external_wp_i18n_namespaceObject.__)('Publish'),

    /* translators: accessibility text for the footer landmark region. */
    footer: (0,external_wp_i18n_namespaceObject.__)('Footer')
  };
  const mergedLabels = { ...defaultLabels,
    ...labels
  };
  return (0,external_wp_element_namespaceObject.createElement)("div", _extends({}, navigateRegionsProps, {
    ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([ref, navigateRegionsProps.ref]),
    className: classnames_default()(className, 'interface-interface-skeleton', navigateRegionsProps.className, !!footer && 'has-footer')
  }), !!drawer && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__drawer",
    role: "region",
    "aria-label": mergedLabels.drawer,
    tabIndex: "-1"
  }, drawer), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__editor"
  }, !!header && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__header",
    role: "region",
    "aria-label": mergedLabels.header,
    tabIndex: "-1"
  }, header), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__body"
  }, !!secondarySidebar && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__secondary-sidebar",
    role: "region",
    "aria-label": mergedLabels.secondarySidebar,
    tabIndex: "-1"
  }, secondarySidebar), !!notices && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__notices"
  }, notices), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__content",
    role: "region",
    "aria-label": mergedLabels.body,
    tabIndex: "-1"
  }, content), !!sidebar && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__sidebar",
    role: "region",
    "aria-label": mergedLabels.sidebar,
    tabIndex: "-1"
  }, sidebar), !!actions && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__actions",
    role: "region",
    "aria-label": mergedLabels.actions,
    tabIndex: "-1"
  }, actions))), !!footer && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__footer",
    role: "region",
    "aria-label": mergedLabels.footer,
    tabIndex: "-1"
  }, footer));
}

/* harmony default export */ var interface_skeleton = ((0,external_wp_element_namespaceObject.forwardRef)(InterfaceSkeleton));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/more-vertical.js


/**
 * WordPress dependencies
 */

const moreVertical = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"
}));
/* harmony default export */ var more_vertical = (moreVertical);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/more-menu-dropdown/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function MoreMenuDropdown(_ref) {
  let {
    as: DropdownComponent = external_wp_components_namespaceObject.DropdownMenu,
    className,

    /* translators: button label text should, if possible, be under 16 characters. */
    label = (0,external_wp_i18n_namespaceObject.__)('Options'),
    popoverProps,
    toggleProps,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(DropdownComponent, {
    className: classnames_default()('interface-more-menu-dropdown', className),
    icon: more_vertical,
    label: label,
    popoverProps: {
      position: 'bottom left',
      ...popoverProps,
      className: classnames_default()('interface-more-menu-dropdown__content', popoverProps === null || popoverProps === void 0 ? void 0 : popoverProps.className)
    },
    toggleProps: {
      tooltipPosition: 'bottom',
      ...toggleProps
    }
  }, onClose => children(onClose));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal/index.js


/**
 * WordPress dependencies
 */


function PreferencesModal(_ref) {
  let {
    closeModal,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    className: "interface-preferences-modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Preferences'),
    closeLabel: (0,external_wp_i18n_namespaceObject.__)('Close'),
    onRequestClose: closeModal
  }, children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */

/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps} props icon is the SVG component to render
 *                          size is a number specifiying the icon size in pixels
 *                          Other props will be passed to wrapped SVG component
 *
 * @return {JSX.Element}  Icon component
 */

function Icon(_ref) {
  let {
    icon,
    size = 24,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ var icon = (Icon);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-left.js


/**
 * WordPress dependencies
 */

const chevronLeft = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"
}));
/* harmony default export */ var chevron_left = (chevronLeft);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-right.js


/**
 * WordPress dependencies
 */

const chevronRight = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"
}));
/* harmony default export */ var chevron_right = (chevronRight);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal-tabs/index.js


/**
 * WordPress dependencies
 */





const PREFERENCES_MENU = 'preferences-menu';
function PreferencesModalTabs(_ref) {
  let {
    sections
  } = _ref;
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium'); // This is also used to sync the two different rendered components
  // between small and large viewports.

  const [activeMenu, setActiveMenu] = (0,external_wp_element_namespaceObject.useState)(PREFERENCES_MENU);
  /**
   * Create helper objects from `sections` for easier data handling.
   * `tabs` is used for creating the `TabPanel` and `sectionsContentMap`
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
      mappedTabs = sections.reduce((accumulator, _ref2) => {
        let {
          name,
          tabLabel: title,
          content
        } = _ref2;
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
  const getCurrentTab = (0,external_wp_element_namespaceObject.useCallback)(tab => sectionsContentMap[tab.name] || null, [sectionsContentMap]);
  let modalContent; // We render different components based on the viewport size.

  if (isLargeViewport) {
    modalContent = (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TabPanel, {
      className: "interface-preferences__tabs",
      tabs: tabs,
      initialTabName: activeMenu !== PREFERENCES_MENU ? activeMenu : undefined,
      onSelect: setActiveMenu,
      orientation: "vertical"
    }, getCurrentTab);
  } else {
    modalContent = (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorProvider, {
      initialPath: "/",
      className: "interface-preferences__provider"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
      path: "/"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Card, {
      isBorderless: true,
      size: "small"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardBody, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, null, tabs.map(tab => {
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorButton, {
        key: tab.name,
        path: tab.name,
        as: external_wp_components_namespaceObject.__experimentalItem,
        isAction: true
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
        justify: "space-between"
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalTruncate, null, tab.title)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(icon, {
        icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_left : chevron_right
      }))));
    }))))), sections.length && sections.map(section => {
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
        key: `${section.name}-menu`,
        path: section.name
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Card, {
        isBorderless: true,
        size: "large"
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardHeader, {
        isBorderless: false,
        justify: "left",
        size: "small",
        gap: "6"
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorBackButton, {
        icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_right : chevron_left,
        "aria-label": (0,external_wp_i18n_namespaceObject.__)('Navigate to the previous view')
      }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
        size: "16"
      }, section.tabLabel)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardBody, null, section.content)));
    }));
  }

  return modalContent;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal-section/index.js


const Section = _ref => {
  let {
    description,
    title,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)("fieldset", {
    className: "interface-preferences-modal__section"
  }, (0,external_wp_element_namespaceObject.createElement)("legend", {
    className: "interface-preferences-modal__section-legend"
  }, (0,external_wp_element_namespaceObject.createElement)("h2", {
    className: "interface-preferences-modal__section-title"
  }, title), description && (0,external_wp_element_namespaceObject.createElement)("p", {
    className: "interface-preferences-modal__section-description"
  }, description)), children);
};

/* harmony default export */ var preferences_modal_section = (Section);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal-base-option/index.js


/**
 * WordPress dependencies
 */


function BaseOption(_ref) {
  let {
    help,
    label,
    isChecked,
    onChange,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-preferences-modal__option"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToggleControl, {
    __nextHasNoMarginBottom: true,
    help: help,
    label: label,
    checked: isChecked,
    onChange: onChange
  }), children);
}

/* harmony default export */ var preferences_modal_base_option = (BaseOption);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/index.js













;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/index.js



;// CONCATENATED MODULE: external ["wp","a11y"]
var external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// CONCATENATED MODULE: external ["wp","coreData"]
var external_wp_coreData_namespaceObject = window["wp"]["coreData"];
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
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
 * Returns an action object used in signalling that the user opened an editor sidebar.
 *
 * @param {?string} name Sidebar name to be opened.
 */

const openGeneralSidebar = name => _ref => {
  let {
    registry
  } = _ref;
  return registry.dispatch(store).enableComplementaryArea(store_store.name, name);
};
/**
 * Returns an action object signalling that the user closed the sidebar.
 */

const closeGeneralSidebar = () => _ref2 => {
  let {
    registry
  } = _ref2;
  return registry.dispatch(store).disableComplementaryArea(store_store.name);
};
/**
 * Returns an action object used in signalling that the user opened a modal.
 *
 * @param {string} name A string that uniquely identifies the modal.
 *
 * @return {Object} Action object.
 */

function openModal(name) {
  return {
    type: 'OPEN_MODAL',
    name
  };
}
/**
 * Returns an action object signalling that the user closed a modal.
 *
 * @return {Object} Action object.
 */

function closeModal() {
  return {
    type: 'CLOSE_MODAL'
  };
}
/**
 * Returns an action object used in signalling that the user opened the publish
 * sidebar.
 *
 * @return {Object} Action object
 */

function openPublishSidebar() {
  return {
    type: 'OPEN_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user closed the
 * publish sidebar.
 *
 * @return {Object} Action object.
 */

function closePublishSidebar() {
  return {
    type: 'CLOSE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user toggles the publish sidebar.
 *
 * @return {Object} Action object
 */

function togglePublishSidebar() {
  return {
    type: 'TOGGLE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used to enable or disable a panel in the editor.
 *
 * @param {string} panelName A string that identifies the panel to enable or disable.
 *
 * @return {Object} Action object.
 */

const toggleEditorPanelEnabled = panelName => _ref3 => {
  var _registry$select$get;

  let {
    registry
  } = _ref3;
  const inactivePanels = (_registry$select$get = registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'inactivePanels')) !== null && _registry$select$get !== void 0 ? _registry$select$get : [];
  const isPanelInactive = !!(inactivePanels !== null && inactivePanels !== void 0 && inactivePanels.includes(panelName)); // If the panel is inactive, remove it to enable it, else add it to
  // make it inactive.

  let updatedInactivePanels;

  if (isPanelInactive) {
    updatedInactivePanels = inactivePanels.filter(invactivePanelName => invactivePanelName !== panelName);
  } else {
    updatedInactivePanels = [...inactivePanels, panelName];
  }

  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'inactivePanels', updatedInactivePanels);
};
/**
 * Opens a closed panel and closes an open panel.
 *
 * @param {string} panelName A string that identifies the panel to open or close.
 */

const toggleEditorPanelOpened = panelName => _ref4 => {
  var _registry$select$get2;

  let {
    registry
  } = _ref4;
  const openPanels = (_registry$select$get2 = registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'openPanels')) !== null && _registry$select$get2 !== void 0 ? _registry$select$get2 : [];
  const isPanelOpen = !!(openPanels !== null && openPanels !== void 0 && openPanels.includes(panelName)); // If the panel is open, remove it to close it, else add it to
  // make it open.

  let updatedOpenPanels;

  if (isPanelOpen) {
    updatedOpenPanels = openPanels.filter(openPanelName => openPanelName !== panelName);
  } else {
    updatedOpenPanels = [...openPanels, panelName];
  }

  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'openPanels', updatedOpenPanels);
};
/**
 * Returns an action object used to remove a panel from the editor.
 *
 * @param {string} panelName A string that identifies the panel to remove.
 *
 * @return {Object} Action object.
 */

function removeEditorPanel(panelName) {
  return {
    type: 'REMOVE_PANEL',
    panelName
  };
}
/**
 * Triggers an action used to toggle a feature flag.
 *
 * @param {string} feature Feature name.
 */

const actions_toggleFeature = feature => _ref5 => {
  let {
    registry
  } = _ref5;
  return registry.dispatch(external_wp_preferences_namespaceObject.store).toggle('core/edit-post', feature);
};
/**
 * Triggers an action used to switch editor mode.
 *
 * @param {string} mode The editor mode.
 */

const switchEditorMode = mode => _ref6 => {
  let {
    registry
  } = _ref6;
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'editorMode', mode); // Unselect blocks when we switch to the code editor.

  if (mode !== 'visual') {
    registry.dispatch(external_wp_blockEditor_namespaceObject.store).clearSelectedBlock();
  }

  const message = mode === 'visual' ? (0,external_wp_i18n_namespaceObject.__)('Visual editor selected') : (0,external_wp_i18n_namespaceObject.__)('Code editor selected');
  (0,external_wp_a11y_namespaceObject.speak)(message, 'assertive');
};
/**
 * Triggers an action object used to toggle a plugin name flag.
 *
 * @param {string} pluginName Plugin name.
 */

const togglePinnedPluginItem = pluginName => _ref7 => {
  let {
    registry
  } = _ref7;
  const isPinned = registry.select(store).isItemPinned('core/edit-post', pluginName);
  registry.dispatch(store)[isPinned ? 'unpinItem' : 'pinItem']('core/edit-post', pluginName);
};
/**
 * Returns an action object used in signaling that a style should be auto-applied when a block is created.
 *
 * @param {string}  blockName  Name of the block.
 * @param {?string} blockStyle Name of the style that should be auto applied. If undefined, the "auto apply" setting of the block is removed.
 */

const updatePreferredStyleVariations = (blockName, blockStyle) => _ref8 => {
  var _registry$select$get3;

  let {
    registry
  } = _ref8;

  if (!blockName) {
    return;
  }

  const existingVariations = (_registry$select$get3 = registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'preferredStyleVariations')) !== null && _registry$select$get3 !== void 0 ? _registry$select$get3 : {}; // When the blockStyle is omitted, remove the block's preferred variation.

  if (!blockStyle) {
    const updatedVariations = { ...existingVariations
    };
    delete updatedVariations[blockName];
    registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'preferredStyleVariations', updatedVariations);
  } else {
    // Else add the variation.
    registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'preferredStyleVariations', { ...existingVariations,
      [blockName]: blockStyle
    });
  }
};
/**
 * Update the provided block types to be visible.
 *
 * @param {string[]} blockNames Names of block types to show.
 */

const showBlockTypes = blockNames => _ref9 => {
  var _registry$select$get4;

  let {
    registry
  } = _ref9;
  const existingBlockNames = (_registry$select$get4 = registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'hiddenBlockTypes')) !== null && _registry$select$get4 !== void 0 ? _registry$select$get4 : [];
  const newBlockNames = (0,external_lodash_namespaceObject.without)(existingBlockNames, ...(0,external_lodash_namespaceObject.castArray)(blockNames));
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'hiddenBlockTypes', newBlockNames);
};
/**
 * Update the provided block types to be hidden.
 *
 * @param {string[]} blockNames Names of block types to hide.
 */

const hideBlockTypes = blockNames => _ref10 => {
  var _registry$select$get5;

  let {
    registry
  } = _ref10;
  const existingBlockNames = (_registry$select$get5 = registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'hiddenBlockTypes')) !== null && _registry$select$get5 !== void 0 ? _registry$select$get5 : [];
  const mergedBlockNames = new Set([...existingBlockNames, ...(0,external_lodash_namespaceObject.castArray)(blockNames)]);
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'hiddenBlockTypes', [...mergedBlockNames]);
};
/**
 * Returns an action object used in signaling
 * what Meta boxes are available in which location.
 *
 * @param {Object} metaBoxesPerLocation Meta boxes per location.
 */

const setAvailableMetaBoxesPerLocation = metaBoxesPerLocation => _ref11 => {
  let {
    dispatch
  } = _ref11;
  return dispatch({
    type: 'SET_META_BOXES_PER_LOCATIONS',
    metaBoxesPerLocation
  });
};
/**
 * Update a metabox.
 */

const requestMetaBoxUpdates = () => async _ref12 => {
  let {
    registry,
    select,
    dispatch
  } = _ref12;
  dispatch({
    type: 'REQUEST_META_BOX_UPDATES'
  }); // Saves the wp_editor fields.

  if (window.tinyMCE) {
    window.tinyMCE.triggerSave();
  } // Additional data needed for backward compatibility.
  // If we do not provide this data, the post will be overridden with the default values.


  const post = registry.select(external_wp_editor_namespaceObject.store).getCurrentPost();
  const additionalData = [post.comment_status ? ['comment_status', post.comment_status] : false, post.ping_status ? ['ping_status', post.ping_status] : false, post.sticky ? ['sticky', post.sticky] : false, post.author ? ['post_author', post.author] : false].filter(Boolean); // We gather all the metaboxes locations data and the base form data.

  const baseFormData = new window.FormData(document.querySelector('.metabox-base-form'));
  const activeMetaBoxLocations = select.getActiveMetaBoxLocations();
  const formDataToMerge = [baseFormData, ...activeMetaBoxLocations.map(location => new window.FormData(getMetaBoxContainer(location)))]; // Merge all form data objects into a single one.

  const formData = (0,external_lodash_namespaceObject.reduce)(formDataToMerge, (memo, currentFormData) => {
    for (const [key, value] of currentFormData) {
      memo.append(key, value);
    }

    return memo;
  }, new window.FormData());
  additionalData.forEach(_ref13 => {
    let [key, value] = _ref13;
    return formData.append(key, value);
  });

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
 * Returns an action object used to toggle the width of the editing canvas.
 *
 * @param {string} deviceType
 *
 * @return {Object} Action object.
 */

function __experimentalSetPreviewDeviceType(deviceType) {
  return {
    type: 'SET_PREVIEW_DEVICE_TYPE',
    deviceType
  };
}
/**
 * Returns an action object used to open/close the inserter.
 *
 * @param {boolean|Object} value                Whether the inserter should be
 *                                              opened (true) or closed (false).
 *                                              To specify an insertion point,
 *                                              use an object.
 * @param {string}         value.rootClientId   The root client ID to insert at.
 * @param {number}         value.insertionIndex The index to insert at.
 *
 * @return {Object} Action object.
 */

function setIsInserterOpened(value) {
  return {
    type: 'SET_IS_INSERTER_OPENED',
    value
  };
}
/**
 * Returns an action object used to open/close the list view.
 *
 * @param {boolean} isOpen A boolean representing whether the list view should be opened or closed.
 * @return {Object} Action object.
 */

function setIsListViewOpened(isOpen) {
  return {
    type: 'SET_IS_LIST_VIEW_OPENED',
    isOpen
  };
}
/**
 * Returns an action object used to switch to template editing.
 *
 * @param {boolean} value Is editing template.
 * @return {Object} Action object.
 */

function setIsEditingTemplate(value) {
  return {
    type: 'SET_IS_EDITING_TEMPLATE',
    value
  };
}
/**
 * Switches to the template mode.
 *
 * @param {boolean} newTemplate Is new template.
 */

const __unstableSwitchToTemplateMode = function () {
  let newTemplate = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  return _ref14 => {
    let {
      registry,
      select,
      dispatch
    } = _ref14;
    dispatch(setIsEditingTemplate(true));
    const isWelcomeGuideActive = select.isFeatureActive('welcomeGuideTemplate');

    if (!isWelcomeGuideActive) {
      const message = newTemplate ? (0,external_wp_i18n_namespaceObject.__)("Custom template created. You're in template mode now.") : (0,external_wp_i18n_namespaceObject.__)('Editing template. Changes made here affect all posts and pages that use the template.');
      registry.dispatch(external_wp_notices_namespaceObject.store).createSuccessNotice(message, {
        type: 'snackbar'
      });
    }
  };
};
/**
 * Create a block based template.
 *
 * @param {Object?} template Template to create and assign.
 */

const __unstableCreateTemplate = template => async _ref15 => {
  let {
    registry
  } = _ref15;
  const savedTemplate = await registry.dispatch(external_wp_coreData_namespaceObject.store).saveEntityRecord('postType', 'wp_template', template);
  const post = registry.select(external_wp_editor_namespaceObject.store).getCurrentPost();
  registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord('postType', post.type, post.id, {
    template: savedTemplate.slug
  });
};
let actions_metaBoxesInitialized = false;
/**
 * Initializes WordPress `postboxes` script and the logic for saving meta boxes.
 */

const initializeMetaBoxes = () => _ref16 => {
  let {
    registry,
    select,
    dispatch
  } = _ref16;

  const isEditorReady = registry.select(external_wp_editor_namespaceObject.store).__unstableIsEditorReady();

  if (!isEditorReady) {
    return;
  } // Only initialize once.


  if (actions_metaBoxesInitialized) {
    return;
  }

  const postType = registry.select(external_wp_editor_namespaceObject.store).getCurrentPostType();

  if (window.postboxes.page !== postType) {
    window.postboxes.add_postbox_toggles(postType);
  }

  actions_metaBoxesInitialized = true;
  let wasSavingPost = registry.select(external_wp_editor_namespaceObject.store).isSavingPost();
  let wasAutosavingPost = registry.select(external_wp_editor_namespaceObject.store).isAutosavingPost();
  const hasMetaBoxes = select.hasMetaBoxes(); // Save metaboxes when performing a full save on the post.

  registry.subscribe(async () => {
    const isSavingPost = registry.select(external_wp_editor_namespaceObject.store).isSavingPost();
    const isAutosavingPost = registry.select(external_wp_editor_namespaceObject.store).isAutosavingPost(); // Save metaboxes on save completion, except for autosaves that are not a post preview.
    //
    // Meta boxes are initialized once at page load. It is not necessary to
    // account for updates on each state change.
    //
    // See: https://github.com/WordPress/WordPress/blob/5.1.1/wp-admin/includes/post.php#L2307-L2309.

    const shouldTriggerMetaboxesSave = hasMetaBoxes && wasSavingPost && !isSavingPost && !wasAutosavingPost; // Save current state for next inspection.

    wasSavingPost = isSavingPost;
    wasAutosavingPost = isAutosavingPost;

    if (shouldTriggerMetaboxesSave) {
      await dispatch.requestMetaBoxUpdates();
    }
  });
  dispatch({
    type: 'META_BOXES_INITIALIZED'
  });
};

;// CONCATENATED MODULE: ./node_modules/rememo/rememo.js


/** @typedef {(...args: any[]) => *[]} GetDependants */

/** @typedef {() => void} Clear */

/**
 * @typedef {{
 *   getDependants: GetDependants,
 *   clear: Clear
 * }} EnhancedSelector
 */

/**
 * Internal cache entry.
 *
 * @typedef CacheNode
 *
 * @property {?CacheNode|undefined} [prev] Previous node.
 * @property {?CacheNode|undefined} [next] Next node.
 * @property {*[]} args Function arguments for cache entry.
 * @property {*} val Function result.
 */

/**
 * @typedef Cache
 *
 * @property {Clear} clear Function to clear cache.
 * @property {boolean} [isUniqueByDependants] Whether dependants are valid in
 * considering cache uniqueness. A cache is unique if dependents are all arrays
 * or objects.
 * @property {CacheNode?} [head] Cache head.
 * @property {*[]} [lastDependants] Dependants from previous invocation.
 */

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {{}}
 */
var LEAF_KEY = {};

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @template T
 *
 * @param {T} value Value to return.
 *
 * @return {[T]} Value returned as entry in array.
 */
function arrayOf(value) {
	return [value];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike(value) {
	return !!value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Cache} Cache object.
 */
function createCache() {
	/** @type {Cache} */
	var cache = {
		clear: function () {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {*[]} a First array.
 * @param {*[]} b Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual(a, b, fromIndex) {
	var i;

	if (a.length !== b.length) {
		return false;
	}

	for (i = fromIndex; i < a.length; i++) {
		if (a[i] !== b[i]) {
			return false;
		}
	}

	return true;
}

/**
 * Returns a memoized selector function. The getDependants function argument is
 * called before the memoized selector and is expected to return an immutable
 * reference or array of references on which the selector depends for computing
 * its own return value. The memoize cache is preserved only as long as those
 * dependant references remain the same. If getDependants returns a different
 * reference(s), the cache is cleared and the selector value regenerated.
 *
 * @template {(...args: *[]) => *} S
 *
 * @param {S} selector Selector function.
 * @param {GetDependants=} getDependants Dependant getter returning an array of
 * references used in cache bust consideration.
 */
/* harmony default export */ function rememo(selector, getDependants) {
	/** @type {WeakMap<*,*>} */
	var rootCache;

	/** @type {GetDependants} */
	var normalizedGetDependants = getDependants ? getDependants : arrayOf;

	/**
	 * Returns the cache for a given dependants array. When possible, a WeakMap
	 * will be used to create a unique cache for each set of dependants. This
	 * is feasible due to the nature of WeakMap in allowing garbage collection
	 * to occur on entries where the key object is no longer referenced. Since
	 * WeakMap requires the key to be an object, this is only possible when the
	 * dependant is object-like. The root cache is created as a hierarchy where
	 * each top-level key is the first entry in a dependants set, the value a
	 * WeakMap where each key is the next dependant, and so on. This continues
	 * so long as the dependants are object-like. If no dependants are object-
	 * like, then the cache is shared across all invocations.
	 *
	 * @see isObjectLike
	 *
	 * @param {*[]} dependants Selector dependants.
	 *
	 * @return {Cache} Cache object.
	 */
	function getCache(dependants) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i,
			dependant,
			map,
			cache;

		for (i = 0; i < dependants.length; i++) {
			dependant = dependants[i];

			// Can only compose WeakMap from object-like key.
			if (!isObjectLike(dependant)) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if (caches.has(dependant)) {
				// Traverse into nested WeakMap.
				caches = caches.get(dependant);
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set(dependant, map);
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if (!caches.has(LEAF_KEY)) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set(LEAF_KEY, cache);
		}

		return caches.get(LEAF_KEY);
	}

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = new WeakMap();
	}

	/* eslint-disable jsdoc/check-param-names */
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {*}    source    Source object for derivation.
	 * @param {...*} extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	/* eslint-enable jsdoc/check-param-names */
	function callSelector(/* source, ...extraArgs */) {
		var len = arguments.length,
			cache,
			node,
			i,
			args,
			dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		dependants = normalizedGetDependants.apply(null, args);
		cache = getCache(dependants);

		// If not guaranteed uniqueness by dependants (primitive type), shallow
		// compare against last dependants and, if references have changed,
		// destroy cache to recalculate result.
		if (!cache.isUniqueByDependants) {
			if (
				cache.lastDependants &&
				!isShallowEqual(dependants, cache.lastDependants, 0)
			) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while (node) {
			// Check whether node arguments match arguments
			if (!isShallowEqual(node.args, args, 1)) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== cache.head) {
				// Adjust siblings to point to each other.
				/** @type {CacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				/** @type {CacheNode} */ (cache.head).prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = /** @type {CacheNode} */ ({
			// Generate the result from original function
			val: selector.apply(null, args),
		});

		// Avoid including the source object in the cache.
		args[0] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (cache.head) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = normalizedGetDependants;
	callSelector.clear = clear;
	clear();

	return /** @type {S & EnhancedSelector} */ (callSelector);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







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

  return (_select$get = select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'editorMode')) !== null && _select$get !== void 0 ? _select$get : 'visual';
});
/**
 * Returns true if the editor sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the editor sidebar is opened.
 */

const isEditorSidebarOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  const activeGeneralSidebar = select(store).getActiveComplementaryArea('core/edit-post');
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
  const activeGeneralSidebar = select(store).getActiveComplementaryArea('core/edit-post');
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
  return select(store).getActiveComplementaryArea('core/edit-post');
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
  const panelsWithEnabledState = inactivePanels === null || inactivePanels === void 0 ? void 0 : inactivePanels.reduce((accumulatedPanels, panelName) => ({ ...accumulatedPanels,
    [panelName]: {
      enabled: false
    }
  }), {}); // Then reduce the open panels, passing in the result of the previous
  // reduction as the initial value so that both open and inactive
  // panel state is combined.

  const panels = openPanels === null || openPanels === void 0 ? void 0 : openPanels.reduce((accumulatedPanels, panelName) => {
    const currentPanelState = accumulatedPanels === null || accumulatedPanels === void 0 ? void 0 : accumulatedPanels[panelName];
    return { ...accumulatedPanels,
      [panelName]: { ...currentPanelState,
        opened: true
      }
    };
  }, panelsWithEnabledState !== null && panelsWithEnabledState !== void 0 ? panelsWithEnabledState : {}); // The panels variable will only be set if openPanels wasn't `undefined`.
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
  }); // These preferences now exist in the preferences store.
  // Fetch them so that they can be merged into the post
  // editor preferences.

  const preferences = ['hiddenBlockTypes', 'editorMode', 'preferredStyleVariations'].reduce((accumulatedPrefs, preferenceKey) => {
    const value = select(external_wp_preferences_namespaceObject.store).get('core/edit-post', preferenceKey);
    return { ...accumulatedPrefs,
      [preferenceKey]: value
    };
  }, {}); // Panels were a preference, but the data structure changed when the state
  // was migrated to the preferences store. They need to be converted from
  // the new preferences store format to old format to ensure no breaking
  // changes for plugins.

  const inactivePanels = select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'inactivePanels');
  const openPanels = select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'openPanels');
  const panels = convertPanelsToOldFormat(inactivePanels, openPanels);
  return { ...preferences,
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
  }); // Avoid using the `getPreferences` registry selector where possible.

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

  return (_select$get2 = select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'hiddenBlockTypes')) !== null && _select$get2 !== void 0 ? _select$get2 : EMPTY_ARRAY;
});
/**
 * Returns true if the publish sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the publish sidebar is open.
 */

function isPublishSidebarOpened(state) {
  return state.publishSidebarActive;
}
/**
 * Returns true if the given panel was programmatically removed, or false otherwise.
 * All panels are not removed by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is removed.
 */

function isEditorPanelRemoved(state, panelName) {
  return state.removedPanels.includes(panelName);
}
/**
 * Returns true if the given panel is enabled, or false otherwise. Panels are
 * enabled by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is enabled.
 */

const isEditorPanelEnabled = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, panelName) => {
  const inactivePanels = select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'inactivePanels');
  return !isEditorPanelRemoved(state, panelName) && !(inactivePanels !== null && inactivePanels !== void 0 && inactivePanels.includes(panelName));
});
/**
 * Returns true if the given panel is open, or false otherwise. Panels are
 * closed by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is open.
 */

const isEditorPanelOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, panelName) => {
  const openPanels = select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'openPanels');
  return !!(openPanels !== null && openPanels !== void 0 && openPanels.includes(panelName));
});
/**
 * Returns true if a modal is active, or false otherwise.
 *
 * @param {Object} state     Global application state.
 * @param {string} modalName A string that uniquely identifies the modal.
 *
 * @return {boolean} Whether the modal is active.
 */

function isModalActive(state, modalName) {
  return state.activeModal === modalName;
}
/**
 * Returns whether the given feature is enabled or not.
 *
 * @param {Object} state   Global application state.
 * @param {string} feature Feature slug.
 *
 * @return {boolean} Is active.
 */

const selectors_isFeatureActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, feature) => {
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
  return select(store).isItemPinned('core/edit-post', pluginName);
});
/**
 * Returns an array of active meta box locations.
 *
 * @param {Object} state Post editor state.
 *
 * @return {string[]} Active meta box locations.
 */

const getActiveMetaBoxLocations = rememo(state => {
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

function isMetaBoxLocationVisible(state, location) {
  var _getMetaBoxesPerLocat;

  return isMetaBoxLocationActive(state, location) && ((_getMetaBoxesPerLocat = getMetaBoxesPerLocation(state, location)) === null || _getMetaBoxesPerLocat === void 0 ? void 0 : _getMetaBoxesPerLocat.some(_ref2 => {
    let {
      id
    } = _ref2;
    return isEditorPanelEnabled(state, `meta-box-${id}`);
  }));
}
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

const getAllMetaBoxes = rememo(state => {
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
 * @param {Object} state Global application state.
 *
 * @return {string} Device type.
 */

function __experimentalGetPreviewDeviceType(state) {
  return state.deviceType;
}
/**
 * Returns true if the inserter is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the inserter is opened.
 */

function isInserterOpened(state) {
  return !!state.blockInserterPanel;
}
/**
 * Get the insertion point for the inserter.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The root client ID, index to insert at and starting filter value.
 */

function __experimentalGetInsertionPoint(state) {
  const {
    rootClientId,
    insertionIndex,
    filterValue
  } = state.blockInserterPanel;
  return {
    rootClientId,
    insertionIndex,
    filterValue
  };
}
/**
 * Returns true if the list view is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the list view is opened.
 */

function isListViewOpened(state) {
  return state.listViewPanel;
}
/**
 * Returns true if the template editing mode is enabled.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether we're editing the template.
 */

function selectors_isEditingTemplate(state) {
  return state.isEditingTemplate;
}
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
  const currentTemplate = select(external_wp_editor_namespaceObject.store).getEditedPostAttribute('template');

  if (currentTemplate) {
    var _select$getEntityReco;

    const templateWithSameSlug = (_select$getEntityReco = select(external_wp_coreData_namespaceObject.store).getEntityRecords('postType', 'wp_template', {
      per_page: -1
    })) === null || _select$getEntityReco === void 0 ? void 0 : _select$getEntityReco.find(template => template.slug === currentTemplate);

    if (!templateWithSameSlug) {
      return templateWithSameSlug;
    }

    return select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord('postType', 'wp_template', templateWithSameSlug.id);
  }

  const post = select(external_wp_editor_namespaceObject.store).getCurrentPost();

  if (post.link) {
    return select(external_wp_coreData_namespaceObject.store).__experimentalGetTemplateForLink(post.link);
  }

  return null;
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const constants_STORE_NAME = 'core/edit-post';
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

const store_store = (0,external_wp_data_namespaceObject.createReduxStore)(constants_STORE_NAME, {
  reducer: reducer,
  actions: store_actions_namespaceObject,
  selectors: store_selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store_store);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function KeyboardShortcutsHelpMenuItem(_ref) {
  let {
    openModal
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      openModal('edit-post/keyboard-shortcut-help');
    },
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.access('h')
  }, (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts'));
}
/* harmony default export */ var keyboard_shortcuts_help_menu_item = ((0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    openModal
  } = dispatch(store_store);
  return {
    openModal
  };
})(KeyboardShortcutsHelpMenuItem));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/tools-more-menu-group/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



const {
  Fill: ToolsMoreMenuGroup,
  Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('ToolsMoreMenuGroup');

ToolsMoreMenuGroup.Slot = _ref => {
  let {
    fillProps
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(Slot, {
    fillProps: fillProps
  }, fills => !(0,external_lodash_namespaceObject.isEmpty)(fills) && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    label: (0,external_wp_i18n_namespaceObject.__)('Tools')
  }, fills));
};

/* harmony default export */ var tools_more_menu_group = (ToolsMoreMenuGroup);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/welcome-guide-menu-item/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function WelcomeGuideMenuItem() {
  const isTemplateMode = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).isEditingTemplate(), []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    name: isTemplateMode ? 'welcomeGuideTemplate' : 'welcomeGuide',
    label: (0,external_wp_i18n_namespaceObject.__)('Welcome Guide')
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





(0,external_wp_plugins_namespaceObject.registerPlugin)('edit-post', {
  render() {
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(tools_more_menu_group, null, _ref => {
      let {
        onClose
      } = _ref;
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
        role: "menuitem",
        href: (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
          post_type: 'wp_block'
        })
      }, (0,external_wp_i18n_namespaceObject.__)('Manage Reusable blocks')), (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcuts_help_menu_item, {
        onSelect: onClose
      }), (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideMenuItem, null), (0,external_wp_element_namespaceObject.createElement)(CopyContentMenuItem, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
        role: "menuitem",
        icon: library_external,
        href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/support/article/wordpress-editor/'),
        target: "_blank",
        rel: "noopener noreferrer"
      }, (0,external_wp_i18n_namespaceObject.__)('Help'), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
        as: "span"
      },
      /* translators: accessibility text */
      (0,external_wp_i18n_namespaceObject.__)('(opens in a new tab)'))));
    }));
  }

});

;// CONCATENATED MODULE: external ["wp","keyboardShortcuts"]
var external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/text-editor/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function TextEditor() {
  const isRichEditingEnabled = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return select(external_wp_editor_namespaceObject.store).getEditorSettings().richEditingEnabled;
  }, []);
  const {
    switchEditorMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-text-editor"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.TextEditorGlobalKeyboardShortcuts, null), isRichEditingEnabled && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-text-editor__toolbar"
  }, (0,external_wp_element_namespaceObject.createElement)("h2", null, (0,external_wp_i18n_namespaceObject.__)('Editing code')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: () => switchEditorMode('visual'),
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.secondary('m')
  }, (0,external_wp_i18n_namespaceObject.__)('Exit code editor'))), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-text-editor__body"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTitle, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTextEditor, null)));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/arrow-left.js


/**
 * WordPress dependencies
 */

const arrowLeft = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20 10.8H6.7l4.1-4.5-1.1-1.1-5.8 6.3 5.8 5.8 1.1-1.1-4-3.9H20z"
}));
/* harmony default export */ var arrow_left = (arrowLeft);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/block-inspector-button.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



const block_inspector_button_noop = () => {};

function BlockInspectorButton(_ref) {
  let {
    onClick = block_inspector_button_noop,
    small = false
  } = _ref;
  const {
    shortcut,
    areAdvancedSettingsOpened
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    shortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getShortcutRepresentation('core/edit-post/toggle-sidebar'),
    areAdvancedSettingsOpened: select(store_store).getActiveGeneralSidebarName() === 'edit-post/block'
  }), []);
  const {
    openGeneralSidebar,
    closeGeneralSidebar
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const label = areAdvancedSettingsOpened ? (0,external_wp_i18n_namespaceObject.__)('Hide more settings') : (0,external_wp_i18n_namespaceObject.__)('Show more settings');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      if (areAdvancedSettingsOpened) {
        closeGeneralSidebar();
        (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.__)('Block settings closed'));
      } else {
        openGeneralSidebar('edit-post/block');
        (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.__)('Additional settings are now available in the Editor block settings sidebar'));
      }

      onClick();
    },
    shortcut: shortcut
  }, !small && label);
}
/* harmony default export */ var block_inspector_button = (BlockInspectorButton);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */




function MaybeIframe(_ref) {
  let {
    children,
    contentRef,
    shouldIframe,
    styles,
    assets,
    style
  } = _ref;
  const ref = (0,external_wp_blockEditor_namespaceObject.__unstableUseMouseMoveTypingReset)();

  if (!shouldIframe) {
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableEditorStyles, {
      styles: styles
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.WritingFlow, {
      ref: contentRef,
      className: "editor-styles-wrapper",
      style: {
        flex: '1',
        ...style
      },
      tabIndex: -1
    }, children));
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableIframe, {
    head: (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableEditorStyles, {
      styles: styles
    }),
    assets: assets,
    ref: ref,
    contentRef: contentRef,
    style: {
      width: '100%',
      height: '100%',
      display: 'block'
    },
    name: "editor-canvas"
  }, children);
}
/**
 * Given an array of nested blocks, find the first Post Content
 * block inside it, recursing through any nesting levels.
 *
 * @param {Array} blocks A list of blocks.
 *
 * @return {Object} The Post Content block.
 */


function findPostContent(blocks) {
  for (let i = 0; i < blocks.length; i++) {
    if (blocks[i].name === 'core/post-content') {
      return blocks[i];
    }

    if (blocks[i].innerBlocks.length) {
      const nestedPostContent = findPostContent(blocks[i].innerBlocks);

      if (nestedPostContent) {
        return nestedPostContent;
      }
    }
  }
}

function VisualEditor(_ref2) {
  var _postContentBlock$att;

  let {
    styles
  } = _ref2;
  const {
    deviceType,
    isWelcomeGuideVisible,
    isTemplateMode,
    editedPostTemplate = {},
    wrapperBlockName,
    wrapperUniqueId
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isFeatureActive,
      isEditingTemplate,
      __experimentalGetPreviewDeviceType,
      getEditedPostTemplate
    } = select(store_store);
    const {
      getCurrentPostId,
      getCurrentPostType,
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);

    const _isTemplateMode = isEditingTemplate();

    let _wrapperBlockName;

    if (getCurrentPostType() === 'wp_block') {
      _wrapperBlockName = 'core/block';
    } else if (!_isTemplateMode) {
      _wrapperBlockName = 'core/post-content';
    }

    const supportsTemplateMode = getEditorSettings().supportsTemplateMode;
    return {
      deviceType: __experimentalGetPreviewDeviceType(),
      isWelcomeGuideVisible: isFeatureActive('welcomeGuide'),
      isTemplateMode: _isTemplateMode,
      // Post template fetch returns a 404 on classic themes, which
      // messes with e2e tests, so we check it's a block theme first.
      editedPostTemplate: supportsTemplateMode ? getEditedPostTemplate() : {},
      wrapperBlockName: _wrapperBlockName,
      wrapperUniqueId: getCurrentPostId()
    };
  }, []);
  const {
    isCleanNewPost
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_editor_namespaceObject.store);
  const hasMetaBoxes = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).hasMetaBoxes(), []);
  const {
    themeHasDisabledLayoutStyles,
    themeSupportsLayout,
    assets
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const _settings = select(external_wp_blockEditor_namespaceObject.store).getSettings();

    return {
      themeHasDisabledLayoutStyles: _settings.disableLayoutStyles,
      themeSupportsLayout: _settings.supportsLayout,
      assets: _settings.__unstableResolvedAssets
    };
  }, []);
  const {
    clearSelectedBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    setIsEditingTemplate
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const desktopCanvasStyles = {
    height: '100%',
    width: '100%',
    margin: 0,
    display: 'flex',
    flexFlow: 'column',
    // Default background color so that grey
    // .edit-post-editor-regions__content color doesn't show through.
    background: 'white'
  };
  const templateModeStyles = { ...desktopCanvasStyles,
    borderRadius: '2px 2px 0 0',
    border: '1px solid #ddd',
    borderBottom: 0
  };
  const resizedCanvasStyles = (0,external_wp_blockEditor_namespaceObject.__experimentalUseResizeCanvas)(deviceType, isTemplateMode);
  const globalLayoutSettings = (0,external_wp_blockEditor_namespaceObject.useSetting)('layout');
  const previewMode = 'is-' + deviceType.toLowerCase() + '-preview';
  let animatedStyles = isTemplateMode ? templateModeStyles : desktopCanvasStyles;

  if (resizedCanvasStyles) {
    animatedStyles = resizedCanvasStyles;
  }

  let paddingBottom; // Add a constant padding for the typewritter effect. When typing at the
  // bottom, there needs to be room to scroll up.

  if (!hasMetaBoxes && !resizedCanvasStyles && !isTemplateMode) {
    paddingBottom = '40vh';
  }

  const ref = (0,external_wp_element_namespaceObject.useRef)();
  const contentRef = (0,external_wp_compose_namespaceObject.useMergeRefs)([ref, (0,external_wp_blockEditor_namespaceObject.__unstableUseClipboardHandler)(), (0,external_wp_blockEditor_namespaceObject.__unstableUseTypewriter)(), (0,external_wp_blockEditor_namespaceObject.__unstableUseTypingObserver)(), (0,external_wp_blockEditor_namespaceObject.__unstableUseBlockSelectionClearer)()]);
  const blockSelectionClearerRef = (0,external_wp_blockEditor_namespaceObject.__unstableUseBlockSelectionClearer)(); // fallbackLayout is used if there is no Post Content,
  // and for Post Title.

  const fallbackLayout = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (isTemplateMode) {
      return {
        type: 'default'
      };
    }

    if (themeSupportsLayout) {
      // We need to ensure support for wide and full alignments,
      // so we add the constrained type.
      return { ...globalLayoutSettings,
        type: 'constrained'
      };
    } // Set default layout for classic themes so all alignments are supported.


    return {
      type: 'default'
    };
  }, [isTemplateMode, themeSupportsLayout, globalLayoutSettings]);
  const postContentBlock = (0,external_wp_element_namespaceObject.useMemo)(() => {
    // When in template editing mode, we can access the blocks directly.
    if (editedPostTemplate !== null && editedPostTemplate !== void 0 && editedPostTemplate.blocks) {
      return findPostContent(editedPostTemplate === null || editedPostTemplate === void 0 ? void 0 : editedPostTemplate.blocks);
    } // If there are no blocks, we have to parse the content string.
    // Best double-check it's a string otherwise the parse function gets unhappy.


    const parseableContent = typeof (editedPostTemplate === null || editedPostTemplate === void 0 ? void 0 : editedPostTemplate.content) === 'string' ? editedPostTemplate === null || editedPostTemplate === void 0 ? void 0 : editedPostTemplate.content : '';
    return findPostContent((0,external_wp_blocks_namespaceObject.parse)(parseableContent)) || {};
  }, [editedPostTemplate === null || editedPostTemplate === void 0 ? void 0 : editedPostTemplate.content, editedPostTemplate === null || editedPostTemplate === void 0 ? void 0 : editedPostTemplate.blocks]);
  const postContentLayoutClasses = (0,external_wp_blockEditor_namespaceObject.__experimentaluseLayoutClasses)(postContentBlock);
  const blockListLayoutClass = classnames_default()({
    'is-layout-flow': !themeSupportsLayout
  }, themeSupportsLayout && postContentLayoutClasses);
  const postContentLayoutStyles = (0,external_wp_blockEditor_namespaceObject.__experimentaluseLayoutStyles)(postContentBlock, '.block-editor-block-list__layout.is-root-container');
  const layout = (postContentBlock === null || postContentBlock === void 0 ? void 0 : (_postContentBlock$att = postContentBlock.attributes) === null || _postContentBlock$att === void 0 ? void 0 : _postContentBlock$att.layout) || {}; // Update type for blocks using legacy layouts.

  const postContentLayout = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return layout && ((layout === null || layout === void 0 ? void 0 : layout.type) === 'constrained' || layout !== null && layout !== void 0 && layout.inherit || layout !== null && layout !== void 0 && layout.contentSize || layout !== null && layout !== void 0 && layout.wideSize) ? { ...globalLayoutSettings,
      ...layout,
      type: 'constrained'
    } : { ...globalLayoutSettings,
      ...layout,
      type: 'default'
    };
  }, [layout === null || layout === void 0 ? void 0 : layout.type, layout === null || layout === void 0 ? void 0 : layout.inherit, layout === null || layout === void 0 ? void 0 : layout.contentSize, layout === null || layout === void 0 ? void 0 : layout.wideSize, globalLayoutSettings]); // If there is a Post Content block we use its layout for the block list;
  // if not, this must be a classic theme, in which case we use the fallback layout.

  const blockListLayout = postContentBlock ? postContentLayout : fallbackLayout;
  const titleRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    var _titleRef$current;

    if (isWelcomeGuideVisible || !isCleanNewPost()) {
      return;
    }

    titleRef === null || titleRef === void 0 ? void 0 : (_titleRef$current = titleRef.current) === null || _titleRef$current === void 0 ? void 0 : _titleRef$current.focus();
  }, [isWelcomeGuideVisible, isCleanNewPost]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockTools, {
    __unstableContentRef: ref,
    className: classnames_default()('edit-post-visual-editor', {
      'is-template-mode': isTemplateMode
    })
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.VisualEditorGlobalKeyboardShortcuts, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    className: "edit-post-visual-editor__content-area",
    animate: {
      padding: isTemplateMode ? '48px 48px 0' : '0'
    },
    ref: blockSelectionClearerRef
  }, isTemplateMode && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-post-visual-editor__exit-template-mode",
    icon: arrow_left,
    onClick: () => {
      clearSelectedBlock();
      setIsEditingTemplate(false);
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Back')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    animate: animatedStyles,
    initial: desktopCanvasStyles,
    className: previewMode
  }, (0,external_wp_element_namespaceObject.createElement)(MaybeIframe, {
    shouldIframe: isTemplateMode || deviceType === 'Tablet' || deviceType === 'Mobile',
    contentRef: contentRef,
    styles: styles,
    assets: assets,
    style: {
      paddingBottom
    }
  }, themeSupportsLayout && !themeHasDisabledLayoutStyles && !isTemplateMode && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalLayoutStyle, {
    selector: ".edit-post-visual-editor__post-title-wrapper, .block-editor-block-list__layout.is-root-container",
    layout: fallbackLayout,
    layoutDefinitions: globalLayoutSettings === null || globalLayoutSettings === void 0 ? void 0 : globalLayoutSettings.definitions
  }), postContentLayoutStyles && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalLayoutStyle, {
    layout: postContentLayout,
    css: postContentLayoutStyles,
    layoutDefinitions: globalLayoutSettings === null || globalLayoutSettings === void 0 ? void 0 : globalLayoutSettings.definitions
  })), !isTemplateMode && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-visual-editor__post-title-wrapper",
    contentEditable: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTitle, {
    ref: titleRef
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalRecursionProvider, {
    blockName: wrapperBlockName,
    uniqueId: wrapperUniqueId
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockList, {
    className: isTemplateMode ? 'wp-site-blocks' : `${blockListLayoutClass} wp-block-post-content` // Ensure root level blocks receive default/flow blockGap styling rules.
    ,
    __experimentalLayout: blockListLayout
  }))))), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableBlockSettingsMenuFirstItem, null, _ref3 => {
    let {
      onClose
    } = _ref3;
    return (0,external_wp_element_namespaceObject.createElement)(block_inspector_button, {
      onClick: onClose
    });
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function KeyboardShortcuts() {
  const {
    getBlockSelectionStart
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);
  const {
    getEditorMode,
    isEditorSidebarOpened,
    isListViewOpened
  } = (0,external_wp_data_namespaceObject.useSelect)(store_store);
  const isModeToggleDisabled = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      richEditingEnabled,
      codeEditingEnabled
    } = select(external_wp_editor_namespaceObject.store).getEditorSettings();
    return !richEditingEnabled || !codeEditingEnabled;
  }, []);
  const {
    switchEditorMode,
    openGeneralSidebar,
    closeGeneralSidebar,
    toggleFeature,
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    registerShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: 'core/edit-post/toggle-mode',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Switch between visual editor and code editor.'),
      keyCombination: {
        modifier: 'secondary',
        character: 'm'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-fullscreen',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Toggle fullscreen mode.'),
      keyCombination: {
        modifier: 'secondary',
        character: 'f'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-list-view',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Open the block list view.'),
      keyCombination: {
        modifier: 'access',
        character: 'o'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-sidebar',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Show or hide the settings sidebar.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: ','
      }
    });
    registerShortcut({
      name: 'core/edit-post/next-region',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Navigate to the next part of the editor.'),
      keyCombination: {
        modifier: 'ctrl',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'n'
      }]
    });
    registerShortcut({
      name: 'core/edit-post/previous-region',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Navigate to the previous part of the editor.'),
      keyCombination: {
        modifier: 'ctrlShift',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'p'
      }]
    });
    registerShortcut({
      name: 'core/edit-post/keyboard-shortcuts',
      category: 'main',
      description: (0,external_wp_i18n_namespaceObject.__)('Display these keyboard shortcuts.'),
      keyCombination: {
        modifier: 'access',
        character: 'h'
      }
    });
  }, []);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-mode', () => {
    switchEditorMode(getEditorMode() === 'visual' ? 'text' : 'visual');
  }, {
    isDisabled: isModeToggleDisabled
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-fullscreen', () => {
    toggleFeature('fullscreenMode');
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-sidebar', event => {
    // This shortcut has no known clashes, but use preventDefault to prevent any
    // obscure shortcuts from triggering.
    event.preventDefault();

    if (isEditorSidebarOpened()) {
      closeGeneralSidebar();
    } else {
      const sidebarToOpen = getBlockSelectionStart() ? 'edit-post/block' : 'edit-post/document';
      openGeneralSidebar(sidebarToOpen);
    }
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-list-view', () => setIsListViewOpened(!isListViewOpened()));
  return null;
}

/* harmony default export */ var keyboard_shortcuts = (KeyboardShortcuts);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * WordPress dependencies
 */

const textFormattingShortcuts = [{
  keyCombination: {
    modifier: 'primary',
    character: 'b'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text bold.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'i'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text italic.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'k'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Convert the selected text into a link.')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'k'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Remove a link.')
}, {
  keyCombination: {
    character: '[['
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Insert a link to a post or page')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'u'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Underline the selected text.')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'd'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Strikethrough the selected text.')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'x'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text inline code.')
}];

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/shortcut.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function KeyCombination(_ref) {
  let {
    keyCombination,
    forceAriaLabel
  } = _ref;
  const shortcut = keyCombination.modifier ? external_wp_keycodes_namespaceObject.displayShortcutList[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_wp_keycodes_namespaceObject.shortcutAriaLabel[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  return (0,external_wp_element_namespaceObject.createElement)("kbd", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel
  }, (0,external_lodash_namespaceObject.castArray)(shortcut).map((character, index) => {
    if (character === '+') {
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, {
        key: index
      }, character);
    }

    return (0,external_wp_element_namespaceObject.createElement)("kbd", {
      key: index,
      className: "edit-post-keyboard-shortcut-help-modal__shortcut-key"
    }, character);
  }));
}

function Shortcut(_ref2) {
  let {
    description,
    keyCombination,
    aliases = [],
    ariaLabel
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-description"
  }, description), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-term"
  }, (0,external_wp_element_namespaceObject.createElement)(KeyCombination, {
    keyCombination: keyCombination,
    forceAriaLabel: ariaLabel
  }), aliases.map((alias, index) => (0,external_wp_element_namespaceObject.createElement)(KeyCombination, {
    keyCombination: alias,
    forceAriaLabel: ariaLabel,
    key: index
  }))));
}

/* harmony default export */ var keyboard_shortcut_help_modal_shortcut = (Shortcut);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



function DynamicShortcut(_ref) {
  let {
    name
  } = _ref;
  const {
    keyCombination,
    description,
    aliases
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
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
  }, [name]);

  if (!keyCombination) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcut_help_modal_shortcut, {
    keyCombination: keyCombination,
    description: description,
    aliases: aliases
  });
}

/* harmony default export */ var dynamic_shortcut = (DynamicShortcut);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */





const MODAL_NAME = 'edit-post/keyboard-shortcut-help';

const ShortcutList = _ref => {
  let {
    shortcuts
  } = _ref;
  return (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */

    /* eslint-disable jsx-a11y/no-redundant-roles */
    (0,external_wp_element_namespaceObject.createElement)("ul", {
      className: "edit-post-keyboard-shortcut-help-modal__shortcut-list",
      role: "list"
    }, shortcuts.map((shortcut, index) => (0,external_wp_element_namespaceObject.createElement)("li", {
      className: "edit-post-keyboard-shortcut-help-modal__shortcut",
      key: index
    }, typeof shortcut === 'string' ? (0,external_wp_element_namespaceObject.createElement)(dynamic_shortcut, {
      name: shortcut
    }) : (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcut_help_modal_shortcut, shortcut))))
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
};

const ShortcutSection = _ref2 => {
  let {
    title,
    shortcuts,
    className
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)("section", {
    className: classnames_default()('edit-post-keyboard-shortcut-help-modal__section', className)
  }, !!title && (0,external_wp_element_namespaceObject.createElement)("h2", {
    className: "edit-post-keyboard-shortcut-help-modal__section-title"
  }, title), (0,external_wp_element_namespaceObject.createElement)(ShortcutList, {
    shortcuts: shortcuts
  }));
};

const ShortcutCategorySection = _ref3 => {
  let {
    title,
    categoryName,
    additionalShortcuts = []
  } = _ref3;
  const categoryShortcuts = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return select(external_wp_keyboardShortcuts_namespaceObject.store).getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    title: title,
    shortcuts: categoryShortcuts.concat(additionalShortcuts)
  });
};

function KeyboardShortcutHelpModal(_ref4) {
  let {
    isModalActive,
    toggleModal
  } = _ref4;
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/keyboard-shortcuts', toggleModal);

  if (!isModalActive) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    className: "edit-post-keyboard-shortcut-help-modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts'),
    closeLabel: (0,external_wp_i18n_namespaceObject.__)('Close'),
    onRequestClose: toggleModal
  }, (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    className: "edit-post-keyboard-shortcut-help-modal__main-shortcuts",
    shortcuts: ['core/edit-post/keyboard-shortcuts']
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Global shortcuts'),
    categoryName: "global"
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Selection shortcuts'),
    categoryName: "selection"
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Block shortcuts'),
    categoryName: "block",
    additionalShortcuts: [{
      keyCombination: {
        character: '/'
      },
      description: (0,external_wp_i18n_namespaceObject.__)('Change the block type after adding a new paragraph.'),

      /* translators: The forward-slash character. e.g. '/'. */
      ariaLabel: (0,external_wp_i18n_namespaceObject.__)('Forward-slash')
    }]
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Text formatting'),
    shortcuts: textFormattingShortcuts
  }));
}
/* harmony default export */ var keyboard_shortcut_help_modal = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => ({
  isModalActive: select(store_store).isModalActive(MODAL_NAME)
})), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, _ref5) => {
  let {
    isModalActive
  } = _ref5;
  const {
    openModal,
    closeModal
  } = dispatch(store_store);
  return {
    toggleModal: () => isModalActive ? closeModal() : openModal(MODAL_NAME)
  };
})])(KeyboardShortcutHelpModal));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-custom-fields.js


/**
 * WordPress dependencies
 */






function CustomFieldsConfirmation(_ref) {
  let {
    willEnable
  } = _ref;
  const [isReloading, setIsReloading] = (0,external_wp_element_namespaceObject.useState)(false);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("p", {
    className: "edit-post-preferences-modal__custom-fields-confirmation-message"
  }, (0,external_wp_i18n_namespaceObject.__)('A page reload is required for this change. Make sure your content is saved before reloading.')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-post-preferences-modal__custom-fields-confirmation-button",
    variant: "secondary",
    isBusy: isReloading,
    disabled: isReloading,
    onClick: () => {
      setIsReloading(true);
      document.getElementById('toggle-custom-fields-form').submit();
    }
  }, willEnable ? (0,external_wp_i18n_namespaceObject.__)('Enable & Reload') : (0,external_wp_i18n_namespaceObject.__)('Disable & Reload')));
}
function EnableCustomFieldsOption(_ref2) {
  let {
    label,
    areCustomFieldsEnabled
  } = _ref2;
  const [isChecked, setIsChecked] = (0,external_wp_element_namespaceObject.useState)(areCustomFieldsEnabled);
  return (0,external_wp_element_namespaceObject.createElement)(preferences_modal_base_option, {
    label: label,
    isChecked: isChecked,
    onChange: setIsChecked
  }, isChecked !== areCustomFieldsEnabled && (0,external_wp_element_namespaceObject.createElement)(CustomFieldsConfirmation, {
    willEnable: isChecked
  }));
}
/* harmony default export */ var enable_custom_fields = ((0,external_wp_data_namespaceObject.withSelect)(select => ({
  areCustomFieldsEnabled: !!select(external_wp_editor_namespaceObject.store).getEditorSettings().enableCustomFields
}))(EnableCustomFieldsOption));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-panel.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/* harmony default export */ var enable_panel = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, _ref) => {
  let {
    panelName
  } = _ref;
  const {
    isEditorPanelEnabled,
    isEditorPanelRemoved
  } = select(store_store);
  return {
    isRemoved: isEditorPanelRemoved(panelName),
    isChecked: isEditorPanelEnabled(panelName)
  };
}), (0,external_wp_compose_namespaceObject.ifCondition)(_ref2 => {
  let {
    isRemoved
  } = _ref2;
  return !isRemoved;
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, _ref3) => {
  let {
    panelName
  } = _ref3;
  return {
    onChange: () => dispatch(store_store).toggleEditorPanelEnabled(panelName)
  };
}))(preferences_modal_base_option));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-plugin-document-setting-panel.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


const {
  Fill,
  Slot: enable_plugin_document_setting_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('EnablePluginDocumentSettingPanelOption');

const EnablePluginDocumentSettingPanelOption = _ref => {
  let {
    label,
    panelName
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(Fill, null, (0,external_wp_element_namespaceObject.createElement)(enable_panel, {
    label: label,
    panelName: panelName
  }));
};

EnablePluginDocumentSettingPanelOption.Slot = enable_plugin_document_setting_panel_Slot;
/* harmony default export */ var enable_plugin_document_setting_panel = (EnablePluginDocumentSettingPanelOption);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-publish-sidebar.js
/**
 * WordPress dependencies
 */





/* harmony default export */ var enable_publish_sidebar = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)(select => ({
  isChecked: select(external_wp_editor_namespaceObject.store).isPublishSidebarEnabled()
})), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    enablePublishSidebar,
    disablePublishSidebar
  } = dispatch(external_wp_editor_namespaceObject.store);
  return {
    onChange: isEnabled => isEnabled ? enablePublishSidebar() : disablePublishSidebar()
  };
}), // In < medium viewports we override this option and always show the publish sidebar.
// See the edit-post's header component for the specific logic.
(0,external_wp_viewport_namespaceObject.ifViewportMatches)('medium'))(preferences_modal_base_option));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-feature.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/* harmony default export */ var enable_feature = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, _ref) => {
  let {
    featureName
  } = _ref;
  const {
    isFeatureActive
  } = select(store_store);
  return {
    isChecked: isFeatureActive(featureName)
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, _ref2) => {
  let {
    featureName
  } = _ref2;
  return {
    onChange: () => dispatch(store_store).toggleFeature(featureName)
  };
}))(preferences_modal_base_option));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/index.js






;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/meta-boxes-section.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function MetaBoxesSection(_ref) {
  let {
    areCustomFieldsRegistered,
    metaBoxes,
    ...sectionProps
  } = _ref;
  // The 'Custom Fields' meta box is a special case that we handle separately.
  const thirdPartyMetaBoxes = (0,external_lodash_namespaceObject.filter)(metaBoxes, _ref2 => {
    let {
      id
    } = _ref2;
    return id !== 'postcustom';
  });

  if (!areCustomFieldsRegistered && thirdPartyMetaBoxes.length === 0) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(preferences_modal_section, sectionProps, areCustomFieldsRegistered && (0,external_wp_element_namespaceObject.createElement)(enable_custom_fields, {
    label: (0,external_wp_i18n_namespaceObject.__)('Custom fields')
  }), (0,external_lodash_namespaceObject.map)(thirdPartyMetaBoxes, _ref3 => {
    let {
      id,
      title
    } = _ref3;
    return (0,external_wp_element_namespaceObject.createElement)(enable_panel, {
      key: id,
      label: title,
      panelName: `meta-box-${id}`
    });
  }));
}
/* harmony default export */ var meta_boxes_section = ((0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getEditorSettings
  } = select(external_wp_editor_namespaceObject.store);
  const {
    getAllMetaBoxes
  } = select(store_store);
  return {
    // This setting should not live in the block editor's store.
    areCustomFieldsRegistered: getEditorSettings().enableCustomFields !== undefined,
    metaBoxes: getAllMetaBoxes()
  };
})(MetaBoxesSection));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-manager/checklist.js


/**
 * WordPress dependencies
 */



function BlockTypesChecklist(_ref) {
  let {
    blockTypes,
    value,
    onItemChange
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)("ul", {
    className: "edit-post-block-manager__checklist"
  }, blockTypes.map(blockType => (0,external_wp_element_namespaceObject.createElement)("li", {
    key: blockType.name,
    className: "edit-post-block-manager__checklist-item"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    label: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, blockType.title, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockIcon, {
      icon: blockType.icon
    })),
    checked: value.includes(blockType.name),
    onChange: function () {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      return onItemChange(blockType.name, ...args);
    }
  }))));
}

/* harmony default export */ var checklist = (BlockTypesChecklist);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-manager/category.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




function BlockManagerCategory(_ref) {
  let {
    title,
    blockTypes
  } = _ref;
  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(BlockManagerCategory);
  const {
    defaultAllowedBlockTypes,
    hiddenBlockTypes
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);
    const {
      getHiddenBlockTypes
    } = select(store_store);
    return {
      defaultAllowedBlockTypes: getEditorSettings().defaultAllowedBlockTypes,
      hiddenBlockTypes: getHiddenBlockTypes()
    };
  }, []);
  const filteredBlockTypes = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (defaultAllowedBlockTypes === true) {
      return blockTypes;
    }

    return blockTypes.filter(_ref2 => {
      let {
        name
      } = _ref2;
      return (0,external_lodash_namespaceObject.includes)(defaultAllowedBlockTypes || [], name);
    });
  }, [defaultAllowedBlockTypes, blockTypes]);
  const {
    showBlockTypes,
    hideBlockTypes
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const toggleVisible = (0,external_wp_element_namespaceObject.useCallback)((blockName, nextIsChecked) => {
    if (nextIsChecked) {
      showBlockTypes(blockName);
    } else {
      hideBlockTypes(blockName);
    }
  }, []);
  const toggleAllVisible = (0,external_wp_element_namespaceObject.useCallback)(nextIsChecked => {
    const blockNames = (0,external_lodash_namespaceObject.map)(blockTypes, 'name');

    if (nextIsChecked) {
      showBlockTypes(blockNames);
    } else {
      hideBlockTypes(blockNames);
    }
  }, [blockTypes]);

  if (!filteredBlockTypes.length) {
    return null;
  }

  const checkedBlockNames = (0,external_lodash_namespaceObject.without)((0,external_lodash_namespaceObject.map)(filteredBlockTypes, 'name'), ...hiddenBlockTypes);
  const titleId = 'edit-post-block-manager__category-title-' + instanceId;
  const isAllChecked = checkedBlockNames.length === filteredBlockTypes.length;
  let ariaChecked;

  if (isAllChecked) {
    ariaChecked = 'true';
  } else if (checkedBlockNames.length > 0) {
    ariaChecked = 'mixed';
  } else {
    ariaChecked = 'false';
  }

  return (0,external_wp_element_namespaceObject.createElement)("div", {
    role: "group",
    "aria-labelledby": titleId,
    className: "edit-post-block-manager__category"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    checked: isAllChecked,
    onChange: toggleAllVisible,
    className: "edit-post-block-manager__category-title",
    "aria-checked": ariaChecked,
    label: (0,external_wp_element_namespaceObject.createElement)("span", {
      id: titleId
    }, title)
  }), (0,external_wp_element_namespaceObject.createElement)(checklist, {
    blockTypes: filteredBlockTypes,
    value: checkedBlockNames,
    onItemChange: toggleVisible
  }));
}

/* harmony default export */ var block_manager_category = (BlockManagerCategory);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-manager/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */




function BlockManager(_ref) {
  let {
    blockTypes,
    categories,
    hasBlockSupport,
    isMatchingSearchTerm,
    numberOfHiddenBlocks
  } = _ref;
  const debouncedSpeak = (0,external_wp_compose_namespaceObject.useDebounce)(external_wp_a11y_namespaceObject.speak, 500);
  const [search, setSearch] = (0,external_wp_element_namespaceObject.useState)(''); // Filtering occurs here (as opposed to `withSelect`) to avoid
  // wasted renders by consequence of `Array#filter` producing
  // a new value reference on each call.

  blockTypes = blockTypes.filter(blockType => hasBlockSupport(blockType, 'inserter', true) && (!search || isMatchingSearchTerm(blockType, search)) && (!blockType.parent || (0,external_lodash_namespaceObject.includes)(blockType.parent, 'core/post-content'))); // Announce search results on change

  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!search) {
      return;
    }

    const count = blockTypes.length;
    const resultsFoundMessage = (0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %d: number of results. */
    (0,external_wp_i18n_namespaceObject._n)('%d result found.', '%d results found.', count), count);
    debouncedSpeak(resultsFoundMessage);
  }, [blockTypes.length, search, debouncedSpeak]);
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-block-manager__content"
  }, !!numberOfHiddenBlocks && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-block-manager__disabled-blocks-count"
  }, (0,external_wp_i18n_namespaceObject.sprintf)(
  /* translators: %d: number of blocks. */
  (0,external_wp_i18n_namespaceObject._n)('%d block is hidden.', '%d blocks are hidden.', numberOfHiddenBlocks), numberOfHiddenBlocks)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SearchControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Search for a block'),
    placeholder: (0,external_wp_i18n_namespaceObject.__)('Search for a block'),
    value: search,
    onChange: nextSearch => setSearch(nextSearch),
    className: "edit-post-block-manager__search"
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    tabIndex: "0",
    role: "region",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Available block types'),
    className: "edit-post-block-manager__results"
  }, blockTypes.length === 0 && (0,external_wp_element_namespaceObject.createElement)("p", {
    className: "edit-post-block-manager__no-results"
  }, (0,external_wp_i18n_namespaceObject.__)('No blocks found.')), categories.map(category => (0,external_wp_element_namespaceObject.createElement)(block_manager_category, {
    key: category.slug,
    title: category.title,
    blockTypes: (0,external_lodash_namespaceObject.filter)(blockTypes, {
      category: category.slug
    })
  })), (0,external_wp_element_namespaceObject.createElement)(block_manager_category, {
    title: (0,external_wp_i18n_namespaceObject.__)('Uncategorized'),
    blockTypes: (0,external_lodash_namespaceObject.filter)(blockTypes, _ref2 => {
      let {
        category
      } = _ref2;
      return !category;
    })
  })));
}

/* harmony default export */ var block_manager = ((0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getBlockTypes,
    getCategories,
    hasBlockSupport,
    isMatchingSearchTerm
  } = select(external_wp_blocks_namespaceObject.store);
  const {
    getHiddenBlockTypes
  } = select(store_store); // Some hidden blocks become unregistered
  // by removing for instance the plugin that registered them, yet
  // they're still remain as hidden by the user's action.
  // We consider "hidden", blocks which were hidden and
  // are still registered.

  const blockTypes = getBlockTypes();
  const hiddenBlockTypes = getHiddenBlockTypes().filter(hiddenBlock => {
    return blockTypes.some(registeredBlock => registeredBlock.name === hiddenBlock);
  });
  const numberOfHiddenBlocks = Array.isArray(hiddenBlockTypes) && hiddenBlockTypes.length;
  return {
    blockTypes,
    categories: getCategories(),
    hasBlockSupport,
    isMatchingSearchTerm,
    numberOfHiddenBlocks
  };
})(BlockManager));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */





const preferences_modal_MODAL_NAME = 'edit-post/preferences';
function EditPostPreferencesModal() {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');
  const {
    closeModal
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const isModalActive = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).isModalActive(preferences_modal_MODAL_NAME), []);
  const showBlockBreadcrumbsOption = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);
    const {
      getEditorMode,
      isFeatureActive
    } = select(store_store);
    const mode = getEditorMode();
    const isRichEditingEnabled = getEditorSettings().richEditingEnabled;
    const hasReducedUI = isFeatureActive('reducedUI');
    return !hasReducedUI && isLargeViewport && isRichEditingEnabled && mode === 'visual';
  }, [isLargeViewport]);
  const sections = (0,external_wp_element_namespaceObject.useMemo)(() => [{
    name: 'general',
    tabLabel: (0,external_wp_i18n_namespaceObject.__)('General'),
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isLargeViewport && (0,external_wp_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_wp_i18n_namespaceObject.__)('Publishing'),
      description: (0,external_wp_i18n_namespaceObject.__)('Change options related to publishing.')
    }, (0,external_wp_element_namespaceObject.createElement)(enable_publish_sidebar, {
      help: (0,external_wp_i18n_namespaceObject.__)('Review settings, such as visibility and tags.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Include pre-publish checklist')
    })), (0,external_wp_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_wp_i18n_namespaceObject.__)('Appearance'),
      description: (0,external_wp_i18n_namespaceObject.__)('Customize options related to the block editor interface and editing flow.')
    }, (0,external_wp_element_namespaceObject.createElement)(enable_feature, {
      featureName: "focusMode",
      help: (0,external_wp_i18n_namespaceObject.__)('Highlights the current block and fades other content.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Spotlight mode')
    }), (0,external_wp_element_namespaceObject.createElement)(enable_feature, {
      featureName: "showIconLabels",
      label: (0,external_wp_i18n_namespaceObject.__)('Show button text labels'),
      help: (0,external_wp_i18n_namespaceObject.__)('Show text instead of icons on buttons.')
    }), (0,external_wp_element_namespaceObject.createElement)(enable_feature, {
      featureName: "showListViewByDefault",
      help: (0,external_wp_i18n_namespaceObject.__)('Opens the block list view sidebar by default.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Always open list view')
    }), (0,external_wp_element_namespaceObject.createElement)(enable_feature, {
      featureName: "reducedUI",
      help: (0,external_wp_i18n_namespaceObject.__)('Compacts options and outlines in the toolbar.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Reduce the interface')
    }), (0,external_wp_element_namespaceObject.createElement)(enable_feature, {
      featureName: "themeStyles",
      help: (0,external_wp_i18n_namespaceObject.__)('Make the editor look like your theme.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Use theme styles')
    }), showBlockBreadcrumbsOption && (0,external_wp_element_namespaceObject.createElement)(enable_feature, {
      featureName: "showBlockBreadcrumbs",
      help: (0,external_wp_i18n_namespaceObject.__)('Shows block breadcrumbs at the bottom of the editor.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Display block breadcrumbs')
    })))
  }, {
    name: 'blocks',
    tabLabel: (0,external_wp_i18n_namespaceObject.__)('Blocks'),
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_wp_i18n_namespaceObject.__)('Block interactions'),
      description: (0,external_wp_i18n_namespaceObject.__)('Customize how you interact with blocks in the block library and editing canvas.')
    }, (0,external_wp_element_namespaceObject.createElement)(enable_feature, {
      featureName: "mostUsedBlocks",
      help: (0,external_wp_i18n_namespaceObject.__)('Places the most frequent blocks in the block library.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Show most used blocks')
    }), (0,external_wp_element_namespaceObject.createElement)(enable_feature, {
      featureName: "keepCaretInsideBlock",
      help: (0,external_wp_i18n_namespaceObject.__)('Aids screen readers by stopping text caret from leaving blocks.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Contain text cursor inside block')
    })), (0,external_wp_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_wp_i18n_namespaceObject.__)('Visible blocks'),
      description: (0,external_wp_i18n_namespaceObject.__)("Disable blocks that you don't want to appear in the inserter. They can always be toggled back on later.")
    }, (0,external_wp_element_namespaceObject.createElement)(block_manager, null)))
  }, {
    name: 'panels',
    tabLabel: (0,external_wp_i18n_namespaceObject.__)('Panels'),
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_wp_i18n_namespaceObject.__)('Document settings'),
      description: (0,external_wp_i18n_namespaceObject.__)('Choose what displays in the panel.')
    }, (0,external_wp_element_namespaceObject.createElement)(enable_plugin_document_setting_panel.Slot, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTaxonomies, {
      taxonomyWrapper: (content, taxonomy) => (0,external_wp_element_namespaceObject.createElement)(enable_panel, {
        label: (0,external_lodash_namespaceObject.get)(taxonomy, ['labels', 'menu_name']),
        panelName: `taxonomy-panel-${taxonomy.slug}`
      })
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostFeaturedImageCheck, null, (0,external_wp_element_namespaceObject.createElement)(enable_panel, {
      label: (0,external_wp_i18n_namespaceObject.__)('Featured image'),
      panelName: "featured-image"
    })), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostExcerptCheck, null, (0,external_wp_element_namespaceObject.createElement)(enable_panel, {
      label: (0,external_wp_i18n_namespaceObject.__)('Excerpt'),
      panelName: "post-excerpt"
    })), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTypeSupportCheck, {
      supportKeys: ['comments', 'trackbacks']
    }, (0,external_wp_element_namespaceObject.createElement)(enable_panel, {
      label: (0,external_wp_i18n_namespaceObject.__)('Discussion'),
      panelName: "discussion-panel"
    })), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PageAttributesCheck, null, (0,external_wp_element_namespaceObject.createElement)(enable_panel, {
      label: (0,external_wp_i18n_namespaceObject.__)('Page attributes'),
      panelName: "page-attributes"
    }))), (0,external_wp_element_namespaceObject.createElement)(meta_boxes_section, {
      title: (0,external_wp_i18n_namespaceObject.__)('Additional'),
      description: (0,external_wp_i18n_namespaceObject.__)('Add extra areas to the editor.')
    }))
  }], [isLargeViewport, showBlockBreadcrumbsOption]);

  if (!isModalActive) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(PreferencesModal, {
    closeModal: closeModal
  }, (0,external_wp_element_namespaceObject.createElement)(PreferencesModalTabs, {
    sections: sections
  }));
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
      isSavingPost
    } = this.props;
    const {
      historyId
    } = this.state; // Posts are still dirty while saving so wait for saving to finish
    // to avoid the unsaved changes warning when trashing posts.

    if (postStatus === 'trash' && !isSavingPost) {
      this.setTrashURL(postId, postType);
      return;
    }

    if ((postId !== prevProps.postId || postId !== historyId) && postStatus !== 'auto-draft' && postId) {
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
/* harmony default export */ var browser_url = ((0,external_wp_data_namespaceObject.withSelect)(select => {
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/wordpress.js


/**
 * WordPress dependencies
 */

const wordpress = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z"
}));
/* harmony default export */ var library_wordpress = (wordpress);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/fullscreen-mode-close/index.js


/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */



function FullscreenModeClose(_ref) {
  let {
    showTooltip,
    icon,
    href
  } = _ref;
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
    } = select(store_store);
    const {
      getEntityRecord,
      getPostType,
      isResolving
    } = select(external_wp_coreData_namespaceObject.store);
    const siteData = getEntityRecord('root', '__unstableBase', undefined) || {};
    return {
      isActive: isFeatureActive('fullscreenMode'),
      isRequestingSiteIcon: isResolving('getEntityRecord', ['root', '__unstableBase', undefined]),
      postType: getPostType(getCurrentPostType()),
      siteIconUrl: siteData.site_icon_url
    };
  }, []);
  const disableMotion = (0,external_wp_compose_namespaceObject.useReducedMotion)();

  if (!isActive || !postType) {
    return null;
  }

  let buttonIcon = (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, {
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
    buttonIcon = (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.img, {
      variants: !disableMotion && effect,
      alt: (0,external_wp_i18n_namespaceObject.__)('Site Icon'),
      className: "edit-post-fullscreen-mode-close_site-icon",
      src: siteIconUrl
    });
  }

  if (isRequestingSiteIcon) {
    buttonIcon = null;
  } // Override default icon if custom icon is provided via props.


  if (icon) {
    buttonIcon = (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, {
      size: "36px",
      icon: icon
    });
  }

  const classes = classnames_default()({
    'edit-post-fullscreen-mode-close': true,
    'has-icon': siteIconUrl
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    whileHover: "expand"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: classes,
    href: href !== null && href !== void 0 ? href : (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
      post_type: postType.slug
    }),
    label: (0,external_lodash_namespaceObject.get)(postType, ['labels', 'view_items'], (0,external_wp_i18n_namespaceObject.__)('Back')),
    showTooltip: showTooltip
  }, buttonIcon));
}

/* harmony default export */ var fullscreen_mode_close = (FullscreenModeClose);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/list-view.js


/**
 * WordPress dependencies
 */

const listView = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13.8 5.2H3v1.5h10.8V5.2zm-3.6 12v1.5H21v-1.5H10.2zm7.2-6H6.6v1.5h10.8v-1.5z"
}));
/* harmony default export */ var list_view = (listView);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/plus.js


/**
 * WordPress dependencies
 */

const plus = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z"
}));
/* harmony default export */ var library_plus = (plus);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/header-toolbar/index.js


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */



const preventDefault = event => {
  event.preventDefault();
};

function HeaderToolbar() {
  const inserterButton = (0,external_wp_element_namespaceObject.useRef)();
  const {
    setIsInserterOpened,
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    isInserterEnabled,
    isInserterOpened,
    isTextModeEnabled,
    showIconLabels,
    isListViewOpen,
    listViewShortcut
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      hasInserterItems,
      getBlockRootClientId,
      getBlockSelectionEnd
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);
    const {
      getEditorMode,
      isFeatureActive,
      isListViewOpened
    } = select(store_store);
    const {
      getShortcutRepresentation
    } = select(external_wp_keyboardShortcuts_namespaceObject.store);
    return {
      // This setting (richEditingEnabled) should not live in the block editor's setting.
      isInserterEnabled: getEditorMode() === 'visual' && getEditorSettings().richEditingEnabled && hasInserterItems(getBlockRootClientId(getBlockSelectionEnd())),
      isInserterOpened: select(store_store).isInserterOpened(),
      isTextModeEnabled: getEditorMode() === 'text',
      showIconLabels: isFeatureActive('showIconLabels'),
      isListViewOpen: isListViewOpened(),
      listViewShortcut: getShortcutRepresentation('core/edit-post/toggle-list-view')
    };
  }, []);
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');
  const isWideViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('wide');
  /* translators: accessibility text for the editor toolbar */

  const toolbarAriaLabel = (0,external_wp_i18n_namespaceObject.__)('Document tools');

  const toggleListView = (0,external_wp_element_namespaceObject.useCallback)(() => setIsListViewOpened(!isListViewOpen), [setIsListViewOpened, isListViewOpen]);
  const overflowItems = (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_editor_namespaceObject.TableOfContents,
    hasOutlineItemsDisabled: isTextModeEnabled,
    repositionDropdown: showIconLabels && !isWideViewport,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_components_namespaceObject.Button,
    className: "edit-post-header-toolbar__list-view-toggle",
    icon: list_view,
    disabled: isTextModeEnabled,
    isPressed: isListViewOpen
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: (0,external_wp_i18n_namespaceObject.__)('List View'),
    onClick: toggleListView,
    shortcut: listViewShortcut,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }));
  const openInserter = (0,external_wp_element_namespaceObject.useCallback)(() => {
    if (isInserterOpened) {
      // Focusing the inserter button closes the inserter popover.
      inserterButton.current.focus();
    } else {
      setIsInserterOpened(true);
    }
  }, [isInserterOpened, setIsInserterOpened]);
  /* translators: button label text should, if possible, be under 16 characters. */

  const longLabel = (0,external_wp_i18n_namespaceObject._x)('Toggle block inserter', 'Generic label for block inserter button');

  const shortLabel = !isInserterOpened ? (0,external_wp_i18n_namespaceObject.__)('Add') : (0,external_wp_i18n_namespaceObject.__)('Close');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.NavigableToolbar, {
    className: "edit-post-header-toolbar",
    "aria-label": toolbarAriaLabel
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-header-toolbar__left"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    ref: inserterButton,
    as: external_wp_components_namespaceObject.Button,
    className: "edit-post-header-toolbar__inserter-toggle",
    variant: "primary",
    isPressed: isInserterOpened,
    onMouseDown: preventDefault,
    onClick: openInserter,
    disabled: !isInserterEnabled,
    icon: library_plus,
    label: showIconLabels ? shortLabel : longLabel,
    showTooltip: !showIconLabels
  }), (isWideViewport || !showIconLabels) && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isLargeViewport && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_blockEditor_namespaceObject.ToolSelector,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined,
    disabled: isTextModeEnabled
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_editor_namespaceObject.EditorHistoryUndo,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_editor_namespaceObject.EditorHistoryRedo,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), overflowItems)));
}

/* harmony default export */ var header_toolbar = (HeaderToolbar);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/mode-switcher/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Set of available mode options.
 *
 * @type {Array}
 */

const MODES = [{
  value: 'visual',
  label: (0,external_wp_i18n_namespaceObject.__)('Visual editor')
}, {
  value: 'text',
  label: (0,external_wp_i18n_namespaceObject.__)('Code editor')
}];

function ModeSwitcher() {
  const {
    shortcut,
    isRichEditingEnabled,
    isCodeEditingEnabled,
    isEditingTemplate,
    mode
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    shortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getShortcutRepresentation('core/edit-post/toggle-mode'),
    isRichEditingEnabled: select(external_wp_editor_namespaceObject.store).getEditorSettings().richEditingEnabled,
    isCodeEditingEnabled: select(external_wp_editor_namespaceObject.store).getEditorSettings().codeEditingEnabled,
    isEditingTemplate: select(store_store).isEditingTemplate(),
    mode: select(store_store).getEditorMode()
  }), []);
  const {
    switchEditorMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);

  if (isEditingTemplate) {
    return null;
  }

  if (!isRichEditingEnabled || !isCodeEditingEnabled) {
    return null;
  }

  const choices = MODES.map(choice => {
    if (choice.value !== mode) {
      return { ...choice,
        shortcut
      };
    }

    return choice;
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    label: (0,external_wp_i18n_namespaceObject.__)('Editor')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItemsChoice, {
    choices: choices,
    value: mode,
    onSelect: switchEditorMode
  }));
}

/* harmony default export */ var mode_switcher = (ModeSwitcher);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/preferences-menu-item/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function PreferencesMenuItem() {
  const {
    openModal
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      openModal('edit-post/preferences');
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Preferences'));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/writing-menu/index.js


/**
 * WordPress dependencies
 */






function WritingMenu() {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');

  if (!isLargeViewport) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    label: (0,external_wp_i18n_namespaceObject._x)('View', 'noun')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    name: "fixedToolbar",
    label: (0,external_wp_i18n_namespaceObject.__)('Top toolbar'),
    info: (0,external_wp_i18n_namespaceObject.__)('Access all block and document tools in a single place'),
    messageActivated: (0,external_wp_i18n_namespaceObject.__)('Top toolbar activated'),
    messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Top toolbar deactivated')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    name: "focusMode",
    label: (0,external_wp_i18n_namespaceObject.__)('Spotlight mode'),
    info: (0,external_wp_i18n_namespaceObject.__)('Focus on one block at a time'),
    messageActivated: (0,external_wp_i18n_namespaceObject.__)('Spotlight mode activated'),
    messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Spotlight mode deactivated')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    name: "fullscreenMode",
    label: (0,external_wp_i18n_namespaceObject.__)('Fullscreen mode'),
    info: (0,external_wp_i18n_namespaceObject.__)('Work without distraction'),
    messageActivated: (0,external_wp_i18n_namespaceObject.__)('Fullscreen mode activated'),
    messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Fullscreen mode deactivated'),
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.secondary('f')
  }));
}

/* harmony default export */ var writing_menu = (WritingMenu);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/more-menu/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */






const MoreMenu = _ref => {
  let {
    showIconLabels
  } = _ref;
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('large');
  return (0,external_wp_element_namespaceObject.createElement)(MoreMenuDropdown, {
    toggleProps: {
      showTooltip: !showIconLabels,
      ...(showIconLabels && {
        variant: 'tertiary'
      })
    }
  }, _ref2 => {
    let {
      onClose
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, showIconLabels && !isLargeViewport && (0,external_wp_element_namespaceObject.createElement)(pinned_items.Slot, {
      className: showIconLabels && 'show-icon-labels',
      scope: "core/edit-post"
    }), (0,external_wp_element_namespaceObject.createElement)(writing_menu, null), (0,external_wp_element_namespaceObject.createElement)(mode_switcher, null), (0,external_wp_element_namespaceObject.createElement)(action_item.Slot, {
      name: "core/edit-post/plugin-more-menu",
      label: (0,external_wp_i18n_namespaceObject.__)('Plugins'),
      as: external_wp_components_namespaceObject.MenuGroup,
      fillProps: {
        onClick: onClose
      }
    }), (0,external_wp_element_namespaceObject.createElement)(tools_more_menu_group.Slot, {
      fillProps: {
        onClose
      }
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_wp_element_namespaceObject.createElement)(PreferencesMenuItem, null)));
  });
};

/* harmony default export */ var more_menu = (MoreMenu);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/post-publish-button-or-toggle.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function PostPublishButtonOrToggle(_ref) {
  let {
    forceIsDirty,
    forceIsSaving,
    hasPublishAction,
    isBeingScheduled,
    isPending,
    isPublished,
    isPublishSidebarEnabled,
    isPublishSidebarOpened,
    isScheduled,
    togglePublishSidebar,
    setEntitiesSavedStatesCallback
  } = _ref;
  const IS_TOGGLE = 'toggle';
  const IS_BUTTON = 'button';
  const isSmallerThanMediumViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  let component;
  /**
   * Conditions to show a BUTTON (publish directly) or a TOGGLE (open publish sidebar):
   *
   * 1) We want to show a BUTTON when the post status is at the _final stage_
   * for a particular role (see https://wordpress.org/support/article/post-status/):
   *
   * - is published
   * - is scheduled to be published
   * - is pending and can't be published (but only for viewports >= medium).
   * 	 Originally, we considered showing a button for pending posts that couldn't be published
   * 	 (for example, for an author with the contributor role). Some languages can have
   * 	 long translations for "Submit for review", so given the lack of UI real estate available
   * 	 we decided to take into account the viewport in that case.
   *  	 See: https://github.com/WordPress/gutenberg/issues/10475
   *
   * 2) Then, in small viewports, we'll show a TOGGLE.
   *
   * 3) Finally, we'll use the publish sidebar status to decide:
   *
   * - if it is enabled, we show a TOGGLE
   * - if it is disabled, we show a BUTTON
   */

  if (isPublished || isScheduled && isBeingScheduled || isPending && !hasPublishAction && !isSmallerThanMediumViewport) {
    component = IS_BUTTON;
  } else if (isSmallerThanMediumViewport) {
    component = IS_TOGGLE;
  } else if (isPublishSidebarEnabled) {
    component = IS_TOGGLE;
  } else {
    component = IS_BUTTON;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPublishButton, {
    forceIsDirty: forceIsDirty,
    forceIsSaving: forceIsSaving,
    isOpen: isPublishSidebarOpened,
    isToggle: component === IS_TOGGLE,
    onToggle: togglePublishSidebar,
    setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
  });
}
/* harmony default export */ var post_publish_button_or_toggle = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)(select => ({
  hasPublishAction: (0,external_lodash_namespaceObject.get)(select(external_wp_editor_namespaceObject.store).getCurrentPost(), ['_links', 'wp:action-publish'], false),
  isBeingScheduled: select(external_wp_editor_namespaceObject.store).isEditedPostBeingScheduled(),
  isPending: select(external_wp_editor_namespaceObject.store).isCurrentPostPending(),
  isPublished: select(external_wp_editor_namespaceObject.store).isCurrentPostPublished(),
  isPublishSidebarEnabled: select(external_wp_editor_namespaceObject.store).isPublishSidebarEnabled(),
  isPublishSidebarOpened: select(store_store).isPublishSidebarOpened(),
  isScheduled: select(external_wp_editor_namespaceObject.store).isCurrentPostScheduled()
})), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    togglePublishSidebar
  } = dispatch(store_store);
  return {
    togglePublishSidebar
  };
}))(PostPublishButtonOrToggle));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/device-preview/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


function DevicePreview() {
  const {
    hasActiveMetaboxes,
    isPostSaveable,
    isSaving,
    isViewable,
    deviceType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute
    } = select(external_wp_editor_namespaceObject.store);
    const {
      getPostType
    } = select(external_wp_coreData_namespaceObject.store);
    const postType = getPostType(getEditedPostAttribute('type'));
    return {
      hasActiveMetaboxes: select(store_store).hasMetaBoxes(),
      isSaving: select(store_store).isSavingMetaBoxes(),
      isPostSaveable: select(external_wp_editor_namespaceObject.store).isEditedPostSaveable(),
      isViewable: (0,external_lodash_namespaceObject.get)(postType, ['viewable'], false),
      deviceType: select(store_store).__experimentalGetPreviewDeviceType()
    };
  }, []);
  const {
    __experimentalSetPreviewDeviceType: setPreviewDeviceType
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalPreviewOptions, {
    isEnabled: isPostSaveable,
    className: "edit-post-post-preview-dropdown",
    deviceType: deviceType,
    setDeviceType: setPreviewDeviceType
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    viewLabel: (0,external_wp_i18n_namespaceObject.__)('Preview')
  }, isViewable && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-header-preview__grouping-external"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPreviewButton, {
    className: 'edit-post-header-preview__button-external',
    role: "menuitem",
    forceIsAutosaveable: hasActiveMetaboxes,
    forcePreviewLink: isSaving ? null : undefined,
    textContent: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_i18n_namespaceObject.__)('Preview in new tab'), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, {
      icon: library_external
    }))
  }))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/main-dashboard-button/index.js


/**
 * WordPress dependencies
 */

const slotName = '__experimentalMainDashboardButton';
const {
  Fill: main_dashboard_button_Fill,
  Slot: MainDashboardButtonSlot
} = (0,external_wp_components_namespaceObject.createSlotFill)(slotName);
const MainDashboardButton = main_dashboard_button_Fill;

const main_dashboard_button_Slot = _ref => {
  let {
    children
  } = _ref;
  const slot = (0,external_wp_components_namespaceObject.__experimentalUseSlot)(slotName);
  const hasFills = Boolean(slot.fills && slot.fills.length);

  if (!hasFills) {
    return children;
  }

  return (0,external_wp_element_namespaceObject.createElement)(MainDashboardButtonSlot, {
    bubblesVirtually: true
  });
};

MainDashboardButton.Slot = main_dashboard_button_Slot;
/* harmony default export */ var main_dashboard_button = (MainDashboardButton);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-down.js


/**
 * WordPress dependencies
 */

const chevronDown = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"
}));
/* harmony default export */ var chevron_down = (chevronDown);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/template-title/delete-template.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


function DeleteTemplate() {
  const {
    clearSelectedBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    setIsEditingTemplate
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    getEditorSettings
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_editor_namespaceObject.store);
  const {
    updateEditorSettings,
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_editor_namespaceObject.store);
  const {
    deleteEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    template
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isEditingTemplate,
      getEditedPostTemplate
    } = select(store_store);

    const _isEditing = isEditingTemplate();

    return {
      template: _isEditing ? getEditedPostTemplate() : null
    };
  }, []);
  const [showConfirmDialog, setShowConfirmDialog] = (0,external_wp_element_namespaceObject.useState)(false);

  if (!template || !template.wp_id) {
    return null;
  }

  let templateTitle = template.slug;

  if (template !== null && template !== void 0 && template.title) {
    templateTitle = template.title;
  }

  const isRevertable = template === null || template === void 0 ? void 0 : template.has_theme_file;

  const onDelete = () => {
    clearSelectedBlock();
    setIsEditingTemplate(false);
    setShowConfirmDialog(false);
    editPost({
      template: ''
    });
    const settings = getEditorSettings();
    const newAvailableTemplates = (0,external_lodash_namespaceObject.pickBy)(settings.availableTemplates, (_title, id) => {
      return id !== template.slug;
    });
    updateEditorSettings({ ...settings,
      availableTemplates: newAvailableTemplates
    });
    deleteEntityRecord('postType', 'wp_template', template.id, {
      throwOnError: true
    });
  };

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    className: "edit-post-template-top-area__second-menu-group"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    className: "edit-post-template-top-area__delete-template-button",
    isDestructive: !isRevertable,
    onClick: () => {
      setShowConfirmDialog(true);
    },
    info: isRevertable ? (0,external_wp_i18n_namespaceObject.__)('Use the template as supplied by the theme.') : undefined
  }, isRevertable ? (0,external_wp_i18n_namespaceObject.__)('Clear customizations') : (0,external_wp_i18n_namespaceObject.__)('Delete template')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalConfirmDialog, {
    isOpen: showConfirmDialog,
    onConfirm: onDelete,
    onCancel: () => {
      setShowConfirmDialog(false);
    }
  }, (0,external_wp_i18n_namespaceObject.sprintf)(
  /* translators: %s: template name */
  (0,external_wp_i18n_namespaceObject.__)('Are you sure you want to delete the %s template? It may be used by other pages or posts.'), templateTitle))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/template-title/edit-template-title.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function EditTemplateTitle() {
  const [forceEmpty, setForceEmpty] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    template
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostTemplate
    } = select(store_store);
    return {
      template: getEditedPostTemplate()
    };
  }, []);
  const {
    editEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    getEditorSettings
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_editor_namespaceObject.store);
  const {
    updateEditorSettings
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_editor_namespaceObject.store); // Only user-created and non-default templates can change the name.

  if (!template.is_custom || template.has_theme_file) {
    return null;
  }

  let templateTitle = (0,external_wp_i18n_namespaceObject.__)('Default');

  if (template !== null && template !== void 0 && template.title) {
    templateTitle = template.title;
  } else if (!!template) {
    templateTitle = template.slug;
  }

  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-template-details__group"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Title'),
    value: forceEmpty ? '' : templateTitle,
    help: (0,external_wp_i18n_namespaceObject.__)('Give the template a title that indicates its purpose, e.g. "Full Width".'),
    onChange: newTitle => {
      // Allow having the field temporarily empty while typing.
      if (!newTitle && !forceEmpty) {
        setForceEmpty(true);
        return;
      }

      setForceEmpty(false);
      const settings = getEditorSettings();
      const newAvailableTemplates = (0,external_lodash_namespaceObject.mapValues)(settings.availableTemplates, (existingTitle, id) => {
        if (id !== template.slug) {
          return existingTitle;
        }

        return newTitle;
      });
      updateEditorSettings({ ...settings,
        availableTemplates: newAvailableTemplates
      });
      editEntityRecord('postType', 'wp_template', template.id, {
        title: newTitle
      });
    },
    onBlur: () => setForceEmpty(false)
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/template-title/template-description.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function TemplateDescription() {
  const {
    description,
    title
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostTemplate
    } = select(store_store);
    return {
      title: getEditedPostTemplate().title,
      description: getEditedPostTemplate().description
    };
  }, []);

  if (!description) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-template-details__group"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHeading, {
    level: 4,
    weight: 600
  }, title), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    className: "edit-post-template-details__description",
    size: "body",
    as: "p",
    style: {
      marginTop: '12px'
    }
  }, description));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/template-title/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */








function TemplateTitle() {
  const {
    template,
    isEditing,
    title
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isEditingTemplate,
      getEditedPostTemplate
    } = select(store_store);
    const {
      getEditedPostAttribute
    } = select(external_wp_editor_namespaceObject.store);

    const _isEditing = isEditingTemplate();

    return {
      template: _isEditing ? getEditedPostTemplate() : null,
      isEditing: _isEditing,
      title: getEditedPostAttribute('title') ? getEditedPostAttribute('title') : (0,external_wp_i18n_namespaceObject.__)('Untitled')
    };
  }, []);
  const {
    clearSelectedBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    setIsEditingTemplate
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);

  if (!isEditing || !template) {
    return null;
  }

  let templateTitle = (0,external_wp_i18n_namespaceObject.__)('Default');

  if (template !== null && template !== void 0 && template.title) {
    templateTitle = template.title;
  } else if (!!template) {
    templateTitle = template.slug;
  }

  const hasOptions = !!(template.custom || template.wp_id || template.description);
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-template-top-area"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-post-template-post-title",
    isLink: true,
    showTooltip: true,
    label: (0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %s: Title of the referring post, e.g: "Hello World!" */
    (0,external_wp_i18n_namespaceObject.__)('Edit %s'), title),
    onClick: () => {
      clearSelectedBlock();
      setIsEditingTemplate(false);
    }
  }, title), hasOptions ? (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
    position: "bottom center",
    contentClassName: "edit-post-template-top-area__popover",
    renderToggle: _ref => {
      let {
        onToggle
      } = _ref;
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
        className: "edit-post-template-title",
        isLink: true,
        icon: chevron_down,
        showTooltip: true,
        onClick: onToggle,
        label: (0,external_wp_i18n_namespaceObject.__)('Template Options')
      }, templateTitle);
    },
    renderContent: () => (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(EditTemplateTitle, null), (0,external_wp_element_namespaceObject.createElement)(TemplateDescription, null), (0,external_wp_element_namespaceObject.createElement)(DeleteTemplate, null))
  }) : (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    className: "edit-post-template-title",
    size: "body",
    style: {
      lineHeight: '24px'
    }
  }, templateTitle));
}

/* harmony default export */ var template_title = (TemplateTitle);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */










function Header(_ref) {
  let {
    setEntitiesSavedStatesCallback
  } = _ref;
  const {
    hasActiveMetaboxes,
    isPublishSidebarOpened,
    isSaving,
    showIconLabels,
    hasReducedUI
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    hasActiveMetaboxes: select(store_store).hasMetaBoxes(),
    isPublishSidebarOpened: select(store_store).isPublishSidebarOpened(),
    isSaving: select(store_store).isSavingMetaBoxes(),
    showIconLabels: select(store_store).isFeatureActive('showIconLabels'),
    hasReducedUI: select(store_store).isFeatureActive('reducedUI')
  }), []);
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('large');
  const classes = classnames_default()('edit-post-header', {
    'has-reduced-ui': hasReducedUI
  });
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classes
  }, (0,external_wp_element_namespaceObject.createElement)(main_dashboard_button.Slot, null, (0,external_wp_element_namespaceObject.createElement)(fullscreen_mode_close, {
    showTooltip: true
  })), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-header__toolbar"
  }, (0,external_wp_element_namespaceObject.createElement)(header_toolbar, null), (0,external_wp_element_namespaceObject.createElement)(template_title, null)), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-header__settings"
  }, !isPublishSidebarOpened && // This button isn't completely hidden by the publish sidebar.
  // We can't hide the whole toolbar when the publish sidebar is open because
  // we want to prevent mounting/unmounting the PostPublishButtonOrToggle DOM node.
  // We track that DOM node to return focus to the PostPublishButtonOrToggle
  // when the publish sidebar has been closed.
  (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSavedState, {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving,
    showIconLabels: showIconLabels
  }), (0,external_wp_element_namespaceObject.createElement)(DevicePreview, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPreviewButton, {
    forceIsAutosaveable: hasActiveMetaboxes,
    forcePreviewLink: isSaving ? null : undefined
  }), (0,external_wp_element_namespaceObject.createElement)(post_publish_button_or_toggle, {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving,
    setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
  }), (isLargeViewport || !showIconLabels) && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(pinned_items.Slot, {
    scope: "core/edit-post"
  }), (0,external_wp_element_namespaceObject.createElement)(more_menu, {
    showIconLabels: showIconLabels
  })), showIconLabels && !isLargeViewport && (0,external_wp_element_namespaceObject.createElement)(more_menu, {
    showIconLabels: showIconLabels
  })));
}

/* harmony default export */ var header = (Header);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js


/**
 * WordPress dependencies
 */

const close_close = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ var library_close = (close_close);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/secondary-sidebar/inserter-sidebar.js



/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function InserterSidebar() {
  const {
    insertionPoint,
    showMostUsedBlocks
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isFeatureActive,
      __experimentalGetInsertionPoint
    } = select(store_store);
    return {
      insertionPoint: __experimentalGetInsertionPoint(),
      showMostUsedBlocks: isFeatureActive('mostUsedBlocks')
    };
  }, []);
  const {
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  const TagName = !isMobileViewport ? external_wp_components_namespaceObject.VisuallyHidden : 'div';
  const [inserterDialogRef, inserterDialogProps] = (0,external_wp_compose_namespaceObject.__experimentalUseDialog)({
    onClose: () => setIsInserterOpened(false),
    focusOnMount: null
  });
  const libraryRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    libraryRef.current.focusSearch();
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)("div", _extends({
    ref: inserterDialogRef
  }, inserterDialogProps, {
    className: "edit-post-editor__inserter-panel"
  }), (0,external_wp_element_namespaceObject.createElement)(TagName, {
    className: "edit-post-editor__inserter-panel-header"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    icon: library_close,
    label: (0,external_wp_i18n_namespaceObject.__)('Close block inserter'),
    onClick: () => setIsInserterOpened(false)
  })), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-editor__inserter-panel-content"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalLibrary, {
    showMostUsedBlocks: showMostUsedBlocks,
    showInserterHelpPanel: true,
    shouldFocusBlock: isMobileViewport,
    rootClientId: insertionPoint.rootClientId,
    __experimentalInsertionIndex: insertionPoint.insertionIndex,
    __experimentalFilterValue: insertionPoint.filterValue,
    ref: libraryRef
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/secondary-sidebar/list-view-sidebar.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function ListViewSidebar() {
  const {
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const focusOnMountRef = (0,external_wp_compose_namespaceObject.useFocusOnMount)('firstElement');
  const headerFocusReturnRef = (0,external_wp_compose_namespaceObject.useFocusReturn)();
  const contentFocusReturnRef = (0,external_wp_compose_namespaceObject.useFocusReturn)();

  function closeOnEscape(event) {
    if (event.keyCode === external_wp_keycodes_namespaceObject.ESCAPE && !event.defaultPrevented) {
      event.preventDefault();
      setIsListViewOpened(false);
    }
  }

  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(ListViewSidebar);
  const labelId = `edit-post-editor__list-view-panel-label-${instanceId}`;
  return (// eslint-disable-next-line jsx-a11y/no-static-element-interactions
    (0,external_wp_element_namespaceObject.createElement)("div", {
      "aria-labelledby": labelId,
      className: "edit-post-editor__list-view-panel",
      onKeyDown: closeOnEscape
    }, (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-editor__list-view-panel-header",
      ref: headerFocusReturnRef
    }, (0,external_wp_element_namespaceObject.createElement)("strong", {
      id: labelId
    }, (0,external_wp_i18n_namespaceObject.__)('List View')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      icon: close_small,
      label: (0,external_wp_i18n_namespaceObject.__)('Close List View Sidebar'),
      onClick: () => setIsListViewOpened(false)
    })), (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-editor__list-view-panel-content",
      ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([contentFocusReturnRef, focusOnMountRef])
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalListView, null)))
  );
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/cog.js


/**
 * WordPress dependencies
 */

const cog = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M10.289 4.836A1 1 0 0111.275 4h1.306a1 1 0 01.987.836l.244 1.466c.787.26 1.503.679 2.108 1.218l1.393-.522a1 1 0 011.216.437l.653 1.13a1 1 0 01-.23 1.273l-1.148.944a6.025 6.025 0 010 2.435l1.149.946a1 1 0 01.23 1.272l-.653 1.13a1 1 0 01-1.216.437l-1.394-.522c-.605.54-1.32.958-2.108 1.218l-.244 1.466a1 1 0 01-.987.836h-1.306a1 1 0 01-.986-.836l-.244-1.466a5.995 5.995 0 01-2.108-1.218l-1.394.522a1 1 0 01-1.217-.436l-.653-1.131a1 1 0 01.23-1.272l1.149-.946a6.026 6.026 0 010-2.435l-1.148-.944a1 1 0 01-.23-1.272l.653-1.131a1 1 0 011.217-.437l1.393.522a5.994 5.994 0 012.108-1.218l.244-1.466zM14.929 12a3 3 0 11-6 0 3 3 0 016 0z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_cog = (cog);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-header/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



const SettingsHeader = _ref => {
  let {
    sidebarName
  } = _ref;
  const {
    openGeneralSidebar
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);

  const openDocumentSettings = () => openGeneralSidebar('edit-post/document');

  const openBlockSettings = () => openGeneralSidebar('edit-post/block');

  const {
    documentLabel,
    isTemplateMode
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const postTypeLabel = select(external_wp_editor_namespaceObject.store).getPostTypeLabel();
    return {
      // translators: Default label for the Document sidebar tab, not selected.
      documentLabel: postTypeLabel || (0,external_wp_i18n_namespaceObject._x)('Document', 'noun'),
      isTemplateMode: select(store_store).isEditingTemplate()
    };
  }, []);
  const [documentAriaLabel, documentActiveClass] = sidebarName === 'edit-post/document' ? // translators: ARIA label for the Document sidebar tab, selected. %s: Document label.
  [(0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('%s (selected)'), documentLabel), 'is-active'] : [documentLabel, ''];
  const [blockAriaLabel, blockActiveClass] = sidebarName === 'edit-post/block' ? // translators: ARIA label for the Block Settings Sidebar tab, selected.
  [(0,external_wp_i18n_namespaceObject.__)('Block (selected)'), 'is-active'] : // translators: ARIA label for the Block Settings Sidebar tab, not selected.
  [(0,external_wp_i18n_namespaceObject.__)('Block'), ''];
  const [templateAriaLabel, templateActiveClass] = sidebarName === 'edit-post/document' ? [(0,external_wp_i18n_namespaceObject.__)('Template (selected)'), 'is-active'] : [(0,external_wp_i18n_namespaceObject.__)('Template'), ''];
  /* Use a list so screen readers will announce how many tabs there are. */

  return (0,external_wp_element_namespaceObject.createElement)("ul", null, !isTemplateMode && (0,external_wp_element_namespaceObject.createElement)("li", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    onClick: openDocumentSettings,
    className: `edit-post-sidebar__panel-tab ${documentActiveClass}`,
    "aria-label": documentAriaLabel,
    "data-label": documentLabel
  }, documentLabel)), isTemplateMode && (0,external_wp_element_namespaceObject.createElement)("li", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    onClick: openDocumentSettings,
    className: `edit-post-sidebar__panel-tab ${templateActiveClass}`,
    "aria-label": templateAriaLabel,
    "data-label": (0,external_wp_i18n_namespaceObject.__)('Template')
  }, (0,external_wp_i18n_namespaceObject.__)('Template'))), (0,external_wp_element_namespaceObject.createElement)("li", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    onClick: openBlockSettings,
    className: `edit-post-sidebar__panel-tab ${blockActiveClass}`,
    "aria-label": blockAriaLabel // translators: Data label for the Block Settings Sidebar tab.
    ,
    "data-label": (0,external_wp_i18n_namespaceObject.__)('Block')
  }, // translators: Text label for the Block Settings Sidebar tab.
  (0,external_wp_i18n_namespaceObject.__)('Block'))));
};

/* harmony default export */ var settings_header = (SettingsHeader);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-visibility/index.js


/**
 * WordPress dependencies
 */




function PostVisibility() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    // Anchor the popover to the middle of the entire row so that it doesn't
    // move around when the label changes.
    anchor: popoverAnchor
  }), [popoverAnchor]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostVisibilityCheck, {
    render: _ref => {
      let {
        canEdit
      } = _ref;
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
        ref: setPopoverAnchor,
        className: "edit-post-post-visibility"
      }, (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_i18n_namespaceObject.__)('Visibility')), !canEdit && (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostVisibilityLabel, null)), canEdit && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
        position: "bottom left",
        contentClassName: "edit-post-post-visibility__dialog",
        popoverProps: popoverProps,
        focusOnMount: true,
        renderToggle: _ref2 => {
          let {
            isOpen,
            onToggle
          } = _ref2;
          return (0,external_wp_element_namespaceObject.createElement)(PostVisibilityToggle, {
            isOpen: isOpen,
            onClick: onToggle
          });
        },
        renderContent: _ref3 => {
          let {
            onClose
          } = _ref3;
          return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostVisibility, {
            onClose: onClose
          });
        }
      }));
    }
  });
}

function PostVisibilityToggle(_ref4) {
  let {
    isOpen,
    onClick
  } = _ref4;
  const label = (0,external_wp_editor_namespaceObject.usePostVisibilityLabel)();
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-post-post-visibility__toggle",
    variant: "tertiary",
    "aria-expanded": isOpen // translators: %s: Current post visibility.
    ,
    "aria-label": (0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('Select visibility: %s'), label),
    onClick: onClick
  }, label);
}

/* harmony default export */ var post_visibility = (PostVisibility);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-trash/index.js


/**
 * WordPress dependencies
 */


function PostTrash() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTrashCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTrash, null)));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-schedule/index.js


/**
 * WordPress dependencies
 */




function PostSchedule() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    anchor: popoverAnchor
  }), [popoverAnchor]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostScheduleCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-schedule",
    ref: setPopoverAnchor
  }, (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_i18n_namespaceObject.__)('Publish')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    position: "bottom left",
    contentClassName: "edit-post-post-schedule__dialog",
    focusOnMount: true,
    renderToggle: _ref => {
      let {
        isOpen,
        onToggle
      } = _ref;
      return (0,external_wp_element_namespaceObject.createElement)(PostScheduleToggle, {
        isOpen: isOpen,
        onClick: onToggle
      });
    },
    renderContent: _ref2 => {
      let {
        onClose
      } = _ref2;
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSchedule, {
        onClose: onClose
      });
    }
  })));
}

function PostScheduleToggle(_ref3) {
  let {
    isOpen,
    onClick
  } = _ref3;
  const label = (0,external_wp_editor_namespaceObject.usePostScheduleLabel)();
  const fullLabel = (0,external_wp_editor_namespaceObject.usePostScheduleLabel)({
    full: true
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-post-post-schedule__toggle",
    variant: "tertiary",
    label: fullLabel,
    showTooltip: true,
    "aria-expanded": isOpen // translators: %s: Current post date.
    ,
    "aria-label": (0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('Change date: %s'), label),
    onClick: onClick
  }, label);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-sticky/index.js


/**
 * WordPress dependencies
 */


function PostSticky() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostStickyCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSticky, null)));
}
/* harmony default export */ var post_sticky = (PostSticky);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-author/index.js


/**
 * WordPress dependencies
 */


function PostAuthor() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostAuthorCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-author"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostAuthor, null)));
}
/* harmony default export */ var post_author = (PostAuthor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-slug/index.js


/**
 * WordPress dependencies
 */


function PostSlug() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSlugCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-slug"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSlug, null)));
}
/* harmony default export */ var post_slug = (PostSlug);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-format/index.js


/**
 * WordPress dependencies
 */


function PostFormat() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostFormatCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-format"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostFormat, null)));
}
/* harmony default export */ var post_format = (PostFormat);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-pending-status/index.js


/**
 * WordPress dependencies
 */


function PostPendingStatus() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPendingStatusCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPendingStatus, null)));
}
/* harmony default export */ var post_pending_status = (PostPendingStatus);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-status-info/index.js


/**
 * Defines as extensibility slot for the Summary panel.
 */

/**
 * WordPress dependencies
 */

const {
  Fill: plugin_post_status_info_Fill,
  Slot: plugin_post_status_info_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('PluginPostStatusInfo');
/**
 * Renders a row in the Summary panel of the Document sidebar.
 * It should be noted that this is named and implemented around the function it serves
 * and not its location, which may change in future iterations.
 *
 * @param {Object}    props             Component properties.
 * @param {string}    [props.className] An optional class name added to the row.
 * @param {WPElement} props.children    Children to be rendered.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
 *
 * function MyPluginPostStatusInfo() {
 * 	return wp.element.createElement(
 * 		PluginPostStatusInfo,
 * 		{
 * 			className: 'my-plugin-post-status-info',
 * 		},
 * 		__( 'My post status info' )
 * 	)
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginPostStatusInfo } from '@wordpress/edit-post';
 *
 * const MyPluginPostStatusInfo = () => (
 * 	<PluginPostStatusInfo
 * 		className="my-plugin-post-status-info"
 * 	>
 * 		{ __( 'My post status info' ) }
 * 	</PluginPostStatusInfo>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */

const PluginPostStatusInfo = _ref => {
  let {
    children,
    className
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(plugin_post_status_info_Fill, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: className
  }, children));
};

PluginPostStatusInfo.Slot = plugin_post_status_info_Slot;
/* harmony default export */ var plugin_post_status_info = (PluginPostStatusInfo);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/add-template.js


/**
 * WordPress dependencies
 */

const addTemplate = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M18.5 5.5V8H20V5.5H22.5V4H20V1.5H18.5V4H16V5.5H18.5ZM13.9624 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V10.0391H18.5V18C18.5 18.2761 18.2761 18.5 18 18.5H10L10 10.4917L16.4589 10.5139L16.4641 9.01389L5.5 8.97618V6C5.5 5.72386 5.72386 5.5 6 5.5H13.9624V4ZM5.5 10.4762V18C5.5 18.2761 5.72386 18.5 6 18.5H8.5L8.5 10.4865L5.5 10.4762Z"
}));
/* harmony default export */ var add_template = (addTemplate);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-template/create-modal.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



const DEFAULT_TITLE = (0,external_wp_i18n_namespaceObject.__)('Custom Template');

function PostTemplateCreateModal(_ref) {
  let {
    onClose
  } = _ref;
  const defaultBlockTemplate = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_editor_namespaceObject.store).getEditorSettings().defaultBlockTemplate, []);
  const {
    __unstableCreateTemplate,
    __unstableSwitchToTemplateMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)('');
  const [isBusy, setIsBusy] = (0,external_wp_element_namespaceObject.useState)(false);

  const cancel = () => {
    setTitle('');
    onClose();
  };

  const submit = async event => {
    event.preventDefault();

    if (isBusy) {
      return;
    }

    setIsBusy(true);
    const newTemplateContent = defaultBlockTemplate !== null && defaultBlockTemplate !== void 0 ? defaultBlockTemplate : (0,external_wp_blocks_namespaceObject.serialize)([(0,external_wp_blocks_namespaceObject.createBlock)('core/group', {
      tagName: 'header',
      layout: {
        inherit: true
      }
    }, [(0,external_wp_blocks_namespaceObject.createBlock)('core/site-title'), (0,external_wp_blocks_namespaceObject.createBlock)('core/site-tagline')]), (0,external_wp_blocks_namespaceObject.createBlock)('core/separator'), (0,external_wp_blocks_namespaceObject.createBlock)('core/group', {
      tagName: 'main'
    }, [(0,external_wp_blocks_namespaceObject.createBlock)('core/group', {
      layout: {
        inherit: true
      }
    }, [(0,external_wp_blocks_namespaceObject.createBlock)('core/post-title')]), (0,external_wp_blocks_namespaceObject.createBlock)('core/post-content', {
      layout: {
        inherit: true
      }
    })])]);
    await __unstableCreateTemplate({
      slug: (0,external_wp_url_namespaceObject.cleanForSlug)(title || DEFAULT_TITLE),
      content: newTemplateContent,
      title: title || DEFAULT_TITLE
    });
    setIsBusy(false);
    cancel();

    __unstableSwitchToTemplateMode(true);
  };

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    title: (0,external_wp_i18n_namespaceObject.__)('Create custom template'),
    closeLabel: (0,external_wp_i18n_namespaceObject.__)('Close'),
    onRequestClose: cancel,
    className: "edit-post-post-template__create-modal"
  }, (0,external_wp_element_namespaceObject.createElement)("form", {
    className: "edit-post-post-template__create-form",
    onSubmit: submit
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Flex, {
    align: "flex-start",
    gap: 8
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Name'),
    value: title,
    onChange: setTitle,
    placeholder: DEFAULT_TITLE,
    disabled: isBusy,
    help: (0,external_wp_i18n_namespaceObject.__)('Describe the template, e.g. "Post with sidebar". Custom templates can be applied to any post or page.')
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Flex, {
    justify: "flex-end",
    expanded: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: cancel
  }, (0,external_wp_i18n_namespaceObject.__)('Cancel'))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    type: "submit",
    isBusy: isBusy,
    "aria-disabled": isBusy
  }, (0,external_wp_i18n_namespaceObject.__)('Create'))))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-template/form.js


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */



function PostTemplateForm(_ref) {
  var _options$find, _selectedOption$value;

  let {
    onClose
  } = _ref;
  const {
    isPostsPage,
    availableTemplates,
    fetchedTemplates,
    selectedTemplateSlug,
    canCreate,
    canEdit
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      canUser,
      getEntityRecord,
      getEntityRecords
    } = select(external_wp_coreData_namespaceObject.store);
    const editorSettings = select(external_wp_editor_namespaceObject.store).getEditorSettings();
    const siteSettings = canUser('read', 'settings') ? getEntityRecord('root', 'site') : undefined;

    const _isPostsPage = select(external_wp_editor_namespaceObject.store).getCurrentPostId() === (siteSettings === null || siteSettings === void 0 ? void 0 : siteSettings.page_for_posts);

    const canCreateTemplates = canUser('create', 'templates');
    return {
      isPostsPage: _isPostsPage,
      availableTemplates: editorSettings.availableTemplates,
      fetchedTemplates: canCreateTemplates ? getEntityRecords('postType', 'wp_template', {
        post_type: select(external_wp_editor_namespaceObject.store).getCurrentPostType(),
        per_page: -1
      }) : undefined,
      selectedTemplateSlug: select(external_wp_editor_namespaceObject.store).getEditedPostAttribute('template'),
      canCreate: canCreateTemplates && !_isPostsPage && editorSettings.supportsTemplateMode,
      canEdit: canCreateTemplates && editorSettings.supportsTemplateMode && !!select(store_store).getEditedPostTemplate()
    };
  }, []);
  const options = (0,external_wp_element_namespaceObject.useMemo)(() => Object.entries({ ...availableTemplates,
    ...Object.fromEntries((fetchedTemplates !== null && fetchedTemplates !== void 0 ? fetchedTemplates : []).map(_ref2 => {
      let {
        slug,
        title
      } = _ref2;
      return [slug, title.rendered];
    }))
  }).map(_ref3 => {
    let [slug, title] = _ref3;
    return {
      value: slug,
      label: title
    };
  }), [availableTemplates, fetchedTemplates]);
  const selectedOption = (_options$find = options.find(option => option.value === selectedTemplateSlug)) !== null && _options$find !== void 0 ? _options$find : options.find(option => !option.value); // The default option has '' value.

  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_editor_namespaceObject.store);
  const {
    __unstableSwitchToTemplateMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const [isCreateModalOpen, setIsCreateModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-post-template__form"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalInspectorPopoverHeader, {
    title: (0,external_wp_i18n_namespaceObject.__)('Template'),
    help: (0,external_wp_i18n_namespaceObject.__)('Templates define the way content is displayed when viewing your site.'),
    actions: canCreate ? [{
      icon: add_template,
      label: (0,external_wp_i18n_namespaceObject.__)('Add template'),
      onClick: () => setIsCreateModalOpen(true)
    }] : [],
    onClose: onClose
  }), isPostsPage ? (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Notice, {
    className: "edit-post-post-template__notice",
    status: "warning",
    isDismissible: false
  }, (0,external_wp_i18n_namespaceObject.__)('The posts page template cannot be changed.')) : (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SelectControl, {
    hideLabelFromVision: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Template'),
    value: (_selectedOption$value = selectedOption === null || selectedOption === void 0 ? void 0 : selectedOption.value) !== null && _selectedOption$value !== void 0 ? _selectedOption$value : '',
    options: options,
    onChange: slug => editPost({
      template: slug || ''
    })
  }), canEdit && (0,external_wp_element_namespaceObject.createElement)("p", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "link",
    onClick: () => __unstableSwitchToTemplateMode()
  }, (0,external_wp_i18n_namespaceObject.__)('Edit template'))), isCreateModalOpen && (0,external_wp_element_namespaceObject.createElement)(PostTemplateCreateModal, {
    onClose: () => setIsCreateModalOpen(false)
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-template/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function PostTemplate() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    anchor: popoverAnchor
  }), [popoverAnchor]);
  const isVisible = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$canUser;

    const postTypeSlug = select(external_wp_editor_namespaceObject.store).getCurrentPostType();
    const postType = select(external_wp_coreData_namespaceObject.store).getPostType(postTypeSlug);

    if (!(postType !== null && postType !== void 0 && postType.viewable)) {
      return false;
    }

    const settings = select(external_wp_editor_namespaceObject.store).getEditorSettings();
    const hasTemplates = !!settings.availableTemplates && Object.keys(settings.availableTemplates).length > 0;

    if (hasTemplates) {
      return true;
    }

    if (!settings.supportsTemplateMode) {
      return false;
    }

    const canCreateTemplates = (_select$canUser = select(external_wp_coreData_namespaceObject.store).canUser('create', 'templates')) !== null && _select$canUser !== void 0 ? _select$canUser : false;
    return canCreateTemplates;
  }, []);

  if (!isVisible) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-template",
    ref: setPopoverAnchor
  }, (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_i18n_namespaceObject.__)('Template')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    position: "bottom left",
    className: "edit-post-post-template__dropdown",
    contentClassName: "edit-post-post-template__dialog",
    focusOnMount: true,
    renderToggle: _ref => {
      let {
        isOpen,
        onToggle
      } = _ref;
      return (0,external_wp_element_namespaceObject.createElement)(PostTemplateToggle, {
        isOpen: isOpen,
        onClick: onToggle
      });
    },
    renderContent: _ref2 => {
      let {
        onClose
      } = _ref2;
      return (0,external_wp_element_namespaceObject.createElement)(PostTemplateForm, {
        onClose: onClose
      });
    }
  }));
}

function PostTemplateToggle(_ref3) {
  let {
    isOpen,
    onClick
  } = _ref3;
  const templateTitle = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _template$title;

    const templateSlug = select(external_wp_editor_namespaceObject.store).getEditedPostAttribute('template');
    const {
      supportsTemplateMode,
      availableTemplates
    } = select(external_wp_editor_namespaceObject.store).getEditorSettings();

    if (!supportsTemplateMode && availableTemplates[templateSlug]) {
      return availableTemplates[templateSlug];
    }

    const template = select(store_store).getEditedPostTemplate();
    return (_template$title = template === null || template === void 0 ? void 0 : template.title) !== null && _template$title !== void 0 ? _template$title : template === null || template === void 0 ? void 0 : template.slug;
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-post-post-template__toggle",
    variant: "tertiary",
    "aria-expanded": isOpen,
    "aria-label": templateTitle ? (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Name of the currently selected template.
    (0,external_wp_i18n_namespaceObject.__)('Select template: %s'), templateTitle) : (0,external_wp_i18n_namespaceObject.__)('Select template'),
    onClick: onClick
  }, templateTitle !== null && templateTitle !== void 0 ? templateTitle : (0,external_wp_i18n_namespaceObject.__)('Default template'));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-url/index.js


/**
 * WordPress dependencies
 */




function PostURL() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    anchor: popoverAnchor
  }), [popoverAnchor]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostURLCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-url",
    ref: setPopoverAnchor
  }, (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_i18n_namespaceObject.__)('URL')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    position: "bottom left",
    className: "edit-post-post-url__dropdown",
    contentClassName: "edit-post-post-url__dialog",
    focusOnMount: true,
    renderToggle: _ref => {
      let {
        isOpen,
        onToggle
      } = _ref;
      return (0,external_wp_element_namespaceObject.createElement)(PostURLToggle, {
        isOpen: isOpen,
        onClick: onToggle
      });
    },
    renderContent: _ref2 => {
      let {
        onClose
      } = _ref2;
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostURL, {
        onClose: onClose
      });
    }
  })));
}

function PostURLToggle(_ref3) {
  let {
    isOpen,
    onClick
  } = _ref3;
  const label = (0,external_wp_editor_namespaceObject.usePostURLLabel)();
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-post-post-url__toggle",
    variant: "tertiary",
    "aria-expanded": isOpen // translators: %s: Current post URL.
    ,
    "aria-label": (0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('Change URL: %s'), label),
    onClick: onClick
  }, label);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-status/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */













/**
 * Module Constants
 */

const PANEL_NAME = 'post-status';

function PostStatus(_ref) {
  let {
    isOpened,
    onTogglePanel
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    className: "edit-post-post-status",
    title: (0,external_wp_i18n_namespaceObject.__)('Summary'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_wp_element_namespaceObject.createElement)(plugin_post_status_info.Slot, null, fills => (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(post_visibility, null), (0,external_wp_element_namespaceObject.createElement)(PostSchedule, null), (0,external_wp_element_namespaceObject.createElement)(PostURL, null), (0,external_wp_element_namespaceObject.createElement)(PostTemplate, null), (0,external_wp_element_namespaceObject.createElement)(post_sticky, null), (0,external_wp_element_namespaceObject.createElement)(post_pending_status, null), (0,external_wp_element_namespaceObject.createElement)(post_format, null), (0,external_wp_element_namespaceObject.createElement)(post_slug, null), (0,external_wp_element_namespaceObject.createElement)(post_author, null), fills, (0,external_wp_element_namespaceObject.createElement)(PostTrash, null))));
}

/* harmony default export */ var post_status = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
  // We use isEditorPanelRemoved to hide the panel if it was programatically removed. We do
  // not use isEditorPanelEnabled since this panel should not be disabled through the UI.
  const {
    isEditorPanelRemoved,
    isEditorPanelOpened
  } = select(store_store);
  return {
    isRemoved: isEditorPanelRemoved(PANEL_NAME),
    isOpened: isEditorPanelOpened(PANEL_NAME)
  };
}), (0,external_wp_compose_namespaceObject.ifCondition)(_ref2 => {
  let {
    isRemoved
  } = _ref2;
  return !isRemoved;
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => ({
  onTogglePanel() {
    return dispatch(store_store).toggleEditorPanelOpened(PANEL_NAME);
  }

}))])(PostStatus));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/last-revision/index.js


/**
 * WordPress dependencies
 */



function LastRevision() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostLastRevisionCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    className: "edit-post-last-revision__panel"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostLastRevision, null)));
}

/* harmony default export */ var last_revision = (LastRevision);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function TaxonomyPanel(_ref) {
  let {
    isEnabled,
    taxonomy,
    isOpened,
    onTogglePanel,
    children
  } = _ref;

  if (!isEnabled) {
    return null;
  }

  const taxonomyMenuName = (0,external_lodash_namespaceObject.get)(taxonomy, ['labels', 'menu_name']);

  if (!taxonomyMenuName) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: taxonomyMenuName,
    opened: isOpened,
    onToggle: onTogglePanel
  }, children);
}

/* harmony default export */ var taxonomy_panel = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, ownProps) => {
  const slug = (0,external_lodash_namespaceObject.get)(ownProps.taxonomy, ['slug']);
  const panelName = slug ? `taxonomy-panel-${slug}` : '';
  return {
    panelName,
    isEnabled: slug ? select(store_store).isEditorPanelEnabled(panelName) : false,
    isOpened: slug ? select(store_store).isEditorPanelOpened(panelName) : false
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, ownProps) => ({
  onTogglePanel: () => {
    dispatch(store_store).toggleEditorPanelOpened(ownProps.panelName);
  }
})))(TaxonomyPanel));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function PostTaxonomies() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTaxonomiesCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTaxonomies, {
    taxonomyWrapper: (content, taxonomy) => {
      return (0,external_wp_element_namespaceObject.createElement)(taxonomy_panel, {
        taxonomy: taxonomy
      }, content);
    }
  }));
}

/* harmony default export */ var post_taxonomies = (PostTaxonomies);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/featured-image/index.js


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
 * Module Constants
 */

const featured_image_PANEL_NAME = 'featured-image';

function FeaturedImage(_ref) {
  let {
    isEnabled,
    isOpened,
    postType,
    onTogglePanel
  } = _ref;

  if (!isEnabled) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostFeaturedImageCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_lodash_namespaceObject.get)(postType, ['labels', 'featured_image'], (0,external_wp_i18n_namespaceObject.__)('Featured image')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostFeaturedImage, null)));
}

const applyWithSelect = (0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getEditedPostAttribute
  } = select(external_wp_editor_namespaceObject.store);
  const {
    getPostType
  } = select(external_wp_coreData_namespaceObject.store);
  const {
    isEditorPanelEnabled,
    isEditorPanelOpened
  } = select(store_store);
  return {
    postType: getPostType(getEditedPostAttribute('type')),
    isEnabled: isEditorPanelEnabled(featured_image_PANEL_NAME),
    isOpened: isEditorPanelOpened(featured_image_PANEL_NAME)
  };
});
const applyWithDispatch = (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    toggleEditorPanelOpened
  } = dispatch(store_store);
  return {
    onTogglePanel: function () {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      return toggleEditorPanelOpened(featured_image_PANEL_NAME, ...args);
    }
  };
});
/* harmony default export */ var featured_image = ((0,external_wp_compose_namespaceObject.compose)(applyWithSelect, applyWithDispatch)(FeaturedImage));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-excerpt/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const post_excerpt_PANEL_NAME = 'post-excerpt';

function PostExcerpt(_ref) {
  let {
    isEnabled,
    isOpened,
    onTogglePanel
  } = _ref;

  if (!isEnabled) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostExcerptCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_wp_i18n_namespaceObject.__)('Excerpt'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostExcerpt, null)));
}

/* harmony default export */ var post_excerpt = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
  return {
    isEnabled: select(store_store).isEditorPanelEnabled(post_excerpt_PANEL_NAME),
    isOpened: select(store_store).isEditorPanelOpened(post_excerpt_PANEL_NAME)
  };
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => ({
  onTogglePanel() {
    return dispatch(store_store).toggleEditorPanelOpened(post_excerpt_PANEL_NAME);
  }

}))])(PostExcerpt));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/discussion-panel/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const discussion_panel_PANEL_NAME = 'discussion-panel';

function DiscussionPanel(_ref) {
  let {
    isEnabled,
    isOpened,
    onTogglePanel
  } = _ref;

  if (!isEnabled) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTypeSupportCheck, {
    supportKeys: ['comments', 'trackbacks']
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_wp_i18n_namespaceObject.__)('Discussion'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTypeSupportCheck, {
    supportKeys: "comments"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostComments, null))), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTypeSupportCheck, {
    supportKeys: "trackbacks"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPingbacks, null)))));
}

/* harmony default export */ var discussion_panel = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
  return {
    isEnabled: select(store_store).isEditorPanelEnabled(discussion_panel_PANEL_NAME),
    isOpened: select(store_store).isEditorPanelOpened(discussion_panel_PANEL_NAME)
  };
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => ({
  onTogglePanel() {
    return dispatch(store_store).toggleEditorPanelOpened(discussion_panel_PANEL_NAME);
  }

}))])(DiscussionPanel));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/page-attributes/index.js


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
 * Module Constants
 */

const page_attributes_PANEL_NAME = 'page-attributes';
function PageAttributes() {
  const {
    isEnabled,
    isOpened,
    postType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute
    } = select(external_wp_editor_namespaceObject.store);
    const {
      isEditorPanelEnabled,
      isEditorPanelOpened
    } = select(store_store);
    const {
      getPostType
    } = select(external_wp_coreData_namespaceObject.store);
    return {
      isEnabled: isEditorPanelEnabled(page_attributes_PANEL_NAME),
      isOpened: isEditorPanelOpened(page_attributes_PANEL_NAME),
      postType: getPostType(getEditedPostAttribute('type'))
    };
  }, []);
  const {
    toggleEditorPanelOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);

  if (!isEnabled || !postType) {
    return null;
  }

  const onTogglePanel = function () {
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    return toggleEditorPanelOpened(page_attributes_PANEL_NAME, ...args);
  };

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PageAttributesCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_lodash_namespaceObject.get)(postType, ['labels', 'attributes'], (0,external_wp_i18n_namespaceObject.__)('Page attributes')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PageAttributesParent, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PageAttributesOrder, null))));
}
/* harmony default export */ var page_attributes = (PageAttributes);

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
 * @return {WPComponent} The component to be rendered.
 */

function MetaBoxesArea(_ref) {
  let {
    location
  } = _ref;
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
    return select(store_store).isSavingMetaBoxes();
  }, []);
  const classes = classnames_default()('edit-post-meta-boxes-area', `is-${location}`, {
    'is-loading': isSaving
  });
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classes
  }, isSaving && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Spinner, null), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-meta-boxes-area__container",
    ref: container
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-meta-boxes-area__clear"
  }));
}

/* harmony default export */ var meta_boxes_area = (MetaBoxesArea);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-box-visibility.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
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

/* harmony default export */ var meta_box_visibility = ((0,external_wp_data_namespaceObject.withSelect)((select, _ref) => {
  let {
    id
  } = _ref;
  return {
    isVisible: select(store_store).isEditorPanelEnabled(`meta-box-${id}`)
  };
})(MetaBoxVisibility));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




function MetaBoxes(_ref) {
  let {
    location
  } = _ref;
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
    } = select(store_store);
    return {
      metaBoxes: getMetaBoxesPerLocation(location),
      areMetaBoxesInitialized: _areMetaBoxesInitialized(),
      isEditorReady: __unstableIsEditorReady()
    };
  }, [location]); // When editor is ready, initialize postboxes (wp core script) and metabox
  // saving. This initializes all meta box locations, not just this specific
  // one.

  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isEditorReady && !areMetaBoxesInitialized) {
      registry.dispatch(store_store).initializeMetaBoxes();
    }
  }, [isEditorReady, areMetaBoxesInitialized]);

  if (!areMetaBoxesInitialized) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_lodash_namespaceObject.map)(metaBoxes, _ref2 => {
    let {
      id
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)(meta_box_visibility, {
      key: id,
      id: id
    });
  }), (0,external_wp_element_namespaceObject.createElement)(meta_boxes_area, {
    location: location
  }));
}

;// CONCATENATED MODULE: external ["wp","warning"]
var external_wp_warning_namespaceObject = window["wp"]["warning"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-document-setting-panel/index.js


/**
 * Defines as extensibility slot for the Settings sidebar
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



const {
  Fill: plugin_document_setting_panel_Fill,
  Slot: plugin_document_setting_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('PluginDocumentSettingPanel');

const PluginDocumentSettingFill = _ref => {
  let {
    isEnabled,
    panelName,
    opened,
    onToggle,
    className,
    title,
    icon,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(enable_plugin_document_setting_panel, {
    label: title,
    panelName: panelName
  }), (0,external_wp_element_namespaceObject.createElement)(plugin_document_setting_panel_Fill, null, isEnabled && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    className: className,
    title: title,
    icon: icon,
    opened: opened,
    onToggle: onToggle
  }, children)));
};
/**
 * Renders items below the Status & Availability panel in the Document Sidebar.
 *
 * @param {Object}                props                                 Component properties.
 * @param {string}                [props.name]                          The machine-friendly name for the panel.
 * @param {string}                [props.className]                     An optional class name added to the row.
 * @param {string}                [props.title]                         The title of the panel
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var el = wp.element.createElement;
 * var __ = wp.i18n.__;
 * var registerPlugin = wp.plugins.registerPlugin;
 * var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
 *
 * function MyDocumentSettingPlugin() {
 * 	return el(
 * 		PluginDocumentSettingPanel,
 * 		{
 * 			className: 'my-document-setting-plugin',
 * 			title: 'My Panel',
 * 		},
 * 		__( 'My Document Setting Panel' )
 * 	);
 * }
 *
 * registerPlugin( 'my-document-setting-plugin', {
 * 		render: MyDocumentSettingPlugin
 * } );
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { registerPlugin } from '@wordpress/plugins';
 * import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
 *
 * const MyDocumentSettingTest = () => (
 * 		<PluginDocumentSettingPanel className="my-document-setting-plugin" title="My Panel">
 *			<p>My Document Setting Panel</p>
 *		</PluginDocumentSettingPanel>
 *	);
 *
 *  registerPlugin( 'document-setting-test', { render: MyDocumentSettingTest } );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


const PluginDocumentSettingPanel = (0,external_wp_compose_namespaceObject.compose)((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  if (undefined === ownProps.name) {
    typeof process !== "undefined" && process.env && "production" !== "production" ? 0 : void 0;
  }

  return {
    panelName: `${context.name}/${ownProps.name}`
  };
}), (0,external_wp_data_namespaceObject.withSelect)((select, _ref2) => {
  let {
    panelName
  } = _ref2;
  return {
    opened: select(store_store).isEditorPanelOpened(panelName),
    isEnabled: select(store_store).isEditorPanelEnabled(panelName)
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, _ref3) => {
  let {
    panelName
  } = _ref3;
  return {
    onToggle() {
      return dispatch(store_store).toggleEditorPanelOpened(panelName);
    }

  };
}))(PluginDocumentSettingFill);
PluginDocumentSettingPanel.Slot = plugin_document_setting_panel_Slot;
/* harmony default export */ var plugin_document_setting_panel = (PluginDocumentSettingPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-sidebar/index.js



/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Renders a sidebar when activated. The contents within the `PluginSidebar` will appear as content within the sidebar.
 * It also automatically renders a corresponding `PluginSidebarMenuItem` component when `isPinnable` flag is set to `true`.
 * If you wish to display the sidebar, you can with use the `PluginSidebarMoreMenuItem` component or the `wp.data.dispatch` API:
 *
 * ```js
 * wp.data.dispatch( 'core/edit-post' ).openGeneralSidebar( 'plugin-name/sidebar-name' );
 * ```
 *
 * @see PluginSidebarMoreMenuItem
 *
 * @param {Object}                props                                 Element props.
 * @param {string}                props.name                            A string identifying the sidebar. Must be unique for every sidebar registered within the scope of your plugin.
 * @param {string}                [props.className]                     An optional class name added to the sidebar body.
 * @param {string}                props.title                           Title displayed at the top of the sidebar.
 * @param {boolean}               [props.isPinnable=true]               Whether to allow to pin sidebar to the toolbar. When set to `true` it also automatically renders a corresponding menu item.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var el = wp.element.createElement;
 * var PanelBody = wp.components.PanelBody;
 * var PluginSidebar = wp.editPost.PluginSidebar;
 * var moreIcon = wp.element.createElement( 'svg' ); //... svg element.
 *
 * function MyPluginSidebar() {
 * 	return el(
 * 			PluginSidebar,
 * 			{
 * 				name: 'my-sidebar',
 * 				title: 'My sidebar title',
 * 				icon: moreIcon,
 * 			},
 * 			el(
 * 				PanelBody,
 * 				{},
 * 				__( 'My sidebar content' )
 * 			)
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PanelBody } from '@wordpress/components';
 * import { PluginSidebar } from '@wordpress/edit-post';
 * import { more } from '@wordpress/icons';
 *
 * const MyPluginSidebar = () => (
 * 	<PluginSidebar
 * 		name="my-sidebar"
 * 		title="My sidebar title"
 * 		icon={ more }
 * 	>
 * 		<PanelBody>
 * 			{ __( 'My sidebar content' ) }
 * 		</PanelBody>
 * 	</PluginSidebar>
 * );
 * ```
 */

function PluginSidebarEditPost(_ref) {
  let {
    className,
    ...props
  } = _ref;
  const {
    postTitle,
    shortcut,
    showIconLabels
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return {
      postTitle: select(external_wp_editor_namespaceObject.store).getEditedPostAttribute('title'),
      shortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getShortcutRepresentation('core/edit-post/toggle-sidebar'),
      showIconLabels: select(store_store).isFeatureActive('showIconLabels')
    };
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(complementary_area, _extends({
    panelClassName: className,
    className: "edit-post-sidebar",
    smallScreenTitle: postTitle || (0,external_wp_i18n_namespaceObject.__)('(no title)'),
    scope: "core/edit-post",
    toggleShortcut: shortcut,
    showIconLabels: showIconLabels
  }, props));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/layout.js


/**
 * WordPress dependencies
 */

const layout = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ var library_layout = (layout);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/template-summary/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function TemplateSummary() {
  const template = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostTemplate
    } = select(store_store);
    return getEditedPostTemplate();
  }, []);

  if (!template) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Flex, {
    align: "flex-start",
    gap: "3"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(icon, {
    icon: library_layout
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexBlock, null, (0,external_wp_element_namespaceObject.createElement)("h2", {
    className: "edit-post-template-summary__title"
  }, (template === null || template === void 0 ? void 0 : template.title) || (template === null || template === void 0 ? void 0 : template.slug)), (0,external_wp_element_namespaceObject.createElement)("p", null, template === null || template === void 0 ? void 0 : template.description))));
}

/* harmony default export */ var template_summary = (TemplateSummary);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-sidebar/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */
















const SIDEBAR_ACTIVE_BY_DEFAULT = external_wp_element_namespaceObject.Platform.select({
  web: true,
  native: false
});

const SettingsSidebar = () => {
  const {
    sidebarName,
    keyboardShortcut,
    isTemplateMode
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    // The settings sidebar is used by the edit-post/document and edit-post/block sidebars.
    // sidebarName represents the sidebar that is active or that should be active when the SettingsSidebar toggle button is pressed.
    // If one of the two sidebars is active the component will contain the content of that sidebar.
    // When neither of the two sidebars is active we can not simply return null, because the PluginSidebarEditPost
    // component, besides being used to render the sidebar, also renders the toggle button. In that case sidebarName
    // should contain the sidebar that will be active when the toggle button is pressed. If a block
    // is selected, that should be edit-post/block otherwise it's edit-post/document.
    let sidebar = select(store).getActiveComplementaryArea(store_store.name);

    if (!['edit-post/document', 'edit-post/block'].includes(sidebar)) {
      if (select(external_wp_blockEditor_namespaceObject.store).getBlockSelectionStart()) {
        sidebar = 'edit-post/block';
      }

      sidebar = 'edit-post/document';
    }

    const shortcut = select(external_wp_keyboardShortcuts_namespaceObject.store).getShortcutRepresentation('core/edit-post/toggle-sidebar');
    return {
      sidebarName: sidebar,
      keyboardShortcut: shortcut,
      isTemplateMode: select(store_store).isEditingTemplate()
    };
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(PluginSidebarEditPost, {
    identifier: sidebarName,
    header: (0,external_wp_element_namespaceObject.createElement)(settings_header, {
      sidebarName: sidebarName
    }),
    closeLabel: (0,external_wp_i18n_namespaceObject.__)('Close settings'),
    headerClassName: "edit-post-sidebar__panel-tabs"
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    title: (0,external_wp_i18n_namespaceObject.__)('Settings'),
    toggleShortcut: keyboardShortcut,
    icon: library_cog,
    isActiveByDefault: SIDEBAR_ACTIVE_BY_DEFAULT
  }, !isTemplateMode && sidebarName === 'edit-post/document' && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(post_status, null), (0,external_wp_element_namespaceObject.createElement)(plugin_document_setting_panel.Slot, null), (0,external_wp_element_namespaceObject.createElement)(last_revision, null), (0,external_wp_element_namespaceObject.createElement)(post_taxonomies, null), (0,external_wp_element_namespaceObject.createElement)(featured_image, null), (0,external_wp_element_namespaceObject.createElement)(post_excerpt, null), (0,external_wp_element_namespaceObject.createElement)(discussion_panel, null), (0,external_wp_element_namespaceObject.createElement)(page_attributes, null), (0,external_wp_element_namespaceObject.createElement)(MetaBoxes, {
    location: "side"
  })), isTemplateMode && sidebarName === 'edit-post/document' && (0,external_wp_element_namespaceObject.createElement)(template_summary, null), sidebarName === 'edit-post/block' && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockInspector, null));
};

/* harmony default export */ var settings_sidebar = (SettingsSidebar);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/image.js

function WelcomeGuideImage(_ref) {
  let {
    nonAnimatedSrc,
    animatedSrc
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)("picture", {
    className: "edit-post-welcome-guide__image"
  }, (0,external_wp_element_namespaceObject.createElement)("source", {
    srcSet: nonAnimatedSrc,
    media: "(prefers-reduced-motion: reduce)"
  }), (0,external_wp_element_namespaceObject.createElement)("img", {
    src: animatedSrc,
    width: "312",
    height: "240",
    alt: ""
  }));
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
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Guide, {
    className: "edit-post-welcome-guide",
    contentLabel: (0,external_wp_i18n_namespaceObject.__)('Welcome to the block editor'),
    finishButtonText: (0,external_wp_i18n_namespaceObject.__)('Get started'),
    onFinish: () => toggleFeature('welcomeGuide'),
    pages: [{
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-post-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Welcome to the block editor')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-post-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('In the WordPress editor, each paragraph, image, or video is presented as a distinct “block” of content.')))
    }, {
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-editor.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-editor.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-post-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Make each block your own')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-post-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('Each block comes with its own set of controls for changing things like color, width, and alignment. These will show and hide automatically when you have a block selected.')))
    }, {
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-library.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-library.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-post-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Get to know the block library')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-post-welcome-guide__text"
      }, (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.__)('All of the blocks available to you live in the block library. You’ll find it wherever you see the <InserterIconImage /> icon.'), {
        InserterIconImage: (0,external_wp_element_namespaceObject.createElement)("img", {
          alt: (0,external_wp_i18n_namespaceObject.__)('inserter'),
          src: "data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 18 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='18' height='18' rx='2' fill='%231E1E1E'/%3E%3Cpath d='M9.22727 4V14M4 8.77273H14' stroke='white' stroke-width='1.5'/%3E%3C/svg%3E%0A"
        })
      })))
    }, {
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-post-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Learn how to use the block editor')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-post-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('New to the block editor? Want to learn more about using it? '), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ExternalLink, {
        href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/support/article/wordpress-editor/')
      }, (0,external_wp_i18n_namespaceObject.__)("Here's a detailed guide."))))
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
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Guide, {
    className: "edit-template-welcome-guide",
    contentLabel: (0,external_wp_i18n_namespaceObject.__)('Welcome to the template editor'),
    finishButtonText: (0,external_wp_i18n_namespaceObject.__)('Get started'),
    onFinish: () => toggleFeature('welcomeGuideTemplate'),
    pages: [{
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-post-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Welcome to the template editor')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-post-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('Templates help define the layout of the site. You can customize all aspects of your posts and pages using blocks and patterns in this editor.')))
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
    isTemplateMode
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isFeatureActive,
      isEditingTemplate
    } = select(store_store);

    const _isTemplateMode = isEditingTemplate();

    const feature = _isTemplateMode ? 'welcomeGuideTemplate' : 'welcomeGuide';
    return {
      isActive: isFeatureActive(feature),
      isTemplateMode: _isTemplateMode
    };
  }, []);

  if (!isActive) {
    return null;
  }

  return isTemplateMode ? (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideTemplate, null) : (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideDefault, null);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-publish-panel/index.js


/**
 * WordPress dependencies
 */



const {
  Fill: plugin_post_publish_panel_Fill,
  Slot: plugin_post_publish_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('PluginPostPublishPanel');

const PluginPostPublishPanelFill = _ref => {
  let {
    children,
    className,
    title,
    initialOpen = false,
    icon
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(plugin_post_publish_panel_Fill, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    className: className,
    initialOpen: initialOpen || !title,
    title: title,
    icon: icon
  }, children));
};
/**
 * Renders provided content to the post-publish panel in the publish flow
 * (side panel that opens after a user publishes the post).
 *
 * @param {Object}                props                                 Component properties.
 * @param {string}                [props.className]                     An optional class name added to the panel.
 * @param {string}                [props.title]                         Title displayed at the top of the panel.
 * @param {boolean}               [props.initialOpen=false]             Whether to have the panel initially opened. When no title is provided it is always opened.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPostPublishPanel = wp.editPost.PluginPostPublishPanel;
 *
 * function MyPluginPostPublishPanel() {
 * 	return wp.element.createElement(
 * 		PluginPostPublishPanel,
 * 		{
 * 			className: 'my-plugin-post-publish-panel',
 * 			title: __( 'My panel title' ),
 * 			initialOpen: true,
 * 		},
 * 		__( 'My panel content' )
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginPostPublishPanel } from '@wordpress/edit-post';
 *
 * const MyPluginPostPublishPanel = () => (
 * 	<PluginPostPublishPanel
 * 		className="my-plugin-post-publish-panel"
 * 		title={ __( 'My panel title' ) }
 * 		initialOpen={ true }
 * 	>
 *         { __( 'My panel content' ) }
 * 	</PluginPostPublishPanel>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


const PluginPostPublishPanel = (0,external_wp_compose_namespaceObject.compose)((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon
  };
}))(PluginPostPublishPanelFill);
PluginPostPublishPanel.Slot = plugin_post_publish_panel_Slot;
/* harmony default export */ var plugin_post_publish_panel = (PluginPostPublishPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js


/**
 * WordPress dependencies
 */



const {
  Fill: plugin_pre_publish_panel_Fill,
  Slot: plugin_pre_publish_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('PluginPrePublishPanel');

const PluginPrePublishPanelFill = _ref => {
  let {
    children,
    className,
    title,
    initialOpen = false,
    icon
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(plugin_pre_publish_panel_Fill, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    className: className,
    initialOpen: initialOpen || !title,
    title: title,
    icon: icon
  }, children));
};
/**
 * Renders provided content to the pre-publish side panel in the publish flow
 * (side panel that opens when a user first pushes "Publish" from the main editor).
 *
 * @param {Object}                props                                 Component props.
 * @param {string}                [props.className]                     An optional class name added to the panel.
 * @param {string}                [props.title]                         Title displayed at the top of the panel.
 * @param {boolean}               [props.initialOpen=false]             Whether to have the panel initially opened.
 *                                                                      When no title is provided it is always opened.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/)
 *                                                                      icon slug string, or an SVG WP element, to be rendered when
 *                                                                      the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPrePublishPanel = wp.editPost.PluginPrePublishPanel;
 *
 * function MyPluginPrePublishPanel() {
 * 	return wp.element.createElement(
 * 		PluginPrePublishPanel,
 * 		{
 * 			className: 'my-plugin-pre-publish-panel',
 * 			title: __( 'My panel title' ),
 * 			initialOpen: true,
 * 		},
 * 		__( 'My panel content' )
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginPrePublishPanel } from '@wordpress/edit-post';
 *
 * const MyPluginPrePublishPanel = () => (
 * 	<PluginPrePublishPanel
 * 		className="my-plugin-pre-publish-panel"
 * 		title={ __( 'My panel title' ) }
 * 		initialOpen={ true }
 * 	>
 * 	    { __( 'My panel content' ) }
 * 	</PluginPrePublishPanel>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


const PluginPrePublishPanel = (0,external_wp_compose_namespaceObject.compose)((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon
  };
}))(PluginPrePublishPanelFill);
PluginPrePublishPanel.Slot = plugin_pre_publish_panel_Slot;
/* harmony default export */ var plugin_pre_publish_panel = (PluginPrePublishPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/actions-panel.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




const {
  Fill: actions_panel_Fill,
  Slot: actions_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('ActionsPanel');
const ActionsPanelFill = (/* unused pure expression or super */ null && (actions_panel_Fill));
function ActionsPanel(_ref) {
  let {
    setEntitiesSavedStatesCallback,
    closeEntitiesSavedStates,
    isEntitiesSavedStatesOpen
  } = _ref;
  const {
    closePublishSidebar,
    togglePublishSidebar
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    publishSidebarOpened,
    hasActiveMetaboxes,
    isSavingMetaBoxes,
    hasNonPostEntityChanges
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return {
      publishSidebarOpened: select(store_store).isPublishSidebarOpened(),
      hasActiveMetaboxes: select(store_store).hasMetaBoxes(),
      isSavingMetaBoxes: select(store_store).isSavingMetaBoxes(),
      hasNonPostEntityChanges: select(external_wp_editor_namespaceObject.store).hasNonPostEntityChanges()
    };
  }, []);
  const openEntitiesSavedStates = (0,external_wp_element_namespaceObject.useCallback)(() => setEntitiesSavedStatesCallback(true), []); // It is ok for these components to be unmounted when not in visual use.
  // We don't want more than one present at a time, decide which to render.

  let unmountableContent;

  if (publishSidebarOpened) {
    unmountableContent = (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPublishPanel, {
      onClose: closePublishSidebar,
      forceIsDirty: hasActiveMetaboxes,
      forceIsSaving: isSavingMetaBoxes,
      PrePublishExtension: plugin_pre_publish_panel.Slot,
      PostPublishExtension: plugin_post_publish_panel.Slot
    });
  } else if (hasNonPostEntityChanges) {
    unmountableContent = (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__toggle-entities-saved-states-panel"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      variant: "secondary",
      className: "edit-post-layout__toggle-entities-saved-states-panel-button",
      onClick: openEntitiesSavedStates,
      "aria-expanded": false
    }, (0,external_wp_i18n_namespaceObject.__)('Open save panel')));
  } else {
    unmountableContent = (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__toggle-publish-panel"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      variant: "secondary",
      className: "edit-post-layout__toggle-publish-panel-button",
      onClick: togglePublishSidebar,
      "aria-expanded": false
    }, (0,external_wp_i18n_namespaceObject.__)('Open publish panel')));
  } // Since EntitiesSavedStates controls its own panel, we can keep it
  // always mounted to retain its own component state (such as checkboxes).


  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isEntitiesSavedStatesOpen && (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EntitiesSavedStates, {
    close: closeEntitiesSavedStates
  }), (0,external_wp_element_namespaceObject.createElement)(actions_panel_Slot, {
    bubblesVirtually: true
  }), !isEntitiesSavedStatesOpen && unmountableContent);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/start-page-options/index.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



function useStartPatterns() {
  // A pattern is a start pattern if it includes 'core/post-content' in its blockTypes,
  // and it has no postTypes declares and the current post type is page or if
  // the current post type is part of the postTypes declared.
  const {
    blockPatternsWithPostContentBlockType,
    postType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      __experimentalGetPatternsByBlockTypes
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      getCurrentPostType
    } = select(external_wp_editor_namespaceObject.store);
    return {
      // get pa
      blockPatternsWithPostContentBlockType: __experimentalGetPatternsByBlockTypes('core/post-content'),
      postType: getCurrentPostType()
    };
  }, []);
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    // filter patterns without postTypes declared if the current postType is page
    // or patterns that declare the current postType in its post type array.
    return blockPatternsWithPostContentBlockType.filter(pattern => {
      return postType === 'page' && !pattern.postTypes || Array.isArray(pattern.postTypes) && pattern.postTypes.includes(postType);
    });
  }, [postType, blockPatternsWithPostContentBlockType]);
}

function PatternSelection(_ref) {
  let {
    onChoosePattern
  } = _ref;
  const blockPatterns = useStartPatterns();
  const shownBlockPatterns = (0,external_wp_compose_namespaceObject.useAsyncList)(blockPatterns);
  const {
    resetEditorBlocks
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_editor_namespaceObject.store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalBlockPatternsList, {
    blockPatterns: blockPatterns,
    shownPatterns: shownBlockPatterns,
    onClickPattern: (_pattern, blocks) => {
      resetEditorBlocks(blocks);
      onChoosePattern();
    }
  });
}

const START_PAGE_MODAL_STATES = {
  INITIAL: 'INITIAL',
  PATTERN: 'PATTERN',
  CLOSED: 'CLOSED'
};
function StartPageOptions() {
  const [modalState, setModalState] = (0,external_wp_element_namespaceObject.useState)(START_PAGE_MODAL_STATES.INITIAL);
  const blockPatterns = useStartPatterns();
  const hasStartPattern = blockPatterns.length > 0;
  const shouldOpenModel = (0,external_wp_data_namespaceObject.useSelect)(select => {
    if (!hasStartPattern || modalState !== START_PAGE_MODAL_STATES.INITIAL) {
      return false;
    }

    const {
      getEditedPostContent,
      isEditedPostSaveable
    } = select(external_wp_editor_namespaceObject.store);
    const {
      isEditingTemplate,
      isFeatureActive
    } = select(store_store);
    return !isEditedPostSaveable() && '' === getEditedPostContent() && !isEditingTemplate() && !isFeatureActive('welcomeGuide');
  }, [modalState, hasStartPattern]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (shouldOpenModel) {
      setModalState(START_PAGE_MODAL_STATES.PATTERN);
    }
  }, [shouldOpenModel]);

  if (modalState === START_PAGE_MODAL_STATES.INITIAL || modalState === START_PAGE_MODAL_STATES.CLOSED) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    className: "edit-post-start-page-options__modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Choose a pattern'),
    closeLabel: (0,external_wp_i18n_namespaceObject.__)('Cancel'),
    onRequestClose: () => {
      setModalState(START_PAGE_MODAL_STATES.CLOSED);
    }
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-start-page-options__modal-content"
  }, modalState === START_PAGE_MODAL_STATES.PATTERN && (0,external_wp_element_namespaceObject.createElement)(PatternSelection, {
    onChoosePattern: () => {
      setModalState(START_PAGE_MODAL_STATES.CLOSED);
    }
  })));
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
















const interfaceLabels = {
  /* translators: accessibility text for the editor top bar landmark region. */
  header: (0,external_wp_i18n_namespaceObject.__)('Editor top bar'),

  /* translators: accessibility text for the editor content landmark region. */
  body: (0,external_wp_i18n_namespaceObject.__)('Editor content'),

  /* translators: accessibility text for the editor settings landmark region. */
  sidebar: (0,external_wp_i18n_namespaceObject.__)('Editor settings'),

  /* translators: accessibility text for the editor publish landmark region. */
  actions: (0,external_wp_i18n_namespaceObject.__)('Editor publish'),

  /* translators: accessibility text for the editor footer landmark region. */
  footer: (0,external_wp_i18n_namespaceObject.__)('Editor footer')
};

function Layout(_ref) {
  let {
    styles
  } = _ref;
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  const isHugeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('huge', '>=');
  const {
    openGeneralSidebar,
    closeGeneralSidebar,
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const {
    mode,
    isFullscreenActive,
    isRichEditingEnabled,
    sidebarIsOpened,
    hasActiveMetaboxes,
    hasFixedToolbar,
    previousShortcut,
    nextShortcut,
    hasBlockSelected,
    isInserterOpened,
    isListViewOpened,
    showIconLabels,
    hasReducedUI,
    showBlockBreadcrumbs,
    isTemplateMode,
    documentLabel
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings,
      getPostTypeLabel
    } = select(external_wp_editor_namespaceObject.store);
    const editorSettings = getEditorSettings();
    const postTypeLabel = getPostTypeLabel();
    return {
      isTemplateMode: select(store_store).isEditingTemplate(),
      hasFixedToolbar: select(store_store).isFeatureActive('fixedToolbar'),
      sidebarIsOpened: !!(select(store).getActiveComplementaryArea(store_store.name) || select(store_store).isPublishSidebarOpened()),
      isFullscreenActive: select(store_store).isFeatureActive('fullscreenMode'),
      isInserterOpened: select(store_store).isInserterOpened(),
      isListViewOpened: select(store_store).isListViewOpened(),
      mode: select(store_store).getEditorMode(),
      isRichEditingEnabled: editorSettings.richEditingEnabled,
      hasActiveMetaboxes: select(store_store).hasMetaBoxes(),
      previousShortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getAllShortcutKeyCombinations('core/edit-post/previous-region'),
      nextShortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getAllShortcutKeyCombinations('core/edit-post/next-region'),
      showIconLabels: select(store_store).isFeatureActive('showIconLabels'),
      hasReducedUI: select(store_store).isFeatureActive('reducedUI'),
      showBlockBreadcrumbs: select(store_store).isFeatureActive('showBlockBreadcrumbs'),
      // translators: Default label for the Document in the Block Breadcrumb.
      documentLabel: postTypeLabel || (0,external_wp_i18n_namespaceObject._x)('Document', 'noun')
    };
  }, []);
  const className = classnames_default()('edit-post-layout', 'is-mode-' + mode, {
    'is-sidebar-opened': sidebarIsOpened,
    'has-fixed-toolbar': hasFixedToolbar,
    'has-metaboxes': hasActiveMetaboxes,
    'show-icon-labels': showIconLabels
  });

  const openSidebarPanel = () => openGeneralSidebar(hasBlockSelected ? 'edit-post/block' : 'edit-post/document'); // Inserter and Sidebars are mutually exclusive


  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (sidebarIsOpened && !isHugeViewport) {
      setIsInserterOpened(false);
    }
  }, [sidebarIsOpened, isHugeViewport]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isInserterOpened && !isHugeViewport) {
      closeGeneralSidebar();
    }
  }, [isInserterOpened, isHugeViewport]); // Local state for save panel.
  // Note 'truthy' callback implies an open panel.

  const [entitiesSavedStatesCallback, setEntitiesSavedStatesCallback] = (0,external_wp_element_namespaceObject.useState)(false);
  const closeEntitiesSavedStates = (0,external_wp_element_namespaceObject.useCallback)(arg => {
    if (typeof entitiesSavedStatesCallback === 'function') {
      entitiesSavedStatesCallback(arg);
    }

    setEntitiesSavedStatesCallback(false);
  }, [entitiesSavedStatesCallback]);
  const secondarySidebarLabel = isListViewOpened ? (0,external_wp_i18n_namespaceObject.__)('List View') : (0,external_wp_i18n_namespaceObject.__)('Block Library');

  const secondarySidebar = () => {
    if (mode === 'visual' && isInserterOpened) {
      return (0,external_wp_element_namespaceObject.createElement)(InserterSidebar, null);
    }

    if (mode === 'visual' && isListViewOpened) {
      return (0,external_wp_element_namespaceObject.createElement)(ListViewSidebar, null);
    }

    return null;
  };

  function onPluginAreaError(name) {
    createErrorNotice((0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %s: plugin name */
    (0,external_wp_i18n_namespaceObject.__)('The "%s" plugin has encountered an error and cannot be rendered.'), name));
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(fullscreen_mode, {
    isActive: isFullscreenActive
  }), (0,external_wp_element_namespaceObject.createElement)(browser_url, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.UnsavedChangesWarning, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.AutosaveMonitor, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.LocalAutosaveMonitor, null), (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcuts, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EditorKeyboardShortcutsRegister, null), (0,external_wp_element_namespaceObject.createElement)(settings_sidebar, null), (0,external_wp_element_namespaceObject.createElement)(interface_skeleton, {
    className: className,
    labels: { ...interfaceLabels,
      secondarySidebar: secondarySidebarLabel
    },
    header: (0,external_wp_element_namespaceObject.createElement)(header, {
      setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
    }),
    secondarySidebar: secondarySidebar(),
    sidebar: (!isMobileViewport || sidebarIsOpened) && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, !isMobileViewport && !sidebarIsOpened && (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__toggle-sidebar-panel"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      variant: "secondary",
      className: "edit-post-layout__toggle-sidebar-panel-button",
      onClick: openSidebarPanel,
      "aria-expanded": false
    }, hasBlockSelected ? (0,external_wp_i18n_namespaceObject.__)('Open block settings') : (0,external_wp_i18n_namespaceObject.__)('Open document settings'))), (0,external_wp_element_namespaceObject.createElement)(complementary_area.Slot, {
      scope: "core/edit-post"
    })),
    notices: (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EditorSnackbars, null),
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EditorNotices, null), (mode === 'text' || !isRichEditingEnabled) && (0,external_wp_element_namespaceObject.createElement)(TextEditor, null), isRichEditingEnabled && mode === 'visual' && (0,external_wp_element_namespaceObject.createElement)(VisualEditor, {
      styles: styles
    }), !isTemplateMode && (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__metaboxes"
    }, (0,external_wp_element_namespaceObject.createElement)(MetaBoxes, {
      location: "normal"
    }), (0,external_wp_element_namespaceObject.createElement)(MetaBoxes, {
      location: "advanced"
    })), isMobileViewport && sidebarIsOpened && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ScrollLock, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockStyles.Slot, {
      scope: "core/block-inspector"
    })),
    footer: !hasReducedUI && showBlockBreadcrumbs && !isMobileViewport && isRichEditingEnabled && mode === 'visual' && (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__footer"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockBreadcrumb, {
      rootLabelText: documentLabel
    })),
    actions: (0,external_wp_element_namespaceObject.createElement)(ActionsPanel, {
      closeEntitiesSavedStates: closeEntitiesSavedStates,
      isEntitiesSavedStatesOpen: entitiesSavedStatesCallback,
      setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
    }),
    shortcuts: {
      previous: previousShortcut,
      next: nextShortcut
    }
  }), (0,external_wp_element_namespaceObject.createElement)(EditPostPreferencesModal, null), (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcut_help_modal, null), (0,external_wp_element_namespaceObject.createElement)(WelcomeGuide, null), (0,external_wp_element_namespaceObject.createElement)(StartPageOptions, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Popover.Slot, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_plugins_namespaceObject.PluginArea, {
    onError: onPluginAreaError
  }));
}

/* harmony default export */ var components_layout = (Layout);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/listener-hooks.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/**
 * This listener hook monitors for block selection and triggers the appropriate
 * sidebar state.
 *
 * @param {number} postId The current post id.
 */

const useBlockSelectionListener = postId => {
  const {
    hasBlockSelection,
    isEditorSidebarOpened
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    hasBlockSelection: !!select(external_wp_blockEditor_namespaceObject.store).getBlockSelectionStart(),
    isEditorSidebarOpened: select(constants_STORE_NAME).isEditorSidebarOpened()
  }), [postId]);
  const {
    openGeneralSidebar
  } = (0,external_wp_data_namespaceObject.useDispatch)(constants_STORE_NAME);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!isEditorSidebarOpened) {
      return;
    }

    if (hasBlockSelection) {
      openGeneralSidebar('edit-post/block');
    } else {
      openGeneralSidebar('edit-post/document');
    }
  }, [hasBlockSelection, isEditorSidebarOpened]);
};
/**
 * This listener hook monitors any change in permalink and updates the view
 * post link in the admin bar.
 *
 * @param {number} postId
 */

const useUpdatePostLinkListener = postId => {
  const {
    newPermalink
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    newPermalink: select(external_wp_editor_namespaceObject.store).getCurrentPost().link
  }), [postId]);
  const nodeToUpdate = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    nodeToUpdate.current = document.querySelector(VIEW_AS_PREVIEW_LINK_SELECTOR) || document.querySelector(VIEW_AS_LINK_SELECTOR);
  }, [postId]);
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
 * @param {number} postId The id of the post.
 * @return {null} This is a data component so does not render any ui.
 */

function EditorInitialization(_ref) {
  let {
    postId
  } = _ref;
  useBlockSelectionListener(postId);
  useUpdatePostLinkListener(postId);
  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/editor.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */





function Editor(_ref) {
  let {
    postId,
    postType,
    settings,
    initialEdits,
    onError,
    ...props
  } = _ref;
  const {
    hasFixedToolbar,
    focusMode,
    hasReducedUI,
    hasInlineToolbar,
    hasThemeStyles,
    post,
    preferredStyleVariations,
    hiddenBlockTypes,
    blockTypes,
    keepCaretInsideBlock,
    isTemplateMode,
    template
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getPostType$viewable, _getPostType;

    const {
      isFeatureActive,
      __experimentalGetPreviewDeviceType,
      isEditingTemplate,
      getEditedPostTemplate,
      getHiddenBlockTypes
    } = select(store_store);
    const {
      getEntityRecord,
      getPostType,
      getEntityRecords,
      canUser
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);
    const {
      getBlockTypes
    } = select(external_wp_blocks_namespaceObject.store);
    const isTemplate = ['wp_template', 'wp_template_part'].includes(postType); // Ideally the initializeEditor function should be called using the ID of the REST endpoint.
    // to avoid the special case.

    let postObject;

    if (isTemplate) {
      const posts = getEntityRecords('postType', postType, {
        wp_id: postId
      });
      postObject = posts === null || posts === void 0 ? void 0 : posts[0];
    } else {
      postObject = getEntityRecord('postType', postType, postId);
    }

    const supportsTemplateMode = getEditorSettings().supportsTemplateMode;
    const isViewable = (_getPostType$viewable = (_getPostType = getPostType(postType)) === null || _getPostType === void 0 ? void 0 : _getPostType.viewable) !== null && _getPostType$viewable !== void 0 ? _getPostType$viewable : false;
    const canEditTemplate = canUser('create', 'templates');
    return {
      hasFixedToolbar: isFeatureActive('fixedToolbar') || __experimentalGetPreviewDeviceType() !== 'Desktop',
      focusMode: isFeatureActive('focusMode'),
      hasReducedUI: isFeatureActive('reducedUI'),
      hasInlineToolbar: isFeatureActive('inlineToolbar'),
      hasThemeStyles: isFeatureActive('themeStyles'),
      preferredStyleVariations: select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'preferredStyleVariations'),
      hiddenBlockTypes: getHiddenBlockTypes(),
      blockTypes: getBlockTypes(),
      keepCaretInsideBlock: isFeatureActive('keepCaretInsideBlock'),
      isTemplateMode: isEditingTemplate(),
      template: supportsTemplateMode && isViewable && canEditTemplate ? getEditedPostTemplate() : null,
      post: postObject
    };
  }, [postType, postId]);
  const {
    updatePreferredStyleVariations,
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const editorSettings = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const result = { ...settings,
      __experimentalPreferredStyleVariations: {
        value: preferredStyleVariations,
        onChange: updatePreferredStyleVariations
      },
      hasFixedToolbar,
      focusMode,
      hasReducedUI,
      hasInlineToolbar,
      // This is marked as experimental to give time for the quick inserter to mature.
      __experimentalSetIsInserterOpened: setIsInserterOpened,
      keepCaretInsideBlock,
      // Keep a reference of the `allowedBlockTypes` from the server to handle use cases
      // where we need to differentiate if a block is disabled by the user or some plugin.
      defaultAllowedBlockTypes: settings.allowedBlockTypes
    }; // Omit hidden block types if exists and non-empty.

    if (hiddenBlockTypes.length > 0) {
      // Defer to passed setting for `allowedBlockTypes` if provided as
      // anything other than `true` (where `true` is equivalent to allow
      // all block types).
      const defaultAllowedBlockTypes = true === settings.allowedBlockTypes ? (0,external_lodash_namespaceObject.map)(blockTypes, 'name') : settings.allowedBlockTypes || [];
      result.allowedBlockTypes = (0,external_lodash_namespaceObject.without)(defaultAllowedBlockTypes, ...hiddenBlockTypes);
    }

    return result;
  }, [settings, hasFixedToolbar, focusMode, hasReducedUI, hiddenBlockTypes, blockTypes, preferredStyleVariations, setIsInserterOpened, updatePreferredStyleVariations, keepCaretInsideBlock]);
  const styles = (0,external_wp_element_namespaceObject.useMemo)(() => {
    var _settings$styles;

    const themeStyles = [];
    const presetStyles = [];
    (_settings$styles = settings.styles) === null || _settings$styles === void 0 ? void 0 : _settings$styles.forEach(style => {
      if (!style.__unstableType || style.__unstableType === 'theme') {
        themeStyles.push(style);
      } else {
        presetStyles.push(style);
      }
    });
    const defaultEditorStyles = [...settings.defaultEditorStyles, ...presetStyles];
    return hasThemeStyles && themeStyles.length ? settings.styles : defaultEditorStyles;
  }, [settings, hasThemeStyles]);

  if (!post) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.StrictMode, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_keyboardShortcuts_namespaceObject.ShortcutProvider, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SlotFillProvider, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EditorProvider, _extends({
    settings: editorSettings,
    post: post,
    initialEdits: initialEdits,
    useSubRegistry: false,
    __unstableTemplate: isTemplateMode ? template : undefined
  }, props), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.ErrorBoundary, {
    onError: onError
  }, (0,external_wp_element_namespaceObject.createElement)(EditorInitialization, {
    postId: postId
  }), (0,external_wp_element_namespaceObject.createElement)(components_layout, {
    styles: styles
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostLockedModal, null)))));
}

/* harmony default export */ var editor = (Editor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-item.js


/**
 * WordPress dependencies
 */




const isEverySelectedBlockAllowed = (selected, allowed) => selected.filter(id => !allowed.includes(id)).length === 0;
/**
 * Plugins may want to add an item to the menu either for every block
 * or only for the specific ones provided in the `allowedBlocks` component property.
 *
 * If there are multiple blocks selected the item will be rendered if every block
 * is of one allowed type (not necessarily the same).
 *
 * @param {string[]} selectedBlocks Array containing the names of the blocks selected
 * @param {string[]} allowedBlocks  Array containing the names of the blocks allowed
 * @return {boolean} Whether the item will be rendered or not.
 */


const shouldRenderItem = (selectedBlocks, allowedBlocks) => !Array.isArray(allowedBlocks) || isEverySelectedBlockAllowed(selectedBlocks, allowedBlocks);
/**
 * Renders a new item in the block settings menu.
 *
 * @param {Object}                props                 Component props.
 * @param {Array}                 [props.allowedBlocks] An array containing a list of block names for which the item should be shown. If not present, it'll be rendered for any block. If multiple blocks are selected, it'll be shown if and only if all of them are in the allowed list.
 * @param {WPBlockTypeIconRender} [props.icon]          The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element.
 * @param {string}                props.label           The menu item text.
 * @param {Function}              props.onClick         Callback function to be executed when the user click the menu item.
 * @param {boolean}               [props.small]         Whether to render the label or not.
 * @param {string}                [props.role]          The ARIA role for the menu item.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginBlockSettingsMenuItem = wp.editPost.PluginBlockSettingsMenuItem;
 *
 * function doOnClick(){
 * 	// To be called when the user clicks the menu item.
 * }
 *
 * function MyPluginBlockSettingsMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginBlockSettingsMenuItem,
 * 		{
 * 			allowedBlocks: [ 'core/paragraph' ],
 * 			icon: 'dashicon-name',
 * 			label: __( 'Menu item text' ),
 * 			onClick: doOnClick,
 * 		}
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginBlockSettingsMenuItem } from '@wordpress/edit-post';
 *
 * const doOnClick = ( ) => {
 *     // To be called when the user clicks the menu item.
 * };
 *
 * const MyPluginBlockSettingsMenuItem = () => (
 *     <PluginBlockSettingsMenuItem
 * 		allowedBlocks={ [ 'core/paragraph' ] }
 * 		icon='dashicon-name'
 * 		label={ __( 'Menu item text' ) }
 * 		onClick={ doOnClick } />
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


const PluginBlockSettingsMenuItem = _ref => {
  let {
    allowedBlocks,
    icon,
    label,
    onClick,
    small,
    role
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockSettingsMenuControls, null, _ref2 => {
    let {
      selectedBlocks,
      onClose
    } = _ref2;

    if (!shouldRenderItem(selectedBlocks, allowedBlocks)) {
      return null;
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      onClick: (0,external_wp_compose_namespaceObject.compose)(onClick, onClose),
      icon: icon,
      label: small ? label : undefined,
      role: role
    }, !small && label);
  });
};

/* harmony default export */ var plugin_block_settings_menu_item = (PluginBlockSettingsMenuItem);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-more-menu-item/index.js
/**
 * WordPress dependencies
 */




/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down, and can be used to as a button or link depending on the props provided.
 * The text within the component appears as the menu item label.
 *
 * @param {Object}                props                                 Component properties.
 * @param {string}                [props.href]                          When `href` is provided then the menu item is represented as an anchor rather than button. It corresponds to the `href` attribute of the anchor.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered to the left of the menu item label.
 * @param {Function}              [props.onClick=noop]                  The callback function to be executed when the user clicks the menu item.
 * @param {...*}                  [props.other]                         Any additional props are passed through to the underlying [MenuItem](https://github.com/WordPress/gutenberg/tree/HEAD/packages/components/src/menu-item/README.md) component.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginMoreMenuItem = wp.editPost.PluginMoreMenuItem;
 * var moreIcon = wp.element.createElement( 'svg' ); //... svg element.
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * function MyButtonMoreMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginMoreMenuItem,
 * 		{
 * 			icon: moreIcon,
 * 			onClick: onButtonClick,
 * 		},
 * 		__( 'My button title' )
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginMoreMenuItem } from '@wordpress/edit-post';
 * import { more } from '@wordpress/icons';
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * const MyButtonMoreMenuItem = () => (
 * 	<PluginMoreMenuItem
 * 		icon={ more }
 * 		onClick={ onButtonClick }
 * 	>
 * 		{ __( 'My button title' ) }
 * 	</PluginMoreMenuItem>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */

/* harmony default export */ var plugin_more_menu_item = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  var _ownProps$as;

  return {
    as: (_ownProps$as = ownProps.as) !== null && _ownProps$as !== void 0 ? _ownProps$as : external_wp_components_namespaceObject.MenuItem,
    icon: ownProps.icon || context.icon,
    name: 'core/edit-post/plugin-more-menu'
  };
}))(action_item));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-sidebar-more-menu-item/index.js



/**
 * WordPress dependencies
 */

/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down,
 * and can be used to activate the corresponding `PluginSidebar` component.
 * The text within the component appears as the menu item label.
 *
 * @param {Object}                props                                 Component props.
 * @param {string}                props.target                          A string identifying the target sidebar you wish to be activated by this menu item. Must be the same as the `name` prop you have given to that sidebar.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered to the left of the menu item label.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
 * var moreIcon = wp.element.createElement( 'svg' ); //... svg element.
 *
 * function MySidebarMoreMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginSidebarMoreMenuItem,
 * 		{
 * 			target: 'my-sidebar',
 * 			icon: moreIcon,
 * 		},
 * 		__( 'My sidebar title' )
 * 	)
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
 * import { more } from '@wordpress/icons';
 *
 * const MySidebarMoreMenuItem = () => (
 * 	<PluginSidebarMoreMenuItem
 * 		target="my-sidebar"
 * 		icon={ more }
 * 	>
 * 		{ __( 'My sidebar title' ) }
 * 	</PluginSidebarMoreMenuItem>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */

function PluginSidebarMoreMenuItem(props) {
  return (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaMoreMenuItem // Menu item is marked with unstable prop for backward compatibility.
  // @see https://github.com/WordPress/gutenberg/issues/14457
  , _extends({
    __unstableExplicitMenuItem: true,
    scope: "core/edit-post"
  }, props));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */





/**
 * Reinitializes the editor after the user chooses to reboot the editor after
 * an unhandled error occurs, replacing previously mounted editor element using
 * an initial state from prior to the crash.
 *
 * @param {Object}  postType     Post type of the post to edit.
 * @param {Object}  postId       ID of the post to edit.
 * @param {Element} target       DOM node in which editor is rendered.
 * @param {?Object} settings     Editor settings object.
 * @param {Object}  initialEdits Programmatic edits to apply initially, to be
 *                               considered as non-user-initiated (bypass for
 *                               unsaved changes prompt).
 */

function reinitializeEditor(postType, postId, target, settings, initialEdits) {
  (0,external_wp_element_namespaceObject.unmountComponentAtNode)(target);
  const reboot = reinitializeEditor.bind(null, postType, postId, target, settings, initialEdits);
  (0,external_wp_element_namespaceObject.render)((0,external_wp_element_namespaceObject.createElement)(editor, {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits,
    recovery: true
  }), target);
}
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
  const target = document.getElementById(id);
  const reboot = reinitializeEditor.bind(null, postType, postId, target, settings, initialEdits);
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults('core/edit-post', {
    editorMode: 'visual',
    fixedToolbar: false,
    fullscreenMode: true,
    hiddenBlockTypes: [],
    inactivePanels: [],
    isPublishSidebarEnabled: true,
    openPanels: ['post-status'],
    preferredStyleVariations: {},
    showBlockBreadcrumbs: true,
    showIconLabels: false,
    showListViewByDefault: false,
    themeStyles: true,
    welcomeGuide: true,
    welcomeGuideTemplate: true
  });

  (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).__experimentalReapplyBlockTypeFilters(); // Check if the block list view should be open by default.


  if ((0,external_wp_data_namespaceObject.select)(store_store).isFeatureActive('showListViewByDefault')) {
    (0,external_wp_data_namespaceObject.dispatch)(store_store).setIsListViewOpened(true);
  }

  (0,external_wp_blockLibrary_namespaceObject.registerCoreBlocks)();

  if (false) {}
  /*
   * Prevent adding template part in the post editor.
   * Only add the filter when the post editor is initialized, not imported.
   * Also only add the filter(s) after registerCoreBlocks()
   * so that common filters in the block library are not overwritten.
   */


  (0,external_wp_hooks_namespaceObject.addFilter)('blockEditor.__unstableCanInsertBlockType', 'removeTemplatePartsFromInserter', (canInsert, blockType) => {
    if (!(0,external_wp_data_namespaceObject.select)(store_store).isEditingTemplate() && blockType.name === 'core/template-part') {
      return false;
    }

    return canInsert;
  }); // Show a console log warning if the browser is not in Standards rendering mode.

  const documentMode = document.compatMode === 'CSS1Compat' ? 'Standards' : 'Quirks';

  if (documentMode !== 'Standards') {
    // eslint-disable-next-line no-console
    console.warn("Your browser is using Quirks Mode. \nThis can cause rendering issues such as blocks overlaying meta boxes in the editor. Quirks Mode can be triggered by PHP errors or HTML code appearing before the opening <!DOCTYPE html>. Try checking the raw page source or your site's PHP error log and resolving errors there, removing any HTML before the doctype, or disabling plugins.");
  } // This is a temporary fix for a couple of issues specific to Webkit on iOS.
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
        } // Undo unwanted scroll on html element, but only in the visual editor.


        if (document.getElementsByClassName('is-mode-visual')[0]) {
          window.scrollTo(0, 0);
        }
      }
    });
  } // Prevent the default browser action for files dropped outside of dropzones.


  window.addEventListener('dragover', e => e.preventDefault(), false);
  window.addEventListener('drop', e => e.preventDefault(), false);
  (0,external_wp_element_namespaceObject.render)((0,external_wp_element_namespaceObject.createElement)(editor, {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits
  }), target);
}












}();
(window.wp = window.wp || {}).editPost = __webpack_exports__;
/******/ })()
;