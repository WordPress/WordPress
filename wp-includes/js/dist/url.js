this["wp"] = this["wp"] || {}; this["wp"]["url"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 300);
/******/ })
/************************************************************************/
/******/ ({

/***/ 179:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var has = Object.prototype.hasOwnProperty;

var hexTable = (function () {
    var array = [];
    for (var i = 0; i < 256; ++i) {
        array.push('%' + ((i < 16 ? '0' : '') + i.toString(16)).toUpperCase());
    }

    return array;
}());

var compactQueue = function compactQueue(queue) {
    while (queue.length > 1) {
        var item = queue.pop();
        var obj = item.obj[item.prop];

        if (Array.isArray(obj)) {
            var compacted = [];

            for (var j = 0; j < obj.length; ++j) {
                if (typeof obj[j] !== 'undefined') {
                    compacted.push(obj[j]);
                }
            }

            item.obj[item.prop] = compacted;
        }
    }
};

var arrayToObject = function arrayToObject(source, options) {
    var obj = options && options.plainObjects ? Object.create(null) : {};
    for (var i = 0; i < source.length; ++i) {
        if (typeof source[i] !== 'undefined') {
            obj[i] = source[i];
        }
    }

    return obj;
};

var merge = function merge(target, source, options) {
    if (!source) {
        return target;
    }

    if (typeof source !== 'object') {
        if (Array.isArray(target)) {
            target.push(source);
        } else if (typeof target === 'object') {
            if ((options && (options.plainObjects || options.allowPrototypes)) || !has.call(Object.prototype, source)) {
                target[source] = true;
            }
        } else {
            return [target, source];
        }

        return target;
    }

    if (typeof target !== 'object') {
        return [target].concat(source);
    }

    var mergeTarget = target;
    if (Array.isArray(target) && !Array.isArray(source)) {
        mergeTarget = arrayToObject(target, options);
    }

    if (Array.isArray(target) && Array.isArray(source)) {
        source.forEach(function (item, i) {
            if (has.call(target, i)) {
                if (target[i] && typeof target[i] === 'object') {
                    target[i] = merge(target[i], item, options);
                } else {
                    target.push(item);
                }
            } else {
                target[i] = item;
            }
        });
        return target;
    }

    return Object.keys(source).reduce(function (acc, key) {
        var value = source[key];

        if (has.call(acc, key)) {
            acc[key] = merge(acc[key], value, options);
        } else {
            acc[key] = value;
        }
        return acc;
    }, mergeTarget);
};

var assign = function assignSingleSource(target, source) {
    return Object.keys(source).reduce(function (acc, key) {
        acc[key] = source[key];
        return acc;
    }, target);
};

var decode = function (str, decoder, charset) {
    var strWithoutPlus = str.replace(/\+/g, ' ');
    if (charset === 'iso-8859-1') {
        // unescape never throws, no try...catch needed:
        return strWithoutPlus.replace(/%[0-9a-f]{2}/gi, unescape);
    }
    // utf-8
    try {
        return decodeURIComponent(strWithoutPlus);
    } catch (e) {
        return strWithoutPlus;
    }
};

var encode = function encode(str, defaultEncoder, charset) {
    // This code was originally written by Brian White (mscdex) for the io.js core querystring library.
    // It has been adapted here for stricter adherence to RFC 3986
    if (str.length === 0) {
        return str;
    }

    var string = typeof str === 'string' ? str : String(str);

    if (charset === 'iso-8859-1') {
        return escape(string).replace(/%u[0-9a-f]{4}/gi, function ($0) {
            return '%26%23' + parseInt($0.slice(2), 16) + '%3B';
        });
    }

    var out = '';
    for (var i = 0; i < string.length; ++i) {
        var c = string.charCodeAt(i);

        if (
            c === 0x2D // -
            || c === 0x2E // .
            || c === 0x5F // _
            || c === 0x7E // ~
            || (c >= 0x30 && c <= 0x39) // 0-9
            || (c >= 0x41 && c <= 0x5A) // a-z
            || (c >= 0x61 && c <= 0x7A) // A-Z
        ) {
            out += string.charAt(i);
            continue;
        }

        if (c < 0x80) {
            out = out + hexTable[c];
            continue;
        }

        if (c < 0x800) {
            out = out + (hexTable[0xC0 | (c >> 6)] + hexTable[0x80 | (c & 0x3F)]);
            continue;
        }

        if (c < 0xD800 || c >= 0xE000) {
            out = out + (hexTable[0xE0 | (c >> 12)] + hexTable[0x80 | ((c >> 6) & 0x3F)] + hexTable[0x80 | (c & 0x3F)]);
            continue;
        }

        i += 1;
        c = 0x10000 + (((c & 0x3FF) << 10) | (string.charCodeAt(i) & 0x3FF));
        out += hexTable[0xF0 | (c >> 18)]
            + hexTable[0x80 | ((c >> 12) & 0x3F)]
            + hexTable[0x80 | ((c >> 6) & 0x3F)]
            + hexTable[0x80 | (c & 0x3F)];
    }

    return out;
};

var compact = function compact(value) {
    var queue = [{ obj: { o: value }, prop: 'o' }];
    var refs = [];

    for (var i = 0; i < queue.length; ++i) {
        var item = queue[i];
        var obj = item.obj[item.prop];

        var keys = Object.keys(obj);
        for (var j = 0; j < keys.length; ++j) {
            var key = keys[j];
            var val = obj[key];
            if (typeof val === 'object' && val !== null && refs.indexOf(val) === -1) {
                queue.push({ obj: obj, prop: key });
                refs.push(val);
            }
        }
    }

    compactQueue(queue);

    return value;
};

var isRegExp = function isRegExp(obj) {
    return Object.prototype.toString.call(obj) === '[object RegExp]';
};

var isBuffer = function isBuffer(obj) {
    if (obj === null || typeof obj === 'undefined') {
        return false;
    }

    return !!(obj.constructor && obj.constructor.isBuffer && obj.constructor.isBuffer(obj));
};

var combine = function combine(a, b) {
    return [].concat(a, b);
};

module.exports = {
    arrayToObject: arrayToObject,
    assign: assign,
    combine: combine,
    compact: compact,
    decode: decode,
    encode: encode,
    isBuffer: isBuffer,
    isRegExp: isRegExp,
    merge: merge
};


/***/ }),

/***/ 180:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var replace = String.prototype.replace;
var percentTwenties = /%20/g;

module.exports = {
    'default': 'RFC3986',
    formatters: {
        RFC1738: function (value) {
            return replace.call(value, percentTwenties, '+');
        },
        RFC3986: function (value) {
            return value;
        }
    },
    RFC1738: 'RFC1738',
    RFC3986: 'RFC3986'
};


/***/ }),

/***/ 300:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isURL", function() { return isURL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getProtocol", function() { return getProtocol; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isValidProtocol", function() { return isValidProtocol; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getAuthority", function() { return getAuthority; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isValidAuthority", function() { return isValidAuthority; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getPath", function() { return getPath; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isValidPath", function() { return isValidPath; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getQueryString", function() { return getQueryString; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isValidQueryString", function() { return isValidQueryString; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getFragment", function() { return getFragment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isValidFragment", function() { return isValidFragment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "addQueryArgs", function() { return addQueryArgs; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getQueryArg", function() { return getQueryArg; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "hasQueryArg", function() { return hasQueryArg; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "removeQueryArgs", function() { return removeQueryArgs; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "prependHTTP", function() { return prependHTTP; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "safeDecodeURI", function() { return safeDecodeURI; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "filterURLForDisplay", function() { return filterURLForDisplay; });
/* harmony import */ var qs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(76);
/* harmony import */ var qs__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(qs__WEBPACK_IMPORTED_MODULE_0__);
/**
 * External dependencies
 */

var URL_REGEXP = /^(?:https?:)?\/\/\S+$/i;
var EMAIL_REGEXP = /^(mailto:)?[a-z0-9._%+-]+@[a-z0-9][a-z0-9.-]*\.[a-z]{2,63}$/i;
var USABLE_HREF_REGEXP = /^(?:[a-z]+:|#|\?|\.|\/)/i;
/**
 * Determines whether the given string looks like a URL.
 *
 * @param {string} url The string to scrutinise.
 *
 * @return {boolean} Whether or not it looks like a URL.
 */

function isURL(url) {
  return URL_REGEXP.test(url);
}
/**
 * Returns the protocol part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @return {?string} The protocol part of the URL.
 */

function getProtocol(url) {
  var matches = /^([^\s:]+:)/.exec(url);

  if (matches) {
    return matches[1];
  }
}
/**
 * Tests if a url protocol is valid.
 *
 * @param {string} protocol The url protocol.
 *
 * @return {boolean} True if the argument is a valid protocol (e.g. http:, tel:).
 */

function isValidProtocol(protocol) {
  if (!protocol) {
    return false;
  }

  return /^[a-z\-.\+]+[0-9]*:$/i.test(protocol);
}
/**
 * Returns the authority part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @return {?string} The authority part of the URL.
 */

function getAuthority(url) {
  var matches = /^[^\/\s:]+:(?:\/\/)?\/?([^\/\s#?]+)[\/#?]{0,1}\S*$/.exec(url);

  if (matches) {
    return matches[1];
  }
}
/**
 * Checks for invalid characters within the provided authority.
 *
 * @param {string} authority A string containing the URL authority.
 *
 * @return {boolean} True if the argument contains a valid authority.
 */

function isValidAuthority(authority) {
  if (!authority) {
    return false;
  }

  return /^[^\s#?]+$/.test(authority);
}
/**
 * Returns the path part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @return {?string} The path part of the URL.
 */

function getPath(url) {
  var matches = /^[^\/\s:]+:(?:\/\/)?[^\/\s#?]+[\/]([^\s#?]+)[#?]{0,1}\S*$/.exec(url);

  if (matches) {
    return matches[1];
  }
}
/**
 * Checks for invalid characters within the provided path.
 *
 * @param {string} path The URL path.
 *
 * @return {boolean} True if the argument contains a valid path
 */

function isValidPath(path) {
  if (!path) {
    return false;
  }

  return /^[^\s#?]+$/.test(path);
}
/**
 * Returns the query string part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @return {?string} The query string part of the URL.
 */

function getQueryString(url) {
  var matches = /^\S+?\?([^\s#]+)/.exec(url);

  if (matches) {
    return matches[1];
  }
}
/**
 * Checks for invalid characters within the provided query string.
 *
 * @param {string} queryString The query string.
 *
 * @return {boolean} True if the argument contains a valid query string.
 */

function isValidQueryString(queryString) {
  if (!queryString) {
    return false;
  }

  return /^[^\s#?\/]+$/.test(queryString);
}
/**
 * Returns the fragment part of the URL.
 *
 * @param {string} url The full URL
 *
 * @return {?string} The fragment part of the URL.
 */

function getFragment(url) {
  var matches = /^\S+?(#[^\s\?]*)/.exec(url);

  if (matches) {
    return matches[1];
  }
}
/**
 * Checks for invalid characters within the provided fragment.
 *
 * @param {string} fragment The url fragment.
 *
 * @return {boolean} True if the argument contains a valid fragment.
 */

function isValidFragment(fragment) {
  if (!fragment) {
    return false;
  }

  return /^#[^\s#?\/]*$/.test(fragment);
}
/**
 * Appends arguments as querystring to the provided URL. If the URL already
 * includes query arguments, the arguments are merged with (and take precedent
 * over) the existing set.
 *
 * @param {?string} url  URL to which arguments should be appended. If omitted,
 *                       only the resulting querystring is returned.
 * @param {Object}  args Query arguments to apply to URL.
 *
 * @return {string} URL with arguments applied.
 */

function addQueryArgs() {
  var url = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var args = arguments.length > 1 ? arguments[1] : undefined;
  var baseUrl = url; // Determine whether URL already had query arguments.

  var queryStringIndex = url.indexOf('?');

  if (queryStringIndex !== -1) {
    // Merge into existing query arguments.
    args = Object.assign(Object(qs__WEBPACK_IMPORTED_MODULE_0__["parse"])(url.substr(queryStringIndex + 1)), args); // Change working base URL to omit previous query arguments.

    baseUrl = baseUrl.substr(0, queryStringIndex);
  }

  return baseUrl + '?' + Object(qs__WEBPACK_IMPORTED_MODULE_0__["stringify"])(args);
}
/**
 * Returns a single query argument of the url
 *
 * @param {string} url URL
 * @param {string} arg Query arg name
 *
 * @return {Array|string} Query arg value.
 */

function getQueryArg(url, arg) {
  var queryStringIndex = url.indexOf('?');
  var query = queryStringIndex !== -1 ? Object(qs__WEBPACK_IMPORTED_MODULE_0__["parse"])(url.substr(queryStringIndex + 1)) : {};
  return query[arg];
}
/**
 * Determines whether the URL contains a given query arg.
 *
 * @param {string} url URL
 * @param {string} arg Query arg name
 *
 * @return {boolean} Whether or not the URL contains the query aeg.
 */

function hasQueryArg(url, arg) {
  return getQueryArg(url, arg) !== undefined;
}
/**
 * Removes arguments from the query string of the url
 *
 * @param {string} url  URL
 * @param {...string} args Query Args
 *
 * @return {string} Updated URL
 */

function removeQueryArgs(url) {
  var queryStringIndex = url.indexOf('?');
  var query = queryStringIndex !== -1 ? Object(qs__WEBPACK_IMPORTED_MODULE_0__["parse"])(url.substr(queryStringIndex + 1)) : {};
  var baseUrl = queryStringIndex !== -1 ? url.substr(0, queryStringIndex) : url;

  for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    args[_key - 1] = arguments[_key];
  }

  args.forEach(function (arg) {
    return delete query[arg];
  });
  return baseUrl + '?' + Object(qs__WEBPACK_IMPORTED_MODULE_0__["stringify"])(query);
}
/**
 * Prepends "http://" to a url, if it looks like something that is meant to be a TLD.
 *
 * @param  {string} url The URL to test
 *
 * @return {string}     The updated URL
 */

function prependHTTP(url) {
  if (!USABLE_HREF_REGEXP.test(url) && !EMAIL_REGEXP.test(url)) {
    return 'http://' + url;
  }

  return url;
}
/**
 * Safely decodes a URI with `decodeURI`. Returns the URI unmodified if
 * `decodeURI` throws an error.
 *
 * @param {string} uri URI to decode.
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
/**
 * Returns a URL for display.
 *
 * @param {string} url Original URL.
 *
 * @return {string} Displayed URL.
 */

function filterURLForDisplay(url) {
  // Remove protocol and www prefixes.
  var filteredURL = url.replace(/^(?:https?:)\/\/(?:www\.)?/, ''); // Ends with / and only has that single slash, strip it.

  if (filteredURL.match(/^[^\/]+\/$/)) {
    return filteredURL.replace('/', '');
  }

  return filteredURL;
}


/***/ }),

/***/ 301:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(179);
var formats = __webpack_require__(180);

var arrayPrefixGenerators = {
    brackets: function brackets(prefix) { // eslint-disable-line func-name-matching
        return prefix + '[]';
    },
    indices: function indices(prefix, key) { // eslint-disable-line func-name-matching
        return prefix + '[' + key + ']';
    },
    repeat: function repeat(prefix) { // eslint-disable-line func-name-matching
        return prefix;
    }
};

var isArray = Array.isArray;
var push = Array.prototype.push;
var pushToArray = function (arr, valueOrArray) {
    push.apply(arr, isArray(valueOrArray) ? valueOrArray : [valueOrArray]);
};

var toISO = Date.prototype.toISOString;

var defaults = {
    addQueryPrefix: false,
    allowDots: false,
    charset: 'utf-8',
    charsetSentinel: false,
    delimiter: '&',
    encode: true,
    encoder: utils.encode,
    encodeValuesOnly: false,
    // deprecated
    indices: false,
    serializeDate: function serializeDate(date) { // eslint-disable-line func-name-matching
        return toISO.call(date);
    },
    skipNulls: false,
    strictNullHandling: false
};

var stringify = function stringify( // eslint-disable-line func-name-matching
    object,
    prefix,
    generateArrayPrefix,
    strictNullHandling,
    skipNulls,
    encoder,
    filter,
    sort,
    allowDots,
    serializeDate,
    formatter,
    encodeValuesOnly,
    charset
) {
    var obj = object;
    if (typeof filter === 'function') {
        obj = filter(prefix, obj);
    } else if (obj instanceof Date) {
        obj = serializeDate(obj);
    }

    if (obj === null) {
        if (strictNullHandling) {
            return encoder && !encodeValuesOnly ? encoder(prefix, defaults.encoder, charset) : prefix;
        }

        obj = '';
    }

    if (typeof obj === 'string' || typeof obj === 'number' || typeof obj === 'boolean' || utils.isBuffer(obj)) {
        if (encoder) {
            var keyValue = encodeValuesOnly ? prefix : encoder(prefix, defaults.encoder, charset);
            return [formatter(keyValue) + '=' + formatter(encoder(obj, defaults.encoder, charset))];
        }
        return [formatter(prefix) + '=' + formatter(String(obj))];
    }

    var values = [];

    if (typeof obj === 'undefined') {
        return values;
    }

    var objKeys;
    if (Array.isArray(filter)) {
        objKeys = filter;
    } else {
        var keys = Object.keys(obj);
        objKeys = sort ? keys.sort(sort) : keys;
    }

    for (var i = 0; i < objKeys.length; ++i) {
        var key = objKeys[i];

        if (skipNulls && obj[key] === null) {
            continue;
        }

        if (Array.isArray(obj)) {
            pushToArray(values, stringify(
                obj[key],
                generateArrayPrefix(prefix, key),
                generateArrayPrefix,
                strictNullHandling,
                skipNulls,
                encoder,
                filter,
                sort,
                allowDots,
                serializeDate,
                formatter,
                encodeValuesOnly,
                charset
            ));
        } else {
            pushToArray(values, stringify(
                obj[key],
                prefix + (allowDots ? '.' + key : '[' + key + ']'),
                generateArrayPrefix,
                strictNullHandling,
                skipNulls,
                encoder,
                filter,
                sort,
                allowDots,
                serializeDate,
                formatter,
                encodeValuesOnly,
                charset
            ));
        }
    }

    return values;
};

module.exports = function (object, opts) {
    var obj = object;
    var options = opts ? utils.assign({}, opts) : {};

    if (options.encoder !== null && options.encoder !== undefined && typeof options.encoder !== 'function') {
        throw new TypeError('Encoder has to be a function.');
    }

    var delimiter = typeof options.delimiter === 'undefined' ? defaults.delimiter : options.delimiter;
    var strictNullHandling = typeof options.strictNullHandling === 'boolean' ? options.strictNullHandling : defaults.strictNullHandling;
    var skipNulls = typeof options.skipNulls === 'boolean' ? options.skipNulls : defaults.skipNulls;
    var encode = typeof options.encode === 'boolean' ? options.encode : defaults.encode;
    var encoder = typeof options.encoder === 'function' ? options.encoder : defaults.encoder;
    var sort = typeof options.sort === 'function' ? options.sort : null;
    var allowDots = typeof options.allowDots === 'undefined' ? defaults.allowDots : !!options.allowDots;
    var serializeDate = typeof options.serializeDate === 'function' ? options.serializeDate : defaults.serializeDate;
    var encodeValuesOnly = typeof options.encodeValuesOnly === 'boolean' ? options.encodeValuesOnly : defaults.encodeValuesOnly;
    var charset = options.charset || defaults.charset;
    if (typeof options.charset !== 'undefined' && options.charset !== 'utf-8' && options.charset !== 'iso-8859-1') {
        throw new Error('The charset option must be either utf-8, iso-8859-1, or undefined');
    }

    if (typeof options.format === 'undefined') {
        options.format = formats['default'];
    } else if (!Object.prototype.hasOwnProperty.call(formats.formatters, options.format)) {
        throw new TypeError('Unknown format option provided.');
    }
    var formatter = formats.formatters[options.format];
    var objKeys;
    var filter;

    if (typeof options.filter === 'function') {
        filter = options.filter;
        obj = filter('', obj);
    } else if (Array.isArray(options.filter)) {
        filter = options.filter;
        objKeys = filter;
    }

    var keys = [];

    if (typeof obj !== 'object' || obj === null) {
        return '';
    }

    var arrayFormat;
    if (options.arrayFormat in arrayPrefixGenerators) {
        arrayFormat = options.arrayFormat;
    } else if ('indices' in options) {
        arrayFormat = options.indices ? 'indices' : 'repeat';
    } else {
        arrayFormat = 'indices';
    }

    var generateArrayPrefix = arrayPrefixGenerators[arrayFormat];

    if (!objKeys) {
        objKeys = Object.keys(obj);
    }

    if (sort) {
        objKeys.sort(sort);
    }

    for (var i = 0; i < objKeys.length; ++i) {
        var key = objKeys[i];

        if (skipNulls && obj[key] === null) {
            continue;
        }
        pushToArray(keys, stringify(
            obj[key],
            key,
            generateArrayPrefix,
            strictNullHandling,
            skipNulls,
            encode ? encoder : null,
            filter,
            sort,
            allowDots,
            serializeDate,
            formatter,
            encodeValuesOnly,
            charset
        ));
    }

    var joined = keys.join(delimiter);
    var prefix = options.addQueryPrefix === true ? '?' : '';

    if (options.charsetSentinel) {
        if (charset === 'iso-8859-1') {
            // encodeURIComponent('&#10003;'), the "numeric entity" representation of a checkmark
            prefix += 'utf8=%26%2310003%3B&';
        } else {
            // encodeURIComponent('✓')
            prefix += 'utf8=%E2%9C%93&';
        }
    }

    return joined.length > 0 ? prefix + joined : '';
};


/***/ }),

/***/ 302:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(179);

var has = Object.prototype.hasOwnProperty;

var defaults = {
    allowDots: false,
    allowPrototypes: false,
    arrayLimit: 20,
    charset: 'utf-8',
    charsetSentinel: false,
    decoder: utils.decode,
    delimiter: '&',
    depth: 5,
    ignoreQueryPrefix: false,
    interpretNumericEntities: false,
    parameterLimit: 1000,
    parseArrays: true,
    plainObjects: false,
    strictNullHandling: false
};

var interpretNumericEntities = function (str) {
    return str.replace(/&#(\d+);/g, function ($0, numberStr) {
        return String.fromCharCode(parseInt(numberStr, 10));
    });
};

// This is what browsers will submit when the ✓ character occurs in an
// application/x-www-form-urlencoded body and the encoding of the page containing
// the form is iso-8859-1, or when the submitted form has an accept-charset
// attribute of iso-8859-1. Presumably also with other charsets that do not contain
// the ✓ character, such as us-ascii.
var isoSentinel = 'utf8=%26%2310003%3B'; // encodeURIComponent('&#10003;')

// These are the percent-encoded utf-8 octets representing a checkmark, indicating that the request actually is utf-8 encoded.
var charsetSentinel = 'utf8=%E2%9C%93'; // encodeURIComponent('✓')

var parseValues = function parseQueryStringValues(str, options) {
    var obj = {};
    var cleanStr = options.ignoreQueryPrefix ? str.replace(/^\?/, '') : str;
    var limit = options.parameterLimit === Infinity ? undefined : options.parameterLimit;
    var parts = cleanStr.split(options.delimiter, limit);
    var skipIndex = -1; // Keep track of where the utf8 sentinel was found
    var i;

    var charset = options.charset;
    if (options.charsetSentinel) {
        for (i = 0; i < parts.length; ++i) {
            if (parts[i].indexOf('utf8=') === 0) {
                if (parts[i] === charsetSentinel) {
                    charset = 'utf-8';
                } else if (parts[i] === isoSentinel) {
                    charset = 'iso-8859-1';
                }
                skipIndex = i;
                i = parts.length; // The eslint settings do not allow break;
            }
        }
    }

    for (i = 0; i < parts.length; ++i) {
        if (i === skipIndex) {
            continue;
        }
        var part = parts[i];

        var bracketEqualsPos = part.indexOf(']=');
        var pos = bracketEqualsPos === -1 ? part.indexOf('=') : bracketEqualsPos + 1;

        var key, val;
        if (pos === -1) {
            key = options.decoder(part, defaults.decoder, charset);
            val = options.strictNullHandling ? null : '';
        } else {
            key = options.decoder(part.slice(0, pos), defaults.decoder, charset);
            val = options.decoder(part.slice(pos + 1), defaults.decoder, charset);
        }

        if (val && options.interpretNumericEntities && charset === 'iso-8859-1') {
            val = interpretNumericEntities(val);
        }
        if (has.call(obj, key)) {
            obj[key] = utils.combine(obj[key], val);
        } else {
            obj[key] = val;
        }
    }

    return obj;
};

var parseObject = function (chain, val, options) {
    var leaf = val;

    for (var i = chain.length - 1; i >= 0; --i) {
        var obj;
        var root = chain[i];

        if (root === '[]' && options.parseArrays) {
            obj = [].concat(leaf);
        } else {
            obj = options.plainObjects ? Object.create(null) : {};
            var cleanRoot = root.charAt(0) === '[' && root.charAt(root.length - 1) === ']' ? root.slice(1, -1) : root;
            var index = parseInt(cleanRoot, 10);
            if (!options.parseArrays && cleanRoot === '') {
                obj = { 0: leaf };
            } else if (
                !isNaN(index)
                && root !== cleanRoot
                && String(index) === cleanRoot
                && index >= 0
                && (options.parseArrays && index <= options.arrayLimit)
            ) {
                obj = [];
                obj[index] = leaf;
            } else {
                obj[cleanRoot] = leaf;
            }
        }

        leaf = obj;
    }

    return leaf;
};

var parseKeys = function parseQueryStringKeys(givenKey, val, options) {
    if (!givenKey) {
        return;
    }

    // Transform dot notation to bracket notation
    var key = options.allowDots ? givenKey.replace(/\.([^.[]+)/g, '[$1]') : givenKey;

    // The regex chunks

    var brackets = /(\[[^[\]]*])/;
    var child = /(\[[^[\]]*])/g;

    // Get the parent

    var segment = brackets.exec(key);
    var parent = segment ? key.slice(0, segment.index) : key;

    // Stash the parent if it exists

    var keys = [];
    if (parent) {
        // If we aren't using plain objects, optionally prefix keys that would overwrite object prototype properties
        if (!options.plainObjects && has.call(Object.prototype, parent)) {
            if (!options.allowPrototypes) {
                return;
            }
        }

        keys.push(parent);
    }

    // Loop through children appending to the array until we hit depth

    var i = 0;
    while ((segment = child.exec(key)) !== null && i < options.depth) {
        i += 1;
        if (!options.plainObjects && has.call(Object.prototype, segment[1].slice(1, -1))) {
            if (!options.allowPrototypes) {
                return;
            }
        }
        keys.push(segment[1]);
    }

    // If there's a remainder, just add whatever is left

    if (segment) {
        keys.push('[' + key.slice(segment.index) + ']');
    }

    return parseObject(keys, val, options);
};

module.exports = function (str, opts) {
    var options = opts ? utils.assign({}, opts) : {};

    if (options.decoder !== null && options.decoder !== undefined && typeof options.decoder !== 'function') {
        throw new TypeError('Decoder has to be a function.');
    }

    options.ignoreQueryPrefix = options.ignoreQueryPrefix === true;
    options.delimiter = typeof options.delimiter === 'string' || utils.isRegExp(options.delimiter) ? options.delimiter : defaults.delimiter;
    options.depth = typeof options.depth === 'number' ? options.depth : defaults.depth;
    options.arrayLimit = typeof options.arrayLimit === 'number' ? options.arrayLimit : defaults.arrayLimit;
    options.parseArrays = options.parseArrays !== false;
    options.decoder = typeof options.decoder === 'function' ? options.decoder : defaults.decoder;
    options.allowDots = typeof options.allowDots === 'undefined' ? defaults.allowDots : !!options.allowDots;
    options.plainObjects = typeof options.plainObjects === 'boolean' ? options.plainObjects : defaults.plainObjects;
    options.allowPrototypes = typeof options.allowPrototypes === 'boolean' ? options.allowPrototypes : defaults.allowPrototypes;
    options.parameterLimit = typeof options.parameterLimit === 'number' ? options.parameterLimit : defaults.parameterLimit;
    options.strictNullHandling = typeof options.strictNullHandling === 'boolean' ? options.strictNullHandling : defaults.strictNullHandling;

    if (typeof options.charset !== 'undefined' && options.charset !== 'utf-8' && options.charset !== 'iso-8859-1') {
        throw new Error('The charset option must be either utf-8, iso-8859-1, or undefined');
    }
    if (typeof options.charset === 'undefined') {
        options.charset = defaults.charset;
    }

    if (str === '' || str === null || typeof str === 'undefined') {
        return options.plainObjects ? Object.create(null) : {};
    }

    var tempObj = typeof str === 'string' ? parseValues(str, options) : str;
    var obj = options.plainObjects ? Object.create(null) : {};

    // Iterate over the keys and setup the new object

    var keys = Object.keys(tempObj);
    for (var i = 0; i < keys.length; ++i) {
        var key = keys[i];
        var newObj = parseKeys(key, tempObj[key], options);
        obj = utils.merge(obj, newObj, options);
    }

    return utils.compact(obj);
};


/***/ }),

/***/ 76:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var stringify = __webpack_require__(301);
var parse = __webpack_require__(302);
var formats = __webpack_require__(180);

module.exports = {
    formats: formats,
    parse: parse,
    stringify: stringify
};


/***/ })

/******/ });