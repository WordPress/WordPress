this["wp"] = this["wp"] || {}; this["wp"]["element"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/element/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _defineProperty; });\nfunction _defineProperty(obj, key, value) {\n  if (key in obj) {\n    Object.defineProperty(obj, key, {\n      value: value,\n      enumerable: true,\n      configurable: true,\n      writable: true\n    });\n  } else {\n    obj[key] = value;\n  }\n\n  return obj;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/defineProperty.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectSpread.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _objectSpread; });\n/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./defineProperty */ \"./node_modules/@babel/runtime/helpers/esm/defineProperty.js\");\n\nfunction _objectSpread(target) {\n  for (var i = 1; i < arguments.length; i++) {\n    var source = arguments[i] != null ? arguments[i] : {};\n    var ownKeys = Object.keys(source);\n\n    if (typeof Object.getOwnPropertySymbols === 'function') {\n      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {\n        return Object.getOwnPropertyDescriptor(source, sym).enumerable;\n      }));\n    }\n\n    ownKeys.forEach(function (key) {\n      Object(_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(target, key, source[key]);\n    });\n  }\n\n  return target;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/objectSpread.js?");

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

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _typeof; });\nfunction _typeof2(obj) { if (typeof Symbol === \"function\" && typeof Symbol.iterator === \"symbol\") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === \"function\" && obj.constructor === Symbol && obj !== Symbol.prototype ? \"symbol\" : typeof obj; }; } return _typeof2(obj); }\n\nfunction _typeof(obj) {\n  if (typeof Symbol === \"function\" && _typeof2(Symbol.iterator) === \"symbol\") {\n    _typeof = function _typeof(obj) {\n      return _typeof2(obj);\n    };\n  } else {\n    _typeof = function _typeof(obj) {\n      return obj && typeof Symbol === \"function\" && obj.constructor === Symbol && obj !== Symbol.prototype ? \"symbol\" : _typeof2(obj);\n    };\n  }\n\n  return _typeof(obj);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/typeof.js?");

/***/ }),

/***/ "./node_modules/@wordpress/element/build-module/index.js":
/*!***************************************************************!*\
  !*** ./node_modules/@wordpress/element/build-module/index.js ***!
  \***************************************************************/
/*! exports provided: renderToString, RawHTML, Children, cloneElement, Component, createContext, createElement, createRef, forwardRef, Fragment, isValidElement, StrictMode, concatChildren, switchChildrenNodeName, createPortal, findDOMNode, render, unmountComponentAtNode, isEmptyElement */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./react */ \"./node_modules/@wordpress/element/build-module/react.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"Children\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"Children\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"cloneElement\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"cloneElement\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"Component\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"Component\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"createContext\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"createContext\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"createElement\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"createRef\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"createRef\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"forwardRef\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"forwardRef\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"Fragment\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"Fragment\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"isValidElement\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"isValidElement\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"StrictMode\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"StrictMode\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"concatChildren\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"concatChildren\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"switchChildrenNodeName\", function() { return _react__WEBPACK_IMPORTED_MODULE_0__[\"switchChildrenNodeName\"]; });\n\n/* harmony import */ var _react_platform__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./react-platform */ \"./node_modules/@wordpress/element/build-module/react-platform.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"createPortal\", function() { return _react_platform__WEBPACK_IMPORTED_MODULE_1__[\"createPortal\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"findDOMNode\", function() { return _react_platform__WEBPACK_IMPORTED_MODULE_1__[\"findDOMNode\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _react_platform__WEBPACK_IMPORTED_MODULE_1__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"unmountComponentAtNode\", function() { return _react_platform__WEBPACK_IMPORTED_MODULE_1__[\"unmountComponentAtNode\"]; });\n\n/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ \"./node_modules/@wordpress/element/build-module/utils.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"isEmptyElement\", function() { return _utils__WEBPACK_IMPORTED_MODULE_2__[\"isEmptyElement\"]; });\n\n/* harmony import */ var _serialize__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./serialize */ \"./node_modules/@wordpress/element/build-module/serialize.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"renderToString\", function() { return _serialize__WEBPACK_IMPORTED_MODULE_3__[\"default\"]; });\n\n/* harmony import */ var _raw_html__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./raw-html */ \"./node_modules/@wordpress/element/build-module/raw-html.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"RawHTML\", function() { return _raw_html__WEBPACK_IMPORTED_MODULE_4__[\"default\"]; });\n\n\n\n\n\n\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/element/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/element/build-module/raw-html.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/element/build-module/raw-html.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return RawHTML; });\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js\");\n/* harmony import */ var _react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./react */ \"./node_modules/@wordpress/element/build-module/react.js\");\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * Component used as equivalent of Fragment with unescaped HTML, in cases where\n * it is desirable to render dangerous HTML without needing a wrapper element.\n * To preserve additional props, a `div` wrapper _will_ be created if any props\n * aside from `children` are passed.\n *\n * @param {string} props.children HTML to render.\n *\n * @return {WPElement} Dangerously-rendering element.\n */\n\nfunction RawHTML(_ref) {\n  var children = _ref.children,\n      props = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(_ref, [\"children\"]);\n\n  // The DIV wrapper will be stripped by serializer, unless there are\n  // non-children props present.\n  return Object(_react__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])('div', Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({\n    dangerouslySetInnerHTML: {\n      __html: children\n    }\n  }, props));\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/element/build-module/raw-html.js?");

/***/ }),

/***/ "./node_modules/@wordpress/element/build-module/react-platform.js":
/*!************************************************************************!*\
  !*** ./node_modules/@wordpress/element/build-module/react-platform.js ***!
  \************************************************************************/
/*! exports provided: createPortal, findDOMNode, render, unmountComponentAtNode */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-dom */ \"react-dom\");\n/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"createPortal\", function() { return react_dom__WEBPACK_IMPORTED_MODULE_0__[\"createPortal\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"findDOMNode\", function() { return react_dom__WEBPACK_IMPORTED_MODULE_0__[\"findDOMNode\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return react_dom__WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"unmountComponentAtNode\", function() { return react_dom__WEBPACK_IMPORTED_MODULE_0__[\"unmountComponentAtNode\"]; });\n\n/**\n * External dependencies\n */\n\n/**\n * Creates a portal into which a component can be rendered.\n *\n * @see https://github.com/facebook/react/issues/10309#issuecomment-318433235\n *\n * @param {Component} component Component\n * @param {Element}   target    DOM node into which element should be rendered\n */\n\n\n/**\n * Finds the dom node of a React component\n *\n * @param {Component} component component's instance\n * @param {Element}   target    DOM node into which element should be rendered\n */\n\n\n/**\n * Renders a given element into the target DOM node.\n *\n * @param {WPElement} element Element to render\n * @param {Element}   target  DOM node into which element should be rendered\n */\n\n\n/**\n * Removes any mounted element from the target DOM node.\n *\n * @param {Element} target DOM node in which element is to be removed\n */\n\n\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/element/build-module/react-platform.js?");

/***/ }),

/***/ "./node_modules/@wordpress/element/build-module/react.js":
/*!***************************************************************!*\
  !*** ./node_modules/@wordpress/element/build-module/react.js ***!
  \***************************************************************/
/*! exports provided: Children, cloneElement, Component, createContext, createElement, createRef, forwardRef, Fragment, isValidElement, StrictMode, concatChildren, switchChildrenNodeName */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"concatChildren\", function() { return concatChildren; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"switchChildrenNodeName\", function() { return switchChildrenNodeName; });\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react */ \"react\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"Children\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"Children\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"cloneElement\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"cloneElement\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"Component\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"Component\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"createContext\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"createContext\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"createElement\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"createRef\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"createRef\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"forwardRef\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"forwardRef\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"Fragment\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"Fragment\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"isValidElement\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"isValidElement\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"StrictMode\", function() { return react__WEBPACK_IMPORTED_MODULE_2__[\"StrictMode\"]; });\n\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);\n\n\n\n/**\n * External dependencies\n */\n\n\n\n/**\n * Creates a copy of an element with extended props.\n *\n * @param {WPElement} element Element\n * @param {?Object}   props   Props to apply to cloned element\n *\n * @return {WPElement} Cloned element.\n */\n\n\n/**\n * A base class to create WordPress Components (Refs, state and lifecycle hooks)\n */\n\n\n/**\n * Creates a context object containing two components: a provider and consumer.\n *\n * @param {Object} defaultValue A default data stored in the context.\n *\n * @return {Object} Context object.\n */\n\n\n/**\n * Returns a new element of given type. Type can be either a string tag name or\n * another function which itself returns an element.\n *\n * @param {?(string|Function)} type     Tag name or element creator\n * @param {Object}             props    Element properties, either attribute\n *                                       set to apply to DOM node or values to\n *                                       pass through to element creator\n * @param {...WPElement}       children Descendant elements\n *\n * @return {WPElement} Element.\n */\n\n\n/**\n * Returns an object tracking a reference to a rendered element via its\n * `current` property as either a DOMElement or Element, dependent upon the\n * type of element rendered with the ref attribute.\n *\n * @return {Object} Ref object.\n */\n\n\n/**\n * Component enhancer used to enable passing a ref to its wrapped component.\n * Pass a function argument which receives `props` and `ref` as its arguments,\n * returning an element using the forwarded ref. The return value is a new\n * component which forwards its ref.\n *\n * @param {Function} forwarder Function passed `props` and `ref`, expected to\n *                             return an element.\n *\n * @return {WPComponent} Enhanced component.\n */\n\n\n/**\n * A component which renders its children without any wrapping element.\n */\n\n\n/**\n * Checks if an object is a valid WPElement\n *\n * @param {Object} objectToCheck The object to be checked.\n *\n * @return {boolean} true if objectToTest is a valid WPElement and false otherwise.\n */\n\n\n\n/**\n * Concatenate two or more React children objects.\n *\n * @param {...?Object} childrenArguments Array of children arguments (array of arrays/strings/objects) to concatenate.\n *\n * @return {Array} The concatenated value.\n */\n\nfunction concatChildren() {\n  for (var _len = arguments.length, childrenArguments = new Array(_len), _key = 0; _key < _len; _key++) {\n    childrenArguments[_key] = arguments[_key];\n  }\n\n  return childrenArguments.reduce(function (memo, children, i) {\n    react__WEBPACK_IMPORTED_MODULE_2__[\"Children\"].forEach(children, function (child, j) {\n      if (child && 'string' !== typeof child) {\n        child = Object(react__WEBPACK_IMPORTED_MODULE_2__[\"cloneElement\"])(child, {\n          key: [i, j].join()\n        });\n      }\n\n      memo.push(child);\n    });\n    return memo;\n  }, []);\n}\n/**\n * Switches the nodeName of all the elements in the children object.\n *\n * @param {?Object} children Children object.\n * @param {string}  nodeName Node name.\n *\n * @return {?Object} The updated children object.\n */\n\nfunction switchChildrenNodeName(children, nodeName) {\n  return children && react__WEBPACK_IMPORTED_MODULE_2__[\"Children\"].map(children, function (elt, index) {\n    if (Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"isString\"])(elt)) {\n      return Object(react__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(nodeName, {\n        key: index\n      }, elt);\n    }\n\n    var _elt$props = elt.props,\n        childrenProp = _elt$props.children,\n        props = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(_elt$props, [\"children\"]);\n\n    return Object(react__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(nodeName, Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({\n      key: index\n    }, props), childrenProp);\n  });\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/element/build-module/react.js?");

/***/ }),

/***/ "./node_modules/@wordpress/element/build-module/serialize.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/element/build-module/serialize.js ***!
  \*******************************************************************/
/*! exports provided: hasPrefix, renderElement, renderNativeComponent, renderComponent, renderAttributes, renderStyle, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"hasPrefix\", function() { return hasPrefix; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"renderElement\", function() { return renderElement; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"renderNativeComponent\", function() { return renderNativeComponent; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"renderComponent\", function() { return renderComponent; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"renderAttributes\", function() { return renderAttributes; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"renderStyle\", function() { return renderStyle; });\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/typeof */ \"./node_modules/@babel/runtime/helpers/esm/typeof.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _wordpress_escape_html__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/escape-html */ \"@wordpress/escape-html\");\n/* harmony import */ var _wordpress_escape_html__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_escape_html__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var _react__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./react */ \"./node_modules/@wordpress/element/build-module/react.js\");\n/* harmony import */ var _raw_html__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./raw-html */ \"./node_modules/@wordpress/element/build-module/raw-html.js\");\n\n\n\n\n/**\n * Parts of this source were derived and modified from fast-react-render,\n * released under the MIT license.\n *\n * https://github.com/alt-j/fast-react-render\n *\n * Copyright (c) 2016 Andrey Morozov\n *\n * Permission is hereby granted, free of charge, to any person obtaining a copy\n * of this software and associated documentation files (the \"Software\"), to deal\n * in the Software without restriction, including without limitation the rights\n * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell\n * copies of the Software, and to permit persons to whom the Software is\n * furnished to do so, subject to the following conditions:\n *\n * The above copyright notice and this permission notice shall be included in\n * all copies or substantial portions of the Software.\n *\n * THE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR\n * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,\n * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE\n * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER\n * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,\n * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN\n * THE SOFTWARE.\n */\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n\n\nvar _createContext = Object(_react__WEBPACK_IMPORTED_MODULE_5__[\"createContext\"])(),\n    Provider = _createContext.Provider,\n    Consumer = _createContext.Consumer;\n/**\n * Valid attribute types.\n *\n * @type {Set}\n */\n\n\nvar ATTRIBUTES_TYPES = new Set(['string', 'boolean', 'number']);\n/**\n * Element tags which can be self-closing.\n *\n * @type {Set}\n */\n\nvar SELF_CLOSING_TAGS = new Set(['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr']);\n/**\n * Boolean attributes are attributes whose presence as being assigned is\n * meaningful, even if only empty.\n *\n * See: https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#boolean-attributes\n * Extracted from: https://html.spec.whatwg.org/multipage/indices.html#attributes-3\n *\n * Object.keys( [ ...document.querySelectorAll( '#attributes-1 > tbody > tr' ) ]\n *     .filter( ( tr ) => tr.lastChild.textContent.indexOf( 'Boolean attribute' ) !== -1 )\n *     .reduce( ( result, tr ) => Object.assign( result, {\n *         [ tr.firstChild.textContent.trim() ]: true\n *     } ), {} ) ).sort();\n *\n * @type {Set}\n */\n\nvar BOOLEAN_ATTRIBUTES = new Set(['allowfullscreen', 'allowpaymentrequest', 'allowusermedia', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled', 'download', 'formnovalidate', 'hidden', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected', 'typemustmatch']);\n/**\n * Enumerated attributes are attributes which must be of a specific value form.\n * Like boolean attributes, these are meaningful if specified, even if not of a\n * valid enumerated value.\n *\n * See: https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#enumerated-attribute\n * Extracted from: https://html.spec.whatwg.org/multipage/indices.html#attributes-3\n *\n * Object.keys( [ ...document.querySelectorAll( '#attributes-1 > tbody > tr' ) ]\n *     .filter( ( tr ) => /^(\"(.+?)\";?\\s*)+/.test( tr.lastChild.textContent.trim() ) )\n *     .reduce( ( result, tr ) => Object.assign( result, {\n *         [ tr.firstChild.textContent.trim() ]: true\n *     } ), {} ) ).sort();\n *\n * Some notable omissions:\n *\n *  - `alt`: https://blog.whatwg.org/omit-alt\n *\n * @type {Set}\n */\n\nvar ENUMERATED_ATTRIBUTES = new Set(['autocapitalize', 'autocomplete', 'charset', 'contenteditable', 'crossorigin', 'decoding', 'dir', 'draggable', 'enctype', 'formenctype', 'formmethod', 'http-equiv', 'inputmode', 'kind', 'method', 'preload', 'scope', 'shape', 'spellcheck', 'translate', 'type', 'wrap']);\n/**\n * Set of CSS style properties which support assignment of unitless numbers.\n * Used in rendering of style properties, where `px` unit is assumed unless\n * property is included in this set or value is zero.\n *\n * Generated via:\n *\n * Object.entries( document.createElement( 'div' ).style )\n *     .filter( ( [ key ] ) => (\n *         ! /^(webkit|ms|moz)/.test( key ) &&\n *         ( e.style[ key ] = 10 ) &&\n *         e.style[ key ] === '10'\n *     ) )\n *     .map( ( [ key ] ) => key )\n *     .sort();\n *\n * @type {Set}\n */\n\nvar CSS_PROPERTIES_SUPPORTS_UNITLESS = new Set(['animation', 'animationIterationCount', 'baselineShift', 'borderImageOutset', 'borderImageSlice', 'borderImageWidth', 'columnCount', 'cx', 'cy', 'fillOpacity', 'flexGrow', 'flexShrink', 'floodOpacity', 'fontWeight', 'gridColumnEnd', 'gridColumnStart', 'gridRowEnd', 'gridRowStart', 'lineHeight', 'opacity', 'order', 'orphans', 'r', 'rx', 'ry', 'shapeImageThreshold', 'stopOpacity', 'strokeDasharray', 'strokeDashoffset', 'strokeMiterlimit', 'strokeOpacity', 'strokeWidth', 'tabSize', 'widows', 'x', 'y', 'zIndex', 'zoom']);\n/**\n * Returns true if the specified string is prefixed by one of an array of\n * possible prefixes.\n *\n * @param {string}   string   String to check.\n * @param {string[]} prefixes Possible prefixes.\n *\n * @return {boolean} Whether string has prefix.\n */\n\nfunction hasPrefix(string, prefixes) {\n  return prefixes.some(function (prefix) {\n    return string.indexOf(prefix) === 0;\n  });\n}\n/**\n * Returns true if the given prop name should be ignored in attributes\n * serialization, or false otherwise.\n *\n * @param {string} attribute Attribute to check.\n *\n * @return {boolean} Whether attribute should be ignored.\n */\n\nfunction isInternalAttribute(attribute) {\n  return 'key' === attribute || 'children' === attribute;\n}\n/**\n * Returns the normal form of the element's attribute value for HTML.\n *\n * @param {string} attribute Attribute name.\n * @param {*}      value     Non-normalized attribute value.\n *\n * @return {string} Normalized attribute value.\n */\n\n\nfunction getNormalAttributeValue(attribute, value) {\n  switch (attribute) {\n    case 'style':\n      return renderStyle(value);\n  }\n\n  return value;\n}\n/**\n * Returns the normal form of the element's attribute name for HTML.\n *\n * @param {string} attribute Non-normalized attribute name.\n *\n * @return {string} Normalized attribute name.\n */\n\n\nfunction getNormalAttributeName(attribute) {\n  switch (attribute) {\n    case 'htmlFor':\n      return 'for';\n\n    case 'className':\n      return 'class';\n  }\n\n  return attribute.toLowerCase();\n}\n/**\n * Returns the normal form of the style property name for HTML.\n *\n * - Converts property names to kebab-case, e.g. 'backgroundColor' → 'background-color'\n * - Leaves custom attributes alone, e.g. '--myBackgroundColor' → '--myBackgroundColor'\n * - Converts vendor-prefixed property names to -kebab-case, e.g. 'MozTransform' → '-moz-transform'\n *\n * @param {string} property Property name.\n *\n * @return {string} Normalized property name.\n */\n\n\nfunction getNormalStylePropertyName(property) {\n  if (Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"startsWith\"])(property, '--')) {\n    return property;\n  }\n\n  if (hasPrefix(property, ['ms', 'O', 'Moz', 'Webkit'])) {\n    return '-' + Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"kebabCase\"])(property);\n  }\n\n  return Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"kebabCase\"])(property);\n}\n/**\n * Returns the normal form of the style property value for HTML. Appends a\n * default pixel unit if numeric, not a unitless property, and not zero.\n *\n * @param {string} property Property name.\n * @param {*}      value    Non-normalized property value.\n *\n * @return {*} Normalized property value.\n */\n\n\nfunction getNormalStylePropertyValue(property, value) {\n  if (typeof value === 'number' && 0 !== value && !CSS_PROPERTIES_SUPPORTS_UNITLESS.has(property)) {\n    return value + 'px';\n  }\n\n  return value;\n}\n/**\n * Serializes a React element to string.\n *\n * @param {WPElement} element       Element to serialize.\n * @param {?Object}   context       Context object.\n * @param {?Object}   legacyContext Legacy context object.\n *\n * @return {string} Serialized element.\n */\n\n\nfunction renderElement(element, context) {\n  var legacyContext = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};\n\n  if (null === element || undefined === element || false === element) {\n    return '';\n  }\n\n  if (Array.isArray(element)) {\n    return renderChildren(element, context, legacyContext);\n  }\n\n  switch (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(element)) {\n    case 'string':\n      return Object(_wordpress_escape_html__WEBPACK_IMPORTED_MODULE_4__[\"escapeHTML\"])(element);\n\n    case 'number':\n      return element.toString();\n  }\n\n  var type = element.type,\n      props = element.props;\n\n  switch (type) {\n    case _react__WEBPACK_IMPORTED_MODULE_5__[\"StrictMode\"]:\n    case _react__WEBPACK_IMPORTED_MODULE_5__[\"Fragment\"]:\n      return renderChildren(props.children, context, legacyContext);\n\n    case _raw_html__WEBPACK_IMPORTED_MODULE_6__[\"default\"]:\n      var children = props.children,\n          wrapperProps = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(props, [\"children\"]);\n\n      return renderNativeComponent(Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"isEmpty\"])(wrapperProps) ? null : 'div', Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, wrapperProps, {\n        dangerouslySetInnerHTML: {\n          __html: children\n        }\n      }), context, legacyContext);\n  }\n\n  switch (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(type)) {\n    case 'string':\n      return renderNativeComponent(type, props, context, legacyContext);\n\n    case 'function':\n      if (type.prototype && typeof type.prototype.render === 'function') {\n        return renderComponent(type, props, context, legacyContext);\n      }\n\n      return renderElement(type(props, legacyContext), context, legacyContext);\n  }\n\n  switch (type && type.$$typeof) {\n    case Provider.$$typeof:\n      return renderChildren(props.children, props.value, legacyContext);\n\n    case Consumer.$$typeof:\n      return renderElement(props.children(context || type._currentValue), context, legacyContext);\n  }\n\n  return '';\n}\n/**\n * Serializes a native component type to string.\n *\n * @param {?string} type          Native component type to serialize, or null if\n *                                rendering as fragment of children content.\n * @param {Object}  props         Props object.\n * @param {?Object} context       Context object.\n * @param {?Object} legacyContext Legacy context object.\n *\n * @return {string} Serialized element.\n */\n\nfunction renderNativeComponent(type, props, context) {\n  var legacyContext = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};\n  var content = '';\n\n  if (type === 'textarea' && props.hasOwnProperty('value')) {\n    // Textarea children can be assigned as value prop. If it is, render in\n    // place of children. Ensure to omit so it is not assigned as attribute\n    // as well.\n    content = renderChildren(props.value, context, legacyContext);\n    props = Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"omit\"])(props, 'value');\n  } else if (props.dangerouslySetInnerHTML && typeof props.dangerouslySetInnerHTML.__html === 'string') {\n    // Dangerous content is left unescaped.\n    content = props.dangerouslySetInnerHTML.__html;\n  } else if (typeof props.children !== 'undefined') {\n    content = renderChildren(props.children, context, legacyContext);\n  }\n\n  if (!type) {\n    return content;\n  }\n\n  var attributes = renderAttributes(props);\n\n  if (SELF_CLOSING_TAGS.has(type)) {\n    return '<' + type + attributes + '/>';\n  }\n\n  return '<' + type + attributes + '>' + content + '</' + type + '>';\n}\n/**\n * Serializes a non-native component type to string.\n *\n * @param {Function} Component     Component type to serialize.\n * @param {Object}   props         Props object.\n * @param {?Object}  context       Context object.\n * @param {?Object}  legacyContext Legacy context object.\n *\n * @return {string} Serialized element\n */\n\nfunction renderComponent(Component, props, context) {\n  var legacyContext = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};\n  var instance = new Component(props, legacyContext);\n\n  if (typeof instance.getChildContext === 'function') {\n    Object.assign(legacyContext, instance.getChildContext());\n  }\n\n  var html = renderElement(instance.render(), context, legacyContext);\n  return html;\n}\n/**\n * Serializes an array of children to string.\n *\n * @param {Array}   children      Children to serialize.\n * @param {?Object} context       Context object.\n * @param {?Object} legacyContext Legacy context object.\n *\n * @return {string} Serialized children.\n */\n\nfunction renderChildren(children, context) {\n  var legacyContext = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};\n  var result = '';\n  children = Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"castArray\"])(children);\n\n  for (var i = 0; i < children.length; i++) {\n    var child = children[i];\n    result += renderElement(child, context, legacyContext);\n  }\n\n  return result;\n}\n/**\n * Renders a props object as a string of HTML attributes.\n *\n * @param {Object} props Props object.\n *\n * @return {string} Attributes string.\n */\n\n\nfunction renderAttributes(props) {\n  var result = '';\n\n  for (var key in props) {\n    var attribute = getNormalAttributeName(key);\n\n    if (!Object(_wordpress_escape_html__WEBPACK_IMPORTED_MODULE_4__[\"isValidAttributeName\"])(attribute)) {\n      continue;\n    }\n\n    var value = getNormalAttributeValue(key, props[key]); // If value is not of serializeable type, skip.\n\n    if (!ATTRIBUTES_TYPES.has(Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(value))) {\n      continue;\n    } // Don't render internal attribute names.\n\n\n    if (isInternalAttribute(key)) {\n      continue;\n    }\n\n    var isBooleanAttribute = BOOLEAN_ATTRIBUTES.has(attribute); // Boolean attribute should be omitted outright if its value is false.\n\n    if (isBooleanAttribute && value === false) {\n      continue;\n    }\n\n    var isMeaningfulAttribute = isBooleanAttribute || hasPrefix(key, ['data-', 'aria-']) || ENUMERATED_ATTRIBUTES.has(attribute); // Only write boolean value as attribute if meaningful.\n\n    if (typeof value === 'boolean' && !isMeaningfulAttribute) {\n      continue;\n    }\n\n    result += ' ' + attribute; // Boolean attributes should write attribute name, but without value.\n    // Mere presence of attribute name is effective truthiness.\n\n    if (isBooleanAttribute) {\n      continue;\n    }\n\n    if (typeof value === 'string') {\n      value = Object(_wordpress_escape_html__WEBPACK_IMPORTED_MODULE_4__[\"escapeAttribute\"])(value);\n    }\n\n    result += '=\"' + value + '\"';\n  }\n\n  return result;\n}\n/**\n * Renders a style object as a string attribute value.\n *\n * @param {Object} style Style object.\n *\n * @return {string} Style attribute value.\n */\n\nfunction renderStyle(style) {\n  // Only generate from object, e.g. tolerate string value.\n  if (!Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"isPlainObject\"])(style)) {\n    return style;\n  }\n\n  var result;\n\n  for (var property in style) {\n    var value = style[property];\n\n    if (null === value || undefined === value) {\n      continue;\n    }\n\n    if (result) {\n      result += ';';\n    } else {\n      result = '';\n    }\n\n    var normalName = getNormalStylePropertyName(property);\n    var normalValue = getNormalStylePropertyValue(property, value);\n    result += normalName + ':' + normalValue;\n  }\n\n  return result;\n}\n/* harmony default export */ __webpack_exports__[\"default\"] = (renderElement);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/element/build-module/serialize.js?");

/***/ }),

/***/ "./node_modules/@wordpress/element/build-module/utils.js":
/*!***************************************************************!*\
  !*** ./node_modules/@wordpress/element/build-module/utils.js ***!
  \***************************************************************/
/*! exports provided: isEmptyElement */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isEmptyElement\", function() { return isEmptyElement; });\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * External dependencies\n */\n\n/**\n * Checks if the provided WP element is empty.\n *\n * @param {*} element WP element to check.\n * @return {boolean} True when an element is considered empty.\n */\n\nvar isEmptyElement = function isEmptyElement(element) {\n  if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"isNumber\"])(element)) {\n    return false;\n  }\n\n  if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"isString\"])(element) || Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"isArray\"])(element)) {\n    return !element.length;\n  }\n\n  return !element;\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/element/build-module/utils.js?");

/***/ }),

/***/ "@wordpress/escape-html":
/*!*********************************************!*\
  !*** external {"this":["wp","escapeHtml"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"escapeHtml\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22escapeHtml%22%5D%7D?");

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22lodash%22?");

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"React\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22React%22?");

/***/ }),

/***/ "react-dom":
/*!***************************!*\
  !*** external "ReactDOM" ***!
  \***************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"ReactDOM\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22ReactDOM%22?");

/***/ })

/******/ });