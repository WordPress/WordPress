this["wp"] = this["wp"] || {}; this["wp"]["shortcode"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/shortcode/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@wordpress/shortcode/build-module/index.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@wordpress/shortcode/build-module/index.js ***!
  \*****************************************************************/
/*! exports provided: next, replace, string, regexp, attrs, fromMatch, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"next\", function() { return next; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"replace\", function() { return replace; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"string\", function() { return string; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"regexp\", function() { return regexp; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"attrs\", function() { return attrs; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"fromMatch\", function() { return fromMatch; });\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! memize */ \"./node_modules/memize/index.js\");\n/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(memize__WEBPACK_IMPORTED_MODULE_1__);\n/**\n * Internal dependencies\n */\n\n\n/**\n * Shortcode attributes object.\n *\n * @typedef {Object} WPShortcodeAttrs\n *\n * @property {Object} named   Object with named attributes.\n * @property {Array}  numeric Array with numeric attributes.\n */\n\n/**\n * Shortcode object.\n *\n * @typedef {Object} WPShortcode\n *\n * @property {string}           tag     Shortcode tag.\n * @property {WPShortcodeAttrs} attrs   Shortcode attributes.\n * @property {string}           content Shortcode content.\n * @property {string}           type    Shortcode type: `self-closing`,\n *                                      `closed`, or `single`.\n */\n\n/**\n * @typedef {Object} WPShortcodeMatch\n *\n * @property {number}      index     Index the shortcode is found at.\n * @property {string}      content   Matched content.\n * @property {WPShortcode} shortcode Shortcode instance of the match.\n */\n\n/**\n * Find the next matching shortcode.\n *\n * @param {string} tag   Shortcode tag.\n * @param {string} text  Text to search.\n * @param {number} index Index to start search from.\n *\n * @return {?WPShortcodeMatch} Matched information.\n */\n\nfunction next(tag, text) {\n  var index = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;\n  var re = regexp(tag);\n  re.lastIndex = index;\n  var match = re.exec(text);\n\n  if (!match) {\n    return;\n  } // If we matched an escaped shortcode, try again.\n\n\n  if ('[' === match[1] && ']' === match[7]) {\n    return next(tag, text, re.lastIndex);\n  }\n\n  var result = {\n    index: match.index,\n    content: match[0],\n    shortcode: fromMatch(match)\n  }; // If we matched a leading `[`, strip it from the match and increment the\n  // index accordingly.\n\n  if (match[1]) {\n    result.content = result.content.slice(1);\n    result.index++;\n  } // If we matched a trailing `]`, strip it from the match.\n\n\n  if (match[7]) {\n    result.content = result.content.slice(0, -1);\n  }\n\n  return result;\n}\n/**\n * Replace matching shortcodes in a block of text.\n *\n * @param {string}   tag      Shortcode tag.\n * @param {string}   text     Text to search.\n * @param {Function} callback Function to process the match and return\n *                            replacement string.\n *\n * @return {string} Text with shortcodes replaced.\n */\n\nfunction replace(tag, text, callback) {\n  var _arguments = arguments;\n  return text.replace(regexp(tag), function (match, left, $3, attrs, slash, content, closing, right) {\n    // If both extra brackets exist, the shortcode has been properly\n    // escaped.\n    if (left === '[' && right === ']') {\n      return match;\n    } // Create the match object and pass it through the callback.\n\n\n    var result = callback(fromMatch(_arguments)); // Make sure to return any of the extra brackets if they weren't used to\n    // escape the shortcode.\n\n    return result ? left + result + right : match;\n  });\n}\n/**\n * Generate a string from shortcode parameters.\n *\n * Creates a shortcode instance and returns a string.\n *\n * Accepts the same `options` as the `shortcode()` constructor, containing a\n * `tag` string, a string or object of `attrs`, a boolean indicating whether to\n * format the shortcode using a `single` tag, and a `content` string.\n *\n * @param {Object} options\n *\n * @return {string} String representation of the shortcode.\n */\n\nfunction string(options) {\n  return new shortcode(options).string();\n}\n/**\n * Generate a RegExp to identify a shortcode.\n *\n * The base regex is functionally equivalent to the one found in\n * `get_shortcode_regex()` in `wp-includes/shortcodes.php`.\n *\n * Capture groups:\n *\n * 1. An extra `[` to allow for escaping shortcodes with double `[[]]`\n * 2. The shortcode name\n * 3. The shortcode argument list\n * 4. The self closing `/`\n * 5. The content of a shortcode when it wraps some content.\n * 6. The closing tag.\n * 7. An extra `]` to allow for escaping shortcodes with double `[[]]`\n *\n * @param {string} tag Shortcode tag.\n *\n * @return {RegExp} Shortcode RegExp.\n */\n\nfunction regexp(tag) {\n  return new RegExp('\\\\[(\\\\[?)(' + tag + ')(?![\\\\w-])([^\\\\]\\\\/]*(?:\\\\/(?!\\\\])[^\\\\]\\\\/]*)*?)(?:(\\\\/)\\\\]|\\\\](?:([^\\\\[]*(?:\\\\[(?!\\\\/\\\\2\\\\])[^\\\\[]*)*)(\\\\[\\\\/\\\\2\\\\]))?)(\\\\]?)', 'g');\n}\n/**\n * Parse shortcode attributes.\n *\n * Shortcodes accept many types of attributes. These can chiefly be divided into\n * named and numeric attributes:\n *\n * Named attributes are assigned on a key/value basis, while numeric attributes\n * are treated as an array.\n *\n * Named attributes can be formatted as either `name=\"value\"`, `name='value'`,\n * or `name=value`. Numeric attributes can be formatted as `\"value\"` or just\n * `value`.\n *\n * @param {string} text Serialised shortcode attributes.\n *\n * @return {WPShortcodeAttrs} Parsed shortcode attributes.\n */\n\nvar attrs = memize__WEBPACK_IMPORTED_MODULE_1___default()(function (text) {\n  var named = {};\n  var numeric = []; // This regular expression is reused from `shortcode_parse_atts()` in\n  // `wp-includes/shortcodes.php`.\n  //\n  // Capture groups:\n  //\n  // 1. An attribute name, that corresponds to...\n  // 2. a value in double quotes.\n  // 3. An attribute name, that corresponds to...\n  // 4. a value in single quotes.\n  // 5. An attribute name, that corresponds to...\n  // 6. an unquoted value.\n  // 7. A numeric attribute in double quotes.\n  // 8. A numeric attribute in single quotes.\n  // 9. An unquoted numeric attribute.\n\n  var pattern = /([\\w-]+)\\s*=\\s*\"([^\"]*)\"(?:\\s|$)|([\\w-]+)\\s*=\\s*'([^']*)'(?:\\s|$)|([\\w-]+)\\s*=\\s*([^\\s'\"]+)(?:\\s|$)|\"([^\"]*)\"(?:\\s|$)|'([^']*)'(?:\\s|$)|(\\S+)(?:\\s|$)/g; // Map zero-width spaces to actual spaces.\n\n  text = text.replace(/[\\u00a0\\u200b]/g, ' ');\n  var match; // Match and normalize attributes.\n\n  while (match = pattern.exec(text)) {\n    if (match[1]) {\n      named[match[1].toLowerCase()] = match[2];\n    } else if (match[3]) {\n      named[match[3].toLowerCase()] = match[4];\n    } else if (match[5]) {\n      named[match[5].toLowerCase()] = match[6];\n    } else if (match[7]) {\n      numeric.push(match[7]);\n    } else if (match[8]) {\n      numeric.push(match[8]);\n    } else if (match[9]) {\n      numeric.push(match[9]);\n    }\n  }\n\n  return {\n    named: named,\n    numeric: numeric\n  };\n});\n/**\n * Generate a Shortcode Object from a RegExp match.\n *\n * Accepts a `match` object from calling `regexp.exec()` on a `RegExp` generated\n * by `regexp()`. `match` can also be set to the `arguments` from a callback\n * passed to `regexp.replace()`.\n *\n * @param {Array} match Match array.\n *\n * @return {WPShortcode} Shortcode instance.\n */\n\nfunction fromMatch(match) {\n  var type;\n\n  if (match[4]) {\n    type = 'self-closing';\n  } else if (match[6]) {\n    type = 'closed';\n  } else {\n    type = 'single';\n  }\n\n  return new shortcode({\n    tag: match[2],\n    attrs: match[3],\n    type: type,\n    content: match[5]\n  });\n}\n/**\n * Creates a shortcode instance.\n *\n * To access a raw representation of a shortcode, pass an `options` object,\n * containing a `tag` string, a string or object of `attrs`, a string indicating\n * the `type` of the shortcode ('single', 'self-closing', or 'closed'), and a\n * `content` string.\n *\n * @param {Object} options Options as described.\n *\n * @return {WPShortcode} Shortcode instance.\n */\n\nvar shortcode = Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"extend\"])(function (options) {\n  var _this = this;\n\n  Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"extend\"])(this, Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"pick\"])(options || {}, 'tag', 'attrs', 'type', 'content'));\n  var attributes = this.attrs; // Ensure we have a correctly formatted `attrs` object.\n\n  this.attrs = {\n    named: {},\n    numeric: []\n  };\n\n  if (!attributes) {\n    return;\n  } // Parse a string of attributes.\n\n\n  if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"isString\"])(attributes)) {\n    this.attrs = attrs(attributes); // Identify a correctly formatted `attrs` object.\n  } else if (Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"isEqual\"])(Object.keys(attributes), ['named', 'numeric'])) {\n    this.attrs = attributes; // Handle a flat object of attributes.\n  } else {\n    Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"forEach\"])(attributes, function (value, key) {\n      _this.set(key, value);\n    });\n  }\n}, {\n  next: next,\n  replace: replace,\n  string: string,\n  regexp: regexp,\n  attrs: attrs,\n  fromMatch: fromMatch\n});\nObject(lodash__WEBPACK_IMPORTED_MODULE_0__[\"extend\"])(shortcode.prototype, {\n  /**\n   * Get a shortcode attribute.\n   *\n   * Automatically detects whether `attr` is named or numeric and routes it\n   * accordingly.\n   *\n   * @param {(number|string)} attr Attribute key.\n   *\n   * @return {string} Attribute value.\n   */\n  get: function get(attr) {\n    return this.attrs[Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"isNumber\"])(attr) ? 'numeric' : 'named'][attr];\n  },\n\n  /**\n   * Set a shortcode attribute.\n   *\n   * Automatically detects whether `attr` is named or numeric and routes it\n   * accordingly.\n   *\n   * @param {(number|string)} attr  Attribute key.\n   * @param {string}          value Attribute value.\n   *\n   * @return {WPShortcode} Shortcode instance.\n   */\n  set: function set(attr, value) {\n    this.attrs[Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"isNumber\"])(attr) ? 'numeric' : 'named'][attr] = value;\n    return this;\n  },\n\n  /**\n   * Transform the shortcode into a string.\n   *\n   * @return {string} String representation of the shortcode.\n   */\n  string: function string() {\n    var text = '[' + this.tag;\n    Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"forEach\"])(this.attrs.numeric, function (value) {\n      if (/\\s/.test(value)) {\n        text += ' \"' + value + '\"';\n      } else {\n        text += ' ' + value;\n      }\n    });\n    Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"forEach\"])(this.attrs.named, function (value, name) {\n      text += ' ' + name + '=\"' + value + '\"';\n    }); // If the tag is marked as `single` or `self-closing`, close the tag and\n    // ignore any additional content.\n\n    if ('single' === this.type) {\n      return text + ']';\n    } else if ('self-closing' === this.type) {\n      return text + ' /]';\n    } // Complete the opening tag.\n\n\n    text += ']';\n\n    if (this.content) {\n      text += this.content;\n    } // Add the closing tag.\n\n\n    return text + '[/' + this.tag + ']';\n  }\n});\n/* harmony default export */ __webpack_exports__[\"default\"] = (shortcode);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/shortcode/build-module/index.js?");

/***/ }),

/***/ "./node_modules/memize/index.js":
/*!**************************************!*\
  !*** ./node_modules/memize/index.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = function memize( fn, options ) {\n\tvar size = 0,\n\t\tmaxSize, head, tail;\n\n\tif ( options && options.maxSize ) {\n\t\tmaxSize = options.maxSize;\n\t}\n\n\tfunction memoized( /* ...args */ ) {\n\t\tvar node = head,\n\t\t\tlen = arguments.length,\n\t\t\targs, i;\n\n\t\tsearchCache: while ( node ) {\n\t\t\t// Perform a shallow equality test to confirm that whether the node\n\t\t\t// under test is a candidate for the arguments passed. Two arrays\n\t\t\t// are shallowly equal if their length matches and each entry is\n\t\t\t// strictly equal between the two sets. Avoid abstracting to a\n\t\t\t// function which could incur an arguments leaking deoptimization.\n\n\t\t\t// Check whether node arguments match arguments length\n\t\t\tif ( node.args.length !== arguments.length ) {\n\t\t\t\tnode = node.next;\n\t\t\t\tcontinue;\n\t\t\t}\n\n\t\t\t// Check whether node arguments match arguments values\n\t\t\tfor ( i = 0; i < len; i++ ) {\n\t\t\t\tif ( node.args[ i ] !== arguments[ i ] ) {\n\t\t\t\t\tnode = node.next;\n\t\t\t\t\tcontinue searchCache;\n\t\t\t\t}\n\t\t\t}\n\n\t\t\t// At this point we can assume we've found a match\n\n\t\t\t// Surface matched node to head if not already\n\t\t\tif ( node !== head ) {\n\t\t\t\t// As tail, shift to previous. Must only shift if not also\n\t\t\t\t// head, since if both head and tail, there is no previous.\n\t\t\t\tif ( node === tail ) {\n\t\t\t\t\ttail = node.prev;\n\t\t\t\t}\n\n\t\t\t\t// Adjust siblings to point to each other. If node was tail,\n\t\t\t\t// this also handles new tail's empty `next` assignment.\n\t\t\t\tnode.prev.next = node.next;\n\t\t\t\tif ( node.next ) {\n\t\t\t\t\tnode.next.prev = node.prev;\n\t\t\t\t}\n\n\t\t\t\tnode.next = head;\n\t\t\t\tnode.prev = null;\n\t\t\t\thead.prev = node;\n\t\t\t\thead = node;\n\t\t\t}\n\n\t\t\t// Return immediately\n\t\t\treturn node.val;\n\t\t}\n\n\t\t// No cached value found. Continue to insertion phase:\n\n\t\t// Create a copy of arguments (avoid leaking deoptimization)\n\t\targs = new Array( len );\n\t\tfor ( i = 0; i < len; i++ ) {\n\t\t\targs[ i ] = arguments[ i ];\n\t\t}\n\n\t\tnode = {\n\t\t\targs: args,\n\n\t\t\t// Generate the result from original function\n\t\t\tval: fn.apply( null, args )\n\t\t};\n\n\t\t// Don't need to check whether node is already head, since it would\n\t\t// have been returned above already if it was\n\n\t\t// Shift existing head down list\n\t\tif ( head ) {\n\t\t\thead.prev = node;\n\t\t\tnode.next = head;\n\t\t} else {\n\t\t\t// If no head, follows that there's no tail (at initial or reset)\n\t\t\ttail = node;\n\t\t}\n\n\t\t// Trim tail if we're reached max size and are pending cache insertion\n\t\tif ( size === maxSize ) {\n\t\t\ttail = tail.prev;\n\t\t\ttail.next = null;\n\t\t} else {\n\t\t\tsize++;\n\t\t}\n\n\t\thead = node;\n\n\t\treturn node.val;\n\t}\n\n\tmemoized.clear = function() {\n\t\thead = null;\n\t\ttail = null;\n\t\tsize = 0;\n\t};\n\n\tif ( false ) {}\n\n\treturn memoized;\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/memize/index.js?");

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