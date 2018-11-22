this["wp"] = this["wp"] || {}; this["wp"]["autop"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/autop/build-module/index.js");
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

/***/ "./node_modules/@wordpress/autop/build-module/index.js":
/*!*************************************************************!*\
  !*** ./node_modules/@wordpress/autop/build-module/index.js ***!
  \*************************************************************/
/*! exports provided: autop, removep */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"autop\", function() { return autop; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"removep\", function() { return removep; });\n/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/slicedToArray */ \"./node_modules/@babel/runtime/helpers/esm/slicedToArray.js\");\n\n\n/**\n * The regular expression for an HTML element.\n *\n * @type {String}\n */\nvar htmlSplitRegex = function () {\n  /* eslint-disable no-multi-spaces */\n  var comments = '!' + // Start of comment, after the <.\n  '(?:' + // Unroll the loop: Consume everything until --> is found.\n  '-(?!->)' + // Dash not followed by end of comment.\n  '[^\\\\-]*' + // Consume non-dashes.\n  ')*' + // Loop possessively.\n  '(?:-->)?'; // End of comment. If not found, match all input.\n\n  var cdata = '!\\\\[CDATA\\\\[' + // Start of comment, after the <.\n  '[^\\\\]]*' + // Consume non-].\n  '(?:' + // Unroll the loop: Consume everything until ]]> is found.\n  '](?!]>)' + // One ] not followed by end of comment.\n  '[^\\\\]]*' + // Consume non-].\n  ')*?' + // Loop possessively.\n  '(?:]]>)?'; // End of comment. If not found, match all input.\n\n  var escaped = '(?=' + // Is the element escaped?\n  '!--' + '|' + '!\\\\[CDATA\\\\[' + ')' + '((?=!-)' + // If yes, which type?\n  comments + '|' + cdata + ')';\n  var regex = '(' + // Capture the entire match.\n  '<' + // Find start of element.\n  '(' + // Conditional expression follows.\n  escaped + // Find end of escaped element.\n  '|' + // ... else ...\n  '[^>]*>?' + // Find end of normal element.\n  ')' + ')';\n  return new RegExp(regex);\n  /* eslint-enable no-multi-spaces */\n}();\n/**\n * Separate HTML elements and comments from the text.\n *\n * @param  {string} input The text which has to be formatted.\n * @return {Array}        The formatted text.\n */\n\n\nfunction htmlSplit(input) {\n  var parts = [];\n  var workingInput = input;\n  var match;\n\n  while (match = workingInput.match(htmlSplitRegex)) {\n    parts.push(workingInput.slice(0, match.index));\n    parts.push(match[0]);\n    workingInput = workingInput.slice(match.index + match[0].length);\n  }\n\n  if (workingInput.length) {\n    parts.push(workingInput);\n  }\n\n  return parts;\n}\n/**\n * Replace characters or phrases within HTML elements only.\n *\n * @param  {string} haystack     The text which has to be formatted.\n * @param  {Object} replacePairs In the form {from: 'to', ...}.\n * @return {string}              The formatted text.\n */\n\n\nfunction replaceInHtmlTags(haystack, replacePairs) {\n  // Find all elements.\n  var textArr = htmlSplit(haystack);\n  var changed = false; // Extract all needles.\n\n  var needles = Object.keys(replacePairs); // Loop through delimiters (elements) only.\n\n  for (var i = 1; i < textArr.length; i += 2) {\n    for (var j = 0; j < needles.length; j++) {\n      var needle = needles[j];\n\n      if (-1 !== textArr[i].indexOf(needle)) {\n        textArr[i] = textArr[i].replace(new RegExp(needle, 'g'), replacePairs[needle]);\n        changed = true; // After one strtr() break out of the foreach loop and look at next element.\n\n        break;\n      }\n    }\n  }\n\n  if (changed) {\n    haystack = textArr.join('');\n  }\n\n  return haystack;\n}\n/**\n * Replaces double line-breaks with paragraph elements.\n *\n * A group of regex replaces used to identify text formatted with newlines and\n * replace double line-breaks with HTML paragraph tags. The remaining line-\n * breaks after conversion become <<br />> tags, unless br is set to 'false'.\n *\n * @param  {string}    text The text which has to be formatted.\n * @param  {boolean}   br   Optional. If set, will convert all remaining line-\n *                          breaks after paragraphing. Default true.\n * @return {string}         Text which has been converted into paragraph tags.\n */\n\n\nfunction autop(text) {\n  var br = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;\n  var preTags = [];\n\n  if (text.trim() === '') {\n    return '';\n  } // Just to make things a little easier, pad the end.\n\n\n  text = text + '\\n';\n  /*\n   * Pre tags shouldn't be touched by autop.\n   * Replace pre tags with placeholders and bring them back after autop.\n   */\n\n  if (text.indexOf('<pre') !== -1) {\n    var textParts = text.split('</pre>');\n    var lastText = textParts.pop();\n    text = '';\n\n    for (var i = 0; i < textParts.length; i++) {\n      var textPart = textParts[i];\n      var start = textPart.indexOf('<pre'); // Malformed html?\n\n      if (start === -1) {\n        text += textPart;\n        continue;\n      }\n\n      var name = '<pre wp-pre-tag-' + i + '></pre>';\n      preTags.push([name, textPart.substr(start) + '</pre>']);\n      text += textPart.substr(0, start) + name;\n    }\n\n    text += lastText;\n  } // Change multiple <br>s into two line breaks, which will turn into paragraphs.\n\n\n  text = text.replace(/<br\\s*\\/?>\\s*<br\\s*\\/?>/g, '\\n\\n');\n  var allBlocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)'; // Add a double line break above block-level opening tags.\n\n  text = text.replace(new RegExp('(<' + allBlocks + '[\\s\\/>])', 'g'), '\\n\\n$1'); // Add a double line break below block-level closing tags.\n\n  text = text.replace(new RegExp('(<\\/' + allBlocks + '>)', 'g'), '$1\\n\\n'); // Standardize newline characters to \"\\n\".\n\n  text = text.replace(/\\r\\n|\\r/g, '\\n'); // Find newlines in all elements and add placeholders.\n\n  text = replaceInHtmlTags(text, {\n    '\\n': ' <!-- wpnl --> '\n  }); // Collapse line breaks before and after <option> elements so they don't get autop'd.\n\n  if (text.indexOf('<option') !== -1) {\n    text = text.replace(/\\s*<option/g, '<option');\n    text = text.replace(/<\\/option>\\s*/g, '</option>');\n  }\n  /*\n   * Collapse line breaks inside <object> elements, before <param> and <embed> elements\n   * so they don't get autop'd.\n   */\n\n\n  if (text.indexOf('</object>') !== -1) {\n    text = text.replace(/(<object[^>]*>)\\s*/g, '$1');\n    text = text.replace(/\\s*<\\/object>/g, '</object>');\n    text = text.replace(/\\s*(<\\/?(?:param|embed)[^>]*>)\\s*/g, '$1');\n  }\n  /*\n   * Collapse line breaks inside <audio> and <video> elements,\n   * before and after <source> and <track> elements.\n   */\n\n\n  if (text.indexOf('<source') !== -1 || text.indexOf('<track') !== -1) {\n    text = text.replace(/([<\\[](?:audio|video)[^>\\]]*[>\\]])\\s*/g, '$1');\n    text = text.replace(/\\s*([<\\[]\\/(?:audio|video)[>\\]])/g, '$1');\n    text = text.replace(/\\s*(<(?:source|track)[^>]*>)\\s*/g, '$1');\n  } // Collapse line breaks before and after <figcaption> elements.\n\n\n  if (text.indexOf('<figcaption') !== -1) {\n    text = text.replace(/\\s*(<figcaption[^>]*>)/, '$1');\n    text = text.replace(/<\\/figcaption>\\s*/, '</figcaption>');\n  } // Remove more than two contiguous line breaks.\n\n\n  text = text.replace(/\\n\\n+/g, '\\n\\n'); // Split up the contents into an array of strings, separated by double line breaks.\n\n  var texts = text.split(/\\n\\s*\\n/).filter(Boolean); // Reset text prior to rebuilding.\n\n  text = ''; // Rebuild the content as a string, wrapping every bit with a <p>.\n\n  texts.forEach(function (textPiece) {\n    text += '<p>' + textPiece.replace(/^\\n*|\\n*$/g, '') + '</p>\\n';\n  }); // Under certain strange conditions it could create a P of entirely whitespace.\n\n  text = text.replace(/<p>\\s*<\\/p>/g, ''); // Add a closing <p> inside <div>, <address>, or <form> tag if missing.\n\n  text = text.replace(/<p>([^<]+)<\\/(div|address|form)>/g, '<p>$1</p></$2>'); // If an opening or closing block element tag is wrapped in a <p>, unwrap it.\n\n  text = text.replace(new RegExp('<p>\\s*(<\\/?' + allBlocks + '[^>]*>)\\s*<\\/p>', 'g'), '$1'); // In some cases <li> may get wrapped in <p>, fix them.\n\n  text = text.replace(/<p>(<li.+?)<\\/p>/g, '$1'); // If a <blockquote> is wrapped with a <p>, move it inside the <blockquote>.\n\n  text = text.replace(/<p><blockquote([^>]*)>/gi, '<blockquote$1><p>');\n  text = text.replace(/<\\/blockquote><\\/p>/g, '</p></blockquote>'); // If an opening or closing block element tag is preceded by an opening <p> tag, remove it.\n\n  text = text.replace(new RegExp('<p>\\s*(<\\/?' + allBlocks + '[^>]*>)', 'g'), '$1'); // If an opening or closing block element tag is followed by a closing <p> tag, remove it.\n\n  text = text.replace(new RegExp('(<\\/?' + allBlocks + '[^>]*>)\\s*<\\/p>', 'g'), '$1'); // Optionally insert line breaks.\n\n  if (br) {\n    // Replace newlines that shouldn't be touched with a placeholder.\n    text = text.replace(/<(script|style).*?<\\/\\\\1>/g, function (match) {\n      return match[0].replace(/\\n/g, '<WPPreserveNewline />');\n    }); // Normalize <br>\n\n    text = text.replace(/<br>|<br\\/>/g, '<br />'); // Replace any new line characters that aren't preceded by a <br /> with a <br />.\n\n    text = text.replace(/(<br \\/>)?\\s*\\n/g, function (a, b) {\n      return b ? a : '<br />\\n';\n    }); // Replace newline placeholders with newlines.\n\n    text = text.replace(/<WPPreserveNewline \\/>/g, '\\n');\n  } // If a <br /> tag is after an opening or closing block tag, remove it.\n\n\n  text = text.replace(new RegExp('(<\\/?' + allBlocks + '[^>]*>)\\s*<br \\/>', 'g'), '$1'); // If a <br /> tag is before a subset of opening or closing block tags, remove it.\n\n  text = text.replace(/<br \\/>(\\s*<\\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)/g, '$1');\n  text = text.replace(/\\n<\\/p>$/g, '</p>'); // Replace placeholder <pre> tags with their original content.\n\n  preTags.forEach(function (preTag) {\n    var _preTag = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(preTag, 2),\n        name = _preTag[0],\n        original = _preTag[1];\n\n    text = text.replace(name, original);\n  }); // Restore newlines in all elements.\n\n  if (-1 !== text.indexOf('<!-- wpnl -->')) {\n    text = text.replace(/\\s?<!-- wpnl -->\\s?/g, '\\n');\n  }\n\n  return text;\n}\n/**\n * Replaces <p> tags with two line breaks. \"Opposite\" of autop().\n *\n * Replaces <p> tags with two line breaks except where the <p> has attributes.\n * Unifies whitespace. Indents <li>, <dt> and <dd> for better readability.\n *\n * @param  {string} html The content from the editor.\n * @return {string}      The content with stripped paragraph tags.\n */\n\nfunction removep(html) {\n  var blocklist = 'blockquote|ul|ol|li|dl|dt|dd|table|thead|tbody|tfoot|tr|th|td|h[1-6]|fieldset|figure';\n  var blocklist1 = blocklist + '|div|p';\n  var blocklist2 = blocklist + '|pre';\n  var preserve = [];\n  var preserveLinebreaks = false;\n  var preserveBr = false;\n\n  if (!html) {\n    return '';\n  } // Protect script and style tags.\n\n\n  if (html.indexOf('<script') !== -1 || html.indexOf('<style') !== -1) {\n    html = html.replace(/<(script|style)[^>]*>[\\s\\S]*?<\\/\\1>/g, function (match) {\n      preserve.push(match);\n      return '<wp-preserve>';\n    });\n  } // Protect pre tags.\n\n\n  if (html.indexOf('<pre') !== -1) {\n    preserveLinebreaks = true;\n    html = html.replace(/<pre[^>]*>[\\s\\S]+?<\\/pre>/g, function (a) {\n      a = a.replace(/<br ?\\/?>(\\r\\n|\\n)?/g, '<wp-line-break>');\n      a = a.replace(/<\\/?p( [^>]*)?>(\\r\\n|\\n)?/g, '<wp-line-break>');\n      return a.replace(/\\r?\\n/g, '<wp-line-break>');\n    });\n  } // Remove line breaks but keep <br> tags inside image captions.\n\n\n  if (html.indexOf('[caption') !== -1) {\n    preserveBr = true;\n    html = html.replace(/\\[caption[\\s\\S]+?\\[\\/caption\\]/g, function (a) {\n      return a.replace(/<br([^>]*)>/g, '<wp-temp-br$1>').replace(/[\\r\\n\\t]+/, '');\n    });\n  } // Normalize white space characters before and after block tags.\n\n\n  html = html.replace(new RegExp('\\\\s*</(' + blocklist1 + ')>\\\\s*', 'g'), '</$1>\\n');\n  html = html.replace(new RegExp('\\\\s*<((?:' + blocklist1 + ')(?: [^>]*)?)>', 'g'), '\\n<$1>'); // Mark </p> if it has any attributes.\n\n  html = html.replace(/(<p [^>]+>.*?)<\\/p>/g, '$1</p#>'); // Preserve the first <p> inside a <div>.\n\n  html = html.replace(/<div( [^>]*)?>\\s*<p>/gi, '<div$1>\\n\\n'); // Remove paragraph tags.\n\n  html = html.replace(/\\s*<p>/gi, '');\n  html = html.replace(/\\s*<\\/p>\\s*/gi, '\\n\\n'); // Normalize white space chars and remove multiple line breaks.\n\n  html = html.replace(/\\n[\\s\\u00a0]+\\n/g, '\\n\\n'); // Replace <br> tags with line breaks.\n\n  html = html.replace(/(\\s*)<br ?\\/?>\\s*/gi, function (match, space) {\n    if (space && space.indexOf('\\n') !== -1) {\n      return '\\n\\n';\n    }\n\n    return '\\n';\n  }); // Fix line breaks around <div>.\n\n  html = html.replace(/\\s*<div/g, '\\n<div');\n  html = html.replace(/<\\/div>\\s*/g, '</div>\\n'); // Fix line breaks around caption shortcodes.\n\n  html = html.replace(/\\s*\\[caption([^\\[]+)\\[\\/caption\\]\\s*/gi, '\\n\\n[caption$1[/caption]\\n\\n');\n  html = html.replace(/caption\\]\\n\\n+\\[caption/g, 'caption]\\n\\n[caption'); // Pad block elements tags with a line break.\n\n  html = html.replace(new RegExp('\\\\s*<((?:' + blocklist2 + ')(?: [^>]*)?)\\\\s*>', 'g'), '\\n<$1>');\n  html = html.replace(new RegExp('\\\\s*</(' + blocklist2 + ')>\\\\s*', 'g'), '</$1>\\n'); // Indent <li>, <dt> and <dd> tags.\n\n  html = html.replace(/<((li|dt|dd)[^>]*)>/g, ' \\t<$1>'); // Fix line breaks around <select> and <option>.\n\n  if (html.indexOf('<option') !== -1) {\n    html = html.replace(/\\s*<option/g, '\\n<option');\n    html = html.replace(/\\s*<\\/select>/g, '\\n</select>');\n  } // Pad <hr> with two line breaks.\n\n\n  if (html.indexOf('<hr') !== -1) {\n    html = html.replace(/\\s*<hr( [^>]*)?>\\s*/g, '\\n\\n<hr$1>\\n\\n');\n  } // Remove line breaks in <object> tags.\n\n\n  if (html.indexOf('<object') !== -1) {\n    html = html.replace(/<object[\\s\\S]+?<\\/object>/g, function (a) {\n      return a.replace(/[\\r\\n]+/g, '');\n    });\n  } // Unmark special paragraph closing tags.\n\n\n  html = html.replace(/<\\/p#>/g, '</p>\\n'); // Pad remaining <p> tags whit a line break.\n\n  html = html.replace(/\\s*(<p [^>]+>[\\s\\S]*?<\\/p>)/g, '\\n$1'); // Trim.\n\n  html = html.replace(/^\\s+/, '');\n  html = html.replace(/[\\s\\u00a0]+$/, '');\n\n  if (preserveLinebreaks) {\n    html = html.replace(/<wp-line-break>/g, '\\n');\n  }\n\n  if (preserveBr) {\n    html = html.replace(/<wp-temp-br([^>]*)>/g, '<br$1>');\n  } // Restore preserved tags.\n\n\n  if (preserve.length) {\n    html = html.replace(/<wp-preserve>/g, function () {\n      return preserve.shift();\n    });\n  }\n\n  return html;\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/autop/build-module/index.js?");

/***/ })

/******/ });