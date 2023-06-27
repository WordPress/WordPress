/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 4793:
/***/ (function(module) {

var characterMap = {
	"À": "A",
	"Á": "A",
	"Â": "A",
	"Ã": "A",
	"Ä": "A",
	"Å": "A",
	"Ấ": "A",
	"Ắ": "A",
	"Ẳ": "A",
	"Ẵ": "A",
	"Ặ": "A",
	"Æ": "AE",
	"Ầ": "A",
	"Ằ": "A",
	"Ȃ": "A",
	"Ç": "C",
	"Ḉ": "C",
	"È": "E",
	"É": "E",
	"Ê": "E",
	"Ë": "E",
	"Ế": "E",
	"Ḗ": "E",
	"Ề": "E",
	"Ḕ": "E",
	"Ḝ": "E",
	"Ȇ": "E",
	"Ì": "I",
	"Í": "I",
	"Î": "I",
	"Ï": "I",
	"Ḯ": "I",
	"Ȋ": "I",
	"Ð": "D",
	"Ñ": "N",
	"Ò": "O",
	"Ó": "O",
	"Ô": "O",
	"Õ": "O",
	"Ö": "O",
	"Ø": "O",
	"Ố": "O",
	"Ṍ": "O",
	"Ṓ": "O",
	"Ȏ": "O",
	"Ù": "U",
	"Ú": "U",
	"Û": "U",
	"Ü": "U",
	"Ý": "Y",
	"à": "a",
	"á": "a",
	"â": "a",
	"ã": "a",
	"ä": "a",
	"å": "a",
	"ấ": "a",
	"ắ": "a",
	"ẳ": "a",
	"ẵ": "a",
	"ặ": "a",
	"æ": "ae",
	"ầ": "a",
	"ằ": "a",
	"ȃ": "a",
	"ç": "c",
	"ḉ": "c",
	"è": "e",
	"é": "e",
	"ê": "e",
	"ë": "e",
	"ế": "e",
	"ḗ": "e",
	"ề": "e",
	"ḕ": "e",
	"ḝ": "e",
	"ȇ": "e",
	"ì": "i",
	"í": "i",
	"î": "i",
	"ï": "i",
	"ḯ": "i",
	"ȋ": "i",
	"ð": "d",
	"ñ": "n",
	"ò": "o",
	"ó": "o",
	"ô": "o",
	"õ": "o",
	"ö": "o",
	"ø": "o",
	"ố": "o",
	"ṍ": "o",
	"ṓ": "o",
	"ȏ": "o",
	"ù": "u",
	"ú": "u",
	"û": "u",
	"ü": "u",
	"ý": "y",
	"ÿ": "y",
	"Ā": "A",
	"ā": "a",
	"Ă": "A",
	"ă": "a",
	"Ą": "A",
	"ą": "a",
	"Ć": "C",
	"ć": "c",
	"Ĉ": "C",
	"ĉ": "c",
	"Ċ": "C",
	"ċ": "c",
	"Č": "C",
	"č": "c",
	"C̆": "C",
	"c̆": "c",
	"Ď": "D",
	"ď": "d",
	"Đ": "D",
	"đ": "d",
	"Ē": "E",
	"ē": "e",
	"Ĕ": "E",
	"ĕ": "e",
	"Ė": "E",
	"ė": "e",
	"Ę": "E",
	"ę": "e",
	"Ě": "E",
	"ě": "e",
	"Ĝ": "G",
	"Ǵ": "G",
	"ĝ": "g",
	"ǵ": "g",
	"Ğ": "G",
	"ğ": "g",
	"Ġ": "G",
	"ġ": "g",
	"Ģ": "G",
	"ģ": "g",
	"Ĥ": "H",
	"ĥ": "h",
	"Ħ": "H",
	"ħ": "h",
	"Ḫ": "H",
	"ḫ": "h",
	"Ĩ": "I",
	"ĩ": "i",
	"Ī": "I",
	"ī": "i",
	"Ĭ": "I",
	"ĭ": "i",
	"Į": "I",
	"į": "i",
	"İ": "I",
	"ı": "i",
	"Ĳ": "IJ",
	"ĳ": "ij",
	"Ĵ": "J",
	"ĵ": "j",
	"Ķ": "K",
	"ķ": "k",
	"Ḱ": "K",
	"ḱ": "k",
	"K̆": "K",
	"k̆": "k",
	"Ĺ": "L",
	"ĺ": "l",
	"Ļ": "L",
	"ļ": "l",
	"Ľ": "L",
	"ľ": "l",
	"Ŀ": "L",
	"ŀ": "l",
	"Ł": "l",
	"ł": "l",
	"Ḿ": "M",
	"ḿ": "m",
	"M̆": "M",
	"m̆": "m",
	"Ń": "N",
	"ń": "n",
	"Ņ": "N",
	"ņ": "n",
	"Ň": "N",
	"ň": "n",
	"ŉ": "n",
	"N̆": "N",
	"n̆": "n",
	"Ō": "O",
	"ō": "o",
	"Ŏ": "O",
	"ŏ": "o",
	"Ő": "O",
	"ő": "o",
	"Œ": "OE",
	"œ": "oe",
	"P̆": "P",
	"p̆": "p",
	"Ŕ": "R",
	"ŕ": "r",
	"Ŗ": "R",
	"ŗ": "r",
	"Ř": "R",
	"ř": "r",
	"R̆": "R",
	"r̆": "r",
	"Ȓ": "R",
	"ȓ": "r",
	"Ś": "S",
	"ś": "s",
	"Ŝ": "S",
	"ŝ": "s",
	"Ş": "S",
	"Ș": "S",
	"ș": "s",
	"ş": "s",
	"Š": "S",
	"š": "s",
	"ß": "ss",
	"Ţ": "T",
	"ţ": "t",
	"ț": "t",
	"Ț": "T",
	"Ť": "T",
	"ť": "t",
	"Ŧ": "T",
	"ŧ": "t",
	"T̆": "T",
	"t̆": "t",
	"Ũ": "U",
	"ũ": "u",
	"Ū": "U",
	"ū": "u",
	"Ŭ": "U",
	"ŭ": "u",
	"Ů": "U",
	"ů": "u",
	"Ű": "U",
	"ű": "u",
	"Ų": "U",
	"ų": "u",
	"Ȗ": "U",
	"ȗ": "u",
	"V̆": "V",
	"v̆": "v",
	"Ŵ": "W",
	"ŵ": "w",
	"Ẃ": "W",
	"ẃ": "w",
	"X̆": "X",
	"x̆": "x",
	"Ŷ": "Y",
	"ŷ": "y",
	"Ÿ": "Y",
	"Y̆": "Y",
	"y̆": "y",
	"Ź": "Z",
	"ź": "z",
	"Ż": "Z",
	"ż": "z",
	"Ž": "Z",
	"ž": "z",
	"ſ": "s",
	"ƒ": "f",
	"Ơ": "O",
	"ơ": "o",
	"Ư": "U",
	"ư": "u",
	"Ǎ": "A",
	"ǎ": "a",
	"Ǐ": "I",
	"ǐ": "i",
	"Ǒ": "O",
	"ǒ": "o",
	"Ǔ": "U",
	"ǔ": "u",
	"Ǖ": "U",
	"ǖ": "u",
	"Ǘ": "U",
	"ǘ": "u",
	"Ǚ": "U",
	"ǚ": "u",
	"Ǜ": "U",
	"ǜ": "u",
	"Ứ": "U",
	"ứ": "u",
	"Ṹ": "U",
	"ṹ": "u",
	"Ǻ": "A",
	"ǻ": "a",
	"Ǽ": "AE",
	"ǽ": "ae",
	"Ǿ": "O",
	"ǿ": "o",
	"Þ": "TH",
	"þ": "th",
	"Ṕ": "P",
	"ṕ": "p",
	"Ṥ": "S",
	"ṥ": "s",
	"X́": "X",
	"x́": "x",
	"Ѓ": "Г",
	"ѓ": "г",
	"Ќ": "К",
	"ќ": "к",
	"A̋": "A",
	"a̋": "a",
	"E̋": "E",
	"e̋": "e",
	"I̋": "I",
	"i̋": "i",
	"Ǹ": "N",
	"ǹ": "n",
	"Ồ": "O",
	"ồ": "o",
	"Ṑ": "O",
	"ṑ": "o",
	"Ừ": "U",
	"ừ": "u",
	"Ẁ": "W",
	"ẁ": "w",
	"Ỳ": "Y",
	"ỳ": "y",
	"Ȁ": "A",
	"ȁ": "a",
	"Ȅ": "E",
	"ȅ": "e",
	"Ȉ": "I",
	"ȉ": "i",
	"Ȍ": "O",
	"ȍ": "o",
	"Ȑ": "R",
	"ȑ": "r",
	"Ȕ": "U",
	"ȕ": "u",
	"B̌": "B",
	"b̌": "b",
	"Č̣": "C",
	"č̣": "c",
	"Ê̌": "E",
	"ê̌": "e",
	"F̌": "F",
	"f̌": "f",
	"Ǧ": "G",
	"ǧ": "g",
	"Ȟ": "H",
	"ȟ": "h",
	"J̌": "J",
	"ǰ": "j",
	"Ǩ": "K",
	"ǩ": "k",
	"M̌": "M",
	"m̌": "m",
	"P̌": "P",
	"p̌": "p",
	"Q̌": "Q",
	"q̌": "q",
	"Ř̩": "R",
	"ř̩": "r",
	"Ṧ": "S",
	"ṧ": "s",
	"V̌": "V",
	"v̌": "v",
	"W̌": "W",
	"w̌": "w",
	"X̌": "X",
	"x̌": "x",
	"Y̌": "Y",
	"y̌": "y",
	"A̧": "A",
	"a̧": "a",
	"B̧": "B",
	"b̧": "b",
	"Ḑ": "D",
	"ḑ": "d",
	"Ȩ": "E",
	"ȩ": "e",
	"Ɛ̧": "E",
	"ɛ̧": "e",
	"Ḩ": "H",
	"ḩ": "h",
	"I̧": "I",
	"i̧": "i",
	"Ɨ̧": "I",
	"ɨ̧": "i",
	"M̧": "M",
	"m̧": "m",
	"O̧": "O",
	"o̧": "o",
	"Q̧": "Q",
	"q̧": "q",
	"U̧": "U",
	"u̧": "u",
	"X̧": "X",
	"x̧": "x",
	"Z̧": "Z",
	"z̧": "z",
	"й":"и",
	"Й":"И",
	"ё":"е",
	"Ё":"Е",
};

var chars = Object.keys(characterMap).join('|');
var allAccents = new RegExp(chars, 'g');
var firstAccent = new RegExp(chars, '');

function matcher(match) {
	return characterMap[match];
}

var removeAccents = function(string) {	
	return string.replace(allAccents, matcher);
};

var hasAccents = function(string) {
	return !!string.match(firstAccent);
};

module.exports = removeAccents;
module.exports.has = hasAccents;
module.exports.remove = removeAccents;


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "addQueryArgs": function() { return /* reexport */ addQueryArgs; },
  "buildQueryString": function() { return /* reexport */ buildQueryString; },
  "cleanForSlug": function() { return /* reexport */ cleanForSlug; },
  "filterURLForDisplay": function() { return /* reexport */ filterURLForDisplay; },
  "getAuthority": function() { return /* reexport */ getAuthority; },
  "getFilename": function() { return /* reexport */ getFilename; },
  "getFragment": function() { return /* reexport */ getFragment; },
  "getPath": function() { return /* reexport */ getPath; },
  "getPathAndQueryString": function() { return /* reexport */ getPathAndQueryString; },
  "getProtocol": function() { return /* reexport */ getProtocol; },
  "getQueryArg": function() { return /* reexport */ getQueryArg; },
  "getQueryArgs": function() { return /* reexport */ getQueryArgs; },
  "getQueryString": function() { return /* reexport */ getQueryString; },
  "hasQueryArg": function() { return /* reexport */ hasQueryArg; },
  "isEmail": function() { return /* reexport */ isEmail; },
  "isURL": function() { return /* reexport */ isURL; },
  "isValidAuthority": function() { return /* reexport */ isValidAuthority; },
  "isValidFragment": function() { return /* reexport */ isValidFragment; },
  "isValidPath": function() { return /* reexport */ isValidPath; },
  "isValidProtocol": function() { return /* reexport */ isValidProtocol; },
  "isValidQueryString": function() { return /* reexport */ isValidQueryString; },
  "normalizePath": function() { return /* reexport */ normalizePath; },
  "prependHTTP": function() { return /* reexport */ prependHTTP; },
  "prependHTTPS": function() { return /* reexport */ prependHTTPS; },
  "removeQueryArgs": function() { return /* reexport */ removeQueryArgs; },
  "safeDecodeURI": function() { return /* reexport */ safeDecodeURI; },
  "safeDecodeURIComponent": function() { return /* reexport */ safeDecodeURIComponent; }
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-url.js
/**
 * Determines whether the given string looks like a URL.
 *
 * @param {string} url The string to scrutinise.
 *
 * @example
 * ```js
 * const isURL = isURL( 'https://wordpress.org' ); // true
 * ```
 *
 * @see https://url.spec.whatwg.org/
 * @see https://url.spec.whatwg.org/#valid-url-string
 *
 * @return {boolean} Whether or not it looks like a URL.
 */
function isURL(url) {
  // A URL can be considered value if the `URL` constructor is able to parse
  // it. The constructor throws an error for an invalid URL.
  try {
    new URL(url);
    return true;
  } catch {
    return false;
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-email.js
const EMAIL_REGEXP = /^(mailto:)?[a-z0-9._%+-]+@[a-z0-9][a-z0-9.-]*\.[a-z]{2,63}$/i;
/**
 * Determines whether the given string looks like an email.
 *
 * @param {string} email The string to scrutinise.
 *
 * @example
 * ```js
 * const isEmail = isEmail( 'hello@wordpress.org' ); // true
 * ```
 *
 * @return {boolean} Whether or not it looks like an email.
 */

function isEmail(email) {
  return EMAIL_REGEXP.test(email);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-protocol.js
/**
 * Returns the protocol part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const protocol1 = getProtocol( 'tel:012345678' ); // 'tel:'
 * const protocol2 = getProtocol( 'https://wordpress.org' ); // 'https:'
 * ```
 *
 * @return {string|void} The protocol part of the URL.
 */
function getProtocol(url) {
  const matches = /^([^\s:]+:)/.exec(url);

  if (matches) {
    return matches[1];
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-protocol.js
/**
 * Tests if a url protocol is valid.
 *
 * @param {string} protocol The url protocol.
 *
 * @example
 * ```js
 * const isValid = isValidProtocol( 'https:' ); // true
 * const isNotValid = isValidProtocol( 'https :' ); // false
 * ```
 *
 * @return {boolean} True if the argument is a valid protocol (e.g. http:, tel:).
 */
function isValidProtocol(protocol) {
  if (!protocol) {
    return false;
  }

  return /^[a-z\-.\+]+[0-9]*:$/i.test(protocol);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-authority.js
/**
 * Returns the authority part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const authority1 = getAuthority( 'https://wordpress.org/help/' ); // 'wordpress.org'
 * const authority2 = getAuthority( 'https://localhost:8080/test/' ); // 'localhost:8080'
 * ```
 *
 * @return {string|void} The authority part of the URL.
 */
function getAuthority(url) {
  const matches = /^[^\/\s:]+:(?:\/\/)?\/?([^\/\s#?]+)[\/#?]{0,1}\S*$/.exec(url);

  if (matches) {
    return matches[1];
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-authority.js
/**
 * Checks for invalid characters within the provided authority.
 *
 * @param {string} authority A string containing the URL authority.
 *
 * @example
 * ```js
 * const isValid = isValidAuthority( 'wordpress.org' ); // true
 * const isNotValid = isValidAuthority( 'wordpress#org' ); // false
 * ```
 *
 * @return {boolean} True if the argument contains a valid authority.
 */
function isValidAuthority(authority) {
  if (!authority) {
    return false;
  }

  return /^[^\s#?]+$/.test(authority);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-path.js
/**
 * Returns the path part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const path1 = getPath( 'http://localhost:8080/this/is/a/test?query=true' ); // 'this/is/a/test'
 * const path2 = getPath( 'https://wordpress.org/help/faq/' ); // 'help/faq'
 * ```
 *
 * @return {string|void} The path part of the URL.
 */
function getPath(url) {
  const matches = /^[^\/\s:]+:(?:\/\/)?[^\/\s#?]+[\/]([^\s#?]+)[#?]{0,1}\S*$/.exec(url);

  if (matches) {
    return matches[1];
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-path.js
/**
 * Checks for invalid characters within the provided path.
 *
 * @param {string} path The URL path.
 *
 * @example
 * ```js
 * const isValid = isValidPath( 'test/path/' ); // true
 * const isNotValid = isValidPath( '/invalid?test/path/' ); // false
 * ```
 *
 * @return {boolean} True if the argument contains a valid path
 */
function isValidPath(path) {
  if (!path) {
    return false;
  }

  return /^[^\s#?]+$/.test(path);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-query-string.js
/**
 * Returns the query string part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const queryString = getQueryString( 'http://localhost:8080/this/is/a/test?query=true#fragment' ); // 'query=true'
 * ```
 *
 * @return {string|void} The query string part of the URL.
 */
function getQueryString(url) {
  let query;

  try {
    query = new URL(url, 'http://example.com').search.substring(1);
  } catch (error) {}

  if (query) {
    return query;
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/build-query-string.js
/**
 * Generates URL-encoded query string using input query data.
 *
 * It is intended to behave equivalent as PHP's `http_build_query`, configured
 * with encoding type PHP_QUERY_RFC3986 (spaces as `%20`).
 *
 * @example
 * ```js
 * const queryString = buildQueryString( {
 *    simple: 'is ok',
 *    arrays: [ 'are', 'fine', 'too' ],
 *    objects: {
 *       evenNested: {
 *          ok: 'yes',
 *       },
 *    },
 * } );
 * // "simple=is%20ok&arrays%5B0%5D=are&arrays%5B1%5D=fine&arrays%5B2%5D=too&objects%5BevenNested%5D%5Bok%5D=yes"
 * ```
 *
 * @param {Record<string,*>} data Data to encode.
 *
 * @return {string} Query string.
 */
function buildQueryString(data) {
  let string = '';
  const stack = Object.entries(data);
  let pair;

  while (pair = stack.shift()) {
    let [key, value] = pair; // Support building deeply nested data, from array or object values.

    const hasNestedData = Array.isArray(value) || value && value.constructor === Object;

    if (hasNestedData) {
      // Push array or object values onto the stack as composed of their
      // original key and nested index or key, retaining order by a
      // combination of Array#reverse and Array#unshift onto the stack.
      const valuePairs = Object.entries(value).reverse();

      for (const [member, memberValue] of valuePairs) {
        stack.unshift([`${key}[${member}]`, memberValue]);
      }
    } else if (value !== undefined) {
      // Null is treated as special case, equivalent to empty string.
      if (value === null) {
        value = '';
      }

      string += '&' + [key, value].map(encodeURIComponent).join('=');
    }
  } // Loop will concatenate with leading `&`, but it's only expected for all
  // but the first query parameter. This strips the leading `&`, while still
  // accounting for the case that the string may in-fact be empty.


  return string.substr(1);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-query-string.js
/**
 * Checks for invalid characters within the provided query string.
 *
 * @param {string} queryString The query string.
 *
 * @example
 * ```js
 * const isValid = isValidQueryString( 'query=true&another=false' ); // true
 * const isNotValid = isValidQueryString( 'query=true?another=false' ); // false
 * ```
 *
 * @return {boolean} True if the argument contains a valid query string.
 */
function isValidQueryString(queryString) {
  if (!queryString) {
    return false;
  }

  return /^[^\s#?\/]+$/.test(queryString);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-path-and-query-string.js
/**
 * Internal dependencies
 */

/**
 * Returns the path part and query string part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const pathAndQueryString1 = getPathAndQueryString( 'http://localhost:8080/this/is/a/test?query=true' ); // '/this/is/a/test?query=true'
 * const pathAndQueryString2 = getPathAndQueryString( 'https://wordpress.org/help/faq/' ); // '/help/faq'
 * ```
 *
 * @return {string} The path part and query string part of the URL.
 */

function getPathAndQueryString(url) {
  const path = getPath(url);
  const queryString = getQueryString(url);
  let value = '/';
  if (path) value += path;
  if (queryString) value += `?${queryString}`;
  return value;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-fragment.js
/**
 * Returns the fragment part of the URL.
 *
 * @param {string} url The full URL
 *
 * @example
 * ```js
 * const fragment1 = getFragment( 'http://localhost:8080/this/is/a/test?query=true#fragment' ); // '#fragment'
 * const fragment2 = getFragment( 'https://wordpress.org#another-fragment?query=true' ); // '#another-fragment'
 * ```
 *
 * @return {string|void} The fragment part of the URL.
 */
function getFragment(url) {
  const matches = /^\S+?(#[^\s\?]*)/.exec(url);

  if (matches) {
    return matches[1];
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-fragment.js
/**
 * Checks for invalid characters within the provided fragment.
 *
 * @param {string} fragment The url fragment.
 *
 * @example
 * ```js
 * const isValid = isValidFragment( '#valid-fragment' ); // true
 * const isNotValid = isValidFragment( '#invalid-#fragment' ); // false
 * ```
 *
 * @return {boolean} True if the argument contains a valid fragment.
 */
function isValidFragment(fragment) {
  if (!fragment) {
    return false;
  }

  return /^#[^\s#?\/]*$/.test(fragment);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/safe-decode-uri-component.js
/**
 * Safely decodes a URI component with `decodeURIComponent`. Returns the URI component unmodified if
 * `decodeURIComponent` throws an error.
 *
 * @param {string} uriComponent URI component to decode.
 *
 * @return {string} Decoded URI component if possible.
 */
function safeDecodeURIComponent(uriComponent) {
  try {
    return decodeURIComponent(uriComponent);
  } catch (uriComponentError) {
    return uriComponent;
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-query-args.js
/**
 * Internal dependencies
 */


/** @typedef {import('./get-query-arg').QueryArgParsed} QueryArgParsed */

/**
 * @typedef {Record<string,QueryArgParsed>} QueryArgs
 */

/**
 * Sets a value in object deeply by a given array of path segments. Mutates the
 * object reference.
 *
 * @param {Record<string,*>} object Object in which to assign.
 * @param {string[]}         path   Path segment at which to set value.
 * @param {*}                value  Value to set.
 */

function setPath(object, path, value) {
  const length = path.length;
  const lastIndex = length - 1;

  for (let i = 0; i < length; i++) {
    let key = path[i];

    if (!key && Array.isArray(object)) {
      // If key is empty string and next value is array, derive key from
      // the current length of the array.
      key = object.length.toString();
    }

    key = ['__proto__', 'constructor', 'prototype'].includes(key) ? key.toUpperCase() : key; // If the next key in the path is numeric (or empty string), it will be
    // created as an array. Otherwise, it will be created as an object.

    const isNextKeyArrayIndex = !isNaN(Number(path[i + 1]));
    object[key] = i === lastIndex ? // If at end of path, assign the intended value.
    value : // Otherwise, advance to the next object in the path, creating
    // it if it does not yet exist.
    object[key] || (isNextKeyArrayIndex ? [] : {});

    if (Array.isArray(object[key]) && !isNextKeyArrayIndex) {
      // If we current key is non-numeric, but the next value is an
      // array, coerce the value to an object.
      object[key] = { ...object[key]
      };
    } // Update working reference object to the next in the path.


    object = object[key];
  }
}
/**
 * Returns an object of query arguments of the given URL. If the given URL is
 * invalid or has no querystring, an empty object is returned.
 *
 * @param {string} url URL.
 *
 * @example
 * ```js
 * const foo = getQueryArgs( 'https://wordpress.org?foo=bar&bar=baz' );
 * // { "foo": "bar", "bar": "baz" }
 * ```
 *
 * @return {QueryArgs} Query args object.
 */


function getQueryArgs(url) {
  return (getQueryString(url) || '' // Normalize space encoding, accounting for PHP URL encoding
  // corresponding to `application/x-www-form-urlencoded`.
  //
  // See: https://tools.ietf.org/html/rfc1866#section-8.2.1
  ).replace(/\+/g, '%20').split('&').reduce((accumulator, keyValue) => {
    const [key, value = ''] = keyValue.split('=') // Filtering avoids decoding as `undefined` for value, where
    // default is restored in destructuring assignment.
    .filter(Boolean).map(safeDecodeURIComponent);

    if (key) {
      const segments = key.replace(/\]/g, '').split('[');
      setPath(accumulator, segments, value);
    }

    return accumulator;
  }, Object.create(null));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/add-query-args.js
/**
 * Internal dependencies
 */


/**
 * Appends arguments as querystring to the provided URL. If the URL already
 * includes query arguments, the arguments are merged with (and take precedent
 * over) the existing set.
 *
 * @param {string} [url=''] URL to which arguments should be appended. If omitted,
 *                          only the resulting querystring is returned.
 * @param {Object} [args]   Query arguments to apply to URL.
 *
 * @example
 * ```js
 * const newURL = addQueryArgs( 'https://google.com', { q: 'test' } ); // https://google.com/?q=test
 * ```
 *
 * @return {string} URL with arguments applied.
 */

function addQueryArgs(url = '', args) {
  // If no arguments are to be appended, return original URL.
  if (!args || !Object.keys(args).length) {
    return url;
  }

  let baseUrl = url; // Determine whether URL already had query arguments.

  const queryStringIndex = url.indexOf('?');

  if (queryStringIndex !== -1) {
    // Merge into existing query arguments.
    args = Object.assign(getQueryArgs(url), args); // Change working base URL to omit previous query arguments.

    baseUrl = baseUrl.substr(0, queryStringIndex);
  }

  return baseUrl + '?' + buildQueryString(args);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-query-arg.js
/**
 * Internal dependencies
 */

/**
 * @typedef {{[key: string]: QueryArgParsed}} QueryArgObject
 */

/**
 * @typedef {string|string[]|QueryArgObject} QueryArgParsed
 */

/**
 * Returns a single query argument of the url
 *
 * @param {string} url URL.
 * @param {string} arg Query arg name.
 *
 * @example
 * ```js
 * const foo = getQueryArg( 'https://wordpress.org?foo=bar&bar=baz', 'foo' ); // bar
 * ```
 *
 * @return {QueryArgParsed|void} Query arg value.
 */

function getQueryArg(url, arg) {
  return getQueryArgs(url)[arg];
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/has-query-arg.js
/**
 * Internal dependencies
 */

/**
 * Determines whether the URL contains a given query arg.
 *
 * @param {string} url URL.
 * @param {string} arg Query arg name.
 *
 * @example
 * ```js
 * const hasBar = hasQueryArg( 'https://wordpress.org?foo=bar&bar=baz', 'bar' ); // true
 * ```
 *
 * @return {boolean} Whether or not the URL contains the query arg.
 */

function hasQueryArg(url, arg) {
  return getQueryArg(url, arg) !== undefined;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/remove-query-args.js
/**
 * Internal dependencies
 */


/**
 * Removes arguments from the query string of the url
 *
 * @param {string}    url  URL.
 * @param {...string} args Query Args.
 *
 * @example
 * ```js
 * const newUrl = removeQueryArgs( 'https://wordpress.org?foo=bar&bar=baz&baz=foobar', 'foo', 'bar' ); // https://wordpress.org?baz=foobar
 * ```
 *
 * @return {string} Updated URL.
 */

function removeQueryArgs(url, ...args) {
  const queryStringIndex = url.indexOf('?');

  if (queryStringIndex === -1) {
    return url;
  }

  const query = getQueryArgs(url);
  const baseURL = url.substr(0, queryStringIndex);
  args.forEach(arg => delete query[arg]);
  const queryString = buildQueryString(query);
  return queryString ? baseURL + '?' + queryString : baseURL;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/prepend-http.js
/**
 * Internal dependencies
 */

const USABLE_HREF_REGEXP = /^(?:[a-z]+:|#|\?|\.|\/)/i;
/**
 * Prepends "http://" to a url, if it looks like something that is meant to be a TLD.
 *
 * @param {string} url The URL to test.
 *
 * @example
 * ```js
 * const actualURL = prependHTTP( 'wordpress.org' ); // http://wordpress.org
 * ```
 *
 * @return {string} The updated URL.
 */

function prependHTTP(url) {
  if (!url) {
    return url;
  }

  url = url.trim();

  if (!USABLE_HREF_REGEXP.test(url) && !isEmail(url)) {
    return 'http://' + url;
  }

  return url;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/safe-decode-uri.js
/**
 * Safely decodes a URI with `decodeURI`. Returns the URI unmodified if
 * `decodeURI` throws an error.
 *
 * @param {string} uri URI to decode.
 *
 * @example
 * ```js
 * const badUri = safeDecodeURI( '%z' ); // does not throw an Error, simply returns '%z'
 * ```
 *
 * @return {string} Decoded URI if possible.
 */
function safeDecodeURI(uri) {
  try {
    return decodeURI(uri);
  } catch (uriError) {
    return uri;
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/filter-url-for-display.js
/**
 * Returns a URL for display.
 *
 * @param {string}      url       Original URL.
 * @param {number|null} maxLength URL length.
 *
 * @example
 * ```js
 * const displayUrl = filterURLForDisplay( 'https://www.wordpress.org/gutenberg/' ); // wordpress.org/gutenberg
 * const imageUrl = filterURLForDisplay( 'https://www.wordpress.org/wp-content/uploads/img.png', 20 ); // …ent/uploads/img.png
 * ```
 *
 * @return {string} Displayed URL.
 */
function filterURLForDisplay(url, maxLength = null) {
  // Remove protocol and www prefixes.
  let filteredURL = url.replace(/^(?:https?:)\/\/(?:www\.)?/, ''); // Ends with / and only has that single slash, strip it.

  if (filteredURL.match(/^[^\/]+\/$/)) {
    filteredURL = filteredURL.replace('/', '');
  }

  const mediaRegexp = /([\w|:])*\.(?:jpg|jpeg|gif|png|svg)/;

  if (!maxLength || filteredURL.length <= maxLength || !filteredURL.match(mediaRegexp)) {
    return filteredURL;
  } // If the file is not greater than max length, return last portion of URL.


  filteredURL = filteredURL.split('?')[0];
  const urlPieces = filteredURL.split('/');
  const file = urlPieces[urlPieces.length - 1];

  if (file.length <= maxLength) {
    return '…' + filteredURL.slice(-maxLength);
  } // If the file is greater than max length, truncate the file.


  const index = file.lastIndexOf('.');
  const [fileName, extension] = [file.slice(0, index), file.slice(index + 1)];
  const truncatedFile = fileName.slice(-3) + '.' + extension;
  return file.slice(0, maxLength - truncatedFile.length - 1) + '…' + truncatedFile;
}

// EXTERNAL MODULE: ./node_modules/remove-accents/index.js
var remove_accents = __webpack_require__(4793);
var remove_accents_default = /*#__PURE__*/__webpack_require__.n(remove_accents);
;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/clean-for-slug.js
/**
 * External dependencies
 */

/**
 * Performs some basic cleanup of a string for use as a post slug.
 *
 * This replicates some of what `sanitize_title()` does in WordPress core, but
 * is only designed to approximate what the slug will be.
 *
 * Converts Latin-1 Supplement and Latin Extended-A letters to basic Latin
 * letters. Removes combining diacritical marks. Converts whitespace, periods,
 * and forward slashes to hyphens. Removes any remaining non-word characters
 * except hyphens. Converts remaining string to lowercase. It does not account
 * for octets, HTML entities, or other encoded characters.
 *
 * @param {string} string Title or slug to be processed.
 *
 * @return {string} Processed string.
 */

function cleanForSlug(string) {
  if (!string) {
    return '';
  }

  return remove_accents_default()(string) // Convert each group of whitespace, periods, and forward slashes to a hyphen.
  .replace(/[\s\./]+/g, '-') // Remove anything that's not a letter, number, underscore or hyphen.
  .replace(/[^\p{L}\p{N}_-]+/gu, '') // Convert to lowercase
  .toLowerCase() // Replace multiple hyphens with a single one.
  .replace(/-+/g, '-') // Remove any remaining leading or trailing hyphens.
  .replace(/(^-+)|(-+$)/g, '');
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-filename.js
/**
 * Returns the filename part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const filename1 = getFilename( 'http://localhost:8080/this/is/a/test.jpg' ); // 'test.jpg'
 * const filename2 = getFilename( '/this/is/a/test.png' ); // 'test.png'
 * ```
 *
 * @return {string|void} The filename part of the URL.
 */
function getFilename(url) {
  let filename;

  try {
    filename = new URL(url, 'http://example.com').pathname.split('/').pop();
  } catch (error) {}

  if (filename) {
    return filename;
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/normalize-path.js
/**
 * Given a path, returns a normalized path where equal query parameter values
 * will be treated as identical, regardless of order they appear in the original
 * text.
 *
 * @param {string} path Original path.
 *
 * @return {string} Normalized path.
 */
function normalizePath(path) {
  const splitted = path.split('?');
  const query = splitted[1];
  const base = splitted[0];

  if (!query) {
    return base;
  } // 'b=1%2C2&c=2&a=5'


  return base + '?' + query // [ 'b=1%2C2', 'c=2', 'a=5' ]
  .split('&') // [ [ 'b, '1%2C2' ], [ 'c', '2' ], [ 'a', '5' ] ]
  .map(entry => entry.split('=')) // [ [ 'b', '1,2' ], [ 'c', '2' ], [ 'a', '5' ] ]
  .map(pair => pair.map(decodeURIComponent)) // [ [ 'a', '5' ], [ 'b, '1,2' ], [ 'c', '2' ] ]
  .sort((a, b) => a[0].localeCompare(b[0])) // [ [ 'a', '5' ], [ 'b, '1%2C2' ], [ 'c', '2' ] ]
  .map(pair => pair.map(encodeURIComponent)) // [ 'a=5', 'b=1%2C2', 'c=2' ]
  .map(pair => pair.join('=')) // 'a=5&b=1%2C2&c=2'
  .join('&');
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/prepend-https.js
/**
 * Internal dependencies
 */

/**
 * Prepends "https://" to a url, if it looks like something that is meant to be a TLD.
 *
 * Note: this will not replace "http://" with "https://".
 *
 * @param {string} url The URL to test.
 *
 * @example
 * ```js
 * const actualURL = prependHTTPS( 'wordpress.org' ); // https://wordpress.org
 * ```
 *
 * @return {string} The updated URL.
 */

function prependHTTPS(url) {
  if (!url) {
    return url;
  } // If url starts with http://, return it as is.


  if (url.startsWith('http://')) {
    return url;
  }

  url = prependHTTP(url);
  return url.replace(/^http:/, 'https:');
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/index.js




























}();
(window.wp = window.wp || {}).url = __webpack_exports__;
/******/ })()
;