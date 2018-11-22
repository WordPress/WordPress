this["wp"] = this["wp"] || {}; this["wp"]["annotations"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/annotations/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _arrayWithoutHoles; });\nfunction _arrayWithoutHoles(arr) {\n  if (Array.isArray(arr)) {\n    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {\n      arr2[i] = arr[i];\n    }\n\n    return arr2;\n  }\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _defineProperty; });\nfunction _defineProperty(obj, key, value) {\n  if (key in obj) {\n    Object.defineProperty(obj, key, {\n      value: value,\n      enumerable: true,\n      configurable: true,\n      writable: true\n    });\n  } else {\n    obj[key] = value;\n  }\n\n  return obj;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/defineProperty.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _iterableToArray; });\nfunction _iterableToArray(iter) {\n  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/iterableToArray.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _nonIterableSpread; });\nfunction _nonIterableSpread() {\n  throw new TypeError(\"Invalid attempt to spread non-iterable instance\");\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js?");

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

/***/ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _toConsumableArray; });\n/* harmony import */ var _arrayWithoutHoles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithoutHoles */ \"./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js\");\n/* harmony import */ var _iterableToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArray */ \"./node_modules/@babel/runtime/helpers/esm/iterableToArray.js\");\n/* harmony import */ var _nonIterableSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./nonIterableSpread */ \"./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js\");\n\n\n\nfunction _toConsumableArray(arr) {\n  return Object(_arrayWithoutHoles__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(arr) || Object(_iterableToArray__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(arr) || Object(_nonIterableSpread__WEBPACK_IMPORTED_MODULE_2__[\"default\"])();\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js?");

/***/ }),

/***/ "./node_modules/@wordpress/annotations/build-module/block/index.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/annotations/build-module/block/index.js ***!
  \*************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ \"@wordpress/hooks\");\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ \"@wordpress/data\");\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Adds annotation className to the block-list-block component.\n *\n * @param {Object} OriginalComponent The original BlockListBlock component.\n * @return {Object} The enhanced component.\n */\n\nvar addAnnotationClassName = function addAnnotationClassName(OriginalComponent) {\n  return Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__[\"withSelect\"])(function (select, _ref) {\n    var clientId = _ref.clientId;\n\n    var annotations = select('core/annotations').__experimentalGetAnnotationsForBlock(clientId);\n\n    return {\n      className: annotations.map(function (annotation) {\n        return 'is-annotated-by-' + annotation.source;\n      })\n    };\n  })(OriginalComponent);\n};\n\nObject(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__[\"addFilter\"])('editor.BlockListBlock', 'core/annotations', addAnnotationClassName);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/annotations/build-module/block/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/annotations/build-module/format/annotation.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@wordpress/annotations/build-module/format/annotation.js ***!
  \*******************************************************************************/
/*! exports provided: applyAnnotations, removeAnnotations, annotation */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"applyAnnotations\", function() { return applyAnnotations; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"removeAnnotations\", function() { return removeAnnotations; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"annotation\", function() { return annotation; });\n/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! memize */ \"./node_modules/memize/index.js\");\n/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(memize__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__);\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n\nvar FORMAT_NAME = 'core/annotation';\nvar ANNOTATION_ATTRIBUTE_PREFIX = 'annotation-text-';\nvar STORE_KEY = 'core/annotations';\n/**\n * Applies given annotations to the given record.\n *\n * @param {Object} record The record to apply annotations to.\n * @param {Array} annotations The annotation to apply.\n * @return {Object} A record with the annotations applied.\n */\n\nfunction applyAnnotations(record) {\n  var annotations = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];\n  annotations.forEach(function (annotation) {\n    var start = annotation.start,\n        end = annotation.end;\n\n    if (start > record.text.length) {\n      start = record.text.length;\n    }\n\n    if (end > record.text.length) {\n      end = record.text.length;\n    }\n\n    var className = ANNOTATION_ATTRIBUTE_PREFIX + annotation.source;\n    var id = ANNOTATION_ATTRIBUTE_PREFIX + annotation.id;\n    record = Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__[\"applyFormat\"])(record, {\n      type: FORMAT_NAME,\n      attributes: {\n        className: className,\n        id: id\n      }\n    }, start, end);\n  });\n  return record;\n}\n/**\n * Removes annotations from the given record.\n *\n * @param {Object} record Record to remove annotations from.\n * @return {Object} The cleaned record.\n */\n\nfunction removeAnnotations(record) {\n  return Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_2__[\"removeFormat\"])(record, 'core/annotation', 0, record.text.length);\n}\n/**\n * Retrieves the positions of annotations inside an array of formats.\n *\n * @param {Array} formats Formats with annotations in there.\n * @return {Object} ID keyed positions of annotations.\n */\n\nfunction retrieveAnnotationPositions(formats) {\n  var positions = {};\n  formats.forEach(function (characterFormats, i) {\n    characterFormats = characterFormats || [];\n    characterFormats = characterFormats.filter(function (format) {\n      return format.type === FORMAT_NAME;\n    });\n    characterFormats.forEach(function (format) {\n      var id = format.attributes.id;\n      id = id.replace(ANNOTATION_ATTRIBUTE_PREFIX, '');\n\n      if (!positions.hasOwnProperty(id)) {\n        positions[id] = {\n          start: i\n        };\n      } // Annotations refer to positions between characters.\n      // Formats refer to the character themselves.\n      // So we need to adjust for that here.\n\n\n      positions[id].end = i + 1;\n    });\n  });\n  return positions;\n}\n/**\n * Updates annotations in the state based on positions retrieved from RichText.\n *\n * @param {Array}    annotations           The annotations that are currently applied.\n * @param {Array}    positions             The current positions of the given annotations.\n * @param {Function} removeAnnotation      Function to remove an annotation from the state.\n * @param {Function} updateAnnotationRange Function to update an annotation range in the state.\n */\n\n\nfunction updateAnnotationsWithPositions(annotations, positions, _ref) {\n  var removeAnnotation = _ref.removeAnnotation,\n      updateAnnotationRange = _ref.updateAnnotationRange;\n  annotations.forEach(function (currentAnnotation) {\n    var position = positions[currentAnnotation.id]; // If we cannot find an annotation, delete it.\n\n    if (!position) {\n      // Apparently the annotation has been removed, so remove it from the state:\n      // Remove...\n      removeAnnotation(currentAnnotation.id);\n      return;\n    }\n\n    var start = currentAnnotation.start,\n        end = currentAnnotation.end;\n\n    if (start !== position.start || end !== position.end) {\n      updateAnnotationRange(currentAnnotation.id, position.start, position.end);\n    }\n  });\n}\n/**\n * Create prepareEditableTree memoized based on the annotation props.\n *\n * @param {Object} The props with annotations in them.\n *\n * @return {Function} The prepareEditableTree.\n */\n\n\nvar createPrepareEditableTree = memize__WEBPACK_IMPORTED_MODULE_0___default()(function (props) {\n  var annotations = props.annotations;\n  return function (formats, text) {\n    if (annotations.length === 0) {\n      return formats;\n    }\n\n    var record = {\n      formats: formats,\n      text: text\n    };\n    record = applyAnnotations(record, annotations);\n    return record.formats;\n  };\n});\n/**\n * Returns the annotations as a props object. Memoized to prevent re-renders.\n *\n * @param {Array} The annotations to put in the object.\n *\n * @return {Object} The annotations props object.\n */\n\nvar getAnnotationObject = memize__WEBPACK_IMPORTED_MODULE_0___default()(function (annotations) {\n  return {\n    annotations: annotations\n  };\n});\nvar annotation = {\n  name: FORMAT_NAME,\n  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Annotation'),\n  tagName: 'mark',\n  className: 'annotation-text',\n  attributes: {\n    className: 'class',\n    id: 'id'\n  },\n  edit: function edit() {\n    return null;\n  },\n  __experimentalGetPropsForEditableTreePreparation: function __experimentalGetPropsForEditableTreePreparation(select, _ref2) {\n    var richTextIdentifier = _ref2.richTextIdentifier,\n        blockClientId = _ref2.blockClientId;\n    return getAnnotationObject(select(STORE_KEY).__experimentalGetAnnotationsForRichText(blockClientId, richTextIdentifier));\n  },\n  __experimentalCreatePrepareEditableTree: createPrepareEditableTree,\n  __experimentalGetPropsForEditableTreeChangeHandler: function __experimentalGetPropsForEditableTreeChangeHandler(dispatch) {\n    return {\n      removeAnnotation: dispatch(STORE_KEY).__experimentalRemoveAnnotation,\n      updateAnnotationRange: dispatch(STORE_KEY).__experimentalUpdateAnnotationRange\n    };\n  },\n  __experimentalCreateOnChangeEditableValue: function __experimentalCreateOnChangeEditableValue(props) {\n    return function (formats) {\n      var positions = retrieveAnnotationPositions(formats);\n      var removeAnnotation = props.removeAnnotation,\n          updateAnnotationRange = props.updateAnnotationRange,\n          annotations = props.annotations;\n      updateAnnotationsWithPositions(annotations, positions, {\n        removeAnnotation: removeAnnotation,\n        updateAnnotationRange: updateAnnotationRange\n      });\n    };\n  }\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/annotations/build-module/format/annotation.js?");

/***/ }),

/***/ "./node_modules/@wordpress/annotations/build-module/format/index.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/annotations/build-module/format/index.js ***!
  \**************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/rich-text */ \"@wordpress/rich-text\");\n/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _annotation__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./annotation */ \"./node_modules/@wordpress/annotations/build-module/format/annotation.js\");\n\n\n/**\n * WordPress dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n\nvar name = _annotation__WEBPACK_IMPORTED_MODULE_2__[\"annotation\"].name,\n    settings = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_annotation__WEBPACK_IMPORTED_MODULE_2__[\"annotation\"], [\"name\"]);\n\nObject(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__[\"registerFormatType\"])(name, settings);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/annotations/build-module/format/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/annotations/build-module/index.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/annotations/build-module/index.js ***!
  \*******************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./store */ \"./node_modules/@wordpress/annotations/build-module/store/index.js\");\n/* harmony import */ var _format__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./format */ \"./node_modules/@wordpress/annotations/build-module/format/index.js\");\n/* harmony import */ var _block__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./block */ \"./node_modules/@wordpress/annotations/build-module/block/index.js\");\n/**\n * Internal dependencies\n */\n\n\n\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/annotations/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/annotations/build-module/store/actions.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/annotations/build-module/store/actions.js ***!
  \***************************************************************************/
/*! exports provided: __experimentalAddAnnotation, __experimentalRemoveAnnotation, __experimentalUpdateAnnotationRange, __experimentalRemoveAnnotationsBySource */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"__experimentalAddAnnotation\", function() { return __experimentalAddAnnotation; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"__experimentalRemoveAnnotation\", function() { return __experimentalRemoveAnnotation; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"__experimentalUpdateAnnotationRange\", function() { return __experimentalUpdateAnnotationRange; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"__experimentalRemoveAnnotationsBySource\", function() { return __experimentalRemoveAnnotationsBySource; });\n/* harmony import */ var uuid_v4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! uuid/v4 */ \"./node_modules/uuid/v4.js\");\n/* harmony import */ var uuid_v4__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(uuid_v4__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * External dependencies\n */\n\n/**\n * Adds an annotation to a block.\n *\n * The `block` attribute refers to a block ID that needs to be annotated.\n * `isBlockAnnotation` controls whether or not the annotation is a block\n * annotation. The `source` is the source of the annotation, this will be used\n * to identity groups of annotations.\n *\n * The `range` property is only relevant if the selector is 'range'.\n *\n * @param {Object} annotation         The annotation to add.\n * @param {string} blockClientId      The blockClientId to add the annotation to.\n * @param {string} richTextIdentifier Identifier for the RichText instance the annotation applies to.\n * @param {Object} range              The range at which to apply this annotation.\n * @param {number} range.start        The offset where the annotation should start.\n * @param {number} range.end          The offset where the annotation should end.\n * @param {string} [selector=\"range\"] The way to apply this annotation.\n * @param {string} [source=\"default\"] The source that added the annotation.\n * @param {string} [id=uuid()]        The ID the annotation should have.\n *                                    Generates a UUID by default.\n *\n * @return {Object} Action object.\n */\n\nfunction __experimentalAddAnnotation(_ref) {\n  var blockClientId = _ref.blockClientId,\n      _ref$richTextIdentifi = _ref.richTextIdentifier,\n      richTextIdentifier = _ref$richTextIdentifi === void 0 ? null : _ref$richTextIdentifi,\n      _ref$range = _ref.range,\n      range = _ref$range === void 0 ? null : _ref$range,\n      _ref$selector = _ref.selector,\n      selector = _ref$selector === void 0 ? 'range' : _ref$selector,\n      _ref$source = _ref.source,\n      source = _ref$source === void 0 ? 'default' : _ref$source,\n      _ref$id = _ref.id,\n      id = _ref$id === void 0 ? uuid_v4__WEBPACK_IMPORTED_MODULE_0___default()() : _ref$id;\n  var action = {\n    type: 'ANNOTATION_ADD',\n    id: id,\n    blockClientId: blockClientId,\n    richTextIdentifier: richTextIdentifier,\n    source: source,\n    selector: selector\n  };\n\n  if (selector === 'range') {\n    action.range = range;\n  }\n\n  return action;\n}\n/**\n * Removes an annotation with a specific ID.\n *\n * @param {string} annotationId The annotation to remove.\n *\n * @return {Object} Action object.\n */\n\nfunction __experimentalRemoveAnnotation(annotationId) {\n  return {\n    type: 'ANNOTATION_REMOVE',\n    annotationId: annotationId\n  };\n}\n/**\n * Updates the range of an annotation.\n *\n * @param {string} annotationId ID of the annotation to update.\n * @param {number} start The start of the new range.\n * @param {number} end The end of the new range.\n *\n * @return {Object} Action object.\n */\n\nfunction __experimentalUpdateAnnotationRange(annotationId, start, end) {\n  return {\n    type: 'ANNOTATION_UPDATE_RANGE',\n    annotationId: annotationId,\n    start: start,\n    end: end\n  };\n}\n/**\n * Removes all annotations of a specific source.\n *\n * @param {string} source The source to remove.\n *\n * @return {Object} Action object.\n */\n\nfunction __experimentalRemoveAnnotationsBySource(source) {\n  return {\n    type: 'ANNOTATION_REMOVE_SOURCE',\n    source: source\n  };\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/annotations/build-module/store/actions.js?");

/***/ }),

/***/ "./node_modules/@wordpress/annotations/build-module/store/index.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/annotations/build-module/store/index.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ \"@wordpress/data\");\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _reducer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./reducer */ \"./node_modules/@wordpress/annotations/build-module/store/reducer.js\");\n/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./selectors */ \"./node_modules/@wordpress/annotations/build-module/store/selectors.js\");\n/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./actions */ \"./node_modules/@wordpress/annotations/build-module/store/actions.js\");\n/**\n * WordPress Dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n\n\n/**\n * Module Constants\n */\n\nvar MODULE_KEY = 'core/annotations';\nvar store = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__[\"registerStore\"])(MODULE_KEY, {\n  reducer: _reducer__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  selectors: _selectors__WEBPACK_IMPORTED_MODULE_2__,\n  actions: _actions__WEBPACK_IMPORTED_MODULE_3__\n});\n/* harmony default export */ __webpack_exports__[\"default\"] = (store);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/annotations/build-module/store/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/annotations/build-module/store/reducer.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/annotations/build-module/store/reducer.js ***!
  \***************************************************************************/
/*! exports provided: annotations, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"annotations\", function() { return annotations; });\n/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ \"./node_modules/@babel/runtime/helpers/esm/defineProperty.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ \"./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);\n\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * Filters an array based on the predicate, but keeps the reference the same if\n * the array hasn't changed.\n *\n * @param {Array}    collection The collection to filter.\n * @param {Function} predicate  Function that determines if the item should stay\n *                              in the array.\n * @return {Array} Filtered array.\n */\n\nfunction filterWithReference(collection, predicate) {\n  var filteredCollection = collection.filter(predicate);\n  return collection.length === filteredCollection.length ? collection : filteredCollection;\n}\n/**\n * Verifies whether the given annotations is a valid annotation.\n *\n * @param {Object} annotation The annotation to verify.\n * @return {boolean} Whether the given annotation is valid.\n */\n\n\nfunction isValidAnnotationRange(annotation) {\n  return Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"isNumber\"])(annotation.start) && Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"isNumber\"])(annotation.end) && annotation.start <= annotation.end;\n}\n/**\n * Reducer managing annotations.\n *\n * @param {Array} state The annotations currently shown in the editor.\n * @param {Object} action Dispatched action.\n *\n * @return {Array} Updated state.\n */\n\n\nfunction annotations() {\n  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};\n  var action = arguments.length > 1 ? arguments[1] : undefined;\n\n  switch (action.type) {\n    case 'ANNOTATION_ADD':\n      var blockClientId = action.blockClientId;\n      var newAnnotation = {\n        id: action.id,\n        blockClientId: blockClientId,\n        richTextIdentifier: action.richTextIdentifier,\n        source: action.source,\n        selector: action.selector,\n        range: action.range\n      };\n\n      if (newAnnotation.selector === 'range' && !isValidAnnotationRange(newAnnotation.range)) {\n        return state;\n      }\n\n      var previousAnnotationsForBlock = Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"get\"])(state, blockClientId, []);\n      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__[\"default\"])({}, state, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, blockClientId, Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(previousAnnotationsForBlock).concat([newAnnotation])));\n\n    case 'ANNOTATION_REMOVE':\n      return Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"mapValues\"])(state, function (annotationsForBlock) {\n        return filterWithReference(annotationsForBlock, function (annotation) {\n          return annotation.id !== action.annotationId;\n        });\n      });\n\n    case 'ANNOTATION_UPDATE_RANGE':\n      return Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"mapValues\"])(state, function (annotationsForBlock) {\n        var hasChangedRange = false;\n        var newAnnotations = annotationsForBlock.map(function (annotation) {\n          if (annotation.id === action.annotationId) {\n            hasChangedRange = true;\n            return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__[\"default\"])({}, annotation, {\n              range: {\n                start: action.start,\n                end: action.end\n              }\n            });\n          }\n\n          return annotation;\n        });\n        return hasChangedRange ? newAnnotations : annotationsForBlock;\n      });\n\n    case 'ANNOTATION_REMOVE_SOURCE':\n      return Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"mapValues\"])(state, function (annotationsForBlock) {\n        return filterWithReference(annotationsForBlock, function (annotation) {\n          return annotation.source !== action.source;\n        });\n      });\n  }\n\n  return state;\n}\n/* harmony default export */ __webpack_exports__[\"default\"] = (annotations);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/annotations/build-module/store/reducer.js?");

/***/ }),

/***/ "./node_modules/@wordpress/annotations/build-module/store/selectors.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/annotations/build-module/store/selectors.js ***!
  \*****************************************************************************/
/*! exports provided: __experimentalGetAnnotationsForBlock, __experimentalGetAllAnnotationsForBlock, __experimentalGetAnnotationsForRichText, __experimentalGetAnnotations */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"__experimentalGetAnnotationsForBlock\", function() { return __experimentalGetAnnotationsForBlock; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"__experimentalGetAllAnnotationsForBlock\", function() { return __experimentalGetAllAnnotationsForBlock; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"__experimentalGetAnnotationsForRichText\", function() { return __experimentalGetAnnotationsForRichText; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"__experimentalGetAnnotations\", function() { return __experimentalGetAnnotations; });\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js\");\n/* harmony import */ var rememo__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! rememo */ \"./node_modules/rememo/es/rememo.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);\n\n\n\n/**\n * External dependencies\n */\n\n\n/**\n * Shared reference to an empty array for cases where it is important to avoid\n * returning a new array reference on every invocation, as in a connected or\n * other pure component which performs `shouldComponentUpdate` check on props.\n * This should be used as a last resort, since the normalized data should be\n * maintained by the reducer result in state.\n *\n * @type {Array}\n */\n\nvar EMPTY_ARRAY = [];\n/**\n * Returns the annotations for a specific client ID.\n *\n * @param {Object} state Editor state.\n * @param {string} clientId The ID of the block to get the annotations for.\n *\n * @return {Array} The annotations applicable to this block.\n */\n\nvar __experimentalGetAnnotationsForBlock = Object(rememo__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(function (state, blockClientId) {\n  return Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"get\"])(state, blockClientId, []).filter(function (annotation) {\n    return annotation.selector === 'block';\n  });\n}, function (state, blockClientId) {\n  return [Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"get\"])(state, blockClientId, EMPTY_ARRAY)];\n});\nvar __experimentalGetAllAnnotationsForBlock = function __experimentalGetAllAnnotationsForBlock(state, blockClientId) {\n  return Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"get\"])(state, blockClientId, EMPTY_ARRAY);\n};\n/**\n * Returns the annotations that apply to the given RichText instance.\n *\n * Both a blockClientId and a richTextIdentifier are required. This is because\n * a block might have multiple `RichText` components. This does mean that every\n * block needs to implement annotations itself.\n *\n * @param {Object} state              Editor state.\n * @param {string} blockClientId      The client ID for the block.\n * @param {string} richTextIdentifier Unique identifier that identifies the given RichText.\n * @return {Array} All the annotations relevant for the `RichText`.\n */\n\nvar __experimentalGetAnnotationsForRichText = Object(rememo__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(function (state, blockClientId, richTextIdentifier) {\n  return Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"get\"])(state, blockClientId, []).filter(function (annotation) {\n    return annotation.selector === 'range' && richTextIdentifier === annotation.richTextIdentifier;\n  }).map(function (annotation) {\n    var range = annotation.range,\n        other = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(annotation, [\"range\"]);\n\n    return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, range, other);\n  });\n}, function (state, blockClientId) {\n  return [Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"get\"])(state, blockClientId, EMPTY_ARRAY)];\n});\n/**\n * Returns all annotations in the editor state.\n *\n * @param {Object} state Editor state.\n * @return {Array} All annotations currently applied.\n */\n\nfunction __experimentalGetAnnotations(state) {\n  return Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"flatMap\"])(state, function (annotations) {\n    return annotations;\n  });\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/annotations/build-module/store/selectors.js?");

/***/ }),

/***/ "./node_modules/memize/index.js":
/*!**************************************!*\
  !*** ./node_modules/memize/index.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = function memize( fn, options ) {\n\tvar size = 0,\n\t\tmaxSize, head, tail;\n\n\tif ( options && options.maxSize ) {\n\t\tmaxSize = options.maxSize;\n\t}\n\n\tfunction memoized( /* ...args */ ) {\n\t\tvar node = head,\n\t\t\tlen = arguments.length,\n\t\t\targs, i;\n\n\t\tsearchCache: while ( node ) {\n\t\t\t// Perform a shallow equality test to confirm that whether the node\n\t\t\t// under test is a candidate for the arguments passed. Two arrays\n\t\t\t// are shallowly equal if their length matches and each entry is\n\t\t\t// strictly equal between the two sets. Avoid abstracting to a\n\t\t\t// function which could incur an arguments leaking deoptimization.\n\n\t\t\t// Check whether node arguments match arguments length\n\t\t\tif ( node.args.length !== arguments.length ) {\n\t\t\t\tnode = node.next;\n\t\t\t\tcontinue;\n\t\t\t}\n\n\t\t\t// Check whether node arguments match arguments values\n\t\t\tfor ( i = 0; i < len; i++ ) {\n\t\t\t\tif ( node.args[ i ] !== arguments[ i ] ) {\n\t\t\t\t\tnode = node.next;\n\t\t\t\t\tcontinue searchCache;\n\t\t\t\t}\n\t\t\t}\n\n\t\t\t// At this point we can assume we've found a match\n\n\t\t\t// Surface matched node to head if not already\n\t\t\tif ( node !== head ) {\n\t\t\t\t// As tail, shift to previous. Must only shift if not also\n\t\t\t\t// head, since if both head and tail, there is no previous.\n\t\t\t\tif ( node === tail ) {\n\t\t\t\t\ttail = node.prev;\n\t\t\t\t}\n\n\t\t\t\t// Adjust siblings to point to each other. If node was tail,\n\t\t\t\t// this also handles new tail's empty `next` assignment.\n\t\t\t\tnode.prev.next = node.next;\n\t\t\t\tif ( node.next ) {\n\t\t\t\t\tnode.next.prev = node.prev;\n\t\t\t\t}\n\n\t\t\t\tnode.next = head;\n\t\t\t\tnode.prev = null;\n\t\t\t\thead.prev = node;\n\t\t\t\thead = node;\n\t\t\t}\n\n\t\t\t// Return immediately\n\t\t\treturn node.val;\n\t\t}\n\n\t\t// No cached value found. Continue to insertion phase:\n\n\t\t// Create a copy of arguments (avoid leaking deoptimization)\n\t\targs = new Array( len );\n\t\tfor ( i = 0; i < len; i++ ) {\n\t\t\targs[ i ] = arguments[ i ];\n\t\t}\n\n\t\tnode = {\n\t\t\targs: args,\n\n\t\t\t// Generate the result from original function\n\t\t\tval: fn.apply( null, args )\n\t\t};\n\n\t\t// Don't need to check whether node is already head, since it would\n\t\t// have been returned above already if it was\n\n\t\t// Shift existing head down list\n\t\tif ( head ) {\n\t\t\thead.prev = node;\n\t\t\tnode.next = head;\n\t\t} else {\n\t\t\t// If no head, follows that there's no tail (at initial or reset)\n\t\t\ttail = node;\n\t\t}\n\n\t\t// Trim tail if we're reached max size and are pending cache insertion\n\t\tif ( size === maxSize ) {\n\t\t\ttail = tail.prev;\n\t\t\ttail.next = null;\n\t\t} else {\n\t\t\tsize++;\n\t\t}\n\n\t\thead = node;\n\n\t\treturn node.val;\n\t}\n\n\tmemoized.clear = function() {\n\t\thead = null;\n\t\ttail = null;\n\t\tsize = 0;\n\t};\n\n\tif ( false ) {}\n\n\treturn memoized;\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/memize/index.js?");

/***/ }),

/***/ "./node_modules/rememo/es/rememo.js":
/*!******************************************!*\
  !*** ./node_modules/rememo/es/rememo.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n\nvar LEAF_KEY, hasWeakMap;\n\n/**\n * Arbitrary value used as key for referencing cache object in WeakMap tree.\n *\n * @type {Object}\n */\nLEAF_KEY = {};\n\n/**\n * Whether environment supports WeakMap.\n *\n * @type {boolean}\n */\nhasWeakMap = typeof WeakMap !== 'undefined';\n\n/**\n * Returns the first argument as the sole entry in an array.\n *\n * @param {*} value Value to return.\n *\n * @return {Array} Value returned as entry in array.\n */\nfunction arrayOf( value ) {\n\treturn [ value ];\n}\n\n/**\n * Returns true if the value passed is object-like, or false otherwise. A value\n * is object-like if it can support property assignment, e.g. object or array.\n *\n * @param {*} value Value to test.\n *\n * @return {boolean} Whether value is object-like.\n */\nfunction isObjectLike( value ) {\n\treturn !! value && 'object' === typeof value;\n}\n\n/**\n * Creates and returns a new cache object.\n *\n * @return {Object} Cache object.\n */\nfunction createCache() {\n\tvar cache = {\n\t\tclear: function() {\n\t\t\tcache.head = null;\n\t\t},\n\t};\n\n\treturn cache;\n}\n\n/**\n * Returns true if entries within the two arrays are strictly equal by\n * reference from a starting index.\n *\n * @param {Array}  a         First array.\n * @param {Array}  b         Second array.\n * @param {number} fromIndex Index from which to start comparison.\n *\n * @return {boolean} Whether arrays are shallowly equal.\n */\nfunction isShallowEqual( a, b, fromIndex ) {\n\tvar i;\n\n\tif ( a.length !== b.length ) {\n\t\treturn false;\n\t}\n\n\tfor ( i = fromIndex; i < a.length; i++ ) {\n\t\tif ( a[ i ] !== b[ i ] ) {\n\t\t\treturn false;\n\t\t}\n\t}\n\n\treturn true;\n}\n\n/**\n * Returns a memoized selector function. The getDependants function argument is\n * called before the memoized selector and is expected to return an immutable\n * reference or array of references on which the selector depends for computing\n * its own return value. The memoize cache is preserved only as long as those\n * dependant references remain the same. If getDependants returns a different\n * reference(s), the cache is cleared and the selector value regenerated.\n *\n * @param {Function} selector      Selector function.\n * @param {Function} getDependants Dependant getter returning an immutable\n *                                 reference or array of reference used in\n *                                 cache bust consideration.\n *\n * @return {Function} Memoized selector.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function( selector, getDependants ) {\n\tvar rootCache, getCache;\n\n\t// Use object source as dependant if getter not provided\n\tif ( ! getDependants ) {\n\t\tgetDependants = arrayOf;\n\t}\n\n\t/**\n\t * Returns the root cache. If WeakMap is supported, this is assigned to the\n\t * root WeakMap cache set, otherwise it is a shared instance of the default\n\t * cache object.\n\t *\n\t * @return {(WeakMap|Object)} Root cache object.\n\t */\n\tfunction getRootCache() {\n\t\treturn rootCache;\n\t}\n\n\t/**\n\t * Returns the cache for a given dependants array. When possible, a WeakMap\n\t * will be used to create a unique cache for each set of dependants. This\n\t * is feasible due to the nature of WeakMap in allowing garbage collection\n\t * to occur on entries where the key object is no longer referenced. Since\n\t * WeakMap requires the key to be an object, this is only possible when the\n\t * dependant is object-like. The root cache is created as a hierarchy where\n\t * each top-level key is the first entry in a dependants set, the value a\n\t * WeakMap where each key is the next dependant, and so on. This continues\n\t * so long as the dependants are object-like. If no dependants are object-\n\t * like, then the cache is shared across all invocations.\n\t *\n\t * @see isObjectLike\n\t *\n\t * @param {Array} dependants Selector dependants.\n\t *\n\t * @return {Object} Cache object.\n\t */\n\tfunction getWeakMapCache( dependants ) {\n\t\tvar caches = rootCache,\n\t\t\tisUniqueByDependants = true,\n\t\t\ti, dependant, map, cache;\n\n\t\tfor ( i = 0; i < dependants.length; i++ ) {\n\t\t\tdependant = dependants[ i ];\n\n\t\t\t// Can only compose WeakMap from object-like key.\n\t\t\tif ( ! isObjectLike( dependant ) ) {\n\t\t\t\tisUniqueByDependants = false;\n\t\t\t\tbreak;\n\t\t\t}\n\n\t\t\t// Does current segment of cache already have a WeakMap?\n\t\t\tif ( caches.has( dependant ) ) {\n\t\t\t\t// Traverse into nested WeakMap.\n\t\t\t\tcaches = caches.get( dependant );\n\t\t\t} else {\n\t\t\t\t// Create, set, and traverse into a new one.\n\t\t\t\tmap = new WeakMap();\n\t\t\t\tcaches.set( dependant, map );\n\t\t\t\tcaches = map;\n\t\t\t}\n\t\t}\n\n\t\t// We use an arbitrary (but consistent) object as key for the last item\n\t\t// in the WeakMap to serve as our running cache.\n\t\tif ( ! caches.has( LEAF_KEY ) ) {\n\t\t\tcache = createCache();\n\t\t\tcache.isUniqueByDependants = isUniqueByDependants;\n\t\t\tcaches.set( LEAF_KEY, cache );\n\t\t}\n\n\t\treturn caches.get( LEAF_KEY );\n\t}\n\n\t// Assign cache handler by availability of WeakMap\n\tgetCache = hasWeakMap ? getWeakMapCache : getRootCache;\n\n\t/**\n\t * Resets root memoization cache.\n\t */\n\tfunction clear() {\n\t\trootCache = hasWeakMap ? new WeakMap() : createCache();\n\t}\n\n\t// eslint-disable-next-line jsdoc/check-param-names\n\t/**\n\t * The augmented selector call, considering first whether dependants have\n\t * changed before passing it to underlying memoize function.\n\t *\n\t * @param {Object} source    Source object for derivation.\n\t * @param {...*}   extraArgs Additional arguments to pass to selector.\n\t *\n\t * @return {*} Selector result.\n\t */\n\tfunction callSelector( /* source, ...extraArgs */ ) {\n\t\tvar len = arguments.length,\n\t\t\tcache, node, i, args, dependants;\n\n\t\t// Create copy of arguments (avoid leaking deoptimization).\n\t\targs = new Array( len );\n\t\tfor ( i = 0; i < len; i++ ) {\n\t\t\targs[ i ] = arguments[ i ];\n\t\t}\n\n\t\tdependants = getDependants.apply( null, args );\n\t\tcache = getCache( dependants );\n\n\t\t// If not guaranteed uniqueness by dependants (primitive type or lack\n\t\t// of WeakMap support), shallow compare against last dependants and, if\n\t\t// references have changed, destroy cache to recalculate result.\n\t\tif ( ! cache.isUniqueByDependants ) {\n\t\t\tif ( cache.lastDependants && ! isShallowEqual( dependants, cache.lastDependants, 0 ) ) {\n\t\t\t\tcache.clear();\n\t\t\t}\n\n\t\t\tcache.lastDependants = dependants;\n\t\t}\n\n\t\tnode = cache.head;\n\t\twhile ( node ) {\n\t\t\t// Check whether node arguments match arguments\n\t\t\tif ( ! isShallowEqual( node.args, args, 1 ) ) {\n\t\t\t\tnode = node.next;\n\t\t\t\tcontinue;\n\t\t\t}\n\n\t\t\t// At this point we can assume we've found a match\n\n\t\t\t// Surface matched node to head if not already\n\t\t\tif ( node !== cache.head ) {\n\t\t\t\t// Adjust siblings to point to each other.\n\t\t\t\tnode.prev.next = node.next;\n\t\t\t\tif ( node.next ) {\n\t\t\t\t\tnode.next.prev = node.prev;\n\t\t\t\t}\n\n\t\t\t\tnode.next = cache.head;\n\t\t\t\tnode.prev = null;\n\t\t\t\tcache.head.prev = node;\n\t\t\t\tcache.head = node;\n\t\t\t}\n\n\t\t\t// Return immediately\n\t\t\treturn node.val;\n\t\t}\n\n\t\t// No cached value found. Continue to insertion phase:\n\n\t\tnode = {\n\t\t\t// Generate the result from original function\n\t\t\tval: selector.apply( null, args ),\n\t\t};\n\n\t\t// Avoid including the source object in the cache.\n\t\targs[ 0 ] = null;\n\t\tnode.args = args;\n\n\t\t// Don't need to check whether node is already head, since it would\n\t\t// have been returned above already if it was\n\n\t\t// Shift existing head down list\n\t\tif ( cache.head ) {\n\t\t\tcache.head.prev = node;\n\t\t\tnode.next = cache.head;\n\t\t}\n\n\t\tcache.head = node;\n\n\t\treturn node.val;\n\t}\n\n\tcallSelector.getDependants = getDependants;\n\tcallSelector.clear = clear;\n\tclear();\n\n\treturn callSelector;\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rememo/es/rememo.js?");

/***/ }),

/***/ "./node_modules/uuid/lib/bytesToUuid.js":
/*!**********************************************!*\
  !*** ./node_modules/uuid/lib/bytesToUuid.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("/**\n * Convert array of 16 byte values to UUID string format of the form:\n * XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX\n */\nvar byteToHex = [];\nfor (var i = 0; i < 256; ++i) {\n  byteToHex[i] = (i + 0x100).toString(16).substr(1);\n}\n\nfunction bytesToUuid(buf, offset) {\n  var i = offset || 0;\n  var bth = byteToHex;\n  // join used to fix memory issue caused by concatenation: https://bugs.chromium.org/p/v8/issues/detail?id=3175#c4\n  return ([bth[buf[i++]], bth[buf[i++]], \n\tbth[buf[i++]], bth[buf[i++]], '-',\n\tbth[buf[i++]], bth[buf[i++]], '-',\n\tbth[buf[i++]], bth[buf[i++]], '-',\n\tbth[buf[i++]], bth[buf[i++]], '-',\n\tbth[buf[i++]], bth[buf[i++]],\n\tbth[buf[i++]], bth[buf[i++]],\n\tbth[buf[i++]], bth[buf[i++]]]).join('');\n}\n\nmodule.exports = bytesToUuid;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/uuid/lib/bytesToUuid.js?");

/***/ }),

/***/ "./node_modules/uuid/lib/rng-browser.js":
/*!**********************************************!*\
  !*** ./node_modules/uuid/lib/rng-browser.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// Unique ID creation requires a high quality random # generator.  In the\n// browser this is a little complicated due to unknown quality of Math.random()\n// and inconsistent support for the `crypto` API.  We do the best we can via\n// feature-detection\n\n// getRandomValues needs to be invoked in a context where \"this\" is a Crypto\n// implementation. Also, find the complete implementation of crypto on IE11.\nvar getRandomValues = (typeof(crypto) != 'undefined' && crypto.getRandomValues && crypto.getRandomValues.bind(crypto)) ||\n                      (typeof(msCrypto) != 'undefined' && typeof window.msCrypto.getRandomValues == 'function' && msCrypto.getRandomValues.bind(msCrypto));\n\nif (getRandomValues) {\n  // WHATWG crypto RNG - http://wiki.whatwg.org/wiki/Crypto\n  var rnds8 = new Uint8Array(16); // eslint-disable-line no-undef\n\n  module.exports = function whatwgRNG() {\n    getRandomValues(rnds8);\n    return rnds8;\n  };\n} else {\n  // Math.random()-based (RNG)\n  //\n  // If all else fails, use Math.random().  It's fast, but is of unspecified\n  // quality.\n  var rnds = new Array(16);\n\n  module.exports = function mathRNG() {\n    for (var i = 0, r; i < 16; i++) {\n      if ((i & 0x03) === 0) r = Math.random() * 0x100000000;\n      rnds[i] = r >>> ((i & 0x03) << 3) & 0xff;\n    }\n\n    return rnds;\n  };\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/uuid/lib/rng-browser.js?");

/***/ }),

/***/ "./node_modules/uuid/v4.js":
/*!*********************************!*\
  !*** ./node_modules/uuid/v4.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var rng = __webpack_require__(/*! ./lib/rng */ \"./node_modules/uuid/lib/rng-browser.js\");\nvar bytesToUuid = __webpack_require__(/*! ./lib/bytesToUuid */ \"./node_modules/uuid/lib/bytesToUuid.js\");\n\nfunction v4(options, buf, offset) {\n  var i = buf && offset || 0;\n\n  if (typeof(options) == 'string') {\n    buf = options === 'binary' ? new Array(16) : null;\n    options = null;\n  }\n  options = options || {};\n\n  var rnds = options.random || (options.rng || rng)();\n\n  // Per 4.4, set bits for version and `clock_seq_hi_and_reserved`\n  rnds[6] = (rnds[6] & 0x0f) | 0x40;\n  rnds[8] = (rnds[8] & 0x3f) | 0x80;\n\n  // Copy bytes to buffer, if provided\n  if (buf) {\n    for (var ii = 0; ii < 16; ++ii) {\n      buf[i + ii] = rnds[ii];\n    }\n  }\n\n  return buf || bytesToUuid(rnds);\n}\n\nmodule.exports = v4;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/uuid/v4.js?");

/***/ }),

/***/ "@wordpress/data":
/*!***************************************!*\
  !*** external {"this":["wp","data"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"data\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22data%22%5D%7D?");

/***/ }),

/***/ "@wordpress/hooks":
/*!****************************************!*\
  !*** external {"this":["wp","hooks"]} ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"hooks\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22hooks%22%5D%7D?");

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"i18n\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22i18n%22%5D%7D?");

/***/ }),

/***/ "@wordpress/rich-text":
/*!*******************************************!*\
  !*** external {"this":["wp","richText"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"richText\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22richText%22%5D%7D?");

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