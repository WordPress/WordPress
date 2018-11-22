this["wp"] = this["wp"] || {}; this["wp"]["formatLibrary"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/format-library/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _assertThisInitialized; });\nfunction _assertThisInitialized(self) {\n  if (self === void 0) {\n    throw new ReferenceError(\"this hasn't been initialised - super() hasn't been called\");\n  }\n\n  return self;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _classCallCheck; });\nfunction _classCallCheck(instance, Constructor) {\n  if (!(instance instanceof Constructor)) {\n    throw new TypeError(\"Cannot call a class as a function\");\n  }\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/classCallCheck.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/createClass.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/createClass.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _createClass; });\nfunction _defineProperties(target, props) {\n  for (var i = 0; i < props.length; i++) {\n    var descriptor = props[i];\n    descriptor.enumerable = descriptor.enumerable || false;\n    descriptor.configurable = true;\n    if (\"value\" in descriptor) descriptor.writable = true;\n    Object.defineProperty(target, descriptor.key, descriptor);\n  }\n}\n\nfunction _createClass(Constructor, protoProps, staticProps) {\n  if (protoProps) _defineProperties(Constructor.prototype, protoProps);\n  if (staticProps) _defineProperties(Constructor, staticProps);\n  return Constructor;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/createClass.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _getPrototypeOf; });\nfunction _getPrototypeOf(o) {\n  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {\n    return o.__proto__ || Object.getPrototypeOf(o);\n  };\n  return _getPrototypeOf(o);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/inherits.js":
/*!*************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/inherits.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _inherits; });\n/* harmony import */ var _setPrototypeOf__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./setPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js\");\n\nfunction _inherits(subClass, superClass) {\n  if (typeof superClass !== \"function\" && superClass !== null) {\n    throw new TypeError(\"Super expression must either be null or a function\");\n  }\n\n  subClass.prototype = Object.create(superClass && superClass.prototype, {\n    constructor: {\n      value: subClass,\n      writable: true,\n      configurable: true\n    }\n  });\n  if (superClass) Object(_setPrototypeOf__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(subClass, superClass);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/inherits.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _objectWithoutProperties; });\n/* harmony import */ var _objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./objectWithoutPropertiesLoose */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js\");\n\nfunction _objectWithoutProperties(source, excluded) {\n  if (source == null) return {};\n  var target = Object(_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(source, excluded);\n  var key, i;\n\n  if (Object.getOwnPropertySymbols) {\n    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);\n\n    for (i = 0; i < sourceSymbolKeys.length; i++) {\n      key = sourceSymbolKeys[i];\n      if (excluded.indexOf(key) >= 0) continue;\n      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;\n      target[key] = source[key];\n    }\n  }\n\n  return target;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _objectWithoutPropertiesLoose; });\nfunction _objectWithoutPropertiesLoose(source, excluded) {\n  if (source == null) return {};\n  var target = {};\n  var sourceKeys = Object.keys(source);\n  var key, i;\n\n  for (i = 0; i < sourceKeys.length; i++) {\n    key = sourceKeys[i];\n    if (excluded.indexOf(key) >= 0) continue;\n    target[key] = source[key];\n  }\n\n  return target;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _possibleConstructorReturn; });\n/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../helpers/esm/typeof */ \"./node_modules/@babel/runtime/helpers/esm/typeof.js\");\n/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n\n\nfunction _possibleConstructorReturn(self, call) {\n  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(call) === \"object\" || typeof call === \"function\")) {\n    return call;\n  }\n\n  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(self);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _setPrototypeOf; });\nfunction _setPrototypeOf(o, p) {\n  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {\n    o.__proto__ = p;\n    return o;\n  };\n\n  return _setPrototypeOf(o, p);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _typeof; });\nfunction _typeof2(obj) { if (typeof Symbol === \"function\" && typeof Symbol.iterator === \"symbol\") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === \"function\" && obj.constructor === Symbol && obj !== Symbol.prototype ? \"symbol\" : typeof obj; }; } return _typeof2(obj); }\n\nfunction _typeof(obj) {\n  if (typeof Symbol === \"function\" && _typeof2(Symbol.iterator) === \"symbol\") {\n    _typeof = function _typeof(obj) {\n      return _typeof2(obj);\n    };\n  } else {\n    _typeof = function _typeof(obj) {\n      return obj && typeof Symbol === \"function\" && obj.constructor === Symbol && obj !== Symbol.prototype ? \"symbol\" : _typeof2(obj);\n    };\n  }\n\n  return _typeof(obj);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/typeof.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/bold/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/bold/index.js ***!
  \***************************************************************************/
/*! exports provided: bold */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"bold\", function() { return bold; });\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ \"@wordpress/editor\");\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);\n\n\n/**\n * WordPress dependencies\n */\n\n\n\n\nvar name = 'core/bold';\nvar bold = {\n  name: name,\n  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Bold'),\n  tagName: 'strong',\n  className: null,\n  edit: function edit(_ref) {\n    var isActive = _ref.isActive,\n        value = _ref.value,\n        onChange = _ref.onChange;\n\n    var onToggle = function onToggle() {\n      return onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__[\"toggleFormat\"])(value, {\n        type: name\n      }));\n    };\n\n    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"Fragment\"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__[\"RichTextShortcut\"], {\n      type: \"primary\",\n      character: \"b\",\n      onUse: onToggle\n    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__[\"RichTextToolbarButton\"], {\n      name: \"bold\",\n      icon: \"editor-bold\",\n      title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Bold'),\n      onClick: onToggle,\n      isActive: isActive,\n      shortcutType: \"primary\",\n      shortcutCharacter: \"b\"\n    }));\n  }\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/bold/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/code/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/code/index.js ***!
  \***************************************************************************/
/*! exports provided: code */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"code\", function() { return code; });\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ \"@wordpress/editor\");\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);\n\n\n/**\n * WordPress dependencies\n */\n\n\n\nvar name = 'core/code';\nvar code = {\n  name: name,\n  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Code'),\n  tagName: 'code',\n  className: null,\n  edit: function edit(_ref) {\n    var value = _ref.value,\n        onChange = _ref.onChange;\n\n    var onToggle = function onToggle() {\n      return onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__[\"toggleFormat\"])(value, {\n        type: name\n      }));\n    };\n\n    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__[\"RichTextShortcut\"], {\n      type: \"access\",\n      character: \"x\",\n      onUse: onToggle\n    });\n  }\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/code/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/image/index.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/image/index.js ***!
  \****************************************************************************/
/*! exports provided: image */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"image\", function() { return image; });\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/editor */ \"@wordpress/editor\");\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_10__);\n\n\n\n\n\n\n\n\n/**\n * WordPress dependencies\n */\n\n\n\n\n\nvar ALLOWED_MEDIA_TYPES = ['image'];\nvar name = 'core/image';\nvar image = {\n  name: name,\n  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Image'),\n  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('photo'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('media')],\n  object: true,\n  tagName: 'img',\n  className: null,\n  attributes: {\n    className: 'class',\n    style: 'style',\n    url: 'src',\n    alt: 'alt'\n  },\n  edit:\n  /*#__PURE__*/\n  function (_Component) {\n    Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(ImageEdit, _Component);\n\n    function ImageEdit() {\n      var _this;\n\n      Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, ImageEdit);\n\n      _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(ImageEdit).apply(this, arguments));\n      _this.openModal = _this.openModal.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n      _this.closeModal = _this.closeModal.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n      _this.state = {\n        modal: false\n      };\n      return _this;\n    }\n\n    Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(ImageEdit, [{\n      key: \"openModal\",\n      value: function openModal() {\n        this.setState({\n          modal: true\n        });\n      }\n    }, {\n      key: \"closeModal\",\n      value: function closeModal() {\n        this.setState({\n          modal: false\n        });\n      }\n    }, {\n      key: \"render\",\n      value: function render() {\n        var _this2 = this;\n\n        var _this$props = this.props,\n            value = _this$props.value,\n            onChange = _this$props.onChange;\n        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_10__[\"MediaUploadCheck\"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_10__[\"RichTextInserterItem\"], {\n          name: name,\n          icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__[\"SVG\"], {\n            xmlns: \"http://www.w3.org/2000/svg\",\n            viewBox: \"0 0 24 24\"\n          }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__[\"Path\"], {\n            d: \"M4 16h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2zM4 5h10v9H4V5zm14 9v2h4v-2h-4zM2 20h20v-2H2v2zm6.4-8.8L7 9.4 5 12h8l-2.6-3.4-2 2.6z\"\n          })),\n          title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Inline Image'),\n          onClick: this.openModal\n        }), this.state.modal && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_10__[\"MediaUpload\"], {\n          allowedTypes: ALLOWED_MEDIA_TYPES,\n          onSelect: function onSelect(_ref) {\n            var id = _ref.id,\n                url = _ref.url,\n                alt = _ref.alt,\n                width = _ref.width;\n\n            _this2.closeModal();\n\n            onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__[\"insertObject\"])(value, {\n              type: name,\n              attributes: {\n                className: \"wp-image-\".concat(id),\n                style: \"width: \".concat(Math.min(width, 150), \"px;\"),\n                url: url,\n                alt: alt\n              }\n            }));\n          },\n          onClose: this.closeModal,\n          render: function render(_ref2) {\n            var open = _ref2.open;\n            open();\n            return null;\n          }\n        }));\n      }\n    }]);\n\n    return ImageEdit;\n  }(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"Component\"])\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/image/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/index.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/index.js ***!
  \**********************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js\");\n/* harmony import */ var _bold__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./bold */ \"./node_modules/@wordpress/format-library/build-module/bold/index.js\");\n/* harmony import */ var _code__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./code */ \"./node_modules/@wordpress/format-library/build-module/code/index.js\");\n/* harmony import */ var _image__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./image */ \"./node_modules/@wordpress/format-library/build-module/image/index.js\");\n/* harmony import */ var _italic__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./italic */ \"./node_modules/@wordpress/format-library/build-module/italic/index.js\");\n/* harmony import */ var _link__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./link */ \"./node_modules/@wordpress/format-library/build-module/link/index.js\");\n/* harmony import */ var _strikethrough__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./strikethrough */ \"./node_modules/@wordpress/format-library/build-module/strikethrough/index.js\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__);\n\n\n/**\n * Internal dependencies\n */\n\n\n\n\n\n\n/**\n * WordPress dependencies\n */\n\n\n[_bold__WEBPACK_IMPORTED_MODULE_1__[\"bold\"], _code__WEBPACK_IMPORTED_MODULE_2__[\"code\"], _image__WEBPACK_IMPORTED_MODULE_3__[\"image\"], _italic__WEBPACK_IMPORTED_MODULE_4__[\"italic\"], _link__WEBPACK_IMPORTED_MODULE_5__[\"link\"], _strikethrough__WEBPACK_IMPORTED_MODULE_6__[\"strikethrough\"]].forEach(function (_ref) {\n  var name = _ref.name,\n      settings = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_ref, [\"name\"]);\n\n  return Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_7__[\"registerFormatType\"])(name, settings);\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/italic/index.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/italic/index.js ***!
  \*****************************************************************************/
/*! exports provided: italic */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"italic\", function() { return italic; });\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ \"@wordpress/editor\");\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);\n\n\n/**\n * WordPress dependencies\n */\n\n\n\n\nvar name = 'core/italic';\nvar italic = {\n  name: name,\n  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Italic'),\n  tagName: 'em',\n  className: null,\n  edit: function edit(_ref) {\n    var isActive = _ref.isActive,\n        value = _ref.value,\n        onChange = _ref.onChange;\n\n    var onToggle = function onToggle() {\n      return onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__[\"toggleFormat\"])(value, {\n        type: name\n      }));\n    };\n\n    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"Fragment\"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__[\"RichTextShortcut\"], {\n      type: \"primary\",\n      character: \"i\",\n      onUse: onToggle\n    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__[\"RichTextToolbarButton\"], {\n      name: \"italic\",\n      icon: \"editor-italic\",\n      title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Italic'),\n      onClick: onToggle,\n      isActive: isActive,\n      shortcutType: \"primary\",\n      shortcutCharacter: \"i\"\n    }));\n  }\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/italic/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/link/index.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/link/index.js ***!
  \***************************************************************************/
/*! exports provided: link */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"link\", function() { return link; });\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/url */ \"@wordpress/url\");\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/editor */ \"@wordpress/editor\");\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__);\n/* harmony import */ var _inline__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./inline */ \"./node_modules/@wordpress/format-library/build-module/link/inline.js\");\n\n\n\n\n\n\n\n\n/**\n * WordPress dependencies\n */\n\n\n\n\n\n\n/**\n * Internal dependencies\n */\n\n\nvar name = 'core/link';\nvar link = {\n  name: name,\n  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__[\"__\"])('Link'),\n  tagName: 'a',\n  className: null,\n  attributes: {\n    url: 'href',\n    target: 'target'\n  },\n  edit: Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__[\"withSpokenMessages\"])(\n  /*#__PURE__*/\n  function (_Component) {\n    Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(LinkEdit, _Component);\n\n    function LinkEdit() {\n      var _this;\n\n      Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, LinkEdit);\n\n      _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(LinkEdit).apply(this, arguments));\n      _this.addLink = _this.addLink.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n      _this.stopAddingLink = _this.stopAddingLink.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n      _this.onRemoveFormat = _this.onRemoveFormat.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n      _this.state = {\n        addingLink: false\n      };\n      return _this;\n    }\n\n    Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(LinkEdit, [{\n      key: \"addLink\",\n      value: function addLink() {\n        var _this$props = this.props,\n            value = _this$props.value,\n            onChange = _this$props.onChange;\n        var text = Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__[\"getTextContent\"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__[\"slice\"])(value));\n\n        if (text && Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_10__[\"isURL\"])(text)) {\n          onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__[\"applyFormat\"])(value, {\n            type: name,\n            attributes: {\n              url: text\n            }\n          }));\n        } else {\n          this.setState({\n            addingLink: true\n          });\n        }\n      }\n    }, {\n      key: \"stopAddingLink\",\n      value: function stopAddingLink() {\n        this.setState({\n          addingLink: false\n        });\n      }\n    }, {\n      key: \"onRemoveFormat\",\n      value: function onRemoveFormat() {\n        var _this$props2 = this.props,\n            value = _this$props2.value,\n            onChange = _this$props2.onChange,\n            speak = _this$props2.speak;\n        onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__[\"removeFormat\"])(value, name));\n        speak(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__[\"__\"])('Link removed.'), 'assertive');\n      }\n    }, {\n      key: \"render\",\n      value: function render() {\n        var _this$props3 = this.props,\n            isActive = _this$props3.isActive,\n            activeAttributes = _this$props3.activeAttributes,\n            value = _this$props3.value,\n            onChange = _this$props3.onChange;\n        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"Fragment\"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__[\"RichTextShortcut\"], {\n          type: \"access\",\n          character: \"a\",\n          onUse: this.addLink\n        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__[\"RichTextShortcut\"], {\n          type: \"access\",\n          character: \"s\",\n          onUse: this.onRemoveFormat\n        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__[\"RichTextShortcut\"], {\n          type: \"primary\",\n          character: \"k\",\n          onUse: this.addLink\n        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__[\"RichTextShortcut\"], {\n          type: \"primaryShift\",\n          character: \"k\",\n          onUse: this.onRemoveFormat\n        }), isActive && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__[\"RichTextToolbarButton\"], {\n          name: \"link\",\n          icon: \"editor-unlink\",\n          title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__[\"__\"])('Unlink'),\n          onClick: this.onRemoveFormat,\n          isActive: isActive,\n          shortcutType: \"primaryShift\",\n          shortcutCharacter: \"k\"\n        }), !isActive && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_11__[\"RichTextToolbarButton\"], {\n          name: \"link\",\n          icon: \"admin-links\",\n          title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__[\"__\"])('Link'),\n          onClick: this.addLink,\n          isActive: isActive,\n          shortcutType: \"primary\",\n          shortcutCharacter: \"k\"\n        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_inline__WEBPACK_IMPORTED_MODULE_12__[\"default\"], {\n          addingLink: this.state.addingLink,\n          stopAddingLink: this.stopAddingLink,\n          isActive: isActive,\n          activeAttributes: activeAttributes,\n          value: value,\n          onChange: onChange\n        }));\n      }\n    }]);\n\n    return LinkEdit;\n  }(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"Component\"]))\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/link/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/link/inline.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/link/inline.js ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! classnames */ \"./node_modules/classnames/index.js\");\n/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/keycodes */ \"@wordpress/keycodes\");\n/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/url */ \"@wordpress/url\");\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_11__);\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_12__);\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/editor */ \"@wordpress/editor\");\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__);\n/* harmony import */ var _positioned_at_selection__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./positioned-at-selection */ \"./node_modules/@wordpress/format-library/build-module/link/positioned-at-selection.js\");\n/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./utils */ \"./node_modules/@wordpress/format-library/build-module/link/utils.js\");\n\n\n\n\n\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n\n\n\n\n\n\n/**\n * Internal dependencies\n */\n\n\n\n\nvar stopKeyPropagation = function stopKeyPropagation(event) {\n  return event.stopPropagation();\n};\n/**\n * Generates the format object that will be applied to the link text.\n *\n * @param {string}  url              The href of the link.\n * @param {boolean} opensInNewWindow Whether this link will open in a new window.\n * @param {Object}  text             The text that is being hyperlinked.\n *\n * @return {Object} The final format object.\n */\n\n\nfunction createLinkFormat(_ref) {\n  var url = _ref.url,\n      opensInNewWindow = _ref.opensInNewWindow,\n      text = _ref.text;\n  var format = {\n    type: 'core/link',\n    attributes: {\n      url: url\n    }\n  };\n\n  if (opensInNewWindow) {\n    // translators: accessibility label for external links, where the argument is the link text\n    var label = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"sprintf\"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('%s (opens in a new tab)'), text).trim();\n    format.attributes.target = '_blank';\n    format.attributes.rel = 'noreferrer noopener';\n    format.attributes['aria-label'] = label;\n  }\n\n  return format;\n}\n\nfunction isShowingInput(props, state) {\n  return props.addingLink || state.editLink;\n}\n\nvar LinkEditor = function LinkEditor(_ref2) {\n  var value = _ref2.value,\n      onChangeInputValue = _ref2.onChangeInputValue,\n      onKeyDown = _ref2.onKeyDown,\n      submitLink = _ref2.submitLink,\n      autocompleteRef = _ref2.autocompleteRef;\n  return (// Disable reason: KeyPress must be suppressed so the block doesn't hide the toolbar\n\n    /* eslint-disable jsx-a11y/no-noninteractive-element-interactions */\n    Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(\"form\", {\n      className: \"editor-format-toolbar__link-container-content\",\n      onKeyPress: stopKeyPropagation,\n      onKeyDown: onKeyDown,\n      onSubmit: submitLink\n    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__[\"URLInput\"], {\n      value: value,\n      onChange: onChangeInputValue,\n      autocompleteRef: autocompleteRef\n    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__[\"IconButton\"], {\n      icon: \"editor-break\",\n      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Apply'),\n      type: \"submit\"\n    }))\n    /* eslint-enable jsx-a11y/no-noninteractive-element-interactions */\n\n  );\n};\n\nvar LinkViewerUrl = function LinkViewerUrl(_ref3) {\n  var url = _ref3.url;\n  var prependedURL = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_11__[\"prependHTTP\"])(url);\n  var linkClassName = classnames__WEBPACK_IMPORTED_MODULE_7___default()('editor-format-toolbar__link-container-value', {\n    'has-invalid-link': !Object(_utils__WEBPACK_IMPORTED_MODULE_15__[\"isValidHref\"])(prependedURL)\n  });\n\n  if (!url) {\n    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(\"span\", {\n      className: linkClassName\n    });\n  }\n\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__[\"ExternalLink\"], {\n    className: linkClassName,\n    href: url\n  }, Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_11__[\"filterURLForDisplay\"])(Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_11__[\"safeDecodeURI\"])(url)));\n};\n\nvar LinkViewer = function LinkViewer(_ref4) {\n  var url = _ref4.url,\n      editLink = _ref4.editLink;\n  return (// Disable reason: KeyPress must be suppressed so the block doesn't hide the toolbar\n\n    /* eslint-disable jsx-a11y/no-static-element-interactions */\n    Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(\"div\", {\n      className: \"editor-format-toolbar__link-container-content\",\n      onKeyPress: stopKeyPropagation\n    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(LinkViewerUrl, {\n      url: url\n    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__[\"IconButton\"], {\n      icon: \"edit\",\n      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Edit'),\n      onClick: editLink\n    }))\n    /* eslint-enable jsx-a11y/no-static-element-interactions */\n\n  );\n};\n\nvar InlineLinkUI =\n/*#__PURE__*/\nfunction (_Component) {\n  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(InlineLinkUI, _Component);\n\n  function InlineLinkUI() {\n    var _this;\n\n    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, InlineLinkUI);\n\n    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(InlineLinkUI).apply(this, arguments));\n    _this.editLink = _this.editLink.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    _this.submitLink = _this.submitLink.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    _this.onKeyDown = _this.onKeyDown.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    _this.onChangeInputValue = _this.onChangeInputValue.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    _this.setLinkTarget = _this.setLinkTarget.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    _this.onClickOutside = _this.onClickOutside.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    _this.resetState = _this.resetState.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    _this.autocompleteRef = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createRef\"])();\n    _this.state = {\n      opensInNewWindow: false,\n      inputValue: ''\n    };\n    return _this;\n  }\n\n  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(InlineLinkUI, [{\n    key: \"onKeyDown\",\n    value: function onKeyDown(event) {\n      if ([_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__[\"LEFT\"], _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__[\"DOWN\"], _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__[\"RIGHT\"], _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__[\"UP\"], _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__[\"BACKSPACE\"], _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_10__[\"ENTER\"]].indexOf(event.keyCode) > -1) {\n        // Stop the key event from propagating up to ObserveTyping.startTypingInTextField.\n        event.stopPropagation();\n      }\n    }\n  }, {\n    key: \"onChangeInputValue\",\n    value: function onChangeInputValue(inputValue) {\n      this.setState({\n        inputValue: inputValue\n      });\n    }\n  }, {\n    key: \"setLinkTarget\",\n    value: function setLinkTarget(opensInNewWindow) {\n      var _this$props = this.props,\n          _this$props$activeAtt = _this$props.activeAttributes.url,\n          url = _this$props$activeAtt === void 0 ? '' : _this$props$activeAtt,\n          value = _this$props.value,\n          onChange = _this$props.onChange;\n      this.setState({\n        opensInNewWindow: opensInNewWindow\n      }); // Apply now if URL is not being edited.\n\n      if (!isShowingInput(this.props, this.state)) {\n        onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_12__[\"applyFormat\"])(value, createLinkFormat({\n          url: url,\n          opensInNewWindow: opensInNewWindow,\n          text: value.text\n        })));\n      }\n    }\n  }, {\n    key: \"editLink\",\n    value: function editLink(event) {\n      this.setState({\n        editLink: true\n      });\n      event.preventDefault();\n    }\n  }, {\n    key: \"submitLink\",\n    value: function submitLink(event) {\n      var _this$props2 = this.props,\n          isActive = _this$props2.isActive,\n          value = _this$props2.value,\n          onChange = _this$props2.onChange,\n          speak = _this$props2.speak;\n      var _this$state = this.state,\n          inputValue = _this$state.inputValue,\n          opensInNewWindow = _this$state.opensInNewWindow;\n      var url = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_11__[\"prependHTTP\"])(inputValue);\n      var format = createLinkFormat({\n        url: url,\n        opensInNewWindow: opensInNewWindow,\n        text: value.text\n      });\n      event.preventDefault();\n\n      if (Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_12__[\"isCollapsed\"])(value) && !isActive) {\n        var toInsert = Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_12__[\"applyFormat\"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_12__[\"create\"])({\n          text: url\n        }), format, 0, url.length);\n        onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_12__[\"insert\"])(value, toInsert));\n      } else {\n        onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_12__[\"applyFormat\"])(value, format));\n      }\n\n      this.resetState();\n\n      if (!Object(_utils__WEBPACK_IMPORTED_MODULE_15__[\"isValidHref\"])(url)) {\n        speak(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Warning: the link has been inserted but may have errors. Please test it.'), 'assertive');\n      } else if (isActive) {\n        speak(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Link edited.'), 'assertive');\n      } else {\n        speak(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Link inserted'), 'assertive');\n      }\n    }\n  }, {\n    key: \"onClickOutside\",\n    value: function onClickOutside(event) {\n      // The autocomplete suggestions list renders in a separate popover (in a portal),\n      // so onClickOutside fails to detect that a click on a suggestion occured in the\n      // LinkContainer. Detect clicks on autocomplete suggestions using a ref here, and\n      // return to avoid the popover being closed.\n      var autocompleteElement = this.autocompleteRef.current;\n\n      if (autocompleteElement && autocompleteElement.contains(event.target)) {\n        return;\n      }\n\n      this.resetState();\n    }\n  }, {\n    key: \"resetState\",\n    value: function resetState() {\n      this.props.stopAddingLink();\n      this.setState({\n        editLink: false\n      });\n    }\n  }, {\n    key: \"render\",\n    value: function render() {\n      var _this2 = this;\n\n      var _this$props3 = this.props,\n          isActive = _this$props3.isActive,\n          url = _this$props3.activeAttributes.url,\n          addingLink = _this$props3.addingLink,\n          value = _this$props3.value;\n\n      if (!isActive && !addingLink) {\n        return null;\n      }\n\n      var _this$state2 = this.state,\n          inputValue = _this$state2.inputValue,\n          opensInNewWindow = _this$state2.opensInNewWindow;\n      var showInput = isShowingInput(this.props, this.state);\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_positioned_at_selection__WEBPACK_IMPORTED_MODULE_14__[\"default\"], {\n        key: \"\".concat(value.start).concat(value.end)\n        /* Used to force rerender on selection change */\n\n      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_13__[\"URLPopover\"], {\n        onClickOutside: this.onClickOutside,\n        onClose: this.resetState,\n        focusOnMount: showInput ? 'firstElement' : false,\n        renderSettings: function renderSettings() {\n          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__[\"ToggleControl\"], {\n            label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Open in New Tab'),\n            checked: opensInNewWindow,\n            onChange: _this2.setLinkTarget\n          });\n        }\n      }, showInput ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(LinkEditor, {\n        value: inputValue,\n        onChangeInputValue: this.onChangeInputValue,\n        onKeyDown: this.onKeyDown,\n        submitLink: this.submitLink,\n        autocompleteRef: this.autocompleteRef\n      }) : Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(LinkViewer, {\n        url: url,\n        editLink: this.editLink\n      })));\n    }\n  }], [{\n    key: \"getDerivedStateFromProps\",\n    value: function getDerivedStateFromProps(props, state) {\n      var _props$activeAttribut = props.activeAttributes,\n          url = _props$activeAttribut.url,\n          target = _props$activeAttribut.target;\n      var opensInNewWindow = target === '_blank';\n\n      if (!isShowingInput(props, state)) {\n        if (url !== state.inputValue) {\n          return {\n            inputValue: url\n          };\n        }\n\n        if (opensInNewWindow !== state.opensInNewWindow) {\n          return {\n            opensInNewWindow: opensInNewWindow\n          };\n        }\n      }\n\n      return null;\n    }\n  }]);\n\n  return InlineLinkUI;\n}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"Component\"]);\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__[\"withSpokenMessages\"])(InlineLinkUI));\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/link/inline.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/link/positioned-at-selection.js":
/*!*********************************************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/link/positioned-at-selection.js ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var _wordpress_dom__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/dom */ \"@wordpress/dom\");\n/* harmony import */ var _wordpress_dom__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_dom__WEBPACK_IMPORTED_MODULE_6__);\n\n\n\n\n\n\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Returns a style object for applying as `position: absolute` for an element\n * relative to the bottom-center of the current selection. Includes `top` and\n * `left` style properties.\n *\n * @return {Object} Style object.\n */\n\nfunction getCurrentCaretPositionStyle() {\n  var selection = window.getSelection(); // Unlikely, but in the case there is no selection, return empty styles so\n  // as to avoid a thrown error by `Selection#getRangeAt` on invalid index.\n\n  if (selection.rangeCount === 0) {\n    return {};\n  } // Get position relative viewport.\n\n\n  var rect = Object(_wordpress_dom__WEBPACK_IMPORTED_MODULE_6__[\"getRectangleFromRange\"])(selection.getRangeAt(0));\n  var top = rect.top + rect.height;\n  var left = rect.left + rect.width / 2; // Offset by positioned parent, if one exists.\n\n  var offsetParent = Object(_wordpress_dom__WEBPACK_IMPORTED_MODULE_6__[\"getOffsetParent\"])(selection.anchorNode);\n\n  if (offsetParent) {\n    var parentRect = offsetParent.getBoundingClientRect();\n    top -= parentRect.top;\n    left -= parentRect.left;\n  }\n\n  return {\n    top: top,\n    left: left\n  };\n}\n/**\n * Component which renders itself positioned under the current caret selection.\n * The position is calculated at the time of the component being mounted, so it\n * should only be mounted after the desired selection has been made.\n *\n * @type {WPComponent}\n */\n\n\nvar PositionedAtSelection =\n/*#__PURE__*/\nfunction (_Component) {\n  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(PositionedAtSelection, _Component);\n\n  function PositionedAtSelection() {\n    var _this;\n\n    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, PositionedAtSelection);\n\n    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(PositionedAtSelection).apply(this, arguments));\n    _this.state = {\n      style: getCurrentCaretPositionStyle()\n    };\n    return _this;\n  }\n\n  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(PositionedAtSelection, [{\n    key: \"render\",\n    value: function render() {\n      var children = this.props.children;\n      var style = this.state.style;\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__[\"createElement\"])(\"div\", {\n        className: \"editor-format-toolbar__selection-position\",\n        style: style\n      }, children);\n    }\n  }]);\n\n  return PositionedAtSelection;\n}(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__[\"Component\"]);\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (PositionedAtSelection);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/link/positioned-at-selection.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/link/utils.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/link/utils.js ***!
  \***************************************************************************/
/*! exports provided: isValidHref */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isValidHref\", function() { return isValidHref; });\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/url */ \"@wordpress/url\");\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__);\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Check for issues with the provided href.\n *\n * @param {string} href The href.\n *\n * @return {boolean} Is the href invalid?\n */\n\nfunction isValidHref(href) {\n  if (!href) {\n    return false;\n  }\n\n  var trimmedHref = href.trim();\n\n  if (!trimmedHref) {\n    return false;\n  } // Does the href start with something that looks like a URL protocol?\n\n\n  if (/^\\S+:/.test(trimmedHref)) {\n    var protocol = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"getProtocol\"])(trimmedHref);\n\n    if (!Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"isValidProtocol\"])(protocol)) {\n      return false;\n    } // Add some extra checks for http(s) URIs, since these are the most common use-case.\n    // This ensures URIs with an http protocol have exactly two forward slashes following the protocol.\n\n\n    if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"startsWith\"])(protocol, 'http') && !/^https?:\\/\\/[^\\/\\s]/i.test(trimmedHref)) {\n      return false;\n    }\n\n    var authority = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"getAuthority\"])(trimmedHref);\n\n    if (!Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"isValidAuthority\"])(authority)) {\n      return false;\n    }\n\n    var path = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"getPath\"])(trimmedHref);\n\n    if (path && !Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"isValidPath\"])(path)) {\n      return false;\n    }\n\n    var queryString = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"getQueryString\"])(trimmedHref);\n\n    if (queryString && !Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"isValidQueryString\"])(queryString)) {\n      return false;\n    }\n\n    var fragment = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"getFragment\"])(trimmedHref);\n\n    if (fragment && !Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"isValidFragment\"])(fragment)) {\n      return false;\n    }\n  } // Validate anchor links.\n\n\n  if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"startsWith\"])(trimmedHref, '#') && !Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__[\"isValidFragment\"])(trimmedHref)) {\n    return false;\n  }\n\n  return true;\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/link/utils.js?");

/***/ }),

/***/ "./node_modules/@wordpress/format-library/build-module/strikethrough/index.js":
/*!************************************************************************************!*\
  !*** ./node_modules/@wordpress/format-library/build-module/strikethrough/index.js ***!
  \************************************************************************************/
/*! exports provided: strikethrough */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"strikethrough\", function() { return strikethrough; });\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ \"@wordpress/editor\");\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);\n\n\n/**\n * WordPress dependencies\n */\n\n\n\n\nvar name = 'core/strikethrough';\nvar strikethrough = {\n  name: name,\n  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Strikethrough'),\n  tagName: 'del',\n  className: null,\n  edit: function edit(_ref) {\n    var isActive = _ref.isActive,\n        value = _ref.value,\n        onChange = _ref.onChange;\n\n    var onToggle = function onToggle() {\n      return onChange(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__[\"toggleFormat\"])(value, {\n        type: name\n      }));\n    };\n\n    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"Fragment\"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__[\"RichTextShortcut\"], {\n      type: \"access\",\n      character: \"d\",\n      onUse: onToggle\n    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__[\"RichTextToolbarButton\"], {\n      name: \"strikethrough\",\n      icon: \"editor-strikethrough\",\n      title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Strikethrough'),\n      onClick: onToggle,\n      isActive: isActive,\n      shortcutType: \"access\",\n      shortcutCharacter: \"d\"\n    }));\n  }\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/format-library/build-module/strikethrough/index.js?");

/***/ }),

/***/ "./node_modules/classnames/index.js":
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!\n  Copyright (c) 2017 Jed Watson.\n  Licensed under the MIT License (MIT), see\n  http://jedwatson.github.io/classnames\n*/\n/* global define */\n\n(function () {\n\t'use strict';\n\n\tvar hasOwn = {}.hasOwnProperty;\n\n\tfunction classNames () {\n\t\tvar classes = [];\n\n\t\tfor (var i = 0; i < arguments.length; i++) {\n\t\t\tvar arg = arguments[i];\n\t\t\tif (!arg) continue;\n\n\t\t\tvar argType = typeof arg;\n\n\t\t\tif (argType === 'string' || argType === 'number') {\n\t\t\t\tclasses.push(arg);\n\t\t\t} else if (Array.isArray(arg) && arg.length) {\n\t\t\t\tvar inner = classNames.apply(null, arg);\n\t\t\t\tif (inner) {\n\t\t\t\t\tclasses.push(inner);\n\t\t\t\t}\n\t\t\t} else if (argType === 'object') {\n\t\t\t\tfor (var key in arg) {\n\t\t\t\t\tif (hasOwn.call(arg, key) && arg[key]) {\n\t\t\t\t\t\tclasses.push(key);\n\t\t\t\t\t}\n\t\t\t\t}\n\t\t\t}\n\t\t}\n\n\t\treturn classes.join(' ');\n\t}\n\n\tif ( true && module.exports) {\n\t\tclassNames.default = classNames;\n\t\tmodule.exports = classNames;\n\t} else if (true) {\n\t\t// register as 'classnames', consistent with npm package name\n\t\t!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {\n\t\t\treturn classNames;\n\t\t}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),\n\t\t\t\t__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));\n\t} else {}\n}());\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/classnames/index.js?");

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"components\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22components%22%5D%7D?");

/***/ }),

/***/ "@wordpress/dom":
/*!**************************************!*\
  !*** external {"this":["wp","dom"]} ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"dom\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22dom%22%5D%7D?");

/***/ }),

/***/ "@wordpress/editor":
/*!*****************************************!*\
  !*** external {"this":["wp","editor"]} ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"editor\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22editor%22%5D%7D?");

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"element\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22element%22%5D%7D?");

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"i18n\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22i18n%22%5D%7D?");

/***/ }),

/***/ "@wordpress/keycodes":
/*!*******************************************!*\
  !*** external {"this":["wp","keycodes"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"keycodes\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22keycodes%22%5D%7D?");

/***/ }),

/***/ "@wordpress/rich-text":
/*!*******************************************!*\
  !*** external {"this":["wp","richText"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"richText\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22richText%22%5D%7D?");

/***/ }),

/***/ "@wordpress/url":
/*!**************************************!*\
  !*** external {"this":["wp","url"]} ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"url\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22url%22%5D%7D?");

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22lodash%22?");

/***/ })

/******/ });