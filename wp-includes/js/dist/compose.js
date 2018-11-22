this["wp"] = this["wp"] || {}; this["wp"]["compose"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/compose/build-module/index.js");
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

/***/ "./node_modules/@babel/runtime/helpers/esm/extends.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/extends.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _extends; });\nfunction _extends() {\n  _extends = Object.assign || function (target) {\n    for (var i = 1; i < arguments.length; i++) {\n      var source = arguments[i];\n\n      for (var key in source) {\n        if (Object.prototype.hasOwnProperty.call(source, key)) {\n          target[key] = source[key];\n        }\n      }\n    }\n\n    return target;\n  };\n\n  return _extends.apply(this, arguments);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/extends.js?");

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

/***/ "./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js":
/*!*********************************************************************************************!*\
  !*** ./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * External dependencies\n */\n\n/**\n * Given a function mapping a component to an enhanced component and modifier\n * name, returns the enhanced component augmented with a generated displayName.\n *\n * @param {Function} mapComponentToEnhancedComponent Function mapping component\n *                                                   to enhanced component.\n * @param {string}   modifierName                    Seed name from which to\n *                                                   generated display name.\n *\n * @return {WPComponent} Component class with generated display name assigned.\n */\n\nfunction createHigherOrderComponent(mapComponentToEnhancedComponent, modifierName) {\n  return function (OriginalComponent) {\n    var EnhancedComponent = mapComponentToEnhancedComponent(OriginalComponent);\n    var _OriginalComponent$di = OriginalComponent.displayName,\n        displayName = _OriginalComponent$di === void 0 ? OriginalComponent.name || 'Component' : _OriginalComponent$di;\n    EnhancedComponent.displayName = \"\".concat(Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"upperFirst\"])(Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"camelCase\"])(modifierName)), \"(\").concat(displayName, \")\");\n    return EnhancedComponent;\n  };\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (createHigherOrderComponent);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/compose/build-module/if-condition/index.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/compose/build-module/if-condition/index.js ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _create_higher_order_component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../create-higher-order-component */ \"./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js\");\n\n\n/**\n * Internal dependencies\n */\n\n/**\n * Higher-order component creator, creating a new component which renders if\n * the given condition is satisfied or with the given optional prop name.\n *\n * @param {Function} predicate Function to test condition.\n *\n * @return {Function} Higher-order component.\n */\n\nvar ifCondition = function ifCondition(predicate) {\n  return Object(_create_higher_order_component__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(function (WrappedComponent) {\n    return function (props) {\n      if (!predicate(props)) {\n        return null;\n      }\n\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(WrappedComponent, props);\n    };\n  }, 'ifCondition');\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (ifCondition);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/compose/build-module/if-condition/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/compose/build-module/index.js":
/*!***************************************************************!*\
  !*** ./node_modules/@wordpress/compose/build-module/index.js ***!
  \***************************************************************/
/*! exports provided: createHigherOrderComponent, ifCondition, pure, withGlobalEvents, withInstanceId, withSafeTimeout, withState, compose */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"compose\", function() { return lodash__WEBPACK_IMPORTED_MODULE_0__[\"flowRight\"]; });\n\n/* harmony import */ var _create_higher_order_component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./create-higher-order-component */ \"./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"createHigherOrderComponent\", function() { return _create_higher_order_component__WEBPACK_IMPORTED_MODULE_1__[\"default\"]; });\n\n/* harmony import */ var _if_condition__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./if-condition */ \"./node_modules/@wordpress/compose/build-module/if-condition/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"ifCondition\", function() { return _if_condition__WEBPACK_IMPORTED_MODULE_2__[\"default\"]; });\n\n/* harmony import */ var _pure__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./pure */ \"./node_modules/@wordpress/compose/build-module/pure/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"pure\", function() { return _pure__WEBPACK_IMPORTED_MODULE_3__[\"default\"]; });\n\n/* harmony import */ var _with_global_events__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./with-global-events */ \"./node_modules/@wordpress/compose/build-module/with-global-events/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"withGlobalEvents\", function() { return _with_global_events__WEBPACK_IMPORTED_MODULE_4__[\"default\"]; });\n\n/* harmony import */ var _with_instance_id__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./with-instance-id */ \"./node_modules/@wordpress/compose/build-module/with-instance-id/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"withInstanceId\", function() { return _with_instance_id__WEBPACK_IMPORTED_MODULE_5__[\"default\"]; });\n\n/* harmony import */ var _with_safe_timeout__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./with-safe-timeout */ \"./node_modules/@wordpress/compose/build-module/with-safe-timeout/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"withSafeTimeout\", function() { return _with_safe_timeout__WEBPACK_IMPORTED_MODULE_6__[\"default\"]; });\n\n/* harmony import */ var _with_state__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./with-state */ \"./node_modules/@wordpress/compose/build-module/with-state/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"withState\", function() { return _with_state__WEBPACK_IMPORTED_MODULE_7__[\"default\"]; });\n\n/**\n * External dependencies\n */\n\n\n\n\n\n\n\n\n/**\n * Composes multiple higher-order components into a single higher-order component. Performs right-to-left function\n * composition, where each successive invocation is supplied the return value of the previous.\n *\n * @param {...Function} hocs The HOC functions to invoke.\n *\n * @return {Function} Returns the new composite function.\n */\n\n\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/compose/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/compose/build-module/pure/index.js":
/*!********************************************************************!*\
  !*** ./node_modules/@wordpress/compose/build-module/pure/index.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var _wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/is-shallow-equal */ \"@wordpress/is-shallow-equal\");\n/* harmony import */ var _wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var _create_higher_order_component__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../create-higher-order-component */ \"./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js\");\n\n\n\n\n\n\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n/**\n * Given a component returns the enhanced component augmented with a component\n * only rerendering when its props/state change\n *\n * @param {Function} mapComponentToEnhancedComponent Function mapping component\n *                                                   to enhanced component.\n * @param {string}   modifierName                    Seed name from which to\n *                                                   generated display name.\n *\n * @return {WPComponent} Component class with generated display name assigned.\n */\n\nvar pure = Object(_create_higher_order_component__WEBPACK_IMPORTED_MODULE_7__[\"default\"])(function (Wrapped) {\n  if (Wrapped.prototype instanceof _wordpress_element__WEBPACK_IMPORTED_MODULE_5__[\"Component\"]) {\n    return (\n      /*#__PURE__*/\n      function (_Wrapped) {\n        Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(_class, _Wrapped);\n\n        function _class() {\n          Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, _class);\n\n          return Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(_class).apply(this, arguments));\n        }\n\n        Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(_class, [{\n          key: \"shouldComponentUpdate\",\n          value: function shouldComponentUpdate(nextProps, nextState) {\n            return !_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_6___default()(nextProps, this.props) || !_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_6___default()(nextState, this.state);\n          }\n        }]);\n\n        return _class;\n      }(Wrapped)\n    );\n  }\n\n  return (\n    /*#__PURE__*/\n    function (_Component) {\n      Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(_class2, _Component);\n\n      function _class2() {\n        Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, _class2);\n\n        return Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(_class2).apply(this, arguments));\n      }\n\n      Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(_class2, [{\n        key: \"shouldComponentUpdate\",\n        value: function shouldComponentUpdate(nextProps) {\n          return !_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_6___default()(nextProps, this.props);\n        }\n      }, {\n        key: \"render\",\n        value: function render() {\n          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__[\"createElement\"])(Wrapped, this.props);\n        }\n      }]);\n\n      return _class2;\n    }(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__[\"Component\"])\n  );\n}, 'pure');\n/* harmony default export */ __webpack_exports__[\"default\"] = (pure);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/compose/build-module/pure/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/compose/build-module/with-global-events/index.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@wordpress/compose/build-module/with-global-events/index.js ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ \"./node_modules/@babel/runtime/helpers/esm/extends.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var _create_higher_order_component__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../create-higher-order-component */ \"./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js\");\n/* harmony import */ var _listener__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./listener */ \"./node_modules/@wordpress/compose/build-module/with-global-events/listener.js\");\n\n\n\n\n\n\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n\n/**\n * Listener instance responsible for managing document event handling.\n *\n * @type {Listener}\n */\n\nvar listener = new _listener__WEBPACK_IMPORTED_MODULE_10__[\"default\"]();\n\nfunction withGlobalEvents(eventTypesToHandlers) {\n  return Object(_create_higher_order_component__WEBPACK_IMPORTED_MODULE_9__[\"default\"])(function (WrappedComponent) {\n    var Wrapper =\n    /*#__PURE__*/\n    function (_Component) {\n      Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Wrapper, _Component);\n\n      function Wrapper() {\n        var _this;\n\n        Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(this, Wrapper);\n\n        _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(Wrapper).apply(this, arguments));\n        _this.handleEvent = _this.handleEvent.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(_this)));\n        _this.handleRef = _this.handleRef.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(_this)));\n        return _this;\n      }\n\n      Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(Wrapper, [{\n        key: \"componentDidMount\",\n        value: function componentDidMount() {\n          var _this2 = this;\n\n          Object(lodash__WEBPACK_IMPORTED_MODULE_8__[\"forEach\"])(eventTypesToHandlers, function (handler, eventType) {\n            listener.add(eventType, _this2);\n          });\n        }\n      }, {\n        key: \"componentWillUnmount\",\n        value: function componentWillUnmount() {\n          var _this3 = this;\n\n          Object(lodash__WEBPACK_IMPORTED_MODULE_8__[\"forEach\"])(eventTypesToHandlers, function (handler, eventType) {\n            listener.remove(eventType, _this3);\n          });\n        }\n      }, {\n        key: \"handleEvent\",\n        value: function handleEvent(event) {\n          var handler = eventTypesToHandlers[event.type];\n\n          if (typeof this.wrappedRef[handler] === 'function') {\n            this.wrappedRef[handler](event);\n          }\n        }\n      }, {\n        key: \"handleRef\",\n        value: function handleRef(el) {\n          this.wrappedRef = el; // Any component using `withGlobalEvents` that is not setting a `ref`\n          // will cause `this.props.forwardedRef` to be `null`, so we need this\n          // check.\n\n          if (this.props.forwardedRef) {\n            this.props.forwardedRef(el);\n          }\n        }\n      }, {\n        key: \"render\",\n        value: function render() {\n          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__[\"createElement\"])(WrappedComponent, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, this.props.ownProps, {\n            ref: this.handleRef\n          }));\n        }\n      }]);\n\n      return Wrapper;\n    }(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__[\"Component\"]);\n\n    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__[\"forwardRef\"])(function (props, ref) {\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__[\"createElement\"])(Wrapper, {\n        ownProps: props,\n        forwardedRef: ref\n      });\n    });\n  }, 'withGlobalEvents');\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (withGlobalEvents);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/compose/build-module/with-global-events/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/compose/build-module/with-global-events/listener.js":
/*!*************************************************************************************!*\
  !*** ./node_modules/@wordpress/compose/build-module/with-global-events/listener.js ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * Class responsible for orchestrating event handling on the global window,\n * binding a single event to be shared across all handling instances, and\n * removing the handler when no instances are listening for the event.\n */\n\nvar Listener =\n/*#__PURE__*/\nfunction () {\n  function Listener() {\n    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, Listener);\n\n    this.listeners = {};\n    this.handleEvent = this.handleEvent.bind(this);\n  }\n\n  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(Listener, [{\n    key: \"add\",\n    value: function add(eventType, instance) {\n      if (!this.listeners[eventType]) {\n        // Adding first listener for this type, so bind event.\n        window.addEventListener(eventType, this.handleEvent);\n        this.listeners[eventType] = [];\n      }\n\n      this.listeners[eventType].push(instance);\n    }\n  }, {\n    key: \"remove\",\n    value: function remove(eventType, instance) {\n      this.listeners[eventType] = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"without\"])(this.listeners[eventType], instance);\n\n      if (!this.listeners[eventType].length) {\n        // Removing last listener for this type, so unbind event.\n        window.removeEventListener(eventType, this.handleEvent);\n        delete this.listeners[eventType];\n      }\n    }\n  }, {\n    key: \"handleEvent\",\n    value: function handleEvent(event) {\n      Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"forEach\"])(this.listeners[event.type], function (instance) {\n        instance.handleEvent(event);\n      });\n    }\n  }]);\n\n  return Listener;\n}();\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (Listener);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/compose/build-module/with-global-events/listener.js?");

/***/ }),

/***/ "./node_modules/@wordpress/compose/build-module/with-instance-id/index.js":
/*!********************************************************************************!*\
  !*** ./node_modules/@wordpress/compose/build-module/with-instance-id/index.js ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ \"./node_modules/@babel/runtime/helpers/esm/extends.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var _create_higher_order_component__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../create-higher-order-component */ \"./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js\");\n\n\n\n\n\n\n\n\n/**\n * WordPress dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n/**\n * A Higher Order Component used to be provide a unique instance ID by\n * component.\n *\n * @param {WPElement} WrappedComponent The wrapped component.\n *\n * @return {Component} Component with an instanceId prop.\n */\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (Object(_create_higher_order_component__WEBPACK_IMPORTED_MODULE_7__[\"default\"])(function (WrappedComponent) {\n  var instances = 0;\n  return (\n    /*#__PURE__*/\n    function (_Component) {\n      Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_class, _Component);\n\n      function _class() {\n        var _this;\n\n        Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(this, _class);\n\n        _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(_class).apply(this, arguments));\n        _this.instanceId = instances++;\n        return _this;\n      }\n\n      Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(_class, [{\n        key: \"render\",\n        value: function render() {\n          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(WrappedComponent, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, this.props, {\n            instanceId: this.instanceId\n          }));\n        }\n      }]);\n\n      return _class;\n    }(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"Component\"])\n  );\n}, 'withInstanceId'));\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/compose/build-module/with-instance-id/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/compose/build-module/with-safe-timeout/index.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@wordpress/compose/build-module/with-safe-timeout/index.js ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ \"./node_modules/@babel/runtime/helpers/esm/extends.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var _create_higher_order_component__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../create-higher-order-component */ \"./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js\");\n\n\n\n\n\n\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n/**\n * A higher-order component used to provide and manage delayed function calls\n * that ought to be bound to a component's lifecycle.\n *\n * @param {Component} OriginalComponent Component requiring setTimeout\n *\n * @return {Component}                  Wrapped component.\n */\n\nvar withSafeTimeout = Object(_create_higher_order_component__WEBPACK_IMPORTED_MODULE_9__[\"default\"])(function (OriginalComponent) {\n  return (\n    /*#__PURE__*/\n    function (_Component) {\n      Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(WrappedComponent, _Component);\n\n      function WrappedComponent() {\n        var _this;\n\n        Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(this, WrappedComponent);\n\n        _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(WrappedComponent).apply(this, arguments));\n        _this.timeouts = [];\n        _this.setTimeout = _this.setTimeout.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(_this)));\n        _this.clearTimeout = _this.clearTimeout.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(_this)));\n        return _this;\n      }\n\n      Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(WrappedComponent, [{\n        key: \"componentWillUnmount\",\n        value: function componentWillUnmount() {\n          this.timeouts.forEach(clearTimeout);\n        }\n      }, {\n        key: \"setTimeout\",\n        value: function (_setTimeout) {\n          function setTimeout(_x, _x2) {\n            return _setTimeout.apply(this, arguments);\n          }\n\n          setTimeout.toString = function () {\n            return _setTimeout.toString();\n          };\n\n          return setTimeout;\n        }(function (fn, delay) {\n          var _this2 = this;\n\n          var id = setTimeout(function () {\n            fn();\n\n            _this2.clearTimeout(id);\n          }, delay);\n          this.timeouts.push(id);\n          return id;\n        })\n      }, {\n        key: \"clearTimeout\",\n        value: function (_clearTimeout) {\n          function clearTimeout(_x3) {\n            return _clearTimeout.apply(this, arguments);\n          }\n\n          clearTimeout.toString = function () {\n            return _clearTimeout.toString();\n          };\n\n          return clearTimeout;\n        }(function (id) {\n          clearTimeout(id);\n          this.timeouts = Object(lodash__WEBPACK_IMPORTED_MODULE_8__[\"without\"])(this.timeouts, id);\n        })\n      }, {\n        key: \"render\",\n        value: function render() {\n          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__[\"createElement\"])(OriginalComponent, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, this.props, {\n            setTimeout: this.setTimeout,\n            clearTimeout: this.clearTimeout\n          }));\n        }\n      }]);\n\n      return WrappedComponent;\n    }(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__[\"Component\"])\n  );\n}, 'withSafeTimeout');\n/* harmony default export */ __webpack_exports__[\"default\"] = (withSafeTimeout);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/compose/build-module/with-safe-timeout/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/compose/build-module/with-state/index.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/compose/build-module/with-state/index.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return withState; });\n/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ \"./node_modules/@babel/runtime/helpers/esm/extends.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var _create_higher_order_component__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../create-higher-order-component */ \"./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js\");\n\n\n\n\n\n\n\n\n\n/**\n * WordPress dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n/**\n * A Higher Order Component used to provide and manage internal component state\n * via props.\n *\n * @param {?Object} initialState Optional initial state of the component.\n *\n * @return {Component} Wrapped component.\n */\n\nfunction withState() {\n  var initialState = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};\n  return Object(_create_higher_order_component__WEBPACK_IMPORTED_MODULE_8__[\"default\"])(function (OriginalComponent) {\n    return (\n      /*#__PURE__*/\n      function (_Component) {\n        Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(WrappedComponent, _Component);\n\n        function WrappedComponent() {\n          var _this;\n\n          Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(this, WrappedComponent);\n\n          _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(WrappedComponent).apply(this, arguments));\n          _this.setState = _this.setState.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(_this)));\n          _this.state = initialState;\n          return _this;\n        }\n\n        Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(WrappedComponent, [{\n          key: \"render\",\n          value: function render() {\n            return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__[\"createElement\"])(OriginalComponent, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, this.props, this.state, {\n              setState: this.setState\n            }));\n          }\n        }]);\n\n        return WrappedComponent;\n      }(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__[\"Component\"])\n    );\n  }, 'withState');\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/compose/build-module/with-state/index.js?");

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"element\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22element%22%5D%7D?");

/***/ }),

/***/ "@wordpress/is-shallow-equal":
/*!*************************************************!*\
  !*** external {"this":["wp","isShallowEqual"]} ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"isShallowEqual\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22isShallowEqual%22%5D%7D?");

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