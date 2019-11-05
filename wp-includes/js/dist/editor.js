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
/******/ 	return __webpack_require__(__webpack_require__.s = 343);
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

/***/ 115:
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
var React = __webpack_require__(28);
var PropTypes = __webpack_require__(33);
var autosize = __webpack_require__(116);
var _getLineHeight = __webpack_require__(117);
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

/***/ 116:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	autosize 4.0.2
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

/***/ 117:
/***/ (function(module, exports, __webpack_require__) {

// Load in dependencies
var computedStyle = __webpack_require__(118);

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

/***/ 118:
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

/***/ 142:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["notices"]; }());

/***/ }),

/***/ 15:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _inherits; });

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
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _toConsumableArray; });



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

/***/ 20:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(47);


/***/ }),

/***/ 21:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

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
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectWithoutProperties; });

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

/***/ 23:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

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
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _slicedToArray; });



function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 26:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ 27:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

/***/ }),

/***/ 28:
/***/ (function(module, exports) {

(function() { module.exports = this["React"]; }());

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

/***/ 32:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["dataControls"]; }());

/***/ }),

/***/ 325:
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

/***/ 33:
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (false) { var throwOnDirectAccess, ReactIs; } else {
  // By explicitly using `prop-types` you are opting into new production behavior.
  // http://fb.me/prop-types-in-prod
  module.exports = __webpack_require__(87)();
}


/***/ }),

/***/ 34:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ 343:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var meta_namespaceObject = {};
__webpack_require__.r(meta_namespaceObject);
__webpack_require__.d(meta_namespaceObject, "getDependencies", function() { return getDependencies; });
__webpack_require__.d(meta_namespaceObject, "apply", function() { return apply; });
__webpack_require__.d(meta_namespaceObject, "update", function() { return update; });
var block_sources_namespaceObject = {};
__webpack_require__.r(block_sources_namespaceObject);
__webpack_require__.d(block_sources_namespaceObject, "meta", function() { return meta_namespaceObject; });
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "setupEditor", function() { return setupEditor; });
__webpack_require__.d(actions_namespaceObject, "__experimentalTearDownEditor", function() { return __experimentalTearDownEditor; });
__webpack_require__.d(actions_namespaceObject, "__experimentalSubscribeSources", function() { return __experimentalSubscribeSources; });
__webpack_require__.d(actions_namespaceObject, "resetPost", function() { return resetPost; });
__webpack_require__.d(actions_namespaceObject, "resetAutosave", function() { return resetAutosave; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRequestPostUpdateStart", function() { return __experimentalRequestPostUpdateStart; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRequestPostUpdateFinish", function() { return __experimentalRequestPostUpdateFinish; });
__webpack_require__.d(actions_namespaceObject, "updatePost", function() { return updatePost; });
__webpack_require__.d(actions_namespaceObject, "setupEditorState", function() { return setupEditorState; });
__webpack_require__.d(actions_namespaceObject, "editPost", function() { return actions_editPost; });
__webpack_require__.d(actions_namespaceObject, "__experimentalOptimisticUpdatePost", function() { return __experimentalOptimisticUpdatePost; });
__webpack_require__.d(actions_namespaceObject, "savePost", function() { return savePost; });
__webpack_require__.d(actions_namespaceObject, "refreshPost", function() { return refreshPost; });
__webpack_require__.d(actions_namespaceObject, "trashPost", function() { return trashPost; });
__webpack_require__.d(actions_namespaceObject, "autosave", function() { return actions_autosave; });
__webpack_require__.d(actions_namespaceObject, "__experimentalLocalAutosave", function() { return actions_experimentalLocalAutosave; });
__webpack_require__.d(actions_namespaceObject, "redo", function() { return actions_redo; });
__webpack_require__.d(actions_namespaceObject, "undo", function() { return actions_undo; });
__webpack_require__.d(actions_namespaceObject, "createUndoLevel", function() { return createUndoLevel; });
__webpack_require__.d(actions_namespaceObject, "updatePostLock", function() { return updatePostLock; });
__webpack_require__.d(actions_namespaceObject, "__experimentalFetchReusableBlocks", function() { return __experimentalFetchReusableBlocks; });
__webpack_require__.d(actions_namespaceObject, "__experimentalReceiveReusableBlocks", function() { return __experimentalReceiveReusableBlocks; });
__webpack_require__.d(actions_namespaceObject, "__experimentalSaveReusableBlock", function() { return __experimentalSaveReusableBlock; });
__webpack_require__.d(actions_namespaceObject, "__experimentalDeleteReusableBlock", function() { return __experimentalDeleteReusableBlock; });
__webpack_require__.d(actions_namespaceObject, "__experimentalUpdateReusableBlock", function() { return __experimentalUpdateReusableBlock; });
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
__webpack_require__.d(actions_namespaceObject, "replaceBlocks", function() { return actions_replaceBlocks; });
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
__webpack_require__.d(actions_namespaceObject, "removeBlocks", function() { return actions_removeBlocks; });
__webpack_require__.d(actions_namespaceObject, "removeBlock", function() { return removeBlock; });
__webpack_require__.d(actions_namespaceObject, "toggleBlockMode", function() { return toggleBlockMode; });
__webpack_require__.d(actions_namespaceObject, "startTyping", function() { return startTyping; });
__webpack_require__.d(actions_namespaceObject, "stopTyping", function() { return stopTyping; });
__webpack_require__.d(actions_namespaceObject, "enterFormattedText", function() { return enterFormattedText; });
__webpack_require__.d(actions_namespaceObject, "exitFormattedText", function() { return exitFormattedText; });
__webpack_require__.d(actions_namespaceObject, "insertDefaultBlock", function() { return insertDefaultBlock; });
__webpack_require__.d(actions_namespaceObject, "updateBlockListSettings", function() { return updateBlockListSettings; });
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
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetReusableBlocks", function() { return selectors_experimentalGetReusableBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getStateBeforeOptimisticTransaction", function() { return getStateBeforeOptimisticTransaction; });
__webpack_require__.d(selectors_namespaceObject, "isPublishingPost", function() { return selectors_isPublishingPost; });
__webpack_require__.d(selectors_namespaceObject, "isPermalinkEditable", function() { return selectors_isPermalinkEditable; });
__webpack_require__.d(selectors_namespaceObject, "getPermalink", function() { return getPermalink; });
__webpack_require__.d(selectors_namespaceObject, "getPermalinkParts", function() { return selectors_getPermalinkParts; });
__webpack_require__.d(selectors_namespaceObject, "inSomeHistory", function() { return inSomeHistory; });
__webpack_require__.d(selectors_namespaceObject, "isPostLocked", function() { return isPostLocked; });
__webpack_require__.d(selectors_namespaceObject, "isPostSavingLocked", function() { return selectors_isPostSavingLocked; });
__webpack_require__.d(selectors_namespaceObject, "isPostAutosavingLocked", function() { return isPostAutosavingLocked; });
__webpack_require__.d(selectors_namespaceObject, "isPostLockTakeover", function() { return isPostLockTakeover; });
__webpack_require__.d(selectors_namespaceObject, "getPostLockUser", function() { return getPostLockUser; });
__webpack_require__.d(selectors_namespaceObject, "getActivePostLock", function() { return getActivePostLock; });
__webpack_require__.d(selectors_namespaceObject, "canUserUseUnfilteredHTML", function() { return selectors_canUserUseUnfilteredHTML; });
__webpack_require__.d(selectors_namespaceObject, "isPublishSidebarEnabled", function() { return selectors_isPublishSidebarEnabled; });
__webpack_require__.d(selectors_namespaceObject, "getEditorBlocks", function() { return selectors_getEditorBlocks; });
__webpack_require__.d(selectors_namespaceObject, "__unstableIsEditorReady", function() { return __unstableIsEditorReady; });
__webpack_require__.d(selectors_namespaceObject, "getEditorSettings", function() { return selectors_getEditorSettings; });
__webpack_require__.d(selectors_namespaceObject, "getBlockName", function() { return selectors_getBlockName; });
__webpack_require__.d(selectors_namespaceObject, "isBlockValid", function() { return isBlockValid; });
__webpack_require__.d(selectors_namespaceObject, "getBlockAttributes", function() { return getBlockAttributes; });
__webpack_require__.d(selectors_namespaceObject, "getBlock", function() { return selectors_getBlock; });
__webpack_require__.d(selectors_namespaceObject, "getBlocks", function() { return selectors_getBlocks; });
__webpack_require__.d(selectors_namespaceObject, "__unstableGetBlockWithoutInnerBlocks", function() { return __unstableGetBlockWithoutInnerBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getClientIdsOfDescendants", function() { return getClientIdsOfDescendants; });
__webpack_require__.d(selectors_namespaceObject, "getClientIdsWithDescendants", function() { return getClientIdsWithDescendants; });
__webpack_require__.d(selectors_namespaceObject, "getGlobalBlockCount", function() { return getGlobalBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "getBlocksByClientId", function() { return selectors_getBlocksByClientId; });
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
__webpack_require__.d(selectors_namespaceObject, "isTyping", function() { return isTyping; });
__webpack_require__.d(selectors_namespaceObject, "isCaretWithinFormattedText", function() { return isCaretWithinFormattedText; });
__webpack_require__.d(selectors_namespaceObject, "getBlockInsertionPoint", function() { return getBlockInsertionPoint; });
__webpack_require__.d(selectors_namespaceObject, "isBlockInsertionPointVisible", function() { return isBlockInsertionPointVisible; });
__webpack_require__.d(selectors_namespaceObject, "isValidTemplate", function() { return isValidTemplate; });
__webpack_require__.d(selectors_namespaceObject, "getTemplate", function() { return getTemplate; });
__webpack_require__.d(selectors_namespaceObject, "getTemplateLock", function() { return getTemplateLock; });
__webpack_require__.d(selectors_namespaceObject, "canInsertBlockType", function() { return selectors_canInsertBlockType; });
__webpack_require__.d(selectors_namespaceObject, "getInserterItems", function() { return selectors_getInserterItems; });
__webpack_require__.d(selectors_namespaceObject, "hasInserterItems", function() { return hasInserterItems; });
__webpack_require__.d(selectors_namespaceObject, "getBlockListSettings", function() { return getBlockListSettings; });
var store_selectors_namespaceObject = {};
__webpack_require__.r(store_selectors_namespaceObject);
__webpack_require__.d(store_selectors_namespaceObject, "isRequestingDownloadableBlocks", function() { return isRequestingDownloadableBlocks; });
__webpack_require__.d(store_selectors_namespaceObject, "getDownloadableBlocks", function() { return selectors_getDownloadableBlocks; });
__webpack_require__.d(store_selectors_namespaceObject, "hasInstallBlocksPermission", function() { return selectors_hasInstallBlocksPermission; });
__webpack_require__.d(store_selectors_namespaceObject, "getInstalledBlockTypes", function() { return selectors_getInstalledBlockTypes; });
var store_actions_namespaceObject = {};
__webpack_require__.r(store_actions_namespaceObject);
__webpack_require__.d(store_actions_namespaceObject, "fetchDownloadableBlocks", function() { return fetchDownloadableBlocks; });
__webpack_require__.d(store_actions_namespaceObject, "receiveDownloadableBlocks", function() { return receiveDownloadableBlocks; });
__webpack_require__.d(store_actions_namespaceObject, "setInstallBlocksPermission", function() { return setInstallBlocksPermission; });
__webpack_require__.d(store_actions_namespaceObject, "downloadBlock", function() { return actions_downloadBlock; });
__webpack_require__.d(store_actions_namespaceObject, "installBlock", function() { return actions_installBlock; });
__webpack_require__.d(store_actions_namespaceObject, "uninstallBlock", function() { return uninstallBlock; });
__webpack_require__.d(store_actions_namespaceObject, "addInstalledBlockType", function() { return addInstalledBlockType; });
__webpack_require__.d(store_actions_namespaceObject, "removeInstalledBlockType", function() { return removeInstalledBlockType; });

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(9);

// EXTERNAL MODULE: external {"this":["wp","coreData"]}
var external_this_wp_coreData_ = __webpack_require__(89);

// EXTERNAL MODULE: external {"this":["wp","notices"]}
var external_this_wp_notices_ = __webpack_require__(142);

// EXTERNAL MODULE: external {"this":["wp","nux"]}
var external_this_wp_nux_ = __webpack_require__(61);

// EXTERNAL MODULE: external {"this":["wp","richText"]}
var external_this_wp_richText_ = __webpack_require__(22);

// EXTERNAL MODULE: external {"this":["wp","viewport"]}
var external_this_wp_viewport_ = __webpack_require__(42);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","dataControls"]}
var external_this_wp_dataControls_ = __webpack_require__(32);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/typeof.js
var esm_typeof = __webpack_require__(31);

// EXTERNAL MODULE: ./node_modules/redux-optimist/index.js
var redux_optimist = __webpack_require__(83);
var redux_optimist_default = /*#__PURE__*/__webpack_require__.n(redux_optimist);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/defaults.js


/**
 * WordPress dependencies
 */

var PREFERENCES_DEFAULTS = {
  insertUsage: {},
  // Should be kept for backward compatibility, see: https://github.com/WordPress/gutenberg/issues/14580.
  isPublishSidebarEnabled: true
};
/**
 * The default post editor settings
 *
 *  allowedBlockTypes  boolean|Array Allowed block types
 *  richEditingEnabled boolean       Whether rich editing is enabled or not
 *  codeEditingEnabled boolean       Whether code editing is enabled or not
 *  enableCustomFields boolean       Whether the WordPress custom fields are enabled or not
 *  autosaveInterval   number        Autosave Interval
 *  availableTemplates array?        The available post templates
 *  disablePostFormats boolean       Whether or not the post formats are disabled
 *  allowedMimeTypes   array?        List of allowed mime types and file extensions
 *  maxUploadFileSize  number        Maximum upload file size
 */

var EDITOR_SETTINGS_DEFAULTS = Object(objectSpread["a" /* default */])({}, external_this_wp_blockEditor_["SETTINGS_DEFAULTS"], {
  richEditingEnabled: true,
  codeEditingEnabled: true,
  enableCustomFields: false
});

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

function shouldOverwriteState(action, previousAction) {
  if (action.type === 'RESET_EDITOR_BLOCKS') {
    return !action.shouldCreateUndoLevel;
  }

  if (!previousAction || action.type !== previousAction.type) {
    return false;
  }

  return isUpdatingSamePostProperty(action, previousAction);
}
function reducer_postId() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SETUP_EDITOR_STATE':
    case 'RESET_POST':
    case 'UPDATE_POST':
      return action.post.id;
  }

  return state;
}
function reducer_postType() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SETUP_EDITOR_STATE':
    case 'RESET_POST':
    case 'UPDATE_POST':
      return action.post.type;
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
    case 'REQUEST_POST_UPDATE_FINISH':
      return {
        pending: action.type === 'REQUEST_POST_UPDATE_START',
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
/**
 * Post autosaving lock.
 *
 * When post autosaving is locked, the post will not autosave.
 *
 * @param {PostAutosavingLockState} state  Current state.
 * @param {Object}                  action Dispatched action.
 *
 * @return {PostLockState} Updated state.
 */

function postAutosavingLock() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'LOCK_POST_AUTOSAVING':
      return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.lockName, true));

    case 'UNLOCK_POST_AUTOSAVING':
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
          return Object(objectSpread["a" /* default */])({}, state, Object(external_lodash_["keyBy"])(action.results, 'id'));
        }

      case 'UPDATE_REUSABLE_BLOCK':
        {
          var id = action.id,
              changes = action.changes;
          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, id, Object(objectSpread["a" /* default */])({}, state[id], changes)));
        }

      case 'SAVE_REUSABLE_BLOCK_SUCCESS':
        {
          var _id = action.id,
              updatedId = action.updatedId; // If a temporary reusable block is saved, we swap the temporary id with the final one

          if (_id === updatedId) {
            return state;
          }

          var value = state[_id];
          return Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(state, _id), Object(defineProperty["a" /* default */])({}, updatedId, Object(objectSpread["a" /* default */])({}, value, {
            id: updatedId
          })));
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

    case 'TEAR_DOWN_EDITOR':
      return false;
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
/* harmony default export */ var reducer = (redux_optimist_default()(Object(external_this_wp_data_["combineReducers"])({
  postId: reducer_postId,
  postType: reducer_postType,
  preferences: preferences,
  saving: saving,
  postLock: postLock,
  reusableBlocks: reducer_reusableBlocks,
  template: reducer_template,
  postSavingLock: postSavingLock,
  isReady: reducer_isReady,
  editorSettings: reducer_editorSettings,
  postAutosavingLock: postAutosavingLock
})));

// EXTERNAL MODULE: ./node_modules/refx/refx.js
var refx = __webpack_require__(72);
var refx_default = /*#__PURE__*/__webpack_require__.n(refx);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/regenerator/index.js
var regenerator = __webpack_require__(20);
var regenerator_default = /*#__PURE__*/__webpack_require__.n(regenerator);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js
var asyncToGenerator = __webpack_require__(43);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(34);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(23);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(17);

// EXTERNAL MODULE: external {"this":["wp","deprecated"]}
var external_this_wp_deprecated_ = __webpack_require__(37);
var external_this_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_deprecated_);

// EXTERNAL MODULE: external {"this":["wp","isShallowEqual"]}
var external_this_wp_isShallowEqual_ = __webpack_require__(41);
var external_this_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_isShallowEqual_);

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
 *
 * @type {string}
 */

var STORE_KEY = 'core/editor';
var POST_UPDATE_TRANSACTION_ID = 'post-update';
var SAVE_POST_NOTICE_ID = 'SAVE_POST_NOTICE_ID';
var TRASH_POST_NOTICE_ID = 'TRASH_POST_NOTICE_ID';
var PERMALINK_POSTNAME_REGEX = /%(?:postname|pagename)%/;
var ONE_MINUTE_IN_MS = 60 * 1000;
var AUTOSAVE_PROPERTIES = ['title', 'excerpt', 'content'];

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
      type: 'snackbar',
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
    publish: Object(external_this_wp_i18n_["__"])('Publishing failed.'),
    private: Object(external_this_wp_i18n_["__"])('Publishing failed.'),
    future: Object(external_this_wp_i18n_["__"])('Scheduling failed.')
  };
  var noticeMessage = !isPublished && publishStatus.indexOf(edits.status) !== -1 ? messages[edits.status] : Object(external_this_wp_i18n_["__"])('Updating failed.'); // Check if message string contains HTML. Notice text is currently only
  // supported as plaintext, and stripping the tags may muddle the meaning.

  if (error.message && !/<\/?[^>]*>/.test(error.message)) {
    noticeMessage = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('%1$s Error message: %2$s'), noticeMessage, error.message);
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
  return [data.error.message && data.error.code !== 'unknown_error' ? data.error.message : Object(external_this_wp_i18n_["__"])('Trashing failed'), {
    id: TRASH_POST_NOTICE_ID
  }];
}

// EXTERNAL MODULE: ./node_modules/memize/index.js
var memize = __webpack_require__(44);
var memize_default = /*#__PURE__*/__webpack_require__.n(memize);

// EXTERNAL MODULE: external {"this":["wp","autop"]}
var external_this_wp_autop_ = __webpack_require__(69);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/utils/serialize-blocks.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Serializes blocks following backwards compatibility conventions.
 *
 * @param {Array} blocksForSerialization The blocks to serialize.
 *
 * @return {string} The blocks serialization.
 */

var serializeBlocks = memize_default()(function (blocksForSerialization) {
  // A single unmodified default block is assumed to
  // be equivalent to an empty post.
  if (blocksForSerialization.length === 1 && Object(external_this_wp_blocks_["isUnmodifiedDefaultBlock"])(blocksForSerialization[0])) {
    blocksForSerialization = [];
  }

  var content = Object(external_this_wp_blocks_["serialize"])(blocksForSerialization); // For compatibility, treat a post consisting of a
  // single freeform block as legacy content and apply
  // pre-block-editor removep'd content formatting.

  if (blocksForSerialization.length === 1 && blocksForSerialization[0].name === Object(external_this_wp_blocks_["getFreeformContentHandlerName"])()) {
    content = Object(external_this_wp_autop_["removep"])(content);
  }

  return content;
}, {
  maxSize: 1
});
/* harmony default export */ var serialize_blocks = (serializeBlocks);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/controls.js
/**
 * WordPress dependencies
 */

/**
 * Returns a control descriptor signalling to subscribe to the registry and
 * resolve the control promise only when the next state change occurs.
 *
 * @return {Object} Control descriptor.
 */

function awaitNextStateChange() {
  return {
    type: 'AWAIT_NEXT_STATE_CHANGE'
  };
}
/**
 * Returns a control descriptor signalling to resolve with the current data
 * registry.
 *
 * @return {Object} Control descriptor.
 */

function getRegistry() {
  return {
    type: 'GET_REGISTRY'
  };
}
/**
 * Function returning a sessionStorage key to set or retrieve a given post's
 * automatic session backup.
 *
 * Keys are crucially prefixed with 'wp-autosave-' so that wp-login.php's
 * `loggedout` handler can clear sessionStorage of any user-private content.
 *
 * @see https://github.com/WordPress/wordpress-develop/blob/6dad32d2aed47e6c0cf2aee8410645f6d7aba6bd/src/wp-login.php#L103
 *
 * @param {string} postId  Post ID.
 * @return {string}        sessionStorage key
 */

function postKey(postId) {
  return "wp-autosave-block-editor-post-".concat(postId);
}

function localAutosaveGet(postId) {
  return window.sessionStorage.getItem(postKey(postId));
}
function localAutosaveSet(postId, title, content, excerpt) {
  window.sessionStorage.setItem(postKey(postId), JSON.stringify({
    post_title: title,
    content: content,
    excerpt: excerpt
  }));
}
function localAutosaveClear(postId) {
  window.sessionStorage.removeItem(postKey(postId));
}
var controls = {
  AWAIT_NEXT_STATE_CHANGE: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function () {
      return new Promise(function (resolve) {
        var unsubscribe = registry.subscribe(function () {
          unsubscribe();
          resolve();
        });
      });
    };
  }),
  GET_REGISTRY: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function () {
      return registry;
    };
  }),
  LOCAL_AUTOSAVE_SET: function LOCAL_AUTOSAVE_SET(_ref) {
    var postId = _ref.postId,
        title = _ref.title,
        content = _ref.content,
        excerpt = _ref.excerpt;
    localAutosaveSet(postId, title, content, excerpt);
  }
};
/* harmony default export */ var store_controls = (controls);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/block-sources/meta.js



var _marked =
/*#__PURE__*/
regenerator_default.a.mark(getDependencies),
    _marked2 =
/*#__PURE__*/
regenerator_default.a.mark(update);

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Store control invoked upon a state change, responsible for returning an
 * object of dependencies. When a change in dependencies occurs (by shallow
 * equality of the returned object), blocks are reset to apply the new sourced
 * value.
 *
 * @yield {Object} Optional yielded controls.
 *
 * @return {Object} Dependencies as object.
 */

function getDependencies() {
  return regenerator_default.a.wrap(function getDependencies$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.next = 2;
          return Object(external_this_wp_dataControls_["select"])('core/editor', 'getEditedPostAttribute', 'meta');

        case 2:
          _context.t0 = _context.sent;
          return _context.abrupt("return", {
            meta: _context.t0
          });

        case 4:
        case "end":
          return _context.stop();
      }
    }
  }, _marked);
}
/**
 * Given an attribute schema and dependencies data, returns a source value.
 *
 * @param {Object} schema            Block type attribute schema.
 * @param {Object} dependencies      Source dependencies.
 * @param {Object} dependencies.meta Post meta.
 *
 * @return {Object} Block attribute value.
 */

function apply(schema, _ref) {
  var meta = _ref.meta;
  return meta[schema.meta];
}
/**
 * Store control invoked upon a block attributes update, responsible for
 * reflecting an update in a meta value.
 *
 * @param {Object} schema Block type attribute schema.
 * @param {*}      value  Updated block attribute value.
 *
 * @yield {Object} Yielded action objects or store controls.
 */

function update(schema, value) {
  return regenerator_default.a.wrap(function update$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.next = 2;
          return actions_editPost({
            meta: Object(defineProperty["a" /* default */])({}, schema.meta, value)
          });

        case 2:
        case "end":
          return _context2.stop();
      }
    }
  }, _marked2);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/block-sources/index.js
/**
 * Internal dependencies
 */



// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/actions.js






var actions_marked =
/*#__PURE__*/
regenerator_default.a.mark(getBlocksWithSourcedAttributes),
    actions_marked2 =
/*#__PURE__*/
regenerator_default.a.mark(resetLastBlockSourceDependencies),
    _marked3 =
/*#__PURE__*/
regenerator_default.a.mark(setupEditor),
    _marked4 =
/*#__PURE__*/
regenerator_default.a.mark(__experimentalSubscribeSources),
    _marked5 =
/*#__PURE__*/
regenerator_default.a.mark(resetAutosave),
    _marked6 =
/*#__PURE__*/
regenerator_default.a.mark(actions_editPost),
    _marked7 =
/*#__PURE__*/
regenerator_default.a.mark(savePost),
    _marked8 =
/*#__PURE__*/
regenerator_default.a.mark(refreshPost),
    _marked9 =
/*#__PURE__*/
regenerator_default.a.mark(trashPost),
    _marked10 =
/*#__PURE__*/
regenerator_default.a.mark(actions_autosave),
    _marked11 =
/*#__PURE__*/
regenerator_default.a.mark(actions_experimentalLocalAutosave),
    _marked12 =
/*#__PURE__*/
regenerator_default.a.mark(actions_redo),
    _marked13 =
/*#__PURE__*/
regenerator_default.a.mark(actions_undo),
    _marked14 =
/*#__PURE__*/
regenerator_default.a.mark(actions_resetEditorBlocks);

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
 * Map of Registry instance to WeakMap of dependencies by custom source.
 *
 * @type WeakMap<WPDataRegistry,WeakMap<WPBlockAttributeSource,Object>>
 */

var lastBlockSourceDependenciesByRegistry = new WeakMap();
/**
 * Given a blocks array, returns a blocks array with sourced attribute values
 * applied. The reference will remain consistent with the original argument if
 * no attribute values must be overridden. If sourced values are applied, the
 * return value will be a modified copy of the original array.
 *
 * @param {WPBlock[]} blocks Original blocks array.
 *
 * @return {WPBlock[]} Blocks array with sourced values applied.
 */

function getBlocksWithSourcedAttributes(blocks) {
  var registry, blockSourceDependencies, workingBlocks, i, block, blockType, _i, _Object$entries, _Object$entries$_i, attributeName, schema, dependencies, sourcedAttributeValue, appliedInnerBlocks;

  return regenerator_default.a.wrap(function getBlocksWithSourcedAttributes$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.next = 2;
          return getRegistry();

        case 2:
          registry = _context.sent;

          if (lastBlockSourceDependenciesByRegistry.has(registry)) {
            _context.next = 5;
            break;
          }

          return _context.abrupt("return", blocks);

        case 5:
          blockSourceDependencies = lastBlockSourceDependenciesByRegistry.get(registry);
          workingBlocks = blocks;
          i = 0;

        case 8:
          if (!(i < blocks.length)) {
            _context.next = 37;
            break;
          }

          block = blocks[i];
          _context.next = 12;
          return Object(external_this_wp_dataControls_["select"])('core/blocks', 'getBlockType', block.name);

        case 12:
          blockType = _context.sent;
          _i = 0, _Object$entries = Object.entries(blockType.attributes);

        case 14:
          if (!(_i < _Object$entries.length)) {
            _context.next = 30;
            break;
          }

          _Object$entries$_i = Object(slicedToArray["a" /* default */])(_Object$entries[_i], 2), attributeName = _Object$entries$_i[0], schema = _Object$entries$_i[1];

          if (!(!block_sources_namespaceObject[schema.source] || !block_sources_namespaceObject[schema.source].apply)) {
            _context.next = 18;
            break;
          }

          return _context.abrupt("continue", 27);

        case 18:
          if (blockSourceDependencies.has(block_sources_namespaceObject[schema.source])) {
            _context.next = 20;
            break;
          }

          return _context.abrupt("continue", 27);

        case 20:
          dependencies = blockSourceDependencies.get(block_sources_namespaceObject[schema.source]);
          sourcedAttributeValue = block_sources_namespaceObject[schema.source].apply(schema, dependencies); // It's only necessary to apply the value if it differs from the
          // block's locally-assigned value, to avoid needlessly resetting
          // the block editor.

          if (!(sourcedAttributeValue === block.attributes[attributeName])) {
            _context.next = 24;
            break;
          }

          return _context.abrupt("continue", 27);

        case 24:
          // Create a shallow clone to mutate, leaving the original intact.
          if (workingBlocks === blocks) {
            workingBlocks = Object(toConsumableArray["a" /* default */])(workingBlocks);
          }

          block = Object(objectSpread["a" /* default */])({}, block, {
            attributes: Object(objectSpread["a" /* default */])({}, block.attributes, Object(defineProperty["a" /* default */])({}, attributeName, sourcedAttributeValue))
          });
          workingBlocks.splice(i, 1, block);

        case 27:
          _i++;
          _context.next = 14;
          break;

        case 30:
          if (!block.innerBlocks.length) {
            _context.next = 34;
            break;
          }

          return _context.delegateYield(getBlocksWithSourcedAttributes(block.innerBlocks), "t0", 32);

        case 32:
          appliedInnerBlocks = _context.t0;

          if (appliedInnerBlocks !== block.innerBlocks) {
            if (workingBlocks === blocks) {
              workingBlocks = Object(toConsumableArray["a" /* default */])(workingBlocks);
            }

            block = Object(objectSpread["a" /* default */])({}, block, {
              innerBlocks: appliedInnerBlocks
            });
            workingBlocks.splice(i, 1, block);
          }

        case 34:
          i++;
          _context.next = 8;
          break;

        case 37:
          return _context.abrupt("return", workingBlocks);

        case 38:
        case "end":
          return _context.stop();
      }
    }
  }, actions_marked);
}
/**
 * Refreshes the last block source dependencies, optionally for a given subset
 * of sources (defaults to the full set of sources).
 *
 * @param {?Array} sourcesToUpdate Optional subset of sources to reset.
 *
 * @yield {Object} Yielded actions or control descriptors.
 */


function resetLastBlockSourceDependencies() {
  var sourcesToUpdate,
      registry,
      lastBlockSourceDependencies,
      _iteratorNormalCompletion,
      _didIteratorError,
      _iteratorError,
      _iterator,
      _step,
      source,
      dependencies,
      _args2 = arguments;

  return regenerator_default.a.wrap(function resetLastBlockSourceDependencies$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          sourcesToUpdate = _args2.length > 0 && _args2[0] !== undefined ? _args2[0] : Object.values(block_sources_namespaceObject);

          if (sourcesToUpdate.length) {
            _context2.next = 3;
            break;
          }

          return _context2.abrupt("return");

        case 3:
          _context2.next = 5;
          return getRegistry();

        case 5:
          registry = _context2.sent;

          if (!lastBlockSourceDependenciesByRegistry.has(registry)) {
            lastBlockSourceDependenciesByRegistry.set(registry, new WeakMap());
          }

          lastBlockSourceDependencies = lastBlockSourceDependenciesByRegistry.get(registry);
          _iteratorNormalCompletion = true;
          _didIteratorError = false;
          _iteratorError = undefined;
          _context2.prev = 11;
          _iterator = sourcesToUpdate[Symbol.iterator]();

        case 13:
          if (_iteratorNormalCompletion = (_step = _iterator.next()).done) {
            _context2.next = 21;
            break;
          }

          source = _step.value;
          return _context2.delegateYield(source.getDependencies(), "t0", 16);

        case 16:
          dependencies = _context2.t0;
          lastBlockSourceDependencies.set(source, dependencies);

        case 18:
          _iteratorNormalCompletion = true;
          _context2.next = 13;
          break;

        case 21:
          _context2.next = 27;
          break;

        case 23:
          _context2.prev = 23;
          _context2.t1 = _context2["catch"](11);
          _didIteratorError = true;
          _iteratorError = _context2.t1;

        case 27:
          _context2.prev = 27;
          _context2.prev = 28;

          if (!_iteratorNormalCompletion && _iterator.return != null) {
            _iterator.return();
          }

        case 30:
          _context2.prev = 30;

          if (!_didIteratorError) {
            _context2.next = 33;
            break;
          }

          throw _iteratorError;

        case 33:
          return _context2.finish(30);

        case 34:
          return _context2.finish(27);

        case 35:
        case "end":
          return _context2.stop();
      }
    }
  }, actions_marked2, null, [[11, 23, 27, 35], [28,, 30, 34]]);
}
/**
 * Returns an action generator used in signalling that editor has initialized with
 * the specified post object and editor settings.
 *
 * @param {Object} post      Post object.
 * @param {Object} edits     Initial edited attributes object.
 * @param {Array?} template  Block Template.
 */


function setupEditor(post, edits, template) {
  var content, blocks, isNewPost;
  return regenerator_default.a.wrap(function setupEditor$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          // In order to ensure maximum of a single parse during setup, edits are
          // included as part of editor setup action. Assume edited content as
          // canonical if provided, falling back to post.
          if (Object(external_lodash_["has"])(edits, ['content'])) {
            content = edits.content;
          } else {
            content = post.content.raw;
          }

          blocks = Object(external_this_wp_blocks_["parse"])(content); // Apply a template for new posts only, if exists.

          isNewPost = post.status === 'auto-draft';

          if (isNewPost && template) {
            blocks = Object(external_this_wp_blocks_["synchronizeBlocksWithTemplate"])(blocks, template);
          }

          _context3.next = 6;
          return resetPost(post);

        case 6:
          return _context3.delegateYield(resetLastBlockSourceDependencies(), "t0", 7);

        case 7:
          _context3.next = 9;
          return {
            type: 'SETUP_EDITOR',
            post: post,
            edits: edits,
            template: template
          };

        case 9:
          _context3.next = 11;
          return actions_resetEditorBlocks(blocks, {
            __unstableShouldCreateUndoLevel: false
          });

        case 11:
          _context3.next = 13;
          return setupEditorState(post);

        case 13:
          if (!(edits && Object.keys(edits).some(function (key) {
            return edits[key] !== (Object(external_lodash_["has"])(post, [key, 'raw']) ? post[key].raw : post[key]);
          }))) {
            _context3.next = 16;
            break;
          }

          _context3.next = 16;
          return actions_editPost(edits);

        case 16:
          return _context3.delegateYield(__experimentalSubscribeSources(), "t1", 17);

        case 17:
        case "end":
          return _context3.stop();
      }
    }
  }, _marked3);
}
/**
 * Returns an action object signalling that the editor is being destroyed and
 * that any necessary state or side-effect cleanup should occur.
 *
 * @return {Object} Action object.
 */

function __experimentalTearDownEditor() {
  return {
    type: 'TEAR_DOWN_EDITOR'
  };
}
/**
 * Returns an action generator which loops to await the next state change,
 * calling to reset blocks when a block source dependencies change.
 *
 * @yield {Object} Action object.
 */

function __experimentalSubscribeSources() {
  var isStillReady, registry, reset, _i2, _Object$values, source, dependencies, lastBlockSourceDependencies, lastDependencies;

  return regenerator_default.a.wrap(function __experimentalSubscribeSources$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          if (false) {}

          _context4.next = 3;
          return awaitNextStateChange();

        case 3:
          _context4.next = 5;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, '__unstableIsEditorReady');

        case 5:
          isStillReady = _context4.sent;

          if (isStillReady) {
            _context4.next = 8;
            break;
          }

          return _context4.abrupt("break", 36);

        case 8:
          _context4.next = 10;
          return getRegistry();

        case 10:
          registry = _context4.sent;
          reset = false;
          _i2 = 0, _Object$values = Object.values(block_sources_namespaceObject);

        case 13:
          if (!(_i2 < _Object$values.length)) {
            _context4.next = 26;
            break;
          }

          source = _Object$values[_i2];

          if (source.getDependencies) {
            _context4.next = 17;
            break;
          }

          return _context4.abrupt("continue", 23);

        case 17:
          return _context4.delegateYield(source.getDependencies(), "t0", 18);

        case 18:
          dependencies = _context4.t0;

          if (!lastBlockSourceDependenciesByRegistry.has(registry)) {
            lastBlockSourceDependenciesByRegistry.set(registry, new WeakMap());
          }

          lastBlockSourceDependencies = lastBlockSourceDependenciesByRegistry.get(registry);
          lastDependencies = lastBlockSourceDependencies.get(source);

          if (!external_this_wp_isShallowEqual_default()(dependencies, lastDependencies)) {
            lastBlockSourceDependencies.set(source, dependencies); // Allow the loop to continue in order to assign latest
            // dependencies values, but mark for reset.

            reset = true;
          }

        case 23:
          _i2++;
          _context4.next = 13;
          break;

        case 26:
          if (!reset) {
            _context4.next = 34;
            break;
          }

          _context4.t1 = actions_resetEditorBlocks;
          _context4.next = 30;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getEditorBlocks');

        case 30:
          _context4.t2 = _context4.sent;
          _context4.t3 = {
            __unstableShouldCreateUndoLevel: false
          };
          _context4.next = 34;
          return (0, _context4.t1)(_context4.t2, _context4.t3);

        case 34:
          _context4.next = 0;
          break;

        case 36:
        case "end":
          return _context4.stop();
      }
    }
  }, _marked4);
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
 * @deprecated since 5.6. Callers should use the `receiveAutosaves( postId, autosave )`
 * 			   selector from the '@wordpress/core-data' package.
 *
 * @param {Object} newAutosave Autosave post object.
 *
 * @return {Object} Action object.
 */

function resetAutosave(newAutosave) {
  var postId;
  return regenerator_default.a.wrap(function resetAutosave$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          external_this_wp_deprecated_default()('resetAutosave action (`core/editor` store)', {
            alternative: 'receiveAutosaves action (`core` store)',
            plugin: 'Gutenberg'
          });
          _context5.next = 3;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPostId');

        case 3:
          postId = _context5.sent;
          _context5.next = 6;
          return Object(external_this_wp_dataControls_["dispatch"])('core', 'receiveAutosaves', postId, newAutosave);

        case 6:
          return _context5.abrupt("return", {
            type: '__INERT__'
          });

        case 7:
        case "end":
          return _context5.stop();
      }
    }
  }, _marked5);
}
/**
 * Action for dispatching that a post update request has started.
 *
 * @param {Object} options
 *
 * @return {Object} An action object
 */

function __experimentalRequestPostUpdateStart() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return {
    type: 'REQUEST_POST_UPDATE_START',
    options: options
  };
}
/**
 * Action for dispatching that a post update request has finished.
 *
 * @param {Object} options
 *
 * @return {Object} An action object
 */

function __experimentalRequestPostUpdateFinish() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return {
    type: 'REQUEST_POST_UPDATE_FINISH',
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
 * @param {Object} edits   Post attributes to edit.
 * @param {Object} options Options for the edit.
 *
 * @yield {Object} Action object or control.
 */

function actions_editPost(edits, options) {
  var _ref, id, type;

  return regenerator_default.a.wrap(function editPost$(_context6) {
    while (1) {
      switch (_context6.prev = _context6.next) {
        case 0:
          _context6.next = 2;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPost');

        case 2:
          _ref = _context6.sent;
          id = _ref.id;
          type = _ref.type;
          _context6.next = 7;
          return Object(external_this_wp_dataControls_["dispatch"])('core', 'editEntityRecord', 'postType', type, id, edits, options);

        case 7:
        case "end":
          return _context6.stop();
      }
    }
  }, _marked6);
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
      edits,
      previousRecord,
      error,
      args,
      updatedRecord,
      _args7,
      _args8 = arguments;

  return regenerator_default.a.wrap(function savePost$(_context7) {
    while (1) {
      switch (_context7.prev = _context7.next) {
        case 0:
          options = _args8.length > 0 && _args8[0] !== undefined ? _args8[0] : {};
          _context7.next = 3;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'isEditedPostSaveable');

        case 3:
          if (_context7.sent) {
            _context7.next = 5;
            break;
          }

          return _context7.abrupt("return");

        case 5:
          _context7.next = 7;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getEditedPostContent');

        case 7:
          _context7.t0 = _context7.sent;
          edits = {
            content: _context7.t0
          };

          if (options.isAutosave) {
            _context7.next = 12;
            break;
          }

          _context7.next = 12;
          return Object(external_this_wp_dataControls_["dispatch"])(STORE_KEY, 'editPost', edits, {
            undoIgnore: true
          });

        case 12:
          _context7.next = 14;
          return __experimentalRequestPostUpdateStart(options);

        case 14:
          _context7.next = 16;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPost');

        case 16:
          previousRecord = _context7.sent;
          _context7.t1 = objectSpread["a" /* default */];
          _context7.t2 = {
            id: previousRecord.id
          };
          _context7.next = 21;
          return Object(external_this_wp_dataControls_["select"])('core', 'getEntityRecordNonTransientEdits', 'postType', previousRecord.type, previousRecord.id);

        case 21:
          _context7.t3 = _context7.sent;
          _context7.t4 = edits;
          edits = (0, _context7.t1)(_context7.t2, _context7.t3, _context7.t4);
          _context7.next = 26;
          return Object(external_this_wp_dataControls_["dispatch"])('core', 'saveEntityRecord', 'postType', previousRecord.type, edits, options);

        case 26:
          _context7.next = 28;
          return __experimentalRequestPostUpdateFinish(options);

        case 28:
          _context7.next = 30;
          return Object(external_this_wp_dataControls_["select"])('core', 'getLastEntitySaveError', 'postType', previousRecord.type, previousRecord.id);

        case 30:
          error = _context7.sent;

          if (!error) {
            _context7.next = 38;
            break;
          }

          args = getNotificationArgumentsForSaveFail({
            post: previousRecord,
            edits: edits,
            error: error
          });

          if (!args.length) {
            _context7.next = 36;
            break;
          }

          _context7.next = 36;
          return external_this_wp_dataControls_["dispatch"].apply(void 0, ['core/notices', 'createErrorNotice'].concat(Object(toConsumableArray["a" /* default */])(args)));

        case 36:
          _context7.next = 56;
          break;

        case 38:
          _context7.next = 40;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPost');

        case 40:
          updatedRecord = _context7.sent;
          _context7.t5 = getNotificationArgumentsForSaveSuccess;
          _context7.t6 = previousRecord;
          _context7.t7 = updatedRecord;
          _context7.next = 46;
          return Object(external_this_wp_dataControls_["select"])('core', 'getPostType', updatedRecord.type);

        case 46:
          _context7.t8 = _context7.sent;
          _context7.t9 = options;
          _context7.t10 = {
            previousPost: _context7.t6,
            post: _context7.t7,
            postType: _context7.t8,
            options: _context7.t9
          };
          _args7 = (0, _context7.t5)(_context7.t10);

          if (!_args7.length) {
            _context7.next = 53;
            break;
          }

          _context7.next = 53;
          return external_this_wp_dataControls_["dispatch"].apply(void 0, ['core/notices', 'createSuccessNotice'].concat(Object(toConsumableArray["a" /* default */])(_args7)));

        case 53:
          if (options.isAutosave) {
            _context7.next = 56;
            break;
          }

          _context7.next = 56;
          return Object(external_this_wp_dataControls_["dispatch"])('core/block-editor', '__unstableMarkLastChangeAsPersistent');

        case 56:
        case "end":
          return _context7.stop();
      }
    }
  }, _marked7);
}
/**
 * Action generator for handling refreshing the current post.
 */

function refreshPost() {
  var post, postTypeSlug, postType, newPost;
  return regenerator_default.a.wrap(function refreshPost$(_context8) {
    while (1) {
      switch (_context8.prev = _context8.next) {
        case 0:
          _context8.next = 2;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPost');

        case 2:
          post = _context8.sent;
          _context8.next = 5;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPostType');

        case 5:
          postTypeSlug = _context8.sent;
          _context8.next = 8;
          return Object(external_this_wp_dataControls_["select"])('core', 'getPostType', postTypeSlug);

        case 8:
          postType = _context8.sent;
          _context8.next = 11;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            // Timestamp arg allows caller to bypass browser caching, which is
            // expected for this specific function.
            path: "/wp/v2/".concat(postType.rest_base, "/").concat(post.id) + "?context=edit&_timestamp=".concat(Date.now())
          });

        case 11:
          newPost = _context8.sent;
          _context8.next = 14;
          return Object(external_this_wp_dataControls_["dispatch"])(STORE_KEY, 'resetPost', newPost);

        case 14:
        case "end":
          return _context8.stop();
      }
    }
  }, _marked8);
}
/**
 * Action generator for trashing the current post in the editor.
 */

function trashPost() {
  var postTypeSlug, postType, post;
  return regenerator_default.a.wrap(function trashPost$(_context9) {
    while (1) {
      switch (_context9.prev = _context9.next) {
        case 0:
          _context9.next = 2;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPostType');

        case 2:
          postTypeSlug = _context9.sent;
          _context9.next = 5;
          return Object(external_this_wp_dataControls_["select"])('core', 'getPostType', postTypeSlug);

        case 5:
          postType = _context9.sent;
          _context9.next = 8;
          return Object(external_this_wp_dataControls_["dispatch"])('core/notices', 'removeNotice', TRASH_POST_NOTICE_ID);

        case 8:
          _context9.prev = 8;
          _context9.next = 11;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPost');

        case 11:
          post = _context9.sent;
          _context9.next = 14;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: "/wp/v2/".concat(postType.rest_base, "/").concat(post.id),
            method: 'DELETE'
          });

        case 14:
          _context9.next = 16;
          return Object(external_this_wp_dataControls_["dispatch"])(STORE_KEY, 'savePost');

        case 16:
          _context9.next = 22;
          break;

        case 18:
          _context9.prev = 18;
          _context9.t0 = _context9["catch"](8);
          _context9.next = 22;
          return external_this_wp_dataControls_["dispatch"].apply(void 0, ['core/notices', 'createErrorNotice'].concat(Object(toConsumableArray["a" /* default */])(getNotificationArgumentsForTrashFail({
            error: _context9.t0
          }))));

        case 22:
        case "end":
          return _context9.stop();
      }
    }
  }, _marked9, null, [[8, 18]]);
}
/**
 * Action generator used in signalling that the post should autosave.
 *
 * @param {Object?} options Extra flags to identify the autosave.
 */

function actions_autosave(options) {
  return regenerator_default.a.wrap(function autosave$(_context10) {
    while (1) {
      switch (_context10.prev = _context10.next) {
        case 0:
          _context10.next = 2;
          return Object(external_this_wp_dataControls_["dispatch"])(STORE_KEY, 'savePost', Object(objectSpread["a" /* default */])({
            isAutosave: true
          }, options));

        case 2:
        case "end":
          return _context10.stop();
      }
    }
  }, _marked10);
}
function actions_experimentalLocalAutosave() {
  var post, title, content, excerpt;
  return regenerator_default.a.wrap(function __experimentalLocalAutosave$(_context11) {
    while (1) {
      switch (_context11.prev = _context11.next) {
        case 0:
          _context11.next = 2;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPost');

        case 2:
          post = _context11.sent;
          _context11.next = 5;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getEditedPostAttribute', 'title');

        case 5:
          title = _context11.sent;
          _context11.next = 8;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getEditedPostAttribute', 'content');

        case 8:
          content = _context11.sent;
          _context11.next = 11;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getEditedPostAttribute', 'excerpt');

        case 11:
          excerpt = _context11.sent;
          _context11.next = 14;
          return {
            type: 'LOCAL_AUTOSAVE_SET',
            postId: post.id,
            title: title,
            content: content,
            excerpt: excerpt
          };

        case 14:
        case "end":
          return _context11.stop();
      }
    }
  }, _marked11);
}
/**
 * Returns an action object used in signalling that undo history should
 * restore last popped state.
 *
 * @yield {Object} Action object.
 */

function actions_redo() {
  return regenerator_default.a.wrap(function redo$(_context12) {
    while (1) {
      switch (_context12.prev = _context12.next) {
        case 0:
          _context12.next = 2;
          return Object(external_this_wp_dataControls_["dispatch"])('core', 'redo');

        case 2:
        case "end":
          return _context12.stop();
      }
    }
  }, _marked12);
}
/**
 * Returns an action object used in signalling that undo history should pop.
 *
 * @yield {Object} Action object.
 */

function actions_undo() {
  return regenerator_default.a.wrap(function undo$(_context13) {
    while (1) {
      switch (_context13.prev = _context13.next) {
        case 0:
          _context13.next = 2;
          return Object(external_this_wp_dataControls_["dispatch"])('core', 'undo');

        case 2:
        case "end":
          return _context13.stop();
      }
    }
  }, _marked13);
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
 * Returns an action object used in signalling that a reusable block is
 * to be updated.
 *
 * @param {number} id      The ID of the reusable block to update.
 * @param {Object} changes The changes to apply.
 *
 * @return {Object} Action object.
 */

function __experimentalUpdateReusableBlock(id, changes) {
  return {
    type: 'UPDATE_REUSABLE_BLOCK',
    id: id,
    changes: changes
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
    lockName: lockName
  };
}
/**
 * Returns an action object used to signal that post saving is unlocked.
 *
 * @param  {string} lockName The lock name.
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
    lockName: lockName
  };
}
/**
 * Returns an action object used to signal that the blocks have been updated.
 *
 * @param {Array}   blocks  Block Array.
 * @param {?Object} options Optional options.
 *
 * @yield {Object} Action object
 */

function actions_resetEditorBlocks(blocks) {
  var options,
      lastBlockAttributesChange,
      updatedSources,
      updatedBlockTypes,
      _i3,
      _Object$entries2,
      _Object$entries2$_i,
      clientId,
      attributes,
      blockName,
      blockType,
      _i4,
      _Object$entries3,
      _Object$entries3$_i,
      attributeName,
      newAttributeValue,
      schema,
      source,
      edits,
      _ref2,
      id,
      type,
      noChange,
      _args15 = arguments;

  return regenerator_default.a.wrap(function resetEditorBlocks$(_context14) {
    while (1) {
      switch (_context14.prev = _context14.next) {
        case 0:
          options = _args15.length > 1 && _args15[1] !== undefined ? _args15[1] : {};
          _context14.next = 3;
          return Object(external_this_wp_dataControls_["select"])('core/block-editor', '__experimentalGetLastBlockAttributeChanges');

        case 3:
          lastBlockAttributesChange = _context14.sent;

          if (!lastBlockAttributesChange) {
            _context14.next = 36;
            break;
          }

          updatedSources = new Set();
          updatedBlockTypes = new Set();
          _i3 = 0, _Object$entries2 = Object.entries(lastBlockAttributesChange);

        case 8:
          if (!(_i3 < _Object$entries2.length)) {
            _context14.next = 35;
            break;
          }

          _Object$entries2$_i = Object(slicedToArray["a" /* default */])(_Object$entries2[_i3], 2), clientId = _Object$entries2$_i[0], attributes = _Object$entries2$_i[1];
          _context14.next = 12;
          return Object(external_this_wp_dataControls_["select"])('core/block-editor', 'getBlockName', clientId);

        case 12:
          blockName = _context14.sent;

          if (!updatedBlockTypes.has(blockName)) {
            _context14.next = 15;
            break;
          }

          return _context14.abrupt("continue", 32);

        case 15:
          updatedBlockTypes.add(blockName);
          _context14.next = 18;
          return Object(external_this_wp_dataControls_["select"])('core/blocks', 'getBlockType', blockName);

        case 18:
          blockType = _context14.sent;
          _i4 = 0, _Object$entries3 = Object.entries(attributes);

        case 20:
          if (!(_i4 < _Object$entries3.length)) {
            _context14.next = 32;
            break;
          }

          _Object$entries3$_i = Object(slicedToArray["a" /* default */])(_Object$entries3[_i4], 2), attributeName = _Object$entries3$_i[0], newAttributeValue = _Object$entries3$_i[1];

          if (blockType.attributes.hasOwnProperty(attributeName)) {
            _context14.next = 24;
            break;
          }

          return _context14.abrupt("continue", 29);

        case 24:
          schema = blockType.attributes[attributeName];
          source = block_sources_namespaceObject[schema.source];

          if (!(source && source.update)) {
            _context14.next = 29;
            break;
          }

          return _context14.delegateYield(source.update(schema, newAttributeValue), "t0", 28);

        case 28:
          updatedSources.add(source);

        case 29:
          _i4++;
          _context14.next = 20;
          break;

        case 32:
          _i3++;
          _context14.next = 8;
          break;

        case 35:
          return _context14.delegateYield(resetLastBlockSourceDependencies(Array.from(updatedSources)), "t1", 36);

        case 36:
          return _context14.delegateYield(getBlocksWithSourcedAttributes(blocks), "t2", 37);

        case 37:
          _context14.t3 = _context14.t2;
          edits = {
            blocks: _context14.t3
          };

          if (!(options.__unstableShouldCreateUndoLevel !== false)) {
            _context14.next = 55;
            break;
          }

          _context14.next = 42;
          return Object(external_this_wp_dataControls_["select"])(STORE_KEY, 'getCurrentPost');

        case 42:
          _ref2 = _context14.sent;
          id = _ref2.id;
          type = _ref2.type;
          _context14.next = 47;
          return Object(external_this_wp_dataControls_["select"])('core', 'getEditedEntityRecord', 'postType', type, id);

        case 47:
          _context14.t4 = _context14.sent.blocks;
          _context14.t5 = edits.blocks;
          noChange = _context14.t4 === _context14.t5;

          if (!noChange) {
            _context14.next = 54;
            break;
          }

          _context14.next = 53;
          return Object(external_this_wp_dataControls_["dispatch"])('core', '__unstableCreateUndoLevel', 'postType', type, id);

        case 53:
          return _context14.abrupt("return", _context14.sent);

        case 54:
          // We create a new function here on every persistent edit
          // to make sure the edit makes the post dirty and creates
          // a new undo level.
          edits.content = function (_ref3) {
            var _ref3$blocks = _ref3.blocks,
                blocksForSerialization = _ref3$blocks === void 0 ? [] : _ref3$blocks;
            return serialize_blocks(blocksForSerialization);
          };

        case 55:
          return _context14.delegateYield(actions_editPost(edits), "t6", 56);

        case 56:
        case "end":
          return _context14.stop();
      }
    }
  }, _marked14);
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
          _args16 = arguments;

      return regenerator_default.a.wrap(function _callee$(_context15) {
        while (1) {
          switch (_context15.prev = _context15.next) {
            case 0:
              external_this_wp_deprecated_default()('`wp.data.dispatch( \'core/editor\' ).' + name + '`', {
                alternative: '`wp.data.dispatch( \'core/block-editor\' ).' + name + '`'
              });

              for (_len = _args16.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
                args[_key] = _args16[_key];
              }

              _context15.next = 4;
              return external_this_wp_dataControls_["dispatch"].apply(void 0, ['core/block-editor', name].concat(args));

            case 4:
            case "end":
              return _context15.stop();
          }
        }
      }, _callee);
    })
  );
};
/**
 * @see resetBlocks in core/block-editor store.
 */


var resetBlocks = actions_getBlockEditorAction('resetBlocks');
/**
 * @see receiveBlocks in core/block-editor store.
 */

var receiveBlocks = actions_getBlockEditorAction('receiveBlocks');
/**
 * @see updateBlock in core/block-editor store.
 */

var updateBlock = actions_getBlockEditorAction('updateBlock');
/**
 * @see updateBlockAttributes in core/block-editor store.
 */

var updateBlockAttributes = actions_getBlockEditorAction('updateBlockAttributes');
/**
 * @see selectBlock in core/block-editor store.
 */

var selectBlock = actions_getBlockEditorAction('selectBlock');
/**
 * @see startMultiSelect in core/block-editor store.
 */

var startMultiSelect = actions_getBlockEditorAction('startMultiSelect');
/**
 * @see stopMultiSelect in core/block-editor store.
 */

var stopMultiSelect = actions_getBlockEditorAction('stopMultiSelect');
/**
 * @see multiSelect in core/block-editor store.
 */

var multiSelect = actions_getBlockEditorAction('multiSelect');
/**
 * @see clearSelectedBlock in core/block-editor store.
 */

var clearSelectedBlock = actions_getBlockEditorAction('clearSelectedBlock');
/**
 * @see toggleSelection in core/block-editor store.
 */

var toggleSelection = actions_getBlockEditorAction('toggleSelection');
/**
 * @see replaceBlocks in core/block-editor store.
 */

var actions_replaceBlocks = actions_getBlockEditorAction('replaceBlocks');
/**
 * @see replaceBlock in core/block-editor store.
 */

var replaceBlock = actions_getBlockEditorAction('replaceBlock');
/**
 * @see moveBlocksDown in core/block-editor store.
 */

var moveBlocksDown = actions_getBlockEditorAction('moveBlocksDown');
/**
 * @see moveBlocksUp in core/block-editor store.
 */

var moveBlocksUp = actions_getBlockEditorAction('moveBlocksUp');
/**
 * @see moveBlockToPosition in core/block-editor store.
 */

var moveBlockToPosition = actions_getBlockEditorAction('moveBlockToPosition');
/**
 * @see insertBlock in core/block-editor store.
 */

var insertBlock = actions_getBlockEditorAction('insertBlock');
/**
 * @see insertBlocks in core/block-editor store.
 */

var insertBlocks = actions_getBlockEditorAction('insertBlocks');
/**
 * @see showInsertionPoint in core/block-editor store.
 */

var showInsertionPoint = actions_getBlockEditorAction('showInsertionPoint');
/**
 * @see hideInsertionPoint in core/block-editor store.
 */

var hideInsertionPoint = actions_getBlockEditorAction('hideInsertionPoint');
/**
 * @see setTemplateValidity in core/block-editor store.
 */

var setTemplateValidity = actions_getBlockEditorAction('setTemplateValidity');
/**
 * @see synchronizeTemplate in core/block-editor store.
 */

var synchronizeTemplate = actions_getBlockEditorAction('synchronizeTemplate');
/**
 * @see mergeBlocks in core/block-editor store.
 */

var mergeBlocks = actions_getBlockEditorAction('mergeBlocks');
/**
 * @see removeBlocks in core/block-editor store.
 */

var actions_removeBlocks = actions_getBlockEditorAction('removeBlocks');
/**
 * @see removeBlock in core/block-editor store.
 */

var removeBlock = actions_getBlockEditorAction('removeBlock');
/**
 * @see toggleBlockMode in core/block-editor store.
 */

var toggleBlockMode = actions_getBlockEditorAction('toggleBlockMode');
/**
 * @see startTyping in core/block-editor store.
 */

var startTyping = actions_getBlockEditorAction('startTyping');
/**
 * @see stopTyping in core/block-editor store.
 */

var stopTyping = actions_getBlockEditorAction('stopTyping');
/**
 * @see enterFormattedText in core/block-editor store.
 */

var enterFormattedText = actions_getBlockEditorAction('enterFormattedText');
/**
 * @see exitFormattedText in core/block-editor store.
 */

var exitFormattedText = actions_getBlockEditorAction('exitFormattedText');
/**
 * @see insertDefaultBlock in core/block-editor store.
 */

var insertDefaultBlock = actions_getBlockEditorAction('insertDefaultBlock');
/**
 * @see updateBlockListSettings in core/block-editor store.
 */

var updateBlockListSettings = actions_getBlockEditorAction('updateBlockListSettings');

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__(36);

// EXTERNAL MODULE: external {"this":["wp","date"]}
var external_this_wp_date_ = __webpack_require__(53);

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(26);

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
 * Shared reference to an empty array for cases where it is important to avoid
 * returning a new array reference on every invocation, as in a connected or
 * other pure component which performs `shouldComponentUpdate` check on props.
 * This should be used as a last resort, since the normalized data should be
 * maintained by the reducer result in state.
 */

var EMPTY_ARRAY = [];
/**
 * Returns true if any past editor history snapshots exist, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether undo history exists.
 */

var hasEditorUndo = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function () {
    return select('core').hasUndo();
  };
});
/**
 * Returns true if any future editor history snapshots exist, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether redo history exists.
 */

var hasEditorRedo = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function () {
    return select('core').hasRedo();
  };
});
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
  var edits = getPostEdits(state);
  return 'blocks' in edits || // `edits` is intended to contain only values which are different from
  // the saved post, so the mere presence of a property is an indicator
  // that the value is different than what is known to be saved. While
  // content in Visual mode is represented by the blocks state, in Text
  // mode it is tracked by `edits.content`.
  'content' in edits;
}
/**
 * Returns true if there are unsaved values for the current edit session, or
 * false if the editing state matches the saved or new post.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether unsaved values exist.
 */

var selectors_isEditedPostDirty = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    // Edits should contain only fields which differ from the saved post (reset
    // at initial load and save complete). Thus, a non-empty edits state can be
    // inferred to contain unsaved values.
    var postType = selectors_getCurrentPostType(state);
    var postId = selectors_getCurrentPostId(state);

    if (select('core').hasEditsForEntityRecord('postType', postType, postId)) {
      return true;
    }

    return false;
  };
});
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

var selectors_getCurrentPost = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    var postId = selectors_getCurrentPostId(state);
    var postType = selectors_getCurrentPostType(state);
    var post = select('core').getRawEntityRecord('postType', postType, postId);

    if (post) {
      return post;
    } // This exists for compatibility with the previous selector behavior
    // which would guarantee an object return based on the editor reducer's
    // default empty object state.


    return EMPTY_OBJECT;
  };
});
/**
 * Returns the post type of the post currently being edited.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Post type.
 */

function selectors_getCurrentPostType(state) {
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

function selectors_getCurrentPostId(state) {
  return state.postId;
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

var getPostEdits = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    var postType = selectors_getCurrentPostType(state);
    var postId = selectors_getCurrentPostId(state);
    return select('core').getEntityRecordEdits('postType', postType, postId) || EMPTY_OBJECT;
  };
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
  switch (attributeName) {
    case 'type':
      return selectors_getCurrentPostType(state);

    case 'id':
      return selectors_getCurrentPostId(state);

    default:
      var post = selectors_getCurrentPost(state);

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

var selectors_getNestedEditedPostProperty = function getNestedEditedPostProperty(state, attributeName) {
  var edits = getPostEdits(state);

  if (!edits.hasOwnProperty(attributeName)) {
    return selectors_getCurrentPostAttribute(state, attributeName);
  }

  return Object(objectSpread["a" /* default */])({}, selectors_getCurrentPostAttribute(state, attributeName), edits[attributeName]);
};
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
    return selectors_getNestedEditedPostProperty(state, attributeName);
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

var getAutosaveAttribute = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state, attributeName) {
    if (!Object(external_lodash_["includes"])(AUTOSAVE_PROPERTIES, attributeName) && attributeName !== 'preview_link') {
      return;
    }

    var postType = selectors_getCurrentPostType(state);
    var postId = selectors_getCurrentPostId(state);
    var currentUserId = Object(external_lodash_["get"])(select('core').getCurrentUser(), ['id']);
    var autosave = select('core').getAutosave(postType, postId, currentUserId);

    if (autosave) {
      return getPostRawValue(autosave[attributeName]);
    }
  };
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
 * @param {Object}  state       Global application state.
 * @param {Object?} currentPost Explicit current post for bypassing registry selector.
 *
 * @return {boolean} Whether the post has been published.
 */

function selectors_isCurrentPostPublished(state, currentPost) {
  var post = currentPost || selectors_getCurrentPost(state);
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
  var blocks = selectors_getEditorBlocks(state);

  if (blocks.length) {
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
 * @param {Object} state    Global application state.
 * @param {Object} autosave A raw autosave object from the REST API.
 *
 * @return {boolean} Whether the post can be autosaved.
 */

var selectors_isEditedPostAutosaveable = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    // A post must contain a title, an excerpt, or non-empty content to be valid for autosaving.
    if (!selectors_isEditedPostSaveable(state)) {
      return false;
    } // A post is not autosavable when there is a post autosave lock.


    if (isPostAutosavingLocked(state)) {
      return false;
    }

    var postType = selectors_getCurrentPostType(state);
    var postId = selectors_getCurrentPostId(state);
    var hasFetchedAutosave = select('core').hasFetchedAutosaves(postType, postId);
    var currentUserId = Object(external_lodash_["get"])(select('core').getCurrentUser(), ['id']); // Disable reason - this line causes the side-effect of fetching the autosave
    // via a resolver, moving below the return would result in the autosave never
    // being fetched.
    // eslint-disable-next-line @wordpress/no-unused-vars-before-return

    var autosave = select('core').getAutosave(postType, postId, currentUserId); // If any existing autosaves have not yet been fetched, this function is
    // unable to determine if the post is autosaveable, so return false.

    if (!hasFetchedAutosave) {
      return false;
    } // If we don't already have an autosave, the post is autosaveable.


    if (!autosave) {
      return true;
    } // To avoid an expensive content serialization, use the content dirtiness
    // flag in place of content field comparison against the known autosave.
    // This is not strictly accurate, and relies on a tolerance toward autosave
    // request failures for unnecessary saves.


    if (hasChangedContent(state)) {
      return true;
    } // If the title or excerpt has changed, the post is autosaveable.


    return ['title', 'excerpt'].some(function (field) {
      return getPostRawValue(autosave[field]) !== selectors_getEditedPostAttribute(state, field);
    });
  };
});
/**
 * Returns the current autosave, or null if one is not set (i.e. if the post
 * has yet to be autosaved, or has been saved or published since the last
 * autosave).
 *
 * @deprecated since 5.6. Callers should use the `getAutosave( postType, postId, userId )`
 * 			   selector from the '@wordpress/core-data' package.
 *
 * @param {Object} state Editor state.
 *
 * @return {?Object} Current autosave, if exists.
 */

var getAutosave = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    external_this_wp_deprecated_default()('`wp.data.select( \'core/editor\' ).getAutosave()`', {
      alternative: '`wp.data.select( \'core\' ).getAutosave( postType, postId, userId )`',
      plugin: 'Gutenberg'
    });
    var postType = selectors_getCurrentPostType(state);
    var postId = selectors_getCurrentPostId(state);
    var currentUserId = Object(external_lodash_["get"])(select('core').getCurrentUser(), ['id']);
    var autosave = select('core').getAutosave(postType, postId, currentUserId);
    return Object(external_lodash_["mapValues"])(Object(external_lodash_["pick"])(autosave, AUTOSAVE_PROPERTIES), getPostRawValue);
  };
});
/**
 * Returns the true if there is an existing autosave, otherwise false.
 *
 * @deprecated since 5.6. Callers should use the `getAutosave( postType, postId, userId )` selector
 *             from the '@wordpress/core-data' package and check for a truthy value.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether there is an existing autosave.
 */

var hasAutosave = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    external_this_wp_deprecated_default()('`wp.data.select( \'core/editor\' ).hasAutosave()`', {
      alternative: '`!! wp.data.select( \'core\' ).getAutosave( postType, postId, userId )`',
      plugin: 'Gutenberg'
    });
    var postType = selectors_getCurrentPostType(state);
    var postId = selectors_getCurrentPostId(state);
    var currentUserId = Object(external_lodash_["get"])(select('core').getCurrentUser(), ['id']);
    return !!select('core').getAutosave(postType, postId, currentUserId);
  };
});
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
 * @param {Object} state Editor state.
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

var selectors_isSavingPost = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    var postType = selectors_getCurrentPostType(state);
    var postId = selectors_getCurrentPostId(state);
    return select('core').isSavingEntityRecord('postType', postType, postId);
  };
});
/**
 * Returns true if a previous post save was attempted successfully, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post was saved successfully.
 */

var didPostSaveRequestSucceed = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    var postType = selectors_getCurrentPostType(state);
    var postId = selectors_getCurrentPostId(state);
    return !select('core').getLastEntitySaveError('postType', postType, postId);
  };
});
/**
 * Returns true if a previous post save was attempted but failed, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post save failed.
 */

var didPostSaveRequestFail = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    var postType = selectors_getCurrentPostType(state);
    var postId = selectors_getCurrentPostId(state);
    return !!select('core').getLastEntitySaveError('postType', postType, postId);
  };
});
/**
 * Returns true if the post is autosaving, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post is autosaving.
 */

function selectors_isAutosavingPost(state) {
  if (!selectors_isSavingPost(state)) {
    return false;
  }

  return !!Object(external_lodash_["get"])(state.saving, ['options', 'isAutosave']);
}
/**
 * Returns true if the post is being previewed, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post is being previewed.
 */

function isPreviewingPost(state) {
  if (!selectors_isSavingPost(state)) {
    return false;
  }

  return !!state.saving.options.isPreview;
}
/**
 * Returns the post preview link
 *
 * @param {Object} state Global application state.
 *
 * @return {string?} Preview Link.
 */

function selectors_getEditedPostPreviewLink(state) {
  if (state.saving.pending || selectors_isSavingPost(state)) {
    return;
  }

  var previewLink = getAutosaveAttribute(state, 'preview_link');

  if (!previewLink) {
    previewLink = selectors_getEditedPostAttribute(state, 'link');

    if (previewLink) {
      previewLink = Object(external_this_wp_url_["addQueryArgs"])(previewLink, {
        preview: true
      });
    }
  }

  var featuredImageId = selectors_getEditedPostAttribute(state, 'featured_media');

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
  var blocks = selectors_getEditorBlocks(state);
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
 * @deprecated since Gutenberg 6.2.0.
 *
 * @param {Object} state Editor state.
 *
 * @return {WPBlock[]} Filtered set of blocks for save.
 */

function getBlocksForSerialization(state) {
  external_this_wp_deprecated_default()('`core/editor` getBlocksForSerialization selector', {
    plugin: 'Gutenberg',
    alternative: 'getEditorBlocks',
    hint: 'Blocks serialization pre-processing occurs at save time'
  });
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
 * Returns the content of the post being edited.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Post content.
 */

var getEditedPostContent = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state) {
    var postId = selectors_getCurrentPostId(state);
    var postType = selectors_getCurrentPostType(state);
    var record = select('core').getEditedEntityRecord('postType', postType, postId);

    if (record) {
      if (typeof record.content === 'function') {
        return record.content(record);
      } else if (record.blocks) {
        return serialize_blocks(record.blocks);
      } else if (record.content) {
        return record.content;
      }
    }

    return '';
  };
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

var selectors_experimentalGetReusableBlocks = Object(rememo["a" /* default */])(function (state) {
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

  return !!stateBeforeRequest && !selectors_isCurrentPostPublished(null, stateBeforeRequest.currentPost);
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

function selectors_canUserUseUnfilteredHTML(state) {
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

function selectors_getEditorBlocks(state) {
  return selectors_getEditedPostAttribute(state, 'blocks') || EMPTY_ARRAY;
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

      external_this_wp_deprecated_default()('`wp.data.select( \'core/editor\' ).' + name + '`', {
        alternative: '`wp.data.select( \'core/block-editor\' ).' + name + '`'
      });

      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }

      return (_select = select('core/block-editor'))[name].apply(_select, args);
    };
  });
}
/**
 * @see getBlockName in core/block-editor store.
 */


var selectors_getBlockName = getBlockEditorSelector('getBlockName');
/**
 * @see isBlockValid in core/block-editor store.
 */

var isBlockValid = getBlockEditorSelector('isBlockValid');
/**
 * @see getBlockAttributes in core/block-editor store.
 */

var getBlockAttributes = getBlockEditorSelector('getBlockAttributes');
/**
 * @see getBlock in core/block-editor store.
 */

var selectors_getBlock = getBlockEditorSelector('getBlock');
/**
 * @see getBlocks in core/block-editor store.
 */

var selectors_getBlocks = getBlockEditorSelector('getBlocks');
/**
 * @see __unstableGetBlockWithoutInnerBlocks in core/block-editor store.
 */

var __unstableGetBlockWithoutInnerBlocks = getBlockEditorSelector('__unstableGetBlockWithoutInnerBlocks');
/**
 * @see getClientIdsOfDescendants in core/block-editor store.
 */

var getClientIdsOfDescendants = getBlockEditorSelector('getClientIdsOfDescendants');
/**
 * @see getClientIdsWithDescendants in core/block-editor store.
 */

var getClientIdsWithDescendants = getBlockEditorSelector('getClientIdsWithDescendants');
/**
 * @see getGlobalBlockCount in core/block-editor store.
 */

var getGlobalBlockCount = getBlockEditorSelector('getGlobalBlockCount');
/**
 * @see getBlocksByClientId in core/block-editor store.
 */

var selectors_getBlocksByClientId = getBlockEditorSelector('getBlocksByClientId');
/**
 * @see getBlockCount in core/block-editor store.
 */

var getBlockCount = getBlockEditorSelector('getBlockCount');
/**
 * @see getBlockSelectionStart in core/block-editor store.
 */

var getBlockSelectionStart = getBlockEditorSelector('getBlockSelectionStart');
/**
 * @see getBlockSelectionEnd in core/block-editor store.
 */

var getBlockSelectionEnd = getBlockEditorSelector('getBlockSelectionEnd');
/**
 * @see getSelectedBlockCount in core/block-editor store.
 */

var getSelectedBlockCount = getBlockEditorSelector('getSelectedBlockCount');
/**
 * @see hasSelectedBlock in core/block-editor store.
 */

var hasSelectedBlock = getBlockEditorSelector('hasSelectedBlock');
/**
 * @see getSelectedBlockClientId in core/block-editor store.
 */

var selectors_getSelectedBlockClientId = getBlockEditorSelector('getSelectedBlockClientId');
/**
 * @see getSelectedBlock in core/block-editor store.
 */

var getSelectedBlock = getBlockEditorSelector('getSelectedBlock');
/**
 * @see getBlockRootClientId in core/block-editor store.
 */

var getBlockRootClientId = getBlockEditorSelector('getBlockRootClientId');
/**
 * @see getBlockHierarchyRootClientId in core/block-editor store.
 */

var getBlockHierarchyRootClientId = getBlockEditorSelector('getBlockHierarchyRootClientId');
/**
 * @see getAdjacentBlockClientId in core/block-editor store.
 */

var getAdjacentBlockClientId = getBlockEditorSelector('getAdjacentBlockClientId');
/**
 * @see getPreviousBlockClientId in core/block-editor store.
 */

var getPreviousBlockClientId = getBlockEditorSelector('getPreviousBlockClientId');
/**
 * @see getNextBlockClientId in core/block-editor store.
 */

var getNextBlockClientId = getBlockEditorSelector('getNextBlockClientId');
/**
 * @see getSelectedBlocksInitialCaretPosition in core/block-editor store.
 */

var getSelectedBlocksInitialCaretPosition = getBlockEditorSelector('getSelectedBlocksInitialCaretPosition');
/**
 * @see getMultiSelectedBlockClientIds in core/block-editor store.
 */

var getMultiSelectedBlockClientIds = getBlockEditorSelector('getMultiSelectedBlockClientIds');
/**
 * @see getMultiSelectedBlocks in core/block-editor store.
 */

var getMultiSelectedBlocks = getBlockEditorSelector('getMultiSelectedBlocks');
/**
 * @see getFirstMultiSelectedBlockClientId in core/block-editor store.
 */

var getFirstMultiSelectedBlockClientId = getBlockEditorSelector('getFirstMultiSelectedBlockClientId');
/**
 * @see getLastMultiSelectedBlockClientId in core/block-editor store.
 */

var getLastMultiSelectedBlockClientId = getBlockEditorSelector('getLastMultiSelectedBlockClientId');
/**
 * @see isFirstMultiSelectedBlock in core/block-editor store.
 */

var isFirstMultiSelectedBlock = getBlockEditorSelector('isFirstMultiSelectedBlock');
/**
 * @see isBlockMultiSelected in core/block-editor store.
 */

var isBlockMultiSelected = getBlockEditorSelector('isBlockMultiSelected');
/**
 * @see isAncestorMultiSelected in core/block-editor store.
 */

var isAncestorMultiSelected = getBlockEditorSelector('isAncestorMultiSelected');
/**
 * @see getMultiSelectedBlocksStartClientId in core/block-editor store.
 */

var getMultiSelectedBlocksStartClientId = getBlockEditorSelector('getMultiSelectedBlocksStartClientId');
/**
 * @see getMultiSelectedBlocksEndClientId in core/block-editor store.
 */

var getMultiSelectedBlocksEndClientId = getBlockEditorSelector('getMultiSelectedBlocksEndClientId');
/**
 * @see getBlockOrder in core/block-editor store.
 */

var getBlockOrder = getBlockEditorSelector('getBlockOrder');
/**
 * @see getBlockIndex in core/block-editor store.
 */

var getBlockIndex = getBlockEditorSelector('getBlockIndex');
/**
 * @see isBlockSelected in core/block-editor store.
 */

var isBlockSelected = getBlockEditorSelector('isBlockSelected');
/**
 * @see hasSelectedInnerBlock in core/block-editor store.
 */

var hasSelectedInnerBlock = getBlockEditorSelector('hasSelectedInnerBlock');
/**
 * @see isBlockWithinSelection in core/block-editor store.
 */

var isBlockWithinSelection = getBlockEditorSelector('isBlockWithinSelection');
/**
 * @see hasMultiSelection in core/block-editor store.
 */

var hasMultiSelection = getBlockEditorSelector('hasMultiSelection');
/**
 * @see isMultiSelecting in core/block-editor store.
 */

var isMultiSelecting = getBlockEditorSelector('isMultiSelecting');
/**
 * @see isSelectionEnabled in core/block-editor store.
 */

var isSelectionEnabled = getBlockEditorSelector('isSelectionEnabled');
/**
 * @see getBlockMode in core/block-editor store.
 */

var getBlockMode = getBlockEditorSelector('getBlockMode');
/**
 * @see isTyping in core/block-editor store.
 */

var isTyping = getBlockEditorSelector('isTyping');
/**
 * @see isCaretWithinFormattedText in core/block-editor store.
 */

var isCaretWithinFormattedText = getBlockEditorSelector('isCaretWithinFormattedText');
/**
 * @see getBlockInsertionPoint in core/block-editor store.
 */

var getBlockInsertionPoint = getBlockEditorSelector('getBlockInsertionPoint');
/**
 * @see isBlockInsertionPointVisible in core/block-editor store.
 */

var isBlockInsertionPointVisible = getBlockEditorSelector('isBlockInsertionPointVisible');
/**
 * @see isValidTemplate in core/block-editor store.
 */

var isValidTemplate = getBlockEditorSelector('isValidTemplate');
/**
 * @see getTemplate in core/block-editor store.
 */

var getTemplate = getBlockEditorSelector('getTemplate');
/**
 * @see getTemplateLock in core/block-editor store.
 */

var getTemplateLock = getBlockEditorSelector('getTemplateLock');
/**
 * @see canInsertBlockType in core/block-editor store.
 */

var selectors_canInsertBlockType = getBlockEditorSelector('canInsertBlockType');
/**
 * @see getInserterItems in core/block-editor store.
 */

var selectors_getInserterItems = getBlockEditorSelector('getInserterItems');
/**
 * @see hasInserterItems in core/block-editor store.
 */

var hasInserterItems = getBlockEditorSelector('hasInserterItems');
/**
 * @see getBlockListSettings in core/block-editor store.
 */

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

              return Object(objectSpread["a" /* default */])({}, post, {
                content: post.content.raw,
                title: post.title.raw
              });
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
    }, _callee, null, [[7, 23]]);
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
    var postType, id, dispatch, state, _getReusableBlock, title, content, isTemporary, data, path, method, updatedReusableBlock, message;

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
            _getReusableBlock = __experimentalGetReusableBlock(state, id), title = _getReusableBlock.title, content = _getReusableBlock.content, isTemporary = _getReusableBlock.isTemporary;
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
            _context2.prev = 12;
            _context2.next = 15;
            return external_this_wp_apiFetch_default()({
              path: path,
              data: data,
              method: method
            });

          case 15:
            updatedReusableBlock = _context2.sent;
            dispatch({
              type: 'SAVE_REUSABLE_BLOCK_SUCCESS',
              updatedId: updatedReusableBlock.id,
              id: id
            });
            message = isTemporary ? Object(external_this_wp_i18n_["__"])('Block created.') : Object(external_this_wp_i18n_["__"])('Block updated.');
            Object(external_this_wp_data_["dispatch"])('core/notices').createSuccessNotice(message, {
              id: REUSABLE_BLOCK_NOTICE_ID,
              type: 'snackbar'
            });

            Object(external_this_wp_data_["dispatch"])('core/block-editor').__unstableSaveReusableBlock(id, updatedReusableBlock.id);

            _context2.next = 26;
            break;

          case 22:
            _context2.prev = 22;
            _context2.t0 = _context2["catch"](12);
            dispatch({
              type: 'SAVE_REUSABLE_BLOCK_FAILURE',
              id: id
            });
            Object(external_this_wp_data_["dispatch"])('core/notices').createErrorNotice(_context2.t0.message, {
              id: REUSABLE_BLOCK_NOTICE_ID
            });

          case 26:
          case "end":
            return _context2.stop();
        }
      }
    }, _callee2, null, [[12, 22]]);
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

            if (associatedBlockClientIds.length) {
              Object(external_this_wp_data_["dispatch"])('core/block-editor').removeBlocks(associatedBlockClientIds);
            }

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
              id: REUSABLE_BLOCK_NOTICE_ID,
              type: 'snackbar'
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
    }, _callee3, null, [[16, 24]]);
  }));

  return function deleteReusableBlocks(_x5, _x6) {
    return _ref3.apply(this, arguments);
  };
}();
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
  var newBlocks = Object(external_this_wp_blocks_["parse"])(reusableBlock.content);
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
  var reusableBlock = {
    id: Object(external_lodash_["uniqueId"])('reusable'),
    title: Object(external_this_wp_i18n_["__"])('Untitled Reusable Block'),
    content: Object(external_this_wp_blocks_["serialize"])(Object(external_this_wp_data_["select"])('core/block-editor').getBlocksByClientId(action.clientIds))
  };
  dispatch(__experimentalReceiveReusableBlocks([reusableBlock]));
  dispatch(__experimentalSaveReusableBlock(reusableBlock.id));
  Object(external_this_wp_data_["dispatch"])('core/block-editor').replaceBlocks(action.clientIds, Object(external_this_wp_blocks_["createBlock"])('core/block', {
    ref: reusableBlock.id
  }));
};

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/effects.js
/**
 * Internal dependencies
 */

/* harmony default export */ var effects = ({
  FETCH_REUSABLE_BLOCKS: function FETCH_REUSABLE_BLOCKS(action, store) {
    fetchReusableBlocks(action, store);
  },
  SAVE_REUSABLE_BLOCK: function SAVE_REUSABLE_BLOCK(action, store) {
    saveReusableBlocks(action, store);
  },
  DELETE_REUSABLE_BLOCK: function DELETE_REUSABLE_BLOCK(action, store) {
    deleteReusableBlocks(action, store);
  },
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
  var enhancedDispatch = function enhancedDispatch() {
    throw new Error('Dispatching while constructing your middleware is not allowed. ' + 'Other middleware would not be applied to this dispatch.');
  };

  var middlewareAPI = {
    getState: store.getState,
    dispatch: function dispatch() {
      return enhancedDispatch.apply(void 0, arguments);
    }
  };
  enhancedDispatch = refx_default()(effects)(middlewareAPI)(store.dispatch);
  store.dispatch = enhancedDispatch;
  return store;
}

/* harmony default export */ var middlewares = (applyMiddlewares);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */







/**
 * Post editor data store configuration.
 *
 * @see https://github.com/WordPress/gutenberg/blob/master/packages/data/README.md#registerStore
 *
 * @type {Object}
 */

var storeConfig = {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject,
  controls: Object(objectSpread["a" /* default */])({}, external_this_wp_dataControls_["controls"], store_controls)
};
var store_store = Object(external_this_wp_data_["registerStore"])(STORE_KEY, Object(objectSpread["a" /* default */])({}, storeConfig, {
  persist: ['preferences']
}));
middlewares(store_store);
/* harmony default export */ var build_module_store = (store_store);

// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__(27);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autocompleters/block.js



/**
 * External dependencies
 */

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
 * Triggers a fetch of reusable blocks, once.
 *
 * TODO: Reusable blocks fetching should be reimplemented as a core-data entity
 * resolver, not relying on `core/editor` (see #7119). The implementation here
 * is imperfect in that the options result will not await the completion of the
 * fetch request and thus will not include any reusable blocks. This has always
 * been true, but relied upon the fact the user would be delayed in typing an
 * autocompleter search query. Once implemented using resolvers, the status of
 * this request could be subscribed to as part of a promised return value using
 * the result of `hasFinishedResolution`. There is currently reliable way to
 * determine that a reusable blocks fetch request has completed.
 *
 * @return {Promise} Promise resolving once reusable blocks fetched.
 */


var block_fetchReusableBlocks = Object(external_lodash_["once"])(function () {
  Object(external_this_wp_data_["dispatch"])('core/editor').__experimentalFetchReusableBlocks();
});
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
      block_fetchReusableBlocks();
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



// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(14);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(15);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(8);

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

      var _this$props2 = this.props,
          interval = _this$props2.interval,
          _this$props2$shouldTh = _this$props2.shouldThrottle,
          shouldThrottle = _this$props2$shouldTh === void 0 ? false : _this$props2$shouldTh; // By default, AutosaveMonitor will wait for a pause in editing before
      // autosaving. In other words, its action is "debounced".
      //
      // The `shouldThrottle` props allows overriding this behaviour, thus
      // making the autosave action "throttled".

      if (!shouldThrottle && this.pendingSave) {
        clearTimeout(this.pendingSave);
        delete this.pendingSave;
      }

      if (isPendingSave && !(shouldThrottle && this.pendingSave)) {
        this.pendingSave = setTimeout(function () {
          _this.props.autosave();

          delete _this.pendingSave;
        }, interval * 1000);
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
/* harmony default export */ var autosave_monitor = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, ownProps) {
  var _select = select('core'),
      getReferenceByDistinctEdits = _select.getReferenceByDistinctEdits;

  var _select2 = select('core/editor'),
      isEditedPostDirty = _select2.isEditedPostDirty,
      isEditedPostAutosaveable = _select2.isEditedPostAutosaveable,
      isAutosavingPost = _select2.isAutosavingPost,
      getEditorSettings = _select2.getEditorSettings;

  var _ownProps$interval = ownProps.interval,
      interval = _ownProps$interval === void 0 ? getEditorSettings().autosaveInterval : _ownProps$interval;
  return {
    isDirty: isEditedPostDirty(),
    isAutosaveable: isEditedPostAutosaveable(),
    editsReference: getReferenceByDistinctEdits(),
    isAutosaving: isAutosavingPost(),
    interval: interval
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  return {
    autosave: function autosave() {
      var _ownProps$autosave = ownProps.autosave,
          autosave = _ownProps$autosave === void 0 ? dispatch('core/editor').autosave : _ownProps$autosave;
      autosave();
    }
  };
})])(autosave_monitor_AutosaveMonitor));

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
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
var assertThisInitialized = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(19);

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
    _this.undoOrRedo = _this.undoOrRedo.bind(Object(assertThisInitialized["a" /* default */])(_this));
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

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__(21);

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
  var notices = _ref.notices,
      onRemove = _ref.onRemove;
  var dismissibleNotices = Object(external_lodash_["filter"])(notices, {
    isDismissible: true,
    type: 'default'
  });
  var nonDismissibleNotices = Object(external_lodash_["filter"])(notices, {
    isDismissible: false,
    type: 'default'
  });
  var snackbarNotices = Object(external_lodash_["filter"])(notices, {
    type: 'snackbar'
  });
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["NoticeList"], {
    notices: nonDismissibleNotices,
    className: "components-editor-notices__pinned"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["NoticeList"], {
    notices: dismissibleNotices,
    className: "components-editor-notices__dismissible",
    onRemove: onRemove
  }, Object(external_this_wp_element_["createElement"])(template_validation_notice, null)), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SnackbarList"], {
    notices: snackbarNotices,
    className: "components-editor-notices__snackbar",
    onRemove: onRemove
  }));
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
    _this.reboot = _this.reboot.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.getContent = _this.getContent.bind(Object(assertThisInitialized["a" /* default */])(_this));
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

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/local-autosave-monitor/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



var requestIdleCallback = window.requestIdleCallback ? window.requestIdleCallback : window.requestAnimationFrame;
/**
 * Function which returns true if the current environment supports browser
 * sessionStorage, or false otherwise. The result of this function is cached and
 * reused in subsequent invocations.
 */

var hasSessionStorageSupport = Object(external_lodash_["once"])(function () {
  try {
    // Private Browsing in Safari 10 and earlier will throw an error when
    // attempting to set into sessionStorage. The test here is intentional in
    // causing a thrown error as condition bailing from local autosave.
    window.sessionStorage.setItem('__wpEditorTestSessionStorage', '');
    window.sessionStorage.removeItem('__wpEditorTestSessionStorage');
    return true;
  } catch (error) {
    return false;
  }
});
/**
 * Custom hook which manages the creation of a notice prompting the user to
 * restore a local autosave, if one exists.
 */

function useAutosaveNotice() {
  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    var _postId = select('core/editor').getCurrentPostId();

    var postType = select('core/editor').getCurrentPostType();
    var user = select('core').getCurrentUser();
    return {
      postId: _postId,
      getEditedPostAttribute: select('core/editor').getEditedPostAttribute,
      remoteAutosave: select('core').getAutosave(postType, _postId, user.id),
      hasFetchedAutosave: select('core').hasFetchedAutosaves(postType, _postId) && user.id
    };
  }),
      postId = _useSelect.postId,
      getEditedPostAttribute = _useSelect.getEditedPostAttribute,
      remoteAutosave = _useSelect.remoteAutosave,
      hasFetchedAutosave = _useSelect.hasFetchedAutosave;

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/notices'),
      createWarningNotice = _useDispatch.createWarningNotice,
      removeNotice = _useDispatch.removeNotice;

  var _useDispatch2 = Object(external_this_wp_data_["useDispatch"])('core/editor'),
      editPost = _useDispatch2.editPost,
      resetEditorBlocks = _useDispatch2.resetEditorBlocks;

  Object(external_this_wp_element_["useEffect"])(function () {
    if (!hasFetchedAutosave) {
      return;
    }

    var localAutosave = localAutosaveGet(postId);

    if (!localAutosave) {
      return;
    }

    try {
      localAutosave = JSON.parse(localAutosave);
    } catch (error) {
      // Not usable if it can't be parsed.
      return;
    }

    var _localAutosave = localAutosave,
        title = _localAutosave.post_title,
        content = _localAutosave.content,
        excerpt = _localAutosave.excerpt;
    var edits = {
      title: title,
      content: content,
      excerpt: excerpt
    };
    {
      // Only display a notice if there is a difference between what has been
      // saved and that which is stored in sessionStorage.
      var hasDifference = Object.keys(edits).some(function (key) {
        return edits[key] !== getEditedPostAttribute(key);
      });

      if (!hasDifference) {
        // If there is no difference, it can be safely ejected from storage.
        localAutosaveClear(postId);
        return;
      }
    }

    if (remoteAutosave) {
      return;
    }

    var noticeId = Object(external_lodash_["uniqueId"])('wpEditorAutosaveRestore');
    createWarningNotice(Object(external_this_wp_i18n_["__"])('The backup of this post in your browser is different from the version below.'), {
      id: noticeId,
      actions: [{
        label: Object(external_this_wp_i18n_["__"])('Restore the backup'),
        onClick: function onClick() {
          editPost(Object(external_lodash_["omit"])(edits, ['content']));
          resetEditorBlocks(Object(external_this_wp_blocks_["parse"])(edits.content));
          removeNotice(noticeId);
        }
      }]
    });
  }, [postId, hasFetchedAutosave]);
}
/**
 * Custom hook which ejects a local autosave after a successful save occurs.
 */


function useAutosavePurge() {
  var _useSelect2 = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      postId: select('core/editor').getCurrentPostId(),
      postType: select('core/editor').getCurrentPostType(),
      isDirty: select('core/editor').isEditedPostDirty(),
      isAutosaving: select('core/editor').isAutosavingPost(),
      didError: select('core/editor').didPostSaveRequestFail()
    };
  }),
      postId = _useSelect2.postId,
      isDirty = _useSelect2.isDirty,
      isAutosaving = _useSelect2.isAutosaving,
      didError = _useSelect2.didError;

  var lastIsDirty = Object(external_this_wp_element_["useRef"])(isDirty);
  var lastIsAutosaving = Object(external_this_wp_element_["useRef"])(isAutosaving);
  Object(external_this_wp_element_["useEffect"])(function () {
    if (!didError && (lastIsAutosaving.current && !isAutosaving || lastIsDirty.current && !isDirty)) {
      localAutosaveClear(postId);
    }

    lastIsDirty.current = isDirty;
    lastIsAutosaving.current = isAutosaving;
  }, [isDirty, isAutosaving, didError]);
}

function LocalAutosaveMonitor() {
  var _useDispatch3 = Object(external_this_wp_data_["useDispatch"])('core/editor'),
      __experimentalLocalAutosave = _useDispatch3.__experimentalLocalAutosave;

  var autosave = Object(external_this_wp_element_["useCallback"])(function () {
    requestIdleCallback(__experimentalLocalAutosave);
  }, []);
  useAutosaveNotice();
  useAutosavePurge();

  var _useSelect3 = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      localAutosaveInterval: select('core/editor').getEditorSettings().__experimentalLocalAutosaveInterval
    };
  }),
      localAutosaveInterval = _useSelect3.localAutosaveInterval;

  return Object(external_this_wp_element_["createElement"])(autosave_monitor, {
    interval: localAutosaveInterval,
    autosave: autosave,
    shouldThrottle: true
  });
}

/* harmony default export */ var local_autosave_monitor = (Object(external_this_wp_compose_["ifCondition"])(hasSessionStorageSupport)(LocalAutosaveMonitor));

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
 * @param {Object}            props
 * @param {string}            [props.postType]  Current post type.
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

// EXTERNAL MODULE: external {"this":["wp","htmlEntities"]}
var external_this_wp_htmlEntities_ = __webpack_require__(52);

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
    _this.setAuthorId = _this.setAuthorId.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
        }, Object(external_this_wp_htmlEntities_["decodeEntities"])(author.name));
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

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(18);

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

var DEFAULT_SET_FEATURE_IMAGE_LABEL = Object(external_this_wp_i18n_["__"])('Set Featured Image');

var DEFAULT_REMOVE_FEATURE_IMAGE_LABEL = Object(external_this_wp_i18n_["__"])('Remove Image');

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
    unstableFeaturedImageFlow: true,
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
        naturalHeight: mediaHeight,
        isInline: true
      }, Object(external_this_wp_element_["createElement"])("img", {
        src: mediaSourceUrl,
        alt: ""
      })), !!featuredImageId && !media && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null), !featuredImageId && (postLabel.set_featured_image || DEFAULT_SET_FEATURE_IMAGE_LABEL));
    },
    value: featuredImageId
  })), !!featuredImageId && media && !media.isLoading && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
    title: postLabel.featured_image || DEFAULT_FEATURE_IMAGE_LABEL,
    onSelect: onUpdateImage,
    unstableFeaturedImageFlow: true,
    allowedTypes: ALLOWED_MEDIA_TYPES,
    modalClass: "editor-post-featured-image__media-modal",
    render: function render(_ref3) {
      var open = _ref3.open;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        onClick: open,
        isDefault: true,
        isLarge: true
      }, Object(external_this_wp_i18n_["__"])('Replace Image'));
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
 * This replicates some of what sanitize_title() does in WordPress core, but
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
  if (!string) {
    return '';
  }

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
   * @param {string} markup The preview interstitial markup.
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
    _this.openPreviewWindow = _this.openPreviewWindow.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
    _this.sendPostLock = _this.sendPostLock.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.receivePostLock = _this.receivePostLock.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.releasePostLock = _this.releasePostLock.bind(Object(assertThisInitialized["a" /* default */])(_this));
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

      if (window.navigator.sendBeacon) {
        window.navigator.sendBeacon(postLockUtils.ajaxUrl, data);
      } else {
        var xhr = new window.XMLHttpRequest();
        xhr.open('POST', postLockUtils.ajaxUrl, false);
        xhr.send(data);
      }
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
      return Object(external_this_wp_element_["createElement"])("div", null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], Object(esm_extends["a" /* default */])({
        ref: this.buttonNode
      }, componentProps), componentChildren), Object(external_this_wp_element_["createElement"])(external_this_wp_nux_["DotTip"], {
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
      }, {
        undoIgnore: true
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
    _this.setPublic = _this.setPublic.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.setPrivate = _this.setPrivate.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.setPasswordProtected = _this.setPasswordProtected.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.updatePassword = _this.updatePassword.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
      var password = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
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

  return date && !isFloating ? Object(external_this_wp_date_["dateI18n"])("".concat(settings.formats.date, " ").concat(settings.formats.time), date) : Object(external_this_wp_i18n_["__"])('Immediately');
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
 * The unescape of the name property is done using lodash unescape function.
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
 * @return {Object[]} Array of term objects unescaped.
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
    _this.onChange = _this.onChange.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.searchTerms = Object(external_lodash_["throttle"])(_this.searchTerms.bind(Object(assertThisInitialized["a" /* default */])(_this)), 500);
    _this.findOrCreateTerm = _this.findOrCreateTerm.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
  }, Object(external_this_wp_element_["createElement"])(post_schedule, null))), Object(external_this_wp_element_["createElement"])(maybe_post_format_panel, null), Object(external_this_wp_element_["createElement"])(maybe_tags_panel, null), children);
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
    _this.onCopy = _this.onCopy.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSelectInput = _this.onSelectInput.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
    _this.onSubmit = _this.onSubmit.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
      onClick = _ref.onClick,
      isMobileViewport = _ref.isMobileViewport;

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
  }, isMobileViewport ? Object(external_this_wp_i18n_["__"])('Draft') : Object(external_this_wp_i18n_["__"])('Switch to Draft'));
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
}), Object(external_this_wp_viewport_["withViewportMatch"])({
  isMobileViewport: '< small'
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
        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Animate"], {
          type: "loading"
        }, function (_ref) {
          var animateClassName = _ref.className;
          return Object(external_this_wp_element_["createElement"])("span", {
            className: classnames_default()(classes, animateClassName)
          }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dashicon"], {
            icon: "cloud"
          }), isAutosaving ? Object(external_this_wp_i18n_["__"])('Autosaving') : Object(external_this_wp_i18n_["__"])('Saving'));
        });
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
          onClick: function onClick() {
            return onSave();
          },
          shortcut: external_this_wp_keycodes_["displayShortcut"].primary('s'),
          icon: "cloud-upload"
        });
      }

      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: "editor-post-save-draft",
        onClick: function onClick() {
          return onSave();
        },
        shortcut: external_this_wp_keycodes_["displayShortcut"].primary('s'),
        isTertiary: true
      }, label);
    }
  }]);

  return PostSavedState;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var post_saved_state = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var forceIsDirty = _ref2.forceIsDirty,
      forceIsSaving = _ref2.forceIsSaving;

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
  isLargeViewport: 'small'
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
    _this.findTerm = _this.findTerm.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onChange = _this.onChange.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onChangeFormName = _this.onChangeFormName.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onChangeFormParent = _this.onChangeFormParent.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onAddTerm = _this.onAddTerm.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onToggleForm = _this.onToggleForm.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.setFilterValue = _this.setFilterValue.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.sortBySelected = _this.sortBySelected.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
    value: function onChange(termId) {
      var _this$props = this.props,
          onUpdateTerms = _this$props.onUpdateTerms,
          _this$props$terms = _this$props.terms,
          terms = _this$props$terms === void 0 ? [] : _this$props$terms,
          taxonomy = _this$props.taxonomy;
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


        if (-1 !== term.name.toLowerCase().indexOf(filterValue.toLowerCase()) || term.children.length > 0) {
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
        return Object(external_this_wp_element_["createElement"])("div", {
          key: term.id,
          className: "editor-post-taxonomies__hierarchical-terms-choice"
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
          checked: terms.indexOf(term.id) !== -1,
          onChange: function onChange() {
            var termId = parseInt(term.id, 10);

            _this4.onChange(termId);
          },
          label: Object(external_lodash_["unescape"])(term.name)
        }), !!term.children.length && Object(external_this_wp_element_["createElement"])("div", {
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
var lib = __webpack_require__(62);
var lib_default = /*#__PURE__*/__webpack_require__.n(lib);

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
    _this.edit = _this.edit.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.stopEditing = _this.stopEditing.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
      }, Object(external_this_wp_i18n_["__"])('Type text or HTML')), Object(external_this_wp_element_["createElement"])(lib_default.a, {
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
    _this.onSavePermalink = _this.onSavePermalink.bind(Object(assertThisInitialized["a" /* default */])(_this));
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

/* harmony default export */ var editor = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
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
    _this.addVisibilityCheck = _this.addVisibilityCheck.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onVisibilityChange = _this.onVisibilityChange.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
      }, Object(external_this_wp_url_["safeDecodeURI"])(samplePermalink), "\u200E"), isEditingPermalink && Object(external_this_wp_element_["createElement"])(editor, {
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
      }, Object(external_this_wp_i18n_["__"])('Edit')));
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
    _this.onChange = _this.onChange.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSelect = _this.onSelect.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onUnselect = _this.onUnselect.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onKeyDown = _this.onKeyDown.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.redirectHistory = _this.redirectHistory.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
      }, decodedPlaceholder || Object(external_this_wp_i18n_["__"])('Add title')), Object(external_this_wp_element_["createElement"])(lib_default.a, {
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
        autoFocus: document.body === document.activeElement && isCleanNewPost
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
  }, Object(external_this_wp_i18n_["__"])('Move to Trash'));
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
var external_this_wp_wordcount_ = __webpack_require__(97);

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
  return (
    /*
    * Disable reason: The `list` ARIA role is redundant but
    * Safari+VoiceOver won't announce the list otherwise.
    */

    /* eslint-disable jsx-a11y/no-redundant-roles */
    Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", {
      className: "table-of-contents__wrapper",
      role: "note",
      "aria-label": Object(external_this_wp_i18n_["__"])('Document Statistics'),
      tabIndex: "0"
    }, Object(external_this_wp_element_["createElement"])("ul", {
      role: "list",
      className: "table-of-contents__counts"
    }, Object(external_this_wp_element_["createElement"])("li", {
      className: "table-of-contents__count"
    }, Object(external_this_wp_i18n_["__"])('Words'), Object(external_this_wp_element_["createElement"])(word_count, null)), Object(external_this_wp_element_["createElement"])("li", {
      className: "table-of-contents__count"
    }, Object(external_this_wp_i18n_["__"])('Headings'), Object(external_this_wp_element_["createElement"])("span", {
      className: "table-of-contents__number"
    }, headingCount)), Object(external_this_wp_element_["createElement"])("li", {
      className: "table-of-contents__count"
    }, Object(external_this_wp_i18n_["__"])('Paragraphs'), Object(external_this_wp_element_["createElement"])("span", {
      className: "table-of-contents__number"
    }, paragraphCount)), Object(external_this_wp_element_["createElement"])("li", {
      className: "table-of-contents__count"
    }, Object(external_this_wp_i18n_["__"])('Blocks'), Object(external_this_wp_element_["createElement"])("span", {
      className: "table-of-contents__number"
    }, numberOfBlocks)))), headingCount > 0 && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("hr", null), Object(external_this_wp_element_["createElement"])("h2", {
      className: "table-of-contents__title"
    }, Object(external_this_wp_i18n_["__"])('Document Outline')), Object(external_this_wp_element_["createElement"])(document_outline, {
      onSelect: onRequestClose,
      hasOutlineItemsDisabled: hasOutlineItemsDisabled
    })))
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
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
    _this.warnIfUnsavedChanges = _this.warnIfUnsavedChanges.bind(Object(assertThisInitialized["a" /* default */])(_this));
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
      var isEditedPostDirty = this.props.isEditedPostDirty;

      if (isEditedPostDirty()) {
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
    // We need to call the selector directly in the listener to avoid race
    // conditions with `BrowserURL` where `componentDidUpdate` gets the
    // new value of `isEditedPostDirty` before this component does,
    // causing this component to incorrectly think a trashed post is still dirty.
    isEditedPostDirty: select('core/editor').isEditedPostDirty
  };
})(unsaved_changes_warning_UnsavedChangesWarning));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/with-registry-provider.js




/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var withRegistryProvider = Object(external_this_wp_compose_["createHigherOrderComponent"])(function (WrappedComponent) {
  return Object(external_this_wp_data_["withRegistry"])(function (props) {
    var _props$useSubRegistry = props.useSubRegistry,
        useSubRegistry = _props$useSubRegistry === void 0 ? true : _props$useSubRegistry,
        registry = props.registry,
        additionalProps = Object(objectWithoutProperties["a" /* default */])(props, ["useSubRegistry", "registry"]);

    if (!useSubRegistry) {
      return Object(external_this_wp_element_["createElement"])(WrappedComponent, additionalProps);
    }

    var _useState = Object(external_this_wp_element_["useState"])(null),
        _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
        subRegistry = _useState2[0],
        setSubRegistry = _useState2[1];

    Object(external_this_wp_element_["useEffect"])(function () {
      var newRegistry = Object(external_this_wp_data_["createRegistry"])({
        'core/block-editor': external_this_wp_blockEditor_["storeConfig"]
      }, registry);
      var store = newRegistry.registerStore('core/editor', storeConfig); // This should be removed after the refactoring of the effects to controls.

      middlewares(store);
      setSubRegistry(newRegistry);
    }, [registry]);

    if (!subRegistry) {
      return null;
    }

    return Object(external_this_wp_element_["createElement"])(external_this_wp_data_["RegistryProvider"], {
      value: subRegistry
    }, Object(external_this_wp_element_["createElement"])(WrappedComponent, additionalProps));
  });
}, 'withRegistryProvider');
/* harmony default export */ var with_registry_provider = (withRegistryProvider);

// EXTERNAL MODULE: external {"this":["wp","mediaUtils"]}
var external_this_wp_mediaUtils_ = __webpack_require__(99);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/media-upload/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
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
  Object(external_this_wp_mediaUtils_["uploadMedia"])({
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




// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/reusable-blocks-buttons/reusable-block-convert-button.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






function ReusableBlockConvertButton(_ref) {
  var isVisible = _ref.isVisible,
      isReusable = _ref.isReusable,
      onConvertToStatic = _ref.onConvertToStatic,
      onConvertToReusable = _ref.onConvertToReusable;

  if (!isVisible) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, !isReusable && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    className: "editor-block-settings-menu__control block-editor-block-settings-menu__control",
    icon: "controls-repeat",
    onClick: onConvertToReusable
  }, Object(external_this_wp_i18n_["__"])('Add to Reusable Blocks')), isReusable && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    className: "editor-block-settings-menu__control block-editor-block-settings-menu__control",
    icon: "controls-repeat",
    onClick: onConvertToStatic
  }, Object(external_this_wp_i18n_["__"])('Convert to Regular Block')));
}
/* harmony default export */ var reusable_block_convert_button = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var clientIds = _ref2.clientIds;

  var _select = select('core/block-editor'),
      getBlocksByClientId = _select.getBlocksByClientId,
      canInsertBlockType = _select.canInsertBlockType;

  var _select2 = select('core/editor'),
      getReusableBlock = _select2.__experimentalGetReusableBlock;

  var _select3 = select('core'),
      canUser = _select3.canUser;

  var blocks = getBlocksByClientId(clientIds);
  var isReusable = blocks.length === 1 && blocks[0] && Object(external_this_wp_blocks_["isReusableBlock"])(blocks[0]) && !!getReusableBlock(blocks[0].attributes.ref); // Show 'Convert to Regular Block' when selected block is a reusable block

  var isVisible = isReusable || // Hide 'Add to Reusable Blocks' when reusable blocks are disabled
  canInsertBlockType('core/block') && Object(external_lodash_["every"])(blocks, function (block) {
    return (// Guard against the case where a regular block has *just* been converted
      !!block && // Hide 'Add to Reusable Blocks' on invalid blocks
      block.isValid && // Hide 'Add to Reusable Blocks' when block doesn't support being made reusable
      Object(external_this_wp_blocks_["hasBlockSupport"])(block.name, 'reusable', true)
    );
  }) && // Hide 'Add to Reusable Blocks' when current doesn't have permission to do that
  !!canUser('create', 'blocks');
  return {
    isReusable: isReusable,
    isVisible: isVisible
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3) {
  var clientIds = _ref3.clientIds,
      _ref3$onToggle = _ref3.onToggle,
      onToggle = _ref3$onToggle === void 0 ? external_lodash_["noop"] : _ref3$onToggle;

  var _dispatch = dispatch('core/editor'),
      convertBlockToReusable = _dispatch.__experimentalConvertBlockToReusable,
      convertBlockToStatic = _dispatch.__experimentalConvertBlockToStatic;

  return {
    onConvertToStatic: function onConvertToStatic() {
      if (clientIds.length !== 1) {
        return;
      }

      convertBlockToStatic(clientIds[0]);
      onToggle();
    },
    onConvertToReusable: function onConvertToReusable() {
      convertBlockToReusable(clientIds);
      onToggle();
    }
  };
})])(ReusableBlockConvertButton));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/reusable-blocks-buttons/reusable-block-delete-button.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






function ReusableBlockDeleteButton(_ref) {
  var isVisible = _ref.isVisible,
      isDisabled = _ref.isDisabled,
      onDelete = _ref.onDelete;

  if (!isVisible) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    className: "editor-block-settings-menu__control block-editor-block-settings-menu__control",
    icon: "no",
    disabled: isDisabled,
    onClick: function onClick() {
      return onDelete();
    }
  }, Object(external_this_wp_i18n_["__"])('Remove from Reusable Blocks'));
}
/* harmony default export */ var reusable_block_delete_button = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var clientId = _ref2.clientId;

  var _select = select('core/block-editor'),
      getBlock = _select.getBlock;

  var _select2 = select('core'),
      canUser = _select2.canUser;

  var _select3 = select('core/editor'),
      getReusableBlock = _select3.__experimentalGetReusableBlock;

  var block = getBlock(clientId);
  var reusableBlock = block && Object(external_this_wp_blocks_["isReusableBlock"])(block) ? getReusableBlock(block.attributes.ref) : null;
  return {
    isVisible: !!reusableBlock && !!canUser('delete', 'blocks', reusableBlock.id),
    isDisabled: reusableBlock && reusableBlock.isTemporary
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3, _ref4) {
  var clientId = _ref3.clientId,
      _ref3$onToggle = _ref3.onToggle,
      onToggle = _ref3$onToggle === void 0 ? external_lodash_["noop"] : _ref3$onToggle;
  var select = _ref4.select;

  var _dispatch = dispatch('core/editor'),
      deleteReusableBlock = _dispatch.__experimentalDeleteReusableBlock;

  var _select4 = select('core/block-editor'),
      getBlock = _select4.getBlock;

  return {
    onDelete: function onDelete() {
      // TODO: Make this a <Confirm /> component or similar
      // eslint-disable-next-line no-alert
      var hasConfirmed = window.confirm(Object(external_this_wp_i18n_["__"])('Are you sure you want to delete this Reusable Block?\n\n' + 'It will be permanently removed from all posts and pages that use it.'));

      if (hasConfirmed) {
        var block = getBlock(clientId);
        deleteReusableBlock(block.attributes.ref);
        onToggle();
      }
    }
  };
})])(ReusableBlockDeleteButton));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/reusable-blocks-buttons/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function ReusableBlocksButtons(_ref) {
  var clientIds = _ref.clientIds;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalBlockSettingsMenuPluginsExtension"], null, function (_ref2) {
    var onClose = _ref2.onClose;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(reusable_block_convert_button, {
      clientIds: clientIds,
      onToggle: onClose
    }), clientIds.length === 1 && Object(external_this_wp_element_["createElement"])(reusable_block_delete_button, {
      clientId: clientIds[0],
      onToggle: onClose
    }));
  });
}

/* harmony default export */ var reusable_blocks_buttons = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getSelectedBlockClientIds = _select.getSelectedBlockClientIds;

  return {
    clientIds: getSelectedBlockClientIds()
  };
})(ReusableBlocksButtons));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/convert-to-group-buttons/icons.js


/**
 * WordPress dependencies
 */

var GroupSVG = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  width: "20",
  height: "20",
  viewBox: "0 0 20 20",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M8 5a1 1 0 0 0-1 1v3H6a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3h1a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H8zm3 6H7v2h4v-2zM9 9V7h4v2H9z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M1 3a2 2 0 0 0 1 1.732v10.536A2 2 0 1 0 4.732 18h10.536A2 2 0 1 0 18 15.268V4.732A2 2 0 1 0 15.268 2H4.732A2 2 0 0 0 1 3zm14.268 1H4.732A2.01 2.01 0 0 1 4 4.732v10.536c.304.175.557.428.732.732h10.536a2.01 2.01 0 0 1 .732-.732V4.732A2.01 2.01 0 0 1 15.268 4z"
}));
var Group = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
  icon: GroupSVG
});
var UngroupSVG = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  width: "20",
  height: "20",
  viewBox: "0 0 20 20",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M9 2H15C16.1 2 17 2.9 17 4V7C17 8.1 16.1 9 15 9H9C7.9 9 7 8.1 7 7V4C7 2.9 7.9 2 9 2ZM9 7H15V4H9V7Z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M5 11H11C12.1 11 13 11.9 13 13V16C13 17.1 12.1 18 11 18H5C3.9 18 3 17.1 3 16V13C3 11.9 3.9 11 5 11ZM5 16H11V13H5V16Z"
}));
var Ungroup = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
  icon: UngroupSVG
});

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/convert-to-group-buttons/convert-button.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function ConvertToGroupButton(_ref) {
  var onConvertToGroup = _ref.onConvertToGroup,
      onConvertFromGroup = _ref.onConvertFromGroup,
      _ref$isGroupable = _ref.isGroupable,
      isGroupable = _ref$isGroupable === void 0 ? false : _ref$isGroupable,
      _ref$isUngroupable = _ref.isUngroupable,
      isUngroupable = _ref$isUngroupable === void 0 ? false : _ref$isUngroupable;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, isGroupable && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    className: "editor-block-settings-menu__control block-editor-block-settings-menu__control",
    icon: Group,
    onClick: onConvertToGroup
  }, Object(external_this_wp_i18n_["_x"])('Group', 'verb')), isUngroupable && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    className: "editor-block-settings-menu__control block-editor-block-settings-menu__control",
    icon: Ungroup,
    onClick: onConvertFromGroup
  }, Object(external_this_wp_i18n_["_x"])('Ungroup', 'Ungrouping blocks from within a Group block back into individual blocks within the Editor ')));
}
/* harmony default export */ var convert_button = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var clientIds = _ref2.clientIds;

  var _select = select('core/block-editor'),
      getBlockRootClientId = _select.getBlockRootClientId,
      getBlocksByClientId = _select.getBlocksByClientId,
      canInsertBlockType = _select.canInsertBlockType;

  var _select2 = select('core/blocks'),
      getGroupingBlockName = _select2.getGroupingBlockName;

  var groupingBlockName = getGroupingBlockName();
  var rootClientId = clientIds && clientIds.length > 0 ? getBlockRootClientId(clientIds[0]) : undefined;
  var groupingBlockAvailable = canInsertBlockType(groupingBlockName, rootClientId);
  var blocksSelection = getBlocksByClientId(clientIds);
  var isSingleGroupingBlock = blocksSelection.length === 1 && blocksSelection[0] && blocksSelection[0].name === groupingBlockName; // Do we have
  // 1. Grouping block available to be inserted?
  // 2. One or more blocks selected
  // (we allow single Blocks to become groups unless
  // they are a soltiary group block themselves)

  var isGroupable = groupingBlockAvailable && blocksSelection.length && !isSingleGroupingBlock; // Do we have a single Group Block selected and does that group have inner blocks?

  var isUngroupable = isSingleGroupingBlock && !!blocksSelection[0].innerBlocks.length;
  return {
    isGroupable: isGroupable,
    isUngroupable: isUngroupable,
    blocksSelection: blocksSelection,
    groupingBlockName: groupingBlockName
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3) {
  var clientIds = _ref3.clientIds,
      _ref3$onToggle = _ref3.onToggle,
      onToggle = _ref3$onToggle === void 0 ? external_lodash_["noop"] : _ref3$onToggle,
      _ref3$blocksSelection = _ref3.blocksSelection,
      blocksSelection = _ref3$blocksSelection === void 0 ? [] : _ref3$blocksSelection,
      groupingBlockName = _ref3.groupingBlockName;

  var _dispatch = dispatch('core/block-editor'),
      replaceBlocks = _dispatch.replaceBlocks;

  return {
    onConvertToGroup: function onConvertToGroup() {
      if (!blocksSelection.length) {
        return;
      } // Activate the `transform` on the Grouping Block which does the conversion


      var newBlocks = Object(external_this_wp_blocks_["switchToBlockType"])(blocksSelection, groupingBlockName);

      if (newBlocks) {
        replaceBlocks(clientIds, newBlocks);
      }

      onToggle();
    },
    onConvertFromGroup: function onConvertFromGroup() {
      if (!blocksSelection.length) {
        return;
      }

      var innerBlocks = blocksSelection[0].innerBlocks;

      if (!innerBlocks.length) {
        return;
      }

      replaceBlocks(clientIds, innerBlocks);
      onToggle();
    }
  };
})])(ConvertToGroupButton));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/convert-to-group-buttons/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function ConvertToGroupButtons(_ref) {
  var clientIds = _ref.clientIds;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalBlockSettingsMenuPluginsExtension"], null, function (_ref2) {
    var onClose = _ref2.onClose;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(convert_button, {
      clientIds: clientIds,
      onToggle: onClose
    }));
  });
}

/* harmony default export */ var convert_to_group_buttons = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getSelectedBlockClientIds = _select.getSelectedBlockClientIds;

  return {
    clientIds: getSelectedBlockClientIds()
  };
})(ConvertToGroupButtons));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/reducer.js




/**
 * WordPress dependencies
 */

/**
 * Reducer returning an array of downloadable blocks.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

var reducer_downloadableBlocks = function downloadableBlocks() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    results: {},
    hasPermission: true,
    filterValue: undefined,
    isRequestingDownloadableBlocks: true,
    installedBlockTypes: []
  };
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'FETCH_DOWNLOADABLE_BLOCKS':
      return Object(objectSpread["a" /* default */])({}, state, {
        isRequestingDownloadableBlocks: true
      });

    case 'RECEIVE_DOWNLOADABLE_BLOCKS':
      return Object(objectSpread["a" /* default */])({}, state, {
        results: Object.assign({}, state.results, Object(defineProperty["a" /* default */])({}, action.filterValue, action.downloadableBlocks)),
        hasPermission: true,
        isRequestingDownloadableBlocks: false
      });

    case 'SET_INSTALL_BLOCKS_PERMISSION':
      return Object(objectSpread["a" /* default */])({}, state, {
        items: action.hasPermission ? state.items : [],
        hasPermission: action.hasPermission
      });

    case 'ADD_INSTALLED_BLOCK_TYPE':
      return Object(objectSpread["a" /* default */])({}, state, {
        installedBlockTypes: [].concat(Object(toConsumableArray["a" /* default */])(state.installedBlockTypes), [action.item])
      });

    case 'REMOVE_INSTALLED_BLOCK_TYPE':
      return Object(objectSpread["a" /* default */])({}, state, {
        installedBlockTypes: state.installedBlockTypes.filter(function (blockType) {
          return blockType.name !== action.item.name;
        })
      });
  }

  return state;
};
/* harmony default export */ var store_reducer = (Object(external_this_wp_data_["combineReducers"])({
  downloadableBlocks: reducer_downloadableBlocks
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/selectors.js
/**
 * External dependencies
 */

/**
 * Returns true if application is requesting for downloadable blocks.
 *
 * @param {Object} state       Global application state.
 *
 * @return {Array} Downloadable blocks
 */

function isRequestingDownloadableBlocks(state) {
  return state.downloadableBlocks.isRequestingDownloadableBlocks;
}
/**
 * Returns the available uninstalled blocks
 *
 * @param {Object} state       Global application state.
 * @param {string} filterValue Search string.
 *
 * @return {Array} Downloadable blocks
 */

function selectors_getDownloadableBlocks(state, filterValue) {
  if (!state.downloadableBlocks.results[filterValue]) {
    return [];
  }

  return state.downloadableBlocks.results[filterValue];
}
/**
 * Returns true if user has permission to install blocks.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} User has permission to install blocks.
 */

function selectors_hasInstallBlocksPermission(state) {
  return state.downloadableBlocks.hasPermission;
}
/**
 * Returns the block types that have been installed on the server.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} Block type items.
 */

function selectors_getInstalledBlockTypes(state) {
  return Object(external_lodash_["get"])(state, ['downloadableBlocks', 'installedBlockTypes'], []);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/controls.js




var controls_marked =
/*#__PURE__*/
regenerator_default.a.mark(loadAssets);

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Calls a selector using the current state.
 *
 * @param {string} storeName    Store name.
 * @param {string} selectorName Selector name.
 * @param {Array}  args         Selector arguments.
 *
 * @return {Object} Control descriptor.
 */

function controls_select(storeName, selectorName) {
  for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  return {
    type: 'SELECT',
    storeName: storeName,
    selectorName: selectorName,
    args: args
  };
}
/**
 * Calls a dispatcher using the current state.
 *
 * @param {string} storeName      Store name.
 * @param {string} dispatcherName Dispatcher name.
 * @param {Array}  args           Selector arguments.
 *
 * @return {Object} Control descriptor.
 */

function controls_dispatch(storeName, dispatcherName) {
  for (var _len2 = arguments.length, args = new Array(_len2 > 2 ? _len2 - 2 : 0), _key2 = 2; _key2 < _len2; _key2++) {
    args[_key2 - 2] = arguments[_key2];
  }

  return {
    type: 'DISPATCH',
    storeName: storeName,
    dispatcherName: dispatcherName,
    args: args
  };
}
/**
 * Trigger an API Fetch request.
 *
 * @param {Object} request API Fetch Request Object.
 *
 * @return {Object} Control descriptor.
 */

function apiFetch(request) {
  return {
    type: 'API_FETCH',
    request: request
  };
}
/**
 * Loads JavaScript
 *
 * @param {Array}    asset   The url for the JavaScript.
 * @param {Function} onLoad  Callback function on success.
 * @param {Function} onError Callback function on failure.
 */

var loadScript = function loadScript(asset, onLoad, onError) {
  if (!asset) {
    return;
  }

  var existing = document.querySelector("script[src=\"".concat(asset.src, "\"]"));

  if (existing) {
    existing.parentNode.removeChild(existing);
  }

  var script = document.createElement('script');
  script.src = typeof asset === 'string' ? asset : asset.src;
  script.onload = onLoad;
  script.onerror = onError;
  document.body.appendChild(script);
};
/**
 * Loads CSS file.
 *
 * @param {*} asset the url for the CSS file.
 */


var loadStyle = function loadStyle(asset) {
  if (!asset) {
    return;
  }

  var link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = typeof asset === 'string' ? asset : asset.src;
  document.body.appendChild(link);
};
/**
 * Load the asset files for a block
 *
 * @param {Array} assets A collection of URL for the assets.
 *
 * @return {Object} Control descriptor.
 */


function loadAssets(assets) {
  return regenerator_default.a.wrap(function loadAssets$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          return _context.abrupt("return", {
            type: 'LOAD_ASSETS',
            assets: assets
          });

        case 1:
        case "end":
          return _context.stop();
      }
    }
  }, controls_marked);
}
var controls_controls = {
  SELECT: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref) {
      var _registry$select;

      var storeName = _ref.storeName,
          selectorName = _ref.selectorName,
          args = _ref.args;
      return (_registry$select = registry.select(storeName))[selectorName].apply(_registry$select, Object(toConsumableArray["a" /* default */])(args));
    };
  }),
  DISPATCH: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref2) {
      var _registry$dispatch;

      var storeName = _ref2.storeName,
          dispatcherName = _ref2.dispatcherName,
          args = _ref2.args;
      return (_registry$dispatch = registry.dispatch(storeName))[dispatcherName].apply(_registry$dispatch, Object(toConsumableArray["a" /* default */])(args));
    };
  }),
  API_FETCH: function API_FETCH(_ref3) {
    var request = _ref3.request;
    return external_this_wp_apiFetch_default()(Object(objectSpread["a" /* default */])({}, request));
  },
  LOAD_ASSETS: function LOAD_ASSETS(_ref4) {
    var assets = _ref4.assets;
    return new Promise(function (resolve, reject) {
      if (Array.isArray(assets)) {
        var scriptsCount = 0;
        Object(external_lodash_["forEach"])(assets, function (asset) {
          if (asset.match(/\.js$/) !== null) {
            scriptsCount++;
            loadScript(asset, function () {
              scriptsCount--;

              if (scriptsCount === 0) {
                return resolve(scriptsCount);
              }
            }, reject);
          } else {
            loadStyle(asset);
          }
        });
      } else {
        loadScript(assets.editor_script, function () {
          return resolve(0);
        }, reject);
        loadStyle(assets.style);
      }
    });
  }
};
/* harmony default export */ var build_module_store_controls = (controls_controls);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/actions.js


var store_actions_marked =
/*#__PURE__*/
regenerator_default.a.mark(actions_downloadBlock),
    store_actions_marked2 =
/*#__PURE__*/
regenerator_default.a.mark(actions_installBlock),
    actions_marked3 =
/*#__PURE__*/
regenerator_default.a.mark(uninstallBlock);

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Returns an action object used in signalling that the downloadable blocks have been requested and is loading.
 *
 * @return {Object} Action object.
 */

function fetchDownloadableBlocks() {
  return {
    type: 'FETCH_DOWNLOADABLE_BLOCKS'
  };
}
/**
 * Returns an action object used in signalling that the downloadable blocks have been updated.
 *
 * @param {Array} downloadableBlocks Downloadable blocks.
 * @param {string} filterValue Search string.
 *
 * @return {Object} Action object.
 */

function receiveDownloadableBlocks(downloadableBlocks, filterValue) {
  return {
    type: 'RECEIVE_DOWNLOADABLE_BLOCKS',
    downloadableBlocks: downloadableBlocks,
    filterValue: filterValue
  };
}
/**
 * Returns an action object used in signalling that the user does not have permission to install blocks.
 *
 @param {boolean} hasPermission User has permission to install blocks.
 *
 * @return {Object} Action object.
 */

function setInstallBlocksPermission(hasPermission) {
  return {
    type: 'SET_INSTALL_BLOCKS_PERMISSION',
    hasPermission: hasPermission
  };
}
/**
 * Action triggered to download block assets.
 *
 * @param {Object} item The selected block item
 * @param {Function} onSuccess The callback function when the action has succeeded.
 * @param {Function} onError The callback function when the action has failed.
 */

function actions_downloadBlock(item, onSuccess, onError) {
  var registeredBlocks;
  return regenerator_default.a.wrap(function downloadBlock$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.prev = 0;

          if (item.assets.length) {
            _context.next = 3;
            break;
          }

          throw new Error('Block has no assets');

        case 3:
          _context.next = 5;
          return loadAssets(item.assets);

        case 5:
          registeredBlocks = Object(external_this_wp_blocks_["getBlockTypes"])();

          if (!registeredBlocks.length) {
            _context.next = 10;
            break;
          }

          onSuccess(item);
          _context.next = 11;
          break;

        case 10:
          throw new Error('Unable to get block types');

        case 11:
          _context.next = 17;
          break;

        case 13:
          _context.prev = 13;
          _context.t0 = _context["catch"](0);
          _context.next = 17;
          return onError(_context.t0);

        case 17:
        case "end":
          return _context.stop();
      }
    }
  }, store_actions_marked, null, [[0, 13]]);
}
/**
 * Action triggered to install a block plugin.
 *
 * @param {string} item The block item returned by search.
 * @param {Function} onSuccess The callback function when the action has succeeded.
 * @param {Function} onError The callback function when the action has failed.
 *
 */

function actions_installBlock(_ref, onSuccess, onError) {
  var id, name, response;
  return regenerator_default.a.wrap(function installBlock$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          id = _ref.id, name = _ref.name;
          _context2.prev = 1;
          _context2.next = 4;
          return apiFetch({
            path: '__experimental/block-directory/install',
            data: {
              slug: id
            },
            method: 'POST'
          });

        case 4:
          response = _context2.sent;

          if (!(response.success === false)) {
            _context2.next = 7;
            break;
          }

          throw new Error(response.errorMessage);

        case 7:
          _context2.next = 9;
          return addInstalledBlockType({
            id: id,
            name: name
          });

        case 9:
          onSuccess();
          _context2.next = 15;
          break;

        case 12:
          _context2.prev = 12;
          _context2.t0 = _context2["catch"](1);
          onError(_context2.t0);

        case 15:
        case "end":
          return _context2.stop();
      }
    }
  }, store_actions_marked2, null, [[1, 12]]);
}
/**
 * Action triggered to uninstall a block plugin.
 *
 * @param {string} item The block item returned by search.
 * @param {Function} onSuccess The callback function when the action has succeeded.
 * @param {Function} onError The callback function when the action has failed.
 *
 */

function uninstallBlock(_ref2, onSuccess, onError) {
  var id, name, response;
  return regenerator_default.a.wrap(function uninstallBlock$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          id = _ref2.id, name = _ref2.name;
          _context3.prev = 1;
          _context3.next = 4;
          return apiFetch({
            path: '__experimental/block-directory/uninstall',
            data: {
              slug: id
            },
            method: 'DELETE'
          });

        case 4:
          response = _context3.sent;

          if (!(response.success === false)) {
            _context3.next = 7;
            break;
          }

          throw new Error(response.errorMessage);

        case 7:
          _context3.next = 9;
          return removeInstalledBlockType({
            id: id,
            name: name
          });

        case 9:
          onSuccess();
          _context3.next = 15;
          break;

        case 12:
          _context3.prev = 12;
          _context3.t0 = _context3["catch"](1);
          onError(_context3.t0);

        case 15:
        case "end":
          return _context3.stop();
      }
    }
  }, actions_marked3, null, [[1, 12]]);
}
/**
 * Returns an action object used to add a newly installed block type.
 *
 * @param {string} item The block item with the block id and name.
 *
 * @return {Object} Action object.
 */

function addInstalledBlockType(item) {
  return {
    type: 'ADD_INSTALLED_BLOCK_TYPE',
    item: item
  };
}
/**
 * Returns an action object used to remove a newly installed block type.
 *
 * @param {string} item The block item with the block id and name.
 *
 * @return {Object} Action object.
 */

function removeInstalledBlockType(item) {
  return {
    type: 'REMOVE_INSTALLED_BLOCK_TYPE',
    item: item
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/resolvers.js


/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



/* harmony default export */ var resolvers = ({
  getDownloadableBlocks:
  /*#__PURE__*/
  regenerator_default.a.mark(function getDownloadableBlocks(filterValue) {
    var results, blocks;
    return regenerator_default.a.wrap(function getDownloadableBlocks$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            if (filterValue) {
              _context.next = 2;
              break;
            }

            return _context.abrupt("return");

          case 2:
            _context.prev = 2;
            _context.next = 5;
            return fetchDownloadableBlocks(filterValue);

          case 5:
            _context.next = 7;
            return apiFetch({
              path: "__experimental/block-directory/search?term=".concat(filterValue)
            });

          case 7:
            results = _context.sent;
            blocks = results.map(function (result) {
              return Object(external_lodash_["mapKeys"])(result, function (value, key) {
                return Object(external_lodash_["camelCase"])(key);
              });
            });
            _context.next = 11;
            return receiveDownloadableBlocks(blocks, filterValue);

          case 11:
            _context.next = 18;
            break;

          case 13:
            _context.prev = 13;
            _context.t0 = _context["catch"](2);

            if (!(_context.t0.code === 'rest_user_cannot_view')) {
              _context.next = 18;
              break;
            }

            _context.next = 18;
            return setInstallBlocksPermission(false);

          case 18:
          case "end":
            return _context.stop();
        }
      }
    }, getDownloadableBlocks, null, [[2, 13]]);
  }),
  hasInstallBlocksPermission:
  /*#__PURE__*/
  regenerator_default.a.mark(function hasInstallBlocksPermission() {
    return regenerator_default.a.wrap(function hasInstallBlocksPermission$(_context2) {
      while (1) {
        switch (_context2.prev = _context2.next) {
          case 0:
            _context2.prev = 0;
            _context2.next = 3;
            return apiFetch({
              path: "__experimental/block-directory/search?term="
            });

          case 3:
            _context2.next = 5;
            return setInstallBlocksPermission(true);

          case 5:
            _context2.next = 12;
            break;

          case 7:
            _context2.prev = 7;
            _context2.t0 = _context2["catch"](0);

            if (!(_context2.t0.code === 'rest_user_cannot_view')) {
              _context2.next = 12;
              break;
            }

            _context2.next = 12;
            return setInstallBlocksPermission(false);

          case 12:
          case "end":
            return _context2.stop();
        }
      }
    }, hasInstallBlocksPermission, null, [[0, 7]]);
  })
});

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */






/**
 * Module Constants
 */

var MODULE_KEY = 'core/block-directory';
/**
 * Block editor data store configuration.
 *
 * @see https://github.com/WordPress/gutenberg/blob/master/packages/data/README.md#registerStore
 *
 * @type {Object}
 */

var store_storeConfig = {
  reducer: store_reducer,
  selectors: store_selectors_namespaceObject,
  actions: store_actions_namespaceObject,
  controls: build_module_store_controls,
  resolvers: resolvers
};
var build_module_store_store = Object(external_this_wp_data_["registerStore"])(MODULE_KEY, store_storeConfig);
/* harmony default export */ var block_directory_build_module_store = (build_module_store_store);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/block-ratings/stars.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function Stars(_ref) {
  var rating = _ref.rating;
  var stars = Math.round(rating / 0.5) * 0.5;
  var fullStarCount = Math.floor(rating);
  var halfStarCount = Math.ceil(rating - fullStarCount);
  var emptyStarCount = 5 - (fullStarCount + halfStarCount);
  return Object(external_this_wp_element_["createElement"])("div", {
    "aria-label": Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('%s out of 5 stars'), stars)
  }, Object(external_lodash_["times"])(fullStarCount, function (i) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
      key: "full_stars_".concat(i),
      icon: "star-filled",
      size: 16
    });
  }), Object(external_lodash_["times"])(halfStarCount, function (i) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
      key: "half_stars_".concat(i),
      icon: "star-half",
      size: 16
    });
  }), Object(external_lodash_["times"])(emptyStarCount, function (i) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
      key: "empty_stars_".concat(i),
      icon: "star-empty",
      size: 16
    });
  }));
}

/* harmony default export */ var block_ratings_stars = (Stars);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/block-ratings/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var block_ratings_BlockRatings = function BlockRatings(_ref) {
  var rating = _ref.rating,
      ratingCount = _ref.ratingCount;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-block-ratings"
  }, Object(external_this_wp_element_["createElement"])(block_ratings_stars, {
    rating: rating
  }), Object(external_this_wp_element_["createElement"])("span", {
    className: "block-directory-block-ratings__rating-count",
    "aria-label": // translators: %d: number of ratings (number).
    Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('%d total rating', '%d total ratings', ratingCount), ratingCount)
  }, "(", ratingCount, ")"));
};
/* harmony default export */ var block_ratings = (block_ratings_BlockRatings);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-header/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function DownloadableBlockHeader(_ref) {
  var icon = _ref.icon,
      title = _ref.title,
      rating = _ref.rating,
      ratingCount = _ref.ratingCount,
      _onClick = _ref.onClick;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-header__row"
  }, icon.match(/\.(jpeg|jpg|gif|png)$/) !== null ? Object(external_this_wp_element_["createElement"])("img", {
    src: icon,
    alt: "block icon"
  }) : Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
    icon: icon,
    showColors: true
  })), Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-header__column"
  }, Object(external_this_wp_element_["createElement"])("span", {
    role: "heading",
    className: "block-directory-downloadable-block-header__title"
  }, title), Object(external_this_wp_element_["createElement"])(block_ratings, {
    rating: rating,
    ratingCount: ratingCount
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isDefault: true,
    onClick: function onClick(event) {
      event.preventDefault();

      _onClick();
    }
  }, Object(external_this_wp_i18n_["__"])('Add')));
}

/* harmony default export */ var downloadable_block_header = (DownloadableBlockHeader);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-author-info/index.js


/**
 * WordPress dependencies
 */



function DownloadableBlockAuthorInfo(_ref) {
  var author = _ref.author,
      authorBlockCount = _ref.authorBlockCount,
      authorBlockRating = _ref.authorBlockRating;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("span", {
    className: "block-directory-downloadable-block-author-info__content-author"
  }, Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Authored by %s'), author)), Object(external_this_wp_element_["createElement"])("span", {
    className: "block-directory-downloadable-block-author-info__content"
  }, Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('This author has %d block, with an average rating of %d.', 'This author has %d blocks, with an average rating of %d.', authorBlockCount), authorBlockCount, authorBlockRating)));
}

/* harmony default export */ var downloadable_block_author_info = (DownloadableBlockAuthorInfo);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-info/index.js


/**
 * WordPress dependencies
 */




function DownloadableBlockInfo(_ref) {
  var description = _ref.description,
      activeInstalls = _ref.activeInstalls,
      humanizedUpdated = _ref.humanizedUpdated;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("p", {
    className: "block-directory-downloadable-block-info__content"
  }, description), Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-info__row"
  }, Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-info__column"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
    icon: "chart-line"
  }), Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('%d active installation', '%d active installations', activeInstalls), activeInstalls)), Object(external_this_wp_element_["createElement"])("div", {
    className: "block-directory-downloadable-block-info__column"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Icon"], {
    icon: "update"
  }), Object(external_this_wp_element_["createElement"])("span", {
    "aria-label": Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Updated %s'), humanizedUpdated)
  }, humanizedUpdated))));
}

/* harmony default export */ var downloadable_block_info = (DownloadableBlockInfo);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-block-list-item/index.js


/**
 * Internal dependencies
 */




function DownloadableBlockListItem(_ref) {
  var item = _ref.item,
      onClick = _ref.onClick;
  var icon = item.icon,
      title = item.title,
      description = item.description,
      rating = item.rating,
      activeInstalls = item.activeInstalls,
      ratingCount = item.ratingCount,
      author = item.author,
      humanizedUpdated = item.humanizedUpdated,
      authorBlockCount = item.authorBlockCount,
      authorBlockRating = item.authorBlockRating;
  return Object(external_this_wp_element_["createElement"])("li", {
    className: "block-directory-downloadable-block-list-item"
  }, Object(external_this_wp_element_["createElement"])("article", {
    className: "block-directory-downloadable-block-list-item__panel"
  }, Object(external_this_wp_element_["createElement"])("header", {
    className: "block-directory-downloadable-block-list-item__header"
  }, Object(external_this_wp_element_["createElement"])(downloadable_block_header, {
    icon: icon,
    onClick: onClick,
    title: title,
    rating: rating,
    ratingCount: ratingCount
  })), Object(external_this_wp_element_["createElement"])("section", {
    className: "block-directory-downloadable-block-list-item__body"
  }, Object(external_this_wp_element_["createElement"])(downloadable_block_info, {
    activeInstalls: activeInstalls,
    description: description,
    humanizedUpdated: humanizedUpdated
  })), Object(external_this_wp_element_["createElement"])("footer", {
    className: "block-directory-downloadable-block-list-item__footer"
  }, Object(external_this_wp_element_["createElement"])(downloadable_block_author_info, {
    author: author,
    authorBlockCount: authorBlockCount,
    authorBlockRating: authorBlockRating
  }))));
}

/* harmony default export */ var downloadable_block_list_item = (DownloadableBlockListItem);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-list/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


var DOWNLOAD_ERROR_NOTICE_ID = 'block-download-error';
var INSTALL_ERROR_NOTICE_ID = 'block-install-error';

function DownloadableBlocksList(_ref) {
  var items = _ref.items,
      _ref$onHover = _ref.onHover,
      onHover = _ref$onHover === void 0 ? external_lodash_["noop"] : _ref$onHover,
      children = _ref.children,
      downloadAndInstallBlock = _ref.downloadAndInstallBlock;
  return (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */

    /* eslint-disable jsx-a11y/no-redundant-roles */
    Object(external_this_wp_element_["createElement"])("ul", {
      role: "list",
      className: "block-directory-downloadable-blocks-list"
    }, items && items.map(function (item) {
      return Object(external_this_wp_element_["createElement"])(downloadable_block_list_item, {
        key: item.id,
        className: Object(external_this_wp_blocks_["getBlockMenuDefaultClassName"])(item.id),
        icons: item.icons,
        onClick: function onClick() {
          downloadAndInstallBlock(item);
          onHover(null);
        },
        onFocus: function onFocus() {
          return onHover(item);
        },
        onMouseEnter: function onMouseEnter() {
          return onHover(item);
        },
        onMouseLeave: function onMouseLeave() {
          return onHover(null);
        },
        onBlur: function onBlur() {
          return onHover(null);
        },
        item: item
      });
    }), children)
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
}

/* harmony default export */ var downloadable_blocks_list = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withDispatch"])(function (dispatch, props) {
  var _dispatch = dispatch('core/block-directory'),
      installBlock = _dispatch.installBlock,
      downloadBlock = _dispatch.downloadBlock;

  var _dispatch2 = dispatch('core/notices'),
      createErrorNotice = _dispatch2.createErrorNotice,
      removeNotice = _dispatch2.removeNotice;

  var _dispatch3 = dispatch('core/block-editor'),
      removeBlocks = _dispatch3.removeBlocks;

  var onSelect = props.onSelect;
  return {
    downloadAndInstallBlock: function downloadAndInstallBlock(item) {
      var onDownloadError = function onDownloadError() {
        createErrorNotice(Object(external_this_wp_i18n_["__"])('Block previews can’t load.'), {
          id: DOWNLOAD_ERROR_NOTICE_ID,
          actions: [{
            label: Object(external_this_wp_i18n_["__"])('Retry'),
            onClick: function onClick() {
              removeNotice(DOWNLOAD_ERROR_NOTICE_ID);
              downloadBlock(item, onSuccess, onDownloadError);
            }
          }]
        });
      };

      var onSuccess = function onSuccess() {
        var createdBlock = onSelect(item);

        var onInstallBlockError = function onInstallBlockError() {
          createErrorNotice(Object(external_this_wp_i18n_["__"])('Block previews can\'t install.'), {
            id: INSTALL_ERROR_NOTICE_ID,
            actions: [{
              label: Object(external_this_wp_i18n_["__"])('Retry'),
              onClick: function onClick() {
                removeNotice(INSTALL_ERROR_NOTICE_ID);
                installBlock(item, external_lodash_["noop"], onInstallBlockError);
              }
            }, {
              label: Object(external_this_wp_i18n_["__"])('Remove'),
              onClick: function onClick() {
                removeNotice(INSTALL_ERROR_NOTICE_ID);
                removeBlocks(createdBlock.clientId);
                Object(external_this_wp_blocks_["unregisterBlockType"])(item.name);
              }
            }]
          });
        };

        installBlock(item, external_lodash_["noop"], onInstallBlockError);
      };

      downloadBlock(item, onSuccess, onDownloadError);
    }
  };
}))(DownloadableBlocksList));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/components/downloadable-blocks-panel/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function DownloadableBlocksPanel(_ref) {
  var downloadableItems = _ref.downloadableItems,
      onSelect = _ref.onSelect,
      onHover = _ref.onHover,
      hasPermission = _ref.hasPermission,
      isLoading = _ref.isLoading,
      isWaiting = _ref.isWaiting,
      debouncedSpeak = _ref.debouncedSpeak;

  if (!hasPermission) {
    debouncedSpeak(Object(external_this_wp_i18n_["__"])('No blocks found in your library. Please contact your site administrator to install new blocks.'));
    return Object(external_this_wp_element_["createElement"])("p", {
      className: "block-directory-downloadable-blocks-panel__description has-no-results"
    }, Object(external_this_wp_i18n_["__"])('No blocks found in your library.'), Object(external_this_wp_element_["createElement"])("br", null), Object(external_this_wp_i18n_["__"])('Please contact your site administrator to install new blocks.'));
  }

  if (isLoading || isWaiting) {
    return Object(external_this_wp_element_["createElement"])("p", {
      className: "block-directory-downloadable-blocks-panel__description has-no-results"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null));
  }

  if (!downloadableItems.length) {
    return Object(external_this_wp_element_["createElement"])("p", {
      className: "block-directory-downloadable-blocks-panel__description has-no-results"
    }, Object(external_this_wp_i18n_["__"])('No blocks found in your library.'));
  }

  var resultsFoundMessage = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('No blocks found in your library. We did find %d block available for download.', 'No blocks found in your library. We did find %d blocks available for download.', downloadableItems.length), downloadableItems.length);
  debouncedSpeak(resultsFoundMessage);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("p", {
    className: "block-directory-downloadable-blocks-panel__description"
  }, Object(external_this_wp_i18n_["__"])('No blocks found in your library. These blocks can be downloaded and installed:')), Object(external_this_wp_element_["createElement"])(downloadable_blocks_list, {
    items: downloadableItems,
    onSelect: onSelect,
    onHover: onHover
  }));
}

/* harmony default export */ var downloadable_blocks_panel = (Object(external_this_wp_compose_["compose"])([external_this_wp_components_["withSpokenMessages"], Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var filterValue = _ref2.filterValue;

  var _select = select('core/block-directory'),
      getDownloadableBlocks = _select.getDownloadableBlocks,
      hasInstallBlocksPermission = _select.hasInstallBlocksPermission,
      isRequestingDownloadableBlocks = _select.isRequestingDownloadableBlocks;

  var hasPermission = hasInstallBlocksPermission();
  var downloadableItems = hasPermission ? getDownloadableBlocks(filterValue) : [];
  var isLoading = isRequestingDownloadableBlocks();
  return {
    downloadableItems: downloadableItems,
    hasPermission: hasPermission,
    isLoading: isLoading
  };
})])(DownloadableBlocksPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-directory/build-module/index.js
/**
 * Internal dependencies
 */



// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/inserter-menu-downloadable-blocks-panel/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





function InserterMenuDownloadableBlocksPanel() {
  var _useState = Object(external_this_wp_element_["useState"])(''),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      debouncedFilterValue = _useState2[0],
      setFilterValue = _useState2[1];

  var debouncedSetFilterValue = Object(external_lodash_["debounce"])(setFilterValue, 400);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalInserterMenuExtension"], null, function (_ref) {
    var onSelect = _ref.onSelect,
        onHover = _ref.onHover,
        filterValue = _ref.filterValue,
        hasItems = _ref.hasItems;

    if (hasItems || !filterValue) {
      return null;
    }

    if (debouncedFilterValue !== filterValue) {
      debouncedSetFilterValue(filterValue);
    }

    return Object(external_this_wp_element_["createElement"])(downloadable_blocks_panel, {
      onSelect: onSelect,
      onHover: onHover,
      filterValue: debouncedFilterValue,
      isWaiting: filterValue !== debouncedFilterValue
    });
  });
}

/* harmony default export */ var inserter_menu_downloadable_blocks_panel = (InserterMenuDownloadableBlocksPanel);

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







var fetchLinkSuggestions =
/*#__PURE__*/
function () {
  var _ref = Object(asyncToGenerator["a" /* default */])(
  /*#__PURE__*/
  regenerator_default.a.mark(function _callee(search) {
    var posts;
    return regenerator_default.a.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            _context.next = 2;
            return external_this_wp_apiFetch_default()({
              path: Object(external_this_wp_url_["addQueryArgs"])('/wp/v2/search', {
                search: search,
                per_page: 20,
                type: 'post'
              })
            });

          case 2:
            posts = _context.sent;
            return _context.abrupt("return", Object(external_lodash_["map"])(posts, function (post) {
              return {
                id: post.id,
                url: post.url,
                title: Object(external_this_wp_htmlEntities_["decodeEntities"])(post.title) || Object(external_this_wp_i18n_["__"])('(no title)')
              };
            }));

          case 4:
          case "end":
            return _context.stop();
        }
      }
    }, _callee);
  }));

  return function fetchLinkSuggestions(_x) {
    return _ref.apply(this, arguments);
  };
}();

var UNINSTALL_ERROR_NOTICE_ID = 'block-uninstall-error';

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
    value: function getBlockEditorSettings(settings, reusableBlocks, hasUploadPermissions, canUserUseUnfilteredHTML) {
      return Object(objectSpread["a" /* default */])({}, Object(external_lodash_["pick"])(settings, ['alignWide', 'allowedBlockTypes', '__experimentalPreferredStyleVariations', 'availableLegacyWidgets', 'bodyPlaceholder', 'codeEditingEnabled', 'colors', 'disableCustomColors', 'disableCustomFontSizes', 'focusMode', 'fontSizes', 'hasFixedToolbar', 'hasPermissionsToManageWidgets', 'imageSizes', 'isRTL', 'maxWidth', 'styles', 'template', 'templateLock', 'titlePlaceholder', 'onUpdateDefaultBlockStyles', '__experimentalEnableLegacyWidgetBlock', '__experimentalEnableMenuBlock', '__experimentalBlockDirectory', 'showInserterHelpPanel']), {
        __experimentalReusableBlocks: reusableBlocks,
        __experimentalMediaUpload: hasUploadPermissions ? media_upload : undefined,
        __experimentalFetchLinkSuggestions: fetchLinkSuggestions,
        __experimentalCanUserUseUnfilteredHTML: canUserUseUnfilteredHTML
      });
    }
  }, {
    key: "componentDidMount",
    value: function componentDidMount() {
      this.props.updateEditorSettings(this.props.settings);

      if (!this.props.settings.styles) {
        return;
      }

      var updatedStyles = Object(external_this_wp_blockEditor_["transformStyles"])(this.props.settings.styles, '.editor-styles-wrapper');
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
      var _this2 = this;

      if (this.props.settings !== prevProps.settings) {
        this.props.updateEditorSettings(this.props.settings);
      } // When a block is installed from the inserter and is unused,
      // it is removed when saving the post.
      // Todo: move this to the edit-post package into a separate component.


      if (!Object(external_lodash_["isEqual"])(this.props.downloadableBlocksToUninstall, prevProps.downloadableBlocksToUninstall)) {
        this.props.downloadableBlocksToUninstall.forEach(function (blockType) {
          _this2.props.uninstallBlock(blockType, external_lodash_["noop"], function () {
            _this2.props.createWarningNotice(Object(external_this_wp_i18n_["__"])('Block previews can\'t uninstall.'), {
              id: UNINSTALL_ERROR_NOTICE_ID
            });
          });

          Object(external_this_wp_blocks_["unregisterBlockType"])(blockType.name);
        });
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.props.tearDownEditor();
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          canUserUseUnfilteredHTML = _this$props.canUserUseUnfilteredHTML,
          children = _this$props.children,
          blocks = _this$props.blocks,
          resetEditorBlocks = _this$props.resetEditorBlocks,
          isReady = _this$props.isReady,
          settings = _this$props.settings,
          reusableBlocks = _this$props.reusableBlocks,
          resetEditorBlocksWithoutUndoLevel = _this$props.resetEditorBlocksWithoutUndoLevel,
          hasUploadPermissions = _this$props.hasUploadPermissions;

      if (!isReady) {
        return null;
      }

      var editorSettings = this.getBlockEditorSettings(settings, reusableBlocks, hasUploadPermissions, canUserUseUnfilteredHTML);
      return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockEditorProvider"], {
        value: blocks,
        onInput: resetEditorBlocksWithoutUndoLevel,
        onChange: resetEditorBlocks,
        settings: editorSettings,
        useSubRegistry: false
      }, children, Object(external_this_wp_element_["createElement"])(reusable_blocks_buttons, null), Object(external_this_wp_element_["createElement"])(convert_to_group_buttons, null), editorSettings.__experimentalBlockDirectory && Object(external_this_wp_element_["createElement"])(inserter_menu_downloadable_blocks_panel, null));
    }
  }]);

  return EditorProvider;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var provider = (Object(external_this_wp_compose_["compose"])([with_registry_provider, Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      canUserUseUnfilteredHTML = _select.canUserUseUnfilteredHTML,
      isEditorReady = _select.__unstableIsEditorReady,
      getEditorBlocks = _select.getEditorBlocks,
      __experimentalGetReusableBlocks = _select.__experimentalGetReusableBlocks;

  var _select2 = select('core'),
      canUser = _select2.canUser;

  var _select3 = select('core/block-directory'),
      getInstalledBlockTypes = _select3.getInstalledBlockTypes;

  var _select4 = select('core/block-editor'),
      getBlocks = _select4.getBlocks;

  var downloadableBlocksToUninstall = Object(external_lodash_["differenceBy"])(getInstalledBlockTypes(), getBlocks(), 'name');
  return {
    canUserUseUnfilteredHTML: canUserUseUnfilteredHTML(),
    isReady: isEditorReady(),
    blocks: getEditorBlocks(),
    reusableBlocks: __experimentalGetReusableBlocks(),
    hasUploadPermissions: Object(external_lodash_["defaultTo"])(canUser('create', 'media'), true),
    downloadableBlocksToUninstall: downloadableBlocksToUninstall
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      setupEditor = _dispatch.setupEditor,
      updatePostLock = _dispatch.updatePostLock,
      resetEditorBlocks = _dispatch.resetEditorBlocks,
      updateEditorSettings = _dispatch.updateEditorSettings,
      __experimentalTearDownEditor = _dispatch.__experimentalTearDownEditor;

  var _dispatch2 = dispatch('core/notices'),
      createWarningNotice = _dispatch2.createWarningNotice;

  var _dispatch3 = dispatch('core/block-directory'),
      uninstallBlock = _dispatch3.uninstallBlock;

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
    tearDownEditor: __experimentalTearDownEditor,
    uninstallBlock: uninstallBlock
  };
})])(provider_EditorProvider));

// EXTERNAL MODULE: external {"this":["wp","serverSideRender"]}
var external_this_wp_serverSideRender_ = __webpack_require__(58);
var external_this_wp_serverSideRender_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_serverSideRender_);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/deprecated.js


// Block Creation Components

/**
 * WordPress dependencies
 */





function deprecateComponent(name, Wrapped) {
  var staticsToHoist = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
  var Component = Object(external_this_wp_element_["forwardRef"])(function (props, ref) {
    external_this_wp_deprecated_default()('wp.editor.' + name, {
      alternative: 'wp.blockEditor.' + name
    });
    return Object(external_this_wp_element_["createElement"])(Wrapped, Object(esm_extends["a" /* default */])({
      ref: ref
    }, props));
  });
  staticsToHoist.forEach(function (staticName) {
    Component[staticName] = deprecateComponent(name + '.' + staticName, Wrapped[staticName]);
  });
  return Component;
}

function deprecateFunction(name, func) {
  return function () {
    external_this_wp_deprecated_default()('wp.editor.' + name, {
      alternative: 'wp.blockEditor.' + name
    });
    return func.apply(void 0, arguments);
  };
}

var RichText = deprecateComponent('RichText', external_this_wp_blockEditor_["RichText"], ['Content']);
RichText.isEmpty = deprecateFunction('RichText.isEmpty', external_this_wp_blockEditor_["RichText"].isEmpty);

var Autocomplete = deprecateComponent('Autocomplete', external_this_wp_blockEditor_["Autocomplete"]);
var AlignmentToolbar = deprecateComponent('AlignmentToolbar', external_this_wp_blockEditor_["AlignmentToolbar"]);
var BlockAlignmentToolbar = deprecateComponent('BlockAlignmentToolbar', external_this_wp_blockEditor_["BlockAlignmentToolbar"]);
var BlockControls = deprecateComponent('BlockControls', external_this_wp_blockEditor_["BlockControls"], ['Slot']);
var BlockEdit = deprecateComponent('BlockEdit', external_this_wp_blockEditor_["BlockEdit"]);
var BlockEditorKeyboardShortcuts = deprecateComponent('BlockEditorKeyboardShortcuts', external_this_wp_blockEditor_["BlockEditorKeyboardShortcuts"]);
var BlockFormatControls = deprecateComponent('BlockFormatControls', external_this_wp_blockEditor_["BlockFormatControls"], ['Slot']);
var BlockIcon = deprecateComponent('BlockIcon', external_this_wp_blockEditor_["BlockIcon"]);
var BlockInspector = deprecateComponent('BlockInspector', external_this_wp_blockEditor_["BlockInspector"]);
var BlockList = deprecateComponent('BlockList', external_this_wp_blockEditor_["BlockList"]);
var BlockMover = deprecateComponent('BlockMover', external_this_wp_blockEditor_["BlockMover"]);
var BlockNavigationDropdown = deprecateComponent('BlockNavigationDropdown', external_this_wp_blockEditor_["BlockNavigationDropdown"]);
var BlockSelectionClearer = deprecateComponent('BlockSelectionClearer', external_this_wp_blockEditor_["BlockSelectionClearer"]);
var BlockSettingsMenu = deprecateComponent('BlockSettingsMenu', external_this_wp_blockEditor_["BlockSettingsMenu"]);
var BlockTitle = deprecateComponent('BlockTitle', external_this_wp_blockEditor_["BlockTitle"]);
var BlockToolbar = deprecateComponent('BlockToolbar', external_this_wp_blockEditor_["BlockToolbar"]);
var ColorPalette = deprecateComponent('ColorPalette', external_this_wp_blockEditor_["ColorPalette"]);
var ContrastChecker = deprecateComponent('ContrastChecker', external_this_wp_blockEditor_["ContrastChecker"]);
var CopyHandler = deprecateComponent('CopyHandler', external_this_wp_blockEditor_["CopyHandler"]);
var DefaultBlockAppender = deprecateComponent('DefaultBlockAppender', external_this_wp_blockEditor_["DefaultBlockAppender"]);
var FontSizePicker = deprecateComponent('FontSizePicker', external_this_wp_blockEditor_["FontSizePicker"]);
var Inserter = deprecateComponent('Inserter', external_this_wp_blockEditor_["Inserter"]);
var InnerBlocks = deprecateComponent('InnerBlocks', external_this_wp_blockEditor_["InnerBlocks"], ['ButtonBlockAppender', 'DefaultBlockAppender', 'Content']);
var InspectorAdvancedControls = deprecateComponent('InspectorAdvancedControls', external_this_wp_blockEditor_["InspectorAdvancedControls"], ['Slot']);
var InspectorControls = deprecateComponent('InspectorControls', external_this_wp_blockEditor_["InspectorControls"], ['Slot']);
var PanelColorSettings = deprecateComponent('PanelColorSettings', external_this_wp_blockEditor_["PanelColorSettings"]);
var PlainText = deprecateComponent('PlainText', external_this_wp_blockEditor_["PlainText"]);
var RichTextShortcut = deprecateComponent('RichTextShortcut', external_this_wp_blockEditor_["RichTextShortcut"]);
var RichTextToolbarButton = deprecateComponent('RichTextToolbarButton', external_this_wp_blockEditor_["RichTextToolbarButton"]);
var __unstableRichTextInputEvent = deprecateComponent('__unstableRichTextInputEvent', external_this_wp_blockEditor_["__unstableRichTextInputEvent"]);
var MediaPlaceholder = deprecateComponent('MediaPlaceholder', external_this_wp_blockEditor_["MediaPlaceholder"]);
var MediaUpload = deprecateComponent('MediaUpload', external_this_wp_blockEditor_["MediaUpload"]);
var MediaUploadCheck = deprecateComponent('MediaUploadCheck', external_this_wp_blockEditor_["MediaUploadCheck"]);
var MultiBlocksSwitcher = deprecateComponent('MultiBlocksSwitcher', external_this_wp_blockEditor_["MultiBlocksSwitcher"]);
var MultiSelectScrollIntoView = deprecateComponent('MultiSelectScrollIntoView', external_this_wp_blockEditor_["MultiSelectScrollIntoView"]);
var NavigableToolbar = deprecateComponent('NavigableToolbar', external_this_wp_blockEditor_["NavigableToolbar"]);
var ObserveTyping = deprecateComponent('ObserveTyping', external_this_wp_blockEditor_["ObserveTyping"]);
var PreserveScrollInReorder = deprecateComponent('PreserveScrollInReorder', external_this_wp_blockEditor_["PreserveScrollInReorder"]);
var SkipToSelectedBlock = deprecateComponent('SkipToSelectedBlock', external_this_wp_blockEditor_["SkipToSelectedBlock"]);
var URLInput = deprecateComponent('URLInput', external_this_wp_blockEditor_["URLInput"]);
var URLInputButton = deprecateComponent('URLInputButton', external_this_wp_blockEditor_["URLInputButton"]);
var URLPopover = deprecateComponent('URLPopover', external_this_wp_blockEditor_["URLPopover"]);
var Warning = deprecateComponent('Warning', external_this_wp_blockEditor_["Warning"]);
var WritingFlow = deprecateComponent('WritingFlow', external_this_wp_blockEditor_["WritingFlow"]);
var createCustomColorsHOC = deprecateFunction('createCustomColorsHOC', external_this_wp_blockEditor_["createCustomColorsHOC"]);
var getColorClassName = deprecateFunction('getColorClassName', external_this_wp_blockEditor_["getColorClassName"]);
var getColorObjectByAttributeValues = deprecateFunction('getColorObjectByAttributeValues', external_this_wp_blockEditor_["getColorObjectByAttributeValues"]);
var getColorObjectByColorValue = deprecateFunction('getColorObjectByColorValue', external_this_wp_blockEditor_["getColorObjectByColorValue"]);
var getFontSize = deprecateFunction('getFontSize', external_this_wp_blockEditor_["getFontSize"]);
var getFontSizeClass = deprecateFunction('getFontSizeClass', external_this_wp_blockEditor_["getFontSizeClass"]);
var withColorContext = deprecateFunction('withColorContext', external_this_wp_blockEditor_["withColorContext"]);
var withColors = deprecateFunction('withColors', external_this_wp_blockEditor_["withColors"]);
var withFontSizes = deprecateFunction('withFontSizes', external_this_wp_blockEditor_["withFontSizes"]);

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



function setDefaultCompleters() {
  var completers = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var blockName = arguments.length > 1 ? arguments[1] : undefined;
  // Provide copies so filters may directly modify them.
  completers.push(Object(external_lodash_["clone"])(autocompleters_user)); // Add blocks autocompleter for Paragraph block

  if (blockName === Object(external_this_wp_blocks_["getDefaultBlockName"])()) {
    completers.push(Object(external_lodash_["clone"])(autocompleters_block));
  }

  return completers;
}

Object(external_this_wp_hooks_["addFilter"])('editor.Autocomplete.completers', 'editor/autocompleters/set-default-completers', setDefaultCompleters);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/hooks/index.js
/**
 * Internal dependencies
 */


// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/index.js
/* concated harmony reexport AutosaveMonitor */__webpack_require__.d(__webpack_exports__, "AutosaveMonitor", function() { return autosave_monitor; });
/* concated harmony reexport DocumentOutline */__webpack_require__.d(__webpack_exports__, "DocumentOutline", function() { return document_outline; });
/* concated harmony reexport DocumentOutlineCheck */__webpack_require__.d(__webpack_exports__, "DocumentOutlineCheck", function() { return check; });
/* concated harmony reexport VisualEditorGlobalKeyboardShortcuts */__webpack_require__.d(__webpack_exports__, "VisualEditorGlobalKeyboardShortcuts", function() { return visual_editor_shortcuts; });
/* concated harmony reexport EditorGlobalKeyboardShortcuts */__webpack_require__.d(__webpack_exports__, "EditorGlobalKeyboardShortcuts", function() { return EditorGlobalKeyboardShortcuts; });
/* concated harmony reexport TextEditorGlobalKeyboardShortcuts */__webpack_require__.d(__webpack_exports__, "TextEditorGlobalKeyboardShortcuts", function() { return TextEditorGlobalKeyboardShortcuts; });
/* concated harmony reexport EditorHistoryRedo */__webpack_require__.d(__webpack_exports__, "EditorHistoryRedo", function() { return editor_history_redo; });
/* concated harmony reexport EditorHistoryUndo */__webpack_require__.d(__webpack_exports__, "EditorHistoryUndo", function() { return editor_history_undo; });
/* concated harmony reexport EditorNotices */__webpack_require__.d(__webpack_exports__, "EditorNotices", function() { return editor_notices; });
/* concated harmony reexport ErrorBoundary */__webpack_require__.d(__webpack_exports__, "ErrorBoundary", function() { return error_boundary; });
/* concated harmony reexport LocalAutosaveMonitor */__webpack_require__.d(__webpack_exports__, "LocalAutosaveMonitor", function() { return local_autosave_monitor; });
/* concated harmony reexport PageAttributesCheck */__webpack_require__.d(__webpack_exports__, "PageAttributesCheck", function() { return page_attributes_check; });
/* concated harmony reexport PageAttributesOrder */__webpack_require__.d(__webpack_exports__, "PageAttributesOrder", function() { return page_attributes_order; });
/* concated harmony reexport PageAttributesParent */__webpack_require__.d(__webpack_exports__, "PageAttributesParent", function() { return page_attributes_parent; });
/* concated harmony reexport PageTemplate */__webpack_require__.d(__webpack_exports__, "PageTemplate", function() { return page_attributes_template; });
/* concated harmony reexport PostAuthor */__webpack_require__.d(__webpack_exports__, "PostAuthor", function() { return post_author; });
/* concated harmony reexport PostAuthorCheck */__webpack_require__.d(__webpack_exports__, "PostAuthorCheck", function() { return post_author_check; });
/* concated harmony reexport PostComments */__webpack_require__.d(__webpack_exports__, "PostComments", function() { return post_comments; });
/* concated harmony reexport PostExcerpt */__webpack_require__.d(__webpack_exports__, "PostExcerpt", function() { return post_excerpt; });
/* concated harmony reexport PostExcerptCheck */__webpack_require__.d(__webpack_exports__, "PostExcerptCheck", function() { return post_excerpt_check; });
/* concated harmony reexport PostFeaturedImage */__webpack_require__.d(__webpack_exports__, "PostFeaturedImage", function() { return post_featured_image; });
/* concated harmony reexport PostFeaturedImageCheck */__webpack_require__.d(__webpack_exports__, "PostFeaturedImageCheck", function() { return post_featured_image_check; });
/* concated harmony reexport PostFormat */__webpack_require__.d(__webpack_exports__, "PostFormat", function() { return post_format; });
/* concated harmony reexport PostFormatCheck */__webpack_require__.d(__webpack_exports__, "PostFormatCheck", function() { return post_format_check; });
/* concated harmony reexport PostLastRevision */__webpack_require__.d(__webpack_exports__, "PostLastRevision", function() { return post_last_revision; });
/* concated harmony reexport PostLastRevisionCheck */__webpack_require__.d(__webpack_exports__, "PostLastRevisionCheck", function() { return post_last_revision_check; });
/* concated harmony reexport PostLockedModal */__webpack_require__.d(__webpack_exports__, "PostLockedModal", function() { return post_locked_modal; });
/* concated harmony reexport PostPendingStatus */__webpack_require__.d(__webpack_exports__, "PostPendingStatus", function() { return post_pending_status; });
/* concated harmony reexport PostPendingStatusCheck */__webpack_require__.d(__webpack_exports__, "PostPendingStatusCheck", function() { return post_pending_status_check; });
/* concated harmony reexport PostPingbacks */__webpack_require__.d(__webpack_exports__, "PostPingbacks", function() { return post_pingbacks; });
/* concated harmony reexport PostPreviewButton */__webpack_require__.d(__webpack_exports__, "PostPreviewButton", function() { return post_preview_button; });
/* concated harmony reexport PostPublishButton */__webpack_require__.d(__webpack_exports__, "PostPublishButton", function() { return post_publish_button; });
/* concated harmony reexport PostPublishButtonLabel */__webpack_require__.d(__webpack_exports__, "PostPublishButtonLabel", function() { return post_publish_button_label; });
/* concated harmony reexport PostPublishPanel */__webpack_require__.d(__webpack_exports__, "PostPublishPanel", function() { return post_publish_panel; });
/* concated harmony reexport PostSavedState */__webpack_require__.d(__webpack_exports__, "PostSavedState", function() { return post_saved_state; });
/* concated harmony reexport PostSchedule */__webpack_require__.d(__webpack_exports__, "PostSchedule", function() { return post_schedule; });
/* concated harmony reexport PostScheduleCheck */__webpack_require__.d(__webpack_exports__, "PostScheduleCheck", function() { return post_schedule_check; });
/* concated harmony reexport PostScheduleLabel */__webpack_require__.d(__webpack_exports__, "PostScheduleLabel", function() { return post_schedule_label; });
/* concated harmony reexport PostSticky */__webpack_require__.d(__webpack_exports__, "PostSticky", function() { return post_sticky; });
/* concated harmony reexport PostStickyCheck */__webpack_require__.d(__webpack_exports__, "PostStickyCheck", function() { return post_sticky_check; });
/* concated harmony reexport PostSwitchToDraftButton */__webpack_require__.d(__webpack_exports__, "PostSwitchToDraftButton", function() { return post_switch_to_draft_button; });
/* concated harmony reexport PostTaxonomies */__webpack_require__.d(__webpack_exports__, "PostTaxonomies", function() { return post_taxonomies; });
/* concated harmony reexport PostTaxonomiesCheck */__webpack_require__.d(__webpack_exports__, "PostTaxonomiesCheck", function() { return post_taxonomies_check; });
/* concated harmony reexport PostTextEditor */__webpack_require__.d(__webpack_exports__, "PostTextEditor", function() { return post_text_editor; });
/* concated harmony reexport PostTitle */__webpack_require__.d(__webpack_exports__, "PostTitle", function() { return post_title; });
/* concated harmony reexport PostTrash */__webpack_require__.d(__webpack_exports__, "PostTrash", function() { return post_trash; });
/* concated harmony reexport PostTrashCheck */__webpack_require__.d(__webpack_exports__, "PostTrashCheck", function() { return post_trash_check; });
/* concated harmony reexport PostTypeSupportCheck */__webpack_require__.d(__webpack_exports__, "PostTypeSupportCheck", function() { return post_type_support_check; });
/* concated harmony reexport PostVisibility */__webpack_require__.d(__webpack_exports__, "PostVisibility", function() { return post_visibility; });
/* concated harmony reexport PostVisibilityLabel */__webpack_require__.d(__webpack_exports__, "PostVisibilityLabel", function() { return post_visibility_label; });
/* concated harmony reexport PostVisibilityCheck */__webpack_require__.d(__webpack_exports__, "PostVisibilityCheck", function() { return post_visibility_check; });
/* concated harmony reexport TableOfContents */__webpack_require__.d(__webpack_exports__, "TableOfContents", function() { return table_of_contents; });
/* concated harmony reexport UnsavedChangesWarning */__webpack_require__.d(__webpack_exports__, "UnsavedChangesWarning", function() { return unsaved_changes_warning; });
/* concated harmony reexport WordCount */__webpack_require__.d(__webpack_exports__, "WordCount", function() { return word_count; });
/* concated harmony reexport EditorProvider */__webpack_require__.d(__webpack_exports__, "EditorProvider", function() { return provider; });
/* concated harmony reexport blockAutocompleter */__webpack_require__.d(__webpack_exports__, "blockAutocompleter", function() { return autocompleters_block; });
/* concated harmony reexport userAutocompleter */__webpack_require__.d(__webpack_exports__, "userAutocompleter", function() { return autocompleters_user; });
/* concated harmony reexport ServerSideRender */__webpack_require__.d(__webpack_exports__, "ServerSideRender", function() { return external_this_wp_serverSideRender_default.a; });
/* concated harmony reexport RichText */__webpack_require__.d(__webpack_exports__, "RichText", function() { return RichText; });
/* concated harmony reexport Autocomplete */__webpack_require__.d(__webpack_exports__, "Autocomplete", function() { return Autocomplete; });
/* concated harmony reexport AlignmentToolbar */__webpack_require__.d(__webpack_exports__, "AlignmentToolbar", function() { return AlignmentToolbar; });
/* concated harmony reexport BlockAlignmentToolbar */__webpack_require__.d(__webpack_exports__, "BlockAlignmentToolbar", function() { return BlockAlignmentToolbar; });
/* concated harmony reexport BlockControls */__webpack_require__.d(__webpack_exports__, "BlockControls", function() { return BlockControls; });
/* concated harmony reexport BlockEdit */__webpack_require__.d(__webpack_exports__, "BlockEdit", function() { return BlockEdit; });
/* concated harmony reexport BlockEditorKeyboardShortcuts */__webpack_require__.d(__webpack_exports__, "BlockEditorKeyboardShortcuts", function() { return BlockEditorKeyboardShortcuts; });
/* concated harmony reexport BlockFormatControls */__webpack_require__.d(__webpack_exports__, "BlockFormatControls", function() { return BlockFormatControls; });
/* concated harmony reexport BlockIcon */__webpack_require__.d(__webpack_exports__, "BlockIcon", function() { return BlockIcon; });
/* concated harmony reexport BlockInspector */__webpack_require__.d(__webpack_exports__, "BlockInspector", function() { return BlockInspector; });
/* concated harmony reexport BlockList */__webpack_require__.d(__webpack_exports__, "BlockList", function() { return BlockList; });
/* concated harmony reexport BlockMover */__webpack_require__.d(__webpack_exports__, "BlockMover", function() { return BlockMover; });
/* concated harmony reexport BlockNavigationDropdown */__webpack_require__.d(__webpack_exports__, "BlockNavigationDropdown", function() { return BlockNavigationDropdown; });
/* concated harmony reexport BlockSelectionClearer */__webpack_require__.d(__webpack_exports__, "BlockSelectionClearer", function() { return BlockSelectionClearer; });
/* concated harmony reexport BlockSettingsMenu */__webpack_require__.d(__webpack_exports__, "BlockSettingsMenu", function() { return BlockSettingsMenu; });
/* concated harmony reexport BlockTitle */__webpack_require__.d(__webpack_exports__, "BlockTitle", function() { return BlockTitle; });
/* concated harmony reexport BlockToolbar */__webpack_require__.d(__webpack_exports__, "BlockToolbar", function() { return BlockToolbar; });
/* concated harmony reexport ColorPalette */__webpack_require__.d(__webpack_exports__, "ColorPalette", function() { return ColorPalette; });
/* concated harmony reexport ContrastChecker */__webpack_require__.d(__webpack_exports__, "ContrastChecker", function() { return ContrastChecker; });
/* concated harmony reexport CopyHandler */__webpack_require__.d(__webpack_exports__, "CopyHandler", function() { return CopyHandler; });
/* concated harmony reexport DefaultBlockAppender */__webpack_require__.d(__webpack_exports__, "DefaultBlockAppender", function() { return DefaultBlockAppender; });
/* concated harmony reexport FontSizePicker */__webpack_require__.d(__webpack_exports__, "FontSizePicker", function() { return FontSizePicker; });
/* concated harmony reexport Inserter */__webpack_require__.d(__webpack_exports__, "Inserter", function() { return Inserter; });
/* concated harmony reexport InnerBlocks */__webpack_require__.d(__webpack_exports__, "InnerBlocks", function() { return InnerBlocks; });
/* concated harmony reexport InspectorAdvancedControls */__webpack_require__.d(__webpack_exports__, "InspectorAdvancedControls", function() { return InspectorAdvancedControls; });
/* concated harmony reexport InspectorControls */__webpack_require__.d(__webpack_exports__, "InspectorControls", function() { return InspectorControls; });
/* concated harmony reexport PanelColorSettings */__webpack_require__.d(__webpack_exports__, "PanelColorSettings", function() { return PanelColorSettings; });
/* concated harmony reexport PlainText */__webpack_require__.d(__webpack_exports__, "PlainText", function() { return PlainText; });
/* concated harmony reexport RichTextShortcut */__webpack_require__.d(__webpack_exports__, "RichTextShortcut", function() { return RichTextShortcut; });
/* concated harmony reexport RichTextToolbarButton */__webpack_require__.d(__webpack_exports__, "RichTextToolbarButton", function() { return RichTextToolbarButton; });
/* concated harmony reexport __unstableRichTextInputEvent */__webpack_require__.d(__webpack_exports__, "__unstableRichTextInputEvent", function() { return __unstableRichTextInputEvent; });
/* concated harmony reexport MediaPlaceholder */__webpack_require__.d(__webpack_exports__, "MediaPlaceholder", function() { return MediaPlaceholder; });
/* concated harmony reexport MediaUpload */__webpack_require__.d(__webpack_exports__, "MediaUpload", function() { return MediaUpload; });
/* concated harmony reexport MediaUploadCheck */__webpack_require__.d(__webpack_exports__, "MediaUploadCheck", function() { return MediaUploadCheck; });
/* concated harmony reexport MultiBlocksSwitcher */__webpack_require__.d(__webpack_exports__, "MultiBlocksSwitcher", function() { return MultiBlocksSwitcher; });
/* concated harmony reexport MultiSelectScrollIntoView */__webpack_require__.d(__webpack_exports__, "MultiSelectScrollIntoView", function() { return MultiSelectScrollIntoView; });
/* concated harmony reexport NavigableToolbar */__webpack_require__.d(__webpack_exports__, "NavigableToolbar", function() { return NavigableToolbar; });
/* concated harmony reexport ObserveTyping */__webpack_require__.d(__webpack_exports__, "ObserveTyping", function() { return ObserveTyping; });
/* concated harmony reexport PreserveScrollInReorder */__webpack_require__.d(__webpack_exports__, "PreserveScrollInReorder", function() { return PreserveScrollInReorder; });
/* concated harmony reexport SkipToSelectedBlock */__webpack_require__.d(__webpack_exports__, "SkipToSelectedBlock", function() { return SkipToSelectedBlock; });
/* concated harmony reexport URLInput */__webpack_require__.d(__webpack_exports__, "URLInput", function() { return URLInput; });
/* concated harmony reexport URLInputButton */__webpack_require__.d(__webpack_exports__, "URLInputButton", function() { return URLInputButton; });
/* concated harmony reexport URLPopover */__webpack_require__.d(__webpack_exports__, "URLPopover", function() { return URLPopover; });
/* concated harmony reexport Warning */__webpack_require__.d(__webpack_exports__, "Warning", function() { return Warning; });
/* concated harmony reexport WritingFlow */__webpack_require__.d(__webpack_exports__, "WritingFlow", function() { return WritingFlow; });
/* concated harmony reexport createCustomColorsHOC */__webpack_require__.d(__webpack_exports__, "createCustomColorsHOC", function() { return createCustomColorsHOC; });
/* concated harmony reexport getColorClassName */__webpack_require__.d(__webpack_exports__, "getColorClassName", function() { return getColorClassName; });
/* concated harmony reexport getColorObjectByAttributeValues */__webpack_require__.d(__webpack_exports__, "getColorObjectByAttributeValues", function() { return getColorObjectByAttributeValues; });
/* concated harmony reexport getColorObjectByColorValue */__webpack_require__.d(__webpack_exports__, "getColorObjectByColorValue", function() { return getColorObjectByColorValue; });
/* concated harmony reexport getFontSize */__webpack_require__.d(__webpack_exports__, "getFontSize", function() { return getFontSize; });
/* concated harmony reexport getFontSizeClass */__webpack_require__.d(__webpack_exports__, "getFontSizeClass", function() { return getFontSizeClass; });
/* concated harmony reexport withColorContext */__webpack_require__.d(__webpack_exports__, "withColorContext", function() { return withColorContext; });
/* concated harmony reexport withColors */__webpack_require__.d(__webpack_exports__, "withColors", function() { return withColors; });
/* concated harmony reexport withFontSizes */__webpack_require__.d(__webpack_exports__, "withFontSizes", function() { return withFontSizes; });
/* concated harmony reexport mediaUpload */__webpack_require__.d(__webpack_exports__, "mediaUpload", function() { return media_upload; });
/* concated harmony reexport cleanForSlug */__webpack_require__.d(__webpack_exports__, "cleanForSlug", function() { return cleanForSlug; });
/* concated harmony reexport storeConfig */__webpack_require__.d(__webpack_exports__, "storeConfig", function() { return storeConfig; });
/* concated harmony reexport transformStyles */__webpack_require__.d(__webpack_exports__, "transformStyles", function() { return external_this_wp_blockEditor_["transformStyles"]; });
/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */






/*
 * Backward compatibility
 */




/***/ }),

/***/ 36:
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

/***/ 41:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["isShallowEqual"]; }());

/***/ }),

/***/ 42:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["viewport"]; }());

/***/ }),

/***/ 43:
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

/***/ 47:
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

var runtime = (function (exports) {
  "use strict";

  var Op = Object.prototype;
  var hasOwn = Op.hasOwnProperty;
  var undefined; // More compressible than void 0.
  var $Symbol = typeof Symbol === "function" ? Symbol : {};
  var iteratorSymbol = $Symbol.iterator || "@@iterator";
  var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
  var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

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
  exports.wrap = wrap;

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

  exports.isGeneratorFunction = function(genFun) {
    var ctor = typeof genFun === "function" && genFun.constructor;
    return ctor
      ? ctor === GeneratorFunction ||
        // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction"
      : false;
  };

  exports.mark = function(genFun) {
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
  exports.awrap = function(arg) {
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
  exports.AsyncIterator = AsyncIterator;

  // Note that simple async functions are implemented on top of
  // AsyncIterator objects; they just return a Promise for the value of
  // the final result produced by the iterator.
  exports.async = function(innerFn, outerFn, self, tryLocsList) {
    var iter = new AsyncIterator(
      wrap(innerFn, outerFn, self, tryLocsList)
    );

    return exports.isGeneratorFunction(outerFn)
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
        // Note: ["return"] must be used for ES3 parsing compatibility.
        if (delegate.iterator["return"]) {
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

  exports.keys = function(object) {
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
  exports.values = values;

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

  // Regardless of whether this script is executing as a CommonJS module
  // or not, return the runtime object so that we can declare the variable
  // regeneratorRuntime in the outer scope, which allows this module to be
  // injected easily by `bin/regenerator --include-runtime script.js`.
  return exports;

}(
  // If this script is executing as a CommonJS module, use module.exports
  // as the regeneratorRuntime namespace. Otherwise create a new empty
  // object. Either way, the resulting object will be used to initialize
  // the regeneratorRuntime variable at the top of this file.
   true ? module.exports : undefined
));

try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  // This module should not be running in strict mode, so the above
  // assignment should always work unless something is misconfigured. Just
  // in case runtime.js accidentally runs in strict mode, we can escape
  // strict mode using a global Function call. This could conceivably fail
  // if a Content Security Policy forbids using Function, but in that case
  // the proper solution is to fix the accidental strict mode problem. If
  // you've misconfigured your bundler to force strict mode and applied a
  // CSP to forbid Function, and you're not willing to fix either of those
  // problems, please detail your unique predicament in a GitHub issue.
  Function("r", "regeneratorRuntime = r")(runtime);
}


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

/***/ 52:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["htmlEntities"]; }());

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

/***/ 61:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["nux"]; }());

/***/ }),

/***/ 62:
/***/ (function(module, exports, __webpack_require__) {

"use strict";

exports.__esModule = true;
var TextareaAutosize_1 = __webpack_require__(115);
exports["default"] = TextareaAutosize_1["default"];


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

/***/ 72:
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

/***/ 8:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ 83:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(325);


/***/ }),

/***/ 87:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = __webpack_require__(88);

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

/***/ 88:
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

/***/ 89:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["coreData"]; }());

/***/ }),

/***/ 9:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ 97:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["wordcount"]; }());

/***/ }),

/***/ 99:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["mediaUtils"]; }());

/***/ })

/******/ });