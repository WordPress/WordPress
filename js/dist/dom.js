this["wp"] = this["wp"] || {}; this["wp"]["dom"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/dom/build-module/index.js");
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

/***/ "./node_modules/@wordpress/dom/build-module/dom.js":
/*!*********************************************************!*\
  !*** ./node_modules/@wordpress/dom/build-module/dom.js ***!
  \*********************************************************/
/*! exports provided: isHorizontalEdge, isVerticalEdge, getRectangleFromRange, computeCaretRect, placeCaretAtHorizontalEdge, placeCaretAtVerticalEdge, isTextField, documentHasSelection, isEntirelySelected, getScrollContainer, getOffsetParent, replace, remove, insertAfter, unwrap, replaceTag */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isHorizontalEdge", function() { return isHorizontalEdge; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isVerticalEdge", function() { return isVerticalEdge; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getRectangleFromRange", function() { return getRectangleFromRange; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "computeCaretRect", function() { return computeCaretRect; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "placeCaretAtHorizontalEdge", function() { return placeCaretAtHorizontalEdge; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "placeCaretAtVerticalEdge", function() { return placeCaretAtVerticalEdge; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isTextField", function() { return isTextField; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "documentHasSelection", function() { return documentHasSelection; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEntirelySelected", function() { return isEntirelySelected; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getScrollContainer", function() { return getScrollContainer; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getOffsetParent", function() { return getOffsetParent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "replace", function() { return replace; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "remove", function() { return remove; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "insertAfter", function() { return insertAfter; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "unwrap", function() { return unwrap; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "replaceTag", function() { return replaceTag; });
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
/**
 * External dependencies
 */

/**
 * Browser dependencies
 */

var _window = window,
    getComputedStyle = _window.getComputedStyle;
var _window$Node = window.Node,
    TEXT_NODE = _window$Node.TEXT_NODE,
    ELEMENT_NODE = _window$Node.ELEMENT_NODE,
    DOCUMENT_POSITION_PRECEDING = _window$Node.DOCUMENT_POSITION_PRECEDING,
    DOCUMENT_POSITION_FOLLOWING = _window$Node.DOCUMENT_POSITION_FOLLOWING;
/**
 * Returns true if the given selection object is in the forward direction, or
 * false otherwise.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Node/compareDocumentPosition
 *
 * @param {Selection} selection Selection object to check.
 *
 * @return {boolean} Whether the selection is forward.
 */

function isSelectionForward(selection) {
  var anchorNode = selection.anchorNode,
      focusNode = selection.focusNode,
      anchorOffset = selection.anchorOffset,
      focusOffset = selection.focusOffset;
  var position = anchorNode.compareDocumentPosition(focusNode); // Disable reason: `Node#compareDocumentPosition` returns a bitmask value,
  // so bitwise operators are intended.

  /* eslint-disable no-bitwise */
  // Compare whether anchor node precedes focus node. If focus node (where
  // end of selection occurs) is after the anchor node, it is forward.

  if (position & DOCUMENT_POSITION_PRECEDING) {
    return false;
  }

  if (position & DOCUMENT_POSITION_FOLLOWING) {
    return true;
  }
  /* eslint-enable no-bitwise */
  // `compareDocumentPosition` returns 0 when passed the same node, in which
  // case compare offsets.


  if (position === 0) {
    return anchorOffset <= focusOffset;
  } // This should never be reached, but return true as default case.


  return true;
}
/**
 * Check whether the selection is horizontally at the edge of the container.
 *
 * @param {Element} container Focusable element.
 * @param {boolean} isReverse Set to true to check left, false for right.
 *
 * @return {boolean} True if at the horizontal edge, false if not.
 */


function isHorizontalEdge(container, isReverse) {
  if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["includes"])(['INPUT', 'TEXTAREA'], container.tagName)) {
    if (container.selectionStart !== container.selectionEnd) {
      return false;
    }

    if (isReverse) {
      return container.selectionStart === 0;
    }

    return container.value.length === container.selectionStart;
  }

  if (!container.isContentEditable) {
    return true;
  }

  var selection = window.getSelection(); // Create copy of range for setting selection to find effective offset.

  var range = selection.getRangeAt(0).cloneRange(); // Collapse in direction of selection.

  if (!selection.isCollapsed) {
    range.collapse(!isSelectionForward(selection));
  }

  var node = range.startContainer;
  var extentOffset;

  if (isReverse) {
    // When in reverse, range node should be first.
    extentOffset = 0;
  } else if (node.nodeValue) {
    // Otherwise, vary by node type. A text node has no children. Its range
    // offset reflects its position in nodeValue.
    //
    // "If the startContainer is a Node of type Text, Comment, or
    // CDATASection, then the offset is the number of characters from the
    // start of the startContainer to the boundary point of the Range."
    //
    // See: https://developer.mozilla.org/en-US/docs/Web/API/Range/startOffset
    // See: https://developer.mozilla.org/en-US/docs/Web/API/Node/nodeValue
    extentOffset = node.nodeValue.length;
  } else {
    // "For other Node types, the startOffset is the number of child nodes
    // between the start of the startContainer and the boundary point of
    // the Range."
    //
    // See: https://developer.mozilla.org/en-US/docs/Web/API/Range/startOffset
    extentOffset = node.childNodes.length;
  } // Offset of range should be at expected extent.


  var position = isReverse ? 'start' : 'end';
  var offset = range["".concat(position, "Offset")];

  if (offset !== extentOffset) {
    return false;
  } // If confirmed to be at extent, traverse up through DOM, verifying that
  // the node is at first or last child for reverse or forward respectively.
  // Continue until container is reached.


  var order = isReverse ? 'first' : 'last';

  while (node !== container) {
    var parentNode = node.parentNode;

    if (parentNode["".concat(order, "Child")] !== node) {
      return false;
    }

    node = parentNode;
  } // If reached, range is assumed to be at edge.


  return true;
}
/**
 * Check whether the selection is vertically at the edge of the container.
 *
 * @param {Element} container Focusable element.
 * @param {boolean} isReverse Set to true to check top, false for bottom.
 *
 * @return {boolean} True if at the edge, false if not.
 */

function isVerticalEdge(container, isReverse) {
  if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["includes"])(['INPUT', 'TEXTAREA'], container.tagName)) {
    return isHorizontalEdge(container, isReverse);
  }

  if (!container.isContentEditable) {
    return true;
  }

  var selection = window.getSelection();
  var range = selection.rangeCount ? selection.getRangeAt(0) : null;

  if (!range) {
    return false;
  }

  var rangeRect = getRectangleFromRange(range);

  if (!rangeRect) {
    return false;
  }

  var buffer = rangeRect.height / 2;
  var editableRect = container.getBoundingClientRect(); // Too low.

  if (isReverse && rangeRect.top - buffer > editableRect.top) {
    return false;
  } // Too high.


  if (!isReverse && rangeRect.bottom + buffer < editableRect.bottom) {
    return false;
  }

  return true;
}
/**
 * Get the rectangle of a given Range.
 *
 * @param {Range} range The range.
 *
 * @return {DOMRect} The rectangle.
 */

function getRectangleFromRange(range) {
  // For uncollapsed ranges, get the rectangle that bounds the contents of the
  // range; this a rectangle enclosing the union of the bounding rectangles
  // for all the elements in the range.
  if (!range.collapsed) {
    return range.getBoundingClientRect();
  }

  var rect = range.getClientRects()[0]; // If the collapsed range starts (and therefore ends) at an element node,
  // `getClientRects` can be empty in some browsers. This can be resolved
  // by adding a temporary text node with zero-width space to the range.
  //
  // See: https://stackoverflow.com/a/6847328/995445

  if (!rect) {
    var padNode = document.createTextNode("\u200B");
    range.insertNode(padNode);
    rect = range.getClientRects()[0];
    padNode.parentNode.removeChild(padNode);
  }

  return rect;
}
/**
 * Get the rectangle for the selection in a container.
 *
 * @param {Element} container Editable container.
 *
 * @return {?DOMRect} The rectangle.
 */

function computeCaretRect(container) {
  if (!container.isContentEditable) {
    return;
  }

  var selection = window.getSelection();
  var range = selection.rangeCount ? selection.getRangeAt(0) : null;

  if (!range) {
    return;
  }

  return getRectangleFromRange(range);
}
/**
 * Places the caret at start or end of a given element.
 *
 * @param {Element} container Focusable element.
 * @param {boolean} isReverse True for end, false for start.
 */

function placeCaretAtHorizontalEdge(container, isReverse) {
  if (!container) {
    return;
  }

  if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["includes"])(['INPUT', 'TEXTAREA'], container.tagName)) {
    container.focus();

    if (isReverse) {
      container.selectionStart = container.value.length;
      container.selectionEnd = container.value.length;
    } else {
      container.selectionStart = 0;
      container.selectionEnd = 0;
    }

    return;
  }

  container.focus();

  if (!container.isContentEditable) {
    return;
  } // Select on extent child of the container, not the container itself. This
  // avoids the selection always being `endOffset` of 1 when placed at end,
  // where `startContainer`, `endContainer` would always be container itself.


  var rangeTarget = container[isReverse ? 'lastChild' : 'firstChild']; // If no range target, it implies that the container is empty. Focusing is
  // sufficient for caret to be placed correctly.

  if (!rangeTarget) {
    return;
  }

  var selection = window.getSelection();
  var range = document.createRange();
  range.selectNodeContents(rangeTarget);
  range.collapse(!isReverse);
  selection.removeAllRanges();
  selection.addRange(range);
}
/**
 * Polyfill.
 * Get a collapsed range for a given point.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Document/caretRangeFromPoint
 *
 * @param {Document} doc The document of the range.
 * @param {number}    x   Horizontal position within the current viewport.
 * @param {number}    y   Vertical position within the current viewport.
 *
 * @return {?Range} The best range for the given point.
 */

function caretRangeFromPoint(doc, x, y) {
  if (doc.caretRangeFromPoint) {
    return doc.caretRangeFromPoint(x, y);
  }

  if (!doc.caretPositionFromPoint) {
    return null;
  }

  var point = doc.caretPositionFromPoint(x, y); // If x or y are negative, outside viewport, or there is no text entry node.
  // https://developer.mozilla.org/en-US/docs/Web/API/Document/caretRangeFromPoint

  if (!point) {
    return null;
  }

  var range = doc.createRange();
  range.setStart(point.offsetNode, point.offset);
  range.collapse(true);
  return range;
}
/**
 * Get a collapsed range for a given point.
 * Gives the container a temporary high z-index (above any UI).
 * This is preferred over getting the UI nodes and set styles there.
 *
 * @param {Document} doc       The document of the range.
 * @param {number}    x         Horizontal position within the current viewport.
 * @param {number}    y         Vertical position within the current viewport.
 * @param {Element}  container Container in which the range is expected to be found.
 *
 * @return {?Range} The best range for the given point.
 */


function hiddenCaretRangeFromPoint(doc, x, y, container) {
  container.style.zIndex = '10000';
  var range = caretRangeFromPoint(doc, x, y);
  container.style.zIndex = null;
  return range;
}
/**
 * Places the caret at the top or bottom of a given element.
 *
 * @param {Element} container           Focusable element.
 * @param {boolean} isReverse           True for bottom, false for top.
 * @param {DOMRect} [rect]              The rectangle to position the caret with.
 * @param {boolean} [mayUseScroll=true] True to allow scrolling, false to disallow.
 */


function placeCaretAtVerticalEdge(container, isReverse, rect) {
  var mayUseScroll = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;

  if (!container) {
    return;
  }

  if (!rect || !container.isContentEditable) {
    placeCaretAtHorizontalEdge(container, isReverse);
    return;
  } // Offset by a buffer half the height of the caret rect. This is needed
  // because caretRangeFromPoint may default to the end of the selection if
  // offset is too close to the edge. It's unclear how to precisely calculate
  // this threshold; it may be the padded area of some combination of line
  // height, caret height, and font size. The buffer offset is effectively
  // equivalent to a point at half the height of a line of text.


  var buffer = rect.height / 2;
  var editableRect = container.getBoundingClientRect();
  var x = rect.left;
  var y = isReverse ? editableRect.bottom - buffer : editableRect.top + buffer;
  var selection = window.getSelection();
  var range = hiddenCaretRangeFromPoint(document, x, y, container);

  if (!range || !container.contains(range.startContainer)) {
    if (mayUseScroll && (!range || !range.startContainer || !range.startContainer.contains(container))) {
      // Might be out of view.
      // Easier than attempting to calculate manually.
      container.scrollIntoView(isReverse);
      placeCaretAtVerticalEdge(container, isReverse, rect, false);
      return;
    }

    placeCaretAtHorizontalEdge(container, isReverse);
    return;
  } // Check if the closest text node is actually further away.
  // If so, attempt to get the range again with the y position adjusted to get the right offset.


  if (range.startContainer.nodeType === TEXT_NODE) {
    var parentNode = range.startContainer.parentNode;
    var parentRect = parentNode.getBoundingClientRect();
    var side = isReverse ? 'bottom' : 'top';
    var padding = parseInt(getComputedStyle(parentNode).getPropertyValue("padding-".concat(side)), 10) || 0;
    var actualY = isReverse ? parentRect.bottom - padding - buffer : parentRect.top + padding + buffer;

    if (y !== actualY) {
      range = hiddenCaretRangeFromPoint(document, x, actualY, container);
    }
  }

  selection.removeAllRanges();
  selection.addRange(range);
  container.focus(); // Editable was already focussed, it goes back to old range...
  // This fixes it.

  selection.removeAllRanges();
  selection.addRange(range);
}
/**
 * Check whether the given element is a text field, where text field is defined
 * by the ability to select within the input, or that it is contenteditable.
 *
 * See: https://html.spec.whatwg.org/#textFieldSelection
 *
 * @param {HTMLElement} element The HTML element.
 *
 * @return {boolean} True if the element is an text field, false if not.
 */

function isTextField(element) {
  var nodeName = element.nodeName,
      selectionStart = element.selectionStart,
      contentEditable = element.contentEditable;
  return nodeName === 'INPUT' && selectionStart !== null || nodeName === 'TEXTAREA' || contentEditable === 'true';
}
/**
 * Check wether the current document has a selection.
 * This checks both for focus in an input field and general text selection.
 *
 * @return {boolean} True if there is selection, false if not.
 */

function documentHasSelection() {
  if (isTextField(document.activeElement)) {
    return true;
  }

  var selection = window.getSelection();
  var range = selection.rangeCount ? selection.getRangeAt(0) : null;
  return range && !range.collapsed;
}
/**
 * Check whether the contents of the element have been entirely selected.
 * Returns true if there is no possibility of selection.
 *
 * @param {Element} element The element to check.
 *
 * @return {boolean} True if entirely selected, false if not.
 */

function isEntirelySelected(element) {
  if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__["includes"])(['INPUT', 'TEXTAREA'], element.nodeName)) {
    return element.selectionStart === 0 && element.value.length === element.selectionEnd;
  }

  if (!element.isContentEditable) {
    return true;
  }

  var selection = window.getSelection();
  var range = selection.rangeCount ? selection.getRangeAt(0) : null;

  if (!range) {
    return true;
  }

  var startContainer = range.startContainer,
      endContainer = range.endContainer,
      startOffset = range.startOffset,
      endOffset = range.endOffset;
  return startContainer === element && endContainer === element && startOffset === 0 && endOffset === element.childNodes.length;
}
/**
 * Given a DOM node, finds the closest scrollable container node.
 *
 * @param {Element} node Node from which to start.
 *
 * @return {?Element} Scrollable container node, if found.
 */

function getScrollContainer(node) {
  if (!node) {
    return;
  } // Scrollable if scrollable height exceeds displayed...


  if (node.scrollHeight > node.clientHeight) {
    // ...except when overflow is defined to be hidden or visible
    var _window$getComputedSt = window.getComputedStyle(node),
        overflowY = _window$getComputedSt.overflowY;

    if (/(auto|scroll)/.test(overflowY)) {
      return node;
    }
  } // Continue traversing


  return getScrollContainer(node.parentNode);
}
/**
 * Returns the closest positioned element, or null under any of the conditions
 * of the offsetParent specification. Unlike offsetParent, this function is not
 * limited to HTMLElement and accepts any Node (e.g. Node.TEXT_NODE).
 *
 * @see https://drafts.csswg.org/cssom-view/#dom-htmlelement-offsetparent
 *
 * @param {Node} node Node from which to find offset parent.
 *
 * @return {?Node} Offset parent.
 */

function getOffsetParent(node) {
  // Cannot retrieve computed style or offset parent only anything other than
  // an element node, so find the closest element node.
  var closestElement;

  while (closestElement = node.parentNode) {
    if (closestElement.nodeType === ELEMENT_NODE) {
      break;
    }
  }

  if (!closestElement) {
    return null;
  } // If the closest element is already positioned, return it, as offsetParent
  // does not otherwise consider the node itself.


  if (getComputedStyle(closestElement).position !== 'static') {
    return closestElement;
  }

  return closestElement.offsetParent;
}
/**
 * Given two DOM nodes, replaces the former with the latter in the DOM.
 *
 * @param {Element} processedNode Node to be removed.
 * @param {Element} newNode       Node to be inserted in its place.
 * @return {void}
 */

function replace(processedNode, newNode) {
  insertAfter(newNode, processedNode.parentNode);
  remove(processedNode);
}
/**
 * Given a DOM node, removes it from the DOM.
 *
 * @param {Element} node Node to be removed.
 * @return {void}
 */

function remove(node) {
  node.parentNode.removeChild(node);
}
/**
 * Given two DOM nodes, inserts the former in the DOM as the next sibling of
 * the latter.
 *
 * @param {Element} newNode       Node to be inserted.
 * @param {Element} referenceNode Node after which to perform the insertion.
 * @return {void}
 */

function insertAfter(newNode, referenceNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}
/**
 * Unwrap the given node. This means any child nodes are moved to the parent.
 *
 * @param {Node} node The node to unwrap.
 *
 * @return {void}
 */

function unwrap(node) {
  var parent = node.parentNode;

  while (node.firstChild) {
    parent.insertBefore(node.firstChild, node);
  }

  parent.removeChild(node);
}
/**
 * Replaces the given node with a new node with the given tag name.
 *
 * @param {Element}  node    The node to replace
 * @param {string}   tagName The new tag name.
 * @param {Document} doc     The document of the node.
 *
 * @return {Element} The new node.
 */

function replaceTag(node, tagName, doc) {
  var newNode = doc.createElement(tagName);

  while (node.firstChild) {
    newNode.appendChild(node.firstChild);
  }

  node.parentNode.replaceChild(newNode, node);
  return newNode;
}


/***/ }),

/***/ "./node_modules/@wordpress/dom/build-module/focusable.js":
/*!***************************************************************!*\
  !*** ./node_modules/@wordpress/dom/build-module/focusable.js ***!
  \***************************************************************/
/*! exports provided: find */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "find", function() { return find; });
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");


/**
 * References:
 *
 * Focusable:
 *  - https://www.w3.org/TR/html5/editing.html#focus-management
 *
 * Sequential focus navigation:
 *  - https://www.w3.org/TR/html5/editing.html#sequential-focus-navigation-and-the-tabindex-attribute
 *
 * Disabled elements:
 *  - https://www.w3.org/TR/html5/disabled-elements.html#disabled-elements
 *
 * getClientRects algorithm (requiring layout box):
 *  - https://www.w3.org/TR/cssom-view-1/#extension-to-the-element-interface
 *
 * AREA elements associated with an IMG:
 *  - https://w3c.github.io/html/editing.html#data-model
 */
var SELECTOR = ['[tabindex]', 'a[href]', 'button:not([disabled])', 'input:not([type="hidden"]):not([disabled])', 'select:not([disabled])', 'textarea:not([disabled])', 'iframe', 'object', 'embed', 'area[href]', '[contenteditable]:not([contenteditable=false])'].join(',');
/**
 * Returns true if the specified element is visible (i.e. neither display: none
 * nor visibility: hidden).
 *
 * @param {Element} element DOM element to test.
 *
 * @return {boolean} Whether element is visible.
 */

function isVisible(element) {
  return element.offsetWidth > 0 || element.offsetHeight > 0 || element.getClientRects().length > 0;
}
/**
 * Returns true if the specified area element is a valid focusable element, or
 * false otherwise. Area is only focusable if within a map where a named map
 * referenced by an image somewhere in the document.
 *
 * @param {Element} element DOM area element to test.
 *
 * @return {boolean} Whether area element is valid for focus.
 */


function isValidFocusableArea(element) {
  var map = element.closest('map[name]');

  if (!map) {
    return false;
  }

  var img = document.querySelector('img[usemap="#' + map.name + '"]');
  return !!img && isVisible(img);
}
/**
 * Returns all focusable elements within a given context.
 *
 * @param {Element} context Element in which to search.
 *
 * @return {Element[]} Focusable elements.
 */


function find(context) {
  var elements = context.querySelectorAll(SELECTOR);
  return Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(elements).filter(function (element) {
    if (!isVisible(element)) {
      return false;
    }

    var nodeName = element.nodeName;

    if ('AREA' === nodeName) {
      return isValidFocusableArea(element);
    }

    return true;
  });
}


/***/ }),

/***/ "./node_modules/@wordpress/dom/build-module/index.js":
/*!***********************************************************!*\
  !*** ./node_modules/@wordpress/dom/build-module/index.js ***!
  \***********************************************************/
/*! exports provided: focus, isHorizontalEdge, isVerticalEdge, getRectangleFromRange, computeCaretRect, placeCaretAtHorizontalEdge, placeCaretAtVerticalEdge, isTextField, documentHasSelection, isEntirelySelected, getScrollContainer, getOffsetParent, replace, remove, insertAfter, unwrap, replaceTag */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "focus", function() { return focus; });
/* harmony import */ var _focusable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./focusable */ "./node_modules/@wordpress/dom/build-module/focusable.js");
/* harmony import */ var _tabbable__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./tabbable */ "./node_modules/@wordpress/dom/build-module/tabbable.js");
/* harmony import */ var _dom__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./dom */ "./node_modules/@wordpress/dom/build-module/dom.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isHorizontalEdge", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["isHorizontalEdge"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isVerticalEdge", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["isVerticalEdge"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getRectangleFromRange", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["getRectangleFromRange"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "computeCaretRect", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["computeCaretRect"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "placeCaretAtHorizontalEdge", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["placeCaretAtHorizontalEdge"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "placeCaretAtVerticalEdge", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["placeCaretAtVerticalEdge"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isTextField", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["isTextField"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "documentHasSelection", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["documentHasSelection"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEntirelySelected", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["isEntirelySelected"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getScrollContainer", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["getScrollContainer"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getOffsetParent", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["getOffsetParent"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "replace", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["replace"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "remove", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["remove"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "insertAfter", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["insertAfter"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unwrap", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["unwrap"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "replaceTag", function() { return _dom__WEBPACK_IMPORTED_MODULE_2__["replaceTag"]; });

/**
 * Internal dependencies
 */


var focus = {
  focusable: _focusable__WEBPACK_IMPORTED_MODULE_0__,
  tabbable: _tabbable__WEBPACK_IMPORTED_MODULE_1__
};



/***/ }),

/***/ "./node_modules/@wordpress/dom/build-module/tabbable.js":
/*!**************************************************************!*\
  !*** ./node_modules/@wordpress/dom/build-module/tabbable.js ***!
  \**************************************************************/
/*! exports provided: isTabbableIndex, find */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isTabbableIndex", function() { return isTabbableIndex; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "find", function() { return find; });
/* harmony import */ var _focusable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./focusable */ "./node_modules/@wordpress/dom/build-module/focusable.js");
/**
 * Internal dependencies
 */

/**
 * Returns the tab index of the given element. In contrast with the tabIndex
 * property, this normalizes the default (0) to avoid browser inconsistencies,
 * operating under the assumption that this function is only ever called with a
 * focusable node.
 *
 * @see https://bugzilla.mozilla.org/show_bug.cgi?id=1190261
 *
 * @param {Element} element Element from which to retrieve.
 *
 * @return {?number} Tab index of element (default 0).
 */

function getTabIndex(element) {
  var tabIndex = element.getAttribute('tabindex');
  return tabIndex === null ? 0 : parseInt(tabIndex, 10);
}
/**
 * Returns true if the specified element is tabbable, or false otherwise.
 *
 * @param {Element} element Element to test.
 *
 * @return {boolean} Whether element is tabbable.
 */


function isTabbableIndex(element) {
  return getTabIndex(element) !== -1;
}
/**
 * An array map callback, returning an object with the element value and its
 * array index location as properties. This is used to emulate a proper stable
 * sort where equal tabIndex should be left in order of their occurrence in the
 * document.
 *
 * @param {Element} element Element.
 * @param {number}  index   Array index of element.
 *
 * @return {Object} Mapped object with element, index.
 */

function mapElementToObjectTabbable(element, index) {
  return {
    element: element,
    index: index
  };
}
/**
 * An array map callback, returning an element of the given mapped object's
 * element value.
 *
 * @param {Object} object Mapped object with index.
 *
 * @return {Element} Mapped object element.
 */


function mapObjectTabbableToElement(object) {
  return object.element;
}
/**
 * A sort comparator function used in comparing two objects of mapped elements.
 *
 * @see mapElementToObjectTabbable
 *
 * @param {Object} a First object to compare.
 * @param {Object} b Second object to compare.
 *
 * @return {number} Comparator result.
 */


function compareObjectTabbables(a, b) {
  var aTabIndex = getTabIndex(a.element);
  var bTabIndex = getTabIndex(b.element);

  if (aTabIndex === bTabIndex) {
    return a.index - b.index;
  }

  return aTabIndex - bTabIndex;
}

function find(context) {
  return Object(_focusable__WEBPACK_IMPORTED_MODULE_0__["find"])(context).filter(isTabbableIndex).map(mapElementToObjectTabbable).sort(compareObjectTabbables).map(mapObjectTabbableToElement);
}


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
//# sourceMappingURL=dom.js.map