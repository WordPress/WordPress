/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 9681:
/***/ ((module) => {

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
	"Ả": "A",
	"Ạ": "A",
	"Ẩ": "A",
	"Ẫ": "A",
	"Ậ": "A",
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
	"Ẻ": "E",
	"Ẽ": "E",
	"Ẹ": "E",
	"Ể": "E",
	"Ễ": "E",
	"Ệ": "E",
	"Ì": "I",
	"Í": "I",
	"Î": "I",
	"Ï": "I",
	"Ḯ": "I",
	"Ȋ": "I",
	"Ỉ": "I",
	"Ị": "I",
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
	"Ỏ": "O",
	"Ọ": "O",
	"Ổ": "O",
	"Ỗ": "O",
	"Ộ": "O",
	"Ờ": "O",
	"Ở": "O",
	"Ỡ": "O",
	"Ớ": "O",
	"Ợ": "O",
	"Ù": "U",
	"Ú": "U",
	"Û": "U",
	"Ü": "U",
	"Ủ": "U",
	"Ụ": "U",
	"Ử": "U",
	"Ữ": "U",
	"Ự": "U",
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
	"ả": "a",
	"ạ": "a",
	"ẩ": "a",
	"ẫ": "a",
	"ậ": "a",
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
	"ẻ": "e",
	"ẽ": "e",
	"ẹ": "e",
	"ể": "e",
	"ễ": "e",
	"ệ": "e",
	"ì": "i",
	"í": "i",
	"î": "i",
	"ï": "i",
	"ḯ": "i",
	"ȋ": "i",
	"ỉ": "i",
	"ị": "i",
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
	"ỏ": "o",
	"ọ": "o",
	"ổ": "o",
	"ỗ": "o",
	"ộ": "o",
	"ờ": "o",
	"ở": "o",
	"ỡ": "o",
	"ớ": "o",
	"ợ": "o",
	"ù": "u",
	"ú": "u",
	"û": "u",
	"ü": "u",
	"ủ": "u",
	"ụ": "u",
	"ử": "u",
	"ữ": "u",
	"ự": "u",
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
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
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
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  addQueryArgs: () => (/* reexport */ addQueryArgs),
  buildQueryString: () => (/* reexport */ buildQueryString),
  cleanForSlug: () => (/* reexport */ cleanForSlug),
  filterURLForDisplay: () => (/* reexport */ filterURLForDisplay),
  getAuthority: () => (/* reexport */ getAuthority),
  getFilename: () => (/* reexport */ getFilename),
  getFragment: () => (/* reexport */ getFragment),
  getPath: () => (/* reexport */ getPath),
  getPathAndQueryString: () => (/* reexport */ getPathAndQueryString),
  getProtocol: () => (/* reexport */ getProtocol),
  getQueryArg: () => (/* reexport */ getQueryArg),
  getQueryArgs: () => (/* reexport */ getQueryArgs),
  getQueryString: () => (/* reexport */ getQueryString),
  hasQueryArg: () => (/* reexport */ hasQueryArg),
  isEmail: () => (/* reexport */ isEmail),
  isPhoneNumber: () => (/* reexport */ isPhoneNumber),
  isURL: () => (/* reexport */ isURL),
  isValidAuthority: () => (/* reexport */ isValidAuthority),
  isValidFragment: () => (/* reexport */ isValidFragment),
  isValidPath: () => (/* reexport */ isValidPath),
  isValidProtocol: () => (/* reexport */ isValidProtocol),
  isValidQueryString: () => (/* reexport */ isValidQueryString),
  normalizePath: () => (/* reexport */ normalizePath),
  prependHTTP: () => (/* reexport */ prependHTTP),
  prependHTTPS: () => (/* reexport */ prependHTTPS),
  removeQueryArgs: () => (/* reexport */ removeQueryArgs),
  safeDecodeURI: () => (/* reexport */ safeDecodeURI),
  safeDecodeURIComponent: () => (/* reexport */ safeDecodeURIComponent)
});

;// ./node_modules/@wordpress/url/build-module/is-url.js
function isURL(url) {
  try {
    new URL(url);
    return true;
  } catch {
    return false;
  }
}


;// ./node_modules/@wordpress/url/build-module/is-email.js
const EMAIL_REGEXP = /^(mailto:)?[a-z0-9._%+-]+@[a-z0-9][a-z0-9.-]*\.[a-z]{2,63}$/i;
function isEmail(email) {
  return EMAIL_REGEXP.test(email);
}


;// ./node_modules/@wordpress/url/build-module/is-phone-number.js
const PHONE_REGEXP = /^(tel:)?(\+)?\d{6,15}$/;
function isPhoneNumber(phoneNumber) {
  phoneNumber = phoneNumber.replace(/[-.() ]/g, "");
  return PHONE_REGEXP.test(phoneNumber);
}


;// ./node_modules/@wordpress/url/build-module/get-protocol.js
function getProtocol(url) {
  const matches = /^([^\s:]+:)/.exec(url);
  if (matches) {
    return matches[1];
  }
}


;// ./node_modules/@wordpress/url/build-module/is-valid-protocol.js
function isValidProtocol(protocol) {
  if (!protocol) {
    return false;
  }
  return /^[a-z\-.\+]+[0-9]*:$/i.test(protocol);
}


;// ./node_modules/@wordpress/url/build-module/get-authority.js
function getAuthority(url) {
  const matches = /^[^\/\s:]+:(?:\/\/)?\/?([^\/\s#?]+)[\/#?]{0,1}\S*$/.exec(
    url
  );
  if (matches) {
    return matches[1];
  }
}


;// ./node_modules/@wordpress/url/build-module/is-valid-authority.js
function isValidAuthority(authority) {
  if (!authority) {
    return false;
  }
  return /^[^\s#?]+$/.test(authority);
}


;// ./node_modules/@wordpress/url/build-module/get-path.js
function getPath(url) {
  const matches = /^[^\/\s:]+:(?:\/\/)?[^\/\s#?]+[\/]([^\s#?]+)[#?]{0,1}\S*$/.exec(url);
  if (matches) {
    return matches[1];
  }
}


;// ./node_modules/@wordpress/url/build-module/is-valid-path.js
function isValidPath(path) {
  if (!path) {
    return false;
  }
  return /^[^\s#?]+$/.test(path);
}


;// ./node_modules/@wordpress/url/build-module/get-query-string.js
function getQueryString(url) {
  let query;
  try {
    query = new URL(url, "http://example.com").search.substring(1);
  } catch (error) {
  }
  if (query) {
    return query;
  }
}


;// ./node_modules/@wordpress/url/build-module/build-query-string.js
function buildQueryString(data) {
  let string = "";
  const stack = Object.entries(data);
  let pair;
  while (pair = stack.shift()) {
    let [key, value] = pair;
    const hasNestedData = Array.isArray(value) || value && value.constructor === Object;
    if (hasNestedData) {
      const valuePairs = Object.entries(value).reverse();
      for (const [member, memberValue] of valuePairs) {
        stack.unshift([`${key}[${member}]`, memberValue]);
      }
    } else if (value !== void 0) {
      if (value === null) {
        value = "";
      }
      string += "&" + [key, String(value)].map(encodeURIComponent).join("=");
    }
  }
  return string.substr(1);
}


;// ./node_modules/@wordpress/url/build-module/is-valid-query-string.js
function isValidQueryString(queryString) {
  if (!queryString) {
    return false;
  }
  return /^[^\s#?\/]+$/.test(queryString);
}


;// ./node_modules/@wordpress/url/build-module/get-path-and-query-string.js

function getPathAndQueryString(url) {
  const path = getPath(url);
  const queryString = getQueryString(url);
  let value = "/";
  if (path) {
    value += path;
  }
  if (queryString) {
    value += `?${queryString}`;
  }
  return value;
}


;// ./node_modules/@wordpress/url/build-module/get-fragment.js
function getFragment(url) {
  const matches = /^\S+?(#[^\s\?]*)/.exec(url);
  if (matches) {
    return matches[1];
  }
}


;// ./node_modules/@wordpress/url/build-module/is-valid-fragment.js
function isValidFragment(fragment) {
  if (!fragment) {
    return false;
  }
  return /^#[^\s#?\/]*$/.test(fragment);
}


;// ./node_modules/@wordpress/url/build-module/safe-decode-uri-component.js
function safeDecodeURIComponent(uriComponent) {
  try {
    return decodeURIComponent(uriComponent);
  } catch (uriComponentError) {
    return uriComponent;
  }
}


;// ./node_modules/@wordpress/url/build-module/get-query-args.js


function setPath(object, path, value) {
  const length = path.length;
  const lastIndex = length - 1;
  for (let i = 0; i < length; i++) {
    let key = path[i];
    if (!key && Array.isArray(object)) {
      key = object.length.toString();
    }
    key = ["__proto__", "constructor", "prototype"].includes(key) ? key.toUpperCase() : key;
    const isNextKeyArrayIndex = !isNaN(Number(path[i + 1]));
    object[key] = i === lastIndex ? (
      // If at end of path, assign the intended value.
      value
    ) : (
      // Otherwise, advance to the next object in the path, creating
      // it if it does not yet exist.
      object[key] || (isNextKeyArrayIndex ? [] : {})
    );
    if (Array.isArray(object[key]) && !isNextKeyArrayIndex) {
      object[key] = { ...object[key] };
    }
    object = object[key];
  }
}
function getQueryArgs(url) {
  return (getQueryString(url) || "").replace(/\+/g, "%20").split("&").reduce((accumulator, keyValue) => {
    const [key, value = ""] = keyValue.split("=").filter(Boolean).map(safeDecodeURIComponent);
    if (key) {
      const segments = key.replace(/\]/g, "").split("[");
      setPath(accumulator, segments, value);
    }
    return accumulator;
  }, /* @__PURE__ */ Object.create(null));
}


;// ./node_modules/@wordpress/url/build-module/add-query-args.js



function addQueryArgs(url = "", args) {
  if (!args || !Object.keys(args).length) {
    return url;
  }
  const fragment = getFragment(url) || "";
  let baseUrl = url.replace(fragment, "");
  const queryStringIndex = url.indexOf("?");
  if (queryStringIndex !== -1) {
    args = Object.assign(getQueryArgs(url), args);
    baseUrl = baseUrl.substr(0, queryStringIndex);
  }
  return baseUrl + "?" + buildQueryString(args) + fragment;
}


;// ./node_modules/@wordpress/url/build-module/get-query-arg.js

function getQueryArg(url, arg) {
  return getQueryArgs(url)[arg];
}


;// ./node_modules/@wordpress/url/build-module/has-query-arg.js

function hasQueryArg(url, arg) {
  return getQueryArg(url, arg) !== void 0;
}


;// ./node_modules/@wordpress/url/build-module/remove-query-args.js


function removeQueryArgs(url, ...args) {
  const fragment = url.replace(/^[^#]*/, "");
  url = url.replace(/#.*/, "");
  const queryStringIndex = url.indexOf("?");
  if (queryStringIndex === -1) {
    return url + fragment;
  }
  const query = getQueryArgs(url);
  const baseURL = url.substr(0, queryStringIndex);
  args.forEach((arg) => delete query[arg]);
  const queryString = buildQueryString(query);
  const updatedUrl = queryString ? baseURL + "?" + queryString : baseURL;
  return updatedUrl + fragment;
}


;// ./node_modules/@wordpress/url/build-module/prepend-http.js

const USABLE_HREF_REGEXP = /^(?:[a-z]+:|#|\?|\.|\/)/i;
function prependHTTP(url) {
  if (!url) {
    return url;
  }
  url = url.trim();
  if (!USABLE_HREF_REGEXP.test(url) && !isEmail(url)) {
    return "http://" + url;
  }
  return url;
}


;// ./node_modules/@wordpress/url/build-module/safe-decode-uri.js
function safeDecodeURI(uri) {
  try {
    return decodeURI(uri);
  } catch (uriError) {
    return uri;
  }
}


;// ./node_modules/@wordpress/url/build-module/filter-url-for-display.js
function filterURLForDisplay(url, maxLength = null) {
  if (!url) {
    return "";
  }
  let filteredURL = url.replace(/^[a-z\-.\+]+[0-9]*:(\/\/)?/i, "").replace(/^www\./i, "");
  if (filteredURL.match(/^[^\/]+\/$/)) {
    filteredURL = filteredURL.replace("/", "");
  }
  const fileRegexp = /\/([^\/?]+)\.(?:[\w]+)(?=\?|$)/;
  if (!maxLength || filteredURL.length <= maxLength || !filteredURL.match(fileRegexp)) {
    return filteredURL;
  }
  filteredURL = filteredURL.split("?")[0];
  const urlPieces = filteredURL.split("/");
  const file = urlPieces[urlPieces.length - 1];
  if (file.length <= maxLength) {
    return "\u2026" + filteredURL.slice(-maxLength);
  }
  const index = file.lastIndexOf(".");
  const [fileName, extension] = [
    file.slice(0, index),
    file.slice(index + 1)
  ];
  const truncatedFile = fileName.slice(-3) + "." + extension;
  return file.slice(0, maxLength - truncatedFile.length - 1) + "\u2026" + truncatedFile;
}


// EXTERNAL MODULE: ./node_modules/remove-accents/index.js
var remove_accents = __webpack_require__(9681);
var remove_accents_default = /*#__PURE__*/__webpack_require__.n(remove_accents);
;// ./node_modules/@wordpress/url/build-module/clean-for-slug.js

function cleanForSlug(string) {
  if (!string) {
    return "";
  }
  return remove_accents_default()(string).replace(/(&nbsp;|&ndash;|&mdash;)/g, "-").replace(/[\s\./]+/g, "-").replace(/&\S+?;/g, "").replace(/[^\p{L}\p{N}_-]+/gu, "").toLowerCase().replace(/-+/g, "-").replace(/(^-+)|(-+$)/g, "");
}


;// ./node_modules/@wordpress/url/build-module/get-filename.js
function getFilename(url) {
  let filename;
  if (!url) {
    return;
  }
  try {
    filename = new URL(url, "http://example.com").pathname.split("/").pop();
  } catch (error) {
  }
  if (filename) {
    return filename;
  }
}


;// ./node_modules/@wordpress/url/build-module/normalize-path.js
function normalizePath(path) {
  const split = path.split("?");
  const query = split[1];
  const base = split[0];
  if (!query) {
    return base;
  }
  return base + "?" + query.split("&").map((entry) => entry.split("=")).map((pair) => pair.map(decodeURIComponent)).sort((a, b) => a[0].localeCompare(b[0])).map((pair) => pair.map(encodeURIComponent)).map((pair) => pair.join("=")).join("&");
}


;// ./node_modules/@wordpress/url/build-module/prepend-https.js

function prependHTTPS(url) {
  if (!url) {
    return url;
  }
  if (url.startsWith("http://")) {
    return url;
  }
  url = prependHTTP(url);
  return url.replace(/^http:/, "https:");
}


;// ./node_modules/@wordpress/url/build-module/index.js






























})();

(window.wp = window.wp || {}).url = __webpack_exports__;
/******/ })()
;