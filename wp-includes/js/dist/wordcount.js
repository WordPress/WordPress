this["wp"] = this["wp"] || {}; this["wp"]["wordcount"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/wordcount/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@wordpress/wordcount/build-module/defaultSettings.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/defaultSettings.js ***!
  \***************************************************************************/
/*! exports provided: defaultSettings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"defaultSettings\", function() { return defaultSettings; });\nvar defaultSettings = {\n  HTMLRegExp: /<\\/?[a-z][^>]*?>/gi,\n  HTMLcommentRegExp: /<!--[\\s\\S]*?-->/g,\n  spaceRegExp: /&nbsp;|&#160;/gi,\n  HTMLEntityRegExp: /&\\S+?;/g,\n  // \\u2014 = em-dash\n  connectorRegExp: /--|\\u2014/g,\n  // Characters to be removed from input text.\n  removeRegExp: new RegExp(['[', // Basic Latin (extract)\n  \"!-@[-`{-~\", // Latin-1 Supplement (extract)\n  \"\\x80-\\xBF\\xD7\\xF7\",\n  /*\n   * The following range consists of:\n   * General Punctuation\n   * Superscripts and Subscripts\n   * Currency Symbols\n   * Combining Diacritical Marks for Symbols\n   * Letterlike Symbols\n   * Number Forms\n   * Arrows\n   * Mathematical Operators\n   * Miscellaneous Technical\n   * Control Pictures\n   * Optical Character Recognition\n   * Enclosed Alphanumerics\n   * Box Drawing\n   * Block Elements\n   * Geometric Shapes\n   * Miscellaneous Symbols\n   * Dingbats\n   * Miscellaneous Mathematical Symbols-A\n   * Supplemental Arrows-A\n   * Braille Patterns\n   * Supplemental Arrows-B\n   * Miscellaneous Mathematical Symbols-B\n   * Supplemental Mathematical Operators\n   * Miscellaneous Symbols and Arrows\n   */\n  \"\\u2000-\\u2BFF\", // Supplemental Punctuation\n  \"\\u2E00-\\u2E7F\", ']'].join(''), 'g'),\n  // Remove UTF-16 surrogate points, see https://en.wikipedia.org/wiki/UTF-16#U.2BD800_to_U.2BDFFF\n  astralRegExp: /[\\uD800-\\uDBFF][\\uDC00-\\uDFFF]/g,\n  wordsRegExp: /\\S\\s+/g,\n  characters_excluding_spacesRegExp: /\\S/g,\n\n  /*\n   * Match anything that is not a formatting character, excluding:\n   * \\f = form feed\n   * \\n = new line\n   * \\r = carriage return\n   * \\t = tab\n   * \\v = vertical tab\n   * \\u00AD = soft hyphen\n   * \\u2028 = line separator\n   * \\u2029 = paragraph separator\n   */\n  characters_including_spacesRegExp: /[^\\f\\n\\r\\t\\v\\u00AD\\u2028\\u2029]/g,\n  l10n: {\n    type: 'words'\n  }\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/defaultSettings.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/index.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/index.js ***!
  \*****************************************************************/
/*! exports provided: count */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"count\", function() { return count; });\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _defaultSettings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./defaultSettings */ \"./node_modules/@wordpress/wordcount/build-module/defaultSettings.js\");\n/* harmony import */ var _stripTags__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./stripTags */ \"./node_modules/@wordpress/wordcount/build-module/stripTags.js\");\n/* harmony import */ var _transposeAstralsToCountableChar__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./transposeAstralsToCountableChar */ \"./node_modules/@wordpress/wordcount/build-module/transposeAstralsToCountableChar.js\");\n/* harmony import */ var _stripHTMLEntities__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./stripHTMLEntities */ \"./node_modules/@wordpress/wordcount/build-module/stripHTMLEntities.js\");\n/* harmony import */ var _stripConnectors__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./stripConnectors */ \"./node_modules/@wordpress/wordcount/build-module/stripConnectors.js\");\n/* harmony import */ var _stripRemovables__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./stripRemovables */ \"./node_modules/@wordpress/wordcount/build-module/stripRemovables.js\");\n/* harmony import */ var _stripHTMLComments__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./stripHTMLComments */ \"./node_modules/@wordpress/wordcount/build-module/stripHTMLComments.js\");\n/* harmony import */ var _stripShortcodes__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./stripShortcodes */ \"./node_modules/@wordpress/wordcount/build-module/stripShortcodes.js\");\n/* harmony import */ var _stripSpaces__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./stripSpaces */ \"./node_modules/@wordpress/wordcount/build-module/stripSpaces.js\");\n/* harmony import */ var _transposeHTMLEntitiesToCountableChars__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./transposeHTMLEntitiesToCountableChars */ \"./node_modules/@wordpress/wordcount/build-module/transposeHTMLEntitiesToCountableChars.js\");\n\n\n\n\n\n\n\n\n\n\n\n/**\n * Private function to manage the settings.\n *\n * @param {string} type         The type of count to be done.\n * @param {Object} userSettings Custom settings for the count.\n *\n * @return {void|Object|*} The combined settings object to be used.\n */\n\nfunction loadSettings(type, userSettings) {\n  var settings = Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"extend\"])(_defaultSettings__WEBPACK_IMPORTED_MODULE_1__[\"defaultSettings\"], userSettings);\n  settings.shortcodes = settings.l10n.shortcodes || {};\n\n  if (settings.shortcodes && settings.shortcodes.length) {\n    settings.shortcodesRegExp = new RegExp('\\\\[\\\\/?(?:' + settings.shortcodes.join('|') + ')[^\\\\]]*?\\\\]', 'g');\n  }\n\n  settings.type = type || settings.l10n.type;\n\n  if (settings.type !== 'characters_excluding_spaces' && settings.type !== 'characters_including_spaces') {\n    settings.type = 'words';\n  }\n\n  return settings;\n}\n/**\n * Match the regex for the type 'words'\n *\n * @param {string} text     The text being processed\n * @param {string} regex    The regular expression pattern being matched\n * @param {Object} settings Settings object containing regular expressions for each strip function\n *\n * @return {Array|{index: number, input: string}} The matched string.\n */\n\n\nfunction matchWords(text, regex, settings) {\n  text = Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"flow\"])(_stripTags__WEBPACK_IMPORTED_MODULE_2__[\"default\"].bind(this, settings), _stripHTMLComments__WEBPACK_IMPORTED_MODULE_7__[\"default\"].bind(this, settings), _stripShortcodes__WEBPACK_IMPORTED_MODULE_8__[\"default\"].bind(this, settings), _stripSpaces__WEBPACK_IMPORTED_MODULE_9__[\"default\"].bind(this, settings), _stripHTMLEntities__WEBPACK_IMPORTED_MODULE_4__[\"default\"].bind(this, settings), _stripConnectors__WEBPACK_IMPORTED_MODULE_5__[\"default\"].bind(this, settings), _stripRemovables__WEBPACK_IMPORTED_MODULE_6__[\"default\"].bind(this, settings))(text);\n  text = text + '\\n';\n  return text.match(regex);\n}\n/**\n * Match the regex for either 'characters_excluding_spaces' or 'characters_including_spaces'\n *\n * @param {string} text     The text being processed\n * @param {string} regex    The regular expression pattern being matched\n * @param {Object} settings Settings object containing regular expressions for each strip function\n *\n * @return {Array|{index: number, input: string}} The matched string.\n */\n\n\nfunction matchCharacters(text, regex, settings) {\n  text = Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"flow\"])(_stripTags__WEBPACK_IMPORTED_MODULE_2__[\"default\"].bind(this, settings), _stripHTMLComments__WEBPACK_IMPORTED_MODULE_7__[\"default\"].bind(this, settings), _stripShortcodes__WEBPACK_IMPORTED_MODULE_8__[\"default\"].bind(this, settings), _stripSpaces__WEBPACK_IMPORTED_MODULE_9__[\"default\"].bind(this, settings), _transposeAstralsToCountableChar__WEBPACK_IMPORTED_MODULE_3__[\"default\"].bind(this, settings), _transposeHTMLEntitiesToCountableChars__WEBPACK_IMPORTED_MODULE_10__[\"default\"].bind(this, settings))(text);\n  text = text + '\\n';\n  return text.match(regex);\n}\n/**\n * Count some words.\n *\n * @param {String} text         The text being processed\n * @param {String} type         The type of count. Accepts ;words', 'characters_excluding_spaces', or 'characters_including_spaces'.\n * @param {Object} userSettings Custom settings object.\n *\n * @return {Number} The word or character count.\n */\n\n\nfunction count(text, type, userSettings) {\n  if ('' === text) {\n    return 0;\n  }\n\n  if (text) {\n    var settings = loadSettings(type, userSettings);\n    var matchRegExp = settings[type + 'RegExp'];\n    var results = 'words' === settings.type ? matchWords(text, matchRegExp, settings) : matchCharacters(text, matchRegExp, settings);\n    return results ? results.length : 0;\n  }\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/stripConnectors.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/stripConnectors.js ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Replaces items matched in the regex with spaces.\n *\n * @param {Object} settings The main settings object containing regular expressions\n * @param {string} text     The string being counted.\n *\n * @return {string} The manipulated text.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (settings, text) {\n  if (settings.connectorRegExp) {\n    return text.replace(settings.connectorRegExp, ' ');\n  }\n\n  return text;\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/stripConnectors.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/stripHTMLComments.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/stripHTMLComments.js ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Removes items matched in the regex.\n *\n * @param {Object} settings The main settings object containing regular expressions\n * @param {string} text     The string being counted.\n *\n * @return {string} The manipulated text.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (settings, text) {\n  if (settings.HTMLcommentRegExp) {\n    return text.replace(settings.HTMLcommentRegExp, '');\n  }\n\n  return text;\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/stripHTMLComments.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/stripHTMLEntities.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/stripHTMLEntities.js ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Removes items matched in the regex.\n *\n * @param {Object} settings The main settings object containing regular expressions\n * @param {string} text     The string being counted.\n *\n * @return {string} The manipulated text.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (settings, text) {\n  if (settings.HTMLEntityRegExp) {\n    return text.replace(settings.HTMLEntityRegExp, '');\n  }\n\n  return text;\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/stripHTMLEntities.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/stripRemovables.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/stripRemovables.js ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Removes items matched in the regex.\n *\n * @param {Object} settings The main settings object containing regular expressions\n * @param {string} text     The string being counted.\n *\n * @return {string} The manipulated text.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (settings, text) {\n  if (settings.removeRegExp) {\n    return text.replace(settings.removeRegExp, '');\n  }\n\n  return text;\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/stripRemovables.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/stripShortcodes.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/stripShortcodes.js ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Replaces items matched in the regex with a new line.\n *\n * @param {Object} settings The main settings object containing regular expressions\n * @param {string} text     The string being counted.\n *\n * @return {string} The manipulated text.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (settings, text) {\n  if (settings.shortcodesRegExp) {\n    return text.replace(settings.shortcodesRegExp, '\\n');\n  }\n\n  return text;\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/stripShortcodes.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/stripSpaces.js":
/*!***********************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/stripSpaces.js ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Replaces items matched in the regex with spaces.\n *\n * @param {Object} settings The main settings object containing regular expressions\n * @param {string} text     The string being counted.\n *\n * @return {string} The manipulated text.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (settings, text) {\n  if (settings.spaceRegExp) {\n    return text.replace(settings.spaceRegExp, ' ');\n  }\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/stripSpaces.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/stripTags.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/stripTags.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Replaces items matched in the regex with new line\n *\n * @param {Object} settings The main settings object containing regular expressions\n * @param {string} text     The string being counted.\n *\n * @return {string} The manipulated text.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (settings, text) {\n  if (settings.HTMLRegExp) {\n    return text.replace(settings.HTMLRegExp, '\\n');\n  }\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/stripTags.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/transposeAstralsToCountableChar.js":
/*!*******************************************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/transposeAstralsToCountableChar.js ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Replaces items matched in the regex with character.\n *\n * @param {Object} settings The main settings object containing regular expressions\n * @param {string} text     The string being counted.\n *\n * @return {string} The manipulated text.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (settings, text) {\n  if (settings.astralRegExp) {\n    return text.replace(settings.astralRegExp, 'a');\n  }\n\n  return text;\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/transposeAstralsToCountableChar.js?");

/***/ }),

/***/ "./node_modules/@wordpress/wordcount/build-module/transposeHTMLEntitiesToCountableChars.js":
/*!*************************************************************************************************!*\
  !*** ./node_modules/@wordpress/wordcount/build-module/transposeHTMLEntitiesToCountableChars.js ***!
  \*************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Replaces items matched in the regex with a single character.\n *\n * @param {Object} settings The main settings object containing regular expressions\n * @param {string} text     The string being counted.\n *\n * @return {string} The manipulated text.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (settings, text) {\n  if (settings.HTMLEntityRegExp) {\n    return text.replace(settings.HTMLEntityRegExp, 'a');\n  }\n\n  return text;\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/wordcount/build-module/transposeHTMLEntitiesToCountableChars.js?");

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