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
/******/ 	return __webpack_require__(__webpack_require__.s = 364);
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

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2017 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg) && arg.length) {
				var inner = classNames.apply(null, arg);
				if (inner) {
					classes.push(inner);
				}
			} else if (argType === 'object') {
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


/***/ }),

/***/ 12:
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

/***/ 13:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ 14:
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

/***/ 142:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["nux"]; }());

/***/ }),

/***/ 143:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["notices"]; }());

/***/ }),

/***/ 15:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(31);
/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(6);


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ 16:
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

/***/ 17:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _inherits; });

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

/***/ 18:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js
function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__(32);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _toConsumableArray; });



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ 19:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 21:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectWithoutProperties; });
/* harmony import */ var _objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(40);

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = Object(_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(source, excluded);
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

/***/ 22:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__(33);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
  if (!(Symbol.iterator in Object(arr) || Object.prototype.toString.call(arr) === "[object Arguments]")) {
    return;
  }

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
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__(34);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _slicedToArray; });



function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 230:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockLibrary"]; }());

/***/ }),

/***/ 25:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ 26:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["editor"]; }());

/***/ }),

/***/ 27:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ 31:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof(obj) {
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

/***/ 32:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

/***/ }),

/***/ 33:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ 34:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

/***/ }),

/***/ 364:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
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
__webpack_require__.d(actions_namespaceObject, "hideBlockTypes", function() { return actions_hideBlockTypes; });
__webpack_require__.d(actions_namespaceObject, "updatePreferredStyleVariations", function() { return actions_updatePreferredStyleVariations; });
__webpack_require__.d(actions_namespaceObject, "__experimentalUpdateLocalAutosaveInterval", function() { return __experimentalUpdateLocalAutosaveInterval; });
__webpack_require__.d(actions_namespaceObject, "showBlockTypes", function() { return actions_showBlockTypes; });
__webpack_require__.d(actions_namespaceObject, "setAvailableMetaBoxesPerLocation", function() { return setAvailableMetaBoxesPerLocation; });
__webpack_require__.d(actions_namespaceObject, "requestMetaBoxUpdates", function() { return requestMetaBoxUpdates; });
__webpack_require__.d(actions_namespaceObject, "metaBoxUpdatesSuccess", function() { return metaBoxUpdatesSuccess; });
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getEditorMode", function() { return getEditorMode; });
__webpack_require__.d(selectors_namespaceObject, "isEditorSidebarOpened", function() { return selectors_isEditorSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "isPluginSidebarOpened", function() { return isPluginSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "getActiveGeneralSidebarName", function() { return getActiveGeneralSidebarName; });
__webpack_require__.d(selectors_namespaceObject, "getPreferences", function() { return getPreferences; });
__webpack_require__.d(selectors_namespaceObject, "getPreference", function() { return selectors_getPreference; });
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
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","coreData"]}
var external_this_wp_coreData_ = __webpack_require__(62);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(26);

// EXTERNAL MODULE: external {"this":["wp","nux"]}
var external_this_wp_nux_ = __webpack_require__(142);

// EXTERNAL MODULE: external {"this":["wp","viewport"]}
var external_this_wp_viewport_ = __webpack_require__(50);

// EXTERNAL MODULE: external {"this":["wp","notices"]}
var external_this_wp_notices_ = __webpack_require__(143);

// EXTERNAL MODULE: external {"this":["wp","blockLibrary"]}
var external_this_wp_blockLibrary_ = __webpack_require__(230);

// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__(27);

// EXTERNAL MODULE: external {"this":["wp","mediaUtils"]}
var external_this_wp_mediaUtils_ = __webpack_require__(98);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/components/index.js
/**
 * WordPress dependencies
 */



var components_replaceMediaUpload = function replaceMediaUpload() {
  return external_this_wp_mediaUtils_["MediaUpload"];
};

Object(external_this_wp_hooks_["addFilter"])('editor.MediaUpload', 'core/edit-post/replace-media-upload', components_replaceMediaUpload);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(14);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js
var objectWithoutProperties = __webpack_require__(21);

// EXTERNAL MODULE: external {"this":"lodash"}
var external_this_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(10);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(8);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/validate-multiple-use/index.js




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








var enhance = Object(external_this_wp_compose_["compose"])(
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
Object(external_this_wp_data_["withSelect"])(function (select, block) {
  var multiple = Object(external_this_wp_blocks_["hasBlockSupport"])(block.name, 'multiple', true); // For block types with `multiple` support, there is no "original
  // block" to be found in the content, as the block itself is valid.

  if (multiple) {
    return {};
  } // Otherwise, only pass `originalBlockClientId` if it refers to a different
  // block from the current one.


  var blocks = select('core/block-editor').getBlocks();
  var firstOfSameType = Object(external_this_lodash_["find"])(blocks, function (_ref) {
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
      return dispatch('core/block-editor').selectBlock(originalBlockClientId);
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
    }, props))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["Warning"], {
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
var external_this_wp_plugins_ = __webpack_require__(49);

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(25);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/copy-content-menu-item/index.js


/**
 * WordPress dependencies
 */





function CopyContentMenuItem(_ref) {
  var createNotice = _ref.createNotice,
      editedPostContent = _ref.editedPostContent,
      hasCopied = _ref.hasCopied,
      setState = _ref.setState;
  return editedPostContent.length > 0 && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ClipboardButton"], {
    text: editedPostContent,
    role: "menuitem",
    className: "components-menu-item__button",
    onCopy: function onCopy() {
      setState({
        hasCopied: true
      });
      createNotice('info', Object(external_this_wp_i18n_["__"])('All content copied.'), {
        isDismissible: true,
        type: 'snackbar'
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
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/notices'),
      createNotice = _dispatch.createNotice;

  return {
    createNotice: createNotice
  };
}), Object(external_this_wp_compose_["withState"])({
  hasCopied: false
}))(CopyContentMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/manage-blocks-menu-item/index.js


/**
 * WordPress dependencies
 */



function ManageBlocksMenuItem(_ref) {
  var openModal = _ref.openModal;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
      openModal('edit-post/manage-blocks');
    }
  }, Object(external_this_wp_i18n_["__"])('Block Manager'));
}
/* harmony default export */ var manage_blocks_menu_item = (Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openModal = _dispatch.openModal;

  return {
    openModal: openModal
  };
})(ManageBlocksMenuItem));

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(19);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js


/**
 * WordPress dependencies
 */




function KeyboardShortcutsHelpMenuItem(_ref) {
  var openModal = _ref.openModal;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
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

/**
 * WordPress dependencies
 */




var _createSlotFill = Object(external_this_wp_components_["createSlotFill"])('ToolsMoreMenuGroup'),
    ToolsMoreMenuGroup = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;

ToolsMoreMenuGroup.Slot = function (_ref) {
  var fillProps = _ref.fillProps;
  return Object(external_this_wp_element_["createElement"])(Slot, {
    fillProps: fillProps
  }, function (fills) {
    return !Object(external_this_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
      label: Object(external_this_wp_i18n_["__"])('Tools')
    }, fills);
  });
};

/* harmony default export */ var tools_more_menu_group = (ToolsMoreMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/welcome-guide-menu-item/index.js


/**
 * WordPress dependencies
 */



function WelcomeGuideMenuItem() {
  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/nux'),
      enableTips = _useDispatch.enableTips;

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: enableTips
  }, Object(external_this_wp_i18n_["__"])('Welcome Guide'));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */






Object(external_this_wp_plugins_["registerPlugin"])('edit-post', {
  render: function render() {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(tools_more_menu_group, null, function (_ref) {
      var onClose = _ref.onClose;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(manage_blocks_menu_item, {
        onSelect: onClose
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
        role: "menuitem",
        href: Object(external_this_wp_url_["addQueryArgs"])('edit.php', {
          post_type: 'wp_block'
        })
      }, Object(external_this_wp_i18n_["__"])('Manage All Reusable Blocks')), Object(external_this_wp_element_["createElement"])(keyboard_shortcuts_help_menu_item, {
        onSelect: onClose
      }), Object(external_this_wp_element_["createElement"])(WelcomeGuideMenuItem, null), Object(external_this_wp_element_["createElement"])(copy_content_menu_item, null));
    }));
  }
});

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(18);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

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
    fixedToolbar: false,
    showInserterHelpPanel: true
  },
  pinnedPluginItems: {},
  hiddenBlockTypes: [],
  preferredStyleVariations: {},
  localAutosaveInterval: 15
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
 * Higher-order reducer creator which provides the given initial state for the
 * original reducer.
 *
 * @param {*} initialState Initial state to provide to reducer.
 *
 * @return {Function} Higher-order reducer.
 */

var createWithInitialState = function createWithInitialState(initialState) {
  return function (reducer) {
    return function () {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : initialState;
      var action = arguments.length > 1 ? arguments[1] : undefined;
      return reducer(state, action);
    };
  };
};
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


var preferences = Object(external_this_lodash_["flow"])([external_this_wp_data_["combineReducers"], createWithInitialState(PREFERENCES_DEFAULTS)])({
  isGeneralSidebarDismissed: function isGeneralSidebarDismissed(state, action) {
    switch (action.type) {
      case 'OPEN_GENERAL_SIDEBAR':
      case 'CLOSE_GENERAL_SIDEBAR':
        return action.type === 'CLOSE_GENERAL_SIDEBAR';
    }

    return state;
  },
  panels: function panels(state, action) {
    switch (action.type) {
      case 'TOGGLE_PANEL_ENABLED':
        {
          var panelName = action.panelName;
          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, panelName, Object(objectSpread["a" /* default */])({}, state[panelName], {
            enabled: !Object(external_this_lodash_["get"])(state, [panelName, 'enabled'], true)
          })));
        }

      case 'TOGGLE_PANEL_OPENED':
        {
          var _panelName = action.panelName;
          var isOpen = state[_panelName] === true || Object(external_this_lodash_["get"])(state, [_panelName, 'opened'], false);
          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, _panelName, Object(objectSpread["a" /* default */])({}, state[_panelName], {
            opened: !isOpen
          })));
        }
    }

    return state;
  },
  features: function features(state, action) {
    if (action.type === 'TOGGLE_FEATURE') {
      return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.feature, !state[action.feature]));
    }

    return state;
  },
  editorMode: function editorMode(state, action) {
    if (action.type === 'SWITCH_MODE') {
      return action.mode;
    }

    return state;
  },
  pinnedPluginItems: function pinnedPluginItems(state, action) {
    if (action.type === 'TOGGLE_PINNED_PLUGIN_ITEM') {
      return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.pluginName, !Object(external_this_lodash_["get"])(state, [action.pluginName], true)));
    }

    return state;
  },
  hiddenBlockTypes: function hiddenBlockTypes(state, action) {
    switch (action.type) {
      case 'SHOW_BLOCK_TYPES':
        return external_this_lodash_["without"].apply(void 0, [state].concat(Object(toConsumableArray["a" /* default */])(action.blockNames)));

      case 'HIDE_BLOCK_TYPES':
        return Object(external_this_lodash_["union"])(state, action.blockNames);
    }

    return state;
  },
  preferredStyleVariations: function preferredStyleVariations(state, action) {
    switch (action.type) {
      case 'UPDATE_PREFERRED_STYLE_VARIATIONS':
        {
          if (!action.blockName) {
            return state;
          }

          if (!action.blockStyle) {
            return Object(external_this_lodash_["omit"])(state, [action.blockName]);
          }

          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.blockName, action.blockStyle));
        }
    }

    return state;
  },
  localAutosaveInterval: function localAutosaveInterval(state, action) {
    switch (action.type) {
      case 'UPDATE_LOCAL_AUTOSAVE_INTERVAL':
        return action.interval;
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
      if (!Object(external_this_lodash_["includes"])(state, action.panelName)) {
        return [].concat(Object(toConsumableArray["a" /* default */])(state), [action.panelName]);
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
  }

  return state;
}
function publishSidebarActive() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

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
 * @param {boolean}  state   Previous state.
 * @param {Object}   action  Action Object.
 *
 * @return {Object} Updated state.
 */

function isSavingMetaBoxes() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REQUEST_META_BOX_UPDATES':
      return true;

    case 'META_BOX_UPDATES_SUCCESS':
      return false;

    default:
      return state;
  }
}
/**
 * Reducer keeping track of the meta boxes per location.
 *
 * @param {boolean}  state   Previous state.
 * @param {Object}   action  Action Object.
 *
 * @return {Object} Updated state.
 */

function metaBoxLocations() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_META_BOXES_PER_LOCATIONS':
      return action.metaBoxesPerLocation;
  }

  return state;
}
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
var refx = __webpack_require__(70);
var refx_default = /*#__PURE__*/__webpack_require__.n(refx);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(22);

// EXTERNAL MODULE: external {"this":["wp","a11y"]}
var external_this_wp_a11y_ = __webpack_require__(44);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(37);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
/**
 * External dependencies
 */

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
 * @param {string} name A string that uniquely identifies the modal.
 *
 * @return {Object} Action object.
 */

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

function actions_closePublishSidebar() {
  return {
    type: 'CLOSE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user toggles the publish sidebar.
 *
 * @return {Object} Action object
 */

function actions_togglePublishSidebar() {
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
 * Returns an action object used in signalling that block types by the given
 * name(s) should be hidden.
 *
 * @param {string[]} blockNames Names of block types to hide.
 *
 * @return {Object} Action object.
 */

function actions_hideBlockTypes(blockNames) {
  return {
    type: 'HIDE_BLOCK_TYPES',
    blockNames: Object(external_this_lodash_["castArray"])(blockNames)
  };
}
/**
 * Returns an action object used in signaling that a style should be auto-applied when a block is created.
 *
 * @param {string}  blockName  Name of the block.
 * @param {?string} blockStyle Name of the style that should be auto applied. If undefined, the "auto apply" setting of the block is removed.
 *
 * @return {Object} Action object.
 */

function actions_updatePreferredStyleVariations(blockName, blockStyle) {
  return {
    type: 'UPDATE_PREFERRED_STYLE_VARIATIONS',
    blockName: blockName,
    blockStyle: blockStyle
  };
}
function __experimentalUpdateLocalAutosaveInterval(interval) {
  return {
    type: 'UPDATE_LOCAL_AUTOSAVE_INTERVAL',
    interval: interval
  };
}
/**
 * Returns an action object used in signalling that block types by the given
 * name(s) should be shown.
 *
 * @param {string[]} blockNames Names of block types to show.
 *
 * @return {Object} Action object.
 */

function actions_showBlockTypes(blockNames) {
  return {
    type: 'SHOW_BLOCK_TYPES',
    blockNames: Object(external_this_lodash_["castArray"])(blockNames)
  };
}
/**
 * Returns an action object used in signaling
 * what Meta boxes are available in which location.
 *
 * @param {Object} metaBoxesPerLocation Meta boxes per location.
 *
 * @return {Object} Action object.
 */

function setAvailableMetaBoxesPerLocation(metaBoxesPerLocation) {
  return {
    type: 'SET_META_BOXES_PER_LOCATIONS',
    metaBoxesPerLocation: metaBoxesPerLocation
  };
}
/**
 * Returns an action object used to request meta box update.
 *
 * @return {Object} Action object.
 */

function requestMetaBoxUpdates() {
  return {
    type: 'REQUEST_META_BOX_UPDATES'
  };
}
/**
 * Returns an action object used signal a successful meta box update.
 *
 * @return {Object} Action object.
 */

function metaBoxUpdatesSuccess() {
  return {
    type: 'META_BOX_UPDATES_SUCCESS'
  };
}

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__(38);

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
  return selectors_getPreference(state, 'editorMode', 'visual');
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
  return Object(external_this_lodash_["includes"])(['edit-post/document', 'edit-post/block'], activeGeneralSidebar);
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
  var isDismissed = selectors_getPreference(state, 'isGeneralSidebarDismissed', false);

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
 * @param {*}      defaultValue  Default Value.
 *
 * @return {*} Preference Value.
 */

function selectors_getPreference(state, preferenceKey, defaultValue) {
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
  return Object(external_this_lodash_["includes"])(state.removedPanels, panelName);
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
  var panels = selectors_getPreference(state, 'panels');
  return !isEditorPanelRemoved(state, panelName) && Object(external_this_lodash_["get"])(panels, [panelName, 'enabled'], true);
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
  var panels = selectors_getPreference(state, 'panels');
  return Object(external_this_lodash_["get"])(panels, [panelName]) === true || Object(external_this_lodash_["get"])(panels, [panelName, 'opened']) === true;
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
  return Object(external_this_lodash_["get"])(state.preferences.features, [feature], false);
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
  var pinnedPluginItems = selectors_getPreference(state, 'pinnedPluginItems', {});
  return Object(external_this_lodash_["get"])(pinnedPluginItems, [pluginName], true);
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
  return isMetaBoxLocationActive(state, location) && Object(external_this_lodash_["some"])(getMetaBoxesPerLocation(state, location), function (_ref) {
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
  return Object(external_this_lodash_["flatten"])(Object(external_this_lodash_["values"])(state.metaBoxes.locations));
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

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/effects.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




var saveMetaboxUnsubscribe;
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
    var wasAutosavingPost = Object(external_this_wp_data_["select"])('core/editor').isAutosavingPost(); // Meta boxes are initialized once at page load. It is not necessary to
    // account for updates on each state change.
    //
    // See: https://github.com/WordPress/WordPress/blob/5.1.1/wp-admin/includes/post.php#L2307-L2309

    var hasActiveMetaBoxes = Object(external_this_wp_data_["select"])('core/edit-post').hasMetaBoxes(); // First remove any existing subscription in order to prevent multiple saves

    if (!!saveMetaboxUnsubscribe) {
      saveMetaboxUnsubscribe();
    } // Save metaboxes when performing a full save on the post.


    saveMetaboxUnsubscribe = Object(external_this_wp_data_["subscribe"])(function () {
      var isSavingPost = Object(external_this_wp_data_["select"])('core/editor').isSavingPost();
      var isAutosavingPost = Object(external_this_wp_data_["select"])('core/editor').isAutosavingPost(); // Save metaboxes on save completion, except for autosaves that are not a post preview.

      var shouldTriggerMetaboxesSave = hasActiveMetaBoxes && wasSavingPost && !isSavingPost && !wasAutosavingPost; // Save current state for next inspection.

      wasSavingPost = isSavingPost;
      wasAutosavingPost = isAutosavingPost;

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
    var additionalData = [post.comment_status ? ['comment_status', post.comment_status] : false, post.ping_status ? ['ping_status', post.ping_status] : false, post.sticky ? ['sticky', post.sticky] : false, post.author ? ['post_author', post.author] : false].filter(Boolean); // We gather all the metaboxes locations data and the base form data

    var baseFormData = new window.FormData(document.querySelector('.metabox-base-form'));
    var formDataToMerge = [baseFormData].concat(Object(toConsumableArray["a" /* default */])(getActiveMetaBoxLocations(state).map(function (location) {
      return new window.FormData(getMetaBoxContainer(location));
    }))); // Merge all form data objects into a single one.

    var formData = Object(external_this_lodash_["reduce"])(formDataToMerge, function (memo, currentFormData) {
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
      Object(external_this_wp_data_["dispatch"])('core/block-editor').clearSelectedBlock();
    }

    var message = action.mode === 'visual' ? Object(external_this_wp_i18n_["__"])('Visual editor selected') : Object(external_this_wp_i18n_["__"])('Code editor selected');
    Object(external_this_wp_a11y_["speak"])(message, 'assertive');
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
  enhancedDispatch = external_this_lodash_["flowRight"].apply(void 0, Object(toConsumableArray["a" /* default */])(chain))(store.dispatch);
  store.dispatch = enhancedDispatch;
  return store;
}

/* harmony default export */ var store_middlewares = (applyMiddlewares);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/controls.js


/**
 * WordPress dependencies
 */

/**
 * Calls a selector using the current state.
 *
 * @param {string} storeName    Store name.
 * @param {string} selectorName Selector name.
 * @param  {Array} args         Selector arguments.
 *
 * @return {Object} control descriptor.
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
var controls = {
  SELECT: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref) {
      var _registry$select;

      var storeName = _ref.storeName,
          selectorName = _ref.selectorName,
          args = _ref.args;
      return (_registry$select = registry.select(storeName))[selectorName].apply(_registry$select, Object(toConsumableArray["a" /* default */])(args));
    };
  })
};
/* harmony default export */ var store_controls = (controls);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
var STORE_KEY = 'core/edit-post';
/**
 * CSS selector string for the admin bar view post link anchor tag.
 *
 * @type {string}
 */

var VIEW_AS_LINK_SELECTOR = '#wp-admin-bar-view a';
/**
 * CSS selector string for the admin bar preview post link anchor tag.
 *
 * @type {string}
 */

var VIEW_AS_PREVIEW_LINK_SELECTOR = '#wp-admin-bar-preview a';

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */







var store_store = Object(external_this_wp_data_["registerStore"])(STORE_KEY, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  controls: store_controls,
  persist: ['preferences']
});
store_middlewares(store_store);
/* harmony default export */ var build_module_store = (store_store);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(16);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(17);

// EXTERNAL MODULE: ./node_modules/memize/index.js
var memize = __webpack_require__(42);
var memize_default = /*#__PURE__*/__webpack_require__.n(memize);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/prevent-event-discovery.js
/* harmony default export */ var prevent_event_discovery = ({
  't a l e s o f g u t e n b e r g': function tALESOFGUTENBERG(event) {
    if (!document.activeElement.classList.contains('edit-post-visual-editor') && document.activeElement !== document.body) {
      return;
    }

    event.preventDefault();
    window.wp.data.dispatch('core/block-editor').insertBlock(window.wp.blocks.createBlock('core/paragraph', {
      content: '🐡🐢🦀🐤🦋🐘🐧🐹🦁🦄🦍🐼🐿🎃🐴🐝🐆🦕🦔🌱🍇π🍌🐉💧🥨🌌🍂🍠🥦🥚🥝🎟🥥🥒🛵🥖🍒🍯🎾🎲🐺🐚🐮⌛️'
    }));
  }
});

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(11);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/text-editor/index.js


/**
 * WordPress dependencies
 */







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
  }, Object(external_this_wp_i18n_["__"])('Exit Code Editor')), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["TextEditorGlobalKeyboardShortcuts"], null)), Object(external_this_wp_element_["createElement"])("div", {
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

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/keyboard-shortcuts.js
/**
 * WordPress dependencies
 */

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
      onClick = _ref$onClick === void 0 ? external_this_lodash_["noop"] : _ref$onClick,
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
    className: "editor-block-settings-menu__control block-editor-block-settings-menu__control",
    onClick: Object(external_this_lodash_["flow"])(areAdvancedSettingsOpened ? closeSidebar : openEditorSidebar, speakMessage, onClick),
    icon: "admin-generic",
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
  selectedBlocks = Object(external_this_lodash_["map"])(selectedBlocks, function (block) {
    return block.name;
  });
  return Object(external_this_wp_element_["createElement"])(plugin_block_settings_menu_group_Slot, {
    fillProps: Object(objectSpread["a" /* default */])({}, fillProps, {
      selectedBlocks: selectedBlocks
    })
  }, function (fills) {
    return !Object(external_this_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", {
      className: "editor-block-settings-menu__separator block-editor-block-settings-menu__separator"
    }), fills);
  });
};

PluginBlockSettingsMenuGroup.Slot = Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var clientIds = _ref2.fillProps.clientIds;
  return {
    selectedBlocks: select('core/block-editor').getBlocksByClientId(clientIds)
  };
})(plugin_block_settings_menu_group_PluginBlockSettingsMenuGroupSlot);
/* harmony default export */ var plugin_block_settings_menu_group = (PluginBlockSettingsMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function VisualEditor() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockSelectionClearer"], {
    className: "edit-post-visual-editor editor-styles-wrapper"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["VisualEditorGlobalKeyboardShortcuts"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MultiSelectScrollIntoView"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["Typewriter"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["WritingFlow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["ObserveTyping"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["CopyHandler"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTitle"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockList"], null))))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalBlockSettingsMenuFirstItem"], null, function (_ref) {
    var onClose = _ref.onClose;
    return Object(external_this_wp_element_["createElement"])(block_inspector_button, {
      onClick: onClose
    });
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalBlockSettingsMenuPluginsExtension"], null, function (_ref2) {
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

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(6);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js









/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var keyboard_shortcuts_EditorModeKeyboardShortcuts =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(EditorModeKeyboardShortcuts, _Component);

  function EditorModeKeyboardShortcuts() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, EditorModeKeyboardShortcuts);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(EditorModeKeyboardShortcuts).apply(this, arguments));
    _this.toggleMode = _this.toggleMode.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.toggleSidebar = _this.toggleSidebar.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(EditorModeKeyboardShortcuts, [{
    key: "toggleMode",
    value: function toggleMode() {
      var _this$props = this.props,
          mode = _this$props.mode,
          switchMode = _this$props.switchMode,
          isModeSwitchEnabled = _this$props.isModeSwitchEnabled;

      if (!isModeSwitchEnabled) {
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
  var _select$getEditorSett = select('core/editor').getEditorSettings(),
      richEditingEnabled = _select$getEditorSett.richEditingEnabled,
      codeEditingEnabled = _select$getEditorSett.codeEditingEnabled;

  return {
    isModeSwitchEnabled: richEditingEnabled && codeEditingEnabled,
    mode: select('core/edit-post').getEditorMode(),
    isEditorSidebarOpen: select('core/edit-post').isEditorSidebarOpened()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps, _ref2) {
  var select = _ref2.select;
  return {
    switchMode: function switchMode(mode) {
      dispatch('core/edit-post').switchEditorMode(mode);
    },
    openSidebar: function openSidebar() {
      var _select = select('core/block-editor'),
          getBlockSelectionStart = _select.getBlockSelectionStart;

      var sidebarToOpen = getBlockSelectionStart() ? 'edit-post/block' : 'edit-post/document';
      dispatch('core/edit-post').openGeneralSidebar(sidebarToOpen);
    },
    closeSidebar: dispatch('core/edit-post').closeGeneralSidebar
  };
})])(keyboard_shortcuts_EditorModeKeyboardShortcuts));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * WordPress dependencies
 */


var primary = external_this_wp_keycodes_["displayShortcutList"].primary,
    primaryShift = external_this_wp_keycodes_["displayShortcutList"].primaryShift,
    primaryAlt = external_this_wp_keycodes_["displayShortcutList"].primaryAlt,
    secondary = external_this_wp_keycodes_["displayShortcutList"].secondary,
    access = external_this_wp_keycodes_["displayShortcutList"].access,
    ctrl = external_this_wp_keycodes_["displayShortcutList"].ctrl,
    alt = external_this_wp_keycodes_["displayShortcutList"].alt,
    ctrlShift = external_this_wp_keycodes_["displayShortcutList"].ctrlShift;
var mainShortcut = {
  className: 'edit-post-keyboard-shortcut-help__main-shortcuts',
  shortcuts: [{
    keyCombination: access('h'),
    description: Object(external_this_wp_i18n_["__"])('Display these keyboard shortcuts.')
  }]
};
var globalShortcuts = {
  title: Object(external_this_wp_i18n_["__"])('Global shortcuts'),
  shortcuts: [{
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
    keyCombination: access('n'),
    description: Object(external_this_wp_i18n_["__"])('Navigate to the next part of the editor (alternative).')
  }, {
    keyCombination: access('p'),
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
    keyCombination: primary('k'),
    description: Object(external_this_wp_i18n_["__"])('Convert the selected text into a link.')
  }, {
    keyCombination: primaryShift('k'),
    description: Object(external_this_wp_i18n_["__"])('Remove a link.')
  }, {
    keyCombination: primary('u'),
    description: Object(external_this_wp_i18n_["__"])('Underline the selected text.')
  }]
};
/* harmony default export */ var keyboard_shortcut_help_modal_config = ([mainShortcut, globalShortcuts, selectionShortcuts, blockShortcuts, textFormattingShortcuts]);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/index.js




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
  return (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */

    /* eslint-disable jsx-a11y/no-redundant-roles */
    Object(external_this_wp_element_["createElement"])("ul", {
      className: "edit-post-keyboard-shortcut-help__shortcut-list",
      role: "list"
    }, shortcuts.map(function (_ref2, index) {
      var keyCombination = _ref2.keyCombination,
          description = _ref2.description,
          ariaLabel = _ref2.ariaLabel;
      return Object(external_this_wp_element_["createElement"])("li", {
        className: "edit-post-keyboard-shortcut-help__shortcut",
        key: index
      }, Object(external_this_wp_element_["createElement"])("div", {
        className: "edit-post-keyboard-shortcut-help__shortcut-description"
      }, description), Object(external_this_wp_element_["createElement"])("div", {
        className: "edit-post-keyboard-shortcut-help__shortcut-term"
      }, Object(external_this_wp_element_["createElement"])("kbd", {
        className: "edit-post-keyboard-shortcut-help__shortcut-key-combination",
        "aria-label": ariaLabel
      }, keyboard_shortcut_help_modal_mapKeyCombination(Object(external_this_lodash_["castArray"])(keyCombination)))));
    }))
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
};

var keyboard_shortcut_help_modal_ShortcutSection = function ShortcutSection(_ref3) {
  var title = _ref3.title,
      shortcuts = _ref3.shortcuts,
      className = _ref3.className;
  return Object(external_this_wp_element_["createElement"])("section", {
    className: classnames_default()('edit-post-keyboard-shortcut-help__section', className)
  }, !!title && Object(external_this_wp_element_["createElement"])("h2", {
    className: "edit-post-keyboard-shortcut-help__section-title"
  }, title), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutList, {
    shortcuts: shortcuts
  }));
};

function KeyboardShortcutHelpModal(_ref4) {
  var isModalActive = _ref4.isModalActive,
      toggleModal = _ref4.toggleModal;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["KeyboardShortcuts"], {
    bindGlobal: true,
    shortcuts: Object(defineProperty["a" /* default */])({}, external_this_wp_keycodes_["rawShortcut"].access('h'), toggleModal)
  }), isModalActive && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Modal"], {
    className: "edit-post-keyboard-shortcut-help",
    title: Object(external_this_wp_i18n_["__"])('Keyboard Shortcuts'),
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

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/manage-blocks-modal/checklist.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function BlockTypesChecklist(_ref) {
  var blockTypes = _ref.blockTypes,
      value = _ref.value,
      onItemChange = _ref.onItemChange;
  return Object(external_this_wp_element_["createElement"])("ul", {
    className: "edit-post-manage-blocks-modal__checklist"
  }, blockTypes.map(function (blockType) {
    return Object(external_this_wp_element_["createElement"])("li", {
      key: blockType.name,
      className: "edit-post-manage-blocks-modal__checklist-item"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
      label: Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, blockType.title, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
        icon: blockType.icon
      })),
      checked: value.includes(blockType.name),
      onChange: Object(external_this_lodash_["partial"])(onItemChange, blockType.name)
    }));
  }));
}

/* harmony default export */ var checklist = (BlockTypesChecklist);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/edit-post-settings/index.js
/**
 * WordPress dependencies
 */

var EditPostSettings = Object(external_this_wp_element_["createContext"])({});
/* harmony default export */ var edit_post_settings = (EditPostSettings);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/manage-blocks-modal/category.js



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
  var instanceId = _ref.instanceId,
      category = _ref.category,
      blockTypes = _ref.blockTypes,
      hiddenBlockTypes = _ref.hiddenBlockTypes,
      toggleVisible = _ref.toggleVisible,
      toggleAllVisible = _ref.toggleAllVisible;
  var settings = Object(external_this_wp_element_["useContext"])(edit_post_settings);
  var allowedBlockTypes = settings.allowedBlockTypes;
  var filteredBlockTypes = Object(external_this_wp_element_["useMemo"])(function () {
    if (allowedBlockTypes === true) {
      return blockTypes;
    }

    return blockTypes.filter(function (_ref2) {
      var name = _ref2.name;
      return Object(external_this_lodash_["includes"])(allowedBlockTypes || [], name);
    });
  }, [allowedBlockTypes, blockTypes]);

  if (!filteredBlockTypes.length) {
    return null;
  }

  var checkedBlockNames = external_this_lodash_["without"].apply(void 0, [Object(external_this_lodash_["map"])(filteredBlockTypes, 'name')].concat(Object(toConsumableArray["a" /* default */])(hiddenBlockTypes)));
  var titleId = 'edit-post-manage-blocks-modal__category-title-' + instanceId;
  var isAllChecked = checkedBlockNames.length === filteredBlockTypes.length;
  var ariaChecked;

  if (isAllChecked) {
    ariaChecked = 'true';
  } else if (checkedBlockNames.length > 0) {
    ariaChecked = 'mixed';
  } else {
    ariaChecked = 'false';
  }

  return Object(external_this_wp_element_["createElement"])("div", {
    role: "group",
    "aria-labelledby": titleId,
    className: "edit-post-manage-blocks-modal__category"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
    checked: isAllChecked,
    onChange: toggleAllVisible,
    className: "edit-post-manage-blocks-modal__category-title",
    "aria-checked": ariaChecked,
    label: Object(external_this_wp_element_["createElement"])("span", {
      id: titleId
    }, category.title)
  }), Object(external_this_wp_element_["createElement"])(checklist, {
    blockTypes: filteredBlockTypes,
    value: checkedBlockNames,
    onItemChange: toggleVisible
  }));
}

/* harmony default export */ var manage_blocks_modal_category = (Object(external_this_wp_compose_["compose"])([external_this_wp_compose_["withInstanceId"], Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/edit-post'),
      getPreference = _select.getPreference;

  return {
    hiddenBlockTypes: getPreference('hiddenBlockTypes')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  var _dispatch = dispatch('core/edit-post'),
      showBlockTypes = _dispatch.showBlockTypes,
      hideBlockTypes = _dispatch.hideBlockTypes;

  return {
    toggleVisible: function toggleVisible(blockName, nextIsChecked) {
      if (nextIsChecked) {
        showBlockTypes(blockName);
      } else {
        hideBlockTypes(blockName);
      }
    },
    toggleAllVisible: function toggleAllVisible(nextIsChecked) {
      var blockNames = Object(external_this_lodash_["map"])(ownProps.blockTypes, 'name');

      if (nextIsChecked) {
        showBlockTypes(blockNames);
      } else {
        hideBlockTypes(blockNames);
      }
    }
  };
})])(BlockManagerCategory));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/manage-blocks-modal/manager.js


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
  var search = _ref.search,
      setState = _ref.setState,
      blockTypes = _ref.blockTypes,
      categories = _ref.categories,
      hasBlockSupport = _ref.hasBlockSupport,
      isMatchingSearchTerm = _ref.isMatchingSearchTerm,
      numberOfHiddenBlocks = _ref.numberOfHiddenBlocks;
  // Filtering occurs here (as opposed to `withSelect`) to avoid wasted
  // wasted renders by consequence of `Array#filter` producing a new
  // value reference on each call.
  blockTypes = blockTypes.filter(function (blockType) {
    return hasBlockSupport(blockType, 'inserter', true) && (!search || isMatchingSearchTerm(blockType, search)) && !blockType.parent;
  });
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-manage-blocks-modal__content"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
    type: "search",
    label: Object(external_this_wp_i18n_["__"])('Search for a block'),
    value: search,
    onChange: function onChange(nextSearch) {
      return setState({
        search: nextSearch
      });
    },
    className: "edit-post-manage-blocks-modal__search"
  }), !!numberOfHiddenBlocks && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-manage-blocks-modal__disabled-blocks-count"
  }, Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('%1$d block is disabled.', '%1$d blocks are disabled.', numberOfHiddenBlocks), numberOfHiddenBlocks)), Object(external_this_wp_element_["createElement"])("div", {
    tabIndex: "0",
    role: "region",
    "aria-label": Object(external_this_wp_i18n_["__"])('Available block types'),
    className: "edit-post-manage-blocks-modal__results"
  }, blockTypes.length === 0 && Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-manage-blocks-modal__no-results"
  }, Object(external_this_wp_i18n_["__"])('No blocks found.')), categories.map(function (category) {
    return Object(external_this_wp_element_["createElement"])(manage_blocks_modal_category, {
      key: category.slug,
      category: category,
      blockTypes: Object(external_this_lodash_["filter"])(blockTypes, {
        category: category.slug
      })
    });
  })));
}

/* harmony default export */ var manager = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_compose_["withState"])({
  search: ''
}), Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/blocks'),
      getBlockTypes = _select.getBlockTypes,
      getCategories = _select.getCategories,
      hasBlockSupport = _select.hasBlockSupport,
      isMatchingSearchTerm = _select.isMatchingSearchTerm;

  var _select2 = select('core/edit-post'),
      getPreference = _select2.getPreference;

  var hiddenBlockTypes = getPreference('hiddenBlockTypes');
  var numberOfHiddenBlocks = Object(external_this_lodash_["isArray"])(hiddenBlockTypes) && hiddenBlockTypes.length;
  return {
    blockTypes: getBlockTypes(),
    categories: getCategories(),
    hasBlockSupport: hasBlockSupport,
    isMatchingSearchTerm: isMatchingSearchTerm,
    numberOfHiddenBlocks: numberOfHiddenBlocks
  };
})])(BlockManager));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/manage-blocks-modal/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/**
 * Unique identifier for Manage Blocks modal.
 *
 * @type {string}
 */

var manage_blocks_modal_MODAL_NAME = 'edit-post/manage-blocks';
function ManageBlocksModal(_ref) {
  var isActive = _ref.isActive,
      closeModal = _ref.closeModal;

  if (!isActive) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Modal"], {
    className: "edit-post-manage-blocks-modal",
    title: Object(external_this_wp_i18n_["__"])('Block Manager'),
    closeLabel: Object(external_this_wp_i18n_["__"])('Close'),
    onRequestClose: closeModal
  }, Object(external_this_wp_element_["createElement"])(manager, null));
}
/* harmony default export */ var manage_blocks_modal = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/edit-post'),
      isModalActive = _select.isModalActive;

  return {
    isActive: isModalActive(manage_blocks_modal_MODAL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      closeModal = _dispatch.closeModal;

  return {
    closeModal: closeModal
  };
})])(ManageBlocksModal));

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
      onChange = _ref.onChange,
      children = _ref.children;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-options-modal__option"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
    label: label,
    checked: isChecked,
    onChange: onChange
  }), children);
}

/* harmony default export */ var base = (BaseOption);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-custom-fields.js



/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function CustomFieldsConfirmation(_ref) {
  var willEnable = _ref.willEnable;

  var _useState = Object(external_this_wp_element_["useState"])(false),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      isReloading = _useState2[0],
      setIsReloading = _useState2[1];

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-options-modal__custom-fields-confirmation-message"
  }, Object(external_this_wp_i18n_["__"])('A page reload is required for this change. Make sure your content is saved before reloading.')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    className: "edit-post-options-modal__custom-fields-confirmation-button",
    isDefault: true,
    isBusy: isReloading,
    disabled: isReloading,
    onClick: function onClick() {
      setIsReloading(true);
      document.getElementById('toggle-custom-fields-form').submit();
    }
  }, willEnable ? Object(external_this_wp_i18n_["__"])('Enable & Reload') : Object(external_this_wp_i18n_["__"])('Disable & Reload')));
}
function EnableCustomFieldsOption(_ref2) {
  var label = _ref2.label,
      areCustomFieldsEnabled = _ref2.areCustomFieldsEnabled;

  var _useState3 = Object(external_this_wp_element_["useState"])(areCustomFieldsEnabled),
      _useState4 = Object(slicedToArray["a" /* default */])(_useState3, 2),
      isChecked = _useState4[0],
      setIsChecked = _useState4[1];

  return Object(external_this_wp_element_["createElement"])(base, {
    label: label,
    isChecked: isChecked,
    onChange: setIsChecked
  }, isChecked !== areCustomFieldsEnabled && Object(external_this_wp_element_["createElement"])(CustomFieldsConfirmation, {
    willEnable: isChecked
  }));
}
/* harmony default export */ var enable_custom_fields = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    areCustomFieldsEnabled: !!select('core/editor').getEditorSettings().enableCustomFields
  };
})(EnableCustomFieldsOption));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-panel.js
/**
 * WordPress dependencies
 */


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

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-plugin-document-setting-panel.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var enable_plugin_document_setting_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('EnablePluginDocumentSettingPanelOption'),
    Fill = enable_plugin_document_setting_panel_createSlotFill.Fill,
    enable_plugin_document_setting_panel_Slot = enable_plugin_document_setting_panel_createSlotFill.Slot;

var enable_plugin_document_setting_panel_EnablePluginDocumentSettingPanelOption = function EnablePluginDocumentSettingPanelOption(_ref) {
  var label = _ref.label,
      panelName = _ref.panelName;
  return Object(external_this_wp_element_["createElement"])(Fill, null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: label,
    panelName: panelName
  }));
};

enable_plugin_document_setting_panel_EnablePluginDocumentSettingPanelOption.Slot = enable_plugin_document_setting_panel_Slot;
/* harmony default export */ var enable_plugin_document_setting_panel = (enable_plugin_document_setting_panel_EnablePluginDocumentSettingPanelOption);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-publish-sidebar.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


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

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-feature.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/* harmony default export */ var enable_feature = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var feature = _ref.feature;
  return {
    isChecked: select('core/edit-post').isFeatureActive(feature)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref2) {
  var feature = _ref2.feature;

  var _dispatch = dispatch('core/edit-post'),
      toggleFeature = _dispatch.toggleFeature;

  return {
    onChange: function onChange() {
      toggleFeature(feature);
    }
  };
}))(base));

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
  var thirdPartyMetaBoxes = Object(external_this_lodash_["filter"])(metaBoxes, function (_ref2) {
    var id = _ref2.id;
    return id !== 'postcustom';
  });

  if (!areCustomFieldsRegistered && thirdPartyMetaBoxes.length === 0) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(section, sectionProps, areCustomFieldsRegistered && Object(external_this_wp_element_["createElement"])(enable_custom_fields, {
    label: Object(external_this_wp_i18n_["__"])('Custom Fields')
  }), Object(external_this_lodash_["map"])(thirdPartyMetaBoxes, function (_ref3) {
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
    // This setting should not live in the block editor's store.
    areCustomFieldsRegistered: getEditorSettings().enableCustomFields !== undefined,
    metaBoxes: getAllMetaBoxes()
  };
})(MetaBoxesSection));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




var options_modal_MODAL_NAME = 'edit-post/options';
function OptionsModal(_ref) {
  var isModalActive = _ref.isModalActive,
      isViewable = _ref.isViewable,
      closeModal = _ref.closeModal;

  if (!isModalActive) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Modal"], {
    className: "edit-post-options-modal",
    title: Object(external_this_wp_i18n_["__"])('Options'),
    closeLabel: Object(external_this_wp_i18n_["__"])('Close'),
    onRequestClose: closeModal
  }, Object(external_this_wp_element_["createElement"])(section, {
    title: Object(external_this_wp_i18n_["__"])('General')
  }, Object(external_this_wp_element_["createElement"])(enable_publish_sidebar, {
    label: Object(external_this_wp_i18n_["__"])('Pre-publish Checks')
  }), Object(external_this_wp_element_["createElement"])(enable_feature, {
    feature: "showInserterHelpPanel",
    label: Object(external_this_wp_i18n_["__"])('Inserter Help Panel')
  })), Object(external_this_wp_element_["createElement"])(section, {
    title: Object(external_this_wp_i18n_["__"])('Document Panels')
  }, Object(external_this_wp_element_["createElement"])(enable_plugin_document_setting_panel.Slot, null), isViewable && Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Permalink'),
    panelName: "post-link"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTaxonomies"], {
    taxonomyWrapper: function taxonomyWrapper(content, taxonomy) {
      return Object(external_this_wp_element_["createElement"])(enable_panel, {
        label: Object(external_this_lodash_["get"])(taxonomy, ['labels', 'menu_name']),
        panelName: "taxonomy-panel-".concat(taxonomy.slug)
      });
    }
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFeaturedImageCheck"], null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Featured Image'),
    panelName: "featured-image"
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostExcerptCheck"], null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Excerpt'),
    panelName: "post-excerpt"
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: ['comments', 'trackbacks']
  }, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Discussion'),
    panelName: "discussion-panel"
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageAttributesCheck"], null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Page Attributes'),
    panelName: "page-attributes"
  }))), Object(external_this_wp_element_["createElement"])(meta_boxes_section, {
    title: Object(external_this_wp_i18n_["__"])('Advanced Panels')
  }));
}
/* harmony default export */ var options_modal = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  var postType = getPostType(getEditedPostAttribute('type'));
  return {
    isModalActive: select('core/edit-post').isModalActive(options_modal_MODAL_NAME),
    isViewable: Object(external_this_lodash_["get"])(postType, ['viewable'], false)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    closeModal: function closeModal() {
      return dispatch('core/edit-post').closeModal();
    }
  };
}))(OptionsModal));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-regions/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function EditorRegions(_ref) {
  var footer = _ref.footer,
      header = _ref.header,
      sidebar = _ref.sidebar,
      content = _ref.content,
      publish = _ref.publish,
      className = _ref.className;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: classnames_default()(className, 'edit-post-editor-regions')
  }, !!header && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-editor-regions__header",
    role: "region"
    /* translators: accessibility text for the top bar landmark region. */
    ,
    "aria-label": Object(external_this_wp_i18n_["__"])('Editor top bar'),
    tabIndex: "-1"
  }, header), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-editor-regions__body"
  }, Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-editor-regions__content",
    role: "region"
    /* translators: accessibility text for the content landmark region. */
    ,
    "aria-label": Object(external_this_wp_i18n_["__"])('Editor content'),
    tabIndex: "-1"
  }, content), !!publish && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-editor-regions__publish",
    role: "region"
    /* translators: accessibility text for the publish landmark region. */
    ,
    "aria-label": Object(external_this_wp_i18n_["__"])('Editor publish'),
    tabIndex: "-1"
  }, publish), !!sidebar && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-editor-regions__sidebar",
    role: "region",
    "aria-label": 'Editor settings',
    tabIndex: "-1"
  }, sidebar)), !!footer && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-editor-regions__footer",
    role: "region",
    "aria-label": 'Editor footer',
    tabIndex: "-1"
  }, footer));
}

/* harmony default export */ var editor_regions = (Object(external_this_wp_components_["navigateRegions"])(EditorRegions));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/fullscreen-mode/index.js






/**
 * WordPress dependencies
 */


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

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/browser-url/index.js






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
  return Object(external_this_wp_url_["addQueryArgs"])('post.php', {
    post: postId,
    action: 'edit'
  });
}
/**
 * Returns the Post's Trashed URL.
 *
 * @param {number} postId    Post ID.
 * @param {string} postType Post Type.
 *
 * @return {string} Post trashed URL.
 */

function getPostTrashedURL(postId, postType) {
  return Object(external_this_wp_url_["addQueryArgs"])('edit.php', {
    trashed: 1,
    post_type: postType,
    ids: postId
  });
}
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
          postType = _this$props.postType,
          isSavingPost = _this$props.isSavingPost;
      var historyId = this.state.historyId; // Posts are still dirty while saving so wait for saving to finish
      // to avoid the unsaved changes warning when trashing posts.

      if (postStatus === 'trash' && !isSavingPost) {
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
      getCurrentPost = _select.getCurrentPost,
      isSavingPost = _select.isSavingPost;

  var _getCurrentPost = getCurrentPost(),
      id = _getCurrentPost.id,
      status = _getCurrentPost.status,
      type = _getCurrentPost.type;

  return {
    postId: id,
    postStatus: status,
    postType: type,
    isSavingPost: isSavingPost()
  };
})(browser_url_BrowserURL));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/fullscreen-mode-close/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






function FullscreenModeClose(_ref) {
  var isActive = _ref.isActive,
      postType = _ref.postType;

  if (!isActive || !postType) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
    className: "edit-post-fullscreen-mode-close__toolbar"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: "arrow-left-alt2",
    href: Object(external_this_wp_url_["addQueryArgs"])('edit.php', {
      post_type: postType.slug
    }),
    label: Object(external_this_lodash_["get"])(postType, ['labels', 'view_items'], Object(external_this_wp_i18n_["__"])('Back'))
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


/**
 * WordPress dependencies
 */






function HeaderToolbar() {
  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
      // This setting (richEditingEnabled) should not live in the block editor's setting.
      showInserter: select('core/edit-post').getEditorMode() === 'visual' && select('core/editor').getEditorSettings().richEditingEnabled,
      isTextModeEnabled: select('core/edit-post').getEditorMode() === 'text'
    };
  }),
      hasFixedToolbar = _useSelect.hasFixedToolbar,
      showInserter = _useSelect.showInserter,
      isTextModeEnabled = _useSelect.isTextModeEnabled;

  var isLargeViewport = Object(external_this_wp_compose_["useViewportMatch"])('medium');
  var toolbarAriaLabel = hasFixedToolbar ?
  /* translators: accessibility text for the editor toolbar when Top Toolbar is on */
  Object(external_this_wp_i18n_["__"])('Document and block tools') :
  /* translators: accessibility text for the editor toolbar when Top Toolbar is off */
  Object(external_this_wp_i18n_["__"])('Document tools');
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["NavigableToolbar"], {
    className: "edit-post-header-toolbar",
    "aria-label": toolbarAriaLabel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["Inserter"], {
    disabled: !showInserter,
    position: "bottom right",
    showInserterHelpPanel: true
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorHistoryUndo"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorHistoryRedo"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["TableOfContents"], {
    hasOutlineItemsDisabled: isTextModeEnabled
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockNavigationDropdown"], {
    isDisabled: isTextModeEnabled
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["ToolSelector"], null), (hasFixedToolbar || !isLargeViewport) && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-header-toolbar__block-toolbar"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockToolbar"], null)));
}

/* harmony default export */ var header_toolbar = (HeaderToolbar);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/mode-switcher/index.js



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
    isCodeEditingEnabled: select('core/editor').getEditorSettings().codeEditingEnabled,
    mode: select('core/edit-post').getEditorMode()
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isRichEditingEnabled = _ref2.isRichEditingEnabled,
      isCodeEditingEnabled = _ref2.isCodeEditingEnabled;
  return isRichEditingEnabled && isCodeEditingEnabled;
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onSwitch: function onSwitch(mode) {
      dispatch('core/edit-post').switchEditorMode(mode);
    }
  };
})])(ModeSwitcher));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugins-more-menu-group/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




var plugins_more_menu_group_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginsMoreMenuGroup'),
    PluginsMoreMenuGroup = plugins_more_menu_group_createSlotFill.Fill,
    plugins_more_menu_group_Slot = plugins_more_menu_group_createSlotFill.Slot;

PluginsMoreMenuGroup.Slot = function (_ref) {
  var fillProps = _ref.fillProps;
  return Object(external_this_wp_element_["createElement"])(plugins_more_menu_group_Slot, {
    fillProps: fillProps
  }, function (fills) {
    return !Object(external_this_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
      label: Object(external_this_wp_i18n_["__"])('Plugins')
    }, fills);
  });
};

/* harmony default export */ var plugins_more_menu_group = (PluginsMoreMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/options-menu-item/index.js


/**
 * WordPress dependencies
 */



function OptionsMenuItem(_ref) {
  var openModal = _ref.openModal;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
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
 * External dependencies
 */

/**
 * WordPress dependencies
 */






function FeatureToggle(_ref) {
  var onToggle = _ref.onToggle,
      isActive = _ref.isActive,
      label = _ref.label,
      info = _ref.info,
      messageActivated = _ref.messageActivated,
      messageDeactivated = _ref.messageDeactivated,
      speak = _ref.speak;

  var speakMessage = function speakMessage() {
    if (isActive) {
      speak(messageDeactivated || Object(external_this_wp_i18n_["__"])('Feature deactivated'));
    } else {
      speak(messageActivated || Object(external_this_wp_i18n_["__"])('Feature activated'));
    }
  };

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    icon: isActive && 'yes',
    isSelected: isActive,
    onClick: Object(external_this_lodash_["flow"])(onToggle, speakMessage),
    role: "menuitemcheckbox",
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
    }
  };
}), external_this_wp_components_["withSpokenMessages"]])(FeatureToggle));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/writing-menu/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function WritingMenu() {
  var isLargeViewport = Object(external_this_wp_compose_["useViewportMatch"])('medium');

  if (!isLargeViewport) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
    label: Object(external_this_wp_i18n_["_x"])('View', 'noun')
  }, Object(external_this_wp_element_["createElement"])(feature_toggle, {
    feature: "fixedToolbar",
    label: Object(external_this_wp_i18n_["__"])('Top Toolbar'),
    info: Object(external_this_wp_i18n_["__"])('Access all block and document tools in a single place'),
    messageActivated: Object(external_this_wp_i18n_["__"])('Top toolbar activated'),
    messageDeactivated: Object(external_this_wp_i18n_["__"])('Top toolbar deactivated')
  }), Object(external_this_wp_element_["createElement"])(feature_toggle, {
    feature: "focusMode",
    label: Object(external_this_wp_i18n_["__"])('Spotlight Mode'),
    info: Object(external_this_wp_i18n_["__"])('Focus on one block at a time'),
    messageActivated: Object(external_this_wp_i18n_["__"])('Spotlight mode activated'),
    messageDeactivated: Object(external_this_wp_i18n_["__"])('Spotlight mode deactivated')
  }), Object(external_this_wp_element_["createElement"])(feature_toggle, {
    feature: "fullscreenMode",
    label: Object(external_this_wp_i18n_["__"])('Fullscreen Mode'),
    info: Object(external_this_wp_i18n_["__"])('Work without distraction'),
    messageActivated: Object(external_this_wp_i18n_["__"])('Fullscreen mode activated'),
    messageDeactivated: Object(external_this_wp_i18n_["__"])('Fullscreen mode deactivated')
  }));
}

/* harmony default export */ var writing_menu = (WritingMenu);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/more-menu/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */






var POPOVER_PROPS = {
  className: 'edit-post-more-menu__content',
  position: 'bottom left'
};
var TOGGLE_PROPS = {
  labelPosition: 'bottom'
};

var more_menu_MoreMenu = function MoreMenu() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["DropdownMenu"], {
    className: "edit-post-more-menu",
    icon: "ellipsis",
    label: Object(external_this_wp_i18n_["__"])('More tools & options'),
    popoverProps: POPOVER_PROPS,
    toggleProps: TOGGLE_PROPS
  }, function (_ref) {
    var onClose = _ref.onClose;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(writing_menu, null), Object(external_this_wp_element_["createElement"])(mode_switcher, null), Object(external_this_wp_element_["createElement"])(plugins_more_menu_group.Slot, {
      fillProps: {
        onClose: onClose
      }
    }), Object(external_this_wp_element_["createElement"])(tools_more_menu_group.Slot, {
      fillProps: {
        onClose: onClose
      }
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], null, Object(external_this_wp_element_["createElement"])(options_menu_item, null)));
  });
};

/* harmony default export */ var more_menu = (more_menu_MoreMenu);

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
    return !Object(external_this_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-pinned-plugins"
    }, fills);
  });
};

/* harmony default export */ var pinned_plugins = (PinnedPlugins);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/post-publish-button-or-toggle.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function PostPublishButtonOrToggle(_ref) {
  var forceIsDirty = _ref.forceIsDirty,
      forceIsSaving = _ref.forceIsSaving,
      hasPublishAction = _ref.hasPublishAction,
      isBeingScheduled = _ref.isBeingScheduled,
      isPending = _ref.isPending,
      isPublished = _ref.isPublished,
      isPublishSidebarEnabled = _ref.isPublishSidebarEnabled,
      isPublishSidebarOpened = _ref.isPublishSidebarOpened,
      isScheduled = _ref.isScheduled,
      togglePublishSidebar = _ref.togglePublishSidebar;
  var IS_TOGGLE = 'toggle';
  var IS_BUTTON = 'button';
  var isSmallerThanMediumViewport = Object(external_this_wp_compose_["useViewportMatch"])('medium', '<');
  var component;
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

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPublishButton"], {
    forceIsDirty: forceIsDirty,
    forceIsSaving: forceIsSaving,
    isOpen: isPublishSidebarOpened,
    isToggle: component === IS_TOGGLE,
    onToggle: togglePublishSidebar
  });
}
/* harmony default export */ var post_publish_button_or_toggle = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    hasPublishAction: Object(external_this_lodash_["get"])(select('core/editor').getCurrentPost(), ['_links', 'wp:action-publish'], false),
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
}))(PostPublishButtonOrToggle));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */








function Header(_ref) {
  var closeGeneralSidebar = _ref.closeGeneralSidebar,
      hasActiveMetaboxes = _ref.hasActiveMetaboxes,
      isEditorSidebarOpened = _ref.isEditorSidebarOpened,
      isPublishSidebarOpened = _ref.isPublishSidebarOpened,
      isSaving = _ref.isSaving,
      openGeneralSidebar = _ref.openGeneralSidebar;
  var toggleGeneralSidebar = isEditorSidebarOpened ? closeGeneralSidebar : openGeneralSidebar;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-header"
  }, Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-header__toolbar"
  }, Object(external_this_wp_element_["createElement"])(fullscreen_mode_close, null), Object(external_this_wp_element_["createElement"])(header_toolbar, null)), Object(external_this_wp_element_["createElement"])("div", {
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
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: "admin-generic",
    label: Object(external_this_wp_i18n_["__"])('Settings'),
    onClick: toggleGeneralSidebar,
    isToggled: isEditorSidebarOpened,
    "aria-expanded": isEditorSidebarOpened,
    shortcut: keyboard_shortcuts.toggleSidebar
  }), Object(external_this_wp_element_["createElement"])(pinned_plugins.Slot, null), Object(external_this_wp_element_["createElement"])(more_menu, null)));
}

/* harmony default export */ var components_header = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    hasActiveMetaboxes: select('core/edit-post').hasMetaBoxes(),
    isEditorSidebarOpened: select('core/edit-post').isEditorSidebarOpened(),
    isPublishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
    isSaving: select('core/edit-post').isSavingMetaBoxes()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps, _ref2) {
  var select = _ref2.select;

  var _select = select('core/block-editor'),
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

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





var sidebar_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('Sidebar'),
    sidebar_Fill = sidebar_createSlotFill.Fill,
    sidebar_Slot = sidebar_createSlotFill.Slot;
/**
 * Renders a sidebar with its content.
 *
 * @return {Object} The rendered sidebar.
 */


function Sidebar(_ref) {
  var children = _ref.children,
      className = _ref.className;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: classnames_default()('edit-post-sidebar', className)
  }, children);
}

Sidebar = Object(external_this_wp_components_["withFocusReturn"])({
  onFocusReturn: function onFocusReturn() {
    var button = document.querySelector('.edit-post-header__settings [aria-label="Settings"]');

    if (button) {
      button.focus();
      return false;
    }
  }
})(Sidebar);

function AnimatedSidebarFill(props) {
  return Object(external_this_wp_element_["createElement"])(sidebar_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Animate"], {
    type: "slide-in",
    options: {
      origin: 'left'
    }
  }, function () {
    return Object(external_this_wp_element_["createElement"])(Sidebar, props);
  }));
}

var WrappedSidebar = Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var name = _ref2.name;
  return {
    isActive: select('core/edit-post').getActiveGeneralSidebarName() === name
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref3) {
  var isActive = _ref3.isActive;
  return isActive;
}))(AnimatedSidebarFill);
WrappedSidebar.Slot = sidebar_Slot;
/* harmony default export */ var components_sidebar = (WrappedSidebar);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/sidebar-header/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



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

  var _ref4 = sidebarName === 'edit-post/block' ? // translators: ARIA label for the Settings Sidebar tab, selected.
  [Object(external_this_wp_i18n_["__"])('Block (selected)'), 'is-active'] : // translators: ARIA label for the Settings Sidebar tab, not selected.
  [Object(external_this_wp_i18n_["__"])('Block'), ''],
      _ref5 = Object(slicedToArray["a" /* default */])(_ref4, 2),
      blockAriaLabel = _ref5[0],
      blockActiveClass = _ref5[1];

  return Object(external_this_wp_element_["createElement"])(sidebar_header, {
    className: "edit-post-sidebar__panel-tabs",
    closeLabel: Object(external_this_wp_i18n_["__"])('Close settings')
  }, Object(external_this_wp_element_["createElement"])("ul", null, Object(external_this_wp_element_["createElement"])("li", null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    onClick: openDocumentSettings,
    className: "edit-post-sidebar__panel-tab ".concat(documentActiveClass),
    "aria-label": documentAriaLabel,
    "data-label": Object(external_this_wp_i18n_["__"])('Document')
  }, Object(external_this_wp_i18n_["__"])('Document'))), Object(external_this_wp_element_["createElement"])("li", null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    onClick: openBlockSettings,
    className: "edit-post-sidebar__panel-tab ".concat(blockActiveClass),
    "aria-label": blockAriaLabel,
    "data-label": blockLabel
  }, blockLabel))));
};

/* harmony default export */ var settings_header = (Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openGeneralSidebar = _dispatch.openGeneralSidebar;

  return {
    openDocumentSettings: function openDocumentSettings() {
      openGeneralSidebar('edit-post/document');
    },
    openBlockSettings: function openBlockSettings() {
      openGeneralSidebar('edit-post/block');
    }
  };
})(settings_header_SettingsHeader));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-visibility/index.js


/**
 * WordPress dependencies
 */



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


/**
 * WordPress dependencies
 */


function PostTrash() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTrashCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTrash"], null)));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-schedule/index.js


/**
 * WordPress dependencies
 */



function PostSchedule() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostScheduleCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], {
    className: "edit-post-post-schedule"
  }, Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_i18n_["__"])('Publish')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dropdown"], {
    position: "bottom left",
    contentClassName: "edit-post-post-schedule__dialog",
    renderToggle: function renderToggle(_ref) {
      var onToggle = _ref.onToggle,
          isOpen = _ref.isOpen;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: "edit-post-post-schedule__toggle",
        onClick: onToggle,
        "aria-expanded": isOpen,
        isLink: true
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostScheduleLabel"], null)));
    },
    renderContent: function renderContent() {
      return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSchedule"], null);
    }
  })));
}
/* harmony default export */ var post_schedule = (PostSchedule);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-sticky/index.js


/**
 * WordPress dependencies
 */


function PostSticky() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostStickyCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSticky"], null)));
}
/* harmony default export */ var post_sticky = (PostSticky);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-author/index.js


/**
 * WordPress dependencies
 */


function PostAuthor() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostAuthorCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostAuthor"], null)));
}
/* harmony default export */ var post_author = (PostAuthor);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-slug/index.js


/**
 * WordPress dependencies
 */


function PostSlug() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSlugCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSlug"], null)));
}
/* harmony default export */ var post_slug = (PostSlug);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-format/index.js


/**
 * WordPress dependencies
 */


function PostFormat() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFormatCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFormat"], null)));
}
/* harmony default export */ var post_format = (PostFormat);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-pending-status/index.js


/**
 * WordPress dependencies
 */


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
/**
 * Renders a row in the Status & Visibility panel of the Document sidebar.
 * It should be noted that this is named and implemented around the function it serves
 * and not its location, which may change in future iterations.
 *
 * @param {Object} props Component properties.
 * @param {string} [props.className] An optional class name added to the row.
 *
 * @example <caption>ES5</caption>
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
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { __ } = wp.i18n;
 * const { PluginPostStatusInfo } = wp.editPost;
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


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */










/**
 * Module Constants
 */

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
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(post_visibility, null), Object(external_this_wp_element_["createElement"])(post_schedule, null), Object(external_this_wp_element_["createElement"])(post_format, null), Object(external_this_wp_element_["createElement"])(post_sticky, null), Object(external_this_wp_element_["createElement"])(post_pending_status, null), Object(external_this_wp_element_["createElement"])(post_slug, null), Object(external_this_wp_element_["createElement"])(post_author, null), fills, Object(external_this_wp_element_["createElement"])(PostTrash, null));
  }));
}

/* harmony default export */ var post_status = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  // We use isEditorPanelRemoved to hide the panel if it was programatically removed. We do
  // not use isEditorPanelEnabled since this panel should not be disabled through the UI.
  var _select = select('core/edit-post'),
      isEditorPanelRemoved = _select.isEditorPanelRemoved,
      isEditorPanelOpened = _select.isEditorPanelOpened;

  return {
    isRemoved: isEditorPanelRemoved(PANEL_NAME),
    isOpened: isEditorPanelOpened(PANEL_NAME)
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isRemoved = _ref2.isRemoved;
  return !isRemoved;
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleEditorPanelOpened(PANEL_NAME);
    }
  };
})])(PostStatus));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/last-revision/index.js


/**
 * WordPress dependencies
 */



function LastRevision() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLastRevisionCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: "edit-post-last-revision__panel"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLastRevision"], null)));
}

/* harmony default export */ var last_revision = (LastRevision);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js


/**
 * External dependencies
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

  var taxonomyMenuName = Object(external_this_lodash_["get"])(taxonomy, ['labels', 'menu_name']);

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
  var slug = Object(external_this_lodash_["get"])(ownProps.taxonomy, ['slug']);
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


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



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
 */






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
    title: Object(external_this_lodash_["get"])(postType, ['labels', 'featured_image'], Object(external_this_wp_i18n_["__"])('Featured Image')),
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
    onTogglePanel: Object(external_this_lodash_["partial"])(toggleEditorPanelOpened, featured_image_PANEL_NAME)
  };
});
/* harmony default export */ var featured_image = (Object(external_this_wp_compose_["compose"])(applyWithSelect, applyWithDispatch)(FeaturedImage));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-excerpt/index.js


/**
 * WordPress dependencies
 */





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
 */

/**
 * WordPress dependencies
 */







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
      postID = _ref.postID,
      postTypeLabel = _ref.postTypeLabel;
  var prefix = permalinkParts.prefix,
      suffix = permalinkParts.suffix;
  var prefixElement, postNameElement, suffixElement;
  var currentSlug = Object(external_this_wp_url_["safeDecodeURIComponent"])(postSlug) || Object(external_this_wp_editor_["cleanForSlug"])(postTitle) || postID;

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
  }, isEditable && Object(external_this_wp_element_["createElement"])("div", {
    className: "editor-post-link"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
    label: Object(external_this_wp_i18n_["__"])('URL Slug'),
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
  }), Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('The last part of the URL.'), ' ', Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
    href: "https://wordpress.org/support/article/writing-posts/#post-field-descriptions"
  }, Object(external_this_wp_i18n_["__"])('Read about permalinks')))), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-post-link__preview-label"
  }, postTypeLabel || Object(external_this_wp_i18n_["__"])('View Post')), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-post-link__preview-link-container"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
    className: "edit-post-post-link__link",
    href: postLink,
    target: "_blank"
  }, isEditable ? Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, prefixElement, postNameElement, suffixElement) : postLink)));
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
    isViewable: Object(external_this_lodash_["get"])(postType, ['viewable'], false),
    postTitle: getEditedPostAttribute('title'),
    postSlug: getEditedPostAttribute('slug'),
    postID: id,
    postTypeLabel: Object(external_this_lodash_["get"])(postType, ['labels', 'view_item'])
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


/**
 * WordPress dependencies
 */





/**
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
 * WordPress dependencies
 */






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
    title: Object(external_this_lodash_["get"])(postType, ['labels', 'attributes'], Object(external_this_wp_i18n_["__"])('Page Attributes')),
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
    onTogglePanel: Object(external_this_lodash_["partial"])(toggleEditorPanelOpened, page_attributes_PANEL_NAME)
  };
});
/* harmony default export */ var page_attributes = (Object(external_this_wp_compose_["compose"])(page_attributes_applyWithSelect, page_attributes_applyWithDispatch)(PageAttributes));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





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
    _this.bindContainerNode = _this.bindContainerNode.bind(Object(assertThisInitialized["a" /* default */])(_this));
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






/**
 * WordPress dependencies
 */



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

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function MetaBoxes(_ref) {
  var location = _ref.location,
      isVisible = _ref.isVisible,
      metaBoxes = _ref.metaBoxes;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_lodash_["map"])(metaBoxes, function (_ref2) {
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

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-document-setting-panel/index.js


/**
 * Defines as extensibility slot for the Settings sidebar
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var plugin_document_setting_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginDocumentSettingPanel'),
    plugin_document_setting_panel_Fill = plugin_document_setting_panel_createSlotFill.Fill,
    plugin_document_setting_panel_Slot = plugin_document_setting_panel_createSlotFill.Slot;



var plugin_document_setting_panel_PluginDocumentSettingFill = function PluginDocumentSettingFill(_ref) {
  var isEnabled = _ref.isEnabled,
      panelName = _ref.panelName,
      opened = _ref.opened,
      onToggle = _ref.onToggle,
      className = _ref.className,
      title = _ref.title,
      icon = _ref.icon,
      children = _ref.children;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(enable_plugin_document_setting_panel, {
    label: title,
    panelName: panelName
  }), Object(external_this_wp_element_["createElement"])(plugin_document_setting_panel_Fill, null, isEnabled && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
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
 * @param {Object} props Component properties.
 * @param {string} [props.name] The machine-friendly name for the panel.
 * @param {string} [props.className] An optional class name added to the row.
 * @param {string} [props.title] The title of the panel
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example <caption>ES5</caption>
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
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { registerPlugin } = wp.plugins;
 * const { PluginDocumentSettingPanel } = wp.editPost;
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


var PluginDocumentSettingPanel = Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon,
    panelName: "".concat(context.name, "/").concat(ownProps.name)
  };
}), Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var panelName = _ref2.panelName;
  return {
    opened: select('core/edit-post').isEditorPanelOpened(panelName),
    isEnabled: select('core/edit-post').isEditorPanelEnabled(panelName)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3) {
  var panelName = _ref3.panelName;
  return {
    onToggle: function onToggle() {
      return dispatch('core/edit-post').toggleEditorPanelOpened(panelName);
    }
  };
}))(plugin_document_setting_panel_PluginDocumentSettingFill);
PluginDocumentSettingPanel.Slot = plugin_document_setting_panel_Slot;
/* harmony default export */ var plugin_document_setting_panel = (PluginDocumentSettingPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-sidebar/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */














var settings_sidebar_SettingsSidebar = function SettingsSidebar(_ref) {
  var sidebarName = _ref.sidebarName;
  return Object(external_this_wp_element_["createElement"])(components_sidebar, {
    name: sidebarName
  }, Object(external_this_wp_element_["createElement"])(settings_header, {
    sidebarName: sidebarName
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Panel"], null, sidebarName === 'edit-post/document' && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(post_status, null), Object(external_this_wp_element_["createElement"])(plugin_document_setting_panel.Slot, null), Object(external_this_wp_element_["createElement"])(last_revision, null), Object(external_this_wp_element_["createElement"])(post_link, null), Object(external_this_wp_element_["createElement"])(post_taxonomies, null), Object(external_this_wp_element_["createElement"])(featured_image, null), Object(external_this_wp_element_["createElement"])(post_excerpt, null), Object(external_this_wp_element_["createElement"])(discussion_panel, null), Object(external_this_wp_element_["createElement"])(page_attributes, null), Object(external_this_wp_element_["createElement"])(meta_boxes, {
    location: "side"
  })), sidebarName === 'edit-post/block' && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockInspector"], null)));
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


/**
 * WordPress dependencies
 */




var plugin_post_publish_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginPostPublishPanel'),
    plugin_post_publish_panel_Fill = plugin_post_publish_panel_createSlotFill.Fill,
    plugin_post_publish_panel_Slot = plugin_post_publish_panel_createSlotFill.Slot;

var plugin_post_publish_panel_PluginPostPublishPanelFill = function PluginPostPublishPanelFill(_ref) {
  var children = _ref.children,
      className = _ref.className,
      title = _ref.title,
      _ref$initialOpen = _ref.initialOpen,
      initialOpen = _ref$initialOpen === void 0 ? false : _ref$initialOpen,
      icon = _ref.icon;
  return Object(external_this_wp_element_["createElement"])(plugin_post_publish_panel_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
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
 * @param {Object} props Component properties.
 * @param {string} [props.className] An optional class name added to the panel.
 * @param {string} [props.title] Title displayed at the top of the panel.
 * @param {boolean} [props.initialOpen=false] Whether to have the panel initially opened. When no title is provided it is always opened.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example <caption>ES5</caption>
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
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { __ } = wp.i18n;
 * const { PluginPostPublishPanel } = wp.editPost;
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


var PluginPostPublishPanel = Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon
  };
}))(plugin_post_publish_panel_PluginPostPublishPanelFill);
PluginPostPublishPanel.Slot = plugin_post_publish_panel_Slot;
/* harmony default export */ var plugin_post_publish_panel = (PluginPostPublishPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js


/**
 * WordPress dependencies
 */




var plugin_pre_publish_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginPrePublishPanel'),
    plugin_pre_publish_panel_Fill = plugin_pre_publish_panel_createSlotFill.Fill,
    plugin_pre_publish_panel_Slot = plugin_pre_publish_panel_createSlotFill.Slot;

var plugin_pre_publish_panel_PluginPrePublishPanelFill = function PluginPrePublishPanelFill(_ref) {
  var children = _ref.children,
      className = _ref.className,
      title = _ref.title,
      _ref$initialOpen = _ref.initialOpen,
      initialOpen = _ref$initialOpen === void 0 ? false : _ref$initialOpen,
      icon = _ref.icon;
  return Object(external_this_wp_element_["createElement"])(plugin_pre_publish_panel_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
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
 * @example <caption>ES5</caption>
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
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { __ } = wp.i18n;
 * const { PluginPrePublishPanel } = wp.editPost;
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


var PluginPrePublishPanel = Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon
  };
}))(plugin_pre_publish_panel_PluginPrePublishPanelFill);
PluginPrePublishPanel.Slot = plugin_pre_publish_panel_Slot;
/* harmony default export */ var plugin_pre_publish_panel = (PluginPrePublishPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/images.js



/**
 * WordPress dependencies
 */

var images_CanvasImage = function CanvasImage(props) {
  return Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
    alt: "",
    src: "data:image/svg+xml,%3Csvg width='306' height='286' viewBox='0 0 306 286' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='306' height='286' rx='4' fill='%2366C6E4'/%3E%3Crect x='36' y='30' width='234' height='256' fill='white'/%3E%3Crect x='36' y='80' width='234' height='94' fill='%23E2E4E7'/%3E%3Cpath d='M140.237 121.47L142.109 125H157.255V133H140.237V121.47ZM159.382 119H155.128L157.255 123H154.064L151.937 119H149.809L151.937 123H148.746L146.618 119H144.491L146.618 123H143.428L141.3 119H140.237C139.067 119 138.12 119.9 138.12 121L138.109 133C138.109 134.1 139.067 135 140.237 135H157.255C158.425 135 159.382 134.1 159.382 133V119Z' fill='%23444444'/%3E%3Crect x='57' y='182' width='91.4727' height='59' fill='%23E2E4E7'/%3E%3Crect x='156.982' y='182' width='91.4727' height='59' fill='%23E2E4E7'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M112.309 203H93.1634C92.0998 203 91.0361 204 91.0361 205V219C91.0361 220.1 91.9934 221 93.1634 221H112.309C113.372 221 114.436 220 114.436 219V205C114.436 204 113.372 203 112.309 203ZM112.309 218.92C112.294 218.941 112.269 218.962 112.248 218.979L112.248 218.979C112.239 218.987 112.23 218.994 112.224 219H93.1634V205.08L93.2485 205H112.213C112.235 205.014 112.258 205.038 112.276 205.057C112.284 205.066 112.292 205.074 112.298 205.08V218.92H112.309ZM99.0134 212.5L101.672 215.51L105.395 211L110.182 217H95.2907L99.0134 212.5Z' fill='%2340464D'/%3E%3Cmask id='mask0' mask-type='alpha' maskUnits='userSpaceOnUse' x='91' y='203' width='24' height='18'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M112.309 203H93.1634C92.0998 203 91.0361 204 91.0361 205V219C91.0361 220.1 91.9934 221 93.1634 221H112.309C113.372 221 114.436 220 114.436 219V205C114.436 204 113.372 203 112.309 203ZM112.309 218.92C112.294 218.941 112.269 218.962 112.248 218.979L112.248 218.979C112.239 218.987 112.23 218.994 112.224 219H93.1634V205.08L93.2485 205H112.213C112.235 205.014 112.258 205.038 112.276 205.057C112.284 205.066 112.292 205.074 112.298 205.08V218.92H112.309ZM99.0134 212.5L101.672 215.51L105.395 211L110.182 217H95.2907L99.0134 212.5Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask0)'%3E%3Crect x='89.9727' y='200' width='25.5273' height='24' fill='%2340464D'/%3E%3C/g%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M212.291 203H193.145C192.082 203 191.018 204 191.018 205V219C191.018 220.1 191.975 221 193.145 221H212.291C213.354 221 214.418 220 214.418 219V205C214.418 204 213.354 203 212.291 203ZM212.291 218.92C212.276 218.941 212.251 218.962 212.23 218.979L212.23 218.979C212.221 218.987 212.212 218.994 212.206 219H193.145V205.08L193.23 205H212.195C212.217 205.014 212.24 205.038 212.258 205.057C212.266 205.066 212.274 205.074 212.28 205.08V218.92H212.291ZM198.995 212.5L201.654 215.51L205.377 211L210.164 217H195.273L198.995 212.5Z' fill='%2340464D'/%3E%3Cmask id='mask1' mask-type='alpha' maskUnits='userSpaceOnUse' x='191' y='203' width='24' height='18'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M212.291 203H193.145C192.082 203 191.018 204 191.018 205V219C191.018 220.1 191.975 221 193.145 221H212.291C213.354 221 214.418 220 214.418 219V205C214.418 204 213.354 203 212.291 203ZM212.291 218.92C212.276 218.941 212.251 218.962 212.23 218.979L212.23 218.979C212.221 218.987 212.212 218.994 212.206 219H193.145V205.08L193.23 205H212.195C212.217 205.014 212.24 205.038 212.258 205.057C212.266 205.066 212.274 205.074 212.28 205.08V218.92H212.291ZM198.995 212.5L201.654 215.51L205.377 211L210.164 217H195.273L198.995 212.5Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask1)'%3E%3Crect x='189.955' y='200' width='25.5273' height='24' fill='%2340464D'/%3E%3C/g%3E%3Crect x='57' y='38' width='191.455' height='34' fill='%23E2E4E7'/%3E%3Cpath d='M155.918 47.8V54.04H149.537V47.8H146.346V63.4H149.537V57.16H155.918V63.4H159.109V47.8' fill='%2340464D'/%3E%3Crect x='58' y='249' width='191' height='37' fill='%23E2E4E7'/%3E%3Cpath d='M160.127 261.4H150.606C149.546 261.4 148.576 261.64 147.696 262.12C146.802 262.612 146.1 263.272 145.59 264.1C145.066 264.928 144.811 265.84 144.811 266.824C144.811 267.808 145.066 268.72 145.59 269.548C146.1 270.376 146.802 271.036 147.696 271.516C148.576 272.008 149.546 272.248 150.606 272.248H151.155V279.4C151.155 279.724 151.282 280.012 151.525 280.252C151.78 280.48 152.086 280.6 152.431 280.6C152.788 280.6 153.082 280.48 153.337 280.252C153.592 280.012 153.72 279.724 153.72 279.4V265C153.72 264.676 153.835 264.388 154.09 264.148C154.345 263.92 154.652 263.8 154.996 263.8C155.341 263.8 155.647 263.92 155.903 264.148C156.145 264.388 156.273 264.676 156.273 265V279.4C156.273 279.724 156.4 280.012 156.656 280.252C156.911 280.48 157.205 280.6 157.562 280.6C157.907 280.6 158.213 280.48 158.468 280.252C158.711 280.012 158.838 279.724 158.838 279.4V263.8H160.127C160.472 263.8 160.766 263.68 161.021 263.44C161.276 263.212 161.404 262.924 161.404 262.6C161.404 262.276 161.276 261.988 161.021 261.748C160.766 261.52 160.472 261.4 160.127 261.4Z' fill='%2340464D'/%3E%3C/svg%3E%0A"
  }, props));
};
var images_EditorImage = function EditorImage(props) {
  return Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
    alt: "",
    src: "data:image/svg+xml,%3Csvg width='306' height='286' viewBox='0 0 306 286' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='306' height='286' rx='4' fill='%2366C6E4'/%3E%3Crect x='34.5' y='89.9424' width='237' height='113.423' fill='white' stroke='%238D96A0'/%3E%3Crect x='42.2383' y='98.5962' width='219.692' height='95.6618' fill='%23E2E4E7'/%3E%3Crect x='34.5' y='71.6346' width='27.0718' height='18.1324' fill='white' stroke='%238D96A0'/%3E%3Crect x='152.89' y='71.6346' width='18.5282' height='18.1324' fill='white' stroke='%238D96A0'/%3E%3Crect x='61.3516' y='71.6346' width='51.482' height='18.1324' fill='white' stroke='%238D96A0'/%3E%3Crect x='112.613' y='71.6346' width='40.4974' height='18.1324' fill='white' stroke='%238D96A0'/%3E%3Cpath d='M157.577 137.408H149.383C148.471 137.408 147.636 137.628 146.878 138.068C146.109 138.518 145.505 139.122 145.066 139.88C144.615 140.638 144.396 141.473 144.396 142.373C144.396 143.274 144.615 144.109 145.066 144.867C145.505 145.625 146.109 146.229 146.878 146.668C147.636 147.119 148.471 147.339 149.383 147.339H149.855V153.885C149.855 154.182 149.965 154.446 150.173 154.665C150.393 154.874 150.657 154.984 150.953 154.984C151.261 154.984 151.514 154.874 151.733 154.665C151.953 154.446 152.063 154.182 152.063 153.885V140.704C152.063 140.407 152.162 140.144 152.381 139.924C152.601 139.715 152.865 139.605 153.161 139.605C153.458 139.605 153.721 139.715 153.941 139.924C154.15 140.144 154.26 140.407 154.26 140.704V153.885C154.26 154.182 154.37 154.446 154.589 154.665C154.809 154.874 155.062 154.984 155.369 154.984C155.666 154.984 155.929 154.874 156.149 154.665C156.358 154.446 156.468 154.182 156.468 153.885V139.605H157.577C157.874 139.605 158.126 139.496 158.346 139.276C158.566 139.067 158.676 138.803 158.676 138.507C158.676 138.21 158.566 137.947 158.346 137.727C158.126 137.518 157.874 137.408 157.577 137.408Z' fill='%2340464D'/%3E%3Crect x='41.3232' y='77.1135' width='15.8667' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='66.9536' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='77.9385' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='88.9229' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='99.9077' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='118.215' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='129.2' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='140.185' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='158.492' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3C/svg%3E%0A"
  }, props));
};
var images_BlockLibraryImage = function BlockLibraryImage(props) {
  return Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
    alt: "",
    src: "data:image/svg+xml,%3Csvg width='306' height='286' viewBox='0 0 306 286' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='306' height='286' rx='4' fill='%2366C6E4'/%3E%3Cmask id='mask0' mask-type='alpha' maskUnits='userSpaceOnUse' x='141' y='25' width='24' height='24'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M152.765 25C146.294 25 141 30.2943 141 36.7651C141 43.2359 146.294 48.5302 152.765 48.5302C159.236 48.5302 164.53 43.2359 164.53 36.7651C164.53 30.2943 159.236 25 152.765 25ZM151.589 32.0591V35.5886H148.059V37.9416H151.589V41.4711H153.942V37.9416H157.471V35.5886H153.942V32.0591H151.589ZM143.353 36.7651C143.353 41.9417 147.588 46.1772 152.765 46.1772C157.942 46.1772 162.177 41.9417 162.177 36.7651C162.177 31.5885 157.942 27.353 152.765 27.353C147.588 27.353 143.353 31.5885 143.353 36.7651Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask0)'%3E%3Crect x='141' y='25' width='23.5253' height='23.5253' fill='white'/%3E%3C/g%3E%3Cg filter='url(%23filter0_d)'%3E%3Crect x='48' y='63' width='210' height='190' fill='white'/%3E%3C/g%3E%3Cmask id='mask1' mask-type='alpha' maskUnits='userSpaceOnUse' x='143' y='139' width='20' height='16'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M143.75 141C143.75 140.17 144.42 139.5 145.25 139.5C146.08 139.5 146.75 140.17 146.75 141C146.75 141.83 146.08 142.5 145.25 142.5C144.42 142.5 143.75 141.83 143.75 141ZM143.75 147C143.75 146.17 144.42 145.5 145.25 145.5C146.08 145.5 146.75 146.17 146.75 147C146.75 147.83 146.08 148.5 145.25 148.5C144.42 148.5 143.75 147.83 143.75 147ZM145.25 151.5C144.42 151.5 143.75 152.18 143.75 153C143.75 153.82 144.43 154.5 145.25 154.5C146.07 154.5 146.75 153.82 146.75 153C146.75 152.18 146.08 151.5 145.25 151.5ZM162.25 154H148.25V152H162.25V154ZM148.25 148H162.25V146H148.25V148ZM148.25 142V140H162.25V142H148.25Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask1)'%3E%3Crect x='141' y='135' width='24' height='24' fill='%23444444'/%3E%3C/g%3E%3Cmask id='mask2' mask-type='alpha' maskUnits='userSpaceOnUse' x='139' y='54' width='28' height='11'%3E%3Crect x='139' y='54' width='28' height='11' fill='%23C4C4C4'/%3E%3C/mask%3E%3Cg mask='url(%23mask2)'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M139 67L153 54L167 67H139Z' fill='white'/%3E%3C/g%3E%3Crect x='59' y='74' width='188' height='28' rx='3' stroke='%231486B8' stroke-width='2'/%3E%3Cpath d='M211 207.47L212.76 211H227V219H211V207.47ZM229 205H225L227 209H224L222 205H220L222 209H219L217 205H215L217 209H214L212 205H211C209.9 205 209.01 205.9 209.01 207L209 219C209 220.1 209.9 221 211 221H227C228.1 221 229 220.1 229 219V205Z' fill='%23444444'/%3E%3Cpath d='M94.0001 136.4H85.0481C84.0521 136.4 83.1401 136.64 82.3121 137.12C81.4721 137.612 80.8121 138.272 80.3321 139.1C79.8401 139.928 79.6001 140.84 79.6001 141.824C79.6001 142.808 79.8401 143.72 80.3321 144.548C80.8121 145.376 81.4721 146.036 82.3121 146.516C83.1401 147.008 84.0521 147.248 85.0481 147.248H85.5641V154.4C85.5641 154.724 85.6841 155.012 85.9121 155.252C86.1521 155.48 86.4401 155.6 86.7641 155.6C87.1001 155.6 87.3761 155.48 87.6161 155.252C87.8561 155.012 87.9761 154.724 87.9761 154.4V140C87.9761 139.676 88.0841 139.388 88.3241 139.148C88.5641 138.92 88.8521 138.8 89.1761 138.8C89.5001 138.8 89.7881 138.92 90.0281 139.148C90.2561 139.388 90.3761 139.676 90.3761 140V154.4C90.3761 154.724 90.4961 155.012 90.7361 155.252C90.9761 155.48 91.2521 155.6 91.5881 155.6C91.9121 155.6 92.2001 155.48 92.4401 155.252C92.6681 155.012 92.7881 154.724 92.7881 154.4V138.8H94.0001C94.3241 138.8 94.6001 138.68 94.8401 138.44C95.0801 138.212 95.2001 137.924 95.2001 137.6C95.2001 137.276 95.0801 136.988 94.8401 136.748C94.6001 136.52 94.3241 136.4 94.0001 136.4Z' fill='%23444444'/%3E%3Cmask id='mask3' mask-type='alpha' maskUnits='userSpaceOnUse' x='76' y='204' width='22' height='18'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M96 204H78C77 204 76 205 76 206V220C76 221.1 76.9 222 78 222H96C97 222 98 221 98 220V206C98 205 97 204 96 204ZM96 219.92C95.9861 219.941 95.9624 219.962 95.9426 219.979C95.9339 219.987 95.9261 219.994 95.92 220H78V206.08L78.08 206H95.91C95.9309 206.014 95.9518 206.038 95.9694 206.057C95.977 206.066 95.9839 206.074 95.99 206.08V219.92H96ZM83.5 213.5L86 216.51L89.5 212L94 218H80L83.5 213.5Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask3)'%3E%3Crect x='75' y='201' width='24' height='24' fill='%23444444'/%3E%3C/g%3E%3Cpath d='M161 205V217H149V205H161ZM161 203H149C147.9 203 147 203.9 147 205V217C147 218.1 147.9 219 149 219H161C162.1 219 163 218.1 163 217V205C163 203.9 162.1 203 161 203ZM152.5 212.67L154.19 214.93L156.67 211.83L160 216H150L152.5 212.67ZM143 207V221C143 222.1 143.9 223 145 223H159V221H145V207H143Z' fill='%23444444'/%3E%3Cmask id='mask4' mask-type='alpha' maskUnits='userSpaceOnUse' x='210' y='140' width='18' height='12'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M215.62 152H210.38L212.38 148H210V140H218V147.24L215.62 152ZM220.38 152H225.62L228 147.24V140H220V148H222.38L220.38 152ZM224.38 150H223.62L225.62 146H222V142H226V146.76L224.38 150ZM214.38 150H213.62L215.62 146H212V142H216V146.76L214.38 150Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask4)'%3E%3Crect x='207' y='134' width='24' height='24' fill='%23444444'/%3E%3C/g%3E%3Cdefs%3E%3Cfilter id='filter0_d' x='18' y='36' width='270' height='250' filterUnits='userSpaceOnUse' color-interpolation-filters='sRGB'%3E%3CfeFlood flood-opacity='0' result='BackgroundImageFix'/%3E%3CfeColorMatrix in='SourceAlpha' type='matrix' values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0'/%3E%3CfeOffset dy='3'/%3E%3CfeGaussianBlur stdDeviation='15'/%3E%3CfeColorMatrix type='matrix' values='0 0 0 0 0.0980392 0 0 0 0 0.117647 0 0 0 0 0.137255 0 0 0 0.1 0'/%3E%3CfeBlend mode='normal' in2='BackgroundImageFix' result='effect1_dropShadow'/%3E%3CfeBlend mode='normal' in='SourceGraphic' in2='effect1_dropShadow' result='shape'/%3E%3C/filter%3E%3C/defs%3E%3C/svg%3E%0A"
  }, props));
};
var images_InserterIconImage = function InserterIconImage(props) {
  return Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
    alt: Object(external_this_wp_i18n_["__"])('inserter'),
    src: "data:image/svg+xml;charset=utf8,%3Csvg width='18' height='18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M8.824 0C3.97 0 0 3.97 0 8.824c0 4.853 3.97 8.824 8.824 8.824 4.853 0 8.824-3.971 8.824-8.824S13.677 0 8.824 0zM7.94 5.294v2.647H5.294v1.765h2.647v2.647h1.765V9.706h2.647V7.941H9.706V5.294H7.941zm-6.176 3.53c0 3.882 3.176 7.059 7.059 7.059 3.882 0 7.059-3.177 7.059-7.06 0-3.882-3.177-7.058-7.06-7.058-3.882 0-7.058 3.176-7.058 7.059z' fill='%234A4A4A'/%3E%3Cmask id='a' maskUnits='userSpaceOnUse' x='0' y='0' width='18' height='18'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M8.824 0C3.97 0 0 3.97 0 8.824c0 4.853 3.97 8.824 8.824 8.824 4.853 0 8.824-3.971 8.824-8.824S13.677 0 8.824 0zM7.94 5.294v2.647H5.294v1.765h2.647v2.647h1.765V9.706h2.647V7.941H9.706V5.294H7.941zm-6.176 3.53c0 3.882 3.176 7.059 7.059 7.059 3.882 0 7.059-3.177 7.059-7.06 0-3.882-3.177-7.058-7.06-7.058-3.882 0-7.058 3.176-7.058 7.059z' fill='%23fff'/%3E%3C/mask%3E%3Cg mask='url(%23a)'%3E%3Cpath fill='%23444' d='M0 0h17.644v17.644H0z'/%3E%3C/g%3E%3C/svg%3E"
  }, props));
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function WelcomeGuide() {
  var areTipsEnabled = Object(external_this_wp_data_["useSelect"])(function (select) {
    return select('core/nux').areTipsEnabled();
  });

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/nux'),
      disableTips = _useDispatch.disableTips;

  if (!areTipsEnabled) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Guide"], {
    className: "edit-post-welcome-guide",
    finishButtonText: Object(external_this_wp_i18n_["__"])('Get started'),
    onFinish: disableTips
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["GuidePage"], {
    className: "edit-post-welcome-guide__page"
  }, Object(external_this_wp_element_["createElement"])("h1", {
    className: "edit-post-welcome-guide__heading"
  }, Object(external_this_wp_i18n_["__"])('Welcome to the Block Editor')), Object(external_this_wp_element_["createElement"])(images_CanvasImage, {
    className: "edit-post-welcome-guide__image"
  }), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-welcome-guide__text"
  }, Object(external_this_wp_i18n_["__"])('In the WordPress editor, each paragraph, image, or video is presented as a distinct “block” of content.'))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["GuidePage"], {
    className: "edit-post-welcome-guide__page"
  }, Object(external_this_wp_element_["createElement"])("h1", {
    className: "edit-post-welcome-guide__heading"
  }, Object(external_this_wp_i18n_["__"])('Make each block your own')), Object(external_this_wp_element_["createElement"])(images_EditorImage, {
    className: "edit-post-welcome-guide__image"
  }), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-welcome-guide__text"
  }, Object(external_this_wp_i18n_["__"])('Each block comes with its own set of controls for changing things like color, width, and alignment. These will show and hide automatically when you have a block selected.'))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["GuidePage"], {
    className: "edit-post-welcome-guide__page"
  }, Object(external_this_wp_element_["createElement"])("h1", {
    className: "edit-post-welcome-guide__heading"
  }, Object(external_this_wp_i18n_["__"])('Get to know the Block Library')), Object(external_this_wp_element_["createElement"])(images_BlockLibraryImage, {
    className: "edit-post-welcome-guide__image"
  }), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-welcome-guide__text"
  }, Object(external_this_wp_element_["__experimentalCreateInterpolateElement"])(Object(external_this_wp_i18n_["__"])('All of the blocks available to you live in the Block Library. You’ll find it wherever you see the <InserterIconImage /> icon.'), {
    InserterIconImage: Object(external_this_wp_element_["createElement"])(images_InserterIconImage, {
      className: "edit-post-welcome-guide__inserter-icon"
    })
  }))));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


















function Layout() {
  var isMobileViewport = Object(external_this_wp_compose_["useViewportMatch"])('small', '<');

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/edit-post'),
      closePublishSidebar = _useDispatch.closePublishSidebar,
      togglePublishSidebar = _useDispatch.togglePublishSidebar;

  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
      editorSidebarOpened: select('core/edit-post').isEditorSidebarOpened(),
      pluginSidebarOpened: select('core/edit-post').isPluginSidebarOpened(),
      publishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
      mode: select('core/edit-post').getEditorMode(),
      isRichEditingEnabled: select('core/editor').getEditorSettings().richEditingEnabled,
      hasActiveMetaboxes: select('core/edit-post').hasMetaBoxes(),
      isSaving: select('core/edit-post').isSavingMetaBoxes()
    };
  }),
      mode = _useSelect.mode,
      isRichEditingEnabled = _useSelect.isRichEditingEnabled,
      editorSidebarOpened = _useSelect.editorSidebarOpened,
      pluginSidebarOpened = _useSelect.pluginSidebarOpened,
      publishSidebarOpened = _useSelect.publishSidebarOpened,
      hasActiveMetaboxes = _useSelect.hasActiveMetaboxes,
      isSaving = _useSelect.isSaving,
      hasFixedToolbar = _useSelect.hasFixedToolbar;

  var showPageTemplatePicker = Object(external_this_wp_blockEditor_["__experimentalUsePageTemplatePickerVisible"])();

  var sidebarIsOpened = editorSidebarOpened || pluginSidebarOpened || publishSidebarOpened;
  var className = classnames_default()('edit-post-layout', 'is-mode-' + mode, {
    'is-sidebar-opened': sidebarIsOpened,
    'has-fixed-toolbar': hasFixedToolbar,
    'has-metaboxes': hasActiveMetaboxes
  });
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(fullscreen_mode, null), Object(external_this_wp_element_["createElement"])(browser_url, null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["UnsavedChangesWarning"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["AutosaveMonitor"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["LocalAutosaveMonitor"], null), Object(external_this_wp_element_["createElement"])(components_keyboard_shortcuts, null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["FocusReturnProvider"], null, Object(external_this_wp_element_["createElement"])(editor_regions, {
    className: className,
    header: Object(external_this_wp_element_["createElement"])(components_header, null),
    sidebar: !publishSidebarOpened && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(settings_sidebar, null), Object(external_this_wp_element_["createElement"])(components_sidebar.Slot, null)),
    content: Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorNotices"], null), (mode === 'text' || !isRichEditingEnabled) && Object(external_this_wp_element_["createElement"])(text_editor, null), isRichEditingEnabled && mode === 'visual' && Object(external_this_wp_element_["createElement"])(visual_editor, null), Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-layout__metaboxes"
    }, Object(external_this_wp_element_["createElement"])(meta_boxes, {
      location: "normal"
    }), Object(external_this_wp_element_["createElement"])(meta_boxes, {
      location: "advanced"
    })), isMobileViewport && sidebarIsOpened && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ScrollLock"], null)),
    footer: isRichEditingEnabled && mode === 'visual' && Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-layout__footer"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockBreadcrumb"], null)),
    publish: publishSidebarOpened ? Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPublishPanel"], {
      onClose: closePublishSidebar,
      forceIsDirty: hasActiveMetaboxes,
      forceIsSaving: isSaving,
      PrePublishExtension: plugin_pre_publish_panel.Slot,
      PostPublishExtension: plugin_post_publish_panel.Slot
    }) : Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-toggle-publish-panel"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
      isDefault: true,
      className: "edit-post-toggle-publish-panel__button",
      onClick: togglePublishSidebar,
      "aria-expanded": false
    }, Object(external_this_wp_i18n_["__"])('Open publish panel')))
  }), Object(external_this_wp_element_["createElement"])(manage_blocks_modal, null), Object(external_this_wp_element_["createElement"])(options_modal, null), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal, null), Object(external_this_wp_element_["createElement"])(WelcomeGuide, null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Popover"].Slot, null), Object(external_this_wp_element_["createElement"])(external_this_wp_plugins_["PluginArea"], null), showPageTemplatePicker && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalPageTemplatePicker"], null)));
}

/* harmony default export */ var layout = (Layout);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/listener-hooks.js
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
 * @param {number} postId  The current post id.
 */

var listener_hooks_useBlockSelectionListener = function useBlockSelectionListener(postId) {
  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      hasBlockSelection: !!select('core/block-editor').getBlockSelectionStart(),
      isEditorSidebarOpened: select(STORE_KEY).isEditorSidebarOpened()
    };
  }, [postId]),
      hasBlockSelection = _useSelect.hasBlockSelection,
      isEditorSidebarOpened = _useSelect.isEditorSidebarOpened;

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])(STORE_KEY),
      openGeneralSidebar = _useDispatch.openGeneralSidebar;

  Object(external_this_wp_element_["useEffect"])(function () {
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
 * This listener hook is used to monitor viewport size and adjust the sidebar
 * accordingly.
 *
 * @param {number} postId  The current post id.
 */

var listener_hooks_useAdjustSidebarListener = function useAdjustSidebarListener(postId) {
  var _useSelect2 = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      isSmall: select('core/viewport').isViewportMatch('< medium'),
      activeGeneralSidebarName: select(STORE_KEY).getActiveGeneralSidebarName()
    };
  }, [postId]),
      isSmall = _useSelect2.isSmall,
      activeGeneralSidebarName = _useSelect2.activeGeneralSidebarName;

  var _useDispatch2 = Object(external_this_wp_data_["useDispatch"])(STORE_KEY),
      openGeneralSidebar = _useDispatch2.openGeneralSidebar,
      closeGeneralSidebar = _useDispatch2.closeGeneralSidebar;

  var previousIsSmall = Object(external_this_wp_element_["useRef"])(null);
  var sidebarToReOpenOnExpand = Object(external_this_wp_element_["useRef"])(null);
  Object(external_this_wp_element_["useEffect"])(function () {
    if (previousIsSmall.current === isSmall) {
      return;
    }

    previousIsSmall.current = isSmall;

    if (isSmall) {
      sidebarToReOpenOnExpand.current = activeGeneralSidebarName;

      if (activeGeneralSidebarName) {
        closeGeneralSidebar();
      }
    } else if (sidebarToReOpenOnExpand.current && !activeGeneralSidebarName) {
      openGeneralSidebar(sidebarToReOpenOnExpand.current);
      sidebarToReOpenOnExpand.current = null;
    }
  }, [isSmall, activeGeneralSidebarName]);
};
/**
 * This listener hook monitors any change in permalink and updates the view
 * post link in the admin bar.
 *
 * @param {number} postId
 */

var listener_hooks_useUpdatePostLinkListener = function useUpdatePostLinkListener(postId) {
  var _useSelect3 = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      newPermalink: select('core/editor').getCurrentPost().link
    };
  }, [postId]),
      newPermalink = _useSelect3.newPermalink;

  var nodeToUpdate = Object(external_this_wp_element_["useRef"])();
  Object(external_this_wp_element_["useEffect"])(function () {
    nodeToUpdate.current = document.querySelector(VIEW_AS_PREVIEW_LINK_SELECTOR) || document.querySelector(VIEW_AS_LINK_SELECTOR);
  }, [postId]);
  Object(external_this_wp_element_["useEffect"])(function () {
    if (!newPermalink || !nodeToUpdate.current) {
      return;
    }

    nodeToUpdate.current.setAttribute('href', newPermalink);
  }, [newPermalink]);
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/index.js
/**
 * Internal dependencies
 */

/**
 * Data component used for initializing the editor and re-initializes
 * when postId changes or on unmount.
 *
 * @param {number} postId  The id of the post.
 * @return {null} This is a data component so does not render any ui.
 */

/* harmony default export */ var editor_initialization = (function (_ref) {
  var postId = _ref.postId;
  listener_hooks_useBlockSelectionListener(postId);
  listener_hooks_useAdjustSidebarListener(postId);
  listener_hooks_useUpdatePostLinkListener(postId);
  return null;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/editor.js











/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */






var editor_Editor =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(Editor, _Component);

  function Editor() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, Editor);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(Editor).apply(this, arguments));
    _this.getEditorSettings = memize_default()(_this.getEditorSettings, {
      maxSize: 1
    });
    return _this;
  }

  Object(createClass["a" /* default */])(Editor, [{
    key: "getEditorSettings",
    value: function getEditorSettings(settings, hasFixedToolbar, showInserterHelpPanel, focusMode, hiddenBlockTypes, blockTypes, preferredStyleVariations, __experimentalLocalAutosaveInterval, updatePreferredStyleVariations) {
      settings = Object(objectSpread["a" /* default */])({}, settings, {
        __experimentalPreferredStyleVariations: {
          value: preferredStyleVariations,
          onChange: updatePreferredStyleVariations
        },
        hasFixedToolbar: hasFixedToolbar,
        focusMode: focusMode,
        showInserterHelpPanel: showInserterHelpPanel,
        __experimentalLocalAutosaveInterval: __experimentalLocalAutosaveInterval
      }); // Omit hidden block types if exists and non-empty.

      if (Object(external_this_lodash_["size"])(hiddenBlockTypes) > 0) {
        // Defer to passed setting for `allowedBlockTypes` if provided as
        // anything other than `true` (where `true` is equivalent to allow
        // all block types).
        var defaultAllowedBlockTypes = true === settings.allowedBlockTypes ? Object(external_this_lodash_["map"])(blockTypes, 'name') : settings.allowedBlockTypes || [];
        settings.allowedBlockTypes = external_this_lodash_["without"].apply(void 0, [defaultAllowedBlockTypes].concat(Object(toConsumableArray["a" /* default */])(hiddenBlockTypes)));
      }

      return settings;
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          settings = _this$props.settings,
          hasFixedToolbar = _this$props.hasFixedToolbar,
          focusMode = _this$props.focusMode,
          post = _this$props.post,
          postId = _this$props.postId,
          initialEdits = _this$props.initialEdits,
          onError = _this$props.onError,
          hiddenBlockTypes = _this$props.hiddenBlockTypes,
          blockTypes = _this$props.blockTypes,
          preferredStyleVariations = _this$props.preferredStyleVariations,
          __experimentalLocalAutosaveInterval = _this$props.__experimentalLocalAutosaveInterval,
          showInserterHelpPanel = _this$props.showInserterHelpPanel,
          updatePreferredStyleVariations = _this$props.updatePreferredStyleVariations,
          props = Object(objectWithoutProperties["a" /* default */])(_this$props, ["settings", "hasFixedToolbar", "focusMode", "post", "postId", "initialEdits", "onError", "hiddenBlockTypes", "blockTypes", "preferredStyleVariations", "__experimentalLocalAutosaveInterval", "showInserterHelpPanel", "updatePreferredStyleVariations"]);

      if (!post) {
        return null;
      }

      var editorSettings = this.getEditorSettings(settings, hasFixedToolbar, showInserterHelpPanel, focusMode, hiddenBlockTypes, blockTypes, preferredStyleVariations, __experimentalLocalAutosaveInterval, updatePreferredStyleVariations);
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["StrictMode"], null, Object(external_this_wp_element_["createElement"])(edit_post_settings.Provider, {
        value: settings
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SlotFillProvider"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["DropZoneProvider"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorProvider"], Object(esm_extends["a" /* default */])({
        settings: editorSettings,
        post: post,
        initialEdits: initialEdits,
        useSubRegistry: false
      }, props), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["ErrorBoundary"], {
        onError: onError
      }, Object(external_this_wp_element_["createElement"])(editor_initialization, {
        postId: postId
      }), Object(external_this_wp_element_["createElement"])(layout, null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["KeyboardShortcuts"], {
        shortcuts: prevent_event_discovery
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLockedModal"], null))))));
    }
  }]);

  return Editor;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var editor = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var postId = _ref.postId,
      postType = _ref.postType;

  var _select = select('core/edit-post'),
      isFeatureActive = _select.isFeatureActive,
      getPreference = _select.getPreference;

  var _select2 = select('core'),
      getEntityRecord = _select2.getEntityRecord;

  var _select3 = select('core/blocks'),
      getBlockTypes = _select3.getBlockTypes;

  return {
    showInserterHelpPanel: isFeatureActive('showInserterHelpPanel'),
    hasFixedToolbar: isFeatureActive('fixedToolbar'),
    focusMode: isFeatureActive('focusMode'),
    post: getEntityRecord('postType', postType, postId),
    preferredStyleVariations: getPreference('preferredStyleVariations'),
    hiddenBlockTypes: getPreference('hiddenBlockTypes'),
    blockTypes: getBlockTypes(),
    __experimentalLocalAutosaveInterval: getPreference('localAutosaveInterval')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      updatePreferredStyleVariations = _dispatch.updatePreferredStyleVariations;

  return {
    updatePreferredStyleVariations: updatePreferredStyleVariations
  };
})])(editor_Editor));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-item.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



var plugin_block_settings_menu_item_isEverySelectedBlockAllowed = function isEverySelectedBlockAllowed(selected, allowed) {
  return Object(external_this_lodash_["difference"])(selected, allowed).length === 0;
};
/**
 * Plugins may want to add an item to the menu either for every block
 * or only for the specific ones provided in the `allowedBlocks` component property.
 *
 * If there are multiple blocks selected the item will be rendered if every block
 * is of one allowed type (not necessarily the same).
 *
 * @param {string[]} selectedBlocks Array containing the names of the blocks selected
 * @param {string[]} allowedBlocks Array containing the names of the blocks allowed
 * @return {boolean} Whether the item will be rendered or not.
 */


var shouldRenderItem = function shouldRenderItem(selectedBlocks, allowedBlocks) {
  return !Array.isArray(allowedBlocks) || plugin_block_settings_menu_item_isEverySelectedBlockAllowed(selectedBlocks, allowedBlocks);
};
/**
 * Renders a new item in the block settings menu.
 *
 * @param {Object} props Component props.
 * @param {Array} [props.allowedBlocks] An array containing a list of block names for which the item should be shown. If not present, it'll be rendered for any block. If multiple blocks are selected, it'll be shown if and only if all of them are in the whitelist.
 * @param {WPBlockTypeIconRender} [props.icon] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element.
 * @param {string} props.label The menu item text.
 * @param {Function} props.onClick Callback function to be executed when the user click the menu item.
 *
 * @example <caption>ES5</caption>
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
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from wp.i18n;
 * import { PluginBlockSettingsMenuItem } from wp.editPost;
 *
 * const doOnClick = ( ) => {
 *     // To be called when the user clicks the menu item.
 * };
 *
 * const MyPluginBlockSettingsMenuItem = () => (
 *     <PluginBlockSettingsMenuItem
 * 		allowedBlocks=[ 'core/paragraph' ]
 * 		icon='dashicon-name'
 * 		label=__( 'Menu item text' )
 * 		onClick={ doOnClick } />
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


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

    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
      className: "editor-block-settings-menu__control block-editor-block-settings-menu__control",
      onClick: Object(external_this_wp_compose_["compose"])(onClick, onClose),
      icon: icon || 'admin-plugins',
      label: small ? label : undefined,
      role: role
    }, !small && label);
  });
};

/* harmony default export */ var plugin_block_settings_menu_item = (plugin_block_settings_menu_item_PluginBlockSettingsMenuItem);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-more-menu-item/index.js




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var plugin_more_menu_item_PluginMoreMenuItem = function PluginMoreMenuItem(_ref) {
  var _ref$onClick = _ref.onClick,
      onClick = _ref$onClick === void 0 ? external_this_lodash_["noop"] : _ref$onClick,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["onClick"]);

  return Object(external_this_wp_element_["createElement"])(plugins_more_menu_group, null, function (fillProps) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], Object(esm_extends["a" /* default */])({}, props, {
      onClick: Object(external_this_wp_compose_["compose"])(onClick, fillProps.onClose)
    }));
  });
};
/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down, and can be used to as a button or link depending on the props provided.
 * The text within the component appears as the menu item label.
 *
 * @param {Object} props Component properties.
 * @param {string} [props.href] When `href` is provided then the menu item is represented as an anchor rather than button. It corresponds to the `href` attribute of the anchor.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered to the left of the menu item label.
 * @param {Function} [props.onClick=noop] The callback function to be executed when the user clicks the menu item.
 * @param {...*} [props.other] Any additional props are passed through to the underlying [MenuItem](/packages/components/src/menu-item/README.md) component.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginMoreMenuItem = wp.editPost.PluginMoreMenuItem;
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * function MyButtonMoreMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginMoreMenuItem,
 * 		{
 * 			icon: 'smiley',
 * 			onClick: onButtonClick
 * 		},
 * 		__( 'My button title' )
 * 	)
 * }
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { __ } = wp.i18n;
 * const { PluginMoreMenuItem } = wp.editPost;
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * const MyButtonMoreMenuItem = () => (
 * 	<PluginMoreMenuItem
 * 		icon="smiley"
 * 		onClick={ onButtonClick }
 * 	>
 * 		{ __( 'My button title' ) }
 * 	</PluginMoreMenuItem>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


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





function PluginSidebar(props) {
  var children = props.children,
      className = props.className,
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
  })), Object(external_this_wp_element_["createElement"])(components_sidebar, {
    name: sidebarName
  }, Object(external_this_wp_element_["createElement"])(sidebar_header, {
    closeLabel: Object(external_this_wp_i18n_["__"])('Close plugin')
  }, Object(external_this_wp_element_["createElement"])("strong", null, title), isPinnable && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: isPinned ? 'star-filled' : 'star-empty',
    label: isPinned ? Object(external_this_wp_i18n_["__"])('Unpin from toolbar') : Object(external_this_wp_i18n_["__"])('Pin to toolbar'),
    onClick: togglePin,
    isToggled: isPinned,
    "aria-expanded": isPinned
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Panel"], {
    className: className
  }, children)));
}
/**
 * Renders a sidebar when activated. The contents within the `PluginSidebar` will appear as content within the sidebar.
 * If you wish to display the sidebar, you can with use the `PluginSidebarMoreMenuItem` component or the `wp.data.dispatch` API:
 *
 * ```js
 * wp.data.dispatch( 'core/edit-post' ).openGeneralSidebar( 'plugin-name/sidebar-name' );
 * ```
 *
 * @see PluginSidebarMoreMenuItem
 *
 * @param {Object} props Element props.
 * @param {string} props.name A string identifying the sidebar. Must be unique for every sidebar registered within the scope of your plugin.
 * @param {string} [props.className] An optional class name added to the sidebar body.
 * @param {string} props.title Title displayed at the top of the sidebar.
 * @param {boolean} [props.isPinnable=true] Whether to allow to pin sidebar to toolbar.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var el = wp.element.createElement;
 * var PanelBody = wp.components.PanelBody;
 * var PluginSidebar = wp.editPost.PluginSidebar;
 *
 * function MyPluginSidebar() {
 * 	return el(
 * 			PluginSidebar,
 * 			{
 * 				name: 'my-sidebar',
 * 				title: 'My sidebar title',
 * 				icon: 'smiley',
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
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { __ } = wp.i18n;
 * const { PanelBody } = wp.components;
 * const { PluginSidebar } = wp.editPost;
 *
 * const MyPluginSidebar = () => (
 * 	<PluginSidebar
 * 		name="my-sidebar"
 * 		title="My sidebar title"
 * 		icon="smiley"
 * 	>
 * 		<PanelBody>
 * 			{ __( 'My sidebar content' ) }
 * 		</PanelBody>
 * 	</PluginSidebar>
 * );
 * ```
 *
 * @return {WPComponent} Plugin sidebar component.
 */


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


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



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
/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down,
 * and can be used to activate the corresponding `PluginSidebar` component.
 * The text within the component appears as the menu item label.
 *
 * @param {Object} props Component props.
 * @param {string} props.target A string identifying the target sidebar you wish to be activated by this menu item. Must be the same as the `name` prop you have given to that sidebar.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered to the left of the menu item label.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
 *
 * function MySidebarMoreMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginSidebarMoreMenuItem,
 * 		{
 * 			target: 'my-sidebar',
 * 			icon: 'smiley',
 * 		},
 * 		__( 'My sidebar title' )
 * 	)
 * }
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { __ } = wp.i18n;
 * const { PluginSidebarMoreMenuItem } = wp.editPost;
 *
 * const MySidebarMoreMenuItem = () => (
 * 	<PluginSidebarMoreMenuItem
 * 		target="my-sidebar"
 * 		icon="smiley"
 * 	>
 * 		{ __( 'My sidebar title' ) }
 * 	</PluginSidebarMoreMenuItem>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


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
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "reinitializeEditor", function() { return reinitializeEditor; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "initializeEditor", function() { return initializeEditor; });
/* concated harmony reexport PluginBlockSettingsMenuItem */__webpack_require__.d(__webpack_exports__, "PluginBlockSettingsMenuItem", function() { return plugin_block_settings_menu_item; });
/* concated harmony reexport PluginDocumentSettingPanel */__webpack_require__.d(__webpack_exports__, "PluginDocumentSettingPanel", function() { return plugin_document_setting_panel; });
/* concated harmony reexport PluginMoreMenuItem */__webpack_require__.d(__webpack_exports__, "PluginMoreMenuItem", function() { return plugin_more_menu_item; });
/* concated harmony reexport PluginPostPublishPanel */__webpack_require__.d(__webpack_exports__, "PluginPostPublishPanel", function() { return plugin_post_publish_panel; });
/* concated harmony reexport PluginPostStatusInfo */__webpack_require__.d(__webpack_exports__, "PluginPostStatusInfo", function() { return plugin_post_status_info; });
/* concated harmony reexport PluginPrePublishPanel */__webpack_require__.d(__webpack_exports__, "PluginPrePublishPanel", function() { return plugin_pre_publish_panel; });
/* concated harmony reexport PluginSidebar */__webpack_require__.d(__webpack_exports__, "PluginSidebar", function() { return plugin_sidebar; });
/* concated harmony reexport PluginSidebarMoreMenuItem */__webpack_require__.d(__webpack_exports__, "PluginSidebarMoreMenuItem", function() { return plugin_sidebar_more_menu_item; });


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
  Object(external_this_wp_blockLibrary_["registerCoreBlocks"])();

  if (false) {} // Show a console log warning if the browser is not in Standards rendering mode.


  var documentMode = document.compatMode === 'CSS1Compat' ? 'Standards' : 'Quirks';

  if (documentMode !== 'Standards') {
    // eslint-disable-next-line no-console
    console.warn("Your browser is using Quirks Mode. \nThis can cause rendering issues such as blocks overlaying meta boxes in the editor. Quirks Mode can be triggered by PHP errors or HTML code appearing before the opening <!DOCTYPE html>. Try checking the raw page source or your site's PHP error log and resolving errors there, removing any HTML before the doctype, or disabling plugins.");
  } // This is a temporary fix for a couple of issues specific to Webkit on iOS.
  // Without this hack the browser scrolls the mobile toolbar off-screen.
  // Once supported in Safari we can replace this in favor of preventScroll.
  // For details see issue #18632 and PR #18686
  // Specifically, we scroll `edit-post-editor-regions__body` to enable a fixed top toolbar.
  // But Mobile Safari forces the `html` element to scroll upwards, hiding the toolbar.


  var isIphone = window.navigator.userAgent.indexOf('iPhone') !== -1;

  if (isIphone) {
    window.addEventListener('scroll', function (event) {
      var editorScrollContainer = document.getElementsByClassName('edit-post-editor-regions__body')[0];

      if (event.target === document) {
        // Scroll element into view by scrolling the editor container by the same amount
        // that Mobile Safari tried to scroll the html element upwards.
        if (window.scrollY > 100) {
          editorScrollContainer.scrollTop = editorScrollContainer.scrollTop + window.scrollY;
        } //Undo unwanted scroll on html element


        window.scrollTo(0, 0);
      }
    });
  }

  Object(external_this_wp_element_["render"])(Object(external_this_wp_element_["createElement"])(editor, {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits
  }), target);
}










/***/ }),

/***/ 37:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ 38:
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

/***/ 4:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ 40:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectWithoutPropertiesLoose; });
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

/***/ }),

/***/ 42:
/***/ (function(module, exports, __webpack_require__) {

module.exports = function memize( fn, options ) {
	var size = 0,
		maxSize, head, tail;

	if ( options && options.maxSize ) {
		maxSize = options.maxSize;
	}

	function memoized( /* ...args */ ) {
		var node = head,
			len = arguments.length,
			args, i;

		searchCache: while ( node ) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if ( node.args.length !== arguments.length ) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for ( i = 0; i < len; i++ ) {
				if ( node.args[ i ] !== arguments[ i ] ) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== head ) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if ( node === tail ) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				node.prev.next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				head.prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply( null, args )
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( head ) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if ( size === maxSize ) {
			tail = tail.prev;
			tail.next = null;
		} else {
			size++;
		}

		head = node;

		return node.val;
	}

	memoized.clear = function() {
		head = null;
		tail = null;
		size = 0;
	};

	if ( false ) {}

	return memoized;
};


/***/ }),

/***/ 44:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["a11y"]; }());

/***/ }),

/***/ 49:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["plugins"]; }());

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockEditor"]; }());

/***/ }),

/***/ 50:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["viewport"]; }());

/***/ }),

/***/ 6:
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

/***/ 62:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["coreData"]; }());

/***/ }),

/***/ 7:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(9);

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(Object(source));

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      Object(_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(target, key, source[key]);
    });
  }

  return target;
}

/***/ }),

/***/ 70:
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

/***/ 8:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ 9:
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

/***/ 98:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["mediaUtils"]; }());

/***/ })

/******/ });