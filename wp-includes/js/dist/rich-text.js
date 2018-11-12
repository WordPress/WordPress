this["wp"] = this["wp"] || {}; this["wp"]["richText"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/rich-text/build-module/index.js");
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

/***/ "./node_modules/@wordpress/rich-text/build-module/apply-format.js":
/*!************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/apply-format.js ***!
  \************************************************************************/
/*! exports provided: applyFormat */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "applyFormat", function() { return applyFormat; });
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _normalise_formats__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./normalise-formats */ "./node_modules/@wordpress/rich-text/build-module/normalise-formats.js");
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Apply a format object to a Rich Text value from the given `startIndex` to the
 * given `endIndex`. Indices are retrieved from the selection if none are
 * provided.
 *
 * @param {Object} value      Value to modify.
 * @param {Object} format     Format to apply.
 * @param {number} startIndex Start index.
 * @param {number} endIndex   End index.
 *
 * @return {Object} A new value with the format applied.
 */

function applyFormat(_ref, format) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end;
  var startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : start;
  var endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : end;
  var newFormats = formats.slice(0); // The selection is collpased.

  if (startIndex === endIndex) {
    var startFormat = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(newFormats[startIndex], {
      type: format.type
    }); // If the caret is at a format of the same type, expand start and end to
    // the edges of the format. This is useful to apply new attributes.

    if (startFormat) {
      while (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(newFormats[startIndex], startFormat)) {
        applyFormats(newFormats, startIndex, format);
        startIndex--;
      }

      endIndex++;

      while (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(newFormats[endIndex], startFormat)) {
        applyFormats(newFormats, endIndex, format);
        endIndex++;
      } // Otherwise, insert a placeholder with the format so new input appears
      // with the format applied.

    } else {
      var previousFormat = newFormats[startIndex - 1] || [];
      var hasType = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(previousFormat, {
        type: format.type
      });
      return {
        formats: formats,
        text: text,
        start: start,
        end: end,
        formatPlaceholder: {
          index: startIndex,
          format: hasType ? undefined : format
        }
      };
    }
  } else {
    for (var index = startIndex; index < endIndex; index++) {
      applyFormats(newFormats, index, format);
    }
  }

  return Object(_normalise_formats__WEBPACK_IMPORTED_MODULE_1__["normaliseFormats"])({
    formats: newFormats,
    text: text,
    start: start,
    end: end
  });
}

function applyFormats(formats, index, format) {
  if (formats[index]) {
    var newFormatsAtIndex = formats[index].filter(function (_ref2) {
      var type = _ref2.type;
      return type !== format.type;
    });
    newFormatsAtIndex.push(format);
    formats[index] = newFormatsAtIndex;
  } else {
    formats[index] = [format];
  }
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/char-at.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/char-at.js ***!
  \*******************************************************************/
/*! exports provided: charAt */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "charAt", function() { return charAt; });
/**
 * Gets the character at the specified index, or returns `undefined` if no
 * character was found.
 *
 * @param {Object} value Value to get the character from.
 * @param {string} index Index to use.
 *
 * @return {?string} A one character long string, or undefined.
 */
function charAt(_ref, index) {
  var text = _ref.text;
  return text[index];
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/concat.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/concat.js ***!
  \******************************************************************/
/*! exports provided: concat */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "concat", function() { return concat; });
/* harmony import */ var _normalise_formats__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./normalise-formats */ "./node_modules/@wordpress/rich-text/build-module/normalise-formats.js");
/**
 * Internal dependencies
 */

/**
 * Combine all Rich Text values into one. This is similar to
 * `String.prototype.concat`.
 *
 * @param {...[object]} values An array of all values to combine.
 *
 * @return {Object} A new value combining all given records.
 */

function concat() {
  for (var _len = arguments.length, values = new Array(_len), _key = 0; _key < _len; _key++) {
    values[_key] = arguments[_key];
  }

  return Object(_normalise_formats__WEBPACK_IMPORTED_MODULE_0__["normaliseFormats"])(values.reduce(function (accumlator, _ref) {
    var formats = _ref.formats,
        text = _ref.text;
    return {
      text: accumlator.text + text,
      formats: accumlator.formats.concat(formats)
    };
  }));
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/create-element.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/create-element.js ***!
  \**************************************************************************/
/*! exports provided: createElement */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createElement", function() { return createElement; });
/**
 * Parse the given HTML into a body element.
 *
 * @param {HTMLDocument} document The HTML document to use to parse.
 * @param {string}       html     The HTML to parse.
 *
 * @return {HTMLBodyElement} Body element with parsed HTML.
 */
function createElement(_ref, html) {
  var implementation = _ref.implementation;

  var _implementation$creat = implementation.createHTMLDocument(''),
      body = _implementation$creat.body;

  body.innerHTML = html;
  return body;
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/create.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/create.js ***!
  \******************************************************************/
/*! exports provided: create */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "create", function() { return create; });
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/typeof */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _is_empty__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./is-empty */ "./node_modules/@wordpress/rich-text/build-module/is-empty.js");
/* harmony import */ var _is_format_equal__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./is-format-equal */ "./node_modules/@wordpress/rich-text/build-module/is-format-equal.js");
/* harmony import */ var _create_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./create-element */ "./node_modules/@wordpress/rich-text/build-module/create-element.js");
/* harmony import */ var _special_characters__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./special-characters */ "./node_modules/@wordpress/rich-text/build-module/special-characters.js");



/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Browser dependencies
 */

var _window$Node = window.Node,
    TEXT_NODE = _window$Node.TEXT_NODE,
    ELEMENT_NODE = _window$Node.ELEMENT_NODE;

function createEmptyValue() {
  return {
    formats: [],
    text: ''
  };
}

function simpleFindKey(object, value) {
  for (var key in object) {
    if (object[key] === value) {
      return key;
    }
  }
}

function toFormat(_ref) {
  var type = _ref.type,
      attributes = _ref.attributes;
  var formatType;

  if (attributes && attributes.class) {
    formatType = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["select"])('core/rich-text').getFormatTypeForClassName(attributes.class);

    if (formatType) {
      // Preserve any additional classes.
      attributes.class = " ".concat(attributes.class, " ").replace(" ".concat(formatType.className, " "), ' ').trim();

      if (!attributes.class) {
        delete attributes.class;
      }
    }
  }

  if (!formatType) {
    formatType = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["select"])('core/rich-text').getFormatTypeForBareElement(type);
  }

  if (!formatType) {
    return attributes ? {
      type: type,
      attributes: attributes
    } : {
      type: type
    };
  }

  if (formatType.__experimentalCreatePrepareEditableTree) {
    return null;
  }

  if (!attributes) {
    return {
      type: formatType.name
    };
  }

  var registeredAttributes = {};
  var unregisteredAttributes = {};

  for (var name in attributes) {
    var key = simpleFindKey(formatType.attributes, name);

    if (key) {
      registeredAttributes[key] = attributes[name];
    } else {
      unregisteredAttributes[name] = attributes[name];
    }
  }

  return {
    type: formatType.name,
    attributes: registeredAttributes,
    unregisteredAttributes: unregisteredAttributes
  };
}
/**
 * Create a RichText value from an `Element` tree (DOM), an HTML string or a
 * plain text string, with optionally a `Range` object to set the selection. If
 * called without any input, an empty value will be created. If
 * `multilineTag` is provided, any content of direct children whose type matches
 * `multilineTag` will be separated by two newlines. The optional functions can
 * be used to filter out content.
 *
 * @param {?Object}   $1                      Optional named argements.
 * @param {?Element}  $1.element              Element to create value from.
 * @param {?string}   $1.text                 Text to create value from.
 * @param {?string}   $1.html                 HTML to create value from.
 * @param {?Range}    $1.range                Range to create value from.
 * @param {?string}   $1.multilineTag         Multiline tag if the structure is
 *                                            multiline.
 * @param {?Array}    $1.multilineWrapperTags Tags where lines can be found if
 *                                            nesting is possible.
 * @param {?Function} $1.removeNode           Function to declare whether the
 *                                            given node should be removed.
 * @param {?Function} $1.unwrapNode           Function to declare whether the
 *                                            given node should be unwrapped.
 * @param {?Function} $1.filterString         Function to filter the given
 *                                            string.
 * @param {?Function} $1.removeAttribute      Wether to remove an attribute
 *                                            based on the name.
 *
 * @return {Object} A rich text value.
 */


function create() {
  var _ref2 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
      element = _ref2.element,
      text = _ref2.text,
      html = _ref2.html,
      range = _ref2.range,
      multilineTag = _ref2.multilineTag,
      multilineWrapperTags = _ref2.multilineWrapperTags,
      removeNode = _ref2.removeNode,
      unwrapNode = _ref2.unwrapNode,
      filterString = _ref2.filterString,
      removeAttribute = _ref2.removeAttribute;

  if (typeof text === 'string' && text.length > 0) {
    return {
      formats: Array(text.length),
      text: text
    };
  }

  if (typeof html === 'string' && html.length > 0) {
    element = Object(_create_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(document, html);
  }

  if (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_1__["default"])(element) !== 'object') {
    return createEmptyValue();
  }

  if (!multilineTag) {
    return createFromElement({
      element: element,
      range: range,
      removeNode: removeNode,
      unwrapNode: unwrapNode,
      filterString: filterString,
      removeAttribute: removeAttribute
    });
  }

  return createFromMultilineElement({
    element: element,
    range: range,
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    removeNode: removeNode,
    unwrapNode: unwrapNode,
    filterString: filterString,
    removeAttribute: removeAttribute
  });
}
/**
 * Helper to accumulate the value's selection start and end from the current
 * node and range.
 *
 * @param {Object} accumulator Object to accumulate into.
 * @param {Node}   node        Node to create value with.
 * @param {Range}  range       Range to create value with.
 * @param {Object} value       Value that is being accumulated.
 */

function accumulateSelection(accumulator, node, range, value) {
  if (!range) {
    return;
  }

  var parentNode = node.parentNode;
  var startContainer = range.startContainer,
      startOffset = range.startOffset,
      endContainer = range.endContainer,
      endOffset = range.endOffset;
  var currentLength = accumulator.text.length; // Selection can be extracted from value.

  if (value.start !== undefined) {
    accumulator.start = currentLength + value.start; // Range indicates that the current node has selection.
  } else if (node === startContainer && node.nodeType === TEXT_NODE) {
    accumulator.start = currentLength + startOffset; // Range indicates that the current node is selected.
  } else if (parentNode === startContainer && node === startContainer.childNodes[startOffset]) {
    accumulator.start = currentLength; // Range indicates that the selection is after the current node.
  } else if (parentNode === startContainer && node === startContainer.childNodes[startOffset - 1]) {
    accumulator.start = currentLength + value.text.length; // Fallback if no child inside handled the selection.
  } else if (node === startContainer) {
    accumulator.start = currentLength;
  } // Selection can be extracted from value.


  if (value.end !== undefined) {
    accumulator.end = currentLength + value.end; // Range indicates that the current node has selection.
  } else if (node === endContainer && node.nodeType === TEXT_NODE) {
    accumulator.end = currentLength + endOffset; // Range indicates that the current node is selected.
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset - 1]) {
    accumulator.end = currentLength + value.text.length; // Range indicates that the selection is before the current node.
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset]) {
    accumulator.end = currentLength; // Fallback if no child inside handled the selection.
  } else if (node === endContainer) {
    accumulator.end = currentLength + endOffset;
  }
}
/**
 * Adjusts the start and end offsets from a range based on a text filter.
 *
 * @param {Node}     node   Node of which the text should be filtered.
 * @param {Range}    range  The range to filter.
 * @param {Function} filter Function to use to filter the text.
 *
 * @return {?Object} Object containing range properties.
 */


function filterRange(node, range, filter) {
  if (!range) {
    return;
  }

  var startContainer = range.startContainer,
      endContainer = range.endContainer;
  var startOffset = range.startOffset,
      endOffset = range.endOffset;

  if (node === startContainer) {
    startOffset = filter(node.nodeValue.slice(0, startOffset)).length;
  }

  if (node === endContainer) {
    endOffset = filter(node.nodeValue.slice(0, endOffset)).length;
  }

  return {
    startContainer: startContainer,
    startOffset: startOffset,
    endContainer: endContainer,
    endOffset: endOffset
  };
}
/**
 * Creates a Rich Text value from a DOM element and range.
 *
 * @param {Object}    $1                      Named argements.
 * @param {?Element}  $1.element              Element to create value from.
 * @param {?Range}    $1.range                Range to create value from.
 * @param {?string}   $1.multilineTag         Multiline tag if the structure is
 *                                            multiline.
 * @param {?Array}    $1.multilineWrapperTags Tags where lines can be found if
 *                                            nesting is possible.
 * @param {?Function} $1.removeNode           Function to declare whether the
 *                                            given node should be removed.
 * @param {?Function} $1.unwrapNode           Function to declare whether the
 *                                            given node should be unwrapped.
 * @param {?Function} $1.filterString         Function to filter the given
 *                                            string.
 * @param {?Function} $1.removeAttribute      Wether to remove an attribute
 *                                            based on the name.
 *
 * @return {Object} A rich text value.
 */


function createFromElement(_ref3) {
  var element = _ref3.element,
      range = _ref3.range,
      multilineTag = _ref3.multilineTag,
      multilineWrapperTags = _ref3.multilineWrapperTags,
      _ref3$currentWrapperT = _ref3.currentWrapperTags,
      currentWrapperTags = _ref3$currentWrapperT === void 0 ? [] : _ref3$currentWrapperT,
      removeNode = _ref3.removeNode,
      unwrapNode = _ref3.unwrapNode,
      filterString = _ref3.filterString,
      removeAttribute = _ref3.removeAttribute;
  var accumulator = createEmptyValue();

  if (!element) {
    return accumulator;
  }

  if (!element.hasChildNodes()) {
    accumulateSelection(accumulator, element, range, createEmptyValue());
    return accumulator;
  }

  var length = element.childNodes.length; // Remove any line breaks in text nodes. They are not content, but used to
  // format the HTML. Line breaks in HTML are stored as BR elements.
  // See https://www.w3.org/TR/html5/syntax.html#newlines.

  var filterStringComplete = function filterStringComplete(string) {
    string = string.replace(/[\r\n]/g, '');

    if (filterString) {
      string = filterString(string);
    }

    return string;
  }; // Optimise for speed.


  for (var index = 0; index < length; index++) {
    var node = element.childNodes[index];
    var type = node.nodeName.toLowerCase();

    if (node.nodeType === TEXT_NODE) {
      var _text = filterStringComplete(node.nodeValue);

      range = filterRange(node, range, filterStringComplete);
      accumulateSelection(accumulator, node, range, {
        text: _text
      });
      accumulator.text += _text; // Create a sparse array of the same length as `text`, in which
      // formats can be added.

      accumulator.formats.length += _text.length;
      continue;
    }

    if (node.nodeType !== ELEMENT_NODE) {
      continue;
    }

    if (removeNode && removeNode(node) || unwrapNode && unwrapNode(node) && !node.hasChildNodes()) {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      continue;
    }

    if (type === 'br') {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      accumulator.text += '\n';
      accumulator.formats.length += 1;
      continue;
    }

    var lastFormats = accumulator.formats[accumulator.formats.length - 1];
    var lastFormat = lastFormats && lastFormats[lastFormats.length - 1];
    var format = void 0;
    var value = void 0;

    if (!unwrapNode || !unwrapNode(node)) {
      var newFormat = toFormat({
        type: type,
        attributes: getAttributes({
          element: node,
          removeAttribute: removeAttribute
        })
      });

      if (newFormat) {
        // Reuse the last format if it's equal.
        if (Object(_is_format_equal__WEBPACK_IMPORTED_MODULE_4__["isFormatEqual"])(newFormat, lastFormat)) {
          format = lastFormat;
        } else {
          format = newFormat;
        }
      }
    }

    if (multilineWrapperTags && multilineWrapperTags.indexOf(type) !== -1) {
      value = createFromMultilineElement({
        element: node,
        range: range,
        multilineTag: multilineTag,
        multilineWrapperTags: multilineWrapperTags,
        removeNode: removeNode,
        unwrapNode: unwrapNode,
        filterString: filterString,
        removeAttribute: removeAttribute,
        currentWrapperTags: Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(currentWrapperTags).concat([format])
      });
      format = undefined;
    } else {
      value = createFromElement({
        element: node,
        range: range,
        multilineTag: multilineTag,
        multilineWrapperTags: multilineWrapperTags,
        removeNode: removeNode,
        unwrapNode: unwrapNode,
        filterString: filterString,
        removeAttribute: removeAttribute
      });
    }

    var text = value.text;
    var start = accumulator.text.length;
    accumulateSelection(accumulator, node, range, value); // Don't apply the element as formatting if it has no content.

    if (Object(_is_empty__WEBPACK_IMPORTED_MODULE_3__["isEmpty"])(value) && format && !format.attributes) {
      continue;
    }

    var formats = accumulator.formats;

    if (format && format.attributes && text.length === 0) {
      format.object = true;
      accumulator.text += _special_characters__WEBPACK_IMPORTED_MODULE_6__["OBJECT_REPLACEMENT_CHARACTER"];

      if (formats[start]) {
        formats[start].unshift(format);
      } else {
        formats[start] = [format];
      }
    } else {
      accumulator.text += text;
      accumulator.formats.length += text.length;
      var i = value.formats.length; // Optimise for speed.

      while (i--) {
        var formatIndex = start + i;

        if (format) {
          if (formats[formatIndex]) {
            formats[formatIndex].push(format);
          } else {
            formats[formatIndex] = [format];
          }
        }

        if (value.formats[i]) {
          if (formats[formatIndex]) {
            var _formats$formatIndex;

            (_formats$formatIndex = formats[formatIndex]).push.apply(_formats$formatIndex, Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(value.formats[i]));
          } else {
            formats[formatIndex] = value.formats[i];
          }
        }
      }
    }
  }

  return accumulator;
}
/**
 * Creates a rich text value from a DOM element and range that should be
 * multiline.
 *
 * @param {Object}    $1                      Named argements.
 * @param {?Element}  $1.element              Element to create value from.
 * @param {?Range}    $1.range                Range to create value from.
 * @param {?string}   $1.multilineTag         Multiline tag if the structure is
 *                                            multiline.
 * @param {?Array}    $1.multilineWrapperTags Tags where lines can be found if
 *                                            nesting is possible.
 * @param {?Function} $1.removeNode           Function to declare whether the
 *                                            given node should be removed.
 * @param {?Function} $1.unwrapNode           Function to declare whether the
 *                                            given node should be unwrapped.
 * @param {?Function} $1.filterString         Function to filter the given
 *                                            string.
 * @param {?Function} $1.removeAttribute      Wether to remove an attribute
 *                                            based on the name.
 * @param {boolean}   $1.currentWrapperTags   Whether to prepend a line
 *                                            separator.
 *
 * @return {Object} A rich text value.
 */


function createFromMultilineElement(_ref4) {
  var element = _ref4.element,
      range = _ref4.range,
      multilineTag = _ref4.multilineTag,
      multilineWrapperTags = _ref4.multilineWrapperTags,
      removeNode = _ref4.removeNode,
      unwrapNode = _ref4.unwrapNode,
      filterString = _ref4.filterString,
      removeAttribute = _ref4.removeAttribute,
      _ref4$currentWrapperT = _ref4.currentWrapperTags,
      currentWrapperTags = _ref4$currentWrapperT === void 0 ? [] : _ref4$currentWrapperT;
  var accumulator = createEmptyValue();

  if (!element || !element.hasChildNodes()) {
    return accumulator;
  }

  var length = element.children.length; // Optimise for speed.

  for (var index = 0; index < length; index++) {
    var node = element.children[index];

    if (node.nodeName.toLowerCase() !== multilineTag) {
      continue;
    }

    var value = createFromElement({
      element: node,
      range: range,
      multilineTag: multilineTag,
      multilineWrapperTags: multilineWrapperTags,
      currentWrapperTags: currentWrapperTags,
      removeNode: removeNode,
      unwrapNode: unwrapNode,
      filterString: filterString,
      removeAttribute: removeAttribute
    }); // If a line consists of one single line break (invisible), consider the
    // line empty, wether this is the browser's doing or not.

    if (value.text === '\n') {
      var start = value.start;
      var end = value.end;
      value = createEmptyValue();

      if (start !== undefined) {
        value.start = 0;
      }

      if (end !== undefined) {
        value.end = 0;
      }
    } // Multiline value text should be separated by a double line break.


    if (index !== 0 || currentWrapperTags.length > 0) {
      var formats = currentWrapperTags.length > 0 ? [currentWrapperTags] : [,];
      accumulator.formats = accumulator.formats.concat(formats);
      accumulator.text += _special_characters__WEBPACK_IMPORTED_MODULE_6__["LINE_SEPARATOR"];
    }

    accumulateSelection(accumulator, node, range, value);
    accumulator.formats = accumulator.formats.concat(value.formats);
    accumulator.text += value.text;
  }

  return accumulator;
}
/**
 * Gets the attributes of an element in object shape.
 *
 * @param {Object}    $1                 Named argements.
 * @param {Element}   $1.element         Element to get attributes from.
 * @param {?Function} $1.removeAttribute Wether to remove an attribute based on
 *                                       the name.
 *
 * @return {?Object} Attribute object or `undefined` if the element has no
 *                   attributes.
 */


function getAttributes(_ref5) {
  var element = _ref5.element,
      removeAttribute = _ref5.removeAttribute;

  if (!element.hasAttributes()) {
    return;
  }

  var length = element.attributes.length;
  var accumulator; // Optimise for speed.

  for (var i = 0; i < length; i++) {
    var _element$attributes$i = element.attributes[i],
        name = _element$attributes$i.name,
        value = _element$attributes$i.value;

    if (removeAttribute && removeAttribute(name)) {
      continue;
    }

    accumulator = accumulator || {};
    accumulator[name] = value;
  }

  return accumulator;
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/get-active-format.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/get-active-format.js ***!
  \*****************************************************************************/
/*! exports provided: getActiveFormat */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getActiveFormat", function() { return getActiveFormat; });
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
/**
 * External dependencies
 */

/**
 * Gets the format object by type at the start of the selection. This can be
 * used to get e.g. the URL of a link format at the current selection, but also
 * to check if a format is active at the selection. Returns undefined if there
 * is no format at the selection.
 *
 * @param {Object} value      Value to inspect.
 * @param {string} formatType Format type to look for.
 *
 * @return {?Object} Active format object of the specified type, or undefined.
 */

function getActiveFormat(_ref, formatType) {
  var formats = _ref.formats,
      start = _ref.start;

  if (start === undefined) {
    return;
  }

  return Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(formats[start], {
    type: formatType
  });
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/get-format-type.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/get-format-type.js ***!
  \***************************************************************************/
/*! exports provided: getFormatType */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getFormatType", function() { return getFormatType; });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/**
 * WordPress dependencies
 */

/**
 * Returns a registered format type.
 *
 * @param {string} name Format name.
 *
 * @return {?Object} Format type.
 */

function getFormatType(name) {
  return Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__["select"])('core/rich-text').getFormatType(name);
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/get-selection-end.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/get-selection-end.js ***!
  \*****************************************************************************/
/*! exports provided: getSelectionEnd */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getSelectionEnd", function() { return getSelectionEnd; });
/**
 * Gets the end index of the current selection, or returns `undefined` if no
 * selection exists. The selection ends right before the character at this
 * index.
 *
 * @param {Object} value Value to get the selection from.
 *
 * @return {?number} Index where the selection ends.
 */
function getSelectionEnd(_ref) {
  var end = _ref.end;
  return end;
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/get-selection-start.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/get-selection-start.js ***!
  \*******************************************************************************/
/*! exports provided: getSelectionStart */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getSelectionStart", function() { return getSelectionStart; });
/**
 * Gets the start index of the current selection, or returns `undefined` if no
 * selection exists. The selection starts right before the character at this
 * index.
 *
 * @param {Object} value Value to get the selection from.
 *
 * @return {?number} Index where the selection starts.
 */
function getSelectionStart(_ref) {
  var start = _ref.start;
  return start;
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/get-text-content.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/get-text-content.js ***!
  \****************************************************************************/
/*! exports provided: getTextContent */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getTextContent", function() { return getTextContent; });
/**
 * Get the textual content of a Rich Text value. This is similar to
 * `Element.textContent`.
 *
 * @param {Object} value Value to use.
 *
 * @return {string} The text content.
 */
function getTextContent(_ref) {
  var text = _ref.text;
  return text;
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/index.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/index.js ***!
  \*****************************************************************/
/*! exports provided: applyFormat, charAt, concat, create, getActiveFormat, getSelectionEnd, getSelectionStart, getTextContent, isCollapsed, isEmpty, isEmptyLine, join, registerFormatType, removeFormat, remove, replace, insert, insertLineSeparator, insertObject, slice, split, apply, unstableToDom, toHTMLString, toggleFormat, LINE_SEPARATOR, unregisterFormatType */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./store */ "./node_modules/@wordpress/rich-text/build-module/store/index.js");
/* harmony import */ var _apply_format__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./apply-format */ "./node_modules/@wordpress/rich-text/build-module/apply-format.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "applyFormat", function() { return _apply_format__WEBPACK_IMPORTED_MODULE_1__["applyFormat"]; });

/* harmony import */ var _char_at__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./char-at */ "./node_modules/@wordpress/rich-text/build-module/char-at.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "charAt", function() { return _char_at__WEBPACK_IMPORTED_MODULE_2__["charAt"]; });

/* harmony import */ var _concat__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./concat */ "./node_modules/@wordpress/rich-text/build-module/concat.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "concat", function() { return _concat__WEBPACK_IMPORTED_MODULE_3__["concat"]; });

/* harmony import */ var _create__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./create */ "./node_modules/@wordpress/rich-text/build-module/create.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "create", function() { return _create__WEBPACK_IMPORTED_MODULE_4__["create"]; });

/* harmony import */ var _get_active_format__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./get-active-format */ "./node_modules/@wordpress/rich-text/build-module/get-active-format.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getActiveFormat", function() { return _get_active_format__WEBPACK_IMPORTED_MODULE_5__["getActiveFormat"]; });

/* harmony import */ var _get_selection_end__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./get-selection-end */ "./node_modules/@wordpress/rich-text/build-module/get-selection-end.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getSelectionEnd", function() { return _get_selection_end__WEBPACK_IMPORTED_MODULE_6__["getSelectionEnd"]; });

/* harmony import */ var _get_selection_start__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./get-selection-start */ "./node_modules/@wordpress/rich-text/build-module/get-selection-start.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getSelectionStart", function() { return _get_selection_start__WEBPACK_IMPORTED_MODULE_7__["getSelectionStart"]; });

/* harmony import */ var _get_text_content__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./get-text-content */ "./node_modules/@wordpress/rich-text/build-module/get-text-content.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getTextContent", function() { return _get_text_content__WEBPACK_IMPORTED_MODULE_8__["getTextContent"]; });

/* harmony import */ var _is_collapsed__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./is-collapsed */ "./node_modules/@wordpress/rich-text/build-module/is-collapsed.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isCollapsed", function() { return _is_collapsed__WEBPACK_IMPORTED_MODULE_9__["isCollapsed"]; });

/* harmony import */ var _is_empty__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./is-empty */ "./node_modules/@wordpress/rich-text/build-module/is-empty.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEmpty", function() { return _is_empty__WEBPACK_IMPORTED_MODULE_10__["isEmpty"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEmptyLine", function() { return _is_empty__WEBPACK_IMPORTED_MODULE_10__["isEmptyLine"]; });

/* harmony import */ var _join__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./join */ "./node_modules/@wordpress/rich-text/build-module/join.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "join", function() { return _join__WEBPACK_IMPORTED_MODULE_11__["join"]; });

/* harmony import */ var _register_format_type__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./register-format-type */ "./node_modules/@wordpress/rich-text/build-module/register-format-type.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "registerFormatType", function() { return _register_format_type__WEBPACK_IMPORTED_MODULE_12__["registerFormatType"]; });

/* harmony import */ var _remove_format__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./remove-format */ "./node_modules/@wordpress/rich-text/build-module/remove-format.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "removeFormat", function() { return _remove_format__WEBPACK_IMPORTED_MODULE_13__["removeFormat"]; });

/* harmony import */ var _remove__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./remove */ "./node_modules/@wordpress/rich-text/build-module/remove.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "remove", function() { return _remove__WEBPACK_IMPORTED_MODULE_14__["remove"]; });

/* harmony import */ var _replace__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./replace */ "./node_modules/@wordpress/rich-text/build-module/replace.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "replace", function() { return _replace__WEBPACK_IMPORTED_MODULE_15__["replace"]; });

/* harmony import */ var _insert__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./insert */ "./node_modules/@wordpress/rich-text/build-module/insert.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "insert", function() { return _insert__WEBPACK_IMPORTED_MODULE_16__["insert"]; });

/* harmony import */ var _insert_line_separator__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./insert-line-separator */ "./node_modules/@wordpress/rich-text/build-module/insert-line-separator.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "insertLineSeparator", function() { return _insert_line_separator__WEBPACK_IMPORTED_MODULE_17__["insertLineSeparator"]; });

/* harmony import */ var _insert_object__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./insert-object */ "./node_modules/@wordpress/rich-text/build-module/insert-object.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "insertObject", function() { return _insert_object__WEBPACK_IMPORTED_MODULE_18__["insertObject"]; });

/* harmony import */ var _slice__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./slice */ "./node_modules/@wordpress/rich-text/build-module/slice.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "slice", function() { return _slice__WEBPACK_IMPORTED_MODULE_19__["slice"]; });

/* harmony import */ var _split__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./split */ "./node_modules/@wordpress/rich-text/build-module/split.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "split", function() { return _split__WEBPACK_IMPORTED_MODULE_20__["split"]; });

/* harmony import */ var _to_dom__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./to-dom */ "./node_modules/@wordpress/rich-text/build-module/to-dom.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "apply", function() { return _to_dom__WEBPACK_IMPORTED_MODULE_21__["apply"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unstableToDom", function() { return _to_dom__WEBPACK_IMPORTED_MODULE_21__["toDom"]; });

/* harmony import */ var _to_html_string__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./to-html-string */ "./node_modules/@wordpress/rich-text/build-module/to-html-string.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "toHTMLString", function() { return _to_html_string__WEBPACK_IMPORTED_MODULE_22__["toHTMLString"]; });

/* harmony import */ var _toggle_format__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./toggle-format */ "./node_modules/@wordpress/rich-text/build-module/toggle-format.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "toggleFormat", function() { return _toggle_format__WEBPACK_IMPORTED_MODULE_23__["toggleFormat"]; });

/* harmony import */ var _special_characters__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./special-characters */ "./node_modules/@wordpress/rich-text/build-module/special-characters.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "LINE_SEPARATOR", function() { return _special_characters__WEBPACK_IMPORTED_MODULE_24__["LINE_SEPARATOR"]; });

/* harmony import */ var _unregister_format_type__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./unregister-format-type */ "./node_modules/@wordpress/rich-text/build-module/unregister-format-type.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unregisterFormatType", function() { return _unregister_format_type__WEBPACK_IMPORTED_MODULE_25__["unregisterFormatType"]; });





























/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/insert-line-separator.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/insert-line-separator.js ***!
  \*********************************************************************************/
/*! exports provided: insertLineSeparator */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "insertLineSeparator", function() { return insertLineSeparator; });
/* harmony import */ var _get_text_content__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-text-content */ "./node_modules/@wordpress/rich-text/build-module/get-text-content.js");
/* harmony import */ var _insert__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./insert */ "./node_modules/@wordpress/rich-text/build-module/insert.js");
/* harmony import */ var _special_characters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./special-characters */ "./node_modules/@wordpress/rich-text/build-module/special-characters.js");
/**
 * Internal dependencies
 */



/**
 * Insert a line break character into a Rich Text value at the given
 * `startIndex`. Any content between `startIndex` and `endIndex` will be
 * removed. Indices are retrieved from the selection if none are provided.
 *
 * @param {Object} value         Value to modify.
 * @param {number} startIndex    Start index.
 * @param {number} endIndex      End index.
 *
 * @return {Object} A new value with the value inserted.
 */

function insertLineSeparator(value) {
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : value.start;
  var endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.end;
  var beforeText = Object(_get_text_content__WEBPACK_IMPORTED_MODULE_0__["getTextContent"])(value).slice(0, startIndex);
  var previousLineSeparatorIndex = beforeText.lastIndexOf(_special_characters__WEBPACK_IMPORTED_MODULE_2__["LINE_SEPARATOR"]);
  var formats = [,];

  if (previousLineSeparatorIndex !== -1) {
    formats = [value.formats[previousLineSeparatorIndex]];
  }

  var valueToInsert = {
    formats: formats,
    text: _special_characters__WEBPACK_IMPORTED_MODULE_2__["LINE_SEPARATOR"]
  };
  return Object(_insert__WEBPACK_IMPORTED_MODULE_1__["insert"])(value, valueToInsert, startIndex, endIndex);
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/insert-object.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/insert-object.js ***!
  \*************************************************************************/
/*! exports provided: insertObject */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "insertObject", function() { return insertObject; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _insert__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./insert */ "./node_modules/@wordpress/rich-text/build-module/insert.js");


/**
 * Internal dependencies
 */

var OBJECT_REPLACEMENT_CHARACTER = "\uFFFC";
/**
 * Insert a format as an object into a Rich Text value at the given
 * `startIndex`. Any content between `startIndex` and `endIndex` will be
 * removed. Indices are retrieved from the selection if none are provided.
 *
 * @param {Object} value          Value to modify.
 * @param {Object} formatToInsert Format to insert as object.
 * @param {number} startIndex     Start index.
 * @param {number} endIndex       End index.
 *
 * @return {Object} A new value with the object inserted.
 */

function insertObject(value, formatToInsert, startIndex, endIndex) {
  var valueToInsert = {
    text: OBJECT_REPLACEMENT_CHARACTER,
    formats: [[Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, formatToInsert, {
      object: true
    })]]
  };
  return Object(_insert__WEBPACK_IMPORTED_MODULE_1__["insert"])(value, valueToInsert, startIndex, endIndex);
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/insert.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/insert.js ***!
  \******************************************************************/
/*! exports provided: insert */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "insert", function() { return insert; });
/* harmony import */ var _create__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./create */ "./node_modules/@wordpress/rich-text/build-module/create.js");
/* harmony import */ var _normalise_formats__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./normalise-formats */ "./node_modules/@wordpress/rich-text/build-module/normalise-formats.js");
/**
 * Internal dependencies
 */


/**
 * Insert a Rich Text value, an HTML string, or a plain text string, into a
 * Rich Text value at the given `startIndex`. Any content between `startIndex`
 * and `endIndex` will be removed. Indices are retrieved from the selection if
 * none are provided.
 *
 * @param {Object} value         Value to modify.
 * @param {string} valueToInsert Value to insert.
 * @param {number} startIndex    Start index.
 * @param {number} endIndex      End index.
 *
 * @return {Object} A new value with the value inserted.
 */

function insert(_ref, valueToInsert) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end;
  var startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : start;
  var endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : end;

  if (typeof valueToInsert === 'string') {
    valueToInsert = Object(_create__WEBPACK_IMPORTED_MODULE_0__["create"])({
      text: valueToInsert
    });
  }

  var index = startIndex + valueToInsert.text.length;
  return Object(_normalise_formats__WEBPACK_IMPORTED_MODULE_1__["normaliseFormats"])({
    formats: formats.slice(0, startIndex).concat(valueToInsert.formats, formats.slice(endIndex)),
    text: text.slice(0, startIndex) + valueToInsert.text + text.slice(endIndex),
    start: index,
    end: index
  });
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/is-collapsed.js":
/*!************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/is-collapsed.js ***!
  \************************************************************************/
/*! exports provided: isCollapsed */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isCollapsed", function() { return isCollapsed; });
/**
 * Check if the selection of a Rich Text value is collapsed or not. Collapsed
 * means that no characters are selected, but there is a caret present. If there
 * is no selection, `undefined` will be returned. This is similar to
 * `window.getSelection().isCollapsed()`.
 *
 * @param {Object} value The rich text value to check.
 *
 * @return {?boolean} True if the selection is collapsed, false if not,
 *                    undefined if there is no selection.
 */
function isCollapsed(_ref) {
  var start = _ref.start,
      end = _ref.end;

  if (start === undefined || end === undefined) {
    return;
  }

  return start === end;
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/is-empty.js":
/*!********************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/is-empty.js ***!
  \********************************************************************/
/*! exports provided: isEmpty, isEmptyLine */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEmpty", function() { return isEmpty; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEmptyLine", function() { return isEmptyLine; });
/* harmony import */ var _special_characters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./special-characters */ "./node_modules/@wordpress/rich-text/build-module/special-characters.js");

/**
 * Check if a Rich Text value is Empty, meaning it contains no text or any
 * objects (such as images).
 *
 * @param {Object} value Value to use.
 *
 * @return {boolean} True if the value is empty, false if not.
 */

function isEmpty(_ref) {
  var text = _ref.text;
  return text.length === 0;
}
/**
 * Check if the current collapsed selection is on an empty line in case of a
 * multiline value.
 *
 * @param  {Object} value Value te check.
 *
 * @return {boolean} True if the line is empty, false if not.
 */

function isEmptyLine(_ref2) {
  var text = _ref2.text,
      start = _ref2.start,
      end = _ref2.end;

  if (start !== end) {
    return false;
  }

  if (text.length === 0) {
    return true;
  }

  if (start === 0 && text.slice(0, 1) === _special_characters__WEBPACK_IMPORTED_MODULE_0__["LINE_SEPARATOR"]) {
    return true;
  }

  if (start === text.length && text.slice(-1) === _special_characters__WEBPACK_IMPORTED_MODULE_0__["LINE_SEPARATOR"]) {
    return true;
  }

  return text.slice(start - 1, end + 1) === "".concat(_special_characters__WEBPACK_IMPORTED_MODULE_0__["LINE_SEPARATOR"]).concat(_special_characters__WEBPACK_IMPORTED_MODULE_0__["LINE_SEPARATOR"]);
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/is-format-equal.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/is-format-equal.js ***!
  \***************************************************************************/
/*! exports provided: isFormatEqual */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFormatEqual", function() { return isFormatEqual; });
/**
 * Optimised equality check for format objects.
 *
 * @param {?Object} format1 Format to compare.
 * @param {?Object} format2 Format to compare.
 *
 * @return {boolean} True if formats are equal, false if not.
 */
function isFormatEqual(format1, format2) {
  // Both not defined.
  if (format1 === format2) {
    return true;
  } // Either not defined.


  if (!format1 || !format2) {
    return false;
  }

  if (format1.type !== format2.type) {
    return false;
  }

  var attributes1 = format1.attributes;
  var attributes2 = format2.attributes; // Both not defined.

  if (attributes1 === attributes2) {
    return true;
  } // Either not defined.


  if (!attributes1 || !attributes2) {
    return false;
  }

  var keys1 = Object.keys(attributes1);
  var keys2 = Object.keys(attributes2);

  if (keys1.length !== keys2.length) {
    return false;
  }

  var length = keys1.length; // Optimise for speed.

  for (var i = 0; i < length; i++) {
    var name = keys1[i];

    if (attributes1[name] !== attributes2[name]) {
      return false;
    }
  }

  return true;
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/join.js":
/*!****************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/join.js ***!
  \****************************************************************/
/*! exports provided: join */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "join", function() { return join; });
/* harmony import */ var _create__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./create */ "./node_modules/@wordpress/rich-text/build-module/create.js");
/* harmony import */ var _normalise_formats__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./normalise-formats */ "./node_modules/@wordpress/rich-text/build-module/normalise-formats.js");
/**
 * Internal dependencies
 */


/**
 * Combine an array of Rich Text values into one, optionally separated by
 * `separator`, which can be a Rich Text value, HTML string, or plain text
 * string. This is similar to `Array.prototype.join`.
 *
 * @param {Array}         values    An array of values to join.
 * @param {string|Object} separator Separator string or value.
 *
 * @return {Object} A new combined value.
 */

function join(values) {
  var separator = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (typeof separator === 'string') {
    separator = Object(_create__WEBPACK_IMPORTED_MODULE_0__["create"])({
      text: separator
    });
  }

  return Object(_normalise_formats__WEBPACK_IMPORTED_MODULE_1__["normaliseFormats"])(values.reduce(function (accumlator, _ref) {
    var formats = _ref.formats,
        text = _ref.text;
    return {
      text: accumlator.text + separator.text + text,
      formats: accumlator.formats.concat(separator.formats, formats)
    };
  }));
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/normalise-formats.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/normalise-formats.js ***!
  \*****************************************************************************/
/*! exports provided: normaliseFormats */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "normaliseFormats", function() { return normaliseFormats; });
/* harmony import */ var _is_format_equal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-format-equal */ "./node_modules/@wordpress/rich-text/build-module/is-format-equal.js");
/**
 * Internal dependencies
 */

/**
 * Normalises formats: ensures subsequent equal formats have the same reference.
 *
 * @param  {Object} value Value to normalise formats of.
 *
 * @return {Object} New value with normalised formats.
 */

function normaliseFormats(_ref) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end;
  var newFormats = formats.slice(0);
  newFormats.forEach(function (formatsAtIndex, index) {
    var lastFormatsAtIndex = newFormats[index - 1];

    if (lastFormatsAtIndex) {
      var newFormatsAtIndex = formatsAtIndex.slice(0);
      newFormatsAtIndex.forEach(function (format, formatIndex) {
        var lastFormat = lastFormatsAtIndex[formatIndex];

        if (Object(_is_format_equal__WEBPACK_IMPORTED_MODULE_0__["isFormatEqual"])(format, lastFormat)) {
          newFormatsAtIndex[formatIndex] = lastFormat;
        }
      });
      newFormats[index] = newFormatsAtIndex;
    }
  });
  return {
    formats: newFormats,
    text: text,
    start: start,
    end: end
  };
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/register-format-type.js":
/*!********************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/register-format-type.js ***!
  \********************************************************************************/
/*! exports provided: registerFormatType */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "registerFormatType", function() { return registerFormatType; });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__);






/**
 * WordPress dependencies
 */


/**
 * Registers a new format provided a unique name and an object defining its
 * behavior.
 *
 * @param {string} name     Format name.
 * @param {Object} settings Format settings.
 *
 * @return {?WPFormat} The format, if it has been successfully registered;
 *                     otherwise `undefined`.
 */

function registerFormatType(name, settings) {
  settings = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_3__["default"])({
    name: name
  }, settings);

  if (typeof settings.name !== 'string') {
    window.console.error('Format names must be strings.');
    return;
  }

  if (!/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/.test(settings.name)) {
    window.console.error('Format names must contain a namespace prefix, include only lowercase alphanumeric characters or dashes, and start with a letter. Example: my-plugin/my-custom-format');
    return;
  }

  if (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["select"])('core/rich-text').getFormatType(settings.name)) {
    window.console.error('Format "' + settings.name + '" is already registered.');
    return;
  }

  if (typeof settings.tagName !== 'string' || settings.tagName === '') {
    window.console.error('Format tag names must be a string.');
    return;
  }

  if ((typeof settings.className !== 'string' || settings.className === '') && settings.className !== null) {
    window.console.error('Format class names must be a string, or null to handle bare elements.');
    return;
  }

  if (!/^[_a-zA-Z]+[a-zA-Z0-9-]*$/.test(settings.className)) {
    window.console.error('A class name must begin with a letter, followed by any number of hyphens, letters, or numbers.');
    return;
  }

  if (settings.className === null) {
    var formatTypeForBareElement = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["select"])('core/rich-text').getFormatTypeForBareElement(settings.tagName);

    if (formatTypeForBareElement) {
      window.console.error("Format \"".concat(formatTypeForBareElement.name, "\" is already registered to handle bare tag name \"").concat(settings.tagName, "\"."));
      return;
    }
  } else {
    var formatTypeForClassName = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["select"])('core/rich-text').getFormatTypeForClassName(settings.className);

    if (formatTypeForClassName) {
      window.console.error("Format \"".concat(formatTypeForClassName.name, "\" is already registered to handle class name \"").concat(settings.className, "\"."));
      return;
    }
  }

  if (!('title' in settings) || settings.title === '') {
    window.console.error('The format "' + settings.name + '" must have a title.');
    return;
  }

  if ('keywords' in settings && settings.keywords.length > 3) {
    window.console.error('The format "' + settings.name + '" can have a maximum of 3 keywords.');
    return;
  }

  if (typeof settings.title !== 'string') {
    window.console.error('Format titles must be strings.');
    return;
  }

  Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["dispatch"])('core/rich-text').addFormatTypes(settings);

  if (settings.__experimentalCreatePrepareEditableTree && settings.__experimentalGetPropsForEditableTreePreparation) {
    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__["addFilter"])('experimentalRichText', name, function (OriginalComponent) {
      return Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withSelect"])(function (sel, _ref) {
        var clientId = _ref.clientId,
            identifier = _ref.identifier;
        return Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_2__["default"])({}, "format_".concat(name), settings.__experimentalGetPropsForEditableTreePreparation(sel, {
          richTextIdentifier: identifier,
          blockClientId: clientId
        }));
      })(function (props) {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(OriginalComponent, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, props, {
          prepareEditableTree: Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__["default"])(props.prepareEditableTree || []).concat([settings.__experimentalCreatePrepareEditableTree(props["format_".concat(name)], {
            richTextIdentifier: props.identifier,
            blockClientId: props.clientId
          })])
        }));
      });
    });
  }

  return settings;
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/remove-format.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/remove-format.js ***!
  \*************************************************************************/
/*! exports provided: removeFormat */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "removeFormat", function() { return removeFormat; });
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _normalise_formats__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./normalise-formats */ "./node_modules/@wordpress/rich-text/build-module/normalise-formats.js");
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Remove any format object from a Rich Text value by type from the given
 * `startIndex` to the given `endIndex`. Indices are retrieved from the
 * selection if none are provided.
 *
 * @param {Object} value      Value to modify.
 * @param {string} formatType Format type to remove.
 * @param {number} startIndex Start index.
 * @param {number} endIndex   End index.
 *
 * @return {Object} A new value with the format applied.
 */

function removeFormat(_ref, formatType) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end;
  var startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : start;
  var endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : end;
  var newFormats = formats.slice(0); // If the selection is collapsed, expand start and end to the edges of the
  // format.

  if (startIndex === endIndex) {
    var format = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(newFormats[startIndex], {
      type: formatType
    });

    while (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(newFormats[startIndex], format)) {
      filterFormats(newFormats, startIndex, formatType);
      startIndex--;
    }

    endIndex++;

    while (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(newFormats[endIndex], format)) {
      filterFormats(newFormats, endIndex, formatType);
      endIndex++;
    }
  } else {
    for (var i = startIndex; i < endIndex; i++) {
      if (newFormats[i]) {
        filterFormats(newFormats, i, formatType);
      }
    }
  }

  return Object(_normalise_formats__WEBPACK_IMPORTED_MODULE_1__["normaliseFormats"])({
    formats: newFormats,
    text: text,
    start: start,
    end: end
  });
}

function filterFormats(formats, index, formatType) {
  var newFormats = formats[index].filter(function (_ref2) {
    var type = _ref2.type;
    return type !== formatType;
  });

  if (newFormats.length) {
    formats[index] = newFormats;
  } else {
    delete formats[index];
  }
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/remove.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/remove.js ***!
  \******************************************************************/
/*! exports provided: remove */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "remove", function() { return remove; });
/* harmony import */ var _insert__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./insert */ "./node_modules/@wordpress/rich-text/build-module/insert.js");
/* harmony import */ var _create__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./create */ "./node_modules/@wordpress/rich-text/build-module/create.js");
/**
 * Internal dependencies
 */


/**
 * Remove content from a Rich Text value between the given `startIndex` and
 * `endIndex`. Indices are retrieved from the selection if none are provided.
 *
 * @param {Object} value      Value to modify.
 * @param {number} startIndex Start index.
 * @param {number} endIndex   End index.
 *
 * @return {Object} A new value with the content removed.
 */

function remove(value, startIndex, endIndex) {
  return Object(_insert__WEBPACK_IMPORTED_MODULE_0__["insert"])(value, Object(_create__WEBPACK_IMPORTED_MODULE_1__["create"])(), startIndex, endIndex);
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/replace.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/replace.js ***!
  \*******************************************************************/
/*! exports provided: replace */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "replace", function() { return replace; });
/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/typeof */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _normalise_formats__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./normalise-formats */ "./node_modules/@wordpress/rich-text/build-module/normalise-formats.js");


/**
 * Internal dependencies
 */

/**
 * Search a Rich Text value and replace the match(es) with `replacement`. This
 * is similar to `String.prototype.replace`.
 *
 * @param {Object}         value        The value to modify.
 * @param {RegExp|string}  pattern      A RegExp object or literal. Can also be
 *                                      a string. It is treated as a verbatim
 *                                      string and is not interpreted as a
 *                                      regular expression. Only the first
 *                                      occurrence will be replaced.
 * @param {Function|string} replacement The match or matches are replaced with
 *                                      the specified or the value returned by
 *                                      the specified function.
 *
 * @return {Object} A new value with replacements applied.
 */

function replace(_ref, pattern, replacement) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end;
  text = text.replace(pattern, function (match) {
    for (var _len = arguments.length, rest = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      rest[_key - 1] = arguments[_key];
    }

    var offset = rest[rest.length - 2];
    var newText = replacement;
    var newFormats;

    if (typeof newText === 'function') {
      newText = replacement.apply(void 0, [match].concat(rest));
    }

    if (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__["default"])(newText) === 'object') {
      newFormats = newText.formats;
      newText = newText.text;
    } else {
      newFormats = Array(newText.length);

      if (formats[offset]) {
        newFormats = newFormats.fill(formats[offset]);
      }
    }

    formats = formats.slice(0, offset).concat(newFormats, formats.slice(offset + match.length));

    if (start) {
      start = end = offset + newText.length;
    }

    return newText;
  });
  return Object(_normalise_formats__WEBPACK_IMPORTED_MODULE_1__["normaliseFormats"])({
    formats: formats,
    text: text,
    start: start,
    end: end
  });
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/slice.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/slice.js ***!
  \*****************************************************************/
/*! exports provided: slice */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "slice", function() { return slice; });
/**
 * Slice a Rich Text value from `startIndex` to `endIndex`. Indices are
 * retrieved from the selection if none are provided. This is similar to
 * `String.prototype.slice`.
 *
 * @param {Object} value       Value to modify.
 * @param {number} startIndex  Start index.
 * @param {number} endIndex    End index.
 *
 * @return {Object} A new extracted value.
 */
function slice(_ref) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end;
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : start;
  var endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : end;

  if (startIndex === undefined || endIndex === undefined) {
    return {
      formats: formats,
      text: text
    };
  }

  return {
    formats: formats.slice(startIndex, endIndex),
    text: text.slice(startIndex, endIndex)
  };
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/special-characters.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/special-characters.js ***!
  \******************************************************************************/
/*! exports provided: LINE_SEPARATOR, OBJECT_REPLACEMENT_CHARACTER, ZERO_WIDTH_NO_BREAK_SPACE */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "LINE_SEPARATOR", function() { return LINE_SEPARATOR; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "OBJECT_REPLACEMENT_CHARACTER", function() { return OBJECT_REPLACEMENT_CHARACTER; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ZERO_WIDTH_NO_BREAK_SPACE", function() { return ZERO_WIDTH_NO_BREAK_SPACE; });
var LINE_SEPARATOR = "\u2028";
var OBJECT_REPLACEMENT_CHARACTER = "\uFFFC";
var ZERO_WIDTH_NO_BREAK_SPACE = "\uFEFF";


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/split.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/split.js ***!
  \*****************************************************************/
/*! exports provided: split */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "split", function() { return split; });
/* harmony import */ var _replace__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./replace */ "./node_modules/@wordpress/rich-text/build-module/replace.js");
/**
 * Internal dependencies
 */

/**
 * Split a Rich Text value in two at the given `startIndex` and `endIndex`, or
 * split at the given separator. This is similar to `String.prototype.split`.
 * Indices are retrieved from the selection if none are provided.
 *
 * @param {Object}        value   Value to modify.
 * @param {number|string} string  Start index, or string at which to split.
 * @param {number}        end     End index.
 *
 * @return {Array} An array of new values.
 */

function split(_ref, string) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end;

  if (typeof string !== 'string') {
    return splitAtSelection.apply(void 0, arguments);
  }

  var nextStart = 0;
  return text.split(string).map(function (substring) {
    var startIndex = nextStart;
    var value = {
      formats: formats.slice(startIndex, startIndex + substring.length),
      text: substring
    };
    nextStart += string.length + substring.length;

    if (start !== undefined && end !== undefined) {
      if (start >= startIndex && start < nextStart) {
        value.start = start - startIndex;
      } else if (start < startIndex && end > startIndex) {
        value.start = 0;
      }

      if (end >= startIndex && end < nextStart) {
        value.end = end - startIndex;
      } else if (start < nextStart && end > nextStart) {
        value.end = substring.length;
      }
    }

    return value;
  });
}

function splitAtSelection(_ref2) {
  var formats = _ref2.formats,
      text = _ref2.text,
      start = _ref2.start,
      end = _ref2.end;
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : start;
  var endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : end;
  var before = {
    formats: formats.slice(0, startIndex),
    text: text.slice(0, startIndex)
  };
  var after = {
    formats: formats.slice(endIndex),
    text: text.slice(endIndex),
    start: 0,
    end: 0
  };
  return [// Ensure newlines are trimmed.
  Object(_replace__WEBPACK_IMPORTED_MODULE_0__["replace"])(before, /\u2028+$/, ''), Object(_replace__WEBPACK_IMPORTED_MODULE_0__["replace"])(after, /^\u2028+/, '')];
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/store/actions.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/store/actions.js ***!
  \*************************************************************************/
/*! exports provided: addFormatTypes, removeFormatTypes */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "addFormatTypes", function() { return addFormatTypes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "removeFormatTypes", function() { return removeFormatTypes; });
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
/**
 * External dependencies
 */

/**
 * Returns an action object used in signalling that format types have been
 * added.
 *
 * @param {Array|Object} formatTypes Format types received.
 *
 * @return {Object} Action object.
 */

function addFormatTypes(formatTypes) {
  return {
    type: 'ADD_FORMAT_TYPES',
    formatTypes: Object(lodash__WEBPACK_IMPORTED_MODULE_0__["castArray"])(formatTypes)
  };
}
/**
 * Returns an action object used to remove a registered format type.
 *
 * @param {string|Array} names Format name.
 *
 * @return {Object} Action object.
 */

function removeFormatTypes(names) {
  return {
    type: 'REMOVE_FORMAT_TYPES',
    names: Object(lodash__WEBPACK_IMPORTED_MODULE_0__["castArray"])(names)
  };
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/store/index.js":
/*!***********************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/store/index.js ***!
  \***********************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _reducer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./reducer */ "./node_modules/@wordpress/rich-text/build-module/store/reducer.js");
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./selectors */ "./node_modules/@wordpress/rich-text/build-module/store/selectors.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./actions */ "./node_modules/@wordpress/rich-text/build-module/store/actions.js");
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__["registerStore"])('core/rich-text', {
  reducer: _reducer__WEBPACK_IMPORTED_MODULE_1__["default"],
  selectors: _selectors__WEBPACK_IMPORTED_MODULE_2__,
  actions: _actions__WEBPACK_IMPORTED_MODULE_3__
});


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/store/reducer.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/store/reducer.js ***!
  \*************************************************************************/
/*! exports provided: formatTypes, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "formatTypes", function() { return formatTypes; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Reducer managing the format types
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function formatTypes() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ADD_FORMAT_TYPES':
      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, state, Object(lodash__WEBPACK_IMPORTED_MODULE_1__["keyBy"])(action.formatTypes, 'name'));

    case 'REMOVE_FORMAT_TYPES':
      return Object(lodash__WEBPACK_IMPORTED_MODULE_1__["omit"])(state, action.names);
  }

  return state;
}
/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["combineReducers"])({
  formatTypes: formatTypes
}));


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/store/selectors.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/store/selectors.js ***!
  \***************************************************************************/
/*! exports provided: getFormatTypes, getFormatType, getFormatTypeForBareElement, getFormatTypeForClassName */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getFormatTypes", function() { return getFormatTypes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getFormatType", function() { return getFormatType; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getFormatTypeForBareElement", function() { return getFormatTypeForBareElement; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getFormatTypeForClassName", function() { return getFormatTypeForClassName; });
/* harmony import */ var rememo__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! rememo */ "./node_modules/rememo/es/rememo.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/**
 * External dependencies
 */


/**
 * Returns all the available format types.
 *
 * @param {Object} state Data state.
 *
 * @return {Array} Format types.
 */

var getFormatTypes = Object(rememo__WEBPACK_IMPORTED_MODULE_0__["default"])(function (state) {
  return Object.values(state.formatTypes);
}, function (state) {
  return [state.formatTypes];
});
/**
 * Returns a format type by name.
 *
 * @param {Object} state Data state.
 * @param {string} name Format type name.
 *
 * @return {Object?} Format type.
 */

function getFormatType(state, name) {
  return state.formatTypes[name];
}
/**
 * Gets the format type, if any, that can handle a bare element (without a
 * data-format-type attribute), given the tag name of this element.
 *
 * @param {Object} state              Data state.
 * @param {string} bareElementTagName The tag name of the element to find a
 *                                    format type for.
 * @return {?Object} Format type.
 */

function getFormatTypeForBareElement(state, bareElementTagName) {
  return Object(lodash__WEBPACK_IMPORTED_MODULE_1__["find"])(getFormatTypes(state), function (_ref) {
    var tagName = _ref.tagName;
    return bareElementTagName === tagName;
  });
}
/**
 * Gets the format type, if any, that can handle an element, given its classes.
 *
 * @param {Object} state            Data state.
 * @param {string} elementClassName The classes of the element to find a format
 *                                  type for.
 * @return {?Object} Format type.
 */

function getFormatTypeForClassName(state, elementClassName) {
  return Object(lodash__WEBPACK_IMPORTED_MODULE_1__["find"])(getFormatTypes(state), function (_ref2) {
    var className = _ref2.className;

    if (className === null) {
      return false;
    }

    return " ".concat(elementClassName, " ").indexOf(" ".concat(className, " ")) >= 0;
  });
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/to-dom.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/to-dom.js ***!
  \******************************************************************/
/*! exports provided: toDom, apply, applyValue, applySelection */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toDom", function() { return toDom; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "apply", function() { return apply; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "applyValue", function() { return applyValue; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "applySelection", function() { return applySelection; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _to_tree__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./to-tree */ "./node_modules/@wordpress/rich-text/build-module/to-tree.js");



/**
 * Internal dependencies
 */

/**
 * Browser dependencies
 */

var _window$Node = window.Node,
    TEXT_NODE = _window$Node.TEXT_NODE,
    ELEMENT_NODE = _window$Node.ELEMENT_NODE;
/**
 * Creates a path as an array of indices from the given root node to the given
 * node.
 *
 * @param {Node}        node     Node to find the path of.
 * @param {HTMLElement} rootNode Root node to find the path from.
 * @param {Array}       path     Initial path to build on.
 *
 * @return {Array} The path from the root node to the node.
 */

function createPathToNode(node, rootNode, path) {
  var parentNode = node.parentNode;
  var i = 0;

  while (node = node.previousSibling) {
    i++;
  }

  path = [i].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__["default"])(path));

  if (parentNode !== rootNode) {
    path = createPathToNode(parentNode, rootNode, path);
  }

  return path;
}
/**
 * Gets a node given a path (array of indices) from the given node.
 *
 * @param {HTMLElement} node Root node to find the wanted node in.
 * @param {Array}       path Path (indices) to the wanted node.
 *
 * @return {Object} Object with the found node and the remaining offset (if any).
 */


function getNodeByPath(node, path) {
  path = Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__["default"])(path);

  while (node && path.length > 1) {
    node = node.childNodes[path.shift()];
  }

  return {
    node: node,
    offset: path[0]
  };
}

function createEmpty() {
  var _document$implementat = document.implementation.createHTMLDocument(''),
      body = _document$implementat.body;

  return body;
}

function append(element, child) {
  if (typeof child === 'string') {
    child = element.ownerDocument.createTextNode(child);
  }

  var _child = child,
      type = _child.type,
      attributes = _child.attributes;

  if (type) {
    child = element.ownerDocument.createElement(type);

    for (var key in attributes) {
      child.setAttribute(key, attributes[key]);
    }
  }

  return element.appendChild(child);
}

function appendText(node, text) {
  node.appendData(text);
}

function getLastChild(_ref) {
  var lastChild = _ref.lastChild;
  return lastChild;
}

function getParent(_ref2) {
  var parentNode = _ref2.parentNode;
  return parentNode;
}

function isText(_ref3) {
  var nodeType = _ref3.nodeType;
  return nodeType === TEXT_NODE;
}

function getText(_ref4) {
  var nodeValue = _ref4.nodeValue;
  return nodeValue;
}

function remove(node) {
  return node.parentNode.removeChild(node);
}

function padEmptyLines(_ref5) {
  var element = _ref5.element,
      createLinePadding = _ref5.createLinePadding,
      multilineWrapperTags = _ref5.multilineWrapperTags;
  var length = element.childNodes.length;
  var doc = element.ownerDocument;

  for (var index = 0; index < length; index++) {
    var child = element.childNodes[index];

    if (child.nodeType === TEXT_NODE) {
      if (length === 1 && !child.nodeValue) {
        // Pad if the only child is an empty text node.
        element.appendChild(createLinePadding(doc));
      }
    } else {
      if (multilineWrapperTags && !child.previousSibling && multilineWrapperTags.indexOf(child.nodeName.toLowerCase()) !== -1) {
        // Pad the line if there is no content before a nested wrapper.
        element.insertBefore(createLinePadding(doc), child);
      }

      padEmptyLines({
        element: child,
        createLinePadding: createLinePadding,
        multilineWrapperTags: multilineWrapperTags
      });
    }
  }
}

function prepareFormats() {
  var prepareEditableTree = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var value = arguments.length > 1 ? arguments[1] : undefined;
  return prepareEditableTree.reduce(function (accumlator, fn) {
    return fn(accumlator, value.text);
  }, value.formats);
}

function toDom(_ref6) {
  var value = _ref6.value,
      multilineTag = _ref6.multilineTag,
      multilineWrapperTags = _ref6.multilineWrapperTags,
      createLinePadding = _ref6.createLinePadding,
      prepareEditableTree = _ref6.prepareEditableTree;
  var startPath = [];
  var endPath = [];
  var tree = Object(_to_tree__WEBPACK_IMPORTED_MODULE_2__["toTree"])({
    value: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, value, {
      formats: prepareFormats(prepareEditableTree, value)
    }),
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    createEmpty: createEmpty,
    append: append,
    getLastChild: getLastChild,
    getParent: getParent,
    isText: isText,
    getText: getText,
    remove: remove,
    appendText: appendText,
    onStartIndex: function onStartIndex(body, pointer) {
      startPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);
    },
    onEndIndex: function onEndIndex(body, pointer) {
      endPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);
    },
    isEditableTree: true
  });

  if (createLinePadding) {
    padEmptyLines({
      element: tree,
      createLinePadding: createLinePadding,
      multilineWrapperTags: multilineWrapperTags
    });
  }

  return {
    body: tree,
    selection: {
      startPath: startPath,
      endPath: endPath
    }
  };
}
/**
 * Create an `Element` tree from a Rich Text value and applies the difference to
 * the `Element` tree contained by `current`. If a `multilineTag` is provided,
 * text separated by two new lines will be wrapped in an `Element` of that type.
 *
 * @param {Object}      value        Value to apply.
 * @param {HTMLElement} current      The live root node to apply the element
 *                                   tree to.
 * @param {string}      multilineTag Multiline tag.
 */

function apply(_ref7) {
  var value = _ref7.value,
      current = _ref7.current,
      multilineTag = _ref7.multilineTag,
      multilineWrapperTags = _ref7.multilineWrapperTags,
      createLinePadding = _ref7.createLinePadding,
      prepareEditableTree = _ref7.prepareEditableTree;

  // Construct a new element tree in memory.
  var _toDom = toDom({
    value: value,
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    createLinePadding: createLinePadding,
    prepareEditableTree: prepareEditableTree
  }),
      body = _toDom.body,
      selection = _toDom.selection;

  applyValue(body, current);

  if (value.start !== undefined) {
    applySelection(selection, current);
  }
}
function applyValue(future, current) {
  var i = 0;

  while (future.firstChild) {
    var currentChild = current.childNodes[i];
    var futureNodeType = future.firstChild.nodeType;

    if (!currentChild) {
      current.appendChild(future.firstChild);
    } else if (futureNodeType !== currentChild.nodeType || futureNodeType !== TEXT_NODE || future.firstChild.nodeValue !== currentChild.nodeValue) {
      current.replaceChild(future.firstChild, currentChild);
    } else {
      future.removeChild(future.firstChild);
    }

    i++;
  }

  while (current.childNodes[i]) {
    current.removeChild(current.childNodes[i]);
  }
}
function applySelection(selection, current) {
  var _getNodeByPath = getNodeByPath(current, selection.startPath),
      startContainer = _getNodeByPath.node,
      startOffset = _getNodeByPath.offset;

  var _getNodeByPath2 = getNodeByPath(current, selection.endPath),
      endContainer = _getNodeByPath2.node,
      endOffset = _getNodeByPath2.offset;

  var windowSelection = window.getSelection();
  var range = current.ownerDocument.createRange();
  var collapsed = startContainer === endContainer && startOffset === endOffset;

  if (collapsed && startOffset === 0 && startContainer.previousSibling && startContainer.previousSibling.nodeType === ELEMENT_NODE && startContainer.previousSibling.nodeName !== 'BR') {
    startContainer.insertData(0, "\uFEFF");
    range.setStart(startContainer, 1);
    range.setEnd(endContainer, 1);
  } else if (collapsed && startOffset === 0 && startContainer === TEXT_NODE && startContainer.nodeValue.length === 0) {
    startContainer.insertData(0, "\uFEFF");
    range.setStart(startContainer, 1);
    range.setEnd(endContainer, 1);
  } else {
    range.setStart(startContainer, startOffset);
    range.setEnd(endContainer, endOffset);
  }

  windowSelection.removeAllRanges();
  windowSelection.addRange(range);
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/to-html-string.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/to-html-string.js ***!
  \**************************************************************************/
/*! exports provided: toHTMLString */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toHTMLString", function() { return toHTMLString; });
/* harmony import */ var _wordpress_escape_html__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/escape-html */ "@wordpress/escape-html");
/* harmony import */ var _wordpress_escape_html__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_escape_html__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/deprecated */ "@wordpress/deprecated");
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _to_tree__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./to-tree */ "./node_modules/@wordpress/rich-text/build-module/to-tree.js");
/**
 * Internal dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Create an HTML string from a Rich Text value. If a `multilineTag` is
 * provided, text separated by a line separator will be wrapped in it.
 *
 * @param {Object} $1                      Named argements.
 * @param {Object} $1.value                Rich text value.
 * @param {string} $1.multilineTag         Multiline tag.
 * @param {Array}  $1.multilineWrapperTags Tags where lines can be found if
 *                                         nesting is possible.
 *
 * @return {string} HTML string.
 */

function toHTMLString(_ref) {
  var value = _ref.value,
      multilineTag = _ref.multilineTag,
      multilineWrapperTags = _ref.multilineWrapperTags;

  // Check other arguments for backward compatibility.
  if (value === undefined) {
    _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default()('wp.richText.toHTMLString positional parameters', {
      version: '4.4',
      alternative: 'named parameters',
      plugin: 'Gutenberg'
    });
    value = arguments[0];
    multilineTag = arguments[1];
    multilineWrapperTags = arguments[2];
  }

  var tree = Object(_to_tree__WEBPACK_IMPORTED_MODULE_2__["toTree"])({
    value: value,
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    createEmpty: createEmpty,
    append: append,
    getLastChild: getLastChild,
    getParent: getParent,
    isText: isText,
    getText: getText,
    remove: remove,
    appendText: appendText
  });
  return createChildrenHTML(tree.children);
}

function createEmpty() {
  return {};
}

function getLastChild(_ref2) {
  var children = _ref2.children;
  return children && children[children.length - 1];
}

function append(parent, object) {
  if (typeof object === 'string') {
    object = {
      text: object
    };
  }

  object.parent = parent;
  parent.children = parent.children || [];
  parent.children.push(object);
  return object;
}

function appendText(object, text) {
  object.text += text;
}

function getParent(_ref3) {
  var parent = _ref3.parent;
  return parent;
}

function isText(_ref4) {
  var text = _ref4.text;
  return typeof text === 'string';
}

function getText(_ref5) {
  var text = _ref5.text;
  return text;
}

function remove(object) {
  var index = object.parent.children.indexOf(object);

  if (index !== -1) {
    object.parent.children.splice(index, 1);
  }

  return object;
}

function createElementHTML(_ref6) {
  var type = _ref6.type,
      attributes = _ref6.attributes,
      object = _ref6.object,
      children = _ref6.children;
  var attributeString = '';

  for (var key in attributes) {
    if (!Object(_wordpress_escape_html__WEBPACK_IMPORTED_MODULE_0__["isValidAttributeName"])(key)) {
      continue;
    }

    attributeString += " ".concat(key, "=\"").concat(Object(_wordpress_escape_html__WEBPACK_IMPORTED_MODULE_0__["escapeAttribute"])(attributes[key]), "\"");
  }

  if (object) {
    return "<".concat(type).concat(attributeString, ">");
  }

  return "<".concat(type).concat(attributeString, ">").concat(createChildrenHTML(children), "</").concat(type, ">");
}

function createChildrenHTML() {
  var children = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  return children.map(function (child) {
    return child.text === undefined ? createElementHTML(child) : Object(_wordpress_escape_html__WEBPACK_IMPORTED_MODULE_0__["escapeHTML"])(child.text);
  }).join('');
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/to-tree.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/to-tree.js ***!
  \*******************************************************************/
/*! exports provided: toTree */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toTree", function() { return toTree; });
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _get_format_type__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./get-format-type */ "./node_modules/@wordpress/rich-text/build-module/get-format-type.js");
/* harmony import */ var _special_characters__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./special-characters */ "./node_modules/@wordpress/rich-text/build-module/special-characters.js");



/**
 * Internal dependencies
 */



function fromFormat(_ref) {
  var type = _ref.type,
      attributes = _ref.attributes,
      unregisteredAttributes = _ref.unregisteredAttributes,
      object = _ref.object;
  var formatType = Object(_get_format_type__WEBPACK_IMPORTED_MODULE_2__["getFormatType"])(type);

  if (!formatType) {
    return {
      type: type,
      attributes: attributes,
      object: object
    };
  }

  var elementAttributes = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__["default"])({}, unregisteredAttributes);

  for (var name in attributes) {
    var key = formatType.attributes[name];

    if (key) {
      elementAttributes[key] = attributes[name];
    } else {
      elementAttributes[name] = attributes[name];
    }
  }

  if (formatType.className) {
    if (elementAttributes.class) {
      elementAttributes.class = "".concat(formatType.className, " ").concat(elementAttributes.class);
    } else {
      elementAttributes.class = formatType.className;
    }
  }

  return {
    type: formatType.tagName,
    object: formatType.object,
    attributes: elementAttributes
  };
}

function toTree(_ref2) {
  var value = _ref2.value,
      multilineTag = _ref2.multilineTag,
      _ref2$multilineWrappe = _ref2.multilineWrapperTags,
      multilineWrapperTags = _ref2$multilineWrappe === void 0 ? [] : _ref2$multilineWrappe,
      createEmpty = _ref2.createEmpty,
      append = _ref2.append,
      getLastChild = _ref2.getLastChild,
      getParent = _ref2.getParent,
      isText = _ref2.isText,
      getText = _ref2.getText,
      remove = _ref2.remove,
      appendText = _ref2.appendText,
      onStartIndex = _ref2.onStartIndex,
      onEndIndex = _ref2.onEndIndex,
      isEditableTree = _ref2.isEditableTree;
  var formats = value.formats,
      text = value.text,
      start = value.start,
      end = value.end,
      formatPlaceholder = value.formatPlaceholder;
  var formatsLength = formats.length + 1;
  var tree = createEmpty();
  var multilineFormat = {
    type: multilineTag
  };
  var lastSeparatorFormats;
  var lastCharacterFormats;
  var lastCharacter; // If we're building a multiline tree, start off with a multiline element.

  if (multilineTag) {
    append(append(tree, {
      type: multilineTag
    }), '');
    lastCharacterFormats = lastSeparatorFormats = [multilineFormat];
  } else {
    append(tree, '');
  }

  function setFormatPlaceholder(pointer, index) {
    if (isEditableTree && formatPlaceholder && formatPlaceholder.index === index) {
      var parent = getParent(pointer);

      if (formatPlaceholder.format === undefined) {
        pointer = getParent(parent);
      } else {
        pointer = append(parent, fromFormat(formatPlaceholder.format));
      }

      pointer = append(pointer, _special_characters__WEBPACK_IMPORTED_MODULE_3__["ZERO_WIDTH_NO_BREAK_SPACE"]);
    }

    return pointer;
  }

  var _loop = function _loop(i) {
    var character = text.charAt(i);
    var characterFormats = formats[i]; // Set multiline tags in queue for building the tree.

    if (multilineTag) {
      if (character === _special_characters__WEBPACK_IMPORTED_MODULE_3__["LINE_SEPARATOR"]) {
        characterFormats = lastSeparatorFormats = (characterFormats || []).reduce(function (accumulator, format) {
          if (character === _special_characters__WEBPACK_IMPORTED_MODULE_3__["LINE_SEPARATOR"] && multilineWrapperTags.indexOf(format.type) !== -1) {
            accumulator.push(format);
            accumulator.push(multilineFormat);
          }

          return accumulator;
        }, [multilineFormat]);
      } else {
        characterFormats = Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(lastSeparatorFormats).concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(characterFormats || []));
      }
    }

    var pointer = getLastChild(tree); // Set selection for the start of line.

    if (lastCharacter === _special_characters__WEBPACK_IMPORTED_MODULE_3__["LINE_SEPARATOR"]) {
      var node = pointer;

      while (!isText(node)) {
        node = getLastChild(node);
      }

      if (onStartIndex && start === i) {
        onStartIndex(tree, node);
      }

      if (onEndIndex && end === i) {
        onEndIndex(tree, node);
      }
    }

    if (characterFormats) {
      characterFormats.forEach(function (format, formatIndex) {
        if (pointer && lastCharacterFormats && format === lastCharacterFormats[formatIndex] && ( // Do not reuse the last element if the character is a
        // line separator.
        character !== _special_characters__WEBPACK_IMPORTED_MODULE_3__["LINE_SEPARATOR"] || characterFormats.length - 1 !== formatIndex)) {
          pointer = getLastChild(pointer);
          return;
        }

        var parent = getParent(pointer);
        var newNode = append(parent, fromFormat(format));

        if (isText(pointer) && getText(pointer).length === 0) {
          remove(pointer);
        }

        pointer = append(format.object ? parent : newNode, '');
      });
    } // No need for further processing if the character is a line separator.


    if (character === _special_characters__WEBPACK_IMPORTED_MODULE_3__["LINE_SEPARATOR"]) {
      lastCharacterFormats = characterFormats;
      lastCharacter = character;
      return "continue";
    }

    pointer = setFormatPlaceholder(pointer, 0); // If there is selection at 0, handle it before characters are inserted.

    if (i === 0) {
      if (onStartIndex && start === 0) {
        onStartIndex(tree, pointer);
      }

      if (onEndIndex && end === 0) {
        onEndIndex(tree, pointer);
      }
    }

    if (character !== _special_characters__WEBPACK_IMPORTED_MODULE_3__["OBJECT_REPLACEMENT_CHARACTER"]) {
      if (character === '\n') {
        pointer = append(getParent(pointer), {
          type: 'br',
          object: true
        }); // Ensure pointer is text node.

        pointer = append(getParent(pointer), '');
      } else if (!isText(pointer)) {
        pointer = append(getParent(pointer), character);
      } else {
        appendText(pointer, character);
      }
    }

    pointer = setFormatPlaceholder(pointer, i + 1);

    if (onStartIndex && start === i + 1) {
      onStartIndex(tree, pointer);
    }

    if (onEndIndex && end === i + 1) {
      onEndIndex(tree, pointer);
    }

    lastCharacterFormats = characterFormats;
    lastCharacter = character;
  };

  for (var i = 0; i < formatsLength; i++) {
    var _ret = _loop(i);

    if (_ret === "continue") continue;
  }

  return tree;
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/toggle-format.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/toggle-format.js ***!
  \*************************************************************************/
/*! exports provided: toggleFormat */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toggleFormat", function() { return toggleFormat; });
/* harmony import */ var _get_active_format__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-active-format */ "./node_modules/@wordpress/rich-text/build-module/get-active-format.js");
/* harmony import */ var _remove_format__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./remove-format */ "./node_modules/@wordpress/rich-text/build-module/remove-format.js");
/* harmony import */ var _apply_format__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./apply-format */ "./node_modules/@wordpress/rich-text/build-module/apply-format.js");
/**
 * Internal dependencies
 */



/**
 * Toggles a format object to a Rich Text value at the current selection.
 *
 * @param {Object} value      Value to modify.
 * @param {Object} format     Format to apply or remove.
 *
 * @return {Object} A new value with the format applied or removed.
 */

function toggleFormat(value, format) {
  if (Object(_get_active_format__WEBPACK_IMPORTED_MODULE_0__["getActiveFormat"])(value, format.type)) {
    return Object(_remove_format__WEBPACK_IMPORTED_MODULE_1__["removeFormat"])(value, format.type);
  }

  return Object(_apply_format__WEBPACK_IMPORTED_MODULE_2__["applyFormat"])(value, format);
}


/***/ }),

/***/ "./node_modules/@wordpress/rich-text/build-module/unregister-format-type.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@wordpress/rich-text/build-module/unregister-format-type.js ***!
  \**********************************************************************************/
/*! exports provided: unregisterFormatType */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "unregisterFormatType", function() { return unregisterFormatType; });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__);
/**
 * WordPress dependencies
 */


/**
 * Unregisters a format.
 *
 * @param {string} name Format name.
 *
 * @return {?WPFormat} The previous format value, if it has been successfully
 *                     unregistered; otherwise `undefined`.
 */

function unregisterFormatType(name) {
  var oldFormat = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__["select"])('core/rich-text').getFormatType(name);

  if (!oldFormat) {
    window.console.error("Format ".concat(name, " is not registered."));
    return;
  }

  if (oldFormat.__experimentalCreatePrepareEditableTree && oldFormat.__experimentalGetPropsForEditableTreePreparation) {
    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["removeFilter"])('experimentalRichText', name);
  }

  Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__["dispatch"])('core/rich-text').removeFormatTypes(name);
  return oldFormat;
}


/***/ }),

/***/ "./node_modules/rememo/es/rememo.js":
/*!******************************************!*\
  !*** ./node_modules/rememo/es/rememo.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);


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
/* harmony default export */ __webpack_exports__["default"] = (function( selector, getDependants ) {
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

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/escape-html":
/*!*********************************************!*\
  !*** external {"this":["wp","escapeHtml"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["escapeHtml"]; }());

/***/ }),

/***/ "@wordpress/hooks":
/*!****************************************!*\
  !*** external {"this":["wp","hooks"]} ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

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
//# sourceMappingURL=rich-text.js.map