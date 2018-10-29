this["wp"] = this["wp"] || {}; this["wp"]["blockSerializationDefaultParser"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/block-serialization-default-parser/build-module/index.js");
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

/***/ "./node_modules/@wordpress/block-serialization-default-parser/build-module/index.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/@wordpress/block-serialization-default-parser/build-module/index.js ***!
  \******************************************************************************************/
/*! exports provided: parse */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "parse", function() { return parse; });
/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/slicedToArray */ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js");

var document;
var offset;
var output;
var stack;
var tokenizer = /<!--\s+(\/)?wp:([a-z][a-z0-9_-]*\/)?([a-z][a-z0-9_-]*)\s+({(?:(?!}\s+-->)[^])+?}\s+)?(\/)?-->/g;

function Block(blockName, attrs, innerBlocks, innerHTML) {
  return {
    blockName: blockName,
    attrs: attrs,
    innerBlocks: innerBlocks,
    innerHTML: innerHTML
  };
}

function Freeform(innerHTML) {
  return Block(null, {}, [], innerHTML);
}

function Frame(block, tokenStart, tokenLength, prevOffset, leadingHtmlStart) {
  return {
    block: block,
    tokenStart: tokenStart,
    tokenLength: tokenLength,
    prevOffset: prevOffset || tokenStart + tokenLength,
    leadingHtmlStart: leadingHtmlStart
  };
}

var parse = function parse(doc) {
  document = doc;
  offset = 0;
  output = [];
  stack = [];
  tokenizer.lastIndex = 0;

  do {// twiddle our thumbs
  } while (proceed());

  return output;
};

function proceed() {
  var next = nextToken();

  var _next = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__["default"])(next, 5),
      tokenType = _next[0],
      blockName = _next[1],
      attrs = _next[2],
      startOffset = _next[3],
      tokenLength = _next[4];

  var stackDepth = stack.length; // we may have some HTML soup before the next block

  var leadingHtmlStart = startOffset > offset ? offset : null;

  switch (tokenType) {
    case 'no-more-tokens':
      // if not in a block then flush output
      if (0 === stackDepth) {
        addFreeform();
        return false;
      } // Otherwise we have a problem
      // This is an error
      // we have options
      //  - treat it all as freeform text
      //  - assume an implicit closer (easiest when not nesting)
      // for the easy case we'll assume an implicit closer


      if (1 === stackDepth) {
        addBlockFromStack();
        return false;
      } // for the nested case where it's more difficult we'll
      // have to assume that multiple closers are missing
      // and so we'll collapse the whole stack piecewise


      while (0 < stack.length) {
        addBlockFromStack();
      }

      return false;

    case 'void-block':
      // easy case is if we stumbled upon a void block
      // in the top-level of the document
      if (0 === stackDepth) {
        if (null !== leadingHtmlStart) {
          output.push(Freeform(document.substr(leadingHtmlStart, startOffset - leadingHtmlStart)));
        }

        output.push(Block(blockName, attrs, [], ''));
        offset = startOffset + tokenLength;
        return true;
      } // otherwise we found an inner block


      addInnerBlock(Block(blockName, attrs, [], ''), startOffset, tokenLength);
      offset = startOffset + tokenLength;
      return true;

    case 'block-opener':
      // track all newly-opened blocks on the stack
      stack.push(Frame(Block(blockName, attrs, [], ''), startOffset, tokenLength, startOffset + tokenLength, leadingHtmlStart));
      offset = startOffset + tokenLength;
      return true;

    case 'block-closer':
      // if we're missing an opener we're in trouble
      // This is an error
      if (0 === stackDepth) {
        // we have options
        //  - assume an implicit opener
        //  - assume _this_ is the opener
        //  - give up and close out the document
        addFreeform();
        return false;
      } // if we're not nesting then this is easy - close the block


      if (1 === stackDepth) {
        addBlockFromStack(startOffset);
        offset = startOffset + tokenLength;
        return true;
      } // otherwise we're nested and we have to close out the current
      // block and add it as a innerBlock to the parent


      var stackTop = stack.pop();
      stackTop.block.innerHTML += document.substr(stackTop.prevOffset, startOffset - stackTop.prevOffset);
      stackTop.prevOffset = startOffset + tokenLength;
      addInnerBlock(stackTop.block, stackTop.tokenStart, stackTop.tokenLength, startOffset + tokenLength);
      offset = startOffset + tokenLength;
      return true;

    default:
      // This is an error
      addFreeform();
      return false;
  }
}
/**
 * Parse JSON if valid, otherwise return null
 *
 * Note that JSON coming from the block comment
 * delimiters is constrained to be an object
 * and cannot be things like `true` or `null`
 *
 * @param {string} input JSON input string to parse
 * @return {Object|null} parsed JSON if valid
 */


function parseJSON(input) {
  try {
    return JSON.parse(input);
  } catch (e) {
    return null;
  }
}

function nextToken() {
  // aye the magic
  // we're using a single RegExp to tokenize the block comment delimiters
  // we're also using a trick here because the only difference between a
  // block opener and a block closer is the leading `/` before `wp:` (and
  // a closer has no attributes). we can trap them both and process the
  // match back in Javascript to see which one it was.
  var matches = tokenizer.exec(document); // we have no more tokens

  if (null === matches) {
    return ['no-more-tokens'];
  }

  var startedAt = matches.index;

  var _matches = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__["default"])(matches, 6),
      match = _matches[0],
      closerMatch = _matches[1],
      namespaceMatch = _matches[2],
      nameMatch = _matches[3],
      attrsMatch = _matches[4],
      voidMatch = _matches[5];

  var length = match.length;
  var isCloser = !!closerMatch;
  var isVoid = !!voidMatch;
  var namespace = namespaceMatch || 'core/';
  var name = namespace + nameMatch;
  var hasAttrs = !!attrsMatch;
  var attrs = hasAttrs ? parseJSON(attrsMatch) : {}; // This state isn't allowed
  // This is an error

  if (isCloser && (isVoid || hasAttrs)) {// we can ignore them since they don't hurt anything
    // we may warn against this at some point or reject it
  }

  if (isVoid) {
    return ['void-block', name, attrs, startedAt, length];
  }

  if (isCloser) {
    return ['block-closer', name, null, startedAt, length];
  }

  return ['block-opener', name, attrs, startedAt, length];
}

function addFreeform(rawLength) {
  var length = rawLength ? rawLength : document.length - offset;

  if (0 === length) {
    return;
  }

  output.push(Freeform(document.substr(offset, length)));
}

function addInnerBlock(block, tokenStart, tokenLength, lastOffset) {
  var parent = stack[stack.length - 1];
  parent.block.innerBlocks.push(block);
  parent.block.innerHTML += document.substr(parent.prevOffset, tokenStart - parent.prevOffset);
  parent.prevOffset = lastOffset ? lastOffset : tokenStart + tokenLength;
}

function addBlockFromStack(endOffset) {
  var _stack$pop = stack.pop(),
      block = _stack$pop.block,
      leadingHtmlStart = _stack$pop.leadingHtmlStart,
      prevOffset = _stack$pop.prevOffset,
      tokenStart = _stack$pop.tokenStart;

  if (endOffset) {
    block.innerHTML += document.substr(prevOffset, endOffset - prevOffset);
  } else {
    block.innerHTML += document.substr(prevOffset);
  }

  if (null !== leadingHtmlStart) {
    output.push(Freeform(document.substr(leadingHtmlStart, tokenStart - leadingHtmlStart)));
  }

  output.push(block);
}


/***/ })

/******/ });
//# sourceMappingURL=block-serialization-default-parser.js.map