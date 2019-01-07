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
  var newFormats = formats.slice(0); // If the selection is collapsed, expand start and end to the edges of the
  // format.

  if (startIndex === endIndex) {
    var startFormat = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(newFormats[startIndex], {
      type: format.type
    });

    while (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(newFormats[startIndex], startFormat)) {
      applyFormats(newFormats, startIndex, format);
      startIndex--;
    }

    endIndex++;

    while (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["find"])(newFormats[endIndex], startFormat)) {
      applyFormats(newFormats, endIndex, format);
      endIndex++;
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
/* harmony import */ var _is_empty__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./is-empty */ "./node_modules/@wordpress/rich-text/build-module/is-empty.js");
/* harmony import */ var _is_format_equal__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./is-format-equal */ "./node_modules/@wordpress/rich-text/build-module/is-format-equal.js");
/* harmony import */ var _create_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./create-element */ "./node_modules/@wordpress/rich-text/build-module/create-element.js");



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
/**
 * Create a RichText value from an `Element` tree (DOM), an HTML string or a
 * plain text string, with optionally a `Range` object to set the selection. If
 * called without any input, an empty value will be created. If
 * `multilineTag` is provided, any content of direct children whose type matches
 * `multilineTag` will be separated by two newlines. The optional functions can
 * be used to filter out content.
 *
 * @param {?Object}   $1                 Optional named argements.
 * @param {?Element}  $1.element         Element to create value from.
 * @param {?string}   $1.text            Text to create value from.
 * @param {?string}   $1.html            HTML to create value from.
 * @param {?Range}    $1.range           Range to create value from.
 * @param {?string}   $1.multilineTag    Multiline tag if the structure is
 *                                       multiline.
 * @param {?Function} $1.removeNode      Function to declare whether the given
 *                                       node should be removed.
 * @param {?Function} $1.unwrapNode      Function to declare whether the given
 *                                       node should be unwrapped.
 * @param {?Function} $1.filterString    Function to filter the given string.
 * @param {?Function} $1.removeAttribute Wether to remove an attribute based on
 *                                       the name.
 *
 * @return {Object} A rich text value.
 */


function create() {
  var _ref = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
      element = _ref.element,
      text = _ref.text,
      html = _ref.html,
      range = _ref.range,
      multilineTag = _ref.multilineTag,
      removeNode = _ref.removeNode,
      unwrapNode = _ref.unwrapNode,
      filterString = _ref.filterString,
      removeAttribute = _ref.removeAttribute;

  if (typeof text === 'string' && text.length > 0) {
    return {
      formats: Array(text.length),
      text: text
    };
  }

  if (typeof html === 'string' && html.length > 0) {
    element = Object(_create_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(document, html);
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
  } else if (node === startContainer) {
    accumulator.start = currentLength + startOffset; // Range indicates that the current node is selected.
  } else if (parentNode === startContainer && node === startContainer.childNodes[startOffset]) {
    accumulator.start = currentLength;
  } // Selection can be extracted from value.


  if (value.end !== undefined) {
    accumulator.end = currentLength + value.end; // Range indicates that the current node has selection.
  } else if (node === endContainer) {
    accumulator.end = currentLength + endOffset; // Range indicates that the current node is selected.
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset - 1]) {
    accumulator.end = currentLength + value.text.length; // Range indicates that the selection is before the current node.
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset]) {
    accumulator.end = currentLength;
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
 * @param {Object}    $1                 Named argements.
 * @param {?Element}  $1.element         Element to create value from.
 * @param {?Range}    $1.range           Range to create value from.
 * @param {?Function} $1.removeNode      Function to declare whether the given
 *                                       node should be removed.
 * @param {?Function} $1.unwrapNode      Function to declare whether the given
 *                                       node should be unwrapped.
 * @param {?Function} $1.filterString    Function to filter the given string.
 * @param {?Function} $1.removeAttribute Wether to remove an attribute based on
 *                                       the name.
 *
 * @return {Object} A rich text value.
 */


function createFromElement(_ref2) {
  var element = _ref2.element,
      range = _ref2.range,
      removeNode = _ref2.removeNode,
      unwrapNode = _ref2.unwrapNode,
      filterString = _ref2.filterString,
      removeAttribute = _ref2.removeAttribute;
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

    if (node.nodeName === 'BR') {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      accumulator.text += '\n';
      accumulator.formats.length += 1;
      continue;
    }

    var lastFormats = accumulator.formats[accumulator.formats.length - 1];
    var lastFormat = lastFormats && lastFormats[lastFormats.length - 1];
    var format = void 0;

    if (!unwrapNode || !unwrapNode(node)) {
      var type = node.nodeName.toLowerCase();
      var attributes = getAttributes({
        element: node,
        removeAttribute: removeAttribute
      });
      var newFormat = attributes ? {
        type: type,
        attributes: attributes
      } : {
        type: type
      }; // Reuse the last format if it's equal.

      if (Object(_is_format_equal__WEBPACK_IMPORTED_MODULE_3__["isFormatEqual"])(newFormat, lastFormat)) {
        format = lastFormat;
      } else {
        format = newFormat;
      }
    }

    var value = createFromElement({
      element: node,
      range: range,
      removeNode: removeNode,
      unwrapNode: unwrapNode,
      filterString: filterString,
      removeAttribute: removeAttribute
    });
    var text = value.text;
    var start = accumulator.text.length;
    accumulateSelection(accumulator, node, range, value); // Don't apply the element as formatting if it has no content.

    if (Object(_is_empty__WEBPACK_IMPORTED_MODULE_2__["isEmpty"])(value) && format && !format.attributes) {
      continue;
    }

    var formats = accumulator.formats;

    if (format && format.attributes && text.length === 0) {
      format.object = true; // Object replacement character.

      accumulator.text += "\uFFFC";

      if (formats[start]) {
        formats[start].unshift(format);
      } else {
        formats[start] = [format];
      }
    } else {
      accumulator.text += text;
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
 * @param {Object}    $1                 Named argements.
 * @param {?Element}  $1.element         Element to create value from.
 * @param {?Range}    $1.range           Range to create value from.
 * @param {?string}   $1.multilineTag    Multiline tag if the structure is
 *                                       multiline.
 * @param {?Function} $1.removeNode      Function to declare whether the given
 *                                       node should be removed.
 * @param {?Function} $1.unwrapNode      Function to declare whether the given
 *                                       node should be unwrapped.
 * @param {?Function} $1.filterString    Function to filter the given string.
 * @param {?Function} $1.removeAttribute Wether to remove an attribute based on
 *                                       the name.
 *
 * @return {Object} A rich text value.
 */


function createFromMultilineElement(_ref3) {
  var element = _ref3.element,
      range = _ref3.range,
      multilineTag = _ref3.multilineTag,
      removeNode = _ref3.removeNode,
      unwrapNode = _ref3.unwrapNode,
      filterString = _ref3.filterString,
      removeAttribute = _ref3.removeAttribute;
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
      removeNode: removeNode,
      unwrapNode: unwrapNode,
      filterString: filterString,
      removeAttribute: removeAttribute
    }); // Multiline value text should be separated by a double line break.

    if (index !== 0) {
      accumulator.formats = accumulator.formats.concat([,]);
      accumulator.text += "\u2028";
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


function getAttributes(_ref4) {
  var element = _ref4.element,
      removeAttribute = _ref4.removeAttribute;

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
/*! exports provided: applyFormat, concat, create, getActiveFormat, getTextContent, isCollapsed, isEmpty, isEmptyLine, join, removeFormat, remove, replace, insert, slice, split, apply, unstableToDom, toHTMLString */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _apply_format__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./apply-format */ "./node_modules/@wordpress/rich-text/build-module/apply-format.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "applyFormat", function() { return _apply_format__WEBPACK_IMPORTED_MODULE_0__["applyFormat"]; });

/* harmony import */ var _concat__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./concat */ "./node_modules/@wordpress/rich-text/build-module/concat.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "concat", function() { return _concat__WEBPACK_IMPORTED_MODULE_1__["concat"]; });

/* harmony import */ var _create__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./create */ "./node_modules/@wordpress/rich-text/build-module/create.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "create", function() { return _create__WEBPACK_IMPORTED_MODULE_2__["create"]; });

/* harmony import */ var _get_active_format__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./get-active-format */ "./node_modules/@wordpress/rich-text/build-module/get-active-format.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getActiveFormat", function() { return _get_active_format__WEBPACK_IMPORTED_MODULE_3__["getActiveFormat"]; });

/* harmony import */ var _get_text_content__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./get-text-content */ "./node_modules/@wordpress/rich-text/build-module/get-text-content.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getTextContent", function() { return _get_text_content__WEBPACK_IMPORTED_MODULE_4__["getTextContent"]; });

/* harmony import */ var _is_collapsed__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./is-collapsed */ "./node_modules/@wordpress/rich-text/build-module/is-collapsed.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isCollapsed", function() { return _is_collapsed__WEBPACK_IMPORTED_MODULE_5__["isCollapsed"]; });

/* harmony import */ var _is_empty__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./is-empty */ "./node_modules/@wordpress/rich-text/build-module/is-empty.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEmpty", function() { return _is_empty__WEBPACK_IMPORTED_MODULE_6__["isEmpty"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEmptyLine", function() { return _is_empty__WEBPACK_IMPORTED_MODULE_6__["isEmptyLine"]; });

/* harmony import */ var _join__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./join */ "./node_modules/@wordpress/rich-text/build-module/join.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "join", function() { return _join__WEBPACK_IMPORTED_MODULE_7__["join"]; });

/* harmony import */ var _remove_format__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./remove-format */ "./node_modules/@wordpress/rich-text/build-module/remove-format.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "removeFormat", function() { return _remove_format__WEBPACK_IMPORTED_MODULE_8__["removeFormat"]; });

/* harmony import */ var _remove__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./remove */ "./node_modules/@wordpress/rich-text/build-module/remove.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "remove", function() { return _remove__WEBPACK_IMPORTED_MODULE_9__["remove"]; });

/* harmony import */ var _replace__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./replace */ "./node_modules/@wordpress/rich-text/build-module/replace.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "replace", function() { return _replace__WEBPACK_IMPORTED_MODULE_10__["replace"]; });

/* harmony import */ var _insert__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./insert */ "./node_modules/@wordpress/rich-text/build-module/insert.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "insert", function() { return _insert__WEBPACK_IMPORTED_MODULE_11__["insert"]; });

/* harmony import */ var _slice__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./slice */ "./node_modules/@wordpress/rich-text/build-module/slice.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "slice", function() { return _slice__WEBPACK_IMPORTED_MODULE_12__["slice"]; });

/* harmony import */ var _split__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./split */ "./node_modules/@wordpress/rich-text/build-module/split.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "split", function() { return _split__WEBPACK_IMPORTED_MODULE_13__["split"]; });

/* harmony import */ var _to_dom__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./to-dom */ "./node_modules/@wordpress/rich-text/build-module/to-dom.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "apply", function() { return _to_dom__WEBPACK_IMPORTED_MODULE_14__["apply"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unstableToDom", function() { return _to_dom__WEBPACK_IMPORTED_MODULE_14__["toDom"]; });

/* harmony import */ var _to_html_string__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./to-html-string */ "./node_modules/@wordpress/rich-text/build-module/to-html-string.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "toHTMLString", function() { return _to_html_string__WEBPACK_IMPORTED_MODULE_15__["toHTMLString"]; });



















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

  if (start === 0 && text.slice(0, 1) === "\u2028") {
    return true;
  }

  if (start === text.length && text.slice(-1) === "\u2028") {
    return true;
  }

  return text.slice(start - 1, end + 1) === "\u2028\u2028";
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
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _to_tree__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./to-tree */ "./node_modules/@wordpress/rich-text/build-module/to-tree.js");


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

  path = [i].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(path));

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
  path = Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(path);

  while (node && path.length > 1) {
    node = node.childNodes[path.shift()];
  }

  return {
    node: node,
    offset: path[0]
  };
}

function createEmpty(type) {
  var _document$implementat = document.implementation.createHTMLDocument(''),
      body = _document$implementat.body;

  if (type) {
    return body.appendChild(body.ownerDocument.createElement(type));
  }

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

function toDom(value, multilineTag) {
  var startPath = [];
  var endPath = [];
  var tree = Object(_to_tree__WEBPACK_IMPORTED_MODULE_1__["toTree"])(value, multilineTag, {
    createEmpty: createEmpty,
    append: append,
    getLastChild: getLastChild,
    getParent: getParent,
    isText: isText,
    getText: getText,
    remove: remove,
    appendText: appendText,
    onStartIndex: function onStartIndex(body, pointer, multilineIndex) {
      startPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);

      if (multilineIndex !== undefined) {
        startPath = [multilineIndex].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(startPath));
      }
    },
    onEndIndex: function onEndIndex(body, pointer, multilineIndex) {
      endPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);

      if (multilineIndex !== undefined) {
        endPath = [multilineIndex].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(endPath));
      }
    },
    onEmpty: function onEmpty(body) {
      var br = body.ownerDocument.createElement('br');
      br.setAttribute('data-mce-bogus', '1');
      body.appendChild(br);
    }
  });
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

function apply(value, current, multilineTag) {
  // Construct a new element tree in memory.
  var _toDom = toDom(value, multilineTag),
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
/* harmony import */ var _to_tree__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./to-tree */ "./node_modules/@wordpress/rich-text/build-module/to-tree.js");
/**
 * Internal dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Create an HTML string from a Rich Text value. If a `multilineTag` is
 * provided, text separated by two new lines will be wrapped in it.
 *
 * @param {Object} value        Rich text value.
 * @param {string} multilineTag Multiline tag.
 *
 * @return {string} HTML string.
 */

function toHTMLString(value, multilineTag) {
  var tree = Object(_to_tree__WEBPACK_IMPORTED_MODULE_1__["toTree"])(value, multilineTag, {
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

function createEmpty(type) {
  return {
    type: type
  };
}

function getLastChild(_ref) {
  var children = _ref.children;
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

function getParent(_ref2) {
  var parent = _ref2.parent;
  return parent;
}

function isText(_ref3) {
  var text = _ref3.text;
  return typeof text === 'string';
}

function getText(_ref4) {
  var text = _ref4.text;
  return text;
}

function remove(object) {
  var index = object.parent.children.indexOf(object);

  if (index !== -1) {
    object.parent.children.splice(index, 1);
  }

  return object;
}

function createElementHTML(_ref5) {
  var type = _ref5.type,
      attributes = _ref5.attributes,
      object = _ref5.object,
      children = _ref5.children;
  var attributeString = '';

  for (var key in attributes) {
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
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js");
/* harmony import */ var _split__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./split */ "./node_modules/@wordpress/rich-text/build-module/split.js");


/**
 * Internal dependencies
 */

function toTree(value, multilineTag, settings) {
  if (multilineTag) {
    var _createEmpty = settings.createEmpty,
        _append = settings.append;

    var _tree = _createEmpty();

    Object(_split__WEBPACK_IMPORTED_MODULE_1__["split"])(value, "\u2028").forEach(function (piece, index) {
      _append(_tree, toTree(piece, null, Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__["default"])({}, settings, {
        tag: multilineTag,
        multilineIndex: index
      })));
    });
    return _tree;
  }

  var tag = settings.tag,
      multilineIndex = settings.multilineIndex,
      createEmpty = settings.createEmpty,
      append = settings.append,
      getLastChild = settings.getLastChild,
      getParent = settings.getParent,
      isText = settings.isText,
      getText = settings.getText,
      remove = settings.remove,
      appendText = settings.appendText,
      onStartIndex = settings.onStartIndex,
      onEndIndex = settings.onEndIndex,
      onEmpty = settings.onEmpty;
  var formats = value.formats,
      text = value.text,
      start = value.start,
      end = value.end;
  var formatsLength = formats.length + 1;
  var tree = createEmpty(tag);
  append(tree, '');

  var _loop = function _loop(i) {
    var character = text.charAt(i);
    var characterFormats = formats[i];
    var lastCharacterFormats = formats[i - 1];
    var pointer = getLastChild(tree);

    if (characterFormats) {
      characterFormats.forEach(function (format, formatIndex) {
        if (pointer && lastCharacterFormats && format === lastCharacterFormats[formatIndex]) {
          pointer = getLastChild(pointer);
          return;
        }

        var type = format.type,
            attributes = format.attributes,
            object = format.object;
        var parent = getParent(pointer);
        var newNode = append(parent, {
          type: type,
          attributes: attributes,
          object: object
        });

        if (isText(pointer) && getText(pointer).length === 0) {
          remove(pointer);
        }

        pointer = append(object ? parent : newNode, '');
      });
    } // If there is selection at 0, handle it before characters are inserted.


    if (onStartIndex && start === 0 && i === 0) {
      onStartIndex(tree, pointer, multilineIndex);
    }

    if (onEndIndex && end === 0 && i === 0) {
      onEndIndex(tree, pointer, multilineIndex);
    }

    if (character !== "\uFFFC") {
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

    if (onStartIndex && start === i + 1) {
      onStartIndex(tree, pointer, multilineIndex);
    }

    if (onEndIndex && end === i + 1) {
      onEndIndex(tree, pointer, multilineIndex);
    }
  };

  for (var i = 0; i < formatsLength; i++) {
    _loop(i);
  }

  if (onEmpty && text.length === 0) {
    onEmpty(tree);
  }

  return tree;
}


/***/ }),

/***/ "@wordpress/escape-html":
/*!*********************************************!*\
  !*** external {"this":["wp","escapeHtml"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["escapeHtml"]; }());

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