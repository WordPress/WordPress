/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 4306:
/***/ (function(module, exports) {

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

/***/ 5755:
/***/ ((module, exports) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;
	var nativeCodeString = '[native code]';

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
				if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
					classes.push(arg.toString());
					continue;
				}

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

/***/ 6109:
/***/ ((module) => {

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

/***/ 461:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

// Load in dependencies
var computedStyle = __webpack_require__(6109);

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

/***/ 628:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = __webpack_require__(4067);

function emptyFunction() {}
function emptyFunctionWithReset() {}
emptyFunctionWithReset.resetWarningCache = emptyFunction;

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
    bigint: shim,
    bool: shim,
    func: shim,
    number: shim,
    object: shim,
    string: shim,
    symbol: shim,

    any: shim,
    arrayOf: getShim,
    element: shim,
    elementType: shim,
    instanceOf: getShim,
    node: shim,
    objectOf: getShim,
    oneOf: getShim,
    oneOfType: getShim,
    shape: getShim,
    exact: getShim,

    checkPropTypes: emptyFunctionWithReset,
    resetWarningCache: emptyFunction
  };

  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ 5826:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (false) { var throwOnDirectAccess, ReactIs; } else {
  // By explicitly using `prop-types` you are opting into new production behavior.
  // http://fb.me/prop-types-in-prod
  module.exports = __webpack_require__(628)();
}


/***/ }),

/***/ 4067:
/***/ ((module) => {

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

/***/ 4462:
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

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
var React = __webpack_require__(1609);
var PropTypes = __webpack_require__(5826);
var autosize = __webpack_require__(4306);
var _getLineHeight = __webpack_require__(461);
var getLineHeight = _getLineHeight;
var RESIZED = "autosize:resized";
/**
 * A light replacement for built-in textarea component
 * which automaticaly adjusts its height to match the content
 */
var TextareaAutosizeClass = /** @class */ (function (_super) {
    __extends(TextareaAutosizeClass, _super);
    function TextareaAutosizeClass() {
        var _this = _super !== null && _super.apply(this, arguments) || this;
        _this.state = {
            lineHeight: null
        };
        _this.textarea = null;
        _this.onResize = function (e) {
            if (_this.props.onResize) {
                _this.props.onResize(e);
            }
        };
        _this.updateLineHeight = function () {
            if (_this.textarea) {
                _this.setState({
                    lineHeight: getLineHeight(_this.textarea)
                });
            }
        };
        _this.onChange = function (e) {
            var onChange = _this.props.onChange;
            _this.currentValue = e.currentTarget.value;
            onChange && onChange(e);
        };
        return _this;
    }
    TextareaAutosizeClass.prototype.componentDidMount = function () {
        var _this = this;
        var _a = this.props, maxRows = _a.maxRows, async = _a.async;
        if (typeof maxRows === "number") {
            this.updateLineHeight();
        }
        if (typeof maxRows === "number" || async) {
            /*
              the defer is needed to:
                - force "autosize" to activate the scrollbar when this.props.maxRows is passed
                - support StyledComponents (see #71)
            */
            setTimeout(function () { return _this.textarea && autosize(_this.textarea); });
        }
        else {
            this.textarea && autosize(this.textarea);
        }
        if (this.textarea) {
            this.textarea.addEventListener(RESIZED, this.onResize);
        }
    };
    TextareaAutosizeClass.prototype.componentWillUnmount = function () {
        if (this.textarea) {
            this.textarea.removeEventListener(RESIZED, this.onResize);
            autosize.destroy(this.textarea);
        }
    };
    TextareaAutosizeClass.prototype.render = function () {
        var _this = this;
        var _a = this, _b = _a.props, onResize = _b.onResize, maxRows = _b.maxRows, onChange = _b.onChange, style = _b.style, innerRef = _b.innerRef, children = _b.children, props = __rest(_b, ["onResize", "maxRows", "onChange", "style", "innerRef", "children"]), lineHeight = _a.state.lineHeight;
        var maxHeight = maxRows && lineHeight ? lineHeight * maxRows : null;
        return (React.createElement("textarea", __assign({}, props, { onChange: this.onChange, style: maxHeight ? __assign({}, style, { maxHeight: maxHeight }) : style, ref: function (element) {
                _this.textarea = element;
                if (typeof _this.props.innerRef === 'function') {
                    _this.props.innerRef(element);
                }
                else if (_this.props.innerRef) {
                    _this.props.innerRef.current = element;
                }
            } }), children));
    };
    TextareaAutosizeClass.prototype.componentDidUpdate = function () {
        this.textarea && autosize.update(this.textarea);
    };
    TextareaAutosizeClass.defaultProps = {
        rows: 1,
        async: false
    };
    TextareaAutosizeClass.propTypes = {
        rows: PropTypes.number,
        maxRows: PropTypes.number,
        onResize: PropTypes.func,
        innerRef: PropTypes.any,
        async: PropTypes.bool
    };
    return TextareaAutosizeClass;
}(React.Component));
exports.TextareaAutosize = React.forwardRef(function (props, ref) {
    return React.createElement(TextareaAutosizeClass, __assign({}, props, { innerRef: ref }));
});


/***/ }),

/***/ 4132:
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
var __webpack_unused_export__;

__webpack_unused_export__ = true;
var TextareaAutosize_1 = __webpack_require__(4462);
exports.A = TextareaAutosize_1.TextareaAutosize;


/***/ }),

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


/***/ }),

/***/ 1609:
/***/ ((module) => {

"use strict";
module.exports = window["React"];

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
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
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
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  AlignmentToolbar: () => (/* reexport */ AlignmentToolbar),
  Autocomplete: () => (/* reexport */ Autocomplete),
  AutosaveMonitor: () => (/* reexport */ autosave_monitor),
  BlockAlignmentToolbar: () => (/* reexport */ BlockAlignmentToolbar),
  BlockControls: () => (/* reexport */ BlockControls),
  BlockEdit: () => (/* reexport */ BlockEdit),
  BlockEditorKeyboardShortcuts: () => (/* reexport */ BlockEditorKeyboardShortcuts),
  BlockFormatControls: () => (/* reexport */ BlockFormatControls),
  BlockIcon: () => (/* reexport */ BlockIcon),
  BlockInspector: () => (/* reexport */ BlockInspector),
  BlockList: () => (/* reexport */ BlockList),
  BlockMover: () => (/* reexport */ BlockMover),
  BlockNavigationDropdown: () => (/* reexport */ BlockNavigationDropdown),
  BlockSelectionClearer: () => (/* reexport */ BlockSelectionClearer),
  BlockSettingsMenu: () => (/* reexport */ BlockSettingsMenu),
  BlockTitle: () => (/* reexport */ BlockTitle),
  BlockToolbar: () => (/* reexport */ BlockToolbar),
  CharacterCount: () => (/* reexport */ CharacterCount),
  ColorPalette: () => (/* reexport */ ColorPalette),
  ContrastChecker: () => (/* reexport */ ContrastChecker),
  CopyHandler: () => (/* reexport */ CopyHandler),
  DefaultBlockAppender: () => (/* reexport */ DefaultBlockAppender),
  DocumentBar: () => (/* reexport */ DocumentBar),
  DocumentOutline: () => (/* reexport */ document_outline),
  DocumentOutlineCheck: () => (/* reexport */ check),
  EditorHistoryRedo: () => (/* reexport */ editor_history_redo),
  EditorHistoryUndo: () => (/* reexport */ editor_history_undo),
  EditorKeyboardShortcuts: () => (/* reexport */ EditorKeyboardShortcuts),
  EditorKeyboardShortcutsRegister: () => (/* reexport */ register_shortcuts),
  EditorNotices: () => (/* reexport */ editor_notices),
  EditorProvider: () => (/* reexport */ provider),
  EditorSnackbars: () => (/* reexport */ EditorSnackbars),
  EntitiesSavedStates: () => (/* reexport */ EntitiesSavedStates),
  ErrorBoundary: () => (/* reexport */ error_boundary),
  FontSizePicker: () => (/* reexport */ FontSizePicker),
  InnerBlocks: () => (/* reexport */ InnerBlocks),
  Inserter: () => (/* reexport */ Inserter),
  InspectorAdvancedControls: () => (/* reexport */ InspectorAdvancedControls),
  InspectorControls: () => (/* reexport */ InspectorControls),
  LocalAutosaveMonitor: () => (/* reexport */ local_autosave_monitor),
  MediaPlaceholder: () => (/* reexport */ MediaPlaceholder),
  MediaUpload: () => (/* reexport */ MediaUpload),
  MediaUploadCheck: () => (/* reexport */ MediaUploadCheck),
  MultiSelectScrollIntoView: () => (/* reexport */ MultiSelectScrollIntoView),
  NavigableToolbar: () => (/* reexport */ NavigableToolbar),
  ObserveTyping: () => (/* reexport */ ObserveTyping),
  PageAttributesCheck: () => (/* reexport */ page_attributes_check),
  PageAttributesOrder: () => (/* reexport */ PageAttributesOrderWithChecks),
  PageAttributesPanel: () => (/* reexport */ panel),
  PageAttributesParent: () => (/* reexport */ page_attributes_parent),
  PageTemplate: () => (/* reexport */ classic_theme),
  PanelColorSettings: () => (/* reexport */ PanelColorSettings),
  PlainText: () => (/* reexport */ PlainText),
  PostAuthor: () => (/* reexport */ post_author),
  PostAuthorCheck: () => (/* reexport */ PostAuthorCheck),
  PostAuthorPanel: () => (/* reexport */ post_author_panel),
  PostComments: () => (/* reexport */ post_comments),
  PostDiscussionPanel: () => (/* reexport */ post_discussion_panel),
  PostExcerpt: () => (/* reexport */ post_excerpt),
  PostExcerptCheck: () => (/* reexport */ post_excerpt_check),
  PostExcerptPanel: () => (/* reexport */ PostExcerptPanel),
  PostFeaturedImage: () => (/* reexport */ post_featured_image),
  PostFeaturedImageCheck: () => (/* reexport */ post_featured_image_check),
  PostFeaturedImagePanel: () => (/* reexport */ post_featured_image_panel),
  PostFormat: () => (/* reexport */ PostFormat),
  PostFormatCheck: () => (/* reexport */ post_format_check),
  PostLastRevision: () => (/* reexport */ post_last_revision),
  PostLastRevisionCheck: () => (/* reexport */ post_last_revision_check),
  PostLastRevisionPanel: () => (/* reexport */ post_last_revision_panel),
  PostLockedModal: () => (/* reexport */ PostLockedModal),
  PostPendingStatus: () => (/* reexport */ post_pending_status),
  PostPendingStatusCheck: () => (/* reexport */ post_pending_status_check),
  PostPingbacks: () => (/* reexport */ post_pingbacks),
  PostPreviewButton: () => (/* reexport */ PostPreviewButton),
  PostPublishButton: () => (/* reexport */ post_publish_button),
  PostPublishButtonLabel: () => (/* reexport */ label),
  PostPublishPanel: () => (/* reexport */ post_publish_panel),
  PostSavedState: () => (/* reexport */ PostSavedState),
  PostSchedule: () => (/* reexport */ PostSchedule),
  PostScheduleCheck: () => (/* reexport */ PostScheduleCheck),
  PostScheduleLabel: () => (/* reexport */ PostScheduleLabel),
  PostSchedulePanel: () => (/* reexport */ PostSchedulePanel),
  PostSlug: () => (/* reexport */ post_slug),
  PostSlugCheck: () => (/* reexport */ PostSlugCheck),
  PostSticky: () => (/* reexport */ PostSticky),
  PostStickyCheck: () => (/* reexport */ PostStickyCheck),
  PostSwitchToDraftButton: () => (/* reexport */ PostSwitchToDraftButton),
  PostSyncStatus: () => (/* reexport */ PostSyncStatus),
  PostTaxonomies: () => (/* reexport */ post_taxonomies),
  PostTaxonomiesCheck: () => (/* reexport */ PostTaxonomiesCheck),
  PostTaxonomiesFlatTermSelector: () => (/* reexport */ FlatTermSelector),
  PostTaxonomiesHierarchicalTermSelector: () => (/* reexport */ HierarchicalTermSelector),
  PostTaxonomiesPanel: () => (/* reexport */ post_taxonomies_panel),
  PostTemplatePanel: () => (/* reexport */ PostTemplatePanel),
  PostTextEditor: () => (/* reexport */ PostTextEditor),
  PostTitle: () => (/* reexport */ post_title),
  PostTitleRaw: () => (/* reexport */ post_title_raw),
  PostTrash: () => (/* reexport */ PostTrash),
  PostTrashCheck: () => (/* reexport */ post_trash_check),
  PostTypeSupportCheck: () => (/* reexport */ post_type_support_check),
  PostURL: () => (/* reexport */ PostURL),
  PostURLCheck: () => (/* reexport */ PostURLCheck),
  PostURLLabel: () => (/* reexport */ PostURLLabel),
  PostURLPanel: () => (/* reexport */ PostURLPanel),
  PostVisibility: () => (/* reexport */ PostVisibility),
  PostVisibilityCheck: () => (/* reexport */ PostVisibilityCheck),
  PostVisibilityLabel: () => (/* reexport */ PostVisibilityLabel),
  RichText: () => (/* reexport */ RichText),
  RichTextShortcut: () => (/* reexport */ RichTextShortcut),
  RichTextToolbarButton: () => (/* reexport */ RichTextToolbarButton),
  ServerSideRender: () => (/* reexport */ (external_wp_serverSideRender_default())),
  SkipToSelectedBlock: () => (/* reexport */ SkipToSelectedBlock),
  TableOfContents: () => (/* reexport */ table_of_contents),
  TextEditorGlobalKeyboardShortcuts: () => (/* reexport */ TextEditorGlobalKeyboardShortcuts),
  ThemeSupportCheck: () => (/* reexport */ theme_support_check),
  TimeToRead: () => (/* reexport */ TimeToRead),
  URLInput: () => (/* reexport */ URLInput),
  URLInputButton: () => (/* reexport */ URLInputButton),
  URLPopover: () => (/* reexport */ URLPopover),
  UnsavedChangesWarning: () => (/* reexport */ UnsavedChangesWarning),
  VisualEditorGlobalKeyboardShortcuts: () => (/* reexport */ VisualEditorGlobalKeyboardShortcuts),
  Warning: () => (/* reexport */ Warning),
  WordCount: () => (/* reexport */ WordCount),
  WritingFlow: () => (/* reexport */ WritingFlow),
  __unstableRichTextInputEvent: () => (/* reexport */ __unstableRichTextInputEvent),
  cleanForSlug: () => (/* reexport */ cleanForSlug),
  createCustomColorsHOC: () => (/* reexport */ createCustomColorsHOC),
  getColorClassName: () => (/* reexport */ getColorClassName),
  getColorObjectByAttributeValues: () => (/* reexport */ getColorObjectByAttributeValues),
  getColorObjectByColorValue: () => (/* reexport */ getColorObjectByColorValue),
  getFontSize: () => (/* reexport */ getFontSize),
  getFontSizeClass: () => (/* reexport */ getFontSizeClass),
  getTemplatePartIcon: () => (/* reexport */ getTemplatePartIcon),
  mediaUpload: () => (/* reexport */ mediaUpload),
  privateApis: () => (/* reexport */ privateApis),
  store: () => (/* reexport */ store_store),
  storeConfig: () => (/* reexport */ storeConfig),
  transformStyles: () => (/* reexport */ external_wp_blockEditor_namespaceObject.transformStyles),
  useEntitiesSavedStatesIsDirty: () => (/* reexport */ useIsDirty),
  usePostScheduleLabel: () => (/* reexport */ usePostScheduleLabel),
  usePostURLLabel: () => (/* reexport */ usePostURLLabel),
  usePostVisibilityLabel: () => (/* reexport */ usePostVisibilityLabel),
  userAutocompleter: () => (/* reexport */ user),
  withColorContext: () => (/* reexport */ withColorContext),
  withColors: () => (/* reexport */ withColors),
  withFontSizes: () => (/* reexport */ withFontSizes)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/editor/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  __experimentalGetDefaultTemplatePartAreas: () => (__experimentalGetDefaultTemplatePartAreas),
  __experimentalGetDefaultTemplateType: () => (__experimentalGetDefaultTemplateType),
  __experimentalGetDefaultTemplateTypes: () => (__experimentalGetDefaultTemplateTypes),
  __experimentalGetTemplateInfo: () => (__experimentalGetTemplateInfo),
  __unstableIsEditorReady: () => (__unstableIsEditorReady),
  canInsertBlockType: () => (canInsertBlockType),
  canUserUseUnfilteredHTML: () => (canUserUseUnfilteredHTML),
  didPostSaveRequestFail: () => (didPostSaveRequestFail),
  didPostSaveRequestSucceed: () => (didPostSaveRequestSucceed),
  getActivePostLock: () => (getActivePostLock),
  getAdjacentBlockClientId: () => (getAdjacentBlockClientId),
  getAutosaveAttribute: () => (getAutosaveAttribute),
  getBlock: () => (getBlock),
  getBlockAttributes: () => (getBlockAttributes),
  getBlockCount: () => (getBlockCount),
  getBlockHierarchyRootClientId: () => (getBlockHierarchyRootClientId),
  getBlockIndex: () => (getBlockIndex),
  getBlockInsertionPoint: () => (getBlockInsertionPoint),
  getBlockListSettings: () => (getBlockListSettings),
  getBlockMode: () => (getBlockMode),
  getBlockName: () => (getBlockName),
  getBlockOrder: () => (getBlockOrder),
  getBlockRootClientId: () => (getBlockRootClientId),
  getBlockSelectionEnd: () => (getBlockSelectionEnd),
  getBlockSelectionStart: () => (getBlockSelectionStart),
  getBlocks: () => (getBlocks),
  getBlocksByClientId: () => (getBlocksByClientId),
  getClientIdsOfDescendants: () => (getClientIdsOfDescendants),
  getClientIdsWithDescendants: () => (getClientIdsWithDescendants),
  getCurrentPost: () => (getCurrentPost),
  getCurrentPostAttribute: () => (getCurrentPostAttribute),
  getCurrentPostId: () => (getCurrentPostId),
  getCurrentPostLastRevisionId: () => (getCurrentPostLastRevisionId),
  getCurrentPostRevisionsCount: () => (getCurrentPostRevisionsCount),
  getCurrentPostType: () => (getCurrentPostType),
  getCurrentTemplateId: () => (getCurrentTemplateId),
  getDeviceType: () => (getDeviceType),
  getEditedPostAttribute: () => (getEditedPostAttribute),
  getEditedPostContent: () => (getEditedPostContent),
  getEditedPostPreviewLink: () => (getEditedPostPreviewLink),
  getEditedPostSlug: () => (getEditedPostSlug),
  getEditedPostVisibility: () => (getEditedPostVisibility),
  getEditorBlocks: () => (getEditorBlocks),
  getEditorSelection: () => (getEditorSelection),
  getEditorSelectionEnd: () => (getEditorSelectionEnd),
  getEditorSelectionStart: () => (getEditorSelectionStart),
  getEditorSettings: () => (getEditorSettings),
  getFirstMultiSelectedBlockClientId: () => (getFirstMultiSelectedBlockClientId),
  getGlobalBlockCount: () => (getGlobalBlockCount),
  getInserterItems: () => (getInserterItems),
  getLastMultiSelectedBlockClientId: () => (getLastMultiSelectedBlockClientId),
  getMultiSelectedBlockClientIds: () => (getMultiSelectedBlockClientIds),
  getMultiSelectedBlocks: () => (getMultiSelectedBlocks),
  getMultiSelectedBlocksEndClientId: () => (getMultiSelectedBlocksEndClientId),
  getMultiSelectedBlocksStartClientId: () => (getMultiSelectedBlocksStartClientId),
  getNextBlockClientId: () => (getNextBlockClientId),
  getPermalink: () => (getPermalink),
  getPermalinkParts: () => (getPermalinkParts),
  getPostEdits: () => (getPostEdits),
  getPostLockUser: () => (getPostLockUser),
  getPostTypeLabel: () => (getPostTypeLabel),
  getPreviousBlockClientId: () => (getPreviousBlockClientId),
  getRenderingMode: () => (getRenderingMode),
  getSelectedBlock: () => (getSelectedBlock),
  getSelectedBlockClientId: () => (getSelectedBlockClientId),
  getSelectedBlockCount: () => (getSelectedBlockCount),
  getSelectedBlocksInitialCaretPosition: () => (getSelectedBlocksInitialCaretPosition),
  getStateBeforeOptimisticTransaction: () => (getStateBeforeOptimisticTransaction),
  getSuggestedPostFormat: () => (getSuggestedPostFormat),
  getTemplate: () => (getTemplate),
  getTemplateLock: () => (getTemplateLock),
  hasChangedContent: () => (hasChangedContent),
  hasEditorRedo: () => (hasEditorRedo),
  hasEditorUndo: () => (hasEditorUndo),
  hasInserterItems: () => (hasInserterItems),
  hasMultiSelection: () => (hasMultiSelection),
  hasNonPostEntityChanges: () => (hasNonPostEntityChanges),
  hasSelectedBlock: () => (hasSelectedBlock),
  hasSelectedInnerBlock: () => (hasSelectedInnerBlock),
  inSomeHistory: () => (inSomeHistory),
  isAncestorMultiSelected: () => (isAncestorMultiSelected),
  isAutosavingPost: () => (isAutosavingPost),
  isBlockInsertionPointVisible: () => (isBlockInsertionPointVisible),
  isBlockMultiSelected: () => (isBlockMultiSelected),
  isBlockSelected: () => (isBlockSelected),
  isBlockValid: () => (isBlockValid),
  isBlockWithinSelection: () => (isBlockWithinSelection),
  isCaretWithinFormattedText: () => (isCaretWithinFormattedText),
  isCleanNewPost: () => (isCleanNewPost),
  isCurrentPostPending: () => (isCurrentPostPending),
  isCurrentPostPublished: () => (isCurrentPostPublished),
  isCurrentPostScheduled: () => (isCurrentPostScheduled),
  isDeletingPost: () => (isDeletingPost),
  isEditedPostAutosaveable: () => (isEditedPostAutosaveable),
  isEditedPostBeingScheduled: () => (isEditedPostBeingScheduled),
  isEditedPostDateFloating: () => (isEditedPostDateFloating),
  isEditedPostDirty: () => (isEditedPostDirty),
  isEditedPostEmpty: () => (isEditedPostEmpty),
  isEditedPostNew: () => (isEditedPostNew),
  isEditedPostPublishable: () => (isEditedPostPublishable),
  isEditedPostSaveable: () => (isEditedPostSaveable),
  isEditorPanelEnabled: () => (isEditorPanelEnabled),
  isEditorPanelOpened: () => (isEditorPanelOpened),
  isEditorPanelRemoved: () => (isEditorPanelRemoved),
  isFirstMultiSelectedBlock: () => (isFirstMultiSelectedBlock),
  isInserterOpened: () => (isInserterOpened),
  isListViewOpened: () => (isListViewOpened),
  isMultiSelecting: () => (isMultiSelecting),
  isPermalinkEditable: () => (isPermalinkEditable),
  isPostAutosavingLocked: () => (isPostAutosavingLocked),
  isPostLockTakeover: () => (isPostLockTakeover),
  isPostLocked: () => (isPostLocked),
  isPostSavingLocked: () => (isPostSavingLocked),
  isPreviewingPost: () => (isPreviewingPost),
  isPublishSidebarEnabled: () => (isPublishSidebarEnabled),
  isPublishingPost: () => (isPublishingPost),
  isSavingNonPostEntityChanges: () => (isSavingNonPostEntityChanges),
  isSavingPost: () => (isSavingPost),
  isSelectionEnabled: () => (isSelectionEnabled),
  isTyping: () => (isTyping),
  isValidTemplate: () => (isValidTemplate)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/editor/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  __experimentalTearDownEditor: () => (__experimentalTearDownEditor),
  __unstableSaveForPreview: () => (__unstableSaveForPreview),
  autosave: () => (autosave),
  clearSelectedBlock: () => (clearSelectedBlock),
  createUndoLevel: () => (createUndoLevel),
  disablePublishSidebar: () => (disablePublishSidebar),
  editPost: () => (editPost),
  enablePublishSidebar: () => (enablePublishSidebar),
  enterFormattedText: () => (enterFormattedText),
  exitFormattedText: () => (exitFormattedText),
  hideInsertionPoint: () => (hideInsertionPoint),
  insertBlock: () => (insertBlock),
  insertBlocks: () => (insertBlocks),
  insertDefaultBlock: () => (insertDefaultBlock),
  lockPostAutosaving: () => (lockPostAutosaving),
  lockPostSaving: () => (lockPostSaving),
  mergeBlocks: () => (mergeBlocks),
  moveBlockToPosition: () => (moveBlockToPosition),
  moveBlocksDown: () => (moveBlocksDown),
  moveBlocksUp: () => (moveBlocksUp),
  multiSelect: () => (multiSelect),
  receiveBlocks: () => (receiveBlocks),
  redo: () => (redo),
  refreshPost: () => (refreshPost),
  removeBlock: () => (removeBlock),
  removeBlocks: () => (removeBlocks),
  removeEditorPanel: () => (removeEditorPanel),
  replaceBlock: () => (replaceBlock),
  replaceBlocks: () => (replaceBlocks),
  resetBlocks: () => (resetBlocks),
  resetEditorBlocks: () => (resetEditorBlocks),
  resetPost: () => (resetPost),
  savePost: () => (savePost),
  selectBlock: () => (selectBlock),
  setDeviceType: () => (setDeviceType),
  setEditedPost: () => (setEditedPost),
  setIsInserterOpened: () => (setIsInserterOpened),
  setIsListViewOpened: () => (setIsListViewOpened),
  setRenderingMode: () => (setRenderingMode),
  setTemplateValidity: () => (setTemplateValidity),
  setupEditor: () => (setupEditor),
  setupEditorState: () => (setupEditorState),
  showInsertionPoint: () => (showInsertionPoint),
  startMultiSelect: () => (startMultiSelect),
  startTyping: () => (startTyping),
  stopMultiSelect: () => (stopMultiSelect),
  stopTyping: () => (stopTyping),
  synchronizeTemplate: () => (synchronizeTemplate),
  toggleBlockMode: () => (toggleBlockMode),
  toggleEditorPanelEnabled: () => (toggleEditorPanelEnabled),
  toggleEditorPanelOpened: () => (toggleEditorPanelOpened),
  toggleSelection: () => (toggleSelection),
  trashPost: () => (trashPost),
  undo: () => (undo),
  unlockPostAutosaving: () => (unlockPostAutosaving),
  unlockPostSaving: () => (unlockPostSaving),
  updateBlock: () => (updateBlock),
  updateBlockAttributes: () => (updateBlockAttributes),
  updateBlockListSettings: () => (updateBlockListSettings),
  updateEditorSettings: () => (updateEditorSettings),
  updatePost: () => (updatePost),
  updatePostLock: () => (updatePostLock)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/editor/build-module/store/private-actions.js
var private_actions_namespaceObject = {};
__webpack_require__.r(private_actions_namespaceObject);
__webpack_require__.d(private_actions_namespaceObject, {
  createTemplate: () => (createTemplate),
  hideBlockTypes: () => (hideBlockTypes),
  setCurrentTemplateId: () => (setCurrentTemplateId),
  showBlockTypes: () => (showBlockTypes)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/editor/build-module/store/private-selectors.js
var private_selectors_namespaceObject = {};
__webpack_require__.r(private_selectors_namespaceObject);
__webpack_require__.d(private_selectors_namespaceObject, {
  getInsertionPoint: () => (getInsertionPoint),
  getListViewToggleRef: () => (getListViewToggleRef)
});

;// CONCATENATED MODULE: external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my theme or plugin will inevitably break in the next version of WordPress.', '@wordpress/editor');

;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/bindings/pattern-overrides.js
/**
 * WordPress dependencies
 */

/* harmony default export */ const pattern_overrides = ({
  name: 'core/pattern-overrides',
  label: (0,external_wp_i18n_namespaceObject._x)('Pattern Overrides', 'block bindings source'),
  useSource: null,
  lockAttributesEditing: false
});

;// CONCATENATED MODULE: external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// CONCATENATED MODULE: external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/defaults.js
/**
 * WordPress dependencies
 */


/**
 * The default post editor settings.
 *
 * @property {boolean|Array} allowedBlockTypes     Allowed block types
 * @property {boolean}       richEditingEnabled    Whether rich editing is enabled or not
 * @property {boolean}       codeEditingEnabled    Whether code editing is enabled or not
 * @property {boolean}       fontLibraryEnabled    Whether the font library is enabled or not.
 * @property {boolean}       enableCustomFields    Whether the WordPress custom fields are enabled or not.
 *                                                 true  = the user has opted to show the Custom Fields panel at the bottom of the editor.
 *                                                 false = the user has opted to hide the Custom Fields panel at the bottom of the editor.
 *                                                 undefined = the current environment does not support Custom Fields, so the option toggle in Preferences -> Panels to enable the Custom Fields panel is not displayed.
 * @property {number}        autosaveInterval      How often in seconds the post will be auto-saved via the REST API.
 * @property {number}        localAutosaveInterval How often in seconds the post will be backed up to sessionStorage.
 * @property {Array?}        availableTemplates    The available post templates
 * @property {boolean}       disablePostFormats    Whether or not the post formats are disabled
 * @property {Array?}        allowedMimeTypes      List of allowed mime types and file extensions
 * @property {number}        maxUploadFileSize     Maximum upload file size
 * @property {boolean}       supportsLayout        Whether the editor supports layouts.
 */
const EDITOR_SETTINGS_DEFAULTS = {
  ...external_wp_blockEditor_namespaceObject.SETTINGS_DEFAULTS,
  richEditingEnabled: true,
  codeEditingEnabled: true,
  fontLibraryEnabled: true,
  enableCustomFields: undefined,
  defaultRenderingMode: 'post-only'
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/reducer.js
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
  if (value && 'object' === typeof value && 'raw' in value) {
    return value.raw;
  }
  return value;
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
  const keysA = Object.keys(a).sort();
  const keysB = Object.keys(b).sort();
  return keysA.length === keysB.length && keysA.every((key, index) => keysB[index] === key);
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
function shouldOverwriteState(action, previousAction) {
  if (action.type === 'RESET_EDITOR_BLOCKS') {
    return !action.shouldCreateUndoLevel;
  }
  if (!previousAction || action.type !== previousAction.type) {
    return false;
  }
  return isUpdatingSamePostProperty(action, previousAction);
}
function postId(state = null, action) {
  switch (action.type) {
    case 'SET_EDITED_POST':
      return action.postId;
  }
  return state;
}
function templateId(state = null, action) {
  switch (action.type) {
    case 'SET_CURRENT_TEMPLATE_ID':
      return action.id;
  }
  return state;
}
function postType(state = null, action) {
  switch (action.type) {
    case 'SET_EDITED_POST':
      return action.postType;
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
function template(state = {
  isValid: true
}, action) {
  switch (action.type) {
    case 'SET_TEMPLATE_VALIDITY':
      return {
        ...state,
        isValid: action.isValid
      };
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
function saving(state = {}, action) {
  switch (action.type) {
    case 'REQUEST_POST_UPDATE_START':
    case 'REQUEST_POST_UPDATE_FINISH':
      return {
        pending: action.type === 'REQUEST_POST_UPDATE_START',
        options: action.options || {}
      };
  }
  return state;
}

/**
 * Reducer returning deleting post request state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
function deleting(state = {}, action) {
  switch (action.type) {
    case 'REQUEST_POST_DELETE_START':
    case 'REQUEST_POST_DELETE_FINISH':
      return {
        pending: action.type === 'REQUEST_POST_DELETE_START'
      };
  }
  return state;
}

/**
 * Post Lock State.
 *
 * @typedef {Object} PostLockState
 *
 * @property {boolean}  isLocked       Whether the post is locked.
 * @property {?boolean} isTakeover     Whether the post editing has been taken over.
 * @property {?boolean} activePostLock Active post lock value.
 * @property {?Object}  user           User that took over the post.
 */

/**
 * Reducer returning the post lock status.
 *
 * @param {PostLockState} state  Current state.
 * @param {Object}        action Dispatched action.
 *
 * @return {PostLockState} Updated state.
 */
function postLock(state = {
  isLocked: false
}, action) {
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
 * @param {PostLockState} state  Current state.
 * @param {Object}        action Dispatched action.
 *
 * @return {PostLockState} Updated state.
 */
function postSavingLock(state = {}, action) {
  switch (action.type) {
    case 'LOCK_POST_SAVING':
      return {
        ...state,
        [action.lockName]: true
      };
    case 'UNLOCK_POST_SAVING':
      {
        const {
          [action.lockName]: removedLockName,
          ...restState
        } = state;
        return restState;
      }
  }
  return state;
}

/**
 * Post autosaving lock.
 *
 * When post autosaving is locked, the post will not autosave.
 *
 * @param {PostLockState} state  Current state.
 * @param {Object}        action Dispatched action.
 *
 * @return {PostLockState} Updated state.
 */
function postAutosavingLock(state = {}, action) {
  switch (action.type) {
    case 'LOCK_POST_AUTOSAVING':
      return {
        ...state,
        [action.lockName]: true
      };
    case 'UNLOCK_POST_AUTOSAVING':
      {
        const {
          [action.lockName]: removedLockName,
          ...restState
        } = state;
        return restState;
      }
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
function editorSettings(state = EDITOR_SETTINGS_DEFAULTS, action) {
  switch (action.type) {
    case 'UPDATE_EDITOR_SETTINGS':
      return {
        ...state,
        ...action.settings
      };
  }
  return state;
}
function renderingMode(state = 'post-only', action) {
  switch (action.type) {
    case 'SET_RENDERING_MODE':
      return action.mode;
  }
  return state;
}

/**
 * Reducer returning the editing canvas device type.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
function deviceType(state = 'Desktop', action) {
  switch (action.type) {
    case 'SET_DEVICE_TYPE':
      return action.deviceType;
  }
  return state;
}

/**
 * Reducer storing the list of all programmatically removed panels.
 *
 * @param {Array}  state  Current state.
 * @param {Object} action Action object.
 *
 * @return {Array} Updated state.
 */
function removedPanels(state = [], action) {
  switch (action.type) {
    case 'REMOVE_PANEL':
      if (!state.includes(action.panelName)) {
        return [...state, action.panelName];
      }
  }
  return state;
}

/**
 * Reducer to set the block inserter panel open or closed.
 *
 * Note: this reducer interacts with the list view panel reducer
 * to make sure that only one of the two panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */
function blockInserterPanel(state = false, action) {
  switch (action.type) {
    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen ? false : state;
    case 'SET_IS_INSERTER_OPENED':
      return action.value;
  }
  return state;
}

/**
 * Reducer to set the list view panel open or closed.
 *
 * Note: this reducer interacts with the inserter panel reducer
 * to make sure that only one of the two panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */
function listViewPanel(state = false, action) {
  switch (action.type) {
    case 'SET_IS_INSERTER_OPENED':
      return action.value ? false : state;
    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen;
  }
  return state;
}

/**
 * This reducer does nothing aside initializing a ref to the list view toggle.
 * We will have a unique ref per "editor" instance.
 *
 * @param {Object} state
 * @return {Object} Reference to the list view toggle button.
 */
function listViewToggleRef(state = {
  current: null
}) {
  return state;
}
/* harmony default export */ const reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  postId,
  postType,
  templateId,
  saving,
  deleting,
  postLock,
  template,
  postSavingLock,
  editorSettings,
  postAutosavingLock,
  renderingMode,
  deviceType,
  removedPanels,
  blockInserterPanel,
  listViewPanel,
  listViewToggleRef
}));

;// CONCATENATED MODULE: ./node_modules/rememo/rememo.js


/** @typedef {(...args: any[]) => *[]} GetDependants */

/** @typedef {() => void} Clear */

/**
 * @typedef {{
 *   getDependants: GetDependants,
 *   clear: Clear
 * }} EnhancedSelector
 */

/**
 * Internal cache entry.
 *
 * @typedef CacheNode
 *
 * @property {?CacheNode|undefined} [prev] Previous node.
 * @property {?CacheNode|undefined} [next] Next node.
 * @property {*[]} args Function arguments for cache entry.
 * @property {*} val Function result.
 */

/**
 * @typedef Cache
 *
 * @property {Clear} clear Function to clear cache.
 * @property {boolean} [isUniqueByDependants] Whether dependants are valid in
 * considering cache uniqueness. A cache is unique if dependents are all arrays
 * or objects.
 * @property {CacheNode?} [head] Cache head.
 * @property {*[]} [lastDependants] Dependants from previous invocation.
 */

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {{}}
 */
var LEAF_KEY = {};

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @template T
 *
 * @param {T} value Value to return.
 *
 * @return {[T]} Value returned as entry in array.
 */
function arrayOf(value) {
	return [value];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike(value) {
	return !!value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Cache} Cache object.
 */
function createCache() {
	/** @type {Cache} */
	var cache = {
		clear: function () {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {*[]} a First array.
 * @param {*[]} b Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual(a, b, fromIndex) {
	var i;

	if (a.length !== b.length) {
		return false;
	}

	for (i = fromIndex; i < a.length; i++) {
		if (a[i] !== b[i]) {
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
 * @template {(...args: *[]) => *} S
 *
 * @param {S} selector Selector function.
 * @param {GetDependants=} getDependants Dependant getter returning an array of
 * references used in cache bust consideration.
 */
/* harmony default export */ function rememo(selector, getDependants) {
	/** @type {WeakMap<*,*>} */
	var rootCache;

	/** @type {GetDependants} */
	var normalizedGetDependants = getDependants ? getDependants : arrayOf;

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
	 * @param {*[]} dependants Selector dependants.
	 *
	 * @return {Cache} Cache object.
	 */
	function getCache(dependants) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i,
			dependant,
			map,
			cache;

		for (i = 0; i < dependants.length; i++) {
			dependant = dependants[i];

			// Can only compose WeakMap from object-like key.
			if (!isObjectLike(dependant)) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if (caches.has(dependant)) {
				// Traverse into nested WeakMap.
				caches = caches.get(dependant);
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set(dependant, map);
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if (!caches.has(LEAF_KEY)) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set(LEAF_KEY, cache);
		}

		return caches.get(LEAF_KEY);
	}

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = new WeakMap();
	}

	/* eslint-disable jsdoc/check-param-names */
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {*}    source    Source object for derivation.
	 * @param {...*} extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	/* eslint-enable jsdoc/check-param-names */
	function callSelector(/* source, ...extraArgs */) {
		var len = arguments.length,
			cache,
			node,
			i,
			args,
			dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		dependants = normalizedGetDependants.apply(null, args);
		cache = getCache(dependants);

		// If not guaranteed uniqueness by dependants (primitive type), shallow
		// compare against last dependants and, if references have changed,
		// destroy cache to recalculate result.
		if (!cache.isUniqueByDependants) {
			if (
				cache.lastDependants &&
				!isShallowEqual(dependants, cache.lastDependants, 0)
			) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while (node) {
			// Check whether node arguments match arguments
			if (!isShallowEqual(node.args, args, 1)) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== cache.head) {
				// Adjust siblings to point to each other.
				/** @type {CacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				/** @type {CacheNode} */ (cache.head).prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = /** @type {CacheNode} */ ({
			// Generate the result from original function
			val: selector.apply(null, args),
		});

		// Avoid including the source object in the cache.
		args[0] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (cache.head) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = normalizedGetDependants;
	callSelector.clear = clear;
	clear();

	return /** @type {S & EnhancedSelector} */ (callSelector);
}

;// CONCATENATED MODULE: external ["wp","date"]
const external_wp_date_namespaceObject = window["wp"]["date"];
;// CONCATENATED MODULE: external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__(1609);
;// CONCATENATED MODULE: external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/layout.js

/**
 * WordPress dependencies
 */

const layout = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ const library_layout = (layout);

;// CONCATENATED MODULE: external ["wp","preferences"]
const external_wp_preferences_namespaceObject = window["wp"]["preferences"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/constants.js
/**
 * Set of post properties for which edits should assume a merging behavior,
 * assuming an object value.
 *
 * @type {Set}
 */
const EDIT_MERGE_PROPERTIES = new Set(['meta']);

/**
 * Constant for the store module (or reducer) key.
 *
 * @type {string}
 */
const STORE_NAME = 'core/editor';
const SAVE_POST_NOTICE_ID = 'SAVE_POST_NOTICE_ID';
const TRASH_POST_NOTICE_ID = 'TRASH_POST_NOTICE_ID';
const PERMALINK_POSTNAME_REGEX = /%(?:postname|pagename)%/;
const ONE_MINUTE_IN_MS = 60 * 1000;
const AUTOSAVE_PROPERTIES = ['title', 'excerpt', 'content'];

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/header.js

/**
 * WordPress dependencies
 */

const header = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18.5 10.5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ const library_header = (header);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/footer.js

/**
 * WordPress dependencies
 */

const footer = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M18 5.5h-8v8h8.5V6a.5.5 0 00-.5-.5zm-9.5 8h-3V6a.5.5 0 01.5-.5h2.5v8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ const library_footer = (footer);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/sidebar.js

/**
 * WordPress dependencies
 */

const sidebar = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ const library_sidebar = (sidebar);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/symbol-filled.js

/**
 * WordPress dependencies
 */

const symbolFilled = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-17.6 1L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z"
}));
/* harmony default export */ const symbol_filled = (symbolFilled);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/get-template-part-icon.js
/**
 * WordPress dependencies
 */

/**
 * Helper function to retrieve the corresponding icon by name.
 *
 * @param {string} iconName The name of the icon.
 *
 * @return {Object} The corresponding icon.
 */
function getTemplatePartIcon(iconName) {
  if ('header' === iconName) {
    return library_header;
  } else if ('footer' === iconName) {
    return library_footer;
  } else if ('sidebar' === iconName) {
    return library_sidebar;
  }
  return symbol_filled;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/selectors.js
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
const EMPTY_OBJECT = {};

/**
 * Returns true if any past editor history snapshots exist, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether undo history exists.
 */
const hasEditorUndo = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  return select(external_wp_coreData_namespaceObject.store).hasUndo();
});

/**
 * Returns true if any future editor history snapshots exist, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether redo history exists.
 */
const hasEditorRedo = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  return select(external_wp_coreData_namespaceObject.store).hasRedo();
});

/**
 * Returns true if the currently edited post is yet to be saved, or false if
 * the post has been saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post is new.
 */
function isEditedPostNew(state) {
  return getCurrentPost(state).status === 'auto-draft';
}

/**
 * Returns true if content includes unsaved changes, or false otherwise.
 *
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether content includes unsaved changes.
 */
function hasChangedContent(state) {
  const edits = getPostEdits(state);
  return 'content' in edits;
}

/**
 * Returns true if there are unsaved values for the current edit session, or
 * false if the editing state matches the saved or new post.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether unsaved values exist.
 */
const isEditedPostDirty = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  // Edits should contain only fields which differ from the saved post (reset
  // at initial load and save complete). Thus, a non-empty edits state can be
  // inferred to contain unsaved values.
  const postType = getCurrentPostType(state);
  const postId = getCurrentPostId(state);
  if (select(external_wp_coreData_namespaceObject.store).hasEditsForEntityRecord('postType', postType, postId)) {
    return true;
  }
  return false;
});

/**
 * Returns true if there are unsaved edits for entities other than
 * the editor's post, and false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether there are edits or not.
 */
const hasNonPostEntityChanges = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const dirtyEntityRecords = select(external_wp_coreData_namespaceObject.store).__experimentalGetDirtyEntityRecords();
  const {
    type,
    id
  } = getCurrentPost(state);
  return dirtyEntityRecords.some(entityRecord => entityRecord.kind !== 'postType' || entityRecord.name !== type || entityRecord.key !== id);
});

/**
 * Returns true if there are no unsaved values for the current edit session and
 * if the currently edited post is new (has never been saved before).
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether new post and unsaved values exist.
 */
function isCleanNewPost(state) {
  return !isEditedPostDirty(state) && isEditedPostNew(state);
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
const getCurrentPost = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const postId = getCurrentPostId(state);
  const postType = getCurrentPostType(state);
  const post = select(external_wp_coreData_namespaceObject.store).getRawEntityRecord('postType', postType, postId);
  if (post) {
    return post;
  }

  // This exists for compatibility with the previous selector behavior
  // which would guarantee an object return based on the editor reducer's
  // default empty object state.
  return EMPTY_OBJECT;
});

/**
 * Returns the post type of the post currently being edited.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Post type.
 */
function getCurrentPostType(state) {
  return state.postType;
}

/**
 * Returns the ID of the post currently being edited, or null if the post has
 * not yet been saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {?number} ID of current post.
 */
function getCurrentPostId(state) {
  return state.postId;
}

/**
 * Returns the template ID currently being rendered/edited
 *
 * @param {Object} state Global application state.
 *
 * @return {string?} Template ID.
 */
function getCurrentTemplateId(state) {
  return state.templateId;
}

/**
 * Returns the number of revisions of the post currently being edited.
 *
 * @param {Object} state Global application state.
 *
 * @return {number} Number of revisions.
 */
function getCurrentPostRevisionsCount(state) {
  var _getCurrentPost$_link;
  return (_getCurrentPost$_link = getCurrentPost(state)._links?.['version-history']?.[0]?.count) !== null && _getCurrentPost$_link !== void 0 ? _getCurrentPost$_link : 0;
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
  var _getCurrentPost$_link2;
  return (_getCurrentPost$_link2 = getCurrentPost(state)._links?.['predecessor-version']?.[0]?.id) !== null && _getCurrentPost$_link2 !== void 0 ? _getCurrentPost$_link2 : null;
}

/**
 * Returns any post values which have been changed in the editor but not yet
 * been saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Object of key value pairs comprising unsaved edits.
 */
const getPostEdits = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const postType = getCurrentPostType(state);
  const postId = getCurrentPostId(state);
  return select(external_wp_coreData_namespaceObject.store).getEntityRecordEdits('postType', postType, postId) || EMPTY_OBJECT;
});

/**
 * Returns an attribute value of the saved post.
 *
 * @param {Object} state         Global application state.
 * @param {string} attributeName Post attribute name.
 *
 * @return {*} Post attribute value.
 */
function getCurrentPostAttribute(state, attributeName) {
  switch (attributeName) {
    case 'type':
      return getCurrentPostType(state);
    case 'id':
      return getCurrentPostId(state);
    default:
      const post = getCurrentPost(state);
      if (!post.hasOwnProperty(attributeName)) {
        break;
      }
      return getPostRawValue(post[attributeName]);
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
const getNestedEditedPostProperty = rememo((state, attributeName) => {
  const edits = getPostEdits(state);
  if (!edits.hasOwnProperty(attributeName)) {
    return getCurrentPostAttribute(state, attributeName);
  }
  return {
    ...getCurrentPostAttribute(state, attributeName),
    ...edits[attributeName]
  };
}, (state, attributeName) => [getCurrentPostAttribute(state, attributeName), getPostEdits(state)[attributeName]]);

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
function getEditedPostAttribute(state, attributeName) {
  // Special cases.
  switch (attributeName) {
    case 'content':
      return getEditedPostContent(state);
  }

  // Fall back to saved post value if not edited.
  const edits = getPostEdits(state);
  if (!edits.hasOwnProperty(attributeName)) {
    return getCurrentPostAttribute(state, attributeName);
  }

  // Merge properties are objects which contain only the patch edit in state,
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
 * @deprecated since 5.6. Callers should use the `getAutosave( postType, postId, userId )` selector
 * 			   from the '@wordpress/core-data' package and access properties on the returned
 * 			   autosave object using getPostRawValue.
 *
 * @param {Object} state         Global application state.
 * @param {string} attributeName Autosave attribute name.
 *
 * @return {*} Autosave attribute value.
 */
const getAutosaveAttribute = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, attributeName) => {
  if (!AUTOSAVE_PROPERTIES.includes(attributeName) && attributeName !== 'preview_link') {
    return;
  }
  const postType = getCurrentPostType(state);

  // Currently template autosaving is not supported.
  if (postType === 'wp_template') {
    return false;
  }
  const postId = getCurrentPostId(state);
  const currentUserId = select(external_wp_coreData_namespaceObject.store).getCurrentUser()?.id;
  const autosave = select(external_wp_coreData_namespaceObject.store).getAutosave(postType, postId, currentUserId);
  if (autosave) {
    return getPostRawValue(autosave[attributeName]);
  }
});

/**
 * Returns the current visibility of the post being edited, preferring the
 * unsaved value if different than the saved post. The return value is one of
 * "private", "password", or "public".
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Post visibility.
 */
function getEditedPostVisibility(state) {
  const status = getEditedPostAttribute(state, 'status');
  if (status === 'private') {
    return 'private';
  }
  const password = getEditedPostAttribute(state, 'password');
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
  return getCurrentPost(state).status === 'pending';
}

/**
 * Return true if the current post has already been published.
 *
 * @param {Object}  state       Global application state.
 * @param {Object?} currentPost Explicit current post for bypassing registry selector.
 *
 * @return {boolean} Whether the post has been published.
 */
function isCurrentPostPublished(state, currentPost) {
  const post = currentPost || getCurrentPost(state);
  return ['publish', 'private'].indexOf(post.status) !== -1 || post.status === 'future' && !(0,external_wp_date_namespaceObject.isInTheFuture)(new Date(Number((0,external_wp_date_namespaceObject.getDate)(post.date)) - ONE_MINUTE_IN_MS));
}

/**
 * Returns true if post is already scheduled.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether current post is scheduled to be posted.
 */
function isCurrentPostScheduled(state) {
  return getCurrentPost(state).status === 'future' && !isCurrentPostPublished(state);
}

/**
 * Return true if the post being edited can be published.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post can been published.
 */
function isEditedPostPublishable(state) {
  const post = getCurrentPost(state);

  // TODO: Post being publishable should be superset of condition of post
  // being saveable. Currently this restriction is imposed at UI.
  //
  //  See: <PostPublishButton /> (`isButtonEnabled` assigned by `isSaveable`).

  return isEditedPostDirty(state) || ['publish', 'private', 'future'].indexOf(post.status) === -1;
}

/**
 * Returns true if the post can be saved, or false otherwise. A post must
 * contain a title, an excerpt, or non-empty content to be valid for save.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post can be saved.
 */
function isEditedPostSaveable(state) {
  if (isSavingPost(state)) {
    return false;
  }

  // TODO: Post should not be saveable if not dirty. Cannot be added here at
  // this time since posts where meta boxes are present can be saved even if
  // the post is not dirty. Currently this restriction is imposed at UI, but
  // should be moved here.
  //
  //  See: `isEditedPostPublishable` (includes `isEditedPostDirty` condition)
  //  See: <PostSavedState /> (`forceIsDirty` prop)
  //  See: <PostPublishButton /> (`forceIsDirty` prop)
  //  See: https://github.com/WordPress/gutenberg/pull/4184.

  return !!getEditedPostAttribute(state, 'title') || !!getEditedPostAttribute(state, 'excerpt') || !isEditedPostEmpty(state) || external_wp_element_namespaceObject.Platform.OS === 'native';
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
const isEditedPostEmpty = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  // While the condition of truthy content string is sufficient to determine
  // emptiness, testing saveable blocks length is a trivial operation. Since
  // this function can be called frequently, optimize for the fast case as a
  // condition of the mere existence of blocks. Note that the value of edited
  // content takes precedent over block content, and must fall through to the
  // default logic.
  const postId = getCurrentPostId(state);
  const postType = getCurrentPostType(state);
  const record = select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord('postType', postType, postId);
  if (typeof record.content !== 'function') {
    return !record.content;
  }
  const blocks = getEditedPostAttribute(state, 'blocks');
  if (blocks.length === 0) {
    return true;
  }

  // Pierce the abstraction of the serializer in knowing that blocks are
  // joined with newlines such that even if every individual block
  // produces an empty save result, the serialized content is non-empty.
  if (blocks.length > 1) {
    return false;
  }

  // There are two conditions under which the optimization cannot be
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
  const blockName = blocks[0].name;
  if (blockName !== (0,external_wp_blocks_namespaceObject.getDefaultBlockName)() && blockName !== (0,external_wp_blocks_namespaceObject.getFreeformContentHandlerName)()) {
    return false;
  }
  return !getEditedPostContent(state);
});

/**
 * Returns true if the post can be autosaved, or false otherwise.
 *
 * @param {Object} state    Global application state.
 * @param {Object} autosave A raw autosave object from the REST API.
 *
 * @return {boolean} Whether the post can be autosaved.
 */
const isEditedPostAutosaveable = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  // A post must contain a title, an excerpt, or non-empty content to be valid for autosaving.
  if (!isEditedPostSaveable(state)) {
    return false;
  }

  // A post is not autosavable when there is a post autosave lock.
  if (isPostAutosavingLocked(state)) {
    return false;
  }
  const postType = getCurrentPostType(state);

  // Currently template autosaving is not supported.
  if (postType === 'wp_template') {
    return false;
  }
  const postId = getCurrentPostId(state);
  const hasFetchedAutosave = select(external_wp_coreData_namespaceObject.store).hasFetchedAutosaves(postType, postId);
  const currentUserId = select(external_wp_coreData_namespaceObject.store).getCurrentUser()?.id;

  // Disable reason - this line causes the side-effect of fetching the autosave
  // via a resolver, moving below the return would result in the autosave never
  // being fetched.
  // eslint-disable-next-line @wordpress/no-unused-vars-before-return
  const autosave = select(external_wp_coreData_namespaceObject.store).getAutosave(postType, postId, currentUserId);

  // If any existing autosaves have not yet been fetched, this function is
  // unable to determine if the post is autosaveable, so return false.
  if (!hasFetchedAutosave) {
    return false;
  }

  // If we don't already have an autosave, the post is autosaveable.
  if (!autosave) {
    return true;
  }

  // To avoid an expensive content serialization, use the content dirtiness
  // flag in place of content field comparison against the known autosave.
  // This is not strictly accurate, and relies on a tolerance toward autosave
  // request failures for unnecessary saves.
  if (hasChangedContent(state)) {
    return true;
  }

  // If title, excerpt, or meta have changed, the post is autosaveable.
  return ['title', 'excerpt', 'meta'].some(field => getPostRawValue(autosave[field]) !== getEditedPostAttribute(state, field));
});

/**
 * Return true if the post being edited is being scheduled. Preferring the
 * unsaved status values.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post has been published.
 */
function isEditedPostBeingScheduled(state) {
  const date = getEditedPostAttribute(state, 'date');
  // Offset the date by one minute (network latency).
  const checkedDate = new Date(Number((0,external_wp_date_namespaceObject.getDate)(date)) - ONE_MINUTE_IN_MS);
  return (0,external_wp_date_namespaceObject.isInTheFuture)(checkedDate);
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
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether the edited post has a floating date value.
 */
function isEditedPostDateFloating(state) {
  const date = getEditedPostAttribute(state, 'date');
  const modified = getEditedPostAttribute(state, 'modified');

  // This should be the status of the persisted post
  // It shouldn't use the "edited" status otherwise it breaks the
  // inferred post data floating status
  // See https://github.com/WordPress/gutenberg/issues/28083.
  const status = getCurrentPost(state).status;
  if (status === 'draft' || status === 'auto-draft' || status === 'pending') {
    return date === modified || date === null;
  }
  return false;
}

/**
 * Returns true if the post is currently being deleted, or false otherwise.
 *
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether post is being deleted.
 */
function isDeletingPost(state) {
  return !!state.deleting.pending;
}

/**
 * Returns true if the post is currently being saved, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether post is being saved.
 */
function isSavingPost(state) {
  return !!state.saving.pending;
}

/**
 * Returns true if non-post entities are currently being saved, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether non-post entities are being saved.
 */
const isSavingNonPostEntityChanges = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const entitiesBeingSaved = select(external_wp_coreData_namespaceObject.store).__experimentalGetEntitiesBeingSaved();
  const {
    type,
    id
  } = getCurrentPost(state);
  return entitiesBeingSaved.some(entityRecord => entityRecord.kind !== 'postType' || entityRecord.name !== type || entityRecord.key !== id);
});

/**
 * Returns true if a previous post save was attempted successfully, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post was saved successfully.
 */
const didPostSaveRequestSucceed = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const postType = getCurrentPostType(state);
  const postId = getCurrentPostId(state);
  return !select(external_wp_coreData_namespaceObject.store).getLastEntitySaveError('postType', postType, postId);
});

/**
 * Returns true if a previous post save was attempted but failed, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post save failed.
 */
const didPostSaveRequestFail = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const postType = getCurrentPostType(state);
  const postId = getCurrentPostId(state);
  return !!select(external_wp_coreData_namespaceObject.store).getLastEntitySaveError('postType', postType, postId);
});

/**
 * Returns true if the post is autosaving, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post is autosaving.
 */
function isAutosavingPost(state) {
  return isSavingPost(state) && Boolean(state.saving.options?.isAutosave);
}

/**
 * Returns true if the post is being previewed, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post is being previewed.
 */
function isPreviewingPost(state) {
  return isSavingPost(state) && Boolean(state.saving.options?.isPreview);
}

/**
 * Returns the post preview link
 *
 * @param {Object} state Global application state.
 *
 * @return {string | undefined} Preview Link.
 */
function getEditedPostPreviewLink(state) {
  if (state.saving.pending || isSavingPost(state)) {
    return;
  }
  let previewLink = getAutosaveAttribute(state, 'preview_link');
  // Fix for issue: https://github.com/WordPress/gutenberg/issues/33616
  // If the post is draft, ignore the preview link from the autosave record,
  // because the preview could be a stale autosave if the post was switched from
  // published to draft.
  // See: https://github.com/WordPress/gutenberg/pull/37952.
  if (!previewLink || 'draft' === getCurrentPost(state).status) {
    previewLink = getEditedPostAttribute(state, 'link');
    if (previewLink) {
      previewLink = (0,external_wp_url_namespaceObject.addQueryArgs)(previewLink, {
        preview: true
      });
    }
  }
  const featuredImageId = getEditedPostAttribute(state, 'featured_media');
  if (previewLink && featuredImageId) {
    return (0,external_wp_url_namespaceObject.addQueryArgs)(previewLink, {
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
 * @return {?string} Suggested post format.
 */
const getSuggestedPostFormat = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  const blocks = select(external_wp_blockEditor_namespaceObject.store).getBlocks();
  if (blocks.length > 2) return null;
  let name;
  // If there is only one block in the content of the post grab its name
  // so we can derive a suitable post format from it.
  if (blocks.length === 1) {
    name = blocks[0].name;
    // Check for core/embed `video` and `audio` eligible suggestions.
    if (name === 'core/embed') {
      const provider = blocks[0].attributes?.providerNameSlug;
      if (['youtube', 'vimeo'].includes(provider)) {
        name = 'core/video';
      } else if (['spotify', 'soundcloud'].includes(provider)) {
        name = 'core/audio';
      }
    }
  }

  // If there are two blocks in the content and the last one is a text blocks
  // grab the name of the first one to also suggest a post format from it.
  if (blocks.length === 2 && blocks[1].name === 'core/paragraph') {
    name = blocks[0].name;
  }

  // We only convert to default post formats in core.
  switch (name) {
    case 'core/image':
      return 'image';
    case 'core/quote':
    case 'core/pullquote':
      return 'quote';
    case 'core/gallery':
      return 'gallery';
    case 'core/video':
      return 'video';
    case 'core/audio':
      return 'audio';
    default:
      return null;
  }
});

/**
 * Returns the content of the post being edited.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Post content.
 */
const getEditedPostContent = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const postId = getCurrentPostId(state);
  const postType = getCurrentPostType(state);
  const record = select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord('postType', postType, postId);
  if (record) {
    if (typeof record.content === 'function') {
      return record.content(record);
    } else if (record.blocks) {
      return (0,external_wp_blocks_namespaceObject.__unstableSerializeAndClean)(record.blocks);
    } else if (record.content) {
      return record.content;
    }
  }
  return '';
});

/**
 * Returns true if the post is being published, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether post is being published.
 */
function isPublishingPost(state) {
  return isSavingPost(state) && !isCurrentPostPublished(state) && getEditedPostAttribute(state, 'status') === 'publish';
}

/**
 * Returns whether the permalink is editable or not.
 *
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether or not the permalink is editable.
 */
function isPermalinkEditable(state) {
  const permalinkTemplate = getEditedPostAttribute(state, 'permalink_template');
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
  const permalinkParts = getPermalinkParts(state);
  if (!permalinkParts) {
    return null;
  }
  const {
    prefix,
    postName,
    suffix
  } = permalinkParts;
  if (isPermalinkEditable(state)) {
    return prefix + postName + suffix;
  }
  return prefix;
}

/**
 * Returns the slug for the post being edited, preferring a manually edited
 * value if one exists, then a sanitized version of the current post title, and
 * finally the post ID.
 *
 * @param {Object} state Editor state.
 *
 * @return {string} The current slug to be displayed in the editor
 */
function getEditedPostSlug(state) {
  return getEditedPostAttribute(state, 'slug') || (0,external_wp_url_namespaceObject.cleanForSlug)(getEditedPostAttribute(state, 'title')) || getCurrentPostId(state);
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
function getPermalinkParts(state) {
  const permalinkTemplate = getEditedPostAttribute(state, 'permalink_template');
  if (!permalinkTemplate) {
    return null;
  }
  const postName = getEditedPostAttribute(state, 'slug') || getEditedPostAttribute(state, 'generated_slug');
  const [prefix, suffix] = permalinkTemplate.split(PERMALINK_POSTNAME_REGEX);
  return {
    prefix,
    postName,
    suffix
  };
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
function isPostSavingLocked(state) {
  return Object.keys(state.postSavingLock).length > 0;
}

/**
 * Returns whether post autosaving is locked.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Is locked.
 */
function isPostAutosavingLocked(state) {
  return Object.keys(state.postAutosavingLock).length > 0;
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
  return Boolean(getCurrentPost(state)._links?.hasOwnProperty('wp:action-unfiltered-html'));
}

/**
 * Returns whether the pre-publish panel should be shown
 * or skipped when the user clicks the "publish" button.
 *
 * @return {boolean} Whether the pre-publish panel should be shown or not.
 */
const isPublishSidebarEnabled = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => !!select(external_wp_preferences_namespaceObject.store).get('core/edit-post', 'isPublishSidebarEnabled'));

/**
 * Return the current block list.
 *
 * @param {Object} state
 * @return {Array} Block list.
 */
const getEditorBlocks = rememo(state => {
  return getEditedPostAttribute(state, 'blocks') || (0,external_wp_blocks_namespaceObject.parse)(getEditedPostContent(state));
}, state => [getEditedPostAttribute(state, 'blocks'), getEditedPostContent(state)]);

/**
 * Returns true if the given panel was programmatically removed, or false otherwise.
 * All panels are not removed by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is removed.
 */
function isEditorPanelRemoved(state, panelName) {
  return state.removedPanels.includes(panelName);
}

/**
 * Returns true if the given panel is enabled, or false otherwise. Panels are
 * enabled by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is enabled.
 */
const isEditorPanelEnabled = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, panelName) => {
  // For backward compatibility, we check edit-post
  // even though now this is in "editor" package.
  const inactivePanels = select(external_wp_preferences_namespaceObject.store).get('core', 'inactivePanels');
  return !isEditorPanelRemoved(state, panelName) && !inactivePanels?.includes(panelName);
});

/**
 * Returns true if the given panel is open, or false otherwise. Panels are
 * closed by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is open.
 */
const isEditorPanelOpened = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, panelName) => {
  // For backward compatibility, we check edit-post
  // even though now this is in "editor" package.
  const openPanels = select(external_wp_preferences_namespaceObject.store).get('core', 'openPanels');
  return !!openPanels?.includes(panelName);
});

/**
 * A block selection object.
 *
 * @typedef {Object} WPBlockSelection
 *
 * @property {string} clientId     A block client ID.
 * @property {string} attributeKey A block attribute key.
 * @property {number} offset       An attribute value offset, based on the rich
 *                                 text value. See `wp.richText.create`.
 */

/**
 * Returns the current selection start.
 *
 * @param {Object} state
 * @return {WPBlockSelection} The selection start.
 *
 * @deprecated since Gutenberg 10.0.0.
 */
function getEditorSelectionStart(state) {
  external_wp_deprecated_default()("select('core/editor').getEditorSelectionStart", {
    since: '5.8',
    alternative: "select('core/editor').getEditorSelection"
  });
  return getEditedPostAttribute(state, 'selection')?.selectionStart;
}

/**
 * Returns the current selection end.
 *
 * @param {Object} state
 * @return {WPBlockSelection} The selection end.
 *
 * @deprecated since Gutenberg 10.0.0.
 */
function getEditorSelectionEnd(state) {
  external_wp_deprecated_default()("select('core/editor').getEditorSelectionStart", {
    since: '5.8',
    alternative: "select('core/editor').getEditorSelection"
  });
  return getEditedPostAttribute(state, 'selection')?.selectionEnd;
}

/**
 * Returns the current selection.
 *
 * @param {Object} state
 * @return {WPBlockSelection} The selection end.
 */
function getEditorSelection(state) {
  return getEditedPostAttribute(state, 'selection');
}

/**
 * Is the editor ready
 *
 * @param {Object} state
 * @return {boolean} is Ready.
 */
function __unstableIsEditorReady(state) {
  return !!state.postId;
}

/**
 * Returns the post editor settings.
 *
 * @param {Object} state Editor state.
 *
 * @return {Object} The editor settings object.
 */
function getEditorSettings(state) {
  return state.editorSettings;
}

/**
 * Returns the post editor's rendering mode.
 *
 * @param {Object} state Editor state.
 *
 * @return {string} Rendering mode.
 */
function getRenderingMode(state) {
  return state.renderingMode;
}

/**
 * Returns the current editing canvas device type.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Device type.
 */
function getDeviceType(state) {
  return state.deviceType;
}

/**
 * Returns true if the list view is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the list view is opened.
 */
function isListViewOpened(state) {
  return state.listViewPanel;
}

/**
 * Returns true if the inserter is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the inserter is opened.
 */
function isInserterOpened(state) {
  return !!state.blockInserterPanel;
}

/*
 * Backward compatibility
 */

/**
 * Returns state object prior to a specified optimist transaction ID, or `null`
 * if the transaction corresponding to the given ID cannot be found.
 *
 * @deprecated since Gutenberg 9.7.0.
 */
function getStateBeforeOptimisticTransaction() {
  external_wp_deprecated_default()("select('core/editor').getStateBeforeOptimisticTransaction", {
    since: '5.7',
    hint: 'No state history is kept on this store anymore'
  });
  return null;
}
/**
 * Returns true if an optimistic transaction is pending commit, for which the
 * before state satisfies the given predicate function.
 *
 * @deprecated since Gutenberg 9.7.0.
 */
function inSomeHistory() {
  external_wp_deprecated_default()("select('core/editor').inSomeHistory", {
    since: '5.7',
    hint: 'No state history is kept on this store anymore'
  });
  return false;
}
function getBlockEditorSelector(name) {
  return (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, ...args) => {
    external_wp_deprecated_default()("`wp.data.select( 'core/editor' )." + name + '`', {
      since: '5.3',
      alternative: "`wp.data.select( 'core/block-editor' )." + name + '`',
      version: '6.2'
    });
    return select(external_wp_blockEditor_namespaceObject.store)[name](...args);
  });
}

/**
 * @see getBlockName in core/block-editor store.
 */
const getBlockName = getBlockEditorSelector('getBlockName');

/**
 * @see isBlockValid in core/block-editor store.
 */
const isBlockValid = getBlockEditorSelector('isBlockValid');

/**
 * @see getBlockAttributes in core/block-editor store.
 */
const getBlockAttributes = getBlockEditorSelector('getBlockAttributes');

/**
 * @see getBlock in core/block-editor store.
 */
const getBlock = getBlockEditorSelector('getBlock');

/**
 * @see getBlocks in core/block-editor store.
 */
const getBlocks = getBlockEditorSelector('getBlocks');

/**
 * @see getClientIdsOfDescendants in core/block-editor store.
 */
const getClientIdsOfDescendants = getBlockEditorSelector('getClientIdsOfDescendants');

/**
 * @see getClientIdsWithDescendants in core/block-editor store.
 */
const getClientIdsWithDescendants = getBlockEditorSelector('getClientIdsWithDescendants');

/**
 * @see getGlobalBlockCount in core/block-editor store.
 */
const getGlobalBlockCount = getBlockEditorSelector('getGlobalBlockCount');

/**
 * @see getBlocksByClientId in core/block-editor store.
 */
const getBlocksByClientId = getBlockEditorSelector('getBlocksByClientId');

/**
 * @see getBlockCount in core/block-editor store.
 */
const getBlockCount = getBlockEditorSelector('getBlockCount');

/**
 * @see getBlockSelectionStart in core/block-editor store.
 */
const getBlockSelectionStart = getBlockEditorSelector('getBlockSelectionStart');

/**
 * @see getBlockSelectionEnd in core/block-editor store.
 */
const getBlockSelectionEnd = getBlockEditorSelector('getBlockSelectionEnd');

/**
 * @see getSelectedBlockCount in core/block-editor store.
 */
const getSelectedBlockCount = getBlockEditorSelector('getSelectedBlockCount');

/**
 * @see hasSelectedBlock in core/block-editor store.
 */
const hasSelectedBlock = getBlockEditorSelector('hasSelectedBlock');

/**
 * @see getSelectedBlockClientId in core/block-editor store.
 */
const getSelectedBlockClientId = getBlockEditorSelector('getSelectedBlockClientId');

/**
 * @see getSelectedBlock in core/block-editor store.
 */
const getSelectedBlock = getBlockEditorSelector('getSelectedBlock');

/**
 * @see getBlockRootClientId in core/block-editor store.
 */
const getBlockRootClientId = getBlockEditorSelector('getBlockRootClientId');

/**
 * @see getBlockHierarchyRootClientId in core/block-editor store.
 */
const getBlockHierarchyRootClientId = getBlockEditorSelector('getBlockHierarchyRootClientId');

/**
 * @see getAdjacentBlockClientId in core/block-editor store.
 */
const getAdjacentBlockClientId = getBlockEditorSelector('getAdjacentBlockClientId');

/**
 * @see getPreviousBlockClientId in core/block-editor store.
 */
const getPreviousBlockClientId = getBlockEditorSelector('getPreviousBlockClientId');

/**
 * @see getNextBlockClientId in core/block-editor store.
 */
const getNextBlockClientId = getBlockEditorSelector('getNextBlockClientId');

/**
 * @see getSelectedBlocksInitialCaretPosition in core/block-editor store.
 */
const getSelectedBlocksInitialCaretPosition = getBlockEditorSelector('getSelectedBlocksInitialCaretPosition');

/**
 * @see getMultiSelectedBlockClientIds in core/block-editor store.
 */
const getMultiSelectedBlockClientIds = getBlockEditorSelector('getMultiSelectedBlockClientIds');

/**
 * @see getMultiSelectedBlocks in core/block-editor store.
 */
const getMultiSelectedBlocks = getBlockEditorSelector('getMultiSelectedBlocks');

/**
 * @see getFirstMultiSelectedBlockClientId in core/block-editor store.
 */
const getFirstMultiSelectedBlockClientId = getBlockEditorSelector('getFirstMultiSelectedBlockClientId');

/**
 * @see getLastMultiSelectedBlockClientId in core/block-editor store.
 */
const getLastMultiSelectedBlockClientId = getBlockEditorSelector('getLastMultiSelectedBlockClientId');

/**
 * @see isFirstMultiSelectedBlock in core/block-editor store.
 */
const isFirstMultiSelectedBlock = getBlockEditorSelector('isFirstMultiSelectedBlock');

/**
 * @see isBlockMultiSelected in core/block-editor store.
 */
const isBlockMultiSelected = getBlockEditorSelector('isBlockMultiSelected');

/**
 * @see isAncestorMultiSelected in core/block-editor store.
 */
const isAncestorMultiSelected = getBlockEditorSelector('isAncestorMultiSelected');

/**
 * @see getMultiSelectedBlocksStartClientId in core/block-editor store.
 */
const getMultiSelectedBlocksStartClientId = getBlockEditorSelector('getMultiSelectedBlocksStartClientId');

/**
 * @see getMultiSelectedBlocksEndClientId in core/block-editor store.
 */
const getMultiSelectedBlocksEndClientId = getBlockEditorSelector('getMultiSelectedBlocksEndClientId');

/**
 * @see getBlockOrder in core/block-editor store.
 */
const getBlockOrder = getBlockEditorSelector('getBlockOrder');

/**
 * @see getBlockIndex in core/block-editor store.
 */
const getBlockIndex = getBlockEditorSelector('getBlockIndex');

/**
 * @see isBlockSelected in core/block-editor store.
 */
const isBlockSelected = getBlockEditorSelector('isBlockSelected');

/**
 * @see hasSelectedInnerBlock in core/block-editor store.
 */
const hasSelectedInnerBlock = getBlockEditorSelector('hasSelectedInnerBlock');

/**
 * @see isBlockWithinSelection in core/block-editor store.
 */
const isBlockWithinSelection = getBlockEditorSelector('isBlockWithinSelection');

/**
 * @see hasMultiSelection in core/block-editor store.
 */
const hasMultiSelection = getBlockEditorSelector('hasMultiSelection');

/**
 * @see isMultiSelecting in core/block-editor store.
 */
const isMultiSelecting = getBlockEditorSelector('isMultiSelecting');

/**
 * @see isSelectionEnabled in core/block-editor store.
 */
const isSelectionEnabled = getBlockEditorSelector('isSelectionEnabled');

/**
 * @see getBlockMode in core/block-editor store.
 */
const getBlockMode = getBlockEditorSelector('getBlockMode');

/**
 * @see isTyping in core/block-editor store.
 */
const isTyping = getBlockEditorSelector('isTyping');

/**
 * @see isCaretWithinFormattedText in core/block-editor store.
 */
const isCaretWithinFormattedText = getBlockEditorSelector('isCaretWithinFormattedText');

/**
 * @see getBlockInsertionPoint in core/block-editor store.
 */
const getBlockInsertionPoint = getBlockEditorSelector('getBlockInsertionPoint');

/**
 * @see isBlockInsertionPointVisible in core/block-editor store.
 */
const isBlockInsertionPointVisible = getBlockEditorSelector('isBlockInsertionPointVisible');

/**
 * @see isValidTemplate in core/block-editor store.
 */
const isValidTemplate = getBlockEditorSelector('isValidTemplate');

/**
 * @see getTemplate in core/block-editor store.
 */
const getTemplate = getBlockEditorSelector('getTemplate');

/**
 * @see getTemplateLock in core/block-editor store.
 */
const getTemplateLock = getBlockEditorSelector('getTemplateLock');

/**
 * @see canInsertBlockType in core/block-editor store.
 */
const canInsertBlockType = getBlockEditorSelector('canInsertBlockType');

/**
 * @see getInserterItems in core/block-editor store.
 */
const getInserterItems = getBlockEditorSelector('getInserterItems');

/**
 * @see hasInserterItems in core/block-editor store.
 */
const hasInserterItems = getBlockEditorSelector('hasInserterItems');

/**
 * @see getBlockListSettings in core/block-editor store.
 */
const getBlockListSettings = getBlockEditorSelector('getBlockListSettings');

/**
 * Returns the default template types.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The template types.
 */
function __experimentalGetDefaultTemplateTypes(state) {
  return getEditorSettings(state)?.defaultTemplateTypes;
}

/**
 * Returns the default template part areas.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} The template part areas.
 */
const __experimentalGetDefaultTemplatePartAreas = rememo(state => {
  const areas = getEditorSettings(state)?.defaultTemplatePartAreas || [];
  return areas?.map(item => {
    return {
      ...item,
      icon: getTemplatePartIcon(item.icon)
    };
  });
}, state => [getEditorSettings(state)?.defaultTemplatePartAreas]);

/**
 * Returns a default template type searched by slug.
 *
 * @param {Object} state Global application state.
 * @param {string} slug  The template type slug.
 *
 * @return {Object} The template type.
 */
const __experimentalGetDefaultTemplateType = rememo((state, slug) => {
  var _Object$values$find;
  const templateTypes = __experimentalGetDefaultTemplateTypes(state);
  if (!templateTypes) {
    return EMPTY_OBJECT;
  }
  return (_Object$values$find = Object.values(templateTypes).find(type => type.slug === slug)) !== null && _Object$values$find !== void 0 ? _Object$values$find : EMPTY_OBJECT;
}, (state, slug) => [__experimentalGetDefaultTemplateTypes(state), slug]);

/**
 * Given a template entity, return information about it which is ready to be
 * rendered, such as the title, description, and icon.
 *
 * @param {Object} state    Global application state.
 * @param {Object} template The template for which we need information.
 * @return {Object} Information about the template, including title, description, and icon.
 */
function __experimentalGetTemplateInfo(state, template) {
  if (!template) {
    return EMPTY_OBJECT;
  }
  const {
    description,
    slug,
    title,
    area
  } = template;
  const {
    title: defaultTitle,
    description: defaultDescription
  } = __experimentalGetDefaultTemplateType(state, slug);
  const templateTitle = typeof title === 'string' ? title : title?.rendered;
  const templateDescription = typeof description === 'string' ? description : description?.raw;
  const templateIcon = __experimentalGetDefaultTemplatePartAreas(state).find(item => area === item.area)?.icon || library_layout;
  return {
    title: templateTitle && templateTitle !== slug ? templateTitle : defaultTitle || slug,
    description: templateDescription || defaultDescription,
    icon: templateIcon
  };
}

/**
 * Returns a post type label depending on the current post.
 *
 * @param {Object} state Global application state.
 *
 * @return {string|undefined} The post type label if available, otherwise undefined.
 */
const getPostTypeLabel = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const currentPostType = getCurrentPostType(state);
  const postType = select(external_wp_coreData_namespaceObject.store).getPostType(currentPostType);
  // Disable reason: Post type labels object is shaped like this.
  // eslint-disable-next-line camelcase
  return postType?.labels?.singular_name;
});

;// CONCATENATED MODULE: external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
;// CONCATENATED MODULE: external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/local-autosave.js
/**
 * Function returning a sessionStorage key to set or retrieve a given post's
 * automatic session backup.
 *
 * Keys are crucially prefixed with 'wp-autosave-' so that wp-login.php's
 * `loggedout` handler can clear sessionStorage of any user-private content.
 *
 * @see https://github.com/WordPress/wordpress-develop/blob/6dad32d2aed47e6c0cf2aee8410645f6d7aba6bd/src/wp-login.php#L103
 *
 * @param {string}  postId    Post ID.
 * @param {boolean} isPostNew Whether post new.
 *
 * @return {string} sessionStorage key
 */
function postKey(postId, isPostNew) {
  return `wp-autosave-block-editor-post-${isPostNew ? 'auto-draft' : postId}`;
}
function localAutosaveGet(postId, isPostNew) {
  return window.sessionStorage.getItem(postKey(postId, isPostNew));
}
function localAutosaveSet(postId, isPostNew, title, content, excerpt) {
  window.sessionStorage.setItem(postKey(postId, isPostNew), JSON.stringify({
    post_title: title,
    content,
    excerpt
  }));
}
function localAutosaveClear(postId, isPostNew) {
  window.sessionStorage.removeItem(postKey(postId, isPostNew));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/utils/notice-builder.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
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
  var _postType$viewable;
  const {
    previousPost,
    post,
    postType
  } = data;
  // Autosaves are neither shown a notice nor redirected.
  if (data.options?.isAutosave) {
    return [];
  }
  const publishStatus = ['publish', 'private', 'future'];
  const isPublished = publishStatus.includes(previousPost.status);
  const willPublish = publishStatus.includes(post.status);
  const willTrash = post.status === 'trash' && previousPost.status !== 'trash';
  let noticeMessage;
  let shouldShowLink = (_postType$viewable = postType?.viewable) !== null && _postType$viewable !== void 0 ? _postType$viewable : false;
  let isDraft;

  // Always should a notice, which will be spoken for accessibility.
  if (willTrash) {
    noticeMessage = postType.labels.item_trashed;
    shouldShowLink = false;
  } else if (!isPublished && !willPublish) {
    // If saving a non-published post, don't show notice.
    noticeMessage = (0,external_wp_i18n_namespaceObject.__)('Draft saved.');
    isDraft = true;
  } else if (isPublished && !willPublish) {
    // If undoing publish status, show specific notice.
    noticeMessage = postType.labels.item_reverted_to_draft;
    shouldShowLink = false;
  } else if (!isPublished && willPublish) {
    // If publishing or scheduling a post, show the corresponding
    // publish message.
    noticeMessage = {
      publish: postType.labels.item_published,
      private: postType.labels.item_published_privately,
      future: postType.labels.item_scheduled
    }[post.status];
  } else {
    // Generic fallback notice.
    noticeMessage = postType.labels.item_updated;
  }
  const actions = [];
  if (shouldShowLink) {
    actions.push({
      label: isDraft ? (0,external_wp_i18n_namespaceObject.__)('View Preview') : postType.labels.view_item,
      url: post.link
    });
  }
  return [noticeMessage, {
    id: SAVE_POST_NOTICE_ID,
    type: 'snackbar',
    actions
  }];
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
  const {
    post,
    edits,
    error
  } = data;
  if (error && 'rest_autosave_no_changes' === error.code) {
    // Autosave requested a new autosave, but there were no changes. This shouldn't
    // result in an error notice for the user.
    return [];
  }
  const publishStatus = ['publish', 'private', 'future'];
  const isPublished = publishStatus.indexOf(post.status) !== -1;
  // If the post was being published, we show the corresponding publish error message
  // Unless we publish an "updating failed" message.
  const messages = {
    publish: (0,external_wp_i18n_namespaceObject.__)('Publishing failed.'),
    private: (0,external_wp_i18n_namespaceObject.__)('Publishing failed.'),
    future: (0,external_wp_i18n_namespaceObject.__)('Scheduling failed.')
  };
  let noticeMessage = !isPublished && publishStatus.indexOf(edits.status) !== -1 ? messages[edits.status] : (0,external_wp_i18n_namespaceObject.__)('Updating failed.');

  // Check if message string contains HTML. Notice text is currently only
  // supported as plaintext, and stripping the tags may muddle the meaning.
  if (error.message && !/<\/?[^>]*>/.test(error.message)) {
    noticeMessage = [noticeMessage, error.message].join(' ');
  }
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
  return [data.error.message && data.error.code !== 'unknown_error' ? data.error.message : (0,external_wp_i18n_namespaceObject.__)('Trashing failed'), {
    id: TRASH_POST_NOTICE_ID
  }];
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/actions.js
/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */




/**
 * Returns an action generator used in signalling that editor has initialized with
 * the specified post object and editor settings.
 *
 * @param {Object} post     Post object.
 * @param {Object} edits    Initial edited attributes object.
 * @param {Array?} template Block Template.
 */
const setupEditor = (post, edits, template) => ({
  dispatch
}) => {
  dispatch.setEditedPost(post.type, post.id);
  // Apply a template for new posts only, if exists.
  const isNewPost = post.status === 'auto-draft';
  if (isNewPost && template) {
    // In order to ensure maximum of a single parse during setup, edits are
    // included as part of editor setup action. Assume edited content as
    // canonical if provided, falling back to post.
    let content;
    if ('content' in edits) {
      content = edits.content;
    } else {
      content = post.content.raw;
    }
    let blocks = (0,external_wp_blocks_namespaceObject.parse)(content);
    blocks = (0,external_wp_blocks_namespaceObject.synchronizeBlocksWithTemplate)(blocks, template);
    dispatch.resetEditorBlocks(blocks, {
      __unstableShouldCreateUndoLevel: false
    });
  }
  if (edits && Object.values(edits).some(([key, edit]) => {
    var _post$key$raw;
    return edit !== ((_post$key$raw = post[key]?.raw) !== null && _post$key$raw !== void 0 ? _post$key$raw : post[key]);
  })) {
    dispatch.editPost(edits);
  }
};

/**
 * Returns an action object signalling that the editor is being destroyed and
 * that any necessary state or side-effect cleanup should occur.
 *
 * @deprecated
 *
 * @return {Object} Action object.
 */
function __experimentalTearDownEditor() {
  external_wp_deprecated_default()("wp.data.dispatch( 'core/editor' ).__experimentalTearDownEditor", {
    since: '6.5'
  });
  return {
    type: 'DO_NOTHING'
  };
}

/**
 * Returns an action object used in signalling that the latest version of the
 * post has been received, either by initialization or save.
 *
 * @deprecated Since WordPress 6.0.
 */
function resetPost() {
  external_wp_deprecated_default()("wp.data.dispatch( 'core/editor' ).resetPost", {
    since: '6.0',
    version: '6.3',
    alternative: 'Initialize the editor with the setupEditorState action'
  });
  return {
    type: 'DO_NOTHING'
  };
}

/**
 * Returns an action object used in signalling that a patch of updates for the
 * latest version of the post have been received.
 *
 * @return {Object} Action object.
 * @deprecated since Gutenberg 9.7.0.
 */
function updatePost() {
  external_wp_deprecated_default()("wp.data.dispatch( 'core/editor' ).updatePost", {
    since: '5.7',
    alternative: 'Use the core entities store instead'
  });
  return {
    type: 'DO_NOTHING'
  };
}

/**
 * Setup the editor state.
 *
 * @deprecated
 *
 * @param {Object} post Post object.
 */
function setupEditorState(post) {
  external_wp_deprecated_default()("wp.data.dispatch( 'core/editor' ).setupEditorState", {
    since: '6.5',
    alternative: "wp.data.dispatch( 'core/editor' ).setEditedPost"
  });
  return setEditedPost(post.type, post.id);
}

/**
 * Returns an action that sets the current post Type and post ID.
 *
 * @param {string} postType Post Type.
 * @param {string} postId   Post ID.
 *
 * @return {Object} Action object.
 */
function setEditedPost(postType, postId) {
  return {
    type: 'SET_EDITED_POST',
    postType,
    postId
  };
}

/**
 * Returns an action object used in signalling that attributes of the post have
 * been edited.
 *
 * @param {Object} edits   Post attributes to edit.
 * @param {Object} options Options for the edit.
 */
const editPost = (edits, options) => ({
  select,
  registry
}) => {
  const {
    id,
    type
  } = select.getCurrentPost();
  registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord('postType', type, id, edits, options);
};

/**
 * Action for saving the current post in the editor.
 *
 * @param {Object} options
 */
const savePost = (options = {}) => async ({
  select,
  dispatch,
  registry
}) => {
  if (!select.isEditedPostSaveable()) {
    return;
  }
  const content = select.getEditedPostContent();
  if (!options.isAutosave) {
    dispatch.editPost({
      content
    }, {
      undoIgnore: true
    });
  }
  const previousRecord = select.getCurrentPost();
  const edits = {
    id: previousRecord.id,
    ...registry.select(external_wp_coreData_namespaceObject.store).getEntityRecordNonTransientEdits('postType', previousRecord.type, previousRecord.id),
    content
  };
  dispatch({
    type: 'REQUEST_POST_UPDATE_START',
    options
  });
  await registry.dispatch(external_wp_coreData_namespaceObject.store).saveEntityRecord('postType', previousRecord.type, edits, options);
  let error = registry.select(external_wp_coreData_namespaceObject.store).getLastEntitySaveError('postType', previousRecord.type, previousRecord.id);
  if (!error) {
    await (0,external_wp_hooks_namespaceObject.applyFilters)('editor.__unstableSavePost', Promise.resolve(), options).catch(err => {
      error = err;
    });
  }
  dispatch({
    type: 'REQUEST_POST_UPDATE_FINISH',
    options
  });
  if (error) {
    const args = getNotificationArgumentsForSaveFail({
      post: previousRecord,
      edits,
      error
    });
    if (args.length) {
      registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(...args);
    }
  } else {
    const updatedRecord = select.getCurrentPost();
    const args = getNotificationArgumentsForSaveSuccess({
      previousPost: previousRecord,
      post: updatedRecord,
      postType: await registry.resolveSelect(external_wp_coreData_namespaceObject.store).getPostType(updatedRecord.type),
      options
    });
    if (args.length) {
      registry.dispatch(external_wp_notices_namespaceObject.store).createSuccessNotice(...args);
    }
    // Make sure that any edits after saving create an undo level and are
    // considered for change detection.
    if (!options.isAutosave) {
      registry.dispatch(external_wp_blockEditor_namespaceObject.store).__unstableMarkLastChangeAsPersistent();
    }
  }
};

/**
 * Action for refreshing the current post.
 *
 * @deprecated Since WordPress 6.0.
 */
function refreshPost() {
  external_wp_deprecated_default()("wp.data.dispatch( 'core/editor' ).refreshPost", {
    since: '6.0',
    version: '6.3',
    alternative: 'Use the core entities store instead'
  });
  return {
    type: 'DO_NOTHING'
  };
}

/**
 * Action for trashing the current post in the editor.
 */
const trashPost = () => async ({
  select,
  dispatch,
  registry
}) => {
  const postTypeSlug = select.getCurrentPostType();
  const postType = await registry.resolveSelect(external_wp_coreData_namespaceObject.store).getPostType(postTypeSlug);
  registry.dispatch(external_wp_notices_namespaceObject.store).removeNotice(TRASH_POST_NOTICE_ID);
  const {
    rest_base: restBase,
    rest_namespace: restNamespace = 'wp/v2'
  } = postType;
  dispatch({
    type: 'REQUEST_POST_DELETE_START'
  });
  try {
    const post = select.getCurrentPost();
    await external_wp_apiFetch_default()({
      path: `/${restNamespace}/${restBase}/${post.id}`,
      method: 'DELETE'
    });
    await dispatch.savePost();
  } catch (error) {
    registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(...getNotificationArgumentsForTrashFail({
      error
    }));
  }
  dispatch({
    type: 'REQUEST_POST_DELETE_FINISH'
  });
};

/**
 * Action that autosaves the current post.  This
 * includes server-side autosaving (default) and client-side (a.k.a. local)
 * autosaving (e.g. on the Web, the post might be committed to Session
 * Storage).
 *
 * @param {Object?} options Extra flags to identify the autosave.
 */
const autosave = ({
  local = false,
  ...options
} = {}) => async ({
  select,
  dispatch
}) => {
  const post = select.getCurrentPost();

  // Currently template autosaving is not supported.
  if (post.type === 'wp_template') {
    return;
  }
  if (local) {
    const isPostNew = select.isEditedPostNew();
    const title = select.getEditedPostAttribute('title');
    const content = select.getEditedPostAttribute('content');
    const excerpt = select.getEditedPostAttribute('excerpt');
    localAutosaveSet(post.id, isPostNew, title, content, excerpt);
  } else {
    await dispatch.savePost({
      isAutosave: true,
      ...options
    });
  }
};
const __unstableSaveForPreview = ({
  forceIsAutosaveable
} = {}) => async ({
  select,
  dispatch
}) => {
  if ((forceIsAutosaveable || select.isEditedPostAutosaveable()) && !select.isPostLocked()) {
    const isDraft = ['draft', 'auto-draft'].includes(select.getEditedPostAttribute('status'));
    if (isDraft) {
      await dispatch.savePost({
        isPreview: true
      });
    } else {
      await dispatch.autosave({
        isPreview: true
      });
    }
  }
  return select.getEditedPostPreviewLink();
};

/**
 * Action that restores last popped state in undo history.
 */
const redo = () => ({
  registry
}) => {
  registry.dispatch(external_wp_coreData_namespaceObject.store).redo();
};

/**
 * Action that pops a record from undo history and undoes the edit.
 */
const undo = () => ({
  registry
}) => {
  registry.dispatch(external_wp_coreData_namespaceObject.store).undo();
};

/**
 * Action that creates an undo history record.
 *
 * @deprecated Since WordPress 6.0
 */
function createUndoLevel() {
  external_wp_deprecated_default()("wp.data.dispatch( 'core/editor' ).createUndoLevel", {
    since: '6.0',
    version: '6.3',
    alternative: 'Use the core entities store instead'
  });
  return {
    type: 'DO_NOTHING'
  };
}

/**
 * Action that locks the editor.
 *
 * @param {Object} lock Details about the post lock status, user, and nonce.
 * @return {Object} Action object.
 */
function updatePostLock(lock) {
  return {
    type: 'UPDATE_POST_LOCK',
    lock
  };
}

/**
 * Enable the publish sidebar.
 */
const enablePublishSidebar = () => ({
  registry
}) => {
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'isPublishSidebarEnabled', true);
};

/**
 * Disables the publish sidebar.
 */
const disablePublishSidebar = () => ({
  registry
}) => {
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core/edit-post', 'isPublishSidebarEnabled', false);
};

/**
 * Action that locks post saving.
 *
 * @param {string} lockName The lock name.
 *
 * @example
 * ```
 * const { subscribe } = wp.data;
 *
 * const initialPostStatus = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'status' );
 *
 * // Only allow publishing posts that are set to a future date.
 * if ( 'publish' !== initialPostStatus ) {
 *
 * 	// Track locking.
 * 	let locked = false;
 *
 * 	// Watch for the publish event.
 * 	let unssubscribe = subscribe( () => {
 * 		const currentPostStatus = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'status' );
 * 		if ( 'publish' !== currentPostStatus ) {
 *
 * 			// Compare the post date to the current date, lock the post if the date isn't in the future.
 * 			const postDate = new Date( wp.data.select( 'core/editor' ).getEditedPostAttribute( 'date' ) );
 * 			const currentDate = new Date();
 * 			if ( postDate.getTime() <= currentDate.getTime() ) {
 * 				if ( ! locked ) {
 * 					locked = true;
 * 					wp.data.dispatch( 'core/editor' ).lockPostSaving( 'futurelock' );
 * 				}
 * 			} else {
 * 				if ( locked ) {
 * 					locked = false;
 * 					wp.data.dispatch( 'core/editor' ).unlockPostSaving( 'futurelock' );
 * 				}
 * 			}
 * 		}
 * 	} );
 * }
 * ```
 *
 * @return {Object} Action object
 */
function lockPostSaving(lockName) {
  return {
    type: 'LOCK_POST_SAVING',
    lockName
  };
}

/**
 * Action that unlocks post saving.
 *
 * @param {string} lockName The lock name.
 *
 * @example
 * ```
 * // Unlock post saving with the lock key `mylock`:
 * wp.data.dispatch( 'core/editor' ).unlockPostSaving( 'mylock' );
 * ```
 *
 * @return {Object} Action object
 */
function unlockPostSaving(lockName) {
  return {
    type: 'UNLOCK_POST_SAVING',
    lockName
  };
}

/**
 * Action that locks post autosaving.
 *
 * @param {string} lockName The lock name.
 *
 * @example
 * ```
 * // Lock post autosaving with the lock key `mylock`:
 * wp.data.dispatch( 'core/editor' ).lockPostAutosaving( 'mylock' );
 * ```
 *
 * @return {Object} Action object
 */
function lockPostAutosaving(lockName) {
  return {
    type: 'LOCK_POST_AUTOSAVING',
    lockName
  };
}

/**
 * Action that unlocks post autosaving.
 *
 * @param {string} lockName The lock name.
 *
 * @example
 * ```
 * // Unlock post saving with the lock key `mylock`:
 * wp.data.dispatch( 'core/editor' ).unlockPostAutosaving( 'mylock' );
 * ```
 *
 * @return {Object} Action object
 */
function unlockPostAutosaving(lockName) {
  return {
    type: 'UNLOCK_POST_AUTOSAVING',
    lockName
  };
}

/**
 * Returns an action object used to signal that the blocks have been updated.
 *
 * @param {Array}   blocks  Block Array.
 * @param {?Object} options Optional options.
 */
const resetEditorBlocks = (blocks, options = {}) => ({
  select,
  dispatch,
  registry
}) => {
  const {
    __unstableShouldCreateUndoLevel,
    selection
  } = options;
  const edits = {
    blocks,
    selection
  };
  if (__unstableShouldCreateUndoLevel !== false) {
    const {
      id,
      type
    } = select.getCurrentPost();
    const noChange = registry.select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord('postType', type, id).blocks === edits.blocks;
    if (noChange) {
      registry.dispatch(external_wp_coreData_namespaceObject.store).__unstableCreateUndoLevel('postType', type, id);
      return;
    }

    // We create a new function here on every persistent edit
    // to make sure the edit makes the post dirty and creates
    // a new undo level.
    edits.content = ({
      blocks: blocksForSerialization = []
    }) => (0,external_wp_blocks_namespaceObject.__unstableSerializeAndClean)(blocksForSerialization);
  }
  dispatch.editPost(edits);
};

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
    settings
  };
}

/**
 * Returns an action used to set the rendering mode of the post editor. We support multiple rendering modes:
 *
 * -   `all`: This is the default mode. It renders the post editor with all the features available. If a template is provided, it's preferred over the post.
 * -   `post-only`: This mode extracts the post blocks from the template and renders only those. The idea is to allow the user to edit the post/page in isolation without the wrapping template.
 * -   `template-locked`: This mode renders both the template and the post blocks but the template blocks are locked and can't be edited. The post blocks are editable.
 *
 * @param {string} mode Mode (one of 'post-only' or 'template-locked').
 */
const setRenderingMode = mode => ({
  dispatch,
  registry,
  select
}) => {
  if (select.__unstableIsEditorReady()) {
    // We clear the block selection but we also need to clear the selection from the core store.
    registry.dispatch(external_wp_blockEditor_namespaceObject.store).clearSelectedBlock();
    dispatch.editPost({
      selection: undefined
    }, {
      undoIgnore: true
    });
  }
  dispatch({
    type: 'SET_RENDERING_MODE',
    mode
  });
};

/**
 * Action that changes the width of the editing canvas.
 *
 * @param {string} deviceType
 *
 * @return {Object} Action object.
 */
function setDeviceType(deviceType) {
  return {
    type: 'SET_DEVICE_TYPE',
    deviceType
  };
}

/**
 * Returns an action object used to enable or disable a panel in the editor.
 *
 * @param {string} panelName A string that identifies the panel to enable or disable.
 *
 * @return {Object} Action object.
 */
const toggleEditorPanelEnabled = panelName => ({
  registry
}) => {
  var _registry$select$get;
  const inactivePanels = (_registry$select$get = registry.select(external_wp_preferences_namespaceObject.store).get('core', 'inactivePanels')) !== null && _registry$select$get !== void 0 ? _registry$select$get : [];
  const isPanelInactive = !!inactivePanels?.includes(panelName);

  // If the panel is inactive, remove it to enable it, else add it to
  // make it inactive.
  let updatedInactivePanels;
  if (isPanelInactive) {
    updatedInactivePanels = inactivePanels.filter(invactivePanelName => invactivePanelName !== panelName);
  } else {
    updatedInactivePanels = [...inactivePanels, panelName];
  }
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core', 'inactivePanels', updatedInactivePanels);
};

/**
 * Opens a closed panel and closes an open panel.
 *
 * @param {string} panelName A string that identifies the panel to open or close.
 */
const toggleEditorPanelOpened = panelName => ({
  registry
}) => {
  var _registry$select$get2;
  const openPanels = (_registry$select$get2 = registry.select(external_wp_preferences_namespaceObject.store).get('core', 'openPanels')) !== null && _registry$select$get2 !== void 0 ? _registry$select$get2 : [];
  const isPanelOpen = !!openPanels?.includes(panelName);

  // If the panel is open, remove it to close it, else add it to
  // make it open.
  let updatedOpenPanels;
  if (isPanelOpen) {
    updatedOpenPanels = openPanels.filter(openPanelName => openPanelName !== panelName);
  } else {
    updatedOpenPanels = [...openPanels, panelName];
  }
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core', 'openPanels', updatedOpenPanels);
};

/**
 * Returns an action object used to remove a panel from the editor.
 *
 * @param {string} panelName A string that identifies the panel to remove.
 *
 * @return {Object} Action object.
 */
function removeEditorPanel(panelName) {
  return {
    type: 'REMOVE_PANEL',
    panelName
  };
}

/**
 * Returns an action object used to open/close the inserter.
 *
 * @param {boolean|Object} value                Whether the inserter should be
 *                                              opened (true) or closed (false).
 *                                              To specify an insertion point,
 *                                              use an object.
 * @param {string}         value.rootClientId   The root client ID to insert at.
 * @param {number}         value.insertionIndex The index to insert at.
 *
 * @return {Object} Action object.
 */
function setIsInserterOpened(value) {
  return {
    type: 'SET_IS_INSERTER_OPENED',
    value
  };
}

/**
 * Returns an action object used to open/close the list view.
 *
 * @param {boolean} isOpen A boolean representing whether the list view should be opened or closed.
 * @return {Object} Action object.
 */
function setIsListViewOpened(isOpen) {
  return {
    type: 'SET_IS_LIST_VIEW_OPENED',
    isOpen
  };
}

/**
 * Backward compatibility
 */

const getBlockEditorAction = name => (...args) => ({
  registry
}) => {
  external_wp_deprecated_default()("`wp.data.dispatch( 'core/editor' )." + name + '`', {
    since: '5.3',
    alternative: "`wp.data.dispatch( 'core/block-editor' )." + name + '`',
    version: '6.2'
  });
  registry.dispatch(external_wp_blockEditor_namespaceObject.store)[name](...args);
};

/**
 * @see resetBlocks in core/block-editor store.
 */
const resetBlocks = getBlockEditorAction('resetBlocks');

/**
 * @see receiveBlocks in core/block-editor store.
 */
const receiveBlocks = getBlockEditorAction('receiveBlocks');

/**
 * @see updateBlock in core/block-editor store.
 */
const updateBlock = getBlockEditorAction('updateBlock');

/**
 * @see updateBlockAttributes in core/block-editor store.
 */
const updateBlockAttributes = getBlockEditorAction('updateBlockAttributes');

/**
 * @see selectBlock in core/block-editor store.
 */
const selectBlock = getBlockEditorAction('selectBlock');

/**
 * @see startMultiSelect in core/block-editor store.
 */
const startMultiSelect = getBlockEditorAction('startMultiSelect');

/**
 * @see stopMultiSelect in core/block-editor store.
 */
const stopMultiSelect = getBlockEditorAction('stopMultiSelect');

/**
 * @see multiSelect in core/block-editor store.
 */
const multiSelect = getBlockEditorAction('multiSelect');

/**
 * @see clearSelectedBlock in core/block-editor store.
 */
const clearSelectedBlock = getBlockEditorAction('clearSelectedBlock');

/**
 * @see toggleSelection in core/block-editor store.
 */
const toggleSelection = getBlockEditorAction('toggleSelection');

/**
 * @see replaceBlocks in core/block-editor store.
 */
const replaceBlocks = getBlockEditorAction('replaceBlocks');

/**
 * @see replaceBlock in core/block-editor store.
 */
const replaceBlock = getBlockEditorAction('replaceBlock');

/**
 * @see moveBlocksDown in core/block-editor store.
 */
const moveBlocksDown = getBlockEditorAction('moveBlocksDown');

/**
 * @see moveBlocksUp in core/block-editor store.
 */
const moveBlocksUp = getBlockEditorAction('moveBlocksUp');

/**
 * @see moveBlockToPosition in core/block-editor store.
 */
const moveBlockToPosition = getBlockEditorAction('moveBlockToPosition');

/**
 * @see insertBlock in core/block-editor store.
 */
const insertBlock = getBlockEditorAction('insertBlock');

/**
 * @see insertBlocks in core/block-editor store.
 */
const insertBlocks = getBlockEditorAction('insertBlocks');

/**
 * @see showInsertionPoint in core/block-editor store.
 */
const showInsertionPoint = getBlockEditorAction('showInsertionPoint');

/**
 * @see hideInsertionPoint in core/block-editor store.
 */
const hideInsertionPoint = getBlockEditorAction('hideInsertionPoint');

/**
 * @see setTemplateValidity in core/block-editor store.
 */
const setTemplateValidity = getBlockEditorAction('setTemplateValidity');

/**
 * @see synchronizeTemplate in core/block-editor store.
 */
const synchronizeTemplate = getBlockEditorAction('synchronizeTemplate');

/**
 * @see mergeBlocks in core/block-editor store.
 */
const mergeBlocks = getBlockEditorAction('mergeBlocks');

/**
 * @see removeBlocks in core/block-editor store.
 */
const removeBlocks = getBlockEditorAction('removeBlocks');

/**
 * @see removeBlock in core/block-editor store.
 */
const removeBlock = getBlockEditorAction('removeBlock');

/**
 * @see toggleBlockMode in core/block-editor store.
 */
const toggleBlockMode = getBlockEditorAction('toggleBlockMode');

/**
 * @see startTyping in core/block-editor store.
 */
const startTyping = getBlockEditorAction('startTyping');

/**
 * @see stopTyping in core/block-editor store.
 */
const stopTyping = getBlockEditorAction('stopTyping');

/**
 * @see enterFormattedText in core/block-editor store.
 */
const enterFormattedText = getBlockEditorAction('enterFormattedText');

/**
 * @see exitFormattedText in core/block-editor store.
 */
const exitFormattedText = getBlockEditorAction('exitFormattedText');

/**
 * @see insertDefaultBlock in core/block-editor store.
 */
const insertDefaultBlock = getBlockEditorAction('insertDefaultBlock');

/**
 * @see updateBlockListSettings in core/block-editor store.
 */
const updateBlockListSettings = getBlockEditorAction('updateBlockListSettings');

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/private-actions.js
/**
 * WordPress dependencies
 */





/**
 * Returns an action object used to set which template is currently being used/edited.
 *
 * @param {string} id Template Id.
 *
 * @return {Object} Action object.
 */
function setCurrentTemplateId(id) {
  return {
    type: 'SET_CURRENT_TEMPLATE_ID',
    id
  };
}

/**
 * Create a block based template.
 *
 * @param {Object?} template Template to create and assign.
 */
const createTemplate = template => async ({
  select,
  dispatch,
  registry
}) => {
  const savedTemplate = await registry.dispatch(external_wp_coreData_namespaceObject.store).saveEntityRecord('postType', 'wp_template', template);
  registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord('postType', select.getCurrentPostType(), select.getCurrentPostId(), {
    template: savedTemplate.slug
  });
  registry.dispatch(external_wp_notices_namespaceObject.store).createSuccessNotice((0,external_wp_i18n_namespaceObject.__)("Custom template created. You're in template mode now."), {
    type: 'snackbar',
    actions: [{
      label: (0,external_wp_i18n_namespaceObject.__)('Go back'),
      onClick: () => dispatch.setRenderingMode(select.getEditorSettings().defaultRenderingMode)
    }]
  });
  return savedTemplate;
};

/**
 * Update the provided block types to be visible.
 *
 * @param {string[]} blockNames Names of block types to show.
 */
const showBlockTypes = blockNames => ({
  registry
}) => {
  var _registry$select$get;
  const existingBlockNames = (_registry$select$get = registry.select(external_wp_preferences_namespaceObject.store).get('core', 'hiddenBlockTypes')) !== null && _registry$select$get !== void 0 ? _registry$select$get : [];
  const newBlockNames = existingBlockNames.filter(type => !(Array.isArray(blockNames) ? blockNames : [blockNames]).includes(type));
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core', 'hiddenBlockTypes', newBlockNames);
};

/**
 * Update the provided block types to be hidden.
 *
 * @param {string[]} blockNames Names of block types to hide.
 */
const hideBlockTypes = blockNames => ({
  registry
}) => {
  var _registry$select$get2;
  const existingBlockNames = (_registry$select$get2 = registry.select(external_wp_preferences_namespaceObject.store).get('core', 'hiddenBlockTypes')) !== null && _registry$select$get2 !== void 0 ? _registry$select$get2 : [];
  const mergedBlockNames = new Set([...existingBlockNames, ...(Array.isArray(blockNames) ? blockNames : [blockNames])]);
  registry.dispatch(external_wp_preferences_namespaceObject.store).set('core', 'hiddenBlockTypes', [...mergedBlockNames]);
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/private-selectors.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

const EMPTY_INSERTION_POINT = {
  rootClientId: undefined,
  insertionIndex: undefined,
  filterValue: undefined
};

/**
 * Get the insertion point for the inserter.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The root client ID, index to insert at and starting filter value.
 */
const getInsertionPoint = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  if (typeof state.blockInserterPanel === 'object') {
    return state.blockInserterPanel;
  }
  if (getRenderingMode(state) === 'template-locked') {
    const [postContentClientId] = select(external_wp_blockEditor_namespaceObject.store).getBlocksByName('core/post-content');
    if (postContentClientId) {
      return {
        rootClientId: postContentClientId,
        insertionIndex: undefined,
        filterValue: undefined
      };
    }
  }
  return EMPTY_INSERTION_POINT;
});
function getListViewToggleRef(state) {
  return state.listViewToggleRef;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */








/**
 * Post editor data store configuration.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#registerStore
 *
 * @type {Object}
 */
const storeConfig = {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
};

/**
 * Store definition for the editor namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */
const store_store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  ...storeConfig
});
(0,external_wp_data_namespaceObject.register)(store_store);
unlock(store_store).registerPrivateActions(private_actions_namespaceObject);
unlock(store_store).registerPrivateSelectors(private_selectors_namespaceObject);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/bindings/post-meta.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

/* harmony default export */ const post_meta = ({
  name: 'core/post-meta',
  label: (0,external_wp_i18n_namespaceObject._x)('Post Meta', 'block bindings source'),
  useSource(props, sourceAttributes) {
    const {
      getCurrentPostType
    } = (0,external_wp_data_namespaceObject.useSelect)(store_store);
    const {
      context
    } = props;
    const {
      key: metaKey
    } = sourceAttributes;
    const postType = context.postType ? context.postType : getCurrentPostType();
    const [meta, setMeta] = (0,external_wp_coreData_namespaceObject.useEntityProp)('postType', context.postType, 'meta', context.postId);
    if (postType === 'wp_template') {
      return {
        placeholder: metaKey
      };
    }
    const metaValue = meta[metaKey];
    const updateMetaValue = newValue => {
      setMeta({
        ...meta,
        [metaKey]: newValue
      });
    };
    return {
      placeholder: metaKey,
      value: metaValue,
      updateValue: updateMetaValue
    };
  }
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/bindings/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const {
  registerBlockBindingsSource
} = unlock((0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store));
registerBlockBindingsSource(post_meta);
if (false) {}

;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/hooks/custom-sources-backwards-compatibility.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


/** @typedef {import('@wordpress/compose').WPHigherOrderComponent} WPHigherOrderComponent */
/** @typedef {import('@wordpress/blocks').WPBlockSettings} WPBlockSettings */

/**
 * Object whose keys are the names of block attributes, where each value
 * represents the meta key to which the block attribute is intended to save.
 *
 * @see https://developer.wordpress.org/reference/functions/register_meta/
 *
 * @typedef {Object<string,string>} WPMetaAttributeMapping
 */

/**
 * Given a mapping of attribute names (meta source attributes) to their
 * associated meta key, returns a higher order component that overrides its
 * `attributes` and `setAttributes` props to sync any changes with the edited
 * post's meta keys.
 *
 * @param {WPMetaAttributeMapping} metaAttributes Meta attribute mapping.
 *
 * @return {WPHigherOrderComponent} Higher-order component.
 */
const createWithMetaAttributeSource = metaAttributes => (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => ({
  attributes,
  setAttributes,
  ...props
}) => {
  const postType = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getCurrentPostType(), []);
  const [meta, setMeta] = (0,external_wp_coreData_namespaceObject.useEntityProp)('postType', postType, 'meta');
  const mergedAttributes = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    ...attributes,
    ...Object.fromEntries(Object.entries(metaAttributes).map(([attributeKey, metaKey]) => [attributeKey, meta[metaKey]]))
  }), [attributes, meta]);
  return (0,external_React_.createElement)(BlockEdit, {
    attributes: mergedAttributes,
    setAttributes: nextAttributes => {
      const nextMeta = Object.fromEntries(Object.entries(nextAttributes !== null && nextAttributes !== void 0 ? nextAttributes : {}).filter(
      // Filter to intersection of keys between the updated
      // attributes and those with an associated meta key.
      ([key]) => key in metaAttributes).map(([attributeKey, value]) => [
      // Rename the keys to the expected meta key name.
      metaAttributes[attributeKey], value]));
      if (Object.entries(nextMeta).length) {
        setMeta(nextMeta);
      }
      setAttributes(nextAttributes);
    },
    ...props
  });
}, 'withMetaAttributeSource');

/**
 * Filters a registered block's settings to enhance a block's `edit` component
 * to upgrade meta-sourced attributes to use the post's meta entity property.
 *
 * @param {WPBlockSettings} settings Registered block settings.
 *
 * @return {WPBlockSettings} Filtered block settings.
 */
function shimAttributeSource(settings) {
  var _settings$attributes;
  /** @type {WPMetaAttributeMapping} */
  const metaAttributes = Object.fromEntries(Object.entries((_settings$attributes = settings.attributes) !== null && _settings$attributes !== void 0 ? _settings$attributes : {}).filter(([, {
    source
  }]) => source === 'meta').map(([attributeKey, {
    meta
  }]) => [attributeKey, meta]));
  if (Object.entries(metaAttributes).length) {
    settings.edit = createWithMetaAttributeSource(metaAttributes)(settings.edit);
  }
  return settings;
}
(0,external_wp_hooks_namespaceObject.addFilter)('blocks.registerBlockType', 'core/editor/custom-sources-backwards-compatibility/shim-attribute-source', shimAttributeSource);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autocompleters/user.js

/**
 * WordPress dependencies
 */




/** @typedef {import('@wordpress/components').WPCompleter} WPCompleter */

function getUserLabel(user) {
  const avatar = user.avatar_urls && user.avatar_urls[24] ? (0,external_React_.createElement)("img", {
    className: "editor-autocompleters__user-avatar",
    alt: "",
    src: user.avatar_urls[24]
  }) : (0,external_React_.createElement)("span", {
    className: "editor-autocompleters__no-avatar"
  });
  return (0,external_React_.createElement)(external_React_.Fragment, null, avatar, (0,external_React_.createElement)("span", {
    className: "editor-autocompleters__user-name"
  }, user.name), (0,external_React_.createElement)("span", {
    className: "editor-autocompleters__user-slug"
  }, user.slug));
}

/**
 * A user mentions completer.
 *
 * @type {WPCompleter}
 */
/* harmony default export */ const user = ({
  name: 'users',
  className: 'editor-autocompleters__user',
  triggerPrefix: '@',
  useItems(filterValue) {
    const users = (0,external_wp_data_namespaceObject.useSelect)(select => {
      const {
        getUsers
      } = select(external_wp_coreData_namespaceObject.store);
      return getUsers({
        context: 'view',
        search: encodeURIComponent(filterValue)
      });
    }, [filterValue]);
    const options = (0,external_wp_element_namespaceObject.useMemo)(() => users ? users.map(user => ({
      key: `user-${user.slug}`,
      value: user,
      label: getUserLabel(user)
    })) : [], [users]);
    return [options];
  },
  getOptionCompletion(user) {
    return `@${user.slug}`;
  }
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/hooks/default-autocompleters.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

function setDefaultCompleters(completers = []) {
  // Provide copies so filters may directly modify them.
  completers.push({
    ...user
  });
  return completers;
}
(0,external_wp_hooks_namespaceObject.addFilter)('editor.Autocomplete.completers', 'editor/autocompleters/set-default-completers', setDefaultCompleters);

;// CONCATENATED MODULE: external ["wp","patterns"]
const external_wp_patterns_namespaceObject = window["wp"]["patterns"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/hooks/pattern-overrides.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


const {
  useSetPatternBindings,
  ResetOverridesControl,
  PATTERN_TYPES,
  PARTIAL_SYNCING_SUPPORTED_BLOCKS
} = unlock(external_wp_patterns_namespaceObject.privateApis);

/**
 * Override the default edit UI to include a new block inspector control for
 * assigning a partial syncing controls to supported blocks in the pattern editor.
 * Currently, only the `core/paragraph` block is supported.
 *
 * @param {Component} BlockEdit Original component.
 *
 * @return {Component} Wrapped component.
 */
const withPatternOverrideControls = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => props => {
  const isSupportedBlock = Object.keys(PARTIAL_SYNCING_SUPPORTED_BLOCKS).includes(props.name);
  return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(BlockEdit, {
    ...props
  }), isSupportedBlock && (0,external_React_.createElement)(BindingUpdater, {
    ...props
  }), props.isSelected && isSupportedBlock && (0,external_React_.createElement)(ControlsWithStoreSubscription, {
    ...props
  }));
});
function BindingUpdater(props) {
  const postType = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getCurrentPostType(), []);
  useSetPatternBindings(props, postType);
  return null;
}

// Split into a separate component to avoid a store subscription
// on every block.
function ControlsWithStoreSubscription(props) {
  const blockEditingMode = (0,external_wp_blockEditor_namespaceObject.useBlockEditingMode)();
  const isEditingPattern = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getCurrentPostType() === PATTERN_TYPES.user, []);
  const bindings = props.attributes.metadata?.bindings;
  const hasPatternBindings = !!bindings && Object.values(bindings).some(binding => binding.source === 'core/pattern-overrides');
  const shouldShowResetOverridesControl = !isEditingPattern && !!props.attributes.metadata?.name && blockEditingMode !== 'disabled' && hasPatternBindings;
  return (0,external_React_.createElement)(external_React_.Fragment, null, shouldShowResetOverridesControl && (0,external_React_.createElement)(ResetOverridesControl, {
    ...props
  }));
}
(0,external_wp_hooks_namespaceObject.addFilter)('editor.BlockEdit', 'core/editor/with-pattern-override-controls', withPatternOverrideControls);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/hooks/index.js
/**
 * Internal dependencies
 */




;// CONCATENATED MODULE: external ["wp","keyboardShortcuts"]
const external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/global-keyboard-shortcuts/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function EditorKeyboardShortcuts() {
  const {
    redo,
    undo,
    savePost,
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    isEditedPostDirty,
    isPostSavingLocked,
    isListViewOpened
  } = (0,external_wp_data_namespaceObject.useSelect)(store_store);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/editor/undo', event => {
    undo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/editor/redo', event => {
    redo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/editor/save', event => {
    event.preventDefault();

    /**
     * Do not save the post if post saving is locked.
     */
    if (isPostSavingLocked()) {
      return;
    }

    // TODO: This should be handled in the `savePost` effect in
    // considering `isSaveable`. See note on `isEditedPostSaveable`
    // selector about dirtiness and meta-boxes.
    //
    // See: `isEditedPostSaveable`
    if (!isEditedPostDirty()) {
      return;
    }
    savePost();
  });

  // Only opens the list view. Other functionality for this shortcut happens in the rendered sidebar.
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/editor/toggle-list-view', event => {
    if (!isListViewOpened()) {
      event.preventDefault();
      setIsListViewOpened(true);
    }
  });
  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autocompleters/index.js


;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autosave-monitor/index.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * AutosaveMonitor invokes `props.autosave()` within at most `interval` seconds after an unsaved change is detected.
 *
 * The logic is straightforward: a check is performed every `props.interval` seconds. If any changes are detected, `props.autosave()` is called.
 * The time between the change and the autosave varies but is no larger than `props.interval` seconds. Refer to the code below for more details, such as
 * the specific way of detecting changes.
 *
 * There are two caveats:
 * * If `props.isAutosaveable` happens to be false at a time of checking for changes, the check is retried every second.
 * * The timer may be disabled by setting `props.disableIntervalChecks` to `true`. In that mode, any change will immediately trigger `props.autosave()`.
 */
class AutosaveMonitor extends external_wp_element_namespaceObject.Component {
  constructor(props) {
    super(props);
    this.needsAutosave = !!(props.isDirty && props.isAutosaveable);
  }
  componentDidMount() {
    if (!this.props.disableIntervalChecks) {
      this.setAutosaveTimer();
    }
  }
  componentDidUpdate(prevProps) {
    if (this.props.disableIntervalChecks) {
      if (this.props.editsReference !== prevProps.editsReference) {
        this.props.autosave();
      }
      return;
    }
    if (this.props.interval !== prevProps.interval) {
      clearTimeout(this.timerId);
      this.setAutosaveTimer();
    }
    if (!this.props.isDirty) {
      this.needsAutosave = false;
      return;
    }
    if (this.props.isAutosaving && !prevProps.isAutosaving) {
      this.needsAutosave = false;
      return;
    }
    if (this.props.editsReference !== prevProps.editsReference) {
      this.needsAutosave = true;
    }
  }
  componentWillUnmount() {
    clearTimeout(this.timerId);
  }
  setAutosaveTimer(timeout = this.props.interval * 1000) {
    this.timerId = setTimeout(() => {
      this.autosaveTimerHandler();
    }, timeout);
  }
  autosaveTimerHandler() {
    if (!this.props.isAutosaveable) {
      this.setAutosaveTimer(1000);
      return;
    }
    if (this.needsAutosave) {
      this.needsAutosave = false;
      this.props.autosave();
    }
    this.setAutosaveTimer();
  }
  render() {
    return null;
  }
}
/* harmony default export */ const autosave_monitor = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)((select, ownProps) => {
  const {
    getReferenceByDistinctEdits
  } = select(external_wp_coreData_namespaceObject.store);
  const {
    isEditedPostDirty,
    isEditedPostAutosaveable,
    isAutosavingPost,
    getEditorSettings
  } = select(store_store);
  const {
    interval = getEditorSettings().autosaveInterval
  } = ownProps;
  return {
    editsReference: getReferenceByDistinctEdits(),
    isDirty: isEditedPostDirty(),
    isAutosaveable: isEditedPostAutosaveable(),
    isAutosaving: isAutosavingPost(),
    interval
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, ownProps) => ({
  autosave() {
    const {
      autosave = dispatch(store_store).autosave
    } = ownProps;
    autosave();
  }
}))])(AutosaveMonitor));

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(5755);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);
;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/symbol.js

/**
 * WordPress dependencies
 */

const symbol = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z"
}));
/* harmony default export */ const library_symbol = (symbol);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/navigation.js

/**
 * WordPress dependencies
 */

const navigation = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 4c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14.5c-3.6 0-6.5-2.9-6.5-6.5S8.4 5.5 12 5.5s6.5 2.9 6.5 6.5-2.9 6.5-6.5 6.5zM9 16l4.5-3L15 8.4l-4.5 3L9 16z"
}));
/* harmony default export */ const library_navigation = (navigation);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/page.js

/**
 * WordPress dependencies
 */

const page = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M15.5 7.5h-7V9h7V7.5Zm-7 3.5h7v1.5h-7V11Zm7 3.5h-7V16h7v-1.5Z"
}), (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17 4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2ZM7 5.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H7a.5.5 0 0 1-.5-.5V6a.5.5 0 0 1 .5-.5Z"
}));
/* harmony default export */ const library_page = (page);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-right-small.js

/**
 * WordPress dependencies
 */

const chevronRightSmall = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M10.8622 8.04053L14.2805 12.0286L10.8622 16.0167L9.72327 15.0405L12.3049 12.0286L9.72327 9.01672L10.8622 8.04053Z"
}));
/* harmony default export */ const chevron_right_small = (chevronRightSmall);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-left-small.js

/**
 * WordPress dependencies
 */

const chevronLeftSmall = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "m13.1 16-3.4-4 3.4-4 1.1 1-2.6 3 2.6 3-1.1 1z"
}));
/* harmony default export */ const chevron_left_small = (chevronLeftSmall);

;// CONCATENATED MODULE: external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// CONCATENATED MODULE: external ["wp","commands"]
const external_wp_commands_namespaceObject = window["wp"]["commands"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-bar/index.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */

const typeLabels = {
  // translators: 1: Pattern title.
  wp_pattern: (0,external_wp_i18n_namespaceObject.__)('Editing pattern: %s'),
  // translators: 1: Navigation menu title.
  wp_navigation: (0,external_wp_i18n_namespaceObject.__)('Editing navigation menu: %s'),
  // translators: 1: Template title.
  wp_template: (0,external_wp_i18n_namespaceObject.__)('Editing template: %s'),
  // translators: 1: Template part title.
  wp_template_part: (0,external_wp_i18n_namespaceObject.__)('Editing template part: %s')
};
const icons = {
  wp_block: library_symbol,
  wp_navigation: library_navigation
};
function DocumentBar() {
  const {
    postType,
    postId,
    onNavigateToPreviousEntityRecord
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getCurrentPostId,
      getCurrentPostType,
      getEditorSettings: getSettings
    } = select(store_store);
    return {
      postType: getCurrentPostType(),
      postId: getCurrentPostId(),
      onNavigateToPreviousEntityRecord: getSettings().onNavigateToPreviousEntityRecord,
      getEditorSettings: getSettings
    };
  }, []);
  const handleOnBack = () => {
    if (onNavigateToPreviousEntityRecord) {
      onNavigateToPreviousEntityRecord();
    }
  };
  return (0,external_React_.createElement)(BaseDocumentActions, {
    postType: postType,
    postId: postId,
    onBack: onNavigateToPreviousEntityRecord ? handleOnBack : undefined
  });
}
function BaseDocumentActions({
  postType,
  postId,
  onBack
}) {
  var _icons$postType;
  const {
    open: openCommandCenter
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_commands_namespaceObject.store);
  const {
    editedRecord: doc,
    isResolving
  } = (0,external_wp_coreData_namespaceObject.useEntityRecord)('postType', postType, postId);
  const {
    templateIcon,
    templateTitle
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      __experimentalGetTemplateInfo: getTemplateInfo
    } = select(store_store);
    const templateInfo = getTemplateInfo(doc);
    return {
      templateIcon: templateInfo.icon,
      templateTitle: templateInfo.title
    };
  });
  const isNotFound = !doc && !isResolving;
  const icon = (_icons$postType = icons[postType]) !== null && _icons$postType !== void 0 ? _icons$postType : library_page;
  const [isAnimated, setIsAnimated] = (0,external_wp_element_namespaceObject.useState)(false);
  const isMounting = (0,external_wp_element_namespaceObject.useRef)(true);
  const isTemplate = ['wp_template', 'wp_template_part'].includes(postType);
  const isGlobalEntity = ['wp_template', 'wp_navigation', 'wp_template_part', 'wp_block'].includes(postType);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!isMounting.current) {
      setIsAnimated(true);
    }
    isMounting.current = false;
  }, [postType, postId]);
  const title = isTemplate ? templateTitle : doc.title;
  return (0,external_React_.createElement)("div", {
    className: classnames_default()('editor-document-bar', {
      'has-back-button': !!onBack,
      'is-animated': isAnimated,
      'is-global': isGlobalEntity
    })
  }, onBack && (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    className: "editor-document-bar__back",
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_right_small : chevron_left_small,
    onClick: event => {
      event.stopPropagation();
      onBack();
    },
    size: "compact"
  }, (0,external_wp_i18n_namespaceObject.__)('Back')), isNotFound && (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalText, null, (0,external_wp_i18n_namespaceObject.__)('Document not found')), !isNotFound && (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    className: "editor-document-bar__command",
    onClick: () => openCommandCenter(),
    size: "compact"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    className: "editor-document-bar__title",
    spacing: 1,
    justify: "center"
  }, (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.BlockIcon, {
    icon: isTemplate ? templateIcon : icon
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    size: "body",
    as: "h1",
    "aria-label": typeLabels[postType] ?
    // eslint-disable-next-line @wordpress/valid-sprintf
    (0,external_wp_i18n_namespaceObject.sprintf)(typeLabels[postType], title) : undefined
  }, title)), (0,external_React_.createElement)("span", {
    className: "editor-document-bar__shortcut"
  }, external_wp_keycodes_namespaceObject.displayShortcut.primary('k'))));
}

;// CONCATENATED MODULE: external ["wp","richText"]
const external_wp_richText_namespaceObject = window["wp"]["richText"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-outline/item.js

/**
 * External dependencies
 */

const TableOfContentsItem = ({
  children,
  isValid,
  level,
  href,
  onSelect
}) => (0,external_React_.createElement)("li", {
  className: classnames_default()('document-outline__item', `is-${level.toLowerCase()}`, {
    'is-invalid': !isValid
  })
}, (0,external_React_.createElement)("a", {
  href: href,
  className: "document-outline__button",
  onClick: onSelect
}, (0,external_React_.createElement)("span", {
  className: "document-outline__emdash",
  "aria-hidden": "true"
}), (0,external_React_.createElement)("strong", {
  className: "document-outline__level"
}, level), (0,external_React_.createElement)("span", {
  className: "document-outline__item-content"
}, children)));
/* harmony default export */ const document_outline_item = (TableOfContentsItem);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-outline/index.js

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */



/**
 * Module constants
 */
const emptyHeadingContent = (0,external_React_.createElement)("em", null, (0,external_wp_i18n_namespaceObject.__)('(Empty heading)'));
const incorrectLevelContent = [(0,external_React_.createElement)("br", {
  key: "incorrect-break"
}), (0,external_React_.createElement)("em", {
  key: "incorrect-message"
}, (0,external_wp_i18n_namespaceObject.__)('(Incorrect heading level)'))];
const singleH1Headings = [(0,external_React_.createElement)("br", {
  key: "incorrect-break-h1"
}), (0,external_React_.createElement)("em", {
  key: "incorrect-message-h1"
}, (0,external_wp_i18n_namespaceObject.__)('(Your theme may already use a H1 for the post title)'))];
const multipleH1Headings = [(0,external_React_.createElement)("br", {
  key: "incorrect-break-multiple-h1"
}), (0,external_React_.createElement)("em", {
  key: "incorrect-message-multiple-h1"
}, (0,external_wp_i18n_namespaceObject.__)('(Multiple H1 headings are not recommended)'))];
function EmptyOutlineIllustration() {
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.SVG, {
    width: "138",
    height: "148",
    viewBox: "0 0 138 148",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Rect, {
    width: "138",
    height: "148",
    rx: "4",
    fill: "#F0F6FC"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Line, {
    x1: "44",
    y1: "28",
    x2: "24",
    y2: "28",
    stroke: "#DDDDDD"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Rect, {
    x: "48",
    y: "16",
    width: "27",
    height: "23",
    rx: "4",
    fill: "#DDDDDD"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Path, {
    d: "M54.7585 32V23.2727H56.6037V26.8736H60.3494V23.2727H62.1903V32H60.3494V28.3949H56.6037V32H54.7585ZM67.4574 23.2727V32H65.6122V25.0241H65.5611L63.5625 26.277V24.6406L65.723 23.2727H67.4574Z",
    fill: "black"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Line, {
    x1: "55",
    y1: "59",
    x2: "24",
    y2: "59",
    stroke: "#DDDDDD"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Rect, {
    x: "59",
    y: "47",
    width: "29",
    height: "23",
    rx: "4",
    fill: "#DDDDDD"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Path, {
    d: "M65.7585 63V54.2727H67.6037V57.8736H71.3494V54.2727H73.1903V63H71.3494V59.3949H67.6037V63H65.7585ZM74.6605 63V61.6705L77.767 58.794C78.0313 58.5384 78.2528 58.3082 78.4318 58.1037C78.6136 57.8991 78.7514 57.6989 78.8452 57.5028C78.9389 57.304 78.9858 57.0895 78.9858 56.8594C78.9858 56.6037 78.9276 56.3835 78.8111 56.1989C78.6946 56.0114 78.5355 55.8679 78.3338 55.7685C78.1321 55.6662 77.9034 55.6151 77.6477 55.6151C77.3807 55.6151 77.1477 55.669 76.9489 55.777C76.75 55.8849 76.5966 56.0398 76.4886 56.2415C76.3807 56.4432 76.3267 56.6832 76.3267 56.9616H74.5753C74.5753 56.3906 74.7045 55.8949 74.9631 55.4744C75.2216 55.054 75.5838 54.7287 76.0497 54.4986C76.5156 54.2685 77.0526 54.1534 77.6605 54.1534C78.2855 54.1534 78.8295 54.2642 79.2926 54.4858C79.7585 54.7045 80.1207 55.0085 80.3793 55.3977C80.6378 55.7869 80.767 56.233 80.767 56.7358C80.767 57.0653 80.7017 57.3906 80.571 57.7116C80.4432 58.0327 80.2145 58.3892 79.8849 58.7812C79.5554 59.1705 79.0909 59.6378 78.4915 60.1832L77.2173 61.4318V61.4915H80.8821V63H74.6605Z",
    fill: "black"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Line, {
    x1: "80",
    y1: "90",
    x2: "24",
    y2: "90",
    stroke: "#DDDDDD"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Rect, {
    x: "84",
    y: "78",
    width: "30",
    height: "23",
    rx: "4",
    fill: "#F0B849"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Path, {
    d: "M90.7585 94V85.2727H92.6037V88.8736H96.3494V85.2727H98.1903V94H96.3494V90.3949H92.6037V94H90.7585ZM99.5284 92.4659V91.0128L103.172 85.2727H104.425V87.2841H103.683L101.386 90.919V90.9872H106.564V92.4659H99.5284ZM103.717 94V92.0227L103.751 91.3793V85.2727H105.482V94H103.717Z",
    fill: "black"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Line, {
    x1: "66",
    y1: "121",
    x2: "24",
    y2: "121",
    stroke: "#DDDDDD"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Rect, {
    x: "70",
    y: "109",
    width: "29",
    height: "23",
    rx: "4",
    fill: "#DDDDDD"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Path, {
    d: "M76.7585 125V116.273H78.6037V119.874H82.3494V116.273H84.1903V125H82.3494V121.395H78.6037V125H76.7585ZM88.8864 125.119C88.25 125.119 87.6832 125.01 87.1861 124.791C86.6918 124.57 86.3011 124.266 86.0142 123.879C85.7301 123.49 85.5838 123.041 85.5753 122.533H87.4332C87.4446 122.746 87.5142 122.933 87.642 123.095C87.7727 123.254 87.946 123.378 88.1619 123.466C88.3778 123.554 88.6207 123.598 88.8906 123.598C89.1719 123.598 89.4205 123.548 89.6364 123.449C89.8523 123.349 90.0213 123.212 90.1435 123.036C90.2656 122.859 90.3267 122.656 90.3267 122.426C90.3267 122.193 90.2614 121.987 90.1307 121.808C90.0028 121.626 89.8182 121.484 89.5767 121.382C89.3381 121.28 89.054 121.229 88.7244 121.229H87.9105V119.874H88.7244C89.0028 119.874 89.2486 119.825 89.4616 119.729C89.6776 119.632 89.8452 119.499 89.9645 119.328C90.0838 119.155 90.1435 118.953 90.1435 118.723C90.1435 118.504 90.0909 118.312 89.9858 118.148C89.8835 117.98 89.7386 117.849 89.5511 117.756C89.3665 117.662 89.1506 117.615 88.9034 117.615C88.6534 117.615 88.4247 117.661 88.2173 117.751C88.0099 117.839 87.8438 117.966 87.7188 118.131C87.5938 118.295 87.527 118.489 87.5185 118.71H85.75C85.7585 118.207 85.902 117.764 86.1804 117.381C86.4588 116.997 86.8338 116.697 87.3054 116.482C87.7798 116.263 88.3153 116.153 88.9119 116.153C89.5142 116.153 90.0412 116.263 90.4929 116.482C90.9446 116.7 91.2955 116.996 91.5455 117.368C91.7983 117.737 91.9233 118.152 91.9205 118.612C91.9233 119.101 91.7713 119.509 91.4645 119.835C91.1605 120.162 90.7642 120.369 90.2756 120.457V120.526C90.9176 120.608 91.4063 120.831 91.7415 121.195C92.0795 121.555 92.2472 122.007 92.2443 122.55C92.2472 123.047 92.1037 123.489 91.8139 123.875C91.527 124.261 91.1307 124.565 90.625 124.787C90.1193 125.009 89.5398 125.119 88.8864 125.119Z",
    fill: "black"
  }));
}

/**
 * Returns an array of heading blocks enhanced with the following properties:
 * level   - An integer with the heading level.
 * isEmpty - Flag indicating if the heading has no content.
 *
 * @param {?Array} blocks An array of blocks.
 *
 * @return {Array} An array of heading blocks enhanced with the properties described above.
 */
const computeOutlineHeadings = (blocks = []) => {
  return blocks.flatMap((block = {}) => {
    if (block.name === 'core/heading') {
      return {
        ...block,
        level: block.attributes.level,
        isEmpty: isEmptyHeading(block)
      };
    }
    return computeOutlineHeadings(block.innerBlocks);
  });
};
const isEmptyHeading = heading => !heading.attributes.content || heading.attributes.content.length === 0;
const DocumentOutline = ({
  blocks = [],
  title,
  onSelect,
  isTitleSupported,
  hasOutlineItemsDisabled
}) => {
  const headings = computeOutlineHeadings(blocks);
  const {
    selectBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  if (headings.length < 1) {
    return (0,external_React_.createElement)("div", {
      className: "editor-document-outline has-no-headings"
    }, (0,external_React_.createElement)(EmptyOutlineIllustration, null), (0,external_React_.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('Navigate the structure of your document and address issues like empty or incorrect heading levels.')));
  }
  let prevHeadingLevel = 1;

  // Not great but it's the simplest way to locate the title right now.
  const titleNode = document.querySelector('.editor-post-title__input');
  const hasTitle = isTitleSupported && title && titleNode;
  const countByLevel = headings.reduce((acc, heading) => ({
    ...acc,
    [heading.level]: (acc[heading.level] || 0) + 1
  }), {});
  const hasMultipleH1 = countByLevel[1] > 1;
  return (0,external_React_.createElement)("div", {
    className: "document-outline"
  }, (0,external_React_.createElement)("ul", null, hasTitle && (0,external_React_.createElement)(document_outline_item, {
    level: (0,external_wp_i18n_namespaceObject.__)('Title'),
    isValid: true,
    onSelect: onSelect,
    href: `#${titleNode.id}`,
    isDisabled: hasOutlineItemsDisabled
  }, title), headings.map((item, index) => {
    // Headings remain the same, go up by one, or down by any amount.
    // Otherwise there are missing levels.
    const isIncorrectLevel = item.level > prevHeadingLevel + 1;
    const isValid = !item.isEmpty && !isIncorrectLevel && !!item.level && (item.level !== 1 || !hasMultipleH1 && !hasTitle);
    prevHeadingLevel = item.level;
    return (0,external_React_.createElement)(document_outline_item, {
      key: index,
      level: `H${item.level}`,
      isValid: isValid,
      isDisabled: hasOutlineItemsDisabled,
      href: `#block-${item.clientId}`,
      onSelect: () => {
        selectBlock(item.clientId);
        onSelect?.();
      }
    }, item.isEmpty ? emptyHeadingContent : (0,external_wp_richText_namespaceObject.getTextContent)((0,external_wp_richText_namespaceObject.create)({
      html: item.attributes.content
    })), isIncorrectLevel && incorrectLevelContent, item.level === 1 && hasMultipleH1 && multipleH1Headings, hasTitle && item.level === 1 && !hasMultipleH1 && singleH1Headings);
  })));
};
/* harmony default export */ const document_outline = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)(select => {
  var _postType$supports$ti;
  const {
    getBlocks
  } = select(external_wp_blockEditor_namespaceObject.store);
  const {
    getEditedPostAttribute
  } = select(store_store);
  const {
    getPostType
  } = select(external_wp_coreData_namespaceObject.store);
  const postType = getPostType(getEditedPostAttribute('type'));
  return {
    title: getEditedPostAttribute('title'),
    blocks: getBlocks(),
    isTitleSupported: (_postType$supports$ti = postType?.supports?.title) !== null && _postType$supports$ti !== void 0 ? _postType$supports$ti : false
  };
}))(DocumentOutline));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-outline/check.js
/**
 * WordPress dependencies
 */


function DocumentOutlineCheck({
  blocks,
  children
}) {
  const headings = blocks.filter(block => block.name === 'core/heading');
  if (headings.length < 1) {
    return null;
  }
  return children;
}
/* harmony default export */ const check = ((0,external_wp_data_namespaceObject.withSelect)(select => ({
  blocks: select(external_wp_blockEditor_namespaceObject.store).getBlocks()
}))(DocumentOutlineCheck));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/global-keyboard-shortcuts/register-shortcuts.js

/**
 * WordPress dependencies
 */






function EditorKeyboardShortcutsRegister() {
  // Registering the shortcuts.
  const {
    registerShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: 'core/editor/save',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Save your changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 's'
      }
    });
    registerShortcut({
      name: 'core/editor/undo',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Undo your last changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 'z'
      }
    });
    registerShortcut({
      name: 'core/editor/redo',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Redo your last undo.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: 'z'
      },
      // Disable on Apple OS because it conflicts with the browser's
      // history shortcut. It's a fine alias for both Windows and Linux.
      // Since there's no conflict for Ctrl+Shift+Z on both Windows and
      // Linux, we keep it as the default for consistency.
      aliases: (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? [] : [{
        modifier: 'primary',
        character: 'y'
      }]
    });
    registerShortcut({
      name: 'core/editor/toggle-list-view',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Open the block list view.'),
      keyCombination: {
        modifier: 'access',
        character: 'o'
      }
    });
  }, [registerShortcut]);
  return (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.BlockEditorKeyboardShortcuts.Register, null);
}
/* harmony default export */ const register_shortcuts = (EditorKeyboardShortcutsRegister);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/redo.js

/**
 * WordPress dependencies
 */

const redo_redo = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"
}));
/* harmony default export */ const library_redo = (redo_redo);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/undo.js

/**
 * WordPress dependencies
 */

const undo_undo = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"
}));
/* harmony default export */ const library_undo = (undo_undo);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-history/redo.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */

function EditorHistoryRedo(props, ref) {
  const shortcut = (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? external_wp_keycodes_namespaceObject.displayShortcut.primaryShift('z') : external_wp_keycodes_namespaceObject.displayShortcut.primary('y');
  const hasRedo = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).hasEditorRedo(), []);
  const {
    redo
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    ...props,
    ref: ref,
    icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? library_redo : library_undo
    /* translators: button label text should, if possible, be under 16 characters. */,
    label: (0,external_wp_i18n_namespaceObject.__)('Redo'),
    shortcut: shortcut
    // If there are no redo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasRedo,
    onClick: hasRedo ? redo : undefined,
    className: "editor-history__redo"
  });
}
/* harmony default export */ const editor_history_redo = ((0,external_wp_element_namespaceObject.forwardRef)(EditorHistoryRedo));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-history/undo.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */

function EditorHistoryUndo(props, ref) {
  const hasUndo = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).hasEditorUndo(), []);
  const {
    undo
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    ...props,
    ref: ref,
    icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? library_undo : library_redo
    /* translators: button label text should, if possible, be under 16 characters. */,
    label: (0,external_wp_i18n_namespaceObject.__)('Undo'),
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.primary('z')
    // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasUndo,
    onClick: hasUndo ? undo : undefined,
    className: "editor-history__undo"
  });
}
/* harmony default export */ const editor_history_undo = ((0,external_wp_element_namespaceObject.forwardRef)(EditorHistoryUndo));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/template-validation-notice/index.js

/**
 * WordPress dependencies
 */





function TemplateValidationNotice({
  isValid,
  ...props
}) {
  if (isValid) {
    return null;
  }
  const confirmSynchronization = () => {
    if (
    // eslint-disable-next-line no-alert
    window.confirm((0,external_wp_i18n_namespaceObject.__)('Resetting the template may result in loss of content, do you want to continue?'))) {
      props.synchronizeTemplate();
    }
  };
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Notice, {
    className: "editor-template-validation-notice",
    isDismissible: false,
    status: "warning",
    actions: [{
      label: (0,external_wp_i18n_namespaceObject.__)('Keep it as is'),
      onClick: props.resetTemplateValidity
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Reset the template'),
      onClick: confirmSynchronization
    }]
  }, (0,external_wp_i18n_namespaceObject.__)('The content of your post doesn’t match the template assigned to your post type.'));
}
/* harmony default export */ const template_validation_notice = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => ({
  isValid: select(external_wp_blockEditor_namespaceObject.store).isValidTemplate()
})), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    setTemplateValidity,
    synchronizeTemplate
  } = dispatch(external_wp_blockEditor_namespaceObject.store);
  return {
    resetTemplateValidity: () => setTemplateValidity(true),
    synchronizeTemplate
  };
})])(TemplateValidationNotice));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-notices/index.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function EditorNotices() {
  const {
    notices
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    notices: select(external_wp_notices_namespaceObject.store).getNotices()
  }), []);
  const {
    removeNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const dismissibleNotices = notices.filter(({
    isDismissible,
    type
  }) => isDismissible && type === 'default');
  const nonDismissibleNotices = notices.filter(({
    isDismissible,
    type
  }) => !isDismissible && type === 'default');
  return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.NoticeList, {
    notices: nonDismissibleNotices,
    className: "components-editor-notices__pinned"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.NoticeList, {
    notices: dismissibleNotices,
    className: "components-editor-notices__dismissible",
    onRemove: removeNotice
  }, (0,external_React_.createElement)(template_validation_notice, null)));
}
/* harmony default export */ const editor_notices = (EditorNotices);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-snackbars/index.js

/**
 * WordPress dependencies
 */




// Last three notices. Slices from the tail end of the list.
const MAX_VISIBLE_NOTICES = -3;
function EditorSnackbars() {
  const notices = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_notices_namespaceObject.store).getNotices(), []);
  const {
    removeNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const snackbarNotices = notices.filter(({
    type
  }) => type === 'snackbar').slice(MAX_VISIBLE_NOTICES);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.SnackbarList, {
    notices: snackbarNotices,
    className: "components-editor-notices__snackbar",
    onRemove: removeNotice
  });
}

;// CONCATENATED MODULE: external ["wp","htmlEntities"]
const external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/entities-saved-states/entity-record-item.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */

function EntityRecordItem({
  record,
  checked,
  onChange
}) {
  const {
    name,
    kind,
    title,
    key
  } = record;

  // Handle templates that might use default descriptive titles.
  const entityRecordTitle = (0,external_wp_data_namespaceObject.useSelect)(select => {
    if ('postType' !== kind || 'wp_template' !== name) {
      return title;
    }
    const template = select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(kind, name, key);
    return select(store_store).__experimentalGetTemplateInfo(template).title;
  }, [name, kind, title, key]);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(entityRecordTitle) || (0,external_wp_i18n_namespaceObject.__)('Untitled'),
    checked: checked,
    onChange: onChange
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/entities-saved-states/entity-type-list.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


const {
  getGlobalStylesChanges,
  GlobalStylesContext
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function getEntityDescription(entity, count) {
  switch (entity) {
    case 'site':
      return 1 === count ? (0,external_wp_i18n_namespaceObject.__)('This change will affect your whole site.') : (0,external_wp_i18n_namespaceObject.__)('These changes will affect your whole site.');
    case 'wp_template':
      return (0,external_wp_i18n_namespaceObject.__)('This change will affect pages and posts that use this template.');
    case 'page':
    case 'post':
      return (0,external_wp_i18n_namespaceObject.__)('The following has been modified.');
  }
}
function GlobalStylesDescription({
  record
}) {
  const {
    user: currentEditorGlobalStyles
  } = (0,external_wp_element_namespaceObject.useContext)(GlobalStylesContext);
  const savedRecord = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getEntityRecord(record.kind, record.name, record.key), [record.kind, record.name, record.key]);
  const globalStylesChanges = getGlobalStylesChanges(currentEditorGlobalStyles, savedRecord, {
    maxResults: 10
  });
  return globalStylesChanges.length ? (0,external_React_.createElement)("ul", {
    className: "entities-saved-states__changes"
  }, globalStylesChanges.map(change => (0,external_React_.createElement)("li", {
    key: change
  }, change))) : null;
}
function EntityDescription({
  record,
  count
}) {
  if ('globalStyles' === record?.name) {
    return null;
  }
  const description = getEntityDescription(record?.name, count);
  return description ? (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelRow, null, description) : null;
}
function EntityTypeList({
  list,
  unselectedEntities,
  setUnselectedEntities
}) {
  const count = list.length;
  const firstRecord = list[0];
  const entityConfig = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getEntityConfig(firstRecord.kind, firstRecord.name), [firstRecord.kind, firstRecord.name]);
  let entityLabel = entityConfig.label;
  if (firstRecord?.name === 'wp_template_part') {
    entityLabel = 1 === count ? (0,external_wp_i18n_namespaceObject.__)('Template Part') : (0,external_wp_i18n_namespaceObject.__)('Template Parts');
  }
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: entityLabel,
    initialOpen: true
  }, (0,external_React_.createElement)(EntityDescription, {
    record: firstRecord,
    count: count
  }), list.map(record => {
    return (0,external_React_.createElement)(EntityRecordItem, {
      key: record.key || record.property,
      record: record,
      checked: !unselectedEntities.some(elt => elt.kind === record.kind && elt.name === record.name && elt.key === record.key && elt.property === record.property),
      onChange: value => setUnselectedEntities(record, value)
    });
  }), 'globalStyles' === firstRecord?.name && (0,external_React_.createElement)(GlobalStylesDescription, {
    record: firstRecord
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/entities-saved-states/hooks/use-is-dirty.js
/**
 * WordPress dependencies
 */




const TRANSLATED_SITE_PROPERTIES = {
  title: (0,external_wp_i18n_namespaceObject.__)('Title'),
  description: (0,external_wp_i18n_namespaceObject.__)('Tagline'),
  site_logo: (0,external_wp_i18n_namespaceObject.__)('Logo'),
  site_icon: (0,external_wp_i18n_namespaceObject.__)('Icon'),
  show_on_front: (0,external_wp_i18n_namespaceObject.__)('Show on front'),
  page_on_front: (0,external_wp_i18n_namespaceObject.__)('Page on front'),
  posts_per_page: (0,external_wp_i18n_namespaceObject.__)('Maximum posts per page'),
  default_comment_status: (0,external_wp_i18n_namespaceObject.__)('Allow comments on new posts')
};
const useIsDirty = () => {
  const {
    editedEntities,
    siteEdits
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      __experimentalGetDirtyEntityRecords,
      getEntityRecordEdits
    } = select(external_wp_coreData_namespaceObject.store);
    return {
      editedEntities: __experimentalGetDirtyEntityRecords(),
      siteEdits: getEntityRecordEdits('root', 'site')
    };
  }, []);
  const dirtyEntityRecords = (0,external_wp_element_namespaceObject.useMemo)(() => {
    // Remove site object and decouple into its edited pieces.
    const editedEntitiesWithoutSite = editedEntities.filter(record => !(record.kind === 'root' && record.name === 'site'));
    const editedSiteEntities = [];
    for (const property in siteEdits) {
      editedSiteEntities.push({
        kind: 'root',
        name: 'site',
        title: TRANSLATED_SITE_PROPERTIES[property] || property,
        property
      });
    }
    return [...editedEntitiesWithoutSite, ...editedSiteEntities];
  }, [editedEntities, siteEdits]);

  // Unchecked entities to be ignored by save function.
  const [unselectedEntities, _setUnselectedEntities] = (0,external_wp_element_namespaceObject.useState)([]);
  const setUnselectedEntities = ({
    kind,
    name,
    key,
    property
  }, checked) => {
    if (checked) {
      _setUnselectedEntities(unselectedEntities.filter(elt => elt.kind !== kind || elt.name !== name || elt.key !== key || elt.property !== property));
    } else {
      _setUnselectedEntities([...unselectedEntities, {
        kind,
        name,
        key,
        property
      }]);
    }
  };
  const isDirty = dirtyEntityRecords.length - unselectedEntities.length > 0;
  return {
    dirtyEntityRecords,
    isDirty,
    setUnselectedEntities,
    unselectedEntities
  };
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/entities-saved-states/index.js

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */


const PUBLISH_ON_SAVE_ENTITIES = [{
  kind: 'postType',
  name: 'wp_navigation'
}];
function identity(values) {
  return values;
}
function EntitiesSavedStates({
  close
}) {
  const isDirtyProps = useIsDirty();
  return (0,external_React_.createElement)(EntitiesSavedStatesExtensible, {
    close: close,
    ...isDirtyProps
  });
}
function EntitiesSavedStatesExtensible({
  additionalPrompt = undefined,
  close,
  onSave = identity,
  saveEnabled: saveEnabledProp = undefined,
  saveLabel = (0,external_wp_i18n_namespaceObject.__)('Save'),
  dirtyEntityRecords,
  isDirty,
  setUnselectedEntities,
  unselectedEntities
}) {
  const saveButtonRef = (0,external_wp_element_namespaceObject.useRef)();
  const {
    editEntityRecord,
    saveEditedEntityRecord,
    __experimentalSaveSpecifiedEntityEdits: saveSpecifiedEntityEdits
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    __unstableMarkLastChangeAsPersistent
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    createSuccessNotice,
    createErrorNotice,
    removeNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);

  // To group entities by type.
  const partitionedSavables = dirtyEntityRecords.reduce((acc, record) => {
    const {
      name
    } = record;
    if (!acc[name]) {
      acc[name] = [];
    }
    acc[name].push(record);
    return acc;
  }, {});

  // Sort entity groups.
  const {
    site: siteSavables,
    wp_template: templateSavables,
    wp_template_part: templatePartSavables,
    ...contentSavables
  } = partitionedSavables;
  const sortedPartitionedSavables = [siteSavables, templateSavables, templatePartSavables, ...Object.values(contentSavables)].filter(Array.isArray);
  const saveEnabled = saveEnabledProp !== null && saveEnabledProp !== void 0 ? saveEnabledProp : isDirty;
  const {
    homeUrl
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getUnstableBase // Site index.
    } = select(external_wp_coreData_namespaceObject.store);
    return {
      homeUrl: getUnstableBase()?.home
    };
  }, []);
  const saveCheckedEntities = () => {
    const saveNoticeId = 'site-editor-save-success';
    removeNotice(saveNoticeId);
    const entitiesToSave = dirtyEntityRecords.filter(({
      kind,
      name,
      key,
      property
    }) => {
      return !unselectedEntities.some(elt => elt.kind === kind && elt.name === name && elt.key === key && elt.property === property);
    });
    close(entitiesToSave);
    const siteItemsToSave = [];
    const pendingSavedRecords = [];
    entitiesToSave.forEach(({
      kind,
      name,
      key,
      property
    }) => {
      if ('root' === kind && 'site' === name) {
        siteItemsToSave.push(property);
      } else {
        if (PUBLISH_ON_SAVE_ENTITIES.some(typeToPublish => typeToPublish.kind === kind && typeToPublish.name === name)) {
          editEntityRecord(kind, name, key, {
            status: 'publish'
          });
        }
        pendingSavedRecords.push(saveEditedEntityRecord(kind, name, key));
      }
    });
    if (siteItemsToSave.length) {
      pendingSavedRecords.push(saveSpecifiedEntityEdits('root', 'site', undefined, siteItemsToSave));
    }
    __unstableMarkLastChangeAsPersistent();
    Promise.all(pendingSavedRecords).then(values => {
      return onSave(values);
    }).then(values => {
      if (values.some(value => typeof value === 'undefined')) {
        createErrorNotice((0,external_wp_i18n_namespaceObject.__)('Saving failed.'));
      } else {
        createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Site updated.'), {
          type: 'snackbar',
          id: saveNoticeId,
          actions: [{
            label: (0,external_wp_i18n_namespaceObject.__)('View site'),
            url: homeUrl
          }]
        });
      }
    }).catch(error => createErrorNotice(`${(0,external_wp_i18n_namespaceObject.__)('Saving failed.')} ${error}`));
  };

  // Explicitly define this with no argument passed.  Using `close` on
  // its own will use the event object in place of the expected saved entities.
  const dismissPanel = (0,external_wp_element_namespaceObject.useCallback)(() => close(), [close]);
  const [saveDialogRef, saveDialogProps] = (0,external_wp_compose_namespaceObject.__experimentalUseDialog)({
    onClose: () => dismissPanel()
  });
  return (0,external_React_.createElement)("div", {
    ref: saveDialogRef,
    ...saveDialogProps,
    className: "entities-saved-states__panel"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Flex, {
    className: "entities-saved-states__panel-header",
    gap: 2
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.FlexItem, {
    isBlock: true,
    as: external_wp_components_namespaceObject.Button,
    ref: saveButtonRef,
    variant: "primary",
    disabled: !saveEnabled,
    onClick: saveCheckedEntities,
    className: "editor-entities-saved-states__save-button"
  }, saveLabel), (0,external_React_.createElement)(external_wp_components_namespaceObject.FlexItem, {
    isBlock: true,
    as: external_wp_components_namespaceObject.Button,
    variant: "secondary",
    onClick: dismissPanel
  }, (0,external_wp_i18n_namespaceObject.__)('Cancel'))), (0,external_React_.createElement)("div", {
    className: "entities-saved-states__text-prompt"
  }, (0,external_React_.createElement)("strong", {
    className: "entities-saved-states__text-prompt--header"
  }, (0,external_wp_i18n_namespaceObject.__)('Are you ready to save?')), additionalPrompt, (0,external_React_.createElement)("p", null, isDirty ? (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %d: number of site changes waiting to be saved. */
  (0,external_wp_i18n_namespaceObject._n)('There is <strong>%d site change</strong> waiting to be saved.', 'There are <strong>%d site changes</strong> waiting to be saved.', sortedPartitionedSavables.length), sortedPartitionedSavables.length), {
    strong: (0,external_React_.createElement)("strong", null)
  }) : (0,external_wp_i18n_namespaceObject.__)('Select the items you want to save.'))), sortedPartitionedSavables.map(list => {
    return (0,external_React_.createElement)(EntityTypeList, {
      key: list[0].name,
      list: list,
      unselectedEntities: unselectedEntities,
      setUnselectedEntities: setUnselectedEntities
    });
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/error-boundary/index.js

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */

function getContent() {
  try {
    // While `select` in a component is generally discouraged, it is
    // used here because it (a) reduces the chance of data loss in the
    // case of additional errors by performing a direct retrieval and
    // (b) avoids the performance cost associated with unnecessary
    // content serialization throughout the lifetime of a non-erroring
    // application.
    return (0,external_wp_data_namespaceObject.select)(store_store).getEditedPostContent();
  } catch (error) {}
}
function CopyButton({
  text,
  children
}) {
  const ref = (0,external_wp_compose_namespaceObject.useCopyToClipboard)(text);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "secondary",
    ref: ref
  }, children);
}
class ErrorBoundary extends external_wp_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.state = {
      error: null
    };
  }
  componentDidCatch(error) {
    (0,external_wp_hooks_namespaceObject.doAction)('editor.ErrorBoundary.errorLogged', error);
  }
  static getDerivedStateFromError(error) {
    return {
      error
    };
  }
  render() {
    const {
      error
    } = this.state;
    if (!error) {
      return this.props.children;
    }
    const actions = [(0,external_React_.createElement)(CopyButton, {
      key: "copy-post",
      text: getContent
    }, (0,external_wp_i18n_namespaceObject.__)('Copy Post Text')), (0,external_React_.createElement)(CopyButton, {
      key: "copy-error",
      text: error.stack
    }, (0,external_wp_i18n_namespaceObject.__)('Copy Error'))];
    return (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.Warning, {
      className: "editor-error-boundary",
      actions: actions
    }, (0,external_wp_i18n_namespaceObject.__)('The editor has encountered an unexpected error.'));
  }
}
/* harmony default export */ const error_boundary = (ErrorBoundary);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/local-autosave-monitor/index.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



const requestIdleCallback = window.requestIdleCallback ? window.requestIdleCallback : window.requestAnimationFrame;
let hasStorageSupport;

/**
 * Function which returns true if the current environment supports browser
 * sessionStorage, or false otherwise. The result of this function is cached and
 * reused in subsequent invocations.
 */
const hasSessionStorageSupport = () => {
  if (hasStorageSupport !== undefined) {
    return hasStorageSupport;
  }
  try {
    // Private Browsing in Safari 10 and earlier will throw an error when
    // attempting to set into sessionStorage. The test here is intentional in
    // causing a thrown error as condition bailing from local autosave.
    window.sessionStorage.setItem('__wpEditorTestSessionStorage', '');
    window.sessionStorage.removeItem('__wpEditorTestSessionStorage');
    hasStorageSupport = true;
  } catch {
    hasStorageSupport = false;
  }
  return hasStorageSupport;
};

/**
 * Custom hook which manages the creation of a notice prompting the user to
 * restore a local autosave, if one exists.
 */
function useAutosaveNotice() {
  const {
    postId,
    isEditedPostNew,
    hasRemoteAutosave
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    postId: select(store_store).getCurrentPostId(),
    isEditedPostNew: select(store_store).isEditedPostNew(),
    hasRemoteAutosave: !!select(store_store).getEditorSettings().autosave
  }), []);
  const {
    getEditedPostAttribute
  } = (0,external_wp_data_namespaceObject.useSelect)(store_store);
  const {
    createWarningNotice,
    removeNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const {
    editPost,
    resetEditorBlocks
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    let localAutosave = localAutosaveGet(postId, isEditedPostNew);
    if (!localAutosave) {
      return;
    }
    try {
      localAutosave = JSON.parse(localAutosave);
    } catch {
      // Not usable if it can't be parsed.
      return;
    }
    const {
      post_title: title,
      content,
      excerpt
    } = localAutosave;
    const edits = {
      title,
      content,
      excerpt
    };
    {
      // Only display a notice if there is a difference between what has been
      // saved and that which is stored in sessionStorage.
      const hasDifference = Object.keys(edits).some(key => {
        return edits[key] !== getEditedPostAttribute(key);
      });
      if (!hasDifference) {
        // If there is no difference, it can be safely ejected from storage.
        localAutosaveClear(postId, isEditedPostNew);
        return;
      }
    }
    if (hasRemoteAutosave) {
      return;
    }
    const id = 'wpEditorAutosaveRestore';
    createWarningNotice((0,external_wp_i18n_namespaceObject.__)('The backup of this post in your browser is different from the version below.'), {
      id,
      actions: [{
        label: (0,external_wp_i18n_namespaceObject.__)('Restore the backup'),
        onClick() {
          const {
            content: editsContent,
            ...editsWithoutContent
          } = edits;
          editPost(editsWithoutContent);
          resetEditorBlocks((0,external_wp_blocks_namespaceObject.parse)(edits.content));
          removeNotice(id);
        }
      }]
    });
  }, [isEditedPostNew, postId]);
}

/**
 * Custom hook which ejects a local autosave after a successful save occurs.
 */
function useAutosavePurge() {
  const {
    postId,
    isEditedPostNew,
    isDirty,
    isAutosaving,
    didError
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    postId: select(store_store).getCurrentPostId(),
    isEditedPostNew: select(store_store).isEditedPostNew(),
    isDirty: select(store_store).isEditedPostDirty(),
    isAutosaving: select(store_store).isAutosavingPost(),
    didError: select(store_store).didPostSaveRequestFail()
  }), []);
  const lastIsDirty = (0,external_wp_element_namespaceObject.useRef)(isDirty);
  const lastIsAutosaving = (0,external_wp_element_namespaceObject.useRef)(isAutosaving);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!didError && (lastIsAutosaving.current && !isAutosaving || lastIsDirty.current && !isDirty)) {
      localAutosaveClear(postId, isEditedPostNew);
    }
    lastIsDirty.current = isDirty;
    lastIsAutosaving.current = isAutosaving;
  }, [isDirty, isAutosaving, didError]);

  // Once the isEditedPostNew changes from true to false, let's clear the auto-draft autosave.
  const wasEditedPostNew = (0,external_wp_compose_namespaceObject.usePrevious)(isEditedPostNew);
  const prevPostId = (0,external_wp_compose_namespaceObject.usePrevious)(postId);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (prevPostId === postId && wasEditedPostNew && !isEditedPostNew) {
      localAutosaveClear(postId, true);
    }
  }, [isEditedPostNew, postId]);
}
function LocalAutosaveMonitor() {
  const {
    autosave
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const deferredAutosave = (0,external_wp_element_namespaceObject.useCallback)(() => {
    requestIdleCallback(() => autosave({
      local: true
    }));
  }, []);
  useAutosaveNotice();
  useAutosavePurge();
  const localAutosaveInterval = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getEditorSettings().localAutosaveInterval, []);
  return (0,external_React_.createElement)(autosave_monitor, {
    interval: localAutosaveInterval,
    autosave: deferredAutosave
  });
}
/* harmony default export */ const local_autosave_monitor = ((0,external_wp_compose_namespaceObject.ifCondition)(hasSessionStorageSupport)(LocalAutosaveMonitor));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/page-attributes/check.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function PageAttributesCheck({
  children
}) {
  const supportsPageAttributes = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute
    } = select(store_store);
    const {
      getPostType
    } = select(external_wp_coreData_namespaceObject.store);
    const postType = getPostType(getEditedPostAttribute('type'));
    return !!postType?.supports?.['page-attributes'];
  }, []);

  // Only render fields if post type supports page attributes or available templates exist.
  if (!supportsPageAttributes) {
    return null;
  }
  return children;
}
/* harmony default export */ const page_attributes_check = (PageAttributesCheck);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-type-support-check/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * A component which renders its own children only if the current editor post
 * type supports one of the given `supportKeys` prop.
 *
 * @param {Object}            props             Props.
 * @param {Element}           props.children    Children to be rendered if post
 *                                              type supports.
 * @param {(string|string[])} props.supportKeys String or string array of keys
 *                                              to test.
 *
 * @return {Component} The component to be rendered.
 */
function PostTypeSupportCheck({
  children,
  supportKeys
}) {
  const postType = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute
    } = select(store_store);
    const {
      getPostType
    } = select(external_wp_coreData_namespaceObject.store);
    return getPostType(getEditedPostAttribute('type'));
  }, []);
  let isSupported = true;
  if (postType) {
    isSupported = (Array.isArray(supportKeys) ? supportKeys : [supportKeys]).some(key => !!postType.supports[key]);
  }
  if (!isSupported) {
    return null;
  }
  return children;
}
/* harmony default export */ const post_type_support_check = (PostTypeSupportCheck);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/page-attributes/order.js

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function PageAttributesOrder() {
  const order = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getEditedPost;
    return (_select$getEditedPost = select(store_store).getEditedPostAttribute('menu_order')) !== null && _select$getEditedPost !== void 0 ? _select$getEditedPost : 0;
  }, []);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const [orderInput, setOrderInput] = (0,external_wp_element_namespaceObject.useState)(null);
  const setUpdatedOrder = value => {
    setOrderInput(value);
    const newOrder = Number(value);
    if (Number.isInteger(newOrder) && value.trim?.() !== '') {
      editPost({
        menu_order: newOrder
      });
    }
  };
  const value = orderInput !== null && orderInput !== void 0 ? orderInput : order;
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Flex, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.FlexBlock, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalNumberControl, {
    __next40pxDefaultSize: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Order'),
    value: value,
    onChange: setUpdatedOrder,
    labelPosition: "side",
    onBlur: () => {
      setOrderInput(null);
    }
  })));
}
function PageAttributesOrderWithChecks() {
  return (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: "page-attributes"
  }, (0,external_React_.createElement)(PageAttributesOrder, null));
}

// EXTERNAL MODULE: ./node_modules/remove-accents/index.js
var remove_accents = __webpack_require__(9681);
var remove_accents_default = /*#__PURE__*/__webpack_require__.n(remove_accents);
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/terms.js
/**
 * WordPress dependencies
 */


/**
 * Returns terms in a tree form.
 *
 * @param {Array} flatTerms Array of terms in flat format.
 *
 * @return {Array} Array of terms in tree format.
 */
function buildTermsTree(flatTerms) {
  const flatTermsWithParentAndChildren = flatTerms.map(term => {
    return {
      children: [],
      parent: null,
      ...term
    };
  });

  // All terms should have a `parent` because we're about to index them by it.
  if (flatTermsWithParentAndChildren.some(({
    parent
  }) => parent === null)) {
    return flatTermsWithParentAndChildren;
  }
  const termsByParent = flatTermsWithParentAndChildren.reduce((acc, term) => {
    const {
      parent
    } = term;
    if (!acc[parent]) {
      acc[parent] = [];
    }
    acc[parent].push(term);
    return acc;
  }, {});
  const fillWithChildren = terms => {
    return terms.map(term => {
      const children = termsByParent[term.id];
      return {
        ...term,
        children: children && children.length ? fillWithChildren(children) : []
      };
    });
  };
  return fillWithChildren(termsByParent['0'] || []);
}
const unescapeString = arg => {
  return (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(arg);
};

/**
 * Returns a term object with name unescaped.
 *
 * @param {Object} term The term object to unescape.
 *
 * @return {Object} Term object with name property unescaped.
 */
const unescapeTerm = term => {
  return {
    ...term,
    name: unescapeString(term.name)
  };
};

/**
 * Returns an array of term objects with names unescaped.
 * The unescape of each term is performed using the unescapeTerm function.
 *
 * @param {Object[]} terms Array of term objects to unescape.
 *
 * @return {Object[]} Array of term objects unescaped.
 */
const unescapeTerms = terms => {
  return (terms !== null && terms !== void 0 ? terms : []).map(unescapeTerm);
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/page-attributes/parent.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


function getTitle(post) {
  return post?.title?.rendered ? (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(post.title.rendered) : `#${post.id} (${(0,external_wp_i18n_namespaceObject.__)('no title')})`;
}
const getItemPriority = (name, searchValue) => {
  const normalizedName = remove_accents_default()(name || '').toLowerCase();
  const normalizedSearch = remove_accents_default()(searchValue || '').toLowerCase();
  if (normalizedName === normalizedSearch) {
    return 0;
  }
  if (normalizedName.startsWith(normalizedSearch)) {
    return normalizedName.length;
  }
  return Infinity;
};
function PageAttributesParent() {
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const [fieldValue, setFieldValue] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    isHierarchical,
    parentPostId,
    parentPostTitle,
    pageItems
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _pType$hierarchical;
    const {
      getPostType,
      getEntityRecords,
      getEntityRecord
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      getCurrentPostId,
      getEditedPostAttribute
    } = select(store_store);
    const postTypeSlug = getEditedPostAttribute('type');
    const pageId = getEditedPostAttribute('parent');
    const pType = getPostType(postTypeSlug);
    const postId = getCurrentPostId();
    const postIsHierarchical = (_pType$hierarchical = pType?.hierarchical) !== null && _pType$hierarchical !== void 0 ? _pType$hierarchical : false;
    const query = {
      per_page: 100,
      exclude: postId,
      parent_exclude: postId,
      orderby: 'menu_order',
      order: 'asc',
      _fields: 'id,title,parent'
    };

    // Perform a search when the field is changed.
    if (!!fieldValue) {
      query.search = fieldValue;
    }
    const parentPost = pageId ? getEntityRecord('postType', postTypeSlug, pageId) : null;
    return {
      isHierarchical: postIsHierarchical,
      parentPostId: pageId,
      parentPostTitle: parentPost ? getTitle(parentPost) : '',
      pageItems: postIsHierarchical ? getEntityRecords('postType', postTypeSlug, query) : null
    };
  }, [fieldValue]);
  const parentOptions = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const getOptionsFromTree = (tree, level = 0) => {
      const mappedNodes = tree.map(treeNode => [{
        value: treeNode.id,
        label: '— '.repeat(level) + (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(treeNode.name),
        rawName: treeNode.name
      }, ...getOptionsFromTree(treeNode.children || [], level + 1)]);
      const sortedNodes = mappedNodes.sort(([a], [b]) => {
        const priorityA = getItemPriority(a.rawName, fieldValue);
        const priorityB = getItemPriority(b.rawName, fieldValue);
        return priorityA >= priorityB ? 1 : -1;
      });
      return sortedNodes.flat();
    };
    if (!pageItems) {
      return [];
    }
    let tree = pageItems.map(item => ({
      id: item.id,
      parent: item.parent,
      name: getTitle(item)
    }));

    // Only build a hierarchical tree when not searching.
    if (!fieldValue) {
      tree = buildTermsTree(tree);
    }
    const opts = getOptionsFromTree(tree);

    // Ensure the current parent is in the options list.
    const optsHasParent = opts.find(item => item.value === parentPostId);
    if (parentPostTitle && !optsHasParent) {
      opts.unshift({
        value: parentPostId,
        label: parentPostTitle
      });
    }
    return opts;
  }, [pageItems, fieldValue, parentPostTitle, parentPostId]);
  if (!isHierarchical) {
    return null;
  }
  /**
   * Handle user input.
   *
   * @param {string} inputValue The current value of the input field.
   */
  const handleKeydown = inputValue => {
    setFieldValue(inputValue);
  };

  /**
   * Handle author selection.
   *
   * @param {Object} selectedPostId The selected Author.
   */
  const handleChange = selectedPostId => {
    editPost({
      parent: selectedPostId
    });
  };
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.ComboboxControl, {
    __nextHasNoMarginBottom: true,
    __next40pxDefaultSize: true,
    className: "editor-page-attributes__parent",
    label: (0,external_wp_i18n_namespaceObject.__)('Parent'),
    value: parentPostId,
    options: parentOptions,
    onFilterValueChange: (0,external_wp_compose_namespaceObject.debounce)(handleKeydown, 300),
    onChange: handleChange
  });
}
/* harmony default export */ const page_attributes_parent = (PageAttributesParent);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/page-attributes/panel.js

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




const PANEL_NAME = 'page-attributes';
function PageAttributesPanel() {
  var _postType$labels$attr;
  const {
    isEnabled,
    isOpened,
    postType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute,
      isEditorPanelEnabled,
      isEditorPanelOpened
    } = select(store_store);
    const {
      getPostType
    } = select(external_wp_coreData_namespaceObject.store);
    return {
      isEnabled: isEditorPanelEnabled(PANEL_NAME),
      isOpened: isEditorPanelOpened(PANEL_NAME),
      postType: getPostType(getEditedPostAttribute('type'))
    };
  }, []);
  const {
    toggleEditorPanelOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  if (!isEnabled || !postType) {
    return null;
  }
  const onTogglePanel = (...args) => toggleEditorPanelOpened(PANEL_NAME, ...args);
  return (0,external_React_.createElement)(page_attributes_check, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (_postType$labels$attr = postType?.labels?.attributes) !== null && _postType$labels$attr !== void 0 ? _postType$labels$attr : (0,external_wp_i18n_namespaceObject.__)('Page attributes'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_React_.createElement)(page_attributes_parent, null), (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_React_.createElement)(PageAttributesOrderWithChecks, null))));
}
/* harmony default export */ const panel = (PageAttributesPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/add-template.js

/**
 * WordPress dependencies
 */

const addTemplate = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M18.5 5.5V8H20V5.5H22.5V4H20V1.5H18.5V4H16V5.5H18.5ZM13.9624 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V10.0391H18.5V18C18.5 18.2761 18.2761 18.5 18 18.5H10L10 10.4917L16.4589 10.5139L16.4641 9.01389L5.5 8.97618V6C5.5 5.72386 5.72386 5.5 6 5.5H13.9624V4ZM5.5 10.4762V18C5.5 18.2761 5.72386 18.5 6 18.5H8.5L8.5 10.4865L5.5 10.4762Z"
}));
/* harmony default export */ const add_template = (addTemplate);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-template/create-new-template-modal.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


const DEFAULT_TITLE = (0,external_wp_i18n_namespaceObject.__)('Custom Template');
function CreateNewTemplateModal({
  onClose
}) {
  const {
    defaultBlockTemplate,
    onNavigateToEntityRecord
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings,
      getCurrentTemplateId
    } = select(store_store);
    return {
      defaultBlockTemplate: getEditorSettings().defaultBlockTemplate,
      onNavigateToEntityRecord: getEditorSettings().onNavigateToEntityRecord,
      getTemplateId: getCurrentTemplateId
    };
  });
  const {
    createTemplate
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)('');
  const [isBusy, setIsBusy] = (0,external_wp_element_namespaceObject.useState)(false);
  const cancel = () => {
    setTitle('');
    onClose();
  };
  const submit = async event => {
    event.preventDefault();
    if (isBusy) {
      return;
    }
    setIsBusy(true);
    const newTemplateContent = defaultBlockTemplate !== null && defaultBlockTemplate !== void 0 ? defaultBlockTemplate : (0,external_wp_blocks_namespaceObject.serialize)([(0,external_wp_blocks_namespaceObject.createBlock)('core/group', {
      tagName: 'header',
      layout: {
        inherit: true
      }
    }, [(0,external_wp_blocks_namespaceObject.createBlock)('core/site-title'), (0,external_wp_blocks_namespaceObject.createBlock)('core/site-tagline')]), (0,external_wp_blocks_namespaceObject.createBlock)('core/separator'), (0,external_wp_blocks_namespaceObject.createBlock)('core/group', {
      tagName: 'main'
    }, [(0,external_wp_blocks_namespaceObject.createBlock)('core/group', {
      layout: {
        inherit: true
      }
    }, [(0,external_wp_blocks_namespaceObject.createBlock)('core/post-title')]), (0,external_wp_blocks_namespaceObject.createBlock)('core/post-content', {
      layout: {
        inherit: true
      }
    })])]);
    const newTemplate = await createTemplate({
      slug: (0,external_wp_url_namespaceObject.cleanForSlug)(title || DEFAULT_TITLE),
      content: newTemplateContent,
      title: title || DEFAULT_TITLE
    });
    setIsBusy(false);
    onNavigateToEntityRecord({
      postId: newTemplate.id,
      postType: 'wp_template'
    });
    cancel();
  };
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Modal, {
    title: (0,external_wp_i18n_namespaceObject.__)('Create custom template'),
    onRequestClose: cancel
  }, (0,external_React_.createElement)("form", {
    className: "editor-post-template__create-form",
    onSubmit: submit
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: "3"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Name'),
    value: title,
    onChange: setTitle,
    placeholder: DEFAULT_TITLE,
    disabled: isBusy,
    help: (0,external_wp_i18n_namespaceObject.__)('Describe the template, e.g. "Post with sidebar". A custom template can be manually applied to any post or page.')
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "right"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: cancel
  }, (0,external_wp_i18n_namespaceObject.__)('Cancel')), (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    type: "submit",
    isBusy: isBusy,
    "aria-disabled": isBusy
  }, (0,external_wp_i18n_namespaceObject.__)('Create'))))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-template/hooks.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function useEditedPostContext() {
  return (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getCurrentPostId,
      getCurrentPostType
    } = select(store_store);
    return {
      postId: getCurrentPostId(),
      postType: getCurrentPostType()
    };
  }, []);
}
function useAllowSwitchingTemplates() {
  const {
    postType,
    postId
  } = useEditedPostContext();
  return (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEntityRecord,
      getEntityRecords
    } = select(external_wp_coreData_namespaceObject.store);
    const siteSettings = getEntityRecord('root', 'site');
    const templates = getEntityRecords('postType', 'wp_template', {
      per_page: -1
    });
    const isPostsPage = +postId === siteSettings?.page_for_posts;
    // If current page is set front page or posts page, we also need
    // to check if the current theme has a template for it. If not
    const isFrontPage = postType === 'page' && +postId === siteSettings?.page_on_front && templates?.some(({
      slug
    }) => slug === 'front-page');
    return !isPostsPage && !isFrontPage;
  }, [postId, postType]);
}
function useTemplates(postType) {
  return (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getEntityRecords('postType', 'wp_template', {
    per_page: -1,
    post_type: postType
  }), [postType]);
}
function useAvailableTemplates(postType) {
  const currentTemplateSlug = useCurrentTemplateSlug();
  const allowSwitchingTemplate = useAllowSwitchingTemplates();
  const templates = useTemplates(postType);
  return (0,external_wp_element_namespaceObject.useMemo)(() => allowSwitchingTemplate && templates?.filter(template => template.is_custom && template.slug !== currentTemplateSlug && !!template.content.raw // Skip empty templates.
  ), [templates, currentTemplateSlug, allowSwitchingTemplate]);
}
function useCurrentTemplateSlug() {
  const {
    postType,
    postId
  } = useEditedPostContext();
  const templates = useTemplates(postType);
  const entityTemplate = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const post = select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord('postType', postType, postId);
    return post?.template;
  }, [postType, postId]);
  if (!entityTemplate) {
    return;
  }
  // If a page has a `template` set and is not included in the list
  // of the theme's templates, do not return it, in order to resolve
  // to the current theme's default template.
  return templates?.find(template => template.slug === entityTemplate)?.slug;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-template/classic-theme.js

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */



const POPOVER_PROPS = {
  className: 'editor-post-template__dropdown',
  placement: 'bottom-start'
};
function PostTemplateToggle({
  isOpen,
  onClick
}) {
  const templateTitle = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const templateSlug = select(store_store).getEditedPostAttribute('template');
    const {
      supportsTemplateMode,
      availableTemplates
    } = select(store_store).getEditorSettings();
    if (!supportsTemplateMode && availableTemplates[templateSlug]) {
      return availableTemplates[templateSlug];
    }
    const template = select(external_wp_coreData_namespaceObject.store).canUser('create', 'templates') && select(store_store).getCurrentTemplateId();
    return template?.title || template?.slug || availableTemplates?.[templateSlug];
  }, []);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    __next40pxDefaultSize: true,
    className: "edit-post-post-template__toggle",
    variant: "tertiary",
    "aria-expanded": isOpen,
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Template options'),
    onClick: onClick
  }, templateTitle !== null && templateTitle !== void 0 ? templateTitle : (0,external_wp_i18n_namespaceObject.__)('Default template'));
}
function PostTemplateDropdownContent({
  onClose
}) {
  var _options$find, _selectedOption$value;
  const allowSwitchingTemplate = useAllowSwitchingTemplates();
  const {
    availableTemplates,
    fetchedTemplates,
    selectedTemplateSlug,
    canCreate,
    canEdit,
    currentTemplateId,
    onNavigateToEntityRecord,
    getEditorSettings
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      canUser,
      getEntityRecords
    } = select(external_wp_coreData_namespaceObject.store);
    const editorSettings = select(store_store).getEditorSettings();
    const canCreateTemplates = canUser('create', 'templates');
    const _currentTemplateId = select(store_store).getCurrentTemplateId();
    return {
      availableTemplates: editorSettings.availableTemplates,
      fetchedTemplates: canCreateTemplates ? getEntityRecords('postType', 'wp_template', {
        post_type: select(store_store).getCurrentPostType(),
        per_page: -1
      }) : undefined,
      selectedTemplateSlug: select(store_store).getEditedPostAttribute('template'),
      canCreate: allowSwitchingTemplate && canCreateTemplates && editorSettings.supportsTemplateMode,
      canEdit: allowSwitchingTemplate && canCreateTemplates && editorSettings.supportsTemplateMode && !!_currentTemplateId,
      currentTemplateId: _currentTemplateId,
      onNavigateToEntityRecord: editorSettings.onNavigateToEntityRecord,
      getEditorSettings: select(store_store).getEditorSettings
    };
  }, [allowSwitchingTemplate]);
  const options = (0,external_wp_element_namespaceObject.useMemo)(() => Object.entries({
    ...availableTemplates,
    ...Object.fromEntries((fetchedTemplates !== null && fetchedTemplates !== void 0 ? fetchedTemplates : []).map(({
      slug,
      title
    }) => [slug, title.rendered]))
  }).map(([slug, title]) => ({
    value: slug,
    label: title
  })), [availableTemplates, fetchedTemplates]);
  const selectedOption = (_options$find = options.find(option => option.value === selectedTemplateSlug)) !== null && _options$find !== void 0 ? _options$find : options.find(option => !option.value); // The default option has '' value.

  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    createSuccessNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const [isCreateModalOpen, setIsCreateModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  return (0,external_React_.createElement)("div", {
    className: "editor-post-template__classic-theme-dropdown"
  }, (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.__experimentalInspectorPopoverHeader, {
    title: (0,external_wp_i18n_namespaceObject.__)('Template'),
    help: (0,external_wp_i18n_namespaceObject.__)('Templates define the way content is displayed when viewing your site.'),
    actions: canCreate ? [{
      icon: add_template,
      label: (0,external_wp_i18n_namespaceObject.__)('Add template'),
      onClick: () => setIsCreateModalOpen(true)
    }] : [],
    onClose: onClose
  }), !allowSwitchingTemplate ? (0,external_React_.createElement)(external_wp_components_namespaceObject.Notice, {
    status: "warning",
    isDismissible: false
  }, (0,external_wp_i18n_namespaceObject.__)('The posts page template cannot be changed.')) : (0,external_React_.createElement)(external_wp_components_namespaceObject.SelectControl, {
    __next40pxDefaultSize: true,
    __nextHasNoMarginBottom: true,
    hideLabelFromVision: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Template'),
    value: (_selectedOption$value = selectedOption?.value) !== null && _selectedOption$value !== void 0 ? _selectedOption$value : '',
    options: options,
    onChange: slug => editPost({
      template: slug || ''
    })
  }), canEdit && onNavigateToEntityRecord && (0,external_React_.createElement)("p", null, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "link",
    onClick: () => {
      onNavigateToEntityRecord({
        postId: currentTemplateId,
        postType: 'wp_template'
      });
      onClose();
      createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Editing template. Changes made here affect all posts and pages that use the template.'), {
        type: 'snackbar',
        actions: [{
          label: (0,external_wp_i18n_namespaceObject.__)('Go back'),
          onClick: () => getEditorSettings().onNavigateToPreviousEntityRecord()
        }]
      });
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Edit template'))), isCreateModalOpen && (0,external_React_.createElement)(CreateNewTemplateModal, {
    onClose: () => setIsCreateModalOpen(false)
  }));
}
function ClassicThemeControl() {
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: POPOVER_PROPS,
    focusOnMount: true,
    renderToggle: ({
      isOpen,
      onToggle
    }) => (0,external_React_.createElement)(PostTemplateToggle, {
      isOpen: isOpen,
      onClick: onToggle
    }),
    renderContent: ({
      onClose
    }) => (0,external_React_.createElement)(PostTemplateDropdownContent, {
      onClose: onClose
    })
  });
}
/* harmony default export */ const classic_theme = (ClassicThemeControl);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js

/**
 * WordPress dependencies
 */

const check_check = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"
}));
/* harmony default export */ const library_check = (check_check);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-template/swap-template-button.js

/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */

function SwapTemplateButton({
  onClick
}) {
  const [showModal, setShowModal] = (0,external_wp_element_namespaceObject.useState)(false);
  const onClose = (0,external_wp_element_namespaceObject.useCallback)(() => {
    setShowModal(false);
  }, []);
  const {
    postType,
    postId
  } = useEditedPostContext();
  const availableTemplates = useAvailableTemplates(postType);
  const {
    editEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  if (!availableTemplates?.length) {
    return null;
  }
  const onTemplateSelect = async template => {
    editEntityRecord('postType', postType, postId, {
      template: template.name
    }, {
      undoIgnore: true
    });
    onClose(); // Close the template suggestions modal first.
    onClick();
  };
  return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => setShowModal(true)
  }, (0,external_wp_i18n_namespaceObject.__)('Swap template')), showModal && (0,external_React_.createElement)(external_wp_components_namespaceObject.Modal, {
    title: (0,external_wp_i18n_namespaceObject.__)('Choose a template'),
    onRequestClose: onClose,
    overlayClassName: "editor-post-template__swap-template-modal",
    isFullScreen: true
  }, (0,external_React_.createElement)("div", {
    className: "editor-post-template__swap-template-modal-content"
  }, (0,external_React_.createElement)(TemplatesList, {
    postType: postType,
    onSelect: onTemplateSelect
  }))));
}
function TemplatesList({
  postType,
  onSelect
}) {
  const availableTemplates = useAvailableTemplates(postType);
  const templatesAsPatterns = (0,external_wp_element_namespaceObject.useMemo)(() => availableTemplates.map(template => ({
    name: template.slug,
    blocks: (0,external_wp_blocks_namespaceObject.parse)(template.content.raw),
    title: (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(template.title.rendered),
    id: template.id
  })), [availableTemplates]);
  const shownTemplates = (0,external_wp_compose_namespaceObject.useAsyncList)(templatesAsPatterns);
  return (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.__experimentalBlockPatternsList, {
    label: (0,external_wp_i18n_namespaceObject.__)('Templates'),
    blockPatterns: templatesAsPatterns,
    shownPatterns: shownTemplates,
    onClickPattern: onSelect
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-template/reset-default-template.js

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */

function ResetDefaultTemplate({
  onClick
}) {
  const currentTemplateSlug = useCurrentTemplateSlug();
  const allowSwitchingTemplate = useAllowSwitchingTemplates();
  const {
    postType,
    postId
  } = useEditedPostContext();
  const {
    editEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  // The default template in a post is indicated by an empty string.
  if (!currentTemplateSlug || !allowSwitchingTemplate) {
    return null;
  }
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      editEntityRecord('postType', postType, postId, {
        template: ''
      }, {
        undoIgnore: true
      });
      onClick();
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Use default template'));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-template/create-new-template.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function CreateNewTemplate({
  onClick
}) {
  const {
    canCreateTemplates
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      canUser
    } = select(external_wp_coreData_namespaceObject.store);
    return {
      canCreateTemplates: canUser('create', 'templates')
    };
  }, []);
  const [isCreateModalOpen, setIsCreateModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  const allowSwitchingTemplate = useAllowSwitchingTemplates();

  // The default template in a post is indicated by an empty string.
  if (!canCreateTemplates || !allowSwitchingTemplate) {
    return null;
  }
  return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      setIsCreateModalOpen(true);
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Create new template')), isCreateModalOpen && (0,external_React_.createElement)(CreateNewTemplateModal, {
    onClose: () => {
      setIsCreateModalOpen(false);
      onClick();
    }
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-template/block-theme.js

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */





const block_theme_POPOVER_PROPS = {
  className: 'editor-post-template__dropdown',
  placement: 'bottom-start'
};
function BlockThemeControl({
  id
}) {
  const {
    isTemplateHidden,
    onNavigateToEntityRecord,
    getEditorSettings,
    hasGoBack
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getRenderingMode,
      getEditorSettings: _getEditorSettings
    } = unlock(select(store_store));
    const editorSettings = _getEditorSettings();
    return {
      isTemplateHidden: getRenderingMode() === 'post-only',
      onNavigateToEntityRecord: editorSettings.onNavigateToEntityRecord,
      getEditorSettings: _getEditorSettings,
      hasGoBack: editorSettings.hasOwnProperty('onNavigateToPreviousEntityRecord')
    };
  }, []);
  const {
    editedRecord: template,
    hasResolved
  } = (0,external_wp_coreData_namespaceObject.useEntityRecord)('postType', 'wp_template', id);
  const {
    createSuccessNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const {
    setRenderingMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  if (!hasResolved) {
    return null;
  }

  // The site editor does not have a `onNavigateToPreviousEntityRecord` setting as it uses its own routing
  // and assigns its own backlink to focusMode pages.
  const notificationAction = hasGoBack ? [{
    label: (0,external_wp_i18n_namespaceObject.__)('Go back'),
    onClick: () => getEditorSettings().onNavigateToPreviousEntityRecord()
  }] : undefined;
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.DropdownMenu, {
    popoverProps: block_theme_POPOVER_PROPS,
    focusOnMount: true,
    toggleProps: {
      __next40pxDefaultSize: true,
      variant: 'tertiary'
    },
    label: (0,external_wp_i18n_namespaceObject.__)('Template options'),
    text: (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(template.title),
    icon: null
  }, ({
    onClose
  }) => (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      onNavigateToEntityRecord({
        postId: template.id,
        postType: 'wp_template'
      });
      onClose();
      createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Editing template. Changes made here affect all posts and pages that use the template.'), {
        type: 'snackbar',
        actions: notificationAction
      });
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Edit template')), (0,external_React_.createElement)(SwapTemplateButton, {
    onClick: onClose
  }), (0,external_React_.createElement)(ResetDefaultTemplate, {
    onClick: onClose
  }), (0,external_React_.createElement)(CreateNewTemplate, {
    onClick: onClose
  })), (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuItem, {
    icon: !isTemplateHidden ? library_check : undefined,
    isSelected: !isTemplateHidden,
    role: "menuitemcheckbox",
    onClick: () => {
      setRenderingMode(isTemplateHidden ? 'template-locked' : 'post-only');
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Template preview')))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-panel-row/index.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


const PostPanelRow = (0,external_wp_element_namespaceObject.forwardRef)(({
  className,
  label,
  children
}, ref) => {
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    className: classnames_default()('editor-post-panel__row', className),
    ref: ref
  }, label && (0,external_React_.createElement)("div", {
    className: "editor-post-panel__row-label"
  }, label), (0,external_React_.createElement)("div", {
    className: "editor-post-panel__row-control"
  }, children));
});
/* harmony default export */ const post_panel_row = (PostPanelRow);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-template/panel.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




function PostTemplatePanel() {
  const {
    templateId,
    isBlockTheme
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getCurrentTemplateId,
      getEditorSettings
    } = select(store_store);
    return {
      templateId: getCurrentTemplateId(),
      isBlockTheme: getEditorSettings().__unstableIsBlockBasedTheme
    };
  }, []);
  const isVisible = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$canUser;
    const postTypeSlug = select(store_store).getCurrentPostType();
    const postType = select(external_wp_coreData_namespaceObject.store).getPostType(postTypeSlug);
    if (!postType?.viewable) {
      return false;
    }
    const settings = select(store_store).getEditorSettings();
    const hasTemplates = !!settings.availableTemplates && Object.keys(settings.availableTemplates).length > 0;
    if (hasTemplates) {
      return true;
    }
    if (!settings.supportsTemplateMode) {
      return false;
    }
    const canCreateTemplates = (_select$canUser = select(external_wp_coreData_namespaceObject.store).canUser('create', 'templates')) !== null && _select$canUser !== void 0 ? _select$canUser : false;
    return canCreateTemplates;
  }, []);
  const canViewTemplates = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$canUser2;
    return (_select$canUser2 = select(external_wp_coreData_namespaceObject.store).canUser('read', 'templates')) !== null && _select$canUser2 !== void 0 ? _select$canUser2 : false;
  }, []);
  if ((!isBlockTheme || !canViewTemplates) && isVisible) {
    return (0,external_React_.createElement)(post_panel_row, {
      label: (0,external_wp_i18n_namespaceObject.__)('Template')
    }, (0,external_React_.createElement)(classic_theme, null));
  }
  if (isBlockTheme && !!templateId) {
    return (0,external_React_.createElement)(post_panel_row, {
      label: (0,external_wp_i18n_namespaceObject.__)('Template')
    }, (0,external_React_.createElement)(BlockThemeControl, {
      id: templateId
    }));
  }
  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/constants.js
const BASE_QUERY = {
  _fields: 'id,name',
  context: 'view' // Allows non-admins to perform requests.
};
const AUTHORS_QUERY = {
  who: 'authors',
  per_page: 50,
  ...BASE_QUERY
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/hook.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function useAuthorsQuery(search) {
  const {
    authorId,
    authors,
    postAuthor
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getUser,
      getUsers
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      getEditedPostAttribute
    } = select(store_store);
    const _authorId = getEditedPostAttribute('author');
    const query = {
      ...AUTHORS_QUERY
    };
    if (search) {
      query.search = search;
    }
    return {
      authorId: _authorId,
      authors: getUsers(query),
      postAuthor: getUser(_authorId, BASE_QUERY)
    };
  }, [search]);
  const authorOptions = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const fetchedAuthors = (authors !== null && authors !== void 0 ? authors : []).map(author => {
      return {
        value: author.id,
        label: (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(author.name)
      };
    });

    // Ensure the current author is included in the dropdown list.
    const foundAuthor = fetchedAuthors.findIndex(({
      value
    }) => postAuthor?.id === value);
    if (foundAuthor < 0 && postAuthor) {
      return [{
        value: postAuthor.id,
        label: (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(postAuthor.name)
      }, ...fetchedAuthors];
    }
    return fetchedAuthors;
  }, [authors, postAuthor]);
  return {
    authorId,
    authorOptions
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/combobox.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function PostAuthorCombobox() {
  const [fieldValue, setFieldValue] = (0,external_wp_element_namespaceObject.useState)();
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    authorId,
    authorOptions
  } = useAuthorsQuery(fieldValue);

  /**
   * Handle author selection.
   *
   * @param {number} postAuthorId The selected Author.
   */
  const handleSelect = postAuthorId => {
    if (!postAuthorId) {
      return;
    }
    editPost({
      author: postAuthorId
    });
  };

  /**
   * Handle user input.
   *
   * @param {string} inputValue The current value of the input field.
   */
  const handleKeydown = inputValue => {
    setFieldValue(inputValue);
  };
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.ComboboxControl, {
    __nextHasNoMarginBottom: true,
    __next40pxDefaultSize: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Author'),
    options: authorOptions,
    value: authorId,
    onFilterValueChange: (0,external_wp_compose_namespaceObject.debounce)(handleKeydown, 300),
    onChange: handleSelect,
    allowReset: false
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/select.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function PostAuthorSelect() {
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    authorId,
    authorOptions
  } = useAuthorsQuery();
  const setAuthorId = value => {
    const author = Number(value);
    editPost({
      author
    });
  };
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.SelectControl, {
    __next40pxDefaultSize: true,
    __nextHasNoMarginBottom: true,
    className: "post-author-selector",
    label: (0,external_wp_i18n_namespaceObject.__)('Author'),
    options: authorOptions,
    onChange: setAuthorId,
    value: authorId
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/index.js

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



const minimumUsersForCombobox = 25;
function PostAuthor() {
  const showCombobox = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const authors = select(external_wp_coreData_namespaceObject.store).getUsers(AUTHORS_QUERY);
    return authors?.length >= minimumUsersForCombobox;
  }, []);
  if (showCombobox) {
    return (0,external_React_.createElement)(PostAuthorCombobox, null);
  }
  return (0,external_React_.createElement)(PostAuthorSelect, null);
}
/* harmony default export */ const post_author = (PostAuthor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/check.js

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function PostAuthorCheck({
  children
}) {
  const {
    hasAssignAuthorAction,
    hasAuthors
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _post$_links$wpActio;
    const post = select(store_store).getCurrentPost();
    const authors = select(external_wp_coreData_namespaceObject.store).getUsers(AUTHORS_QUERY);
    return {
      hasAssignAuthorAction: (_post$_links$wpActio = post._links?.['wp:action-assign-author']) !== null && _post$_links$wpActio !== void 0 ? _post$_links$wpActio : false,
      hasAuthors: authors?.length >= 1
    };
  }, []);
  if (!hasAssignAuthorAction || !hasAuthors) {
    return null;
  }
  return (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: "author"
  }, children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/panel.js

/**
 * Internal dependencies
 */



function panel_PostAuthor() {
  return (0,external_React_.createElement)(PostAuthorCheck, null, (0,external_React_.createElement)(post_panel_row, {
    className: "editor-post-author__panel"
  }, (0,external_React_.createElement)(post_author, null)));
}
/* harmony default export */ const post_author_panel = (panel_PostAuthor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-comments/index.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function PostComments() {
  const commentStatus = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getEditedPost;
    return (_select$getEditedPost = select(store_store).getEditedPostAttribute('comment_status')) !== null && _select$getEditedPost !== void 0 ? _select$getEditedPost : 'open';
  }, []);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const onToggleComments = () => editPost({
    comment_status: commentStatus === 'open' ? 'closed' : 'open'
  });
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Allow comments'),
    checked: commentStatus === 'open',
    onChange: onToggleComments
  });
}
/* harmony default export */ const post_comments = (PostComments);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-pingbacks/index.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function PostPingbacks() {
  const pingStatus = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getEditedPost;
    return (_select$getEditedPost = select(store_store).getEditedPostAttribute('ping_status')) !== null && _select$getEditedPost !== void 0 ? _select$getEditedPost : 'open';
  }, []);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const onTogglePingback = () => editPost({
    ping_status: pingStatus === 'open' ? 'closed' : 'open'
  });
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Allow pingbacks & trackbacks'),
    checked: pingStatus === 'open',
    onChange: onTogglePingback
  });
}
/* harmony default export */ const post_pingbacks = (PostPingbacks);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-discussion/panel.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




const panel_PANEL_NAME = 'discussion-panel';
function PostDiscussionPanel() {
  const {
    isEnabled,
    isOpened
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isEditorPanelEnabled,
      isEditorPanelOpened
    } = select(store_store);
    return {
      isEnabled: isEditorPanelEnabled(panel_PANEL_NAME),
      isOpened: isEditorPanelOpened(panel_PANEL_NAME)
    };
  }, []);
  const {
    toggleEditorPanelOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  if (!isEnabled) {
    return null;
  }
  return (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: ['comments', 'trackbacks']
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_wp_i18n_namespaceObject.__)('Discussion'),
    opened: isOpened,
    onToggle: () => toggleEditorPanelOpened(panel_PANEL_NAME)
  }, (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: "comments"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_React_.createElement)(post_comments, null))), (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: "trackbacks"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelRow, null, (0,external_React_.createElement)(post_pingbacks, null)))));
}
/* harmony default export */ const post_discussion_panel = (PostDiscussionPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-excerpt/index.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function PostExcerpt() {
  const excerpt = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getEditedPostAttribute('excerpt'), []);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_React_.createElement)("div", {
    className: "editor-post-excerpt"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.TextareaControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Write an excerpt (optional)'),
    className: "editor-post-excerpt__textarea",
    onChange: value => editPost({
      excerpt: value
    }),
    value: excerpt
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.ExternalLink, {
    href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/documentation/article/page-post-settings-sidebar/#excerpt')
  }, (0,external_wp_i18n_namespaceObject.__)('Learn more about manual excerpts')));
}
/* harmony default export */ const post_excerpt = (PostExcerpt);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-excerpt/check.js

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function PostExcerptCheck({
  children
}) {
  const postType = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute
    } = select(store_store);
    return getEditedPostAttribute('type');
  }, []);

  // This special case is unfortunate, but the REST API of wp_template and wp_template_part
  // support the excerpt field throught the "description" field rather than "excerpt" which means
  // the default ExcerptPanel won't work for these.
  if (['wp_template', 'wp_template_part'].includes(postType)) {
    return null;
  }
  return (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: "excerpt"
  }, children);
}
/* harmony default export */ const post_excerpt_check = (PostExcerptCheck);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-excerpt/plugin.js

/**
 * Defines as extensibility slot for the Excerpt panel.
 */

/**
 * WordPress dependencies
 */

const {
  Fill,
  Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('PluginPostExcerpt');

/**
 * Renders a post excerpt panel in the post sidebar.
 *
 * @param {Object}  props             Component properties.
 * @param {string}  [props.className] An optional class name added to the row.
 * @param {Element} props.children    Children to be rendered.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPostExcerpt = wp.editPost.PluginPostExcerpt;
 *
 * function MyPluginPostExcerpt() {
 * 	return React.createElement(
 * 		PluginPostExcerpt,
 * 		{
 * 			className: 'my-plugin-post-excerpt',
 * 		},
 * 		__( 'Post excerpt custom content' )
 * 	)
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginPostExcerpt } from '@wordpress/edit-post';
 *
 * const MyPluginPostExcerpt = () => (
 * 	<PluginPostExcerpt className="my-plugin-post-excerpt">
 * 		{ __( 'Post excerpt custom content' ) }
 * 	</PluginPostExcerpt>
 * );
 * ```
 *
 * @return {Component} The component to be rendered.
 */
const PluginPostExcerpt = ({
  children,
  className
}) => {
  return (0,external_React_.createElement)(Fill, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelRow, {
    className: className
  }, children));
};
PluginPostExcerpt.Slot = Slot;
/* harmony default export */ const post_excerpt_plugin = (PluginPostExcerpt);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-excerpt/panel.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */





/**
 * Module Constants
 */
const post_excerpt_panel_PANEL_NAME = 'post-excerpt';
function PostExcerptPanel() {
  const {
    isOpened,
    isEnabled
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isEditorPanelOpened,
      isEditorPanelEnabled
    } = select(store_store);
    return {
      isOpened: isEditorPanelOpened(post_excerpt_panel_PANEL_NAME),
      isEnabled: isEditorPanelEnabled(post_excerpt_panel_PANEL_NAME)
    };
  }, []);
  const {
    toggleEditorPanelOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const toggleExcerptPanel = () => toggleEditorPanelOpened(post_excerpt_panel_PANEL_NAME);
  if (!isEnabled) {
    return null;
  }
  return (0,external_React_.createElement)(post_excerpt_check, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_wp_i18n_namespaceObject.__)('Excerpt'),
    opened: isOpened,
    onToggle: toggleExcerptPanel
  }, (0,external_React_.createElement)(post_excerpt_plugin.Slot, null, fills => (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(post_excerpt, null), fills))));
}

;// CONCATENATED MODULE: external ["wp","blob"]
const external_wp_blob_namespaceObject = window["wp"]["blob"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/theme-support-check/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function ThemeSupportCheck({
  themeSupports,
  children,
  postType,
  supportKeys
}) {
  const isSupported = (Array.isArray(supportKeys) ? supportKeys : [supportKeys]).some(key => {
    var _themeSupports$key;
    const supported = (_themeSupports$key = themeSupports?.[key]) !== null && _themeSupports$key !== void 0 ? _themeSupports$key : false;
    // 'post-thumbnails' can be boolean or an array of post types.
    // In the latter case, we need to verify `postType` exists
    // within `supported`. If `postType` isn't passed, then the check
    // should fail.
    if ('post-thumbnails' === key && Array.isArray(supported)) {
      return supported.includes(postType);
    }
    return supported;
  });
  if (!isSupported) {
    return null;
  }
  return children;
}
/* harmony default export */ const theme_support_check = ((0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getThemeSupports
  } = select(external_wp_coreData_namespaceObject.store);
  const {
    getEditedPostAttribute
  } = select(store_store);
  return {
    postType: getEditedPostAttribute('type'),
    themeSupports: getThemeSupports()
  };
})(ThemeSupportCheck));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-featured-image/check.js

/**
 * Internal dependencies
 */


function PostFeaturedImageCheck({
  children
}) {
  return (0,external_React_.createElement)(theme_support_check, {
    supportKeys: "post-thumbnails"
  }, (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: "thumbnail"
  }, children));
}
/* harmony default export */ const post_featured_image_check = (PostFeaturedImageCheck);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-featured-image/index.js

/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */


const ALLOWED_MEDIA_TYPES = ['image'];

// Used when labels from post type were not yet loaded or when they are not present.
const DEFAULT_FEATURE_IMAGE_LABEL = (0,external_wp_i18n_namespaceObject.__)('Featured image');
const DEFAULT_SET_FEATURE_IMAGE_LABEL = (0,external_wp_i18n_namespaceObject.__)('Set featured image');
const instructions = (0,external_React_.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('To edit the featured image, you need permission to upload media.'));
function getMediaDetails(media, postId) {
  var _media$media_details$, _media$media_details$2;
  if (!media) {
    return {};
  }
  const defaultSize = (0,external_wp_hooks_namespaceObject.applyFilters)('editor.PostFeaturedImage.imageSize', 'large', media.id, postId);
  if (defaultSize in ((_media$media_details$ = media?.media_details?.sizes) !== null && _media$media_details$ !== void 0 ? _media$media_details$ : {})) {
    return {
      mediaWidth: media.media_details.sizes[defaultSize].width,
      mediaHeight: media.media_details.sizes[defaultSize].height,
      mediaSourceUrl: media.media_details.sizes[defaultSize].source_url
    };
  }

  // Use fallbackSize when defaultSize is not available.
  const fallbackSize = (0,external_wp_hooks_namespaceObject.applyFilters)('editor.PostFeaturedImage.imageSize', 'thumbnail', media.id, postId);
  if (fallbackSize in ((_media$media_details$2 = media?.media_details?.sizes) !== null && _media$media_details$2 !== void 0 ? _media$media_details$2 : {})) {
    return {
      mediaWidth: media.media_details.sizes[fallbackSize].width,
      mediaHeight: media.media_details.sizes[fallbackSize].height,
      mediaSourceUrl: media.media_details.sizes[fallbackSize].source_url
    };
  }

  // Use full image size when fallbackSize and defaultSize are not available.
  return {
    mediaWidth: media.media_details.width,
    mediaHeight: media.media_details.height,
    mediaSourceUrl: media.source_url
  };
}
function PostFeaturedImage({
  currentPostId,
  featuredImageId,
  onUpdateImage,
  onRemoveImage,
  media,
  postType,
  noticeUI,
  noticeOperations
}) {
  const toggleRef = (0,external_wp_element_namespaceObject.useRef)();
  const [isLoading, setIsLoading] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    getSettings
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);
  const {
    mediaWidth,
    mediaHeight,
    mediaSourceUrl
  } = getMediaDetails(media, currentPostId);
  function onDropFiles(filesList) {
    getSettings().mediaUpload({
      allowedTypes: ALLOWED_MEDIA_TYPES,
      filesList,
      onFileChange([image]) {
        if ((0,external_wp_blob_namespaceObject.isBlobURL)(image?.url)) {
          setIsLoading(true);
          return;
        }
        if (image) {
          onUpdateImage(image);
        }
        setIsLoading(false);
      },
      onError(message) {
        noticeOperations.removeAllNotices();
        noticeOperations.createErrorNotice(message);
      }
    });
  }
  return (0,external_React_.createElement)(post_featured_image_check, null, noticeUI, (0,external_React_.createElement)("div", {
    className: "editor-post-featured-image"
  }, media && (0,external_React_.createElement)("div", {
    id: `editor-post-featured-image-${featuredImageId}-describedby`,
    className: "hidden"
  }, media.alt_text && (0,external_wp_i18n_namespaceObject.sprintf)(
  // Translators: %s: The selected image alt text.
  (0,external_wp_i18n_namespaceObject.__)('Current image: %s'), media.alt_text), !media.alt_text && (0,external_wp_i18n_namespaceObject.sprintf)(
  // Translators: %s: The selected image filename.
  (0,external_wp_i18n_namespaceObject.__)('The current image has no alternative text. The file name is: %s'), media.media_details.sizes?.full?.file || media.slug)), (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.MediaUploadCheck, {
    fallback: instructions
  }, (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.MediaUpload, {
    title: postType?.labels?.featured_image || DEFAULT_FEATURE_IMAGE_LABEL,
    onSelect: onUpdateImage,
    unstableFeaturedImageFlow: true,
    allowedTypes: ALLOWED_MEDIA_TYPES,
    modalClass: "editor-post-featured-image__media-modal",
    render: ({
      open
    }) => (0,external_React_.createElement)("div", {
      className: "editor-post-featured-image__container"
    }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      ref: toggleRef,
      className: !featuredImageId ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview',
      onClick: open,
      "aria-label": !featuredImageId ? null : (0,external_wp_i18n_namespaceObject.__)('Edit or replace the image'),
      "aria-describedby": !featuredImageId ? null : `editor-post-featured-image-${featuredImageId}-describedby`
    }, !!featuredImageId && media && (0,external_React_.createElement)(external_wp_components_namespaceObject.ResponsiveWrapper, {
      naturalWidth: mediaWidth,
      naturalHeight: mediaHeight,
      isInline: true
    }, (0,external_React_.createElement)("img", {
      src: mediaSourceUrl,
      alt: ""
    })), isLoading && (0,external_React_.createElement)(external_wp_components_namespaceObject.Spinner, null), !featuredImageId && !isLoading && (postType?.labels?.set_featured_image || DEFAULT_SET_FEATURE_IMAGE_LABEL)), !!featuredImageId && (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
      className: "editor-post-featured-image__actions"
    }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      className: "editor-post-featured-image__action",
      onClick: open
    }, (0,external_wp_i18n_namespaceObject.__)('Replace')), (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      className: "editor-post-featured-image__action",
      onClick: () => {
        onRemoveImage();
        toggleRef.current.focus();
      }
    }, (0,external_wp_i18n_namespaceObject.__)('Remove'))), (0,external_React_.createElement)(external_wp_components_namespaceObject.DropZone, {
      onFilesDrop: onDropFiles
    })),
    value: featuredImageId
  }))));
}
const applyWithSelect = (0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getMedia,
    getPostType
  } = select(external_wp_coreData_namespaceObject.store);
  const {
    getCurrentPostId,
    getEditedPostAttribute
  } = select(store_store);
  const featuredImageId = getEditedPostAttribute('featured_media');
  return {
    media: featuredImageId ? getMedia(featuredImageId, {
      context: 'view'
    }) : null,
    currentPostId: getCurrentPostId(),
    postType: getPostType(getEditedPostAttribute('type')),
    featuredImageId
  };
});
const applyWithDispatch = (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  noticeOperations
}, {
  select
}) => {
  const {
    editPost
  } = dispatch(store_store);
  return {
    onUpdateImage(image) {
      editPost({
        featured_media: image.id
      });
    },
    onDropImage(filesList) {
      select(external_wp_blockEditor_namespaceObject.store).getSettings().mediaUpload({
        allowedTypes: ['image'],
        filesList,
        onFileChange([image]) {
          editPost({
            featured_media: image.id
          });
        },
        onError(message) {
          noticeOperations.removeAllNotices();
          noticeOperations.createErrorNotice(message);
        }
      });
    },
    onRemoveImage() {
      editPost({
        featured_media: 0
      });
    }
  };
});
/* harmony default export */ const post_featured_image = ((0,external_wp_compose_namespaceObject.compose)(external_wp_components_namespaceObject.withNotices, applyWithSelect, applyWithDispatch, (0,external_wp_components_namespaceObject.withFilters)('editor.PostFeaturedImage'))(PostFeaturedImage));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-featured-image/panel.js

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



const post_featured_image_panel_PANEL_NAME = 'featured-image';
function FeaturedImage() {
  var _postType$labels$feat;
  const {
    postType,
    isEnabled,
    isOpened
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute,
      isEditorPanelEnabled,
      isEditorPanelOpened
    } = select(store_store);
    const {
      getPostType
    } = select(external_wp_coreData_namespaceObject.store);
    return {
      postType: getPostType(getEditedPostAttribute('type')),
      isEnabled: isEditorPanelEnabled(post_featured_image_panel_PANEL_NAME),
      isOpened: isEditorPanelOpened(post_featured_image_panel_PANEL_NAME)
    };
  }, []);
  const {
    toggleEditorPanelOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  if (!isEnabled) {
    return null;
  }
  return (0,external_React_.createElement)(post_featured_image_check, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (_postType$labels$feat = postType?.labels?.featured_image) !== null && _postType$labels$feat !== void 0 ? _postType$labels$feat : (0,external_wp_i18n_namespaceObject.__)('Featured image'),
    opened: isOpened,
    onToggle: () => toggleEditorPanelOpened(post_featured_image_panel_PANEL_NAME)
  }, (0,external_React_.createElement)(post_featured_image, null)));
}
/* harmony default export */ const post_featured_image_panel = (FeaturedImage);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-format/check.js

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function PostFormatCheck({
  children
}) {
  const disablePostFormats = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getEditorSettings().disablePostFormats, []);
  if (disablePostFormats) {
    return null;
  }
  return (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: "post-formats"
  }, children);
}
/* harmony default export */ const post_format_check = (PostFormatCheck);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-format/index.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



// All WP post formats, sorted alphabetically by translated name.
const POST_FORMATS = [{
  id: 'aside',
  caption: (0,external_wp_i18n_namespaceObject.__)('Aside')
}, {
  id: 'audio',
  caption: (0,external_wp_i18n_namespaceObject.__)('Audio')
}, {
  id: 'chat',
  caption: (0,external_wp_i18n_namespaceObject.__)('Chat')
}, {
  id: 'gallery',
  caption: (0,external_wp_i18n_namespaceObject.__)('Gallery')
}, {
  id: 'image',
  caption: (0,external_wp_i18n_namespaceObject.__)('Image')
}, {
  id: 'link',
  caption: (0,external_wp_i18n_namespaceObject.__)('Link')
}, {
  id: 'quote',
  caption: (0,external_wp_i18n_namespaceObject.__)('Quote')
}, {
  id: 'standard',
  caption: (0,external_wp_i18n_namespaceObject.__)('Standard')
}, {
  id: 'status',
  caption: (0,external_wp_i18n_namespaceObject.__)('Status')
}, {
  id: 'video',
  caption: (0,external_wp_i18n_namespaceObject.__)('Video')
}].sort((a, b) => {
  const normalizedA = a.caption.toUpperCase();
  const normalizedB = b.caption.toUpperCase();
  if (normalizedA < normalizedB) {
    return -1;
  }
  if (normalizedA > normalizedB) {
    return 1;
  }
  return 0;
});
function PostFormat() {
  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(PostFormat);
  const postFormatSelectorId = `post-format-selector-${instanceId}`;
  const {
    postFormat,
    suggestedFormat,
    supportedFormats
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute,
      getSuggestedPostFormat
    } = select(store_store);
    const _postFormat = getEditedPostAttribute('format');
    const themeSupports = select(external_wp_coreData_namespaceObject.store).getThemeSupports();
    return {
      postFormat: _postFormat !== null && _postFormat !== void 0 ? _postFormat : 'standard',
      suggestedFormat: getSuggestedPostFormat(),
      supportedFormats: themeSupports.formats
    };
  }, []);
  const formats = POST_FORMATS.filter(format => {
    // Ensure current format is always in the set.
    // The current format may not be a format supported by the theme.
    return supportedFormats?.includes(format.id) || postFormat === format.id;
  });
  const suggestion = formats.find(format => format.id === suggestedFormat);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const onUpdatePostFormat = format => editPost({
    format
  });
  return (0,external_React_.createElement)(post_format_check, null, (0,external_React_.createElement)("div", {
    className: "editor-post-format"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.SelectControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Post Format'),
    value: postFormat,
    onChange: format => onUpdatePostFormat(format),
    id: postFormatSelectorId,
    options: formats.map(format => ({
      label: format.caption,
      value: format.id
    }))
  }), suggestion && suggestion.id !== postFormat && (0,external_React_.createElement)("p", {
    className: "editor-post-format__suggestion"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "link",
    onClick: () => onUpdatePostFormat(suggestion.id)
  }, (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: post format */
  (0,external_wp_i18n_namespaceObject.__)('Apply suggested format: %s'), suggestion.caption)))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/backup.js

/**
 * WordPress dependencies
 */

const backup = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M5.5 12h1.75l-2.5 3-2.5-3H4a8 8 0 113.134 6.35l.907-1.194A6.5 6.5 0 105.5 12zm9.53 1.97l-2.28-2.28V8.5a.75.75 0 00-1.5 0V12a.747.747 0 00.218.529l1.282-.84-1.28.842 2.5 2.5a.75.75 0 101.06-1.061z"
}));
/* harmony default export */ const library_backup = (backup);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-last-revision/check.js

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function PostLastRevisionCheck({
  children
}) {
  const {
    lastRevisionId,
    revisionsCount
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getCurrentPostLastRevisionId,
      getCurrentPostRevisionsCount
    } = select(store_store);
    return {
      lastRevisionId: getCurrentPostLastRevisionId(),
      revisionsCount: getCurrentPostRevisionsCount()
    };
  }, []);
  if (!lastRevisionId || revisionsCount < 2) {
    return null;
  }
  return (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: "revisions"
  }, children);
}
/* harmony default export */ const post_last_revision_check = (PostLastRevisionCheck);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-last-revision/index.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function LastRevision() {
  const {
    lastRevisionId,
    revisionsCount
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getCurrentPostLastRevisionId,
      getCurrentPostRevisionsCount
    } = select(store_store);
    return {
      lastRevisionId: getCurrentPostLastRevisionId(),
      revisionsCount: getCurrentPostRevisionsCount()
    };
  }, []);
  return (0,external_React_.createElement)(post_last_revision_check, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    href: (0,external_wp_url_namespaceObject.addQueryArgs)('revision.php', {
      revision: lastRevisionId
    }),
    className: "editor-post-last-revision__title",
    icon: library_backup,
    iconPosition: "right",
    text: (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: number of revisions */
    (0,external_wp_i18n_namespaceObject.__)('Revisions (%s)'), revisionsCount)
  }));
}
/* harmony default export */ const post_last_revision = (LastRevision);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-last-revision/panel.js

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function PostLastRevisionPanel() {
  return (0,external_React_.createElement)(post_last_revision_check, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    className: "editor-post-last-revision__panel"
  }, (0,external_React_.createElement)(post_last_revision, null)));
}
/* harmony default export */ const post_last_revision_panel = (PostLastRevisionPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-locked-modal/index.js

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */

function PostLockedModal() {
  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(PostLockedModal);
  const hookName = 'core/editor/post-locked-modal-' + instanceId;
  const {
    autosave,
    updatePostLock
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    isLocked,
    isTakeover,
    user,
    postId,
    postLockUtils,
    activePostLock,
    postType,
    previewLink
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isPostLocked,
      isPostLockTakeover,
      getPostLockUser,
      getCurrentPostId,
      getActivePostLock,
      getEditedPostAttribute,
      getEditedPostPreviewLink,
      getEditorSettings
    } = select(store_store);
    const {
      getPostType
    } = select(external_wp_coreData_namespaceObject.store);
    return {
      isLocked: isPostLocked(),
      isTakeover: isPostLockTakeover(),
      user: getPostLockUser(),
      postId: getCurrentPostId(),
      postLockUtils: getEditorSettings().postLockUtils,
      activePostLock: getActivePostLock(),
      postType: getPostType(getEditedPostAttribute('type')),
      previewLink: getEditedPostPreviewLink()
    };
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    /**
     * Keep the lock refreshed.
     *
     * When the user does not send a heartbeat in a heartbeat-tick
     * the user is no longer editing and another user can start editing.
     *
     * @param {Object} data Data to send in the heartbeat request.
     */
    function sendPostLock(data) {
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
    function receivePostLock(data) {
      if (!data['wp-refresh-post-lock']) {
        return;
      }
      const received = data['wp-refresh-post-lock'];
      if (received.lock_error) {
        // Auto save and display the takeover modal.
        autosave();
        updatePostLock({
          isLocked: true,
          isTakeover: true,
          user: {
            name: received.lock_error.name,
            avatar: received.lock_error.avatar_src_2x
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
    function releasePostLock() {
      if (isLocked || !activePostLock) {
        return;
      }
      const data = new window.FormData();
      data.append('action', 'wp-remove-post-lock');
      data.append('_wpnonce', postLockUtils.unlockNonce);
      data.append('post_ID', postId);
      data.append('active_post_lock', activePostLock);
      if (window.navigator.sendBeacon) {
        window.navigator.sendBeacon(postLockUtils.ajaxUrl, data);
      } else {
        const xhr = new window.XMLHttpRequest();
        xhr.open('POST', postLockUtils.ajaxUrl, false);
        xhr.send(data);
      }
    }

    // Details on these events on the Heartbeat API docs
    // https://developer.wordpress.org/plugins/javascript/heartbeat-api/
    (0,external_wp_hooks_namespaceObject.addAction)('heartbeat.send', hookName, sendPostLock);
    (0,external_wp_hooks_namespaceObject.addAction)('heartbeat.tick', hookName, receivePostLock);
    window.addEventListener('beforeunload', releasePostLock);
    return () => {
      (0,external_wp_hooks_namespaceObject.removeAction)('heartbeat.send', hookName);
      (0,external_wp_hooks_namespaceObject.removeAction)('heartbeat.tick', hookName);
      window.removeEventListener('beforeunload', releasePostLock);
    };
  }, []);
  if (!isLocked) {
    return null;
  }
  const userDisplayName = user.name;
  const userAvatar = user.avatar;
  const unlockUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('post.php', {
    'get-post-lock': '1',
    lockKey: true,
    post: postId,
    action: 'edit',
    _wpnonce: postLockUtils.nonce
  });
  const allPostsUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('edit.php', {
    post_type: postType?.slug
  });
  const allPostsLabel = (0,external_wp_i18n_namespaceObject.__)('Exit editor');
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Modal, {
    title: isTakeover ? (0,external_wp_i18n_namespaceObject.__)('Someone else has taken over this post') : (0,external_wp_i18n_namespaceObject.__)('This post is already being edited'),
    focusOnMount: true,
    shouldCloseOnClickOutside: false,
    shouldCloseOnEsc: false,
    isDismissible: false,
    size: "medium"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    alignment: "top",
    spacing: 6
  }, !!userAvatar && (0,external_React_.createElement)("img", {
    src: userAvatar,
    alt: (0,external_wp_i18n_namespaceObject.__)('Avatar'),
    className: "editor-post-locked-modal__avatar",
    width: 64,
    height: 64
  }), (0,external_React_.createElement)("div", null, !!isTakeover && (0,external_React_.createElement)("p", null, (0,external_wp_element_namespaceObject.createInterpolateElement)(userDisplayName ? (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: user's display name */
  (0,external_wp_i18n_namespaceObject.__)('<strong>%s</strong> now has editing control of this post (<PreviewLink />). Don’t worry, your changes up to this moment have been saved.'), userDisplayName) : (0,external_wp_i18n_namespaceObject.__)('Another user now has editing control of this post (<PreviewLink />). Don’t worry, your changes up to this moment have been saved.'), {
    strong: (0,external_React_.createElement)("strong", null),
    PreviewLink: (0,external_React_.createElement)(external_wp_components_namespaceObject.ExternalLink, {
      href: previewLink
    }, (0,external_wp_i18n_namespaceObject.__)('preview'))
  })), !isTakeover && (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)("p", null, (0,external_wp_element_namespaceObject.createInterpolateElement)(userDisplayName ? (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: user's display name */
  (0,external_wp_i18n_namespaceObject.__)('<strong>%s</strong> is currently working on this post (<PreviewLink />), which means you cannot make changes, unless you take over.'), userDisplayName) : (0,external_wp_i18n_namespaceObject.__)('Another user is currently working on this post (<PreviewLink />), which means you cannot make changes, unless you take over.'), {
    strong: (0,external_React_.createElement)("strong", null),
    PreviewLink: (0,external_React_.createElement)(external_wp_components_namespaceObject.ExternalLink, {
      href: previewLink
    }, (0,external_wp_i18n_namespaceObject.__)('preview'))
  })), (0,external_React_.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('If you take over, the other user will lose editing control to the post, but their changes will be saved.'))), (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    className: "editor-post-locked-modal__buttons",
    justify: "flex-end"
  }, !isTakeover && (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "tertiary",
    href: unlockUrl
  }, (0,external_wp_i18n_namespaceObject.__)('Take over')), (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    href: allPostsUrl
  }, allPostsLabel)))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-pending-status/check.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

function PostPendingStatusCheck({
  children
}) {
  const {
    hasPublishAction,
    isPublished
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getCurrentPost$_link;
    const {
      isCurrentPostPublished,
      getCurrentPost
    } = select(store_store);
    return {
      hasPublishAction: (_getCurrentPost$_link = getCurrentPost()._links?.['wp:action-publish']) !== null && _getCurrentPost$_link !== void 0 ? _getCurrentPost$_link : false,
      isPublished: isCurrentPostPublished()
    };
  }, []);
  if (isPublished || !hasPublishAction) {
    return null;
  }
  return children;
}
/* harmony default export */ const post_pending_status_check = (PostPendingStatusCheck);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-pending-status/index.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function PostPendingStatus() {
  const status = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getEditedPostAttribute('status'), []);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const togglePendingStatus = () => {
    const updatedStatus = status === 'pending' ? 'draft' : 'pending';
    editPost({
      status: updatedStatus
    });
  };
  return (0,external_React_.createElement)(post_pending_status_check, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Pending review'),
    checked: status === 'pending',
    onChange: togglePendingStatus
  }));
}
/* harmony default export */ const post_pending_status = (PostPendingStatus);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-preview-button/index.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */

function writeInterstitialMessage(targetDocument) {
  let markup = (0,external_wp_element_namespaceObject.renderToString)((0,external_React_.createElement)("div", {
    className: "editor-post-preview-button__interstitial-message"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.SVG, {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 96 96"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Path, {
    className: "outer",
    d: "M48 12c19.9 0 36 16.1 36 36S67.9 84 48 84 12 67.9 12 48s16.1-36 36-36",
    fill: "none"
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.Path, {
    className: "inner",
    d: "M69.5 46.4c0-3.9-1.4-6.7-2.6-8.8-1.6-2.6-3.1-4.9-3.1-7.5 0-2.9 2.2-5.7 5.4-5.7h.4C63.9 19.2 56.4 16 48 16c-11.2 0-21 5.7-26.7 14.4h2.1c3.3 0 8.5-.4 8.5-.4 1.7-.1 1.9 2.4.2 2.6 0 0-1.7.2-3.7.3L40 67.5l7-20.9L42 33c-1.7-.1-3.3-.3-3.3-.3-1.7-.1-1.5-2.7.2-2.6 0 0 5.3.4 8.4.4 3.3 0 8.5-.4 8.5-.4 1.7-.1 1.9 2.4.2 2.6 0 0-1.7.2-3.7.3l11.5 34.3 3.3-10.4c1.6-4.5 2.4-7.8 2.4-10.5zM16.1 48c0 12.6 7.3 23.5 18 28.7L18.8 35c-1.7 4-2.7 8.4-2.7 13zm32.5 2.8L39 78.6c2.9.8 5.9 1.3 9 1.3 3.7 0 7.3-.6 10.6-1.8-.1-.1-.2-.3-.2-.4l-9.8-26.9zM76.2 36c0 3.2-.6 6.9-2.4 11.4L64 75.6c9.5-5.5 15.9-15.8 15.9-27.6 0-5.5-1.4-10.8-3.9-15.3.1 1 .2 2.1.2 3.3z",
    fill: "none"
  })), (0,external_React_.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('Generating preview…'))));
  markup += `
		<style>
			body {
				margin: 0;
			}
			.editor-post-preview-button__interstitial-message {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				height: 100vh;
				width: 100vw;
			}
			@-webkit-keyframes paint {
				0% {
					stroke-dashoffset: 0;
				}
			}
			@-moz-keyframes paint {
				0% {
					stroke-dashoffset: 0;
				}
			}
			@-o-keyframes paint {
				0% {
					stroke-dashoffset: 0;
				}
			}
			@keyframes paint {
				0% {
					stroke-dashoffset: 0;
				}
			}
			.editor-post-preview-button__interstitial-message svg {
				width: 192px;
				height: 192px;
				stroke: #555d66;
				stroke-width: 0.75;
			}
			.editor-post-preview-button__interstitial-message svg .outer,
			.editor-post-preview-button__interstitial-message svg .inner {
				stroke-dasharray: 280;
				stroke-dashoffset: 280;
				-webkit-animation: paint 1.5s ease infinite alternate;
				-moz-animation: paint 1.5s ease infinite alternate;
				-o-animation: paint 1.5s ease infinite alternate;
				animation: paint 1.5s ease infinite alternate;
			}
			p {
				text-align: center;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			}
		</style>
	`;

  /**
   * Filters the interstitial message shown when generating previews.
   *
   * @param {string} markup The preview interstitial markup.
   */
  markup = (0,external_wp_hooks_namespaceObject.applyFilters)('editor.PostPreview.interstitialMarkup', markup);
  targetDocument.write(markup);
  targetDocument.title = (0,external_wp_i18n_namespaceObject.__)('Generating preview…');
  targetDocument.close();
}
function PostPreviewButton({
  className,
  textContent,
  forceIsAutosaveable,
  role,
  onPreview
}) {
  const {
    postId,
    currentPostLink,
    previewLink,
    isSaveable,
    isViewable
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _postType$viewable;
    const editor = select(store_store);
    const core = select(external_wp_coreData_namespaceObject.store);
    const postType = core.getPostType(editor.getCurrentPostType('type'));
    return {
      postId: editor.getCurrentPostId(),
      currentPostLink: editor.getCurrentPostAttribute('link'),
      previewLink: editor.getEditedPostPreviewLink(),
      isSaveable: editor.isEditedPostSaveable(),
      isViewable: (_postType$viewable = postType?.viewable) !== null && _postType$viewable !== void 0 ? _postType$viewable : false
    };
  }, []);
  const {
    __unstableSaveForPreview
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  if (!isViewable) {
    return null;
  }
  const targetId = `wp-preview-${postId}`;
  const openPreviewWindow = async event => {
    // Our Preview button has its 'href' and 'target' set correctly for a11y
    // purposes. Unfortunately, though, we can't rely on the default 'click'
    // handler since sometimes it incorrectly opens a new tab instead of reusing
    // the existing one.
    // https://github.com/WordPress/gutenberg/pull/8330
    event.preventDefault();

    // Open up a Preview tab if needed. This is where we'll show the preview.
    const previewWindow = window.open('', targetId);

    // Focus the Preview tab. This might not do anything, depending on the browser's
    // and user's preferences.
    // https://html.spec.whatwg.org/multipage/interaction.html#dom-window-focus
    previewWindow.focus();
    writeInterstitialMessage(previewWindow.document);
    const link = await __unstableSaveForPreview({
      forceIsAutosaveable
    });
    previewWindow.location = link;
    onPreview?.();
  };

  // Link to the `?preview=true` URL if we have it, since this lets us see
  // changes that were autosaved since the post was last published. Otherwise,
  // just link to the post's URL.
  const href = previewLink || currentPostLink;
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: !className ? 'tertiary' : undefined,
    className: className || 'editor-post-preview',
    href: href,
    target: targetId,
    disabled: !isSaveable,
    onClick: openPreviewWindow,
    role: role
  }, textContent || (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_wp_i18n_namespaceObject._x)('Preview', 'imperative verb'), (0,external_React_.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "span"
  }, /* translators: accessibility text */
  (0,external_wp_i18n_namespaceObject.__)('(opens in a new tab)'))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-button/label.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function PublishButtonLabel({
  isPublished,
  isBeingScheduled,
  isSaving,
  isPublishing,
  hasPublishAction,
  isAutosaving,
  hasNonPostEntityChanges
}) {
  if (isPublishing) {
    /* translators: button label text should, if possible, be under 16 characters. */
    return (0,external_wp_i18n_namespaceObject.__)('Publishing…');
  } else if (isPublished && isSaving && !isAutosaving) {
    /* translators: button label text should, if possible, be under 16 characters. */
    return (0,external_wp_i18n_namespaceObject.__)('Updating…');
  } else if (isBeingScheduled && isSaving && !isAutosaving) {
    /* translators: button label text should, if possible, be under 16 characters. */
    return (0,external_wp_i18n_namespaceObject.__)('Scheduling…');
  }
  if (!hasPublishAction) {
    return hasNonPostEntityChanges ? (0,external_wp_i18n_namespaceObject.__)('Submit for Review…') : (0,external_wp_i18n_namespaceObject.__)('Submit for Review');
  } else if (isPublished) {
    return hasNonPostEntityChanges ? (0,external_wp_i18n_namespaceObject.__)('Update…') : (0,external_wp_i18n_namespaceObject.__)('Update');
  } else if (isBeingScheduled) {
    return hasNonPostEntityChanges ? (0,external_wp_i18n_namespaceObject.__)('Schedule…') : (0,external_wp_i18n_namespaceObject.__)('Schedule');
  }
  return (0,external_wp_i18n_namespaceObject.__)('Publish');
}
/* harmony default export */ const label = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
  var _getCurrentPost$_link;
  const {
    isCurrentPostPublished,
    isEditedPostBeingScheduled,
    isSavingPost,
    isPublishingPost,
    getCurrentPost,
    getCurrentPostType,
    isAutosavingPost
  } = select(store_store);
  return {
    isPublished: isCurrentPostPublished(),
    isBeingScheduled: isEditedPostBeingScheduled(),
    isSaving: isSavingPost(),
    isPublishing: isPublishingPost(),
    hasPublishAction: (_getCurrentPost$_link = getCurrentPost()._links?.['wp:action-publish']) !== null && _getCurrentPost$_link !== void 0 ? _getCurrentPost$_link : false,
    postType: getCurrentPostType(),
    isAutosaving: isAutosavingPost()
  };
})])(PublishButtonLabel));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-button/index.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


const noop = () => {};
class PostPublishButton extends external_wp_element_namespaceObject.Component {
  constructor(props) {
    super(props);
    this.buttonNode = (0,external_wp_element_namespaceObject.createRef)();
    this.createOnClick = this.createOnClick.bind(this);
    this.closeEntitiesSavedStates = this.closeEntitiesSavedStates.bind(this);
    this.state = {
      entitiesSavedStatesCallback: false
    };
  }
  componentDidMount() {
    if (this.props.focusOnMount) {
      // This timeout is necessary to make sure the `useEffect` hook of
      // `useFocusReturn` gets the correct element (the button that opens the
      // PostPublishPanel) otherwise it will get this button.
      this.timeoutID = setTimeout(() => {
        this.buttonNode.current.focus();
      }, 0);
    }
  }
  componentWillUnmount() {
    clearTimeout(this.timeoutID);
  }
  createOnClick(callback) {
    return (...args) => {
      const {
        hasNonPostEntityChanges,
        setEntitiesSavedStatesCallback
      } = this.props;
      // If a post with non-post entities is published, but the user
      // elects to not save changes to the non-post entities, those
      // entities will still be dirty when the Publish button is clicked.
      // We also need to check that the `setEntitiesSavedStatesCallback`
      // prop was passed. See https://github.com/WordPress/gutenberg/pull/37383
      if (hasNonPostEntityChanges && setEntitiesSavedStatesCallback) {
        // The modal for multiple entity saving will open,
        // hold the callback for saving/publishing the post
        // so that we can call it if the post entity is checked.
        this.setState({
          entitiesSavedStatesCallback: () => callback(...args)
        });

        // Open the save panel by setting its callback.
        // To set a function on the useState hook, we must set it
        // with another function (() => myFunction). Passing the
        // function on its own will cause an error when called.
        setEntitiesSavedStatesCallback(() => this.closeEntitiesSavedStates);
        return noop;
      }
      return callback(...args);
    };
  }
  closeEntitiesSavedStates(savedEntities) {
    const {
      postType,
      postId
    } = this.props;
    const {
      entitiesSavedStatesCallback
    } = this.state;
    this.setState({
      entitiesSavedStatesCallback: false
    }, () => {
      if (savedEntities && savedEntities.some(elt => elt.kind === 'postType' && elt.name === postType && elt.key === postId)) {
        // The post entity was checked, call the held callback from `createOnClick`.
        entitiesSavedStatesCallback();
      }
    });
  }
  render() {
    const {
      forceIsDirty,
      hasPublishAction,
      isBeingScheduled,
      isOpen,
      isPostSavingLocked,
      isPublishable,
      isPublished,
      isSaveable,
      isSaving,
      isAutoSaving,
      isToggle,
      onSave,
      onStatusChange,
      onSubmit = noop,
      onToggle,
      visibility,
      hasNonPostEntityChanges,
      isSavingNonPostEntityChanges
    } = this.props;
    const isButtonDisabled = (isSaving || !isSaveable || isPostSavingLocked || !isPublishable && !forceIsDirty) && (!hasNonPostEntityChanges || isSavingNonPostEntityChanges);
    const isToggleDisabled = (isPublished || isSaving || !isSaveable || !isPublishable && !forceIsDirty) && (!hasNonPostEntityChanges || isSavingNonPostEntityChanges);
    let publishStatus;
    if (!hasPublishAction) {
      publishStatus = 'pending';
    } else if (visibility === 'private') {
      publishStatus = 'private';
    } else if (isBeingScheduled) {
      publishStatus = 'future';
    } else {
      publishStatus = 'publish';
    }
    const onClickButton = () => {
      if (isButtonDisabled) {
        return;
      }
      onSubmit();
      onStatusChange(publishStatus);
      onSave();
    };
    const onClickToggle = () => {
      if (isToggleDisabled) {
        return;
      }
      onToggle();
    };
    const buttonProps = {
      'aria-disabled': isButtonDisabled,
      className: 'editor-post-publish-button',
      isBusy: !isAutoSaving && isSaving,
      variant: 'primary',
      onClick: this.createOnClick(onClickButton)
    };
    const toggleProps = {
      'aria-disabled': isToggleDisabled,
      'aria-expanded': isOpen,
      className: 'editor-post-publish-panel__toggle',
      isBusy: isSaving && isPublished,
      variant: 'primary',
      size: 'compact',
      onClick: this.createOnClick(onClickToggle)
    };
    const toggleChildren = isBeingScheduled ? (0,external_wp_i18n_namespaceObject.__)('Schedule…') : (0,external_wp_i18n_namespaceObject.__)('Publish');
    const buttonChildren = (0,external_React_.createElement)(label, {
      hasNonPostEntityChanges: hasNonPostEntityChanges
    });
    const componentProps = isToggle ? toggleProps : buttonProps;
    const componentChildren = isToggle ? toggleChildren : buttonChildren;
    return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      ref: this.buttonNode,
      ...componentProps,
      className: classnames_default()(componentProps.className, 'editor-post-publish-button__button', {
        'has-changes-dot': hasNonPostEntityChanges
      })
    }, componentChildren));
  }
}
/* harmony default export */ const post_publish_button = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
  var _getCurrentPost$_link;
  const {
    isSavingPost,
    isAutosavingPost,
    isEditedPostBeingScheduled,
    getEditedPostVisibility,
    isCurrentPostPublished,
    isEditedPostSaveable,
    isEditedPostPublishable,
    isPostSavingLocked,
    getCurrentPost,
    getCurrentPostType,
    getCurrentPostId,
    hasNonPostEntityChanges,
    isSavingNonPostEntityChanges
  } = select(store_store);
  return {
    isSaving: isSavingPost(),
    isAutoSaving: isAutosavingPost(),
    isBeingScheduled: isEditedPostBeingScheduled(),
    visibility: getEditedPostVisibility(),
    isSaveable: isEditedPostSaveable(),
    isPostSavingLocked: isPostSavingLocked(),
    isPublishable: isEditedPostPublishable(),
    isPublished: isCurrentPostPublished(),
    hasPublishAction: (_getCurrentPost$_link = getCurrentPost()._links?.['wp:action-publish']) !== null && _getCurrentPost$_link !== void 0 ? _getCurrentPost$_link : false,
    postType: getCurrentPostType(),
    postId: getCurrentPostId(),
    hasNonPostEntityChanges: hasNonPostEntityChanges(),
    isSavingNonPostEntityChanges: isSavingNonPostEntityChanges()
  };
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    editPost,
    savePost
  } = dispatch(store_store);
  return {
    onStatusChange: status => editPost({
      status
    }, {
      undoIgnore: true
    }),
    onSave: savePost
  };
})])(PostPublishButton));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close-small.js

/**
 * WordPress dependencies
 */

const closeSmall = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
}));
/* harmony default export */ const close_small = (closeSmall);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/wordpress.js

/**
 * WordPress dependencies
 */

const wordpress = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z"
}));
/* harmony default export */ const library_wordpress = (wordpress);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/utils.js
/**
 * WordPress dependencies
 */

const visibilityOptions = {
  public: {
    label: (0,external_wp_i18n_namespaceObject.__)('Public'),
    info: (0,external_wp_i18n_namespaceObject.__)('Visible to everyone.')
  },
  private: {
    label: (0,external_wp_i18n_namespaceObject.__)('Private'),
    info: (0,external_wp_i18n_namespaceObject.__)('Only visible to site admins and editors.')
  },
  password: {
    label: (0,external_wp_i18n_namespaceObject.__)('Password protected'),
    info: (0,external_wp_i18n_namespaceObject.__)('Only those with the password can view this post.')
  }
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/index.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function PostVisibility({
  onClose
}) {
  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(PostVisibility);
  const {
    status,
    visibility,
    password
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    status: select(store_store).getEditedPostAttribute('status'),
    visibility: select(store_store).getEditedPostVisibility(),
    password: select(store_store).getEditedPostAttribute('password')
  }));
  const {
    editPost,
    savePost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const [hasPassword, setHasPassword] = (0,external_wp_element_namespaceObject.useState)(!!password);
  const [showPrivateConfirmDialog, setShowPrivateConfirmDialog] = (0,external_wp_element_namespaceObject.useState)(false);
  const setPublic = () => {
    editPost({
      status: visibility === 'private' ? 'draft' : status,
      password: ''
    });
    setHasPassword(false);
  };
  const setPrivate = () => {
    setShowPrivateConfirmDialog(true);
  };
  const confirmPrivate = () => {
    editPost({
      status: 'private',
      password: ''
    });
    setHasPassword(false);
    setShowPrivateConfirmDialog(false);
    savePost();
  };
  const handleDialogCancel = () => {
    setShowPrivateConfirmDialog(false);
  };
  const setPasswordProtected = () => {
    editPost({
      status: visibility === 'private' ? 'draft' : status,
      password: password || ''
    });
    setHasPassword(true);
  };
  const updatePassword = event => {
    editPost({
      password: event.target.value
    });
  };
  return (0,external_React_.createElement)("div", {
    className: "editor-post-visibility"
  }, (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.__experimentalInspectorPopoverHeader, {
    title: (0,external_wp_i18n_namespaceObject.__)('Visibility'),
    help: (0,external_wp_i18n_namespaceObject.__)('Control how this post is viewed.'),
    onClose: onClose
  }), (0,external_React_.createElement)("fieldset", {
    className: "editor-post-visibility__fieldset"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "legend"
  }, (0,external_wp_i18n_namespaceObject.__)('Visibility')), (0,external_React_.createElement)(PostVisibilityChoice, {
    instanceId: instanceId,
    value: "public",
    label: visibilityOptions.public.label,
    info: visibilityOptions.public.info,
    checked: visibility === 'public' && !hasPassword,
    onChange: setPublic
  }), (0,external_React_.createElement)(PostVisibilityChoice, {
    instanceId: instanceId,
    value: "private",
    label: visibilityOptions.private.label,
    info: visibilityOptions.private.info,
    checked: visibility === 'private',
    onChange: setPrivate
  }), (0,external_React_.createElement)(PostVisibilityChoice, {
    instanceId: instanceId,
    value: "password",
    label: visibilityOptions.password.label,
    info: visibilityOptions.password.info,
    checked: hasPassword,
    onChange: setPasswordProtected
  }), hasPassword && (0,external_React_.createElement)("div", {
    className: "editor-post-visibility__password"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "label",
    htmlFor: `editor-post-visibility__password-input-${instanceId}`
  }, (0,external_wp_i18n_namespaceObject.__)('Create password')), (0,external_React_.createElement)("input", {
    className: "editor-post-visibility__password-input",
    id: `editor-post-visibility__password-input-${instanceId}`,
    type: "text",
    onChange: updatePassword,
    value: password,
    placeholder: (0,external_wp_i18n_namespaceObject.__)('Use a secure password')
  }))), (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalConfirmDialog, {
    isOpen: showPrivateConfirmDialog,
    onConfirm: confirmPrivate,
    onCancel: handleDialogCancel
  }, (0,external_wp_i18n_namespaceObject.__)('Would you like to privately publish this post now?')));
}
function PostVisibilityChoice({
  instanceId,
  value,
  label,
  info,
  ...props
}) {
  return (0,external_React_.createElement)("div", {
    className: "editor-post-visibility__choice"
  }, (0,external_React_.createElement)("input", {
    type: "radio",
    name: `editor-post-visibility__setting-${instanceId}`,
    value: value,
    id: `editor-post-${value}-${instanceId}`,
    "aria-describedby": `editor-post-${value}-${instanceId}-description`,
    className: "editor-post-visibility__radio",
    ...props
  }), (0,external_React_.createElement)("label", {
    htmlFor: `editor-post-${value}-${instanceId}`,
    className: "editor-post-visibility__label"
  }, label), (0,external_React_.createElement)("p", {
    id: `editor-post-${value}-${instanceId}-description`,
    className: "editor-post-visibility__info"
  }, info));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/label.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function PostVisibilityLabel() {
  return usePostVisibilityLabel();
}
function usePostVisibilityLabel() {
  const visibility = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getEditedPostVisibility());
  return visibilityOptions[visibility]?.label;
}

;// CONCATENATED MODULE: ./node_modules/date-fns/esm/_lib/requiredArgs/index.js
function requiredArgs(required, args) {
  if (args.length < required) {
    throw new TypeError(required + ' argument' + (required > 1 ? 's' : '') + ' required, but only ' + args.length + ' present');
  }
}
;// CONCATENATED MODULE: ./node_modules/date-fns/esm/toDate/index.js
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }


/**
 * @name toDate
 * @category Common Helpers
 * @summary Convert the given argument to an instance of Date.
 *
 * @description
 * Convert the given argument to an instance of Date.
 *
 * If the argument is an instance of Date, the function returns its clone.
 *
 * If the argument is a number, it is treated as a timestamp.
 *
 * If the argument is none of the above, the function returns Invalid Date.
 *
 * **Note**: *all* Date arguments passed to any *date-fns* function is processed by `toDate`.
 *
 * @param {Date|Number} argument - the value to convert
 * @returns {Date} the parsed date in the local time zone
 * @throws {TypeError} 1 argument required
 *
 * @example
 * // Clone the date:
 * const result = toDate(new Date(2014, 1, 11, 11, 30, 30))
 * //=> Tue Feb 11 2014 11:30:30
 *
 * @example
 * // Convert the timestamp to date:
 * const result = toDate(1392098430000)
 * //=> Tue Feb 11 2014 11:30:30
 */

function toDate(argument) {
  requiredArgs(1, arguments);
  var argStr = Object.prototype.toString.call(argument); // Clone the date

  if (argument instanceof Date || _typeof(argument) === 'object' && argStr === '[object Date]') {
    // Prevent the date to lose the milliseconds when passed to new Date() in IE10
    return new Date(argument.getTime());
  } else if (typeof argument === 'number' || argStr === '[object Number]') {
    return new Date(argument);
  } else {
    if ((typeof argument === 'string' || argStr === '[object String]') && typeof console !== 'undefined') {
      // eslint-disable-next-line no-console
      console.warn("Starting with v2.0.0-beta.1 date-fns doesn't accept strings as date arguments. Please use `parseISO` to parse strings. See: https://github.com/date-fns/date-fns/blob/master/docs/upgradeGuide.md#string-arguments"); // eslint-disable-next-line no-console

      console.warn(new Error().stack);
    }

    return new Date(NaN);
  }
}
;// CONCATENATED MODULE: ./node_modules/date-fns/esm/startOfMonth/index.js


/**
 * @name startOfMonth
 * @category Month Helpers
 * @summary Return the start of a month for the given date.
 *
 * @description
 * Return the start of a month for the given date.
 * The result will be in the local timezone.
 *
 * @param {Date|Number} date - the original date
 * @returns {Date} the start of a month
 * @throws {TypeError} 1 argument required
 *
 * @example
 * // The start of a month for 2 September 2014 11:55:00:
 * const result = startOfMonth(new Date(2014, 8, 2, 11, 55, 0))
 * //=> Mon Sep 01 2014 00:00:00
 */

function startOfMonth(dirtyDate) {
  requiredArgs(1, arguments);
  var date = toDate(dirtyDate);
  date.setDate(1);
  date.setHours(0, 0, 0, 0);
  return date;
}
;// CONCATENATED MODULE: ./node_modules/date-fns/esm/endOfMonth/index.js


/**
 * @name endOfMonth
 * @category Month Helpers
 * @summary Return the end of a month for the given date.
 *
 * @description
 * Return the end of a month for the given date.
 * The result will be in the local timezone.
 *
 * @param {Date|Number} date - the original date
 * @returns {Date} the end of a month
 * @throws {TypeError} 1 argument required
 *
 * @example
 * // The end of a month for 2 September 2014 11:55:00:
 * const result = endOfMonth(new Date(2014, 8, 2, 11, 55, 0))
 * //=> Tue Sep 30 2014 23:59:59.999
 */

function endOfMonth(dirtyDate) {
  requiredArgs(1, arguments);
  var date = toDate(dirtyDate);
  var month = date.getMonth();
  date.setFullYear(date.getFullYear(), month + 1, 0);
  date.setHours(23, 59, 59, 999);
  return date;
}
;// CONCATENATED MODULE: ./node_modules/date-fns/esm/constants/index.js
/**
 * Days in 1 week.
 *
 * @name daysInWeek
 * @constant
 * @type {number}
 * @default
 */
var daysInWeek = 7;
/**
 * Days in 1 year
 * One years equals 365.2425 days according to the formula:
 *
 * > Leap year occures every 4 years, except for years that are divisable by 100 and not divisable by 400.
 * > 1 mean year = (365+1/4-1/100+1/400) days = 365.2425 days
 *
 * @name daysInYear
 * @constant
 * @type {number}
 * @default
 */

var daysInYear = 365.2425;
/**
 * Maximum allowed time.
 *
 * @name maxTime
 * @constant
 * @type {number}
 * @default
 */

var maxTime = Math.pow(10, 8) * 24 * 60 * 60 * 1000;
/**
 * Milliseconds in 1 minute
 *
 * @name millisecondsInMinute
 * @constant
 * @type {number}
 * @default
 */

var millisecondsInMinute = 60000;
/**
 * Milliseconds in 1 hour
 *
 * @name millisecondsInHour
 * @constant
 * @type {number}
 * @default
 */

var millisecondsInHour = 3600000;
/**
 * Milliseconds in 1 second
 *
 * @name millisecondsInSecond
 * @constant
 * @type {number}
 * @default
 */

var millisecondsInSecond = 1000;
/**
 * Minimum allowed time.
 *
 * @name minTime
 * @constant
 * @type {number}
 * @default
 */

var minTime = -maxTime;
/**
 * Minutes in 1 hour
 *
 * @name minutesInHour
 * @constant
 * @type {number}
 * @default
 */

var minutesInHour = 60;
/**
 * Months in 1 quarter
 *
 * @name monthsInQuarter
 * @constant
 * @type {number}
 * @default
 */

var monthsInQuarter = 3;
/**
 * Months in 1 year
 *
 * @name monthsInYear
 * @constant
 * @type {number}
 * @default
 */

var monthsInYear = 12;
/**
 * Quarters in 1 year
 *
 * @name quartersInYear
 * @constant
 * @type {number}
 * @default
 */

var quartersInYear = 4;
/**
 * Seconds in 1 hour
 *
 * @name secondsInHour
 * @constant
 * @type {number}
 * @default
 */

var secondsInHour = 3600;
/**
 * Seconds in 1 minute
 *
 * @name secondsInMinute
 * @constant
 * @type {number}
 * @default
 */

var secondsInMinute = 60;
/**
 * Seconds in 1 day
 *
 * @name secondsInDay
 * @constant
 * @type {number}
 * @default
 */

var secondsInDay = secondsInHour * 24;
/**
 * Seconds in 1 week
 *
 * @name secondsInWeek
 * @constant
 * @type {number}
 * @default
 */

var secondsInWeek = secondsInDay * 7;
/**
 * Seconds in 1 year
 *
 * @name secondsInYear
 * @constant
 * @type {number}
 * @default
 */

var secondsInYear = secondsInDay * daysInYear;
/**
 * Seconds in 1 month
 *
 * @name secondsInMonth
 * @constant
 * @type {number}
 * @default
 */

var secondsInMonth = secondsInYear / 12;
/**
 * Seconds in 1 quarter
 *
 * @name secondsInQuarter
 * @constant
 * @type {number}
 * @default
 */

var secondsInQuarter = secondsInMonth * 3;
;// CONCATENATED MODULE: ./node_modules/date-fns/esm/_lib/toInteger/index.js
function toInteger(dirtyNumber) {
  if (dirtyNumber === null || dirtyNumber === true || dirtyNumber === false) {
    return NaN;
  }

  var number = Number(dirtyNumber);

  if (isNaN(number)) {
    return number;
  }

  return number < 0 ? Math.ceil(number) : Math.floor(number);
}
;// CONCATENATED MODULE: ./node_modules/date-fns/esm/parseISO/index.js



/**
 * @name parseISO
 * @category Common Helpers
 * @summary Parse ISO string
 *
 * @description
 * Parse the given string in ISO 8601 format and return an instance of Date.
 *
 * Function accepts complete ISO 8601 formats as well as partial implementations.
 * ISO 8601: http://en.wikipedia.org/wiki/ISO_8601
 *
 * If the argument isn't a string, the function cannot parse the string or
 * the values are invalid, it returns Invalid Date.
 *
 * @param {String} argument - the value to convert
 * @param {Object} [options] - an object with options.
 * @param {0|1|2} [options.additionalDigits=2] - the additional number of digits in the extended year format
 * @returns {Date} the parsed date in the local time zone
 * @throws {TypeError} 1 argument required
 * @throws {RangeError} `options.additionalDigits` must be 0, 1 or 2
 *
 * @example
 * // Convert string '2014-02-11T11:30:30' to date:
 * const result = parseISO('2014-02-11T11:30:30')
 * //=> Tue Feb 11 2014 11:30:30
 *
 * @example
 * // Convert string '+02014101' to date,
 * // if the additional number of digits in the extended year format is 1:
 * const result = parseISO('+02014101', { additionalDigits: 1 })
 * //=> Fri Apr 11 2014 00:00:00
 */

function parseISO(argument, options) {
  var _options$additionalDi;

  requiredArgs(1, arguments);
  var additionalDigits = toInteger((_options$additionalDi = options === null || options === void 0 ? void 0 : options.additionalDigits) !== null && _options$additionalDi !== void 0 ? _options$additionalDi : 2);

  if (additionalDigits !== 2 && additionalDigits !== 1 && additionalDigits !== 0) {
    throw new RangeError('additionalDigits must be 0, 1 or 2');
  }

  if (!(typeof argument === 'string' || Object.prototype.toString.call(argument) === '[object String]')) {
    return new Date(NaN);
  }

  var dateStrings = splitDateString(argument);
  var date;

  if (dateStrings.date) {
    var parseYearResult = parseYear(dateStrings.date, additionalDigits);
    date = parseDate(parseYearResult.restDateString, parseYearResult.year);
  }

  if (!date || isNaN(date.getTime())) {
    return new Date(NaN);
  }

  var timestamp = date.getTime();
  var time = 0;
  var offset;

  if (dateStrings.time) {
    time = parseTime(dateStrings.time);

    if (isNaN(time)) {
      return new Date(NaN);
    }
  }

  if (dateStrings.timezone) {
    offset = parseTimezone(dateStrings.timezone);

    if (isNaN(offset)) {
      return new Date(NaN);
    }
  } else {
    var dirtyDate = new Date(timestamp + time); // js parsed string assuming it's in UTC timezone
    // but we need it to be parsed in our timezone
    // so we use utc values to build date in our timezone.
    // Year values from 0 to 99 map to the years 1900 to 1999
    // so set year explicitly with setFullYear.

    var result = new Date(0);
    result.setFullYear(dirtyDate.getUTCFullYear(), dirtyDate.getUTCMonth(), dirtyDate.getUTCDate());
    result.setHours(dirtyDate.getUTCHours(), dirtyDate.getUTCMinutes(), dirtyDate.getUTCSeconds(), dirtyDate.getUTCMilliseconds());
    return result;
  }

  return new Date(timestamp + time + offset);
}
var patterns = {
  dateTimeDelimiter: /[T ]/,
  timeZoneDelimiter: /[Z ]/i,
  timezone: /([Z+-].*)$/
};
var dateRegex = /^-?(?:(\d{3})|(\d{2})(?:-?(\d{2}))?|W(\d{2})(?:-?(\d{1}))?|)$/;
var timeRegex = /^(\d{2}(?:[.,]\d*)?)(?::?(\d{2}(?:[.,]\d*)?))?(?::?(\d{2}(?:[.,]\d*)?))?$/;
var timezoneRegex = /^([+-])(\d{2})(?::?(\d{2}))?$/;

function splitDateString(dateString) {
  var dateStrings = {};
  var array = dateString.split(patterns.dateTimeDelimiter);
  var timeString; // The regex match should only return at maximum two array elements.
  // [date], [time], or [date, time].

  if (array.length > 2) {
    return dateStrings;
  }

  if (/:/.test(array[0])) {
    timeString = array[0];
  } else {
    dateStrings.date = array[0];
    timeString = array[1];

    if (patterns.timeZoneDelimiter.test(dateStrings.date)) {
      dateStrings.date = dateString.split(patterns.timeZoneDelimiter)[0];
      timeString = dateString.substr(dateStrings.date.length, dateString.length);
    }
  }

  if (timeString) {
    var token = patterns.timezone.exec(timeString);

    if (token) {
      dateStrings.time = timeString.replace(token[1], '');
      dateStrings.timezone = token[1];
    } else {
      dateStrings.time = timeString;
    }
  }

  return dateStrings;
}

function parseYear(dateString, additionalDigits) {
  var regex = new RegExp('^(?:(\\d{4}|[+-]\\d{' + (4 + additionalDigits) + '})|(\\d{2}|[+-]\\d{' + (2 + additionalDigits) + '})$)');
  var captures = dateString.match(regex); // Invalid ISO-formatted year

  if (!captures) return {
    year: NaN,
    restDateString: ''
  };
  var year = captures[1] ? parseInt(captures[1]) : null;
  var century = captures[2] ? parseInt(captures[2]) : null; // either year or century is null, not both

  return {
    year: century === null ? year : century * 100,
    restDateString: dateString.slice((captures[1] || captures[2]).length)
  };
}

function parseDate(dateString, year) {
  // Invalid ISO-formatted year
  if (year === null) return new Date(NaN);
  var captures = dateString.match(dateRegex); // Invalid ISO-formatted string

  if (!captures) return new Date(NaN);
  var isWeekDate = !!captures[4];
  var dayOfYear = parseDateUnit(captures[1]);
  var month = parseDateUnit(captures[2]) - 1;
  var day = parseDateUnit(captures[3]);
  var week = parseDateUnit(captures[4]);
  var dayOfWeek = parseDateUnit(captures[5]) - 1;

  if (isWeekDate) {
    if (!validateWeekDate(year, week, dayOfWeek)) {
      return new Date(NaN);
    }

    return dayOfISOWeekYear(year, week, dayOfWeek);
  } else {
    var date = new Date(0);

    if (!validateDate(year, month, day) || !validateDayOfYearDate(year, dayOfYear)) {
      return new Date(NaN);
    }

    date.setUTCFullYear(year, month, Math.max(dayOfYear, day));
    return date;
  }
}

function parseDateUnit(value) {
  return value ? parseInt(value) : 1;
}

function parseTime(timeString) {
  var captures = timeString.match(timeRegex);
  if (!captures) return NaN; // Invalid ISO-formatted time

  var hours = parseTimeUnit(captures[1]);
  var minutes = parseTimeUnit(captures[2]);
  var seconds = parseTimeUnit(captures[3]);

  if (!validateTime(hours, minutes, seconds)) {
    return NaN;
  }

  return hours * millisecondsInHour + minutes * millisecondsInMinute + seconds * 1000;
}

function parseTimeUnit(value) {
  return value && parseFloat(value.replace(',', '.')) || 0;
}

function parseTimezone(timezoneString) {
  if (timezoneString === 'Z') return 0;
  var captures = timezoneString.match(timezoneRegex);
  if (!captures) return 0;
  var sign = captures[1] === '+' ? -1 : 1;
  var hours = parseInt(captures[2]);
  var minutes = captures[3] && parseInt(captures[3]) || 0;

  if (!validateTimezone(hours, minutes)) {
    return NaN;
  }

  return sign * (hours * millisecondsInHour + minutes * millisecondsInMinute);
}

function dayOfISOWeekYear(isoWeekYear, week, day) {
  var date = new Date(0);
  date.setUTCFullYear(isoWeekYear, 0, 4);
  var fourthOfJanuaryDay = date.getUTCDay() || 7;
  var diff = (week - 1) * 7 + day + 1 - fourthOfJanuaryDay;
  date.setUTCDate(date.getUTCDate() + diff);
  return date;
} // Validation functions
// February is null to handle the leap year (using ||)


var daysInMonths = [31, null, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

function isLeapYearIndex(year) {
  return year % 400 === 0 || year % 4 === 0 && year % 100 !== 0;
}

function validateDate(year, month, date) {
  return month >= 0 && month <= 11 && date >= 1 && date <= (daysInMonths[month] || (isLeapYearIndex(year) ? 29 : 28));
}

function validateDayOfYearDate(year, dayOfYear) {
  return dayOfYear >= 1 && dayOfYear <= (isLeapYearIndex(year) ? 366 : 365);
}

function validateWeekDate(_year, week, day) {
  return week >= 1 && week <= 53 && day >= 0 && day <= 6;
}

function validateTime(hours, minutes, seconds) {
  if (hours === 24) {
    return minutes === 0 && seconds === 0;
  }

  return seconds >= 0 && seconds < 60 && minutes >= 0 && minutes < 60 && hours >= 0 && hours < 25;
}

function validateTimezone(_hours, minutes) {
  return minutes >= 0 && minutes <= 59;
}
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/index.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */

function PostSchedule({
  onClose
}) {
  const {
    postDate,
    postType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    postDate: select(store_store).getEditedPostAttribute('date'),
    postType: select(store_store).getCurrentPostType()
  }), []);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const onUpdateDate = date => editPost({
    date
  });
  const [previewedMonth, setPreviewedMonth] = (0,external_wp_element_namespaceObject.useState)(startOfMonth(new Date(postDate)));

  // Pick up published and schduled site posts.
  const eventsByPostType = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getEntityRecords('postType', postType, {
    status: 'publish,future',
    after: startOfMonth(previewedMonth).toISOString(),
    before: endOfMonth(previewedMonth).toISOString(),
    exclude: [select(store_store).getCurrentPostId()],
    per_page: 100,
    _fields: 'id,date'
  }), [previewedMonth, postType]);
  const events = (0,external_wp_element_namespaceObject.useMemo)(() => (eventsByPostType || []).map(({
    date: eventDate
  }) => ({
    date: new Date(eventDate)
  })), [eventsByPostType]);
  const settings = (0,external_wp_date_namespaceObject.getSettings)();

  // To know if the current timezone is a 12 hour time with look for "a" in the time format
  // We also make sure this a is not escaped by a "/"
  const is12HourTime = /a(?!\\)/i.test(settings.formats.time.toLowerCase() // Test only the lower case a.
  .replace(/\\\\/g, '') // Replace "//" with empty strings.
  .split('').reverse().join('') // Reverse the string and test for "a" not followed by a slash.
  );
  return (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.__experimentalPublishDateTimePicker, {
    currentDate: postDate,
    onChange: onUpdateDate,
    is12Hour: is12HourTime,
    events: events,
    onMonthPreviewed: date => setPreviewedMonth(parseISO(date)),
    onClose: onClose
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/label.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function PostScheduleLabel(props) {
  return usePostScheduleLabel(props);
}
function usePostScheduleLabel({
  full = false
} = {}) {
  const {
    date,
    isFloating
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    date: select(store_store).getEditedPostAttribute('date'),
    isFloating: select(store_store).isEditedPostDateFloating()
  }), []);
  return full ? getFullPostScheduleLabel(date) : getPostScheduleLabel(date, {
    isFloating
  });
}
function getFullPostScheduleLabel(dateAttribute) {
  const date = (0,external_wp_date_namespaceObject.getDate)(dateAttribute);
  const timezoneAbbreviation = getTimezoneAbbreviation();
  const formattedDate = (0,external_wp_date_namespaceObject.dateI18n)(
  // translators: If using a space between 'g:i' and 'a', use a non-breaking space.
  (0,external_wp_i18n_namespaceObject._x)('F j, Y g:i\xa0a', 'post schedule full date format'), date);
  return (0,external_wp_i18n_namespaceObject.isRTL)() ? `${timezoneAbbreviation} ${formattedDate}` : `${formattedDate} ${timezoneAbbreviation}`;
}
function getPostScheduleLabel(dateAttribute, {
  isFloating = false,
  now = new Date()
} = {}) {
  if (!dateAttribute || isFloating) {
    return (0,external_wp_i18n_namespaceObject.__)('Immediately');
  }

  // If the user timezone does not equal the site timezone then using words
  // like 'tomorrow' is confusing, so show the full date.
  if (!isTimezoneSameAsSiteTimezone(now)) {
    return getFullPostScheduleLabel(dateAttribute);
  }
  const date = (0,external_wp_date_namespaceObject.getDate)(dateAttribute);
  if (isSameDay(date, now)) {
    return (0,external_wp_i18n_namespaceObject.sprintf)(
    // translators: %s: Time of day the post is scheduled for.
    (0,external_wp_i18n_namespaceObject.__)('Today at %s'),
    // translators: If using a space between 'g:i' and 'a', use a non-breaking space.
    (0,external_wp_date_namespaceObject.dateI18n)((0,external_wp_i18n_namespaceObject._x)('g:i\xa0a', 'post schedule time format'), date));
  }
  const tomorrow = new Date(now);
  tomorrow.setDate(tomorrow.getDate() + 1);
  if (isSameDay(date, tomorrow)) {
    return (0,external_wp_i18n_namespaceObject.sprintf)(
    // translators: %s: Time of day the post is scheduled for.
    (0,external_wp_i18n_namespaceObject.__)('Tomorrow at %s'),
    // translators: If using a space between 'g:i' and 'a', use a non-breaking space.
    (0,external_wp_date_namespaceObject.dateI18n)((0,external_wp_i18n_namespaceObject._x)('g:i\xa0a', 'post schedule time format'), date));
  }
  if (date.getFullYear() === now.getFullYear()) {
    return (0,external_wp_date_namespaceObject.dateI18n)(
    // translators: If using a space between 'g:i' and 'a', use a non-breaking space.
    (0,external_wp_i18n_namespaceObject._x)('F j g:i\xa0a', 'post schedule date format without year'), date);
  }
  return (0,external_wp_date_namespaceObject.dateI18n)(
  // translators: Use a non-breaking space between 'g:i' and 'a' if appropriate.
  (0,external_wp_i18n_namespaceObject._x)('F j, Y g:i\xa0a', 'post schedule full date format'), date);
}
function getTimezoneAbbreviation() {
  const {
    timezone
  } = (0,external_wp_date_namespaceObject.getSettings)();
  if (timezone.abbr && isNaN(Number(timezone.abbr))) {
    return timezone.abbr;
  }
  const symbol = timezone.offset < 0 ? '' : '+';
  return `UTC${symbol}${timezone.offsetFormatted}`;
}
function isTimezoneSameAsSiteTimezone(date) {
  const {
    timezone
  } = (0,external_wp_date_namespaceObject.getSettings)();
  const siteOffset = Number(timezone.offset);
  const dateOffset = -1 * (date.getTimezoneOffset() / 60);
  return siteOffset === dateOffset;
}
function isSameDay(left, right) {
  return left.getDate() === right.getDate() && left.getMonth() === right.getMonth() && left.getFullYear() === right.getFullYear();
}

;// CONCATENATED MODULE: external ["wp","a11y"]
const external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/most-used-terms.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

const MIN_MOST_USED_TERMS = 3;
const DEFAULT_QUERY = {
  per_page: 10,
  orderby: 'count',
  order: 'desc',
  hide_empty: true,
  _fields: 'id,name,count',
  context: 'view'
};
function MostUsedTerms({
  onSelect,
  taxonomy
}) {
  const {
    _terms,
    showTerms
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const mostUsedTerms = select(external_wp_coreData_namespaceObject.store).getEntityRecords('taxonomy', taxonomy.slug, DEFAULT_QUERY);
    return {
      _terms: mostUsedTerms,
      showTerms: mostUsedTerms?.length >= MIN_MOST_USED_TERMS
    };
  }, [taxonomy.slug]);
  if (!showTerms) {
    return null;
  }
  const terms = unescapeTerms(_terms);
  return (0,external_React_.createElement)("div", {
    className: "editor-post-taxonomies__flat-term-most-used"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.BaseControl.VisualLabel, {
    as: "h3",
    className: "editor-post-taxonomies__flat-term-most-used-label"
  }, taxonomy.labels.most_used), (0,external_React_.createElement)("ul", {
    role: "list",
    className: "editor-post-taxonomies__flat-term-most-used-list"
  }, terms.map(term => (0,external_React_.createElement)("li", {
    key: term.id
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "link",
    onClick: () => onSelect(term)
  }, term.name)))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/flat-term-selector.js

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */




/**
 * Shared reference to an empty array for cases where it is important to avoid
 * returning a new array reference on every invocation.
 *
 * @type {Array<any>}
 */
const EMPTY_ARRAY = [];

/**
 * Module constants
 */
const MAX_TERMS_SUGGESTIONS = 20;
const flat_term_selector_DEFAULT_QUERY = {
  per_page: MAX_TERMS_SUGGESTIONS,
  _fields: 'id,name',
  context: 'view'
};
const isSameTermName = (termA, termB) => unescapeString(termA).toLowerCase() === unescapeString(termB).toLowerCase();
const termNamesToIds = (names, terms) => {
  return names.map(termName => terms.find(term => isSameTermName(term.name, termName)).id);
};
function FlatTermSelector({
  slug
}) {
  var _taxonomy$labels$add_, _taxonomy$labels$sing2;
  const [values, setValues] = (0,external_wp_element_namespaceObject.useState)([]);
  const [search, setSearch] = (0,external_wp_element_namespaceObject.useState)('');
  const debouncedSearch = (0,external_wp_compose_namespaceObject.useDebounce)(setSearch, 500);
  const {
    terms,
    termIds,
    taxonomy,
    hasAssignAction,
    hasCreateAction,
    hasResolvedTerms
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _post$_links, _post$_links2;
    const {
      getCurrentPost,
      getEditedPostAttribute
    } = select(store_store);
    const {
      getEntityRecords,
      getTaxonomy,
      hasFinishedResolution
    } = select(external_wp_coreData_namespaceObject.store);
    const post = getCurrentPost();
    const _taxonomy = getTaxonomy(slug);
    const _termIds = _taxonomy ? getEditedPostAttribute(_taxonomy.rest_base) : EMPTY_ARRAY;
    const query = {
      ...flat_term_selector_DEFAULT_QUERY,
      include: _termIds.join(','),
      per_page: -1
    };
    return {
      hasCreateAction: _taxonomy ? (_post$_links = post._links?.['wp:action-create-' + _taxonomy.rest_base]) !== null && _post$_links !== void 0 ? _post$_links : false : false,
      hasAssignAction: _taxonomy ? (_post$_links2 = post._links?.['wp:action-assign-' + _taxonomy.rest_base]) !== null && _post$_links2 !== void 0 ? _post$_links2 : false : false,
      taxonomy: _taxonomy,
      termIds: _termIds,
      terms: _termIds.length ? getEntityRecords('taxonomy', slug, query) : EMPTY_ARRAY,
      hasResolvedTerms: hasFinishedResolution('getEntityRecords', ['taxonomy', slug, query])
    };
  }, [slug]);
  const {
    searchResults
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEntityRecords
    } = select(external_wp_coreData_namespaceObject.store);
    return {
      searchResults: !!search ? getEntityRecords('taxonomy', slug, {
        ...flat_term_selector_DEFAULT_QUERY,
        search
      }) : EMPTY_ARRAY
    };
  }, [search, slug]);

  // Update terms state only after the selectors are resolved.
  // We're using this to avoid terms temporarily disappearing on slow networks
  // while core data makes REST API requests.
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (hasResolvedTerms) {
      const newValues = (terms !== null && terms !== void 0 ? terms : []).map(term => unescapeString(term.name));
      setValues(newValues);
    }
  }, [terms, hasResolvedTerms]);
  const suggestions = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return (searchResults !== null && searchResults !== void 0 ? searchResults : []).map(term => unescapeString(term.name));
  }, [searchResults]);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    saveEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  if (!hasAssignAction) {
    return null;
  }
  async function findOrCreateTerm(term) {
    try {
      const newTerm = await saveEntityRecord('taxonomy', slug, term, {
        throwOnError: true
      });
      return unescapeTerm(newTerm);
    } catch (error) {
      if (error.code !== 'term_exists') {
        throw error;
      }
      return {
        id: error.data.term_id,
        name: term.name
      };
    }
  }
  function onUpdateTerms(newTermIds) {
    editPost({
      [taxonomy.rest_base]: newTermIds
    });
  }
  function onChange(termNames) {
    const availableTerms = [...(terms !== null && terms !== void 0 ? terms : []), ...(searchResults !== null && searchResults !== void 0 ? searchResults : [])];
    const uniqueTerms = termNames.reduce((acc, name) => {
      if (!acc.some(n => n.toLowerCase() === name.toLowerCase())) {
        acc.push(name);
      }
      return acc;
    }, []);
    const newTermNames = uniqueTerms.filter(termName => !availableTerms.find(term => isSameTermName(term.name, termName)));

    // Optimistically update term values.
    // The selector will always re-fetch terms later.
    setValues(uniqueTerms);
    if (newTermNames.length === 0) {
      return onUpdateTerms(termNamesToIds(uniqueTerms, availableTerms));
    }
    if (!hasCreateAction) {
      return;
    }
    Promise.all(newTermNames.map(termName => findOrCreateTerm({
      name: termName
    }))).then(newTerms => {
      const newAvailableTerms = availableTerms.concat(newTerms);
      return onUpdateTerms(termNamesToIds(uniqueTerms, newAvailableTerms));
    }).catch(error => {
      createErrorNotice(error.message, {
        type: 'snackbar'
      });
    });
  }
  function appendTerm(newTerm) {
    var _taxonomy$labels$sing;
    if (termIds.includes(newTerm.id)) {
      return;
    }
    const newTermIds = [...termIds, newTerm.id];
    const defaultName = slug === 'post_tag' ? (0,external_wp_i18n_namespaceObject.__)('Tag') : (0,external_wp_i18n_namespaceObject.__)('Term');
    const termAddedMessage = (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: term name. */
    (0,external_wp_i18n_namespaceObject._x)('%s added', 'term'), (_taxonomy$labels$sing = taxonomy?.labels?.singular_name) !== null && _taxonomy$labels$sing !== void 0 ? _taxonomy$labels$sing : defaultName);
    (0,external_wp_a11y_namespaceObject.speak)(termAddedMessage, 'assertive');
    onUpdateTerms(newTermIds);
  }
  const newTermLabel = (_taxonomy$labels$add_ = taxonomy?.labels?.add_new_item) !== null && _taxonomy$labels$add_ !== void 0 ? _taxonomy$labels$add_ : slug === 'post_tag' ? (0,external_wp_i18n_namespaceObject.__)('Add new tag') : (0,external_wp_i18n_namespaceObject.__)('Add new Term');
  const singularName = (_taxonomy$labels$sing2 = taxonomy?.labels?.singular_name) !== null && _taxonomy$labels$sing2 !== void 0 ? _taxonomy$labels$sing2 : slug === 'post_tag' ? (0,external_wp_i18n_namespaceObject.__)('Tag') : (0,external_wp_i18n_namespaceObject.__)('Term');
  const termAddedLabel = (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: term name. */
  (0,external_wp_i18n_namespaceObject._x)('%s added', 'term'), singularName);
  const termRemovedLabel = (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: term name. */
  (0,external_wp_i18n_namespaceObject._x)('%s removed', 'term'), singularName);
  const removeTermLabel = (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: term name. */
  (0,external_wp_i18n_namespaceObject._x)('Remove %s', 'term'), singularName);
  return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.FormTokenField, {
    __next40pxDefaultSize: true,
    value: values,
    suggestions: suggestions,
    onChange: onChange,
    onInputChange: debouncedSearch,
    maxSuggestions: MAX_TERMS_SUGGESTIONS,
    label: newTermLabel,
    messages: {
      added: termAddedLabel,
      removed: termRemovedLabel,
      remove: removeTermLabel
    }
  }), (0,external_React_.createElement)(MostUsedTerms, {
    taxonomy: taxonomy,
    onSelect: appendTerm
  }));
}
/* harmony default export */ const flat_term_selector = ((0,external_wp_components_namespaceObject.withFilters)('editor.PostTaxonomyType')(FlatTermSelector));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/maybe-tags-panel.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


const TagsPanel = () => {
  const panelBodyTitle = [(0,external_wp_i18n_namespaceObject.__)('Suggestion:'), (0,external_React_.createElement)("span", {
    className: "editor-post-publish-panel__link",
    key: "label"
  }, (0,external_wp_i18n_namespaceObject.__)('Add tags'))];
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    initialOpen: false,
    title: panelBodyTitle
  }, (0,external_React_.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('Tags help users and search engines navigate your site and find your content. Add a few keywords to describe your post.')), (0,external_React_.createElement)(flat_term_selector, {
    slug: 'post_tag'
  }));
};
const MaybeTagsPanel = () => {
  const {
    hasTags,
    isPostTypeSupported
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const postType = select(store_store).getCurrentPostType();
    const tagsTaxonomy = select(external_wp_coreData_namespaceObject.store).getTaxonomy('post_tag');
    const _isPostTypeSupported = tagsTaxonomy?.types?.includes(postType);
    const areTagsFetched = tagsTaxonomy !== undefined;
    const tags = tagsTaxonomy && select(store_store).getEditedPostAttribute(tagsTaxonomy.rest_base);
    return {
      hasTags: !!tags?.length,
      isPostTypeSupported: areTagsFetched && _isPostTypeSupported
    };
  }, []);
  const [hadTagsWhenOpeningThePanel] = (0,external_wp_element_namespaceObject.useState)(hasTags);
  if (!isPostTypeSupported) {
    return null;
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
  if (!hadTagsWhenOpeningThePanel) {
    return (0,external_React_.createElement)(TagsPanel, null);
  }
  return null;
};
/* harmony default export */ const maybe_tags_panel = (MaybeTagsPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/maybe-post-format-panel.js

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


const getSuggestion = (supportedFormats, suggestedPostFormat) => {
  const formats = POST_FORMATS.filter(format => supportedFormats?.includes(format.id));
  return formats.find(format => format.id === suggestedPostFormat);
};
const PostFormatSuggestion = ({
  suggestedPostFormat,
  suggestionText,
  onUpdatePostFormat
}) => (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
  variant: "link",
  onClick: () => onUpdatePostFormat(suggestedPostFormat)
}, suggestionText);
function PostFormatPanel() {
  const {
    currentPostFormat,
    suggestion
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getThemeSuppo;
    const {
      getEditedPostAttribute,
      getSuggestedPostFormat
    } = select(store_store);
    const supportedFormats = (_select$getThemeSuppo = select(external_wp_coreData_namespaceObject.store).getThemeSupports().formats) !== null && _select$getThemeSuppo !== void 0 ? _select$getThemeSuppo : [];
    return {
      currentPostFormat: getEditedPostAttribute('format'),
      suggestion: getSuggestion(supportedFormats, getSuggestedPostFormat())
    };
  }, []);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const onUpdatePostFormat = format => editPost({
    format
  });
  const panelBodyTitle = [(0,external_wp_i18n_namespaceObject.__)('Suggestion:'), (0,external_React_.createElement)("span", {
    className: "editor-post-publish-panel__link",
    key: "label"
  }, (0,external_wp_i18n_namespaceObject.__)('Use a post format'))];
  if (!suggestion || suggestion.id === currentPostFormat) {
    return null;
  }
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    initialOpen: false,
    title: panelBodyTitle
  }, (0,external_React_.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('Your theme uses post formats to highlight different kinds of content, like images or videos. Apply a post format to see this special styling.')), (0,external_React_.createElement)("p", null, (0,external_React_.createElement)(PostFormatSuggestion, {
    onUpdatePostFormat: onUpdatePostFormat,
    suggestedPostFormat: suggestion.id,
    suggestionText: (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: post format */
    (0,external_wp_i18n_namespaceObject.__)('Apply the "%1$s" format.'), suggestion.caption)
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/hierarchical-term-selector.js

/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */



/**
 * Module Constants
 */
const hierarchical_term_selector_DEFAULT_QUERY = {
  per_page: -1,
  orderby: 'name',
  order: 'asc',
  _fields: 'id,name,parent',
  context: 'view'
};
const MIN_TERMS_COUNT_FOR_FILTER = 8;
const hierarchical_term_selector_EMPTY_ARRAY = [];

/**
 * Sort Terms by Selected.
 *
 * @param {Object[]} termsTree Array of terms in tree format.
 * @param {number[]} terms     Selected terms.
 *
 * @return {Object[]} Sorted array of terms.
 */
function sortBySelected(termsTree, terms) {
  const treeHasSelection = termTree => {
    if (terms.indexOf(termTree.id) !== -1) {
      return true;
    }
    if (undefined === termTree.children) {
      return false;
    }
    return termTree.children.map(treeHasSelection).filter(child => child).length > 0;
  };
  const termOrChildIsSelected = (termA, termB) => {
    const termASelected = treeHasSelection(termA);
    const termBSelected = treeHasSelection(termB);
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
  const newTermTree = [...termsTree];
  newTermTree.sort(termOrChildIsSelected);
  return newTermTree;
}

/**
 * Find term by parent id or name.
 *
 * @param {Object[]}      terms  Array of Terms.
 * @param {number|string} parent id.
 * @param {string}        name   Term name.
 * @return {Object} Term object.
 */
function findTerm(terms, parent, name) {
  return terms.find(term => {
    return (!term.parent && !parent || parseInt(term.parent) === parseInt(parent)) && term.name.toLowerCase() === name.toLowerCase();
  });
}

/**
 * Get filter matcher function.
 *
 * @param {string} filterValue Filter value.
 * @return {(function(Object): (Object|boolean))} Matcher function.
 */
function getFilterMatcher(filterValue) {
  const matchTermsForFilter = originalTerm => {
    if ('' === filterValue) {
      return originalTerm;
    }

    // Shallow clone, because we'll be filtering the term's children and
    // don't want to modify the original term.
    const term = {
      ...originalTerm
    };

    // Map and filter the children, recursive so we deal with grandchildren
    // and any deeper levels.
    if (term.children.length > 0) {
      term.children = term.children.map(matchTermsForFilter).filter(child => child);
    }

    // If the term's name contains the filterValue, or it has children
    // (i.e. some child matched at some point in the tree) then return it.
    if (-1 !== term.name.toLowerCase().indexOf(filterValue.toLowerCase()) || term.children.length > 0) {
      return term;
    }

    // Otherwise, return false. After mapping, the list of terms will need
    // to have false values filtered out.
    return false;
  };
  return matchTermsForFilter;
}

/**
 * Hierarchical term selector.
 *
 * @param {Object} props      Component props.
 * @param {string} props.slug Taxonomy slug.
 * @return {Element}        Hierarchical term selector component.
 */
function HierarchicalTermSelector({
  slug
}) {
  var _taxonomy$labels$sear, _taxonomy$name;
  const [adding, setAdding] = (0,external_wp_element_namespaceObject.useState)(false);
  const [formName, setFormName] = (0,external_wp_element_namespaceObject.useState)('');
  /**
   * @type {[number|'', Function]}
   */
  const [formParent, setFormParent] = (0,external_wp_element_namespaceObject.useState)('');
  const [showForm, setShowForm] = (0,external_wp_element_namespaceObject.useState)(false);
  const [filterValue, setFilterValue] = (0,external_wp_element_namespaceObject.useState)('');
  const [filteredTermsTree, setFilteredTermsTree] = (0,external_wp_element_namespaceObject.useState)([]);
  const debouncedSpeak = (0,external_wp_compose_namespaceObject.useDebounce)(external_wp_a11y_namespaceObject.speak, 500);
  const {
    hasCreateAction,
    hasAssignAction,
    terms,
    loading,
    availableTerms,
    taxonomy
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _post$_links, _post$_links2;
    const {
      getCurrentPost,
      getEditedPostAttribute
    } = select(store_store);
    const {
      getTaxonomy,
      getEntityRecords,
      isResolving
    } = select(external_wp_coreData_namespaceObject.store);
    const _taxonomy = getTaxonomy(slug);
    const post = getCurrentPost();
    return {
      hasCreateAction: _taxonomy ? (_post$_links = post._links?.['wp:action-create-' + _taxonomy.rest_base]) !== null && _post$_links !== void 0 ? _post$_links : false : false,
      hasAssignAction: _taxonomy ? (_post$_links2 = post._links?.['wp:action-assign-' + _taxonomy.rest_base]) !== null && _post$_links2 !== void 0 ? _post$_links2 : false : false,
      terms: _taxonomy ? getEditedPostAttribute(_taxonomy.rest_base) : hierarchical_term_selector_EMPTY_ARRAY,
      loading: isResolving('getEntityRecords', ['taxonomy', slug, hierarchical_term_selector_DEFAULT_QUERY]),
      availableTerms: getEntityRecords('taxonomy', slug, hierarchical_term_selector_DEFAULT_QUERY) || hierarchical_term_selector_EMPTY_ARRAY,
      taxonomy: _taxonomy
    };
  }, [slug]);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    saveEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const availableTermsTree = (0,external_wp_element_namespaceObject.useMemo)(() => sortBySelected(buildTermsTree(availableTerms), terms),
  // Remove `terms` from the dependency list to avoid reordering every time
  // checking or unchecking a term.
  [availableTerms]);
  const {
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  if (!hasAssignAction) {
    return null;
  }

  /**
   * Append new term.
   *
   * @param {Object} term Term object.
   * @return {Promise} A promise that resolves to save term object.
   */
  const addTerm = term => {
    return saveEntityRecord('taxonomy', slug, term, {
      throwOnError: true
    });
  };

  /**
   * Update terms for post.
   *
   * @param {number[]} termIds Term ids.
   */
  const onUpdateTerms = termIds => {
    editPost({
      [taxonomy.rest_base]: termIds
    });
  };

  /**
   * Handler for checking term.
   *
   * @param {number} termId
   */
  const onChange = termId => {
    const hasTerm = terms.includes(termId);
    const newTerms = hasTerm ? terms.filter(id => id !== termId) : [...terms, termId];
    onUpdateTerms(newTerms);
  };
  const onChangeFormName = value => {
    setFormName(value);
  };

  /**
   * Handler for changing form parent.
   *
   * @param {number|''} parentId Parent post id.
   */
  const onChangeFormParent = parentId => {
    setFormParent(parentId);
  };
  const onToggleForm = () => {
    setShowForm(!showForm);
  };
  const onAddTerm = async event => {
    var _taxonomy$labels$sing;
    event.preventDefault();
    if (formName === '' || adding) {
      return;
    }

    // Check if the term we are adding already exists.
    const existingTerm = findTerm(availableTerms, formParent, formName);
    if (existingTerm) {
      // If the term we are adding exists but is not selected select it.
      if (!terms.some(term => term === existingTerm.id)) {
        onUpdateTerms([...terms, existingTerm.id]);
      }
      setFormName('');
      setFormParent('');
      return;
    }
    setAdding(true);
    let newTerm;
    try {
      newTerm = await addTerm({
        name: formName,
        parent: formParent ? formParent : undefined
      });
    } catch (error) {
      createErrorNotice(error.message, {
        type: 'snackbar'
      });
      return;
    }
    const defaultName = slug === 'category' ? (0,external_wp_i18n_namespaceObject.__)('Category') : (0,external_wp_i18n_namespaceObject.__)('Term');
    const termAddedMessage = (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: taxonomy name */
    (0,external_wp_i18n_namespaceObject._x)('%s added', 'term'), (_taxonomy$labels$sing = taxonomy?.labels?.singular_name) !== null && _taxonomy$labels$sing !== void 0 ? _taxonomy$labels$sing : defaultName);
    (0,external_wp_a11y_namespaceObject.speak)(termAddedMessage, 'assertive');
    setAdding(false);
    setFormName('');
    setFormParent('');
    onUpdateTerms([...terms, newTerm.id]);
  };
  const setFilter = value => {
    const newFilteredTermsTree = availableTermsTree.map(getFilterMatcher(value)).filter(term => term);
    const getResultCount = termsTree => {
      let count = 0;
      for (let i = 0; i < termsTree.length; i++) {
        count++;
        if (undefined !== termsTree[i].children) {
          count += getResultCount(termsTree[i].children);
        }
      }
      return count;
    };
    setFilterValue(value);
    setFilteredTermsTree(newFilteredTermsTree);
    const resultCount = getResultCount(newFilteredTermsTree);
    const resultsFoundMessage = (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %d: number of results */
    (0,external_wp_i18n_namespaceObject._n)('%d result found.', '%d results found.', resultCount), resultCount);
    debouncedSpeak(resultsFoundMessage, 'assertive');
  };
  const renderTerms = renderedTerms => {
    return renderedTerms.map(term => {
      return (0,external_React_.createElement)("div", {
        key: term.id,
        className: "editor-post-taxonomies__hierarchical-terms-choice"
      }, (0,external_React_.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
        __nextHasNoMarginBottom: true,
        checked: terms.indexOf(term.id) !== -1,
        onChange: () => {
          const termId = parseInt(term.id, 10);
          onChange(termId);
        },
        label: (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(term.name)
      }), !!term.children.length && (0,external_React_.createElement)("div", {
        className: "editor-post-taxonomies__hierarchical-terms-subchoices"
      }, renderTerms(term.children)));
    });
  };
  const labelWithFallback = (labelProperty, fallbackIsCategory, fallbackIsNotCategory) => {
    var _taxonomy$labels$labe;
    return (_taxonomy$labels$labe = taxonomy?.labels?.[labelProperty]) !== null && _taxonomy$labels$labe !== void 0 ? _taxonomy$labels$labe : slug === 'category' ? fallbackIsCategory : fallbackIsNotCategory;
  };
  const newTermButtonLabel = labelWithFallback('add_new_item', (0,external_wp_i18n_namespaceObject.__)('Add new category'), (0,external_wp_i18n_namespaceObject.__)('Add new term'));
  const newTermLabel = labelWithFallback('new_item_name', (0,external_wp_i18n_namespaceObject.__)('Add new category'), (0,external_wp_i18n_namespaceObject.__)('Add new term'));
  const parentSelectLabel = labelWithFallback('parent_item', (0,external_wp_i18n_namespaceObject.__)('Parent Category'), (0,external_wp_i18n_namespaceObject.__)('Parent Term'));
  const noParentOption = `— ${parentSelectLabel} —`;
  const newTermSubmitLabel = newTermButtonLabel;
  const filterLabel = (_taxonomy$labels$sear = taxonomy?.labels?.search_items) !== null && _taxonomy$labels$sear !== void 0 ? _taxonomy$labels$sear : (0,external_wp_i18n_namespaceObject.__)('Search Terms');
  const groupLabel = (_taxonomy$name = taxonomy?.name) !== null && _taxonomy$name !== void 0 ? _taxonomy$name : (0,external_wp_i18n_namespaceObject.__)('Terms');
  const showFilter = availableTerms.length >= MIN_TERMS_COUNT_FOR_FILTER;
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Flex, {
    direction: "column",
    gap: "4"
  }, showFilter && (0,external_React_.createElement)(external_wp_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: filterLabel,
    value: filterValue,
    onChange: setFilter
  }), (0,external_React_.createElement)("div", {
    className: "editor-post-taxonomies__hierarchical-terms-list",
    tabIndex: "0",
    role: "group",
    "aria-label": groupLabel
  }, renderTerms('' !== filterValue ? filteredTermsTree : availableTermsTree)), !loading && hasCreateAction && (0,external_React_.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    onClick: onToggleForm,
    className: "editor-post-taxonomies__hierarchical-terms-add",
    "aria-expanded": showForm,
    variant: "link"
  }, newTermButtonLabel)), showForm && (0,external_React_.createElement)("form", {
    onSubmit: onAddTerm
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Flex, {
    direction: "column",
    gap: "4"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    className: "editor-post-taxonomies__hierarchical-terms-input",
    label: newTermLabel,
    value: formName,
    onChange: onChangeFormName,
    required: true
  }), !!availableTerms.length && (0,external_React_.createElement)(external_wp_components_namespaceObject.TreeSelect, {
    __nextHasNoMarginBottom: true,
    label: parentSelectLabel,
    noOptionLabel: noParentOption,
    onChange: onChangeFormParent,
    selectedId: formParent,
    tree: availableTermsTree
  }), (0,external_React_.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "secondary",
    type: "submit",
    className: "editor-post-taxonomies__hierarchical-terms-submit"
  }, newTermSubmitLabel)))));
}
/* harmony default export */ const hierarchical_term_selector = ((0,external_wp_components_namespaceObject.withFilters)('editor.PostTaxonomyType')(HierarchicalTermSelector));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/maybe-category-panel.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function MaybeCategoryPanel() {
  const hasNoCategory = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const postType = select(store_store).getCurrentPostType();
    const {
      canUser,
      getEntityRecord,
      getTaxonomy
    } = select(external_wp_coreData_namespaceObject.store);
    const categoriesTaxonomy = getTaxonomy('category');
    const defaultCategoryId = canUser('read', 'settings') ? getEntityRecord('root', 'site')?.default_category : undefined;
    const defaultCategory = defaultCategoryId ? getEntityRecord('taxonomy', 'category', defaultCategoryId) : undefined;
    const postTypeSupportsCategories = categoriesTaxonomy && categoriesTaxonomy.types.some(type => type === postType);
    const categories = categoriesTaxonomy && select(store_store).getEditedPostAttribute(categoriesTaxonomy.rest_base);

    // This boolean should return true if everything is loaded
    // ( categoriesTaxonomy, defaultCategory )
    // and the post has not been assigned a category different than "uncategorized".
    return !!categoriesTaxonomy && !!defaultCategory && postTypeSupportsCategories && (categories?.length === 0 || categories?.length === 1 && defaultCategory?.id === categories[0]);
  }, []);
  const [shouldShowPanel, setShouldShowPanel] = (0,external_wp_element_namespaceObject.useState)(false);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // We use state to avoid hiding the panel if the user edits the categories
    // and adds one within the panel itself (while visible).
    if (hasNoCategory) {
      setShouldShowPanel(true);
    }
  }, [hasNoCategory]);
  if (!shouldShowPanel) {
    return null;
  }
  const panelBodyTitle = [(0,external_wp_i18n_namespaceObject.__)('Suggestion:'), (0,external_React_.createElement)("span", {
    className: "editor-post-publish-panel__link",
    key: "label"
  }, (0,external_wp_i18n_namespaceObject.__)('Assign a category'))];
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    initialOpen: false,
    title: panelBodyTitle
  }, (0,external_React_.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('Categories provide a helpful way to group related posts together and to quickly tell readers what a post is about.')), (0,external_React_.createElement)(hierarchical_term_selector, {
    slug: "category"
  }));
}
/* harmony default export */ const maybe_category_panel = (MaybeCategoryPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/maybe-upload-media.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */

function flattenBlocks(blocks) {
  const result = [];
  blocks.forEach(block => {
    result.push(block);
    result.push(...flattenBlocks(block.innerBlocks));
  });
  return result;
}
function Image(block) {
  const {
    selectBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.__unstableMotion.img, {
    tabIndex: 0,
    role: "button",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Select image block.'),
    onClick: () => {
      selectBlock(block.clientId);
    },
    onKeyDown: event => {
      if (event.key === 'Enter' || event.key === ' ') {
        selectBlock(block.clientId);
        event.preventDefault();
      }
    },
    key: block.clientId,
    alt: block.attributes.alt,
    src: block.attributes.url,
    animate: {
      opacity: 1
    },
    exit: {
      opacity: 0,
      scale: 0
    },
    style: {
      width: '36px',
      height: '36px',
      objectFit: 'cover',
      borderRadius: '2px',
      cursor: 'pointer'
    },
    whileHover: {
      scale: 1.08
    }
  });
}
function maybe_upload_media_PostFormatPanel() {
  const [isUploading, setIsUploading] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    editorBlocks,
    mediaUpload
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    editorBlocks: select(store_store).getEditorBlocks(),
    mediaUpload: select(external_wp_blockEditor_namespaceObject.store).getSettings().mediaUpload
  }), []);
  const externalImages = flattenBlocks(editorBlocks).filter(block => block.name === 'core/image' && block.attributes.url && !block.attributes.id);
  const {
    updateBlockAttributes
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  if (!mediaUpload || !externalImages.length) {
    return null;
  }
  const panelBodyTitle = [(0,external_wp_i18n_namespaceObject.__)('Suggestion:'), (0,external_React_.createElement)("span", {
    className: "editor-post-publish-panel__link",
    key: "label"
  }, (0,external_wp_i18n_namespaceObject.__)('External media'))];
  function uploadImages() {
    setIsUploading(true);
    Promise.all(externalImages.map(image => window.fetch(image.attributes.url.includes('?') ? image.attributes.url : image.attributes.url + '?').then(response => response.blob()).then(blob => new Promise((resolve, reject) => {
      mediaUpload({
        filesList: [blob],
        onFileChange: ([media]) => {
          if ((0,external_wp_blob_namespaceObject.isBlobURL)(media.url)) {
            return;
          }
          updateBlockAttributes(image.clientId, {
            id: media.id,
            url: media.url
          });
          resolve();
        },
        onError() {
          reject();
        }
      });
    })))).finally(() => {
      setIsUploading(false);
    });
  }
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    initialOpen: true,
    title: panelBodyTitle
  }, (0,external_React_.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('Upload external images to the Media Library. Images from different domains may load slowly, display incorrectly, or be removed unexpectedly.')), (0,external_React_.createElement)("div", {
    style: {
      display: 'inline-flex',
      flexWrap: 'wrap',
      gap: '8px'
    }
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.__unstableAnimatePresence, null, externalImages.map(image => {
    return (0,external_React_.createElement)(Image, {
      key: image.clientId,
      ...image
    });
  })), isUploading ? (0,external_React_.createElement)(external_wp_components_namespaceObject.Spinner, null) : (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    onClick: uploadImages
  }, (0,external_wp_i18n_namespaceObject.__)('Upload'))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/prepublish.js

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */









function PostPublishPanelPrepublish({
  children
}) {
  const {
    isBeingScheduled,
    isRequestingSiteIcon,
    hasPublishAction,
    siteIconUrl,
    siteTitle,
    siteHome
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getCurrentPost$_link;
    const {
      getCurrentPost,
      isEditedPostBeingScheduled
    } = select(store_store);
    const {
      getEntityRecord,
      isResolving
    } = select(external_wp_coreData_namespaceObject.store);
    const siteData = getEntityRecord('root', '__unstableBase', undefined) || {};
    return {
      hasPublishAction: (_getCurrentPost$_link = getCurrentPost()._links?.['wp:action-publish']) !== null && _getCurrentPost$_link !== void 0 ? _getCurrentPost$_link : false,
      isBeingScheduled: isEditedPostBeingScheduled(),
      isRequestingSiteIcon: isResolving('getEntityRecord', ['root', '__unstableBase', undefined]),
      siteIconUrl: siteData.site_icon_url,
      siteTitle: siteData.name,
      siteHome: siteData.home && (0,external_wp_url_namespaceObject.filterURLForDisplay)(siteData.home)
    };
  }, []);
  let siteIcon = (0,external_React_.createElement)(external_wp_components_namespaceObject.Icon, {
    className: "components-site-icon",
    size: "36px",
    icon: library_wordpress
  });
  if (siteIconUrl) {
    siteIcon = (0,external_React_.createElement)("img", {
      alt: (0,external_wp_i18n_namespaceObject.__)('Site Icon'),
      className: "components-site-icon",
      src: siteIconUrl
    });
  }
  if (isRequestingSiteIcon) {
    siteIcon = null;
  }
  let prePublishTitle, prePublishBodyText;
  if (!hasPublishAction) {
    prePublishTitle = (0,external_wp_i18n_namespaceObject.__)('Are you ready to submit for review?');
    prePublishBodyText = (0,external_wp_i18n_namespaceObject.__)('When you’re ready, submit your work for review, and an Editor will be able to approve it for you.');
  } else if (isBeingScheduled) {
    prePublishTitle = (0,external_wp_i18n_namespaceObject.__)('Are you ready to schedule?');
    prePublishBodyText = (0,external_wp_i18n_namespaceObject.__)('Your work will be published at the specified date and time.');
  } else {
    prePublishTitle = (0,external_wp_i18n_namespaceObject.__)('Are you ready to publish?');
    prePublishBodyText = (0,external_wp_i18n_namespaceObject.__)('Double-check your settings before publishing.');
  }
  return (0,external_React_.createElement)("div", {
    className: "editor-post-publish-panel__prepublish"
  }, (0,external_React_.createElement)("div", null, (0,external_React_.createElement)("strong", null, prePublishTitle)), (0,external_React_.createElement)("p", null, prePublishBodyText), (0,external_React_.createElement)("div", {
    className: "components-site-card"
  }, siteIcon, (0,external_React_.createElement)("div", {
    className: "components-site-info"
  }, (0,external_React_.createElement)("span", {
    className: "components-site-name"
  }, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(siteTitle) || (0,external_wp_i18n_namespaceObject.__)('(Untitled)')), (0,external_React_.createElement)("span", {
    className: "components-site-home"
  }, siteHome))), (0,external_React_.createElement)(maybe_upload_media_PostFormatPanel, null), hasPublishAction && (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    initialOpen: false,
    title: [(0,external_wp_i18n_namespaceObject.__)('Visibility:'), (0,external_React_.createElement)("span", {
      className: "editor-post-publish-panel__link",
      key: "label"
    }, (0,external_React_.createElement)(PostVisibilityLabel, null))]
  }, (0,external_React_.createElement)(PostVisibility, null)), (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    initialOpen: false,
    title: [(0,external_wp_i18n_namespaceObject.__)('Publish:'), (0,external_React_.createElement)("span", {
      className: "editor-post-publish-panel__link",
      key: "label"
    }, (0,external_React_.createElement)(PostScheduleLabel, null))]
  }, (0,external_React_.createElement)(PostSchedule, null))), (0,external_React_.createElement)(PostFormatPanel, null), (0,external_React_.createElement)(maybe_tags_panel, null), (0,external_React_.createElement)(maybe_category_panel, null), children);
}
/* harmony default export */ const prepublish = (PostPublishPanelPrepublish);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/postpublish.js

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */


const POSTNAME = '%postname%';
const PAGENAME = '%pagename%';

/**
 * Returns URL for a future post.
 *
 * @param {Object} post Post object.
 *
 * @return {string} PostPublish URL.
 */

const getFuturePostUrl = post => {
  const {
    slug
  } = post;
  if (post.permalink_template.includes(POSTNAME)) {
    return post.permalink_template.replace(POSTNAME, slug);
  }
  if (post.permalink_template.includes(PAGENAME)) {
    return post.permalink_template.replace(PAGENAME, slug);
  }
  return post.permalink_template;
};
function postpublish_CopyButton({
  text,
  onCopy,
  children
}) {
  const ref = (0,external_wp_compose_namespaceObject.useCopyToClipboard)(text, onCopy);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "secondary",
    ref: ref
  }, children);
}
class PostPublishPanelPostpublish extends external_wp_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.state = {
      showCopyConfirmation: false
    };
    this.onCopy = this.onCopy.bind(this);
    this.onSelectInput = this.onSelectInput.bind(this);
    this.postLink = (0,external_wp_element_namespaceObject.createRef)();
  }
  componentDidMount() {
    if (this.props.focusOnMount) {
      this.postLink.current.focus();
    }
  }
  componentWillUnmount() {
    clearTimeout(this.dismissCopyConfirmation);
  }
  onCopy() {
    this.setState({
      showCopyConfirmation: true
    });
    clearTimeout(this.dismissCopyConfirmation);
    this.dismissCopyConfirmation = setTimeout(() => {
      this.setState({
        showCopyConfirmation: false
      });
    }, 4000);
  }
  onSelectInput(event) {
    event.target.select();
  }
  render() {
    const {
      children,
      isScheduled,
      post,
      postType
    } = this.props;
    const postLabel = postType?.labels?.singular_name;
    const viewPostLabel = postType?.labels?.view_item;
    const addNewPostLabel = postType?.labels?.add_new_item;
    const link = post.status === 'future' ? getFuturePostUrl(post) : post.link;
    const addLink = (0,external_wp_url_namespaceObject.addQueryArgs)('post-new.php', {
      post_type: post.type
    });
    const postPublishNonLinkHeader = isScheduled ? (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_wp_i18n_namespaceObject.__)('is now scheduled. It will go live on'), ' ', (0,external_React_.createElement)(PostScheduleLabel, null), ".") : (0,external_wp_i18n_namespaceObject.__)('is now live.');
    return (0,external_React_.createElement)("div", {
      className: "post-publish-panel__postpublish"
    }, (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
      className: "post-publish-panel__postpublish-header"
    }, (0,external_React_.createElement)("a", {
      ref: this.postLink,
      href: link
    }, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(post.title) || (0,external_wp_i18n_namespaceObject.__)('(no title)')), ' ', postPublishNonLinkHeader), (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, null, (0,external_React_.createElement)("p", {
      className: "post-publish-panel__postpublish-subheader"
    }, (0,external_React_.createElement)("strong", null, (0,external_wp_i18n_namespaceObject.__)('What’s next?'))), (0,external_React_.createElement)("div", {
      className: "post-publish-panel__postpublish-post-address-container"
    }, (0,external_React_.createElement)(external_wp_components_namespaceObject.TextControl, {
      __nextHasNoMarginBottom: true,
      className: "post-publish-panel__postpublish-post-address",
      readOnly: true,
      label: (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: post type singular name */
      (0,external_wp_i18n_namespaceObject.__)('%s address'), postLabel),
      value: (0,external_wp_url_namespaceObject.safeDecodeURIComponent)(link),
      onFocus: this.onSelectInput
    }), (0,external_React_.createElement)("div", {
      className: "post-publish-panel__postpublish-post-address__copy-button-wrap"
    }, (0,external_React_.createElement)(postpublish_CopyButton, {
      text: link,
      onCopy: this.onCopy
    }, this.state.showCopyConfirmation ? (0,external_wp_i18n_namespaceObject.__)('Copied!') : (0,external_wp_i18n_namespaceObject.__)('Copy')))), (0,external_React_.createElement)("div", {
      className: "post-publish-panel__postpublish-buttons"
    }, !isScheduled && (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      variant: "primary",
      href: link
    }, viewPostLabel), (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      variant: isScheduled ? 'primary' : 'secondary',
      href: addLink
    }, addNewPostLabel))), children);
  }
}
/* harmony default export */ const postpublish = ((0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getEditedPostAttribute,
    getCurrentPost,
    isCurrentPostScheduled
  } = select(store_store);
  const {
    getPostType
  } = select(external_wp_coreData_namespaceObject.store);
  return {
    post: getCurrentPost(),
    postType: getPostType(getEditedPostAttribute('type')),
    isScheduled: isCurrentPostScheduled()
  };
})(PostPublishPanelPostpublish));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-panel/index.js

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */




class PostPublishPanel extends external_wp_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.onSubmit = this.onSubmit.bind(this);
  }
  componentDidUpdate(prevProps) {
    // Automatically collapse the publish sidebar when a post
    // is published and the user makes an edit.
    if (prevProps.isPublished && !this.props.isSaving && this.props.isDirty) {
      this.props.onClose();
    }
  }
  onSubmit() {
    const {
      onClose,
      hasPublishAction,
      isPostTypeViewable
    } = this.props;
    if (!hasPublishAction || !isPostTypeViewable) {
      onClose();
    }
  }
  render() {
    const {
      forceIsDirty,
      isBeingScheduled,
      isPublished,
      isPublishSidebarEnabled,
      isScheduled,
      isSaving,
      isSavingNonPostEntityChanges,
      onClose,
      onTogglePublishSidebar,
      PostPublishExtension,
      PrePublishExtension,
      ...additionalProps
    } = this.props;
    const {
      hasPublishAction,
      isDirty,
      isPostTypeViewable,
      ...propsForPanel
    } = additionalProps;
    const isPublishedOrScheduled = isPublished || isScheduled && isBeingScheduled;
    const isPrePublish = !isPublishedOrScheduled && !isSaving;
    const isPostPublish = isPublishedOrScheduled && !isSaving;
    return (0,external_React_.createElement)("div", {
      className: "editor-post-publish-panel",
      ...propsForPanel
    }, (0,external_React_.createElement)("div", {
      className: "editor-post-publish-panel__header"
    }, isPostPublish ? (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      onClick: onClose,
      icon: close_small,
      label: (0,external_wp_i18n_namespaceObject.__)('Close panel')
    }) : (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)("div", {
      className: "editor-post-publish-panel__header-publish-button"
    }, (0,external_React_.createElement)(post_publish_button, {
      focusOnMount: true,
      onSubmit: this.onSubmit,
      forceIsDirty: forceIsDirty
    })), (0,external_React_.createElement)("div", {
      className: "editor-post-publish-panel__header-cancel-button"
    }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      disabled: isSavingNonPostEntityChanges,
      onClick: onClose,
      variant: "secondary"
    }, (0,external_wp_i18n_namespaceObject.__)('Cancel'))))), (0,external_React_.createElement)("div", {
      className: "editor-post-publish-panel__content"
    }, isPrePublish && (0,external_React_.createElement)(prepublish, null, PrePublishExtension && (0,external_React_.createElement)(PrePublishExtension, null)), isPostPublish && (0,external_React_.createElement)(postpublish, {
      focusOnMount: true
    }, PostPublishExtension && (0,external_React_.createElement)(PostPublishExtension, null)), isSaving && (0,external_React_.createElement)(external_wp_components_namespaceObject.Spinner, null)), (0,external_React_.createElement)("div", {
      className: "editor-post-publish-panel__footer"
    }, (0,external_React_.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
      __nextHasNoMarginBottom: true,
      label: (0,external_wp_i18n_namespaceObject.__)('Always show pre-publish checks.'),
      checked: isPublishSidebarEnabled,
      onChange: onTogglePublishSidebar
    })));
  }
}
/* harmony default export */ const post_publish_panel = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
  var _getCurrentPost$_link;
  const {
    getPostType
  } = select(external_wp_coreData_namespaceObject.store);
  const {
    getCurrentPost,
    getEditedPostAttribute,
    isCurrentPostPublished,
    isCurrentPostScheduled,
    isEditedPostBeingScheduled,
    isEditedPostDirty,
    isAutosavingPost,
    isSavingPost,
    isSavingNonPostEntityChanges
  } = select(store_store);
  const {
    isPublishSidebarEnabled
  } = select(store_store);
  const postType = getPostType(getEditedPostAttribute('type'));
  return {
    hasPublishAction: (_getCurrentPost$_link = getCurrentPost()._links?.['wp:action-publish']) !== null && _getCurrentPost$_link !== void 0 ? _getCurrentPost$_link : false,
    isPostTypeViewable: postType?.viewable,
    isBeingScheduled: isEditedPostBeingScheduled(),
    isDirty: isEditedPostDirty(),
    isPublished: isCurrentPostPublished(),
    isPublishSidebarEnabled: isPublishSidebarEnabled(),
    isSaving: isSavingPost() && !isAutosavingPost(),
    isSavingNonPostEntityChanges: isSavingNonPostEntityChanges(),
    isScheduled: isCurrentPostScheduled()
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  isPublishSidebarEnabled
}) => {
  const {
    disablePublishSidebar,
    enablePublishSidebar
  } = dispatch(store_store);
  return {
    onTogglePublishSidebar: () => {
      if (isPublishSidebarEnabled) {
        disablePublishSidebar();
      } else {
        enablePublishSidebar();
      }
    }
  };
}), external_wp_components_namespaceObject.withFocusReturn, external_wp_components_namespaceObject.withConstrainedTabbing])(PostPublishPanel));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/cloud-upload.js

/**
 * WordPress dependencies
 */

const cloudUpload = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17.3 10.1c0-2.5-2.1-4.4-4.8-4.4-2.2 0-4.1 1.4-4.6 3.3h-.2C5.7 9 4 10.7 4 12.8c0 2.1 1.7 3.8 3.7 3.8h9c1.8 0 3.2-1.5 3.2-3.3.1-1.6-1.1-2.9-2.6-3.2zm-.5 5.1h-4v-2.4L14 14l1-1-3-3-3 3 1 1 1.2-1.2v2.4H7.7c-1.2 0-2.2-1.1-2.2-2.3s1-2.4 2.2-2.4H9l.3-1.1c.4-1.3 1.7-2.2 3.2-2.2 1.8 0 3.3 1.3 3.3 2.9v1.3l1.3.2c.8.1 1.4.9 1.4 1.8 0 1-.8 1.8-1.7 1.8z"
}));
/* harmony default export */ const cloud_upload = (cloudUpload);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */


/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps}                                 props icon is the SVG component to render
 *                                                          size is a number specifiying the icon size in pixels
 *                                                          Other props will be passed to wrapped SVG component
 * @param {import('react').ForwardedRef<HTMLElement>} ref   The forwarded ref to the SVG element.
 *
 * @return {JSX.Element}  Icon component
 */
function Icon({
  icon,
  size = 24,
  ...props
}, ref) {
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props,
    ref
  });
}
/* harmony default export */ const icon = ((0,external_wp_element_namespaceObject.forwardRef)(Icon));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/cloud.js

/**
 * WordPress dependencies
 */

const cloud = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17.3 10.1c0-2.5-2.1-4.4-4.8-4.4-2.2 0-4.1 1.4-4.6 3.3h-.2C5.7 9 4 10.7 4 12.8c0 2.1 1.7 3.8 3.7 3.8h9c1.8 0 3.2-1.5 3.2-3.3.1-1.6-1.1-2.9-2.6-3.2zm-.5 5.1h-9c-1.2 0-2.2-1.1-2.2-2.3s1-2.4 2.2-2.4h1.3l.3-1.1c.4-1.3 1.7-2.2 3.2-2.2 1.8 0 3.3 1.3 3.3 2.9v1.3l1.3.2c.8.1 1.4.9 1.4 1.8-.1 1-.9 1.8-1.8 1.8z"
}));
/* harmony default export */ const library_cloud = (cloud);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-saved-state/index.js

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
 * Component showing whether the post is saved or not and providing save
 * buttons.
 *
 * @param {Object}   props              Component props.
 * @param {?boolean} props.forceIsDirty Whether to force the post to be marked
 *                                      as dirty.
 * @return {import('react').ComponentType} The component.
 */
function PostSavedState({
  forceIsDirty
}) {
  const [forceSavedMessage, setForceSavedMessage] = (0,external_wp_element_namespaceObject.useState)(false);
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('small');
  const {
    isAutosaving,
    isDirty,
    isNew,
    isPending,
    isPublished,
    isSaveable,
    isSaving,
    isScheduled,
    hasPublishAction,
    showIconLabels
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getCurrentPost$_link;
    const {
      isEditedPostNew,
      isCurrentPostPublished,
      isCurrentPostScheduled,
      isEditedPostDirty,
      isSavingPost,
      isEditedPostSaveable,
      getCurrentPost,
      isAutosavingPost,
      getEditedPostAttribute
    } = select(store_store);
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    return {
      isAutosaving: isAutosavingPost(),
      isDirty: forceIsDirty || isEditedPostDirty(),
      isNew: isEditedPostNew(),
      isPending: 'pending' === getEditedPostAttribute('status'),
      isPublished: isCurrentPostPublished(),
      isSaving: isSavingPost(),
      isSaveable: isEditedPostSaveable(),
      isScheduled: isCurrentPostScheduled(),
      hasPublishAction: (_getCurrentPost$_link = getCurrentPost()?._links?.['wp:action-publish']) !== null && _getCurrentPost$_link !== void 0 ? _getCurrentPost$_link : false,
      showIconLabels: get('core', 'showIconLabels')
    };
  }, [forceIsDirty]);
  const {
    savePost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const wasSaving = (0,external_wp_compose_namespaceObject.usePrevious)(isSaving);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    let timeoutId;
    if (wasSaving && !isSaving) {
      setForceSavedMessage(true);
      timeoutId = setTimeout(() => {
        setForceSavedMessage(false);
      }, 1000);
    }
    return () => clearTimeout(timeoutId);
  }, [isSaving]);

  // Once the post has been submitted for review this button
  // is not needed for the contributor role.
  if (!hasPublishAction && isPending) {
    return null;
  }
  if (isPublished || isScheduled) {
    return null;
  }

  /* translators: button label text should, if possible, be under 16 characters. */
  const label = isPending ? (0,external_wp_i18n_namespaceObject.__)('Save as pending') : (0,external_wp_i18n_namespaceObject.__)('Save draft');

  /* translators: button label text should, if possible, be under 16 characters. */
  const shortLabel = (0,external_wp_i18n_namespaceObject.__)('Save');
  const isSaved = forceSavedMessage || !isNew && !isDirty;
  const isSavedState = isSaving || isSaved;
  const isDisabled = isSaving || isSaved || !isSaveable;
  let text;
  if (isSaving) {
    text = isAutosaving ? (0,external_wp_i18n_namespaceObject.__)('Autosaving') : (0,external_wp_i18n_namespaceObject.__)('Saving');
  } else if (isSaved) {
    text = (0,external_wp_i18n_namespaceObject.__)('Saved');
  } else if (isLargeViewport) {
    text = label;
  } else if (showIconLabels) {
    text = shortLabel;
  }

  // Use common Button instance for all saved states so that focus is not
  // lost.
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    className: isSaveable || isSaving ? classnames_default()({
      'editor-post-save-draft': !isSavedState,
      'editor-post-saved-state': isSavedState,
      'is-saving': isSaving,
      'is-autosaving': isAutosaving,
      'is-saved': isSaved,
      [(0,external_wp_components_namespaceObject.__unstableGetAnimateClassName)({
        type: 'loading'
      })]: isSaving
    }) : undefined,
    onClick: isDisabled ? undefined : () => savePost()
    /*
     * We want the tooltip to show the keyboard shortcut only when the
     * button does something, i.e. when it's not disabled.
     */,
    shortcut: isDisabled ? undefined : external_wp_keycodes_namespaceObject.displayShortcut.primary('s'),
    variant: "tertiary",
    size: "compact",
    icon: isLargeViewport ? undefined : cloud_upload,
    label: text || label,
    "aria-disabled": isDisabled
  }, isSavedState && (0,external_React_.createElement)(icon, {
    icon: isSaved ? library_check : library_cloud
  }), text);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/check.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

function PostScheduleCheck({
  children
}) {
  const hasPublishAction = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getCurrentPos;
    return (_select$getCurrentPos = select(store_store).getCurrentPost()._links?.['wp:action-publish']) !== null && _select$getCurrentPos !== void 0 ? _select$getCurrentPos : false;
  }, []);
  if (!hasPublishAction) {
    return null;
  }
  return children;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/panel.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




function PostSchedulePanel() {
  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null);
  // Memoize popoverProps to avoid returning a new object every time.
  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    // Anchor the popover to the middle of the entire row so that it doesn't
    // move around when the label changes.
    anchor: popoverAnchor,
    'aria-label': (0,external_wp_i18n_namespaceObject.__)('Change publish date'),
    placement: 'bottom-end'
  }), [popoverAnchor]);
  const label = usePostScheduleLabel();
  const fullLabel = usePostScheduleLabel({
    full: true
  });
  return (0,external_React_.createElement)(PostScheduleCheck, null, (0,external_React_.createElement)(post_panel_row, {
    label: (0,external_wp_i18n_namespaceObject.__)('Publish'),
    ref: setPopoverAnchor
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    focusOnMount: true,
    className: "editor-post-schedule__panel-dropdown",
    contentClassName: "editor-post-schedule__dialog",
    renderToggle: ({
      onToggle,
      isOpen
    }) => (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      __next40pxDefaultSize: true,
      className: "editor-post-schedule__dialog-toggle",
      variant: "tertiary",
      onClick: onToggle,
      "aria-label": (0,external_wp_i18n_namespaceObject.sprintf)(
      // translators: %s: Current post date.
      (0,external_wp_i18n_namespaceObject.__)('Change date: %s'), label),
      label: fullLabel,
      showTooltip: label !== fullLabel,
      "aria-expanded": isOpen
    }, label),
    renderContent: ({
      onClose
    }) => (0,external_React_.createElement)(PostSchedule, {
      onClose: onClose
    })
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-slug/check.js

/**
 * Internal dependencies
 */

function PostSlugCheck({
  children
}) {
  return (0,external_React_.createElement)(post_type_support_check, {
    supportKeys: "slug"
  }, children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-slug/index.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


class PostSlug extends external_wp_element_namespaceObject.Component {
  constructor({
    postSlug,
    postTitle,
    postID
  }) {
    super(...arguments);
    this.state = {
      editedSlug: (0,external_wp_url_namespaceObject.safeDecodeURIComponent)(postSlug) || (0,external_wp_url_namespaceObject.cleanForSlug)(postTitle) || postID
    };
    this.setSlug = this.setSlug.bind(this);
  }
  setSlug(event) {
    const {
      postSlug,
      onUpdateSlug
    } = this.props;
    const {
      value
    } = event.target;
    const editedSlug = (0,external_wp_url_namespaceObject.cleanForSlug)(value);
    if (editedSlug === postSlug) {
      return;
    }
    onUpdateSlug(editedSlug);
  }
  render() {
    const {
      editedSlug
    } = this.state;
    return (0,external_React_.createElement)(PostSlugCheck, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.TextControl, {
      __nextHasNoMarginBottom: true,
      label: (0,external_wp_i18n_namespaceObject.__)('Slug'),
      autoComplete: "off",
      spellCheck: "false",
      value: editedSlug,
      onChange: slug => this.setState({
        editedSlug: slug
      }),
      onBlur: this.setSlug,
      className: "editor-post-slug"
    }));
  }
}
/* harmony default export */ const post_slug = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    getCurrentPost,
    getEditedPostAttribute
  } = select(store_store);
  const {
    id
  } = getCurrentPost();
  return {
    postSlug: getEditedPostAttribute('slug'),
    postTitle: getEditedPostAttribute('title'),
    postID: id
  };
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    editPost
  } = dispatch(store_store);
  return {
    onUpdateSlug(slug) {
      editPost({
        slug
      });
    }
  };
})])(PostSlug));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-sticky/check.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

function PostStickyCheck({
  children
}) {
  const {
    hasStickyAction,
    postType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _post$_links$wpActio;
    const post = select(store_store).getCurrentPost();
    return {
      hasStickyAction: (_post$_links$wpActio = post._links?.['wp:action-sticky']) !== null && _post$_links$wpActio !== void 0 ? _post$_links$wpActio : false,
      postType: select(store_store).getCurrentPostType()
    };
  }, []);
  if (postType !== 'post' || !hasStickyAction) {
    return null;
  }
  return children;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-sticky/index.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function PostSticky() {
  const postSticky = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getEditedPost;
    return (_select$getEditedPost = select(store_store).getEditedPostAttribute('sticky')) !== null && _select$getEditedPost !== void 0 ? _select$getEditedPost : false;
  }, []);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_React_.createElement)(PostStickyCheck, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Stick to the top of the blog'),
    checked: postSticky,
    onChange: () => editPost({
      sticky: !postSticky
    })
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-switch-to-draft-button/index.js

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */

function PostSwitchToDraftButton() {
  const [showConfirmDialog, setShowConfirmDialog] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    editPost,
    savePost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    isSaving,
    isPublished,
    isScheduled
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isSavingPost,
      isCurrentPostPublished,
      isCurrentPostScheduled
    } = select(store_store);
    return {
      isSaving: isSavingPost(),
      isPublished: isCurrentPostPublished(),
      isScheduled: isCurrentPostScheduled()
    };
  }, []);
  const isDisabled = isSaving || !isPublished && !isScheduled;
  let alertMessage;
  if (isPublished) {
    alertMessage = (0,external_wp_i18n_namespaceObject.__)('Are you sure you want to unpublish this post?');
  } else if (isScheduled) {
    alertMessage = (0,external_wp_i18n_namespaceObject.__)('Are you sure you want to unschedule this post?');
  }
  const handleConfirm = () => {
    setShowConfirmDialog(false);
    editPost({
      status: 'draft'
    });
    savePost();
  };
  return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    __next40pxDefaultSize: true,
    className: "editor-post-switch-to-draft",
    onClick: () => {
      if (!isDisabled) {
        setShowConfirmDialog(true);
      }
    },
    "aria-disabled": isDisabled,
    variant: "secondary",
    style: {
      flexGrow: '1',
      justifyContent: 'center'
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Switch to draft')), (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalConfirmDialog, {
    isOpen: showConfirmDialog,
    onConfirm: handleConfirm,
    onCancel: () => setShowConfirmDialog(false)
  }, alertMessage));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-sync-status/index.js

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function PostSyncStatus() {
  const {
    syncStatus,
    postType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute
    } = select(store_store);
    const meta = getEditedPostAttribute('meta');

    // When the post is first created, the top level wp_pattern_sync_status is not set so get meta value instead.
    const currentSyncStatus = meta?.wp_pattern_sync_status === 'unsynced' ? 'unsynced' : getEditedPostAttribute('wp_pattern_sync_status');
    return {
      syncStatus: currentSyncStatus,
      postType: getEditedPostAttribute('type')
    };
  });
  if (postType !== 'wp_block') {
    return null;
  }
  return (0,external_React_.createElement)(post_panel_row, {
    label: (0,external_wp_i18n_namespaceObject.__)('Sync status')
  }, (0,external_React_.createElement)("div", {
    className: "editor-post-sync-status__value"
  }, syncStatus === 'unsynced' ? (0,external_wp_i18n_namespaceObject._x)('Not synced', 'Text that indicates that the pattern is not synchronized') : (0,external_wp_i18n_namespaceObject._x)('Synced', 'Text that indicates that the pattern is synchronized')));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/index.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



const post_taxonomies_identity = x => x;
function PostTaxonomies({
  taxonomyWrapper = post_taxonomies_identity
}) {
  const {
    postType,
    taxonomies
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return {
      postType: select(store_store).getCurrentPostType(),
      taxonomies: select(external_wp_coreData_namespaceObject.store).getTaxonomies({
        per_page: -1
      })
    };
  }, []);
  const visibleTaxonomies = (taxonomies !== null && taxonomies !== void 0 ? taxonomies : []).filter(taxonomy =>
  // In some circumstances .visibility can end up as undefined so optional chaining operator required.
  // https://github.com/WordPress/gutenberg/issues/40326
  taxonomy.types.includes(postType) && taxonomy.visibility?.show_ui);
  return visibleTaxonomies.map(taxonomy => {
    const TaxonomyComponent = taxonomy.hierarchical ? hierarchical_term_selector : flat_term_selector;
    return (0,external_React_.createElement)(external_wp_element_namespaceObject.Fragment, {
      key: `taxonomy-${taxonomy.slug}`
    }, taxonomyWrapper((0,external_React_.createElement)(TaxonomyComponent, {
      slug: taxonomy.slug
    }), taxonomy));
  });
}
/* harmony default export */ const post_taxonomies = (PostTaxonomies);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/check.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function PostTaxonomiesCheck({
  children
}) {
  const hasTaxonomies = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const postType = select(store_store).getCurrentPostType();
    const taxonomies = select(external_wp_coreData_namespaceObject.store).getTaxonomies({
      per_page: -1
    });
    return taxonomies?.some(taxonomy => taxonomy.types.includes(postType));
  }, []);
  if (!hasTaxonomies) {
    return null;
  }
  return children;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/panel.js

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function TaxonomyPanel({
  taxonomy,
  children
}) {
  const slug = taxonomy?.slug;
  const panelName = slug ? `taxonomy-panel-${slug}` : '';
  const {
    isEnabled,
    isOpened
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isEditorPanelEnabled,
      isEditorPanelOpened
    } = select(store_store);
    return {
      isEnabled: slug ? isEditorPanelEnabled(panelName) : false,
      isOpened: slug ? isEditorPanelOpened(panelName) : false
    };
  }, [panelName, slug]);
  const {
    toggleEditorPanelOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  if (!isEnabled) {
    return null;
  }
  const taxonomyMenuName = taxonomy?.labels?.menu_name;
  if (!taxonomyMenuName) {
    return null;
  }
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: taxonomyMenuName,
    opened: isOpened,
    onToggle: () => toggleEditorPanelOpened(panelName)
  }, children);
}
function panel_PostTaxonomies() {
  return (0,external_React_.createElement)(PostTaxonomiesCheck, null, (0,external_React_.createElement)(post_taxonomies, {
    taxonomyWrapper: (content, taxonomy) => {
      return (0,external_React_.createElement)(TaxonomyPanel, {
        taxonomy: taxonomy
      }, content);
    }
  }));
}
/* harmony default export */ const post_taxonomies_panel = (panel_PostTaxonomies);

// EXTERNAL MODULE: ./node_modules/react-autosize-textarea/lib/index.js
var lib = __webpack_require__(4132);
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-text-editor/index.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */

function PostTextEditor() {
  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(PostTextEditor);
  const {
    content,
    blocks,
    type,
    id
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedEntityRecord
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      getCurrentPostType,
      getCurrentPostId
    } = select(store_store);
    const _type = getCurrentPostType();
    const _id = getCurrentPostId();
    const editedRecord = getEditedEntityRecord('postType', _type, _id);
    return {
      content: editedRecord?.content,
      blocks: editedRecord?.blocks,
      type: _type,
      id: _id
    };
  }, []);
  const {
    editEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  // Replicates the logic found in getEditedPostContent().
  const value = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (content instanceof Function) {
      return content({
        blocks
      });
    } else if (blocks) {
      // If we have parsed blocks already, they should be our source of truth.
      // Parsing applies block deprecations and legacy block conversions that
      // unparsed content will not have.
      return (0,external_wp_blocks_namespaceObject.__unstableSerializeAndClean)(blocks);
    }
    return content;
  }, [content, blocks]);
  return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "label",
    htmlFor: `post-content-${instanceId}`
  }, (0,external_wp_i18n_namespaceObject.__)('Type text or HTML')), (0,external_React_.createElement)(lib/* default */.A, {
    autoComplete: "off",
    dir: "auto",
    value: value,
    onChange: event => {
      editEntityRecord('postType', type, id, {
        content: event.target.value,
        blocks: undefined,
        selection: undefined
      });
    },
    className: "editor-post-text-editor",
    id: `post-content-${instanceId}`,
    placeholder: (0,external_wp_i18n_namespaceObject.__)('Start writing with text or HTML')
  }));
}

;// CONCATENATED MODULE: external ["wp","dom"]
const external_wp_dom_namespaceObject = window["wp"]["dom"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-title/constants.js
const DEFAULT_CLASSNAMES = 'wp-block wp-block-post-title block-editor-block-list__block editor-post-title editor-post-title__input rich-text';
const REGEXP_NEWLINES = /[\r\n]+/g;

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-title/use-post-title-focus.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function usePostTitleFocus(forwardedRef) {
  const ref = (0,external_wp_element_namespaceObject.useRef)();
  const {
    isCleanNewPost
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isCleanNewPost: _isCleanNewPost
    } = select(store_store);
    return {
      isCleanNewPost: _isCleanNewPost()
    };
  }, []);
  (0,external_wp_element_namespaceObject.useImperativeHandle)(forwardedRef, () => ({
    focus: () => {
      ref?.current?.focus();
    }
  }));
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!ref.current) {
      return;
    }
    const {
      defaultView
    } = ref.current.ownerDocument;
    const {
      name,
      parent
    } = defaultView;
    const ownerDocument = name === 'editor-canvas' ? parent.document : defaultView.document;
    const {
      activeElement,
      body
    } = ownerDocument;

    // Only autofocus the title when the post is entirely empty. This should
    // only happen for a new post, which means we focus the title on new
    // post so the author can start typing right away, without needing to
    // click anything.
    if (isCleanNewPost && (!activeElement || body === activeElement)) {
      ref.current.focus();
    }
  }, [isCleanNewPost]);
  return {
    ref
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-title/use-post-title.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

function usePostTitle() {
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    title
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute
    } = select(store_store);
    return {
      title: getEditedPostAttribute('title')
    };
  }, []);
  function updateTitle(newTitle) {
    editPost({
      title: newTitle
    });
  }
  return {
    title,
    setTitle: updateTitle
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-title/index.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */











/**
 * Internal dependencies
 */





function PostTitle(_, forwardedRef) {
  const {
    placeholder,
    hasFixedToolbar
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute
    } = select(store_store);
    const {
      getSettings
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      titlePlaceholder,
      hasFixedToolbar: _hasFixedToolbar
    } = getSettings();
    return {
      title: getEditedPostAttribute('title'),
      placeholder: titlePlaceholder,
      hasFixedToolbar: _hasFixedToolbar
    };
  }, []);
  const [isSelected, setIsSelected] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    ref: focusRef
  } = usePostTitleFocus(forwardedRef);
  const {
    title,
    setTitle: onUpdate
  } = usePostTitle();
  const [selection, setSelection] = (0,external_wp_element_namespaceObject.useState)({});
  const {
    clearSelectedBlock,
    insertBlocks,
    insertDefaultBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  function onChange(value) {
    onUpdate(value.replace(REGEXP_NEWLINES, ' '));
  }
  function onInsertBlockAfter(blocks) {
    insertBlocks(blocks, 0);
  }
  function onSelect() {
    setIsSelected(true);
    clearSelectedBlock();
  }
  function onUnselect() {
    setIsSelected(false);
    setSelection({});
  }
  function onEnterPress() {
    insertDefaultBlock(undefined, undefined, 0);
  }
  function onKeyDown(event) {
    if (event.keyCode === external_wp_keycodes_namespaceObject.ENTER) {
      event.preventDefault();
      onEnterPress();
    }
  }
  function onPaste(event) {
    const clipboardData = event.clipboardData;
    let plainText = '';
    let html = '';

    // IE11 only supports `Text` as an argument for `getData` and will
    // otherwise throw an invalid argument error, so we try the standard
    // arguments first, then fallback to `Text` if they fail.
    try {
      plainText = clipboardData.getData('text/plain');
      html = clipboardData.getData('text/html');
    } catch (error1) {
      try {
        html = clipboardData.getData('Text');
      } catch (error2) {
        // Some browsers like UC Browser paste plain text by default and
        // don't support clipboardData at all, so allow default
        // behaviour.
        return;
      }
    }

    // Allows us to ask for this information when we get a report.
    window.console.log('Received HTML:\n\n', html);
    window.console.log('Received plain text:\n\n', plainText);
    const content = (0,external_wp_blocks_namespaceObject.pasteHandler)({
      HTML: html,
      plainText
    });
    event.preventDefault();
    if (!content.length) {
      return;
    }
    if (typeof content !== 'string') {
      const [firstBlock] = content;
      if (!title && (firstBlock.name === 'core/heading' || firstBlock.name === 'core/paragraph')) {
        // Strip HTML to avoid unwanted HTML being added to the title.
        // In the majority of cases it is assumed that HTML in the title
        // is undesirable.
        const contentNoHTML = (0,external_wp_dom_namespaceObject.__unstableStripHTML)(firstBlock.attributes.content);
        onUpdate(contentNoHTML);
        onInsertBlockAfter(content.slice(1));
      } else {
        onInsertBlockAfter(content);
      }
    } else {
      const value = {
        ...(0,external_wp_richText_namespaceObject.create)({
          html: title
        }),
        ...selection
      };

      // Strip HTML to avoid unwanted HTML being added to the title.
      // In the majority of cases it is assumed that HTML in the title
      // is undesirable.
      const contentNoHTML = (0,external_wp_dom_namespaceObject.__unstableStripHTML)(content);
      const newValue = (0,external_wp_richText_namespaceObject.insert)(value, (0,external_wp_richText_namespaceObject.create)({
        html: contentNoHTML
      }));
      onUpdate((0,external_wp_richText_namespaceObject.toHTMLString)({
        value: newValue
      }));
      setSelection({
        start: newValue.start,
        end: newValue.end
      });
    }
  }
  const decodedPlaceholder = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(placeholder) || (0,external_wp_i18n_namespaceObject.__)('Add title');
  const {
    ref: richTextRef
  } = (0,external_wp_richText_namespaceObject.__unstableUseRichText)({
    value: title,
    onChange,
    placeholder: decodedPlaceholder,
    selectionStart: selection.start,
    selectionEnd: selection.end,
    onSelectionChange(newStart, newEnd) {
      setSelection(sel => {
        const {
          start,
          end
        } = sel;
        if (start === newStart && end === newEnd) {
          return sel;
        }
        return {
          start: newStart,
          end: newEnd
        };
      });
    },
    __unstableDisableFormats: false
  });

  // The wp-block className is important for editor styles.
  // This same block is used in both the visual and the code editor.
  const className = classnames_default()(DEFAULT_CLASSNAMES, {
    'is-selected': isSelected,
    'has-fixed-toolbar': hasFixedToolbar
  });
  return /* eslint-disable jsx-a11y/heading-has-content, jsx-a11y/no-noninteractive-element-to-interactive-role */(
    (0,external_React_.createElement)(post_type_support_check, {
      supportKeys: "title"
    }, (0,external_React_.createElement)("h1", {
      ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([richTextRef, focusRef]),
      contentEditable: true,
      className: className,
      "aria-label": decodedPlaceholder,
      role: "textbox",
      "aria-multiline": "true",
      onFocus: onSelect,
      onBlur: onUnselect,
      onKeyDown: onKeyDown,
      onKeyPress: onUnselect,
      onPaste: onPaste
    }))
    /* eslint-enable jsx-a11y/heading-has-content, jsx-a11y/no-noninteractive-element-to-interactive-role */
  );
}
/* harmony default export */ const post_title = ((0,external_wp_element_namespaceObject.forwardRef)(PostTitle));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-title/post-title-raw.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



function PostTitleRaw(_, forwardedRef) {
  const {
    placeholder,
    hasFixedToolbar
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getSettings
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      titlePlaceholder,
      hasFixedToolbar: _hasFixedToolbar
    } = getSettings();
    return {
      placeholder: titlePlaceholder,
      hasFixedToolbar: _hasFixedToolbar
    };
  }, []);
  const [isSelected, setIsSelected] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    title,
    setTitle: onUpdate
  } = usePostTitle();
  const {
    ref: focusRef
  } = usePostTitleFocus(forwardedRef);
  function onChange(value) {
    onUpdate(value.replace(REGEXP_NEWLINES, ' '));
  }
  function onSelect() {
    setIsSelected(true);
  }
  function onUnselect() {
    setIsSelected(false);
  }

  // The wp-block className is important for editor styles.
  // This same block is used in both the visual and the code editor.
  const className = classnames_default()(DEFAULT_CLASSNAMES, {
    'is-selected': isSelected,
    'has-fixed-toolbar': hasFixedToolbar,
    'is-raw-text': true
  });
  const decodedPlaceholder = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(placeholder) || (0,external_wp_i18n_namespaceObject.__)('Add title');
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.TextareaControl, {
    ref: focusRef,
    value: title,
    onChange: onChange,
    onFocus: onSelect,
    onBlur: onUnselect,
    label: placeholder,
    className: className,
    placeholder: decodedPlaceholder,
    hideLabelFromVision: true,
    autoComplete: "off",
    dir: "auto",
    rows: 1,
    __nextHasNoMarginBottom: true
  });
}
/* harmony default export */ const post_title_raw = ((0,external_wp_element_namespaceObject.forwardRef)(PostTitleRaw));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-trash/index.js

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */

function PostTrash() {
  const {
    isNew,
    isDeleting,
    postId
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const store = select(store_store);
    return {
      isNew: store.isEditedPostNew(),
      isDeleting: store.isDeletingPost(),
      postId: store.getCurrentPostId()
    };
  }, []);
  const {
    trashPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const [showConfirmDialog, setShowConfirmDialog] = (0,external_wp_element_namespaceObject.useState)(false);
  if (isNew || !postId) {
    return null;
  }
  const handleConfirm = () => {
    setShowConfirmDialog(false);
    trashPost();
  };
  return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    __next40pxDefaultSize: true,
    className: "editor-post-trash",
    isDestructive: true,
    variant: "secondary",
    isBusy: isDeleting,
    "aria-disabled": isDeleting,
    onClick: isDeleting ? undefined : () => setShowConfirmDialog(true)
  }, (0,external_wp_i18n_namespaceObject.__)('Move to trash')), (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalConfirmDialog, {
    isOpen: showConfirmDialog,
    onConfirm: handleConfirm,
    onCancel: () => setShowConfirmDialog(false)
  }, (0,external_wp_i18n_namespaceObject.__)('Are you sure you want to move this post to the trash?')));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-trash/check.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function PostTrashCheck({
  isNew,
  postId,
  canUserDelete,
  children
}) {
  if (isNew || !postId || !canUserDelete) {
    return null;
  }
  return children;
}
/* harmony default export */ const post_trash_check = ((0,external_wp_data_namespaceObject.withSelect)(select => {
  const {
    isEditedPostNew,
    getCurrentPostId,
    getCurrentPostType
  } = select(store_store);
  const {
    getPostType,
    canUser
  } = select(external_wp_coreData_namespaceObject.store);
  const postId = getCurrentPostId();
  const postType = getPostType(getCurrentPostType());
  const resource = postType?.rest_base || ''; // eslint-disable-line camelcase

  return {
    isNew: isEditedPostNew(),
    postId,
    canUserDelete: postId && resource ? canUser('delete', resource, postId) : false
  };
})(PostTrashCheck));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-url/index.js

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */

function PostURL({
  onClose
}) {
  const {
    isEditable,
    postSlug,
    viewPostLabel,
    postLink,
    permalinkPrefix,
    permalinkSuffix
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _post$_links$wpActio;
    const post = select(store_store).getCurrentPost();
    const postTypeSlug = select(store_store).getCurrentPostType();
    const postType = select(external_wp_coreData_namespaceObject.store).getPostType(postTypeSlug);
    const permalinkParts = select(store_store).getPermalinkParts();
    const hasPublishAction = (_post$_links$wpActio = post?._links?.['wp:action-publish']) !== null && _post$_links$wpActio !== void 0 ? _post$_links$wpActio : false;
    return {
      isEditable: select(store_store).isPermalinkEditable() && hasPublishAction,
      postSlug: (0,external_wp_url_namespaceObject.safeDecodeURIComponent)(select(store_store).getEditedPostSlug()),
      viewPostLabel: postType?.labels.view_item,
      postLink: post.link,
      permalinkPrefix: permalinkParts?.prefix,
      permalinkSuffix: permalinkParts?.suffix
    };
  }, []);
  const {
    editPost
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const [forceEmptyField, setForceEmptyField] = (0,external_wp_element_namespaceObject.useState)(false);
  return (0,external_React_.createElement)("div", {
    className: "editor-post-url"
  }, (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.__experimentalInspectorPopoverHeader, {
    title: (0,external_wp_i18n_namespaceObject.__)('URL'),
    onClose: onClose
  }), isEditable && (0,external_React_.createElement)(external_wp_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Permalink'),
    value: forceEmptyField ? '' : postSlug,
    autoComplete: "off",
    spellCheck: "false",
    help: (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_wp_i18n_namespaceObject.__)('The last part of the URL.'), ' ', (0,external_React_.createElement)(external_wp_components_namespaceObject.ExternalLink, {
      href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/documentation/article/page-post-settings-sidebar/#permalink')
    }, (0,external_wp_i18n_namespaceObject.__)('Learn more.'))),
    onChange: newValue => {
      editPost({
        slug: newValue
      });
      // When we delete the field the permalink gets
      // reverted to the original value.
      // The forceEmptyField logic allows the user to have
      // the field temporarily empty while typing.
      if (!newValue) {
        if (!forceEmptyField) {
          setForceEmptyField(true);
        }
        return;
      }
      if (forceEmptyField) {
        setForceEmptyField(false);
      }
    },
    onBlur: event => {
      editPost({
        slug: (0,external_wp_url_namespaceObject.cleanForSlug)(event.target.value)
      });
      if (forceEmptyField) {
        setForceEmptyField(false);
      }
    }
  }), isEditable && (0,external_React_.createElement)("h3", {
    className: "editor-post-url__link-label"
  }, viewPostLabel !== null && viewPostLabel !== void 0 ? viewPostLabel : (0,external_wp_i18n_namespaceObject.__)('View post')), (0,external_React_.createElement)("p", null, (0,external_React_.createElement)(external_wp_components_namespaceObject.ExternalLink, {
    className: "editor-post-url__link",
    href: postLink,
    target: "_blank"
  }, isEditable ? (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)("span", {
    className: "editor-post-url__link-prefix"
  }, permalinkPrefix), (0,external_React_.createElement)("span", {
    className: "editor-post-url__link-slug"
  }, postSlug), (0,external_React_.createElement)("span", {
    className: "editor-post-url__link-suffix"
  }, permalinkSuffix)) : postLink)));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-url/check.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function PostURLCheck({
  children
}) {
  const isVisible = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const postTypeSlug = select(store_store).getCurrentPostType();
    const postType = select(external_wp_coreData_namespaceObject.store).getPostType(postTypeSlug);
    if (!postType?.viewable) {
      return false;
    }
    const post = select(store_store).getCurrentPost();
    if (!post.link) {
      return false;
    }
    const permalinkParts = select(store_store).getPermalinkParts();
    if (!permalinkParts) {
      return false;
    }
    return true;
  }, []);
  if (!isVisible) {
    return null;
  }
  return children;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-url/label.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function PostURLLabel() {
  return usePostURLLabel();
}
function usePostURLLabel() {
  const postLink = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getPermalink(), []);
  return (0,external_wp_url_namespaceObject.filterURLForDisplay)((0,external_wp_url_namespaceObject.safeDecodeURIComponent)(postLink));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-url/panel.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




function PostURLPanel() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null);
  // Memoize popoverProps to avoid returning a new object every time.
  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    anchor: popoverAnchor,
    placement: 'bottom-end'
  }), [popoverAnchor]);
  return (0,external_React_.createElement)(PostURLCheck, null, (0,external_React_.createElement)(post_panel_row, {
    label: (0,external_wp_i18n_namespaceObject.__)('URL'),
    ref: setPopoverAnchor
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    className: "editor-post-url__panel-dropdown",
    contentClassName: "editor-post-url__panel-dialog",
    focusOnMount: true,
    renderToggle: ({
      isOpen,
      onToggle
    }) => (0,external_React_.createElement)(PostURLToggle, {
      isOpen: isOpen,
      onClick: onToggle
    }),
    renderContent: ({
      onClose
    }) => (0,external_React_.createElement)(PostURL, {
      onClose: onClose
    })
  })));
}
function PostURLToggle({
  isOpen,
  onClick
}) {
  const label = usePostURLLabel();
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    __next40pxDefaultSize: true,
    className: "editor-post-url__panel-toggle",
    variant: "tertiary",
    "aria-expanded": isOpen
    // translators: %s: Current post URL.
    ,
    "aria-label": (0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('Change URL: %s'), label),
    onClick: onClick
  }, label);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/check.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

function PostVisibilityCheck({
  render
}) {
  const canEdit = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getCurrentPos;
    return (_select$getCurrentPos = select(store_store).getCurrentPost()._links?.['wp:action-publish']) !== null && _select$getCurrentPos !== void 0 ? _select$getCurrentPos : false;
  });
  return render({
    canEdit
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/info.js

/**
 * WordPress dependencies
 */

const info = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"
}));
/* harmony default export */ const library_info = (info);

;// CONCATENATED MODULE: external ["wp","wordcount"]
const external_wp_wordcount_namespaceObject = window["wp"]["wordcount"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/word-count/index.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function WordCount() {
  const content = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getEditedPostAttribute('content'), []);

  /*
   * translators: If your word count is based on single characters (e.g. East Asian characters),
   * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
   * Do not translate into your own language.
   */
  const wordCountType = (0,external_wp_i18n_namespaceObject._x)('words', 'Word count type. Do not translate!');
  return (0,external_React_.createElement)("span", {
    className: "word-count"
  }, (0,external_wp_wordcount_namespaceObject.count)(content, wordCountType));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/time-to-read/index.js

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Average reading rate - based on average taken from
 * https://irisreading.com/average-reading-speed-in-various-languages/
 * (Characters/minute used for Chinese rather than words).
 *
 * @type {number} A rough estimate of the average reading rate across multiple languages.
 */
const AVERAGE_READING_RATE = 189;
function TimeToRead() {
  const content = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getEditedPostAttribute('content'), []);

  /*
   * translators: If your word count is based on single characters (e.g. East Asian characters),
   * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
   * Do not translate into your own language.
   */
  const wordCountType = (0,external_wp_i18n_namespaceObject._x)('words', 'Word count type. Do not translate!');
  const minutesToRead = Math.round((0,external_wp_wordcount_namespaceObject.count)(content, wordCountType) / AVERAGE_READING_RATE);
  const minutesToReadString = minutesToRead === 0 ? (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.__)('<span>< 1</span> minute'), {
    span: (0,external_React_.createElement)("span", null)
  }) : (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s is the number of minutes the post will take to read. */
  (0,external_wp_i18n_namespaceObject._n)('<span>%d</span> minute', '<span>%d</span> minutes', minutesToRead), minutesToRead), {
    span: (0,external_React_.createElement)("span", null)
  });
  return (0,external_React_.createElement)("span", {
    className: "time-to-read"
  }, minutesToReadString);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/character-count/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function CharacterCount() {
  const content = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getEditedPostAttribute('content'), []);
  return (0,external_wp_wordcount_namespaceObject.count)(content, 'characters_including_spaces');
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/table-of-contents/panel.js

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




function TableOfContentsPanel({
  hasOutlineItemsDisabled,
  onRequestClose
}) {
  const {
    headingCount,
    paragraphCount,
    numberOfBlocks
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getGlobalBlockCount
    } = select(external_wp_blockEditor_namespaceObject.store);
    return {
      headingCount: getGlobalBlockCount('core/heading'),
      paragraphCount: getGlobalBlockCount('core/paragraph'),
      numberOfBlocks: getGlobalBlockCount()
    };
  }, []);
  return (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */
    /* eslint-disable jsx-a11y/no-redundant-roles */
    (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)("div", {
      className: "table-of-contents__wrapper",
      role: "note",
      "aria-label": (0,external_wp_i18n_namespaceObject.__)('Document Statistics'),
      tabIndex: "0"
    }, (0,external_React_.createElement)("ul", {
      role: "list",
      className: "table-of-contents__counts"
    }, (0,external_React_.createElement)("li", {
      className: "table-of-contents__count"
    }, (0,external_wp_i18n_namespaceObject.__)('Words'), (0,external_React_.createElement)(WordCount, null)), (0,external_React_.createElement)("li", {
      className: "table-of-contents__count"
    }, (0,external_wp_i18n_namespaceObject.__)('Characters'), (0,external_React_.createElement)("span", {
      className: "table-of-contents__number"
    }, (0,external_React_.createElement)(CharacterCount, null))), (0,external_React_.createElement)("li", {
      className: "table-of-contents__count"
    }, (0,external_wp_i18n_namespaceObject.__)('Time to read'), (0,external_React_.createElement)(TimeToRead, null)), (0,external_React_.createElement)("li", {
      className: "table-of-contents__count"
    }, (0,external_wp_i18n_namespaceObject.__)('Headings'), (0,external_React_.createElement)("span", {
      className: "table-of-contents__number"
    }, headingCount)), (0,external_React_.createElement)("li", {
      className: "table-of-contents__count"
    }, (0,external_wp_i18n_namespaceObject.__)('Paragraphs'), (0,external_React_.createElement)("span", {
      className: "table-of-contents__number"
    }, paragraphCount)), (0,external_React_.createElement)("li", {
      className: "table-of-contents__count"
    }, (0,external_wp_i18n_namespaceObject.__)('Blocks'), (0,external_React_.createElement)("span", {
      className: "table-of-contents__number"
    }, numberOfBlocks)))), headingCount > 0 && (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)("hr", null), (0,external_React_.createElement)("h2", {
      className: "table-of-contents__title"
    }, (0,external_wp_i18n_namespaceObject.__)('Document Outline')), (0,external_React_.createElement)(document_outline, {
      onSelect: onRequestClose,
      hasOutlineItemsDisabled: hasOutlineItemsDisabled
    })))
    /* eslint-enable jsx-a11y/no-redundant-roles */
  );
}
/* harmony default export */ const table_of_contents_panel = (TableOfContentsPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/table-of-contents/index.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */

function TableOfContents({
  hasOutlineItemsDisabled,
  repositionDropdown,
  ...props
}, ref) {
  const hasBlocks = (0,external_wp_data_namespaceObject.useSelect)(select => !!select(external_wp_blockEditor_namespaceObject.store).getBlockCount(), []);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: {
      placement: repositionDropdown ? 'right' : 'bottom'
    },
    className: "table-of-contents",
    contentClassName: "table-of-contents__popover",
    renderToggle: ({
      isOpen,
      onToggle
    }) => (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      ...props,
      ref: ref,
      onClick: hasBlocks ? onToggle : undefined,
      icon: library_info,
      "aria-expanded": isOpen,
      "aria-haspopup": "true"
      /* translators: button label text should, if possible, be under 16 characters. */,
      label: (0,external_wp_i18n_namespaceObject.__)('Details'),
      tooltipPosition: "bottom",
      "aria-disabled": !hasBlocks
    }),
    renderContent: ({
      onClose
    }) => (0,external_React_.createElement)(table_of_contents_panel, {
      onRequestClose: onClose,
      hasOutlineItemsDisabled: hasOutlineItemsDisabled
    })
  });
}
/* harmony default export */ const table_of_contents = ((0,external_wp_element_namespaceObject.forwardRef)(TableOfContents));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/unsaved-changes-warning/index.js
/**
 * WordPress dependencies
 */





/**
 * Warns the user if there are unsaved changes before leaving the editor.
 * Compatible with Post Editor and Site Editor.
 *
 * @return {Component} The component.
 */
function UnsavedChangesWarning() {
  const {
    __experimentalGetDirtyEntityRecords
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_coreData_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    /**
     * Warns the user if there are unsaved changes before leaving the editor.
     *
     * @param {Event} event `beforeunload` event.
     *
     * @return {string | undefined} Warning prompt message, if unsaved changes exist.
     */
    const warnIfUnsavedChanges = event => {
      // We need to call the selector directly in the listener to avoid race
      // conditions with `BrowserURL` where `componentDidUpdate` gets the
      // new value of `isEditedPostDirty` before this component does,
      // causing this component to incorrectly think a trashed post is still dirty.
      const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();
      if (dirtyEntityRecords.length > 0) {
        event.returnValue = (0,external_wp_i18n_namespaceObject.__)('You have unsaved changes. If you proceed, they will be lost.');
        return event.returnValue;
      }
    };
    window.addEventListener('beforeunload', warnIfUnsavedChanges);
    return () => {
      window.removeEventListener('beforeunload', warnIfUnsavedChanges);
    };
  }, [__experimentalGetDirtyEntityRecords]);
  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/with-registry-provider.js

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */

const withRegistryProvider = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(WrappedComponent => (0,external_wp_data_namespaceObject.withRegistry)(props => {
  const {
    useSubRegistry = true,
    registry,
    ...additionalProps
  } = props;
  if (!useSubRegistry) {
    return (0,external_React_.createElement)(WrappedComponent, {
      ...additionalProps
    });
  }
  const [subRegistry, setSubRegistry] = (0,external_wp_element_namespaceObject.useState)(null);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const newRegistry = (0,external_wp_data_namespaceObject.createRegistry)({
      'core/block-editor': external_wp_blockEditor_namespaceObject.storeConfig
    }, registry);
    newRegistry.registerStore('core/editor', storeConfig);
    setSubRegistry(newRegistry);
  }, [registry]);
  if (!subRegistry) {
    return null;
  }
  return (0,external_React_.createElement)(external_wp_data_namespaceObject.RegistryProvider, {
    value: subRegistry
  }, (0,external_React_.createElement)(WrappedComponent, {
    ...additionalProps
  }));
}), 'withRegistryProvider');
/* harmony default export */ const with_registry_provider = (withRegistryProvider);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/media-categories/index.js
/**
 * The `editor` settings here need to be in sync with the corresponding ones in `editor` package.
 * See `packages/editor/src/components/media-categories/index.js`.
 *
 * In the future we could consider creating an Openvese package that can be used in both `editor` and `site-editor`.
 * The rest of the settings would still need to be in sync though.
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/** @typedef {import('@wordpress/block-editor').InserterMediaRequest} InserterMediaRequest */
/** @typedef {import('@wordpress/block-editor').InserterMediaItem} InserterMediaItem */
/** @typedef {import('@wordpress/block-editor').InserterMediaCategory} InserterMediaCategory */

const getExternalLink = (url, text) => `<a ${getExternalLinkAttributes(url)}>${text}</a>`;
const getExternalLinkAttributes = url => `href="${url}" target="_blank" rel="noreferrer noopener"`;
const getOpenverseLicense = (license, licenseVersion) => {
  let licenseName = license.trim();
  // PDM has no abbreviation
  if (license !== 'pdm') {
    licenseName = license.toUpperCase().replace('SAMPLING', 'Sampling');
  }
  // If version is known, append version to the name.
  // The license has to have a version to be valid. Only
  // PDM (public domain mark) doesn't have a version.
  if (licenseVersion) {
    licenseName += ` ${licenseVersion}`;
  }
  // For licenses other than public-domain marks, prepend 'CC' to the name.
  if (!['pdm', 'cc0'].includes(license)) {
    licenseName = `CC ${licenseName}`;
  }
  return licenseName;
};
const getOpenverseCaption = item => {
  const {
    title,
    foreign_landing_url: foreignLandingUrl,
    creator,
    creator_url: creatorUrl,
    license,
    license_version: licenseVersion,
    license_url: licenseUrl
  } = item;
  const fullLicense = getOpenverseLicense(license, licenseVersion);
  const _creator = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(creator);
  let _caption;
  if (_creator) {
    _caption = title ? (0,external_wp_i18n_namespaceObject.sprintf)(
    // translators: %1s: Title of a media work from Openverse; %2s: Name of the work's creator; %3s: Work's licence e.g: "CC0 1.0".
    (0,external_wp_i18n_namespaceObject._x)('"%1$s" by %2$s/ %3$s', 'caption'), getExternalLink(foreignLandingUrl, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title)), creatorUrl ? getExternalLink(creatorUrl, _creator) : _creator, licenseUrl ? getExternalLink(`${licenseUrl}?ref=openverse`, fullLicense) : fullLicense) : (0,external_wp_i18n_namespaceObject.sprintf)(
    // translators: %1s: Link attributes for a given Openverse media work; %2s: Name of the work's creator; %3s: Works's licence e.g: "CC0 1.0".
    (0,external_wp_i18n_namespaceObject._x)('<a %1$s>Work</a> by %2$s/ %3$s', 'caption'), getExternalLinkAttributes(foreignLandingUrl), creatorUrl ? getExternalLink(creatorUrl, _creator) : _creator, licenseUrl ? getExternalLink(`${licenseUrl}?ref=openverse`, fullLicense) : fullLicense);
  } else {
    _caption = title ? (0,external_wp_i18n_namespaceObject.sprintf)(
    // translators: %1s: Title of a media work from Openverse; %2s: Work's licence e.g: "CC0 1.0".
    (0,external_wp_i18n_namespaceObject._x)('"%1$s"/ %2$s', 'caption'), getExternalLink(foreignLandingUrl, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title)), licenseUrl ? getExternalLink(`${licenseUrl}?ref=openverse`, fullLicense) : fullLicense) : (0,external_wp_i18n_namespaceObject.sprintf)(
    // translators: %1s: Link attributes for a given Openverse media work; %2s: Works's licence e.g: "CC0 1.0".
    (0,external_wp_i18n_namespaceObject._x)('<a %1$s>Work</a>/ %2$s', 'caption'), getExternalLinkAttributes(foreignLandingUrl), licenseUrl ? getExternalLink(`${licenseUrl}?ref=openverse`, fullLicense) : fullLicense);
  }
  return _caption.replace(/\s{2}/g, ' ');
};
const coreMediaFetch = async (query = {}) => {
  const mediaItems = await (0,external_wp_data_namespaceObject.resolveSelect)(external_wp_coreData_namespaceObject.store).getMediaItems({
    ...query,
    orderBy: !!query?.search ? 'relevance' : 'date'
  });
  return mediaItems.map(mediaItem => ({
    ...mediaItem,
    alt: mediaItem.alt_text,
    url: mediaItem.source_url,
    previewUrl: mediaItem.media_details?.sizes?.medium?.source_url,
    caption: mediaItem.caption?.raw
  }));
};

/** @type {InserterMediaCategory[]} */
const inserterMediaCategories = [{
  name: 'images',
  labels: {
    name: (0,external_wp_i18n_namespaceObject.__)('Images'),
    search_items: (0,external_wp_i18n_namespaceObject.__)('Search images')
  },
  mediaType: 'image',
  async fetch(query = {}) {
    return coreMediaFetch({
      ...query,
      media_type: 'image'
    });
  }
}, {
  name: 'videos',
  labels: {
    name: (0,external_wp_i18n_namespaceObject.__)('Videos'),
    search_items: (0,external_wp_i18n_namespaceObject.__)('Search videos')
  },
  mediaType: 'video',
  async fetch(query = {}) {
    return coreMediaFetch({
      ...query,
      media_type: 'video'
    });
  }
}, {
  name: 'audio',
  labels: {
    name: (0,external_wp_i18n_namespaceObject.__)('Audio'),
    search_items: (0,external_wp_i18n_namespaceObject.__)('Search audio')
  },
  mediaType: 'audio',
  async fetch(query = {}) {
    return coreMediaFetch({
      ...query,
      media_type: 'audio'
    });
  }
}, {
  name: 'openverse',
  labels: {
    name: (0,external_wp_i18n_namespaceObject.__)('Openverse'),
    search_items: (0,external_wp_i18n_namespaceObject.__)('Search Openverse')
  },
  mediaType: 'image',
  async fetch(query = {}) {
    const defaultArgs = {
      mature: false,
      excluded_source: 'flickr,inaturalist,wikimedia',
      license: 'pdm,cc0'
    };
    const finalQuery = {
      ...query,
      ...defaultArgs
    };
    const mapFromInserterMediaRequest = {
      per_page: 'page_size',
      search: 'q'
    };
    const url = new URL('https://api.openverse.engineering/v1/images/');
    Object.entries(finalQuery).forEach(([key, value]) => {
      const queryKey = mapFromInserterMediaRequest[key] || key;
      url.searchParams.set(queryKey, value);
    });
    const response = await window.fetch(url, {
      headers: {
        'User-Agent': 'WordPress/inserter-media-fetch'
      }
    });
    const jsonResponse = await response.json();
    const results = jsonResponse.results;
    return results.map(result => ({
      ...result,
      // This is a temp solution for better titles, until Openverse API
      // completes the cleaning up of some titles of their upstream data.
      title: result.title?.toLowerCase().startsWith('file:') ? result.title.slice(5) : result.title,
      sourceId: result.id,
      id: undefined,
      caption: getOpenverseCaption(result),
      previewUrl: result.thumbnail
    }));
  },
  getReportUrl: ({
    sourceId
  }) => `https://wordpress.org/openverse/image/${sourceId}/report/`,
  isExternalResource: true
}];
/* harmony default export */ const media_categories = (inserterMediaCategories);

;// CONCATENATED MODULE: external ["wp","mediaUtils"]
const external_wp_mediaUtils_namespaceObject = window["wp"]["mediaUtils"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/media-upload/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

const media_upload_noop = () => {};

/**
 * Upload a media file when the file upload button is activated.
 * Wrapper around mediaUpload() that injects the current post ID.
 *
 * @param {Object}   $0                   Parameters object passed to the function.
 * @param {?Object}  $0.additionalData    Additional data to include in the request.
 * @param {string}   $0.allowedTypes      Array with the types of media that can be uploaded, if unset all types are allowed.
 * @param {Array}    $0.filesList         List of files.
 * @param {?number}  $0.maxUploadFileSize Maximum upload size in bytes allowed for the site.
 * @param {Function} $0.onError           Function called when an error happens.
 * @param {Function} $0.onFileChange      Function called each time a file or a temporary representation of the file is available.
 */
function mediaUpload({
  additionalData = {},
  allowedTypes,
  filesList,
  maxUploadFileSize,
  onError = media_upload_noop,
  onFileChange
}) {
  const {
    getCurrentPost,
    getEditorSettings
  } = (0,external_wp_data_namespaceObject.select)(store_store);
  const wpAllowedMimeTypes = getEditorSettings().allowedMimeTypes;
  maxUploadFileSize = maxUploadFileSize || getEditorSettings().maxUploadFileSize;
  const currentPost = getCurrentPost();
  // Templates and template parts' numerical ID is stored in `wp_id`.
  const currentPostId = typeof currentPost?.id === 'number' ? currentPost.id : currentPost?.wp_id;
  const postData = currentPostId ? {
    post: currentPostId
  } : {};
  (0,external_wp_mediaUtils_namespaceObject.uploadMedia)({
    allowedTypes,
    filesList,
    onFileChange,
    additionalData: {
      ...postData,
      ...additionalData
    },
    maxUploadFileSize,
    onError: ({
      message
    }) => onError(message),
    wpAllowedMimeTypes
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/use-block-editor-settings.js
/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */




const EMPTY_BLOCKS_LIST = [];
const BLOCK_EDITOR_SETTINGS = ['__experimentalBlockDirectory', '__experimentalDiscussionSettings', '__experimentalFeatures', '__experimentalGlobalStylesBaseStyles', '__experimentalPreferredStyleVariations', '__unstableGalleryWithImageBlocks', 'alignWide', 'blockInspectorTabs', 'allowedMimeTypes', 'bodyPlaceholder', 'canLockBlocks', 'capabilities', 'clearBlockSelection', 'codeEditingEnabled', 'colors', 'disableCustomColors', 'disableCustomFontSizes', 'disableCustomSpacingSizes', 'disableCustomGradients', 'disableLayoutStyles', 'enableCustomLineHeight', 'enableCustomSpacing', 'enableCustomUnits', 'enableOpenverseMediaCategory', 'fontSizes', 'gradients', 'generateAnchors', 'onNavigateToEntityRecord', 'hasInlineToolbar', 'imageDefaultSize', 'imageDimensions', 'imageEditing', 'imageSizes', 'isRTL', 'locale', 'maxWidth', 'onUpdateDefaultBlockStyles', 'postContentAttributes', 'postsPerPage', 'readOnly', 'styles', 'titlePlaceholder', 'supportsLayout', 'widgetTypesToHideFromLegacyWidgetBlock', '__unstableHasCustomAppender', '__unstableIsPreviewMode', '__unstableResolvedAssets', '__unstableIsBlockBasedTheme', '__experimentalArchiveTitleTypeLabel', '__experimentalArchiveTitleNameLabel'];

/**
 * React hook used to compute the block editor settings to use for the post editor.
 *
 * @param {Object} settings EditorProvider settings prop.
 * @param {string} postType Editor root level post type.
 * @param {string} postId   Editor root level post ID.
 *
 * @return {Object} Block Editor Settings.
 */
function useBlockEditorSettings(settings, postType, postId) {
  var _settings$__experimen, _settings$__experimen2;
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');
  const {
    allowRightClickOverrides,
    blockTypes,
    focusMode,
    hasFixedToolbar,
    isDistractionFree,
    keepCaretInsideBlock,
    reusableBlocks,
    hasUploadPermissions,
    hiddenBlockTypes,
    canUseUnfilteredHTML,
    userCanCreatePages,
    pageOnFront,
    pageForPosts,
    userPatternCategories,
    restBlockPatternCategories
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _canUser;
    const isWeb = external_wp_element_namespaceObject.Platform.OS === 'web';
    const {
      canUser,
      getRawEntityRecord,
      getEntityRecord,
      getUserPatternCategories,
      getEntityRecords,
      getBlockPatternCategories
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    const {
      getBlockTypes
    } = select(external_wp_blocks_namespaceObject.store);
    const siteSettings = canUser('read', 'settings') ? getEntityRecord('root', 'site') : undefined;
    return {
      allowRightClickOverrides: get('core', 'allowRightClickOverrides'),
      blockTypes: getBlockTypes(),
      canUseUnfilteredHTML: getRawEntityRecord('postType', postType, postId)?._links?.hasOwnProperty('wp:action-unfiltered-html'),
      focusMode: get('core', 'focusMode'),
      hasFixedToolbar: get('core', 'fixedToolbar') || !isLargeViewport,
      hiddenBlockTypes: get('core', 'hiddenBlockTypes'),
      isDistractionFree: get('core', 'distractionFree'),
      keepCaretInsideBlock: get('core', 'keepCaretInsideBlock'),
      reusableBlocks: isWeb ? getEntityRecords('postType', 'wp_block', {
        per_page: -1
      }) : EMPTY_BLOCKS_LIST,
      // Reusable blocks are fetched in the native version of this hook.
      hasUploadPermissions: (_canUser = canUser('create', 'media')) !== null && _canUser !== void 0 ? _canUser : true,
      userCanCreatePages: canUser('create', 'pages'),
      pageOnFront: siteSettings?.page_on_front,
      pageForPosts: siteSettings?.page_for_posts,
      userPatternCategories: getUserPatternCategories(),
      restBlockPatternCategories: getBlockPatternCategories()
    };
  }, [postType, postId, isLargeViewport]);
  const settingsBlockPatterns = (_settings$__experimen = settings.__experimentalAdditionalBlockPatterns) !== null && _settings$__experimen !== void 0 ? _settings$__experimen :
  // WP 6.0
  settings.__experimentalBlockPatterns; // WP 5.9
  const settingsBlockPatternCategories = (_settings$__experimen2 = settings.__experimentalAdditionalBlockPatternCategories) !== null && _settings$__experimen2 !== void 0 ? _settings$__experimen2 :
  // WP 6.0
  settings.__experimentalBlockPatternCategories; // WP 5.9

  const blockPatterns = (0,external_wp_element_namespaceObject.useMemo)(() => [...(settingsBlockPatterns || [])].filter(({
    postTypes
  }) => {
    return !postTypes || Array.isArray(postTypes) && postTypes.includes(postType);
  }), [settingsBlockPatterns, postType]);
  const blockPatternCategories = (0,external_wp_element_namespaceObject.useMemo)(() => [...(settingsBlockPatternCategories || []), ...(restBlockPatternCategories || [])].filter((x, index, arr) => index === arr.findIndex(y => x.name === y.name)), [settingsBlockPatternCategories, restBlockPatternCategories]);
  const {
    undo,
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    saveEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);

  /**
   * Creates a Post entity.
   * This is utilised by the Link UI to allow for on-the-fly creation of Posts/Pages.
   *
   * @param {Object} options parameters for the post being created. These mirror those used on 3rd param of saveEntityRecord.
   * @return {Object} the post type object that was created.
   */
  const createPageEntity = (0,external_wp_element_namespaceObject.useCallback)(options => {
    if (!userCanCreatePages) {
      return Promise.reject({
        message: (0,external_wp_i18n_namespaceObject.__)('You do not have permission to create Pages.')
      });
    }
    return saveEntityRecord('postType', 'page', options);
  }, [saveEntityRecord, userCanCreatePages]);
  const allowedBlockTypes = (0,external_wp_element_namespaceObject.useMemo)(() => {
    // Omit hidden block types if exists and non-empty.
    if (hiddenBlockTypes && hiddenBlockTypes.length > 0) {
      // Defer to passed setting for `allowedBlockTypes` if provided as
      // anything other than `true` (where `true` is equivalent to allow
      // all block types).
      const defaultAllowedBlockTypes = true === settings.allowedBlockTypes ? blockTypes.map(({
        name
      }) => name) : settings.allowedBlockTypes || [];
      return defaultAllowedBlockTypes.filter(type => !hiddenBlockTypes.includes(type));
    }
    return settings.allowedBlockTypes;
  }, [settings.allowedBlockTypes, hiddenBlockTypes, blockTypes]);
  const forceDisableFocusMode = settings.focusMode === false;
  return (0,external_wp_element_namespaceObject.useMemo)(() => ({
    ...Object.fromEntries(Object.entries(settings).filter(([key]) => BLOCK_EDITOR_SETTINGS.includes(key))),
    allowedBlockTypes,
    allowRightClickOverrides,
    focusMode: focusMode && !forceDisableFocusMode,
    hasFixedToolbar,
    isDistractionFree,
    keepCaretInsideBlock,
    mediaUpload: hasUploadPermissions ? mediaUpload : undefined,
    __experimentalBlockPatterns: blockPatterns,
    [unlock(external_wp_blockEditor_namespaceObject.privateApis).selectBlockPatternsKey]: select => unlock(select(external_wp_coreData_namespaceObject.store)).getBlockPatternsForPostType(postType),
    __experimentalReusableBlocks: reusableBlocks,
    __experimentalBlockPatternCategories: blockPatternCategories,
    __experimentalUserPatternCategories: userPatternCategories,
    __experimentalFetchLinkSuggestions: (search, searchOptions) => (0,external_wp_coreData_namespaceObject.__experimentalFetchLinkSuggestions)(search, searchOptions, settings),
    inserterMediaCategories: media_categories,
    __experimentalFetchRichUrlData: external_wp_coreData_namespaceObject.__experimentalFetchUrlData,
    // Todo: This only checks the top level post, not the post within a template or any other entity that can be edited.
    // This might be better as a generic "canUser" selector.
    __experimentalCanUserUseUnfilteredHTML: canUseUnfilteredHTML,
    //Todo: this is only needed for native and should probably be removed.
    __experimentalUndo: undo,
    // Check whether we want all site editor frames to have outlines
    // including the navigation / pattern / parts editors.
    outlineMode: postType === 'wp_template',
    // Check these two properties: they were not present in the site editor.
    __experimentalCreatePageEntity: createPageEntity,
    __experimentalUserCanCreatePages: userCanCreatePages,
    pageOnFront,
    pageForPosts,
    __experimentalPreferPatternsOnRoot: postType === 'wp_template',
    templateLock: postType === 'wp_navigation' ? 'insert' : settings.templateLock,
    template: postType === 'wp_navigation' ? [['core/navigation', {}, []]] : settings.template,
    __experimentalSetIsInserterOpened: setIsInserterOpened
  }), [allowedBlockTypes, allowRightClickOverrides, focusMode, forceDisableFocusMode, hasFixedToolbar, isDistractionFree, keepCaretInsideBlock, settings, hasUploadPermissions, reusableBlocks, userPatternCategories, blockPatterns, blockPatternCategories, canUseUnfilteredHTML, undo, createPageEntity, userCanCreatePages, pageOnFront, pageForPosts, postType, setIsInserterOpened]);
}
/* harmony default export */ const use_block_editor_settings = (useBlockEditorSettings);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/disable-non-page-content-blocks.js
/**
 * WordPress dependencies
 */



const PAGE_CONTENT_BLOCKS = ['core/post-title', 'core/post-featured-image', 'core/post-content'];
function useDisableNonPageContentBlocks() {
  const contentIds = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getBlocksByName,
      getBlockParents,
      getBlockName
    } = select(external_wp_blockEditor_namespaceObject.store);
    return getBlocksByName(PAGE_CONTENT_BLOCKS).filter(clientId => getBlockParents(clientId).every(parentClientId => {
      const parentBlockName = getBlockName(parentClientId);
      return parentBlockName !== 'core/query' && !PAGE_CONTENT_BLOCKS.includes(parentBlockName);
    }));
  }, []);
  const {
    setBlockEditingMode,
    unsetBlockEditingMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    setBlockEditingMode('', 'disabled'); // Disable editing at the root level.

    for (const contentId of contentIds) {
      setBlockEditingMode(contentId, 'contentOnly'); // Re-enable each content block.
    }
    return () => {
      unsetBlockEditingMode('');
      for (const contentId of contentIds) {
        unsetBlockEditingMode(contentId);
      }
    };
  }, [contentIds, setBlockEditingMode, unsetBlockEditingMode]);
}

/**
 * Component that when rendered, makes it so that the site editor allows only
 * page content to be edited.
 */
function DisableNonPageContentBlocks() {
  useDisableNonPageContentBlocks();
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/navigation-block-editing-mode.js
/**
 * WordPress dependencies
 */




/**
 * For the Navigation block editor, we need to force the block editor to contentOnly for that block.
 *
 * Set block editing mode to contentOnly when entering Navigation focus mode.
 * this ensures that non-content controls on the block will be hidden and thus
 * the user can focus on editing the Navigation Menu content only.
 */

function NavigationBlockEditingMode() {
  // In the navigation block editor,
  // the navigation block is the only root block.
  const blockClientId = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).getBlockOrder()?.[0], []);
  const {
    setBlockEditingMode,
    unsetBlockEditingMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!blockClientId) {
      return;
    }
    setBlockEditingMode(blockClientId, 'contentOnly');
    return () => {
      unsetBlockEditingMode(blockClientId);
    };
  }, [blockClientId, unsetBlockEditingMode, setBlockEditingMode]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/index.js

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */






const {
  ExperimentalBlockEditorProvider
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const {
  PatternsMenuItems
} = unlock(external_wp_patterns_namespaceObject.privateApis);
const provider_noop = () => {};

/**
 * These are global entities that are only there to split blocks into logical units
 * They don't provide a "context" for the current post/page being rendered.
 * So we should not use their ids as post context. This is important to allow post blocks
 * (post content, post title) to be used within them without issues.
 */
const NON_CONTEXTUAL_POST_TYPES = ['wp_block', 'wp_template', 'wp_navigation', 'wp_template_part'];

/**
 * Depending on the post, template and template mode,
 * returns the appropriate blocks and change handlers for the block editor provider.
 *
 * @param {Array}   post     Block list.
 * @param {boolean} template Whether the page content has focus (and the surrounding template is inert). If `true` return page content blocks. Default `false`.
 * @param {string}  mode     Rendering mode.
 * @return {Array} Block editor props.
 */
function useBlockEditorProps(post, template, mode) {
  const rootLevelPost = mode === 'post-only' || !template ? 'post' : 'template';
  const [postBlocks, onInput, onChange] = (0,external_wp_coreData_namespaceObject.useEntityBlockEditor)('postType', post.type, {
    id: post.id
  });
  const [templateBlocks, onInputTemplate, onChangeTemplate] = (0,external_wp_coreData_namespaceObject.useEntityBlockEditor)('postType', template?.type, {
    id: template?.id
  });
  const maybeNavigationBlocks = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (post.type === 'wp_navigation') {
      return [(0,external_wp_blocks_namespaceObject.createBlock)('core/navigation', {
        ref: post.id,
        // As the parent editor is locked with `templateLock`, the template locking
        // must be explicitly "unset" on the block itself to allow the user to modify
        // the block's content.
        templateLock: false
      })];
    }
  }, [post.type, post.id]);

  // It is important that we don't create a new instance of blocks on every change
  // We should only create a new instance if the blocks them selves change, not a dependency of them.
  const blocks = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (maybeNavigationBlocks) {
      return maybeNavigationBlocks;
    }
    if (rootLevelPost === 'template') {
      return templateBlocks;
    }
    return postBlocks;
  }, [maybeNavigationBlocks, rootLevelPost, templateBlocks, postBlocks]);

  // Handle fallback to postBlocks outside of the above useMemo, to ensure
  // that constructed block templates that call `createBlock` are not generated
  // too frequently. This ensures that clientIds are stable.
  const disableRootLevelChanges = !!template && mode === 'template-locked' || post.type === 'wp_navigation';
  if (disableRootLevelChanges) {
    return [blocks, provider_noop, provider_noop];
  }
  return [blocks, rootLevelPost === 'post' ? onInput : onInputTemplate, rootLevelPost === 'post' ? onChange : onChangeTemplate];
}
const ExperimentalEditorProvider = with_registry_provider(({
  post,
  settings,
  recovery,
  initialEdits,
  children,
  BlockEditorProviderComponent = ExperimentalBlockEditorProvider,
  __unstableTemplate: template
}) => {
  const mode = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getRenderingMode(), []);
  const shouldRenderTemplate = !!template && mode !== 'post-only';
  const rootLevelPost = shouldRenderTemplate ? template : post;
  const defaultBlockContext = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const postContext = !NON_CONTEXTUAL_POST_TYPES.includes(rootLevelPost.type) || shouldRenderTemplate ? {
      postId: post.id,
      postType: post.type
    } : {};
    return {
      ...postContext,
      templateSlug: rootLevelPost.type === 'wp_template' ? rootLevelPost.slug : undefined
    };
  }, [shouldRenderTemplate, post.id, post.type, rootLevelPost.type, rootLevelPost.slug]);
  const {
    editorSettings,
    selection,
    isReady
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings,
      getEditorSelection,
      __unstableIsEditorReady
    } = select(store_store);
    return {
      editorSettings: getEditorSettings(),
      isReady: __unstableIsEditorReady(),
      selection: getEditorSelection()
    };
  }, []);
  const {
    id,
    type
  } = rootLevelPost;
  const blockEditorSettings = use_block_editor_settings(editorSettings, type, id);
  const [blocks, onInput, onChange] = useBlockEditorProps(post, template, mode);
  const {
    updatePostLock,
    setupEditor,
    updateEditorSettings,
    setCurrentTemplateId,
    setEditedPost,
    setRenderingMode
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));
  const {
    createWarningNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);

  // Ideally this should be synced on each change and not just something you do once.
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    // Assume that we don't need to initialize in the case of an error recovery.
    if (recovery) {
      return;
    }
    updatePostLock(settings.postLock);
    setupEditor(post, initialEdits, settings.template);
    if (settings.autosave) {
      createWarningNotice((0,external_wp_i18n_namespaceObject.__)('There is an autosave of this post that is more recent than the version below.'), {
        id: 'autosave-exists',
        actions: [{
          label: (0,external_wp_i18n_namespaceObject.__)('View the autosave'),
          url: settings.autosave.editLink
        }]
      });
    }
  }, []);

  // Synchronizes the active post with the state
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    setEditedPost(post.type, post.id);
  }, [post.type, post.id, setEditedPost]);

  // Synchronize the editor settings as they change.
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    updateEditorSettings(settings);
  }, [settings, updateEditorSettings]);

  // Synchronizes the active template with the state.
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    setCurrentTemplateId(template?.id);
  }, [template?.id, setCurrentTemplateId]);

  // Sets the right rendering mode when loading the editor.
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    var _settings$defaultRend;
    setRenderingMode((_settings$defaultRend = settings.defaultRenderingMode) !== null && _settings$defaultRend !== void 0 ? _settings$defaultRend : 'post-only');
  }, [settings.defaultRenderingMode, setRenderingMode]);
  if (!isReady) {
    return null;
  }
  return (0,external_React_.createElement)(external_wp_coreData_namespaceObject.EntityProvider, {
    kind: "root",
    type: "site"
  }, (0,external_React_.createElement)(external_wp_coreData_namespaceObject.EntityProvider, {
    kind: "postType",
    type: post.type,
    id: post.id
  }, (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.BlockContextProvider, {
    value: defaultBlockContext
  }, (0,external_React_.createElement)(BlockEditorProviderComponent, {
    value: blocks,
    onChange: onChange,
    onInput: onInput,
    selection: selection,
    settings: blockEditorSettings,
    useSubRegistry: false
  }, children, (0,external_React_.createElement)(PatternsMenuItems, null), mode === 'template-locked' && (0,external_React_.createElement)(DisableNonPageContentBlocks, null), type === 'wp_navigation' && (0,external_React_.createElement)(NavigationBlockEditingMode, null)))));
});
function EditorProvider(props) {
  return (0,external_React_.createElement)(ExperimentalEditorProvider, {
    ...props,
    BlockEditorProviderComponent: external_wp_blockEditor_namespaceObject.BlockEditorProvider
  }, props.children);
}
/* harmony default export */ const provider = (EditorProvider);

;// CONCATENATED MODULE: external ["wp","serverSideRender"]
const external_wp_serverSideRender_namespaceObject = window["wp"]["serverSideRender"];
var external_wp_serverSideRender_default = /*#__PURE__*/__webpack_require__.n(external_wp_serverSideRender_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/deprecated.js

// Block Creation Components.
/**
 * WordPress dependencies
 */




function deprecateComponent(name, Wrapped, staticsToHoist = []) {
  const Component = (0,external_wp_element_namespaceObject.forwardRef)((props, ref) => {
    external_wp_deprecated_default()('wp.editor.' + name, {
      since: '5.3',
      alternative: 'wp.blockEditor.' + name,
      version: '6.2'
    });
    return (0,external_React_.createElement)(Wrapped, {
      ref: ref,
      ...props
    });
  });
  staticsToHoist.forEach(staticName => {
    Component[staticName] = deprecateComponent(name + '.' + staticName, Wrapped[staticName]);
  });
  return Component;
}
function deprecateFunction(name, func) {
  return (...args) => {
    external_wp_deprecated_default()('wp.editor.' + name, {
      since: '5.3',
      alternative: 'wp.blockEditor.' + name,
      version: '6.2'
    });
    return func(...args);
  };
}
const RichText = deprecateComponent('RichText', external_wp_blockEditor_namespaceObject.RichText, ['Content']);
RichText.isEmpty = deprecateFunction('RichText.isEmpty', external_wp_blockEditor_namespaceObject.RichText.isEmpty);

const Autocomplete = deprecateComponent('Autocomplete', external_wp_blockEditor_namespaceObject.Autocomplete);
const AlignmentToolbar = deprecateComponent('AlignmentToolbar', external_wp_blockEditor_namespaceObject.AlignmentToolbar);
const BlockAlignmentToolbar = deprecateComponent('BlockAlignmentToolbar', external_wp_blockEditor_namespaceObject.BlockAlignmentToolbar);
const BlockControls = deprecateComponent('BlockControls', external_wp_blockEditor_namespaceObject.BlockControls, ['Slot']);
const BlockEdit = deprecateComponent('BlockEdit', external_wp_blockEditor_namespaceObject.BlockEdit);
const BlockEditorKeyboardShortcuts = deprecateComponent('BlockEditorKeyboardShortcuts', external_wp_blockEditor_namespaceObject.BlockEditorKeyboardShortcuts);
const BlockFormatControls = deprecateComponent('BlockFormatControls', external_wp_blockEditor_namespaceObject.BlockFormatControls, ['Slot']);
const BlockIcon = deprecateComponent('BlockIcon', external_wp_blockEditor_namespaceObject.BlockIcon);
const BlockInspector = deprecateComponent('BlockInspector', external_wp_blockEditor_namespaceObject.BlockInspector);
const BlockList = deprecateComponent('BlockList', external_wp_blockEditor_namespaceObject.BlockList);
const BlockMover = deprecateComponent('BlockMover', external_wp_blockEditor_namespaceObject.BlockMover);
const BlockNavigationDropdown = deprecateComponent('BlockNavigationDropdown', external_wp_blockEditor_namespaceObject.BlockNavigationDropdown);
const BlockSelectionClearer = deprecateComponent('BlockSelectionClearer', external_wp_blockEditor_namespaceObject.BlockSelectionClearer);
const BlockSettingsMenu = deprecateComponent('BlockSettingsMenu', external_wp_blockEditor_namespaceObject.BlockSettingsMenu);
const BlockTitle = deprecateComponent('BlockTitle', external_wp_blockEditor_namespaceObject.BlockTitle);
const BlockToolbar = deprecateComponent('BlockToolbar', external_wp_blockEditor_namespaceObject.BlockToolbar);
const ColorPalette = deprecateComponent('ColorPalette', external_wp_blockEditor_namespaceObject.ColorPalette);
const ContrastChecker = deprecateComponent('ContrastChecker', external_wp_blockEditor_namespaceObject.ContrastChecker);
const CopyHandler = deprecateComponent('CopyHandler', external_wp_blockEditor_namespaceObject.CopyHandler);
const DefaultBlockAppender = deprecateComponent('DefaultBlockAppender', external_wp_blockEditor_namespaceObject.DefaultBlockAppender);
const FontSizePicker = deprecateComponent('FontSizePicker', external_wp_blockEditor_namespaceObject.FontSizePicker);
const Inserter = deprecateComponent('Inserter', external_wp_blockEditor_namespaceObject.Inserter);
const InnerBlocks = deprecateComponent('InnerBlocks', external_wp_blockEditor_namespaceObject.InnerBlocks, ['ButtonBlockAppender', 'DefaultBlockAppender', 'Content']);
const InspectorAdvancedControls = deprecateComponent('InspectorAdvancedControls', external_wp_blockEditor_namespaceObject.InspectorAdvancedControls, ['Slot']);
const InspectorControls = deprecateComponent('InspectorControls', external_wp_blockEditor_namespaceObject.InspectorControls, ['Slot']);
const PanelColorSettings = deprecateComponent('PanelColorSettings', external_wp_blockEditor_namespaceObject.PanelColorSettings);
const PlainText = deprecateComponent('PlainText', external_wp_blockEditor_namespaceObject.PlainText);
const RichTextShortcut = deprecateComponent('RichTextShortcut', external_wp_blockEditor_namespaceObject.RichTextShortcut);
const RichTextToolbarButton = deprecateComponent('RichTextToolbarButton', external_wp_blockEditor_namespaceObject.RichTextToolbarButton);
const __unstableRichTextInputEvent = deprecateComponent('__unstableRichTextInputEvent', external_wp_blockEditor_namespaceObject.__unstableRichTextInputEvent);
const MediaPlaceholder = deprecateComponent('MediaPlaceholder', external_wp_blockEditor_namespaceObject.MediaPlaceholder);
const MediaUpload = deprecateComponent('MediaUpload', external_wp_blockEditor_namespaceObject.MediaUpload);
const MediaUploadCheck = deprecateComponent('MediaUploadCheck', external_wp_blockEditor_namespaceObject.MediaUploadCheck);
const MultiSelectScrollIntoView = deprecateComponent('MultiSelectScrollIntoView', external_wp_blockEditor_namespaceObject.MultiSelectScrollIntoView);
const NavigableToolbar = deprecateComponent('NavigableToolbar', external_wp_blockEditor_namespaceObject.NavigableToolbar);
const ObserveTyping = deprecateComponent('ObserveTyping', external_wp_blockEditor_namespaceObject.ObserveTyping);
const SkipToSelectedBlock = deprecateComponent('SkipToSelectedBlock', external_wp_blockEditor_namespaceObject.SkipToSelectedBlock);
const URLInput = deprecateComponent('URLInput', external_wp_blockEditor_namespaceObject.URLInput);
const URLInputButton = deprecateComponent('URLInputButton', external_wp_blockEditor_namespaceObject.URLInputButton);
const URLPopover = deprecateComponent('URLPopover', external_wp_blockEditor_namespaceObject.URLPopover);
const Warning = deprecateComponent('Warning', external_wp_blockEditor_namespaceObject.Warning);
const WritingFlow = deprecateComponent('WritingFlow', external_wp_blockEditor_namespaceObject.WritingFlow);
const createCustomColorsHOC = deprecateFunction('createCustomColorsHOC', external_wp_blockEditor_namespaceObject.createCustomColorsHOC);
const getColorClassName = deprecateFunction('getColorClassName', external_wp_blockEditor_namespaceObject.getColorClassName);
const getColorObjectByAttributeValues = deprecateFunction('getColorObjectByAttributeValues', external_wp_blockEditor_namespaceObject.getColorObjectByAttributeValues);
const getColorObjectByColorValue = deprecateFunction('getColorObjectByColorValue', external_wp_blockEditor_namespaceObject.getColorObjectByColorValue);
const getFontSize = deprecateFunction('getFontSize', external_wp_blockEditor_namespaceObject.getFontSize);
const getFontSizeClass = deprecateFunction('getFontSizeClass', external_wp_blockEditor_namespaceObject.getFontSizeClass);
const withColorContext = deprecateFunction('withColorContext', external_wp_blockEditor_namespaceObject.withColorContext);
const withColors = deprecateFunction('withColors', external_wp_blockEditor_namespaceObject.withColors);
const withFontSizes = deprecateFunction('withFontSizes', external_wp_blockEditor_namespaceObject.withFontSizes);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/index.js
/**
 * Internal dependencies
 */


// Block Creation Components.


// Post Related Components.
















































































// State Related Components.


const VisualEditorGlobalKeyboardShortcuts = EditorKeyboardShortcuts;
const TextEditorGlobalKeyboardShortcuts = EditorKeyboardShortcuts;

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/url.js
/**
 * WordPress dependencies
 */



/**
 * Performs some basic cleanup of a string for use as a post slug
 *
 * This replicates some of what sanitize_title() does in WordPress core, but
 * is only designed to approximate what the slug will be.
 *
 * Converts Latin-1 Supplement and Latin Extended-A letters to basic Latin letters.
 * Removes combining diacritical marks. Converts whitespace, periods,
 * and forward slashes to hyphens. Removes any remaining non-word characters
 * except hyphens and underscores. Converts remaining string to lowercase.
 * It does not account for octets, HTML entities, or other encoded characters.
 *
 * @param {string} string Title or slug to be processed
 *
 * @return {string} Processed string
 */
function cleanForSlug(string) {
  external_wp_deprecated_default()('wp.editor.cleanForSlug', {
    since: '12.7',
    plugin: 'Gutenberg',
    alternative: 'wp.url.cleanForSlug'
  });
  return (0,external_wp_url_namespaceObject.cleanForSlug)(string);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/index.js
/**
 * Internal dependencies
 */





;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-canvas/edit-template-blocks-notification.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


/**
 * Component that:
 *
 * - Displays a 'Edit your template to edit this block' notification when the
 *   user is focusing on editing page content and clicks on a disabled template
 *   block.
 * - Displays a 'Edit your template to edit this block' dialog when the user
 *   is focusing on editing page conetnt and double clicks on a disabled
 *   template block.
 *
 * @param {Object}                                 props
 * @param {import('react').RefObject<HTMLElement>} props.contentRef Ref to the block
 *                                                                  editor iframe canvas.
 */
function EditTemplateBlocksNotification({
  contentRef
}) {
  const {
    onNavigateToEntityRecord,
    templateId
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings,
      getCurrentTemplateId
    } = select(store_store);
    return {
      onNavigateToEntityRecord: getEditorSettings().onNavigateToEntityRecord,
      templateId: getCurrentTemplateId()
    };
  }, []);
  const {
    getNotices
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_notices_namespaceObject.store);
  const {
    createInfoNotice,
    removeNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const [isDialogOpen, setIsDialogOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  const lastNoticeId = (0,external_wp_element_namespaceObject.useRef)(0);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const handleClick = async event => {
      if (!event.target.classList.contains('is-root-container')) {
        return;
      }
      const isNoticeAlreadyShowing = getNotices().some(notice => notice.id === lastNoticeId.current);
      if (isNoticeAlreadyShowing) {
        return;
      }
      const {
        notice
      } = await createInfoNotice((0,external_wp_i18n_namespaceObject.__)('Edit your template to edit this block.'), {
        isDismissible: true,
        type: 'snackbar',
        actions: [{
          label: (0,external_wp_i18n_namespaceObject.__)('Edit template'),
          onClick: () => onNavigateToEntityRecord({
            postId: templateId,
            postType: 'wp_template'
          })
        }]
      });
      lastNoticeId.current = notice.id;
    };
    const handleDblClick = event => {
      if (!event.target.classList.contains('is-root-container')) {
        return;
      }
      if (lastNoticeId.current) {
        removeNotice(lastNoticeId.current);
      }
      setIsDialogOpen(true);
    };
    const canvas = contentRef.current;
    canvas?.addEventListener('click', handleClick);
    canvas?.addEventListener('dblclick', handleDblClick);
    return () => {
      canvas?.removeEventListener('click', handleClick);
      canvas?.removeEventListener('dblclick', handleDblClick);
    };
  }, [lastNoticeId, contentRef, getNotices, createInfoNotice, onNavigateToEntityRecord, templateId, removeNotice]);
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalConfirmDialog, {
    isOpen: isDialogOpen,
    confirmButtonText: (0,external_wp_i18n_namespaceObject.__)('Edit template'),
    onConfirm: () => {
      setIsDialogOpen(false);
      onNavigateToEntityRecord({
        postId: templateId,
        postType: 'wp_template'
      });
    },
    onCancel: () => setIsDialogOpen(false)
  }, (0,external_wp_i18n_namespaceObject.__)('Edit your template to edit this block.'));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-canvas/index.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */




const {
  LayoutStyle,
  useLayoutClasses,
  useLayoutStyles,
  ExperimentalBlockCanvas: BlockCanvas,
  useFlashEditableBlocks
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const editor_canvas_noop = () => {};

/**
 * These post types have a special editor where they don't allow you to fill the title
 * and they don't apply the layout styles.
 */
const DESIGN_POST_TYPES = ['wp_block', 'wp_template', 'wp_navigation', 'wp_template_part'];

/**
 * Given an array of nested blocks, find the first Post Content
 * block inside it, recursing through any nesting levels,
 * and return its attributes.
 *
 * @param {Array} blocks A list of blocks.
 *
 * @return {Object | undefined} The Post Content block.
 */
function getPostContentAttributes(blocks) {
  for (let i = 0; i < blocks.length; i++) {
    if (blocks[i].name === 'core/post-content') {
      return blocks[i].attributes;
    }
    if (blocks[i].innerBlocks.length) {
      const nestedPostContent = getPostContentAttributes(blocks[i].innerBlocks);
      if (nestedPostContent) {
        return nestedPostContent;
      }
    }
  }
}
function checkForPostContentAtRootLevel(blocks) {
  for (let i = 0; i < blocks.length; i++) {
    if (blocks[i].name === 'core/post-content') {
      return true;
    }
  }
  return false;
}
function EditorCanvas({
  // Ideally as we unify post and site editors, we won't need these props.
  autoFocus,
  className,
  renderAppender,
  styles,
  disableIframe = false,
  iframeProps,
  children
}) {
  const {
    renderingMode,
    postContentAttributes,
    editedPostTemplate = {},
    wrapperBlockName,
    wrapperUniqueId,
    deviceType,
    showEditorPadding,
    isDesignPostType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getCurrentPostId,
      getCurrentPostType,
      getCurrentTemplateId,
      getEditorSettings,
      getRenderingMode,
      getDeviceType
    } = select(store_store);
    const {
      getPostType,
      canUser,
      getEditedEntityRecord
    } = select(external_wp_coreData_namespaceObject.store);
    const postTypeSlug = getCurrentPostType();
    const _renderingMode = getRenderingMode();
    let _wrapperBlockName;
    if (postTypeSlug === 'wp_block') {
      _wrapperBlockName = 'core/block';
    } else if (_renderingMode === 'post-only') {
      _wrapperBlockName = 'core/post-content';
    }
    const editorSettings = getEditorSettings();
    const supportsTemplateMode = editorSettings.supportsTemplateMode;
    const postType = getPostType(postTypeSlug);
    const canEditTemplate = canUser('create', 'templates');
    const currentTemplateId = getCurrentTemplateId();
    const template = currentTemplateId ? getEditedEntityRecord('postType', 'wp_template', currentTemplateId) : undefined;
    return {
      renderingMode: _renderingMode,
      postContentAttributes: editorSettings.postContentAttributes,
      isDesignPostType: DESIGN_POST_TYPES.includes(postTypeSlug),
      // Post template fetch returns a 404 on classic themes, which
      // messes with e2e tests, so check it's a block theme first.
      editedPostTemplate: postType?.viewable && supportsTemplateMode && canEditTemplate ? template : undefined,
      wrapperBlockName: _wrapperBlockName,
      wrapperUniqueId: getCurrentPostId(),
      deviceType: getDeviceType(),
      showEditorPadding: !!editorSettings.onNavigateToPreviousEntityRecord
    };
  }, []);
  const {
    isCleanNewPost
  } = (0,external_wp_data_namespaceObject.useSelect)(store_store);
  const {
    hasRootPaddingAwareAlignments,
    themeHasDisabledLayoutStyles,
    themeSupportsLayout
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const _settings = select(external_wp_blockEditor_namespaceObject.store).getSettings();
    return {
      themeHasDisabledLayoutStyles: _settings.disableLayoutStyles,
      themeSupportsLayout: _settings.supportsLayout,
      hasRootPaddingAwareAlignments: _settings.__experimentalFeatures?.useRootPaddingAwareAlignments
    };
  }, []);
  const deviceStyles = (0,external_wp_blockEditor_namespaceObject.__experimentalUseResizeCanvas)(deviceType);
  const [globalLayoutSettings] = (0,external_wp_blockEditor_namespaceObject.useSettings)('layout');

  // fallbackLayout is used if there is no Post Content,
  // and for Post Title.
  const fallbackLayout = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (renderingMode !== 'post-only' || isDesignPostType) {
      return {
        type: 'default'
      };
    }
    if (themeSupportsLayout) {
      // We need to ensure support for wide and full alignments,
      // so we add the constrained type.
      return {
        ...globalLayoutSettings,
        type: 'constrained'
      };
    }
    // Set default layout for classic themes so all alignments are supported.
    return {
      type: 'default'
    };
  }, [renderingMode, themeSupportsLayout, globalLayoutSettings, isDesignPostType]);
  const newestPostContentAttributes = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!editedPostTemplate?.content && !editedPostTemplate?.blocks && postContentAttributes) {
      return postContentAttributes;
    }
    // When in template editing mode, we can access the blocks directly.
    if (editedPostTemplate?.blocks) {
      return getPostContentAttributes(editedPostTemplate?.blocks);
    }
    // If there are no blocks, we have to parse the content string.
    // Best double-check it's a string otherwise the parse function gets unhappy.
    const parseableContent = typeof editedPostTemplate?.content === 'string' ? editedPostTemplate?.content : '';
    return getPostContentAttributes((0,external_wp_blocks_namespaceObject.parse)(parseableContent)) || {};
  }, [editedPostTemplate?.content, editedPostTemplate?.blocks, postContentAttributes]);
  const hasPostContentAtRootLevel = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!editedPostTemplate?.content && !editedPostTemplate?.blocks) {
      return false;
    }
    // When in template editing mode, we can access the blocks directly.
    if (editedPostTemplate?.blocks) {
      return checkForPostContentAtRootLevel(editedPostTemplate?.blocks);
    }
    // If there are no blocks, we have to parse the content string.
    // Best double-check it's a string otherwise the parse function gets unhappy.
    const parseableContent = typeof editedPostTemplate?.content === 'string' ? editedPostTemplate?.content : '';
    return checkForPostContentAtRootLevel((0,external_wp_blocks_namespaceObject.parse)(parseableContent)) || false;
  }, [editedPostTemplate?.content, editedPostTemplate?.blocks]);
  const {
    layout = {},
    align = ''
  } = newestPostContentAttributes || {};
  const postContentLayoutClasses = useLayoutClasses(newestPostContentAttributes, 'core/post-content');
  const blockListLayoutClass = classnames_default()({
    'is-layout-flow': !themeSupportsLayout
  }, themeSupportsLayout && postContentLayoutClasses, align && `align${align}`);
  const postContentLayoutStyles = useLayoutStyles(newestPostContentAttributes, 'core/post-content', '.block-editor-block-list__layout.is-root-container');

  // Update type for blocks using legacy layouts.
  const postContentLayout = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return layout && (layout?.type === 'constrained' || layout?.inherit || layout?.contentSize || layout?.wideSize) ? {
      ...globalLayoutSettings,
      ...layout,
      type: 'constrained'
    } : {
      ...globalLayoutSettings,
      ...layout,
      type: 'default'
    };
  }, [layout?.type, layout?.inherit, layout?.contentSize, layout?.wideSize, globalLayoutSettings]);

  // If there is a Post Content block we use its layout for the block list;
  // if not, this must be a classic theme, in which case we use the fallback layout.
  const blockListLayout = postContentAttributes ? postContentLayout : fallbackLayout;
  const postEditorLayout = blockListLayout?.type === 'default' && !hasPostContentAtRootLevel ? fallbackLayout : blockListLayout;
  const observeTypingRef = (0,external_wp_blockEditor_namespaceObject.__unstableUseTypingObserver)();
  const titleRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!autoFocus || !isCleanNewPost()) {
      return;
    }
    titleRef?.current?.focus();
  }, [autoFocus, isCleanNewPost]);

  // Add some styles for alignwide/alignfull Post Content and its children.
  const alignCSS = `.is-root-container.alignwide { max-width: var(--wp--style--global--wide-size); margin-left: auto; margin-right: auto;}
		.is-root-container.alignwide:where(.is-layout-flow) > :not(.alignleft):not(.alignright) { max-width: var(--wp--style--global--wide-size);}
		.is-root-container.alignfull { max-width: none; margin-left: auto; margin-right: auto;}
		.is-root-container.alignfull:where(.is-layout-flow) > :not(.alignleft):not(.alignright) { max-width: none;}`;
  const localRef = (0,external_wp_element_namespaceObject.useRef)();
  const typewriterRef = (0,external_wp_blockEditor_namespaceObject.__unstableUseTypewriter)();
  const contentRef = (0,external_wp_compose_namespaceObject.useMergeRefs)([localRef, renderingMode === 'post-only' ? typewriterRef : editor_canvas_noop, useFlashEditableBlocks({
    isEnabled: renderingMode === 'template-locked'
  })]);
  return (0,external_React_.createElement)(BlockCanvas, {
    shouldIframe: !disableIframe || ['Tablet', 'Mobile'].includes(deviceType),
    contentRef: contentRef,
    styles: styles,
    height: "100%",
    iframeProps: {
      className: classnames_default()('editor-canvas__iframe', {
        'has-editor-padding': showEditorPadding
      }),
      ...iframeProps,
      style: {
        ...iframeProps?.style,
        ...deviceStyles
      }
    }
  }, themeSupportsLayout && !themeHasDisabledLayoutStyles && renderingMode === 'post-only' && !isDesignPostType && (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(LayoutStyle, {
    selector: ".editor-editor-canvas__post-title-wrapper",
    layout: fallbackLayout
  }), (0,external_React_.createElement)(LayoutStyle, {
    selector: ".block-editor-block-list__layout.is-root-container",
    layout: postEditorLayout
  }), align && (0,external_React_.createElement)(LayoutStyle, {
    css: alignCSS
  }), postContentLayoutStyles && (0,external_React_.createElement)(LayoutStyle, {
    layout: postContentLayout,
    css: postContentLayoutStyles
  })), renderingMode === 'post-only' && !isDesignPostType && (0,external_React_.createElement)("div", {
    className: classnames_default()('editor-editor-canvas__post-title-wrapper',
    // The following class is only here for backward comapatibility
    // some themes might be using it to style the post title.
    'edit-post-visual-editor__post-title-wrapper', {
      'has-global-padding': hasRootPaddingAwareAlignments
    }),
    contentEditable: false,
    ref: observeTypingRef,
    style: {
      // This is using inline styles
      // so it's applied for both iframed and non iframed editors.
      marginTop: '4rem'
    }
  }, (0,external_React_.createElement)(post_title, {
    ref: titleRef
  })), (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.RecursionProvider, {
    blockName: wrapperBlockName,
    uniqueId: wrapperUniqueId
  }, (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.BlockList, {
    className: classnames_default()(className, 'is-' + deviceType.toLowerCase() + '-preview', renderingMode !== 'post-only' || isDesignPostType ? 'wp-site-blocks' : `${blockListLayoutClass} wp-block-post-content` // Ensure root level blocks receive default/flow blockGap styling rules.
    ),
    layout: blockListLayout,
    dropZoneElement:
    // When iframed, pass in the html element of the iframe to
    // ensure the drop zone extends to the edges of the iframe.
    disableIframe ? localRef.current : localRef.current?.parentNode,
    renderAppender: renderAppender,
    __unstableDisableDropZone:
    // In template preview mode, disable drop zones at the root of the template.
    renderingMode === 'template-locked' ? true : false
  }), renderingMode === 'template-locked' && (0,external_React_.createElement)(EditTemplateBlocksNotification, {
    contentRef: localRef
  })), children);
}
/* harmony default export */ const editor_canvas = (EditorCanvas);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/preferences-modal/enable-panel.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


const {
  PreferenceBaseOption
} = unlock(external_wp_preferences_namespaceObject.privateApis);
/* harmony default export */ const enable_panel = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, {
  panelName
}) => {
  const {
    isEditorPanelEnabled,
    isEditorPanelRemoved
  } = select(store_store);
  return {
    isRemoved: isEditorPanelRemoved(panelName),
    isChecked: isEditorPanelEnabled(panelName)
  };
}), (0,external_wp_compose_namespaceObject.ifCondition)(({
  isRemoved
}) => !isRemoved), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  panelName
}) => ({
  onChange: () => dispatch(store_store).toggleEditorPanelEnabled(panelName)
})))(PreferenceBaseOption));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/preferences-modal/enable-plugin-document-setting-panel.js

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

const {
  Fill: enable_plugin_document_setting_panel_Fill,
  Slot: enable_plugin_document_setting_panel_Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('EnablePluginDocumentSettingPanelOption');
const EnablePluginDocumentSettingPanelOption = ({
  label,
  panelName
}) => (0,external_React_.createElement)(enable_plugin_document_setting_panel_Fill, null, (0,external_React_.createElement)(enable_panel, {
  label: label,
  panelName: panelName
}));
EnablePluginDocumentSettingPanelOption.Slot = enable_plugin_document_setting_panel_Slot;
/* harmony default export */ const enable_plugin_document_setting_panel = (EnablePluginDocumentSettingPanelOption);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/plus.js

/**
 * WordPress dependencies
 */

const plus = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M11 12.5V17.5H12.5V12.5H17.5V11H12.5V6H11V11H6V12.5H11Z"
}));
/* harmony default export */ const library_plus = (plus);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/list-view.js

/**
 * WordPress dependencies
 */

const listView = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M3 6h11v1.5H3V6Zm3.5 5.5h11V13h-11v-1.5ZM21 17H10v1.5h11V17Z"
}));
/* harmony default export */ const list_view = (listView);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-tools/index.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */




const {
  useCanBlockToolbarBeFocused
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const preventDefault = event => {
  event.preventDefault();
};
function DocumentTools({
  className,
  disableBlockTools = false,
  children,
  // This is a temporary prop until the list view is fully unified between post and site editors.
  listViewLabel = (0,external_wp_i18n_namespaceObject.__)('Document Overview')
}) {
  const inserterButton = (0,external_wp_element_namespaceObject.useRef)();
  const {
    setIsInserterOpened,
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    isDistractionFree,
    isInserterOpened,
    isListViewOpen,
    listViewShortcut,
    listViewToggleRef,
    hasFixedToolbar,
    showIconLabels
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getSettings
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    const {
      isListViewOpened,
      getListViewToggleRef
    } = unlock(select(store_store));
    const {
      getShortcutRepresentation
    } = select(external_wp_keyboardShortcuts_namespaceObject.store);
    return {
      isInserterOpened: select(store_store).isInserterOpened(),
      isListViewOpen: isListViewOpened(),
      listViewShortcut: getShortcutRepresentation('core/editor/toggle-list-view'),
      listViewToggleRef: getListViewToggleRef(),
      hasFixedToolbar: getSettings().hasFixedToolbar,
      showIconLabels: get('core', 'showIconLabels'),
      isDistractionFree: get('core', 'distractionFree')
    };
  }, []);
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');
  const isWideViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('wide');
  const blockToolbarCanBeFocused = useCanBlockToolbarBeFocused();

  /* translators: accessibility text for the editor toolbar */
  const toolbarAriaLabel = (0,external_wp_i18n_namespaceObject.__)('Document tools');
  const toggleListView = (0,external_wp_element_namespaceObject.useCallback)(() => setIsListViewOpened(!isListViewOpen), [setIsListViewOpened, isListViewOpen]);
  const toggleInserter = (0,external_wp_element_namespaceObject.useCallback)(() => {
    if (isInserterOpened) {
      // Focusing the inserter button should close the inserter popover.
      // However, there are some cases it won't close when the focus is lost.
      // See https://github.com/WordPress/gutenberg/issues/43090 for more details.
      inserterButton.current.focus();
      setIsInserterOpened(false);
    } else {
      setIsInserterOpened(true);
    }
  }, [isInserterOpened, setIsInserterOpened]);

  /* translators: button label text should, if possible, be under 16 characters. */
  const longLabel = (0,external_wp_i18n_namespaceObject._x)('Toggle block inserter', 'Generic label for block inserter button');
  const shortLabel = !isInserterOpened ? (0,external_wp_i18n_namespaceObject.__)('Add') : (0,external_wp_i18n_namespaceObject.__)('Close');
  return (
    // Some plugins expect and use the `edit-post-header-toolbar` CSS class to
    // find the toolbar and inject UI elements into it. This is not officially
    // supported, but we're keeping it in the list of class names for backwards
    // compatibility.
    (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.NavigableToolbar, {
      className: classnames_default()('editor-document-tools', 'edit-post-header-toolbar', className),
      "aria-label": toolbarAriaLabel,
      shouldUseKeyboardFocusShortcut: !blockToolbarCanBeFocused,
      variant: "unstyled"
    }, (0,external_React_.createElement)("div", {
      className: "editor-document-tools__left"
    }, !isDistractionFree && (0,external_React_.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
      ref: inserterButton,
      as: external_wp_components_namespaceObject.Button,
      className: "editor-document-tools__inserter-toggle",
      variant: "primary",
      isPressed: isInserterOpened,
      onMouseDown: preventDefault,
      onClick: toggleInserter,
      disabled: disableBlockTools,
      icon: library_plus,
      label: showIconLabels ? shortLabel : longLabel,
      showTooltip: !showIconLabels,
      "aria-expanded": isInserterOpened
    }), (isWideViewport || !showIconLabels) && (0,external_React_.createElement)(external_React_.Fragment, null, isLargeViewport && !hasFixedToolbar && (0,external_React_.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
      as: external_wp_blockEditor_namespaceObject.ToolSelector,
      showTooltip: !showIconLabels,
      variant: showIconLabels ? 'tertiary' : undefined,
      disabled: disableBlockTools,
      size: "compact"
    }), (0,external_React_.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
      as: editor_history_undo,
      showTooltip: !showIconLabels,
      variant: showIconLabels ? 'tertiary' : undefined,
      size: "compact"
    }), (0,external_React_.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
      as: editor_history_redo,
      showTooltip: !showIconLabels,
      variant: showIconLabels ? 'tertiary' : undefined,
      size: "compact"
    }), !isDistractionFree && (0,external_React_.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
      as: external_wp_components_namespaceObject.Button,
      className: "editor-document-tools__document-overview-toggle",
      icon: list_view,
      disabled: disableBlockTools,
      isPressed: isListViewOpen
      /* translators: button label text should, if possible, be under 16 characters. */,
      label: listViewLabel,
      onClick: toggleListView,
      shortcut: listViewShortcut,
      showTooltip: !showIconLabels,
      variant: showIconLabels ? 'tertiary' : undefined,
      "aria-expanded": isListViewOpen,
      ref: listViewToggleRef,
      size: "compact"
    })), children))
  );
}
/* harmony default export */ const document_tools = (DocumentTools);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js

/**
 * WordPress dependencies
 */

const close_close = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ const library_close = (close_close);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/inserter-sidebar/index.js

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */


function InserterSidebar() {
  const {
    insertionPoint,
    showMostUsedBlocks
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getInsertionPoint
    } = unlock(select(store_store));
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    return {
      insertionPoint: getInsertionPoint(),
      showMostUsedBlocks: get('core', 'mostUsedBlocks')
    };
  }, []);
  const {
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  const TagName = !isMobileViewport ? external_wp_components_namespaceObject.VisuallyHidden : 'div';
  const [inserterDialogRef, inserterDialogProps] = (0,external_wp_compose_namespaceObject.__experimentalUseDialog)({
    onClose: () => setIsInserterOpened(false),
    focusOnMount: null
  });
  const libraryRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    libraryRef.current.focusSearch();
  }, []);
  return (0,external_React_.createElement)("div", {
    ref: inserterDialogRef,
    ...inserterDialogProps,
    className: "editor-inserter-sidebar"
  }, (0,external_React_.createElement)(TagName, {
    className: "editor-inserter-sidebar__header"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    icon: library_close,
    label: (0,external_wp_i18n_namespaceObject.__)('Close block inserter'),
    onClick: () => setIsInserterOpened(false)
  })), (0,external_React_.createElement)("div", {
    className: "editor-inserter-sidebar__content"
  }, (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.__experimentalLibrary, {
    showMostUsedBlocks: showMostUsedBlocks,
    showInserterHelpPanel: true,
    shouldFocusBlock: isMobileViewport,
    rootClientId: insertionPoint.rootClientId,
    __experimentalInsertionIndex: insertionPoint.insertionIndex,
    __experimentalFilterValue: insertionPoint.filterValue,
    ref: libraryRef
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/list-view-sidebar/list-view-outline.js

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function ListViewOutline() {
  return (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)("div", {
    className: "editor-list-view-sidebar__outline"
  }, (0,external_React_.createElement)("div", null, (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalText, null, (0,external_wp_i18n_namespaceObject.__)('Characters:')), (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalText, null, (0,external_React_.createElement)(CharacterCount, null))), (0,external_React_.createElement)("div", null, (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalText, null, (0,external_wp_i18n_namespaceObject.__)('Words:')), (0,external_React_.createElement)(WordCount, null)), (0,external_React_.createElement)("div", null, (0,external_React_.createElement)(external_wp_components_namespaceObject.__experimentalText, null, (0,external_wp_i18n_namespaceObject.__)('Time to read:')), (0,external_React_.createElement)(TimeToRead, null))), (0,external_React_.createElement)(document_outline, null));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/list-view-sidebar/index.js

/**
 * WordPress dependencies
 */











/**
 * Internal dependencies
 */



const {
  Tabs
} = unlock(external_wp_components_namespaceObject.privateApis);
function ListViewSidebar() {
  const {
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    getListViewToggleRef
  } = unlock((0,external_wp_data_namespaceObject.useSelect)(store_store));

  // This hook handles focus when the sidebar first renders.
  const focusOnMountRef = (0,external_wp_compose_namespaceObject.useFocusOnMount)('firstElement');

  // When closing the list view, focus should return to the toggle button.
  const closeListView = (0,external_wp_element_namespaceObject.useCallback)(() => {
    setIsListViewOpened(false);
    getListViewToggleRef().current?.focus();
  }, [getListViewToggleRef, setIsListViewOpened]);
  const closeOnEscape = (0,external_wp_element_namespaceObject.useCallback)(event => {
    if (event.keyCode === external_wp_keycodes_namespaceObject.ESCAPE && !event.defaultPrevented) {
      event.preventDefault();
      closeListView();
    }
  }, [closeListView]);

  // Use internal state instead of a ref to make sure that the component
  // re-renders when the dropZoneElement updates.
  const [dropZoneElement, setDropZoneElement] = (0,external_wp_element_namespaceObject.useState)(null);
  // Tracks our current tab.
  const [tab, setTab] = (0,external_wp_element_namespaceObject.useState)('list-view');

  // This ref refers to the sidebar as a whole.
  const sidebarRef = (0,external_wp_element_namespaceObject.useRef)();
  // This ref refers to the tab panel.
  const tabsRef = (0,external_wp_element_namespaceObject.useRef)();
  // This ref refers to the list view application area.
  const listViewRef = (0,external_wp_element_namespaceObject.useRef)();

  // Must merge the refs together so focus can be handled properly in the next function.
  const listViewContainerRef = (0,external_wp_compose_namespaceObject.useMergeRefs)([focusOnMountRef, listViewRef, setDropZoneElement]);

  /*
   * Callback function to handle list view or outline focus.
   *
   * @param {string} currentTab The current tab. Either list view or outline.
   *
   * @return void
   */
  function handleSidebarFocus(currentTab) {
    // Tab panel focus.
    const tabPanelFocus = external_wp_dom_namespaceObject.focus.tabbable.find(tabsRef.current)[0];
    // List view tab is selected.
    if (currentTab === 'list-view') {
      // Either focus the list view or the tab panel. Must have a fallback because the list view does not render when there are no blocks.
      const listViewApplicationFocus = external_wp_dom_namespaceObject.focus.tabbable.find(listViewRef.current)[0];
      const listViewFocusArea = sidebarRef.current.contains(listViewApplicationFocus) ? listViewApplicationFocus : tabPanelFocus;
      listViewFocusArea.focus();
      // Outline tab is selected.
    } else {
      tabPanelFocus.focus();
    }
  }
  const handleToggleListViewShortcut = (0,external_wp_element_namespaceObject.useCallback)(() => {
    // If the sidebar has focus, it is safe to close.
    if (sidebarRef.current.contains(sidebarRef.current.ownerDocument.activeElement)) {
      closeListView();
    } else {
      // If the list view or outline does not have focus, focus should be moved to it.
      handleSidebarFocus(tab);
    }
  }, [closeListView, tab]);

  // This only fires when the sidebar is open because of the conditional rendering.
  // It is the same shortcut to open but that is defined as a global shortcut and only fires when the sidebar is closed.
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/editor/toggle-list-view', handleToggleListViewShortcut);
  return (
    // eslint-disable-next-line jsx-a11y/no-static-element-interactions
    (0,external_React_.createElement)("div", {
      className: "editor-list-view-sidebar",
      onKeyDown: closeOnEscape,
      ref: sidebarRef
    }, (0,external_React_.createElement)(Tabs, {
      onSelect: tabName => setTab(tabName),
      selectOnMove: false
      // The initial tab value is set explicitly to avoid an initial
      // render where no tab is selected. This ensures that the
      // tabpanel height is correct so the relevant scroll container
      // can be rendered internally.
      ,
      initialTabId: "list-view"
    }, (0,external_React_.createElement)("div", {
      className: "edit-post-editor__document-overview-panel__header"
    }, (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
      className: "editor-list-view-sidebar__close-button",
      icon: close_small,
      label: (0,external_wp_i18n_namespaceObject.__)('Close'),
      onClick: closeListView
    }), (0,external_React_.createElement)(Tabs.TabList, {
      className: "editor-list-view-sidebar__tabs-tablist",
      ref: tabsRef
    }, (0,external_React_.createElement)(Tabs.Tab, {
      className: "editor-list-view-sidebar__tabs-tab",
      tabId: "list-view"
    }, (0,external_wp_i18n_namespaceObject._x)('List View', 'Post overview')), (0,external_React_.createElement)(Tabs.Tab, {
      className: "editor-list-view-sidebar__tabs-tab",
      tabId: "outline"
    }, (0,external_wp_i18n_namespaceObject._x)('Outline', 'Post overview')))), (0,external_React_.createElement)(Tabs.TabPanel, {
      ref: listViewContainerRef,
      className: "editor-list-view-sidebar__tabs-tabpanel",
      tabId: "list-view",
      focusable: false
    }, (0,external_React_.createElement)("div", {
      className: "editor-list-view-sidebar__list-view-container"
    }, (0,external_React_.createElement)("div", {
      className: "editor-list-view-sidebar__list-view-panel-content"
    }, (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.__experimentalListView, {
      dropZoneElement: dropZoneElement
    })))), (0,external_React_.createElement)(Tabs.TabPanel, {
      className: "editor-list-view-sidebar__tabs-tabpanel",
      tabId: "outline",
      focusable: false
    }, (0,external_React_.createElement)("div", {
      className: "editor-list-view-sidebar__list-view-container"
    }, (0,external_React_.createElement)(ListViewOutline, null)))))
  );
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/external.js

/**
 * WordPress dependencies
 */

const external = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"
}));
/* harmony default export */ const library_external = (external);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-view-link/index.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */

function PostViewLink() {
  const {
    hasLoaded,
    permalink,
    isPublished,
    label,
    showIconLabels
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    // Grab post type to retrieve the view_item label.
    const postTypeSlug = select(store_store).getCurrentPostType();
    const postType = select(external_wp_coreData_namespaceObject.store).getPostType(postTypeSlug);
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    return {
      permalink: select(store_store).getPermalink(),
      isPublished: select(store_store).isCurrentPostPublished(),
      label: postType?.labels.view_item,
      hasLoaded: !!postType,
      showIconLabels: get('core', 'showIconLabels')
    };
  }, []);

  // Only render the view button if the post is published and has a permalink.
  if (!isPublished || !permalink || !hasLoaded) {
    return null;
  }
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    icon: library_external,
    label: label || (0,external_wp_i18n_namespaceObject.__)('View post'),
    href: permalink,
    target: "_blank",
    showTooltip: !showIconLabels
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/mobile.js

/**
 * WordPress dependencies
 */

const mobile = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M15 4H9c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H9c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h6c.3 0 .5.2.5.5v12zm-4.5-.5h2V16h-2v1.5z"
}));
/* harmony default export */ const library_mobile = (mobile);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/tablet.js

/**
 * WordPress dependencies
 */

const tablet = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17 4H7c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H7c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h10c.3 0 .5.2.5.5v12zm-7.5-.5h4V16h-4v1.5z"
}));
/* harmony default export */ const library_tablet = (tablet);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/desktop.js

/**
 * WordPress dependencies
 */

const desktop = (0,external_React_.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20.5 16h-.7V8c0-1.1-.9-2-2-2H6.2c-1.1 0-2 .9-2 2v8h-.7c-.8 0-1.5.7-1.5 1.5h20c0-.8-.7-1.5-1.5-1.5zM5.7 8c0-.3.2-.5.5-.5h11.6c.3 0 .5.2.5.5v7.6H5.7V8z"
}));
/* harmony default export */ const library_desktop = (desktop);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/preview-dropdown/index.js

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


function PreviewDropdown({
  forceIsAutosaveable,
  disabled
}) {
  const {
    deviceType,
    homeUrl,
    isTemplate,
    isViewable,
    showIconLabels
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getPostType$viewable;
    const {
      getDeviceType,
      getCurrentPostType
    } = select(store_store);
    const {
      getUnstableBase,
      getPostType
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    const _currentPostType = getCurrentPostType();
    return {
      deviceType: getDeviceType(),
      homeUrl: getUnstableBase()?.home,
      isTemplate: _currentPostType === 'wp_template',
      isViewable: (_getPostType$viewable = getPostType(_currentPostType)?.viewable) !== null && _getPostType$viewable !== void 0 ? _getPostType$viewable : false,
      showIconLabels: get('core', 'showIconLabels')
    };
  }, []);
  const {
    setDeviceType
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const isMobile = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  if (isMobile) return null;
  const popoverProps = {
    placement: 'bottom-end'
  };
  const toggleProps = {
    className: 'editor-preview-dropdown__toggle',
    size: 'compact',
    showTooltip: !showIconLabels,
    disabled,
    __experimentalIsFocusable: disabled
  };
  const menuProps = {
    'aria-label': (0,external_wp_i18n_namespaceObject.__)('View options')
  };
  const deviceIcons = {
    mobile: library_mobile,
    tablet: library_tablet,
    desktop: library_desktop
  };
  return (0,external_React_.createElement)(external_wp_components_namespaceObject.DropdownMenu, {
    className: "editor-preview-dropdown",
    popoverProps: popoverProps,
    toggleProps: toggleProps,
    menuProps: menuProps,
    icon: deviceIcons[deviceType.toLowerCase()],
    label: (0,external_wp_i18n_namespaceObject.__)('View'),
    disableOpenOnArrowDown: disabled
  }, ({
    onClose
  }) => (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => setDeviceType('Desktop'),
    icon: deviceType === 'Desktop' && library_check
  }, (0,external_wp_i18n_namespaceObject.__)('Desktop')), (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => setDeviceType('Tablet'),
    icon: deviceType === 'Tablet' && library_check
  }, (0,external_wp_i18n_namespaceObject.__)('Tablet')), (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => setDeviceType('Mobile'),
    icon: deviceType === 'Mobile' && library_check
  }, (0,external_wp_i18n_namespaceObject.__)('Mobile'))), isTemplate && (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuItem, {
    href: homeUrl,
    target: "_blank",
    icon: library_external,
    onClick: onClose
  }, (0,external_wp_i18n_namespaceObject.__)('View site'), (0,external_React_.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "span"
  }, /* translators: accessibility text */
  (0,external_wp_i18n_namespaceObject.__)('(opens in a new tab)')))), isViewable && (0,external_React_.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_React_.createElement)(PostPreviewButton, {
    className: "editor-preview-dropdown__button-external",
    role: "menuitem",
    forceIsAutosaveable: forceIsAutosaveable,
    textContent: (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_wp_i18n_namespaceObject.__)('Preview in new tab'), (0,external_React_.createElement)(external_wp_components_namespaceObject.Icon, {
      icon: library_external
    })),
    onPreview: onClose
  }))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/block-manager/checklist.js

/**
 * WordPress dependencies
 */


function BlockTypesChecklist({
  blockTypes,
  value,
  onItemChange
}) {
  return (0,external_React_.createElement)("ul", {
    className: "editor-block-manager__checklist"
  }, blockTypes.map(blockType => (0,external_React_.createElement)("li", {
    key: blockType.name,
    className: "editor-block-manager__checklist-item"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    label: blockType.title,
    checked: value.includes(blockType.name),
    onChange: (...args) => onItemChange(blockType.name, ...args)
  }), (0,external_React_.createElement)(external_wp_blockEditor_namespaceObject.BlockIcon, {
    icon: blockType.icon
  }))));
}
/* harmony default export */ const checklist = (BlockTypesChecklist);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/block-manager/category.js

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function BlockManagerCategory({
  title,
  blockTypes
}) {
  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(BlockManagerCategory);
  const {
    allowedBlockTypes,
    hiddenBlockTypes
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings
    } = select(store_store);
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    return {
      allowedBlockTypes: getEditorSettings().allowedBlockTypes,
      hiddenBlockTypes: get('core', 'hiddenBlockTypes')
    };
  }, []);
  const filteredBlockTypes = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (allowedBlockTypes === true) {
      return blockTypes;
    }
    return blockTypes.filter(({
      name
    }) => {
      return allowedBlockTypes?.includes(name);
    });
  }, [allowedBlockTypes, blockTypes]);
  const {
    showBlockTypes,
    hideBlockTypes
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));
  const toggleVisible = (0,external_wp_element_namespaceObject.useCallback)((blockName, nextIsChecked) => {
    if (nextIsChecked) {
      showBlockTypes(blockName);
    } else {
      hideBlockTypes(blockName);
    }
  }, [showBlockTypes, hideBlockTypes]);
  const toggleAllVisible = (0,external_wp_element_namespaceObject.useCallback)(nextIsChecked => {
    const blockNames = blockTypes.map(({
      name
    }) => name);
    if (nextIsChecked) {
      showBlockTypes(blockNames);
    } else {
      hideBlockTypes(blockNames);
    }
  }, [blockTypes, showBlockTypes, hideBlockTypes]);
  if (!filteredBlockTypes.length) {
    return null;
  }
  const checkedBlockNames = filteredBlockTypes.map(({
    name
  }) => name).filter(type => !(hiddenBlockTypes !== null && hiddenBlockTypes !== void 0 ? hiddenBlockTypes : []).includes(type));
  const titleId = 'editor-block-manager__category-title-' + instanceId;
  const isAllChecked = checkedBlockNames.length === filteredBlockTypes.length;
  const isIndeterminate = !isAllChecked && checkedBlockNames.length > 0;
  return (0,external_React_.createElement)("div", {
    role: "group",
    "aria-labelledby": titleId,
    className: "editor-block-manager__category"
  }, (0,external_React_.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    checked: isAllChecked,
    onChange: toggleAllVisible,
    className: "editor-block-manager__category-title",
    indeterminate: isIndeterminate,
    label: (0,external_React_.createElement)("span", {
      id: titleId
    }, title)
  }), (0,external_React_.createElement)(checklist, {
    blockTypes: filteredBlockTypes,
    value: checkedBlockNames,
    onItemChange: toggleVisible
  }));
}
/* harmony default export */ const block_manager_category = (BlockManagerCategory);

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/block-manager/index.js

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */



function BlockManager({
  blockTypes,
  categories,
  hasBlockSupport,
  isMatchingSearchTerm,
  numberOfHiddenBlocks,
  enableAllBlockTypes
}) {
  const debouncedSpeak = (0,external_wp_compose_namespaceObject.useDebounce)(external_wp_a11y_namespaceObject.speak, 500);
  const [search, setSearch] = (0,external_wp_element_namespaceObject.useState)('');

  // Filtering occurs here (as opposed to `withSelect`) to avoid
  // wasted renders by consequence of `Array#filter` producing
  // a new value reference on each call.
  blockTypes = blockTypes.filter(blockType => hasBlockSupport(blockType, 'inserter', true) && (!search || isMatchingSearchTerm(blockType, search)) && (!blockType.parent || blockType.parent.includes('core/post-content')));

  // Announce search results on change
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!search) {
      return;
    }
    const count = blockTypes.length;
    const resultsFoundMessage = (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %d: number of results. */
    (0,external_wp_i18n_namespaceObject._n)('%d result found.', '%d results found.', count), count);
    debouncedSpeak(resultsFoundMessage);
  }, [blockTypes.length, search, debouncedSpeak]);
  return (0,external_React_.createElement)("div", {
    className: "editor-block-manager__content"
  }, !!numberOfHiddenBlocks && (0,external_React_.createElement)("div", {
    className: "editor-block-manager__disabled-blocks-count"
  }, (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %d: number of blocks. */
  (0,external_wp_i18n_namespaceObject._n)('%d block is hidden.', '%d blocks are hidden.', numberOfHiddenBlocks), numberOfHiddenBlocks), (0,external_React_.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "link",
    onClick: () => enableAllBlockTypes(blockTypes)
  }, (0,external_wp_i18n_namespaceObject.__)('Reset'))), (0,external_React_.createElement)(external_wp_components_namespaceObject.SearchControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Search for a block'),
    placeholder: (0,external_wp_i18n_namespaceObject.__)('Search for a block'),
    value: search,
    onChange: nextSearch => setSearch(nextSearch),
    className: "editor-block-manager__search"
  }), (0,external_React_.createElement)("div", {
    tabIndex: "0",
    role: "region",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Available block types'),
    className: "editor-block-manager__results"
  }, blockTypes.length === 0 && (0,external_React_.createElement)("p", {
    className: "editor-block-manager__no-results"
  }, (0,external_wp_i18n_namespaceObject.__)('No blocks found.')), categories.map(category => (0,external_React_.createElement)(block_manager_category, {
    key: category.slug,
    title: category.title,
    blockTypes: blockTypes.filter(blockType => blockType.category === category.slug)
  })), (0,external_React_.createElement)(block_manager_category, {
    title: (0,external_wp_i18n_namespaceObject.__)('Uncategorized'),
    blockTypes: blockTypes.filter(({
      category
    }) => !category)
  })));
}
/* harmony default export */ const block_manager = ((0,external_wp_compose_namespaceObject.compose)([(0,external_wp_data_namespaceObject.withSelect)(select => {
  var _get;
  const {
    getBlockTypes,
    getCategories,
    hasBlockSupport,
    isMatchingSearchTerm
  } = select(external_wp_blocks_namespaceObject.store);
  const {
    get
  } = select(external_wp_preferences_namespaceObject.store);

  // Some hidden blocks become unregistered
  // by removing for instance the plugin that registered them, yet
  // they're still remain as hidden by the user's action.
  // We consider "hidden", blocks which were hidden and
  // are still registered.
  const blockTypes = getBlockTypes();
  const hiddenBlockTypes = ((_get = get('core', 'hiddenBlockTypes')) !== null && _get !== void 0 ? _get : []).filter(hiddenBlock => {
    return blockTypes.some(registeredBlock => registeredBlock.name === hiddenBlock);
  });
  const numberOfHiddenBlocks = Array.isArray(hiddenBlockTypes) && hiddenBlockTypes.length;
  return {
    blockTypes,
    categories: getCategories(),
    hasBlockSupport,
    isMatchingSearchTerm,
    numberOfHiddenBlocks
  };
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    showBlockTypes
  } = unlock(dispatch(store_store));
  return {
    enableAllBlockTypes: blockTypes => {
      const blockNames = blockTypes.map(({
        name
      }) => name);
      showBlockTypes(blockNames);
    }
  };
})])(BlockManager));

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/preferences-modal/index.js

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */










const {
  PreferencesModal,
  PreferencesModalTabs,
  PreferencesModalSection,
  PreferenceToggleControl
} = unlock(external_wp_preferences_namespaceObject.privateApis);
function EditorPreferencesModal({
  extraSections = {},
  isActive,
  onClose
}) {
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');
  const {
    showBlockBreadcrumbsOption
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings
    } = select(store_store);
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    const isRichEditingEnabled = getEditorSettings().richEditingEnabled;
    const isDistractionFreeEnabled = get('core', 'distractionFree');
    return {
      showBlockBreadcrumbsOption: !isDistractionFreeEnabled && isLargeViewport && isRichEditingEnabled
    };
  }, [isLargeViewport]);
  const {
    setIsListViewOpened,
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    set: setPreference
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const toggleDistractionFree = () => {
    setPreference('core', 'fixedToolbar', true);
    setIsInserterOpened(false);
    setIsListViewOpened(false);
    // Todo: Check sidebar when closing/opening distraction free.
  };
  const turnOffDistractionFree = () => {
    setPreference('core', 'distractionFree', false);
  };
  const sections = (0,external_wp_element_namespaceObject.useMemo)(() => [{
    name: 'general',
    tabLabel: (0,external_wp_i18n_namespaceObject.__)('General'),
    content: (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(PreferencesModalSection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Interface')
    }, (0,external_React_.createElement)(PreferenceToggleControl, {
      scope: "core",
      featureName: "showListViewByDefault",
      help: (0,external_wp_i18n_namespaceObject.__)('Opens the block list view sidebar by default.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Always open list view')
    }), showBlockBreadcrumbsOption && (0,external_React_.createElement)(PreferenceToggleControl, {
      scope: "core",
      featureName: "showBlockBreadcrumbs",
      help: (0,external_wp_i18n_namespaceObject.__)('Display the block hierarchy trail at the bottom of the editor.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Show block breadcrumbs')
    }), (0,external_React_.createElement)(PreferenceToggleControl, {
      scope: "core",
      featureName: "allowRightClickOverrides",
      help: (0,external_wp_i18n_namespaceObject.__)('Allows contextual list view menus via right-click, overriding browser defaults.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Allow right-click contextual menus')
    })), (0,external_React_.createElement)(PreferencesModalSection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Document settings'),
      description: (0,external_wp_i18n_namespaceObject.__)('Select what settings are shown in the document panel.')
    }, (0,external_React_.createElement)(enable_plugin_document_setting_panel.Slot, null), (0,external_React_.createElement)(post_taxonomies, {
      taxonomyWrapper: (content, taxonomy) => (0,external_React_.createElement)(enable_panel, {
        label: taxonomy.labels.menu_name,
        panelName: `taxonomy-panel-${taxonomy.slug}`
      })
    }), (0,external_React_.createElement)(post_featured_image_check, null, (0,external_React_.createElement)(enable_panel, {
      label: (0,external_wp_i18n_namespaceObject.__)('Featured image'),
      panelName: "featured-image"
    })), (0,external_React_.createElement)(post_excerpt_check, null, (0,external_React_.createElement)(enable_panel, {
      label: (0,external_wp_i18n_namespaceObject.__)('Excerpt'),
      panelName: "post-excerpt"
    })), (0,external_React_.createElement)(post_type_support_check, {
      supportKeys: ['comments', 'trackbacks']
    }, (0,external_React_.createElement)(enable_panel, {
      label: (0,external_wp_i18n_namespaceObject.__)('Discussion'),
      panelName: "discussion-panel"
    })), (0,external_React_.createElement)(page_attributes_check, null, (0,external_React_.createElement)(enable_panel, {
      label: (0,external_wp_i18n_namespaceObject.__)('Page attributes'),
      panelName: "page-attributes"
    }))), extraSections?.general)
  }, {
    name: 'appearance',
    tabLabel: (0,external_wp_i18n_namespaceObject.__)('Appearance'),
    content: (0,external_React_.createElement)(PreferencesModalSection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Appearance'),
      description: (0,external_wp_i18n_namespaceObject.__)('Customize the editor interface to suit your needs.')
    }, (0,external_React_.createElement)(PreferenceToggleControl, {
      scope: "core",
      featureName: "fixedToolbar",
      onToggle: turnOffDistractionFree,
      help: (0,external_wp_i18n_namespaceObject.__)('Access all block and document tools in a single place.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Top toolbar')
    }), (0,external_React_.createElement)(PreferenceToggleControl, {
      scope: "core",
      featureName: "distractionFree",
      onToggle: toggleDistractionFree,
      help: (0,external_wp_i18n_namespaceObject.__)('Reduce visual distractions by hiding the toolbar and other elements to focus on writing.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Distraction free')
    }), (0,external_React_.createElement)(PreferenceToggleControl, {
      scope: "core",
      featureName: "focusMode",
      help: (0,external_wp_i18n_namespaceObject.__)('Highlights the current block and fades other content.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Spotlight mode')
    }), extraSections?.appearance)
  }, {
    name: 'accessibility',
    tabLabel: (0,external_wp_i18n_namespaceObject.__)('Accessibility'),
    content: (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(PreferencesModalSection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Navigation'),
      description: (0,external_wp_i18n_namespaceObject.__)('Optimize the editing experience for enhanced control.')
    }, (0,external_React_.createElement)(PreferenceToggleControl, {
      scope: "core",
      featureName: "keepCaretInsideBlock",
      help: (0,external_wp_i18n_namespaceObject.__)('Keeps the text cursor within the block boundaries, aiding users with screen readers by preventing unintentional cursor movement outside the block.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Contain text cursor inside block')
    })), (0,external_React_.createElement)(PreferencesModalSection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Interface')
    }, (0,external_React_.createElement)(PreferenceToggleControl, {
      scope: "core",
      featureName: "showIconLabels",
      label: (0,external_wp_i18n_namespaceObject.__)('Show button text labels'),
      help: (0,external_wp_i18n_namespaceObject.__)('Show text instead of icons on buttons across the interface.')
    })))
  }, {
    name: 'blocks',
    tabLabel: (0,external_wp_i18n_namespaceObject.__)('Blocks'),
    content: (0,external_React_.createElement)(external_React_.Fragment, null, (0,external_React_.createElement)(PreferencesModalSection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Inserter')
    }, (0,external_React_.createElement)(PreferenceToggleControl, {
      scope: "core",
      featureName: "mostUsedBlocks",
      help: (0,external_wp_i18n_namespaceObject.__)('Adds a category with the most frequently used blocks in the inserter.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Show most used blocks')
    })), (0,external_React_.createElement)(PreferencesModalSection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Manage block visibility'),
      description: (0,external_wp_i18n_namespaceObject.__)("Disable blocks that you don't want to appear in the inserter. They can always be toggled back on later.")
    }, (0,external_React_.createElement)(block_manager, null)))
  }], [isLargeViewport, showBlockBreadcrumbsOption, extraSections]);
  if (!isActive) {
    return null;
  }
  return (0,external_React_.createElement)(PreferencesModal, {
    closeModal: onClose
  }, (0,external_React_.createElement)(PreferencesModalTabs, {
    sections: sections
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/private-apis.js
/**
 * Internal dependencies
 */














const privateApis = {};
lock(privateApis, {
  DocumentTools: document_tools,
  EditorCanvas: editor_canvas,
  ExperimentalEditorProvider: ExperimentalEditorProvider,
  EnablePluginDocumentSettingPanelOption: enable_plugin_document_setting_panel,
  EntitiesSavedStatesExtensible: EntitiesSavedStatesExtensible,
  InserterSidebar: InserterSidebar,
  ListViewSidebar: ListViewSidebar,
  PluginPostExcerpt: post_excerpt_plugin,
  PostPanelRow: post_panel_row,
  PostViewLink: PostViewLink,
  PreviewDropdown: PreviewDropdown,
  PreferencesModal: EditorPreferencesModal,
  // This is a temporary private API while we're updating the site editor to use EditorProvider.
  useBlockEditorSettings: use_block_editor_settings
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/index.js
/**
 * Internal dependencies
 */







/*
 * Backward compatibility
 */


})();

(window.wp = window.wp || {}).editor = __webpack_exports__;
/******/ })()
;