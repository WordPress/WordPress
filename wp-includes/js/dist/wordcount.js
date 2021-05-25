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
/******/ 	return __webpack_require__(__webpack_require__.s = "pC98");
/******/ })
/************************************************************************/
/******/ ({

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "pC98":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "count", function() { return /* binding */ count; });

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/defaultSettings.js
/** @typedef {import('./index').WPWordCountStrategy} WPWordCountStrategy */

/** @typedef {Partial<{type: WPWordCountStrategy, shortcodes: string[]}>} WPWordCountL10n */

/**
 * @typedef WPWordCountSettingsFields
 * @property {RegExp}              HTMLRegExp                        Regular expression that matches HTML tags
 * @property {RegExp}              HTMLcommentRegExp                 Regular expression that matches HTML comments
 * @property {RegExp}              spaceRegExp                       Regular expression that matches spaces in HTML
 * @property {RegExp}              HTMLEntityRegExp                  Regular expression that matches HTML entities
 * @property {RegExp}              connectorRegExp                   Regular expression that matches word connectors, like em-dash
 * @property {RegExp}              removeRegExp                      Regular expression that matches various characters to be removed when counting
 * @property {RegExp}              astralRegExp                      Regular expression that matches astral UTF-16 code points
 * @property {RegExp}              wordsRegExp                       Regular expression that matches words
 * @property {RegExp}              characters_excluding_spacesRegExp Regular expression that matches characters excluding spaces
 * @property {RegExp}              characters_including_spacesRegExp Regular expression that matches characters including spaces
 * @property {RegExp}              shortcodesRegExp                  Regular expression that matches WordPress shortcodes
 * @property {string[]}            shortcodes                        List of all shortcodes
 * @property {WPWordCountStrategy} type                              Describes what and how are we counting
 * @property {WPWordCountL10n}     l10n                              Object with human translations
 */

/**
 * Lower-level settings for word counting that can be overridden.
 *
 * @typedef {Partial<WPWordCountSettingsFields>} WPWordCountUserSettings
 */
// Disable reason: JSDoc linter doesn't seem to parse the union (`&`) correctly: https://github.com/jsdoc/jsdoc/issues/1285

/* eslint-disable jsdoc/valid-types */

/**
 * Word counting settings that include non-optional values we set if missing
 *
 * @typedef {WPWordCountUserSettings & typeof defaultSettings} WPWordCountDefaultSettings
 */

/* eslint-enable jsdoc/valid-types */
var defaultSettings = {
  HTMLRegExp: /<\/?[a-z][^>]*?>/gi,
  HTMLcommentRegExp: /<!--[\s\S]*?-->/g,
  spaceRegExp: /&nbsp;|&#160;/gi,
  HTMLEntityRegExp: /&\S+?;/g,
  // \u2014 = em-dash
  connectorRegExp: /--|\u2014/g,
  // Characters to be removed from input text.
  removeRegExp: new RegExp(['[', // Basic Latin (extract)
  "!-@[-`{-~", // Latin-1 Supplement (extract)
  "\x80-\xBF\xD7\xF7",
  /*
   * The following range consists of:
   * General Punctuation
   * Superscripts and Subscripts
   * Currency Symbols
   * Combining Diacritical Marks for Symbols
   * Letterlike Symbols
   * Number Forms
   * Arrows
   * Mathematical Operators
   * Miscellaneous Technical
   * Control Pictures
   * Optical Character Recognition
   * Enclosed Alphanumerics
   * Box Drawing
   * Block Elements
   * Geometric Shapes
   * Miscellaneous Symbols
   * Dingbats
   * Miscellaneous Mathematical Symbols-A
   * Supplemental Arrows-A
   * Braille Patterns
   * Supplemental Arrows-B
   * Miscellaneous Mathematical Symbols-B
   * Supplemental Mathematical Operators
   * Miscellaneous Symbols and Arrows
   */
  "\u2000-\u2BFF", // Supplemental Punctuation
  "\u2E00-\u2E7F", ']'].join(''), 'g'),
  // Remove UTF-16 surrogate points, see https://en.wikipedia.org/wiki/UTF-16#U.2BD800_to_U.2BDFFF
  astralRegExp: /[\uD800-\uDBFF][\uDC00-\uDFFF]/g,
  wordsRegExp: /\S\s+/g,
  characters_excluding_spacesRegExp: /\S/g,

  /*
   * Match anything that is not a formatting character, excluding:
   * \f = form feed
   * \n = new line
   * \r = carriage return
   * \t = tab
   * \v = vertical tab
   * \u00AD = soft hyphen
   * \u2028 = line separator
   * \u2029 = paragraph separator
   */
  characters_including_spacesRegExp: /[^\f\n\r\t\v\u00AD\u2028\u2029]/g,
  l10n: {
    type: 'words'
  }
};

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripTags.js
/**
 * Replaces items matched in the regex with new line
 *
 * @param {import('./index').WPWordCountSettings} settings The main settings object containing regular expressions
 * @param {string}                                text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
function stripTags(settings, text) {
  return text.replace(settings.HTMLRegExp, '\n');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/transposeAstralsToCountableChar.js
/**
 * Replaces items matched in the regex with character.
 *
 * @param {import('./index').WPWordCountSettings} settings The main settings object containing regular expressions
 * @param {string}                                text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
function transposeAstralsToCountableChar(settings, text) {
  return text.replace(settings.astralRegExp, 'a');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripHTMLEntities.js
/**
 * Removes items matched in the regex.
 *
 * @param {import('./index').WPWordCountSettings} settings The main settings object containing regular expressions
 * @param {string}                                text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
function stripHTMLEntities(settings, text) {
  return text.replace(settings.HTMLEntityRegExp, '');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripConnectors.js
/**
 * Replaces items matched in the regex with spaces.
 *
 * @param {import('./index').WPWordCountSettings} settings The main settings object containing regular expressions
 * @param {string}                                text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
function stripConnectors(settings, text) {
  return text.replace(settings.connectorRegExp, ' ');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripRemovables.js
/**
 * Removes items matched in the regex.
 *
 * @param {import('./index').WPWordCountSettings} settings The main settings object containing regular expressions
 * @param {string}                                text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
function stripRemovables(settings, text) {
  return text.replace(settings.removeRegExp, '');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripHTMLComments.js
/**
 * Removes items matched in the regex.
 *
 * @param {import('./index').WPWordCountSettings} settings The main settings object containing regular expressions
 * @param {string}                                text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
function stripHTMLComments(settings, text) {
  return text.replace(settings.HTMLcommentRegExp, '');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripShortcodes.js
/**
 * Replaces items matched in the regex with a new line.
 *
 * @param {import('./index').WPWordCountSettings} settings The main settings object containing regular expressions
 * @param {string}                                text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
function stripShortcodes(settings, text) {
  if (settings.shortcodesRegExp) {
    return text.replace(settings.shortcodesRegExp, '\n');
  }

  return text;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripSpaces.js
/**
 * Replaces items matched in the regex with spaces.
 *
 * @param {import('./index').WPWordCountSettings} settings The main settings object containing regular expressions
 * @param {string}                                text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
function stripSpaces(settings, text) {
  return text.replace(settings.spaceRegExp, ' ');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/transposeHTMLEntitiesToCountableChars.js
/**
 * Replaces items matched in the regex with a single character.
 *
 * @param {import('./index').WPWordCountSettings} settings The main settings object containing regular expressions
 * @param {string}                                text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
function transposeHTMLEntitiesToCountableChars(settings, text) {
  return text.replace(settings.HTMLEntityRegExp, 'a');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/index.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */











/**
 * @typedef {import('./defaultSettings').WPWordCountDefaultSettings}  WPWordCountSettings
 * @typedef {import('./defaultSettings').WPWordCountUserSettings}     WPWordCountUserSettings
 */

/**
 * Possible ways of counting.
 *
 * @typedef {'words'|'characters_excluding_spaces'|'characters_including_spaces'} WPWordCountStrategy
 */

/**
 * Private function to manage the settings.
 *
 * @param {WPWordCountStrategy}     type         The type of count to be done.
 * @param {WPWordCountUserSettings} userSettings Custom settings for the count.
 *
 * @return {WPWordCountSettings} The combined settings object to be used.
 */

function loadSettings(type, userSettings) {
  var _settings$l10n$shortc, _settings$l10n;

  var settings = Object(external_lodash_["extend"])({}, defaultSettings, userSettings);
  settings.shortcodes = (_settings$l10n$shortc = (_settings$l10n = settings.l10n) === null || _settings$l10n === void 0 ? void 0 : _settings$l10n.shortcodes) !== null && _settings$l10n$shortc !== void 0 ? _settings$l10n$shortc : [];

  if (settings.shortcodes && settings.shortcodes.length) {
    settings.shortcodesRegExp = new RegExp('\\[\\/?(?:' + settings.shortcodes.join('|') + ')[^\\]]*?\\]', 'g');
  }

  settings.type = type;

  if (settings.type !== 'characters_excluding_spaces' && settings.type !== 'characters_including_spaces') {
    settings.type = 'words';
  }

  return settings;
}
/**
 * Count the words in text
 *
 * @param {string}              text     The text being processed
 * @param {RegExp}              regex    The regular expression pattern being matched
 * @param {WPWordCountSettings} settings Settings object containing regular expressions for each strip function
 *
 * @return {number} Count of words.
 */


function countWords(text, regex, settings) {
  var _text$match$length, _text$match;

  text = Object(external_lodash_["flow"])(stripTags.bind(null, settings), stripHTMLComments.bind(null, settings), stripShortcodes.bind(null, settings), stripSpaces.bind(null, settings), stripHTMLEntities.bind(null, settings), stripConnectors.bind(null, settings), stripRemovables.bind(null, settings))(text);
  text = text + '\n';
  return (_text$match$length = (_text$match = text.match(regex)) === null || _text$match === void 0 ? void 0 : _text$match.length) !== null && _text$match$length !== void 0 ? _text$match$length : 0;
}
/**
 * Count the characters in text
 *
 * @param {string}              text     The text being processed
 * @param {RegExp}              regex    The regular expression pattern being matched
 * @param {WPWordCountSettings} settings Settings object containing regular expressions for each strip function
 *
 * @return {number} Count of characters.
 */


function countCharacters(text, regex, settings) {
  var _text$match$length2, _text$match2;

  text = Object(external_lodash_["flow"])(stripTags.bind(null, settings), stripHTMLComments.bind(null, settings), stripShortcodes.bind(null, settings), transposeAstralsToCountableChar.bind(null, settings), stripSpaces.bind(null, settings), transposeHTMLEntitiesToCountableChars.bind(null, settings))(text);
  text = text + '\n';
  return (_text$match$length2 = (_text$match2 = text.match(regex)) === null || _text$match2 === void 0 ? void 0 : _text$match2.length) !== null && _text$match$length2 !== void 0 ? _text$match$length2 : 0;
}
/**
 * Count some words.
 *
 * @param {string}                  text         The text being processed
 * @param {WPWordCountStrategy}     type         The type of count. Accepts 'words', 'characters_excluding_spaces', or 'characters_including_spaces'.
 * @param {WPWordCountUserSettings} userSettings Custom settings object.
 *
 * @example
 * ```js
 * import { count } from '@wordpress/wordcount';
 * const numberOfWords = count( 'Words to count', 'words', {} )
 * ```
 *
 * @return {number} The word or character count.
 */


function count(text, type, userSettings) {
  var settings = loadSettings(type, userSettings);
  var matchRegExp;

  switch (settings.type) {
    case 'words':
      matchRegExp = settings.wordsRegExp;
      return countWords(text, matchRegExp, settings);

    case 'characters_including_spaces':
      matchRegExp = settings.characters_including_spacesRegExp;
      return countCharacters(text, matchRegExp, settings);

    case 'characters_excluding_spaces':
      matchRegExp = settings.characters_excluding_spacesRegExp;
      return countCharacters(text, matchRegExp, settings);

    default:
      return 0;
  }
}


/***/ })

/******/ });