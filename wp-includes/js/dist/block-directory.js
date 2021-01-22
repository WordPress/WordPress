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
/******/ 	return __webpack_require__(__webpack_require__.s = 429);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ 1:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ 10:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ 18:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
var arrayLikeToArray = __webpack_require__(25);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return Object(arrayLikeToArray["a" /* default */])(arr);
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__(35);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__(27);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || Object(unsupportedIterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 20:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _slicedToArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__(38);

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
var unsupportedIterableToArray = __webpack_require__(27);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__(39);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(unsupportedIterableToArray["a" /* default */])(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 23:
/***/ (function(module, exports) {

(function() { module.exports = this["regeneratorRuntime"]; }());

/***/ }),

/***/ 25:
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

/***/ 27:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(25);

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(n);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ 35:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

/***/ }),

/***/ 38:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ 39:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ 42:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ 429:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-directory/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "isRequestingDownloadableBlocks", function() { return isRequestingDownloadableBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getDownloadableBlocks", function() { return selectors_getDownloadableBlocks; });
__webpack_require__.d(selectors_namespaceObject, "hasInstallBlocksPermission", function() { return selectors_hasInstallBlocksPermission; });
__webpack_require__.d(selectors_namespaceObject, "getInstalledBlockTypes", function() { return getInstalledBlockTypes; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-directory/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "fetchDownloadableBlocks", function() { return fetchDownloadableBlocks; });
__webpack_require__.d(actions_namespaceObject, "receiveDownloadableBlocks", function() { return receiveDownloadableBlocks; });
__webpack_require__.d(actions_namespaceObject, "setInstallBlocksPermission", function() { return setInstallBlocksPermission; });
__webpack_require__.d(actions_namespaceObject, "downloadBlock", function() { return actions_downloadBlock; });
__webpack_require__.d(actions_namespaceObject, "installBlock", function() { return actions_installBlock; });
__webpack_require__.d(actions_namespaceObject, "uninstallBlock", function() { return uninstallBlock; });
__webpack_require__.d(actions_namespaceObject, "addInstalledBlockType", function() { return addInstalledBlockType; });
__webpack_require__.d(actions_namespaceObject, "removeInstalledBlockType", function() { return removeInstalledBlockType; });

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(18);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(5);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/reducer.js



function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

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

var reducer_downloadableBlocks = function downloadableBlocks() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    results: {},
    filterValue: undefined,
    isRequestingDownloadableBlocks: true
  };
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'FETCH_DOWNLOADABLE_BLOCKS':
      return _objectSpread({}, state, {
        isRequestingDownloadableBlocks: true
      });

    case 'RECEIVE_DOWNLOADABLE_BLOCKS':
      return _objectSpread({}, state, {
        results: Object.assign({}, state.results, Object(defineProperty["a" /* default */])({}, action.filterValue, action.downloadableBlocks)),
        isRequestingDownloadableBlocks: false
      });
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

var reducer_blockManagement = function blockManagement() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    installedBlockTypes: []
  };
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ADD_INSTALLED_BLOCK_TYPE':
      return _objectSpread({}, state, {
        installedBlockTypes: [].concat(Object(toConsumableArray["a" /* default */])(state.installedBlockTypes), [action.item])
      });

    case 'REMOVE_INSTALLED_BLOCK_TYPE':
      return _objectSpread({}, state, {
        installedBlockTypes: state.installedBlockTypes.filter(function (blockType) {
          return blockType.name !== action.item.name;
        })
      });
  }

  return state;
};
/**
 * Reducer returns whether the user can install blocks.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer_hasPermission() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  if (action.type === 'SET_INSTALL_BLOCKS_PERMISSION') {
    return action.hasPermission;
  }

  return state;
}
/* harmony default export */ var reducer = (Object(external_this_wp_data_["combineReducers"])({
  downloadableBlocks: reducer_downloadableBlocks,
  blockManagement: reducer_blockManagement,
  hasPermission: reducer_hasPermission
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/selectors.js
/**
 * Returns true if application is requesting for downloadable blocks.
 *
 * @param {Object} state       Global application state.
 *
 * @return {Array} Downloadable blocks
 */
function isRequestingDownloadableBlocks(state) {
  return state.downloadableBlocks.isRequestingDownloadableBlocks;
}
/**
 * Returns the available uninstalled blocks
 *
 * @param {Object} state       Global application state.
 * @param {string} filterValue Search string.
 *
 * @return {Array} Downloadable blocks
 */

function selectors_getDownloadableBlocks(state, filterValue) {
  if (!state.downloadableBlocks.results[filterValue]) {
    return [];
  }

  return state.downloadableBlocks.results[filterValue];
}
/**
 * Returns true if user has permission to install blocks.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} User has permission to install blocks.
 */

function selectors_hasInstallBlocksPermission(state) {
  return state.hasPermission;
}
/**
 * Returns the block types that have been installed on the server.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} Block type items.
 */

function getInstalledBlockTypes(state) {
  return state.blockManagement.installedBlockTypes;
}

// EXTERNAL MODULE: external {"this":"regeneratorRuntime"}
var external_this_regeneratorRuntime_ = __webpack_require__(23);
var external_this_regeneratorRuntime_default = /*#__PURE__*/__webpack_require__.n(external_this_regeneratorRuntime_);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(10);

// EXTERNAL MODULE: external {"this":"lodash"}
var external_this_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(42);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/controls.js




function controls_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function controls_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { controls_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { controls_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var _marked =
/*#__PURE__*/
external_this_regeneratorRuntime_default.a.mark(loadAssets);

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Calls a selector using the current state.
 *
 * @param {string} storeName    Store name.
 * @param {string} selectorName Selector name.
 * @param {Array}  args         Selector arguments.
 *
 * @return {Object} Control descriptor.
 */

function controls_select(storeName, selectorName) {
  for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  return {
    type: 'SELECT',
    storeName: storeName,
    selectorName: selectorName,
    args: args
  };
}
/**
 * Calls a dispatcher using the current state.
 *
 * @param {string} storeName      Store name.
 * @param {string} dispatcherName Dispatcher name.
 * @param {Array}  args           Selector arguments.
 *
 * @return {Object} Control descriptor.
 */

function controls_dispatch(storeName, dispatcherName) {
  for (var _len2 = arguments.length, args = new Array(_len2 > 2 ? _len2 - 2 : 0), _key2 = 2; _key2 < _len2; _key2++) {
    args[_key2 - 2] = arguments[_key2];
  }

  return {
    type: 'DISPATCH',
    storeName: storeName,
    dispatcherName: dispatcherName,
    args: args
  };
}
/**
 * Trigger an API Fetch request.
 *
 * @param {Object} request API Fetch Request Object.
 *
 * @return {Object} Control descriptor.
 */

function apiFetch(request) {
  return {
    type: 'API_FETCH',
    request: request
  };
}
/**
 * Loads JavaScript
 *
 * @param {Array}    asset   The url for the JavaScript.
 * @param {Function} onLoad  Callback function on success.
 * @param {Function} onError Callback function on failure.
 */

var loadScript = function loadScript(asset, onLoad, onError) {
  if (!asset) {
    return;
  }

  var existing = document.querySelector("script[src=\"".concat(asset.src, "\"]"));

  if (existing) {
    existing.parentNode.removeChild(existing);
  }

  var script = document.createElement('script');
  script.src = typeof asset === 'string' ? asset : asset.src;
  script.onload = onLoad;
  script.onerror = onError;
  document.body.appendChild(script);
};
/**
 * Loads CSS file.
 *
 * @param {*} asset the url for the CSS file.
 */


var loadStyle = function loadStyle(asset) {
  if (!asset) {
    return;
  }

  var link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = typeof asset === 'string' ? asset : asset.src;
  document.body.appendChild(link);
};
/**
 * Load the asset files for a block
 *
 * @param {Array} assets A collection of URL for the assets.
 *
 * @return {Object} Control descriptor.
 */


function loadAssets(assets) {
  return external_this_regeneratorRuntime_default.a.wrap(function loadAssets$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          return _context.abrupt("return", {
            type: 'LOAD_ASSETS',
            assets: assets
          });

        case 1:
        case "end":
          return _context.stop();
      }
    }
  }, _marked);
}
var controls = {
  SELECT: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref) {
      var _registry$select;

      var storeName = _ref.storeName,
          selectorName = _ref.selectorName,
          args = _ref.args;
      return (_registry$select = registry.select(storeName))[selectorName].apply(_registry$select, Object(toConsumableArray["a" /* default */])(args));
    };
  }),
  DISPATCH: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref2) {
      var _registry$dispatch;

      var storeName = _ref2.storeName,
          dispatcherName = _ref2.dispatcherName,
          args = _ref2.args;
      return (_registry$dispatch = registry.dispatch(storeName))[dispatcherName].apply(_registry$dispatch, Object(toConsumableArray["a" /* default */])(args));
    };
  }),
  API_FETCH: function API_FETCH(_ref3) {
    var request = _ref3.request;
    return external_this_wp_apiFetch_default()(controls_objectSpread({}, request));
  },
  LOAD_ASSETS: function LOAD_ASSETS(_ref4) {
    var assets = _ref4.assets;
    return new Promise(function (resolve, reject) {
      if (Array.isArray(assets)) {
        var scriptsCount = 0;
        Object(external_this_lodash_["forEach"])(assets, function (asset) {
          if (asset.match(/\.js$/) !== null) {
            scriptsCount++;
            loadScript(asset, function () {
              scriptsCount--;

              if (scriptsCount === 0) {
                return resolve(scriptsCount);
              }
            }, reject);
          } else {
            loadStyle(asset);
          }
        });
      } else {
        loadScript(assets.editor_script, function () {
          return resolve(0);
        }, reject);
        loadStyle(assets.style);
      }
    });
  }
};
/* harmony default export */ var store_controls = (controls);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/actions.js


var actions_marked =
/*#__PURE__*/
external_this_regeneratorRuntime_default.a.mark(actions_downloadBlock),
    _marked2 =
/*#__PURE__*/
external_this_regeneratorRuntime_default.a.mark(actions_installBlock),
    _marked3 =
/*#__PURE__*/
external_this_regeneratorRuntime_default.a.mark(uninstallBlock);

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Returns an action object used in signalling that the downloadable blocks have been requested and is loading.
 *
 * @return {Object} Action object.
 */

function fetchDownloadableBlocks() {
  return {
    type: 'FETCH_DOWNLOADABLE_BLOCKS'
  };
}
/**
 * Returns an action object used in signalling that the downloadable blocks have been updated.
 *
 * @param {Array} downloadableBlocks Downloadable blocks.
 * @param {string} filterValue Search string.
 *
 * @return {Object} Action object.
 */

function receiveDownloadableBlocks(downloadableBlocks, filterValue) {
  return {
    type: 'RECEIVE_DOWNLOADABLE_BLOCKS',
    downloadableBlocks: downloadableBlocks,
    filterValue: filterValue
  };
}
/**
 * Returns an action object used in signalling that the user does not have permission to install blocks.
 *
 @param {boolean} hasPermission User has permission to install blocks.
 *
 * @return {Object} Action object.
 */

function setInstallBlocksPermission(hasPermission) {
  return {
    type: 'SET_INSTALL_BLOCKS_PERMISSION',
    hasPermission: hasPermission
  };
}
/**
 * Action triggered to download block assets.
 *
 * @param {Object} item The selected block item
 * @param {Function} onSuccess The callback function when the action has succeeded.
 * @param {Function} onError The callback function when the action has failed.
 */

function actions_downloadBlock(item, onSuccess, onError) {
  var registeredBlocks;
  return external_this_regeneratorRuntime_default.a.wrap(function downloadBlock$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.prev = 0;

          if (item.assets.length) {
            _context.next = 3;
            break;
          }

          throw new Error('Block has no assets');

        case 3:
          _context.next = 5;
          return loadAssets(item.assets);

        case 5:
          registeredBlocks = Object(external_this_wp_blocks_["getBlockTypes"])();

          if (!registeredBlocks.length) {
            _context.next = 10;
            break;
          }

          onSuccess(item);
          _context.next = 11;
          break;

        case 10:
          throw new Error('Unable to get block types');

        case 11:
          _context.next = 17;
          break;

        case 13:
          _context.prev = 13;
          _context.t0 = _context["catch"](0);
          _context.next = 17;
          return onError(_context.t0);

        case 17:
        case "end":
          return _context.stop();
      }
    }
  }, actions_marked, null, [[0, 13]]);
}
/**
 * Action triggered to install a block plugin.
 *
 * @param {string} item The block item returned by search.
 * @param {Function} onSuccess The callback function when the action has succeeded.
 * @param {Function} onError The callback function when the action has failed.
 *
 */

function actions_installBlock(_ref, onSuccess, onError) {
  var id, name, response;
  return external_this_regeneratorRuntime_default.a.wrap(function installBlock$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          id = _ref.id, name = _ref.name;
          _context2.prev = 1;
          _context2.next = 4;
          return apiFetch({
            path: '__experimental/block-directory/install',
            data: {
              slug: id
            },
            method: 'POST'
          });

        case 4:
          response = _context2.sent;

          if (!(response.success === false)) {
            _context2.next = 7;
            break;
          }

          throw new Error(response.errorMessage);

        case 7:
          _context2.next = 9;
          return addInstalledBlockType({
            id: id,
            name: name
          });

        case 9:
          onSuccess();
          _context2.next = 15;
          break;

        case 12:
          _context2.prev = 12;
          _context2.t0 = _context2["catch"](1);
          onError(_context2.t0);

        case 15:
        case "end":
          return _context2.stop();
      }
    }
  }, _marked2, null, [[1, 12]]);
}
/**
 * Action triggered to uninstall a block plugin.
 *
 * @param {string} item The block item returned by search.
 * @param {Function} onSuccess The callback function when the action has succeeded.
 * @param {Function} onError The callback function when the action has failed.
 *
 */

function uninstallBlock(_ref2, onSuccess, onError) {
  var id, name, response;
  return external_this_regeneratorRuntime_default.a.wrap(function uninstallBlock$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          id = _ref2.id, name = _ref2.name;
          _context3.prev = 1;
          _context3.next = 4;
          return apiFetch({
            path: '__experimental/block-directory/uninstall',
            data: {
              slug: id
            },
            method: 'DELETE'
          });

        case 4:
          response = _context3.sent;

          if (!(response.success === false)) {
            _context3.next = 7;
            break;
          }

          throw new Error(response.errorMessage);

        case 7:
          _context3.next = 9;
          return removeInstalledBlockType({
            id: id,
            name: name
          });

        case 9:
          onSuccess();
          _context3.next = 15;
          break;

        case 12:
          _context3.prev = 12;
          _context3.t0 = _context3["catch"](1);
          onError(_context3.t0);

        case 15:
        case "end":
          return _context3.stop();
      }
    }
  }, _marked3, null, [[1, 12]]);
}
/**
 * Returns an action object used to add a newly installed block type.
 *
 * @param {string} item The block item with the block id and name.
 *
 * @return {Object} Action object.
 */

function addInstalledBlockType(item) {
  return {
    type: 'ADD_INSTALLED_BLOCK_TYPE',
    item: item
  };
}
/**
 * Returns an action object used to remove a newly installed block type.
 *
 * @param {string} item The block item with the block id and name.
 *
 * @return {Object} Action object.
 */

function removeInstalledBlockType(item) {
  return {
    type: 'REMOVE_INSTALLED_BLOCK_TYPE',
    item: item
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/resolvers.js


/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



/* harmony default export */ var resolvers = ({
  getDownloadableBlocks:
  /*#__PURE__*/
  external_this_regeneratorRuntime_default.a.mark(function getDownloadableBlocks(filterValue) {
    var results, blocks;
    return external_this_regeneratorRuntime_default.a.wrap(function getDownloadableBlocks$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            if (filterValue) {
              _context.next = 2;
              break;
            }

            return _context.abrupt("return");

          case 2:
            _context.prev = 2;
            _context.next = 5;
            return fetchDownloadableBlocks(filterValue);

          case 5:
            _context.next = 7;
            return apiFetch({
              path: "__experimental/block-directory/search?term=".concat(filterValue)
            });

          case 7:
            results = _context.sent;
            blocks = results.map(function (result) {
              return Object(external_this_lodash_["mapKeys"])(result, function (value, key) {
                return Object(external_this_lodash_["camelCase"])(key);
              });
            });
            _context.next = 11;
            return receiveDownloadableBlocks(blocks, filterValue);

          case 11:
            _context.next = 18;
            break;

          case 13:
            _context.prev = 13;
            _context.t0 = _context["catch"](2);

            if (!(_context.t0.code === 'rest_user_cannot_view')) {
              _context.next = 18;
              break;
            }

            _context.next = 18;
            return setInstallBlocksPermission(false);

          case 18:
          case "end":
            return _context.stop();
        }
      }
    }, getDownloadableBlocks, null, [[2, 13]]);
  }),
  hasInstallBlocksPermission:
  /*#__PURE__*/
  external_this_regeneratorRuntime_default.a.mark(function hasInstallBlocksPermission() {
    return external_this_regeneratorRuntime_default.a.wrap(function hasInstallBlocksPermission$(_context2) {
      while (1) {
        switch (_context2.prev = _context2.next) {
          case 0:
            _context2.prev = 0;
            _context2.next = 3;
            return apiFetch({
              path: "__experimental/block-directory/search?term="
            });

          case 3:
            _context2.next = 5;
            return setInstallBlocksPermission(true);

          case 5:
            _context2.next = 12;
            break;

          case 7:
            _context2.prev = 7;
            _context2.t0 = _context2["catch"](0);

            if (!(_context2.t0.code === 'rest_user_cannot_view')) {
              _context2.next = 12;
              break;
            }

            _context2.next = 12;
            return setInstallBlocksPermission(false);

          case 12:
          case "end":
            return _context2.stop();
        }
      }
    }, hasInstallBlocksPermission, null, [[0, 7]]);
  })
});

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

var MODULE_KEY = 'core/block-directory';
/**
 * Block editor data store configuration.
 *
 * @see https://github.com/WordPress/gutenberg/blob/master/packages/data/README.md#registerStore
 *
 * @type {Object}
 */

var storeConfig = {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject,
  controls: store_controls,
  resolvers: resolvers
};
var store = Object(external_this_wp_data_["registerStore"])(MODULE_KEY, storeConfig);
/* harmony default export */ var build_module_store = (store);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","plugins"]}
var external_this_wp_plugins_ = __webpack_require__(55);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(20);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(3);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/block-ratings/stars.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function Stars(_ref) {
  var rating = _ref.rating;
  var stars = Math.round(rating / 0.5) * 0.5;
  var fullStarCount = Math.floor(rating);
  var halfStarCount = Math.ceil(rating - fullStarCount);
  var emptyStarCount = 5 - (fullStarCount + halfStarCount);
  return Object(external_this_wp_element_["createElement"])("div", {
    "aria-label": Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('%s out of 5 stars'), stars)
  }, Object(external_this_lodash_["times"])(fullStarCount, function (i) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
      key: "full_stars_".concat(i),
      icon: "star-filled",
      size: 16
    });
  }), Object(external_this_lodash_["times"])(halfStarCount, function (i) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
      key: "half_stars_".concat(i),
      icon: "star-half",
      size: 16
    });
  }), Object(external_this_lodash_["times"])(emptyStarCount, function (i) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
      key: "empty_stars_".concat(i),
      icon: "star-empty",
      size: 16
    });
  }));
}

/* harmony default export */ var block_ratings_stars = (Stars);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/block-ratings/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var block_ratings_BlockRatings = function BlockRatings(_ref) {
  var rating = _ref.rating,
      ratingCount = _ref.ratingCount;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-block-ratings"
  }, Object(external_this_wp_element_["createElement"])(block_ratings_stars, {
    rating: rating
  }), Object(external_this_wp_element_["createElement"])("span", {
    className: "block-directory-block-ratings__rating-count",
    "aria-label": // translators: %d: number of ratings (number).
    Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('%d total rating', '%d total ratings', ratingCount), ratingCount)
  }, "(", ratingCount, ")"));
};
/* harmony default export */ var block_ratings = (block_ratings_BlockRatings);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-header/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function DownloadableBlockHeader(_ref) {
  var icon = _ref.icon,
      title = _ref.title,
      rating = _ref.rating,
      ratingCount = _ref.ratingCount,
      _onClick = _ref.onClick;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-header__row"
  }, icon.match(/\.(jpeg|jpg|gif|png)(?:\?.*)?$/) !== null ? // translators: %s: Name of the plugin e.g: "Akismet".
  Object(external_this_wp_element_["createElement"])("img", {
    src: icon,
    alt: Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('%s block icon'), title)
  }) : Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
    icon: icon,
    showColors: true
  })), Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-header__column"
  }, Object(external_this_wp_element_["createElement"])("span", {
    role: "heading",
    className: "block-directory-downloadable-block-header__title"
  }, title), Object(external_this_wp_element_["createElement"])(block_ratings, {
    rating: rating,
    ratingCount: ratingCount
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isDefault: true,
    onClick: function onClick(event) {
      event.preventDefault();

      _onClick();
    }
  }, Object(external_this_wp_i18n_["__"])('Add block')));
}

/* harmony default export */ var downloadable_block_header = (DownloadableBlockHeader);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-author-info/index.js


/**
 * WordPress dependencies
 */



function DownloadableBlockAuthorInfo(_ref) {
  var author = _ref.author,
      authorBlockCount = _ref.authorBlockCount,
      authorBlockRating = _ref.authorBlockRating;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("span", {
    className: "block-directory-downloadable-block-author-info__content-author"
  }, Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Authored by %s'), author)), Object(external_this_wp_element_["createElement"])("span", {
    className: "block-directory-downloadable-block-author-info__content"
  }, Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('This author has %d block, with an average rating of %d.', 'This author has %d blocks, with an average rating of %d.', authorBlockCount), authorBlockCount, authorBlockRating)));
}

/* harmony default export */ var downloadable_block_author_info = (DownloadableBlockAuthorInfo);

// EXTERNAL MODULE: external {"this":["wp","primitives"]}
var external_this_wp_primitives_ = __webpack_require__(9);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/update.js


/**
 * WordPress dependencies
 */

var update = Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["Path"], {
  d: "M10.2 3.28c3.53 0 6.43 2.61 6.92 6h2.08l-3.5 4-3.5-4h2.32c-.45-1.97-2.21-3.45-4.32-3.45-1.45 0-2.73.71-3.54 1.78L4.95 5.66C6.23 4.2 8.11 3.28 10.2 3.28zm-.4 13.44c-3.52 0-6.43-2.61-6.92-6H.8l3.5-4c1.17 1.33 2.33 2.67 3.5 4H5.48c.45 1.97 2.21 3.45 4.32 3.45 1.45 0 2.73-.71 3.54-1.78l1.71 1.95c-1.28 1.46-3.15 2.38-5.25 2.38z"
}));
/* harmony default export */ var library_update = (update);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-info/index.js


/**
 * WordPress dependencies
 */





function DownloadableBlockInfo(_ref) {
  var description = _ref.description,
      activeInstalls = _ref.activeInstalls,
      humanizedUpdated = _ref.humanizedUpdated;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("p", {
    className: "block-directory-downloadable-block-info__content"
  }, description), Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-info__row"
  }, Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-info__column"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
    icon: "chart-line"
  }), Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('%d active installation', '%d active installations', activeInstalls), activeInstalls)), Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-info__column"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
    icon: library_update
  }), // translators: %s: Humanized date of last update e.g: "2 months ago".
  Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Updated %s'), humanizedUpdated))));
}

/* harmony default export */ var downloadable_block_info = (DownloadableBlockInfo);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-list-item/index.js


/**
 * Internal dependencies
 */




function DownloadableBlockListItem(_ref) {
  var item = _ref.item,
      onClick = _ref.onClick;
  var icon = item.icon,
      title = item.title,
      description = item.description,
      rating = item.rating,
      activeInstalls = item.activeInstalls,
      ratingCount = item.ratingCount,
      author = item.author,
      humanizedUpdated = item.humanizedUpdated,
      authorBlockCount = item.authorBlockCount,
      authorBlockRating = item.authorBlockRating;
  return Object(external_this_wp_element_["createElement"])("li", {
    className: "block-directory-downloadable-block-list-item"
  }, Object(external_this_wp_element_["createElement"])("article", {
    className: "block-directory-downloadable-block-list-item__panel"
  }, Object(external_this_wp_element_["createElement"])("header", {
    className: "block-directory-downloadable-block-list-item__header"
  }, Object(external_this_wp_element_["createElement"])(downloadable_block_header, {
    icon: icon,
    onClick: onClick,
    title: title,
    rating: rating,
    ratingCount: ratingCount
  })), Object(external_this_wp_element_["createElement"])("section", {
    className: "block-directory-downloadable-block-list-item__body"
  }, Object(external_this_wp_element_["createElement"])(downloadable_block_info, {
    activeInstalls: activeInstalls,
    description: description,
    humanizedUpdated: humanizedUpdated
  })), Object(external_this_wp_element_["createElement"])("footer", {
    className: "block-directory-downloadable-block-list-item__footer"
  }, Object(external_this_wp_element_["createElement"])(downloadable_block_author_info, {
    author: author,
    authorBlockCount: authorBlockCount,
    authorBlockRating: authorBlockRating
  }))));
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


var DOWNLOAD_ERROR_NOTICE_ID = 'block-download-error';
var INSTALL_ERROR_NOTICE_ID = 'block-install-error';

function DownloadableBlocksList(_ref) {
  var items = _ref.items,
      _ref$onHover = _ref.onHover,
      onHover = _ref$onHover === void 0 ? external_this_lodash_["noop"] : _ref$onHover,
      children = _ref.children,
      downloadAndInstallBlock = _ref.downloadAndInstallBlock;
  return (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */

    /* eslint-disable jsx-a11y/no-redundant-roles */
    Object(external_this_wp_element_["createElement"])("ul", {
      role: "list",
      className: "block-directory-downloadable-blocks-list"
    }, items && items.map(function (item) {
      return Object(external_this_wp_element_["createElement"])(downloadable_block_list_item, {
        key: item.id,
        className: Object(external_this_wp_blocks_["getBlockMenuDefaultClassName"])(item.id),
        icons: item.icons,
        onClick: function onClick() {
          downloadAndInstallBlock(item);
          onHover(null);
        },
        onFocus: function onFocus() {
          return onHover(item);
        },
        onMouseEnter: function onMouseEnter() {
          return onHover(item);
        },
        onMouseLeave: function onMouseLeave() {
          return onHover(null);
        },
        onBlur: function onBlur() {
          return onHover(null);
        },
        item: item
      });
    }), children)
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
}

/* harmony default export */ var downloadable_blocks_list = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withDispatch"])(function (dispatch, props) {
  var _dispatch = dispatch('core/block-directory'),
      installBlock = _dispatch.installBlock,
      downloadBlock = _dispatch.downloadBlock;

  var _dispatch2 = dispatch('core/notices'),
      createErrorNotice = _dispatch2.createErrorNotice,
      removeNotice = _dispatch2.removeNotice;

  var _dispatch3 = dispatch('core/block-editor'),
      removeBlocks = _dispatch3.removeBlocks;

  var onSelect = props.onSelect;
  return {
    downloadAndInstallBlock: function downloadAndInstallBlock(item) {
      var onDownloadError = function onDownloadError() {
        createErrorNotice(Object(external_this_wp_i18n_["__"])('Block previews can’t load.'), {
          id: DOWNLOAD_ERROR_NOTICE_ID,
          actions: [{
            label: Object(external_this_wp_i18n_["__"])('Retry'),
            onClick: function onClick() {
              removeNotice(DOWNLOAD_ERROR_NOTICE_ID);
              downloadBlock(item, onSuccess, onDownloadError);
            }
          }]
        });
      };

      var onSuccess = function onSuccess() {
        var createdBlock = onSelect(item);

        var onInstallBlockError = function onInstallBlockError() {
          createErrorNotice(Object(external_this_wp_i18n_["__"])("Block previews can't install."), {
            id: INSTALL_ERROR_NOTICE_ID,
            actions: [{
              label: Object(external_this_wp_i18n_["__"])('Retry'),
              onClick: function onClick() {
                removeNotice(INSTALL_ERROR_NOTICE_ID);
                installBlock(item, external_this_lodash_["noop"], onInstallBlockError);
              }
            }, {
              label: Object(external_this_wp_i18n_["__"])('Remove'),
              onClick: function onClick() {
                removeNotice(INSTALL_ERROR_NOTICE_ID);
                removeBlocks(createdBlock.clientId);
                Object(external_this_wp_blocks_["unregisterBlockType"])(item.name);
              }
            }]
          });
        };

        installBlock(item, external_this_lodash_["noop"], onInstallBlockError);
      };

      downloadBlock(item, onSuccess, onDownloadError);
    }
  };
}))(DownloadableBlocksList));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-panel/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function DownloadableBlocksPanel(_ref) {
  var downloadableItems = _ref.downloadableItems,
      onSelect = _ref.onSelect,
      onHover = _ref.onHover,
      hasPermission = _ref.hasPermission,
      isLoading = _ref.isLoading,
      isWaiting = _ref.isWaiting,
      debouncedSpeak = _ref.debouncedSpeak;

  if (!hasPermission) {
    debouncedSpeak(Object(external_this_wp_i18n_["__"])('No blocks found in your library. Please contact your site administrator to install new blocks.'));
    return Object(external_this_wp_element_["createElement"])("p", {
      className: "block-directory-downloadable-blocks-panel__description has-no-results"
    }, Object(external_this_wp_i18n_["__"])('No blocks found in your library.'), Object(external_this_wp_element_["createElement"])("br", null), Object(external_this_wp_i18n_["__"])('Please contact your site administrator to install new blocks.'));
  }

  if (isLoading || isWaiting) {
    return Object(external_this_wp_element_["createElement"])("p", {
      className: "block-directory-downloadable-blocks-panel__description has-no-results"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null));
  }

  if (!downloadableItems.length) {
    return Object(external_this_wp_element_["createElement"])("p", {
      className: "block-directory-downloadable-blocks-panel__description has-no-results"
    }, Object(external_this_wp_i18n_["__"])('No blocks found in your library.'));
  }

  var resultsFoundMessage = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('No blocks found in your library. We did find %d block available for download.', 'No blocks found in your library. We did find %d blocks available for download.', downloadableItems.length), downloadableItems.length);
  debouncedSpeak(resultsFoundMessage);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("p", {
    className: "block-directory-downloadable-blocks-panel__description"
  }, Object(external_this_wp_i18n_["__"])('No blocks found in your library. These blocks can be downloaded and installed:')), Object(external_this_wp_element_["createElement"])(downloadable_blocks_list, {
    items: downloadableItems,
    onSelect: onSelect,
    onHover: onHover
  }));
}

/* harmony default export */ var downloadable_blocks_panel = (Object(external_this_wp_compose_["compose"])([external_this_wp_components_["withSpokenMessages"], Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var filterValue = _ref2.filterValue;

  var _select = select('core/block-directory'),
      getDownloadableBlocks = _select.getDownloadableBlocks,
      hasInstallBlocksPermission = _select.hasInstallBlocksPermission,
      isRequestingDownloadableBlocks = _select.isRequestingDownloadableBlocks;

  var hasPermission = hasInstallBlocksPermission();
  var downloadableItems = hasPermission ? getDownloadableBlocks(filterValue) : [];
  var isLoading = isRequestingDownloadableBlocks();
  return {
    downloadableItems: downloadableItems,
    hasPermission: hasPermission,
    isLoading: isLoading
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
  var _useState = Object(external_this_wp_element_["useState"])(''),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      debouncedFilterValue = _useState2[0],
      setFilterValue = _useState2[1];

  var debouncedSetFilterValue = Object(external_this_lodash_["debounce"])(setFilterValue, 400);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalInserterMenuExtension"], null, function (_ref) {
    var onSelect = _ref.onSelect,
        onHover = _ref.onHover,
        filterValue = _ref.filterValue,
        hasItems = _ref.hasItems;

    if (hasItems || !filterValue) {
      return null;
    }

    if (debouncedFilterValue !== filterValue) {
      debouncedSetFilterValue(filterValue);
    }

    return Object(external_this_wp_element_["createElement"])(downloadable_blocks_panel, {
      onSelect: onSelect,
      onHover: onHover,
      filterValue: debouncedFilterValue,
      isWaiting: filterValue !== debouncedFilterValue
    });
  });
}

/* harmony default export */ var inserter_menu_downloadable_blocks_panel = (InserterMenuDownloadableBlocksPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/plugins/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


Object(external_this_wp_plugins_["registerPlugin"])('block-directory', {
  render: function render() {
    return Object(external_this_wp_element_["createElement"])(inserter_menu_downloadable_blocks_panel, null);
  }
});

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/index.js
/**
 * Internal dependencies
 */




/***/ }),

/***/ 5:
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

/***/ 55:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["plugins"]; }());

/***/ }),

/***/ 6:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockEditor"]; }());

/***/ }),

/***/ 8:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ 9:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["primitives"]; }());

/***/ })

/******/ });