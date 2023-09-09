<<<<<<< HEAD
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   parse: () => (/* binding */ parse)
/* harmony export */ });
/**
 * @type {string}
 */
let document;
/**
 * @type {number}
 */

let offset;
/**
 * @type {ParsedBlock[]}
 */

let output;
/**
 * @type {ParsedFrame[]}
 */

let stack;
/**
 * @typedef {Object|null} Attributes
 */

/**
 * @typedef {Object} ParsedBlock
 * @property {string|null}        blockName    Block name.
 * @property {Attributes}         attrs        Block attributes.
 * @property {ParsedBlock[]}      innerBlocks  Inner blocks.
 * @property {string}             innerHTML    Inner HTML.
 * @property {Array<string|null>} innerContent Inner content.
 */

/**
 * @typedef {Object} ParsedFrame
 * @property {ParsedBlock} block            Block.
 * @property {number}      tokenStart       Token start.
 * @property {number}      tokenLength      Token length.
 * @property {number}      prevOffset       Previous offset.
 * @property {number|null} leadingHtmlStart Leading HTML start.
 */

/**
 * @typedef {'no-more-tokens'|'void-block'|'block-opener'|'block-closer'} TokenType
 */

/**
 * @typedef {[TokenType, string, Attributes, number, number]} Token
 */

=======
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
/******/ 	return __webpack_require__(__webpack_require__.s = "SiJt");
/******/ })
/************************************************************************/
/******/ ({

/***/ "BsWD":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a3WO");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ "DSFK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ "ODXe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _slicedToArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__("DSFK");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
  if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
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
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__("BsWD");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__("PYwp");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(unsupportedIterableToArray["a" /* default */])(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ "PYwp":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ "SiJt":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "parse", function() { return parse; });
/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ODXe");

var document;
var offset;
var output;
var stack;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Matches block comment delimiters
 *
 * While most of this pattern is straightforward the attribute parsing
 * incorporates a tricks to make sure we don't choke on specific input
 *
 *  - since JavaScript has no possessive quantifier or atomic grouping
 *    we are emulating it with a trick
 *
 *    we want a possessive quantifier or atomic group to prevent backtracking
 *    on the `}`s should we fail to match the remainder of the pattern
 *
 *    we can emulate this with a positive lookahead and back reference
 *    (a++)*c === ((?=(a+))\1)*c
 *
 *    let's examine an example:
 *      - /(a+)*c/.test('aaaaaaaaaaaaad') fails after over 49,000 steps
 *      - /(a++)*c/.test('aaaaaaaaaaaaad') fails after 85 steps
 *      - /(?>a+)*c/.test('aaaaaaaaaaaaad') fails after 126 steps
 *
 *    this is because the possessive `++` and the atomic group `(?>)`
 *    tell the engine that all those `a`s belong together as a single group
 *    and so it won't split it up when stepping backwards to try and match
 *
 *    if we use /((?=(a+))\1)*c/ then we get the same behavior as the atomic group
 *    or possessive and prevent the backtracking because the `a+` is matched but
 *    not captured. thus, we find the long string of `a`s and remember it, then
 *    reference it as a whole unit inside our pattern
 *
<<<<<<< HEAD
 *    @see http://instanceof.me/post/52245507631/regex-emulate-atomic-grouping-with-lookahead
 *    @see http://blog.stevenlevithan.com/archives/mimic-atomic-groups
 *    @see https://javascript.info/regexp-infinite-backtracking-problem
=======
 *    @cite http://instanceof.me/post/52245507631/regex-emulate-atomic-grouping-with-lookahead
 *    @cite http://blog.stevenlevithan.com/archives/mimic-atomic-groups
 *    @cite https://javascript.info/regexp-infinite-backtracking-problem
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 *    once browsers reliably support atomic grouping or possessive
 *    quantifiers natively we should remove this trick and simplify
 *
<<<<<<< HEAD
 * @type {RegExp}
=======
 * @type RegExp
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @since 3.8.0
 * @since 4.6.1 added optimization to prevent backtracking on attribute parsing
 */

<<<<<<< HEAD
const tokenizer = /<!--\s+(\/)?wp:([a-z][a-z0-9_-]*\/)?([a-z][a-z0-9_-]*)\s+({(?:(?=([^}]+|}+(?=})|(?!}\s+\/?-->)[^])*)\5|[^]*?)}\s+)?(\/)?-->/g;
/**
 * Constructs a block object.
 *
 * @param {string|null}   blockName
 * @param {Attributes}    attrs
 * @param {ParsedBlock[]} innerBlocks
 * @param {string}        innerHTML
 * @param {string[]}      innerContent
 * @return {ParsedBlock} The block object.
 */

function Block(blockName, attrs, innerBlocks, innerHTML, innerContent) {
  return {
    blockName,
    attrs,
    innerBlocks,
    innerHTML,
    innerContent
  };
}
/**
 * Constructs a freeform block object.
 *
 * @param {string} innerHTML
 * @return {ParsedBlock} The freeform block object.
 */

=======
var tokenizer = /<!--\s+(\/)?wp:([a-z][a-z0-9_-]*\/)?([a-z][a-z0-9_-]*)\s+({(?:(?=([^}]+|}+(?=})|(?!}\s+\/?-->)[^])*)\5|[^]*?)}\s+)?(\/)?-->/g;

function Block(blockName, attrs, innerBlocks, innerHTML, innerContent) {
  return {
    blockName: blockName,
    attrs: attrs,
    innerBlocks: innerBlocks,
    innerHTML: innerHTML,
    innerContent: innerContent
  };
}
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

function Freeform(innerHTML) {
  return Block(null, {}, [], innerHTML, [innerHTML]);
}
<<<<<<< HEAD
/**
 * Constructs a frame object.
 *
 * @param {ParsedBlock} block
 * @param {number}      tokenStart
 * @param {number}      tokenLength
 * @param {number}      prevOffset
 * @param {number|null} leadingHtmlStart
 * @return {ParsedFrame} The frame object.
 */


function Frame(block, tokenStart, tokenLength, prevOffset, leadingHtmlStart) {
  return {
    block,
    tokenStart,
    tokenLength,
    prevOffset: prevOffset || tokenStart + tokenLength,
    leadingHtmlStart
  };
}
/**
 * Parser function, that converts input HTML into a block based structure.
 *
 * @param {string} doc The HTML document to parse.
 *
 * @example
 * Input post:
 * ```html
 * <!-- wp:columns {"columns":3} -->
 * <div class="wp-block-columns has-3-columns"><!-- wp:column -->
 * <div class="wp-block-column"><!-- wp:paragraph -->
 * <p>Left</p>
 * <!-- /wp:paragraph --></div>
 * <!-- /wp:column -->
 *
 * <!-- wp:column -->
 * <div class="wp-block-column"><!-- wp:paragraph -->
 * <p><strong>Middle</strong></p>
 * <!-- /wp:paragraph --></div>
 * <!-- /wp:column -->
 *
 * <!-- wp:column -->
 * <div class="wp-block-column"></div>
 * <!-- /wp:column --></div>
 * <!-- /wp:columns -->
 * ```
 *
 * Parsing code:
 * ```js
 * import { parse } from '@wordpress/block-serialization-default-parser';
 *
 * parse( post ) === [
 *     {
 *         blockName: "core/columns",
 *         attrs: {
 *             columns: 3
 *         },
 *         innerBlocks: [
 *             {
 *                 blockName: "core/column",
 *                 attrs: null,
 *                 innerBlocks: [
 *                     {
 *                         blockName: "core/paragraph",
 *                         attrs: null,
 *                         innerBlocks: [],
 *                         innerHTML: "\n<p>Left</p>\n"
 *                     }
 *                 ],
 *                 innerHTML: '\n<div class="wp-block-column"></div>\n'
 *             },
 *             {
 *                 blockName: "core/column",
 *                 attrs: null,
 *                 innerBlocks: [
 *                     {
 *                         blockName: "core/paragraph",
 *                         attrs: null,
 *                         innerBlocks: [],
 *                         innerHTML: "\n<p><strong>Middle</strong></p>\n"
 *                     }
 *                 ],
 *                 innerHTML: '\n<div class="wp-block-column"></div>\n'
 *             },
 *             {
 *                 blockName: "core/column",
 *                 attrs: null,
 *                 innerBlocks: [],
 *                 innerHTML: '\n<div class="wp-block-column"></div>\n'
 *             }
 *         ],
 *         innerHTML: '\n<div class="wp-block-columns has-3-columns">\n\n\n\n</div>\n'
 *     }
 * ];
 * ```
 * @return {ParsedBlock[]} A block-based representation of the input HTML.
 */


const parse = doc => {
=======

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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  document = doc;
  offset = 0;
  output = [];
  stack = [];
  tokenizer.lastIndex = 0;

  do {// twiddle our thumbs
  } while (proceed());

  return output;
};
<<<<<<< HEAD
/**
 * Parses the next token in the input document.
 *
 * @return {boolean} Returns true when there is more tokens to parse.
 */

function proceed() {
  const stackDepth = stack.length;
  const next = nextToken();
  const [tokenType, blockName, attrs, startOffset, tokenLength] = next; // We may have some HTML soup before the next block.

  const leadingHtmlStart = startOffset > offset ? offset : null;

  switch (tokenType) {
    case 'no-more-tokens':
      // If not in a block then flush output.
=======

function proceed() {
  var next = nextToken();

  var _next = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(next, 5),
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      if (0 === stackDepth) {
        addFreeform();
        return false;
      } // Otherwise we have a problem
      // This is an error
      // we have options
      //  - treat it all as freeform text
      //  - assume an implicit closer (easiest when not nesting)
<<<<<<< HEAD
      // For the easy case we'll assume an implicit closer.
=======
      // for the easy case we'll assume an implicit closer
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


      if (1 === stackDepth) {
        addBlockFromStack();
        return false;
<<<<<<< HEAD
      } // For the nested case where it's more difficult we'll
      // have to assume that multiple closers are missing
      // and so we'll collapse the whole stack piecewise.
=======
      } // for the nested case where it's more difficult we'll
      // have to assume that multiple closers are missing
      // and so we'll collapse the whole stack piecewise
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


      while (0 < stack.length) {
        addBlockFromStack();
      }

      return false;

    case 'void-block':
      // easy case is if we stumbled upon a void block
<<<<<<< HEAD
      // in the top-level of the document.
=======
      // in the top-level of the document
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      if (0 === stackDepth) {
        if (null !== leadingHtmlStart) {
          output.push(Freeform(document.substr(leadingHtmlStart, startOffset - leadingHtmlStart)));
        }

        output.push(Block(blockName, attrs, [], '', []));
        offset = startOffset + tokenLength;
        return true;
<<<<<<< HEAD
      } // Otherwise we found an inner block.
=======
      } // otherwise we found an inner block
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


      addInnerBlock(Block(blockName, attrs, [], '', []), startOffset, tokenLength);
      offset = startOffset + tokenLength;
      return true;

    case 'block-opener':
<<<<<<< HEAD
      // Track all newly-opened blocks on the stack.
=======
      // track all newly-opened blocks on the stack
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      stack.push(Frame(Block(blockName, attrs, [], '', []), startOffset, tokenLength, startOffset + tokenLength, leadingHtmlStart));
      offset = startOffset + tokenLength;
      return true;

    case 'block-closer':
<<<<<<< HEAD
      // If we're missing an opener we're in trouble
      // This is an error.
      if (0 === stackDepth) {
        // We have options
        //  - assume an implicit opener
        //  - assume _this_ is the opener
        // - give up and close out the document.
        addFreeform();
        return false;
      } // If we're not nesting then this is easy - close the block.
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


      if (1 === stackDepth) {
        addBlockFromStack(startOffset);
        offset = startOffset + tokenLength;
        return true;
<<<<<<< HEAD
      } // Otherwise we're nested and we have to close out the current
      // block and add it as a innerBlock to the parent.


      const stackTop =
      /** @type {ParsedFrame} */
      stack.pop();
      const html = document.substr(stackTop.prevOffset, startOffset - stackTop.prevOffset);
=======
      } // otherwise we're nested and we have to close out the current
      // block and add it as a innerBlock to the parent


      var stackTop = stack.pop();
      var html = document.substr(stackTop.prevOffset, startOffset - stackTop.prevOffset);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      stackTop.block.innerHTML += html;
      stackTop.block.innerContent.push(html);
      stackTop.prevOffset = startOffset + tokenLength;
      addInnerBlock(stackTop.block, stackTop.tokenStart, stackTop.tokenLength, startOffset + tokenLength);
      offset = startOffset + tokenLength;
      return true;

    default:
<<<<<<< HEAD
      // This is an error.
=======
      // This is an error
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
/**
 * Finds the next token in the document.
 *
 * @return {Token} The next matched token.
 */


function nextToken() {
  // Aye the magic
=======

function nextToken() {
  // aye the magic
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  // we're using a single RegExp to tokenize the block comment delimiters
  // we're also using a trick here because the only difference between a
  // block opener and a block closer is the leading `/` before `wp:` (and
  // a closer has no attributes). we can trap them both and process the
<<<<<<< HEAD
  // match back in JavaScript to see which one it was.
  const matches = tokenizer.exec(document); // We have no more tokens.

  if (null === matches) {
    return ['no-more-tokens', '', null, 0, 0];
  }

  const startedAt = matches.index;
  const [match, closerMatch, namespaceMatch, nameMatch, attrsMatch
  /* Internal/unused. */
  ,, voidMatch] = matches;
  const length = match.length;
  const isCloser = !!closerMatch;
  const isVoid = !!voidMatch;
  const namespace = namespaceMatch || 'core/';
  const name = namespace + nameMatch;
  const hasAttrs = !!attrsMatch;
  const attrs = hasAttrs ? parseJSON(attrsMatch) : {}; // This state isn't allowed
  // This is an error.

  if (isCloser && (isVoid || hasAttrs)) {// We can ignore them since they don't hurt anything
    // we may warn against this at some point or reject it.
=======
  // match back in Javascript to see which one it was.
  var matches = tokenizer.exec(document); // we have no more tokens

  if (null === matches) {
    return ['no-more-tokens'];
  }

  var startedAt = matches.index;

  var _matches = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(matches, 7),
      match = _matches[0],
      closerMatch = _matches[1],
      namespaceMatch = _matches[2],
      nameMatch = _matches[3],
      attrsMatch = _matches[4],

  /* internal/unused */
  voidMatch = _matches[6];

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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }

  if (isVoid) {
    return ['void-block', name, attrs, startedAt, length];
  }

  if (isCloser) {
    return ['block-closer', name, null, startedAt, length];
  }

  return ['block-opener', name, attrs, startedAt, length];
}
<<<<<<< HEAD
/**
 * Adds a freeform block to the output.
 *
 * @param {number} [rawLength]
 */


function addFreeform(rawLength) {
  const length = rawLength ? rawLength : document.length - offset;
=======

function addFreeform(rawLength) {
  var length = rawLength ? rawLength : document.length - offset;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (0 === length) {
    return;
  }

  output.push(Freeform(document.substr(offset, length)));
}
<<<<<<< HEAD
/**
 * Adds inner block to the parent block.
 *
 * @param {ParsedBlock} block
 * @param {number}      tokenStart
 * @param {number}      tokenLength
 * @param {number}      [lastOffset]
 */


function addInnerBlock(block, tokenStart, tokenLength, lastOffset) {
  const parent = stack[stack.length - 1];
  parent.block.innerBlocks.push(block);
  const html = document.substr(parent.prevOffset, tokenStart - parent.prevOffset);
=======

function addInnerBlock(block, tokenStart, tokenLength, lastOffset) {
  var parent = stack[stack.length - 1];
  parent.block.innerBlocks.push(block);
  var html = document.substr(parent.prevOffset, tokenStart - parent.prevOffset);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (html) {
    parent.block.innerHTML += html;
    parent.block.innerContent.push(html);
  }

  parent.block.innerContent.push(null);
  parent.prevOffset = lastOffset ? lastOffset : tokenStart + tokenLength;
}
<<<<<<< HEAD
/**
 * Adds block from the stack to the output.
 *
 * @param {number} [endOffset]
 */


function addBlockFromStack(endOffset) {
  const {
    block,
    leadingHtmlStart,
    prevOffset,
    tokenStart
  } =
  /** @type {ParsedFrame} */
  stack.pop();
  const html = endOffset ? document.substr(prevOffset, endOffset - prevOffset) : document.substr(prevOffset);
=======

function addBlockFromStack(endOffset) {
  var _stack$pop = stack.pop(),
      block = _stack$pop.block,
      leadingHtmlStart = _stack$pop.leadingHtmlStart,
      prevOffset = _stack$pop.prevOffset,
      tokenStart = _stack$pop.tokenStart;

  var html = endOffset ? document.substr(prevOffset, endOffset - prevOffset) : document.substr(prevOffset);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (html) {
    block.innerHTML += html;
    block.innerContent.push(html);
  }

  if (null !== leadingHtmlStart) {
    output.push(Freeform(document.substr(leadingHtmlStart, tokenStart - leadingHtmlStart)));
  }

  output.push(block);
}

<<<<<<< HEAD
(window.wp = window.wp || {}).blockSerializationDefaultParser = __webpack_exports__;
/******/ })()
;
=======

/***/ }),

/***/ "a3WO":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayLikeToArray; });
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

/***/ })

/******/ });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
