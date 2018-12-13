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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/edit-post/build-module/index.js");
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

/***/ "./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-group.js":
/*!***************************************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-group.js ***!
  \***************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





var _createSlotFill = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["createSlotFill"])('PluginBlockSettingsMenuGroup'),
    PluginBlockSettingsMenuGroup = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;

var PluginBlockSettingsMenuGroupSlot = function PluginBlockSettingsMenuGroupSlot(_ref) {
  var fillProps = _ref.fillProps,
      selectedBlocks = _ref.selectedBlocks;
  selectedBlocks = Object(lodash__WEBPACK_IMPORTED_MODULE_2__["map"])(selectedBlocks, function (block) {
    return block.name;
  });
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(Slot, {
    fillProps: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, fillProps, {
      selectedBlocks: selectedBlocks
    })
  }, function (fills) {
    return !Object(lodash__WEBPACK_IMPORTED_MODULE_2__["isEmpty"])(fills) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "editor-block-settings-menu__separator"
    }), fills);
  });
};

PluginBlockSettingsMenuGroup.Slot = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withSelect"])(function (select, _ref2) {
  var clientIds = _ref2.fillProps.clientIds;
  return {
    selectedBlocks: select('core/editor').getBlocksByClientId(clientIds)
  };
})(PluginBlockSettingsMenuGroupSlot);
/* harmony default export */ __webpack_exports__["default"] = (PluginBlockSettingsMenuGroup);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-item.js":
/*!**************************************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-item.js ***!
  \**************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _plugin_block_settings_menu_group__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./plugin-block-settings-menu-group */ "./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-group.js");


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



var isEverySelectedBlockAllowed = function isEverySelectedBlockAllowed(selected, allowed) {
  return Object(lodash__WEBPACK_IMPORTED_MODULE_1__["difference"])(selected, allowed).length === 0;
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
  return !Array.isArray(allowedBlockNames) || isEverySelectedBlockAllowed(selectedBlockNames, allowedBlockNames);
};

var PluginBlockSettingsMenuItem = function PluginBlockSettingsMenuItem(_ref) {
  var allowedBlocks = _ref.allowedBlocks,
      icon = _ref.icon,
      label = _ref.label,
      onClick = _ref.onClick,
      small = _ref.small,
      role = _ref.role;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_plugin_block_settings_menu_group__WEBPACK_IMPORTED_MODULE_4__["default"], null, function (_ref2) {
    var selectedBlocks = _ref2.selectedBlocks,
        onClose = _ref2.onClose;

    if (!shouldRenderItem(selectedBlocks, allowedBlocks)) {
      return null;
    }

    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["IconButton"], {
      className: "editor-block-settings-menu__control",
      onClick: Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__["compose"])(onClick, onClose),
      icon: icon || 'admin-plugins',
      label: small ? label : undefined,
      role: role
    }, !small && label);
  });
};

/* harmony default export */ __webpack_exports__["default"] = (PluginBlockSettingsMenuItem);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/browser-url/index.js":
/*!****************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/browser-url/index.js ***!
  \****************************************************************************************/
/*! exports provided: getPostEditURL, getPostTrashedURL, BrowserURL, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getPostEditURL", function() { return getPostEditURL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getPostTrashedURL", function() { return getPostTrashedURL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "BrowserURL", function() { return BrowserURL; });
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_7__);






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
  return Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_7__["addQueryArgs"])('post.php', {
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
  return Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_7__["addQueryArgs"])('edit.php', {
    trashed: 1,
    post_type: postType,
    ids: postId
  });
}
var BrowserURL =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(BrowserURL, _Component);

  function BrowserURL() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, BrowserURL);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(BrowserURL).apply(this, arguments));
    _this.state = {
      historyId: null
    };
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(BrowserURL, [{
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
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["Component"]);
/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withSelect"])(function (select) {
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
})(BrowserURL));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/fullscreen-mode/index.js":
/*!********************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/fullscreen-mode/index.js ***!
  \********************************************************************************************/
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
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);






/**
 * WordPress dependencies
 */



var FullscreenMode =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(FullscreenMode, _Component);

  function FullscreenMode() {
    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, FullscreenMode);

    return Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(FullscreenMode).apply(this, arguments));
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(FullscreenMode, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.sync();
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
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withSelect"])(function (select) {
  return {
    isActive: select('core/edit-post').isFeatureActive('fullscreenMode')
  };
})(FullscreenMode));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/feature-toggle/index.js":
/*!**************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/feature-toggle/index.js ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);


/**
 * WordPress Dependencies
 */




function FeatureToggle(_ref) {
  var onToggle = _ref.onToggle,
      isActive = _ref.isActive,
      label = _ref.label;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["MenuItem"], {
    icon: isActive && 'yes',
    isSelected: isActive,
    onClick: onToggle,
    role: "menuitemcheckbox"
  }, label);
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["withSelect"])(function (select, _ref2) {
  var feature = _ref2.feature;
  return {
    isActive: select('core/edit-post').isFeatureActive(feature)
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["withDispatch"])(function (dispatch, ownProps) {
  return {
    onToggle: function onToggle() {
      dispatch('core/edit-post').toggleFeature(ownProps.feature);
      ownProps.onToggle();
    }
  };
})])(FeatureToggle));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/fullscreen-mode-close/index.js":
/*!*********************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/fullscreen-mode-close/index.js ***!
  \*********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_5__);


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

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Toolbar"], {
    className: "edit-post-fullscreen-mode-close__toolbar"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["IconButton"], {
    icon: "exit",
    href: Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_5__["addQueryArgs"])('edit.php', {
      post_type: postType.slug
    }),
    label: Object(lodash__WEBPACK_IMPORTED_MODULE_1__["get"])(postType, ['labels', 'view_items'], Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('View Posts'))
  }));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withSelect"])(function (select) {
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


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/header-toolbar/index.js":
/*!**************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/header-toolbar/index.js ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/viewport */ "@wordpress/viewport");
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_viewport__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_nux__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/nux */ "@wordpress/nux");
/* harmony import */ var _wordpress_nux__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_nux__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _fullscreen_mode_close__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../fullscreen-mode-close */ "./node_modules/@wordpress/edit-post/build-module/components/header/fullscreen-mode-close/index.js");


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function HeaderToolbar(_ref) {
  var hasFixedToolbar = _ref.hasFixedToolbar,
      isLargeViewport = _ref.isLargeViewport,
      mode = _ref.mode;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["NavigableToolbar"], {
    className: "edit-post-header-toolbar",
    "aria-label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Editor Toolbar')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_fullscreen_mode_close__WEBPACK_IMPORTED_MODULE_7__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["Inserter"], {
    disabled: mode !== 'visual',
    position: "bottom right"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_nux__WEBPACK_IMPORTED_MODULE_4__["DotTip"], {
    id: "core/editor.inserter"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Welcome to the wonderful world of blocks! Click the “+” (“Add block”) button to add a new block. There are blocks available for all kinds of content: you can insert text, headings, images, lists, and lots more!'))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["EditorHistoryUndo"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["EditorHistoryRedo"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["TableOfContents"], null), hasFixedToolbar && isLargeViewport && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "edit-post-header-toolbar__block-toolbar"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_6__["BlockToolbar"], null)));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withSelect"])(function (select) {
  return {
    hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
    mode: select('core/edit-post').getEditorMode()
  };
}), Object(_wordpress_viewport__WEBPACK_IMPORTED_MODULE_3__["withViewportMatch"])({
  isLargeViewport: 'medium'
})])(HeaderToolbar));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/index.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/index.js ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_nux__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/nux */ "@wordpress/nux");
/* harmony import */ var _wordpress_nux__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_nux__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _more_menu__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./more-menu */ "./node_modules/@wordpress/edit-post/build-module/components/header/more-menu/index.js");
/* harmony import */ var _header_toolbar__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./header-toolbar */ "./node_modules/@wordpress/edit-post/build-module/components/header/header-toolbar/index.js");
/* harmony import */ var _pinned_plugins__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./pinned-plugins */ "./node_modules/@wordpress/edit-post/build-module/components/header/pinned-plugins/index.js");
/* harmony import */ var _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../keyboard-shortcuts */ "./node_modules/@wordpress/edit-post/build-module/keyboard-shortcuts.js");


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */






function Header(_ref) {
  var isEditorSidebarOpened = _ref.isEditorSidebarOpened,
      openGeneralSidebar = _ref.openGeneralSidebar,
      closeGeneralSidebar = _ref.closeGeneralSidebar,
      isPublishSidebarOpened = _ref.isPublishSidebarOpened,
      isPublishSidebarEnabled = _ref.isPublishSidebarEnabled,
      togglePublishSidebar = _ref.togglePublishSidebar,
      hasActiveMetaboxes = _ref.hasActiveMetaboxes,
      isSaving = _ref.isSaving;
  var toggleGeneralSidebar = isEditorSidebarOpened ? closeGeneralSidebar : openGeneralSidebar;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    role: "region"
    /* translators: accessibility text for the top bar landmark region. */
    ,
    "aria-label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Editor top bar'),
    className: "edit-post-header",
    tabIndex: "-1"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_header_toolbar__WEBPACK_IMPORTED_MODULE_8__["default"], null), !isPublishSidebarOpened && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "edit-post-header__settings"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostSavedState"], {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostPreviewButton"], null), isPublishSidebarEnabled ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostPublishPanelToggle"], {
    isOpen: isPublishSidebarOpened,
    onToggle: togglePublishSidebar,
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving
  }) : Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostPublishButton"], {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["IconButton"], {
    icon: "admin-generic",
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Settings'),
    onClick: toggleGeneralSidebar,
    isToggled: isEditorSidebarOpened,
    "aria-expanded": isEditorSidebarOpened,
    shortcut: _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_10__["default"].toggleSidebar
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_nux__WEBPACK_IMPORTED_MODULE_6__["DotTip"], {
    id: "core/editor.settings"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('You’ll find more settings for your page and blocks in the sidebar. Click “Settings” to open it.'))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_pinned_plugins__WEBPACK_IMPORTED_MODULE_9__["default"].Slot, null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_more_menu__WEBPACK_IMPORTED_MODULE_7__["default"], null)));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withSelect"])(function (select) {
  return {
    isEditorSidebarOpened: select('core/edit-post').isEditorSidebarOpened(),
    isPublishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
    isPublishSidebarEnabled: select('core/editor').isPublishSidebarEnabled(),
    hasActiveMetaboxes: select('core/edit-post').hasMetaBoxes(),
    isSaving: select('core/edit-post').isSavingMetaBoxes(),
    hasBlockSelection: !!select('core/editor').getBlockSelectionStart()
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withDispatch"])(function (dispatch, _ref2) {
  var hasBlockSelection = _ref2.hasBlockSelection;

  var _dispatch = dispatch('core/edit-post'),
      _openGeneralSidebar = _dispatch.openGeneralSidebar,
      closeGeneralSidebar = _dispatch.closeGeneralSidebar,
      togglePublishSidebar = _dispatch.togglePublishSidebar;

  var sidebarToOpen = hasBlockSelection ? 'edit-post/block' : 'edit-post/document';
  return {
    openGeneralSidebar: function openGeneralSidebar() {
      return _openGeneralSidebar(sidebarToOpen);
    },
    closeGeneralSidebar: closeGeneralSidebar,
    togglePublishSidebar: togglePublishSidebar,
    hasBlockSelection: undefined
  };
}))(Header));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/mode-switcher/index.js":
/*!*************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/mode-switcher/index.js ***!
  \*************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../keyboard-shortcuts */ "./node_modules/@wordpress/edit-post/build-module/keyboard-shortcuts.js");



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
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Visual Editor')
}, {
  value: 'text',
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Code Editor')
}];

function ModeSwitcher(_ref) {
  var onSwitch = _ref.onSwitch,
      mode = _ref.mode;
  var choices = MODES.map(function (choice) {
    if (choice.value !== mode) {
      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, choice, {
        shortcut: _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_6__["default"].toggleEditorMode.display
      });
    }

    return choice;
  });
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["MenuGroup"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Editor')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["MenuItemsChoice"], {
    choices: choices,
    value: mode,
    onSelect: onSwitch
  }));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withSelect"])(function (select) {
  return {
    mode: select('core/edit-post').getEditorMode()
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withDispatch"])(function (dispatch, ownProps) {
  return {
    onSwitch: function onSwitch(mode) {
      dispatch('core/edit-post').switchEditorMode(mode);
      ownProps.onSelect(mode);
    }
  };
})])(ModeSwitcher));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/more-menu/index.js":
/*!*********************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/more-menu/index.js ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _mode_switcher__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../mode-switcher */ "./node_modules/@wordpress/edit-post/build-module/components/header/mode-switcher/index.js");
/* harmony import */ var _plugins_more_menu_group__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../plugins-more-menu-group */ "./node_modules/@wordpress/edit-post/build-module/components/header/plugins-more-menu-group/index.js");
/* harmony import */ var _tools_more_menu_group__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../tools-more-menu-group */ "./node_modules/@wordpress/edit-post/build-module/components/header/tools-more-menu-group/index.js");
/* harmony import */ var _writing_menu__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../writing-menu */ "./node_modules/@wordpress/edit-post/build-module/components/header/writing-menu/index.js");


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */






var MoreMenu = function MoreMenu() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Dropdown"], {
    className: "edit-post-more-menu",
    contentClassName: "edit-post-more-menu__content",
    position: "bottom left",
    renderToggle: function renderToggle(_ref) {
      var isOpen = _ref.isOpen,
          onToggle = _ref.onToggle;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["IconButton"], {
        icon: "ellipsis",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["_x"])('More', 'button to expand options'),
        onClick: onToggle,
        "aria-expanded": isOpen
      });
    },
    renderContent: function renderContent(_ref2) {
      var onClose = _ref2.onClose;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_writing_menu__WEBPACK_IMPORTED_MODULE_6__["default"], {
        onClose: onClose
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_mode_switcher__WEBPACK_IMPORTED_MODULE_3__["default"], {
        onSelect: onClose
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_plugins_more_menu_group__WEBPACK_IMPORTED_MODULE_4__["default"].Slot, {
        fillProps: {
          onClose: onClose
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_tools_more_menu_group__WEBPACK_IMPORTED_MODULE_5__["default"].Slot, {
        fillProps: {
          onClose: onClose
        }
      }));
    }
  });
};

/* harmony default export */ __webpack_exports__["default"] = (MoreMenu);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/pinned-plugins/index.js":
/*!**************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/pinned-plugins/index.js ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



var _createSlotFill = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["createSlotFill"])('PinnedPlugins'),
    PinnedPlugins = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;

PinnedPlugins.Slot = function (props) {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Slot, props, function (fills) {
    return !Object(lodash__WEBPACK_IMPORTED_MODULE_1__["isEmpty"])(fills) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "edit-post-pinned-plugins"
    }, fills);
  });
};

/* harmony default export */ __webpack_exports__["default"] = (PinnedPlugins);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/plugin-sidebar-more-menu-item/index.js":
/*!*****************************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-sidebar-more-menu-item/index.js ***!
  \*****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _plugins_more_menu_group__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../plugins-more-menu-group */ "./node_modules/@wordpress/edit-post/build-module/components/header/plugins-more-menu-group/index.js");


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var PluginSidebarMoreMenuItem = function PluginSidebarMoreMenuItem(_ref) {
  var children = _ref.children,
      icon = _ref.icon,
      isSelected = _ref.isSelected,
      onClick = _ref.onClick;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_plugins_more_menu_group__WEBPACK_IMPORTED_MODULE_5__["default"], null, function (fillProps) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["MenuItem"], {
      icon: isSelected ? 'yes' : icon,
      isSelected: isSelected,
      role: "menuitemcheckbox",
      onClick: Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__["compose"])(onClick, fillProps.onClose)
    }, children);
  });
};

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__["compose"])(Object(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon,
    sidebarName: "".concat(context.name, "/").concat(ownProps.target)
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withSelect"])(function (select, _ref2) {
  var sidebarName = _ref2.sidebarName;

  var _select = select('core/edit-post'),
      getActiveGeneralSidebarName = _select.getActiveGeneralSidebarName;

  return {
    isSelected: getActiveGeneralSidebarName() === sidebarName
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withDispatch"])(function (dispatch, _ref3) {
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
}))(PluginSidebarMoreMenuItem));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/plugins-more-menu-group/index.js":
/*!***********************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/plugins-more-menu-group/index.js ***!
  \***********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




var _createSlotFill = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["createSlotFill"])('PluginsMoreMenuGroup'),
    PluginsMoreMenuGroup = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;

PluginsMoreMenuGroup.Slot = function (_ref) {
  var fillProps = _ref.fillProps;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Slot, {
    fillProps: fillProps
  }, function (fills) {
    return !Object(lodash__WEBPACK_IMPORTED_MODULE_1__["isEmpty"])(fills) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["MenuGroup"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Plugins')
    }, fills);
  });
};

/* harmony default export */ __webpack_exports__["default"] = (PluginsMoreMenuGroup);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/tools-more-menu-group/index.js":
/*!*********************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/tools-more-menu-group/index.js ***!
  \*********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




var _createSlotFill = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["createSlotFill"])('ToolsMoreMenuGroup'),
    ToolsMoreMenuGroup = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;

ToolsMoreMenuGroup.Slot = function (_ref) {
  var fillProps = _ref.fillProps;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Slot, {
    fillProps: fillProps
  }, function (fills) {
    return !Object(lodash__WEBPACK_IMPORTED_MODULE_1__["isEmpty"])(fills) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["MenuGroup"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Tools')
    }, fills);
  });
};

/* harmony default export */ __webpack_exports__["default"] = (ToolsMoreMenuGroup);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/header/writing-menu/index.js":
/*!************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/header/writing-menu/index.js ***!
  \************************************************************************************************/
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
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/viewport */ "@wordpress/viewport");
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_viewport__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _feature_toggle__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../feature-toggle */ "./node_modules/@wordpress/edit-post/build-module/components/header/feature-toggle/index.js");


/**
 * WordPress Dependencies
 */



/**
 * Internal dependencies
 */



function WritingMenu(_ref) {
  var onClose = _ref.onClose;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["MenuGroup"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('View')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_feature_toggle__WEBPACK_IMPORTED_MODULE_4__["default"], {
    feature: "fixedToolbar",
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Unified Toolbar'),
    onToggle: onClose
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_feature_toggle__WEBPACK_IMPORTED_MODULE_4__["default"], {
    feature: "focusMode",
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Spotlight Mode'),
    onToggle: onClose
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_feature_toggle__WEBPACK_IMPORTED_MODULE_4__["default"], {
    feature: "fullscreenMode",
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Fullscreen Mode'),
    onToggle: onClose
  }));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_viewport__WEBPACK_IMPORTED_MODULE_3__["ifViewportMatches"])('medium')(WritingMenu));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/config.js":
/*!**********************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/config.js ***!
  \**********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/**
 * WordPress dependencies
 */


var primary = _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcutList"].primary,
    primaryShift = _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcutList"].primaryShift,
    primaryAlt = _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcutList"].primaryAlt,
    secondary = _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcutList"].secondary,
    access = _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcutList"].access,
    ctrl = _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcutList"].ctrl,
    ctrlShift = _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcutList"].ctrlShift,
    shiftAlt = _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcutList"].shiftAlt;
var globalShortcuts = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Global shortcuts'),
  shortcuts: [{
    keyCombination: access('h'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Display this help.')
  }, {
    keyCombination: primary('s'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Save your changes.')
  }, {
    keyCombination: primary('z'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Undo your last changes.')
  }, {
    keyCombination: primaryShift('z'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Redo your last undo.')
  }, {
    keyCombination: primaryShift(','),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Show or hide the settings sidebar.'),
    ariaLabel: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["shortcutAriaLabel"].primaryShift(',')
  }, {
    keyCombination: ctrl('`'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Navigate to the next part of the editor.'),
    ariaLabel: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["shortcutAriaLabel"].ctrl('`')
  }, {
    keyCombination: ctrlShift('`'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Navigate to the previous part of the editor.'),
    ariaLabel: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["shortcutAriaLabel"].ctrlShift('`')
  }, {
    keyCombination: shiftAlt('n'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Navigate to the next part of the editor (alternative).')
  }, {
    keyCombination: shiftAlt('p'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Navigate to the previous part of the editor (alternative).')
  }, {
    keyCombination: secondary('m'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Switch between Visual Editor and Code Editor.')
  }]
};
var selectionShortcuts = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Selection shortcuts'),
  shortcuts: [{
    keyCombination: primary('a'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Select all text when typing. Press again to select all blocks.')
  }, {
    keyCombination: 'Esc',
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Clear selection.'),

    /* translators: The 'escape' key on a keyboard. */
    ariaLabel: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Escape')
  }]
};
var blockShortcuts = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Block shortcuts'),
  shortcuts: [{
    keyCombination: primaryShift('d'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Duplicate the selected block(s).')
  }, {
    keyCombination: access('z'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Remove the selected block(s).')
  }, {
    keyCombination: primaryAlt('t'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Insert a new block before the selected block(s).')
  }, {
    keyCombination: primaryAlt('y'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Insert a new block after the selected block(s).')
  }, {
    keyCombination: '/',
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Change the block type after adding a new paragraph.'),

    /* translators: The forward-slash character. e.g. '/'. */
    ariaLabel: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Forward-slash')
  }]
};
var textFormattingShortcuts = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Text formatting'),
  shortcuts: [{
    keyCombination: primary('b'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Make the selected text bold.')
  }, {
    keyCombination: primary('i'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Make the selected text italic.')
  }, {
    keyCombination: primary('u'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Underline the selected text.')
  }, {
    keyCombination: primary('k'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Convert the selected text into a link.')
  }, {
    keyCombination: access('s'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Remove a link.')
  }, {
    keyCombination: access('d'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Add a strikethrough to the selected text.')
  }, {
    keyCombination: access('x'),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Display the selected text in a monospaced font.')
  }]
};
/* harmony default export */ __webpack_exports__["default"] = ([globalShortcuts, selectionShortcuts, blockShortcuts, textFormattingShortcuts]);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/index.js":
/*!*********************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/index.js ***!
  \*********************************************************************************************************/
/*! exports provided: KeyboardShortcutHelpModal, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "KeyboardShortcutHelpModal", function() { return KeyboardShortcutHelpModal; });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _config__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./config */ "./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/config.js");




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

var mapKeyCombination = function mapKeyCombination(keyCombination) {
  return keyCombination.map(function (character, index) {
    if (character === '+') {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], {
        key: index
      }, character);
    }

    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("kbd", {
      key: index,
      className: "edit-post-keyboard-shortcut-help__shortcut-key"
    }, character);
  });
};

var ShortcutList = function ShortcutList(_ref) {
  var shortcuts = _ref.shortcuts;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("dl", {
    className: "edit-post-keyboard-shortcut-help__shortcut-list"
  }, shortcuts.map(function (_ref2, index) {
    var keyCombination = _ref2.keyCombination,
        description = _ref2.description,
        ariaLabel = _ref2.ariaLabel;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
      className: "edit-post-keyboard-shortcut-help__shortcut",
      key: index
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("dt", {
      className: "edit-post-keyboard-shortcut-help__shortcut-term"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("kbd", {
      className: "edit-post-keyboard-shortcut-help__shortcut-key-combination",
      "aria-label": ariaLabel
    }, mapKeyCombination(Object(lodash__WEBPACK_IMPORTED_MODULE_3__["castArray"])(keyCombination)))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("dd", {
      className: "edit-post-keyboard-shortcut-help__shortcut-description"
    }, description));
  }));
};

var ShortcutSection = function ShortcutSection(_ref3) {
  var title = _ref3.title,
      shortcuts = _ref3.shortcuts;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("section", {
    className: "edit-post-keyboard-shortcut-help__section"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("h2", {
    className: "edit-post-keyboard-shortcut-help__section-title"
  }, title), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(ShortcutList, {
    shortcuts: shortcuts
  }));
};

function KeyboardShortcutHelpModal(_ref4) {
  var isModalActive = _ref4.isModalActive,
      toggleModal = _ref4.toggleModal;
  var title = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("span", {
    className: "edit-post-keyboard-shortcut-help__title"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Keyboard Shortcuts'));
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["KeyboardShortcuts"], {
    bindGlobal: true,
    shortcuts: Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__["rawShortcut"].access('h'), toggleModal)
  }), isModalActive && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Modal"], {
    className: "edit-post-keyboard-shortcut-help",
    title: title,
    closeLabel: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Close'),
    onRequestClose: toggleModal
  }, _config__WEBPACK_IMPORTED_MODULE_9__["default"].map(function (config, index) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(ShortcutSection, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      key: index
    }, config));
  })));
}
/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_8__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_7__["withSelect"])(function (select) {
  return {
    isModalActive: select('core/edit-post').isModalActive(MODAL_NAME)
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_7__["withDispatch"])(function (dispatch, _ref6) {
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


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js":
/*!***********************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js ***!
  \***********************************************************************************************/
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
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../keyboard-shortcuts */ "./node_modules/@wordpress/edit-post/build-module/keyboard-shortcuts.js");









/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var EditorModeKeyboardShortcuts =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__["default"])(EditorModeKeyboardShortcuts, _Component);

  function EditorModeKeyboardShortcuts() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__["default"])(this, EditorModeKeyboardShortcuts);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__["default"])(EditorModeKeyboardShortcuts).apply(this, arguments));
    _this.toggleMode = _this.toggleMode.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.toggleSidebar = _this.toggleSidebar.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    return _this;
  }

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__["default"])(EditorModeKeyboardShortcuts, [{
    key: "toggleMode",
    value: function toggleMode() {
      var _this$props = this.props,
          mode = _this$props.mode,
          switchMode = _this$props.switchMode;
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

      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["KeyboardShortcuts"], {
        bindGlobal: true,
        shortcuts: (_ref = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref, _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_11__["default"].toggleEditorMode.raw, this.toggleMode), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref, _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_11__["default"].toggleSidebar.raw, this.toggleSidebar), _ref)
      });
    }
  }]);

  return EditorModeKeyboardShortcuts;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_10__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_9__["withSelect"])(function (select) {
  return {
    mode: select('core/edit-post').getEditorMode(),
    isEditorSidebarOpen: select('core/edit-post').isEditorSidebarOpened(),
    hasBlockSelection: !!select('core/editor').getBlockSelectionStart()
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_9__["withDispatch"])(function (dispatch, _ref2) {
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
})])(EditorModeKeyboardShortcuts));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/layout/index.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/layout/index.js ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/viewport */ "@wordpress/viewport");
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_viewport__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _browser_url__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../browser-url */ "./node_modules/@wordpress/edit-post/build-module/components/browser-url/index.js");
/* harmony import */ var _sidebar_block_sidebar__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../sidebar/block-sidebar */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/block-sidebar/index.js");
/* harmony import */ var _sidebar_document_sidebar__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../sidebar/document-sidebar */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/document-sidebar/index.js");
/* harmony import */ var _header__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../header */ "./node_modules/@wordpress/edit-post/build-module/components/header/index.js");
/* harmony import */ var _text_editor__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../text-editor */ "./node_modules/@wordpress/edit-post/build-module/components/text-editor/index.js");
/* harmony import */ var _visual_editor__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../visual-editor */ "./node_modules/@wordpress/edit-post/build-module/components/visual-editor/index.js");
/* harmony import */ var _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ../keyboard-shortcuts */ "./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js");
/* harmony import */ var _keyboard_shortcut_help_modal__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ../keyboard-shortcut-help-modal */ "./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/index.js");
/* harmony import */ var _meta_boxes__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ../meta-boxes */ "./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js");
/* harmony import */ var _sidebar__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ../sidebar */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/index.js");
/* harmony import */ var _sidebar_plugin_post_publish_panel__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ../sidebar/plugin-post-publish-panel */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-publish-panel/index.js");
/* harmony import */ var _sidebar_plugin_pre_publish_panel__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ../sidebar/plugin-pre-publish-panel */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js");
/* harmony import */ var _fullscreen_mode__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ../fullscreen-mode */ "./node_modules/@wordpress/edit-post/build-module/components/fullscreen-mode/index.js");



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
      isMobileViewport = _ref.isMobileViewport;
  var sidebarIsOpened = editorSidebarOpened || pluginSidebarOpened || publishSidebarOpened;
  var className = classnames__WEBPACK_IMPORTED_MODULE_2___default()('edit-post-layout', {
    'is-sidebar-opened': sidebarIsOpened,
    'has-fixed-toolbar': hasFixedToolbar
  });
  var publishLandmarkProps = {
    role: 'region',

    /* translators: accessibility text for the publish landmark region. */
    'aria-label': Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Editor publish'),
    tabIndex: -1
  };
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: className
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_fullscreen_mode__WEBPACK_IMPORTED_MODULE_22__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_browser_url__WEBPACK_IMPORTED_MODULE_10__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["UnsavedChangesWarning"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["AutosaveMonitor"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_header__WEBPACK_IMPORTED_MODULE_13__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "edit-post-layout__content",
    role: "region"
    /* translators: accessibility text for the content landmark region. */
    ,
    "aria-label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Editor content'),
    tabIndex: "-1"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["EditorNotices"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["PreserveScrollInReorder"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_16__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_keyboard_shortcut_help_modal__WEBPACK_IMPORTED_MODULE_17__["default"], null), mode === 'text' && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_text_editor__WEBPACK_IMPORTED_MODULE_14__["default"], null), mode === 'visual' && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_visual_editor__WEBPACK_IMPORTED_MODULE_15__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "edit-post-layout__metaboxes"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_meta_boxes__WEBPACK_IMPORTED_MODULE_18__["default"], {
    location: "normal"
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "edit-post-layout__metaboxes"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_meta_boxes__WEBPACK_IMPORTED_MODULE_18__["default"], {
    location: "advanced"
  }))), publishSidebarOpened ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["PostPublishPanel"], Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, publishLandmarkProps, {
    onClose: closePublishSidebar,
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving,
    PrePublishExtension: _sidebar_plugin_pre_publish_panel__WEBPACK_IMPORTED_MODULE_21__["default"].Slot,
    PostPublishExtension: _sidebar_plugin_post_publish_panel__WEBPACK_IMPORTED_MODULE_20__["default"].Slot
  })) : Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
    className: "edit-post-toggle-publish-panel"
  }, publishLandmarkProps), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    isDefault: true,
    type: "button",
    className: "edit-post-toggle-publish-panel__button",
    onClick: togglePublishSidebar,
    "aria-expanded": false
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Open publish panel'))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_sidebar_document_sidebar__WEBPACK_IMPORTED_MODULE_12__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_sidebar_block_sidebar__WEBPACK_IMPORTED_MODULE_11__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_sidebar__WEBPACK_IMPORTED_MODULE_19__["default"].Slot, null), isMobileViewport && sidebarIsOpened && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["ScrollLock"], null)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Popover"].Slot, null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_7__["PluginArea"], null));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_9__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withSelect"])(function (select) {
  return {
    mode: select('core/edit-post').getEditorMode(),
    editorSidebarOpened: select('core/edit-post').isEditorSidebarOpened(),
    pluginSidebarOpened: select('core/edit-post').isPluginSidebarOpened(),
    publishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
    hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
    hasActiveMetaboxes: select('core/edit-post').hasMetaBoxes(),
    isSaving: select('core/edit-post').isSavingMetaBoxes()
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      closePublishSidebar = _dispatch.closePublishSidebar,
      togglePublishSidebar = _dispatch.togglePublishSidebar;

  return {
    closePublishSidebar: closePublishSidebar,
    togglePublishSidebar: togglePublishSidebar
  };
}), _wordpress_components__WEBPACK_IMPORTED_MODULE_3__["navigateRegions"], Object(_wordpress_viewport__WEBPACK_IMPORTED_MODULE_8__["withViewportMatch"])({
  isMobileViewport: '< small'
}))(Layout));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js":
/*!***************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _meta_boxes_area__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./meta-boxes-area */ "./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js");


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function MetaBoxes(_ref) {
  var location = _ref.location,
      isActive = _ref.isActive;

  if (!isActive) {
    return null;
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_meta_boxes_area__WEBPACK_IMPORTED_MODULE_2__["default"], {
    location: location
  });
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["withSelect"])(function (select, ownProps) {
  var _select = select('core/edit-post'),
      isMetaBoxLocationActive = _select.isMetaBoxLocationActive;

  return {
    isActive: isMetaBoxLocationActive(ownProps.location)
  };
})(MetaBoxes));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js":
/*!*******************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js ***!
  \*******************************************************************************************************/
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
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_9__);








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





var MetaBoxesArea =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(MetaBoxesArea, _Component);

  /**
   * @inheritdoc
   */
  function MetaBoxesArea() {
    var _this;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, MetaBoxesArea);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__["default"])(MetaBoxesArea).apply(this, arguments));
    _this.bindContainerNode = _this.bindContainerNode.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__["default"])(_this)));
    return _this;
  }
  /**
   * @inheritdoc
   */


  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(MetaBoxesArea, [{
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
      var classes = classnames__WEBPACK_IMPORTED_MODULE_7___default()('edit-post-meta-boxes-area', "is-".concat(location), {
        'is-loading': isSaving
      });
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("div", {
        className: classes
      }, isSaving && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Spinner"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("div", {
        className: "edit-post-meta-boxes-area__container",
        ref: this.bindContainerNode
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("div", {
        className: "edit-post-meta-boxes-area__clear"
      }));
    }
  }]);

  return MetaBoxesArea;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_9__["withSelect"])(function (select) {
  return {
    isSaving: select('core/edit-post').isSavingMetaBoxes()
  };
})(MetaBoxesArea));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/block-sidebar/index.js":
/*!**************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/block-sidebar/index.js ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _settings_header__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../settings-header */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-header/index.js");
/* harmony import */ var ___WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../ */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/index.js");


/**
 * WordPress dependencies
 */



/**
 * Internal Dependencies
 */



var SIDEBAR_NAME = 'edit-post/block';

var BlockSidebar = function BlockSidebar() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(___WEBPACK_IMPORTED_MODULE_5__["default"], {
    name: SIDEBAR_NAME,
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Editor settings')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_settings_header__WEBPACK_IMPORTED_MODULE_4__["default"], {
    sidebarName: SIDEBAR_NAME
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Panel"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
    className: "edit-post-block-sidebar__panel"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["BlockInspector"], null))));
};

/* harmony default export */ __webpack_exports__["default"] = (BlockSidebar);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/discussion-panel/index.js":
/*!*****************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/discussion-panel/index.js ***!
  \*****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);


/**
 * WordPress dependencies
 */





/**
 * Module Constants
 */

var PANEL_NAME = 'discussion-panel';

function DiscussionPanel(_ref) {
  var isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostTypeSupportCheck"], {
    supportKeys: ['comments', 'trackbacks']
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Discussion'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostTypeSupportCheck"], {
    supportKeys: "comments"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostComments"], null))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostTypeSupportCheck"], {
    supportKeys: "trackbacks"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostPingbacks"], null)))));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withSelect"])(function (select) {
  return {
    isOpened: select('core/edit-post').isEditorSidebarPanelOpened(PANEL_NAME)
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleGeneralSidebarEditorPanel(PANEL_NAME);
    }
  };
})])(DiscussionPanel));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/document-sidebar/index.js":
/*!*****************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/document-sidebar/index.js ***!
  \*****************************************************************************************************/
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
/* harmony import */ var _post_status__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../post-status */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-status/index.js");
/* harmony import */ var _post_excerpt__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../post-excerpt */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-excerpt/index.js");
/* harmony import */ var _post_taxonomies__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../post-taxonomies */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/index.js");
/* harmony import */ var _featured_image__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../featured-image */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/featured-image/index.js");
/* harmony import */ var _discussion_panel__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../discussion-panel */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/discussion-panel/index.js");
/* harmony import */ var _last_revision__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../last-revision */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/last-revision/index.js");
/* harmony import */ var _page_attributes__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../page-attributes */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/page-attributes/index.js");
/* harmony import */ var _meta_boxes__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../meta-boxes */ "./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js");
/* harmony import */ var _settings_header__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../settings-header */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-header/index.js");
/* harmony import */ var ___WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../ */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/index.js");


/**
 * WordPress dependencies
 */


/**
 * Internal Dependencies
 */











var SIDEBAR_NAME = 'edit-post/document';

var DocumentSidebar = function DocumentSidebar() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(___WEBPACK_IMPORTED_MODULE_12__["default"], {
    name: SIDEBAR_NAME,
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Editor settings')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_settings_header__WEBPACK_IMPORTED_MODULE_11__["default"], {
    sidebarName: SIDEBAR_NAME
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Panel"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_status__WEBPACK_IMPORTED_MODULE_3__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_last_revision__WEBPACK_IMPORTED_MODULE_8__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_taxonomies__WEBPACK_IMPORTED_MODULE_5__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_featured_image__WEBPACK_IMPORTED_MODULE_6__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_excerpt__WEBPACK_IMPORTED_MODULE_4__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_discussion_panel__WEBPACK_IMPORTED_MODULE_7__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_page_attributes__WEBPACK_IMPORTED_MODULE_9__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_meta_boxes__WEBPACK_IMPORTED_MODULE_10__["default"], {
    location: "side"
  })));
};

/* harmony default export */ __webpack_exports__["default"] = (DocumentSidebar);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/featured-image/index.js":
/*!***************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/featured-image/index.js ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Module Constants
 */

var PANEL_NAME = 'featured-image';

function FeaturedImage(_ref) {
  var isOpened = _ref.isOpened,
      postType = _ref.postType,
      onTogglePanel = _ref.onTogglePanel;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["PostFeaturedImageCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
    title: Object(lodash__WEBPACK_IMPORTED_MODULE_1__["get"])(postType, ['labels', 'featured_image'], Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Featured Image')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["PostFeaturedImage"], null)));
}

var applyWithSelect = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  var _select3 = select('core/edit-post'),
      isEditorSidebarPanelOpened = _select3.isEditorSidebarPanelOpened;

  return {
    postType: getPostType(getEditedPostAttribute('type')),
    isOpened: isEditorSidebarPanelOpened(PANEL_NAME)
  };
});
var applyWithDispatch = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      toggleGeneralSidebarEditorPanel = _dispatch.toggleGeneralSidebarEditorPanel;

  return {
    onTogglePanel: Object(lodash__WEBPACK_IMPORTED_MODULE_1__["partial"])(toggleGeneralSidebarEditorPanel, PANEL_NAME)
  };
});
/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__["compose"])(applyWithSelect, applyWithDispatch)(FeaturedImage));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/index.js":
/*!************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/index.js ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);


/**
 * WordPress Dependencies
 */




var _createSlotFill = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["createSlotFill"])('Sidebar'),
    Fill = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;
/**
 * Renders a sidebar with its content.
 *
 * @return {Object} The rendered sidebar.
 */


var Sidebar = function Sidebar(_ref) {
  var children = _ref.children,
      label = _ref.label;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Fill, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "edit-post-sidebar",
    role: "region",
    "aria-label": label,
    tabIndex: "-1"
  }, children));
};

var WrappedSidebar = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withSelect"])(function (select, _ref2) {
  var name = _ref2.name;
  return {
    isActive: select('core/edit-post').getActiveGeneralSidebarName() === name
  };
}), Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__["ifCondition"])(function (_ref3) {
  var isActive = _ref3.isActive;
  return isActive;
}), _wordpress_components__WEBPACK_IMPORTED_MODULE_1__["withFocusReturn"])(Sidebar);
WrappedSidebar.Slot = Slot;
/* harmony default export */ __webpack_exports__["default"] = (WrappedSidebar);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/last-revision/index.js":
/*!**************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/last-revision/index.js ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */



function LastRevision() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostLastRevisionCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
    className: "edit-post-last-revision__panel"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostLastRevision"], null)));
}

/* harmony default export */ __webpack_exports__["default"] = (LastRevision);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/page-attributes/index.js":
/*!****************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/page-attributes/index.js ***!
  \****************************************************************************************************/
/*! exports provided: PageAttributes, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PageAttributes", function() { return PageAttributes; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Module Constants
 */

var PANEL_NAME = 'page-attributes';
function PageAttributes(_ref) {
  var isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel,
      postType = _ref.postType;

  if (!postType) {
    return null;
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["PageAttributesCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
    title: Object(lodash__WEBPACK_IMPORTED_MODULE_1__["get"])(postType, ['labels', 'attributes'], Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Page Attributes')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["PageTemplate"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["PageAttributesParent"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["PageAttributesOrder"], null))));
}
var applyWithSelect = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core/edit-post'),
      isEditorSidebarPanelOpened = _select2.isEditorSidebarPanelOpened;

  var _select3 = select('core'),
      getPostType = _select3.getPostType;

  return {
    isOpened: isEditorSidebarPanelOpened(PANEL_NAME),
    postType: getPostType(getEditedPostAttribute('type'))
  };
});
var applyWithDispatch = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      toggleGeneralSidebarEditorPanel = _dispatch.toggleGeneralSidebarEditorPanel;

  return {
    onTogglePanel: Object(lodash__WEBPACK_IMPORTED_MODULE_1__["partial"])(toggleGeneralSidebarEditorPanel, PANEL_NAME)
  };
});
/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["compose"])(applyWithSelect, applyWithDispatch)(PageAttributes));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-publish-panel/index.js":
/*!**************************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-publish-panel/index.js ***!
  \**************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */


var _createSlotFill = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["createSlotFill"])('PluginPostPublishPanel'),
    Fill = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;

var PluginPostPublishPanel = function PluginPostPublishPanel(_ref) {
  var children = _ref.children,
      className = _ref.className,
      title = _ref.title,
      _ref$initialOpen = _ref.initialOpen,
      initialOpen = _ref$initialOpen === void 0 ? false : _ref$initialOpen;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Fill, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
    className: className,
    initialOpen: initialOpen || !title,
    title: title
  }, children));
};

PluginPostPublishPanel.Slot = Slot;
/* harmony default export */ __webpack_exports__["default"] = (PluginPostPublishPanel);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-status-info/index.js":
/*!************************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-status-info/index.js ***!
  \************************************************************************************************************/
/*! exports provided: Fill, Slot, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Fill", function() { return Fill; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Slot", function() { return Slot; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);


/**
 * Defines as extensibility slot for the Status & Visibility panel.
 */

/**
 * WordPress dependencies
 */


var _createSlotFill = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["createSlotFill"])('PluginPostStatusInfo'),
    Fill = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;



var PluginPostStatusInfo = function PluginPostStatusInfo(_ref) {
  var children = _ref.children,
      className = _ref.className;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Fill, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], {
    className: className
  }, children));
};

PluginPostStatusInfo.Slot = Slot;
/* harmony default export */ __webpack_exports__["default"] = (PluginPostStatusInfo);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js":
/*!*************************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js ***!
  \*************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */


var _createSlotFill = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["createSlotFill"])('PluginPrePublishPanel'),
    Fill = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;

var PluginPrePublishPanel = function PluginPrePublishPanel(_ref) {
  var children = _ref.children,
      className = _ref.className,
      title = _ref.title,
      _ref$initialOpen = _ref.initialOpen,
      initialOpen = _ref$initialOpen === void 0 ? false : _ref$initialOpen;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Fill, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
    className: className,
    initialOpen: initialOpen || !title,
    title: title
  }, children));
};

PluginPrePublishPanel.Slot = Slot;
/* harmony default export */ __webpack_exports__["default"] = (PluginPrePublishPanel);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-sidebar/index.js":
/*!***************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-sidebar/index.js ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _header_pinned_plugins__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../header/pinned-plugins */ "./node_modules/@wordpress/edit-post/build-module/components/header/pinned-plugins/index.js");
/* harmony import */ var ___WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../ */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/index.js");
/* harmony import */ var _sidebar_header__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../sidebar-header */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/sidebar-header/index.js");


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
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, isPinnable && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_header_pinned_plugins__WEBPACK_IMPORTED_MODULE_6__["default"], null, isPinned && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["IconButton"], {
    icon: icon,
    label: title,
    onClick: toggleSidebar,
    isToggled: isActive,
    "aria-expanded": isActive
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(___WEBPACK_IMPORTED_MODULE_7__["default"], {
    name: sidebarName,
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Editor plugins')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_sidebar_header__WEBPACK_IMPORTED_MODULE_8__["default"], {
    closeLabel: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Close plugin')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("strong", null, title), isPinnable && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["IconButton"], {
    icon: isPinned ? 'star-filled' : 'star-empty',
    label: isPinned ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Unpin from toolbar') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Pin to toolbar'),
    onClick: togglePin,
    isToggled: isPinned,
    "aria-expanded": isPinned
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Panel"], null, children)));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__["compose"])(Object(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon,
    sidebarName: "".concat(context.name, "/").concat(ownProps.name)
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withSelect"])(function (select, _ref) {
  var sidebarName = _ref.sidebarName;

  var _select = select('core/edit-post'),
      getActiveGeneralSidebarName = _select.getActiveGeneralSidebarName,
      isPluginItemPinned = _select.isPluginItemPinned;

  return {
    isActive: getActiveGeneralSidebarName() === sidebarName,
    isPinned: isPluginItemPinned(sidebarName)
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withDispatch"])(function (dispatch, _ref2) {
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


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-author/index.js":
/*!************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-author/index.js ***!
  \************************************************************************************************/
/*! exports provided: PostAuthor, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PostAuthor", function() { return PostAuthor; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */


function PostAuthor() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostAuthorCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostAuthor"], null)));
}
/* harmony default export */ __webpack_exports__["default"] = (PostAuthor);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-excerpt/index.js":
/*!*************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-excerpt/index.js ***!
  \*************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);


/**
 * WordPress dependencies
 */





/**
 * Module Constants
 */

var PANEL_NAME = 'post-excerpt';

function PostExcerpt(_ref) {
  var isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostExcerptCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Excerpt'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostExcerpt"], null)));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withSelect"])(function (select) {
  return {
    isOpened: select('core/edit-post').isEditorSidebarPanelOpened(PANEL_NAME)
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleGeneralSidebarEditorPanel(PANEL_NAME);
    }
  };
})])(PostExcerpt));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-format/index.js":
/*!************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-format/index.js ***!
  \************************************************************************************************/
/*! exports provided: PostFormat, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PostFormat", function() { return PostFormat; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */


function PostFormat() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostFormatCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostFormat"], null)));
}
/* harmony default export */ __webpack_exports__["default"] = (PostFormat);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-pending-status/index.js":
/*!********************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-pending-status/index.js ***!
  \********************************************************************************************************/
/*! exports provided: PostPendingStatus, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PostPendingStatus", function() { return PostPendingStatus; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */


function PostPendingStatus() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostPendingStatusCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostPendingStatus"], null)));
}
/* harmony default export */ __webpack_exports__["default"] = (PostPendingStatus);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-schedule/index.js":
/*!**************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-schedule/index.js ***!
  \**************************************************************************************************/
/*! exports provided: PostSchedule, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PostSchedule", function() { return PostSchedule; });
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



function PostSchedule() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostScheduleCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelRow"], {
    className: "edit-post-post-schedule"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Publish')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Dropdown"], {
    position: "bottom left",
    contentClassName: "edit-post-post-schedule__dialog",
    renderToggle: function renderToggle(_ref) {
      var onToggle = _ref.onToggle,
          isOpen = _ref.isOpen;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Button"], {
        type: "button",
        className: "edit-post-post-schedule__toggle",
        onClick: onToggle,
        "aria-expanded": isOpen,
        isLink: true
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostScheduleLabel"], null));
    },
    renderContent: function renderContent() {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostSchedule"], null);
    }
  })));
}
/* harmony default export */ __webpack_exports__["default"] = (PostSchedule);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-status/index.js":
/*!************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-status/index.js ***!
  \************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _post_visibility__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../post-visibility */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-visibility/index.js");
/* harmony import */ var _post_trash__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../post-trash */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-trash/index.js");
/* harmony import */ var _post_schedule__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../post-schedule */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-schedule/index.js");
/* harmony import */ var _post_sticky__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../post-sticky */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-sticky/index.js");
/* harmony import */ var _post_author__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../post-author */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-author/index.js");
/* harmony import */ var _post_format__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../post-format */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-format/index.js");
/* harmony import */ var _post_pending_status__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../post-pending-status */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-pending-status/index.js");
/* harmony import */ var _plugin_post_status_info__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../plugin-post-status-info */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-status-info/index.js");


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
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelBody"], {
    className: "edit-post-post-status",
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Status & Visibility'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_plugin_post_status_info__WEBPACK_IMPORTED_MODULE_12__["default"].Slot, null, function (fills) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_visibility__WEBPACK_IMPORTED_MODULE_5__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_schedule__WEBPACK_IMPORTED_MODULE_7__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_format__WEBPACK_IMPORTED_MODULE_10__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_sticky__WEBPACK_IMPORTED_MODULE_8__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_pending_status__WEBPACK_IMPORTED_MODULE_11__["default"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_author__WEBPACK_IMPORTED_MODULE_9__["default"], null), fills, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_post_trash__WEBPACK_IMPORTED_MODULE_6__["default"], null));
  }));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["withSelect"])(function (select) {
  return {
    isOpened: select('core/edit-post').isEditorSidebarPanelOpened(PANEL_NAME)
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleGeneralSidebarEditorPanel(PANEL_NAME);
    }
  };
})])(PostStatus));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-sticky/index.js":
/*!************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-sticky/index.js ***!
  \************************************************************************************************/
/*! exports provided: PostSticky, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PostSticky", function() { return PostSticky; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */


function PostSticky() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostStickyCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostSticky"], null)));
}
/* harmony default export */ __webpack_exports__["default"] = (PostSticky);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/index.js":
/*!****************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/index.js ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _taxonomy_panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./taxonomy-panel */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js");


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function PostTaxonomies() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["PostTaxonomiesCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["PostTaxonomies"], {
    taxonomyWrapper: function taxonomyWrapper(content, taxonomy) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_taxonomy_panel__WEBPACK_IMPORTED_MODULE_2__["default"], {
        taxonomy: taxonomy
      }, content);
    }
  }));
}

/* harmony default export */ __webpack_exports__["default"] = (PostTaxonomies);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js":
/*!*************************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js ***!
  \*************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);


/**
 * External Dependencies
 */

/**
 * WordPress dependencies
 */





function TaxonomyPanel(_ref) {
  var taxonomy = _ref.taxonomy,
      isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel,
      children = _ref.children;
  var taxonomyMenuName = Object(lodash__WEBPACK_IMPORTED_MODULE_1__["get"])(taxonomy, ['labels', 'menu_name']);

  if (!taxonomyMenuName) {
    return null;
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
    title: taxonomyMenuName,
    opened: isOpened,
    onToggle: onTogglePanel
  }, children);
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withSelect"])(function (select, ownProps) {
  var slug = Object(lodash__WEBPACK_IMPORTED_MODULE_1__["get"])(ownProps.taxonomy, ['slug']);
  var panelName = slug ? "taxonomy-panel-".concat(slug) : '';
  return {
    panelName: panelName,
    isOpened: slug ? select('core/edit-post').isEditorSidebarPanelOpened(panelName) : false
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withDispatch"])(function (dispatch, ownProps) {
  return {
    onTogglePanel: function onTogglePanel() {
      dispatch('core/edit-post').toggleGeneralSidebarEditorPanel(ownProps.panelName);
    }
  };
}))(TaxonomyPanel));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-trash/index.js":
/*!***********************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-trash/index.js ***!
  \***********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return PostTrash; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */


function PostTrash() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostTrashCheck"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["PostTrash"], null)));
}


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-visibility/index.js":
/*!****************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-visibility/index.js ***!
  \****************************************************************************************************/
/*! exports provided: PostVisibility, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PostVisibility", function() { return PostVisibility; });
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



function PostVisibility() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostVisibilityCheck"], {
    render: function render(_ref) {
      var canEdit = _ref.canEdit;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelRow"], {
        className: "edit-post-post-visibility"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Visibility')), !canEdit && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostVisibilityLabel"], null)), canEdit && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Dropdown"], {
        position: "bottom left",
        contentClassName: "edit-post-post-visibility__dialog",
        renderToggle: function renderToggle(_ref2) {
          var isOpen = _ref2.isOpen,
              onToggle = _ref2.onToggle;
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Button"], {
            type: "button",
            "aria-expanded": isOpen,
            className: "edit-post-post-visibility__toggle",
            onClick: onToggle,
            isLink: true
          }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostVisibilityLabel"], null));
        },
        renderContent: function renderContent() {
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__["PostVisibility"], null);
        }
      }));
    }
  });
}
/* harmony default export */ __webpack_exports__["default"] = (PostVisibility);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-header/index.js":
/*!****************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-header/index.js ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _sidebar_header__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../sidebar-header */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/sidebar-header/index.js");


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



var SettingsHeader = function SettingsHeader(_ref) {
  var count = _ref.count,
      openDocumentSettings = _ref.openDocumentSettings,
      openBlockSettings = _ref.openBlockSettings,
      sidebarName = _ref.sidebarName;
  // Do not display "0 Blocks".
  var blockCount = count === 0 ? 1 : count;
  var blockLabel = blockCount === 1 ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Block') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["sprintf"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["_n"])('%d Block', '%d Blocks', blockCount), blockCount);
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_sidebar_header__WEBPACK_IMPORTED_MODULE_4__["default"], {
    className: "edit-post-sidebar__panel-tabs",
    closeLabel: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Close settings')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("button", {
    onClick: openDocumentSettings,
    className: "edit-post-sidebar__panel-tab ".concat(sidebarName === 'edit-post/document' ? 'is-active' : ''),
    "aria-label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Document settings'),
    "data-label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Document')
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Document')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("button", {
    onClick: openBlockSettings,
    className: "edit-post-sidebar__panel-tab ".concat(sidebarName === 'edit-post/block' ? 'is-active' : ''),
    "aria-label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Block settings'),
    "data-label": blockLabel
  }, blockLabel));
};

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["withSelect"])(function (select) {
  return {
    count: select('core/editor').getSelectedBlockCount()
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["withDispatch"])(function (dispatch) {
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
}))(SettingsHeader));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/sidebar-header/index.js":
/*!***************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/sidebar/sidebar-header/index.js ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../keyboard-shortcuts */ "./node_modules/@wordpress/edit-post/build-module/keyboard-shortcuts.js");


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



var SidebarHeader = function SidebarHeader(_ref) {
  var children = _ref.children,
      className = _ref.className,
      closeLabel = _ref.closeLabel,
      closeSidebar = _ref.closeSidebar,
      title = _ref.title;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "components-panel__header edit-post-sidebar-header__small"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
    className: "edit-post-sidebar-header__title"
  }, title || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('(no title)')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["IconButton"], {
    onClick: closeSidebar,
    icon: "no-alt",
    label: closeLabel
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: classnames__WEBPACK_IMPORTED_MODULE_1___default()('components-panel__header edit-post-sidebar-header', className)
  }, children, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["IconButton"], {
    onClick: closeSidebar,
    icon: "no-alt",
    label: closeLabel,
    shortcut: _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_6__["default"].toggleSidebar
  })));
};

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withSelect"])(function (select) {
  return {
    title: select('core/editor').getEditedPostAttribute('title')
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withDispatch"])(function (dispatch) {
  return {
    closeSidebar: dispatch('core/edit-post').closeGeneralSidebar
  };
}))(SidebarHeader));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/text-editor/index.js":
/*!****************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/text-editor/index.js ***!
  \****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_5__);


/**
 * WordPress dependencies
 */






function TextEditor(_ref) {
  var onExit = _ref.onExit;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "edit-post-text-editor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "edit-post-text-editor__toolbar"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("h2", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Editing Code')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["IconButton"], {
    onClick: onExit,
    icon: "no-alt",
    shortcut: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_5__["displayShortcut"].secondary('m')
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Exit Code Editor'))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "edit-post-text-editor__body"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["PostTitle"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["PostTextEditor"], null)));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["withDispatch"])(function (dispatch) {
  return {
    onExit: function onExit() {
      dispatch('core/edit-post').switchEditorMode('visual');
    }
  };
})(TextEditor));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/visual-editor/block-inspector-button.js":
/*!***********************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/block-inspector-button.js ***!
  \***********************************************************************************************************/
/*! exports provided: BlockInspectorButton, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "BlockInspectorButton", function() { return BlockInspectorButton; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../keyboard-shortcuts */ "./node_modules/@wordpress/edit-post/build-module/keyboard-shortcuts.js");


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
      onClick = _ref$onClick === void 0 ? lodash__WEBPACK_IMPORTED_MODULE_1__["noop"] : _ref$onClick,
      _ref$small = _ref.small,
      small = _ref$small === void 0 ? false : _ref$small,
      speak = _ref.speak;

  var speakMessage = function speakMessage() {
    if (areAdvancedSettingsOpened) {
      speak(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Block settings closed'));
    } else {
      speak(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Additional settings are now available in the Editor block settings sidebar'));
    }
  };

  var label = areAdvancedSettingsOpened ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Hide Block Settings') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show Block Settings');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["MenuItem"], {
    className: "editor-block-settings-menu__control",
    onClick: Object(lodash__WEBPACK_IMPORTED_MODULE_1__["flow"])(areAdvancedSettingsOpened ? closeSidebar : openEditorSidebar, speakMessage, onClick),
    icon: "admin-generic",
    label: small ? label : undefined,
    shortcut: _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_6__["default"].toggleSidebar
  }, !small && label);
}
/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withSelect"])(function (select) {
  return {
    areAdvancedSettingsOpened: select('core/edit-post').getActiveGeneralSidebarName() === 'edit-post/block'
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withDispatch"])(function (dispatch) {
  return {
    openEditorSidebar: function openEditorSidebar() {
      return dispatch('core/edit-post').openGeneralSidebar('edit-post/block');
    },
    closeSidebar: dispatch('core/edit-post').closeGeneralSidebar
  };
}), _wordpress_components__WEBPACK_IMPORTED_MODULE_3__["withSpokenMessages"])(BlockInspectorButton));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/components/visual-editor/index.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/index.js ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _block_inspector_button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./block-inspector-button */ "./node_modules/@wordpress/edit-post/build-module/components/visual-editor/block-inspector-button.js");
/* harmony import */ var _block_settings_menu_plugin_block_settings_menu_group__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../block-settings-menu/plugin-block-settings-menu-group */ "./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-group.js");


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




function VisualEditor() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["BlockSelectionClearer"], {
    className: "edit-post-visual-editor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["EditorGlobalKeyboardShortcuts"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["CopyHandler"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["MultiSelectScrollIntoView"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["WritingFlow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["ObserveTyping"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["PostTitle"], null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["BlockList"], null))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["_BlockSettingsMenuFirstItem"], null, function (_ref) {
    var onClose = _ref.onClose;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_block_inspector_button__WEBPACK_IMPORTED_MODULE_2__["default"], {
      onClick: onClose
    });
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_1__["_BlockSettingsMenuPluginsExtension"], null, function (_ref2) {
    var clientIds = _ref2.clientIds,
        onClose = _ref2.onClose;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_block_settings_menu_plugin_block_settings_menu_group__WEBPACK_IMPORTED_MODULE_3__["default"].Slot, {
      fillProps: {
        clientIds: clientIds,
        onClose: onClose
      }
    });
  }));
}

/* harmony default export */ __webpack_exports__["default"] = (VisualEditor);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/editor.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/editor.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _components_layout__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/layout */ "./node_modules/@wordpress/edit-post/build-module/components/layout/index.js");





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
      overridePost = _ref.overridePost,
      onError = _ref.onError,
      props = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_2__["default"])(_ref, ["settings", "hasFixedToolbar", "focusMode", "post", "overridePost", "onError"]);

  if (!post) {
    return null;
  }

  var editorSettings = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, settings, {
    hasFixedToolbar: hasFixedToolbar,
    focusMode: focusMode
  });

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["StrictMode"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["EditorProvider"], Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
    settings: editorSettings,
    post: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, post, overridePost)
  }, props), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["ErrorBoundary"], {
    onError: onError
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_components_layout__WEBPACK_IMPORTED_MODULE_6__["default"], null)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_5__["PostLockedModal"], null)));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withSelect"])(function (select, _ref2) {
  var postId = _ref2.postId,
      postType = _ref2.postType;
  return {
    hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
    focusMode: select('core/edit-post').isFeatureActive('focusMode'),
    post: select('core').getEntityRecord('postType', postType, postId)
  };
})(Editor));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/hooks/components/index.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/hooks/components/index.js ***!
  \**********************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _media_upload__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./media-upload */ "./node_modules/@wordpress/edit-post/build-module/hooks/components/media-upload/index.js");
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var replaceMediaUpload = function replaceMediaUpload() {
  return _media_upload__WEBPACK_IMPORTED_MODULE_1__["default"];
};

Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__["addFilter"])('editor.MediaUpload', 'core/edit-post/components/media-upload/replace-media-upload', replaceMediaUpload);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/hooks/components/media-upload/index.js":
/*!***********************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/hooks/components/media-upload/index.js ***!
  \***********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/deprecated */ "@wordpress/deprecated");
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_11__);








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


var slimImageObject = function slimImageObject(img) {
  var attrSet = ['sizes', 'mime', 'type', 'subtype', 'id', 'url', 'alt', 'link', 'caption'];
  return Object(lodash__WEBPACK_IMPORTED_MODULE_7__["pick"])(img, attrSet);
};

var getAttachmentsCollection = function getAttachmentsCollection(ids) {
  return wp.media.query({
    order: 'ASC',
    orderby: 'post__in',
    per_page: -1,
    post__in: ids,
    query: true,
    type: 'image'
  });
};

var MediaUpload =
/*#__PURE__*/
function (_Component) {
  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__["default"])(MediaUpload, _Component);

  function MediaUpload(_ref) {
    var _this;

    var allowedTypes = _ref.allowedTypes,
        deprecatedType = _ref.type,
        _ref$multiple = _ref.multiple,
        multiple = _ref$multiple === void 0 ? false : _ref$multiple,
        _ref$gallery = _ref.gallery,
        gallery = _ref$gallery === void 0 ? false : _ref$gallery,
        _ref$title = _ref.title,
        title = _ref$title === void 0 ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Select or Upload Media') : _ref$title,
        modalClass = _ref.modalClass,
        value = _ref.value;

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__["default"])(this, MediaUpload);

    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__["default"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__["default"])(MediaUpload).apply(this, arguments));
    _this.openModal = _this.openModal.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onOpen = _this.onOpen.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onSelect = _this.onSelect.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onUpdate = _this.onUpdate.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.onClose = _this.onClose.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    _this.processMediaCaption = _this.processMediaCaption.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__["default"])(_this)));
    var allowedTypesToUse = allowedTypes;

    if (!allowedTypes && deprecatedType) {
      _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_11___default()('type property of wp.editor.MediaUpload', {
        version: '4.2',
        alternative: 'allowedTypes property containing an array with the allowedTypes or do not pass any property if all types are allowed'
      });

      if (deprecatedType === '*') {
        allowedTypesToUse = undefined;
      } else {
        allowedTypesToUse = [deprecatedType];
      }
    }

    if (gallery) {
      var currentState = value ? 'gallery-edit' : 'gallery';
      var GalleryDetailsMediaFrame = getGalleryDetailsMediaFrame();
      var attachments = getAttachmentsCollection(value);
      var selection = new wp.media.model.Selection(attachments.models, {
        props: attachments.props.toJSON(),
        multiple: multiple
      });
      _this.frame = new GalleryDetailsMediaFrame({
        mimeType: allowedTypesToUse,
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
          text: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Select')
        },
        multiple: multiple
      };

      if (!!allowedTypesToUse) {
        frameConfig.library = {
          type: allowedTypesToUse
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

  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__["default"])(MediaUpload, [{
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.frame.remove();
    }
  }, {
    key: "onUpdate",
    value: function onUpdate(selections) {
      var _this2 = this;

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
          return _this2.processMediaCaption(slimImageObject(model.toJSON()));
        }));
      } else {
        onSelect(this.processMediaCaption(slimImageObject(selectedImages.models[0].toJSON())));
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
      onSelect(multiple ? attachment.map(this.processMediaCaption) : this.processMediaCaption(attachment[0]));
    }
  }, {
    key: "onOpen",
    value: function onOpen() {
      if (!this.props.value) {
        return;
      }

      if (!this.props.gallery) {
        var selection = this.frame.state().get('selection');
        Object(lodash__WEBPACK_IMPORTED_MODULE_7__["castArray"])(this.props.value).map(function (id) {
          selection.add(wp.media.attachment(id));
        });
      } // load the images so they are available in the media modal.


      getAttachmentsCollection(Object(lodash__WEBPACK_IMPORTED_MODULE_7__["castArray"])(this.props.value)).more();
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
    key: "openModal",
    value: function openModal() {
      this.frame.open();
    }
  }, {
    key: "processMediaCaption",
    value: function processMediaCaption(mediaObject) {
      return !mediaObject.caption ? mediaObject : Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, mediaObject, {
        caption: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__["parseWithAttributeSchema"])(mediaObject.caption, {
          source: 'rich-text'
        })
      });
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
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_9__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (MediaUpload);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/hooks/index.js":
/*!***********************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/hooks/index.js ***!
  \***********************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components */ "./node_modules/@wordpress/edit-post/build-module/hooks/components/index.js");
/* harmony import */ var _validate_multiple_use__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./validate-multiple-use */ "./node_modules/@wordpress/edit-post/build-module/hooks/validate-multiple-use/index.js");
/**
 * Internal dependencies
 */




/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/hooks/validate-multiple-use/index.js":
/*!*********************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/hooks/validate-multiple-use/index.js ***!
  \*********************************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_10__);




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








var enhance = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_10__["compose"])(
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
Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withSelect"])(function (select, block) {
  var blocks = select('core/editor').getBlocks();
  var multiple = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["hasBlockSupport"])(block.name, 'multiple', true); // For block types with `multiple` support, there is no "original
  // block" to be found in the content, as the block itself is valid.

  if (multiple) {
    return {};
  } // Otherwise, only pass `originalBlockClientId` if it refers to a different
  // block from the current one.


  var firstOfSameType = Object(lodash__WEBPACK_IMPORTED_MODULE_3__["find"])(blocks, function (_ref) {
    var name = _ref.name;
    return block.name === name;
  });
  var isInvalid = firstOfSameType && firstOfSameType.clientId !== block.clientId;
  return {
    originalBlockClientId: isInvalid && firstOfSameType.clientId
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withDispatch"])(function (dispatch, _ref2) {
  var originalBlockClientId = _ref2.originalBlockClientId;
  return {
    selectFirst: function selectFirst() {
      return dispatch('core/editor').selectBlock(originalBlockClientId);
    }
  };
}));
var withMultipleValidation = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_10__["createHigherOrderComponent"])(function (BlockEdit) {
  return enhance(function (_ref3) {
    var originalBlockClientId = _ref3.originalBlockClientId,
        selectFirst = _ref3.selectFirst,
        props = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref3, ["originalBlockClientId", "selectFirst"]);

    if (!originalBlockClientId) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(BlockEdit, props);
    }

    var blockType = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["getBlockType"])(props.name);
    var outboundType = getOutboundType(props.name);
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", {
      key: "invalid-preview",
      style: {
        minHeight: '60px'
      }
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(BlockEdit, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      key: "block-edit"
    }, props))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_7__["Warning"], {
      key: "multiple-use-warning",
      actions: [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Button"], {
        key: "find-original",
        isLarge: true,
        onClick: selectFirst
      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Find original')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Button"], {
        key: "remove",
        isLarge: true,
        onClick: function onClick() {
          return props.onReplace([]);
        }
      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Remove')), outboundType && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Button"], {
        key: "transform",
        isLarge: true,
        onClick: function onClick() {
          return props.onReplace(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])(outboundType.name, props.attributes));
        }
      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('Transform into:'), ' ', outboundType.title)]
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("strong", null, blockType.title, ": "), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__["__"])('This block can only be used once.'))];
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
  var transform = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["findTransform"])(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["getBlockTransforms"])('to', blockName), function (_ref4) {
    var type = _ref4.type,
        blocks = _ref4.blocks;
    return type === 'block' && blocks.length === 1;
  } // What about when .length > 1?
  );

  if (!transform) {
    return null;
  }

  return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["getBlockType"])(transform.blocks[0]);
}

Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__["addFilter"])('editor.BlockEdit', 'core/edit-post/validate-multiple-use/with-multiple-validation', withMultipleValidation);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/index.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/index.js ***!
  \*****************************************************************/
/*! exports provided: reinitializeEditor, initializeEditor, PluginBlockSettingsMenuItem, PluginPostPublishPanel, PluginPostStatusInfo, PluginPrePublishPanel, PluginSidebar, PluginSidebarMoreMenuItem */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "reinitializeEditor", function() { return reinitializeEditor; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "initializeEditor", function() { return initializeEditor; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_nux__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/nux */ "@wordpress/nux");
/* harmony import */ var _wordpress_nux__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_nux__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/viewport */ "@wordpress/viewport");
/* harmony import */ var _wordpress_viewport__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_viewport__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_block_library__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/block-library */ "@wordpress/block-library");
/* harmony import */ var _wordpress_block_library__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_library__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/deprecated */ "@wordpress/deprecated");
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _hooks__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./hooks */ "./node_modules/@wordpress/edit-post/build-module/hooks/index.js");
/* harmony import */ var _plugins__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./plugins */ "./node_modules/@wordpress/edit-post/build-module/plugins/index.js");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./store */ "./node_modules/@wordpress/edit-post/build-module/store/index.js");
/* harmony import */ var _store_actions__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./store/actions */ "./node_modules/@wordpress/edit-post/build-module/store/actions.js");
/* harmony import */ var _editor__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./editor */ "./node_modules/@wordpress/edit-post/build-module/editor.js");
/* harmony import */ var _components_block_settings_menu_plugin_block_settings_menu_item__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./components/block-settings-menu/plugin-block-settings-menu-item */ "./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-item.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "PluginBlockSettingsMenuItem", function() { return _components_block_settings_menu_plugin_block_settings_menu_item__WEBPACK_IMPORTED_MODULE_13__["default"]; });

/* harmony import */ var _components_sidebar_plugin_post_publish_panel__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./components/sidebar/plugin-post-publish-panel */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-publish-panel/index.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "PluginPostPublishPanel", function() { return _components_sidebar_plugin_post_publish_panel__WEBPACK_IMPORTED_MODULE_14__["default"]; });

/* harmony import */ var _components_sidebar_plugin_post_status_info__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./components/sidebar/plugin-post-status-info */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-status-info/index.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "PluginPostStatusInfo", function() { return _components_sidebar_plugin_post_status_info__WEBPACK_IMPORTED_MODULE_15__["default"]; });

/* harmony import */ var _components_sidebar_plugin_pre_publish_panel__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./components/sidebar/plugin-pre-publish-panel */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "PluginPrePublishPanel", function() { return _components_sidebar_plugin_pre_publish_panel__WEBPACK_IMPORTED_MODULE_16__["default"]; });

/* harmony import */ var _components_sidebar_plugin_sidebar__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./components/sidebar/plugin-sidebar */ "./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-sidebar/index.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "PluginSidebar", function() { return _components_sidebar_plugin_sidebar__WEBPACK_IMPORTED_MODULE_17__["default"]; });

/* harmony import */ var _components_header_plugin_sidebar_more_menu_item__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./components/header/plugin-sidebar-more-menu-item */ "./node_modules/@wordpress/edit-post/build-module/components/header/plugin-sidebar-more-menu-item/index.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "PluginSidebarMoreMenuItem", function() { return _components_header_plugin_sidebar_more_menu_item__WEBPACK_IMPORTED_MODULE_18__["default"]; });



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
 * @param {Object}  postType       Post type of the post to edit.
 * @param {Object}  postId         ID of the post to edit.
 * @param {Element} target         DOM node in which editor is rendered.
 * @param {?Object} settings       Editor settings object.
 * @param {Object}  overridePost   Post properties to override.
 */

function reinitializeEditor(postType, postId, target, settings, overridePost) {
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["unmountComponentAtNode"])(target);
  var reboot = reinitializeEditor.bind(null, postType, postId, target, settings, overridePost);
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["render"])(Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_editor__WEBPACK_IMPORTED_MODULE_12__["default"], {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    overridePost: overridePost,
    recovery: true
  }), target);
}
/**
 * Initializes and returns an instance of Editor.
 *
 * The return value of this function is not necessary if we change where we
 * call initializeEditor(). This is due to metaBox timing.
 *
 * @param {string}  id            Unique identifier for editor instance.
 * @param {Object}  postType      Post type of the post to edit.
 * @param {Object}  postId        ID of the post to edit.
 * @param {?Object} settings      Editor settings object.
 * @param {Object}  overridePost  Post properties to override.
 *
 * @return {Object} Editor interface.
 */

function initializeEditor(id, postType, postId, settings, overridePost) {
  var target = document.getElementById(id);
  var reboot = reinitializeEditor.bind(null, postType, postId, target, settings, overridePost);
  Object(_wordpress_block_library__WEBPACK_IMPORTED_MODULE_5__["registerCoreBlocks"])();
  Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["dispatch"])('core/nux').triggerGuide(['core/editor.inserter', 'core/editor.settings', 'core/editor.preview', 'core/editor.publish']);
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["render"])(Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_editor__WEBPACK_IMPORTED_MODULE_12__["default"], {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    overridePost: overridePost
  }), target);
  return {
    initializeMetaBoxes: function initializeMetaBoxes(metaBoxes) {
      _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7___default()('editor.initializeMetaBoxes', {
        alternative: 'setActiveMetaBoxLocations action (`core/edit-post`)',
        plugin: 'Gutenberg',
        version: '4.2'
      });
      _store__WEBPACK_IMPORTED_MODULE_10__["default"].dispatch(Object(_store_actions__WEBPACK_IMPORTED_MODULE_11__["initializeMetaBoxState"])(metaBoxes));
    }
  };
}








/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/keyboard-shortcuts.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/keyboard-shortcuts.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__);
/**
 * WordPress dependencies
 */

/* harmony default export */ __webpack_exports__["default"] = ({
  toggleEditorMode: {
    raw: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["rawShortcut"].secondary('m'),
    display: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcut"].secondary('m')
  },
  toggleSidebar: {
    raw: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["rawShortcut"].primaryShift(','),
    display: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["displayShortcut"].primaryShift(','),
    ariaLabel: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_0__["shortcutAriaLabel"].primaryShift(',')
  }
});


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/plugins/copy-content-menu-item/index.js":
/*!************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/plugins/copy-content-menu-item/index.js ***!
  \************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);


/**
 * WordPress dependencies
 */





function CopyContentMenuItem(_ref) {
  var editedPostContent = _ref.editedPostContent,
      hasCopied = _ref.hasCopied,
      setState = _ref.setState;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["ClipboardButton"], {
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
  }, hasCopied ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Copied!') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Copy All Content'));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withSelect"])(function (select) {
  return {
    editedPostContent: select('core/editor').getEditedPostAttribute('content')
  };
}), Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["withState"])({
  hasCopied: false
}))(CopyContentMenuItem));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/plugins/index.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/plugins/index.js ***!
  \*************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _copy_content_menu_item__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./copy-content-menu-item */ "./node_modules/@wordpress/edit-post/build-module/plugins/copy-content-menu-item/index.js");
/* harmony import */ var _keyboard_shortcuts_help_menu_item__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./keyboard-shortcuts-help-menu-item */ "./node_modules/@wordpress/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js");
/* harmony import */ var _publish_sidebar_toggle_menu_item__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./publish-sidebar-toggle-menu-item */ "./node_modules/@wordpress/edit-post/build-module/plugins/publish-sidebar-toggle-menu-item/index.js");
/* harmony import */ var _tips_toggle_menu_item__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./tips-toggle-menu-item */ "./node_modules/@wordpress/edit-post/build-module/plugins/tips-toggle-menu-item/index.js");
/* harmony import */ var _components_header_tools_more_menu_group__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../components/header/tools-more-menu-group */ "./node_modules/@wordpress/edit-post/build-module/components/header/tools-more-menu-group/index.js");


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */






Object(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_3__["registerPlugin"])('edit-post', {
  render: function render() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_header_tools_more_menu_group__WEBPACK_IMPORTED_MODULE_8__["default"], null, function (_ref) {
      var onClose = _ref.onClose;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["MenuItem"], {
        role: "menuitem",
        href: "edit.php?post_type=wp_block"
      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Manage All Reusable Blocks')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_tips_toggle_menu_item__WEBPACK_IMPORTED_MODULE_7__["default"], {
        onToggle: onClose
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_publish_sidebar_toggle_menu_item__WEBPACK_IMPORTED_MODULE_6__["default"], {
        onToggle: onClose
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_keyboard_shortcuts_help_menu_item__WEBPACK_IMPORTED_MODULE_5__["default"], {
        onSelect: onClose
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_copy_content_menu_item__WEBPACK_IMPORTED_MODULE_4__["default"], null));
    }));
  }
});


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js":
/*!***********************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js ***!
  \***********************************************************************************************************/
/*! exports provided: KeyboardShortcutsHelpMenuItem, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "KeyboardShortcutsHelpMenuItem", function() { return KeyboardShortcutsHelpMenuItem; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_4__);


/**
 * WordPress Dependencies
 */




function KeyboardShortcutsHelpMenuItem(_ref) {
  var openModal = _ref.openModal,
      onSelect = _ref.onSelect;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["MenuItem"], {
    onClick: function onClick() {
      onSelect();
      openModal('edit-post/keyboard-shortcut-help');
    },
    shortcut: _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_4__["displayShortcut"].access('h')
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Keyboard Shortcuts'));
}
/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openModal = _dispatch.openModal;

  return {
    openModal: openModal
  };
})(KeyboardShortcutsHelpMenuItem));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/plugins/publish-sidebar-toggle-menu-item/index.js":
/*!**********************************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/plugins/publish-sidebar-toggle-menu-item/index.js ***!
  \**********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);


/**
 * WordPress Dependencies
 */





var PublishSidebarToggleMenuItem = function PublishSidebarToggleMenuItem(_ref) {
  var onToggle = _ref.onToggle,
      isEnabled = _ref.isEnabled;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["MenuItem"], {
    className: 'edit-post__pre-publish-checks',
    icon: isEnabled && 'yes',
    isSelected: isEnabled,
    role: "menuitemcheckbox",
    onClick: onToggle
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Enable Pre-publish Checks'));
};

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withSelect"])(function (select) {
  return {
    isEnabled: select('core/editor').isPublishSidebarEnabled()
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withDispatch"])(function (dispatch, ownProps) {
  return {
    onToggle: function onToggle() {
      var _dispatch = dispatch('core/editor'),
          disablePublishSidebar = _dispatch.disablePublishSidebar,
          enablePublishSidebar = _dispatch.enablePublishSidebar;

      if (ownProps.isEnabled) {
        disablePublishSidebar();
      } else {
        enablePublishSidebar();
      }

      ownProps.onToggle();
    }
  };
})])(PublishSidebarToggleMenuItem));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/plugins/tips-toggle-menu-item/index.js":
/*!***********************************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/plugins/tips-toggle-menu-item/index.js ***!
  \***********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);


/**
 * WordPress Dependencies
 */





function TipsToggleMenuItem(_ref) {
  var onToggle = _ref.onToggle,
      isActive = _ref.isActive;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["MenuItem"], {
    icon: isActive && 'yes',
    isSelected: isActive,
    role: "menuitemcheckbox",
    onClick: onToggle
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show Tips'));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["withSelect"])(function (select) {
  return {
    isActive: select('core/nux').areTipsEnabled()
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["withDispatch"])(function (dispatch, ownProps) {
  return {
    onToggle: function onToggle() {
      var _dispatch = dispatch('core/nux'),
          disableTips = _dispatch.disableTips,
          enableTips = _dispatch.enableTips;

      if (ownProps.isActive) {
        disableTips();
      } else {
        enableTips();
      }

      ownProps.onToggle();
    }
  };
})])(TipsToggleMenuItem));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/store/actions.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/store/actions.js ***!
  \*************************************************************************/
/*! exports provided: openGeneralSidebar, closeGeneralSidebar, openModal, closeModal, openPublishSidebar, closePublishSidebar, togglePublishSidebar, toggleGeneralSidebarEditorPanel, toggleFeature, switchEditorMode, togglePinnedPluginItem, initializeMetaBoxState, setActiveMetaBoxLocations, requestMetaBoxUpdates, metaBoxUpdatesSuccess, setMetaBoxSavedData */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "openGeneralSidebar", function() { return openGeneralSidebar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "closeGeneralSidebar", function() { return closeGeneralSidebar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "openModal", function() { return openModal; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "closeModal", function() { return closeModal; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "openPublishSidebar", function() { return openPublishSidebar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "closePublishSidebar", function() { return closePublishSidebar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "togglePublishSidebar", function() { return togglePublishSidebar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toggleGeneralSidebarEditorPanel", function() { return toggleGeneralSidebarEditorPanel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toggleFeature", function() { return toggleFeature; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "switchEditorMode", function() { return switchEditorMode; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "togglePinnedPluginItem", function() { return togglePinnedPluginItem; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "initializeMetaBoxState", function() { return initializeMetaBoxState; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setActiveMetaBoxLocations", function() { return setActiveMetaBoxLocations; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "requestMetaBoxUpdates", function() { return requestMetaBoxUpdates; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "metaBoxUpdatesSuccess", function() { return metaBoxUpdatesSuccess; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setMetaBoxSavedData", function() { return setMetaBoxSavedData; });
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/deprecated */ "@wordpress/deprecated");
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__);
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Returns an action object used in signalling that the user opened an editor sidebar.
 *
 * @param {string} name Sidebar name to be opened.
 *
 * @return {Object} Action object.
 */

function openGeneralSidebar(name) {
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

function closeGeneralSidebar() {
  return {
    type: 'CLOSE_GENERAL_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user opened an editor sidebar.
 *
 * @param {string} name A string that uniquely identifies the modal.
 *
 * @return {Object} Action object.
 */

function openModal(name) {
  return {
    type: 'OPEN_MODAL',
    name: name
  };
}
/**
 * Returns an action object signalling that the user closed the sidebar.
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
 * Returns an action object used in signalling that use toggled a panel in the editor.
 *
 * @param {string}  panel The panel to toggle.
 * @return {Object} Action object.
*/

function toggleGeneralSidebarEditorPanel(panel) {
  return {
    type: 'TOGGLE_GENERAL_SIDEBAR_EDITOR_PANEL',
    panel: panel
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
 * Returns an action object used to check the state of meta boxes at a location.
 *
 * This should only be fired once to initialize meta box state. If a meta box
 * area is empty, this will set the store state to indicate that React should
 * not render the meta box area.
 *
 * Example: metaBoxes = { side: true, normal: false }.
 *
 * This indicates that the sidebar has a meta box but the normal area does not.
 *
 * @param {Object} metaBoxes Whether meta box locations are active.
 *
 * @return {Object} Action object.
 */

function initializeMetaBoxState(metaBoxes) {
  _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default()('initializeMetaBoxState action (`core/edit-post`)', {
    alternative: 'setActiveMetaBoxLocations',
    plugin: 'Gutenberg',
    version: '4.2'
  });
  var locations = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["reduce"])(metaBoxes, function (result, isActive, location) {
    if (isActive) {
      result = result.concat(location);
    }

    return result;
  }, []);
  return setActiveMetaBoxLocations(locations);
}
/**
 * Returns an action object used in signaling that the active meta box
 * locations have changed.
 *
 * @param {string[]} locations New active meta box locations.
 *
 * @return {Object} Action object.
 */

function setActiveMetaBoxLocations(locations) {
  return {
    type: 'SET_ACTIVE_META_BOX_LOCATIONS',
    locations: locations
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
/**
 * Returns an action object used to set the saved meta boxes data.
 * This is used to check if the meta boxes have been touched when leaving the editor.
 *
 * @param   {Object} dataPerLocation Meta Boxes Data per location.
 *
 * @return {Object} Action object.
 */

function setMetaBoxSavedData(dataPerLocation) {
  _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default()('setMetaBoxSavedData action (`core/edit-post`)', {
    plugin: 'Gutenberg',
    version: '4.2'
  });
  return {
    type: 'META_BOX_SET_SAVED_DATA',
    dataPerLocation: dataPerLocation
  };
}


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/store/defaults.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/store/defaults.js ***!
  \**************************************************************************/
/*! exports provided: PREFERENCES_DEFAULTS */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PREFERENCES_DEFAULTS", function() { return PREFERENCES_DEFAULTS; });
var PREFERENCES_DEFAULTS = {
  editorMode: 'visual',
  isGeneralSidebarDismissed: false,
  panels: {
    'post-status': true
  },
  features: {
    fixedToolbar: false
  },
  pinnedPluginItems: {}
};


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/store/effects.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/store/effects.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/slicedToArray */ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_a11y__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/a11y */ "@wordpress/a11y");
/* harmony import */ var _wordpress_a11y__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_a11y__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./actions */ "./node_modules/@wordpress/edit-post/build-module/store/actions.js");
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./selectors */ "./node_modules/@wordpress/edit-post/build-module/store/selectors.js");
/* harmony import */ var _utils_meta_boxes__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../utils/meta-boxes */ "./node_modules/@wordpress/edit-post/build-module/utils/meta-boxes.js");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./utils */ "./node_modules/@wordpress/edit-post/build-module/store/utils.js");



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





var effects = {
  SET_ACTIVE_META_BOX_LOCATIONS: function SET_ACTIVE_META_BOX_LOCATIONS(action, store) {
    var hasActiveMetaBoxes = action.locations.length > 0;

    if (!hasActiveMetaBoxes) {
      return;
    } // Allow toggling metaboxes panels
    // We need to wait for all scripts to load
    // If the meta box loads the post script, it will already trigger this.
    // After merge in Core, make sure to drop the timeout and update the postboxes script
    // to avoid the double binding.


    setTimeout(function () {
      var postType = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["select"])('core/editor').getCurrentPostType();

      if (window.postboxes.page !== postType) {
        window.postboxes.add_postbox_toggles(postType);
      }
    });
    var wasSavingPost = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["select"])('core/editor').isSavingPost();
    var wasAutosavingPost = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["select"])('core/editor').isAutosavingPost(); // Save metaboxes when performing a full save on the post.

    Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["subscribe"])(function () {
      var isSavingPost = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["select"])('core/editor').isSavingPost();
      var isAutosavingPost = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["select"])('core/editor').isAutosavingPost(); // Save metaboxes on save completion when past save wasn't an autosave.

      var shouldTriggerMetaboxesSave = wasSavingPost && !wasAutosavingPost && !isSavingPost && !isAutosavingPost; // Save current state for next inspection.

      wasSavingPost = isSavingPost;
      wasAutosavingPost = isAutosavingPost;

      if (shouldTriggerMetaboxesSave) {
        store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_7__["requestMetaBoxUpdates"])());
      }
    });
  },
  REQUEST_META_BOX_UPDATES: function REQUEST_META_BOX_UPDATES(action, store) {
    var state = store.getState(); // Additional data needed for backwards compatibility.
    // If we do not provide this data, the post will be overridden with the default values.

    var post = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["select"])('core/editor').getCurrentPost(state);
    var additionalData = [post.comment_status ? ['comment_status', post.comment_status] : false, post.ping_status ? ['ping_status', post.ping_status] : false, post.sticky ? ['sticky', post.sticky] : false, ['post_author', post.author]].filter(Boolean); // We gather all the metaboxes locations data and the base form data

    var baseFormData = new window.FormData(document.querySelector('.metabox-base-form'));
    var formDataToMerge = [baseFormData].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__["default"])(Object(_selectors__WEBPACK_IMPORTED_MODULE_8__["getActiveMetaBoxLocations"])(state).map(function (location) {
      return new window.FormData(Object(_utils_meta_boxes__WEBPACK_IMPORTED_MODULE_9__["getMetaBoxContainer"])(location));
    }))); // Merge all form data objects into a single one.

    var formData = Object(lodash__WEBPACK_IMPORTED_MODULE_2__["reduce"])(formDataToMerge, function (memo, currentFormData) {
      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        for (var _iterator = currentFormData[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
          var _step$value = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__["default"])(_step.value, 2),
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
      var _ref2 = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref, 2),
          key = _ref2[0],
          value = _ref2[1];

      return formData.append(key, value);
    }); // Save the metaboxes

    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_6___default()({
      url: window._wpMetaBoxUrl,
      method: 'POST',
      body: formData,
      parse: false
    }).then(function () {
      return store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_7__["metaBoxUpdatesSuccess"])());
    });
  },
  SWITCH_MODE: function SWITCH_MODE(action) {
    // Unselect blocks when we switch to the code editor.
    if (action.mode !== 'visual') {
      Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["dispatch"])('core/editor').clearSelectedBlock();
    }

    var message = action.mode === 'visual' ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Visual editor selected') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Code editor selected');
    Object(_wordpress_a11y__WEBPACK_IMPORTED_MODULE_4__["speak"])(message, 'assertive');
  },
  INIT: function INIT(_, store) {
    // Select the block settings tab when the selected block changes
    Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["subscribe"])(Object(_utils__WEBPACK_IMPORTED_MODULE_10__["onChangeListener"])(function () {
      return !!Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["select"])('core/editor').getBlockSelectionStart();
    }, function (hasBlockSelection) {
      if (!Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["select"])('core/edit-post').isEditorSidebarOpened()) {
        return;
      }

      if (hasBlockSelection) {
        store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_7__["openGeneralSidebar"])('edit-post/block'));
      } else {
        store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_7__["openGeneralSidebar"])('edit-post/document'));
      }
    }));

    var isMobileViewPort = function isMobileViewPort() {
      return Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["select"])('core/viewport').isViewportMatch('< medium');
    };

    var adjustSidebar = function () {
      // contains the sidebar we close when going to viewport sizes lower than medium.
      // This allows to reopen it when going again to viewport sizes greater than medium.
      var sidebarToReOpenOnExpand = null;
      return function (isSmall) {
        if (isSmall) {
          sidebarToReOpenOnExpand = Object(_selectors__WEBPACK_IMPORTED_MODULE_8__["getActiveGeneralSidebarName"])(store.getState());

          if (sidebarToReOpenOnExpand) {
            store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_7__["closeGeneralSidebar"])());
          }
        } else if (sidebarToReOpenOnExpand && !Object(_selectors__WEBPACK_IMPORTED_MODULE_8__["getActiveGeneralSidebarName"])(store.getState())) {
          store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_7__["openGeneralSidebar"])(sidebarToReOpenOnExpand));
        }
      };
    }();

    adjustSidebar(isMobileViewPort()); // Collapse sidebar when viewport shrinks.
    // Reopen sidebar it if viewport expands and it was closed because of a previous shrink.

    Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["subscribe"])(Object(_utils__WEBPACK_IMPORTED_MODULE_10__["onChangeListener"])(isMobileViewPort, adjustSidebar));
  }
};
/* harmony default export */ __webpack_exports__["default"] = (effects);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/store/index.js":
/*!***********************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/store/index.js ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _reducer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./reducer */ "./node_modules/@wordpress/edit-post/build-module/store/reducer.js");
/* harmony import */ var _middlewares__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./middlewares */ "./node_modules/@wordpress/edit-post/build-module/store/middlewares.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./actions */ "./node_modules/@wordpress/edit-post/build-module/store/actions.js");
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./selectors */ "./node_modules/@wordpress/edit-post/build-module/store/selectors.js");
/**
 * WordPress Dependencies
 */

/**
 * Internal dependencies
 */





var store = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__["registerStore"])('core/edit-post', {
  reducer: _reducer__WEBPACK_IMPORTED_MODULE_1__["default"],
  actions: _actions__WEBPACK_IMPORTED_MODULE_3__,
  selectors: _selectors__WEBPACK_IMPORTED_MODULE_4__,
  persist: ['preferences']
});
Object(_middlewares__WEBPACK_IMPORTED_MODULE_2__["default"])(store);
store.dispatch({
  type: 'INIT'
});
/* harmony default export */ __webpack_exports__["default"] = (store);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/store/middlewares.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/store/middlewares.js ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var refx__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! refx */ "./node_modules/refx/refx.js");
/* harmony import */ var refx__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(refx__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _effects__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./effects */ "./node_modules/@wordpress/edit-post/build-module/store/effects.js");


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
  var middlewares = [refx__WEBPACK_IMPORTED_MODULE_2___default()(_effects__WEBPACK_IMPORTED_MODULE_3__["default"])];

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
  enhancedDispatch = lodash__WEBPACK_IMPORTED_MODULE_1__["flowRight"].apply(void 0, Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(chain))(store.dispatch);
  store.dispatch = enhancedDispatch;
  return store;
}

/* harmony default export */ __webpack_exports__["default"] = (applyMiddlewares);


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/store/reducer.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/store/reducer.js ***!
  \*************************************************************************/
/*! exports provided: DEFAULT_ACTIVE_GENERAL_SIDEBAR, preferences, activeGeneralSidebar, panel, activeModal, publishSidebarActive, isSavingMetaBoxes, activeMetaBoxLocations, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "DEFAULT_ACTIVE_GENERAL_SIDEBAR", function() { return DEFAULT_ACTIVE_GENERAL_SIDEBAR; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "preferences", function() { return preferences; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "activeGeneralSidebar", function() { return activeGeneralSidebar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "panel", function() { return panel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "activeModal", function() { return activeModal; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "publishSidebarActive", function() { return publishSidebarActive; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isSavingMetaBoxes", function() { return isSavingMetaBoxes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "activeMetaBoxLocations", function() { return activeMetaBoxLocations; });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _defaults__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./defaults */ "./node_modules/@wordpress/edit-post/build-module/store/defaults.js");



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

var preferences = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["combineReducers"])({
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
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _defaults__WEBPACK_IMPORTED_MODULE_4__["PREFERENCES_DEFAULTS"].panels;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'TOGGLE_GENERAL_SIDEBAR_EDITOR_PANEL') {
      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, state, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({}, action.panel, !state[action.panel]));
    }

    return state;
  },
  features: function features() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _defaults__WEBPACK_IMPORTED_MODULE_4__["PREFERENCES_DEFAULTS"].features;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'TOGGLE_FEATURE') {
      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, state, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({}, action.feature, !state[action.feature]));
    }

    return state;
  },
  editorMode: function editorMode() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _defaults__WEBPACK_IMPORTED_MODULE_4__["PREFERENCES_DEFAULTS"].editorMode;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'SWITCH_MODE') {
      return action.mode;
    }

    return state;
  },
  pinnedPluginItems: function pinnedPluginItems() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _defaults__WEBPACK_IMPORTED_MODULE_4__["PREFERENCES_DEFAULTS"].pinnedPluginItems;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'TOGGLE_PINNED_PLUGIN_ITEM') {
      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, state, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({}, action.pluginName, !Object(lodash__WEBPACK_IMPORTED_MODULE_2__["get"])(state, [action.pluginName], true)));
    }

    return state;
  }
});
/**
 * Reducer returning the next active general sidebar state. The active general
 * sidebar is a unique name to identify either an editor or plugin sidebar.
 *
 * @param {?string} state  Current state.
 * @param {Object}  action Action object.
 *
 * @return {?string} Updated state.
 */

function activeGeneralSidebar() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_ACTIVE_GENERAL_SIDEBAR;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_GENERAL_SIDEBAR':
      return action.name;
  }

  return state;
}
function panel() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'document';
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_ACTIVE_PANEL':
      return action.panel;
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
 * Reducer returning an array of active meta box locations after the given
 * action.
 *
 * @param {boolean} state  Previous state.
 * @param {Object}  action Action Object.
 *
 * @return {string[]} Updated state.
 */

function activeMetaBoxLocations() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_ACTIVE_META_BOX_LOCATIONS':
      return action.locations;
  }

  return state;
}
/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["combineReducers"])({
  preferences: preferences,
  activeGeneralSidebar: activeGeneralSidebar,
  panel: panel,
  activeModal: activeModal,
  publishSidebarActive: publishSidebarActive,
  activeMetaBoxLocations: activeMetaBoxLocations,
  isSavingMetaBoxes: isSavingMetaBoxes
}));


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/store/selectors.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/store/selectors.js ***!
  \***************************************************************************/
/*! exports provided: getEditorMode, isEditorSidebarOpened, isPluginSidebarOpened, getActiveGeneralSidebarName, getPreferences, getPreference, isPublishSidebarOpened, isEditorSidebarPanelOpened, isModalActive, isFeatureActive, isPluginItemPinned, getMetaBoxes, getActiveMetaBoxLocations, isMetaBoxLocationActive, getMetaBox, hasMetaBoxes, isSavingMetaBoxes */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getEditorMode", function() { return getEditorMode; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEditorSidebarOpened", function() { return isEditorSidebarOpened; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isPluginSidebarOpened", function() { return isPluginSidebarOpened; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getActiveGeneralSidebarName", function() { return getActiveGeneralSidebarName; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getPreferences", function() { return getPreferences; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getPreference", function() { return getPreference; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isPublishSidebarOpened", function() { return isPublishSidebarOpened; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEditorSidebarPanelOpened", function() { return isEditorSidebarPanelOpened; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isModalActive", function() { return isModalActive; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFeatureActive", function() { return isFeatureActive; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isPluginItemPinned", function() { return isPluginItemPinned; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getMetaBoxes", function() { return getMetaBoxes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getActiveMetaBoxLocations", function() { return getActiveMetaBoxLocations; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isMetaBoxLocationActive", function() { return isMetaBoxLocationActive; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getMetaBox", function() { return getMetaBox; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "hasMetaBoxes", function() { return hasMetaBoxes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isSavingMetaBoxes", function() { return isSavingMetaBoxes; });
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/deprecated */ "@wordpress/deprecated");
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/**
 * WordPress dependencies
 */

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

function isEditorSidebarOpened(state) {
  var activeGeneralSidebar = getActiveGeneralSidebarName(state);
  return Object(lodash__WEBPACK_IMPORTED_MODULE_1__["includes"])(['edit-post/document', 'edit-post/block'], activeGeneralSidebar);
}
/**
 * Returns true if the plugin sidebar is opened.
 *
 * @param {Object} state Global application state
 * @return {boolean}     Whether the plugin sidebar is opened.
 */

function isPluginSidebarOpened(state) {
  var activeGeneralSidebar = getActiveGeneralSidebarName(state);
  return !!activeGeneralSidebar && !isEditorSidebarOpened(state);
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

function isPublishSidebarOpened(state) {
  return state.publishSidebarActive;
}
/**
 * Returns true if the editor sidebar panel is open, or false otherwise.
 *
 * @param  {Object}  state Global application state.
 * @param  {string}  panel Sidebar panel name.
 *
 * @return {boolean} Whether the sidebar panel is open.
 */

function isEditorSidebarPanelOpened(state, panel) {
  var panels = getPreference(state, 'panels');
  return panels ? !!panels[panel] : false;
}
/**
 * Returns true if a modal is active, or false otherwise.
 *
 * @param  {Object}  state 	   Global application state.
 * @param  {string}  modalName A string that uniquely identifies the modal.
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
  return Object(lodash__WEBPACK_IMPORTED_MODULE_1__["get"])(pinnedPluginItems, [pluginName], true);
}
/**
 * Returns the state of legacy meta boxes.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} State of meta boxes.
 */

function getMetaBoxes(state) {
  _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0___default()('getMetaBox selector (`core/edit-post`)', {
    alternative: 'getActiveMetaBoxLocations selector',
    plugin: 'Gutenberg',
    version: '4.2'
  });
  return ['normal', 'side', 'advanced'].reduce(function (result, location) {
    result[location] = {
      isActive: isMetaBoxLocationActive(state, location)
    };
    return result;
  }, {});
}
/**
 * Returns an array of active meta box locations.
 *
 * @param {Object} state Post editor state.
 *
 * @return {string[]} Active meta box locations.
 */

function getActiveMetaBoxLocations(state) {
  return state.activeMetaBoxLocations;
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
  return getActiveMetaBoxLocations(state).includes(location);
}
/**
 * Returns the state of legacy meta boxes.
 *
 * @param {Object} state    Global application state.
 * @param {string} location Location of the meta box.
 *
 * @return {Object} State of meta box at specified location.
 */

function getMetaBox(state, location) {
  _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0___default()('getMetaBox selector (`core/edit-post`)', {
    alternative: 'isMetaBoxLocationActive selector',
    plugin: 'Gutenberg',
    version: '4.2'
  });
  return getMetaBoxes(state)[location];
}
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

function isSavingMetaBoxes(state) {
  return state.isSavingMetaBoxes;
}


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/store/utils.js":
/*!***********************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/store/utils.js ***!
  \***********************************************************************/
/*! exports provided: onChangeListener */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "onChangeListener", function() { return onChangeListener; });
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


/***/ }),

/***/ "./node_modules/@wordpress/edit-post/build-module/utils/meta-boxes.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/edit-post/build-module/utils/meta-boxes.js ***!
  \****************************************************************************/
/*! exports provided: getMetaBoxContainer */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getMetaBoxContainer", function() { return getMetaBoxContainer; });
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

/***/ "./node_modules/refx/refx.js":
/*!***********************************!*\
  !*** ./node_modules/refx/refx.js ***!
  \***********************************/
/*! no static exports found */
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

/***/ "@wordpress/a11y":
/*!***************************************!*\
  !*** external {"this":["wp","a11y"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["a11y"]; }());

/***/ }),

/***/ "@wordpress/api-fetch":
/*!*******************************************!*\
  !*** external {"this":["wp","apiFetch"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ "@wordpress/block-library":
/*!***********************************************!*\
  !*** external {"this":["wp","blockLibrary"]} ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockLibrary"]; }());

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

/***/ "@wordpress/hooks":
/*!****************************************!*\
  !*** external {"this":["wp","hooks"]} ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

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

/***/ "@wordpress/nux":
/*!**************************************!*\
  !*** external {"this":["wp","nux"]} ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["nux"]; }());

/***/ }),

/***/ "@wordpress/plugins":
/*!******************************************!*\
  !*** external {"this":["wp","plugins"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["plugins"]; }());

/***/ }),

/***/ "@wordpress/url":
/*!**************************************!*\
  !*** external {"this":["wp","url"]} ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

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

/***/ })

/******/ });
//# sourceMappingURL=edit-post.js.map