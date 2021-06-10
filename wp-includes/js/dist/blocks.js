this["wp"] = this["wp"] || {}; this["wp"]["blocks"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "0ATp");
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

/***/ "0ATp":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ store; });
__webpack_require__.d(__webpack_exports__, "createBlock", function() { return /* reexport */ createBlock; });
__webpack_require__.d(__webpack_exports__, "createBlocksFromInnerBlocksTemplate", function() { return /* reexport */ createBlocksFromInnerBlocksTemplate; });
__webpack_require__.d(__webpack_exports__, "cloneBlock", function() { return /* reexport */ cloneBlock; });
__webpack_require__.d(__webpack_exports__, "__experimentalCloneSanitizedBlock", function() { return /* reexport */ __experimentalCloneSanitizedBlock; });
__webpack_require__.d(__webpack_exports__, "getPossibleBlockTransformations", function() { return /* reexport */ getPossibleBlockTransformations; });
__webpack_require__.d(__webpack_exports__, "switchToBlockType", function() { return /* reexport */ switchToBlockType; });
__webpack_require__.d(__webpack_exports__, "getBlockTransforms", function() { return /* reexport */ getBlockTransforms; });
__webpack_require__.d(__webpack_exports__, "findTransform", function() { return /* reexport */ findTransform; });
__webpack_require__.d(__webpack_exports__, "getBlockFromExample", function() { return /* reexport */ getBlockFromExample; });
__webpack_require__.d(__webpack_exports__, "parse", function() { return /* reexport */ parser; });
__webpack_require__.d(__webpack_exports__, "getBlockAttributes", function() { return /* reexport */ getBlockAttributes; });
__webpack_require__.d(__webpack_exports__, "parseWithAttributeSchema", function() { return /* reexport */ parseWithAttributeSchema; });
__webpack_require__.d(__webpack_exports__, "pasteHandler", function() { return /* reexport */ pasteHandler; });
__webpack_require__.d(__webpack_exports__, "rawHandler", function() { return /* reexport */ rawHandler; });
__webpack_require__.d(__webpack_exports__, "getPhrasingContentSchema", function() { return /* reexport */ deprecatedGetPhrasingContentSchema; });
__webpack_require__.d(__webpack_exports__, "serialize", function() { return /* reexport */ serialize; });
__webpack_require__.d(__webpack_exports__, "getBlockContent", function() { return /* reexport */ getBlockInnerHTML; });
__webpack_require__.d(__webpack_exports__, "getBlockDefaultClassName", function() { return /* reexport */ getBlockDefaultClassName; });
__webpack_require__.d(__webpack_exports__, "getBlockMenuDefaultClassName", function() { return /* reexport */ getBlockMenuDefaultClassName; });
__webpack_require__.d(__webpack_exports__, "getSaveElement", function() { return /* reexport */ getSaveElement; });
__webpack_require__.d(__webpack_exports__, "getSaveContent", function() { return /* reexport */ getSaveContent; });
__webpack_require__.d(__webpack_exports__, "__unstableGetBlockProps", function() { return /* reexport */ getBlockProps; });
__webpack_require__.d(__webpack_exports__, "__unstableSerializeAndClean", function() { return /* reexport */ __unstableSerializeAndClean; });
__webpack_require__.d(__webpack_exports__, "isValidBlockContent", function() { return /* reexport */ isValidBlockContent; });
__webpack_require__.d(__webpack_exports__, "getCategories", function() { return /* reexport */ categories_getCategories; });
__webpack_require__.d(__webpack_exports__, "setCategories", function() { return /* reexport */ categories_setCategories; });
__webpack_require__.d(__webpack_exports__, "updateCategory", function() { return /* reexport */ categories_updateCategory; });
__webpack_require__.d(__webpack_exports__, "registerBlockType", function() { return /* reexport */ registerBlockType; });
__webpack_require__.d(__webpack_exports__, "registerBlockTypeFromMetadata", function() { return /* reexport */ registerBlockTypeFromMetadata; });
__webpack_require__.d(__webpack_exports__, "registerBlockCollection", function() { return /* reexport */ registerBlockCollection; });
__webpack_require__.d(__webpack_exports__, "unregisterBlockType", function() { return /* reexport */ unregisterBlockType; });
__webpack_require__.d(__webpack_exports__, "setFreeformContentHandlerName", function() { return /* reexport */ setFreeformContentHandlerName; });
__webpack_require__.d(__webpack_exports__, "getFreeformContentHandlerName", function() { return /* reexport */ getFreeformContentHandlerName; });
__webpack_require__.d(__webpack_exports__, "setUnregisteredTypeHandlerName", function() { return /* reexport */ setUnregisteredTypeHandlerName; });
__webpack_require__.d(__webpack_exports__, "getUnregisteredTypeHandlerName", function() { return /* reexport */ getUnregisteredTypeHandlerName; });
__webpack_require__.d(__webpack_exports__, "setDefaultBlockName", function() { return /* reexport */ registration_setDefaultBlockName; });
__webpack_require__.d(__webpack_exports__, "getDefaultBlockName", function() { return /* reexport */ registration_getDefaultBlockName; });
__webpack_require__.d(__webpack_exports__, "setGroupingBlockName", function() { return /* reexport */ registration_setGroupingBlockName; });
__webpack_require__.d(__webpack_exports__, "getGroupingBlockName", function() { return /* reexport */ registration_getGroupingBlockName; });
__webpack_require__.d(__webpack_exports__, "getBlockType", function() { return /* reexport */ registration_getBlockType; });
__webpack_require__.d(__webpack_exports__, "getBlockTypes", function() { return /* reexport */ registration_getBlockTypes; });
__webpack_require__.d(__webpack_exports__, "getBlockSupport", function() { return /* reexport */ registration_getBlockSupport; });
__webpack_require__.d(__webpack_exports__, "hasBlockSupport", function() { return /* reexport */ registration_hasBlockSupport; });
__webpack_require__.d(__webpack_exports__, "getBlockVariations", function() { return /* reexport */ registration_getBlockVariations; });
__webpack_require__.d(__webpack_exports__, "isReusableBlock", function() { return /* reexport */ isReusableBlock; });
__webpack_require__.d(__webpack_exports__, "isTemplatePart", function() { return /* reexport */ isTemplatePart; });
__webpack_require__.d(__webpack_exports__, "getChildBlockNames", function() { return /* reexport */ registration_getChildBlockNames; });
__webpack_require__.d(__webpack_exports__, "hasChildBlocks", function() { return /* reexport */ registration_hasChildBlocks; });
__webpack_require__.d(__webpack_exports__, "hasChildBlocksWithInserterSupport", function() { return /* reexport */ registration_hasChildBlocksWithInserterSupport; });
__webpack_require__.d(__webpack_exports__, "unstable__bootstrapServerSideBlockDefinitions", function() { return /* reexport */ unstable__bootstrapServerSideBlockDefinitions; });
__webpack_require__.d(__webpack_exports__, "registerBlockStyle", function() { return /* reexport */ registerBlockStyle; });
__webpack_require__.d(__webpack_exports__, "unregisterBlockStyle", function() { return /* reexport */ unregisterBlockStyle; });
__webpack_require__.d(__webpack_exports__, "registerBlockVariation", function() { return /* reexport */ registerBlockVariation; });
__webpack_require__.d(__webpack_exports__, "unregisterBlockVariation", function() { return /* reexport */ unregisterBlockVariation; });
__webpack_require__.d(__webpack_exports__, "isUnmodifiedDefaultBlock", function() { return /* reexport */ isUnmodifiedDefaultBlock; });
__webpack_require__.d(__webpack_exports__, "normalizeIconObject", function() { return /* reexport */ normalizeIconObject; });
__webpack_require__.d(__webpack_exports__, "isValidIcon", function() { return /* reexport */ isValidIcon; });
__webpack_require__.d(__webpack_exports__, "__experimentalGetBlockLabel", function() { return /* reexport */ getBlockLabel; });
__webpack_require__.d(__webpack_exports__, "__experimentalGetAccessibleBlockLabel", function() { return /* reexport */ getAccessibleBlockLabel; });
__webpack_require__.d(__webpack_exports__, "__experimentalSanitizeBlockAttributes", function() { return /* reexport */ __experimentalSanitizeBlockAttributes; });
__webpack_require__.d(__webpack_exports__, "__experimentalGetBlockAttributesNamesByRole", function() { return /* reexport */ __experimentalGetBlockAttributesNamesByRole; });
__webpack_require__.d(__webpack_exports__, "doBlocksMatchTemplate", function() { return /* reexport */ doBlocksMatchTemplate; });
__webpack_require__.d(__webpack_exports__, "synchronizeBlocksWithTemplate", function() { return /* reexport */ synchronizeBlocksWithTemplate; });
__webpack_require__.d(__webpack_exports__, "children", function() { return /* reexport */ api_children; });
__webpack_require__.d(__webpack_exports__, "node", function() { return /* reexport */ api_node; });
__webpack_require__.d(__webpack_exports__, "__EXPERIMENTAL_STYLE_PROPERTY", function() { return /* reexport */ __EXPERIMENTAL_STYLE_PROPERTY; });
__webpack_require__.d(__webpack_exports__, "__EXPERIMENTAL_ELEMENTS", function() { return /* reexport */ __EXPERIMENTAL_ELEMENTS; });
__webpack_require__.d(__webpack_exports__, "withBlockContentContext", function() { return /* reexport */ withBlockContentContext; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/blocks/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getBlockTypes", function() { return getBlockTypes; });
__webpack_require__.d(selectors_namespaceObject, "getBlockType", function() { return getBlockType; });
__webpack_require__.d(selectors_namespaceObject, "getBlockStyles", function() { return getBlockStyles; });
__webpack_require__.d(selectors_namespaceObject, "getBlockVariations", function() { return getBlockVariations; });
__webpack_require__.d(selectors_namespaceObject, "getActiveBlockVariation", function() { return getActiveBlockVariation; });
__webpack_require__.d(selectors_namespaceObject, "getDefaultBlockVariation", function() { return getDefaultBlockVariation; });
__webpack_require__.d(selectors_namespaceObject, "getCategories", function() { return getCategories; });
__webpack_require__.d(selectors_namespaceObject, "getCollections", function() { return getCollections; });
__webpack_require__.d(selectors_namespaceObject, "getDefaultBlockName", function() { return getDefaultBlockName; });
__webpack_require__.d(selectors_namespaceObject, "getFreeformFallbackBlockName", function() { return getFreeformFallbackBlockName; });
__webpack_require__.d(selectors_namespaceObject, "getUnregisteredFallbackBlockName", function() { return getUnregisteredFallbackBlockName; });
__webpack_require__.d(selectors_namespaceObject, "getGroupingBlockName", function() { return getGroupingBlockName; });
__webpack_require__.d(selectors_namespaceObject, "getChildBlockNames", function() { return getChildBlockNames; });
__webpack_require__.d(selectors_namespaceObject, "getBlockSupport", function() { return getBlockSupport; });
__webpack_require__.d(selectors_namespaceObject, "hasBlockSupport", function() { return hasBlockSupport; });
__webpack_require__.d(selectors_namespaceObject, "isMatchingSearchTerm", function() { return isMatchingSearchTerm; });
__webpack_require__.d(selectors_namespaceObject, "hasChildBlocks", function() { return hasChildBlocks; });
__webpack_require__.d(selectors_namespaceObject, "hasChildBlocksWithInserterSupport", function() { return hasChildBlocksWithInserterSupport; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/blocks/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "addBlockTypes", function() { return addBlockTypes; });
__webpack_require__.d(actions_namespaceObject, "removeBlockTypes", function() { return removeBlockTypes; });
__webpack_require__.d(actions_namespaceObject, "addBlockStyles", function() { return addBlockStyles; });
__webpack_require__.d(actions_namespaceObject, "removeBlockStyles", function() { return removeBlockStyles; });
__webpack_require__.d(actions_namespaceObject, "addBlockVariations", function() { return addBlockVariations; });
__webpack_require__.d(actions_namespaceObject, "removeBlockVariations", function() { return removeBlockVariations; });
__webpack_require__.d(actions_namespaceObject, "setDefaultBlockName", function() { return setDefaultBlockName; });
__webpack_require__.d(actions_namespaceObject, "setFreeformFallbackBlockName", function() { return setFreeformFallbackBlockName; });
__webpack_require__.d(actions_namespaceObject, "setUnregisteredFallbackBlockName", function() { return setUnregisteredFallbackBlockName; });
__webpack_require__.d(actions_namespaceObject, "setGroupingBlockName", function() { return setGroupingBlockName; });
__webpack_require__.d(actions_namespaceObject, "setCategories", function() { return setCategories; });
__webpack_require__.d(actions_namespaceObject, "updateCategory", function() { return updateCategory; });
__webpack_require__.d(actions_namespaceObject, "addBlockCollection", function() { return addBlockCollection; });
__webpack_require__.d(actions_namespaceObject, "removeBlockCollection", function() { return removeBlockCollection; });

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/store/reducer.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * @typedef {Object} WPBlockCategory
 *
 * @property {string} slug  Unique category slug.
 * @property {string} title Category label, for display in user interface.
 */

/**
 * Default set of categories.
 *
 * @type {WPBlockCategory[]}
 */

const DEFAULT_CATEGORIES = [{
  slug: 'text',
  title: Object(external_wp_i18n_["__"])('Text')
}, {
  slug: 'media',
  title: Object(external_wp_i18n_["__"])('Media')
}, {
  slug: 'design',
  title: Object(external_wp_i18n_["__"])('Design')
}, {
  slug: 'widgets',
  title: Object(external_wp_i18n_["__"])('Widgets')
}, {
  slug: 'theme',
  title: Object(external_wp_i18n_["__"])('Theme')
}, {
  slug: 'embed',
  title: Object(external_wp_i18n_["__"])('Embeds')
}, {
  slug: 'reusable',
  title: Object(external_wp_i18n_["__"])('Reusable blocks')
}];
/**
 * Reducer managing the block types
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer_blockTypes(state = {}, action) {
  switch (action.type) {
    case 'ADD_BLOCK_TYPES':
      return { ...state,
        ...Object(external_lodash_["keyBy"])(Object(external_lodash_["map"])(action.blockTypes, blockType => Object(external_lodash_["omit"])(blockType, 'styles ')), 'name')
      };

    case 'REMOVE_BLOCK_TYPES':
      return Object(external_lodash_["omit"])(state, action.names);
  }

  return state;
}
/**
 * Reducer managing the block style variations.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function blockStyles(state = {}, action) {
  switch (action.type) {
    case 'ADD_BLOCK_TYPES':
      return { ...state,
        ...Object(external_lodash_["mapValues"])(Object(external_lodash_["keyBy"])(action.blockTypes, 'name'), blockType => {
          return Object(external_lodash_["uniqBy"])([...Object(external_lodash_["get"])(blockType, ['styles'], []), ...Object(external_lodash_["get"])(state, [blockType.name], [])], style => style.name);
        })
      };

    case 'ADD_BLOCK_STYLES':
      return { ...state,
        [action.blockName]: Object(external_lodash_["uniqBy"])([...Object(external_lodash_["get"])(state, [action.blockName], []), ...action.styles], style => style.name)
      };

    case 'REMOVE_BLOCK_STYLES':
      return { ...state,
        [action.blockName]: Object(external_lodash_["filter"])(Object(external_lodash_["get"])(state, [action.blockName], []), style => action.styleNames.indexOf(style.name) === -1)
      };
  }

  return state;
}
/**
 * Reducer managing the block variations.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function blockVariations(state = {}, action) {
  switch (action.type) {
    case 'ADD_BLOCK_TYPES':
      return { ...state,
        ...Object(external_lodash_["mapValues"])(Object(external_lodash_["keyBy"])(action.blockTypes, 'name'), blockType => {
          return Object(external_lodash_["uniqBy"])([...Object(external_lodash_["get"])(blockType, ['variations'], []), ...Object(external_lodash_["get"])(state, [blockType.name], [])], variation => variation.name);
        })
      };

    case 'ADD_BLOCK_VARIATIONS':
      return { ...state,
        [action.blockName]: Object(external_lodash_["uniqBy"])([...Object(external_lodash_["get"])(state, [action.blockName], []), ...action.variations], variation => variation.name)
      };

    case 'REMOVE_BLOCK_VARIATIONS':
      return { ...state,
        [action.blockName]: Object(external_lodash_["filter"])(Object(external_lodash_["get"])(state, [action.blockName], []), variation => action.variationNames.indexOf(variation.name) === -1)
      };
  }

  return state;
}
/**
 * Higher-order Reducer creating a reducer keeping track of given block name.
 *
 * @param {string} setActionType  Action type.
 *
 * @return {Function} Reducer.
 */

function createBlockNameSetterReducer(setActionType) {
  return (state = null, action) => {
    switch (action.type) {
      case 'REMOVE_BLOCK_TYPES':
        if (action.names.indexOf(state) !== -1) {
          return null;
        }

        return state;

      case setActionType:
        return action.name || null;
    }

    return state;
  };
}
const reducer_defaultBlockName = createBlockNameSetterReducer('SET_DEFAULT_BLOCK_NAME');
const freeformFallbackBlockName = createBlockNameSetterReducer('SET_FREEFORM_FALLBACK_BLOCK_NAME');
const unregisteredFallbackBlockName = createBlockNameSetterReducer('SET_UNREGISTERED_FALLBACK_BLOCK_NAME');
const groupingBlockName = createBlockNameSetterReducer('SET_GROUPING_BLOCK_NAME');
/**
 * Reducer managing the categories
 *
 * @param {WPBlockCategory[]} state  Current state.
 * @param {Object}            action Dispatched action.
 *
 * @return {WPBlockCategory[]} Updated state.
 */

function reducer_categories(state = DEFAULT_CATEGORIES, action) {
  switch (action.type) {
    case 'SET_CATEGORIES':
      return action.categories || [];

    case 'UPDATE_CATEGORY':
      {
        if (!action.category || Object(external_lodash_["isEmpty"])(action.category)) {
          return state;
        }

        const categoryToChange = Object(external_lodash_["find"])(state, ['slug', action.slug]);

        if (categoryToChange) {
          return Object(external_lodash_["map"])(state, category => {
            if (category.slug === action.slug) {
              return { ...category,
                ...action.category
              };
            }

            return category;
          });
        }
      }
  }

  return state;
}
function collections(state = {}, action) {
  switch (action.type) {
    case 'ADD_BLOCK_COLLECTION':
      return { ...state,
        [action.namespace]: {
          title: action.title,
          icon: action.icon
        }
      };

    case 'REMOVE_BLOCK_COLLECTION':
      return Object(external_lodash_["omit"])(state, action.namespace);
  }

  return state;
}
/* harmony default export */ var reducer = (Object(external_wp_data_["combineReducers"])({
  blockTypes: reducer_blockTypes,
  blockStyles,
  blockVariations,
  defaultBlockName: reducer_defaultBlockName,
  freeformFallbackBlockName,
  unregisteredFallbackBlockName,
  groupingBlockName,
  categories: reducer_categories,
  collections
}));

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/store/selectors.js
/**
 * External dependencies
 */


/** @typedef {import('../api/registration').WPBlockVariation} WPBlockVariation */

/** @typedef {import('../api/registration').WPBlockVariationScope} WPBlockVariationScope */

/** @typedef {import('./reducer').WPBlockCategory} WPBlockCategory */

/**
 * Given a block name or block type object, returns the corresponding
 * normalized block type object.
 *
 * @param {Object}          state      Blocks state.
 * @param {(string|Object)} nameOrType Block name or type object
 *
 * @return {Object} Block type object.
 */

const getNormalizedBlockType = (state, nameOrType) => 'string' === typeof nameOrType ? getBlockType(state, nameOrType) : nameOrType;
/**
 * Returns all the available block types.
 *
 * @param {Object} state Data state.
 *
 * @return {Array} Block Types.
 */


const getBlockTypes = Object(rememo["a" /* default */])(state => {
  return Object.values(state.blockTypes).map(blockType => {
    return { ...blockType,
      variations: getBlockVariations(state, blockType.name)
    };
  });
}, state => [state.blockTypes, state.blockVariations]);
/**
 * Returns a block type by name.
 *
 * @param {Object} state Data state.
 * @param {string} name Block type name.
 *
 * @return {Object?} Block Type.
 */

function getBlockType(state, name) {
  return state.blockTypes[name];
}
/**
 * Returns block styles by block name.
 *
 * @param {Object} state Data state.
 * @param {string} name  Block type name.
 *
 * @return {Array?} Block Styles.
 */

function getBlockStyles(state, name) {
  return state.blockStyles[name];
}
/**
 * Returns block variations by block name.
 *
 * @param {Object}                state     Data state.
 * @param {string}                blockName Block type name.
 * @param {WPBlockVariationScope} [scope]   Block variation scope name.
 *
 * @return {(WPBlockVariation[]|void)} Block variations.
 */

const getBlockVariations = Object(rememo["a" /* default */])((state, blockName, scope) => {
  const variations = state.blockVariations[blockName];

  if (!variations || !scope) {
    return variations;
  }

  return variations.filter(variation => {
    // For backward compatibility reasons, variation's scope defaults to
    // `block` and `inserter` when not set.
    return (variation.scope || ['block', 'inserter']).includes(scope);
  });
}, (state, blockName) => [state.blockVariations[blockName]]);
/**
 * Returns the active block variation for a given block based on its attributes.
 * Variations are determined by their `isActive` property.
 * Which is either an array of block attribute keys or a function.
 *
 * In case of an array of block attribute keys, the `attributes` are compared
 * to the variation's attributes using strict equality check.
 *
 * In case of function type, the function should accept a block's attributes
 * and the variation's attributes and determines if a variation is active.
 * A function that accepts a block's attributes and the variation's attributes and determines if a variation is active.
 *
 * @param {Object}                state      Data state.
 * @param {string}                blockName  Name of block (example: “core/columns”).
 * @param {Object}                attributes Block attributes used to determine active variation.
 * @param {WPBlockVariationScope} [scope]    Block variation scope name.
 *
 * @return {(WPBlockVariation|undefined)} Active block variation.
 */

function getActiveBlockVariation(state, blockName, attributes, scope) {
  const variations = getBlockVariations(state, blockName, scope);
  const match = variations === null || variations === void 0 ? void 0 : variations.find(variation => {
    var _variation$isActive;

    if (Array.isArray(variation.isActive)) {
      const blockType = getBlockType(state, blockName);
      const attributeKeys = Object.keys(blockType.attributes || {});
      const definedAttributes = variation.isActive.filter(attribute => attributeKeys.includes(attribute));

      if (definedAttributes.length === 0) {
        return false;
      }

      return definedAttributes.every(attribute => attributes[attribute] === variation.attributes[attribute]);
    }

    return (_variation$isActive = variation.isActive) === null || _variation$isActive === void 0 ? void 0 : _variation$isActive.call(variation, attributes, variation.attributes);
  });
  return match;
}
/**
 * Returns the default block variation for the given block type.
 * When there are multiple variations annotated as the default one,
 * the last added item is picked. This simplifies registering overrides.
 * When there is no default variation set, it returns the first item.
 *
 * @param {Object}                state     Data state.
 * @param {string}                blockName Block type name.
 * @param {WPBlockVariationScope} [scope]   Block variation scope name.
 *
 * @return {?WPBlockVariation} The default block variation.
 */

function getDefaultBlockVariation(state, blockName, scope) {
  const variations = getBlockVariations(state, blockName, scope);
  return Object(external_lodash_["findLast"])(variations, 'isDefault') || Object(external_lodash_["first"])(variations);
}
/**
 * Returns all the available categories.
 *
 * @param {Object} state Data state.
 *
 * @return {WPBlockCategory[]} Categories list.
 */

function getCategories(state) {
  return state.categories;
}
/**
 * Returns all the available collections.
 *
 * @param {Object} state Data state.
 *
 * @return {Object} Collections list.
 */

function getCollections(state) {
  return state.collections;
}
/**
 * Returns the name of the default block name.
 *
 * @param {Object} state Data state.
 *
 * @return {string?} Default block name.
 */

function getDefaultBlockName(state) {
  return state.defaultBlockName;
}
/**
 * Returns the name of the block for handling non-block content.
 *
 * @param {Object} state Data state.
 *
 * @return {string?} Name of the block for handling non-block content.
 */

function getFreeformFallbackBlockName(state) {
  return state.freeformFallbackBlockName;
}
/**
 * Returns the name of the block for handling unregistered blocks.
 *
 * @param {Object} state Data state.
 *
 * @return {string?} Name of the block for handling unregistered blocks.
 */

function getUnregisteredFallbackBlockName(state) {
  return state.unregisteredFallbackBlockName;
}
/**
 * Returns the name of the block for handling unregistered blocks.
 *
 * @param {Object} state Data state.
 *
 * @return {string?} Name of the block for handling unregistered blocks.
 */

function getGroupingBlockName(state) {
  return state.groupingBlockName;
}
/**
 * Returns an array with the child blocks of a given block.
 *
 * @param {Object} state     Data state.
 * @param {string} blockName Block type name.
 *
 * @return {Array} Array of child block names.
 */

const getChildBlockNames = Object(rememo["a" /* default */])((state, blockName) => {
  return Object(external_lodash_["map"])(Object(external_lodash_["filter"])(state.blockTypes, blockType => {
    return Object(external_lodash_["includes"])(blockType.parent, blockName);
  }), ({
    name
  }) => name);
}, state => [state.blockTypes]);
/**
 * Returns the block support value for a feature, if defined.
 *
 * @param  {Object}          state           Data state.
 * @param  {(string|Object)} nameOrType      Block name or type object
 * @param  {string}          feature         Feature to retrieve
 * @param  {*}               defaultSupports Default value to return if not
 *                                           explicitly defined
 *
 * @return {?*} Block support value
 */

const getBlockSupport = (state, nameOrType, feature, defaultSupports) => {
  const blockType = getNormalizedBlockType(state, nameOrType);
  return Object(external_lodash_["get"])(blockType, ['supports', ...feature.split('.')], defaultSupports);
};
/**
 * Returns true if the block defines support for a feature, or false otherwise.
 *
 * @param  {Object}         state           Data state.
 * @param {(string|Object)} nameOrType      Block name or type object.
 * @param {string}          feature         Feature to test.
 * @param {boolean}         defaultSupports Whether feature is supported by
 *                                          default if not explicitly defined.
 *
 * @return {boolean} Whether block supports feature.
 */

function hasBlockSupport(state, nameOrType, feature, defaultSupports) {
  return !!getBlockSupport(state, nameOrType, feature, defaultSupports);
}
/**
 * Returns true if the block type by the given name or object value matches a
 * search term, or false otherwise.
 *
 * @param {Object}          state      Blocks state.
 * @param {(string|Object)} nameOrType Block name or type object.
 * @param {string}          searchTerm Search term by which to filter.
 *
 * @return {Object[]} Whether block type matches search term.
 */

function isMatchingSearchTerm(state, nameOrType, searchTerm) {
  const blockType = getNormalizedBlockType(state, nameOrType);
  const getNormalizedSearchTerm = Object(external_lodash_["flow"])([// Disregard diacritics.
  //  Input: "média"
  external_lodash_["deburr"], // Lowercase.
  //  Input: "MEDIA"
  term => term.toLowerCase(), // Strip leading and trailing whitespace.
  //  Input: " media "
  term => term.trim()]);
  const normalizedSearchTerm = getNormalizedSearchTerm(searchTerm);
  const isSearchMatch = Object(external_lodash_["flow"])([getNormalizedSearchTerm, normalizedCandidate => Object(external_lodash_["includes"])(normalizedCandidate, normalizedSearchTerm)]);
  return isSearchMatch(blockType.title) || Object(external_lodash_["some"])(blockType.keywords, isSearchMatch) || isSearchMatch(blockType.category);
}
/**
 * Returns a boolean indicating if a block has child blocks or not.
 *
 * @param {Object} state     Data state.
 * @param {string} blockName Block type name.
 *
 * @return {boolean} True if a block contains child blocks and false otherwise.
 */

const hasChildBlocks = (state, blockName) => {
  return getChildBlockNames(state, blockName).length > 0;
};
/**
 * Returns a boolean indicating if a block has at least one child block with inserter support.
 *
 * @param {Object} state     Data state.
 * @param {string} blockName Block type name.
 *
 * @return {boolean} True if a block contains at least one child blocks with inserter support
 *                   and false otherwise.
 */

const hasChildBlocksWithInserterSupport = (state, blockName) => {
  return Object(external_lodash_["some"])(getChildBlockNames(state, blockName), childBlockName => {
    return hasBlockSupport(state, childBlockName, 'inserter', true);
  });
};

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/store/actions.js
/**
 * External dependencies
 */

/** @typedef {import('../api/registration').WPBlockVariation} WPBlockVariation */

/**
 * Returns an action object used in signalling that block types have been added.
 *
 * @param {Array|Object} blockTypes Block types received.
 *
 * @return {Object} Action object.
 */

function addBlockTypes(blockTypes) {
  return {
    type: 'ADD_BLOCK_TYPES',
    blockTypes: Object(external_lodash_["castArray"])(blockTypes)
  };
}
/**
 * Returns an action object used to remove a registered block type.
 *
 * @param {string|Array} names Block name.
 *
 * @return {Object} Action object.
 */

function removeBlockTypes(names) {
  return {
    type: 'REMOVE_BLOCK_TYPES',
    names: Object(external_lodash_["castArray"])(names)
  };
}
/**
 * Returns an action object used in signalling that new block styles have been added.
 *
 * @param {string}       blockName  Block name.
 * @param {Array|Object} styles     Block styles.
 *
 * @return {Object} Action object.
 */

function addBlockStyles(blockName, styles) {
  return {
    type: 'ADD_BLOCK_STYLES',
    styles: Object(external_lodash_["castArray"])(styles),
    blockName
  };
}
/**
 * Returns an action object used in signalling that block styles have been removed.
 *
 * @param {string}       blockName  Block name.
 * @param {Array|string} styleNames Block style names.
 *
 * @return {Object} Action object.
 */

function removeBlockStyles(blockName, styleNames) {
  return {
    type: 'REMOVE_BLOCK_STYLES',
    styleNames: Object(external_lodash_["castArray"])(styleNames),
    blockName
  };
}
/**
 * Returns an action object used in signalling that new block variations have been added.
 *
 * @param {string}                              blockName  Block name.
 * @param {WPBlockVariation|WPBlockVariation[]} variations Block variations.
 *
 * @return {Object} Action object.
 */

function addBlockVariations(blockName, variations) {
  return {
    type: 'ADD_BLOCK_VARIATIONS',
    variations: Object(external_lodash_["castArray"])(variations),
    blockName
  };
}
/**
 * Returns an action object used in signalling that block variations have been removed.
 *
 * @param {string}          blockName      Block name.
 * @param {string|string[]} variationNames Block variation names.
 *
 * @return {Object} Action object.
 */

function removeBlockVariations(blockName, variationNames) {
  return {
    type: 'REMOVE_BLOCK_VARIATIONS',
    variationNames: Object(external_lodash_["castArray"])(variationNames),
    blockName
  };
}
/**
 * Returns an action object used to set the default block name.
 *
 * @param {string} name Block name.
 *
 * @return {Object} Action object.
 */

function setDefaultBlockName(name) {
  return {
    type: 'SET_DEFAULT_BLOCK_NAME',
    name
  };
}
/**
 * Returns an action object used to set the name of the block used as a fallback
 * for non-block content.
 *
 * @param {string} name Block name.
 *
 * @return {Object} Action object.
 */

function setFreeformFallbackBlockName(name) {
  return {
    type: 'SET_FREEFORM_FALLBACK_BLOCK_NAME',
    name
  };
}
/**
 * Returns an action object used to set the name of the block used as a fallback
 * for unregistered blocks.
 *
 * @param {string} name Block name.
 *
 * @return {Object} Action object.
 */

function setUnregisteredFallbackBlockName(name) {
  return {
    type: 'SET_UNREGISTERED_FALLBACK_BLOCK_NAME',
    name
  };
}
/**
 * Returns an action object used to set the name of the block used
 * when grouping other blocks
 * eg: in "Group/Ungroup" interactions
 *
 * @param {string} name Block name.
 *
 * @return {Object} Action object.
 */

function setGroupingBlockName(name) {
  return {
    type: 'SET_GROUPING_BLOCK_NAME',
    name
  };
}
/**
 * Returns an action object used to set block categories.
 *
 * @param {Object[]} categories Block categories.
 *
 * @return {Object} Action object.
 */

function setCategories(categories) {
  return {
    type: 'SET_CATEGORIES',
    categories
  };
}
/**
 * Returns an action object used to update a category.
 *
 * @param {string} slug     Block category slug.
 * @param {Object} category Object containing the category properties that should be updated.
 *
 * @return {Object} Action object.
 */

function updateCategory(slug, category) {
  return {
    type: 'UPDATE_CATEGORY',
    slug,
    category
  };
}
/**
 * Returns an action object used to add block collections
 *
 * @param {string} namespace       The namespace of the blocks to put in the collection
 * @param {string} title           The title to display in the block inserter
 * @param {Object} icon (optional) The icon to display in the block inserter
 *
 * @return {Object} Action object.
 */

function addBlockCollection(namespace, title, icon) {
  return {
    type: 'ADD_BLOCK_COLLECTION',
    namespace,
    title,
    icon
  };
}
/**
 * Returns an action object used to remove block collections
 *
 * @param {string} namespace       The namespace of the blocks to put in the collection
 *
 * @return {Object} Action object.
 */

function removeBlockCollection(namespace) {
  return {
    type: 'REMOVE_BLOCK_COLLECTION',
    namespace
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/store/constants.js
const STORE_NAME = 'core/blocks';

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Store definition for the blocks namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
});
Object(external_wp_data_["register"])(store);

// EXTERNAL MODULE: ./node_modules/uuid/dist/esm-browser/v4.js + 4 modules
var v4 = __webpack_require__("7Cbv");

// EXTERNAL MODULE: external ["wp","hooks"]
var external_wp_hooks_ = __webpack_require__("g56x");

// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__("NMb1");
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/block-default.js
var block_default = __webpack_require__("//Lo");

// EXTERNAL MODULE: ./node_modules/tinycolor2/tinycolor.js
var tinycolor = __webpack_require__("Zss7");
var tinycolor_default = /*#__PURE__*/__webpack_require__.n(tinycolor);

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","dom"]
var external_wp_dom_ = __webpack_require__("1CF3");

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/utils.js
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
 * Array of icon colors containing a color to be used if the icon color
 * was not explicitly set but the icon background color was.
 *
 * @type {Object}
 */

const ICON_COLORS = ['#191e23', '#f8f9f9'];
/**
 * Determines whether the block is a default block
 * and its attributes are equal to the default attributes
 * which means the block is unmodified.
 *
 * @param  {WPBlock} block Block Object
 *
 * @return {boolean}       Whether the block is an unmodified default block
 */

function isUnmodifiedDefaultBlock(block) {
  const defaultBlockName = registration_getDefaultBlockName();

  if (block.name !== defaultBlockName) {
    return false;
  } // Cache a created default block if no cache exists or the default block
  // name changed.


  if (!isUnmodifiedDefaultBlock.block || isUnmodifiedDefaultBlock.block.name !== defaultBlockName) {
    isUnmodifiedDefaultBlock.block = createBlock(defaultBlockName);
  }

  const newDefaultBlock = isUnmodifiedDefaultBlock.block;
  const blockType = registration_getBlockType(defaultBlockName);
  return Object(external_lodash_["every"])(blockType.attributes, (value, key) => newDefaultBlock.attributes[key] === block.attributes[key]);
}
/**
 * Function that checks if the parameter is a valid icon.
 *
 * @param {*} icon  Parameter to be checked.
 *
 * @return {boolean} True if the parameter is a valid icon and false otherwise.
 */

function isValidIcon(icon) {
  return !!icon && (Object(external_lodash_["isString"])(icon) || Object(external_wp_element_["isValidElement"])(icon) || Object(external_lodash_["isFunction"])(icon) || icon instanceof external_wp_element_["Component"]);
}
/**
 * Function that receives an icon as set by the blocks during the registration
 * and returns a new icon object that is normalized so we can rely on just on possible icon structure
 * in the codebase.
 *
 * @param {WPBlockTypeIconRender} icon Render behavior of a block type icon;
 *                                     one of a Dashicon slug, an element, or a
 *                                     component.
 *
 * @return {WPBlockTypeIconDescriptor} Object describing the icon.
 */

function normalizeIconObject(icon) {
  if (isValidIcon(icon)) {
    return {
      src: icon
    };
  }

  if (Object(external_lodash_["has"])(icon, ['background'])) {
    const tinyBgColor = tinycolor_default()(icon.background);
    return { ...icon,
      foreground: icon.foreground ? icon.foreground : Object(tinycolor["mostReadable"])(tinyBgColor, ICON_COLORS, {
        includeFallbackColors: true,
        level: 'AA',
        size: 'large'
      }).toHexString(),
      shadowColor: tinyBgColor.setAlpha(0.3).toRgbString()
    };
  }

  return icon;
}
/**
 * Normalizes block type passed as param. When string is passed then
 * it converts it to the matching block type object.
 * It passes the original object otherwise.
 *
 * @param {string|Object} blockTypeOrName  Block type or name.
 *
 * @return {?Object} Block type.
 */

function normalizeBlockType(blockTypeOrName) {
  if (Object(external_lodash_["isString"])(blockTypeOrName)) {
    return registration_getBlockType(blockTypeOrName);
  }

  return blockTypeOrName;
}
/**
 * Get the label for the block, usually this is either the block title,
 * or the value of the block's `label` function when that's specified.
 *
 * @param {Object} blockType  The block type.
 * @param {Object} attributes The values of the block's attributes.
 * @param {Object} context    The intended use for the label.
 *
 * @return {string} The block label.
 */

function getBlockLabel(blockType, attributes, context = 'visual') {
  const {
    __experimentalLabel: getLabel,
    title
  } = blockType;
  const label = getLabel && getLabel(attributes, {
    context
  });

  if (!label) {
    return title;
  } // Strip any HTML (i.e. RichText formatting) before returning.


  return Object(external_wp_dom_["__unstableStripHTML"])(label);
}
/**
 * Get a label for the block for use by screenreaders, this is more descriptive
 * than the visual label and includes the block title and the value of the
 * `getLabel` function if it's specified.
 *
 * @param {Object}  blockType              The block type.
 * @param {Object}  attributes             The values of the block's attributes.
 * @param {?number} position               The position of the block in the block list.
 * @param {string}  [direction='vertical'] The direction of the block layout.
 *
 * @return {string} The block label.
 */

function getAccessibleBlockLabel(blockType, attributes, position, direction = 'vertical') {
  // `title` is already localized, `label` is a user-supplied value.
  const {
    title
  } = blockType;
  const label = getBlockLabel(blockType, attributes, 'accessibility');
  const hasPosition = position !== undefined; // getBlockLabel returns the block title as a fallback when there's no label,
  // if it did return the title, this function needs to avoid adding the
  // title twice within the accessible label. Use this `hasLabel` boolean to
  // handle that.

  const hasLabel = label && label !== title;

  if (hasPosition && direction === 'vertical') {
    if (hasLabel) {
      return Object(external_wp_i18n_["sprintf"])(
      /* translators: accessibility text. 1: The block title. 2: The block row number. 3: The block label.. */
      Object(external_wp_i18n_["__"])('%1$s Block. Row %2$d. %3$s'), title, position, label);
    }

    return Object(external_wp_i18n_["sprintf"])(
    /* translators: accessibility text. 1: The block title. 2: The block row number. */
    Object(external_wp_i18n_["__"])('%1$s Block. Row %2$d'), title, position);
  } else if (hasPosition && direction === 'horizontal') {
    if (hasLabel) {
      return Object(external_wp_i18n_["sprintf"])(
      /* translators: accessibility text. 1: The block title. 2: The block column number. 3: The block label.. */
      Object(external_wp_i18n_["__"])('%1$s Block. Column %2$d. %3$s'), title, position, label);
    }

    return Object(external_wp_i18n_["sprintf"])(
    /* translators: accessibility text. 1: The block title. 2: The block column number. */
    Object(external_wp_i18n_["__"])('%1$s Block. Column %2$d'), title, position);
  }

  if (hasLabel) {
    return Object(external_wp_i18n_["sprintf"])(
    /* translators: accessibility text. %1: The block title. %2: The block label. */
    Object(external_wp_i18n_["__"])('%1$s Block. %2$s'), title, label);
  }

  return Object(external_wp_i18n_["sprintf"])(
  /* translators: accessibility text. %s: The block title. */
  Object(external_wp_i18n_["__"])('%s Block'), title);
}
/**
 * Ensure attributes contains only values defined by block type, and merge
 * default values for missing attributes.
 *
 * @param {string} name       The block's name.
 * @param {Object} attributes The block's attributes.
 * @return {Object} The sanitized attributes.
 */

function __experimentalSanitizeBlockAttributes(name, attributes) {
  // Get the type definition associated with a registered block.
  const blockType = registration_getBlockType(name);

  if (undefined === blockType) {
    throw new Error(`Block type '${name}' is not registered.`);
  }

  return Object(external_lodash_["reduce"])(blockType.attributes, (accumulator, schema, key) => {
    const value = attributes[key];

    if (undefined !== value) {
      accumulator[key] = value;
    } else if (schema.hasOwnProperty('default')) {
      accumulator[key] = schema.default;
    }

    if (['node', 'children'].indexOf(schema.source) !== -1) {
      // Ensure value passed is always an array, which we're expecting in
      // the RichText component to handle the deprecated value.
      if (typeof accumulator[key] === 'string') {
        accumulator[key] = [accumulator[key]];
      } else if (!Array.isArray(accumulator[key])) {
        accumulator[key] = [];
      }
    }

    return accumulator;
  }, {});
}
/**
 * Filter block attributes by `role` and return their names.
 *
 * @param {string} name Block attribute's name.
 * @param {string} role The role of a block attribute.
 *
 * @return {string[]} The attribute names that have the provided role.
 */

function __experimentalGetBlockAttributesNamesByRole(name, role) {
  var _getBlockType;

  const attributes = (_getBlockType = registration_getBlockType(name)) === null || _getBlockType === void 0 ? void 0 : _getBlockType.attributes;
  if (!attributes) return [];
  const attributesNames = Object.keys(attributes);
  if (!role) return attributesNames;
  return attributesNames.filter(attributeName => {
    var _attributes$attribute;

    return ((_attributes$attribute = attributes[attributeName]) === null || _attributes$attribute === void 0 ? void 0 : _attributes$attribute.__experimentalRole) === role;
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/constants.js
/**
 * Array of valid keys in a block type settings deprecation object.
 *
 * @type {string[]}
 */
const DEPRECATED_ENTRY_KEYS = ['attributes', 'supports', 'save', 'migrate', 'isEligible', 'apiVersion'];
const __EXPERIMENTAL_STYLE_PROPERTY = {
  //kept for back-compatibility purposes.
  '--wp--style--color--link': {
    value: ['color', 'link'],
    support: ['color', 'link']
  },
  background: {
    value: ['color', 'gradient'],
    support: ['color', 'gradients']
  },
  backgroundColor: {
    value: ['color', 'background'],
    support: ['color']
  },
  borderColor: {
    value: ['border', 'color'],
    support: ['__experimentalBorder', 'color']
  },
  borderRadius: {
    value: ['border', 'radius'],
    support: ['__experimentalBorder', 'radius']
  },
  borderStyle: {
    value: ['border', 'style'],
    support: ['__experimentalBorder', 'style']
  },
  borderWidth: {
    value: ['border', 'width'],
    support: ['__experimentalBorder', 'width']
  },
  color: {
    value: ['color', 'text'],
    support: ['color']
  },
  linkColor: {
    value: ['elements', 'link', 'color', 'text'],
    support: ['color', 'link']
  },
  fontFamily: {
    value: ['typography', 'fontFamily'],
    support: ['typography', '__experimentalFontFamily']
  },
  fontSize: {
    value: ['typography', 'fontSize'],
    support: ['typography', 'fontSize']
  },
  fontStyle: {
    value: ['typography', 'fontStyle'],
    support: ['typography', '__experimentalFontStyle']
  },
  fontWeight: {
    value: ['typography', 'fontWeight'],
    support: ['typography', '__experimentalFontWeight']
  },
  lineHeight: {
    value: ['typography', 'lineHeight'],
    support: ['typography', 'lineHeight']
  },
  margin: {
    value: ['spacing', 'margin'],
    support: ['spacing', 'margin'],
    properties: ['top', 'right', 'bottom', 'left']
  },
  padding: {
    value: ['spacing', 'padding'],
    support: ['spacing', 'padding'],
    properties: ['top', 'right', 'bottom', 'left']
  },
  textDecoration: {
    value: ['typography', 'textDecoration'],
    support: ['typography', '__experimentalTextDecoration']
  },
  textTransform: {
    value: ['typography', 'textTransform'],
    support: ['typography', '__experimentalTextTransform']
  }
};
const __EXPERIMENTAL_ELEMENTS = {
  link: 'a',
  h1: 'h1',
  h2: 'h2',
  h3: 'h3',
  h4: 'h4',
  h5: 'h5',
  h6: 'h6'
};

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/registration.js
/* eslint no-console: [ 'error', { allow: [ 'error', 'warn' ] } ] */

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */

const i18nBlockSchema = {
  title: "block title",
  description: "block description",
  keywords: ["block keyword"],
  styles: [{
    label: "block style label"
  }],
  variations: [{
    title: "block variation title",
    description: "block variation description",
    keywords: ["block variation keyword"]
  }]
};



/**
 * An icon type definition. One of a Dashicon slug, an element,
 * or a component.
 *
 * @typedef {(string|WPElement|WPComponent)} WPIcon
 *
 * @see https://developer.wordpress.org/resource/dashicons/
 */

/**
 * Render behavior of a block type icon; one of a Dashicon slug, an element,
 * or a component.
 *
 * @typedef {WPIcon} WPBlockTypeIconRender
 */

/**
 * An object describing a normalized block type icon.
 *
 * @typedef {Object} WPBlockTypeIconDescriptor
 *
 * @property {WPBlockTypeIconRender} src         Render behavior of the icon,
 *                                               one of a Dashicon slug, an
 *                                               element, or a component.
 * @property {string}                background  Optimal background hex string
 *                                               color when displaying icon.
 * @property {string}                foreground  Optimal foreground hex string
 *                                               color when displaying icon.
 * @property {string}                shadowColor Optimal shadow hex string
 *                                               color when displaying icon.
 */

/**
 * Value to use to render the icon for a block type in an editor interface,
 * either a Dashicon slug, an element, a component, or an object describing
 * the icon.
 *
 * @typedef {(WPBlockTypeIconDescriptor|WPBlockTypeIconRender)} WPBlockTypeIcon
 */

/**
 * Named block variation scopes.
 *
 * @typedef {'block'|'inserter'|'transform'} WPBlockVariationScope
 */

/**
 * An object describing a variation defined for the block type.
 *
 * @typedef {Object} WPBlockVariation
 *
 * @property {string}   name                   The unique and machine-readable name.
 * @property {string}   title                  A human-readable variation title.
 * @property {string}   [description]          A detailed variation description.
 * @property {string}   [category]             Block type category classification,
 *                                             used in search interfaces to arrange
 *                                             block types by category.
 * @property {WPIcon}   [icon]                 An icon helping to visualize the variation.
 * @property {boolean}  [isDefault]            Indicates whether the current variation is
 *                                             the default one. Defaults to `false`.
 * @property {Object}   [attributes]           Values which override block attributes.
 * @property {Array[]}  [innerBlocks]          Initial configuration of nested blocks.
 * @property {Object}   [example]              Example provides structured data for
 *                                             the block preview. You can set to
 *                                             `undefined` to disable the preview shown
 *                                             for the block type.
 * @property {WPBlockVariationScope[]} [scope] The list of scopes where the variation
 *                                             is applicable. When not provided, it
 *                                             assumes all available scopes.
 * @property {string[]} [keywords]             An array of terms (which can be translated)
 *                                             that help users discover the variation
 *                                             while searching.
 * @property {Function|string[]} [isActive]    This can be a function or an array of block attributes.
 *                                             Function that accepts a block's attributes and the
 *                                             variation's attributes and determines if a variation is active.
 *                                             This function doesn't try to find a match dynamically based
 *                                             on all block's attributes, as in many cases some attributes are irrelevant.
 *                                             An example would be for `embed` block where we only care
 *                                             about `providerNameSlug` attribute's value.
 *                                             We can also use a `string[]` to tell which attributes
 *                                             should be compared as a shorthand. Each attributes will
 *                                             be matched and the variation will be active if all of them are matching.
 */

/**
 * Defined behavior of a block type.
 *
 * @typedef {Object} WPBlock
 *
 * @property {string}             name          Block type's namespaced name.
 * @property {string}             title         Human-readable block type label.
 * @property {string}             [description] A detailed block type description.
 * @property {string}             [category]    Block type category classification,
 *                                              used in search interfaces to arrange
 *                                              block types by category.
 * @property {WPBlockTypeIcon}    [icon]        Block type icon.
 * @property {string[]}           [keywords]    Additional keywords to produce block
 *                                              type as result in search interfaces.
 * @property {Object}             [attributes]  Block type attributes.
 * @property {WPComponent}        [save]        Optional component describing
 *                                              serialized markup structure of a
 *                                              block type.
 * @property {WPComponent}        edit          Component rendering an element to
 *                                              manipulate the attributes of a block
 *                                              in the context of an editor.
 * @property {WPBlockVariation[]} [variations]  The list of block variations.
 * @property {Object}             [example]     Example provides structured data for
 *                                              the block preview. When not defined
 *                                              then no preview is shown.
 */

/**
 * Mapping of legacy category slugs to their latest normal values, used to
 * accommodate updates of the default set of block categories.
 *
 * @type {Record<string,string>}
 */

const LEGACY_CATEGORY_MAPPING = {
  common: 'text',
  formatting: 'text',
  layout: 'design'
};
const serverSideBlockDefinitions = {};
/**
 * Sets the server side block definition of blocks.
 *
 * @param {Object} definitions Server-side block definitions
 */
// eslint-disable-next-line camelcase

function unstable__bootstrapServerSideBlockDefinitions(definitions) {
  for (const blockName of Object.keys(definitions)) {
    // Don't overwrite if already set. It covers the case when metadata
    // was initialized from the server.
    if (serverSideBlockDefinitions[blockName]) {
      // We still need to polyfill `apiVersion` for WordPress version
      // lower than 5.7. If it isn't present in the definition shared
      // from the server, we try to fallback to the definition passed.
      // @see https://github.com/WordPress/gutenberg/pull/29279
      if (serverSideBlockDefinitions[blockName].apiVersion === undefined && definitions[blockName].apiVersion) {
        serverSideBlockDefinitions[blockName].apiVersion = definitions[blockName].apiVersion;
      }

      continue;
    }

    serverSideBlockDefinitions[blockName] = Object(external_lodash_["mapKeys"])(Object(external_lodash_["pickBy"])(definitions[blockName], value => !Object(external_lodash_["isNil"])(value)), (value, key) => Object(external_lodash_["camelCase"])(key));
  }
}
/**
 * Gets block settings from metadata loaded from `block.json` file.
 *
 * @param {Object} metadata            Block metadata loaded from `block.json`.
 * @param {string} metadata.textdomain Textdomain to use with translations.
 *
 * @return {Object} Block settings.
 */

function getBlockSettingsFromMetadata({
  textdomain,
  ...metadata
}) {
  const allowedFields = ['apiVersion', 'title', 'category', 'parent', 'icon', 'description', 'keywords', 'attributes', 'providesContext', 'usesContext', 'supports', 'styles', 'example', 'variations'];
  const settings = Object(external_lodash_["pick"])(metadata, allowedFields);

  if (textdomain) {
    Object.keys(i18nBlockSchema).forEach(key => {
      if (!settings[key]) {
        return;
      }

      settings[key] = translateBlockSettingUsingI18nSchema(i18nBlockSchema[key], settings[key], textdomain);
    });
  }

  return settings;
}
/**
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made available as an option to any
 * editor interface where blocks are implemented.
 *
 * @param {string|Object} blockNameOrMetadata Block type name or its metadata.
 * @param {Object}        settings            Block settings.
 *
 * @return {?WPBlock} The block, if it has been successfully registered;
 *                    otherwise `undefined`.
 */


function registerBlockType(blockNameOrMetadata, settings) {
  const name = Object(external_lodash_["isObject"])(blockNameOrMetadata) ? blockNameOrMetadata.name : blockNameOrMetadata;

  if (typeof name !== 'string') {
    console.error('Block names must be strings.');
    return;
  }

  if (Object(external_lodash_["isObject"])(blockNameOrMetadata)) {
    unstable__bootstrapServerSideBlockDefinitions({
      [name]: getBlockSettingsFromMetadata(blockNameOrMetadata)
    });
  }

  settings = {
    name,
    icon: block_default["a" /* default */],
    keywords: [],
    attributes: {},
    providesContext: {},
    usesContext: [],
    supports: {},
    styles: [],
    save: () => null,
    ...(serverSideBlockDefinitions === null || serverSideBlockDefinitions === void 0 ? void 0 : serverSideBlockDefinitions[name]),
    ...settings
  };

  if (!/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/.test(name)) {
    console.error('Block names must contain a namespace prefix, include only lowercase alphanumeric characters or dashes, and start with a letter. Example: my-plugin/my-custom-block');
    return;
  }

  if (Object(external_wp_data_["select"])(store).getBlockType(name)) {
    console.error('Block "' + name + '" is already registered.');
    return;
  }

  const preFilterSettings = { ...settings
  };
  settings = Object(external_wp_hooks_["applyFilters"])('blocks.registerBlockType', settings, name);

  if (settings.deprecated) {
    settings.deprecated = settings.deprecated.map(deprecation => Object(external_lodash_["pick"])( // Only keep valid deprecation keys.
    Object(external_wp_hooks_["applyFilters"])('blocks.registerBlockType', // Merge deprecation keys with pre-filter settings
    // so that filters that depend on specific keys being
    // present don't fail.
    { // Omit deprecation keys here so that deprecations
      // can opt out of specific keys like "supports".
      ...Object(external_lodash_["omit"])(preFilterSettings, DEPRECATED_ENTRY_KEYS),
      ...deprecation
    }, name), DEPRECATED_ENTRY_KEYS));
  }

  if (!Object(external_lodash_["isPlainObject"])(settings)) {
    console.error('Block settings must be a valid object.');
    return;
  }

  if (!Object(external_lodash_["isFunction"])(settings.save)) {
    console.error('The "save" property must be a valid function.');
    return;
  }

  if ('edit' in settings && !Object(external_lodash_["isFunction"])(settings.edit)) {
    console.error('The "edit" property must be a valid function.');
    return;
  } // Canonicalize legacy categories to equivalent fallback.


  if (LEGACY_CATEGORY_MAPPING.hasOwnProperty(settings.category)) {
    settings.category = LEGACY_CATEGORY_MAPPING[settings.category];
  }

  if ('category' in settings && !Object(external_lodash_["some"])(Object(external_wp_data_["select"])(store).getCategories(), {
    slug: settings.category
  })) {
    console.warn('The block "' + name + '" is registered with an invalid category "' + settings.category + '".');
    delete settings.category;
  }

  if (!('title' in settings) || settings.title === '') {
    console.error('The block "' + name + '" must have a title.');
    return;
  }

  if (typeof settings.title !== 'string') {
    console.error('Block titles must be strings.');
    return;
  }

  settings.icon = normalizeIconObject(settings.icon);

  if (!isValidIcon(settings.icon.src)) {
    console.error('The icon passed is invalid. ' + 'The icon should be a string, an element, a function, or an object following the specifications documented in https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#icon-optional');
    return;
  }

  Object(external_wp_data_["dispatch"])(store).addBlockTypes(settings);
  return settings;
}
/**
 * Translates block settings provided with metadata using the i18n schema.
 *
 * @param {string|string[]|Object[]} i18nSchema    I18n schema for the block setting.
 * @param {string|string[]|Object[]} settingValue  Value for the block setting.
 * @param {string}                   textdomain    Textdomain to use with translations.
 *
 * @return {string|string[]|Object[]} Translated setting.
 */

function translateBlockSettingUsingI18nSchema(i18nSchema, settingValue, textdomain) {
  if (Object(external_lodash_["isString"])(i18nSchema) && Object(external_lodash_["isString"])(settingValue)) {
    // eslint-disable-next-line @wordpress/i18n-no-variables, @wordpress/i18n-text-domain
    return Object(external_wp_i18n_["_x"])(settingValue, i18nSchema, textdomain);
  }

  if (Object(external_lodash_["isArray"])(i18nSchema) && !Object(external_lodash_["isEmpty"])(i18nSchema) && Object(external_lodash_["isArray"])(settingValue)) {
    return settingValue.map(value => translateBlockSettingUsingI18nSchema(i18nSchema[0], value, textdomain));
  }

  if (Object(external_lodash_["isObject"])(i18nSchema) && !Object(external_lodash_["isEmpty"])(i18nSchema) && Object(external_lodash_["isObject"])(settingValue)) {
    return Object.keys(settingValue).reduce((accumulator, key) => {
      if (!i18nSchema[key]) {
        accumulator[key] = settingValue[key];
        return accumulator;
      }

      accumulator[key] = translateBlockSettingUsingI18nSchema(i18nSchema[key], settingValue[key], textdomain);
      return accumulator;
    }, {});
  }

  return settingValue;
}
/**
 * Registers a new block provided from metadata stored in `block.json` file.
 *
 * @deprecated Use `registerBlockType` instead.
 *
 * @param {Object} metadata            Block metadata loaded from `block.json`.
 * @param {Object} additionalSettings  Additional block settings.
 *
 * @return {?WPBlock} The block, if it has been successfully registered;
 *                    otherwise `undefined`.
 */


function registerBlockTypeFromMetadata(metadata, additionalSettings) {
  external_wp_deprecated_default()('wp.blocks.registerBlockTypeFromMetadata', {
    since: '10.7',
    plugin: 'Gutenberg',
    alternative: 'wp.blocks.registerBlockType',
    version: '11.0'
  });
  return registerBlockType(metadata, additionalSettings);
}
/**
 * Registers a new block collection to group blocks in the same namespace in the inserter.
 *
 * @param {string} namespace       The namespace to group blocks by in the inserter; corresponds to the block namespace.
 * @param {Object} settings        The block collection settings.
 * @param {string} settings.title  The title to display in the block inserter.
 * @param {Object} [settings.icon] The icon to display in the block inserter.
 */

function registerBlockCollection(namespace, {
  title,
  icon
}) {
  Object(external_wp_data_["dispatch"])(store).addBlockCollection(namespace, title, icon);
}
/**
 * Unregisters a block collection
 *
 * @param {string} namespace The namespace to group blocks by in the inserter; corresponds to the block namespace
 *
 */

function unregisterBlockCollection(namespace) {
  Object(external_wp_data_["dispatch"])(store).removeBlockCollection(namespace);
}
/**
 * Unregisters a block.
 *
 * @param {string} name Block name.
 *
 * @return {?WPBlock} The previous block value, if it has been successfully
 *                    unregistered; otherwise `undefined`.
 */

function unregisterBlockType(name) {
  const oldBlock = Object(external_wp_data_["select"])(store).getBlockType(name);

  if (!oldBlock) {
    console.error('Block "' + name + '" is not registered.');
    return;
  }

  Object(external_wp_data_["dispatch"])(store).removeBlockTypes(name);
  return oldBlock;
}
/**
 * Assigns name of block for handling non-block content.
 *
 * @param {string} blockName Block name.
 */

function setFreeformContentHandlerName(blockName) {
  Object(external_wp_data_["dispatch"])(store).setFreeformFallbackBlockName(blockName);
}
/**
 * Retrieves name of block handling non-block content, or undefined if no
 * handler has been defined.
 *
 * @return {?string} Block name.
 */

function getFreeformContentHandlerName() {
  return Object(external_wp_data_["select"])(store).getFreeformFallbackBlockName();
}
/**
 * Retrieves name of block used for handling grouping interactions.
 *
 * @return {?string} Block name.
 */

function registration_getGroupingBlockName() {
  return Object(external_wp_data_["select"])(store).getGroupingBlockName();
}
/**
 * Assigns name of block handling unregistered block types.
 *
 * @param {string} blockName Block name.
 */

function setUnregisteredTypeHandlerName(blockName) {
  Object(external_wp_data_["dispatch"])(store).setUnregisteredFallbackBlockName(blockName);
}
/**
 * Retrieves name of block handling unregistered block types, or undefined if no
 * handler has been defined.
 *
 * @return {?string} Block name.
 */

function getUnregisteredTypeHandlerName() {
  return Object(external_wp_data_["select"])(store).getUnregisteredFallbackBlockName();
}
/**
 * Assigns the default block name.
 *
 * @param {string} name Block name.
 */

function registration_setDefaultBlockName(name) {
  Object(external_wp_data_["dispatch"])(store).setDefaultBlockName(name);
}
/**
 * Assigns name of block for handling block grouping interactions.
 *
 * @param {string} name Block name.
 */

function registration_setGroupingBlockName(name) {
  Object(external_wp_data_["dispatch"])(store).setGroupingBlockName(name);
}
/**
 * Retrieves the default block name.
 *
 * @return {?string} Block name.
 */

function registration_getDefaultBlockName() {
  return Object(external_wp_data_["select"])(store).getDefaultBlockName();
}
/**
 * Returns a registered block type.
 *
 * @param {string} name Block name.
 *
 * @return {?Object} Block type.
 */

function registration_getBlockType(name) {
  return Object(external_wp_data_["select"])(store).getBlockType(name);
}
/**
 * Returns all registered blocks.
 *
 * @return {Array} Block settings.
 */

function registration_getBlockTypes() {
  return Object(external_wp_data_["select"])(store).getBlockTypes();
}
/**
 * Returns the block support value for a feature, if defined.
 *
 * @param  {(string|Object)} nameOrType      Block name or type object
 * @param  {string}          feature         Feature to retrieve
 * @param  {*}               defaultSupports Default value to return if not
 *                                           explicitly defined
 *
 * @return {?*} Block support value
 */

function registration_getBlockSupport(nameOrType, feature, defaultSupports) {
  return Object(external_wp_data_["select"])(store).getBlockSupport(nameOrType, feature, defaultSupports);
}
/**
 * Returns true if the block defines support for a feature, or false otherwise.
 *
 * @param {(string|Object)} nameOrType      Block name or type object.
 * @param {string}          feature         Feature to test.
 * @param {boolean}         defaultSupports Whether feature is supported by
 *                                          default if not explicitly defined.
 *
 * @return {boolean} Whether block supports feature.
 */

function registration_hasBlockSupport(nameOrType, feature, defaultSupports) {
  return Object(external_wp_data_["select"])(store).hasBlockSupport(nameOrType, feature, defaultSupports);
}
/**
 * Determines whether or not the given block is a reusable block. This is a
 * special block type that is used to point to a global block stored via the
 * API.
 *
 * @param {Object} blockOrType Block or Block Type to test.
 *
 * @return {boolean} Whether the given block is a reusable block.
 */

function isReusableBlock(blockOrType) {
  return blockOrType.name === 'core/block';
}
/**
 * Determines whether or not the given block is a template part. This is a
 * special block type that allows composing a page template out of reusable
 * design elements.
 *
 * @param {Object} blockOrType Block or Block Type to test.
 *
 * @return {boolean} Whether the given block is a template part.
 */

function isTemplatePart(blockOrType) {
  return blockOrType.name === 'core/template-part';
}
/**
 * Returns an array with the child blocks of a given block.
 *
 * @param {string} blockName Name of block (example: “latest-posts”).
 *
 * @return {Array} Array of child block names.
 */

const registration_getChildBlockNames = blockName => {
  return Object(external_wp_data_["select"])(store).getChildBlockNames(blockName);
};
/**
 * Returns a boolean indicating if a block has child blocks or not.
 *
 * @param {string} blockName Name of block (example: “latest-posts”).
 *
 * @return {boolean} True if a block contains child blocks and false otherwise.
 */

const registration_hasChildBlocks = blockName => {
  return Object(external_wp_data_["select"])(store).hasChildBlocks(blockName);
};
/**
 * Returns a boolean indicating if a block has at least one child block with inserter support.
 *
 * @param {string} blockName Block type name.
 *
 * @return {boolean} True if a block contains at least one child blocks with inserter support
 *                   and false otherwise.
 */

const registration_hasChildBlocksWithInserterSupport = blockName => {
  return Object(external_wp_data_["select"])(store).hasChildBlocksWithInserterSupport(blockName);
};
/**
 * Registers a new block style variation for the given block.
 *
 * @param {string} blockName      Name of block (example: “core/latest-posts”).
 * @param {Object} styleVariation Object containing `name` which is the class name applied to the block and `label` which identifies the variation to the user.
 */

const registerBlockStyle = (blockName, styleVariation) => {
  Object(external_wp_data_["dispatch"])(store).addBlockStyles(blockName, styleVariation);
};
/**
 * Unregisters a block style variation for the given block.
 *
 * @param {string} blockName          Name of block (example: “core/latest-posts”).
 * @param {string} styleVariationName Name of class applied to the block.
 */

const unregisterBlockStyle = (blockName, styleVariationName) => {
  Object(external_wp_data_["dispatch"])(store).removeBlockStyles(blockName, styleVariationName);
};
/**
 * Returns an array with the variations of a given block type.
 *
 * @param {string}                blockName Name of block (example: “core/columns”).
 * @param {WPBlockVariationScope} [scope]   Block variation scope name.
 *
 * @return {(WPBlockVariation[]|void)} Block variations.
 */

const registration_getBlockVariations = (blockName, scope) => {
  return Object(external_wp_data_["select"])(store).getBlockVariations(blockName, scope);
};
/**
 * Registers a new block variation for the given block type.
 *
 * @param {string}           blockName Name of the block (example: “core/columns”).
 * @param {WPBlockVariation} variation Object describing a block variation.
 */

const registerBlockVariation = (blockName, variation) => {
  Object(external_wp_data_["dispatch"])(store).addBlockVariations(blockName, variation);
};
/**
 * Unregisters a block variation defined for the given block type.
 *
 * @param {string} blockName     Name of the block (example: “core/columns”).
 * @param {string} variationName Name of the variation defined for the block.
 */

const unregisterBlockVariation = (blockName, variationName) => {
  Object(external_wp_data_["dispatch"])(store).removeBlockVariations(blockName, variationName);
};

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/factory.js
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
 * Returns a block object given its type and attributes.
 *
 * @param {string} name        Block name.
 * @param {Object} attributes  Block attributes.
 * @param {?Array} innerBlocks Nested blocks.
 *
 * @return {Object} Block object.
 */

function createBlock(name, attributes = {}, innerBlocks = []) {
  const sanitizedAttributes = __experimentalSanitizeBlockAttributes(name, attributes);

  const clientId = Object(v4["a" /* default */])(); // Blocks are stored with a unique ID, the assigned type name, the block
  // attributes, and their inner blocks.

  return {
    clientId,
    name,
    isValid: true,
    attributes: sanitizedAttributes,
    innerBlocks
  };
}
/**
 * Given an array of InnerBlocks templates or Block Objects,
 * returns an array of created Blocks from them.
 * It handles the case of having InnerBlocks as Blocks by
 * converting them to the proper format to continue recursively.
 *
 * @param {Array} innerBlocksOrTemplate Nested blocks or InnerBlocks templates.
 *
 * @return {Object[]} Array of Block objects.
 */

function createBlocksFromInnerBlocksTemplate(innerBlocksOrTemplate = []) {
  return innerBlocksOrTemplate.map(innerBlock => {
    const innerBlockTemplate = Array.isArray(innerBlock) ? innerBlock : [innerBlock.name, innerBlock.attributes, innerBlock.innerBlocks];
    const [name, attributes, innerBlocks = []] = innerBlockTemplate;
    return createBlock(name, attributes, createBlocksFromInnerBlocksTemplate(innerBlocks));
  });
}
/**
 * Given a block object, returns a copy of the block object while sanitizing its attributes,
 * optionally merging new attributes and/or replacing its inner blocks.
 *
 * @param {Object} block              Block instance.
 * @param {Object} mergeAttributes    Block attributes.
 * @param {?Array} newInnerBlocks     Nested blocks.
 *
 * @return {Object} A cloned block.
 */

function __experimentalCloneSanitizedBlock(block, mergeAttributes = {}, newInnerBlocks) {
  const clientId = Object(v4["a" /* default */])();

  const sanitizedAttributes = __experimentalSanitizeBlockAttributes(block.name, { ...block.attributes,
    ...mergeAttributes
  });

  return { ...block,
    clientId,
    attributes: sanitizedAttributes,
    innerBlocks: newInnerBlocks || block.innerBlocks.map(innerBlock => __experimentalCloneSanitizedBlock(innerBlock))
  };
}
/**
 * Given a block object, returns a copy of the block object,
 * optionally merging new attributes and/or replacing its inner blocks.
 *
 * @param {Object} block              Block instance.
 * @param {Object} mergeAttributes    Block attributes.
 * @param {?Array} newInnerBlocks     Nested blocks.
 *
 * @return {Object} A cloned block.
 */

function cloneBlock(block, mergeAttributes = {}, newInnerBlocks) {
  const clientId = Object(v4["a" /* default */])();
  return { ...block,
    clientId,
    attributes: { ...block.attributes,
      ...mergeAttributes
    },
    innerBlocks: newInnerBlocks || block.innerBlocks.map(innerBlock => cloneBlock(innerBlock))
  };
}
/**
 * Returns a boolean indicating whether a transform is possible based on
 * various bits of context.
 *
 * @param {Object} transform The transform object to validate.
 * @param {string} direction Is this a 'from' or 'to' transform.
 * @param {Array} blocks The blocks to transform from.
 *
 * @return {boolean} Is the transform possible?
 */

const isPossibleTransformForSource = (transform, direction, blocks) => {
  if (Object(external_lodash_["isEmpty"])(blocks)) {
    return false;
  } // If multiple blocks are selected, only multi block transforms
  // or wildcard transforms are allowed.


  const isMultiBlock = blocks.length > 1;
  const firstBlockName = Object(external_lodash_["first"])(blocks).name;
  const isValidForMultiBlocks = isWildcardBlockTransform(transform) || !isMultiBlock || transform.isMultiBlock;

  if (!isValidForMultiBlocks) {
    return false;
  } // Check non-wildcard transforms to ensure that transform is valid
  // for a block selection of multiple blocks of different types


  if (!isWildcardBlockTransform(transform) && !Object(external_lodash_["every"])(blocks, {
    name: firstBlockName
  })) {
    return false;
  } // Only consider 'block' type transforms as valid.


  const isBlockType = transform.type === 'block';

  if (!isBlockType) {
    return false;
  } // Check if the transform's block name matches the source block (or is a wildcard)
  // only if this is a transform 'from'.


  const sourceBlock = Object(external_lodash_["first"])(blocks);
  const hasMatchingName = direction !== 'from' || transform.blocks.indexOf(sourceBlock.name) !== -1 || isWildcardBlockTransform(transform);

  if (!hasMatchingName) {
    return false;
  } // Don't allow single Grouping blocks to be transformed into
  // a Grouping block.


  if (!isMultiBlock && isContainerGroupBlock(sourceBlock.name) && isContainerGroupBlock(transform.blockName)) {
    return false;
  } // If the transform has a `isMatch` function specified, check that it returns true.


  if (Object(external_lodash_["isFunction"])(transform.isMatch)) {
    const attributes = transform.isMultiBlock ? blocks.map(block => block.attributes) : sourceBlock.attributes;

    if (!transform.isMatch(attributes)) {
      return false;
    }
  }

  if (transform.usingMobileTransformations && isWildcardBlockTransform(transform) && !isContainerGroupBlock(sourceBlock.name)) {
    return false;
  }

  return true;
};
/**
 * Returns block types that the 'blocks' can be transformed into, based on
 * 'from' transforms on other blocks.
 *
 * @param {Array}  blocks  The blocks to transform from.
 *
 * @return {Array} Block types that the blocks can be transformed into.
 */


const getBlockTypesForPossibleFromTransforms = blocks => {
  if (Object(external_lodash_["isEmpty"])(blocks)) {
    return [];
  }

  const allBlockTypes = registration_getBlockTypes(); // filter all blocks to find those with a 'from' transform.

  const blockTypesWithPossibleFromTransforms = Object(external_lodash_["filter"])(allBlockTypes, blockType => {
    const fromTransforms = getBlockTransforms('from', blockType.name);
    return !!findTransform(fromTransforms, transform => {
      return isPossibleTransformForSource(transform, 'from', blocks);
    });
  });
  return blockTypesWithPossibleFromTransforms;
};
/**
 * Returns block types that the 'blocks' can be transformed into, based on
 * the source block's own 'to' transforms.
 *
 * @param {Array} blocks The blocks to transform from.
 *
 * @return {Array} Block types that the source can be transformed into.
 */


const getBlockTypesForPossibleToTransforms = blocks => {
  if (Object(external_lodash_["isEmpty"])(blocks)) {
    return [];
  }

  const sourceBlock = Object(external_lodash_["first"])(blocks);
  const blockType = registration_getBlockType(sourceBlock.name);
  const transformsTo = getBlockTransforms('to', blockType.name); // filter all 'to' transforms to find those that are possible.

  const possibleTransforms = Object(external_lodash_["filter"])(transformsTo, transform => {
    return transform && isPossibleTransformForSource(transform, 'to', blocks);
  }); // Build a list of block names using the possible 'to' transforms.

  const blockNames = Object(external_lodash_["flatMap"])(possibleTransforms, transformation => transformation.blocks); // Map block names to block types.

  return blockNames.map(name => registration_getBlockType(name));
};
/**
 * Determines whether transform is a "block" type
 * and if so whether it is a "wildcard" transform
 * ie: targets "any" block type
 *
 * @param {Object} t the Block transform object
 *
 * @return {boolean} whether transform is a wildcard transform
 */


const isWildcardBlockTransform = t => t && t.type === 'block' && Array.isArray(t.blocks) && t.blocks.includes('*');
/**
 * Determines whether the given Block is the core Block which
 * acts as a container Block for other Blocks as part of the
 * Grouping mechanics
 *
 * @param  {string} name the name of the Block to test against
 *
 * @return {boolean} whether or not the Block is the container Block type
 */

const isContainerGroupBlock = name => name === registration_getGroupingBlockName();
/**
 * Returns an array of block types that the set of blocks received as argument
 * can be transformed into.
 *
 * @param {Array} blocks Blocks array.
 *
 * @return {Array} Block types that the blocks argument can be transformed to.
 */

function getPossibleBlockTransformations(blocks) {
  if (Object(external_lodash_["isEmpty"])(blocks)) {
    return [];
  }

  const blockTypesForFromTransforms = getBlockTypesForPossibleFromTransforms(blocks);
  const blockTypesForToTransforms = getBlockTypesForPossibleToTransforms(blocks);
  return Object(external_lodash_["uniq"])([...blockTypesForFromTransforms, ...blockTypesForToTransforms]);
}
/**
 * Given an array of transforms, returns the highest-priority transform where
 * the predicate function returns a truthy value. A higher-priority transform
 * is one with a lower priority value (i.e. first in priority order). Returns
 * null if the transforms set is empty or the predicate function returns a
 * falsey value for all entries.
 *
 * @param {Object[]} transforms Transforms to search.
 * @param {Function} predicate  Function returning true on matching transform.
 *
 * @return {?Object} Highest-priority transform candidate.
 */

function findTransform(transforms, predicate) {
  // The hooks library already has built-in mechanisms for managing priority
  // queue, so leverage via locally-defined instance.
  const hooks = Object(external_wp_hooks_["createHooks"])();

  for (let i = 0; i < transforms.length; i++) {
    const candidate = transforms[i];

    if (predicate(candidate)) {
      hooks.addFilter('transform', 'transform/' + i.toString(), result => result ? result : candidate, candidate.priority);
    }
  } // Filter name is arbitrarily chosen but consistent with above aggregation.


  return hooks.applyFilters('transform', null);
}
/**
 * Returns normal block transforms for a given transform direction, optionally
 * for a specific block by name, or an empty array if there are no transforms.
 * If no block name is provided, returns transforms for all blocks. A normal
 * transform object includes `blockName` as a property.
 *
 * @param {string}  direction Transform direction ("to", "from").
 * @param {string|Object} blockTypeOrName  Block type or name.
 *
 * @return {Array} Block transforms for direction.
 */

function getBlockTransforms(direction, blockTypeOrName) {
  // When retrieving transforms for all block types, recurse into self.
  if (blockTypeOrName === undefined) {
    return Object(external_lodash_["flatMap"])(registration_getBlockTypes(), ({
      name
    }) => getBlockTransforms(direction, name));
  } // Validate that block type exists and has array of direction.


  const blockType = normalizeBlockType(blockTypeOrName);
  const {
    name: blockName,
    transforms
  } = blockType || {};

  if (!transforms || !Array.isArray(transforms[direction])) {
    return [];
  }

  const usingMobileTransformations = transforms.supportedMobileTransforms && Array.isArray(transforms.supportedMobileTransforms);
  const filteredTransforms = usingMobileTransformations ? Object(external_lodash_["filter"])(transforms[direction], t => {
    if (t.type === 'raw') {
      return true;
    }

    if (!t.blocks || !t.blocks.length) {
      return false;
    }

    if (isWildcardBlockTransform(t)) {
      return true;
    }

    return Object(external_lodash_["every"])(t.blocks, transformBlockName => transforms.supportedMobileTransforms.includes(transformBlockName));
  }) : transforms[direction]; // Map transforms to normal form.

  return filteredTransforms.map(transform => ({ ...transform,
    blockName,
    usingMobileTransformations
  }));
}
/**
 * Switch one or more blocks into one or more blocks of the new block type.
 *
 * @param {Array|Object} blocks Blocks array or block object.
 * @param {string}       name   Block name.
 *
 * @return {?Array} Array of blocks or null.
 */

function switchToBlockType(blocks, name) {
  const blocksArray = Object(external_lodash_["castArray"])(blocks);
  const isMultiBlock = blocksArray.length > 1;
  const firstBlock = blocksArray[0];
  const sourceName = firstBlock.name; // Find the right transformation by giving priority to the "to"
  // transformation.

  const transformationsFrom = getBlockTransforms('from', name);
  const transformationsTo = getBlockTransforms('to', sourceName);
  const transformation = findTransform(transformationsTo, t => t.type === 'block' && (isWildcardBlockTransform(t) || t.blocks.indexOf(name) !== -1) && (!isMultiBlock || t.isMultiBlock)) || findTransform(transformationsFrom, t => t.type === 'block' && (isWildcardBlockTransform(t) || t.blocks.indexOf(sourceName) !== -1) && (!isMultiBlock || t.isMultiBlock)); // Stop if there is no valid transformation.

  if (!transformation) {
    return null;
  }

  let transformationResults;

  if (transformation.isMultiBlock) {
    if (Object(external_lodash_["has"])(transformation, '__experimentalConvert')) {
      transformationResults = transformation.__experimentalConvert(blocksArray);
    } else {
      transformationResults = transformation.transform(blocksArray.map(currentBlock => currentBlock.attributes), blocksArray.map(currentBlock => currentBlock.innerBlocks));
    }
  } else if (Object(external_lodash_["has"])(transformation, '__experimentalConvert')) {
    transformationResults = transformation.__experimentalConvert(firstBlock);
  } else {
    transformationResults = transformation.transform(firstBlock.attributes, firstBlock.innerBlocks);
  } // Ensure that the transformation function returned an object or an array
  // of objects.


  if (!Object(external_lodash_["isObjectLike"])(transformationResults)) {
    return null;
  } // If the transformation function returned a single object, we want to work
  // with an array instead.


  transformationResults = Object(external_lodash_["castArray"])(transformationResults); // Ensure that every block object returned by the transformation has a
  // valid block type.

  if (transformationResults.some(result => !registration_getBlockType(result.name))) {
    return null;
  }

  const hasSwitchedBlock = Object(external_lodash_["some"])(transformationResults, result => result.name === name); // Ensure that at least one block object returned by the transformation has
  // the expected "destination" block type.

  if (!hasSwitchedBlock) {
    return null;
  }

  const ret = transformationResults.map(result => {
    /**
     * Filters an individual transform result from block transformation.
     * All of the original blocks are passed, since transformations are
     * many-to-many, not one-to-one.
     *
     * @param {Object}   transformedBlock The transformed block.
     * @param {Object[]} blocks           Original blocks transformed.
     */
    return Object(external_wp_hooks_["applyFilters"])('blocks.switchToBlockType.transformedBlock', result, blocks);
  });
  return ret;
}
/**
 * Create a block object from the example API.
 *
 * @param {string} name
 * @param {Object} example
 *
 * @return {Object} block.
 */

const getBlockFromExample = (name, example) => {
  return createBlock(name, example.attributes, Object(external_lodash_["map"])(example.innerBlocks, innerBlock => getBlockFromExample(innerBlock.name, innerBlock)));
};

// CONCATENATED MODULE: ./node_modules/hpq/es/get-path.js
/**
 * Given object and string of dot-delimited path segments, returns value at
 * path or undefined if path cannot be resolved.
 *
 * @param  {Object} object Lookup object
 * @param  {string} path   Path to resolve
 * @return {?*}            Resolved value
 */
function getPath(object, path) {
  var segments = path.split('.');
  var segment;

  while (segment = segments.shift()) {
    if (!(segment in object)) {
      return;
    }

    object = object[segment];
  }

  return object;
}
// CONCATENATED MODULE: ./node_modules/hpq/es/index.js
/**
 * Internal dependencies
 */

/**
 * Function returning a DOM document created by `createHTMLDocument`. The same
 * document is returned between invocations.
 *
 * @return {Document} DOM document.
 */

var getDocument = function () {
  var doc;
  return function () {
    if (!doc) {
      doc = document.implementation.createHTMLDocument('');
    }

    return doc;
  };
}();
/**
 * Given a markup string or DOM element, creates an object aligning with the
 * shape of the matchers object, or the value returned by the matcher.
 *
 * @param  {(string|Element)}  source   Source content
 * @param  {(Object|Function)} matchers Matcher function or object of matchers
 * @return {(Object|*)}                 Matched value(s), shaped by object
 */


function parse(source, matchers) {
  if (!matchers) {
    return;
  } // Coerce to element


  if ('string' === typeof source) {
    var doc = getDocument();
    doc.body.innerHTML = source;
    source = doc.body;
  } // Return singular value


  if ('function' === typeof matchers) {
    return matchers(source);
  } // Bail if we can't handle matchers


  if (Object !== matchers.constructor) {
    return;
  } // Shape result by matcher object


  return Object.keys(matchers).reduce(function (memo, key) {
    memo[key] = parse(source, matchers[key]);
    return memo;
  }, {});
}
/**
 * Generates a function which matches node of type selector, returning an
 * attribute by property if the attribute exists. If no selector is passed,
 * returns property of the query element.
 *
 * @param  {?string} selector Optional selector
 * @param  {string}  name     Property name
 * @return {*}                Property value
 */

function prop(selector, name) {
  if (1 === arguments.length) {
    name = selector;
    selector = undefined;
  }

  return function (node) {
    var match = node;

    if (selector) {
      match = node.querySelector(selector);
    }

    if (match) {
      return getPath(match, name);
    }
  };
}
/**
 * Generates a function which matches node of type selector, returning an
 * attribute by name if the attribute exists. If no selector is passed,
 * returns attribute of the query element.
 *
 * @param  {?string} selector Optional selector
 * @param  {string}  name     Attribute name
 * @return {?string}          Attribute value
 */

function attr(selector, name) {
  if (1 === arguments.length) {
    name = selector;
    selector = undefined;
  }

  return function (node) {
    var attributes = prop(selector, 'attributes')(node);

    if (attributes && attributes.hasOwnProperty(name)) {
      return attributes[name].value;
    }
  };
}
/**
 * Convenience for `prop( selector, 'innerHTML' )`.
 *
 * @see prop()
 *
 * @param  {?string} selector Optional selector
 * @return {string}           Inner HTML
 */

function es_html(selector) {
  return prop(selector, 'innerHTML');
}
/**
 * Convenience for `prop( selector, 'textContent' )`.
 *
 * @see prop()
 *
 * @param  {?string} selector Optional selector
 * @return {string}           Text content
 */

function es_text(selector) {
  return prop(selector, 'textContent');
}
/**
 * Creates a new matching context by first finding elements matching selector
 * using querySelectorAll before then running another `parse` on `matchers`
 * scoped to the matched elements.
 *
 * @see parse()
 *
 * @param  {string}            selector Selector to match
 * @param  {(Object|Function)} matchers Matcher function or object of matchers
 * @return {Array.<*,Object>}           Array of matched value(s)
 */

function query(selector, matchers) {
  return function (node) {
    var matches = node.querySelectorAll(selector);
    return [].map.call(matches, function (match) {
      return parse(match, matchers);
    });
  };
}
// EXTERNAL MODULE: external ["wp","autop"]
var external_wp_autop_ = __webpack_require__("UuzZ");

// EXTERNAL MODULE: external ["wp","blockSerializationDefaultParser"]
var external_wp_blockSerializationDefaultParser_ = __webpack_require__("ouCq");

// CONCATENATED MODULE: ./node_modules/simple-html-tokenizer/dist/es6/index.js
/**
 * generated from https://raw.githubusercontent.com/w3c/html/26b5126f96f736f796b9e29718138919dd513744/entities.json
 * do not edit
 */
var namedCharRefs = {
    Aacute: "Á", aacute: "á", Abreve: "Ă", abreve: "ă", ac: "∾", acd: "∿", acE: "∾̳", Acirc: "Â", acirc: "â", acute: "´", Acy: "А", acy: "а", AElig: "Æ", aelig: "æ", af: "\u2061", Afr: "𝔄", afr: "𝔞", Agrave: "À", agrave: "à", alefsym: "ℵ", aleph: "ℵ", Alpha: "Α", alpha: "α", Amacr: "Ā", amacr: "ā", amalg: "⨿", amp: "&", AMP: "&", andand: "⩕", And: "⩓", and: "∧", andd: "⩜", andslope: "⩘", andv: "⩚", ang: "∠", ange: "⦤", angle: "∠", angmsdaa: "⦨", angmsdab: "⦩", angmsdac: "⦪", angmsdad: "⦫", angmsdae: "⦬", angmsdaf: "⦭", angmsdag: "⦮", angmsdah: "⦯", angmsd: "∡", angrt: "∟", angrtvb: "⊾", angrtvbd: "⦝", angsph: "∢", angst: "Å", angzarr: "⍼", Aogon: "Ą", aogon: "ą", Aopf: "𝔸", aopf: "𝕒", apacir: "⩯", ap: "≈", apE: "⩰", ape: "≊", apid: "≋", apos: "'", ApplyFunction: "\u2061", approx: "≈", approxeq: "≊", Aring: "Å", aring: "å", Ascr: "𝒜", ascr: "𝒶", Assign: "≔", ast: "*", asymp: "≈", asympeq: "≍", Atilde: "Ã", atilde: "ã", Auml: "Ä", auml: "ä", awconint: "∳", awint: "⨑", backcong: "≌", backepsilon: "϶", backprime: "‵", backsim: "∽", backsimeq: "⋍", Backslash: "∖", Barv: "⫧", barvee: "⊽", barwed: "⌅", Barwed: "⌆", barwedge: "⌅", bbrk: "⎵", bbrktbrk: "⎶", bcong: "≌", Bcy: "Б", bcy: "б", bdquo: "„", becaus: "∵", because: "∵", Because: "∵", bemptyv: "⦰", bepsi: "϶", bernou: "ℬ", Bernoullis: "ℬ", Beta: "Β", beta: "β", beth: "ℶ", between: "≬", Bfr: "𝔅", bfr: "𝔟", bigcap: "⋂", bigcirc: "◯", bigcup: "⋃", bigodot: "⨀", bigoplus: "⨁", bigotimes: "⨂", bigsqcup: "⨆", bigstar: "★", bigtriangledown: "▽", bigtriangleup: "△", biguplus: "⨄", bigvee: "⋁", bigwedge: "⋀", bkarow: "⤍", blacklozenge: "⧫", blacksquare: "▪", blacktriangle: "▴", blacktriangledown: "▾", blacktriangleleft: "◂", blacktriangleright: "▸", blank: "␣", blk12: "▒", blk14: "░", blk34: "▓", block: "█", bne: "=⃥", bnequiv: "≡⃥", bNot: "⫭", bnot: "⌐", Bopf: "𝔹", bopf: "𝕓", bot: "⊥", bottom: "⊥", bowtie: "⋈", boxbox: "⧉", boxdl: "┐", boxdL: "╕", boxDl: "╖", boxDL: "╗", boxdr: "┌", boxdR: "╒", boxDr: "╓", boxDR: "╔", boxh: "─", boxH: "═", boxhd: "┬", boxHd: "╤", boxhD: "╥", boxHD: "╦", boxhu: "┴", boxHu: "╧", boxhU: "╨", boxHU: "╩", boxminus: "⊟", boxplus: "⊞", boxtimes: "⊠", boxul: "┘", boxuL: "╛", boxUl: "╜", boxUL: "╝", boxur: "└", boxuR: "╘", boxUr: "╙", boxUR: "╚", boxv: "│", boxV: "║", boxvh: "┼", boxvH: "╪", boxVh: "╫", boxVH: "╬", boxvl: "┤", boxvL: "╡", boxVl: "╢", boxVL: "╣", boxvr: "├", boxvR: "╞", boxVr: "╟", boxVR: "╠", bprime: "‵", breve: "˘", Breve: "˘", brvbar: "¦", bscr: "𝒷", Bscr: "ℬ", bsemi: "⁏", bsim: "∽", bsime: "⋍", bsolb: "⧅", bsol: "\\", bsolhsub: "⟈", bull: "•", bullet: "•", bump: "≎", bumpE: "⪮", bumpe: "≏", Bumpeq: "≎", bumpeq: "≏", Cacute: "Ć", cacute: "ć", capand: "⩄", capbrcup: "⩉", capcap: "⩋", cap: "∩", Cap: "⋒", capcup: "⩇", capdot: "⩀", CapitalDifferentialD: "ⅅ", caps: "∩︀", caret: "⁁", caron: "ˇ", Cayleys: "ℭ", ccaps: "⩍", Ccaron: "Č", ccaron: "č", Ccedil: "Ç", ccedil: "ç", Ccirc: "Ĉ", ccirc: "ĉ", Cconint: "∰", ccups: "⩌", ccupssm: "⩐", Cdot: "Ċ", cdot: "ċ", cedil: "¸", Cedilla: "¸", cemptyv: "⦲", cent: "¢", centerdot: "·", CenterDot: "·", cfr: "𝔠", Cfr: "ℭ", CHcy: "Ч", chcy: "ч", check: "✓", checkmark: "✓", Chi: "Χ", chi: "χ", circ: "ˆ", circeq: "≗", circlearrowleft: "↺", circlearrowright: "↻", circledast: "⊛", circledcirc: "⊚", circleddash: "⊝", CircleDot: "⊙", circledR: "®", circledS: "Ⓢ", CircleMinus: "⊖", CirclePlus: "⊕", CircleTimes: "⊗", cir: "○", cirE: "⧃", cire: "≗", cirfnint: "⨐", cirmid: "⫯", cirscir: "⧂", ClockwiseContourIntegral: "∲", CloseCurlyDoubleQuote: "”", CloseCurlyQuote: "’", clubs: "♣", clubsuit: "♣", colon: ":", Colon: "∷", Colone: "⩴", colone: "≔", coloneq: "≔", comma: ",", commat: "@", comp: "∁", compfn: "∘", complement: "∁", complexes: "ℂ", cong: "≅", congdot: "⩭", Congruent: "≡", conint: "∮", Conint: "∯", ContourIntegral: "∮", copf: "𝕔", Copf: "ℂ", coprod: "∐", Coproduct: "∐", copy: "©", COPY: "©", copysr: "℗", CounterClockwiseContourIntegral: "∳", crarr: "↵", cross: "✗", Cross: "⨯", Cscr: "𝒞", cscr: "𝒸", csub: "⫏", csube: "⫑", csup: "⫐", csupe: "⫒", ctdot: "⋯", cudarrl: "⤸", cudarrr: "⤵", cuepr: "⋞", cuesc: "⋟", cularr: "↶", cularrp: "⤽", cupbrcap: "⩈", cupcap: "⩆", CupCap: "≍", cup: "∪", Cup: "⋓", cupcup: "⩊", cupdot: "⊍", cupor: "⩅", cups: "∪︀", curarr: "↷", curarrm: "⤼", curlyeqprec: "⋞", curlyeqsucc: "⋟", curlyvee: "⋎", curlywedge: "⋏", curren: "¤", curvearrowleft: "↶", curvearrowright: "↷", cuvee: "⋎", cuwed: "⋏", cwconint: "∲", cwint: "∱", cylcty: "⌭", dagger: "†", Dagger: "‡", daleth: "ℸ", darr: "↓", Darr: "↡", dArr: "⇓", dash: "‐", Dashv: "⫤", dashv: "⊣", dbkarow: "⤏", dblac: "˝", Dcaron: "Ď", dcaron: "ď", Dcy: "Д", dcy: "д", ddagger: "‡", ddarr: "⇊", DD: "ⅅ", dd: "ⅆ", DDotrahd: "⤑", ddotseq: "⩷", deg: "°", Del: "∇", Delta: "Δ", delta: "δ", demptyv: "⦱", dfisht: "⥿", Dfr: "𝔇", dfr: "𝔡", dHar: "⥥", dharl: "⇃", dharr: "⇂", DiacriticalAcute: "´", DiacriticalDot: "˙", DiacriticalDoubleAcute: "˝", DiacriticalGrave: "`", DiacriticalTilde: "˜", diam: "⋄", diamond: "⋄", Diamond: "⋄", diamondsuit: "♦", diams: "♦", die: "¨", DifferentialD: "ⅆ", digamma: "ϝ", disin: "⋲", div: "÷", divide: "÷", divideontimes: "⋇", divonx: "⋇", DJcy: "Ђ", djcy: "ђ", dlcorn: "⌞", dlcrop: "⌍", dollar: "$", Dopf: "𝔻", dopf: "𝕕", Dot: "¨", dot: "˙", DotDot: "⃜", doteq: "≐", doteqdot: "≑", DotEqual: "≐", dotminus: "∸", dotplus: "∔", dotsquare: "⊡", doublebarwedge: "⌆", DoubleContourIntegral: "∯", DoubleDot: "¨", DoubleDownArrow: "⇓", DoubleLeftArrow: "⇐", DoubleLeftRightArrow: "⇔", DoubleLeftTee: "⫤", DoubleLongLeftArrow: "⟸", DoubleLongLeftRightArrow: "⟺", DoubleLongRightArrow: "⟹", DoubleRightArrow: "⇒", DoubleRightTee: "⊨", DoubleUpArrow: "⇑", DoubleUpDownArrow: "⇕", DoubleVerticalBar: "∥", DownArrowBar: "⤓", downarrow: "↓", DownArrow: "↓", Downarrow: "⇓", DownArrowUpArrow: "⇵", DownBreve: "̑", downdownarrows: "⇊", downharpoonleft: "⇃", downharpoonright: "⇂", DownLeftRightVector: "⥐", DownLeftTeeVector: "⥞", DownLeftVectorBar: "⥖", DownLeftVector: "↽", DownRightTeeVector: "⥟", DownRightVectorBar: "⥗", DownRightVector: "⇁", DownTeeArrow: "↧", DownTee: "⊤", drbkarow: "⤐", drcorn: "⌟", drcrop: "⌌", Dscr: "𝒟", dscr: "𝒹", DScy: "Ѕ", dscy: "ѕ", dsol: "⧶", Dstrok: "Đ", dstrok: "đ", dtdot: "⋱", dtri: "▿", dtrif: "▾", duarr: "⇵", duhar: "⥯", dwangle: "⦦", DZcy: "Џ", dzcy: "џ", dzigrarr: "⟿", Eacute: "É", eacute: "é", easter: "⩮", Ecaron: "Ě", ecaron: "ě", Ecirc: "Ê", ecirc: "ê", ecir: "≖", ecolon: "≕", Ecy: "Э", ecy: "э", eDDot: "⩷", Edot: "Ė", edot: "ė", eDot: "≑", ee: "ⅇ", efDot: "≒", Efr: "𝔈", efr: "𝔢", eg: "⪚", Egrave: "È", egrave: "è", egs: "⪖", egsdot: "⪘", el: "⪙", Element: "∈", elinters: "⏧", ell: "ℓ", els: "⪕", elsdot: "⪗", Emacr: "Ē", emacr: "ē", empty: "∅", emptyset: "∅", EmptySmallSquare: "◻", emptyv: "∅", EmptyVerySmallSquare: "▫", emsp13: " ", emsp14: " ", emsp: " ", ENG: "Ŋ", eng: "ŋ", ensp: " ", Eogon: "Ę", eogon: "ę", Eopf: "𝔼", eopf: "𝕖", epar: "⋕", eparsl: "⧣", eplus: "⩱", epsi: "ε", Epsilon: "Ε", epsilon: "ε", epsiv: "ϵ", eqcirc: "≖", eqcolon: "≕", eqsim: "≂", eqslantgtr: "⪖", eqslantless: "⪕", Equal: "⩵", equals: "=", EqualTilde: "≂", equest: "≟", Equilibrium: "⇌", equiv: "≡", equivDD: "⩸", eqvparsl: "⧥", erarr: "⥱", erDot: "≓", escr: "ℯ", Escr: "ℰ", esdot: "≐", Esim: "⩳", esim: "≂", Eta: "Η", eta: "η", ETH: "Ð", eth: "ð", Euml: "Ë", euml: "ë", euro: "€", excl: "!", exist: "∃", Exists: "∃", expectation: "ℰ", exponentiale: "ⅇ", ExponentialE: "ⅇ", fallingdotseq: "≒", Fcy: "Ф", fcy: "ф", female: "♀", ffilig: "ﬃ", fflig: "ﬀ", ffllig: "ﬄ", Ffr: "𝔉", ffr: "𝔣", filig: "ﬁ", FilledSmallSquare: "◼", FilledVerySmallSquare: "▪", fjlig: "fj", flat: "♭", fllig: "ﬂ", fltns: "▱", fnof: "ƒ", Fopf: "𝔽", fopf: "𝕗", forall: "∀", ForAll: "∀", fork: "⋔", forkv: "⫙", Fouriertrf: "ℱ", fpartint: "⨍", frac12: "½", frac13: "⅓", frac14: "¼", frac15: "⅕", frac16: "⅙", frac18: "⅛", frac23: "⅔", frac25: "⅖", frac34: "¾", frac35: "⅗", frac38: "⅜", frac45: "⅘", frac56: "⅚", frac58: "⅝", frac78: "⅞", frasl: "⁄", frown: "⌢", fscr: "𝒻", Fscr: "ℱ", gacute: "ǵ", Gamma: "Γ", gamma: "γ", Gammad: "Ϝ", gammad: "ϝ", gap: "⪆", Gbreve: "Ğ", gbreve: "ğ", Gcedil: "Ģ", Gcirc: "Ĝ", gcirc: "ĝ", Gcy: "Г", gcy: "г", Gdot: "Ġ", gdot: "ġ", ge: "≥", gE: "≧", gEl: "⪌", gel: "⋛", geq: "≥", geqq: "≧", geqslant: "⩾", gescc: "⪩", ges: "⩾", gesdot: "⪀", gesdoto: "⪂", gesdotol: "⪄", gesl: "⋛︀", gesles: "⪔", Gfr: "𝔊", gfr: "𝔤", gg: "≫", Gg: "⋙", ggg: "⋙", gimel: "ℷ", GJcy: "Ѓ", gjcy: "ѓ", gla: "⪥", gl: "≷", glE: "⪒", glj: "⪤", gnap: "⪊", gnapprox: "⪊", gne: "⪈", gnE: "≩", gneq: "⪈", gneqq: "≩", gnsim: "⋧", Gopf: "𝔾", gopf: "𝕘", grave: "`", GreaterEqual: "≥", GreaterEqualLess: "⋛", GreaterFullEqual: "≧", GreaterGreater: "⪢", GreaterLess: "≷", GreaterSlantEqual: "⩾", GreaterTilde: "≳", Gscr: "𝒢", gscr: "ℊ", gsim: "≳", gsime: "⪎", gsiml: "⪐", gtcc: "⪧", gtcir: "⩺", gt: ">", GT: ">", Gt: "≫", gtdot: "⋗", gtlPar: "⦕", gtquest: "⩼", gtrapprox: "⪆", gtrarr: "⥸", gtrdot: "⋗", gtreqless: "⋛", gtreqqless: "⪌", gtrless: "≷", gtrsim: "≳", gvertneqq: "≩︀", gvnE: "≩︀", Hacek: "ˇ", hairsp: " ", half: "½", hamilt: "ℋ", HARDcy: "Ъ", hardcy: "ъ", harrcir: "⥈", harr: "↔", hArr: "⇔", harrw: "↭", Hat: "^", hbar: "ℏ", Hcirc: "Ĥ", hcirc: "ĥ", hearts: "♥", heartsuit: "♥", hellip: "…", hercon: "⊹", hfr: "𝔥", Hfr: "ℌ", HilbertSpace: "ℋ", hksearow: "⤥", hkswarow: "⤦", hoarr: "⇿", homtht: "∻", hookleftarrow: "↩", hookrightarrow: "↪", hopf: "𝕙", Hopf: "ℍ", horbar: "―", HorizontalLine: "─", hscr: "𝒽", Hscr: "ℋ", hslash: "ℏ", Hstrok: "Ħ", hstrok: "ħ", HumpDownHump: "≎", HumpEqual: "≏", hybull: "⁃", hyphen: "‐", Iacute: "Í", iacute: "í", ic: "\u2063", Icirc: "Î", icirc: "î", Icy: "И", icy: "и", Idot: "İ", IEcy: "Е", iecy: "е", iexcl: "¡", iff: "⇔", ifr: "𝔦", Ifr: "ℑ", Igrave: "Ì", igrave: "ì", ii: "ⅈ", iiiint: "⨌", iiint: "∭", iinfin: "⧜", iiota: "℩", IJlig: "Ĳ", ijlig: "ĳ", Imacr: "Ī", imacr: "ī", image: "ℑ", ImaginaryI: "ⅈ", imagline: "ℐ", imagpart: "ℑ", imath: "ı", Im: "ℑ", imof: "⊷", imped: "Ƶ", Implies: "⇒", incare: "℅", in: "∈", infin: "∞", infintie: "⧝", inodot: "ı", intcal: "⊺", int: "∫", Int: "∬", integers: "ℤ", Integral: "∫", intercal: "⊺", Intersection: "⋂", intlarhk: "⨗", intprod: "⨼", InvisibleComma: "\u2063", InvisibleTimes: "\u2062", IOcy: "Ё", iocy: "ё", Iogon: "Į", iogon: "į", Iopf: "𝕀", iopf: "𝕚", Iota: "Ι", iota: "ι", iprod: "⨼", iquest: "¿", iscr: "𝒾", Iscr: "ℐ", isin: "∈", isindot: "⋵", isinE: "⋹", isins: "⋴", isinsv: "⋳", isinv: "∈", it: "\u2062", Itilde: "Ĩ", itilde: "ĩ", Iukcy: "І", iukcy: "і", Iuml: "Ï", iuml: "ï", Jcirc: "Ĵ", jcirc: "ĵ", Jcy: "Й", jcy: "й", Jfr: "𝔍", jfr: "𝔧", jmath: "ȷ", Jopf: "𝕁", jopf: "𝕛", Jscr: "𝒥", jscr: "𝒿", Jsercy: "Ј", jsercy: "ј", Jukcy: "Є", jukcy: "є", Kappa: "Κ", kappa: "κ", kappav: "ϰ", Kcedil: "Ķ", kcedil: "ķ", Kcy: "К", kcy: "к", Kfr: "𝔎", kfr: "𝔨", kgreen: "ĸ", KHcy: "Х", khcy: "х", KJcy: "Ќ", kjcy: "ќ", Kopf: "𝕂", kopf: "𝕜", Kscr: "𝒦", kscr: "𝓀", lAarr: "⇚", Lacute: "Ĺ", lacute: "ĺ", laemptyv: "⦴", lagran: "ℒ", Lambda: "Λ", lambda: "λ", lang: "⟨", Lang: "⟪", langd: "⦑", langle: "⟨", lap: "⪅", Laplacetrf: "ℒ", laquo: "«", larrb: "⇤", larrbfs: "⤟", larr: "←", Larr: "↞", lArr: "⇐", larrfs: "⤝", larrhk: "↩", larrlp: "↫", larrpl: "⤹", larrsim: "⥳", larrtl: "↢", latail: "⤙", lAtail: "⤛", lat: "⪫", late: "⪭", lates: "⪭︀", lbarr: "⤌", lBarr: "⤎", lbbrk: "❲", lbrace: "{", lbrack: "[", lbrke: "⦋", lbrksld: "⦏", lbrkslu: "⦍", Lcaron: "Ľ", lcaron: "ľ", Lcedil: "Ļ", lcedil: "ļ", lceil: "⌈", lcub: "{", Lcy: "Л", lcy: "л", ldca: "⤶", ldquo: "“", ldquor: "„", ldrdhar: "⥧", ldrushar: "⥋", ldsh: "↲", le: "≤", lE: "≦", LeftAngleBracket: "⟨", LeftArrowBar: "⇤", leftarrow: "←", LeftArrow: "←", Leftarrow: "⇐", LeftArrowRightArrow: "⇆", leftarrowtail: "↢", LeftCeiling: "⌈", LeftDoubleBracket: "⟦", LeftDownTeeVector: "⥡", LeftDownVectorBar: "⥙", LeftDownVector: "⇃", LeftFloor: "⌊", leftharpoondown: "↽", leftharpoonup: "↼", leftleftarrows: "⇇", leftrightarrow: "↔", LeftRightArrow: "↔", Leftrightarrow: "⇔", leftrightarrows: "⇆", leftrightharpoons: "⇋", leftrightsquigarrow: "↭", LeftRightVector: "⥎", LeftTeeArrow: "↤", LeftTee: "⊣", LeftTeeVector: "⥚", leftthreetimes: "⋋", LeftTriangleBar: "⧏", LeftTriangle: "⊲", LeftTriangleEqual: "⊴", LeftUpDownVector: "⥑", LeftUpTeeVector: "⥠", LeftUpVectorBar: "⥘", LeftUpVector: "↿", LeftVectorBar: "⥒", LeftVector: "↼", lEg: "⪋", leg: "⋚", leq: "≤", leqq: "≦", leqslant: "⩽", lescc: "⪨", les: "⩽", lesdot: "⩿", lesdoto: "⪁", lesdotor: "⪃", lesg: "⋚︀", lesges: "⪓", lessapprox: "⪅", lessdot: "⋖", lesseqgtr: "⋚", lesseqqgtr: "⪋", LessEqualGreater: "⋚", LessFullEqual: "≦", LessGreater: "≶", lessgtr: "≶", LessLess: "⪡", lesssim: "≲", LessSlantEqual: "⩽", LessTilde: "≲", lfisht: "⥼", lfloor: "⌊", Lfr: "𝔏", lfr: "𝔩", lg: "≶", lgE: "⪑", lHar: "⥢", lhard: "↽", lharu: "↼", lharul: "⥪", lhblk: "▄", LJcy: "Љ", ljcy: "љ", llarr: "⇇", ll: "≪", Ll: "⋘", llcorner: "⌞", Lleftarrow: "⇚", llhard: "⥫", lltri: "◺", Lmidot: "Ŀ", lmidot: "ŀ", lmoustache: "⎰", lmoust: "⎰", lnap: "⪉", lnapprox: "⪉", lne: "⪇", lnE: "≨", lneq: "⪇", lneqq: "≨", lnsim: "⋦", loang: "⟬", loarr: "⇽", lobrk: "⟦", longleftarrow: "⟵", LongLeftArrow: "⟵", Longleftarrow: "⟸", longleftrightarrow: "⟷", LongLeftRightArrow: "⟷", Longleftrightarrow: "⟺", longmapsto: "⟼", longrightarrow: "⟶", LongRightArrow: "⟶", Longrightarrow: "⟹", looparrowleft: "↫", looparrowright: "↬", lopar: "⦅", Lopf: "𝕃", lopf: "𝕝", loplus: "⨭", lotimes: "⨴", lowast: "∗", lowbar: "_", LowerLeftArrow: "↙", LowerRightArrow: "↘", loz: "◊", lozenge: "◊", lozf: "⧫", lpar: "(", lparlt: "⦓", lrarr: "⇆", lrcorner: "⌟", lrhar: "⇋", lrhard: "⥭", lrm: "\u200e", lrtri: "⊿", lsaquo: "‹", lscr: "𝓁", Lscr: "ℒ", lsh: "↰", Lsh: "↰", lsim: "≲", lsime: "⪍", lsimg: "⪏", lsqb: "[", lsquo: "‘", lsquor: "‚", Lstrok: "Ł", lstrok: "ł", ltcc: "⪦", ltcir: "⩹", lt: "<", LT: "<", Lt: "≪", ltdot: "⋖", lthree: "⋋", ltimes: "⋉", ltlarr: "⥶", ltquest: "⩻", ltri: "◃", ltrie: "⊴", ltrif: "◂", ltrPar: "⦖", lurdshar: "⥊", luruhar: "⥦", lvertneqq: "≨︀", lvnE: "≨︀", macr: "¯", male: "♂", malt: "✠", maltese: "✠", Map: "⤅", map: "↦", mapsto: "↦", mapstodown: "↧", mapstoleft: "↤", mapstoup: "↥", marker: "▮", mcomma: "⨩", Mcy: "М", mcy: "м", mdash: "—", mDDot: "∺", measuredangle: "∡", MediumSpace: " ", Mellintrf: "ℳ", Mfr: "𝔐", mfr: "𝔪", mho: "℧", micro: "µ", midast: "*", midcir: "⫰", mid: "∣", middot: "·", minusb: "⊟", minus: "−", minusd: "∸", minusdu: "⨪", MinusPlus: "∓", mlcp: "⫛", mldr: "…", mnplus: "∓", models: "⊧", Mopf: "𝕄", mopf: "𝕞", mp: "∓", mscr: "𝓂", Mscr: "ℳ", mstpos: "∾", Mu: "Μ", mu: "μ", multimap: "⊸", mumap: "⊸", nabla: "∇", Nacute: "Ń", nacute: "ń", nang: "∠⃒", nap: "≉", napE: "⩰̸", napid: "≋̸", napos: "ŉ", napprox: "≉", natural: "♮", naturals: "ℕ", natur: "♮", nbsp: " ", nbump: "≎̸", nbumpe: "≏̸", ncap: "⩃", Ncaron: "Ň", ncaron: "ň", Ncedil: "Ņ", ncedil: "ņ", ncong: "≇", ncongdot: "⩭̸", ncup: "⩂", Ncy: "Н", ncy: "н", ndash: "–", nearhk: "⤤", nearr: "↗", neArr: "⇗", nearrow: "↗", ne: "≠", nedot: "≐̸", NegativeMediumSpace: "​", NegativeThickSpace: "​", NegativeThinSpace: "​", NegativeVeryThinSpace: "​", nequiv: "≢", nesear: "⤨", nesim: "≂̸", NestedGreaterGreater: "≫", NestedLessLess: "≪", NewLine: "\u000a", nexist: "∄", nexists: "∄", Nfr: "𝔑", nfr: "𝔫", ngE: "≧̸", nge: "≱", ngeq: "≱", ngeqq: "≧̸", ngeqslant: "⩾̸", nges: "⩾̸", nGg: "⋙̸", ngsim: "≵", nGt: "≫⃒", ngt: "≯", ngtr: "≯", nGtv: "≫̸", nharr: "↮", nhArr: "⇎", nhpar: "⫲", ni: "∋", nis: "⋼", nisd: "⋺", niv: "∋", NJcy: "Њ", njcy: "њ", nlarr: "↚", nlArr: "⇍", nldr: "‥", nlE: "≦̸", nle: "≰", nleftarrow: "↚", nLeftarrow: "⇍", nleftrightarrow: "↮", nLeftrightarrow: "⇎", nleq: "≰", nleqq: "≦̸", nleqslant: "⩽̸", nles: "⩽̸", nless: "≮", nLl: "⋘̸", nlsim: "≴", nLt: "≪⃒", nlt: "≮", nltri: "⋪", nltrie: "⋬", nLtv: "≪̸", nmid: "∤", NoBreak: "\u2060", NonBreakingSpace: " ", nopf: "𝕟", Nopf: "ℕ", Not: "⫬", not: "¬", NotCongruent: "≢", NotCupCap: "≭", NotDoubleVerticalBar: "∦", NotElement: "∉", NotEqual: "≠", NotEqualTilde: "≂̸", NotExists: "∄", NotGreater: "≯", NotGreaterEqual: "≱", NotGreaterFullEqual: "≧̸", NotGreaterGreater: "≫̸", NotGreaterLess: "≹", NotGreaterSlantEqual: "⩾̸", NotGreaterTilde: "≵", NotHumpDownHump: "≎̸", NotHumpEqual: "≏̸", notin: "∉", notindot: "⋵̸", notinE: "⋹̸", notinva: "∉", notinvb: "⋷", notinvc: "⋶", NotLeftTriangleBar: "⧏̸", NotLeftTriangle: "⋪", NotLeftTriangleEqual: "⋬", NotLess: "≮", NotLessEqual: "≰", NotLessGreater: "≸", NotLessLess: "≪̸", NotLessSlantEqual: "⩽̸", NotLessTilde: "≴", NotNestedGreaterGreater: "⪢̸", NotNestedLessLess: "⪡̸", notni: "∌", notniva: "∌", notnivb: "⋾", notnivc: "⋽", NotPrecedes: "⊀", NotPrecedesEqual: "⪯̸", NotPrecedesSlantEqual: "⋠", NotReverseElement: "∌", NotRightTriangleBar: "⧐̸", NotRightTriangle: "⋫", NotRightTriangleEqual: "⋭", NotSquareSubset: "⊏̸", NotSquareSubsetEqual: "⋢", NotSquareSuperset: "⊐̸", NotSquareSupersetEqual: "⋣", NotSubset: "⊂⃒", NotSubsetEqual: "⊈", NotSucceeds: "⊁", NotSucceedsEqual: "⪰̸", NotSucceedsSlantEqual: "⋡", NotSucceedsTilde: "≿̸", NotSuperset: "⊃⃒", NotSupersetEqual: "⊉", NotTilde: "≁", NotTildeEqual: "≄", NotTildeFullEqual: "≇", NotTildeTilde: "≉", NotVerticalBar: "∤", nparallel: "∦", npar: "∦", nparsl: "⫽⃥", npart: "∂̸", npolint: "⨔", npr: "⊀", nprcue: "⋠", nprec: "⊀", npreceq: "⪯̸", npre: "⪯̸", nrarrc: "⤳̸", nrarr: "↛", nrArr: "⇏", nrarrw: "↝̸", nrightarrow: "↛", nRightarrow: "⇏", nrtri: "⋫", nrtrie: "⋭", nsc: "⊁", nsccue: "⋡", nsce: "⪰̸", Nscr: "𝒩", nscr: "𝓃", nshortmid: "∤", nshortparallel: "∦", nsim: "≁", nsime: "≄", nsimeq: "≄", nsmid: "∤", nspar: "∦", nsqsube: "⋢", nsqsupe: "⋣", nsub: "⊄", nsubE: "⫅̸", nsube: "⊈", nsubset: "⊂⃒", nsubseteq: "⊈", nsubseteqq: "⫅̸", nsucc: "⊁", nsucceq: "⪰̸", nsup: "⊅", nsupE: "⫆̸", nsupe: "⊉", nsupset: "⊃⃒", nsupseteq: "⊉", nsupseteqq: "⫆̸", ntgl: "≹", Ntilde: "Ñ", ntilde: "ñ", ntlg: "≸", ntriangleleft: "⋪", ntrianglelefteq: "⋬", ntriangleright: "⋫", ntrianglerighteq: "⋭", Nu: "Ν", nu: "ν", num: "#", numero: "№", numsp: " ", nvap: "≍⃒", nvdash: "⊬", nvDash: "⊭", nVdash: "⊮", nVDash: "⊯", nvge: "≥⃒", nvgt: ">⃒", nvHarr: "⤄", nvinfin: "⧞", nvlArr: "⤂", nvle: "≤⃒", nvlt: "<⃒", nvltrie: "⊴⃒", nvrArr: "⤃", nvrtrie: "⊵⃒", nvsim: "∼⃒", nwarhk: "⤣", nwarr: "↖", nwArr: "⇖", nwarrow: "↖", nwnear: "⤧", Oacute: "Ó", oacute: "ó", oast: "⊛", Ocirc: "Ô", ocirc: "ô", ocir: "⊚", Ocy: "О", ocy: "о", odash: "⊝", Odblac: "Ő", odblac: "ő", odiv: "⨸", odot: "⊙", odsold: "⦼", OElig: "Œ", oelig: "œ", ofcir: "⦿", Ofr: "𝔒", ofr: "𝔬", ogon: "˛", Ograve: "Ò", ograve: "ò", ogt: "⧁", ohbar: "⦵", ohm: "Ω", oint: "∮", olarr: "↺", olcir: "⦾", olcross: "⦻", oline: "‾", olt: "⧀", Omacr: "Ō", omacr: "ō", Omega: "Ω", omega: "ω", Omicron: "Ο", omicron: "ο", omid: "⦶", ominus: "⊖", Oopf: "𝕆", oopf: "𝕠", opar: "⦷", OpenCurlyDoubleQuote: "“", OpenCurlyQuote: "‘", operp: "⦹", oplus: "⊕", orarr: "↻", Or: "⩔", or: "∨", ord: "⩝", order: "ℴ", orderof: "ℴ", ordf: "ª", ordm: "º", origof: "⊶", oror: "⩖", orslope: "⩗", orv: "⩛", oS: "Ⓢ", Oscr: "𝒪", oscr: "ℴ", Oslash: "Ø", oslash: "ø", osol: "⊘", Otilde: "Õ", otilde: "õ", otimesas: "⨶", Otimes: "⨷", otimes: "⊗", Ouml: "Ö", ouml: "ö", ovbar: "⌽", OverBar: "‾", OverBrace: "⏞", OverBracket: "⎴", OverParenthesis: "⏜", para: "¶", parallel: "∥", par: "∥", parsim: "⫳", parsl: "⫽", part: "∂", PartialD: "∂", Pcy: "П", pcy: "п", percnt: "%", period: ".", permil: "‰", perp: "⊥", pertenk: "‱", Pfr: "𝔓", pfr: "𝔭", Phi: "Φ", phi: "φ", phiv: "ϕ", phmmat: "ℳ", phone: "☎", Pi: "Π", pi: "π", pitchfork: "⋔", piv: "ϖ", planck: "ℏ", planckh: "ℎ", plankv: "ℏ", plusacir: "⨣", plusb: "⊞", pluscir: "⨢", plus: "+", plusdo: "∔", plusdu: "⨥", pluse: "⩲", PlusMinus: "±", plusmn: "±", plussim: "⨦", plustwo: "⨧", pm: "±", Poincareplane: "ℌ", pointint: "⨕", popf: "𝕡", Popf: "ℙ", pound: "£", prap: "⪷", Pr: "⪻", pr: "≺", prcue: "≼", precapprox: "⪷", prec: "≺", preccurlyeq: "≼", Precedes: "≺", PrecedesEqual: "⪯", PrecedesSlantEqual: "≼", PrecedesTilde: "≾", preceq: "⪯", precnapprox: "⪹", precneqq: "⪵", precnsim: "⋨", pre: "⪯", prE: "⪳", precsim: "≾", prime: "′", Prime: "″", primes: "ℙ", prnap: "⪹", prnE: "⪵", prnsim: "⋨", prod: "∏", Product: "∏", profalar: "⌮", profline: "⌒", profsurf: "⌓", prop: "∝", Proportional: "∝", Proportion: "∷", propto: "∝", prsim: "≾", prurel: "⊰", Pscr: "𝒫", pscr: "𝓅", Psi: "Ψ", psi: "ψ", puncsp: " ", Qfr: "𝔔", qfr: "𝔮", qint: "⨌", qopf: "𝕢", Qopf: "ℚ", qprime: "⁗", Qscr: "𝒬", qscr: "𝓆", quaternions: "ℍ", quatint: "⨖", quest: "?", questeq: "≟", quot: "\"", QUOT: "\"", rAarr: "⇛", race: "∽̱", Racute: "Ŕ", racute: "ŕ", radic: "√", raemptyv: "⦳", rang: "⟩", Rang: "⟫", rangd: "⦒", range: "⦥", rangle: "⟩", raquo: "»", rarrap: "⥵", rarrb: "⇥", rarrbfs: "⤠", rarrc: "⤳", rarr: "→", Rarr: "↠", rArr: "⇒", rarrfs: "⤞", rarrhk: "↪", rarrlp: "↬", rarrpl: "⥅", rarrsim: "⥴", Rarrtl: "⤖", rarrtl: "↣", rarrw: "↝", ratail: "⤚", rAtail: "⤜", ratio: "∶", rationals: "ℚ", rbarr: "⤍", rBarr: "⤏", RBarr: "⤐", rbbrk: "❳", rbrace: "}", rbrack: "]", rbrke: "⦌", rbrksld: "⦎", rbrkslu: "⦐", Rcaron: "Ř", rcaron: "ř", Rcedil: "Ŗ", rcedil: "ŗ", rceil: "⌉", rcub: "}", Rcy: "Р", rcy: "р", rdca: "⤷", rdldhar: "⥩", rdquo: "”", rdquor: "”", rdsh: "↳", real: "ℜ", realine: "ℛ", realpart: "ℜ", reals: "ℝ", Re: "ℜ", rect: "▭", reg: "®", REG: "®", ReverseElement: "∋", ReverseEquilibrium: "⇋", ReverseUpEquilibrium: "⥯", rfisht: "⥽", rfloor: "⌋", rfr: "𝔯", Rfr: "ℜ", rHar: "⥤", rhard: "⇁", rharu: "⇀", rharul: "⥬", Rho: "Ρ", rho: "ρ", rhov: "ϱ", RightAngleBracket: "⟩", RightArrowBar: "⇥", rightarrow: "→", RightArrow: "→", Rightarrow: "⇒", RightArrowLeftArrow: "⇄", rightarrowtail: "↣", RightCeiling: "⌉", RightDoubleBracket: "⟧", RightDownTeeVector: "⥝", RightDownVectorBar: "⥕", RightDownVector: "⇂", RightFloor: "⌋", rightharpoondown: "⇁", rightharpoonup: "⇀", rightleftarrows: "⇄", rightleftharpoons: "⇌", rightrightarrows: "⇉", rightsquigarrow: "↝", RightTeeArrow: "↦", RightTee: "⊢", RightTeeVector: "⥛", rightthreetimes: "⋌", RightTriangleBar: "⧐", RightTriangle: "⊳", RightTriangleEqual: "⊵", RightUpDownVector: "⥏", RightUpTeeVector: "⥜", RightUpVectorBar: "⥔", RightUpVector: "↾", RightVectorBar: "⥓", RightVector: "⇀", ring: "˚", risingdotseq: "≓", rlarr: "⇄", rlhar: "⇌", rlm: "\u200f", rmoustache: "⎱", rmoust: "⎱", rnmid: "⫮", roang: "⟭", roarr: "⇾", robrk: "⟧", ropar: "⦆", ropf: "𝕣", Ropf: "ℝ", roplus: "⨮", rotimes: "⨵", RoundImplies: "⥰", rpar: ")", rpargt: "⦔", rppolint: "⨒", rrarr: "⇉", Rrightarrow: "⇛", rsaquo: "›", rscr: "𝓇", Rscr: "ℛ", rsh: "↱", Rsh: "↱", rsqb: "]", rsquo: "’", rsquor: "’", rthree: "⋌", rtimes: "⋊", rtri: "▹", rtrie: "⊵", rtrif: "▸", rtriltri: "⧎", RuleDelayed: "⧴", ruluhar: "⥨", rx: "℞", Sacute: "Ś", sacute: "ś", sbquo: "‚", scap: "⪸", Scaron: "Š", scaron: "š", Sc: "⪼", sc: "≻", sccue: "≽", sce: "⪰", scE: "⪴", Scedil: "Ş", scedil: "ş", Scirc: "Ŝ", scirc: "ŝ", scnap: "⪺", scnE: "⪶", scnsim: "⋩", scpolint: "⨓", scsim: "≿", Scy: "С", scy: "с", sdotb: "⊡", sdot: "⋅", sdote: "⩦", searhk: "⤥", searr: "↘", seArr: "⇘", searrow: "↘", sect: "§", semi: ";", seswar: "⤩", setminus: "∖", setmn: "∖", sext: "✶", Sfr: "𝔖", sfr: "𝔰", sfrown: "⌢", sharp: "♯", SHCHcy: "Щ", shchcy: "щ", SHcy: "Ш", shcy: "ш", ShortDownArrow: "↓", ShortLeftArrow: "←", shortmid: "∣", shortparallel: "∥", ShortRightArrow: "→", ShortUpArrow: "↑", shy: "\u00ad", Sigma: "Σ", sigma: "σ", sigmaf: "ς", sigmav: "ς", sim: "∼", simdot: "⩪", sime: "≃", simeq: "≃", simg: "⪞", simgE: "⪠", siml: "⪝", simlE: "⪟", simne: "≆", simplus: "⨤", simrarr: "⥲", slarr: "←", SmallCircle: "∘", smallsetminus: "∖", smashp: "⨳", smeparsl: "⧤", smid: "∣", smile: "⌣", smt: "⪪", smte: "⪬", smtes: "⪬︀", SOFTcy: "Ь", softcy: "ь", solbar: "⌿", solb: "⧄", sol: "/", Sopf: "𝕊", sopf: "𝕤", spades: "♠", spadesuit: "♠", spar: "∥", sqcap: "⊓", sqcaps: "⊓︀", sqcup: "⊔", sqcups: "⊔︀", Sqrt: "√", sqsub: "⊏", sqsube: "⊑", sqsubset: "⊏", sqsubseteq: "⊑", sqsup: "⊐", sqsupe: "⊒", sqsupset: "⊐", sqsupseteq: "⊒", square: "□", Square: "□", SquareIntersection: "⊓", SquareSubset: "⊏", SquareSubsetEqual: "⊑", SquareSuperset: "⊐", SquareSupersetEqual: "⊒", SquareUnion: "⊔", squarf: "▪", squ: "□", squf: "▪", srarr: "→", Sscr: "𝒮", sscr: "𝓈", ssetmn: "∖", ssmile: "⌣", sstarf: "⋆", Star: "⋆", star: "☆", starf: "★", straightepsilon: "ϵ", straightphi: "ϕ", strns: "¯", sub: "⊂", Sub: "⋐", subdot: "⪽", subE: "⫅", sube: "⊆", subedot: "⫃", submult: "⫁", subnE: "⫋", subne: "⊊", subplus: "⪿", subrarr: "⥹", subset: "⊂", Subset: "⋐", subseteq: "⊆", subseteqq: "⫅", SubsetEqual: "⊆", subsetneq: "⊊", subsetneqq: "⫋", subsim: "⫇", subsub: "⫕", subsup: "⫓", succapprox: "⪸", succ: "≻", succcurlyeq: "≽", Succeeds: "≻", SucceedsEqual: "⪰", SucceedsSlantEqual: "≽", SucceedsTilde: "≿", succeq: "⪰", succnapprox: "⪺", succneqq: "⪶", succnsim: "⋩", succsim: "≿", SuchThat: "∋", sum: "∑", Sum: "∑", sung: "♪", sup1: "¹", sup2: "²", sup3: "³", sup: "⊃", Sup: "⋑", supdot: "⪾", supdsub: "⫘", supE: "⫆", supe: "⊇", supedot: "⫄", Superset: "⊃", SupersetEqual: "⊇", suphsol: "⟉", suphsub: "⫗", suplarr: "⥻", supmult: "⫂", supnE: "⫌", supne: "⊋", supplus: "⫀", supset: "⊃", Supset: "⋑", supseteq: "⊇", supseteqq: "⫆", supsetneq: "⊋", supsetneqq: "⫌", supsim: "⫈", supsub: "⫔", supsup: "⫖", swarhk: "⤦", swarr: "↙", swArr: "⇙", swarrow: "↙", swnwar: "⤪", szlig: "ß", Tab: "\u0009", target: "⌖", Tau: "Τ", tau: "τ", tbrk: "⎴", Tcaron: "Ť", tcaron: "ť", Tcedil: "Ţ", tcedil: "ţ", Tcy: "Т", tcy: "т", tdot: "⃛", telrec: "⌕", Tfr: "𝔗", tfr: "𝔱", there4: "∴", therefore: "∴", Therefore: "∴", Theta: "Θ", theta: "θ", thetasym: "ϑ", thetav: "ϑ", thickapprox: "≈", thicksim: "∼", ThickSpace: "  ", ThinSpace: " ", thinsp: " ", thkap: "≈", thksim: "∼", THORN: "Þ", thorn: "þ", tilde: "˜", Tilde: "∼", TildeEqual: "≃", TildeFullEqual: "≅", TildeTilde: "≈", timesbar: "⨱", timesb: "⊠", times: "×", timesd: "⨰", tint: "∭", toea: "⤨", topbot: "⌶", topcir: "⫱", top: "⊤", Topf: "𝕋", topf: "𝕥", topfork: "⫚", tosa: "⤩", tprime: "‴", trade: "™", TRADE: "™", triangle: "▵", triangledown: "▿", triangleleft: "◃", trianglelefteq: "⊴", triangleq: "≜", triangleright: "▹", trianglerighteq: "⊵", tridot: "◬", trie: "≜", triminus: "⨺", TripleDot: "⃛", triplus: "⨹", trisb: "⧍", tritime: "⨻", trpezium: "⏢", Tscr: "𝒯", tscr: "𝓉", TScy: "Ц", tscy: "ц", TSHcy: "Ћ", tshcy: "ћ", Tstrok: "Ŧ", tstrok: "ŧ", twixt: "≬", twoheadleftarrow: "↞", twoheadrightarrow: "↠", Uacute: "Ú", uacute: "ú", uarr: "↑", Uarr: "↟", uArr: "⇑", Uarrocir: "⥉", Ubrcy: "Ў", ubrcy: "ў", Ubreve: "Ŭ", ubreve: "ŭ", Ucirc: "Û", ucirc: "û", Ucy: "У", ucy: "у", udarr: "⇅", Udblac: "Ű", udblac: "ű", udhar: "⥮", ufisht: "⥾", Ufr: "𝔘", ufr: "𝔲", Ugrave: "Ù", ugrave: "ù", uHar: "⥣", uharl: "↿", uharr: "↾", uhblk: "▀", ulcorn: "⌜", ulcorner: "⌜", ulcrop: "⌏", ultri: "◸", Umacr: "Ū", umacr: "ū", uml: "¨", UnderBar: "_", UnderBrace: "⏟", UnderBracket: "⎵", UnderParenthesis: "⏝", Union: "⋃", UnionPlus: "⊎", Uogon: "Ų", uogon: "ų", Uopf: "𝕌", uopf: "𝕦", UpArrowBar: "⤒", uparrow: "↑", UpArrow: "↑", Uparrow: "⇑", UpArrowDownArrow: "⇅", updownarrow: "↕", UpDownArrow: "↕", Updownarrow: "⇕", UpEquilibrium: "⥮", upharpoonleft: "↿", upharpoonright: "↾", uplus: "⊎", UpperLeftArrow: "↖", UpperRightArrow: "↗", upsi: "υ", Upsi: "ϒ", upsih: "ϒ", Upsilon: "Υ", upsilon: "υ", UpTeeArrow: "↥", UpTee: "⊥", upuparrows: "⇈", urcorn: "⌝", urcorner: "⌝", urcrop: "⌎", Uring: "Ů", uring: "ů", urtri: "◹", Uscr: "𝒰", uscr: "𝓊", utdot: "⋰", Utilde: "Ũ", utilde: "ũ", utri: "▵", utrif: "▴", uuarr: "⇈", Uuml: "Ü", uuml: "ü", uwangle: "⦧", vangrt: "⦜", varepsilon: "ϵ", varkappa: "ϰ", varnothing: "∅", varphi: "ϕ", varpi: "ϖ", varpropto: "∝", varr: "↕", vArr: "⇕", varrho: "ϱ", varsigma: "ς", varsubsetneq: "⊊︀", varsubsetneqq: "⫋︀", varsupsetneq: "⊋︀", varsupsetneqq: "⫌︀", vartheta: "ϑ", vartriangleleft: "⊲", vartriangleright: "⊳", vBar: "⫨", Vbar: "⫫", vBarv: "⫩", Vcy: "В", vcy: "в", vdash: "⊢", vDash: "⊨", Vdash: "⊩", VDash: "⊫", Vdashl: "⫦", veebar: "⊻", vee: "∨", Vee: "⋁", veeeq: "≚", vellip: "⋮", verbar: "|", Verbar: "‖", vert: "|", Vert: "‖", VerticalBar: "∣", VerticalLine: "|", VerticalSeparator: "❘", VerticalTilde: "≀", VeryThinSpace: " ", Vfr: "𝔙", vfr: "𝔳", vltri: "⊲", vnsub: "⊂⃒", vnsup: "⊃⃒", Vopf: "𝕍", vopf: "𝕧", vprop: "∝", vrtri: "⊳", Vscr: "𝒱", vscr: "𝓋", vsubnE: "⫋︀", vsubne: "⊊︀", vsupnE: "⫌︀", vsupne: "⊋︀", Vvdash: "⊪", vzigzag: "⦚", Wcirc: "Ŵ", wcirc: "ŵ", wedbar: "⩟", wedge: "∧", Wedge: "⋀", wedgeq: "≙", weierp: "℘", Wfr: "𝔚", wfr: "𝔴", Wopf: "𝕎", wopf: "𝕨", wp: "℘", wr: "≀", wreath: "≀", Wscr: "𝒲", wscr: "𝓌", xcap: "⋂", xcirc: "◯", xcup: "⋃", xdtri: "▽", Xfr: "𝔛", xfr: "𝔵", xharr: "⟷", xhArr: "⟺", Xi: "Ξ", xi: "ξ", xlarr: "⟵", xlArr: "⟸", xmap: "⟼", xnis: "⋻", xodot: "⨀", Xopf: "𝕏", xopf: "𝕩", xoplus: "⨁", xotime: "⨂", xrarr: "⟶", xrArr: "⟹", Xscr: "𝒳", xscr: "𝓍", xsqcup: "⨆", xuplus: "⨄", xutri: "△", xvee: "⋁", xwedge: "⋀", Yacute: "Ý", yacute: "ý", YAcy: "Я", yacy: "я", Ycirc: "Ŷ", ycirc: "ŷ", Ycy: "Ы", ycy: "ы", yen: "¥", Yfr: "𝔜", yfr: "𝔶", YIcy: "Ї", yicy: "ї", Yopf: "𝕐", yopf: "𝕪", Yscr: "𝒴", yscr: "𝓎", YUcy: "Ю", yucy: "ю", yuml: "ÿ", Yuml: "Ÿ", Zacute: "Ź", zacute: "ź", Zcaron: "Ž", zcaron: "ž", Zcy: "З", zcy: "з", Zdot: "Ż", zdot: "ż", zeetrf: "ℨ", ZeroWidthSpace: "​", Zeta: "Ζ", zeta: "ζ", zfr: "𝔷", Zfr: "ℨ", ZHcy: "Ж", zhcy: "ж", zigrarr: "⇝", zopf: "𝕫", Zopf: "ℤ", Zscr: "𝒵", zscr: "𝓏", zwj: "\u200d", zwnj: "\u200c"
};

var HEXCHARCODE = /^#[xX]([A-Fa-f0-9]+)$/;
var CHARCODE = /^#([0-9]+)$/;
var NAMED = /^([A-Za-z0-9]+)$/;
var EntityParser = /** @class */ (function () {
    function EntityParser(named) {
        this.named = named;
    }
    EntityParser.prototype.parse = function (entity) {
        if (!entity) {
            return;
        }
        var matches = entity.match(HEXCHARCODE);
        if (matches) {
            return String.fromCharCode(parseInt(matches[1], 16));
        }
        matches = entity.match(CHARCODE);
        if (matches) {
            return String.fromCharCode(parseInt(matches[1], 10));
        }
        matches = entity.match(NAMED);
        if (matches) {
            return this.named[matches[1]];
        }
    };
    return EntityParser;
}());

var WSP = /[\t\n\f ]/;
var ALPHA = /[A-Za-z]/;
var CRLF = /\r\n?/g;
function isSpace(char) {
    return WSP.test(char);
}
function isAlpha(char) {
    return ALPHA.test(char);
}
function preprocessInput(input) {
    return input.replace(CRLF, '\n');
}

var EventedTokenizer = /** @class */ (function () {
    function EventedTokenizer(delegate, entityParser, mode) {
        if (mode === void 0) { mode = 'precompile'; }
        this.delegate = delegate;
        this.entityParser = entityParser;
        this.mode = mode;
        this.state = "beforeData" /* beforeData */;
        this.line = -1;
        this.column = -1;
        this.input = '';
        this.index = -1;
        this.tagNameBuffer = '';
        this.states = {
            beforeData: function () {
                var char = this.peek();
                if (char === '<' && !this.isIgnoredEndTag()) {
                    this.transitionTo("tagOpen" /* tagOpen */);
                    this.markTagStart();
                    this.consume();
                }
                else {
                    if (this.mode === 'precompile' && char === '\n') {
                        var tag = this.tagNameBuffer.toLowerCase();
                        if (tag === 'pre' || tag === 'textarea') {
                            this.consume();
                        }
                    }
                    this.transitionTo("data" /* data */);
                    this.delegate.beginData();
                }
            },
            data: function () {
                var char = this.peek();
                var tag = this.tagNameBuffer;
                if (char === '<' && !this.isIgnoredEndTag()) {
                    this.delegate.finishData();
                    this.transitionTo("tagOpen" /* tagOpen */);
                    this.markTagStart();
                    this.consume();
                }
                else if (char === '&' && tag !== 'script' && tag !== 'style') {
                    this.consume();
                    this.delegate.appendToData(this.consumeCharRef() || '&');
                }
                else {
                    this.consume();
                    this.delegate.appendToData(char);
                }
            },
            tagOpen: function () {
                var char = this.consume();
                if (char === '!') {
                    this.transitionTo("markupDeclarationOpen" /* markupDeclarationOpen */);
                }
                else if (char === '/') {
                    this.transitionTo("endTagOpen" /* endTagOpen */);
                }
                else if (char === '@' || char === ':' || isAlpha(char)) {
                    this.transitionTo("tagName" /* tagName */);
                    this.tagNameBuffer = '';
                    this.delegate.beginStartTag();
                    this.appendToTagName(char);
                }
            },
            markupDeclarationOpen: function () {
                var char = this.consume();
                if (char === '-' && this.peek() === '-') {
                    this.consume();
                    this.transitionTo("commentStart" /* commentStart */);
                    this.delegate.beginComment();
                }
                else {
                    var maybeDoctype = char.toUpperCase() + this.input.substring(this.index, this.index + 6).toUpperCase();
                    if (maybeDoctype === 'DOCTYPE') {
                        this.consume();
                        this.consume();
                        this.consume();
                        this.consume();
                        this.consume();
                        this.consume();
                        this.transitionTo("doctype" /* doctype */);
                        if (this.delegate.beginDoctype)
                            this.delegate.beginDoctype();
                    }
                }
            },
            doctype: function () {
                var char = this.consume();
                if (isSpace(char)) {
                    this.transitionTo("beforeDoctypeName" /* beforeDoctypeName */);
                }
            },
            beforeDoctypeName: function () {
                var char = this.consume();
                if (isSpace(char)) {
                    return;
                }
                else {
                    this.transitionTo("doctypeName" /* doctypeName */);
                    if (this.delegate.appendToDoctypeName)
                        this.delegate.appendToDoctypeName(char.toLowerCase());
                }
            },
            doctypeName: function () {
                var char = this.consume();
                if (isSpace(char)) {
                    this.transitionTo("afterDoctypeName" /* afterDoctypeName */);
                }
                else if (char === '>') {
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    if (this.delegate.appendToDoctypeName)
                        this.delegate.appendToDoctypeName(char.toLowerCase());
                }
            },
            afterDoctypeName: function () {
                var char = this.consume();
                if (isSpace(char)) {
                    return;
                }
                else if (char === '>') {
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    var nextSixChars = char.toUpperCase() + this.input.substring(this.index, this.index + 5).toUpperCase();
                    var isPublic = nextSixChars.toUpperCase() === 'PUBLIC';
                    var isSystem = nextSixChars.toUpperCase() === 'SYSTEM';
                    if (isPublic || isSystem) {
                        this.consume();
                        this.consume();
                        this.consume();
                        this.consume();
                        this.consume();
                        this.consume();
                    }
                    if (isPublic) {
                        this.transitionTo("afterDoctypePublicKeyword" /* afterDoctypePublicKeyword */);
                    }
                    else if (isSystem) {
                        this.transitionTo("afterDoctypeSystemKeyword" /* afterDoctypeSystemKeyword */);
                    }
                }
            },
            afterDoctypePublicKeyword: function () {
                var char = this.peek();
                if (isSpace(char)) {
                    this.transitionTo("beforeDoctypePublicIdentifier" /* beforeDoctypePublicIdentifier */);
                    this.consume();
                }
                else if (char === '"') {
                    this.transitionTo("doctypePublicIdentifierDoubleQuoted" /* doctypePublicIdentifierDoubleQuoted */);
                    this.consume();
                }
                else if (char === "'") {
                    this.transitionTo("doctypePublicIdentifierSingleQuoted" /* doctypePublicIdentifierSingleQuoted */);
                    this.consume();
                }
                else if (char === '>') {
                    this.consume();
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
            },
            doctypePublicIdentifierDoubleQuoted: function () {
                var char = this.consume();
                if (char === '"') {
                    this.transitionTo("afterDoctypePublicIdentifier" /* afterDoctypePublicIdentifier */);
                }
                else if (char === '>') {
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    if (this.delegate.appendToDoctypePublicIdentifier)
                        this.delegate.appendToDoctypePublicIdentifier(char);
                }
            },
            doctypePublicIdentifierSingleQuoted: function () {
                var char = this.consume();
                if (char === "'") {
                    this.transitionTo("afterDoctypePublicIdentifier" /* afterDoctypePublicIdentifier */);
                }
                else if (char === '>') {
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    if (this.delegate.appendToDoctypePublicIdentifier)
                        this.delegate.appendToDoctypePublicIdentifier(char);
                }
            },
            afterDoctypePublicIdentifier: function () {
                var char = this.consume();
                if (isSpace(char)) {
                    this.transitionTo("betweenDoctypePublicAndSystemIdentifiers" /* betweenDoctypePublicAndSystemIdentifiers */);
                }
                else if (char === '>') {
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else if (char === '"') {
                    this.transitionTo("doctypeSystemIdentifierDoubleQuoted" /* doctypeSystemIdentifierDoubleQuoted */);
                }
                else if (char === "'") {
                    this.transitionTo("doctypeSystemIdentifierSingleQuoted" /* doctypeSystemIdentifierSingleQuoted */);
                }
            },
            betweenDoctypePublicAndSystemIdentifiers: function () {
                var char = this.consume();
                if (isSpace(char)) {
                    return;
                }
                else if (char === '>') {
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else if (char === '"') {
                    this.transitionTo("doctypeSystemIdentifierDoubleQuoted" /* doctypeSystemIdentifierDoubleQuoted */);
                }
                else if (char === "'") {
                    this.transitionTo("doctypeSystemIdentifierSingleQuoted" /* doctypeSystemIdentifierSingleQuoted */);
                }
            },
            doctypeSystemIdentifierDoubleQuoted: function () {
                var char = this.consume();
                if (char === '"') {
                    this.transitionTo("afterDoctypeSystemIdentifier" /* afterDoctypeSystemIdentifier */);
                }
                else if (char === '>') {
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    if (this.delegate.appendToDoctypeSystemIdentifier)
                        this.delegate.appendToDoctypeSystemIdentifier(char);
                }
            },
            doctypeSystemIdentifierSingleQuoted: function () {
                var char = this.consume();
                if (char === "'") {
                    this.transitionTo("afterDoctypeSystemIdentifier" /* afterDoctypeSystemIdentifier */);
                }
                else if (char === '>') {
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    if (this.delegate.appendToDoctypeSystemIdentifier)
                        this.delegate.appendToDoctypeSystemIdentifier(char);
                }
            },
            afterDoctypeSystemIdentifier: function () {
                var char = this.consume();
                if (isSpace(char)) {
                    return;
                }
                else if (char === '>') {
                    if (this.delegate.endDoctype)
                        this.delegate.endDoctype();
                    this.transitionTo("beforeData" /* beforeData */);
                }
            },
            commentStart: function () {
                var char = this.consume();
                if (char === '-') {
                    this.transitionTo("commentStartDash" /* commentStartDash */);
                }
                else if (char === '>') {
                    this.delegate.finishComment();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    this.delegate.appendToCommentData(char);
                    this.transitionTo("comment" /* comment */);
                }
            },
            commentStartDash: function () {
                var char = this.consume();
                if (char === '-') {
                    this.transitionTo("commentEnd" /* commentEnd */);
                }
                else if (char === '>') {
                    this.delegate.finishComment();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    this.delegate.appendToCommentData('-');
                    this.transitionTo("comment" /* comment */);
                }
            },
            comment: function () {
                var char = this.consume();
                if (char === '-') {
                    this.transitionTo("commentEndDash" /* commentEndDash */);
                }
                else {
                    this.delegate.appendToCommentData(char);
                }
            },
            commentEndDash: function () {
                var char = this.consume();
                if (char === '-') {
                    this.transitionTo("commentEnd" /* commentEnd */);
                }
                else {
                    this.delegate.appendToCommentData('-' + char);
                    this.transitionTo("comment" /* comment */);
                }
            },
            commentEnd: function () {
                var char = this.consume();
                if (char === '>') {
                    this.delegate.finishComment();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    this.delegate.appendToCommentData('--' + char);
                    this.transitionTo("comment" /* comment */);
                }
            },
            tagName: function () {
                var char = this.consume();
                if (isSpace(char)) {
                    this.transitionTo("beforeAttributeName" /* beforeAttributeName */);
                }
                else if (char === '/') {
                    this.transitionTo("selfClosingStartTag" /* selfClosingStartTag */);
                }
                else if (char === '>') {
                    this.delegate.finishTag();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    this.appendToTagName(char);
                }
            },
            endTagName: function () {
                var char = this.consume();
                if (isSpace(char)) {
                    this.transitionTo("beforeAttributeName" /* beforeAttributeName */);
                    this.tagNameBuffer = '';
                }
                else if (char === '/') {
                    this.transitionTo("selfClosingStartTag" /* selfClosingStartTag */);
                    this.tagNameBuffer = '';
                }
                else if (char === '>') {
                    this.delegate.finishTag();
                    this.transitionTo("beforeData" /* beforeData */);
                    this.tagNameBuffer = '';
                }
                else {
                    this.appendToTagName(char);
                }
            },
            beforeAttributeName: function () {
                var char = this.peek();
                if (isSpace(char)) {
                    this.consume();
                    return;
                }
                else if (char === '/') {
                    this.transitionTo("selfClosingStartTag" /* selfClosingStartTag */);
                    this.consume();
                }
                else if (char === '>') {
                    this.consume();
                    this.delegate.finishTag();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else if (char === '=') {
                    this.delegate.reportSyntaxError('attribute name cannot start with equals sign');
                    this.transitionTo("attributeName" /* attributeName */);
                    this.delegate.beginAttribute();
                    this.consume();
                    this.delegate.appendToAttributeName(char);
                }
                else {
                    this.transitionTo("attributeName" /* attributeName */);
                    this.delegate.beginAttribute();
                }
            },
            attributeName: function () {
                var char = this.peek();
                if (isSpace(char)) {
                    this.transitionTo("afterAttributeName" /* afterAttributeName */);
                    this.consume();
                }
                else if (char === '/') {
                    this.delegate.beginAttributeValue(false);
                    this.delegate.finishAttributeValue();
                    this.consume();
                    this.transitionTo("selfClosingStartTag" /* selfClosingStartTag */);
                }
                else if (char === '=') {
                    this.transitionTo("beforeAttributeValue" /* beforeAttributeValue */);
                    this.consume();
                }
                else if (char === '>') {
                    this.delegate.beginAttributeValue(false);
                    this.delegate.finishAttributeValue();
                    this.consume();
                    this.delegate.finishTag();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else if (char === '"' || char === "'" || char === '<') {
                    this.delegate.reportSyntaxError(char + ' is not a valid character within attribute names');
                    this.consume();
                    this.delegate.appendToAttributeName(char);
                }
                else {
                    this.consume();
                    this.delegate.appendToAttributeName(char);
                }
            },
            afterAttributeName: function () {
                var char = this.peek();
                if (isSpace(char)) {
                    this.consume();
                    return;
                }
                else if (char === '/') {
                    this.delegate.beginAttributeValue(false);
                    this.delegate.finishAttributeValue();
                    this.consume();
                    this.transitionTo("selfClosingStartTag" /* selfClosingStartTag */);
                }
                else if (char === '=') {
                    this.consume();
                    this.transitionTo("beforeAttributeValue" /* beforeAttributeValue */);
                }
                else if (char === '>') {
                    this.delegate.beginAttributeValue(false);
                    this.delegate.finishAttributeValue();
                    this.consume();
                    this.delegate.finishTag();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    this.delegate.beginAttributeValue(false);
                    this.delegate.finishAttributeValue();
                    this.transitionTo("attributeName" /* attributeName */);
                    this.delegate.beginAttribute();
                    this.consume();
                    this.delegate.appendToAttributeName(char);
                }
            },
            beforeAttributeValue: function () {
                var char = this.peek();
                if (isSpace(char)) {
                    this.consume();
                }
                else if (char === '"') {
                    this.transitionTo("attributeValueDoubleQuoted" /* attributeValueDoubleQuoted */);
                    this.delegate.beginAttributeValue(true);
                    this.consume();
                }
                else if (char === "'") {
                    this.transitionTo("attributeValueSingleQuoted" /* attributeValueSingleQuoted */);
                    this.delegate.beginAttributeValue(true);
                    this.consume();
                }
                else if (char === '>') {
                    this.delegate.beginAttributeValue(false);
                    this.delegate.finishAttributeValue();
                    this.consume();
                    this.delegate.finishTag();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    this.transitionTo("attributeValueUnquoted" /* attributeValueUnquoted */);
                    this.delegate.beginAttributeValue(false);
                    this.consume();
                    this.delegate.appendToAttributeValue(char);
                }
            },
            attributeValueDoubleQuoted: function () {
                var char = this.consume();
                if (char === '"') {
                    this.delegate.finishAttributeValue();
                    this.transitionTo("afterAttributeValueQuoted" /* afterAttributeValueQuoted */);
                }
                else if (char === '&') {
                    this.delegate.appendToAttributeValue(this.consumeCharRef() || '&');
                }
                else {
                    this.delegate.appendToAttributeValue(char);
                }
            },
            attributeValueSingleQuoted: function () {
                var char = this.consume();
                if (char === "'") {
                    this.delegate.finishAttributeValue();
                    this.transitionTo("afterAttributeValueQuoted" /* afterAttributeValueQuoted */);
                }
                else if (char === '&') {
                    this.delegate.appendToAttributeValue(this.consumeCharRef() || '&');
                }
                else {
                    this.delegate.appendToAttributeValue(char);
                }
            },
            attributeValueUnquoted: function () {
                var char = this.peek();
                if (isSpace(char)) {
                    this.delegate.finishAttributeValue();
                    this.consume();
                    this.transitionTo("beforeAttributeName" /* beforeAttributeName */);
                }
                else if (char === '/') {
                    this.delegate.finishAttributeValue();
                    this.consume();
                    this.transitionTo("selfClosingStartTag" /* selfClosingStartTag */);
                }
                else if (char === '&') {
                    this.consume();
                    this.delegate.appendToAttributeValue(this.consumeCharRef() || '&');
                }
                else if (char === '>') {
                    this.delegate.finishAttributeValue();
                    this.consume();
                    this.delegate.finishTag();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    this.consume();
                    this.delegate.appendToAttributeValue(char);
                }
            },
            afterAttributeValueQuoted: function () {
                var char = this.peek();
                if (isSpace(char)) {
                    this.consume();
                    this.transitionTo("beforeAttributeName" /* beforeAttributeName */);
                }
                else if (char === '/') {
                    this.consume();
                    this.transitionTo("selfClosingStartTag" /* selfClosingStartTag */);
                }
                else if (char === '>') {
                    this.consume();
                    this.delegate.finishTag();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    this.transitionTo("beforeAttributeName" /* beforeAttributeName */);
                }
            },
            selfClosingStartTag: function () {
                var char = this.peek();
                if (char === '>') {
                    this.consume();
                    this.delegate.markTagAsSelfClosing();
                    this.delegate.finishTag();
                    this.transitionTo("beforeData" /* beforeData */);
                }
                else {
                    this.transitionTo("beforeAttributeName" /* beforeAttributeName */);
                }
            },
            endTagOpen: function () {
                var char = this.consume();
                if (char === '@' || char === ':' || isAlpha(char)) {
                    this.transitionTo("endTagName" /* endTagName */);
                    this.tagNameBuffer = '';
                    this.delegate.beginEndTag();
                    this.appendToTagName(char);
                }
            }
        };
        this.reset();
    }
    EventedTokenizer.prototype.reset = function () {
        this.transitionTo("beforeData" /* beforeData */);
        this.input = '';
        this.tagNameBuffer = '';
        this.index = 0;
        this.line = 1;
        this.column = 0;
        this.delegate.reset();
    };
    EventedTokenizer.prototype.transitionTo = function (state) {
        this.state = state;
    };
    EventedTokenizer.prototype.tokenize = function (input) {
        this.reset();
        this.tokenizePart(input);
        this.tokenizeEOF();
    };
    EventedTokenizer.prototype.tokenizePart = function (input) {
        this.input += preprocessInput(input);
        while (this.index < this.input.length) {
            var handler = this.states[this.state];
            if (handler !== undefined) {
                handler.call(this);
            }
            else {
                throw new Error("unhandled state " + this.state);
            }
        }
    };
    EventedTokenizer.prototype.tokenizeEOF = function () {
        this.flushData();
    };
    EventedTokenizer.prototype.flushData = function () {
        if (this.state === 'data') {
            this.delegate.finishData();
            this.transitionTo("beforeData" /* beforeData */);
        }
    };
    EventedTokenizer.prototype.peek = function () {
        return this.input.charAt(this.index);
    };
    EventedTokenizer.prototype.consume = function () {
        var char = this.peek();
        this.index++;
        if (char === '\n') {
            this.line++;
            this.column = 0;
        }
        else {
            this.column++;
        }
        return char;
    };
    EventedTokenizer.prototype.consumeCharRef = function () {
        var endIndex = this.input.indexOf(';', this.index);
        if (endIndex === -1) {
            return;
        }
        var entity = this.input.slice(this.index, endIndex);
        var chars = this.entityParser.parse(entity);
        if (chars) {
            var count = entity.length;
            // consume the entity chars
            while (count) {
                this.consume();
                count--;
            }
            // consume the `;`
            this.consume();
            return chars;
        }
    };
    EventedTokenizer.prototype.markTagStart = function () {
        this.delegate.tagOpen();
    };
    EventedTokenizer.prototype.appendToTagName = function (char) {
        this.tagNameBuffer += char;
        this.delegate.appendToTagName(char);
    };
    EventedTokenizer.prototype.isIgnoredEndTag = function () {
        var tag = this.tagNameBuffer;
        return (tag === 'title' && this.input.substring(this.index, this.index + 8) !== '</title>') ||
            (tag === 'style' && this.input.substring(this.index, this.index + 8) !== '</style>') ||
            (tag === 'script' && this.input.substring(this.index, this.index + 9) !== '</script>');
    };
    return EventedTokenizer;
}());

var Tokenizer = /** @class */ (function () {
    function Tokenizer(entityParser, options) {
        if (options === void 0) { options = {}; }
        this.options = options;
        this.token = null;
        this.startLine = 1;
        this.startColumn = 0;
        this.tokens = [];
        this.tokenizer = new EventedTokenizer(this, entityParser, options.mode);
        this._currentAttribute = undefined;
    }
    Tokenizer.prototype.tokenize = function (input) {
        this.tokens = [];
        this.tokenizer.tokenize(input);
        return this.tokens;
    };
    Tokenizer.prototype.tokenizePart = function (input) {
        this.tokens = [];
        this.tokenizer.tokenizePart(input);
        return this.tokens;
    };
    Tokenizer.prototype.tokenizeEOF = function () {
        this.tokens = [];
        this.tokenizer.tokenizeEOF();
        return this.tokens[0];
    };
    Tokenizer.prototype.reset = function () {
        this.token = null;
        this.startLine = 1;
        this.startColumn = 0;
    };
    Tokenizer.prototype.current = function () {
        var token = this.token;
        if (token === null) {
            throw new Error('token was unexpectedly null');
        }
        if (arguments.length === 0) {
            return token;
        }
        for (var i = 0; i < arguments.length; i++) {
            if (token.type === arguments[i]) {
                return token;
            }
        }
        throw new Error("token type was unexpectedly " + token.type);
    };
    Tokenizer.prototype.push = function (token) {
        this.token = token;
        this.tokens.push(token);
    };
    Tokenizer.prototype.currentAttribute = function () {
        return this._currentAttribute;
    };
    Tokenizer.prototype.addLocInfo = function () {
        if (this.options.loc) {
            this.current().loc = {
                start: {
                    line: this.startLine,
                    column: this.startColumn
                },
                end: {
                    line: this.tokenizer.line,
                    column: this.tokenizer.column
                }
            };
        }
        this.startLine = this.tokenizer.line;
        this.startColumn = this.tokenizer.column;
    };
    // Data
    Tokenizer.prototype.beginDoctype = function () {
        this.push({
            type: "Doctype" /* Doctype */,
            name: '',
        });
    };
    Tokenizer.prototype.appendToDoctypeName = function (char) {
        this.current("Doctype" /* Doctype */).name += char;
    };
    Tokenizer.prototype.appendToDoctypePublicIdentifier = function (char) {
        var doctype = this.current("Doctype" /* Doctype */);
        if (doctype.publicIdentifier === undefined) {
            doctype.publicIdentifier = char;
        }
        else {
            doctype.publicIdentifier += char;
        }
    };
    Tokenizer.prototype.appendToDoctypeSystemIdentifier = function (char) {
        var doctype = this.current("Doctype" /* Doctype */);
        if (doctype.systemIdentifier === undefined) {
            doctype.systemIdentifier = char;
        }
        else {
            doctype.systemIdentifier += char;
        }
    };
    Tokenizer.prototype.endDoctype = function () {
        this.addLocInfo();
    };
    Tokenizer.prototype.beginData = function () {
        this.push({
            type: "Chars" /* Chars */,
            chars: ''
        });
    };
    Tokenizer.prototype.appendToData = function (char) {
        this.current("Chars" /* Chars */).chars += char;
    };
    Tokenizer.prototype.finishData = function () {
        this.addLocInfo();
    };
    // Comment
    Tokenizer.prototype.beginComment = function () {
        this.push({
            type: "Comment" /* Comment */,
            chars: ''
        });
    };
    Tokenizer.prototype.appendToCommentData = function (char) {
        this.current("Comment" /* Comment */).chars += char;
    };
    Tokenizer.prototype.finishComment = function () {
        this.addLocInfo();
    };
    // Tags - basic
    Tokenizer.prototype.tagOpen = function () { };
    Tokenizer.prototype.beginStartTag = function () {
        this.push({
            type: "StartTag" /* StartTag */,
            tagName: '',
            attributes: [],
            selfClosing: false
        });
    };
    Tokenizer.prototype.beginEndTag = function () {
        this.push({
            type: "EndTag" /* EndTag */,
            tagName: ''
        });
    };
    Tokenizer.prototype.finishTag = function () {
        this.addLocInfo();
    };
    Tokenizer.prototype.markTagAsSelfClosing = function () {
        this.current("StartTag" /* StartTag */).selfClosing = true;
    };
    // Tags - name
    Tokenizer.prototype.appendToTagName = function (char) {
        this.current("StartTag" /* StartTag */, "EndTag" /* EndTag */).tagName += char;
    };
    // Tags - attributes
    Tokenizer.prototype.beginAttribute = function () {
        this._currentAttribute = ['', '', false];
    };
    Tokenizer.prototype.appendToAttributeName = function (char) {
        this.currentAttribute()[0] += char;
    };
    Tokenizer.prototype.beginAttributeValue = function (isQuoted) {
        this.currentAttribute()[2] = isQuoted;
    };
    Tokenizer.prototype.appendToAttributeValue = function (char) {
        this.currentAttribute()[1] += char;
    };
    Tokenizer.prototype.finishAttributeValue = function () {
        this.current("StartTag" /* StartTag */).attributes.push(this._currentAttribute);
    };
    Tokenizer.prototype.reportSyntaxError = function (message) {
        this.current().syntaxError = message;
    };
    return Tokenizer;
}());

function tokenize(input, options) {
    var tokenizer = new Tokenizer(new EntityParser(namedCharRefs), options);
    return tokenizer.tokenize(input);
}



// EXTERNAL MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_ = __webpack_require__("rmEH");

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/validation/logger.js
function createLogger() {
  /**
   * Creates a log handler with block validation prefix.
   *
   * @param {Function} logger Original logger function.
   *
   * @return {Function} Augmented logger function.
   */
  function createLogHandler(logger) {
    let log = (message, ...args) => logger('Block validation: ' + message, ...args); // In test environments, pre-process string substitutions to improve
    // readability of error messages. We'd prefer to avoid pulling in this
    // dependency in runtime environments, and it can be dropped by a combo
    // of Webpack env substitution + UglifyJS dead code elimination.


    if (false) {}

    return log;
  }

  return {
    // eslint-disable-next-line no-console
    error: createLogHandler(console.error),
    // eslint-disable-next-line no-console
    warning: createLogHandler(console.warn),

    getItems() {
      return [];
    }

  };
}
function createQueuedLogger() {
  /**
   * The list of enqueued log actions to print.
   *
   * @type {Array}
   */
  const queue = [];
  const logger = createLogger();
  return {
    error(...args) {
      queue.push({
        log: logger.error,
        args
      });
    },

    warning(...args) {
      queue.push({
        log: logger.warning,
        args
      });
    },

    getItems() {
      return queue;
    }

  };
}

// EXTERNAL MODULE: external ["wp","isShallowEqual"]
var external_wp_isShallowEqual_ = __webpack_require__("rl8x");
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/block-content-provider/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const {
  Consumer,
  Provider
} = Object(external_wp_element_["createContext"])(() => {});
/**
 * An internal block component used in block content serialization to inject
 * nested block content within the `save` implementation of the ancestor
 * component in which it is nested. The component provides a pre-bound
 * `BlockContent` component via context, which is used by the developer-facing
 * `InnerBlocks.Content` component to render block content.
 *
 * @example
 *
 * ```jsx
 * <BlockContentProvider innerBlocks={ innerBlocks }>
 * 	{ blockSaveElement }
 * </BlockContentProvider>
 * ```
 *
 * @param {Object}    props             Component props.
 * @param {WPElement} props.children    Block save result.
 * @param {Array}     props.innerBlocks Block(s) to serialize.
 *
 * @return {WPComponent} Element with BlockContent injected via context.
 */

const BlockContentProvider = ({
  children,
  innerBlocks
}) => {
  const BlockContent = () => {
    // Value is an array of blocks, so defer to block serializer
    const html = serialize(innerBlocks, {
      isInnerBlocks: true
    }); // Use special-cased raw HTML tag to avoid default escaping

    return Object(external_wp_element_["createElement"])(external_wp_element_["RawHTML"], null, html);
  };

  return Object(external_wp_element_["createElement"])(Provider, {
    value: BlockContent
  }, children);
};
/**
 * A Higher Order Component used to inject BlockContent using context to the
 * wrapped component.
 *
 * @return {WPComponent} Enhanced component with injected BlockContent as prop.
 */


const withBlockContentContext = Object(external_wp_compose_["createHigherOrderComponent"])(OriginalComponent => {
  return props => Object(external_wp_element_["createElement"])(Consumer, null, context => Object(external_wp_element_["createElement"])(OriginalComponent, Object(esm_extends["a" /* default */])({}, props, {
    BlockContent: context
  })));
}, 'withBlockContentContext');
/* harmony default export */ var block_content_provider = (BlockContentProvider);

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/serializer.js


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
 * @typedef {Object} WPBlockSerializationOptions Serialization Options.
 *
 * @property {boolean} isInnerBlocks Whether we are serializing inner blocks.
 */

/**
 * Returns the block's default classname from its name.
 *
 * @param {string} blockName The block name.
 *
 * @return {string} The block's default class.
 */

function getBlockDefaultClassName(blockName) {
  // Generated HTML classes for blocks follow the `wp-block-{name}` nomenclature.
  // Blocks provided by WordPress drop the prefixes 'core/' or 'core-' (historically used in 'core-embed/').
  const className = 'wp-block-' + blockName.replace(/\//, '-').replace(/^core-/, '');
  return Object(external_wp_hooks_["applyFilters"])('blocks.getBlockDefaultClassName', className, blockName);
}
/**
 * Returns the block's default menu item classname from its name.
 *
 * @param {string} blockName The block name.
 *
 * @return {string} The block's default menu item class.
 */

function getBlockMenuDefaultClassName(blockName) {
  // Generated HTML classes for blocks follow the `editor-block-list-item-{name}` nomenclature.
  // Blocks provided by WordPress drop the prefixes 'core/' or 'core-' (historically used in 'core-embed/').
  const className = 'editor-block-list-item-' + blockName.replace(/\//, '-').replace(/^core-/, '');
  return Object(external_wp_hooks_["applyFilters"])('blocks.getBlockMenuDefaultClassName', className, blockName);
}
const blockPropsProvider = {};
/**
 * Call within a save function to get the props for the block wrapper.
 *
 * @param {Object} props Optional. Props to pass to the element.
 */

function getBlockProps(props = {}) {
  const {
    blockType,
    attributes
  } = blockPropsProvider;
  return Object(external_wp_hooks_["applyFilters"])('blocks.getSaveContent.extraProps', { ...props
  }, blockType, attributes);
}
/**
 * Given a block type containing a save render implementation and attributes, returns the
 * enhanced element to be saved or string when raw HTML expected.
 *
 * @param {string|Object} blockTypeOrName   Block type or name.
 * @param {Object}        attributes        Block attributes.
 * @param {?Array}        innerBlocks       Nested blocks.
 *
 * @return {Object|string} Save element or raw HTML string.
 */

function getSaveElement(blockTypeOrName, attributes, innerBlocks = []) {
  const blockType = normalizeBlockType(blockTypeOrName);
  let {
    save
  } = blockType; // Component classes are unsupported for save since serialization must
  // occur synchronously. For improved interoperability with higher-order
  // components which often return component class, emulate basic support.

  if (save.prototype instanceof external_wp_element_["Component"]) {
    const instance = new save({
      attributes
    });
    save = instance.render.bind(instance);
  }

  blockPropsProvider.blockType = blockType;
  blockPropsProvider.attributes = attributes;
  let element = save({
    attributes,
    innerBlocks
  });
  const hasLightBlockWrapper = blockType.apiVersion > 1 || registration_hasBlockSupport(blockType, 'lightBlockWrapper', false);

  if (Object(external_lodash_["isObject"])(element) && Object(external_wp_hooks_["hasFilter"])('blocks.getSaveContent.extraProps') && !hasLightBlockWrapper) {
    /**
     * Filters the props applied to the block save result element.
     *
     * @param {Object}  props      Props applied to save element.
     * @param {WPBlock} blockType  Block type definition.
     * @param {Object}  attributes Block attributes.
     */
    const props = Object(external_wp_hooks_["applyFilters"])('blocks.getSaveContent.extraProps', { ...element.props
    }, blockType, attributes);

    if (!external_wp_isShallowEqual_default()(props, element.props)) {
      element = Object(external_wp_element_["cloneElement"])(element, props);
    }
  }
  /**
   * Filters the save result of a block during serialization.
   *
   * @param {WPElement} element    Block save result.
   * @param {WPBlock}   blockType  Block type definition.
   * @param {Object}    attributes Block attributes.
   */


  element = Object(external_wp_hooks_["applyFilters"])('blocks.getSaveElement', element, blockType, attributes);
  return Object(external_wp_element_["createElement"])(block_content_provider, {
    innerBlocks: innerBlocks
  }, element);
}
/**
 * Given a block type containing a save render implementation and attributes, returns the
 * static markup to be saved.
 *
 * @param {string|Object} blockTypeOrName Block type or name.
 * @param {Object}        attributes      Block attributes.
 * @param {?Array}        innerBlocks     Nested blocks.
 *
 * @return {string} Save content.
 */

function getSaveContent(blockTypeOrName, attributes, innerBlocks) {
  const blockType = normalizeBlockType(blockTypeOrName);
  return Object(external_wp_element_["renderToString"])(getSaveElement(blockType, attributes, innerBlocks));
}
/**
 * Returns attributes which are to be saved and serialized into the block
 * comment delimiter.
 *
 * When a block exists in memory it contains as its attributes both those
 * parsed the block comment delimiter _and_ those which matched from the
 * contents of the block.
 *
 * This function returns only those attributes which are needed to persist and
 * which cannot be matched from the block content.
 *
 * @param {Object<string,*>} blockType     Block type.
 * @param {Object<string,*>} attributes Attributes from in-memory block data.
 *
 * @return {Object<string,*>} Subset of attributes for comment serialization.
 */

function getCommentAttributes(blockType, attributes) {
  return Object(external_lodash_["reduce"])(blockType.attributes, (accumulator, attributeSchema, key) => {
    const value = attributes[key]; // Ignore undefined values.

    if (undefined === value) {
      return accumulator;
    } // Ignore all attributes but the ones with an "undefined" source
    // "undefined" source refers to attributes saved in the block comment.


    if (attributeSchema.source !== undefined) {
      return accumulator;
    } // Ignore default value.


    if ('default' in attributeSchema && attributeSchema.default === value) {
      return accumulator;
    } // Otherwise, include in comment set.


    accumulator[key] = value;
    return accumulator;
  }, {});
}
/**
 * Given an attributes object, returns a string in the serialized attributes
 * format prepared for post content.
 *
 * @param {Object} attributes Attributes object.
 *
 * @return {string} Serialized attributes.
 */

function serializeAttributes(attributes) {
  return JSON.stringify(attributes) // Don't break HTML comments.
  .replace(/--/g, '\\u002d\\u002d') // Don't break non-standard-compliant tools.
  .replace(/</g, '\\u003c').replace(/>/g, '\\u003e').replace(/&/g, '\\u0026') // Bypass server stripslashes behavior which would unescape stringify's
  // escaping of quotation mark.
  //
  // See: https://developer.wordpress.org/reference/functions/wp_kses_stripslashes/
  .replace(/\\"/g, '\\u0022');
}
/**
 * Given a block object, returns the Block's Inner HTML markup.
 *
 * @param {Object} block Block instance.
 *
 * @return {string} HTML.
 */

function getBlockInnerHTML(block) {
  // If block was parsed as invalid or encounters an error while generating
  // save content, use original content instead to avoid content loss. If a
  // block contains nested content, exempt it from this condition because we
  // otherwise have no access to its original content and content loss would
  // still occur.
  let saveContent = block.originalContent;

  if (block.isValid || block.innerBlocks.length) {
    try {
      saveContent = getSaveContent(block.name, block.attributes, block.innerBlocks);
    } catch (error) {}
  }

  return saveContent;
}
/**
 * Returns the content of a block, including comment delimiters.
 *
 * @param {string} rawBlockName Block name.
 * @param {Object} attributes   Block attributes.
 * @param {string} content      Block save content.
 *
 * @return {string} Comment-delimited block content.
 */

function getCommentDelimitedContent(rawBlockName, attributes, content) {
  const serializedAttributes = !Object(external_lodash_["isEmpty"])(attributes) ? serializeAttributes(attributes) + ' ' : ''; // Strip core blocks of their namespace prefix.

  const blockName = Object(external_lodash_["startsWith"])(rawBlockName, 'core/') ? rawBlockName.slice(5) : rawBlockName; // @todo make the `wp:` prefix potentially configurable.

  if (!content) {
    return `<!-- wp:${blockName} ${serializedAttributes}/-->`;
  }

  return `<!-- wp:${blockName} ${serializedAttributes}-->\n` + content + `\n<!-- /wp:${blockName} -->`;
}
/**
 * Returns the content of a block, including comment delimiters, determining
 * serialized attributes and content form from the current state of the block.
 *
 * @param {Object}                      block   Block instance.
 * @param {WPBlockSerializationOptions} options Serialization options.
 *
 * @return {string} Serialized block.
 */

function serializeBlock(block, {
  isInnerBlocks = false
} = {}) {
  const blockName = block.name;
  const saveContent = getBlockInnerHTML(block);

  if (blockName === getUnregisteredTypeHandlerName() || !isInnerBlocks && blockName === getFreeformContentHandlerName()) {
    return saveContent;
  }

  const blockType = registration_getBlockType(blockName);
  const saveAttributes = getCommentAttributes(blockType, block.attributes);
  return getCommentDelimitedContent(blockName, saveAttributes, saveContent);
}
function __unstableSerializeAndClean(blocks) {
  // A single unmodified default block is assumed to
  // be equivalent to an empty post.
  if (blocks.length === 1 && isUnmodifiedDefaultBlock(blocks[0])) {
    blocks = [];
  }

  let content = serialize(blocks); // For compatibility, treat a post consisting of a
  // single freeform block as legacy content and apply
  // pre-block-editor removep'd content formatting.

  if (blocks.length === 1 && blocks[0].name === getFreeformContentHandlerName()) {
    content = Object(external_wp_autop_["removep"])(content);
  }

  return content;
}
/**
 * Takes a block or set of blocks and returns the serialized post content.
 *
 * @param {Array}                       blocks  Block(s) to serialize.
 * @param {WPBlockSerializationOptions} options Serialization options.
 *
 * @return {string} The post content.
 */

function serialize(blocks, options) {
  return Object(external_lodash_["castArray"])(blocks).map(block => serializeBlock(block, options)).join('\n\n');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/validation/index.js
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
 * Globally matches any consecutive whitespace
 *
 * @type {RegExp}
 */

const REGEXP_WHITESPACE = /[\t\n\r\v\f ]+/g;
/**
 * Matches a string containing only whitespace
 *
 * @type {RegExp}
 */

const REGEXP_ONLY_WHITESPACE = /^[\t\n\r\v\f ]*$/;
/**
 * Matches a CSS URL type value
 *
 * @type {RegExp}
 */

const REGEXP_STYLE_URL_TYPE = /^url\s*\(['"\s]*(.*?)['"\s]*\)$/;
/**
 * Boolean attributes are attributes whose presence as being assigned is
 * meaningful, even if only empty.
 *
 * See: https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#boolean-attributes
 * Extracted from: https://html.spec.whatwg.org/multipage/indices.html#attributes-3
 *
 * Object.keys( Array.from( document.querySelectorAll( '#attributes-1 > tbody > tr' ) )
 *     .filter( ( tr ) => tr.lastChild.textContent.indexOf( 'Boolean attribute' ) !== -1 )
 *     .reduce( ( result, tr ) => Object.assign( result, {
 *         [ tr.firstChild.textContent.trim() ]: true
 *     } ), {} ) ).sort();
 *
 * @type {Array}
 */

const BOOLEAN_ATTRIBUTES = ['allowfullscreen', 'allowpaymentrequest', 'allowusermedia', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled', 'download', 'formnovalidate', 'hidden', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected', 'typemustmatch'];
/**
 * Enumerated attributes are attributes which must be of a specific value form.
 * Like boolean attributes, these are meaningful if specified, even if not of a
 * valid enumerated value.
 *
 * See: https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#enumerated-attribute
 * Extracted from: https://html.spec.whatwg.org/multipage/indices.html#attributes-3
 *
 * Object.keys( Array.from( document.querySelectorAll( '#attributes-1 > tbody > tr' ) )
 *     .filter( ( tr ) => /^("(.+?)";?\s*)+/.test( tr.lastChild.textContent.trim() ) )
 *     .reduce( ( result, tr ) => Object.assign( result, {
 *         [ tr.firstChild.textContent.trim() ]: true
 *     } ), {} ) ).sort();
 *
 * @type {Array}
 */

const ENUMERATED_ATTRIBUTES = ['autocapitalize', 'autocomplete', 'charset', 'contenteditable', 'crossorigin', 'decoding', 'dir', 'draggable', 'enctype', 'formenctype', 'formmethod', 'http-equiv', 'inputmode', 'kind', 'method', 'preload', 'scope', 'shape', 'spellcheck', 'translate', 'type', 'wrap'];
/**
 * Meaningful attributes are those who cannot be safely ignored when omitted in
 * one HTML markup string and not another.
 *
 * @type {Array}
 */

const MEANINGFUL_ATTRIBUTES = [...BOOLEAN_ATTRIBUTES, ...ENUMERATED_ATTRIBUTES];
/**
 * Array of functions which receive a text string on which to apply normalizing
 * behavior for consideration in text token equivalence, carefully ordered from
 * least-to-most expensive operations.
 *
 * @type {Array}
 */

const TEXT_NORMALIZATIONS = [external_lodash_["identity"], getTextWithCollapsedWhitespace];
/**
 * Regular expression matching a named character reference. In lieu of bundling
 * a full set of references, the pattern covers the minimal necessary to test
 * positively against the full set.
 *
 * "The ampersand must be followed by one of the names given in the named
 * character references section, using the same case."
 *
 * Tested aginst "12.5 Named character references":
 *
 * ```
 * const references = Array.from( document.querySelectorAll(
 *     '#named-character-references-table tr[id^=entity-] td:first-child'
 * ) ).map( ( code ) => code.textContent )
 * references.every( ( reference ) => /^[\da-z]+$/i.test( reference ) )
 * ```
 *
 * @see https://html.spec.whatwg.org/multipage/syntax.html#character-references
 * @see https://html.spec.whatwg.org/multipage/named-characters.html#named-character-references
 *
 * @type {RegExp}
 */

const REGEXP_NAMED_CHARACTER_REFERENCE = /^[\da-z]+$/i;
/**
 * Regular expression matching a decimal character reference.
 *
 * "The ampersand must be followed by a U+0023 NUMBER SIGN character (#),
 * followed by one or more ASCII digits, representing a base-ten integer"
 *
 * @see https://html.spec.whatwg.org/multipage/syntax.html#character-references
 *
 * @type {RegExp}
 */

const REGEXP_DECIMAL_CHARACTER_REFERENCE = /^#\d+$/;
/**
 * Regular expression matching a hexadecimal character reference.
 *
 * "The ampersand must be followed by a U+0023 NUMBER SIGN character (#), which
 * must be followed by either a U+0078 LATIN SMALL LETTER X character (x) or a
 * U+0058 LATIN CAPITAL LETTER X character (X), which must then be followed by
 * one or more ASCII hex digits, representing a hexadecimal integer"
 *
 * @see https://html.spec.whatwg.org/multipage/syntax.html#character-references
 *
 * @type {RegExp}
 */

const REGEXP_HEXADECIMAL_CHARACTER_REFERENCE = /^#x[\da-f]+$/i;
/**
 * Returns true if the given string is a valid character reference segment, or
 * false otherwise. The text should be stripped of `&` and `;` demarcations.
 *
 * @param {string} text Text to test.
 *
 * @return {boolean} Whether text is valid character reference.
 */

function isValidCharacterReference(text) {
  return REGEXP_NAMED_CHARACTER_REFERENCE.test(text) || REGEXP_DECIMAL_CHARACTER_REFERENCE.test(text) || REGEXP_HEXADECIMAL_CHARACTER_REFERENCE.test(text);
}
/**
 * Subsitute EntityParser class for `simple-html-tokenizer` which uses the
 * implementation of `decodeEntities` from `html-entities`, in order to avoid
 * bundling a massive named character reference.
 *
 * @see https://github.com/tildeio/simple-html-tokenizer/tree/HEAD/src/entity-parser.ts
 */

class validation_DecodeEntityParser {
  /**
   * Returns a substitute string for an entity string sequence between `&`
   * and `;`, or undefined if no substitution should occur.
   *
   * @param {string} entity Entity fragment discovered in HTML.
   *
   * @return {?string} Entity substitute value.
   */
  parse(entity) {
    if (isValidCharacterReference(entity)) {
      return Object(external_wp_htmlEntities_["decodeEntities"])('&' + entity + ';');
    }
  }

}
/**
 * Given a specified string, returns an array of strings split by consecutive
 * whitespace, ignoring leading or trailing whitespace.
 *
 * @param {string} text Original text.
 *
 * @return {string[]} Text pieces split on whitespace.
 */

function getTextPiecesSplitOnWhitespace(text) {
  return text.trim().split(REGEXP_WHITESPACE);
}
/**
 * Given a specified string, returns a new trimmed string where all consecutive
 * whitespace is collapsed to a single space.
 *
 * @param {string} text Original text.
 *
 * @return {string} Trimmed text with consecutive whitespace collapsed.
 */

function getTextWithCollapsedWhitespace(text) {
  // This is an overly simplified whitespace comparison. The specification is
  // more prescriptive of whitespace behavior in inline and block contexts.
  //
  // See: https://medium.com/@patrickbrosset/when-does-white-space-matter-in-html-b90e8a7cdd33
  return getTextPiecesSplitOnWhitespace(text).join(' ');
}
/**
 * Returns attribute pairs of the given StartTag token, including only pairs
 * where the value is non-empty or the attribute is a boolean attribute, an
 * enumerated attribute, or a custom data- attribute.
 *
 * @see MEANINGFUL_ATTRIBUTES
 *
 * @param {Object} token StartTag token.
 *
 * @return {Array[]} Attribute pairs.
 */

function getMeaningfulAttributePairs(token) {
  return token.attributes.filter(pair => {
    const [key, value] = pair;
    return value || key.indexOf('data-') === 0 || Object(external_lodash_["includes"])(MEANINGFUL_ATTRIBUTES, key);
  });
}
/**
 * Returns true if two text tokens (with `chars` property) are equivalent, or
 * false otherwise.
 *
 * @param {Object} actual   Actual token.
 * @param {Object} expected Expected token.
 * @param {Object} logger   Validation logger object.
 *
 * @return {boolean} Whether two text tokens are equivalent.
 */

function isEquivalentTextTokens(actual, expected, logger = createLogger()) {
  // This function is intentionally written as syntactically "ugly" as a hot
  // path optimization. Text is progressively normalized in order from least-
  // to-most operationally expensive, until the earliest point at which text
  // can be confidently inferred as being equal.
  let actualChars = actual.chars;
  let expectedChars = expected.chars;

  for (let i = 0; i < TEXT_NORMALIZATIONS.length; i++) {
    const normalize = TEXT_NORMALIZATIONS[i];
    actualChars = normalize(actualChars);
    expectedChars = normalize(expectedChars);

    if (actualChars === expectedChars) {
      return true;
    }
  }

  logger.warning('Expected text `%s`, saw `%s`.', expected.chars, actual.chars);
  return false;
}
/**
 * Given a CSS length value, returns a normalized CSS length value for strict equality
 * comparison.
 *
 * @param {string} value CSS length value.
 *
 * @return {string} Normalized CSS length value.
 */

function getNormalizedLength(value) {
  return 0 === parseFloat(value) ? '0' : value;
}
/**
 * Given a style value, returns a normalized style value for strict equality
 * comparison.
 *
 * @param {string} value Style value.
 *
 * @return {string} Normalized style value.
 */

function getNormalizedStyleValue(value) {
  const textPieces = getTextPiecesSplitOnWhitespace(value);
  const normalizedPieces = textPieces.map(getNormalizedLength);
  const result = normalizedPieces.join(' ');
  return result // Normalize URL type to omit whitespace or quotes
  .replace(REGEXP_STYLE_URL_TYPE, 'url($1)');
}
/**
 * Given a style attribute string, returns an object of style properties.
 *
 * @param {string} text Style attribute.
 *
 * @return {Object} Style properties.
 */

function getStyleProperties(text) {
  const pairs = text // Trim ending semicolon (avoid including in split)
  .replace(/;?\s*$/, '') // Split on property assignment
  .split(';') // For each property assignment...
  .map(style => {
    // ...split further into key-value pairs
    const [key, ...valueParts] = style.split(':');
    const value = valueParts.join(':');
    return [key.trim(), getNormalizedStyleValue(value.trim())];
  });
  return Object(external_lodash_["fromPairs"])(pairs);
}
/**
 * Attribute-specific equality handlers
 *
 * @type {Object}
 */

const isEqualAttributesOfName = {
  class: (actual, expected) => {
    // Class matches if members are the same, even if out of order or
    // superfluous whitespace between.
    return !Object(external_lodash_["xor"])(...[actual, expected].map(getTextPiecesSplitOnWhitespace)).length;
  },
  style: (actual, expected) => {
    return Object(external_lodash_["isEqual"])(...[actual, expected].map(getStyleProperties));
  },
  // For each boolean attribute, mere presence of attribute in both is enough
  // to assume equivalence.
  ...Object(external_lodash_["fromPairs"])(BOOLEAN_ATTRIBUTES.map(attribute => [attribute, external_lodash_["stubTrue"]]))
};
/**
 * Given two sets of attribute tuples, returns true if the attribute sets are
 * equivalent.
 *
 * @param {Array[]} actual   Actual attributes tuples.
 * @param {Array[]} expected Expected attributes tuples.
 * @param {Object}  logger   Validation logger object.
 *
 * @return {boolean} Whether attributes are equivalent.
 */

function isEqualTagAttributePairs(actual, expected, logger = createLogger()) {
  // Attributes is tokenized as tuples. Their lengths should match. This also
  // avoids us needing to check both attributes sets, since if A has any keys
  // which do not exist in B, we know the sets to be different.
  if (actual.length !== expected.length) {
    logger.warning('Expected attributes %o, instead saw %o.', expected, actual);
    return false;
  } // Attributes are not guaranteed to occur in the same order. For validating
  // actual attributes, first convert the set of expected attribute values to
  // an object, for lookup by key.


  const expectedAttributes = {};

  for (let i = 0; i < expected.length; i++) {
    expectedAttributes[expected[i][0].toLowerCase()] = expected[i][1];
  }

  for (let i = 0; i < actual.length; i++) {
    const [name, actualValue] = actual[i];
    const nameLower = name.toLowerCase(); // As noted above, if missing member in B, assume different

    if (!expectedAttributes.hasOwnProperty(nameLower)) {
      logger.warning('Encountered unexpected attribute `%s`.', name);
      return false;
    }

    const expectedValue = expectedAttributes[nameLower];
    const isEqualAttributes = isEqualAttributesOfName[nameLower];

    if (isEqualAttributes) {
      // Defer custom attribute equality handling
      if (!isEqualAttributes(actualValue, expectedValue)) {
        logger.warning('Expected attribute `%s` of value `%s`, saw `%s`.', name, expectedValue, actualValue);
        return false;
      }
    } else if (actualValue !== expectedValue) {
      // Otherwise strict inequality should bail
      logger.warning('Expected attribute `%s` of value `%s`, saw `%s`.', name, expectedValue, actualValue);
      return false;
    }
  }

  return true;
}
/**
 * Token-type-specific equality handlers
 *
 * @type {Object}
 */

const isEqualTokensOfType = {
  StartTag: (actual, expected, logger = createLogger()) => {
    if (actual.tagName !== expected.tagName && // Optimization: Use short-circuit evaluation to defer case-
    // insensitive check on the assumption that the majority case will
    // have exactly equal tag names.
    actual.tagName.toLowerCase() !== expected.tagName.toLowerCase()) {
      logger.warning('Expected tag name `%s`, instead saw `%s`.', expected.tagName, actual.tagName);
      return false;
    }

    return isEqualTagAttributePairs(...[actual, expected].map(getMeaningfulAttributePairs), logger);
  },
  Chars: isEquivalentTextTokens,
  Comment: isEquivalentTextTokens
};
/**
 * Given an array of tokens, returns the first token which is not purely
 * whitespace.
 *
 * Mutates the tokens array.
 *
 * @param {Object[]} tokens Set of tokens to search.
 *
 * @return {Object} Next non-whitespace token.
 */

function getNextNonWhitespaceToken(tokens) {
  let token;

  while (token = tokens.shift()) {
    if (token.type !== 'Chars') {
      return token;
    }

    if (!REGEXP_ONLY_WHITESPACE.test(token.chars)) {
      return token;
    }
  }
}
/**
 * Tokenize an HTML string, gracefully handling any errors thrown during
 * underlying tokenization.
 *
 * @param {string} html   HTML string to tokenize.
 * @param {Object} logger Validation logger object.
 *
 * @return {Object[]|null} Array of valid tokenized HTML elements, or null on error
 */

function getHTMLTokens(html, logger = createLogger()) {
  try {
    return new Tokenizer(new validation_DecodeEntityParser()).tokenize(html);
  } catch (e) {
    logger.warning('Malformed HTML detected: %s', html);
  }

  return null;
}
/**
 * Returns true if the next HTML token closes the current token.
 *
 * @param {Object} currentToken Current token to compare with.
 * @param {Object|undefined} nextToken Next token to compare against.
 *
 * @return {boolean} true if `nextToken` closes `currentToken`, false otherwise
 */


function isClosedByToken(currentToken, nextToken) {
  // Ensure this is a self closed token
  if (!currentToken.selfClosing) {
    return false;
  } // Check token names and determine if nextToken is the closing tag for currentToken


  if (nextToken && nextToken.tagName === currentToken.tagName && nextToken.type === 'EndTag') {
    return true;
  }

  return false;
}
/**
 * Returns true if the given HTML strings are effectively equivalent, or
 * false otherwise. Invalid HTML is not considered equivalent, even if the
 * strings directly match.
 *
 * @param {string} actual   Actual HTML string.
 * @param {string} expected Expected HTML string.
 * @param {Object} logger   Validation logger object.
 *
 * @return {boolean} Whether HTML strings are equivalent.
 */

function isEquivalentHTML(actual, expected, logger = createLogger()) {
  // Short-circuit if markup is identical.
  if (actual === expected) {
    return true;
  } // Tokenize input content and reserialized save content


  const [actualTokens, expectedTokens] = [actual, expected].map(html => getHTMLTokens(html, logger)); // If either is malformed then stop comparing - the strings are not equivalent

  if (!actualTokens || !expectedTokens) {
    return false;
  }

  let actualToken, expectedToken;

  while (actualToken = getNextNonWhitespaceToken(actualTokens)) {
    expectedToken = getNextNonWhitespaceToken(expectedTokens); // Inequal if exhausted all expected tokens

    if (!expectedToken) {
      logger.warning('Expected end of content, instead saw %o.', actualToken);
      return false;
    } // Inequal if next non-whitespace token of each set are not same type


    if (actualToken.type !== expectedToken.type) {
      logger.warning('Expected token of type `%s` (%o), instead saw `%s` (%o).', expectedToken.type, expectedToken, actualToken.type, actualToken);
      return false;
    } // Defer custom token type equality handling, otherwise continue and
    // assume as equal


    const isEqualTokens = isEqualTokensOfType[actualToken.type];

    if (isEqualTokens && !isEqualTokens(actualToken, expectedToken, logger)) {
      return false;
    } // Peek at the next tokens (actual and expected) to see if they close
    // a self-closing tag


    if (isClosedByToken(actualToken, expectedTokens[0])) {
      // Consume the next expected token that closes the current actual
      // self-closing token
      getNextNonWhitespaceToken(expectedTokens);
    } else if (isClosedByToken(expectedToken, actualTokens[0])) {
      // Consume the next actual token that closes the current expected
      // self-closing token
      getNextNonWhitespaceToken(actualTokens);
    }
  }

  if (expectedToken = getNextNonWhitespaceToken(expectedTokens)) {
    // If any non-whitespace tokens remain in expected token set, this
    // indicates inequality
    logger.warning('Expected %o, instead saw end of content.', expectedToken);
    return false;
  }

  return true;
}
/**
 * Returns an object with `isValid` property set to `true` if the parsed block
 * is valid given the input content. A block is considered valid if, when serialized
 * with assumed attributes, the content matches the original value. If block is
 * invalid, this function returns all validations issues as well.
 *
 * @param {string|Object} blockTypeOrName      Block type.
 * @param {Object}        attributes           Parsed block attributes.
 * @param {string}        originalBlockContent Original block content.
 * @param {Object}        logger           	   Validation logger object.
 *
 * @return {Object} Whether block is valid and contains validation messages.
 */

function getBlockContentValidationResult(blockTypeOrName, attributes, originalBlockContent, logger = createQueuedLogger()) {
  const blockType = normalizeBlockType(blockTypeOrName);
  let generatedBlockContent;

  try {
    generatedBlockContent = getSaveContent(blockType, attributes);
  } catch (error) {
    logger.error('Block validation failed because an error occurred while generating block content:\n\n%s', error.toString());
    return {
      isValid: false,
      validationIssues: logger.getItems()
    };
  }

  const isValid = isEquivalentHTML(originalBlockContent, generatedBlockContent, logger);

  if (!isValid) {
    logger.error('Block validation failed for `%s` (%o).\n\nContent generated by `save` function:\n\n%s\n\nContent retrieved from post body:\n\n%s', blockType.name, blockType, generatedBlockContent, originalBlockContent);
  }

  return {
    isValid,
    validationIssues: logger.getItems()
  };
}
/**
 * Returns true if the parsed block is valid given the input content. A block
 * is considered valid if, when serialized with assumed attributes, the content
 * matches the original value.
 *
 * Logs to console in development environments when invalid.
 *
 * @param {string|Object} blockTypeOrName      Block type.
 * @param {Object}        attributes           Parsed block attributes.
 * @param {string}        originalBlockContent Original block content.
 *
 * @return {boolean} Whether block is valid.
 */

function isValidBlockContent(blockTypeOrName, attributes, originalBlockContent) {
  const {
    isValid
  } = getBlockContentValidationResult(blockTypeOrName, attributes, originalBlockContent, createLogger());
  return isValid;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/matchers.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



function matchers_html(selector, multilineTag) {
  return domNode => {
    let match = domNode;

    if (selector) {
      match = domNode.querySelector(selector);
    }

    if (!match) {
      return '';
    }

    if (multilineTag) {
      let value = '';
      const length = match.children.length;

      for (let index = 0; index < length; index++) {
        const child = match.children[index];

        if (child.nodeName.toLowerCase() !== multilineTag) {
          continue;
        }

        value += child.outerHTML;
      }

      return value;
    }

    return match.innerHTML;
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/node.js
/**
 * Internal dependencies
 */

/**
 * A representation of a single node within a block's rich text value. If
 * representing a text node, the value is simply a string of the node value.
 * As representing an element node, it is an object of:
 *
 * 1. `type` (string): Tag name.
 * 2. `props` (object): Attributes and children array of WPBlockNode.
 *
 * @typedef {string|Object} WPBlockNode
 */

/**
 * Given a single node and a node type (e.g. `'br'`), returns true if the node
 * corresponds to that type, false otherwise.
 *
 * @param {WPBlockNode} node Block node to test
 * @param {string} type      Node to type to test against.
 *
 * @return {boolean} Whether node is of intended type.
 */

function isNodeOfType(node, type) {
  return node && node.type === type;
}
/**
 * Given an object implementing the NamedNodeMap interface, returns a plain
 * object equivalent value of name, value key-value pairs.
 *
 * @see https://dom.spec.whatwg.org/#interface-namednodemap
 *
 * @param {NamedNodeMap} nodeMap NamedNodeMap to convert to object.
 *
 * @return {Object} Object equivalent value of NamedNodeMap.
 */


function getNamedNodeMapAsObject(nodeMap) {
  const result = {};

  for (let i = 0; i < nodeMap.length; i++) {
    const {
      name,
      value
    } = nodeMap[i];
    result[name] = value;
  }

  return result;
}
/**
 * Given a DOM Element or Text node, returns an equivalent block node. Throws
 * if passed any node type other than element or text.
 *
 * @throws {TypeError} If non-element/text node is passed.
 *
 * @param {Node} domNode DOM node to convert.
 *
 * @return {WPBlockNode} Block node equivalent to DOM node.
 */

function fromDOM(domNode) {
  if (domNode.nodeType === domNode.TEXT_NODE) {
    return domNode.nodeValue;
  }

  if (domNode.nodeType !== domNode.ELEMENT_NODE) {
    throw new TypeError('A block node can only be created from a node of type text or ' + 'element.');
  }

  return {
    type: domNode.nodeName.toLowerCase(),
    props: { ...getNamedNodeMapAsObject(domNode.attributes),
      children: children_fromDOM(domNode.childNodes)
    }
  };
}
/**
 * Given a block node, returns its HTML string representation.
 *
 * @param {WPBlockNode} node Block node to convert to string.
 *
 * @return {string} String HTML representation of block node.
 */

function toHTML(node) {
  return children_toHTML([node]);
}
/**
 * Given a selector, returns an hpq matcher generating a WPBlockNode value
 * matching the selector result.
 *
 * @param {string} selector DOM selector.
 *
 * @return {Function} hpq matcher.
 */

function node_matcher(selector) {
  return domNode => {
    let match = domNode;

    if (selector) {
      match = domNode.querySelector(selector);
    }

    try {
      return fromDOM(match);
    } catch (error) {
      return null;
    }
  };
}
/**
 * Object of utility functions used in managing block attribute values of
 * source `node`.
 *
 * @see https://github.com/WordPress/gutenberg/pull/10439
 *
 * @deprecated since 4.0. The `node` source should not be used, and can be
 *             replaced by the `html` source.
 *
 * @private
 */

/* harmony default export */ var api_node = ({
  isNodeOfType,
  fromDOM,
  toHTML,
  matcher: node_matcher
});

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/children.js
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
 * A representation of a block's rich text value.
 *
 * @typedef {WPBlockNode[]} WPBlockChildren
 */

/**
 * Given block children, returns a serialize-capable WordPress element.
 *
 * @param {WPBlockChildren} children Block children object to convert.
 *
 * @return {WPElement} A serialize-capable element.
 */

function getSerializeCapableElement(children) {
  // The fact that block children are compatible with the element serializer is
  // merely an implementation detail that currently serves to be true, but
  // should not be mistaken as being a guarantee on the external API. The
  // public API only offers guarantees to work with strings (toHTML) and DOM
  // elements (fromDOM), and should provide utilities to manipulate the value
  // rather than expect consumers to inspect or construct its shape (concat).
  return children;
}
/**
 * Given block children, returns an array of block nodes.
 *
 * @param {WPBlockChildren} children Block children object to convert.
 *
 * @return {Array<WPBlockNode>} An array of individual block nodes.
 */

function getChildrenArray(children) {
  // The fact that block children are compatible with the element serializer
  // is merely an implementation detail that currently serves to be true, but
  // should not be mistaken as being a guarantee on the external API.
  return children;
}
/**
 * Given two or more block nodes, returns a new block node representing a
 * concatenation of its values.
 *
 * @param {...WPBlockChildren} blockNodes Block nodes to concatenate.
 *
 * @return {WPBlockChildren} Concatenated block node.
 */


function concat(...blockNodes) {
  const result = [];

  for (let i = 0; i < blockNodes.length; i++) {
    const blockNode = Object(external_lodash_["castArray"])(blockNodes[i]);

    for (let j = 0; j < blockNode.length; j++) {
      const child = blockNode[j];
      const canConcatToPreviousString = typeof child === 'string' && typeof result[result.length - 1] === 'string';

      if (canConcatToPreviousString) {
        result[result.length - 1] += child;
      } else {
        result.push(child);
      }
    }
  }

  return result;
}
/**
 * Given an iterable set of DOM nodes, returns equivalent block children.
 * Ignores any non-element/text nodes included in set.
 *
 * @param {Iterable.<Node>} domNodes Iterable set of DOM nodes to convert.
 *
 * @return {WPBlockChildren} Block children equivalent to DOM nodes.
 */

function children_fromDOM(domNodes) {
  const result = [];

  for (let i = 0; i < domNodes.length; i++) {
    try {
      result.push(fromDOM(domNodes[i]));
    } catch (error) {// Simply ignore if DOM node could not be converted.
    }
  }

  return result;
}
/**
 * Given a block node, returns its HTML string representation.
 *
 * @param {WPBlockChildren} children Block node(s) to convert to string.
 *
 * @return {string} String HTML representation of block node.
 */

function children_toHTML(children) {
  const element = getSerializeCapableElement(children);
  return Object(external_wp_element_["renderToString"])(element);
}
/**
 * Given a selector, returns an hpq matcher generating a WPBlockChildren value
 * matching the selector result.
 *
 * @param {string} selector DOM selector.
 *
 * @return {Function} hpq matcher.
 */

function children_matcher(selector) {
  return domNode => {
    let match = domNode;

    if (selector) {
      match = domNode.querySelector(selector);
    }

    if (match) {
      return children_fromDOM(match.childNodes);
    }

    return [];
  };
}
/**
 * Object of utility functions used in managing block attribute values of
 * source `children`.
 *
 * @see https://github.com/WordPress/gutenberg/pull/10439
 *
 * @deprecated since 4.0. The `children` source should not be used, and can be
 *             replaced by the `html` source.
 *
 * @private
 */

/* harmony default export */ var api_children = ({
  concat,
  getChildrenArray,
  fromDOM: children_fromDOM,
  toHTML: children_toHTML,
  matcher: children_matcher
});

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/parser.js
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
 * Sources which are guaranteed to return a string value.
 *
 * @type {Set}
 */

const STRING_SOURCES = new Set(['attribute', 'html', 'text', 'tag']);
/**
 * Higher-order hpq matcher which enhances an attribute matcher to return true
 * or false depending on whether the original matcher returns undefined. This
 * is useful for boolean attributes (e.g. disabled) whose attribute values may
 * be technically falsey (empty string), though their mere presence should be
 * enough to infer as true.
 *
 * @param {Function} matcher Original hpq matcher.
 *
 * @return {Function} Enhanced hpq matcher.
 */

const toBooleanAttributeMatcher = matcher => Object(external_lodash_["flow"])([matcher, // Expected values from `attr( 'disabled' )`:
//
// <input>
// - Value:       `undefined`
// - Transformed: `false`
//
// <input disabled>
// - Value:       `''`
// - Transformed: `true`
//
// <input disabled="disabled">
// - Value:       `'disabled'`
// - Transformed: `true`
value => value !== undefined]);
/**
 * Returns true if value is of the given JSON schema type, or false otherwise.
 *
 * @see http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.25
 *
 * @param {*}      value Value to test.
 * @param {string} type  Type to test.
 *
 * @return {boolean} Whether value is of type.
 */

function isOfType(value, type) {
  switch (type) {
    case 'string':
      return typeof value === 'string';

    case 'boolean':
      return typeof value === 'boolean';

    case 'object':
      return !!value && value.constructor === Object;

    case 'null':
      return value === null;

    case 'array':
      return Array.isArray(value);

    case 'integer':
    case 'number':
      return typeof value === 'number';
  }

  return true;
}
/**
 * Returns true if value is of an array of given JSON schema types, or false
 * otherwise.
 *
 * @see http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.25
 *
 * @param {*}        value Value to test.
 * @param {string[]} types Types to test.
 *
 * @return {boolean} Whether value is of types.
 */

function isOfTypes(value, types) {
  return types.some(type => isOfType(value, type));
}
/**
 * Returns true if value is valid per the given block attribute schema type
 * definition, or false otherwise.
 *
 * @see https://json-schema.org/latest/json-schema-validation.html#rfc.section.6.1.1
 *
 * @param {*}                       value Value to test.
 * @param {?(Array<string>|string)} type  Block attribute schema type.
 *
 * @return {boolean} Whether value is valid.
 */

function isValidByType(value, type) {
  return type === undefined || isOfTypes(value, Object(external_lodash_["castArray"])(type));
}
/**
 * Returns true if value is valid per the given block attribute schema enum
 * definition, or false otherwise.
 *
 * @see https://json-schema.org/latest/json-schema-validation.html#rfc.section.6.1.2
 *
 * @param {*}      value   Value to test.
 * @param {?Array} enumSet Block attribute schema enum.
 *
 * @return {boolean} Whether value is valid.
 */

function isValidByEnum(value, enumSet) {
  return !Array.isArray(enumSet) || enumSet.includes(value);
}
/**
 * Returns true if the given attribute schema describes a value which may be
 * an ambiguous string.
 *
 * Some sources are ambiguously serialized as strings, for which value casting
 * is enabled. This is only possible when a singular type is assigned to the
 * attribute schema, since the string ambiguity makes it impossible to know the
 * correct type of multiple to which to cast.
 *
 * @param {Object} attributeSchema Attribute's schema.
 *
 * @return {boolean} Whether attribute schema defines an ambiguous string
 *                   source.
 */

function isAmbiguousStringSource(attributeSchema) {
  const {
    source,
    type
  } = attributeSchema;
  const isStringSource = STRING_SOURCES.has(source);
  const isSingleType = typeof type === 'string';
  return isStringSource && isSingleType;
}
/**
 * Returns an hpq matcher given a source object.
 *
 * @param {Object} sourceConfig Attribute Source object.
 *
 * @return {Function} A hpq Matcher.
 */

function matcherFromSource(sourceConfig) {
  switch (sourceConfig.source) {
    case 'attribute':
      let matcher = attr(sourceConfig.selector, sourceConfig.attribute);

      if (sourceConfig.type === 'boolean') {
        matcher = toBooleanAttributeMatcher(matcher);
      }

      return matcher;

    case 'html':
      return matchers_html(sourceConfig.selector, sourceConfig.multiline);

    case 'text':
      return es_text(sourceConfig.selector);

    case 'children':
      return children_matcher(sourceConfig.selector);

    case 'node':
      return node_matcher(sourceConfig.selector);

    case 'query':
      const subMatchers = Object(external_lodash_["mapValues"])(sourceConfig.query, matcherFromSource);
      return query(sourceConfig.selector, subMatchers);

    case 'tag':
      return Object(external_lodash_["flow"])([prop(sourceConfig.selector, 'nodeName'), nodeName => nodeName ? nodeName.toLowerCase() : undefined]);

    default:
      // eslint-disable-next-line no-console
      console.error(`Unknown source type "${sourceConfig.source}"`);
  }
}
/**
 * Given a block's raw content and an attribute's schema returns the attribute's
 * value depending on its source.
 *
 * @param {string} innerHTML         Block's raw content.
 * @param {Object} attributeSchema   Attribute's schema.
 *
 * @return {*} Attribute value.
 */

function parseWithAttributeSchema(innerHTML, attributeSchema) {
  return parse(innerHTML, matcherFromSource(attributeSchema));
}
/**
 * Given an attribute key, an attribute's schema, a block's raw content and the
 * commentAttributes returns the attribute value depending on its source
 * definition of the given attribute key.
 *
 * @param {string} attributeKey      Attribute key.
 * @param {Object} attributeSchema   Attribute's schema.
 * @param {string} innerHTML         Block's raw content.
 * @param {Object} commentAttributes Block's comment attributes.
 *
 * @return {*} Attribute value.
 */

function getBlockAttribute(attributeKey, attributeSchema, innerHTML, commentAttributes) {
  const {
    type,
    enum: enumSet
  } = attributeSchema;
  let value;

  switch (attributeSchema.source) {
    // An undefined source means that it's an attribute serialized to the
    // block's "comment".
    case undefined:
      value = commentAttributes ? commentAttributes[attributeKey] : undefined;
      break;

    case 'attribute':
    case 'property':
    case 'html':
    case 'text':
    case 'children':
    case 'node':
    case 'query':
    case 'tag':
      value = parseWithAttributeSchema(innerHTML, attributeSchema);
      break;
  }

  if (!isValidByType(value, type) || !isValidByEnum(value, enumSet)) {
    // Reject the value if it is not valid. Reverting to the undefined
    // value ensures the default is respected, if applicable.
    value = undefined;
  }

  if (value === undefined) {
    return attributeSchema.default;
  }

  return value;
}
/**
 * Returns the block attributes of a registered block node given its type.
 *
 * @param {string|Object} blockTypeOrName Block type or name.
 * @param {string}        innerHTML       Raw block content.
 * @param {?Object}       attributes      Known block attributes (from delimiters).
 *
 * @return {Object} All block attributes.
 */

function getBlockAttributes(blockTypeOrName, innerHTML, attributes = {}) {
  const blockType = normalizeBlockType(blockTypeOrName);
  const blockAttributes = Object(external_lodash_["mapValues"])(blockType.attributes, (attributeSchema, attributeKey) => {
    return getBlockAttribute(attributeKey, attributeSchema, innerHTML, attributes);
  });
  return Object(external_wp_hooks_["applyFilters"])('blocks.getBlockAttributes', blockAttributes, blockType, innerHTML, attributes);
}
/**
 * Given a block object, returns a new copy of the block with any applicable
 * deprecated migrations applied, or the original block if it was both valid
 * and no eligible migrations exist.
 *
 * @param {WPBlock} block            Original block object.
 * @param {Object}  parsedAttributes Attributes as parsed from the initial
 *                                   block markup.
 *
 * @return {WPBlock} Migrated block object.
 */

function getMigratedBlock(block, parsedAttributes) {
  const blockType = registration_getBlockType(block.name);
  const {
    deprecated: deprecatedDefinitions
  } = blockType; // Bail early if there are no registered deprecations to be handled.

  if (!deprecatedDefinitions || !deprecatedDefinitions.length) {
    return block;
  }

  const {
    originalContent,
    innerBlocks
  } = block; // By design, blocks lack any sort of version tracking. Instead, to process
  // outdated content the system operates a queue out of all the defined
  // attribute shapes and tries each definition until the input produces a
  // valid result. This mechanism seeks to avoid polluting the user-space with
  // machine-specific code. An invalid block is thus a block that could not be
  // matched successfully with any of the registered deprecation definitions.

  for (let i = 0; i < deprecatedDefinitions.length; i++) {
    // A block can opt into a migration even if the block is valid by
    // defining `isEligible` on its deprecation. If the block is both valid
    // and does not opt to migrate, skip.
    const {
      isEligible = external_lodash_["stubFalse"]
    } = deprecatedDefinitions[i];

    if (block.isValid && !isEligible(parsedAttributes, innerBlocks)) {
      continue;
    } // Block type properties which could impact either serialization or
    // parsing are not considered in the deprecated block type by default,
    // and must be explicitly provided.


    const deprecatedBlockType = Object.assign(Object(external_lodash_["omit"])(blockType, DEPRECATED_ENTRY_KEYS), deprecatedDefinitions[i]);
    let migratedAttributes = getBlockAttributes(deprecatedBlockType, originalContent, parsedAttributes); // Ignore the deprecation if it produces a block which is not valid.

    const {
      isValid,
      validationIssues
    } = getBlockContentValidationResult(deprecatedBlockType, migratedAttributes, originalContent); // An invalid block does not imply incorrect HTML but the fact block
    // source information could be lost on reserialization.

    if (!isValid) {
      block = { ...block,
        validationIssues: [...Object(external_lodash_["get"])(block, 'validationIssues', []), ...validationIssues]
      };
      continue;
    }

    let migratedInnerBlocks = innerBlocks; // A block may provide custom behavior to assign new attributes and/or
    // inner blocks.

    const {
      migrate
    } = deprecatedBlockType;

    if (migrate) {
      [migratedAttributes = parsedAttributes, migratedInnerBlocks = innerBlocks] = Object(external_lodash_["castArray"])(migrate(migratedAttributes, innerBlocks));
    }

    block = { ...block,
      attributes: migratedAttributes,
      innerBlocks: migratedInnerBlocks,
      isValid: true
    };
  }

  return block;
}
/**
 * Convert legacy blocks to their canonical form. This function is used
 * both in the parser level for previous content and to convert such blocks
 * used in Custom Post Types templates.
 *
 * @param {string} name The block's name
 * @param {Object} attributes The block's attributes
 *
 * @return {Object} The block's name and attributes, changed accordingly if a match was found
 */

function convertLegacyBlocks(name, attributes) {
  const newAttributes = { ...attributes
  }; // Convert 'core/cover-image' block in existing content to 'core/cover'.

  if ('core/cover-image' === name) {
    name = 'core/cover';
  } // Convert 'core/text' blocks in existing content to 'core/paragraph'.


  if ('core/text' === name || 'core/cover-text' === name) {
    name = 'core/paragraph';
  } // Convert derivative blocks such as 'core/social-link-wordpress' to the
  // canonical form 'core/social-link'.


  if (name && name.indexOf('core/social-link-') === 0) {
    // Capture `social-link-wordpress` into `{"service":"wordpress"}`
    newAttributes.service = name.substring(17);
    name = 'core/social-link';
  } // Convert derivative blocks such as 'core-embed/instagram' to the
  // canonical form 'core/embed'.


  if (name && name.indexOf('core-embed/') === 0) {
    // Capture `core-embed/instagram` into `{"providerNameSlug":"instagram"}`
    const providerSlug = name.substring(11);
    const deprecated = {
      speaker: 'speaker-deck',
      polldaddy: 'crowdsignal'
    };
    newAttributes.providerNameSlug = providerSlug in deprecated ? deprecated[providerSlug] : providerSlug; // this is needed as the `responsive` attribute was passed
    // in a different way before the refactoring to block variations

    if (!['amazon-kindle', 'wordpress'].includes(providerSlug)) {
      newAttributes.responsive = true;
    }

    name = 'core/embed';
  } // Convert 'core/query-loop' blocks in existing content to 'core/post-template'.
  // TODO: Remove this check when WordPress 5.9 is released.


  if (name === 'core/query-loop') {
    name = 'core/post-template';
  }

  return {
    name,
    attributes: newAttributes
  };
}
/**
 * Creates a block with fallback to the unknown type handler.
 *
 * @param {Object} blockNode Parsed block node.
 *
 * @return {?Object} An initialized block object (if possible).
 */

function createBlockWithFallback(blockNode) {
  const {
    blockName: originalName
  } = blockNode; // The fundamental structure of a blocktype includes its attributes, inner
  // blocks, and inner HTML. It is important to distinguish inner blocks from
  // the HTML content of the block as only the latter is relevant for block
  // validation and edit operations.

  let {
    attrs: attributes,
    innerBlocks = [],
    innerHTML
  } = blockNode;
  const {
    innerContent
  } = blockNode; // Blocks that don't have a registered handler are considered freeform.

  const freeformContentFallbackBlock = getFreeformContentHandlerName();
  const unregisteredFallbackBlock = getUnregisteredTypeHandlerName() || freeformContentFallbackBlock;
  attributes = attributes || {}; // Trim content to avoid creation of intermediary freeform segments.

  innerHTML = innerHTML.trim(); // Use type from block content if available. Otherwise, default to the
  // freeform content fallback.

  let name = originalName || freeformContentFallbackBlock;
  ({
    name,
    attributes
  } = convertLegacyBlocks(name, attributes)); // Fallback content may be upgraded from classic content expecting implicit
  // automatic paragraphs, so preserve them. Assumes wpautop is idempotent,
  // meaning there are no negative consequences to repeated autop calls.

  if (name === freeformContentFallbackBlock) {
    innerHTML = Object(external_wp_autop_["autop"])(innerHTML).trim();
  } // Try finding the type for known block name, else fall back again.


  let blockType = registration_getBlockType(name);

  if (!blockType) {
    // Since the constituents of the block node are extracted at the start
    // of the present function, construct a new object rather than reuse
    // `blockNode`.
    const reconstitutedBlockNode = {
      attrs: attributes,
      blockName: originalName,
      innerBlocks,
      innerContent
    }; // Preserve undelimited content for use by the unregistered type
    // handler. A block node's `innerHTML` isn't enough, as that field only
    // carries the block's own HTML and not its nested blocks.

    const originalUndelimitedContent = serializeBlockNode(reconstitutedBlockNode, {
      isCommentDelimited: false
    }); // Preserve full block content for use by the unregistered type
    // handler, block boundaries included.

    const originalContent = serializeBlockNode(reconstitutedBlockNode, {
      isCommentDelimited: true
    }); // If detected as a block which is not registered, preserve comment
    // delimiters in content of unregistered type handler.

    if (name) {
      innerHTML = originalContent;
    }

    name = unregisteredFallbackBlock;
    attributes = {
      originalName,
      originalContent,
      originalUndelimitedContent
    };
    blockType = registration_getBlockType(name);
  } // Coerce inner blocks from parsed form to canonical form.


  innerBlocks = innerBlocks.map(createBlockWithFallback); // Remove `undefined` innerBlocks.
  //
  // This is a temporary fix to prevent unrecoverable TypeErrors when handling unexpectedly
  // empty freeform block nodes. See https://github.com/WordPress/gutenberg/pull/17164.

  innerBlocks = innerBlocks.filter(innerBlock => innerBlock);
  const isFallbackBlock = name === freeformContentFallbackBlock || name === unregisteredFallbackBlock; // Include in set only if type was determined.

  if (!blockType || !innerHTML && isFallbackBlock) {
    return;
  }

  let block = createBlock(name, getBlockAttributes(blockType, innerHTML, attributes), innerBlocks); // Block validation assumes an idempotent operation from source block to serialized block
  // provided there are no changes in attributes. The validation procedure thus compares the
  // provided source value with the serialized output before there are any modifications to
  // the block. When both match, the block is marked as valid.

  if (!isFallbackBlock) {
    const {
      isValid,
      validationIssues
    } = getBlockContentValidationResult(blockType, block.attributes, innerHTML);
    block.isValid = isValid;
    block.validationIssues = validationIssues;
  } // Preserve original content for future use in case the block is parsed
  // as invalid, or future serialization attempt results in an error.


  block.originalContent = block.originalContent || innerHTML; // Ensure all necessary migrations are applied to the block.

  block = getMigratedBlock(block, attributes);

  if (block.validationIssues && block.validationIssues.length > 0) {
    if (block.isValid) {
      // eslint-disable-next-line no-console
      console.info('Block successfully updated for `%s` (%o).\n\nNew content generated by `save` function:\n\n%s\n\nContent retrieved from post body:\n\n%s', blockType.name, blockType, getSaveContent(blockType, block.attributes), block.originalContent);
    } else {
      block.validationIssues.forEach(({
        log,
        args
      }) => log(...args));
    }
  }

  return block;
}
/**
 * Serializes a block node into the native HTML-comment-powered block format.
 * CAVEAT: This function is intended for reserializing blocks as parsed by
 * valid parsers and skips any validation steps. This is NOT a generic
 * serialization function for in-memory blocks. For most purposes, see the
 * following functions available in the `@wordpress/blocks` package:
 *
 * @see serializeBlock
 * @see serialize
 *
 * For more on the format of block nodes as returned by valid parsers:
 *
 * @see `@wordpress/block-serialization-default-parser` package
 * @see `@wordpress/block-serialization-spec-parser` package
 *
 * @param {Object}   blockNode                  A block node as returned by a valid parser.
 * @param {?Object}  options                    Serialization options.
 * @param {?boolean} options.isCommentDelimited Whether to output HTML comments around blocks.
 *
 * @return {string} An HTML string representing a block.
 */

function serializeBlockNode(blockNode, options = {}) {
  const {
    isCommentDelimited = true
  } = options;
  const {
    blockName,
    attrs = {},
    innerBlocks = [],
    innerContent = []
  } = blockNode;
  let childIndex = 0;
  const content = innerContent.map(item => // `null` denotes a nested block, otherwise we have an HTML fragment.
  item !== null ? item : serializeBlockNode(innerBlocks[childIndex++], options)).join('\n').replace(/\n+/g, '\n').trim();
  return isCommentDelimited ? getCommentDelimitedContent(blockName, attrs, content) : content;
}
/**
 * Creates a parse implementation for the post content which returns a list of blocks.
 *
 * @param {Function} parseImplementation Parse implementation.
 *
 * @return {Function} An implementation which parses the post content.
 */

const createParse = parseImplementation => content => parseImplementation(content).reduce((accumulator, blockNode) => {
  const block = createBlockWithFallback(blockNode);

  if (block) {
    accumulator.push(block);
  }

  return accumulator;
}, []);
/**
 * Utilizes an optimized token-driven parser based on the Gutenberg grammar spec
 * defined through a parsing expression grammar to take advantage of the regular
 * cadence provided by block delimiters -- composed syntactically through HTML
 * comments -- which, given a general HTML document as an input, returns a block
 * list array representation.
 *
 * This is a recursive-descent parser that scans linearly once through the input
 * document. Instead of directly recursing it utilizes a trampoline mechanism to
 * prevent stack overflow. This initial pass is mainly interested in separating
 * and isolating the blocks serialized in the document and manifestly not in the
 * content within the blocks.
 *
 * @see
 * https://developer.wordpress.org/block-editor/packages/packages-block-serialization-default-parser/
 *
 * @param {string} content The post content.
 *
 * @return {Array} Block list.
 */


const parseWithGrammar = createParse(external_wp_blockSerializationDefaultParser_["parse"]);
/* harmony default export */ var parser = (parseWithGrammar);

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/get-raw-transforms.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


function getRawTransforms() {
  return Object(external_lodash_["filter"])(getBlockTransforms('from'), {
    type: 'raw'
  }).map(transform => {
    return transform.isMatch ? transform : { ...transform,
      isMatch: node => transform.selector && node.matches(transform.selector)
    };
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/html-to-blocks.js
/**
 * Internal dependencies
 */



/**
 * Converts HTML directly to blocks. Looks for a matching transform for each
 * top-level tag. The HTML should be filtered to not have any text between
 * top-level tags and formatted in a way that blocks can handle the HTML.
 *
 * @param {string} html HTML to convert.
 *
 * @return {Array} An array of blocks.
 */

function htmlToBlocks(html) {
  const doc = document.implementation.createHTMLDocument('');
  doc.body.innerHTML = html;
  return Array.from(doc.body.children).flatMap(node => {
    const rawTransform = findTransform(getRawTransforms(), ({
      isMatch
    }) => isMatch(node));

    if (!rawTransform) {
      return createBlock( // Should not be hardcoded.
      'core/html', getBlockAttributes('core/html', node.outerHTML));
    }

    const {
      transform,
      blockName
    } = rawTransform;

    if (transform) {
      return transform(node);
    }

    return createBlock(blockName, getBlockAttributes(blockName, node.outerHTML));
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/normalise-blocks.js
/**
 * WordPress dependencies
 */

function normaliseBlocks(HTML) {
  const decuDoc = document.implementation.createHTMLDocument('');
  const accuDoc = document.implementation.createHTMLDocument('');
  const decu = decuDoc.body;
  const accu = accuDoc.body;
  decu.innerHTML = HTML;

  while (decu.firstChild) {
    const node = decu.firstChild; // Text nodes: wrap in a paragraph, or append to previous.

    if (node.nodeType === node.TEXT_NODE) {
      if (Object(external_wp_dom_["isEmpty"])(node)) {
        decu.removeChild(node);
      } else {
        if (!accu.lastChild || accu.lastChild.nodeName !== 'P') {
          accu.appendChild(accuDoc.createElement('P'));
        }

        accu.lastChild.appendChild(node);
      } // Element nodes.

    } else if (node.nodeType === node.ELEMENT_NODE) {
      // BR nodes: create a new paragraph on double, or append to previous.
      if (node.nodeName === 'BR') {
        if (node.nextSibling && node.nextSibling.nodeName === 'BR') {
          accu.appendChild(accuDoc.createElement('P'));
          decu.removeChild(node.nextSibling);
        } // Don't append to an empty paragraph.


        if (accu.lastChild && accu.lastChild.nodeName === 'P' && accu.lastChild.hasChildNodes()) {
          accu.lastChild.appendChild(node);
        } else {
          decu.removeChild(node);
        }
      } else if (node.nodeName === 'P') {
        // Only append non-empty paragraph nodes.
        if (Object(external_wp_dom_["isEmpty"])(node)) {
          decu.removeChild(node);
        } else {
          accu.appendChild(node);
        }
      } else if (Object(external_wp_dom_["isPhrasingContent"])(node)) {
        if (!accu.lastChild || accu.lastChild.nodeName !== 'P') {
          accu.appendChild(accuDoc.createElement('P'));
        }

        accu.lastChild.appendChild(node);
      } else {
        accu.appendChild(node);
      }
    } else {
      decu.removeChild(node);
    }
  }

  return accu.innerHTML;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/special-comment-converter.js
/**
 * WordPress dependencies
 */

/**
 * Looks for `<!--nextpage-->` and `<!--more-->` comments, as well as the
 * `<!--more Some text-->` variant and its `<!--noteaser-->` companion,
 * and replaces them with a custom element representing a future block.
 *
 * The custom element is a way to bypass the rest of the `raw-handling`
 * transforms, which would eliminate other kinds of node with which to carry
 * `<!--more-->`'s data: nodes with `data` attributes, empty paragraphs, etc.
 *
 * The custom element is then expected to be recognized by any registered
 * block's `raw` transform.
 *
 * @param {Node}     node The node to be processed.
 * @param {Document} doc  The document of the node.
 * @return {void}
 */

function specialCommentConverter(node, doc) {
  if (node.nodeType !== node.COMMENT_NODE) {
    return;
  }

  if (node.nodeValue === 'nextpage') {
    Object(external_wp_dom_["replace"])(node, createNextpage(doc));
    return;
  }

  if (node.nodeValue.indexOf('more') === 0) {
    // Grab any custom text in the comment.
    const customText = node.nodeValue.slice(4).trim();
    /*
     * When a `<!--more-->` comment is found, we need to look for any
     * `<!--noteaser-->` sibling, but it may not be a direct sibling
     * (whitespace typically lies in between)
     */

    let sibling = node;
    let noTeaser = false;

    while (sibling = sibling.nextSibling) {
      if (sibling.nodeType === sibling.COMMENT_NODE && sibling.nodeValue === 'noteaser') {
        noTeaser = true;
        Object(external_wp_dom_["remove"])(sibling);
        break;
      }
    }

    Object(external_wp_dom_["replace"])(node, createMore(customText, noTeaser, doc));
  }
}

function createMore(customText, noTeaser, doc) {
  const node = doc.createElement('wp-block');
  node.dataset.block = 'core/more';

  if (customText) {
    node.dataset.customText = customText;
  }

  if (noTeaser) {
    // "Boolean" data attribute
    node.dataset.noTeaser = '';
  }

  return node;
}

function createNextpage(doc) {
  const node = doc.createElement('wp-block');
  node.dataset.block = 'core/nextpage';
  return node;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/comment-remover.js
/**
 * WordPress dependencies
 */

/**
 * Looks for comments, and removes them.
 *
 * @param {Node} node The node to be processed.
 * @return {void}
 */

function commentRemover(node) {
  if (node.nodeType === node.COMMENT_NODE) {
    Object(external_wp_dom_["remove"])(node);
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/is-inline-content.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Checks if the given node should be considered inline content, optionally
 * depending on a context tag.
 *
 * @param {Node}   node       Node name.
 * @param {string} contextTag Tag name.
 *
 * @return {boolean} True if the node is inline content, false if nohe.
 */

function isInline(node, contextTag) {
  if (Object(external_wp_dom_["isTextContent"])(node)) {
    return true;
  }

  if (!contextTag) {
    return false;
  }

  const tag = node.nodeName.toLowerCase();
  const inlineAllowedTagGroups = [['ul', 'li', 'ol'], ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']];
  return inlineAllowedTagGroups.some(tagGroup => Object(external_lodash_["difference"])([tag, contextTag], tagGroup).length === 0);
}

function deepCheck(nodes, contextTag) {
  return nodes.every(node => isInline(node, contextTag) && deepCheck(Array.from(node.children), contextTag));
}

function isDoubleBR(node) {
  return node.nodeName === 'BR' && node.previousSibling && node.previousSibling.nodeName === 'BR';
}

function isInlineContent(HTML, contextTag) {
  const doc = document.implementation.createHTMLDocument('');
  doc.body.innerHTML = HTML;
  const nodes = Array.from(doc.body.children);
  return !nodes.some(isDoubleBR) && deepCheck(nodes, contextTag);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/phrasing-content-reducer.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function phrasingContentReducer(node, doc) {
  // In jsdom-jscore, 'node.style' can be null.
  // TODO: Explore fixing this by patching jsdom-jscore.
  if (node.nodeName === 'SPAN' && node.style) {
    const {
      fontWeight,
      fontStyle,
      textDecorationLine,
      textDecoration,
      verticalAlign
    } = node.style;

    if (fontWeight === 'bold' || fontWeight === '700') {
      Object(external_wp_dom_["wrap"])(doc.createElement('strong'), node);
    }

    if (fontStyle === 'italic') {
      Object(external_wp_dom_["wrap"])(doc.createElement('em'), node);
    } // Some DOM implementations (Safari, JSDom) don't support
    // style.textDecorationLine, so we check style.textDecoration as a
    // fallback.


    if (textDecorationLine === 'line-through' || Object(external_lodash_["includes"])(textDecoration, 'line-through')) {
      Object(external_wp_dom_["wrap"])(doc.createElement('s'), node);
    }

    if (verticalAlign === 'super') {
      Object(external_wp_dom_["wrap"])(doc.createElement('sup'), node);
    } else if (verticalAlign === 'sub') {
      Object(external_wp_dom_["wrap"])(doc.createElement('sub'), node);
    }
  } else if (node.nodeName === 'B') {
    node = Object(external_wp_dom_["replaceTag"])(node, 'strong');
  } else if (node.nodeName === 'I') {
    node = Object(external_wp_dom_["replaceTag"])(node, 'em');
  } else if (node.nodeName === 'A') {
    // In jsdom-jscore, 'node.target' can be null.
    // TODO: Explore fixing this by patching jsdom-jscore.
    if (node.target && node.target.toLowerCase() === '_blank') {
      node.rel = 'noreferrer noopener';
    } else {
      node.removeAttribute('target');
      node.removeAttribute('rel');
    }
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/head-remover.js
function headRemover(node) {
  if (node.nodeName !== 'SCRIPT' && node.nodeName !== 'NOSCRIPT' && node.nodeName !== 'TEMPLATE' && node.nodeName !== 'STYLE') {
    return;
  }

  node.parentNode.removeChild(node);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/ms-list-converter.js
/**
 * Browser dependencies
 */
const {
  parseInt: ms_list_converter_parseInt
} = window;

function isList(node) {
  return node.nodeName === 'OL' || node.nodeName === 'UL';
}

function msListConverter(node, doc) {
  if (node.nodeName !== 'P') {
    return;
  }

  const style = node.getAttribute('style');

  if (!style) {
    return;
  } // Quick check.


  if (style.indexOf('mso-list') === -1) {
    return;
  }

  const matches = /mso-list\s*:[^;]+level([0-9]+)/i.exec(style);

  if (!matches) {
    return;
  }

  let level = ms_list_converter_parseInt(matches[1], 10) - 1 || 0;
  const prevNode = node.previousElementSibling; // Add new list if no previous.

  if (!prevNode || !isList(prevNode)) {
    // See https://html.spec.whatwg.org/multipage/grouping-content.html#attr-ol-type.
    const type = node.textContent.trim().slice(0, 1);
    const isNumeric = /[1iIaA]/.test(type);
    const newListNode = doc.createElement(isNumeric ? 'ol' : 'ul');

    if (isNumeric) {
      newListNode.setAttribute('type', type);
    }

    node.parentNode.insertBefore(newListNode, node);
  }

  const listNode = node.previousElementSibling;
  const listType = listNode.nodeName;
  const listItem = doc.createElement('li');
  let receivingNode = listNode; // Remove the first span with list info.

  node.removeChild(node.firstElementChild); // Add content.

  while (node.firstChild) {
    listItem.appendChild(node.firstChild);
  } // Change pointer depending on indentation level.


  while (level--) {
    receivingNode = receivingNode.lastElementChild || receivingNode; // If it's a list, move pointer to the last item.

    if (isList(receivingNode)) {
      receivingNode = receivingNode.lastElementChild || receivingNode;
    }
  } // Make sure we append to a list.


  if (!isList(receivingNode)) {
    receivingNode = receivingNode.appendChild(doc.createElement(listType));
  } // Append the list item to the list.


  receivingNode.appendChild(listItem); // Remove the wrapper paragraph.

  node.parentNode.removeChild(node);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/list-reducer.js
/**
 * WordPress dependencies
 */


function list_reducer_isList(node) {
  return node.nodeName === 'OL' || node.nodeName === 'UL';
}

function shallowTextContent(element) {
  return Array.from(element.childNodes).map(({
    nodeValue = ''
  }) => nodeValue).join('');
}

function listReducer(node) {
  if (!list_reducer_isList(node)) {
    return;
  }

  const list = node;
  const prevElement = node.previousElementSibling; // Merge with previous list if:
  // * There is a previous list of the same type.
  // * There is only one list item.

  if (prevElement && prevElement.nodeName === node.nodeName && list.children.length === 1) {
    // Move all child nodes, including any text nodes, if any.
    while (list.firstChild) {
      prevElement.appendChild(list.firstChild);
    }

    list.parentNode.removeChild(list);
  }

  const parentElement = node.parentNode; // Nested list with empty parent item.

  if (parentElement && parentElement.nodeName === 'LI' && parentElement.children.length === 1 && !/\S/.test(shallowTextContent(parentElement))) {
    const parentListItem = parentElement;
    const prevListItem = parentListItem.previousElementSibling;
    const parentList = parentListItem.parentNode;

    if (prevListItem) {
      prevListItem.appendChild(list);
      parentList.removeChild(parentListItem);
    } else {
      parentList.parentNode.insertBefore(list, parentList);
      parentList.parentNode.removeChild(parentList);
    }
  } // Invalid: OL/UL > OL/UL.


  if (parentElement && list_reducer_isList(parentElement)) {
    const prevListItem = node.previousElementSibling;

    if (prevListItem) {
      prevListItem.appendChild(node);
    } else {
      Object(external_wp_dom_["unwrap"])(node);
    }
  }
}

// EXTERNAL MODULE: external ["wp","blob"]
var external_wp_blob_ = __webpack_require__("xTGt");

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/image-corrector.js
/**
 * WordPress dependencies
 */

/**
 * Browser dependencies
 */

const {
  atob,
  File
} = window;
function imageCorrector(node) {
  if (node.nodeName !== 'IMG') {
    return;
  }

  if (node.src.indexOf('file:') === 0) {
    node.src = '';
  } // This piece cannot be tested outside a browser env.


  if (node.src.indexOf('data:') === 0) {
    const [properties, data] = node.src.split(',');
    const [type] = properties.slice(5).split(';');

    if (!data || !type) {
      node.src = '';
      return;
    }

    let decoded; // Can throw DOMException!

    try {
      decoded = atob(data);
    } catch (e) {
      node.src = '';
      return;
    }

    const uint8Array = new Uint8Array(decoded.length);

    for (let i = 0; i < uint8Array.length; i++) {
      uint8Array[i] = decoded.charCodeAt(i);
    }

    const name = type.replace('/', '.');
    const file = new File([uint8Array], name, {
      type
    });
    node.src = Object(external_wp_blob_["createBlobURL"])(file);
  } // Remove trackers and hardly visible images.


  if (node.height === 1 || node.width === 1) {
    node.parentNode.removeChild(node);
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/blockquote-normaliser.js
/**
 * Internal dependencies
 */

function blockquoteNormaliser(node) {
  if (node.nodeName !== 'BLOCKQUOTE') {
    return;
  }

  node.innerHTML = normaliseBlocks(node.innerHTML);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/figure-content-reducer.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Whether or not the given node is figure content.
 *
 * @param {Node}   node   The node to check.
 * @param {Object} schema The schema to use.
 *
 * @return {boolean} True if figure content, false if not.
 */

function isFigureContent(node, schema) {
  const tag = node.nodeName.toLowerCase(); // We are looking for tags that can be a child of the figure tag, excluding
  // `figcaption` and any phrasing content.

  if (tag === 'figcaption' || Object(external_wp_dom_["isTextContent"])(node)) {
    return false;
  }

  return Object(external_lodash_["has"])(schema, ['figure', 'children', tag]);
}
/**
 * Whether or not the given node can have an anchor.
 *
 * @param {Node}   node   The node to check.
 * @param {Object} schema The schema to use.
 *
 * @return {boolean} True if it can, false if not.
 */


function canHaveAnchor(node, schema) {
  const tag = node.nodeName.toLowerCase();
  return Object(external_lodash_["has"])(schema, ['figure', 'children', 'a', 'children', tag]);
}
/**
 * Wraps the given element in a figure element.
 *
 * @param {Element} element       The element to wrap.
 * @param {Element} beforeElement The element before which to place the figure.
 */


function wrapFigureContent(element, beforeElement = element) {
  const figure = element.ownerDocument.createElement('figure');
  beforeElement.parentNode.insertBefore(figure, beforeElement);
  figure.appendChild(element);
}
/**
 * This filter takes figure content out of paragraphs, wraps it in a figure
 * element, and moves any anchors with it if needed.
 *
 * @param {Node}     node   The node to filter.
 * @param {Document} doc    The document of the node.
 * @param {Object}   schema The schema to use.
 *
 * @return {void}
 */


function figureContentReducer(node, doc, schema) {
  if (!isFigureContent(node, schema)) {
    return;
  }

  let nodeToInsert = node;
  const parentNode = node.parentNode; // If the figure content can have an anchor and its parent is an anchor with
  // only the figure content, take the anchor out instead of just the content.

  if (canHaveAnchor(node, schema) && parentNode.nodeName === 'A' && parentNode.childNodes.length === 1) {
    nodeToInsert = node.parentNode;
  }

  const wrapper = nodeToInsert.closest('p,div'); // If wrapped in a paragraph or div, only extract if it's aligned or if
  // there is no text content.
  // Otherwise, if directly at the root, wrap in a figure element.

  if (wrapper) {
    // In jsdom-jscore, 'node.classList' can be undefined.
    // In this case, default to extract as it offers a better UI experience on mobile.
    if (!node.classList) {
      wrapFigureContent(nodeToInsert, wrapper);
    } else if (node.classList.contains('alignright') || node.classList.contains('alignleft') || node.classList.contains('aligncenter') || !wrapper.textContent.trim()) {
      wrapFigureContent(nodeToInsert, wrapper);
    }
  } else if (nodeToInsert.parentNode.nodeName === 'BODY') {
    wrapFigureContent(nodeToInsert);
  }
}

// EXTERNAL MODULE: external ["wp","shortcode"]
var external_wp_shortcode_ = __webpack_require__("SVSp");

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/shortcode-converter.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





function segmentHTMLToShortcodeBlock(HTML, lastIndex = 0, excludedBlockNames = []) {
  // Get all matches.
  const transformsFrom = getBlockTransforms('from');
  const transformation = findTransform(transformsFrom, transform => excludedBlockNames.indexOf(transform.blockName) === -1 && transform.type === 'shortcode' && Object(external_lodash_["some"])(Object(external_lodash_["castArray"])(transform.tag), tag => Object(external_wp_shortcode_["regexp"])(tag).test(HTML)));

  if (!transformation) {
    return [HTML];
  }

  const transformTags = Object(external_lodash_["castArray"])(transformation.tag);
  const transformTag = Object(external_lodash_["find"])(transformTags, tag => Object(external_wp_shortcode_["regexp"])(tag).test(HTML));
  let match;
  const previousIndex = lastIndex;

  if (match = Object(external_wp_shortcode_["next"])(transformTag, HTML, lastIndex)) {
    lastIndex = match.index + match.content.length;
    const beforeHTML = HTML.substr(0, match.index);
    const afterHTML = HTML.substr(lastIndex); // If the shortcode content does not contain HTML and the shortcode is
    // not on a new line (or in paragraph from Markdown converter),
    // consider the shortcode as inline text, and thus skip conversion for
    // this segment.

    if (!Object(external_lodash_["includes"])(match.shortcode.content || '', '<') && !(/(\n|<p>)\s*$/.test(beforeHTML) && /^\s*(\n|<\/p>)/.test(afterHTML))) {
      return segmentHTMLToShortcodeBlock(HTML, lastIndex);
    } // If a transformation's `isMatch` predicate fails for the inbound
    // shortcode, try again by excluding the current block type.
    //
    // This is the only call to `segmentHTMLToShortcodeBlock` that should
    // ever carry over `excludedBlockNames`. Other calls in the module
    // should skip that argument as a way to reset the exclusion state, so
    // that one `isMatch` fail in an HTML fragment doesn't prevent any
    // valid matches in subsequent fragments.


    if (transformation.isMatch && !transformation.isMatch(match.shortcode.attrs)) {
      return segmentHTMLToShortcodeBlock(HTML, previousIndex, [...excludedBlockNames, transformation.blockName]);
    }

    const attributes = Object(external_lodash_["mapValues"])(Object(external_lodash_["pickBy"])(transformation.attributes, schema => schema.shortcode), // Passing all of `match` as second argument is intentionally broad
    // but shouldn't be too relied upon.
    //
    // See: https://github.com/WordPress/gutenberg/pull/3610#discussion_r152546926
    schema => schema.shortcode(match.shortcode.attrs, match));
    const block = createBlock(transformation.blockName, getBlockAttributes({ ...registration_getBlockType(transformation.blockName),
      attributes: transformation.attributes
    }, match.shortcode.content, attributes));
    return [...segmentHTMLToShortcodeBlock(beforeHTML), block, ...segmentHTMLToShortcodeBlock(afterHTML)];
  }

  return [HTML];
}

/* harmony default export */ var shortcode_converter = (segmentHTMLToShortcodeBlock);

// EXTERNAL MODULE: ./node_modules/showdown/dist/showdown.js
var showdown = __webpack_require__("M55E");
var showdown_default = /*#__PURE__*/__webpack_require__.n(showdown);

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/markdown-converter.js
/**
 * External dependencies
 */
 // Reuse the same showdown converter.

const converter = new showdown_default.a.Converter({
  noHeaderId: true,
  tables: true,
  literalMidWordUnderscores: true,
  omitExtraWLInCodeBlocks: true,
  simpleLineBreaks: true,
  strikethrough: true
});
/**
 * Corrects the Slack Markdown variant of the code block.
 * If uncorrected, it will be converted to inline code.
 *
 * @see https://get.slack.help/hc/en-us/articles/202288908-how-can-i-add-formatting-to-my-messages-#code-blocks
 *
 * @param {string} text The potential Markdown text to correct.
 *
 * @return {string} The corrected Markdown.
 */

function slackMarkdownVariantCorrector(text) {
  return text.replace(/((?:^|\n)```)([^\n`]+)(```(?:$|\n))/, (match, p1, p2, p3) => `${p1}\n${p2}\n${p3}`);
}
/**
 * Converts a piece of text into HTML based on any Markdown present.
 * Also decodes any encoded HTML.
 *
 * @param {string} text The plain text to convert.
 *
 * @return {string} HTML.
 */


function markdownConverter(text) {
  return converter.makeHtml(slackMarkdownVariantCorrector(text));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/iframe-remover.js
/**
 * Removes iframes.
 *
 * @param {Node} node The node to check.
 *
 * @return {void}
 */
function iframeRemover(node) {
  if (node.nodeName === 'IFRAME') {
    const text = node.ownerDocument.createTextNode(node.src);
    node.parentNode.replaceChild(text, node);
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/google-docs-uid-remover.js
/**
 * WordPress dependencies
 */

function googleDocsUIdRemover(node) {
  if (!node.id || node.id.indexOf('docs-internal-guid-') !== 0) {
    return;
  }

  Object(external_wp_dom_["unwrap"])(node);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/utils.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



function getBlockContentSchemaFromTransforms(transforms, context) {
  const phrasingContentSchema = Object(external_wp_dom_["getPhrasingContentSchema"])(context);
  const schemaArgs = {
    phrasingContentSchema,
    isPaste: context === 'paste'
  };
  const schemas = transforms.map(({
    isMatch,
    blockName,
    schema
  }) => {
    const hasAnchorSupport = registration_hasBlockSupport(blockName, 'anchor');
    schema = Object(external_lodash_["isFunction"])(schema) ? schema(schemaArgs) : schema; // If the block does not has anchor support and the transform does not
    // provides an isMatch we can return the schema right away.

    if (!hasAnchorSupport && !isMatch) {
      return schema;
    }

    return Object(external_lodash_["mapValues"])(schema, value => {
      let attributes = value.attributes || []; // If the block supports the "anchor" functionality, it needs to keep its ID attribute.

      if (hasAnchorSupport) {
        attributes = [...attributes, 'id'];
      }

      return { ...value,
        attributes,
        isMatch: isMatch ? isMatch : undefined
      };
    });
  });
  return Object(external_lodash_["mergeWith"])({}, ...schemas, (objValue, srcValue, key) => {
    switch (key) {
      case 'children':
        {
          if (objValue === '*' || srcValue === '*') {
            return '*';
          }

          return { ...objValue,
            ...srcValue
          };
        }

      case 'attributes':
      case 'require':
        {
          return [...(objValue || []), ...(srcValue || [])];
        }

      case 'isMatch':
        {
          // If one of the values being merge is undefined (matches everything),
          // the result of the merge will be undefined.
          if (!objValue || !srcValue) {
            return undefined;
          } // When merging two isMatch functions, the result is a new function
          // that returns if one of the source functions returns true.


          return (...args) => {
            return objValue(...args) || srcValue(...args);
          };
        }
    }
  });
}
/**
 * Gets the block content schema, which is extracted and merged from all
 * registered blocks with raw transfroms.
 *
 * @param {string} context Set to "paste" when in paste context, where the
 *                         schema is more strict.
 *
 * @return {Object} A complete block content schema.
 */

function getBlockContentSchema(context) {
  return getBlockContentSchemaFromTransforms(getRawTransforms(), context);
}
/**
 * Checks whether HTML can be considered plain text. That is, it does not contain
 * any elements that are not line breaks.
 *
 * @param {string} HTML The HTML to check.
 *
 * @return {boolean} Whether the HTML can be considered plain text.
 */

function isPlain(HTML) {
  return !/<(?!br[ />])/i.test(HTML);
}
/**
 * Given node filters, deeply filters and mutates a NodeList.
 *
 * @param {NodeList} nodeList The nodeList to filter.
 * @param {Array}    filters  An array of functions that can mutate with the provided node.
 * @param {Document} doc      The document of the nodeList.
 * @param {Object}   schema   The schema to use.
 */

function deepFilterNodeList(nodeList, filters, doc, schema) {
  Array.from(nodeList).forEach(node => {
    deepFilterNodeList(node.childNodes, filters, doc, schema);
    filters.forEach(item => {
      // Make sure the node is still attached to the document.
      if (!doc.contains(node)) {
        return;
      }

      item(node, doc, schema);
    });
  });
}
/**
 * Given node filters, deeply filters HTML tags.
 * Filters from the deepest nodes to the top.
 *
 * @param {string} HTML    The HTML to filter.
 * @param {Array}  filters An array of functions that can mutate with the provided node.
 * @param {Object} schema  The schema to use.
 *
 * @return {string} The filtered HTML.
 */

function deepFilterHTML(HTML, filters = [], schema) {
  const doc = document.implementation.createHTMLDocument('');
  doc.body.innerHTML = HTML;
  deepFilterNodeList(doc.body.childNodes, filters, doc, schema);
  return doc.body.innerHTML;
}
/**
 * Gets a sibling within text-level context.
 *
 * @param {Element} node  The subject node.
 * @param {string}  which "next" or "previous".
 */

function getSibling(node, which) {
  const sibling = node[`${which}Sibling`];

  if (sibling && Object(external_wp_dom_["isPhrasingContent"])(sibling)) {
    return sibling;
  }

  const {
    parentNode
  } = node;

  if (!parentNode || !Object(external_wp_dom_["isPhrasingContent"])(parentNode)) {
    return;
  }

  return getSibling(parentNode, which);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/html-formatting-remover.js
/**
 * Internal dependencies
 */


function isFormattingSpace(character) {
  return character === ' ' || character === '\r' || character === '\n' || character === '\t';
}
/**
 * Removes spacing that formats HTML.
 *
 * @see https://www.w3.org/TR/css-text-3/#white-space-processing
 *
 * @param {Node} node The node to be processed.
 * @return {void}
 */


function htmlFormattingRemover(node) {
  if (node.nodeType !== node.TEXT_NODE) {
    return;
  } // Ignore pre content. Note that this does not use Element#closest due to
  // a combination of (a) node may not be Element and (b) node.parentElement
  // does not have full support in all browsers (Internet Exporer).
  //
  // See: https://developer.mozilla.org/en-US/docs/Web/API/Node/parentElement#Browser_compatibility

  /** @type {Node?} */


  let parent = node;

  while (parent = parent.parentNode) {
    if (parent.nodeType === parent.ELEMENT_NODE && parent.nodeName === 'PRE') {
      return;
    }
  } // First, replace any sequence of HTML formatting space with a single space.


  let newData = node.data.replace(/[ \r\n\t]+/g, ' '); // Remove the leading space if the text element is at the start of a block,
  // is preceded by a line break element, or has a space in the previous
  // node.

  if (newData[0] === ' ') {
    const previousSibling = getSibling(node, 'previous');

    if (!previousSibling || previousSibling.nodeName === 'BR' || previousSibling.textContent.slice(-1) === ' ') {
      newData = newData.slice(1);
    }
  } // Remove the trailing space if the text element is at the end of a block,
  // is succeded by a line break element, or has a space in the next text
  // node.


  if (newData[newData.length - 1] === ' ') {
    const nextSibling = getSibling(node, 'next');

    if (!nextSibling || nextSibling.nodeName === 'BR' || nextSibling.nodeType === nextSibling.TEXT_NODE && isFormattingSpace(nextSibling.textContent[0])) {
      newData = newData.slice(0, -1);
    }
  } // If there's no data left, remove the node, so `previousSibling` stays
  // accurate. Otherwise, update the node data.


  if (!newData) {
    node.parentNode.removeChild(node);
  } else {
    node.data = newData;
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/br-remover.js
/**
 * Internal dependencies
 */

/**
 * Removes trailing br elements from text-level content.
 *
 * @param {Element} node Node to check.
 */

function brRemover(node) {
  if (node.nodeName !== 'BR') {
    return;
  }

  if (getSibling(node, 'next')) {
    return;
  }

  node.parentNode.removeChild(node);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/empty-paragraph-remover.js
/**
 * Removes empty paragraph elements.
 *
 * @param {Element} node Node to check.
 */
function emptyParagraphRemover(node) {
  if (node.nodeName !== 'P') {
    return;
  }

  if (node.hasChildNodes()) {
    return;
  }

  node.parentNode.removeChild(node);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/paste-handler.js
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
 * Browser dependencies
 */

const {
  console: paste_handler_console
} = window;
/**
 * Filters HTML to only contain phrasing content.
 *
 * @param {string}  HTML The HTML to filter.
 * @param {boolean} preserveWhiteSpace Whether or not to preserve consequent white space.
 *
 * @return {string} HTML only containing phrasing content.
 */

function filterInlineHTML(HTML, preserveWhiteSpace) {
  HTML = deepFilterHTML(HTML, [googleDocsUIdRemover, phrasingContentReducer, commentRemover]);
  HTML = Object(external_wp_dom_["removeInvalidHTML"])(HTML, Object(external_wp_dom_["getPhrasingContentSchema"])('paste'), {
    inline: true
  });

  if (!preserveWhiteSpace) {
    HTML = deepFilterHTML(HTML, [htmlFormattingRemover, brRemover]);
  } // Allows us to ask for this information when we get a report.


  paste_handler_console.log('Processed inline HTML:\n\n', HTML);
  return HTML;
}
/**
 * Converts an HTML string to known blocks. Strips everything else.
 *
 * @param {Object}  options
 * @param {string}  [options.HTML]      The HTML to convert.
 * @param {string}  [options.plainText] Plain text version.
 * @param {string}  [options.mode]      Handle content as blocks or inline content.
 *                                      * 'AUTO': Decide based on the content passed.
 *                                      * 'INLINE': Always handle as inline content, and return string.
 *                                      * 'BLOCKS': Always handle as blocks, and return array of blocks.
 * @param {Array}   [options.tagName]   The tag into which content will be inserted.
 * @param {boolean} [options.preserveWhiteSpace] Whether or not to preserve consequent white space.
 *
 * @return {Array|string} A list of blocks or a string, depending on `handlerMode`.
 */


function pasteHandler({
  HTML = '',
  plainText = '',
  mode = 'AUTO',
  tagName,
  preserveWhiteSpace
}) {
  // First of all, strip any meta tags.
  HTML = HTML.replace(/<meta[^>]+>/g, ''); // Strip Windows markers.

  HTML = HTML.replace(/^\s*<html[^>]*>\s*<body[^>]*>(?:\s*<!--\s*StartFragment\s*-->)?/i, '');
  HTML = HTML.replace(/(?:<!--\s*EndFragment\s*-->\s*)?<\/body>\s*<\/html>\s*$/i, ''); // If we detect block delimiters in HTML, parse entirely as blocks.

  if (mode !== 'INLINE') {
    // Check plain text if there is no HTML.
    const content = HTML ? HTML : plainText;

    if (content.indexOf('<!-- wp:') !== -1) {
      return parseWithGrammar(content);
    }
  } // Normalize unicode to use composed characters.
  // This is unsupported in IE 11 but it's a nice-to-have feature, not mandatory.
  // Not normalizing the content will only affect older browsers and won't
  // entirely break the app.
  // See: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/normalize
  // See: https://core.trac.wordpress.org/ticket/30130
  // See: https://github.com/WordPress/gutenberg/pull/6983#pullrequestreview-125151075


  if (String.prototype.normalize) {
    HTML = HTML.normalize();
  } // Parse Markdown (and encoded HTML) if:
  // * There is a plain text version.
  // * There is no HTML version, or it has no formatting.


  if (plainText && (!HTML || isPlain(HTML))) {
    HTML = plainText; // The markdown converter (Showdown) trims whitespace.

    if (!/^\s+$/.test(plainText)) {
      HTML = markdownConverter(HTML);
    } // Switch to inline mode if:
    // * The current mode is AUTO.
    // * The original plain text had no line breaks.
    // * The original plain text was not an HTML paragraph.
    // * The converted text is just a paragraph.


    if (mode === 'AUTO' && plainText.indexOf('\n') === -1 && plainText.indexOf('<p>') !== 0 && HTML.indexOf('<p>') === 0) {
      mode = 'INLINE';
    }
  }

  if (mode === 'INLINE') {
    return filterInlineHTML(HTML, preserveWhiteSpace);
  } // An array of HTML strings and block objects. The blocks replace matched
  // shortcodes.


  const pieces = shortcode_converter(HTML); // The call to shortcodeConverter will always return more than one element
  // if shortcodes are matched. The reason is when shortcodes are matched
  // empty HTML strings are included.

  const hasShortcodes = pieces.length > 1;

  if (mode === 'AUTO' && !hasShortcodes && isInlineContent(HTML, tagName)) {
    return filterInlineHTML(HTML, preserveWhiteSpace);
  }

  const phrasingContentSchema = Object(external_wp_dom_["getPhrasingContentSchema"])('paste');
  const blockContentSchema = getBlockContentSchema('paste');
  const blocks = Object(external_lodash_["compact"])(Object(external_lodash_["flatMap"])(pieces, piece => {
    // Already a block from shortcode.
    if (typeof piece !== 'string') {
      return piece;
    }

    const filters = [googleDocsUIdRemover, msListConverter, headRemover, listReducer, imageCorrector, phrasingContentReducer, specialCommentConverter, commentRemover, iframeRemover, figureContentReducer, blockquoteNormaliser];
    const schema = { ...blockContentSchema,
      // Keep top-level phrasing content, normalised by `normaliseBlocks`.
      ...phrasingContentSchema
    };
    piece = deepFilterHTML(piece, filters, blockContentSchema);
    piece = Object(external_wp_dom_["removeInvalidHTML"])(piece, schema);
    piece = normaliseBlocks(piece);
    piece = deepFilterHTML(piece, [htmlFormattingRemover, brRemover, emptyParagraphRemover], blockContentSchema); // Allows us to ask for this information when we get a report.

    paste_handler_console.log('Processed HTML piece:\n\n', piece);
    return htmlToBlocks(piece);
  })); // If we're allowed to return inline content, and there is only one
  // inlineable block, and the original plain text content does not have any
  // line breaks, then treat it as inline paste.

  if (mode === 'AUTO' && blocks.length === 1 && registration_hasBlockSupport(blocks[0].name, '__unstablePasteTextInline', false)) {
    // Don't catch line breaks at the start or end.
    const trimmedPlainText = plainText.replace(/^[\n]+|[\n]+$/g, '');

    if (trimmedPlainText !== '' && trimmedPlainText.indexOf('\n') === -1) {
      return Object(external_wp_dom_["removeInvalidHTML"])(getBlockInnerHTML(blocks[0]), phrasingContentSchema);
    }
  }

  return blocks;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/raw-handling/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */











function deprecatedGetPhrasingContentSchema(context) {
  external_wp_deprecated_default()('wp.blocks.getPhrasingContentSchema', {
    since: '5.6',
    alternative: 'wp.dom.getPhrasingContentSchema'
  });
  return Object(external_wp_dom_["getPhrasingContentSchema"])(context);
}
/**
 * Converts an HTML string to known blocks.
 *
 * @param {Object} $1
 * @param {string} $1.HTML The HTML to convert.
 *
 * @return {Array} A list of blocks.
 */

function rawHandler({
  HTML = ''
}) {
  // If we detect block delimiters, parse entirely as blocks.
  if (HTML.indexOf('<!-- wp:') !== -1) {
    return parseWithGrammar(HTML);
  } // An array of HTML strings and block objects. The blocks replace matched
  // shortcodes.


  const pieces = shortcode_converter(HTML);
  const blockContentSchema = getBlockContentSchema();
  return Object(external_lodash_["compact"])(Object(external_lodash_["flatMap"])(pieces, piece => {
    // Already a block from shortcode.
    if (typeof piece !== 'string') {
      return piece;
    } // These filters are essential for some blocks to be able to transform
    // from raw HTML. These filters move around some content or add
    // additional tags, they do not remove any content.


    const filters = [// Needed to adjust invalid lists.
    listReducer, // Needed to create more and nextpage blocks.
    specialCommentConverter, // Needed to create media blocks.
    figureContentReducer, // Needed to create the quote block, which cannot handle text
    // without wrapper paragraphs.
    blockquoteNormaliser];
    piece = deepFilterHTML(piece, filters, blockContentSchema);
    piece = normaliseBlocks(piece);
    return htmlToBlocks(piece);
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/categories.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('../store/reducer').WPBlockCategory} WPBlockCategory */

/**
 * Returns all the block categories.
 *
 * @return {WPBlockCategory[]} Block categories.
 */

function categories_getCategories() {
  return Object(external_wp_data_["select"])(store).getCategories();
}
/**
 * Sets the block categories.
 *
 * @param {WPBlockCategory[]} categories Block categories.
 */

function categories_setCategories(categories) {
  Object(external_wp_data_["dispatch"])(store).setCategories(categories);
}
/**
 * Updates a category.
 *
 * @param {string}          slug     Block category slug.
 * @param {WPBlockCategory} category Object containing the category properties
 *                                   that should be updated.
 */

function categories_updateCategory(slug, category) {
  Object(external_wp_data_["dispatch"])(store).updateCategory(slug, category);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/templates.js
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
 * Checks whether a list of blocks matches a template by comparing the block names.
 *
 * @param {Array} blocks    Block list.
 * @param {Array} template  Block template.
 *
 * @return {boolean}        Whether the list of blocks matches a templates
 */

function doBlocksMatchTemplate(blocks = [], template = []) {
  return blocks.length === template.length && Object(external_lodash_["every"])(template, ([name,, innerBlocksTemplate], index) => {
    const block = blocks[index];
    return name === block.name && doBlocksMatchTemplate(block.innerBlocks, innerBlocksTemplate);
  });
}
/**
 * Synchronize a block list with a block template.
 *
 * Synchronizing a block list with a block template means that we loop over the blocks
 * keep the block as is if it matches the block at the same position in the template
 * (If it has the same name) and if doesn't match, we create a new block based on the template.
 * Extra blocks not present in the template are removed.
 *
 * @param {Array} blocks    Block list.
 * @param {Array} template  Block template.
 *
 * @return {Array}          Updated Block list.
 */

function synchronizeBlocksWithTemplate(blocks = [], template) {
  // If no template is provided, return blocks unmodified.
  if (!template) {
    return blocks;
  }

  return Object(external_lodash_["map"])(template, ([name, attributes, innerBlocksTemplate], index) => {
    const block = blocks[index];

    if (block && block.name === name) {
      const innerBlocks = synchronizeBlocksWithTemplate(block.innerBlocks, innerBlocksTemplate);
      return { ...block,
        innerBlocks
      };
    } // To support old templates that were using the "children" format
    // for the attributes using "html" strings now, we normalize the template attributes
    // before creating the blocks.


    const blockType = registration_getBlockType(name);

    const isHTMLAttribute = attributeDefinition => Object(external_lodash_["get"])(attributeDefinition, ['source']) === 'html';

    const isQueryAttribute = attributeDefinition => Object(external_lodash_["get"])(attributeDefinition, ['source']) === 'query';

    const normalizeAttributes = (schema, values) => {
      return Object(external_lodash_["mapValues"])(values, (value, key) => {
        return normalizeAttribute(schema[key], value);
      });
    };

    const normalizeAttribute = (definition, value) => {
      if (isHTMLAttribute(definition) && Object(external_lodash_["isArray"])(value)) {
        // Introduce a deprecated call at this point
        // When we're confident that "children" format should be removed from the templates.
        return Object(external_wp_element_["renderToString"])(value);
      }

      if (isQueryAttribute(definition) && value) {
        return value.map(subValues => {
          return normalizeAttributes(definition.query, subValues);
        });
      }

      return value;
    };

    const normalizedAttributes = normalizeAttributes(Object(external_lodash_["get"])(blockType, ['attributes'], {}), attributes);
    const {
      name: blockName,
      attributes: blockAttributes
    } = convertLegacyBlocks(name, normalizedAttributes);
    return createBlock(blockName, blockAttributes, synchronizeBlocksWithTemplate([], innerBlocksTemplate));
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/api/index.js
// The blocktype is the most important concept within the block API. It defines
// all aspects of the block configuration and its interfaces, including `edit`
// and `save`. The transforms specification allows converting one blocktype to
// another through formulas defined by either the source or the destination.
// Switching a blocktype is to be considered a one-way operation implying a
// transformation in the opposite way has to be handled explicitly.
 // The block tree is composed of a collection of block nodes. Blocks contained
// within other blocks are called inner blocks. An important design
// consideration is that inner blocks are -- conceptually -- not part of the
// territory established by the parent block that contains them.
//
// This has multiple practical implications: when parsing, we can safely dispose
// of any block boundary found within a block from the innerHTML property when
// transfering to state. Not doing so would have a compounding effect on memory
// and uncertainty over the source of truth. This can be illustrated in how,
// given a tree of `n` nested blocks, the entry node would have to contain the
// actual content of each block while each subsequent block node in the state
// tree would replicate the entire chain `n-1`, meaning the extreme end node
// would have been replicated `n` times as the tree is traversed and would
// generate uncertainty as to which one is to hold the current value of the
// block. For composition, it also means inner blocks can effectively be child
// components whose mechanisms can be shielded from the `edit` implementation
// and just passed along.

 // While block transformations account for a specific surface of the API, there
// are also raw transformations which handle arbitrary sources not made out of
// blocks but producing block basaed on various heursitics. This includes
// pasting rich text or HTML data.

 // The process of serialization aims to deflate the internal memory of the block
// editor and its state representation back into an HTML valid string. This
// process restores the document integrity and inserts invisible delimiters
// around each block with HTML comment boundaries which can contain any extra
// attributes needed to operate with the block later on.

 // Validation is the process of comparing a block source with its output before
// there is any user input or interaction with a block. When this operation
// fails -- for whatever reason -- the block is to be considered invalid. As
// part of validating a block the system will attempt to run the source against
// any provided deprecation definitions.
//
// Worth emphasizing that validation is not a case of whether the markup is
// merely HTML spec-compliant but about how the editor knows to create such
// markup and that its inability to create an identical result can be a strong
// indicator of potential data loss (the invalidation is then a protective
// measure).
//
// The invalidation process can also be deconstructed in phases: 1) validate the
// block exists; 2) validate the source matches the output; 3) validate the
// source matches deprecated outputs; 4) work through the significance of
// differences. These are stacked in a way that favors performance and optimizes
// for the majority of cases. That is to say, the evaluation logic can become
// more sophisticated the further down it goes in the process as the cost is
// accounted for. The first logic checks have to be extremely efficient since
// they will be run for all valid and invalid blocks alike. However, once a
// block is detected as invalid -- failing the three first steps -- it is
// adequate to spend more time determining validity before throwing a conflict.


 // Blocks are inherently indifferent about where the data they operate with ends
// up being saved. For example, all blocks can have a static and dynamic aspect
// to them depending on the needs. The static nature of a block is the `save()`
// definition that is meant to be serialized into HTML and which can be left
// void. Any block can also register a `render_callback` on the server, which
// makes its output dynamic either in part or in its totality.
//
// Child blocks are defined as a relationship that builds on top of the inner
// blocks mechanism. A child block is a block node of a particular type that can
// only exist within the inner block boundaries of a specific parent type. This
// allows block authors to compose specific blocks that are not meant to be used
// outside of a specified parent block context. Thus, child blocks extend the
// concept of inner blocks to support a more direct relationship between sets of
// blocks. The addition of parent–child would be a subset of the inner block
// functionality under the premise that certain blocks only make sense as
// children of another block.


 // Templates are, in a general sense, a basic collection of block nodes with any
// given set of predefined attributes that are supplied as the initial state of
// an inner blocks group. These nodes can, in turn, contain any number of nested
// blocks within their definition. Templates allow both to specify a default
// state for an editor session or a default set of blocks for any inner block
// implementation within a specific block.






// CONCATENATED MODULE: ./node_modules/@wordpress/blocks/build-module/index.js
// A "block" is the abstract term used to describe units of markup that,
// when composed together, form the content or layout of a page.
// The API for blocks is exposed via `wp.blocks`.
//
// Supported blocks are registered by calling `registerBlockType`. Once registered,
// the block is made available as an option to the editor interface.
//
// Blocks are inferred from the HTML source of a post through a parsing mechanism
// and then stored as objects in state, from which it is then rendered for editing.





/***/ }),

/***/ "1CF3":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["dom"]; }());

/***/ }),

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "7Cbv":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/rng.js
// Unique ID creation requires a high quality random # generator. In the browser we therefore
// require the crypto API and do not support built-in fallback to lower quality random number
// generators (like Math.random()).
var getRandomValues;
var rnds8 = new Uint8Array(16);
function rng() {
  // lazy load so that environments that need to polyfill have a chance to do so
  if (!getRandomValues) {
    // getRandomValues needs to be invoked in a context where "this" is a Crypto implementation. Also,
    // find the complete implementation of crypto (msCrypto) on IE11.
    getRandomValues = typeof crypto !== 'undefined' && crypto.getRandomValues && crypto.getRandomValues.bind(crypto) || typeof msCrypto !== 'undefined' && typeof msCrypto.getRandomValues === 'function' && msCrypto.getRandomValues.bind(msCrypto);

    if (!getRandomValues) {
      throw new Error('crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported');
    }
  }

  return getRandomValues(rnds8);
}
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/regex.js
/* harmony default export */ var regex = (/^(?:[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}|00000000-0000-0000-0000-000000000000)$/i);
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/validate.js


function validate(uuid) {
  return typeof uuid === 'string' && regex.test(uuid);
}

/* harmony default export */ var esm_browser_validate = (validate);
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/stringify.js

/**
 * Convert array of 16 byte values to UUID string format of the form:
 * XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
 */

var byteToHex = [];

for (var stringify_i = 0; stringify_i < 256; ++stringify_i) {
  byteToHex.push((stringify_i + 0x100).toString(16).substr(1));
}

function stringify(arr) {
  var offset = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
  // Note: Be careful editing this code!  It's been tuned for performance
  // and works in ways you may not expect. See https://github.com/uuidjs/uuid/pull/434
  var uuid = (byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + '-' + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + '-' + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + '-' + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + '-' + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]]).toLowerCase(); // Consistency check for valid UUID.  If this throws, it's likely due to one
  // of the following:
  // - One or more input array values don't map to a hex octet (leading to
  // "undefined" in the uuid)
  // - Invalid input values for the RFC `version` or `variant` fields

  if (!esm_browser_validate(uuid)) {
    throw TypeError('Stringified UUID is invalid');
  }

  return uuid;
}

/* harmony default export */ var esm_browser_stringify = (stringify);
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/v4.js



function v4(options, buf, offset) {
  options = options || {};
  var rnds = options.random || (options.rng || rng)(); // Per 4.4, set bits for version and `clock_seq_hi_and_reserved`

  rnds[6] = rnds[6] & 0x0f | 0x40;
  rnds[8] = rnds[8] & 0x3f | 0x80; // Copy bytes to buffer, if provided

  if (buf) {
    offset = offset || 0;

    for (var i = 0; i < 16; ++i) {
      buf[offset + i] = rnds[i];
    }

    return buf;
  }

  return esm_browser_stringify(rnds);
}

/* harmony default export */ var esm_browser_v4 = __webpack_exports__["a"] = (v4);

/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "M55E":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_RESULT__;;/*! showdown v 1.9.1 - 02-11-2019 */
(function(){
/**
 * Created by Tivie on 13-07-2015.
 */

function getDefaultOpts (simple) {
  'use strict';

  var defaultOptions = {
    omitExtraWLInCodeBlocks: {
      defaultValue: false,
      describe: 'Omit the default extra whiteline added to code blocks',
      type: 'boolean'
    },
    noHeaderId: {
      defaultValue: false,
      describe: 'Turn on/off generated header id',
      type: 'boolean'
    },
    prefixHeaderId: {
      defaultValue: false,
      describe: 'Add a prefix to the generated header ids. Passing a string will prefix that string to the header id. Setting to true will add a generic \'section-\' prefix',
      type: 'string'
    },
    rawPrefixHeaderId: {
      defaultValue: false,
      describe: 'Setting this option to true will prevent showdown from modifying the prefix. This might result in malformed IDs (if, for instance, the " char is used in the prefix)',
      type: 'boolean'
    },
    ghCompatibleHeaderId: {
      defaultValue: false,
      describe: 'Generate header ids compatible with github style (spaces are replaced with dashes, a bunch of non alphanumeric chars are removed)',
      type: 'boolean'
    },
    rawHeaderId: {
      defaultValue: false,
      describe: 'Remove only spaces, \' and " from generated header ids (including prefixes), replacing them with dashes (-). WARNING: This might result in malformed ids',
      type: 'boolean'
    },
    headerLevelStart: {
      defaultValue: false,
      describe: 'The header blocks level start',
      type: 'integer'
    },
    parseImgDimensions: {
      defaultValue: false,
      describe: 'Turn on/off image dimension parsing',
      type: 'boolean'
    },
    simplifiedAutoLink: {
      defaultValue: false,
      describe: 'Turn on/off GFM autolink style',
      type: 'boolean'
    },
    excludeTrailingPunctuationFromURLs: {
      defaultValue: false,
      describe: 'Excludes trailing punctuation from links generated with autoLinking',
      type: 'boolean'
    },
    literalMidWordUnderscores: {
      defaultValue: false,
      describe: 'Parse midword underscores as literal underscores',
      type: 'boolean'
    },
    literalMidWordAsterisks: {
      defaultValue: false,
      describe: 'Parse midword asterisks as literal asterisks',
      type: 'boolean'
    },
    strikethrough: {
      defaultValue: false,
      describe: 'Turn on/off strikethrough support',
      type: 'boolean'
    },
    tables: {
      defaultValue: false,
      describe: 'Turn on/off tables support',
      type: 'boolean'
    },
    tablesHeaderId: {
      defaultValue: false,
      describe: 'Add an id to table headers',
      type: 'boolean'
    },
    ghCodeBlocks: {
      defaultValue: true,
      describe: 'Turn on/off GFM fenced code blocks support',
      type: 'boolean'
    },
    tasklists: {
      defaultValue: false,
      describe: 'Turn on/off GFM tasklist support',
      type: 'boolean'
    },
    smoothLivePreview: {
      defaultValue: false,
      describe: 'Prevents weird effects in live previews due to incomplete input',
      type: 'boolean'
    },
    smartIndentationFix: {
      defaultValue: false,
      description: 'Tries to smartly fix indentation in es6 strings',
      type: 'boolean'
    },
    disableForced4SpacesIndentedSublists: {
      defaultValue: false,
      description: 'Disables the requirement of indenting nested sublists by 4 spaces',
      type: 'boolean'
    },
    simpleLineBreaks: {
      defaultValue: false,
      description: 'Parses simple line breaks as <br> (GFM Style)',
      type: 'boolean'
    },
    requireSpaceBeforeHeadingText: {
      defaultValue: false,
      description: 'Makes adding a space between `#` and the header text mandatory (GFM Style)',
      type: 'boolean'
    },
    ghMentions: {
      defaultValue: false,
      description: 'Enables github @mentions',
      type: 'boolean'
    },
    ghMentionsLink: {
      defaultValue: 'https://github.com/{u}',
      description: 'Changes the link generated by @mentions. Only applies if ghMentions option is enabled.',
      type: 'string'
    },
    encodeEmails: {
      defaultValue: true,
      description: 'Encode e-mail addresses through the use of Character Entities, transforming ASCII e-mail addresses into its equivalent decimal entities',
      type: 'boolean'
    },
    openLinksInNewWindow: {
      defaultValue: false,
      description: 'Open all links in new windows',
      type: 'boolean'
    },
    backslashEscapesHTMLTags: {
      defaultValue: false,
      description: 'Support for HTML Tag escaping. ex: \<div>foo\</div>',
      type: 'boolean'
    },
    emoji: {
      defaultValue: false,
      description: 'Enable emoji support. Ex: `this is a :smile: emoji`',
      type: 'boolean'
    },
    underline: {
      defaultValue: false,
      description: 'Enable support for underline. Syntax is double or triple underscores: `__underline word__`. With this option enabled, underscores no longer parses into `<em>` and `<strong>`',
      type: 'boolean'
    },
    completeHTMLDocument: {
      defaultValue: false,
      description: 'Outputs a complete html document, including `<html>`, `<head>` and `<body>` tags',
      type: 'boolean'
    },
    metadata: {
      defaultValue: false,
      description: 'Enable support for document metadata (defined at the top of the document between `«««` and `»»»` or between `---` and `---`).',
      type: 'boolean'
    },
    splitAdjacentBlockquotes: {
      defaultValue: false,
      description: 'Split adjacent blockquote blocks',
      type: 'boolean'
    }
  };
  if (simple === false) {
    return JSON.parse(JSON.stringify(defaultOptions));
  }
  var ret = {};
  for (var opt in defaultOptions) {
    if (defaultOptions.hasOwnProperty(opt)) {
      ret[opt] = defaultOptions[opt].defaultValue;
    }
  }
  return ret;
}

function allOptionsOn () {
  'use strict';
  var options = getDefaultOpts(true),
      ret = {};
  for (var opt in options) {
    if (options.hasOwnProperty(opt)) {
      ret[opt] = true;
    }
  }
  return ret;
}

/**
 * Created by Tivie on 06-01-2015.
 */

// Private properties
var showdown = {},
    parsers = {},
    extensions = {},
    globalOptions = getDefaultOpts(true),
    setFlavor = 'vanilla',
    flavor = {
      github: {
        omitExtraWLInCodeBlocks:              true,
        simplifiedAutoLink:                   true,
        excludeTrailingPunctuationFromURLs:   true,
        literalMidWordUnderscores:            true,
        strikethrough:                        true,
        tables:                               true,
        tablesHeaderId:                       true,
        ghCodeBlocks:                         true,
        tasklists:                            true,
        disableForced4SpacesIndentedSublists: true,
        simpleLineBreaks:                     true,
        requireSpaceBeforeHeadingText:        true,
        ghCompatibleHeaderId:                 true,
        ghMentions:                           true,
        backslashEscapesHTMLTags:             true,
        emoji:                                true,
        splitAdjacentBlockquotes:             true
      },
      original: {
        noHeaderId:                           true,
        ghCodeBlocks:                         false
      },
      ghost: {
        omitExtraWLInCodeBlocks:              true,
        parseImgDimensions:                   true,
        simplifiedAutoLink:                   true,
        excludeTrailingPunctuationFromURLs:   true,
        literalMidWordUnderscores:            true,
        strikethrough:                        true,
        tables:                               true,
        tablesHeaderId:                       true,
        ghCodeBlocks:                         true,
        tasklists:                            true,
        smoothLivePreview:                    true,
        simpleLineBreaks:                     true,
        requireSpaceBeforeHeadingText:        true,
        ghMentions:                           false,
        encodeEmails:                         true
      },
      vanilla: getDefaultOpts(true),
      allOn: allOptionsOn()
    };

/**
 * helper namespace
 * @type {{}}
 */
showdown.helper = {};

/**
 * TODO LEGACY SUPPORT CODE
 * @type {{}}
 */
showdown.extensions = {};

/**
 * Set a global option
 * @static
 * @param {string} key
 * @param {*} value
 * @returns {showdown}
 */
showdown.setOption = function (key, value) {
  'use strict';
  globalOptions[key] = value;
  return this;
};

/**
 * Get a global option
 * @static
 * @param {string} key
 * @returns {*}
 */
showdown.getOption = function (key) {
  'use strict';
  return globalOptions[key];
};

/**
 * Get the global options
 * @static
 * @returns {{}}
 */
showdown.getOptions = function () {
  'use strict';
  return globalOptions;
};

/**
 * Reset global options to the default values
 * @static
 */
showdown.resetOptions = function () {
  'use strict';
  globalOptions = getDefaultOpts(true);
};

/**
 * Set the flavor showdown should use as default
 * @param {string} name
 */
showdown.setFlavor = function (name) {
  'use strict';
  if (!flavor.hasOwnProperty(name)) {
    throw Error(name + ' flavor was not found');
  }
  showdown.resetOptions();
  var preset = flavor[name];
  setFlavor = name;
  for (var option in preset) {
    if (preset.hasOwnProperty(option)) {
      globalOptions[option] = preset[option];
    }
  }
};

/**
 * Get the currently set flavor
 * @returns {string}
 */
showdown.getFlavor = function () {
  'use strict';
  return setFlavor;
};

/**
 * Get the options of a specified flavor. Returns undefined if the flavor was not found
 * @param {string} name Name of the flavor
 * @returns {{}|undefined}
 */
showdown.getFlavorOptions = function (name) {
  'use strict';
  if (flavor.hasOwnProperty(name)) {
    return flavor[name];
  }
};

/**
 * Get the default options
 * @static
 * @param {boolean} [simple=true]
 * @returns {{}}
 */
showdown.getDefaultOptions = function (simple) {
  'use strict';
  return getDefaultOpts(simple);
};

/**
 * Get or set a subParser
 *
 * subParser(name)       - Get a registered subParser
 * subParser(name, func) - Register a subParser
 * @static
 * @param {string} name
 * @param {function} [func]
 * @returns {*}
 */
showdown.subParser = function (name, func) {
  'use strict';
  if (showdown.helper.isString(name)) {
    if (typeof func !== 'undefined') {
      parsers[name] = func;
    } else {
      if (parsers.hasOwnProperty(name)) {
        return parsers[name];
      } else {
        throw Error('SubParser named ' + name + ' not registered!');
      }
    }
  }
};

/**
 * Gets or registers an extension
 * @static
 * @param {string} name
 * @param {object|function=} ext
 * @returns {*}
 */
showdown.extension = function (name, ext) {
  'use strict';

  if (!showdown.helper.isString(name)) {
    throw Error('Extension \'name\' must be a string');
  }

  name = showdown.helper.stdExtName(name);

  // Getter
  if (showdown.helper.isUndefined(ext)) {
    if (!extensions.hasOwnProperty(name)) {
      throw Error('Extension named ' + name + ' is not registered!');
    }
    return extensions[name];

    // Setter
  } else {
    // Expand extension if it's wrapped in a function
    if (typeof ext === 'function') {
      ext = ext();
    }

    // Ensure extension is an array
    if (!showdown.helper.isArray(ext)) {
      ext = [ext];
    }

    var validExtension = validate(ext, name);

    if (validExtension.valid) {
      extensions[name] = ext;
    } else {
      throw Error(validExtension.error);
    }
  }
};

/**
 * Gets all extensions registered
 * @returns {{}}
 */
showdown.getAllExtensions = function () {
  'use strict';
  return extensions;
};

/**
 * Remove an extension
 * @param {string} name
 */
showdown.removeExtension = function (name) {
  'use strict';
  delete extensions[name];
};

/**
 * Removes all extensions
 */
showdown.resetExtensions = function () {
  'use strict';
  extensions = {};
};

/**
 * Validate extension
 * @param {array} extension
 * @param {string} name
 * @returns {{valid: boolean, error: string}}
 */
function validate (extension, name) {
  'use strict';

  var errMsg = (name) ? 'Error in ' + name + ' extension->' : 'Error in unnamed extension',
      ret = {
        valid: true,
        error: ''
      };

  if (!showdown.helper.isArray(extension)) {
    extension = [extension];
  }

  for (var i = 0; i < extension.length; ++i) {
    var baseMsg = errMsg + ' sub-extension ' + i + ': ',
        ext = extension[i];
    if (typeof ext !== 'object') {
      ret.valid = false;
      ret.error = baseMsg + 'must be an object, but ' + typeof ext + ' given';
      return ret;
    }

    if (!showdown.helper.isString(ext.type)) {
      ret.valid = false;
      ret.error = baseMsg + 'property "type" must be a string, but ' + typeof ext.type + ' given';
      return ret;
    }

    var type = ext.type = ext.type.toLowerCase();

    // normalize extension type
    if (type === 'language') {
      type = ext.type = 'lang';
    }

    if (type === 'html') {
      type = ext.type = 'output';
    }

    if (type !== 'lang' && type !== 'output' && type !== 'listener') {
      ret.valid = false;
      ret.error = baseMsg + 'type ' + type + ' is not recognized. Valid values: "lang/language", "output/html" or "listener"';
      return ret;
    }

    if (type === 'listener') {
      if (showdown.helper.isUndefined(ext.listeners)) {
        ret.valid = false;
        ret.error = baseMsg + '. Extensions of type "listener" must have a property called "listeners"';
        return ret;
      }
    } else {
      if (showdown.helper.isUndefined(ext.filter) && showdown.helper.isUndefined(ext.regex)) {
        ret.valid = false;
        ret.error = baseMsg + type + ' extensions must define either a "regex" property or a "filter" method';
        return ret;
      }
    }

    if (ext.listeners) {
      if (typeof ext.listeners !== 'object') {
        ret.valid = false;
        ret.error = baseMsg + '"listeners" property must be an object but ' + typeof ext.listeners + ' given';
        return ret;
      }
      for (var ln in ext.listeners) {
        if (ext.listeners.hasOwnProperty(ln)) {
          if (typeof ext.listeners[ln] !== 'function') {
            ret.valid = false;
            ret.error = baseMsg + '"listeners" property must be an hash of [event name]: [callback]. listeners.' + ln +
              ' must be a function but ' + typeof ext.listeners[ln] + ' given';
            return ret;
          }
        }
      }
    }

    if (ext.filter) {
      if (typeof ext.filter !== 'function') {
        ret.valid = false;
        ret.error = baseMsg + '"filter" must be a function, but ' + typeof ext.filter + ' given';
        return ret;
      }
    } else if (ext.regex) {
      if (showdown.helper.isString(ext.regex)) {
        ext.regex = new RegExp(ext.regex, 'g');
      }
      if (!(ext.regex instanceof RegExp)) {
        ret.valid = false;
        ret.error = baseMsg + '"regex" property must either be a string or a RegExp object, but ' + typeof ext.regex + ' given';
        return ret;
      }
      if (showdown.helper.isUndefined(ext.replace)) {
        ret.valid = false;
        ret.error = baseMsg + '"regex" extensions must implement a replace string or function';
        return ret;
      }
    }
  }
  return ret;
}

/**
 * Validate extension
 * @param {object} ext
 * @returns {boolean}
 */
showdown.validateExtension = function (ext) {
  'use strict';

  var validateExtension = validate(ext, null);
  if (!validateExtension.valid) {
    console.warn(validateExtension.error);
    return false;
  }
  return true;
};

/**
 * showdownjs helper functions
 */

if (!showdown.hasOwnProperty('helper')) {
  showdown.helper = {};
}

/**
 * Check if var is string
 * @static
 * @param {string} a
 * @returns {boolean}
 */
showdown.helper.isString = function (a) {
  'use strict';
  return (typeof a === 'string' || a instanceof String);
};

/**
 * Check if var is a function
 * @static
 * @param {*} a
 * @returns {boolean}
 */
showdown.helper.isFunction = function (a) {
  'use strict';
  var getType = {};
  return a && getType.toString.call(a) === '[object Function]';
};

/**
 * isArray helper function
 * @static
 * @param {*} a
 * @returns {boolean}
 */
showdown.helper.isArray = function (a) {
  'use strict';
  return Array.isArray(a);
};

/**
 * Check if value is undefined
 * @static
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is `undefined`, else `false`.
 */
showdown.helper.isUndefined = function (value) {
  'use strict';
  return typeof value === 'undefined';
};

/**
 * ForEach helper function
 * Iterates over Arrays and Objects (own properties only)
 * @static
 * @param {*} obj
 * @param {function} callback Accepts 3 params: 1. value, 2. key, 3. the original array/object
 */
showdown.helper.forEach = function (obj, callback) {
  'use strict';
  // check if obj is defined
  if (showdown.helper.isUndefined(obj)) {
    throw new Error('obj param is required');
  }

  if (showdown.helper.isUndefined(callback)) {
    throw new Error('callback param is required');
  }

  if (!showdown.helper.isFunction(callback)) {
    throw new Error('callback param must be a function/closure');
  }

  if (typeof obj.forEach === 'function') {
    obj.forEach(callback);
  } else if (showdown.helper.isArray(obj)) {
    for (var i = 0; i < obj.length; i++) {
      callback(obj[i], i, obj);
    }
  } else if (typeof (obj) === 'object') {
    for (var prop in obj) {
      if (obj.hasOwnProperty(prop)) {
        callback(obj[prop], prop, obj);
      }
    }
  } else {
    throw new Error('obj does not seem to be an array or an iterable object');
  }
};

/**
 * Standardidize extension name
 * @static
 * @param {string} s extension name
 * @returns {string}
 */
showdown.helper.stdExtName = function (s) {
  'use strict';
  return s.replace(/[_?*+\/\\.^-]/g, '').replace(/\s/g, '').toLowerCase();
};

function escapeCharactersCallback (wholeMatch, m1) {
  'use strict';
  var charCodeToEscape = m1.charCodeAt(0);
  return '¨E' + charCodeToEscape + 'E';
}

/**
 * Callback used to escape characters when passing through String.replace
 * @static
 * @param {string} wholeMatch
 * @param {string} m1
 * @returns {string}
 */
showdown.helper.escapeCharactersCallback = escapeCharactersCallback;

/**
 * Escape characters in a string
 * @static
 * @param {string} text
 * @param {string} charsToEscape
 * @param {boolean} afterBackslash
 * @returns {XML|string|void|*}
 */
showdown.helper.escapeCharacters = function (text, charsToEscape, afterBackslash) {
  'use strict';
  // First we have to escape the escape characters so that
  // we can build a character class out of them
  var regexString = '([' + charsToEscape.replace(/([\[\]\\])/g, '\\$1') + '])';

  if (afterBackslash) {
    regexString = '\\\\' + regexString;
  }

  var regex = new RegExp(regexString, 'g');
  text = text.replace(regex, escapeCharactersCallback);

  return text;
};

/**
 * Unescape HTML entities
 * @param txt
 * @returns {string}
 */
showdown.helper.unescapeHTMLEntities = function (txt) {
  'use strict';

  return txt
    .replace(/&quot;/g, '"')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&amp;/g, '&');
};

var rgxFindMatchPos = function (str, left, right, flags) {
  'use strict';
  var f = flags || '',
      g = f.indexOf('g') > -1,
      x = new RegExp(left + '|' + right, 'g' + f.replace(/g/g, '')),
      l = new RegExp(left, f.replace(/g/g, '')),
      pos = [],
      t, s, m, start, end;

  do {
    t = 0;
    while ((m = x.exec(str))) {
      if (l.test(m[0])) {
        if (!(t++)) {
          s = x.lastIndex;
          start = s - m[0].length;
        }
      } else if (t) {
        if (!--t) {
          end = m.index + m[0].length;
          var obj = {
            left: {start: start, end: s},
            match: {start: s, end: m.index},
            right: {start: m.index, end: end},
            wholeMatch: {start: start, end: end}
          };
          pos.push(obj);
          if (!g) {
            return pos;
          }
        }
      }
    }
  } while (t && (x.lastIndex = s));

  return pos;
};

/**
 * matchRecursiveRegExp
 *
 * (c) 2007 Steven Levithan <stevenlevithan.com>
 * MIT License
 *
 * Accepts a string to search, a left and right format delimiter
 * as regex patterns, and optional regex flags. Returns an array
 * of matches, allowing nested instances of left/right delimiters.
 * Use the "g" flag to return all matches, otherwise only the
 * first is returned. Be careful to ensure that the left and
 * right format delimiters produce mutually exclusive matches.
 * Backreferences are not supported within the right delimiter
 * due to how it is internally combined with the left delimiter.
 * When matching strings whose format delimiters are unbalanced
 * to the left or right, the output is intentionally as a
 * conventional regex library with recursion support would
 * produce, e.g. "<<x>" and "<x>>" both produce ["x"] when using
 * "<" and ">" as the delimiters (both strings contain a single,
 * balanced instance of "<x>").
 *
 * examples:
 * matchRecursiveRegExp("test", "\\(", "\\)")
 * returns: []
 * matchRecursiveRegExp("<t<<e>><s>>t<>", "<", ">", "g")
 * returns: ["t<<e>><s>", ""]
 * matchRecursiveRegExp("<div id=\"x\">test</div>", "<div\\b[^>]*>", "</div>", "gi")
 * returns: ["test"]
 */
showdown.helper.matchRecursiveRegExp = function (str, left, right, flags) {
  'use strict';

  var matchPos = rgxFindMatchPos (str, left, right, flags),
      results = [];

  for (var i = 0; i < matchPos.length; ++i) {
    results.push([
      str.slice(matchPos[i].wholeMatch.start, matchPos[i].wholeMatch.end),
      str.slice(matchPos[i].match.start, matchPos[i].match.end),
      str.slice(matchPos[i].left.start, matchPos[i].left.end),
      str.slice(matchPos[i].right.start, matchPos[i].right.end)
    ]);
  }
  return results;
};

/**
 *
 * @param {string} str
 * @param {string|function} replacement
 * @param {string} left
 * @param {string} right
 * @param {string} flags
 * @returns {string}
 */
showdown.helper.replaceRecursiveRegExp = function (str, replacement, left, right, flags) {
  'use strict';

  if (!showdown.helper.isFunction(replacement)) {
    var repStr = replacement;
    replacement = function () {
      return repStr;
    };
  }

  var matchPos = rgxFindMatchPos(str, left, right, flags),
      finalStr = str,
      lng = matchPos.length;

  if (lng > 0) {
    var bits = [];
    if (matchPos[0].wholeMatch.start !== 0) {
      bits.push(str.slice(0, matchPos[0].wholeMatch.start));
    }
    for (var i = 0; i < lng; ++i) {
      bits.push(
        replacement(
          str.slice(matchPos[i].wholeMatch.start, matchPos[i].wholeMatch.end),
          str.slice(matchPos[i].match.start, matchPos[i].match.end),
          str.slice(matchPos[i].left.start, matchPos[i].left.end),
          str.slice(matchPos[i].right.start, matchPos[i].right.end)
        )
      );
      if (i < lng - 1) {
        bits.push(str.slice(matchPos[i].wholeMatch.end, matchPos[i + 1].wholeMatch.start));
      }
    }
    if (matchPos[lng - 1].wholeMatch.end < str.length) {
      bits.push(str.slice(matchPos[lng - 1].wholeMatch.end));
    }
    finalStr = bits.join('');
  }
  return finalStr;
};

/**
 * Returns the index within the passed String object of the first occurrence of the specified regex,
 * starting the search at fromIndex. Returns -1 if the value is not found.
 *
 * @param {string} str string to search
 * @param {RegExp} regex Regular expression to search
 * @param {int} [fromIndex = 0] Index to start the search
 * @returns {Number}
 * @throws InvalidArgumentError
 */
showdown.helper.regexIndexOf = function (str, regex, fromIndex) {
  'use strict';
  if (!showdown.helper.isString(str)) {
    throw 'InvalidArgumentError: first parameter of showdown.helper.regexIndexOf function must be a string';
  }
  if (regex instanceof RegExp === false) {
    throw 'InvalidArgumentError: second parameter of showdown.helper.regexIndexOf function must be an instance of RegExp';
  }
  var indexOf = str.substring(fromIndex || 0).search(regex);
  return (indexOf >= 0) ? (indexOf + (fromIndex || 0)) : indexOf;
};

/**
 * Splits the passed string object at the defined index, and returns an array composed of the two substrings
 * @param {string} str string to split
 * @param {int} index index to split string at
 * @returns {[string,string]}
 * @throws InvalidArgumentError
 */
showdown.helper.splitAtIndex = function (str, index) {
  'use strict';
  if (!showdown.helper.isString(str)) {
    throw 'InvalidArgumentError: first parameter of showdown.helper.regexIndexOf function must be a string';
  }
  return [str.substring(0, index), str.substring(index)];
};

/**
 * Obfuscate an e-mail address through the use of Character Entities,
 * transforming ASCII characters into their equivalent decimal or hex entities.
 *
 * Since it has a random component, subsequent calls to this function produce different results
 *
 * @param {string} mail
 * @returns {string}
 */
showdown.helper.encodeEmailAddress = function (mail) {
  'use strict';
  var encode = [
    function (ch) {
      return '&#' + ch.charCodeAt(0) + ';';
    },
    function (ch) {
      return '&#x' + ch.charCodeAt(0).toString(16) + ';';
    },
    function (ch) {
      return ch;
    }
  ];

  mail = mail.replace(/./g, function (ch) {
    if (ch === '@') {
      // this *must* be encoded. I insist.
      ch = encode[Math.floor(Math.random() * 2)](ch);
    } else {
      var r = Math.random();
      // roughly 10% raw, 45% hex, 45% dec
      ch = (
        r > 0.9 ? encode[2](ch) : r > 0.45 ? encode[1](ch) : encode[0](ch)
      );
    }
    return ch;
  });

  return mail;
};

/**
 *
 * @param str
 * @param targetLength
 * @param padString
 * @returns {string}
 */
showdown.helper.padEnd = function padEnd (str, targetLength, padString) {
  'use strict';
  /*jshint bitwise: false*/
  // eslint-disable-next-line space-infix-ops
  targetLength = targetLength>>0; //floor if number or convert non-number to 0;
  /*jshint bitwise: true*/
  padString = String(padString || ' ');
  if (str.length > targetLength) {
    return String(str);
  } else {
    targetLength = targetLength - str.length;
    if (targetLength > padString.length) {
      padString += padString.repeat(targetLength / padString.length); //append to original to ensure we are longer than needed
    }
    return String(str) + padString.slice(0,targetLength);
  }
};

/**
 * POLYFILLS
 */
// use this instead of builtin is undefined for IE8 compatibility
if (typeof console === 'undefined') {
  console = {
    warn: function (msg) {
      'use strict';
      alert(msg);
    },
    log: function (msg) {
      'use strict';
      alert(msg);
    },
    error: function (msg) {
      'use strict';
      throw msg;
    }
  };
}

/**
 * Common regexes.
 * We declare some common regexes to improve performance
 */
showdown.helper.regexes = {
  asteriskDashAndColon: /([*_:~])/g
};

/**
 * EMOJIS LIST
 */
showdown.helper.emojis = {
  '+1':'\ud83d\udc4d',
  '-1':'\ud83d\udc4e',
  '100':'\ud83d\udcaf',
  '1234':'\ud83d\udd22',
  '1st_place_medal':'\ud83e\udd47',
  '2nd_place_medal':'\ud83e\udd48',
  '3rd_place_medal':'\ud83e\udd49',
  '8ball':'\ud83c\udfb1',
  'a':'\ud83c\udd70\ufe0f',
  'ab':'\ud83c\udd8e',
  'abc':'\ud83d\udd24',
  'abcd':'\ud83d\udd21',
  'accept':'\ud83c\ude51',
  'aerial_tramway':'\ud83d\udea1',
  'airplane':'\u2708\ufe0f',
  'alarm_clock':'\u23f0',
  'alembic':'\u2697\ufe0f',
  'alien':'\ud83d\udc7d',
  'ambulance':'\ud83d\ude91',
  'amphora':'\ud83c\udffa',
  'anchor':'\u2693\ufe0f',
  'angel':'\ud83d\udc7c',
  'anger':'\ud83d\udca2',
  'angry':'\ud83d\ude20',
  'anguished':'\ud83d\ude27',
  'ant':'\ud83d\udc1c',
  'apple':'\ud83c\udf4e',
  'aquarius':'\u2652\ufe0f',
  'aries':'\u2648\ufe0f',
  'arrow_backward':'\u25c0\ufe0f',
  'arrow_double_down':'\u23ec',
  'arrow_double_up':'\u23eb',
  'arrow_down':'\u2b07\ufe0f',
  'arrow_down_small':'\ud83d\udd3d',
  'arrow_forward':'\u25b6\ufe0f',
  'arrow_heading_down':'\u2935\ufe0f',
  'arrow_heading_up':'\u2934\ufe0f',
  'arrow_left':'\u2b05\ufe0f',
  'arrow_lower_left':'\u2199\ufe0f',
  'arrow_lower_right':'\u2198\ufe0f',
  'arrow_right':'\u27a1\ufe0f',
  'arrow_right_hook':'\u21aa\ufe0f',
  'arrow_up':'\u2b06\ufe0f',
  'arrow_up_down':'\u2195\ufe0f',
  'arrow_up_small':'\ud83d\udd3c',
  'arrow_upper_left':'\u2196\ufe0f',
  'arrow_upper_right':'\u2197\ufe0f',
  'arrows_clockwise':'\ud83d\udd03',
  'arrows_counterclockwise':'\ud83d\udd04',
  'art':'\ud83c\udfa8',
  'articulated_lorry':'\ud83d\ude9b',
  'artificial_satellite':'\ud83d\udef0',
  'astonished':'\ud83d\ude32',
  'athletic_shoe':'\ud83d\udc5f',
  'atm':'\ud83c\udfe7',
  'atom_symbol':'\u269b\ufe0f',
  'avocado':'\ud83e\udd51',
  'b':'\ud83c\udd71\ufe0f',
  'baby':'\ud83d\udc76',
  'baby_bottle':'\ud83c\udf7c',
  'baby_chick':'\ud83d\udc24',
  'baby_symbol':'\ud83d\udebc',
  'back':'\ud83d\udd19',
  'bacon':'\ud83e\udd53',
  'badminton':'\ud83c\udff8',
  'baggage_claim':'\ud83d\udec4',
  'baguette_bread':'\ud83e\udd56',
  'balance_scale':'\u2696\ufe0f',
  'balloon':'\ud83c\udf88',
  'ballot_box':'\ud83d\uddf3',
  'ballot_box_with_check':'\u2611\ufe0f',
  'bamboo':'\ud83c\udf8d',
  'banana':'\ud83c\udf4c',
  'bangbang':'\u203c\ufe0f',
  'bank':'\ud83c\udfe6',
  'bar_chart':'\ud83d\udcca',
  'barber':'\ud83d\udc88',
  'baseball':'\u26be\ufe0f',
  'basketball':'\ud83c\udfc0',
  'basketball_man':'\u26f9\ufe0f',
  'basketball_woman':'\u26f9\ufe0f&zwj;\u2640\ufe0f',
  'bat':'\ud83e\udd87',
  'bath':'\ud83d\udec0',
  'bathtub':'\ud83d\udec1',
  'battery':'\ud83d\udd0b',
  'beach_umbrella':'\ud83c\udfd6',
  'bear':'\ud83d\udc3b',
  'bed':'\ud83d\udecf',
  'bee':'\ud83d\udc1d',
  'beer':'\ud83c\udf7a',
  'beers':'\ud83c\udf7b',
  'beetle':'\ud83d\udc1e',
  'beginner':'\ud83d\udd30',
  'bell':'\ud83d\udd14',
  'bellhop_bell':'\ud83d\udece',
  'bento':'\ud83c\udf71',
  'biking_man':'\ud83d\udeb4',
  'bike':'\ud83d\udeb2',
  'biking_woman':'\ud83d\udeb4&zwj;\u2640\ufe0f',
  'bikini':'\ud83d\udc59',
  'biohazard':'\u2623\ufe0f',
  'bird':'\ud83d\udc26',
  'birthday':'\ud83c\udf82',
  'black_circle':'\u26ab\ufe0f',
  'black_flag':'\ud83c\udff4',
  'black_heart':'\ud83d\udda4',
  'black_joker':'\ud83c\udccf',
  'black_large_square':'\u2b1b\ufe0f',
  'black_medium_small_square':'\u25fe\ufe0f',
  'black_medium_square':'\u25fc\ufe0f',
  'black_nib':'\u2712\ufe0f',
  'black_small_square':'\u25aa\ufe0f',
  'black_square_button':'\ud83d\udd32',
  'blonde_man':'\ud83d\udc71',
  'blonde_woman':'\ud83d\udc71&zwj;\u2640\ufe0f',
  'blossom':'\ud83c\udf3c',
  'blowfish':'\ud83d\udc21',
  'blue_book':'\ud83d\udcd8',
  'blue_car':'\ud83d\ude99',
  'blue_heart':'\ud83d\udc99',
  'blush':'\ud83d\ude0a',
  'boar':'\ud83d\udc17',
  'boat':'\u26f5\ufe0f',
  'bomb':'\ud83d\udca3',
  'book':'\ud83d\udcd6',
  'bookmark':'\ud83d\udd16',
  'bookmark_tabs':'\ud83d\udcd1',
  'books':'\ud83d\udcda',
  'boom':'\ud83d\udca5',
  'boot':'\ud83d\udc62',
  'bouquet':'\ud83d\udc90',
  'bowing_man':'\ud83d\ude47',
  'bow_and_arrow':'\ud83c\udff9',
  'bowing_woman':'\ud83d\ude47&zwj;\u2640\ufe0f',
  'bowling':'\ud83c\udfb3',
  'boxing_glove':'\ud83e\udd4a',
  'boy':'\ud83d\udc66',
  'bread':'\ud83c\udf5e',
  'bride_with_veil':'\ud83d\udc70',
  'bridge_at_night':'\ud83c\udf09',
  'briefcase':'\ud83d\udcbc',
  'broken_heart':'\ud83d\udc94',
  'bug':'\ud83d\udc1b',
  'building_construction':'\ud83c\udfd7',
  'bulb':'\ud83d\udca1',
  'bullettrain_front':'\ud83d\ude85',
  'bullettrain_side':'\ud83d\ude84',
  'burrito':'\ud83c\udf2f',
  'bus':'\ud83d\ude8c',
  'business_suit_levitating':'\ud83d\udd74',
  'busstop':'\ud83d\ude8f',
  'bust_in_silhouette':'\ud83d\udc64',
  'busts_in_silhouette':'\ud83d\udc65',
  'butterfly':'\ud83e\udd8b',
  'cactus':'\ud83c\udf35',
  'cake':'\ud83c\udf70',
  'calendar':'\ud83d\udcc6',
  'call_me_hand':'\ud83e\udd19',
  'calling':'\ud83d\udcf2',
  'camel':'\ud83d\udc2b',
  'camera':'\ud83d\udcf7',
  'camera_flash':'\ud83d\udcf8',
  'camping':'\ud83c\udfd5',
  'cancer':'\u264b\ufe0f',
  'candle':'\ud83d\udd6f',
  'candy':'\ud83c\udf6c',
  'canoe':'\ud83d\udef6',
  'capital_abcd':'\ud83d\udd20',
  'capricorn':'\u2651\ufe0f',
  'car':'\ud83d\ude97',
  'card_file_box':'\ud83d\uddc3',
  'card_index':'\ud83d\udcc7',
  'card_index_dividers':'\ud83d\uddc2',
  'carousel_horse':'\ud83c\udfa0',
  'carrot':'\ud83e\udd55',
  'cat':'\ud83d\udc31',
  'cat2':'\ud83d\udc08',
  'cd':'\ud83d\udcbf',
  'chains':'\u26d3',
  'champagne':'\ud83c\udf7e',
  'chart':'\ud83d\udcb9',
  'chart_with_downwards_trend':'\ud83d\udcc9',
  'chart_with_upwards_trend':'\ud83d\udcc8',
  'checkered_flag':'\ud83c\udfc1',
  'cheese':'\ud83e\uddc0',
  'cherries':'\ud83c\udf52',
  'cherry_blossom':'\ud83c\udf38',
  'chestnut':'\ud83c\udf30',
  'chicken':'\ud83d\udc14',
  'children_crossing':'\ud83d\udeb8',
  'chipmunk':'\ud83d\udc3f',
  'chocolate_bar':'\ud83c\udf6b',
  'christmas_tree':'\ud83c\udf84',
  'church':'\u26ea\ufe0f',
  'cinema':'\ud83c\udfa6',
  'circus_tent':'\ud83c\udfaa',
  'city_sunrise':'\ud83c\udf07',
  'city_sunset':'\ud83c\udf06',
  'cityscape':'\ud83c\udfd9',
  'cl':'\ud83c\udd91',
  'clamp':'\ud83d\udddc',
  'clap':'\ud83d\udc4f',
  'clapper':'\ud83c\udfac',
  'classical_building':'\ud83c\udfdb',
  'clinking_glasses':'\ud83e\udd42',
  'clipboard':'\ud83d\udccb',
  'clock1':'\ud83d\udd50',
  'clock10':'\ud83d\udd59',
  'clock1030':'\ud83d\udd65',
  'clock11':'\ud83d\udd5a',
  'clock1130':'\ud83d\udd66',
  'clock12':'\ud83d\udd5b',
  'clock1230':'\ud83d\udd67',
  'clock130':'\ud83d\udd5c',
  'clock2':'\ud83d\udd51',
  'clock230':'\ud83d\udd5d',
  'clock3':'\ud83d\udd52',
  'clock330':'\ud83d\udd5e',
  'clock4':'\ud83d\udd53',
  'clock430':'\ud83d\udd5f',
  'clock5':'\ud83d\udd54',
  'clock530':'\ud83d\udd60',
  'clock6':'\ud83d\udd55',
  'clock630':'\ud83d\udd61',
  'clock7':'\ud83d\udd56',
  'clock730':'\ud83d\udd62',
  'clock8':'\ud83d\udd57',
  'clock830':'\ud83d\udd63',
  'clock9':'\ud83d\udd58',
  'clock930':'\ud83d\udd64',
  'closed_book':'\ud83d\udcd5',
  'closed_lock_with_key':'\ud83d\udd10',
  'closed_umbrella':'\ud83c\udf02',
  'cloud':'\u2601\ufe0f',
  'cloud_with_lightning':'\ud83c\udf29',
  'cloud_with_lightning_and_rain':'\u26c8',
  'cloud_with_rain':'\ud83c\udf27',
  'cloud_with_snow':'\ud83c\udf28',
  'clown_face':'\ud83e\udd21',
  'clubs':'\u2663\ufe0f',
  'cocktail':'\ud83c\udf78',
  'coffee':'\u2615\ufe0f',
  'coffin':'\u26b0\ufe0f',
  'cold_sweat':'\ud83d\ude30',
  'comet':'\u2604\ufe0f',
  'computer':'\ud83d\udcbb',
  'computer_mouse':'\ud83d\uddb1',
  'confetti_ball':'\ud83c\udf8a',
  'confounded':'\ud83d\ude16',
  'confused':'\ud83d\ude15',
  'congratulations':'\u3297\ufe0f',
  'construction':'\ud83d\udea7',
  'construction_worker_man':'\ud83d\udc77',
  'construction_worker_woman':'\ud83d\udc77&zwj;\u2640\ufe0f',
  'control_knobs':'\ud83c\udf9b',
  'convenience_store':'\ud83c\udfea',
  'cookie':'\ud83c\udf6a',
  'cool':'\ud83c\udd92',
  'policeman':'\ud83d\udc6e',
  'copyright':'\u00a9\ufe0f',
  'corn':'\ud83c\udf3d',
  'couch_and_lamp':'\ud83d\udecb',
  'couple':'\ud83d\udc6b',
  'couple_with_heart_woman_man':'\ud83d\udc91',
  'couple_with_heart_man_man':'\ud83d\udc68&zwj;\u2764\ufe0f&zwj;\ud83d\udc68',
  'couple_with_heart_woman_woman':'\ud83d\udc69&zwj;\u2764\ufe0f&zwj;\ud83d\udc69',
  'couplekiss_man_man':'\ud83d\udc68&zwj;\u2764\ufe0f&zwj;\ud83d\udc8b&zwj;\ud83d\udc68',
  'couplekiss_man_woman':'\ud83d\udc8f',
  'couplekiss_woman_woman':'\ud83d\udc69&zwj;\u2764\ufe0f&zwj;\ud83d\udc8b&zwj;\ud83d\udc69',
  'cow':'\ud83d\udc2e',
  'cow2':'\ud83d\udc04',
  'cowboy_hat_face':'\ud83e\udd20',
  'crab':'\ud83e\udd80',
  'crayon':'\ud83d\udd8d',
  'credit_card':'\ud83d\udcb3',
  'crescent_moon':'\ud83c\udf19',
  'cricket':'\ud83c\udfcf',
  'crocodile':'\ud83d\udc0a',
  'croissant':'\ud83e\udd50',
  'crossed_fingers':'\ud83e\udd1e',
  'crossed_flags':'\ud83c\udf8c',
  'crossed_swords':'\u2694\ufe0f',
  'crown':'\ud83d\udc51',
  'cry':'\ud83d\ude22',
  'crying_cat_face':'\ud83d\ude3f',
  'crystal_ball':'\ud83d\udd2e',
  'cucumber':'\ud83e\udd52',
  'cupid':'\ud83d\udc98',
  'curly_loop':'\u27b0',
  'currency_exchange':'\ud83d\udcb1',
  'curry':'\ud83c\udf5b',
  'custard':'\ud83c\udf6e',
  'customs':'\ud83d\udec3',
  'cyclone':'\ud83c\udf00',
  'dagger':'\ud83d\udde1',
  'dancer':'\ud83d\udc83',
  'dancing_women':'\ud83d\udc6f',
  'dancing_men':'\ud83d\udc6f&zwj;\u2642\ufe0f',
  'dango':'\ud83c\udf61',
  'dark_sunglasses':'\ud83d\udd76',
  'dart':'\ud83c\udfaf',
  'dash':'\ud83d\udca8',
  'date':'\ud83d\udcc5',
  'deciduous_tree':'\ud83c\udf33',
  'deer':'\ud83e\udd8c',
  'department_store':'\ud83c\udfec',
  'derelict_house':'\ud83c\udfda',
  'desert':'\ud83c\udfdc',
  'desert_island':'\ud83c\udfdd',
  'desktop_computer':'\ud83d\udda5',
  'male_detective':'\ud83d\udd75\ufe0f',
  'diamond_shape_with_a_dot_inside':'\ud83d\udca0',
  'diamonds':'\u2666\ufe0f',
  'disappointed':'\ud83d\ude1e',
  'disappointed_relieved':'\ud83d\ude25',
  'dizzy':'\ud83d\udcab',
  'dizzy_face':'\ud83d\ude35',
  'do_not_litter':'\ud83d\udeaf',
  'dog':'\ud83d\udc36',
  'dog2':'\ud83d\udc15',
  'dollar':'\ud83d\udcb5',
  'dolls':'\ud83c\udf8e',
  'dolphin':'\ud83d\udc2c',
  'door':'\ud83d\udeaa',
  'doughnut':'\ud83c\udf69',
  'dove':'\ud83d\udd4a',
  'dragon':'\ud83d\udc09',
  'dragon_face':'\ud83d\udc32',
  'dress':'\ud83d\udc57',
  'dromedary_camel':'\ud83d\udc2a',
  'drooling_face':'\ud83e\udd24',
  'droplet':'\ud83d\udca7',
  'drum':'\ud83e\udd41',
  'duck':'\ud83e\udd86',
  'dvd':'\ud83d\udcc0',
  'e-mail':'\ud83d\udce7',
  'eagle':'\ud83e\udd85',
  'ear':'\ud83d\udc42',
  'ear_of_rice':'\ud83c\udf3e',
  'earth_africa':'\ud83c\udf0d',
  'earth_americas':'\ud83c\udf0e',
  'earth_asia':'\ud83c\udf0f',
  'egg':'\ud83e\udd5a',
  'eggplant':'\ud83c\udf46',
  'eight_pointed_black_star':'\u2734\ufe0f',
  'eight_spoked_asterisk':'\u2733\ufe0f',
  'electric_plug':'\ud83d\udd0c',
  'elephant':'\ud83d\udc18',
  'email':'\u2709\ufe0f',
  'end':'\ud83d\udd1a',
  'envelope_with_arrow':'\ud83d\udce9',
  'euro':'\ud83d\udcb6',
  'european_castle':'\ud83c\udff0',
  'european_post_office':'\ud83c\udfe4',
  'evergreen_tree':'\ud83c\udf32',
  'exclamation':'\u2757\ufe0f',
  'expressionless':'\ud83d\ude11',
  'eye':'\ud83d\udc41',
  'eye_speech_bubble':'\ud83d\udc41&zwj;\ud83d\udde8',
  'eyeglasses':'\ud83d\udc53',
  'eyes':'\ud83d\udc40',
  'face_with_head_bandage':'\ud83e\udd15',
  'face_with_thermometer':'\ud83e\udd12',
  'fist_oncoming':'\ud83d\udc4a',
  'factory':'\ud83c\udfed',
  'fallen_leaf':'\ud83c\udf42',
  'family_man_woman_boy':'\ud83d\udc6a',
  'family_man_boy':'\ud83d\udc68&zwj;\ud83d\udc66',
  'family_man_boy_boy':'\ud83d\udc68&zwj;\ud83d\udc66&zwj;\ud83d\udc66',
  'family_man_girl':'\ud83d\udc68&zwj;\ud83d\udc67',
  'family_man_girl_boy':'\ud83d\udc68&zwj;\ud83d\udc67&zwj;\ud83d\udc66',
  'family_man_girl_girl':'\ud83d\udc68&zwj;\ud83d\udc67&zwj;\ud83d\udc67',
  'family_man_man_boy':'\ud83d\udc68&zwj;\ud83d\udc68&zwj;\ud83d\udc66',
  'family_man_man_boy_boy':'\ud83d\udc68&zwj;\ud83d\udc68&zwj;\ud83d\udc66&zwj;\ud83d\udc66',
  'family_man_man_girl':'\ud83d\udc68&zwj;\ud83d\udc68&zwj;\ud83d\udc67',
  'family_man_man_girl_boy':'\ud83d\udc68&zwj;\ud83d\udc68&zwj;\ud83d\udc67&zwj;\ud83d\udc66',
  'family_man_man_girl_girl':'\ud83d\udc68&zwj;\ud83d\udc68&zwj;\ud83d\udc67&zwj;\ud83d\udc67',
  'family_man_woman_boy_boy':'\ud83d\udc68&zwj;\ud83d\udc69&zwj;\ud83d\udc66&zwj;\ud83d\udc66',
  'family_man_woman_girl':'\ud83d\udc68&zwj;\ud83d\udc69&zwj;\ud83d\udc67',
  'family_man_woman_girl_boy':'\ud83d\udc68&zwj;\ud83d\udc69&zwj;\ud83d\udc67&zwj;\ud83d\udc66',
  'family_man_woman_girl_girl':'\ud83d\udc68&zwj;\ud83d\udc69&zwj;\ud83d\udc67&zwj;\ud83d\udc67',
  'family_woman_boy':'\ud83d\udc69&zwj;\ud83d\udc66',
  'family_woman_boy_boy':'\ud83d\udc69&zwj;\ud83d\udc66&zwj;\ud83d\udc66',
  'family_woman_girl':'\ud83d\udc69&zwj;\ud83d\udc67',
  'family_woman_girl_boy':'\ud83d\udc69&zwj;\ud83d\udc67&zwj;\ud83d\udc66',
  'family_woman_girl_girl':'\ud83d\udc69&zwj;\ud83d\udc67&zwj;\ud83d\udc67',
  'family_woman_woman_boy':'\ud83d\udc69&zwj;\ud83d\udc69&zwj;\ud83d\udc66',
  'family_woman_woman_boy_boy':'\ud83d\udc69&zwj;\ud83d\udc69&zwj;\ud83d\udc66&zwj;\ud83d\udc66',
  'family_woman_woman_girl':'\ud83d\udc69&zwj;\ud83d\udc69&zwj;\ud83d\udc67',
  'family_woman_woman_girl_boy':'\ud83d\udc69&zwj;\ud83d\udc69&zwj;\ud83d\udc67&zwj;\ud83d\udc66',
  'family_woman_woman_girl_girl':'\ud83d\udc69&zwj;\ud83d\udc69&zwj;\ud83d\udc67&zwj;\ud83d\udc67',
  'fast_forward':'\u23e9',
  'fax':'\ud83d\udce0',
  'fearful':'\ud83d\ude28',
  'feet':'\ud83d\udc3e',
  'female_detective':'\ud83d\udd75\ufe0f&zwj;\u2640\ufe0f',
  'ferris_wheel':'\ud83c\udfa1',
  'ferry':'\u26f4',
  'field_hockey':'\ud83c\udfd1',
  'file_cabinet':'\ud83d\uddc4',
  'file_folder':'\ud83d\udcc1',
  'film_projector':'\ud83d\udcfd',
  'film_strip':'\ud83c\udf9e',
  'fire':'\ud83d\udd25',
  'fire_engine':'\ud83d\ude92',
  'fireworks':'\ud83c\udf86',
  'first_quarter_moon':'\ud83c\udf13',
  'first_quarter_moon_with_face':'\ud83c\udf1b',
  'fish':'\ud83d\udc1f',
  'fish_cake':'\ud83c\udf65',
  'fishing_pole_and_fish':'\ud83c\udfa3',
  'fist_raised':'\u270a',
  'fist_left':'\ud83e\udd1b',
  'fist_right':'\ud83e\udd1c',
  'flags':'\ud83c\udf8f',
  'flashlight':'\ud83d\udd26',
  'fleur_de_lis':'\u269c\ufe0f',
  'flight_arrival':'\ud83d\udeec',
  'flight_departure':'\ud83d\udeeb',
  'floppy_disk':'\ud83d\udcbe',
  'flower_playing_cards':'\ud83c\udfb4',
  'flushed':'\ud83d\ude33',
  'fog':'\ud83c\udf2b',
  'foggy':'\ud83c\udf01',
  'football':'\ud83c\udfc8',
  'footprints':'\ud83d\udc63',
  'fork_and_knife':'\ud83c\udf74',
  'fountain':'\u26f2\ufe0f',
  'fountain_pen':'\ud83d\udd8b',
  'four_leaf_clover':'\ud83c\udf40',
  'fox_face':'\ud83e\udd8a',
  'framed_picture':'\ud83d\uddbc',
  'free':'\ud83c\udd93',
  'fried_egg':'\ud83c\udf73',
  'fried_shrimp':'\ud83c\udf64',
  'fries':'\ud83c\udf5f',
  'frog':'\ud83d\udc38',
  'frowning':'\ud83d\ude26',
  'frowning_face':'\u2639\ufe0f',
  'frowning_man':'\ud83d\ude4d&zwj;\u2642\ufe0f',
  'frowning_woman':'\ud83d\ude4d',
  'middle_finger':'\ud83d\udd95',
  'fuelpump':'\u26fd\ufe0f',
  'full_moon':'\ud83c\udf15',
  'full_moon_with_face':'\ud83c\udf1d',
  'funeral_urn':'\u26b1\ufe0f',
  'game_die':'\ud83c\udfb2',
  'gear':'\u2699\ufe0f',
  'gem':'\ud83d\udc8e',
  'gemini':'\u264a\ufe0f',
  'ghost':'\ud83d\udc7b',
  'gift':'\ud83c\udf81',
  'gift_heart':'\ud83d\udc9d',
  'girl':'\ud83d\udc67',
  'globe_with_meridians':'\ud83c\udf10',
  'goal_net':'\ud83e\udd45',
  'goat':'\ud83d\udc10',
  'golf':'\u26f3\ufe0f',
  'golfing_man':'\ud83c\udfcc\ufe0f',
  'golfing_woman':'\ud83c\udfcc\ufe0f&zwj;\u2640\ufe0f',
  'gorilla':'\ud83e\udd8d',
  'grapes':'\ud83c\udf47',
  'green_apple':'\ud83c\udf4f',
  'green_book':'\ud83d\udcd7',
  'green_heart':'\ud83d\udc9a',
  'green_salad':'\ud83e\udd57',
  'grey_exclamation':'\u2755',
  'grey_question':'\u2754',
  'grimacing':'\ud83d\ude2c',
  'grin':'\ud83d\ude01',
  'grinning':'\ud83d\ude00',
  'guardsman':'\ud83d\udc82',
  'guardswoman':'\ud83d\udc82&zwj;\u2640\ufe0f',
  'guitar':'\ud83c\udfb8',
  'gun':'\ud83d\udd2b',
  'haircut_woman':'\ud83d\udc87',
  'haircut_man':'\ud83d\udc87&zwj;\u2642\ufe0f',
  'hamburger':'\ud83c\udf54',
  'hammer':'\ud83d\udd28',
  'hammer_and_pick':'\u2692',
  'hammer_and_wrench':'\ud83d\udee0',
  'hamster':'\ud83d\udc39',
  'hand':'\u270b',
  'handbag':'\ud83d\udc5c',
  'handshake':'\ud83e\udd1d',
  'hankey':'\ud83d\udca9',
  'hatched_chick':'\ud83d\udc25',
  'hatching_chick':'\ud83d\udc23',
  'headphones':'\ud83c\udfa7',
  'hear_no_evil':'\ud83d\ude49',
  'heart':'\u2764\ufe0f',
  'heart_decoration':'\ud83d\udc9f',
  'heart_eyes':'\ud83d\ude0d',
  'heart_eyes_cat':'\ud83d\ude3b',
  'heartbeat':'\ud83d\udc93',
  'heartpulse':'\ud83d\udc97',
  'hearts':'\u2665\ufe0f',
  'heavy_check_mark':'\u2714\ufe0f',
  'heavy_division_sign':'\u2797',
  'heavy_dollar_sign':'\ud83d\udcb2',
  'heavy_heart_exclamation':'\u2763\ufe0f',
  'heavy_minus_sign':'\u2796',
  'heavy_multiplication_x':'\u2716\ufe0f',
  'heavy_plus_sign':'\u2795',
  'helicopter':'\ud83d\ude81',
  'herb':'\ud83c\udf3f',
  'hibiscus':'\ud83c\udf3a',
  'high_brightness':'\ud83d\udd06',
  'high_heel':'\ud83d\udc60',
  'hocho':'\ud83d\udd2a',
  'hole':'\ud83d\udd73',
  'honey_pot':'\ud83c\udf6f',
  'horse':'\ud83d\udc34',
  'horse_racing':'\ud83c\udfc7',
  'hospital':'\ud83c\udfe5',
  'hot_pepper':'\ud83c\udf36',
  'hotdog':'\ud83c\udf2d',
  'hotel':'\ud83c\udfe8',
  'hotsprings':'\u2668\ufe0f',
  'hourglass':'\u231b\ufe0f',
  'hourglass_flowing_sand':'\u23f3',
  'house':'\ud83c\udfe0',
  'house_with_garden':'\ud83c\udfe1',
  'houses':'\ud83c\udfd8',
  'hugs':'\ud83e\udd17',
  'hushed':'\ud83d\ude2f',
  'ice_cream':'\ud83c\udf68',
  'ice_hockey':'\ud83c\udfd2',
  'ice_skate':'\u26f8',
  'icecream':'\ud83c\udf66',
  'id':'\ud83c\udd94',
  'ideograph_advantage':'\ud83c\ude50',
  'imp':'\ud83d\udc7f',
  'inbox_tray':'\ud83d\udce5',
  'incoming_envelope':'\ud83d\udce8',
  'tipping_hand_woman':'\ud83d\udc81',
  'information_source':'\u2139\ufe0f',
  'innocent':'\ud83d\ude07',
  'interrobang':'\u2049\ufe0f',
  'iphone':'\ud83d\udcf1',
  'izakaya_lantern':'\ud83c\udfee',
  'jack_o_lantern':'\ud83c\udf83',
  'japan':'\ud83d\uddfe',
  'japanese_castle':'\ud83c\udfef',
  'japanese_goblin':'\ud83d\udc7a',
  'japanese_ogre':'\ud83d\udc79',
  'jeans':'\ud83d\udc56',
  'joy':'\ud83d\ude02',
  'joy_cat':'\ud83d\ude39',
  'joystick':'\ud83d\udd79',
  'kaaba':'\ud83d\udd4b',
  'key':'\ud83d\udd11',
  'keyboard':'\u2328\ufe0f',
  'keycap_ten':'\ud83d\udd1f',
  'kick_scooter':'\ud83d\udef4',
  'kimono':'\ud83d\udc58',
  'kiss':'\ud83d\udc8b',
  'kissing':'\ud83d\ude17',
  'kissing_cat':'\ud83d\ude3d',
  'kissing_closed_eyes':'\ud83d\ude1a',
  'kissing_heart':'\ud83d\ude18',
  'kissing_smiling_eyes':'\ud83d\ude19',
  'kiwi_fruit':'\ud83e\udd5d',
  'koala':'\ud83d\udc28',
  'koko':'\ud83c\ude01',
  'label':'\ud83c\udff7',
  'large_blue_circle':'\ud83d\udd35',
  'large_blue_diamond':'\ud83d\udd37',
  'large_orange_diamond':'\ud83d\udd36',
  'last_quarter_moon':'\ud83c\udf17',
  'last_quarter_moon_with_face':'\ud83c\udf1c',
  'latin_cross':'\u271d\ufe0f',
  'laughing':'\ud83d\ude06',
  'leaves':'\ud83c\udf43',
  'ledger':'\ud83d\udcd2',
  'left_luggage':'\ud83d\udec5',
  'left_right_arrow':'\u2194\ufe0f',
  'leftwards_arrow_with_hook':'\u21a9\ufe0f',
  'lemon':'\ud83c\udf4b',
  'leo':'\u264c\ufe0f',
  'leopard':'\ud83d\udc06',
  'level_slider':'\ud83c\udf9a',
  'libra':'\u264e\ufe0f',
  'light_rail':'\ud83d\ude88',
  'link':'\ud83d\udd17',
  'lion':'\ud83e\udd81',
  'lips':'\ud83d\udc44',
  'lipstick':'\ud83d\udc84',
  'lizard':'\ud83e\udd8e',
  'lock':'\ud83d\udd12',
  'lock_with_ink_pen':'\ud83d\udd0f',
  'lollipop':'\ud83c\udf6d',
  'loop':'\u27bf',
  'loud_sound':'\ud83d\udd0a',
  'loudspeaker':'\ud83d\udce2',
  'love_hotel':'\ud83c\udfe9',
  'love_letter':'\ud83d\udc8c',
  'low_brightness':'\ud83d\udd05',
  'lying_face':'\ud83e\udd25',
  'm':'\u24c2\ufe0f',
  'mag':'\ud83d\udd0d',
  'mag_right':'\ud83d\udd0e',
  'mahjong':'\ud83c\udc04\ufe0f',
  'mailbox':'\ud83d\udceb',
  'mailbox_closed':'\ud83d\udcea',
  'mailbox_with_mail':'\ud83d\udcec',
  'mailbox_with_no_mail':'\ud83d\udced',
  'man':'\ud83d\udc68',
  'man_artist':'\ud83d\udc68&zwj;\ud83c\udfa8',
  'man_astronaut':'\ud83d\udc68&zwj;\ud83d\ude80',
  'man_cartwheeling':'\ud83e\udd38&zwj;\u2642\ufe0f',
  'man_cook':'\ud83d\udc68&zwj;\ud83c\udf73',
  'man_dancing':'\ud83d\udd7a',
  'man_facepalming':'\ud83e\udd26&zwj;\u2642\ufe0f',
  'man_factory_worker':'\ud83d\udc68&zwj;\ud83c\udfed',
  'man_farmer':'\ud83d\udc68&zwj;\ud83c\udf3e',
  'man_firefighter':'\ud83d\udc68&zwj;\ud83d\ude92',
  'man_health_worker':'\ud83d\udc68&zwj;\u2695\ufe0f',
  'man_in_tuxedo':'\ud83e\udd35',
  'man_judge':'\ud83d\udc68&zwj;\u2696\ufe0f',
  'man_juggling':'\ud83e\udd39&zwj;\u2642\ufe0f',
  'man_mechanic':'\ud83d\udc68&zwj;\ud83d\udd27',
  'man_office_worker':'\ud83d\udc68&zwj;\ud83d\udcbc',
  'man_pilot':'\ud83d\udc68&zwj;\u2708\ufe0f',
  'man_playing_handball':'\ud83e\udd3e&zwj;\u2642\ufe0f',
  'man_playing_water_polo':'\ud83e\udd3d&zwj;\u2642\ufe0f',
  'man_scientist':'\ud83d\udc68&zwj;\ud83d\udd2c',
  'man_shrugging':'\ud83e\udd37&zwj;\u2642\ufe0f',
  'man_singer':'\ud83d\udc68&zwj;\ud83c\udfa4',
  'man_student':'\ud83d\udc68&zwj;\ud83c\udf93',
  'man_teacher':'\ud83d\udc68&zwj;\ud83c\udfeb',
  'man_technologist':'\ud83d\udc68&zwj;\ud83d\udcbb',
  'man_with_gua_pi_mao':'\ud83d\udc72',
  'man_with_turban':'\ud83d\udc73',
  'tangerine':'\ud83c\udf4a',
  'mans_shoe':'\ud83d\udc5e',
  'mantelpiece_clock':'\ud83d\udd70',
  'maple_leaf':'\ud83c\udf41',
  'martial_arts_uniform':'\ud83e\udd4b',
  'mask':'\ud83d\ude37',
  'massage_woman':'\ud83d\udc86',
  'massage_man':'\ud83d\udc86&zwj;\u2642\ufe0f',
  'meat_on_bone':'\ud83c\udf56',
  'medal_military':'\ud83c\udf96',
  'medal_sports':'\ud83c\udfc5',
  'mega':'\ud83d\udce3',
  'melon':'\ud83c\udf48',
  'memo':'\ud83d\udcdd',
  'men_wrestling':'\ud83e\udd3c&zwj;\u2642\ufe0f',
  'menorah':'\ud83d\udd4e',
  'mens':'\ud83d\udeb9',
  'metal':'\ud83e\udd18',
  'metro':'\ud83d\ude87',
  'microphone':'\ud83c\udfa4',
  'microscope':'\ud83d\udd2c',
  'milk_glass':'\ud83e\udd5b',
  'milky_way':'\ud83c\udf0c',
  'minibus':'\ud83d\ude90',
  'minidisc':'\ud83d\udcbd',
  'mobile_phone_off':'\ud83d\udcf4',
  'money_mouth_face':'\ud83e\udd11',
  'money_with_wings':'\ud83d\udcb8',
  'moneybag':'\ud83d\udcb0',
  'monkey':'\ud83d\udc12',
  'monkey_face':'\ud83d\udc35',
  'monorail':'\ud83d\ude9d',
  'moon':'\ud83c\udf14',
  'mortar_board':'\ud83c\udf93',
  'mosque':'\ud83d\udd4c',
  'motor_boat':'\ud83d\udee5',
  'motor_scooter':'\ud83d\udef5',
  'motorcycle':'\ud83c\udfcd',
  'motorway':'\ud83d\udee3',
  'mount_fuji':'\ud83d\uddfb',
  'mountain':'\u26f0',
  'mountain_biking_man':'\ud83d\udeb5',
  'mountain_biking_woman':'\ud83d\udeb5&zwj;\u2640\ufe0f',
  'mountain_cableway':'\ud83d\udea0',
  'mountain_railway':'\ud83d\ude9e',
  'mountain_snow':'\ud83c\udfd4',
  'mouse':'\ud83d\udc2d',
  'mouse2':'\ud83d\udc01',
  'movie_camera':'\ud83c\udfa5',
  'moyai':'\ud83d\uddff',
  'mrs_claus':'\ud83e\udd36',
  'muscle':'\ud83d\udcaa',
  'mushroom':'\ud83c\udf44',
  'musical_keyboard':'\ud83c\udfb9',
  'musical_note':'\ud83c\udfb5',
  'musical_score':'\ud83c\udfbc',
  'mute':'\ud83d\udd07',
  'nail_care':'\ud83d\udc85',
  'name_badge':'\ud83d\udcdb',
  'national_park':'\ud83c\udfde',
  'nauseated_face':'\ud83e\udd22',
  'necktie':'\ud83d\udc54',
  'negative_squared_cross_mark':'\u274e',
  'nerd_face':'\ud83e\udd13',
  'neutral_face':'\ud83d\ude10',
  'new':'\ud83c\udd95',
  'new_moon':'\ud83c\udf11',
  'new_moon_with_face':'\ud83c\udf1a',
  'newspaper':'\ud83d\udcf0',
  'newspaper_roll':'\ud83d\uddde',
  'next_track_button':'\u23ed',
  'ng':'\ud83c\udd96',
  'no_good_man':'\ud83d\ude45&zwj;\u2642\ufe0f',
  'no_good_woman':'\ud83d\ude45',
  'night_with_stars':'\ud83c\udf03',
  'no_bell':'\ud83d\udd15',
  'no_bicycles':'\ud83d\udeb3',
  'no_entry':'\u26d4\ufe0f',
  'no_entry_sign':'\ud83d\udeab',
  'no_mobile_phones':'\ud83d\udcf5',
  'no_mouth':'\ud83d\ude36',
  'no_pedestrians':'\ud83d\udeb7',
  'no_smoking':'\ud83d\udead',
  'non-potable_water':'\ud83d\udeb1',
  'nose':'\ud83d\udc43',
  'notebook':'\ud83d\udcd3',
  'notebook_with_decorative_cover':'\ud83d\udcd4',
  'notes':'\ud83c\udfb6',
  'nut_and_bolt':'\ud83d\udd29',
  'o':'\u2b55\ufe0f',
  'o2':'\ud83c\udd7e\ufe0f',
  'ocean':'\ud83c\udf0a',
  'octopus':'\ud83d\udc19',
  'oden':'\ud83c\udf62',
  'office':'\ud83c\udfe2',
  'oil_drum':'\ud83d\udee2',
  'ok':'\ud83c\udd97',
  'ok_hand':'\ud83d\udc4c',
  'ok_man':'\ud83d\ude46&zwj;\u2642\ufe0f',
  'ok_woman':'\ud83d\ude46',
  'old_key':'\ud83d\udddd',
  'older_man':'\ud83d\udc74',
  'older_woman':'\ud83d\udc75',
  'om':'\ud83d\udd49',
  'on':'\ud83d\udd1b',
  'oncoming_automobile':'\ud83d\ude98',
  'oncoming_bus':'\ud83d\ude8d',
  'oncoming_police_car':'\ud83d\ude94',
  'oncoming_taxi':'\ud83d\ude96',
  'open_file_folder':'\ud83d\udcc2',
  'open_hands':'\ud83d\udc50',
  'open_mouth':'\ud83d\ude2e',
  'open_umbrella':'\u2602\ufe0f',
  'ophiuchus':'\u26ce',
  'orange_book':'\ud83d\udcd9',
  'orthodox_cross':'\u2626\ufe0f',
  'outbox_tray':'\ud83d\udce4',
  'owl':'\ud83e\udd89',
  'ox':'\ud83d\udc02',
  'package':'\ud83d\udce6',
  'page_facing_up':'\ud83d\udcc4',
  'page_with_curl':'\ud83d\udcc3',
  'pager':'\ud83d\udcdf',
  'paintbrush':'\ud83d\udd8c',
  'palm_tree':'\ud83c\udf34',
  'pancakes':'\ud83e\udd5e',
  'panda_face':'\ud83d\udc3c',
  'paperclip':'\ud83d\udcce',
  'paperclips':'\ud83d\udd87',
  'parasol_on_ground':'\u26f1',
  'parking':'\ud83c\udd7f\ufe0f',
  'part_alternation_mark':'\u303d\ufe0f',
  'partly_sunny':'\u26c5\ufe0f',
  'passenger_ship':'\ud83d\udef3',
  'passport_control':'\ud83d\udec2',
  'pause_button':'\u23f8',
  'peace_symbol':'\u262e\ufe0f',
  'peach':'\ud83c\udf51',
  'peanuts':'\ud83e\udd5c',
  'pear':'\ud83c\udf50',
  'pen':'\ud83d\udd8a',
  'pencil2':'\u270f\ufe0f',
  'penguin':'\ud83d\udc27',
  'pensive':'\ud83d\ude14',
  'performing_arts':'\ud83c\udfad',
  'persevere':'\ud83d\ude23',
  'person_fencing':'\ud83e\udd3a',
  'pouting_woman':'\ud83d\ude4e',
  'phone':'\u260e\ufe0f',
  'pick':'\u26cf',
  'pig':'\ud83d\udc37',
  'pig2':'\ud83d\udc16',
  'pig_nose':'\ud83d\udc3d',
  'pill':'\ud83d\udc8a',
  'pineapple':'\ud83c\udf4d',
  'ping_pong':'\ud83c\udfd3',
  'pisces':'\u2653\ufe0f',
  'pizza':'\ud83c\udf55',
  'place_of_worship':'\ud83d\uded0',
  'plate_with_cutlery':'\ud83c\udf7d',
  'play_or_pause_button':'\u23ef',
  'point_down':'\ud83d\udc47',
  'point_left':'\ud83d\udc48',
  'point_right':'\ud83d\udc49',
  'point_up':'\u261d\ufe0f',
  'point_up_2':'\ud83d\udc46',
  'police_car':'\ud83d\ude93',
  'policewoman':'\ud83d\udc6e&zwj;\u2640\ufe0f',
  'poodle':'\ud83d\udc29',
  'popcorn':'\ud83c\udf7f',
  'post_office':'\ud83c\udfe3',
  'postal_horn':'\ud83d\udcef',
  'postbox':'\ud83d\udcee',
  'potable_water':'\ud83d\udeb0',
  'potato':'\ud83e\udd54',
  'pouch':'\ud83d\udc5d',
  'poultry_leg':'\ud83c\udf57',
  'pound':'\ud83d\udcb7',
  'rage':'\ud83d\ude21',
  'pouting_cat':'\ud83d\ude3e',
  'pouting_man':'\ud83d\ude4e&zwj;\u2642\ufe0f',
  'pray':'\ud83d\ude4f',
  'prayer_beads':'\ud83d\udcff',
  'pregnant_woman':'\ud83e\udd30',
  'previous_track_button':'\u23ee',
  'prince':'\ud83e\udd34',
  'princess':'\ud83d\udc78',
  'printer':'\ud83d\udda8',
  'purple_heart':'\ud83d\udc9c',
  'purse':'\ud83d\udc5b',
  'pushpin':'\ud83d\udccc',
  'put_litter_in_its_place':'\ud83d\udeae',
  'question':'\u2753',
  'rabbit':'\ud83d\udc30',
  'rabbit2':'\ud83d\udc07',
  'racehorse':'\ud83d\udc0e',
  'racing_car':'\ud83c\udfce',
  'radio':'\ud83d\udcfb',
  'radio_button':'\ud83d\udd18',
  'radioactive':'\u2622\ufe0f',
  'railway_car':'\ud83d\ude83',
  'railway_track':'\ud83d\udee4',
  'rainbow':'\ud83c\udf08',
  'rainbow_flag':'\ud83c\udff3\ufe0f&zwj;\ud83c\udf08',
  'raised_back_of_hand':'\ud83e\udd1a',
  'raised_hand_with_fingers_splayed':'\ud83d\udd90',
  'raised_hands':'\ud83d\ude4c',
  'raising_hand_woman':'\ud83d\ude4b',
  'raising_hand_man':'\ud83d\ude4b&zwj;\u2642\ufe0f',
  'ram':'\ud83d\udc0f',
  'ramen':'\ud83c\udf5c',
  'rat':'\ud83d\udc00',
  'record_button':'\u23fa',
  'recycle':'\u267b\ufe0f',
  'red_circle':'\ud83d\udd34',
  'registered':'\u00ae\ufe0f',
  'relaxed':'\u263a\ufe0f',
  'relieved':'\ud83d\ude0c',
  'reminder_ribbon':'\ud83c\udf97',
  'repeat':'\ud83d\udd01',
  'repeat_one':'\ud83d\udd02',
  'rescue_worker_helmet':'\u26d1',
  'restroom':'\ud83d\udebb',
  'revolving_hearts':'\ud83d\udc9e',
  'rewind':'\u23ea',
  'rhinoceros':'\ud83e\udd8f',
  'ribbon':'\ud83c\udf80',
  'rice':'\ud83c\udf5a',
  'rice_ball':'\ud83c\udf59',
  'rice_cracker':'\ud83c\udf58',
  'rice_scene':'\ud83c\udf91',
  'right_anger_bubble':'\ud83d\uddef',
  'ring':'\ud83d\udc8d',
  'robot':'\ud83e\udd16',
  'rocket':'\ud83d\ude80',
  'rofl':'\ud83e\udd23',
  'roll_eyes':'\ud83d\ude44',
  'roller_coaster':'\ud83c\udfa2',
  'rooster':'\ud83d\udc13',
  'rose':'\ud83c\udf39',
  'rosette':'\ud83c\udff5',
  'rotating_light':'\ud83d\udea8',
  'round_pushpin':'\ud83d\udccd',
  'rowing_man':'\ud83d\udea3',
  'rowing_woman':'\ud83d\udea3&zwj;\u2640\ufe0f',
  'rugby_football':'\ud83c\udfc9',
  'running_man':'\ud83c\udfc3',
  'running_shirt_with_sash':'\ud83c\udfbd',
  'running_woman':'\ud83c\udfc3&zwj;\u2640\ufe0f',
  'sa':'\ud83c\ude02\ufe0f',
  'sagittarius':'\u2650\ufe0f',
  'sake':'\ud83c\udf76',
  'sandal':'\ud83d\udc61',
  'santa':'\ud83c\udf85',
  'satellite':'\ud83d\udce1',
  'saxophone':'\ud83c\udfb7',
  'school':'\ud83c\udfeb',
  'school_satchel':'\ud83c\udf92',
  'scissors':'\u2702\ufe0f',
  'scorpion':'\ud83e\udd82',
  'scorpius':'\u264f\ufe0f',
  'scream':'\ud83d\ude31',
  'scream_cat':'\ud83d\ude40',
  'scroll':'\ud83d\udcdc',
  'seat':'\ud83d\udcba',
  'secret':'\u3299\ufe0f',
  'see_no_evil':'\ud83d\ude48',
  'seedling':'\ud83c\udf31',
  'selfie':'\ud83e\udd33',
  'shallow_pan_of_food':'\ud83e\udd58',
  'shamrock':'\u2618\ufe0f',
  'shark':'\ud83e\udd88',
  'shaved_ice':'\ud83c\udf67',
  'sheep':'\ud83d\udc11',
  'shell':'\ud83d\udc1a',
  'shield':'\ud83d\udee1',
  'shinto_shrine':'\u26e9',
  'ship':'\ud83d\udea2',
  'shirt':'\ud83d\udc55',
  'shopping':'\ud83d\udecd',
  'shopping_cart':'\ud83d\uded2',
  'shower':'\ud83d\udebf',
  'shrimp':'\ud83e\udd90',
  'signal_strength':'\ud83d\udcf6',
  'six_pointed_star':'\ud83d\udd2f',
  'ski':'\ud83c\udfbf',
  'skier':'\u26f7',
  'skull':'\ud83d\udc80',
  'skull_and_crossbones':'\u2620\ufe0f',
  'sleeping':'\ud83d\ude34',
  'sleeping_bed':'\ud83d\udecc',
  'sleepy':'\ud83d\ude2a',
  'slightly_frowning_face':'\ud83d\ude41',
  'slightly_smiling_face':'\ud83d\ude42',
  'slot_machine':'\ud83c\udfb0',
  'small_airplane':'\ud83d\udee9',
  'small_blue_diamond':'\ud83d\udd39',
  'small_orange_diamond':'\ud83d\udd38',
  'small_red_triangle':'\ud83d\udd3a',
  'small_red_triangle_down':'\ud83d\udd3b',
  'smile':'\ud83d\ude04',
  'smile_cat':'\ud83d\ude38',
  'smiley':'\ud83d\ude03',
  'smiley_cat':'\ud83d\ude3a',
  'smiling_imp':'\ud83d\ude08',
  'smirk':'\ud83d\ude0f',
  'smirk_cat':'\ud83d\ude3c',
  'smoking':'\ud83d\udeac',
  'snail':'\ud83d\udc0c',
  'snake':'\ud83d\udc0d',
  'sneezing_face':'\ud83e\udd27',
  'snowboarder':'\ud83c\udfc2',
  'snowflake':'\u2744\ufe0f',
  'snowman':'\u26c4\ufe0f',
  'snowman_with_snow':'\u2603\ufe0f',
  'sob':'\ud83d\ude2d',
  'soccer':'\u26bd\ufe0f',
  'soon':'\ud83d\udd1c',
  'sos':'\ud83c\udd98',
  'sound':'\ud83d\udd09',
  'space_invader':'\ud83d\udc7e',
  'spades':'\u2660\ufe0f',
  'spaghetti':'\ud83c\udf5d',
  'sparkle':'\u2747\ufe0f',
  'sparkler':'\ud83c\udf87',
  'sparkles':'\u2728',
  'sparkling_heart':'\ud83d\udc96',
  'speak_no_evil':'\ud83d\ude4a',
  'speaker':'\ud83d\udd08',
  'speaking_head':'\ud83d\udde3',
  'speech_balloon':'\ud83d\udcac',
  'speedboat':'\ud83d\udea4',
  'spider':'\ud83d\udd77',
  'spider_web':'\ud83d\udd78',
  'spiral_calendar':'\ud83d\uddd3',
  'spiral_notepad':'\ud83d\uddd2',
  'spoon':'\ud83e\udd44',
  'squid':'\ud83e\udd91',
  'stadium':'\ud83c\udfdf',
  'star':'\u2b50\ufe0f',
  'star2':'\ud83c\udf1f',
  'star_and_crescent':'\u262a\ufe0f',
  'star_of_david':'\u2721\ufe0f',
  'stars':'\ud83c\udf20',
  'station':'\ud83d\ude89',
  'statue_of_liberty':'\ud83d\uddfd',
  'steam_locomotive':'\ud83d\ude82',
  'stew':'\ud83c\udf72',
  'stop_button':'\u23f9',
  'stop_sign':'\ud83d\uded1',
  'stopwatch':'\u23f1',
  'straight_ruler':'\ud83d\udccf',
  'strawberry':'\ud83c\udf53',
  'stuck_out_tongue':'\ud83d\ude1b',
  'stuck_out_tongue_closed_eyes':'\ud83d\ude1d',
  'stuck_out_tongue_winking_eye':'\ud83d\ude1c',
  'studio_microphone':'\ud83c\udf99',
  'stuffed_flatbread':'\ud83e\udd59',
  'sun_behind_large_cloud':'\ud83c\udf25',
  'sun_behind_rain_cloud':'\ud83c\udf26',
  'sun_behind_small_cloud':'\ud83c\udf24',
  'sun_with_face':'\ud83c\udf1e',
  'sunflower':'\ud83c\udf3b',
  'sunglasses':'\ud83d\ude0e',
  'sunny':'\u2600\ufe0f',
  'sunrise':'\ud83c\udf05',
  'sunrise_over_mountains':'\ud83c\udf04',
  'surfing_man':'\ud83c\udfc4',
  'surfing_woman':'\ud83c\udfc4&zwj;\u2640\ufe0f',
  'sushi':'\ud83c\udf63',
  'suspension_railway':'\ud83d\ude9f',
  'sweat':'\ud83d\ude13',
  'sweat_drops':'\ud83d\udca6',
  'sweat_smile':'\ud83d\ude05',
  'sweet_potato':'\ud83c\udf60',
  'swimming_man':'\ud83c\udfca',
  'swimming_woman':'\ud83c\udfca&zwj;\u2640\ufe0f',
  'symbols':'\ud83d\udd23',
  'synagogue':'\ud83d\udd4d',
  'syringe':'\ud83d\udc89',
  'taco':'\ud83c\udf2e',
  'tada':'\ud83c\udf89',
  'tanabata_tree':'\ud83c\udf8b',
  'taurus':'\u2649\ufe0f',
  'taxi':'\ud83d\ude95',
  'tea':'\ud83c\udf75',
  'telephone_receiver':'\ud83d\udcde',
  'telescope':'\ud83d\udd2d',
  'tennis':'\ud83c\udfbe',
  'tent':'\u26fa\ufe0f',
  'thermometer':'\ud83c\udf21',
  'thinking':'\ud83e\udd14',
  'thought_balloon':'\ud83d\udcad',
  'ticket':'\ud83c\udfab',
  'tickets':'\ud83c\udf9f',
  'tiger':'\ud83d\udc2f',
  'tiger2':'\ud83d\udc05',
  'timer_clock':'\u23f2',
  'tipping_hand_man':'\ud83d\udc81&zwj;\u2642\ufe0f',
  'tired_face':'\ud83d\ude2b',
  'tm':'\u2122\ufe0f',
  'toilet':'\ud83d\udebd',
  'tokyo_tower':'\ud83d\uddfc',
  'tomato':'\ud83c\udf45',
  'tongue':'\ud83d\udc45',
  'top':'\ud83d\udd1d',
  'tophat':'\ud83c\udfa9',
  'tornado':'\ud83c\udf2a',
  'trackball':'\ud83d\uddb2',
  'tractor':'\ud83d\ude9c',
  'traffic_light':'\ud83d\udea5',
  'train':'\ud83d\ude8b',
  'train2':'\ud83d\ude86',
  'tram':'\ud83d\ude8a',
  'triangular_flag_on_post':'\ud83d\udea9',
  'triangular_ruler':'\ud83d\udcd0',
  'trident':'\ud83d\udd31',
  'triumph':'\ud83d\ude24',
  'trolleybus':'\ud83d\ude8e',
  'trophy':'\ud83c\udfc6',
  'tropical_drink':'\ud83c\udf79',
  'tropical_fish':'\ud83d\udc20',
  'truck':'\ud83d\ude9a',
  'trumpet':'\ud83c\udfba',
  'tulip':'\ud83c\udf37',
  'tumbler_glass':'\ud83e\udd43',
  'turkey':'\ud83e\udd83',
  'turtle':'\ud83d\udc22',
  'tv':'\ud83d\udcfa',
  'twisted_rightwards_arrows':'\ud83d\udd00',
  'two_hearts':'\ud83d\udc95',
  'two_men_holding_hands':'\ud83d\udc6c',
  'two_women_holding_hands':'\ud83d\udc6d',
  'u5272':'\ud83c\ude39',
  'u5408':'\ud83c\ude34',
  'u55b6':'\ud83c\ude3a',
  'u6307':'\ud83c\ude2f\ufe0f',
  'u6708':'\ud83c\ude37\ufe0f',
  'u6709':'\ud83c\ude36',
  'u6e80':'\ud83c\ude35',
  'u7121':'\ud83c\ude1a\ufe0f',
  'u7533':'\ud83c\ude38',
  'u7981':'\ud83c\ude32',
  'u7a7a':'\ud83c\ude33',
  'umbrella':'\u2614\ufe0f',
  'unamused':'\ud83d\ude12',
  'underage':'\ud83d\udd1e',
  'unicorn':'\ud83e\udd84',
  'unlock':'\ud83d\udd13',
  'up':'\ud83c\udd99',
  'upside_down_face':'\ud83d\ude43',
  'v':'\u270c\ufe0f',
  'vertical_traffic_light':'\ud83d\udea6',
  'vhs':'\ud83d\udcfc',
  'vibration_mode':'\ud83d\udcf3',
  'video_camera':'\ud83d\udcf9',
  'video_game':'\ud83c\udfae',
  'violin':'\ud83c\udfbb',
  'virgo':'\u264d\ufe0f',
  'volcano':'\ud83c\udf0b',
  'volleyball':'\ud83c\udfd0',
  'vs':'\ud83c\udd9a',
  'vulcan_salute':'\ud83d\udd96',
  'walking_man':'\ud83d\udeb6',
  'walking_woman':'\ud83d\udeb6&zwj;\u2640\ufe0f',
  'waning_crescent_moon':'\ud83c\udf18',
  'waning_gibbous_moon':'\ud83c\udf16',
  'warning':'\u26a0\ufe0f',
  'wastebasket':'\ud83d\uddd1',
  'watch':'\u231a\ufe0f',
  'water_buffalo':'\ud83d\udc03',
  'watermelon':'\ud83c\udf49',
  'wave':'\ud83d\udc4b',
  'wavy_dash':'\u3030\ufe0f',
  'waxing_crescent_moon':'\ud83c\udf12',
  'wc':'\ud83d\udebe',
  'weary':'\ud83d\ude29',
  'wedding':'\ud83d\udc92',
  'weight_lifting_man':'\ud83c\udfcb\ufe0f',
  'weight_lifting_woman':'\ud83c\udfcb\ufe0f&zwj;\u2640\ufe0f',
  'whale':'\ud83d\udc33',
  'whale2':'\ud83d\udc0b',
  'wheel_of_dharma':'\u2638\ufe0f',
  'wheelchair':'\u267f\ufe0f',
  'white_check_mark':'\u2705',
  'white_circle':'\u26aa\ufe0f',
  'white_flag':'\ud83c\udff3\ufe0f',
  'white_flower':'\ud83d\udcae',
  'white_large_square':'\u2b1c\ufe0f',
  'white_medium_small_square':'\u25fd\ufe0f',
  'white_medium_square':'\u25fb\ufe0f',
  'white_small_square':'\u25ab\ufe0f',
  'white_square_button':'\ud83d\udd33',
  'wilted_flower':'\ud83e\udd40',
  'wind_chime':'\ud83c\udf90',
  'wind_face':'\ud83c\udf2c',
  'wine_glass':'\ud83c\udf77',
  'wink':'\ud83d\ude09',
  'wolf':'\ud83d\udc3a',
  'woman':'\ud83d\udc69',
  'woman_artist':'\ud83d\udc69&zwj;\ud83c\udfa8',
  'woman_astronaut':'\ud83d\udc69&zwj;\ud83d\ude80',
  'woman_cartwheeling':'\ud83e\udd38&zwj;\u2640\ufe0f',
  'woman_cook':'\ud83d\udc69&zwj;\ud83c\udf73',
  'woman_facepalming':'\ud83e\udd26&zwj;\u2640\ufe0f',
  'woman_factory_worker':'\ud83d\udc69&zwj;\ud83c\udfed',
  'woman_farmer':'\ud83d\udc69&zwj;\ud83c\udf3e',
  'woman_firefighter':'\ud83d\udc69&zwj;\ud83d\ude92',
  'woman_health_worker':'\ud83d\udc69&zwj;\u2695\ufe0f',
  'woman_judge':'\ud83d\udc69&zwj;\u2696\ufe0f',
  'woman_juggling':'\ud83e\udd39&zwj;\u2640\ufe0f',
  'woman_mechanic':'\ud83d\udc69&zwj;\ud83d\udd27',
  'woman_office_worker':'\ud83d\udc69&zwj;\ud83d\udcbc',
  'woman_pilot':'\ud83d\udc69&zwj;\u2708\ufe0f',
  'woman_playing_handball':'\ud83e\udd3e&zwj;\u2640\ufe0f',
  'woman_playing_water_polo':'\ud83e\udd3d&zwj;\u2640\ufe0f',
  'woman_scientist':'\ud83d\udc69&zwj;\ud83d\udd2c',
  'woman_shrugging':'\ud83e\udd37&zwj;\u2640\ufe0f',
  'woman_singer':'\ud83d\udc69&zwj;\ud83c\udfa4',
  'woman_student':'\ud83d\udc69&zwj;\ud83c\udf93',
  'woman_teacher':'\ud83d\udc69&zwj;\ud83c\udfeb',
  'woman_technologist':'\ud83d\udc69&zwj;\ud83d\udcbb',
  'woman_with_turban':'\ud83d\udc73&zwj;\u2640\ufe0f',
  'womans_clothes':'\ud83d\udc5a',
  'womans_hat':'\ud83d\udc52',
  'women_wrestling':'\ud83e\udd3c&zwj;\u2640\ufe0f',
  'womens':'\ud83d\udeba',
  'world_map':'\ud83d\uddfa',
  'worried':'\ud83d\ude1f',
  'wrench':'\ud83d\udd27',
  'writing_hand':'\u270d\ufe0f',
  'x':'\u274c',
  'yellow_heart':'\ud83d\udc9b',
  'yen':'\ud83d\udcb4',
  'yin_yang':'\u262f\ufe0f',
  'yum':'\ud83d\ude0b',
  'zap':'\u26a1\ufe0f',
  'zipper_mouth_face':'\ud83e\udd10',
  'zzz':'\ud83d\udca4',

  /* special emojis :P */
  'octocat':  '<img alt=":octocat:" height="20" width="20" align="absmiddle" src="https://assets-cdn.github.com/images/icons/emoji/octocat.png">',
  'showdown': '<span style="font-family: \'Anonymous Pro\', monospace; text-decoration: underline; text-decoration-style: dashed; text-decoration-color: #3e8b8a;text-underline-position: under;">S</span>'
};

/**
 * Created by Estevao on 31-05-2015.
 */

/**
 * Showdown Converter class
 * @class
 * @param {object} [converterOptions]
 * @returns {Converter}
 */
showdown.Converter = function (converterOptions) {
  'use strict';

  var
      /**
       * Options used by this converter
       * @private
       * @type {{}}
       */
      options = {},

      /**
       * Language extensions used by this converter
       * @private
       * @type {Array}
       */
      langExtensions = [],

      /**
       * Output modifiers extensions used by this converter
       * @private
       * @type {Array}
       */
      outputModifiers = [],

      /**
       * Event listeners
       * @private
       * @type {{}}
       */
      listeners = {},

      /**
       * The flavor set in this converter
       */
      setConvFlavor = setFlavor,

      /**
       * Metadata of the document
       * @type {{parsed: {}, raw: string, format: string}}
       */
      metadata = {
        parsed: {},
        raw: '',
        format: ''
      };

  _constructor();

  /**
   * Converter constructor
   * @private
   */
  function _constructor () {
    converterOptions = converterOptions || {};

    for (var gOpt in globalOptions) {
      if (globalOptions.hasOwnProperty(gOpt)) {
        options[gOpt] = globalOptions[gOpt];
      }
    }

    // Merge options
    if (typeof converterOptions === 'object') {
      for (var opt in converterOptions) {
        if (converterOptions.hasOwnProperty(opt)) {
          options[opt] = converterOptions[opt];
        }
      }
    } else {
      throw Error('Converter expects the passed parameter to be an object, but ' + typeof converterOptions +
      ' was passed instead.');
    }

    if (options.extensions) {
      showdown.helper.forEach(options.extensions, _parseExtension);
    }
  }

  /**
   * Parse extension
   * @param {*} ext
   * @param {string} [name='']
   * @private
   */
  function _parseExtension (ext, name) {

    name = name || null;
    // If it's a string, the extension was previously loaded
    if (showdown.helper.isString(ext)) {
      ext = showdown.helper.stdExtName(ext);
      name = ext;

      // LEGACY_SUPPORT CODE
      if (showdown.extensions[ext]) {
        console.warn('DEPRECATION WARNING: ' + ext + ' is an old extension that uses a deprecated loading method.' +
          'Please inform the developer that the extension should be updated!');
        legacyExtensionLoading(showdown.extensions[ext], ext);
        return;
        // END LEGACY SUPPORT CODE

      } else if (!showdown.helper.isUndefined(extensions[ext])) {
        ext = extensions[ext];

      } else {
        throw Error('Extension "' + ext + '" could not be loaded. It was either not found or is not a valid extension.');
      }
    }

    if (typeof ext === 'function') {
      ext = ext();
    }

    if (!showdown.helper.isArray(ext)) {
      ext = [ext];
    }

    var validExt = validate(ext, name);
    if (!validExt.valid) {
      throw Error(validExt.error);
    }

    for (var i = 0; i < ext.length; ++i) {
      switch (ext[i].type) {

        case 'lang':
          langExtensions.push(ext[i]);
          break;

        case 'output':
          outputModifiers.push(ext[i]);
          break;
      }
      if (ext[i].hasOwnProperty('listeners')) {
        for (var ln in ext[i].listeners) {
          if (ext[i].listeners.hasOwnProperty(ln)) {
            listen(ln, ext[i].listeners[ln]);
          }
        }
      }
    }

  }

  /**
   * LEGACY_SUPPORT
   * @param {*} ext
   * @param {string} name
   */
  function legacyExtensionLoading (ext, name) {
    if (typeof ext === 'function') {
      ext = ext(new showdown.Converter());
    }
    if (!showdown.helper.isArray(ext)) {
      ext = [ext];
    }
    var valid = validate(ext, name);

    if (!valid.valid) {
      throw Error(valid.error);
    }

    for (var i = 0; i < ext.length; ++i) {
      switch (ext[i].type) {
        case 'lang':
          langExtensions.push(ext[i]);
          break;
        case 'output':
          outputModifiers.push(ext[i]);
          break;
        default:// should never reach here
          throw Error('Extension loader error: Type unrecognized!!!');
      }
    }
  }

  /**
   * Listen to an event
   * @param {string} name
   * @param {function} callback
   */
  function listen (name, callback) {
    if (!showdown.helper.isString(name)) {
      throw Error('Invalid argument in converter.listen() method: name must be a string, but ' + typeof name + ' given');
    }

    if (typeof callback !== 'function') {
      throw Error('Invalid argument in converter.listen() method: callback must be a function, but ' + typeof callback + ' given');
    }

    if (!listeners.hasOwnProperty(name)) {
      listeners[name] = [];
    }
    listeners[name].push(callback);
  }

  function rTrimInputText (text) {
    var rsp = text.match(/^\s*/)[0].length,
        rgx = new RegExp('^\\s{0,' + rsp + '}', 'gm');
    return text.replace(rgx, '');
  }

  /**
   * Dispatch an event
   * @private
   * @param {string} evtName Event name
   * @param {string} text Text
   * @param {{}} options Converter Options
   * @param {{}} globals
   * @returns {string}
   */
  this._dispatch = function dispatch (evtName, text, options, globals) {
    if (listeners.hasOwnProperty(evtName)) {
      for (var ei = 0; ei < listeners[evtName].length; ++ei) {
        var nText = listeners[evtName][ei](evtName, text, this, options, globals);
        if (nText && typeof nText !== 'undefined') {
          text = nText;
        }
      }
    }
    return text;
  };

  /**
   * Listen to an event
   * @param {string} name
   * @param {function} callback
   * @returns {showdown.Converter}
   */
  this.listen = function (name, callback) {
    listen(name, callback);
    return this;
  };

  /**
   * Converts a markdown string into HTML
   * @param {string} text
   * @returns {*}
   */
  this.makeHtml = function (text) {
    //check if text is not falsy
    if (!text) {
      return text;
    }

    var globals = {
      gHtmlBlocks:     [],
      gHtmlMdBlocks:   [],
      gHtmlSpans:      [],
      gUrls:           {},
      gTitles:         {},
      gDimensions:     {},
      gListLevel:      0,
      hashLinkCounts:  {},
      langExtensions:  langExtensions,
      outputModifiers: outputModifiers,
      converter:       this,
      ghCodeBlocks:    [],
      metadata: {
        parsed: {},
        raw: '',
        format: ''
      }
    };

    // This lets us use ¨ trema as an escape char to avoid md5 hashes
    // The choice of character is arbitrary; anything that isn't
    // magic in Markdown will work.
    text = text.replace(/¨/g, '¨T');

    // Replace $ with ¨D
    // RegExp interprets $ as a special character
    // when it's in a replacement string
    text = text.replace(/\$/g, '¨D');

    // Standardize line endings
    text = text.replace(/\r\n/g, '\n'); // DOS to Unix
    text = text.replace(/\r/g, '\n'); // Mac to Unix

    // Stardardize line spaces
    text = text.replace(/\u00A0/g, '&nbsp;');

    if (options.smartIndentationFix) {
      text = rTrimInputText(text);
    }

    // Make sure text begins and ends with a couple of newlines:
    text = '\n\n' + text + '\n\n';

    // detab
    text = showdown.subParser('detab')(text, options, globals);

    /**
     * Strip any lines consisting only of spaces and tabs.
     * This makes subsequent regexs easier to write, because we can
     * match consecutive blank lines with /\n+/ instead of something
     * contorted like /[ \t]*\n+/
     */
    text = text.replace(/^[ \t]+$/mg, '');

    //run languageExtensions
    showdown.helper.forEach(langExtensions, function (ext) {
      text = showdown.subParser('runExtension')(ext, text, options, globals);
    });

    // run the sub parsers
    text = showdown.subParser('metadata')(text, options, globals);
    text = showdown.subParser('hashPreCodeTags')(text, options, globals);
    text = showdown.subParser('githubCodeBlocks')(text, options, globals);
    text = showdown.subParser('hashHTMLBlocks')(text, options, globals);
    text = showdown.subParser('hashCodeTags')(text, options, globals);
    text = showdown.subParser('stripLinkDefinitions')(text, options, globals);
    text = showdown.subParser('blockGamut')(text, options, globals);
    text = showdown.subParser('unhashHTMLSpans')(text, options, globals);
    text = showdown.subParser('unescapeSpecialChars')(text, options, globals);

    // attacklab: Restore dollar signs
    text = text.replace(/¨D/g, '$$');

    // attacklab: Restore tremas
    text = text.replace(/¨T/g, '¨');

    // render a complete html document instead of a partial if the option is enabled
    text = showdown.subParser('completeHTMLDocument')(text, options, globals);

    // Run output modifiers
    showdown.helper.forEach(outputModifiers, function (ext) {
      text = showdown.subParser('runExtension')(ext, text, options, globals);
    });

    // update metadata
    metadata = globals.metadata;
    return text;
  };

  /**
   * Converts an HTML string into a markdown string
   * @param src
   * @param [HTMLParser] A WHATWG DOM and HTML parser, such as JSDOM. If none is supplied, window.document will be used.
   * @returns {string}
   */
  this.makeMarkdown = this.makeMd = function (src, HTMLParser) {

    // replace \r\n with \n
    src = src.replace(/\r\n/g, '\n');
    src = src.replace(/\r/g, '\n'); // old macs

    // due to an edge case, we need to find this: > <
    // to prevent removing of non silent white spaces
    // ex: <em>this is</em> <strong>sparta</strong>
    src = src.replace(/>[ \t]+</, '>¨NBSP;<');

    if (!HTMLParser) {
      if (window && window.document) {
        HTMLParser = window.document;
      } else {
        throw new Error('HTMLParser is undefined. If in a webworker or nodejs environment, you need to provide a WHATWG DOM and HTML such as JSDOM');
      }
    }

    var doc = HTMLParser.createElement('div');
    doc.innerHTML = src;

    var globals = {
      preList: substitutePreCodeTags(doc)
    };

    // remove all newlines and collapse spaces
    clean(doc);

    // some stuff, like accidental reference links must now be escaped
    // TODO
    // doc.innerHTML = doc.innerHTML.replace(/\[[\S\t ]]/);

    var nodes = doc.childNodes,
        mdDoc = '';

    for (var i = 0; i < nodes.length; i++) {
      mdDoc += showdown.subParser('makeMarkdown.node')(nodes[i], globals);
    }

    function clean (node) {
      for (var n = 0; n < node.childNodes.length; ++n) {
        var child = node.childNodes[n];
        if (child.nodeType === 3) {
          if (!/\S/.test(child.nodeValue)) {
            node.removeChild(child);
            --n;
          } else {
            child.nodeValue = child.nodeValue.split('\n').join(' ');
            child.nodeValue = child.nodeValue.replace(/(\s)+/g, '$1');
          }
        } else if (child.nodeType === 1) {
          clean(child);
        }
      }
    }

    // find all pre tags and replace contents with placeholder
    // we need this so that we can remove all indentation from html
    // to ease up parsing
    function substitutePreCodeTags (doc) {

      var pres = doc.querySelectorAll('pre'),
          presPH = [];

      for (var i = 0; i < pres.length; ++i) {

        if (pres[i].childElementCount === 1 && pres[i].firstChild.tagName.toLowerCase() === 'code') {
          var content = pres[i].firstChild.innerHTML.trim(),
              language = pres[i].firstChild.getAttribute('data-language') || '';

          // if data-language attribute is not defined, then we look for class language-*
          if (language === '') {
            var classes = pres[i].firstChild.className.split(' ');
            for (var c = 0; c < classes.length; ++c) {
              var matches = classes[c].match(/^language-(.+)$/);
              if (matches !== null) {
                language = matches[1];
                break;
              }
            }
          }

          // unescape html entities in content
          content = showdown.helper.unescapeHTMLEntities(content);

          presPH.push(content);
          pres[i].outerHTML = '<precode language="' + language + '" precodenum="' + i.toString() + '"></precode>';
        } else {
          presPH.push(pres[i].innerHTML);
          pres[i].innerHTML = '';
          pres[i].setAttribute('prenum', i.toString());
        }
      }
      return presPH;
    }

    return mdDoc;
  };

  /**
   * Set an option of this Converter instance
   * @param {string} key
   * @param {*} value
   */
  this.setOption = function (key, value) {
    options[key] = value;
  };

  /**
   * Get the option of this Converter instance
   * @param {string} key
   * @returns {*}
   */
  this.getOption = function (key) {
    return options[key];
  };

  /**
   * Get the options of this Converter instance
   * @returns {{}}
   */
  this.getOptions = function () {
    return options;
  };

  /**
   * Add extension to THIS converter
   * @param {{}} extension
   * @param {string} [name=null]
   */
  this.addExtension = function (extension, name) {
    name = name || null;
    _parseExtension(extension, name);
  };

  /**
   * Use a global registered extension with THIS converter
   * @param {string} extensionName Name of the previously registered extension
   */
  this.useExtension = function (extensionName) {
    _parseExtension(extensionName);
  };

  /**
   * Set the flavor THIS converter should use
   * @param {string} name
   */
  this.setFlavor = function (name) {
    if (!flavor.hasOwnProperty(name)) {
      throw Error(name + ' flavor was not found');
    }
    var preset = flavor[name];
    setConvFlavor = name;
    for (var option in preset) {
      if (preset.hasOwnProperty(option)) {
        options[option] = preset[option];
      }
    }
  };

  /**
   * Get the currently set flavor of this converter
   * @returns {string}
   */
  this.getFlavor = function () {
    return setConvFlavor;
  };

  /**
   * Remove an extension from THIS converter.
   * Note: This is a costly operation. It's better to initialize a new converter
   * and specify the extensions you wish to use
   * @param {Array} extension
   */
  this.removeExtension = function (extension) {
    if (!showdown.helper.isArray(extension)) {
      extension = [extension];
    }
    for (var a = 0; a < extension.length; ++a) {
      var ext = extension[a];
      for (var i = 0; i < langExtensions.length; ++i) {
        if (langExtensions[i] === ext) {
          langExtensions[i].splice(i, 1);
        }
      }
      for (var ii = 0; ii < outputModifiers.length; ++i) {
        if (outputModifiers[ii] === ext) {
          outputModifiers[ii].splice(i, 1);
        }
      }
    }
  };

  /**
   * Get all extension of THIS converter
   * @returns {{language: Array, output: Array}}
   */
  this.getAllExtensions = function () {
    return {
      language: langExtensions,
      output: outputModifiers
    };
  };

  /**
   * Get the metadata of the previously parsed document
   * @param raw
   * @returns {string|{}}
   */
  this.getMetadata = function (raw) {
    if (raw) {
      return metadata.raw;
    } else {
      return metadata.parsed;
    }
  };

  /**
   * Get the metadata format of the previously parsed document
   * @returns {string}
   */
  this.getMetadataFormat = function () {
    return metadata.format;
  };

  /**
   * Private: set a single key, value metadata pair
   * @param {string} key
   * @param {string} value
   */
  this._setMetadataPair = function (key, value) {
    metadata.parsed[key] = value;
  };

  /**
   * Private: set metadata format
   * @param {string} format
   */
  this._setMetadataFormat = function (format) {
    metadata.format = format;
  };

  /**
   * Private: set metadata raw text
   * @param {string} raw
   */
  this._setMetadataRaw = function (raw) {
    metadata.raw = raw;
  };
};

/**
 * Turn Markdown link shortcuts into XHTML <a> tags.
 */
showdown.subParser('anchors', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('anchors.before', text, options, globals);

  var writeAnchorTag = function (wholeMatch, linkText, linkId, url, m5, m6, title) {
    if (showdown.helper.isUndefined(title)) {
      title = '';
    }
    linkId = linkId.toLowerCase();

    // Special case for explicit empty url
    if (wholeMatch.search(/\(<?\s*>? ?(['"].*['"])?\)$/m) > -1) {
      url = '';
    } else if (!url) {
      if (!linkId) {
        // lower-case and turn embedded newlines into spaces
        linkId = linkText.toLowerCase().replace(/ ?\n/g, ' ');
      }
      url = '#' + linkId;

      if (!showdown.helper.isUndefined(globals.gUrls[linkId])) {
        url = globals.gUrls[linkId];
        if (!showdown.helper.isUndefined(globals.gTitles[linkId])) {
          title = globals.gTitles[linkId];
        }
      } else {
        return wholeMatch;
      }
    }

    //url = showdown.helper.escapeCharacters(url, '*_', false); // replaced line to improve performance
    url = url.replace(showdown.helper.regexes.asteriskDashAndColon, showdown.helper.escapeCharactersCallback);

    var result = '<a href="' + url + '"';

    if (title !== '' && title !== null) {
      title = title.replace(/"/g, '&quot;');
      //title = showdown.helper.escapeCharacters(title, '*_', false); // replaced line to improve performance
      title = title.replace(showdown.helper.regexes.asteriskDashAndColon, showdown.helper.escapeCharactersCallback);
      result += ' title="' + title + '"';
    }

    // optionLinksInNewWindow only applies
    // to external links. Hash links (#) open in same page
    if (options.openLinksInNewWindow && !/^#/.test(url)) {
      // escaped _
      result += ' rel="noopener noreferrer" target="¨E95Eblank"';
    }

    result += '>' + linkText + '</a>';

    return result;
  };

  // First, handle reference-style links: [link text] [id]
  text = text.replace(/\[((?:\[[^\]]*]|[^\[\]])*)] ?(?:\n *)?\[(.*?)]()()()()/g, writeAnchorTag);

  // Next, inline-style links: [link text](url "optional title")
  // cases with crazy urls like ./image/cat1).png
  text = text.replace(/\[((?:\[[^\]]*]|[^\[\]])*)]()[ \t]*\([ \t]?<([^>]*)>(?:[ \t]*((["'])([^"]*?)\5))?[ \t]?\)/g,
    writeAnchorTag);

  // normal cases
  text = text.replace(/\[((?:\[[^\]]*]|[^\[\]])*)]()[ \t]*\([ \t]?<?([\S]+?(?:\([\S]*?\)[\S]*?)?)>?(?:[ \t]*((["'])([^"]*?)\5))?[ \t]?\)/g,
    writeAnchorTag);

  // handle reference-style shortcuts: [link text]
  // These must come last in case you've also got [link test][1]
  // or [link test](/foo)
  text = text.replace(/\[([^\[\]]+)]()()()()()/g, writeAnchorTag);

  // Lastly handle GithubMentions if option is enabled
  if (options.ghMentions) {
    text = text.replace(/(^|\s)(\\)?(@([a-z\d]+(?:[a-z\d.-]+?[a-z\d]+)*))/gmi, function (wm, st, escape, mentions, username) {
      if (escape === '\\') {
        return st + mentions;
      }

      //check if options.ghMentionsLink is a string
      if (!showdown.helper.isString(options.ghMentionsLink)) {
        throw new Error('ghMentionsLink option must be a string');
      }
      var lnk = options.ghMentionsLink.replace(/\{u}/g, username),
          target = '';
      if (options.openLinksInNewWindow) {
        target = ' rel="noopener noreferrer" target="¨E95Eblank"';
      }
      return st + '<a href="' + lnk + '"' + target + '>' + mentions + '</a>';
    });
  }

  text = globals.converter._dispatch('anchors.after', text, options, globals);
  return text;
});

// url allowed chars [a-z\d_.~:/?#[]@!$&'()*+,;=-]

var simpleURLRegex  = /([*~_]+|\b)(((https?|ftp|dict):\/\/|www\.)[^'">\s]+?\.[^'">\s]+?)()(\1)?(?=\s|$)(?!["<>])/gi,
    simpleURLRegex2 = /([*~_]+|\b)(((https?|ftp|dict):\/\/|www\.)[^'">\s]+\.[^'">\s]+?)([.!?,()\[\]])?(\1)?(?=\s|$)(?!["<>])/gi,
    delimUrlRegex   = /()<(((https?|ftp|dict):\/\/|www\.)[^'">\s]+)()>()/gi,
    simpleMailRegex = /(^|\s)(?:mailto:)?([A-Za-z0-9!#$%&'*+-/=?^_`{|}~.]+@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]+)(?=$|\s)/gmi,
    delimMailRegex  = /<()(?:mailto:)?([-.\w]+@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]+)>/gi,

    replaceLink = function (options) {
      'use strict';
      return function (wm, leadingMagicChars, link, m2, m3, trailingPunctuation, trailingMagicChars) {
        link = link.replace(showdown.helper.regexes.asteriskDashAndColon, showdown.helper.escapeCharactersCallback);
        var lnkTxt = link,
            append = '',
            target = '',
            lmc    = leadingMagicChars || '',
            tmc    = trailingMagicChars || '';
        if (/^www\./i.test(link)) {
          link = link.replace(/^www\./i, 'http://www.');
        }
        if (options.excludeTrailingPunctuationFromURLs && trailingPunctuation) {
          append = trailingPunctuation;
        }
        if (options.openLinksInNewWindow) {
          target = ' rel="noopener noreferrer" target="¨E95Eblank"';
        }
        return lmc + '<a href="' + link + '"' + target + '>' + lnkTxt + '</a>' + append + tmc;
      };
    },

    replaceMail = function (options, globals) {
      'use strict';
      return function (wholeMatch, b, mail) {
        var href = 'mailto:';
        b = b || '';
        mail = showdown.subParser('unescapeSpecialChars')(mail, options, globals);
        if (options.encodeEmails) {
          href = showdown.helper.encodeEmailAddress(href + mail);
          mail = showdown.helper.encodeEmailAddress(mail);
        } else {
          href = href + mail;
        }
        return b + '<a href="' + href + '">' + mail + '</a>';
      };
    };

showdown.subParser('autoLinks', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('autoLinks.before', text, options, globals);

  text = text.replace(delimUrlRegex, replaceLink(options));
  text = text.replace(delimMailRegex, replaceMail(options, globals));

  text = globals.converter._dispatch('autoLinks.after', text, options, globals);

  return text;
});

showdown.subParser('simplifiedAutoLinks', function (text, options, globals) {
  'use strict';

  if (!options.simplifiedAutoLink) {
    return text;
  }

  text = globals.converter._dispatch('simplifiedAutoLinks.before', text, options, globals);

  if (options.excludeTrailingPunctuationFromURLs) {
    text = text.replace(simpleURLRegex2, replaceLink(options));
  } else {
    text = text.replace(simpleURLRegex, replaceLink(options));
  }
  text = text.replace(simpleMailRegex, replaceMail(options, globals));

  text = globals.converter._dispatch('simplifiedAutoLinks.after', text, options, globals);

  return text;
});

/**
 * These are all the transformations that form block-level
 * tags like paragraphs, headers, and list items.
 */
showdown.subParser('blockGamut', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('blockGamut.before', text, options, globals);

  // we parse blockquotes first so that we can have headings and hrs
  // inside blockquotes
  text = showdown.subParser('blockQuotes')(text, options, globals);
  text = showdown.subParser('headers')(text, options, globals);

  // Do Horizontal Rules:
  text = showdown.subParser('horizontalRule')(text, options, globals);

  text = showdown.subParser('lists')(text, options, globals);
  text = showdown.subParser('codeBlocks')(text, options, globals);
  text = showdown.subParser('tables')(text, options, globals);

  // We already ran _HashHTMLBlocks() before, in Markdown(), but that
  // was to escape raw HTML in the original Markdown source. This time,
  // we're escaping the markup we've just created, so that we don't wrap
  // <p> tags around block-level tags.
  text = showdown.subParser('hashHTMLBlocks')(text, options, globals);
  text = showdown.subParser('paragraphs')(text, options, globals);

  text = globals.converter._dispatch('blockGamut.after', text, options, globals);

  return text;
});

showdown.subParser('blockQuotes', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('blockQuotes.before', text, options, globals);

  // add a couple extra lines after the text and endtext mark
  text = text + '\n\n';

  var rgx = /(^ {0,3}>[ \t]?.+\n(.+\n)*\n*)+/gm;

  if (options.splitAdjacentBlockquotes) {
    rgx = /^ {0,3}>[\s\S]*?(?:\n\n)/gm;
  }

  text = text.replace(rgx, function (bq) {
    // attacklab: hack around Konqueror 3.5.4 bug:
    // "----------bug".replace(/^-/g,"") == "bug"
    bq = bq.replace(/^[ \t]*>[ \t]?/gm, ''); // trim one level of quoting

    // attacklab: clean up hack
    bq = bq.replace(/¨0/g, '');

    bq = bq.replace(/^[ \t]+$/gm, ''); // trim whitespace-only lines
    bq = showdown.subParser('githubCodeBlocks')(bq, options, globals);
    bq = showdown.subParser('blockGamut')(bq, options, globals); // recurse

    bq = bq.replace(/(^|\n)/g, '$1  ');
    // These leading spaces screw with <pre> content, so we need to fix that:
    bq = bq.replace(/(\s*<pre>[^\r]+?<\/pre>)/gm, function (wholeMatch, m1) {
      var pre = m1;
      // attacklab: hack around Konqueror 3.5.4 bug:
      pre = pre.replace(/^  /mg, '¨0');
      pre = pre.replace(/¨0/g, '');
      return pre;
    });

    return showdown.subParser('hashBlock')('<blockquote>\n' + bq + '\n</blockquote>', options, globals);
  });

  text = globals.converter._dispatch('blockQuotes.after', text, options, globals);
  return text;
});

/**
 * Process Markdown `<pre><code>` blocks.
 */
showdown.subParser('codeBlocks', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('codeBlocks.before', text, options, globals);

  // sentinel workarounds for lack of \A and \Z, safari\khtml bug
  text += '¨0';

  var pattern = /(?:\n\n|^)((?:(?:[ ]{4}|\t).*\n+)+)(\n*[ ]{0,3}[^ \t\n]|(?=¨0))/g;
  text = text.replace(pattern, function (wholeMatch, m1, m2) {
    var codeblock = m1,
        nextChar = m2,
        end = '\n';

    codeblock = showdown.subParser('outdent')(codeblock, options, globals);
    codeblock = showdown.subParser('encodeCode')(codeblock, options, globals);
    codeblock = showdown.subParser('detab')(codeblock, options, globals);
    codeblock = codeblock.replace(/^\n+/g, ''); // trim leading newlines
    codeblock = codeblock.replace(/\n+$/g, ''); // trim trailing newlines

    if (options.omitExtraWLInCodeBlocks) {
      end = '';
    }

    codeblock = '<pre><code>' + codeblock + end + '</code></pre>';

    return showdown.subParser('hashBlock')(codeblock, options, globals) + nextChar;
  });

  // strip sentinel
  text = text.replace(/¨0/, '');

  text = globals.converter._dispatch('codeBlocks.after', text, options, globals);
  return text;
});

/**
 *
 *   *  Backtick quotes are used for <code></code> spans.
 *
 *   *  You can use multiple backticks as the delimiters if you want to
 *     include literal backticks in the code span. So, this input:
 *
 *         Just type ``foo `bar` baz`` at the prompt.
 *
 *       Will translate to:
 *
 *         <p>Just type <code>foo `bar` baz</code> at the prompt.</p>
 *
 *    There's no arbitrary limit to the number of backticks you
 *    can use as delimters. If you need three consecutive backticks
 *    in your code, use four for delimiters, etc.
 *
 *  *  You can use spaces to get literal backticks at the edges:
 *
 *         ... type `` `bar` `` ...
 *
 *       Turns to:
 *
 *         ... type <code>`bar`</code> ...
 */
showdown.subParser('codeSpans', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('codeSpans.before', text, options, globals);

  if (typeof text === 'undefined') {
    text = '';
  }
  text = text.replace(/(^|[^\\])(`+)([^\r]*?[^`])\2(?!`)/gm,
    function (wholeMatch, m1, m2, m3) {
      var c = m3;
      c = c.replace(/^([ \t]*)/g, '');	// leading whitespace
      c = c.replace(/[ \t]*$/g, '');	// trailing whitespace
      c = showdown.subParser('encodeCode')(c, options, globals);
      c = m1 + '<code>' + c + '</code>';
      c = showdown.subParser('hashHTMLSpans')(c, options, globals);
      return c;
    }
  );

  text = globals.converter._dispatch('codeSpans.after', text, options, globals);
  return text;
});

/**
 * Create a full HTML document from the processed markdown
 */
showdown.subParser('completeHTMLDocument', function (text, options, globals) {
  'use strict';

  if (!options.completeHTMLDocument) {
    return text;
  }

  text = globals.converter._dispatch('completeHTMLDocument.before', text, options, globals);

  var doctype = 'html',
      doctypeParsed = '<!DOCTYPE HTML>\n',
      title = '',
      charset = '<meta charset="utf-8">\n',
      lang = '',
      metadata = '';

  if (typeof globals.metadata.parsed.doctype !== 'undefined') {
    doctypeParsed = '<!DOCTYPE ' +  globals.metadata.parsed.doctype + '>\n';
    doctype = globals.metadata.parsed.doctype.toString().toLowerCase();
    if (doctype === 'html' || doctype === 'html5') {
      charset = '<meta charset="utf-8">';
    }
  }

  for (var meta in globals.metadata.parsed) {
    if (globals.metadata.parsed.hasOwnProperty(meta)) {
      switch (meta.toLowerCase()) {
        case 'doctype':
          break;

        case 'title':
          title = '<title>' +  globals.metadata.parsed.title + '</title>\n';
          break;

        case 'charset':
          if (doctype === 'html' || doctype === 'html5') {
            charset = '<meta charset="' + globals.metadata.parsed.charset + '">\n';
          } else {
            charset = '<meta name="charset" content="' + globals.metadata.parsed.charset + '">\n';
          }
          break;

        case 'language':
        case 'lang':
          lang = ' lang="' + globals.metadata.parsed[meta] + '"';
          metadata += '<meta name="' + meta + '" content="' + globals.metadata.parsed[meta] + '">\n';
          break;

        default:
          metadata += '<meta name="' + meta + '" content="' + globals.metadata.parsed[meta] + '">\n';
      }
    }
  }

  text = doctypeParsed + '<html' + lang + '>\n<head>\n' + title + charset + metadata + '</head>\n<body>\n' + text.trim() + '\n</body>\n</html>';

  text = globals.converter._dispatch('completeHTMLDocument.after', text, options, globals);
  return text;
});

/**
 * Convert all tabs to spaces
 */
showdown.subParser('detab', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('detab.before', text, options, globals);

  // expand first n-1 tabs
  text = text.replace(/\t(?=\t)/g, '    '); // g_tab_width

  // replace the nth with two sentinels
  text = text.replace(/\t/g, '¨A¨B');

  // use the sentinel to anchor our regex so it doesn't explode
  text = text.replace(/¨B(.+?)¨A/g, function (wholeMatch, m1) {
    var leadingText = m1,
        numSpaces = 4 - leadingText.length % 4;  // g_tab_width

    // there *must* be a better way to do this:
    for (var i = 0; i < numSpaces; i++) {
      leadingText += ' ';
    }

    return leadingText;
  });

  // clean up sentinels
  text = text.replace(/¨A/g, '    ');  // g_tab_width
  text = text.replace(/¨B/g, '');

  text = globals.converter._dispatch('detab.after', text, options, globals);
  return text;
});

showdown.subParser('ellipsis', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('ellipsis.before', text, options, globals);

  text = text.replace(/\.\.\./g, '…');

  text = globals.converter._dispatch('ellipsis.after', text, options, globals);

  return text;
});

/**
 * Turn emoji codes into emojis
 *
 * List of supported emojis: https://github.com/showdownjs/showdown/wiki/Emojis
 */
showdown.subParser('emoji', function (text, options, globals) {
  'use strict';

  if (!options.emoji) {
    return text;
  }

  text = globals.converter._dispatch('emoji.before', text, options, globals);

  var emojiRgx = /:([\S]+?):/g;

  text = text.replace(emojiRgx, function (wm, emojiCode) {
    if (showdown.helper.emojis.hasOwnProperty(emojiCode)) {
      return showdown.helper.emojis[emojiCode];
    }
    return wm;
  });

  text = globals.converter._dispatch('emoji.after', text, options, globals);

  return text;
});

/**
 * Smart processing for ampersands and angle brackets that need to be encoded.
 */
showdown.subParser('encodeAmpsAndAngles', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('encodeAmpsAndAngles.before', text, options, globals);

  // Ampersand-encoding based entirely on Nat Irons's Amputator MT plugin:
  // http://bumppo.net/projects/amputator/
  text = text.replace(/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/g, '&amp;');

  // Encode naked <'s
  text = text.replace(/<(?![a-z\/?$!])/gi, '&lt;');

  // Encode <
  text = text.replace(/</g, '&lt;');

  // Encode >
  text = text.replace(/>/g, '&gt;');

  text = globals.converter._dispatch('encodeAmpsAndAngles.after', text, options, globals);
  return text;
});

/**
 * Returns the string, with after processing the following backslash escape sequences.
 *
 * attacklab: The polite way to do this is with the new escapeCharacters() function:
 *
 *    text = escapeCharacters(text,"\\",true);
 *    text = escapeCharacters(text,"`*_{}[]()>#+-.!",true);
 *
 * ...but we're sidestepping its use of the (slow) RegExp constructor
 * as an optimization for Firefox.  This function gets called a LOT.
 */
showdown.subParser('encodeBackslashEscapes', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('encodeBackslashEscapes.before', text, options, globals);

  text = text.replace(/\\(\\)/g, showdown.helper.escapeCharactersCallback);
  text = text.replace(/\\([`*_{}\[\]()>#+.!~=|-])/g, showdown.helper.escapeCharactersCallback);

  text = globals.converter._dispatch('encodeBackslashEscapes.after', text, options, globals);
  return text;
});

/**
 * Encode/escape certain characters inside Markdown code runs.
 * The point is that in code, these characters are literals,
 * and lose their special Markdown meanings.
 */
showdown.subParser('encodeCode', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('encodeCode.before', text, options, globals);

  // Encode all ampersands; HTML entities are not
  // entities within a Markdown code span.
  text = text
    .replace(/&/g, '&amp;')
  // Do the angle bracket song and dance:
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
  // Now, escape characters that are magic in Markdown:
    .replace(/([*_{}\[\]\\=~-])/g, showdown.helper.escapeCharactersCallback);

  text = globals.converter._dispatch('encodeCode.after', text, options, globals);
  return text;
});

/**
 * Within tags -- meaning between < and > -- encode [\ ` * _ ~ =] so they
 * don't conflict with their use in Markdown for code, italics and strong.
 */
showdown.subParser('escapeSpecialCharsWithinTagAttributes', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('escapeSpecialCharsWithinTagAttributes.before', text, options, globals);

  // Build a regex to find HTML tags.
  var tags     = /<\/?[a-z\d_:-]+(?:[\s]+[\s\S]+?)?>/gi,
      comments = /<!(--(?:(?:[^>-]|-[^>])(?:[^-]|-[^-])*)--)>/gi;

  text = text.replace(tags, function (wholeMatch) {
    return wholeMatch
      .replace(/(.)<\/?code>(?=.)/g, '$1`')
      .replace(/([\\`*_~=|])/g, showdown.helper.escapeCharactersCallback);
  });

  text = text.replace(comments, function (wholeMatch) {
    return wholeMatch
      .replace(/([\\`*_~=|])/g, showdown.helper.escapeCharactersCallback);
  });

  text = globals.converter._dispatch('escapeSpecialCharsWithinTagAttributes.after', text, options, globals);
  return text;
});

/**
 * Handle github codeblocks prior to running HashHTML so that
 * HTML contained within the codeblock gets escaped properly
 * Example:
 * ```ruby
 *     def hello_world(x)
 *       puts "Hello, #{x}"
 *     end
 * ```
 */
showdown.subParser('githubCodeBlocks', function (text, options, globals) {
  'use strict';

  // early exit if option is not enabled
  if (!options.ghCodeBlocks) {
    return text;
  }

  text = globals.converter._dispatch('githubCodeBlocks.before', text, options, globals);

  text += '¨0';

  text = text.replace(/(?:^|\n)(?: {0,3})(```+|~~~+)(?: *)([^\s`~]*)\n([\s\S]*?)\n(?: {0,3})\1/g, function (wholeMatch, delim, language, codeblock) {
    var end = (options.omitExtraWLInCodeBlocks) ? '' : '\n';

    // First parse the github code block
    codeblock = showdown.subParser('encodeCode')(codeblock, options, globals);
    codeblock = showdown.subParser('detab')(codeblock, options, globals);
    codeblock = codeblock.replace(/^\n+/g, ''); // trim leading newlines
    codeblock = codeblock.replace(/\n+$/g, ''); // trim trailing whitespace

    codeblock = '<pre><code' + (language ? ' class="' + language + ' language-' + language + '"' : '') + '>' + codeblock + end + '</code></pre>';

    codeblock = showdown.subParser('hashBlock')(codeblock, options, globals);

    // Since GHCodeblocks can be false positives, we need to
    // store the primitive text and the parsed text in a global var,
    // and then return a token
    return '\n\n¨G' + (globals.ghCodeBlocks.push({text: wholeMatch, codeblock: codeblock}) - 1) + 'G\n\n';
  });

  // attacklab: strip sentinel
  text = text.replace(/¨0/, '');

  return globals.converter._dispatch('githubCodeBlocks.after', text, options, globals);
});

showdown.subParser('hashBlock', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('hashBlock.before', text, options, globals);
  text = text.replace(/(^\n+|\n+$)/g, '');
  text = '\n\n¨K' + (globals.gHtmlBlocks.push(text) - 1) + 'K\n\n';
  text = globals.converter._dispatch('hashBlock.after', text, options, globals);
  return text;
});

/**
 * Hash and escape <code> elements that should not be parsed as markdown
 */
showdown.subParser('hashCodeTags', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('hashCodeTags.before', text, options, globals);

  var repFunc = function (wholeMatch, match, left, right) {
    var codeblock = left + showdown.subParser('encodeCode')(match, options, globals) + right;
    return '¨C' + (globals.gHtmlSpans.push(codeblock) - 1) + 'C';
  };

  // Hash naked <code>
  text = showdown.helper.replaceRecursiveRegExp(text, repFunc, '<code\\b[^>]*>', '</code>', 'gim');

  text = globals.converter._dispatch('hashCodeTags.after', text, options, globals);
  return text;
});

showdown.subParser('hashElement', function (text, options, globals) {
  'use strict';

  return function (wholeMatch, m1) {
    var blockText = m1;

    // Undo double lines
    blockText = blockText.replace(/\n\n/g, '\n');
    blockText = blockText.replace(/^\n/, '');

    // strip trailing blank lines
    blockText = blockText.replace(/\n+$/g, '');

    // Replace the element text with a marker ("¨KxK" where x is its key)
    blockText = '\n\n¨K' + (globals.gHtmlBlocks.push(blockText) - 1) + 'K\n\n';

    return blockText;
  };
});

showdown.subParser('hashHTMLBlocks', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('hashHTMLBlocks.before', text, options, globals);

  var blockTags = [
        'pre',
        'div',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'blockquote',
        'table',
        'dl',
        'ol',
        'ul',
        'script',
        'noscript',
        'form',
        'fieldset',
        'iframe',
        'math',
        'style',
        'section',
        'header',
        'footer',
        'nav',
        'article',
        'aside',
        'address',
        'audio',
        'canvas',
        'figure',
        'hgroup',
        'output',
        'video',
        'p'
      ],
      repFunc = function (wholeMatch, match, left, right) {
        var txt = wholeMatch;
        // check if this html element is marked as markdown
        // if so, it's contents should be parsed as markdown
        if (left.search(/\bmarkdown\b/) !== -1) {
          txt = left + globals.converter.makeHtml(match) + right;
        }
        return '\n\n¨K' + (globals.gHtmlBlocks.push(txt) - 1) + 'K\n\n';
      };

  if (options.backslashEscapesHTMLTags) {
    // encode backslash escaped HTML tags
    text = text.replace(/\\<(\/?[^>]+?)>/g, function (wm, inside) {
      return '&lt;' + inside + '&gt;';
    });
  }

  // hash HTML Blocks
  for (var i = 0; i < blockTags.length; ++i) {

    var opTagPos,
        rgx1     = new RegExp('^ {0,3}(<' + blockTags[i] + '\\b[^>]*>)', 'im'),
        patLeft  = '<' + blockTags[i] + '\\b[^>]*>',
        patRight = '</' + blockTags[i] + '>';
    // 1. Look for the first position of the first opening HTML tag in the text
    while ((opTagPos = showdown.helper.regexIndexOf(text, rgx1)) !== -1) {

      // if the HTML tag is \ escaped, we need to escape it and break


      //2. Split the text in that position
      var subTexts = showdown.helper.splitAtIndex(text, opTagPos),
          //3. Match recursively
          newSubText1 = showdown.helper.replaceRecursiveRegExp(subTexts[1], repFunc, patLeft, patRight, 'im');

      // prevent an infinite loop
      if (newSubText1 === subTexts[1]) {
        break;
      }
      text = subTexts[0].concat(newSubText1);
    }
  }
  // HR SPECIAL CASE
  text = text.replace(/(\n {0,3}(<(hr)\b([^<>])*?\/?>)[ \t]*(?=\n{2,}))/g,
    showdown.subParser('hashElement')(text, options, globals));

  // Special case for standalone HTML comments
  text = showdown.helper.replaceRecursiveRegExp(text, function (txt) {
    return '\n\n¨K' + (globals.gHtmlBlocks.push(txt) - 1) + 'K\n\n';
  }, '^ {0,3}<!--', '-->', 'gm');

  // PHP and ASP-style processor instructions (<?...?> and <%...%>)
  text = text.replace(/(?:\n\n)( {0,3}(?:<([?%])[^\r]*?\2>)[ \t]*(?=\n{2,}))/g,
    showdown.subParser('hashElement')(text, options, globals));

  text = globals.converter._dispatch('hashHTMLBlocks.after', text, options, globals);
  return text;
});

/**
 * Hash span elements that should not be parsed as markdown
 */
showdown.subParser('hashHTMLSpans', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('hashHTMLSpans.before', text, options, globals);

  function hashHTMLSpan (html) {
    return '¨C' + (globals.gHtmlSpans.push(html) - 1) + 'C';
  }

  // Hash Self Closing tags
  text = text.replace(/<[^>]+?\/>/gi, function (wm) {
    return hashHTMLSpan(wm);
  });

  // Hash tags without properties
  text = text.replace(/<([^>]+?)>[\s\S]*?<\/\1>/g, function (wm) {
    return hashHTMLSpan(wm);
  });

  // Hash tags with properties
  text = text.replace(/<([^>]+?)\s[^>]+?>[\s\S]*?<\/\1>/g, function (wm) {
    return hashHTMLSpan(wm);
  });

  // Hash self closing tags without />
  text = text.replace(/<[^>]+?>/gi, function (wm) {
    return hashHTMLSpan(wm);
  });

  /*showdown.helper.matchRecursiveRegExp(text, '<code\\b[^>]*>', '</code>', 'gi');*/

  text = globals.converter._dispatch('hashHTMLSpans.after', text, options, globals);
  return text;
});

/**
 * Unhash HTML spans
 */
showdown.subParser('unhashHTMLSpans', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('unhashHTMLSpans.before', text, options, globals);

  for (var i = 0; i < globals.gHtmlSpans.length; ++i) {
    var repText = globals.gHtmlSpans[i],
        // limiter to prevent infinite loop (assume 10 as limit for recurse)
        limit = 0;

    while (/¨C(\d+)C/.test(repText)) {
      var num = RegExp.$1;
      repText = repText.replace('¨C' + num + 'C', globals.gHtmlSpans[num]);
      if (limit === 10) {
        console.error('maximum nesting of 10 spans reached!!!');
        break;
      }
      ++limit;
    }
    text = text.replace('¨C' + i + 'C', repText);
  }

  text = globals.converter._dispatch('unhashHTMLSpans.after', text, options, globals);
  return text;
});

/**
 * Hash and escape <pre><code> elements that should not be parsed as markdown
 */
showdown.subParser('hashPreCodeTags', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('hashPreCodeTags.before', text, options, globals);

  var repFunc = function (wholeMatch, match, left, right) {
    // encode html entities
    var codeblock = left + showdown.subParser('encodeCode')(match, options, globals) + right;
    return '\n\n¨G' + (globals.ghCodeBlocks.push({text: wholeMatch, codeblock: codeblock}) - 1) + 'G\n\n';
  };

  // Hash <pre><code>
  text = showdown.helper.replaceRecursiveRegExp(text, repFunc, '^ {0,3}<pre\\b[^>]*>\\s*<code\\b[^>]*>', '^ {0,3}</code>\\s*</pre>', 'gim');

  text = globals.converter._dispatch('hashPreCodeTags.after', text, options, globals);
  return text;
});

showdown.subParser('headers', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('headers.before', text, options, globals);

  var headerLevelStart = (isNaN(parseInt(options.headerLevelStart))) ? 1 : parseInt(options.headerLevelStart),

      // Set text-style headers:
      //	Header 1
      //	========
      //
      //	Header 2
      //	--------
      //
      setextRegexH1 = (options.smoothLivePreview) ? /^(.+)[ \t]*\n={2,}[ \t]*\n+/gm : /^(.+)[ \t]*\n=+[ \t]*\n+/gm,
      setextRegexH2 = (options.smoothLivePreview) ? /^(.+)[ \t]*\n-{2,}[ \t]*\n+/gm : /^(.+)[ \t]*\n-+[ \t]*\n+/gm;

  text = text.replace(setextRegexH1, function (wholeMatch, m1) {

    var spanGamut = showdown.subParser('spanGamut')(m1, options, globals),
        hID = (options.noHeaderId) ? '' : ' id="' + headerId(m1) + '"',
        hLevel = headerLevelStart,
        hashBlock = '<h' + hLevel + hID + '>' + spanGamut + '</h' + hLevel + '>';
    return showdown.subParser('hashBlock')(hashBlock, options, globals);
  });

  text = text.replace(setextRegexH2, function (matchFound, m1) {
    var spanGamut = showdown.subParser('spanGamut')(m1, options, globals),
        hID = (options.noHeaderId) ? '' : ' id="' + headerId(m1) + '"',
        hLevel = headerLevelStart + 1,
        hashBlock = '<h' + hLevel + hID + '>' + spanGamut + '</h' + hLevel + '>';
    return showdown.subParser('hashBlock')(hashBlock, options, globals);
  });

  // atx-style headers:
  //  # Header 1
  //  ## Header 2
  //  ## Header 2 with closing hashes ##
  //  ...
  //  ###### Header 6
  //
  var atxStyle = (options.requireSpaceBeforeHeadingText) ? /^(#{1,6})[ \t]+(.+?)[ \t]*#*\n+/gm : /^(#{1,6})[ \t]*(.+?)[ \t]*#*\n+/gm;

  text = text.replace(atxStyle, function (wholeMatch, m1, m2) {
    var hText = m2;
    if (options.customizedHeaderId) {
      hText = m2.replace(/\s?\{([^{]+?)}\s*$/, '');
    }

    var span = showdown.subParser('spanGamut')(hText, options, globals),
        hID = (options.noHeaderId) ? '' : ' id="' + headerId(m2) + '"',
        hLevel = headerLevelStart - 1 + m1.length,
        header = '<h' + hLevel + hID + '>' + span + '</h' + hLevel + '>';

    return showdown.subParser('hashBlock')(header, options, globals);
  });

  function headerId (m) {
    var title,
        prefix;

    // It is separate from other options to allow combining prefix and customized
    if (options.customizedHeaderId) {
      var match = m.match(/\{([^{]+?)}\s*$/);
      if (match && match[1]) {
        m = match[1];
      }
    }

    title = m;

    // Prefix id to prevent causing inadvertent pre-existing style matches.
    if (showdown.helper.isString(options.prefixHeaderId)) {
      prefix = options.prefixHeaderId;
    } else if (options.prefixHeaderId === true) {
      prefix = 'section-';
    } else {
      prefix = '';
    }

    if (!options.rawPrefixHeaderId) {
      title = prefix + title;
    }

    if (options.ghCompatibleHeaderId) {
      title = title
        .replace(/ /g, '-')
        // replace previously escaped chars (&, ¨ and $)
        .replace(/&amp;/g, '')
        .replace(/¨T/g, '')
        .replace(/¨D/g, '')
        // replace rest of the chars (&~$ are repeated as they might have been escaped)
        // borrowed from github's redcarpet (some they should produce similar results)
        .replace(/[&+$,\/:;=?@"#{}|^¨~\[\]`\\*)(%.!'<>]/g, '')
        .toLowerCase();
    } else if (options.rawHeaderId) {
      title = title
        .replace(/ /g, '-')
        // replace previously escaped chars (&, ¨ and $)
        .replace(/&amp;/g, '&')
        .replace(/¨T/g, '¨')
        .replace(/¨D/g, '$')
        // replace " and '
        .replace(/["']/g, '-')
        .toLowerCase();
    } else {
      title = title
        .replace(/[^\w]/g, '')
        .toLowerCase();
    }

    if (options.rawPrefixHeaderId) {
      title = prefix + title;
    }

    if (globals.hashLinkCounts[title]) {
      title = title + '-' + (globals.hashLinkCounts[title]++);
    } else {
      globals.hashLinkCounts[title] = 1;
    }
    return title;
  }

  text = globals.converter._dispatch('headers.after', text, options, globals);
  return text;
});

/**
 * Turn Markdown link shortcuts into XHTML <a> tags.
 */
showdown.subParser('horizontalRule', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('horizontalRule.before', text, options, globals);

  var key = showdown.subParser('hashBlock')('<hr />', options, globals);
  text = text.replace(/^ {0,2}( ?-){3,}[ \t]*$/gm, key);
  text = text.replace(/^ {0,2}( ?\*){3,}[ \t]*$/gm, key);
  text = text.replace(/^ {0,2}( ?_){3,}[ \t]*$/gm, key);

  text = globals.converter._dispatch('horizontalRule.after', text, options, globals);
  return text;
});

/**
 * Turn Markdown image shortcuts into <img> tags.
 */
showdown.subParser('images', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('images.before', text, options, globals);

  var inlineRegExp      = /!\[([^\]]*?)][ \t]*()\([ \t]?<?([\S]+?(?:\([\S]*?\)[\S]*?)?)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*(?:(["'])([^"]*?)\6)?[ \t]?\)/g,
      crazyRegExp       = /!\[([^\]]*?)][ \t]*()\([ \t]?<([^>]*)>(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*(?:(?:(["'])([^"]*?)\6))?[ \t]?\)/g,
      base64RegExp      = /!\[([^\]]*?)][ \t]*()\([ \t]?<?(data:.+?\/.+?;base64,[A-Za-z0-9+/=\n]+?)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*(?:(["'])([^"]*?)\6)?[ \t]?\)/g,
      referenceRegExp   = /!\[([^\]]*?)] ?(?:\n *)?\[([\s\S]*?)]()()()()()/g,
      refShortcutRegExp = /!\[([^\[\]]+)]()()()()()/g;

  function writeImageTagBase64 (wholeMatch, altText, linkId, url, width, height, m5, title) {
    url = url.replace(/\s/g, '');
    return writeImageTag (wholeMatch, altText, linkId, url, width, height, m5, title);
  }

  function writeImageTag (wholeMatch, altText, linkId, url, width, height, m5, title) {

    var gUrls   = globals.gUrls,
        gTitles = globals.gTitles,
        gDims   = globals.gDimensions;

    linkId = linkId.toLowerCase();

    if (!title) {
      title = '';
    }
    // Special case for explicit empty url
    if (wholeMatch.search(/\(<?\s*>? ?(['"].*['"])?\)$/m) > -1) {
      url = '';

    } else if (url === '' || url === null) {
      if (linkId === '' || linkId === null) {
        // lower-case and turn embedded newlines into spaces
        linkId = altText.toLowerCase().replace(/ ?\n/g, ' ');
      }
      url = '#' + linkId;

      if (!showdown.helper.isUndefined(gUrls[linkId])) {
        url = gUrls[linkId];
        if (!showdown.helper.isUndefined(gTitles[linkId])) {
          title = gTitles[linkId];
        }
        if (!showdown.helper.isUndefined(gDims[linkId])) {
          width = gDims[linkId].width;
          height = gDims[linkId].height;
        }
      } else {
        return wholeMatch;
      }
    }

    altText = altText
      .replace(/"/g, '&quot;')
    //altText = showdown.helper.escapeCharacters(altText, '*_', false);
      .replace(showdown.helper.regexes.asteriskDashAndColon, showdown.helper.escapeCharactersCallback);
    //url = showdown.helper.escapeCharacters(url, '*_', false);
    url = url.replace(showdown.helper.regexes.asteriskDashAndColon, showdown.helper.escapeCharactersCallback);
    var result = '<img src="' + url + '" alt="' + altText + '"';

    if (title && showdown.helper.isString(title)) {
      title = title
        .replace(/"/g, '&quot;')
      //title = showdown.helper.escapeCharacters(title, '*_', false);
        .replace(showdown.helper.regexes.asteriskDashAndColon, showdown.helper.escapeCharactersCallback);
      result += ' title="' + title + '"';
    }

    if (width && height) {
      width  = (width === '*') ? 'auto' : width;
      height = (height === '*') ? 'auto' : height;

      result += ' width="' + width + '"';
      result += ' height="' + height + '"';
    }

    result += ' />';

    return result;
  }

  // First, handle reference-style labeled images: ![alt text][id]
  text = text.replace(referenceRegExp, writeImageTag);

  // Next, handle inline images:  ![alt text](url =<width>x<height> "optional title")

  // base64 encoded images
  text = text.replace(base64RegExp, writeImageTagBase64);

  // cases with crazy urls like ./image/cat1).png
  text = text.replace(crazyRegExp, writeImageTag);

  // normal cases
  text = text.replace(inlineRegExp, writeImageTag);

  // handle reference-style shortcuts: ![img text]
  text = text.replace(refShortcutRegExp, writeImageTag);

  text = globals.converter._dispatch('images.after', text, options, globals);
  return text;
});

showdown.subParser('italicsAndBold', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('italicsAndBold.before', text, options, globals);

  // it's faster to have 3 separate regexes for each case than have just one
  // because of backtracing, in some cases, it could lead to an exponential effect
  // called "catastrophic backtrace". Ominous!

  function parseInside (txt, left, right) {
    /*
    if (options.simplifiedAutoLink) {
      txt = showdown.subParser('simplifiedAutoLinks')(txt, options, globals);
    }
    */
    return left + txt + right;
  }

  // Parse underscores
  if (options.literalMidWordUnderscores) {
    text = text.replace(/\b___(\S[\s\S]*?)___\b/g, function (wm, txt) {
      return parseInside (txt, '<strong><em>', '</em></strong>');
    });
    text = text.replace(/\b__(\S[\s\S]*?)__\b/g, function (wm, txt) {
      return parseInside (txt, '<strong>', '</strong>');
    });
    text = text.replace(/\b_(\S[\s\S]*?)_\b/g, function (wm, txt) {
      return parseInside (txt, '<em>', '</em>');
    });
  } else {
    text = text.replace(/___(\S[\s\S]*?)___/g, function (wm, m) {
      return (/\S$/.test(m)) ? parseInside (m, '<strong><em>', '</em></strong>') : wm;
    });
    text = text.replace(/__(\S[\s\S]*?)__/g, function (wm, m) {
      return (/\S$/.test(m)) ? parseInside (m, '<strong>', '</strong>') : wm;
    });
    text = text.replace(/_([^\s_][\s\S]*?)_/g, function (wm, m) {
      // !/^_[^_]/.test(m) - test if it doesn't start with __ (since it seems redundant, we removed it)
      return (/\S$/.test(m)) ? parseInside (m, '<em>', '</em>') : wm;
    });
  }

  // Now parse asterisks
  if (options.literalMidWordAsterisks) {
    text = text.replace(/([^*]|^)\B\*\*\*(\S[\s\S]*?)\*\*\*\B(?!\*)/g, function (wm, lead, txt) {
      return parseInside (txt, lead + '<strong><em>', '</em></strong>');
    });
    text = text.replace(/([^*]|^)\B\*\*(\S[\s\S]*?)\*\*\B(?!\*)/g, function (wm, lead, txt) {
      return parseInside (txt, lead + '<strong>', '</strong>');
    });
    text = text.replace(/([^*]|^)\B\*(\S[\s\S]*?)\*\B(?!\*)/g, function (wm, lead, txt) {
      return parseInside (txt, lead + '<em>', '</em>');
    });
  } else {
    text = text.replace(/\*\*\*(\S[\s\S]*?)\*\*\*/g, function (wm, m) {
      return (/\S$/.test(m)) ? parseInside (m, '<strong><em>', '</em></strong>') : wm;
    });
    text = text.replace(/\*\*(\S[\s\S]*?)\*\*/g, function (wm, m) {
      return (/\S$/.test(m)) ? parseInside (m, '<strong>', '</strong>') : wm;
    });
    text = text.replace(/\*([^\s*][\s\S]*?)\*/g, function (wm, m) {
      // !/^\*[^*]/.test(m) - test if it doesn't start with ** (since it seems redundant, we removed it)
      return (/\S$/.test(m)) ? parseInside (m, '<em>', '</em>') : wm;
    });
  }


  text = globals.converter._dispatch('italicsAndBold.after', text, options, globals);
  return text;
});

/**
 * Form HTML ordered (numbered) and unordered (bulleted) lists.
 */
showdown.subParser('lists', function (text, options, globals) {
  'use strict';

  /**
   * Process the contents of a single ordered or unordered list, splitting it
   * into individual list items.
   * @param {string} listStr
   * @param {boolean} trimTrailing
   * @returns {string}
   */
  function processListItems (listStr, trimTrailing) {
    // The $g_list_level global keeps track of when we're inside a list.
    // Each time we enter a list, we increment it; when we leave a list,
    // we decrement. If it's zero, we're not in a list anymore.
    //
    // We do this because when we're not inside a list, we want to treat
    // something like this:
    //
    //    I recommend upgrading to version
    //    8. Oops, now this line is treated
    //    as a sub-list.
    //
    // As a single paragraph, despite the fact that the second line starts
    // with a digit-period-space sequence.
    //
    // Whereas when we're inside a list (or sub-list), that line will be
    // treated as the start of a sub-list. What a kludge, huh? This is
    // an aspect of Markdown's syntax that's hard to parse perfectly
    // without resorting to mind-reading. Perhaps the solution is to
    // change the syntax rules such that sub-lists must start with a
    // starting cardinal number; e.g. "1." or "a.".
    globals.gListLevel++;

    // trim trailing blank lines:
    listStr = listStr.replace(/\n{2,}$/, '\n');

    // attacklab: add sentinel to emulate \z
    listStr += '¨0';

    var rgx = /(\n)?(^ {0,3})([*+-]|\d+[.])[ \t]+((\[(x|X| )?])?[ \t]*[^\r]+?(\n{1,2}))(?=\n*(¨0| {0,3}([*+-]|\d+[.])[ \t]+))/gm,
        isParagraphed = (/\n[ \t]*\n(?!¨0)/.test(listStr));

    // Since version 1.5, nesting sublists requires 4 spaces (or 1 tab) indentation,
    // which is a syntax breaking change
    // activating this option reverts to old behavior
    if (options.disableForced4SpacesIndentedSublists) {
      rgx = /(\n)?(^ {0,3})([*+-]|\d+[.])[ \t]+((\[(x|X| )?])?[ \t]*[^\r]+?(\n{1,2}))(?=\n*(¨0|\2([*+-]|\d+[.])[ \t]+))/gm;
    }

    listStr = listStr.replace(rgx, function (wholeMatch, m1, m2, m3, m4, taskbtn, checked) {
      checked = (checked && checked.trim() !== '');

      var item = showdown.subParser('outdent')(m4, options, globals),
          bulletStyle = '';

      // Support for github tasklists
      if (taskbtn && options.tasklists) {
        bulletStyle = ' class="task-list-item" style="list-style-type: none;"';
        item = item.replace(/^[ \t]*\[(x|X| )?]/m, function () {
          var otp = '<input type="checkbox" disabled style="margin: 0px 0.35em 0.25em -1.6em; vertical-align: middle;"';
          if (checked) {
            otp += ' checked';
          }
          otp += '>';
          return otp;
        });
      }

      // ISSUE #312
      // This input: - - - a
      // causes trouble to the parser, since it interprets it as:
      // <ul><li><li><li>a</li></li></li></ul>
      // instead of:
      // <ul><li>- - a</li></ul>
      // So, to prevent it, we will put a marker (¨A)in the beginning of the line
      // Kind of hackish/monkey patching, but seems more effective than overcomplicating the list parser
      item = item.replace(/^([-*+]|\d\.)[ \t]+[\S\n ]*/g, function (wm2) {
        return '¨A' + wm2;
      });

      // m1 - Leading line or
      // Has a double return (multi paragraph) or
      // Has sublist
      if (m1 || (item.search(/\n{2,}/) > -1)) {
        item = showdown.subParser('githubCodeBlocks')(item, options, globals);
        item = showdown.subParser('blockGamut')(item, options, globals);
      } else {
        // Recursion for sub-lists:
        item = showdown.subParser('lists')(item, options, globals);
        item = item.replace(/\n$/, ''); // chomp(item)
        item = showdown.subParser('hashHTMLBlocks')(item, options, globals);

        // Colapse double linebreaks
        item = item.replace(/\n\n+/g, '\n\n');
        if (isParagraphed) {
          item = showdown.subParser('paragraphs')(item, options, globals);
        } else {
          item = showdown.subParser('spanGamut')(item, options, globals);
        }
      }

      // now we need to remove the marker (¨A)
      item = item.replace('¨A', '');
      // we can finally wrap the line in list item tags
      item =  '<li' + bulletStyle + '>' + item + '</li>\n';

      return item;
    });

    // attacklab: strip sentinel
    listStr = listStr.replace(/¨0/g, '');

    globals.gListLevel--;

    if (trimTrailing) {
      listStr = listStr.replace(/\s+$/, '');
    }

    return listStr;
  }

  function styleStartNumber (list, listType) {
    // check if ol and starts by a number different than 1
    if (listType === 'ol') {
      var res = list.match(/^ *(\d+)\./);
      if (res && res[1] !== '1') {
        return ' start="' + res[1] + '"';
      }
    }
    return '';
  }

  /**
   * Check and parse consecutive lists (better fix for issue #142)
   * @param {string} list
   * @param {string} listType
   * @param {boolean} trimTrailing
   * @returns {string}
   */
  function parseConsecutiveLists (list, listType, trimTrailing) {
    // check if we caught 2 or more consecutive lists by mistake
    // we use the counterRgx, meaning if listType is UL we look for OL and vice versa
    var olRgx = (options.disableForced4SpacesIndentedSublists) ? /^ ?\d+\.[ \t]/gm : /^ {0,3}\d+\.[ \t]/gm,
        ulRgx = (options.disableForced4SpacesIndentedSublists) ? /^ ?[*+-][ \t]/gm : /^ {0,3}[*+-][ \t]/gm,
        counterRxg = (listType === 'ul') ? olRgx : ulRgx,
        result = '';

    if (list.search(counterRxg) !== -1) {
      (function parseCL (txt) {
        var pos = txt.search(counterRxg),
            style = styleStartNumber(list, listType);
        if (pos !== -1) {
          // slice
          result += '\n\n<' + listType + style + '>\n' + processListItems(txt.slice(0, pos), !!trimTrailing) + '</' + listType + '>\n';

          // invert counterType and listType
          listType = (listType === 'ul') ? 'ol' : 'ul';
          counterRxg = (listType === 'ul') ? olRgx : ulRgx;

          //recurse
          parseCL(txt.slice(pos));
        } else {
          result += '\n\n<' + listType + style + '>\n' + processListItems(txt, !!trimTrailing) + '</' + listType + '>\n';
        }
      })(list);
    } else {
      var style = styleStartNumber(list, listType);
      result = '\n\n<' + listType + style + '>\n' + processListItems(list, !!trimTrailing) + '</' + listType + '>\n';
    }

    return result;
  }

  /** Start of list parsing **/
  text = globals.converter._dispatch('lists.before', text, options, globals);
  // add sentinel to hack around khtml/safari bug:
  // http://bugs.webkit.org/show_bug.cgi?id=11231
  text += '¨0';

  if (globals.gListLevel) {
    text = text.replace(/^(( {0,3}([*+-]|\d+[.])[ \t]+)[^\r]+?(¨0|\n{2,}(?=\S)(?![ \t]*(?:[*+-]|\d+[.])[ \t]+)))/gm,
      function (wholeMatch, list, m2) {
        var listType = (m2.search(/[*+-]/g) > -1) ? 'ul' : 'ol';
        return parseConsecutiveLists(list, listType, true);
      }
    );
  } else {
    text = text.replace(/(\n\n|^\n?)(( {0,3}([*+-]|\d+[.])[ \t]+)[^\r]+?(¨0|\n{2,}(?=\S)(?![ \t]*(?:[*+-]|\d+[.])[ \t]+)))/gm,
      function (wholeMatch, m1, list, m3) {
        var listType = (m3.search(/[*+-]/g) > -1) ? 'ul' : 'ol';
        return parseConsecutiveLists(list, listType, false);
      }
    );
  }

  // strip sentinel
  text = text.replace(/¨0/, '');
  text = globals.converter._dispatch('lists.after', text, options, globals);
  return text;
});

/**
 * Parse metadata at the top of the document
 */
showdown.subParser('metadata', function (text, options, globals) {
  'use strict';

  if (!options.metadata) {
    return text;
  }

  text = globals.converter._dispatch('metadata.before', text, options, globals);

  function parseMetadataContents (content) {
    // raw is raw so it's not changed in any way
    globals.metadata.raw = content;

    // escape chars forbidden in html attributes
    // double quotes
    content = content
      // ampersand first
      .replace(/&/g, '&amp;')
      // double quotes
      .replace(/"/g, '&quot;');

    content = content.replace(/\n {4}/g, ' ');
    content.replace(/^([\S ]+): +([\s\S]+?)$/gm, function (wm, key, value) {
      globals.metadata.parsed[key] = value;
      return '';
    });
  }

  text = text.replace(/^\s*«««+(\S*?)\n([\s\S]+?)\n»»»+\n/, function (wholematch, format, content) {
    parseMetadataContents(content);
    return '¨M';
  });

  text = text.replace(/^\s*---+(\S*?)\n([\s\S]+?)\n---+\n/, function (wholematch, format, content) {
    if (format) {
      globals.metadata.format = format;
    }
    parseMetadataContents(content);
    return '¨M';
  });

  text = text.replace(/¨M/g, '');

  text = globals.converter._dispatch('metadata.after', text, options, globals);
  return text;
});

/**
 * Remove one level of line-leading tabs or spaces
 */
showdown.subParser('outdent', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('outdent.before', text, options, globals);

  // attacklab: hack around Konqueror 3.5.4 bug:
  // "----------bug".replace(/^-/g,"") == "bug"
  text = text.replace(/^(\t|[ ]{1,4})/gm, '¨0'); // attacklab: g_tab_width

  // attacklab: clean up hack
  text = text.replace(/¨0/g, '');

  text = globals.converter._dispatch('outdent.after', text, options, globals);
  return text;
});

/**
 *
 */
showdown.subParser('paragraphs', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('paragraphs.before', text, options, globals);
  // Strip leading and trailing lines:
  text = text.replace(/^\n+/g, '');
  text = text.replace(/\n+$/g, '');

  var grafs = text.split(/\n{2,}/g),
      grafsOut = [],
      end = grafs.length; // Wrap <p> tags

  for (var i = 0; i < end; i++) {
    var str = grafs[i];
    // if this is an HTML marker, copy it
    if (str.search(/¨(K|G)(\d+)\1/g) >= 0) {
      grafsOut.push(str);

    // test for presence of characters to prevent empty lines being parsed
    // as paragraphs (resulting in undesired extra empty paragraphs)
    } else if (str.search(/\S/) >= 0) {
      str = showdown.subParser('spanGamut')(str, options, globals);
      str = str.replace(/^([ \t]*)/g, '<p>');
      str += '</p>';
      grafsOut.push(str);
    }
  }

  /** Unhashify HTML blocks */
  end = grafsOut.length;
  for (i = 0; i < end; i++) {
    var blockText = '',
        grafsOutIt = grafsOut[i],
        codeFlag = false;
    // if this is a marker for an html block...
    // use RegExp.test instead of string.search because of QML bug
    while (/¨(K|G)(\d+)\1/.test(grafsOutIt)) {
      var delim = RegExp.$1,
          num   = RegExp.$2;

      if (delim === 'K') {
        blockText = globals.gHtmlBlocks[num];
      } else {
        // we need to check if ghBlock is a false positive
        if (codeFlag) {
          // use encoded version of all text
          blockText = showdown.subParser('encodeCode')(globals.ghCodeBlocks[num].text, options, globals);
        } else {
          blockText = globals.ghCodeBlocks[num].codeblock;
        }
      }
      blockText = blockText.replace(/\$/g, '$$$$'); // Escape any dollar signs

      grafsOutIt = grafsOutIt.replace(/(\n\n)?¨(K|G)\d+\2(\n\n)?/, blockText);
      // Check if grafsOutIt is a pre->code
      if (/^<pre\b[^>]*>\s*<code\b[^>]*>/.test(grafsOutIt)) {
        codeFlag = true;
      }
    }
    grafsOut[i] = grafsOutIt;
  }
  text = grafsOut.join('\n');
  // Strip leading and trailing lines:
  text = text.replace(/^\n+/g, '');
  text = text.replace(/\n+$/g, '');
  return globals.converter._dispatch('paragraphs.after', text, options, globals);
});

/**
 * Run extension
 */
showdown.subParser('runExtension', function (ext, text, options, globals) {
  'use strict';

  if (ext.filter) {
    text = ext.filter(text, globals.converter, options);

  } else if (ext.regex) {
    // TODO remove this when old extension loading mechanism is deprecated
    var re = ext.regex;
    if (!(re instanceof RegExp)) {
      re = new RegExp(re, 'g');
    }
    text = text.replace(re, ext.replace);
  }

  return text;
});

/**
 * These are all the transformations that occur *within* block-level
 * tags like paragraphs, headers, and list items.
 */
showdown.subParser('spanGamut', function (text, options, globals) {
  'use strict';

  text = globals.converter._dispatch('spanGamut.before', text, options, globals);
  text = showdown.subParser('codeSpans')(text, options, globals);
  text = showdown.subParser('escapeSpecialCharsWithinTagAttributes')(text, options, globals);
  text = showdown.subParser('encodeBackslashEscapes')(text, options, globals);

  // Process anchor and image tags. Images must come first,
  // because ![foo][f] looks like an anchor.
  text = showdown.subParser('images')(text, options, globals);
  text = showdown.subParser('anchors')(text, options, globals);

  // Make links out of things like `<http://example.com/>`
  // Must come after anchors, because you can use < and >
  // delimiters in inline links like [this](<url>).
  text = showdown.subParser('autoLinks')(text, options, globals);
  text = showdown.subParser('simplifiedAutoLinks')(text, options, globals);
  text = showdown.subParser('emoji')(text, options, globals);
  text = showdown.subParser('underline')(text, options, globals);
  text = showdown.subParser('italicsAndBold')(text, options, globals);
  text = showdown.subParser('strikethrough')(text, options, globals);
  text = showdown.subParser('ellipsis')(text, options, globals);

  // we need to hash HTML tags inside spans
  text = showdown.subParser('hashHTMLSpans')(text, options, globals);

  // now we encode amps and angles
  text = showdown.subParser('encodeAmpsAndAngles')(text, options, globals);

  // Do hard breaks
  if (options.simpleLineBreaks) {
    // GFM style hard breaks
    // only add line breaks if the text does not contain a block (special case for lists)
    if (!/\n\n¨K/.test(text)) {
      text = text.replace(/\n+/g, '<br />\n');
    }
  } else {
    // Vanilla hard breaks
    text = text.replace(/  +\n/g, '<br />\n');
  }

  text = globals.converter._dispatch('spanGamut.after', text, options, globals);
  return text;
});

showdown.subParser('strikethrough', function (text, options, globals) {
  'use strict';

  function parseInside (txt) {
    if (options.simplifiedAutoLink) {
      txt = showdown.subParser('simplifiedAutoLinks')(txt, options, globals);
    }
    return '<del>' + txt + '</del>';
  }

  if (options.strikethrough) {
    text = globals.converter._dispatch('strikethrough.before', text, options, globals);
    text = text.replace(/(?:~){2}([\s\S]+?)(?:~){2}/g, function (wm, txt) { return parseInside(txt); });
    text = globals.converter._dispatch('strikethrough.after', text, options, globals);
  }

  return text;
});

/**
 * Strips link definitions from text, stores the URLs and titles in
 * hash references.
 * Link defs are in the form: ^[id]: url "optional title"
 */
showdown.subParser('stripLinkDefinitions', function (text, options, globals) {
  'use strict';

  var regex       = /^ {0,3}\[(.+)]:[ \t]*\n?[ \t]*<?([^>\s]+)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*\n?[ \t]*(?:(\n*)["|'(](.+?)["|')][ \t]*)?(?:\n+|(?=¨0))/gm,
      base64Regex = /^ {0,3}\[(.+)]:[ \t]*\n?[ \t]*<?(data:.+?\/.+?;base64,[A-Za-z0-9+/=\n]+?)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*\n?[ \t]*(?:(\n*)["|'(](.+?)["|')][ \t]*)?(?:\n\n|(?=¨0)|(?=\n\[))/gm;

  // attacklab: sentinel workarounds for lack of \A and \Z, safari\khtml bug
  text += '¨0';

  var replaceFunc = function (wholeMatch, linkId, url, width, height, blankLines, title) {
    linkId = linkId.toLowerCase();
    if (url.match(/^data:.+?\/.+?;base64,/)) {
      // remove newlines
      globals.gUrls[linkId] = url.replace(/\s/g, '');
    } else {
      globals.gUrls[linkId] = showdown.subParser('encodeAmpsAndAngles')(url, options, globals);  // Link IDs are case-insensitive
    }

    if (blankLines) {
      // Oops, found blank lines, so it's not a title.
      // Put back the parenthetical statement we stole.
      return blankLines + title;

    } else {
      if (title) {
        globals.gTitles[linkId] = title.replace(/"|'/g, '&quot;');
      }
      if (options.parseImgDimensions && width && height) {
        globals.gDimensions[linkId] = {
          width:  width,
          height: height
        };
      }
    }
    // Completely remove the definition from the text
    return '';
  };

  // first we try to find base64 link references
  text = text.replace(base64Regex, replaceFunc);

  text = text.replace(regex, replaceFunc);

  // attacklab: strip sentinel
  text = text.replace(/¨0/, '');

  return text;
});

showdown.subParser('tables', function (text, options, globals) {
  'use strict';

  if (!options.tables) {
    return text;
  }

  var tableRgx       = /^ {0,3}\|?.+\|.+\n {0,3}\|?[ \t]*:?[ \t]*(?:[-=]){2,}[ \t]*:?[ \t]*\|[ \t]*:?[ \t]*(?:[-=]){2,}[\s\S]+?(?:\n\n|¨0)/gm,
      //singeColTblRgx = /^ {0,3}\|.+\|\n {0,3}\|[ \t]*:?[ \t]*(?:[-=]){2,}[ \t]*:?[ \t]*\|[ \t]*\n(?: {0,3}\|.+\|\n)+(?:\n\n|¨0)/gm;
      singeColTblRgx = /^ {0,3}\|.+\|[ \t]*\n {0,3}\|[ \t]*:?[ \t]*(?:[-=]){2,}[ \t]*:?[ \t]*\|[ \t]*\n( {0,3}\|.+\|[ \t]*\n)*(?:\n|¨0)/gm;

  function parseStyles (sLine) {
    if (/^:[ \t]*--*$/.test(sLine)) {
      return ' style="text-align:left;"';
    } else if (/^--*[ \t]*:[ \t]*$/.test(sLine)) {
      return ' style="text-align:right;"';
    } else if (/^:[ \t]*--*[ \t]*:$/.test(sLine)) {
      return ' style="text-align:center;"';
    } else {
      return '';
    }
  }

  function parseHeaders (header, style) {
    var id = '';
    header = header.trim();
    // support both tablesHeaderId and tableHeaderId due to error in documentation so we don't break backwards compatibility
    if (options.tablesHeaderId || options.tableHeaderId) {
      id = ' id="' + header.replace(/ /g, '_').toLowerCase() + '"';
    }
    header = showdown.subParser('spanGamut')(header, options, globals);

    return '<th' + id + style + '>' + header + '</th>\n';
  }

  function parseCells (cell, style) {
    var subText = showdown.subParser('spanGamut')(cell, options, globals);
    return '<td' + style + '>' + subText + '</td>\n';
  }

  function buildTable (headers, cells) {
    var tb = '<table>\n<thead>\n<tr>\n',
        tblLgn = headers.length;

    for (var i = 0; i < tblLgn; ++i) {
      tb += headers[i];
    }
    tb += '</tr>\n</thead>\n<tbody>\n';

    for (i = 0; i < cells.length; ++i) {
      tb += '<tr>\n';
      for (var ii = 0; ii < tblLgn; ++ii) {
        tb += cells[i][ii];
      }
      tb += '</tr>\n';
    }
    tb += '</tbody>\n</table>\n';
    return tb;
  }

  function parseTable (rawTable) {
    var i, tableLines = rawTable.split('\n');

    for (i = 0; i < tableLines.length; ++i) {
      // strip wrong first and last column if wrapped tables are used
      if (/^ {0,3}\|/.test(tableLines[i])) {
        tableLines[i] = tableLines[i].replace(/^ {0,3}\|/, '');
      }
      if (/\|[ \t]*$/.test(tableLines[i])) {
        tableLines[i] = tableLines[i].replace(/\|[ \t]*$/, '');
      }
      // parse code spans first, but we only support one line code spans
      tableLines[i] = showdown.subParser('codeSpans')(tableLines[i], options, globals);
    }

    var rawHeaders = tableLines[0].split('|').map(function (s) { return s.trim();}),
        rawStyles = tableLines[1].split('|').map(function (s) { return s.trim();}),
        rawCells = [],
        headers = [],
        styles = [],
        cells = [];

    tableLines.shift();
    tableLines.shift();

    for (i = 0; i < tableLines.length; ++i) {
      if (tableLines[i].trim() === '') {
        continue;
      }
      rawCells.push(
        tableLines[i]
          .split('|')
          .map(function (s) {
            return s.trim();
          })
      );
    }

    if (rawHeaders.length < rawStyles.length) {
      return rawTable;
    }

    for (i = 0; i < rawStyles.length; ++i) {
      styles.push(parseStyles(rawStyles[i]));
    }

    for (i = 0; i < rawHeaders.length; ++i) {
      if (showdown.helper.isUndefined(styles[i])) {
        styles[i] = '';
      }
      headers.push(parseHeaders(rawHeaders[i], styles[i]));
    }

    for (i = 0; i < rawCells.length; ++i) {
      var row = [];
      for (var ii = 0; ii < headers.length; ++ii) {
        if (showdown.helper.isUndefined(rawCells[i][ii])) {

        }
        row.push(parseCells(rawCells[i][ii], styles[ii]));
      }
      cells.push(row);
    }

    return buildTable(headers, cells);
  }

  text = globals.converter._dispatch('tables.before', text, options, globals);

  // find escaped pipe characters
  text = text.replace(/\\(\|)/g, showdown.helper.escapeCharactersCallback);

  // parse multi column tables
  text = text.replace(tableRgx, parseTable);

  // parse one column tables
  text = text.replace(singeColTblRgx, parseTable);

  text = globals.converter._dispatch('tables.after', text, options, globals);

  return text;
});

showdown.subParser('underline', function (text, options, globals) {
  'use strict';

  if (!options.underline) {
    return text;
  }

  text = globals.converter._dispatch('underline.before', text, options, globals);

  if (options.literalMidWordUnderscores) {
    text = text.replace(/\b___(\S[\s\S]*?)___\b/g, function (wm, txt) {
      return '<u>' + txt + '</u>';
    });
    text = text.replace(/\b__(\S[\s\S]*?)__\b/g, function (wm, txt) {
      return '<u>' + txt + '</u>';
    });
  } else {
    text = text.replace(/___(\S[\s\S]*?)___/g, function (wm, m) {
      return (/\S$/.test(m)) ? '<u>' + m + '</u>' : wm;
    });
    text = text.replace(/__(\S[\s\S]*?)__/g, function (wm, m) {
      return (/\S$/.test(m)) ? '<u>' + m + '</u>' : wm;
    });
  }

  // escape remaining underscores to prevent them being parsed by italic and bold
  text = text.replace(/(_)/g, showdown.helper.escapeCharactersCallback);

  text = globals.converter._dispatch('underline.after', text, options, globals);

  return text;
});

/**
 * Swap back in all the special characters we've hidden.
 */
showdown.subParser('unescapeSpecialChars', function (text, options, globals) {
  'use strict';
  text = globals.converter._dispatch('unescapeSpecialChars.before', text, options, globals);

  text = text.replace(/¨E(\d+)E/g, function (wholeMatch, m1) {
    var charCodeToReplace = parseInt(m1);
    return String.fromCharCode(charCodeToReplace);
  });

  text = globals.converter._dispatch('unescapeSpecialChars.after', text, options, globals);
  return text;
});

showdown.subParser('makeMarkdown.blockquote', function (node, globals) {
  'use strict';

  var txt = '';
  if (node.hasChildNodes()) {
    var children = node.childNodes,
        childrenLength = children.length;

    for (var i = 0; i < childrenLength; ++i) {
      var innerTxt = showdown.subParser('makeMarkdown.node')(children[i], globals);

      if (innerTxt === '') {
        continue;
      }
      txt += innerTxt;
    }
  }
  // cleanup
  txt = txt.trim();
  txt = '> ' + txt.split('\n').join('\n> ');
  return txt;
});

showdown.subParser('makeMarkdown.codeBlock', function (node, globals) {
  'use strict';

  var lang = node.getAttribute('language'),
      num  = node.getAttribute('precodenum');
  return '```' + lang + '\n' + globals.preList[num] + '\n```';
});

showdown.subParser('makeMarkdown.codeSpan', function (node) {
  'use strict';

  return '`' + node.innerHTML + '`';
});

showdown.subParser('makeMarkdown.emphasis', function (node, globals) {
  'use strict';

  var txt = '';
  if (node.hasChildNodes()) {
    txt += '*';
    var children = node.childNodes,
        childrenLength = children.length;
    for (var i = 0; i < childrenLength; ++i) {
      txt += showdown.subParser('makeMarkdown.node')(children[i], globals);
    }
    txt += '*';
  }
  return txt;
});

showdown.subParser('makeMarkdown.header', function (node, globals, headerLevel) {
  'use strict';

  var headerMark = new Array(headerLevel + 1).join('#'),
      txt = '';

  if (node.hasChildNodes()) {
    txt = headerMark + ' ';
    var children = node.childNodes,
        childrenLength = children.length;

    for (var i = 0; i < childrenLength; ++i) {
      txt += showdown.subParser('makeMarkdown.node')(children[i], globals);
    }
  }
  return txt;
});

showdown.subParser('makeMarkdown.hr', function () {
  'use strict';

  return '---';
});

showdown.subParser('makeMarkdown.image', function (node) {
  'use strict';

  var txt = '';
  if (node.hasAttribute('src')) {
    txt += '![' + node.getAttribute('alt') + '](';
    txt += '<' + node.getAttribute('src') + '>';
    if (node.hasAttribute('width') && node.hasAttribute('height')) {
      txt += ' =' + node.getAttribute('width') + 'x' + node.getAttribute('height');
    }

    if (node.hasAttribute('title')) {
      txt += ' "' + node.getAttribute('title') + '"';
    }
    txt += ')';
  }
  return txt;
});

showdown.subParser('makeMarkdown.links', function (node, globals) {
  'use strict';

  var txt = '';
  if (node.hasChildNodes() && node.hasAttribute('href')) {
    var children = node.childNodes,
        childrenLength = children.length;
    txt = '[';
    for (var i = 0; i < childrenLength; ++i) {
      txt += showdown.subParser('makeMarkdown.node')(children[i], globals);
    }
    txt += '](';
    txt += '<' + node.getAttribute('href') + '>';
    if (node.hasAttribute('title')) {
      txt += ' "' + node.getAttribute('title') + '"';
    }
    txt += ')';
  }
  return txt;
});

showdown.subParser('makeMarkdown.list', function (node, globals, type) {
  'use strict';

  var txt = '';
  if (!node.hasChildNodes()) {
    return '';
  }
  var listItems       = node.childNodes,
      listItemsLenght = listItems.length,
      listNum = node.getAttribute('start') || 1;

  for (var i = 0; i < listItemsLenght; ++i) {
    if (typeof listItems[i].tagName === 'undefined' || listItems[i].tagName.toLowerCase() !== 'li') {
      continue;
    }

    // define the bullet to use in list
    var bullet = '';
    if (type === 'ol') {
      bullet = listNum.toString() + '. ';
    } else {
      bullet = '- ';
    }

    // parse list item
    txt += bullet + showdown.subParser('makeMarkdown.listItem')(listItems[i], globals);
    ++listNum;
  }

  // add comment at the end to prevent consecutive lists to be parsed as one
  txt += '\n<!-- -->\n';
  return txt.trim();
});

showdown.subParser('makeMarkdown.listItem', function (node, globals) {
  'use strict';

  var listItemTxt = '';

  var children = node.childNodes,
      childrenLenght = children.length;

  for (var i = 0; i < childrenLenght; ++i) {
    listItemTxt += showdown.subParser('makeMarkdown.node')(children[i], globals);
  }
  // if it's only one liner, we need to add a newline at the end
  if (!/\n$/.test(listItemTxt)) {
    listItemTxt += '\n';
  } else {
    // it's multiparagraph, so we need to indent
    listItemTxt = listItemTxt
      .split('\n')
      .join('\n    ')
      .replace(/^ {4}$/gm, '')
      .replace(/\n\n+/g, '\n\n');
  }

  return listItemTxt;
});



showdown.subParser('makeMarkdown.node', function (node, globals, spansOnly) {
  'use strict';

  spansOnly = spansOnly || false;

  var txt = '';

  // edge case of text without wrapper paragraph
  if (node.nodeType === 3) {
    return showdown.subParser('makeMarkdown.txt')(node, globals);
  }

  // HTML comment
  if (node.nodeType === 8) {
    return '<!--' + node.data + '-->\n\n';
  }

  // process only node elements
  if (node.nodeType !== 1) {
    return '';
  }

  var tagName = node.tagName.toLowerCase();

  switch (tagName) {

    //
    // BLOCKS
    //
    case 'h1':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.header')(node, globals, 1) + '\n\n'; }
      break;
    case 'h2':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.header')(node, globals, 2) + '\n\n'; }
      break;
    case 'h3':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.header')(node, globals, 3) + '\n\n'; }
      break;
    case 'h4':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.header')(node, globals, 4) + '\n\n'; }
      break;
    case 'h5':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.header')(node, globals, 5) + '\n\n'; }
      break;
    case 'h6':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.header')(node, globals, 6) + '\n\n'; }
      break;

    case 'p':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.paragraph')(node, globals) + '\n\n'; }
      break;

    case 'blockquote':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.blockquote')(node, globals) + '\n\n'; }
      break;

    case 'hr':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.hr')(node, globals) + '\n\n'; }
      break;

    case 'ol':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.list')(node, globals, 'ol') + '\n\n'; }
      break;

    case 'ul':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.list')(node, globals, 'ul') + '\n\n'; }
      break;

    case 'precode':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.codeBlock')(node, globals) + '\n\n'; }
      break;

    case 'pre':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.pre')(node, globals) + '\n\n'; }
      break;

    case 'table':
      if (!spansOnly) { txt = showdown.subParser('makeMarkdown.table')(node, globals) + '\n\n'; }
      break;

    //
    // SPANS
    //
    case 'code':
      txt = showdown.subParser('makeMarkdown.codeSpan')(node, globals);
      break;

    case 'em':
    case 'i':
      txt = showdown.subParser('makeMarkdown.emphasis')(node, globals);
      break;

    case 'strong':
    case 'b':
      txt = showdown.subParser('makeMarkdown.strong')(node, globals);
      break;

    case 'del':
      txt = showdown.subParser('makeMarkdown.strikethrough')(node, globals);
      break;

    case 'a':
      txt = showdown.subParser('makeMarkdown.links')(node, globals);
      break;

    case 'img':
      txt = showdown.subParser('makeMarkdown.image')(node, globals);
      break;

    default:
      txt = node.outerHTML + '\n\n';
  }

  // common normalization
  // TODO eventually

  return txt;
});

showdown.subParser('makeMarkdown.paragraph', function (node, globals) {
  'use strict';

  var txt = '';
  if (node.hasChildNodes()) {
    var children = node.childNodes,
        childrenLength = children.length;
    for (var i = 0; i < childrenLength; ++i) {
      txt += showdown.subParser('makeMarkdown.node')(children[i], globals);
    }
  }

  // some text normalization
  txt = txt.trim();

  return txt;
});

showdown.subParser('makeMarkdown.pre', function (node, globals) {
  'use strict';

  var num  = node.getAttribute('prenum');
  return '<pre>' + globals.preList[num] + '</pre>';
});

showdown.subParser('makeMarkdown.strikethrough', function (node, globals) {
  'use strict';

  var txt = '';
  if (node.hasChildNodes()) {
    txt += '~~';
    var children = node.childNodes,
        childrenLength = children.length;
    for (var i = 0; i < childrenLength; ++i) {
      txt += showdown.subParser('makeMarkdown.node')(children[i], globals);
    }
    txt += '~~';
  }
  return txt;
});

showdown.subParser('makeMarkdown.strong', function (node, globals) {
  'use strict';

  var txt = '';
  if (node.hasChildNodes()) {
    txt += '**';
    var children = node.childNodes,
        childrenLength = children.length;
    for (var i = 0; i < childrenLength; ++i) {
      txt += showdown.subParser('makeMarkdown.node')(children[i], globals);
    }
    txt += '**';
  }
  return txt;
});

showdown.subParser('makeMarkdown.table', function (node, globals) {
  'use strict';

  var txt = '',
      tableArray = [[], []],
      headings   = node.querySelectorAll('thead>tr>th'),
      rows       = node.querySelectorAll('tbody>tr'),
      i, ii;
  for (i = 0; i < headings.length; ++i) {
    var headContent = showdown.subParser('makeMarkdown.tableCell')(headings[i], globals),
        allign = '---';

    if (headings[i].hasAttribute('style')) {
      var style = headings[i].getAttribute('style').toLowerCase().replace(/\s/g, '');
      switch (style) {
        case 'text-align:left;':
          allign = ':---';
          break;
        case 'text-align:right;':
          allign = '---:';
          break;
        case 'text-align:center;':
          allign = ':---:';
          break;
      }
    }
    tableArray[0][i] = headContent.trim();
    tableArray[1][i] = allign;
  }

  for (i = 0; i < rows.length; ++i) {
    var r = tableArray.push([]) - 1,
        cols = rows[i].getElementsByTagName('td');

    for (ii = 0; ii < headings.length; ++ii) {
      var cellContent = ' ';
      if (typeof cols[ii] !== 'undefined') {
        cellContent = showdown.subParser('makeMarkdown.tableCell')(cols[ii], globals);
      }
      tableArray[r].push(cellContent);
    }
  }

  var cellSpacesCount = 3;
  for (i = 0; i < tableArray.length; ++i) {
    for (ii = 0; ii < tableArray[i].length; ++ii) {
      var strLen = tableArray[i][ii].length;
      if (strLen > cellSpacesCount) {
        cellSpacesCount = strLen;
      }
    }
  }

  for (i = 0; i < tableArray.length; ++i) {
    for (ii = 0; ii < tableArray[i].length; ++ii) {
      if (i === 1) {
        if (tableArray[i][ii].slice(-1) === ':') {
          tableArray[i][ii] = showdown.helper.padEnd(tableArray[i][ii].slice(-1), cellSpacesCount - 1, '-') + ':';
        } else {
          tableArray[i][ii] = showdown.helper.padEnd(tableArray[i][ii], cellSpacesCount, '-');
        }
      } else {
        tableArray[i][ii] = showdown.helper.padEnd(tableArray[i][ii], cellSpacesCount);
      }
    }
    txt += '| ' + tableArray[i].join(' | ') + ' |\n';
  }

  return txt.trim();
});

showdown.subParser('makeMarkdown.tableCell', function (node, globals) {
  'use strict';

  var txt = '';
  if (!node.hasChildNodes()) {
    return '';
  }
  var children = node.childNodes,
      childrenLength = children.length;

  for (var i = 0; i < childrenLength; ++i) {
    txt += showdown.subParser('makeMarkdown.node')(children[i], globals, true);
  }
  return txt.trim();
});

showdown.subParser('makeMarkdown.txt', function (node) {
  'use strict';

  var txt = node.nodeValue;

  // multiple spaces are collapsed
  txt = txt.replace(/ +/g, ' ');

  // replace the custom ¨NBSP; with a space
  txt = txt.replace(/¨NBSP;/g, ' ');

  // ", <, > and & should replace escaped html entities
  txt = showdown.helper.unescapeHTMLEntities(txt);

  // escape markdown magic characters
  // emphasis, strong and strikethrough - can appear everywhere
  // we also escape pipe (|) because of tables
  // and escape ` because of code blocks and spans
  txt = txt.replace(/([*_~|`])/g, '\\$1');

  // escape > because of blockquotes
  txt = txt.replace(/^(\s*)>/g, '\\$1>');

  // hash character, only troublesome at the beginning of a line because of headers
  txt = txt.replace(/^#/gm, '\\#');

  // horizontal rules
  txt = txt.replace(/^(\s*)([-=]{3,})(\s*)$/, '$1\\$2$3');

  // dot, because of ordered lists, only troublesome at the beginning of a line when preceded by an integer
  txt = txt.replace(/^( {0,3}\d+)\./gm, '$1\\.');

  // +, * and -, at the beginning of a line becomes a list, so we need to escape them also (asterisk was already escaped)
  txt = txt.replace(/^( {0,3})([+-])/gm, '$1\\$2');

  // images and links, ] followed by ( is problematic, so we escape it
  txt = txt.replace(/]([\s]*)\(/g, '\\]$1\\(');

  // reference URIs must also be escaped
  txt = txt.replace(/^ {0,3}\[([\S \t]*?)]:/gm, '\\[$1]:');

  return txt;
});

var root = this;

// AMD Loader
if (true) {
  !(__WEBPACK_AMD_DEFINE_RESULT__ = (function () {
    'use strict';
    return showdown;
  }).call(exports, __webpack_require__, exports, module),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));

// CommonJS/nodeJS Loader
} else {}
}).call(this);




/***/ }),

/***/ "NMb1":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["deprecated"]; }());

/***/ }),

/***/ "SVSp":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["shortcode"]; }());

/***/ }),

/***/ "Tqx9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["primitives"]; }());

/***/ }),

/***/ "UuzZ":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["autop"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "Zss7":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_RESULT__;// TinyColor v1.4.2
// https://github.com/bgrins/TinyColor
// Brian Grinstead, MIT License

(function(Math) {

var trimLeft = /^\s+/,
    trimRight = /\s+$/,
    tinyCounter = 0,
    mathRound = Math.round,
    mathMin = Math.min,
    mathMax = Math.max,
    mathRandom = Math.random;

function tinycolor (color, opts) {

    color = (color) ? color : '';
    opts = opts || { };

    // If input is already a tinycolor, return itself
    if (color instanceof tinycolor) {
       return color;
    }
    // If we are called as a function, call using new instead
    if (!(this instanceof tinycolor)) {
        return new tinycolor(color, opts);
    }

    var rgb = inputToRGB(color);
    this._originalInput = color,
    this._r = rgb.r,
    this._g = rgb.g,
    this._b = rgb.b,
    this._a = rgb.a,
    this._roundA = mathRound(100*this._a) / 100,
    this._format = opts.format || rgb.format;
    this._gradientType = opts.gradientType;

    // Don't let the range of [0,255] come back in [0,1].
    // Potentially lose a little bit of precision here, but will fix issues where
    // .5 gets interpreted as half of the total, instead of half of 1
    // If it was supposed to be 128, this was already taken care of by `inputToRgb`
    if (this._r < 1) { this._r = mathRound(this._r); }
    if (this._g < 1) { this._g = mathRound(this._g); }
    if (this._b < 1) { this._b = mathRound(this._b); }

    this._ok = rgb.ok;
    this._tc_id = tinyCounter++;
}

tinycolor.prototype = {
    isDark: function() {
        return this.getBrightness() < 128;
    },
    isLight: function() {
        return !this.isDark();
    },
    isValid: function() {
        return this._ok;
    },
    getOriginalInput: function() {
      return this._originalInput;
    },
    getFormat: function() {
        return this._format;
    },
    getAlpha: function() {
        return this._a;
    },
    getBrightness: function() {
        //http://www.w3.org/TR/AERT#color-contrast
        var rgb = this.toRgb();
        return (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
    },
    getLuminance: function() {
        //http://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef
        var rgb = this.toRgb();
        var RsRGB, GsRGB, BsRGB, R, G, B;
        RsRGB = rgb.r/255;
        GsRGB = rgb.g/255;
        BsRGB = rgb.b/255;

        if (RsRGB <= 0.03928) {R = RsRGB / 12.92;} else {R = Math.pow(((RsRGB + 0.055) / 1.055), 2.4);}
        if (GsRGB <= 0.03928) {G = GsRGB / 12.92;} else {G = Math.pow(((GsRGB + 0.055) / 1.055), 2.4);}
        if (BsRGB <= 0.03928) {B = BsRGB / 12.92;} else {B = Math.pow(((BsRGB + 0.055) / 1.055), 2.4);}
        return (0.2126 * R) + (0.7152 * G) + (0.0722 * B);
    },
    setAlpha: function(value) {
        this._a = boundAlpha(value);
        this._roundA = mathRound(100*this._a) / 100;
        return this;
    },
    toHsv: function() {
        var hsv = rgbToHsv(this._r, this._g, this._b);
        return { h: hsv.h * 360, s: hsv.s, v: hsv.v, a: this._a };
    },
    toHsvString: function() {
        var hsv = rgbToHsv(this._r, this._g, this._b);
        var h = mathRound(hsv.h * 360), s = mathRound(hsv.s * 100), v = mathRound(hsv.v * 100);
        return (this._a == 1) ?
          "hsv("  + h + ", " + s + "%, " + v + "%)" :
          "hsva(" + h + ", " + s + "%, " + v + "%, "+ this._roundA + ")";
    },
    toHsl: function() {
        var hsl = rgbToHsl(this._r, this._g, this._b);
        return { h: hsl.h * 360, s: hsl.s, l: hsl.l, a: this._a };
    },
    toHslString: function() {
        var hsl = rgbToHsl(this._r, this._g, this._b);
        var h = mathRound(hsl.h * 360), s = mathRound(hsl.s * 100), l = mathRound(hsl.l * 100);
        return (this._a == 1) ?
          "hsl("  + h + ", " + s + "%, " + l + "%)" :
          "hsla(" + h + ", " + s + "%, " + l + "%, "+ this._roundA + ")";
    },
    toHex: function(allow3Char) {
        return rgbToHex(this._r, this._g, this._b, allow3Char);
    },
    toHexString: function(allow3Char) {
        return '#' + this.toHex(allow3Char);
    },
    toHex8: function(allow4Char) {
        return rgbaToHex(this._r, this._g, this._b, this._a, allow4Char);
    },
    toHex8String: function(allow4Char) {
        return '#' + this.toHex8(allow4Char);
    },
    toRgb: function() {
        return { r: mathRound(this._r), g: mathRound(this._g), b: mathRound(this._b), a: this._a };
    },
    toRgbString: function() {
        return (this._a == 1) ?
          "rgb("  + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ")" :
          "rgba(" + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ", " + this._roundA + ")";
    },
    toPercentageRgb: function() {
        return { r: mathRound(bound01(this._r, 255) * 100) + "%", g: mathRound(bound01(this._g, 255) * 100) + "%", b: mathRound(bound01(this._b, 255) * 100) + "%", a: this._a };
    },
    toPercentageRgbString: function() {
        return (this._a == 1) ?
          "rgb("  + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%)" :
          "rgba(" + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%, " + this._roundA + ")";
    },
    toName: function() {
        if (this._a === 0) {
            return "transparent";
        }

        if (this._a < 1) {
            return false;
        }

        return hexNames[rgbToHex(this._r, this._g, this._b, true)] || false;
    },
    toFilter: function(secondColor) {
        var hex8String = '#' + rgbaToArgbHex(this._r, this._g, this._b, this._a);
        var secondHex8String = hex8String;
        var gradientType = this._gradientType ? "GradientType = 1, " : "";

        if (secondColor) {
            var s = tinycolor(secondColor);
            secondHex8String = '#' + rgbaToArgbHex(s._r, s._g, s._b, s._a);
        }

        return "progid:DXImageTransform.Microsoft.gradient("+gradientType+"startColorstr="+hex8String+",endColorstr="+secondHex8String+")";
    },
    toString: function(format) {
        var formatSet = !!format;
        format = format || this._format;

        var formattedString = false;
        var hasAlpha = this._a < 1 && this._a >= 0;
        var needsAlphaFormat = !formatSet && hasAlpha && (format === "hex" || format === "hex6" || format === "hex3" || format === "hex4" || format === "hex8" || format === "name");

        if (needsAlphaFormat) {
            // Special case for "transparent", all other non-alpha formats
            // will return rgba when there is transparency.
            if (format === "name" && this._a === 0) {
                return this.toName();
            }
            return this.toRgbString();
        }
        if (format === "rgb") {
            formattedString = this.toRgbString();
        }
        if (format === "prgb") {
            formattedString = this.toPercentageRgbString();
        }
        if (format === "hex" || format === "hex6") {
            formattedString = this.toHexString();
        }
        if (format === "hex3") {
            formattedString = this.toHexString(true);
        }
        if (format === "hex4") {
            formattedString = this.toHex8String(true);
        }
        if (format === "hex8") {
            formattedString = this.toHex8String();
        }
        if (format === "name") {
            formattedString = this.toName();
        }
        if (format === "hsl") {
            formattedString = this.toHslString();
        }
        if (format === "hsv") {
            formattedString = this.toHsvString();
        }

        return formattedString || this.toHexString();
    },
    clone: function() {
        return tinycolor(this.toString());
    },

    _applyModification: function(fn, args) {
        var color = fn.apply(null, [this].concat([].slice.call(args)));
        this._r = color._r;
        this._g = color._g;
        this._b = color._b;
        this.setAlpha(color._a);
        return this;
    },
    lighten: function() {
        return this._applyModification(lighten, arguments);
    },
    brighten: function() {
        return this._applyModification(brighten, arguments);
    },
    darken: function() {
        return this._applyModification(darken, arguments);
    },
    desaturate: function() {
        return this._applyModification(desaturate, arguments);
    },
    saturate: function() {
        return this._applyModification(saturate, arguments);
    },
    greyscale: function() {
        return this._applyModification(greyscale, arguments);
    },
    spin: function() {
        return this._applyModification(spin, arguments);
    },

    _applyCombination: function(fn, args) {
        return fn.apply(null, [this].concat([].slice.call(args)));
    },
    analogous: function() {
        return this._applyCombination(analogous, arguments);
    },
    complement: function() {
        return this._applyCombination(complement, arguments);
    },
    monochromatic: function() {
        return this._applyCombination(monochromatic, arguments);
    },
    splitcomplement: function() {
        return this._applyCombination(splitcomplement, arguments);
    },
    triad: function() {
        return this._applyCombination(triad, arguments);
    },
    tetrad: function() {
        return this._applyCombination(tetrad, arguments);
    }
};

// If input is an object, force 1 into "1.0" to handle ratios properly
// String input requires "1.0" as input, so 1 will be treated as 1
tinycolor.fromRatio = function(color, opts) {
    if (typeof color == "object") {
        var newColor = {};
        for (var i in color) {
            if (color.hasOwnProperty(i)) {
                if (i === "a") {
                    newColor[i] = color[i];
                }
                else {
                    newColor[i] = convertToPercentage(color[i]);
                }
            }
        }
        color = newColor;
    }

    return tinycolor(color, opts);
};

// Given a string or object, convert that input to RGB
// Possible string inputs:
//
//     "red"
//     "#f00" or "f00"
//     "#ff0000" or "ff0000"
//     "#ff000000" or "ff000000"
//     "rgb 255 0 0" or "rgb (255, 0, 0)"
//     "rgb 1.0 0 0" or "rgb (1, 0, 0)"
//     "rgba (255, 0, 0, 1)" or "rgba 255, 0, 0, 1"
//     "rgba (1.0, 0, 0, 1)" or "rgba 1.0, 0, 0, 1"
//     "hsl(0, 100%, 50%)" or "hsl 0 100% 50%"
//     "hsla(0, 100%, 50%, 1)" or "hsla 0 100% 50%, 1"
//     "hsv(0, 100%, 100%)" or "hsv 0 100% 100%"
//
function inputToRGB(color) {

    var rgb = { r: 0, g: 0, b: 0 };
    var a = 1;
    var s = null;
    var v = null;
    var l = null;
    var ok = false;
    var format = false;

    if (typeof color == "string") {
        color = stringInputToObject(color);
    }

    if (typeof color == "object") {
        if (isValidCSSUnit(color.r) && isValidCSSUnit(color.g) && isValidCSSUnit(color.b)) {
            rgb = rgbToRgb(color.r, color.g, color.b);
            ok = true;
            format = String(color.r).substr(-1) === "%" ? "prgb" : "rgb";
        }
        else if (isValidCSSUnit(color.h) && isValidCSSUnit(color.s) && isValidCSSUnit(color.v)) {
            s = convertToPercentage(color.s);
            v = convertToPercentage(color.v);
            rgb = hsvToRgb(color.h, s, v);
            ok = true;
            format = "hsv";
        }
        else if (isValidCSSUnit(color.h) && isValidCSSUnit(color.s) && isValidCSSUnit(color.l)) {
            s = convertToPercentage(color.s);
            l = convertToPercentage(color.l);
            rgb = hslToRgb(color.h, s, l);
            ok = true;
            format = "hsl";
        }

        if (color.hasOwnProperty("a")) {
            a = color.a;
        }
    }

    a = boundAlpha(a);

    return {
        ok: ok,
        format: color.format || format,
        r: mathMin(255, mathMax(rgb.r, 0)),
        g: mathMin(255, mathMax(rgb.g, 0)),
        b: mathMin(255, mathMax(rgb.b, 0)),
        a: a
    };
}


// Conversion Functions
// --------------------

// `rgbToHsl`, `rgbToHsv`, `hslToRgb`, `hsvToRgb` modified from:
// <http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript>

// `rgbToRgb`
// Handle bounds / percentage checking to conform to CSS color spec
// <http://www.w3.org/TR/css3-color/>
// *Assumes:* r, g, b in [0, 255] or [0, 1]
// *Returns:* { r, g, b } in [0, 255]
function rgbToRgb(r, g, b){
    return {
        r: bound01(r, 255) * 255,
        g: bound01(g, 255) * 255,
        b: bound01(b, 255) * 255
    };
}

// `rgbToHsl`
// Converts an RGB color value to HSL.
// *Assumes:* r, g, and b are contained in [0, 255] or [0, 1]
// *Returns:* { h, s, l } in [0,1]
function rgbToHsl(r, g, b) {

    r = bound01(r, 255);
    g = bound01(g, 255);
    b = bound01(b, 255);

    var max = mathMax(r, g, b), min = mathMin(r, g, b);
    var h, s, l = (max + min) / 2;

    if(max == min) {
        h = s = 0; // achromatic
    }
    else {
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch(max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }

        h /= 6;
    }

    return { h: h, s: s, l: l };
}

// `hslToRgb`
// Converts an HSL color value to RGB.
// *Assumes:* h is contained in [0, 1] or [0, 360] and s and l are contained [0, 1] or [0, 100]
// *Returns:* { r, g, b } in the set [0, 255]
function hslToRgb(h, s, l) {
    var r, g, b;

    h = bound01(h, 360);
    s = bound01(s, 100);
    l = bound01(l, 100);

    function hue2rgb(p, q, t) {
        if(t < 0) t += 1;
        if(t > 1) t -= 1;
        if(t < 1/6) return p + (q - p) * 6 * t;
        if(t < 1/2) return q;
        if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
        return p;
    }

    if(s === 0) {
        r = g = b = l; // achromatic
    }
    else {
        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    return { r: r * 255, g: g * 255, b: b * 255 };
}

// `rgbToHsv`
// Converts an RGB color value to HSV
// *Assumes:* r, g, and b are contained in the set [0, 255] or [0, 1]
// *Returns:* { h, s, v } in [0,1]
function rgbToHsv(r, g, b) {

    r = bound01(r, 255);
    g = bound01(g, 255);
    b = bound01(b, 255);

    var max = mathMax(r, g, b), min = mathMin(r, g, b);
    var h, s, v = max;

    var d = max - min;
    s = max === 0 ? 0 : d / max;

    if(max == min) {
        h = 0; // achromatic
    }
    else {
        switch(max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }
    return { h: h, s: s, v: v };
}

// `hsvToRgb`
// Converts an HSV color value to RGB.
// *Assumes:* h is contained in [0, 1] or [0, 360] and s and v are contained in [0, 1] or [0, 100]
// *Returns:* { r, g, b } in the set [0, 255]
 function hsvToRgb(h, s, v) {

    h = bound01(h, 360) * 6;
    s = bound01(s, 100);
    v = bound01(v, 100);

    var i = Math.floor(h),
        f = h - i,
        p = v * (1 - s),
        q = v * (1 - f * s),
        t = v * (1 - (1 - f) * s),
        mod = i % 6,
        r = [v, q, p, p, t, v][mod],
        g = [t, v, v, q, p, p][mod],
        b = [p, p, t, v, v, q][mod];

    return { r: r * 255, g: g * 255, b: b * 255 };
}

// `rgbToHex`
// Converts an RGB color to hex
// Assumes r, g, and b are contained in the set [0, 255]
// Returns a 3 or 6 character hex
function rgbToHex(r, g, b, allow3Char) {

    var hex = [
        pad2(mathRound(r).toString(16)),
        pad2(mathRound(g).toString(16)),
        pad2(mathRound(b).toString(16))
    ];

    // Return a 3 character hex if possible
    if (allow3Char && hex[0].charAt(0) == hex[0].charAt(1) && hex[1].charAt(0) == hex[1].charAt(1) && hex[2].charAt(0) == hex[2].charAt(1)) {
        return hex[0].charAt(0) + hex[1].charAt(0) + hex[2].charAt(0);
    }

    return hex.join("");
}

// `rgbaToHex`
// Converts an RGBA color plus alpha transparency to hex
// Assumes r, g, b are contained in the set [0, 255] and
// a in [0, 1]. Returns a 4 or 8 character rgba hex
function rgbaToHex(r, g, b, a, allow4Char) {

    var hex = [
        pad2(mathRound(r).toString(16)),
        pad2(mathRound(g).toString(16)),
        pad2(mathRound(b).toString(16)),
        pad2(convertDecimalToHex(a))
    ];

    // Return a 4 character hex if possible
    if (allow4Char && hex[0].charAt(0) == hex[0].charAt(1) && hex[1].charAt(0) == hex[1].charAt(1) && hex[2].charAt(0) == hex[2].charAt(1) && hex[3].charAt(0) == hex[3].charAt(1)) {
        return hex[0].charAt(0) + hex[1].charAt(0) + hex[2].charAt(0) + hex[3].charAt(0);
    }

    return hex.join("");
}

// `rgbaToArgbHex`
// Converts an RGBA color to an ARGB Hex8 string
// Rarely used, but required for "toFilter()"
function rgbaToArgbHex(r, g, b, a) {

    var hex = [
        pad2(convertDecimalToHex(a)),
        pad2(mathRound(r).toString(16)),
        pad2(mathRound(g).toString(16)),
        pad2(mathRound(b).toString(16))
    ];

    return hex.join("");
}

// `equals`
// Can be called with any tinycolor input
tinycolor.equals = function (color1, color2) {
    if (!color1 || !color2) { return false; }
    return tinycolor(color1).toRgbString() == tinycolor(color2).toRgbString();
};

tinycolor.random = function() {
    return tinycolor.fromRatio({
        r: mathRandom(),
        g: mathRandom(),
        b: mathRandom()
    });
};


// Modification Functions
// ----------------------
// Thanks to less.js for some of the basics here
// <https://github.com/cloudhead/less.js/blob/master/lib/less/functions.js>

function desaturate(color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var hsl = tinycolor(color).toHsl();
    hsl.s -= amount / 100;
    hsl.s = clamp01(hsl.s);
    return tinycolor(hsl);
}

function saturate(color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var hsl = tinycolor(color).toHsl();
    hsl.s += amount / 100;
    hsl.s = clamp01(hsl.s);
    return tinycolor(hsl);
}

function greyscale(color) {
    return tinycolor(color).desaturate(100);
}

function lighten (color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var hsl = tinycolor(color).toHsl();
    hsl.l += amount / 100;
    hsl.l = clamp01(hsl.l);
    return tinycolor(hsl);
}

function brighten(color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var rgb = tinycolor(color).toRgb();
    rgb.r = mathMax(0, mathMin(255, rgb.r - mathRound(255 * - (amount / 100))));
    rgb.g = mathMax(0, mathMin(255, rgb.g - mathRound(255 * - (amount / 100))));
    rgb.b = mathMax(0, mathMin(255, rgb.b - mathRound(255 * - (amount / 100))));
    return tinycolor(rgb);
}

function darken (color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var hsl = tinycolor(color).toHsl();
    hsl.l -= amount / 100;
    hsl.l = clamp01(hsl.l);
    return tinycolor(hsl);
}

// Spin takes a positive or negative amount within [-360, 360] indicating the change of hue.
// Values outside of this range will be wrapped into this range.
function spin(color, amount) {
    var hsl = tinycolor(color).toHsl();
    var hue = (hsl.h + amount) % 360;
    hsl.h = hue < 0 ? 360 + hue : hue;
    return tinycolor(hsl);
}

// Combination Functions
// ---------------------
// Thanks to jQuery xColor for some of the ideas behind these
// <https://github.com/infusion/jQuery-xcolor/blob/master/jquery.xcolor.js>

function complement(color) {
    var hsl = tinycolor(color).toHsl();
    hsl.h = (hsl.h + 180) % 360;
    return tinycolor(hsl);
}

function triad(color) {
    var hsl = tinycolor(color).toHsl();
    var h = hsl.h;
    return [
        tinycolor(color),
        tinycolor({ h: (h + 120) % 360, s: hsl.s, l: hsl.l }),
        tinycolor({ h: (h + 240) % 360, s: hsl.s, l: hsl.l })
    ];
}

function tetrad(color) {
    var hsl = tinycolor(color).toHsl();
    var h = hsl.h;
    return [
        tinycolor(color),
        tinycolor({ h: (h + 90) % 360, s: hsl.s, l: hsl.l }),
        tinycolor({ h: (h + 180) % 360, s: hsl.s, l: hsl.l }),
        tinycolor({ h: (h + 270) % 360, s: hsl.s, l: hsl.l })
    ];
}

function splitcomplement(color) {
    var hsl = tinycolor(color).toHsl();
    var h = hsl.h;
    return [
        tinycolor(color),
        tinycolor({ h: (h + 72) % 360, s: hsl.s, l: hsl.l}),
        tinycolor({ h: (h + 216) % 360, s: hsl.s, l: hsl.l})
    ];
}

function analogous(color, results, slices) {
    results = results || 6;
    slices = slices || 30;

    var hsl = tinycolor(color).toHsl();
    var part = 360 / slices;
    var ret = [tinycolor(color)];

    for (hsl.h = ((hsl.h - (part * results >> 1)) + 720) % 360; --results; ) {
        hsl.h = (hsl.h + part) % 360;
        ret.push(tinycolor(hsl));
    }
    return ret;
}

function monochromatic(color, results) {
    results = results || 6;
    var hsv = tinycolor(color).toHsv();
    var h = hsv.h, s = hsv.s, v = hsv.v;
    var ret = [];
    var modification = 1 / results;

    while (results--) {
        ret.push(tinycolor({ h: h, s: s, v: v}));
        v = (v + modification) % 1;
    }

    return ret;
}

// Utility Functions
// ---------------------

tinycolor.mix = function(color1, color2, amount) {
    amount = (amount === 0) ? 0 : (amount || 50);

    var rgb1 = tinycolor(color1).toRgb();
    var rgb2 = tinycolor(color2).toRgb();

    var p = amount / 100;

    var rgba = {
        r: ((rgb2.r - rgb1.r) * p) + rgb1.r,
        g: ((rgb2.g - rgb1.g) * p) + rgb1.g,
        b: ((rgb2.b - rgb1.b) * p) + rgb1.b,
        a: ((rgb2.a - rgb1.a) * p) + rgb1.a
    };

    return tinycolor(rgba);
};


// Readability Functions
// ---------------------
// <http://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef (WCAG Version 2)

// `contrast`
// Analyze the 2 colors and returns the color contrast defined by (WCAG Version 2)
tinycolor.readability = function(color1, color2) {
    var c1 = tinycolor(color1);
    var c2 = tinycolor(color2);
    return (Math.max(c1.getLuminance(),c2.getLuminance())+0.05) / (Math.min(c1.getLuminance(),c2.getLuminance())+0.05);
};

// `isReadable`
// Ensure that foreground and background color combinations meet WCAG2 guidelines.
// The third argument is an optional Object.
//      the 'level' property states 'AA' or 'AAA' - if missing or invalid, it defaults to 'AA';
//      the 'size' property states 'large' or 'small' - if missing or invalid, it defaults to 'small'.
// If the entire object is absent, isReadable defaults to {level:"AA",size:"small"}.

// *Example*
//    tinycolor.isReadable("#000", "#111") => false
//    tinycolor.isReadable("#000", "#111",{level:"AA",size:"large"}) => false
tinycolor.isReadable = function(color1, color2, wcag2) {
    var readability = tinycolor.readability(color1, color2);
    var wcag2Parms, out;

    out = false;

    wcag2Parms = validateWCAG2Parms(wcag2);
    switch (wcag2Parms.level + wcag2Parms.size) {
        case "AAsmall":
        case "AAAlarge":
            out = readability >= 4.5;
            break;
        case "AAlarge":
            out = readability >= 3;
            break;
        case "AAAsmall":
            out = readability >= 7;
            break;
    }
    return out;

};

// `mostReadable`
// Given a base color and a list of possible foreground or background
// colors for that base, returns the most readable color.
// Optionally returns Black or White if the most readable color is unreadable.
// *Example*
//    tinycolor.mostReadable(tinycolor.mostReadable("#123", ["#124", "#125"],{includeFallbackColors:false}).toHexString(); // "#112255"
//    tinycolor.mostReadable(tinycolor.mostReadable("#123", ["#124", "#125"],{includeFallbackColors:true}).toHexString();  // "#ffffff"
//    tinycolor.mostReadable("#a8015a", ["#faf3f3"],{includeFallbackColors:true,level:"AAA",size:"large"}).toHexString(); // "#faf3f3"
//    tinycolor.mostReadable("#a8015a", ["#faf3f3"],{includeFallbackColors:true,level:"AAA",size:"small"}).toHexString(); // "#ffffff"
tinycolor.mostReadable = function(baseColor, colorList, args) {
    var bestColor = null;
    var bestScore = 0;
    var readability;
    var includeFallbackColors, level, size ;
    args = args || {};
    includeFallbackColors = args.includeFallbackColors ;
    level = args.level;
    size = args.size;

    for (var i= 0; i < colorList.length ; i++) {
        readability = tinycolor.readability(baseColor, colorList[i]);
        if (readability > bestScore) {
            bestScore = readability;
            bestColor = tinycolor(colorList[i]);
        }
    }

    if (tinycolor.isReadable(baseColor, bestColor, {"level":level,"size":size}) || !includeFallbackColors) {
        return bestColor;
    }
    else {
        args.includeFallbackColors=false;
        return tinycolor.mostReadable(baseColor,["#fff", "#000"],args);
    }
};


// Big List of Colors
// ------------------
// <http://www.w3.org/TR/css3-color/#svg-color>
var names = tinycolor.names = {
    aliceblue: "f0f8ff",
    antiquewhite: "faebd7",
    aqua: "0ff",
    aquamarine: "7fffd4",
    azure: "f0ffff",
    beige: "f5f5dc",
    bisque: "ffe4c4",
    black: "000",
    blanchedalmond: "ffebcd",
    blue: "00f",
    blueviolet: "8a2be2",
    brown: "a52a2a",
    burlywood: "deb887",
    burntsienna: "ea7e5d",
    cadetblue: "5f9ea0",
    chartreuse: "7fff00",
    chocolate: "d2691e",
    coral: "ff7f50",
    cornflowerblue: "6495ed",
    cornsilk: "fff8dc",
    crimson: "dc143c",
    cyan: "0ff",
    darkblue: "00008b",
    darkcyan: "008b8b",
    darkgoldenrod: "b8860b",
    darkgray: "a9a9a9",
    darkgreen: "006400",
    darkgrey: "a9a9a9",
    darkkhaki: "bdb76b",
    darkmagenta: "8b008b",
    darkolivegreen: "556b2f",
    darkorange: "ff8c00",
    darkorchid: "9932cc",
    darkred: "8b0000",
    darksalmon: "e9967a",
    darkseagreen: "8fbc8f",
    darkslateblue: "483d8b",
    darkslategray: "2f4f4f",
    darkslategrey: "2f4f4f",
    darkturquoise: "00ced1",
    darkviolet: "9400d3",
    deeppink: "ff1493",
    deepskyblue: "00bfff",
    dimgray: "696969",
    dimgrey: "696969",
    dodgerblue: "1e90ff",
    firebrick: "b22222",
    floralwhite: "fffaf0",
    forestgreen: "228b22",
    fuchsia: "f0f",
    gainsboro: "dcdcdc",
    ghostwhite: "f8f8ff",
    gold: "ffd700",
    goldenrod: "daa520",
    gray: "808080",
    green: "008000",
    greenyellow: "adff2f",
    grey: "808080",
    honeydew: "f0fff0",
    hotpink: "ff69b4",
    indianred: "cd5c5c",
    indigo: "4b0082",
    ivory: "fffff0",
    khaki: "f0e68c",
    lavender: "e6e6fa",
    lavenderblush: "fff0f5",
    lawngreen: "7cfc00",
    lemonchiffon: "fffacd",
    lightblue: "add8e6",
    lightcoral: "f08080",
    lightcyan: "e0ffff",
    lightgoldenrodyellow: "fafad2",
    lightgray: "d3d3d3",
    lightgreen: "90ee90",
    lightgrey: "d3d3d3",
    lightpink: "ffb6c1",
    lightsalmon: "ffa07a",
    lightseagreen: "20b2aa",
    lightskyblue: "87cefa",
    lightslategray: "789",
    lightslategrey: "789",
    lightsteelblue: "b0c4de",
    lightyellow: "ffffe0",
    lime: "0f0",
    limegreen: "32cd32",
    linen: "faf0e6",
    magenta: "f0f",
    maroon: "800000",
    mediumaquamarine: "66cdaa",
    mediumblue: "0000cd",
    mediumorchid: "ba55d3",
    mediumpurple: "9370db",
    mediumseagreen: "3cb371",
    mediumslateblue: "7b68ee",
    mediumspringgreen: "00fa9a",
    mediumturquoise: "48d1cc",
    mediumvioletred: "c71585",
    midnightblue: "191970",
    mintcream: "f5fffa",
    mistyrose: "ffe4e1",
    moccasin: "ffe4b5",
    navajowhite: "ffdead",
    navy: "000080",
    oldlace: "fdf5e6",
    olive: "808000",
    olivedrab: "6b8e23",
    orange: "ffa500",
    orangered: "ff4500",
    orchid: "da70d6",
    palegoldenrod: "eee8aa",
    palegreen: "98fb98",
    paleturquoise: "afeeee",
    palevioletred: "db7093",
    papayawhip: "ffefd5",
    peachpuff: "ffdab9",
    peru: "cd853f",
    pink: "ffc0cb",
    plum: "dda0dd",
    powderblue: "b0e0e6",
    purple: "800080",
    rebeccapurple: "663399",
    red: "f00",
    rosybrown: "bc8f8f",
    royalblue: "4169e1",
    saddlebrown: "8b4513",
    salmon: "fa8072",
    sandybrown: "f4a460",
    seagreen: "2e8b57",
    seashell: "fff5ee",
    sienna: "a0522d",
    silver: "c0c0c0",
    skyblue: "87ceeb",
    slateblue: "6a5acd",
    slategray: "708090",
    slategrey: "708090",
    snow: "fffafa",
    springgreen: "00ff7f",
    steelblue: "4682b4",
    tan: "d2b48c",
    teal: "008080",
    thistle: "d8bfd8",
    tomato: "ff6347",
    turquoise: "40e0d0",
    violet: "ee82ee",
    wheat: "f5deb3",
    white: "fff",
    whitesmoke: "f5f5f5",
    yellow: "ff0",
    yellowgreen: "9acd32"
};

// Make it easy to access colors via `hexNames[hex]`
var hexNames = tinycolor.hexNames = flip(names);


// Utilities
// ---------

// `{ 'name1': 'val1' }` becomes `{ 'val1': 'name1' }`
function flip(o) {
    var flipped = { };
    for (var i in o) {
        if (o.hasOwnProperty(i)) {
            flipped[o[i]] = i;
        }
    }
    return flipped;
}

// Return a valid alpha value [0,1] with all invalid values being set to 1
function boundAlpha(a) {
    a = parseFloat(a);

    if (isNaN(a) || a < 0 || a > 1) {
        a = 1;
    }

    return a;
}

// Take input from [0, n] and return it as [0, 1]
function bound01(n, max) {
    if (isOnePointZero(n)) { n = "100%"; }

    var processPercent = isPercentage(n);
    n = mathMin(max, mathMax(0, parseFloat(n)));

    // Automatically convert percentage into number
    if (processPercent) {
        n = parseInt(n * max, 10) / 100;
    }

    // Handle floating point rounding errors
    if ((Math.abs(n - max) < 0.000001)) {
        return 1;
    }

    // Convert into [0, 1] range if it isn't already
    return (n % max) / parseFloat(max);
}

// Force a number between 0 and 1
function clamp01(val) {
    return mathMin(1, mathMax(0, val));
}

// Parse a base-16 hex value into a base-10 integer
function parseIntFromHex(val) {
    return parseInt(val, 16);
}

// Need to handle 1.0 as 100%, since once it is a number, there is no difference between it and 1
// <http://stackoverflow.com/questions/7422072/javascript-how-to-detect-number-as-a-decimal-including-1-0>
function isOnePointZero(n) {
    return typeof n == "string" && n.indexOf('.') != -1 && parseFloat(n) === 1;
}

// Check to see if string passed in is a percentage
function isPercentage(n) {
    return typeof n === "string" && n.indexOf('%') != -1;
}

// Force a hex value to have 2 characters
function pad2(c) {
    return c.length == 1 ? '0' + c : '' + c;
}

// Replace a decimal with it's percentage value
function convertToPercentage(n) {
    if (n <= 1) {
        n = (n * 100) + "%";
    }

    return n;
}

// Converts a decimal to a hex value
function convertDecimalToHex(d) {
    return Math.round(parseFloat(d) * 255).toString(16);
}
// Converts a hex value to a decimal
function convertHexToDecimal(h) {
    return (parseIntFromHex(h) / 255);
}

var matchers = (function() {

    // <http://www.w3.org/TR/css3-values/#integers>
    var CSS_INTEGER = "[-\\+]?\\d+%?";

    // <http://www.w3.org/TR/css3-values/#number-value>
    var CSS_NUMBER = "[-\\+]?\\d*\\.\\d+%?";

    // Allow positive/negative integer/number.  Don't capture the either/or, just the entire outcome.
    var CSS_UNIT = "(?:" + CSS_NUMBER + ")|(?:" + CSS_INTEGER + ")";

    // Actual matching.
    // Parentheses and commas are optional, but not required.
    // Whitespace can take the place of commas or opening paren
    var PERMISSIVE_MATCH3 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";
    var PERMISSIVE_MATCH4 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";

    return {
        CSS_UNIT: new RegExp(CSS_UNIT),
        rgb: new RegExp("rgb" + PERMISSIVE_MATCH3),
        rgba: new RegExp("rgba" + PERMISSIVE_MATCH4),
        hsl: new RegExp("hsl" + PERMISSIVE_MATCH3),
        hsla: new RegExp("hsla" + PERMISSIVE_MATCH4),
        hsv: new RegExp("hsv" + PERMISSIVE_MATCH3),
        hsva: new RegExp("hsva" + PERMISSIVE_MATCH4),
        hex3: /^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
        hex6: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,
        hex4: /^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
        hex8: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/
    };
})();

// `isValidCSSUnit`
// Take in a single string / number and check to see if it looks like a CSS unit
// (see `matchers` above for definition).
function isValidCSSUnit(color) {
    return !!matchers.CSS_UNIT.exec(color);
}

// `stringInputToObject`
// Permissive string parsing.  Take in a number of formats, and output an object
// based on detected format.  Returns `{ r, g, b }` or `{ h, s, l }` or `{ h, s, v}`
function stringInputToObject(color) {

    color = color.replace(trimLeft,'').replace(trimRight, '').toLowerCase();
    var named = false;
    if (names[color]) {
        color = names[color];
        named = true;
    }
    else if (color == 'transparent') {
        return { r: 0, g: 0, b: 0, a: 0, format: "name" };
    }

    // Try to match string input using regular expressions.
    // Keep most of the number bounding out of this function - don't worry about [0,1] or [0,100] or [0,360]
    // Just return an object and let the conversion functions handle that.
    // This way the result will be the same whether the tinycolor is initialized with string or object.
    var match;
    if ((match = matchers.rgb.exec(color))) {
        return { r: match[1], g: match[2], b: match[3] };
    }
    if ((match = matchers.rgba.exec(color))) {
        return { r: match[1], g: match[2], b: match[3], a: match[4] };
    }
    if ((match = matchers.hsl.exec(color))) {
        return { h: match[1], s: match[2], l: match[3] };
    }
    if ((match = matchers.hsla.exec(color))) {
        return { h: match[1], s: match[2], l: match[3], a: match[4] };
    }
    if ((match = matchers.hsv.exec(color))) {
        return { h: match[1], s: match[2], v: match[3] };
    }
    if ((match = matchers.hsva.exec(color))) {
        return { h: match[1], s: match[2], v: match[3], a: match[4] };
    }
    if ((match = matchers.hex8.exec(color))) {
        return {
            r: parseIntFromHex(match[1]),
            g: parseIntFromHex(match[2]),
            b: parseIntFromHex(match[3]),
            a: convertHexToDecimal(match[4]),
            format: named ? "name" : "hex8"
        };
    }
    if ((match = matchers.hex6.exec(color))) {
        return {
            r: parseIntFromHex(match[1]),
            g: parseIntFromHex(match[2]),
            b: parseIntFromHex(match[3]),
            format: named ? "name" : "hex"
        };
    }
    if ((match = matchers.hex4.exec(color))) {
        return {
            r: parseIntFromHex(match[1] + '' + match[1]),
            g: parseIntFromHex(match[2] + '' + match[2]),
            b: parseIntFromHex(match[3] + '' + match[3]),
            a: convertHexToDecimal(match[4] + '' + match[4]),
            format: named ? "name" : "hex8"
        };
    }
    if ((match = matchers.hex3.exec(color))) {
        return {
            r: parseIntFromHex(match[1] + '' + match[1]),
            g: parseIntFromHex(match[2] + '' + match[2]),
            b: parseIntFromHex(match[3] + '' + match[3]),
            format: named ? "name" : "hex"
        };
    }

    return false;
}

function validateWCAG2Parms(parms) {
    // return valid WCAG2 parms for isReadable.
    // If input parms are invalid, return {"level":"AA", "size":"small"}
    var level, size;
    parms = parms || {"level":"AA", "size":"small"};
    level = (parms.level || "AA").toUpperCase();
    size = (parms.size || "small").toLowerCase();
    if (level !== "AA" && level !== "AAA") {
        level = "AA";
    }
    if (size !== "small" && size !== "large") {
        size = "small";
    }
    return {"level":level, "size":size};
}

// Node: Export function
if ( true && module.exports) {
    module.exports = tinycolor;
}
// AMD/requirejs: Define the module
else if (true) {
    !(__WEBPACK_AMD_DEFINE_RESULT__ = (function () {return tinycolor;}).call(exports, __webpack_require__, exports, module),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
}
// Browser: Expose to window
else {}

})(Math);


/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["hooks"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "ouCq":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockSerializationDefaultParser"]; }());

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

/***/ "rl8x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["isShallowEqual"]; }());

/***/ }),

/***/ "rmEH":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["htmlEntities"]; }());

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

/***/ "xTGt":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blob"]; }());

/***/ })

/******/ });