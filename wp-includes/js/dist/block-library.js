this["wp"] = this["wp"] || {}; this["wp"]["blockLibrary"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 341);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ 1:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ 10:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _defineProperty; });
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

/***/ }),

/***/ 11:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _createClass; });
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

/***/ }),

/***/ 12:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ 123:
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(module, global) {var __WEBPACK_AMD_DEFINE_RESULT__;/*! https://mths.be/punycode v1.3.2 by @mathias */
;(function(root) {

	/** Detect free variables */
	var freeExports =  true && exports &&
		!exports.nodeType && exports;
	var freeModule =  true && module &&
		!module.nodeType && module;
	var freeGlobal = typeof global == 'object' && global;
	if (
		freeGlobal.global === freeGlobal ||
		freeGlobal.window === freeGlobal ||
		freeGlobal.self === freeGlobal
	) {
		root = freeGlobal;
	}

	/**
	 * The `punycode` object.
	 * @name punycode
	 * @type Object
	 */
	var punycode,

	/** Highest positive signed 32-bit float value */
	maxInt = 2147483647, // aka. 0x7FFFFFFF or 2^31-1

	/** Bootstring parameters */
	base = 36,
	tMin = 1,
	tMax = 26,
	skew = 38,
	damp = 700,
	initialBias = 72,
	initialN = 128, // 0x80
	delimiter = '-', // '\x2D'

	/** Regular expressions */
	regexPunycode = /^xn--/,
	regexNonASCII = /[^\x20-\x7E]/, // unprintable ASCII chars + non-ASCII chars
	regexSeparators = /[\x2E\u3002\uFF0E\uFF61]/g, // RFC 3490 separators

	/** Error messages */
	errors = {
		'overflow': 'Overflow: input needs wider integers to process',
		'not-basic': 'Illegal input >= 0x80 (not a basic code point)',
		'invalid-input': 'Invalid input'
	},

	/** Convenience shortcuts */
	baseMinusTMin = base - tMin,
	floor = Math.floor,
	stringFromCharCode = String.fromCharCode,

	/** Temporary variable */
	key;

	/*--------------------------------------------------------------------------*/

	/**
	 * A generic error utility function.
	 * @private
	 * @param {String} type The error type.
	 * @returns {Error} Throws a `RangeError` with the applicable error message.
	 */
	function error(type) {
		throw RangeError(errors[type]);
	}

	/**
	 * A generic `Array#map` utility function.
	 * @private
	 * @param {Array} array The array to iterate over.
	 * @param {Function} callback The function that gets called for every array
	 * item.
	 * @returns {Array} A new array of values returned by the callback function.
	 */
	function map(array, fn) {
		var length = array.length;
		var result = [];
		while (length--) {
			result[length] = fn(array[length]);
		}
		return result;
	}

	/**
	 * A simple `Array#map`-like wrapper to work with domain name strings or email
	 * addresses.
	 * @private
	 * @param {String} domain The domain name or email address.
	 * @param {Function} callback The function that gets called for every
	 * character.
	 * @returns {Array} A new string of characters returned by the callback
	 * function.
	 */
	function mapDomain(string, fn) {
		var parts = string.split('@');
		var result = '';
		if (parts.length > 1) {
			// In email addresses, only the domain name should be punycoded. Leave
			// the local part (i.e. everything up to `@`) intact.
			result = parts[0] + '@';
			string = parts[1];
		}
		// Avoid `split(regex)` for IE8 compatibility. See #17.
		string = string.replace(regexSeparators, '\x2E');
		var labels = string.split('.');
		var encoded = map(labels, fn).join('.');
		return result + encoded;
	}

	/**
	 * Creates an array containing the numeric code points of each Unicode
	 * character in the string. While JavaScript uses UCS-2 internally,
	 * this function will convert a pair of surrogate halves (each of which
	 * UCS-2 exposes as separate characters) into a single code point,
	 * matching UTF-16.
	 * @see `punycode.ucs2.encode`
	 * @see <https://mathiasbynens.be/notes/javascript-encoding>
	 * @memberOf punycode.ucs2
	 * @name decode
	 * @param {String} string The Unicode input string (UCS-2).
	 * @returns {Array} The new array of code points.
	 */
	function ucs2decode(string) {
		var output = [],
		    counter = 0,
		    length = string.length,
		    value,
		    extra;
		while (counter < length) {
			value = string.charCodeAt(counter++);
			if (value >= 0xD800 && value <= 0xDBFF && counter < length) {
				// high surrogate, and there is a next character
				extra = string.charCodeAt(counter++);
				if ((extra & 0xFC00) == 0xDC00) { // low surrogate
					output.push(((value & 0x3FF) << 10) + (extra & 0x3FF) + 0x10000);
				} else {
					// unmatched surrogate; only append this code unit, in case the next
					// code unit is the high surrogate of a surrogate pair
					output.push(value);
					counter--;
				}
			} else {
				output.push(value);
			}
		}
		return output;
	}

	/**
	 * Creates a string based on an array of numeric code points.
	 * @see `punycode.ucs2.decode`
	 * @memberOf punycode.ucs2
	 * @name encode
	 * @param {Array} codePoints The array of numeric code points.
	 * @returns {String} The new Unicode string (UCS-2).
	 */
	function ucs2encode(array) {
		return map(array, function(value) {
			var output = '';
			if (value > 0xFFFF) {
				value -= 0x10000;
				output += stringFromCharCode(value >>> 10 & 0x3FF | 0xD800);
				value = 0xDC00 | value & 0x3FF;
			}
			output += stringFromCharCode(value);
			return output;
		}).join('');
	}

	/**
	 * Converts a basic code point into a digit/integer.
	 * @see `digitToBasic()`
	 * @private
	 * @param {Number} codePoint The basic numeric code point value.
	 * @returns {Number} The numeric value of a basic code point (for use in
	 * representing integers) in the range `0` to `base - 1`, or `base` if
	 * the code point does not represent a value.
	 */
	function basicToDigit(codePoint) {
		if (codePoint - 48 < 10) {
			return codePoint - 22;
		}
		if (codePoint - 65 < 26) {
			return codePoint - 65;
		}
		if (codePoint - 97 < 26) {
			return codePoint - 97;
		}
		return base;
	}

	/**
	 * Converts a digit/integer into a basic code point.
	 * @see `basicToDigit()`
	 * @private
	 * @param {Number} digit The numeric value of a basic code point.
	 * @returns {Number} The basic code point whose value (when used for
	 * representing integers) is `digit`, which needs to be in the range
	 * `0` to `base - 1`. If `flag` is non-zero, the uppercase form is
	 * used; else, the lowercase form is used. The behavior is undefined
	 * if `flag` is non-zero and `digit` has no uppercase form.
	 */
	function digitToBasic(digit, flag) {
		//  0..25 map to ASCII a..z or A..Z
		// 26..35 map to ASCII 0..9
		return digit + 22 + 75 * (digit < 26) - ((flag != 0) << 5);
	}

	/**
	 * Bias adaptation function as per section 3.4 of RFC 3492.
	 * http://tools.ietf.org/html/rfc3492#section-3.4
	 * @private
	 */
	function adapt(delta, numPoints, firstTime) {
		var k = 0;
		delta = firstTime ? floor(delta / damp) : delta >> 1;
		delta += floor(delta / numPoints);
		for (/* no initialization */; delta > baseMinusTMin * tMax >> 1; k += base) {
			delta = floor(delta / baseMinusTMin);
		}
		return floor(k + (baseMinusTMin + 1) * delta / (delta + skew));
	}

	/**
	 * Converts a Punycode string of ASCII-only symbols to a string of Unicode
	 * symbols.
	 * @memberOf punycode
	 * @param {String} input The Punycode string of ASCII-only symbols.
	 * @returns {String} The resulting string of Unicode symbols.
	 */
	function decode(input) {
		// Don't use UCS-2
		var output = [],
		    inputLength = input.length,
		    out,
		    i = 0,
		    n = initialN,
		    bias = initialBias,
		    basic,
		    j,
		    index,
		    oldi,
		    w,
		    k,
		    digit,
		    t,
		    /** Cached calculation results */
		    baseMinusT;

		// Handle the basic code points: let `basic` be the number of input code
		// points before the last delimiter, or `0` if there is none, then copy
		// the first basic code points to the output.

		basic = input.lastIndexOf(delimiter);
		if (basic < 0) {
			basic = 0;
		}

		for (j = 0; j < basic; ++j) {
			// if it's not a basic code point
			if (input.charCodeAt(j) >= 0x80) {
				error('not-basic');
			}
			output.push(input.charCodeAt(j));
		}

		// Main decoding loop: start just after the last delimiter if any basic code
		// points were copied; start at the beginning otherwise.

		for (index = basic > 0 ? basic + 1 : 0; index < inputLength; /* no final expression */) {

			// `index` is the index of the next character to be consumed.
			// Decode a generalized variable-length integer into `delta`,
			// which gets added to `i`. The overflow checking is easier
			// if we increase `i` as we go, then subtract off its starting
			// value at the end to obtain `delta`.
			for (oldi = i, w = 1, k = base; /* no condition */; k += base) {

				if (index >= inputLength) {
					error('invalid-input');
				}

				digit = basicToDigit(input.charCodeAt(index++));

				if (digit >= base || digit > floor((maxInt - i) / w)) {
					error('overflow');
				}

				i += digit * w;
				t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);

				if (digit < t) {
					break;
				}

				baseMinusT = base - t;
				if (w > floor(maxInt / baseMinusT)) {
					error('overflow');
				}

				w *= baseMinusT;

			}

			out = output.length + 1;
			bias = adapt(i - oldi, out, oldi == 0);

			// `i` was supposed to wrap around from `out` to `0`,
			// incrementing `n` each time, so we'll fix that now:
			if (floor(i / out) > maxInt - n) {
				error('overflow');
			}

			n += floor(i / out);
			i %= out;

			// Insert `n` at position `i` of the output
			output.splice(i++, 0, n);

		}

		return ucs2encode(output);
	}

	/**
	 * Converts a string of Unicode symbols (e.g. a domain name label) to a
	 * Punycode string of ASCII-only symbols.
	 * @memberOf punycode
	 * @param {String} input The string of Unicode symbols.
	 * @returns {String} The resulting Punycode string of ASCII-only symbols.
	 */
	function encode(input) {
		var n,
		    delta,
		    handledCPCount,
		    basicLength,
		    bias,
		    j,
		    m,
		    q,
		    k,
		    t,
		    currentValue,
		    output = [],
		    /** `inputLength` will hold the number of code points in `input`. */
		    inputLength,
		    /** Cached calculation results */
		    handledCPCountPlusOne,
		    baseMinusT,
		    qMinusT;

		// Convert the input in UCS-2 to Unicode
		input = ucs2decode(input);

		// Cache the length
		inputLength = input.length;

		// Initialize the state
		n = initialN;
		delta = 0;
		bias = initialBias;

		// Handle the basic code points
		for (j = 0; j < inputLength; ++j) {
			currentValue = input[j];
			if (currentValue < 0x80) {
				output.push(stringFromCharCode(currentValue));
			}
		}

		handledCPCount = basicLength = output.length;

		// `handledCPCount` is the number of code points that have been handled;
		// `basicLength` is the number of basic code points.

		// Finish the basic string - if it is not empty - with a delimiter
		if (basicLength) {
			output.push(delimiter);
		}

		// Main encoding loop:
		while (handledCPCount < inputLength) {

			// All non-basic code points < n have been handled already. Find the next
			// larger one:
			for (m = maxInt, j = 0; j < inputLength; ++j) {
				currentValue = input[j];
				if (currentValue >= n && currentValue < m) {
					m = currentValue;
				}
			}

			// Increase `delta` enough to advance the decoder's <n,i> state to <m,0>,
			// but guard against overflow
			handledCPCountPlusOne = handledCPCount + 1;
			if (m - n > floor((maxInt - delta) / handledCPCountPlusOne)) {
				error('overflow');
			}

			delta += (m - n) * handledCPCountPlusOne;
			n = m;

			for (j = 0; j < inputLength; ++j) {
				currentValue = input[j];

				if (currentValue < n && ++delta > maxInt) {
					error('overflow');
				}

				if (currentValue == n) {
					// Represent delta as a generalized variable-length integer
					for (q = delta, k = base; /* no condition */; k += base) {
						t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);
						if (q < t) {
							break;
						}
						qMinusT = q - t;
						baseMinusT = base - t;
						output.push(
							stringFromCharCode(digitToBasic(t + qMinusT % baseMinusT, 0))
						);
						q = floor(qMinusT / baseMinusT);
					}

					output.push(stringFromCharCode(digitToBasic(q, 0)));
					bias = adapt(delta, handledCPCountPlusOne, handledCPCount == basicLength);
					delta = 0;
					++handledCPCount;
				}
			}

			++delta;
			++n;

		}
		return output.join('');
	}

	/**
	 * Converts a Punycode string representing a domain name or an email address
	 * to Unicode. Only the Punycoded parts of the input will be converted, i.e.
	 * it doesn't matter if you call it on a string that has already been
	 * converted to Unicode.
	 * @memberOf punycode
	 * @param {String} input The Punycoded domain name or email address to
	 * convert to Unicode.
	 * @returns {String} The Unicode representation of the given Punycode
	 * string.
	 */
	function toUnicode(input) {
		return mapDomain(input, function(string) {
			return regexPunycode.test(string)
				? decode(string.slice(4).toLowerCase())
				: string;
		});
	}

	/**
	 * Converts a Unicode string representing a domain name or an email address to
	 * Punycode. Only the non-ASCII parts of the domain name will be converted,
	 * i.e. it doesn't matter if you call it with a domain that's already in
	 * ASCII.
	 * @memberOf punycode
	 * @param {String} input The domain name or email address to convert, as a
	 * Unicode string.
	 * @returns {String} The Punycode representation of the given domain name or
	 * email address.
	 */
	function toASCII(input) {
		return mapDomain(input, function(string) {
			return regexNonASCII.test(string)
				? 'xn--' + encode(string)
				: string;
		});
	}

	/*--------------------------------------------------------------------------*/

	/** Define the public API */
	punycode = {
		/**
		 * A string representing the current Punycode.js version number.
		 * @memberOf punycode
		 * @type String
		 */
		'version': '1.3.2',
		/**
		 * An object of methods to convert from JavaScript's internal character
		 * representation (UCS-2) to Unicode code points, and back.
		 * @see <https://mathiasbynens.be/notes/javascript-encoding>
		 * @memberOf punycode
		 * @type Object
		 */
		'ucs2': {
			'decode': ucs2decode,
			'encode': ucs2encode
		},
		'decode': decode,
		'encode': encode,
		'toASCII': toASCII,
		'toUnicode': toUnicode
	};

	/** Expose `punycode` */
	// Some AMD build optimizers, like r.js, check for specific condition patterns
	// like the following:
	if (
		true
	) {
		!(__WEBPACK_AMD_DEFINE_RESULT__ = (function() {
			return punycode;
		}).call(exports, __webpack_require__, exports, module),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}

}(this));

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(124)(module), __webpack_require__(65)))

/***/ }),

/***/ 124:
/***/ (function(module, exports) {

module.exports = function(module) {
	if (!module.webpackPolyfill) {
		module.deprecate = function() {};
		module.paths = [];
		// module.parent = undefined by default
		if (!module.children) module.children = [];
		Object.defineProperty(module, "loaded", {
			enumerable: true,
			get: function() {
				return module.l;
			}
		});
		Object.defineProperty(module, "id", {
			enumerable: true,
			get: function() {
				return module.i;
			}
		});
		module.webpackPolyfill = 1;
	}
	return module;
};


/***/ }),

/***/ 125:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = {
  isString: function(arg) {
    return typeof(arg) === 'string';
  },
  isObject: function(arg) {
    return typeof(arg) === 'object' && arg !== null;
  },
  isNull: function(arg) {
    return arg === null;
  },
  isNullOrUndefined: function(arg) {
    return arg == null;
  }
};


/***/ }),

/***/ 126:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.decode = exports.parse = __webpack_require__(127);
exports.encode = exports.stringify = __webpack_require__(128);


/***/ }),

/***/ 127:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



// If obj.hasOwnProperty has been overridden, then calling
// obj.hasOwnProperty(prop) will break.
// See: https://github.com/joyent/node/issues/1707
function hasOwnProperty(obj, prop) {
  return Object.prototype.hasOwnProperty.call(obj, prop);
}

module.exports = function(qs, sep, eq, options) {
  sep = sep || '&';
  eq = eq || '=';
  var obj = {};

  if (typeof qs !== 'string' || qs.length === 0) {
    return obj;
  }

  var regexp = /\+/g;
  qs = qs.split(sep);

  var maxKeys = 1000;
  if (options && typeof options.maxKeys === 'number') {
    maxKeys = options.maxKeys;
  }

  var len = qs.length;
  // maxKeys <= 0 means that we should not limit keys count
  if (maxKeys > 0 && len > maxKeys) {
    len = maxKeys;
  }

  for (var i = 0; i < len; ++i) {
    var x = qs[i].replace(regexp, '%20'),
        idx = x.indexOf(eq),
        kstr, vstr, k, v;

    if (idx >= 0) {
      kstr = x.substr(0, idx);
      vstr = x.substr(idx + 1);
    } else {
      kstr = x;
      vstr = '';
    }

    k = decodeURIComponent(kstr);
    v = decodeURIComponent(vstr);

    if (!hasOwnProperty(obj, k)) {
      obj[k] = v;
    } else if (isArray(obj[k])) {
      obj[k].push(v);
    } else {
      obj[k] = [obj[k], v];
    }
  }

  return obj;
};

var isArray = Array.isArray || function (xs) {
  return Object.prototype.toString.call(xs) === '[object Array]';
};


/***/ }),

/***/ 128:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



var stringifyPrimitive = function(v) {
  switch (typeof v) {
    case 'string':
      return v;

    case 'boolean':
      return v ? 'true' : 'false';

    case 'number':
      return isFinite(v) ? v : '';

    default:
      return '';
  }
};

module.exports = function(obj, sep, eq, name) {
  sep = sep || '&';
  eq = eq || '=';
  if (obj === null) {
    obj = undefined;
  }

  if (typeof obj === 'object') {
    return map(objectKeys(obj), function(k) {
      var ks = encodeURIComponent(stringifyPrimitive(k)) + eq;
      if (isArray(obj[k])) {
        return map(obj[k], function(v) {
          return ks + encodeURIComponent(stringifyPrimitive(v));
        }).join(sep);
      } else {
        return ks + encodeURIComponent(stringifyPrimitive(obj[k]));
      }
    }).join(sep);

  }

  if (!name) return '';
  return encodeURIComponent(stringifyPrimitive(name)) + eq +
         encodeURIComponent(stringifyPrimitive(obj));
};

var isArray = Array.isArray || function (xs) {
  return Object.prototype.toString.call(xs) === '[object Array]';
};

function map (xs, f) {
  if (xs.map) return xs.map(f);
  var res = [];
  for (var i = 0; i < xs.length; i++) {
    res.push(f(xs[i], i));
  }
  return res;
}

var objectKeys = Object.keys || function (obj) {
  var res = [];
  for (var key in obj) {
    if (Object.prototype.hasOwnProperty.call(obj, key)) res.push(key);
  }
  return res;
};


/***/ }),

/***/ 13:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(31);
/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(5);


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ 14:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _getPrototypeOf; });
function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

/***/ }),

/***/ 15:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _inherits; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) _setPrototypeOf(subClass, superClass);
}

/***/ }),

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2017 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg) && arg.length) {
				var inner = classNames.apply(null, arg);
				if (inner) {
					classes.push(inner);
				}
			} else if (argType === 'object') {
				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ 17:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js
function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__(30);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ 18:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _extends; });
function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

/***/ }),

/***/ 19:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 21:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _objectWithoutProperties; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = _objectWithoutPropertiesLoose(source, excluded);
  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

/***/ }),

/***/ 22:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["richText"]; }());

/***/ }),

/***/ 221:
/***/ (function(module, exports, __webpack_require__) {

/*! Fast Average Color | © 2019 Denis Seleznev | MIT License | https://github.com/hcodes/fast-average-color/ */
(function (global, factory) {
	 true ? module.exports = factory() :
	undefined;
}(this, (function () { 'use strict';

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

function _slicedToArray(arr, i) {
  return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest();
}

function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

function _iterableToArrayLimit(arr, i) {
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

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

var FastAverageColor =
/*#__PURE__*/
function () {
  function FastAverageColor() {
    _classCallCheck(this, FastAverageColor);
  }

  _createClass(FastAverageColor, [{
    key: "getColorAsync",

    /**
     * Get asynchronously the average color from not loaded image.
     *
     * @param {HTMLImageElement} resource
     * @param {Function} callback
     * @param {Object|null} [options]
     * @param {Array}  [options.defaultColor=[255, 255, 255, 255]]
     * @param {*}      [options.data]
     * @param {string} [options.mode="speed"] "precision" or "speed"
     * @param {string} [options.algorithm="sqrt"] "simple", "sqrt" or "dominant"
     * @param {number} [options.step=1]
     * @param {number} [options.left=0]
     * @param {number} [options.top=0]
     * @param {number} [options.width=width of resource]
     * @param {number} [options.height=height of resource]
     */
    value: function getColorAsync(resource, callback, options) {
      if (resource.complete) {
        callback.call(resource, this.getColor(resource, options), options && options.data);
      } else {
        this._bindImageEvents(resource, callback, options);
      }
    }
    /**
     * Get the average color from images, videos and canvas.
     *
     * @param {HTMLImageElement|HTMLVideoElement|HTMLCanvasElement} resource
     * @param {Object|null} [options]
     * @param {Array}  [options.defaultColor=[255, 255, 255, 255]]
     * @param {*}      [options.data]
     * @param {string} [options.mode="speed"] "precision" or "speed"
     * @param {string} [options.algorithm="sqrt"] "simple", "sqrt" or "dominant"
     * @param {number} [options.step=1]
     * @param {number} [options.left=0]
     * @param {number} [options.top=0]
     * @param {number} [options.width=width of resource]
     * @param {number} [options.height=height of resource]
     *
     * @returns {Object}
     */

  }, {
    key: "getColor",
    value: function getColor(resource, options) {
      options = options || {};

      var defaultColor = this._getDefaultColor(options),
          originalSize = this._getOriginalSize(resource),
          size = this._prepareSizeAndPosition(originalSize, options);

      var error = null,
          value = defaultColor;

      if (!size.srcWidth || !size.srcHeight || !size.destWidth || !size.destHeight) {
        return this._prepareResult(defaultColor, new Error('FastAverageColor: Incorrect sizes.'));
      }

      if (!this._ctx) {
        this._canvas = this._makeCanvas();
        this._ctx = this._canvas.getContext && this._canvas.getContext('2d');

        if (!this._ctx) {
          return this._prepareResult(defaultColor, new Error('FastAverageColor: Canvas Context 2D is not supported in this browser.'));
        }
      }

      this._canvas.width = size.destWidth;
      this._canvas.height = size.destHeight;

      try {
        this._ctx.clearRect(0, 0, size.destWidth, size.destHeight);

        this._ctx.drawImage(resource, size.srcLeft, size.srcTop, size.srcWidth, size.srcHeight, 0, 0, size.destWidth, size.destHeight);

        var bitmapData = this._ctx.getImageData(0, 0, size.destWidth, size.destHeight).data;

        value = this.getColorFromArray4(bitmapData, options);
      } catch (e) {
        // Security error, CORS
        // https://developer.mozilla.org/en/docs/Web/HTML/CORS_enabled_image
        error = e;
      }

      return this._prepareResult(value, error);
    }
    /**
     * Get the average color from a array when 1 pixel is 4 bytes.
     *
     * @param {Array|Uint8Array} arr
     * @param {Object} [options]
     * @param {string} [options.algorithm="sqrt"] "simple", "sqrt" or "dominant"
     * @param {Array}  [options.defaultColor=[255, 255, 255, 255]]
     * @param {number} [options.step=1]
     *
     * @returns {Array} [red (0-255), green (0-255), blue (0-255), alpha (0-255)]
     */

  }, {
    key: "getColorFromArray4",
    value: function getColorFromArray4(arr, options) {
      options = options || {};
      var bytesPerPixel = 4,
          arrLength = arr.length;

      if (arrLength < bytesPerPixel) {
        return this._getDefaultColor(options);
      }

      var len = arrLength - arrLength % bytesPerPixel,
          preparedStep = (options.step || 1) * bytesPerPixel,
          algorithm = '_' + (options.algorithm || 'sqrt') + 'Algorithm';

      if (typeof this[algorithm] !== 'function') {
        throw new Error("FastAverageColor: ".concat(options.algorithm, " is unknown algorithm."));
      }

      return this[algorithm](arr, len, preparedStep);
    }
    /**
     * Destroy the instance.
     */

  }, {
    key: "destroy",
    value: function destroy() {
      delete this._canvas;
      delete this._ctx;
    }
  }, {
    key: "_getDefaultColor",
    value: function _getDefaultColor(options) {
      return this._getOption(options, 'defaultColor', [255, 255, 255, 255]);
    }
  }, {
    key: "_getOption",
    value: function _getOption(options, name, defaultValue) {
      return typeof options[name] === 'undefined' ? defaultValue : options[name];
    }
  }, {
    key: "_prepareSizeAndPosition",
    value: function _prepareSizeAndPosition(originalSize, options) {
      var srcLeft = this._getOption(options, 'left', 0),
          srcTop = this._getOption(options, 'top', 0),
          srcWidth = this._getOption(options, 'width', originalSize.width),
          srcHeight = this._getOption(options, 'height', originalSize.height),
          destWidth = srcWidth,
          destHeight = srcHeight;

      if (options.mode === 'precision') {
        return {
          srcLeft: srcLeft,
          srcTop: srcTop,
          srcWidth: srcWidth,
          srcHeight: srcHeight,
          destWidth: destWidth,
          destHeight: destHeight
        };
      }

      var maxSize = 100,
          minSize = 10;
      var factor;

      if (srcWidth > srcHeight) {
        factor = srcWidth / srcHeight;
        destWidth = maxSize;
        destHeight = Math.round(destWidth / factor);
      } else {
        factor = srcHeight / srcWidth;
        destHeight = maxSize;
        destWidth = Math.round(destHeight / factor);
      }

      if (destWidth > srcWidth || destHeight > srcHeight || destWidth < minSize || destHeight < minSize) {
        destWidth = srcWidth;
        destHeight = srcHeight;
      }

      return {
        srcLeft: srcLeft,
        srcTop: srcTop,
        srcWidth: srcWidth,
        srcHeight: srcHeight,
        destWidth: destWidth,
        destHeight: destHeight
      };
    }
  }, {
    key: "_simpleAlgorithm",
    value: function _simpleAlgorithm(arr, len, preparedStep) {
      var redTotal = 0,
          greenTotal = 0,
          blueTotal = 0,
          alphaTotal = 0,
          count = 0;

      for (var i = 0; i < len; i += preparedStep) {
        var alpha = arr[i + 3],
            red = arr[i] * alpha,
            green = arr[i + 1] * alpha,
            blue = arr[i + 2] * alpha;
        redTotal += red;
        greenTotal += green;
        blueTotal += blue;
        alphaTotal += alpha;
        count++;
      }

      return alphaTotal ? [Math.round(redTotal / alphaTotal), Math.round(greenTotal / alphaTotal), Math.round(blueTotal / alphaTotal), Math.round(alphaTotal / count)] : [0, 0, 0, 0];
    }
  }, {
    key: "_sqrtAlgorithm",
    value: function _sqrtAlgorithm(arr, len, preparedStep) {
      var redTotal = 0,
          greenTotal = 0,
          blueTotal = 0,
          alphaTotal = 0,
          count = 0;

      for (var i = 0; i < len; i += preparedStep) {
        var red = arr[i],
            green = arr[i + 1],
            blue = arr[i + 2],
            alpha = arr[i + 3];
        redTotal += red * red * alpha;
        greenTotal += green * green * alpha;
        blueTotal += blue * blue * alpha;
        alphaTotal += alpha;
        count++;
      }

      return alphaTotal ? [Math.round(Math.sqrt(redTotal / alphaTotal)), Math.round(Math.sqrt(greenTotal / alphaTotal)), Math.round(Math.sqrt(blueTotal / alphaTotal)), Math.round(alphaTotal / count)] : [0, 0, 0, 0];
    }
  }, {
    key: "_dominantAlgorithm",
    value: function _dominantAlgorithm(arr, len, preparedStep) {
      var colorHash = {},
          divider = 24;

      for (var i = 0; i < len; i += preparedStep) {
        var red = arr[i],
            green = arr[i + 1],
            blue = arr[i + 2],
            alpha = arr[i + 3],
            key = Math.round(red / divider) + ',' + Math.round(green / divider) + ',' + Math.round(blue / divider);

        if (colorHash[key]) {
          colorHash[key] = [colorHash[key][0] + red * alpha, colorHash[key][1] + green * alpha, colorHash[key][2] + blue * alpha, colorHash[key][3] + alpha, colorHash[key][4] + 1];
        } else {
          colorHash[key] = [red * alpha, green * alpha, blue * alpha, alpha, 1];
        }
      }

      var buffer = Object.keys(colorHash).map(function (key) {
        return colorHash[key];
      }).sort(function (a, b) {
        var countA = a[4],
            countB = b[4];
        return countA > countB ? -1 : countA === countB ? 0 : 1;
      });

      var _buffer$ = _slicedToArray(buffer[0], 5),
          redTotal = _buffer$[0],
          greenTotal = _buffer$[1],
          blueTotal = _buffer$[2],
          alphaTotal = _buffer$[3],
          count = _buffer$[4];

      return alphaTotal ? [Math.round(redTotal / alphaTotal), Math.round(greenTotal / alphaTotal), Math.round(blueTotal / alphaTotal), Math.round(alphaTotal / count)] : [0, 0, 0, 0];
    }
  }, {
    key: "_bindImageEvents",
    value: function _bindImageEvents(resource, callback, options) {
      var _this = this;

      options = options || {};

      var data = options && options.data,
          defaultColor = this._getDefaultColor(options),
          onload = function onload() {
        unbindEvents();
        callback.call(resource, _this.getColor(resource, options), data);
      },
          onerror = function onerror() {
        unbindEvents();
        callback.call(resource, _this._prepareResult(defaultColor, new Error('Image error')), data);
      },
          onabort = function onabort() {
        unbindEvents();
        callback.call(resource, _this._prepareResult(defaultColor, new Error('Image abort')), data);
      },
          unbindEvents = function unbindEvents() {
        resource.removeEventListener('load', onload);
        resource.removeEventListener('error', onerror);
        resource.removeEventListener('abort', onabort);
      };

      resource.addEventListener('load', onload);
      resource.addEventListener('error', onerror);
      resource.addEventListener('abort', onabort);
    }
  }, {
    key: "_prepareResult",
    value: function _prepareResult(value, error) {
      var rgb = value.slice(0, 3),
          rgba = [].concat(rgb, value[3] / 255),
          isDark = this._isDark(value);

      return {
        error: error,
        value: value,
        rgb: 'rgb(' + rgb.join(',') + ')',
        rgba: 'rgba(' + rgba.join(',') + ')',
        hex: this._arrayToHex(rgb),
        hexa: this._arrayToHex(value),
        isDark: isDark,
        isLight: !isDark
      };
    }
  }, {
    key: "_getOriginalSize",
    value: function _getOriginalSize(resource) {
      if (resource instanceof HTMLImageElement) {
        return {
          width: resource.naturalWidth,
          height: resource.naturalHeight
        };
      }

      if (resource instanceof HTMLVideoElement) {
        return {
          width: resource.videoWidth,
          height: resource.videoHeight
        };
      }

      return {
        width: resource.width,
        height: resource.height
      };
    }
  }, {
    key: "_toHex",
    value: function _toHex(num) {
      var str = num.toString(16);
      return str.length === 1 ? '0' + str : str;
    }
  }, {
    key: "_arrayToHex",
    value: function _arrayToHex(arr) {
      return '#' + arr.map(this._toHex).join('');
    }
  }, {
    key: "_isDark",
    value: function _isDark(color) {
      // http://www.w3.org/TR/AERT#color-contrast
      var result = (color[0] * 299 + color[1] * 587 + color[2] * 114) / 1000;
      return result < 128;
    }
  }, {
    key: "_makeCanvas",
    value: function _makeCanvas() {
      return typeof window === 'undefined' ? new OffscreenCanvas(1, 1) : document.createElement('canvas');
    }
  }]);

  return FastAverageColor;
}();

return FastAverageColor;

})));


/***/ }),

/***/ 23:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _slicedToArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__(38);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
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
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__(39);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js



function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 24:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["editor"]; }());

/***/ }),

/***/ 26:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ 29:
/***/ (function(module, exports) {

(function() { module.exports = this["moment"]; }());

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ 30:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

/***/ }),

/***/ 31:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ 34:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ 341:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "registerCoreBlocks", function() { return /* binding */ build_module_registerCoreBlocks; });
__webpack_require__.d(__webpack_exports__, "__experimentalRegisterExperimentalCoreBlocks", function() { return /* binding */ __experimentalRegisterExperimentalCoreBlocks; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/paragraph/index.js
var paragraph_namespaceObject = {};
__webpack_require__.r(paragraph_namespaceObject);
__webpack_require__.d(paragraph_namespaceObject, "metadata", function() { return paragraph_metadata; });
__webpack_require__.d(paragraph_namespaceObject, "name", function() { return paragraph_name; });
__webpack_require__.d(paragraph_namespaceObject, "settings", function() { return paragraph_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/image/index.js
var image_namespaceObject = {};
__webpack_require__.r(image_namespaceObject);
__webpack_require__.d(image_namespaceObject, "metadata", function() { return image_metadata; });
__webpack_require__.d(image_namespaceObject, "name", function() { return image_name; });
__webpack_require__.d(image_namespaceObject, "settings", function() { return image_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/heading/index.js
var heading_namespaceObject = {};
__webpack_require__.r(heading_namespaceObject);
__webpack_require__.d(heading_namespaceObject, "metadata", function() { return heading_metadata; });
__webpack_require__.d(heading_namespaceObject, "name", function() { return heading_name; });
__webpack_require__.d(heading_namespaceObject, "settings", function() { return heading_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/quote/index.js
var quote_namespaceObject = {};
__webpack_require__.r(quote_namespaceObject);
__webpack_require__.d(quote_namespaceObject, "metadata", function() { return quote_metadata; });
__webpack_require__.d(quote_namespaceObject, "name", function() { return quote_name; });
__webpack_require__.d(quote_namespaceObject, "settings", function() { return quote_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/gallery/index.js
var gallery_namespaceObject = {};
__webpack_require__.r(gallery_namespaceObject);
__webpack_require__.d(gallery_namespaceObject, "metadata", function() { return gallery_metadata; });
__webpack_require__.d(gallery_namespaceObject, "name", function() { return gallery_name; });
__webpack_require__.d(gallery_namespaceObject, "settings", function() { return gallery_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/archives/index.js
var archives_namespaceObject = {};
__webpack_require__.r(archives_namespaceObject);
__webpack_require__.d(archives_namespaceObject, "name", function() { return archives_name; });
__webpack_require__.d(archives_namespaceObject, "settings", function() { return archives_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/audio/index.js
var audio_namespaceObject = {};
__webpack_require__.r(audio_namespaceObject);
__webpack_require__.d(audio_namespaceObject, "metadata", function() { return audio_metadata; });
__webpack_require__.d(audio_namespaceObject, "name", function() { return audio_name; });
__webpack_require__.d(audio_namespaceObject, "settings", function() { return audio_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/button/index.js
var button_namespaceObject = {};
__webpack_require__.r(button_namespaceObject);
__webpack_require__.d(button_namespaceObject, "metadata", function() { return button_metadata; });
__webpack_require__.d(button_namespaceObject, "name", function() { return button_name; });
__webpack_require__.d(button_namespaceObject, "settings", function() { return button_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/calendar/index.js
var calendar_namespaceObject = {};
__webpack_require__.r(calendar_namespaceObject);
__webpack_require__.d(calendar_namespaceObject, "name", function() { return calendar_name; });
__webpack_require__.d(calendar_namespaceObject, "settings", function() { return calendar_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/categories/index.js
var categories_namespaceObject = {};
__webpack_require__.r(categories_namespaceObject);
__webpack_require__.d(categories_namespaceObject, "name", function() { return categories_name; });
__webpack_require__.d(categories_namespaceObject, "settings", function() { return categories_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/code/index.js
var code_namespaceObject = {};
__webpack_require__.r(code_namespaceObject);
__webpack_require__.d(code_namespaceObject, "metadata", function() { return code_metadata; });
__webpack_require__.d(code_namespaceObject, "name", function() { return code_name; });
__webpack_require__.d(code_namespaceObject, "settings", function() { return code_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/columns/index.js
var columns_namespaceObject = {};
__webpack_require__.r(columns_namespaceObject);
__webpack_require__.d(columns_namespaceObject, "metadata", function() { return columns_metadata; });
__webpack_require__.d(columns_namespaceObject, "name", function() { return columns_name; });
__webpack_require__.d(columns_namespaceObject, "settings", function() { return columns_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/column/index.js
var column_namespaceObject = {};
__webpack_require__.r(column_namespaceObject);
__webpack_require__.d(column_namespaceObject, "metadata", function() { return column_metadata; });
__webpack_require__.d(column_namespaceObject, "name", function() { return column_name; });
__webpack_require__.d(column_namespaceObject, "settings", function() { return column_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/cover/index.js
var cover_namespaceObject = {};
__webpack_require__.r(cover_namespaceObject);
__webpack_require__.d(cover_namespaceObject, "metadata", function() { return cover_metadata; });
__webpack_require__.d(cover_namespaceObject, "name", function() { return cover_name; });
__webpack_require__.d(cover_namespaceObject, "settings", function() { return cover_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/embed/index.js
var embed_namespaceObject = {};
__webpack_require__.r(embed_namespaceObject);
__webpack_require__.d(embed_namespaceObject, "name", function() { return embed_name; });
__webpack_require__.d(embed_namespaceObject, "settings", function() { return embed_settings; });
__webpack_require__.d(embed_namespaceObject, "common", function() { return embed_common; });
__webpack_require__.d(embed_namespaceObject, "others", function() { return embed_others; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/file/index.js
var file_namespaceObject = {};
__webpack_require__.r(file_namespaceObject);
__webpack_require__.d(file_namespaceObject, "metadata", function() { return file_metadata; });
__webpack_require__.d(file_namespaceObject, "name", function() { return file_name; });
__webpack_require__.d(file_namespaceObject, "settings", function() { return file_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/html/index.js
var html_namespaceObject = {};
__webpack_require__.r(html_namespaceObject);
__webpack_require__.d(html_namespaceObject, "metadata", function() { return html_metadata; });
__webpack_require__.d(html_namespaceObject, "name", function() { return html_name; });
__webpack_require__.d(html_namespaceObject, "settings", function() { return html_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/media-text/index.js
var media_text_namespaceObject = {};
__webpack_require__.r(media_text_namespaceObject);
__webpack_require__.d(media_text_namespaceObject, "metadata", function() { return media_text_metadata; });
__webpack_require__.d(media_text_namespaceObject, "name", function() { return media_text_name; });
__webpack_require__.d(media_text_namespaceObject, "settings", function() { return media_text_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/latest-comments/index.js
var latest_comments_namespaceObject = {};
__webpack_require__.r(latest_comments_namespaceObject);
__webpack_require__.d(latest_comments_namespaceObject, "name", function() { return latest_comments_name; });
__webpack_require__.d(latest_comments_namespaceObject, "settings", function() { return latest_comments_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/latest-posts/index.js
var latest_posts_namespaceObject = {};
__webpack_require__.r(latest_posts_namespaceObject);
__webpack_require__.d(latest_posts_namespaceObject, "name", function() { return latest_posts_name; });
__webpack_require__.d(latest_posts_namespaceObject, "settings", function() { return latest_posts_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/list/index.js
var list_namespaceObject = {};
__webpack_require__.r(list_namespaceObject);
__webpack_require__.d(list_namespaceObject, "metadata", function() { return list_metadata; });
__webpack_require__.d(list_namespaceObject, "name", function() { return list_name; });
__webpack_require__.d(list_namespaceObject, "settings", function() { return list_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/missing/index.js
var missing_namespaceObject = {};
__webpack_require__.r(missing_namespaceObject);
__webpack_require__.d(missing_namespaceObject, "metadata", function() { return missing_metadata; });
__webpack_require__.d(missing_namespaceObject, "name", function() { return missing_name; });
__webpack_require__.d(missing_namespaceObject, "settings", function() { return missing_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/more/index.js
var more_namespaceObject = {};
__webpack_require__.r(more_namespaceObject);
__webpack_require__.d(more_namespaceObject, "metadata", function() { return more_metadata; });
__webpack_require__.d(more_namespaceObject, "name", function() { return more_name; });
__webpack_require__.d(more_namespaceObject, "settings", function() { return more_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/nextpage/index.js
var nextpage_namespaceObject = {};
__webpack_require__.r(nextpage_namespaceObject);
__webpack_require__.d(nextpage_namespaceObject, "metadata", function() { return nextpage_metadata; });
__webpack_require__.d(nextpage_namespaceObject, "name", function() { return nextpage_name; });
__webpack_require__.d(nextpage_namespaceObject, "settings", function() { return nextpage_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/preformatted/index.js
var preformatted_namespaceObject = {};
__webpack_require__.r(preformatted_namespaceObject);
__webpack_require__.d(preformatted_namespaceObject, "metadata", function() { return preformatted_metadata; });
__webpack_require__.d(preformatted_namespaceObject, "name", function() { return preformatted_name; });
__webpack_require__.d(preformatted_namespaceObject, "settings", function() { return preformatted_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/pullquote/index.js
var pullquote_namespaceObject = {};
__webpack_require__.r(pullquote_namespaceObject);
__webpack_require__.d(pullquote_namespaceObject, "metadata", function() { return pullquote_metadata; });
__webpack_require__.d(pullquote_namespaceObject, "name", function() { return pullquote_name; });
__webpack_require__.d(pullquote_namespaceObject, "settings", function() { return pullquote_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/block/index.js
var block_namespaceObject = {};
__webpack_require__.r(block_namespaceObject);
__webpack_require__.d(block_namespaceObject, "name", function() { return block_name; });
__webpack_require__.d(block_namespaceObject, "settings", function() { return block_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/rss/index.js
var rss_namespaceObject = {};
__webpack_require__.r(rss_namespaceObject);
__webpack_require__.d(rss_namespaceObject, "name", function() { return rss_name; });
__webpack_require__.d(rss_namespaceObject, "settings", function() { return rss_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/search/index.js
var search_namespaceObject = {};
__webpack_require__.r(search_namespaceObject);
__webpack_require__.d(search_namespaceObject, "name", function() { return search_name; });
__webpack_require__.d(search_namespaceObject, "settings", function() { return search_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/group/index.js
var group_namespaceObject = {};
__webpack_require__.r(group_namespaceObject);
__webpack_require__.d(group_namespaceObject, "metadata", function() { return group_metadata; });
__webpack_require__.d(group_namespaceObject, "name", function() { return group_name; });
__webpack_require__.d(group_namespaceObject, "settings", function() { return group_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/separator/index.js
var separator_namespaceObject = {};
__webpack_require__.r(separator_namespaceObject);
__webpack_require__.d(separator_namespaceObject, "metadata", function() { return separator_metadata; });
__webpack_require__.d(separator_namespaceObject, "name", function() { return separator_name; });
__webpack_require__.d(separator_namespaceObject, "settings", function() { return separator_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/shortcode/index.js
var shortcode_namespaceObject = {};
__webpack_require__.r(shortcode_namespaceObject);
__webpack_require__.d(shortcode_namespaceObject, "name", function() { return shortcode_name; });
__webpack_require__.d(shortcode_namespaceObject, "settings", function() { return shortcode_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/spacer/index.js
var spacer_namespaceObject = {};
__webpack_require__.r(spacer_namespaceObject);
__webpack_require__.d(spacer_namespaceObject, "metadata", function() { return spacer_metadata; });
__webpack_require__.d(spacer_namespaceObject, "name", function() { return spacer_name; });
__webpack_require__.d(spacer_namespaceObject, "settings", function() { return spacer_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/subhead/index.js
var subhead_namespaceObject = {};
__webpack_require__.r(subhead_namespaceObject);
__webpack_require__.d(subhead_namespaceObject, "metadata", function() { return subhead_metadata; });
__webpack_require__.d(subhead_namespaceObject, "name", function() { return subhead_name; });
__webpack_require__.d(subhead_namespaceObject, "settings", function() { return subhead_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/table/index.js
var table_namespaceObject = {};
__webpack_require__.r(table_namespaceObject);
__webpack_require__.d(table_namespaceObject, "metadata", function() { return table_metadata; });
__webpack_require__.d(table_namespaceObject, "name", function() { return table_name; });
__webpack_require__.d(table_namespaceObject, "settings", function() { return table_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/text-columns/index.js
var text_columns_namespaceObject = {};
__webpack_require__.r(text_columns_namespaceObject);
__webpack_require__.d(text_columns_namespaceObject, "metadata", function() { return text_columns_metadata; });
__webpack_require__.d(text_columns_namespaceObject, "name", function() { return text_columns_name; });
__webpack_require__.d(text_columns_namespaceObject, "settings", function() { return text_columns_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/verse/index.js
var verse_namespaceObject = {};
__webpack_require__.r(verse_namespaceObject);
__webpack_require__.d(verse_namespaceObject, "metadata", function() { return verse_metadata; });
__webpack_require__.d(verse_namespaceObject, "name", function() { return verse_name; });
__webpack_require__.d(verse_namespaceObject, "settings", function() { return verse_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/video/index.js
var video_namespaceObject = {};
__webpack_require__.r(video_namespaceObject);
__webpack_require__.d(video_namespaceObject, "metadata", function() { return video_metadata; });
__webpack_require__.d(video_namespaceObject, "name", function() { return video_name; });
__webpack_require__.d(video_namespaceObject, "settings", function() { return video_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/tag-cloud/index.js
var tag_cloud_namespaceObject = {};
__webpack_require__.r(tag_cloud_namespaceObject);
__webpack_require__.d(tag_cloud_namespaceObject, "name", function() { return tag_cloud_name; });
__webpack_require__.d(tag_cloud_namespaceObject, "settings", function() { return tag_cloud_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/block-library/build-module/classic/index.js
var classic_namespaceObject = {};
__webpack_require__.r(classic_namespaceObject);
__webpack_require__.d(classic_namespaceObject, "metadata", function() { return classic_metadata; });
__webpack_require__.d(classic_namespaceObject, "name", function() { return classic_name; });
__webpack_require__.d(classic_namespaceObject, "settings", function() { return classic_settings; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(17);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(10);

// EXTERNAL MODULE: external {"this":["wp","coreData"]}
var external_this_wp_coreData_ = __webpack_require__(89);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(24);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(9);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/paragraph/deprecated.js




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



var deprecated_supports = {
  className: false
};
var deprecated_blockAttributes = {
  align: {
    type: 'string'
  },
  content: {
    type: 'string',
    source: 'html',
    selector: 'p',
    default: ''
  },
  dropCap: {
    type: 'boolean',
    default: false
  },
  placeholder: {
    type: 'string'
  },
  textColor: {
    type: 'string'
  },
  customTextColor: {
    type: 'string'
  },
  backgroundColor: {
    type: 'string'
  },
  customBackgroundColor: {
    type: 'string'
  },
  fontSize: {
    type: 'string'
  },
  customFontSize: {
    type: 'number'
  },
  direction: {
    type: 'string',
    enum: ['ltr', 'rtl']
  }
};
var deprecated = [{
  supports: deprecated_supports,
  attributes: deprecated_blockAttributes,
  save: function save(_ref) {
    var _classnames;

    var attributes = _ref.attributes;
    var align = attributes.align,
        content = attributes.content,
        dropCap = attributes.dropCap,
        backgroundColor = attributes.backgroundColor,
        textColor = attributes.textColor,
        customBackgroundColor = attributes.customBackgroundColor,
        customTextColor = attributes.customTextColor,
        fontSize = attributes.fontSize,
        customFontSize = attributes.customFontSize,
        direction = attributes.direction;
    var textClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
    var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
    var fontSizeClass = Object(external_this_wp_blockEditor_["getFontSizeClass"])(fontSize);
    var className = classnames_default()((_classnames = {
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-drop-cap': dropCap
    }, Object(defineProperty["a" /* default */])(_classnames, fontSizeClass, fontSizeClass), Object(defineProperty["a" /* default */])(_classnames, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames, backgroundClass, backgroundClass), _classnames));
    var styles = {
      backgroundColor: backgroundClass ? undefined : customBackgroundColor,
      color: textClass ? undefined : customTextColor,
      fontSize: fontSizeClass ? undefined : customFontSize,
      textAlign: align
    };
    return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "p",
      style: styles,
      className: className ? className : undefined,
      value: content,
      dir: direction
    });
  }
}, {
  supports: deprecated_supports,
  attributes: Object(objectSpread["a" /* default */])({}, deprecated_blockAttributes, {
    width: {
      type: 'string'
    }
  }),
  save: function save(_ref2) {
    var _classnames2;

    var attributes = _ref2.attributes;
    var width = attributes.width,
        align = attributes.align,
        content = attributes.content,
        dropCap = attributes.dropCap,
        backgroundColor = attributes.backgroundColor,
        textColor = attributes.textColor,
        customBackgroundColor = attributes.customBackgroundColor,
        customTextColor = attributes.customTextColor,
        fontSize = attributes.fontSize,
        customFontSize = attributes.customFontSize;
    var textClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
    var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
    var fontSizeClass = fontSize && "is-".concat(fontSize, "-text");
    var className = classnames_default()((_classnames2 = {}, Object(defineProperty["a" /* default */])(_classnames2, "align".concat(width), width), Object(defineProperty["a" /* default */])(_classnames2, 'has-background', backgroundColor || customBackgroundColor), Object(defineProperty["a" /* default */])(_classnames2, 'has-drop-cap', dropCap), Object(defineProperty["a" /* default */])(_classnames2, fontSizeClass, fontSizeClass), Object(defineProperty["a" /* default */])(_classnames2, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames2, backgroundClass, backgroundClass), _classnames2));
    var styles = {
      backgroundColor: backgroundClass ? undefined : customBackgroundColor,
      color: textClass ? undefined : customTextColor,
      fontSize: fontSizeClass ? undefined : customFontSize,
      textAlign: align
    };
    return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "p",
      style: styles,
      className: className ? className : undefined,
      value: content
    });
  }
}, {
  supports: deprecated_supports,
  attributes: Object(external_lodash_["omit"])(Object(objectSpread["a" /* default */])({}, deprecated_blockAttributes, {
    fontSize: {
      type: 'number'
    }
  }), 'customFontSize', 'customTextColor', 'customBackgroundColor'),
  save: function save(_ref3) {
    var _classnames3;

    var attributes = _ref3.attributes;
    var width = attributes.width,
        align = attributes.align,
        content = attributes.content,
        dropCap = attributes.dropCap,
        backgroundColor = attributes.backgroundColor,
        textColor = attributes.textColor,
        fontSize = attributes.fontSize;
    var className = classnames_default()((_classnames3 = {}, Object(defineProperty["a" /* default */])(_classnames3, "align".concat(width), width), Object(defineProperty["a" /* default */])(_classnames3, 'has-background', backgroundColor), Object(defineProperty["a" /* default */])(_classnames3, 'has-drop-cap', dropCap), _classnames3));
    var styles = {
      backgroundColor: backgroundColor,
      color: textColor,
      fontSize: fontSize,
      textAlign: align
    };
    return Object(external_this_wp_element_["createElement"])("p", {
      style: styles,
      className: className ? className : undefined
    }, content);
  },
  migrate: function migrate(attributes) {
    return Object(external_lodash_["omit"])(Object(objectSpread["a" /* default */])({}, attributes, {
      customFontSize: Object(external_lodash_["isFinite"])(attributes.fontSize) ? attributes.fontSize : undefined,
      customTextColor: attributes.textColor && '#' === attributes.textColor[0] ? attributes.textColor : undefined,
      customBackgroundColor: attributes.backgroundColor && '#' === attributes.backgroundColor[0] ? attributes.backgroundColor : undefined
    }), ['fontSize', 'textColor', 'backgroundColor']);
  }
}, {
  supports: deprecated_supports,
  attributes: Object(objectSpread["a" /* default */])({}, deprecated_blockAttributes, {
    content: {
      type: 'string',
      source: 'html',
      default: ''
    }
  }),
  save: function save(_ref4) {
    var attributes = _ref4.attributes;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, attributes.content);
  },
  migrate: function migrate(attributes) {
    return attributes;
  }
}];
/* harmony default export */ var paragraph_deprecated = (deprecated);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(18);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(14);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(5);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(15);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(4);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/paragraph/edit.js











/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








var _window = window,
    getComputedStyle = _window.getComputedStyle;
var edit_name = 'core/paragraph';
var applyFallbackStyles = Object(external_this_wp_components_["withFallbackStyles"])(function (node, ownProps) {
  var _ownProps$attributes = ownProps.attributes,
      textColor = _ownProps$attributes.textColor,
      backgroundColor = _ownProps$attributes.backgroundColor,
      fontSize = _ownProps$attributes.fontSize,
      customFontSize = _ownProps$attributes.customFontSize;
  var editableNode = node.querySelector('[contenteditable="true"]'); //verify if editableNode is available, before using getComputedStyle.

  var computedStyles = editableNode ? getComputedStyle(editableNode) : null;
  return {
    fallbackBackgroundColor: backgroundColor || !computedStyles ? undefined : computedStyles.backgroundColor,
    fallbackTextColor: textColor || !computedStyles ? undefined : computedStyles.color,
    fallbackFontSize: fontSize || customFontSize || !computedStyles ? undefined : parseInt(computedStyles.fontSize) || undefined
  };
});

var edit_ParagraphBlock =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ParagraphBlock, _Component);

  function ParagraphBlock() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, ParagraphBlock);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ParagraphBlock).apply(this, arguments));
    _this.toggleDropCap = _this.toggleDropCap.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(ParagraphBlock, [{
    key: "toggleDropCap",
    value: function toggleDropCap() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      setAttributes({
        dropCap: !attributes.dropCap
      });
    }
  }, {
    key: "getDropCapHelp",
    value: function getDropCapHelp(checked) {
      return checked ? Object(external_this_wp_i18n_["__"])('Showing large initial letter.') : Object(external_this_wp_i18n_["__"])('Toggle to show a large initial letter.');
    }
  }, {
    key: "render",
    value: function render() {
      var _classnames;

      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes,
          mergeBlocks = _this$props2.mergeBlocks,
          onReplace = _this$props2.onReplace,
          className = _this$props2.className,
          backgroundColor = _this$props2.backgroundColor,
          textColor = _this$props2.textColor,
          setBackgroundColor = _this$props2.setBackgroundColor,
          setTextColor = _this$props2.setTextColor,
          fallbackBackgroundColor = _this$props2.fallbackBackgroundColor,
          fallbackTextColor = _this$props2.fallbackTextColor,
          fallbackFontSize = _this$props2.fallbackFontSize,
          fontSize = _this$props2.fontSize,
          setFontSize = _this$props2.setFontSize,
          isRTL = _this$props2.isRTL;
      var align = attributes.align,
          content = attributes.content,
          dropCap = attributes.dropCap,
          placeholder = attributes.placeholder,
          direction = attributes.direction;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["AlignmentToolbar"], {
        value: align,
        onChange: function onChange(nextAlign) {
          setAttributes({
            align: nextAlign
          });
        }
      }), isRTL && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
        controls: [{
          icon: 'editor-ltr',
          title: Object(external_this_wp_i18n_["_x"])('Left to right', 'editor button'),
          isActive: direction === 'ltr',
          onClick: function onClick() {
            var nextDirection = direction === 'ltr' ? undefined : 'ltr';
            setAttributes({
              direction: nextDirection
            });
          }
        }]
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Text Settings'),
        className: "blocks-font-size"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["FontSizePicker"], {
        fallbackFontSize: fallbackFontSize,
        value: fontSize.size,
        onChange: setFontSize
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Drop Cap'),
        checked: !!dropCap,
        onChange: this.toggleDropCap,
        help: this.getDropCapHelp
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
        title: Object(external_this_wp_i18n_["__"])('Color Settings'),
        initialOpen: false,
        colorSettings: [{
          value: backgroundColor.color,
          onChange: setBackgroundColor,
          label: Object(external_this_wp_i18n_["__"])('Background Color')
        }, {
          value: textColor.color,
          onChange: setTextColor,
          label: Object(external_this_wp_i18n_["__"])('Text Color')
        }]
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["ContrastChecker"], Object(esm_extends["a" /* default */])({
        textColor: textColor.color,
        backgroundColor: backgroundColor.color,
        fallbackTextColor: fallbackTextColor,
        fallbackBackgroundColor: fallbackBackgroundColor
      }, {
        fontSize: fontSize.size
      })))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        identifier: "content",
        tagName: "p",
        className: classnames_default()('wp-block-paragraph', className, (_classnames = {
          'has-text-color': textColor.color,
          'has-background': backgroundColor.color,
          'has-drop-cap': dropCap
        }, Object(defineProperty["a" /* default */])(_classnames, "has-text-align-".concat(align), align), Object(defineProperty["a" /* default */])(_classnames, backgroundColor.class, backgroundColor.class), Object(defineProperty["a" /* default */])(_classnames, textColor.class, textColor.class), Object(defineProperty["a" /* default */])(_classnames, fontSize.class, fontSize.class), _classnames)),
        style: {
          backgroundColor: backgroundColor.color,
          color: textColor.color,
          fontSize: fontSize.size ? fontSize.size + 'px' : undefined,
          direction: direction
        },
        value: content,
        onChange: function onChange(nextContent) {
          setAttributes({
            content: nextContent
          });
        },
        onSplit: function onSplit(value) {
          if (!value) {
            return Object(external_this_wp_blocks_["createBlock"])(edit_name);
          }

          return Object(external_this_wp_blocks_["createBlock"])(edit_name, Object(objectSpread["a" /* default */])({}, attributes, {
            content: value
          }));
        },
        onMerge: mergeBlocks,
        onReplace: onReplace,
        onRemove: onReplace ? function () {
          return onReplace([]);
        } : undefined,
        "aria-label": content ? Object(external_this_wp_i18n_["__"])('Paragraph block') : Object(external_this_wp_i18n_["__"])('Empty block; start writing or type forward slash to choose a block'),
        placeholder: placeholder || Object(external_this_wp_i18n_["__"])('Start writing or type / to choose a block'),
        __unstableEmbedURLOnPaste: true
      }));
    }
  }]);

  return ParagraphBlock;
}(external_this_wp_element_["Component"]);

var ParagraphEdit = Object(external_this_wp_compose_["compose"])([Object(external_this_wp_blockEditor_["withColors"])('backgroundColor', {
  textColor: 'color'
}), Object(external_this_wp_blockEditor_["withFontSizes"])('fontSize'), applyFallbackStyles, Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getSettings = _select.getSettings;

  return {
    isRTL: getSettings().isRTL
  };
})])(edit_ParagraphBlock);
/* harmony default export */ var paragraph_edit = (ParagraphEdit);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/paragraph/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var paragraph_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M11 5v7H9.5C7.6 12 6 10.4 6 8.5S7.6 5 9.5 5H11m8-2H9.5C6.5 3 4 5.5 4 8.5S6.5 14 9.5 14H11v7h2V5h2v16h2V5h2V3z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/paragraph/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function save_save(_ref) {
  var _classnames;

  var attributes = _ref.attributes;
  var align = attributes.align,
      content = attributes.content,
      dropCap = attributes.dropCap,
      backgroundColor = attributes.backgroundColor,
      textColor = attributes.textColor,
      customBackgroundColor = attributes.customBackgroundColor,
      customTextColor = attributes.customTextColor,
      fontSize = attributes.fontSize,
      customFontSize = attributes.customFontSize,
      direction = attributes.direction;
  var textClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
  var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
  var fontSizeClass = Object(external_this_wp_blockEditor_["getFontSizeClass"])(fontSize);
  var className = classnames_default()((_classnames = {
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-drop-cap': dropCap
  }, Object(defineProperty["a" /* default */])(_classnames, "has-text-align-".concat(align), align), Object(defineProperty["a" /* default */])(_classnames, fontSizeClass, fontSizeClass), Object(defineProperty["a" /* default */])(_classnames, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames, backgroundClass, backgroundClass), _classnames));
  var styles = {
    backgroundColor: backgroundClass ? undefined : customBackgroundColor,
    color: textClass ? undefined : customTextColor,
    fontSize: fontSizeClass ? undefined : customFontSize
  };
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "p",
    style: styles,
    className: className ? className : undefined,
    value: content,
    dir: direction
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/paragraph/transforms.js
/**
 * WordPress dependencies
 */

var transforms_transforms = {
  from: [{
    type: 'raw',
    // Paragraph is a fallback and should be matched last.
    priority: 20,
    selector: 'p',
    schema: {
      p: {
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      }
    }
  }]
};
/* harmony default export */ var paragraph_transforms = (transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/paragraph/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var paragraph_metadata = {
  name: "core/paragraph",
  category: "common",
  attributes: {
    align: {
      type: "string"
    },
    content: {
      type: "string",
      source: "html",
      selector: "p",
      "default": ""
    },
    dropCap: {
      type: "boolean",
      "default": false
    },
    placeholder: {
      type: "string"
    },
    textColor: {
      type: "string"
    },
    customTextColor: {
      type: "string"
    },
    backgroundColor: {
      type: "string"
    },
    customBackgroundColor: {
      type: "string"
    },
    fontSize: {
      type: "string"
    },
    customFontSize: {
      type: "number"
    },
    direction: {
      type: "string",
      "enum": ["ltr", "rtl"]
    }
  }
};


var paragraph_name = paragraph_metadata.name;

var paragraph_settings = {
  title: Object(external_this_wp_i18n_["__"])('Paragraph'),
  description: Object(external_this_wp_i18n_["__"])('Start with the building block of all narrative.'),
  icon: paragraph_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('text')],
  example: {
    attributes: {
      content: Object(external_this_wp_i18n_["__"])('In a village of La Mancha, the name of which I have no desire to call to mind, there lived not long since one of those gentlemen that keep a lance in the lance-rack, an old buckler, a lean hack, and a greyhound for coursing.'),
      customFontSize: 28,
      dropCap: true
    }
  },
  supports: {
    className: false
  },
  transforms: paragraph_transforms,
  deprecated: paragraph_deprecated,
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: (attributes.content || '') + (attributesToMerge.content || '')
    };
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var width = attributes.width;

    if (['wide', 'full', 'left', 'right'].indexOf(width) !== -1) {
      return {
        'data-align': width
      };
    }
  },
  edit: paragraph_edit,
  save: save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/deprecated.js




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


var image_deprecated_blockAttributes = {
  align: {
    type: 'string'
  },
  url: {
    type: 'string',
    source: 'attribute',
    selector: 'img',
    attribute: 'src'
  },
  alt: {
    type: 'string',
    source: 'attribute',
    selector: 'img',
    attribute: 'alt',
    default: ''
  },
  caption: {
    type: 'string',
    source: 'html',
    selector: 'figcaption'
  },
  href: {
    type: 'string',
    source: 'attribute',
    selector: 'figure > a',
    attribute: 'href'
  },
  rel: {
    type: 'string',
    source: 'attribute',
    selector: 'figure > a',
    attribute: 'rel'
  },
  linkClass: {
    type: 'string',
    source: 'attribute',
    selector: 'figure > a',
    attribute: 'class'
  },
  id: {
    type: 'number'
  },
  width: {
    type: 'number'
  },
  height: {
    type: 'number'
  },
  linkDestination: {
    type: 'string',
    default: 'none'
  },
  linkTarget: {
    type: 'string',
    source: 'attribute',
    selector: 'figure > a',
    attribute: 'target'
  }
};
var deprecated_deprecated = [{
  attributes: image_deprecated_blockAttributes,
  save: function save(_ref) {
    var _classnames;

    var attributes = _ref.attributes;
    var url = attributes.url,
        alt = attributes.alt,
        caption = attributes.caption,
        align = attributes.align,
        href = attributes.href,
        width = attributes.width,
        height = attributes.height,
        id = attributes.id;
    var classes = classnames_default()((_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, "align".concat(align), align), Object(defineProperty["a" /* default */])(_classnames, 'is-resized', width || height), _classnames));
    var image = Object(external_this_wp_element_["createElement"])("img", {
      src: url,
      alt: alt,
      className: id ? "wp-image-".concat(id) : null,
      width: width,
      height: height
    });
    return Object(external_this_wp_element_["createElement"])("figure", {
      className: classes
    }, href ? Object(external_this_wp_element_["createElement"])("a", {
      href: href
    }, image) : image, !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "figcaption",
      value: caption
    }));
  }
}, {
  attributes: image_deprecated_blockAttributes,
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    var url = attributes.url,
        alt = attributes.alt,
        caption = attributes.caption,
        align = attributes.align,
        href = attributes.href,
        width = attributes.width,
        height = attributes.height,
        id = attributes.id;
    var image = Object(external_this_wp_element_["createElement"])("img", {
      src: url,
      alt: alt,
      className: id ? "wp-image-".concat(id) : null,
      width: width,
      height: height
    });
    return Object(external_this_wp_element_["createElement"])("figure", {
      className: align ? "align".concat(align) : null
    }, href ? Object(external_this_wp_element_["createElement"])("a", {
      href: href
    }, image) : image, !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "figcaption",
      value: caption
    }));
  }
}, {
  attributes: image_deprecated_blockAttributes,
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    var url = attributes.url,
        alt = attributes.alt,
        caption = attributes.caption,
        align = attributes.align,
        href = attributes.href,
        width = attributes.width,
        height = attributes.height;
    var extraImageProps = width || height ? {
      width: width,
      height: height
    } : {};
    var image = Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
      src: url,
      alt: alt
    }, extraImageProps));
    var figureStyle = {};

    if (width) {
      figureStyle = {
        width: width
      };
    } else if (align === 'left' || align === 'right') {
      figureStyle = {
        maxWidth: '50%'
      };
    }

    return Object(external_this_wp_element_["createElement"])("figure", {
      className: align ? "align".concat(align) : null,
      style: figureStyle
    }, href ? Object(external_this_wp_element_["createElement"])("a", {
      href: href
    }, image) : image, !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "figcaption",
      value: caption
    }));
  }
}];
/* harmony default export */ var image_deprecated = (deprecated_deprecated);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(23);

// EXTERNAL MODULE: external {"this":["wp","blob"]}
var external_this_wp_blob_ = __webpack_require__(35);

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(19);

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(26);

// EXTERNAL MODULE: external {"this":["wp","viewport"]}
var external_this_wp_viewport_ = __webpack_require__(42);

// EXTERNAL MODULE: external {"this":["wp","a11y"]}
var external_this_wp_a11y_ = __webpack_require__(45);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/icons.js


/**
 * WordPress dependencies
 */

var embedContentIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M19,4H5C3.89,4,3,4.9,3,6v12c0,1.1,0.89,2,2,2h14c1.1,0,2-0.9,2-2V6C21,4.9,20.11,4,19,4z M19,18H5V8h14V18z"
}));
var embedAudioIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM8 15c0-1.66 1.34-3 3-3 .35 0 .69.07 1 .18V6h5v2h-3v7.03c-.02 1.64-1.35 2.97-3 2.97-1.66 0-3-1.34-3-3z"
}));
var embedPhotoIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M21,4H3C1.9,4,1,4.9,1,6v12c0,1.1,0.9,2,2,2h18c1.1,0,2-0.9,2-2V6C23,4.9,22.1,4,21,4z M21,18H3V6h18V18z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Polygon"], {
  points: "14.5 11 11 15.51 8.5 12.5 5 17 19 17"
}));
var embedVideoIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m10 8v8l5-4-5-4zm9-5h-14c-1.1 0-2 0.9-2 2v14c0 1.1 0.9 2 2 2h14c1.1 0 2-0.9 2-2v-14c0-1.1-0.9-2-2-2zm0 16h-14v-14h14v14z"
}));
var embedTwitterIcon = {
  foreground: '#1da1f2',
  src: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M22.23 5.924c-.736.326-1.527.547-2.357.646.847-.508 1.498-1.312 1.804-2.27-.793.47-1.67.812-2.606.996C18.325 4.498 17.258 4 16.078 4c-2.266 0-4.103 1.837-4.103 4.103 0 .322.036.635.106.935-3.41-.17-6.433-1.804-8.457-4.287-.353.607-.556 1.312-.556 2.064 0 1.424.724 2.68 1.825 3.415-.673-.022-1.305-.207-1.86-.514v.052c0 1.988 1.415 3.647 3.293 4.023-.344.095-.707.145-1.08.145-.265 0-.522-.026-.773-.074.522 1.63 2.038 2.817 3.833 2.85-1.404 1.1-3.174 1.757-5.096 1.757-.332 0-.66-.02-.98-.057 1.816 1.164 3.973 1.843 6.29 1.843 7.547 0 11.675-6.252 11.675-11.675 0-.178-.004-.355-.012-.53.802-.578 1.497-1.3 2.047-2.124z"
  })))
};
var embedYouTubeIcon = {
  foreground: '#ff0000',
  src: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M21.8 8s-.195-1.377-.795-1.984c-.76-.797-1.613-.8-2.004-.847-2.798-.203-6.996-.203-6.996-.203h-.01s-4.197 0-6.996.202c-.39.046-1.242.05-2.003.846C2.395 6.623 2.2 8 2.2 8S2 9.62 2 11.24v1.517c0 1.618.2 3.237.2 3.237s.195 1.378.795 1.985c.76.797 1.76.77 2.205.855 1.6.153 6.8.2 6.8.2s4.203-.005 7-.208c.392-.047 1.244-.05 2.005-.847.6-.607.795-1.985.795-1.985s.2-1.618.2-3.237v-1.517C22 9.62 21.8 8 21.8 8zM9.935 14.595v-5.62l5.403 2.82-5.403 2.8z"
  }))
};
var embedFacebookIcon = {
  foreground: '#3b5998',
  src: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M20 3H4c-.6 0-1 .4-1 1v16c0 .5.4 1 1 1h8.6v-7h-2.3v-2.7h2.3v-2c0-2.3 1.4-3.6 3.5-3.6 1 0 1.8.1 2.1.1v2.4h-1.4c-1.1 0-1.3.5-1.3 1.3v1.7h2.7l-.4 2.8h-2.3v7H20c.5 0 1-.4 1-1V4c0-.6-.4-1-1-1z"
  }))
};
var embedInstagramIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M12 4.622c2.403 0 2.688.01 3.637.052.877.04 1.354.187 1.67.31.42.163.72.358 1.036.673.315.315.51.615.673 1.035.123.317.27.794.31 1.67.043.95.052 1.235.052 3.638s-.01 2.688-.052 3.637c-.04.877-.187 1.354-.31 1.67-.163.42-.358.72-.673 1.036-.315.315-.615.51-1.035.673-.317.123-.794.27-1.67.31-.95.043-1.234.052-3.638.052s-2.688-.01-3.637-.052c-.877-.04-1.354-.187-1.67-.31-.42-.163-.72-.358-1.036-.673-.315-.315-.51-.615-.673-1.035-.123-.317-.27-.794-.31-1.67-.043-.95-.052-1.235-.052-3.638s.01-2.688.052-3.637c.04-.877.187-1.354.31-1.67.163-.42.358-.72.673-1.036.315-.315.615-.51 1.035-.673.317-.123.794-.27 1.67-.31.95-.043 1.235-.052 3.638-.052M12 3c-2.444 0-2.75.01-3.71.054s-1.613.196-2.185.418c-.592.23-1.094.538-1.594 1.04-.5.5-.807 1-1.037 1.593-.223.572-.375 1.226-.42 2.184C3.01 9.25 3 9.555 3 12s.01 2.75.054 3.71.196 1.613.418 2.186c.23.592.538 1.094 1.038 1.594s1.002.808 1.594 1.038c.572.222 1.227.375 2.185.418.96.044 1.266.054 3.71.054s2.75-.01 3.71-.054 1.613-.196 2.186-.418c.592-.23 1.094-.538 1.594-1.038s.808-1.002 1.038-1.594c.222-.572.375-1.227.418-2.185.044-.96.054-1.266.054-3.71s-.01-2.75-.054-3.71-.196-1.613-.418-2.186c-.23-.592-.538-1.094-1.038-1.594s-1.002-.808-1.594-1.038c-.572-.222-1.227-.375-2.185-.418C14.75 3.01 14.445 3 12 3zm0 4.378c-2.552 0-4.622 2.07-4.622 4.622s2.07 4.622 4.622 4.622 4.622-2.07 4.622-4.622S14.552 7.378 12 7.378zM12 15c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3zm4.804-8.884c-.596 0-1.08.484-1.08 1.08s.484 1.08 1.08 1.08c.596 0 1.08-.484 1.08-1.08s-.483-1.08-1.08-1.08z"
})));
var embedWordPressIcon = {
  foreground: '#0073AA',
  src: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M12.158 12.786l-2.698 7.84c.806.236 1.657.365 2.54.365 1.047 0 2.05-.18 2.986-.51-.024-.037-.046-.078-.065-.123l-2.762-7.57zM3.008 12c0 3.56 2.07 6.634 5.068 8.092L3.788 8.342c-.5 1.117-.78 2.354-.78 3.658zm15.06-.454c0-1.112-.398-1.88-.74-2.48-.456-.74-.883-1.368-.883-2.11 0-.825.627-1.595 1.51-1.595.04 0 .078.006.116.008-1.598-1.464-3.73-2.36-6.07-2.36-3.14 0-5.904 1.613-7.512 4.053.21.008.41.012.58.012.94 0 2.395-.114 2.395-.114.484-.028.54.684.057.74 0 0-.487.058-1.03.086l3.275 9.74 1.968-5.902-1.4-3.838c-.485-.028-.944-.085-.944-.085-.486-.03-.43-.77.056-.742 0 0 1.484.114 2.368.114.94 0 2.397-.114 2.397-.114.486-.028.543.684.058.74 0 0-.488.058-1.03.086l3.25 9.665.897-2.997c.456-1.17.684-2.137.684-2.907zm1.82-3.86c.04.286.06.593.06.924 0 .912-.17 1.938-.683 3.22l-2.746 7.94c2.672-1.558 4.47-4.454 4.47-7.77 0-1.564-.4-3.033-1.1-4.314zM12 22C6.486 22 2 17.514 2 12S6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"
  })))
};
var embedSpotifyIcon = {
  foreground: '#1db954',
  src: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2m4.586 14.424c-.18.295-.563.387-.857.207-2.35-1.434-5.305-1.76-8.786-.963-.335.077-.67-.133-.746-.47-.077-.334.132-.67.47-.745 3.808-.87 7.076-.496 9.712 1.115.293.18.386.563.206.857M17.81 13.7c-.226.367-.706.482-1.072.257-2.687-1.652-6.785-2.13-9.965-1.166-.413.127-.848-.106-.973-.517-.125-.413.108-.848.52-.973 3.632-1.102 8.147-.568 11.234 1.328.366.226.48.707.256 1.072m.105-2.835C14.692 8.95 9.375 8.775 6.297 9.71c-.493.15-1.016-.13-1.166-.624-.148-.495.13-1.017.625-1.167 3.532-1.073 9.404-.866 13.115 1.337.445.264.59.838.327 1.282-.264.443-.838.59-1.282.325"
  }))
};
var embedFlickrIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m6.5 7c-2.75 0-5 2.25-5 5s2.25 5 5 5 5-2.25 5-5-2.25-5-5-5zm11 0c-2.75 0-5 2.25-5 5s2.25 5 5 5 5-2.25 5-5-2.25-5-5-5z"
}));
var embedVimeoIcon = {
  foreground: '#1ab7ea',
  src: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M22.396 7.164c-.093 2.026-1.507 4.8-4.245 8.32C15.323 19.16 12.93 21 10.97 21c-1.214 0-2.24-1.12-3.08-3.36-.56-2.052-1.118-4.105-1.68-6.158-.622-2.24-1.29-3.36-2.004-3.36-.156 0-.7.328-1.634.98l-.978-1.26c1.027-.903 2.04-1.806 3.037-2.71C6 3.95 7.03 3.328 7.716 3.265c1.62-.156 2.616.95 2.99 3.32.404 2.558.685 4.148.84 4.77.468 2.12.982 3.18 1.543 3.18.435 0 1.09-.687 1.963-2.064.872-1.376 1.34-2.422 1.402-3.142.125-1.187-.343-1.782-1.4-1.782-.5 0-1.013.115-1.542.34 1.023-3.35 2.977-4.976 5.862-4.883 2.14.063 3.148 1.45 3.024 4.16z"
  })))
};
var embedRedditIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M22 11.816c0-1.256-1.02-2.277-2.277-2.277-.593 0-1.122.24-1.526.613-1.48-.965-3.455-1.594-5.647-1.69l1.17-3.702 3.18.75c.01 1.027.847 1.86 1.877 1.86 1.035 0 1.877-.84 1.877-1.877 0-1.035-.842-1.877-1.877-1.877-.77 0-1.43.466-1.72 1.13L13.55 3.92c-.204-.047-.4.067-.46.26l-1.35 4.27c-2.317.037-4.412.67-5.97 1.67-.402-.355-.917-.58-1.493-.58C3.02 9.54 2 10.56 2 11.815c0 .814.433 1.523 1.078 1.925-.037.222-.06.445-.06.673 0 3.292 4.01 5.97 8.94 5.97s8.94-2.678 8.94-5.97c0-.214-.02-.424-.052-.632.687-.39 1.154-1.12 1.154-1.964zm-3.224-7.422c.606 0 1.1.493 1.1 1.1s-.493 1.1-1.1 1.1-1.1-.494-1.1-1.1.493-1.1 1.1-1.1zm-16 7.422c0-.827.673-1.5 1.5-1.5.313 0 .598.103.838.27-.85.675-1.477 1.478-1.812 2.36-.32-.274-.525-.676-.525-1.13zm9.183 7.79c-4.502 0-8.165-2.33-8.165-5.193S7.457 9.22 11.96 9.22s8.163 2.33 8.163 5.193-3.663 5.193-8.164 5.193zM20.635 13c-.326-.89-.948-1.7-1.797-2.383.247-.186.55-.3.882-.3.827 0 1.5.672 1.5 1.5 0 .482-.23.91-.586 1.184zm-11.64 1.704c-.76 0-1.397-.616-1.397-1.376 0-.76.636-1.397 1.396-1.397.76 0 1.376.638 1.376 1.398 0 .76-.616 1.376-1.376 1.376zm7.405-1.376c0 .76-.615 1.376-1.375 1.376s-1.4-.616-1.4-1.376c0-.76.64-1.397 1.4-1.397.76 0 1.376.638 1.376 1.398zm-1.17 3.38c.15.152.15.398 0 .55-.675.674-1.728 1.002-3.22 1.002l-.01-.002-.012.002c-1.492 0-2.544-.328-3.218-1.002-.152-.152-.152-.398 0-.55.152-.152.4-.15.55 0 .52.52 1.394.775 2.67.775l.01.002.01-.002c1.276 0 2.15-.253 2.67-.775.15-.152.398-.152.55 0z"
}));
var embedTumbrIcon = {
  foreground: '#35465c',
  src: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M19 3H5c-1.105 0-2 .895-2 2v14c0 1.105.895 2 2 2h14c1.105 0 2-.895 2-2V5c0-1.105-.895-2-2-2zm-5.57 14.265c-2.445.042-3.37-1.742-3.37-2.998V10.6H8.922V9.15c1.703-.615 2.113-2.15 2.21-3.026.006-.06.053-.084.08-.084h1.645V8.9h2.246v1.7H12.85v3.495c.008.476.182 1.13 1.08 1.107.3-.008.698-.094.907-.194l.54 1.6c-.205.297-1.12.642-1.946.657z"
  }))
};
var embedAmazonIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M18.42 14.58c-.51-.66-1.05-1.23-1.05-2.5V7.87c0-1.8.15-3.45-1.2-4.68-1.05-1.02-2.79-1.35-4.14-1.35-2.6 0-5.52.96-6.12 4.14-.06.36.18.54.4.57l2.66.3c.24-.03.42-.27.48-.5.24-1.12 1.17-1.63 2.2-1.63.56 0 1.22.21 1.55.7.4.56.33 1.31.33 1.97v.36c-1.59.18-3.66.27-5.16.93a4.63 4.63 0 0 0-2.93 4.44c0 2.82 1.8 4.23 4.1 4.23 1.95 0 3.03-.45 4.53-1.98.51.72.66 1.08 1.59 1.83.18.09.45.09.63-.1v.04l2.1-1.8c.24-.21.2-.48.03-.75zm-5.4-1.2c-.45.75-1.14 1.23-1.92 1.23-1.05 0-1.65-.81-1.65-1.98 0-2.31 2.1-2.73 4.08-2.73v.6c0 1.05.03 1.92-.5 2.88z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M21.69 19.2a17.62 17.62 0 0 1-21.6-1.57c-.23-.2 0-.5.28-.33a23.88 23.88 0 0 0 20.93 1.3c.45-.19.84.3.39.6z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M22.8 17.96c-.36-.45-2.22-.2-3.1-.12-.23.03-.3-.18-.05-.36 1.5-1.05 3.96-.75 4.26-.39.3.36-.1 2.82-1.5 4.02-.21.18-.42.1-.3-.15.3-.8 1.02-2.58.69-3z"
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/core-embeds.js
/**
 * Internal dependencies
 */

/**
 * WordPress dependencies
 */



var common = [{
  name: 'core-embed/twitter',
  settings: {
    title: 'Twitter',
    icon: embedTwitterIcon,
    keywords: ['tweet'],
    description: Object(external_this_wp_i18n_["__"])('Embed a tweet.')
  },
  patterns: [/^https?:\/\/(www\.)?twitter\.com\/.+/i]
}, {
  name: 'core-embed/youtube',
  settings: {
    title: 'YouTube',
    icon: embedYouTubeIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('music'), Object(external_this_wp_i18n_["__"])('video')],
    description: Object(external_this_wp_i18n_["__"])('Embed a YouTube video.')
  },
  patterns: [/^https?:\/\/((m|www)\.)?youtube\.com\/.+/i, /^https?:\/\/youtu\.be\/.+/i]
}, {
  name: 'core-embed/facebook',
  settings: {
    title: 'Facebook',
    icon: embedFacebookIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed a Facebook post.')
  },
  patterns: [/^https?:\/\/www\.facebook.com\/.+/i]
}, {
  name: 'core-embed/instagram',
  settings: {
    title: 'Instagram',
    icon: embedInstagramIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('image')],
    description: Object(external_this_wp_i18n_["__"])('Embed an Instagram post.')
  },
  patterns: [/^https?:\/\/(www\.)?instagr(\.am|am\.com)\/.+/i]
}, {
  name: 'core-embed/wordpress',
  settings: {
    title: 'WordPress',
    icon: embedWordPressIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('post'), Object(external_this_wp_i18n_["__"])('blog')],
    responsive: false,
    description: Object(external_this_wp_i18n_["__"])('Embed a WordPress post.')
  }
}, {
  name: 'core-embed/soundcloud',
  settings: {
    title: 'SoundCloud',
    icon: embedAudioIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('music'), Object(external_this_wp_i18n_["__"])('audio')],
    description: Object(external_this_wp_i18n_["__"])('Embed SoundCloud content.')
  },
  patterns: [/^https?:\/\/(www\.)?soundcloud\.com\/.+/i]
}, {
  name: 'core-embed/spotify',
  settings: {
    title: 'Spotify',
    icon: embedSpotifyIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('music'), Object(external_this_wp_i18n_["__"])('audio')],
    description: Object(external_this_wp_i18n_["__"])('Embed Spotify content.')
  },
  patterns: [/^https?:\/\/(open|play)\.spotify\.com\/.+/i]
}, {
  name: 'core-embed/flickr',
  settings: {
    title: 'Flickr',
    icon: embedFlickrIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('image')],
    description: Object(external_this_wp_i18n_["__"])('Embed Flickr content.')
  },
  patterns: [/^https?:\/\/(www\.)?flickr\.com\/.+/i, /^https?:\/\/flic\.kr\/.+/i]
}, {
  name: 'core-embed/vimeo',
  settings: {
    title: 'Vimeo',
    icon: embedVimeoIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('video')],
    description: Object(external_this_wp_i18n_["__"])('Embed a Vimeo video.')
  },
  patterns: [/^https?:\/\/(www\.)?vimeo\.com\/.+/i]
}];
var others = [{
  name: 'core-embed/animoto',
  settings: {
    title: 'Animoto',
    icon: embedVideoIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed an Animoto video.')
  },
  patterns: [/^https?:\/\/(www\.)?(animoto|video214)\.com\/.+/i]
}, {
  name: 'core-embed/cloudup',
  settings: {
    title: 'Cloudup',
    icon: embedContentIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Cloudup content.')
  },
  patterns: [/^https?:\/\/cloudup\.com\/.+/i]
}, {
  // Deprecated since CollegeHumor content is now powered by YouTube
  name: 'core-embed/collegehumor',
  settings: {
    title: 'CollegeHumor',
    icon: embedVideoIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed CollegeHumor content.'),
    supports: {
      inserter: false
    }
  },
  patterns: []
}, {
  name: 'core-embed/crowdsignal',
  settings: {
    title: 'Crowdsignal',
    icon: embedContentIcon,
    keywords: ['polldaddy'],
    transform: [{
      type: 'block',
      blocks: ['core-embed/polldaddy'],
      transform: function transform(content) {
        return Object(external_this_wp_blocks_["createBlock"])('core-embed/crowdsignal', {
          content: content
        });
      }
    }],
    description: Object(external_this_wp_i18n_["__"])('Embed Crowdsignal (formerly Polldaddy) content.')
  },
  patterns: [/^https?:\/\/((.+\.)?polldaddy\.com|poll\.fm|.+\.survey\.fm)\/.+/i]
}, {
  name: 'core-embed/dailymotion',
  settings: {
    title: 'Dailymotion',
    icon: embedVideoIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed a Dailymotion video.')
  },
  patterns: [/^https?:\/\/(www\.)?dailymotion\.com\/.+/i]
}, {
  name: 'core-embed/hulu',
  settings: {
    title: 'Hulu',
    icon: embedVideoIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Hulu content.')
  },
  patterns: [/^https?:\/\/(www\.)?hulu\.com\/.+/i]
}, {
  name: 'core-embed/imgur',
  settings: {
    title: 'Imgur',
    icon: embedPhotoIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Imgur content.')
  },
  patterns: [/^https?:\/\/(.+\.)?imgur\.com\/.+/i]
}, {
  name: 'core-embed/issuu',
  settings: {
    title: 'Issuu',
    icon: embedContentIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Issuu content.')
  },
  patterns: [/^https?:\/\/(www\.)?issuu\.com\/.+/i]
}, {
  name: 'core-embed/kickstarter',
  settings: {
    title: 'Kickstarter',
    icon: embedContentIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Kickstarter content.')
  },
  patterns: [/^https?:\/\/(www\.)?kickstarter\.com\/.+/i, /^https?:\/\/kck\.st\/.+/i]
}, {
  name: 'core-embed/meetup-com',
  settings: {
    title: 'Meetup.com',
    icon: embedContentIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Meetup.com content.')
  },
  patterns: [/^https?:\/\/(www\.)?meetu(\.ps|p\.com)\/.+/i]
}, {
  name: 'core-embed/mixcloud',
  settings: {
    title: 'Mixcloud',
    icon: embedAudioIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('music'), Object(external_this_wp_i18n_["__"])('audio')],
    description: Object(external_this_wp_i18n_["__"])('Embed Mixcloud content.')
  },
  patterns: [/^https?:\/\/(www\.)?mixcloud\.com\/.+/i]
}, {
  // Deprecated in favour of the core-embed/crowdsignal block
  name: 'core-embed/polldaddy',
  settings: {
    title: 'Polldaddy',
    icon: embedContentIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Polldaddy content.'),
    supports: {
      inserter: false
    }
  },
  patterns: []
}, {
  name: 'core-embed/reddit',
  settings: {
    title: 'Reddit',
    icon: embedRedditIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed a Reddit thread.')
  },
  patterns: [/^https?:\/\/(www\.)?reddit\.com\/.+/i]
}, {
  name: 'core-embed/reverbnation',
  settings: {
    title: 'ReverbNation',
    icon: embedAudioIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed ReverbNation content.')
  },
  patterns: [/^https?:\/\/(www\.)?reverbnation\.com\/.+/i]
}, {
  name: 'core-embed/screencast',
  settings: {
    title: 'Screencast',
    icon: embedVideoIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Screencast content.')
  },
  patterns: [/^https?:\/\/(www\.)?screencast\.com\/.+/i]
}, {
  name: 'core-embed/scribd',
  settings: {
    title: 'Scribd',
    icon: embedContentIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Scribd content.')
  },
  patterns: [/^https?:\/\/(www\.)?scribd\.com\/.+/i]
}, {
  name: 'core-embed/slideshare',
  settings: {
    title: 'Slideshare',
    icon: embedContentIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed Slideshare content.')
  },
  patterns: [/^https?:\/\/(.+?\.)?slideshare\.net\/.+/i]
}, {
  name: 'core-embed/smugmug',
  settings: {
    title: 'SmugMug',
    icon: embedPhotoIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed SmugMug content.')
  },
  patterns: [/^https?:\/\/(www\.)?smugmug\.com\/.+/i]
}, {
  // Deprecated in favour of the core-embed/speaker-deck block.
  name: 'core-embed/speaker',
  settings: {
    title: 'Speaker',
    icon: embedAudioIcon,
    supports: {
      inserter: false
    }
  },
  patterns: []
}, {
  name: 'core-embed/speaker-deck',
  settings: {
    title: 'Speaker Deck',
    icon: embedContentIcon,
    transform: [{
      type: 'block',
      blocks: ['core-embed/speaker'],
      transform: function transform(content) {
        return Object(external_this_wp_blocks_["createBlock"])('core-embed/speaker-deck', {
          content: content
        });
      }
    }],
    description: Object(external_this_wp_i18n_["__"])('Embed Speaker Deck content.')
  },
  patterns: [/^https?:\/\/(www\.)?speakerdeck\.com\/.+/i]
}, {
  name: 'core-embed/ted',
  settings: {
    title: 'TED',
    icon: embedVideoIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed a TED video.')
  },
  patterns: [/^https?:\/\/(www\.|embed\.)?ted\.com\/.+/i]
}, {
  name: 'core-embed/tumblr',
  settings: {
    title: 'Tumblr',
    icon: embedTumbrIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed a Tumblr post.')
  },
  patterns: [/^https?:\/\/(www\.)?tumblr\.com\/.+/i]
}, {
  name: 'core-embed/videopress',
  settings: {
    title: 'VideoPress',
    icon: embedVideoIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('video')],
    description: Object(external_this_wp_i18n_["__"])('Embed a VideoPress video.')
  },
  patterns: [/^https?:\/\/videopress\.com\/.+/i]
}, {
  name: 'core-embed/wordpress-tv',
  settings: {
    title: 'WordPress.tv',
    icon: embedVideoIcon,
    description: Object(external_this_wp_i18n_["__"])('Embed a WordPress.tv video.')
  },
  patterns: [/^https?:\/\/wordpress\.tv\/.+/i]
}, {
  name: 'core-embed/amazon-kindle',
  settings: {
    title: 'Amazon Kindle',
    icon: embedAmazonIcon,
    keywords: [Object(external_this_wp_i18n_["__"])('ebook')],
    responsive: false,
    description: Object(external_this_wp_i18n_["__"])('Embed Amazon Kindle content.')
  },
  patterns: [/^https?:\/\/([a-z0-9-]+\.)?(amazon|amzn)(\.[a-z]{2,4})+\/.+/i, /^https?:\/\/(www\.)?(a\.co|z\.cn)\/.+/i]
}];

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/constants.js
// These embeds do not work in sandboxes due to the iframe's security restrictions.
var HOSTS_NO_PREVIEWS = ['facebook.com', 'smugmug.com'];
var ASPECT_RATIOS = [// Common video resolutions.
{
  ratio: '2.33',
  className: 'wp-embed-aspect-21-9'
}, {
  ratio: '2.00',
  className: 'wp-embed-aspect-18-9'
}, {
  ratio: '1.78',
  className: 'wp-embed-aspect-16-9'
}, {
  ratio: '1.33',
  className: 'wp-embed-aspect-4-3'
}, // Vertical video and instagram square video support.
{
  ratio: '1.00',
  className: 'wp-embed-aspect-1-1'
}, {
  ratio: '0.56',
  className: 'wp-embed-aspect-9-16'
}, {
  ratio: '0.50',
  className: 'wp-embed-aspect-1-2'
}];
var DEFAULT_EMBED_BLOCK = 'core/embed';
var WORDPRESS_EMBED_BLOCK = 'core-embed/wordpress';

// EXTERNAL MODULE: ./node_modules/classnames/dedupe.js
var dedupe = __webpack_require__(70);
var dedupe_default = /*#__PURE__*/__webpack_require__.n(dedupe);

// EXTERNAL MODULE: ./node_modules/memize/index.js
var memize = __webpack_require__(44);
var memize_default = /*#__PURE__*/__webpack_require__.n(memize);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/util.js





/**
 * Internal dependencies
 */


/**
 * External dependencies
 */




/**
 * WordPress dependencies
 */



/**
 * Returns true if any of the regular expressions match the URL.
 *
 * @param {string}   url      The URL to test.
 * @param {Array}    patterns The list of regular expressions to test agains.
 * @return {boolean} True if any of the regular expressions match the URL.
 */

var matchesPatterns = function matchesPatterns(url) {
  var patterns = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  return patterns.some(function (pattern) {
    return url.match(pattern);
  });
};
/**
 * Finds the block name that should be used for the URL, based on the
 * structure of the URL.
 *
 * @param {string}  url The URL to test.
 * @return {string} The name of the block that should be used for this URL, e.g. core-embed/twitter
 */

var util_findBlock = function findBlock(url) {
  for (var _i = 0, _arr = [].concat(Object(toConsumableArray["a" /* default */])(common), Object(toConsumableArray["a" /* default */])(others)); _i < _arr.length; _i++) {
    var block = _arr[_i];

    if (matchesPatterns(url, block.patterns)) {
      return block.name;
    }
  }

  return DEFAULT_EMBED_BLOCK;
};
var util_isFromWordPress = function isFromWordPress(html) {
  return Object(external_lodash_["includes"])(html, 'class="wp-embedded-content"');
};
var util_getPhotoHtml = function getPhotoHtml(photo) {
  // 100% width for the preview so it fits nicely into the document, some "thumbnails" are
  // actually the full size photo. If thumbnails not found, use full image.
  var imageUrl = photo.thumbnail_url ? photo.thumbnail_url : photo.url;
  var photoPreview = Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_element_["createElement"])("img", {
    src: imageUrl,
    alt: photo.title,
    width: "100%"
  }));
  return Object(external_this_wp_element_["renderToString"])(photoPreview);
};
/**
 * Creates a more suitable embed block based on the passed in props
 * and attributes generated from an embed block's preview.
 *
 * We require `attributesFromPreview` to be generated from the latest attributes
 * and preview, and because of the way the react lifecycle operates, we can't
 * guarantee that the attributes contained in the block's props are the latest
 * versions, so we require that these are generated separately.
 * See `getAttributesFromPreview` in the generated embed edit component.
 *
 * @param {Object} props                  The block's props.
 * @param {Object} attributesFromPreview  Attributes generated from the block's most up to date preview.
 * @return {Object|undefined} A more suitable embed block if one exists.
 */

var util_createUpgradedEmbedBlock = function createUpgradedEmbedBlock(props, attributesFromPreview) {
  var preview = props.preview,
      name = props.name;
  var url = props.attributes.url;

  if (!url) {
    return;
  }

  var matchingBlock = util_findBlock(url);

  if (!Object(external_this_wp_blocks_["getBlockType"])(matchingBlock)) {
    return;
  } // WordPress blocks can work on multiple sites, and so don't have patterns,
  // so if we're in a WordPress block, assume the user has chosen it for a WordPress URL.


  if (WORDPRESS_EMBED_BLOCK !== name && DEFAULT_EMBED_BLOCK !== matchingBlock) {
    // At this point, we have discovered a more suitable block for this url, so transform it.
    if (name !== matchingBlock) {
      return Object(external_this_wp_blocks_["createBlock"])(matchingBlock, {
        url: url
      });
    }
  }

  if (preview) {
    var html = preview.html; // We can't match the URL for WordPress embeds, we have to check the HTML instead.

    if (util_isFromWordPress(html)) {
      // If this is not the WordPress embed block, transform it into one.
      if (WORDPRESS_EMBED_BLOCK !== name) {
        return Object(external_this_wp_blocks_["createBlock"])(WORDPRESS_EMBED_BLOCK, Object(objectSpread["a" /* default */])({
          url: url
        }, attributesFromPreview));
      }
    }
  }
};
/**
 * Returns class names with any relevant responsive aspect ratio names.
 *
 * @param {string}  html               The preview HTML that possibly contains an iframe with width and height set.
 * @param {string}  existingClassNames Any existing class names.
 * @param {boolean} allowResponsive    If the responsive class names should be added, or removed.
 * @return {string} Deduped class names.
 */

function getClassNames(html) {
  var existingClassNames = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  var allowResponsive = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;

  if (!allowResponsive) {
    // Remove all of the aspect ratio related class names.
    var aspectRatioClassNames = {
      'wp-has-aspect-ratio': false
    };

    for (var ratioIndex = 0; ratioIndex < ASPECT_RATIOS.length; ratioIndex++) {
      var aspectRatioToRemove = ASPECT_RATIOS[ratioIndex];
      aspectRatioClassNames[aspectRatioToRemove.className] = false;
    }

    return dedupe_default()(existingClassNames, aspectRatioClassNames);
  }

  var previewDocument = document.implementation.createHTMLDocument('');
  previewDocument.body.innerHTML = html;
  var iframe = previewDocument.body.querySelector('iframe'); // If we have a fixed aspect iframe, and it's a responsive embed block.

  if (iframe && iframe.height && iframe.width) {
    var aspectRatio = (iframe.width / iframe.height).toFixed(2); // Given the actual aspect ratio, find the widest ratio to support it.

    for (var _ratioIndex = 0; _ratioIndex < ASPECT_RATIOS.length; _ratioIndex++) {
      var potentialRatio = ASPECT_RATIOS[_ratioIndex];

      if (aspectRatio >= potentialRatio.ratio) {
        var _classnames;

        return dedupe_default()(existingClassNames, (_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, potentialRatio.className, allowResponsive), Object(defineProperty["a" /* default */])(_classnames, 'wp-has-aspect-ratio', allowResponsive), _classnames));
      }
    }
  }

  return existingClassNames;
}
/**
 * Fallback behaviour for unembeddable URLs.
 * Creates a paragraph block containing a link to the URL, and calls `onReplace`.
 *
 * @param {string}   url       The URL that could not be embedded.
 * @param {Function} onReplace Function to call with the created fallback block.
 */

function util_fallback(url, onReplace) {
  var link = Object(external_this_wp_element_["createElement"])("a", {
    href: url
  }, url);
  onReplace(Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
    content: Object(external_this_wp_element_["renderToString"])(link)
  }));
}
/***
 * Gets block attributes based on the preview and responsive state.
 *
 * @param {Object} preview The preview data.
 * @param {string} title The block's title, e.g. Twitter.
 * @param {Object} currentClassNames The block's current class names.
 * @param {boolean} isResponsive Boolean indicating if the block supports responsive content.
 * @param {boolean} allowResponsive Apply responsive classes to fixed size content.
 * @return {Object} Attributes and values.
 */

var getAttributesFromPreview = memize_default()(function (preview, title, currentClassNames, isResponsive) {
  var allowResponsive = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;

  if (!preview) {
    return {};
  }

  var attributes = {}; // Some plugins only return HTML with no type info, so default this to 'rich'.

  var _preview$type = preview.type,
      type = _preview$type === void 0 ? 'rich' : _preview$type; // If we got a provider name from the API, use it for the slug, otherwise we use the title,
  // because not all embed code gives us a provider name.

  var html = preview.html,
      providerName = preview.provider_name;
  var providerNameSlug = Object(external_lodash_["kebabCase"])(Object(external_lodash_["toLower"])('' !== providerName ? providerName : title));

  if (util_isFromWordPress(html)) {
    type = 'wp-embed';
  }

  if (html || 'photo' === type) {
    attributes.type = type;
    attributes.providerNameSlug = providerNameSlug;
  }

  attributes.className = getClassNames(html, currentClassNames, isResponsive && allowResponsive);
  return attributes;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var image_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m19 5v14h-14v-14h14m0-2h-14c-1.1 0-2 0.9-2 2v14c0 1.1 0.9 2 2 2h14c1.1 0 2-0.9 2-2v-14c0-1.1-0.9-2-2-2z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m14.14 11.86l-3 3.87-2.14-2.59-3 3.86h12l-3.86-5.14z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/utils.js
function calculatePreferedImageSize(image, container) {
  var maxWidth = container.clientWidth;
  var exceedMaxWidth = image.width > maxWidth;
  var ratio = image.height / image.width;
  var width = exceedMaxWidth ? maxWidth : image.width;
  var height = exceedMaxWidth ? maxWidth * ratio : image.height;
  return {
    width: width,
    height: height
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/image-size.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



var image_size_ImageSize =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ImageSize, _Component);

  function ImageSize() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, ImageSize);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ImageSize).apply(this, arguments));
    _this.state = {
      width: undefined,
      height: undefined
    };
    _this.bindContainer = _this.bindContainer.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.calculateSize = _this.calculateSize.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(ImageSize, [{
    key: "bindContainer",
    value: function bindContainer(ref) {
      this.container = ref;
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.src !== prevProps.src) {
        this.setState({
          width: undefined,
          height: undefined
        });
        this.fetchImageSize();
      }

      if (this.props.dirtynessTrigger !== prevProps.dirtynessTrigger) {
        this.calculateSize();
      }
    }
  }, {
    key: "componentDidMount",
    value: function componentDidMount() {
      this.fetchImageSize();
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      if (this.image) {
        this.image.onload = external_lodash_["noop"];
      }
    }
  }, {
    key: "fetchImageSize",
    value: function fetchImageSize() {
      this.image = new window.Image();
      this.image.onload = this.calculateSize;
      this.image.src = this.props.src;
    }
  }, {
    key: "calculateSize",
    value: function calculateSize() {
      var _calculatePreferedIma = calculatePreferedImageSize(this.image, this.container),
          width = _calculatePreferedIma.width,
          height = _calculatePreferedIma.height;

      this.setState({
        width: width,
        height: height
      });
    }
  }, {
    key: "render",
    value: function render() {
      var sizes = {
        imageWidth: this.image && this.image.width,
        imageHeight: this.image && this.image.height,
        containerWidth: this.container && this.container.clientWidth,
        containerHeight: this.container && this.container.clientHeight,
        imageWidthWithinContainer: this.state.width,
        imageHeightWithinContainer: this.state.height
      };
      return Object(external_this_wp_element_["createElement"])("div", {
        ref: this.bindContainer
      }, this.props.children(sizes));
    }
  }]);

  return ImageSize;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var image_size = (Object(external_this_wp_compose_["withGlobalEvents"])({
  resize: 'calculateSize'
})(image_size_ImageSize));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/edit.js











/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */












/**
 * Internal dependencies
 */




/**
 * Module constants
 */

var MIN_SIZE = 20;
var LINK_DESTINATION_NONE = 'none';
var LINK_DESTINATION_MEDIA = 'media';
var LINK_DESTINATION_ATTACHMENT = 'attachment';
var LINK_DESTINATION_CUSTOM = 'custom';
var NEW_TAB_REL = 'noreferrer noopener';
var ALLOWED_MEDIA_TYPES = ['image'];
var DEFAULT_SIZE_SLUG = 'large';
var edit_pickRelevantMediaFiles = function pickRelevantMediaFiles(image) {
  var imageProps = Object(external_lodash_["pick"])(image, ['alt', 'id', 'link', 'caption']);
  imageProps.url = Object(external_lodash_["get"])(image, ['sizes', 'large', 'url']) || Object(external_lodash_["get"])(image, ['media_details', 'sizes', 'large', 'source_url']) || image.url;
  return imageProps;
};
/**
 * Is the URL a temporary blob URL? A blob URL is one that is used temporarily
 * while the image is being uploaded and will not have an id yet allocated.
 *
 * @param {number=} id The id of the image.
 * @param {string=} url The url of the image.
 *
 * @return {boolean} Is the URL a Blob URL
 */

var edit_isTemporaryImage = function isTemporaryImage(id, url) {
  return !id && Object(external_this_wp_blob_["isBlobURL"])(url);
};
/**
 * Is the url for the image hosted externally. An externally hosted image has no id
 * and is not a blob url.
 *
 * @param {number=} id  The id of the image.
 * @param {string=} url The url of the image.
 *
 * @return {boolean} Is the url an externally hosted url?
 */


var edit_isExternalImage = function isExternalImage(id, url) {
  return url && !id && !Object(external_this_wp_blob_["isBlobURL"])(url);
};

var stopPropagation = function stopPropagation(event) {
  event.stopPropagation();
};

var edit_stopPropagationRelevantKeys = function stopPropagationRelevantKeys(event) {
  if ([external_this_wp_keycodes_["LEFT"], external_this_wp_keycodes_["DOWN"], external_this_wp_keycodes_["RIGHT"], external_this_wp_keycodes_["UP"], external_this_wp_keycodes_["BACKSPACE"], external_this_wp_keycodes_["ENTER"]].indexOf(event.keyCode) > -1) {
    // Stop the key event from propagating up to ObserveTyping.startTypingInTextField.
    event.stopPropagation();
  }
};

var edit_ImageURLInputUI = function ImageURLInputUI(_ref) {
  var advancedOptions = _ref.advancedOptions,
      linkDestination = _ref.linkDestination,
      mediaLinks = _ref.mediaLinks,
      onChangeUrl = _ref.onChangeUrl,
      url = _ref.url;

  var _useState = Object(external_this_wp_element_["useState"])(false),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      isOpen = _useState2[0],
      setIsOpen = _useState2[1];

  var openLinkUI = Object(external_this_wp_element_["useCallback"])(function () {
    setIsOpen(true);
  });

  var _useState3 = Object(external_this_wp_element_["useState"])(false),
      _useState4 = Object(slicedToArray["a" /* default */])(_useState3, 2),
      isEditingLink = _useState4[0],
      setIsEditingLink = _useState4[1];

  var _useState5 = Object(external_this_wp_element_["useState"])(null),
      _useState6 = Object(slicedToArray["a" /* default */])(_useState5, 2),
      urlInput = _useState6[0],
      setUrlInput = _useState6[1];

  var startEditLink = Object(external_this_wp_element_["useCallback"])(function () {
    if (linkDestination === LINK_DESTINATION_MEDIA || linkDestination === LINK_DESTINATION_ATTACHMENT) {
      setUrlInput('');
    }

    setIsEditingLink(true);
  });
  var stopEditLink = Object(external_this_wp_element_["useCallback"])(function () {
    setIsEditingLink(false);
  });
  var closeLinkUI = Object(external_this_wp_element_["useCallback"])(function () {
    setUrlInput(null);
    stopEditLink();
    setIsOpen(false);
  });
  var autocompleteRef = Object(external_this_wp_element_["useRef"])(null);
  var onClickOutside = Object(external_this_wp_element_["useCallback"])(function () {
    return function (event) {
      // The autocomplete suggestions list renders in a separate popover (in a portal),
      // so onClickOutside fails to detect that a click on a suggestion occurred in the
      // LinkContainer. Detect clicks on autocomplete suggestions using a ref here, and
      // return to avoid the popover being closed.
      var autocompleteElement = autocompleteRef.current;

      if (autocompleteElement && autocompleteElement.contains(event.target)) {
        return;
      }

      setIsOpen(false);
      setUrlInput(null);
      stopEditLink();
    };
  });
  var onSubmitLinkChange = Object(external_this_wp_element_["useCallback"])(function () {
    return function (event) {
      if (urlInput) {
        onChangeUrl(urlInput);
      }

      stopEditLink();
      setUrlInput(null);
      event.preventDefault();
    };
  });
  var onLinkRemove = Object(external_this_wp_element_["useCallback"])(function () {
    closeLinkUI();
    onChangeUrl('');
  });
  var linkEditorValue = urlInput !== null ? urlInput : url;
  var urlLabel = (Object(external_lodash_["find"])(mediaLinks, ['linkDestination', linkDestination]) || {}).title;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: "admin-links",
    className: "components-toolbar__control",
    label: url ? Object(external_this_wp_i18n_["__"])('Edit link') : Object(external_this_wp_i18n_["__"])('Insert link'),
    "aria-expanded": isOpen,
    onClick: openLinkUI
  }), isOpen && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["URLPopover"], {
    onClickOutside: onClickOutside(),
    onClose: closeLinkUI,
    renderSettings: function renderSettings() {
      return advancedOptions;
    },
    additionalControls: !linkEditorValue && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["NavigableMenu"], null, Object(external_lodash_["map"])(mediaLinks, function (link) {
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
        key: link.linkDestination,
        icon: link.icon,
        onClick: function onClick() {
          setUrlInput(null);
          onChangeUrl(link.url);
          stopEditLink();
        }
      }, link.title);
    }))
  }, (!url || isEditingLink) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["URLPopover"].LinkEditor, {
    className: "editor-format-toolbar__link-container-content block-editor-format-toolbar__link-container-content",
    value: linkEditorValue,
    onChangeInputValue: setUrlInput,
    onKeyDown: edit_stopPropagationRelevantKeys,
    onKeyPress: stopPropagation,
    onSubmit: onSubmitLinkChange(),
    autocompleteRef: autocompleteRef
  }), url && !isEditingLink && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["URLPopover"].LinkViewer, {
    className: "editor-format-toolbar__link-container-content block-editor-format-toolbar__link-container-content",
    onKeyPress: stopPropagation,
    url: url,
    onEditLinkClick: startEditLink,
    urlLabel: urlLabel
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: "no",
    label: Object(external_this_wp_i18n_["__"])('Remove link'),
    onClick: onLinkRemove
  }))));
};

var edit_ImageEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ImageEdit, _Component);

  function ImageEdit(_ref2) {
    var _this;

    var attributes = _ref2.attributes;

    Object(classCallCheck["a" /* default */])(this, ImageEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ImageEdit).apply(this, arguments));
    _this.updateAlt = _this.updateAlt.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.updateAlignment = _this.updateAlignment.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onFocusCaption = _this.onFocusCaption.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onImageClick = _this.onImageClick.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSelectImage = _this.onSelectImage.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSelectURL = _this.onSelectURL.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.updateImage = _this.updateImage.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.updateWidth = _this.updateWidth.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.updateHeight = _this.updateHeight.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.updateDimensions = _this.updateDimensions.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSetHref = _this.onSetHref.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSetLinkClass = _this.onSetLinkClass.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSetLinkRel = _this.onSetLinkRel.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSetNewTab = _this.onSetNewTab.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.getFilename = _this.getFilename.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.toggleIsEditing = _this.toggleIsEditing.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onUploadError = _this.onUploadError.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onImageError = _this.onImageError.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.getLinkDestinations = _this.getLinkDestinations.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.state = {
      captionFocused: false,
      isEditing: !attributes.url
    };
    return _this;
  }

  Object(createClass["a" /* default */])(ImageEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          mediaUpload = _this$props.mediaUpload,
          noticeOperations = _this$props.noticeOperations;
      var id = attributes.id,
          _attributes$url = attributes.url,
          url = _attributes$url === void 0 ? '' : _attributes$url;

      if (edit_isTemporaryImage(id, url)) {
        var file = Object(external_this_wp_blob_["getBlobByURL"])(url);

        if (file) {
          mediaUpload({
            filesList: [file],
            onFileChange: function onFileChange(_ref3) {
              var _ref4 = Object(slicedToArray["a" /* default */])(_ref3, 1),
                  image = _ref4[0];

              _this2.onSelectImage(image);
            },
            allowedTypes: ALLOWED_MEDIA_TYPES,
            onError: function onError(message) {
              noticeOperations.createErrorNotice(message);

              _this2.setState({
                isEditing: true
              });
            }
          });
        }
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _prevProps$attributes = prevProps.attributes,
          prevID = _prevProps$attributes.id,
          _prevProps$attributes2 = _prevProps$attributes.url,
          prevURL = _prevProps$attributes2 === void 0 ? '' : _prevProps$attributes2;
      var _this$props$attribute = this.props.attributes,
          id = _this$props$attribute.id,
          _this$props$attribute2 = _this$props$attribute.url,
          url = _this$props$attribute2 === void 0 ? '' : _this$props$attribute2;

      if (edit_isTemporaryImage(prevID, prevURL) && !edit_isTemporaryImage(id, url)) {
        Object(external_this_wp_blob_["revokeBlobURL"])(url);
      }

      if (!this.props.isSelected && prevProps.isSelected && this.state.captionFocused) {
        this.setState({
          captionFocused: false
        });
      }
    }
  }, {
    key: "onUploadError",
    value: function onUploadError(message) {
      var noticeOperations = this.props.noticeOperations;
      noticeOperations.removeAllNotices();
      noticeOperations.createErrorNotice(message);
      this.setState({
        isEditing: true
      });
    }
  }, {
    key: "onSelectImage",
    value: function onSelectImage(media) {
      if (!media || !media.url) {
        this.props.setAttributes({
          url: undefined,
          alt: undefined,
          id: undefined,
          caption: undefined
        });
        return;
      }

      this.setState({
        isEditing: false
      });
      var _this$props$attribute3 = this.props.attributes,
          id = _this$props$attribute3.id,
          url = _this$props$attribute3.url,
          alt = _this$props$attribute3.alt,
          caption = _this$props$attribute3.caption;
      var mediaAttributes = edit_pickRelevantMediaFiles(media); // If the current image is temporary but an alt or caption text was meanwhile written by the user,
      // make sure the text is not overwritten.

      if (edit_isTemporaryImage(id, url)) {
        if (alt) {
          mediaAttributes = Object(external_lodash_["omit"])(mediaAttributes, ['alt']);
        }

        if (caption) {
          mediaAttributes = Object(external_lodash_["omit"])(mediaAttributes, ['caption']);
        }
      }

      var additionalAttributes; // Reset the dimension attributes if changing to a different image.

      if (!media.id || media.id !== id) {
        additionalAttributes = {
          width: undefined,
          height: undefined,
          sizeSlug: DEFAULT_SIZE_SLUG
        };
      } else {
        // Keep the same url when selecting the same file, so "Image Size" option is not changed.
        additionalAttributes = {
          url: url
        };
      }

      this.props.setAttributes(Object(objectSpread["a" /* default */])({}, mediaAttributes, additionalAttributes));
    }
  }, {
    key: "onSelectURL",
    value: function onSelectURL(newURL) {
      var url = this.props.attributes.url;

      if (newURL !== url) {
        this.props.setAttributes({
          url: newURL,
          id: undefined,
          sizeSlug: DEFAULT_SIZE_SLUG
        });
      }

      this.setState({
        isEditing: false
      });
    }
  }, {
    key: "onImageError",
    value: function onImageError(url) {
      // Check if there's an embed block that handles this URL.
      var embedBlock = util_createUpgradedEmbedBlock({
        attributes: {
          url: url
        }
      });

      if (undefined !== embedBlock) {
        this.props.onReplace(embedBlock);
      }
    }
  }, {
    key: "onSetHref",
    value: function onSetHref(value) {
      var linkDestinations = this.getLinkDestinations();
      var attributes = this.props.attributes;
      var linkDestination = attributes.linkDestination;
      var linkDestinationInput;

      if (!value) {
        linkDestinationInput = LINK_DESTINATION_NONE;
      } else {
        linkDestinationInput = (Object(external_lodash_["find"])(linkDestinations, function (destination) {
          return destination.url === value;
        }) || {
          linkDestination: LINK_DESTINATION_CUSTOM
        }).linkDestination;
      }

      if (linkDestination !== linkDestinationInput) {
        this.props.setAttributes({
          linkDestination: linkDestinationInput,
          href: value
        });
        return;
      }

      this.props.setAttributes({
        href: value
      });
    }
  }, {
    key: "onSetLinkClass",
    value: function onSetLinkClass(value) {
      this.props.setAttributes({
        linkClass: value
      });
    }
  }, {
    key: "onSetLinkRel",
    value: function onSetLinkRel(value) {
      this.props.setAttributes({
        rel: value
      });
    }
  }, {
    key: "onSetNewTab",
    value: function onSetNewTab(value) {
      var rel = this.props.attributes.rel;
      var linkTarget = value ? '_blank' : undefined;
      var updatedRel = rel;

      if (linkTarget && !rel) {
        updatedRel = NEW_TAB_REL;
      } else if (!linkTarget && rel === NEW_TAB_REL) {
        updatedRel = undefined;
      }

      this.props.setAttributes({
        linkTarget: linkTarget,
        rel: updatedRel
      });
    }
  }, {
    key: "onFocusCaption",
    value: function onFocusCaption() {
      if (!this.state.captionFocused) {
        this.setState({
          captionFocused: true
        });
      }
    }
  }, {
    key: "onImageClick",
    value: function onImageClick() {
      if (this.state.captionFocused) {
        this.setState({
          captionFocused: false
        });
      }
    }
  }, {
    key: "updateAlt",
    value: function updateAlt(newAlt) {
      this.props.setAttributes({
        alt: newAlt
      });
    }
  }, {
    key: "updateAlignment",
    value: function updateAlignment(nextAlign) {
      var extraUpdatedAttributes = ['wide', 'full'].indexOf(nextAlign) !== -1 ? {
        width: undefined,
        height: undefined
      } : {};
      this.props.setAttributes(Object(objectSpread["a" /* default */])({}, extraUpdatedAttributes, {
        align: nextAlign
      }));
    }
  }, {
    key: "updateImage",
    value: function updateImage(sizeSlug) {
      var image = this.props.image;
      var url = Object(external_lodash_["get"])(image, ['media_details', 'sizes', sizeSlug, 'source_url']);

      if (!url) {
        return null;
      }

      this.props.setAttributes({
        url: url,
        width: undefined,
        height: undefined,
        sizeSlug: sizeSlug
      });
    }
  }, {
    key: "updateWidth",
    value: function updateWidth(width) {
      this.props.setAttributes({
        width: parseInt(width, 10)
      });
    }
  }, {
    key: "updateHeight",
    value: function updateHeight(height) {
      this.props.setAttributes({
        height: parseInt(height, 10)
      });
    }
  }, {
    key: "updateDimensions",
    value: function updateDimensions() {
      var _this3 = this;

      var width = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : undefined;
      var height = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;
      return function () {
        _this3.props.setAttributes({
          width: width,
          height: height
        });
      };
    }
  }, {
    key: "getFilename",
    value: function getFilename(url) {
      var path = Object(external_this_wp_url_["getPath"])(url);

      if (path) {
        return Object(external_lodash_["last"])(path.split('/'));
      }
    }
  }, {
    key: "getLinkDestinations",
    value: function getLinkDestinations() {
      return [{
        linkDestination: LINK_DESTINATION_MEDIA,
        title: Object(external_this_wp_i18n_["__"])('Media File'),
        url: this.props.image && this.props.image.source_url || this.props.attributes.url,
        icon: image_icon
      }, {
        linkDestination: LINK_DESTINATION_ATTACHMENT,
        title: Object(external_this_wp_i18n_["__"])('Attachment Page'),
        url: this.props.image && this.props.image.link,
        icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
          viewBox: "0 0 24 24",
          xmlns: "http://www.w3.org/2000/svg"
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
          d: "M0 0h24v24H0V0z",
          fill: "none"
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
          d: "M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"
        }))
      }];
    }
  }, {
    key: "toggleIsEditing",
    value: function toggleIsEditing() {
      this.setState({
        isEditing: !this.state.isEditing
      });

      if (this.state.isEditing) {
        Object(external_this_wp_a11y_["speak"])(Object(external_this_wp_i18n_["__"])('You are now viewing the image in the image block.'));
      } else {
        Object(external_this_wp_a11y_["speak"])(Object(external_this_wp_i18n_["__"])('You are now editing the image in the image block.'));
      }
    }
  }, {
    key: "getImageSizeOptions",
    value: function getImageSizeOptions() {
      var imageSizes = this.props.imageSizes;
      return Object(external_lodash_["map"])(imageSizes, function (_ref5) {
        var name = _ref5.name,
            slug = _ref5.slug;
        return {
          value: slug,
          label: name
        };
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this4 = this;

      var isEditing = this.state.isEditing;
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes,
          isLargeViewport = _this$props2.isLargeViewport,
          isSelected = _this$props2.isSelected,
          className = _this$props2.className,
          maxWidth = _this$props2.maxWidth,
          noticeUI = _this$props2.noticeUI,
          isRTL = _this$props2.isRTL,
          onResizeStart = _this$props2.onResizeStart,
          _onResizeStop = _this$props2.onResizeStop;
      var url = attributes.url,
          alt = attributes.alt,
          caption = attributes.caption,
          align = attributes.align,
          id = attributes.id,
          href = attributes.href,
          rel = attributes.rel,
          linkClass = attributes.linkClass,
          linkDestination = attributes.linkDestination,
          width = attributes.width,
          height = attributes.height,
          linkTarget = attributes.linkTarget,
          sizeSlug = attributes.sizeSlug;
      var isExternal = edit_isExternalImage(id, url);
      var editImageIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
        width: 20,
        height: 20,
        viewBox: "0 0 20 20"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
        x: 11,
        y: 3,
        width: 7,
        height: 5,
        rx: 1
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
        x: 2,
        y: 12,
        width: 7,
        height: 5,
        rx: 1
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
        d: "M13,12h1a3,3,0,0,1-3,3v2a5,5,0,0,0,5-5h1L15,9Z"
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
        d: "M4,8H3l2,3L7,8H6A3,3,0,0,1,9,5V3A5,5,0,0,0,4,8Z"
      }));
      var controls = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockAlignmentToolbar"], {
        value: align,
        onChange: this.updateAlignment
      }), url && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        className: classnames_default()('components-icon-button components-toolbar__control', {
          'is-active': this.state.isEditing
        }),
        label: Object(external_this_wp_i18n_["__"])('Edit image'),
        "aria-pressed": this.state.isEditing,
        onClick: this.toggleIsEditing,
        icon: editImageIcon
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(edit_ImageURLInputUI, {
        url: href || '',
        onChangeUrl: this.onSetHref,
        mediaLinks: this.getLinkDestinations(),
        linkDestination: linkDestination,
        advancedOptions: Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
          label: Object(external_this_wp_i18n_["__"])('Open in New Tab'),
          onChange: this.onSetNewTab,
          checked: linkTarget === '_blank'
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          label: Object(external_this_wp_i18n_["__"])('Link CSS Class'),
          value: linkClass || '',
          onKeyPress: stopPropagation,
          onKeyDown: edit_stopPropagationRelevantKeys,
          onChange: this.onSetLinkClass
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          label: Object(external_this_wp_i18n_["__"])('Link Rel'),
          value: rel || '',
          onChange: this.onSetLinkRel,
          onKeyPress: stopPropagation,
          onKeyDown: edit_stopPropagationRelevantKeys
        }))
      }))));
      var src = isExternal ? url : undefined;
      var labels = {
        title: !url ? Object(external_this_wp_i18n_["__"])('Image') : Object(external_this_wp_i18n_["__"])('Edit image'),
        instructions: Object(external_this_wp_i18n_["__"])('Upload an image file, pick one from your media library, or add one with a URL.')
      };
      var mediaPreview = !!url && Object(external_this_wp_element_["createElement"])("img", {
        alt: Object(external_this_wp_i18n_["__"])('Edit image'),
        title: Object(external_this_wp_i18n_["__"])('Edit image'),
        className: 'edit-image-preview',
        src: url
      });
      var mediaPlaceholder = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaPlaceholder"], {
        icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
          icon: image_icon
        }),
        className: className,
        labels: labels,
        onSelect: this.onSelectImage,
        onSelectURL: this.onSelectURL,
        onDoubleClick: this.toggleIsEditing,
        onCancel: !!url && this.toggleIsEditing,
        notices: noticeUI,
        onError: this.onUploadError,
        accept: "image/*",
        allowedTypes: ALLOWED_MEDIA_TYPES,
        value: {
          id: id,
          src: src
        },
        mediaPreview: mediaPreview,
        dropZoneUIOnly: !isEditing && url
      });

      if (isEditing || !url) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, controls, mediaPlaceholder);
      }

      var classes = classnames_default()(className, Object(defineProperty["a" /* default */])({
        'is-transient': Object(external_this_wp_blob_["isBlobURL"])(url),
        'is-resized': !!width || !!height,
        'is-focused': isSelected
      }, "size-".concat(sizeSlug), sizeSlug));
      var isResizable = ['wide', 'full'].indexOf(align) === -1 && isLargeViewport;
      var imageSizeOptions = this.getImageSizeOptions();

      var getInspectorControls = function getInspectorControls(imageWidth, imageHeight) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
          title: Object(external_this_wp_i18n_["__"])('Image Settings')
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextareaControl"], {
          label: Object(external_this_wp_i18n_["__"])('Alt Text (Alternative Text)'),
          value: alt,
          onChange: _this4.updateAlt,
          help: Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
            href: "https://www.w3.org/WAI/tutorials/images/decision-tree"
          }, Object(external_this_wp_i18n_["__"])('Describe the purpose of the image')), Object(external_this_wp_i18n_["__"])('Leave empty if the image is purely decorative.'))
        }), !Object(external_lodash_["isEmpty"])(imageSizeOptions) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
          label: Object(external_this_wp_i18n_["__"])('Image Size'),
          value: sizeSlug,
          options: imageSizeOptions,
          onChange: _this4.updateImage
        }), isResizable && Object(external_this_wp_element_["createElement"])("div", {
          className: "block-library-image__dimensions"
        }, Object(external_this_wp_element_["createElement"])("p", {
          className: "block-library-image__dimensions__row"
        }, Object(external_this_wp_i18n_["__"])('Image Dimensions')), Object(external_this_wp_element_["createElement"])("div", {
          className: "block-library-image__dimensions__row"
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          type: "number",
          className: "block-library-image__dimensions__width",
          label: Object(external_this_wp_i18n_["__"])('Width'),
          value: width || imageWidth || '',
          min: 1,
          onChange: _this4.updateWidth
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          type: "number",
          className: "block-library-image__dimensions__height",
          label: Object(external_this_wp_i18n_["__"])('Height'),
          value: height || imageHeight || '',
          min: 1,
          onChange: _this4.updateHeight
        })), Object(external_this_wp_element_["createElement"])("div", {
          className: "block-library-image__dimensions__row"
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ButtonGroup"], {
          "aria-label": Object(external_this_wp_i18n_["__"])('Image Size')
        }, [25, 50, 75, 100].map(function (scale) {
          var scaledWidth = Math.round(imageWidth * (scale / 100));
          var scaledHeight = Math.round(imageHeight * (scale / 100));
          var isCurrent = width === scaledWidth && height === scaledHeight;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
            key: scale,
            isSmall: true,
            isPrimary: isCurrent,
            "aria-pressed": isCurrent,
            onClick: _this4.updateDimensions(scaledWidth, scaledHeight)
          }, scale, "%");
        })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
          isSmall: true,
          onClick: _this4.updateDimensions()
        }, Object(external_this_wp_i18n_["__"])('Reset'))))));
      }; // Disable reason: Each block can be selected by clicking on it

      /* eslint-disable jsx-a11y/click-events-have-key-events */


      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, controls, Object(external_this_wp_element_["createElement"])("figure", {
        className: classes
      }, Object(external_this_wp_element_["createElement"])(image_size, {
        src: url,
        dirtynessTrigger: align
      }, function (sizes) {
        var imageWidthWithinContainer = sizes.imageWidthWithinContainer,
            imageHeightWithinContainer = sizes.imageHeightWithinContainer,
            imageWidth = sizes.imageWidth,
            imageHeight = sizes.imageHeight;

        var filename = _this4.getFilename(url);

        var defaultedAlt;

        if (alt) {
          defaultedAlt = alt;
        } else if (filename) {
          defaultedAlt = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('This image has an empty alt attribute; its file name is %s'), filename);
        } else {
          defaultedAlt = Object(external_this_wp_i18n_["__"])('This image has an empty alt attribute');
        }

        var img = // Disable reason: Image itself is not meant to be interactive, but
        // should direct focus to block.

        /* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
        Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("img", {
          src: url,
          alt: defaultedAlt,
          onDoubleClick: _this4.toggleIsEditing,
          onClick: _this4.onImageClick,
          onError: function onError() {
            return _this4.onImageError(url);
          }
        }), Object(external_this_wp_blob_["isBlobURL"])(url) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null))
        /* eslint-enable jsx-a11y/no-noninteractive-element-interactions */
        ;

        if (!isResizable || !imageWidthWithinContainer) {
          return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, getInspectorControls(imageWidth, imageHeight), Object(external_this_wp_element_["createElement"])("div", {
            style: {
              width: width,
              height: height
            }
          }, img));
        }

        var currentWidth = width || imageWidthWithinContainer;
        var currentHeight = height || imageHeightWithinContainer;
        var ratio = imageWidth / imageHeight;
        var minWidth = imageWidth < imageHeight ? MIN_SIZE : MIN_SIZE * ratio;
        var minHeight = imageHeight < imageWidth ? MIN_SIZE : MIN_SIZE / ratio; // With the current implementation of ResizableBox, an image needs an explicit pixel value for the max-width.
        // In absence of being able to set the content-width, this max-width is currently dictated by the vanilla editor style.
        // The following variable adds a buffer to this vanilla style, so 3rd party themes have some wiggleroom.
        // This does, in most cases, allow you to scale the image beyond the width of the main column, though not infinitely.
        // @todo It would be good to revisit this once a content-width variable becomes available.

        var maxWidthBuffer = maxWidth * 2.5;
        var showRightHandle = false;
        var showLeftHandle = false;
        /* eslint-disable no-lonely-if */
        // See https://github.com/WordPress/gutenberg/issues/7584.

        if (align === 'center') {
          // When the image is centered, show both handles.
          showRightHandle = true;
          showLeftHandle = true;
        } else if (isRTL) {
          // In RTL mode the image is on the right by default.
          // Show the right handle and hide the left handle only when it is aligned left.
          // Otherwise always show the left handle.
          if (align === 'left') {
            showRightHandle = true;
          } else {
            showLeftHandle = true;
          }
        } else {
          // Show the left handle and hide the right handle only when the image is aligned right.
          // Otherwise always show the right handle.
          if (align === 'right') {
            showLeftHandle = true;
          } else {
            showRightHandle = true;
          }
        }
        /* eslint-enable no-lonely-if */


        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, getInspectorControls(imageWidth, imageHeight), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ResizableBox"], {
          size: {
            width: width,
            height: height
          },
          minWidth: minWidth,
          maxWidth: maxWidthBuffer,
          minHeight: minHeight,
          maxHeight: maxWidthBuffer / ratio,
          lockAspectRatio: true,
          enable: {
            top: false,
            right: showRightHandle,
            bottom: true,
            left: showLeftHandle
          },
          onResizeStart: onResizeStart,
          onResizeStop: function onResizeStop(event, direction, elt, delta) {
            _onResizeStop();

            setAttributes({
              width: parseInt(currentWidth + delta.width, 10),
              height: parseInt(currentHeight + delta.height, 10)
            });
          }
        }, img));
      }), (!external_this_wp_blockEditor_["RichText"].isEmpty(caption) || isSelected) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        tagName: "figcaption",
        placeholder: Object(external_this_wp_i18n_["__"])('Write caption…'),
        value: caption,
        unstableOnFocus: this.onFocusCaption,
        onChange: function onChange(value) {
          return setAttributes({
            caption: value
          });
        },
        isSelected: this.state.captionFocused,
        inlineToolbar: true
      })), mediaPlaceholder);
      /* eslint-enable jsx-a11y/click-events-have-key-events */
    }
  }]);

  return ImageEdit;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var image_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/block-editor'),
      toggleSelection = _dispatch.toggleSelection;

  return {
    onResizeStart: function onResizeStart() {
      return toggleSelection(false);
    },
    onResizeStop: function onResizeStop() {
      return toggleSelection(true);
    }
  };
}), Object(external_this_wp_data_["withSelect"])(function (select, props) {
  var _select = select('core'),
      getMedia = _select.getMedia;

  var _select2 = select('core/block-editor'),
      getSettings = _select2.getSettings;

  var id = props.attributes.id,
      isSelected = props.isSelected;

  var _getSettings = getSettings(),
      __experimentalMediaUpload = _getSettings.__experimentalMediaUpload,
      imageSizes = _getSettings.imageSizes,
      isRTL = _getSettings.isRTL,
      maxWidth = _getSettings.maxWidth;

  return {
    image: id && isSelected ? getMedia(id) : null,
    maxWidth: maxWidth,
    isRTL: isRTL,
    imageSizes: imageSizes,
    mediaUpload: __experimentalMediaUpload
  };
}), Object(external_this_wp_viewport_["withViewportMatch"])({
  isLargeViewport: 'medium'
}), external_this_wp_components_["withNotices"]])(edit_ImageEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function image_save_save(_ref) {
  var _classnames;

  var attributes = _ref.attributes;
  var url = attributes.url,
      alt = attributes.alt,
      caption = attributes.caption,
      align = attributes.align,
      href = attributes.href,
      rel = attributes.rel,
      linkClass = attributes.linkClass,
      width = attributes.width,
      height = attributes.height,
      id = attributes.id,
      linkTarget = attributes.linkTarget,
      sizeSlug = attributes.sizeSlug;
  var classes = classnames_default()((_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, "align".concat(align), align), Object(defineProperty["a" /* default */])(_classnames, "size-".concat(sizeSlug), sizeSlug), Object(defineProperty["a" /* default */])(_classnames, 'is-resized', width || height), _classnames));
  var image = Object(external_this_wp_element_["createElement"])("img", {
    src: url,
    alt: alt,
    className: id ? "wp-image-".concat(id) : null,
    width: width,
    height: height
  });
  var figure = Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, href ? Object(external_this_wp_element_["createElement"])("a", {
    className: linkClass,
    href: href,
    target: linkTarget,
    rel: rel
  }, image) : image, !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "figcaption",
    value: caption
  }));

  if ('left' === align || 'right' === align || 'center' === align) {
    return Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])("figure", {
      className: classes
    }, figure));
  }

  return Object(external_this_wp_element_["createElement"])("figure", {
    className: classes
  }, figure);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/transforms.js


/**
 * WordPress dependencies
 */


function stripFirstImage(attributes, _ref) {
  var shortcode = _ref.shortcode;

  var _document$implementat = document.implementation.createHTMLDocument(''),
      body = _document$implementat.body;

  body.innerHTML = shortcode.content;
  var nodeToRemove = body.querySelector('img'); // if an image has parents, find the topmost node to remove

  while (nodeToRemove && nodeToRemove.parentNode && nodeToRemove.parentNode !== body) {
    nodeToRemove = nodeToRemove.parentNode;
  }

  if (nodeToRemove) {
    nodeToRemove.parentNode.removeChild(nodeToRemove);
  }

  return body.innerHTML.trim();
}

function getFirstAnchorAttributeFormHTML(html, attributeName) {
  var _document$implementat2 = document.implementation.createHTMLDocument(''),
      body = _document$implementat2.body;

  body.innerHTML = html;
  var firstElementChild = body.firstElementChild;

  if (firstElementChild && firstElementChild.nodeName === 'A') {
    return firstElementChild.getAttribute(attributeName) || undefined;
  }
}

var imageSchema = {
  img: {
    attributes: ['src', 'alt'],
    classes: ['alignleft', 'aligncenter', 'alignright', 'alignnone', /^wp-image-\d+$/]
  }
};
var schema = {
  figure: {
    require: ['img'],
    children: Object(objectSpread["a" /* default */])({}, imageSchema, {
      a: {
        attributes: ['href', 'rel', 'target'],
        children: imageSchema
      },
      figcaption: {
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      }
    })
  }
};
var image_transforms_transforms = {
  from: [{
    type: 'raw',
    isMatch: function isMatch(node) {
      return node.nodeName === 'FIGURE' && !!node.querySelector('img');
    },
    schema: schema,
    transform: function transform(node) {
      // Search both figure and image classes. Alignment could be
      // set on either. ID is set on the image.
      var className = node.className + ' ' + node.querySelector('img').className;
      var alignMatches = /(?:^|\s)align(left|center|right)(?:$|\s)/.exec(className);
      var align = alignMatches ? alignMatches[1] : undefined;
      var idMatches = /(?:^|\s)wp-image-(\d+)(?:$|\s)/.exec(className);
      var id = idMatches ? Number(idMatches[1]) : undefined;
      var anchorElement = node.querySelector('a');
      var linkDestination = anchorElement && anchorElement.href ? 'custom' : undefined;
      var href = anchorElement && anchorElement.href ? anchorElement.href : undefined;
      var rel = anchorElement && anchorElement.rel ? anchorElement.rel : undefined;
      var linkClass = anchorElement && anchorElement.className ? anchorElement.className : undefined;
      var attributes = Object(external_this_wp_blocks_["getBlockAttributes"])('core/image', node.outerHTML, {
        align: align,
        id: id,
        linkDestination: linkDestination,
        href: href,
        rel: rel,
        linkClass: linkClass
      });
      return Object(external_this_wp_blocks_["createBlock"])('core/image', attributes);
    }
  }, {
    type: 'files',
    isMatch: function isMatch(files) {
      return files.length === 1 && files[0].type.indexOf('image/') === 0;
    },
    transform: function transform(files) {
      var file = files[0]; // We don't need to upload the media directly here
      // It's already done as part of the `componentDidMount`
      // int the image block

      return Object(external_this_wp_blocks_["createBlock"])('core/image', {
        url: Object(external_this_wp_blob_["createBlobURL"])(file)
      });
    }
  }, {
    type: 'shortcode',
    tag: 'caption',
    attributes: {
      url: {
        type: 'string',
        source: 'attribute',
        attribute: 'src',
        selector: 'img'
      },
      alt: {
        type: 'string',
        source: 'attribute',
        attribute: 'alt',
        selector: 'img'
      },
      caption: {
        shortcode: stripFirstImage
      },
      href: {
        shortcode: function shortcode(attributes, _ref2) {
          var _shortcode = _ref2.shortcode;
          return getFirstAnchorAttributeFormHTML(_shortcode.content, 'href');
        }
      },
      rel: {
        shortcode: function shortcode(attributes, _ref3) {
          var _shortcode2 = _ref3.shortcode;
          return getFirstAnchorAttributeFormHTML(_shortcode2.content, 'rel');
        }
      },
      linkClass: {
        shortcode: function shortcode(attributes, _ref4) {
          var _shortcode3 = _ref4.shortcode;
          return getFirstAnchorAttributeFormHTML(_shortcode3.content, 'class');
        }
      },
      id: {
        type: 'number',
        shortcode: function shortcode(_ref5) {
          var id = _ref5.named.id;

          if (!id) {
            return;
          }

          return parseInt(id.replace('attachment_', ''), 10);
        }
      },
      align: {
        type: 'string',
        shortcode: function shortcode(_ref6) {
          var _ref6$named$align = _ref6.named.align,
              align = _ref6$named$align === void 0 ? 'alignnone' : _ref6$named$align;
          return align.replace('align', '');
        }
      }
    }
  }]
};
/* harmony default export */ var image_transforms = (image_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var image_metadata = {
  name: "core/image",
  category: "common",
  attributes: {
    align: {
      type: "string"
    },
    url: {
      type: "string",
      source: "attribute",
      selector: "img",
      attribute: "src"
    },
    alt: {
      type: "string",
      source: "attribute",
      selector: "img",
      attribute: "alt",
      "default": ""
    },
    caption: {
      type: "string",
      source: "html",
      selector: "figcaption"
    },
    href: {
      type: "string",
      source: "attribute",
      selector: "figure > a",
      attribute: "href"
    },
    rel: {
      type: "string",
      source: "attribute",
      selector: "figure > a",
      attribute: "rel"
    },
    linkClass: {
      type: "string",
      source: "attribute",
      selector: "figure > a",
      attribute: "class"
    },
    id: {
      type: "number"
    },
    width: {
      type: "number"
    },
    height: {
      type: "number"
    },
    sizeSlug: {
      type: "string"
    },
    linkDestination: {
      type: "string",
      "default": "none"
    },
    linkTarget: {
      type: "string",
      source: "attribute",
      selector: "figure > a",
      attribute: "target"
    }
  }
};


var image_name = image_metadata.name;

var image_settings = {
  title: Object(external_this_wp_i18n_["__"])('Image'),
  description: Object(external_this_wp_i18n_["__"])('Insert an image to make a visual statement.'),
  icon: image_icon,
  keywords: ['img', // "img" is not translated as it is intended to reflect the HTML <img> tag.
  Object(external_this_wp_i18n_["__"])('photo')],
  example: {
    attributes: {
      sizeSlug: 'large',
      url: 'https://s.w.org/images/core/5.3/MtBlanc1.jpg',
      caption: Object(external_this_wp_i18n_["__"])('Mont Blanc appears—still, snowy, and serene.')
    }
  },
  styles: [{
    name: 'default',
    label: Object(external_this_wp_i18n_["_x"])('Default', 'block style'),
    isDefault: true
  }, {
    name: 'circle-mask',
    label: Object(external_this_wp_i18n_["_x"])('Circle Mask', 'block style')
  }],
  transforms: image_transforms,
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var align = attributes.align,
        width = attributes.width;

    if ('left' === align || 'center' === align || 'right' === align || 'wide' === align || 'full' === align) {
      return {
        'data-align': align,
        'data-resized': !!width
      };
    }
  },
  edit: image_edit,
  save: image_save_save,
  deprecated: image_deprecated
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/deprecated.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


var blockSupports = {
  className: false,
  anchor: true
};
var heading_deprecated_blockAttributes = {
  align: {
    type: 'string'
  },
  content: {
    type: 'string',
    source: 'html',
    selector: 'h1,h2,h3,h4,h5,h6',
    default: ''
  },
  level: {
    type: 'number',
    default: 2
  },
  placeholder: {
    type: 'string'
  },
  textColor: {
    type: 'string'
  },
  customTextColor: {
    type: 'string'
  }
};
var heading_deprecated_deprecated = [{
  attributes: heading_deprecated_blockAttributes,
  save: function save(_ref) {
    var _classnames;

    var attributes = _ref.attributes;
    var align = attributes.align,
        content = attributes.content,
        customTextColor = attributes.customTextColor,
        level = attributes.level,
        textColor = attributes.textColor;
    var tagName = 'h' + level;
    var textClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
    var className = classnames_default()((_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames, "has-text-align-".concat(align), align), _classnames));
    return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      className: className ? className : undefined,
      tagName: tagName,
      style: {
        color: textClass ? undefined : customTextColor
      },
      value: content
    });
  },
  supports: blockSupports
}, {
  supports: blockSupports,
  attributes: heading_deprecated_blockAttributes,
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    var align = attributes.align,
        level = attributes.level,
        content = attributes.content,
        textColor = attributes.textColor,
        customTextColor = attributes.customTextColor;
    var tagName = 'h' + level;
    var textClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
    var className = classnames_default()(Object(defineProperty["a" /* default */])({}, textClass, textClass));
    return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      className: className ? className : undefined,
      tagName: tagName,
      style: {
        textAlign: align,
        color: textClass ? undefined : customTextColor
      },
      value: content
    });
  }
}];
/* harmony default export */ var heading_deprecated = (heading_deprecated_deprecated);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/heading-level-icon.js


/**
 * WordPress dependencies
 */

function HeadingLevelIcon(_ref) {
  var level = _ref.level;
  var levelToPath = {
    1: 'M9 5h2v10H9v-4H5v4H3V5h2v4h4V5zm6.6 0c-.6.9-1.5 1.7-2.6 2v1h2v7h2V5h-1.4z',
    2: 'M7 5h2v10H7v-4H3v4H1V5h2v4h4V5zm8 8c.5-.4.6-.6 1.1-1.1.4-.4.8-.8 1.2-1.3.3-.4.6-.8.9-1.3.2-.4.3-.8.3-1.3 0-.4-.1-.9-.3-1.3-.2-.4-.4-.7-.8-1-.3-.3-.7-.5-1.2-.6-.5-.2-1-.2-1.5-.2-.4 0-.7 0-1.1.1-.3.1-.7.2-1 .3-.3.1-.6.3-.9.5-.3.2-.6.4-.8.7l1.2 1.2c.3-.3.6-.5 1-.7.4-.2.7-.3 1.2-.3s.9.1 1.3.4c.3.3.5.7.5 1.1 0 .4-.1.8-.4 1.1-.3.5-.6.9-1 1.2-.4.4-1 .9-1.6 1.4-.6.5-1.4 1.1-2.2 1.6V15h8v-2H15z',
    3: 'M12.1 12.2c.4.3.8.5 1.2.7.4.2.9.3 1.4.3.5 0 1-.1 1.4-.3.3-.1.5-.5.5-.8 0-.2 0-.4-.1-.6-.1-.2-.3-.3-.5-.4-.3-.1-.7-.2-1-.3-.5-.1-1-.1-1.5-.1V9.1c.7.1 1.5-.1 2.2-.4.4-.2.6-.5.6-.9 0-.3-.1-.6-.4-.8-.3-.2-.7-.3-1.1-.3-.4 0-.8.1-1.1.3-.4.2-.7.4-1.1.6l-1.2-1.4c.5-.4 1.1-.7 1.6-.9.5-.2 1.2-.3 1.8-.3.5 0 1 .1 1.6.2.4.1.8.3 1.2.5.3.2.6.5.8.8.2.3.3.7.3 1.1 0 .5-.2.9-.5 1.3-.4.4-.9.7-1.5.9v.1c.6.1 1.2.4 1.6.8.4.4.7.9.7 1.5 0 .4-.1.8-.3 1.2-.2.4-.5.7-.9.9-.4.3-.9.4-1.3.5-.5.1-1 .2-1.6.2-.8 0-1.6-.1-2.3-.4-.6-.2-1.1-.6-1.6-1l1.1-1.4zM7 9H3V5H1v10h2v-4h4v4h2V5H7v4z',
    4: 'M9 15H7v-4H3v4H1V5h2v4h4V5h2v10zm10-2h-1v2h-2v-2h-5v-2l4-6h3v6h1v2zm-3-2V7l-2.8 4H16z',
    5: 'M12.1 12.2c.4.3.7.5 1.1.7.4.2.9.3 1.3.3.5 0 1-.1 1.4-.4.4-.3.6-.7.6-1.1 0-.4-.2-.9-.6-1.1-.4-.3-.9-.4-1.4-.4H14c-.1 0-.3 0-.4.1l-.4.1-.5.2-1-.6.3-5h6.4v1.9h-4.3L14 8.8c.2-.1.5-.1.7-.2.2 0 .5-.1.7-.1.5 0 .9.1 1.4.2.4.1.8.3 1.1.6.3.2.6.6.8.9.2.4.3.9.3 1.4 0 .5-.1 1-.3 1.4-.2.4-.5.8-.9 1.1-.4.3-.8.5-1.3.7-.5.2-1 .3-1.5.3-.8 0-1.6-.1-2.3-.4-.6-.2-1.1-.6-1.6-1-.1-.1 1-1.5 1-1.5zM9 15H7v-4H3v4H1V5h2v4h4V5h2v10z',
    6: 'M9 15H7v-4H3v4H1V5h2v4h4V5h2v10zm8.6-7.5c-.2-.2-.5-.4-.8-.5-.6-.2-1.3-.2-1.9 0-.3.1-.6.3-.8.5l-.6.9c-.2.5-.2.9-.2 1.4.4-.3.8-.6 1.2-.8.4-.2.8-.3 1.3-.3.4 0 .8 0 1.2.2.4.1.7.3 1 .6.3.3.5.6.7.9.2.4.3.8.3 1.3s-.1.9-.3 1.4c-.2.4-.5.7-.8 1-.4.3-.8.5-1.2.6-1 .3-2 .3-3 0-.5-.2-1-.5-1.4-.9-.4-.4-.8-.9-1-1.5-.2-.6-.3-1.3-.3-2.1s.1-1.6.4-2.3c.2-.6.6-1.2 1-1.6.4-.4.9-.7 1.4-.9.6-.3 1.1-.4 1.7-.4.7 0 1.4.1 2 .3.5.2 1 .5 1.4.8 0 .1-1.3 1.4-1.3 1.4zm-2.4 5.8c.2 0 .4 0 .6-.1.2 0 .4-.1.5-.2.1-.1.3-.3.4-.5.1-.2.1-.5.1-.7 0-.4-.1-.8-.4-1.1-.3-.2-.7-.3-1.1-.3-.3 0-.7.1-1 .2-.4.2-.7.4-1 .7 0 .3.1.7.3 1 .1.2.3.4.4.6.2.1.3.3.5.3.2.1.5.2.7.1z'
  };

  if (!levelToPath.hasOwnProperty(level)) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    width: "20",
    height: "20",
    viewBox: "0 0 20 20",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: levelToPath[level]
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/heading-toolbar.js







/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var heading_toolbar_HeadingToolbar =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(HeadingToolbar, _Component);

  function HeadingToolbar() {
    Object(classCallCheck["a" /* default */])(this, HeadingToolbar);

    return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(HeadingToolbar).apply(this, arguments));
  }

  Object(createClass["a" /* default */])(HeadingToolbar, [{
    key: "createLevelControl",
    value: function createLevelControl(targetLevel, selectedLevel, onChange) {
      return {
        icon: Object(external_this_wp_element_["createElement"])(HeadingLevelIcon, {
          level: targetLevel
        }),
        // translators: %s: heading level e.g: "1", "2", "3"
        title: Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Heading %d'), targetLevel),
        isActive: targetLevel === selectedLevel,
        onClick: function onClick() {
          return onChange(targetLevel);
        }
      };
    }
  }, {
    key: "render",
    value: function render() {
      var _this = this;

      var _this$props = this.props,
          _this$props$isCollaps = _this$props.isCollapsed,
          isCollapsed = _this$props$isCollaps === void 0 ? true : _this$props$isCollaps,
          minLevel = _this$props.minLevel,
          maxLevel = _this$props.maxLevel,
          selectedLevel = _this$props.selectedLevel,
          onChange = _this$props.onChange;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
        isCollapsed: isCollapsed,
        icon: Object(external_this_wp_element_["createElement"])(HeadingLevelIcon, {
          level: selectedLevel
        }),
        controls: Object(external_lodash_["range"])(minLevel, maxLevel).map(function (index) {
          return _this.createLevelControl(index, selectedLevel, onChange);
        })
      });
    }
  }]);

  return HeadingToolbar;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var heading_toolbar = (heading_toolbar_HeadingToolbar);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/edit.js




/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */







var HeadingColorUI = Object(external_this_wp_element_["memo"])(function (_ref) {
  var textColorValue = _ref.textColorValue,
      setTextColor = _ref.setTextColor;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
    title: Object(external_this_wp_i18n_["__"])('Color Settings'),
    initialOpen: false,
    colorSettings: [{
      value: textColorValue,
      onChange: setTextColor,
      label: Object(external_this_wp_i18n_["__"])('Text Color')
    }]
  });
});

function HeadingEdit(_ref2) {
  var _classnames;

  var attributes = _ref2.attributes,
      setAttributes = _ref2.setAttributes,
      mergeBlocks = _ref2.mergeBlocks,
      onReplace = _ref2.onReplace,
      className = _ref2.className,
      textColor = _ref2.textColor,
      setTextColor = _ref2.setTextColor;
  var align = attributes.align,
      content = attributes.content,
      level = attributes.level,
      placeholder = attributes.placeholder;
  var tagName = 'h' + level;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(heading_toolbar, {
    minLevel: 2,
    maxLevel: 5,
    selectedLevel: level,
    onChange: function onChange(newLevel) {
      return setAttributes({
        level: newLevel
      });
    }
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["AlignmentToolbar"], {
    value: align,
    onChange: function onChange(nextAlign) {
      setAttributes({
        align: nextAlign
      });
    }
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Heading Settings')
  }, Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('Level')), Object(external_this_wp_element_["createElement"])(heading_toolbar, {
    isCollapsed: false,
    minLevel: 1,
    maxLevel: 7,
    selectedLevel: level,
    onChange: function onChange(newLevel) {
      return setAttributes({
        level: newLevel
      });
    }
  })), Object(external_this_wp_element_["createElement"])(HeadingColorUI, {
    setTextColor: setTextColor,
    textColorValue: textColor.color
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
    identifier: "content",
    wrapperClassName: "wp-block-heading",
    tagName: tagName,
    value: content,
    onChange: function onChange(value) {
      return setAttributes({
        content: value
      });
    },
    onMerge: mergeBlocks,
    onSplit: function onSplit(value) {
      if (!value) {
        return Object(external_this_wp_blocks_["createBlock"])('core/paragraph');
      }

      return Object(external_this_wp_blocks_["createBlock"])('core/heading', Object(objectSpread["a" /* default */])({}, attributes, {
        content: value
      }));
    },
    onReplace: onReplace,
    onRemove: function onRemove() {
      return onReplace([]);
    },
    className: classnames_default()(className, (_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, "has-text-align-".concat(align), align), Object(defineProperty["a" /* default */])(_classnames, 'has-text-color', textColor.color), Object(defineProperty["a" /* default */])(_classnames, textColor.class, textColor.class), _classnames)),
    placeholder: placeholder || Object(external_this_wp_i18n_["__"])('Write heading…'),
    style: {
      color: textColor.color
    }
  }));
}

/* harmony default export */ var heading_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_blockEditor_["withColors"])('backgroundColor', {
  textColor: 'color'
})])(HeadingEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function heading_save_save(_ref) {
  var _classnames;

  var attributes = _ref.attributes;
  var align = attributes.align,
      content = attributes.content,
      customTextColor = attributes.customTextColor,
      level = attributes.level,
      textColor = attributes.textColor;
  var tagName = 'h' + level;
  var textClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
  var className = classnames_default()((_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames, 'has-text-color', textColor || customTextColor), Object(defineProperty["a" /* default */])(_classnames, "has-text-align-".concat(align), align), _classnames));
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    className: className ? className : undefined,
    tagName: tagName,
    style: {
      color: textClass ? undefined : customTextColor
    },
    value: content
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/shared.js
/**
 * Given a node name string for a heading node, returns its numeric level.
 *
 * @param {string} nodeName Heading node name.
 *
 * @return {number} Heading level.
 */
function getLevelFromHeadingNodeName(nodeName) {
  return Number(nodeName.substr(1));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/transforms.js



/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var heading_transforms_transforms = {
  from: [{
    type: 'block',
    blocks: ['core/paragraph'],
    transform: function transform(_ref) {
      var content = _ref.content;
      return Object(external_this_wp_blocks_["createBlock"])('core/heading', {
        content: content
      });
    }
  }, {
    type: 'raw',
    selector: 'h1,h2,h3,h4,h5,h6',
    schema: {
      h1: {
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      },
      h2: {
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      },
      h3: {
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      },
      h4: {
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      },
      h5: {
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      },
      h6: {
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      }
    },
    transform: function transform(node) {
      return Object(external_this_wp_blocks_["createBlock"])('core/heading', Object(objectSpread["a" /* default */])({}, Object(external_this_wp_blocks_["getBlockAttributes"])('core/heading', node.outerHTML), {
        level: getLevelFromHeadingNodeName(node.nodeName)
      }));
    }
  }].concat(Object(toConsumableArray["a" /* default */])([2, 3, 4, 5, 6].map(function (level) {
    return {
      type: 'prefix',
      prefix: Array(level + 1).join('#'),
      transform: function transform(content) {
        return Object(external_this_wp_blocks_["createBlock"])('core/heading', {
          level: level,
          content: content
        });
      }
    };
  }))),
  to: [{
    type: 'block',
    blocks: ['core/paragraph'],
    transform: function transform(_ref2) {
      var content = _ref2.content;
      return Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
        content: content
      });
    }
  }]
};
/* harmony default export */ var heading_transforms = (heading_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var heading_metadata = {
  name: "core/heading",
  category: "common",
  attributes: {
    align: {
      type: "string"
    },
    content: {
      type: "string",
      source: "html",
      selector: "h1,h2,h3,h4,h5,h6",
      "default": ""
    },
    level: {
      type: "number",
      "default": 2
    },
    placeholder: {
      type: "string"
    },
    textColor: {
      type: "string"
    },
    customTextColor: {
      type: "string"
    }
  }
};


var heading_name = heading_metadata.name;

var heading_settings = {
  title: Object(external_this_wp_i18n_["__"])('Heading'),
  description: Object(external_this_wp_i18n_["__"])('Introduce new sections and organize content to help visitors (and search engines) understand the structure of your content.'),
  icon: 'heading',
  keywords: [Object(external_this_wp_i18n_["__"])('title'), Object(external_this_wp_i18n_["__"])('subtitle')],
  supports: {
    className: false,
    anchor: true
  },
  example: {
    attributes: {
      content: Object(external_this_wp_i18n_["__"])('Code is Poetry'),
      level: 2
    }
  },
  transforms: heading_transforms,
  deprecated: heading_deprecated,
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: (attributes.content || '') + (attributesToMerge.content || '')
    };
  },
  edit: heading_edit,
  save: heading_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/quote/deprecated.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


var quote_deprecated_blockAttributes = {
  value: {
    type: 'string',
    source: 'html',
    selector: 'blockquote',
    multiline: 'p',
    default: ''
  },
  citation: {
    type: 'string',
    source: 'html',
    selector: 'cite',
    default: ''
  },
  align: {
    type: 'string'
  }
};
var quote_deprecated_deprecated = [{
  attributes: quote_deprecated_blockAttributes,
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var align = attributes.align,
        value = attributes.value,
        citation = attributes.citation;
    return Object(external_this_wp_element_["createElement"])("blockquote", {
      style: {
        textAlign: align ? align : null
      }
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      multiline: true,
      value: value
    }), !external_this_wp_blockEditor_["RichText"].isEmpty(citation) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "cite",
      value: citation
    }));
  }
}, {
  attributes: Object(objectSpread["a" /* default */])({}, quote_deprecated_blockAttributes, {
    style: {
      type: 'number',
      default: 1
    }
  }),
  migrate: function migrate(attributes) {
    if (attributes.style === 2) {
      return Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(attributes, ['style']), {
        className: attributes.className ? attributes.className + ' is-style-large' : 'is-style-large'
      });
    }

    return attributes;
  },
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    var align = attributes.align,
        value = attributes.value,
        citation = attributes.citation,
        style = attributes.style;
    return Object(external_this_wp_element_["createElement"])("blockquote", {
      className: style === 2 ? 'is-large' : '',
      style: {
        textAlign: align ? align : null
      }
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      multiline: true,
      value: value
    }), !external_this_wp_blockEditor_["RichText"].isEmpty(citation) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "cite",
      value: citation
    }));
  }
}, {
  attributes: Object(objectSpread["a" /* default */])({}, quote_deprecated_blockAttributes, {
    citation: {
      type: 'string',
      source: 'html',
      selector: 'footer',
      default: ''
    },
    style: {
      type: 'number',
      default: 1
    }
  }),
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    var align = attributes.align,
        value = attributes.value,
        citation = attributes.citation,
        style = attributes.style;
    return Object(external_this_wp_element_["createElement"])("blockquote", {
      className: "blocks-quote-style-".concat(style),
      style: {
        textAlign: align ? align : null
      }
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      multiline: true,
      value: value
    }), !external_this_wp_blockEditor_["RichText"].isEmpty(citation) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "footer",
      value: citation
    }));
  }
}];
/* harmony default export */ var quote_deprecated = (quote_deprecated_deprecated);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/quote/edit.js




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





function QuoteEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      isSelected = _ref.isSelected,
      mergeBlocks = _ref.mergeBlocks,
      onReplace = _ref.onReplace,
      className = _ref.className;
  var align = attributes.align,
      value = attributes.value,
      citation = attributes.citation;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["AlignmentToolbar"], {
    value: align,
    onChange: function onChange(nextAlign) {
      setAttributes({
        align: nextAlign
      });
    }
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["BlockQuotation"], {
    className: classnames_default()(className, Object(defineProperty["a" /* default */])({}, "has-text-align-".concat(align), align))
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
    identifier: "value",
    multiline: true,
    value: value,
    onChange: function onChange(nextValue) {
      return setAttributes({
        value: nextValue
      });
    },
    onMerge: mergeBlocks,
    onRemove: function onRemove(forward) {
      var hasEmptyCitation = !citation || citation.length === 0;

      if (!forward && hasEmptyCitation) {
        onReplace([]);
      }
    },
    placeholder: // translators: placeholder text used for the quote
    Object(external_this_wp_i18n_["__"])('Write quote…'),
    onReplace: onReplace,
    onSplit: function onSplit(piece) {
      return Object(external_this_wp_blocks_["createBlock"])('core/quote', Object(objectSpread["a" /* default */])({}, attributes, {
        value: piece
      }));
    },
    __unstableOnSplitMiddle: function __unstableOnSplitMiddle() {
      return Object(external_this_wp_blocks_["createBlock"])('core/paragraph');
    }
  }), (!external_this_wp_blockEditor_["RichText"].isEmpty(citation) || isSelected) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
    identifier: "citation",
    value: citation,
    onChange: function onChange(nextCitation) {
      return setAttributes({
        citation: nextCitation
      });
    },
    __unstableMobileNoFocusOnMount: true,
    placeholder: // translators: placeholder text used for the citation
    Object(external_this_wp_i18n_["__"])('Write citation…'),
    className: "wp-block-quote__citation"
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/quote/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var quote_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M18.62 18h-5.24l2-4H13V6h8v7.24L18.62 18zm-2-2h.76L19 12.76V8h-4v4h3.62l-2 4zm-8 2H3.38l2-4H3V6h8v7.24L8.62 18zm-2-2h.76L9 12.76V8H5v4h3.62l-2 4z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/quote/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function quote_save_save(_ref) {
  var attributes = _ref.attributes;
  var align = attributes.align,
      value = attributes.value,
      citation = attributes.citation;
  var className = classnames_default()(Object(defineProperty["a" /* default */])({}, "has-text-align-".concat(align), align));
  return Object(external_this_wp_element_["createElement"])("blockquote", {
    className: className
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    multiline: true,
    value: value
  }), !external_this_wp_blockEditor_["RichText"].isEmpty(citation) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "cite",
    value: citation
  }));
}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__(21);

// EXTERNAL MODULE: external {"this":["wp","richText"]}
var external_this_wp_richText_ = __webpack_require__(22);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/quote/transforms.js




/**
 * WordPress dependencies
 */


var quote_transforms_transforms = {
  from: [{
    type: 'block',
    isMultiBlock: true,
    blocks: ['core/paragraph'],
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/quote', {
        value: Object(external_this_wp_richText_["toHTMLString"])({
          value: Object(external_this_wp_richText_["join"])(attributes.map(function (_ref) {
            var content = _ref.content;
            return Object(external_this_wp_richText_["create"])({
              html: content
            });
          }), "\u2028"),
          multilineTag: 'p'
        })
      });
    }
  }, {
    type: 'block',
    blocks: ['core/heading'],
    transform: function transform(_ref2) {
      var content = _ref2.content;
      return Object(external_this_wp_blocks_["createBlock"])('core/quote', {
        value: "<p>".concat(content, "</p>")
      });
    }
  }, {
    type: 'block',
    blocks: ['core/pullquote'],
    transform: function transform(_ref3) {
      var value = _ref3.value,
          citation = _ref3.citation;
      return Object(external_this_wp_blocks_["createBlock"])('core/quote', {
        value: value,
        citation: citation
      });
    }
  }, {
    type: 'prefix',
    prefix: '>',
    transform: function transform(content) {
      return Object(external_this_wp_blocks_["createBlock"])('core/quote', {
        value: "<p>".concat(content, "</p>")
      });
    }
  }, {
    type: 'raw',
    isMatch: function isMatch(node) {
      var isParagraphOrSingleCite = function () {
        var hasCitation = false;
        return function (child) {
          // Child is a paragraph.
          if (child.nodeName === 'P') {
            return true;
          } // Child is a cite and no other cite child exists before it.


          if (!hasCitation && child.nodeName === 'CITE') {
            hasCitation = true;
            return true;
          }
        };
      }();

      return node.nodeName === 'BLOCKQUOTE' && // The quote block can only handle multiline paragraph
      // content with an optional cite child.
      Array.from(node.childNodes).every(isParagraphOrSingleCite);
    },
    schema: {
      blockquote: {
        children: {
          p: {
            children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
          },
          cite: {
            children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
          }
        }
      }
    }
  }],
  to: [{
    type: 'block',
    blocks: ['core/paragraph'],
    transform: function transform(_ref4) {
      var value = _ref4.value,
          citation = _ref4.citation;
      var paragraphs = [];

      if (value && value !== '<p></p>') {
        paragraphs.push.apply(paragraphs, Object(toConsumableArray["a" /* default */])(Object(external_this_wp_richText_["split"])(Object(external_this_wp_richText_["create"])({
          html: value,
          multilineTag: 'p'
        }), "\u2028").map(function (piece) {
          return Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
            content: Object(external_this_wp_richText_["toHTMLString"])({
              value: piece
            })
          });
        })));
      }

      if (citation && citation !== '<p></p>') {
        paragraphs.push(Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
          content: citation
        }));
      }

      if (paragraphs.length === 0) {
        return Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
          content: ''
        });
      }

      return paragraphs;
    }
  }, {
    type: 'block',
    blocks: ['core/heading'],
    transform: function transform(_ref5) {
      var value = _ref5.value,
          citation = _ref5.citation,
          attrs = Object(objectWithoutProperties["a" /* default */])(_ref5, ["value", "citation"]);

      // If there is no quote content, use the citation as the
      // content of the resulting heading. A nonexistent citation
      // will result in an empty heading.
      if (value === '<p></p>') {
        return Object(external_this_wp_blocks_["createBlock"])('core/heading', {
          content: citation
        });
      }

      var pieces = Object(external_this_wp_richText_["split"])(Object(external_this_wp_richText_["create"])({
        html: value,
        multilineTag: 'p'
      }), "\u2028");
      var headingBlock = Object(external_this_wp_blocks_["createBlock"])('core/heading', {
        content: Object(external_this_wp_richText_["toHTMLString"])({
          value: pieces[0]
        })
      });

      if (!citation && pieces.length === 1) {
        return headingBlock;
      }

      var quotePieces = pieces.slice(1);
      var quoteBlock = Object(external_this_wp_blocks_["createBlock"])('core/quote', Object(objectSpread["a" /* default */])({}, attrs, {
        citation: citation,
        value: Object(external_this_wp_richText_["toHTMLString"])({
          value: quotePieces.length ? Object(external_this_wp_richText_["join"])(pieces.slice(1), "\u2028") : Object(external_this_wp_richText_["create"])(),
          multilineTag: 'p'
        })
      }));
      return [headingBlock, quoteBlock];
    }
  }, {
    type: 'block',
    blocks: ['core/pullquote'],
    transform: function transform(_ref6) {
      var value = _ref6.value,
          citation = _ref6.citation;
      return Object(external_this_wp_blocks_["createBlock"])('core/pullquote', {
        value: value,
        citation: citation
      });
    }
  }]
};
/* harmony default export */ var quote_transforms = (quote_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/quote/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var quote_metadata = {
  name: "core/quote",
  category: "common",
  attributes: {
    value: {
      type: "string",
      source: "html",
      selector: "blockquote",
      multiline: "p",
      "default": ""
    },
    citation: {
      type: "string",
      source: "html",
      selector: "cite",
      "default": ""
    },
    align: {
      type: "string"
    }
  }
};


var quote_name = quote_metadata.name;

var quote_settings = {
  title: Object(external_this_wp_i18n_["__"])('Quote'),
  description: Object(external_this_wp_i18n_["__"])('Give quoted text visual emphasis. "In quoting others, we cite ourselves." — Julio Cortázar'),
  icon: quote_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('blockquote'), Object(external_this_wp_i18n_["__"])('cite')],
  example: {
    attributes: {
      value: '<p>' + Object(external_this_wp_i18n_["__"])('In quoting others, we cite ourselves.') + '</p>',
      citation: 'Julio Cortázar',
      className: 'is-style-large'
    }
  },
  styles: [{
    name: 'default',
    label: Object(external_this_wp_i18n_["_x"])('Default', 'block style'),
    isDefault: true
  }, {
    name: 'large',
    label: Object(external_this_wp_i18n_["_x"])('Large', 'block style')
  }],
  transforms: quote_transforms,
  edit: QuoteEdit,
  save: quote_save_save,
  merge: function merge(attributes, _ref) {
    var value = _ref.value,
        citation = _ref.citation;

    // Quote citations cannot be merged. Pick the second one unless it's
    // empty.
    if (!citation) {
      citation = attributes.citation;
    }

    if (!value || value === '<p></p>') {
      return Object(objectSpread["a" /* default */])({}, attributes, {
        citation: citation
      });
    }

    return Object(objectSpread["a" /* default */])({}, attributes, {
      value: attributes.value + value,
      citation: citation
    });
  },
  deprecated: quote_deprecated
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/shared.js
/**
 * External dependencies
 */

function defaultColumnsNumber(attributes) {
  return Math.min(3, attributes.images.length);
}
var shared_pickRelevantMediaFiles = function pickRelevantMediaFiles(image) {
  var imageProps = Object(external_lodash_["pick"])(image, ['alt', 'id', 'link', 'caption']);
  imageProps.url = Object(external_lodash_["get"])(image, ['sizes', 'large', 'url']) || Object(external_lodash_["get"])(image, ['media_details', 'sizes', 'large', 'source_url']) || image.url;
  var fullUrl = Object(external_lodash_["get"])(image, ['sizes', 'full', 'url']) || Object(external_lodash_["get"])(image, ['media_details', 'sizes', 'full', 'source_url']);

  if (fullUrl) {
    imageProps.fullUrl = fullUrl;
  }

  return imageProps;
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/deprecated.js



/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var gallery_deprecated_deprecated = [{
  attributes: {
    images: {
      type: 'array',
      default: [],
      source: 'query',
      selector: 'ul.wp-block-gallery .blocks-gallery-item',
      query: {
        url: {
          source: 'attribute',
          selector: 'img',
          attribute: 'src'
        },
        fullUrl: {
          source: 'attribute',
          selector: 'img',
          attribute: 'data-full-url'
        },
        alt: {
          source: 'attribute',
          selector: 'img',
          attribute: 'alt',
          default: ''
        },
        id: {
          source: 'attribute',
          selector: 'img',
          attribute: 'data-id'
        },
        link: {
          source: 'attribute',
          selector: 'img',
          attribute: 'data-link'
        },
        caption: {
          type: 'array',
          source: 'children',
          selector: 'figcaption'
        }
      }
    },
    ids: {
      type: 'array',
      default: []
    },
    columns: {
      type: 'number'
    },
    imageCrop: {
      type: 'boolean',
      default: true
    },
    linkTo: {
      type: 'string',
      default: 'none'
    }
  },
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var images = attributes.images,
        _attributes$columns = attributes.columns,
        columns = _attributes$columns === void 0 ? defaultColumnsNumber(attributes) : _attributes$columns,
        imageCrop = attributes.imageCrop,
        linkTo = attributes.linkTo;
    return Object(external_this_wp_element_["createElement"])("ul", {
      className: "columns-".concat(columns, " ").concat(imageCrop ? 'is-cropped' : '')
    }, images.map(function (image) {
      var href;

      switch (linkTo) {
        case 'media':
          href = image.fullUrl || image.url;
          break;

        case 'attachment':
          href = image.link;
          break;
      }

      var img = Object(external_this_wp_element_["createElement"])("img", {
        src: image.url,
        alt: image.alt,
        "data-id": image.id,
        "data-full-url": image.fullUrl,
        "data-link": image.link,
        className: image.id ? "wp-image-".concat(image.id) : null
      });
      return Object(external_this_wp_element_["createElement"])("li", {
        key: image.id || image.url,
        className: "blocks-gallery-item"
      }, Object(external_this_wp_element_["createElement"])("figure", null, href ? Object(external_this_wp_element_["createElement"])("a", {
        href: href
      }, img) : img, image.caption && image.caption.length > 0 && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
        tagName: "figcaption",
        value: image.caption
      })));
    }));
  }
}, {
  attributes: {
    images: {
      type: 'array',
      default: [],
      source: 'query',
      selector: 'ul.wp-block-gallery .blocks-gallery-item',
      query: {
        url: {
          source: 'attribute',
          selector: 'img',
          attribute: 'src'
        },
        alt: {
          source: 'attribute',
          selector: 'img',
          attribute: 'alt',
          default: ''
        },
        id: {
          source: 'attribute',
          selector: 'img',
          attribute: 'data-id'
        },
        link: {
          source: 'attribute',
          selector: 'img',
          attribute: 'data-link'
        },
        caption: {
          type: 'array',
          source: 'children',
          selector: 'figcaption'
        }
      }
    },
    columns: {
      type: 'number'
    },
    imageCrop: {
      type: 'boolean',
      default: true
    },
    linkTo: {
      type: 'string',
      default: 'none'
    }
  },
  isEligible: function isEligible(_ref2) {
    var images = _ref2.images,
        ids = _ref2.ids;
    return images && images.length > 0 && (!ids && images || ids && images && ids.length !== images.length || Object(external_lodash_["some"])(images, function (id, index) {
      if (!id && ids[index] !== null) {
        return true;
      }

      return parseInt(id, 10) !== ids[index];
    }));
  },
  migrate: function migrate(attributes) {
    return Object(objectSpread["a" /* default */])({}, attributes, {
      ids: Object(external_lodash_["map"])(attributes.images, function (_ref3) {
        var id = _ref3.id;

        if (!id) {
          return null;
        }

        return parseInt(id, 10);
      })
    });
  },
  save: function save(_ref4) {
    var attributes = _ref4.attributes;
    var images = attributes.images,
        _attributes$columns2 = attributes.columns,
        columns = _attributes$columns2 === void 0 ? defaultColumnsNumber(attributes) : _attributes$columns2,
        imageCrop = attributes.imageCrop,
        linkTo = attributes.linkTo;
    return Object(external_this_wp_element_["createElement"])("ul", {
      className: "columns-".concat(columns, " ").concat(imageCrop ? 'is-cropped' : '')
    }, images.map(function (image) {
      var href;

      switch (linkTo) {
        case 'media':
          href = image.url;
          break;

        case 'attachment':
          href = image.link;
          break;
      }

      var img = Object(external_this_wp_element_["createElement"])("img", {
        src: image.url,
        alt: image.alt,
        "data-id": image.id,
        "data-link": image.link,
        className: image.id ? "wp-image-".concat(image.id) : null
      });
      return Object(external_this_wp_element_["createElement"])("li", {
        key: image.id || image.url,
        className: "blocks-gallery-item"
      }, Object(external_this_wp_element_["createElement"])("figure", null, href ? Object(external_this_wp_element_["createElement"])("a", {
        href: href
      }, img) : img, image.caption && image.caption.length > 0 && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
        tagName: "figcaption",
        value: image.caption
      })));
    }));
  }
}, {
  attributes: {
    images: {
      type: 'array',
      default: [],
      source: 'query',
      selector: 'div.wp-block-gallery figure.blocks-gallery-image img',
      query: {
        url: {
          source: 'attribute',
          attribute: 'src'
        },
        alt: {
          source: 'attribute',
          attribute: 'alt',
          default: ''
        },
        id: {
          source: 'attribute',
          attribute: 'data-id'
        }
      }
    },
    columns: {
      type: 'number'
    },
    imageCrop: {
      type: 'boolean',
      default: true
    },
    linkTo: {
      type: 'string',
      default: 'none'
    },
    align: {
      type: 'string',
      default: 'none'
    }
  },
  save: function save(_ref5) {
    var attributes = _ref5.attributes;
    var images = attributes.images,
        _attributes$columns3 = attributes.columns,
        columns = _attributes$columns3 === void 0 ? defaultColumnsNumber(attributes) : _attributes$columns3,
        align = attributes.align,
        imageCrop = attributes.imageCrop,
        linkTo = attributes.linkTo;
    var className = classnames_default()("columns-".concat(columns), {
      alignnone: align === 'none',
      'is-cropped': imageCrop
    });
    return Object(external_this_wp_element_["createElement"])("div", {
      className: className
    }, images.map(function (image) {
      var href;

      switch (linkTo) {
        case 'media':
          href = image.url;
          break;

        case 'attachment':
          href = image.link;
          break;
      }

      var img = Object(external_this_wp_element_["createElement"])("img", {
        src: image.url,
        alt: image.alt,
        "data-id": image.id
      });
      return Object(external_this_wp_element_["createElement"])("figure", {
        key: image.id || image.url,
        className: "blocks-gallery-image"
      }, href ? Object(external_this_wp_element_["createElement"])("a", {
        href: href
      }, img) : img);
    }));
  }
}];
/* harmony default export */ var gallery_deprecated = (gallery_deprecated_deprecated);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/icons.js


/**
 * WordPress dependencies
 */

var icons_icon = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M20 4v12H8V4h12m0-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 9.67l1.69 2.26 2.48-3.1L19 15H9zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z"
})));
var leftArrow = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  width: "18",
  height: "18",
  viewBox: "0 0 18 18",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M5 8.70002L10.6 14.4L12 12.9L7.8 8.70002L12 4.50002L10.6 3.00002L5 8.70002Z"
}));
var rightArrow = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  width: "18",
  height: "18",
  viewBox: "0 0 18 18",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M13 8.7L7.4 3L6 4.5L10.2 8.7L6 12.9L7.4 14.4L13 8.7Z"
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/gallery-image.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */



var gallery_image_GalleryImage =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(GalleryImage, _Component);

  function GalleryImage() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, GalleryImage);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(GalleryImage).apply(this, arguments));
    _this.onSelectImage = _this.onSelectImage.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSelectCaption = _this.onSelectCaption.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onRemoveImage = _this.onRemoveImage.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.bindContainer = _this.bindContainer.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.state = {
      captionSelected: false
    };
    return _this;
  }

  Object(createClass["a" /* default */])(GalleryImage, [{
    key: "bindContainer",
    value: function bindContainer(ref) {
      this.container = ref;
    }
  }, {
    key: "onSelectCaption",
    value: function onSelectCaption() {
      if (!this.state.captionSelected) {
        this.setState({
          captionSelected: true
        });
      }

      if (!this.props.isSelected) {
        this.props.onSelect();
      }
    }
  }, {
    key: "onSelectImage",
    value: function onSelectImage() {
      if (!this.props.isSelected) {
        this.props.onSelect();
      }

      if (this.state.captionSelected) {
        this.setState({
          captionSelected: false
        });
      }
    }
  }, {
    key: "onRemoveImage",
    value: function onRemoveImage(event) {
      if (this.container === document.activeElement && this.props.isSelected && [external_this_wp_keycodes_["BACKSPACE"], external_this_wp_keycodes_["DELETE"]].indexOf(event.keyCode) !== -1) {
        event.stopPropagation();
        event.preventDefault();
        this.props.onRemove();
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this$props = this.props,
          isSelected = _this$props.isSelected,
          image = _this$props.image,
          url = _this$props.url;

      if (image && !url) {
        this.props.setAttributes({
          url: image.source_url,
          alt: image.alt_text
        });
      } // unselect the caption so when the user selects other image and comeback
      // the caption is not immediately selected


      if (this.state.captionSelected && !isSelected && prevProps.isSelected) {
        this.setState({
          captionSelected: false
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          url = _this$props2.url,
          alt = _this$props2.alt,
          id = _this$props2.id,
          linkTo = _this$props2.linkTo,
          link = _this$props2.link,
          isFirstItem = _this$props2.isFirstItem,
          isLastItem = _this$props2.isLastItem,
          isSelected = _this$props2.isSelected,
          caption = _this$props2.caption,
          onRemove = _this$props2.onRemove,
          onMoveForward = _this$props2.onMoveForward,
          onMoveBackward = _this$props2.onMoveBackward,
          setAttributes = _this$props2.setAttributes,
          ariaLabel = _this$props2['aria-label'];
      var href;

      switch (linkTo) {
        case 'media':
          href = url;
          break;

        case 'attachment':
          href = link;
          break;
      }

      var img = // Disable reason: Image itself is not meant to be interactive, but should
      // direct image selection and unfocus caption fields.

      /* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
      Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("img", {
        src: url,
        alt: alt,
        "data-id": id,
        onClick: this.onSelectImage,
        onFocus: this.onSelectImage,
        onKeyDown: this.onRemoveImage,
        tabIndex: "0",
        "aria-label": ariaLabel,
        ref: this.bindContainer
      }), Object(external_this_wp_blob_["isBlobURL"])(url) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null))
      /* eslint-enable jsx-a11y/no-noninteractive-element-interactions */
      ;
      var className = classnames_default()({
        'is-selected': isSelected,
        'is-transient': Object(external_this_wp_blob_["isBlobURL"])(url)
      });
      return Object(external_this_wp_element_["createElement"])("figure", {
        className: className
      }, href ? Object(external_this_wp_element_["createElement"])("a", {
        href: href
      }, img) : img, Object(external_this_wp_element_["createElement"])("div", {
        className: "block-library-gallery-item__move-menu"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        icon: leftArrow,
        onClick: isFirstItem ? undefined : onMoveBackward,
        className: "blocks-gallery-item__move-backward",
        label: Object(external_this_wp_i18n_["__"])('Move image backward'),
        "aria-disabled": isFirstItem,
        disabled: !isSelected
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        icon: rightArrow,
        onClick: isLastItem ? undefined : onMoveForward,
        className: "blocks-gallery-item__move-forward",
        label: Object(external_this_wp_i18n_["__"])('Move image forward'),
        "aria-disabled": isLastItem,
        disabled: !isSelected
      })), Object(external_this_wp_element_["createElement"])("div", {
        className: "block-library-gallery-item__inline-menu"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        icon: "no-alt",
        onClick: onRemove,
        className: "blocks-gallery-item__remove",
        label: Object(external_this_wp_i18n_["__"])('Remove image'),
        disabled: !isSelected
      })), (isSelected || caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        tagName: "figcaption",
        placeholder: isSelected ? Object(external_this_wp_i18n_["__"])('Write caption…') : null,
        value: caption,
        isSelected: this.state.captionSelected,
        onChange: function onChange(newCaption) {
          return setAttributes({
            caption: newCaption
          });
        },
        unstableOnFocus: this.onSelectCaption,
        inlineToolbar: true
      }));
    }
  }]);

  return GalleryImage;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var gallery_image = (Object(external_this_wp_data_["withSelect"])(function (select, ownProps) {
  var _select = select('core'),
      getMedia = _select.getMedia;

  var id = ownProps.id;
  return {
    image: id ? getMedia(id) : null
  };
})(gallery_image_GalleryImage));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/edit.js











/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */




var MAX_COLUMNS = 8;
var linkOptions = [{
  value: 'attachment',
  label: Object(external_this_wp_i18n_["__"])('Attachment Page')
}, {
  value: 'media',
  label: Object(external_this_wp_i18n_["__"])('Media File')
}, {
  value: 'none',
  label: Object(external_this_wp_i18n_["__"])('None')
}];
var edit_ALLOWED_MEDIA_TYPES = ['image'];

var edit_GalleryEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(GalleryEdit, _Component);

  function GalleryEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, GalleryEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(GalleryEdit).apply(this, arguments));
    _this.onSelectImage = _this.onSelectImage.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSelectImages = _this.onSelectImages.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.setLinkTo = _this.setLinkTo.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.setColumnsNumber = _this.setColumnsNumber.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.toggleImageCrop = _this.toggleImageCrop.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onMove = _this.onMove.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onMoveForward = _this.onMoveForward.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onMoveBackward = _this.onMoveBackward.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onRemoveImage = _this.onRemoveImage.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onUploadError = _this.onUploadError.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.setImageAttributes = _this.setImageAttributes.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.setAttributes = _this.setAttributes.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onFocusGalleryCaption = _this.onFocusGalleryCaption.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.state = {
      selectedImage: null,
      attachmentCaptions: null
    };
    return _this;
  }

  Object(createClass["a" /* default */])(GalleryEdit, [{
    key: "setAttributes",
    value: function setAttributes(attributes) {
      if (attributes.ids) {
        throw new Error('The "ids" attribute should not be changed directly. It is managed automatically when "images" attribute changes');
      }

      if (attributes.images) {
        attributes = Object(objectSpread["a" /* default */])({}, attributes, {
          ids: Object(external_lodash_["map"])(attributes.images, 'id')
        });
      }

      this.props.setAttributes(attributes);
    }
  }, {
    key: "onSelectImage",
    value: function onSelectImage(index) {
      var _this2 = this;

      return function () {
        if (_this2.state.selectedImage !== index) {
          _this2.setState({
            selectedImage: index
          });
        }
      };
    }
  }, {
    key: "onMove",
    value: function onMove(oldIndex, newIndex) {
      var images = Object(toConsumableArray["a" /* default */])(this.props.attributes.images);

      images.splice(newIndex, 1, this.props.attributes.images[oldIndex]);
      images.splice(oldIndex, 1, this.props.attributes.images[newIndex]);
      this.setState({
        selectedImage: newIndex
      });
      this.setAttributes({
        images: images
      });
    }
  }, {
    key: "onMoveForward",
    value: function onMoveForward(oldIndex) {
      var _this3 = this;

      return function () {
        if (oldIndex === _this3.props.attributes.images.length - 1) {
          return;
        }

        _this3.onMove(oldIndex, oldIndex + 1);
      };
    }
  }, {
    key: "onMoveBackward",
    value: function onMoveBackward(oldIndex) {
      var _this4 = this;

      return function () {
        if (oldIndex === 0) {
          return;
        }

        _this4.onMove(oldIndex, oldIndex - 1);
      };
    }
  }, {
    key: "onRemoveImage",
    value: function onRemoveImage(index) {
      var _this5 = this;

      return function () {
        var images = Object(external_lodash_["filter"])(_this5.props.attributes.images, function (img, i) {
          return index !== i;
        });
        var columns = _this5.props.attributes.columns;

        _this5.setState({
          selectedImage: null
        });

        _this5.setAttributes({
          images: images,
          columns: columns ? Math.min(images.length, columns) : columns
        });
      };
    }
  }, {
    key: "selectCaption",
    value: function selectCaption(newImage, images, attachmentCaptions) {
      var currentImage = Object(external_lodash_["find"])(images, {
        id: newImage.id
      });
      var currentImageCaption = currentImage ? currentImage.caption : newImage.caption;

      if (!attachmentCaptions) {
        return currentImageCaption;
      }

      var attachment = Object(external_lodash_["find"])(attachmentCaptions, {
        id: newImage.id
      }); // if the attachment caption is updated

      if (attachment && attachment.caption !== newImage.caption) {
        return newImage.caption;
      }

      return currentImageCaption;
    }
  }, {
    key: "onSelectImages",
    value: function onSelectImages(newImages) {
      var _this6 = this;

      var _this$props$attribute = this.props.attributes,
          columns = _this$props$attribute.columns,
          images = _this$props$attribute.images;
      var attachmentCaptions = this.state.attachmentCaptions;
      this.setState({
        attachmentCaptions: newImages.map(function (newImage) {
          return {
            id: newImage.id,
            caption: newImage.caption
          };
        })
      });
      this.setAttributes({
        images: newImages.map(function (newImage) {
          return Object(objectSpread["a" /* default */])({}, shared_pickRelevantMediaFiles(newImage), {
            caption: _this6.selectCaption(newImage, images, attachmentCaptions)
          });
        }),
        columns: columns ? Math.min(newImages.length, columns) : columns
      });
    }
  }, {
    key: "onUploadError",
    value: function onUploadError(message) {
      var noticeOperations = this.props.noticeOperations;
      noticeOperations.removeAllNotices();
      noticeOperations.createErrorNotice(message);
    }
  }, {
    key: "setLinkTo",
    value: function setLinkTo(value) {
      this.setAttributes({
        linkTo: value
      });
    }
  }, {
    key: "setColumnsNumber",
    value: function setColumnsNumber(value) {
      this.setAttributes({
        columns: value
      });
    }
  }, {
    key: "toggleImageCrop",
    value: function toggleImageCrop() {
      this.setAttributes({
        imageCrop: !this.props.attributes.imageCrop
      });
    }
  }, {
    key: "getImageCropHelp",
    value: function getImageCropHelp(checked) {
      return checked ? Object(external_this_wp_i18n_["__"])('Thumbnails are cropped to align.') : Object(external_this_wp_i18n_["__"])('Thumbnails are not cropped.');
    }
  }, {
    key: "onFocusGalleryCaption",
    value: function onFocusGalleryCaption() {
      this.setState({
        selectedImage: null
      });
    }
  }, {
    key: "setImageAttributes",
    value: function setImageAttributes(index, attributes) {
      var images = this.props.attributes.images;
      var setAttributes = this.setAttributes;

      if (!images[index]) {
        return;
      }

      setAttributes({
        images: [].concat(Object(toConsumableArray["a" /* default */])(images.slice(0, index)), [Object(objectSpread["a" /* default */])({}, images[index], attributes)], Object(toConsumableArray["a" /* default */])(images.slice(index + 1)))
      });
    }
  }, {
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          mediaUpload = _this$props.mediaUpload;
      var images = attributes.images;

      if (Object(external_lodash_["every"])(images, function (_ref) {
        var url = _ref.url;
        return Object(external_this_wp_blob_["isBlobURL"])(url);
      })) {
        var filesList = Object(external_lodash_["map"])(images, function (_ref2) {
          var url = _ref2.url;
          return Object(external_this_wp_blob_["getBlobByURL"])(url);
        });
        Object(external_lodash_["forEach"])(images, function (_ref3) {
          var url = _ref3.url;
          return Object(external_this_wp_blob_["revokeBlobURL"])(url);
        });
        mediaUpload({
          filesList: filesList,
          onFileChange: this.onSelectImages,
          allowedTypes: ['image']
        });
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      // Deselect images when deselecting the block
      if (!this.props.isSelected && prevProps.isSelected) {
        this.setState({
          selectedImage: null,
          captionSelected: false
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _classnames,
          _this7 = this;

      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          className = _this$props2.className,
          isSelected = _this$props2.isSelected,
          noticeUI = _this$props2.noticeUI,
          setAttributes = _this$props2.setAttributes;
      var align = attributes.align,
          _attributes$columns = attributes.columns,
          columns = _attributes$columns === void 0 ? defaultColumnsNumber(attributes) : _attributes$columns,
          caption = attributes.caption,
          imageCrop = attributes.imageCrop,
          images = attributes.images,
          linkTo = attributes.linkTo;
      var hasImages = !!images.length;
      var hasImagesWithId = hasImages && Object(external_lodash_["some"])(images, function (_ref4) {
        var id = _ref4.id;
        return id;
      });
      var mediaPlaceholder = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaPlaceholder"], {
        addToGallery: hasImagesWithId,
        isAppender: hasImages,
        className: className,
        dropZoneUIOnly: hasImages && !isSelected,
        icon: !hasImages && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
          icon: icons_icon
        }),
        labels: {
          title: !hasImages && Object(external_this_wp_i18n_["__"])('Gallery'),
          instructions: !hasImages && Object(external_this_wp_i18n_["__"])('Drag images, upload new ones or select files from your library.')
        },
        onSelect: this.onSelectImages,
        accept: "image/*",
        allowedTypes: edit_ALLOWED_MEDIA_TYPES,
        multiple: true,
        value: hasImagesWithId ? images : undefined,
        onError: this.onUploadError,
        notices: hasImages ? undefined : noticeUI
      });

      if (!hasImages) {
        return mediaPlaceholder;
      }

      var captionClassNames = classnames_default()('blocks-gallery-caption', {
        'screen-reader-text': !isSelected && external_this_wp_blockEditor_["RichText"].isEmpty(caption)
      });
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Gallery Settings')
      }, images.length > 1 && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
        label: Object(external_this_wp_i18n_["__"])('Columns'),
        value: columns,
        onChange: this.setColumnsNumber,
        min: 1,
        max: Math.min(MAX_COLUMNS, images.length),
        required: true
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Crop Images'),
        checked: !!imageCrop,
        onChange: this.toggleImageCrop,
        help: this.getImageCropHelp
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
        label: Object(external_this_wp_i18n_["__"])('Link To'),
        value: linkTo,
        onChange: this.setLinkTo,
        options: linkOptions
      }))), noticeUI, Object(external_this_wp_element_["createElement"])("figure", {
        className: classnames_default()(className, (_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, "align".concat(align), align), Object(defineProperty["a" /* default */])(_classnames, "columns-".concat(columns), columns), Object(defineProperty["a" /* default */])(_classnames, 'is-cropped', imageCrop), _classnames))
      }, Object(external_this_wp_element_["createElement"])("ul", {
        className: "blocks-gallery-grid"
      }, images.map(function (img, index) {
        /* translators: %1$d is the order number of the image, %2$d is the total number of images. */
        var ariaLabel = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('image %1$d of %2$d in gallery'), index + 1, images.length);
        return Object(external_this_wp_element_["createElement"])("li", {
          className: "blocks-gallery-item",
          key: img.id || img.url
        }, Object(external_this_wp_element_["createElement"])(gallery_image, {
          url: img.url,
          alt: img.alt,
          id: img.id,
          isFirstItem: index === 0,
          isLastItem: index + 1 === images.length,
          isSelected: isSelected && _this7.state.selectedImage === index,
          onMoveBackward: _this7.onMoveBackward(index),
          onMoveForward: _this7.onMoveForward(index),
          onRemove: _this7.onRemoveImage(index),
          onSelect: _this7.onSelectImage(index),
          setAttributes: function setAttributes(attrs) {
            return _this7.setImageAttributes(index, attrs);
          },
          caption: img.caption,
          "aria-label": ariaLabel
        }));
      })), mediaPlaceholder, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        tagName: "figcaption",
        className: captionClassNames,
        placeholder: Object(external_this_wp_i18n_["__"])('Write gallery caption…'),
        value: caption,
        unstableOnFocus: this.onFocusGalleryCaption,
        onChange: function onChange(value) {
          return setAttributes({
            caption: value
          });
        },
        inlineToolbar: true
      })));
    }
  }]);

  return GalleryEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var gallery_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getSettings = _select.getSettings;

  var _getSettings = getSettings(),
      __experimentalMediaUpload = _getSettings.__experimentalMediaUpload;

  return {
    mediaUpload: __experimentalMediaUpload
  };
}), external_this_wp_components_["withNotices"]])(edit_GalleryEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/save.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


function gallery_save_save(_ref) {
  var attributes = _ref.attributes;
  var images = attributes.images,
      _attributes$columns = attributes.columns,
      columns = _attributes$columns === void 0 ? defaultColumnsNumber(attributes) : _attributes$columns,
      imageCrop = attributes.imageCrop,
      caption = attributes.caption,
      linkTo = attributes.linkTo;
  return Object(external_this_wp_element_["createElement"])("figure", {
    className: "columns-".concat(columns, " ").concat(imageCrop ? 'is-cropped' : '')
  }, Object(external_this_wp_element_["createElement"])("ul", {
    className: "blocks-gallery-grid"
  }, images.map(function (image) {
    var href;

    switch (linkTo) {
      case 'media':
        href = image.fullUrl || image.url;
        break;

      case 'attachment':
        href = image.link;
        break;
    }

    var img = Object(external_this_wp_element_["createElement"])("img", {
      src: image.url,
      alt: image.alt,
      "data-id": image.id,
      "data-full-url": image.fullUrl,
      "data-link": image.link,
      className: image.id ? "wp-image-".concat(image.id) : null
    });
    return Object(external_this_wp_element_["createElement"])("li", {
      key: image.id || image.url,
      className: "blocks-gallery-item"
    }, Object(external_this_wp_element_["createElement"])("figure", null, href ? Object(external_this_wp_element_["createElement"])("a", {
      href: href
    }, img) : img, !external_this_wp_blockEditor_["RichText"].isEmpty(image.caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "figcaption",
      className: "blocks-gallery-item__caption",
      value: image.caption
    })));
  })), !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "figcaption",
    className: "blocks-gallery-caption",
    value: caption
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/transforms.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



var parseShortcodeIds = function parseShortcodeIds(ids) {
  if (!ids) {
    return [];
  }

  return ids.split(',').map(function (id) {
    return parseInt(id, 10);
  });
};

var gallery_transforms_transforms = {
  from: [{
    type: 'block',
    isMultiBlock: true,
    blocks: ['core/image'],
    transform: function transform(attributes) {
      // Init the align attribute from the first item which may be either the placeholder or an image.
      var align = attributes[0].align; // Loop through all the images and check if they have the same align.

      align = Object(external_lodash_["every"])(attributes, ['align', align]) ? align : undefined;
      var validImages = Object(external_lodash_["filter"])(attributes, function (_ref) {
        var url = _ref.url;
        return url;
      });
      return Object(external_this_wp_blocks_["createBlock"])('core/gallery', {
        images: validImages.map(function (_ref2) {
          var id = _ref2.id,
              url = _ref2.url,
              alt = _ref2.alt,
              caption = _ref2.caption;
          return {
            id: id,
            url: url,
            alt: alt,
            caption: caption
          };
        }),
        ids: validImages.map(function (_ref3) {
          var id = _ref3.id;
          return id;
        }),
        align: align
      });
    }
  }, {
    type: 'shortcode',
    tag: 'gallery',
    attributes: {
      images: {
        type: 'array',
        shortcode: function shortcode(_ref4) {
          var ids = _ref4.named.ids;
          return parseShortcodeIds(ids).map(function (id) {
            return {
              id: id
            };
          });
        }
      },
      ids: {
        type: 'array',
        shortcode: function shortcode(_ref5) {
          var ids = _ref5.named.ids;
          return parseShortcodeIds(ids);
        }
      },
      columns: {
        type: 'number',
        shortcode: function shortcode(_ref6) {
          var _ref6$named$columns = _ref6.named.columns,
              columns = _ref6$named$columns === void 0 ? '3' : _ref6$named$columns;
          return parseInt(columns, 10);
        }
      },
      linkTo: {
        type: 'string',
        shortcode: function shortcode(_ref7) {
          var _ref7$named$link = _ref7.named.link,
              link = _ref7$named$link === void 0 ? 'attachment' : _ref7$named$link;
          return link === 'file' ? 'media' : link;
        }
      }
    }
  }, {
    // When created by drag and dropping multiple files on an insertion point
    type: 'files',
    isMatch: function isMatch(files) {
      return files.length !== 1 && Object(external_lodash_["every"])(files, function (file) {
        return file.type.indexOf('image/') === 0;
      });
    },
    transform: function transform(files) {
      var block = Object(external_this_wp_blocks_["createBlock"])('core/gallery', {
        images: files.map(function (file) {
          return shared_pickRelevantMediaFiles({
            url: Object(external_this_wp_blob_["createBlobURL"])(file)
          });
        })
      });
      return block;
    }
  }],
  to: [{
    type: 'block',
    blocks: ['core/image'],
    transform: function transform(_ref8) {
      var images = _ref8.images,
          align = _ref8.align;

      if (images.length > 0) {
        return images.map(function (_ref9) {
          var id = _ref9.id,
              url = _ref9.url,
              alt = _ref9.alt,
              caption = _ref9.caption;
          return Object(external_this_wp_blocks_["createBlock"])('core/image', {
            id: id,
            url: url,
            alt: alt,
            caption: caption,
            align: align
          });
        });
      }

      return Object(external_this_wp_blocks_["createBlock"])('core/image', {
        align: align
      });
    }
  }]
};
/* harmony default export */ var gallery_transforms = (gallery_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var gallery_metadata = {
  name: "core/gallery",
  category: "common",
  attributes: {
    images: {
      type: "array",
      "default": [],
      source: "query",
      selector: ".blocks-gallery-item",
      query: {
        url: {
          source: "attribute",
          selector: "img",
          attribute: "src"
        },
        fullUrl: {
          source: "attribute",
          selector: "img",
          attribute: "data-full-url"
        },
        link: {
          source: "attribute",
          selector: "img",
          attribute: "data-link"
        },
        alt: {
          source: "attribute",
          selector: "img",
          attribute: "alt",
          "default": ""
        },
        id: {
          source: "attribute",
          selector: "img",
          attribute: "data-id"
        },
        caption: {
          type: "string",
          source: "html",
          selector: ".blocks-gallery-item__caption"
        }
      }
    },
    ids: {
      type: "array",
      "default": []
    },
    columns: {
      type: "number"
    },
    caption: {
      type: "string",
      source: "html",
      selector: ".blocks-gallery-caption"
    },
    imageCrop: {
      type: "boolean",
      "default": true
    },
    linkTo: {
      type: "string",
      "default": "none"
    }
  }
};


var gallery_name = gallery_metadata.name;

var gallery_settings = {
  title: Object(external_this_wp_i18n_["__"])('Gallery'),
  description: Object(external_this_wp_i18n_["__"])('Display multiple images in a rich gallery.'),
  icon: icons_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('images'), Object(external_this_wp_i18n_["__"])('photos')],
  example: {
    attributes: {
      columns: 2,
      images: [{
        url: 'https://s.w.org/images/core/5.3/Glacial_lakes%2C_Bhutan.jpg'
      }, {
        url: 'https://s.w.org/images/core/5.3/Sediment_off_the_Yucatan_Peninsula.jpg'
      }]
    }
  },
  supports: {
    align: true
  },
  transforms: gallery_transforms,
  edit: gallery_edit,
  save: gallery_save_save,
  deprecated: gallery_deprecated
};

// EXTERNAL MODULE: external {"this":["wp","serverSideRender"]}
var external_this_wp_serverSideRender_ = __webpack_require__(58);
var external_this_wp_serverSideRender_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_serverSideRender_);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/archives/edit.js


/**
 * WordPress dependencies
 */




function ArchivesEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes;
  var showPostCounts = attributes.showPostCounts,
      displayAsDropdown = attributes.displayAsDropdown;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Archives Settings')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
    label: Object(external_this_wp_i18n_["__"])('Display as Dropdown'),
    checked: displayAsDropdown,
    onChange: function onChange() {
      return setAttributes({
        displayAsDropdown: !displayAsDropdown
      });
    }
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
    label: Object(external_this_wp_i18n_["__"])('Show Post Counts'),
    checked: showPostCounts,
    onChange: function onChange() {
      return setAttributes({
        showPostCounts: !showPostCounts
      });
    }
  }))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_serverSideRender_default.a, {
    block: "core/archives",
    attributes: attributes
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/archives/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var archives_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M21 6V20C21 21.1 20.1 22 19 22H5C3.89 22 3 21.1 3 20L3.01 6C3.01 4.9 3.89 4 5 4H6V2H8V4H16V2H18V4H19C20.1 4 21 4.9 21 6ZM5 8H19V6H5V8ZM19 20V10H5V20H19ZM11 12H17V14H11V12ZM17 16H11V18H17V16ZM7 12H9V14H7V12ZM9 18V16H7V18H9Z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/archives/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var archives_name = 'core/archives';
var archives_settings = {
  title: Object(external_this_wp_i18n_["__"])('Archives'),
  description: Object(external_this_wp_i18n_["__"])('Display a monthly archive of your posts.'),
  icon: archives_icon,
  category: 'widgets',
  supports: {
    align: true,
    html: false
  },
  edit: ArchivesEdit
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/audio/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var audio_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m12 3l0.01 10.55c-0.59-0.34-1.27-0.55-2-0.55-2.22 0-4.01 1.79-4.01 4s1.79 4 4.01 4 3.99-1.79 3.99-4v-10h4v-4h-6zm-1.99 16c-1.1 0-2-0.9-2-2s0.9-2 2-2 2 0.9 2 2-0.9 2-2 2z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/audio/edit.js










/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


/**
 * Internal dependencies
 */


var audio_edit_ALLOWED_MEDIA_TYPES = ['audio'];

var edit_AudioEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(AudioEdit, _Component);

  function AudioEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, AudioEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(AudioEdit).apply(this, arguments)); // edit component has its own src in the state so it can be edited
    // without setting the actual value outside of the edit UI

    _this.state = {
      editing: !_this.props.attributes.src
    };
    _this.toggleAttribute = _this.toggleAttribute.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSelectURL = _this.onSelectURL.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onUploadError = _this.onUploadError.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(AudioEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          mediaUpload = _this$props.mediaUpload,
          noticeOperations = _this$props.noticeOperations,
          setAttributes = _this$props.setAttributes;
      var id = attributes.id,
          _attributes$src = attributes.src,
          src = _attributes$src === void 0 ? '' : _attributes$src;

      if (!id && Object(external_this_wp_blob_["isBlobURL"])(src)) {
        var file = Object(external_this_wp_blob_["getBlobByURL"])(src);

        if (file) {
          mediaUpload({
            filesList: [file],
            onFileChange: function onFileChange(_ref) {
              var _ref2 = Object(slicedToArray["a" /* default */])(_ref, 1),
                  _ref2$ = _ref2[0],
                  mediaId = _ref2$.id,
                  url = _ref2$.url;

              setAttributes({
                id: mediaId,
                src: url
              });
            },
            onError: function onError(e) {
              setAttributes({
                src: undefined,
                id: undefined
              });

              _this2.setState({
                editing: true
              });

              noticeOperations.createErrorNotice(e);
            },
            allowedTypes: audio_edit_ALLOWED_MEDIA_TYPES
          });
        }
      }
    }
  }, {
    key: "toggleAttribute",
    value: function toggleAttribute(attribute) {
      var _this3 = this;

      return function (newValue) {
        _this3.props.setAttributes(Object(defineProperty["a" /* default */])({}, attribute, newValue));
      };
    }
  }, {
    key: "onSelectURL",
    value: function onSelectURL(newSrc) {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      var src = attributes.src; // Set the block's src from the edit component's state, and switch off
      // the editing UI.

      if (newSrc !== src) {
        // Check if there's an embed block that handles this URL.
        var embedBlock = util_createUpgradedEmbedBlock({
          attributes: {
            url: newSrc
          }
        });

        if (undefined !== embedBlock) {
          this.props.onReplace(embedBlock);
          return;
        }

        setAttributes({
          src: newSrc,
          id: undefined
        });
      }

      this.setState({
        editing: false
      });
    }
  }, {
    key: "onUploadError",
    value: function onUploadError(message) {
      var noticeOperations = this.props.noticeOperations;
      noticeOperations.removeAllNotices();
      noticeOperations.createErrorNotice(message);
    }
  }, {
    key: "getAutoplayHelp",
    value: function getAutoplayHelp(checked) {
      return checked ? Object(external_this_wp_i18n_["__"])('Note: Autoplaying audio may cause usability issues for some visitors.') : null;
    }
  }, {
    key: "render",
    value: function render() {
      var _this4 = this;

      var _this$props$attribute = this.props.attributes,
          autoplay = _this$props$attribute.autoplay,
          caption = _this$props$attribute.caption,
          loop = _this$props$attribute.loop,
          preload = _this$props$attribute.preload,
          src = _this$props$attribute.src;
      var _this$props3 = this.props,
          setAttributes = _this$props3.setAttributes,
          isSelected = _this$props3.isSelected,
          className = _this$props3.className,
          noticeUI = _this$props3.noticeUI;
      var editing = this.state.editing;

      var switchToEditing = function switchToEditing() {
        _this4.setState({
          editing: true
        });
      };

      var onSelectAudio = function onSelectAudio(media) {
        if (!media || !media.url) {
          // in this case there was an error and we should continue in the editing state
          // previous attributes should be removed because they may be temporary blob urls
          setAttributes({
            src: undefined,
            id: undefined
          });
          switchToEditing();
          return;
        } // sets the block's attribute and updates the edit component from the
        // selected media, then switches off the editing UI


        setAttributes({
          src: media.url,
          id: media.id
        });

        _this4.setState({
          src: media.url,
          editing: false
        });
      };

      if (editing) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaPlaceholder"], {
          icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
            icon: audio_icon
          }),
          className: className,
          onSelect: onSelectAudio,
          onSelectURL: this.onSelectURL,
          accept: "audio/*",
          allowedTypes: audio_edit_ALLOWED_MEDIA_TYPES,
          value: this.props.attributes,
          notices: noticeUI,
          onError: this.onUploadError
        });
      }

      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        className: "components-icon-button components-toolbar__control",
        label: Object(external_this_wp_i18n_["__"])('Edit audio'),
        onClick: switchToEditing,
        icon: "edit"
      }))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Audio Settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Autoplay'),
        onChange: this.toggleAttribute('autoplay'),
        checked: autoplay,
        help: this.getAutoplayHelp
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Loop'),
        onChange: this.toggleAttribute('loop'),
        checked: loop
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
        label: Object(external_this_wp_i18n_["__"])('Preload'),
        value: undefined !== preload ? preload : 'none' // `undefined` is required for the preload attribute to be unset.
        ,
        onChange: function onChange(value) {
          return setAttributes({
            preload: 'none' !== value ? value : undefined
          });
        },
        options: [{
          value: 'auto',
          label: Object(external_this_wp_i18n_["__"])('Auto')
        }, {
          value: 'metadata',
          label: Object(external_this_wp_i18n_["__"])('Metadata')
        }, {
          value: 'none',
          label: Object(external_this_wp_i18n_["__"])('None')
        }]
      }))), Object(external_this_wp_element_["createElement"])("figure", {
        className: className
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])("audio", {
        controls: "controls",
        src: src
      })), (!external_this_wp_blockEditor_["RichText"].isEmpty(caption) || isSelected) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        tagName: "figcaption",
        placeholder: Object(external_this_wp_i18n_["__"])('Write caption…'),
        value: caption,
        onChange: function onChange(value) {
          return setAttributes({
            caption: value
          });
        },
        inlineToolbar: true
      })));
    }
  }]);

  return AudioEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var audio_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getSettings = _select.getSettings;

  var _getSettings = getSettings(),
      __experimentalMediaUpload = _getSettings.__experimentalMediaUpload;

  return {
    mediaUpload: __experimentalMediaUpload
  };
}), external_this_wp_components_["withNotices"]])(edit_AudioEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/audio/save.js


/**
 * WordPress dependencies
 */

function audio_save_save(_ref) {
  var attributes = _ref.attributes;
  var autoplay = attributes.autoplay,
      caption = attributes.caption,
      loop = attributes.loop,
      preload = attributes.preload,
      src = attributes.src;
  return Object(external_this_wp_element_["createElement"])("figure", null, Object(external_this_wp_element_["createElement"])("audio", {
    controls: "controls",
    src: src,
    autoPlay: autoplay,
    loop: loop,
    preload: preload
  }), !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "figcaption",
    value: caption
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/audio/transforms.js
/**
 * WordPress dependencies
 */


var audio_transforms_transforms = {
  from: [{
    type: 'files',
    isMatch: function isMatch(files) {
      return files.length === 1 && files[0].type.indexOf('audio/') === 0;
    },
    transform: function transform(files) {
      var file = files[0]; // We don't need to upload the media directly here
      // It's already done as part of the `componentDidMount`
      // in the audio block

      var block = Object(external_this_wp_blocks_["createBlock"])('core/audio', {
        src: Object(external_this_wp_blob_["createBlobURL"])(file)
      });
      return block;
    }
  }, {
    type: 'shortcode',
    tag: 'audio',
    attributes: {
      src: {
        type: 'string',
        shortcode: function shortcode(_ref) {
          var src = _ref.named.src;
          return src;
        }
      },
      loop: {
        type: 'string',
        shortcode: function shortcode(_ref2) {
          var loop = _ref2.named.loop;
          return loop;
        }
      },
      autoplay: {
        type: 'string',
        shortcode: function shortcode(_ref3) {
          var autoplay = _ref3.named.autoplay;
          return autoplay;
        }
      },
      preload: {
        type: 'string',
        shortcode: function shortcode(_ref4) {
          var preload = _ref4.named.preload;
          return preload;
        }
      }
    }
  }]
};
/* harmony default export */ var audio_transforms = (audio_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/audio/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var audio_metadata = {
  name: "core/audio",
  category: "common",
  attributes: {
    src: {
      type: "string",
      source: "attribute",
      selector: "audio",
      attribute: "src"
    },
    caption: {
      type: "string",
      source: "html",
      selector: "figcaption"
    },
    id: {
      type: "number"
    },
    autoplay: {
      type: "boolean",
      source: "attribute",
      selector: "audio",
      attribute: "autoplay"
    },
    loop: {
      type: "boolean",
      source: "attribute",
      selector: "audio",
      attribute: "loop"
    },
    preload: {
      type: "string",
      source: "attribute",
      selector: "audio",
      attribute: "preload"
    }
  }
};


var audio_name = audio_metadata.name;

var audio_settings = {
  title: Object(external_this_wp_i18n_["__"])('Audio'),
  description: Object(external_this_wp_i18n_["__"])('Embed a simple audio player.'),
  icon: audio_icon,
  transforms: audio_transforms,
  supports: {
    align: true
  },
  edit: audio_edit,
  save: audio_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/button/deprecated.js




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



var deprecated_colorsMigration = function colorsMigration(attributes) {
  return Object(external_lodash_["omit"])(Object(objectSpread["a" /* default */])({}, attributes, {
    customTextColor: attributes.textColor && '#' === attributes.textColor[0] ? attributes.textColor : undefined,
    customBackgroundColor: attributes.color && '#' === attributes.color[0] ? attributes.color : undefined
  }), ['color', 'textColor']);
};

var button_deprecated_blockAttributes = {
  url: {
    type: 'string',
    source: 'attribute',
    selector: 'a',
    attribute: 'href'
  },
  title: {
    type: 'string',
    source: 'attribute',
    selector: 'a',
    attribute: 'title'
  },
  text: {
    type: 'string',
    source: 'html',
    selector: 'a'
  }
};
var button_deprecated_deprecated = [{
  attributes: Object(objectSpread["a" /* default */])({}, button_deprecated_blockAttributes, {
    align: {
      type: 'string',
      default: 'none'
    },
    backgroundColor: {
      type: 'string'
    },
    textColor: {
      type: 'string'
    },
    customBackgroundColor: {
      type: 'string'
    },
    customTextColor: {
      type: 'string'
    },
    linkTarget: {
      type: 'string',
      source: 'attribute',
      selector: 'a',
      attribute: 'target'
    },
    rel: {
      type: 'string',
      source: 'attribute',
      selector: 'a',
      attribute: 'rel'
    },
    placeholder: {
      type: 'string'
    }
  }),
  isEligible: function isEligible(attribute) {
    return attribute.className && attribute.className.includes('is-style-squared');
  },
  migrate: function migrate(attributes) {
    var newClassName = attributes.className;

    if (newClassName) {
      newClassName = newClassName.replace(/is-style-squared[\s]?/, '').trim();
    }

    return Object(objectSpread["a" /* default */])({}, attributes, {
      className: newClassName ? newClassName : undefined,
      borderRadius: 0
    });
  },
  save: function save(_ref) {
    var _classnames;

    var attributes = _ref.attributes;
    var backgroundColor = attributes.backgroundColor,
        customBackgroundColor = attributes.customBackgroundColor,
        customTextColor = attributes.customTextColor,
        linkTarget = attributes.linkTarget,
        rel = attributes.rel,
        text = attributes.text,
        textColor = attributes.textColor,
        title = attributes.title,
        url = attributes.url;
    var textClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
    var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
    var buttonClasses = classnames_default()('wp-block-button__link', (_classnames = {
      'has-text-color': textColor || customTextColor
    }, Object(defineProperty["a" /* default */])(_classnames, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames, 'has-background', backgroundColor || customBackgroundColor), Object(defineProperty["a" /* default */])(_classnames, backgroundClass, backgroundClass), _classnames));
    var buttonStyle = {
      backgroundColor: backgroundClass ? undefined : customBackgroundColor,
      color: textClass ? undefined : customTextColor
    };
    return Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "a",
      className: buttonClasses,
      href: url,
      title: title,
      style: buttonStyle,
      value: text,
      target: linkTarget,
      rel: rel
    }));
  }
}, {
  attributes: Object(objectSpread["a" /* default */])({}, button_deprecated_blockAttributes, {
    align: {
      type: 'string',
      default: 'none'
    },
    backgroundColor: {
      type: 'string'
    },
    textColor: {
      type: 'string'
    },
    customBackgroundColor: {
      type: 'string'
    },
    customTextColor: {
      type: 'string'
    }
  }),
  save: function save(_ref2) {
    var _classnames2;

    var attributes = _ref2.attributes;
    var url = attributes.url,
        text = attributes.text,
        title = attributes.title,
        backgroundColor = attributes.backgroundColor,
        textColor = attributes.textColor,
        customBackgroundColor = attributes.customBackgroundColor,
        customTextColor = attributes.customTextColor;
    var textClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
    var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
    var buttonClasses = classnames_default()('wp-block-button__link', (_classnames2 = {
      'has-text-color': textColor || customTextColor
    }, Object(defineProperty["a" /* default */])(_classnames2, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames2, 'has-background', backgroundColor || customBackgroundColor), Object(defineProperty["a" /* default */])(_classnames2, backgroundClass, backgroundClass), _classnames2));
    var buttonStyle = {
      backgroundColor: backgroundClass ? undefined : customBackgroundColor,
      color: textClass ? undefined : customTextColor
    };
    return Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "a",
      className: buttonClasses,
      href: url,
      title: title,
      style: buttonStyle,
      value: text
    }));
  },
  migrate: deprecated_colorsMigration
}, {
  attributes: Object(objectSpread["a" /* default */])({}, button_deprecated_blockAttributes, {
    color: {
      type: 'string'
    },
    textColor: {
      type: 'string'
    },
    align: {
      type: 'string',
      default: 'none'
    }
  }),
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    var url = attributes.url,
        text = attributes.text,
        title = attributes.title,
        align = attributes.align,
        color = attributes.color,
        textColor = attributes.textColor;
    var buttonStyle = {
      backgroundColor: color,
      color: textColor
    };
    var linkClass = 'wp-block-button__link';
    return Object(external_this_wp_element_["createElement"])("div", {
      className: "align".concat(align)
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "a",
      className: linkClass,
      href: url,
      title: title,
      style: buttonStyle,
      value: text
    }));
  },
  migrate: deprecated_colorsMigration
}, {
  attributes: Object(objectSpread["a" /* default */])({}, button_deprecated_blockAttributes, {
    color: {
      type: 'string'
    },
    textColor: {
      type: 'string'
    },
    align: {
      type: 'string',
      default: 'none'
    }
  }),
  save: function save(_ref4) {
    var attributes = _ref4.attributes;
    var url = attributes.url,
        text = attributes.text,
        title = attributes.title,
        align = attributes.align,
        color = attributes.color,
        textColor = attributes.textColor;
    return Object(external_this_wp_element_["createElement"])("div", {
      className: "align".concat(align),
      style: {
        backgroundColor: color
      }
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "a",
      href: url,
      title: title,
      style: {
        color: textColor
      },
      value: text
    }));
  },
  migrate: deprecated_colorsMigration
}];
/* harmony default export */ var button_deprecated = (button_deprecated_deprecated);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/button/edit.js









/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






var edit_window = window,
    edit_getComputedStyle = edit_window.getComputedStyle;
var edit_applyFallbackStyles = Object(external_this_wp_components_["withFallbackStyles"])(function (node, ownProps) {
  var textColor = ownProps.textColor,
      backgroundColor = ownProps.backgroundColor;
  var backgroundColorValue = backgroundColor && backgroundColor.color;
  var textColorValue = textColor && textColor.color; //avoid the use of querySelector if textColor color is known and verify if node is available.

  var textNode = !textColorValue && node ? node.querySelector('[contenteditable="true"]') : null;
  return {
    fallbackBackgroundColor: backgroundColorValue || !node ? undefined : edit_getComputedStyle(node).backgroundColor,
    fallbackTextColor: textColorValue || !textNode ? undefined : edit_getComputedStyle(textNode).color
  };
});
var edit_NEW_TAB_REL = 'noreferrer noopener';
var MIN_BORDER_RADIUS_VALUE = 0;
var MAX_BORDER_RADIUS_VALUE = 50;
var INITIAL_BORDER_RADIUS_POSITION = 5;

function BorderPanel(_ref) {
  var _ref$borderRadius = _ref.borderRadius,
      borderRadius = _ref$borderRadius === void 0 ? '' : _ref$borderRadius,
      setAttributes = _ref.setAttributes;
  var setBorderRadius = Object(external_this_wp_element_["useCallback"])(function (newBorderRadius) {
    setAttributes({
      borderRadius: newBorderRadius
    });
  }, [setAttributes]);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Border Settings')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
    value: borderRadius,
    label: Object(external_this_wp_i18n_["__"])('Border Radius'),
    min: MIN_BORDER_RADIUS_VALUE,
    max: MAX_BORDER_RADIUS_VALUE,
    initialPosition: INITIAL_BORDER_RADIUS_POSITION,
    allowReset: true,
    onChange: setBorderRadius
  }));
}

var edit_ButtonEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ButtonEdit, _Component);

  function ButtonEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, ButtonEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ButtonEdit).apply(this, arguments));
    _this.nodeRef = null;
    _this.bindRef = _this.bindRef.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSetLinkRel = _this.onSetLinkRel.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onToggleOpenInNewTab = _this.onToggleOpenInNewTab.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(ButtonEdit, [{
    key: "bindRef",
    value: function bindRef(node) {
      if (!node) {
        return;
      }

      this.nodeRef = node;
    }
  }, {
    key: "onSetLinkRel",
    value: function onSetLinkRel(value) {
      this.props.setAttributes({
        rel: value
      });
    }
  }, {
    key: "onToggleOpenInNewTab",
    value: function onToggleOpenInNewTab(value) {
      var rel = this.props.attributes.rel;
      var linkTarget = value ? '_blank' : undefined;
      var updatedRel = rel;

      if (linkTarget && !rel) {
        updatedRel = edit_NEW_TAB_REL;
      } else if (!linkTarget && rel === edit_NEW_TAB_REL) {
        updatedRel = undefined;
      }

      this.props.setAttributes({
        linkTarget: linkTarget,
        rel: updatedRel
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _classnames;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          backgroundColor = _this$props.backgroundColor,
          textColor = _this$props.textColor,
          setBackgroundColor = _this$props.setBackgroundColor,
          setTextColor = _this$props.setTextColor,
          fallbackBackgroundColor = _this$props.fallbackBackgroundColor,
          fallbackTextColor = _this$props.fallbackTextColor,
          setAttributes = _this$props.setAttributes,
          className = _this$props.className,
          instanceId = _this$props.instanceId,
          isSelected = _this$props.isSelected;
      var borderRadius = attributes.borderRadius,
          linkTarget = attributes.linkTarget,
          placeholder = attributes.placeholder,
          rel = attributes.rel,
          text = attributes.text,
          title = attributes.title,
          url = attributes.url;
      var linkId = "wp-block-button__inline-link-".concat(instanceId);
      return Object(external_this_wp_element_["createElement"])("div", {
        className: className,
        title: title,
        ref: this.bindRef
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        placeholder: placeholder || Object(external_this_wp_i18n_["__"])('Add text…'),
        value: text,
        onChange: function onChange(value) {
          return setAttributes({
            text: value
          });
        },
        withoutInteractiveFormatting: true,
        className: classnames_default()('wp-block-button__link', (_classnames = {
          'has-background': backgroundColor.color
        }, Object(defineProperty["a" /* default */])(_classnames, backgroundColor.class, backgroundColor.class), Object(defineProperty["a" /* default */])(_classnames, 'has-text-color', textColor.color), Object(defineProperty["a" /* default */])(_classnames, textColor.class, textColor.class), Object(defineProperty["a" /* default */])(_classnames, 'no-border-radius', borderRadius === 0), _classnames)),
        style: {
          backgroundColor: backgroundColor.color,
          color: textColor.color,
          borderRadius: borderRadius ? borderRadius + 'px' : undefined
        }
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["BaseControl"], {
        label: Object(external_this_wp_i18n_["__"])('Link'),
        className: "wp-block-button__inline-link",
        id: linkId
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["URLInput"], {
        className: "wp-block-button__inline-link-input",
        value: url
        /* eslint-disable jsx-a11y/no-autofocus */
        // Disable Reason: The rule is meant to prevent enabling auto-focus, not disabling it.
        ,
        autoFocus: false
        /* eslint-enable jsx-a11y/no-autofocus */
        ,
        onChange: function onChange(value) {
          return setAttributes({
            url: value
          });
        },
        disableSuggestions: !isSelected,
        id: linkId,
        isFullWidth: true,
        hasBorder: true
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
        title: Object(external_this_wp_i18n_["__"])('Color Settings'),
        colorSettings: [{
          value: backgroundColor.color,
          onChange: setBackgroundColor,
          label: Object(external_this_wp_i18n_["__"])('Background Color')
        }, {
          value: textColor.color,
          onChange: setTextColor,
          label: Object(external_this_wp_i18n_["__"])('Text Color')
        }]
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["ContrastChecker"], {
        // Text is considered large if font size is greater or equal to 18pt or 24px,
        // currently that's not the case for button.
        isLargeText: false,
        textColor: textColor.color,
        backgroundColor: backgroundColor.color,
        fallbackBackgroundColor: fallbackBackgroundColor,
        fallbackTextColor: fallbackTextColor
      })), Object(external_this_wp_element_["createElement"])(BorderPanel, {
        borderRadius: borderRadius,
        setAttributes: setAttributes
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Link settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Open in new tab'),
        onChange: this.onToggleOpenInNewTab,
        checked: linkTarget === '_blank'
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
        label: Object(external_this_wp_i18n_["__"])('Link rel'),
        value: rel || '',
        onChange: this.onSetLinkRel
      }))));
    }
  }]);

  return ButtonEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var button_edit = (Object(external_this_wp_compose_["compose"])([external_this_wp_compose_["withInstanceId"], Object(external_this_wp_blockEditor_["withColors"])('backgroundColor', {
  textColor: 'color'
}), edit_applyFallbackStyles])(edit_ButtonEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/button/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var button_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M19 6H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 10H5V8h14v8z"
}))));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/button/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function button_save_save(_ref) {
  var _classnames;

  var attributes = _ref.attributes;
  var backgroundColor = attributes.backgroundColor,
      borderRadius = attributes.borderRadius,
      customBackgroundColor = attributes.customBackgroundColor,
      customTextColor = attributes.customTextColor,
      linkTarget = attributes.linkTarget,
      rel = attributes.rel,
      text = attributes.text,
      textColor = attributes.textColor,
      title = attributes.title,
      url = attributes.url;
  var textClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
  var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
  var buttonClasses = classnames_default()('wp-block-button__link', (_classnames = {
    'has-text-color': textColor || customTextColor
  }, Object(defineProperty["a" /* default */])(_classnames, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames, 'has-background', backgroundColor || customBackgroundColor), Object(defineProperty["a" /* default */])(_classnames, backgroundClass, backgroundClass), Object(defineProperty["a" /* default */])(_classnames, 'no-border-radius', borderRadius === 0), _classnames));
  var buttonStyle = {
    backgroundColor: backgroundClass ? undefined : customBackgroundColor,
    color: textClass ? undefined : customTextColor,
    borderRadius: borderRadius ? borderRadius + 'px' : undefined
  };
  return Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "a",
    className: buttonClasses,
    href: url,
    title: title,
    style: buttonStyle,
    value: text,
    target: linkTarget,
    rel: rel
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/button/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var button_metadata = {
  name: "core/button",
  category: "layout",
  attributes: {
    url: {
      type: "string",
      source: "attribute",
      selector: "a",
      attribute: "href"
    },
    title: {
      type: "string",
      source: "attribute",
      selector: "a",
      attribute: "title"
    },
    text: {
      type: "string",
      source: "html",
      selector: "a"
    },
    backgroundColor: {
      type: "string"
    },
    textColor: {
      type: "string"
    },
    customBackgroundColor: {
      type: "string"
    },
    customTextColor: {
      type: "string"
    },
    linkTarget: {
      type: "string",
      source: "attribute",
      selector: "a",
      attribute: "target"
    },
    rel: {
      type: "string",
      source: "attribute",
      selector: "a",
      attribute: "rel"
    },
    placeholder: {
      type: "string"
    },
    borderRadius: {
      type: "number"
    }
  }
};

var button_name = button_metadata.name;

var button_settings = {
  title: Object(external_this_wp_i18n_["__"])('Button'),
  description: Object(external_this_wp_i18n_["__"])('Prompt visitors to take action with a button-style link.'),
  icon: button_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('link')],
  example: {
    attributes: {
      className: 'is-style-fill',
      backgroundColor: 'vivid-green-cyan',
      text: Object(external_this_wp_i18n_["__"])('Call to Action')
    }
  },
  supports: {
    align: true,
    alignWide: false
  },
  styles: [{
    name: 'fill',
    label: Object(external_this_wp_i18n_["__"])('Fill'),
    isDefault: true
  }, {
    name: 'outline',
    label: Object(external_this_wp_i18n_["__"])('Outline')
  }],
  edit: button_edit,
  save: button_save_save,
  deprecated: button_deprecated
};

// EXTERNAL MODULE: external "moment"
var external_moment_ = __webpack_require__(29);
var external_moment_default = /*#__PURE__*/__webpack_require__.n(external_moment_);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/calendar/edit.js









/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






var edit_CalendarEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(CalendarEdit, _Component);

  function CalendarEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, CalendarEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(CalendarEdit).apply(this, arguments));
    _this.getYearMonth = memize_default()(_this.getYearMonth.bind(Object(assertThisInitialized["a" /* default */])(_this)), {
      maxSize: 1
    });
    _this.getServerSideAttributes = memize_default()(_this.getServerSideAttributes.bind(Object(assertThisInitialized["a" /* default */])(_this)), {
      maxSize: 1
    });
    return _this;
  }

  Object(createClass["a" /* default */])(CalendarEdit, [{
    key: "getYearMonth",
    value: function getYearMonth(date) {
      if (!date) {
        return {};
      }

      var momentDate = external_moment_default()(date);
      return {
        year: momentDate.year(),
        month: momentDate.month() + 1
      };
    }
  }, {
    key: "getServerSideAttributes",
    value: function getServerSideAttributes(attributes, date) {
      return Object(objectSpread["a" /* default */])({}, attributes, this.getYearMonth(date));
    }
  }, {
    key: "render",
    value: function render() {
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_serverSideRender_default.a, {
        block: "core/calendar",
        attributes: this.getServerSideAttributes(this.props.attributes, this.props.date)
      }));
    }
  }]);

  return CalendarEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var calendar_edit = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var coreEditorSelect = select('core/editor');

  if (!coreEditorSelect) {
    return;
  }

  var getEditedPostAttribute = coreEditorSelect.getEditedPostAttribute;
  var postType = getEditedPostAttribute('type'); // Dates are used to overwrite year and month used on the calendar.
  // This overwrite should only happen for 'post' post types.
  // For other post types the calendar always displays the current month.

  return {
    date: postType === 'post' ? getEditedPostAttribute('date') : undefined
  };
})(edit_CalendarEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/calendar/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var calendar_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M7 11h2v2H7v-2zm14-5v14c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2l.01-14c0-1.1.88-2 1.99-2h1V2h2v2h8V2h2v2h1c1.1 0 2 .9 2 2zM5 8h14V6H5v2zm14 12V10H5v10h14zm-4-7h2v-2h-2v2zm-4 0h2v-2h-2v2z"
}))));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/calendar/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var calendar_name = 'core/calendar';
var calendar_settings = {
  title: Object(external_this_wp_i18n_["__"])('Calendar'),
  description: Object(external_this_wp_i18n_["__"])('A calendar of your site’s posts.'),
  icon: calendar_icon,
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('posts'), Object(external_this_wp_i18n_["__"])('archive')],
  supports: {
    align: true
  },
  example: {},
  edit: calendar_edit
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/categories/edit.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








var edit_CategoriesEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(CategoriesEdit, _Component);

  function CategoriesEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, CategoriesEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(CategoriesEdit).apply(this, arguments));
    _this.toggleDisplayAsDropdown = _this.toggleDisplayAsDropdown.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.toggleShowPostCounts = _this.toggleShowPostCounts.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.toggleShowHierarchy = _this.toggleShowHierarchy.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(CategoriesEdit, [{
    key: "toggleDisplayAsDropdown",
    value: function toggleDisplayAsDropdown() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      var displayAsDropdown = attributes.displayAsDropdown;
      setAttributes({
        displayAsDropdown: !displayAsDropdown
      });
    }
  }, {
    key: "toggleShowPostCounts",
    value: function toggleShowPostCounts() {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      var showPostCounts = attributes.showPostCounts;
      setAttributes({
        showPostCounts: !showPostCounts
      });
    }
  }, {
    key: "toggleShowHierarchy",
    value: function toggleShowHierarchy() {
      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          setAttributes = _this$props3.setAttributes;
      var showHierarchy = attributes.showHierarchy;
      setAttributes({
        showHierarchy: !showHierarchy
      });
    }
  }, {
    key: "getCategories",
    value: function getCategories() {
      var parentId = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var categories = this.props.categories;

      if (!categories || !categories.length) {
        return [];
      }

      if (parentId === null) {
        return categories;
      }

      return categories.filter(function (category) {
        return category.parent === parentId;
      });
    }
  }, {
    key: "getCategoryListClassName",
    value: function getCategoryListClassName(level) {
      return "wp-block-categories__list wp-block-categories__list-level-".concat(level);
    }
  }, {
    key: "renderCategoryName",
    value: function renderCategoryName(category) {
      if (!category.name) {
        return Object(external_this_wp_i18n_["__"])('(Untitled)');
      }

      return Object(external_lodash_["unescape"])(category.name).trim();
    }
  }, {
    key: "renderCategoryList",
    value: function renderCategoryList() {
      var _this2 = this;

      var showHierarchy = this.props.attributes.showHierarchy;
      var parentId = showHierarchy ? 0 : null;
      var categories = this.getCategories(parentId);
      return Object(external_this_wp_element_["createElement"])("ul", {
        className: this.getCategoryListClassName(0)
      }, categories.map(function (category) {
        return _this2.renderCategoryListItem(category, 0);
      }));
    }
  }, {
    key: "renderCategoryListItem",
    value: function renderCategoryListItem(category, level) {
      var _this3 = this;

      var _this$props$attribute = this.props.attributes,
          showHierarchy = _this$props$attribute.showHierarchy,
          showPostCounts = _this$props$attribute.showPostCounts;
      var childCategories = this.getCategories(category.id);
      return Object(external_this_wp_element_["createElement"])("li", {
        key: category.id
      }, Object(external_this_wp_element_["createElement"])("a", {
        href: category.link,
        target: "_blank",
        rel: "noreferrer noopener"
      }, this.renderCategoryName(category)), showPostCounts && Object(external_this_wp_element_["createElement"])("span", {
        className: "wp-block-categories__post-count"
      }, ' ', "(", category.count, ")"), showHierarchy && !!childCategories.length && Object(external_this_wp_element_["createElement"])("ul", {
        className: this.getCategoryListClassName(level + 1)
      }, childCategories.map(function (childCategory) {
        return _this3.renderCategoryListItem(childCategory, level + 1);
      })));
    }
  }, {
    key: "renderCategoryDropdown",
    value: function renderCategoryDropdown() {
      var _this4 = this;

      var instanceId = this.props.instanceId;
      var showHierarchy = this.props.attributes.showHierarchy;
      var parentId = showHierarchy ? 0 : null;
      var categories = this.getCategories(parentId);
      var selectId = "blocks-category-select-".concat(instanceId);
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("label", {
        htmlFor: selectId,
        className: "screen-reader-text"
      }, Object(external_this_wp_i18n_["__"])('Categories')), Object(external_this_wp_element_["createElement"])("select", {
        id: selectId,
        className: "wp-block-categories__dropdown"
      }, categories.map(function (category) {
        return _this4.renderCategoryDropdownItem(category, 0);
      })));
    }
  }, {
    key: "renderCategoryDropdownItem",
    value: function renderCategoryDropdownItem(category, level) {
      var _this5 = this;

      var _this$props$attribute2 = this.props.attributes,
          showHierarchy = _this$props$attribute2.showHierarchy,
          showPostCounts = _this$props$attribute2.showPostCounts;
      var childCategories = this.getCategories(category.id);
      return [Object(external_this_wp_element_["createElement"])("option", {
        key: category.id
      }, Object(external_lodash_["times"])(level * 3, function () {
        return '\xa0';
      }), this.renderCategoryName(category), !!showPostCounts ? " (".concat(category.count, ")") : ''), showHierarchy && !!childCategories.length && childCategories.map(function (childCategory) {
        return _this5.renderCategoryDropdownItem(childCategory, level + 1);
      })];
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props4 = this.props,
          attributes = _this$props4.attributes,
          isRequesting = _this$props4.isRequesting;
      var displayAsDropdown = attributes.displayAsDropdown,
          showHierarchy = attributes.showHierarchy,
          showPostCounts = attributes.showPostCounts;
      var inspectorControls = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Categories Settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Display as Dropdown'),
        checked: displayAsDropdown,
        onChange: this.toggleDisplayAsDropdown
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Show Hierarchy'),
        checked: showHierarchy,
        onChange: this.toggleShowHierarchy
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Show Post Counts'),
        checked: showPostCounts,
        onChange: this.toggleShowPostCounts
      })));

      if (isRequesting) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, inspectorControls, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], {
          icon: "admin-post",
          label: Object(external_this_wp_i18n_["__"])('Categories')
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null)));
      }

      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, inspectorControls, Object(external_this_wp_element_["createElement"])("div", {
        className: this.props.className
      }, displayAsDropdown ? this.renderCategoryDropdown() : this.renderCategoryList()));
    }
  }]);

  return CategoriesEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var categories_edit = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core'),
      getEntityRecords = _select.getEntityRecords;

  var _select2 = select('core/data'),
      isResolving = _select2.isResolving;

  var query = {
    per_page: -1,
    hide_empty: true
  };
  return {
    categories: getEntityRecords('taxonomy', 'category', query),
    isRequesting: isResolving('core', 'getEntityRecords', ['taxonomy', 'category', query])
  };
}), external_this_wp_compose_["withInstanceId"])(edit_CategoriesEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/categories/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var categories_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M12,2l-5.5,9h11L12,2z M12,5.84L13.93,9h-3.87L12,5.84z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m3 21.5h8v-8h-8v8zm2-6h4v4h-4v-4z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/categories/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var categories_name = 'core/categories';
var categories_settings = {
  title: Object(external_this_wp_i18n_["__"])('Categories'),
  description: Object(external_this_wp_i18n_["__"])('Display a list of all categories.'),
  icon: categories_icon,
  category: 'widgets',
  supports: {
    align: true,
    html: false
  },
  edit: categories_edit
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/code/utils.js
/**
 * External dependencies
 */

/**
 * Escapes ampersands, shortcodes, and links.
 *
 * @param {string} content The content of a code block.
 * @return {string} The given content with some characters escaped.
 */

function utils_escape(content) {
  return Object(external_lodash_["flow"])(escapeAmpersands, escapeOpeningSquareBrackets, escapeProtocolInIsolatedUrls)(content || '');
}
/**
 * Unescapes escaped ampersands, shortcodes, and links.
 *
 * @param {string} content Content with (maybe) escaped ampersands, shortcodes, and links.
 * @return {string} The given content with escaped characters unescaped.
 */

function utils_unescape(content) {
  return Object(external_lodash_["flow"])(unescapeProtocolInIsolatedUrls, unescapeOpeningSquareBrackets, unescapeAmpersands)(content || '');
}
/**
 * Returns the given content with all its ampersand characters converted
 * into their HTML entity counterpart (i.e. & => &amp;)
 *
 * @param {string}  content The content of a code block.
 * @return {string} The given content with its ampersands converted into
 *                  their HTML entity counterpart (i.e. & => &amp;)
 */

function escapeAmpersands(content) {
  return content.replace(/&/g, '&amp;');
}
/**
 * Returns the given content with all &amp; HTML entities converted into &.
 *
 * @param {string}  content The content of a code block.
 * @return {string} The given content with all &amp; HTML entities
 *                  converted into &.
 */


function unescapeAmpersands(content) {
  return content.replace(/&amp;/g, '&');
}
/**
 * Returns the given content with all opening shortcode characters converted
 * into their HTML entity counterpart (i.e. [ => &#91;). For instance, a
 * shortcode like [embed] becomes &#91;embed]
 *
 * This function replicates the escaping of HTML tags, where a tag like
 * <strong> becomes &lt;strong>.
 *
 * @param {string}  content The content of a code block.
 * @return {string} The given content with its opening shortcode characters
 *                  converted into their HTML entity counterpart
 *                  (i.e. [ => &#91;)
 */


function escapeOpeningSquareBrackets(content) {
  return content.replace(/\[/g, '&#91;');
}
/**
 * Returns the given content translating all &#91; into [.
 *
 * @param {string}  content The content of a code block.
 * @return {string} The given content with all &#91; into [.
 */


function unescapeOpeningSquareBrackets(content) {
  return content.replace(/&#91;/g, '[');
}
/**
 * Converts the first two forward slashes of any isolated URL into their HTML
 * counterparts (i.e. // => &#47;&#47;). For instance, https://youtube.com/watch?x
 * becomes https:&#47;&#47;youtube.com/watch?x.
 *
 * An isolated URL is a URL that sits in its own line, surrounded only by spacing
 * characters.
 *
 * See https://github.com/WordPress/wordpress-develop/blob/5.1.1/src/wp-includes/class-wp-embed.php#L403
 *
 * @param {string}  content The content of a code block.
 * @return {string} The given content with its ampersands converted into
 *                  their HTML entity counterpart (i.e. & => &amp;)
 */


function escapeProtocolInIsolatedUrls(content) {
  return content.replace(/^(\s*https?:)\/\/([^\s<>"]+\s*)$/m, '$1&#47;&#47;$2');
}
/**
 * Converts the first two forward slashes of any isolated URL from the HTML entity
 * &#73; into /.
 *
 * An isolated URL is a URL that sits in its own line, surrounded only by spacing
 * characters.
 *
 * See https://github.com/WordPress/wordpress-develop/blob/5.1.1/src/wp-includes/class-wp-embed.php#L403
 *
 * @param {string}  content The content of a code block.
 * @return {string} The given content with the first two forward slashes of any
 *                  isolated URL from the HTML entity &#73; into /.
 */


function unescapeProtocolInIsolatedUrls(content) {
  return content.replace(/^(\s*https?:)&#47;&#47;([^\s<>"]+\s*)$/m, '$1//$2');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/code/edit.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function CodeEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      className = _ref.className;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: className
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PlainText"], {
    value: utils_unescape(attributes.content),
    onChange: function onChange(content) {
      return setAttributes({
        content: utils_escape(content)
      });
    },
    placeholder: Object(external_this_wp_i18n_["__"])('Write code…'),
    "aria-label": Object(external_this_wp_i18n_["__"])('Code')
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/code/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var code_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M9.4,16.6L4.8,12l4.6-4.6L8,6l-6,6l6,6L9.4,16.6z M14.6,16.6l4.6-4.6l-4.6-4.6L16,6l6,6l-6,6L14.6,16.6z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/code/save.js

function code_save_save(_ref) {
  var attributes = _ref.attributes;
  return Object(external_this_wp_element_["createElement"])("pre", null, Object(external_this_wp_element_["createElement"])("code", null, attributes.content));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/code/transforms.js
/**
 * WordPress dependencies
 */

var code_transforms_transforms = {
  from: [{
    type: 'enter',
    regExp: /^```$/,
    transform: function transform() {
      return Object(external_this_wp_blocks_["createBlock"])('core/code');
    }
  }, {
    type: 'raw',
    isMatch: function isMatch(node) {
      return node.nodeName === 'PRE' && node.children.length === 1 && node.firstChild.nodeName === 'CODE';
    },
    schema: {
      pre: {
        children: {
          code: {
            children: {
              '#text': {}
            }
          }
        }
      }
    }
  }]
};
/* harmony default export */ var code_transforms = (code_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/code/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var code_metadata = {
  name: "core/code",
  category: "formatting",
  attributes: {
    content: {
      type: "string",
      source: "text",
      selector: "code"
    }
  }
};


var code_name = code_metadata.name;

var code_settings = {
  title: Object(external_this_wp_i18n_["__"])('Code'),
  description: Object(external_this_wp_i18n_["__"])('Display code snippets that respect your spacing and tabs.'),
  icon: code_icon,
  example: {
    attributes: {
      content: Object(external_this_wp_i18n_["__"])('// A "block" is the abstract term used') + '\n' + Object(external_this_wp_i18n_["__"])('// to describe units of markup that,') + '\n' + Object(external_this_wp_i18n_["__"])('// when composed together, form the') + '\n' + Object(external_this_wp_i18n_["__"])('// content or layout of a page.') + '\n' + Object(external_this_wp_i18n_["__"])('registerBlockType( name, settings );')
    }
  },
  supports: {
    html: false
  },
  transforms: code_transforms,
  edit: CodeEdit,
  save: code_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/columns/deprecated.js



/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



/**
 * Given an HTML string for a deprecated columns inner block, returns the
 * column index to which the migrated inner block should be assigned. Returns
 * undefined if the inner block was not assigned to a column.
 *
 * @param {string} originalContent Deprecated Columns inner block HTML.
 *
 * @return {?number} Column to which inner block is to be assigned.
 */

function getDeprecatedLayoutColumn(originalContent) {
  var doc = getDeprecatedLayoutColumn.doc;

  if (!doc) {
    doc = document.implementation.createHTMLDocument('');
    getDeprecatedLayoutColumn.doc = doc;
  }

  var columnMatch;
  doc.body.innerHTML = originalContent;
  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = doc.body.firstChild.classList[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      var classListItem = _step.value;

      if (columnMatch = classListItem.match(/^layout-column-(\d+)$/)) {
        return Number(columnMatch[1]) - 1;
      }
    }
  } catch (err) {
    _didIteratorError = true;
    _iteratorError = err;
  } finally {
    try {
      if (!_iteratorNormalCompletion && _iterator.return != null) {
        _iterator.return();
      }
    } finally {
      if (_didIteratorError) {
        throw _iteratorError;
      }
    }
  }
}

/* harmony default export */ var columns_deprecated = ([{
  attributes: {
    columns: {
      type: 'number',
      default: 2
    }
  },
  isEligible: function isEligible(attributes, innerBlocks) {
    // Since isEligible is called on every valid instance of the
    // Columns block and a deprecation is the unlikely case due to
    // its subsequent migration, optimize for the `false` condition
    // by performing a naive, inaccurate pass at inner blocks.
    var isFastPassEligible = innerBlocks.some(function (innerBlock) {
      return /layout-column-\d+/.test(innerBlock.originalContent);
    });

    if (!isFastPassEligible) {
      return false;
    } // Only if the fast pass is considered eligible is the more
    // accurate, durable, slower condition performed.


    return innerBlocks.some(function (innerBlock) {
      return getDeprecatedLayoutColumn(innerBlock.originalContent) !== undefined;
    });
  },
  migrate: function migrate(attributes, innerBlocks) {
    var columns = innerBlocks.reduce(function (result, innerBlock) {
      var originalContent = innerBlock.originalContent;
      var columnIndex = getDeprecatedLayoutColumn(originalContent);

      if (columnIndex === undefined) {
        columnIndex = 0;
      }

      if (!result[columnIndex]) {
        result[columnIndex] = [];
      }

      result[columnIndex].push(innerBlock);
      return result;
    }, []);
    var migratedInnerBlocks = columns.map(function (columnBlocks) {
      return Object(external_this_wp_blocks_["createBlock"])('core/column', {}, columnBlocks);
    });
    return [Object(external_lodash_["omit"])(attributes, ['columns']), migratedInnerBlocks];
  },
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var columns = attributes.columns;
    return Object(external_this_wp_element_["createElement"])("div", {
      className: "has-".concat(columns, "-columns")
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].Content, null));
  }
}, {
  attributes: {
    columns: {
      type: 'number',
      default: 2
    }
  },
  migrate: function migrate(attributes, innerBlocks) {
    attributes = Object(external_lodash_["omit"])(attributes, ['columns']);
    return [attributes, innerBlocks];
  },
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    var verticalAlignment = attributes.verticalAlignment,
        columns = attributes.columns;
    var wrapperClasses = classnames_default()("has-".concat(columns, "-columns"), Object(defineProperty["a" /* default */])({}, "are-vertically-aligned-".concat(verticalAlignment), verticalAlignment));
    return Object(external_this_wp_element_["createElement"])("div", {
      className: wrapperClasses
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].Content, null));
  }
}]);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/columns/utils.js


/**
 * External dependencies
 */


/**
 * Returns the layouts configuration for a given number of columns.
 *
 * @param {number} columns Number of columns.
 *
 * @return {Object[]} Columns layout configuration.
 */

var getColumnsTemplate = memize_default()(function (columns) {
  if (columns === undefined) {
    return null;
  }

  return Object(external_lodash_["times"])(columns, function () {
    return ['core/column'];
  });
});
/**
 * Returns a column width attribute value rounded to standard precision.
 * Returns `undefined` if the value is not a valid finite number.
 *
 * @param {?number} value Raw value.
 *
 * @return {number} Value rounded to standard precision.
 */

var toWidthPrecision = function toWidthPrecision(value) {
  return Number.isFinite(value) ? parseFloat(value.toFixed(2)) : undefined;
};
/**
 * Returns the considered adjacent to that of the specified `clientId` for
 * resizing consideration. Adjacent blocks are those occurring after, except
 * when the given block is the last block in the set. For the last block, the
 * behavior is reversed.
 *
 * @param {WPBlock[]} blocks   Block objects.
 * @param {string}    clientId Client ID to consider for adjacent blocks.
 *
 * @return {WPBlock[]} Adjacent block objects.
 */

function getAdjacentBlocks(blocks, clientId) {
  var index = Object(external_lodash_["findIndex"])(blocks, {
    clientId: clientId
  });
  var isLastBlock = index === blocks.length - 1;
  return isLastBlock ? blocks.slice(0, index) : blocks.slice(index + 1);
}
/**
 * Returns an effective width for a given block. An effective width is equal to
 * its attribute value if set, or a computed value assuming equal distribution.
 *
 * @param {WPBlock} block           Block object.
 * @param {number}  totalBlockCount Total number of blocks in Columns.
 *
 * @return {number} Effective column width.
 */

function getEffectiveColumnWidth(block, totalBlockCount) {
  var _block$attributes$wid = block.attributes.width,
      width = _block$attributes$wid === void 0 ? 100 / totalBlockCount : _block$attributes$wid;
  return toWidthPrecision(width);
}
/**
 * Returns the total width occupied by the given set of column blocks.
 *
 * @param {WPBlock[]} blocks          Block objects.
 * @param {?number}   totalBlockCount Total number of blocks in Columns.
 *                                    Defaults to number of blocks passed.
 *
 * @return {number} Total width occupied by blocks.
 */

function getTotalColumnsWidth(blocks) {
  var totalBlockCount = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : blocks.length;
  return Object(external_lodash_["sumBy"])(blocks, function (block) {
    return getEffectiveColumnWidth(block, totalBlockCount);
  });
}
/**
 * Returns an object of `clientId` → `width` of effective column widths.
 *
 * @param {WPBlock[]} blocks          Block objects.
 * @param {?number}   totalBlockCount Total number of blocks in Columns.
 *                                    Defaults to number of blocks passed.
 *
 * @return {Object<string,number>} Column widths.
 */

function getColumnWidths(blocks) {
  var totalBlockCount = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : blocks.length;
  return blocks.reduce(function (result, block) {
    var width = getEffectiveColumnWidth(block, totalBlockCount);
    return Object.assign(result, Object(defineProperty["a" /* default */])({}, block.clientId, width));
  }, {});
}
/**
 * Returns an object of `clientId` → `width` of column widths as redistributed
 * proportional to their current widths, constrained or expanded to fit within
 * the given available width.
 *
 * @param {WPBlock[]} blocks          Block objects.
 * @param {number}    availableWidth  Maximum width to fit within.
 * @param {?number}   totalBlockCount Total number of blocks in Columns.
 *                                    Defaults to number of blocks passed.
 *
 * @return {Object<string,number>} Redistributed column widths.
 */

function getRedistributedColumnWidths(blocks, availableWidth) {
  var totalBlockCount = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : blocks.length;
  var totalWidth = getTotalColumnsWidth(blocks, totalBlockCount);
  var difference = availableWidth - totalWidth;
  var adjustment = difference / blocks.length;
  return Object(external_lodash_["mapValues"])(getColumnWidths(blocks, totalBlockCount), function (width) {
    return toWidthPrecision(width + adjustment);
  });
}
/**
 * Returns true if column blocks within the provided set are assigned with
 * explicit widths, or false otherwise.
 *
 * @param {WPBlock[]} blocks Block objects.
 *
 * @return {boolean} Whether columns have explicit widths.
 */

function hasExplicitColumnWidths(blocks) {
  return blocks.some(function (block) {
    return Number.isFinite(block.attributes.width);
  });
}
/**
 * Returns a copy of the given set of blocks with new widths assigned from the
 * provided object of redistributed column widths.
 *
 * @param {WPBlock[]}             blocks Block objects.
 * @param {Object<string,number>} widths Redistributed column widths.
 *
 * @return {WPBlock[]} blocks Mapped block objects.
 */

function getMappedColumnWidths(blocks, widths) {
  return blocks.map(function (block) {
    return Object(external_lodash_["merge"])({}, block, {
      attributes: {
        width: widths[block.clientId]
      }
    });
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/columns/edit.js





/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


/**
 * Allowed blocks constant is passed to InnerBlocks precisely as specified here.
 * The contents of the array should never change.
 * The array should contain the name of each block that is allowed.
 * In columns block, the only block we allow is 'core/column'.
 *
 * @constant
 * @type {string[]}
 */

var ALLOWED_BLOCKS = ['core/column'];
/**
 * Template option choices for predefined columns layouts.
 *
 * @constant
 * @type {Array}
 */

var TEMPLATE_OPTIONS = [{
  title: Object(external_this_wp_i18n_["__"])('Two columns; equal split'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    width: "48",
    height: "48",
    viewBox: "0 0 48 48",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
  })),
  template: [['core/column'], ['core/column']]
}, {
  title: Object(external_this_wp_i18n_["__"])('Two columns; one-third, two-thirds split'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    width: "48",
    height: "48",
    viewBox: "0 0 48 48",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H20V34H39ZM18 34H9V14H18V34Z"
  })),
  template: [['core/column', {
    width: 33.33
  }], ['core/column', {
    width: 66.66
  }]]
}, {
  title: Object(external_this_wp_i18n_["__"])('Two columns; two-thirds, one-third split'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    width: "48",
    height: "48",
    viewBox: "0 0 48 48",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H30V34H39ZM28 34H9V14H28V34Z"
  })),
  template: [['core/column', {
    width: 66.66
  }], ['core/column', {
    width: 33.33
  }]]
}, {
  title: Object(external_this_wp_i18n_["__"])('Three columns; equal split'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    width: "48",
    height: "48",
    viewBox: "0 0 48 48",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fillRule: "evenodd",
    d: "M41 14a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v20a2 2 0 0 0 2 2h30a2 2 0 0 0 2-2V14zM28.5 34h-9V14h9v20zm2 0V14H39v20h-8.5zm-13 0H9V14h8.5v20z"
  })),
  template: [['core/column'], ['core/column'], ['core/column']]
}, {
  title: Object(external_this_wp_i18n_["__"])('Three columns; wide center column'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    width: "48",
    height: "48",
    viewBox: "0 0 48 48",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fillRule: "evenodd",
    d: "M41 14a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v20a2 2 0 0 0 2 2h30a2 2 0 0 0 2-2V14zM31 34H17V14h14v20zm2 0V14h6v20h-6zm-18 0H9V14h6v20z"
  })),
  template: [['core/column', {
    width: 25
  }], ['core/column', {
    width: 50
  }], ['core/column', {
    width: 25
  }]]
}];
/**
 * Number of columns to assume for template in case the user opts to skip
 * template option selection.
 *
 * @type {number}
 */

var DEFAULT_COLUMNS = 2;
function ColumnsEdit(_ref) {
  var attributes = _ref.attributes,
      className = _ref.className,
      updateAlignment = _ref.updateAlignment,
      updateColumns = _ref.updateColumns,
      clientId = _ref.clientId;
  var verticalAlignment = attributes.verticalAlignment;

  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      count: select('core/block-editor').getBlockCount(clientId)
    };
  }),
      count = _useSelect.count;

  var _useState = Object(external_this_wp_element_["useState"])(getColumnsTemplate(count)),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      template = _useState2[0],
      setTemplate = _useState2[1];

  var _useState3 = Object(external_this_wp_element_["useState"])(false),
      _useState4 = Object(slicedToArray["a" /* default */])(_useState3, 2),
      forceUseTemplate = _useState4[0],
      setForceUseTemplate = _useState4[1]; // This is used to force the usage of the template even if the count doesn't match the template
  // The count doesn't match the template once you use undo/redo (this is used to reset to the placeholder state).


  Object(external_this_wp_element_["useEffect"])(function () {
    // Once the template is applied, reset it.
    if (forceUseTemplate) {
      setForceUseTemplate(false);
    }
  }, [forceUseTemplate]);
  var classes = classnames_default()(className, Object(defineProperty["a" /* default */])({}, "are-vertically-aligned-".concat(verticalAlignment), verticalAlignment)); // The template selector is shown when we first insert the columns block (count === 0).
  // or if there's no template available.
  // The count === 0 trick is useful when you use undo/redo.

  var showTemplateSelector = count === 0 && !forceUseTemplate || !template;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, !showTemplateSelector && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
    label: Object(external_this_wp_i18n_["__"])('Columns'),
    value: count,
    onChange: function onChange(value) {
      return updateColumns(count, value);
    },
    min: 2,
    max: 6
  }))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockVerticalAlignmentToolbar"], {
    onChange: updateAlignment,
    value: verticalAlignment
  }))), Object(external_this_wp_element_["createElement"])("div", {
    className: classes
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"], {
    __experimentalTemplateOptions: TEMPLATE_OPTIONS,
    __experimentalOnSelectTemplateOption: function __experimentalOnSelectTemplateOption(nextTemplate) {
      if (nextTemplate === undefined) {
        nextTemplate = getColumnsTemplate(DEFAULT_COLUMNS);
      }

      setTemplate(nextTemplate);
      setForceUseTemplate(true);
    },
    __experimentalAllowTemplateOptionSkip: true,
    template: showTemplateSelector ? null : template,
    templateLock: "all",
    allowedBlocks: ALLOWED_BLOCKS
  })));
}
/* harmony default export */ var columns_edit = (Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps, registry) {
  return {
    /**
     * Update all child Column blocks with a new vertical alignment setting
     * based on whatever alignment is passed in. This allows change to parent
     * to overide anything set on a individual column basis.
     *
     * @param {string} verticalAlignment the vertical alignment setting
     */
    updateAlignment: function updateAlignment(verticalAlignment) {
      var clientId = ownProps.clientId,
          setAttributes = ownProps.setAttributes;

      var _dispatch = dispatch('core/block-editor'),
          updateBlockAttributes = _dispatch.updateBlockAttributes;

      var _registry$select = registry.select('core/block-editor'),
          getBlockOrder = _registry$select.getBlockOrder; // Update own alignment.


      setAttributes({
        verticalAlignment: verticalAlignment
      }); // Update all child Column Blocks to match

      var innerBlockClientIds = getBlockOrder(clientId);
      innerBlockClientIds.forEach(function (innerBlockClientId) {
        updateBlockAttributes(innerBlockClientId, {
          verticalAlignment: verticalAlignment
        });
      });
    },

    /**
     * Updates the column count, including necessary revisions to child Column
     * blocks to grant required or redistribute available space.
     *
     * @param {number} previousColumns Previous column count.
     * @param {number} newColumns      New column count.
     */
    updateColumns: function updateColumns(previousColumns, newColumns) {
      var clientId = ownProps.clientId;

      var _dispatch2 = dispatch('core/block-editor'),
          replaceInnerBlocks = _dispatch2.replaceInnerBlocks;

      var _registry$select2 = registry.select('core/block-editor'),
          getBlocks = _registry$select2.getBlocks;

      var innerBlocks = getBlocks(clientId);
      var hasExplicitWidths = hasExplicitColumnWidths(innerBlocks); // Redistribute available width for existing inner blocks.

      var isAddingColumn = newColumns > previousColumns;

      if (isAddingColumn && hasExplicitWidths) {
        // If adding a new column, assign width to the new column equal to
        // as if it were `1 / columns` of the total available space.
        var newColumnWidth = toWidthPrecision(100 / newColumns); // Redistribute in consideration of pending block insertion as
        // constraining the available working width.

        var widths = getRedistributedColumnWidths(innerBlocks, 100 - newColumnWidth);
        innerBlocks = [].concat(Object(toConsumableArray["a" /* default */])(getMappedColumnWidths(innerBlocks, widths)), Object(toConsumableArray["a" /* default */])(Object(external_lodash_["times"])(newColumns - previousColumns, function () {
          return Object(external_this_wp_blocks_["createBlock"])('core/column', {
            width: newColumnWidth
          });
        })));
      } else if (isAddingColumn) {
        innerBlocks = [].concat(Object(toConsumableArray["a" /* default */])(innerBlocks), Object(toConsumableArray["a" /* default */])(Object(external_lodash_["times"])(newColumns - previousColumns, function () {
          return Object(external_this_wp_blocks_["createBlock"])('core/column');
        })));
      } else {
        // The removed column will be the last of the inner blocks.
        innerBlocks = Object(external_lodash_["dropRight"])(innerBlocks, previousColumns - newColumns);

        if (hasExplicitWidths) {
          // Redistribute as if block is already removed.
          var _widths = getRedistributedColumnWidths(innerBlocks, 100);

          innerBlocks = getMappedColumnWidths(innerBlocks, _widths);
        }
      }

      replaceInnerBlocks(clientId, innerBlocks, false);
    }
  };
})(ColumnsEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/columns/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var columns_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M4,4H20a2,2,0,0,1,2,2V18a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V6A2,2,0,0,1,4,4ZM4 6V18H8V6Zm6 0V18h4V6Zm6 0V18h4V6Z"
}))));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/columns/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function columns_save_save(_ref) {
  var attributes = _ref.attributes;
  var verticalAlignment = attributes.verticalAlignment;
  var wrapperClasses = classnames_default()(Object(defineProperty["a" /* default */])({}, "are-vertically-aligned-".concat(verticalAlignment), verticalAlignment));
  return Object(external_this_wp_element_["createElement"])("div", {
    className: wrapperClasses
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].Content, null));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/columns/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var columns_metadata = {
  name: "core/columns",
  category: "layout",
  attributes: {
    verticalAlignment: {
      type: "string"
    }
  }
};

var columns_name = columns_metadata.name;

var columns_settings = {
  title: Object(external_this_wp_i18n_["__"])('Columns'),
  icon: columns_icon,
  description: Object(external_this_wp_i18n_["__"])('Add a block that displays content in multiple columns, then add whatever content blocks you’d like.'),
  supports: {
    align: ['wide', 'full'],
    html: false
  },
  example: {
    innerBlocks: [{
      name: 'core/column',
      innerBlocks: [{
        name: 'core/paragraph',
        attributes: {
          content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent et eros eu felis.'
        }
      }, {
        name: 'core/image',
        attributes: {
          url: 'https://s.w.org/images/core/5.3/Windbuchencom.jpg'
        }
      }, {
        name: 'core/paragraph',
        attributes: {
          content: 'Suspendisse commodo neque lacus, a dictum orci interdum et.'
        }
      }]
    }, {
      name: 'core/column',
      innerBlocks: [{
        name: 'core/paragraph',
        attributes: {
          content: Object(external_this_wp_i18n_["__"])('Etiam et egestas lorem. Vivamus sagittis sit amet dolor quis lobortis. Integer sed fermentum arcu, id vulputate lacus. Etiam fermentum sem eu quam hendrerit.')
        }
      }, {
        name: 'core/paragraph',
        attributes: {
          content: Object(external_this_wp_i18n_["__"])('Nam risus massa, ullamcorper consectetur eros fermentum, porta aliquet ligula. Sed vel mauris nec enim.')
        }
      }]
    }]
  },
  deprecated: columns_deprecated,
  edit: columns_edit,
  save: columns_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/column/edit.js





/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function ColumnEdit(_ref) {
  var attributes = _ref.attributes,
      className = _ref.className,
      updateAlignment = _ref.updateAlignment,
      updateWidth = _ref.updateWidth,
      hasChildBlocks = _ref.hasChildBlocks;
  var verticalAlignment = attributes.verticalAlignment,
      width = attributes.width;
  var classes = classnames_default()(className, 'block-core-columns', Object(defineProperty["a" /* default */])({}, "is-vertically-aligned-".concat(verticalAlignment), verticalAlignment));
  return Object(external_this_wp_element_["createElement"])("div", {
    className: classes
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockVerticalAlignmentToolbar"], {
    onChange: updateAlignment,
    value: verticalAlignment
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Column Settings')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
    label: Object(external_this_wp_i18n_["__"])('Percentage width'),
    value: width || '',
    onChange: updateWidth,
    min: 0,
    max: 100,
    required: true,
    allowReset: true
  }))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"], {
    templateLock: false,
    renderAppender: hasChildBlocks ? undefined : function () {
      return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].ButtonBlockAppender, null);
    }
  }));
}

/* harmony default export */ var column_edit = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, ownProps) {
  var clientId = ownProps.clientId;

  var _select = select('core/block-editor'),
      getBlockOrder = _select.getBlockOrder;

  return {
    hasChildBlocks: getBlockOrder(clientId).length > 0
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps, registry) {
  return {
    updateAlignment: function updateAlignment(verticalAlignment) {
      var clientId = ownProps.clientId,
          setAttributes = ownProps.setAttributes;

      var _dispatch = dispatch('core/block-editor'),
          updateBlockAttributes = _dispatch.updateBlockAttributes;

      var _registry$select = registry.select('core/block-editor'),
          getBlockRootClientId = _registry$select.getBlockRootClientId; // Update own alignment.


      setAttributes({
        verticalAlignment: verticalAlignment
      }); // Reset Parent Columns Block

      var rootClientId = getBlockRootClientId(clientId);
      updateBlockAttributes(rootClientId, {
        verticalAlignment: null
      });
    },
    updateWidth: function updateWidth(width) {
      var clientId = ownProps.clientId;

      var _dispatch2 = dispatch('core/block-editor'),
          updateBlockAttributes = _dispatch2.updateBlockAttributes;

      var _registry$select2 = registry.select('core/block-editor'),
          getBlockRootClientId = _registry$select2.getBlockRootClientId,
          getBlocks = _registry$select2.getBlocks; // Constrain or expand siblings to account for gain or loss of
      // total columns area.


      var columns = getBlocks(getBlockRootClientId(clientId));
      var adjacentColumns = getAdjacentBlocks(columns, clientId); // The occupied width is calculated as the sum of the new width
      // and the total width of blocks _not_ in the adjacent set.

      var occupiedWidth = width + getTotalColumnsWidth(Object(external_lodash_["difference"])(columns, [Object(external_lodash_["find"])(columns, {
        clientId: clientId
      })].concat(Object(toConsumableArray["a" /* default */])(adjacentColumns)))); // Compute _all_ next column widths, in case the updated column
      // is in the middle of a set of columns which don't yet have
      // any explicit widths assigned (include updates to those not
      // part of the adjacent blocks).

      var nextColumnWidths = Object(objectSpread["a" /* default */])({}, getColumnWidths(columns, columns.length), Object(defineProperty["a" /* default */])({}, clientId, toWidthPrecision(width)), getRedistributedColumnWidths(adjacentColumns, 100 - occupiedWidth, columns.length));

      Object(external_lodash_["forEach"])(nextColumnWidths, function (nextColumnWidth, columnClientId) {
        updateBlockAttributes(columnClientId, {
          width: nextColumnWidth
        });
      });
    }
  };
}))(ColumnEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/column/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var column_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16zm0-11.47L17.74 9 12 13.47 6.26 9 12 4.53z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/column/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function column_save_save(_ref) {
  var attributes = _ref.attributes;
  var verticalAlignment = attributes.verticalAlignment,
      width = attributes.width;
  var wrapperClasses = classnames_default()(Object(defineProperty["a" /* default */])({}, "is-vertically-aligned-".concat(verticalAlignment), verticalAlignment));
  var style;

  if (Number.isFinite(width)) {
    style = {
      flexBasis: width + '%'
    };
  }

  return Object(external_this_wp_element_["createElement"])("div", {
    className: wrapperClasses,
    style: style
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].Content, null));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/column/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var column_metadata = {
  name: "core/column",
  category: "common",
  attributes: {
    verticalAlignment: {
      type: "string"
    },
    width: {
      type: "number",
      min: 0,
      max: 100
    }
  }
};

var column_name = column_metadata.name;

var column_settings = {
  title: Object(external_this_wp_i18n_["__"])('Column'),
  parent: ['core/columns'],
  icon: column_icon,
  description: Object(external_this_wp_i18n_["__"])('A single column within a columns block.'),
  supports: {
    inserter: false,
    reusable: false,
    html: false
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var width = attributes.width;

    if (Number.isFinite(width)) {
      return {
        style: {
          flexBasis: width + '%'
        }
      };
    }
  },
  edit: column_edit,
  save: column_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/cover/shared.js
var IMAGE_BACKGROUND_TYPE = 'image';
var VIDEO_BACKGROUND_TYPE = 'video';
var COVER_MIN_HEIGHT = 50;
function backgroundImageStyles(url) {
  return url ? {
    backgroundImage: "url(".concat(url, ")")
  } : {};
}
function dimRatioToClass(ratio) {
  return ratio === 0 || ratio === 50 ? null : 'has-background-dim-' + 10 * Math.round(ratio / 10);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/cover/deprecated.js




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var cover_deprecated_blockAttributes = {
  url: {
    type: 'string'
  },
  id: {
    type: 'number'
  },
  hasParallax: {
    type: 'boolean',
    default: false
  },
  dimRatio: {
    type: 'number',
    default: 50
  },
  overlayColor: {
    type: 'string'
  },
  customOverlayColor: {
    type: 'string'
  },
  backgroundType: {
    type: 'string',
    default: 'image'
  },
  focalPoint: {
    type: 'object'
  }
};
var cover_deprecated_deprecated = [{
  attributes: Object(objectSpread["a" /* default */])({}, cover_deprecated_blockAttributes, {
    title: {
      type: 'string',
      source: 'html',
      selector: 'p'
    },
    contentAlign: {
      type: 'string',
      default: 'center'
    }
  }),
  supports: {
    align: true
  },
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var backgroundType = attributes.backgroundType,
        contentAlign = attributes.contentAlign,
        customOverlayColor = attributes.customOverlayColor,
        dimRatio = attributes.dimRatio,
        focalPoint = attributes.focalPoint,
        hasParallax = attributes.hasParallax,
        overlayColor = attributes.overlayColor,
        title = attributes.title,
        url = attributes.url;
    var overlayColorClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', overlayColor);
    var style = backgroundType === IMAGE_BACKGROUND_TYPE ? backgroundImageStyles(url) : {};

    if (!overlayColorClass) {
      style.backgroundColor = customOverlayColor;
    }

    if (focalPoint && !hasParallax) {
      style.backgroundPosition = "".concat(focalPoint.x * 100, "% ").concat(focalPoint.y * 100, "%");
    }

    var classes = classnames_default()(dimRatioToClass(dimRatio), overlayColorClass, Object(defineProperty["a" /* default */])({
      'has-background-dim': dimRatio !== 0,
      'has-parallax': hasParallax
    }, "has-".concat(contentAlign, "-content"), contentAlign !== 'center'));
    return Object(external_this_wp_element_["createElement"])("div", {
      className: classes,
      style: style
    }, VIDEO_BACKGROUND_TYPE === backgroundType && url && Object(external_this_wp_element_["createElement"])("video", {
      className: "wp-block-cover__video-background",
      autoPlay: true,
      muted: true,
      loop: true,
      src: url
    }), !external_this_wp_blockEditor_["RichText"].isEmpty(title) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "p",
      className: "wp-block-cover-text",
      value: title
    }));
  },
  migrate: function migrate(attributes) {
    return [Object(external_lodash_["omit"])(attributes, ['title', 'contentAlign']), [Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
      content: attributes.title,
      align: attributes.contentAlign,
      fontSize: 'large',
      placeholder: Object(external_this_wp_i18n_["__"])('Write title…')
    })]];
  }
}, {
  attributes: Object(objectSpread["a" /* default */])({}, cover_deprecated_blockAttributes, {
    title: {
      type: 'string',
      source: 'html',
      selector: 'p'
    },
    contentAlign: {
      type: 'string',
      default: 'center'
    },
    align: {
      type: 'string'
    }
  }),
  supports: {
    className: false
  },
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    var url = attributes.url,
        title = attributes.title,
        hasParallax = attributes.hasParallax,
        dimRatio = attributes.dimRatio,
        align = attributes.align,
        contentAlign = attributes.contentAlign,
        overlayColor = attributes.overlayColor,
        customOverlayColor = attributes.customOverlayColor;
    var overlayColorClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', overlayColor);
    var style = backgroundImageStyles(url);

    if (!overlayColorClass) {
      style.backgroundColor = customOverlayColor;
    }

    var classes = classnames_default()('wp-block-cover-image', dimRatioToClass(dimRatio), overlayColorClass, Object(defineProperty["a" /* default */])({
      'has-background-dim': dimRatio !== 0,
      'has-parallax': hasParallax
    }, "has-".concat(contentAlign, "-content"), contentAlign !== 'center'), align ? "align".concat(align) : null);
    return Object(external_this_wp_element_["createElement"])("div", {
      className: classes,
      style: style
    }, !external_this_wp_blockEditor_["RichText"].isEmpty(title) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "p",
      className: "wp-block-cover-image-text",
      value: title
    }));
  },
  migrate: function migrate(attributes) {
    return [Object(external_lodash_["omit"])(attributes, ['title', 'contentAlign', 'align']), [Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
      content: attributes.title,
      align: attributes.contentAlign,
      fontSize: 'large',
      placeholder: Object(external_this_wp_i18n_["__"])('Write title…')
    })]];
  }
}, {
  attributes: Object(objectSpread["a" /* default */])({}, cover_deprecated_blockAttributes, {
    title: {
      type: 'string',
      source: 'html',
      selector: 'h2'
    },
    align: {
      type: 'string'
    },
    contentAlign: {
      type: 'string',
      default: 'center'
    }
  }),
  supports: {
    className: false
  },
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    var url = attributes.url,
        title = attributes.title,
        hasParallax = attributes.hasParallax,
        dimRatio = attributes.dimRatio,
        align = attributes.align;
    var style = backgroundImageStyles(url);
    var classes = classnames_default()('wp-block-cover-image', dimRatioToClass(dimRatio), {
      'has-background-dim': dimRatio !== 0,
      'has-parallax': hasParallax
    }, align ? "align".concat(align) : null);
    return Object(external_this_wp_element_["createElement"])("section", {
      className: classes,
      style: style
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "h2",
      value: title
    }));
  },
  migrate: function migrate(attributes) {
    return [Object(external_lodash_["omit"])(attributes, ['title', 'contentAlign', 'align']), [Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
      content: attributes.title,
      align: attributes.contentAlign,
      fontSize: 'large',
      placeholder: Object(external_this_wp_i18n_["__"])('Write title…')
    })]];
  }
}];
/* harmony default export */ var cover_deprecated = (cover_deprecated_deprecated);

// EXTERNAL MODULE: ./node_modules/fast-average-color/dist/index.js
var dist = __webpack_require__(221);
var dist_default = /*#__PURE__*/__webpack_require__.n(dist);

// EXTERNAL MODULE: ./node_modules/tinycolor2/tinycolor.js
var tinycolor = __webpack_require__(48);
var tinycolor_default = /*#__PURE__*/__webpack_require__.n(tinycolor);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/cover/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var cover_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M4 4h7V2H4c-1.1 0-2 .9-2 2v7h2V4zm6 9l-4 5h12l-3-4-2.03 2.71L10 13zm7-4.5c0-.83-.67-1.5-1.5-1.5S14 7.67 14 8.5s.67 1.5 1.5 1.5S17 9.33 17 8.5zM20 2h-7v2h7v7h2V4c0-1.1-.9-2-2-2zm0 18h-7v2h7c1.1 0 2-.9 2-2v-7h-2v7zM4 13H2v7c0 1.1.9 2 2 2h7v-2H4v-7z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0 0h24v24H0z",
  fill: "none"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/cover/edit.js











/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



/**
 * Module Constants
 */

var cover_edit_ALLOWED_MEDIA_TYPES = ['image', 'video'];
var INNER_BLOCKS_TEMPLATE = [['core/paragraph', {
  align: 'center',
  fontSize: 'large',
  placeholder: Object(external_this_wp_i18n_["__"])('Write title…')
}]];

function retrieveFastAverageColor() {
  if (!retrieveFastAverageColor.fastAverageColor) {
    retrieveFastAverageColor.fastAverageColor = new dist_default.a();
  }

  return retrieveFastAverageColor.fastAverageColor;
}

var CoverHeightInput = Object(external_this_wp_compose_["withInstanceId"])(function (_ref) {
  var _ref$value = _ref.value,
      value = _ref$value === void 0 ? '' : _ref$value,
      instanceId = _ref.instanceId,
      onChange = _ref.onChange;

  var _useState = Object(external_this_wp_element_["useState"])(null),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      temporaryInput = _useState2[0],
      setTemporaryInput = _useState2[1];

  var onChangeEvent = Object(external_this_wp_element_["useCallback"])(function (event) {
    var unprocessedValue = event.target.value;
    var inputValue = unprocessedValue !== '' ? parseInt(event.target.value, 10) : undefined;

    if ((isNaN(inputValue) || inputValue < COVER_MIN_HEIGHT) && inputValue !== undefined) {
      setTemporaryInput(event.target.value);
      return;
    }

    setTemporaryInput(null);
    onChange(inputValue);
  }, [onChange, setTemporaryInput]);
  var onBlurEvent = Object(external_this_wp_element_["useCallback"])(function () {
    if (temporaryInput !== null) {
      setTemporaryInput(null);
    }
  }, [temporaryInput, setTemporaryInput]);
  var inputId = "block-cover-height-input-".concat(instanceId);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["BaseControl"], {
    label: Object(external_this_wp_i18n_["__"])('Minimum height in pixels'),
    id: inputId
  }, Object(external_this_wp_element_["createElement"])("input", {
    type: "number",
    id: inputId,
    onChange: onChangeEvent,
    onBlur: onBlurEvent,
    value: temporaryInput !== null ? temporaryInput : value,
    min: COVER_MIN_HEIGHT,
    step: "10"
  }));
});
var RESIZABLE_BOX_ENABLE_OPTION = {
  top: false,
  right: false,
  bottom: true,
  left: false,
  topRight: false,
  bottomRight: false,
  bottomLeft: false,
  topLeft: false
};

function ResizableCover(_ref2) {
  var className = _ref2.className,
      children = _ref2.children,
      onResizeStart = _ref2.onResizeStart,
      onResize = _ref2.onResize,
      onResizeStop = _ref2.onResizeStop;

  var _useState3 = Object(external_this_wp_element_["useState"])(false),
      _useState4 = Object(slicedToArray["a" /* default */])(_useState3, 2),
      isResizing = _useState4[0],
      setIsResizing = _useState4[1];

  var onResizeEvent = Object(external_this_wp_element_["useCallback"])(function (event, direction, elt) {
    onResize(elt.clientHeight);

    if (!isResizing) {
      setIsResizing(true);
    }
  }, [onResize, setIsResizing]);
  var onResizeStartEvent = Object(external_this_wp_element_["useCallback"])(function (event, direction, elt) {
    onResizeStart(elt.clientHeight);
    onResize(elt.clientHeight);
  }, [onResizeStart, onResize]);
  var onResizeStopEvent = Object(external_this_wp_element_["useCallback"])(function (event, direction, elt) {
    onResizeStop(elt.clientHeight);
    setIsResizing(false);
  }, [onResizeStop, setIsResizing]);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ResizableBox"], {
    className: classnames_default()(className, {
      'is-resizing': isResizing
    }),
    enable: RESIZABLE_BOX_ENABLE_OPTION,
    onResizeStart: onResizeStartEvent,
    onResize: onResizeEvent,
    onResizeStop: onResizeStopEvent,
    minHeight: COVER_MIN_HEIGHT
  }, children);
}

var edit_CoverEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(CoverEdit, _Component);

  function CoverEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, CoverEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(CoverEdit).apply(this, arguments));
    _this.state = {
      isDark: false,
      temporaryMinHeight: null
    };
    _this.imageRef = Object(external_this_wp_element_["createRef"])();
    _this.videoRef = Object(external_this_wp_element_["createRef"])();
    _this.changeIsDarkIfRequired = _this.changeIsDarkIfRequired.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onUploadError = _this.onUploadError.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(CoverEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.handleBackgroundMode();
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      this.handleBackgroundMode(prevProps);
    }
  }, {
    key: "onUploadError",
    value: function onUploadError(message) {
      var noticeOperations = this.props.noticeOperations;
      noticeOperations.removeAllNotices();
      noticeOperations.createErrorNotice(message);
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes,
          isSelected = _this$props.isSelected,
          className = _this$props.className,
          noticeUI = _this$props.noticeUI,
          overlayColor = _this$props.overlayColor,
          setOverlayColor = _this$props.setOverlayColor,
          toggleSelection = _this$props.toggleSelection;
      var backgroundType = attributes.backgroundType,
          dimRatio = attributes.dimRatio,
          focalPoint = attributes.focalPoint,
          hasParallax = attributes.hasParallax,
          id = attributes.id,
          url = attributes.url,
          minHeight = attributes.minHeight;

      var onSelectMedia = function onSelectMedia(media) {
        if (!media || !media.url) {
          setAttributes({
            url: undefined,
            id: undefined
          });
          return;
        }

        var mediaType; // for media selections originated from a file upload.

        if (media.media_type) {
          if (media.media_type === IMAGE_BACKGROUND_TYPE) {
            mediaType = IMAGE_BACKGROUND_TYPE;
          } else {
            // only images and videos are accepted so if the media_type is not an image we can assume it is a video.
            // Videos contain the media type of 'file' in the object returned from the rest api.
            mediaType = VIDEO_BACKGROUND_TYPE;
          }
        } else {
          // for media selections originated from existing files in the media library.
          if (media.type !== IMAGE_BACKGROUND_TYPE && media.type !== VIDEO_BACKGROUND_TYPE) {
            return;
          }

          mediaType = media.type;
        }

        setAttributes(Object(objectSpread["a" /* default */])({
          url: media.url,
          id: media.id,
          backgroundType: mediaType
        }, mediaType === VIDEO_BACKGROUND_TYPE ? {
          focalPoint: undefined,
          hasParallax: undefined
        } : {}));
      };

      var toggleParallax = function toggleParallax() {
        setAttributes(Object(objectSpread["a" /* default */])({
          hasParallax: !hasParallax
        }, !hasParallax ? {
          focalPoint: undefined
        } : {}));
      };

      var setDimRatio = function setDimRatio(ratio) {
        return setAttributes({
          dimRatio: ratio
        });
      };

      var temporaryMinHeight = this.state.temporaryMinHeight;

      var style = Object(objectSpread["a" /* default */])({}, backgroundType === IMAGE_BACKGROUND_TYPE ? backgroundImageStyles(url) : {}, {
        backgroundColor: overlayColor.color,
        minHeight: temporaryMinHeight || minHeight
      });

      if (focalPoint) {
        style.backgroundPosition = "".concat(focalPoint.x * 100, "% ").concat(focalPoint.y * 100, "%");
      }

      var controls = Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, !!(url || overlayColor.color) && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
        onSelect: onSelectMedia,
        allowedTypes: cover_edit_ALLOWED_MEDIA_TYPES,
        value: id,
        render: function render(_ref3) {
          var open = _ref3.open;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
            className: "components-toolbar__control",
            label: Object(external_this_wp_i18n_["__"])('Edit media'),
            icon: "edit",
            onClick: open
          });
        }
      }))))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, !!url && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Media Settings')
      }, IMAGE_BACKGROUND_TYPE === backgroundType && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Fixed Background'),
        checked: hasParallax,
        onChange: toggleParallax
      }), IMAGE_BACKGROUND_TYPE === backgroundType && !hasParallax && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["FocalPointPicker"], {
        label: Object(external_this_wp_i18n_["__"])('Focal Point Picker'),
        url: url,
        value: focalPoint,
        onChange: function onChange(value) {
          return setAttributes({
            focalPoint: value
          });
        }
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        isDefault: true,
        isSmall: true,
        className: "block-library-cover__reset-button",
        onClick: function onClick() {
          return setAttributes({
            url: undefined,
            id: undefined,
            backgroundType: undefined,
            dimRatio: undefined,
            focalPoint: undefined,
            hasParallax: undefined
          });
        }
      }, Object(external_this_wp_i18n_["__"])('Clear Media')))), (url || overlayColor.color) && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Dimensions')
      }, Object(external_this_wp_element_["createElement"])(CoverHeightInput, {
        value: temporaryMinHeight || minHeight,
        onChange: function onChange(value) {
          setAttributes({
            minHeight: value
          });
        }
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
        title: Object(external_this_wp_i18n_["__"])('Overlay'),
        initialOpen: true,
        colorSettings: [{
          value: overlayColor.color,
          onChange: setOverlayColor,
          label: Object(external_this_wp_i18n_["__"])('Overlay Color')
        }]
      }, !!url && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
        label: Object(external_this_wp_i18n_["__"])('Background Opacity'),
        value: dimRatio,
        onChange: setDimRatio,
        min: 0,
        max: 100,
        step: 10,
        required: true
      })))));

      if (!(url || overlayColor.color)) {
        var placeholderIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
          icon: cover_icon
        });

        var label = Object(external_this_wp_i18n_["__"])('Cover');

        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, controls, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaPlaceholder"], {
          icon: placeholderIcon,
          className: className,
          labels: {
            title: label,
            instructions: Object(external_this_wp_i18n_["__"])('Upload an image or video file, or pick one from your media library.')
          },
          onSelect: onSelectMedia,
          accept: "image/*,video/*",
          allowedTypes: cover_edit_ALLOWED_MEDIA_TYPES,
          notices: noticeUI,
          onError: this.onUploadError
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["ColorPalette"], {
          disableCustomColors: true,
          value: overlayColor.color,
          onChange: setOverlayColor,
          clearable: false,
          className: "wp-block-cover__placeholder-color-palette"
        })));
      }

      var classes = classnames_default()(className, dimRatioToClass(dimRatio), Object(defineProperty["a" /* default */])({
        'is-dark-theme': this.state.isDark,
        'has-background-dim': dimRatio !== 0,
        'has-parallax': hasParallax
      }, overlayColor.class, overlayColor.class));
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, controls, Object(external_this_wp_element_["createElement"])(ResizableCover, {
        className: classnames_default()('block-library-cover__resize-container', {
          'is-selected': isSelected
        }),
        onResizeStart: function onResizeStart() {
          return toggleSelection(false);
        },
        onResize: function onResize(newMinHeight) {
          _this2.setState({
            temporaryMinHeight: newMinHeight
          });
        },
        onResizeStop: function onResizeStop(newMinHeight) {
          toggleSelection(true);
          setAttributes({
            minHeight: newMinHeight
          });

          _this2.setState({
            temporaryMinHeight: null
          });
        }
      }, Object(external_this_wp_element_["createElement"])("div", {
        "data-url": url,
        style: style,
        className: classes
      }, IMAGE_BACKGROUND_TYPE === backgroundType && // Used only to programmatically check if the image is dark or not
      Object(external_this_wp_element_["createElement"])("img", {
        ref: this.imageRef,
        "aria-hidden": true,
        alt: "",
        style: {
          display: 'none'
        },
        src: url
      }), VIDEO_BACKGROUND_TYPE === backgroundType && Object(external_this_wp_element_["createElement"])("video", {
        ref: this.videoRef,
        className: "wp-block-cover__video-background",
        autoPlay: true,
        muted: true,
        loop: true,
        src: url
      }), Object(external_this_wp_element_["createElement"])("div", {
        className: "wp-block-cover__inner-container"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"], {
        template: INNER_BLOCKS_TEMPLATE
      })))));
    }
  }, {
    key: "handleBackgroundMode",
    value: function handleBackgroundMode(prevProps) {
      var _this3 = this;

      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          overlayColor = _this$props2.overlayColor;
      var dimRatio = attributes.dimRatio,
          url = attributes.url; // If opacity is greater than 50 the dominant color is the overlay color,
      // so use that color for the dark mode computation.

      if (dimRatio > 50) {
        if (prevProps && prevProps.attributes.dimRatio > 50 && prevProps.overlayColor.color === overlayColor.color) {
          // No relevant prop changes happened there is no need to apply any change.
          return;
        }

        if (!overlayColor.color) {
          // If no overlay color exists the overlay color is black (isDark )
          this.changeIsDarkIfRequired(true);
          return;
        }

        this.changeIsDarkIfRequired(tinycolor_default()(overlayColor.color).isDark());
        return;
      } // If opacity is lower than 50 the dominant color is the image or video color,
      // so use that color for the dark mode computation.


      if (prevProps && prevProps.attributes.dimRatio <= 50 && prevProps.attributes.url === url) {
        // No relevant prop changes happened there is no need to apply any change.
        return;
      }

      var backgroundType = attributes.backgroundType;
      var element;

      switch (backgroundType) {
        case IMAGE_BACKGROUND_TYPE:
          element = this.imageRef.current;
          break;

        case VIDEO_BACKGROUND_TYPE:
          element = this.videoRef.current;
          break;
      }

      if (!element) {
        return;
      }

      retrieveFastAverageColor().getColorAsync(element, function (color) {
        _this3.changeIsDarkIfRequired(color.isDark);
      });
    }
  }, {
    key: "changeIsDarkIfRequired",
    value: function changeIsDarkIfRequired(newIsDark) {
      if (this.state.isDark !== newIsDark) {
        this.setState({
          isDark: newIsDark
        });
      }
    }
  }]);

  return CoverEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var cover_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/block-editor'),
      toggleSelection = _dispatch.toggleSelection;

  return {
    toggleSelection: toggleSelection
  };
}), Object(external_this_wp_blockEditor_["withColors"])({
  overlayColor: 'background-color'
}), external_this_wp_components_["withNotices"], external_this_wp_compose_["withInstanceId"]])(edit_CoverEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/cover/save.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function cover_save_save(_ref) {
  var attributes = _ref.attributes;
  var backgroundType = attributes.backgroundType,
      customOverlayColor = attributes.customOverlayColor,
      dimRatio = attributes.dimRatio,
      focalPoint = attributes.focalPoint,
      hasParallax = attributes.hasParallax,
      overlayColor = attributes.overlayColor,
      url = attributes.url,
      minHeight = attributes.minHeight;
  var overlayColorClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', overlayColor);
  var style = backgroundType === IMAGE_BACKGROUND_TYPE ? backgroundImageStyles(url) : {};

  if (!overlayColorClass) {
    style.backgroundColor = customOverlayColor;
  }

  if (focalPoint && !hasParallax) {
    style.backgroundPosition = "".concat(focalPoint.x * 100, "% ").concat(focalPoint.y * 100, "%");
  }

  style.minHeight = minHeight || undefined;
  var classes = classnames_default()(dimRatioToClass(dimRatio), overlayColorClass, {
    'has-background-dim': dimRatio !== 0,
    'has-parallax': hasParallax
  });
  return Object(external_this_wp_element_["createElement"])("div", {
    className: classes,
    style: style
  }, VIDEO_BACKGROUND_TYPE === backgroundType && url && Object(external_this_wp_element_["createElement"])("video", {
    className: "wp-block-cover__video-background",
    autoPlay: true,
    muted: true,
    loop: true,
    src: url
  }), Object(external_this_wp_element_["createElement"])("div", {
    className: "wp-block-cover__inner-container"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].Content, null)));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/cover/transforms.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var cover_transforms_transforms = {
  from: [{
    type: 'block',
    blocks: ['core/image'],
    transform: function transform(_ref) {
      var caption = _ref.caption,
          url = _ref.url,
          align = _ref.align,
          id = _ref.id;
      return Object(external_this_wp_blocks_["createBlock"])('core/cover', {
        title: caption,
        url: url,
        align: align,
        id: id
      });
    }
  }, {
    type: 'block',
    blocks: ['core/video'],
    transform: function transform(_ref2) {
      var caption = _ref2.caption,
          src = _ref2.src,
          align = _ref2.align,
          id = _ref2.id;
      return Object(external_this_wp_blocks_["createBlock"])('core/cover', {
        title: caption,
        url: src,
        align: align,
        id: id,
        backgroundType: VIDEO_BACKGROUND_TYPE
      });
    }
  }],
  to: [{
    type: 'block',
    blocks: ['core/image'],
    isMatch: function isMatch(_ref3) {
      var backgroundType = _ref3.backgroundType,
          url = _ref3.url;
      return !url || backgroundType === IMAGE_BACKGROUND_TYPE;
    },
    transform: function transform(_ref4) {
      var title = _ref4.title,
          url = _ref4.url,
          align = _ref4.align,
          id = _ref4.id;
      return Object(external_this_wp_blocks_["createBlock"])('core/image', {
        caption: title,
        url: url,
        align: align,
        id: id
      });
    }
  }, {
    type: 'block',
    blocks: ['core/video'],
    isMatch: function isMatch(_ref5) {
      var backgroundType = _ref5.backgroundType,
          url = _ref5.url;
      return !url || backgroundType === VIDEO_BACKGROUND_TYPE;
    },
    transform: function transform(_ref6) {
      var title = _ref6.title,
          url = _ref6.url,
          align = _ref6.align,
          id = _ref6.id;
      return Object(external_this_wp_blocks_["createBlock"])('core/video', {
        caption: title,
        src: url,
        id: id,
        align: align
      });
    }
  }]
};
/* harmony default export */ var cover_transforms = (cover_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/cover/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var cover_metadata = {
  name: "core/cover",
  category: "common",
  attributes: {
    url: {
      type: "string"
    },
    id: {
      type: "number"
    },
    hasParallax: {
      type: "boolean",
      "default": false
    },
    dimRatio: {
      type: "number",
      "default": 50
    },
    overlayColor: {
      type: "string"
    },
    customOverlayColor: {
      type: "string"
    },
    backgroundType: {
      type: "string",
      "default": "image"
    },
    focalPoint: {
      type: "object"
    },
    minHeight: {
      type: "number"
    }
  }
};


var cover_name = cover_metadata.name;

var cover_settings = {
  title: Object(external_this_wp_i18n_["__"])('Cover'),
  description: Object(external_this_wp_i18n_["__"])('Add an image or video with a text overlay — great for headers.'),
  icon: cover_icon,
  supports: {
    align: true
  },
  example: {
    attributes: {
      customOverlayColor: '#065174',
      dimRatio: 40,
      url: 'https://s.w.org/images/core/5.3/Windbuchencom.jpg'
    },
    innerBlocks: [{
      name: 'core/paragraph',
      attributes: {
        customFontSize: 48,
        content: Object(external_this_wp_i18n_["__"])('<strong>Snow Patrol</strong>'),
        align: 'center'
      }
    }]
  },
  transforms: cover_transforms,
  save: cover_save_save,
  edit: cover_edit,
  deprecated: cover_deprecated
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/embed-controls.js


/**
 * WordPress dependencies
 */




var embed_controls_EmbedControls = function EmbedControls(props) {
  var blockSupportsResponsive = props.blockSupportsResponsive,
      showEditButton = props.showEditButton,
      themeSupportsResponsive = props.themeSupportsResponsive,
      allowResponsive = props.allowResponsive,
      getResponsiveHelp = props.getResponsiveHelp,
      toggleResponsive = props.toggleResponsive,
      switchBackToURLInput = props.switchBackToURLInput;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, showEditButton && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    className: "components-toolbar__control",
    label: Object(external_this_wp_i18n_["__"])('Edit URL'),
    icon: "edit",
    onClick: switchBackToURLInput
  }))), themeSupportsResponsive && blockSupportsResponsive && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Media Settings'),
    className: "blocks-responsive"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
    label: Object(external_this_wp_i18n_["__"])('Resize for smaller devices'),
    checked: allowResponsive,
    help: getResponsiveHelp,
    onChange: toggleResponsive
  }))));
};

/* harmony default export */ var embed_controls = (embed_controls_EmbedControls);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/embed-loading.js


/**
 * WordPress dependencies
 */



var embed_loading_EmbedLoading = function EmbedLoading() {
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "wp-block-embed is-loading"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null), Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('Embedding…')));
};

/* harmony default export */ var embed_loading = (embed_loading_EmbedLoading);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/embed-placeholder.js


/**
 * WordPress dependencies
 */




var embed_placeholder_EmbedPlaceholder = function EmbedPlaceholder(props) {
  var icon = props.icon,
      label = props.label,
      value = props.value,
      onSubmit = props.onSubmit,
      onChange = props.onChange,
      cannotEmbed = props.cannotEmbed,
      fallback = props.fallback,
      tryAgain = props.tryAgain;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], {
    icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
      icon: icon,
      showColors: true
    }),
    label: label,
    className: "wp-block-embed",
    instructions: Object(external_this_wp_i18n_["__"])('Paste a link to the content you want to display on your site.')
  }, Object(external_this_wp_element_["createElement"])("form", {
    onSubmit: onSubmit
  }, Object(external_this_wp_element_["createElement"])("input", {
    type: "url",
    value: value || '',
    className: "components-placeholder__input",
    "aria-label": label,
    placeholder: Object(external_this_wp_i18n_["__"])('Enter URL to embed here…'),
    onChange: onChange
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isLarge: true,
    type: "submit"
  }, Object(external_this_wp_i18n_["_x"])('Embed', 'button label')), cannotEmbed && Object(external_this_wp_element_["createElement"])("p", {
    className: "components-placeholder__error"
  }, Object(external_this_wp_i18n_["__"])('Sorry, this content could not be embedded.'), Object(external_this_wp_element_["createElement"])("br", null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isLarge: true,
    onClick: tryAgain
  }, Object(external_this_wp_i18n_["_x"])('Try again', 'button label')), " ", Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isLarge: true,
    onClick: fallback
  }, Object(external_this_wp_i18n_["_x"])('Convert to link', 'button label')))), Object(external_this_wp_element_["createElement"])("div", {
    className: "components-placeholder__learn-more"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
    href: Object(external_this_wp_i18n_["__"])('https://wordpress.org/support/article/embeds/')
  }, Object(external_this_wp_i18n_["__"])('Learn more about embeds'))));
};

/* harmony default export */ var embed_placeholder = (embed_placeholder_EmbedPlaceholder);

// EXTERNAL MODULE: ./node_modules/url/url.js
var url_url = __webpack_require__(82);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/wp-embed-preview.js








/**
 * WordPress dependencies
 */


/**
 * Browser dependencies
 */

var wp_embed_preview_window = window,
    FocusEvent = wp_embed_preview_window.FocusEvent;

var wp_embed_preview_WpEmbedPreview =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(WpEmbedPreview, _Component);

  function WpEmbedPreview() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, WpEmbedPreview);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(WpEmbedPreview).apply(this, arguments));
    _this.checkFocus = _this.checkFocus.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.node = Object(external_this_wp_element_["createRef"])();
    return _this;
  }
  /**
   * Checks whether the wp embed iframe is the activeElement,
   * if it is dispatch a focus event.
   */


  Object(createClass["a" /* default */])(WpEmbedPreview, [{
    key: "checkFocus",
    value: function checkFocus() {
      var _document = document,
          activeElement = _document.activeElement;

      if (activeElement.tagName !== 'IFRAME' || activeElement.parentNode !== this.node.current) {
        return;
      }

      var focusEvent = new FocusEvent('focus', {
        bubbles: true
      });
      activeElement.dispatchEvent(focusEvent);
    }
  }, {
    key: "render",
    value: function render() {
      var html = this.props.html;
      return Object(external_this_wp_element_["createElement"])("div", {
        ref: this.node,
        className: "wp-block-embed__wrapper",
        dangerouslySetInnerHTML: {
          __html: html
        }
      });
    }
  }]);

  return WpEmbedPreview;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var wp_embed_preview = (Object(external_this_wp_compose_["withGlobalEvents"])({
  blur: 'checkFocus'
})(wp_embed_preview_WpEmbedPreview));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/embed-preview.js








/**
 * Internal dependencies
 */


/**
 * External dependencies
 */




/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



var embed_preview_EmbedPreview =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(EmbedPreview, _Component);

  function EmbedPreview() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, EmbedPreview);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(EmbedPreview).apply(this, arguments));
    _this.hideOverlay = _this.hideOverlay.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.state = {
      interactive: false
    };
    return _this;
  }

  Object(createClass["a" /* default */])(EmbedPreview, [{
    key: "hideOverlay",
    value: function hideOverlay() {
      // This is called onMouseUp on the overlay. We can't respond to the `isSelected` prop
      // changing, because that happens on mouse down, and the overlay immediately disappears,
      // and the mouse event can end up in the preview content. We can't use onClick on
      // the overlay to hide it either, because then the editor misses the mouseup event, and
      // thinks we're multi-selecting blocks.
      this.setState({
        interactive: true
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          preview = _this$props.preview,
          url = _this$props.url,
          type = _this$props.type,
          caption = _this$props.caption,
          onCaptionChange = _this$props.onCaptionChange,
          isSelected = _this$props.isSelected,
          className = _this$props.className,
          icon = _this$props.icon,
          label = _this$props.label;
      var scripts = preview.scripts;
      var interactive = this.state.interactive;
      var html = 'photo' === type ? util_getPhotoHtml(preview) : preview.html;
      var parsedHost = Object(url_url["parse"])(url).host.split('.');
      var parsedHostBaseUrl = parsedHost.splice(parsedHost.length - 2, parsedHost.length - 1).join('.');
      var cannotPreview = Object(external_lodash_["includes"])(HOSTS_NO_PREVIEWS, parsedHostBaseUrl); // translators: %s: host providing embed content e.g: www.youtube.com

      var iframeTitle = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Embedded content from %s'), parsedHostBaseUrl);
      var sandboxClassnames = dedupe_default()(type, className, 'wp-block-embed__wrapper'); // Disabled because the overlay div doesn't actually have a role or functionality
      // as far as the user is concerned. We're just catching the first click so that
      // the block can be selected without interacting with the embed preview that the overlay covers.

      /* eslint-disable jsx-a11y/no-static-element-interactions */

      var embedWrapper = 'wp-embed' === type ? Object(external_this_wp_element_["createElement"])(wp_embed_preview, {
        html: html
      }) : Object(external_this_wp_element_["createElement"])("div", {
        className: "wp-block-embed__wrapper"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SandBox"], {
        html: html,
        scripts: scripts,
        title: iframeTitle,
        type: sandboxClassnames,
        onFocus: this.hideOverlay
      }), !interactive && Object(external_this_wp_element_["createElement"])("div", {
        className: "block-library-embed__interactive-overlay",
        onMouseUp: this.hideOverlay
      }));
      /* eslint-enable jsx-a11y/no-static-element-interactions */

      return Object(external_this_wp_element_["createElement"])("figure", {
        className: dedupe_default()(className, 'wp-block-embed', {
          'is-type-video': 'video' === type
        })
      }, cannotPreview ? Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], {
        icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
          icon: icon,
          showColors: true
        }),
        label: label
      }, Object(external_this_wp_element_["createElement"])("p", {
        className: "components-placeholder__error"
      }, Object(external_this_wp_element_["createElement"])("a", {
        href: url
      }, url)), Object(external_this_wp_element_["createElement"])("p", {
        className: "components-placeholder__error"
      },
      /* translators: %s: host providing embed content e.g: www.youtube.com */
      Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])("Embedded content from %s can't be previewed in the editor."), parsedHostBaseUrl))) : embedWrapper, (!external_this_wp_blockEditor_["RichText"].isEmpty(caption) || isSelected) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        tagName: "figcaption",
        placeholder: Object(external_this_wp_i18n_["__"])('Write caption…'),
        value: caption,
        onChange: onCaptionChange,
        inlineToolbar: true
      }));
    }
  }], [{
    key: "getDerivedStateFromProps",
    value: function getDerivedStateFromProps(nextProps, state) {
      if (!nextProps.isSelected && state.interactive) {
        // We only want to change this when the block is not selected, because changing it when
        // the block becomes selected makes the overlap disappear too early. Hiding the overlay
        // happens on mouseup when the overlay is clicked.
        return {
          interactive: false
        };
      }

      return null;
    }
  }]);

  return EmbedPreview;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var embed_preview = (embed_preview_EmbedPreview);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/edit.js









/**
 * Internal dependencies
 */





/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



function getEmbedEditComponent(title, icon) {
  var responsive = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
  return (
    /*#__PURE__*/
    function (_Component) {
      Object(inherits["a" /* default */])(_class, _Component);

      function _class() {
        var _this;

        Object(classCallCheck["a" /* default */])(this, _class);

        _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(_class).apply(this, arguments));
        _this.switchBackToURLInput = _this.switchBackToURLInput.bind(Object(assertThisInitialized["a" /* default */])(_this));
        _this.setUrl = _this.setUrl.bind(Object(assertThisInitialized["a" /* default */])(_this));
        _this.getMergedAttributes = _this.getMergedAttributes.bind(Object(assertThisInitialized["a" /* default */])(_this));
        _this.setMergedAttributes = _this.setMergedAttributes.bind(Object(assertThisInitialized["a" /* default */])(_this));
        _this.getResponsiveHelp = _this.getResponsiveHelp.bind(Object(assertThisInitialized["a" /* default */])(_this));
        _this.toggleResponsive = _this.toggleResponsive.bind(Object(assertThisInitialized["a" /* default */])(_this));
        _this.handleIncomingPreview = _this.handleIncomingPreview.bind(Object(assertThisInitialized["a" /* default */])(_this));
        _this.state = {
          editingURL: false,
          url: _this.props.attributes.url
        };

        if (_this.props.preview) {
          _this.handleIncomingPreview();
        }

        return _this;
      }

      Object(createClass["a" /* default */])(_class, [{
        key: "handleIncomingPreview",
        value: function handleIncomingPreview() {
          this.setMergedAttributes();

          if (this.props.onReplace) {
            var upgradedBlock = util_createUpgradedEmbedBlock(this.props, this.getMergedAttributes());

            if (upgradedBlock) {
              this.props.onReplace(upgradedBlock);
            }
          }
        }
      }, {
        key: "componentDidUpdate",
        value: function componentDidUpdate(prevProps) {
          var hasPreview = undefined !== this.props.preview;
          var hadPreview = undefined !== prevProps.preview;
          var previewChanged = prevProps.preview && this.props.preview && this.props.preview.html !== prevProps.preview.html;
          var switchedPreview = previewChanged || hasPreview && !hadPreview;
          var switchedURL = this.props.attributes.url !== prevProps.attributes.url;

          if (switchedPreview || switchedURL) {
            if (this.props.cannotEmbed) {
              // We either have a new preview or a new URL, but we can't embed it.
              if (!this.props.fetching) {
                // If we're not fetching the preview, then we know it can't be embedded, so try
                // removing any trailing slash, and resubmit.
                this.resubmitWithoutTrailingSlash();
              }

              return;
            }

            this.handleIncomingPreview();
          }
        }
      }, {
        key: "resubmitWithoutTrailingSlash",
        value: function resubmitWithoutTrailingSlash() {
          this.setState(function (prevState) {
            return {
              url: prevState.url.replace(/\/$/, '')
            };
          }, this.setUrl);
        }
      }, {
        key: "setUrl",
        value: function setUrl(event) {
          if (event) {
            event.preventDefault();
          }

          var url = this.state.url;
          var setAttributes = this.props.setAttributes;
          this.setState({
            editingURL: false
          });
          setAttributes({
            url: url
          });
        }
        /***
         * @return {Object} Attributes derived from the preview, merged with the current attributes.
         */

      }, {
        key: "getMergedAttributes",
        value: function getMergedAttributes() {
          var preview = this.props.preview;
          var _this$props$attribute = this.props.attributes,
              className = _this$props$attribute.className,
              allowResponsive = _this$props$attribute.allowResponsive;
          return Object(objectSpread["a" /* default */])({}, this.props.attributes, getAttributesFromPreview(preview, title, className, responsive, allowResponsive));
        }
        /***
         * Sets block attributes based on the current attributes and preview data.
         */

      }, {
        key: "setMergedAttributes",
        value: function setMergedAttributes() {
          var setAttributes = this.props.setAttributes;
          setAttributes(this.getMergedAttributes());
        }
      }, {
        key: "switchBackToURLInput",
        value: function switchBackToURLInput() {
          this.setState({
            editingURL: true
          });
        }
      }, {
        key: "getResponsiveHelp",
        value: function getResponsiveHelp(checked) {
          return checked ? Object(external_this_wp_i18n_["__"])('This embed will preserve its aspect ratio when the browser is resized.') : Object(external_this_wp_i18n_["__"])('This embed may not preserve its aspect ratio when the browser is resized.');
        }
      }, {
        key: "toggleResponsive",
        value: function toggleResponsive() {
          var _this$props$attribute2 = this.props.attributes,
              allowResponsive = _this$props$attribute2.allowResponsive,
              className = _this$props$attribute2.className;
          var html = this.props.preview.html;
          var newAllowResponsive = !allowResponsive;
          this.props.setAttributes({
            allowResponsive: newAllowResponsive,
            className: getClassNames(html, className, responsive && newAllowResponsive)
          });
        }
      }, {
        key: "render",
        value: function render() {
          var _this2 = this;

          var _this$state = this.state,
              url = _this$state.url,
              editingURL = _this$state.editingURL;
          var _this$props = this.props,
              fetching = _this$props.fetching,
              setAttributes = _this$props.setAttributes,
              isSelected = _this$props.isSelected,
              preview = _this$props.preview,
              cannotEmbed = _this$props.cannotEmbed,
              themeSupportsResponsive = _this$props.themeSupportsResponsive,
              tryAgain = _this$props.tryAgain;

          if (fetching) {
            return Object(external_this_wp_element_["createElement"])(embed_loading, null);
          } // translators: %s: type of embed e.g: "YouTube", "Twitter", etc. "Embed" is used when no specific type exists


          var label = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('%s URL'), title); // No preview, or we can't embed the current URL, or we've clicked the edit button.

          if (!preview || cannotEmbed || editingURL) {
            return Object(external_this_wp_element_["createElement"])(embed_placeholder, {
              icon: icon,
              label: label,
              onSubmit: this.setUrl,
              value: url,
              cannotEmbed: cannotEmbed,
              onChange: function onChange(event) {
                return _this2.setState({
                  url: event.target.value
                });
              },
              fallback: function fallback() {
                return util_fallback(url, _this2.props.onReplace);
              },
              tryAgain: tryAgain
            });
          } // Even though we set attributes that get derived from the preview,
          // we don't access them directly because for the initial render,
          // the `setAttributes` call will not have taken effect. If we're
          // rendering responsive content, setting the responsive classes
          // after the preview has been rendered can result in unwanted
          // clipping or scrollbars. The `getAttributesFromPreview` function
          // that `getMergedAttributes` uses is memoized so that we're not
          // calculating them on every render.


          var previewAttributes = this.getMergedAttributes();
          var caption = previewAttributes.caption,
              type = previewAttributes.type,
              allowResponsive = previewAttributes.allowResponsive;
          var className = classnames_default()(previewAttributes.className, this.props.className);
          return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(embed_controls, {
            showEditButton: preview && !cannotEmbed,
            themeSupportsResponsive: themeSupportsResponsive,
            blockSupportsResponsive: responsive,
            allowResponsive: allowResponsive,
            getResponsiveHelp: this.getResponsiveHelp,
            toggleResponsive: this.toggleResponsive,
            switchBackToURLInput: this.switchBackToURLInput
          }), Object(external_this_wp_element_["createElement"])(embed_preview, {
            preview: preview,
            className: className,
            url: url,
            type: type,
            caption: caption,
            onCaptionChange: function onCaptionChange(value) {
              return setAttributes({
                caption: value
              });
            },
            isSelected: isSelected,
            icon: icon,
            label: label
          }));
        }
      }]);

      return _class;
    }(external_this_wp_element_["Component"])
  );
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/settings.js




/**
 * Internal dependencies
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */





var embedAttributes = {
  url: {
    type: 'string'
  },
  caption: {
    type: 'string',
    source: 'html',
    selector: 'figcaption'
  },
  type: {
    type: 'string'
  },
  providerNameSlug: {
    type: 'string'
  },
  allowResponsive: {
    type: 'boolean',
    default: true
  }
};
function getEmbedBlockSettings(_ref) {
  var title = _ref.title,
      description = _ref.description,
      icon = _ref.icon,
      _ref$category = _ref.category,
      category = _ref$category === void 0 ? 'embed' : _ref$category,
      transforms = _ref.transforms,
      _ref$keywords = _ref.keywords,
      keywords = _ref$keywords === void 0 ? [] : _ref$keywords,
      _ref$supports = _ref.supports,
      supports = _ref$supports === void 0 ? {} : _ref$supports,
      _ref$responsive = _ref.responsive,
      responsive = _ref$responsive === void 0 ? true : _ref$responsive;

  var blockDescription = description || Object(external_this_wp_i18n_["__"])('Add a block that displays content pulled from other sites, like Twitter, Instagram or YouTube.');

  var edit = getEmbedEditComponent(title, icon, responsive);
  return {
    title: title,
    description: blockDescription,
    icon: icon,
    category: category,
    keywords: keywords,
    attributes: embedAttributes,
    supports: Object(objectSpread["a" /* default */])({
      align: true
    }, supports),
    transforms: transforms,
    edit: Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, ownProps) {
      var url = ownProps.attributes.url;
      var core = select('core');
      var getEmbedPreview = core.getEmbedPreview,
          isPreviewEmbedFallback = core.isPreviewEmbedFallback,
          isRequestingEmbedPreview = core.isRequestingEmbedPreview,
          getThemeSupports = core.getThemeSupports;
      var preview = undefined !== url && getEmbedPreview(url);
      var previewIsFallback = undefined !== url && isPreviewEmbedFallback(url);
      var fetching = undefined !== url && isRequestingEmbedPreview(url);
      var themeSupports = getThemeSupports(); // The external oEmbed provider does not exist. We got no type info and no html.

      var badEmbedProvider = !!preview && undefined === preview.type && false === preview.html; // Some WordPress URLs that can't be embedded will cause the API to return
      // a valid JSON response with no HTML and `data.status` set to 404, rather
      // than generating a fallback response as other embeds do.

      var wordpressCantEmbed = !!preview && preview.data && preview.data.status === 404;
      var validPreview = !!preview && !badEmbedProvider && !wordpressCantEmbed;
      var cannotEmbed = undefined !== url && (!validPreview || previewIsFallback);
      return {
        preview: validPreview ? preview : undefined,
        fetching: fetching,
        themeSupportsResponsive: themeSupports['responsive-embeds'],
        cannotEmbed: cannotEmbed
      };
    }), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
      var url = ownProps.attributes.url;
      var coreData = dispatch('core/data');

      var tryAgain = function tryAgain() {
        coreData.invalidateResolution('core', 'getEmbedPreview', [url]);
      };

      return {
        tryAgain: tryAgain
      };
    }))(edit),
    save: function save(_ref2) {
      var _classnames;

      var attributes = _ref2.attributes;
      var url = attributes.url,
          caption = attributes.caption,
          type = attributes.type,
          providerNameSlug = attributes.providerNameSlug;

      if (!url) {
        return null;
      }

      var embedClassName = dedupe_default()('wp-block-embed', (_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, "is-type-".concat(type), type), Object(defineProperty["a" /* default */])(_classnames, "is-provider-".concat(providerNameSlug), providerNameSlug), _classnames));
      return Object(external_this_wp_element_["createElement"])("figure", {
        className: embedClassName
      }, Object(external_this_wp_element_["createElement"])("div", {
        className: "wp-block-embed__wrapper"
      }, "\n".concat(url, "\n")
      /* URL needs to be on its own line. */
      ), !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
        tagName: "figcaption",
        value: caption
      }));
    },
    deprecated: [{
      attributes: embedAttributes,
      save: function save(_ref3) {
        var _classnames2;

        var attributes = _ref3.attributes;
        var url = attributes.url,
            caption = attributes.caption,
            type = attributes.type,
            providerNameSlug = attributes.providerNameSlug;

        if (!url) {
          return null;
        }

        var embedClassName = dedupe_default()('wp-block-embed', (_classnames2 = {}, Object(defineProperty["a" /* default */])(_classnames2, "is-type-".concat(type), type), Object(defineProperty["a" /* default */])(_classnames2, "is-provider-".concat(providerNameSlug), providerNameSlug), _classnames2));
        return Object(external_this_wp_element_["createElement"])("figure", {
          className: embedClassName
        }, "\n".concat(url, "\n")
        /* URL needs to be on its own line. */
        , !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
          tagName: "figcaption",
          value: caption
        }));
      }
    }]
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/index.js


/**
 * Internal dependencies
 */



/**
 * WordPress dependencies
 */



var embed_name = 'core/embed';
var embed_settings = getEmbedBlockSettings({
  title: Object(external_this_wp_i18n_["_x"])('Embed', 'block title'),
  description: Object(external_this_wp_i18n_["__"])('Embed videos, images, tweets, audio, and other content from external sources.'),
  icon: embedContentIcon,
  // Unknown embeds should not be responsive by default.
  responsive: false,
  transforms: {
    from: [{
      type: 'raw',
      isMatch: function isMatch(node) {
        return node.nodeName === 'P' && /^\s*(https?:\/\/\S+)\s*$/i.test(node.textContent);
      },
      transform: function transform(node) {
        return Object(external_this_wp_blocks_["createBlock"])('core/embed', {
          url: node.textContent.trim()
        });
      }
    }]
  }
});
var embed_common = common.map(function (embedDefinition) {
  return Object(objectSpread["a" /* default */])({}, embedDefinition, {
    settings: getEmbedBlockSettings(embedDefinition.settings)
  });
});
var embed_others = others.map(function (embedDefinition) {
  return Object(objectSpread["a" /* default */])({}, embedDefinition, {
    settings: getEmbedBlockSettings(embedDefinition.settings)
  });
});

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var file_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M9.17 6l2 2H20v10H4V6h5.17M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/inspector.js


/**
 * WordPress dependencies
 */



function FileBlockInspector(_ref) {
  var hrefs = _ref.hrefs,
      openInNewWindow = _ref.openInNewWindow,
      showDownloadButton = _ref.showDownloadButton,
      changeLinkDestinationOption = _ref.changeLinkDestinationOption,
      changeOpenInNewWindow = _ref.changeOpenInNewWindow,
      changeShowDownloadButton = _ref.changeShowDownloadButton;
  var href = hrefs.href,
      textLinkHref = hrefs.textLinkHref,
      attachmentPage = hrefs.attachmentPage;
  var linkDestinationOptions = [{
    value: href,
    label: Object(external_this_wp_i18n_["__"])('URL')
  }];

  if (attachmentPage) {
    linkDestinationOptions = [{
      value: href,
      label: Object(external_this_wp_i18n_["__"])('Media File')
    }, {
      value: attachmentPage,
      label: Object(external_this_wp_i18n_["__"])('Attachment page')
    }];
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Text link settings')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
    label: Object(external_this_wp_i18n_["__"])('Link To'),
    value: textLinkHref,
    options: linkDestinationOptions,
    onChange: changeLinkDestinationOption
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
    label: Object(external_this_wp_i18n_["__"])('Open in new tab'),
    checked: openInNewWindow,
    onChange: changeOpenInNewWindow
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Download button settings')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
    label: Object(external_this_wp_i18n_["__"])('Show download button'),
    checked: showDownloadButton,
    onChange: changeShowDownloadButton
  }))));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/edit.js










/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */




var edit_FileEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(FileEdit, _Component);

  function FileEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, FileEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(FileEdit).apply(this, arguments));
    _this.onSelectFile = _this.onSelectFile.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.confirmCopyURL = _this.confirmCopyURL.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.resetCopyConfirmation = _this.resetCopyConfirmation.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.changeLinkDestinationOption = _this.changeLinkDestinationOption.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.changeOpenInNewWindow = _this.changeOpenInNewWindow.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.changeShowDownloadButton = _this.changeShowDownloadButton.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onUploadError = _this.onUploadError.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.state = {
      hasError: false,
      showCopyConfirmation: false
    };
    return _this;
  }

  Object(createClass["a" /* default */])(FileEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          mediaUpload = _this$props.mediaUpload,
          noticeOperations = _this$props.noticeOperations,
          setAttributes = _this$props.setAttributes;
      var downloadButtonText = attributes.downloadButtonText,
          href = attributes.href; // Upload a file drag-and-dropped into the editor

      if (Object(external_this_wp_blob_["isBlobURL"])(href)) {
        var file = Object(external_this_wp_blob_["getBlobByURL"])(href);
        mediaUpload({
          filesList: [file],
          onFileChange: function onFileChange(_ref) {
            var _ref2 = Object(slicedToArray["a" /* default */])(_ref, 1),
                media = _ref2[0];

            return _this2.onSelectFile(media);
          },
          onError: function onError(message) {
            _this2.setState({
              hasError: true
            });

            noticeOperations.createErrorNotice(message);
          }
        });
        Object(external_this_wp_blob_["revokeBlobURL"])(href);
      }

      if (downloadButtonText === undefined) {
        setAttributes({
          downloadButtonText: Object(external_this_wp_i18n_["_x"])('Download', 'button label')
        });
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      // Reset copy confirmation state when block is deselected
      if (prevProps.isSelected && !this.props.isSelected) {
        this.setState({
          showCopyConfirmation: false
        });
      }
    }
  }, {
    key: "onSelectFile",
    value: function onSelectFile(media) {
      if (media && media.url) {
        this.setState({
          hasError: false
        });
        this.props.setAttributes({
          href: media.url,
          fileName: media.title,
          textLinkHref: media.url,
          id: media.id
        });
      }
    }
  }, {
    key: "onUploadError",
    value: function onUploadError(message) {
      var noticeOperations = this.props.noticeOperations;
      noticeOperations.removeAllNotices();
      noticeOperations.createErrorNotice(message);
    }
  }, {
    key: "confirmCopyURL",
    value: function confirmCopyURL() {
      this.setState({
        showCopyConfirmation: true
      });
    }
  }, {
    key: "resetCopyConfirmation",
    value: function resetCopyConfirmation() {
      this.setState({
        showCopyConfirmation: false
      });
    }
  }, {
    key: "changeLinkDestinationOption",
    value: function changeLinkDestinationOption(newHref) {
      // Choose Media File or Attachment Page (when file is in Media Library)
      this.props.setAttributes({
        textLinkHref: newHref
      });
    }
  }, {
    key: "changeOpenInNewWindow",
    value: function changeOpenInNewWindow(newValue) {
      this.props.setAttributes({
        textLinkTarget: newValue ? '_blank' : false
      });
    }
  }, {
    key: "changeShowDownloadButton",
    value: function changeShowDownloadButton(newValue) {
      this.props.setAttributes({
        showDownloadButton: newValue
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this3 = this;

      var _this$props2 = this.props,
          className = _this$props2.className,
          isSelected = _this$props2.isSelected,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes,
          noticeUI = _this$props2.noticeUI,
          media = _this$props2.media;
      var fileName = attributes.fileName,
          href = attributes.href,
          textLinkHref = attributes.textLinkHref,
          textLinkTarget = attributes.textLinkTarget,
          showDownloadButton = attributes.showDownloadButton,
          downloadButtonText = attributes.downloadButtonText,
          id = attributes.id;
      var _this$state = this.state,
          hasError = _this$state.hasError,
          showCopyConfirmation = _this$state.showCopyConfirmation;
      var attachmentPage = media && media.link;

      if (!href || hasError) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaPlaceholder"], {
          icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
            icon: file_icon
          }),
          labels: {
            title: Object(external_this_wp_i18n_["__"])('File'),
            instructions: Object(external_this_wp_i18n_["__"])('Upload a file or pick one from your media library.')
          },
          onSelect: this.onSelectFile,
          notices: noticeUI,
          onError: this.onUploadError,
          accept: "*"
        });
      }

      var classes = classnames_default()(className, {
        'is-transient': Object(external_this_wp_blob_["isBlobURL"])(href)
      });
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(FileBlockInspector, Object(esm_extends["a" /* default */])({
        hrefs: {
          href: href,
          textLinkHref: textLinkHref,
          attachmentPage: attachmentPage
        }
      }, {
        openInNewWindow: !!textLinkTarget,
        showDownloadButton: showDownloadButton,
        changeLinkDestinationOption: this.changeLinkDestinationOption,
        changeOpenInNewWindow: this.changeOpenInNewWindow,
        changeShowDownloadButton: this.changeShowDownloadButton
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
        onSelect: this.onSelectFile,
        value: id,
        render: function render(_ref3) {
          var open = _ref3.open;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
            className: "components-toolbar__control",
            label: Object(external_this_wp_i18n_["__"])('Edit file'),
            onClick: open,
            icon: "edit"
          });
        }
      })))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Animate"], {
        type: Object(external_this_wp_blob_["isBlobURL"])(href) ? 'loading' : null
      }, function (_ref4) {
        var animateClassName = _ref4.className;
        return Object(external_this_wp_element_["createElement"])("div", {
          className: classnames_default()(classes, animateClassName)
        }, Object(external_this_wp_element_["createElement"])("div", {
          className: 'wp-block-file__content-wrapper'
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
          wrapperClassName: 'wp-block-file__textlink',
          tagName: "div" // must be block-level or else cursor disappears
          ,
          value: fileName,
          placeholder: Object(external_this_wp_i18n_["__"])('Write file name…'),
          withoutInteractiveFormatting: true,
          onChange: function onChange(text) {
            return setAttributes({
              fileName: text
            });
          }
        }), showDownloadButton && Object(external_this_wp_element_["createElement"])("div", {
          className: 'wp-block-file__button-richtext-wrapper'
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
          tagName: "div" // must be block-level or else cursor disappears
          ,
          className: 'wp-block-file__button',
          value: downloadButtonText,
          withoutInteractiveFormatting: true,
          placeholder: Object(external_this_wp_i18n_["__"])('Add text…'),
          onChange: function onChange(text) {
            return setAttributes({
              downloadButtonText: text
            });
          }
        }))), isSelected && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ClipboardButton"], {
          isDefault: true,
          text: href,
          className: 'wp-block-file__copy-url-button',
          onCopy: _this3.confirmCopyURL,
          onFinishCopy: _this3.resetCopyConfirmation,
          disabled: Object(external_this_wp_blob_["isBlobURL"])(href)
        }, showCopyConfirmation ? Object(external_this_wp_i18n_["__"])('Copied!') : Object(external_this_wp_i18n_["__"])('Copy URL')));
      }));
    }
  }]);

  return FileEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var file_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, props) {
  var _select = select('core'),
      getMedia = _select.getMedia;

  var _select2 = select('core/block-editor'),
      getSettings = _select2.getSettings;

  var _getSettings = getSettings(),
      __experimentalMediaUpload = _getSettings.__experimentalMediaUpload;

  var id = props.attributes.id;
  return {
    media: id === undefined ? undefined : getMedia(id),
    mediaUpload: __experimentalMediaUpload
  };
}), external_this_wp_components_["withNotices"]])(edit_FileEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/save.js


/**
 * WordPress dependencies
 */

function file_save_save(_ref) {
  var attributes = _ref.attributes;
  var href = attributes.href,
      fileName = attributes.fileName,
      textLinkHref = attributes.textLinkHref,
      textLinkTarget = attributes.textLinkTarget,
      showDownloadButton = attributes.showDownloadButton,
      downloadButtonText = attributes.downloadButtonText;
  return href && Object(external_this_wp_element_["createElement"])("div", null, !external_this_wp_blockEditor_["RichText"].isEmpty(fileName) && Object(external_this_wp_element_["createElement"])("a", {
    href: textLinkHref,
    target: textLinkTarget,
    rel: textLinkTarget ? 'noreferrer noopener' : false
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    value: fileName
  })), showDownloadButton && Object(external_this_wp_element_["createElement"])("a", {
    href: href,
    className: "wp-block-file__button",
    download: true
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    value: downloadButtonText
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/transforms.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




var file_transforms_transforms = {
  from: [{
    type: 'files',
    isMatch: function isMatch(files) {
      return files.length > 0;
    },
    // We define a lower priorty (higher number) than the default of 10. This
    // ensures that the File block is only created as a fallback.
    priority: 15,
    transform: function transform(files) {
      var blocks = [];
      files.forEach(function (file) {
        var blobURL = Object(external_this_wp_blob_["createBlobURL"])(file); // File will be uploaded in componentDidMount()

        blocks.push(Object(external_this_wp_blocks_["createBlock"])('core/file', {
          href: blobURL,
          fileName: file.name,
          textLinkHref: blobURL
        }));
      });
      return blocks;
    }
  }, {
    type: 'block',
    blocks: ['core/audio'],
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/file', {
        href: attributes.src,
        fileName: attributes.caption,
        textLinkHref: attributes.src,
        id: attributes.id
      });
    }
  }, {
    type: 'block',
    blocks: ['core/video'],
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/file', {
        href: attributes.src,
        fileName: attributes.caption,
        textLinkHref: attributes.src,
        id: attributes.id
      });
    }
  }, {
    type: 'block',
    blocks: ['core/image'],
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/file', {
        href: attributes.url,
        fileName: attributes.caption,
        textLinkHref: attributes.url,
        id: attributes.id
      });
    }
  }],
  to: [{
    type: 'block',
    blocks: ['core/audio'],
    isMatch: function isMatch(_ref) {
      var id = _ref.id;

      if (!id) {
        return false;
      }

      var _select = Object(external_this_wp_data_["select"])('core'),
          getMedia = _select.getMedia;

      var media = getMedia(id);
      return !!media && Object(external_lodash_["includes"])(media.mime_type, 'audio');
    },
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/audio', {
        src: attributes.href,
        caption: attributes.fileName,
        id: attributes.id
      });
    }
  }, {
    type: 'block',
    blocks: ['core/video'],
    isMatch: function isMatch(_ref2) {
      var id = _ref2.id;

      if (!id) {
        return false;
      }

      var _select2 = Object(external_this_wp_data_["select"])('core'),
          getMedia = _select2.getMedia;

      var media = getMedia(id);
      return !!media && Object(external_lodash_["includes"])(media.mime_type, 'video');
    },
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/video', {
        src: attributes.href,
        caption: attributes.fileName,
        id: attributes.id
      });
    }
  }, {
    type: 'block',
    blocks: ['core/image'],
    isMatch: function isMatch(_ref3) {
      var id = _ref3.id;

      if (!id) {
        return false;
      }

      var _select3 = Object(external_this_wp_data_["select"])('core'),
          getMedia = _select3.getMedia;

      var media = getMedia(id);
      return !!media && Object(external_lodash_["includes"])(media.mime_type, 'image');
    },
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/image', {
        url: attributes.href,
        caption: attributes.fileName,
        id: attributes.id
      });
    }
  }]
};
/* harmony default export */ var file_transforms = (file_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var file_metadata = {
  name: "core/file",
  category: "common",
  attributes: {
    id: {
      type: "number"
    },
    href: {
      type: "string"
    },
    fileName: {
      type: "string",
      source: "html",
      selector: "a:not([download])"
    },
    textLinkHref: {
      type: "string",
      source: "attribute",
      selector: "a:not([download])",
      attribute: "href"
    },
    textLinkTarget: {
      type: "string",
      source: "attribute",
      selector: "a:not([download])",
      attribute: "target"
    },
    showDownloadButton: {
      type: "boolean",
      "default": true
    },
    downloadButtonText: {
      type: "string",
      source: "html",
      selector: "a[download]"
    }
  }
};


var file_name = file_metadata.name;

var file_settings = {
  title: Object(external_this_wp_i18n_["__"])('File'),
  description: Object(external_this_wp_i18n_["__"])('Add a link to a downloadable file.'),
  icon: file_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('document'), Object(external_this_wp_i18n_["__"])('pdf')],
  supports: {
    align: true
  },
  transforms: file_transforms,
  edit: file_edit,
  save: file_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/html/edit.js









/**
 * WordPress dependencies
 */






var edit_HTMLEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(HTMLEdit, _Component);

  function HTMLEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, HTMLEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(HTMLEdit).apply(this, arguments));
    _this.state = {
      isPreview: false,
      styles: []
    };
    _this.switchToHTML = _this.switchToHTML.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.switchToPreview = _this.switchToPreview.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(HTMLEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var styles = this.props.styles; // Default styles used to unset some of the styles
      // that might be inherited from the editor style.

      var defaultStyles = "\n\t\t\thtml,body,:root {\n\t\t\t\tmargin: 0 !important;\n\t\t\t\tpadding: 0 !important;\n\t\t\t\toverflow: visible !important;\n\t\t\t\tmin-height: auto !important;\n\t\t\t}\n\t\t";
      this.setState({
        styles: [defaultStyles].concat(Object(toConsumableArray["a" /* default */])(Object(external_this_wp_blockEditor_["transformStyles"])(styles)))
      });
    }
  }, {
    key: "switchToPreview",
    value: function switchToPreview() {
      this.setState({
        isPreview: true
      });
    }
  }, {
    key: "switchToHTML",
    value: function switchToHTML() {
      this.setState({
        isPreview: false
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      var _this$state = this.state,
          isPreview = _this$state.isPreview,
          styles = _this$state.styles;
      return Object(external_this_wp_element_["createElement"])("div", {
        className: "wp-block-html"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])("div", {
        className: "components-toolbar"
      }, Object(external_this_wp_element_["createElement"])("button", {
        className: "components-tab-button ".concat(!isPreview ? 'is-active' : ''),
        onClick: this.switchToHTML
      }, Object(external_this_wp_element_["createElement"])("span", null, "HTML")), Object(external_this_wp_element_["createElement"])("button", {
        className: "components-tab-button ".concat(isPreview ? 'is-active' : ''),
        onClick: this.switchToPreview
      }, Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_i18n_["__"])('Preview'))))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"].Consumer, null, function (isDisabled) {
        return isPreview || isDisabled ? Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SandBox"], {
          html: attributes.content,
          styles: styles
        }) : Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PlainText"], {
          value: attributes.content,
          onChange: function onChange(content) {
            return setAttributes({
              content: content
            });
          },
          placeholder: Object(external_this_wp_i18n_["__"])('Write HTML…'),
          "aria-label": Object(external_this_wp_i18n_["__"])('HTML')
        });
      }));
    }
  }]);

  return HTMLEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var html_edit = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getSettings = _select.getSettings;

  return {
    styles: getSettings().styles
  };
})(edit_HTMLEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/html/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var html_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M4.5,11h-2V9H1v6h1.5v-2.5h2V15H6V9H4.5V11z M7,10.5h1.5V15H10v-4.5h1.5V9H7V10.5z M14.5,10l-1-1H12v6h1.5v-3.9  l1,1l1-1V15H17V9h-1.5L14.5,10z M19.5,13.5V9H18v6h5v-1.5H19.5z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/html/save.js


/**
 * WordPress dependencies
 */

function html_save_save(_ref) {
  var attributes = _ref.attributes;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, attributes.content);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/html/transforms.js
/**
 * WordPress dependencies
 */

var html_transforms_transforms = {
  from: [{
    type: 'raw',
    isMatch: function isMatch(node) {
      return node.nodeName === 'FIGURE' && !!node.querySelector('iframe');
    },
    schema: {
      figure: {
        require: ['iframe'],
        children: {
          iframe: {
            attributes: ['src', 'allowfullscreen', 'height', 'width']
          },
          figcaption: {
            children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
          }
        }
      }
    }
  }]
};
/* harmony default export */ var html_transforms = (html_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/html/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var html_metadata = {
  name: "core/html",
  category: "formatting",
  attributes: {
    content: {
      type: "string",
      source: "html"
    }
  }
};


var html_name = html_metadata.name;

var html_settings = {
  title: Object(external_this_wp_i18n_["__"])('Custom HTML'),
  description: Object(external_this_wp_i18n_["__"])('Add custom HTML code and preview it as you edit.'),
  icon: html_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('embed')],
  example: {
    attributes: {
      content: '<marquee>' + Object(external_this_wp_i18n_["__"])('Welcome to the wonderful world of blocks…') + '</marquee>'
    }
  },
  supports: {
    customClassName: false,
    className: false,
    html: false
  },
  transforms: html_transforms,
  edit: html_edit,
  save: html_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/deprecated.js



/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


var DEFAULT_MEDIA_WIDTH = 50;
/* harmony default export */ var media_text_deprecated = ([{
  attributes: {
    align: {
      type: 'string',
      default: 'wide'
    },
    backgroundColor: {
      type: 'string'
    },
    customBackgroundColor: {
      type: 'string'
    },
    mediaAlt: {
      type: 'string',
      source: 'attribute',
      selector: 'figure img',
      attribute: 'alt',
      default: ''
    },
    mediaPosition: {
      type: 'string',
      default: 'left'
    },
    mediaId: {
      type: 'number'
    },
    mediaUrl: {
      type: 'string',
      source: 'attribute',
      selector: 'figure video,figure img',
      attribute: 'src'
    },
    mediaType: {
      type: 'string'
    },
    mediaWidth: {
      type: 'number',
      default: 50
    },
    isStackedOnMobile: {
      type: 'boolean',
      default: false
    }
  },
  save: function save(_ref) {
    var _classnames;

    var attributes = _ref.attributes;
    var backgroundColor = attributes.backgroundColor,
        customBackgroundColor = attributes.customBackgroundColor,
        isStackedOnMobile = attributes.isStackedOnMobile,
        mediaAlt = attributes.mediaAlt,
        mediaPosition = attributes.mediaPosition,
        mediaType = attributes.mediaType,
        mediaUrl = attributes.mediaUrl,
        mediaWidth = attributes.mediaWidth;
    var mediaTypeRenders = {
      image: function image() {
        return Object(external_this_wp_element_["createElement"])("img", {
          src: mediaUrl,
          alt: mediaAlt
        });
      },
      video: function video() {
        return Object(external_this_wp_element_["createElement"])("video", {
          controls: true,
          src: mediaUrl
        });
      }
    };
    var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
    var className = classnames_default()((_classnames = {
      'has-media-on-the-right': 'right' === mediaPosition
    }, Object(defineProperty["a" /* default */])(_classnames, backgroundClass, backgroundClass), Object(defineProperty["a" /* default */])(_classnames, 'is-stacked-on-mobile', isStackedOnMobile), _classnames));
    var gridTemplateColumns;

    if (mediaWidth !== DEFAULT_MEDIA_WIDTH) {
      gridTemplateColumns = 'right' === mediaPosition ? "auto ".concat(mediaWidth, "%") : "".concat(mediaWidth, "% auto");
    }

    var style = {
      backgroundColor: backgroundClass ? undefined : customBackgroundColor,
      gridTemplateColumns: gridTemplateColumns
    };
    return Object(external_this_wp_element_["createElement"])("div", {
      className: className,
      style: style
    }, Object(external_this_wp_element_["createElement"])("figure", {
      className: "wp-block-media-text__media"
    }, (mediaTypeRenders[mediaType] || external_lodash_["noop"])()), Object(external_this_wp_element_["createElement"])("div", {
      className: "wp-block-media-text__content"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].Content, null)));
  }
}]);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/media-container-icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var media_container_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M18 2l2 4h-2l-2-4h-3l2 4h-2l-2-4h-1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V2zm2 12H10V4.4L11.8 8H20z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M14 20H4V10h3V8H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3h-2z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M5 19h8l-1.59-2H9.24l-.84 1.1L7 16.3 5 19z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/media-container.js








/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


/**
 * Constants
 */

var media_container_ALLOWED_MEDIA_TYPES = ['image', 'video'];
function imageFillStyles(url, focalPoint) {
  return url ? {
    backgroundImage: "url(".concat(url, ")"),
    backgroundPosition: focalPoint ? "".concat(focalPoint.x * 100, "% ").concat(focalPoint.y * 100, "%") : "50% 50%"
  } : {};
}

var media_container_MediaContainer =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MediaContainer, _Component);

  function MediaContainer() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, MediaContainer);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MediaContainer).apply(this, arguments));
    _this.onUploadError = _this.onUploadError.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(MediaContainer, [{
    key: "onUploadError",
    value: function onUploadError(message) {
      var noticeOperations = this.props.noticeOperations;
      noticeOperations.removeAllNotices();
      noticeOperations.createErrorNotice(message);
    }
  }, {
    key: "renderToolbarEditButton",
    value: function renderToolbarEditButton() {
      var _this$props = this.props,
          mediaId = _this$props.mediaId,
          onSelectMedia = _this$props.onSelectMedia;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
        onSelect: onSelectMedia,
        allowedTypes: media_container_ALLOWED_MEDIA_TYPES,
        value: mediaId,
        render: function render(_ref) {
          var open = _ref.open;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
            className: "components-toolbar__control",
            label: Object(external_this_wp_i18n_["__"])('Edit media'),
            icon: "edit",
            onClick: open
          });
        }
      })));
    }
  }, {
    key: "renderImage",
    value: function renderImage() {
      var _this$props2 = this.props,
          mediaAlt = _this$props2.mediaAlt,
          mediaUrl = _this$props2.mediaUrl,
          className = _this$props2.className,
          imageFill = _this$props2.imageFill,
          focalPoint = _this$props2.focalPoint;
      var backgroundStyles = imageFill ? imageFillStyles(mediaUrl, focalPoint) : {};
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, this.renderToolbarEditButton(), Object(external_this_wp_element_["createElement"])("figure", {
        className: className,
        style: backgroundStyles
      }, Object(external_this_wp_element_["createElement"])("img", {
        src: mediaUrl,
        alt: mediaAlt
      })));
    }
  }, {
    key: "renderVideo",
    value: function renderVideo() {
      var _this$props3 = this.props,
          mediaUrl = _this$props3.mediaUrl,
          className = _this$props3.className;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, this.renderToolbarEditButton(), Object(external_this_wp_element_["createElement"])("figure", {
        className: className
      }, Object(external_this_wp_element_["createElement"])("video", {
        controls: true,
        src: mediaUrl
      })));
    }
  }, {
    key: "renderPlaceholder",
    value: function renderPlaceholder() {
      var _this$props4 = this.props,
          onSelectMedia = _this$props4.onSelectMedia,
          className = _this$props4.className,
          noticeUI = _this$props4.noticeUI;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaPlaceholder"], {
        icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
          icon: media_container_icon
        }),
        labels: {
          title: Object(external_this_wp_i18n_["__"])('Media area')
        },
        className: className,
        onSelect: onSelectMedia,
        accept: "image/*,video/*",
        allowedTypes: media_container_ALLOWED_MEDIA_TYPES,
        notices: noticeUI,
        onError: this.onUploadError
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props5 = this.props,
          mediaPosition = _this$props5.mediaPosition,
          mediaUrl = _this$props5.mediaUrl,
          mediaType = _this$props5.mediaType,
          mediaWidth = _this$props5.mediaWidth,
          commitWidthChange = _this$props5.commitWidthChange,
          onWidthChange = _this$props5.onWidthChange,
          toggleSelection = _this$props5.toggleSelection;

      if (mediaType && mediaUrl) {
        var onResizeStart = function onResizeStart() {
          toggleSelection(false);
        };

        var onResize = function onResize(event, direction, elt) {
          onWidthChange(parseInt(elt.style.width));
        };

        var onResizeStop = function onResizeStop(event, direction, elt) {
          toggleSelection(true);
          commitWidthChange(parseInt(elt.style.width));
        };

        var enablePositions = {
          right: mediaPosition === 'left',
          left: mediaPosition === 'right'
        };
        var mediaElement = null;

        switch (mediaType) {
          case 'image':
            mediaElement = this.renderImage();
            break;

          case 'video':
            mediaElement = this.renderVideo();
            break;
        }

        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ResizableBox"], {
          className: "editor-media-container__resizer",
          size: {
            width: mediaWidth + '%'
          },
          minWidth: "10%",
          maxWidth: "100%",
          enable: enablePositions,
          onResizeStart: onResizeStart,
          onResize: onResize,
          onResizeStop: onResizeStop,
          axis: "x"
        }, mediaElement);
      }

      return this.renderPlaceholder();
    }
  }]);

  return MediaContainer;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var media_container = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/block-editor'),
      toggleSelection = _dispatch.toggleSelection;

  return {
    toggleSelection: toggleSelection
  };
}), external_this_wp_components_["withNotices"]])(media_container_MediaContainer));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/edit.js










/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Constants
 */

var TEMPLATE = [['core/paragraph', {
  fontSize: 'large',
  placeholder: Object(external_this_wp_i18n_["_x"])('Content…', 'content placeholder')
}]]; // this limits the resize to a safe zone to avoid making broken layouts

var WIDTH_CONSTRAINT_PERCENTAGE = 15;

var applyWidthConstraints = function applyWidthConstraints(width) {
  return Math.max(WIDTH_CONSTRAINT_PERCENTAGE, Math.min(width, 100 - WIDTH_CONSTRAINT_PERCENTAGE));
};

var edit_MediaTextEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MediaTextEdit, _Component);

  function MediaTextEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, MediaTextEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MediaTextEdit).apply(this, arguments));
    _this.onSelectMedia = _this.onSelectMedia.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onWidthChange = _this.onWidthChange.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.commitWidthChange = _this.commitWidthChange.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.state = {
      mediaWidth: null
    };
    return _this;
  }

  Object(createClass["a" /* default */])(MediaTextEdit, [{
    key: "onSelectMedia",
    value: function onSelectMedia(media) {
      var setAttributes = this.props.setAttributes;
      var mediaType;
      var src; // for media selections originated from a file upload.

      if (media.media_type) {
        if (media.media_type === 'image') {
          mediaType = 'image';
        } else {
          // only images and videos are accepted so if the media_type is not an image we can assume it is a video.
          // video contain the media type of 'file' in the object returned from the rest api.
          mediaType = 'video';
        }
      } else {
        // for media selections originated from existing files in the media library.
        mediaType = media.type;
      }

      if (mediaType === 'image') {
        // Try the "large" size URL, falling back to the "full" size URL below.
        src = Object(external_lodash_["get"])(media, ['sizes', 'large', 'url']) || Object(external_lodash_["get"])(media, ['media_details', 'sizes', 'large', 'source_url']);
      }

      setAttributes({
        mediaAlt: media.alt,
        mediaId: media.id,
        mediaType: mediaType,
        mediaUrl: src || media.url,
        imageFill: undefined,
        focalPoint: undefined
      });
    }
  }, {
    key: "onWidthChange",
    value: function onWidthChange(width) {
      this.setState({
        mediaWidth: applyWidthConstraints(width)
      });
    }
  }, {
    key: "commitWidthChange",
    value: function commitWidthChange(width) {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        mediaWidth: applyWidthConstraints(width)
      });
      this.setState({
        mediaWidth: null
      });
    }
  }, {
    key: "renderMediaArea",
    value: function renderMediaArea() {
      var attributes = this.props.attributes;
      var mediaAlt = attributes.mediaAlt,
          mediaId = attributes.mediaId,
          mediaPosition = attributes.mediaPosition,
          mediaType = attributes.mediaType,
          mediaUrl = attributes.mediaUrl,
          mediaWidth = attributes.mediaWidth,
          imageFill = attributes.imageFill,
          focalPoint = attributes.focalPoint;
      return Object(external_this_wp_element_["createElement"])(media_container, Object(esm_extends["a" /* default */])({
        className: "block-library-media-text__media-container",
        onSelectMedia: this.onSelectMedia,
        onWidthChange: this.onWidthChange,
        commitWidthChange: this.commitWidthChange
      }, {
        mediaAlt: mediaAlt,
        mediaId: mediaId,
        mediaType: mediaType,
        mediaUrl: mediaUrl,
        mediaPosition: mediaPosition,
        mediaWidth: mediaWidth,
        imageFill: imageFill,
        focalPoint: focalPoint
      }));
    }
  }, {
    key: "render",
    value: function render() {
      var _classnames;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          className = _this$props.className,
          backgroundColor = _this$props.backgroundColor,
          isSelected = _this$props.isSelected,
          setAttributes = _this$props.setAttributes,
          setBackgroundColor = _this$props.setBackgroundColor;
      var isStackedOnMobile = attributes.isStackedOnMobile,
          mediaAlt = attributes.mediaAlt,
          mediaPosition = attributes.mediaPosition,
          mediaType = attributes.mediaType,
          mediaWidth = attributes.mediaWidth,
          verticalAlignment = attributes.verticalAlignment,
          mediaUrl = attributes.mediaUrl,
          imageFill = attributes.imageFill,
          focalPoint = attributes.focalPoint;
      var temporaryMediaWidth = this.state.mediaWidth;
      var classNames = classnames_default()(className, (_classnames = {
        'has-media-on-the-right': 'right' === mediaPosition,
        'is-selected': isSelected,
        'has-background': backgroundColor.class || backgroundColor.color
      }, Object(defineProperty["a" /* default */])(_classnames, backgroundColor.class, backgroundColor.class), Object(defineProperty["a" /* default */])(_classnames, 'is-stacked-on-mobile', isStackedOnMobile), Object(defineProperty["a" /* default */])(_classnames, "is-vertically-aligned-".concat(verticalAlignment), verticalAlignment), Object(defineProperty["a" /* default */])(_classnames, 'is-image-fill', imageFill), _classnames));
      var widthString = "".concat(temporaryMediaWidth || mediaWidth, "%");
      var gridTemplateColumns = 'right' === mediaPosition ? "1fr ".concat(widthString) : "".concat(widthString, " 1fr");
      var style = {
        gridTemplateColumns: gridTemplateColumns,
        msGridColumns: gridTemplateColumns,
        backgroundColor: backgroundColor.color
      };
      var colorSettings = [{
        value: backgroundColor.color,
        onChange: setBackgroundColor,
        label: Object(external_this_wp_i18n_["__"])('Background Color')
      }];
      var toolbarControls = [{
        icon: 'align-pull-left',
        title: Object(external_this_wp_i18n_["__"])('Show media on left'),
        isActive: mediaPosition === 'left',
        onClick: function onClick() {
          return setAttributes({
            mediaPosition: 'left'
          });
        }
      }, {
        icon: 'align-pull-right',
        title: Object(external_this_wp_i18n_["__"])('Show media on right'),
        isActive: mediaPosition === 'right',
        onClick: function onClick() {
          return setAttributes({
            mediaPosition: 'right'
          });
        }
      }];

      var onMediaAltChange = function onMediaAltChange(newMediaAlt) {
        setAttributes({
          mediaAlt: newMediaAlt
        });
      };

      var onVerticalAlignmentChange = function onVerticalAlignmentChange(alignment) {
        setAttributes({
          verticalAlignment: alignment
        });
      };

      var mediaTextGeneralSettings = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Media & Text Settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Stack on mobile'),
        checked: isStackedOnMobile,
        onChange: function onChange() {
          return setAttributes({
            isStackedOnMobile: !isStackedOnMobile
          });
        }
      }), mediaType === 'image' && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Crop image to fill entire column'),
        checked: imageFill,
        onChange: function onChange() {
          return setAttributes({
            imageFill: !imageFill
          });
        }
      }), imageFill && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["FocalPointPicker"], {
        label: Object(external_this_wp_i18n_["__"])('Focal Point Picker'),
        url: mediaUrl,
        value: focalPoint,
        onChange: function onChange(value) {
          return setAttributes({
            focalPoint: value
          });
        }
      }), mediaType === 'image' && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextareaControl"], {
        label: Object(external_this_wp_i18n_["__"])('Alt Text (Alternative Text)'),
        value: mediaAlt,
        onChange: onMediaAltChange,
        help: Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
          href: "https://www.w3.org/WAI/tutorials/images/decision-tree"
        }, Object(external_this_wp_i18n_["__"])('Describe the purpose of the image')), Object(external_this_wp_i18n_["__"])('Leave empty if the image is purely decorative.'))
      }));
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, mediaTextGeneralSettings, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
        title: Object(external_this_wp_i18n_["__"])('Color Settings'),
        initialOpen: false,
        colorSettings: colorSettings
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
        controls: toolbarControls
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockVerticalAlignmentToolbar"], {
        onChange: onVerticalAlignmentChange,
        value: verticalAlignment
      })), Object(external_this_wp_element_["createElement"])("div", {
        className: classNames,
        style: style
      }, this.renderMediaArea(), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"], {
        template: TEMPLATE,
        templateInsertUpdatesSelection: false
      })));
    }
  }]);

  return MediaTextEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var media_text_edit = (Object(external_this_wp_blockEditor_["withColors"])('backgroundColor')(edit_MediaTextEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var media_text_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M13 17h8v-2h-8v2zM3 19h8V5H3v14zM13 9h8V7h-8v2zm0 4h8v-2h-8v2z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/save.js



/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var save_DEFAULT_MEDIA_WIDTH = 50;
function media_text_save_save(_ref) {
  var _classnames;

  var attributes = _ref.attributes;
  var backgroundColor = attributes.backgroundColor,
      customBackgroundColor = attributes.customBackgroundColor,
      isStackedOnMobile = attributes.isStackedOnMobile,
      mediaAlt = attributes.mediaAlt,
      mediaPosition = attributes.mediaPosition,
      mediaType = attributes.mediaType,
      mediaUrl = attributes.mediaUrl,
      mediaWidth = attributes.mediaWidth,
      mediaId = attributes.mediaId,
      verticalAlignment = attributes.verticalAlignment,
      imageFill = attributes.imageFill,
      focalPoint = attributes.focalPoint;
  var mediaTypeRenders = {
    image: function image() {
      return Object(external_this_wp_element_["createElement"])("img", {
        src: mediaUrl,
        alt: mediaAlt,
        className: mediaId && mediaType === 'image' ? "wp-image-".concat(mediaId) : null
      });
    },
    video: function video() {
      return Object(external_this_wp_element_["createElement"])("video", {
        controls: true,
        src: mediaUrl
      });
    }
  };
  var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
  var className = classnames_default()((_classnames = {
    'has-media-on-the-right': 'right' === mediaPosition,
    'has-background': backgroundClass || customBackgroundColor
  }, Object(defineProperty["a" /* default */])(_classnames, backgroundClass, backgroundClass), Object(defineProperty["a" /* default */])(_classnames, 'is-stacked-on-mobile', isStackedOnMobile), Object(defineProperty["a" /* default */])(_classnames, "is-vertically-aligned-".concat(verticalAlignment), verticalAlignment), Object(defineProperty["a" /* default */])(_classnames, 'is-image-fill', imageFill), _classnames));
  var backgroundStyles = imageFill ? imageFillStyles(mediaUrl, focalPoint) : {};
  var gridTemplateColumns;

  if (mediaWidth !== save_DEFAULT_MEDIA_WIDTH) {
    gridTemplateColumns = 'right' === mediaPosition ? "auto ".concat(mediaWidth, "%") : "".concat(mediaWidth, "% auto");
  }

  var style = {
    backgroundColor: backgroundClass ? undefined : customBackgroundColor,
    gridTemplateColumns: gridTemplateColumns
  };
  return Object(external_this_wp_element_["createElement"])("div", {
    className: className,
    style: style
  }, Object(external_this_wp_element_["createElement"])("figure", {
    className: "wp-block-media-text__media",
    style: backgroundStyles
  }, (mediaTypeRenders[mediaType] || external_lodash_["noop"])()), Object(external_this_wp_element_["createElement"])("div", {
    className: "wp-block-media-text__content"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].Content, null)));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/transforms.js
/**
 * WordPress dependencies
 */

var media_text_transforms_transforms = {
  from: [{
    type: 'block',
    blocks: ['core/image'],
    transform: function transform(_ref) {
      var alt = _ref.alt,
          url = _ref.url,
          id = _ref.id;
      return Object(external_this_wp_blocks_["createBlock"])('core/media-text', {
        mediaAlt: alt,
        mediaId: id,
        mediaUrl: url,
        mediaType: 'image'
      });
    }
  }, {
    type: 'block',
    blocks: ['core/video'],
    transform: function transform(_ref2) {
      var src = _ref2.src,
          id = _ref2.id;
      return Object(external_this_wp_blocks_["createBlock"])('core/media-text', {
        mediaId: id,
        mediaUrl: src,
        mediaType: 'video'
      });
    }
  }],
  to: [{
    type: 'block',
    blocks: ['core/image'],
    isMatch: function isMatch(_ref3) {
      var mediaType = _ref3.mediaType,
          mediaUrl = _ref3.mediaUrl;
      return !mediaUrl || mediaType === 'image';
    },
    transform: function transform(_ref4) {
      var mediaAlt = _ref4.mediaAlt,
          mediaId = _ref4.mediaId,
          mediaUrl = _ref4.mediaUrl;
      return Object(external_this_wp_blocks_["createBlock"])('core/image', {
        alt: mediaAlt,
        id: mediaId,
        url: mediaUrl
      });
    }
  }, {
    type: 'block',
    blocks: ['core/video'],
    isMatch: function isMatch(_ref5) {
      var mediaType = _ref5.mediaType,
          mediaUrl = _ref5.mediaUrl;
      return !mediaUrl || mediaType === 'video';
    },
    transform: function transform(_ref6) {
      var mediaId = _ref6.mediaId,
          mediaUrl = _ref6.mediaUrl;
      return Object(external_this_wp_blocks_["createBlock"])('core/video', {
        id: mediaId,
        src: mediaUrl
      });
    }
  }]
};
/* harmony default export */ var media_text_transforms = (media_text_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var media_text_metadata = {
  name: "core/media-text",
  category: "layout",
  attributes: {
    align: {
      type: "string",
      "default": "wide"
    },
    backgroundColor: {
      type: "string"
    },
    customBackgroundColor: {
      type: "string"
    },
    mediaAlt: {
      type: "string",
      source: "attribute",
      selector: "figure img",
      attribute: "alt",
      "default": ""
    },
    mediaPosition: {
      type: "string",
      "default": "left"
    },
    mediaId: {
      type: "number"
    },
    mediaUrl: {
      type: "string",
      source: "attribute",
      selector: "figure video,figure img",
      attribute: "src"
    },
    mediaType: {
      type: "string"
    },
    mediaWidth: {
      type: "number",
      "default": 50
    },
    isStackedOnMobile: {
      type: "boolean",
      "default": false
    },
    verticalAlignment: {
      type: "string"
    },
    imageFill: {
      type: "boolean"
    },
    focalPoint: {
      type: "object"
    }
  }
};


var media_text_name = media_text_metadata.name;

var media_text_settings = {
  title: Object(external_this_wp_i18n_["__"])('Media & Text'),
  description: Object(external_this_wp_i18n_["__"])('Set media and words side-by-side for a richer layout.'),
  icon: media_text_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('image'), Object(external_this_wp_i18n_["__"])('video')],
  supports: {
    align: ['wide', 'full'],
    html: false
  },
  example: {
    attributes: {
      mediaType: 'image',
      mediaUrl: 'https://s.w.org/images/core/5.3/Biologia_Centrali-Americana_-_Cantorchilus_semibadius_1902.jpg'
    },
    innerBlocks: [{
      name: 'core/paragraph',
      attributes: {
        content: Object(external_this_wp_i18n_["__"])('The wren<br>Earns his living<br>Noiselessly.')
      }
    }, {
      name: 'core/paragraph',
      attributes: {
        content: Object(external_this_wp_i18n_["__"])('— Kobayashi Issa (一茶)')
      }
    }]
  },
  transforms: media_text_transforms,
  edit: media_text_edit,
  save: media_text_save_save,
  deprecated: media_text_deprecated
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/latest-comments/edit.js









/**
 * WordPress dependencies
 */





/**
 * Minimum number of comments a user can show using this block.
 *
 * @type {number}
 */

var MIN_COMMENTS = 1;
/**
 * Maximum number of comments a user can show using this block.
 *
 * @type {number}
 */

var MAX_COMMENTS = 100;

var edit_LatestComments =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(LatestComments, _Component);

  function LatestComments() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, LatestComments);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(LatestComments).apply(this, arguments));
    _this.setCommentsToShow = _this.setCommentsToShow.bind(Object(assertThisInitialized["a" /* default */])(_this)); // Create toggles for each attribute; we create them here rather than
    // passing `this.createToggleAttribute( 'displayAvatar' )` directly to
    // `onChange` to avoid re-renders.

    _this.toggleDisplayAvatar = _this.createToggleAttribute('displayAvatar');
    _this.toggleDisplayDate = _this.createToggleAttribute('displayDate');
    _this.toggleDisplayExcerpt = _this.createToggleAttribute('displayExcerpt');
    return _this;
  }

  Object(createClass["a" /* default */])(LatestComments, [{
    key: "createToggleAttribute",
    value: function createToggleAttribute(propName) {
      var _this2 = this;

      return function () {
        var value = _this2.props.attributes[propName];
        var setAttributes = _this2.props.setAttributes;
        setAttributes(Object(defineProperty["a" /* default */])({}, propName, !value));
      };
    }
  }, {
    key: "setCommentsToShow",
    value: function setCommentsToShow(commentsToShow) {
      this.props.setAttributes({
        commentsToShow: commentsToShow
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props$attribute = this.props.attributes,
          commentsToShow = _this$props$attribute.commentsToShow,
          displayAvatar = _this$props$attribute.displayAvatar,
          displayDate = _this$props$attribute.displayDate,
          displayExcerpt = _this$props$attribute.displayExcerpt;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Latest Comments Settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Display Avatar'),
        checked: displayAvatar,
        onChange: this.toggleDisplayAvatar
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Display Date'),
        checked: displayDate,
        onChange: this.toggleDisplayDate
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Display Excerpt'),
        checked: displayExcerpt,
        onChange: this.toggleDisplayExcerpt
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
        label: Object(external_this_wp_i18n_["__"])('Number of Comments'),
        value: commentsToShow,
        onChange: this.setCommentsToShow,
        min: MIN_COMMENTS,
        max: MAX_COMMENTS,
        required: true
      }))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_serverSideRender_default.a, {
        block: "core/latest-comments",
        attributes: this.props.attributes
      })));
    }
  }]);

  return LatestComments;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var latest_comments_edit = (edit_LatestComments);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/latest-comments/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var latest_comments_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM20 4v13.17L18.83 16H4V4h16zM6 12h12v2H6zm0-3h12v2H6zm0-3h12v2H6z"
}))));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/latest-comments/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var latest_comments_name = 'core/latest-comments';
var latest_comments_settings = {
  title: Object(external_this_wp_i18n_["__"])('Latest Comments'),
  description: Object(external_this_wp_i18n_["__"])('Display a list of your most recent comments.'),
  icon: latest_comments_icon,
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('recent comments')],
  supports: {
    align: true,
    html: false
  },
  edit: latest_comments_edit
};

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(34);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// EXTERNAL MODULE: external {"this":["wp","date"]}
var external_this_wp_date_ = __webpack_require__(53);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/latest-posts/edit.js









/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */









/**
 * Module Constants
 */

var CATEGORIES_LIST_QUERY = {
  per_page: -1
};
var MAX_POSTS_COLUMNS = 6;

var edit_LatestPostsEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(LatestPostsEdit, _Component);

  function LatestPostsEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, LatestPostsEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(LatestPostsEdit).apply(this, arguments));
    _this.state = {
      categoriesList: []
    };
    return _this;
  }

  Object(createClass["a" /* default */])(LatestPostsEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      this.isStillMounted = true;
      this.fetchRequest = external_this_wp_apiFetch_default()({
        path: Object(external_this_wp_url_["addQueryArgs"])("/wp/v2/categories", CATEGORIES_LIST_QUERY)
      }).then(function (categoriesList) {
        if (_this2.isStillMounted) {
          _this2.setState({
            categoriesList: categoriesList
          });
        }
      }).catch(function () {
        if (_this2.isStillMounted) {
          _this2.setState({
            categoriesList: []
          });
        }
      });
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.isStillMounted = false;
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes,
          latestPosts = _this$props.latestPosts;
      var categoriesList = this.state.categoriesList;
      var displayPostContentRadio = attributes.displayPostContentRadio,
          displayPostContent = attributes.displayPostContent,
          displayPostDate = attributes.displayPostDate,
          postLayout = attributes.postLayout,
          columns = attributes.columns,
          order = attributes.order,
          orderBy = attributes.orderBy,
          categories = attributes.categories,
          postsToShow = attributes.postsToShow,
          excerptLength = attributes.excerptLength;
      var inspectorControls = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Post Content Settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Post Content'),
        checked: displayPostContent,
        onChange: function onChange(value) {
          return setAttributes({
            displayPostContent: value
          });
        }
      }), displayPostContent && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RadioControl"], {
        label: "Show:",
        selected: displayPostContentRadio,
        options: [{
          label: 'Excerpt',
          value: 'excerpt'
        }, {
          label: 'Full Post',
          value: 'full_post'
        }],
        onChange: function onChange(value) {
          return setAttributes({
            displayPostContentRadio: value
          });
        }
      }), displayPostContent && displayPostContentRadio === 'excerpt' && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
        label: Object(external_this_wp_i18n_["__"])('Max number of words in excerpt'),
        value: excerptLength,
        onChange: function onChange(value) {
          return setAttributes({
            excerptLength: value
          });
        },
        min: 10,
        max: 100
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Post Meta Settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Display post date'),
        checked: displayPostDate,
        onChange: function onChange(value) {
          return setAttributes({
            displayPostDate: value
          });
        }
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Sorting and Filtering')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["QueryControls"], Object(esm_extends["a" /* default */])({
        order: order,
        orderBy: orderBy
      }, {
        numberOfItems: postsToShow,
        categoriesList: categoriesList,
        selectedCategoryId: categories,
        onOrderChange: function onOrderChange(value) {
          return setAttributes({
            order: value
          });
        },
        onOrderByChange: function onOrderByChange(value) {
          return setAttributes({
            orderBy: value
          });
        },
        onCategoryChange: function onCategoryChange(value) {
          return setAttributes({
            categories: '' !== value ? value : undefined
          });
        },
        onNumberOfItemsChange: function onNumberOfItemsChange(value) {
          return setAttributes({
            postsToShow: value
          });
        }
      })), postLayout === 'grid' && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
        label: Object(external_this_wp_i18n_["__"])('Columns'),
        value: columns,
        onChange: function onChange(value) {
          return setAttributes({
            columns: value
          });
        },
        min: 2,
        max: !hasPosts ? MAX_POSTS_COLUMNS : Math.min(MAX_POSTS_COLUMNS, latestPosts.length),
        required: true
      })));
      var hasPosts = Array.isArray(latestPosts) && latestPosts.length;

      if (!hasPosts) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, inspectorControls, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], {
          icon: "admin-post",
          label: Object(external_this_wp_i18n_["__"])('Latest Posts')
        }, !Array.isArray(latestPosts) ? Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null) : Object(external_this_wp_i18n_["__"])('No posts found.')));
      } // Removing posts from display should be instant.


      var displayPosts = latestPosts.length > postsToShow ? latestPosts.slice(0, postsToShow) : latestPosts;
      var layoutControls = [{
        icon: 'list-view',
        title: Object(external_this_wp_i18n_["__"])('List view'),
        onClick: function onClick() {
          return setAttributes({
            postLayout: 'list'
          });
        },
        isActive: postLayout === 'list'
      }, {
        icon: 'grid-view',
        title: Object(external_this_wp_i18n_["__"])('Grid view'),
        onClick: function onClick() {
          return setAttributes({
            postLayout: 'grid'
          });
        },
        isActive: postLayout === 'grid'
      }];

      var dateFormat = Object(external_this_wp_date_["__experimentalGetSettings"])().formats.date;

      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, inspectorControls, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
        controls: layoutControls
      })), Object(external_this_wp_element_["createElement"])("ul", {
        className: classnames_default()(this.props.className, Object(defineProperty["a" /* default */])({
          'wp-block-latest-posts__list': true,
          'is-grid': postLayout === 'grid',
          'has-dates': displayPostDate
        }, "columns-".concat(columns), postLayout === 'grid'))
      }, displayPosts.map(function (post, i) {
        var titleTrimmed = post.title.rendered.trim();
        var excerpt = post.excerpt.rendered;

        if (post.excerpt.raw === '') {
          excerpt = post.content.raw;
        }

        var excerptElement = document.createElement('div');
        excerptElement.innerHTML = excerpt;
        excerpt = excerptElement.textContent || excerptElement.innerText || '';
        return Object(external_this_wp_element_["createElement"])("li", {
          key: i
        }, Object(external_this_wp_element_["createElement"])("a", {
          href: post.link,
          target: "_blank",
          rel: "noreferrer noopener"
        }, titleTrimmed ? Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, titleTrimmed) : Object(external_this_wp_i18n_["__"])('(no title)')), displayPostDate && post.date_gmt && Object(external_this_wp_element_["createElement"])("time", {
          dateTime: Object(external_this_wp_date_["format"])('c', post.date_gmt),
          className: "wp-block-latest-posts__post-date"
        }, Object(external_this_wp_date_["dateI18n"])(dateFormat, post.date_gmt)), displayPostContent && displayPostContentRadio === 'excerpt' && Object(external_this_wp_element_["createElement"])("div", {
          className: "wp-block-latest-posts__post-excerpt"
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], {
          key: "html"
        }, excerptLength < excerpt.trim().split(' ').length ? excerpt.trim().split(' ', excerptLength).join(' ') + ' ... <a href=' + post.link + 'target="_blank" rel="noopener noreferrer">' + Object(external_this_wp_i18n_["__"])('Read more') + '</a>' : excerpt.trim().split(' ', excerptLength).join(' '))), displayPostContent && displayPostContentRadio === 'full_post' && Object(external_this_wp_element_["createElement"])("div", {
          className: "wp-block-latest-posts__post-full-content"
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], {
          key: "html"
        }, post.content.raw.trim())));
      })));
    }
  }]);

  return LatestPostsEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var latest_posts_edit = (Object(external_this_wp_data_["withSelect"])(function (select, props) {
  var _props$attributes = props.attributes,
      postsToShow = _props$attributes.postsToShow,
      order = _props$attributes.order,
      orderBy = _props$attributes.orderBy,
      categories = _props$attributes.categories;

  var _select = select('core'),
      getEntityRecords = _select.getEntityRecords;

  var latestPostsQuery = Object(external_lodash_["pickBy"])({
    categories: categories,
    order: order,
    orderby: orderBy,
    per_page: postsToShow
  }, function (value) {
    return !Object(external_lodash_["isUndefined"])(value);
  });
  return {
    latestPosts: getEntityRecords('postType', 'post', latestPostsQuery)
  };
})(edit_LatestPostsEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/latest-posts/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var latest_posts_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "11",
  y: "7",
  width: "6",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "11",
  y: "11",
  width: "6",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "11",
  y: "15",
  width: "6",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "7",
  y: "7",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "7",
  y: "11",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "7",
  y: "15",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M20.1,3H3.9C3.4,3,3,3.4,3,3.9v16.2C3,20.5,3.4,21,3.9,21h16.2c0.4,0,0.9-0.5,0.9-0.9V3.9C21,3.4,20.5,3,20.1,3z M19,19H5V5h14V19z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/latest-posts/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var latest_posts_name = 'core/latest-posts';
var latest_posts_settings = {
  title: Object(external_this_wp_i18n_["__"])('Latest Posts'),
  description: Object(external_this_wp_i18n_["__"])('Display a list of your most recent posts.'),
  icon: latest_posts_icon,
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('recent posts')],
  supports: {
    align: true,
    html: false
  },
  edit: latest_posts_edit
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/list/ordered-list-settings.js


/**
 * WordPress dependencies
 */




var ordered_list_settings_OrderedListSettings = function OrderedListSettings(_ref) {
  var setAttributes = _ref.setAttributes,
      reversed = _ref.reversed,
      start = _ref.start;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Ordered List Settings')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
    label: Object(external_this_wp_i18n_["__"])('Start Value'),
    type: "number",
    onChange: function onChange(value) {
      var int = parseInt(value, 10);
      setAttributes({
        // It should be possible to unset the value,
        // e.g. with an empty string.
        start: isNaN(int) ? undefined : int
      });
    },
    value: Number.isInteger(start) ? start.toString(10) : '',
    step: "1"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
    label: Object(external_this_wp_i18n_["__"])('Reverse List Numbering'),
    checked: reversed || false,
    onChange: function onChange(value) {
      setAttributes({
        // Unset the attribute if not reversed.
        reversed: value || undefined
      });
    }
  })));
};

/* harmony default export */ var ordered_list_settings = (ordered_list_settings_OrderedListSettings);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/list/edit.js



/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function ListEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      mergeBlocks = _ref.mergeBlocks,
      onReplace = _ref.onReplace,
      className = _ref.className;
  var ordered = attributes.ordered,
      values = attributes.values,
      reversed = attributes.reversed,
      start = attributes.start;
  var tagName = ordered ? 'ol' : 'ul';

  var controls = function controls(_ref2) {
    var value = _ref2.value,
        onChange = _ref2.onChange;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextShortcut"], {
      type: "primary",
      character: "[",
      onUse: function onUse() {
        onChange(Object(external_this_wp_richText_["__unstableOutdentListItems"])(value));
      }
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextShortcut"], {
      type: "primary",
      character: "]",
      onUse: function onUse() {
        onChange(Object(external_this_wp_richText_["__unstableIndentListItems"])(value, {
          type: tagName
        }));
      }
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextShortcut"], {
      type: "primary",
      character: "m",
      onUse: function onUse() {
        onChange(Object(external_this_wp_richText_["__unstableIndentListItems"])(value, {
          type: tagName
        }));
      }
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextShortcut"], {
      type: "primaryShift",
      character: "m",
      onUse: function onUse() {
        onChange(Object(external_this_wp_richText_["__unstableOutdentListItems"])(value));
      }
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
      controls: [{
        icon: 'editor-ul',
        title: Object(external_this_wp_i18n_["__"])('Convert to unordered list'),
        isActive: Object(external_this_wp_richText_["__unstableIsActiveListType"])(value, 'ul', tagName),
        onClick: function onClick() {
          onChange(Object(external_this_wp_richText_["__unstableChangeListType"])(value, {
            type: 'ul'
          }));

          if (Object(external_this_wp_richText_["__unstableIsListRootSelected"])(value)) {
            setAttributes({
              ordered: false
            });
          }
        }
      }, {
        icon: 'editor-ol',
        title: Object(external_this_wp_i18n_["__"])('Convert to ordered list'),
        isActive: Object(external_this_wp_richText_["__unstableIsActiveListType"])(value, 'ol', tagName),
        onClick: function onClick() {
          onChange(Object(external_this_wp_richText_["__unstableChangeListType"])(value, {
            type: 'ol'
          }));

          if (Object(external_this_wp_richText_["__unstableIsListRootSelected"])(value)) {
            setAttributes({
              ordered: true
            });
          }
        }
      }, {
        icon: 'editor-outdent',
        title: Object(external_this_wp_i18n_["__"])('Outdent list item'),
        shortcut: Object(external_this_wp_i18n_["_x"])('Backspace', 'keyboard key'),
        onClick: function onClick() {
          onChange(Object(external_this_wp_richText_["__unstableOutdentListItems"])(value));
        }
      }, {
        icon: 'editor-indent',
        title: Object(external_this_wp_i18n_["__"])('Indent list item'),
        shortcut: Object(external_this_wp_i18n_["_x"])('Space', 'keyboard key'),
        onClick: function onClick() {
          onChange(Object(external_this_wp_richText_["__unstableIndentListItems"])(value, {
            type: tagName
          }));
        }
      }]
    })));
  };

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
    identifier: "values",
    multiline: "li",
    tagName: tagName,
    onChange: function onChange(nextValues) {
      return setAttributes({
        values: nextValues
      });
    },
    value: values,
    wrapperClassName: "block-library-list",
    className: className,
    placeholder: Object(external_this_wp_i18n_["__"])('Write list…'),
    onMerge: mergeBlocks,
    onSplit: function onSplit(value) {
      return Object(external_this_wp_blocks_["createBlock"])(list_name, Object(objectSpread["a" /* default */])({}, attributes, {
        values: value
      }));
    },
    __unstableOnSplitMiddle: function __unstableOnSplitMiddle() {
      return Object(external_this_wp_blocks_["createBlock"])('core/paragraph');
    },
    onReplace: onReplace,
    onRemove: function onRemove() {
      return onReplace([]);
    },
    start: start,
    reversed: reversed
  }, controls), ordered && Object(external_this_wp_element_["createElement"])(ordered_list_settings, {
    setAttributes: setAttributes,
    ordered: ordered,
    reversed: reversed,
    start: start
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/list/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var list_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M9 19h12v-2H9v2zm0-6h12v-2H9v2zm0-8v2h12V5H9zm-4-.5c-.828 0-1.5.672-1.5 1.5S4.172 7.5 5 7.5 6.5 6.828 6.5 6 5.828 4.5 5 4.5zm0 6c-.828 0-1.5.672-1.5 1.5s.672 1.5 1.5 1.5 1.5-.672 1.5-1.5-.672-1.5-1.5-1.5zm0 6c-.828 0-1.5.672-1.5 1.5s.672 1.5 1.5 1.5 1.5-.672 1.5-1.5-.672-1.5-1.5-1.5z"
}))));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/list/save.js


/**
 * WordPress dependencies
 */

function list_save_save(_ref) {
  var attributes = _ref.attributes;
  var ordered = attributes.ordered,
      values = attributes.values,
      reversed = attributes.reversed,
      start = attributes.start;
  var tagName = ordered ? 'ol' : 'ul';
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: tagName,
    value: values,
    reversed: reversed,
    start: start,
    multiline: "li"
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/list/transforms.js



/**
 * WordPress dependencies
 */



var listContentSchema = Object(objectSpread["a" /* default */])({}, Object(external_this_wp_blocks_["getPhrasingContentSchema"])(), {
  ul: {},
  ol: {
    attributes: ['type']
  }
}); // Recursion is needed.
// Possible: ul > li > ul.
// Impossible: ul > ul.


['ul', 'ol'].forEach(function (tag) {
  listContentSchema[tag].children = {
    li: {
      children: listContentSchema
    }
  };
});
var list_transforms_transforms = {
  from: [{
    type: 'block',
    isMultiBlock: true,
    blocks: ['core/paragraph'],
    transform: function transform(blockAttributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/list', {
        values: Object(external_this_wp_richText_["toHTMLString"])({
          value: Object(external_this_wp_richText_["join"])(blockAttributes.map(function (_ref) {
            var content = _ref.content;
            var value = Object(external_this_wp_richText_["create"])({
              html: content
            });

            if (blockAttributes.length > 1) {
              return value;
            } // When converting only one block, transform
            // every line to a list item.


            return Object(external_this_wp_richText_["replace"])(value, /\n/g, external_this_wp_richText_["__UNSTABLE_LINE_SEPARATOR"]);
          }), external_this_wp_richText_["__UNSTABLE_LINE_SEPARATOR"]),
          multilineTag: 'li'
        })
      });
    }
  }, {
    type: 'block',
    blocks: ['core/quote'],
    transform: function transform(_ref2) {
      var value = _ref2.value;
      return Object(external_this_wp_blocks_["createBlock"])('core/list', {
        values: Object(external_this_wp_richText_["toHTMLString"])({
          value: Object(external_this_wp_richText_["create"])({
            html: value,
            multilineTag: 'p'
          }),
          multilineTag: 'li'
        })
      });
    }
  }, {
    type: 'raw',
    selector: 'ol,ul',
    schema: {
      ol: listContentSchema.ol,
      ul: listContentSchema.ul
    },
    transform: function transform(node) {
      return Object(external_this_wp_blocks_["createBlock"])('core/list', Object(objectSpread["a" /* default */])({}, Object(external_this_wp_blocks_["getBlockAttributes"])('core/list', node.outerHTML), {
        ordered: node.nodeName === 'OL'
      }));
    }
  }].concat(Object(toConsumableArray["a" /* default */])(['*', '-'].map(function (prefix) {
    return {
      type: 'prefix',
      prefix: prefix,
      transform: function transform(content) {
        return Object(external_this_wp_blocks_["createBlock"])('core/list', {
          values: "<li>".concat(content, "</li>")
        });
      }
    };
  })), Object(toConsumableArray["a" /* default */])(['1.', '1)'].map(function (prefix) {
    return {
      type: 'prefix',
      prefix: prefix,
      transform: function transform(content) {
        return Object(external_this_wp_blocks_["createBlock"])('core/list', {
          ordered: true,
          values: "<li>".concat(content, "</li>")
        });
      }
    };
  }))),
  to: [{
    type: 'block',
    blocks: ['core/paragraph'],
    transform: function transform(_ref3) {
      var values = _ref3.values;
      return Object(external_this_wp_richText_["split"])(Object(external_this_wp_richText_["create"])({
        html: values,
        multilineTag: 'li',
        multilineWrapperTags: ['ul', 'ol']
      }), external_this_wp_richText_["__UNSTABLE_LINE_SEPARATOR"]).map(function (piece) {
        return Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
          content: Object(external_this_wp_richText_["toHTMLString"])({
            value: piece
          })
        });
      });
    }
  }, {
    type: 'block',
    blocks: ['core/quote'],
    transform: function transform(_ref4) {
      var values = _ref4.values;
      return Object(external_this_wp_blocks_["createBlock"])('core/quote', {
        value: Object(external_this_wp_richText_["toHTMLString"])({
          value: Object(external_this_wp_richText_["create"])({
            html: values,
            multilineTag: 'li',
            multilineWrapperTags: ['ul', 'ol']
          }),
          multilineTag: 'p'
        })
      });
    }
  }]
};
/* harmony default export */ var list_transforms = (list_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/list/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var list_metadata = {
  name: "core/list",
  category: "common",
  attributes: {
    ordered: {
      type: "boolean",
      "default": false
    },
    values: {
      type: "string",
      source: "html",
      selector: "ol,ul",
      multiline: "li",
      __unstableMultilineWrapperTags: ["ol", "ul"],
      "default": ""
    },
    start: {
      type: "number"
    },
    reversed: {
      type: "boolean"
    }
  }
};


var list_name = list_metadata.name;

var list_settings = {
  title: Object(external_this_wp_i18n_["__"])('List'),
  description: Object(external_this_wp_i18n_["__"])('Create a bulleted or numbered list.'),
  icon: list_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('bullet list'), Object(external_this_wp_i18n_["__"])('ordered list'), Object(external_this_wp_i18n_["__"])('numbered list')],
  supports: {
    className: false
  },
  example: {
    attributes: {
      values: '<li>Alice.</li><li>The White Rabbit.</li><li>The Cheshire Cat.</li><li>The Mad Hatter.</li><li>The Queen of Hearts.</li>'
    }
  },
  transforms: list_transforms,
  merge: function merge(attributes, attributesToMerge) {
    var values = attributesToMerge.values;

    if (!values || values === '<li></li>') {
      return attributes;
    }

    return Object(objectSpread["a" /* default */])({}, attributes, {
      values: attributes.values + values
    });
  },
  edit: ListEdit,
  save: list_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/missing/edit.js


/**
 * WordPress dependencies
 */







function MissingBlockWarning(_ref) {
  var attributes = _ref.attributes,
      convertToHTML = _ref.convertToHTML;
  var originalName = attributes.originalName,
      originalUndelimitedContent = attributes.originalUndelimitedContent;
  var hasContent = !!originalUndelimitedContent;
  var hasHTMLBlock = Object(external_this_wp_blocks_["getBlockType"])('core/html');
  var actions = [];
  var messageHTML;

  if (hasContent && hasHTMLBlock) {
    messageHTML = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Your site doesn’t include support for the "%s" block. You can leave this block intact, convert its content to a Custom HTML block, or remove it entirely.'), originalName);
    actions.push(Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
      key: "convert",
      onClick: convertToHTML,
      isLarge: true,
      isPrimary: true
    }, Object(external_this_wp_i18n_["__"])('Keep as HTML')));
  } else {
    messageHTML = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Your site doesn’t include support for the "%s" block. You can leave this block intact or remove it entirely.'), originalName);
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["Warning"], {
    actions: actions
  }, messageHTML), Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, originalUndelimitedContent));
}

var MissingEdit = Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref2) {
  var clientId = _ref2.clientId,
      attributes = _ref2.attributes;

  var _dispatch = dispatch('core/block-editor'),
      replaceBlock = _dispatch.replaceBlock;

  return {
    convertToHTML: function convertToHTML() {
      replaceBlock(clientId, Object(external_this_wp_blocks_["createBlock"])('core/html', {
        content: attributes.originalUndelimitedContent
      }));
    }
  };
})(MissingBlockWarning);
/* harmony default export */ var missing_edit = (MissingEdit);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/missing/save.js


/**
 * WordPress dependencies
 */

function missing_save_save(_ref) {
  var attributes = _ref.attributes;
  // Preserve the missing block's content.
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, attributes.originalContent);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/missing/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var missing_metadata = {
  name: "core/missing",
  category: "common",
  attributes: {
    originalName: {
      type: "string"
    },
    originalUndelimitedContent: {
      type: "string"
    },
    originalContent: {
      type: "string",
      source: "html"
    }
  }
};

var missing_name = missing_metadata.name;

var missing_settings = {
  name: missing_name,
  title: Object(external_this_wp_i18n_["__"])('Unrecognized Block'),
  description: Object(external_this_wp_i18n_["__"])('Your site doesn’t include support for this block.'),
  supports: {
    className: false,
    customClassName: false,
    inserter: false,
    html: false,
    reusable: false
  },
  edit: missing_edit,
  save: missing_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/more/edit.js








/**
 * WordPress dependencies
 */







var edit_MoreEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MoreEdit, _Component);

  function MoreEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, MoreEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MoreEdit).apply(this, arguments));
    _this.onChangeInput = _this.onChangeInput.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onKeyDown = _this.onKeyDown.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.state = {
      defaultText: Object(external_this_wp_i18n_["__"])('Read more')
    };
    return _this;
  }

  Object(createClass["a" /* default */])(MoreEdit, [{
    key: "onChangeInput",
    value: function onChangeInput(event) {
      // Set defaultText to an empty string, allowing the user to clear/replace the input field's text
      this.setState({
        defaultText: ''
      });
      var value = event.target.value.length === 0 ? undefined : event.target.value;
      this.props.setAttributes({
        customText: value
      });
    }
  }, {
    key: "onKeyDown",
    value: function onKeyDown(event) {
      var keyCode = event.keyCode;
      var insertBlocksAfter = this.props.insertBlocksAfter;

      if (keyCode === external_this_wp_keycodes_["ENTER"]) {
        insertBlocksAfter([Object(external_this_wp_blocks_["createBlock"])(Object(external_this_wp_blocks_["getDefaultBlockName"])())]);
      }
    }
  }, {
    key: "getHideExcerptHelp",
    value: function getHideExcerptHelp(checked) {
      return checked ? Object(external_this_wp_i18n_["__"])('The excerpt is hidden.') : Object(external_this_wp_i18n_["__"])('The excerpt is visible.');
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props$attribute = this.props.attributes,
          customText = _this$props$attribute.customText,
          noTeaser = _this$props$attribute.noTeaser;
      var setAttributes = this.props.setAttributes;

      var toggleHideExcerpt = function toggleHideExcerpt() {
        return setAttributes({
          noTeaser: !noTeaser
        });
      };

      var defaultText = this.state.defaultText;
      var value = customText !== undefined ? customText : defaultText;
      var inputLength = value.length + 1;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Hide the excerpt on the full content page'),
        checked: !!noTeaser,
        onChange: toggleHideExcerpt,
        help: this.getHideExcerptHelp
      }))), Object(external_this_wp_element_["createElement"])("div", {
        className: "wp-block-more"
      }, Object(external_this_wp_element_["createElement"])("input", {
        type: "text",
        value: value,
        size: inputLength,
        onChange: this.onChangeInput,
        onKeyDown: this.onKeyDown
      })));
    }
  }]);

  return MoreEdit;
}(external_this_wp_element_["Component"]);



// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/more/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var more_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M2 9v2h19V9H2zm0 6h5v-2H2v2zm7 0h5v-2H9v2zm7 0h5v-2h-5v2z"
}))));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/more/save.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function more_save_save(_ref) {
  var attributes = _ref.attributes;
  var customText = attributes.customText,
      noTeaser = attributes.noTeaser;
  var moreTag = customText ? "<!--more ".concat(customText, "-->") : '<!--more-->';
  var noTeaserTag = noTeaser ? '<!--noteaser-->' : '';
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, Object(external_lodash_["compact"])([moreTag, noTeaserTag]).join('\n'));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/more/transforms.js
/**
 * WordPress dependencies
 */

var more_transforms_transforms = {
  from: [{
    type: 'raw',
    schema: {
      'wp-block': {
        attributes: ['data-block']
      }
    },
    isMatch: function isMatch(node) {
      return node.dataset && node.dataset.block === 'core/more';
    },
    transform: function transform(node) {
      var _node$dataset = node.dataset,
          customText = _node$dataset.customText,
          noTeaser = _node$dataset.noTeaser;
      var attrs = {}; // Don't copy unless defined and not an empty string

      if (customText) {
        attrs.customText = customText;
      } // Special handling for boolean


      if (noTeaser === '') {
        attrs.noTeaser = true;
      }

      return Object(external_this_wp_blocks_["createBlock"])('core/more', attrs);
    }
  }]
};
/* harmony default export */ var more_transforms = (more_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/more/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var more_metadata = {
  name: "core/more",
  category: "layout",
  attributes: {
    customText: {
      type: "string"
    },
    noTeaser: {
      type: "boolean",
      "default": false
    }
  }
};


var more_name = more_metadata.name;

var more_settings = {
  title: Object(external_this_wp_i18n_["_x"])('More', 'block name'),
  description: Object(external_this_wp_i18n_["__"])('Content before this block will be shown in the excerpt on your archives page.'),
  icon: more_icon,
  supports: {
    customClassName: false,
    className: false,
    html: false,
    multiple: false
  },
  example: {},
  transforms: more_transforms,
  edit: edit_MoreEdit,
  save: more_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/nextpage/edit.js


/**
 * WordPress dependencies
 */

function NextPageEdit() {
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "wp-block-nextpage"
  }, Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_i18n_["__"])('Page break')));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/nextpage/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var nextpage_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  width: "24px",
  height: "24px",
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0 0h24v24H0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M9 11h6v2H9zM2 11h5v2H2zM17 11h5v2h-5zM6 4h7v5h7V8l-6-6H6a2 2 0 0 0-2 2v5h2zM18 20H6v-5H4v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5h-2z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/nextpage/save.js


/**
 * WordPress dependencies
 */

function nextpage_save_save() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, '<!--nextpage-->');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/nextpage/transforms.js
/**
 * WordPress dependencies
 */

var nextpage_transforms_transforms = {
  from: [{
    type: 'raw',
    schema: {
      'wp-block': {
        attributes: ['data-block']
      }
    },
    isMatch: function isMatch(node) {
      return node.dataset && node.dataset.block === 'core/nextpage';
    },
    transform: function transform() {
      return Object(external_this_wp_blocks_["createBlock"])('core/nextpage', {});
    }
  }]
};
/* harmony default export */ var nextpage_transforms = (nextpage_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/nextpage/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var nextpage_metadata = {
  name: "core/nextpage",
  category: "layout"
};


var nextpage_name = nextpage_metadata.name;

var nextpage_settings = {
  title: Object(external_this_wp_i18n_["__"])('Page Break'),
  description: Object(external_this_wp_i18n_["__"])('Separate your content into a multi-page experience.'),
  icon: nextpage_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('next page'), Object(external_this_wp_i18n_["__"])('pagination')],
  supports: {
    customClassName: false,
    className: false,
    html: false
  },
  example: {},
  transforms: nextpage_transforms,
  edit: NextPageEdit,
  save: nextpage_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/preformatted/edit.js


/**
 * WordPress dependencies
 */


function PreformattedEdit(_ref) {
  var attributes = _ref.attributes,
      mergeBlocks = _ref.mergeBlocks,
      setAttributes = _ref.setAttributes,
      className = _ref.className;
  var content = attributes.content;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
    tagName: "pre" // Ensure line breaks are normalised to HTML.
    ,
    value: content.replace(/\n/g, '<br>'),
    onChange: function onChange(nextContent) {
      setAttributes({
        // Ensure line breaks are normalised to characters. This
        // saves space, is easier to read, and ensures display
        // filters work correctly.
        content: nextContent.replace(/<br ?\/?>/g, '\n')
      });
    },
    placeholder: Object(external_this_wp_i18n_["__"])('Write preformatted text…'),
    wrapperClassName: className,
    onMerge: mergeBlocks
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/preformatted/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var preformatted_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M20,4H4C2.9,4,2,4.9,2,6v12c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2V6C22,4.9,21.1,4,20,4z M20,18H4V6h16V18z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "6",
  y: "10",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "6",
  y: "14",
  width: "8",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "16",
  y: "14",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "10",
  y: "10",
  width: "8",
  height: "2"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/preformatted/save.js


/**
 * WordPress dependencies
 */

function preformatted_save_save(_ref) {
  var attributes = _ref.attributes;
  var content = attributes.content;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "pre",
    value: content
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/preformatted/transforms.js
/**
 * WordPress dependencies
 */

var preformatted_transforms_transforms = {
  from: [{
    type: 'block',
    blocks: ['core/code', 'core/paragraph'],
    transform: function transform(_ref) {
      var content = _ref.content;
      return Object(external_this_wp_blocks_["createBlock"])('core/preformatted', {
        content: content
      });
    }
  }, {
    type: 'raw',
    isMatch: function isMatch(node) {
      return node.nodeName === 'PRE' && !(node.children.length === 1 && node.firstChild.nodeName === 'CODE');
    },
    schema: {
      pre: {
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      }
    }
  }],
  to: [{
    type: 'block',
    blocks: ['core/paragraph'],
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/paragraph', attributes);
    }
  }]
};
/* harmony default export */ var preformatted_transforms = (preformatted_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/preformatted/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var preformatted_metadata = {
  name: "core/preformatted",
  category: "formatting",
  attributes: {
    content: {
      type: "string",
      source: "html",
      selector: "pre",
      "default": ""
    }
  }
};


var preformatted_name = preformatted_metadata.name;

var preformatted_settings = {
  title: Object(external_this_wp_i18n_["__"])('Preformatted'),
  description: Object(external_this_wp_i18n_["__"])('Add text that respects your spacing and tabs, and also allows styling.'),
  icon: preformatted_icon,
  example: {
    attributes: {
      content: Object(external_this_wp_i18n_["__"])('EXT. XANADU - FAINT DAWN - 1940 (MINIATURE)') + '\n' + Object(external_this_wp_i18n_["__"])('Window, very small in the distance, illuminated.') + '\n' + Object(external_this_wp_i18n_["__"])('All around this is an almost totally black screen. Now, as the camera moves slowly towards the window which is almost a postage stamp in the frame, other forms appear;')
    }
  },
  transforms: preformatted_transforms,
  edit: PreformattedEdit,
  save: preformatted_save_save,
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: attributes.content + attributesToMerge.content
    };
  }
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/pullquote/shared.js
var SOLID_COLOR_STYLE_NAME = 'solid-color';
var SOLID_COLOR_CLASS = "is-style-".concat(SOLID_COLOR_STYLE_NAME);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/pullquote/deprecated.js





/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


var pullquote_deprecated_blockAttributes = {
  value: {
    type: 'string',
    source: 'html',
    selector: 'blockquote',
    multiline: 'p'
  },
  citation: {
    type: 'string',
    source: 'html',
    selector: 'cite',
    default: ''
  },
  mainColor: {
    type: 'string'
  },
  customMainColor: {
    type: 'string'
  },
  textColor: {
    type: 'string'
  },
  customTextColor: {
    type: 'string'
  }
};

function parseBorderColor(styleString) {
  if (!styleString) {
    return;
  }

  var matches = styleString.match(/border-color:([^;]+)[;]?/);

  if (matches && matches[1]) {
    return matches[1];
  }
}

var pullquote_deprecated_deprecated = [{
  attributes: Object(objectSpread["a" /* default */])({}, pullquote_deprecated_blockAttributes, {
    // figureStyle is an attribute that never existed.
    // We are using it as a way to access the styles previously applied to the figure.
    figureStyle: {
      source: 'attribute',
      selector: 'figure',
      attribute: 'style'
    }
  }),
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var mainColor = attributes.mainColor,
        customMainColor = attributes.customMainColor,
        textColor = attributes.textColor,
        customTextColor = attributes.customTextColor,
        value = attributes.value,
        citation = attributes.citation,
        className = attributes.className,
        figureStyle = attributes.figureStyle;
    var isSolidColorStyle = Object(external_lodash_["includes"])(className, SOLID_COLOR_CLASS);
    var figureClasses, figureStyles; // Is solid color style

    if (isSolidColorStyle) {
      var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', mainColor);
      figureClasses = classnames_default()(Object(defineProperty["a" /* default */])({
        'has-background': backgroundClass || customMainColor
      }, backgroundClass, backgroundClass));
      figureStyles = {
        backgroundColor: backgroundClass ? undefined : customMainColor
      }; // Is normal style and a custom color is being used ( we can set a style directly with its value)
    } else if (customMainColor) {
      figureStyles = {
        borderColor: customMainColor
      }; // If normal style and a named color are being used, we need to retrieve the color value to set the style,
      // as there is no expectation that themes create classes that set border colors.
    } else if (mainColor) {
      // Previously here we queried the color settings to know the color value
      // of a named color. This made the save function impure and the block was refactored,
      // because meanwhile a change in the editor made it impossible to query color settings in the save function.
      // Here instead of querying the color settings to know the color value, we retrieve the value
      // directly from the style previously serialized.
      var borderColor = parseBorderColor(figureStyle);
      figureStyles = {
        borderColor: borderColor
      };
    }

    var blockquoteTextColorClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
    var blockquoteClasses = (textColor || customTextColor) && classnames_default()('has-text-color', Object(defineProperty["a" /* default */])({}, blockquoteTextColorClass, blockquoteTextColorClass));
    var blockquoteStyles = blockquoteTextColorClass ? undefined : {
      color: customTextColor
    };
    return Object(external_this_wp_element_["createElement"])("figure", {
      className: figureClasses,
      style: figureStyles
    }, Object(external_this_wp_element_["createElement"])("blockquote", {
      className: blockquoteClasses,
      style: blockquoteStyles
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      value: value,
      multiline: true
    }), !external_this_wp_blockEditor_["RichText"].isEmpty(citation) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "cite",
      value: citation
    })));
  },
  migrate: function migrate(_ref2) {
    var className = _ref2.className,
        figureStyle = _ref2.figureStyle,
        mainColor = _ref2.mainColor,
        attributes = Object(objectWithoutProperties["a" /* default */])(_ref2, ["className", "figureStyle", "mainColor"]);

    var isSolidColorStyle = Object(external_lodash_["includes"])(className, SOLID_COLOR_CLASS); // If is the default style, and a main color is set,
    // migrate the main color value into a custom color.
    // The custom color value is retrived by parsing the figure styles.

    if (!isSolidColorStyle && mainColor && figureStyle) {
      var borderColor = parseBorderColor(figureStyle);

      if (borderColor) {
        return Object(objectSpread["a" /* default */])({}, attributes, {
          className: className,
          customMainColor: borderColor
        });
      }
    }

    return Object(objectSpread["a" /* default */])({
      className: className,
      mainColor: mainColor
    }, attributes);
  }
}, {
  attributes: pullquote_deprecated_blockAttributes,
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    var mainColor = attributes.mainColor,
        customMainColor = attributes.customMainColor,
        textColor = attributes.textColor,
        customTextColor = attributes.customTextColor,
        value = attributes.value,
        citation = attributes.citation,
        className = attributes.className;
    var isSolidColorStyle = Object(external_lodash_["includes"])(className, SOLID_COLOR_CLASS);
    var figureClass, figureStyles; // Is solid color style

    if (isSolidColorStyle) {
      figureClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', mainColor);

      if (!figureClass) {
        figureStyles = {
          backgroundColor: customMainColor
        };
      } // Is normal style and a custom color is being used ( we can set a style directly with its value)

    } else if (customMainColor) {
      figureStyles = {
        borderColor: customMainColor
      }; // Is normal style and a named color is being used, we need to retrieve the color value to set the style,
      // as there is no expectation that themes create classes that set border colors.
    } else if (mainColor) {
      var colors = Object(external_lodash_["get"])(Object(external_this_wp_data_["select"])('core/block-editor').getSettings(), ['colors'], []);
      var colorObject = Object(external_this_wp_blockEditor_["getColorObjectByAttributeValues"])(colors, mainColor);
      figureStyles = {
        borderColor: colorObject.color
      };
    }

    var blockquoteTextColorClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
    var blockquoteClasses = textColor || customTextColor ? classnames_default()('has-text-color', Object(defineProperty["a" /* default */])({}, blockquoteTextColorClass, blockquoteTextColorClass)) : undefined;
    var blockquoteStyle = blockquoteTextColorClass ? undefined : {
      color: customTextColor
    };
    return Object(external_this_wp_element_["createElement"])("figure", {
      className: figureClass,
      style: figureStyles
    }, Object(external_this_wp_element_["createElement"])("blockquote", {
      className: blockquoteClasses,
      style: blockquoteStyle
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      value: value,
      multiline: true
    }), !external_this_wp_blockEditor_["RichText"].isEmpty(citation) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "cite",
      value: citation
    })));
  }
}, {
  attributes: Object(objectSpread["a" /* default */])({}, pullquote_deprecated_blockAttributes),
  save: function save(_ref4) {
    var attributes = _ref4.attributes;
    var value = attributes.value,
        citation = attributes.citation;
    return Object(external_this_wp_element_["createElement"])("blockquote", null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      value: value,
      multiline: true
    }), !external_this_wp_blockEditor_["RichText"].isEmpty(citation) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "cite",
      value: citation
    }));
  }
}, {
  attributes: Object(objectSpread["a" /* default */])({}, pullquote_deprecated_blockAttributes, {
    citation: {
      type: 'string',
      source: 'html',
      selector: 'footer'
    },
    align: {
      type: 'string',
      default: 'none'
    }
  }),
  save: function save(_ref5) {
    var attributes = _ref5.attributes;
    var value = attributes.value,
        citation = attributes.citation,
        align = attributes.align;
    return Object(external_this_wp_element_["createElement"])("blockquote", {
      className: "align".concat(align)
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      value: value,
      multiline: true
    }), !external_this_wp_blockEditor_["RichText"].isEmpty(citation) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "footer",
      value: citation
    }));
  }
}];
/* harmony default export */ var pullquote_deprecated = (pullquote_deprecated_deprecated);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/pullquote/edit.js










/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var edit_PullQuoteEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PullQuoteEdit, _Component);

  function PullQuoteEdit(props) {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PullQuoteEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PullQuoteEdit).call(this, props));
    _this.wasTextColorAutomaticallyComputed = false;
    _this.pullQuoteMainColorSetter = _this.pullQuoteMainColorSetter.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.pullQuoteTextColorSetter = _this.pullQuoteTextColorSetter.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(PullQuoteEdit, [{
    key: "pullQuoteMainColorSetter",
    value: function pullQuoteMainColorSetter(colorValue) {
      var _this$props = this.props,
          colorUtils = _this$props.colorUtils,
          textColor = _this$props.textColor,
          setAttributes = _this$props.setAttributes,
          setTextColor = _this$props.setTextColor,
          setMainColor = _this$props.setMainColor,
          className = _this$props.className;
      var isSolidColorStyle = Object(external_lodash_["includes"])(className, SOLID_COLOR_CLASS);
      var needTextColor = !textColor.color || this.wasTextColorAutomaticallyComputed;
      var shouldSetTextColor = isSolidColorStyle && needTextColor && colorValue;

      if (isSolidColorStyle) {
        // If we use the solid color style, set the color using the normal mechanism.
        setMainColor(colorValue);
      } else {
        // If we use the default style, set the color as a custom color to force the usage of an inline style.
        // Default style uses a border color for which classes are not available.
        setAttributes({
          customMainColor: colorValue
        });
      }

      if (shouldSetTextColor) {
        this.wasTextColorAutomaticallyComputed = true;
        setTextColor(colorUtils.getMostReadableColor(colorValue));
      }
    }
  }, {
    key: "pullQuoteTextColorSetter",
    value: function pullQuoteTextColorSetter(colorValue) {
      var setTextColor = this.props.setTextColor;
      setTextColor(colorValue);
      this.wasTextColorAutomaticallyComputed = false;
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          className = _this$props2.className,
          mainColor = _this$props2.mainColor,
          setAttributes = _this$props2.setAttributes; // If the block includes a named color and we switched from the
      // solid color style to the default style.

      if (attributes.mainColor && !Object(external_lodash_["includes"])(className, SOLID_COLOR_CLASS) && Object(external_lodash_["includes"])(prevProps.className, SOLID_COLOR_CLASS)) {
        // Remove the named color, and set the color as a custom color.
        // This is done because named colors use classes, in the default style we use a border color,
        // and themes don't set classes for border colors.
        setAttributes({
          mainColor: undefined,
          customMainColor: mainColor.color
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          mainColor = _this$props3.mainColor,
          textColor = _this$props3.textColor,
          setAttributes = _this$props3.setAttributes,
          isSelected = _this$props3.isSelected,
          className = _this$props3.className;
      var value = attributes.value,
          citation = attributes.citation;
      var isSolidColorStyle = Object(external_lodash_["includes"])(className, SOLID_COLOR_CLASS);
      var figureStyles = isSolidColorStyle ? {
        backgroundColor: mainColor.color
      } : {
        borderColor: mainColor.color
      };
      var figureClasses = classnames_default()(className, Object(defineProperty["a" /* default */])({
        'has-background': isSolidColorStyle && mainColor.color
      }, mainColor.class, isSolidColorStyle && mainColor.class));
      var blockquoteStyles = {
        color: textColor.color
      };
      var blockquoteClasses = textColor.color && classnames_default()('has-text-color', Object(defineProperty["a" /* default */])({}, textColor.class, textColor.class));
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("figure", {
        style: figureStyles,
        className: figureClasses
      }, Object(external_this_wp_element_["createElement"])("blockquote", {
        style: blockquoteStyles,
        className: blockquoteClasses
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        multiline: true,
        value: value,
        onChange: function onChange(nextValue) {
          return setAttributes({
            value: nextValue
          });
        },
        placeholder: // translators: placeholder text used for the quote
        Object(external_this_wp_i18n_["__"])('Write quote…'),
        wrapperClassName: "block-library-pullquote__content"
      }), (!external_this_wp_blockEditor_["RichText"].isEmpty(citation) || isSelected) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        value: citation,
        placeholder: // translators: placeholder text used for the citation
        Object(external_this_wp_i18n_["__"])('Write citation…'),
        onChange: function onChange(nextCitation) {
          return setAttributes({
            citation: nextCitation
          });
        },
        className: "wp-block-pullquote__citation"
      }))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
        title: Object(external_this_wp_i18n_["__"])('Color Settings'),
        colorSettings: [{
          value: mainColor.color,
          onChange: this.pullQuoteMainColorSetter,
          label: Object(external_this_wp_i18n_["__"])('Main Color')
        }, {
          value: textColor.color,
          onChange: this.pullQuoteTextColorSetter,
          label: Object(external_this_wp_i18n_["__"])('Text Color')
        }]
      }, isSolidColorStyle && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["ContrastChecker"], Object(esm_extends["a" /* default */])({
        textColor: textColor.color,
        backgroundColor: mainColor.color
      }, {
        isLargeText: false
      })))));
    }
  }]);

  return PullQuoteEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var pullquote_edit = (Object(external_this_wp_blockEditor_["withColors"])({
  mainColor: 'background-color',
  textColor: 'color'
})(edit_PullQuoteEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/pullquote/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var pullquote_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Polygon"], {
  points: "21 18 2 18 2 20 21 20"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m19 10v4h-15v-4h15m1-2h-17c-0.55 0-1 0.45-1 1v6c0 0.55 0.45 1 1 1h17c0.55 0 1-0.45 1-1v-6c0-0.55-0.45-1-1-1z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Polygon"], {
  points: "21 4 2 4 2 6 21 6"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/pullquote/save.js



/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function pullquote_save_save(_ref) {
  var attributes = _ref.attributes;
  var mainColor = attributes.mainColor,
      customMainColor = attributes.customMainColor,
      textColor = attributes.textColor,
      customTextColor = attributes.customTextColor,
      value = attributes.value,
      citation = attributes.citation,
      className = attributes.className;
  var isSolidColorStyle = Object(external_lodash_["includes"])(className, SOLID_COLOR_CLASS);
  var figureClasses, figureStyles; // Is solid color style

  if (isSolidColorStyle) {
    var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', mainColor);
    figureClasses = classnames_default()(Object(defineProperty["a" /* default */])({
      'has-background': backgroundClass || customMainColor
    }, backgroundClass, backgroundClass));
    figureStyles = {
      backgroundColor: backgroundClass ? undefined : customMainColor
    }; // Is normal style and a custom color is being used ( we can set a style directly with its value)
  } else if (customMainColor) {
    figureStyles = {
      borderColor: customMainColor
    };
  }

  var blockquoteTextColorClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', textColor);
  var blockquoteClasses = (textColor || customTextColor) && classnames_default()('has-text-color', Object(defineProperty["a" /* default */])({}, blockquoteTextColorClass, blockquoteTextColorClass));
  var blockquoteStyles = blockquoteTextColorClass ? undefined : {
    color: customTextColor
  };
  return Object(external_this_wp_element_["createElement"])("figure", {
    className: figureClasses,
    style: figureStyles
  }, Object(external_this_wp_element_["createElement"])("blockquote", {
    className: blockquoteClasses,
    style: blockquoteStyles
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    value: value,
    multiline: true
  }), !external_this_wp_blockEditor_["RichText"].isEmpty(citation) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "cite",
    value: citation
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/pullquote/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





var pullquote_metadata = {
  name: "core/pullquote",
  category: "formatting",
  attributes: {
    value: {
      type: "string",
      source: "html",
      selector: "blockquote",
      multiline: "p"
    },
    citation: {
      type: "string",
      source: "html",
      selector: "cite",
      "default": ""
    },
    mainColor: {
      type: "string"
    },
    customMainColor: {
      type: "string"
    },
    textColor: {
      type: "string"
    },
    customTextColor: {
      type: "string"
    }
  }
};

var pullquote_name = pullquote_metadata.name;

var pullquote_settings = {
  title: Object(external_this_wp_i18n_["__"])('Pullquote'),
  description: Object(external_this_wp_i18n_["__"])('Give special visual emphasis to a quote from your text.'),
  icon: pullquote_icon,
  example: {
    attributes: {
      value: '<p>' + Object(external_this_wp_i18n_["__"])('One of the hardest things to do in technology is disrupt yourself.') + '</p>',
      citation: 'Matt Mullenweg'
    }
  },
  styles: [{
    name: 'default',
    label: Object(external_this_wp_i18n_["_x"])('Default', 'block style'),
    isDefault: true
  }, {
    name: SOLID_COLOR_STYLE_NAME,
    label: Object(external_this_wp_i18n_["__"])('Solid Color')
  }],
  supports: {
    align: ['left', 'right', 'wide', 'full']
  },
  edit: pullquote_edit,
  save: pullquote_save_save,
  deprecated: pullquote_deprecated
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/block/edit-panel/index.js








/**
 * WordPress dependencies
 */






var edit_panel_ReusableBlockEditPanel =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ReusableBlockEditPanel, _Component);

  function ReusableBlockEditPanel() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, ReusableBlockEditPanel);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ReusableBlockEditPanel).apply(this, arguments));
    _this.titleField = Object(external_this_wp_element_["createRef"])();
    _this.editButton = Object(external_this_wp_element_["createRef"])();
    _this.handleFormSubmit = _this.handleFormSubmit.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.handleTitleChange = _this.handleTitleChange.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.handleTitleKeyDown = _this.handleTitleKeyDown.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(ReusableBlockEditPanel, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      // Select the input text when the form opens.
      if (this.props.isEditing && this.titleField.current) {
        this.titleField.current.select();
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      // Select the input text only once when the form opens.
      if (!prevProps.isEditing && this.props.isEditing) {
        this.titleField.current.select();
      } // Move focus back to the Edit button after pressing the Escape key or Save.


      if ((prevProps.isEditing || prevProps.isSaving) && !this.props.isEditing && !this.props.isSaving) {
        this.editButton.current.focus();
      }
    }
  }, {
    key: "handleFormSubmit",
    value: function handleFormSubmit(event) {
      event.preventDefault();
      this.props.onSave();
    }
  }, {
    key: "handleTitleChange",
    value: function handleTitleChange(event) {
      this.props.onChangeTitle(event.target.value);
    }
  }, {
    key: "handleTitleKeyDown",
    value: function handleTitleKeyDown(event) {
      if (event.keyCode === external_this_wp_keycodes_["ESCAPE"]) {
        event.stopPropagation();
        this.props.onCancel();
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          isEditing = _this$props.isEditing,
          title = _this$props.title,
          isSaving = _this$props.isSaving,
          isEditDisabled = _this$props.isEditDisabled,
          onEdit = _this$props.onEdit,
          instanceId = _this$props.instanceId;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, !isEditing && !isSaving && Object(external_this_wp_element_["createElement"])("div", {
        className: "reusable-block-edit-panel"
      }, Object(external_this_wp_element_["createElement"])("b", {
        className: "reusable-block-edit-panel__info"
      }, title), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        ref: this.editButton,
        isLarge: true,
        className: "reusable-block-edit-panel__button",
        disabled: isEditDisabled,
        onClick: onEdit
      }, Object(external_this_wp_i18n_["__"])('Edit'))), (isEditing || isSaving) && Object(external_this_wp_element_["createElement"])("form", {
        className: "reusable-block-edit-panel",
        onSubmit: this.handleFormSubmit
      }, Object(external_this_wp_element_["createElement"])("label", {
        htmlFor: "reusable-block-edit-panel__title-".concat(instanceId),
        className: "reusable-block-edit-panel__label"
      }, Object(external_this_wp_i18n_["__"])('Name:')), Object(external_this_wp_element_["createElement"])("input", {
        ref: this.titleField,
        type: "text",
        disabled: isSaving,
        className: "reusable-block-edit-panel__title",
        value: title,
        onChange: this.handleTitleChange,
        onKeyDown: this.handleTitleKeyDown,
        id: "reusable-block-edit-panel__title-".concat(instanceId)
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        type: "submit",
        isLarge: true,
        isBusy: isSaving,
        disabled: !title || isSaving,
        className: "reusable-block-edit-panel__button"
      }, Object(external_this_wp_i18n_["__"])('Save'))));
    }
  }]);

  return ReusableBlockEditPanel;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit_panel = (Object(external_this_wp_compose_["withInstanceId"])(edit_panel_ReusableBlockEditPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/block/indicator/index.js


/**
 * WordPress dependencies
 */



function ReusableBlockIndicator(_ref) {
  var title = _ref.title;
  // translators: %s: title/name of the reusable block
  var tooltipText = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Reusable Block: %s'), title);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Tooltip"], {
    text: tooltipText
  }, Object(external_this_wp_element_["createElement"])("span", {
    className: "reusable-block-indicator"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dashicon"], {
    icon: "controls-repeat"
  })));
}

/* harmony default export */ var indicator = (ReusableBlockIndicator);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/block/edit.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */




var edit_ReusableBlockEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ReusableBlockEdit, _Component);

  function ReusableBlockEdit(_ref) {
    var _this;

    var reusableBlock = _ref.reusableBlock;

    Object(classCallCheck["a" /* default */])(this, ReusableBlockEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ReusableBlockEdit).apply(this, arguments));
    _this.startEditing = _this.startEditing.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.stopEditing = _this.stopEditing.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.setBlocks = _this.setBlocks.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.setTitle = _this.setTitle.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.save = _this.save.bind(Object(assertThisInitialized["a" /* default */])(_this));

    if (reusableBlock) {
      // Start in edit mode when we're working with a newly created reusable block
      _this.state = {
        isEditing: reusableBlock.isTemporary,
        title: reusableBlock.title,
        blocks: Object(external_this_wp_blocks_["parse"])(reusableBlock.content)
      };
    } else {
      // Start in preview mode when we're working with an existing reusable block
      _this.state = {
        isEditing: false,
        title: null,
        blocks: []
      };
    }

    return _this;
  }

  Object(createClass["a" /* default */])(ReusableBlockEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      if (!this.props.reusableBlock) {
        this.props.fetchReusableBlock();
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (prevProps.reusableBlock !== this.props.reusableBlock && this.state.title === null) {
        this.setState({
          title: this.props.reusableBlock.title,
          blocks: Object(external_this_wp_blocks_["parse"])(this.props.reusableBlock.content)
        });
      }
    }
  }, {
    key: "startEditing",
    value: function startEditing() {
      var reusableBlock = this.props.reusableBlock;
      this.setState({
        isEditing: true,
        title: reusableBlock.title,
        blocks: Object(external_this_wp_blocks_["parse"])(reusableBlock.content)
      });
    }
  }, {
    key: "stopEditing",
    value: function stopEditing() {
      this.setState({
        isEditing: false,
        title: null,
        blocks: []
      });
    }
  }, {
    key: "setBlocks",
    value: function setBlocks(blocks) {
      this.setState({
        blocks: blocks
      });
    }
  }, {
    key: "setTitle",
    value: function setTitle(title) {
      this.setState({
        title: title
      });
    }
  }, {
    key: "save",
    value: function save() {
      var _this$props = this.props,
          onChange = _this$props.onChange,
          onSave = _this$props.onSave;
      var _this$state = this.state,
          blocks = _this$state.blocks,
          title = _this$state.title;
      var content = Object(external_this_wp_blocks_["serialize"])(blocks);
      onChange({
        title: title,
        content: content
      });
      onSave();
      this.stopEditing();
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          isSelected = _this$props2.isSelected,
          reusableBlock = _this$props2.reusableBlock,
          isFetching = _this$props2.isFetching,
          isSaving = _this$props2.isSaving,
          canUpdateBlock = _this$props2.canUpdateBlock,
          settings = _this$props2.settings;
      var _this$state2 = this.state,
          isEditing = _this$state2.isEditing,
          title = _this$state2.title,
          blocks = _this$state2.blocks;

      if (!reusableBlock && isFetching) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null));
      }

      if (!reusableBlock) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], null, Object(external_this_wp_i18n_["__"])('Block has been deleted or is unavailable.'));
      }

      var element = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockEditorProvider"], {
        settings: settings,
        value: blocks,
        onChange: this.setBlocks,
        onInput: this.setBlocks
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["WritingFlow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockList"], null)));

      if (!isEditing) {
        element = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, element);
      }

      return Object(external_this_wp_element_["createElement"])("div", {
        className: "block-library-block__reusable-block-container"
      }, (isSelected || isEditing) && Object(external_this_wp_element_["createElement"])(edit_panel, {
        isEditing: isEditing,
        title: title !== null ? title : reusableBlock.title,
        isSaving: isSaving && !reusableBlock.isTemporary,
        isEditDisabled: !canUpdateBlock,
        onEdit: this.startEditing,
        onChangeTitle: this.setTitle,
        onSave: this.save,
        onCancel: this.stopEditing
      }), !isSelected && !isEditing && Object(external_this_wp_element_["createElement"])(indicator, {
        title: reusableBlock.title
      }), element);
    }
  }]);

  return ReusableBlockEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var block_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, ownProps) {
  var _select = select('core/editor'),
      getReusableBlock = _select.__experimentalGetReusableBlock,
      isFetchingReusableBlock = _select.__experimentalIsFetchingReusableBlock,
      isSavingReusableBlock = _select.__experimentalIsSavingReusableBlock;

  var _select2 = select('core'),
      canUser = _select2.canUser;

  var _select3 = select('core/block-editor'),
      __experimentalGetParsedReusableBlock = _select3.__experimentalGetParsedReusableBlock,
      getSettings = _select3.getSettings;

  var ref = ownProps.attributes.ref;
  var reusableBlock = getReusableBlock(ref);
  return {
    reusableBlock: reusableBlock,
    isFetching: isFetchingReusableBlock(ref),
    isSaving: isSavingReusableBlock(ref),
    blocks: reusableBlock ? __experimentalGetParsedReusableBlock(reusableBlock.id) : null,
    canUpdateBlock: !!reusableBlock && !reusableBlock.isTemporary && !!canUser('update', 'blocks', ref),
    settings: getSettings()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  var _dispatch = dispatch('core/editor'),
      fetchReusableBlocks = _dispatch.__experimentalFetchReusableBlocks,
      updateReusableBlock = _dispatch.__experimentalUpdateReusableBlock,
      saveReusableBlock = _dispatch.__experimentalSaveReusableBlock;

  var ref = ownProps.attributes.ref;
  return {
    fetchReusableBlock: Object(external_lodash_["partial"])(fetchReusableBlocks, ref),
    onChange: Object(external_lodash_["partial"])(updateReusableBlock, ref),
    onSave: Object(external_lodash_["partial"])(saveReusableBlock, ref)
  };
})])(edit_ReusableBlockEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/block/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var block_name = 'core/block';
var block_settings = {
  title: Object(external_this_wp_i18n_["__"])('Reusable Block'),
  category: 'reusable',
  description: Object(external_this_wp_i18n_["__"])('Create content, and save it for you and other contributors to reuse across your site. Update the block, and the changes apply everywhere it’s used.'),
  supports: {
    customClassName: false,
    html: false,
    inserter: false
  },
  edit: block_edit
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/rss/edit.js









/**
 * WordPress dependencies
 */





var DEFAULT_MIN_ITEMS = 1;
var DEFAULT_MAX_ITEMS = 10;

var edit_RSSEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(RSSEdit, _Component);

  function RSSEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, RSSEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(RSSEdit).apply(this, arguments));
    _this.state = {
      editing: !_this.props.attributes.feedURL
    };
    _this.toggleAttribute = _this.toggleAttribute.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSubmitURL = _this.onSubmitURL.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(RSSEdit, [{
    key: "toggleAttribute",
    value: function toggleAttribute(propName) {
      var _this2 = this;

      return function () {
        var value = _this2.props.attributes[propName];
        var setAttributes = _this2.props.setAttributes;
        setAttributes(Object(defineProperty["a" /* default */])({}, propName, !value));
      };
    }
  }, {
    key: "onSubmitURL",
    value: function onSubmitURL(event) {
      event.preventDefault();
      var feedURL = this.props.attributes.feedURL;

      if (feedURL) {
        this.setState({
          editing: false
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this3 = this;

      var _this$props$attribute = this.props.attributes,
          blockLayout = _this$props$attribute.blockLayout,
          columns = _this$props$attribute.columns,
          displayAuthor = _this$props$attribute.displayAuthor,
          displayExcerpt = _this$props$attribute.displayExcerpt,
          displayDate = _this$props$attribute.displayDate,
          excerptLength = _this$props$attribute.excerptLength,
          feedURL = _this$props$attribute.feedURL,
          itemsToShow = _this$props$attribute.itemsToShow;
      var setAttributes = this.props.setAttributes;

      if (this.state.editing) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], {
          icon: "rss",
          label: "RSS"
        }, Object(external_this_wp_element_["createElement"])("form", {
          onSubmit: this.onSubmitURL
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          placeholder: Object(external_this_wp_i18n_["__"])('Enter URL here…'),
          value: feedURL,
          onChange: function onChange(value) {
            return setAttributes({
              feedURL: value
            });
          },
          className: 'components-placeholder__input'
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
          isLarge: true,
          type: "submit"
        }, Object(external_this_wp_i18n_["__"])('Use URL'))));
      }

      var toolbarControls = [{
        icon: 'edit',
        title: Object(external_this_wp_i18n_["__"])('Edit RSS URL'),
        onClick: function onClick() {
          return _this3.setState({
            editing: true
          });
        }
      }, {
        icon: 'list-view',
        title: Object(external_this_wp_i18n_["__"])('List view'),
        onClick: function onClick() {
          return setAttributes({
            blockLayout: 'list'
          });
        },
        isActive: blockLayout === 'list'
      }, {
        icon: 'grid-view',
        title: Object(external_this_wp_i18n_["__"])('Grid view'),
        onClick: function onClick() {
          return setAttributes({
            blockLayout: 'grid'
          });
        },
        isActive: blockLayout === 'grid'
      }];
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
        controls: toolbarControls
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('RSS Settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
        label: Object(external_this_wp_i18n_["__"])('Number of items'),
        value: itemsToShow,
        onChange: function onChange(value) {
          return setAttributes({
            itemsToShow: value
          });
        },
        min: DEFAULT_MIN_ITEMS,
        max: DEFAULT_MAX_ITEMS,
        required: true
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Display author'),
        checked: displayAuthor,
        onChange: this.toggleAttribute('displayAuthor')
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Display date'),
        checked: displayDate,
        onChange: this.toggleAttribute('displayDate')
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Display excerpt'),
        checked: displayExcerpt,
        onChange: this.toggleAttribute('displayExcerpt')
      }), displayExcerpt && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
        label: Object(external_this_wp_i18n_["__"])('Max number of words in excerpt'),
        value: excerptLength,
        onChange: function onChange(value) {
          return setAttributes({
            excerptLength: value
          });
        },
        min: 10,
        max: 100,
        required: true
      }), blockLayout === 'grid' && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
        label: Object(external_this_wp_i18n_["__"])('Columns'),
        value: columns,
        onChange: function onChange(value) {
          return setAttributes({
            columns: value
          });
        },
        min: 2,
        max: 6,
        required: true
      }))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_serverSideRender_default.a, {
        block: "core/rss",
        attributes: this.props.attributes
      })));
    }
  }]);

  return RSSEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var rss_edit = (edit_RSSEdit);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/rss/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var rss_name = 'core/rss';
var rss_settings = {
  title: Object(external_this_wp_i18n_["__"])('RSS'),
  description: Object(external_this_wp_i18n_["__"])('Display entries from any RSS or Atom feed.'),
  icon: 'rss',
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('atom'), Object(external_this_wp_i18n_["__"])('feed')],
  supports: {
    align: true,
    html: false
  },
  example: {
    attributes: {
      feedURL: 'https://wordpress.org'
    }
  },
  edit: rss_edit
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/search/edit.js


/**
 * WordPress dependencies
 */


function SearchEdit(_ref) {
  var className = _ref.className,
      attributes = _ref.attributes,
      setAttributes = _ref.setAttributes;
  var label = attributes.label,
      placeholder = attributes.placeholder,
      buttonText = attributes.buttonText;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: className
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
    wrapperClassName: "wp-block-search__label",
    "aria-label": Object(external_this_wp_i18n_["__"])('Label text'),
    placeholder: Object(external_this_wp_i18n_["__"])('Add label…'),
    withoutInteractiveFormatting: true,
    value: label,
    onChange: function onChange(html) {
      return setAttributes({
        label: html
      });
    }
  }), Object(external_this_wp_element_["createElement"])("input", {
    className: "wp-block-search__input",
    "aria-label": Object(external_this_wp_i18n_["__"])('Optional placeholder text') // We hide the placeholder field's placeholder when there is a value. This
    // stops screen readers from reading the placeholder field's placeholder
    // which is confusing.
    ,
    placeholder: placeholder ? undefined : Object(external_this_wp_i18n_["__"])('Optional placeholder…'),
    value: placeholder,
    onChange: function onChange(event) {
      return setAttributes({
        placeholder: event.target.value
      });
    }
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
    wrapperClassName: "wp-block-search__button",
    className: "wp-block-search__button-rich-text",
    "aria-label": Object(external_this_wp_i18n_["__"])('Button text'),
    placeholder: Object(external_this_wp_i18n_["__"])('Add button text…'),
    withoutInteractiveFormatting: true,
    value: buttonText,
    onChange: function onChange(html) {
      return setAttributes({
        buttonText: html
      });
    }
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/search/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var search_name = 'core/search';
var search_settings = {
  title: Object(external_this_wp_i18n_["__"])('Search'),
  description: Object(external_this_wp_i18n_["__"])('Help visitors find your content.'),
  icon: 'search',
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('find')],
  supports: {
    align: true
  },
  example: {},
  edit: SearchEdit
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/group/deprecated.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


var group_deprecated_deprecated = [// v1 of group block. Deprecated to add an inner-container div around `InnerBlocks.Content`.
{
  attributes: {
    backgroundColor: {
      type: 'string'
    },
    customBackgroundColor: {
      type: 'string'
    }
  },
  supports: {
    align: ['wide', 'full'],
    anchor: true,
    html: false
  },
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var backgroundColor = attributes.backgroundColor,
        customBackgroundColor = attributes.customBackgroundColor;
    var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
    var className = classnames_default()(backgroundClass, {
      'has-background': backgroundColor || customBackgroundColor
    });
    var styles = {
      backgroundColor: backgroundClass ? undefined : customBackgroundColor
    };
    return Object(external_this_wp_element_["createElement"])("div", {
      className: className,
      style: styles
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].Content, null));
  }
}];
/* harmony default export */ var group_deprecated = (group_deprecated_deprecated);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/group/edit.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






function GroupEdit(_ref) {
  var className = _ref.className,
      setBackgroundColor = _ref.setBackgroundColor,
      backgroundColor = _ref.backgroundColor,
      hasInnerBlocks = _ref.hasInnerBlocks;
  var styles = {
    backgroundColor: backgroundColor.color
  };
  var classes = classnames_default()(className, backgroundColor.class, {
    'has-background': !!backgroundColor.color
  });
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
    title: Object(external_this_wp_i18n_["__"])('Color Settings'),
    colorSettings: [{
      value: backgroundColor.color,
      onChange: setBackgroundColor,
      label: Object(external_this_wp_i18n_["__"])('Background Color')
    }]
  })), Object(external_this_wp_element_["createElement"])("div", {
    className: classes,
    style: styles
  }, Object(external_this_wp_element_["createElement"])("div", {
    className: "wp-block-group__inner-container"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"], {
    renderAppender: !hasInnerBlocks && external_this_wp_blockEditor_["InnerBlocks"].ButtonBlockAppender
  }))));
}

/* harmony default export */ var group_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_blockEditor_["withColors"])('backgroundColor'), Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var clientId = _ref2.clientId;

  var _select = select('core/block-editor'),
      getBlock = _select.getBlock;

  var block = getBlock(clientId);
  return {
    hasInnerBlocks: !!(block && block.innerBlocks.length)
  };
})])(GroupEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/group/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var group_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  width: "24",
  height: "24",
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M9 8a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-1v3a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1h1V8zm2 3h4V9h-4v2zm2 2H9v2h4v-2z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M2 4.732A2 2 0 1 1 4.732 2h14.536A2 2 0 1 1 22 4.732v14.536A2 2 0 1 1 19.268 22H4.732A2 2 0 1 1 2 19.268V4.732zM4.732 4h14.536c.175.304.428.557.732.732v14.536a2.01 2.01 0 0 0-.732.732H4.732A2.01 2.01 0 0 0 4 19.268V4.732A2.01 2.01 0 0 0 4.732 4z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/group/save.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function group_save_save(_ref) {
  var attributes = _ref.attributes;
  var backgroundColor = attributes.backgroundColor,
      customBackgroundColor = attributes.customBackgroundColor;
  var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
  var className = classnames_default()(backgroundClass, {
    'has-background': backgroundColor || customBackgroundColor
  });
  var styles = {
    backgroundColor: backgroundClass ? undefined : customBackgroundColor
  };
  return Object(external_this_wp_element_["createElement"])("div", {
    className: className,
    style: styles
  }, Object(external_this_wp_element_["createElement"])("div", {
    className: "wp-block-group__inner-container"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"].Content, null)));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/group/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




var group_metadata = {
  name: "core/group",
  category: "layout",
  attributes: {
    backgroundColor: {
      type: "string"
    },
    customBackgroundColor: {
      type: "string"
    }
  }
};

var group_name = group_metadata.name;

var group_settings = {
  title: Object(external_this_wp_i18n_["__"])('Group'),
  icon: group_icon,
  description: Object(external_this_wp_i18n_["__"])('A block that groups other blocks.'),
  keywords: [Object(external_this_wp_i18n_["__"])('container'), Object(external_this_wp_i18n_["__"])('wrapper'), Object(external_this_wp_i18n_["__"])('row'), Object(external_this_wp_i18n_["__"])('section')],
  example: {
    attributes: {
      customBackgroundColor: '#ffffff'
    },
    innerBlocks: [{
      name: 'core/paragraph',
      attributes: {
        customTextColor: '#cf2e2e',
        fontSize: 'large',
        content: Object(external_this_wp_i18n_["__"])('One.')
      }
    }, {
      name: 'core/paragraph',
      attributes: {
        customTextColor: '#ff6900',
        fontSize: 'large',
        content: Object(external_this_wp_i18n_["__"])('Two.')
      }
    }, {
      name: 'core/paragraph',
      attributes: {
        customTextColor: '#fcb900',
        fontSize: 'large',
        content: Object(external_this_wp_i18n_["__"])('Three.')
      }
    }, {
      name: 'core/paragraph',
      attributes: {
        customTextColor: '#00d084',
        fontSize: 'large',
        content: Object(external_this_wp_i18n_["__"])('Four.')
      }
    }, {
      name: 'core/paragraph',
      attributes: {
        customTextColor: '#0693e3',
        fontSize: 'large',
        content: Object(external_this_wp_i18n_["__"])('Five.')
      }
    }, {
      name: 'core/paragraph',
      attributes: {
        customTextColor: '#9b51e0',
        fontSize: 'large',
        content: Object(external_this_wp_i18n_["__"])('Six.')
      }
    }]
  },
  supports: {
    align: ['wide', 'full'],
    anchor: true,
    html: false
  },
  transforms: {
    from: [{
      type: 'block',
      isMultiBlock: true,
      blocks: ['*'],
      __experimentalConvert: function __experimentalConvert(blocks) {
        // Avoid transforming a single `core/group` Block
        if (blocks.length === 1 && blocks[0].name === 'core/group') {
          return;
        }

        var alignments = ['wide', 'full']; // Determine the widest setting of all the blocks to be grouped

        var widestAlignment = blocks.reduce(function (result, block) {
          var align = block.attributes.align;
          return alignments.indexOf(align) > alignments.indexOf(result) ? align : result;
        }, undefined); // Clone the Blocks to be Grouped
        // Failing to create new block references causes the original blocks
        // to be replaced in the switchToBlockType call thereby meaning they
        // are removed both from their original location and within the
        // new group block.

        var groupInnerBlocks = blocks.map(function (block) {
          return Object(external_this_wp_blocks_["createBlock"])(block.name, block.attributes, block.innerBlocks);
        });
        return Object(external_this_wp_blocks_["createBlock"])('core/group', {
          align: widestAlignment
        }, groupInnerBlocks);
      }
    }]
  },
  edit: group_edit,
  save: group_save_save,
  deprecated: group_deprecated
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/separator/edit.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





function SeparatorEdit(_ref) {
  var color = _ref.color,
      setColor = _ref.setColor,
      className = _ref.className;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["HorizontalRule"], {
    className: classnames_default()(className, Object(defineProperty["a" /* default */])({
      'has-background': color.color
    }, color.class, color.class)),
    style: {
      backgroundColor: color.color,
      color: color.color
    }
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
    title: Object(external_this_wp_i18n_["__"])('Color Settings'),
    colorSettings: [{
      value: color.color,
      onChange: setColor,
      label: Object(external_this_wp_i18n_["__"])('Color')
    }]
  })));
}

/* harmony default export */ var separator_edit = (Object(external_this_wp_blockEditor_["withColors"])('color', {
  textColor: 'color'
})(SeparatorEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/separator/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var separator_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M19 13H5v-2h14v2z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/separator/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function separatorSave(_ref) {
  var _classnames;

  var attributes = _ref.attributes;
  var color = attributes.color,
      customColor = attributes.customColor; // the hr support changing color using border-color, since border-color
  // is not yet supported in the color palette, we use background-color

  var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', color); // the dots styles uses text for the dots, to change those dots color is
  // using color, not backgroundColor

  var colorClass = Object(external_this_wp_blockEditor_["getColorClassName"])('color', color);
  var separatorClasses = classnames_default()((_classnames = {
    'has-text-color has-background': color || customColor
  }, Object(defineProperty["a" /* default */])(_classnames, backgroundClass, backgroundClass), Object(defineProperty["a" /* default */])(_classnames, colorClass, colorClass), _classnames));
  var separatorStyle = {
    backgroundColor: backgroundClass ? undefined : customColor,
    color: colorClass ? undefined : customColor
  };
  return Object(external_this_wp_element_["createElement"])("hr", {
    className: separatorClasses,
    style: separatorStyle
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/separator/transforms.js
/**
 * WordPress dependencies
 */

var separator_transforms_transforms = {
  from: [{
    type: 'enter',
    regExp: /^-{3,}$/,
    transform: function transform() {
      return Object(external_this_wp_blocks_["createBlock"])('core/separator');
    }
  }, {
    type: 'raw',
    selector: 'hr',
    schema: {
      hr: {}
    }
  }]
};
/* harmony default export */ var separator_transforms = (separator_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/separator/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var separator_metadata = {
  name: "core/separator",
  category: "layout",
  attributes: {
    color: {
      type: "string"
    },
    customColor: {
      type: "string"
    }
  }
};


var separator_name = separator_metadata.name;

var separator_settings = {
  title: Object(external_this_wp_i18n_["__"])('Separator'),
  description: Object(external_this_wp_i18n_["__"])('Create a break between ideas or sections with a horizontal separator.'),
  icon: separator_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('horizontal-line'), 'hr', Object(external_this_wp_i18n_["__"])('divider')],
  example: {
    attributes: {
      customColor: '#065174',
      className: 'is-style-wide'
    }
  },
  styles: [{
    name: 'default',
    label: Object(external_this_wp_i18n_["__"])('Default'),
    isDefault: true
  }, {
    name: 'wide',
    label: Object(external_this_wp_i18n_["__"])('Wide Line')
  }, {
    name: 'dots',
    label: Object(external_this_wp_i18n_["__"])('Dots')
  }],
  transforms: separator_transforms,
  edit: separator_edit,
  save: separatorSave
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/shortcode/edit.js


/**
 * WordPress dependencies
 */





var edit_ShortcodeEdit = function ShortcodeEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      instanceId = _ref.instanceId;
  var inputId = "blocks-shortcode-input-".concat(instanceId);
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "wp-block-shortcode  components-placeholder"
  }, Object(external_this_wp_element_["createElement"])("label", {
    htmlFor: inputId,
    className: "components-placeholder__label"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dashicon"], {
    icon: "shortcode"
  }), Object(external_this_wp_i18n_["__"])('Shortcode')), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PlainText"], {
    className: "input-control",
    id: inputId,
    value: attributes.text,
    placeholder: Object(external_this_wp_i18n_["__"])('Write shortcode here…'),
    onChange: function onChange(text) {
      return setAttributes({
        text: text
      });
    }
  }));
};

/* harmony default export */ var shortcode_edit = (Object(external_this_wp_compose_["withInstanceId"])(edit_ShortcodeEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/shortcode/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var shortcode_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M8.5,21.4l1.9,0.5l5.2-19.3l-1.9-0.5L8.5,21.4z M3,19h4v-2H5V7h2V5H3V19z M17,5v2h2v10h-2v2h4V5H17z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/shortcode/save.js


/**
 * WordPress dependencies
 */

function shortcode_save_save(_ref) {
  var attributes = _ref.attributes;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, attributes.text);
}

// EXTERNAL MODULE: external {"this":["wp","autop"]}
var external_this_wp_autop_ = __webpack_require__(69);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/shortcode/transforms.js
/**
 * WordPress dependencies
 */

var shortcode_transforms_transforms = {
  from: [{
    type: 'shortcode',
    // Per "Shortcode names should be all lowercase and use all
    // letters, but numbers and underscores should work fine too.
    // Be wary of using hyphens (dashes), you'll be better off not
    // using them." in https://codex.wordpress.org/Shortcode_API
    // Require that the first character be a letter. This notably
    // prevents footnote markings ([1]) from being caught as
    // shortcodes.
    tag: '[a-z][a-z0-9_-]*',
    attributes: {
      text: {
        type: 'string',
        shortcode: function shortcode(attrs, _ref) {
          var content = _ref.content;
          return Object(external_this_wp_autop_["removep"])(Object(external_this_wp_autop_["autop"])(content));
        }
      }
    },
    priority: 20
  }]
};
/* harmony default export */ var shortcode_transforms = (shortcode_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/shortcode/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





var shortcode_name = 'core/shortcode';
var shortcode_settings = {
  title: Object(external_this_wp_i18n_["__"])('Shortcode'),
  description: Object(external_this_wp_i18n_["__"])('Insert additional custom elements with a WordPress shortcode.'),
  icon: shortcode_icon,
  category: 'widgets',
  transforms: shortcode_transforms,
  supports: {
    customClassName: false,
    className: false,
    html: false
  },
  edit: shortcode_edit,
  save: shortcode_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/spacer/edit.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








var edit_SpacerEdit = function SpacerEdit(_ref) {
  var attributes = _ref.attributes,
      isSelected = _ref.isSelected,
      setAttributes = _ref.setAttributes,
      instanceId = _ref.instanceId,
      onResizeStart = _ref.onResizeStart,
      _onResizeStop = _ref.onResizeStop;
  var height = attributes.height;
  var id = "block-spacer-height-input-".concat(instanceId);

  var _useState = Object(external_this_wp_element_["useState"])(height),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      inputHeightValue = _useState2[0],
      setInputHeightValue = _useState2[1];

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ResizableBox"], {
    className: classnames_default()('block-library-spacer__resize-container', {
      'is-selected': isSelected
    }),
    size: {
      height: height
    },
    minHeight: "20",
    enable: {
      top: false,
      right: false,
      bottom: true,
      left: false,
      topRight: false,
      bottomRight: false,
      bottomLeft: false,
      topLeft: false
    },
    onResizeStart: onResizeStart,
    onResizeStop: function onResizeStop(event, direction, elt, delta) {
      _onResizeStop();

      var spacerHeight = parseInt(height + delta.height, 10);
      setAttributes({
        height: spacerHeight
      });
      setInputHeightValue(spacerHeight);
    }
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Spacer Settings')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["BaseControl"], {
    label: Object(external_this_wp_i18n_["__"])('Height in pixels'),
    id: id
  }, Object(external_this_wp_element_["createElement"])("input", {
    type: "number",
    id: id,
    onChange: function onChange(event) {
      var spacerHeight = parseInt(event.target.value, 10);
      setInputHeightValue(spacerHeight);

      if (isNaN(spacerHeight)) {
        // Set spacer height to default size and input box to empty string
        setInputHeightValue('');
        spacerHeight = 100;
      } else if (spacerHeight < 20) {
        // Set spacer height to minimum size
        spacerHeight = 20;
      }

      setAttributes({
        height: spacerHeight
      });
    },
    value: inputHeightValue,
    min: "20",
    step: "10"
  })))));
};

/* harmony default export */ var spacer_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/block-editor'),
      toggleSelection = _dispatch.toggleSelection;

  return {
    onResizeStart: function onResizeStart() {
      return toggleSelection(false);
    },
    onResizeStop: function onResizeStop() {
      return toggleSelection(true);
    }
  };
}), external_this_wp_compose_["withInstanceId"]])(edit_SpacerEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/spacer/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var spacer_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M13 4v2h3.59L6 16.59V13H4v7h7v-2H7.41L18 7.41V11h2V4h-7"
}))));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/spacer/save.js

function spacer_save_save(_ref) {
  var attributes = _ref.attributes;
  return Object(external_this_wp_element_["createElement"])("div", {
    style: {
      height: attributes.height
    },
    "aria-hidden": true
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/spacer/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var spacer_metadata = {
  name: "core/spacer",
  category: "layout",
  attributes: {
    height: {
      type: "number",
      "default": 100
    }
  }
};

var spacer_name = spacer_metadata.name;

var spacer_settings = {
  title: Object(external_this_wp_i18n_["__"])('Spacer'),
  description: Object(external_this_wp_i18n_["__"])('Add white space between blocks and customize its height.'),
  icon: spacer_icon,
  edit: spacer_edit,
  save: spacer_save_save
};

// EXTERNAL MODULE: external {"this":["wp","deprecated"]}
var external_this_wp_deprecated_ = __webpack_require__(37);
var external_this_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_deprecated_);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/subhead/edit.js


/**
 * WordPress dependencies
 */



function SubheadEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      className = _ref.className;
  var align = attributes.align,
      content = attributes.content,
      placeholder = attributes.placeholder;
  external_this_wp_deprecated_default()('The Subheading block', {
    alternative: 'the Paragraph block',
    plugin: 'Gutenberg'
  });
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["AlignmentToolbar"], {
    value: align,
    onChange: function onChange(nextAlign) {
      setAttributes({
        align: nextAlign
      });
    }
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
    tagName: "p",
    value: content,
    onChange: function onChange(nextContent) {
      setAttributes({
        content: nextContent
      });
    },
    style: {
      textAlign: align
    },
    className: className,
    placeholder: placeholder || Object(external_this_wp_i18n_["__"])('Write subheading…')
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/subhead/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var subhead_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M7.1 6l-.5 3h4.5L9.4 19h3l1.8-10h4.5l.5-3H7.1z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/subhead/save.js


/**
 * WordPress dependencies
 */

function subhead_save_save(_ref) {
  var attributes = _ref.attributes;
  var align = attributes.align,
      content = attributes.content;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "p",
    style: {
      textAlign: align
    },
    value: content
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/subhead/tranforms.js
/**
 * WordPress dependencies
 */

var tranforms_transforms = {
  to: [{
    type: 'block',
    blocks: ['core/paragraph'],
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/paragraph', attributes);
    }
  }]
};
/* harmony default export */ var tranforms = (tranforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/subhead/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var subhead_metadata = {
  name: "core/subhead",
  category: "common",
  attributes: {
    align: {
      type: "string"
    },
    content: {
      type: "string",
      source: "html",
      selector: "p"
    }
  }
};


var subhead_name = subhead_metadata.name;

var subhead_settings = {
  title: Object(external_this_wp_i18n_["__"])('Subheading (deprecated)'),
  description: Object(external_this_wp_i18n_["__"])('This block is deprecated. Please use the Paragraph block instead.'),
  icon: subhead_icon,
  supports: {
    // Hide from inserter as this block is deprecated.
    inserter: false,
    multiple: false
  },
  transforms: tranforms,
  edit: SubheadEdit,
  save: subhead_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/table/deprecated.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

var deprecated_metadata = {
  name: "core/table",
  category: "formatting",
  attributes: {
    hasFixedLayout: {
      type: "boolean",
      "default": false
    },
    backgroundColor: {
      type: "string"
    },
    head: {
      type: "array",
      "default": [],
      source: "query",
      selector: "thead tr",
      query: {
        cells: {
          type: "array",
          "default": [],
          source: "query",
          selector: "td,th",
          query: {
            content: {
              type: "string",
              source: "html"
            },
            tag: {
              type: "string",
              "default": "td",
              source: "tag"
            },
            scope: {
              type: "string",
              source: "attribute",
              attribute: "scope"
            },
            align: {
              type: "string",
              source: "attribute",
              attribute: "data-align"
            }
          }
        }
      }
    },
    body: {
      type: "array",
      "default": [],
      source: "query",
      selector: "tbody tr",
      query: {
        cells: {
          type: "array",
          "default": [],
          source: "query",
          selector: "td,th",
          query: {
            content: {
              type: "string",
              source: "html"
            },
            tag: {
              type: "string",
              "default": "td",
              source: "tag"
            },
            scope: {
              type: "string",
              source: "attribute",
              attribute: "scope"
            },
            align: {
              type: "string",
              source: "attribute",
              attribute: "data-align"
            }
          }
        }
      }
    },
    foot: {
      type: "array",
      "default": [],
      source: "query",
      selector: "tfoot tr",
      query: {
        cells: {
          type: "array",
          "default": [],
          source: "query",
          selector: "td,th",
          query: {
            content: {
              type: "string",
              source: "html"
            },
            tag: {
              type: "string",
              "default": "td",
              source: "tag"
            },
            scope: {
              type: "string",
              source: "attribute",
              attribute: "scope"
            },
            align: {
              type: "string",
              source: "attribute",
              attribute: "data-align"
            }
          }
        }
      }
    }
  }
};
var table_deprecated_supports = {
  align: true
};
var table_deprecated_deprecated = [{
  attributes: deprecated_metadata.attributes,
  supports: table_deprecated_supports,
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var hasFixedLayout = attributes.hasFixedLayout,
        head = attributes.head,
        body = attributes.body,
        foot = attributes.foot,
        backgroundColor = attributes.backgroundColor;
    var isEmpty = !head.length && !body.length && !foot.length;

    if (isEmpty) {
      return null;
    }

    var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
    var classes = classnames_default()(backgroundClass, {
      'has-fixed-layout': hasFixedLayout,
      'has-background': !!backgroundClass
    });

    var Section = function Section(_ref2) {
      var type = _ref2.type,
          rows = _ref2.rows;

      if (!rows.length) {
        return null;
      }

      var Tag = "t".concat(type);
      return Object(external_this_wp_element_["createElement"])(Tag, null, rows.map(function (_ref3, rowIndex) {
        var cells = _ref3.cells;
        return Object(external_this_wp_element_["createElement"])("tr", {
          key: rowIndex
        }, cells.map(function (_ref4, cellIndex) {
          var content = _ref4.content,
              tag = _ref4.tag,
              scope = _ref4.scope;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
            tagName: tag,
            value: content,
            key: cellIndex,
            scope: tag === 'th' ? scope : undefined
          });
        }));
      }));
    };

    return Object(external_this_wp_element_["createElement"])("table", {
      className: classes
    }, Object(external_this_wp_element_["createElement"])(Section, {
      type: "head",
      rows: head
    }), Object(external_this_wp_element_["createElement"])(Section, {
      type: "body",
      rows: body
    }), Object(external_this_wp_element_["createElement"])(Section, {
      type: "foot",
      rows: foot
    }));
  }
}];
/* harmony default export */ var table_deprecated = (table_deprecated_deprecated);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/table/state.js




/**
 * External dependencies
 */

var INHERITED_COLUMN_ATTRIBUTES = ['align'];
/**
 * Creates a table state.
 *
 * @param {Object} options
 * @param {number} options.rowCount    Row count for the table to create.
 * @param {number} options.columnCount Column count for the table to create.
 *
 * @return {Object} New table state.
 */

function createTable(_ref) {
  var rowCount = _ref.rowCount,
      columnCount = _ref.columnCount;
  return {
    body: Object(external_lodash_["times"])(rowCount, function () {
      return {
        cells: Object(external_lodash_["times"])(columnCount, function () {
          return {
            content: '',
            tag: 'td'
          };
        })
      };
    })
  };
}
/**
 * Returns the first row in the table.
 *
 * @param {Object} state Current table state.
 *
 * @return {Object} The first table row.
 */

function getFirstRow(state) {
  if (!isEmptyTableSection(state.head)) {
    return state.head[0];
  }

  if (!isEmptyTableSection(state.body)) {
    return state.body[0];
  }

  if (!isEmptyTableSection(state.foot)) {
    return state.foot[0];
  }
}
/**
 * Gets an attribute for a cell.
 *
 * @param {Object} state 		 Current table state.
 * @param {Object} cellLocation  The location of the cell
 * @param {string} attributeName The name of the attribute to get the value of.
 *
 * @return {*} The attribute value.
 */

function getCellAttribute(state, cellLocation, attributeName) {
  var sectionName = cellLocation.sectionName,
      rowIndex = cellLocation.rowIndex,
      columnIndex = cellLocation.columnIndex;
  return Object(external_lodash_["get"])(state, [sectionName, rowIndex, 'cells', columnIndex, attributeName]);
}
/**
 * Returns updated cell attributes after applying the `updateCell` function to the selection.
 *
 * @param {Object}   state      The block attributes.
 * @param {Object}   selection  The selection of cells to update.
 * @param {Function} updateCell A function to update the selected cell attributes.
 *
 * @return {Object} New table state including the updated cells.
 */

function updateSelectedCell(state, selection, updateCell) {
  if (!selection) {
    return state;
  }

  var tableSections = Object(external_lodash_["pick"])(state, ['head', 'body', 'foot']);
  var selectionSectionName = selection.sectionName,
      selectionRowIndex = selection.rowIndex;
  return Object(external_lodash_["mapValues"])(tableSections, function (section, sectionName) {
    if (selectionSectionName && selectionSectionName !== sectionName) {
      return section;
    }

    return section.map(function (row, rowIndex) {
      if (selectionRowIndex && selectionRowIndex !== rowIndex) {
        return row;
      }

      return {
        cells: row.cells.map(function (cellAttributes, columnIndex) {
          var cellLocation = {
            sectionName: sectionName,
            columnIndex: columnIndex,
            rowIndex: rowIndex
          };

          if (!isCellSelected(cellLocation, selection)) {
            return cellAttributes;
          }

          return updateCell(cellAttributes);
        })
      };
    });
  });
}
/**
 * Returns whether the cell at `cellLocation` is included in the selection `selection`.
 *
 * @param {Object} cellLocation An object containing cell location properties.
 * @param {Object} selection    An object containing selection properties.
 *
 * @return {boolean} True if the cell is selected, false otherwise.
 */

function isCellSelected(cellLocation, selection) {
  if (!cellLocation || !selection) {
    return false;
  }

  switch (selection.type) {
    case 'column':
      return selection.type === 'column' && cellLocation.columnIndex === selection.columnIndex;

    case 'cell':
      return selection.type === 'cell' && cellLocation.sectionName === selection.sectionName && cellLocation.columnIndex === selection.columnIndex && cellLocation.rowIndex === selection.rowIndex;
  }
}
/**
 * Inserts a row in the table state.
 *
 * @param {Object} state               Current table state.
 * @param {Object} options
 * @param {string} options.sectionName Section in which to insert the row.
 * @param {number} options.rowIndex    Row index at which to insert the row.
 *
 * @return {Object} New table state.
 */

function insertRow(state, _ref2) {
  var sectionName = _ref2.sectionName,
      rowIndex = _ref2.rowIndex,
      columnCount = _ref2.columnCount;
  var firstRow = getFirstRow(state);
  var cellCount = columnCount === undefined ? Object(external_lodash_["get"])(firstRow, ['cells', 'length']) : columnCount; // Bail early if the function cannot determine how many cells to add.

  if (!cellCount) {
    return state;
  }

  return Object(defineProperty["a" /* default */])({}, sectionName, [].concat(Object(toConsumableArray["a" /* default */])(state[sectionName].slice(0, rowIndex)), [{
    cells: Object(external_lodash_["times"])(cellCount, function (index) {
      var firstCellInColumn = Object(external_lodash_["get"])(firstRow, ['cells', index], {});
      var inheritedAttributes = Object(external_lodash_["pick"])(firstCellInColumn, INHERITED_COLUMN_ATTRIBUTES);
      return Object(objectSpread["a" /* default */])({}, inheritedAttributes, {
        content: '',
        tag: sectionName === 'head' ? 'th' : 'td'
      });
    })
  }], Object(toConsumableArray["a" /* default */])(state[sectionName].slice(rowIndex))));
}
/**
 * Deletes a row from the table state.
 *
 * @param {Object} state               Current table state.
 * @param {Object} options
 * @param {string} options.sectionName Section in which to delete the row.
 * @param {number} options.rowIndex    Row index to delete.
 *
 * @return {Object} New table state.
 */

function deleteRow(state, _ref4) {
  var sectionName = _ref4.sectionName,
      rowIndex = _ref4.rowIndex;
  return Object(defineProperty["a" /* default */])({}, sectionName, state[sectionName].filter(function (row, index) {
    return index !== rowIndex;
  }));
}
/**
 * Inserts a column in the table state.
 *
 * @param {Object} state               Current table state.
 * @param {Object} options
 * @param {number} options.columnIndex Column index at which to insert the column.
 *
 * @return {Object} New table state.
 */

function insertColumn(state, _ref6) {
  var columnIndex = _ref6.columnIndex;
  var tableSections = Object(external_lodash_["pick"])(state, ['head', 'body', 'foot']);
  return Object(external_lodash_["mapValues"])(tableSections, function (section, sectionName) {
    // Bail early if the table section is empty.
    if (isEmptyTableSection(section)) {
      return section;
    }

    return section.map(function (row) {
      // Bail early if the row is empty or it's an attempt to insert past
      // the last possible index of the array.
      if (isEmptyRow(row) || row.cells.length < columnIndex) {
        return row;
      }

      return {
        cells: [].concat(Object(toConsumableArray["a" /* default */])(row.cells.slice(0, columnIndex)), [{
          content: '',
          tag: sectionName === 'head' ? 'th' : 'td'
        }], Object(toConsumableArray["a" /* default */])(row.cells.slice(columnIndex)))
      };
    });
  });
}
/**
 * Deletes a column from the table state.
 *
 * @param {Object} state               Current table state.
 * @param {Object} options
 * @param {number} options.columnIndex Column index to delete.
 *
 * @return {Object} New table state.
 */

function deleteColumn(state, _ref7) {
  var columnIndex = _ref7.columnIndex;
  var tableSections = Object(external_lodash_["pick"])(state, ['head', 'body', 'foot']);
  return Object(external_lodash_["mapValues"])(tableSections, function (section) {
    // Bail early if the table section is empty.
    if (isEmptyTableSection(section)) {
      return section;
    }

    return section.map(function (row) {
      return {
        cells: row.cells.length >= columnIndex ? row.cells.filter(function (cell, index) {
          return index !== columnIndex;
        }) : row.cells
      };
    }).filter(function (row) {
      return row.cells.length;
    });
  });
}
/**
 * Toggles the existance of a section.
 *
 * @param {Object} state       Current table state.
 * @param {string} sectionName Name of the section to toggle.
 *
 * @return {Object} New table state.
 */

function toggleSection(state, sectionName) {
  // Section exists, replace it with an empty row to remove it.
  if (!isEmptyTableSection(state[sectionName])) {
    return Object(defineProperty["a" /* default */])({}, sectionName, []);
  } // Get the length of the first row of the body to use when creating the header.


  var columnCount = Object(external_lodash_["get"])(state, ['body', 0, 'cells', 'length'], 1); // Section doesn't exist, insert an empty row to create the section.

  return insertRow(state, {
    sectionName: sectionName,
    rowIndex: 0,
    columnCount: columnCount
  });
}
/**
 * Determines whether a table section is empty.
 *
 * @param {Object} section Table section state.
 *
 * @return {boolean} True if the table section is empty, false otherwise.
 */

function isEmptyTableSection(section) {
  return !section || !section.length || Object(external_lodash_["every"])(section, isEmptyRow);
}
/**
 * Determines whether a table row is empty.
 *
 * @param {Object} row Table row state.
 *
 * @return {boolean} True if the table section is empty, false otherwise.
 */

function isEmptyRow(row) {
  return !(row.cells && row.cells.length);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/table/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var table_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M20 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h15c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 2v3H5V5h15zm-5 14h-5v-9h5v9zM5 10h3v9H5v-9zm12 9v-9h3v9h-3z"
}))));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/table/edit.js










/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



var BACKGROUND_COLORS = [{
  color: '#f3f4f5',
  name: 'Subtle light gray',
  slug: 'subtle-light-gray'
}, {
  color: '#e9fbe5',
  name: 'Subtle pale green',
  slug: 'subtle-pale-green'
}, {
  color: '#e7f5fe',
  name: 'Subtle pale blue',
  slug: 'subtle-pale-blue'
}, {
  color: '#fcf0ef',
  name: 'Subtle pale pink',
  slug: 'subtle-pale-pink'
}];
var ALIGNMENT_CONTROLS = [{
  icon: 'editor-alignleft',
  title: Object(external_this_wp_i18n_["__"])('Align Column Left'),
  align: 'left'
}, {
  icon: 'editor-aligncenter',
  title: Object(external_this_wp_i18n_["__"])('Align Column Center'),
  align: 'center'
}, {
  icon: 'editor-alignright',
  title: Object(external_this_wp_i18n_["__"])('Align Column Right'),
  align: 'right'
}];
var withCustomBackgroundColors = Object(external_this_wp_blockEditor_["createCustomColorsHOC"])(BACKGROUND_COLORS);
var edit_TableEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(TableEdit, _Component);

  function TableEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, TableEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(TableEdit).apply(this, arguments));
    _this.onCreateTable = _this.onCreateTable.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onChangeFixedLayout = _this.onChangeFixedLayout.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onChange = _this.onChange.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onChangeInitialColumnCount = _this.onChangeInitialColumnCount.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onChangeInitialRowCount = _this.onChangeInitialRowCount.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.renderSection = _this.renderSection.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.getTableControls = _this.getTableControls.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onInsertRow = _this.onInsertRow.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onInsertRowBefore = _this.onInsertRowBefore.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onInsertRowAfter = _this.onInsertRowAfter.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onDeleteRow = _this.onDeleteRow.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onInsertColumn = _this.onInsertColumn.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onInsertColumnBefore = _this.onInsertColumnBefore.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onInsertColumnAfter = _this.onInsertColumnAfter.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onDeleteColumn = _this.onDeleteColumn.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onToggleHeaderSection = _this.onToggleHeaderSection.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onToggleFooterSection = _this.onToggleFooterSection.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onChangeColumnAlignment = _this.onChangeColumnAlignment.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.getCellAlignment = _this.getCellAlignment.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.state = {
      initialRowCount: 2,
      initialColumnCount: 2,
      selectedCell: null
    };
    return _this;
  }
  /**
   * Updates the initial column count used for table creation.
   *
   * @param {number} initialColumnCount New initial column count.
   */


  Object(createClass["a" /* default */])(TableEdit, [{
    key: "onChangeInitialColumnCount",
    value: function onChangeInitialColumnCount(initialColumnCount) {
      this.setState({
        initialColumnCount: initialColumnCount
      });
    }
    /**
     * Updates the initial row count used for table creation.
     *
     * @param {number} initialRowCount New initial row count.
     */

  }, {
    key: "onChangeInitialRowCount",
    value: function onChangeInitialRowCount(initialRowCount) {
      this.setState({
        initialRowCount: initialRowCount
      });
    }
    /**
     * Creates a table based on dimensions in local state.
     *
     * @param {Object} event Form submit event.
     */

  }, {
    key: "onCreateTable",
    value: function onCreateTable(event) {
      event.preventDefault();
      var setAttributes = this.props.setAttributes;
      var _this$state = this.state,
          initialRowCount = _this$state.initialRowCount,
          initialColumnCount = _this$state.initialColumnCount;
      initialRowCount = parseInt(initialRowCount, 10) || 2;
      initialColumnCount = parseInt(initialColumnCount, 10) || 2;
      setAttributes(createTable({
        rowCount: initialRowCount,
        columnCount: initialColumnCount
      }));
    }
    /**
     * Toggles whether the table has a fixed layout or not.
     */

  }, {
    key: "onChangeFixedLayout",
    value: function onChangeFixedLayout() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      var hasFixedLayout = attributes.hasFixedLayout;
      setAttributes({
        hasFixedLayout: !hasFixedLayout
      });
    }
    /**
     * Changes the content of the currently selected cell.
     *
     * @param {Array} content A RichText content value.
     */

  }, {
    key: "onChange",
    value: function onChange(content) {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      setAttributes(updateSelectedCell(attributes, selectedCell, function (cellAttributes) {
        return Object(objectSpread["a" /* default */])({}, cellAttributes, {
          content: content
        });
      }));
    }
    /**
     * Align text within the a column.
     *
     * @param {string} align The new alignment to apply to the column.
     */

  }, {
    key: "onChangeColumnAlignment",
    value: function onChangeColumnAlignment(align) {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      } // Convert the cell selection to a column selection so that alignment
      // is applied to the entire column.


      var columnSelection = {
        type: 'column',
        columnIndex: selectedCell.columnIndex
      };
      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          setAttributes = _this$props3.setAttributes;
      var newAttributes = updateSelectedCell(attributes, columnSelection, function (cellAttributes) {
        return Object(objectSpread["a" /* default */])({}, cellAttributes, {
          align: align
        });
      });
      setAttributes(newAttributes);
    }
    /**
     * Get the alignment of the currently selected cell.
     *
     * @return {string} The new alignment to apply to the column.
     */

  }, {
    key: "getCellAlignment",
    value: function getCellAlignment() {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var attributes = this.props.attributes;
      return getCellAttribute(attributes, selectedCell, 'align');
    }
    /**
     * Add or remove a `head` table section.
     */

  }, {
    key: "onToggleHeaderSection",
    value: function onToggleHeaderSection() {
      var _this$props4 = this.props,
          attributes = _this$props4.attributes,
          setAttributes = _this$props4.setAttributes;
      setAttributes(toggleSection(attributes, 'head'));
    }
    /**
     * Add or remove a `foot` table section.
     */

  }, {
    key: "onToggleFooterSection",
    value: function onToggleFooterSection() {
      var _this$props5 = this.props,
          attributes = _this$props5.attributes,
          setAttributes = _this$props5.setAttributes;
      setAttributes(toggleSection(attributes, 'foot'));
    }
    /**
     * Inserts a row at the currently selected row index, plus `delta`.
     *
     * @param {number} delta Offset for selected row index at which to insert.
     */

  }, {
    key: "onInsertRow",
    value: function onInsertRow(delta) {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props6 = this.props,
          attributes = _this$props6.attributes,
          setAttributes = _this$props6.setAttributes;
      var sectionName = selectedCell.sectionName,
          rowIndex = selectedCell.rowIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(insertRow(attributes, {
        sectionName: sectionName,
        rowIndex: rowIndex + delta
      }));
    }
    /**
     * Inserts a row before the currently selected row.
     */

  }, {
    key: "onInsertRowBefore",
    value: function onInsertRowBefore() {
      this.onInsertRow(0);
    }
    /**
     * Inserts a row after the currently selected row.
     */

  }, {
    key: "onInsertRowAfter",
    value: function onInsertRowAfter() {
      this.onInsertRow(1);
    }
    /**
     * Deletes the currently selected row.
     */

  }, {
    key: "onDeleteRow",
    value: function onDeleteRow() {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props7 = this.props,
          attributes = _this$props7.attributes,
          setAttributes = _this$props7.setAttributes;
      var sectionName = selectedCell.sectionName,
          rowIndex = selectedCell.rowIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(deleteRow(attributes, {
        sectionName: sectionName,
        rowIndex: rowIndex
      }));
    }
    /**
     * Inserts a column at the currently selected column index, plus `delta`.
     *
     * @param {number} delta Offset for selected column index at which to insert.
     */

  }, {
    key: "onInsertColumn",
    value: function onInsertColumn() {
      var delta = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props8 = this.props,
          attributes = _this$props8.attributes,
          setAttributes = _this$props8.setAttributes;
      var columnIndex = selectedCell.columnIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(insertColumn(attributes, {
        columnIndex: columnIndex + delta
      }));
    }
    /**
     * Inserts a column before the currently selected column.
     */

  }, {
    key: "onInsertColumnBefore",
    value: function onInsertColumnBefore() {
      this.onInsertColumn(0);
    }
    /**
     * Inserts a column after the currently selected column.
     */

  }, {
    key: "onInsertColumnAfter",
    value: function onInsertColumnAfter() {
      this.onInsertColumn(1);
    }
    /**
     * Deletes the currently selected column.
     */

  }, {
    key: "onDeleteColumn",
    value: function onDeleteColumn() {
      var selectedCell = this.state.selectedCell;

      if (!selectedCell) {
        return;
      }

      var _this$props9 = this.props,
          attributes = _this$props9.attributes,
          setAttributes = _this$props9.setAttributes;
      var sectionName = selectedCell.sectionName,
          columnIndex = selectedCell.columnIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(deleteColumn(attributes, {
        sectionName: sectionName,
        columnIndex: columnIndex
      }));
    }
    /**
     * Creates an onFocus handler for a specified cell.
     *
     * @param {Object} cellLocation Object with `section`, `rowIndex`, and
     *                              `columnIndex` properties.
     *
     * @return {Function} Function to call on focus.
     */

  }, {
    key: "createOnFocus",
    value: function createOnFocus(cellLocation) {
      var _this2 = this;

      return function () {
        _this2.setState({
          selectedCell: Object(objectSpread["a" /* default */])({}, cellLocation, {
            type: 'cell'
          })
        });
      };
    }
    /**
     * Gets the table controls to display in the block toolbar.
     *
     * @return {Array} Table controls.
     */

  }, {
    key: "getTableControls",
    value: function getTableControls() {
      var selectedCell = this.state.selectedCell;
      return [{
        icon: 'table-row-before',
        title: Object(external_this_wp_i18n_["__"])('Add Row Before'),
        isDisabled: !selectedCell,
        onClick: this.onInsertRowBefore
      }, {
        icon: 'table-row-after',
        title: Object(external_this_wp_i18n_["__"])('Add Row After'),
        isDisabled: !selectedCell,
        onClick: this.onInsertRowAfter
      }, {
        icon: 'table-row-delete',
        title: Object(external_this_wp_i18n_["__"])('Delete Row'),
        isDisabled: !selectedCell,
        onClick: this.onDeleteRow
      }, {
        icon: 'table-col-before',
        title: Object(external_this_wp_i18n_["__"])('Add Column Before'),
        isDisabled: !selectedCell,
        onClick: this.onInsertColumnBefore
      }, {
        icon: 'table-col-after',
        title: Object(external_this_wp_i18n_["__"])('Add Column After'),
        isDisabled: !selectedCell,
        onClick: this.onInsertColumnAfter
      }, {
        icon: 'table-col-delete',
        title: Object(external_this_wp_i18n_["__"])('Delete Column'),
        isDisabled: !selectedCell,
        onClick: this.onDeleteColumn
      }];
    }
    /**
     * Renders a table section.
     *
     * @param {Object} options
     * @param {string} options.type Section type: head, body, or foot.
     * @param {Array}  options.rows The rows to render.
     *
     * @return {Object} React element for the section.
     */

  }, {
    key: "renderSection",
    value: function renderSection(_ref) {
      var _this3 = this;

      var name = _ref.name,
          rows = _ref.rows;

      if (isEmptyTableSection(rows)) {
        return null;
      }

      var Tag = "t".concat(name);
      var selectedCell = this.state.selectedCell;
      return Object(external_this_wp_element_["createElement"])(Tag, null, rows.map(function (_ref2, rowIndex) {
        var cells = _ref2.cells;
        return Object(external_this_wp_element_["createElement"])("tr", {
          key: rowIndex
        }, cells.map(function (_ref3, columnIndex) {
          var content = _ref3.content,
              CellTag = _ref3.tag,
              scope = _ref3.scope,
              align = _ref3.align;
          var cellLocation = {
            sectionName: name,
            rowIndex: rowIndex,
            columnIndex: columnIndex
          };
          var isSelected = isCellSelected(cellLocation, selectedCell);
          var cellClasses = classnames_default()(Object(defineProperty["a" /* default */])({
            'is-selected': isSelected
          }, "has-text-align-".concat(align), align));
          var richTextClassName = 'wp-block-table__cell-content';
          return Object(external_this_wp_element_["createElement"])(CellTag, {
            key: columnIndex,
            className: cellClasses,
            scope: CellTag === 'th' ? scope : undefined,
            onClick: function onClick(event) {
              // When a cell is selected, forward focus to the child RichText. This solves an issue where the
              // user may click inside a cell, but outside of the RichText, resulting in nothing happening.
              var richTextElement = event && event.target && event.target.querySelector(".".concat(richTextClassName));

              if (richTextElement) {
                richTextElement.focus();
              }
            }
          }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
            className: richTextClassName,
            value: content,
            onChange: _this3.onChange,
            unstableOnFocus: _this3.createOnFocus(cellLocation)
          }));
        }));
      }));
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate() {
      var isSelected = this.props.isSelected;
      var selectedCell = this.state.selectedCell;

      if (!isSelected && selectedCell) {
        this.setState({
          selectedCell: null
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this4 = this;

      var _this$props10 = this.props,
          attributes = _this$props10.attributes,
          className = _this$props10.className,
          backgroundColor = _this$props10.backgroundColor,
          setBackgroundColor = _this$props10.setBackgroundColor;
      var _this$state2 = this.state,
          initialRowCount = _this$state2.initialRowCount,
          initialColumnCount = _this$state2.initialColumnCount;
      var hasFixedLayout = attributes.hasFixedLayout,
          head = attributes.head,
          body = attributes.body,
          foot = attributes.foot;
      var isEmpty = isEmptyTableSection(head) && isEmptyTableSection(body) && isEmptyTableSection(foot);
      var Section = this.renderSection;

      if (isEmpty) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], {
          label: Object(external_this_wp_i18n_["__"])('Table'),
          icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
            icon: table_icon,
            showColors: true
          }),
          instructions: Object(external_this_wp_i18n_["__"])('Insert a table for sharing data.'),
          isColumnLayout: true
        }, Object(external_this_wp_element_["createElement"])("form", {
          className: "wp-block-table__placeholder-form",
          onSubmit: this.onCreateTable
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          type: "number",
          label: Object(external_this_wp_i18n_["__"])('Column Count'),
          value: initialColumnCount,
          onChange: this.onChangeInitialColumnCount,
          min: "1",
          className: "wp-block-table__placeholder-input"
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          type: "number",
          label: Object(external_this_wp_i18n_["__"])('Row Count'),
          value: initialRowCount,
          onChange: this.onChangeInitialRowCount,
          min: "1",
          className: "wp-block-table__placeholder-input"
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
          className: "wp-block-table__placeholder-button",
          isDefault: true,
          type: "submit"
        }, Object(external_this_wp_i18n_["__"])('Create Table'))));
      }

      var tableClasses = classnames_default()(backgroundColor.class, {
        'has-fixed-layout': hasFixedLayout,
        'has-background': !!backgroundColor.color
      });
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["DropdownMenu"], {
        hasArrowIndicator: true,
        icon: "editor-table",
        label: Object(external_this_wp_i18n_["__"])('Edit table'),
        controls: this.getTableControls()
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["AlignmentToolbar"], {
        label: Object(external_this_wp_i18n_["__"])('Change column alignment'),
        alignmentControls: ALIGNMENT_CONTROLS,
        value: this.getCellAlignment(),
        onChange: function onChange(nextAlign) {
          return _this4.onChangeColumnAlignment(nextAlign);
        },
        onHover: this.onHoverAlignment
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Table Settings'),
        className: "blocks-table-settings"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Fixed width table cells'),
        checked: !!hasFixedLayout,
        onChange: this.onChangeFixedLayout
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Header section'),
        checked: !!(head && head.length),
        onChange: this.onToggleHeaderSection
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Footer section'),
        checked: !!(foot && foot.length),
        onChange: this.onToggleFooterSection
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
        title: Object(external_this_wp_i18n_["__"])('Color Settings'),
        initialOpen: false,
        colorSettings: [{
          value: backgroundColor.color,
          onChange: setBackgroundColor,
          label: Object(external_this_wp_i18n_["__"])('Background Color'),
          disableCustomColors: true,
          colors: BACKGROUND_COLORS
        }]
      })), Object(external_this_wp_element_["createElement"])("figure", {
        className: className
      }, Object(external_this_wp_element_["createElement"])("table", {
        className: tableClasses
      }, Object(external_this_wp_element_["createElement"])(Section, {
        name: "head",
        rows: head
      }), Object(external_this_wp_element_["createElement"])(Section, {
        name: "body",
        rows: body
      }), Object(external_this_wp_element_["createElement"])(Section, {
        name: "foot",
        rows: foot
      }))));
    }
  }]);

  return TableEdit;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var table_edit = (withCustomBackgroundColors('backgroundColor')(edit_TableEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/table/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function table_save_save(_ref) {
  var attributes = _ref.attributes;
  var hasFixedLayout = attributes.hasFixedLayout,
      head = attributes.head,
      body = attributes.body,
      foot = attributes.foot,
      backgroundColor = attributes.backgroundColor;
  var isEmpty = !head.length && !body.length && !foot.length;

  if (isEmpty) {
    return null;
  }

  var backgroundClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', backgroundColor);
  var classes = classnames_default()(backgroundClass, {
    'has-fixed-layout': hasFixedLayout,
    'has-background': !!backgroundClass
  });

  var Section = function Section(_ref2) {
    var type = _ref2.type,
        rows = _ref2.rows;

    if (!rows.length) {
      return null;
    }

    var Tag = "t".concat(type);
    return Object(external_this_wp_element_["createElement"])(Tag, null, rows.map(function (_ref3, rowIndex) {
      var cells = _ref3.cells;
      return Object(external_this_wp_element_["createElement"])("tr", {
        key: rowIndex
      }, cells.map(function (_ref4, cellIndex) {
        var content = _ref4.content,
            tag = _ref4.tag,
            scope = _ref4.scope,
            align = _ref4.align;
        var cellClasses = classnames_default()(Object(defineProperty["a" /* default */])({}, "has-text-align-".concat(align), align));
        return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
          className: cellClasses ? cellClasses : undefined,
          "data-align": align,
          tagName: tag,
          value: content,
          key: cellIndex,
          scope: tag === 'th' ? scope : undefined
        });
      }));
    }));
  };

  return Object(external_this_wp_element_["createElement"])("figure", null, Object(external_this_wp_element_["createElement"])("table", {
    className: classes
  }, Object(external_this_wp_element_["createElement"])(Section, {
    type: "head",
    rows: head
  }), Object(external_this_wp_element_["createElement"])(Section, {
    type: "body",
    rows: body
  }), Object(external_this_wp_element_["createElement"])(Section, {
    type: "foot",
    rows: foot
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/table/transforms.js
/**
 * WordPress dependencies
 */

var tableContentPasteSchema = {
  tr: {
    allowEmpty: true,
    children: {
      th: {
        allowEmpty: true,
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])(),
        attributes: ['scope']
      },
      td: {
        allowEmpty: true,
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
      }
    }
  }
};
var tablePasteSchema = {
  table: {
    children: {
      thead: {
        allowEmpty: true,
        children: tableContentPasteSchema
      },
      tfoot: {
        allowEmpty: true,
        children: tableContentPasteSchema
      },
      tbody: {
        allowEmpty: true,
        children: tableContentPasteSchema
      }
    }
  }
};
var table_transforms_transforms = {
  from: [{
    type: 'raw',
    selector: 'table',
    schema: tablePasteSchema
  }]
};
/* harmony default export */ var table_transforms = (table_transforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/table/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var table_metadata = {
  name: "core/table",
  category: "formatting",
  attributes: {
    hasFixedLayout: {
      type: "boolean",
      "default": false
    },
    backgroundColor: {
      type: "string"
    },
    head: {
      type: "array",
      "default": [],
      source: "query",
      selector: "thead tr",
      query: {
        cells: {
          type: "array",
          "default": [],
          source: "query",
          selector: "td,th",
          query: {
            content: {
              type: "string",
              source: "html"
            },
            tag: {
              type: "string",
              "default": "td",
              source: "tag"
            },
            scope: {
              type: "string",
              source: "attribute",
              attribute: "scope"
            },
            align: {
              type: "string",
              source: "attribute",
              attribute: "data-align"
            }
          }
        }
      }
    },
    body: {
      type: "array",
      "default": [],
      source: "query",
      selector: "tbody tr",
      query: {
        cells: {
          type: "array",
          "default": [],
          source: "query",
          selector: "td,th",
          query: {
            content: {
              type: "string",
              source: "html"
            },
            tag: {
              type: "string",
              "default": "td",
              source: "tag"
            },
            scope: {
              type: "string",
              source: "attribute",
              attribute: "scope"
            },
            align: {
              type: "string",
              source: "attribute",
              attribute: "data-align"
            }
          }
        }
      }
    },
    foot: {
      type: "array",
      "default": [],
      source: "query",
      selector: "tfoot tr",
      query: {
        cells: {
          type: "array",
          "default": [],
          source: "query",
          selector: "td,th",
          query: {
            content: {
              type: "string",
              source: "html"
            },
            tag: {
              type: "string",
              "default": "td",
              source: "tag"
            },
            scope: {
              type: "string",
              source: "attribute",
              attribute: "scope"
            },
            align: {
              type: "string",
              source: "attribute",
              attribute: "data-align"
            }
          }
        }
      }
    }
  }
};


var table_name = table_metadata.name;

var table_settings = {
  title: Object(external_this_wp_i18n_["__"])('Table'),
  description: Object(external_this_wp_i18n_["__"])('Insert a table — perfect for sharing charts and data.'),
  icon: table_icon,
  example: {
    attributes: {
      head: [{
        cells: [{
          content: Object(external_this_wp_i18n_["__"])('Version'),
          tag: 'th'
        }, {
          content: Object(external_this_wp_i18n_["__"])('Jazz Musician'),
          tag: 'th'
        }, {
          content: Object(external_this_wp_i18n_["__"])('Release Date'),
          tag: 'th'
        }]
      }],
      body: [{
        cells: [{
          content: '5.2',
          tag: 'td'
        }, {
          content: 'Jaco Pastorius',
          tag: 'td'
        }, {
          content: Object(external_this_wp_i18n_["__"])('May 7, 2019'),
          tag: 'td'
        }]
      }, {
        cells: [{
          content: '5.1',
          tag: 'td'
        }, {
          content: 'Betty Carter',
          tag: 'td'
        }, {
          content: Object(external_this_wp_i18n_["__"])('February 21, 2019'),
          tag: 'td'
        }]
      }, {
        cells: [{
          content: '5.0',
          tag: 'td'
        }, {
          content: 'Bebo Valdés',
          tag: 'td'
        }, {
          content: Object(external_this_wp_i18n_["__"])('December 6, 2018'),
          tag: 'td'
        }]
      }]
    }
  },
  styles: [{
    name: 'regular',
    label: Object(external_this_wp_i18n_["_x"])('Default', 'block style'),
    isDefault: true
  }, {
    name: 'stripes',
    label: Object(external_this_wp_i18n_["__"])('Stripes')
  }],
  supports: {
    align: true
  },
  transforms: table_transforms,
  edit: table_edit,
  save: table_save_save,
  deprecated: table_deprecated
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/text-columns/edit.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





function TextColumnsEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      className = _ref.className;
  var width = attributes.width,
      content = attributes.content,
      columns = attributes.columns;
  external_this_wp_deprecated_default()('The Text Columns block', {
    alternative: 'the Columns block',
    plugin: 'Gutenberg'
  });
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockAlignmentToolbar"], {
    value: width,
    onChange: function onChange(nextWidth) {
      return setAttributes({
        width: nextWidth
      });
    },
    controls: ['center', 'wide', 'full']
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
    label: Object(external_this_wp_i18n_["__"])('Columns'),
    value: columns,
    onChange: function onChange(value) {
      return setAttributes({
        columns: value
      });
    },
    min: 2,
    max: 4,
    required: true
  }))), Object(external_this_wp_element_["createElement"])("div", {
    className: "".concat(className, " align").concat(width, " columns-").concat(columns)
  }, Object(external_lodash_["times"])(columns, function (index) {
    return Object(external_this_wp_element_["createElement"])("div", {
      className: "wp-block-column",
      key: "column-".concat(index)
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
      tagName: "p",
      value: Object(external_lodash_["get"])(content, [index, 'children']),
      onChange: function onChange(nextContent) {
        setAttributes({
          content: [].concat(Object(toConsumableArray["a" /* default */])(content.slice(0, index)), [{
            children: nextContent
          }], Object(toConsumableArray["a" /* default */])(content.slice(index + 1)))
        });
      },
      placeholder: Object(external_this_wp_i18n_["__"])('New Column')
    }));
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/text-columns/save.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function text_columns_save_save(_ref) {
  var attributes = _ref.attributes;
  var width = attributes.width,
      content = attributes.content,
      columns = attributes.columns;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "align".concat(width, " columns-").concat(columns)
  }, Object(external_lodash_["times"])(columns, function (index) {
    return Object(external_this_wp_element_["createElement"])("div", {
      className: "wp-block-column",
      key: "column-".concat(index)
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "p",
      value: Object(external_lodash_["get"])(content, [index, 'children'])
    }));
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/text-columns/tranforms.js
/**
 * WordPress dependencies
 */

var text_columns_tranforms_transforms = {
  to: [{
    type: 'block',
    blocks: ['core/columns'],
    transform: function transform(_ref) {
      var className = _ref.className,
          columns = _ref.columns,
          content = _ref.content,
          width = _ref.width;
      return Object(external_this_wp_blocks_["createBlock"])('core/columns', {
        align: 'wide' === width || 'full' === width ? width : undefined,
        className: className,
        columns: columns
      }, content.map(function (_ref2) {
        var children = _ref2.children;
        return Object(external_this_wp_blocks_["createBlock"])('core/column', {}, [Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
          content: children
        })]);
      }));
    }
  }]
};
/* harmony default export */ var text_columns_tranforms = (text_columns_tranforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/text-columns/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var text_columns_metadata = {
  name: "core/text-columns",
  icon: "columns",
  category: "layout",
  attributes: {
    content: {
      type: "array",
      source: "query",
      selector: "p",
      query: {
        children: {
          type: "string",
          source: "html"
        }
      },
      "default": [{}, {}]
    },
    columns: {
      type: "number",
      "default": 2
    },
    width: {
      type: "string"
    }
  }
};


var text_columns_name = text_columns_metadata.name;

var text_columns_settings = {
  // Disable insertion as this block is deprecated and ultimately replaced by the Columns block.
  supports: {
    inserter: false
  },
  title: Object(external_this_wp_i18n_["__"])('Text Columns (deprecated)'),
  description: Object(external_this_wp_i18n_["__"])('This block is deprecated. Please use the Columns block instead.'),
  transforms: text_columns_tranforms,
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var width = attributes.width;

    if ('wide' === width || 'full' === width) {
      return {
        'data-align': width
      };
    }
  },
  edit: TextColumnsEdit,
  save: text_columns_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/verse/deprecated.js


/**
 * WordPress dependencies
 */

var verse_deprecated_blockAttributes = {
  content: {
    type: 'string',
    source: 'html',
    selector: 'pre',
    default: ''
  },
  textAlign: {
    type: 'string'
  }
};
var verse_deprecated_deprecated = [{
  attributes: verse_deprecated_blockAttributes,
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var textAlign = attributes.textAlign,
        content = attributes.content;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "pre",
      style: {
        textAlign: textAlign
      },
      value: content
    });
  }
}];
/* harmony default export */ var verse_deprecated = (verse_deprecated_deprecated);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/verse/edit.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function VerseEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      className = _ref.className,
      mergeBlocks = _ref.mergeBlocks;
  var textAlign = attributes.textAlign,
      content = attributes.content;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["AlignmentToolbar"], {
    value: textAlign,
    onChange: function onChange(nextAlign) {
      setAttributes({
        textAlign: nextAlign
      });
    }
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
    tagName: "pre",
    value: content,
    onChange: function onChange(nextContent) {
      setAttributes({
        content: nextContent
      });
    },
    placeholder: Object(external_this_wp_i18n_["__"])('Write…'),
    wrapperClassName: className,
    className: classnames_default()(Object(defineProperty["a" /* default */])({}, "has-text-align-".concat(textAlign), textAlign)),
    onMerge: mergeBlocks
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/verse/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var verse_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M21 11.01L3 11V13H21V11.01ZM3 16H17V18H3V16ZM15 6H3V8.01L15 8V6Z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/verse/save.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function verse_save_save(_ref) {
  var attributes = _ref.attributes;
  var textAlign = attributes.textAlign,
      content = attributes.content;
  var className = classnames_default()(Object(defineProperty["a" /* default */])({}, "has-text-align-".concat(textAlign), textAlign));
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "pre",
    className: className,
    value: content
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/verse/tranforms.js
/**
 * WordPress dependencies
 */

var verse_tranforms_transforms = {
  from: [{
    type: 'block',
    blocks: ['core/paragraph'],
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/verse', attributes);
    }
  }],
  to: [{
    type: 'block',
    blocks: ['core/paragraph'],
    transform: function transform(attributes) {
      return Object(external_this_wp_blocks_["createBlock"])('core/paragraph', attributes);
    }
  }]
};
/* harmony default export */ var verse_tranforms = (verse_tranforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/verse/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var verse_metadata = {
  name: "core/verse",
  category: "formatting",
  attributes: {
    content: {
      type: "string",
      source: "html",
      selector: "pre",
      "default": ""
    },
    textAlign: {
      type: "string"
    }
  }
};


var verse_name = verse_metadata.name;

var verse_settings = {
  title: Object(external_this_wp_i18n_["__"])('Verse'),
  description: Object(external_this_wp_i18n_["__"])('Insert poetry. Use special spacing formats. Or quote song lyrics.'),
  icon: verse_icon,
  example: {
    attributes: {
      content: Object(external_this_wp_i18n_["__"])('WHAT was he doing, the great god Pan,') + '<br>' + Object(external_this_wp_i18n_["__"])('    Down in the reeds by the river?') + '<br>' + Object(external_this_wp_i18n_["__"])('Spreading ruin and scattering ban,') + '<br>' + Object(external_this_wp_i18n_["__"])('Splashing and paddling with hoofs of a goat,') + '<br>' + Object(external_this_wp_i18n_["__"])('And breaking the golden lilies afloat') + '<br>' + Object(external_this_wp_i18n_["__"])('    With the dragon-fly on the river.')
    }
  },
  keywords: [Object(external_this_wp_i18n_["__"])('poetry')],
  transforms: verse_tranforms,
  deprecated: verse_deprecated,
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: attributes.content + attributesToMerge.content
    };
  },
  edit: VerseEdit,
  save: verse_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/video/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var video_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M4 6.47L5.76 10H20v8H4V6.47M22 4h-4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/video/edit.js










/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



var video_edit_ALLOWED_MEDIA_TYPES = ['video'];
var VIDEO_POSTER_ALLOWED_MEDIA_TYPES = ['image'];

var edit_VideoEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(VideoEdit, _Component);

  function VideoEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, VideoEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(VideoEdit).apply(this, arguments)); // edit component has its own src in the state so it can be edited
    // without setting the actual value outside of the edit UI

    _this.state = {
      editing: !_this.props.attributes.src
    };
    _this.videoPlayer = Object(external_this_wp_element_["createRef"])();
    _this.posterImageButton = Object(external_this_wp_element_["createRef"])();
    _this.toggleAttribute = _this.toggleAttribute.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSelectURL = _this.onSelectURL.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSelectPoster = _this.onSelectPoster.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onRemovePoster = _this.onRemovePoster.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onUploadError = _this.onUploadError.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(VideoEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          mediaUpload = _this$props.mediaUpload,
          noticeOperations = _this$props.noticeOperations,
          setAttributes = _this$props.setAttributes;
      var id = attributes.id,
          _attributes$src = attributes.src,
          src = _attributes$src === void 0 ? '' : _attributes$src;

      if (!id && Object(external_this_wp_blob_["isBlobURL"])(src)) {
        var file = Object(external_this_wp_blob_["getBlobByURL"])(src);

        if (file) {
          mediaUpload({
            filesList: [file],
            onFileChange: function onFileChange(_ref) {
              var _ref2 = Object(slicedToArray["a" /* default */])(_ref, 1),
                  url = _ref2[0].url;

              setAttributes({
                src: url
              });
            },
            onError: function onError(message) {
              _this2.setState({
                editing: true
              });

              noticeOperations.createErrorNotice(message);
            },
            allowedTypes: video_edit_ALLOWED_MEDIA_TYPES
          });
        }
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.attributes.poster !== prevProps.attributes.poster) {
        this.videoPlayer.current.load();
      }
    }
  }, {
    key: "toggleAttribute",
    value: function toggleAttribute(attribute) {
      var _this3 = this;

      return function (newValue) {
        _this3.props.setAttributes(Object(defineProperty["a" /* default */])({}, attribute, newValue));
      };
    }
  }, {
    key: "onSelectURL",
    value: function onSelectURL(newSrc) {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      var src = attributes.src; // Set the block's src from the edit component's state, and switch off
      // the editing UI.

      if (newSrc !== src) {
        // Check if there's an embed block that handles this URL.
        var embedBlock = util_createUpgradedEmbedBlock({
          attributes: {
            url: newSrc
          }
        });

        if (undefined !== embedBlock) {
          this.props.onReplace(embedBlock);
          return;
        }

        setAttributes({
          src: newSrc,
          id: undefined
        });
      }

      this.setState({
        editing: false
      });
    }
  }, {
    key: "onSelectPoster",
    value: function onSelectPoster(image) {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        poster: image.url
      });
    }
  }, {
    key: "onRemovePoster",
    value: function onRemovePoster() {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        poster: ''
      }); // Move focus back to the Media Upload button.

      this.posterImageButton.current.focus();
    }
  }, {
    key: "onUploadError",
    value: function onUploadError(message) {
      var noticeOperations = this.props.noticeOperations;
      noticeOperations.removeAllNotices();
      noticeOperations.createErrorNotice(message);
    }
  }, {
    key: "getAutoplayHelp",
    value: function getAutoplayHelp(checked) {
      return checked ? Object(external_this_wp_i18n_["__"])('Note: Autoplaying videos may cause usability issues for some visitors.') : null;
    }
  }, {
    key: "render",
    value: function render() {
      var _this4 = this;

      var _this$props$attribute = this.props.attributes,
          autoplay = _this$props$attribute.autoplay,
          caption = _this$props$attribute.caption,
          controls = _this$props$attribute.controls,
          loop = _this$props$attribute.loop,
          muted = _this$props$attribute.muted,
          playsInline = _this$props$attribute.playsInline,
          poster = _this$props$attribute.poster,
          preload = _this$props$attribute.preload,
          src = _this$props$attribute.src;
      var _this$props3 = this.props,
          className = _this$props3.className,
          instanceId = _this$props3.instanceId,
          isSelected = _this$props3.isSelected,
          noticeUI = _this$props3.noticeUI,
          setAttributes = _this$props3.setAttributes;
      var editing = this.state.editing;

      var switchToEditing = function switchToEditing() {
        _this4.setState({
          editing: true
        });
      };

      var onSelectVideo = function onSelectVideo(media) {
        if (!media || !media.url) {
          // in this case there was an error and we should continue in the editing state
          // previous attributes should be removed because they may be temporary blob urls
          setAttributes({
            src: undefined,
            id: undefined
          });
          switchToEditing();
          return;
        } // sets the block's attribute and updates the edit component from the
        // selected media, then switches off the editing UI


        setAttributes({
          src: media.url,
          id: media.id
        });

        _this4.setState({
          src: media.url,
          editing: false
        });
      };

      if (editing) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaPlaceholder"], {
          icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
            icon: video_icon
          }),
          className: className,
          onSelect: onSelectVideo,
          onSelectURL: this.onSelectURL,
          accept: "video/*",
          allowedTypes: video_edit_ALLOWED_MEDIA_TYPES,
          value: this.props.attributes,
          notices: noticeUI,
          onError: this.onUploadError
        });
      }

      var videoPosterDescription = "video-block__poster-image-description-".concat(instanceId);
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        className: "components-icon-button components-toolbar__control",
        label: Object(external_this_wp_i18n_["__"])('Edit video'),
        onClick: switchToEditing,
        icon: "edit"
      }))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Video Settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Autoplay'),
        onChange: this.toggleAttribute('autoplay'),
        checked: autoplay,
        help: this.getAutoplayHelp
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Loop'),
        onChange: this.toggleAttribute('loop'),
        checked: loop
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Muted'),
        onChange: this.toggleAttribute('muted'),
        checked: muted
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Playback Controls'),
        onChange: this.toggleAttribute('controls'),
        checked: controls
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Play inline'),
        onChange: this.toggleAttribute('playsInline'),
        checked: playsInline
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
        label: Object(external_this_wp_i18n_["__"])('Preload'),
        value: preload,
        onChange: function onChange(value) {
          return setAttributes({
            preload: value
          });
        },
        options: [{
          value: 'auto',
          label: Object(external_this_wp_i18n_["__"])('Auto')
        }, {
          value: 'metadata',
          label: Object(external_this_wp_i18n_["__"])('Metadata')
        }, {
          value: 'none',
          label: Object(external_this_wp_i18n_["__"])('None')
        }]
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["BaseControl"], {
        className: "editor-video-poster-control"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["BaseControl"].VisualLabel, null, Object(external_this_wp_i18n_["__"])('Poster Image')), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
        title: Object(external_this_wp_i18n_["__"])('Select Poster Image'),
        onSelect: this.onSelectPoster,
        allowedTypes: VIDEO_POSTER_ALLOWED_MEDIA_TYPES,
        render: function render(_ref3) {
          var open = _ref3.open;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
            isDefault: true,
            onClick: open,
            ref: _this4.posterImageButton,
            "aria-describedby": videoPosterDescription
          }, !_this4.props.attributes.poster ? Object(external_this_wp_i18n_["__"])('Select Poster Image') : Object(external_this_wp_i18n_["__"])('Replace image'));
        }
      }), Object(external_this_wp_element_["createElement"])("p", {
        id: videoPosterDescription,
        hidden: true
      }, this.props.attributes.poster ? Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('The current poster image url is %s'), this.props.attributes.poster) : Object(external_this_wp_i18n_["__"])('There is no poster image currently selected')), !!this.props.attributes.poster && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        onClick: this.onRemovePoster,
        isLink: true,
        isDestructive: true
      }, Object(external_this_wp_i18n_["__"])('Remove Poster Image')))))), Object(external_this_wp_element_["createElement"])("figure", {
        className: className
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])("video", {
        controls: controls,
        poster: poster,
        src: src,
        ref: this.videoPlayer
      })), (!external_this_wp_blockEditor_["RichText"].isEmpty(caption) || isSelected) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        tagName: "figcaption",
        placeholder: Object(external_this_wp_i18n_["__"])('Write caption…'),
        value: caption,
        onChange: function onChange(value) {
          return setAttributes({
            caption: value
          });
        },
        inlineToolbar: true
      })));
    }
  }]);

  return VideoEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var video_edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getSettings = _select.getSettings;

  var _getSettings = getSettings(),
      __experimentalMediaUpload = _getSettings.__experimentalMediaUpload;

  return {
    mediaUpload: __experimentalMediaUpload
  };
}), external_this_wp_components_["withNotices"], external_this_wp_compose_["withInstanceId"]])(edit_VideoEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/video/save.js


/**
 * WordPress dependencies
 */

function video_save_save(_ref) {
  var attributes = _ref.attributes;
  var autoplay = attributes.autoplay,
      caption = attributes.caption,
      controls = attributes.controls,
      loop = attributes.loop,
      muted = attributes.muted,
      poster = attributes.poster,
      preload = attributes.preload,
      src = attributes.src,
      playsInline = attributes.playsInline;
  return Object(external_this_wp_element_["createElement"])("figure", null, src && Object(external_this_wp_element_["createElement"])("video", {
    autoPlay: autoplay,
    controls: controls,
    loop: loop,
    muted: muted,
    poster: poster,
    preload: preload !== 'metadata' ? preload : undefined,
    src: src,
    playsInline: playsInline
  }), !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
    tagName: "figcaption",
    value: caption
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/video/tranforms.js
/**
 * WordPress dependencies
 */


var video_tranforms_transforms = {
  from: [{
    type: 'files',
    isMatch: function isMatch(files) {
      return files.length === 1 && files[0].type.indexOf('video/') === 0;
    },
    transform: function transform(files) {
      var file = files[0]; // We don't need to upload the media directly here
      // It's already done as part of the `componentDidMount`
      // in the video block

      var block = Object(external_this_wp_blocks_["createBlock"])('core/video', {
        src: Object(external_this_wp_blob_["createBlobURL"])(file)
      });
      return block;
    }
  }, {
    type: 'shortcode',
    tag: 'video',
    attributes: {
      src: {
        type: 'string',
        shortcode: function shortcode(_ref) {
          var _ref$named = _ref.named,
              src = _ref$named.src,
              mp4 = _ref$named.mp4,
              m4v = _ref$named.m4v,
              webm = _ref$named.webm,
              ogv = _ref$named.ogv,
              flv = _ref$named.flv;
          return src || mp4 || m4v || webm || ogv || flv;
        }
      },
      poster: {
        type: 'string',
        shortcode: function shortcode(_ref2) {
          var poster = _ref2.named.poster;
          return poster;
        }
      },
      loop: {
        type: 'string',
        shortcode: function shortcode(_ref3) {
          var loop = _ref3.named.loop;
          return loop;
        }
      },
      autoplay: {
        type: 'string',
        shortcode: function shortcode(_ref4) {
          var autoplay = _ref4.named.autoplay;
          return autoplay;
        }
      },
      preload: {
        type: 'string',
        shortcode: function shortcode(_ref5) {
          var preload = _ref5.named.preload;
          return preload;
        }
      }
    }
  }]
};
/* harmony default export */ var video_tranforms = (video_tranforms_transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/video/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var video_metadata = {
  name: "core/video",
  category: "common",
  attributes: {
    autoplay: {
      type: "boolean",
      source: "attribute",
      selector: "video",
      attribute: "autoplay"
    },
    caption: {
      type: "string",
      source: "html",
      selector: "figcaption"
    },
    controls: {
      type: "boolean",
      source: "attribute",
      selector: "video",
      attribute: "controls",
      "default": true
    },
    id: {
      type: "number"
    },
    loop: {
      type: "boolean",
      source: "attribute",
      selector: "video",
      attribute: "loop"
    },
    muted: {
      type: "boolean",
      source: "attribute",
      selector: "video",
      attribute: "muted"
    },
    poster: {
      type: "string",
      source: "attribute",
      selector: "video",
      attribute: "poster"
    },
    preload: {
      type: "string",
      source: "attribute",
      selector: "video",
      attribute: "preload",
      "default": "metadata"
    },
    src: {
      type: "string",
      source: "attribute",
      selector: "video",
      attribute: "src"
    },
    playsInline: {
      type: "boolean",
      source: "attribute",
      selector: "video",
      attribute: "playsinline"
    }
  }
};


var video_name = video_metadata.name;

var video_settings = {
  title: Object(external_this_wp_i18n_["__"])('Video'),
  description: Object(external_this_wp_i18n_["__"])('Embed a video from your media library or upload a new one.'),
  icon: video_icon,
  keywords: [Object(external_this_wp_i18n_["__"])('movie')],
  transforms: video_tranforms,
  supports: {
    align: true
  },
  edit: video_edit,
  save: video_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/tag-cloud/edit.js









/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








var edit_TagCloudEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(TagCloudEdit, _Component);

  function TagCloudEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, TagCloudEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(TagCloudEdit).apply(this, arguments));
    _this.state = {
      editing: !_this.props.attributes.taxonomy
    };
    _this.setTaxonomy = _this.setTaxonomy.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.toggleShowTagCounts = _this.toggleShowTagCounts.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(TagCloudEdit, [{
    key: "getTaxonomyOptions",
    value: function getTaxonomyOptions() {
      var taxonomies = Object(external_lodash_["filter"])(this.props.taxonomies, 'show_cloud');
      var selectOption = {
        label: Object(external_this_wp_i18n_["__"])('- Select -'),
        value: '',
        disabled: true
      };
      var taxonomyOptions = Object(external_lodash_["map"])(taxonomies, function (taxonomy) {
        return {
          value: taxonomy.slug,
          label: taxonomy.name
        };
      });
      return [selectOption].concat(Object(toConsumableArray["a" /* default */])(taxonomyOptions));
    }
  }, {
    key: "setTaxonomy",
    value: function setTaxonomy(taxonomy) {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        taxonomy: taxonomy
      });
    }
  }, {
    key: "toggleShowTagCounts",
    value: function toggleShowTagCounts() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      var showTagCounts = attributes.showTagCounts;
      setAttributes({
        showTagCounts: !showTagCounts
      });
    }
  }, {
    key: "render",
    value: function render() {
      var attributes = this.props.attributes;
      var taxonomy = attributes.taxonomy,
          showTagCounts = attributes.showTagCounts;
      var taxonomyOptions = this.getTaxonomyOptions();
      var inspectorControls = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Tag Cloud Settings')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
        label: Object(external_this_wp_i18n_["__"])('Taxonomy'),
        options: taxonomyOptions,
        value: taxonomy,
        onChange: this.setTaxonomy
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Show post counts'),
        checked: showTagCounts,
        onChange: this.toggleShowTagCounts
      })));
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, inspectorControls, Object(external_this_wp_element_["createElement"])(external_this_wp_serverSideRender_default.a, {
        key: "tag-cloud",
        block: "core/tag-cloud",
        attributes: attributes
      }));
    }
  }]);

  return TagCloudEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var tag_cloud_edit = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    taxonomies: select('core').getTaxonomies()
  };
})(edit_TagCloudEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/tag-cloud/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var tag_cloud_name = 'core/tag-cloud';
var tag_cloud_settings = {
  title: Object(external_this_wp_i18n_["__"])('Tag Cloud'),
  description: Object(external_this_wp_i18n_["__"])('A cloud of your most used tags.'),
  icon: 'tag',
  category: 'widgets',
  supports: {
    html: false,
    align: true
  },
  edit: tag_cloud_edit
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/classic/edit.js









/**
 * WordPress dependencies
 */



var classic_edit_window = window,
    wp = classic_edit_window.wp;

function isTmceEmpty(editor) {
  // When tinyMce is empty the content seems to be:
  // <p><br data-mce-bogus="1"></p>
  // avoid expensive checks for large documents
  var body = editor.getBody();

  if (body.childNodes.length > 1) {
    return false;
  } else if (body.childNodes.length === 0) {
    return true;
  }

  if (body.childNodes[0].childNodes.length > 1) {
    return false;
  }

  return /^\n?$/.test(body.innerText || body.textContent);
}

var edit_ClassicEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ClassicEdit, _Component);

  function ClassicEdit(props) {
    var _this;

    Object(classCallCheck["a" /* default */])(this, ClassicEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ClassicEdit).call(this, props));
    _this.initialize = _this.initialize.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSetup = _this.onSetup.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.focus = _this.focus.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(ClassicEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _window$wpEditorL10n$ = window.wpEditorL10n.tinymce,
          baseURL = _window$wpEditorL10n$.baseURL,
          suffix = _window$wpEditorL10n$.suffix;
      window.tinymce.EditorManager.overrideDefaults({
        base_url: baseURL,
        suffix: suffix
      });

      if (document.readyState === 'complete') {
        this.initialize();
      } else {
        window.addEventListener('DOMContentLoaded', this.initialize);
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      window.addEventListener('DOMContentLoaded', this.initialize);
      wp.oldEditor.remove("editor-".concat(this.props.clientId));
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this$props = this.props,
          clientId = _this$props.clientId,
          content = _this$props.attributes.content;
      var editor = window.tinymce.get("editor-".concat(clientId));

      if (prevProps.attributes.content !== content) {
        editor.setContent(content || '');
      }
    }
  }, {
    key: "initialize",
    value: function initialize() {
      var clientId = this.props.clientId;
      var settings = window.wpEditorL10n.tinymce.settings;
      wp.oldEditor.initialize("editor-".concat(clientId), {
        tinymce: Object(objectSpread["a" /* default */])({}, settings, {
          inline: true,
          content_css: false,
          fixed_toolbar_container: "#toolbar-".concat(clientId),
          setup: this.onSetup
        })
      });
    }
  }, {
    key: "onSetup",
    value: function onSetup(editor) {
      var _this2 = this;

      var _this$props2 = this.props,
          content = _this$props2.attributes.content,
          setAttributes = _this$props2.setAttributes;
      var ref = this.ref;
      var bookmark;
      this.editor = editor;

      if (content) {
        editor.on('loadContent', function () {
          return editor.setContent(content);
        });
      }

      editor.on('blur', function () {
        bookmark = editor.selection.getBookmark(2, true);
        setAttributes({
          content: editor.getContent()
        });
        editor.once('focus', function () {
          if (bookmark) {
            editor.selection.moveToBookmark(bookmark);
          }
        });
        return false;
      });
      editor.on('mousedown touchstart', function () {
        bookmark = null;
      });
      editor.on('keydown', function (event) {
        if ((event.keyCode === external_this_wp_keycodes_["BACKSPACE"] || event.keyCode === external_this_wp_keycodes_["DELETE"]) && isTmceEmpty(editor)) {
          // delete the block
          _this2.props.onReplace([]);

          event.preventDefault();
          event.stopImmediatePropagation();
        }

        var altKey = event.altKey;
        /*
         * Prevent Mousetrap from kicking in: TinyMCE already uses its own
         * `alt+f10` shortcut to focus its toolbar.
         */

        if (altKey && event.keyCode === external_this_wp_keycodes_["F10"]) {
          event.stopPropagation();
        }
      }); // TODO: the following is for back-compat with WP 4.9, not needed in WP 5.0. Remove it after the release.

      editor.addButton('kitchensink', {
        tooltip: Object(external_this_wp_i18n_["_x"])('More', 'button to expand options'),
        icon: 'dashicon dashicons-editor-kitchensink',
        onClick: function onClick() {
          var button = this;
          var active = !button.active();
          button.active(active);
          editor.dom.toggleClass(ref, 'has-advanced-toolbar', active);
        }
      }); // Show the second, third, etc. toolbars when the `kitchensink` button is removed by a plugin.

      editor.on('init', function () {
        if (editor.settings.toolbar1 && editor.settings.toolbar1.indexOf('kitchensink') === -1) {
          editor.dom.addClass(ref, 'has-advanced-toolbar');
        }
      });
      editor.addButton('wp_add_media', {
        tooltip: Object(external_this_wp_i18n_["__"])('Insert Media'),
        icon: 'dashicon dashicons-admin-media',
        cmd: 'WP_Medialib'
      }); // End TODO.

      editor.on('init', function () {
        var rootNode = _this2.editor.getBody(); // Create the toolbar by refocussing the editor.


        if (document.activeElement === rootNode) {
          rootNode.blur();

          _this2.editor.focus();
        }
      });
    }
  }, {
    key: "focus",
    value: function focus() {
      if (this.editor) {
        this.editor.focus();
      }
    }
  }, {
    key: "onToolbarKeyDown",
    value: function onToolbarKeyDown(event) {
      // Prevent WritingFlow from kicking in and allow arrows navigation on the toolbar.
      event.stopPropagation(); // Prevent Mousetrap from moving focus to the top toolbar when pressing `alt+f10` on this block toolbar.

      event.nativeEvent.stopImmediatePropagation();
    }
  }, {
    key: "render",
    value: function render() {
      var _this3 = this;

      var clientId = this.props.clientId; // Disable reasons:
      //
      // jsx-a11y/no-static-element-interactions
      //  - the toolbar itself is non-interactive, but must capture events
      //    from the KeyboardShortcuts component to stop their propagation.

      /* eslint-disable jsx-a11y/no-static-element-interactions */

      return [Object(external_this_wp_element_["createElement"])("div", {
        key: "toolbar",
        id: "toolbar-".concat(clientId),
        ref: function ref(_ref) {
          return _this3.ref = _ref;
        },
        className: "block-library-classic__toolbar",
        onClick: this.focus,
        "data-placeholder": Object(external_this_wp_i18n_["__"])('Classic'),
        onKeyDown: this.onToolbarKeyDown
      }), Object(external_this_wp_element_["createElement"])("div", {
        key: "editor",
        id: "editor-".concat(clientId),
        className: "wp-block-freeform block-library-rich-text__tinymce"
      })];
      /* eslint-enable jsx-a11y/no-static-element-interactions */
    }
  }]);

  return ClassicEdit;
}(external_this_wp_element_["Component"]);



// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/classic/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var classic_icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m20 7v10h-16v-10h16m0-2h-16c-1.1 0-1.99 0.9-1.99 2l-0.01 10c0 1.1 0.9 2 2 2h16c1.1 0 2-0.9 2-2v-10c0-1.1-0.9-2-2-2z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "11",
  y: "8",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "11",
  y: "11",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "8",
  y: "8",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "8",
  y: "11",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "5",
  y: "11",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "5",
  y: "8",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "8",
  y: "14",
  width: "8",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "14",
  y: "11",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "14",
  y: "8",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "17",
  y: "11",
  width: "2",
  height: "2"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Rect"], {
  x: "17",
  y: "8",
  width: "2",
  height: "2"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/classic/save.js


/**
 * WordPress dependencies
 */

function classic_save_save(_ref) {
  var attributes = _ref.attributes;
  var content = attributes.content;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, content);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/classic/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var classic_metadata = {
  name: "core/freeform",
  category: "formatting",
  attributes: {
    content: {
      type: "string",
      source: "html"
    }
  }
};

var classic_name = classic_metadata.name;

var classic_settings = {
  title: Object(external_this_wp_i18n_["_x"])('Classic', 'block title'),
  description: Object(external_this_wp_i18n_["__"])('Use the classic WordPress editor.'),
  icon: classic_icon,
  supports: {
    className: false,
    customClassName: false,
    // Hide 'Add to Reusable Blocks' on Classic blocks. Showing it causes a
    // confusing UX, because of its similarity to the 'Convert to Blocks' button.
    reusable: false
  },
  edit: edit_ClassicEdit,
  save: classic_save_save
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/index.js



/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */














































/**
 * Function to register an individual block.
 *
 * @param {Object} block The block to be registered.
 *
 */

var build_module_registerBlock = function registerBlock(block) {
  if (!block) {
    return;
  }

  var metadata = block.metadata,
      settings = block.settings,
      name = block.name;

  if (metadata) {
    Object(external_this_wp_blocks_["unstable__bootstrapServerSideBlockDefinitions"])(Object(defineProperty["a" /* default */])({}, name, metadata));
  }

  Object(external_this_wp_blocks_["registerBlockType"])(name, settings);
};
/**
 * Function to register core blocks provided by the block editor.
 *
 * @example
 * ```js
 * import { registerCoreBlocks } from '@wordpress/block-library';
 *
 * registerCoreBlocks();
 * ```
 */


var build_module_registerCoreBlocks = function registerCoreBlocks() {
  [// Common blocks are grouped at the top to prioritize their display
  // in various contexts — like the inserter and auto-complete components.
  paragraph_namespaceObject, image_namespaceObject, heading_namespaceObject, gallery_namespaceObject, list_namespaceObject, quote_namespaceObject, // Register all remaining core blocks.
  shortcode_namespaceObject, archives_namespaceObject, audio_namespaceObject, button_namespaceObject, calendar_namespaceObject, categories_namespaceObject, code_namespaceObject, columns_namespaceObject, column_namespaceObject, cover_namespaceObject, embed_namespaceObject].concat(Object(toConsumableArray["a" /* default */])(embed_common), Object(toConsumableArray["a" /* default */])(embed_others), [file_namespaceObject, group_namespaceObject, window.wp && window.wp.oldEditor ? classic_namespaceObject : null, // Only add the classic block in WP Context
  html_namespaceObject, media_text_namespaceObject, latest_comments_namespaceObject, latest_posts_namespaceObject, missing_namespaceObject, more_namespaceObject, nextpage_namespaceObject, preformatted_namespaceObject, pullquote_namespaceObject, rss_namespaceObject, search_namespaceObject, separator_namespaceObject, block_namespaceObject, spacer_namespaceObject, subhead_namespaceObject, table_namespaceObject, tag_cloud_namespaceObject, text_columns_namespaceObject, verse_namespaceObject, video_namespaceObject]).forEach(build_module_registerBlock);
  Object(external_this_wp_blocks_["setDefaultBlockName"])(paragraph_name);

  if (window.wp && window.wp.oldEditor) {
    Object(external_this_wp_blocks_["setFreeformContentHandlerName"])(classic_name);
  }

  Object(external_this_wp_blocks_["setUnregisteredTypeHandlerName"])(missing_name);

  if (group_namespaceObject) {
    Object(external_this_wp_blocks_["setGroupingBlockName"])(group_name);
  }
};
/**
 * Function to register experimental core blocks depending on editor settings.
 *
 * @param {Object} settings Editor settings.
 *
 * @example
 * ```js
 * import { __experimentalRegisterExperimentalCoreBlocks } from '@wordpress/block-library';
 *
 * __experimentalRegisterExperimentalCoreBlocks( settings );
 * ```
 */

var __experimentalRegisterExperimentalCoreBlocks =  false ? undefined : undefined;


/***/ }),

/***/ 35:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blob"]; }());

/***/ }),

/***/ 37:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["deprecated"]; }());

/***/ }),

/***/ 38:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ 39:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ 42:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["viewport"]; }());

/***/ }),

/***/ 44:
/***/ (function(module, exports, __webpack_require__) {

module.exports = function memize( fn, options ) {
	var size = 0,
		maxSize, head, tail;

	if ( options && options.maxSize ) {
		maxSize = options.maxSize;
	}

	function memoized( /* ...args */ ) {
		var node = head,
			len = arguments.length,
			args, i;

		searchCache: while ( node ) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if ( node.args.length !== arguments.length ) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for ( i = 0; i < len; i++ ) {
				if ( node.args[ i ] !== arguments[ i ] ) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== head ) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if ( node === tail ) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				node.prev.next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				head.prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply( null, args )
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( head ) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if ( size === maxSize ) {
			tail = tail.prev;
			tail.next = null;
		} else {
			size++;
		}

		head = node;

		return node.val;
	}

	memoized.clear = function() {
		head = null;
		tail = null;
		size = 0;
	};

	if ( false ) {}

	return memoized;
};


/***/ }),

/***/ 45:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["a11y"]; }());

/***/ }),

/***/ 48:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_RESULT__;// TinyColor v1.4.1
// https://github.com/bgrins/TinyColor
// Brian Grinstead, MIT License

(function(Math) {

var trimLeft = /^\s+/,
    trimRight = /\s+$/,
    tinyCounter = 0,
    mathRound = Math.round,
    mathMin = Math.min,
    mathMax = Math.max,
    mathRandom = Math.random;

function tinycolor (color, opts) {

    color = (color) ? color : '';
    opts = opts || { };

    // If input is already a tinycolor, return itself
    if (color instanceof tinycolor) {
       return color;
    }
    // If we are called as a function, call using new instead
    if (!(this instanceof tinycolor)) {
        return new tinycolor(color, opts);
    }

    var rgb = inputToRGB(color);
    this._originalInput = color,
    this._r = rgb.r,
    this._g = rgb.g,
    this._b = rgb.b,
    this._a = rgb.a,
    this._roundA = mathRound(100*this._a) / 100,
    this._format = opts.format || rgb.format;
    this._gradientType = opts.gradientType;

    // Don't let the range of [0,255] come back in [0,1].
    // Potentially lose a little bit of precision here, but will fix issues where
    // .5 gets interpreted as half of the total, instead of half of 1
    // If it was supposed to be 128, this was already taken care of by `inputToRgb`
    if (this._r < 1) { this._r = mathRound(this._r); }
    if (this._g < 1) { this._g = mathRound(this._g); }
    if (this._b < 1) { this._b = mathRound(this._b); }

    this._ok = rgb.ok;
    this._tc_id = tinyCounter++;
}

tinycolor.prototype = {
    isDark: function() {
        return this.getBrightness() < 128;
    },
    isLight: function() {
        return !this.isDark();
    },
    isValid: function() {
        return this._ok;
    },
    getOriginalInput: function() {
      return this._originalInput;
    },
    getFormat: function() {
        return this._format;
    },
    getAlpha: function() {
        return this._a;
    },
    getBrightness: function() {
        //http://www.w3.org/TR/AERT#color-contrast
        var rgb = this.toRgb();
        return (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
    },
    getLuminance: function() {
        //http://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef
        var rgb = this.toRgb();
        var RsRGB, GsRGB, BsRGB, R, G, B;
        RsRGB = rgb.r/255;
        GsRGB = rgb.g/255;
        BsRGB = rgb.b/255;

        if (RsRGB <= 0.03928) {R = RsRGB / 12.92;} else {R = Math.pow(((RsRGB + 0.055) / 1.055), 2.4);}
        if (GsRGB <= 0.03928) {G = GsRGB / 12.92;} else {G = Math.pow(((GsRGB + 0.055) / 1.055), 2.4);}
        if (BsRGB <= 0.03928) {B = BsRGB / 12.92;} else {B = Math.pow(((BsRGB + 0.055) / 1.055), 2.4);}
        return (0.2126 * R) + (0.7152 * G) + (0.0722 * B);
    },
    setAlpha: function(value) {
        this._a = boundAlpha(value);
        this._roundA = mathRound(100*this._a) / 100;
        return this;
    },
    toHsv: function() {
        var hsv = rgbToHsv(this._r, this._g, this._b);
        return { h: hsv.h * 360, s: hsv.s, v: hsv.v, a: this._a };
    },
    toHsvString: function() {
        var hsv = rgbToHsv(this._r, this._g, this._b);
        var h = mathRound(hsv.h * 360), s = mathRound(hsv.s * 100), v = mathRound(hsv.v * 100);
        return (this._a == 1) ?
          "hsv("  + h + ", " + s + "%, " + v + "%)" :
          "hsva(" + h + ", " + s + "%, " + v + "%, "+ this._roundA + ")";
    },
    toHsl: function() {
        var hsl = rgbToHsl(this._r, this._g, this._b);
        return { h: hsl.h * 360, s: hsl.s, l: hsl.l, a: this._a };
    },
    toHslString: function() {
        var hsl = rgbToHsl(this._r, this._g, this._b);
        var h = mathRound(hsl.h * 360), s = mathRound(hsl.s * 100), l = mathRound(hsl.l * 100);
        return (this._a == 1) ?
          "hsl("  + h + ", " + s + "%, " + l + "%)" :
          "hsla(" + h + ", " + s + "%, " + l + "%, "+ this._roundA + ")";
    },
    toHex: function(allow3Char) {
        return rgbToHex(this._r, this._g, this._b, allow3Char);
    },
    toHexString: function(allow3Char) {
        return '#' + this.toHex(allow3Char);
    },
    toHex8: function(allow4Char) {
        return rgbaToHex(this._r, this._g, this._b, this._a, allow4Char);
    },
    toHex8String: function(allow4Char) {
        return '#' + this.toHex8(allow4Char);
    },
    toRgb: function() {
        return { r: mathRound(this._r), g: mathRound(this._g), b: mathRound(this._b), a: this._a };
    },
    toRgbString: function() {
        return (this._a == 1) ?
          "rgb("  + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ")" :
          "rgba(" + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ", " + this._roundA + ")";
    },
    toPercentageRgb: function() {
        return { r: mathRound(bound01(this._r, 255) * 100) + "%", g: mathRound(bound01(this._g, 255) * 100) + "%", b: mathRound(bound01(this._b, 255) * 100) + "%", a: this._a };
    },
    toPercentageRgbString: function() {
        return (this._a == 1) ?
          "rgb("  + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%)" :
          "rgba(" + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%, " + this._roundA + ")";
    },
    toName: function() {
        if (this._a === 0) {
            return "transparent";
        }

        if (this._a < 1) {
            return false;
        }

        return hexNames[rgbToHex(this._r, this._g, this._b, true)] || false;
    },
    toFilter: function(secondColor) {
        var hex8String = '#' + rgbaToArgbHex(this._r, this._g, this._b, this._a);
        var secondHex8String = hex8String;
        var gradientType = this._gradientType ? "GradientType = 1, " : "";

        if (secondColor) {
            var s = tinycolor(secondColor);
            secondHex8String = '#' + rgbaToArgbHex(s._r, s._g, s._b, s._a);
        }

        return "progid:DXImageTransform.Microsoft.gradient("+gradientType+"startColorstr="+hex8String+",endColorstr="+secondHex8String+")";
    },
    toString: function(format) {
        var formatSet = !!format;
        format = format || this._format;

        var formattedString = false;
        var hasAlpha = this._a < 1 && this._a >= 0;
        var needsAlphaFormat = !formatSet && hasAlpha && (format === "hex" || format === "hex6" || format === "hex3" || format === "hex4" || format === "hex8" || format === "name");

        if (needsAlphaFormat) {
            // Special case for "transparent", all other non-alpha formats
            // will return rgba when there is transparency.
            if (format === "name" && this._a === 0) {
                return this.toName();
            }
            return this.toRgbString();
        }
        if (format === "rgb") {
            formattedString = this.toRgbString();
        }
        if (format === "prgb") {
            formattedString = this.toPercentageRgbString();
        }
        if (format === "hex" || format === "hex6") {
            formattedString = this.toHexString();
        }
        if (format === "hex3") {
            formattedString = this.toHexString(true);
        }
        if (format === "hex4") {
            formattedString = this.toHex8String(true);
        }
        if (format === "hex8") {
            formattedString = this.toHex8String();
        }
        if (format === "name") {
            formattedString = this.toName();
        }
        if (format === "hsl") {
            formattedString = this.toHslString();
        }
        if (format === "hsv") {
            formattedString = this.toHsvString();
        }

        return formattedString || this.toHexString();
    },
    clone: function() {
        return tinycolor(this.toString());
    },

    _applyModification: function(fn, args) {
        var color = fn.apply(null, [this].concat([].slice.call(args)));
        this._r = color._r;
        this._g = color._g;
        this._b = color._b;
        this.setAlpha(color._a);
        return this;
    },
    lighten: function() {
        return this._applyModification(lighten, arguments);
    },
    brighten: function() {
        return this._applyModification(brighten, arguments);
    },
    darken: function() {
        return this._applyModification(darken, arguments);
    },
    desaturate: function() {
        return this._applyModification(desaturate, arguments);
    },
    saturate: function() {
        return this._applyModification(saturate, arguments);
    },
    greyscale: function() {
        return this._applyModification(greyscale, arguments);
    },
    spin: function() {
        return this._applyModification(spin, arguments);
    },

    _applyCombination: function(fn, args) {
        return fn.apply(null, [this].concat([].slice.call(args)));
    },
    analogous: function() {
        return this._applyCombination(analogous, arguments);
    },
    complement: function() {
        return this._applyCombination(complement, arguments);
    },
    monochromatic: function() {
        return this._applyCombination(monochromatic, arguments);
    },
    splitcomplement: function() {
        return this._applyCombination(splitcomplement, arguments);
    },
    triad: function() {
        return this._applyCombination(triad, arguments);
    },
    tetrad: function() {
        return this._applyCombination(tetrad, arguments);
    }
};

// If input is an object, force 1 into "1.0" to handle ratios properly
// String input requires "1.0" as input, so 1 will be treated as 1
tinycolor.fromRatio = function(color, opts) {
    if (typeof color == "object") {
        var newColor = {};
        for (var i in color) {
            if (color.hasOwnProperty(i)) {
                if (i === "a") {
                    newColor[i] = color[i];
                }
                else {
                    newColor[i] = convertToPercentage(color[i]);
                }
            }
        }
        color = newColor;
    }

    return tinycolor(color, opts);
};

// Given a string or object, convert that input to RGB
// Possible string inputs:
//
//     "red"
//     "#f00" or "f00"
//     "#ff0000" or "ff0000"
//     "#ff000000" or "ff000000"
//     "rgb 255 0 0" or "rgb (255, 0, 0)"
//     "rgb 1.0 0 0" or "rgb (1, 0, 0)"
//     "rgba (255, 0, 0, 1)" or "rgba 255, 0, 0, 1"
//     "rgba (1.0, 0, 0, 1)" or "rgba 1.0, 0, 0, 1"
//     "hsl(0, 100%, 50%)" or "hsl 0 100% 50%"
//     "hsla(0, 100%, 50%, 1)" or "hsla 0 100% 50%, 1"
//     "hsv(0, 100%, 100%)" or "hsv 0 100% 100%"
//
function inputToRGB(color) {

    var rgb = { r: 0, g: 0, b: 0 };
    var a = 1;
    var s = null;
    var v = null;
    var l = null;
    var ok = false;
    var format = false;

    if (typeof color == "string") {
        color = stringInputToObject(color);
    }

    if (typeof color == "object") {
        if (isValidCSSUnit(color.r) && isValidCSSUnit(color.g) && isValidCSSUnit(color.b)) {
            rgb = rgbToRgb(color.r, color.g, color.b);
            ok = true;
            format = String(color.r).substr(-1) === "%" ? "prgb" : "rgb";
        }
        else if (isValidCSSUnit(color.h) && isValidCSSUnit(color.s) && isValidCSSUnit(color.v)) {
            s = convertToPercentage(color.s);
            v = convertToPercentage(color.v);
            rgb = hsvToRgb(color.h, s, v);
            ok = true;
            format = "hsv";
        }
        else if (isValidCSSUnit(color.h) && isValidCSSUnit(color.s) && isValidCSSUnit(color.l)) {
            s = convertToPercentage(color.s);
            l = convertToPercentage(color.l);
            rgb = hslToRgb(color.h, s, l);
            ok = true;
            format = "hsl";
        }

        if (color.hasOwnProperty("a")) {
            a = color.a;
        }
    }

    a = boundAlpha(a);

    return {
        ok: ok,
        format: color.format || format,
        r: mathMin(255, mathMax(rgb.r, 0)),
        g: mathMin(255, mathMax(rgb.g, 0)),
        b: mathMin(255, mathMax(rgb.b, 0)),
        a: a
    };
}


// Conversion Functions
// --------------------

// `rgbToHsl`, `rgbToHsv`, `hslToRgb`, `hsvToRgb` modified from:
// <http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript>

// `rgbToRgb`
// Handle bounds / percentage checking to conform to CSS color spec
// <http://www.w3.org/TR/css3-color/>
// *Assumes:* r, g, b in [0, 255] or [0, 1]
// *Returns:* { r, g, b } in [0, 255]
function rgbToRgb(r, g, b){
    return {
        r: bound01(r, 255) * 255,
        g: bound01(g, 255) * 255,
        b: bound01(b, 255) * 255
    };
}

// `rgbToHsl`
// Converts an RGB color value to HSL.
// *Assumes:* r, g, and b are contained in [0, 255] or [0, 1]
// *Returns:* { h, s, l } in [0,1]
function rgbToHsl(r, g, b) {

    r = bound01(r, 255);
    g = bound01(g, 255);
    b = bound01(b, 255);

    var max = mathMax(r, g, b), min = mathMin(r, g, b);
    var h, s, l = (max + min) / 2;

    if(max == min) {
        h = s = 0; // achromatic
    }
    else {
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch(max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }

        h /= 6;
    }

    return { h: h, s: s, l: l };
}

// `hslToRgb`
// Converts an HSL color value to RGB.
// *Assumes:* h is contained in [0, 1] or [0, 360] and s and l are contained [0, 1] or [0, 100]
// *Returns:* { r, g, b } in the set [0, 255]
function hslToRgb(h, s, l) {
    var r, g, b;

    h = bound01(h, 360);
    s = bound01(s, 100);
    l = bound01(l, 100);

    function hue2rgb(p, q, t) {
        if(t < 0) t += 1;
        if(t > 1) t -= 1;
        if(t < 1/6) return p + (q - p) * 6 * t;
        if(t < 1/2) return q;
        if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
        return p;
    }

    if(s === 0) {
        r = g = b = l; // achromatic
    }
    else {
        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    return { r: r * 255, g: g * 255, b: b * 255 };
}

// `rgbToHsv`
// Converts an RGB color value to HSV
// *Assumes:* r, g, and b are contained in the set [0, 255] or [0, 1]
// *Returns:* { h, s, v } in [0,1]
function rgbToHsv(r, g, b) {

    r = bound01(r, 255);
    g = bound01(g, 255);
    b = bound01(b, 255);

    var max = mathMax(r, g, b), min = mathMin(r, g, b);
    var h, s, v = max;

    var d = max - min;
    s = max === 0 ? 0 : d / max;

    if(max == min) {
        h = 0; // achromatic
    }
    else {
        switch(max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }
    return { h: h, s: s, v: v };
}

// `hsvToRgb`
// Converts an HSV color value to RGB.
// *Assumes:* h is contained in [0, 1] or [0, 360] and s and v are contained in [0, 1] or [0, 100]
// *Returns:* { r, g, b } in the set [0, 255]
 function hsvToRgb(h, s, v) {

    h = bound01(h, 360) * 6;
    s = bound01(s, 100);
    v = bound01(v, 100);

    var i = Math.floor(h),
        f = h - i,
        p = v * (1 - s),
        q = v * (1 - f * s),
        t = v * (1 - (1 - f) * s),
        mod = i % 6,
        r = [v, q, p, p, t, v][mod],
        g = [t, v, v, q, p, p][mod],
        b = [p, p, t, v, v, q][mod];

    return { r: r * 255, g: g * 255, b: b * 255 };
}

// `rgbToHex`
// Converts an RGB color to hex
// Assumes r, g, and b are contained in the set [0, 255]
// Returns a 3 or 6 character hex
function rgbToHex(r, g, b, allow3Char) {

    var hex = [
        pad2(mathRound(r).toString(16)),
        pad2(mathRound(g).toString(16)),
        pad2(mathRound(b).toString(16))
    ];

    // Return a 3 character hex if possible
    if (allow3Char && hex[0].charAt(0) == hex[0].charAt(1) && hex[1].charAt(0) == hex[1].charAt(1) && hex[2].charAt(0) == hex[2].charAt(1)) {
        return hex[0].charAt(0) + hex[1].charAt(0) + hex[2].charAt(0);
    }

    return hex.join("");
}

// `rgbaToHex`
// Converts an RGBA color plus alpha transparency to hex
// Assumes r, g, b are contained in the set [0, 255] and
// a in [0, 1]. Returns a 4 or 8 character rgba hex
function rgbaToHex(r, g, b, a, allow4Char) {

    var hex = [
        pad2(mathRound(r).toString(16)),
        pad2(mathRound(g).toString(16)),
        pad2(mathRound(b).toString(16)),
        pad2(convertDecimalToHex(a))
    ];

    // Return a 4 character hex if possible
    if (allow4Char && hex[0].charAt(0) == hex[0].charAt(1) && hex[1].charAt(0) == hex[1].charAt(1) && hex[2].charAt(0) == hex[2].charAt(1) && hex[3].charAt(0) == hex[3].charAt(1)) {
        return hex[0].charAt(0) + hex[1].charAt(0) + hex[2].charAt(0) + hex[3].charAt(0);
    }

    return hex.join("");
}

// `rgbaToArgbHex`
// Converts an RGBA color to an ARGB Hex8 string
// Rarely used, but required for "toFilter()"
function rgbaToArgbHex(r, g, b, a) {

    var hex = [
        pad2(convertDecimalToHex(a)),
        pad2(mathRound(r).toString(16)),
        pad2(mathRound(g).toString(16)),
        pad2(mathRound(b).toString(16))
    ];

    return hex.join("");
}

// `equals`
// Can be called with any tinycolor input
tinycolor.equals = function (color1, color2) {
    if (!color1 || !color2) { return false; }
    return tinycolor(color1).toRgbString() == tinycolor(color2).toRgbString();
};

tinycolor.random = function() {
    return tinycolor.fromRatio({
        r: mathRandom(),
        g: mathRandom(),
        b: mathRandom()
    });
};


// Modification Functions
// ----------------------
// Thanks to less.js for some of the basics here
// <https://github.com/cloudhead/less.js/blob/master/lib/less/functions.js>

function desaturate(color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var hsl = tinycolor(color).toHsl();
    hsl.s -= amount / 100;
    hsl.s = clamp01(hsl.s);
    return tinycolor(hsl);
}

function saturate(color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var hsl = tinycolor(color).toHsl();
    hsl.s += amount / 100;
    hsl.s = clamp01(hsl.s);
    return tinycolor(hsl);
}

function greyscale(color) {
    return tinycolor(color).desaturate(100);
}

function lighten (color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var hsl = tinycolor(color).toHsl();
    hsl.l += amount / 100;
    hsl.l = clamp01(hsl.l);
    return tinycolor(hsl);
}

function brighten(color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var rgb = tinycolor(color).toRgb();
    rgb.r = mathMax(0, mathMin(255, rgb.r - mathRound(255 * - (amount / 100))));
    rgb.g = mathMax(0, mathMin(255, rgb.g - mathRound(255 * - (amount / 100))));
    rgb.b = mathMax(0, mathMin(255, rgb.b - mathRound(255 * - (amount / 100))));
    return tinycolor(rgb);
}

function darken (color, amount) {
    amount = (amount === 0) ? 0 : (amount || 10);
    var hsl = tinycolor(color).toHsl();
    hsl.l -= amount / 100;
    hsl.l = clamp01(hsl.l);
    return tinycolor(hsl);
}

// Spin takes a positive or negative amount within [-360, 360] indicating the change of hue.
// Values outside of this range will be wrapped into this range.
function spin(color, amount) {
    var hsl = tinycolor(color).toHsl();
    var hue = (hsl.h + amount) % 360;
    hsl.h = hue < 0 ? 360 + hue : hue;
    return tinycolor(hsl);
}

// Combination Functions
// ---------------------
// Thanks to jQuery xColor for some of the ideas behind these
// <https://github.com/infusion/jQuery-xcolor/blob/master/jquery.xcolor.js>

function complement(color) {
    var hsl = tinycolor(color).toHsl();
    hsl.h = (hsl.h + 180) % 360;
    return tinycolor(hsl);
}

function triad(color) {
    var hsl = tinycolor(color).toHsl();
    var h = hsl.h;
    return [
        tinycolor(color),
        tinycolor({ h: (h + 120) % 360, s: hsl.s, l: hsl.l }),
        tinycolor({ h: (h + 240) % 360, s: hsl.s, l: hsl.l })
    ];
}

function tetrad(color) {
    var hsl = tinycolor(color).toHsl();
    var h = hsl.h;
    return [
        tinycolor(color),
        tinycolor({ h: (h + 90) % 360, s: hsl.s, l: hsl.l }),
        tinycolor({ h: (h + 180) % 360, s: hsl.s, l: hsl.l }),
        tinycolor({ h: (h + 270) % 360, s: hsl.s, l: hsl.l })
    ];
}

function splitcomplement(color) {
    var hsl = tinycolor(color).toHsl();
    var h = hsl.h;
    return [
        tinycolor(color),
        tinycolor({ h: (h + 72) % 360, s: hsl.s, l: hsl.l}),
        tinycolor({ h: (h + 216) % 360, s: hsl.s, l: hsl.l})
    ];
}

function analogous(color, results, slices) {
    results = results || 6;
    slices = slices || 30;

    var hsl = tinycolor(color).toHsl();
    var part = 360 / slices;
    var ret = [tinycolor(color)];

    for (hsl.h = ((hsl.h - (part * results >> 1)) + 720) % 360; --results; ) {
        hsl.h = (hsl.h + part) % 360;
        ret.push(tinycolor(hsl));
    }
    return ret;
}

function monochromatic(color, results) {
    results = results || 6;
    var hsv = tinycolor(color).toHsv();
    var h = hsv.h, s = hsv.s, v = hsv.v;
    var ret = [];
    var modification = 1 / results;

    while (results--) {
        ret.push(tinycolor({ h: h, s: s, v: v}));
        v = (v + modification) % 1;
    }

    return ret;
}

// Utility Functions
// ---------------------

tinycolor.mix = function(color1, color2, amount) {
    amount = (amount === 0) ? 0 : (amount || 50);

    var rgb1 = tinycolor(color1).toRgb();
    var rgb2 = tinycolor(color2).toRgb();

    var p = amount / 100;

    var rgba = {
        r: ((rgb2.r - rgb1.r) * p) + rgb1.r,
        g: ((rgb2.g - rgb1.g) * p) + rgb1.g,
        b: ((rgb2.b - rgb1.b) * p) + rgb1.b,
        a: ((rgb2.a - rgb1.a) * p) + rgb1.a
    };

    return tinycolor(rgba);
};


// Readability Functions
// ---------------------
// <http://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef (WCAG Version 2)

// `contrast`
// Analyze the 2 colors and returns the color contrast defined by (WCAG Version 2)
tinycolor.readability = function(color1, color2) {
    var c1 = tinycolor(color1);
    var c2 = tinycolor(color2);
    return (Math.max(c1.getLuminance(),c2.getLuminance())+0.05) / (Math.min(c1.getLuminance(),c2.getLuminance())+0.05);
};

// `isReadable`
// Ensure that foreground and background color combinations meet WCAG2 guidelines.
// The third argument is an optional Object.
//      the 'level' property states 'AA' or 'AAA' - if missing or invalid, it defaults to 'AA';
//      the 'size' property states 'large' or 'small' - if missing or invalid, it defaults to 'small'.
// If the entire object is absent, isReadable defaults to {level:"AA",size:"small"}.

// *Example*
//    tinycolor.isReadable("#000", "#111") => false
//    tinycolor.isReadable("#000", "#111",{level:"AA",size:"large"}) => false
tinycolor.isReadable = function(color1, color2, wcag2) {
    var readability = tinycolor.readability(color1, color2);
    var wcag2Parms, out;

    out = false;

    wcag2Parms = validateWCAG2Parms(wcag2);
    switch (wcag2Parms.level + wcag2Parms.size) {
        case "AAsmall":
        case "AAAlarge":
            out = readability >= 4.5;
            break;
        case "AAlarge":
            out = readability >= 3;
            break;
        case "AAAsmall":
            out = readability >= 7;
            break;
    }
    return out;

};

// `mostReadable`
// Given a base color and a list of possible foreground or background
// colors for that base, returns the most readable color.
// Optionally returns Black or White if the most readable color is unreadable.
// *Example*
//    tinycolor.mostReadable(tinycolor.mostReadable("#123", ["#124", "#125"],{includeFallbackColors:false}).toHexString(); // "#112255"
//    tinycolor.mostReadable(tinycolor.mostReadable("#123", ["#124", "#125"],{includeFallbackColors:true}).toHexString();  // "#ffffff"
//    tinycolor.mostReadable("#a8015a", ["#faf3f3"],{includeFallbackColors:true,level:"AAA",size:"large"}).toHexString(); // "#faf3f3"
//    tinycolor.mostReadable("#a8015a", ["#faf3f3"],{includeFallbackColors:true,level:"AAA",size:"small"}).toHexString(); // "#ffffff"
tinycolor.mostReadable = function(baseColor, colorList, args) {
    var bestColor = null;
    var bestScore = 0;
    var readability;
    var includeFallbackColors, level, size ;
    args = args || {};
    includeFallbackColors = args.includeFallbackColors ;
    level = args.level;
    size = args.size;

    for (var i= 0; i < colorList.length ; i++) {
        readability = tinycolor.readability(baseColor, colorList[i]);
        if (readability > bestScore) {
            bestScore = readability;
            bestColor = tinycolor(colorList[i]);
        }
    }

    if (tinycolor.isReadable(baseColor, bestColor, {"level":level,"size":size}) || !includeFallbackColors) {
        return bestColor;
    }
    else {
        args.includeFallbackColors=false;
        return tinycolor.mostReadable(baseColor,["#fff", "#000"],args);
    }
};


// Big List of Colors
// ------------------
// <http://www.w3.org/TR/css3-color/#svg-color>
var names = tinycolor.names = {
    aliceblue: "f0f8ff",
    antiquewhite: "faebd7",
    aqua: "0ff",
    aquamarine: "7fffd4",
    azure: "f0ffff",
    beige: "f5f5dc",
    bisque: "ffe4c4",
    black: "000",
    blanchedalmond: "ffebcd",
    blue: "00f",
    blueviolet: "8a2be2",
    brown: "a52a2a",
    burlywood: "deb887",
    burntsienna: "ea7e5d",
    cadetblue: "5f9ea0",
    chartreuse: "7fff00",
    chocolate: "d2691e",
    coral: "ff7f50",
    cornflowerblue: "6495ed",
    cornsilk: "fff8dc",
    crimson: "dc143c",
    cyan: "0ff",
    darkblue: "00008b",
    darkcyan: "008b8b",
    darkgoldenrod: "b8860b",
    darkgray: "a9a9a9",
    darkgreen: "006400",
    darkgrey: "a9a9a9",
    darkkhaki: "bdb76b",
    darkmagenta: "8b008b",
    darkolivegreen: "556b2f",
    darkorange: "ff8c00",
    darkorchid: "9932cc",
    darkred: "8b0000",
    darksalmon: "e9967a",
    darkseagreen: "8fbc8f",
    darkslateblue: "483d8b",
    darkslategray: "2f4f4f",
    darkslategrey: "2f4f4f",
    darkturquoise: "00ced1",
    darkviolet: "9400d3",
    deeppink: "ff1493",
    deepskyblue: "00bfff",
    dimgray: "696969",
    dimgrey: "696969",
    dodgerblue: "1e90ff",
    firebrick: "b22222",
    floralwhite: "fffaf0",
    forestgreen: "228b22",
    fuchsia: "f0f",
    gainsboro: "dcdcdc",
    ghostwhite: "f8f8ff",
    gold: "ffd700",
    goldenrod: "daa520",
    gray: "808080",
    green: "008000",
    greenyellow: "adff2f",
    grey: "808080",
    honeydew: "f0fff0",
    hotpink: "ff69b4",
    indianred: "cd5c5c",
    indigo: "4b0082",
    ivory: "fffff0",
    khaki: "f0e68c",
    lavender: "e6e6fa",
    lavenderblush: "fff0f5",
    lawngreen: "7cfc00",
    lemonchiffon: "fffacd",
    lightblue: "add8e6",
    lightcoral: "f08080",
    lightcyan: "e0ffff",
    lightgoldenrodyellow: "fafad2",
    lightgray: "d3d3d3",
    lightgreen: "90ee90",
    lightgrey: "d3d3d3",
    lightpink: "ffb6c1",
    lightsalmon: "ffa07a",
    lightseagreen: "20b2aa",
    lightskyblue: "87cefa",
    lightslategray: "789",
    lightslategrey: "789",
    lightsteelblue: "b0c4de",
    lightyellow: "ffffe0",
    lime: "0f0",
    limegreen: "32cd32",
    linen: "faf0e6",
    magenta: "f0f",
    maroon: "800000",
    mediumaquamarine: "66cdaa",
    mediumblue: "0000cd",
    mediumorchid: "ba55d3",
    mediumpurple: "9370db",
    mediumseagreen: "3cb371",
    mediumslateblue: "7b68ee",
    mediumspringgreen: "00fa9a",
    mediumturquoise: "48d1cc",
    mediumvioletred: "c71585",
    midnightblue: "191970",
    mintcream: "f5fffa",
    mistyrose: "ffe4e1",
    moccasin: "ffe4b5",
    navajowhite: "ffdead",
    navy: "000080",
    oldlace: "fdf5e6",
    olive: "808000",
    olivedrab: "6b8e23",
    orange: "ffa500",
    orangered: "ff4500",
    orchid: "da70d6",
    palegoldenrod: "eee8aa",
    palegreen: "98fb98",
    paleturquoise: "afeeee",
    palevioletred: "db7093",
    papayawhip: "ffefd5",
    peachpuff: "ffdab9",
    peru: "cd853f",
    pink: "ffc0cb",
    plum: "dda0dd",
    powderblue: "b0e0e6",
    purple: "800080",
    rebeccapurple: "663399",
    red: "f00",
    rosybrown: "bc8f8f",
    royalblue: "4169e1",
    saddlebrown: "8b4513",
    salmon: "fa8072",
    sandybrown: "f4a460",
    seagreen: "2e8b57",
    seashell: "fff5ee",
    sienna: "a0522d",
    silver: "c0c0c0",
    skyblue: "87ceeb",
    slateblue: "6a5acd",
    slategray: "708090",
    slategrey: "708090",
    snow: "fffafa",
    springgreen: "00ff7f",
    steelblue: "4682b4",
    tan: "d2b48c",
    teal: "008080",
    thistle: "d8bfd8",
    tomato: "ff6347",
    turquoise: "40e0d0",
    violet: "ee82ee",
    wheat: "f5deb3",
    white: "fff",
    whitesmoke: "f5f5f5",
    yellow: "ff0",
    yellowgreen: "9acd32"
};

// Make it easy to access colors via `hexNames[hex]`
var hexNames = tinycolor.hexNames = flip(names);


// Utilities
// ---------

// `{ 'name1': 'val1' }` becomes `{ 'val1': 'name1' }`
function flip(o) {
    var flipped = { };
    for (var i in o) {
        if (o.hasOwnProperty(i)) {
            flipped[o[i]] = i;
        }
    }
    return flipped;
}

// Return a valid alpha value [0,1] with all invalid values being set to 1
function boundAlpha(a) {
    a = parseFloat(a);

    if (isNaN(a) || a < 0 || a > 1) {
        a = 1;
    }

    return a;
}

// Take input from [0, n] and return it as [0, 1]
function bound01(n, max) {
    if (isOnePointZero(n)) { n = "100%"; }

    var processPercent = isPercentage(n);
    n = mathMin(max, mathMax(0, parseFloat(n)));

    // Automatically convert percentage into number
    if (processPercent) {
        n = parseInt(n * max, 10) / 100;
    }

    // Handle floating point rounding errors
    if ((Math.abs(n - max) < 0.000001)) {
        return 1;
    }

    // Convert into [0, 1] range if it isn't already
    return (n % max) / parseFloat(max);
}

// Force a number between 0 and 1
function clamp01(val) {
    return mathMin(1, mathMax(0, val));
}

// Parse a base-16 hex value into a base-10 integer
function parseIntFromHex(val) {
    return parseInt(val, 16);
}

// Need to handle 1.0 as 100%, since once it is a number, there is no difference between it and 1
// <http://stackoverflow.com/questions/7422072/javascript-how-to-detect-number-as-a-decimal-including-1-0>
function isOnePointZero(n) {
    return typeof n == "string" && n.indexOf('.') != -1 && parseFloat(n) === 1;
}

// Check to see if string passed in is a percentage
function isPercentage(n) {
    return typeof n === "string" && n.indexOf('%') != -1;
}

// Force a hex value to have 2 characters
function pad2(c) {
    return c.length == 1 ? '0' + c : '' + c;
}

// Replace a decimal with it's percentage value
function convertToPercentage(n) {
    if (n <= 1) {
        n = (n * 100) + "%";
    }

    return n;
}

// Converts a decimal to a hex value
function convertDecimalToHex(d) {
    return Math.round(parseFloat(d) * 255).toString(16);
}
// Converts a hex value to a decimal
function convertHexToDecimal(h) {
    return (parseIntFromHex(h) / 255);
}

var matchers = (function() {

    // <http://www.w3.org/TR/css3-values/#integers>
    var CSS_INTEGER = "[-\\+]?\\d+%?";

    // <http://www.w3.org/TR/css3-values/#number-value>
    var CSS_NUMBER = "[-\\+]?\\d*\\.\\d+%?";

    // Allow positive/negative integer/number.  Don't capture the either/or, just the entire outcome.
    var CSS_UNIT = "(?:" + CSS_NUMBER + ")|(?:" + CSS_INTEGER + ")";

    // Actual matching.
    // Parentheses and commas are optional, but not required.
    // Whitespace can take the place of commas or opening paren
    var PERMISSIVE_MATCH3 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";
    var PERMISSIVE_MATCH4 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";

    return {
        CSS_UNIT: new RegExp(CSS_UNIT),
        rgb: new RegExp("rgb" + PERMISSIVE_MATCH3),
        rgba: new RegExp("rgba" + PERMISSIVE_MATCH4),
        hsl: new RegExp("hsl" + PERMISSIVE_MATCH3),
        hsla: new RegExp("hsla" + PERMISSIVE_MATCH4),
        hsv: new RegExp("hsv" + PERMISSIVE_MATCH3),
        hsva: new RegExp("hsva" + PERMISSIVE_MATCH4),
        hex3: /^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
        hex6: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,
        hex4: /^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
        hex8: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/
    };
})();

// `isValidCSSUnit`
// Take in a single string / number and check to see if it looks like a CSS unit
// (see `matchers` above for definition).
function isValidCSSUnit(color) {
    return !!matchers.CSS_UNIT.exec(color);
}

// `stringInputToObject`
// Permissive string parsing.  Take in a number of formats, and output an object
// based on detected format.  Returns `{ r, g, b }` or `{ h, s, l }` or `{ h, s, v}`
function stringInputToObject(color) {

    color = color.replace(trimLeft,'').replace(trimRight, '').toLowerCase();
    var named = false;
    if (names[color]) {
        color = names[color];
        named = true;
    }
    else if (color == 'transparent') {
        return { r: 0, g: 0, b: 0, a: 0, format: "name" };
    }

    // Try to match string input using regular expressions.
    // Keep most of the number bounding out of this function - don't worry about [0,1] or [0,100] or [0,360]
    // Just return an object and let the conversion functions handle that.
    // This way the result will be the same whether the tinycolor is initialized with string or object.
    var match;
    if ((match = matchers.rgb.exec(color))) {
        return { r: match[1], g: match[2], b: match[3] };
    }
    if ((match = matchers.rgba.exec(color))) {
        return { r: match[1], g: match[2], b: match[3], a: match[4] };
    }
    if ((match = matchers.hsl.exec(color))) {
        return { h: match[1], s: match[2], l: match[3] };
    }
    if ((match = matchers.hsla.exec(color))) {
        return { h: match[1], s: match[2], l: match[3], a: match[4] };
    }
    if ((match = matchers.hsv.exec(color))) {
        return { h: match[1], s: match[2], v: match[3] };
    }
    if ((match = matchers.hsva.exec(color))) {
        return { h: match[1], s: match[2], v: match[3], a: match[4] };
    }
    if ((match = matchers.hex8.exec(color))) {
        return {
            r: parseIntFromHex(match[1]),
            g: parseIntFromHex(match[2]),
            b: parseIntFromHex(match[3]),
            a: convertHexToDecimal(match[4]),
            format: named ? "name" : "hex8"
        };
    }
    if ((match = matchers.hex6.exec(color))) {
        return {
            r: parseIntFromHex(match[1]),
            g: parseIntFromHex(match[2]),
            b: parseIntFromHex(match[3]),
            format: named ? "name" : "hex"
        };
    }
    if ((match = matchers.hex4.exec(color))) {
        return {
            r: parseIntFromHex(match[1] + '' + match[1]),
            g: parseIntFromHex(match[2] + '' + match[2]),
            b: parseIntFromHex(match[3] + '' + match[3]),
            a: convertHexToDecimal(match[4] + '' + match[4]),
            format: named ? "name" : "hex8"
        };
    }
    if ((match = matchers.hex3.exec(color))) {
        return {
            r: parseIntFromHex(match[1] + '' + match[1]),
            g: parseIntFromHex(match[2] + '' + match[2]),
            b: parseIntFromHex(match[3] + '' + match[3]),
            format: named ? "name" : "hex"
        };
    }

    return false;
}

function validateWCAG2Parms(parms) {
    // return valid WCAG2 parms for isReadable.
    // If input parms are invalid, return {"level":"AA", "size":"small"}
    var level, size;
    parms = parms || {"level":"AA", "size":"small"};
    level = (parms.level || "AA").toUpperCase();
    size = (parms.size || "small").toLowerCase();
    if (level !== "AA" && level !== "AAA") {
        level = "AA";
    }
    if (size !== "small" && size !== "large") {
        size = "small";
    }
    return {"level":level, "size":size};
}

// Node: Export function
if ( true && module.exports) {
    module.exports = tinycolor;
}
// AMD/requirejs: Define the module
else if (true) {
    !(__WEBPACK_AMD_DEFINE_RESULT__ = (function () {return tinycolor;}).call(exports, __webpack_require__, exports, module),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
}
// Browser: Expose to window
else {}

})(Math);


/***/ }),

/***/ 5:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _assertThisInitialized; });
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

/***/ }),

/***/ 53:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["date"]; }());

/***/ }),

/***/ 58:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["serverSideRender"]; }());

/***/ }),

/***/ 6:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockEditor"]; }());

/***/ }),

/***/ 65:
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ 69:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["autop"]; }());

/***/ }),

/***/ 7:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(10);

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      Object(_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(target, key, source[key]);
    });
  }

  return target;
}

/***/ }),

/***/ 70:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2017 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var classNames = (function () {
		// don't inherit from Object so we can skip hasOwnProperty check later
		// http://stackoverflow.com/questions/15518328/creating-js-object-with-object-createnull#answer-21079232
		function StorageObject() {}
		StorageObject.prototype = Object.create(null);

		function _parseArray (resultSet, array) {
			var length = array.length;

			for (var i = 0; i < length; ++i) {
				_parse(resultSet, array[i]);
			}
		}

		var hasOwn = {}.hasOwnProperty;

		function _parseNumber (resultSet, num) {
			resultSet[num] = true;
		}

		function _parseObject (resultSet, object) {
			for (var k in object) {
				if (hasOwn.call(object, k)) {
					// set value to false instead of deleting it to avoid changing object structure
					// https://www.smashingmagazine.com/2012/11/writing-fast-memory-efficient-javascript/#de-referencing-misconceptions
					resultSet[k] = !!object[k];
				}
			}
		}

		var SPACE = /\s+/;
		function _parseString (resultSet, str) {
			var array = str.split(SPACE);
			var length = array.length;

			for (var i = 0; i < length; ++i) {
				resultSet[array[i]] = true;
			}
		}

		function _parse (resultSet, arg) {
			if (!arg) return;
			var argType = typeof arg;

			// 'foo bar'
			if (argType === 'string') {
				_parseString(resultSet, arg);

			// ['foo', 'bar', ...]
			} else if (Array.isArray(arg)) {
				_parseArray(resultSet, arg);

			// { 'foo': true, ... }
			} else if (argType === 'object') {
				_parseObject(resultSet, arg);

			// '130'
			} else if (argType === 'number') {
				_parseNumber(resultSet, arg);
			}
		}

		function _classNames () {
			// don't leak arguments
			// https://github.com/petkaantonov/bluebird/wiki/Optimization-killers#32-leaking-arguments
			var len = arguments.length;
			var args = Array(len);
			for (var i = 0; i < len; i++) {
				args[i] = arguments[i];
			}

			var classSet = new StorageObject();
			_parseArray(classSet, args);

			var list = [];

			for (var k in classSet) {
				if (classSet[k]) {
					list.push(k)
				}
			}

			return list.join(' ');
		}

		return _classNames;
	})();

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ 8:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ 82:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



var punycode = __webpack_require__(123);
var util = __webpack_require__(125);

exports.parse = urlParse;
exports.resolve = urlResolve;
exports.resolveObject = urlResolveObject;
exports.format = urlFormat;

exports.Url = Url;

function Url() {
  this.protocol = null;
  this.slashes = null;
  this.auth = null;
  this.host = null;
  this.port = null;
  this.hostname = null;
  this.hash = null;
  this.search = null;
  this.query = null;
  this.pathname = null;
  this.path = null;
  this.href = null;
}

// Reference: RFC 3986, RFC 1808, RFC 2396

// define these here so at least they only have to be
// compiled once on the first module load.
var protocolPattern = /^([a-z0-9.+-]+:)/i,
    portPattern = /:[0-9]*$/,

    // Special case for a simple path URL
    simplePathPattern = /^(\/\/?(?!\/)[^\?\s]*)(\?[^\s]*)?$/,

    // RFC 2396: characters reserved for delimiting URLs.
    // We actually just auto-escape these.
    delims = ['<', '>', '"', '`', ' ', '\r', '\n', '\t'],

    // RFC 2396: characters not allowed for various reasons.
    unwise = ['{', '}', '|', '\\', '^', '`'].concat(delims),

    // Allowed by RFCs, but cause of XSS attacks.  Always escape these.
    autoEscape = ['\''].concat(unwise),
    // Characters that are never ever allowed in a hostname.
    // Note that any invalid chars are also handled, but these
    // are the ones that are *expected* to be seen, so we fast-path
    // them.
    nonHostChars = ['%', '/', '?', ';', '#'].concat(autoEscape),
    hostEndingChars = ['/', '?', '#'],
    hostnameMaxLen = 255,
    hostnamePartPattern = /^[+a-z0-9A-Z_-]{0,63}$/,
    hostnamePartStart = /^([+a-z0-9A-Z_-]{0,63})(.*)$/,
    // protocols that can allow "unsafe" and "unwise" chars.
    unsafeProtocol = {
      'javascript': true,
      'javascript:': true
    },
    // protocols that never have a hostname.
    hostlessProtocol = {
      'javascript': true,
      'javascript:': true
    },
    // protocols that always contain a // bit.
    slashedProtocol = {
      'http': true,
      'https': true,
      'ftp': true,
      'gopher': true,
      'file': true,
      'http:': true,
      'https:': true,
      'ftp:': true,
      'gopher:': true,
      'file:': true
    },
    querystring = __webpack_require__(126);

function urlParse(url, parseQueryString, slashesDenoteHost) {
  if (url && util.isObject(url) && url instanceof Url) return url;

  var u = new Url;
  u.parse(url, parseQueryString, slashesDenoteHost);
  return u;
}

Url.prototype.parse = function(url, parseQueryString, slashesDenoteHost) {
  if (!util.isString(url)) {
    throw new TypeError("Parameter 'url' must be a string, not " + typeof url);
  }

  // Copy chrome, IE, opera backslash-handling behavior.
  // Back slashes before the query string get converted to forward slashes
  // See: https://code.google.com/p/chromium/issues/detail?id=25916
  var queryIndex = url.indexOf('?'),
      splitter =
          (queryIndex !== -1 && queryIndex < url.indexOf('#')) ? '?' : '#',
      uSplit = url.split(splitter),
      slashRegex = /\\/g;
  uSplit[0] = uSplit[0].replace(slashRegex, '/');
  url = uSplit.join(splitter);

  var rest = url;

  // trim before proceeding.
  // This is to support parse stuff like "  http://foo.com  \n"
  rest = rest.trim();

  if (!slashesDenoteHost && url.split('#').length === 1) {
    // Try fast path regexp
    var simplePath = simplePathPattern.exec(rest);
    if (simplePath) {
      this.path = rest;
      this.href = rest;
      this.pathname = simplePath[1];
      if (simplePath[2]) {
        this.search = simplePath[2];
        if (parseQueryString) {
          this.query = querystring.parse(this.search.substr(1));
        } else {
          this.query = this.search.substr(1);
        }
      } else if (parseQueryString) {
        this.search = '';
        this.query = {};
      }
      return this;
    }
  }

  var proto = protocolPattern.exec(rest);
  if (proto) {
    proto = proto[0];
    var lowerProto = proto.toLowerCase();
    this.protocol = lowerProto;
    rest = rest.substr(proto.length);
  }

  // figure out if it's got a host
  // user@server is *always* interpreted as a hostname, and url
  // resolution will treat //foo/bar as host=foo,path=bar because that's
  // how the browser resolves relative URLs.
  if (slashesDenoteHost || proto || rest.match(/^\/\/[^@\/]+@[^@\/]+/)) {
    var slashes = rest.substr(0, 2) === '//';
    if (slashes && !(proto && hostlessProtocol[proto])) {
      rest = rest.substr(2);
      this.slashes = true;
    }
  }

  if (!hostlessProtocol[proto] &&
      (slashes || (proto && !slashedProtocol[proto]))) {

    // there's a hostname.
    // the first instance of /, ?, ;, or # ends the host.
    //
    // If there is an @ in the hostname, then non-host chars *are* allowed
    // to the left of the last @ sign, unless some host-ending character
    // comes *before* the @-sign.
    // URLs are obnoxious.
    //
    // ex:
    // http://a@b@c/ => user:a@b host:c
    // http://a@b?@c => user:a host:c path:/?@c

    // v0.12 TODO(isaacs): This is not quite how Chrome does things.
    // Review our test case against browsers more comprehensively.

    // find the first instance of any hostEndingChars
    var hostEnd = -1;
    for (var i = 0; i < hostEndingChars.length; i++) {
      var hec = rest.indexOf(hostEndingChars[i]);
      if (hec !== -1 && (hostEnd === -1 || hec < hostEnd))
        hostEnd = hec;
    }

    // at this point, either we have an explicit point where the
    // auth portion cannot go past, or the last @ char is the decider.
    var auth, atSign;
    if (hostEnd === -1) {
      // atSign can be anywhere.
      atSign = rest.lastIndexOf('@');
    } else {
      // atSign must be in auth portion.
      // http://a@b/c@d => host:b auth:a path:/c@d
      atSign = rest.lastIndexOf('@', hostEnd);
    }

    // Now we have a portion which is definitely the auth.
    // Pull that off.
    if (atSign !== -1) {
      auth = rest.slice(0, atSign);
      rest = rest.slice(atSign + 1);
      this.auth = decodeURIComponent(auth);
    }

    // the host is the remaining to the left of the first non-host char
    hostEnd = -1;
    for (var i = 0; i < nonHostChars.length; i++) {
      var hec = rest.indexOf(nonHostChars[i]);
      if (hec !== -1 && (hostEnd === -1 || hec < hostEnd))
        hostEnd = hec;
    }
    // if we still have not hit it, then the entire thing is a host.
    if (hostEnd === -1)
      hostEnd = rest.length;

    this.host = rest.slice(0, hostEnd);
    rest = rest.slice(hostEnd);

    // pull out port.
    this.parseHost();

    // we've indicated that there is a hostname,
    // so even if it's empty, it has to be present.
    this.hostname = this.hostname || '';

    // if hostname begins with [ and ends with ]
    // assume that it's an IPv6 address.
    var ipv6Hostname = this.hostname[0] === '[' &&
        this.hostname[this.hostname.length - 1] === ']';

    // validate a little.
    if (!ipv6Hostname) {
      var hostparts = this.hostname.split(/\./);
      for (var i = 0, l = hostparts.length; i < l; i++) {
        var part = hostparts[i];
        if (!part) continue;
        if (!part.match(hostnamePartPattern)) {
          var newpart = '';
          for (var j = 0, k = part.length; j < k; j++) {
            if (part.charCodeAt(j) > 127) {
              // we replace non-ASCII char with a temporary placeholder
              // we need this to make sure size of hostname is not
              // broken by replacing non-ASCII by nothing
              newpart += 'x';
            } else {
              newpart += part[j];
            }
          }
          // we test again with ASCII char only
          if (!newpart.match(hostnamePartPattern)) {
            var validParts = hostparts.slice(0, i);
            var notHost = hostparts.slice(i + 1);
            var bit = part.match(hostnamePartStart);
            if (bit) {
              validParts.push(bit[1]);
              notHost.unshift(bit[2]);
            }
            if (notHost.length) {
              rest = '/' + notHost.join('.') + rest;
            }
            this.hostname = validParts.join('.');
            break;
          }
        }
      }
    }

    if (this.hostname.length > hostnameMaxLen) {
      this.hostname = '';
    } else {
      // hostnames are always lower case.
      this.hostname = this.hostname.toLowerCase();
    }

    if (!ipv6Hostname) {
      // IDNA Support: Returns a punycoded representation of "domain".
      // It only converts parts of the domain name that
      // have non-ASCII characters, i.e. it doesn't matter if
      // you call it with a domain that already is ASCII-only.
      this.hostname = punycode.toASCII(this.hostname);
    }

    var p = this.port ? ':' + this.port : '';
    var h = this.hostname || '';
    this.host = h + p;
    this.href += this.host;

    // strip [ and ] from the hostname
    // the host field still retains them, though
    if (ipv6Hostname) {
      this.hostname = this.hostname.substr(1, this.hostname.length - 2);
      if (rest[0] !== '/') {
        rest = '/' + rest;
      }
    }
  }

  // now rest is set to the post-host stuff.
  // chop off any delim chars.
  if (!unsafeProtocol[lowerProto]) {

    // First, make 100% sure that any "autoEscape" chars get
    // escaped, even if encodeURIComponent doesn't think they
    // need to be.
    for (var i = 0, l = autoEscape.length; i < l; i++) {
      var ae = autoEscape[i];
      if (rest.indexOf(ae) === -1)
        continue;
      var esc = encodeURIComponent(ae);
      if (esc === ae) {
        esc = escape(ae);
      }
      rest = rest.split(ae).join(esc);
    }
  }


  // chop off from the tail first.
  var hash = rest.indexOf('#');
  if (hash !== -1) {
    // got a fragment string.
    this.hash = rest.substr(hash);
    rest = rest.slice(0, hash);
  }
  var qm = rest.indexOf('?');
  if (qm !== -1) {
    this.search = rest.substr(qm);
    this.query = rest.substr(qm + 1);
    if (parseQueryString) {
      this.query = querystring.parse(this.query);
    }
    rest = rest.slice(0, qm);
  } else if (parseQueryString) {
    // no query string, but parseQueryString still requested
    this.search = '';
    this.query = {};
  }
  if (rest) this.pathname = rest;
  if (slashedProtocol[lowerProto] &&
      this.hostname && !this.pathname) {
    this.pathname = '/';
  }

  //to support http.request
  if (this.pathname || this.search) {
    var p = this.pathname || '';
    var s = this.search || '';
    this.path = p + s;
  }

  // finally, reconstruct the href based on what has been validated.
  this.href = this.format();
  return this;
};

// format a parsed object into a url string
function urlFormat(obj) {
  // ensure it's an object, and not a string url.
  // If it's an obj, this is a no-op.
  // this way, you can call url_format() on strings
  // to clean up potentially wonky urls.
  if (util.isString(obj)) obj = urlParse(obj);
  if (!(obj instanceof Url)) return Url.prototype.format.call(obj);
  return obj.format();
}

Url.prototype.format = function() {
  var auth = this.auth || '';
  if (auth) {
    auth = encodeURIComponent(auth);
    auth = auth.replace(/%3A/i, ':');
    auth += '@';
  }

  var protocol = this.protocol || '',
      pathname = this.pathname || '',
      hash = this.hash || '',
      host = false,
      query = '';

  if (this.host) {
    host = auth + this.host;
  } else if (this.hostname) {
    host = auth + (this.hostname.indexOf(':') === -1 ?
        this.hostname :
        '[' + this.hostname + ']');
    if (this.port) {
      host += ':' + this.port;
    }
  }

  if (this.query &&
      util.isObject(this.query) &&
      Object.keys(this.query).length) {
    query = querystring.stringify(this.query);
  }

  var search = this.search || (query && ('?' + query)) || '';

  if (protocol && protocol.substr(-1) !== ':') protocol += ':';

  // only the slashedProtocols get the //.  Not mailto:, xmpp:, etc.
  // unless they had them to begin with.
  if (this.slashes ||
      (!protocol || slashedProtocol[protocol]) && host !== false) {
    host = '//' + (host || '');
    if (pathname && pathname.charAt(0) !== '/') pathname = '/' + pathname;
  } else if (!host) {
    host = '';
  }

  if (hash && hash.charAt(0) !== '#') hash = '#' + hash;
  if (search && search.charAt(0) !== '?') search = '?' + search;

  pathname = pathname.replace(/[?#]/g, function(match) {
    return encodeURIComponent(match);
  });
  search = search.replace('#', '%23');

  return protocol + host + pathname + search + hash;
};

function urlResolve(source, relative) {
  return urlParse(source, false, true).resolve(relative);
}

Url.prototype.resolve = function(relative) {
  return this.resolveObject(urlParse(relative, false, true)).format();
};

function urlResolveObject(source, relative) {
  if (!source) return relative;
  return urlParse(source, false, true).resolveObject(relative);
}

Url.prototype.resolveObject = function(relative) {
  if (util.isString(relative)) {
    var rel = new Url();
    rel.parse(relative, false, true);
    relative = rel;
  }

  var result = new Url();
  var tkeys = Object.keys(this);
  for (var tk = 0; tk < tkeys.length; tk++) {
    var tkey = tkeys[tk];
    result[tkey] = this[tkey];
  }

  // hash is always overridden, no matter what.
  // even href="" will remove it.
  result.hash = relative.hash;

  // if the relative url is empty, then there's nothing left to do here.
  if (relative.href === '') {
    result.href = result.format();
    return result;
  }

  // hrefs like //foo/bar always cut to the protocol.
  if (relative.slashes && !relative.protocol) {
    // take everything except the protocol from relative
    var rkeys = Object.keys(relative);
    for (var rk = 0; rk < rkeys.length; rk++) {
      var rkey = rkeys[rk];
      if (rkey !== 'protocol')
        result[rkey] = relative[rkey];
    }

    //urlParse appends trailing / to urls like http://www.example.com
    if (slashedProtocol[result.protocol] &&
        result.hostname && !result.pathname) {
      result.path = result.pathname = '/';
    }

    result.href = result.format();
    return result;
  }

  if (relative.protocol && relative.protocol !== result.protocol) {
    // if it's a known url protocol, then changing
    // the protocol does weird things
    // first, if it's not file:, then we MUST have a host,
    // and if there was a path
    // to begin with, then we MUST have a path.
    // if it is file:, then the host is dropped,
    // because that's known to be hostless.
    // anything else is assumed to be absolute.
    if (!slashedProtocol[relative.protocol]) {
      var keys = Object.keys(relative);
      for (var v = 0; v < keys.length; v++) {
        var k = keys[v];
        result[k] = relative[k];
      }
      result.href = result.format();
      return result;
    }

    result.protocol = relative.protocol;
    if (!relative.host && !hostlessProtocol[relative.protocol]) {
      var relPath = (relative.pathname || '').split('/');
      while (relPath.length && !(relative.host = relPath.shift()));
      if (!relative.host) relative.host = '';
      if (!relative.hostname) relative.hostname = '';
      if (relPath[0] !== '') relPath.unshift('');
      if (relPath.length < 2) relPath.unshift('');
      result.pathname = relPath.join('/');
    } else {
      result.pathname = relative.pathname;
    }
    result.search = relative.search;
    result.query = relative.query;
    result.host = relative.host || '';
    result.auth = relative.auth;
    result.hostname = relative.hostname || relative.host;
    result.port = relative.port;
    // to support http.request
    if (result.pathname || result.search) {
      var p = result.pathname || '';
      var s = result.search || '';
      result.path = p + s;
    }
    result.slashes = result.slashes || relative.slashes;
    result.href = result.format();
    return result;
  }

  var isSourceAbs = (result.pathname && result.pathname.charAt(0) === '/'),
      isRelAbs = (
          relative.host ||
          relative.pathname && relative.pathname.charAt(0) === '/'
      ),
      mustEndAbs = (isRelAbs || isSourceAbs ||
                    (result.host && relative.pathname)),
      removeAllDots = mustEndAbs,
      srcPath = result.pathname && result.pathname.split('/') || [],
      relPath = relative.pathname && relative.pathname.split('/') || [],
      psychotic = result.protocol && !slashedProtocol[result.protocol];

  // if the url is a non-slashed url, then relative
  // links like ../.. should be able
  // to crawl up to the hostname, as well.  This is strange.
  // result.protocol has already been set by now.
  // Later on, put the first path part into the host field.
  if (psychotic) {
    result.hostname = '';
    result.port = null;
    if (result.host) {
      if (srcPath[0] === '') srcPath[0] = result.host;
      else srcPath.unshift(result.host);
    }
    result.host = '';
    if (relative.protocol) {
      relative.hostname = null;
      relative.port = null;
      if (relative.host) {
        if (relPath[0] === '') relPath[0] = relative.host;
        else relPath.unshift(relative.host);
      }
      relative.host = null;
    }
    mustEndAbs = mustEndAbs && (relPath[0] === '' || srcPath[0] === '');
  }

  if (isRelAbs) {
    // it's absolute.
    result.host = (relative.host || relative.host === '') ?
                  relative.host : result.host;
    result.hostname = (relative.hostname || relative.hostname === '') ?
                      relative.hostname : result.hostname;
    result.search = relative.search;
    result.query = relative.query;
    srcPath = relPath;
    // fall through to the dot-handling below.
  } else if (relPath.length) {
    // it's relative
    // throw away the existing file, and take the new path instead.
    if (!srcPath) srcPath = [];
    srcPath.pop();
    srcPath = srcPath.concat(relPath);
    result.search = relative.search;
    result.query = relative.query;
  } else if (!util.isNullOrUndefined(relative.search)) {
    // just pull out the search.
    // like href='?foo'.
    // Put this after the other two cases because it simplifies the booleans
    if (psychotic) {
      result.hostname = result.host = srcPath.shift();
      //occationaly the auth can get stuck only in host
      //this especially happens in cases like
      //url.resolveObject('mailto:local1@domain1', 'local2@domain2')
      var authInHost = result.host && result.host.indexOf('@') > 0 ?
                       result.host.split('@') : false;
      if (authInHost) {
        result.auth = authInHost.shift();
        result.host = result.hostname = authInHost.shift();
      }
    }
    result.search = relative.search;
    result.query = relative.query;
    //to support http.request
    if (!util.isNull(result.pathname) || !util.isNull(result.search)) {
      result.path = (result.pathname ? result.pathname : '') +
                    (result.search ? result.search : '');
    }
    result.href = result.format();
    return result;
  }

  if (!srcPath.length) {
    // no path at all.  easy.
    // we've already handled the other stuff above.
    result.pathname = null;
    //to support http.request
    if (result.search) {
      result.path = '/' + result.search;
    } else {
      result.path = null;
    }
    result.href = result.format();
    return result;
  }

  // if a url ENDs in . or .., then it must get a trailing slash.
  // however, if it ends in anything else non-slashy,
  // then it must NOT get a trailing slash.
  var last = srcPath.slice(-1)[0];
  var hasTrailingSlash = (
      (result.host || relative.host || srcPath.length > 1) &&
      (last === '.' || last === '..') || last === '');

  // strip single dots, resolve double dots to parent dir
  // if the path tries to go above the root, `up` ends up > 0
  var up = 0;
  for (var i = srcPath.length; i >= 0; i--) {
    last = srcPath[i];
    if (last === '.') {
      srcPath.splice(i, 1);
    } else if (last === '..') {
      srcPath.splice(i, 1);
      up++;
    } else if (up) {
      srcPath.splice(i, 1);
      up--;
    }
  }

  // if the path is allowed to go above the root, restore leading ..s
  if (!mustEndAbs && !removeAllDots) {
    for (; up--; up) {
      srcPath.unshift('..');
    }
  }

  if (mustEndAbs && srcPath[0] !== '' &&
      (!srcPath[0] || srcPath[0].charAt(0) !== '/')) {
    srcPath.unshift('');
  }

  if (hasTrailingSlash && (srcPath.join('/').substr(-1) !== '/')) {
    srcPath.push('');
  }

  var isAbsolute = srcPath[0] === '' ||
      (srcPath[0] && srcPath[0].charAt(0) === '/');

  // put the host back
  if (psychotic) {
    result.hostname = result.host = isAbsolute ? '' :
                                    srcPath.length ? srcPath.shift() : '';
    //occationaly the auth can get stuck only in host
    //this especially happens in cases like
    //url.resolveObject('mailto:local1@domain1', 'local2@domain2')
    var authInHost = result.host && result.host.indexOf('@') > 0 ?
                     result.host.split('@') : false;
    if (authInHost) {
      result.auth = authInHost.shift();
      result.host = result.hostname = authInHost.shift();
    }
  }

  mustEndAbs = mustEndAbs || (result.host && srcPath.length);

  if (mustEndAbs && !isAbsolute) {
    srcPath.unshift('');
  }

  if (!srcPath.length) {
    result.pathname = null;
    result.path = null;
  } else {
    result.pathname = srcPath.join('/');
  }

  //to support request.http
  if (!util.isNull(result.pathname) || !util.isNull(result.search)) {
    result.path = (result.pathname ? result.pathname : '') +
                  (result.search ? result.search : '');
  }
  result.auth = relative.auth || result.auth;
  result.slashes = result.slashes || relative.slashes;
  result.href = result.format();
  return result;
};

Url.prototype.parseHost = function() {
  var host = this.host;
  var port = portPattern.exec(host);
  if (port) {
    port = port[0];
    if (port !== ':') {
      this.port = port.substr(1);
    }
    host = host.substr(0, host.length - port.length);
  }
  if (host) this.hostname = host;
};


/***/ }),

/***/ 89:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["coreData"]; }());

/***/ }),

/***/ 9:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ })

/******/ });