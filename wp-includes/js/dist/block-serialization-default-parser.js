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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _arrayWithHoles; });\nfunction _arrayWithHoles(arr) {\n  if (Array.isArray(arr)) return arr;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _iterableToArrayLimit; });\nfunction _iterableToArrayLimit(arr, i) {\n  var _arr = [];\n  var _n = true;\n  var _d = false;\n  var _e = undefined;\n\n  try {\n    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {\n      _arr.push(_s.value);\n\n      if (i && _arr.length === i) break;\n    }\n  } catch (err) {\n    _d = true;\n    _e = err;\n  } finally {\n    try {\n      if (!_n && _i[\"return\"] != null) _i[\"return\"]();\n    } finally {\n      if (_d) throw _e;\n    }\n  }\n\n  return _arr;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _nonIterableRest; });\nfunction _nonIterableRest() {\n  throw new TypeError(\"Invalid attempt to destructure non-iterable instance\");\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _slicedToArray; });\n/* harmony import */ var _arrayWithHoles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithHoles */ \"./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js\");\n/* harmony import */ var _iterableToArrayLimit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArrayLimit */ \"./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js\");\n/* harmony import */ var _nonIterableRest__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./nonIterableRest */ \"./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js\");\n\n\n\nfunction _slicedToArray(arr, i) {\n  return Object(_arrayWithHoles__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(arr) || Object(_iterableToArrayLimit__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(arr, i) || Object(_nonIterableRest__WEBPACK_IMPORTED_MODULE_2__[\"default\"])();\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/slicedToArray.js?");

/***/ }),

/***/ "./node_modules/@wordpress/block-serialization-default-parser/build-module/index.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/@wordpress/block-serialization-default-parser/build-module/index.js ***!
  \******************************************************************************************/
/*! exports provided: parse */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"parse\", function() { return parse; });\n/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/slicedToArray */ \"./node_modules/@babel/runtime/helpers/esm/slicedToArray.js\");\n\nvar document;\nvar offset;\nvar output;\nvar stack;\nvar tokenizer = /<!--\\s+(\\/)?wp:([a-z][a-z0-9_-]*\\/)?([a-z][a-z0-9_-]*)\\s+({(?:[^}]+|}+(?=})|(?!}\\s+-->)[^])*?}\\s+)?(\\/)?-->/g;\n\nfunction Block(blockName, attrs, innerBlocks, innerHTML, innerContent) {\n  return {\n    blockName: blockName,\n    attrs: attrs,\n    innerBlocks: innerBlocks,\n    innerHTML: innerHTML,\n    innerContent: innerContent\n  };\n}\n\nfunction Freeform(innerHTML) {\n  return Block(null, {}, [], innerHTML, [innerHTML]);\n}\n\nfunction Frame(block, tokenStart, tokenLength, prevOffset, leadingHtmlStart) {\n  return {\n    block: block,\n    tokenStart: tokenStart,\n    tokenLength: tokenLength,\n    prevOffset: prevOffset || tokenStart + tokenLength,\n    leadingHtmlStart: leadingHtmlStart\n  };\n}\n\nvar parse = function parse(doc) {\n  document = doc;\n  offset = 0;\n  output = [];\n  stack = [];\n  tokenizer.lastIndex = 0;\n\n  do {// twiddle our thumbs\n  } while (proceed());\n\n  return output;\n};\n\nfunction proceed() {\n  var next = nextToken();\n\n  var _next = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(next, 5),\n      tokenType = _next[0],\n      blockName = _next[1],\n      attrs = _next[2],\n      startOffset = _next[3],\n      tokenLength = _next[4];\n\n  var stackDepth = stack.length; // we may have some HTML soup before the next block\n\n  var leadingHtmlStart = startOffset > offset ? offset : null;\n\n  switch (tokenType) {\n    case 'no-more-tokens':\n      // if not in a block then flush output\n      if (0 === stackDepth) {\n        addFreeform();\n        return false;\n      } // Otherwise we have a problem\n      // This is an error\n      // we have options\n      //  - treat it all as freeform text\n      //  - assume an implicit closer (easiest when not nesting)\n      // for the easy case we'll assume an implicit closer\n\n\n      if (1 === stackDepth) {\n        addBlockFromStack();\n        return false;\n      } // for the nested case where it's more difficult we'll\n      // have to assume that multiple closers are missing\n      // and so we'll collapse the whole stack piecewise\n\n\n      while (0 < stack.length) {\n        addBlockFromStack();\n      }\n\n      return false;\n\n    case 'void-block':\n      // easy case is if we stumbled upon a void block\n      // in the top-level of the document\n      if (0 === stackDepth) {\n        if (null !== leadingHtmlStart) {\n          output.push(Freeform(document.substr(leadingHtmlStart, startOffset - leadingHtmlStart)));\n        }\n\n        output.push(Block(blockName, attrs, [], '', []));\n        offset = startOffset + tokenLength;\n        return true;\n      } // otherwise we found an inner block\n\n\n      addInnerBlock(Block(blockName, attrs, [], '', []), startOffset, tokenLength);\n      offset = startOffset + tokenLength;\n      return true;\n\n    case 'block-opener':\n      // track all newly-opened blocks on the stack\n      stack.push(Frame(Block(blockName, attrs, [], '', []), startOffset, tokenLength, startOffset + tokenLength, leadingHtmlStart));\n      offset = startOffset + tokenLength;\n      return true;\n\n    case 'block-closer':\n      // if we're missing an opener we're in trouble\n      // This is an error\n      if (0 === stackDepth) {\n        // we have options\n        //  - assume an implicit opener\n        //  - assume _this_ is the opener\n        //  - give up and close out the document\n        addFreeform();\n        return false;\n      } // if we're not nesting then this is easy - close the block\n\n\n      if (1 === stackDepth) {\n        addBlockFromStack(startOffset);\n        offset = startOffset + tokenLength;\n        return true;\n      } // otherwise we're nested and we have to close out the current\n      // block and add it as a innerBlock to the parent\n\n\n      var stackTop = stack.pop();\n      var html = document.substr(stackTop.prevOffset, startOffset - stackTop.prevOffset);\n      stackTop.block.innerHTML += html;\n      stackTop.block.innerContent.push(html);\n      stackTop.prevOffset = startOffset + tokenLength;\n      addInnerBlock(stackTop.block, stackTop.tokenStart, stackTop.tokenLength, startOffset + tokenLength);\n      offset = startOffset + tokenLength;\n      return true;\n\n    default:\n      // This is an error\n      addFreeform();\n      return false;\n  }\n}\n/**\n * Parse JSON if valid, otherwise return null\n *\n * Note that JSON coming from the block comment\n * delimiters is constrained to be an object\n * and cannot be things like `true` or `null`\n *\n * @param {string} input JSON input string to parse\n * @return {Object|null} parsed JSON if valid\n */\n\n\nfunction parseJSON(input) {\n  try {\n    return JSON.parse(input);\n  } catch (e) {\n    return null;\n  }\n}\n\nfunction nextToken() {\n  // aye the magic\n  // we're using a single RegExp to tokenize the block comment delimiters\n  // we're also using a trick here because the only difference between a\n  // block opener and a block closer is the leading `/` before `wp:` (and\n  // a closer has no attributes). we can trap them both and process the\n  // match back in Javascript to see which one it was.\n  var matches = tokenizer.exec(document); // we have no more tokens\n\n  if (null === matches) {\n    return ['no-more-tokens'];\n  }\n\n  var startedAt = matches.index;\n\n  var _matches = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(matches, 6),\n      match = _matches[0],\n      closerMatch = _matches[1],\n      namespaceMatch = _matches[2],\n      nameMatch = _matches[3],\n      attrsMatch = _matches[4],\n      voidMatch = _matches[5];\n\n  var length = match.length;\n  var isCloser = !!closerMatch;\n  var isVoid = !!voidMatch;\n  var namespace = namespaceMatch || 'core/';\n  var name = namespace + nameMatch;\n  var hasAttrs = !!attrsMatch;\n  var attrs = hasAttrs ? parseJSON(attrsMatch) : {}; // This state isn't allowed\n  // This is an error\n\n  if (isCloser && (isVoid || hasAttrs)) {// we can ignore them since they don't hurt anything\n    // we may warn against this at some point or reject it\n  }\n\n  if (isVoid) {\n    return ['void-block', name, attrs, startedAt, length];\n  }\n\n  if (isCloser) {\n    return ['block-closer', name, null, startedAt, length];\n  }\n\n  return ['block-opener', name, attrs, startedAt, length];\n}\n\nfunction addFreeform(rawLength) {\n  var length = rawLength ? rawLength : document.length - offset;\n\n  if (0 === length) {\n    return;\n  }\n\n  output.push(Freeform(document.substr(offset, length)));\n}\n\nfunction addInnerBlock(block, tokenStart, tokenLength, lastOffset) {\n  var parent = stack[stack.length - 1];\n  parent.block.innerBlocks.push(block);\n  var html = document.substr(parent.prevOffset, tokenStart - parent.prevOffset);\n\n  if (html) {\n    parent.block.innerHTML += html;\n    parent.block.innerContent.push(html);\n  }\n\n  parent.block.innerContent.push(null);\n  parent.prevOffset = lastOffset ? lastOffset : tokenStart + tokenLength;\n}\n\nfunction addBlockFromStack(endOffset) {\n  var _stack$pop = stack.pop(),\n      block = _stack$pop.block,\n      leadingHtmlStart = _stack$pop.leadingHtmlStart,\n      prevOffset = _stack$pop.prevOffset,\n      tokenStart = _stack$pop.tokenStart;\n\n  var html = endOffset ? document.substr(prevOffset, endOffset - prevOffset) : document.substr(prevOffset);\n\n  if (html) {\n    block.innerHTML += html;\n    block.innerContent.push(html);\n  }\n\n  if (null !== leadingHtmlStart) {\n    output.push(Freeform(document.substr(leadingHtmlStart, tokenStart - leadingHtmlStart)));\n  }\n\n  output.push(block);\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/block-serialization-default-parser/build-module/index.js?");

/***/ })

/******/ });