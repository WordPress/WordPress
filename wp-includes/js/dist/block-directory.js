/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "store": function() { return /* reexport */ store; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-directory/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "getDownloadableBlocks": function() { return getDownloadableBlocks; },
  "getErrorNoticeForBlock": function() { return getErrorNoticeForBlock; },
  "getErrorNotices": function() { return getErrorNotices; },
  "getInstalledBlockTypes": function() { return getInstalledBlockTypes; },
  "getNewBlockTypes": function() { return getNewBlockTypes; },
  "getUnusedBlockTypes": function() { return getUnusedBlockTypes; },
  "isInstalling": function() { return isInstalling; },
  "isRequestingDownloadableBlocks": function() { return isRequestingDownloadableBlocks; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-directory/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "addInstalledBlockType": function() { return addInstalledBlockType; },
  "clearErrorNotice": function() { return clearErrorNotice; },
  "fetchDownloadableBlocks": function() { return fetchDownloadableBlocks; },
  "installBlockType": function() { return installBlockType; },
  "receiveDownloadableBlocks": function() { return receiveDownloadableBlocks; },
  "removeInstalledBlockType": function() { return removeInstalledBlockType; },
  "setErrorNotice": function() { return setErrorNotice; },
  "setIsInstalling": function() { return setIsInstalling; },
  "uninstallBlockType": function() { return uninstallBlockType; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-directory/build-module/store/resolvers.js
var resolvers_namespaceObject = {};
__webpack_require__.r(resolvers_namespaceObject);
__webpack_require__.d(resolvers_namespaceObject, {
  "getDownloadableBlocks": function() { return resolvers_getDownloadableBlocks; }
});

;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","plugins"]
var external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// CONCATENATED MODULE: external ["wp","hooks"]
var external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: external ["wp","blocks"]
var external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","editor"]
var external_wp_editor_namespaceObject = window["wp"]["editor"];
;// CONCATENATED MODULE: external "lodash"
var external_lodash_namespaceObject = window["lodash"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/reducer.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Reducer returning an array of downloadable blocks.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

const downloadableBlocks = function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'FETCH_DOWNLOADABLE_BLOCKS':
      return { ...state,
        [action.filterValue]: {
          isRequesting: true
        }
      };

    case 'RECEIVE_DOWNLOADABLE_BLOCKS':
      return { ...state,
        [action.filterValue]: {
          results: action.downloadableBlocks,
          isRequesting: false
        }
      };
  }

  return state;
};
/**
 * Reducer managing the installation and deletion of blocks.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

const blockManagement = function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    installedBlockTypes: [],
    isInstalling: {}
  };
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ADD_INSTALLED_BLOCK_TYPE':
      return { ...state,
        installedBlockTypes: [...state.installedBlockTypes, action.item]
      };

    case 'REMOVE_INSTALLED_BLOCK_TYPE':
      return { ...state,
        installedBlockTypes: state.installedBlockTypes.filter(blockType => blockType.name !== action.item.name)
      };

    case 'SET_INSTALLING_BLOCK':
      return { ...state,
        isInstalling: { ...state.isInstalling,
          [action.blockId]: action.isInstalling
        }
      };
  }

  return state;
};
/**
 * Reducer returning an object of error notices.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

const errorNotices = function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_ERROR_NOTICE':
      return { ...state,
        [action.blockId]: {
          message: action.message,
          isFatal: action.isFatal
        }
      };

    case 'CLEAR_ERROR_NOTICE':
      return (0,external_lodash_namespaceObject.omit)(state, action.blockId);
  }

  return state;
};
/* harmony default export */ var reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  downloadableBlocks,
  blockManagement,
  errorNotices
}));

;// CONCATENATED MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/utils/has-block-type.js
/**
 * Check if a block list contains a specific block type. Recursively searches
 * through `innerBlocks` if they exist.
 *
 * @param {Object}   blockType A block object to search for.
 * @param {Object[]} blocks    The list of blocks to look through.
 *
 * @return {boolean} Whether the blockType is found.
 */
function hasBlockType(blockType) {
  let blocks = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];

  if (!blocks.length) {
    return false;
  }

  if (blocks.some(_ref => {
    let {
      name
    } = _ref;
    return name === blockType.name;
  })) {
    return true;
  }

  for (let i = 0; i < blocks.length; i++) {
    if (hasBlockType(blockType, blocks[i].innerBlocks)) {
      return true;
    }
  }

  return false;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/selectors.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Returns true if application is requesting for downloadable blocks.
 *
 * @param {Object} state       Global application state.
 * @param {string} filterValue Search string.
 *
 * @return {boolean} Whether a request is in progress for the blocks list.
 */

function isRequestingDownloadableBlocks(state, filterValue) {
  var _state$downloadableBl, _state$downloadableBl2;

  return (_state$downloadableBl = (_state$downloadableBl2 = state.downloadableBlocks[filterValue]) === null || _state$downloadableBl2 === void 0 ? void 0 : _state$downloadableBl2.isRequesting) !== null && _state$downloadableBl !== void 0 ? _state$downloadableBl : false;
}
/**
 * Returns the available uninstalled blocks.
 *
 * @param {Object} state       Global application state.
 * @param {string} filterValue Search string.
 *
 * @return {Array} Downloadable blocks.
 */

function getDownloadableBlocks(state, filterValue) {
  var _state$downloadableBl3, _state$downloadableBl4;

  return (_state$downloadableBl3 = (_state$downloadableBl4 = state.downloadableBlocks[filterValue]) === null || _state$downloadableBl4 === void 0 ? void 0 : _state$downloadableBl4.results) !== null && _state$downloadableBl3 !== void 0 ? _state$downloadableBl3 : [];
}
/**
 * Returns the block types that have been installed on the server in this
 * session.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} Block type items
 */

function getInstalledBlockTypes(state) {
  return state.blockManagement.installedBlockTypes;
}
/**
 * Returns block types that have been installed on the server and used in the
 * current post.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} Block type items.
 */

const getNewBlockTypes = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const usedBlockTree = select(external_wp_blockEditor_namespaceObject.store).getBlocks();
  const installedBlockTypes = getInstalledBlockTypes(state);
  return installedBlockTypes.filter(blockType => hasBlockType(blockType, usedBlockTree));
});
/**
 * Returns the block types that have been installed on the server but are not
 * used in the current post.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} Block type items.
 */

const getUnusedBlockTypes = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const usedBlockTree = select(external_wp_blockEditor_namespaceObject.store).getBlocks();
  const installedBlockTypes = getInstalledBlockTypes(state);
  return installedBlockTypes.filter(blockType => !hasBlockType(blockType, usedBlockTree));
});
/**
 * Returns true if a block plugin install is in progress.
 *
 * @param {Object} state   Global application state.
 * @param {string} blockId Id of the block.
 *
 * @return {boolean} Whether this block is currently being installed.
 */

function isInstalling(state, blockId) {
  return state.blockManagement.isInstalling[blockId] || false;
}
/**
 * Returns all block error notices.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Object with error notices.
 */

function getErrorNotices(state) {
  return state.errorNotices;
}
/**
 * Returns the error notice for a given block.
 *
 * @param {Object} state   Global application state.
 * @param {string} blockId The ID of the block plugin. eg: my-block
 *
 * @return {string|boolean} The error text, or false if no error.
 */

function getErrorNoticeForBlock(state, blockId) {
  return state.errorNotices[blockId];
}

;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["wp","notices"]
var external_wp_notices_namespaceObject = window["wp"]["notices"];
;// CONCATENATED MODULE: external ["wp","url"]
var external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/load-assets.js
/**
 * WordPress dependencies
 */

/**
 * Load an asset for a block.
 *
 * This function returns a Promise that will resolve once the asset is loaded,
 * or in the case of Stylesheets and Inline JavaScript, will resolve immediately.
 *
 * @param {HTMLElement} el A HTML Element asset to inject.
 *
 * @return {Promise} Promise which will resolve when the asset is loaded.
 */

const loadAsset = el => {
  return new Promise((resolve, reject) => {
    /*
     * Reconstruct the passed element, this is required as inserting the Node directly
     * won't always fire the required onload events, even if the asset wasn't already loaded.
     */
    const newNode = document.createElement(el.nodeName);
    ['id', 'rel', 'src', 'href', 'type'].forEach(attr => {
      if (el[attr]) {
        newNode[attr] = el[attr];
      }
    }); // Append inline <script> contents.

    if (el.innerHTML) {
      newNode.appendChild(document.createTextNode(el.innerHTML));
    }

    newNode.onload = () => resolve(true);

    newNode.onerror = () => reject(new Error('Error loading asset.'));

    document.body.appendChild(newNode); // Resolve Stylesheets and Inline JavaScript immediately.

    if ('link' === newNode.nodeName.toLowerCase() || 'script' === newNode.nodeName.toLowerCase() && !newNode.src) {
      resolve();
    }
  });
};
/**
 * Load the asset files for a block
 */

async function loadAssets() {
  /*
   * Fetch the current URL (post-new.php, or post.php?post=1&action=edit) and compare the
   * JavaScript and CSS assets loaded between the pages. This imports the required assets
   * for the block into the current page while not requiring that we know them up-front.
   * In the future this can be improved by reliance upon block.json and/or a script-loader
   * dependency API.
   */
  const response = await external_wp_apiFetch_default()({
    url: document.location.href,
    parse: false
  });
  const data = await response.text();
  const doc = new window.DOMParser().parseFromString(data, 'text/html');
  const newAssets = Array.from(doc.querySelectorAll('link[rel="stylesheet"],script')).filter(asset => asset.id && !document.getElementById(asset.id));
  /*
   * Load each asset in order, as they may depend upon an earlier loaded script.
   * Stylesheets and Inline Scripts will resolve immediately upon insertion.
   */

  for (const newAsset of newAssets) {
    await loadAsset(newAsset);
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/utils/get-plugin-url.js
/**
 * Get the plugin's direct API link out of a block-directory response.
 *
 * @param {Object} block The block object
 *
 * @return {string} The plugin URL, if exists.
 */
function getPluginUrl(block) {
  if (!block) {
    return false;
  }

  const link = block.links['wp:plugin'] || block.links.self;

  if (link && link.length) {
    return link[0].href;
  }

  return false;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/actions.js
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
 * Returns an action object used in signalling that the downloadable blocks
 * have been requested and are loading.
 *
 * @param {string} filterValue Search string.
 *
 * @return {Object} Action object.
 */

function fetchDownloadableBlocks(filterValue) {
  return {
    type: 'FETCH_DOWNLOADABLE_BLOCKS',
    filterValue
  };
}
/**
 * Returns an action object used in signalling that the downloadable blocks
 * have been updated.
 *
 * @param {Array}  downloadableBlocks Downloadable blocks.
 * @param {string} filterValue        Search string.
 *
 * @return {Object} Action object.
 */

function receiveDownloadableBlocks(downloadableBlocks, filterValue) {
  return {
    type: 'RECEIVE_DOWNLOADABLE_BLOCKS',
    downloadableBlocks,
    filterValue
  };
}
/**
 * Action triggered to install a block plugin.
 *
 * @param {Object} block The block item returned by search.
 *
 * @return {boolean} Whether the block was successfully installed & loaded.
 */

const installBlockType = block => async _ref => {
  let {
    registry,
    dispatch
  } = _ref;
  const {
    id,
    name
  } = block;
  let success = false;
  dispatch.clearErrorNotice(id);

  try {
    dispatch.setIsInstalling(id, true); // If we have a wp:plugin link, the plugin is installed but inactive.

    const url = getPluginUrl(block);
    let links = {};

    if (url) {
      await external_wp_apiFetch_default()({
        method: 'PUT',
        url,
        data: {
          status: 'active'
        }
      });
    } else {
      const response = await external_wp_apiFetch_default()({
        method: 'POST',
        path: 'wp/v2/plugins',
        data: {
          slug: id,
          status: 'active'
        }
      }); // Add the `self` link for newly-installed blocks.

      links = response._links;
    }

    dispatch.addInstalledBlockType({ ...block,
      links: { ...block.links,
        ...links
      }
    }); // Ensures that the block metadata is propagated to the editor when registered on the server.

    const metadataFields = ['api_version', 'title', 'category', 'parent', 'icon', 'description', 'keywords', 'attributes', 'provides_context', 'uses_context', 'supports', 'styles', 'example', 'variations'];
    await external_wp_apiFetch_default()({
      path: (0,external_wp_url_namespaceObject.addQueryArgs)(`/wp/v2/block-types/${name}`, {
        _fields: metadataFields
      })
    }) // Ignore when the block is not registered on the server.
    .catch(() => {}).then(response => {
      if (!response) {
        return;
      }

      (0,external_wp_blocks_namespaceObject.unstable__bootstrapServerSideBlockDefinitions)({
        [name]: (0,external_lodash_namespaceObject.pick)(response, metadataFields)
      });
    });
    await loadAssets();
    const registeredBlocks = registry.select(external_wp_blocks_namespaceObject.store).getBlockTypes();

    if (!registeredBlocks.some(i => i.name === name)) {
      throw new Error((0,external_wp_i18n_namespaceObject.__)('Error registering block. Try reloading the page.'));
    }

    registry.dispatch(external_wp_notices_namespaceObject.store).createInfoNotice((0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s is the block title.
    (0,external_wp_i18n_namespaceObject.__)('Block %s installed and added.'), block.title), {
      speak: true,
      type: 'snackbar'
    });
    success = true;
  } catch (error) {
    let message = error.message || (0,external_wp_i18n_namespaceObject.__)('An error occurred.'); // Errors we throw are fatal.


    let isFatal = error instanceof Error; // Specific API errors that are fatal.

    const fatalAPIErrors = {
      folder_exists: (0,external_wp_i18n_namespaceObject.__)('This block is already installed. Try reloading the page.'),
      unable_to_connect_to_filesystem: (0,external_wp_i18n_namespaceObject.__)('Error installing block. You can reload the page and try again.')
    };

    if (fatalAPIErrors[error.code]) {
      isFatal = true;
      message = fatalAPIErrors[error.code];
    }

    dispatch.setErrorNotice(id, message, isFatal);
    registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(message, {
      speak: true,
      isDismissible: true
    });
  }

  dispatch.setIsInstalling(id, false);
  return success;
};
/**
 * Action triggered to uninstall a block plugin.
 *
 * @param {Object} block The blockType object.
 */

const uninstallBlockType = block => async _ref2 => {
  let {
    registry,
    dispatch
  } = _ref2;

  try {
    const url = getPluginUrl(block);
    await external_wp_apiFetch_default()({
      method: 'PUT',
      url,
      data: {
        status: 'inactive'
      }
    });
    await external_wp_apiFetch_default()({
      method: 'DELETE',
      url
    });
    dispatch.removeInstalledBlockType(block);
  } catch (error) {
    registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(error.message || (0,external_wp_i18n_namespaceObject.__)('An error occurred.'));
  }
};
/**
 * Returns an action object used to add a block type to the "newly installed"
 * tracking list.
 *
 * @param {Object} item The block item with the block id and name.
 *
 * @return {Object} Action object.
 */

function addInstalledBlockType(item) {
  return {
    type: 'ADD_INSTALLED_BLOCK_TYPE',
    item
  };
}
/**
 * Returns an action object used to remove a block type from the "newly installed"
 * tracking list.
 *
 * @param {string} item The block item with the block id and name.
 *
 * @return {Object} Action object.
 */

function removeInstalledBlockType(item) {
  return {
    type: 'REMOVE_INSTALLED_BLOCK_TYPE',
    item
  };
}
/**
 * Returns an action object used to indicate install in progress.
 *
 * @param {string}  blockId
 * @param {boolean} isInstalling
 *
 * @return {Object} Action object.
 */

function setIsInstalling(blockId, isInstalling) {
  return {
    type: 'SET_INSTALLING_BLOCK',
    blockId,
    isInstalling
  };
}
/**
 * Sets an error notice to be displayed to the user for a given block.
 *
 * @param {string}  blockId The ID of the block plugin. eg: my-block
 * @param {string}  message The message shown in the notice.
 * @param {boolean} isFatal Whether the user can recover from the error.
 *
 * @return {Object} Action object.
 */

function setErrorNotice(blockId, message) {
  let isFatal = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  return {
    type: 'SET_ERROR_NOTICE',
    blockId,
    message,
    isFatal
  };
}
/**
 * Sets the error notice to empty for specific block.
 *
 * @param {string} blockId The ID of the block plugin. eg: my-block
 *
 * @return {Object} Action object.
 */

function clearErrorNotice(blockId) {
  return {
    type: 'CLEAR_ERROR_NOTICE',
    blockId
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/resolvers.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const resolvers_getDownloadableBlocks = filterValue => async _ref => {
  let {
    dispatch
  } = _ref;

  if (!filterValue) {
    return;
  }

  try {
    dispatch(fetchDownloadableBlocks(filterValue));
    const results = await external_wp_apiFetch_default()({
      path: `wp/v2/block-directory/search?term=${filterValue}`
    });
    const blocks = results.map(result => (0,external_lodash_namespaceObject.mapKeys)(result, (value, key) => (0,external_lodash_namespaceObject.camelCase)(key)));
    dispatch(receiveDownloadableBlocks(blocks, filterValue));
  } catch {}
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Module Constants
 */

const STORE_NAME = 'core/block-directory';
/**
 * Block editor data store configuration.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#registerStore
 *
 * @type {Object}
 */

const storeConfig = {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject,
  resolvers: resolvers_namespaceObject
};
/**
 * Store definition for the block directory namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, storeConfig);
(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/auto-block-uninstaller/index.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function AutoBlockUninstaller() {
  const {
    uninstallBlockType
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const shouldRemoveBlockTypes = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isAutosavingPost,
      isSavingPost
    } = select(external_wp_editor_namespaceObject.store);
    return isSavingPost() && !isAutosavingPost();
  }, []);
  const unusedBlockTypes = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getUnusedBlockTypes(), []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (shouldRemoveBlockTypes && unusedBlockTypes.length) {
      unusedBlockTypes.forEach(blockType => {
        uninstallBlockType(blockType);
        (0,external_wp_blocks_namespaceObject.unregisterBlockType)(blockType.name);
      });
    }
  }, [shouldRemoveBlockTypes]);
  return null;
}

;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","compose"]
var external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","coreData"]
var external_wp_coreData_namespaceObject = window["wp"]["coreData"];
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
;// CONCATENATED MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
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

;// CONCATENATED MODULE: external ["wp","primitives"]
var external_wp_primitives_namespaceObject = window["wp"]["primitives"];
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/star-half.js


/**
 * WordPress dependencies
 */

const starHalf = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M9.518 8.783a.25.25 0 00.188-.137l2.069-4.192a.25.25 0 01.448 0l2.07 4.192a.25.25 0 00.187.137l4.626.672a.25.25 0 01.139.427l-3.347 3.262a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.363.264l-4.137-2.176a.25.25 0 00-.233 0l-4.138 2.175a.25.25 0 01-.362-.263l.79-4.607a.25.25 0 00-.072-.222L4.753 9.882a.25.25 0 01.14-.427l4.625-.672zM12 14.533c.28 0 .559.067.814.2l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39v7.143z"
}));
/* harmony default export */ var star_half = (starHalf);

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

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/block-ratings/stars.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function Stars(_ref) {
  let {
    rating
  } = _ref;
  const stars = Math.round(rating / 0.5) * 0.5;
  const fullStarCount = Math.floor(rating);
  const halfStarCount = Math.ceil(rating - fullStarCount);
  const emptyStarCount = 5 - (fullStarCount + halfStarCount);
  return (0,external_wp_element_namespaceObject.createElement)("span", {
    "aria-label": (0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %s: number of stars. */
    (0,external_wp_i18n_namespaceObject.__)('%s out of 5 stars'), stars)
  }, (0,external_lodash_namespaceObject.times)(fullStarCount, i => (0,external_wp_element_namespaceObject.createElement)(icon, {
    key: `full_stars_${i}`,
    className: "block-directory-block-ratings__star-full",
    icon: star_filled,
    size: 16
  })), (0,external_lodash_namespaceObject.times)(halfStarCount, i => (0,external_wp_element_namespaceObject.createElement)(icon, {
    key: `half_stars_${i}`,
    className: "block-directory-block-ratings__star-half-full",
    icon: star_half,
    size: 16
  })), (0,external_lodash_namespaceObject.times)(emptyStarCount, i => (0,external_wp_element_namespaceObject.createElement)(icon, {
    key: `empty_stars_${i}`,
    className: "block-directory-block-ratings__star-empty",
    icon: star_empty,
    size: 16
  })));
}

/* harmony default export */ var stars = (Stars);

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/block-ratings/index.js


/**
 * Internal dependencies
 */

const BlockRatings = _ref => {
  let {
    rating
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "block-directory-block-ratings"
  }, (0,external_wp_element_namespaceObject.createElement)(stars, {
    rating: rating
  }));
};
/* harmony default export */ var block_ratings = (BlockRatings);

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-icon/index.js


/**
 * WordPress dependencies
 */


function DownloadableBlockIcon(_ref) {
  let {
    icon
  } = _ref;
  const className = 'block-directory-downloadable-block-icon';
  return icon.match(/\.(jpeg|jpg|gif|png|svg)(?:\?.*)?$/) !== null ? (0,external_wp_element_namespaceObject.createElement)("img", {
    className: className,
    src: icon,
    alt: ""
  }) : (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockIcon, {
    className: className,
    icon: icon,
    showColors: true
  });
}

/* harmony default export */ var downloadable_block_icon = (DownloadableBlockIcon);

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-notice/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const DownloadableBlockNotice = _ref => {
  let {
    block
  } = _ref;
  const errorNotice = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getErrorNoticeForBlock(block.id), [block]);

  if (!errorNotice) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "block-directory-downloadable-block-notice"
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "block-directory-downloadable-block-notice__content"
  }, errorNotice.message, errorNotice.isFatal ? ' ' + (0,external_wp_i18n_namespaceObject.__)('Try reloading the page.') : null));
};
/* harmony default export */ var downloadable_block_notice = (DownloadableBlockNotice);

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-list-item/index.js



/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




 // Return the appropriate block item label, given the block data and status.

function getDownloadableBlockLabel(_ref, _ref2) {
  let {
    title,
    rating,
    ratingCount
  } = _ref;
  let {
    hasNotice,
    isInstalled,
    isInstalling
  } = _ref2;
  const stars = Math.round(rating / 0.5) * 0.5;

  if (!isInstalled && hasNotice) {
    /* translators: %1$s: block title */
    return (0,external_wp_i18n_namespaceObject.sprintf)('Retry installing %s.', (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title));
  }

  if (isInstalled) {
    /* translators: %1$s: block title */
    return (0,external_wp_i18n_namespaceObject.sprintf)('Add %s.', (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title));
  }

  if (isInstalling) {
    /* translators: %1$s: block title */
    return (0,external_wp_i18n_namespaceObject.sprintf)('Installing %s.', (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title));
  } // No ratings yet, just use the title.


  if (ratingCount < 1) {
    /* translators: %1$s: block title */
    return (0,external_wp_i18n_namespaceObject.sprintf)('Install %s.', (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title));
  }

  return (0,external_wp_i18n_namespaceObject.sprintf)(
  /* translators: %1$s: block title, %2$s: average rating, %3$s: total ratings count. */
  (0,external_wp_i18n_namespaceObject._n)('Install %1$s. %2$s stars with %3$s review.', 'Install %1$s. %2$s stars with %3$s reviews.', ratingCount), (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title), stars, ratingCount);
}

function DownloadableBlockListItem(_ref3) {
  let {
    composite,
    item,
    onClick
  } = _ref3;
  const {
    author,
    description,
    icon,
    rating,
    title
  } = item; // getBlockType returns a block object if this block exists, or null if not.

  const isInstalled = !!(0,external_wp_blocks_namespaceObject.getBlockType)(item.name);
  const {
    hasNotice,
    isInstalling,
    isInstallable
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getErrorNoticeForBlock,
      isInstalling: isBlockInstalling
    } = select(store);
    const notice = getErrorNoticeForBlock(item.id);
    const hasFatal = notice && notice.isFatal;
    return {
      hasNotice: !!notice,
      isInstalling: isBlockInstalling(item.id),
      isInstallable: !hasFatal
    };
  }, [item]);
  let statusText = '';

  if (isInstalled) {
    statusText = (0,external_wp_i18n_namespaceObject.__)('Installed!');
  } else if (isInstalling) {
    statusText = (0,external_wp_i18n_namespaceObject.__)('Installing…');
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableCompositeItem, _extends({
    __experimentalIsFocusable: true,
    role: "option",
    as: external_wp_components_namespaceObject.Button
  }, composite, {
    className: "block-directory-downloadable-block-list-item",
    onClick: event => {
      event.preventDefault();
      onClick();
    },
    isBusy: isInstalling,
    disabled: isInstalling || !isInstallable,
    label: getDownloadableBlockLabel(item, {
      hasNotice,
      isInstalled,
      isInstalling
    }),
    showTooltip: true,
    tooltipPosition: "top center"
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "block-directory-downloadable-block-list-item__icon"
  }, (0,external_wp_element_namespaceObject.createElement)(downloadable_block_icon, {
    icon: icon,
    title: title
  }), isInstalling ? (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "block-directory-downloadable-block-list-item__spinner"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Spinner, null)) : (0,external_wp_element_namespaceObject.createElement)(block_ratings, {
    rating: rating
  })), (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "block-directory-downloadable-block-list-item__details"
  }, (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "block-directory-downloadable-block-list-item__title"
  }, (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.sprintf)(
  /* translators: %1$s: block title, %2$s: author name. */
  (0,external_wp_i18n_namespaceObject.__)('%1$s <span>by %2$s</span>'), (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title), author), {
    span: (0,external_wp_element_namespaceObject.createElement)("span", {
      className: "block-directory-downloadable-block-list-item__author"
    })
  })), hasNotice ? (0,external_wp_element_namespaceObject.createElement)(downloadable_block_notice, {
    block: item
  }) : (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "block-directory-downloadable-block-list-item__desc"
  }, !!statusText ? statusText : (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(description)), isInstallable && !(isInstalled || isInstalling) && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, null, (0,external_wp_i18n_namespaceObject.__)('Install block')))));
}

/* harmony default export */ var downloadable_block_list_item = (DownloadableBlockListItem);

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-list/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




function DownloadableBlocksList(_ref) {
  let {
    items,
    onHover = external_lodash_namespaceObject.noop,
    onSelect
  } = _ref;
  const composite = (0,external_wp_components_namespaceObject.__unstableUseCompositeState)();
  const {
    installBlockType
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);

  if (!items.length) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableComposite, _extends({}, composite, {
    role: "listbox",
    className: "block-directory-downloadable-blocks-list",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Blocks available for install')
  }), items.map(item => {
    return (0,external_wp_element_namespaceObject.createElement)(downloadable_block_list_item, {
      key: item.id,
      composite: composite,
      onClick: () => {
        // Check if the block is registered (`getBlockType`
        // will return an object). If so, insert the block.
        // This prevents installing existing plugins.
        if ((0,external_wp_blocks_namespaceObject.getBlockType)(item.name)) {
          onSelect(item);
        } else {
          installBlockType(item).then(success => {
            if (success) {
              onSelect(item);
            }
          });
        }

        onHover(null);
      },
      onHover: onHover,
      item: item
    });
  }));
}

/* harmony default export */ var downloadable_blocks_list = (DownloadableBlocksList);

;// CONCATENATED MODULE: external ["wp","a11y"]
var external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-panel/inserter-panel.js


/**
 * WordPress dependencies
 */




function DownloadableBlocksInserterPanel(_ref) {
  let {
    children,
    downloadableItems,
    hasLocalBlocks
  } = _ref;
  const count = downloadableItems.length;
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %d: number of available blocks. */
    (0,external_wp_i18n_namespaceObject._n)('%d additional block is available to install.', '%d additional blocks are available to install.', count), count));
  }, [count]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, !hasLocalBlocks && (0,external_wp_element_namespaceObject.createElement)("p", {
    className: "block-directory-downloadable-blocks-panel__no-local"
  }, (0,external_wp_i18n_namespaceObject.__)('No results available from your installed blocks.')), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "block-editor-inserter__quick-inserter-separator"
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "block-directory-downloadable-blocks-panel"
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "block-directory-downloadable-blocks-panel__header"
  }, (0,external_wp_element_namespaceObject.createElement)("h2", {
    className: "block-directory-downloadable-blocks-panel__title"
  }, (0,external_wp_i18n_namespaceObject.__)('Available to install')), (0,external_wp_element_namespaceObject.createElement)("p", {
    className: "block-directory-downloadable-blocks-panel__description"
  }, (0,external_wp_i18n_namespaceObject.__)('Select a block to install and add it to your post.'))), children));
}

/* harmony default export */ var inserter_panel = (DownloadableBlocksInserterPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/block-default.js


/**
 * WordPress dependencies
 */

const blockDefault = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M19 8h-1V6h-5v2h-2V6H6v2H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm.5 10c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-8c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v8z"
}));
/* harmony default export */ var block_default = (blockDefault);

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-panel/no-results.js


/**
 * WordPress dependencies
 */



function DownloadableBlocksNoResults() {
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "block-editor-inserter__no-results"
  }, (0,external_wp_element_namespaceObject.createElement)(icon, {
    className: "block-editor-inserter__no-results-icon",
    icon: block_default
  }), (0,external_wp_element_namespaceObject.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('No results found.')));
}

/* harmony default export */ var no_results = (DownloadableBlocksNoResults);

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-panel/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */






function DownloadableBlocksPanel(_ref) {
  let {
    downloadableItems,
    onSelect,
    onHover,
    hasLocalBlocks,
    hasPermission,
    isLoading,
    isTyping
  } = _ref;

  if (typeof hasPermission === 'undefined' || isLoading || isTyping) {
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, hasPermission && !hasLocalBlocks && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("p", {
      className: "block-directory-downloadable-blocks-panel__no-local"
    }, (0,external_wp_i18n_namespaceObject.__)('No results available from your installed blocks.')), (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "block-editor-inserter__quick-inserter-separator"
    })), (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "block-directory-downloadable-blocks-panel has-blocks-loading"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Spinner, null)));
  }

  if (false === hasPermission) {
    if (!hasLocalBlocks) {
      return (0,external_wp_element_namespaceObject.createElement)(no_results, null);
    }

    return null;
  }

  return !!downloadableItems.length ? (0,external_wp_element_namespaceObject.createElement)(inserter_panel, {
    downloadableItems: downloadableItems,
    hasLocalBlocks: hasLocalBlocks
  }, (0,external_wp_element_namespaceObject.createElement)(downloadable_blocks_list, {
    items: downloadableItems,
    onSelect: onSelect,
    onHover: onHover
  })) : !hasLocalBlocks && (0,external_wp_element_namespaceObject.createElement)(no_results, null);
}

/* harmony default export */ var downloadable_blocks_panel = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)((select, _ref2) => {
  let {
    filterValue,
    rootClientId = null
  } = _ref2;
  const {
    getDownloadableBlocks,
    isRequestingDownloadableBlocks
  } = select(store);
  const {
    canInsertBlockType
  } = select(external_wp_blockEditor_namespaceObject.store);
  const hasPermission = select(external_wp_coreData_namespaceObject.store).canUser('read', 'block-directory/search');

  function getInstallableBlocks(term) {
    return getDownloadableBlocks(term).filter(block => canInsertBlockType(block, rootClientId, true));
  }

  const downloadableItems = hasPermission ? getInstallableBlocks(filterValue) : [];
  const isLoading = isRequestingDownloadableBlocks(filterValue);
  return {
    downloadableItems,
    hasPermission,
    isLoading
  };
})])(DownloadableBlocksPanel));

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/inserter-menu-downloadable-blocks-panel/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function InserterMenuDownloadableBlocksPanel() {
  const [debouncedFilterValue, setFilterValue] = (0,external_wp_element_namespaceObject.useState)('');
  const debouncedSetFilterValue = (0,external_lodash_namespaceObject.debounce)(setFilterValue, 400);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableInserterMenuExtension, null, _ref => {
    let {
      onSelect,
      onHover,
      filterValue,
      hasItems,
      rootClientId
    } = _ref;

    if (debouncedFilterValue !== filterValue) {
      debouncedSetFilterValue(filterValue);
    }

    if (!debouncedFilterValue) {
      return null;
    }

    return (0,external_wp_element_namespaceObject.createElement)(downloadable_blocks_panel, {
      onSelect: onSelect,
      onHover: onHover,
      rootClientId: rootClientId,
      filterValue: debouncedFilterValue,
      hasLocalBlocks: hasItems,
      isTyping: filterValue !== debouncedFilterValue
    });
  });
}

/* harmony default export */ var inserter_menu_downloadable_blocks_panel = (InserterMenuDownloadableBlocksPanel);

;// CONCATENATED MODULE: external ["wp","editPost"]
var external_wp_editPost_namespaceObject = window["wp"]["editPost"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/compact-list/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


function CompactList(_ref) {
  let {
    items
  } = _ref;

  if (!items.length) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)("ul", {
    className: "block-directory-compact-list"
  }, items.map(_ref2 => {
    let {
      icon,
      id,
      title,
      author
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)("li", {
      key: id,
      className: "block-directory-compact-list__item"
    }, (0,external_wp_element_namespaceObject.createElement)(downloadable_block_icon, {
      icon: icon,
      title: title
    }), (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "block-directory-compact-list__item-details"
    }, (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "block-directory-compact-list__item-title"
    }, title), (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "block-directory-compact-list__item-author"
    }, (0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %s: Name of the block author. */
    (0,external_wp_i18n_namespaceObject.__)('By %s'), author))));
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/installed-blocks-pre-publish-panel/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function InstalledBlocksPrePublishPanel() {
  const newBlockTypes = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getNewBlockTypes(), []);

  if (!newBlockTypes.length) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_editPost_namespaceObject.PluginPrePublishPanel, {
    icon: block_default,
    title: (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %d: number of blocks (number).
    (0,external_wp_i18n_namespaceObject._n)('Added: %d block', 'Added: %d blocks', newBlockTypes.length), newBlockTypes.length),
    initialOpen: true
  }, (0,external_wp_element_namespaceObject.createElement)("p", {
    className: "installed-blocks-pre-publish-panel__copy"
  }, (0,external_wp_i18n_namespaceObject._n)('The following block has been added to your site.', 'The following blocks have been added to your site.', newBlockTypes.length)), (0,external_wp_element_namespaceObject.createElement)(CompactList, {
    items: newBlockTypes
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/get-install-missing/install-button.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function InstallButton(_ref) {
  let {
    attributes,
    block,
    clientId
  } = _ref;
  const isInstallingBlock = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).isInstalling(block.id), [block.id]);
  const {
    installBlockType
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const {
    replaceBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    onClick: () => installBlockType(block).then(success => {
      if (success) {
        const blockType = (0,external_wp_blocks_namespaceObject.getBlockType)(block.name);
        const [originalBlock] = (0,external_wp_blocks_namespaceObject.parse)(attributes.originalContent);

        if (originalBlock && blockType) {
          replaceBlock(clientId, (0,external_wp_blocks_namespaceObject.createBlock)(blockType.name, originalBlock.attributes, originalBlock.innerBlocks));
        }
      }
    }),
    disabled: isInstallingBlock,
    isBusy: isInstallingBlock,
    variant: "primary"
  }, (0,external_wp_i18n_namespaceObject.sprintf)(
  /* translators: %s: block name */
  (0,external_wp_i18n_namespaceObject.__)('Install %s'), block.title));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/get-install-missing/index.js



/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */




const getInstallMissing = OriginalComponent => props => {
  const {
    originalName
  } = props.attributes; // Disable reason: This is a valid component, but it's mistaken for a callback.
  // eslint-disable-next-line react-hooks/rules-of-hooks

  const {
    block,
    hasPermission
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getDownloadableBlocks
    } = select(store);
    const blocks = getDownloadableBlocks('block:' + originalName).filter(_ref => {
      let {
        name
      } = _ref;
      return originalName === name;
    });
    return {
      hasPermission: select(external_wp_coreData_namespaceObject.store).canUser('read', 'block-directory/search'),
      block: blocks.length && blocks[0]
    };
  }, [originalName]); // The user can't install blocks, or the block isn't available for download.

  if (!hasPermission || !block) {
    return (0,external_wp_element_namespaceObject.createElement)(OriginalComponent, props);
  }

  return (0,external_wp_element_namespaceObject.createElement)(ModifiedWarning, _extends({}, props, {
    originalBlock: block
  }));
};

const ModifiedWarning = _ref2 => {
  let {
    originalBlock,
    ...props
  } = _ref2;
  const {
    originalName,
    originalUndelimitedContent
  } = props.attributes;
  const {
    replaceBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);

  const convertToHTML = () => {
    replaceBlock(props.clientId, (0,external_wp_blocks_namespaceObject.createBlock)('core/html', {
      content: originalUndelimitedContent
    }));
  };

  const hasContent = !!originalUndelimitedContent;
  const hasHTMLBlock = (0,external_wp_blocks_namespaceObject.getBlockType)('core/html');
  let messageHTML = (0,external_wp_i18n_namespaceObject.sprintf)(
  /* translators: %s: block name */
  (0,external_wp_i18n_namespaceObject.__)('Your site doesn’t include support for the %s block. You can try installing the block or remove it entirely.'), originalBlock.title || originalName);
  const actions = [(0,external_wp_element_namespaceObject.createElement)(InstallButton, {
    key: "install",
    block: originalBlock,
    attributes: props.attributes,
    clientId: props.clientId
  })];

  if (hasContent && hasHTMLBlock) {
    messageHTML = (0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %s: block name */
    (0,external_wp_i18n_namespaceObject.__)('Your site doesn’t include support for the %s block. You can try installing the block, convert it to a Custom HTML block, or remove it entirely.'), originalBlock.title || originalName);
    actions.push((0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      key: "convert",
      onClick: convertToHTML,
      variant: "link"
    }, (0,external_wp_i18n_namespaceObject.__)('Keep as HTML')));
  }

  return (0,external_wp_element_namespaceObject.createElement)("div", (0,external_wp_blockEditor_namespaceObject.useBlockProps)(), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.Warning, {
    actions: actions
  }, messageHTML), (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.RawHTML, null, originalUndelimitedContent));
};

/* harmony default export */ var get_install_missing = (getInstallMissing);

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





(0,external_wp_plugins_namespaceObject.registerPlugin)('block-directory', {
  render() {
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(AutoBlockUninstaller, null), (0,external_wp_element_namespaceObject.createElement)(inserter_menu_downloadable_blocks_panel, null), (0,external_wp_element_namespaceObject.createElement)(InstalledBlocksPrePublishPanel, null));
  }

});
(0,external_wp_hooks_namespaceObject.addFilter)('blocks.registerBlockType', 'block-directory/fallback', (settings, name) => {
  if (name !== 'core/missing') {
    return settings;
  }

  settings.edit = get_install_missing(settings.edit);
  return settings;
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/index.js
/**
 * Internal dependencies
 */



(window.wp = window.wp || {}).blockDirectory = __webpack_exports__;
/******/ })()
;