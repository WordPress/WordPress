this["wp"] = this["wp"] || {}; this["wp"]["blockLibrary"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/block-library/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _arrayWithoutHoles; });
function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _assertThisInitialized; });
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/createClass.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/createClass.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _createClass; });
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

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _defineProperty; });
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

/***/ "./node_modules/@babel/runtime/helpers/esm/extends.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/extends.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _extends; });
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

/***/ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _getPrototypeOf; });
function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/inherits.js":
/*!*************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/inherits.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _inherits; });
/* harmony import */ var _setPrototypeOf__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./setPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js");

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
  if (superClass) Object(_setPrototypeOf__WEBPACK_IMPORTED_MODULE_0__["default"])(subClass, superClass);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _iterableToArrayLimit; });
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

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _nonIterableSpread; });
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectSpread.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _objectSpread; });
/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");

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
      Object(_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(target, key, source[key]);
    });
  }

  return target;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _objectWithoutProperties; });
/* harmony import */ var _objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./objectWithoutPropertiesLoose */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = Object(_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(source, excluded);
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

/***/ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _objectWithoutPropertiesLoose; });
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

/***/ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _possibleConstructorReturn; });
/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../helpers/esm/typeof */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__["default"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__["default"])(self);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _setPrototypeOf; });
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _slicedToArray; });
/* harmony import */ var _arrayWithHoles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithHoles */ "./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js");
/* harmony import */ var _iterableToArrayLimit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArrayLimit */ "./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js");
/* harmony import */ var _nonIterableRest__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./nonIterableRest */ "./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js");



function _slicedToArray(arr, i) {
  return Object(_arrayWithHoles__WEBPACK_IMPORTED_MODULE_0__["default"])(arr) || Object(_iterableToArrayLimit__WEBPACK_IMPORTED_MODULE_1__["default"])(arr, i) || Object(_nonIterableRest__WEBPACK_IMPORTED_MODULE_2__["default"])();
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _toConsumableArray; });
/* harmony import */ var _arrayWithoutHoles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithoutHoles */ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js");
/* harmony import */ var _iterableToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArray */ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js");
/* harmony import */ var _nonIterableSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./nonIterableSpread */ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js");



function _toConsumableArray(arr) {
  return Object(_arrayWithoutHoles__WEBPACK_IMPORTED_MODULE_0__["default"])(arr) || Object(_iterableToArray__WEBPACK_IMPORTED_MODULE_1__["default"])(arr) || Object(_nonIterableSpread__WEBPACK_IMPORTED_MODULE_2__["default"])();
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _typeof; });
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

/***/ "./node_modules/@wordpress/block-library/build-module/archives/edit.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/archives/edit.js ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ArchivesEdit; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function ArchivesEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes;
  var align = attributes.align,
      showPostCounts = attributes.showPostCounts,
      displayAsDropdown = attributes.displayAsDropdown;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Archives Settings')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Display as Dropdown'),
    checked: displayAsDropdown,
    onChange: function onChange() {
      return setAttributes({
        displayAsDropdown: !displayAsDropdown
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show Post Counts'),
    checked: showPostCounts,
    onChange: function onChange() {
      return setAttributes({
        showPostCounts: !showPostCounts
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["BlockAlignmentToolbar"], {
    value: align,
    onChange: function onChange(nextAlign) {
      setAttributes({
        align: nextAlign
      });
    },
    controls: ['left', 'center', 'right']
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Disabled"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["ServerSideRender"], {
    block: "core/archives",
    attributes: attributes
  })));
}


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/archives/index.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/archives/index.js ***!
  \******************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/archives/edit.js");


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var name = 'core/archives';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Archives'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Display a monthly archive of your site’s Posts.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M7 11h2v2H7v-2zm14-5v14l-2 2H5l-2-2V6l2-2h1V2h2v2h8V2h2v2h1l2 2zM5 8h14V6H5v2zm14 12V10H5v10h14zm-4-7h2v-2h-2v2zm-4 0h2v-2h-2v2z"
  }))),
  category: 'widgets',
  supports: {
    html: false
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var align = attributes.align;

    if (['left', 'center', 'right'].includes(align)) {
      return {
        'data-align': align
      };
    }
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: function save() {
    // Handled by PHP.
    return null;
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/audio/edit.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/audio/edit.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/slicedToArray */ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/blob */ "@wordpress/blob");
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blob__WEBPACK_IMPORTED_MODULE_12__);










/**
 * WordPress dependencies
 */





var ALLOWED_MEDIA_TYPES = ['audio'];

var AudioEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__["default"])(AudioEdit, _Component);

  function AudioEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__["default"])(this, AudioEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__["default"])(AudioEdit).apply(this, arguments)); // edit component has its own src in the state so it can be edited
    // without setting the actual value outside of the edit UI

    _this.state = {
      editing: !_this.props.attributes.src
    };
    _this.toggleAttribute = _this.toggleAttribute.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.onSelectURL = _this.onSelectURL.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__["default"])(AudioEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          noticeOperations = _this$props.noticeOperations,
          setAttributes = _this$props.setAttributes;
      var id = attributes.id,
          _attributes$src = attributes.src,
          src = _attributes$src === void 0 ? '' : _attributes$src;

      if (!id && src.indexOf('blob:') === 0) {
        var file = Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_12__["getBlobByURL"])(src);

        if (file) {
          Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["mediaUpload"])({
            filesList: [file],
            onFileChange: function onFileChange(_ref) {
              var _ref2 = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref, 1),
                  _ref2$ = _ref2[0],
                  mediaId = _ref2$.id,
                  url = _ref2$.url;

              setAttributes({
                id: mediaId,
                src: url
              });
            },
            onError: function onError(e) {
              setAttributes({
                src: undefined,
                id: undefined
              });

              _this2.setState({
                editing: true
              });

              noticeOperations.createErrorNotice(e);
            },
            allowedTypes: ALLOWED_MEDIA_TYPES
          });
        }
      }
    }
  }, {
    key: "toggleAttribute",
    value: function toggleAttribute(attribute) {
      var _this3 = this;

      return function (newValue) {
        _this3.props.setAttributes(Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({}, attribute, newValue));
      };
    }
  }, {
    key: "onSelectURL",
    value: function onSelectURL(newSrc) {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      var src = attributes.src; // Set the block's src from the edit component's state, and switch off
      // the editing UI.

      if (newSrc !== src) {
        setAttributes({
          src: newSrc,
          id: undefined
        });
      }

      this.setState({
        editing: false
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this4 = this;

      var _this$props$attribute = this.props.attributes,
          autoplay = _this$props$attribute.autoplay,
          caption = _this$props$attribute.caption,
          loop = _this$props$attribute.loop,
          preload = _this$props$attribute.preload,
          src = _this$props$attribute.src;
      var _this$props3 = this.props,
          setAttributes = _this$props3.setAttributes,
          isSelected = _this$props3.isSelected,
          className = _this$props3.className,
          noticeOperations = _this$props3.noticeOperations,
          noticeUI = _this$props3.noticeUI;
      var editing = this.state.editing;

      var switchToEditing = function switchToEditing() {
        _this4.setState({
          editing: true
        });
      };

      var onSelectAudio = function onSelectAudio(media) {
        if (!media || !media.url) {
          // in this case there was an error and we should continue in the editing state
          // previous attributes should be removed because they may be temporary blob urls
          setAttributes({
            src: undefined,
            id: undefined
          });
          switchToEditing();
          return;
        } // sets the block's attribute and updates the edit component from the
        // selected media, then switches off the editing UI


        setAttributes({
          src: media.url,
          id: media.id
        });

        _this4.setState({
          src: media.url,
          editing: false
        });
      };

      if (editing) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["MediaPlaceholder"], {
          icon: "media-audio",
          labels: {
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Audio'),
            name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('an audio')
          },
          className: className,
          onSelect: onSelectAudio,
          onSelectURL: this.onSelectURL,
          accept: "audio/*",
          allowedTypes: ALLOWED_MEDIA_TYPES,
          value: this.props.attributes,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice
        });
      }
      /* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */


      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Toolbar"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["IconButton"], {
        className: "components-icon-button components-toolbar__control",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Edit audio'),
        onClick: switchToEditing,
        icon: "edit"
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Audio Settings')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Autoplay'),
        onChange: this.toggleAttribute('autoplay'),
        checked: autoplay
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Loop'),
        onChange: this.toggleAttribute('loop'),
        checked: loop
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["SelectControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Preload'),
        value: undefined !== preload ? preload : 'none' // `undefined` is required for the preload attribute to be unset.
        ,
        onChange: function onChange(value) {
          return setAttributes({
            preload: 'none' !== value ? value : undefined
          });
        },
        options: [{
          value: 'auto',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Auto')
        }, {
          value: 'metadata',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Metadata')
        }, {
          value: 'none',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('None')
        }]
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("figure", {
        className: className
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Disabled"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("audio", {
        controls: "controls",
        src: src
      })), (!_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["RichText"].isEmpty(caption) || isSelected) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["RichText"], {
        tagName: "figcaption",
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Write caption…'),
        value: caption,
        onChange: function onChange(value) {
          return setAttributes({
            caption: value
          });
        },
        inlineToolbar: true
      })));
      /* eslint-enable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */
    }
  }]);

  return AudioEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["withNotices"])(AudioEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/audio/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/audio/index.js ***!
  \***************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/audio/edit.js");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/blob */ "@wordpress/blob");
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blob__WEBPACK_IMPORTED_MODULE_5__);


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




var name = 'core/audio';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Audio'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed an audio file and a simple audio player.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M0,0h24v24H0V0z",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "m12 3l0.01 10.55c-0.59-0.34-1.27-0.55-2-0.55-2.22 0-4.01 1.79-4.01 4s1.79 4 4.01 4 3.99-1.79 3.99-4v-10h4v-4h-6zm-1.99 16c-1.1 0-2-0.9-2-2s0.9-2 2-2 2 0.9 2 2-0.9 2-2 2z"
  })),
  category: 'common',
  attributes: {
    src: {
      type: 'string',
      source: 'attribute',
      selector: 'audio',
      attribute: 'src'
    },
    caption: {
      source: 'html',
      selector: 'figcaption'
    },
    id: {
      type: 'number'
    },
    autoplay: {
      type: 'boolean',
      source: 'attribute',
      selector: 'audio',
      attribute: 'autoplay'
    },
    loop: {
      type: 'boolean',
      source: 'attribute',
      selector: 'audio',
      attribute: 'loop'
    },
    preload: {
      type: 'string',
      source: 'attribute',
      selector: 'audio',
      attribute: 'preload'
    }
  },
  transforms: {
    from: [{
      type: 'files',
      isMatch: function isMatch(files) {
        return files.length === 1 && files[0].type.indexOf('audio/') === 0;
      },
      transform: function transform(files) {
        var file = files[0]; // We don't need to upload the media directly here
        // It's already done as part of the `componentDidMount`
        // in the audio block

        var block = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/audio', {
          src: Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_5__["createBlobURL"])(file)
        });
        return block;
      }
    }]
  },
  supports: {
    align: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_3__["default"],
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var autoplay = attributes.autoplay,
        caption = attributes.caption,
        loop = attributes.loop,
        preload = attributes.preload,
        src = attributes.src;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("figure", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("audio", {
      controls: "controls",
      src: src,
      autoPlay: autoplay,
      loop: loop,
      preload: preload
    }), !_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["RichText"].isEmpty(caption) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["RichText"].Content, {
      tagName: "figcaption",
      value: caption
    }));
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/block/edit-panel/index.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/block/edit-panel/index.js ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_10__);








/**
 * WordPress dependencies
 */






var ReusableBlockEditPanel =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(ReusableBlockEditPanel, _Component);

  function ReusableBlockEditPanel() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, ReusableBlockEditPanel);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(ReusableBlockEditPanel).apply(this, arguments));
    _this.titleField = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createRef"])();
    _this.editButton = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createRef"])();
    _this.handleFormSubmit = _this.handleFormSubmit.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.handleTitleChange = _this.handleTitleChange.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.handleTitleKeyDown = _this.handleTitleKeyDown.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(ReusableBlockEditPanel, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      // Select the input text when the form opens.
      if (this.props.isEditing && this.titleField.current) {
        this.titleField.current.select();
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      // Select the input text only once when the form opens.
      if (!prevProps.isEditing && this.props.isEditing) {
        this.titleField.current.select();
      } // Move focus back to the Edit button after pressing the Escape key, Cancel, or Save.


      if ((prevProps.isEditing || prevProps.isSaving) && !this.props.isEditing && !this.props.isSaving) {
        this.editButton.current.focus();
      }
    }
  }, {
    key: "handleFormSubmit",
    value: function handleFormSubmit(event) {
      event.preventDefault();
      this.props.onSave();
    }
  }, {
    key: "handleTitleChange",
    value: function handleTitleChange(event) {
      this.props.onChangeTitle(event.target.value);
    }
  }, {
    key: "handleTitleKeyDown",
    value: function handleTitleKeyDown(event) {
      if (event.keyCode === _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9__["ESCAPE"]) {
        event.stopPropagation();
        this.props.onCancel();
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          isEditing = _this$props.isEditing,
          title = _this$props.title,
          isSaving = _this$props.isSaving,
          onEdit = _this$props.onEdit,
          instanceId = _this$props.instanceId;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Fragment"], null, !isEditing && !isSaving && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("div", {
        className: "reusable-block-edit-panel"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("b", {
        className: "reusable-block-edit-panel__info"
      }, title), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["Button"], {
        ref: this.editButton,
        isLarge: true,
        className: "reusable-block-edit-panel__button",
        onClick: onEdit
      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["__"])('Edit'))), (isEditing || isSaving) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("form", {
        className: "reusable-block-edit-panel",
        onSubmit: this.handleFormSubmit
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("label", {
        htmlFor: "reusable-block-edit-panel__title-".concat(instanceId),
        className: "reusable-block-edit-panel__label"
      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["__"])('Name:')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("input", {
        ref: this.titleField,
        type: "text",
        disabled: isSaving,
        className: "reusable-block-edit-panel__title",
        value: title,
        onChange: this.handleTitleChange,
        onKeyDown: this.handleTitleKeyDown,
        id: "reusable-block-edit-panel__title-".concat(instanceId)
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["Button"], {
        type: "submit",
        isLarge: true,
        isBusy: isSaving,
        disabled: !title || isSaving,
        className: "reusable-block-edit-panel__button"
      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["__"])('Save'))));
    }
  }]);

  return ReusableBlockEditPanel;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_10__["withInstanceId"])(ReusableBlockEditPanel));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/block/edit.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/block/edit.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var _edit_panel__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./edit-panel */ "./node_modules/@wordpress/block-library/build-module/block/edit-panel/index.js");
/* harmony import */ var _indicator__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./indicator */ "./node_modules/@wordpress/block-library/build-module/block/indicator/index.js");










/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */




var ReusableBlockEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__["default"])(ReusableBlockEdit, _Component);

  function ReusableBlockEdit(_ref) {
    var _this;

    var reusableBlock = _ref.reusableBlock;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__["default"])(this, ReusableBlockEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__["default"])(ReusableBlockEdit).apply(this, arguments));
    _this.startEditing = _this.startEditing.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.stopEditing = _this.stopEditing.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.setAttributes = _this.setAttributes.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.setTitle = _this.setTitle.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.save = _this.save.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));

    if (reusableBlock && reusableBlock.isTemporary) {
      // Start in edit mode when we're working with a newly created reusable block
      _this.state = {
        isEditing: true,
        title: reusableBlock.title,
        changedAttributes: {}
      };
    } else {
      // Start in preview mode when we're working with an existing reusable block
      _this.state = {
        isEditing: false,
        title: null,
        changedAttributes: null
      };
    }

    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__["default"])(ReusableBlockEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      if (!this.props.reusableBlock) {
        this.props.fetchReusableBlock();
      }
    }
  }, {
    key: "startEditing",
    value: function startEditing() {
      var reusableBlock = this.props.reusableBlock;
      this.setState({
        isEditing: true,
        title: reusableBlock.title,
        changedAttributes: {}
      });
    }
  }, {
    key: "stopEditing",
    value: function stopEditing() {
      this.setState({
        isEditing: false,
        title: null,
        changedAttributes: null
      });
    }
  }, {
    key: "setAttributes",
    value: function setAttributes(attributes) {
      this.setState(function (prevState) {
        if (prevState.changedAttributes !== null) {
          return {
            changedAttributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, prevState.changedAttributes, attributes)
          };
        }
      });
    }
  }, {
    key: "setTitle",
    value: function setTitle(title) {
      this.setState({
        title: title
      });
    }
  }, {
    key: "save",
    value: function save() {
      var _this$props = this.props,
          reusableBlock = _this$props.reusableBlock,
          onUpdateTitle = _this$props.onUpdateTitle,
          updateAttributes = _this$props.updateAttributes,
          block = _this$props.block,
          onSave = _this$props.onSave;
      var _this$state = this.state,
          title = _this$state.title,
          changedAttributes = _this$state.changedAttributes;

      if (title !== reusableBlock.title) {
        onUpdateTitle(title);
      }

      updateAttributes(block.clientId, changedAttributes);
      onSave();
      this.stopEditing();
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          isSelected = _this$props2.isSelected,
          reusableBlock = _this$props2.reusableBlock,
          block = _this$props2.block,
          isFetching = _this$props2.isFetching,
          isSaving = _this$props2.isSaving;
      var _this$state2 = this.state,
          isEditing = _this$state2.isEditing,
          title = _this$state2.title,
          changedAttributes = _this$state2.changedAttributes;

      if (!reusableBlock && isFetching) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Placeholder"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Spinner"], null));
      }

      if (!reusableBlock || !block) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Placeholder"], null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Block has been deleted or is unavailable.'));
      }

      var element = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["BlockEdit"], Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, this.props, {
        isSelected: isEditing && isSelected,
        clientId: block.clientId,
        name: block.name,
        attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, block.attributes, changedAttributes),
        setAttributes: isEditing ? this.setAttributes : lodash__WEBPACK_IMPORTED_MODULE_9__["noop"]
      }));

      if (!isEditing) {
        element = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Disabled"], null, element);
      }

      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Fragment"], null, (isSelected || isEditing) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_edit_panel__WEBPACK_IMPORTED_MODULE_15__["default"], {
        isEditing: isEditing,
        title: title !== null ? title : reusableBlock.title,
        isSaving: isSaving && !reusableBlock.isTemporary,
        onEdit: this.startEditing,
        onChangeTitle: this.setTitle,
        onSave: this.save,
        onCancel: this.stopEditing
      }), !isSelected && !isEditing && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_indicator__WEBPACK_IMPORTED_MODULE_16__["default"], {
        title: reusableBlock.title
      }), element);
    }
  }]);

  return ReusableBlockEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_14__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_11__["withSelect"])(function (select, ownProps) {
  var _select = select('core/editor'),
      getReusableBlock = _select.getReusableBlock,
      isFetchingReusableBlock = _select.isFetchingReusableBlock,
      isSavingReusableBlock = _select.isSavingReusableBlock,
      getBlock = _select.getBlock;

  var ref = ownProps.attributes.ref;
  var reusableBlock = getReusableBlock(ref);
  return {
    reusableBlock: reusableBlock,
    isFetching: isFetchingReusableBlock(ref),
    isSaving: isSavingReusableBlock(ref),
    block: reusableBlock ? getBlock(reusableBlock.clientId) : null
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_11__["withDispatch"])(function (dispatch, ownProps) {
  var _dispatch = dispatch('core/editor'),
      fetchReusableBlocks = _dispatch.fetchReusableBlocks,
      updateBlockAttributes = _dispatch.updateBlockAttributes,
      updateReusableBlockTitle = _dispatch.updateReusableBlockTitle,
      saveReusableBlock = _dispatch.saveReusableBlock;

  var ref = ownProps.attributes.ref;
  return {
    fetchReusableBlock: Object(lodash__WEBPACK_IMPORTED_MODULE_9__["partial"])(fetchReusableBlocks, ref),
    updateAttributes: updateBlockAttributes,
    onUpdateTitle: Object(lodash__WEBPACK_IMPORTED_MODULE_9__["partial"])(updateReusableBlockTitle, ref),
    onSave: Object(lodash__WEBPACK_IMPORTED_MODULE_9__["partial"])(saveReusableBlock, ref)
  };
})])(ReusableBlockEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/block/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/block/index.js ***!
  \***************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/block/edit.js");
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var name = 'core/block';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__["__"])('Reusable Block'),
  category: 'reusable',
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__["__"])('Create content, and save it to reuse across your site. Update the block, and the changes apply everywhere it’s used.'),
  attributes: {
    ref: {
      type: 'number'
    }
  },
  supports: {
    customClassName: false,
    html: false,
    inserter: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/block/indicator/index.js":
/*!*************************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/block/indicator/index.js ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */



function ReusableBlockIndicator(_ref) {
  var title = _ref.title;
  // translators: %s: title/name of the reusable block
  var tooltipText = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["sprintf"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Reusable Block: %s'), title);
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Tooltip"], {
    text: tooltipText
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
    className: "reusable-block-indicator"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Dashicon"], {
    icon: "controls-repeat"
  })));
}

/* harmony default export */ __webpack_exports__["default"] = (ReusableBlockIndicator);


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/button/edit.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/button/edit.js ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__);









/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






var _window = window,
    getComputedStyle = _window.getComputedStyle;
var FallbackStyles = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_11__["withFallbackStyles"])(function (node, ownProps) {
  var textColor = ownProps.textColor,
      backgroundColor = ownProps.backgroundColor;
  var backgroundColorValue = backgroundColor && backgroundColor.color;
  var textColorValue = textColor && textColor.color; //avoid the use of querySelector if textColor color is known and verify if node is available.

  var textNode = !textColorValue && node ? node.querySelector('[contenteditable="true"]') : null;
  return {
    fallbackBackgroundColor: backgroundColorValue || !node ? undefined : getComputedStyle(node).backgroundColor,
    fallbackTextColor: textColorValue || !textNode ? undefined : getComputedStyle(textNode).color
  };
});

var ButtonEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__["default"])(ButtonEdit, _Component);

  function ButtonEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__["default"])(this, ButtonEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__["default"])(ButtonEdit).apply(this, arguments));
    _this.nodeRef = null;
    _this.bindRef = _this.bindRef.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__["default"])(ButtonEdit, [{
    key: "bindRef",
    value: function bindRef(node) {
      if (!node) {
        return;
      }

      this.nodeRef = node;
    }
  }, {
    key: "render",
    value: function render() {
      var _classnames;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          backgroundColor = _this$props.backgroundColor,
          textColor = _this$props.textColor,
          setBackgroundColor = _this$props.setBackgroundColor,
          setTextColor = _this$props.setTextColor,
          fallbackBackgroundColor = _this$props.fallbackBackgroundColor,
          fallbackTextColor = _this$props.fallbackTextColor,
          setAttributes = _this$props.setAttributes,
          isSelected = _this$props.isSelected,
          className = _this$props.className;
      var text = attributes.text,
          url = attributes.url,
          title = attributes.title;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("div", {
        className: className,
        title: title,
        ref: this.bindRef
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["RichText"], {
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Add text…'),
        value: text,
        onChange: function onChange(value) {
          return setAttributes({
            text: value
          });
        },
        formattingControls: ['bold', 'italic', 'strikethrough'],
        className: classnames__WEBPACK_IMPORTED_MODULE_8___default()('wp-block-button__link', (_classnames = {
          'has-background': backgroundColor.color
        }, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, backgroundColor.class, backgroundColor.class), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, 'has-text-color', textColor.color), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, textColor.class, textColor.class), _classnames)),
        style: {
          backgroundColor: backgroundColor.color,
          color: textColor.color
        },
        keepPlaceholderOnFocus: true
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["PanelColorSettings"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Color Settings'),
        colorSettings: [{
          value: backgroundColor.color,
          onChange: setBackgroundColor,
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Background Color')
        }, {
          value: textColor.color,
          onChange: setTextColor,
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Text Color')
        }]
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["ContrastChecker"], {
        isLargeText: true,
        textColor: textColor.color,
        backgroundColor: backgroundColor.color,
        fallbackBackgroundColor: fallbackBackgroundColor,
        fallbackTextColor: fallbackTextColor
      })))), isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("form", {
        className: "block-library-button__inline-link",
        onSubmit: function onSubmit(event) {
          return event.preventDefault();
        }
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_11__["Dashicon"], {
        icon: "admin-links"
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["URLInput"], {
        value: url,
        onChange: function onChange(value) {
          return setAttributes({
            url: value
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_11__["IconButton"], {
        icon: "editor-break",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Apply'),
        type: "submit"
      })));
    }
  }]);

  return ButtonEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_10__["compose"])([Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["withColors"])('backgroundColor', {
  textColor: 'color'
}), FallbackStyles])(ButtonEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/button/index.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/button/index.js ***!
  \****************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/button/edit.js");




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


var blockAttributes = {
  url: {
    type: 'string',
    source: 'attribute',
    selector: 'a',
    attribute: 'href'
  },
  title: {
    type: 'string',
    source: 'attribute',
    selector: 'a',
    attribute: 'title'
  },
  text: {
    source: 'html',
    selector: 'a'
  },
  backgroundColor: {
    type: 'string'
  },
  textColor: {
    type: 'string'
  },
  customBackgroundColor: {
    type: 'string'
  },
  customTextColor: {
    type: 'string'
  }
};
var name = 'core/button';

var colorsMigration = function colorsMigration(attributes) {
  return Object(lodash__WEBPACK_IMPORTED_MODULE_4__["omit"])(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, attributes, {
    customTextColor: attributes.textColor && '#' === attributes.textColor[0] ? attributes.textColor : undefined,
    customBackgroundColor: attributes.color && '#' === attributes.color[0] ? attributes.color : undefined
  }), ['color', 'textColor']);
};

var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Button'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Want visitors to click to subscribe, buy, or read more? Get their attention with a button.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    d: "M19 6H5L3 8v8l2 2h14l2-2V8l-2-2zm0 10H5V8h14v8z"
  }))),
  category: 'layout',
  attributes: blockAttributes,
  supports: {
    align: true,
    alignWide: false
  },
  styles: [{
    name: 'default',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Rounded'),
    isDefault: true
  }, {
    name: 'outline',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Outline')
  }, {
    name: 'squared',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Squared')
  }],
  edit: _edit__WEBPACK_IMPORTED_MODULE_7__["default"],
  save: function save(_ref) {
    var _classnames;

    var attributes = _ref.attributes;
    var url = attributes.url,
        text = attributes.text,
        title = attributes.title,
        backgroundColor = attributes.backgroundColor,
        textColor = attributes.textColor,
        customBackgroundColor = attributes.customBackgroundColor,
        customTextColor = attributes.customTextColor;
    var textClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getColorClassName"])('color', textColor);
    var backgroundClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getColorClassName"])('background-color', backgroundColor);
    var buttonClasses = classnames__WEBPACK_IMPORTED_MODULE_3___default()('wp-block-button__link', (_classnames = {
      'has-text-color': textColor || customTextColor
    }, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, textClass, textClass), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, 'has-background', backgroundColor || customBackgroundColor), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, backgroundClass, backgroundClass), _classnames));
    var buttonStyle = {
      backgroundColor: backgroundClass ? undefined : customBackgroundColor,
      color: textClass ? undefined : customTextColor
    };
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
      tagName: "a",
      className: buttonClasses,
      href: url,
      title: title,
      style: buttonStyle,
      value: text
    }));
  },
  deprecated: [{
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, Object(lodash__WEBPACK_IMPORTED_MODULE_4__["pick"])(blockAttributes, ['url', 'title', 'text']), {
      color: {
        type: 'string'
      },
      textColor: {
        type: 'string'
      },
      align: {
        type: 'string',
        default: 'none'
      }
    }),
    save: function save(_ref2) {
      var attributes = _ref2.attributes;
      var url = attributes.url,
          text = attributes.text,
          title = attributes.title,
          align = attributes.align,
          color = attributes.color,
          textColor = attributes.textColor;
      var buttonStyle = {
        backgroundColor: color,
        color: textColor
      };
      var linkClass = 'wp-block-button__link';
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
        className: "align".concat(align)
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: "a",
        className: linkClass,
        href: url,
        title: title,
        style: buttonStyle,
        value: text
      }));
    },
    migrate: colorsMigration
  }, {
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, Object(lodash__WEBPACK_IMPORTED_MODULE_4__["pick"])(blockAttributes, ['url', 'title', 'text']), {
      color: {
        type: 'string'
      },
      textColor: {
        type: 'string'
      },
      align: {
        type: 'string',
        default: 'none'
      }
    }),
    save: function save(_ref3) {
      var attributes = _ref3.attributes;
      var url = attributes.url,
          text = attributes.text,
          title = attributes.title,
          align = attributes.align,
          color = attributes.color,
          textColor = attributes.textColor;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
        className: "align".concat(align),
        style: {
          backgroundColor: color
        }
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: "a",
        href: url,
        title: title,
        style: {
          color: textColor
        },
        value: text
      }));
    },
    migrate: colorsMigration
  }]
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/categories/edit.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/categories/edit.js ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__);








/**
 * WordPress dependencies
 */







var CategoriesEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(CategoriesEdit, _Component);

  function CategoriesEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, CategoriesEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(CategoriesEdit).apply(this, arguments));
    _this.toggleDisplayAsDropdown = _this.toggleDisplayAsDropdown.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.toggleShowPostCounts = _this.toggleShowPostCounts.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.toggleShowHierarchy = _this.toggleShowHierarchy.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(CategoriesEdit, [{
    key: "toggleDisplayAsDropdown",
    value: function toggleDisplayAsDropdown() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      var displayAsDropdown = attributes.displayAsDropdown;
      setAttributes({
        displayAsDropdown: !displayAsDropdown
      });
    }
  }, {
    key: "toggleShowPostCounts",
    value: function toggleShowPostCounts() {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      var showPostCounts = attributes.showPostCounts;
      setAttributes({
        showPostCounts: !showPostCounts
      });
    }
  }, {
    key: "toggleShowHierarchy",
    value: function toggleShowHierarchy() {
      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          setAttributes = _this$props3.setAttributes;
      var showHierarchy = attributes.showHierarchy;
      setAttributes({
        showHierarchy: !showHierarchy
      });
    }
  }, {
    key: "getCategories",
    value: function getCategories() {
      var parentId = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var categories = this.props.categories;

      if (!categories || !categories.length) {
        return [];
      }

      if (parentId === null) {
        return categories;
      }

      return categories.filter(function (category) {
        return category.parent === parentId;
      });
    }
  }, {
    key: "getCategoryListClassName",
    value: function getCategoryListClassName(level) {
      var className = this.props.className;
      return "".concat(className, "__list ").concat(className, "__list-level-").concat(level);
    }
  }, {
    key: "renderCategoryName",
    value: function renderCategoryName(category) {
      if (!category.name) {
        return Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('(Untitled)');
      }

      return Object(lodash__WEBPACK_IMPORTED_MODULE_10__["unescape"])(category.name).trim();
    }
  }, {
    key: "renderCategoryList",
    value: function renderCategoryList() {
      var _this2 = this;

      var showHierarchy = this.props.attributes.showHierarchy;
      var parentId = showHierarchy ? 0 : null;
      var categories = this.getCategories(parentId);
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("ul", {
        className: this.getCategoryListClassName(0)
      }, categories.map(function (category) {
        return _this2.renderCategoryListItem(category, 0);
      }));
    }
  }, {
    key: "renderCategoryListItem",
    value: function renderCategoryListItem(category, level) {
      var _this3 = this;

      var _this$props$attribute = this.props.attributes,
          showHierarchy = _this$props$attribute.showHierarchy,
          showPostCounts = _this$props$attribute.showPostCounts;
      var childCategories = this.getCategories(category.id);
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("li", {
        key: category.id
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("a", {
        href: category.link,
        target: "_blank"
      }, this.renderCategoryName(category)), showPostCounts && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("span", {
        className: "".concat(this.props.className, "__post-count")
      }, ' ', "(", category.count, ")"), showHierarchy && !!childCategories.length && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("ul", {
        className: this.getCategoryListClassName(level + 1)
      }, childCategories.map(function (childCategory) {
        return _this3.renderCategoryListItem(childCategory, level + 1);
      })));
    }
  }, {
    key: "renderCategoryDropdown",
    value: function renderCategoryDropdown() {
      var _this4 = this;

      var showHierarchy = this.props.attributes.showHierarchy;
      var parentId = showHierarchy ? 0 : null;
      var categories = this.getCategories(parentId);
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("select", {
        className: "".concat(this.props.className, "__dropdown")
      }, categories.map(function (category) {
        return _this4.renderCategoryDropdownItem(category, 0);
      }));
    }
  }, {
    key: "renderCategoryDropdownItem",
    value: function renderCategoryDropdownItem(category, level) {
      var _this5 = this;

      var _this$props$attribute2 = this.props.attributes,
          showHierarchy = _this$props$attribute2.showHierarchy,
          showPostCounts = _this$props$attribute2.showPostCounts;
      var childCategories = this.getCategories(category.id);
      return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("option", {
        key: category.id
      }, Object(lodash__WEBPACK_IMPORTED_MODULE_10__["times"])(level * 3, function () {
        return '\xa0';
      }), this.renderCategoryName(category), !!showPostCounts ? " (".concat(category.count, ")") : ''), showHierarchy && !!childCategories.length && childCategories.map(function (childCategory) {
        return _this5.renderCategoryDropdownItem(childCategory, level + 1);
      })];
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props4 = this.props,
          attributes = _this$props4.attributes,
          setAttributes = _this$props4.setAttributes,
          isRequesting = _this$props4.isRequesting;
      var align = attributes.align,
          displayAsDropdown = attributes.displayAsDropdown,
          showHierarchy = attributes.showHierarchy,
          showPostCounts = attributes.showPostCounts;
      var inspectorControls = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Categories Settings')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Display as Dropdown'),
        checked: displayAsDropdown,
        onChange: this.toggleDisplayAsDropdown
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Show Hierarchy'),
        checked: showHierarchy,
        onChange: this.toggleShowHierarchy
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Show Post Counts'),
        checked: showPostCounts,
        onChange: this.toggleShowPostCounts
      })));

      if (isRequesting) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Fragment"], null, inspectorControls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["Placeholder"], {
          icon: "admin-post",
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Categories')
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["Spinner"], null)));
      }

      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Fragment"], null, inspectorControls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["BlockAlignmentToolbar"], {
        value: align,
        onChange: function onChange(nextAlign) {
          setAttributes({
            align: nextAlign
          });
        },
        controls: ['left', 'center', 'right', 'full']
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("div", {
        className: this.props.className
      }, displayAsDropdown ? this.renderCategoryDropdown() : this.renderCategoryList()));
    }
  }]);

  return CategoriesEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_8__["withSelect"])(function (select) {
  var _select = select('core'),
      getEntityRecords = _select.getEntityRecords;

  var _select2 = select('core/data'),
      isResolving = _select2.isResolving;

  var query = {
    per_page: -1
  };
  return {
    categories: getEntityRecords('taxonomy', 'category', query),
    isRequesting: isResolving('core', 'getEntityRecords', ['taxonomy', 'category', query])
  };
})(CategoriesEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/categories/index.js":
/*!********************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/categories/index.js ***!
  \********************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/categories/edit.js");


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var name = 'core/categories';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Categories'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Display a list of all your site’s categories.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M0,0h24v24H0V0z",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M12,2l-5.5,9h11L12,2z M12,5.84L13.93,9h-3.87L12,5.84z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "m17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "m3 21.5h8v-8h-8v8zm2-6h4v4h-4v-4z"
  })),
  category: 'widgets',
  attributes: {
    showPostCounts: {
      type: 'boolean',
      default: false
    },
    displayAsDropdown: {
      type: 'boolean',
      default: false
    },
    showHierarchy: {
      type: 'boolean',
      default: false
    },
    align: {
      type: 'string'
    }
  },
  supports: {
    html: false
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var align = attributes.align;

    if ('left' === align || 'right' === align || 'full' === align) {
      return {
        'data-align': align
      };
    }
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/classic/edit.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/classic/edit.js ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ClassicEdit; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9__);









/**
 * WordPress dependencies
 */




function isTmceEmpty(editor) {
  // When tinyMce is empty the content seems to be:
  // <p><br data-mce-bogus="1"></p>
  // avoid expensive checks for large documents
  var body = editor.getBody();

  if (body.childNodes.length > 1) {
    return false;
  } else if (body.childNodes.length === 0) {
    return true;
  }

  if (body.childNodes[0].childNodes.length > 1) {
    return false;
  }

  return /^\n?$/.test(body.innerText || body.textContent);
}

var ClassicEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__["default"])(ClassicEdit, _Component);

  function ClassicEdit(props) {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__["default"])(this, ClassicEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__["default"])(ClassicEdit).call(this, props));
    _this.initialize = _this.initialize.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onSetup = _this.onSetup.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.focus = _this.focus.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__["default"])(ClassicEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _window$wpEditorL10n$ = window.wpEditorL10n.tinymce,
          baseURL = _window$wpEditorL10n$.baseURL,
          suffix = _window$wpEditorL10n$.suffix;
      window.tinymce.EditorManager.overrideDefaults({
        base_url: baseURL,
        suffix: suffix
      });

      if (document.readyState === 'complete') {
        this.initialize();
      } else {
        window.addEventListener('DOMContentLoaded', this.initialize);
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      window.addEventListener('DOMContentLoaded', this.initialize);
      wp.oldEditor.remove("editor-".concat(this.props.clientId));
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this$props = this.props,
          clientId = _this$props.clientId,
          content = _this$props.attributes.content;
      var editor = window.tinymce.get("editor-".concat(clientId));

      if (prevProps.attributes.content !== content) {
        editor.setContent(content || '');
      }
    }
  }, {
    key: "initialize",
    value: function initialize() {
      var clientId = this.props.clientId;
      var settings = window.wpEditorL10n.tinymce.settings;
      wp.oldEditor.initialize("editor-".concat(clientId), {
        tinymce: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, settings, {
          inline: true,
          content_css: false,
          fixed_toolbar_container: "#toolbar-".concat(clientId),
          setup: this.onSetup
        })
      });
    }
  }, {
    key: "onSetup",
    value: function onSetup(editor) {
      var _this2 = this;

      var _this$props2 = this.props,
          content = _this$props2.attributes.content,
          setAttributes = _this$props2.setAttributes;
      var ref = this.ref;
      this.editor = editor; // Disable TinyMCE's keyboard shortcut help.

      editor.on('BeforeExecCommand', function (event) {
        if (event.command === 'WP_Help') {
          event.preventDefault();
        }
      });

      if (content) {
        editor.on('loadContent', function () {
          return editor.setContent(content);
        });
      }

      editor.on('blur', function () {
        setAttributes({
          content: editor.getContent()
        });
        return false;
      });
      editor.on('keydown', function (event) {
        if ((event.keyCode === _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9__["BACKSPACE"] || event.keyCode === _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9__["DELETE"]) && isTmceEmpty(editor)) {
          // delete the block
          _this2.props.onReplace([]);

          event.preventDefault();
          event.stopImmediatePropagation();
        }

        var altKey = event.altKey;
        /*
         * Prevent Mousetrap from kicking in: TinyMCE already uses its own
         * `alt+f10` shortcut to focus its toolbar.
         */

        if (altKey && event.keyCode === _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_9__["F10"]) {
          event.stopPropagation();
        }
      });
      editor.addButton('kitchensink', {
        tooltip: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["_x"])('More', 'button to expand options'),
        icon: 'dashicon dashicons-editor-kitchensink',
        onClick: function onClick() {
          var button = this;
          var active = !button.active();
          button.active(active);
          editor.dom.toggleClass(ref, 'has-advanced-toolbar', active);
        }
      });
      editor.addButton('wp_add_media', {
        tooltip: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["__"])('Insert Media'),
        icon: 'dashicon dashicons-admin-media',
        cmd: 'WP_Medialib'
      });
      editor.on('init', function () {
        var rootNode = _this2.editor.getBody(); // Create the toolbar by refocussing the editor.


        if (document.activeElement === rootNode) {
          rootNode.blur();

          _this2.editor.focus();
        }
      });
    }
  }, {
    key: "focus",
    value: function focus() {
      if (this.editor) {
        this.editor.focus();
      }
    }
  }, {
    key: "onToolbarKeyDown",
    value: function onToolbarKeyDown(event) {
      // Prevent WritingFlow from kicking in and allow arrows navigation on the toolbar.
      event.stopPropagation(); // Prevent Mousetrap from moving focus to the top toolbar when pressing `alt+f10` on this block toolbar.

      event.nativeEvent.stopImmediatePropagation();
    }
  }, {
    key: "render",
    value: function render() {
      var _this3 = this;

      var clientId = this.props.clientId; // Disable reason: the toolbar itself is non-interactive, but must capture
      // events from the KeyboardShortcuts component to stop their propagation.

      /* eslint-disable jsx-a11y/no-static-element-interactions */

      return [// Disable reason: Clicking on this visual placeholder should create
      // the toolbar, it can also be created by focussing the field below.

      /* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/click-events-have-key-events */
      Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("div", {
        key: "toolbar",
        id: "toolbar-".concat(clientId),
        ref: function ref(_ref) {
          return _this3.ref = _ref;
        },
        className: "block-library-classic__toolbar",
        onClick: this.focus,
        "data-placeholder": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["__"])('Classic'),
        onKeyDown: this.onToolbarKeyDown
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("div", {
        key: "editor",
        id: "editor-".concat(clientId),
        className: "wp-block-freeform block-library-rich-text__tinymce"
      })];
      /* eslint-enable jsx-a11y/no-static-element-interactions */
    }
  }]);

  return ClassicEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);




/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/classic/index.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/classic/index.js ***!
  \*****************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/classic/edit.js");


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var name = 'core/freeform';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Classic'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('It’s the classic WordPress editor and it’s a block! Drop the editor right in.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M0,0h24v24H0V0z M0,0h24v24H0V0z",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "m20 7v10h-16v-10h16m0-2h-16c-1.1 0-1.99 0.9-1.99 2l-0.01 10c0 1.1 0.9 2 2 2h16c1.1 0 2-0.9 2-2v-10c0-1.1-0.9-2-2-2z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "11",
    y: "8",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "11",
    y: "11",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "8",
    y: "8",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "8",
    y: "11",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "5",
    y: "11",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "5",
    y: "8",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "8",
    y: "14",
    width: "8",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "14",
    y: "11",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "14",
    y: "8",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "17",
    y: "11",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "17",
    y: "8",
    width: "2",
    height: "2"
  })),
  category: 'formatting',
  attributes: {
    content: {
      type: 'string',
      source: 'html'
    }
  },
  supports: {
    className: false,
    customClassName: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["RawHTML"], null, content);
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/code/edit.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/code/edit.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return CodeEdit; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


function CodeEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      className = _ref.className;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: className
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PlainText"], {
    value: attributes.content,
    onChange: function onChange(content) {
      return setAttributes({
        content: content
      });
    },
    placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Write code…'),
    "aria-label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Code')
  }));
}


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/code/index.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/code/index.js ***!
  \**************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/code/edit.js");


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var name = 'core/code';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Code'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Add text that respects your spacing and tabs -- perfect for displaying code.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M0,0h24v24H0V0z",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M9.4,16.6L4.8,12l4.6-4.6L8,6l-6,6l6,6L9.4,16.6z M14.6,16.6l4.6-4.6l-4.6-4.6L16,6l6,6l-6,6L14.6,16.6z"
  })),
  category: 'formatting',
  attributes: {
    content: {
      type: 'string',
      source: 'text',
      selector: 'code'
    }
  },
  supports: {
    html: false
  },
  transforms: {
    from: [{
      type: 'pattern',
      trigger: 'enter',
      regExp: /^```$/,
      transform: function transform() {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/code');
      }
    }, {
      type: 'raw',
      isMatch: function isMatch(node) {
        return node.nodeName === 'PRE' && node.children.length === 1 && node.firstChild.nodeName === 'CODE';
      },
      schema: {
        pre: {
          children: {
            code: {
              children: {
                '#text': {}
              }
            }
          }
        }
      }
    }]
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_3__["default"],
  save: function save(_ref) {
    var attributes = _ref.attributes;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("pre", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("code", null, attributes.content));
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/columns/column.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/columns/column.js ***!
  \******************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */


var name = 'core/column';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Column'),
  parent: ['core/columns'],
  icon: 'columns',
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('A single column within a columns block.'),
  category: 'common',
  supports: {
    inserter: false
  },
  edit: function edit() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InnerBlocks"], {
      templateLock: false
    });
  },
  save: function save() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InnerBlocks"].Content, null));
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/columns/index.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/columns/index.js ***!
  \*****************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! memize */ "./node_modules/memize/index.js");
/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(memize__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__);



/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */






/**
 * Allowed blocks constant is passed to InnerBlocks precisely as specified here.
 * The contents of the array should never change.
 * The array should contain the name of each block that is allowed.
 * In columns block, the only block we allow is 'core/column'.
 *
 * @constant
 * @type {string[]}
*/

var ALLOWED_BLOCKS = ['core/column'];
/**
 * Returns the layouts configuration for a given number of columns.
 *
 * @param {number} columns Number of columns.
 *
 * @return {Object[]} Columns layout configuration.
 */

var getColumnsTemplate = memize__WEBPACK_IMPORTED_MODULE_4___default()(function (columns) {
  return Object(lodash__WEBPACK_IMPORTED_MODULE_2__["times"])(columns, function () {
    return ['core/column'];
  });
});
var name = 'core/columns';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["sprintf"])(
  /* translators: Block title modifier */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('%1$s (%2$s)'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Columns'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('beta')),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
    d: "M21 4H3L2 5v14l1 1h18l1-1V5l-1-1zM8 18H4V6h4v12zm6 0h-4V6h4v12zm6 0h-4V6h4v12z"
  }))),
  category: 'layout',
  attributes: {
    columns: {
      type: 'number',
      default: 2
    }
  },
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Add a block that displays content in multiple columns, then add whatever content blocks you’d like.'),
  supports: {
    align: ['wide', 'full']
  },
  deprecated: [{
    attributes: {
      columns: {
        type: 'number',
        default: 2
      }
    },
    isEligible: function isEligible(attributes, innerBlocks) {
      return innerBlocks.some(Object(lodash__WEBPACK_IMPORTED_MODULE_2__["property"])(['attributes', 'layout']));
    },
    migrate: function migrate(attributes, innerBlocks) {
      function withoutLayout(block) {
        return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, block, {
          attributes: Object(lodash__WEBPACK_IMPORTED_MODULE_2__["omit"])(block.attributes, ['layout'])
        });
      }

      var columns = innerBlocks.reduce(function (result, innerBlock) {
        var layout = innerBlock.attributes.layout;
        var columnIndex, columnMatch;

        if (layout && (columnMatch = layout.match(/^column-(\d+)$/))) {
          columnIndex = Number(columnMatch[1]) - 1;
        } else {
          columnIndex = 0;
        }

        if (!result[columnIndex]) {
          result[columnIndex] = [];
        }

        result[columnIndex].push(withoutLayout(innerBlock));
        return result;
      }, []);
      var migratedInnerBlocks = columns.map(function (columnBlocks) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/column', {}, columnBlocks);
      });
      return [attributes, migratedInnerBlocks];
    },
    save: function save(_ref) {
      var attributes = _ref.attributes;
      var columns = attributes.columns;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
        className: "has-".concat(columns, "-columns")
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["InnerBlocks"].Content, null));
    }
  }],
  edit: function edit(_ref2) {
    var attributes = _ref2.attributes,
        setAttributes = _ref2.setAttributes,
        className = _ref2.className;
    var columns = attributes.columns;
    var classes = classnames__WEBPACK_IMPORTED_MODULE_3___default()(className, "has-".concat(columns, "-columns"));
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__["RangeControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Columns'),
      value: columns,
      onChange: function onChange(nextColumns) {
        setAttributes({
          columns: nextColumns
        });
      },
      min: 2,
      max: 6
    }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: classes
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["InnerBlocks"], {
      template: getColumnsTemplate(columns),
      templateLock: "all",
      allowedBlocks: ALLOWED_BLOCKS
    })));
  },
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    var columns = attributes.columns;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "has-".concat(columns, "-columns")
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["InnerBlocks"].Content, null));
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/cover-image/index.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/cover-image/index.js ***!
  \*********************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__);




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







var validAlignments = ['left', 'center', 'right', 'wide', 'full'];
var blockAttributes = {
  title: {
    source: 'html',
    selector: 'p'
  },
  url: {
    type: 'string'
  },
  align: {
    type: 'string'
  },
  contentAlign: {
    type: 'string',
    default: 'center'
  },
  id: {
    type: 'number'
  },
  hasParallax: {
    type: 'boolean',
    default: false
  },
  dimRatio: {
    type: 'number',
    default: 50
  },
  overlayColor: {
    type: 'string'
  },
  customOverlayColor: {
    type: 'string'
  }
};
var name = 'core/cover-image';
var ALLOWED_MEDIA_TYPES = ['image'];
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Cover Image'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Add a full-width image, and layer text over it — great for headers.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    d: "M4 4h7V2H4c-1.1 0-2 .9-2 2v7h2V4zm6 9l-4 5h12l-3-4-2.03 2.71L10 13zm7-4.5c0-.83-.67-1.5-1.5-1.5S14 7.67 14 8.5s.67 1.5 1.5 1.5S17 9.33 17 8.5zM20 2h-7v2h7v7h2V4c0-1.1-.9-2-2-2zm0 18h-7v2h7c1.1 0 2-.9 2-2v-7h-2v7zM4 13H2v7c0 1.1.9 2 2 2h7v-2H4v-7z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    d: "M0 0h24v24H0z",
    fill: "none"
  })),
  category: 'common',
  attributes: blockAttributes,
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/heading'],
      transform: function transform(_ref) {
        var content = _ref.content;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/cover-image', {
          title: content
        });
      }
    }, {
      type: 'block',
      blocks: ['core/image'],
      transform: function transform(_ref2) {
        var caption = _ref2.caption,
            url = _ref2.url,
            align = _ref2.align,
            id = _ref2.id;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/cover-image', {
          title: caption,
          url: url,
          align: align,
          id: id
        });
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/heading'],
      transform: function transform(_ref3) {
        var title = _ref3.title;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/heading', {
          content: title
        });
      }
    }, {
      type: 'block',
      blocks: ['core/image'],
      transform: function transform(_ref4) {
        var title = _ref4.title,
            url = _ref4.url,
            align = _ref4.align,
            id = _ref4.id;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/image', {
          caption: title,
          url: url,
          align: align,
          id: id
        });
      }
    }]
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var align = attributes.align;

    if (-1 !== validAlignments.indexOf(align)) {
      return {
        'data-align': align
      };
    }
  },
  edit: Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_7__["compose"])([Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["withColors"])({
    overlayColor: 'background-color'
  }), _wordpress_components__WEBPACK_IMPORTED_MODULE_4__["withNotices"]])(function (_ref5) {
    var attributes = _ref5.attributes,
        setAttributes = _ref5.setAttributes,
        isSelected = _ref5.isSelected,
        className = _ref5.className,
        noticeOperations = _ref5.noticeOperations,
        noticeUI = _ref5.noticeUI,
        overlayColor = _ref5.overlayColor,
        setOverlayColor = _ref5.setOverlayColor;
    var url = attributes.url,
        title = attributes.title,
        align = attributes.align,
        contentAlign = attributes.contentAlign,
        id = attributes.id,
        hasParallax = attributes.hasParallax,
        dimRatio = attributes.dimRatio;

    var updateAlignment = function updateAlignment(nextAlign) {
      return setAttributes({
        align: nextAlign
      });
    };

    var onSelectImage = function onSelectImage(media) {
      if (!media || !media.url) {
        setAttributes({
          url: undefined,
          id: undefined
        });
        return;
      }

      setAttributes({
        url: media.url,
        id: media.id
      });
    };

    var toggleParallax = function toggleParallax() {
      return setAttributes({
        hasParallax: !hasParallax
      });
    };

    var setDimRatio = function setDimRatio(ratio) {
      return setAttributes({
        dimRatio: ratio
      });
    };

    var setTitle = function setTitle(newTitle) {
      return setAttributes({
        title: newTitle
      });
    };

    var style = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, backgroundImageStyles(url), {
      backgroundColor: overlayColor.color
    });

    var classes = classnames__WEBPACK_IMPORTED_MODULE_3___default()(className, contentAlign !== 'center' && "has-".concat(contentAlign, "-content"), dimRatioToClass(dimRatio), {
      'has-background-dim': dimRatio !== 0,
      'has-parallax': hasParallax
    });
    var controls = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["BlockAlignmentToolbar"], {
      value: align,
      onChange: updateAlignment
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["AlignmentToolbar"], {
      value: contentAlign,
      onChange: function onChange(nextAlign) {
        setAttributes({
          contentAlign: nextAlign
        });
      }
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Toolbar"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["MediaUpload"], {
      onSelect: onSelectImage,
      allowedTypes: ALLOWED_MEDIA_TYPES,
      value: id,
      render: function render(_ref6) {
        var open = _ref6.open;
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["IconButton"], {
          className: "components-toolbar__control",
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Edit image'),
          icon: "edit",
          onClick: open
        });
      }
    }))), !!url && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
      title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Cover Image Settings')
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ToggleControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Fixed Background'),
      checked: hasParallax,
      onChange: toggleParallax
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["PanelColorSettings"], {
      title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Overlay'),
      initialOpen: true,
      colorSettings: [{
        value: overlayColor.color,
        onChange: setOverlayColor,
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Overlay Color')
      }]
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["RangeControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Background Opacity'),
      value: dimRatio,
      onChange: setDimRatio,
      min: 0,
      max: 100,
      step: 10
    })))));

    if (!url) {
      var hasTitle = !_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].isEmpty(title);
      var icon = hasTitle ? undefined : 'format-image';
      var label = hasTitle ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"], {
        tagName: "h2",
        value: title,
        onChange: setTitle,
        inlineToolbar: true
      }) : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Cover Image');
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], null, controls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["MediaPlaceholder"], {
        icon: icon,
        className: className,
        labels: {
          title: label,
          name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('an image')
        },
        onSelect: onSelectImage,
        accept: "image/*",
        allowedTypes: ALLOWED_MEDIA_TYPES,
        notices: noticeUI,
        onError: noticeOperations.createErrorNotice
      }));
    }

    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], null, controls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
      "data-url": url,
      style: style,
      className: classes
    }, (!_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].isEmpty(title) || isSelected) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"], {
      tagName: "p",
      className: "wp-block-cover-image-text",
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Write title…'),
      value: title,
      onChange: setTitle,
      inlineToolbar: true
    })));
  }),
  save: function save(_ref7) {
    var attributes = _ref7.attributes,
        className = _ref7.className;
    var url = attributes.url,
        title = attributes.title,
        hasParallax = attributes.hasParallax,
        dimRatio = attributes.dimRatio,
        align = attributes.align,
        contentAlign = attributes.contentAlign,
        overlayColor = attributes.overlayColor,
        customOverlayColor = attributes.customOverlayColor;
    var overlayColorClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["getColorClassName"])('background-color', overlayColor);
    var style = backgroundImageStyles(url);

    if (!overlayColorClass) {
      style.backgroundColor = customOverlayColor;
    }

    var classes = classnames__WEBPACK_IMPORTED_MODULE_3___default()(className, dimRatioToClass(dimRatio), overlayColorClass, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({
      'has-background-dim': dimRatio !== 0,
      'has-parallax': hasParallax
    }, "has-".concat(contentAlign, "-content"), contentAlign !== 'center'), align ? "align".concat(align) : null);
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
      className: classes,
      style: style
    }, !_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].isEmpty(title) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].Content, {
      tagName: "p",
      className: "wp-block-cover-image-text",
      value: title
    }));
  },
  deprecated: [{
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, blockAttributes, {
      title: {
        source: 'html',
        selector: 'h2'
      }
    }),
    save: function save(_ref8) {
      var attributes = _ref8.attributes,
          className = _ref8.className;
      var url = attributes.url,
          title = attributes.title,
          hasParallax = attributes.hasParallax,
          dimRatio = attributes.dimRatio,
          align = attributes.align;
      var style = backgroundImageStyles(url);
      var classes = classnames__WEBPACK_IMPORTED_MODULE_3___default()(className, dimRatioToClass(dimRatio), {
        'has-background-dim': dimRatio !== 0,
        'has-parallax': hasParallax
      }, align ? "align".concat(align) : null);
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("section", {
        className: classes,
        style: style
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].Content, {
        tagName: "h2",
        value: title
      }));
    }
  }]
};

function dimRatioToClass(ratio) {
  return ratio === 0 || ratio === 50 ? null : 'has-background-dim-' + 10 * Math.round(ratio / 10);
}

function backgroundImageStyles(url) {
  return url ? {
    backgroundImage: "url(".concat(url, ")")
  } : {};
}


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/embed/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/embed/index.js ***!
  \***************************************************************************/
/*! exports provided: getEmbedEdit, name, settings, common, others */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getEmbedEdit", function() { return getEmbedEdit; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "common", function() { return common; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "others", function() { return others; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var url__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! url */ "./node_modules/url/url.js");
/* harmony import */ var url__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(url__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var classnames_dedupe__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! classnames/dedupe */ "./node_modules/classnames/dedupe.js");
/* harmony import */ var classnames_dedupe__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(classnames_dedupe__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_17___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_17__);










/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */







 // These embeds do not work in sandboxes

var HOSTS_NO_PREVIEWS = ['facebook.com'];
var ASPECT_RATIOS = [// Common video resolutions.
{
  ratio: '2.33',
  className: 'wp-embed-aspect-21-9'
}, {
  ratio: '2.00',
  className: 'wp-embed-aspect-18-9'
}, {
  ratio: '1.78',
  className: 'wp-embed-aspect-16-9'
}, {
  ratio: '1.33',
  className: 'wp-embed-aspect-4-3'
}, // Vertical video and instagram square video support.
{
  ratio: '1.00',
  className: 'wp-embed-aspect-1-1'
}, {
  ratio: '0.56',
  className: 'wp-embed-aspect-9-16'
}, {
  ratio: '0.50',
  className: 'wp-embed-aspect-1-2'
}];

var matchesPatterns = function matchesPatterns(url) {
  var patterns = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  return patterns.some(function (pattern) {
    return url.match(pattern);
  });
};

var findBlock = function findBlock(url) {
  var _arr = common.concat(others);

  for (var _i = 0; _i < _arr.length; _i++) {
    var block = _arr[_i];

    if (matchesPatterns(url, block.patterns)) {
      return block.name;
    }
  }

  return 'core/embed';
};

function getEmbedEdit(title, icon) {
  return (
    /*#__PURE__*/
    function (_Component) {
      Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__["default"])(_class, _Component);

      function _class() {
        var _this;

        Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__["default"])(this, _class);

        _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__["default"])(_class).apply(this, arguments));
        _this.switchBackToURLInput = _this.switchBackToURLInput.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
        _this.setUrl = _this.setUrl.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
        _this.maybeSwitchBlock = _this.maybeSwitchBlock.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
        _this.setAttributesFromPreview = _this.setAttributesFromPreview.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
        _this.setAspectRatioClassNames = _this.setAspectRatioClassNames.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
        _this.getResponsiveHelp = _this.getResponsiveHelp.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
        _this.toggleResponsive = _this.toggleResponsive.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
        _this.handleIncomingPreview = _this.handleIncomingPreview.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
        _this.state = {
          editingURL: false,
          url: _this.props.attributes.url
        };

        if (_this.props.preview) {
          _this.handleIncomingPreview();
        }

        return _this;
      }

      Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__["default"])(_class, [{
        key: "handleIncomingPreview",
        value: function handleIncomingPreview() {
          this.setAttributesFromPreview();
          this.maybeSwitchBlock();
        }
      }, {
        key: "componentDidUpdate",
        value: function componentDidUpdate(prevProps) {
          var hasPreview = undefined !== this.props.preview;
          var hadPreview = undefined !== prevProps.preview; // We had a preview, and the URL was edited, and the new URL already has a preview fetched.

          var switchedPreview = this.props.preview && this.props.attributes.url !== prevProps.attributes.url;
          var switchedURL = this.props.attributes.url !== prevProps.attributes.url;

          if (switchedURL && this.maybeSwitchBlock()) {
            return;
          }

          if (hasPreview && !hadPreview || switchedPreview) {
            if (this.props.previewIsFallback) {
              this.setState({
                editingURL: true
              });
              return;
            }

            this.handleIncomingPreview();
          }
        }
      }, {
        key: "getPhotoHtml",
        value: function getPhotoHtml(photo) {
          // 100% width for the preview so it fits nicely into the document, some "thumbnails" are
          // acually the full size photo.
          var photoPreview = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("p", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("img", {
            src: photo.thumbnail_url,
            alt: photo.title,
            width: "100%"
          }));
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["renderToString"])(photoPreview);
        }
      }, {
        key: "setUrl",
        value: function setUrl(event) {
          if (event) {
            event.preventDefault();
          }

          var url = this.state.url;
          var setAttributes = this.props.setAttributes;
          this.setState({
            editingURL: false
          });
          setAttributes({
            url: url
          });
        }
        /***
         * Maybe switches to a different embed block type, based on the URL
         * and the HTML in the preview.
         *
         * @return {boolean} Whether the block was switched.
         */

      }, {
        key: "maybeSwitchBlock",
        value: function maybeSwitchBlock() {
          var preview = this.props.preview;
          var url = this.props.attributes.url;

          if (!url) {
            return false;
          }

          var matchingBlock = findBlock(url); // WordPress blocks can work on multiple sites, and so don't have patterns,
          // so if we're in a WordPress block, assume the user has chosen it for a WordPress URL.

          if ('core-embed/wordpress' !== this.props.name && 'core/embed' !== matchingBlock) {
            // At this point, we have discovered a more suitable block for this url, so transform it.
            if (this.props.name !== matchingBlock) {
              this.props.onReplace(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_15__["createBlock"])(matchingBlock, {
                url: url
              }));
              return true;
            }
          }

          if (preview) {
            var html = preview.html; // This indicates it's a WordPress embed, there aren't a set of URL patterns we can use to match WordPress URLs.

            if (Object(lodash__WEBPACK_IMPORTED_MODULE_10__["includes"])(html, 'class="wp-embedded-content" data-secret')) {
              // If this is not the WordPress embed block, transform it into one.
              if (this.props.name !== 'core-embed/wordpress') {
                this.props.onReplace(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_15__["createBlock"])('core-embed/wordpress', {
                  url: url
                }));
                return true;
              }
            }
          }

          return false;
        }
        /**
         * Gets the appropriate CSS class names to enforce an aspect ratio when the embed is resized
         * if the HTML has an iframe with width and height set.
         *
         * @param {string} html The preview HTML that possibly contains an iframe with width and height set.
         * @param {boolean} allowResponsive If the classes should be added, or removed.
         * @return {Object} Object with classnames set for use with `classnames`.
         */

      }, {
        key: "getAspectRatioClassNames",
        value: function getAspectRatioClassNames(html) {
          var allowResponsive = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
          var previewDocument = document.implementation.createHTMLDocument('');
          previewDocument.body.innerHTML = html;
          var iframe = previewDocument.body.querySelector('iframe');

          if (iframe && iframe.height && iframe.width) {
            var aspectRatio = (iframe.width / iframe.height).toFixed(2); // Given the actual aspect ratio, find the widest ratio to support it.

            for (var ratioIndex = 0; ratioIndex < ASPECT_RATIOS.length; ratioIndex++) {
              var potentialRatio = ASPECT_RATIOS[ratioIndex];

              if (aspectRatio >= potentialRatio.ratio) {
                var _ref;

                return _ref = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref, potentialRatio.className, allowResponsive), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref, 'wp-has-aspect-ratio', allowResponsive), _ref;
              }
            }
          }
        }
        /**
         * Sets the aspect ratio related class names returned by `getAspectRatioClassNames`
         * if `allowResponsive` is truthy.
         *
         * @param {string} html The preview HTML.
         */

      }, {
        key: "setAspectRatioClassNames",
        value: function setAspectRatioClassNames(html) {
          var allowResponsive = this.props.attributes.allowResponsive;

          if (!allowResponsive) {
            return;
          }

          var className = classnames_dedupe__WEBPACK_IMPORTED_MODULE_11___default()(this.props.attributes.className, this.getAspectRatioClassNames(html));
          this.props.setAttributes({
            className: className
          });
        }
        /***
         * Sets block attributes based on the preview data.
         */

      }, {
        key: "setAttributesFromPreview",
        value: function setAttributesFromPreview() {
          var _this$props = this.props,
              setAttributes = _this$props.setAttributes,
              preview = _this$props.preview; // Some plugins only return HTML with no type info, so default this to 'rich'.

          var _preview$type = preview.type,
              type = _preview$type === void 0 ? 'rich' : _preview$type; // If we got a provider name from the API, use it for the slug, otherwise we use the title,
          // because not all embed code gives us a provider name.

          var html = preview.html,
              providerName = preview.provider_name;
          var providerNameSlug = Object(lodash__WEBPACK_IMPORTED_MODULE_10__["kebabCase"])(Object(lodash__WEBPACK_IMPORTED_MODULE_10__["toLower"])('' !== providerName ? providerName : title));

          if (Object(lodash__WEBPACK_IMPORTED_MODULE_10__["includes"])(html, 'class="wp-embedded-content" data-secret')) {
            type = 'wp-embed';
          }

          if (html || 'photo' === type) {
            setAttributes({
              type: type,
              providerNameSlug: providerNameSlug
            });
          }

          this.setAspectRatioClassNames(html);
        }
      }, {
        key: "switchBackToURLInput",
        value: function switchBackToURLInput() {
          this.setState({
            editingURL: true
          });
        }
      }, {
        key: "getResponsiveHelp",
        value: function getResponsiveHelp(checked) {
          return checked ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Videos and other content automatically resizes.') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Content is fixed size.');
        }
      }, {
        key: "toggleResponsive",
        value: function toggleResponsive() {
          var _this$props$attribute = this.props.attributes,
              allowResponsive = _this$props$attribute.allowResponsive,
              className = _this$props$attribute.className;
          var html = this.props.preview.html;
          var responsiveClassNames = this.getAspectRatioClassNames(html, !allowResponsive);
          this.props.setAttributes({
            allowResponsive: !allowResponsive,
            className: classnames_dedupe__WEBPACK_IMPORTED_MODULE_11___default()(className, responsiveClassNames)
          });
        }
      }, {
        key: "render",
        value: function render() {
          var _this2 = this;

          var _this$state = this.state,
              url = _this$state.url,
              editingURL = _this$state.editingURL;
          var _this$props$attribute2 = this.props.attributes,
              caption = _this$props$attribute2.caption,
              type = _this$props$attribute2.type,
              allowResponsive = _this$props$attribute2.allowResponsive;
          var _this$props2 = this.props,
              fetching = _this$props2.fetching,
              setAttributes = _this$props2.setAttributes,
              isSelected = _this$props2.isSelected,
              className = _this$props2.className,
              preview = _this$props2.preview,
              previewIsFallback = _this$props2.previewIsFallback; // We have a URL, but couldn't get a preview, or the preview was the oEmbed fallback.

          var cannotEmbed = url && (!preview || previewIsFallback);
          var controls = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["Toolbar"], null, preview && !previewIsFallback && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["IconButton"], {
            className: "components-toolbar__control",
            label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Edit URL'),
            icon: "edit",
            onClick: this.switchBackToURLInput
          }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["PanelBody"], {
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Media Settings'),
            className: "blocks-responsive"
          }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["ToggleControl"], {
            label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Automatically scale content'),
            checked: allowResponsive,
            help: this.getResponsiveHelp,
            onChange: this.toggleResponsive
          }))));

          if (fetching) {
            return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("div", {
              className: "wp-block-embed is-loading"
            }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["Spinner"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Embedding…')));
          } // translators: %s: type of embed e.g: "YouTube", "Twitter", etc. "Embed" is used when no specific type exists


          var label = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["sprintf"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('%s URL'), title);

          if (!preview || previewIsFallback || editingURL) {
            return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["Placeholder"], {
              icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["BlockIcon"], {
                icon: icon,
                showColors: true
              }),
              label: label,
              className: "wp-block-embed"
            }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("form", {
              onSubmit: this.setUrl
            }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("input", {
              type: "url",
              value: url || '',
              className: "components-placeholder__input",
              "aria-label": label,
              placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Enter URL to embed here…'),
              onChange: function onChange(event) {
                return _this2.setState({
                  url: event.target.value
                });
              }
            }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["Button"], {
              isLarge: true,
              type: "submit"
            }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Embed')), cannotEmbed && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("p", {
              className: "components-placeholder__error"
            }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Sorry, we could not embed that content.'))));
          }

          var html = 'photo' === type ? this.getPhotoHtml(preview) : preview.html;
          var scripts = preview.scripts;
          var parsedUrl = Object(url__WEBPACK_IMPORTED_MODULE_9__["parse"])(url);
          var cannotPreview = Object(lodash__WEBPACK_IMPORTED_MODULE_10__["includes"])(HOSTS_NO_PREVIEWS, parsedUrl.host.replace(/^www\./, '')); // translators: %s: host providing embed content e.g: www.youtube.com

          var iframeTitle = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["sprintf"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Embedded content from %s'), parsedUrl.host);
          var sandboxClassnames = classnames_dedupe__WEBPACK_IMPORTED_MODULE_11___default()(type, className);
          var embedWrapper = 'wp-embed' === type ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("div", {
            className: "wp-block-embed__wrapper",
            dangerouslySetInnerHTML: {
              __html: html
            }
          }) : Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("div", {
            className: "wp-block-embed__wrapper"
          }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["SandBox"], {
            html: html,
            scripts: scripts,
            title: iframeTitle,
            type: sandboxClassnames
          }));
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("figure", {
            className: classnames_dedupe__WEBPACK_IMPORTED_MODULE_11___default()(className, 'wp-block-embed', {
              'is-video': 'video' === type
            })
          }, controls, cannotPreview ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["Placeholder"], {
            icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["BlockIcon"], {
              icon: icon,
              showColors: true
            }),
            label: label
          }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("p", {
            className: "components-placeholder__error"
          }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("a", {
            href: url
          }, url)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("p", {
            className: "components-placeholder__error"
          }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Previews for this are unavailable in the editor, sorry!'))) : embedWrapper, (!_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["RichText"].isEmpty(caption) || isSelected) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["RichText"], {
            tagName: "figcaption",
            placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Write caption…'),
            value: caption,
            onChange: function onChange(value) {
              return setAttributes({
                caption: value
              });
            },
            inlineToolbar: true
          }));
        }
      }]);

      return _class;
    }(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Component"])
  );
}
var embedAttributes = {
  url: {
    type: 'string'
  },
  caption: {
    source: 'html',
    selector: 'figcaption'
  },
  type: {
    type: 'string'
  },
  providerNameSlug: {
    type: 'string'
  },
  allowResponsive: {
    type: 'boolean',
    default: true
  }
};

function getEmbedBlockSettings(_ref2) {
  var title = _ref2.title,
      description = _ref2.description,
      icon = _ref2.icon,
      _ref2$category = _ref2.category,
      category = _ref2$category === void 0 ? 'embed' : _ref2$category,
      transforms = _ref2.transforms,
      _ref2$keywords = _ref2.keywords,
      keywords = _ref2$keywords === void 0 ? [] : _ref2$keywords,
      _ref2$supports = _ref2.supports,
      supports = _ref2$supports === void 0 ? {} : _ref2$supports;
  // translators: %s: Name of service (e.g. VideoPress, YouTube)
  var blockDescription = description || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["sprintf"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Add a block that displays content pulled from other sites, like Twitter, Instagram or YouTube.'), title);
  return {
    title: title,
    description: blockDescription,
    icon: icon,
    category: category,
    keywords: keywords,
    attributes: embedAttributes,
    supports: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({
      align: true
    }, supports),
    transforms: transforms,
    edit: Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_13__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_17__["withSelect"])(function (select, ownProps) {
      var url = ownProps.attributes.url;
      var core = select('core');
      var getEmbedPreview = core.getEmbedPreview,
          isPreviewEmbedFallback = core.isPreviewEmbedFallback,
          isRequestingEmbedPreview = core.isRequestingEmbedPreview;
      var preview = url && getEmbedPreview(url);
      var previewIsFallback = url && isPreviewEmbedFallback(url);
      var fetching = undefined !== url && isRequestingEmbedPreview(url); // Some WordPress URLs that can't be embedded will cause the API to return
      // a valid JSON response with no HTML and `data.status` set to 404, rather
      // than generating a fallback response as other embeds do.

      var validPreview = preview && !(preview.data && preview.data.status === 404);
      return {
        preview: validPreview && preview,
        previewIsFallback: previewIsFallback,
        fetching: fetching
      };
    }))(getEmbedEdit(title, icon)),
    save: function save(_ref3) {
      var _classnames;

      var attributes = _ref3.attributes;
      var url = attributes.url,
          caption = attributes.caption,
          type = attributes.type,
          providerNameSlug = attributes.providerNameSlug;

      if (!url) {
        return null;
      }

      var embedClassName = classnames_dedupe__WEBPACK_IMPORTED_MODULE_11___default()('wp-block-embed', (_classnames = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_classnames, "is-type-".concat(type), type), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_classnames, "is-provider-".concat(providerNameSlug), providerNameSlug), _classnames));
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("figure", {
        className: embedClassName
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("div", {
        className: "wp-block-embed__wrapper"
      }, "\n".concat(url, "\n")
      /* URL needs to be on its own line. */
      ), !_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["RichText"].isEmpty(caption) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["RichText"].Content, {
        tagName: "figcaption",
        value: caption
      }));
    },
    deprecated: [{
      attributes: embedAttributes,
      save: function save(_ref4) {
        var _classnames2;

        var attributes = _ref4.attributes;
        var url = attributes.url,
            caption = attributes.caption,
            type = attributes.type,
            providerNameSlug = attributes.providerNameSlug;

        if (!url) {
          return null;
        }

        var embedClassName = classnames_dedupe__WEBPACK_IMPORTED_MODULE_11___default()('wp-block-embed', (_classnames2 = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_classnames2, "is-type-".concat(type), type), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_classnames2, "is-provider-".concat(providerNameSlug), providerNameSlug), _classnames2));
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("figure", {
          className: embedClassName
        }, "\n".concat(url, "\n")
        /* URL needs to be on its own line. */
        , !_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["RichText"].isEmpty(caption) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["RichText"].Content, {
          tagName: "figcaption",
          value: caption
        }));
      }
    }]
  };
}

var name = 'core/embed';
var embedContentIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
  d: "M19,4H5C3.89,4,3,4.9,3,6v12c0,1.1,0.89,2,2,2h14c1.1,0,2-0.9,2-2V6C21,4.9,20.11,4,19,4z M19,18H5V8h14V18z"
}));
var embedAudioIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
  d: "M21 3H3L1 5v14l2 2h18l2-2V5l-2-2zm0 16H3V5h18v14zM8 15a3 3 0 0 1 4-3V6h5v2h-3v7a3 3 0 0 1-6 0z"
}));
var embedPhotoIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
  d: "M21,4H3C1.9,4,1,4.9,1,6v12c0,1.1,0.9,2,2,2h18c1.1,0,2-0.9,2-2V6C23,4.9,22.1,4,21,4z M21,18H3V6h18V18z"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("polygon", {
  points: "14.5 11 11 15.51 8.5 12.5 5 17 19 17"
}));
var embedVideoIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
  d: "m10 8v8l5-4-5-4zm9-5h-14c-1.1 0-2 0.9-2 2v14c0 1.1 0.9 2 2 2h14c1.1 0 2-0.9 2-2v-14c0-1.1-0.9-2-2-2zm0 16h-14v-14h14v14z"
}));
var settings = getEmbedBlockSettings({
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Embed'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('The Embed block allows you to easily add videos, images, tweets, audio, and other content to your post or page.'),
  icon: embedContentIcon,
  transforms: {
    from: [{
      type: 'raw',
      isMatch: function isMatch(node) {
        return node.nodeName === 'P' && /^\s*(https?:\/\/\S+)\s*$/i.test(node.textContent);
      },
      transform: function transform(node) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_15__["createBlock"])('core/embed', {
          url: node.textContent.trim()
        });
      }
    }]
  }
});
var common = [{
  name: 'core-embed/twitter',
  settings: getEmbedBlockSettings({
    title: 'Twitter',
    icon: {
      foreground: '#1da1f2',
      src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
        xmlns: "http://www.w3.org/2000/svg",
        viewBox: "0 0 24 24"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
        d: "M22.23 5.924c-.736.326-1.527.547-2.357.646.847-.508 1.498-1.312 1.804-2.27-.793.47-1.67.812-2.606.996C18.325 4.498 17.258 4 16.078 4c-2.266 0-4.103 1.837-4.103 4.103 0 .322.036.635.106.935-3.41-.17-6.433-1.804-8.457-4.287-.353.607-.556 1.312-.556 2.064 0 1.424.724 2.68 1.825 3.415-.673-.022-1.305-.207-1.86-.514v.052c0 1.988 1.415 3.647 3.293 4.023-.344.095-.707.145-1.08.145-.265 0-.522-.026-.773-.074.522 1.63 2.038 2.817 3.833 2.85-1.404 1.1-3.174 1.757-5.096 1.757-.332 0-.66-.02-.98-.057 1.816 1.164 3.973 1.843 6.29 1.843 7.547 0 11.675-6.252 11.675-11.675 0-.178-.004-.355-.012-.53.802-.578 1.497-1.3 2.047-2.124z"
      })))
    },
    keywords: ['tweet']
  }),
  patterns: [/^https?:\/\/(www\.)?twitter\.com\/.+/i]
}, {
  name: 'core-embed/youtube',
  settings: getEmbedBlockSettings({
    title: 'YouTube',
    icon: {
      foreground: '#ff0000',
      src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
        viewBox: "0 0 24 24"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
        d: "M21.8 8s-.195-1.377-.795-1.984c-.76-.797-1.613-.8-2.004-.847-2.798-.203-6.996-.203-6.996-.203h-.01s-4.197 0-6.996.202c-.39.046-1.242.05-2.003.846C2.395 6.623 2.2 8 2.2 8S2 9.62 2 11.24v1.517c0 1.618.2 3.237.2 3.237s.195 1.378.795 1.985c.76.797 1.76.77 2.205.855 1.6.153 6.8.2 6.8.2s4.203-.005 7-.208c.392-.047 1.244-.05 2.005-.847.6-.607.795-1.985.795-1.985s.2-1.618.2-3.237v-1.517C22 9.62 21.8 8 21.8 8zM9.935 14.595v-5.62l5.403 2.82-5.403 2.8z"
      }))
    },
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('music'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('video')]
  }),
  patterns: [/^https?:\/\/((m|www)\.)?youtube\.com\/.+/i, /^https?:\/\/youtu\.be\/.+/i]
}, {
  name: 'core-embed/facebook',
  settings: getEmbedBlockSettings({
    title: 'Facebook',
    icon: {
      foreground: '#3b5998',
      src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
        viewBox: "0 0 24 24"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
        d: "M20 3H4c-.6 0-1 .4-1 1v16c0 .5.4 1 1 1h8.6v-7h-2.3v-2.7h2.3v-2c0-2.3 1.4-3.6 3.5-3.6 1 0 1.8.1 2.1.1v2.4h-1.4c-1.1 0-1.3.5-1.3 1.3v1.7h2.7l-.4 2.8h-2.3v7H20c.5 0 1-.4 1-1V4c0-.6-.4-1-1-1z"
      }))
    }
  }),
  patterns: [/^https?:\/\/www\.facebook.com\/.+/i]
}, {
  name: 'core-embed/instagram',
  settings: getEmbedBlockSettings({
    title: 'Instagram',
    icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
      viewBox: "0 0 24 24"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
      d: "M12 4.622c2.403 0 2.688.01 3.637.052.877.04 1.354.187 1.67.31.42.163.72.358 1.036.673.315.315.51.615.673 1.035.123.317.27.794.31 1.67.043.95.052 1.235.052 3.638s-.01 2.688-.052 3.637c-.04.877-.187 1.354-.31 1.67-.163.42-.358.72-.673 1.036-.315.315-.615.51-1.035.673-.317.123-.794.27-1.67.31-.95.043-1.234.052-3.638.052s-2.688-.01-3.637-.052c-.877-.04-1.354-.187-1.67-.31-.42-.163-.72-.358-1.036-.673-.315-.315-.51-.615-.673-1.035-.123-.317-.27-.794-.31-1.67-.043-.95-.052-1.235-.052-3.638s.01-2.688.052-3.637c.04-.877.187-1.354.31-1.67.163-.42.358-.72.673-1.036.315-.315.615-.51 1.035-.673.317-.123.794-.27 1.67-.31.95-.043 1.235-.052 3.638-.052M12 3c-2.444 0-2.75.01-3.71.054s-1.613.196-2.185.418c-.592.23-1.094.538-1.594 1.04-.5.5-.807 1-1.037 1.593-.223.572-.375 1.226-.42 2.184C3.01 9.25 3 9.555 3 12s.01 2.75.054 3.71.196 1.613.418 2.186c.23.592.538 1.094 1.038 1.594s1.002.808 1.594 1.038c.572.222 1.227.375 2.185.418.96.044 1.266.054 3.71.054s2.75-.01 3.71-.054 1.613-.196 2.186-.418c.592-.23 1.094-.538 1.594-1.038s.808-1.002 1.038-1.594c.222-.572.375-1.227.418-2.185.044-.96.054-1.266.054-3.71s-.01-2.75-.054-3.71-.196-1.613-.418-2.186c-.23-.592-.538-1.094-1.038-1.594s-1.002-.808-1.594-1.038c-.572-.222-1.227-.375-2.185-.418C14.75 3.01 14.445 3 12 3zm0 4.378c-2.552 0-4.622 2.07-4.622 4.622s2.07 4.622 4.622 4.622 4.622-2.07 4.622-4.622S14.552 7.378 12 7.378zM12 15c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3zm4.804-8.884c-.596 0-1.08.484-1.08 1.08s.484 1.08 1.08 1.08c.596 0 1.08-.484 1.08-1.08s-.483-1.08-1.08-1.08z"
    }))),
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('image')]
  }),
  patterns: [/^https?:\/\/(www\.)?instagr(\.am|am\.com)\/.+/i]
}, {
  name: 'core-embed/wordpress',
  settings: getEmbedBlockSettings({
    title: 'WordPress',
    icon: {
      foreground: '#0073AA',
      src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
        viewBox: "0 0 24 24"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
        d: "M12.158 12.786l-2.698 7.84c.806.236 1.657.365 2.54.365 1.047 0 2.05-.18 2.986-.51-.024-.037-.046-.078-.065-.123l-2.762-7.57zM3.008 12c0 3.56 2.07 6.634 5.068 8.092L3.788 8.342c-.5 1.117-.78 2.354-.78 3.658zm15.06-.454c0-1.112-.398-1.88-.74-2.48-.456-.74-.883-1.368-.883-2.11 0-.825.627-1.595 1.51-1.595.04 0 .078.006.116.008-1.598-1.464-3.73-2.36-6.07-2.36-3.14 0-5.904 1.613-7.512 4.053.21.008.41.012.58.012.94 0 2.395-.114 2.395-.114.484-.028.54.684.057.74 0 0-.487.058-1.03.086l3.275 9.74 1.968-5.902-1.4-3.838c-.485-.028-.944-.085-.944-.085-.486-.03-.43-.77.056-.742 0 0 1.484.114 2.368.114.94 0 2.397-.114 2.397-.114.486-.028.543.684.058.74 0 0-.488.058-1.03.086l3.25 9.665.897-2.997c.456-1.17.684-2.137.684-2.907zm1.82-3.86c.04.286.06.593.06.924 0 .912-.17 1.938-.683 3.22l-2.746 7.94c2.672-1.558 4.47-4.454 4.47-7.77 0-1.564-.4-3.033-1.1-4.314zM12 22C6.486 22 2 17.514 2 12S6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"
      })))
    },
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('post'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('blog')]
  })
}, {
  name: 'core-embed/soundcloud',
  settings: getEmbedBlockSettings({
    title: 'SoundCloud',
    icon: embedAudioIcon,
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('music'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('audio')]
  }),
  patterns: [/^https?:\/\/(www\.)?soundcloud\.com\/.+/i]
}, {
  name: 'core-embed/spotify',
  settings: getEmbedBlockSettings({
    title: 'Spotify',
    icon: {
      foreground: '#1db954',
      src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
        viewBox: "0 0 24 24"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
        d: "M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2m4.586 14.424c-.18.295-.563.387-.857.207-2.35-1.434-5.305-1.76-8.786-.963-.335.077-.67-.133-.746-.47-.077-.334.132-.67.47-.745 3.808-.87 7.076-.496 9.712 1.115.293.18.386.563.206.857M17.81 13.7c-.226.367-.706.482-1.072.257-2.687-1.652-6.785-2.13-9.965-1.166-.413.127-.848-.106-.973-.517-.125-.413.108-.848.52-.973 3.632-1.102 8.147-.568 11.234 1.328.366.226.48.707.256 1.072m.105-2.835C14.692 8.95 9.375 8.775 6.297 9.71c-.493.15-1.016-.13-1.166-.624-.148-.495.13-1.017.625-1.167 3.532-1.073 9.404-.866 13.115 1.337.445.264.59.838.327 1.282-.264.443-.838.59-1.282.325"
      }))
    },
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('music'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('audio')]
  }),
  patterns: [/^https?:\/\/(open|play)\.spotify\.com\/.+/i]
}, {
  name: 'core-embed/flickr',
  settings: getEmbedBlockSettings({
    title: 'Flickr',
    icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
      viewBox: "0 0 24 24"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
      d: "m6.5 7c-2.75 0-5 2.25-5 5s2.25 5 5 5 5-2.25 5-5-2.25-5-5-5zm11 0c-2.75 0-5 2.25-5 5s2.25 5 5 5 5-2.25 5-5-2.25-5-5-5z"
    })),
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('image')]
  }),
  patterns: [/^https?:\/\/(www\.)?flickr\.com\/.+/i, /^https?:\/\/flic\.kr\/.+/i]
}, {
  name: 'core-embed/vimeo',
  settings: getEmbedBlockSettings({
    title: 'Vimeo',
    icon: {
      foreground: '#1ab7ea',
      src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
        xmlns: "http://www.w3.org/2000/svg",
        viewBox: "0 0 24 24"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
        d: "M22.396 7.164c-.093 2.026-1.507 4.8-4.245 8.32C15.323 19.16 12.93 21 10.97 21c-1.214 0-2.24-1.12-3.08-3.36-.56-2.052-1.118-4.105-1.68-6.158-.622-2.24-1.29-3.36-2.004-3.36-.156 0-.7.328-1.634.98l-.978-1.26c1.027-.903 2.04-1.806 3.037-2.71C6 3.95 7.03 3.328 7.716 3.265c1.62-.156 2.616.95 2.99 3.32.404 2.558.685 4.148.84 4.77.468 2.12.982 3.18 1.543 3.18.435 0 1.09-.687 1.963-2.064.872-1.376 1.34-2.422 1.402-3.142.125-1.187-.343-1.782-1.4-1.782-.5 0-1.013.115-1.542.34 1.023-3.35 2.977-4.976 5.862-4.883 2.14.063 3.148 1.45 3.024 4.16z"
      })))
    },
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('video')]
  }),
  patterns: [/^https?:\/\/(www\.)?vimeo\.com\/.+/i]
}];
var others = [{
  name: 'core-embed/animoto',
  settings: getEmbedBlockSettings({
    title: 'Animoto',
    icon: embedVideoIcon
  }),
  patterns: [/^https?:\/\/(www\.)?(animoto|video214)\.com\/.+/i]
}, {
  name: 'core-embed/cloudup',
  settings: getEmbedBlockSettings({
    title: 'Cloudup',
    icon: embedContentIcon
  }),
  patterns: [/^https?:\/\/cloudup\.com\/.+/i]
}, {
  name: 'core-embed/collegehumor',
  settings: getEmbedBlockSettings({
    title: 'CollegeHumor',
    icon: embedVideoIcon
  }),
  patterns: [/^https?:\/\/(www\.)?collegehumor\.com\/.+/i]
}, {
  name: 'core-embed/dailymotion',
  settings: getEmbedBlockSettings({
    title: 'Dailymotion',
    icon: embedVideoIcon
  }),
  patterns: [/^https?:\/\/(www\.)?dailymotion\.com\/.+/i]
}, {
  name: 'core-embed/funnyordie',
  settings: getEmbedBlockSettings({
    title: 'Funny or Die',
    icon: embedVideoIcon
  }),
  patterns: [/^https?:\/\/(www\.)?funnyordie\.com\/.+/i]
}, {
  name: 'core-embed/hulu',
  settings: getEmbedBlockSettings({
    title: 'Hulu',
    icon: embedVideoIcon
  }),
  patterns: [/^https?:\/\/(www\.)?hulu\.com\/.+/i]
}, {
  name: 'core-embed/imgur',
  settings: getEmbedBlockSettings({
    title: 'Imgur',
    icon: embedPhotoIcon
  }),
  patterns: [/^https?:\/\/(.+\.)?imgur\.com\/.+/i]
}, {
  name: 'core-embed/issuu',
  settings: getEmbedBlockSettings({
    title: 'Issuu',
    icon: embedContentIcon
  }),
  patterns: [/^https?:\/\/(www\.)?issuu\.com\/.+/i]
}, {
  name: 'core-embed/kickstarter',
  settings: getEmbedBlockSettings({
    title: 'Kickstarter',
    icon: embedContentIcon
  }),
  patterns: [/^https?:\/\/(www\.)?kickstarter\.com\/.+/i, /^https?:\/\/kck\.st\/.+/i]
}, {
  name: 'core-embed/meetup-com',
  settings: getEmbedBlockSettings({
    title: 'Meetup.com',
    icon: embedContentIcon
  }),
  patterns: [/^https?:\/\/(www\.)?meetu(\.ps|p\.com)\/.+/i]
}, {
  name: 'core-embed/mixcloud',
  settings: getEmbedBlockSettings({
    title: 'Mixcloud',
    icon: embedAudioIcon,
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('music'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('audio')]
  }),
  patterns: [/^https?:\/\/(www\.)?mixcloud\.com\/.+/i]
}, {
  name: 'core-embed/photobucket',
  settings: getEmbedBlockSettings({
    title: 'Photobucket',
    icon: embedPhotoIcon
  }),
  patterns: [/^http:\/\/g?i*\.photobucket\.com\/.+/i]
}, {
  name: 'core-embed/polldaddy',
  settings: getEmbedBlockSettings({
    title: 'Polldaddy',
    icon: embedContentIcon
  }),
  patterns: [/^https?:\/\/(www\.)?polldaddy\.com\/.+/i]
}, {
  name: 'core-embed/reddit',
  settings: getEmbedBlockSettings({
    title: 'Reddit',
    icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
      viewBox: "0 0 24 24"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
      d: "M22 11.816c0-1.256-1.02-2.277-2.277-2.277-.593 0-1.122.24-1.526.613-1.48-.965-3.455-1.594-5.647-1.69l1.17-3.702 3.18.75c.01 1.027.847 1.86 1.877 1.86 1.035 0 1.877-.84 1.877-1.877 0-1.035-.842-1.877-1.877-1.877-.77 0-1.43.466-1.72 1.13L13.55 3.92c-.204-.047-.4.067-.46.26l-1.35 4.27c-2.317.037-4.412.67-5.97 1.67-.402-.355-.917-.58-1.493-.58C3.02 9.54 2 10.56 2 11.815c0 .814.433 1.523 1.078 1.925-.037.222-.06.445-.06.673 0 3.292 4.01 5.97 8.94 5.97s8.94-2.678 8.94-5.97c0-.214-.02-.424-.052-.632.687-.39 1.154-1.12 1.154-1.964zm-3.224-7.422c.606 0 1.1.493 1.1 1.1s-.493 1.1-1.1 1.1-1.1-.494-1.1-1.1.493-1.1 1.1-1.1zm-16 7.422c0-.827.673-1.5 1.5-1.5.313 0 .598.103.838.27-.85.675-1.477 1.478-1.812 2.36-.32-.274-.525-.676-.525-1.13zm9.183 7.79c-4.502 0-8.165-2.33-8.165-5.193S7.457 9.22 11.96 9.22s8.163 2.33 8.163 5.193-3.663 5.193-8.164 5.193zM20.635 13c-.326-.89-.948-1.7-1.797-2.383.247-.186.55-.3.882-.3.827 0 1.5.672 1.5 1.5 0 .482-.23.91-.586 1.184zm-11.64 1.704c-.76 0-1.397-.616-1.397-1.376 0-.76.636-1.397 1.396-1.397.76 0 1.376.638 1.376 1.398 0 .76-.616 1.376-1.376 1.376zm7.405-1.376c0 .76-.615 1.376-1.375 1.376s-1.4-.616-1.4-1.376c0-.76.64-1.397 1.4-1.397.76 0 1.376.638 1.376 1.398zm-1.17 3.38c.15.152.15.398 0 .55-.675.674-1.728 1.002-3.22 1.002l-.01-.002-.012.002c-1.492 0-2.544-.328-3.218-1.002-.152-.152-.152-.398 0-.55.152-.152.4-.15.55 0 .52.52 1.394.775 2.67.775l.01.002.01-.002c1.276 0 2.15-.253 2.67-.775.15-.152.398-.152.55 0z"
    }))
  }),
  patterns: [/^https?:\/\/(www\.)?reddit\.com\/.+/i]
}, {
  name: 'core-embed/reverbnation',
  settings: getEmbedBlockSettings({
    title: 'ReverbNation',
    icon: embedAudioIcon
  }),
  patterns: [/^https?:\/\/(www\.)?reverbnation\.com\/.+/i]
}, {
  name: 'core-embed/screencast',
  settings: getEmbedBlockSettings({
    title: 'Screencast',
    icon: embedVideoIcon
  }),
  patterns: [/^https?:\/\/(www\.)?screencast\.com\/.+/i]
}, {
  name: 'core-embed/scribd',
  settings: getEmbedBlockSettings({
    title: 'Scribd',
    icon: embedContentIcon
  }),
  patterns: [/^https?:\/\/(www\.)?scribd\.com\/.+/i]
}, {
  name: 'core-embed/slideshare',
  settings: getEmbedBlockSettings({
    title: 'Slideshare',
    icon: embedContentIcon
  }),
  patterns: [/^https?:\/\/(.+?\.)?slideshare\.net\/.+/i]
}, {
  name: 'core-embed/smugmug',
  settings: getEmbedBlockSettings({
    title: 'SmugMug',
    icon: embedPhotoIcon
  }),
  patterns: [/^https?:\/\/(www\.)?smugmug\.com\/.+/i]
}, {
  // Deprecated in favour of the core-embed/speaker-deck block.
  name: 'core-embed/speaker',
  settings: getEmbedBlockSettings({
    title: 'Speaker',
    icon: embedAudioIcon,
    supports: {
      inserter: false
    }
  }),
  patterns: []
}, {
  name: 'core-embed/speaker-deck',
  settings: getEmbedBlockSettings({
    title: 'Speaker Deck',
    icon: embedContentIcon,
    transform: [{
      type: 'block',
      blocks: ['core-embed/speaker'],
      transform: function transform(content) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_15__["createBlock"])('core-embed/speaker-deck', {
          content: content
        });
      }
    }]
  }),
  patterns: [/^https?:\/\/(www\.)?speakerdeck\.com\/.+/i]
}, {
  name: 'core-embed/ted',
  settings: getEmbedBlockSettings({
    title: 'TED',
    icon: embedVideoIcon
  }),
  patterns: [/^https?:\/\/(www\.|embed\.)?ted\.com\/.+/i]
}, {
  name: 'core-embed/tumblr',
  settings: getEmbedBlockSettings({
    title: 'Tumblr',
    icon: {
      foreground: '#35465c',
      src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
        viewBox: "0 0 24 24"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
        d: "M19 3H5c-1.105 0-2 .895-2 2v14c0 1.105.895 2 2 2h14c1.105 0 2-.895 2-2V5c0-1.105-.895-2-2-2zm-5.57 14.265c-2.445.042-3.37-1.742-3.37-2.998V10.6H8.922V9.15c1.703-.615 2.113-2.15 2.21-3.026.006-.06.053-.084.08-.084h1.645V8.9h2.246v1.7H12.85v3.495c.008.476.182 1.13 1.08 1.107.3-.008.698-.094.907-.194l.54 1.6c-.205.297-1.12.642-1.946.657z"
      }))
    }
  }),
  patterns: [/^https?:\/\/(www\.)?tumblr\.com\/.+/i]
}, {
  name: 'core-embed/videopress',
  settings: getEmbedBlockSettings({
    title: 'VideoPress',
    icon: embedVideoIcon,
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('video')]
  }),
  patterns: [/^https?:\/\/videopress\.com\/.+/i]
}, {
  name: 'core-embed/wordpress-tv',
  settings: getEmbedBlockSettings({
    title: 'WordPress.tv',
    icon: embedVideoIcon
  }),
  patterns: [/^https?:\/\/wordpress\.tv\/.+/i]
}];


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/file/edit.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/file/edit.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/slicedToArray */ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/blob */ "@wordpress/blob");
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blob__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var _inspector__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./inspector */ "./node_modules/@wordpress/block-library/build-module/file/inspector.js");










/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */



var FileEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__["default"])(FileEdit, _Component);

  function FileEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__["default"])(this, FileEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__["default"])(FileEdit).apply(this, arguments));
    _this.onSelectFile = _this.onSelectFile.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.confirmCopyURL = _this.confirmCopyURL.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.resetCopyConfirmation = _this.resetCopyConfirmation.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.changeLinkDestinationOption = _this.changeLinkDestinationOption.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.changeOpenInNewWindow = _this.changeOpenInNewWindow.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.changeShowDownloadButton = _this.changeShowDownloadButton.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.state = {
      hasError: false,
      showCopyConfirmation: false
    };
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__["default"])(FileEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          noticeOperations = _this$props.noticeOperations;
      var href = attributes.href; // Upload a file drag-and-dropped into the editor

      if (this.isBlobURL(href)) {
        var file = Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_11__["getBlobByURL"])(href);
        Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_14__["mediaUpload"])({
          filesList: [file],
          onFileChange: function onFileChange(_ref) {
            var _ref2 = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref, 1),
                media = _ref2[0];

            return _this2.onSelectFile(media);
          },
          onError: function onError(message) {
            _this2.setState({
              hasError: true
            });

            noticeOperations.createErrorNotice(message);
          }
        });
        Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_11__["revokeBlobURL"])(href);
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      // Reset copy confirmation state when block is deselected
      if (prevProps.isSelected && !this.props.isSelected) {
        this.setState({
          showCopyConfirmation: false
        });
      }
    }
  }, {
    key: "onSelectFile",
    value: function onSelectFile(media) {
      if (media && media.url) {
        this.setState({
          hasError: false
        });
        this.props.setAttributes({
          href: media.url,
          fileName: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_16__["create"])({
            text: media.title
          }),
          textLinkHref: media.url,
          id: media.id
        });
      }
    }
  }, {
    key: "isBlobURL",
    value: function isBlobURL() {
      var url = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      return url.indexOf('blob:') === 0;
    }
  }, {
    key: "confirmCopyURL",
    value: function confirmCopyURL() {
      this.setState({
        showCopyConfirmation: true
      });
    }
  }, {
    key: "resetCopyConfirmation",
    value: function resetCopyConfirmation() {
      this.setState({
        showCopyConfirmation: false
      });
    }
  }, {
    key: "changeLinkDestinationOption",
    value: function changeLinkDestinationOption(newHref) {
      // Choose Media File or Attachment Page (when file is in Media Library)
      this.props.setAttributes({
        textLinkHref: newHref
      });
    }
  }, {
    key: "changeOpenInNewWindow",
    value: function changeOpenInNewWindow(newValue) {
      this.props.setAttributes({
        textLinkTarget: newValue ? '_blank' : false
      });
    }
  }, {
    key: "changeShowDownloadButton",
    value: function changeShowDownloadButton(newValue) {
      this.props.setAttributes({
        showDownloadButton: newValue
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          className = _this$props2.className,
          isSelected = _this$props2.isSelected,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes,
          noticeUI = _this$props2.noticeUI,
          noticeOperations = _this$props2.noticeOperations,
          media = _this$props2.media;
      var fileName = attributes.fileName,
          href = attributes.href,
          textLinkHref = attributes.textLinkHref,
          textLinkTarget = attributes.textLinkTarget,
          showDownloadButton = attributes.showDownloadButton,
          downloadButtonText = attributes.downloadButtonText,
          id = attributes.id;
      var _this$state = this.state,
          hasError = _this$state.hasError,
          showCopyConfirmation = _this$state.showCopyConfirmation;
      var attachmentPage = media && media.link;

      if (!href || hasError) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_14__["MediaPlaceholder"], {
          icon: "media-default",
          labels: {
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('File'),
            name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('a file')
          },
          onSelect: this.onSelectFile,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice,
          accept: "*"
        });
      }

      var classes = classnames__WEBPACK_IMPORTED_MODULE_9___default()(className, {
        'is-transient': this.isBlobURL(href)
      });
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_inspector__WEBPACK_IMPORTED_MODULE_17__["default"], Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
        hrefs: {
          href: href,
          textLinkHref: textLinkHref,
          attachmentPage: attachmentPage
        }
      }, {
        openInNewWindow: !!textLinkTarget,
        showDownloadButton: showDownloadButton,
        changeLinkDestinationOption: this.changeLinkDestinationOption,
        changeOpenInNewWindow: this.changeOpenInNewWindow,
        changeShowDownloadButton: this.changeShowDownloadButton
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_14__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["Toolbar"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_14__["MediaUpload"], {
        onSelect: this.onSelectFile,
        value: id,
        render: function render(_ref3) {
          var open = _ref3.open;
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["IconButton"], {
            className: "components-toolbar__control",
            label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Edit file'),
            onClick: open,
            icon: "edit"
          });
        }
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("div", {
        className: classes
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("div", {
        className: "".concat(className, "__content-wrapper")
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_14__["RichText"], {
        wrapperClassName: "".concat(className, "__textlink"),
        tagName: "div" // must be block-level or else cursor disappears
        ,
        value: fileName,
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Write file name…'),
        keepPlaceholderOnFocus: true,
        formattingControls: [] // disable controls
        ,
        onChange: function onChange(text) {
          return setAttributes({
            fileName: text
          });
        }
      }), showDownloadButton && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("div", {
        className: "".concat(className, "__button-richtext-wrapper")
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_14__["RichText"], {
        tagName: "div" // must be block-level or else cursor disappears
        ,
        className: "".concat(className, "__button"),
        value: downloadButtonText,
        formattingControls: [] // disable controls
        ,
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Add text…'),
        keepPlaceholderOnFocus: true,
        onChange: function onChange(text) {
          return setAttributes({
            downloadButtonText: text
          });
        }
      }))), isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["ClipboardButton"], {
        isDefault: true,
        text: href,
        className: "".concat(className, "__copy-url-button"),
        onCopy: this.confirmCopyURL,
        onFinishCopy: this.resetCopyConfirmation
      }, showCopyConfirmation ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Copied!') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Copy URL'))));
    }
  }]);

  return FileEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_15__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_13__["withSelect"])(function (select, props) {
  var _select = select('core'),
      getMedia = _select.getMedia;

  var id = props.attributes.id;
  return {
    media: id === undefined ? undefined : getMedia(id)
  };
}), _wordpress_components__WEBPACK_IMPORTED_MODULE_12__["withNotices"]])(FileEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/file/index.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/file/index.js ***!
  \**************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blob */ "@wordpress/blob");
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blob__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/file/edit.js");


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


var name = 'core/file';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('File'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add a link to a file that visitors can download.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M9 6l2 2h9v10H4V6h5m1-2H4L2 6v12l2 2h16l2-2V8l-2-2h-8l-2-2z"
  })),
  category: 'common',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('document'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('pdf')],
  attributes: {
    id: {
      type: 'number'
    },
    href: {
      type: 'string'
    },
    fileName: {
      source: 'html',
      selector: 'a:not([download])'
    },
    // Differs to the href when the block is configured to link to the attachment page
    textLinkHref: {
      type: 'string',
      source: 'attribute',
      selector: 'a:not([download])',
      attribute: 'href'
    },
    // e.g. `_blank` when the block is configured to open in a new window
    textLinkTarget: {
      type: 'string',
      source: 'attribute',
      selector: 'a:not([download])',
      attribute: 'target'
    },
    showDownloadButton: {
      type: 'boolean',
      default: true
    },
    downloadButtonText: {
      source: 'html',
      selector: 'a[download]',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Download')
    }
  },
  supports: {
    align: true
  },
  transforms: {
    from: [{
      type: 'files',
      isMatch: function isMatch(files) {
        return files.length === 1;
      },
      // We define a lower priorty (higher number) than the default of 10. This
      // ensures that the File block is only created as a fallback.
      priority: 15,
      transform: function transform(files) {
        var file = files[0];
        var blobURL = Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_3__["createBlobURL"])(file); // File will be uploaded in componentDidMount()

        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/file', {
          href: blobURL,
          fileName: file.name,
          textLinkHref: blobURL
        });
      }
    }, {
      type: 'block',
      blocks: ['core/audio'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/file', {
          href: attributes.src,
          fileName: attributes.caption,
          textLinkHref: attributes.src,
          id: attributes.id
        });
      }
    }, {
      type: 'block',
      blocks: ['core/video'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/file', {
          href: attributes.src,
          fileName: attributes.caption,
          textLinkHref: attributes.src,
          id: attributes.id
        });
      }
    }, {
      type: 'block',
      blocks: ['core/image'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/file', {
          href: attributes.url,
          fileName: attributes.caption,
          textLinkHref: attributes.url,
          id: attributes.id
        });
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/audio'],
      isMatch: function isMatch(_ref) {
        var id = _ref.id;

        if (!id) {
          return false;
        }

        var _select = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["select"])('core'),
            getMedia = _select.getMedia;

        var media = getMedia(id);
        return !!media && Object(lodash__WEBPACK_IMPORTED_MODULE_1__["includes"])(media.mime_type, 'audio');
      },
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/audio', {
          src: attributes.href,
          caption: attributes.fileName,
          id: attributes.id
        });
      }
    }, {
      type: 'block',
      blocks: ['core/video'],
      isMatch: function isMatch(_ref2) {
        var id = _ref2.id;

        if (!id) {
          return false;
        }

        var _select2 = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["select"])('core'),
            getMedia = _select2.getMedia;

        var media = getMedia(id);
        return !!media && Object(lodash__WEBPACK_IMPORTED_MODULE_1__["includes"])(media.mime_type, 'video');
      },
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/video', {
          src: attributes.href,
          caption: attributes.fileName,
          id: attributes.id
        });
      }
    }, {
      type: 'block',
      blocks: ['core/image'],
      isMatch: function isMatch(_ref3) {
        var id = _ref3.id;

        if (!id) {
          return false;
        }

        var _select3 = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["select"])('core'),
            getMedia = _select3.getMedia;

        var media = getMedia(id);
        return !!media && Object(lodash__WEBPACK_IMPORTED_MODULE_1__["includes"])(media.mime_type, 'image');
      },
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/image', {
          url: attributes.href,
          caption: attributes.fileName,
          id: attributes.id
        });
      }
    }]
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_8__["default"],
  save: function save(_ref4) {
    var attributes = _ref4.attributes;
    var href = attributes.href,
        fileName = attributes.fileName,
        textLinkHref = attributes.textLinkHref,
        textLinkTarget = attributes.textLinkTarget,
        showDownloadButton = attributes.showDownloadButton,
        downloadButtonText = attributes.downloadButtonText;
    return href && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", null, !_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].isEmpty(fileName) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("a", {
      href: textLinkHref,
      target: textLinkTarget,
      rel: textLinkTarget ? 'noreferrer noopener' : false
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
      value: fileName
    })), showDownloadButton && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("a", {
      href: href,
      className: "wp-block-file__button" // ensure download attribute is still set when fileName
      // is undefined. Using '' here as `true` still leaves
      // the attribute unset.
      ,
      download: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["getTextContent"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["create"])({
        html: fileName
      }))
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
      value: downloadButtonText
    })));
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/file/inspector.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/file/inspector.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return FileBlockInspector; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);


/**
 * WordPress dependencies
 */





function getDownloadButtonHelp(checked) {
  return checked ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('The download button is visible.') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('The download button is hidden.');
}

function FileBlockInspector(_ref) {
  var hrefs = _ref.hrefs,
      openInNewWindow = _ref.openInNewWindow,
      showDownloadButton = _ref.showDownloadButton,
      changeLinkDestinationOption = _ref.changeLinkDestinationOption,
      changeOpenInNewWindow = _ref.changeOpenInNewWindow,
      changeShowDownloadButton = _ref.changeShowDownloadButton;
  var href = hrefs.href,
      textLinkHref = hrefs.textLinkHref,
      attachmentPage = hrefs.attachmentPage;
  var linkDestinationOptions = [{
    value: href,
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('URL')
  }];

  if (attachmentPage) {
    linkDestinationOptions = [{
      value: href,
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Media File')
    }, {
      value: attachmentPage,
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Attachment Page')
    }];
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Text Link Settings')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SelectControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Link To'),
    value: textLinkHref,
    options: linkDestinationOptions,
    onChange: changeLinkDestinationOption
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Open in New Window'),
    checked: openInNewWindow,
    onChange: changeOpenInNewWindow
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Download Button Settings')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Show Download Button'),
    help: getDownloadButtonHelp,
    checked: showDownloadButton,
    onChange: changeShowDownloadButton
  }))));
}


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/gallery/edit.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/gallery/edit.js ***!
  \****************************************************************************/
/*! exports provided: defaultColumnsNumber, pickRelevantMediaFiles, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "defaultColumnsNumber", function() { return defaultColumnsNumber; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "pickRelevantMediaFiles", function() { return pickRelevantMediaFiles; });
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @babel/runtime/helpers/esm/typeof */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var _gallery_image__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./gallery-image */ "./node_modules/@wordpress/block-library/build-module/gallery/gallery-image.js");











/**
 * External Dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


var MAX_COLUMNS = 8;
var linkOptions = [{
  value: 'attachment',
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Attachment Page')
}, {
  value: 'media',
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Media File')
}, {
  value: 'none',
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('None')
}];
var ALLOWED_MEDIA_TYPES = ['image'];
function defaultColumnsNumber(attributes) {
  return Math.min(3, attributes.images.length);
}
var pickRelevantMediaFiles = function pickRelevantMediaFiles(image) {
  var caption = image.caption;

  if (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_8__["default"])(caption) !== 'object') {
    caption = Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_14__["create"])({
      html: caption
    });
  }

  return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, Object(lodash__WEBPACK_IMPORTED_MODULE_10__["pick"])(image, ['alt', 'id', 'link', 'url']), {
    caption: caption
  });
};

var GalleryEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__["default"])(GalleryEdit, _Component);

  function GalleryEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__["default"])(this, GalleryEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__["default"])(GalleryEdit).apply(this, arguments));
    _this.onSelectImage = _this.onSelectImage.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onSelectImages = _this.onSelectImages.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.setLinkTo = _this.setLinkTo.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.setColumnsNumber = _this.setColumnsNumber.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.toggleImageCrop = _this.toggleImageCrop.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onRemoveImage = _this.onRemoveImage.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.setImageAttributes = _this.setImageAttributes.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.addFiles = _this.addFiles.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.uploadFromFiles = _this.uploadFromFiles.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.state = {
      selectedImage: null
    };
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__["default"])(GalleryEdit, [{
    key: "onSelectImage",
    value: function onSelectImage(index) {
      var _this2 = this;

      return function () {
        if (_this2.state.selectedImage !== index) {
          _this2.setState({
            selectedImage: index
          });
        }
      };
    }
  }, {
    key: "onRemoveImage",
    value: function onRemoveImage(index) {
      var _this3 = this;

      return function () {
        var images = Object(lodash__WEBPACK_IMPORTED_MODULE_10__["filter"])(_this3.props.attributes.images, function (img, i) {
          return index !== i;
        });
        var columns = _this3.props.attributes.columns;

        _this3.setState({
          selectedImage: null
        });

        _this3.props.setAttributes({
          images: images,
          columns: columns ? Math.min(images.length, columns) : columns
        });
      };
    }
  }, {
    key: "onSelectImages",
    value: function onSelectImages(images) {
      this.props.setAttributes({
        images: images.map(function (image) {
          return pickRelevantMediaFiles(image);
        })
      });
    }
  }, {
    key: "setLinkTo",
    value: function setLinkTo(value) {
      this.props.setAttributes({
        linkTo: value
      });
    }
  }, {
    key: "setColumnsNumber",
    value: function setColumnsNumber(value) {
      this.props.setAttributes({
        columns: value
      });
    }
  }, {
    key: "toggleImageCrop",
    value: function toggleImageCrop() {
      this.props.setAttributes({
        imageCrop: !this.props.attributes.imageCrop
      });
    }
  }, {
    key: "getImageCropHelp",
    value: function getImageCropHelp(checked) {
      return checked ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Thumbnails are cropped to align.') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Thumbnails are not cropped.');
    }
  }, {
    key: "setImageAttributes",
    value: function setImageAttributes(index, attributes) {
      var _this$props = this.props,
          images = _this$props.attributes.images,
          setAttributes = _this$props.setAttributes;

      if (!images[index]) {
        return;
      }

      setAttributes({
        images: Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(images.slice(0, index)).concat([Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, images[index], attributes)], Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(images.slice(index + 1)))
      });
    }
  }, {
    key: "uploadFromFiles",
    value: function uploadFromFiles(event) {
      this.addFiles(event.target.files);
    }
  }, {
    key: "addFiles",
    value: function addFiles(files) {
      var currentImages = this.props.attributes.images || [];
      var _this$props2 = this.props,
          noticeOperations = _this$props2.noticeOperations,
          setAttributes = _this$props2.setAttributes;
      Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["mediaUpload"])({
        allowedTypes: ALLOWED_MEDIA_TYPES,
        filesList: files,
        onFileChange: function onFileChange(images) {
          var imagesNormalized = images.map(function (image) {
            return pickRelevantMediaFiles(image);
          });
          setAttributes({
            images: currentImages.concat(imagesNormalized)
          });
        },
        onError: noticeOperations.createErrorNotice
      });
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      // Deselect images when deselecting the block
      if (!this.props.isSelected && prevProps.isSelected) {
        this.setState({
          selectedImage: null,
          captionSelected: false
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this4 = this;

      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          isSelected = _this$props3.isSelected,
          className = _this$props3.className,
          noticeOperations = _this$props3.noticeOperations,
          noticeUI = _this$props3.noticeUI;
      var images = attributes.images,
          _attributes$columns = attributes.columns,
          columns = _attributes$columns === void 0 ? defaultColumnsNumber(attributes) : _attributes$columns,
          align = attributes.align,
          imageCrop = attributes.imageCrop,
          linkTo = attributes.linkTo;
      var dropZone = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["DropZone"], {
        onFilesDrop: this.addFiles
      });
      var controls = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["BlockControls"], null, !!images.length && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["Toolbar"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["MediaUpload"], {
        onSelect: this.onSelectImages,
        allowedTypes: ALLOWED_MEDIA_TYPES,
        multiple: true,
        gallery: true,
        value: images.map(function (img) {
          return img.id;
        }),
        render: function render(_ref) {
          var open = _ref.open;
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["IconButton"], {
            className: "components-toolbar__control",
            label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Edit Gallery'),
            icon: "edit",
            onClick: open
          });
        }
      })));

      if (images.length === 0) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Fragment"], null, controls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["MediaPlaceholder"], {
          icon: "format-gallery",
          className: className,
          labels: {
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Gallery'),
            name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('images')
          },
          onSelect: this.onSelectImages,
          accept: "image/*",
          allowedTypes: ALLOWED_MEDIA_TYPES,
          multiple: true,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice
        }));
      }

      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Fragment"], null, controls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Gallery Settings')
      }, images.length > 1 && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["RangeControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Columns'),
        value: columns,
        onChange: this.setColumnsNumber,
        min: 1,
        max: Math.min(MAX_COLUMNS, images.length)
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Crop Images'),
        checked: !!imageCrop,
        onChange: this.toggleImageCrop,
        help: this.getImageCropHelp
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["SelectControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Link To'),
        value: linkTo,
        onChange: this.setLinkTo,
        options: linkOptions
      }))), noticeUI, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("ul", {
        className: "".concat(className, " align").concat(align, " columns-").concat(columns, " ").concat(imageCrop ? 'is-cropped' : '')
      }, dropZone, images.map(function (img, index) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("li", {
          className: "blocks-gallery-item",
          key: img.id || img.url
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_gallery_image__WEBPACK_IMPORTED_MODULE_15__["default"], {
          url: img.url,
          alt: img.alt,
          id: img.id,
          isSelected: isSelected && _this4.state.selectedImage === index,
          onRemove: _this4.onRemoveImage(index),
          onSelect: _this4.onSelectImage(index),
          setAttributes: function setAttributes(attrs) {
            return _this4.setImageAttributes(index, attrs);
          },
          caption: img.caption
        }));
      }), isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("li", {
        className: "blocks-gallery-item has-add-item-button"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["FormFileUpload"], {
        multiple: true,
        isLarge: true,
        className: "block-library-gallery-add-item-button",
        onChange: this.uploadFromFiles,
        accept: "image/*",
        icon: "insert"
      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Upload an image')))));
    }
  }]);

  return GalleryEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["withNotices"])(GalleryEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/gallery/gallery-image.js":
/*!*************************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/gallery/gallery-image.js ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__);








/**
 * External Dependencies
 */

/**
 * WordPress Dependencies
 */








var GalleryImage =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(GalleryImage, _Component);

  function GalleryImage() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, GalleryImage);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(GalleryImage).apply(this, arguments));
    _this.onImageClick = _this.onImageClick.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onSelectCaption = _this.onSelectCaption.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onKeyDown = _this.onKeyDown.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.bindContainer = _this.bindContainer.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.state = {
      captionSelected: false
    };
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(GalleryImage, [{
    key: "bindContainer",
    value: function bindContainer(ref) {
      this.container = ref;
    }
  }, {
    key: "onSelectCaption",
    value: function onSelectCaption() {
      if (!this.state.captionSelected) {
        this.setState({
          captionSelected: true
        });
      }

      if (!this.props.isSelected) {
        this.props.onSelect();
      }
    }
  }, {
    key: "onImageClick",
    value: function onImageClick() {
      if (!this.props.isSelected) {
        this.props.onSelect();
      }

      if (this.state.captionSelected) {
        this.setState({
          captionSelected: false
        });
      }
    }
  }, {
    key: "onKeyDown",
    value: function onKeyDown(event) {
      if (this.container === document.activeElement && this.props.isSelected && [_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__["BACKSPACE"], _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__["DELETE"]].indexOf(event.keyCode) !== -1) {
        event.stopPropagation();
        event.preventDefault();
        this.props.onRemove();
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this$props = this.props,
          isSelected = _this$props.isSelected,
          image = _this$props.image,
          url = _this$props.url;

      if (image && !url) {
        this.props.setAttributes({
          url: image.source_url,
          alt: image.alt_text
        });
      } // unselect the caption so when the user selects other image and comeback
      // the caption is not immediately selected


      if (this.state.captionSelected && !isSelected && prevProps.isSelected) {
        this.setState({
          captionSelected: false
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          url = _this$props2.url,
          alt = _this$props2.alt,
          id = _this$props2.id,
          linkTo = _this$props2.linkTo,
          link = _this$props2.link,
          isSelected = _this$props2.isSelected,
          caption = _this$props2.caption,
          onRemove = _this$props2.onRemove,
          setAttributes = _this$props2.setAttributes;
      var href;

      switch (linkTo) {
        case 'media':
          href = url;
          break;

        case 'attachment':
          href = link;
          break;
      } // Disable reason: Image itself is not meant to be
      // interactive, but should direct image selection and unfocus caption fields
      // eslint-disable-next-line jsx-a11y/no-noninteractive-element-interactions, jsx-a11y/click-events-have-key-events


      var img = url ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("img", {
        src: url,
        alt: alt,
        "data-id": id,
        onClick: this.onImageClick
      }) : Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Spinner"], null);
      var className = classnames__WEBPACK_IMPORTED_MODULE_7___default()({
        'is-selected': isSelected,
        'is-transient': url && 0 === url.indexOf('blob:')
      }); // Disable reason: Each block can be selected by clicking on it and we should keep the same saved markup

      /* eslint-disable jsx-a11y/no-noninteractive-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */

      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("figure", {
        className: className,
        tabIndex: "-1",
        onKeyDown: this.onKeyDown,
        ref: this.bindContainer
      }, isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("div", {
        className: "block-library-gallery-item__inline-menu"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["IconButton"], {
        icon: "no-alt",
        onClick: onRemove,
        className: "blocks-gallery-item__remove",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Remove Image')
      })), href ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("a", {
        href: href
      }, img) : img, !_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["RichText"].isEmpty(caption) || isSelected ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["RichText"], {
        tagName: "figcaption",
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Write caption…'),
        value: caption,
        isSelected: this.state.captionSelected,
        onChange: function onChange(newCaption) {
          return setAttributes({
            caption: newCaption
          });
        },
        unstableOnFocus: this.onSelectCaption,
        inlineToolbar: true
      }) : null);
      /* eslint-enable jsx-a11y/no-noninteractive-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */
    }
  }]);

  return GalleryImage;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_11__["withSelect"])(function (select, ownProps) {
  var _select = select('core'),
      getMedia = _select.getMedia;

  var id = ownProps.id;
  return {
    image: id ? getMedia(id) : null
  };
})(GalleryImage));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/gallery/index.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/gallery/index.js ***!
  \*****************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/blob */ "@wordpress/blob");
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blob__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/gallery/edit.js");



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


var blockAttributes = {
  images: {
    type: 'array',
    default: [],
    source: 'query',
    selector: 'ul.wp-block-gallery .blocks-gallery-item',
    query: {
      url: {
        source: 'attribute',
        selector: 'img',
        attribute: 'src'
      },
      link: {
        source: 'attribute',
        selector: 'img',
        attribute: 'data-link'
      },
      alt: {
        source: 'attribute',
        selector: 'img',
        attribute: 'alt',
        default: ''
      },
      id: {
        source: 'attribute',
        selector: 'img',
        attribute: 'data-id'
      },
      caption: {
        source: 'html',
        selector: 'figcaption'
      }
    }
  },
  columns: {
    type: 'number'
  },
  imageCrop: {
    type: 'boolean',
    default: true
  },
  linkTo: {
    type: 'string',
    default: 'none'
  }
};
var name = 'core/gallery';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Gallery'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Display multiple images in an elegantly organized tiled layout.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
    d: "M20 4v12H8V4h12m0-2H8L6 4v12l2 2h12l2-2V4l-2-2z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
    d: "M12 12l1 2 3-3 3 4H9z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
    d: "M2 6v14l2 2h14v-2H4V6H2z"
  }))),
  category: 'common',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('images'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('photos')],
  attributes: blockAttributes,
  supports: {
    align: true
  },
  transforms: {
    from: [{
      type: 'block',
      isMultiBlock: true,
      blocks: ['core/image'],
      transform: function transform(attributes) {
        var validImages = Object(lodash__WEBPACK_IMPORTED_MODULE_2__["filter"])(attributes, function (_ref) {
          var id = _ref.id,
              url = _ref.url;
          return id && url;
        });

        if (validImages.length > 0) {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/gallery', {
            images: validImages.map(function (_ref2) {
              var id = _ref2.id,
                  url = _ref2.url,
                  alt = _ref2.alt,
                  caption = _ref2.caption;
              return {
                id: id,
                url: url,
                alt: alt,
                caption: caption
              };
            })
          });
        }

        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/gallery');
      }
    }, {
      type: 'shortcode',
      tag: 'gallery',
      attributes: {
        images: {
          type: 'array',
          shortcode: function shortcode(_ref3) {
            var ids = _ref3.named.ids;

            if (!ids) {
              return [];
            }

            return ids.split(',').map(function (id) {
              return {
                id: parseInt(id, 10)
              };
            });
          }
        },
        columns: {
          type: 'number',
          shortcode: function shortcode(_ref4) {
            var _ref4$named$columns = _ref4.named.columns,
                columns = _ref4$named$columns === void 0 ? '3' : _ref4$named$columns;
            return parseInt(columns, 10);
          }
        },
        linkTo: {
          type: 'string',
          shortcode: function shortcode(_ref5) {
            var _ref5$named$link = _ref5.named.link,
                link = _ref5$named$link === void 0 ? 'attachment' : _ref5$named$link;
            return link === 'file' ? 'media' : link;
          }
        }
      }
    }, {
      // When created by drag and dropping multiple files on an insertion point
      type: 'files',
      isMatch: function isMatch(files) {
        return files.length !== 1 && Object(lodash__WEBPACK_IMPORTED_MODULE_2__["every"])(files, function (file) {
          return file.type.indexOf('image/') === 0;
        });
      },
      transform: function transform(files, onChange) {
        var block = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/gallery', {
          images: files.map(function (file) {
            return Object(_edit__WEBPACK_IMPORTED_MODULE_7__["pickRelevantMediaFiles"])({
              url: Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_6__["createBlobURL"])(file)
            });
          })
        });
        Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["mediaUpload"])({
          filesList: files,
          onFileChange: function onFileChange(images) {
            onChange(block.clientId, {
              images: images.map(function (image) {
                return Object(_edit__WEBPACK_IMPORTED_MODULE_7__["pickRelevantMediaFiles"])(image);
              })
            });
          },
          allowedTypes: ['image']
        });
        return block;
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/image'],
      transform: function transform(_ref6) {
        var images = _ref6.images;

        if (images.length > 0) {
          return images.map(function (_ref7) {
            var id = _ref7.id,
                url = _ref7.url,
                alt = _ref7.alt,
                caption = _ref7.caption;
            return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/image', {
              id: id,
              url: url,
              alt: alt,
              caption: caption
            });
          });
        }

        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/image');
      }
    }]
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_7__["default"],
  save: function save(_ref8) {
    var attributes = _ref8.attributes;
    var images = attributes.images,
        _attributes$columns = attributes.columns,
        columns = _attributes$columns === void 0 ? Object(_edit__WEBPACK_IMPORTED_MODULE_7__["defaultColumnsNumber"])(attributes) : _attributes$columns,
        imageCrop = attributes.imageCrop,
        linkTo = attributes.linkTo;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("ul", {
      className: "columns-".concat(columns, " ").concat(imageCrop ? 'is-cropped' : '')
    }, images.map(function (image) {
      var href;

      switch (linkTo) {
        case 'media':
          href = image.url;
          break;

        case 'attachment':
          href = image.link;
          break;
      }

      var img = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("img", {
        src: image.url,
        alt: image.alt,
        "data-id": image.id,
        "data-link": image.link,
        className: image.id ? "wp-image-".concat(image.id) : null
      });
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("li", {
        key: image.id || image.url,
        className: "blocks-gallery-item"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("figure", null, href ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("a", {
        href: href
      }, img) : img, image.caption && image.caption.length > 0 && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"].Content, {
        tagName: "figcaption",
        value: image.caption
      })));
    }));
  },
  deprecated: [{
    attributes: blockAttributes,
    save: function save(_ref9) {
      var attributes = _ref9.attributes;
      var images = attributes.images,
          _attributes$columns2 = attributes.columns,
          columns = _attributes$columns2 === void 0 ? Object(_edit__WEBPACK_IMPORTED_MODULE_7__["defaultColumnsNumber"])(attributes) : _attributes$columns2,
          imageCrop = attributes.imageCrop,
          linkTo = attributes.linkTo;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("ul", {
        className: "columns-".concat(columns, " ").concat(imageCrop ? 'is-cropped' : '')
      }, images.map(function (image) {
        var href;

        switch (linkTo) {
          case 'media':
            href = image.url;
            break;

          case 'attachment':
            href = image.link;
            break;
        }

        var img = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("img", {
          src: image.url,
          alt: image.alt,
          "data-id": image.id,
          "data-link": image.link
        });
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("li", {
          key: image.id || image.url,
          className: "blocks-gallery-item"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("figure", null, href ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("a", {
          href: href
        }, img) : img, image.caption && image.caption.length > 0 && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"].Content, {
          tagName: "figcaption",
          value: image.caption
        })));
      }));
    }
  }, {
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, blockAttributes, {
      images: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, blockAttributes.images, {
        selector: 'div.wp-block-gallery figure.blocks-gallery-image img'
      }),
      align: {
        type: 'string',
        default: 'none'
      }
    }),
    save: function save(_ref10) {
      var attributes = _ref10.attributes;
      var images = attributes.images,
          _attributes$columns3 = attributes.columns,
          columns = _attributes$columns3 === void 0 ? Object(_edit__WEBPACK_IMPORTED_MODULE_7__["defaultColumnsNumber"])(attributes) : _attributes$columns3,
          align = attributes.align,
          imageCrop = attributes.imageCrop,
          linkTo = attributes.linkTo;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
        className: "align".concat(align, " columns-").concat(columns, " ").concat(imageCrop ? 'is-cropped' : '')
      }, images.map(function (image) {
        var href;

        switch (linkTo) {
          case 'media':
            href = image.url;
            break;

          case 'attachment':
            href = image.link;
            break;
        }

        var img = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("img", {
          src: image.url,
          alt: image.alt,
          "data-id": image.id
        });
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("figure", {
          key: image.id || image.url,
          className: "blocks-gallery-image"
        }, href ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("a", {
          href: href
        }, img) : img);
      }));
    }
  }]
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/heading/edit.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/heading/edit.js ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return HeadingEdit; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _heading_toolbar__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./heading-toolbar */ "./node_modules/@wordpress/block-library/build-module/heading/heading-toolbar.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__);


/**
 * Internal dependencies
 */

/**
 * WordPress dependencies
 */






function HeadingEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      mergeBlocks = _ref.mergeBlocks,
      insertBlocksAfter = _ref.insertBlocksAfter,
      onReplace = _ref.onReplace,
      className = _ref.className;
  var align = attributes.align,
      content = attributes.content,
      level = attributes.level,
      placeholder = attributes.placeholder;
  var tagName = 'h' + level;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_heading_toolbar__WEBPACK_IMPORTED_MODULE_1__["default"], {
    minLevel: 2,
    maxLevel: 5,
    selectedLevel: level,
    onChange: function onChange(newLevel) {
      return setAttributes({
        level: newLevel
      });
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Heading Settings')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Level')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_heading_toolbar__WEBPACK_IMPORTED_MODULE_1__["default"], {
    minLevel: 1,
    maxLevel: 7,
    selectedLevel: level,
    onChange: function onChange(newLevel) {
      return setAttributes({
        level: newLevel
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Text Alignment')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["AlignmentToolbar"], {
    value: align,
    onChange: function onChange(nextAlign) {
      setAttributes({
        align: nextAlign
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"], {
    wrapperClassName: "wp-block-heading",
    tagName: tagName,
    value: content,
    onChange: function onChange(value) {
      return setAttributes({
        content: value
      });
    },
    onMerge: mergeBlocks,
    onSplit: insertBlocksAfter ? function (before, after) {
      setAttributes({
        content: before
      });

      for (var _len = arguments.length, blocks = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
        blocks[_key - 2] = arguments[_key];
      }

      insertBlocksAfter(blocks.concat([Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])('core/paragraph', {
        content: after
      })]));
    } : undefined,
    onRemove: function onRemove() {
      return onReplace([]);
    },
    style: {
      textAlign: align
    },
    className: className,
    placeholder: placeholder || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Write heading…')
  }));
}


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/heading/heading-toolbar.js":
/*!***************************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/heading/heading-toolbar.js ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);







/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





var HeadingToolbar =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(HeadingToolbar, _Component);

  function HeadingToolbar() {
    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, HeadingToolbar);

    return Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(HeadingToolbar).apply(this, arguments));
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(HeadingToolbar, [{
    key: "createLevelControl",
    value: function createLevelControl(targetLevel, selectedLevel, onChange) {
      return {
        icon: 'heading',
        // translators: %s: heading level e.g: "1", "2", "3"
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["sprintf"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('Heading %d'), targetLevel),
        isActive: targetLevel === selectedLevel,
        onClick: function onClick() {
          return onChange(targetLevel);
        },
        subscript: String(targetLevel)
      };
    }
  }, {
    key: "render",
    value: function render() {
      var _this = this;

      var _this$props = this.props,
          minLevel = _this$props.minLevel,
          maxLevel = _this$props.maxLevel,
          selectedLevel = _this$props.selectedLevel,
          onChange = _this$props.onChange;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Toolbar"], {
        controls: Object(lodash__WEBPACK_IMPORTED_MODULE_6__["range"])(minLevel, maxLevel).map(function (index) {
          return _this.createLevelControl(index, selectedLevel, onChange);
        })
      });
    }
  }]);

  return HeadingToolbar;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (HeadingToolbar);


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/heading/index.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/heading/index.js ***!
  \*****************************************************************************/
/*! exports provided: getLevelFromHeadingNodeName, name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getLevelFromHeadingNodeName", function() { return getLevelFromHeadingNodeName; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/heading/edit.js");




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
 * Given a node name string for a heading node, returns its numeric level.
 *
 * @param {string} nodeName Heading node name.
 *
 * @return {number} Heading level.
 */

function getLevelFromHeadingNodeName(nodeName) {
  return Number(nodeName.substr(1));
}
var supports = {
  className: false,
  anchor: true
};
var schema = {
  content: {
    source: 'html',
    selector: 'h1,h2,h3,h4,h5,h6'
  },
  level: {
    type: 'number',
    default: 2
  },
  align: {
    type: 'string'
  },
  placeholder: {
    type: 'string'
  }
};
var name = 'core/heading';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Heading'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Introduce topics and help visitors (and search engines!) understand how your content is organized.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    d: "M5 4v3h5.5v12h3V7H19V4z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  })),
  category: 'common',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('title'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('subtitle')],
  supports: supports,
  attributes: schema,
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(_ref) {
        var content = _ref.content;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/heading', {
          content: content
        });
      }
    }, {
      type: 'raw',
      selector: 'h1,h2,h3,h4,h5,h6',
      schema: {
        h1: {
          children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["getPhrasingContentSchema"])()
        },
        h2: {
          children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["getPhrasingContentSchema"])()
        },
        h3: {
          children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["getPhrasingContentSchema"])()
        },
        h4: {
          children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["getPhrasingContentSchema"])()
        },
        h5: {
          children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["getPhrasingContentSchema"])()
        },
        h6: {
          children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["getPhrasingContentSchema"])()
        }
      },
      transform: function transform(node) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/heading', Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["getBlockAttributes"])(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["getBlockType"])('core/heading'), node.outerHTML), {
          level: getLevelFromHeadingNodeName(node.nodeName)
        }));
      }
    }, {
      type: 'pattern',
      regExp: /^(#{2,6})\s/,
      transform: function transform(_ref2) {
        var content = _ref2.content,
            match = _ref2.match;
        var level = match[1].length;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/heading', {
          level: level,
          content: content
        });
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(_ref3) {
        var content = _ref3.content;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/paragraph', {
          content: content
        });
      }
    }]
  },
  deprecated: [{
    supports: supports,
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, Object(lodash__WEBPACK_IMPORTED_MODULE_3__["omit"])(schema, ['level']), {
      nodeName: {
        type: 'string',
        source: 'property',
        selector: 'h1,h2,h3,h4,h5,h6',
        property: 'nodeName',
        default: 'H2'
      }
    }),
    migrate: function migrate(attributes) {
      var nodeName = attributes.nodeName,
          migratedAttributes = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_0__["default"])(attributes, ["nodeName"]);

      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, migratedAttributes, {
        level: getLevelFromHeadingNodeName(nodeName)
      });
    },
    save: function save(_ref4) {
      var attributes = _ref4.attributes;
      var align = attributes.align,
          nodeName = attributes.nodeName,
          content = attributes.content;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: nodeName.toLowerCase(),
        style: {
          textAlign: align
        },
        value: content
      });
    }
  }],
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: attributes.content + attributesToMerge.content
    };
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_7__["default"],
  save: function save(_ref5) {
    var attributes = _ref5.attributes;
    var align = attributes.align,
        level = attributes.level,
        content = attributes.content;
    var tagName = 'h' + level;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
      tagName: tagName,
      style: {
        textAlign: align
      },
      value: content
    });
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/html/index.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/html/index.js ***!
  \**************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__);


/**
 * WordPress dependencies
 */






var name = 'core/html';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Custom HTML'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Add your own HTML (and view it right here as you edit!).'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M4.5,11h-2V9H1v6h1.5v-2.5h2V15H6V9H4.5V11z M7,10.5h1.5V15H10v-4.5h1.5V9H7V10.5z M14.5,10l-1-1H12v6h1.5v-3.9  l1,1l1-1V15H17V9h-1.5L14.5,10z M19.5,13.5V9H18v6h5v-1.5H19.5z"
  })),
  category: 'formatting',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('embed')],
  supports: {
    customClassName: false,
    className: false,
    html: false
  },
  attributes: {
    content: {
      type: 'string',
      source: 'html'
    }
  },
  transforms: {
    from: [{
      type: 'raw',
      isMatch: function isMatch(node) {
        return node.nodeName === 'FIGURE' && !!node.querySelector('iframe');
      },
      schema: {
        figure: {
          require: ['iframe'],
          children: {
            iframe: {
              attributes: ['src', 'allowfullscreen', 'height', 'width']
            },
            figcaption: {
              children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["getPhrasingContentSchema"])()
            }
          }
        }
      }
    }]
  },
  edit: Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__["withState"])({
    isPreview: false
  })(function (_ref) {
    var attributes = _ref.attributes,
        setAttributes = _ref.setAttributes,
        setState = _ref.setState,
        isPreview = _ref.isPreview;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "wp-block-html"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "components-toolbar"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("button", {
      className: "components-tab-button ".concat(!isPreview ? 'is-active' : ''),
      onClick: function onClick() {
        return setState({
          isPreview: false
        });
      }
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", null, "HTML")), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("button", {
      className: "components-tab-button ".concat(isPreview ? 'is-active' : ''),
      onClick: function onClick() {
        return setState({
          isPreview: true
        });
      }
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Preview'))))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Disabled"].Consumer, null, function (isDisabled) {
      return isPreview || isDisabled ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SandBox"], {
        html: attributes.content
      }) : Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["PlainText"], {
        value: attributes.content,
        onChange: function onChange(content) {
          return setAttributes({
            content: content
          });
        },
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Write HTML…'),
        "aria-label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('HTML')
      });
    }));
  }),
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["RawHTML"], null, attributes.content);
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/image/edit.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/image/edit.js ***!
  \**************************************************************************/
/*! exports provided: pickRelevantMediaFiles, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "pickRelevantMediaFiles", function() { return pickRelevantMediaFiles; });
/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/slicedToArray */ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @babel/runtime/helpers/esm/typeof */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/blob */ "@wordpress/blob");
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blob__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! @wordpress/viewport */ "@wordpress/viewport");
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_17___default = /*#__PURE__*/__webpack_require__.n(_wordpress_viewport__WEBPACK_IMPORTED_MODULE_17__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_18___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_18__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_19___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_19__);
/* harmony import */ var _image_size__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./image-size */ "./node_modules/@wordpress/block-library/build-module/image/image-size.js");











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
 * Module constants
 */

var MIN_SIZE = 20;
var LINK_DESTINATION_NONE = 'none';
var LINK_DESTINATION_MEDIA = 'media';
var LINK_DESTINATION_ATTACHMENT = 'attachment';
var LINK_DESTINATION_CUSTOM = 'custom';
var ALLOWED_MEDIA_TYPES = ['image'];
var pickRelevantMediaFiles = function pickRelevantMediaFiles(image) {
  var caption = image.caption;

  if (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_8__["default"])(caption) !== 'object') {
    caption = Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_19__["create"])({
      html: caption
    });
  }

  return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, Object(lodash__WEBPACK_IMPORTED_MODULE_11__["pick"])(image, ['alt', 'id', 'link', 'url']), {
    caption: caption
  });
};

var ImageEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__["default"])(ImageEdit, _Component);

  function ImageEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__["default"])(this, ImageEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__["default"])(ImageEdit).apply(this, arguments));
    _this.updateAlt = _this.updateAlt.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.updateAlignment = _this.updateAlignment.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onFocusCaption = _this.onFocusCaption.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onImageClick = _this.onImageClick.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onSelectImage = _this.onSelectImage.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.updateImageURL = _this.updateImageURL.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.updateWidth = _this.updateWidth.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.updateHeight = _this.updateHeight.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.updateDimensions = _this.updateDimensions.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onSetCustomHref = _this.onSetCustomHref.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onSetLinkDestination = _this.onSetLinkDestination.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.state = {
      captionFocused: false
    };
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__["default"])(ImageEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      var id = attributes.id,
          _attributes$url = attributes.url,
          url = _attributes$url === void 0 ? '' : _attributes$url;

      if (!id && url.indexOf('blob:') === 0) {
        var file = Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_13__["getBlobByURL"])(url);

        if (file) {
          Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["mediaUpload"])({
            filesList: [file],
            onFileChange: function onFileChange(_ref) {
              var _ref2 = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref, 1),
                  image = _ref2[0];

              setAttributes(pickRelevantMediaFiles(image));
            },
            allowedTypes: ALLOWED_MEDIA_TYPES
          });
        }
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _prevProps$attributes = prevProps.attributes,
          prevID = _prevProps$attributes.id,
          _prevProps$attributes2 = _prevProps$attributes.url,
          prevUrl = _prevProps$attributes2 === void 0 ? '' : _prevProps$attributes2;
      var _this$props$attribute = this.props.attributes,
          id = _this$props$attribute.id,
          _this$props$attribute2 = _this$props$attribute.url,
          url = _this$props$attribute2 === void 0 ? '' : _this$props$attribute2;

      if (!prevID && prevUrl.indexOf('blob:') === 0 && id && url.indexOf('blob:') === -1) {
        Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_13__["revokeBlobURL"])(url);
      }

      if (!this.props.isSelected && prevProps.isSelected && this.state.captionFocused) {
        this.setState({
          captionFocused: false
        });
      }
    }
  }, {
    key: "onSelectImage",
    value: function onSelectImage(media) {
      if (!media || !media.url) {
        this.props.setAttributes({
          url: undefined,
          alt: undefined,
          id: undefined,
          caption: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_19__["create"])()
        });
        return;
      }

      this.props.setAttributes(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, pickRelevantMediaFiles(media), {
        width: undefined,
        height: undefined
      }));
    }
  }, {
    key: "onSetLinkDestination",
    value: function onSetLinkDestination(value) {
      var href;

      if (value === LINK_DESTINATION_NONE) {
        href = undefined;
      } else if (value === LINK_DESTINATION_MEDIA) {
        href = this.props.attributes.url;
      } else if (value === LINK_DESTINATION_ATTACHMENT) {
        href = this.props.image && this.props.image.link;
      } else {
        href = this.props.attributes.href;
      }

      this.props.setAttributes({
        linkDestination: value,
        href: href
      });
    }
  }, {
    key: "onSetCustomHref",
    value: function onSetCustomHref(value) {
      this.props.setAttributes({
        href: value
      });
    }
  }, {
    key: "onFocusCaption",
    value: function onFocusCaption() {
      if (!this.state.captionFocused) {
        this.setState({
          captionFocused: true
        });
      }
    }
  }, {
    key: "onImageClick",
    value: function onImageClick() {
      if (this.state.captionFocused) {
        this.setState({
          captionFocused: false
        });
      }
    }
  }, {
    key: "updateAlt",
    value: function updateAlt(newAlt) {
      this.props.setAttributes({
        alt: newAlt
      });
    }
  }, {
    key: "updateAlignment",
    value: function updateAlignment(nextAlign) {
      var extraUpdatedAttributes = ['wide', 'full'].indexOf(nextAlign) !== -1 ? {
        width: undefined,
        height: undefined
      } : {};
      this.props.setAttributes(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, extraUpdatedAttributes, {
        align: nextAlign
      }));
    }
  }, {
    key: "updateImageURL",
    value: function updateImageURL(url) {
      this.props.setAttributes({
        url: url,
        width: undefined,
        height: undefined
      });
    }
  }, {
    key: "updateWidth",
    value: function updateWidth(width) {
      this.props.setAttributes({
        width: parseInt(width, 10)
      });
    }
  }, {
    key: "updateHeight",
    value: function updateHeight(height) {
      this.props.setAttributes({
        height: parseInt(height, 10)
      });
    }
  }, {
    key: "updateDimensions",
    value: function updateDimensions() {
      var _this2 = this;

      var width = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : undefined;
      var height = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;
      return function () {
        _this2.props.setAttributes({
          width: width,
          height: height
        });
      };
    }
  }, {
    key: "getAvailableSizes",
    value: function getAvailableSizes() {
      return Object(lodash__WEBPACK_IMPORTED_MODULE_11__["get"])(this.props.image, ['media_details', 'sizes'], {});
    }
  }, {
    key: "getLinkDestinationOptions",
    value: function getLinkDestinationOptions() {
      return [{
        value: LINK_DESTINATION_NONE,
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('None')
      }, {
        value: LINK_DESTINATION_MEDIA,
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Media File')
      }, {
        value: LINK_DESTINATION_ATTACHMENT,
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Attachment Page')
      }, {
        value: LINK_DESTINATION_CUSTOM,
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Custom URL')
      }];
    }
  }, {
    key: "render",
    value: function render() {
      var _this3 = this;

      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes,
          isLargeViewport = _this$props2.isLargeViewport,
          isSelected = _this$props2.isSelected,
          className = _this$props2.className,
          maxWidth = _this$props2.maxWidth,
          noticeOperations = _this$props2.noticeOperations,
          noticeUI = _this$props2.noticeUI,
          toggleSelection = _this$props2.toggleSelection,
          isRTL = _this$props2.isRTL;
      var url = attributes.url,
          alt = attributes.alt,
          caption = attributes.caption,
          align = attributes.align,
          id = attributes.id,
          href = attributes.href,
          linkDestination = attributes.linkDestination,
          width = attributes.width,
          height = attributes.height;
      var controls = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["BlockAlignmentToolbar"], {
        value: align,
        onChange: this.updateAlignment
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["Toolbar"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["MediaUpload"], {
        onSelect: this.onSelectImage,
        allowedTypes: ALLOWED_MEDIA_TYPES,
        value: id,
        render: function render(_ref3) {
          var open = _ref3.open;
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["IconButton"], {
            className: "components-toolbar__control",
            label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Edit image'),
            icon: "edit",
            onClick: open
          });
        }
      })));

      if (!url) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Fragment"], null, controls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["MediaPlaceholder"], {
          icon: "format-image",
          labels: {
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Image'),
            name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('an image')
          },
          className: className,
          onSelect: this.onSelectImage,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice,
          accept: "image/*",
          allowedTypes: ALLOWED_MEDIA_TYPES
        }));
      }

      var classes = classnames__WEBPACK_IMPORTED_MODULE_10___default()(className, {
        'is-transient': 0 === url.indexOf('blob:'),
        'is-resized': !!width || !!height,
        'is-focused': isSelected
      });
      var availableSizes = this.getAvailableSizes();
      var isResizable = ['wide', 'full'].indexOf(align) === -1 && isLargeViewport;
      var isLinkURLInputDisabled = linkDestination !== LINK_DESTINATION_CUSTOM;

      var getInspectorControls = function getInspectorControls(imageWidth, imageHeight) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["PanelBody"], {
          title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Image Settings')
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["TextareaControl"], {
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Alt Text (Alternative Text)'),
          value: alt,
          onChange: _this3.updateAlt,
          help: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Describe the purpose of the image. Leave empty if the image is not a key part of the content.')
        }), !Object(lodash__WEBPACK_IMPORTED_MODULE_11__["isEmpty"])(availableSizes) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["SelectControl"], {
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Image Size'),
          value: url,
          options: Object(lodash__WEBPACK_IMPORTED_MODULE_11__["map"])(availableSizes, function (size, name) {
            return {
              value: size.source_url,
              label: Object(lodash__WEBPACK_IMPORTED_MODULE_11__["startCase"])(name)
            };
          }),
          onChange: _this3.updateImageURL
        }), isResizable && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("div", {
          className: "block-library-image__dimensions"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("p", {
          className: "block-library-image__dimensions__row"
        }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Image Dimensions')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("div", {
          className: "block-library-image__dimensions__row"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["TextControl"], {
          type: "number",
          className: "block-library-image__dimensions__width",
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Width'),
          value: width !== undefined ? width : '',
          placeholder: imageWidth,
          min: 1,
          onChange: _this3.updateWidth
        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["TextControl"], {
          type: "number",
          className: "block-library-image__dimensions__height",
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Height'),
          value: height !== undefined ? height : '',
          placeholder: imageHeight,
          min: 1,
          onChange: _this3.updateHeight
        })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("div", {
          className: "block-library-image__dimensions__row"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["ButtonGroup"], {
          "aria-label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Image Size')
        }, [25, 50, 75, 100].map(function (scale) {
          var scaledWidth = Math.round(imageWidth * (scale / 100));
          var scaledHeight = Math.round(imageHeight * (scale / 100));
          var isCurrent = width === scaledWidth && height === scaledHeight;
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["Button"], {
            key: scale,
            isSmall: true,
            isPrimary: isCurrent,
            "aria-pressed": isCurrent,
            onClick: _this3.updateDimensions(scaledWidth, scaledHeight)
          }, scale, "%");
        })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["Button"], {
          isSmall: true,
          onClick: _this3.updateDimensions()
        }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Reset'))))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["PanelBody"], {
          title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Link Settings')
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["SelectControl"], {
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Link To'),
          value: linkDestination,
          options: _this3.getLinkDestinationOptions(),
          onChange: _this3.onSetLinkDestination
        }), linkDestination !== LINK_DESTINATION_NONE && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["TextControl"], {
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Link URL'),
          value: href || '',
          onChange: _this3.onSetCustomHref,
          placeholder: !isLinkURLInputDisabled ? 'https://' : undefined,
          disabled: isLinkURLInputDisabled
        })));
      }; // Disable reason: Each block can be selected by clicking on it

      /* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */


      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Fragment"], null, controls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("figure", {
        className: classes
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_image_size__WEBPACK_IMPORTED_MODULE_20__["default"], {
        src: url,
        dirtynessTrigger: align
      }, function (sizes) {
        var imageWidthWithinContainer = sizes.imageWidthWithinContainer,
            imageHeightWithinContainer = sizes.imageHeightWithinContainer,
            imageWidth = sizes.imageWidth,
            imageHeight = sizes.imageHeight; // Disable reason: Image itself is not meant to be
        // interactive, but should direct focus to block
        // eslint-disable-next-line jsx-a11y/no-noninteractive-element-interactions

        var img = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("img", {
          src: url,
          alt: alt,
          onClick: _this3.onImageClick
        });

        if (!isResizable || !imageWidthWithinContainer) {
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Fragment"], null, getInspectorControls(imageWidth, imageHeight), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])("div", {
            style: {
              width: width,
              height: height
            }
          }, img));
        }

        var currentWidth = width || imageWidthWithinContainer;
        var currentHeight = height || imageHeightWithinContainer;
        var ratio = imageWidth / imageHeight;
        var minWidth = imageWidth < imageHeight ? MIN_SIZE : MIN_SIZE * ratio;
        var minHeight = imageHeight < imageWidth ? MIN_SIZE : MIN_SIZE / ratio;
        var showRightHandle = false;
        var showLeftHandle = false;
        /* eslint-disable no-lonely-if */
        // See https://github.com/WordPress/gutenberg/issues/7584.

        if (align === 'center') {
          // When the image is centered, show both handles.
          showRightHandle = true;
          showLeftHandle = true;
        } else if (isRTL) {
          // In RTL mode the image is on the right by default.
          // Show the right handle and hide the left handle only when it is aligned left.
          // Otherwise always show the left handle.
          if (align === 'left') {
            showRightHandle = true;
          } else {
            showLeftHandle = true;
          }
        } else {
          // Show the left handle and hide the right handle only when the image is aligned right.
          // Otherwise always show the right handle.
          if (align === 'right') {
            showLeftHandle = true;
          } else {
            showRightHandle = true;
          }
        }
        /* eslint-enable no-lonely-if */


        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Fragment"], null, getInspectorControls(imageWidth, imageHeight), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_14__["ResizableBox"], {
          size: width && height ? {
            width: width,
            height: height
          } : undefined,
          minWidth: minWidth,
          maxWidth: maxWidth,
          minHeight: minHeight,
          maxHeight: maxWidth / ratio,
          lockAspectRatio: true,
          enable: {
            top: false,
            right: showRightHandle,
            bottom: true,
            left: showLeftHandle
          },
          onResizeStart: function onResizeStart() {
            toggleSelection(false);
          },
          onResizeStop: function onResizeStop(event, direction, elt, delta) {
            setAttributes({
              width: parseInt(currentWidth + delta.width, 10),
              height: parseInt(currentHeight + delta.height, 10)
            });
            toggleSelection(true);
          }
        }, img));
      }), (!_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["RichText"].isEmpty(caption) || isSelected) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_16__["RichText"], {
        tagName: "figcaption",
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_12__["__"])('Write caption…'),
        value: caption,
        unstableOnFocus: this.onFocusCaption,
        onChange: function onChange(value) {
          return setAttributes({
            caption: value
          });
        },
        isSelected: this.state.captionFocused,
        inlineToolbar: true
      })));
      /* eslint-enable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */
    }
  }]);

  return ImageEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_18__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_15__["withSelect"])(function (select, props) {
  var _select = select('core'),
      getMedia = _select.getMedia;

  var _select2 = select('core/editor'),
      getEditorSettings = _select2.getEditorSettings;

  var id = props.attributes.id;

  var _getEditorSettings = getEditorSettings(),
      maxWidth = _getEditorSettings.maxWidth,
      isRTL = _getEditorSettings.isRTL;

  return {
    image: id ? getMedia(id) : null,
    maxWidth: maxWidth,
    isRTL: isRTL
  };
}), Object(_wordpress_viewport__WEBPACK_IMPORTED_MODULE_17__["withViewportMatch"])({
  isLargeViewport: 'medium'
}), _wordpress_components__WEBPACK_IMPORTED_MODULE_14__["withNotices"]])(ImageEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/image/image-size.js":
/*!********************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/image/image-size.js ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_8__);








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




var ImageSize =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(ImageSize, _Component);

  function ImageSize() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, ImageSize);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(ImageSize).apply(this, arguments));
    _this.state = {
      width: undefined,
      height: undefined
    };
    _this.bindContainer = _this.bindContainer.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.calculateSize = _this.calculateSize.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(ImageSize, [{
    key: "bindContainer",
    value: function bindContainer(ref) {
      this.container = ref;
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.src !== prevProps.src) {
        this.setState({
          width: undefined,
          height: undefined
        });
        this.fetchImageSize();
      }

      if (this.props.dirtynessTrigger !== prevProps.dirtynessTrigger) {
        this.calculateSize();
      }
    }
  }, {
    key: "componentDidMount",
    value: function componentDidMount() {
      this.fetchImageSize();
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      if (this.image) {
        this.image.onload = lodash__WEBPACK_IMPORTED_MODULE_7__["noop"];
      }
    }
  }, {
    key: "fetchImageSize",
    value: function fetchImageSize() {
      this.image = new window.Image();
      this.image.onload = this.calculateSize;
      this.image.src = this.props.src;
    }
  }, {
    key: "calculateSize",
    value: function calculateSize() {
      var maxWidth = this.container.clientWidth;
      var exceedMaxWidth = this.image.width > maxWidth;
      var ratio = this.image.height / this.image.width;
      var width = exceedMaxWidth ? maxWidth : this.image.width;
      var height = exceedMaxWidth ? maxWidth * ratio : this.image.height;
      this.setState({
        width: width,
        height: height
      });
    }
  }, {
    key: "render",
    value: function render() {
      var sizes = {
        imageWidth: this.image && this.image.width,
        imageHeight: this.image && this.image.height,
        containerWidth: this.container && this.container.clientWidth,
        containerHeight: this.container && this.container.clientHeight,
        imageWidthWithinContainer: this.state.width,
        imageHeightWithinContainer: this.state.height
      };
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("div", {
        ref: this.bindContainer
      }, this.props.children(sizes));
    }
  }]);

  return ImageSize;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_8__["withGlobalEvents"])({
  resize: 'calculateSize'
})(ImageSize));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/image/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/image/index.js ***!
  \***************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/blob */ "@wordpress/blob");
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blob__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/image/edit.js");





/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


var name = 'core/image';
var blockAttributes = {
  url: {
    type: 'string',
    source: 'attribute',
    selector: 'img',
    attribute: 'src'
  },
  alt: {
    type: 'string',
    source: 'attribute',
    selector: 'img',
    attribute: 'alt',
    default: ''
  },
  caption: {
    source: 'html',
    selector: 'figcaption'
  },
  href: {
    type: 'string',
    source: 'attribute',
    selector: 'figure > a',
    attribute: 'href'
  },
  id: {
    type: 'number'
  },
  align: {
    type: 'string'
  },
  width: {
    type: 'number'
  },
  height: {
    type: 'number'
  },
  linkDestination: {
    type: 'string',
    default: 'none'
  }
};
var imageSchema = {
  img: {
    attributes: ['src', 'alt'],
    classes: ['alignleft', 'aligncenter', 'alignright', 'alignnone', /^wp-image-\d+$/]
  }
};
var schema = {
  figure: {
    require: ['img'],
    children: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__["default"])({}, imageSchema, {
      a: {
        attributes: ['href'],
        children: imageSchema
      },
      figcaption: {
        children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["getPhrasingContentSchema"])()
      }
    })
  }
};
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Image'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('They’re worth 1,000 words! Insert a single image.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("path", {
    d: "M0,0h24v24H0V0z",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("path", {
    d: "m19 5v14h-14v-14h14m0-2h-14c-1.1 0-2 0.9-2 2v14c0 1.1 0.9 2 2 2h14c1.1 0 2-0.9 2-2v-14c0-1.1-0.9-2-2-2z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("path", {
    d: "m14.14 11.86l-3 3.87-2.14-2.59-3 3.86h12l-3.86-5.14z"
  })),
  category: 'common',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('photo')],
  attributes: blockAttributes,
  transforms: {
    from: [{
      type: 'raw',
      isMatch: function isMatch(node) {
        return node.nodeName === 'FIGURE' && !!node.querySelector('img');
      },
      schema: schema,
      transform: function transform(node) {
        // Search both figure and image classes. Alignment could be
        // set on either. ID is set on the image.
        var className = node.className + ' ' + node.querySelector('img').className;
        var alignMatches = /(?:^|\s)align(left|center|right)(?:$|\s)/.exec(className);
        var align = alignMatches ? alignMatches[1] : undefined;
        var idMatches = /(?:^|\s)wp-image-(\d+)(?:$|\s)/.exec(className);
        var id = idMatches ? Number(idMatches[1]) : undefined;
        var anchorElement = node.querySelector('a');
        var linkDestination = anchorElement && anchorElement.href ? 'custom' : undefined;
        var href = anchorElement && anchorElement.href ? anchorElement.href : undefined;
        var blockType = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["getBlockType"])('core/image');
        var attributes = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["getBlockAttributes"])(blockType, node.outerHTML, {
          align: align,
          id: id,
          linkDestination: linkDestination,
          href: href
        });
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/image', attributes);
      }
    }, {
      type: 'files',
      isMatch: function isMatch(files) {
        return files.length === 1 && files[0].type.indexOf('image/') === 0;
      },
      transform: function transform(files) {
        var file = files[0]; // We don't need to upload the media directly here
        // It's already done as part of the `componentDidMount`
        // int the image block

        var block = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/image', {
          url: Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_8__["createBlobURL"])(file)
        });
        return block;
      }
    }, {
      type: 'shortcode',
      tag: 'caption',
      attributes: {
        url: {
          type: 'string',
          source: 'attribute',
          attribute: 'src',
          selector: 'img'
        },
        alt: {
          type: 'string',
          source: 'attribute',
          attribute: 'alt',
          selector: 'img'
        },
        caption: {
          shortcode: function shortcode(attributes, _ref) {
            var _shortcode = _ref.shortcode;
            var content = _shortcode.content;
            return content.replace(/\s*<img[^>]*>\s/, '');
          }
        },
        href: {
          type: 'string',
          source: 'attribute',
          attribute: 'href',
          selector: 'a'
        },
        id: {
          type: 'number',
          shortcode: function shortcode(_ref2) {
            var id = _ref2.named.id;

            if (!id) {
              return;
            }

            return parseInt(id.replace('attachment_', ''), 10);
          }
        },
        align: {
          type: 'string',
          shortcode: function shortcode(_ref3) {
            var _ref3$named$align = _ref3.named.align,
                align = _ref3$named$align === void 0 ? 'alignnone' : _ref3$named$align;
            return align.replace('align', '');
          }
        }
      }
    }]
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var align = attributes.align,
        width = attributes.width;

    if ('left' === align || 'center' === align || 'right' === align || 'wide' === align || 'full' === align) {
      return {
        'data-align': align,
        'data-resized': !!width
      };
    }
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_9__["default"],
  save: function save(_ref4) {
    var _classnames;

    var attributes = _ref4.attributes;
    var url = attributes.url,
        alt = attributes.alt,
        caption = attributes.caption,
        align = attributes.align,
        href = attributes.href,
        width = attributes.width,
        height = attributes.height,
        id = attributes.id;
    var classes = classnames__WEBPACK_IMPORTED_MODULE_4___default()((_classnames = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_classnames, "align".concat(align), align), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_classnames, 'is-resized', width || height), _classnames));
    var image = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("img", {
      src: url,
      alt: alt,
      className: id ? "wp-image-".concat(id) : null,
      width: width,
      height: height
    });
    var figure = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["Fragment"], null, href ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("a", {
      href: href
    }, image) : image, !_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].isEmpty(caption) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].Content, {
      tagName: "figcaption",
      value: caption
    }));

    if ('left' === align || 'right' === align || 'center' === align) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("figure", {
        className: classes
      }, figure));
    }

    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("figure", {
      className: classes
    }, figure);
  },
  deprecated: [{
    attributes: blockAttributes,
    save: function save(_ref5) {
      var _classnames2;

      var attributes = _ref5.attributes;
      var url = attributes.url,
          alt = attributes.alt,
          caption = attributes.caption,
          align = attributes.align,
          href = attributes.href,
          width = attributes.width,
          height = attributes.height,
          id = attributes.id;
      var classes = classnames__WEBPACK_IMPORTED_MODULE_4___default()((_classnames2 = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_classnames2, "align".concat(align), align), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(_classnames2, 'is-resized', width || height), _classnames2));
      var image = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("img", {
        src: url,
        alt: alt,
        className: id ? "wp-image-".concat(id) : null,
        width: width,
        height: height
      });
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("figure", {
        className: classes
      }, href ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("a", {
        href: href
      }, image) : image, !_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].isEmpty(caption) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].Content, {
        tagName: "figcaption",
        value: caption
      }));
    }
  }, {
    attributes: blockAttributes,
    save: function save(_ref6) {
      var attributes = _ref6.attributes;
      var url = attributes.url,
          alt = attributes.alt,
          caption = attributes.caption,
          align = attributes.align,
          href = attributes.href,
          width = attributes.width,
          height = attributes.height,
          id = attributes.id;
      var image = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("img", {
        src: url,
        alt: alt,
        className: id ? "wp-image-".concat(id) : null,
        width: width,
        height: height
      });
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("figure", {
        className: align ? "align".concat(align) : null
      }, href ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("a", {
        href: href
      }, image) : image, !_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].isEmpty(caption) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].Content, {
        tagName: "figcaption",
        value: caption
      }));
    }
  }, {
    attributes: blockAttributes,
    save: function save(_ref7) {
      var attributes = _ref7.attributes;
      var url = attributes.url,
          alt = attributes.alt,
          caption = attributes.caption,
          align = attributes.align,
          href = attributes.href,
          width = attributes.width,
          height = attributes.height;
      var extraImageProps = width || height ? {
        width: width,
        height: height
      } : {};
      var image = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("img", Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
        src: url,
        alt: alt
      }, extraImageProps));
      var figureStyle = {};

      if (width) {
        figureStyle = {
          width: width
        };
      } else if (align === 'left' || align === 'right') {
        figureStyle = {
          maxWidth: '50%'
        };
      }

      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("figure", {
        className: align ? "align".concat(align) : null,
        style: figureStyle
      }, href ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("a", {
        href: href
      }, image) : image, !_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].isEmpty(caption) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].Content, {
        tagName: "figcaption",
        value: caption
      }));
    }
  }]
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/index.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/index.js ***!
  \*********************************************************************/
/*! exports provided: registerCoreBlocks */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "registerCoreBlocks", function() { return registerCoreBlocks; });
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _paragraph__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./paragraph */ "./node_modules/@wordpress/block-library/build-module/paragraph/index.js");
/* harmony import */ var _image__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./image */ "./node_modules/@wordpress/block-library/build-module/image/index.js");
/* harmony import */ var _heading__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./heading */ "./node_modules/@wordpress/block-library/build-module/heading/index.js");
/* harmony import */ var _quote__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./quote */ "./node_modules/@wordpress/block-library/build-module/quote/index.js");
/* harmony import */ var _gallery__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./gallery */ "./node_modules/@wordpress/block-library/build-module/gallery/index.js");
/* harmony import */ var _archives__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./archives */ "./node_modules/@wordpress/block-library/build-module/archives/index.js");
/* harmony import */ var _audio__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./audio */ "./node_modules/@wordpress/block-library/build-module/audio/index.js");
/* harmony import */ var _button__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./button */ "./node_modules/@wordpress/block-library/build-module/button/index.js");
/* harmony import */ var _categories__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./categories */ "./node_modules/@wordpress/block-library/build-module/categories/index.js");
/* harmony import */ var _code__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./code */ "./node_modules/@wordpress/block-library/build-module/code/index.js");
/* harmony import */ var _columns__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./columns */ "./node_modules/@wordpress/block-library/build-module/columns/index.js");
/* harmony import */ var _columns_column__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./columns/column */ "./node_modules/@wordpress/block-library/build-module/columns/column.js");
/* harmony import */ var _cover_image__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./cover-image */ "./node_modules/@wordpress/block-library/build-module/cover-image/index.js");
/* harmony import */ var _embed__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./embed */ "./node_modules/@wordpress/block-library/build-module/embed/index.js");
/* harmony import */ var _file__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./file */ "./node_modules/@wordpress/block-library/build-module/file/index.js");
/* harmony import */ var _html__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./html */ "./node_modules/@wordpress/block-library/build-module/html/index.js");
/* harmony import */ var _latest_comments__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./latest-comments */ "./node_modules/@wordpress/block-library/build-module/latest-comments/index.js");
/* harmony import */ var _latest_posts__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./latest-posts */ "./node_modules/@wordpress/block-library/build-module/latest-posts/index.js");
/* harmony import */ var _list__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./list */ "./node_modules/@wordpress/block-library/build-module/list/index.js");
/* harmony import */ var _more__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./more */ "./node_modules/@wordpress/block-library/build-module/more/index.js");
/* harmony import */ var _nextpage__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./nextpage */ "./node_modules/@wordpress/block-library/build-module/nextpage/index.js");
/* harmony import */ var _preformatted__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./preformatted */ "./node_modules/@wordpress/block-library/build-module/preformatted/index.js");
/* harmony import */ var _pullquote__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./pullquote */ "./node_modules/@wordpress/block-library/build-module/pullquote/index.js");
/* harmony import */ var _block__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./block */ "./node_modules/@wordpress/block-library/build-module/block/index.js");
/* harmony import */ var _separator__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./separator */ "./node_modules/@wordpress/block-library/build-module/separator/index.js");
/* harmony import */ var _shortcode__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./shortcode */ "./node_modules/@wordpress/block-library/build-module/shortcode/index.js");
/* harmony import */ var _spacer__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./spacer */ "./node_modules/@wordpress/block-library/build-module/spacer/index.js");
/* harmony import */ var _subhead__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./subhead */ "./node_modules/@wordpress/block-library/build-module/subhead/index.js");
/* harmony import */ var _table__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./table */ "./node_modules/@wordpress/block-library/build-module/table/index.js");
/* harmony import */ var _template__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./template */ "./node_modules/@wordpress/block-library/build-module/template/index.js");
/* harmony import */ var _text_columns__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./text-columns */ "./node_modules/@wordpress/block-library/build-module/text-columns/index.js");
/* harmony import */ var _verse__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./verse */ "./node_modules/@wordpress/block-library/build-module/verse/index.js");
/* harmony import */ var _video__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./video */ "./node_modules/@wordpress/block-library/build-module/video/index.js");
/* harmony import */ var _classic__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./classic */ "./node_modules/@wordpress/block-library/build-module/classic/index.js");


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



































var registerCoreBlocks = function registerCoreBlocks() {
  [// Common blocks are grouped at the top to prioritize their display
  // in various contexts — like the inserter and auto-complete components.
  _paragraph__WEBPACK_IMPORTED_MODULE_3__, _image__WEBPACK_IMPORTED_MODULE_4__, _heading__WEBPACK_IMPORTED_MODULE_5__, _gallery__WEBPACK_IMPORTED_MODULE_7__, _list__WEBPACK_IMPORTED_MODULE_21__, _quote__WEBPACK_IMPORTED_MODULE_6__, // Register all remaining core blocks.
  _shortcode__WEBPACK_IMPORTED_MODULE_28__, _archives__WEBPACK_IMPORTED_MODULE_8__, _audio__WEBPACK_IMPORTED_MODULE_9__, _button__WEBPACK_IMPORTED_MODULE_10__, _categories__WEBPACK_IMPORTED_MODULE_11__, _code__WEBPACK_IMPORTED_MODULE_12__, _columns__WEBPACK_IMPORTED_MODULE_13__, _columns_column__WEBPACK_IMPORTED_MODULE_14__, _cover_image__WEBPACK_IMPORTED_MODULE_15__, _embed__WEBPACK_IMPORTED_MODULE_16__].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(_embed__WEBPACK_IMPORTED_MODULE_16__["common"]), Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(_embed__WEBPACK_IMPORTED_MODULE_16__["others"]), [_file__WEBPACK_IMPORTED_MODULE_17__, window.wp && window.wp.oldEditor ? _classic__WEBPACK_IMPORTED_MODULE_36__ : null, // Only add the classic block in WP Context
  _html__WEBPACK_IMPORTED_MODULE_18__, _latest_comments__WEBPACK_IMPORTED_MODULE_19__, _latest_posts__WEBPACK_IMPORTED_MODULE_20__, _more__WEBPACK_IMPORTED_MODULE_22__, _nextpage__WEBPACK_IMPORTED_MODULE_23__, _preformatted__WEBPACK_IMPORTED_MODULE_24__, _pullquote__WEBPACK_IMPORTED_MODULE_25__, _separator__WEBPACK_IMPORTED_MODULE_27__, _block__WEBPACK_IMPORTED_MODULE_26__, _spacer__WEBPACK_IMPORTED_MODULE_29__, _subhead__WEBPACK_IMPORTED_MODULE_30__, _table__WEBPACK_IMPORTED_MODULE_31__, _template__WEBPACK_IMPORTED_MODULE_32__, _text_columns__WEBPACK_IMPORTED_MODULE_33__, _verse__WEBPACK_IMPORTED_MODULE_34__, _video__WEBPACK_IMPORTED_MODULE_35__]).forEach(function (block) {
    if (!block) {
      return;
    }

    var name = block.name,
        settings = block.settings;
    Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["registerBlockType"])(name, settings);
  });
  Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["setDefaultBlockName"])(_paragraph__WEBPACK_IMPORTED_MODULE_3__["name"]);

  if (window.wp && window.wp.oldEditor) {
    Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["setUnknownTypeHandlerName"])(_classic__WEBPACK_IMPORTED_MODULE_36__["name"]);
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/latest-comments/edit.js":
/*!************************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/latest-comments/edit.js ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_10__);









/**
 * WordPress dependencies
 */




/**
 * Minimum number of comments a user can show using this block.
 *
 * @type {number}
 */

var MIN_COMMENTS = 1;
/**
 * Maximum number of comments a user can show using this block.
 *
 * @type {number}
 */

var MAX_COMMENTS = 100;

var LatestComments =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__["default"])(LatestComments, _Component);

  function LatestComments() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__["default"])(this, LatestComments);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__["default"])(LatestComments).apply(this, arguments));
    _this.setAlignment = _this.setAlignment.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.setCommentsToShow = _this.setCommentsToShow.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this))); // Create toggles for each attribute; we create them here rather than
    // passing `this.createToggleAttribute( 'displayAvatar' )` directly to
    // `onChange` to avoid re-renders.

    _this.toggleDisplayAvatar = _this.createToggleAttribute('displayAvatar');
    _this.toggleDisplayDate = _this.createToggleAttribute('displayDate');
    _this.toggleDisplayExcerpt = _this.createToggleAttribute('displayExcerpt');
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__["default"])(LatestComments, [{
    key: "createToggleAttribute",
    value: function createToggleAttribute(propName) {
      var _this2 = this;

      return function () {
        var value = _this2.props.attributes[propName];
        var setAttributes = _this2.props.setAttributes;
        setAttributes(Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({}, propName, !value));
      };
    }
  }, {
    key: "setAlignment",
    value: function setAlignment(align) {
      this.props.setAttributes({
        align: align
      });
    }
  }, {
    key: "setCommentsToShow",
    value: function setCommentsToShow(commentsToShow) {
      this.props.setAttributes({
        commentsToShow: commentsToShow
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props$attribute = this.props.attributes,
          align = _this$props$attribute.align,
          commentsToShow = _this$props$attribute.commentsToShow,
          displayAvatar = _this$props$attribute.displayAvatar,
          displayDate = _this$props$attribute.displayDate,
          displayExcerpt = _this$props$attribute.displayExcerpt;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_10__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_10__["BlockAlignmentToolbar"], {
        value: align,
        onChange: this.setAlignment
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_10__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Latest Comments Settings')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Display Avatar'),
        checked: displayAvatar,
        onChange: this.toggleDisplayAvatar
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Display Date'),
        checked: displayDate,
        onChange: this.toggleDisplayDate
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Display Excerpt'),
        checked: displayExcerpt,
        onChange: this.toggleDisplayExcerpt
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["RangeControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Number of Comments'),
        value: commentsToShow,
        onChange: this.setCommentsToShow,
        min: MIN_COMMENTS,
        max: MAX_COMMENTS
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Disabled"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_10__["ServerSideRender"], {
        block: "core/latest-comments",
        attributes: this.props.attributes
      })));
    }
  }]);

  return LatestComments;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (LatestComments);


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/latest-comments/index.js":
/*!*************************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/latest-comments/index.js ***!
  \*************************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/latest-comments/edit.js");


/**
 * WordPress dependencies.
 */

/**
 * Internal dependencies.
 */


var name = 'core/latest-comments';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Latest Comments'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Show a list of your site’s most recent comments.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M22 4l-2-2H4L2 4v12l2 2h14l4 4V4zm-2 0v13l-1-1H4V4h16z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M6 12h12v2H6zM6 9h12v2H6zM6 6h12v2H6z"
  }))),
  category: 'widgets',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('recent comments')],
  supports: {
    html: false
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var align = attributes.align; // TODO: Use consistent values across the app;
    // see: https://github.com/WordPress/gutenberg/issues/7908.

    if (['left', 'center', 'right', 'wide', 'full'].includes(align)) {
      return {
        'data-align': align
      };
    }
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/latest-posts/edit.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/latest-posts/edit.js ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var moment__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! moment */ "moment");
/* harmony import */ var moment__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(moment__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @wordpress/html-entities */ "@wordpress/html-entities");
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_16__);










/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */







var MAX_POSTS_COLUMNS = 6;

var LatestPostsEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__["default"])(LatestPostsEdit, _Component);

  function LatestPostsEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__["default"])(this, LatestPostsEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__["default"])(LatestPostsEdit).apply(this, arguments));
    _this.toggleDisplayPostDate = _this.toggleDisplayPostDate.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__["default"])(LatestPostsEdit, [{
    key: "toggleDisplayPostDate",
    value: function toggleDisplayPostDate() {
      var displayPostDate = this.props.attributes.displayPostDate;
      var setAttributes = this.props.setAttributes;
      setAttributes({
        displayPostDate: !displayPostDate
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          categoriesList = _this$props.categoriesList,
          setAttributes = _this$props.setAttributes,
          latestPosts = _this$props.latestPosts;
      var displayPostDate = attributes.displayPostDate,
          align = attributes.align,
          postLayout = attributes.postLayout,
          columns = attributes.columns,
          order = attributes.order,
          orderBy = attributes.orderBy,
          categories = attributes.categories,
          postsToShow = attributes.postsToShow;
      var inspectorControls = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_15__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__["__"])('Latest Posts Settings')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["QueryControls"], Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__["default"])({
        order: order,
        orderBy: orderBy
      }, {
        numberOfItems: postsToShow,
        categoriesList: categoriesList,
        selectedCategoryId: categories,
        onOrderChange: function onOrderChange(value) {
          return setAttributes({
            order: value
          });
        },
        onOrderByChange: function onOrderByChange(value) {
          return setAttributes({
            orderBy: value
          });
        },
        onCategoryChange: function onCategoryChange(value) {
          return setAttributes({
            categories: '' !== value ? value : undefined
          });
        },
        onNumberOfItemsChange: function onNumberOfItemsChange(value) {
          return setAttributes({
            postsToShow: value
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__["__"])('Display post date'),
        checked: displayPostDate,
        onChange: this.toggleDisplayPostDate
      }), postLayout === 'grid' && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["RangeControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__["__"])('Columns'),
        value: columns,
        onChange: function onChange(value) {
          return setAttributes({
            columns: value
          });
        },
        min: 2,
        max: !hasPosts ? MAX_POSTS_COLUMNS : Math.min(MAX_POSTS_COLUMNS, latestPosts.length)
      })));
      var hasPosts = Array.isArray(latestPosts) && latestPosts.length;

      if (!hasPosts) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Fragment"], null, inspectorControls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["Placeholder"], {
          icon: "admin-post",
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__["__"])('Latest Posts')
        }, !Array.isArray(latestPosts) ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["Spinner"], null) : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__["__"])('No posts found.')));
      } // Removing posts from display should be instant.


      var displayPosts = latestPosts.length > postsToShow ? latestPosts.slice(0, postsToShow) : latestPosts;
      var layoutControls = [{
        icon: 'list-view',
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__["__"])('List View'),
        onClick: function onClick() {
          return setAttributes({
            postLayout: 'list'
          });
        },
        isActive: postLayout === 'list'
      }, {
        icon: 'grid-view',
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__["__"])('Grid View'),
        onClick: function onClick() {
          return setAttributes({
            postLayout: 'grid'
          });
        },
        isActive: postLayout === 'grid'
      }];
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Fragment"], null, inspectorControls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_15__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_15__["BlockAlignmentToolbar"], {
        value: align,
        onChange: function onChange(nextAlign) {
          setAttributes({
            align: nextAlign
          });
        },
        controls: ['center', 'wide', 'full']
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["Toolbar"], {
        controls: layoutControls
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("ul", {
        className: classnames__WEBPACK_IMPORTED_MODULE_11___default()(this.props.className, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({
          'is-grid': postLayout === 'grid'
        }, "columns-".concat(columns), postLayout === 'grid'))
      }, displayPosts.map(function (post, i) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("li", {
          key: i
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("a", {
          href: post.link,
          target: "_blank"
        }, Object(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_14__["decodeEntities"])(post.title.rendered.trim()) || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_13__["__"])('(Untitled)')), displayPostDate && post.date_gmt && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("time", {
          dateTime: moment__WEBPACK_IMPORTED_MODULE_10___default()(post.date_gmt).utc().format(),
          className: "".concat(_this2.props.className, "__post-date")
        }, moment__WEBPACK_IMPORTED_MODULE_10___default()(post.date_gmt).local().format('MMMM DD, Y')));
      })));
    }
  }]);

  return LatestPostsEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_16__["withSelect"])(function (select, props) {
  var _props$attributes = props.attributes,
      postsToShow = _props$attributes.postsToShow,
      order = _props$attributes.order,
      orderBy = _props$attributes.orderBy,
      categories = _props$attributes.categories;

  var _select = select('core'),
      getEntityRecords = _select.getEntityRecords;

  var latestPostsQuery = Object(lodash__WEBPACK_IMPORTED_MODULE_9__["pickBy"])({
    categories: categories,
    order: order,
    orderby: orderBy,
    per_page: postsToShow
  }, function (value) {
    return !Object(lodash__WEBPACK_IMPORTED_MODULE_9__["isUndefined"])(value);
  });
  var categoriesListQuery = {
    per_page: 100
  };
  return {
    latestPosts: getEntityRecords('postType', 'post', latestPostsQuery),
    categoriesList: getEntityRecords('taxonomy', 'category', categoriesListQuery)
  };
})(LatestPostsEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/latest-posts/index.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/latest-posts/index.js ***!
  \**********************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/latest-posts/edit.js");


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var name = 'core/latest-posts';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Latest Posts'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Display a list of your most recent posts.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M0,0h24v24H0V0z",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "11",
    y: "7",
    width: "6",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "11",
    y: "11",
    width: "6",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "11",
    y: "15",
    width: "6",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "7",
    y: "7",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "7",
    y: "11",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "7",
    y: "15",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M20.1,3H3.9C3.4,3,3,3.4,3,3.9v16.2C3,20.5,3.4,21,3.9,21h16.2c0.4,0,0.9-0.5,0.9-0.9V3.9C21,3.4,20.5,3,20.1,3z M19,19H5V5h14V19z"
  })),
  category: 'widgets',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('recent posts')],
  supports: {
    html: false
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var align = attributes.align;

    if ('left' === align || 'right' === align || 'wide' === align || 'full' === align) {
      return {
        'data-align': align
      };
    }
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/list/index.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/list/index.js ***!
  \**************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__);










/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







var listContentSchema = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["getPhrasingContentSchema"])(), {
  ul: {},
  ol: {
    attributes: ['type']
  }
}); // Recursion is needed.
// Possible: ul > li > ul.
// Impossible: ul > ul.


['ul', 'ol'].forEach(function (tag) {
  listContentSchema[tag].children = {
    li: {
      children: listContentSchema
    }
  };
});
var supports = {
  className: false
};
var schema = {
  ordered: {
    type: 'boolean',
    default: false
  },
  values: {
    source: 'html',
    selector: 'ol,ul',
    multiline: 'li'
  }
};
var name = 'core/list';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('List'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Numbers, bullets, up to you. Add a list of items.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("path", {
    d: "M9 19h12v-2H9v2zm0-6h12v-2H9v2zm0-8v2h12V5H9zm-4-.5c-.828 0-1.5.672-1.5 1.5S4.172 7.5 5 7.5 6.5 6.828 6.5 6 5.828 4.5 5 4.5zm0 6c-.828 0-1.5.672-1.5 1.5s.672 1.5 1.5 1.5 1.5-.672 1.5-1.5-.672-1.5-1.5-1.5zm0 6c-.828 0-1.5.672-1.5 1.5s.672 1.5 1.5 1.5 1.5-.672 1.5-1.5-.672-1.5-1.5-1.5z"
  }))),
  category: 'common',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('bullet list'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('ordered list'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('numbered list')],
  attributes: schema,
  supports: supports,
  transforms: {
    from: [{
      type: 'block',
      isMultiBlock: true,
      blocks: ['core/paragraph'],
      transform: function transform(blockAttributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])('core/list', {
          values: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__["toHTMLString"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__["join"])(blockAttributes.map(function (_ref) {
            var content = _ref.content;
            return Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__["replace"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__["create"])({
              html: content
            }), /\n/g, "\u2028");
          }), "\u2028"), 'li')
        });
      }
    }, {
      type: 'block',
      blocks: ['core/quote'],
      transform: function transform(_ref2) {
        var value = _ref2.value;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])('core/list', {
          values: value
        });
      }
    }, {
      type: 'raw',
      selector: 'ol,ul',
      schema: {
        ol: listContentSchema.ol,
        ul: listContentSchema.ul
      },
      transform: function transform(node) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])('core/list', Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["getBlockAttributes"])(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["getBlockType"])('core/list'), node.outerHTML), {
          ordered: node.nodeName === 'OL'
        }));
      }
    }, {
      type: 'pattern',
      regExp: /^[*-]\s/,
      transform: function transform(_ref3) {
        var content = _ref3.content;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])('core/list', {
          values: content
        });
      }
    }, {
      type: 'pattern',
      regExp: /^1[.)]\s/,
      transform: function transform(_ref4) {
        var content = _ref4.content;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])('core/list', {
          ordered: true,
          values: content
        });
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(_ref5) {
        var values = _ref5.values;
        return Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__["split"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__["create"])({
          html: values,
          multilineTag: 'li'
        }), "\u2028").map(function (piece) {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])('core/paragraph', {
            content: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__["toHTMLString"])(piece)
          });
        });
      }
    }, {
      type: 'block',
      blocks: ['core/quote'],
      transform: function transform(_ref6) {
        var values = _ref6.values;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])('core/quote', {
          value: values
        });
      }
    }]
  },
  deprecated: [{
    supports: supports,
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, Object(lodash__WEBPACK_IMPORTED_MODULE_9__["omit"])(schema, ['ordered']), {
      nodeName: {
        type: 'string',
        source: 'property',
        selector: 'ol,ul',
        property: 'nodeName',
        default: 'UL'
      }
    }),
    migrate: function migrate(attributes) {
      var nodeName = attributes.nodeName,
          migratedAttributes = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_6__["default"])(attributes, ["nodeName"]);

      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, migratedAttributes, {
        ordered: 'OL' === nodeName
      });
    },
    save: function save(_ref7) {
      var attributes = _ref7.attributes;
      var nodeName = attributes.nodeName,
          values = attributes.values;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["RichText"].Content, {
        tagName: nodeName.toLowerCase(),
        value: values
      });
    }
  }],
  merge: function merge(attributes, attributesToMerge) {
    var values = attributesToMerge.values;

    if (!values || values === '<li></li>') {
      return attributes;
    }

    return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, attributes, {
      values: attributes.values + values
    });
  },
  edit:
  /*#__PURE__*/
  function (_Component) {
    Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(edit, _Component);

    function edit() {
      var _this;

      Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, edit);

      _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(edit).apply(this, arguments));
      _this.setupEditor = _this.setupEditor.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
      _this.getEditorSettings = _this.getEditorSettings.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
      _this.setNextValues = _this.setNextValues.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
      _this.state = {
        internalListType: null
      };
      return _this;
    }

    Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(edit, [{
      key: "findInternalListType",
      value: function findInternalListType(_ref8) {
        var parents = _ref8.parents;
        var list = Object(lodash__WEBPACK_IMPORTED_MODULE_9__["find"])(parents, function (node) {
          return node.nodeName === 'UL' || node.nodeName === 'OL';
        });
        return list ? list.nodeName : null;
      }
    }, {
      key: "setupEditor",
      value: function setupEditor(editor) {
        var _this2 = this;

        editor.on('nodeChange', function (nodeInfo) {
          _this2.setState({
            internalListType: _this2.findInternalListType(nodeInfo)
          });
        }); // Check for languages that do not have square brackets on their keyboards.

        var lang = window.navigator.browserLanguage || window.navigator.language;
        var keyboardHasSquareBracket = !/^(?:fr|nl|sv|ru|de|es|it)/.test(lang);

        if (keyboardHasSquareBracket) {
          // `[` is keycode 219; `]` is keycode 221.
          editor.shortcuts.add('meta+219', 'Decrease indent', 'Outdent');
          editor.shortcuts.add('meta+221', 'Increase indent', 'Indent');
        } else {
          editor.shortcuts.add('meta+shift+m', 'Decrease indent', 'Outdent');
          editor.shortcuts.add('meta+m', 'Increase indent', 'Indent');
        }

        this.editor = editor;
      }
    }, {
      key: "createSetListType",
      value: function createSetListType(type, command) {
        var _this3 = this;

        return function () {
          var setAttributes = _this3.props.setAttributes;
          var internalListType = _this3.state.internalListType;

          if (internalListType) {
            // Only change list types, don't toggle off internal lists.
            if (internalListType !== type && _this3.editor) {
              _this3.editor.execCommand(command);
            }
          } else {
            setAttributes({
              ordered: type === 'OL'
            });
          }
        };
      }
    }, {
      key: "createExecCommand",
      value: function createExecCommand(command) {
        var _this4 = this;

        return function () {
          if (_this4.editor) {
            _this4.editor.execCommand(command);
          }
        };
      }
    }, {
      key: "getEditorSettings",
      value: function getEditorSettings(editorSettings) {
        return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_7__["default"])({}, editorSettings, {
          plugins: (editorSettings.plugins || []).concat('lists'),
          lists_indent_on_tab: false
        });
      }
    }, {
      key: "setNextValues",
      value: function setNextValues(nextValues) {
        this.props.setAttributes({
          values: nextValues
        });
      }
    }, {
      key: "render",
      value: function render() {
        var _this$props = this.props,
            attributes = _this$props.attributes,
            insertBlocksAfter = _this$props.insertBlocksAfter,
            setAttributes = _this$props.setAttributes,
            mergeBlocks = _this$props.mergeBlocks,
            onReplace = _this$props.onReplace,
            className = _this$props.className;
        var ordered = attributes.ordered,
            values = attributes.values;
        var tagName = ordered ? 'ol' : 'ul';
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["BlockControls"], {
          controls: [{
            icon: 'editor-ul',
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Convert to unordered list'),
            isActive: !ordered,
            onClick: this.createSetListType('UL', 'InsertUnorderedList')
          }, {
            icon: 'editor-ol',
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Convert to ordered list'),
            isActive: ordered,
            onClick: this.createSetListType('OL', 'InsertOrderedList')
          }, {
            icon: 'editor-outdent',
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Outdent list item'),
            onClick: this.createExecCommand('Outdent')
          }, {
            icon: 'editor-indent',
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Indent list item'),
            onClick: this.createExecCommand('Indent')
          }]
        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["RichText"], {
          multiline: "li",
          tagName: tagName,
          unstableGetSettings: this.getEditorSettings,
          unstableOnSetup: this.setupEditor,
          onChange: this.setNextValues,
          value: values,
          wrapperClassName: "block-library-list",
          className: className,
          placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Write list…'),
          onMerge: mergeBlocks,
          onSplit: insertBlocksAfter ? function (before, after) {
            for (var _len = arguments.length, blocks = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
              blocks[_key - 2] = arguments[_key];
            }

            if (!blocks.length) {
              blocks.push(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])('core/paragraph'));
            }

            if (after !== '<li></li>') {
              blocks.push(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])('core/list', {
                ordered: ordered,
                values: after
              }));
            }

            setAttributes({
              values: before
            });
            insertBlocksAfter(blocks);
          } : undefined,
          onRemove: function onRemove() {
            return onReplace([]);
          }
        }));
      }
    }]);

    return edit;
  }(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Component"]),
  save: function save(_ref9) {
    var attributes = _ref9.attributes;
    var ordered = attributes.ordered,
        values = attributes.values;
    var tagName = ordered ? 'ol' : 'ul';
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["RichText"].Content, {
      tagName: tagName,
      value: values,
      multiline: "li"
    });
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/more/edit.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/more/edit.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return MoreEdit; });
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__);








/**
 * WordPress dependencies
 */







var MoreEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(MoreEdit, _Component);

  function MoreEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, MoreEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(MoreEdit).apply(this, arguments));
    _this.onChangeInput = _this.onChangeInput.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onKeyDown = _this.onKeyDown.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.state = {
      defaultText: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('Read more')
    };
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(MoreEdit, [{
    key: "onChangeInput",
    value: function onChangeInput(event) {
      // Set defaultText to an empty string, allowing the user to clear/replace the input field's text
      this.setState({
        defaultText: ''
      });
      var value = event.target.value.length === 0 ? undefined : event.target.value;
      this.props.setAttributes({
        customText: value
      });
    }
  }, {
    key: "onKeyDown",
    value: function onKeyDown(event) {
      var keyCode = event.keyCode;
      var insertBlocksAfter = this.props.insertBlocksAfter;

      if (keyCode === _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__["ENTER"]) {
        insertBlocksAfter([Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["createBlock"])(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_11__["getDefaultBlockName"])())]);
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props$attribute = this.props.attributes,
          customText = _this$props$attribute.customText,
          noTeaser = _this$props$attribute.noTeaser;
      var setAttributes = this.props.setAttributes;

      var toggleNoTeaser = function toggleNoTeaser() {
        return setAttributes({
          noTeaser: !noTeaser
        });
      };

      var defaultText = this.state.defaultText;
      var value = customText !== undefined ? customText : defaultText;
      var inputLength = value.length + 1;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_9__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('Hide the teaser before the "More" tag'),
        checked: !!noTeaser,
        onChange: toggleNoTeaser
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("div", {
        className: "wp-block-more"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("input", {
        type: "text",
        value: value,
        size: inputLength,
        onChange: this.onChangeInput,
        onKeyDown: this.onKeyDown
      })));
    }
  }]);

  return MoreEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Component"]);




/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/more/index.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/more/index.js ***!
  \**************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/more/edit.js");


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var name = 'core/more';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["_x"])('More', 'block name'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Want to show only part of this post on your blog’s home page? Insert a "More" block where you want the split.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M2 9v2h19V9H2zm0 6h5v-2H2v2zm7 0h5v-2H9v2zm7 0h5v-2h-5v2z"
  }))),
  category: 'layout',
  supports: {
    customClassName: false,
    className: false,
    html: false,
    multiple: false
  },
  attributes: {
    customText: {
      type: 'string'
    },
    noTeaser: {
      type: 'boolean',
      default: false
    }
  },
  transforms: {
    from: [{
      type: 'raw',
      schema: {
        'wp-block': {
          attributes: ['data-block']
        }
      },
      isMatch: function isMatch(node) {
        return node.dataset && node.dataset.block === 'core/more';
      },
      transform: function transform(node) {
        var _node$dataset = node.dataset,
            customText = _node$dataset.customText,
            noTeaser = _node$dataset.noTeaser;
        var attrs = {}; // Don't copy unless defined and not an empty string

        if (customText) {
          attrs.customText = customText;
        } // Special handling for boolean


        if (noTeaser === '') {
          attrs.noTeaser = true;
        }

        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/more', attrs);
      }
    }]
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_4__["default"],
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var customText = attributes.customText,
        noTeaser = attributes.noTeaser;
    var moreTag = customText ? "<!--more ".concat(customText, "-->") : '<!--more-->';
    var noTeaserTag = noTeaser ? '<!--noteaser-->' : '';
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["RawHTML"], null, Object(lodash__WEBPACK_IMPORTED_MODULE_1__["compact"])([moreTag, noTeaserTag]).join('\n'));
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/nextpage/index.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/nextpage/index.js ***!
  \******************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */



var name = 'core/nextpage';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Page break'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('This block allows you to set break points on your post. Visitors of your blog are then presented with content split into multiple pages.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M9 12h6v-2H9zm-7 0h5v-2H2zm15 0h5v-2h-5zm3 2v2l-6 6H6a2 2 0 0 1-2-2v-6h2v6h6v-4a2 2 0 0 1 2-2h6zM4 8V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4h-2V4H6v4z"
  }))),
  category: 'layout',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('next page'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('pagination')],
  supports: {
    customClassName: false,
    className: false,
    html: false
  },
  attributes: {},
  transforms: {
    from: [{
      type: 'raw',
      schema: {
        'wp-block': {
          attributes: ['data-block']
        }
      },
      isMatch: function isMatch(node) {
        return node.dataset && node.dataset.block === 'core/nextpage';
      },
      transform: function transform() {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/nextpage', {});
      }
    }]
  },
  edit: function edit() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "wp-block-nextpage"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Page break')));
  },
  save: function save() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["RawHTML"], null, '<!--nextpage-->');
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/paragraph/edit.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/paragraph/edit.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_15__);











/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







var _window = window,
    getComputedStyle = _window.getComputedStyle;
var name = 'core/paragraph';
var applyFallbackStyles = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["withFallbackStyles"])(function (node, ownProps) {
  var _ownProps$attributes = ownProps.attributes,
      textColor = _ownProps$attributes.textColor,
      backgroundColor = _ownProps$attributes.backgroundColor,
      fontSize = _ownProps$attributes.fontSize,
      customFontSize = _ownProps$attributes.customFontSize;
  var editableNode = node.querySelector('[contenteditable="true"]'); //verify if editableNode is available, before using getComputedStyle.

  var computedStyles = editableNode ? getComputedStyle(editableNode) : null;
  return {
    fallbackBackgroundColor: backgroundColor || !computedStyles ? undefined : computedStyles.backgroundColor,
    fallbackTextColor: textColor || !computedStyles ? undefined : computedStyles.color,
    fallbackFontSize: fontSize || customFontSize || !computedStyles ? undefined : parseInt(computedStyles.fontSize) || undefined
  };
});

var ParagraphBlock =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_7__["default"])(ParagraphBlock, _Component);

  function ParagraphBlock() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_3__["default"])(this, ParagraphBlock);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_5__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__["default"])(ParagraphBlock).apply(this, arguments));
    _this.onReplace = _this.onReplace.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_8__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_8__["default"])(_this)));
    _this.toggleDropCap = _this.toggleDropCap.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_8__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_8__["default"])(_this)));
    _this.splitBlock = _this.splitBlock.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_8__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_8__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_4__["default"])(ParagraphBlock, [{
    key: "onReplace",
    value: function onReplace(blocks) {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          onReplace = _this$props.onReplace;
      onReplace(blocks.map(function (block, index) {
        return index === 0 && block.name === name ? Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__["default"])({}, block, {
          attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__["default"])({}, attributes, block.attributes)
        }) : block;
      }));
    }
  }, {
    key: "toggleDropCap",
    value: function toggleDropCap() {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      setAttributes({
        dropCap: !attributes.dropCap
      });
    }
  }, {
    key: "getDropCapHelp",
    value: function getDropCapHelp(checked) {
      return checked ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Showing large initial letter.') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Toggle to show a large initial letter.');
    }
    /**
     * Split handler for RichText value, namely when content is pasted or the
     * user presses the Enter key.
     *
     * @param {?Array}     before Optional before value, to be used as content
     *                            in place of what exists currently for the
     *                            block. If undefined, the block is deleted.
     * @param {?Array}     after  Optional after value, to be appended in a new
     *                            paragraph block to the set of blocks passed
     *                            as spread.
     * @param {...WPBlock} blocks Optional blocks inserted between the before
     *                            and after value blocks.
     */

  }, {
    key: "splitBlock",
    value: function splitBlock(before, after) {
      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          insertBlocksAfter = _this$props3.insertBlocksAfter,
          setAttributes = _this$props3.setAttributes,
          onReplace = _this$props3.onReplace;

      for (var _len = arguments.length, blocks = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
        blocks[_key - 2] = arguments[_key];
      }

      if (after !== null) {
        // Append "After" content as a new paragraph block to the end of
        // any other blocks being inserted after the current paragraph.
        blocks.push(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_14__["createBlock"])(name, {
          content: after
        }));
      }

      if (blocks.length && insertBlocksAfter) {
        insertBlocksAfter(blocks);
      }

      var content = attributes.content;

      if (before === null) {
        // If before content is omitted, treat as intent to delete block.
        onReplace([]);
      } else if (content !== before) {
        // Only update content if it has in-fact changed. In case that user
        // has created a new paragraph at end of an existing one, the value
        // of before will be strictly equal to the current content.
        setAttributes({
          content: before
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _classnames;

      var _this$props4 = this.props,
          attributes = _this$props4.attributes,
          setAttributes = _this$props4.setAttributes,
          mergeBlocks = _this$props4.mergeBlocks,
          onReplace = _this$props4.onReplace,
          className = _this$props4.className,
          backgroundColor = _this$props4.backgroundColor,
          textColor = _this$props4.textColor,
          setBackgroundColor = _this$props4.setBackgroundColor,
          setTextColor = _this$props4.setTextColor,
          fallbackBackgroundColor = _this$props4.fallbackBackgroundColor,
          fallbackTextColor = _this$props4.fallbackTextColor,
          fallbackFontSize = _this$props4.fallbackFontSize,
          fontSize = _this$props4.fontSize,
          setFontSize = _this$props4.setFontSize;
      var align = attributes.align,
          content = attributes.content,
          dropCap = attributes.dropCap,
          placeholder = attributes.placeholder;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["AlignmentToolbar"], {
        value: align,
        onChange: function onChange(nextAlign) {
          setAttributes({
            align: nextAlign
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Text Settings'),
        className: "blocks-font-size"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["FontSizePicker"], {
        fallbackFontSize: fallbackFontSize,
        value: fontSize.size,
        onChange: setFontSize
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_12__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Drop Cap'),
        checked: !!dropCap,
        onChange: this.toggleDropCap,
        help: this.getDropCapHelp
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["PanelColorSettings"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Color Settings'),
        initialOpen: false,
        colorSettings: [{
          value: backgroundColor.color,
          onChange: setBackgroundColor,
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Background Color')
        }, {
          value: textColor.color,
          onChange: setTextColor,
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Text Color')
        }]
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["ContrastChecker"], Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__["default"])({
        textColor: textColor.color,
        backgroundColor: backgroundColor.color,
        fallbackTextColor: fallbackTextColor,
        fallbackBackgroundColor: fallbackBackgroundColor
      }, {
        fontSize: fontSize.size
      })))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["RichText"], {
        tagName: "p",
        className: classnames__WEBPACK_IMPORTED_MODULE_10___default()('wp-block-paragraph', className, (_classnames = {
          'has-text-color': textColor.color,
          'has-background': backgroundColor.color,
          'has-drop-cap': dropCap
        }, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, backgroundColor.class, backgroundColor.class), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, textColor.class, textColor.class), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, fontSize.class, fontSize.class), _classnames)),
        style: {
          backgroundColor: backgroundColor.color,
          color: textColor.color,
          fontSize: fontSize.size ? fontSize.size + 'px' : undefined,
          textAlign: align
        },
        value: content,
        onChange: function onChange(nextContent) {
          setAttributes({
            content: nextContent
          });
        },
        onSplit: this.splitBlock,
        onMerge: mergeBlocks,
        onReplace: this.onReplace,
        onRemove: function onRemove() {
          return onReplace([]);
        },
        placeholder: placeholder || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Add text or type / to add content')
      }));
    }
  }]);

  return ParagraphBlock;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Component"]);

var ParagraphEdit = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_15__["compose"])([Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["withColors"])('backgroundColor', {
  textColor: 'color'
}), Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__["withFontSizes"])('fontSize'), applyFallbackStyles])(ParagraphBlock);
/* harmony default export */ __webpack_exports__["default"] = (ParagraphEdit);


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/paragraph/index.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/paragraph/index.js ***!
  \*******************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/paragraph/edit.js");




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


var supports = {
  className: false
};
var schema = {
  content: {
    source: 'html',
    selector: 'p',
    default: ''
  },
  align: {
    type: 'string'
  },
  dropCap: {
    type: 'boolean',
    default: false
  },
  placeholder: {
    type: 'string'
  },
  textColor: {
    type: 'string'
  },
  customTextColor: {
    type: 'string'
  },
  backgroundColor: {
    type: 'string'
  },
  customBackgroundColor: {
    type: 'string'
  },
  fontSize: {
    type: 'string'
  },
  customFontSize: {
    type: 'number'
  }
};
var name = 'core/paragraph';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Paragraph'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Add some basic text.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    d: "M11 5v7H9.5C7.6 12 6 10.4 6 8.5S7.6 5 9.5 5H11m8-2H9.5C6.5 3 4 5.5 4 8.5S6.5 14 9.5 14H11v7h2V5h2v16h2V5h2V3z"
  })),
  category: 'common',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('text')],
  supports: supports,
  attributes: schema,
  transforms: {
    from: [{
      type: 'raw',
      // Paragraph is a fallback and should be matched last.
      priority: 20,
      selector: 'p',
      schema: {
        p: {
          children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["getPhrasingContentSchema"])()
        }
      }
    }]
  },
  deprecated: [{
    supports: supports,
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, schema, {
      width: {
        type: 'string'
      }
    }),
    save: function save(_ref) {
      var _classnames;

      var attributes = _ref.attributes;
      var width = attributes.width,
          align = attributes.align,
          content = attributes.content,
          dropCap = attributes.dropCap,
          backgroundColor = attributes.backgroundColor,
          textColor = attributes.textColor,
          customBackgroundColor = attributes.customBackgroundColor,
          customTextColor = attributes.customTextColor,
          fontSize = attributes.fontSize,
          customFontSize = attributes.customFontSize;
      var textClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getColorClassName"])('color', textColor);
      var backgroundClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getColorClassName"])('background-color', backgroundColor);
      var fontSizeClass = fontSize && "is-".concat(fontSize, "-text");
      var className = classnames__WEBPACK_IMPORTED_MODULE_3___default()((_classnames = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, "align".concat(width), width), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, 'has-background', backgroundColor || customBackgroundColor), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, 'has-drop-cap', dropCap), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, fontSizeClass, fontSizeClass), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, textClass, textClass), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames, backgroundClass, backgroundClass), _classnames));
      var styles = {
        backgroundColor: backgroundClass ? undefined : customBackgroundColor,
        color: textClass ? undefined : customTextColor,
        fontSize: fontSizeClass ? undefined : customFontSize,
        textAlign: align
      };
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: "p",
        style: styles,
        className: className ? className : undefined,
        value: content
      });
    }
  }, {
    supports: supports,
    attributes: Object(lodash__WEBPACK_IMPORTED_MODULE_4__["omit"])(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, schema, {
      fontSize: {
        type: 'number'
      }
    }), 'customFontSize', 'customTextColor', 'customBackgroundColor'),
    save: function save(_ref2) {
      var _classnames2;

      var attributes = _ref2.attributes;
      var width = attributes.width,
          align = attributes.align,
          content = attributes.content,
          dropCap = attributes.dropCap,
          backgroundColor = attributes.backgroundColor,
          textColor = attributes.textColor,
          fontSize = attributes.fontSize;
      var className = classnames__WEBPACK_IMPORTED_MODULE_3___default()((_classnames2 = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames2, "align".concat(width), width), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames2, 'has-background', backgroundColor), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames2, 'has-drop-cap', dropCap), _classnames2));
      var styles = {
        backgroundColor: backgroundColor,
        color: textColor,
        fontSize: fontSize,
        textAlign: align
      };
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("p", {
        style: styles,
        className: className ? className : undefined
      }, content);
    },
    migrate: function migrate(attributes) {
      return Object(lodash__WEBPACK_IMPORTED_MODULE_4__["omit"])(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, attributes, {
        customFontSize: Object(lodash__WEBPACK_IMPORTED_MODULE_4__["isFinite"])(attributes.fontSize) ? attributes.fontSize : undefined,
        customTextColor: attributes.textColor && '#' === attributes.textColor[0] ? attributes.textColor : undefined,
        customBackgroundColor: attributes.backgroundColor && '#' === attributes.backgroundColor[0] ? attributes.backgroundColor : undefined
      }), ['fontSize', 'textColor', 'backgroundColor']);
    }
  }, {
    supports: supports,
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, schema, {
      content: {
        type: 'string',
        source: 'html'
      }
    }),
    save: function save(_ref3) {
      var attributes = _ref3.attributes;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["RawHTML"], null, attributes.content);
    },
    migrate: function migrate(attributes) {
      return attributes;
    }
  }],
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: attributes.content + attributesToMerge.content
    };
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var width = attributes.width;

    if (['wide', 'full', 'left', 'right'].indexOf(width) !== -1) {
      return {
        'data-align': width
      };
    }
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_8__["default"],
  save: function save(_ref4) {
    var _classnames3;

    var attributes = _ref4.attributes;
    var align = attributes.align,
        content = attributes.content,
        dropCap = attributes.dropCap,
        backgroundColor = attributes.backgroundColor,
        textColor = attributes.textColor,
        customBackgroundColor = attributes.customBackgroundColor,
        customTextColor = attributes.customTextColor,
        fontSize = attributes.fontSize,
        customFontSize = attributes.customFontSize;
    var textClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getColorClassName"])('color', textColor);
    var backgroundClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getColorClassName"])('background-color', backgroundColor);
    var fontSizeClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getFontSizeClass"])(fontSize);
    var className = classnames__WEBPACK_IMPORTED_MODULE_3___default()((_classnames3 = {
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-drop-cap': dropCap
    }, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames3, fontSizeClass, fontSizeClass), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames3, textClass, textClass), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_classnames3, backgroundClass, backgroundClass), _classnames3));
    var styles = {
      backgroundColor: backgroundClass ? undefined : customBackgroundColor,
      color: textClass ? undefined : customTextColor,
      fontSize: fontSizeClass ? undefined : customFontSize,
      textAlign: align
    };
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
      tagName: "p",
      style: styles,
      className: className ? className : undefined,
      value: content
    });
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/preformatted/index.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/preformatted/index.js ***!
  \**********************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);


/**
 * WordPress
 */



var name = 'core/preformatted';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Preformatted'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Add text that respects your spacing and tabs, and also allows styling.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M0,0h24v24H0V0z",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M20,4H4C2.9,4,2,4.9,2,6v12c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2V6C22,4.9,21.1,4,20,4z M20,18H4V6h16V18z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "6",
    y: "10",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "6",
    y: "14",
    width: "8",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "16",
    y: "14",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "10",
    y: "10",
    width: "8",
    height: "2"
  })),
  category: 'formatting',
  attributes: {
    content: {
      source: 'html',
      selector: 'pre'
    }
  },
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/code', 'core/paragraph'],
      transform: function transform(_ref) {
        var content = _ref.content;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/preformatted', {
          content: content
        });
      }
    }, {
      type: 'raw',
      isMatch: function isMatch(node) {
        return node.nodeName === 'PRE' && !(node.children.length === 1 && node.firstChild.nodeName === 'CODE');
      },
      schema: {
        pre: {
          children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["getPhrasingContentSchema"])()
        }
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/paragraph', attributes);
      }
    }]
  },
  edit: function edit(_ref2) {
    var attributes = _ref2.attributes,
        mergeBlocks = _ref2.mergeBlocks,
        setAttributes = _ref2.setAttributes,
        className = _ref2.className;
    var content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"], {
      tagName: "pre",
      value: content,
      onChange: function onChange(nextContent) {
        setAttributes({
          content: nextContent
        });
      },
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Write preformatted text…'),
      wrapperClassName: className,
      onMerge: mergeBlocks
    });
  },
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    var content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"].Content, {
      tagName: "pre",
      value: content
    });
  },
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: attributes.content + attributesToMerge.content
    };
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/pullquote/edit.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/pullquote/edit.js ***!
  \******************************************************************************/
/*! exports provided: SOLID_COLOR_STYLE_NAME, SOLID_COLOR_CLASS, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SOLID_COLOR_STYLE_NAME", function() { return SOLID_COLOR_STYLE_NAME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SOLID_COLOR_CLASS", function() { return SOLID_COLOR_CLASS; });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__);










/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




var SOLID_COLOR_STYLE_NAME = 'solid-color';
var SOLID_COLOR_CLASS = "is-style-".concat(SOLID_COLOR_STYLE_NAME);

var PullQuoteEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__["default"])(PullQuoteEdit, _Component);

  function PullQuoteEdit(props) {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__["default"])(this, PullQuoteEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__["default"])(PullQuoteEdit).call(this, props));
    _this.wasTextColorAutomaticallyComputed = false;
    _this.pullQuoteMainColorSetter = _this.pullQuoteMainColorSetter.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.pullQuoteTextColorSetter = _this.pullQuoteTextColorSetter.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__["default"])(PullQuoteEdit, [{
    key: "pullQuoteMainColorSetter",
    value: function pullQuoteMainColorSetter(colorValue) {
      var _this$props = this.props,
          colorUtils = _this$props.colorUtils,
          textColor = _this$props.textColor,
          setTextColor = _this$props.setTextColor,
          setMainColor = _this$props.setMainColor;
      setMainColor(colorValue);

      if (!textColor.color || this.wasTextColorAutomaticallyComputed) {
        this.wasTextColorAutomaticallyComputed = true;
        setTextColor(colorUtils.getMostReadableColor(colorValue));
      }
    }
  }, {
    key: "pullQuoteTextColorSetter",
    value: function pullQuoteTextColorSetter(colorValue) {
      var setTextColor = this.props.setTextColor;
      setTextColor(colorValue);
      this.wasTextColorAutomaticallyComputed = false;
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          mainColor = _this$props2.mainColor,
          textColor = _this$props2.textColor,
          setAttributes = _this$props2.setAttributes,
          isSelected = _this$props2.isSelected,
          className = _this$props2.className;
      var value = attributes.value,
          citation = attributes.citation;
      var isSolidColorStyle = Object(lodash__WEBPACK_IMPORTED_MODULE_10__["includes"])(className, SOLID_COLOR_CLASS);
      var figureStyle = isSolidColorStyle ? {
        backgroundColor: mainColor.color
      } : {
        borderColor: mainColor.color
      };
      var blockquoteStyle = {
        color: textColor.color
      };
      var blockquoteClasses = textColor.color ? classnames__WEBPACK_IMPORTED_MODULE_9___default()('has-text-color', Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, textColor.class, textColor.class)) : undefined;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("figure", {
        style: figureStyle,
        className: classnames__WEBPACK_IMPORTED_MODULE_9___default()(className, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, mainColor.class, isSolidColorStyle && mainColor.class))
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("blockquote", {
        style: blockquoteStyle,
        className: blockquoteClasses
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["RichText"], {
        multiline: "p",
        value: value,
        onChange: function onChange(nextValue) {
          return setAttributes({
            value: nextValue
          });
        }
        /* translators: the text of the quotation */
        ,
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Write quote…'),
        wrapperClassName: "block-library-pullquote__content"
      }), (!_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["RichText"].isEmpty(citation) || isSelected) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["RichText"], {
        value: citation
        /* translators: the individual or entity quoted */
        ,
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Write citation…'),
        onChange: function onChange(nextCitation) {
          return setAttributes({
            citation: nextCitation
          });
        },
        className: "wp-block-pullquote__citation"
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["PanelColorSettings"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Color Settings'),
        colorSettings: [{
          value: mainColor.color,
          onChange: this.pullQuoteMainColorSetter,
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Main Color')
        }, {
          value: textColor.color,
          onChange: this.pullQuoteTextColorSetter,
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__["__"])('Text Color')
        }]
      }, isSolidColorStyle && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["ContrastChecker"], Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
        textColor: textColor.color,
        backgroundColor: mainColor.color
      }, {
        isLargeText: false
      })))));
    }
  }]);

  return PullQuoteEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_12__["withColors"])({
  mainColor: 'background-color',
  textColor: 'color'
})(PullQuoteEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/pullquote/index.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/pullquote/index.js ***!
  \*******************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/pullquote/edit.js");




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */





var blockAttributes = {
  value: {
    source: 'html',
    selector: 'blockquote',
    multiline: 'p'
  },
  citation: {
    source: 'html',
    selector: 'cite'
  },
  mainColor: {
    type: 'string'
  },
  customMainColor: {
    type: 'string'
  },
  textColor: {
    type: 'string'
  },
  customTextColor: {
    type: 'string'
  }
};
var name = 'core/pullquote';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Pullquote'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Highlight a quote from your post or page by displaying it as a graphic element.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    d: "M0,0h24v24H0V0z",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("polygon", {
    points: "21 18 2 18 2 20 21 20"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    d: "m19 10v4h-15v-4h15m1-2h-17c-0.55 0-1 0.45-1 1v6c0 0.55 0.45 1 1 1h17c0.55 0 1-0.45 1-1v-6c0-0.55-0.45-1-1-1z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("polygon", {
    points: "21 4 2 4 2 6 21 6"
  })),
  category: 'formatting',
  attributes: blockAttributes,
  styles: [{
    name: 'default',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Regular'),
    isDefault: true
  }, {
    name: _edit__WEBPACK_IMPORTED_MODULE_8__["SOLID_COLOR_STYLE_NAME"],
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Solid Color')
  }],
  supports: {
    align: ['left', 'right', 'wide', 'full']
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_8__["default"],
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var mainColor = attributes.mainColor,
        customMainColor = attributes.customMainColor,
        textColor = attributes.textColor,
        customTextColor = attributes.customTextColor,
        value = attributes.value,
        citation = attributes.citation,
        className = attributes.className;
    var isSolidColorStyle = Object(lodash__WEBPACK_IMPORTED_MODULE_4__["includes"])(className, _edit__WEBPACK_IMPORTED_MODULE_8__["SOLID_COLOR_CLASS"]);
    var figureClass, figureStyles; // Is solid color style

    if (isSolidColorStyle) {
      figureClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getColorClassName"])('background-color', mainColor);

      if (!figureClass) {
        figureStyles = {
          backgroundColor: customMainColor
        };
      } // Is normal style and a custom color is being used ( we can set a style directly with its value)

    } else if (customMainColor) {
      figureStyles = {
        borderColor: customMainColor
      }; // Is normal style and a named color is being used, we need to retrieve the color value to set the style,
      // as there is no expectation that themes create classes that set border colors.
    } else if (mainColor) {
      var colors = Object(lodash__WEBPACK_IMPORTED_MODULE_4__["get"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_7__["select"])('core/editor').getEditorSettings(), ['colors'], []);
      var colorObject = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getColorObjectByAttributeValues"])(colors, mainColor);
      figureStyles = {
        borderColor: colorObject.color
      };
    }

    var blockquoteTextColorClass = Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["getColorClassName"])('color', textColor);
    var blockquoteClasses = textColor || customTextColor ? classnames__WEBPACK_IMPORTED_MODULE_3___default()('has-text-color', Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, blockquoteTextColorClass, blockquoteTextColorClass)) : undefined;
    var blockquoteStyle = blockquoteTextColorClass ? undefined : {
      color: customTextColor
    };
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("figure", {
      className: figureClass,
      style: figureStyles
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("blockquote", {
      className: blockquoteClasses,
      style: blockquoteStyle
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
      value: value,
      multiline: "p"
    }), !_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].isEmpty(citation) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
      tagName: "cite",
      value: citation
    })));
  },
  deprecated: [{
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, blockAttributes),
    save: function save(_ref2) {
      var attributes = _ref2.attributes;
      var value = attributes.value,
          citation = attributes.citation;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("blockquote", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        value: value,
        multiline: "p"
      }), !_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].isEmpty(citation) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: "cite",
        value: citation
      }));
    }
  }, {
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, blockAttributes, {
      citation: {
        source: 'html',
        selector: 'footer'
      },
      align: {
        type: 'string',
        default: 'none'
      }
    }),
    save: function save(_ref3) {
      var attributes = _ref3.attributes;
      var value = attributes.value,
          citation = attributes.citation,
          align = attributes.align;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("blockquote", {
        className: "align".concat(align)
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        value: value
      }), !_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].isEmpty(citation) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: "footer",
        value: citation
      }));
    }
  }]
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/quote/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/quote/index.js ***!
  \***************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__);




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






var blockAttributes = {
  value: {
    source: 'html',
    selector: 'blockquote',
    multiline: 'p'
  },
  citation: {
    source: 'html',
    selector: 'cite'
  },
  align: {
    type: 'string'
  }
};
var name = 'core/quote';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Quote'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Maybe someone else said it better -- add some quoted text.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("path", {
    d: "M19 18h-6l2-4h-2V6h8v7l-2 5zm-2-2l2-3V8h-4v4h4l-2 4zm-8 2H3l2-4H3V6h8v7l-2 5zm-2-2l2-3V8H5v4h4l-2 4z"
  }))),
  category: 'common',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('blockquote')],
  attributes: blockAttributes,
  styles: [{
    name: 'default',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Regular'),
    isDefault: true
  }, {
    name: 'large',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Large')
  }],
  transforms: {
    from: [{
      type: 'block',
      isMultiBlock: true,
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/quote', {
          value: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["toHTMLString"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["join"])(attributes.map(function (_ref) {
            var content = _ref.content;
            return Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["create"])({
              html: content
            });
          }), "\u2028"), 'p')
        });
      }
    }, {
      type: 'block',
      blocks: ['core/heading'],
      transform: function transform(_ref2) {
        var content = _ref2.content;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/quote', {
          value: content
        });
      }
    }, {
      type: 'pattern',
      regExp: /^>\s/,
      transform: function transform(_ref3) {
        var content = _ref3.content;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/quote', {
          value: content
        });
      }
    }, {
      type: 'raw',
      selector: 'blockquote',
      schema: {
        blockquote: {
          children: {
            p: {
              children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["getPhrasingContentSchema"])()
            }
          }
        }
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(_ref4) {
        var value = _ref4.value;
        return Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["split"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["create"])({
          html: value,
          multilineTag: 'p'
        }), "\u2028").map(function (piece) {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/paragraph', {
            content: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["toHTMLString"])(piece)
          });
        });
      }
    }, {
      type: 'block',
      blocks: ['core/heading'],
      transform: function transform(_ref5) {
        var value = _ref5.value,
            citation = _ref5.citation,
            attrs = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref5, ["value", "citation"]);

        // If there is no quote content, use the citation as the
        // content of the resulting heading. A nonexistent citation
        // will result in an empty heading.
        if (value === '<p></p>') {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/heading', {
            content: citation
          });
        }

        var pieces = Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["split"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["create"])({
          html: value,
          multilineTag: 'p'
        }), "\u2028");
        var quotePieces = pieces.slice(1);
        return [Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/heading', {
          content: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["toHTMLString"])(pieces[0])
        }), Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["createBlock"])('core/quote', Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, attrs, {
          citation: citation,
          value: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["toHTMLString"])(quotePieces.length ? Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["join"])(pieces.slice(1), "\u2028") : Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__["create"])(), 'p')
        }))];
      }
    }]
  },
  edit: function edit(_ref6) {
    var attributes = _ref6.attributes,
        setAttributes = _ref6.setAttributes,
        isSelected = _ref6.isSelected,
        mergeBlocks = _ref6.mergeBlocks,
        onReplace = _ref6.onReplace,
        className = _ref6.className;
    var align = attributes.align,
        value = attributes.value,
        citation = attributes.citation;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["AlignmentToolbar"], {
      value: align,
      onChange: function onChange(nextAlign) {
        setAttributes({
          align: nextAlign
        });
      }
    })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("blockquote", {
      className: className,
      style: {
        textAlign: align
      }
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"], {
      multiline: "p",
      value: value,
      onChange: function onChange(nextValue) {
        return setAttributes({
          value: nextValue
        });
      },
      onMerge: mergeBlocks,
      onRemove: function onRemove(forward) {
        var hasEmptyCitation = !citation || citation.length === 0;

        if (!forward && hasEmptyCitation) {
          onReplace([]);
        }
      }
      /* translators: the text of the quotation */
      ,
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Write quote…')
    }), (!_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].isEmpty(citation) || isSelected) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"], {
      value: citation,
      onChange: function onChange(nextCitation) {
        return setAttributes({
          citation: nextCitation
        });
      }
      /* translators: the individual or entity quoted */
      ,
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Write citation…'),
      className: "wp-block-quote__citation"
    })));
  },
  save: function save(_ref7) {
    var attributes = _ref7.attributes;
    var align = attributes.align,
        value = attributes.value,
        citation = attributes.citation;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("blockquote", {
      style: {
        textAlign: align ? align : null
      }
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
      multiline: "p",
      value: value
    }), !_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].isEmpty(citation) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
      tagName: "cite",
      value: citation
    }));
  },
  merge: function merge(attributes, attributesToMerge) {
    return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, attributes, {
      value: attributes.value + attributesToMerge.value,
      citation: attributes.citation + attributesToMerge.citation
    });
  },
  deprecated: [{
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, blockAttributes, {
      style: {
        type: 'number',
        default: 1
      }
    }),
    migrate: function migrate(attributes) {
      if (attributes.style === 2) {
        return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, Object(lodash__WEBPACK_IMPORTED_MODULE_3__["omit"])(attributes, ['style']), {
          className: attributes.className ? attributes.className + ' is-style-large' : 'is-style-large'
        });
      }

      return attributes;
    },
    save: function save(_ref8) {
      var attributes = _ref8.attributes;
      var align = attributes.align,
          value = attributes.value,
          citation = attributes.citation,
          style = attributes.style;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("blockquote", {
        className: style === 2 ? 'is-large' : '',
        style: {
          textAlign: align ? align : null
        }
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        multiline: "p",
        value: value
      }), !_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].isEmpty(citation) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: "cite",
        value: citation
      }));
    }
  }, {
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, blockAttributes, {
      citation: {
        source: 'html',
        selector: 'footer'
      },
      style: {
        type: 'number',
        default: 1
      }
    }),
    save: function save(_ref9) {
      var attributes = _ref9.attributes;
      var align = attributes.align,
          value = attributes.value,
          citation = attributes.citation,
          style = attributes.style;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("blockquote", {
        className: "blocks-quote-style-".concat(style),
        style: {
          textAlign: align ? align : null
        }
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        multiline: "p",
        value: value
      }), !_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].isEmpty(citation) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: "footer",
        value: citation
      }));
    }
  }]
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/separator/index.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/separator/index.js ***!
  \*******************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */


var name = 'core/separator';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Separator'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Insert a horizontal line where you want to create a break between ideas.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M19 13H5v-2h14v2z"
  })),
  category: 'layout',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('horizontal-line'), 'hr', Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('divider')],
  styles: [{
    name: 'default',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Short Line'),
    isDefault: true
  }, {
    name: 'wide',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Wide Line')
  }, {
    name: 'dots',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Dots')
  }],
  transforms: {
    from: [{
      type: 'pattern',
      trigger: 'enter',
      regExp: /^-{3,}$/,
      transform: function transform() {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/separator');
      }
    }, {
      type: 'raw',
      selector: 'hr',
      schema: {
        hr: {}
      }
    }]
  },
  edit: function edit(_ref) {
    var className = _ref.className;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("hr", {
      className: className
    });
  },
  save: function save() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("hr", null);
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/shortcode/index.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/shortcode/index.js ***!
  \*******************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_autop__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/autop */ "@wordpress/autop");
/* harmony import */ var _wordpress_autop__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_autop__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__);


/**
 * WordPress dependencies
 */






var name = 'core/shortcode';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shortcode'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add a shortcode -- a WordPress-specific snippet of code written between square brackets.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M8.5,21.4l1.9,0.5l5.2-19.3l-1.9-0.5L8.5,21.4z M3,19h4v-2H5V7h2V5H3V19z M17,5v2h2v10h-2v2h4V5H17z"
  })),
  category: 'widgets',
  attributes: {
    text: {
      type: 'string',
      source: 'text'
    }
  },
  transforms: {
    from: [{
      type: 'shortcode',
      // Per "Shortcode names should be all lowercase and use all
      // letters, but numbers and underscores should work fine too.
      // Be wary of using hyphens (dashes), you'll be better off not
      // using them." in https://codex.wordpress.org/Shortcode_API
      // Require that the first character be a letter. This notably
      // prevents footnote markings ([1]) from being caught as
      // shortcodes.
      tag: '[a-z][a-z0-9_-]*',
      attributes: {
        text: {
          type: 'string',
          shortcode: function shortcode(attrs, _ref) {
            var content = _ref.content;
            return Object(_wordpress_autop__WEBPACK_IMPORTED_MODULE_1__["removep"])(Object(_wordpress_autop__WEBPACK_IMPORTED_MODULE_1__["autop"])(content));
          }
        }
      },
      priority: 20
    }]
  },
  supports: {
    customClassName: false,
    className: false,
    html: false
  },
  edit: Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__["withInstanceId"])(function (_ref2) {
    var attributes = _ref2.attributes,
        setAttributes = _ref2.setAttributes,
        instanceId = _ref2.instanceId;
    var inputId = "blocks-shortcode-input-".concat(instanceId);
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "wp-block-shortcode"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("label", {
      htmlFor: inputId
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Dashicon"], {
      icon: "shortcode"
    }), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shortcode')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["PlainText"], {
      className: "input-control",
      id: inputId,
      value: attributes.text,
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Write shortcode here…'),
      onChange: function onChange(text) {
        return setAttributes({
          text: text
        });
      }
    }));
  }),
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["RawHTML"], null, attributes.text);
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/spacer/index.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/spacer/index.js ***!
  \****************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__);


/**
 * External dependencies
 */

/**
 * WordPress
 */






var name = 'core/spacer';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Spacer'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add an element with empty space and custom height.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M13 4v2h3.59L6 16.59V13H4v7h7v-2H7.41L18 7.41V11h2V4h-7"
  }))),
  category: 'layout',
  attributes: {
    height: {
      type: 'number',
      default: 100
    }
  },
  edit: Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__["withInstanceId"])(function (_ref) {
    var attributes = _ref.attributes,
        isSelected = _ref.isSelected,
        setAttributes = _ref.setAttributes,
        toggleSelection = _ref.toggleSelection,
        instanceId = _ref.instanceId;
    var height = attributes.height;
    var id = "block-spacer-height-input-".concat(instanceId);
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ResizableBox"], {
      className: classnames__WEBPACK_IMPORTED_MODULE_1___default()('block-library-spacer__resize-container', {
        'is-selected': isSelected
      }),
      size: {
        height: height
      },
      minHeight: "20",
      enable: {
        top: false,
        right: false,
        bottom: true,
        left: false,
        topRight: false,
        bottomRight: false,
        bottomLeft: false,
        topLeft: false
      },
      onResizeStop: function onResizeStop(event, direction, elt, delta) {
        setAttributes({
          height: parseInt(height + delta.height, 10)
        });
        toggleSelection(true);
      },
      onResizeStart: function onResizeStart() {
        toggleSelection(false);
      }
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
      title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Spacer Settings')
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["BaseControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Height in pixels'),
      id: id
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("input", {
      type: "number",
      id: id,
      onChange: function onChange(event) {
        setAttributes({
          height: parseInt(event.target.value, 10)
        });
      },
      value: height,
      min: "20",
      step: "10"
    })))));
  }),
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      style: {
        height: attributes.height
      },
      "aria-hidden": true
    });
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/subhead/index.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/subhead/index.js ***!
  \*****************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/deprecated */ "@wordpress/deprecated");
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__);


/**
 * WordPress dependencies
 */





var name = 'core/subhead';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Subheading (deprecated)'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('This block is deprecated. Please use the Paragraph block instead.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M7.1 6l-.5 3h4.5L9.4 19h3l1.8-10h4.5l.5-3H7.1z"
  })),
  category: 'common',
  supports: {
    // Hide from inserter as this block is deprecated.
    inserter: false,
    multiple: false
  },
  attributes: {
    content: {
      source: 'html',
      selector: 'p'
    },
    align: {
      type: 'string'
    }
  },
  transforms: {
    to: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/paragraph', attributes);
      }
    }]
  },
  edit: function edit(_ref) {
    var attributes = _ref.attributes,
        setAttributes = _ref.setAttributes,
        className = _ref.className;
    var align = attributes.align,
        content = attributes.content,
        placeholder = attributes.placeholder;
    _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default()('The Subheading block', {
      alternative: 'the Paragraph block',
      plugin: 'Gutenberg'
    });
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["AlignmentToolbar"], {
      value: align,
      onChange: function onChange(nextAlign) {
        setAttributes({
          align: nextAlign
        });
      }
    })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
      tagName: "p",
      value: content,
      onChange: function onChange(nextContent) {
        setAttributes({
          content: nextContent
        });
      },
      style: {
        textAlign: align
      },
      className: className,
      placeholder: placeholder || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Write subheading…')
    }));
  },
  save: function save(_ref2) {
    var attributes = _ref2.attributes,
        className = _ref2.className;
    var align = attributes.align,
        content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"].Content, {
      tagName: "p",
      className: className,
      style: {
        textAlign: align
      },
      value: content
    });
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/table/edit.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/table/edit.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TableEdit; });
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _state__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./state */ "./node_modules/@wordpress/block-library/build-module/table/state.js");








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



var TableEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(TableEdit, _Component);

  function TableEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, TableEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(TableEdit).apply(this, arguments));
    _this.onCreateTable = _this.onCreateTable.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onChangeFixedLayout = _this.onChangeFixedLayout.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onChange = _this.onChange.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onChangeInitialColumnCount = _this.onChangeInitialColumnCount.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onChangeInitialRowCount = _this.onChangeInitialRowCount.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.renderSection = _this.renderSection.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.getTableControls = _this.getTableControls.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onInsertRow = _this.onInsertRow.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onInsertRowBefore = _this.onInsertRowBefore.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onInsertRowAfter = _this.onInsertRowAfter.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onDeleteRow = _this.onDeleteRow.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onInsertColumn = _this.onInsertColumn.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onInsertColumnBefore = _this.onInsertColumnBefore.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onInsertColumnAfter = _this.onInsertColumnAfter.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.onDeleteColumn = _this.onDeleteColumn.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    _this.state = {
      initialRowCount: 2,
      initialColumnCount: 2,
      selectedCell: null
    };
    return _this;
  }
  /**
   * Updates the initial column count used for table creation.
   *
   * @param {number} initialColumnCount New initial column count.
   */


  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(TableEdit, [{
    key: "onChangeInitialColumnCount",
    value: function onChangeInitialColumnCount(initialColumnCount) {
      this.setState({
        initialColumnCount: initialColumnCount
      });
    }
    /**
     * Updates the initial row count used for table creation.
     *
     * @param {number} initialRowCount New initial row count.
     */

  }, {
    key: "onChangeInitialRowCount",
    value: function onChangeInitialRowCount(initialRowCount) {
      this.setState({
        initialRowCount: initialRowCount
      });
    }
    /**
     * Creates a table based on dimensions in local state.
     */

  }, {
    key: "onCreateTable",
    value: function onCreateTable() {
      var setAttributes = this.props.setAttributes;
      var _this$state = this.state,
          initialRowCount = _this$state.initialRowCount,
          initialColumnCount = _this$state.initialColumnCount;
      initialRowCount = parseInt(initialRowCount, 10) || 2;
      initialColumnCount = parseInt(initialColumnCount, 10) || 2;
      setAttributes(Object(_state__WEBPACK_IMPORTED_MODULE_11__["createTable"])({
        rowCount: initialRowCount,
        columnCount: initialColumnCount
      }));
    }
    /**
     * Toggles whether the table has a fixed layout or not.
     */

  }, {
    key: "onChangeFixedLayout",
    value: function onChangeFixedLayout() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      var hasFixedLayout = attributes.hasFixedLayout;
      setAttributes({
        hasFixedLayout: !hasFixedLayout
      });
    }
    /**
     * Changes the content of the currently selected cell.
     *
     * @param {Array} content A RichText content value.
     */

  }, {
    key: "onChange",
    value: function onChange(content) {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      var section = selectedCell.section,
          rowIndex = selectedCell.rowIndex,
          columnIndex = selectedCell.columnIndex;
      setAttributes(Object(_state__WEBPACK_IMPORTED_MODULE_11__["updateCellContent"])(attributes, {
        section: section,
        rowIndex: rowIndex,
        columnIndex: columnIndex,
        content: content
      }));
    }
    /**
     * Inserts a row at the currently selected row index, plus `delta`.
     *
     * @param {number} delta Offset for selected row index at which to insert.
     */

  }, {
    key: "onInsertRow",
    value: function onInsertRow(delta) {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          setAttributes = _this$props3.setAttributes;
      var section = selectedCell.section,
          rowIndex = selectedCell.rowIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(Object(_state__WEBPACK_IMPORTED_MODULE_11__["insertRow"])(attributes, {
        section: section,
        rowIndex: rowIndex + delta
      }));
    }
    /**
     * Inserts a row before the currently selected row.
     */

  }, {
    key: "onInsertRowBefore",
    value: function onInsertRowBefore() {
      this.onInsertRow(0);
    }
    /**
     * Inserts a row after the currently selected row.
     */

  }, {
    key: "onInsertRowAfter",
    value: function onInsertRowAfter() {
      this.onInsertRow(1);
    }
    /**
     * Deletes the currently selected row.
     */

  }, {
    key: "onDeleteRow",
    value: function onDeleteRow() {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props4 = this.props,
          attributes = _this$props4.attributes,
          setAttributes = _this$props4.setAttributes;
      var section = selectedCell.section,
          rowIndex = selectedCell.rowIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(Object(_state__WEBPACK_IMPORTED_MODULE_11__["deleteRow"])(attributes, {
        section: section,
        rowIndex: rowIndex
      }));
    }
    /**
     * Inserts a column at the currently selected column index, plus `delta`.
     *
     * @param {number} delta Offset for selected column index at which to insert.
     */

  }, {
    key: "onInsertColumn",
    value: function onInsertColumn() {
      var delta = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props5 = this.props,
          attributes = _this$props5.attributes,
          setAttributes = _this$props5.setAttributes;
      var section = selectedCell.section,
          columnIndex = selectedCell.columnIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(Object(_state__WEBPACK_IMPORTED_MODULE_11__["insertColumn"])(attributes, {
        section: section,
        columnIndex: columnIndex + delta
      }));
    }
    /**
     * Inserts a column before the currently selected column.
     */

  }, {
    key: "onInsertColumnBefore",
    value: function onInsertColumnBefore() {
      this.onInsertColumn(0);
    }
    /**
     * Inserts a column after the currently selected column.
     */

  }, {
    key: "onInsertColumnAfter",
    value: function onInsertColumnAfter() {
      this.onInsertColumn(1);
    }
    /**
     * Deletes the currently selected column.
     */

  }, {
    key: "onDeleteColumn",
    value: function onDeleteColumn() {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props6 = this.props,
          attributes = _this$props6.attributes,
          setAttributes = _this$props6.setAttributes;
      var section = selectedCell.section,
          columnIndex = selectedCell.columnIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(Object(_state__WEBPACK_IMPORTED_MODULE_11__["deleteColumn"])(attributes, {
        section: section,
        columnIndex: columnIndex
      }));
    }
    /**
     * Creates an onFocus handler for a specified cell.
     *
     * @param {Object} selectedCell Object with `section`, `rowIndex`, and
     *                              `columnIndex` properties.
     *
     * @return {Function} Function to call on focus.
     */

  }, {
    key: "createOnFocus",
    value: function createOnFocus(selectedCell) {
      var _this2 = this;

      return function () {
        _this2.setState({
          selectedCell: selectedCell
        });
      };
    }
    /**
     * Gets the table controls to display in the block toolbar.
     *
     * @return {Array} Table controls.
     */

  }, {
    key: "getTableControls",
    value: function getTableControls() {
      var selectedCell = this.state.selectedCell;
      return [{
        icon: 'table-row-before',
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Add Row Before'),
        isDisabled: !selectedCell,
        onClick: this.onInsertRowBefore
      }, {
        icon: 'table-row-after',
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Add Row After'),
        isDisabled: !selectedCell,
        onClick: this.onInsertRowAfter
      }, {
        icon: 'table-row-delete',
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Delete Row'),
        isDisabled: !selectedCell,
        onClick: this.onDeleteRow
      }, {
        icon: 'table-col-before',
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Add Column Before'),
        isDisabled: !selectedCell,
        onClick: this.onInsertColumnBefore
      }, {
        icon: 'table-col-after',
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Add Column After'),
        isDisabled: !selectedCell,
        onClick: this.onInsertColumnAfter
      }, {
        icon: 'table-col-delete',
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Delete Column'),
        isDisabled: !selectedCell,
        onClick: this.onDeleteColumn
      }];
    }
    /**
     * Renders a table section.
     *
     * @param {string} options.type Section type: head, body, or foot.
     * @param {Array}  options.rows The rows to render.
     *
     * @return {Object} React element for the section.
     */

  }, {
    key: "renderSection",
    value: function renderSection(_ref) {
      var _this3 = this;

      var type = _ref.type,
          rows = _ref.rows;

      if (!rows.length) {
        return null;
      }

      var Tag = "t".concat(type);
      var selectedCell = this.state.selectedCell;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(Tag, null, rows.map(function (_ref2, rowIndex) {
        var cells = _ref2.cells;
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("tr", {
          key: rowIndex
        }, cells.map(function (_ref3, columnIndex) {
          var content = _ref3.content,
              CellTag = _ref3.tag;
          var isSelected = selectedCell && type === selectedCell.section && rowIndex === selectedCell.rowIndex && columnIndex === selectedCell.columnIndex;
          var cell = {
            section: type,
            rowIndex: rowIndex,
            columnIndex: columnIndex
          };
          var classes = classnames__WEBPACK_IMPORTED_MODULE_7___default()({
            'is-selected': isSelected
          });
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(CellTag, {
            key: columnIndex,
            className: classes
          }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"], {
            className: "wp-block-table__cell-content",
            value: content,
            onChange: _this3.onChange,
            unstableOnFocus: _this3.createOnFocus(cell)
          }));
        }));
      }));
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate() {
      var isSelected = this.props.isSelected;
      var selectedCell = this.state.selectedCell;

      if (!isSelected && selectedCell) {
        this.setState({
          selectedCell: null
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props7 = this.props,
          attributes = _this$props7.attributes,
          className = _this$props7.className;
      var _this$state2 = this.state,
          initialRowCount = _this$state2.initialRowCount,
          initialColumnCount = _this$state2.initialColumnCount;
      var hasFixedLayout = attributes.hasFixedLayout,
          head = attributes.head,
          body = attributes.body,
          foot = attributes.foot;
      var isEmpty = !head.length && !body.length && !foot.length;
      var Section = this.renderSection;

      if (isEmpty) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("form", {
          onSubmit: this.onCreateTable
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["TextControl"], {
          type: "number",
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Column Count'),
          value: initialColumnCount,
          onChange: this.onChangeInitialColumnCount,
          min: "1"
        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["TextControl"], {
          type: "number",
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Row Count'),
          value: initialRowCount,
          onChange: this.onChangeInitialRowCount,
          min: "1"
        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Button"], {
          isPrimary: true,
          type: "submit"
        }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Create')));
      }

      var classes = classnames__WEBPACK_IMPORTED_MODULE_7___default()(className, {
        'has-fixed-layout': hasFixedLayout
      });
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Toolbar"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["DropdownMenu"], {
        icon: "editor-table",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Edit Table'),
        controls: this.getTableControls()
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Table Settings'),
        className: "blocks-table-settings"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Fixed width table cells'),
        checked: !!hasFixedLayout,
        onChange: this.onChangeFixedLayout
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("table", {
        className: classes
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(Section, {
        type: "head",
        rows: head
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(Section, {
        type: "body",
        rows: body
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(Section, {
        type: "foot",
        rows: foot
      })));
    }
  }]);

  return TableEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Component"]);




/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/table/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/table/index.js ***!
  \***************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/table/edit.js");


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var tableContentPasteSchema = {
  tr: {
    children: {
      th: {
        children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["getPhrasingContentSchema"])()
      },
      td: {
        children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["getPhrasingContentSchema"])()
      }
    }
  }
};
var tablePasteSchema = {
  table: {
    children: {
      thead: {
        children: tableContentPasteSchema
      },
      tfoot: {
        children: tableContentPasteSchema
      },
      tbody: {
        children: tableContentPasteSchema
      }
    }
  }
};

function getTableSectionAttributeSchema(section) {
  return {
    type: 'array',
    default: [],
    source: 'query',
    selector: "t".concat(section, " tr"),
    query: {
      cells: {
        type: 'array',
        default: [],
        source: 'query',
        selector: 'td,th',
        query: {
          content: {
            source: 'html'
          },
          tag: {
            type: 'string',
            default: 'td',
            source: 'tag'
          }
        }
      }
    }
  };
}

var name = 'core/table';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Table'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Insert a table -- perfect for sharing charts and data.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M20 3H5L3 5v14l2 2h15l2-2V5l-2-2zm0 2v3H5V5h15zm-5 14h-5v-9h5v9zM5 10h3v9H5v-9zm12 9v-9h3v9h-3z"
  }))),
  category: 'formatting',
  attributes: {
    hasFixedLayout: {
      type: 'boolean',
      default: false
    },
    head: getTableSectionAttributeSchema('head'),
    body: getTableSectionAttributeSchema('body'),
    foot: getTableSectionAttributeSchema('foot')
  },
  supports: {
    align: true
  },
  transforms: {
    from: [{
      type: 'raw',
      selector: 'table',
      schema: tablePasteSchema
    }]
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_5__["default"],
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var hasFixedLayout = attributes.hasFixedLayout,
        head = attributes.head,
        body = attributes.body,
        foot = attributes.foot;
    var isEmpty = !head.length && !body.length && !foot.length;

    if (isEmpty) {
      return null;
    }

    var classes = classnames__WEBPACK_IMPORTED_MODULE_1___default()({
      'has-fixed-layout': hasFixedLayout
    });

    var Section = function Section(_ref2) {
      var type = _ref2.type,
          rows = _ref2.rows;

      if (!rows.length) {
        return null;
      }

      var Tag = "t".concat(type);
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Tag, null, rows.map(function (_ref3, rowIndex) {
        var cells = _ref3.cells;
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("tr", {
          key: rowIndex
        }, cells.map(function (_ref4, cellIndex) {
          var content = _ref4.content,
              tag = _ref4.tag;
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"].Content, {
            tagName: tag,
            value: content,
            key: cellIndex
          });
        }));
      }));
    };

    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("table", {
      className: classes
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Section, {
      type: "head",
      rows: head
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Section, {
      type: "body",
      rows: body
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Section, {
      type: "foot",
      rows: foot
    }));
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/table/state.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/table/state.js ***!
  \***************************************************************************/
/*! exports provided: createTable, updateCellContent, insertRow, deleteRow, insertColumn, deleteColumn */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createTable", function() { return createTable; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "updateCellContent", function() { return updateCellContent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "insertRow", function() { return insertRow; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "deleteRow", function() { return deleteRow; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "insertColumn", function() { return insertColumn; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "deleteColumn", function() { return deleteColumn; });
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);




/**
 * External dependencies
 */

/**
 * Creates a table state.
 *
 * @param {number} options.rowCount    Row count for the table to create.
 * @param {number} options.columnCount Column count for the table to create.
 *
 * @return {Object} New table state.
 */

function createTable(_ref) {
  var rowCount = _ref.rowCount,
      columnCount = _ref.columnCount;
  return {
    body: Object(lodash__WEBPACK_IMPORTED_MODULE_3__["times"])(rowCount, function () {
      return {
        cells: Object(lodash__WEBPACK_IMPORTED_MODULE_3__["times"])(columnCount, function () {
          return {
            content: '',
            tag: 'td'
          };
        })
      };
    })
  };
}
/**
 * Updates cell content in the table state.
 *
 * @param {Object} state               Current table state.
 * @param {string} options.section     Section of the cell to update.
 * @param {number} options.rowIndex    Row index of the cell to update.
 * @param {number} options.columnIndex Column index of the cell to update.
 * @param {Array}  options.content     Content to set for the cell.
 *
 * @return {Object} New table state.
 */

function updateCellContent(state, _ref2) {
  var section = _ref2.section,
      rowIndex = _ref2.rowIndex,
      columnIndex = _ref2.columnIndex,
      content = _ref2.content;
  return Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, section, state[section].map(function (row, currentRowIndex) {
    if (currentRowIndex !== rowIndex) {
      return row;
    }

    return {
      cells: row.cells.map(function (cell, currentColumnIndex) {
        if (currentColumnIndex !== columnIndex) {
          return cell;
        }

        return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__["default"])({}, cell, {
          content: content
        });
      })
    };
  }));
}
/**
 * Inserts a row in the table state.
 *
 * @param {Object} state            Current table state.
 * @param {string} options.section  Section in which to insert the row.
 * @param {number} options.rowIndex Row index at which to insert the row.
 *
 * @return {Object} New table state.
 */

function insertRow(state, _ref4) {
  var section = _ref4.section,
      rowIndex = _ref4.rowIndex;
  var cellCount = state[section][0].cells.length;
  return Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, section, Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(state[section].slice(0, rowIndex)).concat([{
    cells: Object(lodash__WEBPACK_IMPORTED_MODULE_3__["times"])(cellCount, function () {
      return {
        content: '',
        tag: 'td'
      };
    })
  }], Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(state[section].slice(rowIndex))));
}
/**
 * Deletes a row from the table state.
 *
 * @param {Object} state            Current table state.
 * @param {string} options.section  Section in which to delete the row.
 * @param {number} options.rowIndex Row index to delete.
 *
 * @return {Object} New table state.
 */

function deleteRow(state, _ref6) {
  var section = _ref6.section,
      rowIndex = _ref6.rowIndex;
  return Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, section, state[section].filter(function (row, index) {
    return index !== rowIndex;
  }));
}
/**
 * Inserts a column in the table state.
 *
 * @param {Object} state               Current table state.
 * @param {string} options.section     Section in which to insert the column.
 * @param {number} options.columnIndex Column index at which to insert the column.
 *
 * @return {Object} New table state.
 */

function insertColumn(state, _ref8) {
  var section = _ref8.section,
      columnIndex = _ref8.columnIndex;
  return Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, section, state[section].map(function (row) {
    return {
      cells: Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(row.cells.slice(0, columnIndex)).concat([{
        content: '',
        tag: 'td'
      }], Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(row.cells.slice(columnIndex)))
    };
  }));
}
/**
 * Deletes a column from the table state.
 *
 * @param {Object} state               Current table state.
 * @param {string} options.section     Section in which to delete the column.
 * @param {number} options.columnIndex Column index to delete.
 *
 * @return {Object} New table state.
 */

function deleteColumn(state, _ref10) {
  var section = _ref10.section,
      columnIndex = _ref10.columnIndex;
  return Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, section, state[section].map(function (row) {
    return {
      cells: row.cells.filter(function (cell, index) {
        return index !== columnIndex;
      })
    };
  }).filter(function (row) {
    return row.cells.length;
  }));
}


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/template/index.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/template/index.js ***!
  \******************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */


var name = 'core/template';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Reusable Template'),
  category: 'reusable',
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Template block used as a container.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: "0",
    fill: "none",
    width: "24",
    height: "24"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M19 3H5c-1.105 0-2 .895-2 2v14c0 1.105.895 2 2 2h14c1.105 0 2-.895 2-2V5c0-1.105-.895-2-2-2zM6 6h5v5H6V6zm4.5 13C9.12 19 8 17.88 8 16.5S9.12 14 10.5 14s2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5zm3-6l3-5 3 5h-6z"
  }))),
  supports: {
    customClassName: false,
    html: false,
    inserter: false
  },
  edit: function edit() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InnerBlocks"], null);
  },
  save: function save() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InnerBlocks"].Content, null);
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/text-columns/index.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/text-columns/index.js ***!
  \**********************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/deprecated */ "@wordpress/deprecated");
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__);



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








var name = 'core/text-columns';
var settings = {
  // Disable insertion as this block is deprecated and ultimately replaced by the Columns block.
  supports: {
    inserter: false
  },
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Text Columns (deprecated)'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('This block is deprecated. Please use the Columns block instead.'),
  icon: 'columns',
  category: 'layout',
  attributes: {
    content: {
      type: 'array',
      source: 'query',
      selector: 'p',
      query: {
        children: {
          source: 'html'
        }
      },
      default: [{
        children: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["create"])()
      }, {
        children: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["create"])()
      }]
    },
    columns: {
      type: 'number',
      default: 2
    },
    width: {
      type: 'string'
    }
  },
  transforms: {
    to: [{
      type: 'block',
      blocks: ['core/columns'],
      transform: function transform(_ref) {
        var className = _ref.className,
            columns = _ref.columns,
            content = _ref.content,
            width = _ref.width;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/columns', {
          align: 'wide' === width || 'full' === width ? width : undefined,
          className: className,
          columns: columns
        }, content.map(function (_ref2) {
          var children = _ref2.children;
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/column', {}, [Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/paragraph', {
            content: children
          })]);
        }));
      }
    }]
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var width = attributes.width;

    if ('wide' === width || 'full' === width) {
      return {
        'data-align': width
      };
    }
  },
  edit: function edit(_ref3) {
    var attributes = _ref3.attributes,
        setAttributes = _ref3.setAttributes,
        className = _ref3.className;
    var width = attributes.width,
        content = attributes.content,
        columns = attributes.columns;
    _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7___default()('The Text Columns block', {
      alternative: 'the Columns block',
      plugin: 'Gutenberg'
    });
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["BlockAlignmentToolbar"], {
      value: width,
      onChange: function onChange(nextWidth) {
        return setAttributes({
          width: nextWidth
        });
      },
      controls: ['center', 'wide', 'full']
    })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["RangeControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Columns'),
      value: columns,
      onChange: function onChange(value) {
        return setAttributes({
          columns: value
        });
      },
      min: 2,
      max: 4
    }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "".concat(className, " align").concat(width, " columns-").concat(columns)
    }, Object(lodash__WEBPACK_IMPORTED_MODULE_2__["times"])(columns, function (index) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
        className: "wp-block-column",
        key: "column-".concat(index)
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"], {
        tagName: "p",
        value: Object(lodash__WEBPACK_IMPORTED_MODULE_2__["get"])(content, [index, 'children']),
        onChange: function onChange(nextContent) {
          setAttributes({
            content: Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(content.slice(0, index)).concat([{
              children: nextContent
            }], Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(content.slice(index + 1)))
          });
        },
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('New Column')
      }));
    })));
  },
  save: function save(_ref4) {
    var attributes = _ref4.attributes;
    var width = attributes.width,
        content = attributes.content,
        columns = attributes.columns;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "align".concat(width, " columns-").concat(columns)
    }, Object(lodash__WEBPACK_IMPORTED_MODULE_2__["times"])(columns, function (index) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
        className: "wp-block-column",
        key: "column-".concat(index)
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: "p",
        value: Object(lodash__WEBPACK_IMPORTED_MODULE_2__["get"])(content, [index, 'children'])
      }));
    }));
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/verse/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/verse/index.js ***!
  \***************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);


/**
 * WordPress
 */




var name = 'core/verse';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Verse'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('A block for haiku? Why not? Blocks for all the things! (See what we did here?)'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M3 17v4h4l11-11-4-4L3 17zm3 2H5v-1l9-9 1 1-9 9zM21 6l-3-3h-1l-2 2 4 4 2-2V6z"
  })),
  category: 'formatting',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('poetry')],
  attributes: {
    content: {
      source: 'html',
      selector: 'pre'
    },
    textAlign: {
      type: 'string'
    }
  },
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/verse', attributes);
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/paragraph', attributes);
      }
    }]
  },
  edit: function edit(_ref) {
    var attributes = _ref.attributes,
        setAttributes = _ref.setAttributes,
        className = _ref.className,
        mergeBlocks = _ref.mergeBlocks;
    var textAlign = attributes.textAlign,
        content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["AlignmentToolbar"], {
      value: textAlign,
      onChange: function onChange(nextAlign) {
        setAttributes({
          textAlign: nextAlign
        });
      }
    })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"], {
      tagName: "pre",
      value: content,
      onChange: function onChange(nextContent) {
        setAttributes({
          content: nextContent
        });
      },
      style: {
        textAlign: textAlign
      },
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Write…'),
      wrapperClassName: className,
      onMerge: mergeBlocks
    }));
  },
  save: function save(_ref2) {
    var attributes = _ref2.attributes,
        className = _ref2.className;
    var textAlign = attributes.textAlign,
        content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"].Content, {
      tagName: "pre",
      className: className,
      style: {
        textAlign: textAlign
      },
      value: content
    });
  },
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: attributes.content + attributesToMerge.content
    };
  }
};


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/video/edit.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/video/edit.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/slicedToArray */ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/blob */ "@wordpress/blob");
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blob__WEBPACK_IMPORTED_MODULE_12__);










/**
 * WordPress dependencies
 */





var ALLOWED_MEDIA_TYPES = ['video'];

var VideoEdit =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_6__["default"])(VideoEdit, _Component);

  function VideoEdit() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_2__["default"])(this, VideoEdit);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__["default"])(VideoEdit).apply(this, arguments)); // edit component has its own src in the state so it can be edited
    // without setting the actual value outside of the edit UI

    _this.state = {
      editing: !_this.props.attributes.src
    };
    _this.videoPlayer = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createRef"])();
    _this.toggleAttribute = _this.toggleAttribute.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.onSelectURL = _this.onSelectURL.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.onSelectPoster = _this.onSelectPoster.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    _this.onRemovePoster = _this.onRemovePoster.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_7__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_3__["default"])(VideoEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          noticeOperations = _this$props.noticeOperations,
          setAttributes = _this$props.setAttributes;
      var id = attributes.id,
          _attributes$src = attributes.src,
          src = _attributes$src === void 0 ? '' : _attributes$src;

      if (!id && src.indexOf('blob:') === 0) {
        var file = Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_12__["getBlobByURL"])(src);

        if (file) {
          Object(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["mediaUpload"])({
            filesList: [file],
            onFileChange: function onFileChange(_ref) {
              var _ref2 = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref, 1),
                  url = _ref2[0].url;

              setAttributes({
                src: url
              });
            },
            onError: function onError(message) {
              _this2.setState({
                editing: true
              });

              noticeOperations.createErrorNotice(message);
            },
            allowedTypes: ALLOWED_MEDIA_TYPES
          });
        }
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.attributes.poster !== prevProps.attributes.poster) {
        this.videoPlayer.current.load();
      }
    }
  }, {
    key: "toggleAttribute",
    value: function toggleAttribute(attribute) {
      var _this3 = this;

      return function (newValue) {
        _this3.props.setAttributes(Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({}, attribute, newValue));
      };
    }
  }, {
    key: "onSelectURL",
    value: function onSelectURL(newSrc) {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      var src = attributes.src; // Set the block's src from the edit component's state, and switch off
      // the editing UI.

      if (newSrc !== src) {
        setAttributes({
          src: newSrc,
          id: undefined
        });
      }

      this.setState({
        editing: false
      });
    }
  }, {
    key: "onSelectPoster",
    value: function onSelectPoster(image) {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        poster: image.url
      });
    }
  }, {
    key: "onRemovePoster",
    value: function onRemovePoster() {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        poster: ''
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this4 = this;

      var _this$props$attribute = this.props.attributes,
          autoplay = _this$props$attribute.autoplay,
          caption = _this$props$attribute.caption,
          controls = _this$props$attribute.controls,
          loop = _this$props$attribute.loop,
          muted = _this$props$attribute.muted,
          poster = _this$props$attribute.poster,
          preload = _this$props$attribute.preload,
          src = _this$props$attribute.src;
      var _this$props3 = this.props,
          setAttributes = _this$props3.setAttributes,
          isSelected = _this$props3.isSelected,
          className = _this$props3.className,
          noticeOperations = _this$props3.noticeOperations,
          noticeUI = _this$props3.noticeUI;
      var editing = this.state.editing;

      var switchToEditing = function switchToEditing() {
        _this4.setState({
          editing: true
        });
      };

      var onSelectVideo = function onSelectVideo(media) {
        if (!media || !media.url) {
          // in this case there was an error and we should continue in the editing state
          // previous attributes should be removed because they may be temporary blob urls
          setAttributes({
            src: undefined,
            id: undefined
          });
          switchToEditing();
          return;
        } // sets the block's attribute and updates the edit component from the
        // selected media, then switches off the editing UI


        setAttributes({
          src: media.url,
          id: media.id
        });

        _this4.setState({
          src: media.url,
          editing: false
        });
      };

      if (editing) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["MediaPlaceholder"], {
          icon: "media-video",
          labels: {
            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Video'),
            name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('a video')
          },
          className: className,
          onSelect: onSelectVideo,
          onSelectURL: this.onSelectURL,
          accept: "video/*",
          allowedTypes: ALLOWED_MEDIA_TYPES,
          value: this.props.attributes,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice
        });
      }
      /* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */


      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Toolbar"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["IconButton"], {
        className: "components-icon-button components-toolbar__control",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Edit video'),
        onClick: switchToEditing,
        icon: "edit"
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Video Settings')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Autoplay'),
        onChange: this.toggleAttribute('autoplay'),
        checked: autoplay
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Loop'),
        onChange: this.toggleAttribute('loop'),
        checked: loop
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Muted'),
        onChange: this.toggleAttribute('muted'),
        checked: muted
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["ToggleControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Playback Controls'),
        onChange: this.toggleAttribute('controls'),
        checked: controls
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["SelectControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Preload'),
        value: preload,
        onChange: function onChange(value) {
          return setAttributes({
            preload: value
          });
        },
        options: [{
          value: 'auto',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Auto')
        }, {
          value: 'metadata',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Metadata')
        }, {
          value: 'none',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('None')
        }]
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["BaseControl"], {
        className: "editor-video-poster-control",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Poster Image')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["MediaUpload"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Select Poster Image'),
        onSelect: this.onSelectPoster,
        allowedTypes: ALLOWED_MEDIA_TYPES,
        render: function render(_ref3) {
          var open = _ref3.open;
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Button"], {
            isDefault: true,
            onClick: open
          }, !_this4.props.attributes.poster ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Select Poster Image') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Replace image'));
        }
      }), !!this.props.attributes.poster && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Button"], {
        onClick: this.onRemovePoster,
        isLink: true,
        isDestructive: true
      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Remove Poster Image'))))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("figure", {
        className: className
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Disabled"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])("video", {
        controls: controls,
        poster: poster,
        src: src,
        ref: this.videoPlayer
      })), (!_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["RichText"].isEmpty(caption) || isSelected) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__["RichText"], {
        tagName: "figcaption",
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Write caption…'),
        value: caption,
        onChange: function onChange(value) {
          return setAttributes({
            caption: value
          });
        },
        inlineToolbar: true
      })));
      /* eslint-enable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */
    }
  }]);

  return VideoEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["withNotices"])(VideoEdit));


/***/ }),

/***/ "./node_modules/@wordpress/block-library/build-module/video/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/block-library/build-module/video/index.js ***!
  \***************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blob */ "@wordpress/blob");
/* harmony import */ var _wordpress_blob__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blob__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./edit */ "./node_modules/@wordpress/block-library/build-module/video/edit.js");


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var name = 'core/video';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Video'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a video file and a simple video player.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M4 6l2 4h14v8H4V6m18-2h-4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4L2 6v12l2 2h16l2-2V4z"
  })),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('movie')],
  category: 'common',
  attributes: {
    autoplay: {
      type: 'boolean',
      source: 'attribute',
      selector: 'video',
      attribute: 'autoplay'
    },
    caption: {
      source: 'html',
      selector: 'figcaption'
    },
    controls: {
      type: 'boolean',
      source: 'attribute',
      selector: 'video',
      attribute: 'controls',
      default: true
    },
    id: {
      type: 'number'
    },
    loop: {
      type: 'boolean',
      source: 'attribute',
      selector: 'video',
      attribute: 'loop'
    },
    muted: {
      type: 'boolean',
      source: 'attribute',
      selector: 'video',
      attribute: 'muted'
    },
    poster: {
      type: 'string',
      source: 'attribute',
      selector: 'video',
      attribute: 'poster'
    },
    preload: {
      type: 'string',
      source: 'attribute',
      selector: 'video',
      attribute: 'preload',
      default: 'metadata'
    },
    src: {
      type: 'string',
      source: 'attribute',
      selector: 'video',
      attribute: 'src'
    }
  },
  transforms: {
    from: [{
      type: 'files',
      isMatch: function isMatch(files) {
        return files.length === 1 && files[0].type.indexOf('video/') === 0;
      },
      transform: function transform(files) {
        var file = files[0]; // We don't need to upload the media directly here
        // It's already done as part of the `componentDidMount`
        // in the video block

        var block = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/video', {
          src: Object(_wordpress_blob__WEBPACK_IMPORTED_MODULE_4__["createBlobURL"])(file)
        });
        return block;
      }
    }]
  },
  supports: {
    align: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_5__["default"],
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var autoplay = attributes.autoplay,
        caption = attributes.caption,
        controls = attributes.controls,
        loop = attributes.loop,
        muted = attributes.muted,
        poster = attributes.poster,
        preload = attributes.preload,
        src = attributes.src;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("figure", null, src && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("video", {
      autoPlay: autoplay,
      controls: controls,
      loop: loop,
      muted: muted,
      poster: poster,
      preload: preload !== 'metadata' ? preload : undefined,
      src: src
    }), !_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["RichText"].isEmpty(caption) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["RichText"].Content, {
      tagName: "figcaption",
      value: caption
    }));
  }
};


/***/ }),

/***/ "./node_modules/classnames/dedupe.js":
/*!*******************************************!*\
  !*** ./node_modules/classnames/dedupe.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2017 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var classNames = (function () {
		// don't inherit from Object so we can skip hasOwnProperty check later
		// http://stackoverflow.com/questions/15518328/creating-js-object-with-object-createnull#answer-21079232
		function StorageObject() {}
		StorageObject.prototype = Object.create(null);

		function _parseArray (resultSet, array) {
			var length = array.length;

			for (var i = 0; i < length; ++i) {
				_parse(resultSet, array[i]);
			}
		}

		var hasOwn = {}.hasOwnProperty;

		function _parseNumber (resultSet, num) {
			resultSet[num] = true;
		}

		function _parseObject (resultSet, object) {
			for (var k in object) {
				if (hasOwn.call(object, k)) {
					// set value to false instead of deleting it to avoid changing object structure
					// https://www.smashingmagazine.com/2012/11/writing-fast-memory-efficient-javascript/#de-referencing-misconceptions
					resultSet[k] = !!object[k];
				}
			}
		}

		var SPACE = /\s+/;
		function _parseString (resultSet, str) {
			var array = str.split(SPACE);
			var length = array.length;

			for (var i = 0; i < length; ++i) {
				resultSet[array[i]] = true;
			}
		}

		function _parse (resultSet, arg) {
			if (!arg) return;
			var argType = typeof arg;

			// 'foo bar'
			if (argType === 'string') {
				_parseString(resultSet, arg);

			// ['foo', 'bar', ...]
			} else if (Array.isArray(arg)) {
				_parseArray(resultSet, arg);

			// { 'foo': true, ... }
			} else if (argType === 'object') {
				_parseObject(resultSet, arg);

			// '130'
			} else if (argType === 'number') {
				_parseNumber(resultSet, arg);
			}
		}

		function _classNames () {
			// don't leak arguments
			// https://github.com/petkaantonov/bluebird/wiki/Optimization-killers#32-leaking-arguments
			var len = arguments.length;
			var args = Array(len);
			for (var i = 0; i < len; i++) {
				args[i] = arguments[i];
			}

			var classSet = new StorageObject();
			_parseArray(classSet, args);

			var list = [];

			for (var k in classSet) {
				if (classSet[k]) {
					list.push(k)
				}
			}

			return list.join(' ');
		}

		return _classNames;
	})();

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

/***/ "./node_modules/classnames/index.js":
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
/*! no static exports found */
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

/***/ "./node_modules/memize/index.js":
/*!**************************************!*\
  !*** ./node_modules/memize/index.js ***!
  \**************************************/
/*! no static exports found */
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

/***/ "./node_modules/punycode/punycode.js":
/*!*******************************************!*\
  !*** ./node_modules/punycode/punycode.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(module, global) {var __WEBPACK_AMD_DEFINE_RESULT__;/*! https://mths.be/punycode v1.4.1 by @mathias */
;(function(root) {

	/** Detect free variables */
	var freeExports =  true && exports &&
		!exports.nodeType && exports;
	var freeModule =  true && module &&
		!module.nodeType && module;
	var freeGlobal = typeof global == 'object' && global;
	if (
		freeGlobal.global === freeGlobal ||
		freeGlobal.window === freeGlobal ||
		freeGlobal.self === freeGlobal
	) {
		root = freeGlobal;
	}

	/**
	 * The `punycode` object.
	 * @name punycode
	 * @type Object
	 */
	var punycode,

	/** Highest positive signed 32-bit float value */
	maxInt = 2147483647, // aka. 0x7FFFFFFF or 2^31-1

	/** Bootstring parameters */
	base = 36,
	tMin = 1,
	tMax = 26,
	skew = 38,
	damp = 700,
	initialBias = 72,
	initialN = 128, // 0x80
	delimiter = '-', // '\x2D'

	/** Regular expressions */
	regexPunycode = /^xn--/,
	regexNonASCII = /[^\x20-\x7E]/, // unprintable ASCII chars + non-ASCII chars
	regexSeparators = /[\x2E\u3002\uFF0E\uFF61]/g, // RFC 3490 separators

	/** Error messages */
	errors = {
		'overflow': 'Overflow: input needs wider integers to process',
		'not-basic': 'Illegal input >= 0x80 (not a basic code point)',
		'invalid-input': 'Invalid input'
	},

	/** Convenience shortcuts */
	baseMinusTMin = base - tMin,
	floor = Math.floor,
	stringFromCharCode = String.fromCharCode,

	/** Temporary variable */
	key;

	/*--------------------------------------------------------------------------*/

	/**
	 * A generic error utility function.
	 * @private
	 * @param {String} type The error type.
	 * @returns {Error} Throws a `RangeError` with the applicable error message.
	 */
	function error(type) {
		throw new RangeError(errors[type]);
	}

	/**
	 * A generic `Array#map` utility function.
	 * @private
	 * @param {Array} array The array to iterate over.
	 * @param {Function} callback The function that gets called for every array
	 * item.
	 * @returns {Array} A new array of values returned by the callback function.
	 */
	function map(array, fn) {
		var length = array.length;
		var result = [];
		while (length--) {
			result[length] = fn(array[length]);
		}
		return result;
	}

	/**
	 * A simple `Array#map`-like wrapper to work with domain name strings or email
	 * addresses.
	 * @private
	 * @param {String} domain The domain name or email address.
	 * @param {Function} callback The function that gets called for every
	 * character.
	 * @returns {Array} A new string of characters returned by the callback
	 * function.
	 */
	function mapDomain(string, fn) {
		var parts = string.split('@');
		var result = '';
		if (parts.length > 1) {
			// In email addresses, only the domain name should be punycoded. Leave
			// the local part (i.e. everything up to `@`) intact.
			result = parts[0] + '@';
			string = parts[1];
		}
		// Avoid `split(regex)` for IE8 compatibility. See #17.
		string = string.replace(regexSeparators, '\x2E');
		var labels = string.split('.');
		var encoded = map(labels, fn).join('.');
		return result + encoded;
	}

	/**
	 * Creates an array containing the numeric code points of each Unicode
	 * character in the string. While JavaScript uses UCS-2 internally,
	 * this function will convert a pair of surrogate halves (each of which
	 * UCS-2 exposes as separate characters) into a single code point,
	 * matching UTF-16.
	 * @see `punycode.ucs2.encode`
	 * @see <https://mathiasbynens.be/notes/javascript-encoding>
	 * @memberOf punycode.ucs2
	 * @name decode
	 * @param {String} string The Unicode input string (UCS-2).
	 * @returns {Array} The new array of code points.
	 */
	function ucs2decode(string) {
		var output = [],
		    counter = 0,
		    length = string.length,
		    value,
		    extra;
		while (counter < length) {
			value = string.charCodeAt(counter++);
			if (value >= 0xD800 && value <= 0xDBFF && counter < length) {
				// high surrogate, and there is a next character
				extra = string.charCodeAt(counter++);
				if ((extra & 0xFC00) == 0xDC00) { // low surrogate
					output.push(((value & 0x3FF) << 10) + (extra & 0x3FF) + 0x10000);
				} else {
					// unmatched surrogate; only append this code unit, in case the next
					// code unit is the high surrogate of a surrogate pair
					output.push(value);
					counter--;
				}
			} else {
				output.push(value);
			}
		}
		return output;
	}

	/**
	 * Creates a string based on an array of numeric code points.
	 * @see `punycode.ucs2.decode`
	 * @memberOf punycode.ucs2
	 * @name encode
	 * @param {Array} codePoints The array of numeric code points.
	 * @returns {String} The new Unicode string (UCS-2).
	 */
	function ucs2encode(array) {
		return map(array, function(value) {
			var output = '';
			if (value > 0xFFFF) {
				value -= 0x10000;
				output += stringFromCharCode(value >>> 10 & 0x3FF | 0xD800);
				value = 0xDC00 | value & 0x3FF;
			}
			output += stringFromCharCode(value);
			return output;
		}).join('');
	}

	/**
	 * Converts a basic code point into a digit/integer.
	 * @see `digitToBasic()`
	 * @private
	 * @param {Number} codePoint The basic numeric code point value.
	 * @returns {Number} The numeric value of a basic code point (for use in
	 * representing integers) in the range `0` to `base - 1`, or `base` if
	 * the code point does not represent a value.
	 */
	function basicToDigit(codePoint) {
		if (codePoint - 48 < 10) {
			return codePoint - 22;
		}
		if (codePoint - 65 < 26) {
			return codePoint - 65;
		}
		if (codePoint - 97 < 26) {
			return codePoint - 97;
		}
		return base;
	}

	/**
	 * Converts a digit/integer into a basic code point.
	 * @see `basicToDigit()`
	 * @private
	 * @param {Number} digit The numeric value of a basic code point.
	 * @returns {Number} The basic code point whose value (when used for
	 * representing integers) is `digit`, which needs to be in the range
	 * `0` to `base - 1`. If `flag` is non-zero, the uppercase form is
	 * used; else, the lowercase form is used. The behavior is undefined
	 * if `flag` is non-zero and `digit` has no uppercase form.
	 */
	function digitToBasic(digit, flag) {
		//  0..25 map to ASCII a..z or A..Z
		// 26..35 map to ASCII 0..9
		return digit + 22 + 75 * (digit < 26) - ((flag != 0) << 5);
	}

	/**
	 * Bias adaptation function as per section 3.4 of RFC 3492.
	 * https://tools.ietf.org/html/rfc3492#section-3.4
	 * @private
	 */
	function adapt(delta, numPoints, firstTime) {
		var k = 0;
		delta = firstTime ? floor(delta / damp) : delta >> 1;
		delta += floor(delta / numPoints);
		for (/* no initialization */; delta > baseMinusTMin * tMax >> 1; k += base) {
			delta = floor(delta / baseMinusTMin);
		}
		return floor(k + (baseMinusTMin + 1) * delta / (delta + skew));
	}

	/**
	 * Converts a Punycode string of ASCII-only symbols to a string of Unicode
	 * symbols.
	 * @memberOf punycode
	 * @param {String} input The Punycode string of ASCII-only symbols.
	 * @returns {String} The resulting string of Unicode symbols.
	 */
	function decode(input) {
		// Don't use UCS-2
		var output = [],
		    inputLength = input.length,
		    out,
		    i = 0,
		    n = initialN,
		    bias = initialBias,
		    basic,
		    j,
		    index,
		    oldi,
		    w,
		    k,
		    digit,
		    t,
		    /** Cached calculation results */
		    baseMinusT;

		// Handle the basic code points: let `basic` be the number of input code
		// points before the last delimiter, or `0` if there is none, then copy
		// the first basic code points to the output.

		basic = input.lastIndexOf(delimiter);
		if (basic < 0) {
			basic = 0;
		}

		for (j = 0; j < basic; ++j) {
			// if it's not a basic code point
			if (input.charCodeAt(j) >= 0x80) {
				error('not-basic');
			}
			output.push(input.charCodeAt(j));
		}

		// Main decoding loop: start just after the last delimiter if any basic code
		// points were copied; start at the beginning otherwise.

		for (index = basic > 0 ? basic + 1 : 0; index < inputLength; /* no final expression */) {

			// `index` is the index of the next character to be consumed.
			// Decode a generalized variable-length integer into `delta`,
			// which gets added to `i`. The overflow checking is easier
			// if we increase `i` as we go, then subtract off its starting
			// value at the end to obtain `delta`.
			for (oldi = i, w = 1, k = base; /* no condition */; k += base) {

				if (index >= inputLength) {
					error('invalid-input');
				}

				digit = basicToDigit(input.charCodeAt(index++));

				if (digit >= base || digit > floor((maxInt - i) / w)) {
					error('overflow');
				}

				i += digit * w;
				t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);

				if (digit < t) {
					break;
				}

				baseMinusT = base - t;
				if (w > floor(maxInt / baseMinusT)) {
					error('overflow');
				}

				w *= baseMinusT;

			}

			out = output.length + 1;
			bias = adapt(i - oldi, out, oldi == 0);

			// `i` was supposed to wrap around from `out` to `0`,
			// incrementing `n` each time, so we'll fix that now:
			if (floor(i / out) > maxInt - n) {
				error('overflow');
			}

			n += floor(i / out);
			i %= out;

			// Insert `n` at position `i` of the output
			output.splice(i++, 0, n);

		}

		return ucs2encode(output);
	}

	/**
	 * Converts a string of Unicode symbols (e.g. a domain name label) to a
	 * Punycode string of ASCII-only symbols.
	 * @memberOf punycode
	 * @param {String} input The string of Unicode symbols.
	 * @returns {String} The resulting Punycode string of ASCII-only symbols.
	 */
	function encode(input) {
		var n,
		    delta,
		    handledCPCount,
		    basicLength,
		    bias,
		    j,
		    m,
		    q,
		    k,
		    t,
		    currentValue,
		    output = [],
		    /** `inputLength` will hold the number of code points in `input`. */
		    inputLength,
		    /** Cached calculation results */
		    handledCPCountPlusOne,
		    baseMinusT,
		    qMinusT;

		// Convert the input in UCS-2 to Unicode
		input = ucs2decode(input);

		// Cache the length
		inputLength = input.length;

		// Initialize the state
		n = initialN;
		delta = 0;
		bias = initialBias;

		// Handle the basic code points
		for (j = 0; j < inputLength; ++j) {
			currentValue = input[j];
			if (currentValue < 0x80) {
				output.push(stringFromCharCode(currentValue));
			}
		}

		handledCPCount = basicLength = output.length;

		// `handledCPCount` is the number of code points that have been handled;
		// `basicLength` is the number of basic code points.

		// Finish the basic string - if it is not empty - with a delimiter
		if (basicLength) {
			output.push(delimiter);
		}

		// Main encoding loop:
		while (handledCPCount < inputLength) {

			// All non-basic code points < n have been handled already. Find the next
			// larger one:
			for (m = maxInt, j = 0; j < inputLength; ++j) {
				currentValue = input[j];
				if (currentValue >= n && currentValue < m) {
					m = currentValue;
				}
			}

			// Increase `delta` enough to advance the decoder's <n,i> state to <m,0>,
			// but guard against overflow
			handledCPCountPlusOne = handledCPCount + 1;
			if (m - n > floor((maxInt - delta) / handledCPCountPlusOne)) {
				error('overflow');
			}

			delta += (m - n) * handledCPCountPlusOne;
			n = m;

			for (j = 0; j < inputLength; ++j) {
				currentValue = input[j];

				if (currentValue < n && ++delta > maxInt) {
					error('overflow');
				}

				if (currentValue == n) {
					// Represent delta as a generalized variable-length integer
					for (q = delta, k = base; /* no condition */; k += base) {
						t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);
						if (q < t) {
							break;
						}
						qMinusT = q - t;
						baseMinusT = base - t;
						output.push(
							stringFromCharCode(digitToBasic(t + qMinusT % baseMinusT, 0))
						);
						q = floor(qMinusT / baseMinusT);
					}

					output.push(stringFromCharCode(digitToBasic(q, 0)));
					bias = adapt(delta, handledCPCountPlusOne, handledCPCount == basicLength);
					delta = 0;
					++handledCPCount;
				}
			}

			++delta;
			++n;

		}
		return output.join('');
	}

	/**
	 * Converts a Punycode string representing a domain name or an email address
	 * to Unicode. Only the Punycoded parts of the input will be converted, i.e.
	 * it doesn't matter if you call it on a string that has already been
	 * converted to Unicode.
	 * @memberOf punycode
	 * @param {String} input The Punycoded domain name or email address to
	 * convert to Unicode.
	 * @returns {String} The Unicode representation of the given Punycode
	 * string.
	 */
	function toUnicode(input) {
		return mapDomain(input, function(string) {
			return regexPunycode.test(string)
				? decode(string.slice(4).toLowerCase())
				: string;
		});
	}

	/**
	 * Converts a Unicode string representing a domain name or an email address to
	 * Punycode. Only the non-ASCII parts of the domain name will be converted,
	 * i.e. it doesn't matter if you call it with a domain that's already in
	 * ASCII.
	 * @memberOf punycode
	 * @param {String} input The domain name or email address to convert, as a
	 * Unicode string.
	 * @returns {String} The Punycode representation of the given domain name or
	 * email address.
	 */
	function toASCII(input) {
		return mapDomain(input, function(string) {
			return regexNonASCII.test(string)
				? 'xn--' + encode(string)
				: string;
		});
	}

	/*--------------------------------------------------------------------------*/

	/** Define the public API */
	punycode = {
		/**
		 * A string representing the current Punycode.js version number.
		 * @memberOf punycode
		 * @type String
		 */
		'version': '1.4.1',
		/**
		 * An object of methods to convert from JavaScript's internal character
		 * representation (UCS-2) to Unicode code points, and back.
		 * @see <https://mathiasbynens.be/notes/javascript-encoding>
		 * @memberOf punycode
		 * @type Object
		 */
		'ucs2': {
			'decode': ucs2decode,
			'encode': ucs2encode
		},
		'decode': decode,
		'encode': encode,
		'toASCII': toASCII,
		'toUnicode': toUnicode
	};

	/** Expose `punycode` */
	// Some AMD build optimizers, like r.js, check for specific condition patterns
	// like the following:
	if (
		true
	) {
		!(__WEBPACK_AMD_DEFINE_RESULT__ = (function() {
			return punycode;
		}).call(exports, __webpack_require__, exports, module),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}

}(this));

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../webpack/buildin/module.js */ "./node_modules/webpack/buildin/module.js")(module), __webpack_require__(/*! ./../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/querystring-es3/decode.js":
/*!************************************************!*\
  !*** ./node_modules/querystring-es3/decode.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



// If obj.hasOwnProperty has been overridden, then calling
// obj.hasOwnProperty(prop) will break.
// See: https://github.com/joyent/node/issues/1707
function hasOwnProperty(obj, prop) {
  return Object.prototype.hasOwnProperty.call(obj, prop);
}

module.exports = function(qs, sep, eq, options) {
  sep = sep || '&';
  eq = eq || '=';
  var obj = {};

  if (typeof qs !== 'string' || qs.length === 0) {
    return obj;
  }

  var regexp = /\+/g;
  qs = qs.split(sep);

  var maxKeys = 1000;
  if (options && typeof options.maxKeys === 'number') {
    maxKeys = options.maxKeys;
  }

  var len = qs.length;
  // maxKeys <= 0 means that we should not limit keys count
  if (maxKeys > 0 && len > maxKeys) {
    len = maxKeys;
  }

  for (var i = 0; i < len; ++i) {
    var x = qs[i].replace(regexp, '%20'),
        idx = x.indexOf(eq),
        kstr, vstr, k, v;

    if (idx >= 0) {
      kstr = x.substr(0, idx);
      vstr = x.substr(idx + 1);
    } else {
      kstr = x;
      vstr = '';
    }

    k = decodeURIComponent(kstr);
    v = decodeURIComponent(vstr);

    if (!hasOwnProperty(obj, k)) {
      obj[k] = v;
    } else if (isArray(obj[k])) {
      obj[k].push(v);
    } else {
      obj[k] = [obj[k], v];
    }
  }

  return obj;
};

var isArray = Array.isArray || function (xs) {
  return Object.prototype.toString.call(xs) === '[object Array]';
};


/***/ }),

/***/ "./node_modules/querystring-es3/encode.js":
/*!************************************************!*\
  !*** ./node_modules/querystring-es3/encode.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



var stringifyPrimitive = function(v) {
  switch (typeof v) {
    case 'string':
      return v;

    case 'boolean':
      return v ? 'true' : 'false';

    case 'number':
      return isFinite(v) ? v : '';

    default:
      return '';
  }
};

module.exports = function(obj, sep, eq, name) {
  sep = sep || '&';
  eq = eq || '=';
  if (obj === null) {
    obj = undefined;
  }

  if (typeof obj === 'object') {
    return map(objectKeys(obj), function(k) {
      var ks = encodeURIComponent(stringifyPrimitive(k)) + eq;
      if (isArray(obj[k])) {
        return map(obj[k], function(v) {
          return ks + encodeURIComponent(stringifyPrimitive(v));
        }).join(sep);
      } else {
        return ks + encodeURIComponent(stringifyPrimitive(obj[k]));
      }
    }).join(sep);

  }

  if (!name) return '';
  return encodeURIComponent(stringifyPrimitive(name)) + eq +
         encodeURIComponent(stringifyPrimitive(obj));
};

var isArray = Array.isArray || function (xs) {
  return Object.prototype.toString.call(xs) === '[object Array]';
};

function map (xs, f) {
  if (xs.map) return xs.map(f);
  var res = [];
  for (var i = 0; i < xs.length; i++) {
    res.push(f(xs[i], i));
  }
  return res;
}

var objectKeys = Object.keys || function (obj) {
  var res = [];
  for (var key in obj) {
    if (Object.prototype.hasOwnProperty.call(obj, key)) res.push(key);
  }
  return res;
};


/***/ }),

/***/ "./node_modules/querystring-es3/index.js":
/*!***********************************************!*\
  !*** ./node_modules/querystring-es3/index.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.decode = exports.parse = __webpack_require__(/*! ./decode */ "./node_modules/querystring-es3/decode.js");
exports.encode = exports.stringify = __webpack_require__(/*! ./encode */ "./node_modules/querystring-es3/encode.js");


/***/ }),

/***/ "./node_modules/url/url.js":
/*!*********************************!*\
  !*** ./node_modules/url/url.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



var punycode = __webpack_require__(/*! punycode */ "./node_modules/punycode/punycode.js");
var util = __webpack_require__(/*! ./util */ "./node_modules/url/util.js");

exports.parse = urlParse;
exports.resolve = urlResolve;
exports.resolveObject = urlResolveObject;
exports.format = urlFormat;

exports.Url = Url;

function Url() {
  this.protocol = null;
  this.slashes = null;
  this.auth = null;
  this.host = null;
  this.port = null;
  this.hostname = null;
  this.hash = null;
  this.search = null;
  this.query = null;
  this.pathname = null;
  this.path = null;
  this.href = null;
}

// Reference: RFC 3986, RFC 1808, RFC 2396

// define these here so at least they only have to be
// compiled once on the first module load.
var protocolPattern = /^([a-z0-9.+-]+:)/i,
    portPattern = /:[0-9]*$/,

    // Special case for a simple path URL
    simplePathPattern = /^(\/\/?(?!\/)[^\?\s]*)(\?[^\s]*)?$/,

    // RFC 2396: characters reserved for delimiting URLs.
    // We actually just auto-escape these.
    delims = ['<', '>', '"', '`', ' ', '\r', '\n', '\t'],

    // RFC 2396: characters not allowed for various reasons.
    unwise = ['{', '}', '|', '\\', '^', '`'].concat(delims),

    // Allowed by RFCs, but cause of XSS attacks.  Always escape these.
    autoEscape = ['\''].concat(unwise),
    // Characters that are never ever allowed in a hostname.
    // Note that any invalid chars are also handled, but these
    // are the ones that are *expected* to be seen, so we fast-path
    // them.
    nonHostChars = ['%', '/', '?', ';', '#'].concat(autoEscape),
    hostEndingChars = ['/', '?', '#'],
    hostnameMaxLen = 255,
    hostnamePartPattern = /^[+a-z0-9A-Z_-]{0,63}$/,
    hostnamePartStart = /^([+a-z0-9A-Z_-]{0,63})(.*)$/,
    // protocols that can allow "unsafe" and "unwise" chars.
    unsafeProtocol = {
      'javascript': true,
      'javascript:': true
    },
    // protocols that never have a hostname.
    hostlessProtocol = {
      'javascript': true,
      'javascript:': true
    },
    // protocols that always contain a // bit.
    slashedProtocol = {
      'http': true,
      'https': true,
      'ftp': true,
      'gopher': true,
      'file': true,
      'http:': true,
      'https:': true,
      'ftp:': true,
      'gopher:': true,
      'file:': true
    },
    querystring = __webpack_require__(/*! querystring */ "./node_modules/querystring-es3/index.js");

function urlParse(url, parseQueryString, slashesDenoteHost) {
  if (url && util.isObject(url) && url instanceof Url) return url;

  var u = new Url;
  u.parse(url, parseQueryString, slashesDenoteHost);
  return u;
}

Url.prototype.parse = function(url, parseQueryString, slashesDenoteHost) {
  if (!util.isString(url)) {
    throw new TypeError("Parameter 'url' must be a string, not " + typeof url);
  }

  // Copy chrome, IE, opera backslash-handling behavior.
  // Back slashes before the query string get converted to forward slashes
  // See: https://code.google.com/p/chromium/issues/detail?id=25916
  var queryIndex = url.indexOf('?'),
      splitter =
          (queryIndex !== -1 && queryIndex < url.indexOf('#')) ? '?' : '#',
      uSplit = url.split(splitter),
      slashRegex = /\\/g;
  uSplit[0] = uSplit[0].replace(slashRegex, '/');
  url = uSplit.join(splitter);

  var rest = url;

  // trim before proceeding.
  // This is to support parse stuff like "  http://foo.com  \n"
  rest = rest.trim();

  if (!slashesDenoteHost && url.split('#').length === 1) {
    // Try fast path regexp
    var simplePath = simplePathPattern.exec(rest);
    if (simplePath) {
      this.path = rest;
      this.href = rest;
      this.pathname = simplePath[1];
      if (simplePath[2]) {
        this.search = simplePath[2];
        if (parseQueryString) {
          this.query = querystring.parse(this.search.substr(1));
        } else {
          this.query = this.search.substr(1);
        }
      } else if (parseQueryString) {
        this.search = '';
        this.query = {};
      }
      return this;
    }
  }

  var proto = protocolPattern.exec(rest);
  if (proto) {
    proto = proto[0];
    var lowerProto = proto.toLowerCase();
    this.protocol = lowerProto;
    rest = rest.substr(proto.length);
  }

  // figure out if it's got a host
  // user@server is *always* interpreted as a hostname, and url
  // resolution will treat //foo/bar as host=foo,path=bar because that's
  // how the browser resolves relative URLs.
  if (slashesDenoteHost || proto || rest.match(/^\/\/[^@\/]+@[^@\/]+/)) {
    var slashes = rest.substr(0, 2) === '//';
    if (slashes && !(proto && hostlessProtocol[proto])) {
      rest = rest.substr(2);
      this.slashes = true;
    }
  }

  if (!hostlessProtocol[proto] &&
      (slashes || (proto && !slashedProtocol[proto]))) {

    // there's a hostname.
    // the first instance of /, ?, ;, or # ends the host.
    //
    // If there is an @ in the hostname, then non-host chars *are* allowed
    // to the left of the last @ sign, unless some host-ending character
    // comes *before* the @-sign.
    // URLs are obnoxious.
    //
    // ex:
    // http://a@b@c/ => user:a@b host:c
    // http://a@b?@c => user:a host:c path:/?@c

    // v0.12 TODO(isaacs): This is not quite how Chrome does things.
    // Review our test case against browsers more comprehensively.

    // find the first instance of any hostEndingChars
    var hostEnd = -1;
    for (var i = 0; i < hostEndingChars.length; i++) {
      var hec = rest.indexOf(hostEndingChars[i]);
      if (hec !== -1 && (hostEnd === -1 || hec < hostEnd))
        hostEnd = hec;
    }

    // at this point, either we have an explicit point where the
    // auth portion cannot go past, or the last @ char is the decider.
    var auth, atSign;
    if (hostEnd === -1) {
      // atSign can be anywhere.
      atSign = rest.lastIndexOf('@');
    } else {
      // atSign must be in auth portion.
      // http://a@b/c@d => host:b auth:a path:/c@d
      atSign = rest.lastIndexOf('@', hostEnd);
    }

    // Now we have a portion which is definitely the auth.
    // Pull that off.
    if (atSign !== -1) {
      auth = rest.slice(0, atSign);
      rest = rest.slice(atSign + 1);
      this.auth = decodeURIComponent(auth);
    }

    // the host is the remaining to the left of the first non-host char
    hostEnd = -1;
    for (var i = 0; i < nonHostChars.length; i++) {
      var hec = rest.indexOf(nonHostChars[i]);
      if (hec !== -1 && (hostEnd === -1 || hec < hostEnd))
        hostEnd = hec;
    }
    // if we still have not hit it, then the entire thing is a host.
    if (hostEnd === -1)
      hostEnd = rest.length;

    this.host = rest.slice(0, hostEnd);
    rest = rest.slice(hostEnd);

    // pull out port.
    this.parseHost();

    // we've indicated that there is a hostname,
    // so even if it's empty, it has to be present.
    this.hostname = this.hostname || '';

    // if hostname begins with [ and ends with ]
    // assume that it's an IPv6 address.
    var ipv6Hostname = this.hostname[0] === '[' &&
        this.hostname[this.hostname.length - 1] === ']';

    // validate a little.
    if (!ipv6Hostname) {
      var hostparts = this.hostname.split(/\./);
      for (var i = 0, l = hostparts.length; i < l; i++) {
        var part = hostparts[i];
        if (!part) continue;
        if (!part.match(hostnamePartPattern)) {
          var newpart = '';
          for (var j = 0, k = part.length; j < k; j++) {
            if (part.charCodeAt(j) > 127) {
              // we replace non-ASCII char with a temporary placeholder
              // we need this to make sure size of hostname is not
              // broken by replacing non-ASCII by nothing
              newpart += 'x';
            } else {
              newpart += part[j];
            }
          }
          // we test again with ASCII char only
          if (!newpart.match(hostnamePartPattern)) {
            var validParts = hostparts.slice(0, i);
            var notHost = hostparts.slice(i + 1);
            var bit = part.match(hostnamePartStart);
            if (bit) {
              validParts.push(bit[1]);
              notHost.unshift(bit[2]);
            }
            if (notHost.length) {
              rest = '/' + notHost.join('.') + rest;
            }
            this.hostname = validParts.join('.');
            break;
          }
        }
      }
    }

    if (this.hostname.length > hostnameMaxLen) {
      this.hostname = '';
    } else {
      // hostnames are always lower case.
      this.hostname = this.hostname.toLowerCase();
    }

    if (!ipv6Hostname) {
      // IDNA Support: Returns a punycoded representation of "domain".
      // It only converts parts of the domain name that
      // have non-ASCII characters, i.e. it doesn't matter if
      // you call it with a domain that already is ASCII-only.
      this.hostname = punycode.toASCII(this.hostname);
    }

    var p = this.port ? ':' + this.port : '';
    var h = this.hostname || '';
    this.host = h + p;
    this.href += this.host;

    // strip [ and ] from the hostname
    // the host field still retains them, though
    if (ipv6Hostname) {
      this.hostname = this.hostname.substr(1, this.hostname.length - 2);
      if (rest[0] !== '/') {
        rest = '/' + rest;
      }
    }
  }

  // now rest is set to the post-host stuff.
  // chop off any delim chars.
  if (!unsafeProtocol[lowerProto]) {

    // First, make 100% sure that any "autoEscape" chars get
    // escaped, even if encodeURIComponent doesn't think they
    // need to be.
    for (var i = 0, l = autoEscape.length; i < l; i++) {
      var ae = autoEscape[i];
      if (rest.indexOf(ae) === -1)
        continue;
      var esc = encodeURIComponent(ae);
      if (esc === ae) {
        esc = escape(ae);
      }
      rest = rest.split(ae).join(esc);
    }
  }


  // chop off from the tail first.
  var hash = rest.indexOf('#');
  if (hash !== -1) {
    // got a fragment string.
    this.hash = rest.substr(hash);
    rest = rest.slice(0, hash);
  }
  var qm = rest.indexOf('?');
  if (qm !== -1) {
    this.search = rest.substr(qm);
    this.query = rest.substr(qm + 1);
    if (parseQueryString) {
      this.query = querystring.parse(this.query);
    }
    rest = rest.slice(0, qm);
  } else if (parseQueryString) {
    // no query string, but parseQueryString still requested
    this.search = '';
    this.query = {};
  }
  if (rest) this.pathname = rest;
  if (slashedProtocol[lowerProto] &&
      this.hostname && !this.pathname) {
    this.pathname = '/';
  }

  //to support http.request
  if (this.pathname || this.search) {
    var p = this.pathname || '';
    var s = this.search || '';
    this.path = p + s;
  }

  // finally, reconstruct the href based on what has been validated.
  this.href = this.format();
  return this;
};

// format a parsed object into a url string
function urlFormat(obj) {
  // ensure it's an object, and not a string url.
  // If it's an obj, this is a no-op.
  // this way, you can call url_format() on strings
  // to clean up potentially wonky urls.
  if (util.isString(obj)) obj = urlParse(obj);
  if (!(obj instanceof Url)) return Url.prototype.format.call(obj);
  return obj.format();
}

Url.prototype.format = function() {
  var auth = this.auth || '';
  if (auth) {
    auth = encodeURIComponent(auth);
    auth = auth.replace(/%3A/i, ':');
    auth += '@';
  }

  var protocol = this.protocol || '',
      pathname = this.pathname || '',
      hash = this.hash || '',
      host = false,
      query = '';

  if (this.host) {
    host = auth + this.host;
  } else if (this.hostname) {
    host = auth + (this.hostname.indexOf(':') === -1 ?
        this.hostname :
        '[' + this.hostname + ']');
    if (this.port) {
      host += ':' + this.port;
    }
  }

  if (this.query &&
      util.isObject(this.query) &&
      Object.keys(this.query).length) {
    query = querystring.stringify(this.query);
  }

  var search = this.search || (query && ('?' + query)) || '';

  if (protocol && protocol.substr(-1) !== ':') protocol += ':';

  // only the slashedProtocols get the //.  Not mailto:, xmpp:, etc.
  // unless they had them to begin with.
  if (this.slashes ||
      (!protocol || slashedProtocol[protocol]) && host !== false) {
    host = '//' + (host || '');
    if (pathname && pathname.charAt(0) !== '/') pathname = '/' + pathname;
  } else if (!host) {
    host = '';
  }

  if (hash && hash.charAt(0) !== '#') hash = '#' + hash;
  if (search && search.charAt(0) !== '?') search = '?' + search;

  pathname = pathname.replace(/[?#]/g, function(match) {
    return encodeURIComponent(match);
  });
  search = search.replace('#', '%23');

  return protocol + host + pathname + search + hash;
};

function urlResolve(source, relative) {
  return urlParse(source, false, true).resolve(relative);
}

Url.prototype.resolve = function(relative) {
  return this.resolveObject(urlParse(relative, false, true)).format();
};

function urlResolveObject(source, relative) {
  if (!source) return relative;
  return urlParse(source, false, true).resolveObject(relative);
}

Url.prototype.resolveObject = function(relative) {
  if (util.isString(relative)) {
    var rel = new Url();
    rel.parse(relative, false, true);
    relative = rel;
  }

  var result = new Url();
  var tkeys = Object.keys(this);
  for (var tk = 0; tk < tkeys.length; tk++) {
    var tkey = tkeys[tk];
    result[tkey] = this[tkey];
  }

  // hash is always overridden, no matter what.
  // even href="" will remove it.
  result.hash = relative.hash;

  // if the relative url is empty, then there's nothing left to do here.
  if (relative.href === '') {
    result.href = result.format();
    return result;
  }

  // hrefs like //foo/bar always cut to the protocol.
  if (relative.slashes && !relative.protocol) {
    // take everything except the protocol from relative
    var rkeys = Object.keys(relative);
    for (var rk = 0; rk < rkeys.length; rk++) {
      var rkey = rkeys[rk];
      if (rkey !== 'protocol')
        result[rkey] = relative[rkey];
    }

    //urlParse appends trailing / to urls like http://www.example.com
    if (slashedProtocol[result.protocol] &&
        result.hostname && !result.pathname) {
      result.path = result.pathname = '/';
    }

    result.href = result.format();
    return result;
  }

  if (relative.protocol && relative.protocol !== result.protocol) {
    // if it's a known url protocol, then changing
    // the protocol does weird things
    // first, if it's not file:, then we MUST have a host,
    // and if there was a path
    // to begin with, then we MUST have a path.
    // if it is file:, then the host is dropped,
    // because that's known to be hostless.
    // anything else is assumed to be absolute.
    if (!slashedProtocol[relative.protocol]) {
      var keys = Object.keys(relative);
      for (var v = 0; v < keys.length; v++) {
        var k = keys[v];
        result[k] = relative[k];
      }
      result.href = result.format();
      return result;
    }

    result.protocol = relative.protocol;
    if (!relative.host && !hostlessProtocol[relative.protocol]) {
      var relPath = (relative.pathname || '').split('/');
      while (relPath.length && !(relative.host = relPath.shift()));
      if (!relative.host) relative.host = '';
      if (!relative.hostname) relative.hostname = '';
      if (relPath[0] !== '') relPath.unshift('');
      if (relPath.length < 2) relPath.unshift('');
      result.pathname = relPath.join('/');
    } else {
      result.pathname = relative.pathname;
    }
    result.search = relative.search;
    result.query = relative.query;
    result.host = relative.host || '';
    result.auth = relative.auth;
    result.hostname = relative.hostname || relative.host;
    result.port = relative.port;
    // to support http.request
    if (result.pathname || result.search) {
      var p = result.pathname || '';
      var s = result.search || '';
      result.path = p + s;
    }
    result.slashes = result.slashes || relative.slashes;
    result.href = result.format();
    return result;
  }

  var isSourceAbs = (result.pathname && result.pathname.charAt(0) === '/'),
      isRelAbs = (
          relative.host ||
          relative.pathname && relative.pathname.charAt(0) === '/'
      ),
      mustEndAbs = (isRelAbs || isSourceAbs ||
                    (result.host && relative.pathname)),
      removeAllDots = mustEndAbs,
      srcPath = result.pathname && result.pathname.split('/') || [],
      relPath = relative.pathname && relative.pathname.split('/') || [],
      psychotic = result.protocol && !slashedProtocol[result.protocol];

  // if the url is a non-slashed url, then relative
  // links like ../.. should be able
  // to crawl up to the hostname, as well.  This is strange.
  // result.protocol has already been set by now.
  // Later on, put the first path part into the host field.
  if (psychotic) {
    result.hostname = '';
    result.port = null;
    if (result.host) {
      if (srcPath[0] === '') srcPath[0] = result.host;
      else srcPath.unshift(result.host);
    }
    result.host = '';
    if (relative.protocol) {
      relative.hostname = null;
      relative.port = null;
      if (relative.host) {
        if (relPath[0] === '') relPath[0] = relative.host;
        else relPath.unshift(relative.host);
      }
      relative.host = null;
    }
    mustEndAbs = mustEndAbs && (relPath[0] === '' || srcPath[0] === '');
  }

  if (isRelAbs) {
    // it's absolute.
    result.host = (relative.host || relative.host === '') ?
                  relative.host : result.host;
    result.hostname = (relative.hostname || relative.hostname === '') ?
                      relative.hostname : result.hostname;
    result.search = relative.search;
    result.query = relative.query;
    srcPath = relPath;
    // fall through to the dot-handling below.
  } else if (relPath.length) {
    // it's relative
    // throw away the existing file, and take the new path instead.
    if (!srcPath) srcPath = [];
    srcPath.pop();
    srcPath = srcPath.concat(relPath);
    result.search = relative.search;
    result.query = relative.query;
  } else if (!util.isNullOrUndefined(relative.search)) {
    // just pull out the search.
    // like href='?foo'.
    // Put this after the other two cases because it simplifies the booleans
    if (psychotic) {
      result.hostname = result.host = srcPath.shift();
      //occationaly the auth can get stuck only in host
      //this especially happens in cases like
      //url.resolveObject('mailto:local1@domain1', 'local2@domain2')
      var authInHost = result.host && result.host.indexOf('@') > 0 ?
                       result.host.split('@') : false;
      if (authInHost) {
        result.auth = authInHost.shift();
        result.host = result.hostname = authInHost.shift();
      }
    }
    result.search = relative.search;
    result.query = relative.query;
    //to support http.request
    if (!util.isNull(result.pathname) || !util.isNull(result.search)) {
      result.path = (result.pathname ? result.pathname : '') +
                    (result.search ? result.search : '');
    }
    result.href = result.format();
    return result;
  }

  if (!srcPath.length) {
    // no path at all.  easy.
    // we've already handled the other stuff above.
    result.pathname = null;
    //to support http.request
    if (result.search) {
      result.path = '/' + result.search;
    } else {
      result.path = null;
    }
    result.href = result.format();
    return result;
  }

  // if a url ENDs in . or .., then it must get a trailing slash.
  // however, if it ends in anything else non-slashy,
  // then it must NOT get a trailing slash.
  var last = srcPath.slice(-1)[0];
  var hasTrailingSlash = (
      (result.host || relative.host || srcPath.length > 1) &&
      (last === '.' || last === '..') || last === '');

  // strip single dots, resolve double dots to parent dir
  // if the path tries to go above the root, `up` ends up > 0
  var up = 0;
  for (var i = srcPath.length; i >= 0; i--) {
    last = srcPath[i];
    if (last === '.') {
      srcPath.splice(i, 1);
    } else if (last === '..') {
      srcPath.splice(i, 1);
      up++;
    } else if (up) {
      srcPath.splice(i, 1);
      up--;
    }
  }

  // if the path is allowed to go above the root, restore leading ..s
  if (!mustEndAbs && !removeAllDots) {
    for (; up--; up) {
      srcPath.unshift('..');
    }
  }

  if (mustEndAbs && srcPath[0] !== '' &&
      (!srcPath[0] || srcPath[0].charAt(0) !== '/')) {
    srcPath.unshift('');
  }

  if (hasTrailingSlash && (srcPath.join('/').substr(-1) !== '/')) {
    srcPath.push('');
  }

  var isAbsolute = srcPath[0] === '' ||
      (srcPath[0] && srcPath[0].charAt(0) === '/');

  // put the host back
  if (psychotic) {
    result.hostname = result.host = isAbsolute ? '' :
                                    srcPath.length ? srcPath.shift() : '';
    //occationaly the auth can get stuck only in host
    //this especially happens in cases like
    //url.resolveObject('mailto:local1@domain1', 'local2@domain2')
    var authInHost = result.host && result.host.indexOf('@') > 0 ?
                     result.host.split('@') : false;
    if (authInHost) {
      result.auth = authInHost.shift();
      result.host = result.hostname = authInHost.shift();
    }
  }

  mustEndAbs = mustEndAbs || (result.host && srcPath.length);

  if (mustEndAbs && !isAbsolute) {
    srcPath.unshift('');
  }

  if (!srcPath.length) {
    result.pathname = null;
    result.path = null;
  } else {
    result.pathname = srcPath.join('/');
  }

  //to support request.http
  if (!util.isNull(result.pathname) || !util.isNull(result.search)) {
    result.path = (result.pathname ? result.pathname : '') +
                  (result.search ? result.search : '');
  }
  result.auth = relative.auth || result.auth;
  result.slashes = result.slashes || relative.slashes;
  result.href = result.format();
  return result;
};

Url.prototype.parseHost = function() {
  var host = this.host;
  var port = portPattern.exec(host);
  if (port) {
    port = port[0];
    if (port !== ':') {
      this.port = port.substr(1);
    }
    host = host.substr(0, host.length - port.length);
  }
  if (host) this.hostname = host;
};


/***/ }),

/***/ "./node_modules/url/util.js":
/*!**********************************!*\
  !*** ./node_modules/url/util.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = {
  isString: function(arg) {
    return typeof(arg) === 'string';
  },
  isObject: function(arg) {
    return typeof(arg) === 'object' && arg !== null;
  },
  isNull: function(arg) {
    return arg === null;
  },
  isNullOrUndefined: function(arg) {
    return arg == null;
  }
};


/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ "./node_modules/webpack/buildin/module.js":
/*!***********************************!*\
  !*** (webpack)/buildin/module.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function(module) {
	if (!module.webpackPolyfill) {
		module.deprecate = function() {};
		module.paths = [];
		// module.parent = undefined by default
		if (!module.children) module.children = [];
		Object.defineProperty(module, "loaded", {
			enumerable: true,
			get: function() {
				return module.l;
			}
		});
		Object.defineProperty(module, "id", {
			enumerable: true,
			get: function() {
				return module.i;
			}
		});
		module.webpackPolyfill = 1;
	}
	return module;
};


/***/ }),

/***/ "@wordpress/autop":
/*!****************************************!*\
  !*** external {"this":["wp","autop"]} ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["autop"]; }());

/***/ }),

/***/ "@wordpress/blob":
/*!***************************************!*\
  !*** external {"this":["wp","blob"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blob"]; }());

/***/ }),

/***/ "@wordpress/blocks":
/*!*****************************************!*\
  !*** external {"this":["wp","blocks"]} ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/compose":
/*!******************************************!*\
  !*** external {"this":["wp","compose"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ "@wordpress/core-data":
/*!*******************************************!*\
  !*** external {"this":["wp","coreData"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["coreData"]; }());

/***/ }),

/***/ "@wordpress/data":
/*!***************************************!*\
  !*** external {"this":["wp","data"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ "@wordpress/deprecated":
/*!*********************************************!*\
  !*** external {"this":["wp","deprecated"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["deprecated"]; }());

/***/ }),

/***/ "@wordpress/editor":
/*!*****************************************!*\
  !*** external {"this":["wp","editor"]} ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["editor"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/html-entities":
/*!***********************************************!*\
  !*** external {"this":["wp","htmlEntities"]} ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["htmlEntities"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ "@wordpress/keycodes":
/*!*******************************************!*\
  !*** external {"this":["wp","keycodes"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),

/***/ "@wordpress/rich-text":
/*!*******************************************!*\
  !*** external {"this":["wp","richText"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["richText"]; }());

/***/ }),

/***/ "@wordpress/viewport":
/*!*******************************************!*\
  !*** external {"this":["wp","viewport"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["viewport"]; }());

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ "moment":
/*!*************************!*\
  !*** external "moment" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["moment"]; }());

/***/ })

/******/ });
//# sourceMappingURL=block-library.js.map