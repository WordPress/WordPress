this["wp"] = this["wp"] || {}; this["wp"]["listReusableBlocks"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/list-reusable-blocks/build-module/index.js");
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

/***/ "./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _asyncToGenerator; });\nfunction asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {\n  try {\n    var info = gen[key](arg);\n    var value = info.value;\n  } catch (error) {\n    reject(error);\n    return;\n  }\n\n  if (info.done) {\n    resolve(value);\n  } else {\n    Promise.resolve(value).then(_next, _throw);\n  }\n}\n\nfunction _asyncToGenerator(fn) {\n  return function () {\n    var self = this,\n        args = arguments;\n    return new Promise(function (resolve, reject) {\n      var gen = fn.apply(self, args);\n\n      function _next(value) {\n        asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"next\", value);\n      }\n\n      function _throw(err) {\n        asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"throw\", err);\n      }\n\n      _next(undefined);\n    });\n  };\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js?");

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

/***/ "./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-dropdown/index.js":
/*!*******************************************************************************************************!*\
  !*** ./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-dropdown/index.js ***!
  \*******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _import_form__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../import-form */ \"./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-form/index.js\");\n\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n\n/**\n * Internal dependencies\n */\n\n\n\nfunction ImportDropdown(_ref) {\n  var onUpload = _ref.onUpload;\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__[\"Dropdown\"], {\n    position: \"bottom right\",\n    contentClassName: \"list-reusable-blocks-import-dropdown__content\",\n    renderToggle: function renderToggle(_ref2) {\n      var isOpen = _ref2.isOpen,\n          onToggle = _ref2.onToggle;\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__[\"Button\"], {\n        type: \"button\",\n        \"aria-expanded\": isOpen,\n        onClick: onToggle,\n        isPrimary: true\n      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__[\"__\"])('Import from JSON'));\n    },\n    renderContent: function renderContent(_ref3) {\n      var onClose = _ref3.onClose;\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_import_form__WEBPACK_IMPORTED_MODULE_4__[\"default\"], {\n        onUpload: Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"flow\"])(onClose, onUpload)\n      });\n    }\n  });\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (ImportDropdown);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-dropdown/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-form/index.js":
/*!***************************************************************************************************!*\
  !*** ./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-form/index.js ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/compose */ \"@wordpress/compose\");\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var _utils_import__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../utils/import */ \"./node_modules/@wordpress/list-reusable-blocks/build-module/utils/import.js\");\n\n\n\n\n\n\n\n\n/**\n * WordPress dependencies\n */\n\n\n\n\n/**\n * Internal dependencies\n */\n\n\n\nvar ImportForm =\n/*#__PURE__*/\nfunction (_Component) {\n  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(ImportForm, _Component);\n\n  function ImportForm() {\n    var _this;\n\n    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, ImportForm);\n\n    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(ImportForm).apply(this, arguments));\n    _this.state = {\n      isLoading: false,\n      error: null,\n      file: null\n    };\n    _this.isStillMounted = true;\n    _this.onChangeFile = _this.onChangeFile.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    _this.onSubmit = _this.onSubmit.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    return _this;\n  }\n\n  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(ImportForm, [{\n    key: \"componentWillUnmount\",\n    value: function componentWillUnmount() {\n      this.isStillMounted = false;\n    }\n  }, {\n    key: \"onChangeFile\",\n    value: function onChangeFile(event) {\n      this.setState({\n        file: event.target.files[0]\n      });\n    }\n  }, {\n    key: \"onSubmit\",\n    value: function onSubmit(event) {\n      var _this2 = this;\n\n      event.preventDefault();\n      var file = this.state.file;\n      var onUpload = this.props.onUpload;\n\n      if (!file) {\n        return;\n      }\n\n      this.setState({\n        isLoading: true\n      });\n      Object(_utils_import__WEBPACK_IMPORTED_MODULE_10__[\"default\"])(file).then(function (reusableBlock) {\n        if (!_this2.isStillMounted) {\n          return;\n        }\n\n        _this2.setState({\n          isLoading: false\n        });\n\n        onUpload(reusableBlock);\n      }).catch(function (error) {\n        if (!_this2.isStillMounted) {\n          return;\n        }\n\n        var uiMessage;\n\n        switch (error.message) {\n          case 'Invalid JSON file':\n            uiMessage = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Invalid JSON file');\n            break;\n\n          case 'Invalid Reusable Block JSON file':\n            uiMessage = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Invalid Reusable Block JSON file');\n            break;\n\n          default:\n            uiMessage = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('Unknown error');\n        }\n\n        _this2.setState({\n          isLoading: false,\n          error: uiMessage\n        });\n      });\n    }\n  }, {\n    key: \"render\",\n    value: function render() {\n      var instanceId = this.props.instanceId;\n      var _this$state = this.state,\n          file = _this$state.file,\n          isLoading = _this$state.isLoading,\n          error = _this$state.error;\n      var inputId = 'list-reusable-blocks-import-form-' + instanceId;\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(\"form\", {\n        className: \"list-reusable-blocks-import-form\",\n        onSubmit: this.onSubmit\n      }, error && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__[\"Notice\"], {\n        status: \"error\"\n      }, error), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(\"label\", {\n        htmlFor: inputId,\n        className: \"list-reusable-blocks-import-form__label\"\n      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"__\"])('File')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(\"input\", {\n        id: inputId,\n        type: \"file\",\n        onChange: this.onChangeFile\n      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__[\"Button\"], {\n        type: \"submit\",\n        isBusy: isLoading,\n        disabled: !file || isLoading,\n        isDefault: true,\n        className: \"list-reusable-blocks-import-form__button\"\n      }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__[\"_x\"])('Import', 'button label')));\n    }\n  }]);\n\n  return ImportForm;\n}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"Component\"]);\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_7__[\"withInstanceId\"])(ImportForm));\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-form/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/list-reusable-blocks/build-module/index.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/list-reusable-blocks/build-module/index.js ***!
  \****************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _utils_export__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/export */ \"./node_modules/@wordpress/list-reusable-blocks/build-module/utils/export.js\");\n/* harmony import */ var _components_import_dropdown__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/import-dropdown */ \"./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-dropdown/index.js\");\n\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n // Setup Export Links\n\ndocument.body.addEventListener('click', function (event) {\n  if (!event.target.classList.contains('wp-list-reusable-blocks__export')) {\n    return;\n  }\n\n  event.preventDefault();\n  Object(_utils_export__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(event.target.dataset.id);\n}); // Setup Import Form\n\ndocument.addEventListener('DOMContentLoaded', function () {\n  var button = document.querySelector('.page-title-action');\n\n  if (!button) {\n    return;\n  }\n\n  var showNotice = function showNotice() {\n    var notice = document.createElement('div');\n    notice.className = 'notice notice-success is-dismissible';\n    notice.innerHTML = \"<p>\".concat(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Reusable block imported successfully!'), \"</p>\");\n    var headerEnd = document.querySelector('.wp-header-end');\n\n    if (!headerEnd) {\n      return;\n    }\n\n    headerEnd.parentNode.insertBefore(notice, headerEnd);\n  };\n\n  var container = document.createElement('div');\n  container.className = 'list-reusable-blocks__container';\n  button.parentNode.insertBefore(container, button);\n  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"render\"])(Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_components_import_dropdown__WEBPACK_IMPORTED_MODULE_3__[\"default\"], {\n    onUpload: showNotice\n  }), container);\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/list-reusable-blocks/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/list-reusable-blocks/build-module/utils/export.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/@wordpress/list-reusable-blocks/build-module/utils/export.js ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/asyncToGenerator */ \"./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/api-fetch */ \"@wordpress/api-fetch\");\n/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _file__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./file */ \"./node_modules/@wordpress/list-reusable-blocks/build-module/utils/file.js\");\n\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n/**\n * Export a reusable block as a JSON file.\n *\n * @param {number} id\n */\n\nfunction exportReusableBlock(_x) {\n  return _exportReusableBlock.apply(this, arguments);\n}\n\nfunction _exportReusableBlock() {\n  _exportReusableBlock = Object(_babel_runtime_helpers_esm_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(\n  /*#__PURE__*/\n  regeneratorRuntime.mark(function _callee(id) {\n    var postType, post, title, content, fileContent, fileName;\n    return regeneratorRuntime.wrap(function _callee$(_context) {\n      while (1) {\n        switch (_context.prev = _context.next) {\n          case 0:\n            _context.next = 2;\n            return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({\n              path: \"/wp/v2/types/wp_block\"\n            });\n\n          case 2:\n            postType = _context.sent;\n            _context.next = 5;\n            return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({\n              path: \"/wp/v2/\".concat(postType.rest_base, \"/\").concat(id, \"?context=edit\")\n            });\n\n          case 5:\n            post = _context.sent;\n            title = post.title.raw;\n            content = post.content.raw;\n            fileContent = JSON.stringify({\n              __file: 'wp_block',\n              title: title,\n              content: content\n            }, null, 2);\n            fileName = Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"kebabCase\"])(title) + '.json';\n            Object(_file__WEBPACK_IMPORTED_MODULE_3__[\"download\"])(fileName, fileContent, 'application/json');\n\n          case 11:\n          case \"end\":\n            return _context.stop();\n        }\n      }\n    }, _callee, this);\n  }));\n  return _exportReusableBlock.apply(this, arguments);\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (exportReusableBlock);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/list-reusable-blocks/build-module/utils/export.js?");

/***/ }),

/***/ "./node_modules/@wordpress/list-reusable-blocks/build-module/utils/file.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@wordpress/list-reusable-blocks/build-module/utils/file.js ***!
  \*********************************************************************************/
/*! exports provided: download, readTextFile */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"download\", function() { return download; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"readTextFile\", function() { return readTextFile; });\n/**\n * Downloads a file.\n *\n * @param {string} fileName    File Name.\n * @param {string} content     File Content.\n * @param {string} contentType File mime type.\n */\nfunction download(fileName, content, contentType) {\n  var file = new window.Blob([content], {\n    type: contentType\n  }); // IE11 can't use the click to download technique\n  // we use a specific IE11 technique instead.\n\n  if (window.navigator.msSaveOrOpenBlob) {\n    window.navigator.msSaveOrOpenBlob(file, fileName);\n  } else {\n    var a = document.createElement('a');\n    a.href = URL.createObjectURL(file);\n    a.download = fileName;\n    a.style.display = 'none';\n    document.body.appendChild(a);\n    a.click();\n    document.body.removeChild(a);\n  }\n}\n/**\n * Reads the textual content of the given file.\n *\n * @param  {File} file        File.\n * @return {Promise<string>}  Content of the file.\n */\n\nfunction readTextFile(file) {\n  var reader = new window.FileReader();\n  return new Promise(function (resolve) {\n    reader.onload = function () {\n      resolve(reader.result);\n    };\n\n    reader.readAsText(file);\n  });\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/list-reusable-blocks/build-module/utils/file.js?");

/***/ }),

/***/ "./node_modules/@wordpress/list-reusable-blocks/build-module/utils/import.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/@wordpress/list-reusable-blocks/build-module/utils/import.js ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/asyncToGenerator */ \"./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/api-fetch */ \"@wordpress/api-fetch\");\n/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _file__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./file */ \"./node_modules/@wordpress/list-reusable-blocks/build-module/utils/file.js\");\n\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n/**\n * Import a reusable block from a JSON file.\n *\n * @param {File}     file File.\n * @return {Promise} Promise returning the imported reusable block.\n */\n\nfunction importReusableBlock(_x) {\n  return _importReusableBlock.apply(this, arguments);\n}\n\nfunction _importReusableBlock() {\n  _importReusableBlock = Object(_babel_runtime_helpers_esm_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(\n  /*#__PURE__*/\n  regeneratorRuntime.mark(function _callee(file) {\n    var fileContent, parsedContent, postType, reusableBlock;\n    return regeneratorRuntime.wrap(function _callee$(_context) {\n      while (1) {\n        switch (_context.prev = _context.next) {\n          case 0:\n            _context.next = 2;\n            return Object(_file__WEBPACK_IMPORTED_MODULE_3__[\"readTextFile\"])(file);\n\n          case 2:\n            fileContent = _context.sent;\n            _context.prev = 3;\n            parsedContent = JSON.parse(fileContent);\n            _context.next = 10;\n            break;\n\n          case 7:\n            _context.prev = 7;\n            _context.t0 = _context[\"catch\"](3);\n            throw new Error('Invalid JSON file');\n\n          case 10:\n            if (!(parsedContent.__file !== 'wp_block' || !parsedContent.title || !parsedContent.content || !Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"isString\"])(parsedContent.title) || !Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"isString\"])(parsedContent.content))) {\n              _context.next = 12;\n              break;\n            }\n\n            throw new Error('Invalid Reusable Block JSON file');\n\n          case 12:\n            _context.next = 14;\n            return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({\n              path: \"/wp/v2/types/wp_block\"\n            });\n\n          case 14:\n            postType = _context.sent;\n            _context.next = 17;\n            return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({\n              path: \"/wp/v2/\".concat(postType.rest_base),\n              data: {\n                title: parsedContent.title,\n                content: parsedContent.content,\n                status: 'publish'\n              },\n              method: 'POST'\n            });\n\n          case 17:\n            reusableBlock = _context.sent;\n            return _context.abrupt(\"return\", reusableBlock);\n\n          case 19:\n          case \"end\":\n            return _context.stop();\n        }\n      }\n    }, _callee, this, [[3, 7]]);\n  }));\n  return _importReusableBlock.apply(this, arguments);\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (importReusableBlock);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/list-reusable-blocks/build-module/utils/import.js?");

/***/ }),

/***/ "@wordpress/api-fetch":
/*!*******************************************!*\
  !*** external {"this":["wp","apiFetch"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"apiFetch\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22apiFetch%22%5D%7D?");

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"components\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22components%22%5D%7D?");

/***/ }),

/***/ "@wordpress/compose":
/*!******************************************!*\
  !*** external {"this":["wp","compose"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"compose\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22compose%22%5D%7D?");

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

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22lodash%22?");

/***/ })

/******/ });