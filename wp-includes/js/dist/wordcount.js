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
/******/ 	return __webpack_require__(__webpack_require__.s = 351);
/******/ })
/************************************************************************/
/******/ ({

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 351:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/defaultSettings.js
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
 * @param {Object} settings The main settings object containing regular expressions
 * @param {string} text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
/* harmony default export */ var stripTags = (function (settings, text) {
  if (settings.HTMLRegExp) {
    return text.replace(settings.HTMLRegExp, '\n');
  }
});

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/transposeAstralsToCountableChar.js
/**
 * Replaces items matched in the regex with character.
 *
 * @param {Object} settings The main settings object containing regular expressions
 * @param {string} text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
/* harmony default export */ var transposeAstralsToCountableChar = (function (settings, text) {
  if (settings.astralRegExp) {
    return text.replace(settings.astralRegExp, 'a');
  }

  return text;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripHTMLEntities.js
/**
 * Removes items matched in the regex.
 *
 * @param {Object} settings The main settings object containing regular expressions
 * @param {string} text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
/* harmony default export */ var stripHTMLEntities = (function (settings, text) {
  if (settings.HTMLEntityRegExp) {
    return text.replace(settings.HTMLEntityRegExp, '');
  }

  return text;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripConnectors.js
/**
 * Replaces items matched in the regex with spaces.
 *
 * @param {Object} settings The main settings object containing regular expressions
 * @param {string} text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
/* harmony default export */ var stripConnectors = (function (settings, text) {
  if (settings.connectorRegExp) {
    return text.replace(settings.connectorRegExp, ' ');
  }

  return text;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripRemovables.js
/**
 * Removes items matched in the regex.
 *
 * @param {Object} settings The main settings object containing regular expressions
 * @param {string} text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
/* harmony default export */ var stripRemovables = (function (settings, text) {
  if (settings.removeRegExp) {
    return text.replace(settings.removeRegExp, '');
  }

  return text;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripHTMLComments.js
/**
 * Removes items matched in the regex.
 *
 * @param {Object} settings The main settings object containing regular expressions
 * @param {string} text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
/* harmony default export */ var stripHTMLComments = (function (settings, text) {
  if (settings.HTMLcommentRegExp) {
    return text.replace(settings.HTMLcommentRegExp, '');
  }

  return text;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripShortcodes.js
/**
 * Replaces items matched in the regex with a new line.
 *
 * @param {Object} settings The main settings object containing regular expressions
 * @param {string} text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
/* harmony default export */ var stripShortcodes = (function (settings, text) {
  if (settings.shortcodesRegExp) {
    return text.replace(settings.shortcodesRegExp, '\n');
  }

  return text;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/stripSpaces.js
/**
 * Replaces items matched in the regex with spaces.
 *
 * @param {Object} settings The main settings object containing regular expressions
 * @param {string} text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
/* harmony default export */ var stripSpaces = (function (settings, text) {
  if (settings.spaceRegExp) {
    return text.replace(settings.spaceRegExp, ' ');
  }
});

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/transposeHTMLEntitiesToCountableChars.js
/**
 * Replaces items matched in the regex with a single character.
 *
 * @param {Object} settings The main settings object containing regular expressions
 * @param {string} text     The string being counted.
 *
 * @return {string} The manipulated text.
 */
/* harmony default export */ var transposeHTMLEntitiesToCountableChars = (function (settings, text) {
  if (settings.HTMLEntityRegExp) {
    return text.replace(settings.HTMLEntityRegExp, 'a');
  }

  return text;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/wordcount/build-module/index.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "count", function() { return count; });
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */











/**
 * Private function to manage the settings.
 *
 * @param {string} type         The type of count to be done.
 * @param {Object} userSettings Custom settings for the count.
 *
 * @return {void|Object|*} The combined settings object to be used.
 */

function loadSettings(type, userSettings) {
  var settings = Object(external_lodash_["extend"])(defaultSettings, userSettings);
  settings.shortcodes = settings.l10n.shortcodes || {};

  if (settings.shortcodes && settings.shortcodes.length) {
    settings.shortcodesRegExp = new RegExp('\\[\\/?(?:' + settings.shortcodes.join('|') + ')[^\\]]*?\\]', 'g');
  }

  settings.type = type || settings.l10n.type;

  if (settings.type !== 'characters_excluding_spaces' && settings.type !== 'characters_including_spaces') {
    settings.type = 'words';
  }

  return settings;
}
/**
 * Match the regex for the type 'words'
 *
 * @param {string} text     The text being processed
 * @param {string} regex    The regular expression pattern being matched
 * @param {Object} settings Settings object containing regular expressions for each strip function
 *
 * @return {Array|{index: number, input: string}} The matched string.
 */


function matchWords(text, regex, settings) {
  text = Object(external_lodash_["flow"])(stripTags.bind(this, settings), stripHTMLComments.bind(this, settings), stripShortcodes.bind(this, settings), stripSpaces.bind(this, settings), stripHTMLEntities.bind(this, settings), stripConnectors.bind(this, settings), stripRemovables.bind(this, settings))(text);
  text = text + '\n';
  return text.match(regex);
}
/**
 * Match the regex for either 'characters_excluding_spaces' or 'characters_including_spaces'
 *
 * @param {string} text     The text being processed
 * @param {string} regex    The regular expression pattern being matched
 * @param {Object} settings Settings object containing regular expressions for each strip function
 *
 * @return {Array|{index: number, input: string}} The matched string.
 */


function matchCharacters(text, regex, settings) {
  text = Object(external_lodash_["flow"])(stripTags.bind(this, settings), stripHTMLComments.bind(this, settings), stripShortcodes.bind(this, settings), stripSpaces.bind(this, settings), transposeAstralsToCountableChar.bind(this, settings), transposeHTMLEntitiesToCountableChars.bind(this, settings))(text);
  text = text + '\n';
  return text.match(regex);
}
/**
 * Count some words.
 *
 * @param {String} text         The text being processed
 * @param {String} type         The type of count. Accepts ;words', 'characters_excluding_spaces', or 'characters_including_spaces'.
 * @param {Object} userSettings Custom settings object.
 *
 * @example
 * ```js
 * import { count } from '@wordpress/wordcount';
 * const numberOfWords = count( 'Words to count', 'words', {} )
 * ```
 *
 * @return {Number} The word or character count.
 */


function count(text, type, userSettings) {
  if ('' === text) {
    return 0;
  }

  if (text) {
    var settings = loadSettings(type, userSettings);
    var matchRegExp = settings[type + 'RegExp'];
    var results = 'words' === settings.type ? matchWords(text, matchRegExp, settings) : matchCharacters(text, matchRegExp, settings);
    return results ? results.length : 0;
  }
}


/***/ })

/******/ });