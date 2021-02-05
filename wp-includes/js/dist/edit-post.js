this["wp"] = this["wp"] || {}; this["wp"]["editPost"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 423);
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
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ 11:
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

/***/ 12:
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

/***/ 120:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(9);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

var close = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M14.95 6.46L11.41 10l3.54 3.54-1.41 1.41L10 11.42l-3.53 3.53-1.42-1.42L8.58 10 5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z"
}));
/* harmony default export */ __webpack_exports__["a"] = (close);


/***/ }),

/***/ 13:
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

/***/ 130:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["mediaUtils"]; }());

/***/ }),

/***/ 14:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ 15:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(34);
/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(7);


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ 16:
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

/***/ 17:
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

/***/ 18:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
var arrayLikeToArray = __webpack_require__(25);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return Object(arrayLikeToArray["a" /* default */])(arr);
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__(35);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__(27);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || Object(unsupportedIterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ 182:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["notices"]; }());

/***/ }),

/***/ 19:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectWithoutProperties; });
/* harmony import */ var _objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(43);

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = Object(_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(source, excluded);
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

/***/ 193:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(9);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

var check = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"
}));
/* harmony default export */ __webpack_exports__["a"] = (check);


/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 20:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _slicedToArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__(38);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
  if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
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
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__(27);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__(39);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(unsupportedIterableToArray["a" /* default */])(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 22:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),

/***/ 25:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayLikeToArray; });
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

/***/ }),

/***/ 27:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(25);

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(n);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ 275:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockLibrary"]; }());

/***/ }),

/***/ 284:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(9);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

var moreHorizontal = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5 10c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm12-2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-7 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (moreHorizontal);


/***/ }),

/***/ 3:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ 30:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ 31:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["editor"]; }());

/***/ }),

/***/ 32:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

/***/ }),

/***/ 34:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ 35:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

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
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ 40:
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

/***/ 42:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ 423:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "reinitializeEditor", function() { return /* binding */ reinitializeEditor; });
__webpack_require__.d(__webpack_exports__, "initializeEditor", function() { return /* binding */ initializeEditor; });
__webpack_require__.d(__webpack_exports__, "PluginBlockSettingsMenuItem", function() { return /* reexport */ plugin_block_settings_menu_item; });
__webpack_require__.d(__webpack_exports__, "PluginDocumentSettingPanel", function() { return /* reexport */ plugin_document_setting_panel; });
__webpack_require__.d(__webpack_exports__, "PluginMoreMenuItem", function() { return /* reexport */ plugin_more_menu_item; });
__webpack_require__.d(__webpack_exports__, "PluginPostPublishPanel", function() { return /* reexport */ plugin_post_publish_panel; });
__webpack_require__.d(__webpack_exports__, "PluginPostStatusInfo", function() { return /* reexport */ plugin_post_status_info; });
__webpack_require__.d(__webpack_exports__, "PluginPrePublishPanel", function() { return /* reexport */ plugin_pre_publish_panel; });
__webpack_require__.d(__webpack_exports__, "PluginSidebar", function() { return /* reexport */ plugin_sidebar; });
__webpack_require__.d(__webpack_exports__, "PluginSidebarMoreMenuItem", function() { return /* reexport */ plugin_sidebar_more_menu_item; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "openGeneralSidebar", function() { return actions_openGeneralSidebar; });
__webpack_require__.d(actions_namespaceObject, "closeGeneralSidebar", function() { return actions_closeGeneralSidebar; });
__webpack_require__.d(actions_namespaceObject, "openModal", function() { return actions_openModal; });
__webpack_require__.d(actions_namespaceObject, "closeModal", function() { return actions_closeModal; });
__webpack_require__.d(actions_namespaceObject, "openPublishSidebar", function() { return openPublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "closePublishSidebar", function() { return actions_closePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "togglePublishSidebar", function() { return actions_togglePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "toggleEditorPanelEnabled", function() { return toggleEditorPanelEnabled; });
__webpack_require__.d(actions_namespaceObject, "toggleEditorPanelOpened", function() { return actions_toggleEditorPanelOpened; });
__webpack_require__.d(actions_namespaceObject, "removeEditorPanel", function() { return removeEditorPanel; });
__webpack_require__.d(actions_namespaceObject, "toggleFeature", function() { return actions_toggleFeature; });
__webpack_require__.d(actions_namespaceObject, "switchEditorMode", function() { return actions_switchEditorMode; });
__webpack_require__.d(actions_namespaceObject, "togglePinnedPluginItem", function() { return togglePinnedPluginItem; });
__webpack_require__.d(actions_namespaceObject, "hideBlockTypes", function() { return actions_hideBlockTypes; });
__webpack_require__.d(actions_namespaceObject, "updatePreferredStyleVariations", function() { return actions_updatePreferredStyleVariations; });
__webpack_require__.d(actions_namespaceObject, "__experimentalUpdateLocalAutosaveInterval", function() { return __experimentalUpdateLocalAutosaveInterval; });
__webpack_require__.d(actions_namespaceObject, "showBlockTypes", function() { return actions_showBlockTypes; });
__webpack_require__.d(actions_namespaceObject, "setAvailableMetaBoxesPerLocation", function() { return setAvailableMetaBoxesPerLocation; });
__webpack_require__.d(actions_namespaceObject, "requestMetaBoxUpdates", function() { return requestMetaBoxUpdates; });
__webpack_require__.d(actions_namespaceObject, "metaBoxUpdatesSuccess", function() { return metaBoxUpdatesSuccess; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getEditorMode", function() { return selectors_getEditorMode; });
__webpack_require__.d(selectors_namespaceObject, "isEditorSidebarOpened", function() { return selectors_isEditorSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "isPluginSidebarOpened", function() { return isPluginSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "getActiveGeneralSidebarName", function() { return getActiveGeneralSidebarName; });
__webpack_require__.d(selectors_namespaceObject, "getPreferences", function() { return getPreferences; });
__webpack_require__.d(selectors_namespaceObject, "getPreference", function() { return selectors_getPreference; });
__webpack_require__.d(selectors_namespaceObject, "isPublishSidebarOpened", function() { return selectors_isPublishSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelRemoved", function() { return isEditorPanelRemoved; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelEnabled", function() { return selectors_isEditorPanelEnabled; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelOpened", function() { return selectors_isEditorPanelOpened; });
__webpack_require__.d(selectors_namespaceObject, "isModalActive", function() { return selectors_isModalActive; });
__webpack_require__.d(selectors_namespaceObject, "isFeatureActive", function() { return isFeatureActive; });
__webpack_require__.d(selectors_namespaceObject, "isPluginItemPinned", function() { return isPluginItemPinned; });
__webpack_require__.d(selectors_namespaceObject, "getActiveMetaBoxLocations", function() { return getActiveMetaBoxLocations; });
__webpack_require__.d(selectors_namespaceObject, "isMetaBoxLocationVisible", function() { return isMetaBoxLocationVisible; });
__webpack_require__.d(selectors_namespaceObject, "isMetaBoxLocationActive", function() { return isMetaBoxLocationActive; });
__webpack_require__.d(selectors_namespaceObject, "getMetaBoxesPerLocation", function() { return getMetaBoxesPerLocation; });
__webpack_require__.d(selectors_namespaceObject, "getAllMetaBoxes", function() { return getAllMetaBoxes; });
__webpack_require__.d(selectors_namespaceObject, "hasMetaBoxes", function() { return hasMetaBoxes; });
__webpack_require__.d(selectors_namespaceObject, "isSavingMetaBoxes", function() { return selectors_isSavingMetaBoxes; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","coreData"]}
var external_this_wp_coreData_ = __webpack_require__(75);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(31);

// EXTERNAL MODULE: external {"this":["wp","keyboardShortcuts"]}
var external_this_wp_keyboardShortcuts_ = __webpack_require__(48);

// EXTERNAL MODULE: external {"this":["wp","viewport"]}
var external_this_wp_viewport_ = __webpack_require__(56);

// EXTERNAL MODULE: external {"this":["wp","notices"]}
var external_this_wp_notices_ = __webpack_require__(182);

// EXTERNAL MODULE: external {"this":["wp","blockLibrary"]}
var external_this_wp_blockLibrary_ = __webpack_require__(275);

// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__(32);

// EXTERNAL MODULE: external {"this":["wp","mediaUtils"]}
var external_this_wp_mediaUtils_ = __webpack_require__(130);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/components/index.js
/**
 * WordPress dependencies
 */



var components_replaceMediaUpload = function replaceMediaUpload() {
  return external_this_wp_mediaUtils_["MediaUpload"];
};

Object(external_this_wp_hooks_["addFilter"])('editor.MediaUpload', 'core/edit-post/replace-media-upload', components_replaceMediaUpload);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js
var objectWithoutProperties = __webpack_require__(19);

// EXTERNAL MODULE: external {"this":"lodash"}
var external_this_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(10);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(8);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/validate-multiple-use/index.js




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








var enhance = Object(external_this_wp_compose_["compose"])(
/**
 * For blocks whose block type doesn't support `multiple`, provides the
 * wrapped component with `originalBlockClientId` -- a reference to the
 * first block of the same type in the content -- if and only if that
 * "original" block is not the current one. Thus, an inexisting
 * `originalBlockClientId` prop signals that the block is valid.
 *
 * @param {WPComponent} WrappedBlockEdit A filtered BlockEdit instance.
 *
 * @return {WPComponent} Enhanced component with merged state data props.
 */
Object(external_this_wp_data_["withSelect"])(function (select, block) {
  var multiple = Object(external_this_wp_blocks_["hasBlockSupport"])(block.name, 'multiple', true); // For block types with `multiple` support, there is no "original
  // block" to be found in the content, as the block itself is valid.

  if (multiple) {
    return {};
  } // Otherwise, only pass `originalBlockClientId` if it refers to a different
  // block from the current one.


  var blocks = select('core/block-editor').getBlocks();
  var firstOfSameType = Object(external_this_lodash_["find"])(blocks, function (_ref) {
    var name = _ref.name;
    return block.name === name;
  });
  var isInvalid = firstOfSameType && firstOfSameType.clientId !== block.clientId;
  return {
    originalBlockClientId: isInvalid && firstOfSameType.clientId
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref2) {
  var originalBlockClientId = _ref2.originalBlockClientId;
  return {
    selectFirst: function selectFirst() {
      return dispatch('core/block-editor').selectBlock(originalBlockClientId);
    }
  };
}));
var withMultipleValidation = Object(external_this_wp_compose_["createHigherOrderComponent"])(function (BlockEdit) {
  return enhance(function (_ref3) {
    var originalBlockClientId = _ref3.originalBlockClientId,
        selectFirst = _ref3.selectFirst,
        props = Object(objectWithoutProperties["a" /* default */])(_ref3, ["originalBlockClientId", "selectFirst"]);

    if (!originalBlockClientId) {
      return Object(external_this_wp_element_["createElement"])(BlockEdit, props);
    }

    var blockType = Object(external_this_wp_blocks_["getBlockType"])(props.name);
    var outboundType = getOutboundType(props.name);
    return [Object(external_this_wp_element_["createElement"])("div", {
      key: "invalid-preview",
      style: {
        minHeight: '60px'
      }
    }, Object(external_this_wp_element_["createElement"])(BlockEdit, Object(esm_extends["a" /* default */])({
      key: "block-edit"
    }, props))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["Warning"], {
      key: "multiple-use-warning",
      actions: [Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        key: "find-original",
        isSecondary: true,
        onClick: selectFirst
      }, Object(external_this_wp_i18n_["__"])('Find original')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        key: "remove",
        isSecondary: true,
        onClick: function onClick() {
          return props.onReplace([]);
        }
      }, Object(external_this_wp_i18n_["__"])('Remove')), outboundType && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        key: "transform",
        isSecondary: true,
        onClick: function onClick() {
          return props.onReplace(Object(external_this_wp_blocks_["createBlock"])(outboundType.name, props.attributes));
        }
      }, Object(external_this_wp_i18n_["__"])('Transform into:'), " ", outboundType.title)]
    }, Object(external_this_wp_element_["createElement"])("strong", null, blockType.title, ": "), Object(external_this_wp_i18n_["__"])('This block can only be used once.'))];
  });
}, 'withMultipleValidation');
/**
 * Given a base block name, returns the default block type to which to offer
 * transforms.
 *
 * @param {string} blockName Base block name.
 *
 * @return {?Object} The chosen default block type.
 */

function getOutboundType(blockName) {
  // Grab the first outbound transform
  var transform = Object(external_this_wp_blocks_["findTransform"])(Object(external_this_wp_blocks_["getBlockTransforms"])('to', blockName), function (_ref4) {
    var type = _ref4.type,
        blocks = _ref4.blocks;
    return type === 'block' && blocks.length === 1;
  } // What about when .length > 1?
  );

  if (!transform) {
    return null;
  }

  return Object(external_this_wp_blocks_["getBlockType"])(transform.blocks[0]);
}

Object(external_this_wp_hooks_["addFilter"])('editor.BlockEdit', 'core/edit-post/validate-multiple-use/with-multiple-validation', withMultipleValidation);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/index.js
/**
 * Internal dependencies
 */



// EXTERNAL MODULE: external {"this":["wp","plugins"]}
var external_this_wp_plugins_ = __webpack_require__(55);

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(30);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/copy-content-menu-item/index.js


/**
 * WordPress dependencies
 */





function CopyContentMenuItem(_ref) {
  var createNotice = _ref.createNotice,
      editedPostContent = _ref.editedPostContent,
      hasCopied = _ref.hasCopied,
      setState = _ref.setState;
  return editedPostContent.length > 0 && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ClipboardButton"], {
    text: editedPostContent,
    role: "menuitem",
    className: "components-menu-item__button",
    onCopy: function onCopy() {
      setState({
        hasCopied: true
      });
      createNotice('info', Object(external_this_wp_i18n_["__"])('All content copied.'), {
        isDismissible: true,
        type: 'snackbar'
      });
    },
    onFinishCopy: function onFinishCopy() {
      return setState({
        hasCopied: false
      });
    }
  }, hasCopied ? Object(external_this_wp_i18n_["__"])('Copied!') : Object(external_this_wp_i18n_["__"])('Copy all content'));
}

/* harmony default export */ var copy_content_menu_item = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    editedPostContent: select('core/editor').getEditedPostAttribute('content')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/notices'),
      createNotice = _dispatch.createNotice;

  return {
    createNotice: createNotice
  };
}), Object(external_this_wp_compose_["withState"])({
  hasCopied: false
}))(CopyContentMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/manage-blocks-menu-item/index.js


/**
 * WordPress dependencies
 */



function ManageBlocksMenuItem(_ref) {
  var openModal = _ref.openModal;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
      openModal('edit-post/manage-blocks');
    }
  }, Object(external_this_wp_i18n_["__"])('Block Manager'));
}
/* harmony default export */ var manage_blocks_menu_item = (Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openModal = _dispatch.openModal;

  return {
    openModal: openModal
  };
})(ManageBlocksMenuItem));

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(22);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js


/**
 * WordPress dependencies
 */




function KeyboardShortcutsHelpMenuItem(_ref) {
  var openModal = _ref.openModal;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
      openModal('edit-post/keyboard-shortcut-help');
    },
    shortcut: external_this_wp_keycodes_["displayShortcut"].access('h')
  }, Object(external_this_wp_i18n_["__"])('Keyboard shortcuts'));
}
/* harmony default export */ var keyboard_shortcuts_help_menu_item = (Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openModal = _dispatch.openModal;

  return {
    openModal: openModal
  };
})(KeyboardShortcutsHelpMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/tools-more-menu-group/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




var _createSlotFill = Object(external_this_wp_components_["createSlotFill"])('ToolsMoreMenuGroup'),
    ToolsMoreMenuGroup = _createSlotFill.Fill,
    Slot = _createSlotFill.Slot;

ToolsMoreMenuGroup.Slot = function (_ref) {
  var fillProps = _ref.fillProps;
  return Object(external_this_wp_element_["createElement"])(Slot, {
    fillProps: fillProps
  }, function (fills) {
    return !Object(external_this_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
      label: Object(external_this_wp_i18n_["__"])('Tools')
    }, fills);
  });
};

/* harmony default export */ var tools_more_menu_group = (ToolsMoreMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/welcome-guide-menu-item/index.js


/**
 * WordPress dependencies
 */



function WelcomeGuideMenuItem() {
  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/edit-post'),
      toggleFeature = _useDispatch.toggleFeature;

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
      return toggleFeature('welcomeGuide');
    }
  }, Object(external_this_wp_i18n_["__"])('Welcome Guide'));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */






Object(external_this_wp_plugins_["registerPlugin"])('edit-post', {
  render: function render() {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(tools_more_menu_group, null, function (_ref) {
      var onClose = _ref.onClose;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(manage_blocks_menu_item, {
        onSelect: onClose
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
        role: "menuitem",
        href: Object(external_this_wp_url_["addQueryArgs"])('edit.php', {
          post_type: 'wp_block'
        })
      }, Object(external_this_wp_i18n_["__"])('Manage all reusable blocks')), Object(external_this_wp_element_["createElement"])(keyboard_shortcuts_help_menu_item, {
        onSelect: onClose
      }), Object(external_this_wp_element_["createElement"])(WelcomeGuideMenuItem, null), Object(external_this_wp_element_["createElement"])(copy_content_menu_item, null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
        role: "menuitem",
        href: Object(external_this_wp_i18n_["__"])('https://wordpress.org/support/article/wordpress-editor/'),
        target: "_new"
      }, Object(external_this_wp_i18n_["__"])('Help')));
    }));
  }
});

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(18);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(5);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/defaults.js
var PREFERENCES_DEFAULTS = {
  editorMode: 'visual',
  isGeneralSidebarDismissed: false,
  panels: {
    'post-status': {
      opened: true
    }
  },
  features: {
    fixedToolbar: false,
    showInserterHelpPanel: true,
    welcomeGuide: true,
    fullscreenMode: true
  },
  pinnedPluginItems: {},
  hiddenBlockTypes: [],
  preferredStyleVariations: {},
  localAutosaveInterval: 15
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/reducer.js



function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

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
 * The default active general sidebar: The "Document" tab.
 *
 * @type {string}
 */

var DEFAULT_ACTIVE_GENERAL_SIDEBAR = 'edit-post/document';
/**
 * Higher-order reducer creator which provides the given initial state for the
 * original reducer.
 *
 * @param {*} initialState Initial state to provide to reducer.
 *
 * @return {Function} Higher-order reducer.
 */

var createWithInitialState = function createWithInitialState(initialState) {
  return function (reducer) {
    return function () {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : initialState;
      var action = arguments.length > 1 ? arguments[1] : undefined;
      return reducer(state, action);
    };
  };
};
/**
 * Reducer returning the user preferences.
 *
 * @param {Object}  state                           Current state.
 * @param {string}  state.mode                      Current editor mode, either
 *                                                  "visual" or "text".
 * @param {boolean} state.isGeneralSidebarDismissed Whether general sidebar is
 *                                                  dismissed. False by default
 *                                                  or when closing general
 *                                                  sidebar, true when opening
 *                                                  sidebar.
 * @param {boolean} state.isSidebarOpened           Whether the sidebar is
 *                                                  opened or closed.
 * @param {Object}  state.panels                    The state of the different
 *                                                  sidebar panels.
 * @param {Object}  action                          Dispatched action.
 *
 * @return {Object} Updated state.
 */


var preferences = Object(external_this_lodash_["flow"])([external_this_wp_data_["combineReducers"], createWithInitialState(PREFERENCES_DEFAULTS)])({
  isGeneralSidebarDismissed: function isGeneralSidebarDismissed(state, action) {
    switch (action.type) {
      case 'OPEN_GENERAL_SIDEBAR':
      case 'CLOSE_GENERAL_SIDEBAR':
        return action.type === 'CLOSE_GENERAL_SIDEBAR';
    }

    return state;
  },
  panels: function panels(state, action) {
    switch (action.type) {
      case 'TOGGLE_PANEL_ENABLED':
        {
          var panelName = action.panelName;
          return _objectSpread({}, state, Object(defineProperty["a" /* default */])({}, panelName, _objectSpread({}, state[panelName], {
            enabled: !Object(external_this_lodash_["get"])(state, [panelName, 'enabled'], true)
          })));
        }

      case 'TOGGLE_PANEL_OPENED':
        {
          var _panelName = action.panelName;
          var isOpen = state[_panelName] === true || Object(external_this_lodash_["get"])(state, [_panelName, 'opened'], false);
          return _objectSpread({}, state, Object(defineProperty["a" /* default */])({}, _panelName, _objectSpread({}, state[_panelName], {
            opened: !isOpen
          })));
        }
    }

    return state;
  },
  features: function features(state, action) {
    if (action.type === 'TOGGLE_FEATURE') {
      return _objectSpread({}, state, Object(defineProperty["a" /* default */])({}, action.feature, !state[action.feature]));
    }

    return state;
  },
  editorMode: function editorMode(state, action) {
    if (action.type === 'SWITCH_MODE') {
      return action.mode;
    }

    return state;
  },
  pinnedPluginItems: function pinnedPluginItems(state, action) {
    if (action.type === 'TOGGLE_PINNED_PLUGIN_ITEM') {
      return _objectSpread({}, state, Object(defineProperty["a" /* default */])({}, action.pluginName, !Object(external_this_lodash_["get"])(state, [action.pluginName], true)));
    }

    return state;
  },
  hiddenBlockTypes: function hiddenBlockTypes(state, action) {
    switch (action.type) {
      case 'SHOW_BLOCK_TYPES':
        return external_this_lodash_["without"].apply(void 0, [state].concat(Object(toConsumableArray["a" /* default */])(action.blockNames)));

      case 'HIDE_BLOCK_TYPES':
        return Object(external_this_lodash_["union"])(state, action.blockNames);
    }

    return state;
  },
  preferredStyleVariations: function preferredStyleVariations(state, action) {
    switch (action.type) {
      case 'UPDATE_PREFERRED_STYLE_VARIATIONS':
        {
          if (!action.blockName) {
            return state;
          }

          if (!action.blockStyle) {
            return Object(external_this_lodash_["omit"])(state, [action.blockName]);
          }

          return _objectSpread({}, state, Object(defineProperty["a" /* default */])({}, action.blockName, action.blockStyle));
        }
    }

    return state;
  },
  localAutosaveInterval: function localAutosaveInterval(state, action) {
    switch (action.type) {
      case 'UPDATE_LOCAL_AUTOSAVE_INTERVAL':
        return action.interval;
    }

    return state;
  }
});
/**
 * Reducer storing the list of all programmatically removed panels.
 *
 * @param {Array}  state  Current state.
 * @param {Object} action Action object.
 *
 * @return {Array} Updated state.
 */

function removedPanels() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REMOVE_PANEL':
      if (!Object(external_this_lodash_["includes"])(state, action.panelName)) {
        return [].concat(Object(toConsumableArray["a" /* default */])(state), [action.panelName]);
      }

  }

  return state;
}
/**
 * Reducer returning the next active general sidebar state. The active general
 * sidebar is a unique name to identify either an editor or plugin sidebar.
 *
 * @param {?string} state  Current state.
 * @param {Object}  action Action object.
 *
 * @return {?string} Updated state.
 */

function reducer_activeGeneralSidebar() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_ACTIVE_GENERAL_SIDEBAR;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_GENERAL_SIDEBAR':
      return action.name;
  }

  return state;
}
/**
 * Reducer for storing the name of the open modal, or null if no modal is open.
 *
 * @param {Object} state  Previous state.
 * @param {Object} action Action object containing the `name` of the modal
 *
 * @return {Object} Updated state
 */

function activeModal() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_MODAL':
      return action.name;

    case 'CLOSE_MODAL':
      return null;
  }

  return state;
}
function publishSidebarActive() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_PUBLISH_SIDEBAR':
      return true;

    case 'CLOSE_PUBLISH_SIDEBAR':
      return false;

    case 'TOGGLE_PUBLISH_SIDEBAR':
      return !state;
  }

  return state;
}
/**
 * Reducer keeping track of the meta boxes isSaving state.
 * A "true" value means the meta boxes saving request is in-flight.
 *
 *
 * @param {boolean}  state   Previous state.
 * @param {Object}   action  Action Object.
 *
 * @return {Object} Updated state.
 */

function isSavingMetaBoxes() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REQUEST_META_BOX_UPDATES':
      return true;

    case 'META_BOX_UPDATES_SUCCESS':
      return false;

    default:
      return state;
  }
}
/**
 * Reducer keeping track of the meta boxes per location.
 *
 * @param {boolean}  state   Previous state.
 * @param {Object}   action  Action Object.
 *
 * @return {Object} Updated state.
 */

function metaBoxLocations() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_META_BOXES_PER_LOCATIONS':
      return action.metaBoxesPerLocation;
  }

  return state;
}
var reducer_metaBoxes = Object(external_this_wp_data_["combineReducers"])({
  isSaving: isSavingMetaBoxes,
  locations: metaBoxLocations
});
/* harmony default export */ var reducer = (Object(external_this_wp_data_["combineReducers"])({
  activeGeneralSidebar: reducer_activeGeneralSidebar,
  activeModal: activeModal,
  metaBoxes: reducer_metaBoxes,
  preferences: preferences,
  publishSidebarActive: publishSidebarActive,
  removedPanels: removedPanels
}));

// EXTERNAL MODULE: ./node_modules/refx/refx.js
var refx = __webpack_require__(89);
var refx_default = /*#__PURE__*/__webpack_require__.n(refx);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(20);

// EXTERNAL MODULE: external {"this":["wp","a11y"]}
var external_this_wp_a11y_ = __webpack_require__(50);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(42);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
/**
 * External dependencies
 */

/**
 * Returns an action object used in signalling that the user opened an editor sidebar.
 *
 * @param {string} name Sidebar name to be opened.
 *
 * @return {Object} Action object.
 */

function actions_openGeneralSidebar(name) {
  return {
    type: 'OPEN_GENERAL_SIDEBAR',
    name: name
  };
}
/**
 * Returns an action object signalling that the user closed the sidebar.
 *
 * @return {Object} Action object.
 */

function actions_closeGeneralSidebar() {
  return {
    type: 'CLOSE_GENERAL_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user opened a modal.
 *
 * @param {string} name A string that uniquely identifies the modal.
 *
 * @return {Object} Action object.
 */

function actions_openModal(name) {
  return {
    type: 'OPEN_MODAL',
    name: name
  };
}
/**
 * Returns an action object signalling that the user closed a modal.
 *
 * @return {Object} Action object.
 */

function actions_closeModal() {
  return {
    type: 'CLOSE_MODAL'
  };
}
/**
 * Returns an action object used in signalling that the user opened the publish
 * sidebar.
 *
 * @return {Object} Action object
 */

function openPublishSidebar() {
  return {
    type: 'OPEN_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user closed the
 * publish sidebar.
 *
 * @return {Object} Action object.
 */

function actions_closePublishSidebar() {
  return {
    type: 'CLOSE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user toggles the publish sidebar.
 *
 * @return {Object} Action object
 */

function actions_togglePublishSidebar() {
  return {
    type: 'TOGGLE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used to enable or disable a panel in the editor.
 *
 * @param {string} panelName A string that identifies the panel to enable or disable.
 *
 * @return {Object} Action object.
 */

function toggleEditorPanelEnabled(panelName) {
  return {
    type: 'TOGGLE_PANEL_ENABLED',
    panelName: panelName
  };
}
/**
 * Returns an action object used to open or close a panel in the editor.
 *
 * @param {string} panelName A string that identifies the panel to open or close.
 *
 * @return {Object} Action object.
 */

function actions_toggleEditorPanelOpened(panelName) {
  return {
    type: 'TOGGLE_PANEL_OPENED',
    panelName: panelName
  };
}
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
    panelName: panelName
  };
}
/**
 * Returns an action object used to toggle a feature flag.
 *
 * @param {string} feature Feature name.
 *
 * @return {Object} Action object.
 */

function actions_toggleFeature(feature) {
  return {
    type: 'TOGGLE_FEATURE',
    feature: feature
  };
}
function actions_switchEditorMode(mode) {
  return {
    type: 'SWITCH_MODE',
    mode: mode
  };
}
/**
 * Returns an action object used to toggle a plugin name flag.
 *
 * @param {string} pluginName Plugin name.
 *
 * @return {Object} Action object.
 */

function togglePinnedPluginItem(pluginName) {
  return {
    type: 'TOGGLE_PINNED_PLUGIN_ITEM',
    pluginName: pluginName
  };
}
/**
 * Returns an action object used in signalling that block types by the given
 * name(s) should be hidden.
 *
 * @param {string[]} blockNames Names of block types to hide.
 *
 * @return {Object} Action object.
 */

function actions_hideBlockTypes(blockNames) {
  return {
    type: 'HIDE_BLOCK_TYPES',
    blockNames: Object(external_this_lodash_["castArray"])(blockNames)
  };
}
/**
 * Returns an action object used in signaling that a style should be auto-applied when a block is created.
 *
 * @param {string}  blockName  Name of the block.
 * @param {?string} blockStyle Name of the style that should be auto applied. If undefined, the "auto apply" setting of the block is removed.
 *
 * @return {Object} Action object.
 */

function actions_updatePreferredStyleVariations(blockName, blockStyle) {
  return {
    type: 'UPDATE_PREFERRED_STYLE_VARIATIONS',
    blockName: blockName,
    blockStyle: blockStyle
  };
}
/**
 * Returns an action object used in signalling that the editor should attempt
 * to locally autosave the current post every `interval` seconds.
 *
 * @param {number} interval The new interval, in seconds.
 * @return {Object} Action object.
 */

function __experimentalUpdateLocalAutosaveInterval(interval) {
  return {
    type: 'UPDATE_LOCAL_AUTOSAVE_INTERVAL',
    interval: interval
  };
}
/**
 * Returns an action object used in signalling that block types by the given
 * name(s) should be shown.
 *
 * @param {string[]} blockNames Names of block types to show.
 *
 * @return {Object} Action object.
 */

function actions_showBlockTypes(blockNames) {
  return {
    type: 'SHOW_BLOCK_TYPES',
    blockNames: Object(external_this_lodash_["castArray"])(blockNames)
  };
}
/**
 * Returns an action object used in signaling
 * what Meta boxes are available in which location.
 *
 * @param {Object} metaBoxesPerLocation Meta boxes per location.
 *
 * @return {Object} Action object.
 */

function setAvailableMetaBoxesPerLocation(metaBoxesPerLocation) {
  return {
    type: 'SET_META_BOXES_PER_LOCATIONS',
    metaBoxesPerLocation: metaBoxesPerLocation
  };
}
/**
 * Returns an action object used to request meta box update.
 *
 * @return {Object} Action object.
 */

function requestMetaBoxUpdates() {
  return {
    type: 'REQUEST_META_BOX_UPDATES'
  };
}
/**
 * Returns an action object used signal a successful meta box update.
 *
 * @return {Object} Action object.
 */

function metaBoxUpdatesSuccess() {
  return {
    type: 'META_BOX_UPDATES_SUCCESS'
  };
}

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__(40);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
/**
 * External dependencies
 */


/**
 * Returns the current editing mode.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Editing mode.
 */

function selectors_getEditorMode(state) {
  return selectors_getPreference(state, 'editorMode', 'visual');
}
/**
 * Returns true if the editor sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the editor sidebar is opened.
 */

function selectors_isEditorSidebarOpened(state) {
  var activeGeneralSidebar = getActiveGeneralSidebarName(state);
  return Object(external_this_lodash_["includes"])(['edit-post/document', 'edit-post/block'], activeGeneralSidebar);
}
/**
 * Returns true if the plugin sidebar is opened.
 *
 * @param {Object} state Global application state
 * @return {boolean}     Whether the plugin sidebar is opened.
 */

function isPluginSidebarOpened(state) {
  var activeGeneralSidebar = getActiveGeneralSidebarName(state);
  return !!activeGeneralSidebar && !selectors_isEditorSidebarOpened(state);
}
/**
 * Returns the current active general sidebar name, or null if there is no
 * general sidebar active. The active general sidebar is a unique name to
 * identify either an editor or plugin sidebar.
 *
 * Examples:
 *
 *  - `edit-post/document`
 *  - `my-plugin/insert-image-sidebar`
 *
 * @param {Object} state Global application state.
 *
 * @return {?string} Active general sidebar name.
 */

function getActiveGeneralSidebarName(state) {
  // Dismissal takes precedent.
  var isDismissed = selectors_getPreference(state, 'isGeneralSidebarDismissed', false);

  if (isDismissed) {
    return null;
  }

  return state.activeGeneralSidebar;
}
/**
 * Returns the preferences (these preferences are persisted locally).
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Preferences Object.
 */

function getPreferences(state) {
  return state.preferences;
}
/**
 *
 * @param {Object} state         Global application state.
 * @param {string} preferenceKey Preference Key.
 * @param {*}      defaultValue  Default Value.
 *
 * @return {*} Preference Value.
 */

function selectors_getPreference(state, preferenceKey, defaultValue) {
  var preferences = getPreferences(state);
  var value = preferences[preferenceKey];
  return value === undefined ? defaultValue : value;
}
/**
 * Returns true if the publish sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the publish sidebar is open.
 */

function selectors_isPublishSidebarOpened(state) {
  return state.publishSidebarActive;
}
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
  return Object(external_this_lodash_["includes"])(state.removedPanels, panelName);
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

function selectors_isEditorPanelEnabled(state, panelName) {
  var panels = selectors_getPreference(state, 'panels');
  return !isEditorPanelRemoved(state, panelName) && Object(external_this_lodash_["get"])(panels, [panelName, 'enabled'], true);
}
/**
 * Returns true if the given panel is open, or false otherwise. Panels are
 * closed by default.
 *
 * @param  {Object}  state     Global application state.
 * @param  {string}  panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is open.
 */

function selectors_isEditorPanelOpened(state, panelName) {
  var panels = selectors_getPreference(state, 'panels');
  return Object(external_this_lodash_["get"])(panels, [panelName]) === true || Object(external_this_lodash_["get"])(panels, [panelName, 'opened']) === true;
}
/**
 * Returns true if a modal is active, or false otherwise.
 *
 * @param  {Object}  state 	   Global application state.
 * @param  {string}  modalName A string that uniquely identifies the modal.
 *
 * @return {boolean} Whether the modal is active.
 */

function selectors_isModalActive(state, modalName) {
  return state.activeModal === modalName;
}
/**
 * Returns whether the given feature is enabled or not.
 *
 * @param {Object} state   Global application state.
 * @param {string} feature Feature slug.
 *
 * @return {boolean} Is active.
 */

function isFeatureActive(state, feature) {
  return Object(external_this_lodash_["get"])(state.preferences.features, [feature], false);
}
/**
 * Returns true if the plugin item is pinned to the header.
 * When the value is not set it defaults to true.
 *
 * @param  {Object}  state      Global application state.
 * @param  {string}  pluginName Plugin item name.
 *
 * @return {boolean} Whether the plugin item is pinned.
 */

function isPluginItemPinned(state, pluginName) {
  var pinnedPluginItems = selectors_getPreference(state, 'pinnedPluginItems', {});
  return Object(external_this_lodash_["get"])(pinnedPluginItems, [pluginName], true);
}
/**
 * Returns an array of active meta box locations.
 *
 * @param {Object} state Post editor state.
 *
 * @return {string[]} Active meta box locations.
 */

var getActiveMetaBoxLocations = Object(rememo["a" /* default */])(function (state) {
  return Object.keys(state.metaBoxes.locations).filter(function (location) {
    return isMetaBoxLocationActive(state, location);
  });
}, function (state) {
  return [state.metaBoxes.locations];
});
/**
 * Returns true if a metabox location is active and visible
 *
 * @param {Object} state    Post editor state.
 * @param {string} location Meta box location to test.
 *
 * @return {boolean} Whether the meta box location is active and visible.
 */

function isMetaBoxLocationVisible(state, location) {
  return isMetaBoxLocationActive(state, location) && Object(external_this_lodash_["some"])(getMetaBoxesPerLocation(state, location), function (_ref) {
    var id = _ref.id;
    return selectors_isEditorPanelEnabled(state, "meta-box-".concat(id));
  });
}
/**
 * Returns true if there is an active meta box in the given location, or false
 * otherwise.
 *
 * @param {Object} state    Post editor state.
 * @param {string} location Meta box location to test.
 *
 * @return {boolean} Whether the meta box location is active.
 */

function isMetaBoxLocationActive(state, location) {
  var metaBoxes = getMetaBoxesPerLocation(state, location);
  return !!metaBoxes && metaBoxes.length !== 0;
}
/**
 * Returns the list of all the available meta boxes for a given location.
 *
 * @param {Object} state    Global application state.
 * @param {string} location Meta box location to test.
 *
 * @return {?Array} List of meta boxes.
 */

function getMetaBoxesPerLocation(state, location) {
  return state.metaBoxes.locations[location];
}
/**
 * Returns the list of all the available meta boxes.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} List of meta boxes.
 */

var getAllMetaBoxes = Object(rememo["a" /* default */])(function (state) {
  return Object(external_this_lodash_["flatten"])(Object(external_this_lodash_["values"])(state.metaBoxes.locations));
}, function (state) {
  return [state.metaBoxes.locations];
});
/**
 * Returns true if the post is using Meta Boxes
 *
 * @param  {Object} state Global application state
 *
 * @return {boolean} Whether there are metaboxes or not.
 */

function hasMetaBoxes(state) {
  return getActiveMetaBoxLocations(state).length > 0;
}
/**
 * Returns true if the Meta Boxes are being saved.
 *
 * @param   {Object}  state Global application state.
 *
 * @return {boolean} Whether the metaboxes are being saved.
 */

function selectors_isSavingMetaBoxes(state) {
  return state.metaBoxes.isSaving;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/utils/meta-boxes.js
/**
 * Function returning the current Meta Boxes DOM Node in the editor
 * whether the meta box area is opened or not.
 * If the MetaBox Area is visible returns it, and returns the original container instead.
 *
 * @param   {string} location Meta Box location.
 * @return {string}          HTML content.
 */
var getMetaBoxContainer = function getMetaBoxContainer(location) {
  var area = document.querySelector(".edit-post-meta-boxes-area.is-".concat(location, " .metabox-location-").concat(location));

  if (area) {
    return area;
  }

  return document.querySelector('#metaboxes .metabox-location-' + location);
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/effects.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




var saveMetaboxUnsubscribe;
var effects = {
  SET_META_BOXES_PER_LOCATIONS: function SET_META_BOXES_PER_LOCATIONS(action, store) {
    // Allow toggling metaboxes panels
    // We need to wait for all scripts to load
    // If the meta box loads the post script, it will already trigger this.
    // After merge in Core, make sure to drop the timeout and update the postboxes script
    // to avoid the double binding.
    setTimeout(function () {
      var postType = Object(external_this_wp_data_["select"])('core/editor').getCurrentPostType();

      if (window.postboxes.page !== postType) {
        window.postboxes.add_postbox_toggles(postType);
      }
    });
    var wasSavingPost = Object(external_this_wp_data_["select"])('core/editor').isSavingPost();
    var wasAutosavingPost = Object(external_this_wp_data_["select"])('core/editor').isAutosavingPost(); // Meta boxes are initialized once at page load. It is not necessary to
    // account for updates on each state change.
    //
    // See: https://github.com/WordPress/WordPress/blob/5.1.1/wp-admin/includes/post.php#L2307-L2309

    var hasActiveMetaBoxes = Object(external_this_wp_data_["select"])('core/edit-post').hasMetaBoxes(); // First remove any existing subscription in order to prevent multiple saves

    if (!!saveMetaboxUnsubscribe) {
      saveMetaboxUnsubscribe();
    } // Save metaboxes when performing a full save on the post.


    saveMetaboxUnsubscribe = Object(external_this_wp_data_["subscribe"])(function () {
      var isSavingPost = Object(external_this_wp_data_["select"])('core/editor').isSavingPost();
      var isAutosavingPost = Object(external_this_wp_data_["select"])('core/editor').isAutosavingPost(); // Save metaboxes on save completion, except for autosaves that are not a post preview.

      var shouldTriggerMetaboxesSave = hasActiveMetaBoxes && wasSavingPost && !isSavingPost && !wasAutosavingPost; // Save current state for next inspection.

      wasSavingPost = isSavingPost;
      wasAutosavingPost = isAutosavingPost;

      if (shouldTriggerMetaboxesSave) {
        store.dispatch(requestMetaBoxUpdates());
      }
    });
  },
  REQUEST_META_BOX_UPDATES: function REQUEST_META_BOX_UPDATES(action, store) {
    // Saves the wp_editor fields
    if (window.tinyMCE) {
      window.tinyMCE.triggerSave();
    }

    var state = store.getState(); // Additional data needed for backward compatibility.
    // If we do not provide this data, the post will be overridden with the default values.

    var post = Object(external_this_wp_data_["select"])('core/editor').getCurrentPost(state);
    var additionalData = [post.comment_status ? ['comment_status', post.comment_status] : false, post.ping_status ? ['ping_status', post.ping_status] : false, post.sticky ? ['sticky', post.sticky] : false, post.author ? ['post_author', post.author] : false].filter(Boolean); // We gather all the metaboxes locations data and the base form data

    var baseFormData = new window.FormData(document.querySelector('.metabox-base-form'));
    var formDataToMerge = [baseFormData].concat(Object(toConsumableArray["a" /* default */])(getActiveMetaBoxLocations(state).map(function (location) {
      return new window.FormData(getMetaBoxContainer(location));
    }))); // Merge all form data objects into a single one.

    var formData = Object(external_this_lodash_["reduce"])(formDataToMerge, function (memo, currentFormData) {
      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        for (var _iterator = currentFormData[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
          var _step$value = Object(slicedToArray["a" /* default */])(_step.value, 2),
              key = _step$value[0],
              value = _step$value[1];

          memo.append(key, value);
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

      return memo;
    }, new window.FormData());
    additionalData.forEach(function (_ref) {
      var _ref2 = Object(slicedToArray["a" /* default */])(_ref, 2),
          key = _ref2[0],
          value = _ref2[1];

      return formData.append(key, value);
    }); // Save the metaboxes

    external_this_wp_apiFetch_default()({
      url: window._wpMetaBoxUrl,
      method: 'POST',
      body: formData,
      parse: false
    }).then(function () {
      return store.dispatch(metaBoxUpdatesSuccess());
    });
  },
  SWITCH_MODE: function SWITCH_MODE(action) {
    // Unselect blocks when we switch to the code editor.
    if (action.mode !== 'visual') {
      Object(external_this_wp_data_["dispatch"])('core/block-editor').clearSelectedBlock();
    }

    var message = action.mode === 'visual' ? Object(external_this_wp_i18n_["__"])('Visual editor selected') : Object(external_this_wp_i18n_["__"])('Code editor selected');
    Object(external_this_wp_a11y_["speak"])(message, 'assertive');
  }
};
/* harmony default export */ var store_effects = (effects);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/middlewares.js


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
  var middlewares = [refx_default()(store_effects)];

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
  enhancedDispatch = external_this_lodash_["flowRight"].apply(void 0, Object(toConsumableArray["a" /* default */])(chain))(store.dispatch);
  store.dispatch = enhancedDispatch;
  return store;
}

/* harmony default export */ var store_middlewares = (applyMiddlewares);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/controls.js


/**
 * WordPress dependencies
 */

/**
 * Calls a selector using the current state.
 *
 * @param {string} storeName    Store name.
 * @param {string} selectorName Selector name.
 * @param  {Array} args         Selector arguments.
 *
 * @return {Object} control descriptor.
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
var controls = {
  SELECT: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref) {
      var _registry$select;

      var storeName = _ref.storeName,
          selectorName = _ref.selectorName,
          args = _ref.args;
      return (_registry$select = registry.select(storeName))[selectorName].apply(_registry$select, Object(toConsumableArray["a" /* default */])(args));
    };
  })
};
/* harmony default export */ var store_controls = (controls);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
var STORE_KEY = 'core/edit-post';
/**
 * CSS selector string for the admin bar view post link anchor tag.
 *
 * @type {string}
 */

var VIEW_AS_LINK_SELECTOR = '#wp-admin-bar-view a';
/**
 * CSS selector string for the admin bar preview post link anchor tag.
 *
 * @type {string}
 */

var VIEW_AS_PREVIEW_LINK_SELECTOR = '#wp-admin-bar-preview a';

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */







var store_store = Object(external_this_wp_data_["registerStore"])(STORE_KEY, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  controls: store_controls,
  persist: ['preferences']
});
store_middlewares(store_store);
/* harmony default export */ var build_module_store = (store_store);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(14);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(16);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(17);

// EXTERNAL MODULE: ./node_modules/memize/index.js
var memize = __webpack_require__(46);
var memize_default = /*#__PURE__*/__webpack_require__.n(memize);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/prevent-event-discovery.js
/* harmony default export */ var prevent_event_discovery = ({
  't a l e s o f g u t e n b e r g': function tALESOFGUTENBERG(event) {
    if (!document.activeElement.classList.contains('edit-post-visual-editor') && document.activeElement !== document.body) {
      return;
    }

    event.preventDefault();
    window.wp.data.dispatch('core/block-editor').insertBlock(window.wp.blocks.createBlock('core/paragraph', {
      content: '🐡🐢🦀🐤🦋🐘🐧🐹🦁🦄🦍🐼🐿🎃🐴🐝🐆🦕🦔🌱🍇π🍌🐉💧🥨🌌🍂🍠🥦🥚🥝🎟🥥🥒🛵🥖🍒🍯🎾🎲🐺🐚🐮⌛️'
    }));
  }
});

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(11);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js
var library_close = __webpack_require__(120);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/text-editor/index.js


/**
 * WordPress dependencies
 */








function TextEditor(_ref) {
  var onExit = _ref.onExit,
      isRichEditingEnabled = _ref.isRichEditingEnabled;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-text-editor"
  }, isRichEditingEnabled && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-text-editor__toolbar"
  }, Object(external_this_wp_element_["createElement"])("h2", null, Object(external_this_wp_i18n_["__"])('Editing Code')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    onClick: onExit,
    icon: library_close["a" /* default */],
    shortcut: external_this_wp_keycodes_["displayShortcut"].secondary('m')
  }, Object(external_this_wp_i18n_["__"])('Exit Code Editor')), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["TextEditorGlobalKeyboardShortcuts"], null)), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-text-editor__body"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTitle"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTextEditor"], null)));
}

/* harmony default export */ var text_editor = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isRichEditingEnabled: select('core/editor').getEditorSettings().richEditingEnabled
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onExit: function onExit() {
      dispatch('core/edit-post').switchEditorMode('visual');
    }
  };
}))(TextEditor));

// EXTERNAL MODULE: external {"this":["wp","primitives"]}
var external_this_wp_primitives_ = __webpack_require__(9);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/cog.js


/**
 * WordPress dependencies
 */

var cog = Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["Path"], {
  d: "M18 12h-2.18c-.17.7-.44 1.35-.81 1.93l1.54 1.54-2.1 2.1-1.54-1.54c-.58.36-1.23.63-1.91.79V19H8v-2.18c-.68-.16-1.33-.43-1.91-.79l-1.54 1.54-2.12-2.12 1.54-1.54c-.36-.58-.63-1.23-.79-1.91H1V9.03h2.17c.16-.7.44-1.35.8-1.94L2.43 5.55l2.1-2.1 1.54 1.54c.58-.37 1.24-.64 1.93-.81V2h3v2.18c.68.16 1.33.43 1.91.79l1.54-1.54 2.12 2.12-1.54 1.54c.36.59.64 1.24.8 1.94H18V12zm-8.5 1.5c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3z"
}));
/* harmony default export */ var library_cog = (cog);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/block-inspector-button.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





function BlockInspectorButton(_ref) {
  var _ref$onClick = _ref.onClick,
      _onClick = _ref$onClick === void 0 ? external_this_lodash_["noop"] : _ref$onClick,
      _ref$small = _ref.small,
      small = _ref$small === void 0 ? false : _ref$small,
      speak = _ref.speak;

  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      shortcut: select('core/keyboard-shortcuts').getShortcutRepresentation('core/edit-post/toggle-sidebar'),
      areAdvancedSettingsOpened: select('core/edit-post').getActiveGeneralSidebarName() === 'edit-post/block'
    };
  }, []),
      shortcut = _useSelect.shortcut,
      areAdvancedSettingsOpened = _useSelect.areAdvancedSettingsOpened;

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/edit-post'),
      openGeneralSidebar = _useDispatch.openGeneralSidebar,
      closeGeneralSidebar = _useDispatch.closeGeneralSidebar;

  var speakMessage = function speakMessage() {
    if (areAdvancedSettingsOpened) {
      speak(Object(external_this_wp_i18n_["__"])('Block settings closed'));
    } else {
      speak(Object(external_this_wp_i18n_["__"])('Additional settings are now available in the Editor block settings sidebar'));
    }
  };

  var label = areAdvancedSettingsOpened ? Object(external_this_wp_i18n_["__"])('Hide Block Settings') : Object(external_this_wp_i18n_["__"])('Show Block Settings');
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
      if (areAdvancedSettingsOpened) {
        closeGeneralSidebar();
      } else {
        openGeneralSidebar('edit-post/block');
        speakMessage();

        _onClick();
      }
    },
    icon: library_cog,
    shortcut: shortcut
  }, !small && label);
}
/* harmony default export */ var block_inspector_button = (Object(external_this_wp_components_["withSpokenMessages"])(BlockInspectorButton));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-group.js



function plugin_block_settings_menu_group_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function plugin_block_settings_menu_group_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { plugin_block_settings_menu_group_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { plugin_block_settings_menu_group_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




var plugin_block_settings_menu_group_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginBlockSettingsMenuGroup'),
    PluginBlockSettingsMenuGroup = plugin_block_settings_menu_group_createSlotFill.Fill,
    plugin_block_settings_menu_group_Slot = plugin_block_settings_menu_group_createSlotFill.Slot;

var plugin_block_settings_menu_group_PluginBlockSettingsMenuGroupSlot = function PluginBlockSettingsMenuGroupSlot(_ref) {
  var fillProps = _ref.fillProps,
      selectedBlocks = _ref.selectedBlocks;
  selectedBlocks = Object(external_this_lodash_["map"])(selectedBlocks, function (block) {
    return block.name;
  });
  return Object(external_this_wp_element_["createElement"])(plugin_block_settings_menu_group_Slot, {
    fillProps: plugin_block_settings_menu_group_objectSpread({}, fillProps, {
      selectedBlocks: selectedBlocks
    })
  }, function (fills) {
    return !Object(external_this_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", {
      className: "block-editor-block-settings-menu__separator"
    }), fills);
  });
};

PluginBlockSettingsMenuGroup.Slot = Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var clientIds = _ref2.fillProps.clientIds;
  return {
    selectedBlocks: select('core/block-editor').getBlocksByClientId(clientIds)
  };
})(plugin_block_settings_menu_group_PluginBlockSettingsMenuGroupSlot);
/* harmony default export */ var plugin_block_settings_menu_group = (PluginBlockSettingsMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function VisualEditor() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockSelectionClearer"], {
    className: "edit-post-visual-editor editor-styles-wrapper"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["VisualEditorGlobalKeyboardShortcuts"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MultiSelectScrollIntoView"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Popover"].Slot, {
    name: "block-toolbar"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["Typewriter"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["CopyHandler"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["WritingFlow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["ObserveTyping"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["CopyHandler"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTitle"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockList"], null)))))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalBlockSettingsMenuFirstItem"], null, function (_ref) {
    var onClose = _ref.onClose;
    return Object(external_this_wp_element_["createElement"])(block_inspector_button, {
      onClick: onClose
    });
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalBlockSettingsMenuPluginsExtension"], null, function (_ref2) {
    var clientIds = _ref2.clientIds,
        onClose = _ref2.onClose;
    return Object(external_this_wp_element_["createElement"])(plugin_block_settings_menu_group.Slot, {
      fillProps: {
        clientIds: clientIds,
        onClose: onClose
      }
    });
  }));
}

/* harmony default export */ var visual_editor = (VisualEditor);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js
/**
 * WordPress dependencies
 */





function KeyboardShortcuts() {
  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    var settings = select('core/editor').getEditorSettings();
    return {
      getBlockSelectionStart: select('core/block-editor').getBlockSelectionStart,
      getEditorMode: select('core/edit-post').getEditorMode,
      isEditorSidebarOpened: select('core/edit-post').isEditorSidebarOpened,
      richEditingEnabled: settings.richEditingEnabled,
      codeEditingEnabled: settings.codeEditingEnabled
    };
  }),
      getBlockSelectionStart = _useSelect.getBlockSelectionStart,
      getEditorMode = _useSelect.getEditorMode,
      isEditorSidebarOpened = _useSelect.isEditorSidebarOpened,
      richEditingEnabled = _useSelect.richEditingEnabled,
      codeEditingEnabled = _useSelect.codeEditingEnabled;

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/edit-post'),
      switchEditorMode = _useDispatch.switchEditorMode,
      openGeneralSidebar = _useDispatch.openGeneralSidebar,
      closeGeneralSidebar = _useDispatch.closeGeneralSidebar;

  var _useDispatch2 = Object(external_this_wp_data_["useDispatch"])('core/keyboard-shortcuts'),
      registerShortcut = _useDispatch2.registerShortcut;

  Object(external_this_wp_element_["useEffect"])(function () {
    registerShortcut({
      name: 'core/edit-post/toggle-mode',
      category: 'global',
      description: Object(external_this_wp_i18n_["__"])('Switch between Visual editor and Code editor.'),
      keyCombination: {
        modifier: 'secondary',
        character: 'm'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-block-navigation',
      category: 'global',
      description: Object(external_this_wp_i18n_["__"])('Open the block navigation menu.'),
      keyCombination: {
        modifier: 'access',
        character: 'o'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-sidebar',
      category: 'global',
      description: Object(external_this_wp_i18n_["__"])('Show or hide the settings sidebar.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: ','
      }
    });
    registerShortcut({
      name: 'core/edit-post/next-region',
      category: 'global',
      description: Object(external_this_wp_i18n_["__"])('Navigate to the next part of the editor.'),
      keyCombination: {
        modifier: 'ctrl',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'n'
      }]
    });
    registerShortcut({
      name: 'core/edit-post/previous-region',
      category: 'global',
      description: Object(external_this_wp_i18n_["__"])('Navigate to the previous part of the editor.'),
      keyCombination: {
        modifier: 'ctrlShift',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'p'
      }]
    });
    registerShortcut({
      name: 'core/edit-post/keyboard-shortcuts',
      category: 'main',
      description: Object(external_this_wp_i18n_["__"])('Display these keyboard shortcuts.'),
      keyCombination: {
        modifier: 'access',
        character: 'h'
      }
    });
  }, []);
  Object(external_this_wp_keyboardShortcuts_["useShortcut"])('core/edit-post/toggle-mode', function () {
    switchEditorMode(getEditorMode() === 'visual' ? 'text' : 'visual');
  }, {
    bindGlobal: true,
    isDisabled: !richEditingEnabled || !codeEditingEnabled
  });
  Object(external_this_wp_keyboardShortcuts_["useShortcut"])('core/edit-post/toggle-sidebar', function (event) {
    // This shortcut has no known clashes, but use preventDefault to prevent any
    // obscure shortcuts from triggering.
    event.preventDefault();

    if (isEditorSidebarOpened()) {
      closeGeneralSidebar();
    } else {
      var sidebarToOpen = getBlockSelectionStart() ? 'edit-post/block' : 'edit-post/document';
      openGeneralSidebar(sidebarToOpen);
    }
  }, {
    bindGlobal: true
  });
  return null;
}

/* harmony default export */ var keyboard_shortcuts = (KeyboardShortcuts);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * WordPress dependencies
 */

var textFormattingShortcuts = [{
  keyCombination: {
    modifier: 'primary',
    character: 'b'
  },
  description: Object(external_this_wp_i18n_["__"])('Make the selected text bold.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'i'
  },
  description: Object(external_this_wp_i18n_["__"])('Make the selected text italic.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'k'
  },
  description: Object(external_this_wp_i18n_["__"])('Convert the selected text into a link.')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'k'
  },
  description: Object(external_this_wp_i18n_["__"])('Remove a link.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'u'
  },
  description: Object(external_this_wp_i18n_["__"])('Underline the selected text.')
}];

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/shortcut.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function KeyCombination(_ref) {
  var keyCombination = _ref.keyCombination,
      forceAriaLabel = _ref.forceAriaLabel;
  var shortcut = keyCombination.modifier ? external_this_wp_keycodes_["displayShortcutList"][keyCombination.modifier](keyCombination.character) : keyCombination.character;
  var ariaLabel = keyCombination.modifier ? external_this_wp_keycodes_["shortcutAriaLabel"][keyCombination.modifier](keyCombination.character) : keyCombination.character;
  return Object(external_this_wp_element_["createElement"])("kbd", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel
  }, Object(external_this_lodash_["castArray"])(shortcut).map(function (character, index) {
    if (character === '+') {
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], {
        key: index
      }, character);
    }

    return Object(external_this_wp_element_["createElement"])("kbd", {
      key: index,
      className: "edit-post-keyboard-shortcut-help-modal__shortcut-key"
    }, character);
  }));
}

function Shortcut(_ref2) {
  var description = _ref2.description,
      keyCombination = _ref2.keyCombination,
      _ref2$aliases = _ref2.aliases,
      aliases = _ref2$aliases === void 0 ? [] : _ref2$aliases,
      ariaLabel = _ref2.ariaLabel;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-description"
  }, description), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-term"
  }, Object(external_this_wp_element_["createElement"])(KeyCombination, {
    keyCombination: keyCombination,
    forceAriaLabel: ariaLabel
  }), aliases.map(function (alias, index) {
    return Object(external_this_wp_element_["createElement"])(KeyCombination, {
      keyCombination: alias,
      forceAriaLabel: ariaLabel,
      key: index
    });
  })));
}

/* harmony default export */ var keyboard_shortcut_help_modal_shortcut = (Shortcut);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function DynamicShortcut(_ref) {
  var name = _ref.name;

  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    var _select = select('core/keyboard-shortcuts'),
        getShortcutKeyCombination = _select.getShortcutKeyCombination,
        getShortcutDescription = _select.getShortcutDescription,
        getShortcutAliases = _select.getShortcutAliases;

    return {
      keyCombination: getShortcutKeyCombination(name),
      aliases: getShortcutAliases(name),
      description: getShortcutDescription(name)
    };
  }),
      keyCombination = _useSelect.keyCombination,
      description = _useSelect.description,
      aliases = _useSelect.aliases;

  if (!keyCombination) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_shortcut, {
    keyCombination: keyCombination,
    description: description,
    aliases: aliases
  });
}

/* harmony default export */ var dynamic_shortcut = (DynamicShortcut);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/index.js


/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




var MODAL_NAME = 'edit-post/keyboard-shortcut-help';

var keyboard_shortcut_help_modal_ShortcutList = function ShortcutList(_ref) {
  var shortcuts = _ref.shortcuts;
  return (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */

    /* eslint-disable jsx-a11y/no-redundant-roles */
    Object(external_this_wp_element_["createElement"])("ul", {
      className: "edit-post-keyboard-shortcut-help-modal__shortcut-list",
      role: "list"
    }, shortcuts.map(function (shortcut, index) {
      return Object(external_this_wp_element_["createElement"])("li", {
        className: "edit-post-keyboard-shortcut-help-modal__shortcut",
        key: index
      }, Object(external_this_lodash_["isString"])(shortcut) ? Object(external_this_wp_element_["createElement"])(dynamic_shortcut, {
        name: shortcut
      }) : Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_shortcut, shortcut));
    }))
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
};

var keyboard_shortcut_help_modal_ShortcutSection = function ShortcutSection(_ref2) {
  var title = _ref2.title,
      shortcuts = _ref2.shortcuts,
      className = _ref2.className;
  return Object(external_this_wp_element_["createElement"])("section", {
    className: classnames_default()('edit-post-keyboard-shortcut-help-modal__section', className)
  }, !!title && Object(external_this_wp_element_["createElement"])("h2", {
    className: "edit-post-keyboard-shortcut-help-modal__section-title"
  }, title), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutList, {
    shortcuts: shortcuts
  }));
};

var keyboard_shortcut_help_modal_ShortcutCategorySection = function ShortcutCategorySection(_ref3) {
  var title = _ref3.title,
      categoryName = _ref3.categoryName,
      _ref3$additionalShort = _ref3.additionalShortcuts,
      additionalShortcuts = _ref3$additionalShort === void 0 ? [] : _ref3$additionalShort;
  var categoryShortcuts = Object(external_this_wp_data_["useSelect"])(function (select) {
    return select('core/keyboard-shortcuts').getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutSection, {
    title: title,
    shortcuts: categoryShortcuts.concat(additionalShortcuts)
  });
};

function KeyboardShortcutHelpModal(_ref4) {
  var isModalActive = _ref4.isModalActive,
      toggleModal = _ref4.toggleModal;
  Object(external_this_wp_keyboardShortcuts_["useShortcut"])('core/edit-post/keyboard-shortcuts', toggleModal, {
    bindGlobal: true
  });

  if (!isModalActive) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Modal"], {
    className: "edit-post-keyboard-shortcut-help-modal",
    title: Object(external_this_wp_i18n_["__"])('Keyboard shortcuts'),
    closeLabel: Object(external_this_wp_i18n_["__"])('Close'),
    onRequestClose: toggleModal
  }, Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutSection, {
    className: "edit-post-keyboard-shortcut-help-modal__main-shortcuts",
    shortcuts: ['core/edit-post/keyboard-shortcuts']
  }), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutCategorySection, {
    title: Object(external_this_wp_i18n_["__"])('Global shortcuts'),
    categoryName: "global"
  }), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutCategorySection, {
    title: Object(external_this_wp_i18n_["__"])('Selection shortcuts'),
    categoryName: "selection"
  }), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutCategorySection, {
    title: Object(external_this_wp_i18n_["__"])('Block shortcuts'),
    categoryName: "block",
    additionalShortcuts: [{
      keyCombination: {
        character: '/'
      },
      description: Object(external_this_wp_i18n_["__"])('Change the block type after adding a new paragraph.'),

      /* translators: The forward-slash character. e.g. '/'. */
      ariaLabel: Object(external_this_wp_i18n_["__"])('Forward-slash')
    }]
  }), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal_ShortcutSection, {
    title: Object(external_this_wp_i18n_["__"])('Text formatting'),
    shortcuts: textFormattingShortcuts
  }));
}
/* harmony default export */ var keyboard_shortcut_help_modal = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isModalActive: select('core/edit-post').isModalActive(MODAL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref5) {
  var isModalActive = _ref5.isModalActive;

  var _dispatch = dispatch('core/edit-post'),
      openModal = _dispatch.openModal,
      closeModal = _dispatch.closeModal;

  return {
    toggleModal: function toggleModal() {
      return isModalActive ? closeModal() : openModal(MODAL_NAME);
    }
  };
})])(KeyboardShortcutHelpModal));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/manage-blocks-modal/checklist.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function BlockTypesChecklist(_ref) {
  var blockTypes = _ref.blockTypes,
      value = _ref.value,
      onItemChange = _ref.onItemChange;
  return Object(external_this_wp_element_["createElement"])("ul", {
    className: "edit-post-manage-blocks-modal__checklist"
  }, blockTypes.map(function (blockType) {
    return Object(external_this_wp_element_["createElement"])("li", {
      key: blockType.name,
      className: "edit-post-manage-blocks-modal__checklist-item"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
      label: Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, blockType.title, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
        icon: blockType.icon
      })),
      checked: value.includes(blockType.name),
      onChange: Object(external_this_lodash_["partial"])(onItemChange, blockType.name)
    }));
  }));
}

/* harmony default export */ var checklist = (BlockTypesChecklist);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/edit-post-settings/index.js
/**
 * WordPress dependencies
 */

var EditPostSettings = Object(external_this_wp_element_["createContext"])({});
/* harmony default export */ var edit_post_settings = (EditPostSettings);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/manage-blocks-modal/category.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




function BlockManagerCategory(_ref) {
  var instanceId = _ref.instanceId,
      category = _ref.category,
      blockTypes = _ref.blockTypes,
      hiddenBlockTypes = _ref.hiddenBlockTypes,
      toggleVisible = _ref.toggleVisible,
      toggleAllVisible = _ref.toggleAllVisible;
  var settings = Object(external_this_wp_element_["useContext"])(edit_post_settings);
  var allowedBlockTypes = settings.allowedBlockTypes;
  var filteredBlockTypes = Object(external_this_wp_element_["useMemo"])(function () {
    if (allowedBlockTypes === true) {
      return blockTypes;
    }

    return blockTypes.filter(function (_ref2) {
      var name = _ref2.name;
      return Object(external_this_lodash_["includes"])(allowedBlockTypes || [], name);
    });
  }, [allowedBlockTypes, blockTypes]);

  if (!filteredBlockTypes.length) {
    return null;
  }

  var checkedBlockNames = external_this_lodash_["without"].apply(void 0, [Object(external_this_lodash_["map"])(filteredBlockTypes, 'name')].concat(Object(toConsumableArray["a" /* default */])(hiddenBlockTypes)));
  var titleId = 'edit-post-manage-blocks-modal__category-title-' + instanceId;
  var isAllChecked = checkedBlockNames.length === filteredBlockTypes.length;
  var ariaChecked;

  if (isAllChecked) {
    ariaChecked = 'true';
  } else if (checkedBlockNames.length > 0) {
    ariaChecked = 'mixed';
  } else {
    ariaChecked = 'false';
  }

  return Object(external_this_wp_element_["createElement"])("div", {
    role: "group",
    "aria-labelledby": titleId,
    className: "edit-post-manage-blocks-modal__category"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
    checked: isAllChecked,
    onChange: toggleAllVisible,
    className: "edit-post-manage-blocks-modal__category-title",
    "aria-checked": ariaChecked,
    label: Object(external_this_wp_element_["createElement"])("span", {
      id: titleId
    }, category.title)
  }), Object(external_this_wp_element_["createElement"])(checklist, {
    blockTypes: filteredBlockTypes,
    value: checkedBlockNames,
    onItemChange: toggleVisible
  }));
}

/* harmony default export */ var manage_blocks_modal_category = (Object(external_this_wp_compose_["compose"])([external_this_wp_compose_["withInstanceId"], Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/edit-post'),
      getPreference = _select.getPreference;

  return {
    hiddenBlockTypes: getPreference('hiddenBlockTypes')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  var _dispatch = dispatch('core/edit-post'),
      showBlockTypes = _dispatch.showBlockTypes,
      hideBlockTypes = _dispatch.hideBlockTypes;

  return {
    toggleVisible: function toggleVisible(blockName, nextIsChecked) {
      if (nextIsChecked) {
        showBlockTypes(blockName);
      } else {
        hideBlockTypes(blockName);
      }
    },
    toggleAllVisible: function toggleAllVisible(nextIsChecked) {
      var blockNames = Object(external_this_lodash_["map"])(ownProps.blockTypes, 'name');

      if (nextIsChecked) {
        showBlockTypes(blockNames);
      } else {
        hideBlockTypes(blockNames);
      }
    }
  };
})])(BlockManagerCategory));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/manage-blocks-modal/manager.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function BlockManager(_ref) {
  var search = _ref.search,
      setState = _ref.setState,
      blockTypes = _ref.blockTypes,
      categories = _ref.categories,
      hasBlockSupport = _ref.hasBlockSupport,
      isMatchingSearchTerm = _ref.isMatchingSearchTerm,
      numberOfHiddenBlocks = _ref.numberOfHiddenBlocks;
  // Filtering occurs here (as opposed to `withSelect`) to avoid wasted
  // wasted renders by consequence of `Array#filter` producing a new
  // value reference on each call.
  blockTypes = blockTypes.filter(function (blockType) {
    return hasBlockSupport(blockType, 'inserter', true) && (!search || isMatchingSearchTerm(blockType, search)) && (!blockType.parent || Object(external_this_lodash_["includes"])(blockType.parent, 'core/post-content'));
  });
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-manage-blocks-modal__content"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
    type: "search",
    label: Object(external_this_wp_i18n_["__"])('Search for a block'),
    value: search,
    onChange: function onChange(nextSearch) {
      return setState({
        search: nextSearch
      });
    },
    className: "edit-post-manage-blocks-modal__search"
  }), !!numberOfHiddenBlocks && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-manage-blocks-modal__disabled-blocks-count"
  }, Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('%1$d block is disabled.', '%1$d blocks are disabled.', numberOfHiddenBlocks), numberOfHiddenBlocks)), Object(external_this_wp_element_["createElement"])("div", {
    tabIndex: "0",
    role: "region",
    "aria-label": Object(external_this_wp_i18n_["__"])('Available block types'),
    className: "edit-post-manage-blocks-modal__results"
  }, blockTypes.length === 0 && Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-manage-blocks-modal__no-results"
  }, Object(external_this_wp_i18n_["__"])('No blocks found.')), categories.map(function (category) {
    return Object(external_this_wp_element_["createElement"])(manage_blocks_modal_category, {
      key: category.slug,
      category: category,
      blockTypes: Object(external_this_lodash_["filter"])(blockTypes, {
        category: category.slug
      })
    });
  })));
}

/* harmony default export */ var manager = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_compose_["withState"])({
  search: ''
}), Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/blocks'),
      getBlockTypes = _select.getBlockTypes,
      getCategories = _select.getCategories,
      hasBlockSupport = _select.hasBlockSupport,
      isMatchingSearchTerm = _select.isMatchingSearchTerm;

  var _select2 = select('core/edit-post'),
      getPreference = _select2.getPreference;

  var hiddenBlockTypes = getPreference('hiddenBlockTypes');
  var numberOfHiddenBlocks = Object(external_this_lodash_["isArray"])(hiddenBlockTypes) && hiddenBlockTypes.length;
  return {
    blockTypes: getBlockTypes(),
    categories: getCategories(),
    hasBlockSupport: hasBlockSupport,
    isMatchingSearchTerm: isMatchingSearchTerm,
    numberOfHiddenBlocks: numberOfHiddenBlocks
  };
})])(BlockManager));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/manage-blocks-modal/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/**
 * Unique identifier for Manage Blocks modal.
 *
 * @type {string}
 */

var manage_blocks_modal_MODAL_NAME = 'edit-post/manage-blocks';
function ManageBlocksModal(_ref) {
  var isActive = _ref.isActive,
      closeModal = _ref.closeModal;

  if (!isActive) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Modal"], {
    className: "edit-post-manage-blocks-modal",
    title: Object(external_this_wp_i18n_["__"])('Block Manager'),
    closeLabel: Object(external_this_wp_i18n_["__"])('Close'),
    onRequestClose: closeModal
  }, Object(external_this_wp_element_["createElement"])(manager, null));
}
/* harmony default export */ var manage_blocks_modal = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/edit-post'),
      isModalActive = _select.isModalActive;

  return {
    isActive: isModalActive(manage_blocks_modal_MODAL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      closeModal = _dispatch.closeModal;

  return {
    closeModal: closeModal
  };
})])(ManageBlocksModal));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/section.js


var section_Section = function Section(_ref) {
  var title = _ref.title,
      children = _ref.children;
  return Object(external_this_wp_element_["createElement"])("section", {
    className: "edit-post-options-modal__section"
  }, Object(external_this_wp_element_["createElement"])("h2", {
    className: "edit-post-options-modal__section-title"
  }, title), children);
};

/* harmony default export */ var section = (section_Section);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/base.js


/**
 * WordPress dependencies
 */


function BaseOption(_ref) {
  var label = _ref.label,
      isChecked = _ref.isChecked,
      onChange = _ref.onChange,
      children = _ref.children;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-options-modal__option"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["CheckboxControl"], {
    label: label,
    checked: isChecked,
    onChange: onChange
  }), children);
}

/* harmony default export */ var base = (BaseOption);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-custom-fields.js



/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function CustomFieldsConfirmation(_ref) {
  var willEnable = _ref.willEnable;

  var _useState = Object(external_this_wp_element_["useState"])(false),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      isReloading = _useState2[0],
      setIsReloading = _useState2[1];

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-options-modal__custom-fields-confirmation-message"
  }, Object(external_this_wp_i18n_["__"])('A page reload is required for this change. Make sure your content is saved before reloading.')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    className: "edit-post-options-modal__custom-fields-confirmation-button",
    isSecondary: true,
    isBusy: isReloading,
    disabled: isReloading,
    onClick: function onClick() {
      setIsReloading(true);
      document.getElementById('toggle-custom-fields-form').submit();
    }
  }, willEnable ? Object(external_this_wp_i18n_["__"])('Enable & Reload') : Object(external_this_wp_i18n_["__"])('Disable & Reload')));
}
function EnableCustomFieldsOption(_ref2) {
  var label = _ref2.label,
      areCustomFieldsEnabled = _ref2.areCustomFieldsEnabled;

  var _useState3 = Object(external_this_wp_element_["useState"])(areCustomFieldsEnabled),
      _useState4 = Object(slicedToArray["a" /* default */])(_useState3, 2),
      isChecked = _useState4[0],
      setIsChecked = _useState4[1];

  return Object(external_this_wp_element_["createElement"])(base, {
    label: label,
    isChecked: isChecked,
    onChange: setIsChecked
  }, isChecked !== areCustomFieldsEnabled && Object(external_this_wp_element_["createElement"])(CustomFieldsConfirmation, {
    willEnable: isChecked
  }));
}
/* harmony default export */ var enable_custom_fields = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    areCustomFieldsEnabled: !!select('core/editor').getEditorSettings().enableCustomFields
  };
})(EnableCustomFieldsOption));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-panel.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/* harmony default export */ var enable_panel = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var panelName = _ref.panelName;

  var _select = select('core/edit-post'),
      isEditorPanelEnabled = _select.isEditorPanelEnabled,
      isEditorPanelRemoved = _select.isEditorPanelRemoved;

  return {
    isRemoved: isEditorPanelRemoved(panelName),
    isChecked: isEditorPanelEnabled(panelName)
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isRemoved = _ref2.isRemoved;
  return !isRemoved;
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3) {
  var panelName = _ref3.panelName;
  return {
    onChange: function onChange() {
      return dispatch('core/edit-post').toggleEditorPanelEnabled(panelName);
    }
  };
}))(base));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-plugin-document-setting-panel.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var enable_plugin_document_setting_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('EnablePluginDocumentSettingPanelOption'),
    Fill = enable_plugin_document_setting_panel_createSlotFill.Fill,
    enable_plugin_document_setting_panel_Slot = enable_plugin_document_setting_panel_createSlotFill.Slot;

var enable_plugin_document_setting_panel_EnablePluginDocumentSettingPanelOption = function EnablePluginDocumentSettingPanelOption(_ref) {
  var label = _ref.label,
      panelName = _ref.panelName;
  return Object(external_this_wp_element_["createElement"])(Fill, null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: label,
    panelName: panelName
  }));
};

enable_plugin_document_setting_panel_EnablePluginDocumentSettingPanelOption.Slot = enable_plugin_document_setting_panel_Slot;
/* harmony default export */ var enable_plugin_document_setting_panel = (enable_plugin_document_setting_panel_EnablePluginDocumentSettingPanelOption);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-publish-sidebar.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/* harmony default export */ var enable_publish_sidebar = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isChecked: select('core/editor').isPublishSidebarEnabled()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/editor'),
      enablePublishSidebar = _dispatch.enablePublishSidebar,
      disablePublishSidebar = _dispatch.disablePublishSidebar;

  return {
    onChange: function onChange(isEnabled) {
      return isEnabled ? enablePublishSidebar() : disablePublishSidebar();
    }
  };
}), // In < medium viewports we override this option and always show the publish sidebar.
// See the edit-post's header component for the specific logic.
Object(external_this_wp_viewport_["ifViewportMatches"])('medium'))(base));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/enable-feature.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/* harmony default export */ var enable_feature = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var feature = _ref.feature;
  return {
    isChecked: select('core/edit-post').isFeatureActive(feature)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref2) {
  var feature = _ref2.feature;

  var _dispatch = dispatch('core/edit-post'),
      toggleFeature = _dispatch.toggleFeature;

  return {
    onChange: function onChange() {
      toggleFeature(feature);
    }
  };
}))(base));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/options/index.js






// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/meta-boxes-section.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function MetaBoxesSection(_ref) {
  var areCustomFieldsRegistered = _ref.areCustomFieldsRegistered,
      metaBoxes = _ref.metaBoxes,
      sectionProps = Object(objectWithoutProperties["a" /* default */])(_ref, ["areCustomFieldsRegistered", "metaBoxes"]);

  // The 'Custom Fields' meta box is a special case that we handle separately.
  var thirdPartyMetaBoxes = Object(external_this_lodash_["filter"])(metaBoxes, function (_ref2) {
    var id = _ref2.id;
    return id !== 'postcustom';
  });

  if (!areCustomFieldsRegistered && thirdPartyMetaBoxes.length === 0) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(section, sectionProps, areCustomFieldsRegistered && Object(external_this_wp_element_["createElement"])(enable_custom_fields, {
    label: Object(external_this_wp_i18n_["__"])('Custom fields')
  }), Object(external_this_lodash_["map"])(thirdPartyMetaBoxes, function (_ref3) {
    var id = _ref3.id,
        title = _ref3.title;
    return Object(external_this_wp_element_["createElement"])(enable_panel, {
      key: id,
      label: title,
      panelName: "meta-box-".concat(id)
    });
  }));
}
/* harmony default export */ var meta_boxes_section = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditorSettings = _select.getEditorSettings;

  var _select2 = select('core/edit-post'),
      getAllMetaBoxes = _select2.getAllMetaBoxes;

  return {
    // This setting should not live in the block editor's store.
    areCustomFieldsRegistered: getEditorSettings().enableCustomFields !== undefined,
    metaBoxes: getAllMetaBoxes()
  };
})(MetaBoxesSection));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/options-modal/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




var options_modal_MODAL_NAME = 'edit-post/options';
function OptionsModal(_ref) {
  var isModalActive = _ref.isModalActive,
      isViewable = _ref.isViewable,
      closeModal = _ref.closeModal;

  if (!isModalActive) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Modal"], {
    className: "edit-post-options-modal",
    title: Object(external_this_wp_i18n_["__"])('Options'),
    closeLabel: Object(external_this_wp_i18n_["__"])('Close'),
    onRequestClose: closeModal
  }, Object(external_this_wp_element_["createElement"])(section, {
    title: Object(external_this_wp_i18n_["__"])('General')
  }, Object(external_this_wp_element_["createElement"])(enable_publish_sidebar, {
    label: Object(external_this_wp_i18n_["__"])('Pre-publish checks')
  }), Object(external_this_wp_element_["createElement"])(enable_feature, {
    feature: "showInserterHelpPanel",
    label: Object(external_this_wp_i18n_["__"])('Inserter help panel')
  })), Object(external_this_wp_element_["createElement"])(section, {
    title: Object(external_this_wp_i18n_["__"])('Document panels')
  }, Object(external_this_wp_element_["createElement"])(enable_plugin_document_setting_panel.Slot, null), isViewable && Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Permalink'),
    panelName: "post-link"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTaxonomies"], {
    taxonomyWrapper: function taxonomyWrapper(content, taxonomy) {
      return Object(external_this_wp_element_["createElement"])(enable_panel, {
        label: Object(external_this_lodash_["get"])(taxonomy, ['labels', 'menu_name']),
        panelName: "taxonomy-panel-".concat(taxonomy.slug)
      });
    }
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFeaturedImageCheck"], null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Featured image'),
    panelName: "featured-image"
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostExcerptCheck"], null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Excerpt'),
    panelName: "post-excerpt"
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: ['comments', 'trackbacks']
  }, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Discussion'),
    panelName: "discussion-panel"
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageAttributesCheck"], null, Object(external_this_wp_element_["createElement"])(enable_panel, {
    label: Object(external_this_wp_i18n_["__"])('Page attributes'),
    panelName: "page-attributes"
  }))), Object(external_this_wp_element_["createElement"])(meta_boxes_section, {
    title: Object(external_this_wp_i18n_["__"])('Advanced panels')
  }));
}
/* harmony default export */ var options_modal = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  var postType = getPostType(getEditedPostAttribute('type'));
  return {
    isModalActive: select('core/edit-post').isModalActive(options_modal_MODAL_NAME),
    isViewable: Object(external_this_lodash_["get"])(postType, ['viewable'], false)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    closeModal: function closeModal() {
      return dispatch('core/edit-post').closeModal();
    }
  };
}))(OptionsModal));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/fullscreen-mode/index.js






/**
 * WordPress dependencies
 */


var fullscreen_mode_FullscreenMode =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(FullscreenMode, _Component);

  function FullscreenMode() {
    Object(classCallCheck["a" /* default */])(this, FullscreenMode);

    return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(FullscreenMode).apply(this, arguments));
  }

  Object(createClass["a" /* default */])(FullscreenMode, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.isSticky = false;
      this.sync(); // `is-fullscreen-mode` is set in PHP as a body class by Gutenberg, and this causes
      // `sticky-menu` to be applied by WordPress and prevents the admin menu being scrolled
      // even if `is-fullscreen-mode` is then removed. Let's remove `sticky-menu` here as
      // a consequence of the FullscreenMode setup

      if (document.body.classList.contains('sticky-menu')) {
        this.isSticky = true;
        document.body.classList.remove('sticky-menu');
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      if (this.isSticky) {
        document.body.classList.add('sticky-menu');
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.isActive !== prevProps.isActive) {
        this.sync();
      }
    }
  }, {
    key: "sync",
    value: function sync() {
      var isActive = this.props.isActive;

      if (isActive) {
        document.body.classList.add('is-fullscreen-mode');
      } else {
        document.body.classList.remove('is-fullscreen-mode');
      }
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return FullscreenMode;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var fullscreen_mode = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isActive: select('core/edit-post').isFeatureActive('fullscreenMode')
  };
})(fullscreen_mode_FullscreenMode));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/browser-url/index.js






/**
 * WordPress dependencies
 */



/**
 * Returns the Post's Edit URL.
 *
 * @param {number} postId Post ID.
 *
 * @return {string} Post edit URL.
 */

function getPostEditURL(postId) {
  return Object(external_this_wp_url_["addQueryArgs"])('post.php', {
    post: postId,
    action: 'edit'
  });
}
/**
 * Returns the Post's Trashed URL.
 *
 * @param {number} postId    Post ID.
 * @param {string} postType Post Type.
 *
 * @return {string} Post trashed URL.
 */

function getPostTrashedURL(postId, postType) {
  return Object(external_this_wp_url_["addQueryArgs"])('edit.php', {
    trashed: 1,
    post_type: postType,
    ids: postId
  });
}
var browser_url_BrowserURL =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(BrowserURL, _Component);

  function BrowserURL() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, BrowserURL);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(BrowserURL).apply(this, arguments));
    _this.state = {
      historyId: null
    };
    return _this;
  }

  Object(createClass["a" /* default */])(BrowserURL, [{
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this$props = this.props,
          postId = _this$props.postId,
          postStatus = _this$props.postStatus,
          postType = _this$props.postType,
          isSavingPost = _this$props.isSavingPost;
      var historyId = this.state.historyId; // Posts are still dirty while saving so wait for saving to finish
      // to avoid the unsaved changes warning when trashing posts.

      if (postStatus === 'trash' && !isSavingPost) {
        this.setTrashURL(postId, postType);
        return;
      }

      if ((postId !== prevProps.postId || postId !== historyId) && postStatus !== 'auto-draft') {
        this.setBrowserURL(postId);
      }
    }
    /**
     * Navigates the browser to the post trashed URL to show a notice about the trashed post.
     *
     * @param {number} postId    Post ID.
     * @param {string} postType  Post Type.
     */

  }, {
    key: "setTrashURL",
    value: function setTrashURL(postId, postType) {
      window.location.href = getPostTrashedURL(postId, postType);
    }
    /**
     * Replaces the browser URL with a post editor link for the given post ID.
     *
     * Note it is important that, since this function may be called when the
     * editor first loads, the result generated `getPostEditURL` matches that
     * produced by the server. Otherwise, the URL will change unexpectedly.
     *
     * @param {number} postId Post ID for which to generate post editor URL.
     */

  }, {
    key: "setBrowserURL",
    value: function setBrowserURL(postId) {
      window.history.replaceState({
        id: postId
      }, 'Post ' + postId, getPostEditURL(postId));
      this.setState(function () {
        return {
          historyId: postId
        };
      });
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return BrowserURL;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var browser_url = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getCurrentPost = _select.getCurrentPost,
      isSavingPost = _select.isSavingPost;

  var _getCurrentPost = getCurrentPost(),
      id = _getCurrentPost.id,
      status = _getCurrentPost.status,
      type = _getCurrentPost.type;

  return {
    postId: id,
    postStatus: status,
    postType: type,
    isSavingPost: isSavingPost()
  };
})(browser_url_BrowserURL));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/fullscreen-mode-close/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






var wordPressLogo = Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["Path"], {
  d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z"
}));

function FullscreenModeClose() {
  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    var _select = select('core/editor'),
        getCurrentPostType = _select.getCurrentPostType;

    var _select2 = select('core/edit-post'),
        isFeatureActive = _select2.isFeatureActive;

    var _select3 = select('core'),
        getPostType = _select3.getPostType;

    return {
      isActive: isFeatureActive('fullscreenMode'),
      postType: getPostType(getCurrentPostType())
    };
  }, []),
      isActive = _useSelect.isActive,
      postType = _useSelect.postType;

  if (!isActive || !postType) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    className: "edit-post-fullscreen-mode-close",
    icon: wordPressLogo,
    iconSize: 36,
    href: Object(external_this_wp_url_["addQueryArgs"])('edit.php', {
      post_type: postType.slug
    }),
    label: Object(external_this_lodash_["get"])(postType, ['labels', 'view_items'], Object(external_this_wp_i18n_["__"])('Back'))
  });
}

/* harmony default export */ var fullscreen_mode_close = (FullscreenModeClose);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/header-toolbar/index.js


/**
 * WordPress dependencies
 */






function HeaderToolbar() {
  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
      // This setting (richEditingEnabled) should not live in the block editor's setting.
      showInserter: select('core/edit-post').getEditorMode() === 'visual' && select('core/editor').getEditorSettings().richEditingEnabled,
      isTextModeEnabled: select('core/edit-post').getEditorMode() === 'text'
    };
  }, []),
      hasFixedToolbar = _useSelect.hasFixedToolbar,
      showInserter = _useSelect.showInserter,
      isTextModeEnabled = _useSelect.isTextModeEnabled;

  var isLargeViewport = Object(external_this_wp_compose_["useViewportMatch"])('medium');
  var toolbarAriaLabel = hasFixedToolbar ?
  /* translators: accessibility text for the editor toolbar when Top Toolbar is on */
  Object(external_this_wp_i18n_["__"])('Document and block tools') :
  /* translators: accessibility text for the editor toolbar when Top Toolbar is off */
  Object(external_this_wp_i18n_["__"])('Document tools');
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["NavigableToolbar"], {
    className: "edit-post-header-toolbar",
    "aria-label": toolbarAriaLabel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["Inserter"], {
    disabled: !showInserter,
    position: "bottom right",
    showInserterHelpPanel: true
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorHistoryUndo"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorHistoryRedo"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["TableOfContents"], {
    hasOutlineItemsDisabled: isTextModeEnabled
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockNavigationDropdown"], {
    isDisabled: isTextModeEnabled
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["ToolSelector"], null), (hasFixedToolbar || !isLargeViewport) && Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-header-toolbar__block-toolbar"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockToolbar"], {
    hideDragHandle: true
  })));
}

/* harmony default export */ var header_toolbar = (HeaderToolbar);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/more-horizontal.js
var more_horizontal = __webpack_require__(284);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/mode-switcher/index.js



function mode_switcher_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function mode_switcher_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { mode_switcher_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { mode_switcher_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * WordPress dependencies
 */



/**
 * Set of available mode options.
 *
 * @type {Array}
 */

var MODES = [{
  value: 'visual',
  label: Object(external_this_wp_i18n_["__"])('Visual editor')
}, {
  value: 'text',
  label: Object(external_this_wp_i18n_["__"])('Code editor')
}];

function ModeSwitcher() {
  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      shortcut: select('core/keyboard-shortcuts').getShortcutRepresentation('core/edit-post/toggle-mode'),
      isRichEditingEnabled: select('core/editor').getEditorSettings().richEditingEnabled,
      isCodeEditingEnabled: select('core/editor').getEditorSettings().codeEditingEnabled,
      mode: select('core/edit-post').getEditorMode()
    };
  }, []),
      shortcut = _useSelect.shortcut,
      isRichEditingEnabled = _useSelect.isRichEditingEnabled,
      isCodeEditingEnabled = _useSelect.isCodeEditingEnabled,
      mode = _useSelect.mode;

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/edit-post'),
      switchEditorMode = _useDispatch.switchEditorMode;

  var choices = MODES.map(function (choice) {
    if (choice.value !== mode) {
      return mode_switcher_objectSpread({}, choice, {
        shortcut: shortcut
      });
    }

    return choice;
  });

  if (!isRichEditingEnabled || !isCodeEditingEnabled) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
    label: Object(external_this_wp_i18n_["__"])('Editor')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItemsChoice"], {
    choices: choices,
    value: mode,
    onSelect: switchEditorMode
  }));
}

/* harmony default export */ var mode_switcher = (ModeSwitcher);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugins-more-menu-group/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




var plugins_more_menu_group_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginsMoreMenuGroup'),
    PluginsMoreMenuGroup = plugins_more_menu_group_createSlotFill.Fill,
    plugins_more_menu_group_Slot = plugins_more_menu_group_createSlotFill.Slot;

PluginsMoreMenuGroup.Slot = function (_ref) {
  var fillProps = _ref.fillProps;
  return Object(external_this_wp_element_["createElement"])(plugins_more_menu_group_Slot, {
    fillProps: fillProps
  }, function (fills) {
    return !Object(external_this_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
      label: Object(external_this_wp_i18n_["__"])('Plugins')
    }, fills);
  });
};

/* harmony default export */ var plugins_more_menu_group = (PluginsMoreMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/options-menu-item/index.js


/**
 * WordPress dependencies
 */



function OptionsMenuItem(_ref) {
  var openModal = _ref.openModal;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    onClick: function onClick() {
      openModal('edit-post/options');
    }
  }, Object(external_this_wp_i18n_["__"])('Options'));
}
/* harmony default export */ var options_menu_item = (Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openModal = _dispatch.openModal;

  return {
    openModal: openModal
  };
})(OptionsMenuItem));

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js
var check = __webpack_require__(193);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/feature-toggle/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







function FeatureToggle(_ref) {
  var onToggle = _ref.onToggle,
      isActive = _ref.isActive,
      label = _ref.label,
      info = _ref.info,
      messageActivated = _ref.messageActivated,
      messageDeactivated = _ref.messageDeactivated,
      speak = _ref.speak;

  var speakMessage = function speakMessage() {
    if (isActive) {
      speak(messageDeactivated || Object(external_this_wp_i18n_["__"])('Feature deactivated'));
    } else {
      speak(messageActivated || Object(external_this_wp_i18n_["__"])('Feature activated'));
    }
  };

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
    icon: isActive && check["a" /* default */],
    isSelected: isActive,
    onClick: Object(external_this_lodash_["flow"])(onToggle, speakMessage),
    role: "menuitemcheckbox",
    info: info
  }, label);
}

/* harmony default export */ var feature_toggle = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var feature = _ref2.feature;
  return {
    isActive: select('core/edit-post').isFeatureActive(feature)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  return {
    onToggle: function onToggle() {
      dispatch('core/edit-post').toggleFeature(ownProps.feature);
    }
  };
}), external_this_wp_components_["withSpokenMessages"]])(FeatureToggle));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/writing-menu/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function WritingMenu() {
  var isLargeViewport = Object(external_this_wp_compose_["useViewportMatch"])('medium');

  if (!isLargeViewport) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], {
    label: Object(external_this_wp_i18n_["_x"])('View', 'noun')
  }, Object(external_this_wp_element_["createElement"])(feature_toggle, {
    feature: "fixedToolbar",
    label: Object(external_this_wp_i18n_["__"])('Top toolbar'),
    info: Object(external_this_wp_i18n_["__"])('Access all block and document tools in a single place'),
    messageActivated: Object(external_this_wp_i18n_["__"])('Top toolbar activated'),
    messageDeactivated: Object(external_this_wp_i18n_["__"])('Top toolbar deactivated')
  }), Object(external_this_wp_element_["createElement"])(feature_toggle, {
    feature: "focusMode",
    label: Object(external_this_wp_i18n_["__"])('Spotlight mode'),
    info: Object(external_this_wp_i18n_["__"])('Focus on one block at a time'),
    messageActivated: Object(external_this_wp_i18n_["__"])('Spotlight mode activated'),
    messageDeactivated: Object(external_this_wp_i18n_["__"])('Spotlight mode deactivated')
  }), Object(external_this_wp_element_["createElement"])(feature_toggle, {
    feature: "fullscreenMode",
    label: Object(external_this_wp_i18n_["__"])('Fullscreen mode'),
    info: Object(external_this_wp_i18n_["__"])('Work without distraction'),
    messageActivated: Object(external_this_wp_i18n_["__"])('Fullscreen mode activated'),
    messageDeactivated: Object(external_this_wp_i18n_["__"])('Fullscreen mode deactivated')
  }));
}

/* harmony default export */ var writing_menu = (WritingMenu);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/more-menu/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */






var POPOVER_PROPS = {
  className: 'edit-post-more-menu__content',
  position: 'bottom left'
};
var TOGGLE_PROPS = {
  tooltipPosition: 'bottom'
};

var more_menu_MoreMenu = function MoreMenu() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["DropdownMenu"], {
    className: "edit-post-more-menu",
    icon: more_horizontal["a" /* default */],
    label: Object(external_this_wp_i18n_["__"])('More tools & options'),
    popoverProps: POPOVER_PROPS,
    toggleProps: TOGGLE_PROPS
  }, function (_ref) {
    var onClose = _ref.onClose;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(writing_menu, null), Object(external_this_wp_element_["createElement"])(mode_switcher, null), Object(external_this_wp_element_["createElement"])(plugins_more_menu_group.Slot, {
      fillProps: {
        onClose: onClose
      }
    }), Object(external_this_wp_element_["createElement"])(tools_more_menu_group.Slot, {
      fillProps: {
        onClose: onClose
      }
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuGroup"], null, Object(external_this_wp_element_["createElement"])(options_menu_item, null)));
  });
};

/* harmony default export */ var more_menu = (more_menu_MoreMenu);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/pinned-plugins/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



var pinned_plugins_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PinnedPlugins'),
    PinnedPlugins = pinned_plugins_createSlotFill.Fill,
    pinned_plugins_Slot = pinned_plugins_createSlotFill.Slot;

PinnedPlugins.Slot = function (props) {
  return Object(external_this_wp_element_["createElement"])(pinned_plugins_Slot, props, function (fills) {
    return !Object(external_this_lodash_["isEmpty"])(fills) && Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-pinned-plugins"
    }, fills);
  });
};

/* harmony default export */ var pinned_plugins = (PinnedPlugins);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/post-publish-button-or-toggle.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function PostPublishButtonOrToggle(_ref) {
  var forceIsDirty = _ref.forceIsDirty,
      forceIsSaving = _ref.forceIsSaving,
      hasPublishAction = _ref.hasPublishAction,
      isBeingScheduled = _ref.isBeingScheduled,
      isPending = _ref.isPending,
      isPublished = _ref.isPublished,
      isPublishSidebarEnabled = _ref.isPublishSidebarEnabled,
      isPublishSidebarOpened = _ref.isPublishSidebarOpened,
      isScheduled = _ref.isScheduled,
      togglePublishSidebar = _ref.togglePublishSidebar;
  var IS_TOGGLE = 'toggle';
  var IS_BUTTON = 'button';
  var isSmallerThanMediumViewport = Object(external_this_wp_compose_["useViewportMatch"])('medium', '<');
  var component;
  /**
   * Conditions to show a BUTTON (publish directly) or a TOGGLE (open publish sidebar):
   *
   * 1) We want to show a BUTTON when the post status is at the _final stage_
   * for a particular role (see https://wordpress.org/support/article/post-status/):
   *
   * - is published
   * - is scheduled to be published
   * - is pending and can't be published (but only for viewports >= medium).
   * 	 Originally, we considered showing a button for pending posts that couldn't be published
   * 	 (for example, for an author with the contributor role). Some languages can have
   * 	 long translations for "Submit for review", so given the lack of UI real estate available
   * 	 we decided to take into account the viewport in that case.
   *  	 See: https://github.com/WordPress/gutenberg/issues/10475
   *
   * 2) Then, in small viewports, we'll show a TOGGLE.
   *
   * 3) Finally, we'll use the publish sidebar status to decide:
   *
   * - if it is enabled, we show a TOGGLE
   * - if it is disabled, we show a BUTTON
   */

  if (isPublished || isScheduled && isBeingScheduled || isPending && !hasPublishAction && !isSmallerThanMediumViewport) {
    component = IS_BUTTON;
  } else if (isSmallerThanMediumViewport) {
    component = IS_TOGGLE;
  } else if (isPublishSidebarEnabled) {
    component = IS_TOGGLE;
  } else {
    component = IS_BUTTON;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPublishButton"], {
    forceIsDirty: forceIsDirty,
    forceIsSaving: forceIsSaving,
    isOpen: isPublishSidebarOpened,
    isToggle: component === IS_TOGGLE,
    onToggle: togglePublishSidebar
  });
}
/* harmony default export */ var post_publish_button_or_toggle = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    hasPublishAction: Object(external_this_lodash_["get"])(select('core/editor').getCurrentPost(), ['_links', 'wp:action-publish'], false),
    isBeingScheduled: select('core/editor').isEditedPostBeingScheduled(),
    isPending: select('core/editor').isCurrentPostPending(),
    isPublished: select('core/editor').isCurrentPostPublished(),
    isPublishSidebarEnabled: select('core/editor').isPublishSidebarEnabled(),
    isPublishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
    isScheduled: select('core/editor').isCurrentPostScheduled()
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      togglePublishSidebar = _dispatch.togglePublishSidebar;

  return {
    togglePublishSidebar: togglePublishSidebar
  };
}))(PostPublishButtonOrToggle));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */







function Header() {
  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      shortcut: select('core/keyboard-shortcuts').getShortcutRepresentation('core/edit-post/toggle-sidebar'),
      hasActiveMetaboxes: select('core/edit-post').hasMetaBoxes(),
      isEditorSidebarOpened: select('core/edit-post').isEditorSidebarOpened(),
      isPublishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
      isSaving: select('core/edit-post').isSavingMetaBoxes(),
      getBlockSelectionStart: select('core/block-editor').getBlockSelectionStart
    };
  }, []),
      shortcut = _useSelect.shortcut,
      hasActiveMetaboxes = _useSelect.hasActiveMetaboxes,
      isEditorSidebarOpened = _useSelect.isEditorSidebarOpened,
      isPublishSidebarOpened = _useSelect.isPublishSidebarOpened,
      isSaving = _useSelect.isSaving,
      getBlockSelectionStart = _useSelect.getBlockSelectionStart;

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/edit-post'),
      openGeneralSidebar = _useDispatch.openGeneralSidebar,
      closeGeneralSidebar = _useDispatch.closeGeneralSidebar;

  var toggleGeneralSidebar = isEditorSidebarOpened ? closeGeneralSidebar : function () {
    return openGeneralSidebar(getBlockSelectionStart() ? 'edit-post/block' : 'edit-post/document');
  };
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-header"
  }, Object(external_this_wp_element_["createElement"])(fullscreen_mode_close, null), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-header__toolbar"
  }, Object(external_this_wp_element_["createElement"])(header_toolbar, null)), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-header__settings"
  }, !isPublishSidebarOpened && // This button isn't completely hidden by the publish sidebar.
  // We can't hide the whole toolbar when the publish sidebar is open because
  // we want to prevent mounting/unmounting the PostPublishButtonOrToggle DOM node.
  // We track that DOM node to return focus to the PostPublishButtonOrToggle
  // when the publish sidebar has been closed.
  Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSavedState"], {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPreviewButton"], {
    forceIsAutosaveable: hasActiveMetaboxes,
    forcePreviewLink: isSaving ? null : undefined
  }), Object(external_this_wp_element_["createElement"])(post_publish_button_or_toggle, {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    icon: library_cog,
    label: Object(external_this_wp_i18n_["__"])('Settings'),
    onClick: toggleGeneralSidebar,
    isPressed: isEditorSidebarOpened,
    "aria-expanded": isEditorSidebarOpened,
    shortcut: shortcut
  }), Object(external_this_wp_element_["createElement"])(pinned_plugins.Slot, null), Object(external_this_wp_element_["createElement"])(more_menu, null)));
}

/* harmony default export */ var header = (Header);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





var sidebar_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('Sidebar'),
    sidebar_Fill = sidebar_createSlotFill.Fill,
    sidebar_Slot = sidebar_createSlotFill.Slot;
/**
 * Renders a sidebar with its content.
 *
 * @return {Object} The rendered sidebar.
 */


function Sidebar(_ref) {
  var children = _ref.children,
      className = _ref.className;
  return Object(external_this_wp_element_["createElement"])("div", {
    className: classnames_default()('edit-post-sidebar', className)
  }, children);
}

Sidebar = Object(external_this_wp_components_["withFocusReturn"])({
  onFocusReturn: function onFocusReturn() {
    var button = document.querySelector('.edit-post-header__settings [aria-label="Settings"]');

    if (button) {
      button.focus();
      return false;
    }
  }
})(Sidebar);

function AnimatedSidebarFill(props) {
  return Object(external_this_wp_element_["createElement"])(sidebar_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Animate"], {
    type: "slide-in",
    options: {
      origin: 'left'
    }
  }, function () {
    return Object(external_this_wp_element_["createElement"])(Sidebar, props);
  }));
}

var WrappedSidebar = Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var name = _ref2.name;
  return {
    isActive: select('core/edit-post').getActiveGeneralSidebarName() === name
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref3) {
  var isActive = _ref3.isActive;
  return isActive;
}))(AnimatedSidebarFill);
WrappedSidebar.Slot = sidebar_Slot;
/* harmony default export */ var sidebar = (WrappedSidebar);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/sidebar-header/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






var sidebar_header_SidebarHeader = function SidebarHeader(_ref) {
  var children = _ref.children,
      className = _ref.className,
      closeLabel = _ref.closeLabel;

  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      shortcut: select('core/keyboard-shortcuts').getShortcutRepresentation('core/edit-post/toggle-sidebar'),
      title: select('core/editor').getEditedPostAttribute('title')
    };
  }, []),
      shortcut = _useSelect.shortcut,
      title = _useSelect.title;

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/edit-post'),
      closeGeneralSidebar = _useDispatch.closeGeneralSidebar; // The `tabIndex` serves the purpose of normalizing browser behavior of
  // button clicks and focus. Notably, without making the header focusable, a
  // Button click would not trigger a focus event in macOS Firefox. Thus, when
  // the sidebar is unmounted, the corresponding "focus return" behavior to
  // shift focus back to the heading toolbar would not be run.
  //
  // See: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/button#Clicking_and_focus


  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", {
    className: "components-panel__header edit-post-sidebar-header__small"
  }, Object(external_this_wp_element_["createElement"])("span", {
    className: "edit-post-sidebar-header__title"
  }, title || Object(external_this_wp_i18n_["__"])('(no title)')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    onClick: closeGeneralSidebar,
    icon: library_close["a" /* default */],
    label: closeLabel
  })), Object(external_this_wp_element_["createElement"])("div", {
    className: classnames_default()('components-panel__header edit-post-sidebar-header', className),
    tabIndex: -1
  }, children, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    onClick: closeGeneralSidebar,
    icon: library_close["a" /* default */],
    label: closeLabel,
    shortcut: shortcut
  })));
};

/* harmony default export */ var sidebar_header = (sidebar_header_SidebarHeader);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-header/index.js



/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



var settings_header_SettingsHeader = function SettingsHeader(_ref) {
  var openDocumentSettings = _ref.openDocumentSettings,
      openBlockSettings = _ref.openBlockSettings,
      sidebarName = _ref.sidebarName;

  var blockLabel = Object(external_this_wp_i18n_["__"])('Block');

  var _ref2 = sidebarName === 'edit-post/document' ? // translators: ARIA label for the Document sidebar tab, selected.
  [Object(external_this_wp_i18n_["__"])('Document (selected)'), 'is-active'] : // translators: ARIA label for the Document sidebar tab, not selected.
  [Object(external_this_wp_i18n_["__"])('Document'), ''],
      _ref3 = Object(slicedToArray["a" /* default */])(_ref2, 2),
      documentAriaLabel = _ref3[0],
      documentActiveClass = _ref3[1];

  var _ref4 = sidebarName === 'edit-post/block' ? // translators: ARIA label for the Settings Sidebar tab, selected.
  [Object(external_this_wp_i18n_["__"])('Block (selected)'), 'is-active'] : // translators: ARIA label for the Settings Sidebar tab, not selected.
  [Object(external_this_wp_i18n_["__"])('Block'), ''],
      _ref5 = Object(slicedToArray["a" /* default */])(_ref4, 2),
      blockAriaLabel = _ref5[0],
      blockActiveClass = _ref5[1];

  return Object(external_this_wp_element_["createElement"])(sidebar_header, {
    className: "edit-post-sidebar__panel-tabs",
    closeLabel: Object(external_this_wp_i18n_["__"])('Close settings')
  }, Object(external_this_wp_element_["createElement"])("ul", null, Object(external_this_wp_element_["createElement"])("li", null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    onClick: openDocumentSettings,
    className: "edit-post-sidebar__panel-tab ".concat(documentActiveClass),
    "aria-label": documentAriaLabel,
    "data-label": Object(external_this_wp_i18n_["__"])('Document')
  }, Object(external_this_wp_i18n_["__"])('Document'))), Object(external_this_wp_element_["createElement"])("li", null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    onClick: openBlockSettings,
    className: "edit-post-sidebar__panel-tab ".concat(blockActiveClass),
    "aria-label": blockAriaLabel,
    "data-label": blockLabel
  }, blockLabel))));
};

/* harmony default export */ var settings_header = (Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      openGeneralSidebar = _dispatch.openGeneralSidebar;

  return {
    openDocumentSettings: function openDocumentSettings() {
      openGeneralSidebar('edit-post/document');
    },
    openBlockSettings: function openBlockSettings() {
      openGeneralSidebar('edit-post/block');
    }
  };
})(settings_header_SettingsHeader));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-visibility/index.js


/**
 * WordPress dependencies
 */



function PostVisibility() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostVisibilityCheck"], {
    render: function render(_ref) {
      var canEdit = _ref.canEdit;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], {
        className: "edit-post-post-visibility"
      }, Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_i18n_["__"])('Visibility')), !canEdit && Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostVisibilityLabel"], null)), canEdit && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dropdown"], {
        position: "bottom left",
        contentClassName: "edit-post-post-visibility__dialog",
        renderToggle: function renderToggle(_ref2) {
          var isOpen = _ref2.isOpen,
              onToggle = _ref2.onToggle;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
            "aria-expanded": isOpen,
            className: "edit-post-post-visibility__toggle",
            onClick: onToggle,
            isLink: true
          }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostVisibilityLabel"], null));
        },
        renderContent: function renderContent() {
          return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostVisibility"], null);
        }
      }));
    }
  });
}
/* harmony default export */ var post_visibility = (PostVisibility);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-trash/index.js


/**
 * WordPress dependencies
 */


function PostTrash() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTrashCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTrash"], null)));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-schedule/index.js


/**
 * WordPress dependencies
 */



function PostSchedule() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostScheduleCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], {
    className: "edit-post-post-schedule"
  }, Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_i18n_["__"])('Publish')), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dropdown"], {
    position: "bottom left",
    contentClassName: "edit-post-post-schedule__dialog",
    renderToggle: function renderToggle(_ref) {
      var onToggle = _ref.onToggle,
          isOpen = _ref.isOpen;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: "edit-post-post-schedule__toggle",
        onClick: onToggle,
        "aria-expanded": isOpen,
        isLink: true
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostScheduleLabel"], null)));
    },
    renderContent: function renderContent() {
      return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSchedule"], null);
    }
  })));
}
/* harmony default export */ var post_schedule = (PostSchedule);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-sticky/index.js


/**
 * WordPress dependencies
 */


function PostSticky() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostStickyCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSticky"], null)));
}
/* harmony default export */ var post_sticky = (PostSticky);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-author/index.js


/**
 * WordPress dependencies
 */


function PostAuthor() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostAuthorCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostAuthor"], null)));
}
/* harmony default export */ var post_author = (PostAuthor);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-slug/index.js


/**
 * WordPress dependencies
 */


function PostSlug() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSlugCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostSlug"], null)));
}
/* harmony default export */ var post_slug = (PostSlug);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-format/index.js


/**
 * WordPress dependencies
 */


function PostFormat() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFormatCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFormat"], null)));
}
/* harmony default export */ var post_format = (PostFormat);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-pending-status/index.js


/**
 * WordPress dependencies
 */


function PostPendingStatus() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPendingStatusCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPendingStatus"], null)));
}
/* harmony default export */ var post_pending_status = (PostPendingStatus);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-status-info/index.js


/**
 * Defines as extensibility slot for the Status & visibility panel.
 */

/**
 * WordPress dependencies
 */


var plugin_post_status_info_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginPostStatusInfo'),
    plugin_post_status_info_Fill = plugin_post_status_info_createSlotFill.Fill,
    plugin_post_status_info_Slot = plugin_post_status_info_createSlotFill.Slot;
/**
 * Renders a row in the Status & visibility panel of the Document sidebar.
 * It should be noted that this is named and implemented around the function it serves
 * and not its location, which may change in future iterations.
 *
 * @param {Object} props Component properties.
 * @param {string} [props.className] An optional class name added to the row.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
 *
 * function MyPluginPostStatusInfo() {
 * 	return wp.element.createElement(
 * 		PluginPostStatusInfo,
 * 		{
 * 			className: 'my-plugin-post-status-info',
 * 		},
 * 		__( 'My post status info' )
 * 	)
 * }
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { __ } = wp.i18n;
 * const { PluginPostStatusInfo } = wp.editPost;
 *
 * const MyPluginPostStatusInfo = () => (
 * 	<PluginPostStatusInfo
 * 		className="my-plugin-post-status-info"
 * 	>
 * 		{ __( 'My post status info' ) }
 * 	</PluginPostStatusInfo>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */




var plugin_post_status_info_PluginPostStatusInfo = function PluginPostStatusInfo(_ref) {
  var children = _ref.children,
      className = _ref.className;
  return Object(external_this_wp_element_["createElement"])(plugin_post_status_info_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], {
    className: className
  }, children));
};

plugin_post_status_info_PluginPostStatusInfo.Slot = plugin_post_status_info_Slot;
/* harmony default export */ var plugin_post_status_info = (plugin_post_status_info_PluginPostStatusInfo);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-status/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */










/**
 * Module Constants
 */

var PANEL_NAME = 'post-status';

function PostStatus(_ref) {
  var isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: "edit-post-post-status",
    title: Object(external_this_wp_i18n_["__"])('Status & visibility'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(plugin_post_status_info.Slot, null, function (fills) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(post_visibility, null), Object(external_this_wp_element_["createElement"])(post_schedule, null), Object(external_this_wp_element_["createElement"])(post_format, null), Object(external_this_wp_element_["createElement"])(post_sticky, null), Object(external_this_wp_element_["createElement"])(post_pending_status, null), Object(external_this_wp_element_["createElement"])(post_slug, null), Object(external_this_wp_element_["createElement"])(post_author, null), fills, Object(external_this_wp_element_["createElement"])(PostTrash, null));
  }));
}

/* harmony default export */ var post_status = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  // We use isEditorPanelRemoved to hide the panel if it was programatically removed. We do
  // not use isEditorPanelEnabled since this panel should not be disabled through the UI.
  var _select = select('core/edit-post'),
      isEditorPanelRemoved = _select.isEditorPanelRemoved,
      isEditorPanelOpened = _select.isEditorPanelOpened;

  return {
    isRemoved: isEditorPanelRemoved(PANEL_NAME),
    isOpened: isEditorPanelOpened(PANEL_NAME)
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isRemoved = _ref2.isRemoved;
  return !isRemoved;
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleEditorPanelOpened(PANEL_NAME);
    }
  };
})])(PostStatus));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/last-revision/index.js


/**
 * WordPress dependencies
 */



function LastRevision() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLastRevisionCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: "edit-post-last-revision__panel"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLastRevision"], null)));
}

/* harmony default export */ var last_revision = (LastRevision);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





function TaxonomyPanel(_ref) {
  var isEnabled = _ref.isEnabled,
      taxonomy = _ref.taxonomy,
      isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel,
      children = _ref.children;

  if (!isEnabled) {
    return null;
  }

  var taxonomyMenuName = Object(external_this_lodash_["get"])(taxonomy, ['labels', 'menu_name']);

  if (!taxonomyMenuName) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: taxonomyMenuName,
    opened: isOpened,
    onToggle: onTogglePanel
  }, children);
}

/* harmony default export */ var taxonomy_panel = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, ownProps) {
  var slug = Object(external_this_lodash_["get"])(ownProps.taxonomy, ['slug']);
  var panelName = slug ? "taxonomy-panel-".concat(slug) : '';
  return {
    panelName: panelName,
    isEnabled: slug ? select('core/edit-post').isEditorPanelEnabled(panelName) : false,
    isOpened: slug ? select('core/edit-post').isEditorPanelOpened(panelName) : false
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  return {
    onTogglePanel: function onTogglePanel() {
      dispatch('core/edit-post').toggleEditorPanelOpened(ownProps.panelName);
    }
  };
}))(TaxonomyPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function PostTaxonomies() {
  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTaxonomiesCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTaxonomies"], {
    taxonomyWrapper: function taxonomyWrapper(content, taxonomy) {
      return Object(external_this_wp_element_["createElement"])(taxonomy_panel, {
        taxonomy: taxonomy
      }, content);
    }
  }));
}

/* harmony default export */ var post_taxonomies = (PostTaxonomies);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/featured-image/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Module Constants
 */

var featured_image_PANEL_NAME = 'featured-image';

function FeaturedImage(_ref) {
  var isEnabled = _ref.isEnabled,
      isOpened = _ref.isOpened,
      postType = _ref.postType,
      onTogglePanel = _ref.onTogglePanel;

  if (!isEnabled) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFeaturedImageCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_lodash_["get"])(postType, ['labels', 'featured_image'], Object(external_this_wp_i18n_["__"])('Featured image')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostFeaturedImage"], null)));
}

var applyWithSelect = Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core'),
      getPostType = _select2.getPostType;

  var _select3 = select('core/edit-post'),
      isEditorPanelEnabled = _select3.isEditorPanelEnabled,
      isEditorPanelOpened = _select3.isEditorPanelOpened;

  return {
    postType: getPostType(getEditedPostAttribute('type')),
    isEnabled: isEditorPanelEnabled(featured_image_PANEL_NAME),
    isOpened: isEditorPanelOpened(featured_image_PANEL_NAME)
  };
});
var applyWithDispatch = Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      toggleEditorPanelOpened = _dispatch.toggleEditorPanelOpened;

  return {
    onTogglePanel: Object(external_this_lodash_["partial"])(toggleEditorPanelOpened, featured_image_PANEL_NAME)
  };
});
/* harmony default export */ var featured_image = (Object(external_this_wp_compose_["compose"])(applyWithSelect, applyWithDispatch)(FeaturedImage));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-excerpt/index.js


/**
 * WordPress dependencies
 */





/**
 * Module Constants
 */

var post_excerpt_PANEL_NAME = 'post-excerpt';

function PostExcerpt(_ref) {
  var isEnabled = _ref.isEnabled,
      isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel;

  if (!isEnabled) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostExcerptCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Excerpt'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostExcerpt"], null)));
}

/* harmony default export */ var post_excerpt = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isEnabled: select('core/edit-post').isEditorPanelEnabled(post_excerpt_PANEL_NAME),
    isOpened: select('core/edit-post').isEditorPanelOpened(post_excerpt_PANEL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleEditorPanelOpened(post_excerpt_PANEL_NAME);
    }
  };
})])(PostExcerpt));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-link/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Module Constants
 */

var post_link_PANEL_NAME = 'post-link';

function PostLink(_ref) {
  var isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel,
      isEditable = _ref.isEditable,
      postLink = _ref.postLink,
      permalinkParts = _ref.permalinkParts,
      editPermalink = _ref.editPermalink,
      forceEmptyField = _ref.forceEmptyField,
      setState = _ref.setState,
      postTitle = _ref.postTitle,
      postSlug = _ref.postSlug,
      postID = _ref.postID,
      postTypeLabel = _ref.postTypeLabel;
  var prefix = permalinkParts.prefix,
      suffix = permalinkParts.suffix;
  var prefixElement, postNameElement, suffixElement;
  var currentSlug = Object(external_this_wp_url_["safeDecodeURIComponent"])(postSlug) || Object(external_this_wp_editor_["cleanForSlug"])(postTitle) || postID;

  if (isEditable) {
    prefixElement = prefix && Object(external_this_wp_element_["createElement"])("span", {
      className: "edit-post-post-link__link-prefix"
    }, prefix);
    postNameElement = currentSlug && Object(external_this_wp_element_["createElement"])("span", {
      className: "edit-post-post-link__link-post-name"
    }, currentSlug);
    suffixElement = suffix && Object(external_this_wp_element_["createElement"])("span", {
      className: "edit-post-post-link__link-suffix"
    }, suffix);
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Permalink'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, isEditable && Object(external_this_wp_element_["createElement"])("div", {
    className: "editor-post-link"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
    label: Object(external_this_wp_i18n_["__"])('URL Slug'),
    value: forceEmptyField ? '' : currentSlug,
    onChange: function onChange(newValue) {
      editPermalink(newValue); // When we delete the field the permalink gets
      // reverted to the original value.
      // The forceEmptyField logic allows the user to have
      // the field temporarily empty while typing.

      if (!newValue) {
        if (!forceEmptyField) {
          setState({
            forceEmptyField: true
          });
        }

        return;
      }

      if (forceEmptyField) {
        setState({
          forceEmptyField: false
        });
      }
    },
    onBlur: function onBlur(event) {
      editPermalink(Object(external_this_wp_editor_["cleanForSlug"])(event.target.value));

      if (forceEmptyField) {
        setState({
          forceEmptyField: false
        });
      }
    }
  }), Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('The last part of the URL.'), ' ', Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
    href: "https://wordpress.org/support/article/writing-posts/#post-field-descriptions"
  }, Object(external_this_wp_i18n_["__"])('Read about permalinks')))), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-post-link__preview-label"
  }, postTypeLabel || Object(external_this_wp_i18n_["__"])('View post')), Object(external_this_wp_element_["createElement"])("div", {
    className: "edit-post-post-link__preview-link-container"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
    className: "edit-post-post-link__link",
    href: postLink,
    target: "_blank"
  }, isEditable ? Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, prefixElement, postNameElement, suffixElement) : postLink)));
}

/* harmony default export */ var post_link = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      isEditedPostNew = _select.isEditedPostNew,
      isPermalinkEditable = _select.isPermalinkEditable,
      getCurrentPost = _select.getCurrentPost,
      isCurrentPostPublished = _select.isCurrentPostPublished,
      getPermalinkParts = _select.getPermalinkParts,
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core/edit-post'),
      isEditorPanelEnabled = _select2.isEditorPanelEnabled,
      isEditorPanelOpened = _select2.isEditorPanelOpened;

  var _select3 = select('core'),
      getPostType = _select3.getPostType;

  var _getCurrentPost = getCurrentPost(),
      link = _getCurrentPost.link,
      id = _getCurrentPost.id;

  var postTypeName = getEditedPostAttribute('type');
  var postType = getPostType(postTypeName);
  return {
    isNew: isEditedPostNew(),
    postLink: link,
    isEditable: isPermalinkEditable(),
    isPublished: isCurrentPostPublished(),
    isOpened: isEditorPanelOpened(post_link_PANEL_NAME),
    permalinkParts: getPermalinkParts(),
    isEnabled: isEditorPanelEnabled(post_link_PANEL_NAME),
    isViewable: Object(external_this_lodash_["get"])(postType, ['viewable'], false),
    postTitle: getEditedPostAttribute('title'),
    postSlug: getEditedPostAttribute('slug'),
    postID: id,
    postTypeLabel: Object(external_this_lodash_["get"])(postType, ['labels', 'view_item'])
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isEnabled = _ref2.isEnabled,
      isNew = _ref2.isNew,
      postLink = _ref2.postLink,
      isViewable = _ref2.isViewable,
      permalinkParts = _ref2.permalinkParts;
  return isEnabled && !isNew && postLink && isViewable && permalinkParts;
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      toggleEditorPanelOpened = _dispatch.toggleEditorPanelOpened;

  var _dispatch2 = dispatch('core/editor'),
      editPost = _dispatch2.editPost;

  return {
    onTogglePanel: function onTogglePanel() {
      return toggleEditorPanelOpened(post_link_PANEL_NAME);
    },
    editPermalink: function editPermalink(newSlug) {
      editPost({
        slug: newSlug
      });
    }
  };
}), Object(external_this_wp_compose_["withState"])({
  forceEmptyField: false
})])(PostLink));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/discussion-panel/index.js


/**
 * WordPress dependencies
 */





/**
 * Module Constants
 */

var discussion_panel_PANEL_NAME = 'discussion-panel';

function DiscussionPanel(_ref) {
  var isEnabled = _ref.isEnabled,
      isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel;

  if (!isEnabled) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: ['comments', 'trackbacks']
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Discussion'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: "comments"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostComments"], null))), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: "trackbacks"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPingbacks"], null)))));
}

/* harmony default export */ var discussion_panel = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isEnabled: select('core/edit-post').isEditorPanelEnabled(discussion_panel_PANEL_NAME),
    isOpened: select('core/edit-post').isEditorPanelOpened(discussion_panel_PANEL_NAME)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  return {
    onTogglePanel: function onTogglePanel() {
      return dispatch('core/edit-post').toggleEditorPanelOpened(discussion_panel_PANEL_NAME);
    }
  };
})])(DiscussionPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/page-attributes/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Module Constants
 */

var page_attributes_PANEL_NAME = 'page-attributes';
function PageAttributes(_ref) {
  var isEnabled = _ref.isEnabled,
      isOpened = _ref.isOpened,
      onTogglePanel = _ref.onTogglePanel,
      postType = _ref.postType;

  if (!isEnabled || !postType) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageAttributesCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_lodash_["get"])(postType, ['labels', 'attributes'], Object(external_this_wp_i18n_["__"])('Page attributes')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageTemplate"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageAttributesParent"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelRow"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PageAttributesOrder"], null))));
}
var page_attributes_applyWithSelect = Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var _select2 = select('core/edit-post'),
      isEditorPanelEnabled = _select2.isEditorPanelEnabled,
      isEditorPanelOpened = _select2.isEditorPanelOpened;

  var _select3 = select('core'),
      getPostType = _select3.getPostType;

  return {
    isEnabled: isEditorPanelEnabled(page_attributes_PANEL_NAME),
    isOpened: isEditorPanelOpened(page_attributes_PANEL_NAME),
    postType: getPostType(getEditedPostAttribute('type'))
  };
});
var page_attributes_applyWithDispatch = Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      toggleEditorPanelOpened = _dispatch.toggleEditorPanelOpened;

  return {
    onTogglePanel: Object(external_this_lodash_["partial"])(toggleEditorPanelOpened, page_attributes_PANEL_NAME)
  };
});
/* harmony default export */ var page_attributes = (Object(external_this_wp_compose_["compose"])(page_attributes_applyWithSelect, page_attributes_applyWithDispatch)(PageAttributes));

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(7);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





var meta_boxes_area_MetaBoxesArea =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MetaBoxesArea, _Component);

  /**
   * @inheritdoc
   */
  function MetaBoxesArea() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, MetaBoxesArea);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MetaBoxesArea).apply(this, arguments));
    _this.bindContainerNode = _this.bindContainerNode.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }
  /**
   * @inheritdoc
   */


  Object(createClass["a" /* default */])(MetaBoxesArea, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.form = document.querySelector('.metabox-location-' + this.props.location);

      if (this.form) {
        this.container.appendChild(this.form);
      }
    }
    /**
     * Get the meta box location form from the original location.
     */

  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      if (this.form) {
        document.querySelector('#metaboxes').appendChild(this.form);
      }
    }
    /**
     * Binds the metabox area container node.
     *
     * @param {Element} node DOM Node.
     */

  }, {
    key: "bindContainerNode",
    value: function bindContainerNode(node) {
      this.container = node;
    }
    /**
     * @inheritdoc
     */

  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          location = _this$props.location,
          isSaving = _this$props.isSaving;
      var classes = classnames_default()('edit-post-meta-boxes-area', "is-".concat(location), {
        'is-loading': isSaving
      });
      return Object(external_this_wp_element_["createElement"])("div", {
        className: classes
      }, isSaving && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null), Object(external_this_wp_element_["createElement"])("div", {
        className: "edit-post-meta-boxes-area__container",
        ref: this.bindContainerNode
      }), Object(external_this_wp_element_["createElement"])("div", {
        className: "edit-post-meta-boxes-area__clear"
      }));
    }
  }]);

  return MetaBoxesArea;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var meta_boxes_area = (Object(external_this_wp_data_["withSelect"])(function (select) {
  return {
    isSaving: select('core/edit-post').isSavingMetaBoxes()
  };
})(meta_boxes_area_MetaBoxesArea));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-box-visibility.js






/**
 * WordPress dependencies
 */



var meta_box_visibility_MetaBoxVisibility =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MetaBoxVisibility, _Component);

  function MetaBoxVisibility() {
    Object(classCallCheck["a" /* default */])(this, MetaBoxVisibility);

    return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MetaBoxVisibility).apply(this, arguments));
  }

  Object(createClass["a" /* default */])(MetaBoxVisibility, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.updateDOM();
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (this.props.isVisible !== prevProps.isVisible) {
        this.updateDOM();
      }
    }
  }, {
    key: "updateDOM",
    value: function updateDOM() {
      var _this$props = this.props,
          id = _this$props.id,
          isVisible = _this$props.isVisible;
      var element = document.getElementById(id);

      if (!element) {
        return;
      }

      if (isVisible) {
        element.classList.remove('is-hidden');
      } else {
        element.classList.add('is-hidden');
      }
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return MetaBoxVisibility;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var meta_box_visibility = (Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var id = _ref.id;
  return {
    isVisible: select('core/edit-post').isEditorPanelEnabled("meta-box-".concat(id))
  };
})(meta_box_visibility_MetaBoxVisibility));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function MetaBoxes(_ref) {
  var location = _ref.location,
      isVisible = _ref.isVisible,
      metaBoxes = _ref.metaBoxes;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_lodash_["map"])(metaBoxes, function (_ref2) {
    var id = _ref2.id;
    return Object(external_this_wp_element_["createElement"])(meta_box_visibility, {
      key: id,
      id: id
    });
  }), isVisible && Object(external_this_wp_element_["createElement"])(meta_boxes_area, {
    location: location
  }));
}

/* harmony default export */ var meta_boxes = (Object(external_this_wp_data_["withSelect"])(function (select, _ref3) {
  var location = _ref3.location;

  var _select = select('core/edit-post'),
      isMetaBoxLocationVisible = _select.isMetaBoxLocationVisible,
      getMetaBoxesPerLocation = _select.getMetaBoxesPerLocation;

  return {
    metaBoxes: getMetaBoxesPerLocation(location),
    isVisible: isMetaBoxLocationVisible(location)
  };
})(MetaBoxes));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-document-setting-panel/index.js


/**
 * Defines as extensibility slot for the Settings sidebar
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var plugin_document_setting_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginDocumentSettingPanel'),
    plugin_document_setting_panel_Fill = plugin_document_setting_panel_createSlotFill.Fill,
    plugin_document_setting_panel_Slot = plugin_document_setting_panel_createSlotFill.Slot;



var plugin_document_setting_panel_PluginDocumentSettingFill = function PluginDocumentSettingFill(_ref) {
  var isEnabled = _ref.isEnabled,
      panelName = _ref.panelName,
      opened = _ref.opened,
      onToggle = _ref.onToggle,
      className = _ref.className,
      title = _ref.title,
      icon = _ref.icon,
      children = _ref.children;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(enable_plugin_document_setting_panel, {
    label: title,
    panelName: panelName
  }), Object(external_this_wp_element_["createElement"])(plugin_document_setting_panel_Fill, null, isEnabled && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: className,
    title: title,
    icon: icon,
    opened: opened,
    onToggle: onToggle
  }, children)));
};
/**
 * Renders items below the Status & Availability panel in the Document Sidebar.
 *
 * @param {Object} props Component properties.
 * @param {string} [props.name] The machine-friendly name for the panel.
 * @param {string} [props.className] An optional class name added to the row.
 * @param {string} [props.title] The title of the panel
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var el = wp.element.createElement;
 * var __ = wp.i18n.__;
 * var registerPlugin = wp.plugins.registerPlugin;
 * var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
 *
 * function MyDocumentSettingPlugin() {
 * 	return el(
 * 		PluginDocumentSettingPanel,
 * 		{
 * 			className: 'my-document-setting-plugin',
 * 			title: 'My Panel',
 * 		},
 * 		__( 'My Document Setting Panel' )
 * 	);
 * }
 *
 * registerPlugin( 'my-document-setting-plugin', {
 * 		render: MyDocumentSettingPlugin
 * } );
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { registerPlugin } = wp.plugins;
 * const { PluginDocumentSettingPanel } = wp.editPost;
 *
 * const MyDocumentSettingTest = () => (
 * 		<PluginDocumentSettingPanel className="my-document-setting-plugin" title="My Panel">
 *			<p>My Document Setting Panel</p>
 *		</PluginDocumentSettingPanel>
 *	);
 *
 *  registerPlugin( 'document-setting-test', { render: MyDocumentSettingTest } );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


var PluginDocumentSettingPanel = Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon,
    panelName: "".concat(context.name, "/").concat(ownProps.name)
  };
}), Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var panelName = _ref2.panelName;
  return {
    opened: select('core/edit-post').isEditorPanelOpened(panelName),
    isEnabled: select('core/edit-post').isEditorPanelEnabled(panelName)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3) {
  var panelName = _ref3.panelName;
  return {
    onToggle: function onToggle() {
      return dispatch('core/edit-post').toggleEditorPanelOpened(panelName);
    }
  };
}))(plugin_document_setting_panel_PluginDocumentSettingFill);
PluginDocumentSettingPanel.Slot = plugin_document_setting_panel_Slot;
/* harmony default export */ var plugin_document_setting_panel = (PluginDocumentSettingPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-sidebar/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */














var settings_sidebar_SettingsSidebar = function SettingsSidebar(_ref) {
  var sidebarName = _ref.sidebarName;
  return Object(external_this_wp_element_["createElement"])(sidebar, {
    name: sidebarName
  }, Object(external_this_wp_element_["createElement"])(settings_header, {
    sidebarName: sidebarName
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Panel"], null, sidebarName === 'edit-post/document' && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(post_status, null), Object(external_this_wp_element_["createElement"])(plugin_document_setting_panel.Slot, null), Object(external_this_wp_element_["createElement"])(last_revision, null), Object(external_this_wp_element_["createElement"])(post_link, null), Object(external_this_wp_element_["createElement"])(post_taxonomies, null), Object(external_this_wp_element_["createElement"])(featured_image, null), Object(external_this_wp_element_["createElement"])(post_excerpt, null), Object(external_this_wp_element_["createElement"])(discussion_panel, null), Object(external_this_wp_element_["createElement"])(page_attributes, null), Object(external_this_wp_element_["createElement"])(meta_boxes, {
    location: "side"
  })), sidebarName === 'edit-post/block' && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockInspector"], null)));
};

/* harmony default export */ var settings_sidebar = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/edit-post'),
      getActiveGeneralSidebarName = _select.getActiveGeneralSidebarName,
      isEditorSidebarOpened = _select.isEditorSidebarOpened;

  return {
    isEditorSidebarOpened: isEditorSidebarOpened(),
    sidebarName: getActiveGeneralSidebarName()
  };
}), Object(external_this_wp_compose_["ifCondition"])(function (_ref2) {
  var isEditorSidebarOpened = _ref2.isEditorSidebarOpened;
  return isEditorSidebarOpened;
}))(settings_sidebar_SettingsSidebar));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-publish-panel/index.js


/**
 * WordPress dependencies
 */




var plugin_post_publish_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginPostPublishPanel'),
    plugin_post_publish_panel_Fill = plugin_post_publish_panel_createSlotFill.Fill,
    plugin_post_publish_panel_Slot = plugin_post_publish_panel_createSlotFill.Slot;

var plugin_post_publish_panel_PluginPostPublishPanelFill = function PluginPostPublishPanelFill(_ref) {
  var children = _ref.children,
      className = _ref.className,
      title = _ref.title,
      _ref$initialOpen = _ref.initialOpen,
      initialOpen = _ref$initialOpen === void 0 ? false : _ref$initialOpen,
      icon = _ref.icon;
  return Object(external_this_wp_element_["createElement"])(plugin_post_publish_panel_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: className,
    initialOpen: initialOpen || !title,
    title: title,
    icon: icon
  }, children));
};
/**
 * Renders provided content to the post-publish panel in the publish flow
 * (side panel that opens after a user publishes the post).
 *
 * @param {Object} props Component properties.
 * @param {string} [props.className] An optional class name added to the panel.
 * @param {string} [props.title] Title displayed at the top of the panel.
 * @param {boolean} [props.initialOpen=false] Whether to have the panel initially opened. When no title is provided it is always opened.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPostPublishPanel = wp.editPost.PluginPostPublishPanel;
 *
 * function MyPluginPostPublishPanel() {
 * 	return wp.element.createElement(
 * 		PluginPostPublishPanel,
 * 		{
 * 			className: 'my-plugin-post-publish-panel',
 * 			title: __( 'My panel title' ),
 * 			initialOpen: true,
 * 		},
 * 		__( 'My panel content' )
 * 	);
 * }
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { __ } = wp.i18n;
 * const { PluginPostPublishPanel } = wp.editPost;
 *
 * const MyPluginPostPublishPanel = () => (
 * 	<PluginPostPublishPanel
 * 		className="my-plugin-post-publish-panel"
 * 		title={ __( 'My panel title' ) }
 * 		initialOpen={ true }
 * 	>
 *         { __( 'My panel content' ) }
 * 	</PluginPostPublishPanel>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


var PluginPostPublishPanel = Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon
  };
}))(plugin_post_publish_panel_PluginPostPublishPanelFill);
PluginPostPublishPanel.Slot = plugin_post_publish_panel_Slot;
/* harmony default export */ var plugin_post_publish_panel = (PluginPostPublishPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js


/**
 * WordPress dependencies
 */




var plugin_pre_publish_panel_createSlotFill = Object(external_this_wp_components_["createSlotFill"])('PluginPrePublishPanel'),
    plugin_pre_publish_panel_Fill = plugin_pre_publish_panel_createSlotFill.Fill,
    plugin_pre_publish_panel_Slot = plugin_pre_publish_panel_createSlotFill.Slot;

var plugin_pre_publish_panel_PluginPrePublishPanelFill = function PluginPrePublishPanelFill(_ref) {
  var children = _ref.children,
      className = _ref.className,
      title = _ref.title,
      _ref$initialOpen = _ref.initialOpen,
      initialOpen = _ref$initialOpen === void 0 ? false : _ref$initialOpen,
      icon = _ref.icon;
  return Object(external_this_wp_element_["createElement"])(plugin_pre_publish_panel_Fill, null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    className: className,
    initialOpen: initialOpen || !title,
    title: title,
    icon: icon
  }, children));
};
/**
 * Renders provided content to the pre-publish side panel in the publish flow
 * (side panel that opens when a user first pushes "Publish" from the main editor).
 *
 * @param {Object}                props                                 Component props.
 * @param {string}                [props.className]                     An optional class name added to the panel.
 * @param {string}                [props.title]                         Title displayed at the top of the panel.
 * @param {boolean}               [props.initialOpen=false]             Whether to have the panel initially opened.
 *                                                                      When no title is provided it is always opened.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/)
 *                                                                      icon slug string, or an SVG WP element, to be rendered when
 *                                                                      the sidebar is pinned to toolbar.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPrePublishPanel = wp.editPost.PluginPrePublishPanel;
 *
 * function MyPluginPrePublishPanel() {
 * 	return wp.element.createElement(
 * 		PluginPrePublishPanel,
 * 		{
 * 			className: 'my-plugin-pre-publish-panel',
 * 			title: __( 'My panel title' ),
 * 			initialOpen: true,
 * 		},
 * 		__( 'My panel content' )
 * 	);
 * }
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * const { __ } = wp.i18n;
 * const { PluginPrePublishPanel } = wp.editPost;
 *
 * const MyPluginPrePublishPanel = () => (
 * 	<PluginPrePublishPanel
 * 		className="my-plugin-pre-publish-panel"
 * 		title={ __( 'My panel title' ) }
 * 		initialOpen={ true }
 * 	>
 * 	    { __( 'My panel content' ) }
 * 	</PluginPrePublishPanel>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


var PluginPrePublishPanel = Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon
  };
}))(plugin_pre_publish_panel_PluginPrePublishPanelFill);
PluginPrePublishPanel.Slot = plugin_pre_publish_panel_Slot;
/* harmony default export */ var plugin_pre_publish_panel = (PluginPrePublishPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/images.js



/**
 * WordPress dependencies
 */

var images_CanvasImage = function CanvasImage(props) {
  return Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
    alt: "",
    src: "data:image/svg+xml,%3Csvg width='306' height='286' viewBox='0 0 306 286' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='306' height='286' rx='4' fill='%2366C6E4'/%3E%3Crect x='36' y='30' width='234' height='256' fill='white'/%3E%3Crect x='36' y='80' width='234' height='94' fill='%23E2E4E7'/%3E%3Cpath d='M140.237 121.47L142.109 125H157.255V133H140.237V121.47ZM159.382 119H155.128L157.255 123H154.064L151.937 119H149.809L151.937 123H148.746L146.618 119H144.491L146.618 123H143.428L141.3 119H140.237C139.067 119 138.12 119.9 138.12 121L138.109 133C138.109 134.1 139.067 135 140.237 135H157.255C158.425 135 159.382 134.1 159.382 133V119Z' fill='%23444444'/%3E%3Crect x='57' y='182' width='91.4727' height='59' fill='%23E2E4E7'/%3E%3Crect x='156.982' y='182' width='91.4727' height='59' fill='%23E2E4E7'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M112.309 203H93.1634C92.0998 203 91.0361 204 91.0361 205V219C91.0361 220.1 91.9934 221 93.1634 221H112.309C113.372 221 114.436 220 114.436 219V205C114.436 204 113.372 203 112.309 203ZM112.309 218.92C112.294 218.941 112.269 218.962 112.248 218.979L112.248 218.979C112.239 218.987 112.23 218.994 112.224 219H93.1634V205.08L93.2485 205H112.213C112.235 205.014 112.258 205.038 112.276 205.057C112.284 205.066 112.292 205.074 112.298 205.08V218.92H112.309ZM99.0134 212.5L101.672 215.51L105.395 211L110.182 217H95.2907L99.0134 212.5Z' fill='%2340464D'/%3E%3Cmask id='mask0' mask-type='alpha' maskUnits='userSpaceOnUse' x='91' y='203' width='24' height='18'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M112.309 203H93.1634C92.0998 203 91.0361 204 91.0361 205V219C91.0361 220.1 91.9934 221 93.1634 221H112.309C113.372 221 114.436 220 114.436 219V205C114.436 204 113.372 203 112.309 203ZM112.309 218.92C112.294 218.941 112.269 218.962 112.248 218.979L112.248 218.979C112.239 218.987 112.23 218.994 112.224 219H93.1634V205.08L93.2485 205H112.213C112.235 205.014 112.258 205.038 112.276 205.057C112.284 205.066 112.292 205.074 112.298 205.08V218.92H112.309ZM99.0134 212.5L101.672 215.51L105.395 211L110.182 217H95.2907L99.0134 212.5Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask0)'%3E%3Crect x='89.9727' y='200' width='25.5273' height='24' fill='%2340464D'/%3E%3C/g%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M212.291 203H193.145C192.082 203 191.018 204 191.018 205V219C191.018 220.1 191.975 221 193.145 221H212.291C213.354 221 214.418 220 214.418 219V205C214.418 204 213.354 203 212.291 203ZM212.291 218.92C212.276 218.941 212.251 218.962 212.23 218.979L212.23 218.979C212.221 218.987 212.212 218.994 212.206 219H193.145V205.08L193.23 205H212.195C212.217 205.014 212.24 205.038 212.258 205.057C212.266 205.066 212.274 205.074 212.28 205.08V218.92H212.291ZM198.995 212.5L201.654 215.51L205.377 211L210.164 217H195.273L198.995 212.5Z' fill='%2340464D'/%3E%3Cmask id='mask1' mask-type='alpha' maskUnits='userSpaceOnUse' x='191' y='203' width='24' height='18'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M212.291 203H193.145C192.082 203 191.018 204 191.018 205V219C191.018 220.1 191.975 221 193.145 221H212.291C213.354 221 214.418 220 214.418 219V205C214.418 204 213.354 203 212.291 203ZM212.291 218.92C212.276 218.941 212.251 218.962 212.23 218.979L212.23 218.979C212.221 218.987 212.212 218.994 212.206 219H193.145V205.08L193.23 205H212.195C212.217 205.014 212.24 205.038 212.258 205.057C212.266 205.066 212.274 205.074 212.28 205.08V218.92H212.291ZM198.995 212.5L201.654 215.51L205.377 211L210.164 217H195.273L198.995 212.5Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask1)'%3E%3Crect x='189.955' y='200' width='25.5273' height='24' fill='%2340464D'/%3E%3C/g%3E%3Crect x='57' y='38' width='191.455' height='34' fill='%23E2E4E7'/%3E%3Cpath d='M155.918 47.8V54.04H149.537V47.8H146.346V63.4H149.537V57.16H155.918V63.4H159.109V47.8' fill='%2340464D'/%3E%3Crect x='58' y='249' width='191' height='37' fill='%23E2E4E7'/%3E%3Cpath d='M160.127 261.4H150.606C149.546 261.4 148.576 261.64 147.696 262.12C146.802 262.612 146.1 263.272 145.59 264.1C145.066 264.928 144.811 265.84 144.811 266.824C144.811 267.808 145.066 268.72 145.59 269.548C146.1 270.376 146.802 271.036 147.696 271.516C148.576 272.008 149.546 272.248 150.606 272.248H151.155V279.4C151.155 279.724 151.282 280.012 151.525 280.252C151.78 280.48 152.086 280.6 152.431 280.6C152.788 280.6 153.082 280.48 153.337 280.252C153.592 280.012 153.72 279.724 153.72 279.4V265C153.72 264.676 153.835 264.388 154.09 264.148C154.345 263.92 154.652 263.8 154.996 263.8C155.341 263.8 155.647 263.92 155.903 264.148C156.145 264.388 156.273 264.676 156.273 265V279.4C156.273 279.724 156.4 280.012 156.656 280.252C156.911 280.48 157.205 280.6 157.562 280.6C157.907 280.6 158.213 280.48 158.468 280.252C158.711 280.012 158.838 279.724 158.838 279.4V263.8H160.127C160.472 263.8 160.766 263.68 161.021 263.44C161.276 263.212 161.404 262.924 161.404 262.6C161.404 262.276 161.276 261.988 161.021 261.748C160.766 261.52 160.472 261.4 160.127 261.4Z' fill='%2340464D'/%3E%3C/svg%3E%0A"
  }, props));
};
var images_EditorImage = function EditorImage(props) {
  return Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
    alt: "",
    src: "data:image/svg+xml,%3Csvg width='306' height='286' viewBox='0 0 306 286' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='306' height='286' rx='4' fill='%2366C6E4'/%3E%3Crect x='34.5' y='89.9424' width='237' height='113.423' fill='white' stroke='%238D96A0'/%3E%3Crect x='42.2383' y='98.5962' width='219.692' height='95.6618' fill='%23E2E4E7'/%3E%3Crect x='34.5' y='71.6346' width='27.0718' height='18.1324' fill='white' stroke='%238D96A0'/%3E%3Crect x='152.89' y='71.6346' width='18.5282' height='18.1324' fill='white' stroke='%238D96A0'/%3E%3Crect x='61.3516' y='71.6346' width='51.482' height='18.1324' fill='white' stroke='%238D96A0'/%3E%3Crect x='112.613' y='71.6346' width='40.4974' height='18.1324' fill='white' stroke='%238D96A0'/%3E%3Cpath d='M157.577 137.408H149.383C148.471 137.408 147.636 137.628 146.878 138.068C146.109 138.518 145.505 139.122 145.066 139.88C144.615 140.638 144.396 141.473 144.396 142.373C144.396 143.274 144.615 144.109 145.066 144.867C145.505 145.625 146.109 146.229 146.878 146.668C147.636 147.119 148.471 147.339 149.383 147.339H149.855V153.885C149.855 154.182 149.965 154.446 150.173 154.665C150.393 154.874 150.657 154.984 150.953 154.984C151.261 154.984 151.514 154.874 151.733 154.665C151.953 154.446 152.063 154.182 152.063 153.885V140.704C152.063 140.407 152.162 140.144 152.381 139.924C152.601 139.715 152.865 139.605 153.161 139.605C153.458 139.605 153.721 139.715 153.941 139.924C154.15 140.144 154.26 140.407 154.26 140.704V153.885C154.26 154.182 154.37 154.446 154.589 154.665C154.809 154.874 155.062 154.984 155.369 154.984C155.666 154.984 155.929 154.874 156.149 154.665C156.358 154.446 156.468 154.182 156.468 153.885V139.605H157.577C157.874 139.605 158.126 139.496 158.346 139.276C158.566 139.067 158.676 138.803 158.676 138.507C158.676 138.21 158.566 137.947 158.346 137.727C158.126 137.518 157.874 137.408 157.577 137.408Z' fill='%2340464D'/%3E%3Crect x='41.3232' y='77.1135' width='15.8667' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='66.9536' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='77.9385' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='88.9229' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='99.9077' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='118.215' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='129.2' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='140.185' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3Crect x='158.492' y='77.1135' width='7.32308' height='7.17464' fill='%23E2E4E7'/%3E%3C/svg%3E%0A"
  }, props));
};
var images_BlockLibraryImage = function BlockLibraryImage(props) {
  return Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
    alt: "",
    src: "data:image/svg+xml,%3Csvg width='306' height='286' viewBox='0 0 306 286' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='306' height='286' rx='4' fill='%2366C6E4'/%3E%3Cmask id='mask0' mask-type='alpha' maskUnits='userSpaceOnUse' x='141' y='25' width='24' height='24'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M152.765 25C146.294 25 141 30.2943 141 36.7651C141 43.2359 146.294 48.5302 152.765 48.5302C159.236 48.5302 164.53 43.2359 164.53 36.7651C164.53 30.2943 159.236 25 152.765 25ZM151.589 32.0591V35.5886H148.059V37.9416H151.589V41.4711H153.942V37.9416H157.471V35.5886H153.942V32.0591H151.589ZM143.353 36.7651C143.353 41.9417 147.588 46.1772 152.765 46.1772C157.942 46.1772 162.177 41.9417 162.177 36.7651C162.177 31.5885 157.942 27.353 152.765 27.353C147.588 27.353 143.353 31.5885 143.353 36.7651Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask0)'%3E%3Crect x='141' y='25' width='23.5253' height='23.5253' fill='white'/%3E%3C/g%3E%3Cg filter='url(%23filter0_d)'%3E%3Crect x='48' y='63' width='210' height='190' fill='white'/%3E%3C/g%3E%3Cmask id='mask1' mask-type='alpha' maskUnits='userSpaceOnUse' x='143' y='139' width='20' height='16'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M143.75 141C143.75 140.17 144.42 139.5 145.25 139.5C146.08 139.5 146.75 140.17 146.75 141C146.75 141.83 146.08 142.5 145.25 142.5C144.42 142.5 143.75 141.83 143.75 141ZM143.75 147C143.75 146.17 144.42 145.5 145.25 145.5C146.08 145.5 146.75 146.17 146.75 147C146.75 147.83 146.08 148.5 145.25 148.5C144.42 148.5 143.75 147.83 143.75 147ZM145.25 151.5C144.42 151.5 143.75 152.18 143.75 153C143.75 153.82 144.43 154.5 145.25 154.5C146.07 154.5 146.75 153.82 146.75 153C146.75 152.18 146.08 151.5 145.25 151.5ZM162.25 154H148.25V152H162.25V154ZM148.25 148H162.25V146H148.25V148ZM148.25 142V140H162.25V142H148.25Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask1)'%3E%3Crect x='141' y='135' width='24' height='24' fill='%23444444'/%3E%3C/g%3E%3Cmask id='mask2' mask-type='alpha' maskUnits='userSpaceOnUse' x='139' y='54' width='28' height='11'%3E%3Crect x='139' y='54' width='28' height='11' fill='%23C4C4C4'/%3E%3C/mask%3E%3Cg mask='url(%23mask2)'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M139 67L153 54L167 67H139Z' fill='white'/%3E%3C/g%3E%3Crect x='59' y='74' width='188' height='28' rx='3' stroke='%231486B8' stroke-width='2'/%3E%3Cpath d='M211 207.47L212.76 211H227V219H211V207.47ZM229 205H225L227 209H224L222 205H220L222 209H219L217 205H215L217 209H214L212 205H211C209.9 205 209.01 205.9 209.01 207L209 219C209 220.1 209.9 221 211 221H227C228.1 221 229 220.1 229 219V205Z' fill='%23444444'/%3E%3Cpath d='M94.0001 136.4H85.0481C84.0521 136.4 83.1401 136.64 82.3121 137.12C81.4721 137.612 80.8121 138.272 80.3321 139.1C79.8401 139.928 79.6001 140.84 79.6001 141.824C79.6001 142.808 79.8401 143.72 80.3321 144.548C80.8121 145.376 81.4721 146.036 82.3121 146.516C83.1401 147.008 84.0521 147.248 85.0481 147.248H85.5641V154.4C85.5641 154.724 85.6841 155.012 85.9121 155.252C86.1521 155.48 86.4401 155.6 86.7641 155.6C87.1001 155.6 87.3761 155.48 87.6161 155.252C87.8561 155.012 87.9761 154.724 87.9761 154.4V140C87.9761 139.676 88.0841 139.388 88.3241 139.148C88.5641 138.92 88.8521 138.8 89.1761 138.8C89.5001 138.8 89.7881 138.92 90.0281 139.148C90.2561 139.388 90.3761 139.676 90.3761 140V154.4C90.3761 154.724 90.4961 155.012 90.7361 155.252C90.9761 155.48 91.2521 155.6 91.5881 155.6C91.9121 155.6 92.2001 155.48 92.4401 155.252C92.6681 155.012 92.7881 154.724 92.7881 154.4V138.8H94.0001C94.3241 138.8 94.6001 138.68 94.8401 138.44C95.0801 138.212 95.2001 137.924 95.2001 137.6C95.2001 137.276 95.0801 136.988 94.8401 136.748C94.6001 136.52 94.3241 136.4 94.0001 136.4Z' fill='%23444444'/%3E%3Cmask id='mask3' mask-type='alpha' maskUnits='userSpaceOnUse' x='76' y='204' width='22' height='18'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M96 204H78C77 204 76 205 76 206V220C76 221.1 76.9 222 78 222H96C97 222 98 221 98 220V206C98 205 97 204 96 204ZM96 219.92C95.9861 219.941 95.9624 219.962 95.9426 219.979C95.9339 219.987 95.9261 219.994 95.92 220H78V206.08L78.08 206H95.91C95.9309 206.014 95.9518 206.038 95.9694 206.057C95.977 206.066 95.9839 206.074 95.99 206.08V219.92H96ZM83.5 213.5L86 216.51L89.5 212L94 218H80L83.5 213.5Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask3)'%3E%3Crect x='75' y='201' width='24' height='24' fill='%23444444'/%3E%3C/g%3E%3Cpath d='M161 205V217H149V205H161ZM161 203H149C147.9 203 147 203.9 147 205V217C147 218.1 147.9 219 149 219H161C162.1 219 163 218.1 163 217V205C163 203.9 162.1 203 161 203ZM152.5 212.67L154.19 214.93L156.67 211.83L160 216H150L152.5 212.67ZM143 207V221C143 222.1 143.9 223 145 223H159V221H145V207H143Z' fill='%23444444'/%3E%3Cmask id='mask4' mask-type='alpha' maskUnits='userSpaceOnUse' x='210' y='140' width='18' height='12'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M215.62 152H210.38L212.38 148H210V140H218V147.24L215.62 152ZM220.38 152H225.62L228 147.24V140H220V148H222.38L220.38 152ZM224.38 150H223.62L225.62 146H222V142H226V146.76L224.38 150ZM214.38 150H213.62L215.62 146H212V142H216V146.76L214.38 150Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask4)'%3E%3Crect x='207' y='134' width='24' height='24' fill='%23444444'/%3E%3C/g%3E%3Cdefs%3E%3Cfilter id='filter0_d' x='18' y='36' width='270' height='250' filterUnits='userSpaceOnUse' color-interpolation-filters='sRGB'%3E%3CfeFlood flood-opacity='0' result='BackgroundImageFix'/%3E%3CfeColorMatrix in='SourceAlpha' type='matrix' values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0'/%3E%3CfeOffset dy='3'/%3E%3CfeGaussianBlur stdDeviation='15'/%3E%3CfeColorMatrix type='matrix' values='0 0 0 0 0.0980392 0 0 0 0 0.117647 0 0 0 0 0.137255 0 0 0 0.1 0'/%3E%3CfeBlend mode='normal' in2='BackgroundImageFix' result='effect1_dropShadow'/%3E%3CfeBlend mode='normal' in='SourceGraphic' in2='effect1_dropShadow' result='shape'/%3E%3C/filter%3E%3C/defs%3E%3C/svg%3E%0A"
  }, props));
};
var images_DocumentationImage = function DocumentationImage(props) {
  return Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
    alt: "",
    src: "data:image/svg+xml,%3C%3Fxml version='1.0' encoding='UTF-8'%3F%3E%3Csvg width='306px' height='286px' viewBox='0 0 306 286' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'%3E%3C!-- Generator: Sketch 61.2 (89653) - https://sketch.com --%3E%3Ctitle%3EPage 1%3C/title%3E%3Cdesc%3ECreated with Sketch.%3C/desc%3E%3Cg id='Page-1' stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'%3E%3Cg id='Documentation'%3E%3Crect id='bg' fill='%2361C6E6' x='0' y='0' width='306' height='286' rx='4'%3E%3C/rect%3E%3Crect id='page' fill='%23FFFFFF' x='36' y='30' width='234' height='256'%3E%3C/rect%3E%3Crect id='text' fill='%23E2E4E7' x='124' y='119' width='106' height='13'%3E%3C/rect%3E%3Crect id='heading' fill='%23E2E4E7' x='76' y='96' width='154' height='13'%3E%3C/rect%3E%3Crect id='header' fill='%2340464D' x='36' y='30' width='234' height='41'%3E%3C/rect%3E%3Cimage id='WordPress-logotype-wmark-white' x='45' y='32' width='37' height='37' xlink:href='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA+gAAAPoCAYAAABNo9TkAAAABGdBTUEAALGOfPtRkwAAAERlWElmTU0AKgAAAAgAAYdpAAQAAAABAAAAGgAAAAAAA6ABAAMAAAABAAEAAKACAAQAAAABAAAD6KADAAQAAAABAAAD6AAAAAAYK4+nAABAAElEQVR4AezdB7gkRdX/8UV2yTmDhMuSkQySQUCSpBWUvCACggFFRPQVRREB/6+gooiB8BphEVCygESRIEmyBAlLRiTnzP93oGeZe/eGmek63VXV33qeeubemelTpz49PV013dMzahQFAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQQAABBBBAAAEEEEAAAQQQ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AQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAA4nty+wAAB2JJREFUAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAAQIECBAgQIAAgd4J/B+dcycNJZZ/4gAAAABJRU5ErkJggg=='%3E%3C/image%3E%3Crect id='text' fill='%23E2E4E7' x='124' y='173' width='106' height='13'%3E%3C/rect%3E%3Crect id='text' fill='%23E2E4E7' x='124' y='137' width='106' height='13'%3E%3C/rect%3E%3Crect id='text' fill='%23E2E4E7' x='124' y='191' width='106' height='13'%3E%3C/rect%3E%3Crect id='text' fill='%23E2E4E7' x='124' y='245' width='106' height='13'%3E%3C/rect%3E%3Crect id='text' fill='%23E2E4E7' x='124' y='155' width='106' height='13'%3E%3C/rect%3E%3Crect id='text' fill='%23E2E4E7' x='124' y='209' width='106' height='13'%3E%3C/rect%3E%3Crect id='text' fill='%23E2E4E7' x='124' y='263' width='106' height='13'%3E%3C/rect%3E%3C/g%3E%3C/g%3E%3C/svg%3E"
  }, props));
};
var images_InserterIconImage = function InserterIconImage(props) {
  return Object(external_this_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
    alt: Object(external_this_wp_i18n_["__"])('inserter'),
    src: "data:image/svg+xml;charset=utf8,%3Csvg width='18' height='18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M8.824 0C3.97 0 0 3.97 0 8.824c0 4.853 3.97 8.824 8.824 8.824 4.853 0 8.824-3.971 8.824-8.824S13.677 0 8.824 0zM7.94 5.294v2.647H5.294v1.765h2.647v2.647h1.765V9.706h2.647V7.941H9.706V5.294H7.941zm-6.176 3.53c0 3.882 3.176 7.059 7.059 7.059 3.882 0 7.059-3.177 7.059-7.06 0-3.882-3.177-7.058-7.06-7.058-3.882 0-7.058 3.176-7.058 7.059z' fill='%234A4A4A'/%3E%3Cmask id='a' maskUnits='userSpaceOnUse' x='0' y='0' width='18' height='18'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M8.824 0C3.97 0 0 3.97 0 8.824c0 4.853 3.97 8.824 8.824 8.824 4.853 0 8.824-3.971 8.824-8.824S13.677 0 8.824 0zM7.94 5.294v2.647H5.294v1.765h2.647v2.647h1.765V9.706h2.647V7.941H9.706V5.294H7.941zm-6.176 3.53c0 3.882 3.176 7.059 7.059 7.059 3.882 0 7.059-3.177 7.059-7.06 0-3.882-3.177-7.058-7.06-7.058-3.882 0-7.058 3.176-7.058 7.059z' fill='%23fff'/%3E%3C/mask%3E%3Cg mask='url(%23a)'%3E%3Cpath fill='%23444' d='M0 0h17.644v17.644H0z'/%3E%3C/g%3E%3C/svg%3E"
  }, props));
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function WelcomeGuide() {
  var isActive = Object(external_this_wp_data_["useSelect"])(function (select) {
    return select('core/edit-post').isFeatureActive('welcomeGuide');
  }, []);

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/edit-post'),
      toggleFeature = _useDispatch.toggleFeature;

  if (!isActive) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Guide"], {
    className: "edit-post-welcome-guide",
    contentLabel: Object(external_this_wp_i18n_["__"])('Welcome to the Block Editor'),
    finishButtonText: Object(external_this_wp_i18n_["__"])('Get started'),
    onFinish: function onFinish() {
      return toggleFeature('welcomeGuide');
    }
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["GuidePage"], {
    className: "edit-post-welcome-guide__page"
  }, Object(external_this_wp_element_["createElement"])("h1", {
    className: "edit-post-welcome-guide__heading"
  }, Object(external_this_wp_i18n_["__"])('Welcome to the Block Editor')), Object(external_this_wp_element_["createElement"])(images_CanvasImage, {
    className: "edit-post-welcome-guide__image"
  }), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-welcome-guide__text"
  }, Object(external_this_wp_i18n_["__"])('In the WordPress editor, each paragraph, image, or video is presented as a distinct “block” of content.'))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["GuidePage"], {
    className: "edit-post-welcome-guide__page"
  }, Object(external_this_wp_element_["createElement"])("h1", {
    className: "edit-post-welcome-guide__heading"
  }, Object(external_this_wp_i18n_["__"])('Make each block your own')), Object(external_this_wp_element_["createElement"])(images_EditorImage, {
    className: "edit-post-welcome-guide__image"
  }), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-welcome-guide__text"
  }, Object(external_this_wp_i18n_["__"])('Each block comes with its own set of controls for changing things like color, width, and alignment. These will show and hide automatically when you have a block selected.'))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["GuidePage"], {
    className: "edit-post-welcome-guide__page"
  }, Object(external_this_wp_element_["createElement"])("h1", {
    className: "edit-post-welcome-guide__heading"
  }, Object(external_this_wp_i18n_["__"])('Get to know the Block Library')), Object(external_this_wp_element_["createElement"])(images_BlockLibraryImage, {
    className: "edit-post-welcome-guide__image"
  }), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-welcome-guide__text"
  }, Object(external_this_wp_element_["__experimentalCreateInterpolateElement"])(Object(external_this_wp_i18n_["__"])('All of the blocks available to you live in the Block Library. You’ll find it wherever you see the <InserterIconImage /> icon.'), {
    InserterIconImage: Object(external_this_wp_element_["createElement"])(images_InserterIconImage, {
      className: "edit-post-welcome-guide__inserter-icon"
    })
  }))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["GuidePage"], {
    className: "edit-post-welcome-guide__page"
  }, Object(external_this_wp_element_["createElement"])("h1", {
    className: "edit-post-welcome-guide__heading"
  }, Object(external_this_wp_i18n_["__"])('Learn how to use the Block Editor')), Object(external_this_wp_element_["createElement"])(images_DocumentationImage, {
    className: "edit-post-welcome-guide__image"
  }), Object(external_this_wp_element_["createElement"])("p", {
    className: "edit-post-welcome-guide__text"
  }, Object(external_this_wp_i18n_["__"])('New to the Block Editor? Want to learn more about using it? '), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
    href: Object(external_this_wp_i18n_["__"])('https://wordpress.org/support/article/wordpress-editor/')
  }, Object(external_this_wp_i18n_["__"])("Here's a detailed guide.")))));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */

















function Layout() {
  var isMobileViewport = Object(external_this_wp_compose_["useViewportMatch"])('small', '<');

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core/edit-post'),
      closePublishSidebar = _useDispatch.closePublishSidebar,
      openGeneralSidebar = _useDispatch.openGeneralSidebar,
      togglePublishSidebar = _useDispatch.togglePublishSidebar;

  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      hasFixedToolbar: select('core/edit-post').isFeatureActive('fixedToolbar'),
      editorSidebarOpened: select('core/edit-post').isEditorSidebarOpened(),
      pluginSidebarOpened: select('core/edit-post').isPluginSidebarOpened(),
      publishSidebarOpened: select('core/edit-post').isPublishSidebarOpened(),
      mode: select('core/edit-post').getEditorMode(),
      isRichEditingEnabled: select('core/editor').getEditorSettings().richEditingEnabled,
      hasActiveMetaboxes: select('core/edit-post').hasMetaBoxes(),
      isSaving: select('core/edit-post').isSavingMetaBoxes(),
      previousShortcut: select('core/keyboard-shortcuts').getAllShortcutRawKeyCombinations('core/edit-post/previous-region'),
      nextShortcut: select('core/keyboard-shortcuts').getAllShortcutRawKeyCombinations('core/edit-post/next-region'),
      hasBlockSelected: select('core/block-editor').getBlockSelectionStart()
    };
  }, []),
      mode = _useSelect.mode,
      isRichEditingEnabled = _useSelect.isRichEditingEnabled,
      editorSidebarOpened = _useSelect.editorSidebarOpened,
      pluginSidebarOpened = _useSelect.pluginSidebarOpened,
      publishSidebarOpened = _useSelect.publishSidebarOpened,
      hasActiveMetaboxes = _useSelect.hasActiveMetaboxes,
      isSaving = _useSelect.isSaving,
      hasFixedToolbar = _useSelect.hasFixedToolbar,
      previousShortcut = _useSelect.previousShortcut,
      nextShortcut = _useSelect.nextShortcut,
      hasBlockSelected = _useSelect.hasBlockSelected;

  var showPageTemplatePicker = Object(external_this_wp_blockEditor_["__experimentalUsePageTemplatePickerVisible"])();

  var sidebarIsOpened = editorSidebarOpened || pluginSidebarOpened || publishSidebarOpened;
  var className = classnames_default()('edit-post-layout', 'is-mode-' + mode, {
    'is-sidebar-opened': sidebarIsOpened,
    'has-fixed-toolbar': hasFixedToolbar,
    'has-metaboxes': hasActiveMetaboxes
  });

  var openSidebarPanel = function openSidebarPanel() {
    return openGeneralSidebar(hasBlockSelected ? 'edit-post/block' : 'edit-post/document');
  };

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(fullscreen_mode, null), Object(external_this_wp_element_["createElement"])(browser_url, null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["UnsavedChangesWarning"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["AutosaveMonitor"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["LocalAutosaveMonitor"], null), Object(external_this_wp_element_["createElement"])(keyboard_shortcuts, null), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorKeyboardShortcutsRegister"], null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["FocusReturnProvider"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalEditorSkeleton"], {
    className: className,
    header: Object(external_this_wp_element_["createElement"])(header, null),
    sidebar: Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, !sidebarIsOpened && Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-layout__toogle-sidebar-panel"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
      isSecondary: true,
      className: "edit-post-layout__toogle-sidebar-panel-button",
      onClick: openSidebarPanel,
      "aria-expanded": false
    }, hasBlockSelected ? Object(external_this_wp_i18n_["__"])('Open block settings') : Object(external_this_wp_i18n_["__"])('Open document settings'))), Object(external_this_wp_element_["createElement"])(settings_sidebar, null), Object(external_this_wp_element_["createElement"])(sidebar.Slot, null)),
    content: Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorNotices"], null), (mode === 'text' || !isRichEditingEnabled) && Object(external_this_wp_element_["createElement"])(text_editor, null), isRichEditingEnabled && mode === 'visual' && Object(external_this_wp_element_["createElement"])(visual_editor, null), Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-layout__metaboxes"
    }, Object(external_this_wp_element_["createElement"])(meta_boxes, {
      location: "normal"
    }), Object(external_this_wp_element_["createElement"])(meta_boxes, {
      location: "advanced"
    })), isMobileViewport && sidebarIsOpened && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ScrollLock"], null)),
    footer: isRichEditingEnabled && mode === 'visual' && Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-layout__footer"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockBreadcrumb"], null)),
    publish: publishSidebarOpened ? Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostPublishPanel"], {
      onClose: closePublishSidebar,
      forceIsDirty: hasActiveMetaboxes,
      forceIsSaving: isSaving,
      PrePublishExtension: plugin_pre_publish_panel.Slot,
      PostPublishExtension: plugin_post_publish_panel.Slot
    }) : Object(external_this_wp_element_["createElement"])("div", {
      className: "edit-post-layout__toogle-publish-panel"
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
      isSecondary: true,
      className: "edit-post-layout__toogle-publish-panel-button",
      onClick: togglePublishSidebar,
      "aria-expanded": false
    }, Object(external_this_wp_i18n_["__"])('Open publish panel'))),
    shortcuts: {
      previous: previousShortcut,
      next: nextShortcut
    }
  }), Object(external_this_wp_element_["createElement"])(manage_blocks_modal, null), Object(external_this_wp_element_["createElement"])(options_modal, null), Object(external_this_wp_element_["createElement"])(keyboard_shortcut_help_modal, null), Object(external_this_wp_element_["createElement"])(WelcomeGuide, null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Popover"].Slot, null), Object(external_this_wp_element_["createElement"])(external_this_wp_plugins_["PluginArea"], null), showPageTemplatePicker && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalPageTemplatePicker"], null)));
}

/* harmony default export */ var layout = (Layout);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/listener-hooks.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * This listener hook monitors for block selection and triggers the appropriate
 * sidebar state.
 *
 * @param {number} postId  The current post id.
 */

var listener_hooks_useBlockSelectionListener = function useBlockSelectionListener(postId) {
  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      hasBlockSelection: !!select('core/block-editor').getBlockSelectionStart(),
      isEditorSidebarOpened: select(STORE_KEY).isEditorSidebarOpened()
    };
  }, [postId]),
      hasBlockSelection = _useSelect.hasBlockSelection,
      isEditorSidebarOpened = _useSelect.isEditorSidebarOpened;

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])(STORE_KEY),
      openGeneralSidebar = _useDispatch.openGeneralSidebar;

  Object(external_this_wp_element_["useEffect"])(function () {
    if (!isEditorSidebarOpened) {
      return;
    }

    if (hasBlockSelection) {
      openGeneralSidebar('edit-post/block');
    } else {
      openGeneralSidebar('edit-post/document');
    }
  }, [hasBlockSelection, isEditorSidebarOpened]);
};
/**
 * This listener hook is used to monitor viewport size and adjust the sidebar
 * accordingly.
 *
 * @param {number} postId  The current post id.
 */

var listener_hooks_useAdjustSidebarListener = function useAdjustSidebarListener(postId) {
  var _useSelect2 = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      isSmall: select('core/viewport').isViewportMatch('< medium'),
      activeGeneralSidebarName: select(STORE_KEY).getActiveGeneralSidebarName()
    };
  }, [postId]),
      isSmall = _useSelect2.isSmall,
      activeGeneralSidebarName = _useSelect2.activeGeneralSidebarName;

  var _useDispatch2 = Object(external_this_wp_data_["useDispatch"])(STORE_KEY),
      openGeneralSidebar = _useDispatch2.openGeneralSidebar,
      closeGeneralSidebar = _useDispatch2.closeGeneralSidebar;

  var previousIsSmall = Object(external_this_wp_element_["useRef"])(null);
  var sidebarToReOpenOnExpand = Object(external_this_wp_element_["useRef"])(null);
  Object(external_this_wp_element_["useEffect"])(function () {
    if (previousIsSmall.current === isSmall) {
      return;
    }

    previousIsSmall.current = isSmall;

    if (isSmall) {
      sidebarToReOpenOnExpand.current = activeGeneralSidebarName;

      if (activeGeneralSidebarName) {
        closeGeneralSidebar();
      }
    } else if (sidebarToReOpenOnExpand.current && !activeGeneralSidebarName) {
      openGeneralSidebar(sidebarToReOpenOnExpand.current);
      sidebarToReOpenOnExpand.current = null;
    }
  }, [isSmall, activeGeneralSidebarName]);
};
/**
 * This listener hook monitors any change in permalink and updates the view
 * post link in the admin bar.
 *
 * @param {number} postId
 */

var listener_hooks_useUpdatePostLinkListener = function useUpdatePostLinkListener(postId) {
  var _useSelect3 = Object(external_this_wp_data_["useSelect"])(function (select) {
    return {
      newPermalink: select('core/editor').getCurrentPost().link
    };
  }, [postId]),
      newPermalink = _useSelect3.newPermalink;

  var nodeToUpdate = Object(external_this_wp_element_["useRef"])();
  Object(external_this_wp_element_["useEffect"])(function () {
    nodeToUpdate.current = document.querySelector(VIEW_AS_PREVIEW_LINK_SELECTOR) || document.querySelector(VIEW_AS_LINK_SELECTOR);
  }, [postId]);
  Object(external_this_wp_element_["useEffect"])(function () {
    if (!newPermalink || !nodeToUpdate.current) {
      return;
    }

    nodeToUpdate.current.setAttribute('href', newPermalink);
  }, [newPermalink]);
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/index.js
/**
 * Internal dependencies
 */

/**
 * Data component used for initializing the editor and re-initializes
 * when postId changes or on unmount.
 *
 * @param {number} postId  The id of the post.
 * @return {null} This is a data component so does not render any ui.
 */

/* harmony default export */ var editor_initialization = (function (_ref) {
  var postId = _ref.postId;
  listener_hooks_useBlockSelectionListener(postId);
  listener_hooks_useAdjustSidebarListener(postId);
  listener_hooks_useUpdatePostLinkListener(postId);
  return null;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/editor.js











function editor_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function editor_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { editor_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { editor_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */






var editor_Editor =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(Editor, _Component);

  function Editor() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, Editor);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(Editor).apply(this, arguments));
    _this.getEditorSettings = memize_default()(_this.getEditorSettings, {
      maxSize: 1
    });
    return _this;
  }

  Object(createClass["a" /* default */])(Editor, [{
    key: "getEditorSettings",
    value: function getEditorSettings(settings, hasFixedToolbar, showInserterHelpPanel, focusMode, hiddenBlockTypes, blockTypes, preferredStyleVariations, __experimentalLocalAutosaveInterval, updatePreferredStyleVariations) {
      settings = editor_objectSpread({}, settings, {
        __experimentalPreferredStyleVariations: {
          value: preferredStyleVariations,
          onChange: updatePreferredStyleVariations
        },
        hasFixedToolbar: hasFixedToolbar,
        focusMode: focusMode,
        showInserterHelpPanel: showInserterHelpPanel,
        __experimentalLocalAutosaveInterval: __experimentalLocalAutosaveInterval
      }); // Omit hidden block types if exists and non-empty.

      if (Object(external_this_lodash_["size"])(hiddenBlockTypes) > 0) {
        // Defer to passed setting for `allowedBlockTypes` if provided as
        // anything other than `true` (where `true` is equivalent to allow
        // all block types).
        var defaultAllowedBlockTypes = true === settings.allowedBlockTypes ? Object(external_this_lodash_["map"])(blockTypes, 'name') : settings.allowedBlockTypes || [];
        settings.allowedBlockTypes = external_this_lodash_["without"].apply(void 0, [defaultAllowedBlockTypes].concat(Object(toConsumableArray["a" /* default */])(hiddenBlockTypes)));
      }

      return settings;
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          settings = _this$props.settings,
          hasFixedToolbar = _this$props.hasFixedToolbar,
          focusMode = _this$props.focusMode,
          post = _this$props.post,
          postId = _this$props.postId,
          initialEdits = _this$props.initialEdits,
          onError = _this$props.onError,
          hiddenBlockTypes = _this$props.hiddenBlockTypes,
          blockTypes = _this$props.blockTypes,
          preferredStyleVariations = _this$props.preferredStyleVariations,
          __experimentalLocalAutosaveInterval = _this$props.__experimentalLocalAutosaveInterval,
          showInserterHelpPanel = _this$props.showInserterHelpPanel,
          updatePreferredStyleVariations = _this$props.updatePreferredStyleVariations,
          props = Object(objectWithoutProperties["a" /* default */])(_this$props, ["settings", "hasFixedToolbar", "focusMode", "post", "postId", "initialEdits", "onError", "hiddenBlockTypes", "blockTypes", "preferredStyleVariations", "__experimentalLocalAutosaveInterval", "showInserterHelpPanel", "updatePreferredStyleVariations"]);

      if (!post) {
        return null;
      }

      var editorSettings = this.getEditorSettings(settings, hasFixedToolbar, showInserterHelpPanel, focusMode, hiddenBlockTypes, blockTypes, preferredStyleVariations, __experimentalLocalAutosaveInterval, updatePreferredStyleVariations);
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["StrictMode"], null, Object(external_this_wp_element_["createElement"])(edit_post_settings.Provider, {
        value: settings
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SlotFillProvider"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["DropZoneProvider"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["EditorProvider"], Object(esm_extends["a" /* default */])({
        settings: editorSettings,
        post: post,
        initialEdits: initialEdits,
        useSubRegistry: false
      }, props), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["ErrorBoundary"], {
        onError: onError
      }, Object(external_this_wp_element_["createElement"])(editor_initialization, {
        postId: postId
      }), Object(external_this_wp_element_["createElement"])(layout, null), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["KeyboardShortcuts"], {
        shortcuts: prevent_event_discovery
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PostLockedModal"], null))))));
    }
  }]);

  return Editor;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var editor = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var postId = _ref.postId,
      postType = _ref.postType;

  var _select = select('core/edit-post'),
      isFeatureActive = _select.isFeatureActive,
      getPreference = _select.getPreference;

  var _select2 = select('core'),
      getEntityRecord = _select2.getEntityRecord;

  var _select3 = select('core/blocks'),
      getBlockTypes = _select3.getBlockTypes;

  return {
    showInserterHelpPanel: isFeatureActive('showInserterHelpPanel'),
    hasFixedToolbar: isFeatureActive('fixedToolbar'),
    focusMode: isFeatureActive('focusMode'),
    post: getEntityRecord('postType', postType, postId),
    preferredStyleVariations: getPreference('preferredStyleVariations'),
    hiddenBlockTypes: getPreference('hiddenBlockTypes'),
    blockTypes: getBlockTypes(),
    __experimentalLocalAutosaveInterval: getPreference('localAutosaveInterval')
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/edit-post'),
      updatePreferredStyleVariations = _dispatch.updatePreferredStyleVariations;

  return {
    updatePreferredStyleVariations: updatePreferredStyleVariations
  };
})])(editor_Editor));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-item.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



var plugin_block_settings_menu_item_isEverySelectedBlockAllowed = function isEverySelectedBlockAllowed(selected, allowed) {
  return Object(external_this_lodash_["difference"])(selected, allowed).length === 0;
};
/**
 * Plugins may want to add an item to the menu either for every block
 * or only for the specific ones provided in the `allowedBlocks` component property.
 *
 * If there are multiple blocks selected the item will be rendered if every block
 * is of one allowed type (not necessarily the same).
 *
 * @param {string[]} selectedBlocks Array containing the names of the blocks selected
 * @param {string[]} allowedBlocks Array containing the names of the blocks allowed
 * @return {boolean} Whether the item will be rendered or not.
 */


var shouldRenderItem = function shouldRenderItem(selectedBlocks, allowedBlocks) {
  return !Array.isArray(allowedBlocks) || plugin_block_settings_menu_item_isEverySelectedBlockAllowed(selectedBlocks, allowedBlocks);
};
/**
 * Renders a new item in the block settings menu.
 *
 * @param {Object} props Component props.
 * @param {Array} [props.allowedBlocks] An array containing a list of block names for which the item should be shown. If not present, it'll be rendered for any block. If multiple blocks are selected, it'll be shown if and only if all of them are in the whitelist.
 * @param {WPBlockTypeIconRender} [props.icon] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element.
 * @param {string} props.label The menu item text.
 * @param {Function} props.onClick Callback function to be executed when the user click the menu item.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginBlockSettingsMenuItem = wp.editPost.PluginBlockSettingsMenuItem;
 *
 * function doOnClick(){
 * 	// To be called when the user clicks the menu item.
 * }
 *
 * function MyPluginBlockSettingsMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginBlockSettingsMenuItem,
 * 		{
 * 			allowedBlocks: [ 'core/paragraph' ],
 * 			icon: 'dashicon-name',
 * 			label: __( 'Menu item text' ),
 * 			onClick: doOnClick,
 * 		}
 * 	);
 * }
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from wp.i18n;
 * import { PluginBlockSettingsMenuItem } from wp.editPost;
 *
 * const doOnClick = ( ) => {
 *     // To be called when the user clicks the menu item.
 * };
 *
 * const MyPluginBlockSettingsMenuItem = () => (
 *     <PluginBlockSettingsMenuItem
 * 		allowedBlocks=[ 'core/paragraph' ]
 * 		icon='dashicon-name'
 * 		label=__( 'Menu item text' )
 * 		onClick={ doOnClick } />
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


var plugin_block_settings_menu_item_PluginBlockSettingsMenuItem = function PluginBlockSettingsMenuItem(_ref) {
  var allowedBlocks = _ref.allowedBlocks,
      icon = _ref.icon,
      label = _ref.label,
      onClick = _ref.onClick,
      small = _ref.small,
      role = _ref.role;
  return Object(external_this_wp_element_["createElement"])(plugin_block_settings_menu_group, null, function (_ref2) {
    var selectedBlocks = _ref2.selectedBlocks,
        onClose = _ref2.onClose;

    if (!shouldRenderItem(selectedBlocks, allowedBlocks)) {
      return null;
    }

    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], {
      onClick: Object(external_this_wp_compose_["compose"])(onClick, onClose),
      icon: icon || 'admin-plugins',
      label: small ? label : undefined,
      role: role
    }, !small && label);
  });
};

/* harmony default export */ var plugin_block_settings_menu_item = (plugin_block_settings_menu_item_PluginBlockSettingsMenuItem);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-more-menu-item/index.js




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var plugin_more_menu_item_PluginMoreMenuItem = function PluginMoreMenuItem(_ref) {
  var _ref$onClick = _ref.onClick,
      onClick = _ref$onClick === void 0 ? external_this_lodash_["noop"] : _ref$onClick,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["onClick"]);

  return Object(external_this_wp_element_["createElement"])(plugins_more_menu_group, null, function (fillProps) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["MenuItem"], Object(esm_extends["a" /* default */])({}, props, {
      onClick: Object(external_this_wp_compose_["compose"])(onClick, fillProps.onClose)
    }));
  });
};
/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down, and can be used to as a button or link depending on the props provided.
 * The text within the component appears as the menu item label.
 *
 * @param {Object} props Component properties.
 * @param {string} [props.href] When `href` is provided then the menu item is represented as an anchor rather than button. It corresponds to the `href` attribute of the anchor.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered to the left of the menu item label.
 * @param {Function} [props.onClick=noop] The callback function to be executed when the user clicks the menu item.
 * @param {...*} [props.other] Any additional props are passed through to the underlying [MenuItem](/packages/components/src/menu-item/README.md) component.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginMoreMenuItem = wp.editPost.PluginMoreMenuItem;
 * var moreIcon = wp.element.createElement( 'svg' ); //... svg element.
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * function MyButtonMoreMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginMoreMenuItem,
 * 		{
 * 			icon: moreIcon,
 * 			onClick: onButtonClick,
 * 		},
 * 		__( 'My button title' )
 * 	);
 * }
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginMoreMenuItem } from '@wordpress/edit-post';
 * import { more } from '@wordpress/icons';
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * const MyButtonMoreMenuItem = () => (
 * 	<PluginMoreMenuItem
 * 		icon={ more }
 * 		onClick={ onButtonClick }
 * 	>
 * 		{ __( 'My button title' ) }
 * 	</PluginMoreMenuItem>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


/* harmony default export */ var plugin_more_menu_item = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon
  };
}))(plugin_more_menu_item_PluginMoreMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-sidebar/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





function PluginSidebar(props) {
  var children = props.children,
      className = props.className,
      icon = props.icon,
      isActive = props.isActive,
      _props$isPinnable = props.isPinnable,
      isPinnable = _props$isPinnable === void 0 ? true : _props$isPinnable,
      isPinned = props.isPinned,
      sidebarName = props.sidebarName,
      title = props.title,
      togglePin = props.togglePin,
      toggleSidebar = props.toggleSidebar;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, isPinnable && Object(external_this_wp_element_["createElement"])(pinned_plugins, null, isPinned && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    icon: icon,
    label: title,
    onClick: toggleSidebar,
    isPressed: isActive,
    "aria-expanded": isActive
  })), Object(external_this_wp_element_["createElement"])(sidebar, {
    name: sidebarName
  }, Object(external_this_wp_element_["createElement"])(sidebar_header, {
    closeLabel: Object(external_this_wp_i18n_["__"])('Close plugin')
  }, Object(external_this_wp_element_["createElement"])("strong", null, title), isPinnable && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    icon: isPinned ? 'star-filled' : 'star-empty',
    label: isPinned ? Object(external_this_wp_i18n_["__"])('Unpin from toolbar') : Object(external_this_wp_i18n_["__"])('Pin to toolbar'),
    onClick: togglePin,
    isPressed: isPinned,
    "aria-expanded": isPinned
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Panel"], {
    className: className
  }, children)));
}
/**
 * Renders a sidebar when activated. The contents within the `PluginSidebar` will appear as content within the sidebar.
 * If you wish to display the sidebar, you can with use the `PluginSidebarMoreMenuItem` component or the `wp.data.dispatch` API:
 *
 * ```js
 * wp.data.dispatch( 'core/edit-post' ).openGeneralSidebar( 'plugin-name/sidebar-name' );
 * ```
 *
 * @see PluginSidebarMoreMenuItem
 *
 * @param {Object} props Element props.
 * @param {string} props.name A string identifying the sidebar. Must be unique for every sidebar registered within the scope of your plugin.
 * @param {string} [props.className] An optional class name added to the sidebar body.
 * @param {string} props.title Title displayed at the top of the sidebar.
 * @param {boolean} [props.isPinnable=true] Whether to allow to pin sidebar to toolbar.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var el = wp.element.createElement;
 * var PanelBody = wp.components.PanelBody;
 * var PluginSidebar = wp.editPost.PluginSidebar;
 * var moreIcon = wp.element.createElement( 'svg' ); //... svg element.
 *
 * function MyPluginSidebar() {
 * 	return el(
 * 			PluginSidebar,
 * 			{
 * 				name: 'my-sidebar',
 * 				title: 'My sidebar title',
 * 				icon: moreIcon,
 * 			},
 * 			el(
 * 				PanelBody,
 * 				{},
 * 				__( 'My sidebar content' )
 * 			)
 * 	);
 * }
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PanelBody } from '@wordpress/components';
 * import { PluginSidebar } from '@wordpress/edit-post';
 * import { more } from '@wordpress/icons';
 *
 * const MyPluginSidebar = () => (
 * 	<PluginSidebar
 * 		name="my-sidebar"
 * 		title="My sidebar title"
 * 		icon={ more }
 * 	>
 * 		<PanelBody>
 * 			{ __( 'My sidebar content' ) }
 * 		</PanelBody>
 * 	</PluginSidebar>
 * );
 * ```
 *
 * @return {WPComponent} Plugin sidebar component.
 */


/* harmony default export */ var plugin_sidebar = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon,
    sidebarName: "".concat(context.name, "/").concat(ownProps.name)
  };
}), Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
  var sidebarName = _ref.sidebarName;

  var _select = select('core/edit-post'),
      getActiveGeneralSidebarName = _select.getActiveGeneralSidebarName,
      isPluginItemPinned = _select.isPluginItemPinned;

  return {
    isActive: getActiveGeneralSidebarName() === sidebarName,
    isPinned: isPluginItemPinned(sidebarName)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref2) {
  var isActive = _ref2.isActive,
      sidebarName = _ref2.sidebarName;

  var _dispatch = dispatch('core/edit-post'),
      closeGeneralSidebar = _dispatch.closeGeneralSidebar,
      openGeneralSidebar = _dispatch.openGeneralSidebar,
      togglePinnedPluginItem = _dispatch.togglePinnedPluginItem;

  return {
    togglePin: function togglePin() {
      togglePinnedPluginItem(sidebarName);
    },
    toggleSidebar: function toggleSidebar() {
      if (isActive) {
        closeGeneralSidebar();
      } else {
        openGeneralSidebar(sidebarName);
      }
    }
  };
}))(PluginSidebar));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-sidebar-more-menu-item/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var plugin_sidebar_more_menu_item_PluginSidebarMoreMenuItem = function PluginSidebarMoreMenuItem(_ref) {
  var children = _ref.children,
      icon = _ref.icon,
      isSelected = _ref.isSelected,
      onClick = _ref.onClick;
  return Object(external_this_wp_element_["createElement"])(plugin_more_menu_item, {
    icon: isSelected ? check["a" /* default */] : icon,
    isSelected: isSelected,
    role: "menuitemcheckbox",
    onClick: onClick
  }, children);
};
/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down,
 * and can be used to activate the corresponding `PluginSidebar` component.
 * The text within the component appears as the menu item label.
 *
 * @param {Object} props Component props.
 * @param {string} props.target A string identifying the target sidebar you wish to be activated by this menu item. Must be the same as the `name` prop you have given to that sidebar.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered to the left of the menu item label.
 *
 * @example <caption>ES5</caption>
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
 * var moreIcon = wp.element.createElement( 'svg' ); //... svg element.
 *
 * function MySidebarMoreMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginSidebarMoreMenuItem,
 * 		{
 * 			target: 'my-sidebar',
 * 			icon: moreIcon,
 * 		},
 * 		__( 'My sidebar title' )
 * 	)
 * }
 * ```
 *
 * @example <caption>ESNext</caption>
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
 * import { more } from '@wordpress/icons';
 *
 * const MySidebarMoreMenuItem = () => (
 * 	<PluginSidebarMoreMenuItem
 * 		target="my-sidebar"
 * 		icon={ more }
 * 	>
 * 		{ __( 'My sidebar title' ) }
 * 	</PluginSidebarMoreMenuItem>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


/* harmony default export */ var plugin_sidebar_more_menu_item = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_plugins_["withPluginContext"])(function (context, ownProps) {
  return {
    icon: ownProps.icon || context.icon,
    sidebarName: "".concat(context.name, "/").concat(ownProps.target)
  };
}), Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var sidebarName = _ref2.sidebarName;

  var _select = select('core/edit-post'),
      getActiveGeneralSidebarName = _select.getActiveGeneralSidebarName;

  return {
    isSelected: getActiveGeneralSidebarName() === sidebarName
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3) {
  var isSelected = _ref3.isSelected,
      sidebarName = _ref3.sidebarName;

  var _dispatch = dispatch('core/edit-post'),
      closeGeneralSidebar = _dispatch.closeGeneralSidebar,
      openGeneralSidebar = _dispatch.openGeneralSidebar;

  var onClick = isSelected ? closeGeneralSidebar : function () {
    return openGeneralSidebar(sidebarName);
  };
  return {
    onClick: onClick
  };
}))(plugin_sidebar_more_menu_item_PluginSidebarMoreMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/index.js


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */





/**
 * Reinitializes the editor after the user chooses to reboot the editor after
 * an unhandled error occurs, replacing previously mounted editor element using
 * an initial state from prior to the crash.
 *
 * @param {Object}  postType     Post type of the post to edit.
 * @param {Object}  postId       ID of the post to edit.
 * @param {Element} target       DOM node in which editor is rendered.
 * @param {?Object} settings     Editor settings object.
 * @param {Object}  initialEdits Programmatic edits to apply initially, to be
 *                               considered as non-user-initiated (bypass for
 *                               unsaved changes prompt).
 */

function reinitializeEditor(postType, postId, target, settings, initialEdits) {
  Object(external_this_wp_element_["unmountComponentAtNode"])(target);
  var reboot = reinitializeEditor.bind(null, postType, postId, target, settings, initialEdits);
  Object(external_this_wp_element_["render"])(Object(external_this_wp_element_["createElement"])(editor, {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits,
    recovery: true
  }), target);
}
/**
 * Initializes and returns an instance of Editor.
 *
 * The return value of this function is not necessary if we change where we
 * call initializeEditor(). This is due to metaBox timing.
 *
 * @param {string}  id           Unique identifier for editor instance.
 * @param {Object}  postType     Post type of the post to edit.
 * @param {Object}  postId       ID of the post to edit.
 * @param {?Object} settings     Editor settings object.
 * @param {Object}  initialEdits Programmatic edits to apply initially, to be
 *                               considered as non-user-initiated (bypass for
 *                               unsaved changes prompt).
 */

function initializeEditor(id, postType, postId, settings, initialEdits) {
  var target = document.getElementById(id);
  var reboot = reinitializeEditor.bind(null, postType, postId, target, settings, initialEdits);
  Object(external_this_wp_blockLibrary_["registerCoreBlocks"])();

  if (false) {} // Show a console log warning if the browser is not in Standards rendering mode.


  var documentMode = document.compatMode === 'CSS1Compat' ? 'Standards' : 'Quirks';

  if (documentMode !== 'Standards') {
    // eslint-disable-next-line no-console
    console.warn("Your browser is using Quirks Mode. \nThis can cause rendering issues such as blocks overlaying meta boxes in the editor. Quirks Mode can be triggered by PHP errors or HTML code appearing before the opening <!DOCTYPE html>. Try checking the raw page source or your site's PHP error log and resolving errors there, removing any HTML before the doctype, or disabling plugins.");
  } // This is a temporary fix for a couple of issues specific to Webkit on iOS.
  // Without this hack the browser scrolls the mobile toolbar off-screen.
  // Once supported in Safari we can replace this in favor of preventScroll.
  // For details see issue #18632 and PR #18686
  // Specifically, we scroll `block-editor-editor-skeleton__body` to enable a fixed top toolbar.
  // But Mobile Safari forces the `html` element to scroll upwards, hiding the toolbar.


  var isIphone = window.navigator.userAgent.indexOf('iPhone') !== -1;

  if (isIphone) {
    window.addEventListener('scroll', function (event) {
      var editorScrollContainer = document.getElementsByClassName('block-editor-editor-skeleton__body')[0];

      if (event.target === document) {
        // Scroll element into view by scrolling the editor container by the same amount
        // that Mobile Safari tried to scroll the html element upwards.
        if (window.scrollY > 100) {
          editorScrollContainer.scrollTop = editorScrollContainer.scrollTop + window.scrollY;
        } //Undo unwanted scroll on html element


        window.scrollTo(0, 0);
      }
    });
  }

  Object(external_this_wp_element_["render"])(Object(external_this_wp_element_["createElement"])(editor, {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits
  }), target);
}










/***/ }),

/***/ 43:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectWithoutPropertiesLoose; });
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

/***/ }),

/***/ 46:
/***/ (function(module, exports, __webpack_require__) {

/**
 * Memize options object.
 *
 * @typedef MemizeOptions
 *
 * @property {number} [maxSize] Maximum size of the cache.
 */

/**
 * Internal cache entry.
 *
 * @typedef MemizeCacheNode
 *
 * @property {?MemizeCacheNode|undefined} [prev] Previous node.
 * @property {?MemizeCacheNode|undefined} [next] Next node.
 * @property {Array<*>}                   args   Function arguments for cache
 *                                               entry.
 * @property {*}                          val    Function result.
 */

/**
 * Properties of the enhanced function for controlling cache.
 *
 * @typedef MemizeMemoizedFunction
 *
 * @property {()=>void} clear Clear the cache.
 */

/**
 * Accepts a function to be memoized, and returns a new memoized function, with
 * optional options.
 *
 * @template {Function} F
 *
 * @param {F}             fn        Function to memoize.
 * @param {MemizeOptions} [options] Options object.
 *
 * @return {F & MemizeMemoizedFunction} Memoized function.
 */
function memize( fn, options ) {
	var size = 0;

	/** @type {?MemizeCacheNode|undefined} */
	var head;

	/** @type {?MemizeCacheNode|undefined} */
	var tail;

	options = options || {};

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
				/** @type {MemizeCacheNode} */ ( node.prev ).next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				/** @type {MemizeCacheNode} */ ( head ).prev = node;
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
			val: fn.apply( null, args ),
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
		if ( size === /** @type {MemizeOptions} */ ( options ).maxSize ) {
			tail = /** @type {MemizeCacheNode} */ ( tail ).prev;
			/** @type {MemizeCacheNode} */ ( tail ).next = null;
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

	// Ignore reason: There's not a clear solution to create an intersection of
	// the function with additional properties, where the goal is to retain the
	// function signature of the incoming argument and add control properties
	// on the return value.

	// @ts-ignore
	return memoized;
}

module.exports = memize;


/***/ }),

/***/ 48:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keyboardShortcuts"]; }());

/***/ }),

/***/ 5:
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

/***/ 50:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["a11y"]; }());

/***/ }),

/***/ 55:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["plugins"]; }());

/***/ }),

/***/ 56:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["viewport"]; }());

/***/ }),

/***/ 6:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockEditor"]; }());

/***/ }),

/***/ 7:
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

/***/ 75:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["coreData"]; }());

/***/ }),

/***/ 8:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ 89:
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

/***/ 9:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["primitives"]; }());

/***/ })

/******/ });