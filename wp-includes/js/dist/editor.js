this["wp"] = this["wp"] || {}; this["wp"]["editor"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "PLxR");
/******/ })
/************************************************************************/
/******/ ({

/***/ "16Al":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = __webpack_require__("WbBG");

function emptyFunction() {}

module.exports = function() {
  function shim(props, propName, componentName, location, propFullName, secret) {
    if (secret === ReactPropTypesSecret) {
      // It is still safe when called from React.
      return;
    }
    var err = new Error(
      'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
      'Use PropTypes.checkPropTypes() to call them. ' +
      'Read more at http://fb.me/use-check-prop-types'
    );
    err.name = 'Invariant Violation';
    throw err;
  };
  shim.isRequired = shim;
  function getShim() {
    return shim;
  };
  // Important!
  // Keep this list in sync with production version in `./factoryWithTypeCheckers.js`.
  var ReactPropTypes = {
    array: shim,
    bool: shim,
    func: shim,
    number: shim,
    object: shim,
    string: shim,
    symbol: shim,

    any: shim,
    arrayOf: getShim,
    element: shim,
    instanceOf: getShim,
    node: shim,
    objectOf: getShim,
    oneOf: getShim,
    oneOfType: getShim,
    shape: getShim,
    exact: getShim
  };

  ReactPropTypes.checkPropTypes = emptyFunction;
  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ "17x9":
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (false) { var throwOnDirectAccess, isValidElement, REACT_ELEMENT_TYPE; } else {
  // By explicitly using `prop-types` you are opting into new production behavior.
  // http://fb.me/prop-types-in-prod
  module.exports = __webpack_require__("16Al")();
}


/***/ }),

/***/ "1OyB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ "25BE":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

/***/ }),

/***/ "4JlD":
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

/***/ "4eJC":
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

/***/ "7fqt":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["wordcount"]; }());

/***/ }),

/***/ "CNgt":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __assign = (this && this.__assign) || Object.assign || function(t) {
    for (var s, i = 1, n = arguments.length; i < n; i++) {
        s = arguments[i];
        for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
            t[p] = s[p];
    }
    return t;
};
var __rest = (this && this.__rest) || function (s, e) {
    var t = {};
    for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
        t[p] = s[p];
    if (s != null && typeof Object.getOwnPropertySymbols === "function")
        for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) if (e.indexOf(p[i]) < 0)
            t[p[i]] = s[p[i]];
    return t;
};
exports.__esModule = true;
var React = __webpack_require__("cDcd");
var PropTypes = __webpack_require__("17x9");
var autosize = __webpack_require__("GemG");
var _getLineHeight = __webpack_require__("Rk8H");
var getLineHeight = _getLineHeight;
var UPDATE = 'autosize:update';
var DESTROY = 'autosize:destroy';
var RESIZED = 'autosize:resized';
/**
 * A light replacement for built-in textarea component
 * which automaticaly adjusts its height to match the content
 */
var TextareaAutosize = /** @class */ (function (_super) {
    __extends(TextareaAutosize, _super);
    function TextareaAutosize() {
        var _this = _super !== null && _super.apply(this, arguments) || this;
        _this.state = {
            lineHeight: null
        };
        _this.dispatchEvent = function (EVENT_TYPE) {
            var event = document.createEvent('Event');
            event.initEvent(EVENT_TYPE, true, false);
            _this.textarea.dispatchEvent(event);
        };
        _this.updateLineHeight = function () {
            _this.setState({
                lineHeight: getLineHeight(_this.textarea)
            });
        };
        _this.onChange = function (e) {
            var onChange = _this.props.onChange;
            _this.currentValue = e.currentTarget.value;
            onChange && onChange(e);
        };
        _this.saveDOMNodeRef = function (ref) {
            var innerRef = _this.props.innerRef;
            if (innerRef) {
                innerRef(ref);
            }
            _this.textarea = ref;
        };
        _this.getLocals = function () {
            var _a = _this, _b = _a.props, onResize = _b.onResize, maxRows = _b.maxRows, onChange = _b.onChange, style = _b.style, innerRef = _b.innerRef, props = __rest(_b, ["onResize", "maxRows", "onChange", "style", "innerRef"]), lineHeight = _a.state.lineHeight, saveDOMNodeRef = _a.saveDOMNodeRef;
            var maxHeight = maxRows && lineHeight ? lineHeight * maxRows : null;
            return __assign({}, props, { saveDOMNodeRef: saveDOMNodeRef, style: maxHeight ? __assign({}, style, { maxHeight: maxHeight }) : style, onChange: _this.onChange });
        };
        return _this;
    }
    TextareaAutosize.prototype.componentDidMount = function () {
        var _this = this;
        var _a = this.props, onResize = _a.onResize, maxRows = _a.maxRows;
        if (typeof maxRows === 'number') {
            this.updateLineHeight();
        }
        /*
          the defer is needed to:
            - force "autosize" to activate the scrollbar when this.props.maxRows is passed
            - support StyledComponents (see #71)
        */
        setTimeout(function () { return autosize(_this.textarea); });
        if (onResize) {
            this.textarea.addEventListener(RESIZED, onResize);
        }
    };
    TextareaAutosize.prototype.componentWillUnmount = function () {
        var onResize = this.props.onResize;
        if (onResize) {
            this.textarea.removeEventListener(RESIZED, onResize);
        }
        this.dispatchEvent(DESTROY);
    };
    TextareaAutosize.prototype.render = function () {
        var _a = this.getLocals(), children = _a.children, saveDOMNodeRef = _a.saveDOMNodeRef, locals = __rest(_a, ["children", "saveDOMNodeRef"]);
        return (React.createElement("textarea", __assign({}, locals, { ref: saveDOMNodeRef }), children));
    };
    TextareaAutosize.prototype.componentDidUpdate = function (prevProps) {
        if (this.props.value !== this.currentValue || this.props.rows !== prevProps.rows) {
            this.dispatchEvent(UPDATE);
        }
    };
    TextareaAutosize.defaultProps = {
        rows: 1
    };
    TextareaAutosize.propTypes = {
        rows: PropTypes.number,
        maxRows: PropTypes.number,
        onResize: PropTypes.func,
        innerRef: PropTypes.func
    };
    return TextareaAutosize;
}(React.Component));
exports["default"] = TextareaAutosize;


/***/ }),

/***/ "CxY0":
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



var punycode = __webpack_require__("GYWy");
var util = __webpack_require__("Nehr");

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
    querystring = __webpack_require__("s4NR");

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

/***/ "DSFK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ "Ff2n":
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

/***/ "FqII":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["date"]; }());

/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "GYWy":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(module, global) {var __WEBPACK_AMD_DEFINE_RESULT__;/*! https://mths.be/punycode v1.4.1 by @mathias */
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
		throw new RangeError(errors[type]);
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
	 * https://tools.ietf.org/html/rfc3492#section-3.4
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
		'version': '1.4.1',
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

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("YuTi")(module), __webpack_require__("yLpj")))

/***/ }),

/***/ "GemG":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	autosize 4.0.4
	license: MIT
	http://www.jacklmoore.com/autosize
*/
(function (global, factory) {
	if (true) {
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [module, exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else { var mod; }
})(this, function (module, exports) {
	'use strict';

	var map = typeof Map === "function" ? new Map() : function () {
		var keys = [];
		var values = [];

		return {
			has: function has(key) {
				return keys.indexOf(key) > -1;
			},
			get: function get(key) {
				return values[keys.indexOf(key)];
			},
			set: function set(key, value) {
				if (keys.indexOf(key) === -1) {
					keys.push(key);
					values.push(value);
				}
			},
			delete: function _delete(key) {
				var index = keys.indexOf(key);
				if (index > -1) {
					keys.splice(index, 1);
					values.splice(index, 1);
				}
			}
		};
	}();

	var createEvent = function createEvent(name) {
		return new Event(name, { bubbles: true });
	};
	try {
		new Event('test');
	} catch (e) {
		// IE does not support `new Event()`
		createEvent = function createEvent(name) {
			var evt = document.createEvent('Event');
			evt.initEvent(name, true, false);
			return evt;
		};
	}

	function assign(ta) {
		if (!ta || !ta.nodeName || ta.nodeName !== 'TEXTAREA' || map.has(ta)) return;

		var heightOffset = null;
		var clientWidth = null;
		var cachedHeight = null;

		function init() {
			var style = window.getComputedStyle(ta, null);

			if (style.resize === 'vertical') {
				ta.style.resize = 'none';
			} else if (style.resize === 'both') {
				ta.style.resize = 'horizontal';
			}

			if (style.boxSizing === 'content-box') {
				heightOffset = -(parseFloat(style.paddingTop) + parseFloat(style.paddingBottom));
			} else {
				heightOffset = parseFloat(style.borderTopWidth) + parseFloat(style.borderBottomWidth);
			}
			// Fix when a textarea is not on document body and heightOffset is Not a Number
			if (isNaN(heightOffset)) {
				heightOffset = 0;
			}

			update();
		}

		function changeOverflow(value) {
			{
				// Chrome/Safari-specific fix:
				// When the textarea y-overflow is hidden, Chrome/Safari do not reflow the text to account for the space
				// made available by removing the scrollbar. The following forces the necessary text reflow.
				var width = ta.style.width;
				ta.style.width = '0px';
				// Force reflow:
				/* jshint ignore:start */
				ta.offsetWidth;
				/* jshint ignore:end */
				ta.style.width = width;
			}

			ta.style.overflowY = value;
		}

		function getParentOverflows(el) {
			var arr = [];

			while (el && el.parentNode && el.parentNode instanceof Element) {
				if (el.parentNode.scrollTop) {
					arr.push({
						node: el.parentNode,
						scrollTop: el.parentNode.scrollTop
					});
				}
				el = el.parentNode;
			}

			return arr;
		}

		function resize() {
			if (ta.scrollHeight === 0) {
				// If the scrollHeight is 0, then the element probably has display:none or is detached from the DOM.
				return;
			}

			var overflows = getParentOverflows(ta);
			var docTop = document.documentElement && document.documentElement.scrollTop; // Needed for Mobile IE (ticket #240)

			ta.style.height = '';
			ta.style.height = ta.scrollHeight + heightOffset + 'px';

			// used to check if an update is actually necessary on window.resize
			clientWidth = ta.clientWidth;

			// prevents scroll-position jumping
			overflows.forEach(function (el) {
				el.node.scrollTop = el.scrollTop;
			});

			if (docTop) {
				document.documentElement.scrollTop = docTop;
			}
		}

		function update() {
			resize();

			var styleHeight = Math.round(parseFloat(ta.style.height));
			var computed = window.getComputedStyle(ta, null);

			// Using offsetHeight as a replacement for computed.height in IE, because IE does not account use of border-box
			var actualHeight = computed.boxSizing === 'content-box' ? Math.round(parseFloat(computed.height)) : ta.offsetHeight;

			// The actual height not matching the style height (set via the resize method) indicates that 
			// the max-height has been exceeded, in which case the overflow should be allowed.
			if (actualHeight < styleHeight) {
				if (computed.overflowY === 'hidden') {
					changeOverflow('scroll');
					resize();
					actualHeight = computed.boxSizing === 'content-box' ? Math.round(parseFloat(window.getComputedStyle(ta, null).height)) : ta.offsetHeight;
				}
			} else {
				// Normally keep overflow set to hidden, to avoid flash of scrollbar as the textarea expands.
				if (computed.overflowY !== 'hidden') {
					changeOverflow('hidden');
					resize();
					actualHeight = computed.boxSizing === 'content-box' ? Math.round(parseFloat(window.getComputedStyle(ta, null).height)) : ta.offsetHeight;
				}
			}

			if (cachedHeight !== actualHeight) {
				cachedHeight = actualHeight;
				var evt = createEvent('autosize:resized');
				try {
					ta.dispatchEvent(evt);
				} catch (err) {
					// Firefox will throw an error on dispatchEvent for a detached element
					// https://bugzilla.mozilla.org/show_bug.cgi?id=889376
				}
			}
		}

		var pageResize = function pageResize() {
			if (ta.clientWidth !== clientWidth) {
				update();
			}
		};

		var destroy = function (style) {
			window.removeEventListener('resize', pageResize, false);
			ta.removeEventListener('input', update, false);
			ta.removeEventListener('keyup', update, false);
			ta.removeEventListener('autosize:destroy', destroy, false);
			ta.removeEventListener('autosize:update', update, false);

			Object.keys(style).forEach(function (key) {
				ta.style[key] = style[key];
			});

			map.delete(ta);
		}.bind(ta, {
			height: ta.style.height,
			resize: ta.style.resize,
			overflowY: ta.style.overflowY,
			overflowX: ta.style.overflowX,
			wordWrap: ta.style.wordWrap
		});

		ta.addEventListener('autosize:destroy', destroy, false);

		// IE9 does not fire onpropertychange or oninput for deletions,
		// so binding to onkeyup to catch most of those events.
		// There is no way that I know of to detect something like 'cut' in IE9.
		if ('onpropertychange' in ta && 'oninput' in ta) {
			ta.addEventListener('keyup', update, false);
		}

		window.addEventListener('resize', pageResize, false);
		ta.addEventListener('input', update, false);
		ta.addEventListener('autosize:update', update, false);
		ta.style.overflowX = 'hidden';
		ta.style.wordWrap = 'break-word';

		map.set(ta, {
			destroy: destroy,
			update: update
		});

		init();
	}

	function destroy(ta) {
		var methods = map.get(ta);
		if (methods) {
			methods.destroy();
		}
	}

	function update(ta) {
		var methods = map.get(ta);
		if (methods) {
			methods.update();
		}
	}

	var autosize = null;

	// Do nothing in Node.js environment and IE8 (or lower)
	if (typeof window === 'undefined' || typeof window.getComputedStyle !== 'function') {
		autosize = function autosize(el) {
			return el;
		};
		autosize.destroy = function (el) {
			return el;
		};
		autosize.update = function (el) {
			return el;
		};
	} else {
		autosize = function autosize(el, options) {
			if (el) {
				Array.prototype.forEach.call(el.length ? el : [el], function (x) {
					return assign(x, options);
				});
			}
			return el;
		};
		autosize.destroy = function (el) {
			if (el) {
				Array.prototype.forEach.call(el.length ? el : [el], destroy);
			}
			return el;
		};
		autosize.update = function (el) {
			if (el) {
				Array.prototype.forEach.call(el.length ? el : [el], update);
			}
			return el;
		};
	}

	exports.default = autosize;
	module.exports = exports['default'];
});

/***/ }),

/***/ "HSyU":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ "HaE+":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _asyncToGenerator; });
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}

/***/ }),

/***/ "JX7q":
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

/***/ "Ji7U":
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

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ "KEfo":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["viewport"]; }());

/***/ }),

/***/ "KQm4":
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
var iterableToArray = __webpack_require__("25BE");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ "Mmq9":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ "NMb1":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["deprecated"]; }());

/***/ }),

/***/ "Nehr":
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

/***/ "O6Fj":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

exports.__esModule = true;
var TextareaAutosize_1 = __webpack_require__("CNgt");
exports["default"] = TextareaAutosize_1["default"];


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
var nonIterableRest = __webpack_require__("PYwp");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js



function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ "P7XM":
/***/ (function(module, exports) {

if (typeof Object.create === 'function') {
  // implementation from standard node.js 'util' module
  module.exports = function inherits(ctor, superCtor) {
    ctor.super_ = superCtor
    ctor.prototype = Object.create(superCtor.prototype, {
      constructor: {
        value: ctor,
        enumerable: false,
        writable: true,
        configurable: true
      }
    });
  };
} else {
  // old school shim for old browsers
  module.exports = function inherits(ctor, superCtor) {
    ctor.super_ = superCtor
    var TempCtor = function () {}
    TempCtor.prototype = superCtor.prototype
    ctor.prototype = new TempCtor()
    ctor.prototype.constructor = ctor
  }
}


/***/ }),

/***/ "PLxR":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "blockAutocompleter", function() { return /* reexport */ autocompleters_block; });
__webpack_require__.d(__webpack_exports__, "userAutocompleter", function() { return /* reexport */ autocompleters_user; });
__webpack_require__.d(__webpack_exports__, "ServerSideRender", function() { return /* reexport */ server_side_render; });
__webpack_require__.d(__webpack_exports__, "AutosaveMonitor", function() { return /* reexport */ autosave_monitor; });
__webpack_require__.d(__webpack_exports__, "DocumentOutline", function() { return /* reexport */ document_outline; });
__webpack_require__.d(__webpack_exports__, "DocumentOutlineCheck", function() { return /* reexport */ check; });
__webpack_require__.d(__webpack_exports__, "VisualEditorGlobalKeyboardShortcuts", function() { return /* reexport */ visual_editor_shortcuts; });
__webpack_require__.d(__webpack_exports__, "EditorGlobalKeyboardShortcuts", function() { return /* reexport */ EditorGlobalKeyboardShortcuts; });
__webpack_require__.d(__webpack_exports__, "TextEditorGlobalKeyboardShortcuts", function() { return /* reexport */ TextEditorGlobalKeyboardShortcuts; });
__webpack_require__.d(__webpack_exports__, "EditorHistoryRedo", function() { return /* reexport */ editor_history_redo; });
__webpack_require__.d(__webpack_exports__, "EditorHistoryUndo", function() { return /* reexport */ editor_history_undo; });
__webpack_require__.d(__webpack_exports__, "EditorNotices", function() { return /* reexport */ editor_notices; });
__webpack_require__.d(__webpack_exports__, "ErrorBoundary", function() { return /* reexport */ error_boundary; });
__webpack_require__.d(__webpack_exports__, "PageAttributesCheck", function() { return /* reexport */ page_attributes_check; });
__webpack_require__.d(__webpack_exports__, "PageAttributesOrder", function() { return /* reexport */ page_attributes_order; });
__webpack_require__.d(__webpack_exports__, "PageAttributesParent", function() { return /* reexport */ page_attributes_parent; });
__webpack_require__.d(__webpack_exports__, "PageTemplate", function() { return /* reexport */ page_attributes_template; });
__webpack_require__.d(__webpack_exports__, "PostAuthor", function() { return /* reexport */ post_author; });
__webpack_require__.d(__webpack_exports__, "PostAuthorCheck", function() { return /* reexport */ post_author_check; });
__webpack_require__.d(__webpack_exports__, "PostComments", function() { return /* reexport */ post_comments; });
__webpack_require__.d(__webpack_exports__, "PostExcerpt", function() { return /* reexport */ post_excerpt; });
__webpack_require__.d(__webpack_exports__, "PostExcerptCheck", function() { return /* reexport */ post_excerpt_check; });
__webpack_require__.d(__webpack_exports__, "PostFeaturedImage", function() { return /* reexport */ post_featured_image; });
__webpack_require__.d(__webpack_exports__, "PostFeaturedImageCheck", function() { return /* reexport */ post_featured_image_check; });
__webpack_require__.d(__webpack_exports__, "PostFormat", function() { return /* reexport */ post_format; });
__webpack_require__.d(__webpack_exports__, "PostFormatCheck", function() { return /* reexport */ post_format_check; });
__webpack_require__.d(__webpack_exports__, "PostLastRevision", function() { return /* reexport */ post_last_revision; });
__webpack_require__.d(__webpack_exports__, "PostLastRevisionCheck", function() { return /* reexport */ post_last_revision_check; });
__webpack_require__.d(__webpack_exports__, "PostLockedModal", function() { return /* reexport */ post_locked_modal; });
__webpack_require__.d(__webpack_exports__, "PostPendingStatus", function() { return /* reexport */ post_pending_status; });
__webpack_require__.d(__webpack_exports__, "PostPendingStatusCheck", function() { return /* reexport */ post_pending_status_check; });
__webpack_require__.d(__webpack_exports__, "PostPingbacks", function() { return /* reexport */ post_pingbacks; });
__webpack_require__.d(__webpack_exports__, "PostPreviewButton", function() { return /* reexport */ post_preview_button; });
__webpack_require__.d(__webpack_exports__, "PostPublishButton", function() { return /* reexport */ post_publish_button; });
__webpack_require__.d(__webpack_exports__, "PostPublishButtonLabel", function() { return /* reexport */ post_publish_button_label; });
__webpack_require__.d(__webpack_exports__, "PostPublishPanel", function() { return /* reexport */ post_publish_panel; });
__webpack_require__.d(__webpack_exports__, "PostSavedState", function() { return /* reexport */ post_saved_state; });
__webpack_require__.d(__webpack_exports__, "PostSchedule", function() { return /* reexport */ post_schedule; });
__webpack_require__.d(__webpack_exports__, "PostScheduleCheck", function() { return /* reexport */ post_schedule_check; });
__webpack_require__.d(__webpack_exports__, "PostScheduleLabel", function() { return /* reexport */ post_schedule_label; });
__webpack_require__.d(__webpack_exports__, "PostSticky", function() { return /* reexport */ post_sticky; });
__webpack_require__.d(__webpack_exports__, "PostStickyCheck", function() { return /* reexport */ post_sticky_check; });
__webpack_require__.d(__webpack_exports__, "PostSwitchToDraftButton", function() { return /* reexport */ post_switch_to_draft_button; });
__webpack_require__.d(__webpack_exports__, "PostTaxonomies", function() { return /* reexport */ post_taxonomies; });
__webpack_require__.d(__webpack_exports__, "PostTaxonomiesCheck", function() { return /* reexport */ post_taxonomies_check; });
__webpack_require__.d(__webpack_exports__, "PostTextEditor", function() { return /* reexport */ post_text_editor; });
__webpack_require__.d(__webpack_exports__, "PostTitle", function() { return /* reexport */ post_title; });
__webpack_require__.d(__webpack_exports__, "PostTrash", function() { return /* reexport */ post_trash; });
__webpack_require__.d(__webpack_exports__, "PostTrashCheck", function() { return /* reexport */ post_trash_check; });
__webpack_require__.d(__webpack_exports__, "PostTypeSupportCheck", function() { return /* reexport */ post_type_support_check; });
__webpack_require__.d(__webpack_exports__, "PostVisibility", function() { return /* reexport */ post_visibility; });
__webpack_require__.d(__webpack_exports__, "PostVisibilityLabel", function() { return /* reexport */ post_visibility_label; });
__webpack_require__.d(__webpack_exports__, "PostVisibilityCheck", function() { return /* reexport */ post_visibility_check; });
__webpack_require__.d(__webpack_exports__, "TableOfContents", function() { return /* reexport */ table_of_contents; });
__webpack_require__.d(__webpack_exports__, "UnsavedChangesWarning", function() { return /* reexport */ unsaved_changes_warning; });
__webpack_require__.d(__webpack_exports__, "WordCount", function() { return /* reexport */ word_count; });
__webpack_require__.d(__webpack_exports__, "EditorProvider", function() { return /* reexport */ provider; });
__webpack_require__.d(__webpack_exports__, "Autocomplete", function() { return /* reexport */ external_this_wp_blockEditor_["Autocomplete"]; });
__webpack_require__.d(__webpack_exports__, "AlignmentToolbar", function() { return /* reexport */ external_this_wp_blockEditor_["AlignmentToolbar"]; });
__webpack_require__.d(__webpack_exports__, "BlockAlignmentToolbar", function() { return /* reexport */ external_this_wp_blockEditor_["BlockAlignmentToolbar"]; });
__webpack_require__.d(__webpack_exports__, "BlockControls", function() { return /* reexport */ external_this_wp_blockEditor_["BlockControls"]; });
__webpack_require__.d(__webpack_exports__, "BlockEdit", function() { return /* reexport */ external_this_wp_blockEditor_["BlockEdit"]; });
__webpack_require__.d(__webpack_exports__, "BlockEditorKeyboardShortcuts", function() { return /* reexport */ external_this_wp_blockEditor_["BlockEditorKeyboardShortcuts"]; });
__webpack_require__.d(__webpack_exports__, "BlockFormatControls", function() { return /* reexport */ external_this_wp_blockEditor_["BlockFormatControls"]; });
__webpack_require__.d(__webpack_exports__, "BlockIcon", function() { return /* reexport */ external_this_wp_blockEditor_["BlockIcon"]; });
__webpack_require__.d(__webpack_exports__, "BlockInspector", function() { return /* reexport */ external_this_wp_blockEditor_["BlockInspector"]; });
__webpack_require__.d(__webpack_exports__, "BlockList", function() { return /* reexport */ external_this_wp_blockEditor_["BlockList"]; });
__webpack_require__.d(__webpack_exports__, "BlockMover", function() { return /* reexport */ external_this_wp_blockEditor_["BlockMover"]; });
__webpack_require__.d(__webpack_exports__, "BlockNavigationDropdown", function() { return /* reexport */ external_this_wp_blockEditor_["BlockNavigationDropdown"]; });
__webpack_require__.d(__webpack_exports__, "BlockSelectionClearer", function() { return /* reexport */ external_this_wp_blockEditor_["BlockSelectionClearer"]; });
__webpack_require__.d(__webpack_exports__, "BlockSettingsMenu", function() { return /* reexport */ external_this_wp_blockEditor_["BlockSettingsMenu"]; });
__webpack_require__.d(__webpack_exports__, "BlockTitle", function() { return /* reexport */ external_this_wp_blockEditor_["BlockTitle"]; });
__webpack_require__.d(__webpack_exports__, "BlockToolbar", function() { return /* reexport */ external_this_wp_blockEditor_["BlockToolbar"]; });
__webpack_require__.d(__webpack_exports__, "ColorPalette", function() { return /* reexport */ external_this_wp_blockEditor_["ColorPalette"]; });
__webpack_require__.d(__webpack_exports__, "ContrastChecker", function() { return /* reexport */ external_this_wp_blockEditor_["ContrastChecker"]; });
__webpack_require__.d(__webpack_exports__, "CopyHandler", function() { return /* reexport */ external_this_wp_blockEditor_["CopyHandler"]; });
__webpack_require__.d(__webpack_exports__, "createCustomColorsHOC", function() { return /* reexport */ external_this_wp_blockEditor_["createCustomColorsHOC"]; });
__webpack_require__.d(__webpack_exports__, "DefaultBlockAppender", function() { return /* reexport */ external_this_wp_blockEditor_["DefaultBlockAppender"]; });
__webpack_require__.d(__webpack_exports__, "FontSizePicker", function() { return /* reexport */ external_this_wp_blockEditor_["FontSizePicker"]; });
__webpack_require__.d(__webpack_exports__, "getColorClassName", function() { return /* reexport */ external_this_wp_blockEditor_["getColorClassName"]; });
__webpack_require__.d(__webpack_exports__, "getColorObjectByAttributeValues", function() { return /* reexport */ external_this_wp_blockEditor_["getColorObjectByAttributeValues"]; });
__webpack_require__.d(__webpack_exports__, "getColorObjectByColorValue", function() { return /* reexport */ external_this_wp_blockEditor_["getColorObjectByColorValue"]; });
__webpack_require__.d(__webpack_exports__, "getFontSize", function() { return /* reexport */ external_this_wp_blockEditor_["getFontSize"]; });
__webpack_require__.d(__webpack_exports__, "getFontSizeClass", function() { return /* reexport */ external_this_wp_blockEditor_["getFontSizeClass"]; });
__webpack_require__.d(__webpack_exports__, "Inserter", function() { return /* reexport */ external_this_wp_blockEditor_["Inserter"]; });
__webpack_require__.d(__webpack_exports__, "InnerBlocks", function() { return /* reexport */ external_this_wp_blockEditor_["InnerBlocks"]; });
__webpack_require__.d(__webpack_exports__, "InspectorAdvancedControls", function() { return /* reexport */ external_this_wp_blockEditor_["InspectorAdvancedControls"]; });
__webpack_require__.d(__webpack_exports__, "InspectorControls", function() { return /* reexport */ external_this_wp_blockEditor_["InspectorControls"]; });
__webpack_require__.d(__webpack_exports__, "PanelColorSettings", function() { return /* reexport */ external_this_wp_blockEditor_["PanelColorSettings"]; });
__webpack_require__.d(__webpack_exports__, "PlainText", function() { return /* reexport */ external_this_wp_blockEditor_["PlainText"]; });
__webpack_require__.d(__webpack_exports__, "RichText", function() { return /* reexport */ external_this_wp_blockEditor_["RichText"]; });
__webpack_require__.d(__webpack_exports__, "RichTextShortcut", function() { return /* reexport */ external_this_wp_blockEditor_["RichTextShortcut"]; });
__webpack_require__.d(__webpack_exports__, "RichTextToolbarButton", function() { return /* reexport */ external_this_wp_blockEditor_["RichTextToolbarButton"]; });
__webpack_require__.d(__webpack_exports__, "RichTextInserterItem", function() { return /* reexport */ external_this_wp_blockEditor_["RichTextInserterItem"]; });
__webpack_require__.d(__webpack_exports__, "UnstableRichTextInputEvent", function() { return /* reexport */ external_this_wp_blockEditor_["UnstableRichTextInputEvent"]; });
__webpack_require__.d(__webpack_exports__, "MediaPlaceholder", function() { return /* reexport */ external_this_wp_blockEditor_["MediaPlaceholder"]; });
__webpack_require__.d(__webpack_exports__, "MediaUpload", function() { return /* reexport */ external_this_wp_blockEditor_["MediaUpload"]; });
__webpack_require__.d(__webpack_exports__, "MediaUploadCheck", function() { return /* reexport */ external_this_wp_blockEditor_["MediaUploadCheck"]; });
__webpack_require__.d(__webpack_exports__, "MultiBlocksSwitcher", function() { return /* reexport */ external_this_wp_blockEditor_["MultiBlocksSwitcher"]; });
__webpack_require__.d(__webpack_exports__, "MultiSelectScrollIntoView", function() { return /* reexport */ external_this_wp_blockEditor_["MultiSelectScrollIntoView"]; });
__webpack_require__.d(__webpack_exports__, "NavigableToolbar", function() { return /* reexport */ external_this_wp_blockEditor_["NavigableToolbar"]; });
__webpack_require__.d(__webpack_exports__, "ObserveTyping", function() { return /* reexport */ external_this_wp_blockEditor_["ObserveTyping"]; });
__webpack_require__.d(__webpack_exports__, "PreserveScrollInReorder", function() { return /* reexport */ external_this_wp_blockEditor_["PreserveScrollInReorder"]; });
__webpack_require__.d(__webpack_exports__, "SkipToSelectedBlock", function() { return /* reexport */ external_this_wp_blockEditor_["SkipToSelectedBlock"]; });
__webpack_require__.d(__webpack_exports__, "URLInput", function() { return /* reexport */ external_this_wp_blockEditor_["URLInput"]; });
__webpack_require__.d(__webpack_exports__, "URLInputButton", function() { return /* reexport */ external_this_wp_blockEditor_["URLInputButton"]; });
__webpack_require__.d(__webpack_exports__, "URLPopover", function() { return /* reexport */ external_this_wp_blockEditor_["URLPopover"]; });
__webpack_require__.d(__webpack_exports__, "Warning", function() { return /* reexport */ external_this_wp_blockEditor_["Warning"]; });
__webpack_require__.d(__webpack_exports__, "WritingFlow", function() { return /* reexport */ external_this_wp_blockEditor_["WritingFlow"]; });
__webpack_require__.d(__webpack_exports__, "withColorContext", function() { return /* reexport */ external_this_wp_blockEditor_["withColorContext"]; });
__webpack_require__.d(__webpack_exports__, "withColors", function() { return /* reexport */ external_this_wp_blockEditor_["withColors"]; });
__webpack_require__.d(__webpack_exports__, "withFontSizes", function() { return /* reexport */ external_this_wp_blockEditor_["withFontSizes"]; });
__webpack_require__.d(__webpack_exports__, "mediaUpload", function() { return /* reexport */ media_upload; });
__webpack_require__.d(__webpack_exports__, "cleanForSlug", function() { return /* reexport */ cleanForSlug; });
__webpack_require__.d(__webpack_exports__, "transformStyles", function() { return /* reexport */ editor_styles; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/editor/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "setupEditor", function() { return setupEditor; });
__webpack_require__.d(actions_namespaceObject, "resetPost", function() { return resetPost; });
__webpack_require__.d(actions_namespaceObject, "resetAutosave", function() { return resetAutosave; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRequestPostUpdateStart", function() { return __experimentalRequestPostUpdateStart; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRequestPostUpdateSuccess", function() { return __experimentalRequestPostUpdateSuccess; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRequestPostUpdateFailure", function() { return __experimentalRequestPostUpdateFailure; });
__webpack_require__.d(actions_namespaceObject, "updatePost", function() { return updatePost; });
__webpack_require__.d(actions_namespaceObject, "setupEditorState", function() { return setupEditorState; });
__webpack_require__.d(actions_namespaceObject, "editPost", function() { return actions_editPost; });
__webpack_require__.d(actions_namespaceObject, "__experimentalOptimisticUpdatePost", function() { return __experimentalOptimisticUpdatePost; });
__webpack_require__.d(actions_namespaceObject, "savePost", function() { return savePost; });
__webpack_require__.d(actions_namespaceObject, "refreshPost", function() { return refreshPost; });
__webpack_require__.d(actions_namespaceObject, "trashPost", function() { return trashPost; });
__webpack_require__.d(actions_namespaceObject, "autosave", function() { return actions_autosave; });
__webpack_require__.d(actions_namespaceObject, "redo", function() { return actions_redo; });
__webpack_require__.d(actions_namespaceObject, "undo", function() { return actions_undo; });
__webpack_require__.d(actions_namespaceObject, "createUndoLevel", function() { return createUndoLevel; });
__webpack_require__.d(actions_namespaceObject, "updatePostLock", function() { return updatePostLock; });
__webpack_require__.d(actions_namespaceObject, "__experimentalFetchReusableBlocks", function() { return __experimentalFetchReusableBlocks; });
__webpack_require__.d(actions_namespaceObject, "__experimentalReceiveReusableBlocks", function() { return __experimentalReceiveReusableBlocks; });
__webpack_require__.d(actions_namespaceObject, "__experimentalSaveReusableBlock", function() { return __experimentalSaveReusableBlock; });
__webpack_require__.d(actions_namespaceObject, "__experimentalDeleteReusableBlock", function() { return __experimentalDeleteReusableBlock; });
__webpack_require__.d(actions_namespaceObject, "__experimentalUpdateReusableBlockTitle", function() { return __experimentalUpdateReusableBlockTitle; });
__webpack_require__.d(actions_namespaceObject, "__experimentalConvertBlockToStatic", function() { return __experimentalConvertBlockToStatic; });
__webpack_require__.d(actions_namespaceObject, "__experimentalConvertBlockToReusable", function() { return __experimentalConvertBlockToReusable; });
__webpack_require__.d(actions_namespaceObject, "enablePublishSidebar", function() { return enablePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "disablePublishSidebar", function() { return disablePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "lockPostSaving", function() { return lockPostSaving; });
__webpack_require__.d(actions_namespaceObject, "unlockPostSaving", function() { return unlockPostSaving; });
__webpack_require__.d(actions_namespaceObject, "resetEditorBlocks", function() { return actions_resetEditorBlocks; });
__webpack_require__.d(actions_namespaceObject, "updateEditorSettings", function() { return updateEditorSettings; });
__webpack_require__.d(actions_namespaceObject, "resetBlocks", function() { return resetBlocks; });
__webpack_require__.d(actions_namespaceObject, "receiveBlocks", function() { return receiveBlocks; });
__webpack_require__.d(actions_namespaceObject, "updateBlock", function() { return updateBlock; });
__webpack_require__.d(actions_namespaceObject, "updateBlockAttributes", function() { return updateBlockAttributes; });
__webpack_require__.d(actions_namespaceObject, "selectBlock", function() { return selectBlock; });
__webpack_require__.d(actions_namespaceObject, "startMultiSelect", function() { return startMultiSelect; });
__webpack_require__.d(actions_namespaceObject, "stopMultiSelect", function() { return stopMultiSelect; });
__webpack_require__.d(actions_namespaceObject, "multiSelect", function() { return multiSelect; });
__webpack_require__.d(actions_namespaceObject, "clearSelectedBlock", function() { return clearSelectedBlock; });
__webpack_require__.d(actions_namespaceObject, "toggleSelection", function() { return toggleSelection; });
__webpack_require__.d(actions_namespaceObject, "replaceBlocks", function() { return replaceBlocks; });
__webpack_require__.d(actions_namespaceObject, "replaceBlock", function() { return replaceBlock; });
__webpack_require__.d(actions_namespaceObject, "moveBlocksDown", function() { return moveBlocksDown; });
__webpack_require__.d(actions_namespaceObject, "moveBlocksUp", function() { return moveBlocksUp; });
__webpack_require__.d(actions_namespaceObject, "moveBlockToPosition", function() { return moveBlockToPosition; });
__webpack_require__.d(actions_namespaceObject, "insertBlock", function() { return insertBlock; });
__webpack_require__.d(actions_namespaceObject, "insertBlocks", function() { return insertBlocks; });
__webpack_require__.d(actions_namespaceObject, "showInsertionPoint", function() { return showInsertionPoint; });
__webpack_require__.d(actions_namespaceObject, "hideInsertionPoint", function() { return hideInsertionPoint; });
__webpack_require__.d(actions_namespaceObject, "setTemplateValidity", function() { return setTemplateValidity; });
__webpack_require__.d(actions_namespaceObject, "synchronizeTemplate", function() { return synchronizeTemplate; });
__webpack_require__.d(actions_namespaceObject, "mergeBlocks", function() { return mergeBlocks; });
__webpack_require__.d(actions_namespaceObject, "removeBlocks", function() { return removeBlocks; });
__webpack_require__.d(actions_namespaceObject, "removeBlock", function() { return removeBlock; });
__webpack_require__.d(actions_namespaceObject, "toggleBlockMode", function() { return toggleBlockMode; });
__webpack_require__.d(actions_namespaceObject, "startTyping", function() { return startTyping; });
__webpack_require__.d(actions_namespaceObject, "stopTyping", function() { return stopTyping; });
__webpack_require__.d(actions_namespaceObject, "enterFormattedText", function() { return enterFormattedText; });
__webpack_require__.d(actions_namespaceObject, "exitFormattedText", function() { return exitFormattedText; });
__webpack_require__.d(actions_namespaceObject, "insertDefaultBlock", function() { return insertDefaultBlock; });
__webpack_require__.d(actions_namespaceObject, "updateBlockListSettings", function() { return updateBlockListSettings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/editor/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "hasEditorUndo", function() { return hasEditorUndo; });
__webpack_require__.d(selectors_namespaceObject, "hasEditorRedo", function() { return hasEditorRedo; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostNew", function() { return selectors_isEditedPostNew; });
__webpack_require__.d(selectors_namespaceObject, "hasChangedContent", function() { return hasChangedContent; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostDirty", function() { return selectors_isEditedPostDirty; });
__webpack_require__.d(selectors_namespaceObject, "isCleanNewPost", function() { return selectors_isCleanNewPost; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPost", function() { return selectors_getCurrentPost; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostType", function() { return selectors_getCurrentPostType; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostId", function() { return selectors_getCurrentPostId; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostRevisionsCount", function() { return getCurrentPostRevisionsCount; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostLastRevisionId", function() { return getCurrentPostLastRevisionId; });
__webpack_require__.d(selectors_namespaceObject, "getPostEdits", function() { return getPostEdits; });
__webpack_require__.d(selectors_namespaceObject, "getReferenceByDistinctEdits", function() { return getReferenceByDistinctEdits; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostAttribute", function() { return selectors_getCurrentPostAttribute; });
__webpack_require__.d(selectors_namespaceObject, "getEditedPostAttribute", function() { return selectors_getEditedPostAttribute; });
__webpack_require__.d(selectors_namespaceObject, "getAutosaveAttribute", function() { return getAutosaveAttribute; });
__webpack_require__.d(selectors_namespaceObject, "getEditedPostVisibility", function() { return selectors_getEditedPostVisibility; });
__webpack_require__.d(selectors_namespaceObject, "isCurrentPostPending", function() { return isCurrentPostPending; });
__webpack_require__.d(selectors_namespaceObject, "isCurrentPostPublished", function() { return selectors_isCurrentPostPublished; });
__webpack_require__.d(selectors_namespaceObject, "isCurrentPostScheduled", function() { return selectors_isCurrentPostScheduled; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostPublishable", function() { return selectors_isEditedPostPublishable; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostSaveable", function() { return selectors_isEditedPostSaveable; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostEmpty", function() { return isEditedPostEmpty; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostAutosaveable", function() { return selectors_isEditedPostAutosaveable; });
__webpack_require__.d(selectors_namespaceObject, "getAutosave", function() { return getAutosave; });
__webpack_require__.d(selectors_namespaceObject, "hasAutosave", function() { return hasAutosave; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostBeingScheduled", function() { return selectors_isEditedPostBeingScheduled; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostDateFloating", function() { return isEditedPostDateFloating; });
__webpack_require__.d(selectors_namespaceObject, "isSavingPost", function() { return selectors_isSavingPost; });
__webpack_require__.d(selectors_namespaceObject, "didPostSaveRequestSucceed", function() { return didPostSaveRequestSucceed; });
__webpack_require__.d(selectors_namespaceObject, "didPostSaveRequestFail", function() { return didPostSaveRequestFail; });
__webpack_require__.d(selectors_namespaceObject, "isAutosavingPost", function() { return selectors_isAutosavingPost; });
__webpack_require__.d(selectors_namespaceObject, "isPreviewingPost", function() { return isPreviewingPost; });
__webpack_require__.d(selectors_namespaceObject, "getEditedPostPreviewLink", function() { return selectors_getEditedPostPreviewLink; });
__webpack_require__.d(selectors_namespaceObject, "getSuggestedPostFormat", function() { return selectors_getSuggestedPostFormat; });
__webpack_require__.d(selectors_namespaceObject, "getBlocksForSerialization", function() { return getBlocksForSerialization; });
__webpack_require__.d(selectors_namespaceObject, "getEditedPostContent", function() { return getEditedPostContent; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetReusableBlock", function() { return __experimentalGetReusableBlock; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalIsSavingReusableBlock", function() { return __experimentalIsSavingReusableBlock; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalIsFetchingReusableBlock", function() { return __experimentalIsFetchingReusableBlock; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetReusableBlocks", function() { return __experimentalGetReusableBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getStateBeforeOptimisticTransaction", function() { return getStateBeforeOptimisticTransaction; });
__webpack_require__.d(selectors_namespaceObject, "isPublishingPost", function() { return selectors_isPublishingPost; });
__webpack_require__.d(selectors_namespaceObject, "isPermalinkEditable", function() { return selectors_isPermalinkEditable; });
__webpack_require__.d(selectors_namespaceObject, "getPermalink", function() { return getPermalink; });
__webpack_require__.d(selectors_namespaceObject, "getPermalinkParts", function() { return selectors_getPermalinkParts; });
__webpack_require__.d(selectors_namespaceObject, "inSomeHistory", function() { return inSomeHistory; });
__webpack_require__.d(selectors_namespaceObject, "isPostLocked", function() { return isPostLocked; });
__webpack_require__.d(selectors_namespaceObject, "isPostSavingLocked", function() { return selectors_isPostSavingLocked; });
__webpack_require__.d(selectors_namespaceObject, "isPostLockTakeover", function() { return isPostLockTakeover; });
__webpack_require__.d(selectors_namespaceObject, "getPostLockUser", function() { return getPostLockUser; });
__webpack_require__.d(selectors_namespaceObject, "getActivePostLock", function() { return getActivePostLock; });
__webpack_require__.d(selectors_namespaceObject, "canUserUseUnfilteredHTML", function() { return canUserUseUnfilteredHTML; });
__webpack_require__.d(selectors_namespaceObject, "isPublishSidebarEnabled", function() { return selectors_isPublishSidebarEnabled; });
__webpack_require__.d(selectors_namespaceObject, "getEditorBlocks", function() { return getEditorBlocks; });
__webpack_require__.d(selectors_namespaceObject, "__unstableIsEditorReady", function() { return __unstableIsEditorReady; });
__webpack_require__.d(selectors_namespaceObject, "getEditorSettings", function() { return selectors_getEditorSettings; });
__webpack_require__.d(selectors_namespaceObject, "getBlockDependantsCacheBust", function() { return getBlockDependantsCacheBust; });
__webpack_require__.d(selectors_namespaceObject, "getBlockName", function() { return selectors_getBlockName; });
__webpack_require__.d(selectors_namespaceObject, "isBlockValid", function() { return isBlockValid; });
__webpack_require__.d(selectors_namespaceObject, "getBlockAttributes", function() { return getBlockAttributes; });
__webpack_require__.d(selectors_namespaceObject, "getBlock", function() { return getBlock; });
__webpack_require__.d(selectors_namespaceObject, "getBlocks", function() { return selectors_getBlocks; });
__webpack_require__.d(selectors_namespaceObject, "__unstableGetBlockWithoutInnerBlocks", function() { return __unstableGetBlockWithoutInnerBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getClientIdsOfDescendants", function() { return getClientIdsOfDescendants; });
__webpack_require__.d(selectors_namespaceObject, "getClientIdsWithDescendants", function() { return getClientIdsWithDescendants; });
__webpack_require__.d(selectors_namespaceObject, "getGlobalBlockCount", function() { return getGlobalBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "getBlocksByClientId", function() { return getBlocksByClientId; });
__webpack_require__.d(selectors_namespaceObject, "getBlockCount", function() { return getBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "getBlockSelectionStart", function() { return getBlockSelectionStart; });
__webpack_require__.d(selectors_namespaceObject, "getBlockSelectionEnd", function() { return getBlockSelectionEnd; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlockCount", function() { return getSelectedBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "hasSelectedBlock", function() { return hasSelectedBlock; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlockClientId", function() { return selectors_getSelectedBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlock", function() { return getSelectedBlock; });
__webpack_require__.d(selectors_namespaceObject, "getBlockRootClientId", function() { return getBlockRootClientId; });
__webpack_require__.d(selectors_namespaceObject, "getBlockHierarchyRootClientId", function() { return getBlockHierarchyRootClientId; });
__webpack_require__.d(selectors_namespaceObject, "getAdjacentBlockClientId", function() { return getAdjacentBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getPreviousBlockClientId", function() { return getPreviousBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getNextBlockClientId", function() { return getNextBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlocksInitialCaretPosition", function() { return getSelectedBlocksInitialCaretPosition; });
__webpack_require__.d(selectors_namespaceObject, "getMultiSelectedBlockClientIds", function() { return getMultiSelectedBlockClientIds; });
__webpack_require__.d(selectors_namespaceObject, "getMultiSelectedBlocks", function() { return getMultiSelectedBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getFirstMultiSelectedBlockClientId", function() { return getFirstMultiSelectedBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getLastMultiSelectedBlockClientId", function() { return getLastMultiSelectedBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "isFirstMultiSelectedBlock", function() { return isFirstMultiSelectedBlock; });
__webpack_require__.d(selectors_namespaceObject, "isBlockMultiSelected", function() { return isBlockMultiSelected; });
__webpack_require__.d(selectors_namespaceObject, "isAncestorMultiSelected", function() { return isAncestorMultiSelected; });
__webpack_require__.d(selectors_namespaceObject, "getMultiSelectedBlocksStartClientId", function() { return getMultiSelectedBlocksStartClientId; });
__webpack_require__.d(selectors_namespaceObject, "getMultiSelectedBlocksEndClientId", function() { return getMultiSelectedBlocksEndClientId; });
__webpack_require__.d(selectors_namespaceObject, "getBlockOrder", function() { return getBlockOrder; });
__webpack_require__.d(selectors_namespaceObject, "getBlockIndex", function() { return getBlockIndex; });
__webpack_require__.d(selectors_namespaceObject, "isBlockSelected", function() { return isBlockSelected; });
__webpack_require__.d(selectors_namespaceObject, "hasSelectedInnerBlock", function() { return hasSelectedInnerBlock; });
__webpack_require__.d(selectors_namespaceObject, "isBlockWithinSelection", function() { return isBlockWithinSelection; });
__webpack_require__.d(selectors_namespaceObject, "hasMultiSelection", function() { return hasMultiSelection; });
__webpack_require__.d(selectors_namespaceObject, "isMultiSelecting", function() { return isMultiSelecting; });
__webpack_require__.d(selectors_namespaceObject, "isSelectionEnabled", function() { return isSelectionEnabled; });
__webpack_require__.d(selectors_namespaceObject, "getBlockMode", function() { return getBlockMode; });
__webpack_require__.d(selectors_namespaceObject, "isTyping", function() { return selectors_isTyping; });
__webpack_require__.d(selectors_namespaceObject, "isCaretWithinFormattedText", function() { return selectors_isCaretWithinFormattedText; });
__webpack_require__.d(selectors_namespaceObject, "getBlockInsertionPoint", function() { return getBlockInsertionPoint; });
__webpack_require__.d(selectors_namespaceObject, "isBlockInsertionPointVisible", function() { return isBlockInsertionPointVisible; });
__webpack_require__.d(selectors_namespaceObject, "isValidTemplate", function() { return isValidTemplate; });
__webpack_require__.d(selectors_namespaceObject, "getTemplate", function() { return getTemplate; });
__webpack_require__.d(selectors_namespaceObject, "getTemplateLock", function() { return getTemplateLock; });
__webpack_require__.d(selectors_namespaceObject, "canInsertBlockType", function() { return canInsertBlockType; });
__webpack_require__.d(selectors_namespaceObject, "getInserterItems", function() { return selectors_getInserterItems; });
__webpack_require__.d(selectors_namespaceObject, "hasInserterItems", function() { return hasInserterItems; });
__webpack_require__.d(selectors_namespaceObject, "getBlockListSettings", function() { return getBlockListSettings; });

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__("axFQ");

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: external {"this":["wp","coreData"]}
var external_this_wp_coreData_ = __webpack_require__("jZUy");

// EXTERNAL MODULE: external {"this":["wp","notices"]}
var external_this_wp_notices_ = __webpack_require__("onLe");

// EXTERNAL MODULE: external {"this":["wp","nux"]}
var external_this_wp_nux_ = __webpack_require__("ZU7w");

// EXTERNAL MODULE: external {"this":["wp","richText"]}
var external_this_wp_richText_ = __webpack_require__("qRz9");

// EXTERNAL MODULE: external {"this":["wp","viewport"]}
var external_this_wp_viewport_ = __webpack_require__("KEfo");

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__("ODXe");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("rePB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__("vpQ4");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/typeof.js
var esm_typeof = __webpack_require__("U8pU");

// EXTERNAL MODULE: ./node_modules/redux-optimist/index.js
var redux_optimist = __webpack_require__("hx/w");
var redux_optimist_default = /*#__PURE__*/__webpack_require__.n(redux_optimist);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__("Mmq9");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/defaults.js


/**
 * WordPress dependencies
 */

var PREFERENCES_DEFAULTS = {
  isPublishSidebarEnabled: true
};
/**
 * Default initial edits state.
 *
 * @type {Object}
 */

var INITIAL_EDITS_DEFAULTS = {};
/**
 * The default post editor settings
 *
 *  allowedBlockTypes  boolean|Array Allowed block types
 *  richEditingEnabled boolean       Whether rich editing is enabled or not
 *  enableCustomFields boolean       Whether the WordPress custom fields are enabled or not
 *  autosaveInterval   number        Autosave Interval
 *  availableTemplates array?        The available post templates
 *  disablePostFormats boolean       Whether or not the post formats are disabled
 *  allowedMimeTypes   array?        List of allowed mime types and file extensions
 *  maxUploadFileSize  number        Maximum upload file size
 */

var EDITOR_SETTINGS_DEFAULTS = Object(objectSpread["a" /* default */])({}, external_this_wp_blockEditor_["SETTINGS_DEFAULTS"], {
  richEditingEnabled: true,
  enableCustomFields: false
});

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/constants.js
/**
 * Set of post properties for which edits should assume a merging behavior,
 * assuming an object value.
 *
 * @type {Set}
 */
var EDIT_MERGE_PROPERTIES = new Set(['meta']);
/**
 * Constant for the store module (or reducer) key.
 * @type {string}
 */

var STORE_KEY = 'core/editor';
var POST_UPDATE_TRANSACTION_ID = 'post-update';
var SAVE_POST_NOTICE_ID = 'SAVE_POST_NOTICE_ID';
var TRASH_POST_NOTICE_ID = 'TRASH_POST_NOTICE_ID';
var PERMALINK_POSTNAME_REGEX = /%(?:postname|pagename)%/;
var ONE_MINUTE_IN_MS = 60 * 1000;

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/with-change-detection/index.js


/**
 * External dependencies
 */

/**
 * Higher-order reducer creator for tracking changes to state over time. The
 * returned reducer will include a `isDirty` property on the object reflecting
 * whether the original reference of the reducer has changed.
 *
 * @param {?Object} options             Optional options.
 * @param {?Array}  options.ignoreTypes Action types upon which to skip check.
 * @param {?Array}  options.resetTypes  Action types upon which to reset dirty.
 *
 * @return {Function} Higher-order reducer.
 */

var with_change_detection_withChangeDetection = function withChangeDetection() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return function (reducer) {
    return function (state, action) {
      var nextState = reducer(state, action); // Reset at:
      //  - Initial state
      //  - Reset types

      var isReset = state === undefined || Object(external_lodash_["includes"])(options.resetTypes, action.type);
      var isChanging = state !== nextState; // If not intending to update dirty flag, return early and avoid clone.

      if (!isChanging && !isReset) {
        return state;
      } // Avoid mutating state, unless it's already changing by original
      // reducer and not initial.


      if (!isChanging || state === undefined) {
        nextState = Object(objectSpread["a" /* default */])({}, nextState);
      }

      var isIgnored = Object(external_lodash_["includes"])(options.ignoreTypes, action.type);

      if (isIgnored) {
        // Preserve the original value if ignored.
        nextState.isDirty = state.isDirty;
      } else {
        nextState.isDirty = !isReset && isChanging;
      }

      return nextState;
    };
  };
};

/* harmony default export */ var with_change_detection = (with_change_detection_withChangeDetection);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__("KQm4");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/with-history/index.js



/**
 * External dependencies
 */

/**
 * Default options for withHistory reducer enhancer. Refer to withHistory
 * documentation for options explanation.
 *
 * @see withHistory
 *
 * @type {Object}
 */

var DEFAULT_OPTIONS = {
  resetTypes: [],
  ignoreTypes: [],
  shouldOverwriteState: function shouldOverwriteState() {
    return false;
  }
};
/**
 * Higher-order reducer creator which transforms the result of the original
 * reducer into an object tracking its own history (past, present, future).
 *
 * @param {?Object}   options                      Optional options.
 * @param {?Array}    options.resetTypes           Action types upon which to
 *                                                 clear past.
 * @param {?Array}    options.ignoreTypes          Action types upon which to
 *                                                 avoid history tracking.
 * @param {?Function} options.shouldOverwriteState Function receiving last and
 *                                                 current actions, returning
 *                                                 boolean indicating whether
 *                                                 present should be merged,
 *                                                 rather than add undo level.
 *
 * @return {Function} Higher-order reducer.
 */

var with_history_withHistory = function withHistory() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return function (reducer) {
    options = Object(objectSpread["a" /* default */])({}, DEFAULT_OPTIONS, options); // `ignoreTypes` is simply a convenience for `shouldOverwriteState`

    options.shouldOverwriteState = Object(external_lodash_["overSome"])([options.shouldOverwriteState, function (action) {
      return Object(external_lodash_["includes"])(options.ignoreTypes, action.type);
    }]);
    var initialState = {
      past: [],
      present: reducer(undefined, {}),
      future: [],
      lastAction: null,
      shouldCreateUndoLevel: false
    };
    var _options = options,
        _options$resetTypes = _options.resetTypes,
        resetTypes = _options$resetTypes === void 0 ? [] : _options$resetTypes,
        _options$shouldOverwr = _options.shouldOverwriteState,
        shouldOverwriteState = _options$shouldOverwr === void 0 ? function () {
      return false;
    } : _options$shouldOverwr;
    return function () {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : initialState;
      var action = arguments.length > 1 ? arguments[1] : undefined;
      var past = state.past,
          present = state.present,
          future = state.future,
          lastAction = state.lastAction,
          shouldCreateUndoLevel = state.shouldCreateUndoLevel;
      var previousAction = lastAction;

      switch (action.type) {
        case 'UNDO':
          // Can't undo if no past.
          if (!past.length) {
            return state;
          }

          return {
            past: Object(external_lodash_["dropRight"])(past),
            present: Object(external_lodash_["last"])(past),
            future: [present].concat(Object(toConsumableArray["a" /* default */])(future)),
            lastAction: null,
            shouldCreateUndoLevel: false
          };

        case 'REDO':
          // Can't redo if no future.
          if (!future.length) {
            return state;
          }

          return {
            past: [].concat(Object(toConsumableArray["a" /* default */])(past), [present]),
            present: Object(external_lodash_["first"])(future),
            future: Object(external_lodash_["drop"])(future),
            lastAction: null,
            shouldCreateUndoLevel: false
          };

        case 'CREATE_UNDO_LEVEL':
          return Object(objectSpread["a" /* default */])({}, state, {
            lastAction: null,
            shouldCreateUndoLevel: true
          });
      }

      var nextPresent = reducer(present, action);

      if (Object(external_lodash_["includes"])(resetTypes, action.type)) {
        return {
          past: [],
          present: nextPresent,
          future: [],
          lastAction: null,
          shouldCreateUndoLevel: false
        };
      }

      if (present === nextPresent) {
        return state;
      }

      var nextPast = past; // The `lastAction` property is used to compare actions in the
      // `shouldOverwriteState` option. If an action should be ignored, do not
      // submit that action as the last action, otherwise the ability to
      // compare subsequent actions will break.

      var lastActionToSubmit = previousAction;

      if (shouldCreateUndoLevel || !past.length || !shouldOverwriteState(action, previousAction)) {
        nextPast = [].concat(Object(toConsumableArray["a" /* default */])(past), [present]);
        lastActionToSubmit = action;
      }

      return {
        past: nextPast,
        present: nextPresent,
        future: [],
        shouldCreateUndoLevel: false,
        lastAction: lastActionToSubmit
      };
    };
  };
};

/* harmony default export */ var with_history = (with_history_withHistory);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/reducer.js





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
 * Returns a post attribute value, flattening nested rendered content using its
 * raw value in place of its original object form.
 *
 * @param {*} value Original value.
 *
 * @return {*} Raw value.
 */

function getPostRawValue(value) {
  if (value && 'object' === Object(esm_typeof["a" /* default */])(value) && 'raw' in value) {
    return value.raw;
  }

  return value;
}
/**
 * Returns an object against which it is safe to perform mutating operations,
 * given the original object and its current working copy.
 *
 * @param {Object} original Original object.
 * @param {Object} working  Working object.
 *
 * @return {Object} Mutation-safe object.
 */

function getMutateSafeObject(original, working) {
  if (original === working) {
    return Object(objectSpread["a" /* default */])({}, original);
  }

  return working;
}
/**
 * Returns true if the two object arguments have the same keys, or false
 * otherwise.
 *
 * @param {Object} a First object.
 * @param {Object} b Second object.
 *
 * @return {boolean} Whether the two objects have the same keys.
 */


function hasSameKeys(a, b) {
  return Object(external_lodash_["isEqual"])(Object(external_lodash_["keys"])(a), Object(external_lodash_["keys"])(b));
}
/**
 * Returns true if, given the currently dispatching action and the previously
 * dispatched action, the two actions are editing the same post property, or
 * false otherwise.
 *
 * @param {Object} action         Currently dispatching action.
 * @param {Object} previousAction Previously dispatched action.
 *
 * @return {boolean} Whether actions are updating the same post property.
 */

function isUpdatingSamePostProperty(action, previousAction) {
  return action.type === 'EDIT_POST' && hasSameKeys(action.edits, previousAction.edits);
}
/**
 * Returns true if, given the currently dispatching action and the previously
 * dispatched action, the two actions are modifying the same property such that
 * undo history should be batched.
 *
 * @param {Object} action         Currently dispatching action.
 * @param {Object} previousAction Previously dispatched action.
 *
 * @return {boolean} Whether to overwrite present state.
 */

function reducer_shouldOverwriteState(action, previousAction) {
  if (action.type === 'RESET_EDITOR_BLOCKS') {
    return !action.shouldCreateUndoLevel;
  }

  if (!previousAction || action.type !== previousAction.type) {
    return false;
  }

  return isUpdatingSamePostProperty(action, previousAction);
}
/**
 * Undoable reducer returning the editor post state, including blocks parsed
 * from current HTML markup.
 *
 * Handles the following state keys:
 *  - edits: an object describing changes to be made to the current post, in
 *           the format accepted by the WP REST API
 *  - blocks: post content blocks
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @returns {Object} Updated state.
 */

var editor = Object(external_lodash_["flow"])([external_this_wp_data_["combineReducers"], with_history({
  resetTypes: ['SETUP_EDITOR_STATE'],
  ignoreTypes: ['RESET_POST', 'UPDATE_POST'],
  shouldOverwriteState: reducer_shouldOverwriteState
})])({
  // Track whether changes exist, resetting at each post save. Relies on
  // editor initialization firing post reset as an effect.
  blocks: with_change_detection({
    resetTypes: ['SETUP_EDITOR_STATE', 'REQUEST_POST_UPDATE_START']
  })(function () {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
      value: []
    };
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'RESET_EDITOR_BLOCKS':
        if (action.blocks === state.value) {
          return state;
        }

        return {
          value: action.blocks
        };
    }

    return state;
  }),
  edits: function edits() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'EDIT_POST':
        return Object(external_lodash_["reduce"])(action.edits, function (result, value, key) {
          // Only assign into result if not already same value
          if (value !== state[key]) {
            result = getMutateSafeObject(state, result);

            if (EDIT_MERGE_PROPERTIES.has(key)) {
              // Merge properties should assign to current value.
              result[key] = Object(objectSpread["a" /* default */])({}, result[key], value);
            } else {
              // Otherwise override.
              result[key] = value;
            }
          }

          return result;
        }, state);

      case 'UPDATE_POST':
      case 'RESET_POST':
        var getCanonicalValue = action.type === 'UPDATE_POST' ? function (key) {
          return action.edits[key];
        } : function (key) {
          return getPostRawValue(action.post[key]);
        };
        return Object(external_lodash_["reduce"])(state, function (result, value, key) {
          if (!Object(external_lodash_["isEqual"])(value, getCanonicalValue(key))) {
            return result;
          }

          result = getMutateSafeObject(state, result);
          delete result[key];
          return result;
        }, state);

      case 'RESET_EDITOR_BLOCKS':
        if ('content' in state) {
          return Object(external_lodash_["omit"])(state, 'content');
        }

        return state;
    }

    return state;
  }
});
/**
 * Reducer returning the initial edits state. With matching shape to that of
 * `editor.edits`, the initial edits are those applied programmatically, are
 * not considered in prompting the user for unsaved changes, and are included
 * in (and reset by) the next save payload.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Action object.
 *
 * @return {Object} Next state.
 */

function initialEdits() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : INITIAL_EDITS_DEFAULTS;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SETUP_EDITOR':
      if (!action.edits) {
        break;
      }

      return action.edits;

    case 'SETUP_EDITOR_STATE':
      if ('content' in state) {
        return Object(external_lodash_["omit"])(state, 'content');
      }

      return state;

    case 'UPDATE_POST':
      return Object(external_lodash_["reduce"])(action.edits, function (result, value, key) {
        if (!result.hasOwnProperty(key)) {
          return result;
        }

        result = getMutateSafeObject(state, result);
        delete result[key];
        return result;
      }, state);

    case 'RESET_POST':
      return INITIAL_EDITS_DEFAULTS;
  }

  return state;
}
/**
 * Reducer returning the last-known state of the current post, in the format
 * returned by the WP REST API.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function currentPost() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SETUP_EDITOR_STATE':
    case 'RESET_POST':
    case 'UPDATE_POST':
      var post;

      if (action.post) {
        post = action.post;
      } else if (action.edits) {
        post = Object(objectSpread["a" /* default */])({}, state, action.edits);
      } else {
        return state;
      }

      return Object(external_lodash_["mapValues"])(post, getPostRawValue);
  }

  return state;
}
/**
 * Reducer returning typing state.
 *
 * @param {boolean} state  Current state.
 * @param {Object}  action Dispatched action.
 *
 * @return {boolean} Updated state.
 */

function isTyping() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'START_TYPING':
      return true;

    case 'STOP_TYPING':
      return false;
  }

  return state;
}
/**
 * Reducer returning whether the caret is within formatted text.
 *
 * @param {boolean} state  Current state.
 * @param {Object}  action Dispatched action.
 *
 * @return {boolean} Updated state.
 */

function isCaretWithinFormattedText() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ENTER_FORMATTED_TEXT':
      return true;

    case 'EXIT_FORMATTED_TEXT':
      return false;
  }

  return state;
}
/**
 * Reducer returning the block selection's state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function blockSelection() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    start: null,
    end: null,
    isMultiSelecting: false,
    isEnabled: true,
    initialPosition: null
  };
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'CLEAR_SELECTED_BLOCK':
      if (state.start === null && state.end === null && !state.isMultiSelecting) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        start: null,
        end: null,
        isMultiSelecting: false,
        initialPosition: null
      });

    case 'START_MULTI_SELECT':
      if (state.isMultiSelecting) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        isMultiSelecting: true,
        initialPosition: null
      });

    case 'STOP_MULTI_SELECT':
      if (!state.isMultiSelecting) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        isMultiSelecting: false,
        initialPosition: null
      });

    case 'MULTI_SELECT':
      return Object(objectSpread["a" /* default */])({}, state, {
        start: action.start,
        end: action.end,
        initialPosition: null
      });

    case 'SELECT_BLOCK':
      if (action.clientId === state.start && action.clientId === state.end) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        start: action.clientId,
        end: action.clientId,
        initialPosition: action.initialPosition
      });

    case 'INSERT_BLOCKS':
      {
        if (action.updateSelection) {
          return Object(objectSpread["a" /* default */])({}, state, {
            start: action.blocks[0].clientId,
            end: action.blocks[0].clientId,
            initialPosition: null,
            isMultiSelecting: false
          });
        }

        return state;
      }

    case 'REMOVE_BLOCKS':
      if (!action.clientIds || !action.clientIds.length || action.clientIds.indexOf(state.start) === -1) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        start: null,
        end: null,
        initialPosition: null,
        isMultiSelecting: false
      });

    case 'REPLACE_BLOCKS':
      if (action.clientIds.indexOf(state.start) === -1) {
        return state;
      } // If there are replacement blocks, assign last block as the next
      // selected block, otherwise set to null.


      var lastBlock = Object(external_lodash_["last"])(action.blocks);
      var nextSelectedBlockClientId = lastBlock ? lastBlock.clientId : null;

      if (nextSelectedBlockClientId === state.start && nextSelectedBlockClientId === state.end) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        start: nextSelectedBlockClientId,
        end: nextSelectedBlockClientId,
        initialPosition: null,
        isMultiSelecting: false
      });

    case 'TOGGLE_SELECTION':
      return Object(objectSpread["a" /* default */])({}, state, {
        isEnabled: action.isSelectionEnabled
      });
  }

  return state;
}
function blocksMode() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  if (action.type === 'TOGGLE_BLOCK_MODE') {
    var clientId = action.clientId;
    return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, clientId, state[clientId] && state[clientId] === 'html' ? 'visual' : 'html'));
  }

  return state;
}
/**
 * Reducer returning the block insertion point visibility, either null if there
 * is not an explicit insertion point assigned, or an object of its `index` and
 * `rootClientId`.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function insertionPoint() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SHOW_INSERTION_POINT':
      var rootClientId = action.rootClientId,
          index = action.index;
      return {
        rootClientId: rootClientId,
        index: index
      };

    case 'HIDE_INSERTION_POINT':
      return null;
  }

  return state;
}
/**
 * Reducer returning whether the post blocks match the defined template or not.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {boolean} Updated state.
 */

function reducer_template() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    isValid: true
  };
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_TEMPLATE_VALIDITY':
      return Object(objectSpread["a" /* default */])({}, state, {
        isValid: action.isValid
      });
  }

  return state;
}
/**
 * Reducer returning the user preferences.
 *
 * @param {Object}  state                 Current state.
 * @param {Object}  action                Dispatched action.
 *
 * @return {string} Updated state.
 */

function preferences() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : PREFERENCES_DEFAULTS;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ENABLE_PUBLISH_SIDEBAR':
      return Object(objectSpread["a" /* default */])({}, state, {
        isPublishSidebarEnabled: true
      });

    case 'DISABLE_PUBLISH_SIDEBAR':
      return Object(objectSpread["a" /* default */])({}, state, {
        isPublishSidebarEnabled: false
      });
  }

  return state;
}
/**
 * Reducer returning current network request state (whether a request to
 * the WP REST API is in progress, successful, or failed).
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function saving() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REQUEST_POST_UPDATE_START':
      return {
        requesting: true,
        successful: false,
        error: null,
        options: action.options || {}
      };

    case 'REQUEST_POST_UPDATE_SUCCESS':
      return {
        requesting: false,
        successful: true,
        error: null,
        options: action.options || {}
      };

    case 'REQUEST_POST_UPDATE_FAILURE':
      return {
        requesting: false,
        successful: false,
        error: action.error,
        options: action.options || {}
      };
  }

  return state;
}
/**
 * Post Lock State.
 *
 * @typedef {Object} PostLockState
 *
 * @property {boolean} isLocked       Whether the post is locked.
 * @property {?boolean} isTakeover     Whether the post editing has been taken over.
 * @property {?boolean} activePostLock Active post lock value.
 * @property {?Object}  user           User that took over the post.
 */

/**
 * Reducer returning the post lock status.
 *
 * @param {PostLockState} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {PostLockState} Updated state.
 */

function postLock() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    isLocked: false
  };
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'UPDATE_POST_LOCK':
      return action.lock;
  }

  return state;
}
/**
 * Post saving lock.
 *
 * When post saving is locked, the post cannot be published or updated.
 *
 * @param {PostSavingLockState} state  Current state.
 * @param {Object}              action Dispatched action.
 *
 * @return {PostLockState} Updated state.
 */

function postSavingLock() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'LOCK_POST_SAVING':
      return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.lockName, true));

    case 'UNLOCK_POST_SAVING':
      return Object(external_lodash_["omit"])(state, action.lockName);
  }

  return state;
}
var reducer_reusableBlocks = Object(external_this_wp_data_["combineReducers"])({
  data: function data() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'RECEIVE_REUSABLE_BLOCKS':
        {
          return Object(external_lodash_["reduce"])(action.results, function (nextState, result) {
            var _result$reusableBlock = result.reusableBlock,
                id = _result$reusableBlock.id,
                title = _result$reusableBlock.title;
            var clientId = result.parsedBlock.clientId;
            var value = {
              clientId: clientId,
              title: title
            };

            if (!Object(external_lodash_["isEqual"])(nextState[id], value)) {
              nextState = getMutateSafeObject(state, nextState);
              nextState[id] = value;
            }

            return nextState;
          }, state);
        }

      case 'UPDATE_REUSABLE_BLOCK_TITLE':
        {
          var id = action.id,
              title = action.title;

          if (!state[id] || state[id].title === title) {
            return state;
          }

          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, id, Object(objectSpread["a" /* default */])({}, state[id], {
            title: title
          })));
        }

      case 'SAVE_REUSABLE_BLOCK_SUCCESS':
        {
          var _id = action.id,
              updatedId = action.updatedId; // If a temporary reusable block is saved, we swap the temporary id with the final one

          if (_id === updatedId) {
            return state;
          }

          var value = state[_id];
          return Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(state, _id), Object(defineProperty["a" /* default */])({}, updatedId, value));
        }

      case 'REMOVE_REUSABLE_BLOCK':
        {
          var _id2 = action.id;
          return Object(external_lodash_["omit"])(state, _id2);
        }
    }

    return state;
  },
  isFetching: function isFetching() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'FETCH_REUSABLE_BLOCKS':
        {
          var id = action.id;

          if (!id) {
            return state;
          }

          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, id, true));
        }

      case 'FETCH_REUSABLE_BLOCKS_SUCCESS':
      case 'FETCH_REUSABLE_BLOCKS_FAILURE':
        {
          var _id3 = action.id;
          return Object(external_lodash_["omit"])(state, _id3);
        }
    }

    return state;
  },
  isSaving: function isSaving() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'SAVE_REUSABLE_BLOCK':
        return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.id, true));

      case 'SAVE_REUSABLE_BLOCK_SUCCESS':
      case 'SAVE_REUSABLE_BLOCK_FAILURE':
        {
          var id = action.id;
          return Object(external_lodash_["omit"])(state, id);
        }
    }

    return state;
  }
});
/**
 * Reducer returning an object where each key is a block client ID, its value
 * representing the settings for its nested blocks.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

var reducer_blockListSettings = function blockListSettings() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    // Even if the replaced blocks have the same client ID, our logic
    // should correct the state.
    case 'REPLACE_BLOCKS':
    case 'REMOVE_BLOCKS':
      {
        return Object(external_lodash_["omit"])(state, action.clientIds);
      }

    case 'UPDATE_BLOCK_LIST_SETTINGS':
      {
        var clientId = action.clientId;

        if (!action.settings) {
          if (state.hasOwnProperty(clientId)) {
            return Object(external_lodash_["omit"])(state, clientId);
          }

          return state;
        }

        if (Object(external_lodash_["isEqual"])(state[clientId], action.settings)) {
          return state;
        }

        return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, clientId, action.settings));
      }
  }

  return state;
};
/**
 * Reducer returning the most recent autosave.
 *
 * @param  {Object} state  The autosave object.
 * @param  {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function autosave() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RESET_AUTOSAVE':
      var post = action.post;

      var _map = ['title', 'excerpt', 'content'].map(function (field) {
        return getPostRawValue(post[field]);
      }),
          _map2 = Object(slicedToArray["a" /* default */])(_map, 3),
          title = _map2[0],
          excerpt = _map2[1],
          content = _map2[2];

      return {
        title: title,
        excerpt: excerpt,
        content: content
      };
  }

  return state;
}
/**
 * Reducer returning the post preview link.
 *
 * @param {string?} state  The preview link
 * @param {Object}  action Dispatched action.
 *
 * @return {string?} Updated state.
 */

function reducer_previewLink() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REQUEST_POST_UPDATE_SUCCESS':
      if (action.post.preview_link) {
        return action.post.preview_link;
      } else if (action.post.link) {
        return Object(external_this_wp_url_["addQueryArgs"])(action.post.link, {
          preview: true
        });
      }

      return state;

    case 'REQUEST_POST_UPDATE_START':
      // Invalidate known preview link when autosave starts.
      if (state && action.options.isPreview) {
        return null;
      }

      break;
  }

  return state;
}
/**
 * Reducer returning whether the editor is ready to be rendered.
 * The editor is considered ready to be rendered once
 * the post object is loaded properly and the initial blocks parsed.
 *
 * @param {boolean} state
 * @param {Object} action
 *
 * @return {boolean} Updated state.
 */

function reducer_isReady() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SETUP_EDITOR_STATE':
      return true;
  }

  return state;
}
/**
 * Reducer returning the post editor setting.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer_editorSettings() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : EDITOR_SETTINGS_DEFAULTS;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'UPDATE_EDITOR_SETTINGS':
      return Object(objectSpread["a" /* default */])({}, state, action.settings);
  }

  return state;
}
/* harmony default export */ var store_reducer = (redux_optimist_default()(Object(external_this_wp_data_["combineReducers"])({
  editor: editor,
  initialEdits: initialEdits,
  currentPost: currentPost,
  preferences: preferences,
  saving: saving,
  postLock: postLock,
  reusableBlocks: reducer_reusableBlocks,
  template: reducer_template,
  autosave: autosave,
  previewLink: reducer_previewLink,
  postSavingLock: postSavingLock,
  isReady: reducer_isReady,
  editorSettings: reducer_editorSettings
})));

// EXTERNAL MODULE: ./node_modules/refx/refx.js
var refx = __webpack_require__("gQxa");
var refx_default = /*#__PURE__*/__webpack_require__.n(refx);

// EXTERNAL MODULE: ./node_modules/redux-multi/lib/index.js
var lib = __webpack_require__("d2gM");
var lib_default = /*#__PURE__*/__webpack_require__.n(lib);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/regenerator/index.js
var regenerator = __webpack_require__("o0o1");
var regenerator_default = /*#__PURE__*/__webpack_require__.n(regenerator);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__("ywyh");
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/controls.js


/**
 * WordPress dependencies
 */


/**
 * Dispatches a control action for triggering an api fetch call.
 *
 * @param {Object} request Arguments for the fetch request.
 *
 * @return {Object} control descriptor.
 */

function apiFetch(request) {
  return {
    type: 'API_FETCH',
    request: request
  };
}
/**
 * Dispatches a control action for triggering a registry select.
 *
 * @param {string} storeKey
 * @param {string} selectorName
 * @param {Array}  args Arguments for the select.
 *
 * @return {Object} control descriptor.
 */

function controls_select(storeKey, selectorName) {
  for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  return {
    type: 'SELECT',
    storeKey: storeKey,
    selectorName: selectorName,
    args: args
  };
}
/**
 * Dispatches a control action for triggering a registry select that has a
 * resolver.
 *
 * @param {string}  storeKey
 * @param {string}  selectorName
 * @param {Array}   args  Arguments for the select.
 *
 * @return {Object} control descriptor.
 */

function resolveSelect(storeKey, selectorName) {
  for (var _len2 = arguments.length, args = new Array(_len2 > 2 ? _len2 - 2 : 0), _key2 = 2; _key2 < _len2; _key2++) {
    args[_key2 - 2] = arguments[_key2];
  }

  return {
    type: 'RESOLVE_SELECT',
    storeKey: storeKey,
    selectorName: selectorName,
    args: args
  };
}
/**
 * Dispatches a control action for triggering a registry dispatch.
 *
 * @param {string} storeKey
 * @param {string} actionName
 * @param {Array} args  Arguments for the dispatch action.
 *
 * @return {Object}  control descriptor.
 */

function controls_dispatch(storeKey, actionName) {
  for (var _len3 = arguments.length, args = new Array(_len3 > 2 ? _len3 - 2 : 0), _key3 = 2; _key3 < _len3; _key3++) {
    args[_key3 - 2] = arguments[_key3];
  }

  return {
    type: 'DISPATCH',
    storeKey: storeKey,
    actionName: actionName,
    args: args
  };
}
/* harmony default export */ var controls = ({
  API_FETCH: function API_FETCH(_ref) {
    var request = _ref.request;
    return external_this_wp_apiFetch_default()(request);
  },
  SELECT: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref2) {
      var _registry$select;

      var storeKey = _ref2.storeKey,
          selectorName = _ref2.selectorName,
          args = _ref2.args;
      return (_registry$select = registry.select(storeKey))[selectorName].apply(_registry$select, Object(toConsumableArray["a" /* default */])(args));
    };
  }),
  DISPATCH: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref3) {
      var _registry$dispatch;

      var storeKey = _ref3.storeKey,
          actionName = _ref3.actionName,
          args = _ref3.args;
      return (_registry$dispatch = registry.dispatch(storeKey))[actionName].apply(_registry$dispatch, Object(toConsumableArray["a" /* default */])(args));
    };
  }),
  RESOLVE_SELECT: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref4) {
      var storeKey = _ref4.storeKey,
          selectorName = _ref4.selectorName,
          args = _ref4.args;
      return new Promise(function (resolve) {
        var hasFinished = function hasFinished() {
          return registry.select('core/data').hasFinishedResolution(storeKey, selectorName, args);
        };

        var getResult = function getResult() {
          return registry.select(storeKey)[selectorName].apply(null, args);
        }; // trigger the selector (to trigger the resolver)


        var result = getResult();

        if (hasFinished()) {
          return resolve(result);
        }

        var unsubscribe = registry.subscribe(function () {
          if (hasFinished()) {
            unsubscribe();
            resolve(getResult());
          }
        });
      });
    };
  })
});

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/utils/notice-builder.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * External dependencies
 */


/**
 * Builds the arguments for a success notification dispatch.
 *
 * @param {Object} data Incoming data to build the arguments from.
 *
 * @return {Array} Arguments for dispatch. An empty array signals no
 *                 notification should be sent.
 */

function getNotificationArgumentsForSaveSuccess(data) {
  var previousPost = data.previousPost,
      post = data.post,
      postType = data.postType; // Autosaves are neither shown a notice nor redirected.

  if (Object(external_lodash_["get"])(data.options, ['isAutosave'])) {
    return [];
  }

  var publishStatus = ['publish', 'private', 'future'];
  var isPublished = Object(external_lodash_["includes"])(publishStatus, previousPost.status);
  var willPublish = Object(external_lodash_["includes"])(publishStatus, post.status);
  var noticeMessage;
  var shouldShowLink = Object(external_lodash_["get"])(postType, ['viewable'], false);

  if (!isPublished && !willPublish) {
    // If saving a non-published post, don't show notice.
    noticeMessage = null;
  } else if (isPublished && !willPublish) {
    // If undoing publish status, show specific notice
    noticeMessage = postType.labels.item_reverted_to_draft;
    shouldShowLink = false;
  } else if (!isPublished && willPublish) {
    // If publishing or scheduling a post, show the corresponding
    // publish message
    noticeMessage = {
      publish: postType.labels.item_published,
      private: postType.labels.item_published_privately,
      future: postType.labels.item_scheduled
    }[post.status];
  } else {
    // Generic fallback notice
    noticeMessage = postType.labels.item_updated;
  }

  if (noticeMessage) {
    var actions = [];

    if (shouldShowLink) {
      actions.push({
        label: postType.labels.view_item,
        url: post.link
      });
    }

    return [noticeMessage, {
      id: SAVE_POST_NOTICE_ID,
      actions: actions
    }];
  }

  return [];
}
/**
 * Builds the fail notification arguments for dispatch.
 *
 * @param {Object} data Incoming data to build the arguments with.
 *
 * @return {Array} Arguments for dispatch. An empty array signals no
 *                 notification should be sent.
 */

function getNotificationArgumentsForSaveFail(data) {
  var post = data.post,
      edits = data.edits,
      error = data.error;

  if (error && 'rest_autosave_no_changes' === error.code) {
    // Autosave requested a new autosave, but there were no changes. This shouldn't
    // result in an error notice for the user.
    return [];
  }

  var publishStatus = ['publish', 'private', 'future'];
  var isPublished = publishStatus.indexOf(post.status) !== -1; // If the post was being published, we show the corresponding publish error message
  // Unless we publish an "updating failed" message

  var messages = {
    publish: Object(external_this_wp_i18n_["__"])('Publishing failed'),
    private: Object(external_this_wp_i18n_["__"])('Publishing failed'),
    future: Object(external_this_wp_i18n_["__"])('Scheduling failed')
  };
  var noticeMessage = !isPublished && publishStatus.indexOf(edits.status) !== -1 ? messages[edits.status] : Object(external_this_wp_i18n_["__"])('Updating failed');
  return [noticeMessage, {
    id: SAVE_POST_NOTICE_ID
  }];
}
/**
 * Builds the trash fail notification arguments for dispatch.
 *
 * @param {Object} data
 *
 * @return {Array} Arguments for dispatch.
 */

function getNotificationArgumentsForTrashFail(data) {
  return [data.error.message && data.error.code !== 'unknown_error' ? data.error.message : Object(external_this_wp_i18n_["__"])('Trashing failed'), {
    id: TRASH_POST_NOTICE_ID
  }];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/actions.js




var _marked =
/*#__PURE__*/
regenerator_default.a.mark(savePost),
    _marked2 =
/*#__PURE__*/
regenerator_default.a.mark(refreshPost),
    _marked3 =
/*#__PURE__*/
regenerator_default.a.mark(trashPost),
    _marked4 =
/*#__PURE__*/
regenerator_default.a.mark(actions_autosave);

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */




/**
 * Returns an action object used in signalling that editor has initialized with
 * the specified post object and editor settings.
 *
 * @param {Object} post      Post object.
 * @param {Object} edits     Initial edited attributes object.
 * @param {Array?} template  Block Template.
 *
 * @return {Object} Action object.
 */

function setupEditor(post, edits, template) {
  return {
    type: 'SETUP_EDITOR',
    post: post,
    edits: edits,
    template: template
  };
}
/**
 * Returns an action object used in signalling that the latest version of the
 * post has been received, either by initialization or save.
 *
 * @param {Object} post Post object.
 *
 * @return {Object} Action object.
 */

function resetPost(post) {
  return {
    type: 'RESET_POST',
    post: post
  };
}
/**
 * Returns an action object used in signalling that the latest autosave of the
 * post has been received, by initialization or autosave.
 *
 * @param {Object} post Autosave post object.
 *
 * @return {Object} Action object.
 */

function resetAutosave(post) {
  return {
    type: 'RESET_AUTOSAVE',
    post: post
  };
}
/**
 * Optimistic action for dispatching that a post update request has started.
 *
 * @param {Object} options
 *
 * @return {Object} An action object
 */

function __experimentalRequestPostUpdateStart() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return {
    type: 'REQUEST_POST_UPDATE_START',
    optimist: {
      type: redux_optimist["BEGIN"],
      id: POST_UPDATE_TRANSACTION_ID
    },
    options: options
  };
}
/**
 * Optimistic action for indicating that the request post update has completed
 * successfully.
 *
 * @param {Object}  data                The data for the action.
 * @param {Object}  data.previousPost   The previous post prior to update.
 * @param {Object}  data.post           The new post after update
 * @param {boolean} data.isRevision     Whether the post is a revision or not.
 * @param {Object}  data.options        Options passed through from the original
 *                                      action dispatch.
 * @param {Object}  data.postType       The post type object.
 *
 * @return {Object}	Action object.
 */

function __experimentalRequestPostUpdateSuccess(_ref) {
  var previousPost = _ref.previousPost,
      post = _ref.post,
      isRevision = _ref.isRevision,
      options = _ref.options,
      postType = _ref.postType;
  return {
    type: 'REQUEST_POST_UPDATE_SUCCESS',
    previousPost: previousPost,
    post: post,
    optimist: {
      // Note: REVERT is not a failure case here. Rather, it
      // is simply reversing the assumption that the updates
      // were applied to the post proper, such that the post
      // treated as having unsaved changes.
      type: isRevision ? redux_optimist["REVERT"] : redux_optimist["COMMIT"],
      id: POST_UPDATE_TRANSACTION_ID
    },
    options: options,
    postType: postType
  };
}
/**
 * Optimistic action for indicating that the request post update has completed
 * with a failure.
 *
 * @param {Object}  data          The data for the action
 * @param {Object}  data.post     The post that failed updating.
 * @param {Object}  data.edits    The fields that were being updated.
 * @param {*}       data.error    The error from the failed call.
 * @param {Object}  data.options  Options passed through from the original
 *                                action dispatch.
 * @return {Object} An action object
 */

function __experimentalRequestPostUpdateFailure(_ref2) {
  var post = _ref2.post,
      edits = _ref2.edits,
      error = _ref2.error,
      options = _ref2.options;
  return {
    type: 'REQUEST_POST_UPDATE_FAILURE',
    optimist: {
      type: redux_optimist["REVERT"],
      id: POST_UPDATE_TRANSACTION_ID
    },
    post: post,
    edits: edits,
    error: error,
    options: options
  };
}
/**
 * Returns an action object used in signalling that a patch of updates for the
 * latest version of the post have been received.
 *
 * @param {Object} edits Updated post fields.
 *
 * @return {Object} Action object.
 */

function updatePost(edits) {
  return {
    type: 'UPDATE_POST',
    edits: edits
  };
}
/**
 * Returns an action object used to setup the editor state when first opening
 * an editor.
 *
 * @param {Object} post   Post object.
 *
 * @return {Object} Action object.
 */

function setupEditorState(post) {
  return {
    type: 'SETUP_EDITOR_STATE',
    post: post
  };
}
/**
 * Returns an action object used in signalling that attributes of the post have
 * been edited.
 *
 * @param {Object} edits Post attributes to edit.
 *
 * @return {Object} Action object.
 */

function actions_editPost(edits) {
  return {
    type: 'EDIT_POST',
    edits: edits
  };
}
/**
 * Returns action object produced by the updatePost creator augmented by
 * an optimist option that signals optimistically applying updates.
 *
 * @param {Object} edits  Updated post fields.
 *
 * @return {Object} Action object.
 */

function __experimentalOptimisticUpdatePost(edits) {
  return Object(objectSpread["a" /* default */])({}, updatePost(edits), {
    optimist: {
      id: POST_UPDATE_TRANSACTION_ID
    }
  });
}
/**
 * Action generator for saving the current post in the editor.
 *
 * @param {Object} options
 */

function savePost() {
  var options,
      isEditedPostSaveable,
      edits,
      isAutosave,
      isEditedPostNew,
      post,
      editedPostContent,
      toSend,
      currentPostType,
      postType,
      path,
      method,
      autoSavePost,
      newPost,
      resetAction,
      notifySuccessArgs,
      notifyFailArgs,
      _args = arguments;
  return regenerator_default.a.wrap(function savePost$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          options = _args.length > 0 && _args[0] !== undefined ? _args[0] : {};
          _context.next = 3;
          return controls_select(STORE_KEY, 'isEditedPostSaveable');

        case 3:
          isEditedPostSaveable = _context.sent;

          if (isEditedPostSaveable) {
            _context.next = 6;
            break;
          }

          return _context.abrupt("return");

        case 6:
          _context.next = 8;
          return controls_select(STORE_KEY, 'getPostEdits');

        case 8:
          edits = _context.sent;
          isAutosave = !!options.isAutosave;

          if (isAutosave) {
            edits = Object(external_lodash_["pick"])(edits, ['title', 'content', 'excerpt']);
          }

          _context.next = 13;
          return controls_select(STORE_KEY, 'isEditedPostNew');

        case 13:
          isEditedPostNew = _context.sent;

          // New posts (with auto-draft status) must be explicitly assigned draft
          // status if there is not already a status assigned in edits (publish).
          // Otherwise, they are wrongly left as auto-draft. Status is not always
          // respected for autosaves, so it cannot simply be included in the pick
          // above. This behavior relies on an assumption that an auto-draft post
          // would never be saved by anyone other than the owner of the post, per
          // logic within autosaves REST controller to save status field only for
          // draft/auto-draft by current user.
          //
          // See: https://core.trac.wordpress.org/ticket/43316#comment:88
          // See: https://core.trac.wordpress.org/ticket/43316#comment:89
          if (isEditedPostNew) {
            edits = Object(objectSpread["a" /* default */])({
              status: 'draft'
            }, edits);
          }

          _context.next = 17;
          return controls_select(STORE_KEY, 'getCurrentPost');

        case 17:
          post = _context.sent;
          _context.next = 20;
          return controls_select(STORE_KEY, 'getEditedPostContent');

        case 20:
          editedPostContent = _context.sent;
          toSend = Object(objectSpread["a" /* default */])({}, edits, {
            content: editedPostContent,
            id: post.id
          });
          _context.next = 24;
          return controls_select(STORE_KEY, 'getCurrentPostType');

        case 24:
          currentPostType = _context.sent;
          _context.next = 27;
          return resolveSelect('core', 'getPostType', currentPostType);

        case 27:
          postType = _context.sent;
          _context.next = 30;
          return controls_dispatch(STORE_KEY, '__experimentalRequestPostUpdateStart', options);

        case 30:
          _context.next = 32;
          return controls_dispatch(STORE_KEY, '__experimentalOptimisticUpdatePost', toSend);

        case 32:
          path = "/wp/v2/".concat(postType.rest_base, "/").concat(post.id);
          method = 'PUT';

          if (!isAutosave) {
            _context.next = 43;
            break;
          }

          _context.next = 37;
          return controls_select(STORE_KEY, 'getAutosave');

        case 37:
          autoSavePost = _context.sent;
          // Ensure autosaves contain all expected fields, using autosave or
          // post values as fallback if not otherwise included in edits.
          toSend = Object(objectSpread["a" /* default */])({}, Object(external_lodash_["pick"])(post, ['title', 'content', 'excerpt']), autoSavePost, toSend);
          path += '/autosaves';
          method = 'POST';
          _context.next = 47;
          break;

        case 43:
          _context.next = 45;
          return controls_dispatch('core/notices', 'removeNotice', SAVE_POST_NOTICE_ID);

        case 45:
          _context.next = 47;
          return controls_dispatch('core/notices', 'removeNotice', 'autosave-exists');

        case 47:
          _context.prev = 47;
          _context.next = 50;
          return apiFetch({
            path: path,
            method: method,
            data: toSend
          });

        case 50:
          newPost = _context.sent;
          resetAction = isAutosave ? 'resetAutosave' : 'resetPost';
          _context.next = 54;
          return controls_dispatch(STORE_KEY, resetAction, newPost);

        case 54:
          _context.next = 56;
          return controls_dispatch(STORE_KEY, '__experimentalRequestPostUpdateSuccess', {
            previousPost: post,
            post: newPost,
            options: options,
            postType: postType,
            // An autosave may be processed by the server as a regular save
            // when its update is requested by the author and the post was
            // draft or auto-draft.
            isRevision: newPost.id !== post.id
          });

        case 56:
          notifySuccessArgs = getNotificationArgumentsForSaveSuccess({
            previousPost: post,
            post: newPost,
            postType: postType,
            options: options
          });

          if (!(notifySuccessArgs.length > 0)) {
            _context.next = 60;
            break;
          }

          _context.next = 60;
          return controls_dispatch.apply(void 0, ['core/notices', 'createSuccessNotice'].concat(Object(toConsumableArray["a" /* default */])(notifySuccessArgs)));

        case 60:
          _context.next = 70;
          break;

        case 62:
          _context.prev = 62;
          _context.t0 = _context["catch"](47);
          _context.next = 66;
          return controls_dispatch(STORE_KEY, '__experimentalRequestPostUpdateFailure', {
            post: post,
            edits: edits,
            error: _context.t0,
            options: options
          });

        case 66:
          notifyFailArgs = getNotificationArgumentsForSaveFail({
            post: post,
            edits: edits,
            error: _context.t0
          });

          if (!(notifyFailArgs.length > 0)) {
            _context.next = 70;
            break;
          }

          _context.next = 70;
          return controls_dispatch.apply(void 0, ['core/notices', 'createErrorNotice'].concat(Object(toConsumableArray["a" /* default */])(notifyFailArgs)));

        case 70:
        case "end":
          return _context.stop();
      }
    }
  }, _marked, this, [[47, 62]]);
}
/**
 * Action generator for handling refreshing the current post.
 */

function refreshPost() {
  var post, postTypeSlug, postType, newPost;
  return regenerator_default.a.wrap(function refreshPost$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.next = 2;
          return controls_select(STORE_KEY, 'getCurrentPost');

        case 2:
          post = _context2.sent;
          _context2.next = 5;
          return controls_select(STORE_KEY, 'getCurrentPostType');

        case 5:
          postTypeSlug = _context2.sent;
          _context2.next = 8;
          return resolveSelect('core', 'getPostType', postTypeSlug);

        case 8:
          postType = _context2.sent;
          _context2.next = 11;
          return apiFetch({
            // Timestamp arg allows caller to bypass browser caching, which is
            // expected for this specific function.
            path: "/wp/v2/".concat(postType.rest_base, "/").concat(post.id) + "?context=edit&_timestamp=".concat(Date.now())
          });

        case 11:
          newPost = _context2.sent;
          _context2.next = 14;
          return controls_dispatch(STORE_KEY, 'resetPost', newPost);

        case 14:
        case "end":
          return _context2.stop();
      }
    }
  }, _marked2, this);
}
/**
 * Action generator for trashing the current post in the editor.
 */

function trashPost() {
  var postTypeSlug, postType, post;
  return regenerator_default.a.wrap(function trashPost$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return controls_select(STORE_KEY, 'getCurrentPostType');

        case 2:
          postTypeSlug = _context3.sent;
          _context3.next = 5;
          return resolveSelect('core', 'getPostType', postTypeSlug);

        case 5:
          postType = _context3.sent;
          _context3.next = 8;
          return controls_dispatch('core/notices', 'removeNotice', TRASH_POST_NOTICE_ID);

        case 8:
          _context3.prev = 8;
          _context3.next = 11;
          return controls_select(STORE_KEY, 'getCurrentPost');

        case 11:
          post = _context3.sent;
          _context3.next = 14;
          return apiFetch({
            path: "/wp/v2/".concat(postType.rest_base, "/").concat(post.id),
            method: 'DELETE'
          });

        case 14:
          _context3.next = 16;
          return controls_dispatch(STORE_KEY, 'resetPost', Object(objectSpread["a" /* default */])({}, post, {
            status: 'trash'
          }));

        case 16:
          _context3.next = 22;
          break;

        case 18:
          _context3.prev = 18;
          _context3.t0 = _context3["catch"](8);
          _context3.next = 22;
          return controls_dispatch.apply(void 0, ['core/notices', 'createErrorNotice'].concat(Object(toConsumableArray["a" /* default */])(getNotificationArgumentsForTrashFail({
            error: _context3.t0
          }))));

        case 22:
        case "end":
          return _context3.stop();
      }
    }
  }, _marked3, this, [[8, 18]]);
}
/**
 * Action generator used in signalling that the post should autosave.
 *
 * @param {Object?} options Extra flags to identify the autosave.
 */

function actions_autosave(options) {
  return regenerator_default.a.wrap(function autosave$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          _context4.next = 2;
          return controls_dispatch(STORE_KEY, 'savePost', Object(objectSpread["a" /* default */])({
            isAutosave: true
          }, options));

        case 2:
        case "end":
          return _context4.stop();
      }
    }
  }, _marked4, this);
}
/**
 * Returns an action object used in signalling that undo history should
 * restore last popped state.
 *
 * @return {Object} Action object.
 */

function actions_redo() {
  return {
    type: 'REDO'
  };
}
/**
 * Returns an action object used in signalling that undo history should pop.
 *
 * @return {Object} Action object.
 */

function actions_undo() {
  return {
    type: 'UNDO'
  };
}
/**
 * Returns an action object used in signalling that undo history record should
 * be created.
 *
 * @return {Object} Action object.
 */

function createUndoLevel() {
  return {
    type: 'CREATE_UNDO_LEVEL'
  };
}
/**
 * Returns an action object used to lock the editor.
 *
 * @param {Object}  lock Details about the post lock status, user, and nonce.
 *
 * @return {Object} Action object.
 */

function updatePostLock(lock) {
  return {
    type: 'UPDATE_POST_LOCK',
    lock: lock
  };
}
/**
 * Returns an action object used to fetch a single reusable block or all
 * reusable blocks from the REST API into the store.
 *
 * @param {?string} id If given, only a single reusable block with this ID will
 *                     be fetched.
 *
 * @return {Object} Action object.
 */

function __experimentalFetchReusableBlocks(id) {
  return {
    type: 'FETCH_REUSABLE_BLOCKS',
    id: id
  };
}
/**
 * Returns an action object used in signalling that reusable blocks have been
 * received. `results` is an array of objects containing:
 *  - `reusableBlock` - Details about how the reusable block is persisted.
 *  - `parsedBlock` - The original block.
 *
 * @param {Object[]} results Reusable blocks received.
 *
 * @return {Object} Action object.
 */

function __experimentalReceiveReusableBlocks(results) {
  return {
    type: 'RECEIVE_REUSABLE_BLOCKS',
    results: results
  };
}
/**
 * Returns an action object used to save a reusable block that's in the store to
 * the REST API.
 *
 * @param {Object} id The ID of the reusable block to save.
 *
 * @return {Object} Action object.
 */

function __experimentalSaveReusableBlock(id) {
  return {
    type: 'SAVE_REUSABLE_BLOCK',
    id: id
  };
}
/**
 * Returns an action object used to delete a reusable block via the REST API.
 *
 * @param {number} id The ID of the reusable block to delete.
 *
 * @return {Object} Action object.
 */

function __experimentalDeleteReusableBlock(id) {
  return {
    type: 'DELETE_REUSABLE_BLOCK',
    id: id
  };
}
/**
 * Returns an action object used in signalling that a reusable block's title is
 * to be updated.
 *
 * @param {number} id    The ID of the reusable block to update.
 * @param {string} title The new title.
 *
 * @return {Object} Action object.
 */

function __experimentalUpdateReusableBlockTitle(id, title) {
  return {
    type: 'UPDATE_REUSABLE_BLOCK_TITLE',
    id: id,
    title: title
  };
}
/**
 * Returns an action object used to convert a reusable block into a static
 * block.
 *
 * @param {string} clientId The client ID of the block to attach.
 *
 * @return {Object} Action object.
 */

function __experimentalConvertBlockToStatic(clientId) {
  return {
    type: 'CONVERT_BLOCK_TO_STATIC',
    clientId: clientId
  };
}
/**
 * Returns an action object used to convert a static block into a reusable
 * block.
 *
 * @param {string} clientIds The client IDs of the block to detach.
 *
 * @return {Object} Action object.
 */

function __experimentalConvertBlockToReusable(clientIds) {
  return {
    type: 'CONVERT_BLOCK_TO_REUSABLE',
    clientIds: Object(external_lodash_["castArray"])(clientIds)
  };
}
/**
 * Returns an action object used in signalling that the user has enabled the
 * publish sidebar.
 *
 * @return {Object} Action object
 */

function enablePublishSidebar() {
  return {
    type: 'ENABLE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user has disabled the
 * publish sidebar.
 *
 * @return {Object} Action object
 */

function disablePublishSidebar() {
  return {
    type: 'DISABLE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used to signal that post saving is locked.
 *
 * @param  {string} lockName The lock name.
 *
 * @return {Object} Action object
 */

function lockPostSaving(lockName) {
  return {
    type: 'LOCK_POST_SAVING',
    lockName: lockName
  };
}
/**
 * Returns an action object used to signal that post saving is unlocked.
 *
 * @param  {string} lockName The lock name.
 *
 * @return {Object} Action object
 */

function unlockPostSaving(lockName) {
  return {
    type: 'UNLOCK_POST_SAVING',
    lockName: lockName
  };
}
/**
 * Returns an action object used to signal that the blocks have been updated.
 *
 * @param {Array}   blocks  Block Array.
 * @param {?Object} options Optional options.
 *
 * @return {Object} Action object
 */

function actions_resetEditorBlocks(blocks) {
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  return {
    type: 'RESET_EDITOR_BLOCKS',
    blocks: blocks,
    shouldCreateUndoLevel: options.__unstableShouldCreateUndoLevel !== false
  };
}
/*
 * Returns an action object used in signalling that the post editor settings have been updated.
 *
 * @param {Object} settings Updated settings
 *
 * @return {Object} Action object
 */

function updateEditorSettings(settings) {
  return {
    type: 'UPDATE_EDITOR_SETTINGS',
    settings: settings
  };
}
/**
 * Backward compatibility
 */

var actions_getBlockEditorAction = function getBlockEditorAction(name) {
  return (
    /*#__PURE__*/
    regenerator_default.a.mark(function _callee() {
      var _len,
          args,
          _key,
          _args5 = arguments;

      return regenerator_default.a.wrap(function _callee$(_context5) {
        while (1) {
          switch (_context5.prev = _context5.next) {
            case 0:
              for (_len = _args5.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
                args[_key] = _args5[_key];
              }

              _context5.next = 3;
              return controls_dispatch.apply(void 0, ['core/block-editor', name].concat(args));

            case 3:
            case "end":
              return _context5.stop();
          }
        }
      }, _callee, this);
    })
  );
};

var resetBlocks = actions_getBlockEditorAction('resetBlocks');
var receiveBlocks = actions_getBlockEditorAction('receiveBlocks');
var updateBlock = actions_getBlockEditorAction('updateBlock');
var updateBlockAttributes = actions_getBlockEditorAction('updateBlockAttributes');
var selectBlock = actions_getBlockEditorAction('selectBlock');
var startMultiSelect = actions_getBlockEditorAction('startMultiSelect');
var stopMultiSelect = actions_getBlockEditorAction('stopMultiSelect');
var multiSelect = actions_getBlockEditorAction('multiSelect');
var clearSelectedBlock = actions_getBlockEditorAction('clearSelectedBlock');
var toggleSelection = actions_getBlockEditorAction('toggleSelection');
var replaceBlocks = actions_getBlockEditorAction('replaceBlocks');
var replaceBlock = actions_getBlockEditorAction('replaceBlock');
var moveBlocksDown = actions_getBlockEditorAction('moveBlocksDown');
var moveBlocksUp = actions_getBlockEditorAction('moveBlocksUp');
var moveBlockToPosition = actions_getBlockEditorAction('moveBlockToPosition');
var insertBlock = actions_getBlockEditorAction('insertBlock');
var insertBlocks = actions_getBlockEditorAction('insertBlocks');
var showInsertionPoint = actions_getBlockEditorAction('showInsertionPoint');
var hideInsertionPoint = actions_getBlockEditorAction('hideInsertionPoint');
var setTemplateValidity = actions_getBlockEditorAction('setTemplateValidity');
var synchronizeTemplate = actions_getBlockEditorAction('synchronizeTemplate');
var mergeBlocks = actions_getBlockEditorAction('mergeBlocks');
var removeBlocks = actions_getBlockEditorAction('removeBlocks');
var removeBlock = actions_getBlockEditorAction('removeBlock');
var toggleBlockMode = actions_getBlockEditorAction('toggleBlockMode');
var startTyping = actions_getBlockEditorAction('startTyping');
var stopTyping = actions_getBlockEditorAction('stopTyping');
var enterFormattedText = actions_getBlockEditorAction('enterFormattedText');
var exitFormattedText = actions_getBlockEditorAction('exitFormattedText');
var insertDefaultBlock = actions_getBlockEditorAction('insertDefaultBlock');
var updateBlockListSettings = actions_getBlockEditorAction('updateBlockListSettings');

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js
var asyncToGenerator = __webpack_require__("HaE+");

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// EXTERNAL MODULE: external {"this":["wp","date"]}
var external_this_wp_date_ = __webpack_require__("FqII");

// EXTERNAL MODULE: external {"this":["wp","autop"]}
var external_this_wp_autop_ = __webpack_require__("UuzZ");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/selectors.js



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
 * Shared reference to an empty object for cases where it is important to avoid
 * returning a new object reference on every invocation, as in a connected or
 * other pure component which performs `shouldComponentUpdate` check on props.
 * This should be used as a last resort, since the normalized data should be
 * maintained by the reducer result in state.
 */

var EMPTY_OBJECT = {};
/**
 * Returns true if any past editor history snapshots exist, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether undo history exists.
 */

function hasEditorUndo(state) {
  return state.editor.past.length > 0;
}
/**
 * Returns true if any future editor history snapshots exist, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether redo history exists.
 */

function hasEditorRedo(state) {
  return state.editor.future.length > 0;
}
/**
 * Returns true if the currently edited post is yet to be saved, or false if
 * the post has been saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post is new.
 */

function selectors_isEditedPostNew(state) {
  return selectors_getCurrentPost(state).status === 'auto-draft';
}
/**
 * Returns true if content includes unsaved changes, or false otherwise.
 *
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether content includes unsaved changes.
 */

function hasChangedContent(state) {
  return state.editor.present.blocks.isDirty || // `edits` is intended to contain only values which are different from
  // the saved post, so the mere presence of a property is an indicator
  // that the value is different than what is known to be saved. While
  // content in Visual mode is represented by the blocks state, in Text
  // mode it is tracked by `edits.content`.
  'content' in state.editor.present.edits;
}
/**
 * Returns true if there are unsaved values for the current edit session, or
 * false if the editing state matches the saved or new post.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether unsaved values exist.
 */

function selectors_isEditedPostDirty(state) {
  if (hasChangedContent(state)) {
    return true;
  } // Edits should contain only fields which differ from the saved post (reset
  // at initial load and save complete). Thus, a non-empty edits state can be
  // inferred to contain unsaved values.


  if (Object.keys(state.editor.present.edits).length > 0) {
    return true;
  } // Edits and change detection are reset at the start of a save, but a post
  // is still considered dirty until the point at which the save completes.
  // Because the save is performed optimistically, the prior states are held
  // until committed. These can be referenced to determine whether there's a
  // chance that state may be reverted into one considered dirty.


  return inSomeHistory(state, selectors_isEditedPostDirty);
}
/**
 * Returns true if there are no unsaved values for the current edit session and
 * if the currently edited post is new (has never been saved before).
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether new post and unsaved values exist.
 */

function selectors_isCleanNewPost(state) {
  return !selectors_isEditedPostDirty(state) && selectors_isEditedPostNew(state);
}
/**
 * Returns the post currently being edited in its last known saved state, not
 * including unsaved edits. Returns an object containing relevant default post
 * values if the post has not yet been saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Post object.
 */

function selectors_getCurrentPost(state) {
  return state.currentPost;
}
/**
 * Returns the post type of the post currently being edited.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Post type.
 */

function selectors_getCurrentPostType(state) {
  return state.currentPost.type;
}
/**
 * Returns the ID of the post currently being edited, or null if the post has
 * not yet been saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {?number} ID of current post.
 */

function selectors_getCurrentPostId(state) {
  return selectors_getCurrentPost(state).id || null;
}
/**
 * Returns the number of revisions of the post currently being edited.
 *
 * @param {Object} state Global application state.
 *
 * @return {number} Number of revisions.
 */

function getCurrentPostRevisionsCount(state) {
  return Object(external_lodash_["get"])(selectors_getCurrentPost(state), ['_links', 'version-history', 0, 'count'], 0);
}
/**
 * Returns the last revision ID of the post currently being edited,
 * or null if the post has no revisions.
 *
 * @param {Object} state Global application state.
 *
 * @return {?number} ID of the last revision.
 */

function getCurrentPostLastRevisionId(state) {
  return Object(external_lodash_["get"])(selectors_getCurrentPost(state), ['_links', 'predecessor-version', 0, 'id'], null);
}
/**
 * Returns any post values which have been changed in the editor but not yet
 * been saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Object of key value pairs comprising unsaved edits.
 */

var getPostEdits = Object(rememo["a" /* default */])(function (state) {
  return Object(objectSpread["a" /* default */])({}, state.initialEdits, state.editor.present.edits);
}, function (state) {
  return [state.editor.present.edits, state.initialEdits];
});
/**
 * Returns a new reference when edited values have changed. This is useful in
 * inferring where an edit has been made between states by comparison of the
 * return values using strict equality.
 *
 * @example
 *
 * ```
 * const hasEditOccurred = (
 *    getReferenceByDistinctEdits( beforeState ) !==
 *    getReferenceByDistinctEdits( afterState )
 * );
 * ```
 *
 * @param {Object} state Editor state.
 *
 * @return {*} A value whose reference will change only when an edit occurs.
 */

var getReferenceByDistinctEdits = Object(rememo["a" /* default */])(function () {
  return [];
}, function (state) {
  return [state.editor];
});
/**
 * Returns an attribute value of the saved post.
 *
 * @param {Object} state         Global application state.
 * @param {string} attributeName Post attribute name.
 *
 * @return {*} Post attribute value.
 */

function selectors_getCurrentPostAttribute(state, attributeName) {
  var post = selectors_getCurrentPost(state);

  if (post.hasOwnProperty(attributeName)) {
    return post[attributeName];
  }
}
/**
 * Returns a single attribute of the post being edited, preferring the unsaved
 * edit if one exists, but merging with the attribute value for the last known
 * saved state of the post (this is needed for some nested attributes like meta).
 *
 * @param {Object} state         Global application state.
 * @param {string} attributeName Post attribute name.
 *
 * @return {*} Post attribute value.
 */

var getNestedEditedPostProperty = Object(rememo["a" /* default */])(function (state, attributeName) {
  var edits = getPostEdits(state);

  if (!edits.hasOwnProperty(attributeName)) {
    return selectors_getCurrentPostAttribute(state, attributeName);
  }

  return Object(objectSpread["a" /* default */])({}, selectors_getCurrentPostAttribute(state, attributeName), edits[attributeName]);
}, function (state, attributeName) {
  return [Object(external_lodash_["get"])(state.editor.present.edits, [attributeName], EMPTY_OBJECT), Object(external_lodash_["get"])(state.currentPost, [attributeName], EMPTY_OBJECT)];
});
/**
 * Returns a single attribute of the post being edited, preferring the unsaved
 * edit if one exists, but falling back to the attribute for the last known
 * saved state of the post.
 *
 * @param {Object} state         Global application state.
 * @param {string} attributeName Post attribute name.
 *
 * @return {*} Post attribute value.
 */

function selectors_getEditedPostAttribute(state, attributeName) {
  // Special cases
  switch (attributeName) {
    case 'content':
      return getEditedPostContent(state);
  } // Fall back to saved post value if not edited.


  var edits = getPostEdits(state);

  if (!edits.hasOwnProperty(attributeName)) {
    return selectors_getCurrentPostAttribute(state, attributeName);
  } // Merge properties are objects which contain only the patch edit in state,
  // and thus must be merged with the current post attribute.


  if (EDIT_MERGE_PROPERTIES.has(attributeName)) {
    return getNestedEditedPostProperty(state, attributeName);
  }

  return edits[attributeName];
}
/**
 * Returns an attribute value of the current autosave revision for a post, or
 * null if there is no autosave for the post.
 *
 * @param {Object} state         Global application state.
 * @param {string} attributeName Autosave attribute name.
 *
 * @return {*} Autosave attribute value.
 */

function getAutosaveAttribute(state, attributeName) {
  if (!hasAutosave(state)) {
    return null;
  }

  var autosave = getAutosave(state);

  if (autosave.hasOwnProperty(attributeName)) {
    return autosave[attributeName];
  }
}
/**
 * Returns the current visibility of the post being edited, preferring the
 * unsaved value if different than the saved post. The return value is one of
 * "private", "password", or "public".
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Post visibility.
 */

function selectors_getEditedPostVisibility(state) {
  var status = selectors_getEditedPostAttribute(state, 'status');

  if (status === 'private') {
    return 'private';
  }

  var password = selectors_getEditedPostAttribute(state, 'password');

  if (password) {
    return 'password';
  }

  return 'public';
}
/**
 * Returns true if post is pending review.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether current post is pending review.
 */

function isCurrentPostPending(state) {
  return selectors_getCurrentPost(state).status === 'pending';
}
/**
 * Return true if the current post has already been published.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post has been published.
 */

function selectors_isCurrentPostPublished(state) {
  var post = selectors_getCurrentPost(state);
  return ['publish', 'private'].indexOf(post.status) !== -1 || post.status === 'future' && !Object(external_this_wp_date_["isInTheFuture"])(new Date(Number(Object(external_this_wp_date_["getDate"])(post.date)) - ONE_MINUTE_IN_MS));
}
/**
 * Returns true if post is already scheduled.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether current post is scheduled to be posted.
 */

function selectors_isCurrentPostScheduled(state) {
  return selectors_getCurrentPost(state).status === 'future' && !selectors_isCurrentPostPublished(state);
}
/**
 * Return true if the post being edited can be published.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post can been published.
 */

function selectors_isEditedPostPublishable(state) {
  var post = selectors_getCurrentPost(state); // TODO: Post being publishable should be superset of condition of post
  // being saveable. Currently this restriction is imposed at UI.
  //
  //  See: <PostPublishButton /> (`isButtonEnabled` assigned by `isSaveable`)

  return selectors_isEditedPostDirty(state) || ['publish', 'private', 'future'].indexOf(post.status) === -1;
}
/**
 * Returns true if the post can be saved, or false otherwise. A post must
 * contain a title, an excerpt, or non-empty content to be valid for save.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post can be saved.
 */

function selectors_isEditedPostSaveable(state) {
  if (selectors_isSavingPost(state)) {
    return false;
  } // TODO: Post should not be saveable if not dirty. Cannot be added here at
  // this time since posts where meta boxes are present can be saved even if
  // the post is not dirty. Currently this restriction is imposed at UI, but
  // should be moved here.
  //
  //  See: `isEditedPostPublishable` (includes `isEditedPostDirty` condition)
  //  See: <PostSavedState /> (`forceIsDirty` prop)
  //  See: <PostPublishButton /> (`forceIsDirty` prop)
  //  See: https://github.com/WordPress/gutenberg/pull/4184


  return !!selectors_getEditedPostAttribute(state, 'title') || !!selectors_getEditedPostAttribute(state, 'excerpt') || !isEditedPostEmpty(state);
}
/**
 * Returns true if the edited post has content. A post has content if it has at
 * least one saveable block or otherwise has a non-empty content property
 * assigned.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether post has content.
 */

function isEditedPostEmpty(state) {
  // While the condition of truthy content string is sufficient to determine
  // emptiness, testing saveable blocks length is a trivial operation. Since
  // this function can be called frequently, optimize for the fast case as a
  // condition of the mere existence of blocks. Note that the value of edited
  // content takes precedent over block content, and must fall through to the
  // default logic.
  var blocks = state.editor.present.blocks.value;

  if (blocks.length && !('content' in getPostEdits(state))) {
    // Pierce the abstraction of the serializer in knowing that blocks are
    // joined with with newlines such that even if every individual block
    // produces an empty save result, the serialized content is non-empty.
    if (blocks.length > 1) {
      return false;
    } // There are two conditions under which the optimization cannot be
    // assumed, and a fallthrough to getEditedPostContent must occur:
    //
    // 1. getBlocksForSerialization has special treatment in omitting a
    //    single unmodified default block.
    // 2. Comment delimiters are omitted for a freeform or unregistered
    //    block in its serialization. The freeform block specifically may
    //    produce an empty string in its saved output.
    //
    // For all other content, the single block is assumed to make a post
    // non-empty, if only by virtue of its own comment delimiters.


    var blockName = blocks[0].name;

    if (blockName !== Object(external_this_wp_blocks_["getDefaultBlockName"])() && blockName !== Object(external_this_wp_blocks_["getFreeformContentHandlerName"])()) {
      return false;
    }
  }

  return !getEditedPostContent(state);
}
/**
 * Returns true if the post can be autosaved, or false otherwise.
 *
 * @param  {Object}  state Global application state.
 *
 * @return {boolean} Whether the post can be autosaved.
 */

function selectors_isEditedPostAutosaveable(state) {
  // A post must contain a title, an excerpt, or non-empty content to be valid for autosaving.
  if (!selectors_isEditedPostSaveable(state)) {
    return false;
  } // If we don't already have an autosave, the post is autosaveable.


  if (!hasAutosave(state)) {
    return true;
  } // To avoid an expensive content serialization, use the content dirtiness
  // flag in place of content field comparison against the known autosave.
  // This is not strictly accurate, and relies on a tolerance toward autosave
  // request failures for unnecessary saves.


  if (hasChangedContent(state)) {
    return true;
  } // If the title, excerpt or content has changed, the post is autosaveable.


  var autosave = getAutosave(state);
  return ['title', 'excerpt'].some(function (field) {
    return autosave[field] !== selectors_getEditedPostAttribute(state, field);
  });
}
/**
 * Returns the current autosave, or null if one is not set (i.e. if the post
 * has yet to be autosaved, or has been saved or published since the last
 * autosave).
 *
 * @param {Object} state Editor state.
 *
 * @return {?Object} Current autosave, if exists.
 */

function getAutosave(state) {
  return state.autosave;
}
/**
 * Returns the true if there is an existing autosave, otherwise false.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether there is an existing autosave.
 */

function hasAutosave(state) {
  return !!getAutosave(state);
}
/**
 * Return true if the post being edited is being scheduled. Preferring the
 * unsaved status values.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post has been published.
 */

function selectors_isEditedPostBeingScheduled(state) {
  var date = selectors_getEditedPostAttribute(state, 'date'); // Offset the date by one minute (network latency)

  var checkedDate = new Date(Number(Object(external_this_wp_date_["getDate"])(date)) - ONE_MINUTE_IN_MS);
  return Object(external_this_wp_date_["isInTheFuture"])(checkedDate);
}
/**
 * Returns whether the current post should be considered to have a "floating"
 * date (i.e. that it would publish "Immediately" rather than at a set time).
 *
 * Unlike in the PHP backend, the REST API returns a full date string for posts
 * where the 0000-00-00T00:00:00 placeholder is present in the database. To
 * infer that a post is set to publish "Immediately" we check whether the date
 * and modified date are the same.
 *
 * @param  {Object}  state Editor state.
 *
 * @return {boolean} Whether the edited post has a floating date value.
 */

function isEditedPostDateFloating(state) {
  var date = selectors_getEditedPostAttribute(state, 'date');
  var modified = selectors_getEditedPostAttribute(state, 'modified');
  var status = selectors_getEditedPostAttribute(state, 'status');

  if (status === 'draft' || status === 'auto-draft' || status === 'pending') {
    return date === modified;
  }

  return false;
}
/**
 * Returns true if the post is currently being saved, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether post is being saved.
 */

function selectors_isSavingPost(state) {
  return state.saving.requesting;
}
/**
 * Returns true if a previous post save was attempted successfully, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post was saved successfully.
 */

function didPostSaveRequestSucceed(state) {
  return state.saving.successful;
}
/**
 * Returns true if a previous post save was attempted but failed, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post save failed.
 */

function didPostSaveRequestFail(state) {
  return !!state.saving.error;
}
/**
 * Returns true if the post is autosaving, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post is autosaving.
 */

function selectors_isAutosavingPost(state) {
  return selectors_isSavingPost(state) && !!state.saving.options.isAutosave;
}
/**
 * Returns true if the post is being previewed, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post is being previewed.
 */

function isPreviewingPost(state) {
  return selectors_isSavingPost(state) && !!state.saving.options.isPreview;
}
/**
 * Returns the post preview link
 *
 * @param {Object} state Global application state.
 *
 * @return {string?} Preview Link.
 */

function selectors_getEditedPostPreviewLink(state) {
  var featuredImageId = selectors_getEditedPostAttribute(state, 'featured_media');
  var previewLink = state.previewLink;

  if (previewLink && featuredImageId) {
    return Object(external_this_wp_url_["addQueryArgs"])(previewLink, {
      _thumbnail_id: featuredImageId
    });
  }

  return previewLink;
}
/**
 * Returns a suggested post format for the current post, inferred only if there
 * is a single block within the post and it is of a type known to match a
 * default post format. Returns null if the format cannot be determined.
 *
 * @param {Object} state Global application state.
 *
 * @return {?string} Suggested post format.
 */

function selectors_getSuggestedPostFormat(state) {
  var blocks = state.editor.present.blocks.value;
  var name; // If there is only one block in the content of the post grab its name
  // so we can derive a suitable post format from it.

  if (blocks.length === 1) {
    name = blocks[0].name;
  } // If there are two blocks in the content and the last one is a text blocks
  // grab the name of the first one to also suggest a post format from it.


  if (blocks.length === 2) {
    if (blocks[1].name === 'core/paragraph') {
      name = blocks[0].name;
    }
  } // We only convert to default post formats in core.


  switch (name) {
    case 'core/image':
      return 'image';

    case 'core/quote':
    case 'core/pullquote':
      return 'quote';

    case 'core/gallery':
      return 'gallery';

    case 'core/video':
    case 'core-embed/youtube':
    case 'core-embed/vimeo':
      return 'video';

    case 'core/audio':
    case 'core-embed/spotify':
    case 'core-embed/soundcloud':
      return 'audio';
  }

  return null;
}
/**
 * Returns a set of blocks which are to be used in consideration of the post's
 * generated save content.
 *
 * @param {Object} state Editor state.
 *
 * @return {WPBlock[]} Filtered set of blocks for save.
 */

function getBlocksForSerialization(state) {
  var blocks = state.editor.present.blocks.value; // WARNING: Any changes to the logic of this function should be verified
  // against the implementation of isEditedPostEmpty, which bypasses this
  // function for performance' sake, in an assumption of this current logic
  // being irrelevant to the optimized condition of emptiness.
  // A single unmodified default block is assumed to be equivalent to an
  // empty post.

  var isSingleUnmodifiedDefaultBlock = blocks.length === 1 && Object(external_this_wp_blocks_["isUnmodifiedDefaultBlock"])(blocks[0]);

  if (isSingleUnmodifiedDefaultBlock) {
    return [];
  }

  return blocks;
}
/**
 * Returns the content of the post being edited, preferring raw string edit
 * before falling back to serialization of block state.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Post content.
 */

var getEditedPostContent = Object(rememo["a" /* default */])(function (state) {
  var edits = getPostEdits(state);

  if ('content' in edits) {
    return edits.content;
  }

  var blocks = getBlocksForSerialization(state);
  var content = Object(external_this_wp_blocks_["serialize"])(blocks); // For compatibility purposes, treat a post consisting of a single
  // freeform block as legacy content and downgrade to a pre-block-editor
  // removep'd content format.

  var isSingleFreeformBlock = blocks.length === 1 && blocks[0].name === Object(external_this_wp_blocks_["getFreeformContentHandlerName"])();

  if (isSingleFreeformBlock) {
    return Object(external_this_wp_autop_["removep"])(content);
  }

  return content;
}, function (state) {
  return [state.editor.present.blocks.value, state.editor.present.edits.content, state.initialEdits.content];
});
/**
 * Returns the reusable block with the given ID.
 *
 * @param {Object}        state Global application state.
 * @param {number|string} ref   The reusable block's ID.
 *
 * @return {Object} The reusable block, or null if none exists.
 */

var __experimentalGetReusableBlock = Object(rememo["a" /* default */])(function (state, ref) {
  var block = state.reusableBlocks.data[ref];

  if (!block) {
    return null;
  }

  var isTemporary = isNaN(parseInt(ref));
  return Object(objectSpread["a" /* default */])({}, block, {
    id: isTemporary ? ref : +ref,
    isTemporary: isTemporary
  });
}, function (state, ref) {
  return [state.reusableBlocks.data[ref]];
});
/**
 * Returns whether or not the reusable block with the given ID is being saved.
 *
 * @param {Object} state Global application state.
 * @param {string} ref   The reusable block's ID.
 *
 * @return {boolean} Whether or not the reusable block is being saved.
 */

function __experimentalIsSavingReusableBlock(state, ref) {
  return state.reusableBlocks.isSaving[ref] || false;
}
/**
 * Returns true if the reusable block with the given ID is being fetched, or
 * false otherwise.
 *
 * @param {Object} state Global application state.
 * @param {string} ref   The reusable block's ID.
 *
 * @return {boolean} Whether the reusable block is being fetched.
 */

function __experimentalIsFetchingReusableBlock(state, ref) {
  return !!state.reusableBlocks.isFetching[ref];
}
/**
 * Returns an array of all reusable blocks.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} An array of all reusable blocks.
 */

var __experimentalGetReusableBlocks = Object(rememo["a" /* default */])(function (state) {
  return Object(external_lodash_["map"])(state.reusableBlocks.data, function (value, ref) {
    return __experimentalGetReusableBlock(state, ref);
  });
}, function (state) {
  return [state.reusableBlocks.data];
});
/**
 * Returns state object prior to a specified optimist transaction ID, or `null`
 * if the transaction corresponding to the given ID cannot be found.
 *
 * @param {Object} state         Current global application state.
 * @param {Object} transactionId Optimist transaction ID.
 *
 * @return {Object} Global application state prior to transaction.
 */

function getStateBeforeOptimisticTransaction(state, transactionId) {
  var transaction = Object(external_lodash_["find"])(state.optimist, function (entry) {
    return entry.beforeState && Object(external_lodash_["get"])(entry.action, ['optimist', 'id']) === transactionId;
  });
  return transaction ? transaction.beforeState : null;
}
/**
 * Returns true if the post is being published, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether post is being published.
 */

function selectors_isPublishingPost(state) {
  if (!selectors_isSavingPost(state)) {
    return false;
  } // Saving is optimistic, so assume that current post would be marked as
  // published if publishing


  if (!selectors_isCurrentPostPublished(state)) {
    return false;
  } // Use post update transaction ID to retrieve the state prior to the
  // optimistic transaction


  var stateBeforeRequest = getStateBeforeOptimisticTransaction(state, POST_UPDATE_TRANSACTION_ID); // Consider as publishing when current post prior to request was not
  // considered published

  return !!stateBeforeRequest && !selectors_isCurrentPostPublished(stateBeforeRequest);
}
/**
 * Returns whether the permalink is editable or not.
 *
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether or not the permalink is editable.
 */

function selectors_isPermalinkEditable(state) {
  var permalinkTemplate = selectors_getEditedPostAttribute(state, 'permalink_template');
  return PERMALINK_POSTNAME_REGEX.test(permalinkTemplate);
}
/**
 * Returns the permalink for the post.
 *
 * @param {Object} state Editor state.
 *
 * @return {?string} The permalink, or null if the post is not viewable.
 */

function getPermalink(state) {
  var permalinkParts = selectors_getPermalinkParts(state);

  if (!permalinkParts) {
    return null;
  }

  var prefix = permalinkParts.prefix,
      postName = permalinkParts.postName,
      suffix = permalinkParts.suffix;

  if (selectors_isPermalinkEditable(state)) {
    return prefix + postName + suffix;
  }

  return prefix;
}
/**
 * Returns the permalink for a post, split into it's three parts: the prefix,
 * the postName, and the suffix.
 *
 * @param {Object} state Editor state.
 *
 * @return {Object} An object containing the prefix, postName, and suffix for
 *                  the permalink, or null if the post is not viewable.
 */

function selectors_getPermalinkParts(state) {
  var permalinkTemplate = selectors_getEditedPostAttribute(state, 'permalink_template');

  if (!permalinkTemplate) {
    return null;
  }

  var postName = selectors_getEditedPostAttribute(state, 'slug') || selectors_getEditedPostAttribute(state, 'generated_slug');

  var _permalinkTemplate$sp = permalinkTemplate.split(PERMALINK_POSTNAME_REGEX),
      _permalinkTemplate$sp2 = Object(slicedToArray["a" /* default */])(_permalinkTemplate$sp, 2),
      prefix = _permalinkTemplate$sp2[0],
      suffix = _permalinkTemplate$sp2[1];

  return {
    prefix: prefix,
    postName: postName,
    suffix: suffix
  };
}
/**
 * Returns true if an optimistic transaction is pending commit, for which the
 * before state satisfies the given predicate function.
 *
 * @param {Object}   state     Editor state.
 * @param {Function} predicate Function given state, returning true if match.
 *
 * @return {boolean} Whether predicate matches for some history.
 */

function inSomeHistory(state, predicate) {
  var optimist = state.optimist; // In recursion, optimist state won't exist. Assume exhausted options.

  if (!optimist) {
    return false;
  }

  return optimist.some(function (_ref) {
    var beforeState = _ref.beforeState;
    return beforeState && predicate(beforeState);
  });
}
/**
 * Returns whether the post is locked.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Is locked.
 */

function isPostLocked(state) {
  return state.postLock.isLocked;
}
/**
 * Returns whether post saving is locked.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Is locked.
 */

function selectors_isPostSavingLocked(state) {
  return Object.keys(state.postSavingLock).length > 0;
}
/**
 * Returns whether the edition of the post has been taken over.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Is post lock takeover.
 */

function isPostLockTakeover(state) {
  return state.postLock.isTakeover;
}
/**
 * Returns details about the post lock user.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} A user object.
 */

function getPostLockUser(state) {
  return state.postLock.user;
}
/**
 * Returns the active post lock.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The lock object.
 */

function getActivePostLock(state) {
  return state.postLock.activePostLock;
}
/**
 * Returns whether or not the user has the unfiltered_html capability.
 *
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether the user can or can't post unfiltered HTML.
 */

function canUserUseUnfilteredHTML(state) {
  return Object(external_lodash_["has"])(selectors_getCurrentPost(state), ['_links', 'wp:action-unfiltered-html']);
}
/**
 * Returns whether the pre-publish panel should be shown
 * or skipped when the user clicks the "publish" button.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the pre-publish panel should be shown or not.
 */

function selectors_isPublishSidebarEnabled(state) {
  if (state.preferences.hasOwnProperty('isPublishSidebarEnabled')) {
    return state.preferences.isPublishSidebarEnabled;
  }

  return PREFERENCES_DEFAULTS.isPublishSidebarEnabled;
}
/**
 * Return the current block list.
 *
 * @param {Object} state
 * @return {Array} Block list.
 */

function getEditorBlocks(state) {
  return state.editor.present.blocks.value;
}
/**
 * Is the editor ready
 *
 * @param {Object} state
 * @return {boolean} is Ready.
 */

function __unstableIsEditorReady(state) {
  return state.isReady;
}
/**
 * Returns the post editor settings.
 *
 * @param {Object} state Editor state.
 *
 * @return {Object} The editor settings object.
 */

function selectors_getEditorSettings(state) {
  return state.editorSettings;
}
/*
 * Backward compatibility
 */

function getBlockEditorSelector(name) {
  return Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
    return function (state) {
      var _select;

      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }

      return (_select = select('core/block-editor'))[name].apply(_select, args);
    };
  });
}

var getBlockDependantsCacheBust = getBlockEditorSelector('getBlockDependantsCacheBust');
var selectors_getBlockName = getBlockEditorSelector('getBlockName');
var isBlockValid = getBlockEditorSelector('isBlockValid');
var getBlockAttributes = getBlockEditorSelector('getBlockAttributes');
var getBlock = getBlockEditorSelector('getBlock');
var selectors_getBlocks = getBlockEditorSelector('getBlocks');
var __unstableGetBlockWithoutInnerBlocks = getBlockEditorSelector('__unstableGetBlockWithoutInnerBlocks');
var getClientIdsOfDescendants = getBlockEditorSelector('getClientIdsOfDescendants');
var getClientIdsWithDescendants = getBlockEditorSelector('getClientIdsWithDescendants');
var getGlobalBlockCount = getBlockEditorSelector('getGlobalBlockCount');
var getBlocksByClientId = getBlockEditorSelector('getBlocksByClientId');
var getBlockCount = getBlockEditorSelector('getBlockCount');
var getBlockSelectionStart = getBlockEditorSelector('getBlockSelectionStart');
var getBlockSelectionEnd = getBlockEditorSelector('getBlockSelectionEnd');
var getSelectedBlockCount = getBlockEditorSelector('getSelectedBlockCount');
var hasSelectedBlock = getBlockEditorSelector('hasSelectedBlock');
var selectors_getSelectedBlockClientId = getBlockEditorSelector('getSelectedBlockClientId');
var getSelectedBlock = getBlockEditorSelector('getSelectedBlock');
var getBlockRootClientId = getBlockEditorSelector('getBlockRootClientId');
var getBlockHierarchyRootClientId = getBlockEditorSelector('getBlockHierarchyRootClientId');
var getAdjacentBlockClientId = getBlockEditorSelector('getAdjacentBlockClientId');
var getPreviousBlockClientId = getBlockEditorSelector('getPreviousBlockClientId');
var getNextBlockClientId = getBlockEditorSelector('getNextBlockClientId');
var getSelectedBlocksInitialCaretPosition = getBlockEditorSelector('getSelectedBlocksInitialCaretPosition');
var getMultiSelectedBlockClientIds = getBlockEditorSelector('getMultiSelectedBlockClientIds');
var getMultiSelectedBlocks = getBlockEditorSelector('getMultiSelectedBlocks');
var getFirstMultiSelectedBlockClientId = getBlockEditorSelector('getFirstMultiSelectedBlockClientId');
var getLastMultiSelectedBlockClientId = getBlockEditorSelector('getLastMultiSelectedBlockClientId');
var isFirstMultiSelectedBlock = getBlockEditorSelector('isFirstMultiSelectedBlock');
var isBlockMultiSelected = getBlockEditorSelector('isBlockMultiSelected');
var isAncestorMultiSelected = getBlockEditorSelector('isAncestorMultiSelected');
var getMultiSelectedBlocksStartClientId = getBlockEditorSelector('getMultiSelectedBlocksStartClientId');
var getMultiSelectedBlocksEndClientId = getBlockEditorSelector('getMultiSelectedBlocksEndClientId');
var getBlockOrder = getBlockEditorSelector('getBlockOrder');
var getBlockIndex = getBlockEditorSelector('getBlockIndex');
var isBlockSelected = getBlockEditorSelector('isBlockSelected');
var hasSelectedInnerBlock = getBlockEditorSelector('hasSelectedInnerBlock');
var isBlockWithinSelection = getBlockEditorSelector('isBlockWithinSelection');
var hasMultiSelection = getBlockEditorSelector('hasMultiSelection');
var isMultiSelecting = getBlockEditorSelector('isMultiSelecting');
var isSelectionEnabled = getBlockEditorSelector('isSelectionEnabled');
var getBlockMode = getBlockEditorSelector('getBlockMode');
var selectors_isTyping = getBlockEditorSelector('isTyping');
var selectors_isCaretWithinFormattedText = getBlockEditorSelector('isCaretWithinFormattedText');
var getBlockInsertionPoint = getBlockEditorSelector('getBlockInsertionPoint');
var isBlockInsertionPointVisible = getBlockEditorSelector('isBlockInsertionPointVisible');
var isValidTemplate = getBlockEditorSelector('isValidTemplate');
var getTemplate = getBlockEditorSelector('getTemplate');
var getTemplateLock = getBlockEditorSelector('getTemplateLock');
var canInsertBlockType = getBlockEditorSelector('canInsertBlockType');
var selectors_getInserterItems = getBlockEditorSelector('getInserterItems');
var hasInserterItems = getBlockEditorSelector('hasInserterItems');
var getBlockListSettings = getBlockEditorSelector('getBlockListSettings');

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/effects/reusable-blocks.js




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



 // TODO: Ideally this would be the only dispatch in scope. This requires either
// refactoring editor actions to yielded controls, or replacing direct dispatch
// on the editor store with action creators (e.g. `REMOVE_REUSABLE_BLOCK`).


/**
 * Internal dependencies
 */




/**
 * Module Constants
 */

var REUSABLE_BLOCK_NOTICE_ID = 'REUSABLE_BLOCK_NOTICE_ID';
/**
 * Fetch Reusable Blocks Effect Handler.
 *
 * @param {Object} action  action object.
 * @param {Object} store   Redux Store.
 */

var fetchReusableBlocks =
/*#__PURE__*/
function () {
  var _ref = Object(asyncToGenerator["a" /* default */])(
  /*#__PURE__*/
  regenerator_default.a.mark(function _callee(action, store) {
    var id, dispatch, postType, posts, results;
    return regenerator_default.a.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            id = action.id;
            dispatch = store.dispatch; // TODO: these are potentially undefined, this fix is in place
            // until there is a filter to not use reusable blocks if undefined

            _context.next = 4;
            return external_this_wp_apiFetch_default()({
              path: '/wp/v2/types/wp_block'
            });

          case 4:
            postType = _context.sent;

            if (postType) {
              _context.next = 7;
              break;
            }

            return _context.abrupt("return");

          case 7:
            _context.prev = 7;

            if (!id) {
              _context.next = 15;
              break;
            }

            _context.next = 11;
            return external_this_wp_apiFetch_default()({
              path: "/wp/v2/".concat(postType.rest_base, "/").concat(id)
            });

          case 11:
            _context.t0 = _context.sent;
            posts = [_context.t0];
            _context.next = 18;
            break;

          case 15:
            _context.next = 17;
            return external_this_wp_apiFetch_default()({
              path: "/wp/v2/".concat(postType.rest_base, "?per_page=-1")
            });

          case 17:
            posts = _context.sent;

          case 18:
            results = Object(external_lodash_["compact"])(Object(external_lodash_["map"])(posts, function (post) {
              if (post.status !== 'publish' || post.content.protected) {
                return null;
              }

              var parsedBlocks = Object(external_this_wp_blocks_["parse"])(post.content.raw);
              return {
                reusableBlock: {
                  id: post.id,
                  title: getPostRawValue(post.title)
                },
                parsedBlock: parsedBlocks.length === 1 ? parsedBlocks[0] : Object(external_this_wp_blocks_["createBlock"])('core/template', {}, parsedBlocks)
              };
            }));

            if (results.length) {
              dispatch(__experimentalReceiveReusableBlocks(results));
            }

            dispatch({
              type: 'FETCH_REUSABLE_BLOCKS_SUCCESS',
              id: id
            });
            _context.next = 26;
            break;

          case 23:
            _context.prev = 23;
            _context.t1 = _context["catch"](7);
            dispatch({
              type: 'FETCH_REUSABLE_BLOCKS_FAILURE',
              id: id,
              error: _context.t1
            });

          case 26:
          case "end":
            return _context.stop();
        }
      }
    }, _callee, this, [[7, 23]]);
  }));

  return function fetchReusableBlocks(_x, _x2) {
    return _ref.apply(this, arguments);
  };
}();
/**
 * Save Reusable Blocks Effect Handler.
 *
 * @param {Object} action  action object.
 * @param {Object} store   Redux Store.
 */

var saveReusableBlocks =
/*#__PURE__*/
function () {
  var _ref2 = Object(asyncToGenerator["a" /* default */])(
  /*#__PURE__*/
  regenerator_default.a.mark(function _callee2(action, store) {
    var postType, id, dispatch, state, _getReusableBlock, clientId, title, isTemporary, reusableBlock, content, data, path, method, updatedReusableBlock, message;

    return regenerator_default.a.wrap(function _callee2$(_context2) {
      while (1) {
        switch (_context2.prev = _context2.next) {
          case 0:
            _context2.next = 2;
            return external_this_wp_apiFetch_default()({
              path: '/wp/v2/types/wp_block'
            });

          case 2:
            postType = _context2.sent;

            if (postType) {
              _context2.next = 5;
              break;
            }

            return _context2.abrupt("return");

          case 5:
            id = action.id;
            dispatch = store.dispatch;
            state = store.getState();
            _getReusableBlock = __experimentalGetReusableBlock(state, id), clientId = _getReusableBlock.clientId, title = _getReusableBlock.title, isTemporary = _getReusableBlock.isTemporary;
            reusableBlock = Object(external_this_wp_data_["select"])('core/block-editor').getBlock(clientId);
            content = Object(external_this_wp_blocks_["serialize"])(reusableBlock.name === 'core/template' ? reusableBlock.innerBlocks : reusableBlock);
            data = isTemporary ? {
              title: title,
              content: content,
              status: 'publish'
            } : {
              id: id,
              title: title,
              content: content,
              status: 'publish'
            };
            path = isTemporary ? "/wp/v2/".concat(postType.rest_base) : "/wp/v2/".concat(postType.rest_base, "/").concat(id);
            method = isTemporary ? 'POST' : 'PUT';
            _context2.prev = 14;
            _context2.next = 17;
            return external_this_wp_apiFetch_default()({
              path: path,
              data: data,
              method: method
            });

          case 17:
            updatedReusableBlock = _context2.sent;
            dispatch({
              type: 'SAVE_REUSABLE_BLOCK_SUCCESS',
              updatedId: updatedReusableBlock.id,
              id: id
            });
            message = isTemporary ? Object(external_this_wp_i18n_["__"])('Block created.') : Object(external_this_wp_i18n_["__"])('Block updated.');
            Object(external_this_wp_data_["dispatch"])('core/notices').createSuccessNotice(message, {
              id: REUSABLE_BLOCK_NOTICE_ID
            });

            Object(external_this_wp_data_["dispatch"])('core/block-editor').__unstableSaveReusableBlock(id, updatedReusableBlock.id);

            _context2.next = 28;
            break;

          case 24:
            _context2.prev = 24;
            _context2.t0 = _context2["catch"](14);
            dispatch({
              type: 'SAVE_REUSABLE_BLOCK_FAILURE',
              id: id
            });
            Object(external_this_wp_data_["dispatch"])('core/notices').createErrorNotice(_context2.t0.message, {
              id: REUSABLE_BLOCK_NOTICE_ID
            });

          case 28:
          case "end":
            return _context2.stop();
        }
      }
    }, _callee2, this, [[14, 24]]);
  }));

  return function saveReusableBlocks(_x3, _x4) {
    return _ref2.apply(this, arguments);
  };
}();
/**
 * Delete Reusable Blocks Effect Handler.
 *
 * @param {Object} action  action object.
 * @param {Object} store   Redux Store.
 */

var deleteReusableBlocks =
/*#__PURE__*/
function () {
  var _ref3 = Object(asyncToGenerator["a" /* default */])(
  /*#__PURE__*/
  regenerator_default.a.mark(function _callee3(action, store) {
    var postType, id, getState, dispatch, reusableBlock, allBlocks, associatedBlocks, associatedBlockClientIds, transactionId, message;
    return regenerator_default.a.wrap(function _callee3$(_context3) {
      while (1) {
        switch (_context3.prev = _context3.next) {
          case 0:
            _context3.next = 2;
            return external_this_wp_apiFetch_default()({
              path: '/wp/v2/types/wp_block'
            });

          case 2:
            postType = _context3.sent;

            if (postType) {
              _context3.next = 5;
              break;
            }

            return _context3.abrupt("return");

          case 5:
            id = action.id;
            getState = store.getState, dispatch = store.dispatch; // Don't allow a reusable block with a temporary ID to be deleted

            reusableBlock = __experimentalGetReusableBlock(getState(), id);

            if (!(!reusableBlock || reusableBlock.isTemporary)) {
              _context3.next = 10;
              break;
            }

            return _context3.abrupt("return");

          case 10:
            // Remove any other blocks that reference this reusable block
            allBlocks = Object(external_this_wp_data_["select"])('core/block-editor').getBlocks();
            associatedBlocks = allBlocks.filter(function (block) {
              return Object(external_this_wp_blocks_["isReusableBlock"])(block) && block.attributes.ref === id;
            });
            associatedBlockClientIds = associatedBlocks.map(function (block) {
              return block.clientId;
            });
            transactionId = Object(external_lodash_["uniqueId"])();
            dispatch({
              type: 'REMOVE_REUSABLE_BLOCK',
              id: id,
              optimist: {
                type: redux_optimist["BEGIN"],
                id: transactionId
              }
            }); // Remove the parsed block.

            Object(external_this_wp_data_["dispatch"])('core/block-editor').removeBlocks([].concat(Object(toConsumableArray["a" /* default */])(associatedBlockClientIds), [reusableBlock.clientId]));
            _context3.prev = 16;
            _context3.next = 19;
            return external_this_wp_apiFetch_default()({
              path: "/wp/v2/".concat(postType.rest_base, "/").concat(id),
              method: 'DELETE'
            });

          case 19:
            dispatch({
              type: 'DELETE_REUSABLE_BLOCK_SUCCESS',
              id: id,
              optimist: {
                type: redux_optimist["COMMIT"],
                id: transactionId
              }
            });
            message = Object(external_this_wp_i18n_["__"])('Block deleted.');
            Object(external_this_wp_data_["dispatch"])('core/notices').createSuccessNotice(message, {
              id: REUSABLE_BLOCK_NOTICE_ID
            });
            _context3.next = 28;
            break;

          case 24:
            _context3.prev = 24;
            _context3.t0 = _context3["catch"](16);
            dispatch({
              type: 'DELETE_REUSABLE_BLOCK_FAILURE',
              id: id,
              optimist: {
                type: redux_optimist["REVERT"],
                id: transactionId
              }
            });
            Object(external_this_wp_data_["dispatch"])('core/notices').createErrorNotice(_context3.t0.message, {
              id: REUSABLE_BLOCK_NOTICE_ID
            });

          case 28:
          case "end":
            return _context3.stop();
        }
      }
    }, _callee3, this, [[16, 24]]);
  }));

  return function deleteReusableBlocks(_x5, _x6) {
    return _ref3.apply(this, arguments);
  };
}();
/**
 * Receive Reusable Blocks Effect Handler.
 *
 * @param {Object} action  action object.
 */

var reusable_blocks_receiveReusableBlocks = function receiveReusableBlocks(action) {
  Object(external_this_wp_data_["dispatch"])('core/block-editor').receiveBlocks(Object(external_lodash_["map"])(action.results, 'parsedBlock'));
};
/**
 * Convert a reusable block to a static block effect handler
 *
 * @param {Object} action  action object.
 * @param {Object} store   Redux Store.
 */

var reusable_blocks_convertBlockToStatic = function convertBlockToStatic(action, store) {
  var state = store.getState();
  var oldBlock = Object(external_this_wp_data_["select"])('core/block-editor').getBlock(action.clientId);
  var reusableBlock = __experimentalGetReusableBlock(state, oldBlock.attributes.ref);
  var referencedBlock = Object(external_this_wp_data_["select"])('core/block-editor').getBlock(reusableBlock.clientId);
  var newBlocks;

  if (referencedBlock.name === 'core/template') {
    newBlocks = referencedBlock.innerBlocks.map(function (innerBlock) {
      return Object(external_this_wp_blocks_["cloneBlock"])(innerBlock);
    });
  } else {
    newBlocks = [Object(external_this_wp_blocks_["cloneBlock"])(referencedBlock)];
  }

  Object(external_this_wp_data_["dispatch"])('core/block-editor').replaceBlocks(oldBlock.clientId, newBlocks);
};
/**
 * Convert a static block to a reusable block effect handler
 *
 * @param {Object} action  action object.
 * @param {Object} store   Redux Store.
 */

var reusable_blocks_convertBlockToReusable = function convertBlockToReusable(action, store) {
  var dispatch = store.dispatch;
  var parsedBlock;

  if (action.clientIds.length === 1) {
    parsedBlock = Object(external_this_wp_data_["select"])('core/block-editor').getBlock(action.clientIds[0]);
  } else {
    parsedBlock = Object(external_this_wp_blocks_["createBlock"])('core/template', {}, Object(external_this_wp_data_["select"])('core/block-editor').getBlocksByClientId(action.clientIds)); // This shouldn't be necessary but at the moment
    // we expect the content of the shared blocks to live in the blocks state.

    Object(external_this_wp_data_["dispatch"])('core/block-editor').receiveBlocks([parsedBlock]);
  }

  var reusableBlock = {
    id: Object(external_lodash_["uniqueId"])('reusable'),
    clientId: parsedBlock.clientId,
    title: Object(external_this_wp_i18n_["__"])('Untitled Reusable Block')
  };
  dispatch(__experimentalReceiveReusableBlocks([{
    reusableBlock: reusableBlock,
    parsedBlock: parsedBlock
  }]));
  dispatch(__experimentalSaveReusableBlock(reusableBlock.id));
  Object(external_this_wp_data_["dispatch"])('core/block-editor').replaceBlocks(action.clientIds, Object(external_this_wp_blocks_["createBlock"])('core/block', {
    ref: reusableBlock.id
  })); // Re-add the original block to the store, since replaceBlock() will have removed it

  Object(external_this_wp_data_["dispatch"])('core/block-editor').receiveBlocks([parsedBlock]);
};

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/effects.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/* harmony default export */ var effects = ({
  SETUP_EDITOR: function SETUP_EDITOR(action) {
    var post = action.post,
        edits = action.edits,
        template = action.template; // In order to ensure maximum of a single parse during setup, edits are
    // included as part of editor setup action. Assume edited content as
    // canonical if provided, falling back to post.

    var content;

    if (Object(external_lodash_["has"])(edits, ['content'])) {
      content = edits.content;
    } else {
      content = post.content.raw;
    }

    var blocks = Object(external_this_wp_blocks_["parse"])(content); // Apply a template for new posts only, if exists.

    var isNewPost = post.status === 'auto-draft';

    if (isNewPost && template) {
      blocks = Object(external_this_wp_blocks_["synchronizeBlocksWithTemplate"])(blocks, template);
    }

    return [actions_resetEditorBlocks(blocks), setupEditorState(post)];
  },
  FETCH_REUSABLE_BLOCKS: function FETCH_REUSABLE_BLOCKS(action, store) {
    fetchReusableBlocks(action, store);
  },
  SAVE_REUSABLE_BLOCK: function SAVE_REUSABLE_BLOCK(action, store) {
    saveReusableBlocks(action, store);
  },
  DELETE_REUSABLE_BLOCK: function DELETE_REUSABLE_BLOCK(action, store) {
    deleteReusableBlocks(action, store);
  },
  RECEIVE_REUSABLE_BLOCKS: reusable_blocks_receiveReusableBlocks,
  CONVERT_BLOCK_TO_STATIC: reusable_blocks_convertBlockToStatic,
  CONVERT_BLOCK_TO_REUSABLE: reusable_blocks_convertBlockToReusable
});

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/middlewares.js


/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Applies the custom middlewares used specifically in the editor module.
 *
 * @param {Object} store Store Object.
 *
 * @return {Object} Update Store Object.
 */

function applyMiddlewares(store) {
  var middlewares = [refx_default()(effects), lib_default.a];

  var enhancedDispatch = function enhancedDispatch() {
    throw new Error('Dispatching while constructing your middleware is not allowed. ' + 'Other middleware would not be applied to this dispatch.');
  };

  var chain = [];
  var middlewareAPI = {
    getState: store.getState,
    dispatch: function dispatch() {
      return enhancedDispatch.apply(void 0, arguments);
    }
  };
  chain = middlewares.map(function (middleware) {
    return middleware(middlewareAPI);
  });
  enhancedDispatch = external_lodash_["flowRight"].apply(void 0, Object(toConsumableArray["a" /* default */])(chain))(store.dispatch);
  store.dispatch = enhancedDispatch;
  return store;
}

/* harmony default export */ var store_middlewares = (applyMiddlewares);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */







var store_store = Object(external_this_wp_data_["registerStore"])(STORE_KEY, {
  reducer: store_reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject,
  controls: controls,
  persist: ['preferences']
});
store_middlewares(store_store);
/* harmony default export */ var build_module_store = (store_store);

// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__("g56x");

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__("GRId");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autocompleters/block.js



/**
 * WordPress dependencies
 */



/**
 * Returns the client ID of the parent where a newly inserted block would be
 * placed.
 *
 * @return {string} Client ID of the parent where a newly inserted block would
 *                  be placed.
 */

function defaultGetBlockInsertionParentClientId() {
  return Object(external_this_wp_data_["select"])('core/block-editor').getBlockInsertionPoint().rootClientId;
}
/**
 * Returns the inserter items for the specified parent block.
 *
 * @param {string} rootClientId Client ID of the block for which to retrieve
 *                              inserter items.
 *
 * @return {Array<Editor.InserterItem>} The inserter items for the specified
 *                                      parent.
 */


function defaultGetInserterItems(rootClientId) {
  return Object(external_this_wp_data_["select"])('core/block-editor').getInserterItems(rootClientId);
}
/**
 * Returns the name of the currently selected block.
 *
 * @return {string?} The name of the currently selected block or `null` if no
 *                   block is selected.
 */


function defaultGetSelectedBlockName() {
  var _select = Object(external_this_wp_data_["select"])('core/block-editor'),
      getSelectedBlockClientId = _select.getSelectedBlockClientId,
      getBlockName = _select.getBlockName;

  var selectedBlockClientId = getSelectedBlockClientId();
  return selectedBlockClientId ? getBlockName(selectedBlockClientId) : null;
}
/**
 * Creates a blocks repeater for replacing the current block with a selected block type.
 *
 * @return {Completer} A blocks completer.
 */


function createBlockCompleter() {
  var _ref = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
      _ref$getBlockInsertio = _ref.getBlockInsertionParentClientId,
      getBlockInsertionParentClientId = _ref$getBlockInsertio === void 0 ? defaultGetBlockInsertionParentClientId : _ref$getBlockInsertio,
      _ref$getInserterItems = _ref.getInserterItems,
      getInserterItems = _ref$getInserterItems === void 0 ? defaultGetInserterItems : _ref$getInserterItems,
      _ref$getSelectedBlock = _ref.getSelectedBlockName,
      getSelectedBlockName = _ref$getSelectedBlock === void 0 ? defaultGetSelectedBlockName : _ref$getSelectedBlock;

  return {
    name: 'blocks',
    className: 'editor-autocompleters__block',
    triggerPrefix: '/',
    options: function options() {
      var selectedBlockName = getSelectedBlockName();
      return getInserterItems(getBlockInsertionParentClientId()).filter( // Avoid offering to replace the current block with a block of the same type.
      function (inserterItem) {
        return selectedBlockName !== inserterItem.name;
      });
    },
    getOptionKeywords: function getOptionKeywords(inserterItem) {
      var title = inserterItem.title,
          _inserterItem$keyword = inserterItem.keywords,
          keywords = _inserterItem$keyword === void 0 ? [] : _inserterItem$keyword,
          category = inserterItem.category;
      return [category].concat(Object(toConsumableArray["a" /* default */])(keywords), [title]);
    },
    getOptionLabel: function getOptionLabel(inserterItem) {
      var icon = inserterItem.icon,
          title = inserterItem.title;
      return [Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
        key: "icon",
        icon: icon,
        showColors: true
      }), title];
    },
    allowContext: function allowContext(before, after) {
      return !(/\S/.test(before) || /\S/.test(after));
    },
    getOptionCompletion: function getOptionCompletion(inserterItem) {
      var name = inserterItem.name,
          initialAttributes = inserterItem.initialAttributes;
      return {
        action: 'replace',
        value: Object(external_this_wp_blocks_["createBlock"])(name, initialAttributes)
      };
    },
    isOptionDisabled: function isOptionDisabled(inserterItem) {
      return inserterItem.isDisabled;
    }
  };
}
/**
 * Creates a blocks repeater for replacing the current block with a selected block type.
 *
 * @return {Completer} A blocks completer.
 */

/* harmony default export */ var autocompleters_block = (createBlockCompleter());

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autocompleters/user.js


/**
 * WordPress dependencies
 */

/**
* A user mentions completer.
*
* @type {Completer}
*/

/* harmony default export */ var autocompleters_user = ({
  name: 'users',
  className: 'editor-autocompleters__user',
  triggerPrefix: '@',
  options: function options(search) {
    var payload = '';

    if (search) {
      payload = '?search=' + encodeURIComponent(search);
    }

    return external_this_wp_apiFetch_default()({
      path: '/wp/v2/users' + payload
    });
  },
  isDebounced: true,
  getOptionKeywords: function getOptionKeywords(user) {
    return [user.slug, user.name];
  },
  getOptionLabel: function getOptionLabel(user) {
    return [Object(external_this_wp_element_["createElement"])("img", {
      key: "avatar",
      className: "editor-autocompleters__user-avatar",
      alt: "",
      src: user.avatar_urls[24]
    }), Object(external_this_wp_element_["createElement"])("span", {
      key: "name",
      className: "editor-autocompleters__user-name"
    }, user.name), Object(external_this_wp_element_["createElement"])("span", {
      key: "slug",
      className: "editor-autocompleters__user-slug"
    }, user.slug)];
  },
  getOptionCompletion: function getOptionCompletion(user) {
    return "@".concat(user.slug);
  }
});

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autocompleters/index.js



// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__("Ff2n");

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__("tI+e");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/server-side-render/index.js





/**
 * WordPress dependencies
 */


/* harmony default export */ var server_side_render = (function (_ref) {
  var _ref$urlQueryArgs = _ref.urlQueryArgs,
      urlQueryArgs = _ref$urlQueryArgs === void 0 ? {} : _ref$urlQueryArgs,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["urlQueryArgs"]);

  var _select = Object(external_this_wp_data_["select"])('core/editor'),
      getCurrentPostId = _select.getCurrentPostId;

  urlQueryArgs = Object(objectSpread["a" /* default */])({
    post_id: getCurrentPostId()
  }, urlQueryArgs);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ServerSideRender"], Object(esm_extends["a" /* default */])({
    urlQueryArgs: urlQueryArgs
  }, props));
});

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__("1OyB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__("vuIU");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__("md7G");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__("foSv");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__("Ji7U");

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__("K9lf");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autosave-monitor/index.js






/**
 * WordPress dependencies
 */



var autosave_monitor_AutosaveMonitor =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(AutosaveMonitor, _Component);

  function AutosaveMonitor() {
    Object(classCallCheck["a" /* default */])(this, AutosaveMonitor);

    return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(AutosaveMonitor).apply(this, arguments));
  }

  Object(createClass["a" /* default */])(AutosaveMonitor, [{
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this$props = this.props,
          isDirty = _this$props.isDirty,
          editsReference = _this$props.editsReference,
          isAutosaveable = _this$props.isAutosaveable,
          isAutosaving = _this$props.isAutosaving; // The edits reference is held for comparison to avoid scheduling an
      // autosave if an edit has not been made since the last autosave
      // completion. This is assigned when the autosave completes, and reset
      // when an edit occurs.
      //
      // See: https://github.com/WordPress/gutenberg/issues/12318

      if (editsReference !== prevProps.editsReference) {
        this.didAutosaveForEditsReference = false;
      }

      if (!isAutosaving && prevProps.isAutosaving) {
        this.didAutosaveForEditsReference = true;
      }

      if (prevProps.isDirty !== isDirty || prevProps.isAutosaveable !== isAutosaveable || prevProps.editsReference !== editsReference) {
        this.toggleTimer(isDirty && isAutosaveable && !this.didAutosaveForEditsReference);
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.toggleTimer(false);
    }
  }, {
    key: "toggleTimer",
    value: function toggleTimer(isPendingSave) {
      var _this = this;

      clearTimeout(this.pendingSave);
      var autosaveInterval = this.props.autosaveInterval;

      if (isPendingSave) {
        this.pendingSave = setTimeout(function () {
          return _this.props.autosave();
        }, autosaveInterval * 1000);
      }
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return AutosaveMonitor;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var autosave_monitor = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isEditedPostDirty = _select.isEditedPostDirty,
      isEditedPostAutosaveable = _select.isEditedPostAutosaveable,
      getReferenceByDistinctEdits = _select.getReferenceByDistinctEdits,
      isAutosavingPost = _select.isAutosavingPost;

  var _select$getEditorSett = select('core/editor').getEditorSettings(),
      autosaveInterval = _select$getEditorSett.autosaveInterval;

  return {
    isDirty: isEditedPostDirty(),
    isAutosaveable: isEditedPostAutosaveable(),
    editsReference: getReferenceByDistinctEdits(),
    isAutosaving: isAutosavingPost(),
    autosaveInterval: autosaveInterval
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    autosave: dispatch('core/editor').autosave
  };
})])(autosave_monitor_AutosaveMonitor));

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-outline/item.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



var item_TableOfContentsItem = function TableOfContentsItem(_ref) {
  var children = _ref.children,
      isValid = _ref.isValid,
      level = _ref.level,
      _ref$path = _ref.path,
      path = _ref$path === void 0 ? [] : _ref$path,
      href = _ref.href,
      onSelect = _ref.onSelect;
  return Object(external_this_wp_element_["createElement"])("li", {
    className: classnames_default()('document-outline__item', "is-".concat(level.toLowerCase()), {
      'is-invalid': !isValid
    })
  }, Object(external_this_wp_element_["createElement"])("a", {
    href: href,
    className: "document-outline__button",
    onClick: onSelect
  }, Object(external_this_wp_element_["createElement"])("span", {
    className: "document-outline__emdash",
    "aria-hidden": "true"
  }), // path is an array of nodes that are ancestors of the heading starting in the top level node.
  // This mapping renders each ancestor to make it easier for the user to know where the headings are nested.
  path.map(function (_ref2, index) {
    var clientId = _ref2.clientId;
    return Object(external_this_wp_element_["createElement"])("strong", {
      key: index,
      className: "document-outline__level"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockTitle"], {
      clientId: clientId
    }));
  }), Object(external_this_wp_element_["createElement"])("strong", {
    className: "document-outline__level"
  }, level), Object(external_this_wp_element_["createElement"])("span", {
    className: "document-outline__item-content"
  }, children)));
};

/* harmony default export */ var document_outline_item = (item_TableOfContentsItem);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-outline/index.js




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

var emptyHeadingContent = Object(external_this_wp_element_["createElement"])("em", null, Object(external_this_wp_i18n_["__"])('(Empty heading)'));
var incorrectLevelContent = [Object(external_this_wp_element_["createElement"])("br", {
  key: "incorrect-break"
}), Object(external_this_wp_element_["createElement"])("em", {
  key: "incorrect-message"
}, Object(external_this_wp_i18n_["__"])('(Incorrect heading level)'))];
var singleH1Headings = [Object(external_this_wp_element_["createElement"])("br", {
  key: "incorrect-break-h1"
}), Object(external_this_wp_element_["createElement"])("em", {
  key: "incorrect-message-h1"
}, Object(external_this_wp_i18n_["__"])('(Your theme may already use a H1 for the post title)'))];
var multipleH1Headings = [Object(external_this_wp_element_["createElement"])("br", {
  key: "incorrect-break-multiple-h1"
}), Object(external_this_wp_element_["createElement"])("em", {
  key: "incorrect-message-multiple-h1"
}, Object(external_this_wp_i18n_["__"])('(Multiple H1 headings are not recommended)'))];
/**
 * Returns an array of heading blocks enhanced with the following properties:
 * path    - An array of blocks that are ancestors of the heading starting from a top-level node.
 *           Can be an empty array if the heading is a top-level node (is not nested inside another block).
 * level   - An integer with the heading level.
 * isEmpty - Flag indicating if the heading has no content.
 *
 * @param {?Array} blocks An array of blocks.
 * @param {?Array} path   An array of blocks that are ancestors of the blocks passed as blocks.
 *
 * @return {Array} An array of heading blocks enhanced with the properties described above.
 */

var document_outline_computeOutlineHeadings = function computeOutlineHeadings() {
  var blocks = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var path = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  return Object(external_lodash_["flatMap"])(blocks, function () {
    var block = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

    if (block.name === 'core/heading') {
      return Object(objectSpread["a" /* default */])({}, block, {
        path: path,
        level: block.attributes.level,
        isEmpty: isEmptyHeading(block)
      });
    }

    return computeOutlineHeadings(block.innerBlocks, [].concat(Object(toConsumableArray["a" /* default */])(path), [block]));
  });
};

var isEmptyHeading = function isEmptyHeading(heading) {
  return !heading.attributes.content || heading.attributes.content.length === 0;
};

var document_outline_DocumentOutline = function DocumentOutline(_ref) {
  var _ref$blocks = _ref.blocks,
      blocks = _ref$blocks === void 0 ? [] : _ref$blocks,
      title = _ref.title,
      onSelect = _ref.onSelect,
      isTitleSupported = _ref.isTitleSupported,
      hasOutlineItemsDisabled = _ref.hasOutlineItemsDisabled;
  var headings = document_outline_computeOutlineHeadings(blocks);

  if (headings.length < 1) {
    return null;
  }

  var prevHeadingLevel = 1; // Not great but it's the simplest way to locate the title right now.

  var titleNode = document.querySelector('.editor-post-title__input');
  var hasTitle = isTitleSupported && title && titleNode;
  var countByLevel = Object(external_lodash_["countBy"])(headings, 'level');
  var hasMultipleH1 = countByLevel[1] > 1;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "document-outline"
  }, Object(external_this_wp_element_["createElement"])("ul", null, hasTitle && Object(external_this_wp_element_["createElement"])(document_outline_item, {
    level: Object(external_this_wp_i18n_["__"])('Title'),
    isValid: true,
    onSelect: onSelect,
    href: "#".concat(titleNode.id),
    isDisabled: hasOutlineItemsDisabled
  }, title), headings.map(function (item, index) {
    // Headings remain the same, go up by one, or down by any amount.
    // Otherwise there are missing levels.
    var isIncorrectLevel = item.level > prevHeadingLevel + 1;
    var isValid = !item.isEmpty && !isIncorrectLevel && !!item.level && (item.level !== 1 || !hasMultipleH1 && !hasTitle);
    prevHeadingLevel = item.level;
    return Object(external_this_wp_element_["createElement"])(document_outline_item, {
      key: index,
      level: "H".concat(item.level),
      isValid: isValid,
      path: item.path,
      isDisabled: hasOutlineItemsDisabled,
      href: "#block-".concat(item.clientId),
      onSelect: onSelect
    }, item.isEmpty ? emptyHeadingContent : Object(external_this_wp_richText_["getTextContent"])(Object(external_this_wp_richText_["create"])({
      html: item.attributes.content
    })), isIncorrectLevel && incorrectLevelContent, item.level === 1 && hasMultipleH1 && multipleH1Headings, hasTitle && item.level === 1 && !hasMultipleH1 && singleH1Headings);
  })));
};
/* harmony default export */ var document_outline = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getBlocks = _select.getBlocks;

  var _select2 = select('core/editor'),
      getEditedPostAttribute = _select2.getEditedPostAttribute;

  var _select3 = select('core'),
      getPostType = _select3.getPostType;

  var postType = getPostType(getEditedPostAttribute('type'));
  return {
    title: getEditedPostAttribute('title'),
    blocks: getBlocks(),
    isTitleSupported: Object(external_lodash_["get"])(postType, ['supports', 'title'], false)
  };
}))(document_outline_DocumentOutline));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-outline/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function DocumentOutlineCheck(_ref) {
  var blocks = _ref.blocks,
      children = _ref.children;
  var headings = Object(external_lodash_["filter"])(blocks, function (block) {
    return block.name === 'core/heading';
  });

  if (headings.length < 1) {
    return null;
  }

  return children;
}

/* harmony default export */ var check = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    blocks: select('core/block-editor').getBlocks()
  };
})(DocumentOutlineCheck));

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__("JX7q");

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__("RxS6");

// EXTERNAL MODULE: external {"this":["wp","deprecated"]}
var external_this_wp_deprecated_ = __webpack_require__("NMb1");
var external_this_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_deprecated_);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/global-keyboard-shortcuts/save-shortcut.js



/**
 * WordPress dependencies
 */




function SaveShortcut(_ref) {
  var onSave = _ref.onSave;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["KeyboardShortcuts"], {
    bindGlobal: true,
    shortcuts: Object(defineProperty["a" /* default */])({}, external_this_wp_keycodes_["rawShortcut"].primary('s'), function (event) {
      event.preventDefault();
      onSave();
    })
  });
}
/* harmony default export */ var save_shortcut = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isEditedPostDirty = _select.isEditedPostDirty;

  return {
    isDirty: isEditedPostDirty()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps, _ref3) {
  var select = _ref3.select;

  var _dispatch = dispatch('core/editor'),
      savePost = _dispatch.savePost;

  return {
    onSave: function onSave() {
      // TODO: This should be handled in the `savePost` effect in
      // considering `isSaveable`. See note on `isEditedPostSaveable`
      // selector about dirtiness and meta-boxes.
      //
      // See: `isEditedPostSaveable`
      var _select2 = select('core/editor'),
          isEditedPostDirty = _select2.isEditedPostDirty;

      if (!isEditedPostDirty()) {
        return;
      }

      savePost();
    }
  };
})])(SaveShortcut));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/global-keyboard-shortcuts/visual-editor-shortcuts.js









/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



var visual_editor_shortcuts_VisualEditorGlobalKeyboardShortcuts =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(VisualEditorGlobalKeyboardShortcuts, _Component);

  function VisualEditorGlobalKeyboardShortcuts() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, VisualEditorGlobalKeyboardShortcuts);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(VisualEditorGlobalKeyboardShortcuts).apply(this, arguments));
    _this.undoOrRedo = _this.undoOrRedo.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(VisualEditorGlobalKeyboardShortcuts, [{
    key: "undoOrRedo",
    value: function undoOrRedo(event) {
      var _this$props = this.props,
          onRedo = _this$props.onRedo,
          onUndo = _this$props.onUndo;

      if (event.shiftKey) {
        onRedo();
      } else {
        onUndo();
      }

      event.preventDefault();
    }
  }, {
    key: "render",
    value: function render() {
      var _ref;

      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockEditorKeyboardShortcuts"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["KeyboardShortcuts"], {
        shortcuts: (_ref = {}, Object(defineProperty["a" /* default */])(_ref, external_this_wp_keycodes_["rawShortcut"].primary('z'), this.undoOrRedo), Object(defineProperty["a" /* default */])(_ref, external_this_wp_keycodes_["rawShortcut"].primaryShift('z'), this.undoOrRedo), _ref)
      }), Object(external_this_wp_element_["createElement"])(save_shortcut, null));
    }
  }]);

  return VisualEditorGlobalKeyboardShortcuts;
}(external_this_wp_element_["Component"]);

var EnhancedVisualEditorGlobalKeyboardShortcuts = Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      redo = _dispatch.redo,
      undo = _dispatch.undo;

  return {
    onRedo: redo,
    onUndo: undo
  };
})(visual_editor_shortcuts_VisualEditorGlobalKeyboardShortcuts);
/* harmony default export */ var visual_editor_shortcuts = (EnhancedVisualEditorGlobalKeyboardShortcuts);
function EditorGlobalKeyboardShortcuts() {
  external_this_wp_deprecated_default()('EditorGlobalKeyboardShortcuts', {
    alternative: 'VisualEditorGlobalKeyboardShortcuts',
    plugin: 'Gutenberg'
  });
  return Object(external_this_wp_element_["createElement"])(EnhancedVisualEditorGlobalKeyboardShortcuts, null);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/global-keyboard-shortcuts/text-editor-shortcuts.js


/**
 * Internal dependencies
 */

function TextEditorGlobalKeyboardShortcuts() {
  return Object(external_this_wp_element_["createElement"])(save_shortcut, null);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-history/redo.js


/**
 * WordPress dependencies
 */






function EditorHistoryRedo(_ref) {
  var hasRedo = _ref.hasRedo,
      redo = _ref.redo;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: "redo",
    label: Object(external_this_wp_i18n_["__"])('Redo'),
    shortcut: external_this_wp_keycodes_["displayShortcut"].primaryShift('z') // If there are no redo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasRedo,
    onClick: hasRedo ? redo : undefined,
    className: "editor-history__redo"
  });
}

/* harmony default export */ var editor_history_redo = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    hasRedo: select('core/editor').hasEditorRedo()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    redo: dispatch('core/editor').redo
  };
})])(EditorHistoryRedo));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-history/undo.js


/**
 * WordPress dependencies
 */






function EditorHistoryUndo(_ref) {
  var hasUndo = _ref.hasUndo,
      undo = _ref.undo;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    icon: "undo",
    label: Object(external_this_wp_i18n_["__"])('Undo'),
    shortcut: external_this_wp_keycodes_["displayShortcut"].primary('z') // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasUndo,
    onClick: hasUndo ? undo : undefined,
    className: "editor-history__undo"
  });
}

/* harmony default export */ var editor_history_undo = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    hasUndo: select('core/editor').hasEditorUndo()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    undo: dispatch('core/editor').undo
  };
})])(EditorHistoryUndo));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/template-validation-notice/index.js



/**
 * WordPress dependencies
 */





function TemplateValidationNotice(_ref) {
  var isValid = _ref.isValid,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["isValid"]);

  if (isValid) {
    return null;
  }

  var confirmSynchronization = function confirmSynchronization() {
    // eslint-disable-next-line no-alert
    if (window.confirm(Object(external_this_wp_i18n_["__"])('Resetting the template may result in loss of content, do you want to continue?'))) {
      props.synchronizeTemplate();
    }
  };

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Notice"], {
    className: "editor-template-validation-notice",
    isDismissible: false,
    status: "warning"
  }, Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('The content of your post doesn’t match the template assigned to your post type.')), Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isDefault: true,
    onClick: props.resetTemplateValidity
  }, Object(external_this_wp_i18n_["__"])('Keep it as is')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    onClick: confirmSynchronization,
    isPrimary: true
  }, Object(external_this_wp_i18n_["__"])('Reset the template'))));
}

/* harmony default export */ var template_validation_notice = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isValid: select('core/block-editor').isValidTemplate()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/block-editor'),
      setTemplateValidity = _dispatch.setTemplateValidity,
      synchronizeTemplate = _dispatch.synchronizeTemplate;

  return {
    resetTemplateValidity: function resetTemplateValidity() {
      return setTemplateValidity(true);
    },
    synchronizeTemplate: synchronizeTemplate
  };
})])(TemplateValidationNotice));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-notices/index.js




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function EditorNotices(_ref) {
  var dismissible = _ref.dismissible,
      notices = _ref.notices,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["dismissible", "notices"]);

  if (dismissible !== undefined) {
    notices = Object(external_lodash_["filter"])(notices, {
      isDismissible: dismissible
    });
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["NoticeList"], Object(esm_extends["a" /* default */])({
    notices: notices
  }, props), dismissible !== false && Object(external_this_wp_element_["createElement"])(template_validation_notice, null));
}
/* harmony default export */ var editor_notices = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    notices: select('core/notices').getNotices()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onRemove: dispatch('core/notices').removeNotice
  };
})])(EditorNotices));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/error-boundary/index.js








/**
 * WordPress dependencies
 */






var error_boundary_ErrorBoundary =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ErrorBoundary, _Component);

  function ErrorBoundary() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, ErrorBoundary);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ErrorBoundary).apply(this, arguments));
    _this.reboot = _this.reboot.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.getContent = _this.getContent.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.state = {
      error: null
    };
    return _this;
  }

  Object(createClass["a" /* default */])(ErrorBoundary, [{
    key: "componentDidCatch",
    value: function componentDidCatch(error) {
      this.setState({
        error: error
      });
    }
  }, {
    key: "reboot",
    value: function reboot() {
      this.props.onError();
    }
  }, {
    key: "getContent",
    value: function getContent() {
      try {
        // While `select` in a component is generally discouraged, it is
        // used here because it (a) reduces the chance of data loss in the
        // case of additional errors by performing a direct retrieval and
        // (b) avoids the performance cost associated with unnecessary
        // content serialization throughout the lifetime of a non-erroring
        // application.
        return Object(external_this_wp_data_["select"])('core/editor').getEditedPostContent();
      } catch (error) {}
    }
  }, {
    key: "render",
    value: function render() {
      var error = this.state.error;

      if (!error) {
        return this.props.children;
      }

      return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["Warning"], {
        className: "editor-error-boundary",
        actions: [Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
          key: "recovery",
          onClick: this.reboot,
          isLarge: true
        }, Object(external_this_wp_i18n_["__"])('Attempt Recovery')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ClipboardButton"], {
          key: "copy-post",
          text: this.getContent,
          isLarge: true
        }, Object(external_this_wp_i18n_["__"])('Copy Post Text')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ClipboardButton"], {
          key: "copy-error",
          text: error.stack,
          isLarge: true
        }, Object(external_this_wp_i18n_["__"])('Copy Error'))]
      }, Object(external_this_wp_i18n_["__"])('The editor has encountered an unexpected error.'));
    }
  }]);

  return ErrorBoundary;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var error_boundary = (error_boundary_ErrorBoundary);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/page-attributes/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function PageAttributesCheck(_ref) {
  var availableTemplates = _ref.availableTemplates,
      postType = _ref.postType,
      children = _ref.children;
  var supportsPageAttributes = Object(external_lodash_["get"])(postType, ['supports', 'page-attributes'], false); // Only render fields if post type supports page attributes or available templates exist.

  if (!supportsPageAttributes && Object(external_lodash_["isEmpty"])(availableTemplates)) {
    return null;
  }

  return children;
}
/* harmony default export */ var page_attributes_check = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute,
      getEditorSettings = _select.getEditorSettings;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  var _getEditorSettings = getEditorSettings(),
      availableTemplates = _getEditorSettings.availableTemplates;

  return {
    postType: getPostType(getEditedPostAttribute('type')),
    availableTemplates: availableTemplates
  };
})(PageAttributesCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-type-support-check/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * A component which renders its own children only if the current editor post
 * type supports one of the given `supportKeys` prop.
 *
 * @param {?Object}           props.postType    Current post type.
 * @param {WPElement}         props.children    Children to be rendered if post
 *                                              type supports.
 * @param {(string|string[])} props.supportKeys String or string array of keys
 *                                              to test.
 *
 * @return {WPElement} Rendered element.
 */

function PostTypeSupportCheck(_ref) {
  var postType = _ref.postType,
      children = _ref.children,
      supportKeys = _ref.supportKeys;
  var isSupported = true;

  if (postType) {
    isSupported = Object(external_lodash_["some"])(Object(external_lodash_["castArray"])(supportKeys), function (key) {
      return !!postType.supports[key];
    });
  }

  if (!isSupported) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_type_support_check = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  return {
    postType: getPostType(getEditedPostAttribute('type'))
  };
})(PostTypeSupportCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/page-attributes/order.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


var PageAttributesOrder = Object(external_this_wp_compose_["withState"])({
  orderInput: null
})(function (_ref) {
  var onUpdateOrder = _ref.onUpdateOrder,
      _ref$order = _ref.order,
      order = _ref$order === void 0 ? 0 : _ref$order,
      orderInput = _ref.orderInput,
      setState = _ref.setState;

  var setUpdatedOrder = function setUpdatedOrder(value) {
    setState({
      orderInput: value
    });
    var newOrder = Number(value);

    if (Number.isInteger(newOrder) && Object(external_lodash_["invoke"])(value, ['trim']) !== '') {
      onUpdateOrder(Number(value));
    }
  };

  var value = orderInput === null ? order : orderInput;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
    className: "editor-page-attributes__order",
    type: "number",
    label: Object(external_this_wp_i18n_["__"])('Order'),
    value: value,
    onChange: setUpdatedOrder,
    size: 6,
    onBlur: function onBlur() {
      setState({
        orderInput: null
      });
    }
  });
});

function PageAttributesOrderWithChecks(props) {
  return Object(external_this_wp_element_["createElement"])(post_type_support_check, {
    supportKeys: "page-attributes"
  }, Object(external_this_wp_element_["createElement"])(PageAttributesOrder, props));
}

/* harmony default export */ var page_attributes_order = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    order: select('core/editor').getEditedPostAttribute('menu_order')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdateOrder: function onUpdateOrder(order) {
      dispatch('core/editor').editPost({
        menu_order: order
      });
    }
  };
})])(PageAttributesOrderWithChecks));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/terms.js


/**
 * External dependencies
 */

/**
 * Returns terms in a tree form.
 *
 * @param {Array} flatTerms  Array of terms in flat format.
 *
 * @return {Array} Array of terms in tree format.
 */

function buildTermsTree(flatTerms) {
  var flatTermsWithParentAndChildren = flatTerms.map(function (term) {
    return Object(objectSpread["a" /* default */])({
      children: [],
      parent: null
    }, term);
  });
  var termsByParent = Object(external_lodash_["groupBy"])(flatTermsWithParentAndChildren, 'parent');

  if (termsByParent.null && termsByParent.null.length) {
    return flatTermsWithParentAndChildren;
  }

  var fillWithChildren = function fillWithChildren(terms) {
    return terms.map(function (term) {
      var children = termsByParent[term.id];
      return Object(objectSpread["a" /* default */])({}, term, {
        children: children && children.length ? fillWithChildren(children) : []
      });
    });
  };

  return fillWithChildren(termsByParent['0'] || []);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/page-attributes/parent.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function PageAttributesParent(_ref) {
  var parent = _ref.parent,
      postType = _ref.postType,
      items = _ref.items,
      onUpdateParent = _ref.onUpdateParent;
  var isHierarchical = Object(external_lodash_["get"])(postType, ['hierarchical'], false);
  var parentPageLabel = Object(external_lodash_["get"])(postType, ['labels', 'parent_item_colon']);
  var pageItems = items || [];

  if (!isHierarchical || !parentPageLabel || !pageItems.length) {
    return null;
  }

  var pagesTree = buildTermsTree(pageItems.map(function (item) {
    return {
      id: item.id,
      parent: item.parent,
      name: item.title.raw ? item.title.raw : "#".concat(item.id, " (").concat(Object(external_this_wp_i18n_["__"])('no title'), ")")
    };
  }));
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TreeSelect"], {
    className: "editor-page-attributes__parent",
    label: parentPageLabel,
    noOptionLabel: "(".concat(Object(external_this_wp_i18n_["__"])('no parent'), ")"),
    tree: pagesTree,
    selectedId: parent,
    onChange: onUpdateParent
  });
}
var applyWithSelect = Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core'),
      getPostType = _select.getPostType,
      getEntityRecords = _select.getEntityRecords;

  var _select2 = select('core/editor'),
      getCurrentPostId = _select2.getCurrentPostId,
      getEditedPostAttribute = _select2.getEditedPostAttribute;

  var postTypeSlug = getEditedPostAttribute('type');
  var postType = getPostType(postTypeSlug);
  var postId = getCurrentPostId();
  var isHierarchical = Object(external_lodash_["get"])(postType, ['hierarchical'], false);
  var query = {
    per_page: -1,
    exclude: postId,
    parent_exclude: postId,
    orderby: 'menu_order',
    order: 'asc'
  };
  return {
    parent: getEditedPostAttribute('parent'),
    items: isHierarchical ? getEntityRecords('postType', postTypeSlug, query) : [],
    postType: postType
  };
});
var applyWithDispatch = Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      editPost = _dispatch.editPost;

  return {
    onUpdateParent: function onUpdateParent(parent) {
      editPost({
        parent: parent || 0
      });
    }
  };
});
/* harmony default export */ var page_attributes_parent = (Object(external_this_wp_compose_["compose"])([applyWithSelect, applyWithDispatch])(PageAttributesParent));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/page-attributes/template.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





function PageTemplate(_ref) {
  var availableTemplates = _ref.availableTemplates,
      selectedTemplate = _ref.selectedTemplate,
      onUpdate = _ref.onUpdate;

  if (Object(external_lodash_["isEmpty"])(availableTemplates)) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
    label: Object(external_this_wp_i18n_["__"])('Template:'),
    value: selectedTemplate,
    onChange: onUpdate,
    className: "editor-page-attributes__template",
    options: Object(external_lodash_["map"])(availableTemplates, function (templateName, templateSlug) {
      return {
        value: templateSlug,
        label: templateName
      };
    })
  });
}
/* harmony default export */ var page_attributes_template = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute,
      getEditorSettings = _select.getEditorSettings;

  var _getEditorSettings = getEditorSettings(),
      availableTemplates = _getEditorSettings.availableTemplates;

  return {
    selectedTemplate: getEditedPostAttribute('template'),
    availableTemplates: availableTemplates
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdate: function onUpdate(templateSlug) {
      dispatch('core/editor').editPost({
        template: templateSlug || ''
      });
    }
  };
}))(PageTemplate));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/check.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function PostAuthorCheck(_ref) {
  var hasAssignAuthorAction = _ref.hasAssignAuthorAction,
      authors = _ref.authors,
      children = _ref.children;

  if (!hasAssignAuthorAction || authors.length < 2) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(post_type_support_check, {
    supportKeys: "author"
  }, children);
}
/* harmony default export */ var post_author_check = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var post = select('core/editor').getCurrentPost();
  return {
    hasAssignAuthorAction: Object(external_lodash_["get"])(post, ['_links', 'wp:action-assign-author'], false),
    postType: select('core/editor').getCurrentPostType(),
    authors: select('core').getAuthors()
  };
}), external_this_wp_compose_["withInstanceId"]])(PostAuthorCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/index.js








/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var post_author_PostAuthor =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostAuthor, _Component);

  function PostAuthor() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostAuthor);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostAuthor).apply(this, arguments));
    _this.setAuthorId = _this.setAuthorId.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(PostAuthor, [{
    key: "setAuthorId",
    value: function setAuthorId(event) {
      var onUpdateAuthor = this.props.onUpdateAuthor;
      var value = event.target.value;
      onUpdateAuthor(Number(value));
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          postAuthor = _this$props.postAuthor,
          instanceId = _this$props.instanceId,
          authors = _this$props.authors;
      var selectId = 'post-author-selector-' + instanceId; // Disable reason: A select with an onchange throws a warning

      /* eslint-disable jsx-a11y/no-onchange */

      return Object(external_this_wp_element_["createElement"])(post_author_check, null, Object(external_this_wp_element_["createElement"])("label", {
        htmlFor: selectId
      }, Object(external_this_wp_i18n_["__"])('Author')), Object(external_this_wp_element_["createElement"])("select", {
        id: selectId,
        value: postAuthor,
        onChange: this.setAuthorId,
        className: "editor-post-author__select"
      }, authors.map(function (author) {
        return Object(external_this_wp_element_["createElement"])("option", {
          key: author.id,
          value: author.id
        }, author.name);
      })));
      /* eslint-enable jsx-a11y/no-onchange */
    }
  }]);

  return PostAuthor;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var post_author = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    postAuthor: select('core/editor').getEditedPostAttribute('author'),
    authors: select('core').getAuthors()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdateAuthor: function onUpdateAuthor(author) {
      dispatch('core/editor').editPost({
        author: author
      });
    }
  };
}), external_this_wp_compose_["withInstanceId"]])(post_author_PostAuthor));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-comments/index.js



/**
 * WordPress dependencies
 */





function PostComments(_ref) {
  var _ref$commentStatus = _ref.commentStatus,
      commentStatus = _ref$commentStatus === void 0 ? 'open' : _ref$commentStatus,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["commentStatus"]);

  var onToggleComments = function onToggleComments() {
    return props.editPost({
      comment_status: commentStatus === 'open' ? 'closed' : 'open'
    });
  };

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
    label: Object(external_this_wp_i18n_["__"])('Allow Comments'),
    checked: commentStatus === 'open',
    onChange: onToggleComments
  });
}

/* harmony default export */ var post_comments = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    commentStatus: select('core/editor').getEditedPostAttribute('comment_status')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    editPost: dispatch('core/editor').editPost
  };
})])(PostComments));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-excerpt/index.js


/**
 * WordPress dependencies
 */





function PostExcerpt(_ref) {
  var excerpt = _ref.excerpt,
      onUpdateExcerpt = _ref.onUpdateExcerpt;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "editor-post-excerpt"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextareaControl"], {
    label: Object(external_this_wp_i18n_["__"])('Write an excerpt (optional)'),
    className: "editor-post-excerpt__textarea",
    onChange: function onChange(value) {
      return onUpdateExcerpt(value);
    },
    value: excerpt
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
    href: Object(external_this_wp_i18n_["__"])('https://codex.wordpress.org/Excerpt')
  }, Object(external_this_wp_i18n_["__"])('Learn more about manual excerpts')));
}

/* harmony default export */ var post_excerpt = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    excerpt: select('core/editor').getEditedPostAttribute('excerpt')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdateExcerpt: function onUpdateExcerpt(excerpt) {
      dispatch('core/editor').editPost({
        excerpt: excerpt
      });
    }
  };
})])(PostExcerpt));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-excerpt/check.js



/**
 * Internal dependencies
 */


function PostExcerptCheck(props) {
  return Object(external_this_wp_element_["createElement"])(post_type_support_check, Object(esm_extends["a" /* default */])({}, props, {
    supportKeys: "excerpt"
  }));
}

/* harmony default export */ var post_excerpt_check = (PostExcerptCheck);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/theme-support-check/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function ThemeSupportCheck(_ref) {
  var themeSupports = _ref.themeSupports,
      children = _ref.children,
      postType = _ref.postType,
      supportKeys = _ref.supportKeys;
  var isSupported = Object(external_lodash_["some"])(Object(external_lodash_["castArray"])(supportKeys), function (key) {
    var supported = Object(external_lodash_["get"])(themeSupports, [key], false); // 'post-thumbnails' can be boolean or an array of post types.
    // In the latter case, we need to verify `postType` exists
    // within `supported`. If `postType` isn't passed, then the check
    // should fail.

    if ('post-thumbnails' === key && Object(external_lodash_["isArray"])(supported)) {
      return Object(external_lodash_["includes"])(supported, postType);
    }

    return supported;
  });

  if (!isSupported) {
    return null;
  }

  return children;
}
/* harmony default export */ var theme_support_check = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core'),
      getThemeSupports = _select.getThemeSupports;

  var _select2 = select('core/editor'),
      getEditedPostAttribute = _select2.getEditedPostAttribute;

  return {
    postType: getEditedPostAttribute('type'),
    themeSupports: getThemeSupports()
  };
})(ThemeSupportCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-featured-image/check.js



/**
 * Internal dependencies
 */



function PostFeaturedImageCheck(props) {
  return Object(external_this_wp_element_["createElement"])(theme_support_check, {
    supportKeys: "post-thumbnails"
  }, Object(external_this_wp_element_["createElement"])(post_type_support_check, Object(esm_extends["a" /* default */])({}, props, {
    supportKeys: "thumbnail"
  })));
}

/* harmony default export */ var post_featured_image_check = (PostFeaturedImageCheck);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-featured-image/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


var ALLOWED_MEDIA_TYPES = ['image']; // Used when labels from post type were not yet loaded or when they are not present.

var DEFAULT_FEATURE_IMAGE_LABEL = Object(external_this_wp_i18n_["__"])('Featured Image');

var DEFAULT_SET_FEATURE_IMAGE_LABEL = Object(external_this_wp_i18n_["__"])('Set featured image');

var DEFAULT_REMOVE_FEATURE_IMAGE_LABEL = Object(external_this_wp_i18n_["__"])('Remove image');

function PostFeaturedImage(_ref) {
  var currentPostId = _ref.currentPostId,
      featuredImageId = _ref.featuredImageId,
      onUpdateImage = _ref.onUpdateImage,
      onRemoveImage = _ref.onRemoveImage,
      media = _ref.media,
      postType = _ref.postType;
  var postLabel = Object(external_lodash_["get"])(postType, ['labels'], {});
  var instructions = Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('To edit the featured image, you need permission to upload media.'));
  var mediaWidth, mediaHeight, mediaSourceUrl;

  if (media) {
    var mediaSize = Object(external_this_wp_hooks_["applyFilters"])('editor.PostFeaturedImage.imageSize', 'post-thumbnail', media.id, currentPostId);

    if (Object(external_lodash_["has"])(media, ['media_details', 'sizes', mediaSize])) {
      mediaWidth = media.media_details.sizes[mediaSize].width;
      mediaHeight = media.media_details.sizes[mediaSize].height;
      mediaSourceUrl = media.media_details.sizes[mediaSize].source_url;
    } else {
      mediaWidth = media.media_details.width;
      mediaHeight = media.media_details.height;
      mediaSourceUrl = media.source_url;
    }
  }

  return Object(external_this_wp_element_["createElement"])(post_featured_image_check, null, Object(external_this_wp_element_["createElement"])("div", {
    className: "editor-post-featured-image"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUploadCheck"], {
    fallback: instructions
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
    title: postLabel.featured_image || DEFAULT_FEATURE_IMAGE_LABEL,
    onSelect: onUpdateImage,
    allowedTypes: ALLOWED_MEDIA_TYPES,
    modalClass: !featuredImageId ? 'editor-post-featured-image__media-modal' : 'editor-post-featured-image__media-modal',
    render: function render(_ref2) {
      var open = _ref2.open;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: !featuredImageId ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview',
        onClick: open,
        "aria-label": !featuredImageId ? null : Object(external_this_wp_i18n_["__"])('Edit or update the image')
      }, !!featuredImageId && media && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ResponsiveWrapper"], {
        naturalWidth: mediaWidth,
        naturalHeight: mediaHeight
      }, Object(external_this_wp_element_["createElement"])("img", {
        src: mediaSourceUrl,
        alt: ""
      })), !!featuredImageId && !media && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null), !featuredImageId && (postLabel.set_featured_image || DEFAULT_SET_FEATURE_IMAGE_LABEL));
    },
    value: featuredImageId
  })), !!featuredImageId && media && !media.isLoading && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
    title: postLabel.featured_image || DEFAULT_FEATURE_IMAGE_LABEL,
    onSelect: onUpdateImage,
    allowedTypes: ALLOWED_MEDIA_TYPES,
    modalClass: "editor-post-featured-image__media-modal",
    render: function render(_ref3) {
      var open = _ref3.open;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        onClick: open,
        isDefault: true,
        isLarge: true
      }, Object(external_this_wp_i18n_["__"])('Replace image'));
    }
  })), !!featuredImageId && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    onClick: onRemoveImage,
    isLink: true,
    isDestructive: true
  }, postLabel.remove_featured_image || DEFAULT_REMOVE_FEATURE_IMAGE_LABEL))));
}

var post_featured_image_applyWithSelect = Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core'),
      getMedia = _select.getMedia,
      getPostType = _select.getPostType;

  var _select2 = select('core/editor'),
      getCurrentPostId = _select2.getCurrentPostId,
      getEditedPostAttribute = _select2.getEditedPostAttribute;

  var featuredImageId = getEditedPostAttribute('featured_media');
  return {
    media: featuredImageId ? getMedia(featuredImageId) : null,
    currentPostId: getCurrentPostId(),
    postType: getPostType(getEditedPostAttribute('type')),
    featuredImageId: featuredImageId
  };
});
var post_featured_image_applyWithDispatch = Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      editPost = _dispatch.editPost;

  return {
    onUpdateImage: function onUpdateImage(image) {
      editPost({
        featured_media: image.id
      });
    },
    onRemoveImage: function onRemoveImage() {
      editPost({
        featured_media: 0
      });
    }
  };
});
/* harmony default export */ var post_featured_image = (Object(external_this_wp_compose_["compose"])(post_featured_image_applyWithSelect, post_featured_image_applyWithDispatch, Object(external_this_wp_components_["withFilters"])('editor.PostFeaturedImage'))(PostFeaturedImage));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-format/check.js




/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function PostFormatCheck(_ref) {
  var disablePostFormats = _ref.disablePostFormats,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["disablePostFormats"]);

  return !disablePostFormats && Object(external_this_wp_element_["createElement"])(post_type_support_check, Object(esm_extends["a" /* default */])({}, props, {
    supportKeys: "post-formats"
  }));
}

/* harmony default export */ var post_format_check = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var editorSettings = select('core/editor').getEditorSettings();
  return {
    disablePostFormats: editorSettings.disablePostFormats
  };
})(PostFormatCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-format/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


var POST_FORMATS = [{
  id: 'aside',
  caption: Object(external_this_wp_i18n_["__"])('Aside')
}, {
  id: 'gallery',
  caption: Object(external_this_wp_i18n_["__"])('Gallery')
}, {
  id: 'link',
  caption: Object(external_this_wp_i18n_["__"])('Link')
}, {
  id: 'image',
  caption: Object(external_this_wp_i18n_["__"])('Image')
}, {
  id: 'quote',
  caption: Object(external_this_wp_i18n_["__"])('Quote')
}, {
  id: 'standard',
  caption: Object(external_this_wp_i18n_["__"])('Standard')
}, {
  id: 'status',
  caption: Object(external_this_wp_i18n_["__"])('Status')
}, {
  id: 'video',
  caption: Object(external_this_wp_i18n_["__"])('Video')
}, {
  id: 'audio',
  caption: Object(external_this_wp_i18n_["__"])('Audio')
}, {
  id: 'chat',
  caption: Object(external_this_wp_i18n_["__"])('Chat')
}];

function PostFormat(_ref) {
  var onUpdatePostFormat = _ref.onUpdatePostFormat,
      _ref$postFormat = _ref.postFormat,
      postFormat = _ref$postFormat === void 0 ? 'standard' : _ref$postFormat,
      supportedFormats = _ref.supportedFormats,
      suggestedFormat = _ref.suggestedFormat,
      instanceId = _ref.instanceId;
  var postFormatSelectorId = 'post-format-selector-' + instanceId;
  var formats = POST_FORMATS.filter(function (format) {
    return Object(external_lodash_["includes"])(supportedFormats, format.id);
  });
  var suggestion = Object(external_lodash_["find"])(formats, function (format) {
    return format.id === suggestedFormat;
  }); // Disable reason: We need to change the value immiediately to show/hide the suggestion if needed

  /* eslint-disable jsx-a11y/no-onchange */

  return Object(external_this_wp_element_["createElement"])(post_format_check, null, Object(external_this_wp_element_["createElement"])("div", {
    className: "editor-post-format"
  }, Object(external_this_wp_element_["createElement"])("div", {
    className: "editor-post-format__content"
  }, Object(external_this_wp_element_["createElement"])("label", {
    htmlFor: postFormatSelectorId
  }, Object(external_this_wp_i18n_["__"])('Post Format')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
    value: postFormat,
    onChange: function onChange(format) {
      return onUpdatePostFormat(format);
    },
    id: postFormatSelectorId,
    options: formats.map(function (format) {
      return {
        label: format.caption,
        value: format.id
      };
    })
  })), suggestion && suggestion.id !== postFormat && Object(external_this_wp_element_["createElement"])("div", {
    className: "editor-post-format__suggestion"
  }, Object(external_this_wp_i18n_["__"])('Suggestion:'), ' ', Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isLink: true,
    onClick: function onClick() {
      return onUpdatePostFormat(suggestion.id);
    }
  }, suggestion.caption))));
  /* eslint-enable jsx-a11y/no-onchange */
}

/* harmony default export */ var post_format = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute,
      getSuggestedPostFormat = _select.getSuggestedPostFormat;

  var postFormat = getEditedPostAttribute('format');
  var themeSupports = select('core').getThemeSupports(); // Ensure current format is always in the set.
  // The current format may not be a format supported by the theme.

  var supportedFormats = Object(external_lodash_["union"])([postFormat], Object(external_lodash_["get"])(themeSupports, ['formats'], []));
  return {
    postFormat: postFormat,
    supportedFormats: supportedFormats,
    suggestedFormat: getSuggestedPostFormat()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdatePostFormat: function onUpdatePostFormat(postFormat) {
      dispatch('core/editor').editPost({
        format: postFormat
      });
    }
  };
}), external_this_wp_compose_["withInstanceId"]])(PostFormat));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-last-revision/check.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


function PostLastRevisionCheck(_ref) {
  var lastRevisionId = _ref.lastRevisionId,
      revisionsCount = _ref.revisionsCount,
      children = _ref.children;

  if (!lastRevisionId || revisionsCount < 2) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(post_type_support_check, {
    supportKeys: "revisions"
  }, children);
}
/* harmony default export */ var post_last_revision_check = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getCurrentPostLastRevisionId = _select.getCurrentPostLastRevisionId,
      getCurrentPostRevisionsCount = _select.getCurrentPostRevisionsCount;

  return {
    lastRevisionId: getCurrentPostLastRevisionId(),
    revisionsCount: getCurrentPostRevisionsCount()
  };
})(PostLastRevisionCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/url.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Returns the URL of a WPAdmin Page.
 *
 * TODO: This should be moved to a module less specific to the editor.
 *
 * @param {string} page  Page to navigate to.
 * @param {Object} query Query Args.
 *
 * @return {string} WPAdmin URL.
 */

function getWPAdminURL(page, query) {
  return Object(external_this_wp_url_["addQueryArgs"])(page, query);
}
/**
 * Performs some basic cleanup of a string for use as a post slug
 *
 * This replicates some of what santize_title() does in WordPress core, but
 * is only designed to approximate what the slug will be.
 *
 * Converts whitespace, periods, forward slashes and underscores to hyphens.
 * Converts Latin-1 Supplement and Latin Extended-A letters to basic Latin
 * letters. Removes combining diacritical marks. Converts remaining string
 * to lowercase. It does not touch octets, HTML entities, or other encoded
 * characters.
 *
 * @param {string} string Title or slug to be processed
 *
 * @return {string} Processed string
 */

function cleanForSlug(string) {
  return Object(external_lodash_["toLower"])(Object(external_lodash_["deburr"])(Object(external_lodash_["trim"])(string.replace(/[\s\./_]+/g, '-'), '-')));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-last-revision/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function LastRevision(_ref) {
  var lastRevisionId = _ref.lastRevisionId,
      revisionsCount = _ref.revisionsCount;
  return Object(external_this_wp_element_["createElement"])(post_last_revision_check, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    href: getWPAdminURL('revision.php', {
      revision: lastRevisionId,
      gutenberg: true
    }),
    className: "editor-post-last-revision__title",
    icon: "backup"
  }, Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('%d Revision', '%d Revisions', revisionsCount), revisionsCount)));
}

/* harmony default export */ var post_last_revision = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getCurrentPostLastRevisionId = _select.getCurrentPostLastRevisionId,
      getCurrentPostRevisionsCount = _select.getCurrentPostRevisionsCount;

  return {
    lastRevisionId: getCurrentPostLastRevisionId(),
    revisionsCount: getCurrentPostRevisionsCount()
  };
})(LastRevision));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-preview-button/index.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */









function writeInterstitialMessage(targetDocument) {
  var markup = Object(external_this_wp_element_["renderToString"])(Object(external_this_wp_element_["createElement"])("div", {
    className: "editor-post-preview-button__interstitial-message"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 96 96"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    className: "outer",
    d: "M48 12c19.9 0 36 16.1 36 36S67.9 84 48 84 12 67.9 12 48s16.1-36 36-36",
    fill: "none"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    className: "inner",
    d: "M69.5 46.4c0-3.9-1.4-6.7-2.6-8.8-1.6-2.6-3.1-4.9-3.1-7.5 0-2.9 2.2-5.7 5.4-5.7h.4C63.9 19.2 56.4 16 48 16c-11.2 0-21 5.7-26.7 14.4h2.1c3.3 0 8.5-.4 8.5-.4 1.7-.1 1.9 2.4.2 2.6 0 0-1.7.2-3.7.3L40 67.5l7-20.9L42 33c-1.7-.1-3.3-.3-3.3-.3-1.7-.1-1.5-2.7.2-2.6 0 0 5.3.4 8.4.4 3.3 0 8.5-.4 8.5-.4 1.7-.1 1.9 2.4.2 2.6 0 0-1.7.2-3.7.3l11.5 34.3 3.3-10.4c1.6-4.5 2.4-7.8 2.4-10.5zM16.1 48c0 12.6 7.3 23.5 18 28.7L18.8 35c-1.7 4-2.7 8.4-2.7 13zm32.5 2.8L39 78.6c2.9.8 5.9 1.3 9 1.3 3.7 0 7.3-.6 10.6-1.8-.1-.1-.2-.3-.2-.4l-9.8-26.9zM76.2 36c0 3.2-.6 6.9-2.4 11.4L64 75.6c9.5-5.5 15.9-15.8 15.9-27.6 0-5.5-1.4-10.8-3.9-15.3.1 1 .2 2.1.2 3.3z",
    fill: "none"
  })), Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('Generating preview…'))));
  markup += "\n\t\t<style>\n\t\t\tbody {\n\t\t\t\tmargin: 0;\n\t\t\t}\n\t\t\t.editor-post-preview-button__interstitial-message {\n\t\t\t\tdisplay: flex;\n\t\t\t\tflex-direction: column;\n\t\t\t\talign-items: center;\n\t\t\t\tjustify-content: center;\n\t\t\t\theight: 100vh;\n\t\t\t\twidth: 100vw;\n\t\t\t}\n\t\t\t@-webkit-keyframes paint {\n\t\t\t\t0% {\n\t\t\t\t\tstroke-dashoffset: 0;\n\t\t\t\t}\n\t\t\t}\n\t\t\t@-moz-keyframes paint {\n\t\t\t\t0% {\n\t\t\t\t\tstroke-dashoffset: 0;\n\t\t\t\t}\n\t\t\t}\n\t\t\t@-o-keyframes paint {\n\t\t\t\t0% {\n\t\t\t\t\tstroke-dashoffset: 0;\n\t\t\t\t}\n\t\t\t}\n\t\t\t@keyframes paint {\n\t\t\t\t0% {\n\t\t\t\t\tstroke-dashoffset: 0;\n\t\t\t\t}\n\t\t\t}\n\t\t\t.editor-post-preview-button__interstitial-message svg {\n\t\t\t\twidth: 192px;\n\t\t\t\theight: 192px;\n\t\t\t\tstroke: #555d66;\n\t\t\t\tstroke-width: 0.75;\n\t\t\t}\n\t\t\t.editor-post-preview-button__interstitial-message svg .outer,\n\t\t\t.editor-post-preview-button__interstitial-message svg .inner {\n\t\t\t\tstroke-dasharray: 280;\n\t\t\t\tstroke-dashoffset: 280;\n\t\t\t\t-webkit-animation: paint 1.5s ease infinite alternate;\n\t\t\t\t-moz-animation: paint 1.5s ease infinite alternate;\n\t\t\t\t-o-animation: paint 1.5s ease infinite alternate;\n\t\t\t\tanimation: paint 1.5s ease infinite alternate;\n\t\t\t}\n\t\t\tp {\n\t\t\t\ttext-align: center;\n\t\t\t\tfont-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Oxygen-Sans, Ubuntu, Cantarell, \"Helvetica Neue\", sans-serif;\n\t\t\t}\n\t\t</style>\n\t";
  /**
   * Filters the interstitial message shown when generating previews.
   *
   * @param {String} markup The preview interstitial markup.
   */

  markup = Object(external_this_wp_hooks_["applyFilters"])('editor.PostPreview.interstitialMarkup', markup);
  targetDocument.write(markup);
  targetDocument.title = Object(external_this_wp_i18n_["__"])('Generating preview…');
  targetDocument.close();
}

var post_preview_button_PostPreviewButton =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostPreviewButton, _Component);

  function PostPreviewButton() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostPreviewButton);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostPreviewButton).apply(this, arguments));
    _this.openPreviewWindow = _this.openPreviewWindow.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(PostPreviewButton, [{
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var previewLink = this.props.previewLink; // This relies on the window being responsible to unset itself when
      // navigation occurs or a new preview window is opened, to avoid
      // unintentional forceful redirects.

      if (previewLink && !prevProps.previewLink) {
        this.setPreviewWindowLink(previewLink);
      }
    }
    /**
     * Sets the preview window's location to the given URL, if a preview window
     * exists and is not closed.
     *
     * @param {string} url URL to assign as preview window location.
     */

  }, {
    key: "setPreviewWindowLink",
    value: function setPreviewWindowLink(url) {
      var previewWindow = this.previewWindow;

      if (previewWindow && !previewWindow.closed) {
        previewWindow.location = url;
      }
    }
  }, {
    key: "getWindowTarget",
    value: function getWindowTarget() {
      var postId = this.props.postId;
      return "wp-preview-".concat(postId);
    }
  }, {
    key: "openPreviewWindow",
    value: function openPreviewWindow(event) {
      // Our Preview button has its 'href' and 'target' set correctly for a11y
      // purposes. Unfortunately, though, we can't rely on the default 'click'
      // handler since sometimes it incorrectly opens a new tab instead of reusing
      // the existing one.
      // https://github.com/WordPress/gutenberg/pull/8330
      event.preventDefault(); // Open up a Preview tab if needed. This is where we'll show the preview.

      if (!this.previewWindow || this.previewWindow.closed) {
        this.previewWindow = window.open('', this.getWindowTarget());
      } // Focus the Preview tab. This might not do anything, depending on the browser's
      // and user's preferences.
      // https://html.spec.whatwg.org/multipage/interaction.html#dom-window-focus


      this.previewWindow.focus(); // If we don't need to autosave the post before previewing, then we simply
      // load the Preview URL in the Preview tab.

      if (!this.props.isAutosaveable) {
        this.setPreviewWindowLink(event.target.href);
        return;
      } // Request an autosave. This happens asynchronously and causes the component
      // to update when finished.


      if (this.props.isDraft) {
        this.props.savePost({
          isPreview: true
        });
      } else {
        this.props.autosave({
          isPreview: true
        });
      } // Display a 'Generating preview' message in the Preview tab while we wait for the
      // autosave to finish.


      writeInterstitialMessage(this.previewWindow.document);
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          previewLink = _this$props.previewLink,
          currentPostLink = _this$props.currentPostLink,
          isSaveable = _this$props.isSaveable; // Link to the `?preview=true` URL if we have it, since this lets us see
      // changes that were autosaved since the post was last published. Otherwise,
      // just link to the post's URL.

      var href = previewLink || currentPostLink;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        isLarge: true,
        className: "editor-post-preview",
        href: href,
        target: this.getWindowTarget(),
        disabled: !isSaveable,
        onClick: this.openPreviewWindow
      }, Object(external_this_wp_i18n_["_x"])('Preview', 'imperative verb'), Object(external_this_wp_element_["createElement"])("span", {
        className: "screen-reader-text"
      },
      /* translators: accessibility text */
      Object(external_this_wp_i18n_["__"])('(opens in a new tab)')), Object(external_this_wp_element_["createElement"])(external_this_wp_nux_["DotTip"], {
        tipId: "core/editor.preview"
      }, Object(external_this_wp_i18n_["__"])('Click “Preview” to load a preview of this page, so you can make sure you’re happy with your blocks.')));
    }
  }]);

  return PostPreviewButton;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var post_preview_button = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var forcePreviewLink = _ref.forcePreviewLink,
      forceIsAutosaveable = _ref.forceIsAutosaveable;

  var _select = select('core/editor'),
      getCurrentPostId = _select.getCurrentPostId,
      getCurrentPostAttribute = _select.getCurrentPostAttribute,
      getEditedPostAttribute = _select.getEditedPostAttribute,
      isEditedPostSaveable = _select.isEditedPostSaveable,
      isEditedPostAutosaveable = _select.isEditedPostAutosaveable,
      getEditedPostPreviewLink = _select.getEditedPostPreviewLink;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  var previewLink = getEditedPostPreviewLink();
  var postType = getPostType(getEditedPostAttribute('type'));
  return {
    postId: getCurrentPostId(),
    currentPostLink: getCurrentPostAttribute('link'),
    previewLink: forcePreviewLink !== undefined ? forcePreviewLink : previewLink,
    isSaveable: isEditedPostSaveable(),
    isAutosaveable: forceIsAutosaveable || isEditedPostAutosaveable(),
    isViewable: Object(external_lodash_["get"])(postType, ['viewable'], false),
    isDraft: ['draft', 'auto-draft'].indexOf(getEditedPostAttribute('status')) !== -1
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    autosave: dispatch('core/editor').autosave,
    savePost: dispatch('core/editor').savePost
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isViewable = _ref2.isViewable;
  return isViewable;
})])(post_preview_button_PostPreviewButton));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-locked-modal/index.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */




var post_locked_modal_PostLockedModal =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostLockedModal, _Component);

  function PostLockedModal() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostLockedModal);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostLockedModal).apply(this, arguments));
    _this.sendPostLock = _this.sendPostLock.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.receivePostLock = _this.receivePostLock.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.releasePostLock = _this.releasePostLock.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(PostLockedModal, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var hookName = this.getHookName(); // Details on these events on the Heartbeat API docs
      // https://developer.wordpress.org/plugins/javascript/heartbeat-api/

      Object(external_this_wp_hooks_["addAction"])('heartbeat.send', hookName, this.sendPostLock);
      Object(external_this_wp_hooks_["addAction"])('heartbeat.tick', hookName, this.receivePostLock);
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      var hookName = this.getHookName();
      Object(external_this_wp_hooks_["removeAction"])('heartbeat.send', hookName);
      Object(external_this_wp_hooks_["removeAction"])('heartbeat.tick', hookName);
    }
    /**
     * Returns a `@wordpress/hooks` hook name specific to the instance of the
     * component.
     *
     * @return {string} Hook name prefix.
     */

  }, {
    key: "getHookName",
    value: function getHookName() {
      var instanceId = this.props.instanceId;
      return 'core/editor/post-locked-modal-' + instanceId;
    }
    /**
     * Keep the lock refreshed.
     *
     * When the user does not send a heartbeat in a heartbeat-tick
     * the user is no longer editing and another user can start editing.
     *
     * @param {Object} data Data to send in the heartbeat request.
     */

  }, {
    key: "sendPostLock",
    value: function sendPostLock(data) {
      var _this$props = this.props,
          isLocked = _this$props.isLocked,
          activePostLock = _this$props.activePostLock,
          postId = _this$props.postId;

      if (isLocked) {
        return;
      }

      data['wp-refresh-post-lock'] = {
        lock: activePostLock,
        post_id: postId
      };
    }
    /**
     * Refresh post locks: update the lock string or show the dialog if somebody has taken over editing.
     *
     * @param {Object} data Data received in the heartbeat request
     */

  }, {
    key: "receivePostLock",
    value: function receivePostLock(data) {
      if (!data['wp-refresh-post-lock']) {
        return;
      }

      var _this$props2 = this.props,
          autosave = _this$props2.autosave,
          updatePostLock = _this$props2.updatePostLock;
      var received = data['wp-refresh-post-lock'];

      if (received.lock_error) {
        // Auto save and display the takeover modal.
        autosave();
        updatePostLock({
          isLocked: true,
          isTakeover: true,
          user: {
            avatar: received.lock_error.avatar_src
          }
        });
      } else if (received.new_lock) {
        updatePostLock({
          isLocked: false,
          activePostLock: received.new_lock
        });
      }
    }
    /**
     * Unlock the post before the window is exited.
     */

  }, {
    key: "releasePostLock",
    value: function releasePostLock() {
      var _this$props3 = this.props,
          isLocked = _this$props3.isLocked,
          activePostLock = _this$props3.activePostLock,
          postLockUtils = _this$props3.postLockUtils,
          postId = _this$props3.postId;

      if (isLocked || !activePostLock) {
        return;
      }

      var data = new window.FormData();
      data.append('action', 'wp-remove-post-lock');
      data.append('_wpnonce', postLockUtils.unlockNonce);
      data.append('post_ID', postId);
      data.append('active_post_lock', activePostLock);
      var xhr = new window.XMLHttpRequest();
      xhr.open('POST', postLockUtils.ajaxUrl, false);
      xhr.send(data);
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props4 = this.props,
          user = _this$props4.user,
          postId = _this$props4.postId,
          isLocked = _this$props4.isLocked,
          isTakeover = _this$props4.isTakeover,
          postLockUtils = _this$props4.postLockUtils,
          postType = _this$props4.postType;

      if (!isLocked) {
        return null;
      }

      var userDisplayName = user.name;
      var userAvatar = user.avatar;
      var unlockUrl = Object(external_this_wp_url_["addQueryArgs"])('post.php', {
        'get-post-lock': '1',
        lockKey: true,
        post: postId,
        action: 'edit',
        _wpnonce: postLockUtils.nonce
      });
      var allPostsUrl = getWPAdminURL('edit.php', {
        post_type: Object(external_lodash_["get"])(postType, ['slug'])
      });

      var allPostsLabel = Object(external_this_wp_i18n_["__"])('Exit the Editor');

      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Modal"], {
        title: isTakeover ? Object(external_this_wp_i18n_["__"])('Someone else has taken over this post.') : Object(external_this_wp_i18n_["__"])('This post is already being edited.'),
        focusOnMount: true,
        shouldCloseOnClickOutside: false,
        shouldCloseOnEsc: false,
        isDismissable: false,
        className: "editor-post-locked-modal"
      }, !!userAvatar && Object(external_this_wp_element_["createElement"])("img", {
        src: userAvatar,
        alt: Object(external_this_wp_i18n_["__"])('Avatar'),
        className: "editor-post-locked-modal__avatar"
      }), !!isTakeover && Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])("div", null, userDisplayName ? Object(external_this_wp_i18n_["sprintf"])(
      /* translators: %s: user's display name */
      Object(external_this_wp_i18n_["__"])('%s now has editing control of this post. Don’t worry, your changes up to this moment have been saved.'), userDisplayName) : Object(external_this_wp_i18n_["__"])('Another user now has editing control of this post. Don’t worry, your changes up to this moment have been saved.')), Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-locked-modal__buttons"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        isPrimary: true,
        isLarge: true,
        href: allPostsUrl
      }, allPostsLabel))), !isTakeover && Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])("div", null, userDisplayName ? Object(external_this_wp_i18n_["sprintf"])(
      /* translators: %s: user's display name */
      Object(external_this_wp_i18n_["__"])('%s is currently working on this post, which means you cannot make changes, unless you take over.'), userDisplayName) : Object(external_this_wp_i18n_["__"])('Another user is currently working on this post, which means you cannot make changes, unless you take over.')), Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-locked-modal__buttons"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        isDefault: true,
        isLarge: true,
        href: allPostsUrl
      }, allPostsLabel), Object(external_this_wp_element_["createElement"])(post_preview_button, null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        isPrimary: true,
        isLarge: true,
        href: unlockUrl
      }, Object(external_this_wp_i18n_["__"])('Take Over')))));
    }
  }]);

  return PostLockedModal;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var post_locked_modal = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isPostLocked = _select.isPostLocked,
      isPostLockTakeover = _select.isPostLockTakeover,
      getPostLockUser = _select.getPostLockUser,
      getCurrentPostId = _select.getCurrentPostId,
      getActivePostLock = _select.getActivePostLock,
      getEditedPostAttribute = _select.getEditedPostAttribute,
      getEditorSettings = _select.getEditorSettings;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  return {
    isLocked: isPostLocked(),
    isTakeover: isPostLockTakeover(),
    user: getPostLockUser(),
    postId: getCurrentPostId(),
    postLockUtils: getEditorSettings().postLockUtils,
    activePostLock: getActivePostLock(),
    postType: getPostType(getEditedPostAttribute('type'))
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      autosave = _dispatch.autosave,
      updatePostLock = _dispatch.updatePostLock;

  return {
    autosave: autosave,
    updatePostLock: updatePostLock
  };
}), external_this_wp_compose_["withInstanceId"], Object(external_this_wp_compose_["withGlobalEvents"])({
  beforeunload: 'releasePostLock'
}))(post_locked_modal_PostLockedModal));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-pending-status/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PostPendingStatusCheck(_ref) {
  var hasPublishAction = _ref.hasPublishAction,
      isPublished = _ref.isPublished,
      children = _ref.children;

  if (isPublished || !hasPublishAction) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_pending_status_check = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isCurrentPostPublished = _select.isCurrentPostPublished,
      getCurrentPostType = _select.getCurrentPostType,
      getCurrentPost = _select.getCurrentPost;

  return {
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    isPublished: isCurrentPostPublished(),
    postType: getCurrentPostType()
  };
}))(PostPendingStatusCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-pending-status/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function PostPendingStatus(_ref) {
  var status = _ref.status,
      onUpdateStatus = _ref.onUpdateStatus;

  var togglePendingStatus = function togglePendingStatus() {
    var updatedStatus = status === 'pending' ? 'draft' : 'pending';
    onUpdateStatus(updatedStatus);
  };

  return Object(external_this_wp_element_["createElement"])(post_pending_status_check, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
    label: Object(external_this_wp_i18n_["__"])('Pending Review'),
    checked: status === 'pending',
    onChange: togglePendingStatus
  }));
}
/* harmony default export */ var post_pending_status = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    status: select('core/editor').getEditedPostAttribute('status')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdateStatus: function onUpdateStatus(status) {
      dispatch('core/editor').editPost({
        status: status
      });
    }
  };
}))(PostPendingStatus));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-pingbacks/index.js



/**
 * WordPress dependencies
 */





function PostPingbacks(_ref) {
  var _ref$pingStatus = _ref.pingStatus,
      pingStatus = _ref$pingStatus === void 0 ? 'open' : _ref$pingStatus,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["pingStatus"]);

  var onTogglePingback = function onTogglePingback() {
    return props.editPost({
      ping_status: pingStatus === 'open' ? 'closed' : 'open'
    });
  };

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
    label: Object(external_this_wp_i18n_["__"])('Allow Pingbacks & Trackbacks'),
    checked: pingStatus === 'open',
    onChange: onTogglePingback
  });
}

/* harmony default export */ var post_pingbacks = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    pingStatus: select('core/editor').getEditedPostAttribute('ping_status')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    editPost: dispatch('core/editor').editPost
  };
})])(PostPingbacks));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-button/label.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function PublishButtonLabel(_ref) {
  var isPublished = _ref.isPublished,
      isBeingScheduled = _ref.isBeingScheduled,
      isSaving = _ref.isSaving,
      isPublishing = _ref.isPublishing,
      hasPublishAction = _ref.hasPublishAction,
      isAutosaving = _ref.isAutosaving;

  if (isPublishing) {
    return Object(external_this_wp_i18n_["__"])('Publishing…');
  } else if (isPublished && isSaving && !isAutosaving) {
    return Object(external_this_wp_i18n_["__"])('Updating…');
  } else if (isBeingScheduled && isSaving && !isAutosaving) {
    return Object(external_this_wp_i18n_["__"])('Scheduling…');
  }

  if (!hasPublishAction) {
    return Object(external_this_wp_i18n_["__"])('Submit for Review');
  } else if (isPublished) {
    return Object(external_this_wp_i18n_["__"])('Update');
  } else if (isBeingScheduled) {
    return Object(external_this_wp_i18n_["__"])('Schedule');
  }

  return Object(external_this_wp_i18n_["__"])('Publish');
}
/* harmony default export */ var post_publish_button_label = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var forceIsSaving = _ref2.forceIsSaving;

  var _select = select('core/editor'),
      isCurrentPostPublished = _select.isCurrentPostPublished,
      isEditedPostBeingScheduled = _select.isEditedPostBeingScheduled,
      isSavingPost = _select.isSavingPost,
      isPublishingPost = _select.isPublishingPost,
      getCurrentPost = _select.getCurrentPost,
      getCurrentPostType = _select.getCurrentPostType,
      isAutosavingPost = _select.isAutosavingPost;

  return {
    isPublished: isCurrentPostPublished(),
    isBeingScheduled: isEditedPostBeingScheduled(),
    isSaving: forceIsSaving || isSavingPost(),
    isPublishing: isPublishingPost(),
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    postType: getCurrentPostType(),
    isAutosaving: isAutosavingPost()
  };
})])(PublishButtonLabel));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-button/index.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


var post_publish_button_PostPublishButton =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostPublishButton, _Component);

  function PostPublishButton(props) {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostPublishButton);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostPublishButton).call(this, props));
    _this.buttonNode = Object(external_this_wp_element_["createRef"])();
    return _this;
  }

  Object(createClass["a" /* default */])(PostPublishButton, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      if (this.props.focusOnMount) {
        this.buttonNode.current.focus();
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          forceIsDirty = _this$props.forceIsDirty,
          forceIsSaving = _this$props.forceIsSaving,
          hasPublishAction = _this$props.hasPublishAction,
          isBeingScheduled = _this$props.isBeingScheduled,
          isOpen = _this$props.isOpen,
          isPostSavingLocked = _this$props.isPostSavingLocked,
          isPublishable = _this$props.isPublishable,
          isPublished = _this$props.isPublished,
          isSaveable = _this$props.isSaveable,
          isSaving = _this$props.isSaving,
          isToggle = _this$props.isToggle,
          onSave = _this$props.onSave,
          onStatusChange = _this$props.onStatusChange,
          _this$props$onSubmit = _this$props.onSubmit,
          onSubmit = _this$props$onSubmit === void 0 ? external_lodash_["noop"] : _this$props$onSubmit,
          onToggle = _this$props.onToggle,
          visibility = _this$props.visibility;
      var isButtonDisabled = isSaving || forceIsSaving || !isSaveable || isPostSavingLocked || !isPublishable && !forceIsDirty;
      var isToggleDisabled = isPublished || isSaving || forceIsSaving || !isSaveable || !isPublishable && !forceIsDirty;
      var publishStatus;

      if (!hasPublishAction) {
        publishStatus = 'pending';
      } else if (isBeingScheduled) {
        publishStatus = 'future';
      } else if (visibility === 'private') {
        publishStatus = 'private';
      } else {
        publishStatus = 'publish';
      }

      var onClickButton = function onClickButton() {
        if (isButtonDisabled) {
          return;
        }

        onSubmit();
        onStatusChange(publishStatus);
        onSave();
      };

      var onClickToggle = function onClickToggle() {
        if (isToggleDisabled) {
          return;
        }

        onToggle();
      };

      var buttonProps = {
        'aria-disabled': isButtonDisabled,
        className: 'editor-post-publish-button',
        isBusy: isSaving && isPublished,
        isLarge: true,
        isPrimary: true,
        onClick: onClickButton
      };
      var toggleProps = {
        'aria-disabled': isToggleDisabled,
        'aria-expanded': isOpen,
        className: 'editor-post-publish-panel__toggle',
        isBusy: isSaving && isPublished,
        isPrimary: true,
        onClick: onClickToggle
      };
      var toggleChildren = isBeingScheduled ? Object(external_this_wp_i18n_["__"])('Schedule…') : Object(external_this_wp_i18n_["__"])('Publish…');
      var buttonChildren = Object(external_this_wp_element_["createElement"])(post_publish_button_label, {
        forceIsSaving: forceIsSaving
      });
      var componentProps = isToggle ? toggleProps : buttonProps;
      var componentChildren = isToggle ? toggleChildren : buttonChildren;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], Object(esm_extends["a" /* default */])({
        ref: this.buttonNode
      }, componentProps), componentChildren, Object(external_this_wp_element_["createElement"])(external_this_wp_nux_["DotTip"], {
        tipId: "core/editor.publish"
      }, Object(external_this_wp_i18n_["__"])('Finished writing? That’s great, let’s get this published right now. Just click “Publish” and you’re good to go.')));
    }
  }]);

  return PostPublishButton;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var post_publish_button = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isSavingPost = _select.isSavingPost,
      isEditedPostBeingScheduled = _select.isEditedPostBeingScheduled,
      getEditedPostVisibility = _select.getEditedPostVisibility,
      isCurrentPostPublished = _select.isCurrentPostPublished,
      isEditedPostSaveable = _select.isEditedPostSaveable,
      isEditedPostPublishable = _select.isEditedPostPublishable,
      isPostSavingLocked = _select.isPostSavingLocked,
      getCurrentPost = _select.getCurrentPost,
      getCurrentPostType = _select.getCurrentPostType;

  return {
    isSaving: isSavingPost(),
    isBeingScheduled: isEditedPostBeingScheduled(),
    visibility: getEditedPostVisibility(),
    isSaveable: isEditedPostSaveable(),
    isPostSavingLocked: isPostSavingLocked(),
    isPublishable: isEditedPostPublishable(),
    isPublished: isCurrentPostPublished(),
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    postType: getCurrentPostType()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      editPost = _dispatch.editPost,
      savePost = _dispatch.savePost;

  return {
    onStatusChange: function onStatusChange(status) {
      return editPost({
        status: status
      });
    },
    onSave: savePost
  };
})])(post_publish_button_PostPublishButton));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/utils.js
/**
 * WordPress dependencies
 */

var visibilityOptions = [{
  value: 'public',
  label: Object(external_this_wp_i18n_["__"])('Public'),
  info: Object(external_this_wp_i18n_["__"])('Visible to everyone.')
}, {
  value: 'private',
  label: Object(external_this_wp_i18n_["__"])('Private'),
  info: Object(external_this_wp_i18n_["__"])('Only visible to site admins and editors.')
}, {
  value: 'password',
  label: Object(external_this_wp_i18n_["__"])('Password Protected'),
  info: Object(external_this_wp_i18n_["__"])('Protected with a password you choose. Only those with the password can view this post.')
}];

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/index.js








/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var post_visibility_PostVisibility =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostVisibility, _Component);

  function PostVisibility(props) {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostVisibility);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostVisibility).apply(this, arguments));
    _this.setPublic = _this.setPublic.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setPrivate = _this.setPrivate.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setPasswordProtected = _this.setPasswordProtected.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.updatePassword = _this.updatePassword.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.state = {
      hasPassword: !!props.password
    };
    return _this;
  }

  Object(createClass["a" /* default */])(PostVisibility, [{
    key: "setPublic",
    value: function setPublic() {
      var _this$props = this.props,
          visibility = _this$props.visibility,
          onUpdateVisibility = _this$props.onUpdateVisibility,
          status = _this$props.status;
      onUpdateVisibility(visibility === 'private' ? 'draft' : status);
      this.setState({
        hasPassword: false
      });
    }
  }, {
    key: "setPrivate",
    value: function setPrivate() {
      if (!window.confirm(Object(external_this_wp_i18n_["__"])('Would you like to privately publish this post now?'))) {
        // eslint-disable-line no-alert
        return;
      }

      var _this$props2 = this.props,
          onUpdateVisibility = _this$props2.onUpdateVisibility,
          onSave = _this$props2.onSave;
      onUpdateVisibility('private');
      this.setState({
        hasPassword: false
      });
      onSave();
    }
  }, {
    key: "setPasswordProtected",
    value: function setPasswordProtected() {
      var _this$props3 = this.props,
          visibility = _this$props3.visibility,
          onUpdateVisibility = _this$props3.onUpdateVisibility,
          status = _this$props3.status,
          password = _this$props3.password;
      onUpdateVisibility(visibility === 'private' ? 'draft' : status, password || '');
      this.setState({
        hasPassword: true
      });
    }
  }, {
    key: "updatePassword",
    value: function updatePassword(event) {
      var _this$props4 = this.props,
          status = _this$props4.status,
          onUpdateVisibility = _this$props4.onUpdateVisibility;
      onUpdateVisibility(status, event.target.value);
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props5 = this.props,
          visibility = _this$props5.visibility,
          password = _this$props5.password,
          instanceId = _this$props5.instanceId;
      var visibilityHandlers = {
        public: {
          onSelect: this.setPublic,
          checked: visibility === 'public' && !this.state.hasPassword
        },
        private: {
          onSelect: this.setPrivate,
          checked: visibility === 'private'
        },
        password: {
          onSelect: this.setPasswordProtected,
          checked: this.state.hasPassword
        }
      };
      return [Object(external_this_wp_element_["createElement"])("fieldset", {
        key: "visibility-selector",
        className: "editor-post-visibility__dialog-fieldset"
      }, Object(external_this_wp_element_["createElement"])("legend", {
        className: "editor-post-visibility__dialog-legend"
      }, Object(external_this_wp_i18n_["__"])('Post Visibility')), visibilityOptions.map(function (_ref) {
        var value = _ref.value,
            label = _ref.label,
            info = _ref.info;
        return Object(external_this_wp_element_["createElement"])("div", {
          key: value,
          className: "editor-post-visibility__choice"
        }, Object(external_this_wp_element_["createElement"])("input", {
          type: "radio",
          name: "editor-post-visibility__setting-".concat(instanceId),
          value: value,
          onChange: visibilityHandlers[value].onSelect,
          checked: visibilityHandlers[value].checked,
          id: "editor-post-".concat(value, "-").concat(instanceId),
          "aria-describedby": "editor-post-".concat(value, "-").concat(instanceId, "-description"),
          className: "editor-post-visibility__dialog-radio"
        }), Object(external_this_wp_element_["createElement"])("label", {
          htmlFor: "editor-post-".concat(value, "-").concat(instanceId),
          className: "editor-post-visibility__dialog-label"
        }, label), Object(external_this_wp_element_["createElement"])("p", {
          id: "editor-post-".concat(value, "-").concat(instanceId, "-description"),
          className: "editor-post-visibility__dialog-info"
        }, info));
      })), this.state.hasPassword && Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-visibility__dialog-password",
        key: "password-selector"
      }, Object(external_this_wp_element_["createElement"])("label", {
        htmlFor: "editor-post-visibility__dialog-password-input-".concat(instanceId),
        className: "screen-reader-text"
      }, Object(external_this_wp_i18n_["__"])('Create password')), Object(external_this_wp_element_["createElement"])("input", {
        className: "editor-post-visibility__dialog-password-input",
        id: "editor-post-visibility__dialog-password-input-".concat(instanceId),
        type: "text",
        onChange: this.updatePassword,
        value: password,
        placeholder: Object(external_this_wp_i18n_["__"])('Use a secure password')
      }))];
    }
  }]);

  return PostVisibility;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var post_visibility = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute,
      getEditedPostVisibility = _select.getEditedPostVisibility;

  return {
    status: getEditedPostAttribute('status'),
    visibility: getEditedPostVisibility(),
    password: getEditedPostAttribute('password')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      savePost = _dispatch.savePost,
      editPost = _dispatch.editPost;

  return {
    onSave: savePost,
    onUpdateVisibility: function onUpdateVisibility(status) {
      var password = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      editPost({
        status: status,
        password: password
      });
    }
  };
}), external_this_wp_compose_["withInstanceId"]])(post_visibility_PostVisibility));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/label.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



function PostVisibilityLabel(_ref) {
  var visibility = _ref.visibility;

  var getVisibilityLabel = function getVisibilityLabel() {
    return Object(external_lodash_["find"])(visibilityOptions, {
      value: visibility
    }).label;
  };

  return getVisibilityLabel(visibility);
}

/* harmony default export */ var post_visibility_label = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    visibility: select('core/editor').getEditedPostVisibility()
  };
})(PostVisibilityLabel));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/index.js


/**
 * WordPress dependencies
 */




function PostSchedule(_ref) {
  var date = _ref.date,
      onUpdateDate = _ref.onUpdateDate;

  var settings = Object(external_this_wp_date_["__experimentalGetSettings"])(); // To know if the current timezone is a 12 hour time with look for "a" in the time format
  // We also make sure this a is not escaped by a "/"


  var is12HourTime = /a(?!\\)/i.test(settings.formats.time.toLowerCase() // Test only the lower case a
  .replace(/\\\\/g, '') // Replace "//" with empty strings
  .split('').reverse().join('') // Reverse the string and test for "a" not followed by a slash
  );
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["DateTimePicker"], {
    key: "date-time-picker",
    currentDate: date,
    onChange: onUpdateDate,
    is12Hour: is12HourTime
  });
}
/* harmony default export */ var post_schedule = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    date: select('core/editor').getEditedPostAttribute('date')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdateDate: function onUpdateDate(date) {
      dispatch('core/editor').editPost({
        date: date
      });
    }
  };
})])(PostSchedule));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/label.js
/**
 * WordPress dependencies
 */



function PostScheduleLabel(_ref) {
  var date = _ref.date,
      isFloating = _ref.isFloating;

  var settings = Object(external_this_wp_date_["__experimentalGetSettings"])();

  return date && !isFloating ? Object(external_this_wp_date_["dateI18n"])(settings.formats.datetimeAbbreviated, date) : Object(external_this_wp_i18n_["__"])('Immediately');
}
/* harmony default export */ var post_schedule_label = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    date: select('core/editor').getEditedPostAttribute('date'),
    isFloating: select('core/editor').isEditedPostDateFloating()
  };
})(PostScheduleLabel));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/flat-term-selector.js










/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Module constants
 */

var DEFAULT_QUERY = {
  per_page: -1,
  orderby: 'count',
  order: 'desc',
  _fields: 'id,name'
};
var MAX_TERMS_SUGGESTIONS = 20;

var isSameTermName = function isSameTermName(termA, termB) {
  return termA.toLowerCase() === termB.toLowerCase();
};
/**
 * Returns a term object with name unescaped.
 * The unescape of the name propery is done using lodash unescape function.
 *
 * @param {Object} term The term object to unescape.
 *
 * @return {Object} Term object with name property unescaped.
 */


var flat_term_selector_unescapeTerm = function unescapeTerm(term) {
  return Object(objectSpread["a" /* default */])({}, term, {
    name: Object(external_lodash_["unescape"])(term.name)
  });
};
/**
 * Returns an array of term objects with names unescaped.
 * The unescape of each term is performed using the unescapeTerm function.
 *
 * @param {Object[]} terms Array of term objects to unescape.
 *
 * @return {Object[]} Array of therm objects unscaped.
 */


var flat_term_selector_unescapeTerms = function unescapeTerms(terms) {
  return Object(external_lodash_["map"])(terms, flat_term_selector_unescapeTerm);
};

var flat_term_selector_FlatTermSelector =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(FlatTermSelector, _Component);

  function FlatTermSelector() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, FlatTermSelector);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(FlatTermSelector).apply(this, arguments));
    _this.onChange = _this.onChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.searchTerms = Object(external_lodash_["throttle"])(_this.searchTerms.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this))), 500);
    _this.findOrCreateTerm = _this.findOrCreateTerm.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.state = {
      loading: !Object(external_lodash_["isEmpty"])(_this.props.terms),
      availableTerms: [],
      selectedTerms: []
    };
    return _this;
  }

  Object(createClass["a" /* default */])(FlatTermSelector, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      if (!Object(external_lodash_["isEmpty"])(this.props.terms)) {
        this.initRequest = this.fetchTerms({
          include: this.props.terms.join(','),
          per_page: -1
        });
        this.initRequest.then(function () {
          _this2.setState({
            loading: false
          });
        }, function (xhr) {
          if (xhr.statusText === 'abort') {
            return;
          }

          _this2.setState({
            loading: false
          });
        });
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      Object(external_lodash_["invoke"])(this.initRequest, ['abort']);
      Object(external_lodash_["invoke"])(this.searchRequest, ['abort']);
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (prevProps.terms !== this.props.terms) {
        this.updateSelectedTerms(this.props.terms);
      }
    }
  }, {
    key: "fetchTerms",
    value: function fetchTerms() {
      var _this3 = this;

      var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var taxonomy = this.props.taxonomy;

      var query = Object(objectSpread["a" /* default */])({}, DEFAULT_QUERY, params);

      var request = external_this_wp_apiFetch_default()({
        path: Object(external_this_wp_url_["addQueryArgs"])("/wp/v2/".concat(taxonomy.rest_base), query)
      });
      request.then(flat_term_selector_unescapeTerms).then(function (terms) {
        _this3.setState(function (state) {
          return {
            availableTerms: state.availableTerms.concat(terms.filter(function (term) {
              return !Object(external_lodash_["find"])(state.availableTerms, function (availableTerm) {
                return availableTerm.id === term.id;
              });
            }))
          };
        });

        _this3.updateSelectedTerms(_this3.props.terms);
      });
      return request;
    }
  }, {
    key: "updateSelectedTerms",
    value: function updateSelectedTerms() {
      var _this4 = this;

      var terms = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
      var selectedTerms = terms.reduce(function (result, termId) {
        var termObject = Object(external_lodash_["find"])(_this4.state.availableTerms, function (term) {
          return term.id === termId;
        });

        if (termObject) {
          result.push(termObject.name);
        }

        return result;
      }, []);
      this.setState({
        selectedTerms: selectedTerms
      });
    }
  }, {
    key: "findOrCreateTerm",
    value: function findOrCreateTerm(termName) {
      var _this5 = this;

      var taxonomy = this.props.taxonomy;
      var termNameEscaped = Object(external_lodash_["escape"])(termName); // Tries to create a term or fetch it if it already exists.

      return external_this_wp_apiFetch_default()({
        path: "/wp/v2/".concat(taxonomy.rest_base),
        method: 'POST',
        data: {
          name: termNameEscaped
        }
      }).catch(function (error) {
        var errorCode = error.code;

        if (errorCode === 'term_exists') {
          // If the terms exist, fetch it instead of creating a new one.
          _this5.addRequest = external_this_wp_apiFetch_default()({
            path: Object(external_this_wp_url_["addQueryArgs"])("/wp/v2/".concat(taxonomy.rest_base), Object(objectSpread["a" /* default */])({}, DEFAULT_QUERY, {
              search: termNameEscaped
            }))
          }).then(flat_term_selector_unescapeTerms);
          return _this5.addRequest.then(function (searchResult) {
            return Object(external_lodash_["find"])(searchResult, function (result) {
              return isSameTermName(result.name, termName);
            });
          });
        }

        return Promise.reject(error);
      }).then(flat_term_selector_unescapeTerm);
    }
  }, {
    key: "onChange",
    value: function onChange(termNames) {
      var _this6 = this;

      var uniqueTerms = Object(external_lodash_["uniqBy"])(termNames, function (term) {
        return term.toLowerCase();
      });
      this.setState({
        selectedTerms: uniqueTerms
      });
      var newTermNames = uniqueTerms.filter(function (termName) {
        return !Object(external_lodash_["find"])(_this6.state.availableTerms, function (term) {
          return isSameTermName(term.name, termName);
        });
      });

      var termNamesToIds = function termNamesToIds(names, availableTerms) {
        return names.map(function (termName) {
          return Object(external_lodash_["find"])(availableTerms, function (term) {
            return isSameTermName(term.name, termName);
          }).id;
        });
      };

      if (newTermNames.length === 0) {
        return this.props.onUpdateTerms(termNamesToIds(uniqueTerms, this.state.availableTerms), this.props.taxonomy.rest_base);
      }

      Promise.all(newTermNames.map(this.findOrCreateTerm)).then(function (newTerms) {
        var newAvailableTerms = _this6.state.availableTerms.concat(newTerms);

        _this6.setState({
          availableTerms: newAvailableTerms
        });

        return _this6.props.onUpdateTerms(termNamesToIds(uniqueTerms, newAvailableTerms), _this6.props.taxonomy.rest_base);
      });
    }
  }, {
    key: "searchTerms",
    value: function searchTerms() {
      var search = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      Object(external_lodash_["invoke"])(this.searchRequest, ['abort']);
      this.searchRequest = this.fetchTerms({
        search: search
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          slug = _this$props.slug,
          taxonomy = _this$props.taxonomy,
          hasAssignAction = _this$props.hasAssignAction;

      if (!hasAssignAction) {
        return null;
      }

      var _this$state = this.state,
          loading = _this$state.loading,
          availableTerms = _this$state.availableTerms,
          selectedTerms = _this$state.selectedTerms;
      var termNames = availableTerms.map(function (term) {
        return term.name;
      });
      var newTermLabel = Object(external_lodash_["get"])(taxonomy, ['labels', 'add_new_item'], slug === 'post_tag' ? Object(external_this_wp_i18n_["__"])('Add New Tag') : Object(external_this_wp_i18n_["__"])('Add New Term'));
      var singularName = Object(external_lodash_["get"])(taxonomy, ['labels', 'singular_name'], slug === 'post_tag' ? Object(external_this_wp_i18n_["__"])('Tag') : Object(external_this_wp_i18n_["__"])('Term'));
      var termAddedLabel = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_x"])('%s added', 'term'), singularName);
      var termRemovedLabel = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_x"])('%s removed', 'term'), singularName);
      var removeTermLabel = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_x"])('Remove %s', 'term'), singularName);
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["FormTokenField"], {
        value: selectedTerms,
        suggestions: termNames,
        onChange: this.onChange,
        onInputChange: this.searchTerms,
        maxSuggestions: MAX_TERMS_SUGGESTIONS,
        disabled: loading,
        label: newTermLabel,
        messages: {
          added: termAddedLabel,
          removed: termRemovedLabel,
          remove: removeTermLabel
        }
      });
    }
  }]);

  return FlatTermSelector;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var flat_term_selector = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var slug = _ref.slug;

  var _select = select('core/editor'),
      getCurrentPost = _select.getCurrentPost;

  var _select2 = select('core'),
      getTaxonomy = _select2.getTaxonomy;

  var taxonomy = getTaxonomy(slug);
  return {
    hasCreateAction: taxonomy ? Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-create-' + taxonomy.rest_base], false) : false,
    hasAssignAction: taxonomy ? Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-assign-' + taxonomy.rest_base], false) : false,
    terms: taxonomy ? select('core/editor').getEditedPostAttribute(taxonomy.rest_base) : [],
    taxonomy: taxonomy
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdateTerms: function onUpdateTerms(terms, restBase) {
      dispatch('core/editor').editPost(Object(defineProperty["a" /* default */])({}, restBase, terms));
    }
  };
}), Object(external_this_wp_components_["withFilters"])('editor.PostTaxonomyType'))(flat_term_selector_FlatTermSelector));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/maybe-tags-panel.js







/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



var maybe_tags_panel_TagsPanel = function TagsPanel() {
  var panelBodyTitle = [Object(external_this_wp_i18n_["__"])('Suggestion:'), Object(external_this_wp_element_["createElement"])("span", {
    className: "editor-post-publish-panel__link",
    key: "label"
  }, Object(external_this_wp_i18n_["__"])('Add tags'))];
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    initialOpen: false,
    title: panelBodyTitle
  }, Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('Tags help users and search engines navigate your site and find your content. Add a few keywords to describe your post.')), Object(external_this_wp_element_["createElement"])(flat_term_selector, {
    slug: 'post_tag'
  }));
};

var maybe_tags_panel_MaybeTagsPanel =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MaybeTagsPanel, _Component);

  function MaybeTagsPanel(props) {
    var _this;

    Object(classCallCheck["a" /* default */])(this, MaybeTagsPanel);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MaybeTagsPanel).call(this, props));
    _this.state = {
      hadTagsWhenOpeningThePanel: props.hasTags
    };
    return _this;
  }
  /*
   * We only want to show the tag panel if the post didn't have
   * any tags when the user hit the Publish button.
   *
   * We can't use the prop.hasTags because it'll change to true
   * if the user adds a new tag within the pre-publish panel.
   * This would force a re-render and a new prop.hasTags check,
   * hiding this panel and keeping the user from adding
   * more than one tag.
   */


  Object(createClass["a" /* default */])(MaybeTagsPanel, [{
    key: "render",
    value: function render() {
      if (!this.state.hadTagsWhenOpeningThePanel) {
        return Object(external_this_wp_element_["createElement"])(maybe_tags_panel_TagsPanel, null);
      }

      return null;
    }
  }]);

  return MaybeTagsPanel;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var maybe_tags_panel = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var postType = select('core/editor').getCurrentPostType();
  var tagsTaxonomy = select('core').getTaxonomy('post_tag');
  var tags = tagsTaxonomy && select('core/editor').getEditedPostAttribute(tagsTaxonomy.rest_base);
  return {
    areTagsFetched: tagsTaxonomy !== undefined,
    isPostTypeSupported: tagsTaxonomy && Object(external_lodash_["some"])(tagsTaxonomy.types, function (type) {
      return type === postType;
    }),
    hasTags: tags && tags.length
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref) {
  var areTagsFetched = _ref.areTagsFetched,
      isPostTypeSupported = _ref.isPostTypeSupported;
  return isPostTypeSupported && areTagsFetched;
}))(maybe_tags_panel_MaybeTagsPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/maybe-post-format-panel.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



var maybe_post_format_panel_PostFormatSuggestion = function PostFormatSuggestion(_ref) {
  var suggestedPostFormat = _ref.suggestedPostFormat,
      suggestionText = _ref.suggestionText,
      onUpdatePostFormat = _ref.onUpdatePostFormat;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isLink: true,
    onClick: function onClick() {
      return onUpdatePostFormat(suggestedPostFormat);
    }
  }, suggestionText);
};

var maybe_post_format_panel_PostFormatPanel = function PostFormatPanel(_ref2) {
  var suggestion = _ref2.suggestion,
      onUpdatePostFormat = _ref2.onUpdatePostFormat;
  var panelBodyTitle = [Object(external_this_wp_i18n_["__"])('Suggestion:'), Object(external_this_wp_element_["createElement"])("span", {
    className: "editor-post-publish-panel__link",
    key: "label"
  }, Object(external_this_wp_i18n_["__"])('Use a post format'))];
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    initialOpen: false,
    title: panelBodyTitle
  }, Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('Your theme uses post formats to highlight different kinds of content, like images or videos. Apply a post format to see this special styling.')), Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_element_["createElement"])(maybe_post_format_panel_PostFormatSuggestion, {
    onUpdatePostFormat: onUpdatePostFormat,
    suggestedPostFormat: suggestion.id,
    suggestionText: Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Apply the "%1$s" format.'), suggestion.caption)
  })));
};

var maybe_post_format_panel_getSuggestion = function getSuggestion(supportedFormats, suggestedPostFormat) {
  var formats = POST_FORMATS.filter(function (format) {
    return Object(external_lodash_["includes"])(supportedFormats, format.id);
  });
  return Object(external_lodash_["find"])(formats, function (format) {
    return format.id === suggestedPostFormat;
  });
};

/* harmony default export */ var maybe_post_format_panel = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute,
      getSuggestedPostFormat = _select.getSuggestedPostFormat;

  var supportedFormats = Object(external_lodash_["get"])(select('core').getThemeSupports(), ['formats'], []);
  return {
    currentPostFormat: getEditedPostAttribute('format'),
    suggestion: maybe_post_format_panel_getSuggestion(supportedFormats, getSuggestedPostFormat())
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdatePostFormat: function onUpdatePostFormat(postFormat) {
      dispatch('core/editor').editPost({
        format: postFormat
      });
    }
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref3) {
  var suggestion = _ref3.suggestion,
      currentPostFormat = _ref3.currentPostFormat;
  return suggestion && suggestion.id !== currentPostFormat;
}))(maybe_post_format_panel_PostFormatPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/prepublish.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */








function PostPublishPanelPrepublish(_ref) {
  var hasPublishAction = _ref.hasPublishAction,
      isBeingScheduled = _ref.isBeingScheduled,
      children = _ref.children;
  var prePublishTitle, prePublishBodyText;

  if (!hasPublishAction) {
    prePublishTitle = Object(external_this_wp_i18n_["__"])('Are you ready to submit for review?');
    prePublishBodyText = Object(external_this_wp_i18n_["__"])('When you’re ready, submit your work for review, and an Editor will be able to approve it for you.');
  } else if (isBeingScheduled) {
    prePublishTitle = Object(external_this_wp_i18n_["__"])('Are you ready to schedule?');
    prePublishBodyText = Object(external_this_wp_i18n_["__"])('Your work will be published at the specified date and time.');
  } else {
    prePublishTitle = Object(external_this_wp_i18n_["__"])('Are you ready to publish?');
    prePublishBodyText = Object(external_this_wp_i18n_["__"])('Double-check your settings before publishing.');
  }

  return Object(external_this_wp_element_["createElement"])("div", {
    className: "editor-post-publish-panel__prepublish"
  }, Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])("strong", null, prePublishTitle)), Object(external_this_wp_element_["createElement"])("p", null, prePublishBodyText), hasPublishAction && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    initialOpen: false,
    title: [Object(external_this_wp_i18n_["__"])('Visibility:'), Object(external_this_wp_element_["createElement"])("span", {
      className: "editor-post-publish-panel__link",
      key: "label"
    }, Object(external_this_wp_element_["createElement"])(post_visibility_label, null))]
  }, Object(external_this_wp_element_["createElement"])(post_visibility, null)), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    initialOpen: false,
    title: [Object(external_this_wp_i18n_["__"])('Publish:'), Object(external_this_wp_element_["createElement"])("span", {
      className: "editor-post-publish-panel__link",
      key: "label"
    }, Object(external_this_wp_element_["createElement"])(post_schedule_label, null))]
  }, Object(external_this_wp_element_["createElement"])(post_schedule, null)), Object(external_this_wp_element_["createElement"])(maybe_post_format_panel, null), Object(external_this_wp_element_["createElement"])(maybe_tags_panel, null), children));
}

/* harmony default export */ var prepublish = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getCurrentPost = _select.getCurrentPost,
      isEditedPostBeingScheduled = _select.isEditedPostBeingScheduled;

  return {
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    isBeingScheduled: isEditedPostBeingScheduled()
  };
})(PostPublishPanelPrepublish));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/postpublish.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



var postpublish_PostPublishPanelPostpublish =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostPublishPanelPostpublish, _Component);

  function PostPublishPanelPostpublish() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostPublishPanelPostpublish);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostPublishPanelPostpublish).apply(this, arguments));
    _this.state = {
      showCopyConfirmation: false
    };
    _this.onCopy = _this.onCopy.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelectInput = _this.onSelectInput.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.postLink = Object(external_this_wp_element_["createRef"])();
    return _this;
  }

  Object(createClass["a" /* default */])(PostPublishPanelPostpublish, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      if (this.props.focusOnMount) {
        this.postLink.current.focus();
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      clearTimeout(this.dismissCopyConfirmation);
    }
  }, {
    key: "onCopy",
    value: function onCopy() {
      var _this2 = this;

      this.setState({
        showCopyConfirmation: true
      });
      clearTimeout(this.dismissCopyConfirmation);
      this.dismissCopyConfirmation = setTimeout(function () {
        _this2.setState({
          showCopyConfirmation: false
        });
      }, 4000);
    }
  }, {
    key: "onSelectInput",
    value: function onSelectInput(event) {
      event.target.select();
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          children = _this$props.children,
          isScheduled = _this$props.isScheduled,
          post = _this$props.post,
          postType = _this$props.postType;
      var postLabel = Object(external_lodash_["get"])(postType, ['labels', 'singular_name']);
      var viewPostLabel = Object(external_lodash_["get"])(postType, ['labels', 'view_item']);
      var postPublishNonLinkHeader = isScheduled ? Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_i18n_["__"])('is now scheduled. It will go live on'), " ", Object(external_this_wp_element_["createElement"])(post_schedule_label, null), ".") : Object(external_this_wp_i18n_["__"])('is now live.');
      return Object(external_this_wp_element_["createElement"])("div", {
        className: "post-publish-panel__postpublish"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        className: "post-publish-panel__postpublish-header"
      }, Object(external_this_wp_element_["createElement"])("a", {
        ref: this.postLink,
        href: post.link
      }, post.title || Object(external_this_wp_i18n_["__"])('(no title)')), " ", postPublishNonLinkHeader), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], null, Object(external_this_wp_element_["createElement"])("p", {
        className: "post-publish-panel__postpublish-subheader"
      }, Object(external_this_wp_element_["createElement"])("strong", null, Object(external_this_wp_i18n_["__"])('What’s next?'))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
        className: "post-publish-panel__postpublish-post-address",
        readOnly: true,
        label: Object(external_this_wp_i18n_["sprintf"])(
        /* translators: %s: post type singular name */
        Object(external_this_wp_i18n_["__"])('%s address'), postLabel),
        value: Object(external_this_wp_url_["safeDecodeURIComponent"])(post.link),
        onFocus: this.onSelectInput
      }), Object(external_this_wp_element_["createElement"])("div", {
        className: "post-publish-panel__postpublish-buttons"
      }, !isScheduled && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        isDefault: true,
        href: post.link
      }, viewPostLabel), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ClipboardButton"], {
        isDefault: true,
        text: post.link,
        onCopy: this.onCopy
      }, this.state.showCopyConfirmation ? Object(external_this_wp_i18n_["__"])('Copied!') : Object(external_this_wp_i18n_["__"])('Copy Link')))), children);
    }
  }]);

  return PostPublishPanelPostpublish;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var postpublish = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute,
      getCurrentPost = _select.getCurrentPost,
      isCurrentPostScheduled = _select.isCurrentPostScheduled;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  return {
    post: getCurrentPost(),
    postType: getPostType(getEditedPostAttribute('type')),
    isScheduled: isCurrentPostScheduled()
  };
})(postpublish_PostPublishPanelPostpublish));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/index.js










/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




var post_publish_panel_PostPublishPanel =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostPublishPanel, _Component);

  function PostPublishPanel() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostPublishPanel);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostPublishPanel).apply(this, arguments));
    _this.onSubmit = _this.onSubmit.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(PostPublishPanel, [{
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      // Automatically collapse the publish sidebar when a post
      // is published and the user makes an edit.
      if (prevProps.isPublished && !this.props.isSaving && this.props.isDirty) {
        this.props.onClose();
      }
    }
  }, {
    key: "onSubmit",
    value: function onSubmit() {
      var _this$props = this.props,
          onClose = _this$props.onClose,
          hasPublishAction = _this$props.hasPublishAction,
          isPostTypeViewable = _this$props.isPostTypeViewable;

      if (!hasPublishAction || !isPostTypeViewable) {
        onClose();
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          forceIsDirty = _this$props2.forceIsDirty,
          forceIsSaving = _this$props2.forceIsSaving,
          isBeingScheduled = _this$props2.isBeingScheduled,
          isPublished = _this$props2.isPublished,
          isPublishSidebarEnabled = _this$props2.isPublishSidebarEnabled,
          isScheduled = _this$props2.isScheduled,
          isSaving = _this$props2.isSaving,
          onClose = _this$props2.onClose,
          onTogglePublishSidebar = _this$props2.onTogglePublishSidebar,
          PostPublishExtension = _this$props2.PostPublishExtension,
          PrePublishExtension = _this$props2.PrePublishExtension,
          additionalProps = Object(objectWithoutProperties["a" /* default */])(_this$props2, ["forceIsDirty", "forceIsSaving", "isBeingScheduled", "isPublished", "isPublishSidebarEnabled", "isScheduled", "isSaving", "onClose", "onTogglePublishSidebar", "PostPublishExtension", "PrePublishExtension"]);

      var propsForPanel = Object(external_lodash_["omit"])(additionalProps, ['hasPublishAction', 'isDirty', 'isPostTypeViewable']);
      var isPublishedOrScheduled = isPublished || isScheduled && isBeingScheduled;
      var isPrePublish = !isPublishedOrScheduled && !isSaving;
      var isPostPublish = isPublishedOrScheduled && !isSaving;
      return Object(external_this_wp_element_["createElement"])("div", Object(esm_extends["a" /* default */])({
        className: "editor-post-publish-panel"
      }, propsForPanel), Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-publish-panel__header"
      }, isPostPublish ? Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-publish-panel__header-published"
      }, isScheduled ? Object(external_this_wp_i18n_["__"])('Scheduled') : Object(external_this_wp_i18n_["__"])('Published')) : Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-publish-panel__header-publish-button"
      }, Object(external_this_wp_element_["createElement"])(post_publish_button, {
        focusOnMount: true,
        onSubmit: this.onSubmit,
        forceIsDirty: forceIsDirty,
        forceIsSaving: forceIsSaving
      }), Object(external_this_wp_element_["createElement"])("span", {
        className: "editor-post-publish-panel__spacer"
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        "aria-expanded": true,
        onClick: onClose,
        icon: "no-alt",
        label: Object(external_this_wp_i18n_["__"])('Close panel')
      })), Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-publish-panel__content"
      }, isPrePublish && Object(external_this_wp_element_["createElement"])(prepublish, null, PrePublishExtension && Object(external_this_wp_element_["createElement"])(PrePublishExtension, null)), isPostPublish && Object(external_this_wp_element_["createElement"])(postpublish, {
        focusOnMount: true
      }, PostPublishExtension && Object(external_this_wp_element_["createElement"])(PostPublishExtension, null)), isSaving && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null)), Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-publish-panel__footer"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
        label: Object(external_this_wp_i18n_["__"])('Always show pre-publish checks.'),
        checked: isPublishSidebarEnabled,
        onChange: onTogglePublishSidebar
      })));
    }
  }]);

  return PostPublishPanel;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var post_publish_panel = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core'),
      getPostType = _select.getPostType;

  var _select2 = select('core/editor'),
      getCurrentPost = _select2.getCurrentPost,
      getEditedPostAttribute = _select2.getEditedPostAttribute,
      isCurrentPostPublished = _select2.isCurrentPostPublished,
      isCurrentPostScheduled = _select2.isCurrentPostScheduled,
      isEditedPostBeingScheduled = _select2.isEditedPostBeingScheduled,
      isEditedPostDirty = _select2.isEditedPostDirty,
      isSavingPost = _select2.isSavingPost;

  var _select3 = select('core/editor'),
      isPublishSidebarEnabled = _select3.isPublishSidebarEnabled;

  var postType = getPostType(getEditedPostAttribute('type'));
  return {
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    isPostTypeViewable: Object(external_lodash_["get"])(postType, ['viewable'], false),
    isBeingScheduled: isEditedPostBeingScheduled(),
    isDirty: isEditedPostDirty(),
    isPublished: isCurrentPostPublished(),
    isPublishSidebarEnabled: isPublishSidebarEnabled(),
    isSaving: isSavingPost(),
    isScheduled: isCurrentPostScheduled()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref) {
  var isPublishSidebarEnabled = _ref.isPublishSidebarEnabled;

  var _dispatch = dispatch('core/editor'),
      disablePublishSidebar = _dispatch.disablePublishSidebar,
      enablePublishSidebar = _dispatch.enablePublishSidebar;

  return {
    onTogglePublishSidebar: function onTogglePublishSidebar() {
      if (isPublishSidebarEnabled) {
        disablePublishSidebar();
      } else {
        enablePublishSidebar();
      }
    }
  };
}), external_this_wp_components_["withFocusReturn"], external_this_wp_components_["withConstrainedTabbing"]])(post_publish_panel_PostPublishPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-switch-to-draft-button/index.js


/**
 * WordPress dependencies
 */





function PostSwitchToDraftButton(_ref) {
  var isSaving = _ref.isSaving,
      isPublished = _ref.isPublished,
      isScheduled = _ref.isScheduled,
      onClick = _ref.onClick;

  if (!isPublished && !isScheduled) {
    return null;
  }

  var onSwitch = function onSwitch() {
    var alertMessage;

    if (isPublished) {
      alertMessage = Object(external_this_wp_i18n_["__"])('Are you sure you want to unpublish this post?');
    } else if (isScheduled) {
      alertMessage = Object(external_this_wp_i18n_["__"])('Are you sure you want to unschedule this post?');
    } // eslint-disable-next-line no-alert


    if (window.confirm(alertMessage)) {
      onClick();
    }
  };

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    className: "editor-post-switch-to-draft",
    onClick: onSwitch,
    disabled: isSaving,
    isTertiary: true
  }, Object(external_this_wp_i18n_["__"])('Switch to Draft'));
}

/* harmony default export */ var post_switch_to_draft_button = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isSavingPost = _select.isSavingPost,
      isCurrentPostPublished = _select.isCurrentPostPublished,
      isCurrentPostScheduled = _select.isCurrentPostScheduled;

  return {
    isSaving: isSavingPost(),
    isPublished: isCurrentPostPublished(),
    isScheduled: isCurrentPostScheduled()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      editPost = _dispatch.editPost,
      savePost = _dispatch.savePost;

  return {
    onClick: function onClick() {
      editPost({
        status: 'draft'
      });
      savePost();
    }
  };
})])(PostSwitchToDraftButton));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-saved-state/index.js







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
 * Component showing whether the post is saved or not and displaying save links.
 *
 * @param   {Object}    Props Component Props.
 */

var post_saved_state_PostSavedState =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostSavedState, _Component);

  function PostSavedState() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostSavedState);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostSavedState).apply(this, arguments));
    _this.state = {
      forceSavedMessage: false
    };
    return _this;
  }

  Object(createClass["a" /* default */])(PostSavedState, [{
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this2 = this;

      if (prevProps.isSaving && !this.props.isSaving) {
        this.setState({
          forceSavedMessage: true
        });
        this.props.setTimeout(function () {
          _this2.setState({
            forceSavedMessage: false
          });
        }, 1000);
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          post = _this$props.post,
          isNew = _this$props.isNew,
          isScheduled = _this$props.isScheduled,
          isPublished = _this$props.isPublished,
          isDirty = _this$props.isDirty,
          isSaving = _this$props.isSaving,
          isSaveable = _this$props.isSaveable,
          onSave = _this$props.onSave,
          isAutosaving = _this$props.isAutosaving,
          isPending = _this$props.isPending,
          isLargeViewport = _this$props.isLargeViewport;
      var forceSavedMessage = this.state.forceSavedMessage;

      if (isSaving) {
        // TODO: Classes generation should be common across all return
        // paths of this function, including proper naming convention for
        // the "Save Draft" button.
        var classes = classnames_default()('editor-post-saved-state', 'is-saving', {
          'is-autosaving': isAutosaving
        });
        return Object(external_this_wp_element_["createElement"])("span", {
          className: classes
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dashicon"], {
          icon: "cloud"
        }), isAutosaving ? Object(external_this_wp_i18n_["__"])('Autosaving') : Object(external_this_wp_i18n_["__"])('Saving'));
      }

      if (isPublished || isScheduled) {
        return Object(external_this_wp_element_["createElement"])(post_switch_to_draft_button, null);
      }

      if (!isSaveable) {
        return null;
      }

      if (forceSavedMessage || !isNew && !isDirty) {
        return Object(external_this_wp_element_["createElement"])("span", {
          className: "editor-post-saved-state is-saved"
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dashicon"], {
          icon: "saved"
        }), Object(external_this_wp_i18n_["__"])('Saved'));
      } // Once the post has been submitted for review this button
      // is not needed for the contributor role.


      var hasPublishAction = Object(external_lodash_["get"])(post, ['_links', 'wp:action-publish'], false);

      if (!hasPublishAction && isPending) {
        return null;
      }

      var label = isPending ? Object(external_this_wp_i18n_["__"])('Save as Pending') : Object(external_this_wp_i18n_["__"])('Save Draft');

      if (!isLargeViewport) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
          className: "editor-post-save-draft",
          label: label,
          onClick: onSave,
          shortcut: external_this_wp_keycodes_["displayShortcut"].primary('s'),
          icon: "cloud-upload"
        });
      }

      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: "editor-post-save-draft",
        onClick: onSave,
        shortcut: external_this_wp_keycodes_["displayShortcut"].primary('s'),
        isTertiary: true
      }, label);
    }
  }]);

  return PostSavedState;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var post_saved_state = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var forceIsDirty = _ref.forceIsDirty,
      forceIsSaving = _ref.forceIsSaving;

  var _select = select('core/editor'),
      isEditedPostNew = _select.isEditedPostNew,
      isCurrentPostPublished = _select.isCurrentPostPublished,
      isCurrentPostScheduled = _select.isCurrentPostScheduled,
      isEditedPostDirty = _select.isEditedPostDirty,
      isSavingPost = _select.isSavingPost,
      isEditedPostSaveable = _select.isEditedPostSaveable,
      getCurrentPost = _select.getCurrentPost,
      isAutosavingPost = _select.isAutosavingPost,
      getEditedPostAttribute = _select.getEditedPostAttribute;

  return {
    post: getCurrentPost(),
    isNew: isEditedPostNew(),
    isPublished: isCurrentPostPublished(),
    isScheduled: isCurrentPostScheduled(),
    isDirty: forceIsDirty || isEditedPostDirty(),
    isSaving: forceIsSaving || isSavingPost(),
    isSaveable: isEditedPostSaveable(),
    isAutosaving: isAutosavingPost(),
    isPending: 'pending' === getEditedPostAttribute('status')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onSave: dispatch('core/editor').savePost
  };
}), external_this_wp_compose_["withSafeTimeout"], Object(external_this_wp_viewport_["withViewportMatch"])({
  isLargeViewport: 'medium'
})])(post_saved_state_PostSavedState));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PostScheduleCheck(_ref) {
  var hasPublishAction = _ref.hasPublishAction,
      children = _ref.children;

  if (!hasPublishAction) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_schedule_check = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getCurrentPost = _select.getCurrentPost,
      getCurrentPostType = _select.getCurrentPostType;

  return {
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    postType: getCurrentPostType()
  };
})])(PostScheduleCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-sticky/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PostStickyCheck(_ref) {
  var hasStickyAction = _ref.hasStickyAction,
      postType = _ref.postType,
      children = _ref.children;

  if (postType !== 'post' || !hasStickyAction) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_sticky_check = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var post = select('core/editor').getCurrentPost();
  return {
    hasStickyAction: Object(external_lodash_["get"])(post, ['_links', 'wp:action-sticky'], false),
    postType: select('core/editor').getCurrentPostType()
  };
})])(PostStickyCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-sticky/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function PostSticky(_ref) {
  var onUpdateSticky = _ref.onUpdateSticky,
      _ref$postSticky = _ref.postSticky,
      postSticky = _ref$postSticky === void 0 ? false : _ref$postSticky;
  return Object(external_this_wp_element_["createElement"])(post_sticky_check, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
    label: Object(external_this_wp_i18n_["__"])('Stick to the top of the blog'),
    checked: postSticky,
    onChange: function onChange() {
      return onUpdateSticky(!postSticky);
    }
  }));
}
/* harmony default export */ var post_sticky = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    postSticky: select('core/editor').getEditedPostAttribute('sticky')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdateSticky: function onUpdateSticky(postSticky) {
      dispatch('core/editor').editPost({
        sticky: postSticky
      });
    }
  };
})])(PostSticky));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/hierarchical-term-selector.js











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

var hierarchical_term_selector_DEFAULT_QUERY = {
  per_page: -1,
  orderby: 'name',
  order: 'asc',
  _fields: 'id,name,parent'
};
var MIN_TERMS_COUNT_FOR_FILTER = 8;

var hierarchical_term_selector_HierarchicalTermSelector =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(HierarchicalTermSelector, _Component);

  function HierarchicalTermSelector() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, HierarchicalTermSelector);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(HierarchicalTermSelector).apply(this, arguments));
    _this.findTerm = _this.findTerm.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onChange = _this.onChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onChangeFormName = _this.onChangeFormName.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onChangeFormParent = _this.onChangeFormParent.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onAddTerm = _this.onAddTerm.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onToggleForm = _this.onToggleForm.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setFilterValue = _this.setFilterValue.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.sortBySelected = _this.sortBySelected.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.state = {
      loading: true,
      availableTermsTree: [],
      availableTerms: [],
      adding: false,
      formName: '',
      formParent: '',
      showForm: false,
      filterValue: '',
      filteredTermsTree: []
    };
    return _this;
  }

  Object(createClass["a" /* default */])(HierarchicalTermSelector, [{
    key: "onChange",
    value: function onChange(event) {
      var _this$props = this.props,
          onUpdateTerms = _this$props.onUpdateTerms,
          _this$props$terms = _this$props.terms,
          terms = _this$props$terms === void 0 ? [] : _this$props$terms,
          taxonomy = _this$props.taxonomy;
      var termId = parseInt(event.target.value, 10);
      var hasTerm = terms.indexOf(termId) !== -1;
      var newTerms = hasTerm ? Object(external_lodash_["without"])(terms, termId) : [].concat(Object(toConsumableArray["a" /* default */])(terms), [termId]);
      onUpdateTerms(newTerms, taxonomy.rest_base);
    }
  }, {
    key: "onChangeFormName",
    value: function onChangeFormName(event) {
      var newValue = event.target.value.trim() === '' ? '' : event.target.value;
      this.setState({
        formName: newValue
      });
    }
  }, {
    key: "onChangeFormParent",
    value: function onChangeFormParent(newParent) {
      this.setState({
        formParent: newParent
      });
    }
  }, {
    key: "onToggleForm",
    value: function onToggleForm() {
      this.setState(function (state) {
        return {
          showForm: !state.showForm
        };
      });
    }
  }, {
    key: "findTerm",
    value: function findTerm(terms, parent, name) {
      return Object(external_lodash_["find"])(terms, function (term) {
        return (!term.parent && !parent || parseInt(term.parent) === parseInt(parent)) && term.name.toLowerCase() === name.toLowerCase();
      });
    }
  }, {
    key: "onAddTerm",
    value: function onAddTerm(event) {
      var _this2 = this;

      event.preventDefault();
      var _this$props2 = this.props,
          onUpdateTerms = _this$props2.onUpdateTerms,
          taxonomy = _this$props2.taxonomy,
          terms = _this$props2.terms,
          slug = _this$props2.slug;
      var _this$state = this.state,
          formName = _this$state.formName,
          formParent = _this$state.formParent,
          adding = _this$state.adding,
          availableTerms = _this$state.availableTerms;

      if (formName === '' || adding) {
        return;
      } // check if the term we are adding already exists


      var existingTerm = this.findTerm(availableTerms, formParent, formName);

      if (existingTerm) {
        // if the term we are adding exists but is not selected select it
        if (!Object(external_lodash_["some"])(terms, function (term) {
          return term === existingTerm.id;
        })) {
          onUpdateTerms([].concat(Object(toConsumableArray["a" /* default */])(terms), [existingTerm.id]), taxonomy.rest_base);
        }

        this.setState({
          formName: '',
          formParent: ''
        });
        return;
      }

      this.setState({
        adding: true
      });
      this.addRequest = external_this_wp_apiFetch_default()({
        path: "/wp/v2/".concat(taxonomy.rest_base),
        method: 'POST',
        data: {
          name: formName,
          parent: formParent ? formParent : undefined
        }
      }); // Tries to create a term or fetch it if it already exists

      var findOrCreatePromise = this.addRequest.catch(function (error) {
        var errorCode = error.code;

        if (errorCode === 'term_exists') {
          // search the new category created since last fetch
          _this2.addRequest = external_this_wp_apiFetch_default()({
            path: Object(external_this_wp_url_["addQueryArgs"])("/wp/v2/".concat(taxonomy.rest_base), Object(objectSpread["a" /* default */])({}, hierarchical_term_selector_DEFAULT_QUERY, {
              parent: formParent || 0,
              search: formName
            }))
          });
          return _this2.addRequest.then(function (searchResult) {
            return _this2.findTerm(searchResult, formParent, formName);
          });
        }

        return Promise.reject(error);
      });
      findOrCreatePromise.then(function (term) {
        var hasTerm = !!Object(external_lodash_["find"])(_this2.state.availableTerms, function (availableTerm) {
          return availableTerm.id === term.id;
        });
        var newAvailableTerms = hasTerm ? _this2.state.availableTerms : [term].concat(Object(toConsumableArray["a" /* default */])(_this2.state.availableTerms));
        var termAddedMessage = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_x"])('%s added', 'term'), Object(external_lodash_["get"])(_this2.props.taxonomy, ['labels', 'singular_name'], slug === 'category' ? Object(external_this_wp_i18n_["__"])('Category') : Object(external_this_wp_i18n_["__"])('Term')));

        _this2.props.speak(termAddedMessage, 'assertive');

        _this2.addRequest = null;

        _this2.setState({
          adding: false,
          formName: '',
          formParent: '',
          availableTerms: newAvailableTerms,
          availableTermsTree: _this2.sortBySelected(buildTermsTree(newAvailableTerms))
        });

        onUpdateTerms([].concat(Object(toConsumableArray["a" /* default */])(terms), [term.id]), taxonomy.rest_base);
      }, function (xhr) {
        if (xhr.statusText === 'abort') {
          return;
        }

        _this2.addRequest = null;

        _this2.setState({
          adding: false
        });
      });
    }
  }, {
    key: "componentDidMount",
    value: function componentDidMount() {
      this.fetchTerms();
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      Object(external_lodash_["invoke"])(this.fetchRequest, ['abort']);
      Object(external_lodash_["invoke"])(this.addRequest, ['abort']);
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.taxonomy !== prevProps.taxonomy) {
        this.fetchTerms();
      }
    }
  }, {
    key: "fetchTerms",
    value: function fetchTerms() {
      var _this3 = this;

      var taxonomy = this.props.taxonomy;

      if (!taxonomy) {
        return;
      }

      this.fetchRequest = external_this_wp_apiFetch_default()({
        path: Object(external_this_wp_url_["addQueryArgs"])("/wp/v2/".concat(taxonomy.rest_base), hierarchical_term_selector_DEFAULT_QUERY)
      });
      this.fetchRequest.then(function (terms) {
        // resolve
        var availableTermsTree = _this3.sortBySelected(buildTermsTree(terms));

        _this3.fetchRequest = null;

        _this3.setState({
          loading: false,
          availableTermsTree: availableTermsTree,
          availableTerms: terms
        });
      }, function (xhr) {
        // reject
        if (xhr.statusText === 'abort') {
          return;
        }

        _this3.fetchRequest = null;

        _this3.setState({
          loading: false
        });
      });
    }
  }, {
    key: "sortBySelected",
    value: function sortBySelected(termsTree) {
      var terms = this.props.terms;

      var treeHasSelection = function treeHasSelection(termTree) {
        if (terms.indexOf(termTree.id) !== -1) {
          return true;
        }

        if (undefined === termTree.children) {
          return false;
        }

        var anyChildIsSelected = termTree.children.map(treeHasSelection).filter(function (child) {
          return child;
        }).length > 0;

        if (anyChildIsSelected) {
          return true;
        }

        return false;
      };

      var termOrChildIsSelected = function termOrChildIsSelected(termA, termB) {
        var termASelected = treeHasSelection(termA);
        var termBSelected = treeHasSelection(termB);

        if (termASelected === termBSelected) {
          return 0;
        }

        if (termASelected && !termBSelected) {
          return -1;
        }

        if (!termASelected && termBSelected) {
          return 1;
        }

        return 0;
      };

      termsTree.sort(termOrChildIsSelected);
      return termsTree;
    }
  }, {
    key: "setFilterValue",
    value: function setFilterValue(event) {
      var availableTermsTree = this.state.availableTermsTree;
      var filterValue = event.target.value;
      var filteredTermsTree = availableTermsTree.map(this.getFilterMatcher(filterValue)).filter(function (term) {
        return term;
      });

      var getResultCount = function getResultCount(terms) {
        var count = 0;

        for (var i = 0; i < terms.length; i++) {
          count++;

          if (undefined !== terms[i].children) {
            count += getResultCount(terms[i].children);
          }
        }

        return count;
      };

      this.setState({
        filterValue: filterValue,
        filteredTermsTree: filteredTermsTree
      });
      var resultCount = getResultCount(filteredTermsTree);
      var resultsFoundMessage = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('%d result found.', '%d results found.', resultCount), resultCount);
      this.props.debouncedSpeak(resultsFoundMessage, 'assertive');
    }
  }, {
    key: "getFilterMatcher",
    value: function getFilterMatcher(filterValue) {
      var matchTermsForFilter = function matchTermsForFilter(originalTerm) {
        if ('' === filterValue) {
          return originalTerm;
        } // Shallow clone, because we'll be filtering the term's children and
        // don't want to modify the original term.


        var term = Object(objectSpread["a" /* default */])({}, originalTerm); // Map and filter the children, recursive so we deal with grandchildren
        // and any deeper levels.


        if (term.children.length > 0) {
          term.children = term.children.map(matchTermsForFilter).filter(function (child) {
            return child;
          });
        } // If the term's name contains the filterValue, or it has children
        // (i.e. some child matched at some point in the tree) then return it.


        if (-1 !== term.name.toLowerCase().indexOf(filterValue) || term.children.length > 0) {
          return term;
        } // Otherwise, return false. After mapping, the list of terms will need
        // to have false values filtered out.


        return false;
      };

      return matchTermsForFilter;
    }
  }, {
    key: "renderTerms",
    value: function renderTerms(renderedTerms) {
      var _this4 = this;

      var _this$props$terms2 = this.props.terms,
          terms = _this$props$terms2 === void 0 ? [] : _this$props$terms2;
      return renderedTerms.map(function (term) {
        var id = "editor-post-taxonomies-hierarchical-term-".concat(term.id);
        return Object(external_this_wp_element_["createElement"])("div", {
          key: term.id,
          className: "editor-post-taxonomies__hierarchical-terms-choice"
        }, Object(external_this_wp_element_["createElement"])("input", {
          id: id,
          className: "editor-post-taxonomies__hierarchical-terms-input",
          type: "checkbox",
          checked: terms.indexOf(term.id) !== -1,
          value: term.id,
          onChange: _this4.onChange
        }), Object(external_this_wp_element_["createElement"])("label", {
          htmlFor: id
        }, Object(external_lodash_["unescape"])(term.name)), !!term.children.length && Object(external_this_wp_element_["createElement"])("div", {
          className: "editor-post-taxonomies__hierarchical-terms-subchoices"
        }, _this4.renderTerms(term.children)));
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props3 = this.props,
          slug = _this$props3.slug,
          taxonomy = _this$props3.taxonomy,
          instanceId = _this$props3.instanceId,
          hasCreateAction = _this$props3.hasCreateAction,
          hasAssignAction = _this$props3.hasAssignAction;

      if (!hasAssignAction) {
        return null;
      }

      var _this$state2 = this.state,
          availableTermsTree = _this$state2.availableTermsTree,
          availableTerms = _this$state2.availableTerms,
          filteredTermsTree = _this$state2.filteredTermsTree,
          formName = _this$state2.formName,
          formParent = _this$state2.formParent,
          loading = _this$state2.loading,
          showForm = _this$state2.showForm,
          filterValue = _this$state2.filterValue;

      var labelWithFallback = function labelWithFallback(labelProperty, fallbackIsCategory, fallbackIsNotCategory) {
        return Object(external_lodash_["get"])(taxonomy, ['labels', labelProperty], slug === 'category' ? fallbackIsCategory : fallbackIsNotCategory);
      };

      var newTermButtonLabel = labelWithFallback('add_new_item', Object(external_this_wp_i18n_["__"])('Add new category'), Object(external_this_wp_i18n_["__"])('Add new term'));
      var newTermLabel = labelWithFallback('new_item_name', Object(external_this_wp_i18n_["__"])('Add new category'), Object(external_this_wp_i18n_["__"])('Add new term'));
      var parentSelectLabel = labelWithFallback('parent_item', Object(external_this_wp_i18n_["__"])('Parent Category'), Object(external_this_wp_i18n_["__"])('Parent Term'));
      var noParentOption = "\u2014 ".concat(parentSelectLabel, " \u2014");
      var newTermSubmitLabel = newTermButtonLabel;
      var inputId = "editor-post-taxonomies__hierarchical-terms-input-".concat(instanceId);
      var filterInputId = "editor-post-taxonomies__hierarchical-terms-filter-".concat(instanceId);
      var filterLabel = Object(external_lodash_["get"])(this.props.taxonomy, ['labels', 'search_items'], Object(external_this_wp_i18n_["__"])('Search Terms'));
      var groupLabel = Object(external_lodash_["get"])(this.props.taxonomy, ['name'], Object(external_this_wp_i18n_["__"])('Terms'));
      var showFilter = availableTerms.length >= MIN_TERMS_COUNT_FOR_FILTER;
      return [showFilter && Object(external_this_wp_element_["createElement"])("label", {
        key: "filter-label",
        htmlFor: filterInputId
      }, filterLabel), showFilter && Object(external_this_wp_element_["createElement"])("input", {
        type: "search",
        id: filterInputId,
        value: filterValue,
        onChange: this.setFilterValue,
        className: "editor-post-taxonomies__hierarchical-terms-filter",
        key: "term-filter-input"
      }), Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-taxonomies__hierarchical-terms-list",
        key: "term-list",
        tabIndex: "0",
        role: "group",
        "aria-label": groupLabel
      }, this.renderTerms('' !== filterValue ? filteredTermsTree : availableTermsTree)), !loading && hasCreateAction && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        key: "term-add-button",
        onClick: this.onToggleForm,
        className: "editor-post-taxonomies__hierarchical-terms-add",
        "aria-expanded": showForm,
        isLink: true
      }, newTermButtonLabel), showForm && Object(external_this_wp_element_["createElement"])("form", {
        onSubmit: this.onAddTerm,
        key: "hierarchical-terms-form"
      }, Object(external_this_wp_element_["createElement"])("label", {
        htmlFor: inputId,
        className: "editor-post-taxonomies__hierarchical-terms-label"
      }, newTermLabel), Object(external_this_wp_element_["createElement"])("input", {
        type: "text",
        id: inputId,
        className: "editor-post-taxonomies__hierarchical-terms-input",
        value: formName,
        onChange: this.onChangeFormName,
        required: true
      }), !!availableTerms.length && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TreeSelect"], {
        label: parentSelectLabel,
        noOptionLabel: noParentOption,
        onChange: this.onChangeFormParent,
        selectedId: formParent,
        tree: availableTermsTree
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        isDefault: true,
        type: "submit",
        className: "editor-post-taxonomies__hierarchical-terms-submit"
      }, newTermSubmitLabel))];
      /* eslint-enable jsx-a11y/no-onchange */
    }
  }]);

  return HierarchicalTermSelector;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var hierarchical_term_selector = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var slug = _ref.slug;

  var _select = select('core/editor'),
      getCurrentPost = _select.getCurrentPost;

  var _select2 = select('core'),
      getTaxonomy = _select2.getTaxonomy;

  var taxonomy = getTaxonomy(slug);
  return {
    hasCreateAction: taxonomy ? Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-create-' + taxonomy.rest_base], false) : false,
    hasAssignAction: taxonomy ? Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-assign-' + taxonomy.rest_base], false) : false,
    terms: taxonomy ? select('core/editor').getEditedPostAttribute(taxonomy.rest_base) : [],
    taxonomy: taxonomy
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onUpdateTerms: function onUpdateTerms(terms, restBase) {
      dispatch('core/editor').editPost(Object(defineProperty["a" /* default */])({}, restBase, terms));
    }
  };
}), external_this_wp_components_["withSpokenMessages"], external_this_wp_compose_["withInstanceId"], Object(external_this_wp_components_["withFilters"])('editor.PostTaxonomyType')])(hierarchical_term_selector_HierarchicalTermSelector));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function PostTaxonomies(_ref) {
  var postType = _ref.postType,
      taxonomies = _ref.taxonomies,
      _ref$taxonomyWrapper = _ref.taxonomyWrapper,
      taxonomyWrapper = _ref$taxonomyWrapper === void 0 ? external_lodash_["identity"] : _ref$taxonomyWrapper;
  var availableTaxonomies = Object(external_lodash_["filter"])(taxonomies, function (taxonomy) {
    return Object(external_lodash_["includes"])(taxonomy.types, postType);
  });
  var visibleTaxonomies = Object(external_lodash_["filter"])(availableTaxonomies, function (taxonomy) {
    return taxonomy.visibility.show_ui;
  });
  return visibleTaxonomies.map(function (taxonomy) {
    var TaxonomyComponent = taxonomy.hierarchical ? hierarchical_term_selector : flat_term_selector;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], {
      key: "taxonomy-".concat(taxonomy.slug)
    }, taxonomyWrapper(Object(external_this_wp_element_["createElement"])(TaxonomyComponent, {
      slug: taxonomy.slug
    }), taxonomy));
  });
}
/* harmony default export */ var post_taxonomies = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    postType: select('core/editor').getCurrentPostType(),
    taxonomies: select('core').getTaxonomies({
      per_page: -1
    })
  };
})])(PostTaxonomies));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PostTaxonomiesCheck(_ref) {
  var postType = _ref.postType,
      taxonomies = _ref.taxonomies,
      children = _ref.children;
  var hasTaxonomies = Object(external_lodash_["some"])(taxonomies, function (taxonomy) {
    return Object(external_lodash_["includes"])(taxonomy.types, postType);
  });

  if (!hasTaxonomies) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_taxonomies_check = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    postType: select('core/editor').getCurrentPostType(),
    taxonomies: select('core').getTaxonomies({
      per_page: -1
    })
  };
})])(PostTaxonomiesCheck));

// EXTERNAL MODULE: ./node_modules/react-autosize-textarea/lib/index.js
var react_autosize_textarea_lib = __webpack_require__("O6Fj");
var react_autosize_textarea_lib_default = /*#__PURE__*/__webpack_require__.n(react_autosize_textarea_lib);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-text-editor/index.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






var post_text_editor_PostTextEditor =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostTextEditor, _Component);

  function PostTextEditor() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostTextEditor);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostTextEditor).apply(this, arguments));
    _this.edit = _this.edit.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.stopEditing = _this.stopEditing.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.state = {};
    return _this;
  }

  Object(createClass["a" /* default */])(PostTextEditor, [{
    key: "edit",

    /**
     * Handles a textarea change event to notify the onChange prop callback and
     * reflect the new value in the component's own state. This marks the start
     * of the user's edits, if not already changed, preventing future props
     * changes to value from replacing the rendered value. This is expected to
     * be followed by a reset to dirty state via `stopEditing`.
     *
     * @see stopEditing
     *
     * @param {Event} event Change event.
     */
    value: function edit(event) {
      var value = event.target.value;
      this.props.onChange(value);
      this.setState({
        value: value,
        isDirty: true
      });
    }
    /**
     * Function called when the user has completed their edits, responsible for
     * ensuring that changes, if made, are surfaced to the onPersist prop
     * callback and resetting dirty state.
     */

  }, {
    key: "stopEditing",
    value: function stopEditing() {
      if (this.state.isDirty) {
        this.props.onPersist(this.state.value);
        this.setState({
          isDirty: false
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var value = this.state.value;
      var instanceId = this.props.instanceId;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("label", {
        htmlFor: "post-content-".concat(instanceId),
        className: "screen-reader-text"
      }, Object(external_this_wp_i18n_["__"])('Type text or HTML')), Object(external_this_wp_element_["createElement"])(react_autosize_textarea_lib_default.a, {
        autoComplete: "off",
        dir: "auto",
        value: value,
        onChange: this.edit,
        onBlur: this.stopEditing,
        className: "editor-post-text-editor",
        id: "post-content-".concat(instanceId),
        placeholder: Object(external_this_wp_i18n_["__"])('Start writing with text or HTML')
      }));
    }
  }], [{
    key: "getDerivedStateFromProps",
    value: function getDerivedStateFromProps(props, state) {
      if (state.isDirty) {
        return null;
      }

      return {
        value: props.value,
        isDirty: false
      };
    }
  }]);

  return PostTextEditor;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var post_text_editor = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostContent = _select.getEditedPostContent;

  return {
    value: getEditedPostContent()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      editPost = _dispatch.editPost,
      resetEditorBlocks = _dispatch.resetEditorBlocks;

  return {
    onChange: function onChange(content) {
      editPost({
        content: content
      });
    },
    onPersist: function onPersist(content) {
      var blocks = Object(external_this_wp_blocks_["parse"])(content);
      resetEditorBlocks(blocks);
    }
  };
}), external_this_wp_compose_["withInstanceId"]])(post_text_editor_PostTextEditor));

// EXTERNAL MODULE: external {"this":["wp","htmlEntities"]}
var external_this_wp_htmlEntities_ = __webpack_require__("rmEH");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-permalink/editor.js








/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



var editor_PostPermalinkEditor =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostPermalinkEditor, _Component);

  function PostPermalinkEditor(_ref) {
    var _this;

    var permalinkParts = _ref.permalinkParts,
        slug = _ref.slug;

    Object(classCallCheck["a" /* default */])(this, PostPermalinkEditor);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostPermalinkEditor).apply(this, arguments));
    _this.state = {
      editedPostName: slug || permalinkParts.postName
    };
    _this.onSavePermalink = _this.onSavePermalink.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(PostPermalinkEditor, [{
    key: "onSavePermalink",
    value: function onSavePermalink(event) {
      var postName = cleanForSlug(this.state.editedPostName);
      event.preventDefault();
      this.props.onSave();

      if (postName === this.props.postName) {
        return;
      }

      this.props.editPost({
        slug: postName
      });
      this.setState({
        editedPostName: postName
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props$permalink = this.props.permalinkParts,
          prefix = _this$props$permalink.prefix,
          suffix = _this$props$permalink.suffix;
      var editedPostName = this.state.editedPostName;
      /* eslint-disable jsx-a11y/no-autofocus */
      // Autofocus is allowed here, as this mini-UI is only loaded when the user clicks to open it.

      return Object(external_this_wp_element_["createElement"])("form", {
        className: "editor-post-permalink-editor",
        onSubmit: this.onSavePermalink
      }, Object(external_this_wp_element_["createElement"])("span", {
        className: "editor-post-permalink__editor-container"
      }, Object(external_this_wp_element_["createElement"])("span", {
        className: "editor-post-permalink-editor__prefix"
      }, prefix), Object(external_this_wp_element_["createElement"])("input", {
        className: "editor-post-permalink-editor__edit",
        "aria-label": Object(external_this_wp_i18n_["__"])('Edit post permalink'),
        value: editedPostName,
        onChange: function onChange(event) {
          return _this2.setState({
            editedPostName: event.target.value
          });
        },
        type: "text",
        autoFocus: true
      }), Object(external_this_wp_element_["createElement"])("span", {
        className: "editor-post-permalink-editor__suffix"
      }, suffix), "\u200E"), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: "editor-post-permalink-editor__save",
        isLarge: true,
        onClick: this.onSavePermalink
      }, Object(external_this_wp_i18n_["__"])('Save')));
      /* eslint-enable jsx-a11y/no-autofocus */
    }
  }]);

  return PostPermalinkEditor;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var post_permalink_editor = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getPermalinkParts = _select.getPermalinkParts;

  return {
    permalinkParts: getPermalinkParts()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      editPost = _dispatch.editPost;

  return {
    editPost: editPost
  };
})])(editor_PostPermalinkEditor));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-permalink/index.js








/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */




var post_permalink_PostPermalink =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostPermalink, _Component);

  function PostPermalink() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostPermalink);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostPermalink).apply(this, arguments));
    _this.addVisibilityCheck = _this.addVisibilityCheck.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onVisibilityChange = _this.onVisibilityChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.state = {
      isCopied: false,
      isEditingPermalink: false
    };
    return _this;
  }

  Object(createClass["a" /* default */])(PostPermalink, [{
    key: "addVisibilityCheck",
    value: function addVisibilityCheck() {
      window.addEventListener('visibilitychange', this.onVisibilityChange);
    }
  }, {
    key: "onVisibilityChange",
    value: function onVisibilityChange() {
      var _this$props = this.props,
          isEditable = _this$props.isEditable,
          refreshPost = _this$props.refreshPost; // If the user just returned after having clicked the "Change Permalinks" button,
      // fetch a new copy of the post from the server, just in case they enabled permalinks.

      if (!isEditable && 'visible' === document.visibilityState) {
        refreshPost();
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps, prevState) {
      // If we've just stopped editing the permalink, focus on the new permalink.
      if (prevState.isEditingPermalink && !this.state.isEditingPermalink) {
        this.linkElement.focus();
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      window.removeEventListener('visibilitychange', this.addVisibilityCheck);
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props2 = this.props,
          isEditable = _this$props2.isEditable,
          isNew = _this$props2.isNew,
          isPublished = _this$props2.isPublished,
          isViewable = _this$props2.isViewable,
          permalinkParts = _this$props2.permalinkParts,
          postLink = _this$props2.postLink,
          postSlug = _this$props2.postSlug,
          postID = _this$props2.postID,
          postTitle = _this$props2.postTitle;

      if (isNew || !isViewable || !permalinkParts || !postLink) {
        return null;
      }

      var _this$state = this.state,
          isCopied = _this$state.isCopied,
          isEditingPermalink = _this$state.isEditingPermalink;
      var ariaLabel = isCopied ? Object(external_this_wp_i18n_["__"])('Permalink copied') : Object(external_this_wp_i18n_["__"])('Copy the permalink');
      var prefix = permalinkParts.prefix,
          suffix = permalinkParts.suffix;
      var slug = Object(external_this_wp_url_["safeDecodeURIComponent"])(postSlug) || cleanForSlug(postTitle) || postID;
      var samplePermalink = isEditable ? prefix + slug + suffix : prefix;
      return Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-permalink"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ClipboardButton"], {
        className: classnames_default()('editor-post-permalink__copy', {
          'is-copied': isCopied
        }),
        text: samplePermalink,
        label: ariaLabel,
        onCopy: function onCopy() {
          return _this2.setState({
            isCopied: true
          });
        },
        "aria-disabled": isCopied,
        icon: "admin-links"
      }), Object(external_this_wp_element_["createElement"])("span", {
        className: "editor-post-permalink__label"
      }, Object(external_this_wp_i18n_["__"])('Permalink:')), !isEditingPermalink && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
        className: "editor-post-permalink__link",
        href: !isPublished ? postLink : samplePermalink,
        target: "_blank",
        ref: function ref(linkElement) {
          return _this2.linkElement = linkElement;
        }
      }, Object(external_this_wp_url_["safeDecodeURI"])(samplePermalink), "\u200E"), isEditingPermalink && Object(external_this_wp_element_["createElement"])(post_permalink_editor, {
        slug: slug,
        onSave: function onSave() {
          return _this2.setState({
            isEditingPermalink: false
          });
        }
      }), isEditable && !isEditingPermalink && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: "editor-post-permalink__edit",
        isLarge: true,
        onClick: function onClick() {
          return _this2.setState({
            isEditingPermalink: true
          });
        }
      }, Object(external_this_wp_i18n_["__"])('Edit')), !isEditable && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: "editor-post-permalink__change",
        isLarge: true,
        href: getWPAdminURL('options-permalink.php'),
        onClick: this.addVisibilityCheck,
        target: "_blank"
      }, Object(external_this_wp_i18n_["__"])('Change Permalinks')));
    }
  }]);

  return PostPermalink;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var post_permalink = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isEditedPostNew = _select.isEditedPostNew,
      isPermalinkEditable = _select.isPermalinkEditable,
      getCurrentPost = _select.getCurrentPost,
      getPermalinkParts = _select.getPermalinkParts,
      getEditedPostAttribute = _select.getEditedPostAttribute,
      isCurrentPostPublished = _select.isCurrentPostPublished;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  var _getCurrentPost = getCurrentPost(),
      id = _getCurrentPost.id,
      link = _getCurrentPost.link;

  var postTypeName = getEditedPostAttribute('type');
  var postType = getPostType(postTypeName);
  return {
    isNew: isEditedPostNew(),
    postLink: link,
    permalinkParts: getPermalinkParts(),
    postSlug: getEditedPostAttribute('slug'),
    isEditable: isPermalinkEditable(),
    isPublished: isCurrentPostPublished(),
    postTitle: getEditedPostAttribute('title'),
    postID: id,
    isViewable: Object(external_lodash_["get"])(postType, ['viewable'], false)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      refreshPost = _dispatch.refreshPost;

  return {
    refreshPost: refreshPost
  };
})])(post_permalink_PostPermalink));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-title/index.js








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

var REGEXP_NEWLINES = /[\r\n]+/g;

var post_title_PostTitle =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PostTitle, _Component);

  function PostTitle() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PostTitle);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PostTitle).apply(this, arguments));
    _this.onChange = _this.onChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelect = _this.onSelect.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onUnselect = _this.onUnselect.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onKeyDown = _this.onKeyDown.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.redirectHistory = _this.redirectHistory.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.state = {
      isSelected: false
    };
    return _this;
  }

  Object(createClass["a" /* default */])(PostTitle, [{
    key: "handleFocusOutside",
    value: function handleFocusOutside() {
      this.onUnselect();
    }
  }, {
    key: "onSelect",
    value: function onSelect() {
      this.setState({
        isSelected: true
      });
      this.props.clearSelectedBlock();
    }
  }, {
    key: "onUnselect",
    value: function onUnselect() {
      this.setState({
        isSelected: false
      });
    }
  }, {
    key: "onChange",
    value: function onChange(event) {
      var newTitle = event.target.value.replace(REGEXP_NEWLINES, ' ');
      this.props.onUpdate(newTitle);
    }
  }, {
    key: "onKeyDown",
    value: function onKeyDown(event) {
      if (event.keyCode === external_this_wp_keycodes_["ENTER"]) {
        event.preventDefault();
        this.props.onEnterPress();
      }
    }
    /**
     * Emulates behavior of an undo or redo on its corresponding key press
     * combination. This is a workaround to React's treatment of undo in a
     * controlled textarea where characters are updated one at a time.
     * Instead, leverage the store's undo handling of title changes.
     *
     * @see https://github.com/facebook/react/issues/8514
     *
     * @param {KeyboardEvent} event Key event.
     */

  }, {
    key: "redirectHistory",
    value: function redirectHistory(event) {
      if (event.shiftKey) {
        this.props.onRedo();
      } else {
        this.props.onUndo();
      }

      event.preventDefault();
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          hasFixedToolbar = _this$props.hasFixedToolbar,
          isCleanNewPost = _this$props.isCleanNewPost,
          isFocusMode = _this$props.isFocusMode,
          isPostTypeViewable = _this$props.isPostTypeViewable,
          instanceId = _this$props.instanceId,
          placeholder = _this$props.placeholder,
          title = _this$props.title;
      var isSelected = this.state.isSelected; // The wp-block className is important for editor styles.

      var className = classnames_default()('wp-block editor-post-title__block', {
        'is-selected': isSelected,
        'is-focus-mode': isFocusMode,
        'has-fixed-toolbar': hasFixedToolbar
      });
      var decodedPlaceholder = Object(external_this_wp_htmlEntities_["decodeEntities"])(placeholder);
      return Object(external_this_wp_element_["createElement"])(post_type_support_check, {
        supportKeys: "title"
      }, Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-post-title"
      }, Object(external_this_wp_element_["createElement"])("div", {
        className: className
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["KeyboardShortcuts"], {
        shortcuts: {
          'mod+z': this.redirectHistory,
          'mod+shift+z': this.redirectHistory
        }
      }, Object(external_this_wp_element_["createElement"])("label", {
        htmlFor: "post-title-".concat(instanceId),
        className: "screen-reader-text"
      }, decodedPlaceholder || Object(external_this_wp_i18n_["__"])('Add title')), Object(external_this_wp_element_["createElement"])(react_autosize_textarea_lib_default.a, {
        id: "post-title-".concat(instanceId),
        className: "editor-post-title__input",
        value: title,
        onChange: this.onChange,
        placeholder: decodedPlaceholder || Object(external_this_wp_i18n_["__"])('Add title'),
        onFocus: this.onSelect,
        onKeyDown: this.onKeyDown,
        onKeyPress: this.onUnselect
        /*
        	Only autofocus the title when the post is entirely empty.
        	This should only happen for a new post, which means we
        	focus the title on new post so the author can start typing
        	right away, without needing to click anything.
        */

        /* eslint-disable jsx-a11y/no-autofocus */
        ,
        autoFocus: isCleanNewPost
        /* eslint-enable jsx-a11y/no-autofocus */

      })), isSelected && isPostTypeViewable && Object(external_this_wp_element_["createElement"])(post_permalink, null))));
    }
  }]);

  return PostTitle;
}(external_this_wp_element_["Component"]);

var post_title_applyWithSelect = Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute,
      isCleanNewPost = _select.isCleanNewPost;

  var _select2 = select('core/block-editor'),
      getSettings = _select2.getSettings;

  var _select3 = select('core'),
      getPostType = _select3.getPostType;

  var postType = getPostType(getEditedPostAttribute('type'));

  var _getSettings = getSettings(),
      titlePlaceholder = _getSettings.titlePlaceholder,
      focusMode = _getSettings.focusMode,
      hasFixedToolbar = _getSettings.hasFixedToolbar;

  return {
    isCleanNewPost: isCleanNewPost(),
    title: getEditedPostAttribute('title'),
    isPostTypeViewable: Object(external_lodash_["get"])(postType, ['viewable'], false),
    placeholder: titlePlaceholder,
    isFocusMode: focusMode,
    hasFixedToolbar: hasFixedToolbar
  };
});
var post_title_applyWithDispatch = Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/block-editor'),
      insertDefaultBlock = _dispatch.insertDefaultBlock,
      clearSelectedBlock = _dispatch.clearSelectedBlock;

  var _dispatch2 = dispatch('core/editor'),
      editPost = _dispatch2.editPost,
      undo = _dispatch2.undo,
      redo = _dispatch2.redo;

  return {
    onEnterPress: function onEnterPress() {
      insertDefaultBlock(undefined, undefined, 0);
    },
    onUpdate: function onUpdate(title) {
      editPost({
        title: title
      });
    },
    onUndo: undo,
    onRedo: redo,
    clearSelectedBlock: clearSelectedBlock
  };
});
/* harmony default export */ var post_title = (Object(external_this_wp_compose_["compose"])(post_title_applyWithSelect, post_title_applyWithDispatch, external_this_wp_compose_["withInstanceId"], external_this_wp_components_["withFocusOutside"])(post_title_PostTitle));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-trash/index.js



/**
 * WordPress dependencies
 */





function PostTrash(_ref) {
  var isNew = _ref.isNew,
      postId = _ref.postId,
      postType = _ref.postType,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["isNew", "postId", "postType"]);

  if (isNew || !postId) {
    return null;
  }

  var onClick = function onClick() {
    return props.trashPost(postId, postType);
  };

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    className: "editor-post-trash button-link-delete",
    onClick: onClick,
    isDefault: true,
    isLarge: true
  }, Object(external_this_wp_i18n_["__"])('Move to trash'));
}

/* harmony default export */ var post_trash = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isEditedPostNew = _select.isEditedPostNew,
      getCurrentPostId = _select.getCurrentPostId,
      getCurrentPostType = _select.getCurrentPostType;

  return {
    isNew: isEditedPostNew(),
    postId: getCurrentPostId(),
    postType: getCurrentPostType()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    trashPost: dispatch('core/editor').trashPost
  };
})])(PostTrash));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-trash/check.js
/**
 * WordPress dependencies
 */


function PostTrashCheck(_ref) {
  var isNew = _ref.isNew,
      postId = _ref.postId,
      children = _ref.children;

  if (isNew || !postId) {
    return null;
  }

  return children;
}

/* harmony default export */ var post_trash_check = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isEditedPostNew = _select.isEditedPostNew,
      getCurrentPostId = _select.getCurrentPostId;

  return {
    isNew: isEditedPostNew(),
    postId: getCurrentPostId()
  };
})(PostTrashCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PostVisibilityCheck(_ref) {
  var hasPublishAction = _ref.hasPublishAction,
      render = _ref.render;
  var canEdit = hasPublishAction;
  return render({
    canEdit: canEdit
  });
}
/* harmony default export */ var post_visibility_check = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getCurrentPost = _select.getCurrentPost,
      getCurrentPostType = _select.getCurrentPostType;

  return {
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    postType: getCurrentPostType()
  };
})])(PostVisibilityCheck));

// EXTERNAL MODULE: external {"this":["wp","wordcount"]}
var external_this_wp_wordcount_ = __webpack_require__("7fqt");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/word-count/index.js


/**
 * WordPress dependencies
 */




function WordCount(_ref) {
  var content = _ref.content;

  /*
   * translators: If your word count is based on single characters (e.g. East Asian characters),
   * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
   * Do not translate into your own language.
   */
  var wordCountType = Object(external_this_wp_i18n_["_x"])('words', 'Word count type. Do not translate!');

  return Object(external_this_wp_element_["createElement"])("span", {
    className: "word-count"
  }, Object(external_this_wp_wordcount_["count"])(content, wordCountType));
}

/* harmony default export */ var word_count = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    content: select('core/editor').getEditedPostAttribute('content')
  };
})(WordCount));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/table-of-contents/panel.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function TableOfContentsPanel(_ref) {
  var headingCount = _ref.headingCount,
      paragraphCount = _ref.paragraphCount,
      numberOfBlocks = _ref.numberOfBlocks,
      hasOutlineItemsDisabled = _ref.hasOutlineItemsDisabled,
      onRequestClose = _ref.onRequestClose;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", {
    className: "table-of-contents__counts",
    role: "note",
    "aria-label": Object(external_this_wp_i18n_["__"])('Document Statistics'),
    tabIndex: "0"
  }, Object(external_this_wp_element_["createElement"])("div", {
    className: "table-of-contents__count"
  }, Object(external_this_wp_i18n_["__"])('Words'), Object(external_this_wp_element_["createElement"])(word_count, null)), Object(external_this_wp_element_["createElement"])("div", {
    className: "table-of-contents__count"
  }, Object(external_this_wp_i18n_["__"])('Headings'), Object(external_this_wp_element_["createElement"])("span", {
    className: "table-of-contents__number"
  }, headingCount)), Object(external_this_wp_element_["createElement"])("div", {
    className: "table-of-contents__count"
  }, Object(external_this_wp_i18n_["__"])('Paragraphs'), Object(external_this_wp_element_["createElement"])("span", {
    className: "table-of-contents__number"
  }, paragraphCount)), Object(external_this_wp_element_["createElement"])("div", {
    className: "table-of-contents__count"
  }, Object(external_this_wp_i18n_["__"])('Blocks'), Object(external_this_wp_element_["createElement"])("span", {
    className: "table-of-contents__number"
  }, numberOfBlocks))), headingCount > 0 && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("hr", null), Object(external_this_wp_element_["createElement"])("span", {
    className: "table-of-contents__title"
  }, Object(external_this_wp_i18n_["__"])('Document Outline')), Object(external_this_wp_element_["createElement"])(document_outline, {
    onSelect: onRequestClose,
    hasOutlineItemsDisabled: hasOutlineItemsDisabled
  })));
}

/* harmony default export */ var panel = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getGlobalBlockCount = _select.getGlobalBlockCount;

  return {
    headingCount: getGlobalBlockCount('core/heading'),
    paragraphCount: getGlobalBlockCount('core/paragraph'),
    numberOfBlocks: getGlobalBlockCount()
  };
})(TableOfContentsPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/table-of-contents/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function TableOfContents(_ref) {
  var hasBlocks = _ref.hasBlocks,
      hasOutlineItemsDisabled = _ref.hasOutlineItemsDisabled;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dropdown"], {
    position: "bottom",
    className: "table-of-contents",
    contentClassName: "table-of-contents__popover",
    renderToggle: function renderToggle(_ref2) {
      var isOpen = _ref2.isOpen,
          onToggle = _ref2.onToggle;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        onClick: hasBlocks ? onToggle : undefined,
        icon: "info-outline",
        "aria-expanded": isOpen,
        label: Object(external_this_wp_i18n_["__"])('Content structure'),
        labelPosition: "bottom",
        "aria-disabled": !hasBlocks
      });
    },
    renderContent: function renderContent(_ref3) {
      var onClose = _ref3.onClose;
      return Object(external_this_wp_element_["createElement"])(panel, {
        onRequestClose: onClose,
        hasOutlineItemsDisabled: hasOutlineItemsDisabled
      });
    }
  });
}

/* harmony default export */ var table_of_contents = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    hasBlocks: !!select('core/block-editor').getBlockCount()
  };
})(TableOfContents));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/unsaved-changes-warning/index.js







/**
 * WordPress dependencies
 */




var unsaved_changes_warning_UnsavedChangesWarning =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(UnsavedChangesWarning, _Component);

  function UnsavedChangesWarning() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, UnsavedChangesWarning);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(UnsavedChangesWarning).apply(this, arguments));
    _this.warnIfUnsavedChanges = _this.warnIfUnsavedChanges.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(UnsavedChangesWarning, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      window.addEventListener('beforeunload', this.warnIfUnsavedChanges);
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      window.removeEventListener('beforeunload', this.warnIfUnsavedChanges);
    }
    /**
     * Warns the user if there are unsaved changes before leaving the editor.
     *
     * @param {Event} event `beforeunload` event.
     *
     * @return {?string} Warning prompt message, if unsaved changes exist.
     */

  }, {
    key: "warnIfUnsavedChanges",
    value: function warnIfUnsavedChanges(event) {
      var isDirty = this.props.isDirty;

      if (isDirty) {
        event.returnValue = Object(external_this_wp_i18n_["__"])('You have unsaved changes. If you proceed, they will be lost.');
        return event.returnValue;
      }
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return UnsavedChangesWarning;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var unsaved_changes_warning = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isDirty: select('core/editor').isEditedPostDirty()
  };
})(unsaved_changes_warning_UnsavedChangesWarning));

// EXTERNAL MODULE: ./node_modules/memize/index.js
var memize = __webpack_require__("4eJC");
var memize_default = /*#__PURE__*/__webpack_require__.n(memize);

// EXTERNAL MODULE: ./node_modules/traverse/index.js
var traverse = __webpack_require__("eGrx");
var traverse_default = /*#__PURE__*/__webpack_require__.n(traverse);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/ast/parse.js


/* eslint-disable @wordpress/no-unused-vars-before-return */
// Adapted from https://github.com/reworkcss/css
// because we needed to remove source map support.
// http://www.w3.org/TR/CSS21/grammar.htm
// https://github.com/visionmedia/css-parse/pull/49#issuecomment-30088027
var commentre = /\/\*[^*]*\*+([^/*][^*]*\*+)*\//g;
/* harmony default export */ var parse = (function (css, options) {
  options = options || {};
  /**
    * Positional.
    */

  var lineno = 1;
  var column = 1;
  /**
    * Update lineno and column based on `str`.
    */

  function updatePosition(str) {
    var lines = str.match(/\n/g);

    if (lines) {
      lineno += lines.length;
    }

    var i = str.lastIndexOf('\n'); // eslint-disable-next-line no-bitwise

    column = ~i ? str.length - i : column + str.length;
  }
  /**
    * Mark position and patch `node.position`.
    */


  function position() {
    var start = {
      line: lineno,
      column: column
    };
    return function (node) {
      node.position = new Position(start);
      whitespace();
      return node;
    };
  }
  /**
    * Store position information for a node
    */


  function Position(start) {
    this.start = start;
    this.end = {
      line: lineno,
      column: column
    };
    this.source = options.source;
  }
  /**
    * Non-enumerable source string
    */


  Position.prototype.content = css;
  /**
    * Error `msg`.
    */

  var errorsList = [];

  function error(msg) {
    var err = new Error(options.source + ':' + lineno + ':' + column + ': ' + msg);
    err.reason = msg;
    err.filename = options.source;
    err.line = lineno;
    err.column = column;
    err.source = css;

    if (options.silent) {
      errorsList.push(err);
    } else {
      throw err;
    }
  }
  /**
    * Parse stylesheet.
    */


  function stylesheet() {
    var rulesList = rules();
    return {
      type: 'stylesheet',
      stylesheet: {
        source: options.source,
        rules: rulesList,
        parsingErrors: errorsList
      }
    };
  }
  /**
    * Opening brace.
    */


  function open() {
    return match(/^{\s*/);
  }
  /**
    * Closing brace.
    */


  function close() {
    return match(/^}/);
  }
  /**
    * Parse ruleset.
    */


  function rules() {
    var node;
    var accumulator = [];
    whitespace();
    comments(accumulator);

    while (css.length && css.charAt(0) !== '}' && (node = atrule() || rule())) {
      if (node !== false) {
        accumulator.push(node);
        comments(accumulator);
      }
    }

    return accumulator;
  }
  /**
    * Match `re` and return captures.
    */


  function match(re) {
    var m = re.exec(css);

    if (!m) {
      return;
    }

    var str = m[0];
    updatePosition(str);
    css = css.slice(str.length);
    return m;
  }
  /**
    * Parse whitespace.
    */


  function whitespace() {
    match(/^\s*/);
  }
  /**
    * Parse comments;
    */


  function comments(accumulator) {
    var c;
    accumulator = accumulator || []; // eslint-disable-next-line no-cond-assign

    while (c = comment()) {
      if (c !== false) {
        accumulator.push(c);
      }
    }

    return accumulator;
  }
  /**
    * Parse comment.
    */


  function comment() {
    var pos = position();

    if ('/' !== css.charAt(0) || '*' !== css.charAt(1)) {
      return;
    }

    var i = 2;

    while ('' !== css.charAt(i) && ('*' !== css.charAt(i) || '/' !== css.charAt(i + 1))) {
      ++i;
    }

    i += 2;

    if ('' === css.charAt(i - 1)) {
      return error('End of comment missing');
    }

    var str = css.slice(2, i - 2);
    column += 2;
    updatePosition(str);
    css = css.slice(i);
    column += 2;
    return pos({
      type: 'comment',
      comment: str
    });
  }
  /**
    * Parse selector.
    */


  function selector() {
    var m = match(/^([^{]+)/);

    if (!m) {
      return;
    }
    /* @fix Remove all comments from selectors
       * http://ostermiller.org/findcomment.html */


    return trim(m[0]).replace(/\/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*\/+/g, '').replace(/"(?:\\"|[^"])*"|'(?:\\'|[^'])*'/g, function (matched) {
      return matched.replace(/,/g, "\u200C");
    }).split(/\s*(?![^(]*\)),\s*/).map(function (s) {
      return s.replace(/\u200C/g, ',');
    });
  }
  /**
    * Parse declaration.
    */


  function declaration() {
    var pos = position(); // prop

    var prop = match(/^(\*?[-#\/\*\\\w]+(\[[0-9a-z_-]+\])?)\s*/);

    if (!prop) {
      return;
    }

    prop = trim(prop[0]); // :

    if (!match(/^:\s*/)) {
      return error("property missing ':'");
    } // val


    var val = match(/^((?:'(?:\\'|.)*?'|"(?:\\"|.)*?"|\([^\)]*?\)|[^};])+)/);
    var ret = pos({
      type: 'declaration',
      property: prop.replace(commentre, ''),
      value: val ? trim(val[0]).replace(commentre, '') : ''
    }); // ;

    match(/^[;\s]*/);
    return ret;
  }
  /**
    * Parse declarations.
    */


  function declarations() {
    var decls = [];

    if (!open()) {
      return error("missing '{'");
    }

    comments(decls); // declarations

    var decl; // eslint-disable-next-line no-cond-assign

    while (decl = declaration()) {
      if (decl !== false) {
        decls.push(decl);
        comments(decls);
      }
    }

    if (!close()) {
      return error("missing '}'");
    }

    return decls;
  }
  /**
    * Parse keyframe.
    */


  function keyframe() {
    var m;
    var vals = [];
    var pos = position(); // eslint-disable-next-line no-cond-assign

    while (m = match(/^((\d+\.\d+|\.\d+|\d+)%?|[a-z]+)\s*/)) {
      vals.push(m[1]);
      match(/^,\s*/);
    }

    if (!vals.length) {
      return;
    }

    return pos({
      type: 'keyframe',
      values: vals,
      declarations: declarations()
    });
  }
  /**
    * Parse keyframes.
    */


  function atkeyframes() {
    var pos = position();
    var m = match(/^@([-\w]+)?keyframes\s*/);

    if (!m) {
      return;
    }

    var vendor = m[1]; // identifier

    m = match(/^([-\w]+)\s*/);

    if (!m) {
      return error('@keyframes missing name');
    }

    var name = m[1];

    if (!open()) {
      return error("@keyframes missing '{'");
    }

    var frame;
    var frames = comments(); // eslint-disable-next-line no-cond-assign

    while (frame = keyframe()) {
      frames.push(frame);
      frames = frames.concat(comments());
    }

    if (!close()) {
      return error("@keyframes missing '}'");
    }

    return pos({
      type: 'keyframes',
      name: name,
      vendor: vendor,
      keyframes: frames
    });
  }
  /**
    * Parse supports.
    */


  function atsupports() {
    var pos = position();
    var m = match(/^@supports *([^{]+)/);

    if (!m) {
      return;
    }

    var supports = trim(m[1]);

    if (!open()) {
      return error("@supports missing '{'");
    }

    var style = comments().concat(rules());

    if (!close()) {
      return error("@supports missing '}'");
    }

    return pos({
      type: 'supports',
      supports: supports,
      rules: style
    });
  }
  /**
    * Parse host.
    */


  function athost() {
    var pos = position();
    var m = match(/^@host\s*/);

    if (!m) {
      return;
    }

    if (!open()) {
      return error("@host missing '{'");
    }

    var style = comments().concat(rules());

    if (!close()) {
      return error("@host missing '}'");
    }

    return pos({
      type: 'host',
      rules: style
    });
  }
  /**
    * Parse media.
    */


  function atmedia() {
    var pos = position();
    var m = match(/^@media *([^{]+)/);

    if (!m) {
      return;
    }

    var media = trim(m[1]);

    if (!open()) {
      return error("@media missing '{'");
    }

    var style = comments().concat(rules());

    if (!close()) {
      return error("@media missing '}'");
    }

    return pos({
      type: 'media',
      media: media,
      rules: style
    });
  }
  /**
    * Parse custom-media.
    */


  function atcustommedia() {
    var pos = position();
    var m = match(/^@custom-media\s+(--[^\s]+)\s*([^{;]+);/);

    if (!m) {
      return;
    }

    return pos({
      type: 'custom-media',
      name: trim(m[1]),
      media: trim(m[2])
    });
  }
  /**
    * Parse paged media.
    */


  function atpage() {
    var pos = position();
    var m = match(/^@page */);

    if (!m) {
      return;
    }

    var sel = selector() || [];

    if (!open()) {
      return error("@page missing '{'");
    }

    var decls = comments(); // declarations

    var decl; // eslint-disable-next-line no-cond-assign

    while (decl = declaration()) {
      decls.push(decl);
      decls = decls.concat(comments());
    }

    if (!close()) {
      return error("@page missing '}'");
    }

    return pos({
      type: 'page',
      selectors: sel,
      declarations: decls
    });
  }
  /**
    * Parse document.
    */


  function atdocument() {
    var pos = position();
    var m = match(/^@([-\w]+)?document *([^{]+)/);

    if (!m) {
      return;
    }

    var vendor = trim(m[1]);
    var doc = trim(m[2]);

    if (!open()) {
      return error("@document missing '{'");
    }

    var style = comments().concat(rules());

    if (!close()) {
      return error("@document missing '}'");
    }

    return pos({
      type: 'document',
      document: doc,
      vendor: vendor,
      rules: style
    });
  }
  /**
    * Parse font-face.
    */


  function atfontface() {
    var pos = position();
    var m = match(/^@font-face\s*/);

    if (!m) {
      return;
    }

    if (!open()) {
      return error("@font-face missing '{'");
    }

    var decls = comments(); // declarations

    var decl; // eslint-disable-next-line no-cond-assign

    while (decl = declaration()) {
      decls.push(decl);
      decls = decls.concat(comments());
    }

    if (!close()) {
      return error("@font-face missing '}'");
    }

    return pos({
      type: 'font-face',
      declarations: decls
    });
  }
  /**
    * Parse import
    */


  var atimport = _compileAtrule('import');
  /**
    * Parse charset
    */


  var atcharset = _compileAtrule('charset');
  /**
    * Parse namespace
    */


  var atnamespace = _compileAtrule('namespace');
  /**
    * Parse non-block at-rules
    */


  function _compileAtrule(name) {
    var re = new RegExp('^@' + name + '\\s*([^;]+);');
    return function () {
      var pos = position();
      var m = match(re);

      if (!m) {
        return;
      }

      var ret = {
        type: name
      };
      ret[name] = m[1].trim();
      return pos(ret);
    };
  }
  /**
    * Parse at rule.
    */


  function atrule() {
    if (css[0] !== '@') {
      return;
    }

    return atkeyframes() || atmedia() || atcustommedia() || atsupports() || atimport() || atcharset() || atnamespace() || atdocument() || atpage() || athost() || atfontface();
  }
  /**
    * Parse rule.
    */


  function rule() {
    var pos = position();
    var sel = selector();

    if (!sel) {
      return error('selector missing');
    }

    comments();
    return pos({
      type: 'rule',
      selectors: sel,
      declarations: declarations()
    });
  }

  return addParent(stylesheet());
});
/**
 * Trim `str`.
 */

function trim(str) {
  return str ? str.replace(/^\s+|\s+$/g, '') : '';
}
/**
 * Adds non-enumerable parent node reference to each node.
 */


function addParent(obj, parent) {
  var isNode = obj && typeof obj.type === 'string';
  var childParent = isNode ? obj : parent;

  for (var k in obj) {
    var value = obj[k];

    if (Array.isArray(value)) {
      value.forEach(function (v) {
        addParent(v, childParent);
      });
    } else if (value && Object(esm_typeof["a" /* default */])(value) === 'object') {
      addParent(value, childParent);
    }
  }

  if (isNode) {
    Object.defineProperty(obj, 'parent', {
      configurable: true,
      writable: true,
      enumerable: false,
      value: parent || null
    });
  }

  return obj;
}

// EXTERNAL MODULE: ./node_modules/inherits/inherits_browser.js
var inherits_browser = __webpack_require__("P7XM");
var inherits_browser_default = /*#__PURE__*/__webpack_require__.n(inherits_browser);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/ast/stringify/compiler.js
// Adapted from https://github.com/reworkcss/css
// because we needed to remove source map support.

/**
 * Expose `Compiler`.
 */
/* harmony default export */ var stringify_compiler = (Compiler);
/**
 * Initialize a compiler.
 *
 * @param {Type} name
 * @return {Type}
 * @api public
 */

function Compiler(opts) {
  this.options = opts || {};
}
/**
 * Emit `str`
 */


Compiler.prototype.emit = function (str) {
  return str;
};
/**
 * Visit `node`.
 */


Compiler.prototype.visit = function (node) {
  return this[node.type](node);
};
/**
 * Map visit over array of `nodes`, optionally using a `delim`
 */


Compiler.prototype.mapVisit = function (nodes, delim) {
  var buf = '';
  delim = delim || '';

  for (var i = 0, length = nodes.length; i < length; i++) {
    buf += this.visit(nodes[i]);

    if (delim && i < length - 1) {
      buf += this.emit(delim);
    }
  }

  return buf;
};

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/ast/stringify/compress.js
// Adapted from https://github.com/reworkcss/css
// because we needed to remove source map support.

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Expose compiler.
 */

/* harmony default export */ var compress = (compress_Compiler);
/**
 * Initialize a new `Compiler`.
 */

function compress_Compiler(options) {
  stringify_compiler.call(this, options);
}
/**
 * Inherit from `Base.prototype`.
 */


inherits_browser_default()(compress_Compiler, stringify_compiler);
/**
 * Compile `node`.
 */

compress_Compiler.prototype.compile = function (node) {
  return node.stylesheet.rules.map(this.visit, this).join('');
};
/**
 * Visit comment node.
 */


compress_Compiler.prototype.comment = function (node) {
  return this.emit('', node.position);
};
/**
 * Visit import node.
 */


compress_Compiler.prototype.import = function (node) {
  return this.emit('@import ' + node.import + ';', node.position);
};
/**
 * Visit media node.
 */


compress_Compiler.prototype.media = function (node) {
  return this.emit('@media ' + node.media, node.position) + this.emit('{') + this.mapVisit(node.rules) + this.emit('}');
};
/**
 * Visit document node.
 */


compress_Compiler.prototype.document = function (node) {
  var doc = '@' + (node.vendor || '') + 'document ' + node.document;
  return this.emit(doc, node.position) + this.emit('{') + this.mapVisit(node.rules) + this.emit('}');
};
/**
 * Visit charset node.
 */


compress_Compiler.prototype.charset = function (node) {
  return this.emit('@charset ' + node.charset + ';', node.position);
};
/**
 * Visit namespace node.
 */


compress_Compiler.prototype.namespace = function (node) {
  return this.emit('@namespace ' + node.namespace + ';', node.position);
};
/**
 * Visit supports node.
 */


compress_Compiler.prototype.supports = function (node) {
  return this.emit('@supports ' + node.supports, node.position) + this.emit('{') + this.mapVisit(node.rules) + this.emit('}');
};
/**
 * Visit keyframes node.
 */


compress_Compiler.prototype.keyframes = function (node) {
  return this.emit('@' + (node.vendor || '') + 'keyframes ' + node.name, node.position) + this.emit('{') + this.mapVisit(node.keyframes) + this.emit('}');
};
/**
 * Visit keyframe node.
 */


compress_Compiler.prototype.keyframe = function (node) {
  var decls = node.declarations;
  return this.emit(node.values.join(','), node.position) + this.emit('{') + this.mapVisit(decls) + this.emit('}');
};
/**
 * Visit page node.
 */


compress_Compiler.prototype.page = function (node) {
  var sel = node.selectors.length ? node.selectors.join(', ') : '';
  return this.emit('@page ' + sel, node.position) + this.emit('{') + this.mapVisit(node.declarations) + this.emit('}');
};
/**
 * Visit font-face node.
 */


compress_Compiler.prototype['font-face'] = function (node) {
  return this.emit('@font-face', node.position) + this.emit('{') + this.mapVisit(node.declarations) + this.emit('}');
};
/**
 * Visit host node.
 */


compress_Compiler.prototype.host = function (node) {
  return this.emit('@host', node.position) + this.emit('{') + this.mapVisit(node.rules) + this.emit('}');
};
/**
 * Visit custom-media node.
 */


compress_Compiler.prototype['custom-media'] = function (node) {
  return this.emit('@custom-media ' + node.name + ' ' + node.media + ';', node.position);
};
/**
 * Visit rule node.
 */


compress_Compiler.prototype.rule = function (node) {
  var decls = node.declarations;

  if (!decls.length) {
    return '';
  }

  return this.emit(node.selectors.join(','), node.position) + this.emit('{') + this.mapVisit(decls) + this.emit('}');
};
/**
 * Visit declaration node.
 */


compress_Compiler.prototype.declaration = function (node) {
  return this.emit(node.property + ':' + node.value, node.position) + this.emit(';');
};

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/ast/stringify/identity.js
/* eslint-disable @wordpress/no-unused-vars-before-return */
// Adapted from https://github.com/reworkcss/css
// because we needed to remove source map support.

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Expose compiler.
 */

/* harmony default export */ var identity = (identity_Compiler);
/**
 * Initialize a new `Compiler`.
 */

function identity_Compiler(options) {
  options = options || {};
  stringify_compiler.call(this, options);
  this.indentation = options.indent;
}
/**
 * Inherit from `Base.prototype`.
 */


inherits_browser_default()(identity_Compiler, stringify_compiler);
/**
 * Compile `node`.
 */

identity_Compiler.prototype.compile = function (node) {
  return this.stylesheet(node);
};
/**
 * Visit stylesheet node.
 */


identity_Compiler.prototype.stylesheet = function (node) {
  return this.mapVisit(node.stylesheet.rules, '\n\n');
};
/**
 * Visit comment node.
 */


identity_Compiler.prototype.comment = function (node) {
  return this.emit(this.indent() + '/*' + node.comment + '*/', node.position);
};
/**
 * Visit import node.
 */


identity_Compiler.prototype.import = function (node) {
  return this.emit('@import ' + node.import + ';', node.position);
};
/**
 * Visit media node.
 */


identity_Compiler.prototype.media = function (node) {
  return this.emit('@media ' + node.media, node.position) + this.emit(' {\n' + this.indent(1)) + this.mapVisit(node.rules, '\n\n') + this.emit(this.indent(-1) + '\n}');
};
/**
 * Visit document node.
 */


identity_Compiler.prototype.document = function (node) {
  var doc = '@' + (node.vendor || '') + 'document ' + node.document;
  return this.emit(doc, node.position) + this.emit(' ' + ' {\n' + this.indent(1)) + this.mapVisit(node.rules, '\n\n') + this.emit(this.indent(-1) + '\n}');
};
/**
 * Visit charset node.
 */


identity_Compiler.prototype.charset = function (node) {
  return this.emit('@charset ' + node.charset + ';', node.position);
};
/**
 * Visit namespace node.
 */


identity_Compiler.prototype.namespace = function (node) {
  return this.emit('@namespace ' + node.namespace + ';', node.position);
};
/**
 * Visit supports node.
 */


identity_Compiler.prototype.supports = function (node) {
  return this.emit('@supports ' + node.supports, node.position) + this.emit(' {\n' + this.indent(1)) + this.mapVisit(node.rules, '\n\n') + this.emit(this.indent(-1) + '\n}');
};
/**
 * Visit keyframes node.
 */


identity_Compiler.prototype.keyframes = function (node) {
  return this.emit('@' + (node.vendor || '') + 'keyframes ' + node.name, node.position) + this.emit(' {\n' + this.indent(1)) + this.mapVisit(node.keyframes, '\n') + this.emit(this.indent(-1) + '}');
};
/**
 * Visit keyframe node.
 */


identity_Compiler.prototype.keyframe = function (node) {
  var decls = node.declarations;
  return this.emit(this.indent()) + this.emit(node.values.join(', '), node.position) + this.emit(' {\n' + this.indent(1)) + this.mapVisit(decls, '\n') + this.emit(this.indent(-1) + '\n' + this.indent() + '}\n');
};
/**
 * Visit page node.
 */


identity_Compiler.prototype.page = function (node) {
  var sel = node.selectors.length ? node.selectors.join(', ') + ' ' : '';
  return this.emit('@page ' + sel, node.position) + this.emit('{\n') + this.emit(this.indent(1)) + this.mapVisit(node.declarations, '\n') + this.emit(this.indent(-1)) + this.emit('\n}');
};
/**
 * Visit font-face node.
 */


identity_Compiler.prototype['font-face'] = function (node) {
  return this.emit('@font-face ', node.position) + this.emit('{\n') + this.emit(this.indent(1)) + this.mapVisit(node.declarations, '\n') + this.emit(this.indent(-1)) + this.emit('\n}');
};
/**
 * Visit host node.
 */


identity_Compiler.prototype.host = function (node) {
  return this.emit('@host', node.position) + this.emit(' {\n' + this.indent(1)) + this.mapVisit(node.rules, '\n\n') + this.emit(this.indent(-1) + '\n}');
};
/**
 * Visit custom-media node.
 */


identity_Compiler.prototype['custom-media'] = function (node) {
  return this.emit('@custom-media ' + node.name + ' ' + node.media + ';', node.position);
};
/**
 * Visit rule node.
 */


identity_Compiler.prototype.rule = function (node) {
  var indent = this.indent();
  var decls = node.declarations;

  if (!decls.length) {
    return '';
  }

  return this.emit(node.selectors.map(function (s) {
    return indent + s;
  }).join(',\n'), node.position) + this.emit(' {\n') + this.emit(this.indent(1)) + this.mapVisit(decls, '\n') + this.emit(this.indent(-1)) + this.emit('\n' + this.indent() + '}');
};
/**
 * Visit declaration node.
 */


identity_Compiler.prototype.declaration = function (node) {
  return this.emit(this.indent()) + this.emit(node.property + ': ' + node.value, node.position) + this.emit(';');
};
/**
 * Increase, decrease or return current indentation.
 */


identity_Compiler.prototype.indent = function (level) {
  this.level = this.level || 1;

  if (null !== level) {
    this.level += level;
    return '';
  }

  return Array(this.level).join(this.indentation || '  ');
};

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/ast/stringify/index.js
// Adapted from https://github.com/reworkcss/css
// because we needed to remove source map support.

/**
 * Internal dependencies
 */


/**
 * Stringfy the given AST `node`.
 *
 * Options:
 *
 *  - `compress` space-optimized output
 *  - `sourcemap` return an object with `.code` and `.map`
 *
 * @param {Object} node
 * @param {Object} [options]
 * @return {String}
 * @api public
 */

/* harmony default export */ var stringify = (function (node, options) {
  options = options || {};
  var compiler = options.compress ? new compress(options) : new identity(options);
  var code = compiler.compile(node);
  return code;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/ast/index.js
// Adapted from https://github.com/reworkcss/css
// because we needed to remove source map support.



// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/traverse.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



function traverseCSS(css, callback) {
  try {
    var parsed = parse(css);
    var updated = traverse_default.a.map(parsed, function (node) {
      if (!node) {
        return node;
      }

      var updatedNode = callback(node);
      return this.update(updatedNode);
    });
    return stringify(updated);
  } catch (err) {
    // eslint-disable-next-line no-console
    console.warn('Error while traversing the CSS: ' + err);
    return null;
  }
}

/* harmony default export */ var editor_styles_traverse = (traverseCSS);

// EXTERNAL MODULE: ./node_modules/url/url.js
var url = __webpack_require__("CxY0");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/transforms/url-rewrite.js


/**
 * External dependencies
 */

/**
 * Return `true` if the given path is http/https.
 *
 * @param  {string}  filePath path
 *
 * @return {boolean} is remote path.
 */

function isRemotePath(filePath) {
  return /^(?:https?:)?\/\//.test(filePath);
}
/**
 * Return `true` if the given filePath is an absolute url.
 *
 * @param  {string}  filePath path
 *
 * @return {boolean} is absolute path.
 */


function isAbsolutePath(filePath) {
  return /^\/(?!\/)/.test(filePath);
}
/**
 * Whether or not the url should be inluded.
 *
 * @param  {Object} meta url meta info
 *
 * @return {boolean} is valid.
 */


function isValidURL(meta) {
  // ignore hashes or data uris
  if (meta.value.indexOf('data:') === 0 || meta.value.indexOf('#') === 0) {
    return false;
  }

  if (isAbsolutePath(meta.value)) {
    return false;
  } // do not handle the http/https urls if `includeRemote` is false


  if (isRemotePath(meta.value)) {
    return false;
  }

  return true;
}
/**
 * Get the absolute path of the url, relative to the basePath
 *
 * @param  {string} str          the url
 * @param  {string} baseURL      base URL
 * @param  {string} absolutePath the absolute path
 *
 * @return {string}              the full path to the file
 */


function getResourcePath(str, baseURL) {
  var pathname = Object(url["parse"])(str).pathname;
  var filePath = Object(url["resolve"])(baseURL, pathname);
  return filePath;
}
/**
 * Process the single `url()` pattern
 *
 * @param  {string} baseURL  the base URL for relative URLs
 * @return {Promise}         the Promise
 */


function processURL(baseURL) {
  return function (meta) {
    var URL = getResourcePath(meta.value, baseURL);
    return Object(objectSpread["a" /* default */])({}, meta, {
      newUrl: 'url(' + meta.before + meta.quote + URL + meta.quote + meta.after + ')'
    });
  };
}
/**
 * Get all `url()`s, and return the meta info
 *
 * @param  {string} value decl.value
 *
 * @return {Array}        the urls
 */


function getURLs(value) {
  var reg = /url\((\s*)(['"]?)(.+?)\2(\s*)\)/g;
  var match;
  var URLs = [];

  while ((match = reg.exec(value)) !== null) {
    var meta = {
      source: match[0],
      before: match[1],
      quote: match[2],
      value: match[3],
      after: match[4]
    };

    if (isValidURL(meta)) {
      URLs.push(meta);
    }
  }

  return URLs;
}
/**
 * Replace the raw value's `url()` segment to the new value
 *
 * @param  {string} raw  the raw value
 * @param  {Array}  URLs the URLs to replace
 *
 * @return {string}     the new value
 */


function replaceURLs(raw, URLs) {
  URLs.forEach(function (item) {
    raw = raw.replace(item.source, item.newUrl);
  });
  return raw;
}

var url_rewrite_rewrite = function rewrite(rootURL) {
  return function (node) {
    if (node.type === 'declaration') {
      var updatedURLs = getURLs(node.value).map(processURL(rootURL));
      return Object(objectSpread["a" /* default */])({}, node, {
        value: replaceURLs(node.value, updatedURLs)
      });
    }

    return node;
  };
};

/* harmony default export */ var url_rewrite = (url_rewrite_rewrite);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/transforms/wrap.js


/**
 * External dependencies
 */

/**
 * @const string IS_ROOT_TAG Regex to check if the selector is a root tag selector.
 */

var IS_ROOT_TAG = /^(body|html|:root).*$/;

var wrap_wrap = function wrap(namespace) {
  var ignore = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  return function (node) {
    var updateSelector = function updateSelector(selector) {
      if (Object(external_lodash_["includes"])(ignore, selector.trim())) {
        return selector;
      } // Anything other than a root tag is always prefixed.


      {
        if (!selector.match(IS_ROOT_TAG)) {
          return namespace + ' ' + selector;
        }
      } // HTML and Body elements cannot be contained within our container so lets extract their styles.

      return selector.replace(/^(body|html|:root)/, namespace);
    };

    if (node.type === 'rule') {
      return Object(objectSpread["a" /* default */])({}, node, {
        selectors: node.selectors.map(updateSelector)
      });
    }

    return node;
  };
};

/* harmony default export */ var transforms_wrap = (wrap_wrap);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/editor-styles/index.js
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
 * Convert css rules.
 *
 * @param {Array} styles CSS rules.
 * @param {string} wrapperClassName Wrapper Class Name.
 * @return {Array} converted rules.
 */

var editor_styles_transformStyles = function transformStyles(styles) {
  var wrapperClassName = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  return Object(external_lodash_["map"])(styles, function (_ref) {
    var css = _ref.css,
        baseURL = _ref.baseURL;
    var transforms = [];

    if (wrapperClassName) {
      transforms.push(transforms_wrap(wrapperClassName));
    }

    if (baseURL) {
      transforms.push(url_rewrite(baseURL));
    }

    if (transforms.length) {
      return editor_styles_traverse(css, Object(external_this_wp_compose_["compose"])(transforms));
    }

    return css;
  });
};

/* harmony default export */ var editor_styles = (editor_styles_transformStyles);

// EXTERNAL MODULE: external {"this":["wp","blob"]}
var external_this_wp_blob_ = __webpack_require__("xTGt");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/media-upload/media-upload.js







/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Browsers may use unexpected mime types, and they differ from browser to browser.
 * This function computes a flexible array of mime types from the mime type structured provided by the server.
 * Converts { jpg|jpeg|jpe: "image/jpeg" } into [ "image/jpeg", "image/jpg", "image/jpeg", "image/jpe" ]
 * The computation of this array instead of directly using the object,
 * solves the problem in chrome where mp3 files have audio/mp3 as mime type instead of audio/mpeg.
 * https://bugs.chromium.org/p/chromium/issues/detail?id=227004
 *
 * @param {?Object} wpMimeTypesObject Mime type object received from the server.
 *                                    Extensions are keys separated by '|' and values are mime types associated with an extension.
 *
 * @return {?Array} An array of mime types or the parameter passed if it was "falsy".
 */

function getMimeTypesArray(wpMimeTypesObject) {
  if (!wpMimeTypesObject) {
    return wpMimeTypesObject;
  }

  return Object(external_lodash_["flatMap"])(wpMimeTypesObject, function (mime, extensionsString) {
    var _mime$split = mime.split('/'),
        _mime$split2 = Object(slicedToArray["a" /* default */])(_mime$split, 1),
        type = _mime$split2[0];

    var extensions = extensionsString.split('|');
    return [mime].concat(Object(toConsumableArray["a" /* default */])(Object(external_lodash_["map"])(extensions, function (extension) {
      return "".concat(type, "/").concat(extension);
    })));
  });
}
/**
 *	Media Upload is used by audio, image, gallery, video, and file blocks to
 *	handle uploading a media file when a file upload button is activated.
 *
 *	TODO: future enhancement to add an upload indicator.
 *
 * @param   {Object}   $0                    Parameters object passed to the function.
 * @param   {?Array}   $0.allowedTypes       Array with the types of media that can be uploaded, if unset all types are allowed.
 * @param   {?Object}  $0.additionalData     Additional data to include in the request.
 * @param   {Array}    $0.filesList          List of files.
 * @param   {?number}  $0.maxUploadFileSize  Maximum upload size in bytes allowed for the site.
 * @param   {Function} $0.onError            Function called when an error happens.
 * @param   {Function} $0.onFileChange       Function called each time a file or a temporary representation of the file is available.
 * @param   {?Object}  $0.wpAllowedMimeTypes List of allowed mime types and file extensions.
 */

function mediaUpload(_x) {
  return _mediaUpload.apply(this, arguments);
}
/**
 * @param {File}    file           Media File to Save.
 * @param {?Object} additionalData Additional data to include in the request.
 *
 * @return {Promise} Media Object Promise.
 */

function _mediaUpload() {
  _mediaUpload = Object(asyncToGenerator["a" /* default */])(
  /*#__PURE__*/
  regenerator_default.a.mark(function _callee(_ref) {
    var allowedTypes, _ref$additionalData, additionalData, filesList, maxUploadFileSize, _ref$onError, onError, onFileChange, _ref$wpAllowedMimeTyp, wpAllowedMimeTypes, files, filesSet, setAndUpdateFiles, isAllowedType, allowedMimeTypesForUser, isAllowedMimeTypeForUser, triggerError, validFiles, _iteratorNormalCompletion, _didIteratorError, _iteratorError, _iterator, _step, _mediaFile, idx, mediaFile, savedMedia, mediaObject, message;

    return regenerator_default.a.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            allowedTypes = _ref.allowedTypes, _ref$additionalData = _ref.additionalData, additionalData = _ref$additionalData === void 0 ? {} : _ref$additionalData, filesList = _ref.filesList, maxUploadFileSize = _ref.maxUploadFileSize, _ref$onError = _ref.onError, onError = _ref$onError === void 0 ? external_lodash_["noop"] : _ref$onError, onFileChange = _ref.onFileChange, _ref$wpAllowedMimeTyp = _ref.wpAllowedMimeTypes, wpAllowedMimeTypes = _ref$wpAllowedMimeTyp === void 0 ? null : _ref$wpAllowedMimeTyp;
            // Cast filesList to array
            files = Object(toConsumableArray["a" /* default */])(filesList);
            filesSet = [];

            setAndUpdateFiles = function setAndUpdateFiles(idx, value) {
              Object(external_this_wp_blob_["revokeBlobURL"])(Object(external_lodash_["get"])(filesSet, [idx, 'url']));
              filesSet[idx] = value;
              onFileChange(Object(external_lodash_["compact"])(filesSet));
            }; // Allowed type specified by consumer


            isAllowedType = function isAllowedType(fileType) {
              if (!allowedTypes) {
                return true;
              }

              return Object(external_lodash_["some"])(allowedTypes, function (allowedType) {
                // If a complete mimetype is specified verify if it matches exactly the mime type of the file.
                if (Object(external_lodash_["includes"])(allowedType, '/')) {
                  return allowedType === fileType;
                } // Otherwise a general mime type is used and we should verify if the file mimetype starts with it.


                return Object(external_lodash_["startsWith"])(fileType, "".concat(allowedType, "/"));
              });
            }; // Allowed types for the current WP_User


            allowedMimeTypesForUser = getMimeTypesArray(wpAllowedMimeTypes);

            isAllowedMimeTypeForUser = function isAllowedMimeTypeForUser(fileType) {
              return Object(external_lodash_["includes"])(allowedMimeTypesForUser, fileType);
            }; // Build the error message including the filename


            triggerError = function triggerError(error) {
              error.message = [Object(external_this_wp_element_["createElement"])("strong", {
                key: "filename"
              }, error.file.name), ': ', error.message];
              onError(error);
            };

            validFiles = [];
            _iteratorNormalCompletion = true;
            _didIteratorError = false;
            _iteratorError = undefined;
            _context.prev = 12;
            _iterator = files[Symbol.iterator]();

          case 14:
            if (_iteratorNormalCompletion = (_step = _iterator.next()).done) {
              _context.next = 34;
              break;
            }

            _mediaFile = _step.value;

            if (!(allowedMimeTypesForUser && !isAllowedMimeTypeForUser(_mediaFile.type))) {
              _context.next = 19;
              break;
            }

            triggerError({
              code: 'MIME_TYPE_NOT_ALLOWED_FOR_USER',
              message: Object(external_this_wp_i18n_["__"])('Sorry, this file type is not permitted for security reasons.'),
              file: _mediaFile
            });
            return _context.abrupt("continue", 31);

          case 19:
            if (isAllowedType(_mediaFile.type)) {
              _context.next = 22;
              break;
            }

            triggerError({
              code: 'MIME_TYPE_NOT_SUPPORTED',
              message: Object(external_this_wp_i18n_["__"])('Sorry, this file type is not supported here.'),
              file: _mediaFile
            });
            return _context.abrupt("continue", 31);

          case 22:
            if (!(maxUploadFileSize && _mediaFile.size > maxUploadFileSize)) {
              _context.next = 25;
              break;
            }

            triggerError({
              code: 'SIZE_ABOVE_LIMIT',
              message: Object(external_this_wp_i18n_["__"])('This file exceeds the maximum upload size for this site.'),
              file: _mediaFile
            });
            return _context.abrupt("continue", 31);

          case 25:
            if (!(_mediaFile.size <= 0)) {
              _context.next = 28;
              break;
            }

            triggerError({
              code: 'EMPTY_FILE',
              message: Object(external_this_wp_i18n_["__"])('This file is empty.'),
              file: _mediaFile
            });
            return _context.abrupt("continue", 31);

          case 28:
            validFiles.push(_mediaFile); // Set temporary URL to create placeholder media file, this is replaced
            // with final file from media gallery when upload is `done` below

            filesSet.push({
              url: Object(external_this_wp_blob_["createBlobURL"])(_mediaFile)
            });
            onFileChange(filesSet);

          case 31:
            _iteratorNormalCompletion = true;
            _context.next = 14;
            break;

          case 34:
            _context.next = 40;
            break;

          case 36:
            _context.prev = 36;
            _context.t0 = _context["catch"](12);
            _didIteratorError = true;
            _iteratorError = _context.t0;

          case 40:
            _context.prev = 40;
            _context.prev = 41;

            if (!_iteratorNormalCompletion && _iterator.return != null) {
              _iterator.return();
            }

          case 43:
            _context.prev = 43;

            if (!_didIteratorError) {
              _context.next = 46;
              break;
            }

            throw _iteratorError;

          case 46:
            return _context.finish(43);

          case 47:
            return _context.finish(40);

          case 48:
            idx = 0;

          case 49:
            if (!(idx < validFiles.length)) {
              _context.next = 68;
              break;
            }

            mediaFile = validFiles[idx];
            _context.prev = 51;
            _context.next = 54;
            return createMediaFromFile(mediaFile, additionalData);

          case 54:
            savedMedia = _context.sent;
            mediaObject = Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(savedMedia, ['alt_text', 'source_url']), {
              alt: savedMedia.alt_text,
              caption: Object(external_lodash_["get"])(savedMedia, ['caption', 'raw'], ''),
              title: savedMedia.title.raw,
              url: savedMedia.source_url
            });
            setAndUpdateFiles(idx, mediaObject);
            _context.next = 65;
            break;

          case 59:
            _context.prev = 59;
            _context.t1 = _context["catch"](51);
            // Reset to empty on failure.
            setAndUpdateFiles(idx, null);
            message = void 0;

            if (Object(external_lodash_["has"])(_context.t1, ['message'])) {
              message = Object(external_lodash_["get"])(_context.t1, ['message']);
            } else {
              message = Object(external_this_wp_i18n_["sprintf"])( // translators: %s: file name
              Object(external_this_wp_i18n_["__"])('Error while uploading file %s to the media library.'), mediaFile.name);
            }

            onError({
              code: 'GENERAL',
              message: message,
              file: mediaFile
            });

          case 65:
            ++idx;
            _context.next = 49;
            break;

          case 68:
          case "end":
            return _context.stop();
        }
      }
    }, _callee, this, [[12, 36, 40, 48], [41,, 43, 47], [51, 59]]);
  }));
  return _mediaUpload.apply(this, arguments);
}

function createMediaFromFile(file, additionalData) {
  // Create upload payload
  var data = new window.FormData();
  data.append('file', file, file.name || file.type.replace('/', '.'));
  Object(external_lodash_["forEach"])(additionalData, function (value, key) {
    return data.append(key, value);
  });
  return external_this_wp_apiFetch_default()({
    path: '/wp/v2/media',
    body: data,
    method: 'POST'
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/media-upload/index.js


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
 * Upload a media file when the file upload button is activated.
 * Wrapper around mediaUpload() that injects the current post ID.
 *
 * @param   {Object}   $0                   Parameters object passed to the function.
 * @param   {?Object}  $0.additionalData    Additional data to include in the request.
 * @param   {string}   $0.allowedTypes      Array with the types of media that can be uploaded, if unset all types are allowed.
 * @param   {Array}    $0.filesList         List of files.
 * @param   {?number}  $0.maxUploadFileSize Maximum upload size in bytes allowed for the site.
 * @param   {Function} $0.onError           Function called when an error happens.
 * @param   {Function} $0.onFileChange      Function called each time a file or a temporary representation of the file is available.
 */

/* harmony default export */ var media_upload = (function (_ref) {
  var _ref$additionalData = _ref.additionalData,
      additionalData = _ref$additionalData === void 0 ? {} : _ref$additionalData,
      allowedTypes = _ref.allowedTypes,
      filesList = _ref.filesList,
      maxUploadFileSize = _ref.maxUploadFileSize,
      _ref$onError = _ref.onError,
      _onError = _ref$onError === void 0 ? external_lodash_["noop"] : _ref$onError,
      onFileChange = _ref.onFileChange;

  var _select = Object(external_this_wp_data_["select"])('core/editor'),
      getCurrentPostId = _select.getCurrentPostId,
      getEditorSettings = _select.getEditorSettings;

  var wpAllowedMimeTypes = getEditorSettings().allowedMimeTypes;
  maxUploadFileSize = maxUploadFileSize || getEditorSettings().maxUploadFileSize;
  mediaUpload({
    allowedTypes: allowedTypes,
    filesList: filesList,
    onFileChange: onFileChange,
    additionalData: Object(objectSpread["a" /* default */])({
      post: getCurrentPostId()
    }, additionalData),
    maxUploadFileSize: maxUploadFileSize,
    onError: function onError(_ref2) {
      var message = _ref2.message;
      return _onError(message);
    },
    wpAllowedMimeTypes: wpAllowedMimeTypes
  });
});

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/index.js
/**
 * Internal dependencies
 */




// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/index.js








/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




var provider_EditorProvider =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(EditorProvider, _Component);

  function EditorProvider(props) {
    var _this;

    Object(classCallCheck["a" /* default */])(this, EditorProvider);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(EditorProvider).apply(this, arguments));
    _this.getBlockEditorSettings = memize_default()(_this.getBlockEditorSettings, {
      maxSize: 1
    }); // Assume that we don't need to initialize in the case of an error recovery.

    if (props.recovery) {
      return Object(possibleConstructorReturn["a" /* default */])(_this);
    }

    props.updatePostLock(props.settings.postLock);
    props.setupEditor(props.post, props.initialEdits, props.settings.template);

    if (props.settings.autosave) {
      props.createWarningNotice(Object(external_this_wp_i18n_["__"])('There is an autosave of this post that is more recent than the version below.'), {
        id: 'autosave-exists',
        actions: [{
          label: Object(external_this_wp_i18n_["__"])('View the autosave'),
          url: props.settings.autosave.editLink
        }]
      });
    }

    return _this;
  }

  Object(createClass["a" /* default */])(EditorProvider, [{
    key: "getBlockEditorSettings",
    value: function getBlockEditorSettings(settings, meta, onMetaChange, reusableBlocks) {
      return Object(objectSpread["a" /* default */])({}, Object(external_lodash_["pick"])(settings, ['alignWide', 'allowedBlockTypes', 'availableLegacyWidgets', 'bodyPlaceholder', 'colors', 'disableCustomColors', 'disableCustomFontSizes', 'focusMode', 'fontSizes', 'hasFixedToolbar', 'hasPermissionsToManageWidgets', 'imageSizes', 'isRTL', 'maxWidth', 'styles', 'template', 'templateLock', 'titlePlaceholder']), {
        __experimentalMetaSource: {
          value: meta,
          onChange: onMetaChange
        },
        __experimentalReusableBlocks: reusableBlocks,
        __experimentalMediaUpload: media_upload
      });
    }
  }, {
    key: "componentDidMount",
    value: function componentDidMount() {
      this.props.updateEditorSettings(this.props.settings);

      if (!this.props.settings.styles) {
        return;
      }

      var updatedStyles = editor_styles(this.props.settings.styles, '.editor-styles-wrapper');
      Object(external_lodash_["map"])(updatedStyles, function (updatedCSS) {
        if (updatedCSS) {
          var node = document.createElement('style');
          node.innerHTML = updatedCSS;
          document.body.appendChild(node);
        }
      });
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.settings !== prevProps.settings) {
        this.props.updateEditorSettings(this.props.settings);
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          children = _this$props.children,
          blocks = _this$props.blocks,
          resetEditorBlocks = _this$props.resetEditorBlocks,
          isReady = _this$props.isReady,
          settings = _this$props.settings,
          meta = _this$props.meta,
          onMetaChange = _this$props.onMetaChange,
          reusableBlocks = _this$props.reusableBlocks,
          resetEditorBlocksWithoutUndoLevel = _this$props.resetEditorBlocksWithoutUndoLevel;

      if (!isReady) {
        return null;
      }

      var editorSettings = this.getBlockEditorSettings(settings, meta, onMetaChange, reusableBlocks);
      return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockEditorProvider"], {
        value: blocks,
        onInput: resetEditorBlocksWithoutUndoLevel,
        onChange: resetEditorBlocks,
        settings: editorSettings
      }, children);
    }
  }]);

  return EditorProvider;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var provider = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isEditorReady = _select.__unstableIsEditorReady,
      getEditorBlocks = _select.getEditorBlocks,
      getEditedPostAttribute = _select.getEditedPostAttribute,
      __experimentalGetReusableBlocks = _select.__experimentalGetReusableBlocks;

  return {
    isReady: isEditorReady(),
    blocks: getEditorBlocks(),
    meta: getEditedPostAttribute('meta'),
    reusableBlocks: __experimentalGetReusableBlocks()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      setupEditor = _dispatch.setupEditor,
      updatePostLock = _dispatch.updatePostLock,
      resetEditorBlocks = _dispatch.resetEditorBlocks,
      editPost = _dispatch.editPost,
      updateEditorSettings = _dispatch.updateEditorSettings;

  var _dispatch2 = dispatch('core/notices'),
      createWarningNotice = _dispatch2.createWarningNotice;

  return {
    setupEditor: setupEditor,
    updatePostLock: updatePostLock,
    createWarningNotice: createWarningNotice,
    resetEditorBlocks: resetEditorBlocks,
    updateEditorSettings: updateEditorSettings,
    resetEditorBlocksWithoutUndoLevel: function resetEditorBlocksWithoutUndoLevel(blocks) {
      resetEditorBlocks(blocks, {
        __unstableShouldCreateUndoLevel: false
      });
    },
    onMetaChange: function onMetaChange(meta) {
      editPost({
        meta: meta
      });
    }
  };
})])(provider_EditorProvider));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/deprecated.js
// Block Creation Components

/**
 * WordPress dependencies
 */



// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/index.js
// Block Creation Components

 // Post Related Components




















































 // State Related Components




// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/hooks/default-autocompleters.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var defaultAutocompleters = [autocompleters_user];
var default_autocompleters_fetchReusableBlocks = Object(external_lodash_["once"])(function () {
  return Object(external_this_wp_data_["dispatch"])('core/editor').__experimentalFetchReusableBlocks();
});

function setDefaultCompleters(completers, blockName) {
  if (!completers) {
    // Provide copies so filters may directly modify them.
    completers = defaultAutocompleters.map(external_lodash_["clone"]); // Add blocks autocompleter for Paragraph block

    if (blockName === Object(external_this_wp_blocks_["getDefaultBlockName"])()) {
      completers.push(Object(external_lodash_["clone"])(autocompleters_block));
      /*
       * NOTE: This is a hack to help ensure reusable blocks are loaded
       * so they may be included in the block completer. It can be removed
       * once we have a way for completers to Promise options while
       * store-based data dependencies are being resolved.
       */

      default_autocompleters_fetchReusableBlocks();
    }
  }

  return completers;
}

Object(external_this_wp_hooks_["addFilter"])('editor.Autocomplete.completers', 'editor/autocompleters/set-default-completers', setDefaultCompleters);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/hooks/index.js
/**
 * Internal dependencies
 */


// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/index.js
/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */








/***/ }),

/***/ "PYwp":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

/***/ }),

/***/ "Rk8H":
/***/ (function(module, exports, __webpack_require__) {

// Load in dependencies
var computedStyle = __webpack_require__("jTPX");

/**
 * Calculate the `line-height` of a given node
 * @param {HTMLElement} node Element to calculate line height of. Must be in the DOM.
 * @returns {Number} `line-height` of the element in pixels
 */
function lineHeight(node) {
  // Grab the line-height via style
  var lnHeightStr = computedStyle(node, 'line-height');
  var lnHeight = parseFloat(lnHeightStr, 10);

  // If the lineHeight did not contain a unit (i.e. it was numeric), convert it to ems (e.g. '2.3' === '2.3em')
  if (lnHeightStr === lnHeight + '') {
    // Save the old lineHeight style and update the em unit to the element
    var _lnHeightStyle = node.style.lineHeight;
    node.style.lineHeight = lnHeightStr + 'em';

    // Calculate the em based height
    lnHeightStr = computedStyle(node, 'line-height');
    lnHeight = parseFloat(lnHeightStr, 10);

    // Revert the lineHeight style
    if (_lnHeightStyle) {
      node.style.lineHeight = _lnHeightStyle;
    } else {
      delete node.style.lineHeight;
    }
  }

  // If the lineHeight is in `pt`, convert it to pixels (4px for 3pt)
  // DEV: `em` units are converted to `pt` in IE6
  // Conversion ratio from https://developer.mozilla.org/en-US/docs/Web/CSS/length
  if (lnHeightStr.indexOf('pt') !== -1) {
    lnHeight *= 4;
    lnHeight /= 3;
  // Otherwise, if the lineHeight is in `mm`, convert it to pixels (96px for 25.4mm)
  } else if (lnHeightStr.indexOf('mm') !== -1) {
    lnHeight *= 96;
    lnHeight /= 25.4;
  // Otherwise, if the lineHeight is in `cm`, convert it to pixels (96px for 2.54cm)
  } else if (lnHeightStr.indexOf('cm') !== -1) {
    lnHeight *= 96;
    lnHeight /= 2.54;
  // Otherwise, if the lineHeight is in `in`, convert it to pixels (96px for 1in)
  } else if (lnHeightStr.indexOf('in') !== -1) {
    lnHeight *= 96;
  // Otherwise, if the lineHeight is in `pc`, convert it to pixels (12pt for 1pc)
  } else if (lnHeightStr.indexOf('pc') !== -1) {
    lnHeight *= 16;
  }

  // Continue our computation
  lnHeight = Math.round(lnHeight);

  // If the line-height is "normal", calculate by font-size
  if (lnHeightStr === 'normal') {
    // Create a temporary node
    var nodeName = node.nodeName;
    var _node = document.createElement(nodeName);
    _node.innerHTML = '&nbsp;';

    // If we have a text area, reset it to only 1 row
    // https://github.com/twolfson/line-height/issues/4
    if (nodeName.toUpperCase() === 'TEXTAREA') {
      _node.setAttribute('rows', '1');
    }

    // Set the font-size of the element
    var fontSizeStr = computedStyle(node, 'font-size');
    _node.style.fontSize = fontSizeStr;

    // Remove default padding/border which can affect offset height
    // https://github.com/twolfson/line-height/issues/4
    // https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/offsetHeight
    _node.style.padding = '0px';
    _node.style.border = '0px';

    // Append it to the body
    var body = document.body;
    body.appendChild(_node);

    // Assume the line height of the element is the height
    var height = _node.offsetHeight;
    lnHeight = height;

    // Remove our child from the DOM
    body.removeChild(_node);
  }

  // Return the calculated height
  return lnHeight;
}

// Export lineHeight
module.exports = lineHeight;


/***/ }),

/***/ "RxS6":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),

/***/ "TSYQ":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2018 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString === Object.prototype.toString) {
					for (var key in arg) {
						if (hasOwn.call(arg, key) && arg[key]) {
							classes.push(key);
						}
					}
				} else {
					classes.push(arg.toString());
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

/***/ "U8pU":
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

/***/ "UuzZ":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["autop"]; }());

/***/ }),

/***/ "WbBG":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ }),

/***/ "WsEz":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

function _objectWithoutProperties(obj, keys) { var target = {}; for (var i in obj) { if (keys.indexOf(i) >= 0) continue; if (!Object.prototype.hasOwnProperty.call(obj, i)) continue; target[i] = obj[i]; } return target; }

var BEGIN = 'BEGIN';
var COMMIT = 'COMMIT';
var REVERT = 'REVERT';
// Array({transactionID: string or null, beforeState: {object}, action: {object}}
var INITIAL_OPTIMIST = [];

module.exports = optimist;
module.exports.BEGIN = BEGIN;
module.exports.COMMIT = COMMIT;
module.exports.REVERT = REVERT;
function optimist(fn) {
  function beginReducer(state, action) {
    var _separateState = separateState(state);

    var optimist = _separateState.optimist;
    var innerState = _separateState.innerState;

    optimist = optimist.concat([{ beforeState: innerState, action: action }]);
    innerState = fn(innerState, action);
    validateState(innerState, action);
    return _extends({ optimist: optimist }, innerState);
  }
  function commitReducer(state, action) {
    var _separateState2 = separateState(state);

    var optimist = _separateState2.optimist;
    var innerState = _separateState2.innerState;

    var newOptimist = [],
        started = false,
        committed = false;
    optimist.forEach(function (entry) {
      if (started) {
        if (entry.beforeState && matchesTransaction(entry.action, action.optimist.id)) {
          committed = true;
          newOptimist.push({ action: entry.action });
        } else {
          newOptimist.push(entry);
        }
      } else if (entry.beforeState && !matchesTransaction(entry.action, action.optimist.id)) {
        started = true;
        newOptimist.push(entry);
      } else if (entry.beforeState && matchesTransaction(entry.action, action.optimist.id)) {
        committed = true;
      }
    });
    if (!committed) {
      console.error('Cannot commit transaction with id "' + action.optimist.id + '" because it does not exist');
    }
    optimist = newOptimist;
    return baseReducer(optimist, innerState, action);
  }
  function revertReducer(state, action) {
    var _separateState3 = separateState(state);

    var optimist = _separateState3.optimist;
    var innerState = _separateState3.innerState;

    var newOptimist = [],
        started = false,
        gotInitialState = false,
        currentState = innerState;
    optimist.forEach(function (entry) {
      if (entry.beforeState && matchesTransaction(entry.action, action.optimist.id)) {
        currentState = entry.beforeState;
        gotInitialState = true;
      }
      if (!matchesTransaction(entry.action, action.optimist.id)) {
        if (entry.beforeState) {
          started = true;
        }
        if (started) {
          if (gotInitialState && entry.beforeState) {
            newOptimist.push({
              beforeState: currentState,
              action: entry.action
            });
          } else {
            newOptimist.push(entry);
          }
        }
        if (gotInitialState) {
          currentState = fn(currentState, entry.action);
          validateState(innerState, action);
        }
      }
    });
    if (!gotInitialState) {
      console.error('Cannot revert transaction with id "' + action.optimist.id + '" because it does not exist');
    }
    optimist = newOptimist;
    return baseReducer(optimist, currentState, action);
  }
  function baseReducer(optimist, innerState, action) {
    if (optimist.length) {
      optimist = optimist.concat([{ action: action }]);
    }
    innerState = fn(innerState, action);
    validateState(innerState, action);
    return _extends({ optimist: optimist }, innerState);
  }
  return function (state, action) {
    if (action.optimist) {
      switch (action.optimist.type) {
        case BEGIN:
          return beginReducer(state, action);
        case COMMIT:
          return commitReducer(state, action);
        case REVERT:
          return revertReducer(state, action);
      }
    }

    var _separateState4 = separateState(state);

    var optimist = _separateState4.optimist;
    var innerState = _separateState4.innerState;

    if (state && !optimist.length) {
      var nextState = fn(innerState, action);
      if (nextState === innerState) {
        return state;
      }
      validateState(nextState, action);
      return _extends({ optimist: optimist }, nextState);
    }
    return baseReducer(optimist, innerState, action);
  };
}

function matchesTransaction(action, id) {
  return action.optimist && action.optimist.id === id;
}

function validateState(newState, action) {
  if (!newState || typeof newState !== 'object' || Array.isArray(newState)) {
    throw new TypeError('Error while handling "' + action.type + '": Optimist requires that state is always a plain object.');
  }
}

function separateState(state) {
  if (!state) {
    return { optimist: INITIAL_OPTIMIST, innerState: state };
  } else {
    var _state$optimist = state.optimist;

    var _optimist = _state$optimist === undefined ? INITIAL_OPTIMIST : _state$optimist;

    var innerState = _objectWithoutProperties(state, ['optimist']);

    return { optimist: _optimist, innerState: innerState };
  }
}

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ "YuTi":
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

/***/ "ZU7w":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["nux"]; }());

/***/ }),

/***/ "axFQ":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockEditor"]; }());

/***/ }),

/***/ "cDcd":
/***/ (function(module, exports) {

(function() { module.exports = this["React"]; }());

/***/ }),

/***/ "d2gM":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * Redux dispatch multiple actions
 */

function multi(_ref) {
  var dispatch = _ref.dispatch;

  return function (next) {
    return function (action) {
      return Array.isArray(action) ? action.filter(Boolean).map(dispatch) : next(action);
    };
  };
}

/**
 * Exports
 */

exports.default = multi;

/***/ }),

/***/ "eGrx":
/***/ (function(module, exports) {

var traverse = module.exports = function (obj) {
    return new Traverse(obj);
};

function Traverse (obj) {
    this.value = obj;
}

Traverse.prototype.get = function (ps) {
    var node = this.value;
    for (var i = 0; i < ps.length; i ++) {
        var key = ps[i];
        if (!node || !hasOwnProperty.call(node, key)) {
            node = undefined;
            break;
        }
        node = node[key];
    }
    return node;
};

Traverse.prototype.has = function (ps) {
    var node = this.value;
    for (var i = 0; i < ps.length; i ++) {
        var key = ps[i];
        if (!node || !hasOwnProperty.call(node, key)) {
            return false;
        }
        node = node[key];
    }
    return true;
};

Traverse.prototype.set = function (ps, value) {
    var node = this.value;
    for (var i = 0; i < ps.length - 1; i ++) {
        var key = ps[i];
        if (!hasOwnProperty.call(node, key)) node[key] = {};
        node = node[key];
    }
    node[ps[i]] = value;
    return value;
};

Traverse.prototype.map = function (cb) {
    return walk(this.value, cb, true);
};

Traverse.prototype.forEach = function (cb) {
    this.value = walk(this.value, cb, false);
    return this.value;
};

Traverse.prototype.reduce = function (cb, init) {
    var skip = arguments.length === 1;
    var acc = skip ? this.value : init;
    this.forEach(function (x) {
        if (!this.isRoot || !skip) {
            acc = cb.call(this, acc, x);
        }
    });
    return acc;
};

Traverse.prototype.paths = function () {
    var acc = [];
    this.forEach(function (x) {
        acc.push(this.path); 
    });
    return acc;
};

Traverse.prototype.nodes = function () {
    var acc = [];
    this.forEach(function (x) {
        acc.push(this.node);
    });
    return acc;
};

Traverse.prototype.clone = function () {
    var parents = [], nodes = [];
    
    return (function clone (src) {
        for (var i = 0; i < parents.length; i++) {
            if (parents[i] === src) {
                return nodes[i];
            }
        }
        
        if (typeof src === 'object' && src !== null) {
            var dst = copy(src);
            
            parents.push(src);
            nodes.push(dst);
            
            forEach(objectKeys(src), function (key) {
                dst[key] = clone(src[key]);
            });
            
            parents.pop();
            nodes.pop();
            return dst;
        }
        else {
            return src;
        }
    })(this.value);
};

function walk (root, cb, immutable) {
    var path = [];
    var parents = [];
    var alive = true;
    
    return (function walker (node_) {
        var node = immutable ? copy(node_) : node_;
        var modifiers = {};
        
        var keepGoing = true;
        
        var state = {
            node : node,
            node_ : node_,
            path : [].concat(path),
            parent : parents[parents.length - 1],
            parents : parents,
            key : path.slice(-1)[0],
            isRoot : path.length === 0,
            level : path.length,
            circular : null,
            update : function (x, stopHere) {
                if (!state.isRoot) {
                    state.parent.node[state.key] = x;
                }
                state.node = x;
                if (stopHere) keepGoing = false;
            },
            'delete' : function (stopHere) {
                delete state.parent.node[state.key];
                if (stopHere) keepGoing = false;
            },
            remove : function (stopHere) {
                if (isArray(state.parent.node)) {
                    state.parent.node.splice(state.key, 1);
                }
                else {
                    delete state.parent.node[state.key];
                }
                if (stopHere) keepGoing = false;
            },
            keys : null,
            before : function (f) { modifiers.before = f },
            after : function (f) { modifiers.after = f },
            pre : function (f) { modifiers.pre = f },
            post : function (f) { modifiers.post = f },
            stop : function () { alive = false },
            block : function () { keepGoing = false }
        };
        
        if (!alive) return state;
        
        function updateState() {
            if (typeof state.node === 'object' && state.node !== null) {
                if (!state.keys || state.node_ !== state.node) {
                    state.keys = objectKeys(state.node)
                }
                
                state.isLeaf = state.keys.length == 0;
                
                for (var i = 0; i < parents.length; i++) {
                    if (parents[i].node_ === node_) {
                        state.circular = parents[i];
                        break;
                    }
                }
            }
            else {
                state.isLeaf = true;
                state.keys = null;
            }
            
            state.notLeaf = !state.isLeaf;
            state.notRoot = !state.isRoot;
        }
        
        updateState();
        
        // use return values to update if defined
        var ret = cb.call(state, state.node);
        if (ret !== undefined && state.update) state.update(ret);
        
        if (modifiers.before) modifiers.before.call(state, state.node);
        
        if (!keepGoing) return state;
        
        if (typeof state.node == 'object'
        && state.node !== null && !state.circular) {
            parents.push(state);
            
            updateState();
            
            forEach(state.keys, function (key, i) {
                path.push(key);
                
                if (modifiers.pre) modifiers.pre.call(state, state.node[key], key);
                
                var child = walker(state.node[key]);
                if (immutable && hasOwnProperty.call(state.node, key)) {
                    state.node[key] = child.node;
                }
                
                child.isLast = i == state.keys.length - 1;
                child.isFirst = i == 0;
                
                if (modifiers.post) modifiers.post.call(state, child);
                
                path.pop();
            });
            parents.pop();
        }
        
        if (modifiers.after) modifiers.after.call(state, state.node);
        
        return state;
    })(root).node;
}

function copy (src) {
    if (typeof src === 'object' && src !== null) {
        var dst;
        
        if (isArray(src)) {
            dst = [];
        }
        else if (isDate(src)) {
            dst = new Date(src.getTime ? src.getTime() : src);
        }
        else if (isRegExp(src)) {
            dst = new RegExp(src);
        }
        else if (isError(src)) {
            dst = { message: src.message };
        }
        else if (isBoolean(src)) {
            dst = new Boolean(src);
        }
        else if (isNumber(src)) {
            dst = new Number(src);
        }
        else if (isString(src)) {
            dst = new String(src);
        }
        else if (Object.create && Object.getPrototypeOf) {
            dst = Object.create(Object.getPrototypeOf(src));
        }
        else if (src.constructor === Object) {
            dst = {};
        }
        else {
            var proto =
                (src.constructor && src.constructor.prototype)
                || src.__proto__
                || {}
            ;
            var T = function () {};
            T.prototype = proto;
            dst = new T;
        }
        
        forEach(objectKeys(src), function (key) {
            dst[key] = src[key];
        });
        return dst;
    }
    else return src;
}

var objectKeys = Object.keys || function keys (obj) {
    var res = [];
    for (var key in obj) res.push(key)
    return res;
};

function toS (obj) { return Object.prototype.toString.call(obj) }
function isDate (obj) { return toS(obj) === '[object Date]' }
function isRegExp (obj) { return toS(obj) === '[object RegExp]' }
function isError (obj) { return toS(obj) === '[object Error]' }
function isBoolean (obj) { return toS(obj) === '[object Boolean]' }
function isNumber (obj) { return toS(obj) === '[object Number]' }
function isString (obj) { return toS(obj) === '[object String]' }

var isArray = Array.isArray || function isArray (xs) {
    return Object.prototype.toString.call(xs) === '[object Array]';
};

var forEach = function (xs, fn) {
    if (xs.forEach) return xs.forEach(fn)
    else for (var i = 0; i < xs.length; i++) {
        fn(xs[i], i, xs);
    }
};

forEach(objectKeys(Traverse.prototype), function (key) {
    traverse[key] = function (obj) {
        var args = [].slice.call(arguments, 1);
        var t = new Traverse(obj);
        return t[key].apply(t, args);
    };
});

var hasOwnProperty = Object.hasOwnProperty || function (obj, key) {
    return key in obj;
};


/***/ }),

/***/ "foSv":
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

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

/***/ }),

/***/ "gQxa":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


function flattenIntoMap( map, effects ) {
	var i;
	if ( Array.isArray( effects ) ) {
		for ( i = 0; i < effects.length; i++ ) {
			flattenIntoMap( map, effects[ i ] );
		}
	} else {
		for ( i in effects ) {
			map[ i ] = ( map[ i ] || [] ).concat( effects[ i ] );
		}
	}
}

function refx( effects ) {
	var map = {},
		middleware;

	flattenIntoMap( map, effects );

	middleware = function( store ) {
		return function( next ) {
			return function( action ) {
				var handlers = map[ action.type ],
					result = next( action ),
					i, handlerAction;

				if ( handlers ) {
					for ( i = 0; i < handlers.length; i++ ) {
						handlerAction = handlers[ i ]( action, store );
						if ( handlerAction ) {
							store.dispatch( handlerAction );
						}
					}
				}

				return result;
			};
		};
	};

	middleware.effects = map;

	return middleware;
}

module.exports = refx;


/***/ }),

/***/ "hx/w":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("WsEz");


/***/ }),

/***/ "jTPX":
/***/ (function(module, exports) {

// This code has been refactored for 140 bytes
// You can see the original here: https://github.com/twolfson/computedStyle/blob/04cd1da2e30fa45844f95f5cb1ac898e9b9ef050/lib/computedStyle.js
var computedStyle = function (el, prop, getComputedStyle) {
  getComputedStyle = window.getComputedStyle;

  // In one fell swoop
  return (
    // If we have getComputedStyle
    getComputedStyle ?
      // Query it
      // TODO: From CSS-Query notes, we might need (node, null) for FF
      getComputedStyle(el) :

    // Otherwise, we are in IE and use currentStyle
      el.currentStyle
  )[
    // Switch to camelCase for CSSOM
    // DEV: Grabbed from jQuery
    // https://github.com/jquery/jquery/blob/1.9-stable/src/css.js#L191-L194
    // https://github.com/jquery/jquery/blob/1.9-stable/src/core.js#L593-L597
    prop.replace(/-(\w)/gi, function (word, letter) {
      return letter.toUpperCase();
    })
  ];
};

module.exports = computedStyle;


/***/ }),

/***/ "jZUy":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["coreData"]; }());

/***/ }),

/***/ "kd2E":
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

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ "ls82":
/***/ (function(module, exports) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

!(function(global) {
  "use strict";

  var Op = Object.prototype;
  var hasOwn = Op.hasOwnProperty;
  var undefined; // More compressible than void 0.
  var $Symbol = typeof Symbol === "function" ? Symbol : {};
  var iteratorSymbol = $Symbol.iterator || "@@iterator";
  var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
  var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  var inModule = typeof module === "object";
  var runtime = global.regeneratorRuntime;
  if (runtime) {
    if (inModule) {
      // If regeneratorRuntime is defined globally and we're in a module,
      // make the exports object identical to regeneratorRuntime.
      module.exports = runtime;
    }
    // Don't bother evaluating the rest of this file if the runtime was
    // already defined globally.
    return;
  }

  // Define the runtime globally (as expected by generated code) as either
  // module.exports (if we're in a module) or a new, empty object.
  runtime = global.regeneratorRuntime = inModule ? module.exports : {};

  function wrap(innerFn, outerFn, self, tryLocsList) {
    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
    var generator = Object.create(protoGenerator.prototype);
    var context = new Context(tryLocsList || []);

    // The ._invoke method unifies the implementations of the .next,
    // .throw, and .return methods.
    generator._invoke = makeInvokeMethod(innerFn, self, context);

    return generator;
  }
  runtime.wrap = wrap;

  // Try/catch helper to minimize deoptimizations. Returns a completion
  // record like context.tryEntries[i].completion. This interface could
  // have been (and was previously) designed to take a closure to be
  // invoked without arguments, but in all the cases we care about we
  // already have an existing method we want to call, so there's no need
  // to create a new function object. We can even get away with assuming
  // the method takes exactly one argument, since that happens to be true
  // in every case, so we don't have to touch the arguments object. The
  // only additional allocation required is the completion record, which
  // has a stable shape and so hopefully should be cheap to allocate.
  function tryCatch(fn, obj, arg) {
    try {
      return { type: "normal", arg: fn.call(obj, arg) };
    } catch (err) {
      return { type: "throw", arg: err };
    }
  }

  var GenStateSuspendedStart = "suspendedStart";
  var GenStateSuspendedYield = "suspendedYield";
  var GenStateExecuting = "executing";
  var GenStateCompleted = "completed";

  // Returning this object from the innerFn has the same effect as
  // breaking out of the dispatch switch statement.
  var ContinueSentinel = {};

  // Dummy constructor functions that we use as the .constructor and
  // .constructor.prototype properties for functions that return Generator
  // objects. For full spec compliance, you may wish to configure your
  // minifier not to mangle the names of these two functions.
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}

  // This is a polyfill for %IteratorPrototype% for environments that
  // don't natively support it.
  var IteratorPrototype = {};
  IteratorPrototype[iteratorSymbol] = function () {
    return this;
  };

  var getProto = Object.getPrototypeOf;
  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  if (NativeIteratorPrototype &&
      NativeIteratorPrototype !== Op &&
      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {
    // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
  }

  var Gp = GeneratorFunctionPrototype.prototype =
    Generator.prototype = Object.create(IteratorPrototype);
  GeneratorFunction.prototype = Gp.constructor = GeneratorFunctionPrototype;
  GeneratorFunctionPrototype.constructor = GeneratorFunction;
  GeneratorFunctionPrototype[toStringTagSymbol] =
    GeneratorFunction.displayName = "GeneratorFunction";

  // Helper for defining the .next, .throw, and .return methods of the
  // Iterator interface in terms of a single ._invoke method.
  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function(method) {
      prototype[method] = function(arg) {
        return this._invoke(method, arg);
      };
    });
  }

  runtime.isGeneratorFunction = function(genFun) {
    var ctor = typeof genFun === "function" && genFun.constructor;
    return ctor
      ? ctor === GeneratorFunction ||
        // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction"
      : false;
  };

  runtime.mark = function(genFun) {
    if (Object.setPrototypeOf) {
      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
    } else {
      genFun.__proto__ = GeneratorFunctionPrototype;
      if (!(toStringTagSymbol in genFun)) {
        genFun[toStringTagSymbol] = "GeneratorFunction";
      }
    }
    genFun.prototype = Object.create(Gp);
    return genFun;
  };

  // Within the body of any async function, `await x` is transformed to
  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
  // `hasOwn.call(value, "__await")` to determine if the yielded value is
  // meant to be awaited.
  runtime.awrap = function(arg) {
    return { __await: arg };
  };

  function AsyncIterator(generator) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);
      if (record.type === "throw") {
        reject(record.arg);
      } else {
        var result = record.arg;
        var value = result.value;
        if (value &&
            typeof value === "object" &&
            hasOwn.call(value, "__await")) {
          return Promise.resolve(value.__await).then(function(value) {
            invoke("next", value, resolve, reject);
          }, function(err) {
            invoke("throw", err, resolve, reject);
          });
        }

        return Promise.resolve(value).then(function(unwrapped) {
          // When a yielded Promise is resolved, its final value becomes
          // the .value of the Promise<{value,done}> result for the
          // current iteration.
          result.value = unwrapped;
          resolve(result);
        }, function(error) {
          // If a rejected Promise was yielded, throw the rejection back
          // into the async generator function so it can be handled there.
          return invoke("throw", error, resolve, reject);
        });
      }
    }

    var previousPromise;

    function enqueue(method, arg) {
      function callInvokeWithMethodAndArg() {
        return new Promise(function(resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise =
        // If enqueue has been called before, then we want to wait until
        // all previous Promises have been resolved before calling invoke,
        // so that results are always delivered in the correct order. If
        // enqueue has not been called before, then it is important to
        // call invoke immediately, without waiting on a callback to fire,
        // so that the async generator function has the opportunity to do
        // any necessary setup in a predictable way. This predictability
        // is why the Promise constructor synchronously invokes its
        // executor callback, and why async functions synchronously
        // execute code before the first await. Since we implement simple
        // async functions in terms of async generators, it is especially
        // important to get this right, even though it requires care.
        previousPromise ? previousPromise.then(
          callInvokeWithMethodAndArg,
          // Avoid propagating failures to Promises returned by later
          // invocations of the iterator.
          callInvokeWithMethodAndArg
        ) : callInvokeWithMethodAndArg();
    }

    // Define the unified helper method that is used to implement .next,
    // .throw, and .return (see defineIteratorMethods).
    this._invoke = enqueue;
  }

  defineIteratorMethods(AsyncIterator.prototype);
  AsyncIterator.prototype[asyncIteratorSymbol] = function () {
    return this;
  };
  runtime.AsyncIterator = AsyncIterator;

  // Note that simple async functions are implemented on top of
  // AsyncIterator objects; they just return a Promise for the value of
  // the final result produced by the iterator.
  runtime.async = function(innerFn, outerFn, self, tryLocsList) {
    var iter = new AsyncIterator(
      wrap(innerFn, outerFn, self, tryLocsList)
    );

    return runtime.isGeneratorFunction(outerFn)
      ? iter // If outerFn is a generator, return the full iterator.
      : iter.next().then(function(result) {
          return result.done ? result.value : iter.next();
        });
  };

  function makeInvokeMethod(innerFn, self, context) {
    var state = GenStateSuspendedStart;

    return function invoke(method, arg) {
      if (state === GenStateExecuting) {
        throw new Error("Generator is already running");
      }

      if (state === GenStateCompleted) {
        if (method === "throw") {
          throw arg;
        }

        // Be forgiving, per 25.3.3.3.3 of the spec:
        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
        return doneResult();
      }

      context.method = method;
      context.arg = arg;

      while (true) {
        var delegate = context.delegate;
        if (delegate) {
          var delegateResult = maybeInvokeDelegate(delegate, context);
          if (delegateResult) {
            if (delegateResult === ContinueSentinel) continue;
            return delegateResult;
          }
        }

        if (context.method === "next") {
          // Setting context._sent for legacy support of Babel's
          // function.sent implementation.
          context.sent = context._sent = context.arg;

        } else if (context.method === "throw") {
          if (state === GenStateSuspendedStart) {
            state = GenStateCompleted;
            throw context.arg;
          }

          context.dispatchException(context.arg);

        } else if (context.method === "return") {
          context.abrupt("return", context.arg);
        }

        state = GenStateExecuting;

        var record = tryCatch(innerFn, self, context);
        if (record.type === "normal") {
          // If an exception is thrown from innerFn, we leave state ===
          // GenStateExecuting and loop back for another invocation.
          state = context.done
            ? GenStateCompleted
            : GenStateSuspendedYield;

          if (record.arg === ContinueSentinel) {
            continue;
          }

          return {
            value: record.arg,
            done: context.done
          };

        } else if (record.type === "throw") {
          state = GenStateCompleted;
          // Dispatch the exception by looping back around to the
          // context.dispatchException(context.arg) call above.
          context.method = "throw";
          context.arg = record.arg;
        }
      }
    };
  }

  // Call delegate.iterator[context.method](context.arg) and handle the
  // result, either by returning a { value, done } result from the
  // delegate iterator, or by modifying context.method and context.arg,
  // setting context.delegate to null, and returning the ContinueSentinel.
  function maybeInvokeDelegate(delegate, context) {
    var method = delegate.iterator[context.method];
    if (method === undefined) {
      // A .throw or .return when the delegate iterator has no .throw
      // method always terminates the yield* loop.
      context.delegate = null;

      if (context.method === "throw") {
        if (delegate.iterator.return) {
          // If the delegate iterator has a return method, give it a
          // chance to clean up.
          context.method = "return";
          context.arg = undefined;
          maybeInvokeDelegate(delegate, context);

          if (context.method === "throw") {
            // If maybeInvokeDelegate(context) changed context.method from
            // "return" to "throw", let that override the TypeError below.
            return ContinueSentinel;
          }
        }

        context.method = "throw";
        context.arg = new TypeError(
          "The iterator does not provide a 'throw' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);

    if (record.type === "throw") {
      context.method = "throw";
      context.arg = record.arg;
      context.delegate = null;
      return ContinueSentinel;
    }

    var info = record.arg;

    if (! info) {
      context.method = "throw";
      context.arg = new TypeError("iterator result is not an object");
      context.delegate = null;
      return ContinueSentinel;
    }

    if (info.done) {
      // Assign the result of the finished delegate to the temporary
      // variable specified by delegate.resultName (see delegateYield).
      context[delegate.resultName] = info.value;

      // Resume execution at the desired location (see delegateYield).
      context.next = delegate.nextLoc;

      // If context.method was "throw" but the delegate handled the
      // exception, let the outer generator proceed normally. If
      // context.method was "next", forget context.arg since it has been
      // "consumed" by the delegate iterator. If context.method was
      // "return", allow the original .return call to continue in the
      // outer generator.
      if (context.method !== "return") {
        context.method = "next";
        context.arg = undefined;
      }

    } else {
      // Re-yield the result returned by the delegate method.
      return info;
    }

    // The delegate iterator is finished, so forget it and continue with
    // the outer generator.
    context.delegate = null;
    return ContinueSentinel;
  }

  // Define Generator.prototype.{next,throw,return} in terms of the
  // unified ._invoke helper method.
  defineIteratorMethods(Gp);

  Gp[toStringTagSymbol] = "Generator";

  // A Generator should always return itself as the iterator object when the
  // @@iterator function is called on it. Some browsers' implementations of the
  // iterator prototype chain incorrectly implement this, causing the Generator
  // object to not be returned from this call. This ensures that doesn't happen.
  // See https://github.com/facebook/regenerator/issues/274 for more details.
  Gp[iteratorSymbol] = function() {
    return this;
  };

  Gp.toString = function() {
    return "[object Generator]";
  };

  function pushTryEntry(locs) {
    var entry = { tryLoc: locs[0] };

    if (1 in locs) {
      entry.catchLoc = locs[1];
    }

    if (2 in locs) {
      entry.finallyLoc = locs[2];
      entry.afterLoc = locs[3];
    }

    this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal";
    delete record.arg;
    entry.completion = record;
  }

  function Context(tryLocsList) {
    // The root entry object (effectively a try statement without a catch
    // or a finally block) gives us a place to store values thrown from
    // locations where there is no enclosing try statement.
    this.tryEntries = [{ tryLoc: "root" }];
    tryLocsList.forEach(pushTryEntry, this);
    this.reset(true);
  }

  runtime.keys = function(object) {
    var keys = [];
    for (var key in object) {
      keys.push(key);
    }
    keys.reverse();

    // Rather than returning an object with a next method, we keep
    // things simple and return the next function itself.
    return function next() {
      while (keys.length) {
        var key = keys.pop();
        if (key in object) {
          next.value = key;
          next.done = false;
          return next;
        }
      }

      // To avoid creating an additional object, we just hang the .value
      // and .done properties off the next function object itself. This
      // also ensures that the minifier will not anonymize the function.
      next.done = true;
      return next;
    };
  };

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) {
        return iteratorMethod.call(iterable);
      }

      if (typeof iterable.next === "function") {
        return iterable;
      }

      if (!isNaN(iterable.length)) {
        var i = -1, next = function next() {
          while (++i < iterable.length) {
            if (hasOwn.call(iterable, i)) {
              next.value = iterable[i];
              next.done = false;
              return next;
            }
          }

          next.value = undefined;
          next.done = true;

          return next;
        };

        return next.next = next;
      }
    }

    // Return an iterator with no values.
    return { next: doneResult };
  }
  runtime.values = values;

  function doneResult() {
    return { value: undefined, done: true };
  }

  Context.prototype = {
    constructor: Context,

    reset: function(skipTempReset) {
      this.prev = 0;
      this.next = 0;
      // Resetting context._sent for legacy support of Babel's
      // function.sent implementation.
      this.sent = this._sent = undefined;
      this.done = false;
      this.delegate = null;

      this.method = "next";
      this.arg = undefined;

      this.tryEntries.forEach(resetTryEntry);

      if (!skipTempReset) {
        for (var name in this) {
          // Not sure about the optimal order of these conditions:
          if (name.charAt(0) === "t" &&
              hasOwn.call(this, name) &&
              !isNaN(+name.slice(1))) {
            this[name] = undefined;
          }
        }
      }
    },

    stop: function() {
      this.done = true;

      var rootEntry = this.tryEntries[0];
      var rootRecord = rootEntry.completion;
      if (rootRecord.type === "throw") {
        throw rootRecord.arg;
      }

      return this.rval;
    },

    dispatchException: function(exception) {
      if (this.done) {
        throw exception;
      }

      var context = this;
      function handle(loc, caught) {
        record.type = "throw";
        record.arg = exception;
        context.next = loc;

        if (caught) {
          // If the dispatched exception was caught by a catch block,
          // then let that catch block handle the exception normally.
          context.method = "next";
          context.arg = undefined;
        }

        return !! caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        var record = entry.completion;

        if (entry.tryLoc === "root") {
          // Exception thrown outside of any try block that could handle
          // it, so set the completion value of the entire function to
          // throw the exception.
          return handle("end");
        }

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc");
          var hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            } else if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            }

          } else if (hasFinally) {
            if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else {
            throw new Error("try statement without catch or finally");
          }
        }
      }
    },

    abrupt: function(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc <= this.prev &&
            hasOwn.call(entry, "finallyLoc") &&
            this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      if (finallyEntry &&
          (type === "break" ||
           type === "continue") &&
          finallyEntry.tryLoc <= arg &&
          arg <= finallyEntry.finallyLoc) {
        // Ignore the finally entry if control is not jumping to a
        // location outside the try/catch block.
        finallyEntry = null;
      }

      var record = finallyEntry ? finallyEntry.completion : {};
      record.type = type;
      record.arg = arg;

      if (finallyEntry) {
        this.method = "next";
        this.next = finallyEntry.finallyLoc;
        return ContinueSentinel;
      }

      return this.complete(record);
    },

    complete: function(record, afterLoc) {
      if (record.type === "throw") {
        throw record.arg;
      }

      if (record.type === "break" ||
          record.type === "continue") {
        this.next = record.arg;
      } else if (record.type === "return") {
        this.rval = this.arg = record.arg;
        this.method = "return";
        this.next = "end";
      } else if (record.type === "normal" && afterLoc) {
        this.next = afterLoc;
      }

      return ContinueSentinel;
    },

    finish: function(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) {
          this.complete(entry.completion, entry.afterLoc);
          resetTryEntry(entry);
          return ContinueSentinel;
        }
      }
    },

    "catch": function(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;
          if (record.type === "throw") {
            var thrown = record.arg;
            resetTryEntry(entry);
          }
          return thrown;
        }
      }

      // The context.catch method must only be called with a location
      // argument that corresponds to a known catch block.
      throw new Error("illegal catch attempt");
    },

    delegateYield: function(iterable, resultName, nextLoc) {
      this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      };

      if (this.method === "next") {
        // Deliberately forget the last sent value so that we don't
        // accidentally pass it on to the delegate.
        this.arg = undefined;
      }

      return ContinueSentinel;
    }
  };
})(
  // In sloppy mode, unbound `this` refers to the global object, fallback to
  // Function constructor if we're in global strict mode. That is sadly a form
  // of indirect eval which violates Content Security Policy.
  (function() {
    return this || (typeof self === "object" && self);
  })() || Function("return this")()
);


/***/ }),

/***/ "md7G":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("U8pU");
/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("JX7q");


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ "o0o1":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("u938");


/***/ }),

/***/ "onLe":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["notices"]; }());

/***/ }),

/***/ "pPDe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";


var LEAF_KEY, hasWeakMap;

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {Object}
 */
LEAF_KEY = {};

/**
 * Whether environment supports WeakMap.
 *
 * @type {boolean}
 */
hasWeakMap = typeof WeakMap !== 'undefined';

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @param {*} value Value to return.
 *
 * @return {Array} Value returned as entry in array.
 */
function arrayOf( value ) {
	return [ value ];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike( value ) {
	return !! value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Object} Cache object.
 */
function createCache() {
	var cache = {
		clear: function() {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {Array}  a         First array.
 * @param {Array}  b         Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual( a, b, fromIndex ) {
	var i;

	if ( a.length !== b.length ) {
		return false;
	}

	for ( i = fromIndex; i < a.length; i++ ) {
		if ( a[ i ] !== b[ i ] ) {
			return false;
		}
	}

	return true;
}

/**
 * Returns a memoized selector function. The getDependants function argument is
 * called before the memoized selector and is expected to return an immutable
 * reference or array of references on which the selector depends for computing
 * its own return value. The memoize cache is preserved only as long as those
 * dependant references remain the same. If getDependants returns a different
 * reference(s), the cache is cleared and the selector value regenerated.
 *
 * @param {Function} selector      Selector function.
 * @param {Function} getDependants Dependant getter returning an immutable
 *                                 reference or array of reference used in
 *                                 cache bust consideration.
 *
 * @return {Function} Memoized selector.
 */
/* harmony default export */ __webpack_exports__["a"] = (function( selector, getDependants ) {
	var rootCache, getCache;

	// Use object source as dependant if getter not provided
	if ( ! getDependants ) {
		getDependants = arrayOf;
	}

	/**
	 * Returns the root cache. If WeakMap is supported, this is assigned to the
	 * root WeakMap cache set, otherwise it is a shared instance of the default
	 * cache object.
	 *
	 * @return {(WeakMap|Object)} Root cache object.
	 */
	function getRootCache() {
		return rootCache;
	}

	/**
	 * Returns the cache for a given dependants array. When possible, a WeakMap
	 * will be used to create a unique cache for each set of dependants. This
	 * is feasible due to the nature of WeakMap in allowing garbage collection
	 * to occur on entries where the key object is no longer referenced. Since
	 * WeakMap requires the key to be an object, this is only possible when the
	 * dependant is object-like. The root cache is created as a hierarchy where
	 * each top-level key is the first entry in a dependants set, the value a
	 * WeakMap where each key is the next dependant, and so on. This continues
	 * so long as the dependants are object-like. If no dependants are object-
	 * like, then the cache is shared across all invocations.
	 *
	 * @see isObjectLike
	 *
	 * @param {Array} dependants Selector dependants.
	 *
	 * @return {Object} Cache object.
	 */
	function getWeakMapCache( dependants ) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i, dependant, map, cache;

		for ( i = 0; i < dependants.length; i++ ) {
			dependant = dependants[ i ];

			// Can only compose WeakMap from object-like key.
			if ( ! isObjectLike( dependant ) ) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if ( caches.has( dependant ) ) {
				// Traverse into nested WeakMap.
				caches = caches.get( dependant );
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set( dependant, map );
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if ( ! caches.has( LEAF_KEY ) ) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set( LEAF_KEY, cache );
		}

		return caches.get( LEAF_KEY );
	}

	// Assign cache handler by availability of WeakMap
	getCache = hasWeakMap ? getWeakMapCache : getRootCache;

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = hasWeakMap ? new WeakMap() : createCache();
	}

	// eslint-disable-next-line jsdoc/check-param-names
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {Object} source    Source object for derivation.
	 * @param {...*}   extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	function callSelector( /* source, ...extraArgs */ ) {
		var len = arguments.length,
			cache, node, i, args, dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		dependants = getDependants.apply( null, args );
		cache = getCache( dependants );

		// If not guaranteed uniqueness by dependants (primitive type or lack
		// of WeakMap support), shallow compare against last dependants and, if
		// references have changed, destroy cache to recalculate result.
		if ( ! cache.isUniqueByDependants ) {
			if ( cache.lastDependants && ! isShallowEqual( dependants, cache.lastDependants, 0 ) ) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while ( node ) {
			// Check whether node arguments match arguments
			if ( ! isShallowEqual( node.args, args, 1 ) ) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== cache.head ) {
				// Adjust siblings to point to each other.
				node.prev.next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				cache.head.prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = {
			// Generate the result from original function
			val: selector.apply( null, args ),
		};

		// Avoid including the source object in the cache.
		args[ 0 ] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( cache.head ) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = getDependants;
	callSelector.clear = clear;
	clear();

	return callSelector;
});


/***/ }),

/***/ "qRz9":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["richText"]; }());

/***/ }),

/***/ "rePB":
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

/***/ "rmEH":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["htmlEntities"]; }());

/***/ }),

/***/ "s4NR":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.decode = exports.parse = __webpack_require__("kd2E");
exports.encode = exports.stringify = __webpack_require__("4JlD");


/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "u938":
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

// This method of obtaining a reference to the global object needs to be
// kept identical to the way it is obtained in runtime.js
var g = (function() {
  return this || (typeof self === "object" && self);
})() || Function("return this")();

// Use `getOwnPropertyNames` because not all browsers support calling
// `hasOwnProperty` on the global `self` object in a worker. See #183.
var hadRuntime = g.regeneratorRuntime &&
  Object.getOwnPropertyNames(g).indexOf("regeneratorRuntime") >= 0;

// Save the old regeneratorRuntime in case it needs to be restored later.
var oldRuntime = hadRuntime && g.regeneratorRuntime;

// Force reevalutation of runtime.js.
g.regeneratorRuntime = undefined;

module.exports = __webpack_require__("ls82");

if (hadRuntime) {
  // Restore the original runtime.
  g.regeneratorRuntime = oldRuntime;
} else {
  // Remove the global property added by runtime.js.
  try {
    delete g.regeneratorRuntime;
  } catch(e) {
    g.regeneratorRuntime = undefined;
  }
}


/***/ }),

/***/ "vpQ4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("rePB");

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

/***/ "vuIU":
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

/***/ "wx14":
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

/***/ "xTGt":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blob"]; }());

/***/ }),

/***/ "yLpj":
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

/***/ "ywyh":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ })

/******/ });