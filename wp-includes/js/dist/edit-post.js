<<<<<<< HEAD
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 4403:
/***/ ((module, exports) => {

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
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  PluginBlockSettingsMenuItem: () => (/* reexport */ plugin_block_settings_menu_item),
  PluginDocumentSettingPanel: () => (/* reexport */ plugin_document_setting_panel),
  PluginMoreMenuItem: () => (/* reexport */ plugin_more_menu_item),
  PluginPostPublishPanel: () => (/* reexport */ plugin_post_publish_panel),
  PluginPostStatusInfo: () => (/* reexport */ plugin_post_status_info),
  PluginPrePublishPanel: () => (/* reexport */ plugin_pre_publish_panel),
  PluginSidebar: () => (/* reexport */ PluginSidebarEditPost),
  PluginSidebarMoreMenuItem: () => (/* reexport */ PluginSidebarMoreMenuItem),
  __experimentalFullscreenModeClose: () => (/* reexport */ fullscreen_mode_close),
  __experimentalMainDashboardButton: () => (/* reexport */ main_dashboard_button),
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

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
var store_actions_namespaceObject = {};
__webpack_require__.r(store_actions_namespaceObject);
__webpack_require__.d(store_actions_namespaceObject, {
  __experimentalSetPreviewDeviceType: () => (__experimentalSetPreviewDeviceType),
  __unstableCreateTemplate: () => (__unstableCreateTemplate),
  __unstableSwitchToTemplateMode: () => (__unstableSwitchToTemplateMode),
  closeGeneralSidebar: () => (closeGeneralSidebar),
  closeModal: () => (actions_closeModal),
  closePublishSidebar: () => (closePublishSidebar),
  hideBlockTypes: () => (hideBlockTypes),
  initializeMetaBoxes: () => (initializeMetaBoxes),
  metaBoxUpdatesFailure: () => (metaBoxUpdatesFailure),
  metaBoxUpdatesSuccess: () => (metaBoxUpdatesSuccess),
  openGeneralSidebar: () => (openGeneralSidebar),
  openModal: () => (actions_openModal),
  openPublishSidebar: () => (openPublishSidebar),
  removeEditorPanel: () => (removeEditorPanel),
  requestMetaBoxUpdates: () => (requestMetaBoxUpdates),
  setAvailableMetaBoxesPerLocation: () => (setAvailableMetaBoxesPerLocation),
  setIsEditingTemplate: () => (setIsEditingTemplate),
  setIsInserterOpened: () => (setIsInserterOpened),
  setIsListViewOpened: () => (setIsListViewOpened),
  showBlockTypes: () => (showBlockTypes),
  switchEditorMode: () => (switchEditorMode),
  toggleEditorPanelEnabled: () => (toggleEditorPanelEnabled),
  toggleEditorPanelOpened: () => (toggleEditorPanelOpened),
  toggleFeature: () => (actions_toggleFeature),
  togglePinnedPluginItem: () => (togglePinnedPluginItem),
  togglePublishSidebar: () => (togglePublishSidebar),
  updatePreferredStyleVariations: () => (updatePreferredStyleVariations)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
var store_selectors_namespaceObject = {};
__webpack_require__.r(store_selectors_namespaceObject);
__webpack_require__.d(store_selectors_namespaceObject, {
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
  isEditingTemplate: () => (selectors_isEditingTemplate),
  isEditorPanelEnabled: () => (isEditorPanelEnabled),
  isEditorPanelOpened: () => (isEditorPanelOpened),
  isEditorPanelRemoved: () => (isEditorPanelRemoved),
  isEditorSidebarOpened: () => (isEditorSidebarOpened),
  isFeatureActive: () => (selectors_isFeatureActive),
  isInserterOpened: () => (isInserterOpened),
  isListViewOpened: () => (isListViewOpened),
  isMetaBoxLocationActive: () => (isMetaBoxLocationActive),
  isMetaBoxLocationVisible: () => (isMetaBoxLocationVisible),
  isModalActive: () => (selectors_isModalActive),
  isPluginItemPinned: () => (isPluginItemPinned),
  isPluginSidebarOpened: () => (isPluginSidebarOpened),
  isPublishSidebarOpened: () => (isPublishSidebarOpened),
  isSavingMetaBoxes: () => (selectors_isSavingMetaBoxes)
});

;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","blockLibrary"]
const external_wp_blockLibrary_namespaceObject = window["wp"]["blockLibrary"];
;// CONCATENATED MODULE: external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: external ["wp","preferences"]
const external_wp_preferences_namespaceObject = window["wp"]["preferences"];
;// CONCATENATED MODULE: external ["wp","widgets"]
const external_wp_widgets_namespaceObject = window["wp"]["widgets"];
;// CONCATENATED MODULE: external ["wp","mediaUtils"]
const external_wp_mediaUtils_namespaceObject = window["wp"]["mediaUtils"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/components/index.js
/**
 * WordPress dependencies
 */



const replaceMediaUpload = () => external_wp_mediaUtils_namespaceObject.MediaUpload;

(0,external_wp_hooks_namespaceObject.addFilter)('editor.MediaUpload', 'core/edit-post/replace-media-upload', replaceMediaUpload);

;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/validate-multiple-use/index.js


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
  const firstOfSameType = blocks.find(({
    name
  }) => block.name === name);
  const isInvalid = firstOfSameType && firstOfSameType.clientId !== block.clientId;
  return {
    originalBlockClientId: isInvalid && firstOfSameType.clientId
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  originalBlockClientId
}) => ({
  selectFirst: () => dispatch(external_wp_blockEditor_namespaceObject.store).selectBlock(originalBlockClientId)
})));
const withMultipleValidation = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => {
  return enhance(({
    originalBlockClientId,
    selectFirst,
    ...props
  }) => {
    if (!originalBlockClientId) {
      return (0,external_wp_element_namespaceObject.createElement)(BlockEdit, { ...props
      });
    }

    const blockType = (0,external_wp_blocks_namespaceObject.getBlockType)(props.name);
    const outboundType = getOutboundType(props.name);
    return [(0,external_wp_element_namespaceObject.createElement)("div", {
      key: "invalid-preview",
      style: {
        minHeight: '60px'
      }
    }, (0,external_wp_element_namespaceObject.createElement)(BlockEdit, {
      key: "block-edit",
      ...props
    })), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.Warning, {
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
    }, (0,external_wp_element_namespaceObject.createElement)("strong", null, blockType?.title, ": "), (0,external_wp_i18n_namespaceObject.__)('This block can only be used once.'))];
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
  const transform = (0,external_wp_blocks_namespaceObject.findTransform)((0,external_wp_blocks_namespaceObject.getBlockTransforms)('to', blockName), ({
    type,
    blocks
  }) => type === 'block' && blocks.length === 1 // What about when .length > 1?
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



;// CONCATENATED MODULE: external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// CONCATENATED MODULE: external ["wp","editor"]
const external_wp_editor_namespaceObject = window["wp"]["editor"];
;// CONCATENATED MODULE: external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/external.js


/**
 * WordPress dependencies
 */

const external = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"
}));
/* harmony default export */ const library_external = (external);

;// CONCATENATED MODULE: external ["wp","plugins"]
const external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// CONCATENATED MODULE: external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
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
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
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
/* harmony default export */ const library_check = (check);

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
/* harmony default export */ const star_filled = (starFilled);

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
/* harmony default export */ const star_empty = (starEmpty);

;// CONCATENATED MODULE: external ["wp","viewport"]
const external_wp_viewport_namespaceObject = window["wp"]["viewport"];
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
/* harmony default export */ const close_small = (closeSmall);

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

const enableComplementaryArea = (scope, area) => ({
  registry,
  dispatch
}) => {
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

const disableComplementaryArea = scope => ({
  registry
}) => {
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

const pinItem = (scope, item) => ({
  registry
}) => {
  // Return early if there's no item.
  if (!item) {
    return;
  }

  const pinnedItems = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems'); // The item is already pinned, there's nothing to do.

  if (pinnedItems?.[item] === true) {
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

const unpinItem = (scope, item) => ({
  registry
}) => {
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
  return function ({
    registry
  }) {
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
  return function ({
    registry
  }) {
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
  return function ({
    registry
  }) {
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).setFeatureDefaults`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).setDefaults`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).setDefaults(scope, defaults);
  };
}
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
  const isComplementaryAreaVisible = select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible'); // Return `undefined` to indicate that the user has never toggled
  // visibility, this is the vanilla default. Other code relies on this
  // nuance in the return value.

  if (isComplementaryAreaVisible === undefined) {
    return undefined;
  } // Return `null` to indicate the user hid the complementary area.


  if (isComplementaryAreaVisible === false) {
    return null;
  }

  return state?.complementaryAreas?.[scope];
});
const isComplementaryAreaLoading = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope) => {
  const isVisible = select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');
  const identifier = state?.complementaryAreas?.[scope];
  return isVisible && identifier === undefined;
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
  return (_pinnedItems$item = pinnedItems?.[item]) !== null && _pinnedItems$item !== void 0 ? _pinnedItems$item : true;
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

function complementaryAreas(state = {}, action) {
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
/**
 * Reducer for storing the name of the open modal, or null if no modal is open.
 *
 * @param {Object} state  Previous state.
 * @param {Object} action Action object containing the `name` of the modal
 *
 * @return {Object} Updated state
 */

function activeModal(state = null, action) {
  switch (action.type) {
    case 'OPEN_MODAL':
      return action.name;

    case 'CLOSE_MODAL':
      return null;
  }

  return state;
}
/* harmony default export */ const reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  complementaryAreas,
  activeModal
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
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-context/index.js
/**
 * WordPress dependencies
 */

/* harmony default export */ const complementary_area_context = ((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
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




function ComplementaryAreaToggle({
  as = external_wp_components_namespaceObject.Button,
  scope,
  identifier,
  icon,
  selectedIcon,
  name,
  ...props
}) {
  const ComponentToUse = as;
  const isSelected = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getActiveComplementaryArea(scope) === identifier, [identifier]);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return (0,external_wp_element_namespaceObject.createElement)(ComponentToUse, {
    icon: selectedIcon && isSelected ? selectedIcon : icon,
    onClick: () => {
      if (isSelected) {
        disableComplementaryArea(scope);
      } else {
        enableComplementaryArea(scope, identifier);
      }
    },
    ...props
  });
}

/* harmony default export */ const complementary_area_toggle = (complementary_area_context(ComplementaryAreaToggle));

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



const ComplementaryAreaHeader = ({
  smallScreenTitle,
  children,
  className,
  toggleButtonProps
}) => {
  const toggleButton = (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, {
    icon: close_small,
    ...toggleButtonProps
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "components-panel__header interface-complementary-area-header__small"
  }, smallScreenTitle && (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "interface-complementary-area-header__small-title"
  }, smallScreenTitle), toggleButton), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('components-panel__header', 'interface-complementary-area-header', className),
    tabIndex: -1
  }, children, toggleButton));
};

/* harmony default export */ const complementary_area_header = (ComplementaryAreaHeader);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/action-item/index.js


/**
 * WordPress dependencies
 */



const noop = () => {};

function ActionItemSlot({
  name,
  as: Component = external_wp_components_namespaceObject.ButtonGroup,
  fillProps = {},
  bubblesVirtually,
  ...props
}) {
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
    external_wp_element_namespaceObject.Children.forEach(fills, ({
      props: {
        __unstableExplicitMenuItem,
        __unstableTarget
      }
    }) => {
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
    return (0,external_wp_element_namespaceObject.createElement)(Component, { ...props
    }, children);
  });
}

function ActionItem({
  name,
  as: Component = external_wp_components_namespaceObject.Button,
  onClick,
  ...props
}) {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, {
    name: name
  }, ({
    onClick: fpOnClick
  }) => {
    return (0,external_wp_element_namespaceObject.createElement)(Component, {
      onClick: onClick || fpOnClick ? (...args) => {
        (onClick || noop)(...args);
        (fpOnClick || noop)(...args);
      } : undefined,
      ...props
    });
  });
}

ActionItem.Slot = ActionItemSlot;
/* harmony default export */ const action_item = (ActionItem);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-more-menu-item/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const PluginsMenuItem = ({
  // Menu item is marked with unstable prop for backward compatibility.
  // They are removed so they don't leak to DOM elements.
  // @see https://github.com/WordPress/gutenberg/issues/14457
  __unstableExplicitMenuItem,
  __unstableTarget,
  ...restProps
}) => (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, { ...restProps
});

function ComplementaryAreaMoreMenuItem({
  scope,
  target,
  __unstableExplicitMenuItem,
  ...props
}) {
  return (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, {
    as: toggleProps => {
      return (0,external_wp_element_namespaceObject.createElement)(action_item, {
        __unstableExplicitMenuItem: __unstableExplicitMenuItem,
        __unstableTarget: `${scope}/${target}`,
        as: PluginsMenuItem,
        name: `${scope}/plugin-more-menu`,
        ...toggleProps
      });
    },
    role: "menuitemcheckbox",
    selectedIcon: library_check,
    name: target,
    scope: scope,
    ...props
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/pinned-items/index.js


/**
 * External dependencies
=======
this["wp"] = this["wp"] || {}; this["wp"]["editPost"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "dSQ2");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1OyB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ "25BE":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

/***/ }),

/***/ "BsWD":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a3WO");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ "DSFK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ "Ff2n":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _objectWithoutProperties; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = _objectWithoutPropertiesLoose(source, excluded);
  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "HSyU":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ "JX7q":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _assertThisInitialized; });
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

/***/ }),

/***/ "Ji7U":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _inherits; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) _setPrototypeOf(subClass, superClass);
}

/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ "KEfo":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["viewport"]; }());

/***/ }),

/***/ "KQm4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
var arrayLikeToArray = __webpack_require__("a3WO");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return Object(arrayLikeToArray["a" /* default */])(arr);
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__("25BE");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__("BsWD");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || Object(unsupportedIterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ "Mmq9":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ "ODXe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _slicedToArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__("DSFK");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
  if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
  var _arr = [];
  var _n = true;
  var _d = false;
  var _e = undefined;

  try {
    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);

      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }

  return _arr;
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__("BsWD");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__("PYwp");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(unsupportedIterableToArray["a" /* default */])(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ "PYwp":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ "QyPg":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockLibrary"]; }());

/***/ }),

/***/ "RxS6":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),

/***/ "TSYQ":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2018 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

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
				if (arg.toString === Object.prototype.toString) {
					for (var key in arg) {
						if (hasOwn.call(arg, key) && arg[key]) {
							classes.push(key);
						}
					}
				} else {
					classes.push(arg.toString());
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


/***/ }),

/***/ "TvNi":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["plugins"]; }());

/***/ }),

/***/ "U8pU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ "ZU7w":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["nux"]; }());

/***/ }),

/***/ "a3WO":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayLikeToArray; });
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

/***/ }),

/***/ "dSQ2":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "reinitializeEditor", function() { return /* binding */ reinitializeEditor; });
__webpack_require__.d(__webpack_exports__, "initializeEditor", function() { return /* binding */ initializeEditor; });
__webpack_require__.d(__webpack_exports__, "PluginBlockSettingsMenuItem", function() { return /* reexport */ plugin_block_settings_menu_item; });
__webpack_require__.d(__webpack_exports__, "PluginMoreMenuItem", function() { return /* reexport */ plugin_more_menu_item; });
__webpack_require__.d(__webpack_exports__, "PluginPostPublishPanel", function() { return /* reexport */ plugin_post_publish_panel; });
__webpack_require__.d(__webpack_exports__, "PluginPostStatusInfo", function() { return /* reexport */ plugin_post_status_info; });
__webpack_require__.d(__webpack_exports__, "PluginPrePublishPanel", function() { return /* reexport */ plugin_pre_publish_panel; });
__webpack_require__.d(__webpack_exports__, "PluginSidebar", function() { return /* reexport */ plugin_sidebar; });
__webpack_require__.d(__webpack_exports__, "PluginSidebarMoreMenuItem", function() { return /* reexport */ plugin_sidebar_more_menu_item; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "openGeneralSidebar", function() { return actions_openGeneralSidebar; });
__webpack_require__.d(actions_namespaceObject, "closeGeneralSidebar", function() { return actions_closeGeneralSidebar; });
__webpack_require__.d(actions_namespaceObject, "openModal", function() { return actions_openModal; });
__webpack_require__.d(actions_namespaceObject, "closeModal", function() { return actions_closeModal; });
__webpack_require__.d(actions_namespaceObject, "openPublishSidebar", function() { return openPublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "closePublishSidebar", function() { return actions_closePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "togglePublishSidebar", function() { return actions_togglePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "toggleEditorPanelEnabled", function() { return toggleEditorPanelEnabled; });
__webpack_require__.d(actions_namespaceObject, "toggleEditorPanelOpened", function() { return actions_toggleEditorPanelOpened; });
__webpack_require__.d(actions_namespaceObject, "removeEditorPanel", function() { return removeEditorPanel; });
__webpack_require__.d(actions_namespaceObject, "toggleFeature", function() { return toggleFeature; });
__webpack_require__.d(actions_namespaceObject, "switchEditorMode", function() { return switchEditorMode; });
__webpack_require__.d(actions_namespaceObject, "togglePinnedPluginItem", function() { return togglePinnedPluginItem; });
__webpack_require__.d(actions_namespaceObject, "setAvailableMetaBoxesPerLocation", function() { return setAvailableMetaBoxesPerLocation; });
__webpack_require__.d(actions_namespaceObject, "requestMetaBoxUpdates", function() { return requestMetaBoxUpdates; });
__webpack_require__.d(actions_namespaceObject, "metaBoxUpdatesSuccess", function() { return metaBoxUpdatesSuccess; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getEditorMode", function() { return getEditorMode; });
__webpack_require__.d(selectors_namespaceObject, "isEditorSidebarOpened", function() { return selectors_isEditorSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "isPluginSidebarOpened", function() { return isPluginSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "getActiveGeneralSidebarName", function() { return getActiveGeneralSidebarName; });
__webpack_require__.d(selectors_namespaceObject, "getPreferences", function() { return getPreferences; });
__webpack_require__.d(selectors_namespaceObject, "getPreference", function() { return getPreference; });
__webpack_require__.d(selectors_namespaceObject, "isPublishSidebarOpened", function() { return selectors_isPublishSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelRemoved", function() { return isEditorPanelRemoved; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelEnabled", function() { return selectors_isEditorPanelEnabled; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelOpened", function() { return selectors_isEditorPanelOpened; });
__webpack_require__.d(selectors_namespaceObject, "isModalActive", function() { return selectors_isModalActive; });
__webpack_require__.d(selectors_namespaceObject, "isFeatureActive", function() { return isFeatureActive; });
__webpack_require__.d(selectors_namespaceObject, "isPluginItemPinned", function() { return isPluginItemPinned; });
__webpack_require__.d(selectors_namespaceObject, "getActiveMetaBoxLocations", function() { return getActiveMetaBoxLocations; });
__webpack_require__.d(selectors_namespaceObject, "isMetaBoxLocationVisible", function() { return isMetaBoxLocationVisible; });
__webpack_require__.d(selectors_namespaceObject, "isMetaBoxLocationActive", function() { return isMetaBoxLocationActive; });
__webpack_require__.d(selectors_namespaceObject, "getMetaBoxesPerLocation", function() { return getMetaBoxesPerLocation; });
__webpack_require__.d(selectors_namespaceObject, "getAllMetaBoxes", function() { return getAllMetaBoxes; });
__webpack_require__.d(selectors_namespaceObject, "hasMetaBoxes", function() { return hasMetaBoxes; });
__webpack_require__.d(selectors_namespaceObject, "isSavingMetaBoxes", function() { return selectors_isSavingMetaBoxes; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external {"this":["wp","coreData"]}
var external_this_wp_coreData_ = __webpack_require__("jZUy");

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__("jSdM");

// EXTERNAL MODULE: external {"this":["wp","nux"]}
var external_this_wp_nux_ = __webpack_require__("ZU7w");

// EXTERNAL MODULE: external {"this":["wp","viewport"]}
var external_this_wp_viewport_ = __webpack_require__("KEfo");

// EXTERNAL MODULE: external {"this":["wp","notices"]}
var external_this_wp_notices_ = __webpack_require__("onLe");

// EXTERNAL MODULE: external {"this":["wp","blockLibrary"]}
var external_this_wp_blockLibrary_ = __webpack_require__("QyPg");

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__("g56x");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__("1OyB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__("vuIU");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__("md7G");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__("foSv");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__("Ji7U");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__("JX7q");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/components/media-upload/index.js







/**
 * External Dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

/**
 * WordPress dependencies
 */



<<<<<<< HEAD
function PinnedItems({
  scope,
  ...props
}) {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, {
    name: `PinnedItems/${scope}`,
    ...props
  });
}

function PinnedItemsSlot({
  scope,
  className,
  ...props
}) {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, {
    name: `PinnedItems/${scope}`,
    ...props
  }, fills => fills?.length > 0 && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()(className, 'interface-pinned-items')
  }, fills));
}

PinnedItems.Slot = PinnedItemsSlot;
/* harmony default export */ const pinned_items = (PinnedItems);

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








function ComplementaryAreaSlot({
  scope,
  ...props
}) {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, {
    name: `ComplementaryArea/${scope}`,
    ...props
  });
}

function ComplementaryAreaFill({
  scope,
  children,
  className
}) {
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
    // If the complementary area is active and the editor is switching from
    // a big to a small window size.
    if (isActive && isSmall && !previousIsSmall.current) {
      disableComplementaryArea(scope); // Flag the complementary area to be reopened when the window size
      // goes from small to big.

      shouldOpenWhenNotSmall.current = true;
    } else if ( // If there is a flag indicating the complementary area should be
    // enabled when we go from small to big window size and we are going
    // from a small to big window size.
    shouldOpenWhenNotSmall.current && !isSmall && previousIsSmall.current) {
      // Remove the flag indicating the complementary area should be
      // enabled.
      shouldOpenWhenNotSmall.current = false;
      enableComplementaryArea(scope, identifier);
    } else if ( // If the flag is indicating the current complementary should be
    // reopened but another complementary area becomes active, remove
    // the flag.
    shouldOpenWhenNotSmall.current && activeArea && activeArea !== identifier) {
      shouldOpenWhenNotSmall.current = false;
    }

    if (isSmall !== previousIsSmall.current) {
      previousIsSmall.current = isSmall;
    }
  }, [isActive, isSmall, scope, identifier, activeArea]);
}

function ComplementaryArea({
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
}) {
  const {
    isLoading,
    isActive,
    isPinned,
    activeArea,
    isSmall,
    isLarge
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getActiveComplementaryArea,
      isComplementaryAreaLoading,
      isItemPinned
    } = select(store);

    const _activeArea = getActiveComplementaryArea(scope);

    return {
      isLoading: isComplementaryAreaLoading(scope),
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
    // Set initial visibility: For large screens, enable if it's active by
    // default. For small screens, always initially disable.
    if (isActiveByDefault && activeArea === undefined && !isSmall) {
      enableComplementaryArea(scope, identifier);
    } else if (activeArea === undefined && isSmall) {
      disableComplementaryArea(scope, identifier);
    }
  }, [activeArea, isActiveByDefault, scope, identifier, isSmall]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isPinnable && (0,external_wp_element_namespaceObject.createElement)(pinned_items, {
    scope: scope
  }, isPinned && (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, {
    scope: scope,
    identifier: identifier,
    isPressed: isActive && (!showIconLabels || isLarge),
    "aria-expanded": isActive,
    "aria-disabled": isLoading,
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
/* harmony default export */ const complementary_area = (ComplementaryAreaWrapped);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/fullscreen-mode/index.js
=======
var _window = window,
    wp = _window.wp; // Getter for the sake of unit tests.

var media_upload_getGalleryDetailsMediaFrame = function getGalleryDetailsMediaFrame() {
  /**
   * Custom gallery details frame.
   *
   * @link https://github.com/xwp/wp-core-media-widgets/blob/905edbccfc2a623b73a93dac803c5335519d7837/wp-admin/js/widgets/media-gallery-widget.js
   * @class GalleryDetailsMediaFrame
   * @constructor
   */
  return wp.media.view.MediaFrame.Post.extend({
    /**
     * Create the default states.
     *
     * @return {void}
     */
    createStates: function createStates() {
      this.states.add([new wp.media.controller.Library({
        id: 'gallery',
        title: wp.media.view.l10n.createGalleryTitle,
        priority: 40,
        toolbar: 'main-gallery',
        filterable: 'uploaded',
        multiple: 'add',
        editable: false,
        library: wp.media.query(Object(external_lodash_["defaults"])({
          type: 'image'
        }, this.options.library))
      }), new wp.media.controller.GalleryEdit({
        library: this.options.selection,
        editing: this.options.editing,
        menu: 'gallery',
        displaySettings: false,
        multiple: true
      }), new wp.media.controller.GalleryAdd()]);
    }
  });
}; // the media library image object contains numerous attributes
// we only need this set to display the image in the library


var media_upload_slimImageObject = function slimImageObject(img) {
  var attrSet = ['sizes', 'mime', 'type', 'subtype', 'id', 'url', 'alt', 'link', 'caption'];
  return Object(external_lodash_["pick"])(img, attrSet);
};

var getAttachmentsCollection = function getAttachmentsCollection(ids) {
  return wp.media.query({
    order: 'ASC',
    orderby: 'post__in',
    post__in: ids,
    posts_per_page: -1,
    query: true,
    type: 'image'
  });
};

var media_upload_MediaUpload =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MediaUpload, _Component);

  function MediaUpload(_ref) {
    var _this;

    var allowedTypes = _ref.allowedTypes,
        _ref$multiple = _ref.multiple,
        multiple = _ref$multiple === void 0 ? false : _ref$multiple,
        _ref$gallery = _ref.gallery,
        gallery = _ref$gallery === void 0 ? false : _ref$gallery,
        _ref$title = _ref.title,
        title = _ref$title === void 0 ? Object(external_this_wp_i18n_["__"])('Select or Upload Media') : _ref$title,
        modalClass = _ref.modalClass,
        value = _ref.value;

    Object(classCallCheck["a" /* default */])(this, MediaUpload);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MediaUpload).apply(this, arguments));
    _this.openModal = _this.openModal.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onOpen = _this.onOpen.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelect = _this.onSelect.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onUpdate = _this.onUpdate.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onClose = _this.onClose.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));

    if (gallery) {
      var currentState = value ? 'gallery-edit' : 'gallery';
      var GalleryDetailsMediaFrame = media_upload_getGalleryDetailsMediaFrame();
      var attachments = getAttachmentsCollection(value);
      var selection = new wp.media.model.Selection(attachments.models, {
        props: attachments.props.toJSON(),
        multiple: multiple
      });
      _this.frame = new GalleryDetailsMediaFrame({
        mimeType: allowedTypes,
        state: currentState,
        multiple: multiple,
        selection: selection,
        editing: value ? true : false
      });
      wp.media.frame = _this.frame;
    } else {
      var frameConfig = {
        title: title,
        button: {
          text: Object(external_this_wp_i18n_["__"])('Select')
        },
        multiple: multiple
      };

      if (!!allowedTypes) {
        frameConfig.library = {
          type: allowedTypes
        };
      }

      _this.frame = wp.media(frameConfig);
    }

    if (modalClass) {
      _this.frame.$el.addClass(modalClass);
    } // When an image is selected in the media frame...


    _this.frame.on('select', _this.onSelect);

    _this.frame.on('update', _this.onUpdate);

    _this.frame.on('open', _this.onOpen);

    _this.frame.on('close', _this.onClose);

    return _this;
  }

  Object(createClass["a" /* default */])(MediaUpload, [{
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.frame.remove();
    }
  }, {
    key: "onUpdate",
    value: function onUpdate(selections) {
      var _this$props = this.props,
          onSelect = _this$props.onSelect,
          _this$props$multiple = _this$props.multiple,
          multiple = _this$props$multiple === void 0 ? false : _this$props$multiple;
      var state = this.frame.state();
      var selectedImages = selections || state.get('selection');

      if (!selectedImages || !selectedImages.models.length) {
        return;
      }

      if (multiple) {
        onSelect(selectedImages.models.map(function (model) {
          return media_upload_slimImageObject(model.toJSON());
        }));
      } else {
        onSelect(media_upload_slimImageObject(selectedImages.models[0].toJSON()));
      }
    }
  }, {
    key: "onSelect",
    value: function onSelect() {
      var _this$props2 = this.props,
          onSelect = _this$props2.onSelect,
          _this$props2$multiple = _this$props2.multiple,
          multiple = _this$props2$multiple === void 0 ? false : _this$props2$multiple; // Get media attachment details from the frame state

      var attachment = this.frame.state().get('selection').toJSON();
      onSelect(multiple ? attachment : attachment[0]);
    }
  }, {
    key: "onOpen",
    value: function onOpen() {
      this.updateCollection();

      if (!this.props.value) {
        return;
      }

      if (!this.props.gallery) {
        var selection = this.frame.state().get('selection');
        Object(external_lodash_["castArray"])(this.props.value).map(function (id) {
          selection.add(wp.media.attachment(id));
        });
      } // load the images so they are available in the media modal.


      getAttachmentsCollection(Object(external_lodash_["castArray"])(this.props.value)).more();
    }
  }, {
    key: "onClose",
    value: function onClose() {
      var onClose = this.props.onClose;

      if (onClose) {
        onClose();
      }
    }
  }, {
    key: "updateCollection",
    value: function updateCollection() {
      var frameContent = this.frame.content.get();

      if (frameContent && frameContent.collection) {
        var collection = frameContent.collection; // clean all attachments we have in memory.

        collection.toArray().forEach(function (model) {
          return model.trigger('destroy', model);
        }); // reset has more flag, if library had small amount of items all items may have been loaded before.

        collection.mirroring._hasMore = true; // request items

        collection.more();
      }
    }
  }, {
    key: "openModal",
    value: function openModal() {
      this.frame.open();
    }
  }, {
    key: "render",
    value: function render() {
      return this.props.render({
        open: this.openModal
      });
    }
  }]);

  return MediaUpload;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var media_upload = (media_upload_MediaUpload);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/components/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD

const FullscreenMode = ({
  isActive
}) => {
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

/* harmony default export */ const fullscreen_mode = (FullscreenMode);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/navigable-region/index.js


/**
 * External dependencies
 */

function NavigableRegion({
  children,
  className,
  ariaLabel,
  as: Tag = 'div',
  ...props
}) {
  return (0,external_wp_element_namespaceObject.createElement)(Tag, {
    className: classnames_default()('interface-navigable-region', className),
    "aria-label": ariaLabel,
    role: "region",
    tabIndex: "-1",
    ...props
  }, children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/interface-skeleton/index.js
=======
/**
 * Internal dependencies
 */



var components_replaceMediaUpload = function replaceMediaUpload() {
  return media_upload;
};

Object(external_this_wp_hooks_["addFilter"])('editor.MediaUpload', 'core/edit-post/components/media-upload/replace-media-upload', components_replaceMediaUpload);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__("Ff2n");

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__("K9lf");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/validate-multiple-use/index.js


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





<<<<<<< HEAD
/**
 * Internal dependencies
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

const headerVariants = {
  hidden: {
    opacity: 0
  },
  hover: {
    opacity: 1,
    transition: {
      type: 'tween',
      delay: 0.2,
      delayChildren: 0.2
    }
  },
  distractionFreeInactive: {
    opacity: 1,
    transition: {
      delay: 0
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
  notices,
  content,
  actions,
  labels,
  className,
  enableRegionNavigation = true,
  // Todo: does this need to be a prop.
  // Can we use a dependency to keyboard-shortcuts directly?
  shortcuts
}, ref) {
  const navigateRegionsProps = (0,external_wp_components_namespaceObject.__unstableUseNavigateRegions)(shortcuts);
  useHTMLClass('interface-interface-skeleton__html-container');
  const defaultLabels = {
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
  return (0,external_wp_element_namespaceObject.createElement)("div", { ...(enableRegionNavigation ? navigateRegionsProps : {}),
    ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([ref, enableRegionNavigation ? navigateRegionsProps.ref : undefined]),
    className: classnames_default()(className, 'interface-interface-skeleton', navigateRegionsProps.className, !!footer && 'has-footer')
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__editor"
  }, !!header && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    as: external_wp_components_namespaceObject.__unstableMotion.div,
    className: "interface-interface-skeleton__header",
    "aria-label": mergedLabels.header,
    initial: isDistractionFree ? 'hidden' : 'distractionFreeInactive',
    whileHover: isDistractionFree ? 'hover' : 'distractionFreeInactive',
    animate: isDistractionFree ? 'hidden' : 'distractionFreeInactive',
    variants: headerVariants,
    transition: isDistractionFree ? {
      type: 'tween',
      delay: 0.8
    } : undefined
  }, header), isDistractionFree && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__header"
  }, editorNotices), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__body"
  }, !!secondarySidebar && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__secondary-sidebar",
    ariaLabel: mergedLabels.secondarySidebar
  }, secondarySidebar), !!notices && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__notices"
  }, notices), (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__content",
    ariaLabel: mergedLabels.body
  }, content), !!sidebar && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__sidebar",
    ariaLabel: mergedLabels.sidebar
  }, sidebar), !!actions && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__actions",
    ariaLabel: mergedLabels.actions
  }, actions))), !!footer && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__footer",
    ariaLabel: mergedLabels.footer
  }, footer));
}

/* harmony default export */ const interface_skeleton = ((0,external_wp_element_namespaceObject.forwardRef)(InterfaceSkeleton));

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
/* harmony default export */ const more_vertical = (moreVertical);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/more-menu-dropdown/index.js


/**
 * External dependencies
 */
=======



var enhance = Object(external_this_wp_compose_["compose"])(
/**
 * For blocks whose block type doesn't support `multiple`, provides the
 * wrapped component with `originalBlockClientId` -- a reference to the
 * first block of the same type in the content -- if and only if that
 * "original" block is not the current one. Thus, an inexisting
 * `originalBlockClientId` prop signals that the block is valid.
 *
 * @param {Component} WrappedBlockEdit A filtered BlockEdit instance.
 *
 * @return {Component} Enhanced component with merged state data props.
 */
Object(external_this_wp_data_["withSelect"])(function (select, block) {
  var multiple = Object(external_this_wp_blocks_["hasBlockSupport"])(block.name, 'multiple', true); // For block types with `multiple` support, there is no "original
  // block" to be found in the content, as the block itself is valid.

  if (multiple) {
    return {};
  } // Otherwise, only pass `originalBlockClientId` if it refers to a different
  // block from the current one.


  var blocks = select('core/editor').getBlocks();
  var firstOfSameType = Object(external_lodash_["find"])(blocks, function (_ref) {
    var name = _ref.name;
    return block.name === name;
  });
  var isInvalid = firstOfSameType && firstOfSameType.clientId !== block.clientId;
  return {
    originalBlockClientId: isInvalid && firstOfSameType.clientId
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref2) {
  var originalBlockClientId = _ref2.originalBlockClientId;
  return {
    selectFirst: function selectFirst() {
      return dispatch('core/editor').selectBlock(originalBlockClientId);
    }
  };
}));
var withMultipleValidation = Object(external_this_wp_compose_["createHigherOrderComponent"])(function (BlockEdit) {
  return enhance(function (_ref3) {
    var originalBlockClientId = _ref3.originalBlockClientId,
        selectFirst = _ref3.selectFirst,
        props = Object(objectWithoutProperties["a" /* default */])(_ref3, ["originalBlockClientId", "selectFirst"]);

    if (!originalBlockClientId) {
      return Object(external_this_wp_element_["createElement"])(BlockEdit, props);
    }

    var blockType = Object(external_this_wp_blocks_["getBlockType"])(props.name);
    var outboundType = getOutboundType(props.name);
    return [Object(external_this_wp_element_["createElement"])("div", {
      key: "invalid-preview",
      style: {
        minHeight: '60px'
      }
    }, Object(external_this_wp_element_["createElement"])(BlockEdit, Object(esm_extends["a" /* default */])({
      key: "block-edit"
    }, props))), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["Warning"], {
      key: "multiple-use-warning",
      actions: [Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        key: "find-original",
        isLarge: true,
        onClick: selectFirst
      }, Object(external_this_wp_i18n_["__"])('Find original')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        key: "remove",
        isLarge: true,
        onClick: function onClick() {
          return props.onReplace([]);
        }
      }, Object(external_this_wp_i18n_["__"])('Remove')), outboundType && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        key: "transform",
        isLarge: true,
        onClick: function onClick() {
          return props.onReplace(Object(external_this_wp_blocks_["createBlock"])(outboundType.name, props.attributes));
        }
      }, Object(external_this_wp_i18n_["__"])('Transform into:'), ' ', outboundType.title)]
    }, Object(external_this_wp_element_["createElement"])("strong", null, blockType.title, ": "), Object(external_this_wp_i18n_["__"])('This block can only be used once.'))];
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
  // Grab the first outbound transform
  var transform = Object(external_this_wp_blocks_["findTransform"])(Object(external_this_wp_blocks_["getBlockTransforms"])('to', blockName), function (_ref4) {
    var type = _ref4.type,
        blocks = _ref4.blocks;
    return type === 'block' && blocks.length === 1;
  } // What about when .length > 1?
  );

  if (!transform) {
    return null;
  }

  return Object(external_this_wp_blocks_["getBlockType"])(transform.blocks[0]);
}

Object(external_this_wp_hooks_["addFilter"])('editor.BlockEdit', 'core/edit-post/validate-multiple-use/with-multiple-validation', withMultipleValidation);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/index.js
/**
 * Internal dependencies
 */



// EXTERNAL MODULE: external {"this":["wp","plugins"]}
var external_this_wp_plugins_ = __webpack_require__("TvNi");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/copy-content-menu-item/index.js

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * WordPress dependencies
 */




<<<<<<< HEAD
function MoreMenuDropdown({
  as: DropdownComponent = external_wp_components_namespaceObject.DropdownMenu,
  className,

  /* translators: button label text should, if possible, be under 16 characters. */
  label = (0,external_wp_i18n_namespaceObject.__)('Options'),
  popoverProps,
  toggleProps,
  children
}) {
  return (0,external_wp_element_namespaceObject.createElement)(DropdownComponent, {
    className: classnames_default()('interface-more-menu-dropdown', className),
    icon: more_vertical,
    label: label,
    popoverProps: {
      placement: 'bottom-end',
      ...popoverProps,
      className: classnames_default()('interface-more-menu-dropdown__content', popoverProps?.className)
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


function PreferencesModal({
  closeModal,
  children
}) {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    className: "interface-preferences-modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Preferences'),
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

function Icon({
  icon,
  size = 24,
  ...props
}) {
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ const icon = (Icon);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-left.js

=======

function CopyContentMenuItem(_ref) {
  var editedPostContent = _ref.editedPostContent,
      hasCopied = _ref.hasCopied,
      setState = _ref.setState;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ClipboardButton"], {
    text: editedPostContent,
    className: "components-menu-item__button",
    onCopy: function onCopy() {
      return setState({
        hasCopied: true
      });
    },
    onFinishCopy: function onFinishCopy() {
      return setState({
        hasCopied: false
      });
    }
  }, hasCopied ? Object(external_this_wp_i18n_["__"])('Copied!') : Object(external_this_wp_i18n_["__"])('Copy All Content'));
}

/* harmony default export */ var copy_content_menu_item = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    editedPostContent: select('core/editor').getEditedPostAttribute('content')
  };
}), Object(external_this_wp_compose_["withState"])({
  hasCopied: false
}))(CopyContentMenuItem));

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__("RxS6");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js


/**
 * WordPress Dependencies
 */




function KeyboardShortcutsHelpMenuItem(_ref) {
  var openModal = _ref.openModal,
      onSelect = _ref.onSelect;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
      onSelect();
      openModal('edit-post/keyboard-shortcut-help');
    },
    shortcut: external_this_wp_keycodes_["displayShortcut"].access('h')
  }, Object(external_this_wp_i18n_["__"])('Keyboard Shortcuts'));
}
/* harmony default export */ var keyboard_shortcuts_help_menu_item = (Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openModal = _dispatch.openModal;

  return {
    openModal: openModal
  };
})(KeyboardShortcutsHelpMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/tools-more-menu-group/index.js


/**
 * External dependencies
 */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * WordPress dependencies
 */

<<<<<<< HEAD
const chevronLeft = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"
}));
/* harmony default export */ const chevron_left = (chevronLeft);

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
/* harmony default export */ const chevron_right = (chevronRight);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal-tabs/index.js
=======



var _createSlotFill = Object(external_this_wp_components_["createSlotFill"])('ToolsMoreMenuGroup'),
    ToolsMoreMenuGroup = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;

ToolsMoreMenuGroup.Slot = function (_ref) {
  var fillProps = _ref.fillProps;
  return Object(external_this_wp_element_["createElement"])(Slot, {
    fillProps: fillProps
  }, function (fills) {
    return !Object(external_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
      label: Object(external_this_wp_i18n_["__"])('Tools')
    }, fills);
  });
};

/* harmony default export */ var tools_more_menu_group = (ToolsMoreMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */




<<<<<<< HEAD

const PREFERENCES_MENU = 'preferences-menu';
function PreferencesModalTabs({
  sections
}) {
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


const Section = ({
  description,
  title,
  children
}) => (0,external_wp_element_namespaceObject.createElement)("fieldset", {
  className: "interface-preferences-modal__section"
}, (0,external_wp_element_namespaceObject.createElement)("legend", {
  className: "interface-preferences-modal__section-legend"
}, (0,external_wp_element_namespaceObject.createElement)("h2", {
  className: "interface-preferences-modal__section-title"
}, title), description && (0,external_wp_element_namespaceObject.createElement)("p", {
  className: "interface-preferences-modal__section-description"
}, description)), children);

/* harmony default export */ const preferences_modal_section = (Section);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal-base-option/index.js


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

/* harmony default export */ const preferences_modal_base_option = (BaseOption);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/index.js














;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/index.js



;// CONCATENATED MODULE: external ["wp","keyboardShortcuts"]
const external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
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
  description: (0,external_wp_i18n_namespaceObject.__)('Insert a link to a post or page.')
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
}, {
  keyCombination: {
    modifier: 'access',
    character: '0'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Convert the current heading to a paragraph.')
}, {
  keyCombination: {
    modifier: 'access',
    character: '1-6'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Convert the current paragraph or heading to a heading of level 1 to 6.')
}];

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/shortcut.js


/**
 * WordPress dependencies
 */



function KeyCombination({
  keyCombination,
  forceAriaLabel
}) {
  const shortcut = keyCombination.modifier ? external_wp_keycodes_namespaceObject.displayShortcutList[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_wp_keycodes_namespaceObject.shortcutAriaLabel[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  return (0,external_wp_element_namespaceObject.createElement)("kbd", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel
  }, (Array.isArray(shortcut) ? shortcut : [shortcut]).map((character, index) => {
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

function Shortcut({
  description,
  keyCombination,
  aliases = [],
  ariaLabel
}) {
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

/* harmony default export */ const keyboard_shortcut_help_modal_shortcut = (Shortcut);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



function DynamicShortcut({
  name
}) {
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

/* harmony default export */ const dynamic_shortcut = (DynamicShortcut);

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




const KEYBOARD_SHORTCUT_HELP_MODAL_NAME = 'edit-post/keyboard-shortcut-help';

const ShortcutList = ({
  shortcuts
}) =>
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
}) : (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcut_help_modal_shortcut, { ...shortcut
}))))
/* eslint-enable jsx-a11y/no-redundant-roles */
;

const ShortcutSection = ({
  title,
  shortcuts,
  className
}) => (0,external_wp_element_namespaceObject.createElement)("section", {
  className: classnames_default()('edit-post-keyboard-shortcut-help-modal__section', className)
}, !!title && (0,external_wp_element_namespaceObject.createElement)("h2", {
  className: "edit-post-keyboard-shortcut-help-modal__section-title"
}, title), (0,external_wp_element_namespaceObject.createElement)(ShortcutList, {
  shortcuts: shortcuts
}));

const ShortcutCategorySection = ({
  title,
  categoryName,
  additionalShortcuts = []
}) => {
  const categoryShortcuts = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return select(external_wp_keyboardShortcuts_namespaceObject.store).getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    title: title,
    shortcuts: categoryShortcuts.concat(additionalShortcuts)
  });
};

function KeyboardShortcutHelpModal({
  isModalActive,
  toggleModal
}) {
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/keyboard-shortcuts', toggleModal);

  if (!isModalActive) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    className: "edit-post-keyboard-shortcut-help-modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts'),
    closeButtonLabel: (0,external_wp_i18n_namespaceObject.__)('Close'),
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
/* harmony default export */ const keyboard_shortcut_help_modal = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => ({
  isModalActive: select(store).isModalActive(KEYBOARD_SHORTCUT_HELP_MODAL_NAME)
})), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  isModalActive
}) => {
  const {
    openModal,
    closeModal
  } = dispatch(store);
  return {
    toggleModal: () => isModalActive ? closeModal() : openModal(KEYBOARD_SHORTCUT_HELP_MODAL_NAME)
  };
})])(KeyboardShortcutHelpModal));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function KeyboardShortcutsHelpMenuItem({
  openModal
}) {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      openModal(KEYBOARD_SHORTCUT_HELP_MODAL_NAME);
    },
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.access('h')
  }, (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts'));
}
/* harmony default export */ const keyboard_shortcuts_help_menu_item = ((0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    openModal
  } = dispatch(store);
  return {
    openModal
  };
})(KeyboardShortcutsHelpMenuItem));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/tools-more-menu-group/index.js


/**
 * WordPress dependencies
 */


const {
  Fill: ToolsMoreMenuGroup,
  Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('ToolsMoreMenuGroup');

ToolsMoreMenuGroup.Slot = ({
  fillProps
}) => (0,external_wp_element_namespaceObject.createElement)(Slot, {
  fillProps: fillProps
}, fills => fills.length > 0 && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
  label: (0,external_wp_i18n_namespaceObject.__)('Tools')
}, fills));

/* harmony default export */ const tools_more_menu_group = (ToolsMoreMenuGroup);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/reducer.js
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

function removedPanels(state = [], action) {
  switch (action.type) {
    case 'REMOVE_PANEL':
      if (!state.includes(action.panelName)) {
        return [...state, action.panelName];
      }

=======
/**
 * Internal dependencies
 */




Object(external_this_wp_plugins_["registerPlugin"])('edit-post', {
  render: function render() {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(tools_more_menu_group, null, function (_ref) {
      var onClose = _ref.onClose;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
        role: "menuitem",
        href: "edit.php?post_type=wp_block"
      }, Object(external_this_wp_i18n_["__"])('Manage All Reusable Blocks')), Object(external_this_wp_element_["createElement"])(keyboard_shortcuts_help_menu_item, {
        onSelect: onClose
      }), Object(external_this_wp_element_["createElement"])(copy_content_menu_item, null));
    }));
  }
});

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__("KQm4");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("rePB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__("vpQ4");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/defaults.js
var PREFERENCES_DEFAULTS = {
  editorMode: 'visual',
  isGeneralSidebarDismissed: false,
  panels: {
    'post-status': {
      opened: true
    }
  },
  features: {
    fixedToolbar: false
  },
  pinnedPluginItems: {}
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/reducer.js




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
 * The default active general sidebar: The "Document" tab.
 *
 * @type {string}
 */

var DEFAULT_ACTIVE_GENERAL_SIDEBAR = 'edit-post/document';
/**
 * Reducer returning the user preferences.
 *
 * @param {Object}  state                           Current state.
 * @param {string}  state.mode                      Current editor mode, either
 *                                                  "visual" or "text".
 * @param {boolean} state.isGeneralSidebarDismissed Whether general sidebar is
 *                                                  dismissed. False by default
 *                                                  or when closing general
 *                                                  sidebar, true when opening
 *                                                  sidebar.
 * @param {boolean} state.isSidebarOpened           Whether the sidebar is
 *                                                  opened or closed.
 * @param {Object}  state.panels                    The state of the different
 *                                                  sidebar panels.
 * @param {Object}  action                          Dispatched action.
 *
 * @return {Object} Updated state.
 */

var preferences = Object(external_this_wp_data_["combineReducers"])({
  isGeneralSidebarDismissed: function isGeneralSidebarDismissed() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'OPEN_GENERAL_SIDEBAR':
      case 'CLOSE_GENERAL_SIDEBAR':
        return action.type === 'CLOSE_GENERAL_SIDEBAR';
    }

    return state;
  },
  panels: function panels() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : PREFERENCES_DEFAULTS.panels;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'TOGGLE_PANEL_ENABLED':
        {
          var panelName = action.panelName;
          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, panelName, Object(objectSpread["a" /* default */])({}, state[panelName], {
            enabled: !Object(external_lodash_["get"])(state, [panelName, 'enabled'], true)
          })));
        }

      case 'TOGGLE_PANEL_OPENED':
        {
          var _panelName = action.panelName;
          var isOpen = state[_panelName] === true || Object(external_lodash_["get"])(state, [_panelName, 'opened'], false);
          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, _panelName, Object(objectSpread["a" /* default */])({}, state[_panelName], {
            opened: !isOpen
          })));
        }
    }

    return state;
  },
  features: function features() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : PREFERENCES_DEFAULTS.features;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'TOGGLE_FEATURE') {
      return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.feature, !state[action.feature]));
    }

    return state;
  },
  editorMode: function editorMode() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : PREFERENCES_DEFAULTS.editorMode;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'SWITCH_MODE') {
      return action.mode;
    }

    return state;
  },
  pinnedPluginItems: function pinnedPluginItems() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : PREFERENCES_DEFAULTS.pinnedPluginItems;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'TOGGLE_PINNED_PLUGIN_ITEM') {
      return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.pluginName, !Object(external_lodash_["get"])(state, [action.pluginName], true)));
    }

    return state;
  }
});
/**
 * Reducer storing the list of all programmatically removed panels.
 *
 * @param {Array}  state  Current state.
 * @param {Object} action Action object.
 *
 * @return {Array} Updated state.
 */

function removedPanels() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REMOVE_PANEL':
      if (!Object(external_lodash_["includes"])(state, action.panelName)) {
        return Object(toConsumableArray["a" /* default */])(state).concat([action.panelName]);
      }

  }

  return state;
}
/**
 * Reducer returning the next active general sidebar state. The active general
 * sidebar is a unique name to identify either an editor or plugin sidebar.
 *
 * @param {?string} state  Current state.
 * @param {Object}  action Action object.
 *
 * @return {?string} Updated state.
 */

function reducer_activeGeneralSidebar() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_ACTIVE_GENERAL_SIDEBAR;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_GENERAL_SIDEBAR':
      return action.name;
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
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_MODAL':
      return action.name;

    case 'CLOSE_MODAL':
      return null;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }

  return state;
}
<<<<<<< HEAD
function publishSidebarActive(state = false, action) {
=======
function publishSidebarActive() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
 * @param {boolean} state  Previous state.
 * @param {Object}  action Action Object.
=======
 * @param {boolean}  state   Previous state.
 * @param {Object}   action  Action Object.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {Object} Updated state.
 */

<<<<<<< HEAD
function isSavingMetaBoxes(state = false, action) {
=======
function isSavingMetaBoxes() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  switch (action.type) {
    case 'REQUEST_META_BOX_UPDATES':
      return true;

    case 'META_BOX_UPDATES_SUCCESS':
<<<<<<< HEAD
    case 'META_BOX_UPDATES_FAILURE':
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      return false;

    default:
      return state;
  }
}
<<<<<<< HEAD

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
=======
/**
 * Reducer keeping track of the meta boxes per location.
 *
 * @param {boolean}  state   Previous state.
 * @param {Object}   action  Action Object.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {Object} Updated state.
 */

<<<<<<< HEAD

function metaBoxLocations(state = {}, action) {
  switch (action.type) {
    case 'SET_META_BOXES_PER_LOCATIONS':
      {
        const newState = { ...state
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
 * Reducer returning the editing canvas device type.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function deviceType(state = 'Desktop', action) {
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

function blockInserterPanel(state = false, action) {
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

function listViewPanel(state = false, action) {
  switch (action.type) {
    case 'SET_IS_INSERTER_OPENED':
      return action.value ? false : state;

    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen;
  }

  return state;
}
/**
 * Reducer tracking whether template editing is on or off.
 *
 * @param {boolean} state
 * @param {Object}  action
 */

function isEditingTemplate(state = false, action) {
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


function metaBoxesInitialized(state = false, action) {
  switch (action.type) {
    case 'META_BOXES_INITIALIZED':
      return true;
=======
function metaBoxLocations() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_META_BOXES_PER_LOCATIONS':
      return action.metaBoxesPerLocation;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }

  return state;
}
<<<<<<< HEAD

const metaBoxes = (0,external_wp_data_namespaceObject.combineReducers)({
  isSaving: isSavingMetaBoxes,
  locations: metaBoxLocations,
  initialized: metaBoxesInitialized
});
/* harmony default export */ const store_reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  metaBoxes,
  publishSidebarActive,
  removedPanels,
  deviceType,
  blockInserterPanel,
  listViewPanel,
  isEditingTemplate
}));

;// CONCATENATED MODULE: external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["wp","a11y"]
const external_wp_a11y_namespaceObject = window["wp"]["a11y"];
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

const openGeneralSidebar = name => ({
  registry
}) => registry.dispatch(store).enableComplementaryArea(store_store.name, name);
/**
 * Returns an action object signalling that the user closed the sidebar.
 */

const closeGeneralSidebar = () => ({
  registry
}) => registry.dispatch(store).disableComplementaryArea(store_store.name);
/**
 * Returns an action object used in signalling that the user opened a modal.
 *
 * @deprecated since WP 6.3 use `core/interface` store's action with the same name instead.
 *
 *
=======
var reducer_metaBoxes = Object(external_this_wp_data_["combineReducers"])({
  isSaving: isSavingMetaBoxes,
  locations: metaBoxLocations
});
/* harmony default export */ var reducer = (Object(external_this_wp_data_["combineReducers"])({
  activeGeneralSidebar: reducer_activeGeneralSidebar,
  activeModal: activeModal,
  metaBoxes: reducer_metaBoxes,
  preferences: preferences,
  publishSidebarActive: publishSidebarActive,
  removedPanels: removedPanels
}));

// EXTERNAL MODULE: ./node_modules/refx/refx.js
var refx = __webpack_require__("gQxa");
var refx_default = /*#__PURE__*/__webpack_require__.n(refx);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__("ODXe");

// EXTERNAL MODULE: external {"this":["wp","a11y"]}
var external_this_wp_a11y_ = __webpack_require__("gdqT");

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__("ywyh");
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
/**
 * Returns an action object used in signalling that the user opened an editor sidebar.
 *
 * @param {string} name Sidebar name to be opened.
 *
 * @return {Object} Action object.
 */
function actions_openGeneralSidebar(name) {
  return {
    type: 'OPEN_GENERAL_SIDEBAR',
    name: name
  };
}
/**
 * Returns an action object signalling that the user closed the sidebar.
 *
 * @return {Object} Action object.
 */

function actions_closeGeneralSidebar() {
  return {
    type: 'CLOSE_GENERAL_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user opened a modal.
 *
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * @param {string} name A string that uniquely identifies the modal.
 *
 * @return {Object} Action object.
 */

<<<<<<< HEAD
const actions_openModal = name => ({
  registry
}) => {
  external_wp_deprecated_default()("select( 'core/edit-post' ).openModal( name )", {
    since: '6.3',
    alternative: "select( 'core/interface').openModal( name )"
  });
  return registry.dispatch(store).openModal(name);
};
/**
 * Returns an action object signalling that the user closed a modal.
 *
 * @deprecated since WP 6.3 use `core/interface` store's action with the same name instead.
 *
 * @return {Object} Action object.
 */

const actions_closeModal = () => ({
  registry
}) => {
  external_wp_deprecated_default()("select( 'core/edit-post' ).closeModal()", {
    since: '6.3',
    alternative: "select( 'core/interface').closeModal()"
  });
  return registry.dispatch(store).closeModal();
};
=======
function actions_openModal(name) {
  return {
    type: 'OPEN_MODAL',
    name: name
  };
}
/**
 * Returns an action object signalling that the user closed a modal.
 *
 * @return {Object} Action object.
 */

function actions_closeModal() {
  return {
    type: 'CLOSE_MODAL'
  };
}
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
function closePublishSidebar() {
=======
function actions_closePublishSidebar() {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  return {
    type: 'CLOSE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user toggles the publish sidebar.
 *
 * @return {Object} Action object
 */

<<<<<<< HEAD
function togglePublishSidebar() {
=======
function actions_togglePublishSidebar() {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
const toggleEditorPanelEnabled = panelName => ({
  registry
}) => {
  var _registry$select$get;

  const inactivePanels = (_registry$select$get = registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'inactivePanels')) !== null && _registry$select$get !== void 0 ? _registry$select$get : [];
  const isPanelInactive = !!inactivePanels?.includes(panelName); // If the panel is inactive, remove it to enable it, else add it to
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

const toggleEditorPanelOpened = panelName => ({
  registry
}) => {
  var _registry$select$get2;

  const openPanels = (_registry$select$get2 = registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'openPanels')) !== null && _registry$select$get2 !== void 0 ? _registry$select$get2 : [];
  const isPanelOpen = !!openPanels?.includes(panelName); // If the panel is open, remove it to close it, else add it to
  // make it open.

  let updatedOpenPanels;

  if (isPanelOpen) {
    updatedOpenPanels = openPanels.filter(openPanelName => openPanelName !== panelName);
  } else {
    updatedOpenPanels = [...openPanels, panelName];
  }

  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'openPanels', updatedOpenPanels);
};
=======
function toggleEditorPanelEnabled(panelName) {
  return {
    type: 'TOGGLE_PANEL_ENABLED',
    panelName: panelName
  };
}
/**
 * Returns an action object used to open or close a panel in the editor.
 *
 * @param {string} panelName A string that identifies the panel to open or close.
 *
 * @return {Object} Action object.
*/

function actions_toggleEditorPanelOpened(panelName) {
  return {
    type: 'TOGGLE_PANEL_OPENED',
    panelName: panelName
  };
}
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
    panelName
  };
}
/**
 * Triggers an action used to toggle a feature flag.
 *
 * @param {string} feature Feature name.
 */

const actions_toggleFeature = feature => ({
  registry
}) => registry.dispatch(external_wp_preferences_namespaceObject.store).toggle('core/edit-post', feature);
/**
 * Triggers an action used to switch editor mode.
 *
 * @param {string} mode The editor mode.
 */

const switchEditorMode = mode => ({
  registry
}) => {
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

const togglePinnedPluginItem = pluginName => ({
  registry
}) => {
  const isPinned = registry.select(store).isItemPinned('core/edit-post', pluginName);
  registry.dispatch(store)[isPinned ? 'unpinItem' : 'pinItem']('core/edit-post', pluginName);
};
/**
 * Returns an action object used in signaling that a style should be auto-applied when a block is created.
 *
 * @param {string}  blockName  Name of the block.
 * @param {?string} blockStyle Name of the style that should be auto applied. If undefined, the "auto apply" setting of the block is removed.
 */

const updatePreferredStyleVariations = (blockName, blockStyle) => ({
  registry
}) => {
  var _registry$select$get3;

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

const showBlockTypes = blockNames => ({
  registry
}) => {
  var _registry$select$get4;

  const existingBlockNames = (_registry$select$get4 = registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'hiddenBlockTypes')) !== null && _registry$select$get4 !== void 0 ? _registry$select$get4 : [];
  const newBlockNames = existingBlockNames.filter(type => !(Array.isArray(blockNames) ? blockNames : [blockNames]).includes(type));
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'hiddenBlockTypes', newBlockNames);
};
/**
 * Update the provided block types to be hidden.
 *
 * @param {string[]} blockNames Names of block types to hide.
 */

const hideBlockTypes = blockNames => ({
  registry
}) => {
  var _registry$select$get5;

  const existingBlockNames = (_registry$select$get5 = registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'hiddenBlockTypes')) !== null && _registry$select$get5 !== void 0 ? _registry$select$get5 : [];
  const mergedBlockNames = new Set([...existingBlockNames, ...(Array.isArray(blockNames) ? blockNames : [blockNames])]);
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'hiddenBlockTypes', [...mergedBlockNames]);
};
/**
 * Stores info about which Meta boxes are available in which location.
 *
 * @param {Object} metaBoxesPerLocation Meta boxes per location.
=======
    panelName: panelName
  };
}
/**
 * Returns an action object used to toggle a feature flag.
 *
 * @param {string} feature Feature name.
 *
 * @return {Object} Action object.
 */

function toggleFeature(feature) {
  return {
    type: 'TOGGLE_FEATURE',
    feature: feature
  };
}
function switchEditorMode(mode) {
  return {
    type: 'SWITCH_MODE',
    mode: mode
  };
}
/**
 * Returns an action object used to toggle a plugin name flag.
 *
 * @param {string} pluginName Plugin name.
 *
 * @return {Object} Action object.
 */

function togglePinnedPluginItem(pluginName) {
  return {
    type: 'TOGGLE_PINNED_PLUGIN_ITEM',
    pluginName: pluginName
  };
}
/**
 * Returns an action object used in signaling
 * what Meta boxes are available in which location.
 *
 * @param {Object} metaBoxesPerLocation Meta boxes per location.
 *
 * @return {Object} Action object.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

function setAvailableMetaBoxesPerLocation(metaBoxesPerLocation) {
  return {
    type: 'SET_META_BOXES_PER_LOCATIONS',
<<<<<<< HEAD
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
=======
    metaBoxesPerLocation: metaBoxesPerLocation
  };
}
/**
 * Returns an action object used to request meta box update.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {Object} Action object.
 */

<<<<<<< HEAD
function metaBoxUpdatesSuccess() {
  return {
    type: 'META_BOX_UPDATES_SUCCESS'
  };
}
/**
 * Returns an action object used to signal a failed meta box update.
=======
function requestMetaBoxUpdates() {
  return {
    type: 'REQUEST_META_BOX_UPDATES'
  };
}
/**
 * Returns an action object used signal a successful meta box update.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {Object} Action object.
 */

<<<<<<< HEAD
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

const __unstableSwitchToTemplateMode = (newTemplate = false) => ({
  registry,
  select,
  dispatch
}) => {
  dispatch(setIsEditingTemplate(true));
  const isWelcomeGuideActive = select.isFeatureActive('welcomeGuideTemplate');

  if (!isWelcomeGuideActive) {
    const message = newTemplate ? (0,external_wp_i18n_namespaceObject.__)("Custom template created. You're in template mode now.") : (0,external_wp_i18n_namespaceObject.__)('Editing template. Changes made here affect all posts and pages that use the template.');
    registry.dispatch(external_wp_notices_namespaceObject.store).createSuccessNotice(message, {
      type: 'snackbar'
    });
  }
};
/**
 * Create a block based template.
 *
 * @param {Object?} template Template to create and assign.
 */

const __unstableCreateTemplate = template => async ({
  registry
}) => {
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

const initializeMetaBoxes = () => ({
  registry,
  select,
  dispatch
}) => {
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
  let wasAutosavingPost = registry.select(external_wp_editor_namespaceObject.store).isAutosavingPost(); // Save metaboxes when performing a full save on the post.

  registry.subscribe(async () => {
    const isSavingPost = registry.select(external_wp_editor_namespaceObject.store).isSavingPost();
    const isAutosavingPost = registry.select(external_wp_editor_namespaceObject.store).isAutosavingPost(); // Save metaboxes on save completion, except for autosaves.

    const shouldTriggerMetaboxesSave = wasSavingPost && !wasAutosavingPost && !isSavingPost && select.hasMetaBoxes(); // Save current state for next inspection.

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
  const panelsWithEnabledState = inactivePanels?.reduce((accumulatedPanels, panelName) => ({ ...accumulatedPanels,
    [panelName]: {
      enabled: false
    }
  }), {}); // Then reduce the open panels, passing in the result of the previous
  // reduction as the initial value so that both open and inactive
  // panel state is combined.

  const panels = openPanels?.reduce((accumulatedPanels, panelName) => {
    const currentPanelState = accumulatedPanels?.[panelName];
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
  return !isEditorPanelRemoved(state, panelName) && !inactivePanels?.includes(panelName);
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
  return !!openPanels?.includes(panelName);
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

const selectors_isModalActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, modalName) => {
  external_wp_deprecated_default()(`select( 'core/edit-post' ).isModalActive`, {
    since: '6.3',
    alternative: `select( 'core/interface' ).isModalActive`
  });
  return !!select(store).isModalActive(modalName);
});
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
  return isMetaBoxLocationActive(state, location) && getMetaBoxesPerLocation(state, location)?.some(({
    id
  }) => {
    return isEditorPanelEnabled(state, `meta-box-${id}`);
  });
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
    const templateWithSameSlug = select(external_wp_coreData_namespaceObject.store).getEntityRecords('postType', 'wp_template', {
      per_page: -1
    })?.find(template => template.slug === currentTemplate);

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
  reducer: store_reducer,
  actions: store_actions_namespaceObject,
  selectors: store_selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store_store);

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






function ManagePatternsMenuItem() {
  const url = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      canUser
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);

    const isBlockTheme = getEditorSettings().__unstableIsBlockBasedTheme;

    const defaultUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
      post_type: 'wp_block'
    });
    const patternsUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('site-editor.php', {
      path: '/patterns'
    }); // The site editor and templates both check whether the user has
    // edit_theme_options capabilities. We can leverage that here and not
    // display the manage patterns link if the user can't access it.

    return canUser('read', 'templates') && isBlockTheme ? patternsUrl : defaultUrl;
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    role: "menuitem",
    href: url
  }, (0,external_wp_i18n_namespaceObject.__)('Manage patterns'));
}

(0,external_wp_plugins_namespaceObject.registerPlugin)('edit-post', {
  render() {
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(tools_more_menu_group, null, ({
      onClose
    }) => (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(ManagePatternsMenuItem, null), (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcuts_help_menu_item, {
      onSelect: onClose
    }), (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideMenuItem, null), (0,external_wp_element_namespaceObject.createElement)(CopyContentMenuItem, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      role: "menuitem",
      icon: library_external,
      href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/documentation/article/wordpress-block-editor/'),
      target: "_blank",
      rel: "noopener noreferrer"
    }, (0,external_wp_i18n_namespaceObject.__)('Help'), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
      as: "span"
    },
    /* translators: accessibility text */
    (0,external_wp_i18n_namespaceObject.__)('(opens in a new tab)'))))));
  }

});

;// CONCATENATED MODULE: external ["wp","commands"]
const external_wp_commands_namespaceObject = window["wp"]["commands"];
;// CONCATENATED MODULE: external ["wp","coreCommands"]
const external_wp_coreCommands_namespaceObject = window["wp"]["coreCommands"];
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

;// CONCATENATED MODULE: external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my plugin or theme will inevitably break on the next WordPress release.', '@wordpress/edit-post');

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



const {
  LayoutStyle,
  useLayoutClasses,
  useLayoutStyles
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const isGutenbergPlugin =  false ? 0 : false;

function MaybeIframe({
  children,
  contentRef,
  shouldIframe,
  styles,
  style
}) {
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
    ref: ref,
    contentRef: contentRef,
    style: {
      width: '100%',
      height: '100%',
      display: 'block'
    },
    name: "editor-canvas"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableEditorStyles, {
    styles: styles
  }), children);
}
/**
 * Given an array of nested blocks, find the first Post Content
 * block inside it, recursing through any nesting levels,
 * and return its attributes.
 *
 * @param {Array} blocks A list of blocks.
 *
 * @return {Object | undefined} The Post Content block.
 */


function getPostContentAttributes(blocks) {
  for (let i = 0; i < blocks.length; i++) {
    if (blocks[i].name === 'core/post-content') {
      return blocks[i].attributes;
    }

    if (blocks[i].innerBlocks.length) {
      const nestedPostContent = getPostContentAttributes(blocks[i].innerBlocks);

      if (nestedPostContent) {
        return nestedPostContent;
      }
    }
  }
}

function VisualEditor({
  styles
}) {
  const {
    deviceType,
    isWelcomeGuideVisible,
    isTemplateMode,
    postContentAttributes,
    editedPostTemplate = {},
    wrapperBlockName,
    wrapperUniqueId,
    isBlockBasedTheme,
    hasV3BlocksOnly
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isFeatureActive,
      isEditingTemplate,
      getEditedPostTemplate,
      __experimentalGetPreviewDeviceType
    } = select(store_store);
    const {
      getCurrentPostId,
      getCurrentPostType,
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);
    const {
      getBlockTypes
    } = select(external_wp_blocks_namespaceObject.store);

    const _isTemplateMode = isEditingTemplate();

    let _wrapperBlockName;

    if (getCurrentPostType() === 'wp_block') {
      _wrapperBlockName = 'core/block';
    } else if (!_isTemplateMode) {
      _wrapperBlockName = 'core/post-content';
    }

    const editorSettings = getEditorSettings();
    const supportsTemplateMode = editorSettings.supportsTemplateMode;
    const canEditTemplate = select(external_wp_coreData_namespaceObject.store).canUser('create', 'templates');
    return {
      deviceType: __experimentalGetPreviewDeviceType(),
      isWelcomeGuideVisible: isFeatureActive('welcomeGuide'),
      isTemplateMode: _isTemplateMode,
      postContentAttributes: getEditorSettings().postContentAttributes,
      // Post template fetch returns a 404 on classic themes, which
      // messes with e2e tests, so check it's a block theme first.
      editedPostTemplate: supportsTemplateMode && canEditTemplate ? getEditedPostTemplate() : undefined,
      wrapperBlockName: _wrapperBlockName,
      wrapperUniqueId: getCurrentPostId(),
      isBlockBasedTheme: editorSettings.__unstableIsBlockBasedTheme,
      hasV3BlocksOnly: getBlockTypes().every(type => {
        return type.apiVersion >= 3;
      })
    };
  }, []);
  const {
    isCleanNewPost
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_editor_namespaceObject.store);
  const hasMetaBoxes = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).hasMetaBoxes(), []);
  const {
    hasRootPaddingAwareAlignments,
    isFocusMode,
    themeHasDisabledLayoutStyles,
    themeSupportsLayout
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const _settings = select(external_wp_blockEditor_namespaceObject.store).getSettings();

    return {
      themeHasDisabledLayoutStyles: _settings.disableLayoutStyles,
      themeSupportsLayout: _settings.supportsLayout,
      isFocusMode: _settings.focusMode,
      hasRootPaddingAwareAlignments: _settings.__experimentalFeatures?.useRootPaddingAwareAlignments
    };
  }, []);
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
  const newestPostContentAttributes = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!editedPostTemplate?.content && !editedPostTemplate?.blocks) {
      return postContentAttributes;
    } // When in template editing mode, we can access the blocks directly.


    if (editedPostTemplate?.blocks) {
      return getPostContentAttributes(editedPostTemplate?.blocks);
    } // If there are no blocks, we have to parse the content string.
    // Best double-check it's a string otherwise the parse function gets unhappy.


    const parseableContent = typeof editedPostTemplate?.content === 'string' ? editedPostTemplate?.content : '';
    return getPostContentAttributes((0,external_wp_blocks_namespaceObject.parse)(parseableContent)) || {};
  }, [editedPostTemplate?.content, editedPostTemplate?.blocks, postContentAttributes]);
  const {
    layout = {},
    align = ''
  } = newestPostContentAttributes || {};
  const postContentLayoutClasses = useLayoutClasses(newestPostContentAttributes, 'core/post-content');
  const blockListLayoutClass = classnames_default()({
    'is-layout-flow': !themeSupportsLayout
  }, themeSupportsLayout && postContentLayoutClasses, align && `align${align}`);
  const postContentLayoutStyles = useLayoutStyles(newestPostContentAttributes, 'core/post-content', '.block-editor-block-list__layout.is-root-container'); // Update type for blocks using legacy layouts.

  const postContentLayout = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return layout && (layout?.type === 'constrained' || layout?.inherit || layout?.contentSize || layout?.wideSize) ? { ...globalLayoutSettings,
      ...layout,
      type: 'constrained'
    } : { ...globalLayoutSettings,
      ...layout,
      type: 'default'
    };
  }, [layout?.type, layout?.inherit, layout?.contentSize, layout?.wideSize, globalLayoutSettings]); // If there is a Post Content block we use its layout for the block list;
  // if not, this must be a classic theme, in which case we use the fallback layout.

  const blockListLayout = postContentAttributes ? postContentLayout : fallbackLayout;
  const titleRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isWelcomeGuideVisible || !isCleanNewPost()) {
      return;
    }

    titleRef?.current?.focus();
  }, [isWelcomeGuideVisible, isCleanNewPost]);
  styles = (0,external_wp_element_namespaceObject.useMemo)(() => [...styles, {
    // We should move this in to future to the body.
    css: `.edit-post-visual-editor__post-title-wrapper{margin-top:4rem}` + (paddingBottom ? `body{padding-bottom:${paddingBottom}}` : '')
  }], [styles]); // Add some styles for alignwide/alignfull Post Content and its children.

  const alignCSS = `.is-root-container.alignwide { max-width: var(--wp--style--global--wide-size); margin-left: auto; margin-right: auto;}
		.is-root-container.alignwide:where(.is-layout-flow) > :not(.alignleft):not(.alignright) { max-width: var(--wp--style--global--wide-size);}
		.is-root-container.alignfull { max-width: none; margin-left: auto; margin-right: auto;}
		.is-root-container.alignfull:where(.is-layout-flow) > :not(.alignleft):not(.alignright) { max-width: none;}`;
  const isToBeIframed = (hasV3BlocksOnly || isGutenbergPlugin && isBlockBasedTheme) && !hasMetaBoxes || isTemplateMode || deviceType === 'Tablet' || deviceType === 'Mobile';
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockTools, {
    __unstableContentRef: ref,
    className: classnames_default()('edit-post-visual-editor', {
      'is-template-mode': isTemplateMode,
      'has-inline-canvas': !isToBeIframed
    })
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.VisualEditorGlobalKeyboardShortcuts, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    className: "edit-post-visual-editor__content-area",
    animate: {
      padding: isTemplateMode ? '48px 48px 0' : 0
    },
    ref: blockSelectionClearerRef
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    animate: animatedStyles,
    initial: desktopCanvasStyles,
    className: previewMode
  }, (0,external_wp_element_namespaceObject.createElement)(MaybeIframe, {
    shouldIframe: isToBeIframed,
    contentRef: contentRef,
    styles: styles
  }, themeSupportsLayout && !themeHasDisabledLayoutStyles && !isTemplateMode && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(LayoutStyle, {
    selector: ".edit-post-visual-editor__post-title-wrapper",
    layout: fallbackLayout
  }), (0,external_wp_element_namespaceObject.createElement)(LayoutStyle, {
    selector: ".block-editor-block-list__layout.is-root-container",
    layout: blockListLayout
  }), align && (0,external_wp_element_namespaceObject.createElement)(LayoutStyle, {
    css: alignCSS
  }), postContentLayoutStyles && (0,external_wp_element_namespaceObject.createElement)(LayoutStyle, {
    layout: postContentLayout,
    css: postContentLayoutStyles
  })), !isTemplateMode && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('edit-post-visual-editor__post-title-wrapper', {
      'is-focus-mode': isFocusMode,
      'has-global-padding': hasRootPaddingAwareAlignments
    }),
    contentEditable: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTitle, {
    ref: titleRef
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalRecursionProvider, {
    blockName: wrapperBlockName,
    uniqueId: wrapperUniqueId
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockList, {
    className: isTemplateMode ? 'wp-site-blocks' : `${blockListLayoutClass} wp-block-post-content` // Ensure root level blocks receive default/flow blockGap styling rules.
    ,
    layout: blockListLayout
  }))))));
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
    isListViewOpened,
    isFeatureActive
  } = (0,external_wp_data_namespaceObject.useSelect)(store_store);
  const isModeToggleDisabled = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      richEditingEnabled,
      codeEditingEnabled
    } = select(external_wp_editor_namespaceObject.store).getEditorSettings();
    return !richEditingEnabled || !codeEditingEnabled;
  }, []);
  const {
    createInfoNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const {
    switchEditorMode,
    openGeneralSidebar,
    closeGeneralSidebar,
    toggleFeature,
    setIsListViewOpened,
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    registerShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  const {
    set: setPreference
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);

  const toggleDistractionFree = () => {
    setPreference('core/edit-post', 'fixedToolbar', false);
    setIsInserterOpened(false);
    setIsListViewOpened(false);
    closeGeneralSidebar();
  };

  const {
    replaceBlocks
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    getBlockName,
    getSelectedBlockClientId,
    getBlockAttributes
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);

  const handleTextLevelShortcut = (event, level) => {
    event.preventDefault();
    const destinationBlockName = level === 0 ? 'core/paragraph' : 'core/heading';
    const currentClientId = getSelectedBlockClientId();

    if (currentClientId === null) {
      return;
    }

    const blockName = getBlockName(currentClientId);

    if (blockName !== 'core/paragraph' && blockName !== 'core/heading') {
      return;
    }

    const attributes = getBlockAttributes(currentClientId);
    const textAlign = blockName === 'core/paragraph' ? 'align' : 'textAlign';
    const destinationTextAlign = destinationBlockName === 'core/paragraph' ? 'align' : 'textAlign';
    replaceBlocks(currentClientId, (0,external_wp_blocks_namespaceObject.createBlock)(destinationBlockName, {
      level,
      content: attributes.content,
      ...{
        [destinationTextAlign]: attributes[textAlign]
      }
    }));
  };

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
      name: 'core/edit-post/toggle-distraction-free',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Toggle distraction free mode.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: '\\'
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
      description: (0,external_wp_i18n_namespaceObject.__)('Show or hide the Settings sidebar.'),
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
      }, {
        modifier: 'ctrlShift',
        character: '~'
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
    registerShortcut({
      name: 'core/edit-post/transform-heading-to-paragraph',
      category: 'block-library',
      description: (0,external_wp_i18n_namespaceObject.__)('Transform heading to paragraph.'),
      keyCombination: {
        modifier: 'access',
        character: `0`
      }
    });
    [1, 2, 3, 4, 5, 6].forEach(level => {
      registerShortcut({
        name: `core/edit-post/transform-paragraph-to-heading-${level}`,
        category: 'block-library',
        description: (0,external_wp_i18n_namespaceObject.__)('Transform paragraph to heading.'),
        keyCombination: {
          modifier: 'access',
          character: `${level}`
        }
      });
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
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-distraction-free', () => {
    toggleDistractionFree();
    toggleFeature('distractionFree');
    createInfoNotice(isFeatureActive('distractionFree') ? (0,external_wp_i18n_namespaceObject.__)('Distraction free mode turned on.') : (0,external_wp_i18n_namespaceObject.__)('Distraction free mode turned off.'), {
      id: 'core/edit-post/distraction-free-mode/notice',
      type: 'snackbar'
    });
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
  }); // Only opens the list view. Other functionality for this shortcut happens in the rendered sidebar.

  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-list-view', () => {
    if (!isListViewOpened()) {
      setIsListViewOpened(true);
    }
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/transform-heading-to-paragraph', event => handleTextLevelShortcut(event, 0));
  [1, 2, 3, 4, 5, 6].forEach(level => {
    //the loop is based off on a constant therefore
    //the hook will execute the same way every time
    //eslint-disable-next-line react-hooks/rules-of-hooks
    (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)(`core/edit-post/transform-paragraph-to-heading-${level}`, event => handleTextLevelShortcut(event, level));
  });
  return null;
}

/* harmony default export */ const keyboard_shortcuts = (KeyboardShortcuts);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-custom-fields.js


/**
 * WordPress dependencies
 */






function CustomFieldsConfirmation({
  willEnable
}) {
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
  }, willEnable ? (0,external_wp_i18n_namespaceObject.__)('Show & Reload Page') : (0,external_wp_i18n_namespaceObject.__)('Hide & Reload Page')));
}
function EnableCustomFieldsOption({
  label,
  areCustomFieldsEnabled
}) {
  const [isChecked, setIsChecked] = (0,external_wp_element_namespaceObject.useState)(areCustomFieldsEnabled);
  return (0,external_wp_element_namespaceObject.createElement)(preferences_modal_base_option, {
    label: label,
    isChecked: isChecked,
    onChange: setIsChecked
  }, isChecked !== areCustomFieldsEnabled && (0,external_wp_element_namespaceObject.createElement)(CustomFieldsConfirmation, {
    willEnable: isChecked
  }));
}
/* harmony default export */ const enable_custom_fields = ((0,external_wp_data_namespaceObject.withSelect)(select => ({
  areCustomFieldsEnabled: !!select(external_wp_editor_namespaceObject.store).getEditorSettings().enableCustomFields
}))(EnableCustomFieldsOption));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-panel.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/* harmony default export */ const enable_panel = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, {
  panelName
}) => {
  const {
    isEditorPanelEnabled,
    isEditorPanelRemoved
  } = select(store_store);
  return {
    isRemoved: isEditorPanelRemoved(panelName),
    isChecked: isEditorPanelEnabled(panelName)
  };
}), (0,external_wp_compose_namespaceObject.ifCondition)(({
  isRemoved
}) => !isRemoved), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  panelName
}) => ({
  onChange: () => dispatch(store_store).toggleEditorPanelEnabled(panelName)
})))(preferences_modal_base_option));

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

const EnablePluginDocumentSettingPanelOption = ({
  label,
  panelName
}) => (0,external_wp_element_namespaceObject.createElement)(Fill, null, (0,external_wp_element_namespaceObject.createElement)(enable_panel, {
  label: label,
  panelName: panelName
}));

EnablePluginDocumentSettingPanelOption.Slot = enable_plugin_document_setting_panel_Slot;
/* harmony default export */ const enable_plugin_document_setting_panel = (EnablePluginDocumentSettingPanelOption);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-publish-sidebar.js
/**
 * WordPress dependencies
 */





/* harmony default export */ const enable_publish_sidebar = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)(select => ({
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
=======
function metaBoxUpdatesSuccess() {
  return {
    type: 'META_BOX_UPDATES_SUCCESS'
  };
}

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
/**
 * External dependencies
 */


/**
 * Returns the current editing mode.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Editing mode.
 */

function getEditorMode(state) {
  return getPreference(state, 'editorMode', 'visual');
}
/**
 * Returns true if the editor sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the editor sidebar is opened.
 */

function selectors_isEditorSidebarOpened(state) {
  var activeGeneralSidebar = getActiveGeneralSidebarName(state);
  return Object(external_lodash_["includes"])(['edit-post/document', 'edit-post/block'], activeGeneralSidebar);
}
/**
 * Returns true if the plugin sidebar is opened.
 *
 * @param {Object} state Global application state
 * @return {boolean}     Whether the plugin sidebar is opened.
 */

function isPluginSidebarOpened(state) {
  var activeGeneralSidebar = getActiveGeneralSidebarName(state);
  return !!activeGeneralSidebar && !selectors_isEditorSidebarOpened(state);
}
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

function getActiveGeneralSidebarName(state) {
  // Dismissal takes precedent.
  var isDismissed = getPreference(state, 'isGeneralSidebarDismissed', false);

  if (isDismissed) {
    return null;
  }

  return state.activeGeneralSidebar;
}
/**
 * Returns the preferences (these preferences are persisted locally).
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Preferences Object.
 */

function getPreferences(state) {
  return state.preferences;
}
/**
 *
 * @param {Object} state         Global application state.
 * @param {string} preferenceKey Preference Key.
 * @param {Mixed}  defaultValue  Default Value.
 *
 * @return {Mixed} Preference Value.
 */

function getPreference(state, preferenceKey, defaultValue) {
  var preferences = getPreferences(state);
  var value = preferences[preferenceKey];
  return value === undefined ? defaultValue : value;
}
/**
 * Returns true if the publish sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the publish sidebar is open.
 */

function selectors_isPublishSidebarOpened(state) {
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
  return Object(external_lodash_["includes"])(state.removedPanels, panelName);
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

function selectors_isEditorPanelEnabled(state, panelName) {
  var panels = getPreference(state, 'panels');
  return !isEditorPanelRemoved(state, panelName) && Object(external_lodash_["get"])(panels, [panelName, 'enabled'], true);
}
/**
 * Returns true if the given panel is open, or false otherwise. Panels are
 * closed by default.
 *
 * @param  {Object}  state     Global application state.
 * @param  {string}  panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is open.
 */

function selectors_isEditorPanelOpened(state, panelName) {
  var panels = getPreference(state, 'panels');
  return panels[panelName] === true || Object(external_lodash_["get"])(panels, [panelName, 'opened'], false);
}
/**
 * Returns true if a modal is active, or false otherwise.
 *
 * @param  {Object}  state 	   Global application state.
 * @param  {string}  modalName A string that uniquely identifies the modal.
 *
 * @return {boolean} Whether the modal is active.
 */

function selectors_isModalActive(state, modalName) {
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

function isFeatureActive(state, feature) {
  return !!state.preferences.features[feature];
}
/**
 * Returns true if the plugin item is pinned to the header.
 * When the value is not set it defaults to true.
 *
 * @param  {Object}  state      Global application state.
 * @param  {string}  pluginName Plugin item name.
 *
 * @return {boolean} Whether the plugin item is pinned.
 */

function isPluginItemPinned(state, pluginName) {
  var pinnedPluginItems = getPreference(state, 'pinnedPluginItems', {});
  return Object(external_lodash_["get"])(pinnedPluginItems, [pluginName], true);
}
/**
 * Returns an array of active meta box locations.
 *
 * @param {Object} state Post editor state.
 *
 * @return {string[]} Active meta box locations.
 */

var getActiveMetaBoxLocations = Object(rememo["a" /* default */])(function (state) {
  return Object.keys(state.metaBoxes.locations).filter(function (location) {
    return isMetaBoxLocationActive(state, location);
  });
}, function (state) {
  return [state.metaBoxes.locations];
});
/**
 * Returns true if a metabox location is active and visible
 *
 * @param {Object} state    Post editor state.
 * @param {string} location Meta box location to test.
 *
 * @return {boolean} Whether the meta box location is active and visible.
 */

function isMetaBoxLocationVisible(state, location) {
  return isMetaBoxLocationActive(state, location) && Object(external_lodash_["some"])(getMetaBoxesPerLocation(state, location), function (_ref) {
    var id = _ref.id;
    return selectors_isEditorPanelEnabled(state, "meta-box-".concat(id));
  });
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
  var metaBoxes = getMetaBoxesPerLocation(state, location);
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

var getAllMetaBoxes = Object(rememo["a" /* default */])(function (state) {
  return Object(external_lodash_["flatten"])(Object(external_lodash_["values"])(state.metaBoxes.locations));
}, function (state) {
  return [state.metaBoxes.locations];
});
/**
 * Returns true if the post is using Meta Boxes
 *
 * @param  {Object} state Global application state
 *
 * @return {boolean} Whether there are metaboxes or not.
 */

function hasMetaBoxes(state) {
  return getActiveMetaBoxLocations(state).length > 0;
}
/**
 * Returns true if the Meta Boxes are being saved.
 *
 * @param   {Object}  state Global application state.
 *
 * @return {boolean} Whether the metaboxes are being saved.
 */

function selectors_isSavingMetaBoxes(state) {
  return state.metaBoxes.isSaving;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/utils/meta-boxes.js
/**
 * Function returning the current Meta Boxes DOM Node in the editor
 * whether the meta box area is opened or not.
 * If the MetaBox Area is visible returns it, and returns the original container instead.
 *
 * @param   {string} location Meta Box location.
 * @return {string}          HTML content.
 */
var getMetaBoxContainer = function getMetaBoxContainer(location) {
  var area = document.querySelector(".edit-post-meta-boxes-area.is-".concat(location, " .metabox-location-").concat(location));

  if (area) {
    return area;
  }

  return document.querySelector('#metaboxes .metabox-location-' + location);
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/utils.js
/**
 * Given a selector returns a functions that returns the listener only
 * if the returned value from the selector changes.
 *
 * @param  {function} selector Selector.
 * @param  {function} listener Listener.
 * @return {function}          Listener creator.
 */
var onChangeListener = function onChangeListener(selector, listener) {
  var previousValue = selector();
  return function () {
    var selectedValue = selector();

    if (selectedValue !== previousValue) {
      previousValue = selectedValue;
      listener(selectedValue);
    }
  };
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/effects.js



/**
 * External dependencies
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */



<<<<<<< HEAD
/**
 * Internal dependencies
 */


/* harmony default export */ const enable_feature = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, {
  featureName
}) => {
  const {
    isFeatureActive
  } = select(store_store);
  return {
    isChecked: isFeatureActive(featureName)
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  featureName,
  onToggle = () => {}
}) => ({
  onChange: () => {
    onToggle();
    dispatch(store_store).toggleFeature(featureName);
  }
})))(preferences_modal_base_option));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/index.js






;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/meta-boxes-section.js


/**
 * WordPress dependencies
 */


=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * Internal dependencies
 */



<<<<<<< HEAD
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

  return (0,external_wp_element_namespaceObject.createElement)(preferences_modal_section, { ...sectionProps
  }, areCustomFieldsRegistered && (0,external_wp_element_namespaceObject.createElement)(enable_custom_fields, {
    label: (0,external_wp_i18n_namespaceObject.__)('Custom fields')
  }), thirdPartyMetaBoxes.map(({
    id,
    title
  }) => (0,external_wp_element_namespaceObject.createElement)(enable_panel, {
    key: id,
    label: title,
    panelName: `meta-box-${id}`
  })));
}
/* harmony default export */ const meta_boxes_section = ((0,external_wp_data_namespaceObject.withSelect)(select => {
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



function BlockTypesChecklist({
  blockTypes,
  value,
  onItemChange
}) {
  return (0,external_wp_element_namespaceObject.createElement)("ul", {
    className: "edit-post-block-manager__checklist"
  }, blockTypes.map(blockType => (0,external_wp_element_namespaceObject.createElement)("li", {
    key: blockType.name,
    className: "edit-post-block-manager__checklist-item"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    label: blockType.title,
    checked: value.includes(blockType.name),
    onChange: (...args) => onItemChange(blockType.name, ...args)
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockIcon, {
    icon: blockType.icon
  }))));
}

/* harmony default export */ const checklist = (BlockTypesChecklist);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-manager/category.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




function BlockManagerCategory({
  title,
  blockTypes
}) {
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

    return blockTypes.filter(({
      name
    }) => {
      return defaultAllowedBlockTypes?.includes(name);
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
    const blockNames = blockTypes.map(({
      name
    }) => name);

    if (nextIsChecked) {
      showBlockTypes(blockNames);
    } else {
      hideBlockTypes(blockNames);
    }
  }, [blockTypes]);

  if (!filteredBlockTypes.length) {
    return null;
  }

  const checkedBlockNames = filteredBlockTypes.map(({
    name
  }) => name).filter(type => !hiddenBlockTypes.includes(type));
  const titleId = 'edit-post-block-manager__category-title-' + instanceId;
  const isAllChecked = checkedBlockNames.length === filteredBlockTypes.length;
  const isIndeterminate = !isAllChecked && checkedBlockNames.length > 0;
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    role: "group",
    "aria-labelledby": titleId,
    className: "edit-post-block-manager__category"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    checked: isAllChecked,
    onChange: toggleAllVisible,
    className: "edit-post-block-manager__category-title",
    indeterminate: isIndeterminate,
    label: (0,external_wp_element_namespaceObject.createElement)("span", {
      id: titleId
    }, title)
  }), (0,external_wp_element_namespaceObject.createElement)(checklist, {
    blockTypes: filteredBlockTypes,
    value: checkedBlockNames,
    onItemChange: toggleVisible
  }));
}

/* harmony default export */ const block_manager_category = (BlockManagerCategory);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-manager/index.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */




function BlockManager({
  blockTypes,
  categories,
  hasBlockSupport,
  isMatchingSearchTerm,
  numberOfHiddenBlocks,
  enableAllBlockTypes
}) {
  const debouncedSpeak = (0,external_wp_compose_namespaceObject.useDebounce)(external_wp_a11y_namespaceObject.speak, 500);
  const [search, setSearch] = (0,external_wp_element_namespaceObject.useState)(''); // Filtering occurs here (as opposed to `withSelect`) to avoid
  // wasted renders by consequence of `Array#filter` producing
  // a new value reference on each call.

  blockTypes = blockTypes.filter(blockType => hasBlockSupport(blockType, 'inserter', true) && (!search || isMatchingSearchTerm(blockType, search)) && (!blockType.parent || blockType.parent.includes('core/post-content'))); // Announce search results on change

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
  (0,external_wp_i18n_namespaceObject._n)('%d block is hidden.', '%d blocks are hidden.', numberOfHiddenBlocks), numberOfHiddenBlocks), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "link",
    onClick: () => enableAllBlockTypes(blockTypes)
  }, (0,external_wp_i18n_namespaceObject.__)('Reset'))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SearchControl, {
    __nextHasNoMarginBottom: true,
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
    blockTypes: blockTypes.filter(blockType => blockType.category === category.slug)
  })), (0,external_wp_element_namespaceObject.createElement)(block_manager_category, {
    title: (0,external_wp_i18n_namespaceObject.__)('Uncategorized'),
    blockTypes: blockTypes.filter(({
      category
    }) => !category)
  })));
}

/* harmony default export */ const block_manager = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
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
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    showBlockTypes
  } = dispatch(store_store);
  return {
    enableAllBlockTypes: blockTypes => {
      const blockNames = blockTypes.map(({
        name
      }) => name);
      showBlockTypes(blockNames);
    }
  };
})])(BlockManager));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/index.js


/**
 * WordPress dependencies
=======


var VIEW_AS_LINK_SELECTOR = '#wp-admin-bar-view a';
var effects = {
  SET_META_BOXES_PER_LOCATIONS: function SET_META_BOXES_PER_LOCATIONS(action, store) {
    // Allow toggling metaboxes panels
    // We need to wait for all scripts to load
    // If the meta box loads the post script, it will already trigger this.
    // After merge in Core, make sure to drop the timeout and update the postboxes script
    // to avoid the double binding.
    setTimeout(function () {
      var postType = Object(external_this_wp_data_["select"])('core/editor').getCurrentPostType();

      if (window.postboxes.page !== postType) {
        window.postboxes.add_postbox_toggles(postType);
      }
    });
    var wasSavingPost = Object(external_this_wp_data_["select"])('core/editor').isSavingPost();
    var wasAutosavingPost = Object(external_this_wp_data_["select"])('core/editor').isAutosavingPost();
    var wasPreviewingPost = Object(external_this_wp_data_["select"])('core/editor').isPreviewingPost(); // Save metaboxes when performing a full save on the post.

    Object(external_this_wp_data_["subscribe"])(function () {
      var isSavingPost = Object(external_this_wp_data_["select"])('core/editor').isSavingPost();
      var isAutosavingPost = Object(external_this_wp_data_["select"])('core/editor').isAutosavingPost();
      var isPreviewingPost = Object(external_this_wp_data_["select"])('core/editor').isPreviewingPost();
      var hasActiveMetaBoxes = Object(external_this_wp_data_["select"])('core/edit-post').hasMetaBoxes(); // Save metaboxes on save completion, except for autosaves that are not a post preview.

      var shouldTriggerMetaboxesSave = hasActiveMetaBoxes && (wasSavingPost && !isSavingPost && !wasAutosavingPost || wasAutosavingPost && wasPreviewingPost && !isPreviewingPost); // Save current state for next inspection.

      wasSavingPost = isSavingPost;
      wasAutosavingPost = isAutosavingPost;
      wasPreviewingPost = isPreviewingPost;

      if (shouldTriggerMetaboxesSave) {
        store.dispatch(requestMetaBoxUpdates());
      }
    });
  },
  REQUEST_META_BOX_UPDATES: function REQUEST_META_BOX_UPDATES(action, store) {
    // Saves the wp_editor fields
    if (window.tinyMCE) {
      window.tinyMCE.triggerSave();
    }

    var state = store.getState(); // Additional data needed for backward compatibility.
    // If we do not provide this data, the post will be overridden with the default values.

    var post = Object(external_this_wp_data_["select"])('core/editor').getCurrentPost(state);
    var additionalData = [post.comment_status ? ['comment_status', post.comment_status] : false, post.ping_status ? ['ping_status', post.ping_status] : false, post.sticky ? ['sticky', post.sticky] : false, ['post_author', post.author]].filter(Boolean); // We gather all the metaboxes locations data and the base form data

    var baseFormData = new window.FormData(document.querySelector('.metabox-base-form'));
    var formDataToMerge = [baseFormData].concat(Object(toConsumableArray["a" /* default */])(getActiveMetaBoxLocations(state).map(function (location) {
      return new window.FormData(getMetaBoxContainer(location));
    }))); // Merge all form data objects into a single one.

    var formData = Object(external_lodash_["reduce"])(formDataToMerge, function (memo, currentFormData) {
      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        for (var _iterator = currentFormData[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
          var _step$value = Object(slicedToArray["a" /* default */])(_step.value, 2),
              key = _step$value[0],
              value = _step$value[1];

          memo.append(key, value);
        }
      } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion && _iterator.return != null) {
            _iterator.return();
          }
        } finally {
          if (_didIteratorError) {
            throw _iteratorError;
          }
        }
      }

      return memo;
    }, new window.FormData());
    additionalData.forEach(function (_ref) {
      var _ref2 = Object(slicedToArray["a" /* default */])(_ref, 2),
          key = _ref2[0],
          value = _ref2[1];

      return formData.append(key, value);
    }); // Save the metaboxes

    external_this_wp_apiFetch_default()({
      url: window._wpMetaBoxUrl,
      method: 'POST',
      body: formData,
      parse: false
    }).then(function () {
      return store.dispatch(metaBoxUpdatesSuccess());
    });
  },
  SWITCH_MODE: function SWITCH_MODE(action) {
    // Unselect blocks when we switch to the code editor.
    if (action.mode !== 'visual') {
      Object(external_this_wp_data_["dispatch"])('core/editor').clearSelectedBlock();
    }

    var message = action.mode === 'visual' ? Object(external_this_wp_i18n_["__"])('Visual editor selected') : Object(external_this_wp_i18n_["__"])('Code editor selected');
    Object(external_this_wp_a11y_["speak"])(message, 'assertive');
  },
  INIT: function INIT(_, store) {
    // Select the block settings tab when the selected block changes
    Object(external_this_wp_data_["subscribe"])(onChangeListener(function () {
      return !!Object(external_this_wp_data_["select"])('core/editor').getBlockSelectionStart();
    }, function (hasBlockSelection) {
      if (!Object(external_this_wp_data_["select"])('core/edit-post').isEditorSidebarOpened()) {
        return;
      }

      if (hasBlockSelection) {
        store.dispatch(actions_openGeneralSidebar('edit-post/block'));
      } else {
        store.dispatch(actions_openGeneralSidebar('edit-post/document'));
      }
    }));

    var isMobileViewPort = function isMobileViewPort() {
      return Object(external_this_wp_data_["select"])('core/viewport').isViewportMatch('< medium');
    };

    var adjustSidebar = function () {
      // contains the sidebar we close when going to viewport sizes lower than medium.
      // This allows to reopen it when going again to viewport sizes greater than medium.
      var sidebarToReOpenOnExpand = null;
      return function (isSmall) {
        if (isSmall) {
          sidebarToReOpenOnExpand = getActiveGeneralSidebarName(store.getState());

          if (sidebarToReOpenOnExpand) {
            store.dispatch(actions_closeGeneralSidebar());
          }
        } else if (sidebarToReOpenOnExpand && !getActiveGeneralSidebarName(store.getState())) {
          store.dispatch(actions_openGeneralSidebar(sidebarToReOpenOnExpand));
        }
      };
    }();

    adjustSidebar(isMobileViewPort()); // Collapse sidebar when viewport shrinks.
    // Reopen sidebar it if viewport expands and it was closed because of a previous shrink.

    Object(external_this_wp_data_["subscribe"])(onChangeListener(isMobileViewPort, adjustSidebar)); // Update View as link when currentPost link changes

    var updateViewAsLink = function updateViewAsLink(newPermalink) {
      if (!newPermalink) {
        return;
      }

      var nodeToUpdate = document.querySelector(VIEW_AS_LINK_SELECTOR);

      if (!nodeToUpdate) {
        return;
      }

      nodeToUpdate.setAttribute('href', newPermalink);
    };

    Object(external_this_wp_data_["subscribe"])(onChangeListener(function () {
      return Object(external_this_wp_data_["select"])('core/editor').getCurrentPost().link;
    }, updateViewAsLink));
  }
};
/* harmony default export */ var store_effects = (effects);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/middlewares.js


/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Applies the custom middlewares used specifically in the editor module.
 *
 * @param {Object} store Store Object.
 *
 * @return {Object} Update Store Object.
 */

function applyMiddlewares(store) {
  var middlewares = [refx_default()(store_effects)];

  var enhancedDispatch = function enhancedDispatch() {
    throw new Error('Dispatching while constructing your middleware is not allowed. ' + 'Other middleware would not be applied to this dispatch.');
  };

  var chain = [];
  var middlewareAPI = {
    getState: store.getState,
    dispatch: function dispatch() {
      return enhancedDispatch.apply(void 0, arguments);
    }
  };
  chain = middlewares.map(function (middleware) {
    return middleware(middlewareAPI);
  });
  enhancedDispatch = external_lodash_["flowRight"].apply(void 0, Object(toConsumableArray["a" /* default */])(chain))(store.dispatch);
  store.dispatch = enhancedDispatch;
  return store;
}

/* harmony default export */ var store_middlewares = (applyMiddlewares);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/index.js
/**
 * WordPress Dependencies
 */

/**
 * Internal dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */





<<<<<<< HEAD


/**
 * Internal dependencies
 */





const PREFERENCES_MODAL_NAME = 'edit-post/preferences';
function EditPostPreferencesModal() {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');
  const {
    closeModal
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const [isModalActive, showBlockBreadcrumbsOption] = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings
    } = select(external_wp_editor_namespaceObject.store);
    const {
      getEditorMode,
      isFeatureActive
    } = select(store_store);
    const modalActive = select(store).isModalActive(PREFERENCES_MODAL_NAME);
    const mode = getEditorMode();
    const isRichEditingEnabled = getEditorSettings().richEditingEnabled;
    const isDistractionFreeEnabled = isFeatureActive('distractionFree');
    return [modalActive, !isDistractionFreeEnabled && isLargeViewport && isRichEditingEnabled && mode === 'visual', isDistractionFreeEnabled];
  }, [isLargeViewport]);
  const {
    closeGeneralSidebar,
    setIsListViewOpened,
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    set: setPreference
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);

  const toggleDistractionFree = () => {
    setPreference('core/edit-post', 'fixedToolbar', false);
    setIsInserterOpened(false);
    setIsListViewOpened(false);
    closeGeneralSidebar();
  };

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
      featureName: "distractionFree",
      onToggle: toggleDistractionFree,
      help: (0,external_wp_i18n_namespaceObject.__)('Reduce visual distractions by hiding the toolbar and other elements to focus on writing.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Distraction free')
    }), (0,external_wp_element_namespaceObject.createElement)(enable_feature, {
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
        label: taxonomy.labels.menu_name,
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
=======
var store_store = Object(external_this_wp_data_["registerStore"])('core/edit-post', {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
});
store_middlewares(store_store);
store_store.dispatch({
  type: 'INIT'
});
/* harmony default export */ var build_module_store = (store_store);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/prevent-event-discovery.js
/* harmony default export */ var prevent_event_discovery = ({
  't a l e s o f g u t e n b e r g': function tALESOFGUTENBERG(event) {
    if (!document.activeElement.classList.contains('edit-post-visual-editor') && document.activeElement !== document.body) {
      return;
    }

    event.preventDefault();
    window.wp.data.dispatch('core/editor').insertBlock(window.wp.blocks.createBlock('core/paragraph', {
      content: '🐡🐢🦀🐤🦋🐘🐧🐹🦁🦄🦍🐼🐿🎃🐴🐝🐆🦕🦔🌱🍇π🍌🐉💧🥨🌌🍂🍠🥦🥚🥝🎟🥥🥒🛵🥖🍒🍯🎾🎲🐺🐚🐮⌛️'
    }));
  }
});

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__("Mmq9");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/browser-url/index.js






>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */



<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Returns the Post's Edit URL.
 *
 * @param {number} postId Post ID.
 *
 * @return {string} Post edit URL.
 */

function getPostEditURL(postId) {
<<<<<<< HEAD
  return (0,external_wp_url_namespaceObject.addQueryArgs)('post.php', {
=======
  return Object(external_this_wp_url_["addQueryArgs"])('post.php', {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    post: postId,
    action: 'edit'
  });
}
/**
 * Returns the Post's Trashed URL.
 *
<<<<<<< HEAD
 * @param {number} postId   Post ID.
=======
 * @param {number} postId    Post ID.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * @param {string} postType Post Type.
 *
 * @return {string} Post trashed URL.
 */

function getPostTrashedURL(postId, postType) {
<<<<<<< HEAD
  return (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
=======
  return Object(external_this_wp_url_["addQueryArgs"])('edit.php', {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    trashed: 1,
    post_type: postType,
    ids: postId
  });
}
<<<<<<< HEAD
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
=======
var browser_url_BrowserURL =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(BrowserURL, _Component);

  function BrowserURL() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, BrowserURL);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(BrowserURL).apply(this, arguments));
    _this.state = {
      historyId: null
    };
    return _this;
  }

  Object(createClass["a" /* default */])(BrowserURL, [{
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this$props = this.props,
          postId = _this$props.postId,
          postStatus = _this$props.postStatus,
          postType = _this$props.postType;
      var historyId = this.state.historyId;

      if (postStatus === 'trash') {
        this.setTrashURL(postId, postType);
        return;
      }

      if ((postId !== prevProps.postId || postId !== historyId) && postStatus !== 'auto-draft') {
        this.setBrowserURL(postId);
      }
    }
    /**
     * Navigates the browser to the post trashed URL to show a notice about the trashed post.
     *
     * @param {number} postId    Post ID.
     * @param {string} postType  Post Type.
     */

  }, {
    key: "setTrashURL",
    value: function setTrashURL(postId, postType) {
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

  }, {
    key: "setBrowserURL",
    value: function setBrowserURL(postId) {
      window.history.replaceState({
        id: postId
      }, 'Post ' + postId, getPostEditURL(postId));
      this.setState(function () {
        return {
          historyId: postId
        };
      });
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return BrowserURL;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var browser_url = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getCurrentPost = _select.getCurrentPost;

  var _getCurrentPost = getCurrentPost(),
      id = _getCurrentPost.id,
      status = _getCurrentPost.status,
      type = _getCurrentPost.type;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  return {
    postId: id,
    postStatus: status,
<<<<<<< HEAD
    postType: type,
    isSavingPost: isSavingPost()
  };
})(BrowserURL));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/wordpress.js


=======
    postType: type
  };
})(browser_url_BrowserURL));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/keyboard-shortcuts.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD
const wordpress = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z"
}));
/* harmony default export */ const library_wordpress = (wordpress);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/fullscreen-mode-close/index.js


/**
 * External dependencies
 */
=======
/* harmony default export */ var keyboard_shortcuts = ({
  toggleEditorMode: {
    raw: external_this_wp_keycodes_["rawShortcut"].secondary('m'),
    display: external_this_wp_keycodes_["displayShortcut"].secondary('m')
  },
  toggleSidebar: {
    raw: external_this_wp_keycodes_["rawShortcut"].primaryShift(','),
    display: external_this_wp_keycodes_["displayShortcut"].primaryShift(','),
    ariaLabel: external_this_wp_keycodes_["shortcutAriaLabel"].primaryShift(',')
  }
});

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/mode-switcher/index.js


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * WordPress dependencies
 */




<<<<<<< HEAD





=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD

function FullscreenModeClose({
  showTooltip,
  icon,
  href
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
    label: (_postType$labels$view = postType?.labels?.view_items) !== null && _postType$labels$view !== void 0 ? _postType$labels$view : (0,external_wp_i18n_namespaceObject.__)('Back'),
    showTooltip: showTooltip
  }, buttonIcon));
}

/* harmony default export */ const fullscreen_mode_close = (FullscreenModeClose);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/list-view.js


/**
 * WordPress dependencies
 */

const listView = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M3 6h11v1.5H3V6Zm3.5 5.5h11V13h-11v-1.5ZM21 17H10v1.5h11V17Z"
}));
/* harmony default export */ const list_view = (listView);

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
/* harmony default export */ const library_plus = (plus);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/header-toolbar/index.js


=======
/**
 * Set of available mode options.
 *
 * @type {Array}
 */

var MODES = [{
  value: 'visual',
  label: Object(external_this_wp_i18n_["__"])('Visual Editor')
}, {
  value: 'text',
  label: Object(external_this_wp_i18n_["__"])('Code Editor')
}];

function ModeSwitcher(_ref) {
  var onSwitch = _ref.onSwitch,
      mode = _ref.mode;
  var choices = MODES.map(function (choice) {
    if (choice.value !== mode) {
      return Object(objectSpread["a" /* default */])({}, choice, {
        shortcut: keyboard_shortcuts.toggleEditorMode.display
      });
    }

    return choice;
  });
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
    label: Object(external_this_wp_i18n_["__"])('Editor')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItemsChoice"], {
    choices: choices,
    value: mode,
    onSelect: onSwitch
  }));
}

/* harmony default export */ var mode_switcher = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isRichEditingEnabled: select('core/editor').getEditorSettings().richEditingEnabled,
    mode: select('core/edit-post').getEditorMode()
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isRichEditingEnabled = _ref2.isRichEditingEnabled;
  return isRichEditingEnabled;
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  return {
    onSwitch: function onSwitch(mode) {
      dispatch('core/edit-post').switchEditorMode(mode);
      ownProps.onSelect(mode);
    }
  };
})])(ModeSwitcher));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugins-more-menu-group/index.js


/**
 * External dependencies
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */




<<<<<<< HEAD






/**
 * Internal dependencies
=======
var plugins_more_menu_group_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginsMoreMenuGroup'),
    PluginsMoreMenuGroup = plugins_more_menu_group_createSlotFill.Fill,
    plugins_more_menu_group_Slot = plugins_more_menu_group_createSlotFill.Slot;

PluginsMoreMenuGroup.Slot = function (_ref) {
  var fillProps = _ref.fillProps;
  return Object(external_this_wp_element_["createElement"])(plugins_more_menu_group_Slot, {
    fillProps: fillProps
  }, function (fills) {
    return !Object(external_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
      label: Object(external_this_wp_i18n_["__"])('Plugins')
    }, fills);
  });
};

/* harmony default export */ var plugins_more_menu_group = (PluginsMoreMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/options-menu-item/index.js


/**
 * WordPress Dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */



<<<<<<< HEAD
const {
  useShouldContextualToolbarShow
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

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
    get: getPreference
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_preferences_namespaceObject.store);
  const hasFixedToolbar = getPreference('core/edit-post', 'fixedToolbar');
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
  const {
    shouldShowContextualToolbar,
    canFocusHiddenToolbar,
    fixedToolbarCanBeFocused
  } = useShouldContextualToolbarShow(); // If there's a block toolbar to be focused, disable the focus shortcut for the document toolbar.
  // There's a fixed block toolbar when the fixed toolbar option is enabled or when the browser width is less than the large viewport.

  const blockToolbarCanBeFocused = shouldShowContextualToolbar || canFocusHiddenToolbar || fixedToolbarCanBeFocused;
  /* translators: accessibility text for the editor toolbar */

  const toolbarAriaLabel = (0,external_wp_i18n_namespaceObject.__)('Document tools');

  const toggleListView = (0,external_wp_element_namespaceObject.useCallback)(() => setIsListViewOpened(!isListViewOpen), [setIsListViewOpened, isListViewOpen]);
  const overflowItems = (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_components_namespaceObject.Button,
    className: "edit-post-header-toolbar__document-overview-toggle",
    icon: list_view,
    disabled: isTextModeEnabled,
    isPressed: isListViewOpen
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: (0,external_wp_i18n_namespaceObject.__)('Document Overview'),
    onClick: toggleListView,
    shortcut: listViewShortcut,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }));
  const toggleInserter = (0,external_wp_element_namespaceObject.useCallback)(() => {
    if (isInserterOpened) {
      // Focusing the inserter button should close the inserter popover.
      // However, there are some cases it won't close when the focus is lost.
      // See https://github.com/WordPress/gutenberg/issues/43090 for more details.
      inserterButton.current.focus();
      setIsInserterOpened(false);
    } else {
      setIsInserterOpened(true);
    }
  }, [isInserterOpened, setIsInserterOpened]);
  /* translators: button label text should, if possible, be under 16 characters. */

  const longLabel = (0,external_wp_i18n_namespaceObject._x)('Toggle block inserter', 'Generic label for block inserter button');

  const shortLabel = !isInserterOpened ? (0,external_wp_i18n_namespaceObject.__)('Add') : (0,external_wp_i18n_namespaceObject.__)('Close');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.NavigableToolbar, {
    className: "edit-post-header-toolbar",
    "aria-label": toolbarAriaLabel,
    shouldUseKeyboardFocusShortcut: !blockToolbarCanBeFocused
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-header-toolbar__left"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    ref: inserterButton,
    as: external_wp_components_namespaceObject.Button,
    className: "edit-post-header-toolbar__inserter-toggle",
    variant: "primary",
    isPressed: isInserterOpened,
    onMouseDown: preventDefault,
    onClick: toggleInserter,
    disabled: !isInserterEnabled,
    icon: library_plus,
    label: showIconLabels ? shortLabel : longLabel,
    showTooltip: !showIconLabels
  }), (isWideViewport || !showIconLabels) && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isLargeViewport && !hasFixedToolbar && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
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

/* harmony default export */ const header_toolbar = (HeaderToolbar);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/mode-switcher/index.js


/**
 * WordPress dependencies
=======
function OptionsMenuItem(_ref) {
  var openModal = _ref.openModal,
      onSelect = _ref.onSelect;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
      onSelect();
      openModal('edit-post/options');
    }
  }, Object(external_this_wp_i18n_["__"])('Options'));
}
/* harmony default export */ var options_menu_item = (Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openModal = _dispatch.openModal;

  return {
    openModal: openModal
  };
})(OptionsMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/feature-toggle/index.js


/**
 * WordPress Dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */




<<<<<<< HEAD

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

/* harmony default export */ const mode_switcher = (ModeSwitcher);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/preferences-menu-item/index.js
=======
function FeatureToggle(_ref) {
  var onToggle = _ref.onToggle,
      isActive = _ref.isActive,
      label = _ref.label,
      info = _ref.info;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    icon: isActive && 'yes',
    isSelected: isActive,
    onClick: onToggle,
    role: "menuitemcheckbox",
    label: label,
    info: info
  }, label);
}

/* harmony default export */ var feature_toggle = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var feature = _ref2.feature;
  return {
    isActive: select('core/edit-post').isFeatureActive(feature)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  return {
    onToggle: function onToggle() {
      dispatch('core/edit-post').toggleFeature(ownProps.feature);
      ownProps.onToggle();
    }
  };
})])(FeatureToggle));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/writing-menu/index.js


/**
 * WordPress Dependencies
 */



/**
 * Internal dependencies
 */



function WritingMenu(_ref) {
  var onClose = _ref.onClose;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
    label: Object(external_this_wp_i18n_["_x"])('View', 'noun')
  }, Object(external_this_wp_element_["createElement"])(feature_toggle, {
    feature: "fixedToolbar",
    label: Object(external_this_wp_i18n_["__"])('Top Toolbar'),
    info: Object(external_this_wp_i18n_["__"])('Access all block and document tools in a single place'),
    onToggle: onClose
  }), Object(external_this_wp_element_["createElement"])(feature_toggle, {
    feature: "focusMode",
    label: Object(external_this_wp_i18n_["__"])('Spotlight Mode'),
    info: Object(external_this_wp_i18n_["__"])('Focus on one block at a time'),
    onToggle: onClose
  }), Object(external_this_wp_element_["createElement"])(feature_toggle, {
    feature: "fullscreenMode",
    label: Object(external_this_wp_i18n_["__"])('Fullscreen Mode'),
    info: Object(external_this_wp_i18n_["__"])('Work without distraction'),
    onToggle: onClose
  }));
}

/* harmony default export */ var writing_menu = (Object(external_this_wp_viewport_["ifViewportMatches"])('medium')(WritingMenu));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/more-menu/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */



<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD
function PreferencesMenuItem() {
  const {
    openModal
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      openModal(PREFERENCES_MODAL_NAME);
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Preferences'));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/writing-menu/index.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
=======





var ariaClosed = Object(external_this_wp_i18n_["__"])('Show more tools & options');

var ariaOpen = Object(external_this_wp_i18n_["__"])('Hide more tools & options');

var more_menu_MoreMenu = function MoreMenu() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dropdown"], {
    className: "edit-post-more-menu",
    contentClassName: "edit-post-more-menu__content",
    position: "bottom left",
    renderToggle: function renderToggle(_ref) {
      var isOpen = _ref.isOpen,
          onToggle = _ref.onToggle;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        icon: "ellipsis",
        label: isOpen ? ariaOpen : ariaClosed,
        labelPosition: "bottom",
        onClick: onToggle,
        "aria-expanded": isOpen
      });
    },
    renderContent: function renderContent(_ref2) {
      var onClose = _ref2.onClose;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(writing_menu, {
        onClose: onClose
      }), Object(external_this_wp_element_["createElement"])(mode_switcher, {
        onSelect: onClose
      }), Object(external_this_wp_element_["createElement"])(plugins_more_menu_group.Slot, {
        fillProps: {
          onClose: onClose
        }
      }), Object(external_this_wp_element_["createElement"])(tools_more_menu_group.Slot, {
        fillProps: {
          onClose: onClose
        }
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], null, Object(external_this_wp_element_["createElement"])(options_menu_item, {
        onSelect: onClose
      })));
    }
  });
};

/* harmony default export */ var more_menu = (more_menu_MoreMenu);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/fullscreen-mode-close/index.js


/**
 * External Dependencies
 */

/**
 * WordPress Dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */



<<<<<<< HEAD
function WritingMenu() {
  const registry = (0,external_wp_data_namespaceObject.useRegistry)();
  const isDistractionFree = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).getSettings().isDistractionFree, []);
  const {
    setIsInserterOpened,
    setIsListViewOpened,
    closeGeneralSidebar
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    set: setPreference
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);

  const toggleDistractionFree = () => {
    registry.batch(() => {
      setPreference('core/edit-post', 'fixedToolbar', false);
      setIsInserterOpened(false);
      setIsListViewOpened(false);
      closeGeneralSidebar();
    });
  };

  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');

  if (!isLargeViewport) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    label: (0,external_wp_i18n_namespaceObject._x)('View', 'noun')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    disabled: isDistractionFree,
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
    info: (0,external_wp_i18n_namespaceObject.__)('Show and hide admin UI'),
    messageActivated: (0,external_wp_i18n_namespaceObject.__)('Fullscreen mode activated'),
    messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Fullscreen mode deactivated'),
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.secondary('f')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    name: "distractionFree",
    onToggle: toggleDistractionFree,
    label: (0,external_wp_i18n_namespaceObject.__)('Distraction free'),
    info: (0,external_wp_i18n_namespaceObject.__)('Write with calmness'),
    messageActivated: (0,external_wp_i18n_namespaceObject.__)('Distraction free mode activated'),
    messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Distraction free mode deactivated'),
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.primaryShift('\\')
  }));
}

/* harmony default export */ const writing_menu = (WritingMenu);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/more-menu/index.js
=======



function FullscreenModeClose(_ref) {
  var isActive = _ref.isActive,
      postType = _ref.postType;

  if (!isActive || !postType) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
    className: "edit-post-fullscreen-mode-close__toolbar"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: "exit",
    href: Object(external_this_wp_url_["addQueryArgs"])('edit.php', {
      post_type: postType.slug
    }),
    label: Object(external_lodash_["get"])(postType, ['labels', 'view_items'], Object(external_this_wp_i18n_["__"])('View Posts'))
  }));
}

/* harmony default export */ var fullscreen_mode_close = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getCurrentPostType = _select.getCurrentPostType;

  var _select2 = select('core/edit-post'),
      isFeatureActive = _select2.isFeatureActive;

  var _select3 = select('core'),
      getPostType = _select3.getPostType;

  return {
    isActive: isFeatureActive('fullscreenMode'),
    postType: getPostType(getCurrentPostType())
  };
})(FullscreenModeClose));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/header-toolbar/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */




<<<<<<< HEAD
=======


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */



<<<<<<< HEAD



const MoreMenu = ({
  showIconLabels
}) => {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('large');
  return (0,external_wp_element_namespaceObject.createElement)(MoreMenuDropdown, {
    toggleProps: {
      showTooltip: !showIconLabels,
      ...(showIconLabels && {
        variant: 'tertiary'
      })
    }
  }, ({
    onClose
  }) => (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, showIconLabels && !isLargeViewport && (0,external_wp_element_namespaceObject.createElement)(pinned_items.Slot, {
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
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_wp_element_namespaceObject.createElement)(PreferencesMenuItem, null))));
};

/* harmony default export */ const more_menu = (MoreMenu);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/post-publish-button-or-toggle.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function PostPublishButtonOrToggle({
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
}) {
  const IS_TOGGLE = 'toggle';
  const IS_BUTTON = 'button';
  const isSmallerThanMediumViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  let component;
=======
function HeaderToolbar(_ref) {
  var hasFixedToolbar = _ref.hasFixedToolbar,
      isLargeViewport = _ref.isLargeViewport,
      showInserter = _ref.showInserter;
  var toolbarAriaLabel = hasFixedToolbar ?
  /* translators: accessibility text for the editor toolbar when Top Toolbar is on */
  Object(external_this_wp_i18n_["__"])('Document and block tools') :
  /* translators: accessibility text for the editor toolbar when Top Toolbar is off */
  Object(external_this_wp_i18n_["__"])('Document tools');
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["NavigableToolbar"], {
    className: "edit-post-header-toolbar",
    "aria-label": toolbarAriaLabel
  }, Object(external_this_wp_element_["createElement"])(fullscreen_mode_close, null), Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["Inserter"], {
    disabled: !showInserter,
    position: "bottom right"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_nux_["DotTip"], {
    tipId: "core/editor.inserter"
  }, Object(external_this_wp_i18n_["__"])('Welcome to the wonderful world of blocks! Click the “+” (“Add block”) button to add a new block. There are blocks available for all kinds of content: you can insert text, headings, images, lists, and lots more!'))), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorHistoryUndo"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorHistoryRedo"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["TableOfContents"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["BlockNavigationDropdown"], null), hasFixedToolbar && isLargeViewport && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-header-toolbar__block-toolbar"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["BlockToolbar"], null)));
}

/* harmony default export */ var header_toolbar = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
    showInserter: select('core/edit-post').getEditorMode() === 'visual' && select('core/editor').getEditorSettings().richEditingEnabled
  };
}), Object(external_this_wp_viewport_["withViewportMatch"])({
  isLargeViewport: 'medium'
})])(HeaderToolbar));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/pinned-plugins/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



var pinned_plugins_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PinnedPlugins'),
    PinnedPlugins = pinned_plugins_createSlotFill.Fill,
    pinned_plugins_Slot = pinned_plugins_createSlotFill.Slot;

PinnedPlugins.Slot = function (props) {
  return Object(external_this_wp_element_["createElement"])(pinned_plugins_Slot, props, function (fills) {
    return !Object(external_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-pinned-plugins"
    }, fills);
  });
};

/* harmony default export */ var pinned_plugins = (PinnedPlugins);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/post-publish-button-or-toggle.js


/**
 * External Dependencies
 */

/**
 * WordPress dependencies.
 */





function PostPublishButtonOrToggle(_ref) {
  var forceIsDirty = _ref.forceIsDirty,
      forceIsSaving = _ref.forceIsSaving,
      hasPublishAction = _ref.hasPublishAction,
      isBeingScheduled = _ref.isBeingScheduled,
      isLessThanMediumViewport = _ref.isLessThanMediumViewport,
      isPending = _ref.isPending,
      isPublished = _ref.isPublished,
      isPublishSidebarEnabled = _ref.isPublishSidebarEnabled,
      isPublishSidebarOpened = _ref.isPublishSidebarOpened,
      isScheduled = _ref.isScheduled,
      togglePublishSidebar = _ref.togglePublishSidebar;
  var IS_TOGGLE = 'toggle';
  var IS_BUTTON = 'button';
  var component;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  /**
   * Conditions to show a BUTTON (publish directly) or a TOGGLE (open publish sidebar):
   *
   * 1) We want to show a BUTTON when the post status is at the _final stage_
<<<<<<< HEAD
   * for a particular role (see https://wordpress.org/documentation/article/post-status/):
=======
   * for a particular role (see https://codex.wordpress.org/Post_Status):
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
  if (isPublished || isScheduled && isBeingScheduled || isPending && !hasPublishAction && !isSmallerThanMediumViewport) {
    component = IS_BUTTON;
  } else if (isSmallerThanMediumViewport) {
=======
  if (isPublished || isScheduled && isBeingScheduled || isPending && !hasPublishAction && !isLessThanMediumViewport) {
    component = IS_BUTTON;
  } else if (isLessThanMediumViewport) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    component = IS_TOGGLE;
  } else if (isPublishSidebarEnabled) {
    component = IS_TOGGLE;
  } else {
    component = IS_BUTTON;
  }

<<<<<<< HEAD
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPublishButton, {
=======
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPublishButton"], {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    forceIsDirty: forceIsDirty,
    forceIsSaving: forceIsSaving,
    isOpen: isPublishSidebarOpened,
    isToggle: component === IS_TOGGLE,
<<<<<<< HEAD
    onToggle: togglePublishSidebar,
    setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
  });
}
/* harmony default export */ const post_publish_button_or_toggle = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)(select => {
  var _select$getCurrentPos;

  return {
    hasPublishAction: (_select$getCurrentPos = select(external_wp_editor_namespaceObject.store).getCurrentPost()?._links?.['wp:action-publish']) !== null && _select$getCurrentPos !== void 0 ? _select$getCurrentPos : false,
    isBeingScheduled: select(external_wp_editor_namespaceObject.store).isEditedPostBeingScheduled(),
    isPending: select(external_wp_editor_namespaceObject.store).isCurrentPostPending(),
    isPublished: select(external_wp_editor_namespaceObject.store).isCurrentPostPublished(),
    isPublishSidebarEnabled: select(external_wp_editor_namespaceObject.store).isPublishSidebarEnabled(),
    isPublishSidebarOpened: select(store_store).isPublishSidebarOpened(),
    isScheduled: select(external_wp_editor_namespaceObject.store).isCurrentPostScheduled()
  };
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    togglePublishSidebar
  } = dispatch(store_store);
  return {
    togglePublishSidebar
  };
}))(PostPublishButtonOrToggle));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/device-preview/index.js
=======
    onToggle: togglePublishSidebar
  });
}
/* harmony default export */ var post_publish_button_or_toggle = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    hasPublishAction: Object(external_lodash_["get"])(select('core/editor').getCurrentPost(), ['_links', 'wp:action-publish'], false),
    isBeingScheduled: select('core/editor').isEditedPostBeingScheduled(),
    isPending: select('core/editor').isCurrentPostPending(),
    isPublished: select('core/editor').isCurrentPostPublished(),
    isPublishSidebarEnabled: select('core/editor').isPublishSidebarEnabled(),
    isPublishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
    isScheduled: select('core/editor').isCurrentPostScheduled()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      togglePublishSidebar = _dispatch.togglePublishSidebar;

  return {
    togglePublishSidebar: togglePublishSidebar
  };
}), Object(external_this_wp_viewport_["withViewportMatch"])({
  isLessThanMediumViewport: '< medium'
}))(PostPublishButtonOrToggle));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */






<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD
function DevicePreview() {
  const {
    hasActiveMetaboxes,
    isPostSaveable,
    isSaving,
    isViewable,
    deviceType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _postType$viewable;

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
      isViewable: (_postType$viewable = postType?.viewable) !== null && _postType$viewable !== void 0 ? _postType$viewable : false,
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
    setDeviceType: setPreviewDeviceType,
    label: (0,external_wp_i18n_namespaceObject.__)('Preview')
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/view-link/index.js
=======





function Header(_ref) {
  var closeGeneralSidebar = _ref.closeGeneralSidebar,
      hasActiveMetaboxes = _ref.hasActiveMetaboxes,
      isEditorSidebarOpened = _ref.isEditorSidebarOpened,
      isPublishSidebarOpened = _ref.isPublishSidebarOpened,
      isSaving = _ref.isSaving,
      openGeneralSidebar = _ref.openGeneralSidebar;
  var toggleGeneralSidebar = isEditorSidebarOpened ? closeGeneralSidebar : openGeneralSidebar;
  return Object(external_this_wp_element_["createElement"])("div", {
    role: "region"
    /* translators: accessibility text for the top bar landmark region. */
    ,
    "aria-label": Object(external_this_wp_i18n_["__"])('Editor top bar'),
    className: "edit-post-header",
    tabIndex: "-1"
  }, Object(external_this_wp_element_["createElement"])(header_toolbar, null), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-header__settings"
  }, !isPublishSidebarOpened && // This button isn't completely hidden by the publish sidebar.
  // We can't hide the whole toolbar when the publish sidebar is open because
  // we want to prevent mounting/unmounting the PostPublishButtonOrToggle DOM node.
  // We track that DOM node to return focus to the PostPublishButtonOrToggle
  // when the publish sidebar has been closed.
  Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSavedState"], {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPreviewButton"], {
    forceIsAutosaveable: hasActiveMetaboxes,
    forcePreviewLink: isSaving ? null : undefined
  }), Object(external_this_wp_element_["createElement"])(post_publish_button_or_toggle, {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving
  }), Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: "admin-generic",
    label: Object(external_this_wp_i18n_["__"])('Settings'),
    onClick: toggleGeneralSidebar,
    isToggled: isEditorSidebarOpened,
    "aria-expanded": isEditorSidebarOpened,
    shortcut: keyboard_shortcuts.toggleSidebar
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_nux_["DotTip"], {
    tipId: "core/editor.settings"
  }, Object(external_this_wp_i18n_["__"])('You’ll find more settings for your page and blocks in the sidebar. Click “Settings” to open it.'))), Object(external_this_wp_element_["createElement"])(pinned_plugins.Slot, null), Object(external_this_wp_element_["createElement"])(more_menu, null)));
}

/* harmony default export */ var header = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    hasActiveMetaboxes: select('core/edit-post').hasMetaBoxes(),
    isEditorSidebarOpened: select('core/edit-post').isEditorSidebarOpened(),
    isPublishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
    isSaving: select('core/edit-post').isSavingMetaBoxes()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps, _ref2) {
  var select = _ref2.select;

  var _select = select('core/editor'),
      getBlockSelectionStart = _select.getBlockSelectionStart;

  var _dispatch = dispatch('core/edit-post'),
      _openGeneralSidebar = _dispatch.openGeneralSidebar,
      closeGeneralSidebar = _dispatch.closeGeneralSidebar;

  return {
    openGeneralSidebar: function openGeneralSidebar() {
      return _openGeneralSidebar(getBlockSelectionStart() ? 'edit-post/block' : 'edit-post/document');
    },
    closeGeneralSidebar: closeGeneralSidebar
  };
}))(Header));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/text-editor/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */






<<<<<<< HEAD
function ViewLink() {
  const {
    permalink,
    isPublished,
    label
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    // Grab post type to retrieve the view_item label.
    const postTypeSlug = select(external_wp_editor_namespaceObject.store).getCurrentPostType();
    const postType = select(external_wp_coreData_namespaceObject.store).getPostType(postTypeSlug);
    return {
      permalink: select(external_wp_editor_namespaceObject.store).getPermalink(),
      isPublished: select(external_wp_editor_namespaceObject.store).isCurrentPostPublished(),
      label: postType?.labels.view_item
    };
  }, []); // Only render the view button if the post is published and has a permalink.

  if (!isPublished || !permalink) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    icon: library_external,
    label: label || (0,external_wp_i18n_namespaceObject.__)('View post'),
    href: permalink,
    target: "_blank"
  });
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

const main_dashboard_button_Slot = ({
  children
}) => {
  const fills = (0,external_wp_components_namespaceObject.__experimentalUseSlotFills)(slotName);
  const hasFills = Boolean(fills && fills.length);

  if (!hasFills) {
    return children;
  }

  return (0,external_wp_element_namespaceObject.createElement)(MainDashboardButtonSlot, {
    bubblesVirtually: true
  });
};

MainDashboardButton.Slot = main_dashboard_button_Slot;
/* harmony default export */ const main_dashboard_button = (MainDashboardButton);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-right-small.js


/**
 * WordPress dependencies
 */

const chevronRightSmall = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M10.8622 8.04053L14.2805 12.0286L10.8622 16.0167L9.72327 15.0405L12.3049 12.0286L9.72327 9.01672L10.8622 8.04053Z"
}));
/* harmony default export */ const chevron_right_small = (chevronRightSmall);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-left-small.js


/**
 * WordPress dependencies
 */

const chevronLeftSmall = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "m13.1 16-3.4-4 3.4-4 1.1 1-2.6 3 2.6 3-1.1 1z"
}));
/* harmony default export */ const chevron_left_small = (chevronLeftSmall);

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
/* harmony default export */ const library_layout = (layout);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/document-actions/index.js


/**
 * WordPress dependencies
 */






=======

function TextEditor(_ref) {
  var onExit = _ref.onExit,
      isRichEditingEnabled = _ref.isRichEditingEnabled;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-text-editor"
  }, isRichEditingEnabled && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-text-editor__toolbar"
  }, Object(external_this_wp_element_["createElement"])("h2", null, Object(external_this_wp_i18n_["__"])('Editing Code')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    onClick: onExit,
    icon: "no-alt",
    shortcut: external_this_wp_keycodes_["displayShortcut"].secondary('m')
  }, Object(external_this_wp_i18n_["__"])('Exit Code Editor'))), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-text-editor__body"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTitle"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTextEditor"], null)));
}

/* harmony default export */ var text_editor = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isRichEditingEnabled: select('core/editor').getEditorSettings().richEditingEnabled
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onExit: function onExit() {
      dispatch('core/edit-post').switchEditorMode('visual');
    }
  };
}))(TextEditor));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/block-inspector-button.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function BlockInspectorButton(_ref) {
  var areAdvancedSettingsOpened = _ref.areAdvancedSettingsOpened,
      closeSidebar = _ref.closeSidebar,
      openEditorSidebar = _ref.openEditorSidebar,
      _ref$onClick = _ref.onClick,
      onClick = _ref$onClick === void 0 ? external_lodash_["noop"] : _ref$onClick,
      _ref$small = _ref.small,
      small = _ref$small === void 0 ? false : _ref$small,
      speak = _ref.speak;

  var speakMessage = function speakMessage() {
    if (areAdvancedSettingsOpened) {
      speak(Object(external_this_wp_i18n_["__"])('Block settings closed'));
    } else {
      speak(Object(external_this_wp_i18n_["__"])('Additional settings are now available in the Editor block settings sidebar'));
    }
  };

  var label = areAdvancedSettingsOpened ? Object(external_this_wp_i18n_["__"])('Hide Block Settings') : Object(external_this_wp_i18n_["__"])('Show Block Settings');
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    className: "editor-block-settings-menu__control",
    onClick: Object(external_lodash_["flow"])(areAdvancedSettingsOpened ? closeSidebar : openEditorSidebar, speakMessage, onClick),
    icon: "admin-generic",
    label: small ? label : undefined,
    shortcut: keyboard_shortcuts.toggleSidebar
  }, !small && label);
}
/* harmony default export */ var block_inspector_button = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    areAdvancedSettingsOpened: select('core/edit-post').getActiveGeneralSidebarName() === 'edit-post/block'
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    openEditorSidebar: function openEditorSidebar() {
      return dispatch('core/edit-post').openGeneralSidebar('edit-post/block');
    },
    closeSidebar: dispatch('core/edit-post').closeGeneralSidebar
  };
}), external_this_wp_components_["withSpokenMessages"])(BlockInspectorButton));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-group.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





var plugin_block_settings_menu_group_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginBlockSettingsMenuGroup'),
    PluginBlockSettingsMenuGroup = plugin_block_settings_menu_group_createSlotFill.Fill,
    plugin_block_settings_menu_group_Slot = plugin_block_settings_menu_group_createSlotFill.Slot;

var plugin_block_settings_menu_group_PluginBlockSettingsMenuGroupSlot = function PluginBlockSettingsMenuGroupSlot(_ref) {
  var fillProps = _ref.fillProps,
      selectedBlocks = _ref.selectedBlocks;
  selectedBlocks = Object(external_lodash_["map"])(selectedBlocks, function (block) {
    return block.name;
  });
  return Object(external_this_wp_element_["createElement"])(plugin_block_settings_menu_group_Slot, {
    fillProps: Object(objectSpread["a" /* default */])({}, fillProps, {
      selectedBlocks: selectedBlocks
    })
  }, function (fills) {
    return !Object(external_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", {
      className: "editor-block-settings-menu__separator"
    }), fills);
  });
};

PluginBlockSettingsMenuGroup.Slot = Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var clientIds = _ref2.fillProps.clientIds;
  return {
    selectedBlocks: select('core/editor').getBlocksByClientId(clientIds)
  };
})(plugin_block_settings_menu_group_PluginBlockSettingsMenuGroupSlot);
/* harmony default export */ var plugin_block_settings_menu_group = (PluginBlockSettingsMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/index.js


/**
 * WordPress dependencies
 */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * Internal dependencies
 */



<<<<<<< HEAD
function DocumentActions() {
  const {
    template,
    isEditing
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isEditingTemplate,
      getEditedPostTemplate
    } = select(store_store);

    const _isEditing = isEditingTemplate();

    return {
      template: _isEditing ? getEditedPostTemplate() : null,
      isEditing: _isEditing
    };
  }, []);
  const {
    clearSelectedBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    setIsEditingTemplate
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    open: openCommandCenter
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_commands_namespaceObject.store);

  if (!isEditing || !template) {
    return null;
  }

  let templateTitle = (0,external_wp_i18n_namespaceObject.__)('Default');

  if (template?.title) {
    templateTitle = template.title;
  } else if (!!template) {
    templateTitle = template.slug;
  }

  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-document-actions"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-post-document-actions__back",
    onClick: () => {
      clearSelectedBlock();
      setIsEditingTemplate(false);
    },
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_right_small : chevron_left_small
  }, (0,external_wp_i18n_namespaceObject.__)('Back')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-post-document-actions__command",
    onClick: () => openCommandCenter()
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    className: "edit-post-document-actions__title",
    spacing: 1,
    justify: "center"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockIcon, {
    icon: library_layout
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    size: "body",
    as: "h1"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "span"
  }, (0,external_wp_i18n_namespaceObject.__)('Editing template: ')), templateTitle)), (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "edit-post-document-actions__shortcut"
  }, external_wp_keycodes_namespaceObject.displayShortcut.primary('k'))));
}

/* harmony default export */ const document_actions = (DocumentActions);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/index.js
=======

function VisualEditor() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["BlockSelectionClearer"], {
    className: "edit-post-visual-editor editor-styles-wrapper"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorGlobalKeyboardShortcuts"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["CopyHandler"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["MultiSelectScrollIntoView"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["WritingFlow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["ObserveTyping"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTitle"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["BlockList"], null))), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["_BlockSettingsMenuFirstItem"], null, function (_ref) {
    var onClose = _ref.onClose;
    return Object(external_this_wp_element_["createElement"])(block_inspector_button, {
      onClick: onClose
    });
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["_BlockSettingsMenuPluginsExtension"], null, function (_ref2) {
    var clientIds = _ref2.clientIds,
        onClose = _ref2.onClose;
    return Object(external_this_wp_element_["createElement"])(plugin_block_settings_menu_group.Slot, {
      fillProps: {
        clientIds: clientIds,
        onClose: onClose
      }
    });
  }));
}

/* harmony default export */ var visual_editor = (VisualEditor);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js







>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */




<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */



<<<<<<< HEAD







const slideY = {
  hidden: {
    y: '-50px'
  },
  hover: {
    y: 0,
    transition: {
      type: 'tween',
      delay: 0.2
    }
  }
};
const slideX = {
  hidden: {
    x: '-100%'
  },
  hover: {
    x: 0,
    transition: {
      type: 'tween',
      delay: 0.2
    }
  }
};

function Header({
  setEntitiesSavedStatesCallback
}) {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('large');
  const {
    hasActiveMetaboxes,
    isPublishSidebarOpened,
    isSaving,
    showIconLabels
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    hasActiveMetaboxes: select(store_store).hasMetaBoxes(),
    isPublishSidebarOpened: select(store_store).isPublishSidebarOpened(),
    isSaving: select(store_store).isSavingMetaBoxes(),
    showIconLabels: select(store_store).isFeatureActive('showIconLabels')
  }), []);
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-header"
  }, (0,external_wp_element_namespaceObject.createElement)(main_dashboard_button.Slot, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    variants: slideX,
    transition: {
      type: 'tween',
      delay: 0.8
    }
  }, (0,external_wp_element_namespaceObject.createElement)(fullscreen_mode_close, {
    showTooltip: true
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    variants: slideY,
    transition: {
      type: 'tween',
      delay: 0.8
    },
    className: "edit-post-header__toolbar"
  }, (0,external_wp_element_namespaceObject.createElement)(header_toolbar, null), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-header__center"
  }, (0,external_wp_element_namespaceObject.createElement)(document_actions, null))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    variants: slideY,
    transition: {
      type: 'tween',
      delay: 0.8
    },
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
  }), (0,external_wp_element_namespaceObject.createElement)(ViewLink, null), (0,external_wp_element_namespaceObject.createElement)(post_publish_button_or_toggle, {
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

/* harmony default export */ const header = (Header);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js


=======
var keyboard_shortcuts_EditorModeKeyboardShortcuts =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(EditorModeKeyboardShortcuts, _Component);

  function EditorModeKeyboardShortcuts() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, EditorModeKeyboardShortcuts);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(EditorModeKeyboardShortcuts).apply(this, arguments));
    _this.toggleMode = _this.toggleMode.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.toggleSidebar = _this.toggleSidebar.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(EditorModeKeyboardShortcuts, [{
    key: "toggleMode",
    value: function toggleMode() {
      var _this$props = this.props,
          mode = _this$props.mode,
          switchMode = _this$props.switchMode,
          isRichEditingEnabled = _this$props.isRichEditingEnabled;

      if (!isRichEditingEnabled) {
        return;
      }

      switchMode(mode === 'visual' ? 'text' : 'visual');
    }
  }, {
    key: "toggleSidebar",
    value: function toggleSidebar(event) {
      // This shortcut has no known clashes, but use preventDefault to prevent any
      // obscure shortcuts from triggering.
      event.preventDefault();
      var _this$props2 = this.props,
          isEditorSidebarOpen = _this$props2.isEditorSidebarOpen,
          closeSidebar = _this$props2.closeSidebar,
          openSidebar = _this$props2.openSidebar;

      if (isEditorSidebarOpen) {
        closeSidebar();
      } else {
        openSidebar();
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _ref;

      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["KeyboardShortcuts"], {
        bindGlobal: true,
        shortcuts: (_ref = {}, Object(defineProperty["a" /* default */])(_ref, keyboard_shortcuts.toggleEditorMode.raw, this.toggleMode), Object(defineProperty["a" /* default */])(_ref, keyboard_shortcuts.toggleSidebar.raw, this.toggleSidebar), _ref)
      });
    }
  }]);

  return EditorModeKeyboardShortcuts;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var components_keyboard_shortcuts = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isRichEditingEnabled: select('core/editor').getEditorSettings().richEditingEnabled,
    mode: select('core/edit-post').getEditorMode(),
    isEditorSidebarOpen: select('core/edit-post').isEditorSidebarOpened(),
    hasBlockSelection: !!select('core/editor').getBlockSelectionStart()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref2) {
  var hasBlockSelection = _ref2.hasBlockSelection;
  return {
    switchMode: function switchMode(mode) {
      dispatch('core/edit-post').switchEditorMode(mode);
    },
    openSidebar: function openSidebar() {
      var sidebarToOpen = hasBlockSelection ? 'edit-post/block' : 'edit-post/document';
      dispatch('core/edit-post').openGeneralSidebar(sidebarToOpen);
    },
    closeSidebar: dispatch('core/edit-post').closeGeneralSidebar
  };
})])(keyboard_shortcuts_EditorModeKeyboardShortcuts));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/config.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD
const close_close = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ const library_close = (close_close);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/secondary-sidebar/inserter-sidebar.js


/**
 * WordPress dependencies
 */


=======

var primary = external_this_wp_keycodes_["displayShortcutList"].primary,
    primaryShift = external_this_wp_keycodes_["displayShortcutList"].primaryShift,
    primaryAlt = external_this_wp_keycodes_["displayShortcutList"].primaryAlt,
    secondary = external_this_wp_keycodes_["displayShortcutList"].secondary,
    access = external_this_wp_keycodes_["displayShortcutList"].access,
    ctrl = external_this_wp_keycodes_["displayShortcutList"].ctrl,
    alt = external_this_wp_keycodes_["displayShortcutList"].alt,
    ctrlShift = external_this_wp_keycodes_["displayShortcutList"].ctrlShift,
    shiftAlt = external_this_wp_keycodes_["displayShortcutList"].shiftAlt;
var globalShortcuts = {
  title: Object(external_this_wp_i18n_["__"])('Global shortcuts'),
  shortcuts: [{
    keyCombination: access('h'),
    description: Object(external_this_wp_i18n_["__"])('Display this help.')
  }, {
    keyCombination: primary('s'),
    description: Object(external_this_wp_i18n_["__"])('Save your changes.')
  }, {
    keyCombination: primary('z'),
    description: Object(external_this_wp_i18n_["__"])('Undo your last changes.')
  }, {
    keyCombination: primaryShift('z'),
    description: Object(external_this_wp_i18n_["__"])('Redo your last undo.')
  }, {
    keyCombination: primaryShift(','),
    description: Object(external_this_wp_i18n_["__"])('Show or hide the settings sidebar.'),
    ariaLabel: external_this_wp_keycodes_["shortcutAriaLabel"].primaryShift(',')
  }, {
    keyCombination: access('o'),
    description: Object(external_this_wp_i18n_["__"])('Open the block navigation menu.')
  }, {
    keyCombination: ctrl('`'),
    description: Object(external_this_wp_i18n_["__"])('Navigate to the next part of the editor.'),
    ariaLabel: external_this_wp_keycodes_["shortcutAriaLabel"].ctrl('`')
  }, {
    keyCombination: ctrlShift('`'),
    description: Object(external_this_wp_i18n_["__"])('Navigate to the previous part of the editor.'),
    ariaLabel: external_this_wp_keycodes_["shortcutAriaLabel"].ctrlShift('`')
  }, {
    keyCombination: shiftAlt('n'),
    description: Object(external_this_wp_i18n_["__"])('Navigate to the next part of the editor (alternative).')
  }, {
    keyCombination: shiftAlt('p'),
    description: Object(external_this_wp_i18n_["__"])('Navigate to the previous part of the editor (alternative).')
  }, {
    keyCombination: alt('F10'),
    description: Object(external_this_wp_i18n_["__"])('Navigate to the nearest toolbar.')
  }, {
    keyCombination: secondary('m'),
    description: Object(external_this_wp_i18n_["__"])('Switch between Visual Editor and Code Editor.')
  }]
};
var selectionShortcuts = {
  title: Object(external_this_wp_i18n_["__"])('Selection shortcuts'),
  shortcuts: [{
    keyCombination: primary('a'),
    description: Object(external_this_wp_i18n_["__"])('Select all text when typing. Press again to select all blocks.')
  }, {
    keyCombination: 'Esc',
    description: Object(external_this_wp_i18n_["__"])('Clear selection.'),

    /* translators: The 'escape' key on a keyboard. */
    ariaLabel: Object(external_this_wp_i18n_["__"])('Escape')
  }]
};
var blockShortcuts = {
  title: Object(external_this_wp_i18n_["__"])('Block shortcuts'),
  shortcuts: [{
    keyCombination: primaryShift('d'),
    description: Object(external_this_wp_i18n_["__"])('Duplicate the selected block(s).')
  }, {
    keyCombination: access('z'),
    description: Object(external_this_wp_i18n_["__"])('Remove the selected block(s).')
  }, {
    keyCombination: primaryAlt('t'),
    description: Object(external_this_wp_i18n_["__"])('Insert a new block before the selected block(s).')
  }, {
    keyCombination: primaryAlt('y'),
    description: Object(external_this_wp_i18n_["__"])('Insert a new block after the selected block(s).')
  }, {
    keyCombination: '/',
    description: Object(external_this_wp_i18n_["__"])('Change the block type after adding a new paragraph.'),

    /* translators: The forward-slash character. e.g. '/'. */
    ariaLabel: Object(external_this_wp_i18n_["__"])('Forward-slash')
  }]
};
var textFormattingShortcuts = {
  title: Object(external_this_wp_i18n_["__"])('Text formatting'),
  shortcuts: [{
    keyCombination: primary('b'),
    description: Object(external_this_wp_i18n_["__"])('Make the selected text bold.')
  }, {
    keyCombination: primary('i'),
    description: Object(external_this_wp_i18n_["__"])('Make the selected text italic.')
  }, {
    keyCombination: primary('u'),
    description: Object(external_this_wp_i18n_["__"])('Underline the selected text.')
  }, {
    keyCombination: primary('k'),
    description: Object(external_this_wp_i18n_["__"])('Convert the selected text into a link.')
  }, {
    keyCombination: primaryShift('k'),
    description: Object(external_this_wp_i18n_["__"])('Remove a link.')
  }, {
    keyCombination: access('d'),
    description: Object(external_this_wp_i18n_["__"])('Add a strikethrough to the selected text.')
  }, {
    keyCombination: access('x'),
    description: Object(external_this_wp_i18n_["__"])('Display the selected text in a monospaced font.')
  }]
};
/* harmony default export */ var keyboard_shortcut_help_modal_config = ([globalShortcuts, selectionShortcuts, blockShortcuts, textFormattingShortcuts]);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9




<<<<<<< HEAD

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
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    ref: inserterDialogRef,
    ...inserterDialogProps,
    className: "edit-post-editor__inserter-panel"
  }, (0,external_wp_element_namespaceObject.createElement)(TagName, {
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

;// CONCATENATED MODULE: external ["wp","dom"]
const external_wp_dom_namespaceObject = window["wp"]["dom"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/secondary-sidebar/list-view-outline.js


/**
 * WordPress dependencies
 */






function EmptyOutlineIllustration() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SVG, {
    width: "138",
    height: "148",
    viewBox: "0 0 138 148",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Rect, {
    width: "138",
    height: "148",
    rx: "4",
    fill: "#F0F6FC"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Line, {
    x1: "44",
    y1: "28",
    x2: "24",
    y2: "28",
    stroke: "#DDDDDD"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Rect, {
    x: "48",
    y: "16",
    width: "27",
    height: "23",
    rx: "4",
    fill: "#DDDDDD"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Path, {
    d: "M54.7585 32V23.2727H56.6037V26.8736H60.3494V23.2727H62.1903V32H60.3494V28.3949H56.6037V32H54.7585ZM67.4574 23.2727V32H65.6122V25.0241H65.5611L63.5625 26.277V24.6406L65.723 23.2727H67.4574Z",
    fill: "black"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Line, {
    x1: "55",
    y1: "59",
    x2: "24",
    y2: "59",
    stroke: "#DDDDDD"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Rect, {
    x: "59",
    y: "47",
    width: "29",
    height: "23",
    rx: "4",
    fill: "#DDDDDD"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Path, {
    d: "M65.7585 63V54.2727H67.6037V57.8736H71.3494V54.2727H73.1903V63H71.3494V59.3949H67.6037V63H65.7585ZM74.6605 63V61.6705L77.767 58.794C78.0313 58.5384 78.2528 58.3082 78.4318 58.1037C78.6136 57.8991 78.7514 57.6989 78.8452 57.5028C78.9389 57.304 78.9858 57.0895 78.9858 56.8594C78.9858 56.6037 78.9276 56.3835 78.8111 56.1989C78.6946 56.0114 78.5355 55.8679 78.3338 55.7685C78.1321 55.6662 77.9034 55.6151 77.6477 55.6151C77.3807 55.6151 77.1477 55.669 76.9489 55.777C76.75 55.8849 76.5966 56.0398 76.4886 56.2415C76.3807 56.4432 76.3267 56.6832 76.3267 56.9616H74.5753C74.5753 56.3906 74.7045 55.8949 74.9631 55.4744C75.2216 55.054 75.5838 54.7287 76.0497 54.4986C76.5156 54.2685 77.0526 54.1534 77.6605 54.1534C78.2855 54.1534 78.8295 54.2642 79.2926 54.4858C79.7585 54.7045 80.1207 55.0085 80.3793 55.3977C80.6378 55.7869 80.767 56.233 80.767 56.7358C80.767 57.0653 80.7017 57.3906 80.571 57.7116C80.4432 58.0327 80.2145 58.3892 79.8849 58.7812C79.5554 59.1705 79.0909 59.6378 78.4915 60.1832L77.2173 61.4318V61.4915H80.8821V63H74.6605Z",
    fill: "black"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Line, {
    x1: "80",
    y1: "90",
    x2: "24",
    y2: "90",
    stroke: "#DDDDDD"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Rect, {
    x: "84",
    y: "78",
    width: "30",
    height: "23",
    rx: "4",
    fill: "#F0B849"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Path, {
    d: "M90.7585 94V85.2727H92.6037V88.8736H96.3494V85.2727H98.1903V94H96.3494V90.3949H92.6037V94H90.7585ZM99.5284 92.4659V91.0128L103.172 85.2727H104.425V87.2841H103.683L101.386 90.919V90.9872H106.564V92.4659H99.5284ZM103.717 94V92.0227L103.751 91.3793V85.2727H105.482V94H103.717Z",
    fill: "black"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Line, {
    x1: "66",
    y1: "121",
    x2: "24",
    y2: "121",
    stroke: "#DDDDDD"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Rect, {
    x: "70",
    y: "109",
    width: "29",
    height: "23",
    rx: "4",
    fill: "#DDDDDD"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Path, {
    d: "M76.7585 125V116.273H78.6037V119.874H82.3494V116.273H84.1903V125H82.3494V121.395H78.6037V125H76.7585ZM88.8864 125.119C88.25 125.119 87.6832 125.01 87.1861 124.791C86.6918 124.57 86.3011 124.266 86.0142 123.879C85.7301 123.49 85.5838 123.041 85.5753 122.533H87.4332C87.4446 122.746 87.5142 122.933 87.642 123.095C87.7727 123.254 87.946 123.378 88.1619 123.466C88.3778 123.554 88.6207 123.598 88.8906 123.598C89.1719 123.598 89.4205 123.548 89.6364 123.449C89.8523 123.349 90.0213 123.212 90.1435 123.036C90.2656 122.859 90.3267 122.656 90.3267 122.426C90.3267 122.193 90.2614 121.987 90.1307 121.808C90.0028 121.626 89.8182 121.484 89.5767 121.382C89.3381 121.28 89.054 121.229 88.7244 121.229H87.9105V119.874H88.7244C89.0028 119.874 89.2486 119.825 89.4616 119.729C89.6776 119.632 89.8452 119.499 89.9645 119.328C90.0838 119.155 90.1435 118.953 90.1435 118.723C90.1435 118.504 90.0909 118.312 89.9858 118.148C89.8835 117.98 89.7386 117.849 89.5511 117.756C89.3665 117.662 89.1506 117.615 88.9034 117.615C88.6534 117.615 88.4247 117.661 88.2173 117.751C88.0099 117.839 87.8438 117.966 87.7188 118.131C87.5938 118.295 87.527 118.489 87.5185 118.71H85.75C85.7585 118.207 85.902 117.764 86.1804 117.381C86.4588 116.997 86.8338 116.697 87.3054 116.482C87.7798 116.263 88.3153 116.153 88.9119 116.153C89.5142 116.153 90.0412 116.263 90.4929 116.482C90.9446 116.7 91.2955 116.996 91.5455 117.368C91.7983 117.737 91.9233 118.152 91.9205 118.612C91.9233 119.101 91.7713 119.509 91.4645 119.835C91.1605 120.162 90.7642 120.369 90.2756 120.457V120.526C90.9176 120.608 91.4063 120.831 91.7415 121.195C92.0795 121.555 92.2472 122.007 92.2443 122.55C92.2472 123.047 92.1037 123.489 91.8139 123.875C91.527 124.261 91.1307 124.565 90.625 124.787C90.1193 125.009 89.5398 125.119 88.8864 125.119Z",
    fill: "black"
  }));
}

function ListViewOutline() {
  const {
    headingCount
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getGlobalBlockCount
    } = select(external_wp_blockEditor_namespaceObject.store);
    return {
      headingCount: getGlobalBlockCount('core/heading')
    };
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-editor__list-view-overview"
  }, (0,external_wp_element_namespaceObject.createElement)("div", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, null, (0,external_wp_i18n_namespaceObject.__)('Characters:')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.CharacterCount, null))), (0,external_wp_element_namespaceObject.createElement)("div", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, null, (0,external_wp_i18n_namespaceObject.__)('Words:')), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.WordCount, null)), (0,external_wp_element_namespaceObject.createElement)("div", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, null, (0,external_wp_i18n_namespaceObject.__)('Time to read:')), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.TimeToRead, null))), headingCount > 0 ? (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.DocumentOutline, null) : (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-post-editor__list-view-empty-headings"
  }, (0,external_wp_element_namespaceObject.createElement)(EmptyOutlineIllustration, null), (0,external_wp_element_namespaceObject.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('Navigate the structure of your document and address issues like empty or incorrect heading levels.'))));
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
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store); // This hook handles focus when the sidebar first renders.

  const focusOnMountRef = (0,external_wp_compose_namespaceObject.useFocusOnMount)('firstElement'); // The next 2 hooks handle focus for when the sidebar closes and returning focus to the element that had focus before sidebar opened.

  const headerFocusReturnRef = (0,external_wp_compose_namespaceObject.useFocusReturn)();
  const contentFocusReturnRef = (0,external_wp_compose_namespaceObject.useFocusReturn)();

  function closeOnEscape(event) {
    if (event.keyCode === external_wp_keycodes_namespaceObject.ESCAPE && !event.defaultPrevented) {
      event.preventDefault();
      setIsListViewOpened(false);
    }
  } // Use internal state instead of a ref to make sure that the component
  // re-renders when the dropZoneElement updates.


  const [dropZoneElement, setDropZoneElement] = (0,external_wp_element_namespaceObject.useState)(null); // Tracks our current tab.

  const [tab, setTab] = (0,external_wp_element_namespaceObject.useState)('list-view'); // This ref refers to the sidebar as a whole.

  const sidebarRef = (0,external_wp_element_namespaceObject.useRef)(); // This ref refers to the tab panel.

  const tabPanelRef = (0,external_wp_element_namespaceObject.useRef)(); // This ref refers to the list view application area.

  const listViewRef = (0,external_wp_element_namespaceObject.useRef)(); // Must merge the refs together so focus can be handled properly in the next function.

  const listViewContainerRef = (0,external_wp_compose_namespaceObject.useMergeRefs)([contentFocusReturnRef, focusOnMountRef, listViewRef, setDropZoneElement]);
  /*
   * Callback function to handle list view or outline focus.
   *
   * @param {string} currentTab The current tab. Either list view or outline.
   *
   * @return void
   */

  function handleSidebarFocus(currentTab) {
    // Tab panel focus.
    const tabPanelFocus = external_wp_dom_namespaceObject.focus.tabbable.find(tabPanelRef.current)[0]; // List view tab is selected.

    if (currentTab === 'list-view') {
      // Either focus the list view or the tab panel. Must have a fallback because the list view does not render when there are no blocks.
      const listViewApplicationFocus = external_wp_dom_namespaceObject.focus.tabbable.find(listViewRef.current)[0];
      const listViewFocusArea = sidebarRef.current.contains(listViewApplicationFocus) ? listViewApplicationFocus : tabPanelFocus;
      listViewFocusArea.focus(); // Outline tab is selected.
    } else {
      tabPanelFocus.focus();
    }
  } // This only fires when the sidebar is open because of the conditional rendering. It is the same shortcut to open but that is defined as a global shortcut and only fires when the sidebar is closed.


  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-list-view', () => {
    // If the sidebar has focus, it is safe to close.
    if (sidebarRef.current.contains(sidebarRef.current.ownerDocument.activeElement)) {
      setIsListViewOpened(false); // If the list view or outline does not have focus, focus should be moved to it.
    } else {
      handleSidebarFocus(tab);
    }
  });
  /**
   * Render tab content for a given tab name.
   *
   * @param {string} tabName The name of the tab to render.
   */

  function renderTabContent(tabName) {
    if (tabName === 'list-view') {
      return (0,external_wp_element_namespaceObject.createElement)("div", {
        className: "edit-post-editor__list-view-panel-content"
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalListView, {
        dropZoneElement: dropZoneElement
      }));
    }

    return (0,external_wp_element_namespaceObject.createElement)(ListViewOutline, null);
  }

  return (// eslint-disable-next-line jsx-a11y/no-static-element-interactions
    (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-editor__document-overview-panel",
      onKeyDown: closeOnEscape,
      ref: sidebarRef
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      className: "edit-post-editor__document-overview-panel__close-button",
      ref: headerFocusReturnRef,
      icon: close_small,
      label: (0,external_wp_i18n_namespaceObject.__)('Close'),
      onClick: () => setIsListViewOpened(false)
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TabPanel, {
      className: "edit-post-editor__document-overview-panel__tab-panel",
      ref: tabPanelRef,
      onSelect: tabName => setTab(tabName),
      selectOnMove: false,
      tabs: [{
        name: 'list-view',
        title: (0,external_wp_i18n_namespaceObject._x)('List View', 'Post overview'),
        className: 'edit-post-sidebar__panel-tab'
      }, {
        name: 'outline',
        title: (0,external_wp_i18n_namespaceObject._x)('Outline', 'Post overview'),
        className: 'edit-post-sidebar__panel-tab'
      }]
    }, currentTab => (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-editor__list-view-container",
      ref: listViewContainerRef
    }, renderTabContent(currentTab.name))))
  );
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/drawer-left.js


=======
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


var MODAL_NAME = 'edit-post/keyboard-shortcut-help';

var keyboard_shortcut_help_modal_mapKeyCombination = function mapKeyCombination(keyCombination) {
  return keyCombination.map(function (character, index) {
    if (character === '+') {
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], {
        key: index
      }, character);
    }

    return Object(external_this_wp_element_["createElement"])("kbd", {
      key: index,
      className: "edit-post-keyboard-shortcut-help__shortcut-key"
    }, character);
  });
};

var keyboard_shortcut_help_modal_ShortcutList = function ShortcutList(_ref) {
  var shortcuts = _ref.shortcuts;
  return Object(external_this_wp_element_["createElement"])("dl", {
    className: "edit-post-keyboard-shortcut-help__shortcut-list"
  }, shortcuts.map(function (_ref2, index) {
    var keyCombination = _ref2.keyCombination,
        description = _ref2.description,
        ariaLabel = _ref2.ariaLabel;
    return Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-keyboard-shortcut-help__shortcut",
      key: index
    }, Object(external_this_wp_element_["createElement"])("dt", {
      className: "edit-post-keyboard-shortcut-help__shortcut-term"
    }, Object(external_this_wp_element_["createElement"])("kbd", {
      className: "edit-post-keyboard-shortcut-help__shortcut-key-combination",
      "aria-label": ariaLabel
    }, keyboard_shortcut_help_modal_mapKeyCombination(Object(external_lodash_["castArray"])(keyCombination)))), Object(external_this_wp_element_["createElement"])("dd", {
      className: "edit-post-keyboard-shortcut-help__shortcut-description"
    }, description));
  }));
};

var keyboard_shortcut_help_modal_ShortcutSection = function ShortcutSection(_ref3) {
  var title = _ref3.title,
      shortcuts = _ref3.shortcuts;
  return Object(external_this_wp_element_["createElement"])("section", {
    className: "edit-post-keyboard-shortcut-help__section"
  }, Object(external_this_wp_element_["createElement"])("h2", {
    className: "edit-post-keyboard-shortcut-help__section-title"
  }, title), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutList, {
    shortcuts: shortcuts
  }));
};

function KeyboardShortcutHelpModal(_ref4) {
  var isModalActive = _ref4.isModalActive,
      toggleModal = _ref4.toggleModal;
  var title = Object(external_this_wp_element_["createElement"])("span", {
    className: "edit-post-keyboard-shortcut-help__title"
  }, Object(external_this_wp_i18n_["__"])('Keyboard Shortcuts'));
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["KeyboardShortcuts"], {
    bindGlobal: true,
    shortcuts: Object(defineProperty["a" /* default */])({}, external_this_wp_keycodes_["rawShortcut"].access('h'), toggleModal)
  }), isModalActive && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Modal"], {
    className: "edit-post-keyboard-shortcut-help",
    title: title,
    closeLabel: Object(external_this_wp_i18n_["__"])('Close'),
    onRequestClose: toggleModal
  }, keyboard_shortcut_help_modal_config.map(function (config, index) {
    return Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutSection, Object(esm_extends["a" /* default */])({
      key: index
    }, config));
  })));
}
/* harmony default export */ var keyboard_shortcut_help_modal = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isModalActive: select('core/edit-post').isModalActive(MODAL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref6) {
  var isModalActive = _ref6.isModalActive;

  var _dispatch = dispatch('core/edit-post'),
      openModal = _dispatch.openModal,
      closeModal = _dispatch.closeModal;

  return {
    toggleModal: function toggleModal() {
      return isModalActive ? closeModal() : openModal(MODAL_NAME);
    }
  };
})])(KeyboardShortcutHelpModal));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/section.js


var section_Section = function Section(_ref) {
  var title = _ref.title,
      children = _ref.children;
  return Object(external_this_wp_element_["createElement"])("section", {
    className: "edit-post-options-modal__section"
  }, Object(external_this_wp_element_["createElement"])("h2", {
    className: "edit-post-options-modal__section-title"
  }, title), children);
};

/* harmony default export */ var section = (section_Section);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/base.js


/**
 * WordPress dependencies
 */


function BaseOption(_ref) {
  var label = _ref.label,
      isChecked = _ref.isChecked,
      onChange = _ref.onChange;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
    className: "edit-post-options-modal__option",
    label: label,
    checked: isChecked,
    onChange: onChange
  });
}

/* harmony default export */ var base = (BaseOption);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-custom-fields.js








/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var enable_custom_fields_EnableCustomFieldsOption =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(EnableCustomFieldsOption, _Component);

  function EnableCustomFieldsOption(_ref) {
    var _this;

    var isChecked = _ref.isChecked;

    Object(classCallCheck["a" /* default */])(this, EnableCustomFieldsOption);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(EnableCustomFieldsOption).apply(this, arguments));
    _this.toggleCustomFields = _this.toggleCustomFields.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.state = {
      isChecked: isChecked
    };
    return _this;
  }

  Object(createClass["a" /* default */])(EnableCustomFieldsOption, [{
    key: "toggleCustomFields",
    value: function toggleCustomFields() {
      // Submit a hidden form which triggers the toggle_custom_fields admin action.
      // This action will toggle the setting and reload the editor with the meta box
      // assets included on the page.
      document.getElementById('toggle-custom-fields-form').submit(); // Make it look like something happened while the page reloads.

      this.setState({
        isChecked: !this.props.isChecked
      });
    }
  }, {
    key: "render",
    value: function render() {
      var label = this.props.label;
      var isChecked = this.state.isChecked;
      return Object(external_this_wp_element_["createElement"])(base, {
        label: label,
        isChecked: isChecked,
        onChange: this.toggleCustomFields
      });
    }
  }]);

  return EnableCustomFieldsOption;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var enable_custom_fields = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isChecked: !!select('core/editor').getEditorSettings().enableCustomFields
  };
})(enable_custom_fields_EnableCustomFieldsOption));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-panel.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD
const drawerLeft = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  width: "24",
  height: "24",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM8.5 18.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h2.5v13zm10-.5c0 .3-.2.5-.5.5h-8v-13h8c.3 0 .5.2.5.5v12z"
}));
/* harmony default export */ const drawer_left = (drawerLeft);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/drawer-right.js


/**
 * WordPress dependencies
 */

const drawerRight = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  width: "24",
  height: "24",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4 14.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h8v13zm4.5-.5c0 .3-.2.5-.5.5h-2.5v-13H18c.3 0 .5.2.5.5v12z"
}));
/* harmony default export */ const drawer_right = (drawerRight);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-header/index.js


=======

/**
 * Internal dependencies
 */


/* harmony default export */ var enable_panel = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var panelName = _ref.panelName;

  var _select = select('core/edit-post'),
      isEditorPanelEnabled = _select.isEditorPanelEnabled,
      isEditorPanelRemoved = _select.isEditorPanelRemoved;

  return {
    isRemoved: isEditorPanelRemoved(panelName),
    isChecked: isEditorPanelEnabled(panelName)
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isRemoved = _ref2.isRemoved;
  return !isRemoved;
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3) {
  var panelName = _ref3.panelName;
  return {
    onChange: function onChange() {
      return dispatch('core/edit-post').toggleEditorPanelEnabled(panelName);
    }
  };
}))(base));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-publish-sidebar.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */



<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD

const SettingsHeader = ({
  sidebarName
}) => {
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

/* harmony default export */ const settings_header = (SettingsHeader);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-visibility/index.js
=======
/* harmony default export */ var enable_publish_sidebar = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isChecked: select('core/editor').isPublishSidebarEnabled()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      enablePublishSidebar = _dispatch.enablePublishSidebar,
      disablePublishSidebar = _dispatch.disablePublishSidebar;

  return {
    onChange: function onChange(isEnabled) {
      return isEnabled ? enablePublishSidebar() : disablePublishSidebar();
    }
  };
}), // In < medium viewports we override this option and always show the publish sidebar.
// See the edit-post's header component for the specific logic.
Object(external_this_wp_viewport_["ifViewportMatches"])('medium'))(base));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/deferred.js





>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */

<<<<<<< HEAD



function PostVisibility() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    // Anchor the popover to the middle of the entire row so that it doesn't
    // move around when the label changes.
    anchor: popoverAnchor,
    placement: 'bottom-end'
  }), [popoverAnchor]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostVisibilityCheck, {
    render: ({
      canEdit
    }) => (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
      ref: setPopoverAnchor,
      className: "edit-post-post-visibility"
    }, (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_i18n_namespaceObject.__)('Visibility')), !canEdit && (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostVisibilityLabel, null)), canEdit && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
      contentClassName: "edit-post-post-visibility__dialog",
      popoverProps: popoverProps,
      focusOnMount: true,
      renderToggle: ({
        isOpen,
        onToggle
      }) => (0,external_wp_element_namespaceObject.createElement)(PostVisibilityToggle, {
        isOpen: isOpen,
        onClick: onToggle
      }),
      renderContent: ({
        onClose
      }) => (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostVisibility, {
        onClose: onClose
      })
    }))
  });
}

function PostVisibilityToggle({
  isOpen,
  onClick
}) {
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

/* harmony default export */ const post_visibility = (PostVisibility);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-trash/index.js


/**
 * WordPress dependencies
 */

function PostTrash() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTrashCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostTrash, null));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-schedule/index.js


=======
/**
 * Internal dependencies
 */



var deferred_DeferredOption =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(DeferredOption, _Component);

  function DeferredOption(_ref) {
    var _this;

    var isChecked = _ref.isChecked;

    Object(classCallCheck["a" /* default */])(this, DeferredOption);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(DeferredOption).apply(this, arguments));
    _this.state = {
      isChecked: isChecked
    };
    return _this;
  }

  Object(createClass["a" /* default */])(DeferredOption, [{
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      if (this.state.isChecked !== this.props.isChecked) {
        this.props.onChange(this.state.isChecked);
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      return Object(external_this_wp_element_["createElement"])(base, {
        label: this.props.label,
        isChecked: this.state.isChecked,
        onChange: function onChange(isChecked) {
          return _this2.setState({
            isChecked: isChecked
          });
        }
      });
    }
  }]);

  return DeferredOption;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var deferred = (deferred_DeferredOption);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-tips.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */


<<<<<<< HEAD


function PostSchedule() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    anchor: popoverAnchor,
    placement: 'bottom-end'
  }), [popoverAnchor]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostScheduleCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-schedule",
    ref: setPopoverAnchor
  }, (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_i18n_namespaceObject.__)('Publish')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    contentClassName: "edit-post-post-schedule__dialog",
    focusOnMount: true,
    renderToggle: ({
      isOpen,
      onToggle
    }) => (0,external_wp_element_namespaceObject.createElement)(PostScheduleToggle, {
      isOpen: isOpen,
      onClick: onToggle
    }),
    renderContent: ({
      onClose
    }) => (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSchedule, {
      onClose: onClose
    })
  })));
}

function PostScheduleToggle({
  isOpen,
  onClick
}) {
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
/* harmony default export */ const post_sticky = (PostSticky);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-author/index.js


/**
 * WordPress dependencies
 */


function PostAuthor() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostAuthorCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-author"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostAuthor, null)));
}
/* harmony default export */ const post_author = (PostAuthor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-slug/index.js


/**
 * WordPress dependencies
 */


function PostSlug() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSlugCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-slug"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSlug, null)));
}
/* harmony default export */ const post_slug = (PostSlug);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-format/index.js

=======
/**
 * Internal dependencies
 */


/* harmony default export */ var enable_tips = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isChecked: select('core/nux').areTipsEnabled()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/nux'),
      enableTips = _dispatch.enableTips,
      disableTips = _dispatch.disableTips;

  return {
    onChange: function onChange(isEnabled) {
      return isEnabled ? enableTips() : disableTips();
    }
  };
}))( // Using DeferredOption here means enableTips() is called when the Options
// modal is dismissed. This stops the NUX guide from appearing above the
// Options modal, which looks totally weird.
deferred));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/index.js





// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/meta-boxes-section.js



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
  var areCustomFieldsRegistered = _ref.areCustomFieldsRegistered,
      metaBoxes = _ref.metaBoxes,
      sectionProps = Object(objectWithoutProperties["a" /* default */])(_ref, ["areCustomFieldsRegistered", "metaBoxes"]);

  // The 'Custom Fields' meta box is a special case that we handle separately.
  var thirdPartyMetaBoxes = Object(external_lodash_["filter"])(metaBoxes, function (_ref2) {
    var id = _ref2.id;
    return id !== 'postcustom';
  });

  if (!areCustomFieldsRegistered && thirdPartyMetaBoxes.length === 0) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(section, sectionProps, areCustomFieldsRegistered && Object(external_this_wp_element_["createElement"])(enable_custom_fields, {
    label: Object(external_this_wp_i18n_["__"])('Custom Fields')
  }), Object(external_lodash_["map"])(thirdPartyMetaBoxes, function (_ref3) {
    var id = _ref3.id,
        title = _ref3.title;
    return Object(external_this_wp_element_["createElement"])(enable_panel, {
      key: id,
      label: title,
      panelName: "meta-box-".concat(id)
    });
  }));
}
/* harmony default export */ var meta_boxes_section = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditorSettings = _select.getEditorSettings;

  var _select2 = select('core/edit-post'),
      getAllMetaBoxes = _select2.getAllMetaBoxes;

  return {
    areCustomFieldsRegistered: getEditorSettings().enableCustomFields !== undefined,
    metaBoxes: getAllMetaBoxes()
  };
})(MetaBoxesSection));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/index.js


/**
 * External dependencies
 */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * WordPress dependencies
 */


<<<<<<< HEAD
function PostFormat() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostFormatCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-format"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostFormat, null)));
}
/* harmony default export */ const post_format = (PostFormat);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-pending-status/index.js


/**
 * WordPress dependencies
 */


function PostPendingStatus() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPendingStatusCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostPendingStatus, null)));
}
/* harmony default export */ const post_pending_status = (PostPendingStatus);

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

const PluginPostStatusInfo = ({
  children,
  className
}) => (0,external_wp_element_namespaceObject.createElement)(plugin_post_status_info_Fill, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
  className: className
}, children));

PluginPostStatusInfo.Slot = plugin_post_status_info_Slot;
/* harmony default export */ const plugin_post_status_info = (PluginPostStatusInfo);

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
/* harmony default export */ const add_template = (addTemplate);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-template/create-modal.js

=======




/**
 * Internal dependencies
 */




var options_modal_MODAL_NAME = 'edit-post/options';
function OptionsModal(_ref) {
  var isModalActive = _ref.isModalActive,
      closeModal = _ref.closeModal;

  if (!isModalActive) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Modal"], {
    className: "edit-post-options-modal",
    title: Object(external_this_wp_element_["createElement"])("span", {
      className: "edit-post-options-modal__title"
    }, Object(external_this_wp_i18n_["__"])('Options')),
    closeLabel: Object(external_this_wp_i18n_["__"])('Close'),
    onRequestClose: closeModal
  }, Object(external_this_wp_element_["createElement"])(section, {
    title: Object(external_this_wp_i18n_["__"])('General')
  }, Object(external_this_wp_element_["createElement"])(enable_publish_sidebar, {
    label: Object(external_this_wp_i18n_["__"])('Enable Pre-publish Checks')
  }), Object(external_this_wp_element_["createElement"])(enable_tips, {
    label: Object(external_this_wp_i18n_["__"])('Enable Tips')
  })), Object(external_this_wp_element_["createElement"])(section, {
    title: Object(external_this_wp_i18n_["__"])('Document Panels')
  }, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Permalink'),
    panelName: "post-link"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTaxonomies"], {
    taxonomyWrapper: function taxonomyWrapper(content, taxonomy) {
      return Object(external_this_wp_element_["createElement"])(enable_panel, {
        label: Object(external_lodash_["get"])(taxonomy, ['labels', 'menu_name']),
        panelName: "taxonomy-panel-".concat(taxonomy.slug)
      });
    }
  }), Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Featured Image'),
    panelName: "featured-image"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostExcerptCheck"], null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Excerpt'),
    panelName: "post-excerpt"
  })), Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Discussion'),
    panelName: "discussion-panel"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageAttributesCheck"], null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Page Attributes'),
    panelName: "page-attributes"
  }))), Object(external_this_wp_element_["createElement"])(meta_boxes_section, {
    title: Object(external_this_wp_i18n_["__"])('Advanced Panels')
  }));
}
/* harmony default export */ var options_modal = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isModalActive: select('core/edit-post').isModalActive(options_modal_MODAL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    closeModal: function closeModal() {
      return dispatch('core/edit-post').closeModal();
    }
  };
}))(OptionsModal));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js








/**
 * External dependencies
 */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * WordPress dependencies
 */





<<<<<<< HEAD


/**
 * Internal dependencies
 */



const DEFAULT_TITLE = (0,external_wp_i18n_namespaceObject.__)('Custom Template');

function PostTemplateCreateModal({
  onClose
}) {
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
    onRequestClose: cancel,
    className: "edit-post-post-template__create-modal"
  }, (0,external_wp_element_namespaceObject.createElement)("form", {
    className: "edit-post-post-template__create-form",
    onSubmit: submit
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: "3"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Name'),
    value: title,
    onChange: setTitle,
    placeholder: DEFAULT_TITLE,
    disabled: isBusy,
    help: (0,external_wp_i18n_namespaceObject.__)('Describe the template, e.g. "Post with sidebar". A custom template can be manually applied to any post or page.')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "right"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: cancel
  }, (0,external_wp_i18n_namespaceObject.__)('Cancel')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    type: "submit",
    isBusy: isBusy,
    "aria-disabled": isBusy
  }, (0,external_wp_i18n_namespaceObject.__)('Create'))))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-template/form.js
=======
var meta_boxes_area_MetaBoxesArea =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MetaBoxesArea, _Component);

  /**
   * @inheritdoc
   */
  function MetaBoxesArea() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, MetaBoxesArea);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MetaBoxesArea).apply(this, arguments));
    _this.bindContainerNode = _this.bindContainerNode.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }
  /**
   * @inheritdoc
   */


  Object(createClass["a" /* default */])(MetaBoxesArea, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.form = document.querySelector('.metabox-location-' + this.props.location);

      if (this.form) {
        this.container.appendChild(this.form);
      }
    }
    /**
     * Get the meta box location form from the original location.
     */

  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      if (this.form) {
        document.querySelector('#metaboxes').appendChild(this.form);
      }
    }
    /**
     * Binds the metabox area container node.
     *
     * @param {Element} node DOM Node.
     */

  }, {
    key: "bindContainerNode",
    value: function bindContainerNode(node) {
      this.container = node;
    }
    /**
     * @inheritdoc
     */

  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          location = _this$props.location,
          isSaving = _this$props.isSaving;
      var classes = classnames_default()('edit-post-meta-boxes-area', "is-".concat(location), {
        'is-loading': isSaving
      });
      return Object(external_this_wp_element_["createElement"])("div", {
        className: classes
      }, isSaving && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null), Object(external_this_wp_element_["createElement"])("div", {
        className: "edit-post-meta-boxes-area__container",
        ref: this.bindContainerNode
      }), Object(external_this_wp_element_["createElement"])("div", {
        className: "edit-post-meta-boxes-area__clear"
      }));
    }
  }]);

  return MetaBoxesArea;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var meta_boxes_area = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isSaving: select('core/edit-post').isSavingMetaBoxes()
  };
})(meta_boxes_area_MetaBoxesArea));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-box-visibility.js




>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */



<<<<<<< HEAD





/**
 * Internal dependencies
 */



function PostTemplateForm({
  onClose
}) {
  var _options$find, _selectedOption$value;

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

    const _isPostsPage = select(external_wp_editor_namespaceObject.store).getCurrentPostId() === siteSettings?.page_for_posts;

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
    ...Object.fromEntries((fetchedTemplates !== null && fetchedTemplates !== void 0 ? fetchedTemplates : []).map(({
      slug,
      title
    }) => [slug, title.rendered]))
  }).map(([slug, title]) => ({
    value: slug,
    label: title
  })), [availableTemplates, fetchedTemplates]);
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
    __nextHasNoMarginBottom: true,
    hideLabelFromVision: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Template'),
    value: (_selectedOption$value = selectedOption?.value) !== null && _selectedOption$value !== void 0 ? _selectedOption$value : '',
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

=======
var meta_box_visibility_MetaBoxVisibility =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MetaBoxVisibility, _Component);

  function MetaBoxVisibility() {
    Object(classCallCheck["a" /* default */])(this, MetaBoxVisibility);

    return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MetaBoxVisibility).apply(this, arguments));
  }

  Object(createClass["a" /* default */])(MetaBoxVisibility, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.updateDOM();
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.isVisible !== prevProps.isVisible) {
        this.updateDOM();
      }
    }
  }, {
    key: "updateDOM",
    value: function updateDOM() {
      var _this$props = this.props,
          id = _this$props.id,
          isVisible = _this$props.isVisible;
      var element = document.getElementById(id);

      if (!element) {
        return;
      }

      if (isVisible) {
        element.classList.remove('is-hidden');
      } else {
        element.classList.add('is-hidden');
      }
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return MetaBoxVisibility;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var meta_box_visibility = (Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var id = _ref.id;
  return {
    isVisible: select('core/edit-post').isEditorPanelEnabled("meta-box-".concat(id))
  };
})(meta_box_visibility_MetaBoxVisibility));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js


/**
 * External dependencies
 */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * WordPress dependencies
 */



<<<<<<< HEAD



=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */



<<<<<<< HEAD
function PostTemplate() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    anchor: popoverAnchor,
    placement: 'bottom-end'
  }), [popoverAnchor]);
  const isVisible = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$canUser;

    const postTypeSlug = select(external_wp_editor_namespaceObject.store).getCurrentPostType();
    const postType = select(external_wp_coreData_namespaceObject.store).getPostType(postTypeSlug);

    if (!postType?.viewable) {
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
    className: "edit-post-post-template__dropdown",
    contentClassName: "edit-post-post-template__dialog",
    focusOnMount: true,
    renderToggle: ({
      isOpen,
      onToggle
    }) => (0,external_wp_element_namespaceObject.createElement)(PostTemplateToggle, {
      isOpen: isOpen,
      onClick: onToggle
    }),
    renderContent: ({
      onClose
    }) => (0,external_wp_element_namespaceObject.createElement)(PostTemplateForm, {
      onClose: onClose
    })
  }));
}

function PostTemplateToggle({
  isOpen,
  onClick
}) {
  const templateTitle = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const templateSlug = select(external_wp_editor_namespaceObject.store).getEditedPostAttribute('template');
    const {
      supportsTemplateMode,
      availableTemplates
    } = select(external_wp_editor_namespaceObject.store).getEditorSettings();

    if (!supportsTemplateMode && availableTemplates[templateSlug]) {
      return availableTemplates[templateSlug];
    }

    const template = select(external_wp_coreData_namespaceObject.store).canUser('create', 'templates') && select(store_store).getEditedPostTemplate();
    return template?.title || template?.slug || availableTemplates?.[templateSlug];
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
    anchor: popoverAnchor,
    placement: 'bottom-end'
  }), [popoverAnchor]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostURLCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: "edit-post-post-url",
    ref: setPopoverAnchor
  }, (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_i18n_namespaceObject.__)('URL')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    className: "edit-post-post-url__dropdown",
    contentClassName: "edit-post-post-url__dialog",
    focusOnMount: true,
    renderToggle: ({
      isOpen,
      onToggle
    }) => (0,external_wp_element_namespaceObject.createElement)(PostURLToggle, {
      isOpen: isOpen,
      onClick: onToggle
    }),
    renderContent: ({
      onClose
    }) => (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostURL, {
      onClose: onClose
    })
  })));
}

function PostURLToggle({
  isOpen,
  onClick
}) {
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

=======

function MetaBoxes(_ref) {
  var location = _ref.location,
      isVisible = _ref.isVisible,
      metaBoxes = _ref.metaBoxes;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_lodash_["map"])(metaBoxes, function (_ref2) {
    var id = _ref2.id;
    return Object(external_this_wp_element_["createElement"])(meta_box_visibility, {
      key: id,
      id: id
    });
  }), isVisible && Object(external_this_wp_element_["createElement"])(meta_boxes_area, {
    location: location
  }));
}

/* harmony default export */ var meta_boxes = (Object(external_this_wp_data_["withSelect"])(function (select, _ref3) {
  var location = _ref3.location;

  var _select = select('core/edit-post'),
      isMetaBoxLocationVisible = _select.isMetaBoxLocationVisible,
      getMetaBoxesPerLocation = _select.getMetaBoxesPerLocation;

  return {
    metaBoxes: getMetaBoxesPerLocation(location),
    isVisible: isMetaBoxLocationVisible(location)
  };
})(MetaBoxes));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/index.js


/**
 * WordPress Dependencies
 */




var sidebar_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('Sidebar'),
    Fill = sidebar_createSlotFill.Fill,
    sidebar_Slot = sidebar_createSlotFill.Slot;
/**
 * Renders a sidebar with its content.
 *
 * @return {Object} The rendered sidebar.
 */


var sidebar_Sidebar = function Sidebar(_ref) {
  var children = _ref.children,
      label = _ref.label;
  return Object(external_this_wp_element_["createElement"])(Fill, null, Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-sidebar",
    role: "region",
    "aria-label": label,
    tabIndex: "-1"
  }, children));
};

var WrappedSidebar = Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var name = _ref2.name;
  return {
    isActive: select('core/edit-post').getActiveGeneralSidebarName() === name
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref3) {
  var isActive = _ref3.isActive;
  return isActive;
}), external_this_wp_components_["withFocusReturn"])(sidebar_Sidebar);
WrappedSidebar.Slot = sidebar_Slot;
/* harmony default export */ var sidebar = (WrappedSidebar);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/sidebar-header/index.js


/**
 * External dependencies
 */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * WordPress dependencies
 */





<<<<<<< HEAD
=======

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */



<<<<<<< HEAD










/**
 * Module Constants
 */

const PANEL_NAME = 'post-status';

function PostStatus({
  isOpened,
  onTogglePanel
}) {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    className: "edit-post-post-status",
    title: (0,external_wp_i18n_namespaceObject.__)('Summary'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_wp_element_namespaceObject.createElement)(plugin_post_status_info.Slot, null, fills => (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(post_visibility, null), (0,external_wp_element_namespaceObject.createElement)(PostSchedule, null), (0,external_wp_element_namespaceObject.createElement)(PostTemplate, null), (0,external_wp_element_namespaceObject.createElement)(PostURL, null), (0,external_wp_element_namespaceObject.createElement)(post_sticky, null), (0,external_wp_element_namespaceObject.createElement)(post_pending_status, null), (0,external_wp_element_namespaceObject.createElement)(post_format, null), (0,external_wp_element_namespaceObject.createElement)(post_slug, null), (0,external_wp_element_namespaceObject.createElement)(post_author, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSyncStatus, null), fills, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    style: {
      marginTop: '16px'
    },
    spacing: 4,
    wrap: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSwitchToDraftButton, null), (0,external_wp_element_namespaceObject.createElement)(PostTrash, null)))));
}

/* harmony default export */ const post_status = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
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
}), (0,external_wp_compose_namespaceObject.ifCondition)(({
  isRemoved
}) => !isRemoved), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => ({
  onTogglePanel() {
    return dispatch(store_store).toggleEditorPanelOpened(PANEL_NAME);
  }

}))])(PostStatus));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/last-revision/index.js
=======
var sidebar_header_SidebarHeader = function SidebarHeader(_ref) {
  var children = _ref.children,
      className = _ref.className,
      closeLabel = _ref.closeLabel,
      closeSidebar = _ref.closeSidebar,
      title = _ref.title;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", {
    className: "components-panel__header edit-post-sidebar-header__small"
  }, Object(external_this_wp_element_["createElement"])("span", {
    className: "edit-post-sidebar-header__title"
  }, title || Object(external_this_wp_i18n_["__"])('(no title)')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    onClick: closeSidebar,
    icon: "no-alt",
    label: closeLabel
  })), Object(external_this_wp_element_["createElement"])("div", {
    className: classnames_default()('components-panel__header edit-post-sidebar-header', className)
  }, children, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    onClick: closeSidebar,
    icon: "no-alt",
    label: closeLabel,
    shortcut: keyboard_shortcuts.toggleSidebar
  })));
};

/* harmony default export */ var sidebar_header = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    title: select('core/editor').getEditedPostAttribute('title')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    closeSidebar: dispatch('core/edit-post').closeGeneralSidebar
  };
}))(sidebar_header_SidebarHeader));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-header/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



var settings_header_SettingsHeader = function SettingsHeader(_ref) {
  var openDocumentSettings = _ref.openDocumentSettings,
      openBlockSettings = _ref.openBlockSettings,
      sidebarName = _ref.sidebarName;

  var blockLabel = Object(external_this_wp_i18n_["__"])('Block');

  var _ref2 = sidebarName === 'edit-post/document' ? // translators: ARIA label for the Document sidebar tab, selected.
  [Object(external_this_wp_i18n_["__"])('Document (selected)'), 'is-active'] : // translators: ARIA label for the Document sidebar tab, not selected.
  [Object(external_this_wp_i18n_["__"])('Document'), ''],
      _ref3 = Object(slicedToArray["a" /* default */])(_ref2, 2),
      documentAriaLabel = _ref3[0],
      documentActiveClass = _ref3[1];

  var _ref4 = sidebarName === 'edit-post/block' ? // translators: ARIA label for the Block sidebar tab, selected.
  [Object(external_this_wp_i18n_["__"])('Block (selected)'), 'is-active'] : // translators: ARIA label for the Block sidebar tab, not selected.
  [Object(external_this_wp_i18n_["__"])('Block'), ''],
      _ref5 = Object(slicedToArray["a" /* default */])(_ref4, 2),
      blockAriaLabel = _ref5[0],
      blockActiveClass = _ref5[1];

  return Object(external_this_wp_element_["createElement"])(sidebar_header, {
    className: "edit-post-sidebar__panel-tabs",
    closeLabel: Object(external_this_wp_i18n_["__"])('Close settings')
  }, Object(external_this_wp_element_["createElement"])("ul", null, Object(external_this_wp_element_["createElement"])("li", null, Object(external_this_wp_element_["createElement"])("button", {
    onClick: openDocumentSettings,
    className: "edit-post-sidebar__panel-tab ".concat(documentActiveClass),
    "aria-label": documentAriaLabel,
    "data-label": Object(external_this_wp_i18n_["__"])('Document')
  }, Object(external_this_wp_i18n_["__"])('Document'))), Object(external_this_wp_element_["createElement"])("li", null, Object(external_this_wp_element_["createElement"])("button", {
    onClick: openBlockSettings,
    className: "edit-post-sidebar__panel-tab ".concat(blockActiveClass),
    "aria-label": blockAriaLabel,
    "data-label": blockLabel
  }, blockLabel))));
};

/* harmony default export */ var settings_header = (Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openGeneralSidebar = _dispatch.openGeneralSidebar;

  var _dispatch2 = dispatch('core/editor'),
      clearSelectedBlock = _dispatch2.clearSelectedBlock;

  return {
    openDocumentSettings: function openDocumentSettings() {
      openGeneralSidebar('edit-post/document');
      clearSelectedBlock();
    },
    openBlockSettings: function openBlockSettings() {
      openGeneralSidebar('edit-post/block');
    }
  };
})(settings_header_SettingsHeader));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-visibility/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */



<<<<<<< HEAD
function LastRevision() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostLastRevisionCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    className: "edit-post-last-revision__panel"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostLastRevision, null)));
}

/* harmony default export */ const last_revision = (LastRevision);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js
=======
function PostVisibility() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostVisibilityCheck"], {
    render: function render(_ref) {
      var canEdit = _ref.canEdit;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], {
        className: "edit-post-post-visibility"
      }, Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_i18n_["__"])('Visibility')), !canEdit && Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostVisibilityLabel"], null)), canEdit && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dropdown"], {
        position: "bottom left",
        contentClassName: "edit-post-post-visibility__dialog",
        renderToggle: function renderToggle(_ref2) {
          var isOpen = _ref2.isOpen,
              onToggle = _ref2.onToggle;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
            type: "button",
            "aria-expanded": isOpen,
            className: "edit-post-post-visibility__toggle",
            onClick: onToggle,
            isLink: true
          }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostVisibilityLabel"], null));
        },
        renderContent: function renderContent() {
          return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostVisibility"], null);
        }
      }));
    }
  });
}
/* harmony default export */ var post_visibility = (PostVisibility);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-trash/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */


<<<<<<< HEAD

/**
 * Internal dependencies
=======
function PostTrash() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTrashCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTrash"], null)));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-schedule/index.js


/**
 * WordPress dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */



<<<<<<< HEAD
function TaxonomyPanel({
  isEnabled,
  taxonomy,
  isOpened,
  onTogglePanel,
  children
}) {
  if (!isEnabled) {
    return null;
  }

  const taxonomyMenuName = taxonomy?.labels?.menu_name;

  if (!taxonomyMenuName) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: taxonomyMenuName,
    opened: isOpened,
    onToggle: onTogglePanel
  }, children);
}

/* harmony default export */ const taxonomy_panel = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, ownProps) => {
  const slug = ownProps.taxonomy?.slug;
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
=======


function PostSchedule(_ref) {
  var instanceId = _ref.instanceId;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostScheduleCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], {
    className: "edit-post-post-schedule"
  }, Object(external_this_wp_element_["createElement"])("label", {
    htmlFor: "edit-post-post-schedule__toggle-".concat(instanceId),
    id: "edit-post-post-schedule__heading-".concat(instanceId)
  }, Object(external_this_wp_i18n_["__"])('Publish')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dropdown"], {
    position: "bottom left",
    contentClassName: "edit-post-post-schedule__dialog",
    renderToggle: function renderToggle(_ref2) {
      var onToggle = _ref2.onToggle,
          isOpen = _ref2.isOpen;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("label", {
        className: "edit-post-post-schedule__label",
        htmlFor: "edit-post-post-schedule__toggle-".concat(instanceId)
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostScheduleLabel"], null), " ", Object(external_this_wp_i18n_["__"])('Click to change')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        id: "edit-post-post-schedule__toggle-".concat(instanceId),
        type: "button",
        className: "edit-post-post-schedule__toggle",
        onClick: onToggle,
        "aria-expanded": isOpen,
        "aria-live": "polite",
        isLink: true
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostScheduleLabel"], null)));
    },
    renderContent: function renderContent() {
      return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSchedule"], null);
    }
  })));
}
/* harmony default export */ var post_schedule = (Object(external_this_wp_compose_["withInstanceId"])(PostSchedule));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-sticky/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */

<<<<<<< HEAD
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

/* harmony default export */ const post_taxonomies = (PostTaxonomies);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/featured-image/index.js
=======

function PostSticky() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostStickyCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSticky"], null)));
}
/* harmony default export */ var post_sticky = (PostSticky);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-author/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */


<<<<<<< HEAD




/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const featured_image_PANEL_NAME = 'featured-image';

function FeaturedImage({
  isEnabled,
  isOpened,
  postType,
  onTogglePanel
}) {
  var _postType$labels$feat;

  if (!isEnabled) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostFeaturedImageCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (_postType$labels$feat = postType?.labels?.featured_image) !== null && _postType$labels$feat !== void 0 ? _postType$labels$feat : (0,external_wp_i18n_namespaceObject.__)('Featured image'),
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
    onTogglePanel: (...args) => toggleEditorPanelOpened(featured_image_PANEL_NAME, ...args)
  };
});
/* harmony default export */ const featured_image = ((0,external_wp_compose_namespaceObject.compose)(applyWithSelect, applyWithDispatch)(FeaturedImage));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-excerpt/index.js
=======
function PostAuthor() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostAuthorCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostAuthor"], null)));
}
/* harmony default export */ var post_author = (PostAuthor);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-format/index.js


/**
 * WordPress dependencies
 */


function PostFormat() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFormatCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFormat"], null)));
}
/* harmony default export */ var post_format = (PostFormat);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-pending-status/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */


<<<<<<< HEAD



/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const post_excerpt_PANEL_NAME = 'post-excerpt';

function PostExcerpt({
  isEnabled,
  isOpened,
  onTogglePanel
}) {
  if (!isEnabled) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostExcerptCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_wp_i18n_namespaceObject.__)('Excerpt'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostExcerpt, null)));
}

/* harmony default export */ const post_excerpt = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
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
=======
function PostPendingStatus() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPendingStatusCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPendingStatus"], null)));
}
/* harmony default export */ var post_pending_status = (PostPendingStatus);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-status-info/index.js


/**
 * Defines as extensibility slot for the Status & Visibility panel.
 */

/**
 * WordPress dependencies
 */


var plugin_post_status_info_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginPostStatusInfo'),
    plugin_post_status_info_Fill = plugin_post_status_info_createSlotFill.Fill,
    plugin_post_status_info_Slot = plugin_post_status_info_createSlotFill.Slot;



var plugin_post_status_info_PluginPostStatusInfo = function PluginPostStatusInfo(_ref) {
  var children = _ref.children,
      className = _ref.className;
  return Object(external_this_wp_element_["createElement"])(plugin_post_status_info_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], {
    className: className
  }, children));
};

plugin_post_status_info_PluginPostStatusInfo.Slot = plugin_post_status_info_Slot;
/* harmony default export */ var plugin_post_status_info = (plugin_post_status_info_PluginPostStatusInfo);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-status/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */





/**
<<<<<<< HEAD
 * Internal dependencies
 */


=======
 * Internal Dependencies
 */









>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Module Constants
 */

<<<<<<< HEAD
const discussion_panel_PANEL_NAME = 'discussion-panel';

function DiscussionPanel({
  isEnabled,
  isOpened,
  onTogglePanel
}) {
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

/* harmony default export */ const discussion_panel = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
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
=======
var PANEL_NAME = 'post-status';

function PostStatus(_ref) {
  var isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: "edit-post-post-status",
    title: Object(external_this_wp_i18n_["__"])('Status & Visibility'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(plugin_post_status_info.Slot, null, function (fills) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(post_visibility, null), Object(external_this_wp_element_["createElement"])(post_schedule, null), Object(external_this_wp_element_["createElement"])(post_format, null), Object(external_this_wp_element_["createElement"])(post_sticky, null), Object(external_this_wp_element_["createElement"])(post_pending_status, null), Object(external_this_wp_element_["createElement"])(post_author, null), fills, Object(external_this_wp_element_["createElement"])(PostTrash, null));
  }));
}

/* harmony default export */ var post_status = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isOpened: select('core/edit-post').isEditorPanelOpened(PANEL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleEditorPanelOpened(PANEL_NAME);
    }
  };
})])(PostStatus));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/last-revision/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */



<<<<<<< HEAD


/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const page_attributes_PANEL_NAME = 'page-attributes';
function PageAttributes() {
  var _postType$labels$attr;

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

  const onTogglePanel = (...args) => toggleEditorPanelOpened(page_attributes_PANEL_NAME, ...args);

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PageAttributesCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (_postType$labels$attr = postType?.labels?.attributes) !== null && _postType$labels$attr !== void 0 ? _postType$labels$attr : (0,external_wp_i18n_namespaceObject.__)('Page attributes'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PageAttributesParent, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PageAttributesOrder, null))));
}
/* harmony default export */ const page_attributes = (PageAttributes);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js


/**
 * External dependencies
 */
=======
function LastRevision() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLastRevisionCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: "edit-post-last-revision__panel"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLastRevision"], null)));
}

/* harmony default export */ var last_revision = (LastRevision);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js


/**
 * External Dependencies
 */

/**
 * WordPress dependencies
 */





function TaxonomyPanel(_ref) {
  var isEnabled = _ref.isEnabled,
      taxonomy = _ref.taxonomy,
      isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel,
      children = _ref.children;

  if (!isEnabled) {
    return null;
  }

  var taxonomyMenuName = Object(external_lodash_["get"])(taxonomy, ['labels', 'menu_name']);

  if (!taxonomyMenuName) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: taxonomyMenuName,
    opened: isOpened,
    onToggle: onTogglePanel
  }, children);
}

/* harmony default export */ var taxonomy_panel = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, ownProps) {
  var slug = Object(external_lodash_["get"])(ownProps.taxonomy, ['slug']);
  var panelName = slug ? "taxonomy-panel-".concat(slug) : '';
  return {
    panelName: panelName,
    isEnabled: slug ? select('core/edit-post').isEditorPanelEnabled(panelName) : false,
    isOpened: slug ? select('core/edit-post').isEditorPanelOpened(panelName) : false
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  return {
    onTogglePanel: function onTogglePanel() {
      dispatch('core/edit-post').toggleEditorPanelOpened(ownProps.panelName);
    }
  };
}))(TaxonomyPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/index.js

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * WordPress dependencies
 */

<<<<<<< HEAD



=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD
/**
 * Render metabox area.
 *
 * @param {Object} props          Component props.
 * @param {string} props.location metabox location.
 * @return {WPComponent} The component to be rendered.
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

/* harmony default export */ const meta_boxes_area = (MetaBoxesArea);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-box-visibility.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
=======

function PostTaxonomies() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTaxonomiesCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTaxonomies"], {
    taxonomyWrapper: function taxonomyWrapper(content, taxonomy) {
      return Object(external_this_wp_element_["createElement"])(taxonomy_panel, {
        taxonomy: taxonomy
      }, content);
    }
  }));
}

/* harmony default export */ var post_taxonomies = (PostTaxonomies);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/featured-image/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */



<<<<<<< HEAD
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
  isVisible: select(store_store).isEditorPanelEnabled(`meta-box-${id}`)
}))(MetaBoxVisibility));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
=======



/**
 * Module Constants
 */

var featured_image_PANEL_NAME = 'featured-image';

function FeaturedImage(_ref) {
  var isEnabled = _ref.isEnabled,
      isOpened = _ref.isOpened,
      postType = _ref.postType,
      onTogglePanel = _ref.onTogglePanel;

  if (!isEnabled) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFeaturedImageCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_lodash_["get"])(postType, ['labels', 'featured_image'], Object(external_this_wp_i18n_["__"])('Featured Image')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFeaturedImage"], null)));
}

var applyWithSelect = Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  var _select3 = select('core/edit-post'),
      isEditorPanelEnabled = _select3.isEditorPanelEnabled,
      isEditorPanelOpened = _select3.isEditorPanelOpened;

  return {
    postType: getPostType(getEditedPostAttribute('type')),
    isEnabled: isEditorPanelEnabled(featured_image_PANEL_NAME),
    isOpened: isEditorPanelOpened(featured_image_PANEL_NAME)
  };
});
var applyWithDispatch = Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      toggleEditorPanelOpened = _dispatch.toggleEditorPanelOpened;

  return {
    onTogglePanel: Object(external_lodash_["partial"])(toggleEditorPanelOpened, featured_image_PANEL_NAME)
  };
});
/* harmony default export */ var featured_image = (Object(external_this_wp_compose_["compose"])(applyWithSelect, applyWithDispatch)(FeaturedImage));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-excerpt/index.js


/**
 * WordPress dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */




<<<<<<< HEAD
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

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (metaBoxes !== null && metaBoxes !== void 0 ? metaBoxes : []).map(({
    id
  }) => (0,external_wp_element_namespaceObject.createElement)(meta_box_visibility, {
    key: id,
    id: id
  })), (0,external_wp_element_namespaceObject.createElement)(meta_boxes_area, {
    location: location
  }));
}

;// CONCATENATED MODULE: external ["wp","warning"]
const external_wp_warning_namespaceObject = window["wp"]["warning"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-document-setting-panel/index.js


/**
 * Defines as extensibility slot for the Settings sidebar
=======

/**
 * Module Constants
 */

var post_excerpt_PANEL_NAME = 'post-excerpt';

function PostExcerpt(_ref) {
  var isEnabled = _ref.isEnabled,
      isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel;

  if (!isEnabled) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostExcerptCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Excerpt'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostExcerpt"], null)));
}

/* harmony default export */ var post_excerpt = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isEnabled: select('core/edit-post').isEditorPanelEnabled(post_excerpt_PANEL_NAME),
    isOpened: select('core/edit-post').isEditorPanelOpened(post_excerpt_PANEL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleEditorPanelOpened(post_excerpt_PANEL_NAME);
    }
  };
})])(PostExcerpt));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-link/index.js


/**
 * External dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

/**
 * WordPress dependencies
 */





<<<<<<< HEAD
/**
 * Internal dependencies
 */



const {
  Fill: plugin_document_setting_panel_Fill,
  Slot: plugin_document_setting_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('PluginDocumentSettingPanel');

const PluginDocumentSettingFill = ({
  isEnabled,
  panelName,
  opened,
  onToggle,
  className,
  title,
  icon,
  children
}) => {
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
}), (0,external_wp_data_namespaceObject.withSelect)((select, {
  panelName
}) => {
  return {
    opened: select(store_store).isEditorPanelOpened(panelName),
    isEnabled: select(store_store).isEditorPanelEnabled(panelName)
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  panelName
}) => ({
  onToggle() {
    return dispatch(store_store).toggleEditorPanelOpened(panelName);
  }

})))(PluginDocumentSettingFill);
PluginDocumentSettingPanel.Slot = plugin_document_setting_panel_Slot;
/* harmony default export */ const plugin_document_setting_panel = (PluginDocumentSettingPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-sidebar/index.js
=======


/**
 * Module Constants
 */

var post_link_PANEL_NAME = 'post-link';

function PostLink(_ref) {
  var isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel,
      isEditable = _ref.isEditable,
      postLink = _ref.postLink,
      permalinkParts = _ref.permalinkParts,
      editPermalink = _ref.editPermalink,
      forceEmptyField = _ref.forceEmptyField,
      setState = _ref.setState,
      postTitle = _ref.postTitle,
      postSlug = _ref.postSlug,
      postID = _ref.postID;
  var prefix = permalinkParts.prefix,
      suffix = permalinkParts.suffix;
  var prefixElement, postNameElement, suffixElement;
  var currentSlug = postSlug || Object(external_this_wp_editor_["cleanForSlug"])(postTitle) || postID;

  if (isEditable) {
    prefixElement = prefix && Object(external_this_wp_element_["createElement"])("span", {
      className: "edit-post-post-link__link-prefix"
    }, prefix);
    postNameElement = currentSlug && Object(external_this_wp_element_["createElement"])("span", {
      className: "edit-post-post-link__link-post-name"
    }, currentSlug);
    suffixElement = suffix && Object(external_this_wp_element_["createElement"])("span", {
      className: "edit-post-post-link__link-suffix"
    }, suffix);
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Permalink'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, isEditable && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
    label: Object(external_this_wp_i18n_["__"])('URL'),
    value: forceEmptyField ? '' : currentSlug,
    onChange: function onChange(newValue) {
      editPermalink(newValue); // When we delete the field the permalink gets
      // reverted to the original value.
      // The forceEmptyField logic allows the user to have
      // the field temporarily empty while typing.

      if (!newValue) {
        if (!forceEmptyField) {
          setState({
            forceEmptyField: true
          });
        }

        return;
      }

      if (forceEmptyField) {
        setState({
          forceEmptyField: false
        });
      }
    },
    onBlur: function onBlur(event) {
      editPermalink(Object(external_this_wp_editor_["cleanForSlug"])(event.target.value));

      if (forceEmptyField) {
        setState({
          forceEmptyField: false
        });
      }
    }
  }), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-post-link__preview-label"
  }, Object(external_this_wp_i18n_["__"])('Preview')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
    className: "edit-post-post-link__link",
    href: postLink,
    target: "_blank"
  }, isEditable ? Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, prefixElement, postNameElement, suffixElement) : postLink));
}

/* harmony default export */ var post_link = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isEditedPostNew = _select.isEditedPostNew,
      isPermalinkEditable = _select.isPermalinkEditable,
      getCurrentPost = _select.getCurrentPost,
      isCurrentPostPublished = _select.isCurrentPostPublished,
      getPermalinkParts = _select.getPermalinkParts,
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core/edit-post'),
      isEditorPanelEnabled = _select2.isEditorPanelEnabled,
      isEditorPanelOpened = _select2.isEditorPanelOpened;

  var _select3 = select('core'),
      getPostType = _select3.getPostType;

  var _getCurrentPost = getCurrentPost(),
      link = _getCurrentPost.link,
      id = _getCurrentPost.id;

  var postTypeName = getEditedPostAttribute('type');
  var postType = getPostType(postTypeName);
  return {
    isNew: isEditedPostNew(),
    postLink: link,
    isEditable: isPermalinkEditable(),
    isPublished: isCurrentPostPublished(),
    isOpened: isEditorPanelOpened(post_link_PANEL_NAME),
    permalinkParts: getPermalinkParts(),
    isEnabled: isEditorPanelEnabled(post_link_PANEL_NAME),
    isViewable: Object(external_lodash_["get"])(postType, ['viewable'], false),
    postTitle: getEditedPostAttribute('title'),
    postSlug: getEditedPostAttribute('slug'),
    postID: id
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isEnabled = _ref2.isEnabled,
      isNew = _ref2.isNew,
      postLink = _ref2.postLink,
      isViewable = _ref2.isViewable,
      permalinkParts = _ref2.permalinkParts;
  return isEnabled && !isNew && postLink && isViewable && permalinkParts;
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      toggleEditorPanelOpened = _dispatch.toggleEditorPanelOpened;

  var _dispatch2 = dispatch('core/editor'),
      editPost = _dispatch2.editPost;

  return {
    onTogglePanel: function onTogglePanel() {
      return toggleEditorPanelOpened(post_link_PANEL_NAME);
    },
    editPermalink: function editPermalink(newSlug) {
      editPost({
        slug: newSlug
      });
    }
  };
}), Object(external_this_wp_compose_["withState"])({
  forceEmptyField: false
})])(PostLink));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/discussion-panel/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */





/**
<<<<<<< HEAD
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

function PluginSidebarEditPost({
  className,
  ...props
}) {
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
  return (0,external_wp_element_namespaceObject.createElement)(complementary_area, {
    panelClassName: className,
    className: "edit-post-sidebar",
    smallScreenTitle: postTitle || (0,external_wp_i18n_namespaceObject.__)('(no title)'),
    scope: "core/edit-post",
    toggleShortcut: shortcut,
    showIconLabels: showIconLabels,
    ...props
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/template-summary/index.js


/**
=======
 * Module Constants
 */

var discussion_panel_PANEL_NAME = 'discussion-panel';

function DiscussionPanel(_ref) {
  var isEnabled = _ref.isEnabled,
      isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel;

  if (!isEnabled) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: ['comments', 'trackbacks']
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Discussion'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: "comments"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostComments"], null))), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: "trackbacks"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPingbacks"], null)))));
}

/* harmony default export */ var discussion_panel = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isEnabled: select('core/edit-post').isEditorPanelEnabled(discussion_panel_PANEL_NAME),
    isOpened: select('core/edit-post').isEditorPanelOpened(discussion_panel_PANEL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleEditorPanelOpened(discussion_panel_PANEL_NAME);
    }
  };
})])(DiscussionPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/page-attributes/index.js


/**
 * External dependencies
 */

/**
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * WordPress dependencies
 */



<<<<<<< HEAD
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
  }, template?.title || template?.slug), (0,external_wp_element_namespaceObject.createElement)("p", null, template?.description))));
}

/* harmony default export */ const template_summary = (TemplateSummary);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-sidebar/index.js
=======



/**
 * Module Constants
 */

var page_attributes_PANEL_NAME = 'page-attributes';
function PageAttributes(_ref) {
  var isEnabled = _ref.isEnabled,
      isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel,
      postType = _ref.postType;

  if (!isEnabled || !postType) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageAttributesCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_lodash_["get"])(postType, ['labels', 'attributes'], Object(external_this_wp_i18n_["__"])('Page Attributes')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageTemplate"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageAttributesParent"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageAttributesOrder"], null))));
}
var page_attributes_applyWithSelect = Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core/edit-post'),
      isEditorPanelEnabled = _select2.isEditorPanelEnabled,
      isEditorPanelOpened = _select2.isEditorPanelOpened;

  var _select3 = select('core'),
      getPostType = _select3.getPostType;

  return {
    isEnabled: isEditorPanelEnabled(page_attributes_PANEL_NAME),
    isOpened: isEditorPanelOpened(page_attributes_PANEL_NAME),
    postType: getPostType(getEditedPostAttribute('type'))
  };
});
var page_attributes_applyWithDispatch = Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      toggleEditorPanelOpened = _dispatch.toggleEditorPanelOpened;

  return {
    onTogglePanel: Object(external_lodash_["partial"])(toggleEditorPanelOpened, page_attributes_PANEL_NAME)
  };
});
/* harmony default export */ var page_attributes = (Object(external_this_wp_compose_["compose"])(page_attributes_applyWithSelect, page_attributes_applyWithDispatch)(PageAttributes));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-sidebar/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */






<<<<<<< HEAD

/**
 * Internal dependencies
=======
/**
 * Internal Dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */













<<<<<<< HEAD

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
    closeLabel: (0,external_wp_i18n_namespaceObject.__)('Close Settings'),
    headerClassName: "edit-post-sidebar__panel-tabs"
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    title: (0,external_wp_i18n_namespaceObject.__)('Settings'),
    toggleShortcut: keyboardShortcut,
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? drawer_left : drawer_right,
    isActiveByDefault: SIDEBAR_ACTIVE_BY_DEFAULT
  }, !isTemplateMode && sidebarName === 'edit-post/document' && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(post_status, null), (0,external_wp_element_namespaceObject.createElement)(plugin_document_setting_panel.Slot, null), (0,external_wp_element_namespaceObject.createElement)(last_revision, null), (0,external_wp_element_namespaceObject.createElement)(post_taxonomies, null), (0,external_wp_element_namespaceObject.createElement)(featured_image, null), (0,external_wp_element_namespaceObject.createElement)(post_excerpt, null), (0,external_wp_element_namespaceObject.createElement)(discussion_panel, null), (0,external_wp_element_namespaceObject.createElement)(page_attributes, null), (0,external_wp_element_namespaceObject.createElement)(MetaBoxes, {
    location: "side"
  })), isTemplateMode && sidebarName === 'edit-post/document' && (0,external_wp_element_namespaceObject.createElement)(template_summary, null), sidebarName === 'edit-post/block' && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockInspector, null));
};

/* harmony default export */ const settings_sidebar = (SettingsSidebar);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/image.js

function WelcomeGuideImage({
  nonAnimatedSrc,
  animatedSrc
}) {
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
=======
var settings_sidebar_SettingsSidebar = function SettingsSidebar(_ref) {
  var sidebarName = _ref.sidebarName;
  return Object(external_this_wp_element_["createElement"])(sidebar, {
    name: sidebarName,
    label: Object(external_this_wp_i18n_["__"])('Editor settings')
  }, Object(external_this_wp_element_["createElement"])(settings_header, {
    sidebarName: sidebarName
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Panel"], null, sidebarName === 'edit-post/document' && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(post_status, null), Object(external_this_wp_element_["createElement"])(last_revision, null), Object(external_this_wp_element_["createElement"])(post_link, null), Object(external_this_wp_element_["createElement"])(post_taxonomies, null), Object(external_this_wp_element_["createElement"])(featured_image, null), Object(external_this_wp_element_["createElement"])(post_excerpt, null), Object(external_this_wp_element_["createElement"])(discussion_panel, null), Object(external_this_wp_element_["createElement"])(page_attributes, null), Object(external_this_wp_element_["createElement"])(meta_boxes, {
    location: "side"
  })), sidebarName === 'edit-post/block' && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: "edit-post-settings-sidebar__panel-block"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["BlockInspector"], null))));
};

/* harmony default export */ var settings_sidebar = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/edit-post'),
      getActiveGeneralSidebarName = _select.getActiveGeneralSidebarName,
      isEditorSidebarOpened = _select.isEditorSidebarOpened;

  return {
    isEditorSidebarOpened: isEditorSidebarOpened(),
    sidebarName: getActiveGeneralSidebarName()
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isEditorSidebarOpened = _ref2.isEditorSidebarOpened;
  return isEditorSidebarOpened;
}))(settings_sidebar_SettingsSidebar));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-publish-panel/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */


<<<<<<< HEAD


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
        href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/documentation/article/wordpress-block-editor/')
      }, (0,external_wp_i18n_namespaceObject.__)("Here's a detailed guide."))))
    }]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/template.js
=======
var plugin_post_publish_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginPostPublishPanel'),
    plugin_post_publish_panel_Fill = plugin_post_publish_panel_createSlotFill.Fill,
    plugin_post_publish_panel_Slot = plugin_post_publish_panel_createSlotFill.Slot;

var plugin_post_publish_panel_PluginPostPublishPanel = function PluginPostPublishPanel(_ref) {
  var children = _ref.children,
      className = _ref.className,
      title = _ref.title,
      _ref$initialOpen = _ref.initialOpen,
      initialOpen = _ref$initialOpen === void 0 ? false : _ref$initialOpen;
  return Object(external_this_wp_element_["createElement"])(plugin_post_publish_panel_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: className,
    initialOpen: initialOpen || !title,
    title: title
  }, children));
};

plugin_post_publish_panel_PluginPostPublishPanel.Slot = plugin_post_publish_panel_Slot;
/* harmony default export */ var plugin_post_publish_panel = (plugin_post_publish_panel_PluginPostPublishPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */


<<<<<<< HEAD

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
=======
var plugin_pre_publish_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginPrePublishPanel'),
    plugin_pre_publish_panel_Fill = plugin_pre_publish_panel_createSlotFill.Fill,
    plugin_pre_publish_panel_Slot = plugin_pre_publish_panel_createSlotFill.Slot;

var plugin_pre_publish_panel_PluginPrePublishPanel = function PluginPrePublishPanel(_ref) {
  var children = _ref.children,
      className = _ref.className,
      title = _ref.title,
      _ref$initialOpen = _ref.initialOpen,
      initialOpen = _ref$initialOpen === void 0 ? false : _ref$initialOpen;
  return Object(external_this_wp_element_["createElement"])(plugin_pre_publish_panel_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: className,
    initialOpen: initialOpen || !title,
    title: title
  }, children));
};

plugin_pre_publish_panel_PluginPrePublishPanel.Slot = plugin_pre_publish_panel_Slot;
/* harmony default export */ var plugin_pre_publish_panel = (plugin_pre_publish_panel_PluginPrePublishPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/fullscreen-mode/index.js




>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */

<<<<<<< HEAD
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

=======

var fullscreen_mode_FullscreenMode =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(FullscreenMode, _Component);

  function FullscreenMode() {
    Object(classCallCheck["a" /* default */])(this, FullscreenMode);

    return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(FullscreenMode).apply(this, arguments));
  }

  Object(createClass["a" /* default */])(FullscreenMode, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.isSticky = false;
      this.sync(); // `is-fullscreen-mode` is set in PHP as a body class by Gutenberg, and this causes
      // `sticky-menu` to be applied by WordPress and prevents the admin menu being scrolled
      // even if `is-fullscreen-mode` is then removed. Let's remove `sticky-menu` here as
      // a consequence of the FullscreenMode setup

      if (document.body.classList.contains('sticky-menu')) {
        this.isSticky = true;
        document.body.classList.remove('sticky-menu');
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      if (this.isSticky) {
        document.body.classList.add('sticky-menu');
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.isActive !== prevProps.isActive) {
        this.sync();
      }
    }
  }, {
    key: "sync",
    value: function sync() {
      var isActive = this.props.isActive;

      if (isActive) {
        document.body.classList.add('is-fullscreen-mode');
      } else {
        document.body.classList.remove('is-fullscreen-mode');
      }
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return FullscreenMode;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var fullscreen_mode = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isActive: select('core/edit-post').isFeatureActive('fullscreenMode')
  };
})(fullscreen_mode_FullscreenMode));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/index.js



/**
 * External dependencies
 */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * WordPress dependencies
 */



<<<<<<< HEAD
const {
  Fill: plugin_post_publish_panel_Fill,
  Slot: plugin_post_publish_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('PluginPostPublishPanel');

const PluginPostPublishPanelFill = ({
  children,
  className,
  title,
  initialOpen = false,
  icon
}) => (0,external_wp_element_namespaceObject.createElement)(plugin_post_publish_panel_Fill, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
  className: className,
  initialOpen: initialOpen || !title,
  title: title,
  icon: icon
}, children));
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
/* harmony default export */ const plugin_post_publish_panel = (PluginPostPublishPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js


/**
 * WordPress dependencies
=======






/**
 * Internal dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */



<<<<<<< HEAD
const {
  Fill: plugin_pre_publish_panel_Fill,
  Slot: plugin_pre_publish_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('PluginPrePublishPanel');

const PluginPrePublishPanelFill = ({
  children,
  className,
  title,
  initialOpen = false,
  icon
}) => (0,external_wp_element_namespaceObject.createElement)(plugin_pre_publish_panel_Fill, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
  className: className,
  initialOpen: initialOpen || !title,
  title: title,
  icon: icon
}, children));
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
/* harmony default export */ const plugin_pre_publish_panel = (PluginPrePublishPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/actions-panel.js


/**
 * WordPress dependencies
 */
=======






>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9





<<<<<<< HEAD
/**
 * Internal dependencies
 */




const {
  Fill: actions_panel_Fill,
  Slot: actions_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('ActionsPanel');
const ActionsPanelFill = (/* unused pure expression or super */ null && (actions_panel_Fill));
function ActionsPanel({
  setEntitiesSavedStatesCallback,
  closeEntitiesSavedStates,
  isEntitiesSavedStatesOpen
}) {
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
=======

function Layout(_ref) {
  var mode = _ref.mode,
      editorSidebarOpened = _ref.editorSidebarOpened,
      pluginSidebarOpened = _ref.pluginSidebarOpened,
      publishSidebarOpened = _ref.publishSidebarOpened,
      hasFixedToolbar = _ref.hasFixedToolbar,
      closePublishSidebar = _ref.closePublishSidebar,
      togglePublishSidebar = _ref.togglePublishSidebar,
      hasActiveMetaboxes = _ref.hasActiveMetaboxes,
      isSaving = _ref.isSaving,
      isMobileViewport = _ref.isMobileViewport,
      isRichEditingEnabled = _ref.isRichEditingEnabled;
  var sidebarIsOpened = editorSidebarOpened || pluginSidebarOpened || publishSidebarOpened;
  var className = classnames_default()('edit-post-layout', {
    'is-sidebar-opened': sidebarIsOpened,
    'has-fixed-toolbar': hasFixedToolbar
  });
  var publishLandmarkProps = {
    role: 'region',

    /* translators: accessibility text for the publish landmark region. */
    'aria-label': Object(external_this_wp_i18n_["__"])('Editor publish'),
    tabIndex: -1
  };
  return Object(external_this_wp_element_["createElement"])("div", {
    className: className
  }, Object(external_this_wp_element_["createElement"])(fullscreen_mode, null), Object(external_this_wp_element_["createElement"])(browser_url, null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["UnsavedChangesWarning"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["AutosaveMonitor"], null), Object(external_this_wp_element_["createElement"])(header, null), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-layout__content",
    role: "region"
    /* translators: accessibility text for the content landmark region. */
    ,
    "aria-label": Object(external_this_wp_i18n_["__"])('Editor content'),
    tabIndex: "-1"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorNotices"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PreserveScrollInReorder"], null), Object(external_this_wp_element_["createElement"])(components_keyboard_shortcuts, null), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal, null), Object(external_this_wp_element_["createElement"])(options_modal, null), (mode === 'text' || !isRichEditingEnabled) && Object(external_this_wp_element_["createElement"])(text_editor, null), isRichEditingEnabled && mode === 'visual' && Object(external_this_wp_element_["createElement"])(visual_editor, null), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-layout__metaboxes"
  }, Object(external_this_wp_element_["createElement"])(meta_boxes, {
    location: "normal"
  })), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-layout__metaboxes"
  }, Object(external_this_wp_element_["createElement"])(meta_boxes, {
    location: "advanced"
  }))), publishSidebarOpened ? Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPublishPanel"], Object(esm_extends["a" /* default */])({}, publishLandmarkProps, {
    onClose: closePublishSidebar,
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving,
    PrePublishExtension: plugin_pre_publish_panel.Slot,
    PostPublishExtension: plugin_post_publish_panel.Slot
  })) : Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", Object(esm_extends["a" /* default */])({
    className: "edit-post-toggle-publish-panel"
  }, publishLandmarkProps), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isDefault: true,
    type: "button",
    className: "edit-post-toggle-publish-panel__button",
    onClick: togglePublishSidebar,
    "aria-expanded": false
  }, Object(external_this_wp_i18n_["__"])('Open publish panel'))), Object(external_this_wp_element_["createElement"])(settings_sidebar, null), Object(external_this_wp_element_["createElement"])(sidebar.Slot, null), isMobileViewport && sidebarIsOpened && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ScrollLock"], null)), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Popover"].Slot, null), Object(external_this_wp_element_["createElement"])(external_this_wp_plugins_["PluginArea"], null));
}

/* harmony default export */ var layout = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    mode: select('core/edit-post').getEditorMode(),
    editorSidebarOpened: select('core/edit-post').isEditorSidebarOpened(),
    pluginSidebarOpened: select('core/edit-post').isPluginSidebarOpened(),
    publishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
    hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
    hasActiveMetaboxes: select('core/edit-post').hasMetaBoxes(),
    isSaving: select('core/edit-post').isSavingMetaBoxes(),
    isRichEditingEnabled: select('core/editor').getEditorSettings().richEditingEnabled
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      closePublishSidebar = _dispatch.closePublishSidebar,
      togglePublishSidebar = _dispatch.togglePublishSidebar;

  return {
    closePublishSidebar: closePublishSidebar,
    togglePublishSidebar: togglePublishSidebar
  };
}), external_this_wp_components_["navigateRegions"], Object(external_this_wp_viewport_["withViewportMatch"])({
  isMobileViewport: '< small'
}))(Layout));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/editor.js



>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */




<<<<<<< HEAD



=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */



<<<<<<< HEAD
function useStartPatterns() {
  // A pattern is a start pattern if it includes 'core/post-content' in its blockTypes,
  // and it has no postTypes declares and the current post type is page or if
  // the current post type is part of the postTypes declared.
  const {
    blockPatternsWithPostContentBlockType,
    postType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getPatternsByBlockTypes
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      getCurrentPostType
    } = select(external_wp_editor_namespaceObject.store);
    return {
      blockPatternsWithPostContentBlockType: getPatternsByBlockTypes('core/post-content'),
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

function PatternSelection({
  onChoosePattern
}) {
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
    isFullScreen: true,
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
=======

function Editor(_ref) {
  var settings = _ref.settings,
      hasFixedToolbar = _ref.hasFixedToolbar,
      focusMode = _ref.focusMode,
      post = _ref.post,
      initialEdits = _ref.initialEdits,
      onError = _ref.onError,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["settings", "hasFixedToolbar", "focusMode", "post", "initialEdits", "onError"]);

  if (!post) {
    return null;
  }

  var editorSettings = Object(objectSpread["a" /* default */])({}, settings, {
    hasFixedToolbar: hasFixedToolbar,
    focusMode: focusMode
  });

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["StrictMode"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorProvider"], Object(esm_extends["a" /* default */])({
    settings: editorSettings,
    post: post,
    initialEdits: initialEdits
  }, props), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["ErrorBoundary"], {
    onError: onError
  }, Object(external_this_wp_element_["createElement"])(layout, null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["KeyboardShortcuts"], {
    shortcuts: prevent_event_discovery
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLockedModal"], null)));
}

/* harmony default export */ var editor = (Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var postId = _ref2.postId,
      postType = _ref2.postType;
  return {
    hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
    focusMode: select('core/edit-post').isFeatureActive('focusMode'),
    post: select('core').getEntityRecord('postType', postType, postId)
  };
})(Editor));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-item.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



<<<<<<< HEAD









/**
 * Internal dependencies
 */

=======
/**
 * Internal dependencies
 */



var plugin_block_settings_menu_item_isEverySelectedBlockAllowed = function isEverySelectedBlockAllowed(selected, allowed) {
  return Object(external_lodash_["difference"])(selected, allowed).length === 0;
};
/**
 * Plugins may want to add an item to the menu either for every block
 * or only for the specific ones provided in the `allowedBlocks` component property.
 *
 * If there are multiple blocks selected the item will be rendered if every block
 * is of one allowed type (not necessarily the same).
 *
 * @param {string[]} selectedBlockNames Array containing the names of the blocks selected
 * @param {string[]} allowedBlockNames Array containing the names of the blocks allowed
 * @return {boolean} Whether the item will be rendered or not.
 */


var shouldRenderItem = function shouldRenderItem(selectedBlockNames, allowedBlockNames) {
  return !Array.isArray(allowedBlockNames) || plugin_block_settings_menu_item_isEverySelectedBlockAllowed(selectedBlockNames, allowedBlockNames);
};

var plugin_block_settings_menu_item_PluginBlockSettingsMenuItem = function PluginBlockSettingsMenuItem(_ref) {
  var allowedBlocks = _ref.allowedBlocks,
      icon = _ref.icon,
      label = _ref.label,
      onClick = _ref.onClick,
      small = _ref.small,
      role = _ref.role;
  return Object(external_this_wp_element_["createElement"])(plugin_block_settings_menu_group, null, function (_ref2) {
    var selectedBlocks = _ref2.selectedBlocks,
        onClose = _ref2.onClose;

    if (!shouldRenderItem(selectedBlocks, allowedBlocks)) {
      return null;
    }

    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
      className: "editor-block-settings-menu__control",
      onClick: Object(external_this_wp_compose_["compose"])(onClick, onClose),
      icon: icon || 'admin-plugins',
      label: small ? label : undefined,
      role: role
    }, !small && label);
  });
};

/* harmony default export */ var plugin_block_settings_menu_item = (plugin_block_settings_menu_item_PluginBlockSettingsMenuItem);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-more-menu-item/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9




<<<<<<< HEAD

=======
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9




<<<<<<< HEAD







const {
  getLayoutStyles
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
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

function useEditorStyles() {
  const {
    hasThemeStyleSupport,
    editorSettings
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    hasThemeStyleSupport: select(store_store).isFeatureActive('themeStyles'),
    editorSettings: select(external_wp_editor_namespaceObject.store).getEditorSettings()
  }), []); // Compute the default styles.

  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    var _editorSettings$style, _editorSettings$style2;

    const presetStyles = (_editorSettings$style = editorSettings.styles?.filter(style => style.__unstableType && style.__unstableType !== 'theme')) !== null && _editorSettings$style !== void 0 ? _editorSettings$style : [];
    const defaultEditorStyles = [...editorSettings.defaultEditorStyles, ...presetStyles]; // Has theme styles if the theme supports them and if some styles were not preset styles (in which case they're theme styles).

    const hasThemeStyles = hasThemeStyleSupport && presetStyles.length !== ((_editorSettings$style2 = editorSettings.styles?.length) !== null && _editorSettings$style2 !== void 0 ? _editorSettings$style2 : 0); // If theme styles are not present or displayed, ensure that
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

    return hasThemeStyles ? editorSettings.styles : defaultEditorStyles;
  }, [editorSettings.defaultEditorStyles, editorSettings.disableLayoutStyles, editorSettings.styles, hasThemeStyleSupport]);
}

function Layout() {
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  const isHugeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('huge', '>=');
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('large');
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
    isDistractionFree,
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
      isDistractionFree: select(store_store).isFeatureActive('distractionFree'),
      showBlockBreadcrumbs: select(store_store).isFeatureActive('showBlockBreadcrumbs'),
      // translators: Default label for the Document in the Block Breadcrumb.
      documentLabel: postTypeLabel || (0,external_wp_i18n_namespaceObject._x)('Document', 'noun')
    };
  }, []);
  const styles = useEditorStyles();

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
  const className = classnames_default()('edit-post-layout', 'is-mode-' + mode, {
    'is-sidebar-opened': sidebarIsOpened,
    'has-fixed-toolbar': hasFixedToolbar,
    'has-metaboxes': hasActiveMetaboxes,
    'show-icon-labels': showIconLabels,
    'is-distraction-free': isDistractionFree && isLargeViewport,
    'is-entity-save-view-open': !!entitiesSavedStatesCallback
  });
  const secondarySidebarLabel = isListViewOpened ? (0,external_wp_i18n_namespaceObject.__)('Document Overview') : (0,external_wp_i18n_namespaceObject.__)('Block Library');

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
    isDistractionFree: isDistractionFree && isLargeViewport,
    className: className,
    labels: { ...interfaceLabels,
      secondarySidebar: secondarySidebarLabel
    },
    header: (0,external_wp_element_namespaceObject.createElement)(header, {
      setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
    }),
    editorNotices: (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EditorNotices, null),
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
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, !isDistractionFree && (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EditorNotices, null), (mode === 'text' || !isRichEditingEnabled) && (0,external_wp_element_namespaceObject.createElement)(TextEditor, null), isRichEditingEnabled && mode === 'visual' && (0,external_wp_element_namespaceObject.createElement)(VisualEditor, {
      styles: styles
    }), !isDistractionFree && !isTemplateMode && (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__metaboxes"
    }, (0,external_wp_element_namespaceObject.createElement)(MetaBoxes, {
      location: "normal"
    }), (0,external_wp_element_namespaceObject.createElement)(MetaBoxes, {
      location: "advanced"
    })), isMobileViewport && sidebarIsOpened && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ScrollLock, null)),
    footer: !isDistractionFree && !isMobileViewport && showBlockBreadcrumbs && isRichEditingEnabled && mode === 'visual' && (0,external_wp_element_namespaceObject.createElement)("div", {
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
  }), (0,external_wp_element_namespaceObject.createElement)(EditPostPreferencesModal, null), (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcut_help_modal, null), (0,external_wp_element_namespaceObject.createElement)(WelcomeGuide, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostSyncStatusModal, null), (0,external_wp_element_namespaceObject.createElement)(StartPageOptions, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Popover.Slot, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_plugins_namespaceObject.PluginArea, {
    onError: onPluginAreaError
  }));
}

/* harmony default export */ const components_layout = (Layout);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/listener-hooks.js
=======
/**
 * Internal dependencies
 */



var plugin_more_menu_item_PluginMoreMenuItem = function PluginMoreMenuItem(_ref) {
  var _ref$onClick = _ref.onClick,
      onClick = _ref$onClick === void 0 ? external_lodash_["noop"] : _ref$onClick,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["onClick"]);

  return Object(external_this_wp_element_["createElement"])(plugins_more_menu_group, null, function (fillProps) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], Object(esm_extends["a" /* default */])({}, props, {
      onClick: Object(external_this_wp_compose_["compose"])(onClick, fillProps.onClose)
    }));
  });
};

/* harmony default export */ var plugin_more_menu_item = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon
  };
}))(plugin_more_menu_item_PluginMoreMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-sidebar/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




/**
 * Renders the plugin sidebar component.
 *
 * @param {Object} props Element props.
 *
 * @return {WPElement} Plugin sidebar component.
 */

function PluginSidebar(props) {
  var children = props.children,
      icon = props.icon,
      isActive = props.isActive,
      _props$isPinnable = props.isPinnable,
      isPinnable = _props$isPinnable === void 0 ? true : _props$isPinnable,
      isPinned = props.isPinned,
      sidebarName = props.sidebarName,
      title = props.title,
      togglePin = props.togglePin,
      toggleSidebar = props.toggleSidebar;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, isPinnable && Object(external_this_wp_element_["createElement"])(pinned_plugins, null, isPinned && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: icon,
    label: title,
    onClick: toggleSidebar,
    isToggled: isActive,
    "aria-expanded": isActive
  })), Object(external_this_wp_element_["createElement"])(sidebar, {
    name: sidebarName,
    label: Object(external_this_wp_i18n_["__"])('Editor plugins')
  }, Object(external_this_wp_element_["createElement"])(sidebar_header, {
    closeLabel: Object(external_this_wp_i18n_["__"])('Close plugin')
  }, Object(external_this_wp_element_["createElement"])("strong", null, title), isPinnable && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: isPinned ? 'star-filled' : 'star-empty',
    label: isPinned ? Object(external_this_wp_i18n_["__"])('Unpin from toolbar') : Object(external_this_wp_i18n_["__"])('Pin to toolbar'),
    onClick: togglePin,
    isToggled: isPinned,
    "aria-expanded": isPinned
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Panel"], null, children)));
}

/* harmony default export */ var plugin_sidebar = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon,
    sidebarName: "".concat(context.name, "/").concat(ownProps.name)
  };
}), Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var sidebarName = _ref.sidebarName;

  var _select = select('core/edit-post'),
      getActiveGeneralSidebarName = _select.getActiveGeneralSidebarName,
      isPluginItemPinned = _select.isPluginItemPinned;

  return {
    isActive: getActiveGeneralSidebarName() === sidebarName,
    isPinned: isPluginItemPinned(sidebarName)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref2) {
  var isActive = _ref2.isActive,
      sidebarName = _ref2.sidebarName;

  var _dispatch = dispatch('core/edit-post'),
      closeGeneralSidebar = _dispatch.closeGeneralSidebar,
      openGeneralSidebar = _dispatch.openGeneralSidebar,
      togglePinnedPluginItem = _dispatch.togglePinnedPluginItem;

  return {
    togglePin: function togglePin() {
      togglePinnedPluginItem(sidebarName);
    },
    toggleSidebar: function toggleSidebar() {
      if (isActive) {
        closeGeneralSidebar();
      } else {
        openGeneralSidebar(sidebarName);
      }
    }
  };
}))(PluginSidebar));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-sidebar-more-menu-item/index.js


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */



<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD
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

function EditorInitialization({
  postId
}) {
  useBlockSelectionListener(postId);
  useUpdatePostLinkListener(postId);
  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/block-default.js
=======

var plugin_sidebar_more_menu_item_PluginSidebarMoreMenuItem = function PluginSidebarMoreMenuItem(_ref) {
  var children = _ref.children,
      icon = _ref.icon,
      isSelected = _ref.isSelected,
      onClick = _ref.onClick;
  return Object(external_this_wp_element_["createElement"])(plugin_more_menu_item, {
    icon: isSelected ? 'yes' : icon,
    isSelected: isSelected,
    role: "menuitemcheckbox",
    onClick: onClick
  }, children);
};

/* harmony default export */ var plugin_sidebar_more_menu_item = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon,
    sidebarName: "".concat(context.name, "/").concat(ownProps.target)
  };
}), Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var sidebarName = _ref2.sidebarName;

  var _select = select('core/edit-post'),
      getActiveGeneralSidebarName = _select.getActiveGeneralSidebarName;

  return {
    isSelected: getActiveGeneralSidebarName() === sidebarName
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3) {
  var isSelected = _ref3.isSelected,
      sidebarName = _ref3.sidebarName;

  var _dispatch = dispatch('core/edit-post'),
      closeGeneralSidebar = _dispatch.closeGeneralSidebar,
      openGeneralSidebar = _dispatch.openGeneralSidebar;

  var onClick = isSelected ? closeGeneralSidebar : function () {
    return openGeneralSidebar(sidebarName);
  };
  return {
    onClick: onClick
  };
}))(plugin_sidebar_more_menu_item_PluginSidebarMoreMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */

<<<<<<< HEAD
const blockDefault = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M19 8h-1V6h-5v2h-2V6H6v2H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm.5 10c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-8c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v8z"
}));
/* harmony default export */ const block_default = (blockDefault);

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
/* harmony default export */ const library_cog = (cog);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/desktop.js


/**
 * WordPress dependencies
 */

const desktop = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20.5 16h-.7V8c0-1.1-.9-2-2-2H6.2c-1.1 0-2 .9-2 2v8h-.7c-.8 0-1.5.7-1.5 1.5h20c0-.8-.7-1.5-1.5-1.5zM5.7 8c0-.3.2-.5.5-.5h11.6c.3 0 .5.2.5.5v7.6H5.7V8z"
}));
/* harmony default export */ const library_desktop = (desktop);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/code.js


/**
 * WordPress dependencies
 */

const code = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20.8 10.7l-4.3-4.3-1.1 1.1 4.3 4.3c.1.1.1.3 0 .4l-4.3 4.3 1.1 1.1 4.3-4.3c.7-.8.7-1.9 0-2.6zM4.2 11.8l4.3-4.3-1-1-4.3 4.3c-.7.7-.7 1.8 0 2.5l4.3 4.3 1.1-1.1-4.3-4.3c-.2-.1-.2-.3-.1-.4z"
}));
/* harmony default export */ const library_code = (code);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/keyboard-close.js


/**
 * WordPress dependencies
 */

const keyboardClose = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18,0 L2,0 C0.9,0 0.01,0.9 0.01,2 L0,12 C0,13.1 0.9,14 2,14 L18,14 C19.1,14 20,13.1 20,12 L20,2 C20,0.9 19.1,0 18,0 Z M18,12 L2,12 L2,2 L18,2 L18,12 Z M9,3 L11,3 L11,5 L9,5 L9,3 Z M9,6 L11,6 L11,8 L9,8 L9,6 Z M6,3 L8,3 L8,5 L6,5 L6,3 Z M6,6 L8,6 L8,8 L6,8 L6,6 Z M3,6 L5,6 L5,8 L3,8 L3,6 Z M3,3 L5,3 L5,5 L3,5 L3,3 Z M6,9 L14,9 L14,11 L6,11 L6,9 Z M12,6 L14,6 L14,8 L12,8 L12,6 Z M12,3 L14,3 L14,5 L12,5 L12,3 Z M15,6 L17,6 L17,8 L15,8 L15,6 Z M15,3 L17,3 L17,5 L15,5 L15,3 Z M10,20 L14,16 L6,16 L10,20 Z"
}));
/* harmony default export */ const keyboard_close = (keyboardClose);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/commands/use-common-commands.js
/**
 * WordPress dependencies
 */
=======







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
  Object(external_this_wp_element_["unmountComponentAtNode"])(target);
  var reboot = reinitializeEditor.bind(null, postType, postId, target, settings, initialEdits);
  Object(external_this_wp_element_["render"])(Object(external_this_wp_element_["createElement"])(editor, {
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
 * The return value of this function is not necessary if we change where we
 * call initializeEditor(). This is due to metaBox timing.
 *
 * @param {string}  id           Unique identifier for editor instance.
 * @param {Object}  postType     Post type of the post to edit.
 * @param {Object}  postId       ID of the post to edit.
 * @param {?Object} settings     Editor settings object.
 * @param {Object}  initialEdits Programmatic edits to apply initially, to be
 *                               considered as non-user-initiated (bypass for
 *                               unsaved changes prompt).
 */

function initializeEditor(id, postType, postId, settings, initialEdits) {
  var target = document.getElementById(id);
  var reboot = reinitializeEditor.bind(null, postType, postId, target, settings, initialEdits);
  Object(external_this_wp_blockLibrary_["registerCoreBlocks"])(); // Show a console log warning if the browser is not in Standards rendering mode.

  var documentMode = document.compatMode === 'CSS1Compat' ? 'Standards' : 'Quirks';

  if (documentMode !== 'Standards') {
    // eslint-disable-next-line no-console
    console.warn("Your browser is using Quirks Mode. \nThis can cause rendering issues such as blocks overlaying meta boxes in the editor. Quirks Mode can be triggered by PHP errors or HTML code appearing before the opening <!DOCTYPE html>. Try checking the raw page source or your site's PHP error log and resolving errors there, removing any HTML before the doctype, or disabling plugins.");
  }

  Object(external_this_wp_data_["dispatch"])('core/nux').triggerGuide(['core/editor.inserter', 'core/editor.settings', 'core/editor.preview', 'core/editor.publish']);
  Object(external_this_wp_element_["render"])(Object(external_this_wp_element_["createElement"])(editor, {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits
  }), target);
}

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9






<<<<<<< HEAD
/**
 * Internal dependencies
 */




function useCommonCommands() {
  const {
    openGeneralSidebar,
    closeGeneralSidebar,
    switchEditorMode,
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    openModal
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const {
    editorMode,
    activeSidebar,
    isListViewOpen
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorMode,
      isListViewOpened
    } = select(store_store);
    return {
      activeSidebar: select(store).getActiveComplementaryArea(store_store.name),
      editorMode: getEditorMode(),
      isListViewOpen: isListViewOpened()
    };
  }, []);
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/open-settings-sidebar',
    label: (0,external_wp_i18n_namespaceObject.__)('Toggle settings sidebar'),
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? drawer_left : drawer_right,
    callback: ({
      close
    }) => {
      close();

      if (activeSidebar === 'edit-post/document') {
        closeGeneralSidebar();
      } else {
        openGeneralSidebar('edit-post/document');
      }
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/open-block-inspector',
    label: (0,external_wp_i18n_namespaceObject.__)('Toggle block inspector'),
    icon: block_default,
    callback: ({
      close
    }) => {
      close();

      if (activeSidebar === 'edit-post/block') {
        closeGeneralSidebar();
      } else {
        openGeneralSidebar('edit-post/block');
      }
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/toggle-distraction-free',
    label: (0,external_wp_i18n_namespaceObject.__)('Toggle distraction free'),
    icon: library_cog,
    callback: ({
      close
    }) => {
      toggle('core/edit-post', 'distractionFree');
      close();
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/toggle-spotlight-mode',
    label: (0,external_wp_i18n_namespaceObject.__)('Toggle spotlight mode'),
    icon: library_cog,
    callback: ({
      close
    }) => {
      toggle('core/edit-post', 'focusMode');
      close();
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/toggle-fullscreen-mode',
    label: (0,external_wp_i18n_namespaceObject.__)('Toggle fullscreen mode'),
    icon: library_desktop,
    callback: ({
      close
    }) => {
      toggle('core/edit-post', 'fullscreenMode');
      close();
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/toggle-list-view',
    label: (0,external_wp_i18n_namespaceObject.__)('Toggle list view'),
    icon: list_view,
    callback: ({
      close
    }) => {
      setIsListViewOpened(!isListViewOpen);
      close();
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/toggle-top-toolbar',
    label: (0,external_wp_i18n_namespaceObject.__)('Toggle top toolbar'),
    icon: library_cog,
    callback: ({
      close
    }) => {
      toggle('core/edit-post', 'fixedToolbar');
      close();
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/toggle-code-editor',
    label: (0,external_wp_i18n_namespaceObject.__)('Toggle code editor'),
    icon: library_code,
    callback: ({
      close
    }) => {
      switchEditorMode(editorMode === 'visual' ? 'text' : 'visual');
      close();
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/open-preferences',
    label: (0,external_wp_i18n_namespaceObject.__)('Open editor preferences'),
    icon: library_cog,
    callback: () => {
      openModal(PREFERENCES_MODAL_NAME);
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/open-shortcut-help',
    label: (0,external_wp_i18n_namespaceObject.__)('Open keyboard shortcuts'),
    icon: keyboard_close,
    callback: () => {
      openModal(KEYBOARD_SHORTCUT_HELP_MODAL_NAME);
    }
  });
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
const {
  useCommands
} = unlock(external_wp_coreCommands_namespaceObject.privateApis);

function Editor({
  postId,
  postType,
  settings,
  initialEdits,
  ...props
}) {
  useCommands();
  useCommonCommands();
  const {
    hasFixedToolbar,
    focusMode,
    isDistractionFree,
    hasInlineToolbar,
    post,
    preferredStyleVariations,
    hiddenBlockTypes,
    blockTypes,
    keepCaretInsideBlock,
    isTemplateMode,
    template
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getPostType$viewable;

    const {
      isFeatureActive,
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
      postObject = posts?.[0];
    } else {
      postObject = getEntityRecord('postType', postType, postId);
    }

    const supportsTemplateMode = getEditorSettings().supportsTemplateMode;
    const isViewable = (_getPostType$viewable = getPostType(postType)?.viewable) !== null && _getPostType$viewable !== void 0 ? _getPostType$viewable : false;
    const canEditTemplate = canUser('create', 'templates');
    return {
      hasFixedToolbar: isFeatureActive('fixedToolbar'),
      focusMode: isFeatureActive('focusMode'),
      isDistractionFree: isFeatureActive('distractionFree'),
      hasInlineToolbar: isFeatureActive('inlineToolbar'),
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
      isDistractionFree,
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
      const defaultAllowedBlockTypes = true === settings.allowedBlockTypes ? blockTypes.map(({
        name
      }) => name) : settings.allowedBlockTypes || [];
      result.allowedBlockTypes = defaultAllowedBlockTypes.filter(type => !hiddenBlockTypes.includes(type));
    }

    return result;
  }, [settings, hasFixedToolbar, hasInlineToolbar, focusMode, isDistractionFree, hiddenBlockTypes, blockTypes, preferredStyleVariations, setIsInserterOpened, updatePreferredStyleVariations, keepCaretInsideBlock]);

  if (!post) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_keyboardShortcuts_namespaceObject.ShortcutProvider, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SlotFillProvider, null, (0,external_wp_element_namespaceObject.createElement)(ExperimentalEditorProvider, {
    settings: editorSettings,
    post: post,
    initialEdits: initialEdits,
    useSubRegistry: false,
    __unstableTemplate: isTemplateMode ? template : undefined,
    ...props
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.ErrorBoundary, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_commands_namespaceObject.CommandMenu, null), (0,external_wp_element_namespaceObject.createElement)(EditorInitialization, {
    postId: postId
  }), (0,external_wp_element_namespaceObject.createElement)(components_layout, null)), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.PostLockedModal, null))));
}

/* harmony default export */ const editor = (Editor);

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


const PluginBlockSettingsMenuItem = ({
  allowedBlocks,
  icon,
  label,
  onClick,
  small,
  role
}) => (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockSettingsMenuControls, null, ({
  selectedBlocks,
  onClose
}) => {
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

/* harmony default export */ const plugin_block_settings_menu_item = (PluginBlockSettingsMenuItem);

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

/* harmony default export */ const plugin_more_menu_item = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
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
  , {
    __unstableExplicitMenuItem: true,
    scope: "core/edit-post",
    ...props
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/index.js


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */





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
  const root = (0,external_wp_element_namespaceObject.createRoot)(target);
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
  // If `distractionFree` mode is enabled, the block list view should not be open.


  if ((0,external_wp_data_namespaceObject.select)(store_store).isFeatureActive('showListViewByDefault') && !(0,external_wp_data_namespaceObject.select)(store_store).isFeatureActive('distractionFree')) {
    (0,external_wp_data_namespaceObject.dispatch)(store_store).setIsListViewOpened(true);
  }

  (0,external_wp_blockLibrary_namespaceObject.registerCoreBlocks)();
  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetBlock)({
    inserter: false
  });
  (0,external_wp_widgets_namespaceObject.registerWidgetGroupBlock)({
    inserter: false
  });

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
  });
  /*
   * Prevent adding post content block (except in query block) in the post editor.
   * Only add the filter when the post editor is initialized, not imported.
   * Also only add the filter(s) after registerCoreBlocks()
   * so that common filters in the block library are not overwritten.
   */

  (0,external_wp_hooks_namespaceObject.addFilter)('blockEditor.__unstableCanInsertBlockType', 'removePostContentFromInserter', (canInsert, blockType, rootClientId, {
    getBlockParentsByBlockName
  }) => {
    if (!(0,external_wp_data_namespaceObject.select)(store_store).isEditingTemplate() && blockType.name === 'core/post-content') {
      return getBlockParentsByBlockName(rootClientId, 'core/query').length > 0;
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
  root.render((0,external_wp_element_namespaceObject.createElement)(editor, {
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












})();

(window.wp = window.wp || {}).editPost = __webpack_exports__;
/******/ })()
;
=======


/***/ }),

/***/ "foSv":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _getPrototypeOf; });
function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

/***/ }),

/***/ "gQxa":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


function flattenIntoMap( map, effects ) {
	var i;
	if ( Array.isArray( effects ) ) {
		for ( i = 0; i < effects.length; i++ ) {
			flattenIntoMap( map, effects[ i ] );
		}
	} else {
		for ( i in effects ) {
			map[ i ] = ( map[ i ] || [] ).concat( effects[ i ] );
		}
	}
}

function refx( effects ) {
	var map = {},
		middleware;

	flattenIntoMap( map, effects );

	middleware = function( store ) {
		return function( next ) {
			return function( action ) {
				var handlers = map[ action.type ],
					result = next( action ),
					i, handlerAction;

				if ( handlers ) {
					for ( i = 0; i < handlers.length; i++ ) {
						handlerAction = handlers[ i ]( action, store );
						if ( handlerAction ) {
							store.dispatch( handlerAction );
						}
					}
				}

				return result;
			};
		};
	};

	middleware.effects = map;

	return middleware;
}

module.exports = refx;


/***/ }),

/***/ "gdqT":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["a11y"]; }());

/***/ }),

/***/ "jSdM":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["editor"]; }());

/***/ }),

/***/ "jZUy":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["coreData"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ "md7G":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("U8pU");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("JX7q");


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ "onLe":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["notices"]; }());

/***/ }),

/***/ "pPDe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";


var LEAF_KEY, hasWeakMap;

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {Object}
 */
LEAF_KEY = {};

/**
 * Whether environment supports WeakMap.
 *
 * @type {boolean}
 */
hasWeakMap = typeof WeakMap !== 'undefined';

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @param {*} value Value to return.
 *
 * @return {Array} Value returned as entry in array.
 */
function arrayOf( value ) {
	return [ value ];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike( value ) {
	return !! value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Object} Cache object.
 */
function createCache() {
	var cache = {
		clear: function() {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {Array}  a         First array.
 * @param {Array}  b         Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual( a, b, fromIndex ) {
	var i;

	if ( a.length !== b.length ) {
		return false;
	}

	for ( i = fromIndex; i < a.length; i++ ) {
		if ( a[ i ] !== b[ i ] ) {
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
 * @param {Function} selector      Selector function.
 * @param {Function} getDependants Dependant getter returning an immutable
 *                                 reference or array of reference used in
 *                                 cache bust consideration.
 *
 * @return {Function} Memoized selector.
 */
/* harmony default export */ __webpack_exports__["a"] = (function( selector, getDependants ) {
	var rootCache, getCache;

	// Use object source as dependant if getter not provided
	if ( ! getDependants ) {
		getDependants = arrayOf;
	}

	/**
	 * Returns the root cache. If WeakMap is supported, this is assigned to the
	 * root WeakMap cache set, otherwise it is a shared instance of the default
	 * cache object.
	 *
	 * @return {(WeakMap|Object)} Root cache object.
	 */
	function getRootCache() {
		return rootCache;
	}

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
	 * @param {Array} dependants Selector dependants.
	 *
	 * @return {Object} Cache object.
	 */
	function getWeakMapCache( dependants ) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i, dependant, map, cache;

		for ( i = 0; i < dependants.length; i++ ) {
			dependant = dependants[ i ];

			// Can only compose WeakMap from object-like key.
			if ( ! isObjectLike( dependant ) ) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if ( caches.has( dependant ) ) {
				// Traverse into nested WeakMap.
				caches = caches.get( dependant );
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set( dependant, map );
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if ( ! caches.has( LEAF_KEY ) ) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set( LEAF_KEY, cache );
		}

		return caches.get( LEAF_KEY );
	}

	// Assign cache handler by availability of WeakMap
	getCache = hasWeakMap ? getWeakMapCache : getRootCache;

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = hasWeakMap ? new WeakMap() : createCache();
	}

	// eslint-disable-next-line jsdoc/check-param-names
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {Object} source    Source object for derivation.
	 * @param {...*}   extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	function callSelector( /* source, ...extraArgs */ ) {
		var len = arguments.length,
			cache, node, i, args, dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		dependants = getDependants.apply( null, args );
		cache = getCache( dependants );

		// If not guaranteed uniqueness by dependants (primitive type or lack
		// of WeakMap support), shallow compare against last dependants and, if
		// references have changed, destroy cache to recalculate result.
		if ( ! cache.isUniqueByDependants ) {
			if ( cache.lastDependants && ! isShallowEqual( dependants, cache.lastDependants, 0 ) ) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while ( node ) {
			// Check whether node arguments match arguments
			if ( ! isShallowEqual( node.args, args, 1 ) ) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== cache.head ) {
				// Adjust siblings to point to each other.
				node.prev.next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				cache.head.prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = {
			// Generate the result from original function
			val: selector.apply( null, args ),
		};

		// Avoid including the source object in the cache.
		args[ 0 ] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( cache.head ) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = getDependants;
	callSelector.clear = clear;
	clear();

	return callSelector;
});


/***/ }),

/***/ "rePB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _defineProperty; });
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "vpQ4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("rePB");

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? Object(arguments[i]) : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(target, key, source[key]);
    });
  }

  return target;
}

/***/ }),

/***/ "vuIU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _createClass; });
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

/***/ }),

/***/ "wx14":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _extends; });
function _extends() {
  _extends = Object.assign || function (target) {
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

/***/ }),

/***/ "ywyh":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ })

/******/ });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
