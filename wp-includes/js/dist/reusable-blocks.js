/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "ReusableBlocksMenuItems": function() { return /* reexport */ reusable_blocks_menu_items; },
  "store": function() { return /* reexport */ store; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/reusable-blocks/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "__experimentalConvertBlockToStatic": function() { return __experimentalConvertBlockToStatic; },
  "__experimentalConvertBlocksToReusable": function() { return __experimentalConvertBlocksToReusable; },
  "__experimentalDeleteReusableBlock": function() { return __experimentalDeleteReusableBlock; },
  "__experimentalSetEditingReusableBlock": function() { return __experimentalSetEditingReusableBlock; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/reusable-blocks/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "__experimentalIsEditingReusableBlock": function() { return __experimentalIsEditingReusableBlock; }
});

;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: external ["wp","blocks"]
var external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/reusable-blocks/build-module/store/actions.js
/**
 * WordPress dependencies
 */



/**
 * Returns a generator converting a reusable block into a static block.
 *
 * @param {string} clientId The client ID of the block to attach.
 */

const __experimentalConvertBlockToStatic = clientId => _ref => {
  let {
    registry
  } = _ref;
  const oldBlock = registry.select(external_wp_blockEditor_namespaceObject.store).getBlock(clientId);
  const reusableBlock = registry.select('core').getEditedEntityRecord('postType', 'wp_block', oldBlock.attributes.ref);
  const newBlocks = (0,external_wp_blocks_namespaceObject.parse)(typeof reusableBlock.content === 'function' ? reusableBlock.content(reusableBlock) : reusableBlock.content);
  registry.dispatch(external_wp_blockEditor_namespaceObject.store).replaceBlocks(oldBlock.clientId, newBlocks);
};
/**
 * Returns a generator converting one or more static blocks into a reusable block.
 *
 * @param {string[]} clientIds The client IDs of the block to detach.
 * @param {string}   title     Reusable block title.
 */

const __experimentalConvertBlocksToReusable = (clientIds, title) => async _ref2 => {
  let {
    registry,
    dispatch
  } = _ref2;
  const reusableBlock = {
    title: title || (0,external_wp_i18n_namespaceObject.__)('Untitled Reusable block'),
    content: (0,external_wp_blocks_namespaceObject.serialize)(registry.select(external_wp_blockEditor_namespaceObject.store).getBlocksByClientId(clientIds)),
    status: 'publish'
  };
  const updatedRecord = await registry.dispatch('core').saveEntityRecord('postType', 'wp_block', reusableBlock);
  const newBlock = (0,external_wp_blocks_namespaceObject.createBlock)('core/block', {
    ref: updatedRecord.id
  });
  registry.dispatch(external_wp_blockEditor_namespaceObject.store).replaceBlocks(clientIds, newBlock);

  dispatch.__experimentalSetEditingReusableBlock(newBlock.clientId, true);
};
/**
 * Returns a generator deleting a reusable block.
 *
 * @param {string} id The ID of the reusable block to delete.
 */

const __experimentalDeleteReusableBlock = id => async _ref3 => {
  let {
    registry
  } = _ref3;
  const reusableBlock = registry.select('core').getEditedEntityRecord('postType', 'wp_block', id); // Don't allow a reusable block with a temporary ID to be deleted.

  if (!reusableBlock) {
    return;
  } // Remove any other blocks that reference this reusable block.


  const allBlocks = registry.select(external_wp_blockEditor_namespaceObject.store).getBlocks();
  const associatedBlocks = allBlocks.filter(block => (0,external_wp_blocks_namespaceObject.isReusableBlock)(block) && block.attributes.ref === id);
  const associatedBlockClientIds = associatedBlocks.map(block => block.clientId); // Remove the parsed block.

  if (associatedBlockClientIds.length) {
    registry.dispatch(external_wp_blockEditor_namespaceObject.store).removeBlocks(associatedBlockClientIds);
  }

  await registry.dispatch('core').deleteEntityRecord('postType', 'wp_block', id);
};
/**
 * Returns an action descriptor for SET_EDITING_REUSABLE_BLOCK action.
 *
 * @param {string}  clientId  The clientID of the reusable block to target.
 * @param {boolean} isEditing Whether the block should be in editing state.
 * @return {Object} Action descriptor.
 */

function __experimentalSetEditingReusableBlock(clientId, isEditing) {
  return {
    type: 'SET_EDITING_REUSABLE_BLOCK',
    clientId,
    isEditing
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/reusable-blocks/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

function isEditingReusableBlock() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  if ((action === null || action === void 0 ? void 0 : action.type) === 'SET_EDITING_REUSABLE_BLOCK') {
    return { ...state,
      [action.clientId]: action.isEditing
    };
  }

  return state;
}
/* harmony default export */ var reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  isEditingReusableBlock
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/reusable-blocks/build-module/store/selectors.js
/**
 * Returns true if reusable block is in the editing state.
 *
 * @param {Object} state    Global application state.
 * @param {number} clientId the clientID of the block.
 * @return {boolean} Whether the reusable block is in the editing state.
 */
function __experimentalIsEditingReusableBlock(state, clientId) {
  return state.isEditingReusableBlock[clientId];
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/reusable-blocks/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




const STORE_NAME = 'core/reusable-blocks';
/**
 * Store definition for the reusable blocks namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  actions: actions_namespaceObject,
  reducer: reducer,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","primitives"]
var external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/symbol.js


/**
 * WordPress dependencies
 */

const symbol = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z"
}));
/* harmony default export */ var library_symbol = (symbol);

;// CONCATENATED MODULE: external ["wp","notices"]
var external_wp_notices_namespaceObject = window["wp"]["notices"];
;// CONCATENATED MODULE: external ["wp","coreData"]
var external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-block-convert-button.js


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */


/**
 * Menu control to convert block(s) to reusable block.
 *
 * @param {Object}   props              Component props.
 * @param {string[]} props.clientIds    Client ids of selected blocks.
 * @param {string}   props.rootClientId ID of the currently selected top-level block.
 * @return {import('@wordpress/element').WPComponent} The menu control or null.
 */

function ReusableBlockConvertButton(_ref) {
  let {
    clientIds,
    rootClientId
  } = _ref;
  const [isModalOpen, setIsModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)('');
  const canConvert = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getBlocksByClientId;

    const {
      canUser
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      getBlocksByClientId,
      canInsertBlockType
    } = select(external_wp_blockEditor_namespaceObject.store);
    const blocks = (_getBlocksByClientId = getBlocksByClientId(clientIds)) !== null && _getBlocksByClientId !== void 0 ? _getBlocksByClientId : [];
    const isReusable = blocks.length === 1 && blocks[0] && (0,external_wp_blocks_namespaceObject.isReusableBlock)(blocks[0]) && !!select(external_wp_coreData_namespaceObject.store).getEntityRecord('postType', 'wp_block', blocks[0].attributes.ref);

    const _canConvert = // Hide when this is already a reusable block.
    !isReusable && // Hide when reusable blocks are disabled.
    canInsertBlockType('core/block', rootClientId) && blocks.every(block => // Guard against the case where a regular block has *just* been converted.
    !!block && // Hide on invalid blocks.
    block.isValid && // Hide when block doesn't support being made reusable.
    (0,external_wp_blocks_namespaceObject.hasBlockSupport)(block.name, 'reusable', true)) && // Hide when current doesn't have permission to do that.
    !!canUser('create', 'blocks');

    return _canConvert;
  }, [clientIds]);
  const {
    __experimentalConvertBlocksToReusable: convertBlocksToReusable
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const {
    createSuccessNotice,
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const onConvert = (0,external_wp_element_namespaceObject.useCallback)(async function (reusableBlockTitle) {
    try {
      await convertBlocksToReusable(clientIds, reusableBlockTitle);
      createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Reusable block created.'), {
        type: 'snackbar'
      });
    } catch (error) {
      createErrorNotice(error.message, {
        type: 'snackbar'
      });
    }
  }, [clientIds]);

  if (!canConvert) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockSettingsMenuControls, null, _ref2 => {
    let {
      onClose
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      icon: library_symbol,
      onClick: () => {
        setIsModalOpen(true);
      }
    }, (0,external_wp_i18n_namespaceObject.__)('Create Reusable block')), isModalOpen && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
      title: (0,external_wp_i18n_namespaceObject.__)('Create Reusable block'),
      closeLabel: (0,external_wp_i18n_namespaceObject.__)('Close'),
      onRequestClose: () => {
        setIsModalOpen(false);
        setTitle('');
      },
      overlayClassName: "reusable-blocks-menu-items__convert-modal"
    }, (0,external_wp_element_namespaceObject.createElement)("form", {
      onSubmit: event => {
        event.preventDefault();
        onConvert(title);
        setIsModalOpen(false);
        setTitle('');
        onClose();
      }
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
      label: (0,external_wp_i18n_namespaceObject.__)('Name'),
      value: title,
      onChange: setTitle
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Flex, {
      className: "reusable-blocks-menu-items__convert-modal-actions",
      justify: "flex-end"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      variant: "tertiary",
      onClick: () => {
        setIsModalOpen(false);
        setTitle('');
      }
    }, (0,external_wp_i18n_namespaceObject.__)('Cancel'))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      variant: "primary",
      type: "submit"
    }, (0,external_wp_i18n_namespaceObject.__)('Save')))))));
  });
}

;// CONCATENATED MODULE: external ["wp","url"]
var external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-blocks-manage-button.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



function ReusableBlocksManageButton(_ref) {
  let {
    clientId
  } = _ref;
  const {
    canRemove,
    isVisible
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getBlock,
      canRemoveBlock
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      canUser
    } = select(external_wp_coreData_namespaceObject.store);
    const reusableBlock = getBlock(clientId);
    return {
      canRemove: canRemoveBlock(clientId),
      isVisible: !!reusableBlock && (0,external_wp_blocks_namespaceObject.isReusableBlock)(reusableBlock) && !!canUser('update', 'blocks', reusableBlock.attributes.ref)
    };
  }, [clientId]);
  const {
    __experimentalConvertBlockToStatic: convertBlockToStatic
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);

  if (!isVisible) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockSettingsMenuControls, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    href: (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
      post_type: 'wp_block'
    })
  }, (0,external_wp_i18n_namespaceObject.__)('Manage Reusable blocks')), canRemove && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => convertBlockToStatic(clientId)
  }, (0,external_wp_i18n_namespaceObject.__)('Convert to regular blocks')));
}

/* harmony default export */ var reusable_blocks_manage_button = (ReusableBlocksManageButton);

;// CONCATENATED MODULE: ./node_modules/@wordpress/reusable-blocks/build-module/components/reusable-blocks-menu-items/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function ReusableBlocksMenuItems(_ref) {
  let {
    clientIds,
    rootClientId
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(ReusableBlockConvertButton, {
    clientIds: clientIds,
    rootClientId: rootClientId
  }), clientIds.length === 1 && (0,external_wp_element_namespaceObject.createElement)(reusable_blocks_manage_button, {
    clientId: clientIds[0]
  }));
}

/* harmony default export */ var reusable_blocks_menu_items = ((0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getSelectedBlockClientIds
  } = select(external_wp_blockEditor_namespaceObject.store);
  return {
    clientIds: getSelectedBlockClientIds()
  };
})(ReusableBlocksMenuItems));

;// CONCATENATED MODULE: ./node_modules/@wordpress/reusable-blocks/build-module/components/index.js


;// CONCATENATED MODULE: ./node_modules/@wordpress/reusable-blocks/build-module/index.js



(window.wp = window.wp || {}).reusableBlocks = __webpack_exports__;
/******/ })()
;