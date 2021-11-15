this["wp"] = this["wp"] || {}; this["wp"]["blockDirectory"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "7f3f");
/******/ })
/************************************************************************/
/******/ ({

/***/ "//Lo":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const blockDefault = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 8h-1V6h-5v2h-2V6H6v2H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm.5 10c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-8c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v8z"
}));
/* harmony default export */ __webpack_exports__["a"] = (blockDefault);


/***/ }),

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "7f3f":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ store; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-directory/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "isRequestingDownloadableBlocks", function() { return selectors_isRequestingDownloadableBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getDownloadableBlocks", function() { return selectors_getDownloadableBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getInstalledBlockTypes", function() { return getInstalledBlockTypes; });
__webpack_require__.d(selectors_namespaceObject, "getNewBlockTypes", function() { return getNewBlockTypes; });
__webpack_require__.d(selectors_namespaceObject, "getUnusedBlockTypes", function() { return getUnusedBlockTypes; });
__webpack_require__.d(selectors_namespaceObject, "isInstalling", function() { return selectors_isInstalling; });
__webpack_require__.d(selectors_namespaceObject, "getErrorNotices", function() { return getErrorNotices; });
__webpack_require__.d(selectors_namespaceObject, "getErrorNoticeForBlock", function() { return selectors_getErrorNoticeForBlock; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-directory/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "fetchDownloadableBlocks", function() { return fetchDownloadableBlocks; });
__webpack_require__.d(actions_namespaceObject, "receiveDownloadableBlocks", function() { return receiveDownloadableBlocks; });
__webpack_require__.d(actions_namespaceObject, "installBlockType", function() { return actions_installBlockType; });
__webpack_require__.d(actions_namespaceObject, "uninstallBlockType", function() { return actions_uninstallBlockType; });
__webpack_require__.d(actions_namespaceObject, "addInstalledBlockType", function() { return addInstalledBlockType; });
__webpack_require__.d(actions_namespaceObject, "removeInstalledBlockType", function() { return removeInstalledBlockType; });
__webpack_require__.d(actions_namespaceObject, "setIsInstalling", function() { return setIsInstalling; });
__webpack_require__.d(actions_namespaceObject, "setErrorNotice", function() { return setErrorNotice; });
__webpack_require__.d(actions_namespaceObject, "clearErrorNotice", function() { return clearErrorNotice; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-directory/build-module/store/resolvers.js
var resolvers_namespaceObject = {};
__webpack_require__.r(resolvers_namespaceObject);
__webpack_require__.d(resolvers_namespaceObject, "getDownloadableBlocks", function() { return resolvers_getDownloadableBlocks; });

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","plugins"]
var external_wp_plugins_ = __webpack_require__("TvNi");

// EXTERNAL MODULE: external ["wp","hooks"]
var external_wp_hooks_ = __webpack_require__("g56x");

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","editor"]
var external_wp_editor_ = __webpack_require__("jSdM");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/reducer.js
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
      return Object(external_lodash_["omit"])(state, action.blockId);
  }

  return state;
};
/* harmony default export */ var reducer = (Object(external_wp_data_["combineReducers"])({
  downloadableBlocks,
  blockManagement,
  errorNotices
}));

// EXTERNAL MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_ = __webpack_require__("axFQ");

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/utils/has-block-type.js
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

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/selectors.js
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

function selectors_isRequestingDownloadableBlocks(state, filterValue) {
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

function selectors_getDownloadableBlocks(state, filterValue) {
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

const getNewBlockTypes = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  const usedBlockTree = select(external_wp_blockEditor_["store"]).getBlocks();
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

const getUnusedBlockTypes = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  const usedBlockTree = select(external_wp_blockEditor_["store"]).getBlocks();
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

function selectors_isInstalling(state, blockId) {
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

function selectors_getErrorNoticeForBlock(state, blockId) {
  return state.errorNotices[blockId];
}

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__("ywyh");
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);

// EXTERNAL MODULE: external ["wp","notices"]
var external_wp_notices_ = __webpack_require__("onLe");

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/load-assets.js
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

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/utils/get-plugin-url.js
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

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/actions.js
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

const actions_installBlockType = block => async _ref => {
  let {
    registry,
    dispatch
  } = _ref;
  const {
    id
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
    });
    await loadAssets();
    const registeredBlocks = registry.select(external_wp_blocks_["store"]).getBlockTypes();

    if (!registeredBlocks.some(i => i.name === block.name)) {
      throw new Error(Object(external_wp_i18n_["__"])('Error registering block. Try reloading the page.'));
    }

    registry.dispatch(external_wp_notices_["store"]).createInfoNotice(Object(external_wp_i18n_["sprintf"])( // translators: %s is the block title.
    Object(external_wp_i18n_["__"])('Block %s installed and added.'), block.title), {
      speak: true,
      type: 'snackbar'
    });
    success = true;
  } catch (error) {
    let message = error.message || Object(external_wp_i18n_["__"])('An error occurred.'); // Errors we throw are fatal


    let isFatal = error instanceof Error; // Specific API errors that are fatal

    const fatalAPIErrors = {
      folder_exists: Object(external_wp_i18n_["__"])('This block is already installed. Try reloading the page.'),
      unable_to_connect_to_filesystem: Object(external_wp_i18n_["__"])('Error installing block. You can reload the page and try again.')
    };

    if (fatalAPIErrors[error.code]) {
      isFatal = true;
      message = fatalAPIErrors[error.code];
    }

    dispatch.setErrorNotice(id, message, isFatal);
    registry.dispatch(external_wp_notices_["store"]).createErrorNotice(message, {
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

const actions_uninstallBlockType = block => async _ref2 => {
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
    registry.dispatch(external_wp_notices_["store"]).createErrorNotice(error.message || Object(external_wp_i18n_["__"])('An error occurred.'));
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

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/resolvers.js
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
    const blocks = results.map(result => Object(external_lodash_["mapKeys"])(result, (value, key) => Object(external_lodash_["camelCase"])(key)));
    dispatch(receiveDownloadableBlocks(blocks, filterValue));
  } catch {}
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/index.js
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
  resolvers: resolvers_namespaceObject,
  __experimentalUseThunks: true
};
/**
 * Store definition for the block directory namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, storeConfig);
Object(external_wp_data_["register"])(store);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/auto-block-uninstaller/index.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function AutoBlockUninstaller() {
  const {
    uninstallBlockType
  } = Object(external_wp_data_["useDispatch"])(store);
  const shouldRemoveBlockTypes = Object(external_wp_data_["useSelect"])(select => {
    const {
      isAutosavingPost,
      isSavingPost
    } = select(external_wp_editor_["store"]);
    return isSavingPost() && !isAutosavingPost();
  }, []);
  const unusedBlockTypes = Object(external_wp_data_["useSelect"])(select => select(store).getUnusedBlockTypes(), []);
  Object(external_wp_element_["useEffect"])(() => {
    if (shouldRemoveBlockTypes && unusedBlockTypes.length) {
      unusedBlockTypes.forEach(blockType => {
        uninstallBlockType(blockType);
        Object(external_wp_blocks_["unregisterBlockType"])(blockType.name);
      });
    }
  }, [shouldRemoveBlockTypes]);
  return null;
}

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// EXTERNAL MODULE: external ["wp","coreData"]
var external_wp_coreData_ = __webpack_require__("jZUy");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_ = __webpack_require__("rmEH");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
var build_module_icon = __webpack_require__("iClF");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/star-filled.js
var star_filled = __webpack_require__("URob");

// EXTERNAL MODULE: external ["wp","primitives"]
var external_wp_primitives_ = __webpack_require__("Tqx9");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/star-half.js


/**
 * WordPress dependencies
 */

const starHalf = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M9.518 8.783a.25.25 0 00.188-.137l2.069-4.192a.25.25 0 01.448 0l2.07 4.192a.25.25 0 00.187.137l4.626.672a.25.25 0 01.139.427l-3.347 3.262a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.363.264l-4.137-2.176a.25.25 0 00-.233 0l-4.138 2.175a.25.25 0 01-.362-.263l.79-4.607a.25.25 0 00-.072-.222L4.753 9.882a.25.25 0 01.14-.427l4.625-.672zM12 14.533c.28 0 .559.067.814.2l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39v7.143z"
}));
/* harmony default export */ var star_half = (starHalf);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/star-empty.js
var star_empty = __webpack_require__("Xxwi");

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/block-ratings/stars.js


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
  return Object(external_wp_element_["createElement"])("span", {
    "aria-label": Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: number of stars. */
    Object(external_wp_i18n_["__"])('%s out of 5 stars'), stars)
  }, Object(external_lodash_["times"])(fullStarCount, i => Object(external_wp_element_["createElement"])(build_module_icon["a" /* default */], {
    key: `full_stars_${i}`,
    className: "block-directory-block-ratings__star-full",
    icon: star_filled["a" /* default */],
    size: 16
  })), Object(external_lodash_["times"])(halfStarCount, i => Object(external_wp_element_["createElement"])(build_module_icon["a" /* default */], {
    key: `half_stars_${i}`,
    className: "block-directory-block-ratings__star-half-full",
    icon: star_half,
    size: 16
  })), Object(external_lodash_["times"])(emptyStarCount, i => Object(external_wp_element_["createElement"])(build_module_icon["a" /* default */], {
    key: `empty_stars_${i}`,
    className: "block-directory-block-ratings__star-empty",
    icon: star_empty["a" /* default */],
    size: 16
  })));
}

/* harmony default export */ var block_ratings_stars = (Stars);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/block-ratings/index.js


/**
 * Internal dependencies
 */

const BlockRatings = _ref => {
  let {
    rating
  } = _ref;
  return Object(external_wp_element_["createElement"])("span", {
    className: "block-directory-block-ratings"
  }, Object(external_wp_element_["createElement"])(block_ratings_stars, {
    rating: rating
  }));
};
/* harmony default export */ var block_ratings = (BlockRatings);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-icon/index.js


/**
 * WordPress dependencies
 */


function DownloadableBlockIcon(_ref) {
  let {
    icon
  } = _ref;
  const className = 'block-directory-downloadable-block-icon';
  return icon.match(/\.(jpeg|jpg|gif|png|svg)(?:\?.*)?$/) !== null ? Object(external_wp_element_["createElement"])("img", {
    className: className,
    src: icon,
    alt: ""
  }) : Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockIcon"], {
    className: className,
    icon: icon,
    showColors: true
  });
}

/* harmony default export */ var downloadable_block_icon = (DownloadableBlockIcon);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-notice/index.js


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
  const errorNotice = Object(external_wp_data_["useSelect"])(select => select(store).getErrorNoticeForBlock(block.id), [block]);

  if (!errorNotice) {
    return null;
  }

  return Object(external_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-notice"
  }, Object(external_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-notice__content"
  }, errorNotice.message, errorNotice.isFatal ? ' ' + Object(external_wp_i18n_["__"])('Try reloading the page.') : null));
};
/* harmony default export */ var downloadable_block_notice = (DownloadableBlockNotice);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-list-item/index.js



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
    return Object(external_wp_i18n_["sprintf"])('Retry installing %s.', Object(external_wp_htmlEntities_["decodeEntities"])(title));
  }

  if (isInstalled) {
    /* translators: %1$s: block title */
    return Object(external_wp_i18n_["sprintf"])('Add %s.', Object(external_wp_htmlEntities_["decodeEntities"])(title));
  }

  if (isInstalling) {
    /* translators: %1$s: block title */
    return Object(external_wp_i18n_["sprintf"])('Installing %s.', Object(external_wp_htmlEntities_["decodeEntities"])(title));
  } // No ratings yet, just use the title.


  if (ratingCount < 1) {
    /* translators: %1$s: block title */
    return Object(external_wp_i18n_["sprintf"])('Install %s.', Object(external_wp_htmlEntities_["decodeEntities"])(title));
  }

  return Object(external_wp_i18n_["sprintf"])(
  /* translators: %1$s: block title, %2$s: average rating, %3$s: total ratings count. */
  Object(external_wp_i18n_["_n"])('Install %1$s. %2$s stars with %3$s review.', 'Install %1$s. %2$s stars with %3$s reviews.', ratingCount), Object(external_wp_htmlEntities_["decodeEntities"])(title), stars, ratingCount);
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

  const isInstalled = !!Object(external_wp_blocks_["getBlockType"])(item.name);
  const {
    hasNotice,
    isInstalling,
    isInstallable
  } = Object(external_wp_data_["useSelect"])(select => {
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
    statusText = Object(external_wp_i18n_["__"])('Installed!');
  } else if (isInstalling) {
    statusText = Object(external_wp_i18n_["__"])('Installing…');
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["__unstableCompositeItem"], Object(esm_extends["a" /* default */])({
    role: "option",
    as: external_wp_components_["Button"]
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
  }), Object(external_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-list-item__icon"
  }, Object(external_wp_element_["createElement"])(downloadable_block_icon, {
    icon: icon,
    title: title
  }), isInstalling ? Object(external_wp_element_["createElement"])("span", {
    className: "block-directory-downloadable-block-list-item__spinner"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null)) : Object(external_wp_element_["createElement"])(block_ratings, {
    rating: rating
  })), Object(external_wp_element_["createElement"])("span", {
    className: "block-directory-downloadable-block-list-item__details"
  }, Object(external_wp_element_["createElement"])("span", {
    className: "block-directory-downloadable-block-list-item__title"
  }, Object(external_wp_element_["createInterpolateElement"])(Object(external_wp_i18n_["sprintf"])(
  /* translators: %1$s: block title, %2$s: author name. */
  Object(external_wp_i18n_["__"])('%1$s <span>by %2$s</span>'), Object(external_wp_htmlEntities_["decodeEntities"])(title), author), {
    span: Object(external_wp_element_["createElement"])("span", {
      className: "block-directory-downloadable-block-list-item__author"
    })
  })), hasNotice ? Object(external_wp_element_["createElement"])(downloadable_block_notice, {
    block: item
  }) : Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("span", {
    className: "block-directory-downloadable-block-list-item__desc"
  }, !!statusText ? statusText : Object(external_wp_htmlEntities_["decodeEntities"])(description)), isInstallable && !(isInstalled || isInstalling) && Object(external_wp_element_["createElement"])(external_wp_components_["VisuallyHidden"], null, Object(external_wp_i18n_["__"])('Install block')))));
}

/* harmony default export */ var downloadable_block_list_item = (DownloadableBlockListItem);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-list/index.js



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
    onHover = external_lodash_["noop"],
    onSelect
  } = _ref;
  const composite = Object(external_wp_components_["__unstableUseCompositeState"])();
  const {
    installBlockType
  } = Object(external_wp_data_["useDispatch"])(store);

  if (!items.length) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["__unstableComposite"], Object(esm_extends["a" /* default */])({}, composite, {
    role: "listbox",
    className: "block-directory-downloadable-blocks-list",
    "aria-label": Object(external_wp_i18n_["__"])('Blocks available for install')
  }), items.map(item => {
    return Object(external_wp_element_["createElement"])(downloadable_block_list_item, {
      key: item.id,
      composite: composite,
      onClick: () => {
        // Check if the block is registered (`getBlockType`
        // will return an object). If so, insert the block.
        // This prevents installing existing plugins.
        if (Object(external_wp_blocks_["getBlockType"])(item.name)) {
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

// EXTERNAL MODULE: external ["wp","a11y"]
var external_wp_a11y_ = __webpack_require__("gdqT");

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-panel/inserter-panel.js


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
  Object(external_wp_element_["useEffect"])(() => {
    Object(external_wp_a11y_["speak"])(Object(external_wp_i18n_["sprintf"])(
    /* translators: %d: number of available blocks. */
    Object(external_wp_i18n_["_n"])('%d additional block is available to install.', '%d additional blocks are available to install.', count), count));
  }, [count]);
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, !hasLocalBlocks && Object(external_wp_element_["createElement"])("p", {
    className: "block-directory-downloadable-blocks-panel__no-local"
  }, Object(external_wp_i18n_["__"])('No results available from your installed blocks.')), Object(external_wp_element_["createElement"])("div", {
    className: "block-editor-inserter__quick-inserter-separator"
  }), Object(external_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-blocks-panel"
  }, Object(external_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-blocks-panel__header"
  }, Object(external_wp_element_["createElement"])("h2", {
    className: "block-directory-downloadable-blocks-panel__title"
  }, Object(external_wp_i18n_["__"])('Available to install')), Object(external_wp_element_["createElement"])("p", {
    className: "block-directory-downloadable-blocks-panel__description"
  }, Object(external_wp_i18n_["__"])('Select a block to install and add it to your post.'))), children));
}

/* harmony default export */ var inserter_panel = (DownloadableBlocksInserterPanel);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/block-default.js
var block_default = __webpack_require__("//Lo");

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-panel/no-results.js


/**
 * WordPress dependencies
 */



function DownloadableBlocksNoResults() {
  return Object(external_wp_element_["createElement"])("div", {
    className: "block-editor-inserter__no-results"
  }, Object(external_wp_element_["createElement"])(build_module_icon["a" /* default */], {
    className: "block-editor-inserter__no-results-icon",
    icon: block_default["a" /* default */]
  }), Object(external_wp_element_["createElement"])("p", null, Object(external_wp_i18n_["__"])('No results found.')));
}

/* harmony default export */ var no_results = (DownloadableBlocksNoResults);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-panel/index.js


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
    return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, hasPermission && !hasLocalBlocks && Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("p", {
      className: "block-directory-downloadable-blocks-panel__no-local"
    }, Object(external_wp_i18n_["__"])('No results available from your installed blocks.')), Object(external_wp_element_["createElement"])("div", {
      className: "block-editor-inserter__quick-inserter-separator"
    })), Object(external_wp_element_["createElement"])("div", {
      className: "block-directory-downloadable-blocks-panel has-blocks-loading"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null)));
  }

  if (false === hasPermission) {
    if (!hasLocalBlocks) {
      return Object(external_wp_element_["createElement"])(no_results, null);
    }

    return null;
  }

  return !!downloadableItems.length ? Object(external_wp_element_["createElement"])(inserter_panel, {
    downloadableItems: downloadableItems,
    hasLocalBlocks: hasLocalBlocks
  }, Object(external_wp_element_["createElement"])(downloadable_blocks_list, {
    items: downloadableItems,
    onSelect: onSelect,
    onHover: onHover
  })) : !hasLocalBlocks && Object(external_wp_element_["createElement"])(no_results, null);
}

/* harmony default export */ var downloadable_blocks_panel = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])((select, _ref2) => {
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
  } = select(external_wp_blockEditor_["store"]);
  const hasPermission = select(external_wp_coreData_["store"]).canUser('read', 'block-directory/search');

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

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/inserter-menu-downloadable-blocks-panel/index.js


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
  const [debouncedFilterValue, setFilterValue] = Object(external_wp_element_["useState"])('');
  const debouncedSetFilterValue = Object(external_lodash_["debounce"])(setFilterValue, 400);
  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__unstableInserterMenuExtension"], null, _ref => {
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

    return Object(external_wp_element_["createElement"])(downloadable_blocks_panel, {
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

// EXTERNAL MODULE: external ["wp","editPost"]
var external_wp_editPost_ = __webpack_require__("BLhE");

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/compact-list/index.js


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

  return Object(external_wp_element_["createElement"])("ul", {
    className: "block-directory-compact-list"
  }, items.map(_ref2 => {
    let {
      icon,
      id,
      title,
      author
    } = _ref2;
    return Object(external_wp_element_["createElement"])("li", {
      key: id,
      className: "block-directory-compact-list__item"
    }, Object(external_wp_element_["createElement"])(downloadable_block_icon, {
      icon: icon,
      title: title
    }), Object(external_wp_element_["createElement"])("div", {
      className: "block-directory-compact-list__item-details"
    }, Object(external_wp_element_["createElement"])("div", {
      className: "block-directory-compact-list__item-title"
    }, title), Object(external_wp_element_["createElement"])("div", {
      className: "block-directory-compact-list__item-author"
    }, Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: Name of the block author. */
    Object(external_wp_i18n_["__"])('By %s'), author))));
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/installed-blocks-pre-publish-panel/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function InstalledBlocksPrePublishPanel() {
  const newBlockTypes = Object(external_wp_data_["useSelect"])(select => select(store).getNewBlockTypes(), []);

  if (!newBlockTypes.length) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_editPost_["PluginPrePublishPanel"], {
    icon: block_default["a" /* default */],
    title: Object(external_wp_i18n_["sprintf"])( // translators: %d: number of blocks (number).
    Object(external_wp_i18n_["_n"])('Added: %d block', 'Added: %d blocks', newBlockTypes.length), newBlockTypes.length),
    initialOpen: true
  }, Object(external_wp_element_["createElement"])("p", {
    className: "installed-blocks-pre-publish-panel__copy"
  }, Object(external_wp_i18n_["_n"])('The following block has been added to your site.', 'The following blocks have been added to your site.', newBlockTypes.length)), Object(external_wp_element_["createElement"])(CompactList, {
    items: newBlockTypes
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/get-install-missing/install-button.js


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
  const isInstallingBlock = Object(external_wp_data_["useSelect"])(select => select(store).isInstalling(block.id), [block.id]);
  const {
    installBlockType
  } = Object(external_wp_data_["useDispatch"])(store);
  const {
    replaceBlock
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    onClick: () => installBlockType(block).then(success => {
      if (success) {
        const blockType = Object(external_wp_blocks_["getBlockType"])(block.name);
        const [originalBlock] = Object(external_wp_blocks_["parse"])(attributes.originalContent);

        if (originalBlock && blockType) {
          replaceBlock(clientId, Object(external_wp_blocks_["createBlock"])(blockType.name, originalBlock.attributes, originalBlock.innerBlocks));
        }
      }
    }),
    disabled: isInstallingBlock,
    isBusy: isInstallingBlock,
    variant: "primary"
  }, Object(external_wp_i18n_["sprintf"])(
  /* translators: %s: block name */
  Object(external_wp_i18n_["__"])('Install %s'), block.title));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/get-install-missing/index.js



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
  } = Object(external_wp_data_["useSelect"])(select => {
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
      hasPermission: select(external_wp_coreData_["store"]).canUser('read', 'block-directory/search'),
      block: blocks.length && blocks[0]
    };
  }, [originalName]); // The user can't install blocks, or the block isn't available for download.

  if (!hasPermission || !block) {
    return Object(external_wp_element_["createElement"])(OriginalComponent, props);
  }

  return Object(external_wp_element_["createElement"])(ModifiedWarning, Object(esm_extends["a" /* default */])({}, props, {
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
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);

  const convertToHTML = () => {
    replaceBlock(props.clientId, Object(external_wp_blocks_["createBlock"])('core/html', {
      content: originalUndelimitedContent
    }));
  };

  const hasContent = !!originalUndelimitedContent;
  const hasHTMLBlock = Object(external_wp_blocks_["getBlockType"])('core/html');
  let messageHTML = Object(external_wp_i18n_["sprintf"])(
  /* translators: %s: block name */
  Object(external_wp_i18n_["__"])('Your site doesn’t include support for the %s block. You can try installing the block or remove it entirely.'), originalBlock.title || originalName);
  const actions = [Object(external_wp_element_["createElement"])(InstallButton, {
    key: "install",
    block: originalBlock,
    attributes: props.attributes,
    clientId: props.clientId
  })];

  if (hasContent && hasHTMLBlock) {
    messageHTML = Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: block name */
    Object(external_wp_i18n_["__"])('Your site doesn’t include support for the %s block. You can try installing the block, convert it to a Custom HTML block, or remove it entirely.'), originalBlock.title || originalName);
    actions.push(Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      key: "convert",
      onClick: convertToHTML,
      variant: "link"
    }, Object(external_wp_i18n_["__"])('Keep as HTML')));
  }

  return Object(external_wp_element_["createElement"])("div", Object(external_wp_blockEditor_["useBlockProps"])(), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["Warning"], {
    actions: actions
  }, messageHTML), Object(external_wp_element_["createElement"])(external_wp_element_["RawHTML"], null, originalUndelimitedContent));
};

/* harmony default export */ var get_install_missing = (getInstallMissing);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





Object(external_wp_plugins_["registerPlugin"])('block-directory', {
  render() {
    return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(AutoBlockUninstaller, null), Object(external_wp_element_["createElement"])(inserter_menu_downloadable_blocks_panel, null), Object(external_wp_element_["createElement"])(InstalledBlocksPrePublishPanel, null));
  }

});
Object(external_wp_hooks_["addFilter"])('blocks.registerBlockType', 'block-directory/fallback', (settings, name) => {
  if (name !== 'core/missing') {
    return settings;
  }

  settings.edit = get_install_missing(settings.edit);
  return settings;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/index.js
/**
 * Internal dependencies
 */




/***/ }),

/***/ "BLhE":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["editPost"]; }());

/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "HSyU":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "Tqx9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["primitives"]; }());

/***/ }),

/***/ "TvNi":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["plugins"]; }());

/***/ }),

/***/ "URob":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const starFilled = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"
}));
/* harmony default export */ __webpack_exports__["a"] = (starFilled);


/***/ }),

/***/ "Xxwi":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const starEmpty = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  fillRule: "evenodd",
  d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (starEmpty);


/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "axFQ":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockEditor"]; }());

/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["hooks"]; }());

/***/ }),

/***/ "gdqT":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["a11y"]; }());

/***/ }),

/***/ "iClF":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
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
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["cloneElement"])(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ __webpack_exports__["a"] = (Icon);


/***/ }),

/***/ "jSdM":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["editor"]; }());

/***/ }),

/***/ "jZUy":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["coreData"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "onLe":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["notices"]; }());

/***/ }),

/***/ "rmEH":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["htmlEntities"]; }());

/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

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

(function() { module.exports = window["wp"]["apiFetch"]; }());

/***/ })

/******/ });