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
/******/ 	return __webpack_require__(__webpack_require__.s = 304);
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
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ 11:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ 12:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(28);
/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(3);


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ 121:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["notices"]; }());

/***/ }),

/***/ 13:
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

/***/ 14:
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

/***/ 15:
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

/***/ 16:
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

/***/ 17:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),

/***/ 18:
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

/***/ 189:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockLibrary"]; }());

/***/ }),

/***/ 19:
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

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 21:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

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
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectWithoutProperties; });

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

/***/ 23:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

/***/ }),

/***/ 24:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ 25:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__(35);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
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
var nonIterableRest = __webpack_require__(36);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _slicedToArray; });



function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 28:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ 3:
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

/***/ 30:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ 304:
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
__webpack_require__.d(selectors_namespaceObject, "getPreference", function() { return getPreference; });
__webpack_require__.d(selectors_namespaceObject, "isPublishSidebarOpened", function() { return selectors_isPublishSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelRemoved", function() { return isEditorPanelRemoved; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelEnabled", function() { return isEditorPanelEnabled; });
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
var external_this_wp_coreData_ = __webpack_require__(79);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","nux"]}
var external_this_wp_nux_ = __webpack_require__(52);

// EXTERNAL MODULE: external {"this":["wp","viewport"]}
var external_this_wp_viewport_ = __webpack_require__(37);

// EXTERNAL MODULE: external {"this":["wp","notices"]}
var external_this_wp_notices_ = __webpack_require__(121);

// EXTERNAL MODULE: external {"this":["wp","blockLibrary"]}
var external_this_wp_blockLibrary_ = __webpack_require__(189);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__(23);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(14);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/components/media-upload/index.js







/**
 * External Dependencies
 */

/**
 * WordPress dependencies
 */


 // Getter for the sake of unit tests.

var getGalleryDetailsMediaFrame = function getGalleryDetailsMediaFrame() {
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
        library: wp.media.query(_.defaults({
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
      var GalleryDetailsMediaFrame = getGalleryDetailsMediaFrame();
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
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var components_replaceMediaUpload = function replaceMediaUpload() {
  return media_upload;
};

Object(external_this_wp_hooks_["addFilter"])('editor.MediaUpload', 'core/edit-post/components/media-upload/replace-media-upload', components_replaceMediaUpload);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(18);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__(21);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(11);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(7);

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
 * @param {Component} WrappedBlockEdit A filtered BlockEdit instance.
 *
 * @return {Component} Enhanced component with merged state data props.
 */
Object(external_this_wp_data_["withSelect"])(function (select, block) {
  var blocks = select('core/editor').getBlocks();
  var multiple = Object(external_this_wp_blocks_["hasBlockSupport"])(block.name, 'multiple', true); // For block types with `multiple` support, there is no "original
  // block" to be found in the content, as the block itself is valid.

  if (multiple) {
    return {};
  } // Otherwise, only pass `originalBlockClientId` if it refers to a different
  // block from the current one.


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
var external_this_wp_plugins_ = __webpack_require__(62);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/copy-content-menu-item/index.js


/**
 * WordPress dependencies
 */





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
var external_this_wp_keycodes_ = __webpack_require__(17);

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
    return !Object(external_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
      label: Object(external_this_wp_i18n_["__"])('Tools')
    }, fills);
  });
};

/* harmony default export */ var tools_more_menu_group = (ToolsMoreMenuGroup);

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
var toConsumableArray = __webpack_require__(19);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(8);

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
var refx = __webpack_require__(87);
var refx_default = /*#__PURE__*/__webpack_require__.n(refx);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(25);

// EXTERNAL MODULE: external {"this":["wp","a11y"]}
var external_this_wp_a11y_ = __webpack_require__(44);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(30);
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
var rememo = __webpack_require__(31);

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

function isEditorPanelEnabled(state, panelName) {
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
    return isEditorPanelEnabled(state, "meta-box-".concat(id));
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

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





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
    var state = store.getState(); // Additional data needed for backwards compatibility.
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
 */





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

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(24);

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

  return {
    postId: id,
    postStatus: status,
    postType: type
  };
})(browser_url_BrowserURL));

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
    return !Object(external_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
      label: Object(external_this_wp_i18n_["__"])('Plugins')
    }, fills);
  });
};

/* harmony default export */ var plugins_more_menu_group = (PluginsMoreMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/options-menu-item/index.js


/**
 * WordPress Dependencies
 */



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
 */




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


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */







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


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



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
  /**
   * Conditions to show a BUTTON (publish directly) or a TOGGLE (open publish sidebar):
   *
   * 1) We want to show a BUTTON when the post status is at the _final stage_
   * for a particular role (see https://codex.wordpress.org/Post_Status):
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

  if (isPublished || isScheduled && isBeingScheduled || isPending && !hasPublishAction && !isLessThanMediumViewport) {
    component = IS_BUTTON;
  } else if (isLessThanMediumViewport) {
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
    hasBlockSelection: !!select('core/editor').getBlockSelectionStart(),
    isEditorSidebarOpened: select('core/edit-post').isEditorSidebarOpened(),
    isPublishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
    isSaving: select('core/edit-post').isSavingMetaBoxes()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref2) {
  var hasBlockSelection = _ref2.hasBlockSelection;

  var _dispatch = dispatch('core/edit-post'),
      _openGeneralSidebar = _dispatch.openGeneralSidebar,
      closeGeneralSidebar = _dispatch.closeGeneralSidebar;

  var sidebarToOpen = hasBlockSelection ? 'edit-post/block' : 'edit-post/document';
  return {
    openGeneralSidebar: function openGeneralSidebar() {
      return _openGeneralSidebar(sidebarToOpen);
    },
    closeGeneralSidebar: closeGeneralSidebar,
    hasBlockSelection: undefined
  };
}))(Header));

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

/**
 * Internal dependencies
 */




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

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/deferred.js







/**
 * WordPress dependencies
 */

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
/**
 * WordPress dependencies
 */


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

/**
 * WordPress dependencies
 */






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
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTaxonomies"], {
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

      if (element) {
        element.style.display = isVisible ? '' : 'none';
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
 * Internal Dependencies
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
    isViewable: Object(external_lodash_["get"])(postType, ['viewable'], false),
    postTitle: getEditedPostAttribute('title'),
    postSlug: getEditedPostAttribute('slug'),
    postID: id
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isNew = _ref2.isNew,
      postLink = _ref2.postLink,
      isViewable = _ref2.isViewable;
  return !isNew && postLink && isViewable;
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


/**
 * WordPress dependencies
 */






/**
 * Internal Dependencies
 */













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


/**
 * WordPress dependencies
 */


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


/**
 * WordPress dependencies
 */


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





/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



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
  }, Object(external_this_wp_element_["createElement"])(layout, null)), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLockedModal"], null)));
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
  return Object(external_this_wp_element_["createElement"])(plugins_more_menu_group, null, function (fillProps) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
      icon: isSelected ? 'yes' : icon,
      isSelected: isSelected,
      role: "menuitemcheckbox",
      onClick: Object(external_this_wp_compose_["compose"])(onClick, fillProps.onClose)
    }, children);
  });
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
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "reinitializeEditor", function() { return reinitializeEditor; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "initializeEditor", function() { return initializeEditor; });
/* concated harmony reexport PluginBlockSettingsMenuItem */__webpack_require__.d(__webpack_exports__, "PluginBlockSettingsMenuItem", function() { return plugin_block_settings_menu_item; });
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
  Object(external_this_wp_data_["dispatch"])('core/nux').triggerGuide(['core/editor.inserter', 'core/editor.settings', 'core/editor.preview', 'core/editor.publish']);
  Object(external_this_wp_element_["render"])(Object(external_this_wp_element_["createElement"])(editor, {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits
  }), target);
}








/***/ }),

/***/ 31:
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

/***/ 32:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

/***/ }),

/***/ 35:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ 36:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

/***/ }),

/***/ 37:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["viewport"]; }());

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ 44:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["a11y"]; }());

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ 52:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["nux"]; }());

/***/ }),

/***/ 6:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["editor"]; }());

/***/ }),

/***/ 62:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["plugins"]; }());

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ 79:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["coreData"]; }());

/***/ }),

/***/ 8:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(15);

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(source);

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

/***/ 87:
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

/***/ 9:
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

/***/ })

/******/ });