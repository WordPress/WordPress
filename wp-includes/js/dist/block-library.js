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
/******/ 	return __webpack_require__(__webpack_require__.s = 256);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),
/* 1 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),
/* 2 */
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),
/* 3 */
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
/* 4 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),
/* 5 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),
/* 6 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),
/* 7 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(15);

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
/* 8 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockEditor"]; }());

/***/ }),
/* 9 */
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
/* 10 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),
/* 11 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(32);
/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(3);


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),
/* 12 */
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
/* 13 */
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
/* 14 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),
/* 15 */
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
/* 16 */
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
/* 17 */
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
var iterableToArray = __webpack_require__(34);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),
/* 18 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),
/* 19 */
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
/* 20 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["richText"]; }());

/***/ }),
/* 21 */
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
/* 22 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["editor"]; }());

/***/ }),
/* 23 */,
/* 24 */,
/* 25 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),
/* 26 */,
/* 27 */,
/* 28 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _slicedToArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__(37);

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
var nonIterableRest = __webpack_require__(38);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js



function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),
/* 29 */
/***/ (function(module, exports) {

(function() { module.exports = this["moment"]; }());

/***/ }),
/* 30 */,
/* 31 */,
/* 32 */
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
/* 33 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),
/* 34 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

/***/ }),
/* 35 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blob"]; }());

/***/ }),
/* 36 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return embedContentIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return embedAudioIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "g", function() { return embedPhotoIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "l", function() { return embedVideoIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "k", function() { return embedTwitterIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "o", function() { return embedYouTubeIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return embedFacebookIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "f", function() { return embedInstagramIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "n", function() { return embedWordPressIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "i", function() { return embedSpotifyIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return embedFlickrIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "m", function() { return embedVimeoIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "h", function() { return embedRedditIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "j", function() { return embedTumbrIcon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return embedAmazonIcon; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

var embedContentIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19,4H5C3.89,4,3,4.9,3,6v12c0,1.1,0.89,2,2,2h14c1.1,0,2-0.9,2-2V6C21,4.9,20.11,4,19,4z M19,18H5V8h14V18z"
}));
var embedAudioIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM8 15c0-1.66 1.34-3 3-3 .35 0 .69.07 1 .18V6h5v2h-3v7.03c-.02 1.64-1.35 2.97-3 2.97-1.66 0-3-1.34-3-3z"
}));
var embedPhotoIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M21,4H3C1.9,4,1,4.9,1,6v12c0,1.1,0.9,2,2,2h18c1.1,0,2-0.9,2-2V6C23,4.9,22.1,4,21,4z M21,18H3V6h18V18z"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Polygon"], {
  points: "14.5 11 11 15.51 8.5 12.5 5 17 19 17"
}));
var embedVideoIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "m10 8v8l5-4-5-4zm9-5h-14c-1.1 0-2 0.9-2 2v14c0 1.1 0.9 2 2 2h14c1.1 0 2-0.9 2-2v-14c0-1.1-0.9-2-2-2zm0 16h-14v-14h14v14z"
}));
var embedTwitterIcon = {
  foreground: '#1da1f2',
  src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["G"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
    d: "M22.23 5.924c-.736.326-1.527.547-2.357.646.847-.508 1.498-1.312 1.804-2.27-.793.47-1.67.812-2.606.996C18.325 4.498 17.258 4 16.078 4c-2.266 0-4.103 1.837-4.103 4.103 0 .322.036.635.106.935-3.41-.17-6.433-1.804-8.457-4.287-.353.607-.556 1.312-.556 2.064 0 1.424.724 2.68 1.825 3.415-.673-.022-1.305-.207-1.86-.514v.052c0 1.988 1.415 3.647 3.293 4.023-.344.095-.707.145-1.08.145-.265 0-.522-.026-.773-.074.522 1.63 2.038 2.817 3.833 2.85-1.404 1.1-3.174 1.757-5.096 1.757-.332 0-.66-.02-.98-.057 1.816 1.164 3.973 1.843 6.29 1.843 7.547 0 11.675-6.252 11.675-11.675 0-.178-.004-.355-.012-.53.802-.578 1.497-1.3 2.047-2.124z"
  })))
};
var embedYouTubeIcon = {
  foreground: '#ff0000',
  src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
    d: "M21.8 8s-.195-1.377-.795-1.984c-.76-.797-1.613-.8-2.004-.847-2.798-.203-6.996-.203-6.996-.203h-.01s-4.197 0-6.996.202c-.39.046-1.242.05-2.003.846C2.395 6.623 2.2 8 2.2 8S2 9.62 2 11.24v1.517c0 1.618.2 3.237.2 3.237s.195 1.378.795 1.985c.76.797 1.76.77 2.205.855 1.6.153 6.8.2 6.8.2s4.203-.005 7-.208c.392-.047 1.244-.05 2.005-.847.6-.607.795-1.985.795-1.985s.2-1.618.2-3.237v-1.517C22 9.62 21.8 8 21.8 8zM9.935 14.595v-5.62l5.403 2.82-5.403 2.8z"
  }))
};
var embedFacebookIcon = {
  foreground: '#3b5998',
  src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
    d: "M20 3H4c-.6 0-1 .4-1 1v16c0 .5.4 1 1 1h8.6v-7h-2.3v-2.7h2.3v-2c0-2.3 1.4-3.6 3.5-3.6 1 0 1.8.1 2.1.1v2.4h-1.4c-1.1 0-1.3.5-1.3 1.3v1.7h2.7l-.4 2.8h-2.3v7H20c.5 0 1-.4 1-1V4c0-.6-.4-1-1-1z"
  }))
};
var embedInstagramIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["G"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12 4.622c2.403 0 2.688.01 3.637.052.877.04 1.354.187 1.67.31.42.163.72.358 1.036.673.315.315.51.615.673 1.035.123.317.27.794.31 1.67.043.95.052 1.235.052 3.638s-.01 2.688-.052 3.637c-.04.877-.187 1.354-.31 1.67-.163.42-.358.72-.673 1.036-.315.315-.615.51-1.035.673-.317.123-.794.27-1.67.31-.95.043-1.234.052-3.638.052s-2.688-.01-3.637-.052c-.877-.04-1.354-.187-1.67-.31-.42-.163-.72-.358-1.036-.673-.315-.315-.51-.615-.673-1.035-.123-.317-.27-.794-.31-1.67-.043-.95-.052-1.235-.052-3.638s.01-2.688.052-3.637c.04-.877.187-1.354.31-1.67.163-.42.358-.72.673-1.036.315-.315.615-.51 1.035-.673.317-.123.794-.27 1.67-.31.95-.043 1.235-.052 3.638-.052M12 3c-2.444 0-2.75.01-3.71.054s-1.613.196-2.185.418c-.592.23-1.094.538-1.594 1.04-.5.5-.807 1-1.037 1.593-.223.572-.375 1.226-.42 2.184C3.01 9.25 3 9.555 3 12s.01 2.75.054 3.71.196 1.613.418 2.186c.23.592.538 1.094 1.038 1.594s1.002.808 1.594 1.038c.572.222 1.227.375 2.185.418.96.044 1.266.054 3.71.054s2.75-.01 3.71-.054 1.613-.196 2.186-.418c.592-.23 1.094-.538 1.594-1.038s.808-1.002 1.038-1.594c.222-.572.375-1.227.418-2.185.044-.96.054-1.266.054-3.71s-.01-2.75-.054-3.71-.196-1.613-.418-2.186c-.23-.592-.538-1.094-1.038-1.594s-1.002-.808-1.594-1.038c-.572-.222-1.227-.375-2.185-.418C14.75 3.01 14.445 3 12 3zm0 4.378c-2.552 0-4.622 2.07-4.622 4.622s2.07 4.622 4.622 4.622 4.622-2.07 4.622-4.622S14.552 7.378 12 7.378zM12 15c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3zm4.804-8.884c-.596 0-1.08.484-1.08 1.08s.484 1.08 1.08 1.08c.596 0 1.08-.484 1.08-1.08s-.483-1.08-1.08-1.08z"
})));
var embedWordPressIcon = {
  foreground: '#0073AA',
  src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["G"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
    d: "M12.158 12.786l-2.698 7.84c.806.236 1.657.365 2.54.365 1.047 0 2.05-.18 2.986-.51-.024-.037-.046-.078-.065-.123l-2.762-7.57zM3.008 12c0 3.56 2.07 6.634 5.068 8.092L3.788 8.342c-.5 1.117-.78 2.354-.78 3.658zm15.06-.454c0-1.112-.398-1.88-.74-2.48-.456-.74-.883-1.368-.883-2.11 0-.825.627-1.595 1.51-1.595.04 0 .078.006.116.008-1.598-1.464-3.73-2.36-6.07-2.36-3.14 0-5.904 1.613-7.512 4.053.21.008.41.012.58.012.94 0 2.395-.114 2.395-.114.484-.028.54.684.057.74 0 0-.487.058-1.03.086l3.275 9.74 1.968-5.902-1.4-3.838c-.485-.028-.944-.085-.944-.085-.486-.03-.43-.77.056-.742 0 0 1.484.114 2.368.114.94 0 2.397-.114 2.397-.114.486-.028.543.684.058.74 0 0-.488.058-1.03.086l3.25 9.665.897-2.997c.456-1.17.684-2.137.684-2.907zm1.82-3.86c.04.286.06.593.06.924 0 .912-.17 1.938-.683 3.22l-2.746 7.94c2.672-1.558 4.47-4.454 4.47-7.77 0-1.564-.4-3.033-1.1-4.314zM12 22C6.486 22 2 17.514 2 12S6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"
  })))
};
var embedSpotifyIcon = {
  foreground: '#1db954',
  src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
    d: "M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2m4.586 14.424c-.18.295-.563.387-.857.207-2.35-1.434-5.305-1.76-8.786-.963-.335.077-.67-.133-.746-.47-.077-.334.132-.67.47-.745 3.808-.87 7.076-.496 9.712 1.115.293.18.386.563.206.857M17.81 13.7c-.226.367-.706.482-1.072.257-2.687-1.652-6.785-2.13-9.965-1.166-.413.127-.848-.106-.973-.517-.125-.413.108-.848.52-.973 3.632-1.102 8.147-.568 11.234 1.328.366.226.48.707.256 1.072m.105-2.835C14.692 8.95 9.375 8.775 6.297 9.71c-.493.15-1.016-.13-1.166-.624-.148-.495.13-1.017.625-1.167 3.532-1.073 9.404-.866 13.115 1.337.445.264.59.838.327 1.282-.264.443-.838.59-1.282.325"
  }))
};
var embedFlickrIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "m6.5 7c-2.75 0-5 2.25-5 5s2.25 5 5 5 5-2.25 5-5-2.25-5-5-5zm11 0c-2.75 0-5 2.25-5 5s2.25 5 5 5 5-2.25 5-5-2.25-5-5-5z"
}));
var embedVimeoIcon = {
  foreground: '#1ab7ea',
  src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["G"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
    d: "M22.396 7.164c-.093 2.026-1.507 4.8-4.245 8.32C15.323 19.16 12.93 21 10.97 21c-1.214 0-2.24-1.12-3.08-3.36-.56-2.052-1.118-4.105-1.68-6.158-.622-2.24-1.29-3.36-2.004-3.36-.156 0-.7.328-1.634.98l-.978-1.26c1.027-.903 2.04-1.806 3.037-2.71C6 3.95 7.03 3.328 7.716 3.265c1.62-.156 2.616.95 2.99 3.32.404 2.558.685 4.148.84 4.77.468 2.12.982 3.18 1.543 3.18.435 0 1.09-.687 1.963-2.064.872-1.376 1.34-2.422 1.402-3.142.125-1.187-.343-1.782-1.4-1.782-.5 0-1.013.115-1.542.34 1.023-3.35 2.977-4.976 5.862-4.883 2.14.063 3.148 1.45 3.024 4.16z"
  })))
};
var embedRedditIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M22 11.816c0-1.256-1.02-2.277-2.277-2.277-.593 0-1.122.24-1.526.613-1.48-.965-3.455-1.594-5.647-1.69l1.17-3.702 3.18.75c.01 1.027.847 1.86 1.877 1.86 1.035 0 1.877-.84 1.877-1.877 0-1.035-.842-1.877-1.877-1.877-.77 0-1.43.466-1.72 1.13L13.55 3.92c-.204-.047-.4.067-.46.26l-1.35 4.27c-2.317.037-4.412.67-5.97 1.67-.402-.355-.917-.58-1.493-.58C3.02 9.54 2 10.56 2 11.815c0 .814.433 1.523 1.078 1.925-.037.222-.06.445-.06.673 0 3.292 4.01 5.97 8.94 5.97s8.94-2.678 8.94-5.97c0-.214-.02-.424-.052-.632.687-.39 1.154-1.12 1.154-1.964zm-3.224-7.422c.606 0 1.1.493 1.1 1.1s-.493 1.1-1.1 1.1-1.1-.494-1.1-1.1.493-1.1 1.1-1.1zm-16 7.422c0-.827.673-1.5 1.5-1.5.313 0 .598.103.838.27-.85.675-1.477 1.478-1.812 2.36-.32-.274-.525-.676-.525-1.13zm9.183 7.79c-4.502 0-8.165-2.33-8.165-5.193S7.457 9.22 11.96 9.22s8.163 2.33 8.163 5.193-3.663 5.193-8.164 5.193zM20.635 13c-.326-.89-.948-1.7-1.797-2.383.247-.186.55-.3.882-.3.827 0 1.5.672 1.5 1.5 0 .482-.23.91-.586 1.184zm-11.64 1.704c-.76 0-1.397-.616-1.397-1.376 0-.76.636-1.397 1.396-1.397.76 0 1.376.638 1.376 1.398 0 .76-.616 1.376-1.376 1.376zm7.405-1.376c0 .76-.615 1.376-1.375 1.376s-1.4-.616-1.4-1.376c0-.76.64-1.397 1.4-1.397.76 0 1.376.638 1.376 1.398zm-1.17 3.38c.15.152.15.398 0 .55-.675.674-1.728 1.002-3.22 1.002l-.01-.002-.012.002c-1.492 0-2.544-.328-3.218-1.002-.152-.152-.152-.398 0-.55.152-.152.4-.15.55 0 .52.52 1.394.775 2.67.775l.01.002.01-.002c1.276 0 2.15-.253 2.67-.775.15-.152.398-.152.55 0z"
}));
var embedTumbrIcon = {
  foreground: '#35465c',
  src: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
    d: "M19 3H5c-1.105 0-2 .895-2 2v14c0 1.105.895 2 2 2h14c1.105 0 2-.895 2-2V5c0-1.105-.895-2-2-2zm-5.57 14.265c-2.445.042-3.37-1.742-3.37-2.998V10.6H8.922V9.15c1.703-.615 2.113-2.15 2.21-3.026.006-.06.053-.084.08-.084h1.645V8.9h2.246v1.7H12.85v3.495c.008.476.182 1.13 1.08 1.107.3-.008.698-.094.907-.194l.54 1.6c-.205.297-1.12.642-1.946.657z"
  }))
};
var embedAmazonIcon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.42 14.58c-.51-.66-1.05-1.23-1.05-2.5V7.87c0-1.8.15-3.45-1.2-4.68-1.05-1.02-2.79-1.35-4.14-1.35-2.6 0-5.52.96-6.12 4.14-.06.36.18.54.4.57l2.66.3c.24-.03.42-.27.48-.5.24-1.12 1.17-1.63 2.2-1.63.56 0 1.22.21 1.55.7.4.56.33 1.31.33 1.97v.36c-1.59.18-3.66.27-5.16.93a4.63 4.63 0 0 0-2.93 4.44c0 2.82 1.8 4.23 4.1 4.23 1.95 0 3.03-.45 4.53-1.98.51.72.66 1.08 1.59 1.83.18.09.45.09.63-.1v.04l2.1-1.8c.24-.21.2-.48.03-.75zm-5.4-1.2c-.45.75-1.14 1.23-1.92 1.23-1.05 0-1.65-.81-1.65-1.98 0-2.31 2.1-2.73 4.08-2.73v.6c0 1.05.03 1.92-.5 2.88z"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M21.69 19.2a17.62 17.62 0 0 1-21.6-1.57c-.23-.2 0-.5.28-.33a23.88 23.88 0 0 0 20.93 1.3c.45-.19.84.3.39.6z"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M22.8 17.96c-.36-.45-2.22-.2-3.1-.12-.23.03-.3-.18-.05-.36 1.5-1.05 3.96-.75 4.26-.39.3.36-.1 2.82-1.5 4.02-.21.18-.42.1-.3-.15.3-.8 1.02-2.58.69-3z"
}));


/***/ }),
/* 37 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),
/* 38 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

/***/ }),
/* 39 */,
/* 40 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["viewport"]; }());

/***/ }),
/* 41 */
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
/* 42 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["isShallowEqual"]; }());

/***/ }),
/* 43 */,
/* 44 */,
/* 45 */
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
/* 46 */,
/* 47 */,
/* 48 */,
/* 49 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["deprecated"]; }());

/***/ }),
/* 50 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["date"]; }());

/***/ }),
/* 51 */,
/* 52 */,
/* 53 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export matchesPatterns */
/* unused harmony export findBlock */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return isFromWordPress; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return getPhotoHtml; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return createUpgradedEmbedBlock; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return getClassNames; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return fallback; });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(15);
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(7);
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(17);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _core_embeds__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(85);
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(58);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(2);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var classnames_dedupe__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(68);
/* harmony import */ var classnames_dedupe__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(classnames_dedupe__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__);





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

var findBlock = function findBlock(url) {
  var _arr = [].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])(_core_embeds__WEBPACK_IMPORTED_MODULE_4__[/* common */ "a"]), Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])(_core_embeds__WEBPACK_IMPORTED_MODULE_4__[/* others */ "b"]));

  for (var _i = 0; _i < _arr.length; _i++) {
    var block = _arr[_i];

    if (matchesPatterns(url, block.patterns)) {
      return block.name;
    }
  }

  return _constants__WEBPACK_IMPORTED_MODULE_5__[/* DEFAULT_EMBED_BLOCK */ "b"];
};
var isFromWordPress = function isFromWordPress(html) {
  return Object(lodash__WEBPACK_IMPORTED_MODULE_6__["includes"])(html, 'class="wp-embedded-content"');
};
var getPhotoHtml = function getPhotoHtml(photo) {
  // 100% width for the preview so it fits nicely into the document, some "thumbnails" are
  // actually the full size photo. If thumbnails not found, use full image.
  var imageUrl = photo.thumbnail_url ? photo.thumbnail_url : photo.url;
  var photoPreview = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("p", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("img", {
    src: imageUrl,
    alt: photo.title,
    width: "100%"
  }));
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["renderToString"])(photoPreview);
};
/***
 * Creates a more suitable embed block based on the passed in props
 * and attributes generated from an embed block's preview.
 *
 * We require `attributesFromPreview` to be generated from the latest attributes
 * and preview, and because of the way the react lifecycle operates, we can't
 * guarantee that the attributes contained in the block's props are the latest
 * versions, so we require that these are generated separately.
 * See `getAttributesFromPreview` in the generated embed edit component.
 *
 * @param {Object}            props                 The block's props.
 * @param {Object}            attributesFromPreview Attributes generated from the block's most up to date preview.
 * @return {Object|undefined} A more suitable embed block if one exists.
 */

var createUpgradedEmbedBlock = function createUpgradedEmbedBlock(props, attributesFromPreview) {
  var preview = props.preview,
      name = props.name;
  var url = props.attributes.url;

  if (!url) {
    return;
  }

  var matchingBlock = findBlock(url); // WordPress blocks can work on multiple sites, and so don't have patterns,
  // so if we're in a WordPress block, assume the user has chosen it for a WordPress URL.

  if (_constants__WEBPACK_IMPORTED_MODULE_5__[/* WORDPRESS_EMBED_BLOCK */ "d"] !== name && _constants__WEBPACK_IMPORTED_MODULE_5__[/* DEFAULT_EMBED_BLOCK */ "b"] !== matchingBlock) {
    // At this point, we have discovered a more suitable block for this url, so transform it.
    if (name !== matchingBlock) {
      return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__["createBlock"])(matchingBlock, {
        url: url
      });
    }
  }

  if (preview) {
    var html = preview.html; // We can't match the URL for WordPress embeds, we have to check the HTML instead.

    if (isFromWordPress(html)) {
      // If this is not the WordPress embed block, transform it into one.
      if (_constants__WEBPACK_IMPORTED_MODULE_5__[/* WORDPRESS_EMBED_BLOCK */ "d"] !== name) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__["createBlock"])(_constants__WEBPACK_IMPORTED_MODULE_5__[/* WORDPRESS_EMBED_BLOCK */ "d"], Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])({
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

    for (var ratioIndex = 0; ratioIndex < _constants__WEBPACK_IMPORTED_MODULE_5__[/* ASPECT_RATIOS */ "a"].length; ratioIndex++) {
      var aspectRatioToRemove = _constants__WEBPACK_IMPORTED_MODULE_5__[/* ASPECT_RATIOS */ "a"][ratioIndex];
      aspectRatioClassNames[aspectRatioToRemove.className] = false;
    }

    return classnames_dedupe__WEBPACK_IMPORTED_MODULE_7___default()(existingClassNames, aspectRatioClassNames);
  }

  var previewDocument = document.implementation.createHTMLDocument('');
  previewDocument.body.innerHTML = html;
  var iframe = previewDocument.body.querySelector('iframe'); // If we have a fixed aspect iframe, and it's a responsive embed block.

  if (iframe && iframe.height && iframe.width) {
    var aspectRatio = (iframe.width / iframe.height).toFixed(2); // Given the actual aspect ratio, find the widest ratio to support it.

    for (var _ratioIndex = 0; _ratioIndex < _constants__WEBPACK_IMPORTED_MODULE_5__[/* ASPECT_RATIOS */ "a"].length; _ratioIndex++) {
      var potentialRatio = _constants__WEBPACK_IMPORTED_MODULE_5__[/* ASPECT_RATIOS */ "a"][_ratioIndex];

      if (aspectRatio >= potentialRatio.ratio) {
        var _classnames;

        return classnames_dedupe__WEBPACK_IMPORTED_MODULE_7___default()(existingClassNames, (_classnames = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(_classnames, potentialRatio.className, allowResponsive), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(_classnames, 'wp-has-aspect-ratio', allowResponsive), _classnames));
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
 * @param {function} onReplace Function to call with the created fallback block.
 */

function fallback(url, onReplace) {
  var link = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])("a", {
    href: url
  }, url);
  onReplace(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__["createBlock"])('core/paragraph', {
    content: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["renderToString"])(link)
  }));
}


/***/ }),
/* 54 */,
/* 55 */,
/* 56 */,
/* 57 */,
/* 58 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return HOSTS_NO_PREVIEWS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ASPECT_RATIOS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return DEFAULT_EMBED_BLOCK; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return WORDPRESS_EMBED_BLOCK; });
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


/***/ }),
/* 59 */
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
/* 60 */,
/* 61 */,
/* 62 */,
/* 63 */,
/* 64 */,
/* 65 */,
/* 66 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["autop"]; }());

/***/ }),
/* 67 */,
/* 68 */
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
/* 69 */,
/* 70 */,
/* 71 */,
/* 72 */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["coreData"]; }());

/***/ }),
/* 73 */,
/* 74 */,
/* 75 */,
/* 76 */,
/* 77 */,
/* 78 */,
/* 79 */,
/* 80 */,
/* 81 */,
/* 82 */,
/* 83 */,
/* 84 */
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



var punycode = __webpack_require__(117);
var util = __webpack_require__(119);

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
    querystring = __webpack_require__(120);

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
/* 85 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return common; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return others; });
/* harmony import */ var _icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(36);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
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
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedTwitterIcon */ "k"],
    keywords: ['tweet'],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a tweet.')
  },
  patterns: [/^https?:\/\/(www\.)?twitter\.com\/.+/i]
}, {
  name: 'core-embed/youtube',
  settings: {
    title: 'YouTube',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedYouTubeIcon */ "o"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('music'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('video')],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a YouTube video.')
  },
  patterns: [/^https?:\/\/((m|www)\.)?youtube\.com\/.+/i, /^https?:\/\/youtu\.be\/.+/i]
}, {
  name: 'core-embed/facebook',
  settings: {
    title: 'Facebook',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedFacebookIcon */ "d"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a Facebook post.')
  },
  patterns: [/^https?:\/\/www\.facebook.com\/.+/i]
}, {
  name: 'core-embed/instagram',
  settings: {
    title: 'Instagram',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedInstagramIcon */ "f"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('image')],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed an Instagram post.')
  },
  patterns: [/^https?:\/\/(www\.)?instagr(\.am|am\.com)\/.+/i]
}, {
  name: 'core-embed/wordpress',
  settings: {
    title: 'WordPress',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedWordPressIcon */ "n"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('post'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('blog')],
    responsive: false,
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a WordPress post.')
  }
}, {
  name: 'core-embed/soundcloud',
  settings: {
    title: 'SoundCloud',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedAudioIcon */ "b"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('music'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('audio')],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed SoundCloud content.')
  },
  patterns: [/^https?:\/\/(www\.)?soundcloud\.com\/.+/i]
}, {
  name: 'core-embed/spotify',
  settings: {
    title: 'Spotify',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedSpotifyIcon */ "i"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('music'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('audio')],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Spotify content.')
  },
  patterns: [/^https?:\/\/(open|play)\.spotify\.com\/.+/i]
}, {
  name: 'core-embed/flickr',
  settings: {
    title: 'Flickr',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedFlickrIcon */ "e"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('image')],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Flickr content.')
  },
  patterns: [/^https?:\/\/(www\.)?flickr\.com\/.+/i, /^https?:\/\/flic\.kr\/.+/i]
}, {
  name: 'core-embed/vimeo',
  settings: {
    title: 'Vimeo',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedVimeoIcon */ "m"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('video')],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a Vimeo video.')
  },
  patterns: [/^https?:\/\/(www\.)?vimeo\.com\/.+/i]
}];
var others = [{
  name: 'core-embed/animoto',
  settings: {
    title: 'Animoto',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedVideoIcon */ "l"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed an Animoto video.')
  },
  patterns: [/^https?:\/\/(www\.)?(animoto|video214)\.com\/.+/i]
}, {
  name: 'core-embed/cloudup',
  settings: {
    title: 'Cloudup',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedContentIcon */ "c"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Cloudup content.')
  },
  patterns: [/^https?:\/\/cloudup\.com\/.+/i]
}, {
  name: 'core-embed/collegehumor',
  settings: {
    title: 'CollegeHumor',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedVideoIcon */ "l"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed CollegeHumor content.')
  },
  patterns: [/^https?:\/\/(www\.)?collegehumor\.com\/.+/i]
}, {
  name: 'core-embed/crowdsignal',
  settings: {
    title: 'Crowdsignal',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedContentIcon */ "c"],
    keywords: ['polldaddy'],
    transform: [{
      type: 'block',
      blocks: ['core-embed/polldaddy'],
      transform: function transform(content) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core-embed/crowdsignal', {
          content: content
        });
      }
    }],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Crowdsignal (formerly Polldaddy) content.')
  },
  patterns: [/^https?:\/\/((.+\.)?polldaddy\.com|poll\.fm|.+\.survey\.fm)\/.+/i]
}, {
  name: 'core-embed/dailymotion',
  settings: {
    title: 'Dailymotion',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedVideoIcon */ "l"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a Dailymotion video.')
  },
  patterns: [/^https?:\/\/(www\.)?dailymotion\.com\/.+/i]
}, {
  name: 'core-embed/hulu',
  settings: {
    title: 'Hulu',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedVideoIcon */ "l"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Hulu content.')
  },
  patterns: [/^https?:\/\/(www\.)?hulu\.com\/.+/i]
}, {
  name: 'core-embed/imgur',
  settings: {
    title: 'Imgur',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedPhotoIcon */ "g"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Imgur content.')
  },
  patterns: [/^https?:\/\/(.+\.)?imgur\.com\/.+/i]
}, {
  name: 'core-embed/issuu',
  settings: {
    title: 'Issuu',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedContentIcon */ "c"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Issuu content.')
  },
  patterns: [/^https?:\/\/(www\.)?issuu\.com\/.+/i]
}, {
  name: 'core-embed/kickstarter',
  settings: {
    title: 'Kickstarter',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedContentIcon */ "c"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Kickstarter content.')
  },
  patterns: [/^https?:\/\/(www\.)?kickstarter\.com\/.+/i, /^https?:\/\/kck\.st\/.+/i]
}, {
  name: 'core-embed/meetup-com',
  settings: {
    title: 'Meetup.com',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedContentIcon */ "c"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Meetup.com content.')
  },
  patterns: [/^https?:\/\/(www\.)?meetu(\.ps|p\.com)\/.+/i]
}, {
  name: 'core-embed/mixcloud',
  settings: {
    title: 'Mixcloud',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedAudioIcon */ "b"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('music'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('audio')],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Mixcloud content.')
  },
  patterns: [/^https?:\/\/(www\.)?mixcloud\.com\/.+/i]
}, {
  // Deprecated in favour of the core-embed/crowdsignal block
  name: 'core-embed/polldaddy',
  settings: {
    title: 'Polldaddy',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedContentIcon */ "c"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Polldaddy content.'),
    supports: {
      inserter: false
    }
  },
  patterns: []
}, {
  name: 'core-embed/reddit',
  settings: {
    title: 'Reddit',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedRedditIcon */ "h"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a Reddit thread.')
  },
  patterns: [/^https?:\/\/(www\.)?reddit\.com\/.+/i]
}, {
  name: 'core-embed/reverbnation',
  settings: {
    title: 'ReverbNation',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedAudioIcon */ "b"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed ReverbNation content.')
  },
  patterns: [/^https?:\/\/(www\.)?reverbnation\.com\/.+/i]
}, {
  name: 'core-embed/screencast',
  settings: {
    title: 'Screencast',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedVideoIcon */ "l"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Screencast content.')
  },
  patterns: [/^https?:\/\/(www\.)?screencast\.com\/.+/i]
}, {
  name: 'core-embed/scribd',
  settings: {
    title: 'Scribd',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedContentIcon */ "c"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Scribd content.')
  },
  patterns: [/^https?:\/\/(www\.)?scribd\.com\/.+/i]
}, {
  name: 'core-embed/slideshare',
  settings: {
    title: 'Slideshare',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedContentIcon */ "c"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Slideshare content.')
  },
  patterns: [/^https?:\/\/(.+?\.)?slideshare\.net\/.+/i]
}, {
  name: 'core-embed/smugmug',
  settings: {
    title: 'SmugMug',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedPhotoIcon */ "g"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed SmugMug content.')
  },
  patterns: [/^https?:\/\/(www\.)?smugmug\.com\/.+/i]
}, {
  // Deprecated in favour of the core-embed/speaker-deck block.
  name: 'core-embed/speaker',
  settings: {
    title: 'Speaker',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedAudioIcon */ "b"],
    supports: {
      inserter: false
    }
  },
  patterns: []
}, {
  name: 'core-embed/speaker-deck',
  settings: {
    title: 'Speaker Deck',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedContentIcon */ "c"],
    transform: [{
      type: 'block',
      blocks: ['core-embed/speaker'],
      transform: function transform(content) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core-embed/speaker-deck', {
          content: content
        });
      }
    }],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Speaker Deck content.')
  },
  patterns: [/^https?:\/\/(www\.)?speakerdeck\.com\/.+/i]
}, {
  name: 'core-embed/ted',
  settings: {
    title: 'TED',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedVideoIcon */ "l"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a TED video.')
  },
  patterns: [/^https?:\/\/(www\.|embed\.)?ted\.com\/.+/i]
}, {
  name: 'core-embed/tumblr',
  settings: {
    title: 'Tumblr',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedTumbrIcon */ "j"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a Tumblr post.')
  },
  patterns: [/^https?:\/\/(www\.)?tumblr\.com\/.+/i]
}, {
  name: 'core-embed/videopress',
  settings: {
    title: 'VideoPress',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedVideoIcon */ "l"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('video')],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a VideoPress video.')
  },
  patterns: [/^https?:\/\/videopress\.com\/.+/i]
}, {
  name: 'core-embed/wordpress-tv',
  settings: {
    title: 'WordPress.tv',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedVideoIcon */ "l"],
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed a WordPress.tv video.')
  },
  patterns: [/^https?:\/\/wordpress\.tv\/.+/i]
}, {
  name: 'core-embed/amazon-kindle',
  settings: {
    title: 'Amazon Kindle',
    icon: _icons__WEBPACK_IMPORTED_MODULE_0__[/* embedAmazonIcon */ "a"],
    keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('ebook')],
    responsive: false,
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Embed Amazon Kindle content.')
  },
  patterns: [/^https?:\/\/([a-z0-9-]+\.)?(amazon|amzn)(\.[a-z]{2,4})+\/.+/i, /^https?:\/\/(www\.)?(a\.co|z\.cn)\/.+/i]
}];


/***/ }),
/* 86 */,
/* 87 */,
/* 88 */,
/* 89 */,
/* 90 */,
/* 91 */,
/* 92 */,
/* 93 */,
/* 94 */,
/* 95 */,
/* 96 */,
/* 97 */,
/* 98 */,
/* 99 */,
/* 100 */,
/* 101 */,
/* 102 */,
/* 103 */,
/* 104 */,
/* 105 */,
/* 106 */,
/* 107 */,
/* 108 */,
/* 109 */,
/* 110 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ embed_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });
__webpack_require__.d(__webpack_exports__, "common", function() { return /* binding */ common; });
__webpack_require__.d(__webpack_exports__, "others", function() { return /* binding */ others; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: ./node_modules/@wordpress/block-library/build-module/embed/core-embeds.js
var core_embeds = __webpack_require__(85);

// EXTERNAL MODULE: ./node_modules/@wordpress/block-library/build-module/embed/icons.js
var icons = __webpack_require__(36);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: ./node_modules/@wordpress/block-library/build-module/embed/util.js
var util = __webpack_require__(53);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

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
    className: "wp-block-embed"
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
  }, Object(external_this_wp_i18n_["_x"])('Convert to link', 'button label')))));
};

/* harmony default export */ var embed_placeholder = (embed_placeholder_EmbedPlaceholder);

// EXTERNAL MODULE: ./node_modules/@wordpress/block-library/build-module/embed/constants.js
var constants = __webpack_require__(58);

// EXTERNAL MODULE: ./node_modules/url/url.js
var url_url = __webpack_require__(84);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: ./node_modules/classnames/dedupe.js
var dedupe = __webpack_require__(68);
var dedupe_default = /*#__PURE__*/__webpack_require__.n(dedupe);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(6);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/wp-embed-preview.js








/**
 * WordPress dependencies
 */


/**
 * Browser dependencies
 */

var _window = window,
    FocusEvent = _window.FocusEvent;

var wp_embed_preview_WpEmbedPreview =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(WpEmbedPreview, _Component);

  function WpEmbedPreview() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, WpEmbedPreview);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(WpEmbedPreview).apply(this, arguments));
    _this.checkFocus = _this.checkFocus.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
    _this.hideOverlay = _this.hideOverlay.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
      var html = 'photo' === type ? Object(util["d" /* getPhotoHtml */])(preview) : preview.html;
      var parsedHost = Object(url_url["parse"])(url).host.split('.');
      var parsedHostBaseUrl = parsedHost.splice(parsedHost.length - 2, parsedHost.length - 1).join('.');
      var cannotPreview = Object(external_lodash_["includes"])(constants["c" /* HOSTS_NO_PREVIEWS */], parsedHostBaseUrl); // translators: %s: host providing embed content e.g: www.youtube.com

      var iframeTitle = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Embedded content from %s'), parsedHostBaseUrl);
      var sandboxClassnames = dedupe_default()(type, className, 'wp-block-embed__wrapper'); // Disabled because the overlay div doesn't actually have a role or functionality
      // as far as the user is concerned. We're just catching the first click so that
      // the block can be selected without interacting with the embed preview that the overlay covers.

      /* eslint-disable jsx-a11y/no-noninteractive-element-interactions */

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

      /* eslint-enable jsx-a11y/no-noninteractive-element-interactions */

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
        _this.switchBackToURLInput = _this.switchBackToURLInput.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        _this.setUrl = _this.setUrl.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        _this.getAttributesFromPreview = _this.getAttributesFromPreview.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        _this.setAttributesFromPreview = _this.setAttributesFromPreview.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        _this.getResponsiveHelp = _this.getResponsiveHelp.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        _this.toggleResponsive = _this.toggleResponsive.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        _this.handleIncomingPreview = _this.handleIncomingPreview.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
          var allowResponsive = this.props.attributes.allowResponsive;
          this.setAttributesFromPreview();
          var upgradedBlock = Object(util["a" /* createUpgradedEmbedBlock */])(this.props, this.getAttributesFromPreview(this.props.preview, allowResponsive));

          if (upgradedBlock) {
            this.props.onReplace(upgradedBlock);
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
         * Gets block attributes based on the preview and responsive state.
         *
         * @param {string} preview The preview data.
         * @param {boolean} allowResponsive Apply responsive classes to fixed size content.
         * @return {Object} Attributes and values.
         */

      }, {
        key: "getAttributesFromPreview",
        value: function getAttributesFromPreview(preview) {
          var allowResponsive = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
          var attributes = {}; // Some plugins only return HTML with no type info, so default this to 'rich'.

          var _preview$type = preview.type,
              type = _preview$type === void 0 ? 'rich' : _preview$type; // If we got a provider name from the API, use it for the slug, otherwise we use the title,
          // because not all embed code gives us a provider name.

          var html = preview.html,
              providerName = preview.provider_name;
          var providerNameSlug = Object(external_lodash_["kebabCase"])(Object(external_lodash_["toLower"])('' !== providerName ? providerName : title));

          if (Object(util["e" /* isFromWordPress */])(html)) {
            type = 'wp-embed';
          }

          if (html || 'photo' === type) {
            attributes.type = type;
            attributes.providerNameSlug = providerNameSlug;
          }

          attributes.className = Object(util["c" /* getClassNames */])(html, this.props.attributes.className, responsive && allowResponsive);
          return attributes;
        }
        /***
         * Sets block attributes based on the preview data.
         */

      }, {
        key: "setAttributesFromPreview",
        value: function setAttributesFromPreview() {
          var _this$props = this.props,
              setAttributes = _this$props.setAttributes,
              preview = _this$props.preview;
          var allowResponsive = this.props.attributes.allowResponsive;
          setAttributes(this.getAttributesFromPreview(preview, allowResponsive));
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
          var _this$props$attribute = this.props.attributes,
              allowResponsive = _this$props$attribute.allowResponsive,
              className = _this$props$attribute.className;
          var html = this.props.preview.html;
          var newAllowResponsive = !allowResponsive;
          this.props.setAttributes({
            allowResponsive: newAllowResponsive,
            className: Object(util["c" /* getClassNames */])(html, className, responsive && newAllowResponsive)
          });
        }
      }, {
        key: "render",
        value: function render() {
          var _this2 = this;

          var _this$state = this.state,
              url = _this$state.url,
              editingURL = _this$state.editingURL;
          var _this$props$attribute2 = this.props.attributes,
              caption = _this$props$attribute2.caption,
              type = _this$props$attribute2.type,
              allowResponsive = _this$props$attribute2.allowResponsive;
          var _this$props2 = this.props,
              fetching = _this$props2.fetching,
              setAttributes = _this$props2.setAttributes,
              isSelected = _this$props2.isSelected,
              className = _this$props2.className,
              preview = _this$props2.preview,
              cannotEmbed = _this$props2.cannotEmbed,
              themeSupportsResponsive = _this$props2.themeSupportsResponsive,
              tryAgain = _this$props2.tryAgain;

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
                return Object(util["b" /* fallback */])(url, _this2.props.onReplace);
              },
              tryAgain: tryAgain
            });
          }

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

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

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

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/embed/index.js


/**
 * Internal dependencies
 */



/**
 * WordPress dependencies
 */



var embed_name = 'core/embed';
var settings = getEmbedBlockSettings({
  title: Object(external_this_wp_i18n_["_x"])('Embed', 'block title'),
  description: Object(external_this_wp_i18n_["__"])('Embed videos, images, tweets, audio, and other content from external sources.'),
  icon: icons["c" /* embedContentIcon */],
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
var common = core_embeds["a" /* common */].map(function (embedDefinition) {
  return Object(objectSpread["a" /* default */])({}, embedDefinition, {
    settings: getEmbedBlockSettings(embedDefinition.settings)
  });
});
var others = core_embeds["b" /* others */].map(function (embedDefinition) {
  return Object(objectSpread["a" /* default */])({}, embedDefinition, {
    settings: getEmbedBlockSettings(embedDefinition.settings)
  });
});


/***/ }),
/* 111 */,
/* 112 */,
/* 113 */,
/* 114 */,
/* 115 */,
/* 116 */,
/* 117 */
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

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(118)(module), __webpack_require__(59)))

/***/ }),
/* 118 */
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
/* 119 */
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
/* 120 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.decode = exports.parse = __webpack_require__(121);
exports.encode = exports.stringify = __webpack_require__(122);


/***/ }),
/* 121 */
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
/* 122 */
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
/* 123 */,
/* 124 */,
/* 125 */,
/* 126 */,
/* 127 */,
/* 128 */,
/* 129 */,
/* 130 */,
/* 131 */,
/* 132 */,
/* 133 */,
/* 134 */,
/* 135 */,
/* 136 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(5);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__);


/**
 * WordPress dependencies
 */







function MissingBlockWarning(_ref) {
  var attributes = _ref.attributes,
      convertToHTML = _ref.convertToHTML;
  var originalName = attributes.originalName,
      originalUndelimitedContent = attributes.originalUndelimitedContent;
  var hasContent = !!originalUndelimitedContent;
  var hasHTMLBlock = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["getBlockType"])('core/html');
  var actions = [];
  var messageHTML;

  if (hasContent && hasHTMLBlock) {
    messageHTML = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["sprintf"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Your site doesn’t include support for the "%s" block. You can leave this block intact, convert its content to a Custom HTML block, or remove it entirely.'), originalName);
    actions.push(Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Button"], {
      key: "convert",
      onClick: convertToHTML,
      isLarge: true,
      isPrimary: true
    }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Keep as HTML')));
  } else {
    messageHTML = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["sprintf"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Your site doesn’t include support for the "%s" block. You can leave this block intact or remove it entirely.'), originalName);
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["Warning"], {
    actions: actions
  }, messageHTML), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["RawHTML"], null, originalUndelimitedContent));
}

var edit = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withDispatch"])(function (dispatch, _ref2) {
  var clientId = _ref2.clientId,
      attributes = _ref2.attributes;

  var _dispatch = dispatch('core/block-editor'),
      replaceBlock = _dispatch.replaceBlock;

  return {
    convertToHTML: function convertToHTML() {
      replaceBlock(clientId, Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/html', {
        content: attributes.originalUndelimitedContent
      }));
    }
  };
})(MissingBlockWarning);
var name = 'core/missing';
var settings = {
  name: name,
  category: 'common',
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Unrecognized Block'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Your site doesn’t include support for this block.'),
  supports: {
    className: false,
    customClassName: false,
    inserter: false,
    html: false,
    reusable: false
  },
  attributes: {
    originalName: {
      type: 'string'
    },
    originalUndelimitedContent: {
      type: 'string'
    },
    originalContent: {
      type: 'string',
      source: 'html'
    }
  },
  edit: edit,
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    // Preserve the missing block's content.
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["RawHTML"], null, attributes.originalContent);
  }
};


/***/ }),
/* 137 */,
/* 138 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ paragraph_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(19);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

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
    _this.onReplace = _this.onReplace.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.toggleDropCap = _this.toggleDropCap.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.splitBlock = _this.splitBlock.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(ParagraphBlock, [{
    key: "onReplace",
    value: function onReplace(blocks) {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          onReplace = _this$props.onReplace;
      onReplace(blocks.map(function (block, index) {
        return index === 0 && block.name === edit_name ? Object(objectSpread["a" /* default */])({}, block, {
          attributes: Object(objectSpread["a" /* default */])({}, attributes, block.attributes)
        }) : block;
      }));
    }
  }, {
    key: "toggleDropCap",
    value: function toggleDropCap() {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes;
      setAttributes({
        dropCap: !attributes.dropCap
      });
    }
  }, {
    key: "getDropCapHelp",
    value: function getDropCapHelp(checked) {
      return checked ? Object(external_this_wp_i18n_["__"])('Showing large initial letter.') : Object(external_this_wp_i18n_["__"])('Toggle to show a large initial letter.');
    }
    /**
     * Split handler for RichText value, namely when content is pasted or the
     * user presses the Enter key.
     *
     * @param {?Array}     before Optional before value, to be used as content
     *                            in place of what exists currently for the
     *                            block. If undefined, the block is deleted.
     * @param {?Array}     after  Optional after value, to be appended in a new
     *                            paragraph block to the set of blocks passed
     *                            as spread.
     * @param {...WPBlock} blocks Optional blocks inserted between the before
     *                            and after value blocks.
     */

  }, {
    key: "splitBlock",
    value: function splitBlock(before, after) {
      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          insertBlocksAfter = _this$props3.insertBlocksAfter,
          setAttributes = _this$props3.setAttributes,
          onReplace = _this$props3.onReplace;

      for (var _len = arguments.length, blocks = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
        blocks[_key - 2] = arguments[_key];
      }

      if (after !== null) {
        // Append "After" content as a new paragraph block to the end of
        // any other blocks being inserted after the current paragraph.
        blocks.push(Object(external_this_wp_blocks_["createBlock"])(edit_name, {
          content: after
        }));
      }

      if (blocks.length && insertBlocksAfter) {
        insertBlocksAfter(blocks);
      }

      var content = attributes.content;

      if (before === null) {
        // If before content is omitted, treat as intent to delete block.
        onReplace([]);
      } else if (content !== before) {
        // Only update content if it has in-fact changed. In case that user
        // has created a new paragraph at end of an existing one, the value
        // of before will be strictly equal to the current content.
        setAttributes({
          content: before
        });
      }
    }
  }, {
    key: "render",
    value: function render() {
      var _classnames;

      var _this$props4 = this.props,
          attributes = _this$props4.attributes,
          setAttributes = _this$props4.setAttributes,
          mergeBlocks = _this$props4.mergeBlocks,
          onReplace = _this$props4.onReplace,
          className = _this$props4.className,
          backgroundColor = _this$props4.backgroundColor,
          textColor = _this$props4.textColor,
          setBackgroundColor = _this$props4.setBackgroundColor,
          setTextColor = _this$props4.setTextColor,
          fallbackBackgroundColor = _this$props4.fallbackBackgroundColor,
          fallbackTextColor = _this$props4.fallbackTextColor,
          fallbackFontSize = _this$props4.fallbackFontSize,
          fontSize = _this$props4.fontSize,
          setFontSize = _this$props4.setFontSize,
          isRTL = _this$props4.isRTL;
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
        }, Object(defineProperty["a" /* default */])(_classnames, backgroundColor.class, backgroundColor.class), Object(defineProperty["a" /* default */])(_classnames, textColor.class, textColor.class), Object(defineProperty["a" /* default */])(_classnames, fontSize.class, fontSize.class), _classnames)),
        style: {
          backgroundColor: backgroundColor.color,
          color: textColor.color,
          fontSize: fontSize.size ? fontSize.size + 'px' : undefined,
          textAlign: align,
          direction: direction
        },
        value: content,
        onChange: function onChange(nextContent) {
          setAttributes({
            content: nextContent
          });
        },
        unstableOnSplit: this.splitBlock,
        onMerge: mergeBlocks,
        onReplace: this.onReplace,
        onRemove: function onRemove() {
          return onReplace([]);
        },
        "aria-label": content ? Object(external_this_wp_i18n_["__"])('Paragraph block') : Object(external_this_wp_i18n_["__"])('Empty block; start writing or type forward slash to choose a block'),
        placeholder: placeholder || Object(external_this_wp_i18n_["__"])('Start writing or type / to choose a block')
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
/* harmony default export */ var edit = (ParagraphEdit);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/paragraph/index.js




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


var supports = {
  className: false
};
var schema = {
  content: {
    type: 'string',
    source: 'html',
    selector: 'p',
    default: ''
  },
  align: {
    type: 'string'
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
var paragraph_name = 'core/paragraph';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Paragraph'),
  description: Object(external_this_wp_i18n_["__"])('Start with the building block of all narrative.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M11 5v7H9.5C7.6 12 6 10.4 6 8.5S7.6 5 9.5 5H11m8-2H9.5C6.5 3 4 5.5 4 8.5S6.5 14 9.5 14H11v7h2V5h2v16h2V5h2V3z"
  })),
  category: 'common',
  keywords: [Object(external_this_wp_i18n_["__"])('text')],
  supports: supports,
  attributes: schema,
  transforms: {
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
  },
  deprecated: [{
    supports: supports,
    attributes: Object(objectSpread["a" /* default */])({}, schema, {
      width: {
        type: 'string'
      }
    }),
    save: function save(_ref) {
      var _classnames;

      var attributes = _ref.attributes;
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
      var className = classnames_default()((_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, "align".concat(width), width), Object(defineProperty["a" /* default */])(_classnames, 'has-background', backgroundColor || customBackgroundColor), Object(defineProperty["a" /* default */])(_classnames, 'has-drop-cap', dropCap), Object(defineProperty["a" /* default */])(_classnames, fontSizeClass, fontSizeClass), Object(defineProperty["a" /* default */])(_classnames, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames, backgroundClass, backgroundClass), _classnames));
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
    supports: supports,
    attributes: Object(external_lodash_["omit"])(Object(objectSpread["a" /* default */])({}, schema, {
      fontSize: {
        type: 'number'
      }
    }), 'customFontSize', 'customTextColor', 'customBackgroundColor'),
    save: function save(_ref2) {
      var _classnames2;

      var attributes = _ref2.attributes;
      var width = attributes.width,
          align = attributes.align,
          content = attributes.content,
          dropCap = attributes.dropCap,
          backgroundColor = attributes.backgroundColor,
          textColor = attributes.textColor,
          fontSize = attributes.fontSize;
      var className = classnames_default()((_classnames2 = {}, Object(defineProperty["a" /* default */])(_classnames2, "align".concat(width), width), Object(defineProperty["a" /* default */])(_classnames2, 'has-background', backgroundColor), Object(defineProperty["a" /* default */])(_classnames2, 'has-drop-cap', dropCap), _classnames2));
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
    supports: supports,
    attributes: Object(objectSpread["a" /* default */])({}, schema, {
      content: {
        type: 'string',
        source: 'html',
        default: ''
      }
    }),
    save: function save(_ref3) {
      var attributes = _ref3.attributes;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, attributes.content);
    },
    migrate: function migrate(attributes) {
      return attributes;
    }
  }],
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
  edit: edit,
  save: function save(_ref4) {
    var _classnames3;

    var attributes = _ref4.attributes;
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
    var className = classnames_default()((_classnames3 = {
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-drop-cap': dropCap
    }, Object(defineProperty["a" /* default */])(_classnames3, fontSizeClass, fontSizeClass), Object(defineProperty["a" /* default */])(_classnames3, textClass, textClass), Object(defineProperty["a" /* default */])(_classnames3, backgroundClass, backgroundClass), _classnames3));
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
};


/***/ }),
/* 139 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ classic_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ classic_settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(18);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/classic/edit.js









/**
 * WordPress dependencies
 */



var _window = window,
    wp = _window.wp;

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
    _this.initialize = _this.initialize.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSetup = _this.onSetup.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.focus = _this.focus.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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

      var clientId = this.props.clientId; // Disable reason: the toolbar itself is non-interactive, but must capture
      // events from the KeyboardShortcuts component to stop their propagation.

      /* eslint-disable jsx-a11y/no-static-element-interactions */

      return [// Disable reason: Clicking on this visual placeholder should create
      // the toolbar, it can also be created by focussing the field below.

      /* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/click-events-have-key-events */
      Object(external_this_wp_element_["createElement"])("div", {
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



// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/classic/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


var classic_name = 'core/freeform';
var classic_settings = {
  title: Object(external_this_wp_i18n_["_x"])('Classic', 'block title'),
  description: Object(external_this_wp_i18n_["__"])('Use the classic WordPress editor.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
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
  })),
  category: 'formatting',
  attributes: {
    content: {
      type: 'string',
      source: 'html'
    }
  },
  supports: {
    className: false,
    customClassName: false,
    // Hide 'Add to Reusable Blocks' on Classic blocks. Showing it causes a
    // confusing UX, because of its similarity to the 'Convert to Blocks' button.
    reusable: false
  },
  edit: edit_ClassicEdit,
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var content = attributes.content;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, content);
  }
};


/***/ }),
/* 140 */,
/* 141 */,
/* 142 */,
/* 143 */,
/* 144 */,
/* 145 */,
/* 146 */,
/* 147 */,
/* 148 */,
/* 149 */,
/* 150 */,
/* 151 */,
/* 152 */,
/* 153 */,
/* 154 */,
/* 155 */,
/* 156 */,
/* 157 */,
/* 158 */,
/* 159 */,
/* 160 */,
/* 161 */,
/* 162 */,
/* 163 */,
/* 164 */,
/* 165 */,
/* 166 */,
/* 167 */,
/* 168 */,
/* 169 */,
/* 170 */,
/* 171 */,
/* 172 */,
/* 173 */,
/* 174 */,
/* 175 */,
/* 176 */,
/* 177 */,
/* 178 */,
/* 179 */,
/* 180 */,
/* 181 */,
/* 182 */,
/* 183 */,
/* 184 */,
/* 185 */,
/* 186 */,
/* 187 */,
/* 188 */,
/* 189 */,
/* 190 */,
/* 191 */,
/* 192 */,
/* 193 */,
/* 194 */,
/* 195 */,
/* 196 */,
/* 197 */,
/* 198 */,
/* 199 */,
/* 200 */,
/* 201 */,
/* 202 */,
/* 203 */,
/* 204 */,
/* 205 */,
/* 206 */,
/* 207 */,
/* 208 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(7);
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(21);
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(17);
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(15);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(2);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(20);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__);





var _blockAttributes;



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







var ATTRIBUTE_QUOTE = 'value';
var ATTRIBUTE_CITATION = 'citation';
var blockAttributes = (_blockAttributes = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_3__[/* default */ "a"])(_blockAttributes, ATTRIBUTE_QUOTE, {
  type: 'string',
  source: 'html',
  selector: 'blockquote',
  multiline: 'p',
  default: ''
}), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_3__[/* default */ "a"])(_blockAttributes, ATTRIBUTE_CITATION, {
  type: 'string',
  source: 'html',
  selector: 'cite',
  default: ''
}), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_3__[/* default */ "a"])(_blockAttributes, "align", {
  type: 'string'
}), _blockAttributes);
var name = 'core/quote';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('Quote'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('Give quoted text visual emphasis. "In quoting others, we cite ourselves." — Julio Cortázar'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__["Path"], {
    d: "M18.62 18h-5.24l2-4H13V6h8v7.24L18.62 18zm-2-2h.76L19 12.76V8h-4v4h3.62l-2 4zm-8 2H3.38l2-4H3V6h8v7.24L8.62 18zm-2-2h.76L9 12.76V8H5v4h3.62l-2 4z"
  })),
  category: 'common',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('blockquote')],
  attributes: blockAttributes,
  styles: [{
    name: 'default',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["_x"])('Default', 'block style'),
    isDefault: true
  }, {
    name: 'large',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["_x"])('Large', 'block style')
  }],
  transforms: {
    from: [{
      type: 'block',
      isMultiBlock: true,
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/quote', {
          value: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["toHTMLString"])({
            value: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["join"])(attributes.map(function (_ref) {
              var content = _ref.content;
              return Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["create"])({
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
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/quote', {
          value: "<p>".concat(content, "</p>")
        });
      }
    }, {
      type: 'block',
      blocks: ['core/pullquote'],
      transform: function transform(_ref3) {
        var value = _ref3.value,
            citation = _ref3.citation;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/quote', {
          value: value,
          citation: citation
        });
      }
    }, {
      type: 'prefix',
      prefix: '>',
      transform: function transform(content) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/quote', {
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
              children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["getPhrasingContentSchema"])()
            },
            cite: {
              children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["getPhrasingContentSchema"])()
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
          paragraphs.push.apply(paragraphs, Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["split"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["create"])({
            html: value,
            multilineTag: 'p'
          }), "\u2028").map(function (piece) {
            return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/paragraph', {
              content: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["toHTMLString"])({
                value: piece
              })
            });
          })));
        }

        if (citation && citation !== '<p></p>') {
          paragraphs.push(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/paragraph', {
            content: citation
          }));
        }

        if (paragraphs.length === 0) {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/paragraph', {
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
            attrs = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(_ref5, ["value", "citation"]);

        // If there is no quote content, use the citation as the
        // content of the resulting heading. A nonexistent citation
        // will result in an empty heading.
        if (value === '<p></p>') {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/heading', {
            content: citation
          });
        }

        var pieces = Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["split"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["create"])({
          html: value,
          multilineTag: 'p'
        }), "\u2028");
        var headingBlock = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/heading', {
          content: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["toHTMLString"])({
            value: pieces[0]
          })
        });

        if (!citation && pieces.length === 1) {
          return headingBlock;
        }

        var quotePieces = pieces.slice(1);
        var quoteBlock = Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/quote', Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])({}, attrs, {
          citation: citation,
          value: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["toHTMLString"])({
            value: quotePieces.length ? Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["join"])(pieces.slice(1), "\u2028") : Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_9__["create"])(),
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
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])('core/pullquote', {
          value: value,
          citation: citation
        });
      }
    }]
  },
  edit: function edit(_ref7) {
    var attributes = _ref7.attributes,
        setAttributes = _ref7.setAttributes,
        isSelected = _ref7.isSelected,
        mergeBlocks = _ref7.mergeBlocks,
        onReplace = _ref7.onReplace,
        className = _ref7.className;
    var align = attributes.align,
        value = attributes.value,
        citation = attributes.citation;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["AlignmentToolbar"], {
      value: align,
      onChange: function onChange(nextAlign) {
        setAttributes({
          align: nextAlign
        });
      }
    })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])("blockquote", {
      className: className,
      style: {
        textAlign: align
      }
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"], {
      identifier: ATTRIBUTE_QUOTE,
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
      Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('Write quote…')
    }), (!_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].isEmpty(citation) || isSelected) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"], {
      identifier: ATTRIBUTE_CITATION,
      value: citation,
      onChange: function onChange(nextCitation) {
        return setAttributes({
          citation: nextCitation
        });
      },
      placeholder: // translators: placeholder text used for the citation
      Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('Write citation…'),
      className: "wp-block-quote__citation"
    })));
  },
  save: function save(_ref8) {
    var attributes = _ref8.attributes;
    var align = attributes.align,
        value = attributes.value,
        citation = attributes.citation;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])("blockquote", {
      style: {
        textAlign: align ? align : null
      }
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].Content, {
      multiline: true,
      value: value
    }), !_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].isEmpty(citation) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].Content, {
      tagName: "cite",
      value: citation
    }));
  },
  merge: function merge(attributes, _ref9) {
    var value = _ref9.value,
        citation = _ref9.citation;

    if (!value || value === '<p></p>') {
      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])({}, attributes, {
        citation: attributes.citation + citation
      });
    }

    return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])({}, attributes, {
      value: attributes.value + value,
      citation: attributes.citation + citation
    });
  },
  deprecated: [{
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])({}, blockAttributes, {
      style: {
        type: 'number',
        default: 1
      }
    }),
    migrate: function migrate(attributes) {
      if (attributes.style === 2) {
        return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])({}, Object(lodash__WEBPACK_IMPORTED_MODULE_5__["omit"])(attributes, ['style']), {
          className: attributes.className ? attributes.className + ' is-style-large' : 'is-style-large'
        });
      }

      return attributes;
    },
    save: function save(_ref10) {
      var attributes = _ref10.attributes;
      var align = attributes.align,
          value = attributes.value,
          citation = attributes.citation,
          style = attributes.style;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])("blockquote", {
        className: style === 2 ? 'is-large' : '',
        style: {
          textAlign: align ? align : null
        }
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].Content, {
        multiline: true,
        value: value
      }), !_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].isEmpty(citation) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].Content, {
        tagName: "cite",
        value: citation
      }));
    }
  }, {
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])({}, blockAttributes, {
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
    save: function save(_ref11) {
      var attributes = _ref11.attributes;
      var align = attributes.align,
          value = attributes.value,
          citation = attributes.citation,
          style = attributes.style;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])("blockquote", {
        className: "blocks-quote-style-".concat(style),
        style: {
          textAlign: align ? align : null
        }
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].Content, {
        multiline: true,
        value: value
      }), !_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].isEmpty(citation) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_8__["RichText"].Content, {
        tagName: "footer",
        value: citation
      }));
    }
  }]
};


/***/ }),
/* 209 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(2);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(16);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(41);
/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(memize__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__);


/**
 * External dependencies
 */



/**
 * WordPress dependencies
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
 * Returns the layouts configuration for a given number of columns.
 *
 * @param {number} columns Number of columns.
 *
 * @return {Object[]} Columns layout configuration.
 */

var getColumnsTemplate = memize__WEBPACK_IMPORTED_MODULE_3___default()(function (columns) {
  return Object(lodash__WEBPACK_IMPORTED_MODULE_1__["times"])(columns, function () {
    return ['core/column'];
  });
});
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

var name = 'core/columns';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Columns'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["G"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Path"], {
    d: "M4,4H20a2,2,0,0,1,2,2V18a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V6A2,2,0,0,1,4,4ZM4 6V18H8V6Zm6 0V18h4V6Zm6 0V18h4V6Z"
  }))),
  category: 'layout',
  attributes: {
    columns: {
      type: 'number',
      default: 2
    }
  },
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Add a block that displays content in multiple columns, then add whatever content blocks you’d like.'),
  supports: {
    align: ['wide', 'full'],
    html: false
  },
  deprecated: [{
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
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/column', {}, columnBlocks);
      });
      return [attributes, migratedInnerBlocks];
    },
    save: function save(_ref) {
      var attributes = _ref.attributes;
      var columns = attributes.columns;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
        className: "has-".concat(columns, "-columns")
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__["InnerBlocks"].Content, null));
    }
  }],
  edit: function edit(_ref2) {
    var attributes = _ref2.attributes,
        setAttributes = _ref2.setAttributes,
        className = _ref2.className;
    var columns = attributes.columns;
    var classes = classnames__WEBPACK_IMPORTED_MODULE_2___default()(className, "has-".concat(columns, "-columns"));
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["RangeControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Columns'),
      value: columns,
      onChange: function onChange(nextColumns) {
        setAttributes({
          columns: nextColumns
        });
      },
      min: 2,
      max: 6,
      required: true
    }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: classes
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__["InnerBlocks"], {
      template: getColumnsTemplate(columns),
      templateLock: "all",
      allowedBlocks: ALLOWED_BLOCKS
    })));
  },
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    var columns = attributes.columns;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "has-".concat(columns, "-columns")
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__["InnerBlocks"].Content, null));
  }
};


/***/ }),
/* 210 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);


/**
 * WordPress dependencies
 */



var name = 'core/column';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Column'),
  parent: ['core/columns'],
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Path"], {
    d: "M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16zm0-11.47L17.74 9 12 13.47 6.26 9 12 4.53z"
  })),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A single column within a columns block.'),
  category: 'common',
  supports: {
    inserter: false,
    reusable: false,
    html: false
  },
  edit: function edit() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["InnerBlocks"], {
      templateLock: false
    });
  },
  save: function save() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["InnerBlocks"].Content, null));
  }
};


/***/ }),
/* 211 */
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
/* 212 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(21);
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(17);
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(7);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(2);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(20);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__);





/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







var listContentSchema = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])({}, Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["getPhrasingContentSchema"])(), {
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
var supports = {
  className: false
};
var schema = {
  ordered: {
    type: 'boolean',
    default: false
  },
  values: {
    type: 'string',
    source: 'html',
    selector: 'ol,ul',
    multiline: 'li',
    default: ''
  }
};
var name = 'core/list';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('List'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Create a bulleted or numbered list.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["G"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["Path"], {
    d: "M9 19h12v-2H9v2zm0-6h12v-2H9v2zm0-8v2h12V5H9zm-4-.5c-.828 0-1.5.672-1.5 1.5S4.172 7.5 5 7.5 6.5 6.828 6.5 6 5.828 4.5 5 4.5zm0 6c-.828 0-1.5.672-1.5 1.5s.672 1.5 1.5 1.5 1.5-.672 1.5-1.5-.672-1.5-1.5-1.5zm0 6c-.828 0-1.5.672-1.5 1.5s.672 1.5 1.5 1.5 1.5-.672 1.5-1.5-.672-1.5-1.5-1.5z"
  }))),
  category: 'common',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('bullet list'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('ordered list'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('numbered list')],
  attributes: schema,
  supports: supports,
  transforms: {
    from: [{
      type: 'block',
      isMultiBlock: true,
      blocks: ['core/paragraph'],
      transform: function transform(blockAttributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/list', {
          values: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["toHTMLString"])({
            value: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["join"])(blockAttributes.map(function (_ref) {
              var content = _ref.content;
              var value = Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["create"])({
                html: content
              });

              if (blockAttributes.length > 1) {
                return value;
              } // When converting only one block, transform
              // every line to a list item.


              return Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["replace"])(value, /\n/g, _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["LINE_SEPARATOR"]);
            }), _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["LINE_SEPARATOR"]),
            multilineTag: 'li'
          })
        });
      }
    }, {
      type: 'block',
      blocks: ['core/quote'],
      transform: function transform(_ref2) {
        var value = _ref2.value;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/list', {
          values: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["toHTMLString"])({
            value: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["create"])({
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
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/list', Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])({}, Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["getBlockAttributes"])('core/list', node.outerHTML), {
          ordered: node.nodeName === 'OL'
        }));
      }
    }].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(['*', '-'].map(function (prefix) {
      return {
        type: 'prefix',
        prefix: prefix,
        transform: function transform(content) {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/list', {
            values: "<li>".concat(content, "</li>")
          });
        }
      };
    })), Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(['1.', '1)'].map(function (prefix) {
      return {
        type: 'prefix',
        prefix: prefix,
        transform: function transform(content) {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/list', {
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
        return Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["split"])(Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["create"])({
          html: values,
          multilineTag: 'li',
          multilineWrapperTags: ['ul', 'ol']
        }), _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["LINE_SEPARATOR"]).map(function (piece) {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/paragraph', {
            content: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["toHTMLString"])({
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
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/quote', {
          value: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["toHTMLString"])({
            value: Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_8__["create"])({
              html: values,
              multilineTag: 'li',
              multilineWrapperTags: ['ul', 'ol']
            }),
            multilineTag: 'p'
          })
        });
      }
    }]
  },
  deprecated: [{
    supports: supports,
    attributes: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])({}, Object(lodash__WEBPACK_IMPORTED_MODULE_4__["omit"])(schema, ['ordered']), {
      nodeName: {
        type: 'string',
        source: 'property',
        selector: 'ol,ul',
        property: 'nodeName',
        default: 'UL'
      }
    }),
    migrate: function migrate(attributes) {
      var nodeName = attributes.nodeName,
          migratedAttributes = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(attributes, ["nodeName"]);

      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])({}, migratedAttributes, {
        ordered: 'OL' === nodeName
      });
    },
    save: function save(_ref5) {
      var attributes = _ref5.attributes;
      var nodeName = attributes.nodeName,
          values = attributes.values;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].Content, {
        tagName: nodeName.toLowerCase(),
        value: values
      });
    }
  }],
  merge: function merge(attributes, attributesToMerge) {
    var values = attributesToMerge.values;

    if (!values || values === '<li></li>') {
      return attributes;
    }

    return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])({}, attributes, {
      values: attributes.values + values
    });
  },
  edit: function edit(_ref6) {
    var attributes = _ref6.attributes,
        insertBlocksAfter = _ref6.insertBlocksAfter,
        setAttributes = _ref6.setAttributes,
        mergeBlocks = _ref6.mergeBlocks,
        onReplace = _ref6.onReplace,
        className = _ref6.className;
    var ordered = attributes.ordered,
        values = attributes.values;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"], {
      identifier: "values",
      multiline: "li",
      tagName: ordered ? 'ol' : 'ul',
      onChange: function onChange(nextValues) {
        return setAttributes({
          values: nextValues
        });
      },
      value: values,
      wrapperClassName: "block-library-list",
      className: className,
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Write list…'),
      onMerge: mergeBlocks,
      unstableOnSplit: insertBlocksAfter ? function (before, after) {
        for (var _len = arguments.length, blocks = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
          blocks[_key - 2] = arguments[_key];
        }

        if (!blocks.length) {
          blocks.push(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/paragraph'));
        }

        if (after !== '<li></li>') {
          blocks.push(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__["createBlock"])('core/list', {
            ordered: ordered,
            values: after
          }));
        }

        setAttributes({
          values: before
        });
        insertBlocksAfter(blocks);
      } : undefined,
      onRemove: function onRemove() {
        return onReplace([]);
      },
      onTagNameChange: function onTagNameChange(tag) {
        return setAttributes({
          ordered: tag === 'ol'
        });
      }
    });
  },
  save: function save(_ref7) {
    var attributes = _ref7.attributes;
    var ordered = attributes.ordered,
        values = attributes.values;
    var tagName = ordered ? 'ol' : 'ul';
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__["RichText"].Content, {
      tagName: tagName,
      value: values,
      multiline: "li"
    });
  }
};


/***/ }),
/* 213 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);


/**
 * WordPress dependencies
 */




var name = 'core/preformatted';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Preformatted'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Add text that respects your spacing and tabs, and also allows styling.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Path"], {
    d: "M0,0h24v24H0V0z",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Path"], {
    d: "M20,4H4C2.9,4,2,4.9,2,6v12c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2V6C22,4.9,21.1,4,20,4z M20,18H4V6h16V18z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Rect"], {
    x: "6",
    y: "10",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Rect"], {
    x: "6",
    y: "14",
    width: "8",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Rect"], {
    x: "16",
    y: "14",
    width: "2",
    height: "2"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Rect"], {
    x: "10",
    y: "10",
    width: "8",
    height: "2"
  })),
  category: 'formatting',
  attributes: {
    content: {
      type: 'string',
      source: 'html',
      selector: 'pre',
      default: ''
    }
  },
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/code', 'core/paragraph'],
      transform: function transform(_ref) {
        var content = _ref.content;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/preformatted', {
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
          children: Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["getPhrasingContentSchema"])()
        }
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/paragraph', attributes);
      }
    }]
  },
  edit: function edit(_ref2) {
    var attributes = _ref2.attributes,
        mergeBlocks = _ref2.mergeBlocks,
        setAttributes = _ref2.setAttributes,
        className = _ref2.className;
    var content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"], {
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
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Write preformatted text…'),
      wrapperClassName: className,
      onMerge: mergeBlocks
    });
  },
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    var content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"].Content, {
      tagName: "pre",
      value: content
    });
  },
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: attributes.content + attributesToMerge.content
    };
  }
};


/***/ }),
/* 214 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);


/**
 * WordPress dependencies
 */



var name = 'core/separator';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Separator'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Create a break between ideas or sections with a horizontal separator.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Path"], {
    d: "M19 13H5v-2h14v2z"
  })),
  category: 'layout',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('horizontal-line'), 'hr', Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('divider')],
  styles: [{
    name: 'default',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Default'),
    isDefault: true
  }, {
    name: 'wide',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Wide Line')
  }, {
    name: 'dots',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Dots')
  }],
  transforms: {
    from: [{
      type: 'enter',
      regExp: /^-{3,}$/,
      transform: function transform() {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/separator');
      }
    }, {
      type: 'raw',
      selector: 'hr',
      schema: {
        hr: {}
      }
    }]
  },
  edit: function edit(_ref) {
    var className = _ref.className;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("hr", {
      className: className
    });
  },
  save: function save() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("hr", null);
  }
};


/***/ }),
/* 215 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_autop__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(66);
/* harmony import */ var _wordpress_autop__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_autop__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(6);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__);


/**
 * WordPress dependencies
 */






var name = 'core/shortcode';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shortcode'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Insert additional custom elements with a WordPress shortcode.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Path"], {
    d: "M8.5,21.4l1.9,0.5l5.2-19.3l-1.9-0.5L8.5,21.4z M3,19h4v-2H5V7h2V5H3V19z M17,5v2h2v10h-2v2h4V5H17z"
  })),
  category: 'widgets',
  attributes: {
    text: {
      type: 'string',
      source: 'html'
    }
  },
  transforms: {
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
            return Object(_wordpress_autop__WEBPACK_IMPORTED_MODULE_1__["removep"])(Object(_wordpress_autop__WEBPACK_IMPORTED_MODULE_1__["autop"])(content));
          }
        }
      },
      priority: 20
    }]
  },
  supports: {
    customClassName: false,
    className: false,
    html: false
  },
  edit: Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__["withInstanceId"])(function (_ref2) {
    var attributes = _ref2.attributes,
        setAttributes = _ref2.setAttributes,
        instanceId = _ref2.instanceId;
    var inputId = "blocks-shortcode-input-".concat(instanceId);
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "wp-block-shortcode"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("label", {
      htmlFor: inputId
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Dashicon"], {
      icon: "shortcode"
    }), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shortcode')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["PlainText"], {
      className: "input-control",
      id: inputId,
      value: attributes.text,
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Write shortcode here…'),
      onChange: function onChange(text) {
        return setAttributes({
          text: text
        });
      }
    }));
  }),
  save: function save(_ref3) {
    var attributes = _ref3.attributes;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["RawHTML"], null, attributes.text);
  }
};


/***/ }),
/* 216 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(28);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(16);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(6);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_6__);



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






var name = 'core/spacer';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Spacer'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add white space between blocks and customize its height.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["G"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Path"], {
    d: "M13 4v2h3.59L6 16.59V13H4v7h7v-2H7.41L18 7.41V11h2V4h-7"
  }))),
  category: 'layout',
  attributes: {
    height: {
      type: 'number',
      default: 100
    }
  },
  edit: Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_6__["withInstanceId"])(function (_ref) {
    var attributes = _ref.attributes,
        isSelected = _ref.isSelected,
        setAttributes = _ref.setAttributes,
        toggleSelection = _ref.toggleSelection,
        instanceId = _ref.instanceId;
    var height = attributes.height;
    var id = "block-spacer-height-input-".concat(instanceId);

    var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useState"])(height),
        _useState2 = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(_useState, 2),
        inputHeightValue = _useState2[0],
        setInputHeightValue = _useState2[1];

    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["ResizableBox"], {
      className: classnames__WEBPACK_IMPORTED_MODULE_2___default()('block-library-spacer__resize-container', {
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
      onResizeStop: function onResizeStop(event, direction, elt, delta) {
        var spacerHeight = parseInt(height + delta.height, 10);
        setAttributes({
          height: spacerHeight
        });
        setInputHeightValue(spacerHeight);
        toggleSelection(true);
      },
      onResizeStart: function onResizeStart() {
        toggleSelection(false);
      }
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["PanelBody"], {
      title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Spacer Settings')
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["BaseControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Height in pixels'),
      id: id
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("input", {
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
  }),
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      style: {
        height: attributes.height
      },
      "aria-hidden": true
    });
  }
};


/***/ }),
/* 217 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(49);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);


/**
 * WordPress dependencies
 */






var name = 'core/subhead';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Subheading (deprecated)'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('This block is deprecated. Please use the Paragraph block instead.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Path"], {
    d: "M7.1 6l-.5 3h4.5L9.4 19h3l1.8-10h4.5l.5-3H7.1z"
  })),
  category: 'common',
  supports: {
    // Hide from inserter as this block is deprecated.
    inserter: false,
    multiple: false
  },
  attributes: {
    content: {
      type: 'string',
      source: 'html',
      selector: 'p'
    },
    align: {
      type: 'string'
    }
  },
  transforms: {
    to: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/paragraph', attributes);
      }
    }]
  },
  edit: function edit(_ref) {
    var attributes = _ref.attributes,
        setAttributes = _ref.setAttributes,
        className = _ref.className;
    var align = attributes.align,
        content = attributes.content,
        placeholder = attributes.placeholder;
    _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default()('The Subheading block', {
      alternative: 'the Paragraph block',
      plugin: 'Gutenberg'
    });
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["AlignmentToolbar"], {
      value: align,
      onChange: function onChange(nextAlign) {
        setAttributes({
          align: nextAlign
        });
      }
    })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
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
      placeholder: placeholder || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Write subheading…')
    }));
  },
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    var align = attributes.align,
        content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"].Content, {
      tagName: "p",
      style: {
        textAlign: align
      },
      value: content
    });
  }
};


/***/ }),
/* 218 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);


/**
 * WordPress dependencies
 */



var name = 'core/template';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Reusable Template'),
  category: 'reusable',
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Template block used as a container.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Rect"], {
    x: "0",
    fill: "none",
    width: "24",
    height: "24"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["G"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Path"], {
    d: "M19 3H5c-1.105 0-2 .895-2 2v14c0 1.105.895 2 2 2h14c1.105 0 2-.895 2-2V5c0-1.105-.895-2-2-2zM6 6h5v5H6V6zm4.5 13C9.12 19 8 17.88 8 16.5S9.12 14 10.5 14s2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5zm3-6l3-5 3 5h-6z"
  }))),
  supports: {
    customClassName: false,
    html: false,
    inserter: false
  },
  edit: function edit() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__["InnerBlocks"], null);
  },
  save: function save() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__["InnerBlocks"].Content, null);
  }
};


/***/ }),
/* 219 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(17);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(2);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(49);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7__);



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







var name = 'core/text-columns';
var settings = {
  // Disable insertion as this block is deprecated and ultimately replaced by the Columns block.
  supports: {
    inserter: false
  },
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Text Columns (deprecated)'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('This block is deprecated. Please use the Columns block instead.'),
  icon: 'columns',
  category: 'layout',
  attributes: {
    content: {
      type: 'array',
      source: 'query',
      selector: 'p',
      query: {
        children: {
          type: 'string',
          source: 'html'
        }
      },
      default: [{}, {}]
    },
    columns: {
      type: 'number',
      default: 2
    },
    width: {
      type: 'string'
    }
  },
  transforms: {
    to: [{
      type: 'block',
      blocks: ['core/columns'],
      transform: function transform(_ref) {
        var className = _ref.className,
            columns = _ref.columns,
            content = _ref.content,
            width = _ref.width;
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/columns', {
          align: 'wide' === width || 'full' === width ? width : undefined,
          className: className,
          columns: columns
        }, content.map(function (_ref2) {
          var children = _ref2.children;
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/column', {}, [Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('core/paragraph', {
            content: children
          })]);
        }));
      }
    }]
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var width = attributes.width;

    if ('wide' === width || 'full' === width) {
      return {
        'data-align': width
      };
    }
  },
  edit: function edit(_ref3) {
    var attributes = _ref3.attributes,
        setAttributes = _ref3.setAttributes,
        className = _ref3.className;
    var width = attributes.width,
        content = attributes.content,
        columns = attributes.columns;
    _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_7___default()('The Text Columns block', {
      alternative: 'the Columns block',
      plugin: 'Gutenberg'
    });
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["BlockAlignmentToolbar"], {
      value: width,
      onChange: function onChange(nextWidth) {
        return setAttributes({
          width: nextWidth
        });
      },
      controls: ['center', 'wide', 'full']
    })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["RangeControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Columns'),
      value: columns,
      onChange: function onChange(value) {
        return setAttributes({
          columns: value
        });
      },
      min: 2,
      max: 4,
      required: true
    }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "".concat(className, " align").concat(width, " columns-").concat(columns)
    }, Object(lodash__WEBPACK_IMPORTED_MODULE_2__["times"])(columns, function (index) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
        className: "wp-block-column",
        key: "column-".concat(index)
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"], {
        tagName: "p",
        value: Object(lodash__WEBPACK_IMPORTED_MODULE_2__["get"])(content, [index, 'children']),
        onChange: function onChange(nextContent) {
          setAttributes({
            content: [].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(content.slice(0, index)), [{
              children: nextContent
            }], Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(content.slice(index + 1)))
          });
        },
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('New Column')
      }));
    })));
  },
  save: function save(_ref4) {
    var attributes = _ref4.attributes;
    var width = attributes.width,
        content = attributes.content,
        columns = attributes.columns;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "align".concat(width, " columns-").concat(columns)
    }, Object(lodash__WEBPACK_IMPORTED_MODULE_2__["times"])(columns, function (index) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
        className: "wp-block-column",
        key: "column-".concat(index)
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"].Content, {
        tagName: "p",
        value: Object(lodash__WEBPACK_IMPORTED_MODULE_2__["get"])(content, [index, 'children'])
      }));
    }));
  }
};


/***/ }),
/* 220 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(4);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);


/**
 * WordPress dependencies
 */





var name = 'core/verse';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Verse'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Insert poetry. Use special spacing formats. Or quote song lyrics.'),
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Path"], {
    d: "M3 17v4h4l11-11-4-4L3 17zm3 2H5v-1l9-9 1 1-9 9zM21 6l-3-3h-1l-2 2 4 4 2-2V6z"
  })),
  category: 'formatting',
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('poetry')],
  attributes: {
    content: {
      type: 'string',
      source: 'html',
      selector: 'pre',
      default: ''
    },
    textAlign: {
      type: 'string'
    }
  },
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/verse', attributes);
      }
    }],
    to: [{
      type: 'block',
      blocks: ['core/paragraph'],
      transform: function transform(attributes) {
        return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__["createBlock"])('core/paragraph', attributes);
      }
    }]
  },
  edit: function edit(_ref) {
    var attributes = _ref.attributes,
        setAttributes = _ref.setAttributes,
        className = _ref.className,
        mergeBlocks = _ref.mergeBlocks;
    var textAlign = attributes.textAlign,
        content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["AlignmentToolbar"], {
      value: textAlign,
      onChange: function onChange(nextAlign) {
        setAttributes({
          textAlign: nextAlign
        });
      }
    })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"], {
      tagName: "pre",
      value: content,
      onChange: function onChange(nextContent) {
        setAttributes({
          content: nextContent
        });
      },
      style: {
        textAlign: textAlign
      },
      placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Write…'),
      wrapperClassName: className,
      onMerge: mergeBlocks
    }));
  },
  save: function save(_ref2) {
    var attributes = _ref2.attributes;
    var textAlign = attributes.textAlign,
        content = attributes.content;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"].Content, {
      tagName: "pre",
      style: {
        textAlign: textAlign
      },
      value: content
    });
  },
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: attributes.content + attributesToMerge.content
    };
  }
};


/***/ }),
/* 221 */,
/* 222 */,
/* 223 */,
/* 224 */,
/* 225 */,
/* 226 */,
/* 227 */,
/* 228 */,
/* 229 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ image_name; });
__webpack_require__.d(__webpack_exports__, "stripFirstImage", function() { return /* binding */ stripFirstImage; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(19);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","blob"]}
var external_this_wp_blob_ = __webpack_require__(35);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(28);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(25);

// EXTERNAL MODULE: external {"this":["wp","viewport"]}
var external_this_wp_viewport_ = __webpack_require__(40);

// EXTERNAL MODULE: ./node_modules/@wordpress/block-library/build-module/embed/util.js
var util = __webpack_require__(53);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
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
    _this.bindContainer = _this.bindContainer.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.calculateSize = _this.calculateSize.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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

var edit_ImageEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ImageEdit, _Component);

  function ImageEdit(_ref) {
    var _this;

    var attributes = _ref.attributes;

    Object(classCallCheck["a" /* default */])(this, ImageEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ImageEdit).apply(this, arguments));
    _this.updateAlt = _this.updateAlt.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.updateAlignment = _this.updateAlignment.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onFocusCaption = _this.onFocusCaption.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onImageClick = _this.onImageClick.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelectImage = _this.onSelectImage.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelectURL = _this.onSelectURL.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.updateImageURL = _this.updateImageURL.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.updateWidth = _this.updateWidth.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.updateHeight = _this.updateHeight.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.updateDimensions = _this.updateDimensions.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSetCustomHref = _this.onSetCustomHref.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSetLinkClass = _this.onSetLinkClass.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSetLinkRel = _this.onSetLinkRel.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSetLinkDestination = _this.onSetLinkDestination.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSetNewTab = _this.onSetNewTab.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.getFilename = _this.getFilename.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.toggleIsEditing = _this.toggleIsEditing.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onUploadError = _this.onUploadError.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onImageError = _this.onImageError.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
          setAttributes = _this$props.setAttributes,
          noticeOperations = _this$props.noticeOperations;
      var id = attributes.id,
          _attributes$url = attributes.url,
          url = _attributes$url === void 0 ? '' : _attributes$url;

      if (edit_isTemporaryImage(id, url)) {
        var file = Object(external_this_wp_blob_["getBlobByURL"])(url);

        if (file) {
          Object(external_this_wp_editor_["mediaUpload"])({
            filesList: [file],
            onFileChange: function onFileChange(_ref2) {
              var _ref3 = Object(slicedToArray["a" /* default */])(_ref2, 1),
                  image = _ref3[0];

              setAttributes(edit_pickRelevantMediaFiles(image));
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
      this.props.setAttributes(Object(objectSpread["a" /* default */])({}, edit_pickRelevantMediaFiles(media), {
        width: undefined,
        height: undefined
      }));
    }
  }, {
    key: "onSetLinkDestination",
    value: function onSetLinkDestination(value) {
      var href;

      if (value === LINK_DESTINATION_NONE) {
        href = undefined;
      } else if (value === LINK_DESTINATION_MEDIA) {
        href = this.props.image && this.props.image.source_url || this.props.attributes.url;
      } else if (value === LINK_DESTINATION_ATTACHMENT) {
        href = this.props.image && this.props.image.link;
      } else {
        href = this.props.attributes.href;
      }

      this.props.setAttributes({
        linkDestination: value,
        href: href
      });
    }
  }, {
    key: "onSelectURL",
    value: function onSelectURL(newURL) {
      var url = this.props.attributes.url;

      if (newURL !== url) {
        this.props.setAttributes({
          url: newURL,
          id: undefined
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
      var embedBlock = Object(util["a" /* createUpgradedEmbedBlock */])({
        attributes: {
          url: url
        }
      });

      if (undefined !== embedBlock) {
        this.props.onReplace(embedBlock);
      }
    }
  }, {
    key: "onSetCustomHref",
    value: function onSetCustomHref(value) {
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
    key: "updateImageURL",
    value: function updateImageURL(url) {
      this.props.setAttributes({
        url: url,
        width: undefined,
        height: undefined
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
    key: "getLinkDestinationOptions",
    value: function getLinkDestinationOptions() {
      return [{
        value: LINK_DESTINATION_NONE,
        label: Object(external_this_wp_i18n_["__"])('None')
      }, {
        value: LINK_DESTINATION_MEDIA,
        label: Object(external_this_wp_i18n_["__"])('Media File')
      }, {
        value: LINK_DESTINATION_ATTACHMENT,
        label: Object(external_this_wp_i18n_["__"])('Attachment Page')
      }, {
        value: LINK_DESTINATION_CUSTOM,
        label: Object(external_this_wp_i18n_["__"])('Custom URL')
      }];
    }
  }, {
    key: "toggleIsEditing",
    value: function toggleIsEditing() {
      this.setState({
        isEditing: !this.state.isEditing
      });
    }
  }, {
    key: "getImageSizeOptions",
    value: function getImageSizeOptions() {
      var _this$props2 = this.props,
          imageSizes = _this$props2.imageSizes,
          image = _this$props2.image;
      return Object(external_lodash_["compact"])(Object(external_lodash_["map"])(imageSizes, function (_ref4) {
        var name = _ref4.name,
            slug = _ref4.slug;
        var sizeUrl = Object(external_lodash_["get"])(image, ['media_details', 'sizes', slug, 'source_url']);

        if (!sizeUrl) {
          return null;
        }

        return {
          value: sizeUrl,
          label: name
        };
      }));
    }
  }, {
    key: "render",
    value: function render() {
      var _this4 = this;

      var isEditing = this.state.isEditing;
      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          setAttributes = _this$props3.setAttributes,
          isLargeViewport = _this$props3.isLargeViewport,
          isSelected = _this$props3.isSelected,
          className = _this$props3.className,
          maxWidth = _this$props3.maxWidth,
          noticeUI = _this$props3.noticeUI,
          toggleSelection = _this$props3.toggleSelection,
          isRTL = _this$props3.isRTL;
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
          linkTarget = attributes.linkTarget;
      var isExternal = edit_isExternalImage(id, url);
      var toolbarEditButton;

      if (url) {
        if (isExternal) {
          toolbarEditButton = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
            className: "components-icon-button components-toolbar__control",
            label: Object(external_this_wp_i18n_["__"])('Edit image'),
            onClick: this.toggleIsEditing,
            icon: "edit"
          }));
        } else {
          toolbarEditButton = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
            onSelect: this.onSelectImage,
            allowedTypes: ALLOWED_MEDIA_TYPES,
            value: id,
            render: function render(_ref5) {
              var open = _ref5.open;
              return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
                className: "components-toolbar__control",
                label: Object(external_this_wp_i18n_["__"])('Edit image'),
                icon: "edit",
                onClick: open
              });
            }
          })));
        }
      }

      var controls = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockAlignmentToolbar"], {
        value: align,
        onChange: this.updateAlignment
      }), toolbarEditButton);

      if (isEditing || !url) {
        var src = isExternal ? url : undefined;
        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, controls, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaPlaceholder"], {
          icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
            icon: icon
          }),
          className: className,
          onSelect: this.onSelectImage,
          onSelectURL: this.onSelectURL,
          notices: noticeUI,
          onError: this.onUploadError,
          accept: "image/*",
          allowedTypes: ALLOWED_MEDIA_TYPES,
          value: {
            id: id,
            src: src
          }
        }));
      }

      var classes = classnames_default()(className, {
        'is-transient': Object(external_this_wp_blob_["isBlobURL"])(url),
        'is-resized': !!width || !!height,
        'is-focused': isSelected
      });
      var isResizable = ['wide', 'full'].indexOf(align) === -1 && isLargeViewport;
      var isLinkURLInputReadOnly = linkDestination !== LINK_DESTINATION_CUSTOM;
      var imageSizeOptions = this.getImageSizeOptions();

      var getInspectorControls = function getInspectorControls(imageWidth, imageHeight) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
          title: Object(external_this_wp_i18n_["__"])('Image Settings')
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextareaControl"], {
          label: Object(external_this_wp_i18n_["__"])('Alt Text (Alternative Text)'),
          value: alt,
          onChange: _this4.updateAlt,
          help: Object(external_this_wp_i18n_["__"])('Alternative text describes your image to people who can’t see it. Add a short description with its key details.')
        }), !Object(external_lodash_["isEmpty"])(imageSizeOptions) && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
          label: Object(external_this_wp_i18n_["__"])('Image Size'),
          value: url,
          options: imageSizeOptions,
          onChange: _this4.updateImageURL
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
          value: width !== undefined ? width : imageWidth,
          min: 1,
          onChange: _this4.updateWidth
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          type: "number",
          className: "block-library-image__dimensions__height",
          label: Object(external_this_wp_i18n_["__"])('Height'),
          value: height !== undefined ? height : imageHeight,
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
        }, Object(external_this_wp_i18n_["__"])('Reset'))))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
          title: Object(external_this_wp_i18n_["__"])('Link Settings')
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
          label: Object(external_this_wp_i18n_["__"])('Link To'),
          value: linkDestination,
          options: _this4.getLinkDestinationOptions(),
          onChange: _this4.onSetLinkDestination
        }), linkDestination !== LINK_DESTINATION_NONE && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          label: Object(external_this_wp_i18n_["__"])('Link URL'),
          value: href || '',
          onChange: _this4.onSetCustomHref,
          placeholder: !isLinkURLInputReadOnly ? 'https://' : undefined,
          readOnly: isLinkURLInputReadOnly
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
          label: Object(external_this_wp_i18n_["__"])('Open in New Tab'),
          onChange: _this4.onSetNewTab,
          checked: linkTarget === '_blank'
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          label: Object(external_this_wp_i18n_["__"])('Link CSS Class'),
          value: linkClass || '',
          onChange: _this4.onSetLinkClass
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          label: Object(external_this_wp_i18n_["__"])('Link Rel'),
          value: rel || '',
          onChange: _this4.onSetLinkRel
        }))));
      }; // Disable reason: Each block can be selected by clicking on it

      /* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */


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
          size: width && height ? {
            width: width,
            height: height
          } : undefined,
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
          onResizeStart: function onResizeStart() {
            toggleSelection(false);
          },
          onResizeStop: function onResizeStop(event, direction, elt, delta) {
            setAttributes({
              width: parseInt(currentWidth + delta.width, 10),
              height: parseInt(currentHeight + delta.height, 10)
            });
            toggleSelection(true);
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
      })));
      /* eslint-enable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */
    }
  }]);

  return ImageEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, props) {
  var _select = select('core'),
      getMedia = _select.getMedia;

  var _select2 = select('core/block-editor'),
      getSettings = _select2.getSettings;

  var id = props.attributes.id;

  var _getSettings = getSettings(),
      maxWidth = _getSettings.maxWidth,
      isRTL = _getSettings.isRTL,
      imageSizes = _getSettings.imageSizes;

  return {
    image: id ? getMedia(id) : null,
    maxWidth: maxWidth,
    isRTL: isRTL,
    imageSizes: imageSizes
  };
}), Object(external_this_wp_viewport_["withViewportMatch"])({
  isLargeViewport: 'medium'
}), external_this_wp_components_["withNotices"]])(edit_ImageEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/image/index.js





/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



var image_name = 'core/image';
var blockAttributes = {
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
  align: {
    type: 'string'
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

function getFirstAnchorAttributeFormHTML(html, attributeName) {
  var _document$implementat = document.implementation.createHTMLDocument(''),
      body = _document$implementat.body;

  body.innerHTML = html;
  var firstElementChild = body.firstElementChild;

  if (firstElementChild && firstElementChild.nodeName === 'A') {
    return firstElementChild.getAttribute(attributeName) || undefined;
  }
}

function stripFirstImage(attributes, _ref) {
  var shortcode = _ref.shortcode;

  var _document$implementat2 = document.implementation.createHTMLDocument(''),
      body = _document$implementat2.body;

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
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Image'),
  description: Object(external_this_wp_i18n_["__"])('Insert an image to make a visual statement.'),
  icon: icon,
  category: 'common',
  keywords: ['img', // "img" is not translated as it is intended to reflect the HTML <img> tag.
  Object(external_this_wp_i18n_["__"])('photo')],
  attributes: blockAttributes,
  transforms: {
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

        var block = Object(external_this_wp_blocks_["createBlock"])('core/image', {
          url: Object(external_this_wp_blob_["createBlobURL"])(file)
        });
        return block;
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
  },
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
  edit: edit,
  save: function save(_ref7) {
    var _classnames;

    var attributes = _ref7.attributes;
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
        linkTarget = attributes.linkTarget;
    var classes = classnames_default()((_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, "align".concat(align), align), Object(defineProperty["a" /* default */])(_classnames, 'is-resized', width || height), _classnames));
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
  },
  deprecated: [{
    attributes: blockAttributes,
    save: function save(_ref8) {
      var _classnames2;

      var attributes = _ref8.attributes;
      var url = attributes.url,
          alt = attributes.alt,
          caption = attributes.caption,
          align = attributes.align,
          href = attributes.href,
          width = attributes.width,
          height = attributes.height,
          id = attributes.id;
      var classes = classnames_default()((_classnames2 = {}, Object(defineProperty["a" /* default */])(_classnames2, "align".concat(align), align), Object(defineProperty["a" /* default */])(_classnames2, 'is-resized', width || height), _classnames2));
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
    attributes: blockAttributes,
    save: function save(_ref9) {
      var attributes = _ref9.attributes;
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
    attributes: blockAttributes,
    save: function save(_ref10) {
      var attributes = _ref10.attributes;
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
  }]
};


/***/ }),
/* 230 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ media_text_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(19);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

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

var ALLOWED_MEDIA_TYPES = ['image', 'video'];

var media_container_MediaContainer =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MediaContainer, _Component);

  function MediaContainer() {
    Object(classCallCheck["a" /* default */])(this, MediaContainer);

    return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MediaContainer).apply(this, arguments));
  }

  Object(createClass["a" /* default */])(MediaContainer, [{
    key: "renderToolbarEditButton",
    value: function renderToolbarEditButton() {
      var _this$props = this.props,
          mediaId = _this$props.mediaId,
          onSelectMedia = _this$props.onSelectMedia;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
        onSelect: onSelectMedia,
        allowedTypes: ALLOWED_MEDIA_TYPES,
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
          className = _this$props2.className;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, this.renderToolbarEditButton(), Object(external_this_wp_element_["createElement"])("figure", {
        className: className
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
          className = _this$props4.className;
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
        allowedTypes: ALLOWED_MEDIA_TYPES
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
          onWidthChange = _this$props5.onWidthChange;

      if (mediaType && mediaUrl) {
        var onResize = function onResize(event, direction, elt) {
          onWidthChange(parseInt(elt.style.width));
        };

        var onResizeStop = function onResizeStop(event, direction, elt) {
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

/* harmony default export */ var media_container = (media_container_MediaContainer);

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

var ALLOWED_BLOCKS = ['core/button', 'core/paragraph', 'core/heading', 'core/list'];
var TEMPLATE = [['core/paragraph', {
  fontSize: 'large',
  placeholder: Object(external_this_wp_i18n_["_x"])('Content…', 'content placeholder')
}]];

var edit_MediaTextEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(MediaTextEdit, _Component);

  function MediaTextEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, MediaTextEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(MediaTextEdit).apply(this, arguments));
    _this.onSelectMedia = _this.onSelectMedia.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onWidthChange = _this.onWidthChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.commitWidthChange = _this.commitWidthChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
        mediaUrl: src || media.url
      });
    }
  }, {
    key: "onWidthChange",
    value: function onWidthChange(width) {
      this.setState({
        mediaWidth: width
      });
    }
  }, {
    key: "commitWidthChange",
    value: function commitWidthChange(width) {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        mediaWidth: width
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
          mediaWidth = attributes.mediaWidth;
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
        mediaWidth: mediaWidth
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
          mediaWidth = attributes.mediaWidth;
      var temporaryMediaWidth = this.state.mediaWidth;
      var classNames = classnames_default()(className, (_classnames = {
        'has-media-on-the-right': 'right' === mediaPosition,
        'is-selected': isSelected
      }, Object(defineProperty["a" /* default */])(_classnames, backgroundColor.class, backgroundColor.class), Object(defineProperty["a" /* default */])(_classnames, 'is-stacked-on-mobile', isStackedOnMobile), _classnames));
      var widthString = "".concat(temporaryMediaWidth || mediaWidth, "%");
      var style = {
        gridTemplateColumns: 'right' === mediaPosition ? "auto ".concat(widthString) : "".concat(widthString, " auto"),
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
      }), mediaType === 'image' && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextareaControl"], {
        label: Object(external_this_wp_i18n_["__"])('Alt Text (Alternative Text)'),
        value: mediaAlt,
        onChange: onMediaAltChange,
        help: Object(external_this_wp_i18n_["__"])('Alternative text describes your image to people who can’t see it. Add a short description with its key details.')
      }));
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, mediaTextGeneralSettings, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
        title: Object(external_this_wp_i18n_["__"])('Color Settings'),
        initialOpen: false,
        colorSettings: colorSettings
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
        controls: toolbarControls
      })), Object(external_this_wp_element_["createElement"])("div", {
        className: classNames,
        style: style
      }, this.renderMediaArea(), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InnerBlocks"], {
        allowedBlocks: ALLOWED_BLOCKS,
        template: TEMPLATE,
        templateInsertUpdatesSelection: false
      })));
    }
  }]);

  return MediaTextEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_blockEditor_["withColors"])('backgroundColor')(edit_MediaTextEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M13 17h8v-2h-8v2zM3 19h8V5H3v14zM13 9h8V7h-8v2zm0 4h8v-2h-8v2z"
})));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/media-text/index.js



/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var DEFAULT_MEDIA_WIDTH = 50;
var media_text_name = 'core/media-text';
var blockAttributes = {
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
};
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Media & Text'),
  description: Object(external_this_wp_i18n_["__"])('Set media and words side-by-side for a richer layout.'),
  icon: icon,
  category: 'layout',
  keywords: [Object(external_this_wp_i18n_["__"])('image'), Object(external_this_wp_i18n_["__"])('video')],
  attributes: blockAttributes,
  supports: {
    align: ['wide', 'full'],
    html: false
  },
  transforms: {
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
  },
  edit: edit,
  save: function save(_ref7) {
    var _classnames;

    var attributes = _ref7.attributes;
    var backgroundColor = attributes.backgroundColor,
        customBackgroundColor = attributes.customBackgroundColor,
        isStackedOnMobile = attributes.isStackedOnMobile,
        mediaAlt = attributes.mediaAlt,
        mediaPosition = attributes.mediaPosition,
        mediaType = attributes.mediaType,
        mediaUrl = attributes.mediaUrl,
        mediaWidth = attributes.mediaWidth,
        mediaId = attributes.mediaId;
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
  },
  deprecated: [{
    attributes: blockAttributes,
    save: function save(_ref8) {
      var _classnames2;

      var attributes = _ref8.attributes;
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
      var className = classnames_default()((_classnames2 = {
        'has-media-on-the-right': 'right' === mediaPosition
      }, Object(defineProperty["a" /* default */])(_classnames2, backgroundClass, backgroundClass), Object(defineProperty["a" /* default */])(_classnames2, 'is-stacked-on-mobile', isStackedOnMobile), _classnames2));
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
  }]
};


/***/ }),
/* 231 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ gallery_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

// EXTERNAL MODULE: external {"this":["wp","blob"]}
var external_this_wp_blob_ = __webpack_require__(35);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(17);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(18);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/gallery-image.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */









var gallery_image_GalleryImage =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(GalleryImage, _Component);

  function GalleryImage() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, GalleryImage);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(GalleryImage).apply(this, arguments));
    _this.onSelectImage = _this.onSelectImage.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelectCaption = _this.onSelectCaption.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onRemoveImage = _this.onRemoveImage.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.bindContainer = _this.bindContainer.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
          isSelected = _this$props2.isSelected,
          caption = _this$props2.caption,
          onRemove = _this$props2.onRemove,
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
      }, isSelected && Object(external_this_wp_element_["createElement"])("div", {
        className: "block-library-gallery-item__inline-menu"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        icon: "no-alt",
        onClick: onRemove,
        className: "blocks-gallery-item__remove",
        label: Object(external_this_wp_i18n_["__"])('Remove Image')
      })), href ? Object(external_this_wp_element_["createElement"])("a", {
        href: href
      }, img) : img, !external_this_wp_blockEditor_["RichText"].isEmpty(caption) || isSelected ? Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        tagName: "figcaption",
        placeholder: Object(external_this_wp_i18n_["__"])('Write caption…'),
        value: caption,
        isSelected: this.state.captionSelected,
        onChange: function onChange(newCaption) {
          return setAttributes({
            caption: newCaption
          });
        },
        unstableOnFocus: this.onSelectCaption,
        inlineToolbar: true
      }) : null);
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

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  fill: "none",
  d: "M0 0h24v24H0V0z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M20 4v12H8V4h12m0-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 9.67l1.69 2.26 2.48-3.1L19 15H9zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z"
}))));

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
var ALLOWED_MEDIA_TYPES = ['image'];
function defaultColumnsNumber(attributes) {
  return Math.min(3, attributes.images.length);
}
var edit_pickRelevantMediaFiles = function pickRelevantMediaFiles(image) {
  var imageProps = Object(external_lodash_["pick"])(image, ['alt', 'id', 'link', 'caption']);
  imageProps.url = Object(external_lodash_["get"])(image, ['sizes', 'large', 'url']) || Object(external_lodash_["get"])(image, ['media_details', 'sizes', 'large', 'source_url']) || image.url;
  return imageProps;
};

var edit_GalleryEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(GalleryEdit, _Component);

  function GalleryEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, GalleryEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(GalleryEdit).apply(this, arguments));
    _this.onSelectImage = _this.onSelectImage.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelectImages = _this.onSelectImages.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setLinkTo = _this.setLinkTo.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setColumnsNumber = _this.setColumnsNumber.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.toggleImageCrop = _this.toggleImageCrop.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onRemoveImage = _this.onRemoveImage.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setImageAttributes = _this.setImageAttributes.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.addFiles = _this.addFiles.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.uploadFromFiles = _this.uploadFromFiles.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setAttributes = _this.setAttributes.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.state = {
      selectedImage: null
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
    key: "onRemoveImage",
    value: function onRemoveImage(index) {
      var _this3 = this;

      return function () {
        var images = Object(external_lodash_["filter"])(_this3.props.attributes.images, function (img, i) {
          return index !== i;
        });
        var columns = _this3.props.attributes.columns;

        _this3.setState({
          selectedImage: null
        });

        _this3.setAttributes({
          images: images,
          columns: columns ? Math.min(images.length, columns) : columns
        });
      };
    }
  }, {
    key: "onSelectImages",
    value: function onSelectImages(images) {
      var columns = this.props.attributes.columns;
      this.setAttributes({
        images: images.map(function (image) {
          return edit_pickRelevantMediaFiles(image);
        }),
        columns: columns ? Math.min(images.length, columns) : columns
      });
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
    key: "uploadFromFiles",
    value: function uploadFromFiles(event) {
      this.addFiles(event.target.files);
    }
  }, {
    key: "addFiles",
    value: function addFiles(files) {
      var currentImages = this.props.attributes.images || [];
      var noticeOperations = this.props.noticeOperations;
      var setAttributes = this.setAttributes;
      Object(external_this_wp_editor_["mediaUpload"])({
        allowedTypes: ALLOWED_MEDIA_TYPES,
        filesList: files,
        onFileChange: function onFileChange(images) {
          var imagesNormalized = images.map(function (image) {
            return edit_pickRelevantMediaFiles(image);
          });
          setAttributes({
            images: currentImages.concat(imagesNormalized)
          });
        },
        onError: noticeOperations.createErrorNotice
      });
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
          _this4 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          isSelected = _this$props.isSelected,
          className = _this$props.className,
          noticeOperations = _this$props.noticeOperations,
          noticeUI = _this$props.noticeUI;
      var images = attributes.images,
          _attributes$columns = attributes.columns,
          columns = _attributes$columns === void 0 ? defaultColumnsNumber(attributes) : _attributes$columns,
          align = attributes.align,
          imageCrop = attributes.imageCrop,
          linkTo = attributes.linkTo;
      var dropZone = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["DropZone"], {
        onFilesDrop: this.addFiles
      });
      var controls = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, !!images.length && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
        onSelect: this.onSelectImages,
        allowedTypes: ALLOWED_MEDIA_TYPES,
        multiple: true,
        gallery: true,
        value: images.map(function (img) {
          return img.id;
        }),
        render: function render(_ref) {
          var open = _ref.open;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
            className: "components-toolbar__control",
            label: Object(external_this_wp_i18n_["__"])('Edit gallery'),
            icon: "edit",
            onClick: open
          });
        }
      })));

      if (images.length === 0) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, controls, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaPlaceholder"], {
          icon: Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockIcon"], {
            icon: icon
          }),
          className: className,
          labels: {
            title: Object(external_this_wp_i18n_["__"])('Gallery'),
            instructions: Object(external_this_wp_i18n_["__"])('Drag images, upload new ones or select files from your library.')
          },
          onSelect: this.onSelectImages,
          accept: "image/*",
          allowedTypes: ALLOWED_MEDIA_TYPES,
          multiple: true,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice
        }));
      }

      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, controls, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
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
      }))), noticeUI, Object(external_this_wp_element_["createElement"])("ul", {
        className: classnames_default()(className, (_classnames = {}, Object(defineProperty["a" /* default */])(_classnames, "align".concat(align), align), Object(defineProperty["a" /* default */])(_classnames, "columns-".concat(columns), columns), Object(defineProperty["a" /* default */])(_classnames, 'is-cropped', imageCrop), _classnames))
      }, dropZone, images.map(function (img, index) {
        /* translators: %1$d is the order number of the image, %2$d is the total number of images. */
        var ariaLabel = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('image %1$d of %2$d in gallery'), index + 1, images.length);
        return Object(external_this_wp_element_["createElement"])("li", {
          className: "blocks-gallery-item",
          key: img.id || img.url
        }, Object(external_this_wp_element_["createElement"])(gallery_image, {
          url: img.url,
          alt: img.alt,
          id: img.id,
          isSelected: isSelected && _this4.state.selectedImage === index,
          onRemove: _this4.onRemoveImage(index),
          onSelect: _this4.onSelectImage(index),
          setAttributes: function setAttributes(attrs) {
            return _this4.setImageAttributes(index, attrs);
          },
          caption: img.caption,
          "aria-label": ariaLabel
        }));
      }), isSelected && Object(external_this_wp_element_["createElement"])("li", {
        className: "blocks-gallery-item has-add-item-button"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["FormFileUpload"], {
        multiple: true,
        isLarge: true,
        className: "block-library-gallery-add-item-button",
        onChange: this.uploadFromFiles,
        accept: "image/*",
        icon: "insert"
      }, Object(external_this_wp_i18n_["__"])('Upload an image')))));
    }
  }]);

  return GalleryEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_components_["withNotices"])(edit_GalleryEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/gallery/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



var blockAttributes = {
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
      link: {
        source: 'attribute',
        selector: 'img',
        attribute: 'data-link'
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
      caption: {
        type: 'string',
        source: 'html',
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
};
var gallery_name = 'core/gallery';

var parseShortcodeIds = function parseShortcodeIds(ids) {
  if (!ids) {
    return [];
  }

  return ids.split(',').map(function (id) {
    return parseInt(id, 10);
  });
};

var settings = {
  title: Object(external_this_wp_i18n_["__"])('Gallery'),
  description: Object(external_this_wp_i18n_["__"])('Display multiple images in a rich gallery.'),
  icon: icon,
  category: 'common',
  keywords: [Object(external_this_wp_i18n_["__"])('images'), Object(external_this_wp_i18n_["__"])('photos')],
  attributes: blockAttributes,
  supports: {
    align: true
  },
  transforms: {
    from: [{
      type: 'block',
      isMultiBlock: true,
      blocks: ['core/image'],
      transform: function transform(attributes) {
        // Init the align attribute from the first item which may be either the placeholder or an image.
        var align = attributes[0].align; // Loop through all the images and check if they have the same align.

        align = Object(external_lodash_["every"])(attributes, ['align', align]) ? align : undefined;
        var validImages = Object(external_lodash_["filter"])(attributes, function (_ref) {
          var id = _ref.id,
              url = _ref.url;
          return id && url;
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
      transform: function transform(files, onChange) {
        var block = Object(external_this_wp_blocks_["createBlock"])('core/gallery', {
          images: files.map(function (file) {
            return edit_pickRelevantMediaFiles({
              url: Object(external_this_wp_blob_["createBlobURL"])(file)
            });
          })
        });
        Object(external_this_wp_editor_["mediaUpload"])({
          filesList: files,
          onFileChange: function onFileChange(images) {
            var imagesAttr = images.map(edit_pickRelevantMediaFiles);
            onChange(block.clientId, {
              ids: Object(external_lodash_["map"])(imagesAttr, 'id'),
              images: imagesAttr
            });
          },
          allowedTypes: ['image']
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
  },
  edit: edit,
  save: function save(_ref10) {
    var attributes = _ref10.attributes;
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
  },
  deprecated: [{
    attributes: blockAttributes,
    isEligible: function isEligible(_ref11) {
      var images = _ref11.images,
          ids = _ref11.ids;
      return images && images.length > 0 && (!ids && images || ids && images && ids.length !== images.length || Object(external_lodash_["some"])(images, function (id, index) {
        if (!id && ids[index] !== null) {
          return true;
        }

        return parseInt(id, 10) !== ids[index];
      }));
    },
    migrate: function migrate(attributes) {
      return Object(objectSpread["a" /* default */])({}, attributes, {
        ids: Object(external_lodash_["map"])(attributes.images, function (_ref12) {
          var id = _ref12.id;

          if (!id) {
            return null;
          }

          return parseInt(id, 10);
        })
      });
    },
    save: function save(_ref13) {
      var attributes = _ref13.attributes;
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
    attributes: blockAttributes,
    save: function save(_ref14) {
      var attributes = _ref14.attributes;
      var images = attributes.images,
          _attributes$columns3 = attributes.columns,
          columns = _attributes$columns3 === void 0 ? defaultColumnsNumber(attributes) : _attributes$columns3,
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
          "data-link": image.link
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
    attributes: Object(objectSpread["a" /* default */])({}, blockAttributes, {
      images: Object(objectSpread["a" /* default */])({}, blockAttributes.images, {
        selector: 'div.wp-block-gallery figure.blocks-gallery-image img'
      }),
      align: {
        type: 'string',
        default: 'none'
      }
    }),
    save: function save(_ref15) {
      var attributes = _ref15.attributes;
      var images = attributes.images,
          _attributes$columns4 = attributes.columns,
          columns = _attributes$columns4 === void 0 ? defaultColumnsNumber(attributes) : _attributes$columns4,
          align = attributes.align,
          imageCrop = attributes.imageCrop,
          linkTo = attributes.linkTo;
      return Object(external_this_wp_element_["createElement"])("div", {
        className: "align".concat(align, " columns-").concat(columns, " ").concat(imageCrop ? 'is-cropped' : '')
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
  }]
};


/***/ }),
/* 232 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ file_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blob"]}
var external_this_wp_blob_ = __webpack_require__(35);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(19);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(28);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
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
      label: Object(external_this_wp_i18n_["__"])('Attachment Page')
    }];
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Text Link Settings')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
    label: Object(external_this_wp_i18n_["__"])('Link To'),
    value: textLinkHref,
    options: linkDestinationOptions,
    onChange: changeLinkDestinationOption
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
    label: Object(external_this_wp_i18n_["__"])('Open in New Tab'),
    checked: openInNewWindow,
    onChange: changeOpenInNewWindow
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Download Button Settings')
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
    label: Object(external_this_wp_i18n_["__"])('Show Download Button'),
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
    _this.onSelectFile = _this.onSelectFile.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.confirmCopyURL = _this.confirmCopyURL.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.resetCopyConfirmation = _this.resetCopyConfirmation.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.changeLinkDestinationOption = _this.changeLinkDestinationOption.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.changeOpenInNewWindow = _this.changeOpenInNewWindow.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.changeShowDownloadButton = _this.changeShowDownloadButton.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
          noticeOperations = _this$props.noticeOperations;
      var href = attributes.href; // Upload a file drag-and-dropped into the editor

      if (Object(external_this_wp_blob_["isBlobURL"])(href)) {
        var file = Object(external_this_wp_blob_["getBlobByURL"])(href);
        Object(external_this_wp_editor_["mediaUpload"])({
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
      var _this$props2 = this.props,
          className = _this$props2.className,
          isSelected = _this$props2.isSelected,
          attributes = _this$props2.attributes,
          setAttributes = _this$props2.setAttributes,
          noticeUI = _this$props2.noticeUI,
          noticeOperations = _this$props2.noticeOperations,
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
            icon: icon
          }),
          labels: {
            title: Object(external_this_wp_i18n_["__"])('File'),
            instructions: Object(external_this_wp_i18n_["__"])('Drag a file, upload a new one or select a file from your library.')
          },
          onSelect: this.onSelectFile,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice,
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
      })))), Object(external_this_wp_element_["createElement"])("div", {
        className: classes
      }, Object(external_this_wp_element_["createElement"])("div", {
        className: 'wp-block-file__content-wrapper'
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        wrapperClassName: 'wp-block-file__textlink',
        tagName: "div" // must be block-level or else cursor disappears
        ,
        value: fileName,
        placeholder: Object(external_this_wp_i18n_["__"])('Write file name…'),
        keepPlaceholderOnFocus: true,
        formattingControls: [] // disable controls
        ,
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
        formattingControls: [] // disable controls
        ,
        placeholder: Object(external_this_wp_i18n_["__"])('Add text…'),
        keepPlaceholderOnFocus: true,
        onChange: function onChange(text) {
          return setAttributes({
            downloadButtonText: text
          });
        }
      }))), isSelected && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ClipboardButton"], {
        isDefault: true,
        text: href,
        className: 'wp-block-file__copy-url-button',
        onCopy: this.confirmCopyURL,
        onFinishCopy: this.resetCopyConfirmation,
        disabled: Object(external_this_wp_blob_["isBlobURL"])(href)
      }, showCopyConfirmation ? Object(external_this_wp_i18n_["__"])('Copied!') : Object(external_this_wp_i18n_["__"])('Copy URL'))));
    }
  }]);

  return FileEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, props) {
  var _select = select('core'),
      getMedia = _select.getMedia;

  var id = props.attributes.id;
  return {
    media: id === undefined ? undefined : getMedia(id)
  };
}), external_this_wp_components_["withNotices"]])(edit_FileEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



var file_name = 'core/file';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('File'),
  description: Object(external_this_wp_i18n_["__"])('Add a link to a downloadable file.'),
  icon: icon,
  category: 'common',
  keywords: [Object(external_this_wp_i18n_["__"])('document'), Object(external_this_wp_i18n_["__"])('pdf')],
  attributes: {
    id: {
      type: 'number'
    },
    href: {
      type: 'string'
    },
    fileName: {
      type: 'string',
      source: 'html',
      selector: 'a:not([download])'
    },
    // Differs to the href when the block is configured to link to the attachment page
    textLinkHref: {
      type: 'string',
      source: 'attribute',
      selector: 'a:not([download])',
      attribute: 'href'
    },
    // e.g. `_blank` when the block is configured to open in a new tab
    textLinkTarget: {
      type: 'string',
      source: 'attribute',
      selector: 'a:not([download])',
      attribute: 'target'
    },
    showDownloadButton: {
      type: 'boolean',
      default: true
    },
    downloadButtonText: {
      type: 'string',
      source: 'html',
      selector: 'a[download]',
      default: Object(external_this_wp_i18n_["_x"])('Download', 'button label')
    }
  },
  supports: {
    align: true
  },
  transforms: {
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
  },
  edit: edit,
  save: function save(_ref4) {
    var attributes = _ref4.attributes;
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
};


/***/ }),
/* 233 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ legacy_widget_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(33);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","isShallowEqual"]}
var external_this_wp_isShallowEqual_ = __webpack_require__(42);
var external_this_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_isShallowEqual_);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/legacy-widget/WidgetEditDomManager.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




var WidgetEditDomManager_WidgetEditDomManager =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(WidgetEditDomManager, _Component);

  function WidgetEditDomManager() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, WidgetEditDomManager);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(WidgetEditDomManager).apply(this, arguments));
    _this.containerRef = Object(external_this_wp_element_["createRef"])();
    _this.formRef = Object(external_this_wp_element_["createRef"])();
    _this.widgetContentRef = Object(external_this_wp_element_["createRef"])();
    _this.triggerWidgetEvent = _this.triggerWidgetEvent.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(WidgetEditDomManager, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.triggerWidgetEvent('widget-added');
      this.previousFormData = new window.FormData(this.formRef.current);
    }
  }, {
    key: "shouldComponentUpdate",
    value: function shouldComponentUpdate(nextProps) {
      // We can not leverage react render otherwise we would destroy dom changes applied by the plugins.
      // We manually update the required dom node replicating what the widget screen and the customizer do.
      if (nextProps.form !== this.props.form && this.widgetContentRef.current) {
        var widgetContent = this.widgetContentRef.current;
        widgetContent.innerHTML = nextProps.form;
        this.triggerWidgetEvent('widget-updated');
        this.previousFormData = new window.FormData(this.formRef.current);
      }

      return false;
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props = this.props,
          id = _this$props.id,
          idBase = _this$props.idBase,
          widgetNumber = _this$props.widgetNumber,
          form = _this$props.form;
      return Object(external_this_wp_element_["createElement"])("div", {
        className: "widget open",
        ref: this.containerRef
      }, Object(external_this_wp_element_["createElement"])("div", {
        className: "widget-inside"
      }, Object(external_this_wp_element_["createElement"])("form", {
        ref: this.formRef,
        method: "post",
        onBlur: function onBlur() {
          if (_this2.shouldTriggerInstanceUpdate()) {
            _this2.props.onInstanceChange(_this2.retrieveUpdatedInstance());
          }
        }
      }, Object(external_this_wp_element_["createElement"])("div", {
        ref: this.widgetContentRef,
        className: "widget-content",
        dangerouslySetInnerHTML: {
          __html: form
        }
      }), Object(external_this_wp_element_["createElement"])("input", {
        type: "hidden",
        name: "widget-id",
        className: "widget-id",
        value: id
      }), Object(external_this_wp_element_["createElement"])("input", {
        type: "hidden",
        name: "id_base",
        className: "id_base",
        value: idBase
      }), Object(external_this_wp_element_["createElement"])("input", {
        type: "hidden",
        name: "widget_number",
        className: "widget_number",
        value: widgetNumber
      }), Object(external_this_wp_element_["createElement"])("input", {
        type: "hidden",
        name: "multi_number",
        className: "multi_number",
        value: ""
      }), Object(external_this_wp_element_["createElement"])("input", {
        type: "hidden",
        name: "add_new",
        className: "add_new",
        value: ""
      }))));
    }
  }, {
    key: "shouldTriggerInstanceUpdate",
    value: function shouldTriggerInstanceUpdate() {
      if (!this.formRef.current) {
        return false;
      }

      if (!this.previousFormData) {
        return true;
      }

      var currentFormData = new window.FormData(this.formRef.current);
      var currentFormDataKeys = Array.from(currentFormData.keys());
      var previousFormDataKeys = Array.from(this.previousFormData.keys());

      if (currentFormDataKeys.length !== previousFormDataKeys.length) {
        return true;
      }

      for (var _i = 0; _i < currentFormDataKeys.length; _i++) {
        var rawKey = currentFormDataKeys[_i];

        if (!external_this_wp_isShallowEqual_default()(currentFormData.getAll(rawKey), this.previousFormData.getAll(rawKey))) {
          this.previousFormData = currentFormData;
          return true;
        }
      }

      return false;
    }
  }, {
    key: "triggerWidgetEvent",
    value: function triggerWidgetEvent(event) {
      window.$(window.document).trigger(event, [window.$(this.containerRef.current)]);
    }
  }, {
    key: "retrieveUpdatedInstance",
    value: function retrieveUpdatedInstance() {
      if (this.formRef.current) {
        var _this$props2 = this.props,
            idBase = _this$props2.idBase,
            widgetNumber = _this$props2.widgetNumber;
        var form = this.formRef.current;
        var formData = new window.FormData(form);
        var updatedInstance = {};
        var keyPrefixLength = "widget-".concat(idBase, "[").concat(widgetNumber, "][").length;
        var keySuffixLength = "]".length;
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
          for (var _iterator = formData.keys()[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var rawKey = _step.value;

            // This fields are added to the form because the widget JavaScript code may use this values.
            // They are not relevant for the update mechanism.
            if (Object(external_lodash_["includes"])(['widget-id', 'id_base', 'widget_number', 'multi_number', 'add_new'], rawKey)) {
              continue;
            }

            var keyParsed = rawKey.substring(keyPrefixLength, rawKey.length - keySuffixLength);
            var value = formData.getAll(rawKey);

            if (value.length > 1) {
              updatedInstance[keyParsed] = value;
            } else {
              updatedInstance[keyParsed] = value[0];
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

        return updatedInstance;
      }
    }
  }]);

  return WidgetEditDomManager;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var legacy_widget_WidgetEditDomManager = (WidgetEditDomManager_WidgetEditDomManager);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/legacy-widget/WidgetEditHandler.js








/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var WidgetEditHandler_WidgetEditHandler =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(WidgetEditHandler, _Component);

  function WidgetEditHandler() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, WidgetEditHandler);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(WidgetEditHandler).apply(this, arguments));
    _this.state = {
      form: null,
      idBase: null
    };
    _this.instanceUpdating = null;
    _this.onInstanceChange = _this.onInstanceChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.requestWidgetUpdater = _this.requestWidgetUpdater.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(WidgetEditHandler, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.isStillMounted = true;
      this.requestWidgetUpdater();
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      if (prevProps.instance !== this.props.instance && this.instanceUpdating !== this.props.instance) {
        this.requestWidgetUpdater();
      }

      if (this.instanceUpdating === this.props.instance) {
        this.instanceUpdating = null;
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.isStillMounted = false;
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props = this.props,
          instanceId = _this$props.instanceId,
          identifier = _this$props.identifier;
      var _this$state = this.state,
          id = _this$state.id,
          idBase = _this$state.idBase,
          form = _this$state.form;

      if (!identifier) {
        return Object(external_this_wp_i18n_["__"])('Not a valid widget.');
      }

      if (!form) {
        return null;
      }

      return Object(external_this_wp_element_["createElement"])("div", {
        className: "wp-block-legacy-widget__edit-container" // Display none is used because when we switch from edit to preview,
        // we don't want to unmount the component.
        // Otherwise when we went back to edit we wound need to trigger
        // all widgets events again and some scripts may not deal well with this.
        ,
        style: {
          display: this.props.isVisible ? 'block' : 'none'
        }
      }, Object(external_this_wp_element_["createElement"])(legacy_widget_WidgetEditDomManager, {
        ref: function ref(_ref) {
          _this2.widgetEditDomManagerRef = _ref;
        },
        onInstanceChange: this.onInstanceChange,
        widgetNumber: instanceId * -1,
        id: id,
        idBase: idBase,
        form: form
      }));
    }
  }, {
    key: "onInstanceChange",
    value: function onInstanceChange(instanceChanges) {
      var _this3 = this;

      this.requestWidgetUpdater(instanceChanges, function (response) {
        _this3.instanceUpdating = response.instance;

        _this3.props.onInstanceChange(response.instance);
      });
    }
  }, {
    key: "requestWidgetUpdater",
    value: function requestWidgetUpdater(instanceChanges, callback) {
      var _this4 = this;

      var _this$props2 = this.props,
          identifier = _this$props2.identifier,
          instanceId = _this$props2.instanceId,
          instance = _this$props2.instance;

      if (!identifier) {
        return;
      }

      external_this_wp_apiFetch_default()({
        path: "/wp/v2/widgets/".concat(identifier, "/"),
        data: {
          identifier: identifier,
          instance: instance,
          // use negative ids to make sure the id does not exist on the database.
          id_to_use: instanceId * -1,
          instance_changes: instanceChanges
        },
        method: 'POST'
      }).then(function (response) {
        if (_this4.isStillMounted) {
          _this4.setState({
            form: response.form,
            idBase: response.id_base,
            id: response.id
          });

          if (callback) {
            callback(response);
          }
        }
      });
    }
  }]);

  return WidgetEditHandler;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var legacy_widget_WidgetEditHandler = (Object(external_this_wp_compose_["withInstanceId"])(WidgetEditHandler_WidgetEditHandler));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/legacy-widget/edit.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




var edit_LegacyWidgetEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(LegacyWidgetEdit, _Component);

  function LegacyWidgetEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, LegacyWidgetEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(LegacyWidgetEdit).apply(this, arguments));
    _this.state = {
      isPreview: false
    };
    _this.switchToEdit = _this.switchToEdit.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.switchToPreview = _this.switchToPreview.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.changeWidget = _this.changeWidget.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(LegacyWidgetEdit, [{
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          availableLegacyWidgets = _this$props.availableLegacyWidgets,
          hasPermissionsToManageWidgets = _this$props.hasPermissionsToManageWidgets,
          setAttributes = _this$props.setAttributes;
      var isPreview = this.state.isPreview;
      var identifier = attributes.identifier,
          isCallbackWidget = attributes.isCallbackWidget;
      var widgetObject = identifier && availableLegacyWidgets[identifier];

      if (!widgetObject) {
        var placeholderContent;

        if (!hasPermissionsToManageWidgets) {
          placeholderContent = Object(external_this_wp_i18n_["__"])('You don\'t have permissions to use widgets on this site.');
        } else if (availableLegacyWidgets.length === 0) {
          placeholderContent = Object(external_this_wp_i18n_["__"])('There are no widgets available.');
        } else {
          placeholderContent = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SelectControl"], {
            label: Object(external_this_wp_i18n_["__"])('Select a legacy widget to display:'),
            value: identifier || 'none',
            onChange: function onChange(value) {
              return setAttributes({
                instance: {},
                identifier: value,
                isCallbackWidget: availableLegacyWidgets[value].isCallbackWidget
              });
            },
            options: [{
              value: 'none',
              label: 'Select widget'
            }].concat(Object(external_lodash_["map"])(availableLegacyWidgets, function (widget, key) {
              return {
                value: key,
                label: widget.name
              };
            }))
          });
        }

        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], {
          icon: Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["BlockIcon"], {
            icon: "admin-customizer"
          }),
          label: Object(external_this_wp_i18n_["__"])('Legacy Widget')
        }, placeholderContent);
      }

      var inspectorControls = Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: widgetObject.name
      }, widgetObject.description));

      if (!hasPermissionsToManageWidgets) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, inspectorControls, this.renderWidgetPreview());
      }

      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        onClick: this.changeWidget,
        label: Object(external_this_wp_i18n_["__"])('Change widget'),
        icon: "update"
      }), !isCallbackWidget && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: "components-tab-button ".concat(!isPreview ? 'is-active' : ''),
        onClick: this.switchToEdit
      }, Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_i18n_["__"])('Edit'))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        className: "components-tab-button ".concat(isPreview ? 'is-active' : ''),
        onClick: this.switchToPreview
      }, Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_i18n_["__"])('Preview')))))), inspectorControls, !isCallbackWidget && Object(external_this_wp_element_["createElement"])(legacy_widget_WidgetEditHandler, {
        isVisible: !isPreview,
        identifier: attributes.identifier,
        instance: attributes.instance,
        onInstanceChange: function onInstanceChange(newInstance) {
          _this2.props.setAttributes({
            instance: newInstance
          });
        }
      }), (isPreview || isCallbackWidget) && this.renderWidgetPreview());
    }
  }, {
    key: "changeWidget",
    value: function changeWidget() {
      this.switchToEdit();
      this.props.setAttributes({
        instance: {},
        identifier: undefined
      });
    }
  }, {
    key: "switchToEdit",
    value: function switchToEdit() {
      this.setState({
        isPreview: false
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
    key: "renderWidgetPreview",
    value: function renderWidgetPreview() {
      var attributes = this.props.attributes;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["ServerSideRender"], {
        className: "wp-block-legacy-widget__preview",
        block: "core/legacy-widget",
        attributes: attributes
      });
    }
  }]);

  return LegacyWidgetEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var editorSettings = select('core/block-editor').getSettings();
  var availableLegacyWidgets = editorSettings.availableLegacyWidgets,
      hasPermissionsToManageWidgets = editorSettings.hasPermissionsToManageWidgets;
  return {
    hasPermissionsToManageWidgets: hasPermissionsToManageWidgets,
    availableLegacyWidgets: availableLegacyWidgets
  };
})(edit_LegacyWidgetEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/legacy-widget/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var legacy_widget_name = 'core/legacy-widget';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Legacy Widget (Experimental)'),
  description: Object(external_this_wp_i18n_["__"])('Display a legacy widget.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M7 11h2v2H7v-2zm14-5v14l-2 2H5l-2-2V6l2-2h1V2h2v2h8V2h2v2h1l2 2zM5 8h14V6H5v2zm14 12V10H5v10h14zm-4-7h2v-2h-2v2zm-4 0h2v-2h-2v2z"
  }))),
  category: 'widgets',
  supports: {
    html: false
  },
  edit: edit,
  save: function save() {
    // Handled by PHP.
    return null;
  }
};


/***/ }),
/* 234 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ block_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(19);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(18);

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
    _this.handleFormSubmit = _this.handleFormSubmit.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.handleTitleChange = _this.handleTitleChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.handleTitleKeyDown = _this.handleTitleKeyDown.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
    _this.startEditing = _this.startEditing.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.stopEditing = _this.stopEditing.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setAttributes = _this.setAttributes.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setTitle = _this.setTitle.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.save = _this.save.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));

    if (reusableBlock && reusableBlock.isTemporary) {
      // Start in edit mode when we're working with a newly created reusable block
      _this.state = {
        isEditing: true,
        title: reusableBlock.title,
        changedAttributes: {}
      };
    } else {
      // Start in preview mode when we're working with an existing reusable block
      _this.state = {
        isEditing: false,
        title: null,
        changedAttributes: null
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
    key: "startEditing",
    value: function startEditing() {
      var reusableBlock = this.props.reusableBlock;
      this.setState({
        isEditing: true,
        title: reusableBlock.title,
        changedAttributes: {}
      });
    }
  }, {
    key: "stopEditing",
    value: function stopEditing() {
      this.setState({
        isEditing: false,
        title: null,
        changedAttributes: null
      });
    }
  }, {
    key: "setAttributes",
    value: function setAttributes(attributes) {
      this.setState(function (prevState) {
        if (prevState.changedAttributes !== null) {
          return {
            changedAttributes: Object(objectSpread["a" /* default */])({}, prevState.changedAttributes, attributes)
          };
        }
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
          reusableBlock = _this$props.reusableBlock,
          onUpdateTitle = _this$props.onUpdateTitle,
          updateAttributes = _this$props.updateAttributes,
          block = _this$props.block,
          onSave = _this$props.onSave;
      var _this$state = this.state,
          title = _this$state.title,
          changedAttributes = _this$state.changedAttributes;

      if (title !== reusableBlock.title) {
        onUpdateTitle(title);
      }

      updateAttributes(block.clientId, changedAttributes);
      onSave();
      this.stopEditing();
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          isSelected = _this$props2.isSelected,
          reusableBlock = _this$props2.reusableBlock,
          block = _this$props2.block,
          isFetching = _this$props2.isFetching,
          isSaving = _this$props2.isSaving,
          canUpdateBlock = _this$props2.canUpdateBlock;
      var _this$state2 = this.state,
          isEditing = _this$state2.isEditing,
          title = _this$state2.title,
          changedAttributes = _this$state2.changedAttributes;

      if (!reusableBlock && isFetching) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Spinner"], null));
      }

      if (!reusableBlock || !block) {
        return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Placeholder"], null, Object(external_this_wp_i18n_["__"])('Block has been deleted or is unavailable.'));
      }

      var element = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockEdit"], Object(esm_extends["a" /* default */])({}, this.props, {
        isSelected: isEditing && isSelected,
        clientId: block.clientId,
        name: block.name,
        attributes: Object(objectSpread["a" /* default */])({}, block.attributes, changedAttributes),
        setAttributes: isEditing ? this.setAttributes : external_lodash_["noop"]
      }));

      if (!isEditing) {
        element = Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, element);
      }

      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, (isSelected || isEditing) && Object(external_this_wp_element_["createElement"])(edit_panel, {
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

/* harmony default export */ var edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withSelect"])(function (select, ownProps) {
  var _select = select('core/editor'),
      getReusableBlock = _select.__experimentalGetReusableBlock,
      isFetchingReusableBlock = _select.__experimentalIsFetchingReusableBlock,
      isSavingReusableBlock = _select.__experimentalIsSavingReusableBlock;

  var _select2 = select('core'),
      canUser = _select2.canUser;

  var _select3 = select('core/block-editor'),
      getBlock = _select3.getBlock;

  var ref = ownProps.attributes.ref;
  var reusableBlock = getReusableBlock(ref);
  return {
    reusableBlock: reusableBlock,
    isFetching: isFetchingReusableBlock(ref),
    isSaving: isSavingReusableBlock(ref),
    block: reusableBlock ? getBlock(reusableBlock.clientId) : null,
    canUpdateBlock: !!reusableBlock && !reusableBlock.isTemporary && !!canUser('update', 'blocks', ref)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, ownProps) {
  var _dispatch = dispatch('core/editor'),
      fetchReusableBlocks = _dispatch.__experimentalFetchReusableBlocks,
      updateReusableBlockTitle = _dispatch.__experimentalUpdateReusableBlockTitle,
      saveReusableBlock = _dispatch.__experimentalSaveReusableBlock;

  var _dispatch2 = dispatch('core/block-editor'),
      updateBlockAttributes = _dispatch2.updateBlockAttributes;

  var ref = ownProps.attributes.ref;
  return {
    fetchReusableBlock: Object(external_lodash_["partial"])(fetchReusableBlocks, ref),
    updateAttributes: updateBlockAttributes,
    onUpdateTitle: Object(external_lodash_["partial"])(updateReusableBlockTitle, ref),
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
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Reusable Block'),
  category: 'reusable',
  description: Object(external_this_wp_i18n_["__"])('Create content, and save it for you and other contributors to reuse across your site. Update the block, and the changes apply everywhere it’s used.'),
  attributes: {
    ref: {
      type: 'number'
    }
  },
  supports: {
    customClassName: false,
    html: false,
    inserter: false
  },
  edit: edit,
  save: function save() {
    return null;
  }
};


/***/ }),
/* 235 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "getLevelFromHeadingNodeName", function() { return /* binding */ getLevelFromHeadingNodeName; });
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ heading_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__(21);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(17);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/heading-toolbar.js







/**
 * External dependencies
 */

/**
 * WordPress dependencies
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
        icon: 'heading',
        // translators: %s: heading level e.g: "1", "2", "3"
        title: Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('Heading %d'), targetLevel),
        isActive: targetLevel === selectedLevel,
        onClick: function onClick() {
          return onChange(targetLevel);
        },
        subscript: String(targetLevel)
      };
    }
  }, {
    key: "render",
    value: function render() {
      var _this = this;

      var _this$props = this.props,
          minLevel = _this$props.minLevel,
          maxLevel = _this$props.maxLevel,
          selectedLevel = _this$props.selectedLevel,
          onChange = _this$props.onChange;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
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
 * Internal dependencies
 */

/**
 * WordPress dependencies
 */






function HeadingEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes,
      mergeBlocks = _ref.mergeBlocks,
      insertBlocksAfter = _ref.insertBlocksAfter,
      onReplace = _ref.onReplace,
      className = _ref.className;
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
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
    title: Object(external_this_wp_i18n_["__"])('Heading Settings')
  }, Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('Level')), Object(external_this_wp_element_["createElement"])(heading_toolbar, {
    minLevel: 1,
    maxLevel: 7,
    selectedLevel: level,
    onChange: function onChange(newLevel) {
      return setAttributes({
        level: newLevel
      });
    }
  }), Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_i18n_["__"])('Text Alignment')), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["AlignmentToolbar"], {
    value: align,
    onChange: function onChange(nextAlign) {
      setAttributes({
        align: nextAlign
      });
    }
  }))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
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
    unstableOnSplit: insertBlocksAfter ? function (before, after) {
      setAttributes({
        content: before
      });

      for (var _len = arguments.length, blocks = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
        blocks[_key - 2] = arguments[_key];
      }

      insertBlocksAfter([].concat(blocks, [Object(external_this_wp_blocks_["createBlock"])('core/paragraph', {
        content: after
      })]));
    } : undefined,
    onRemove: function onRemove() {
      return onReplace([]);
    },
    style: {
      textAlign: align
    },
    className: className,
    placeholder: placeholder || Object(external_this_wp_i18n_["__"])('Write heading…')
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/heading/index.js





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
 * Given a node name string for a heading node, returns its numeric level.
 *
 * @param {string} nodeName Heading node name.
 *
 * @return {number} Heading level.
 */

function getLevelFromHeadingNodeName(nodeName) {
  return Number(nodeName.substr(1));
}
var supports = {
  className: false,
  anchor: true
};
var schema = {
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
  align: {
    type: 'string'
  },
  placeholder: {
    type: 'string'
  }
};
var heading_name = 'core/heading';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Heading'),
  description: Object(external_this_wp_i18n_["__"])('Introduce new sections and organize content to help visitors (and search engines) understand the structure of your content.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M5 4v3h5.5v12h3V7H19V4z"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  })),
  category: 'common',
  keywords: [Object(external_this_wp_i18n_["__"])('title'), Object(external_this_wp_i18n_["__"])('subtitle')],
  supports: supports,
  attributes: schema,
  transforms: {
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
  },
  deprecated: [{
    supports: supports,
    attributes: Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(schema, ['level']), {
      nodeName: {
        type: 'string',
        source: 'property',
        selector: 'h1,h2,h3,h4,h5,h6',
        property: 'nodeName',
        default: 'H2'
      }
    }),
    migrate: function migrate(attributes) {
      var nodeName = attributes.nodeName,
          migratedAttributes = Object(objectWithoutProperties["a" /* default */])(attributes, ["nodeName"]);

      return Object(objectSpread["a" /* default */])({}, migratedAttributes, {
        level: getLevelFromHeadingNodeName(nodeName)
      });
    },
    save: function save(_ref3) {
      var attributes = _ref3.attributes;
      var align = attributes.align,
          nodeName = attributes.nodeName,
          content = attributes.content;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
        tagName: nodeName.toLowerCase(),
        style: {
          textAlign: align
        },
        value: content
      });
    }
  }],
  merge: function merge(attributes, attributesToMerge) {
    return {
      content: (attributes.content || '') + (attributesToMerge.content || '')
    };
  },
  edit: HeadingEdit,
  save: function save(_ref4) {
    var attributes = _ref4.attributes;
    var align = attributes.align,
        level = attributes.level,
        content = attributes.content;
    var tagName = 'h' + level;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: tagName,
      style: {
        textAlign: align
      },
      value: content
    });
  }
};


/***/ }),
/* 236 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ audio_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","blob"]}
var external_this_wp_blob_ = __webpack_require__(35);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(28);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/audio/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0,0h24v24H0V0z",
  fill: "none"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "m12 3l0.01 10.55c-0.59-0.34-1.27-0.55-2-0.55-2.22 0-4.01 1.79-4.01 4s1.79 4 4.01 4 3.99-1.79 3.99-4v-10h4v-4h-6zm-1.99 16c-1.1 0-2-0.9-2-2s0.9-2 2-2 2 0.9 2 2-0.9 2-2 2z"
})));

// EXTERNAL MODULE: ./node_modules/@wordpress/block-library/build-module/embed/util.js
var util = __webpack_require__(53);

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


var ALLOWED_MEDIA_TYPES = ['audio'];

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
    _this.toggleAttribute = _this.toggleAttribute.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelectURL = _this.onSelectURL.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(AudioEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          noticeOperations = _this$props.noticeOperations,
          setAttributes = _this$props.setAttributes;
      var id = attributes.id,
          _attributes$src = attributes.src,
          src = _attributes$src === void 0 ? '' : _attributes$src;

      if (!id && Object(external_this_wp_blob_["isBlobURL"])(src)) {
        var file = Object(external_this_wp_blob_["getBlobByURL"])(src);

        if (file) {
          Object(external_this_wp_editor_["mediaUpload"])({
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
            allowedTypes: ALLOWED_MEDIA_TYPES
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
        var embedBlock = Object(util["a" /* createUpgradedEmbedBlock */])({
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
          noticeOperations = _this$props3.noticeOperations,
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
            icon: icon
          }),
          className: className,
          onSelect: onSelectAudio,
          onSelectURL: this.onSelectURL,
          accept: "audio/*",
          allowedTypes: ALLOWED_MEDIA_TYPES,
          value: this.props.attributes,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice
        });
      }
      /* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */


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
        checked: autoplay
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
      /* eslint-enable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */
    }
  }]);

  return AudioEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_components_["withNotices"])(edit_AudioEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/audio/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var audio_name = 'core/audio';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Audio'),
  description: Object(external_this_wp_i18n_["__"])('Embed a simple audio player.'),
  icon: icon,
  category: 'common',
  attributes: {
    src: {
      type: 'string',
      source: 'attribute',
      selector: 'audio',
      attribute: 'src'
    },
    caption: {
      type: 'string',
      source: 'html',
      selector: 'figcaption'
    },
    id: {
      type: 'number'
    },
    autoplay: {
      type: 'boolean',
      source: 'attribute',
      selector: 'audio',
      attribute: 'autoplay'
    },
    loop: {
      type: 'boolean',
      source: 'attribute',
      selector: 'audio',
      attribute: 'loop'
    },
    preload: {
      type: 'string',
      source: 'attribute',
      selector: 'audio',
      attribute: 'preload'
    }
  },
  transforms: {
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
          type: 'srting',
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
  },
  supports: {
    align: true
  },
  edit: edit,
  save: function save(_ref5) {
    var attributes = _ref5.attributes;
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
};


/***/ }),
/* 237 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ cover_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/cover/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M4 4h7V2H4c-1.1 0-2 .9-2 2v7h2V4zm6 9l-4 5h12l-3-4-2.03 2.71L10 13zm7-4.5c0-.83-.67-1.5-1.5-1.5S14 7.67 14 8.5s.67 1.5 1.5 1.5S17 9.33 17 8.5zM20 2h-7v2h7v7h2V4c0-1.1-.9-2-2-2zm0 18h-7v2h7c1.1 0 2-.9 2-2v-7h-2v7zM4 13H2v7c0 1.1.9 2 2 2h7v-2H4v-7z"
}), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
  d: "M0 0h24v24H0z",
  fill: "none"
})));

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: ./node_modules/fast-average-color/dist/index.js
var dist = __webpack_require__(211);
var dist_default = /*#__PURE__*/__webpack_require__.n(dist);

// EXTERNAL MODULE: ./node_modules/tinycolor2/tinycolor.js
var tinycolor = __webpack_require__(45);
var tinycolor_default = /*#__PURE__*/__webpack_require__.n(tinycolor);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

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

var IMAGE_BACKGROUND_TYPE = 'image';
var VIDEO_BACKGROUND_TYPE = 'video';
var ALLOWED_MEDIA_TYPES = ['image', 'video'];
var INNER_BLOCKS_TEMPLATE = [['core/paragraph', {
  align: 'center',
  fontSize: 'large',
  placeholder: Object(external_this_wp_i18n_["__"])('Write title…')
}]];
var INNER_BLOCKS_ALLOWED_BLOCKS = ['core/button', 'core/heading', 'core/paragraph'];

function retrieveFastAverageColor() {
  if (!retrieveFastAverageColor.fastAverageColor) {
    retrieveFastAverageColor.fastAverageColor = new dist_default.a();
  }

  return retrieveFastAverageColor.fastAverageColor;
}

function backgroundImageStyles(url) {
  return url ? {
    backgroundImage: "url(".concat(url, ")")
  } : {};
}
function dimRatioToClass(ratio) {
  return ratio === 0 || ratio === 50 ? null : 'has-background-dim-' + 10 * Math.round(ratio / 10);
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
      isDark: false
    };
    _this.imageRef = Object(external_this_wp_element_["createRef"])();
    _this.videoRef = Object(external_this_wp_element_["createRef"])();
    _this.changeIsDarkIfRequired = _this.changeIsDarkIfRequired.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
    key: "render",
    value: function render() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes,
          className = _this$props.className,
          noticeOperations = _this$props.noticeOperations,
          noticeUI = _this$props.noticeUI,
          overlayColor = _this$props.overlayColor,
          setOverlayColor = _this$props.setOverlayColor;
      var backgroundType = attributes.backgroundType,
          dimRatio = attributes.dimRatio,
          focalPoint = attributes.focalPoint,
          hasParallax = attributes.hasParallax,
          id = attributes.id,
          url = attributes.url;

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

        setAttributes({
          url: media.url,
          id: media.id,
          backgroundType: mediaType
        });
      };

      var toggleParallax = function toggleParallax() {
        return setAttributes({
          hasParallax: !hasParallax
        });
      };

      var setDimRatio = function setDimRatio(ratio) {
        return setAttributes({
          dimRatio: ratio
        });
      };

      var style = Object(objectSpread["a" /* default */])({}, backgroundType === IMAGE_BACKGROUND_TYPE ? backgroundImageStyles(url) : {}, {
        backgroundColor: overlayColor.color
      });

      if (focalPoint) {
        style.backgroundPosition = "".concat(focalPoint.x * 100, "% ").concat(focalPoint.y * 100, "%");
      }

      var controls = Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["BlockControls"], null, !!url && Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["MediaUpload"], {
        onSelect: onSelectMedia,
        allowedTypes: ALLOWED_MEDIA_TYPES,
        value: id,
        render: function render(_ref) {
          var open = _ref.open;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
            className: "components-toolbar__control",
            label: Object(external_this_wp_i18n_["__"])('Edit media'),
            icon: "edit",
            onClick: open
          });
        }
      }))))), !!url && Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Cover Settings')
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
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["PanelColorSettings"], {
        title: Object(external_this_wp_i18n_["__"])('Overlay'),
        initialOpen: true,
        colorSettings: [{
          value: overlayColor.color,
          onChange: setOverlayColor,
          label: Object(external_this_wp_i18n_["__"])('Overlay Color')
        }]
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
        label: Object(external_this_wp_i18n_["__"])('Background Opacity'),
        value: dimRatio,
        onChange: setDimRatio,
        min: 0,
        max: 100,
        step: 10,
        required: true
      })))));

      if (!url) {
        var placeholderIcon = Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["BlockIcon"], {
          icon: icon
        });

        var label = Object(external_this_wp_i18n_["__"])('Cover');

        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, controls, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["MediaPlaceholder"], {
          icon: placeholderIcon,
          className: className,
          labels: {
            title: label,
            instructions: Object(external_this_wp_i18n_["__"])('Drag an image or a video, upload a new one or select a file from your library.')
          },
          onSelect: onSelectMedia,
          accept: "image/*,video/*",
          allowedTypes: ALLOWED_MEDIA_TYPES,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice
        }));
      }

      var classes = classnames_default()(className, dimRatioToClass(dimRatio), {
        'is-dark-theme': this.state.isDark,
        'has-background-dim': dimRatio !== 0,
        'has-parallax': hasParallax
      });
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, controls, Object(external_this_wp_element_["createElement"])("div", {
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
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["InnerBlocks"], {
        template: INNER_BLOCKS_TEMPLATE,
        allowedBlocks: INNER_BLOCKS_ALLOWED_BLOCKS
      }))));
    }
  }, {
    key: "handleBackgroundMode",
    value: function handleBackgroundMode(prevProps) {
      var _this2 = this;

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
        _this2.changeIsDarkIfRequired(color.isDark);
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

/* harmony default export */ var edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_editor_["withColors"])({
  overlayColor: 'background-color'
}), external_this_wp_components_["withNotices"]])(edit_CoverEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/cover/index.js




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var blockAttributes = {
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
var cover_name = 'core/cover';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Cover'),
  description: Object(external_this_wp_i18n_["__"])('Add an image or video with a text overlay — great for headers.'),
  icon: icon,
  category: 'common',
  attributes: blockAttributes,
  supports: {
    align: true
  },
  transforms: {
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
  },
  save: function save(_ref7) {
    var attributes = _ref7.attributes;
    var backgroundType = attributes.backgroundType,
        customOverlayColor = attributes.customOverlayColor,
        dimRatio = attributes.dimRatio,
        focalPoint = attributes.focalPoint,
        hasParallax = attributes.hasParallax,
        overlayColor = attributes.overlayColor,
        url = attributes.url;
    var overlayColorClass = Object(external_this_wp_blockEditor_["getColorClassName"])('background-color', overlayColor);
    var style = backgroundType === IMAGE_BACKGROUND_TYPE ? backgroundImageStyles(url) : {};

    if (!overlayColorClass) {
      style.backgroundColor = customOverlayColor;
    }

    if (focalPoint && !hasParallax) {
      style.backgroundPosition = "".concat(focalPoint.x * 100, "% ").concat(focalPoint.y * 100, "%");
    }

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
  },
  edit: edit,
  deprecated: [{
    attributes: Object(objectSpread["a" /* default */])({}, blockAttributes, {
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
    save: function save(_ref8) {
      var attributes = _ref8.attributes;
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
    attributes: Object(objectSpread["a" /* default */])({}, blockAttributes, {
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
    save: function save(_ref9) {
      var attributes = _ref9.attributes;
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
    }
  }, {
    attributes: Object(objectSpread["a" /* default */])({}, blockAttributes, {
      align: {
        type: 'string'
      },
      title: {
        type: 'string',
        source: 'html',
        selector: 'h2'
      },
      contentAlign: {
        type: 'string',
        default: 'center'
      }
    }),
    save: function save(_ref10) {
      var attributes = _ref10.attributes;
      var url = attributes.url,
          title = attributes.title,
          hasParallax = attributes.hasParallax,
          dimRatio = attributes.dimRatio,
          align = attributes.align;
      var style = backgroundImageStyles(url);
      var classes = classnames_default()(dimRatioToClass(dimRatio), {
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
    }
  }]
};


/***/ }),
/* 238 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ table_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(17);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/table/state.js




/**
 * External dependencies
 */

/**
 * Creates a table state.
 *
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
 * Updates cell content in the table state.
 *
 * @param {Object} state               Current table state.
 * @param {string} options.section     Section of the cell to update.
 * @param {number} options.rowIndex    Row index of the cell to update.
 * @param {number} options.columnIndex Column index of the cell to update.
 * @param {Array}  options.content     Content to set for the cell.
 *
 * @return {Object} New table state.
 */

function updateCellContent(state, _ref2) {
  var section = _ref2.section,
      rowIndex = _ref2.rowIndex,
      columnIndex = _ref2.columnIndex,
      content = _ref2.content;
  return Object(defineProperty["a" /* default */])({}, section, state[section].map(function (row, currentRowIndex) {
    if (currentRowIndex !== rowIndex) {
      return row;
    }

    return {
      cells: row.cells.map(function (cell, currentColumnIndex) {
        if (currentColumnIndex !== columnIndex) {
          return cell;
        }

        return Object(objectSpread["a" /* default */])({}, cell, {
          content: content
        });
      })
    };
  }));
}
/**
 * Inserts a row in the table state.
 *
 * @param {Object} state            Current table state.
 * @param {string} options.section  Section in which to insert the row.
 * @param {number} options.rowIndex Row index at which to insert the row.
 *
 * @return {Object} New table state.
 */

function insertRow(state, _ref4) {
  var section = _ref4.section,
      rowIndex = _ref4.rowIndex;
  var cellCount = state[section][0].cells.length;
  return Object(defineProperty["a" /* default */])({}, section, [].concat(Object(toConsumableArray["a" /* default */])(state[section].slice(0, rowIndex)), [{
    cells: Object(external_lodash_["times"])(cellCount, function () {
      return {
        content: '',
        tag: 'td'
      };
    })
  }], Object(toConsumableArray["a" /* default */])(state[section].slice(rowIndex))));
}
/**
 * Deletes a row from the table state.
 *
 * @param {Object} state            Current table state.
 * @param {string} options.section  Section in which to delete the row.
 * @param {number} options.rowIndex Row index to delete.
 *
 * @return {Object} New table state.
 */

function deleteRow(state, _ref6) {
  var section = _ref6.section,
      rowIndex = _ref6.rowIndex;
  return Object(defineProperty["a" /* default */])({}, section, state[section].filter(function (row, index) {
    return index !== rowIndex;
  }));
}
/**
 * Inserts a column in the table state.
 *
 * @param {Object} state               Current table state.
 * @param {string} options.section     Section in which to insert the column.
 * @param {number} options.columnIndex Column index at which to insert the column.
 *
 * @return {Object} New table state.
 */

function insertColumn(state, _ref8) {
  var section = _ref8.section,
      columnIndex = _ref8.columnIndex;
  return Object(defineProperty["a" /* default */])({}, section, state[section].map(function (row) {
    return {
      cells: [].concat(Object(toConsumableArray["a" /* default */])(row.cells.slice(0, columnIndex)), [{
        content: '',
        tag: 'td'
      }], Object(toConsumableArray["a" /* default */])(row.cells.slice(columnIndex)))
    };
  }));
}
/**
 * Deletes a column from the table state.
 *
 * @param {Object} state               Current table state.
 * @param {string} options.section     Section in which to delete the column.
 * @param {number} options.columnIndex Column index to delete.
 *
 * @return {Object} New table state.
 */

function deleteColumn(state, _ref10) {
  var section = _ref10.section,
      columnIndex = _ref10.columnIndex;
  return Object(defineProperty["a" /* default */])({}, section, state[section].map(function (row) {
    return {
      cells: row.cells.filter(function (cell, index) {
        return index !== columnIndex;
      })
    };
  }).filter(function (row) {
    return row.cells.length;
  }));
}

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
var withCustomBackgroundColors = Object(external_this_wp_blockEditor_["createCustomColorsHOC"])(BACKGROUND_COLORS);
var edit_TableEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(TableEdit, _Component);

  function TableEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, TableEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(TableEdit).apply(this, arguments));
    _this.onCreateTable = _this.onCreateTable.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onChangeFixedLayout = _this.onChangeFixedLayout.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onChange = _this.onChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onChangeInitialColumnCount = _this.onChangeInitialColumnCount.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onChangeInitialRowCount = _this.onChangeInitialRowCount.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.renderSection = _this.renderSection.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.getTableControls = _this.getTableControls.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onInsertRow = _this.onInsertRow.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onInsertRowBefore = _this.onInsertRowBefore.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onInsertRowAfter = _this.onInsertRowAfter.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onDeleteRow = _this.onDeleteRow.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onInsertColumn = _this.onInsertColumn.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onInsertColumnBefore = _this.onInsertColumnBefore.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onInsertColumnAfter = _this.onInsertColumnAfter.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onDeleteColumn = _this.onDeleteColumn.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
      var section = selectedCell.section,
          rowIndex = selectedCell.rowIndex,
          columnIndex = selectedCell.columnIndex;
      setAttributes(updateCellContent(attributes, {
        section: section,
        rowIndex: rowIndex,
        columnIndex: columnIndex,
        content: content
      }));
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

      var _this$props3 = this.props,
          attributes = _this$props3.attributes,
          setAttributes = _this$props3.setAttributes;
      var section = selectedCell.section,
          rowIndex = selectedCell.rowIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(insertRow(attributes, {
        section: section,
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

      var _this$props4 = this.props,
          attributes = _this$props4.attributes,
          setAttributes = _this$props4.setAttributes;
      var section = selectedCell.section,
          rowIndex = selectedCell.rowIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(deleteRow(attributes, {
        section: section,
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

      var _this$props5 = this.props,
          attributes = _this$props5.attributes,
          setAttributes = _this$props5.setAttributes;
      var section = selectedCell.section,
          columnIndex = selectedCell.columnIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(insertColumn(attributes, {
        section: section,
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

      var _this$props6 = this.props,
          attributes = _this$props6.attributes,
          setAttributes = _this$props6.setAttributes;
      var section = selectedCell.section,
          columnIndex = selectedCell.columnIndex;
      this.setState({
        selectedCell: null
      });
      setAttributes(deleteColumn(attributes, {
        section: section,
        columnIndex: columnIndex
      }));
    }
    /**
     * Creates an onFocus handler for a specified cell.
     *
     * @param {Object} selectedCell Object with `section`, `rowIndex`, and
     *                              `columnIndex` properties.
     *
     * @return {Function} Function to call on focus.
     */

  }, {
    key: "createOnFocus",
    value: function createOnFocus(selectedCell) {
      var _this2 = this;

      return function () {
        _this2.setState({
          selectedCell: selectedCell
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
     * @param {string} options.type Section type: head, body, or foot.
     * @param {Array}  options.rows The rows to render.
     *
     * @return {Object} React element for the section.
     */

  }, {
    key: "renderSection",
    value: function renderSection(_ref) {
      var _this3 = this;

      var type = _ref.type,
          rows = _ref.rows;

      if (!rows.length) {
        return null;
      }

      var Tag = "t".concat(type);
      var selectedCell = this.state.selectedCell;
      return Object(external_this_wp_element_["createElement"])(Tag, null, rows.map(function (_ref2, rowIndex) {
        var cells = _ref2.cells;
        return Object(external_this_wp_element_["createElement"])("tr", {
          key: rowIndex
        }, cells.map(function (_ref3, columnIndex) {
          var content = _ref3.content,
              CellTag = _ref3.tag;
          var isSelected = selectedCell && type === selectedCell.section && rowIndex === selectedCell.rowIndex && columnIndex === selectedCell.columnIndex;
          var cell = {
            section: type,
            rowIndex: rowIndex,
            columnIndex: columnIndex
          };
          var cellClasses = classnames_default()({
            'is-selected': isSelected
          });
          return Object(external_this_wp_element_["createElement"])(CellTag, {
            key: columnIndex,
            className: cellClasses
          }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
            className: "wp-block-table__cell-content",
            value: content,
            onChange: _this3.onChange,
            unstableOnFocus: _this3.createOnFocus(cell)
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
      var _this$props7 = this.props,
          attributes = _this$props7.attributes,
          className = _this$props7.className,
          backgroundColor = _this$props7.backgroundColor,
          setBackgroundColor = _this$props7.setBackgroundColor;
      var _this$state2 = this.state,
          initialRowCount = _this$state2.initialRowCount,
          initialColumnCount = _this$state2.initialColumnCount;
      var hasFixedLayout = attributes.hasFixedLayout,
          head = attributes.head,
          body = attributes.body,
          foot = attributes.foot;
      var isEmpty = !head.length && !body.length && !foot.length;
      var Section = this.renderSection;

      if (isEmpty) {
        return Object(external_this_wp_element_["createElement"])("form", {
          onSubmit: this.onCreateTable
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          type: "number",
          label: Object(external_this_wp_i18n_["__"])('Column Count'),
          value: initialColumnCount,
          onChange: this.onChangeInitialColumnCount,
          min: "1"
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          type: "number",
          label: Object(external_this_wp_i18n_["__"])('Row Count'),
          value: initialRowCount,
          onChange: this.onChangeInitialRowCount,
          min: "1"
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
          isPrimary: true,
          type: "submit"
        }, Object(external_this_wp_i18n_["__"])('Create')));
      }

      var classes = classnames_default()(className, backgroundColor.class, {
        'has-fixed-layout': hasFixedLayout,
        'has-background': !!backgroundColor.color
      });
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["DropdownMenu"], {
        icon: "editor-table",
        label: Object(external_this_wp_i18n_["__"])('Edit table'),
        controls: this.getTableControls()
      }))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Table Settings'),
        className: "blocks-table-settings"
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Fixed width table cells'),
        checked: !!hasFixedLayout,
        onChange: this.onChangeFixedLayout
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
      })), Object(external_this_wp_element_["createElement"])("table", {
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
  }]);

  return TableEdit;
}(external_this_wp_element_["Component"]);
/* harmony default export */ var edit = (withCustomBackgroundColors('backgroundColor')(edit_TableEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/table/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


var tableContentPasteSchema = {
  tr: {
    allowEmpty: true,
    children: {
      th: {
        allowEmpty: true,
        children: Object(external_this_wp_blocks_["getPhrasingContentSchema"])()
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

function getTableSectionAttributeSchema(section) {
  return {
    type: 'array',
    default: [],
    source: 'query',
    selector: "t".concat(section, " tr"),
    query: {
      cells: {
        type: 'array',
        default: [],
        source: 'query',
        selector: 'td,th',
        query: {
          content: {
            type: 'string',
            source: 'html'
          },
          tag: {
            type: 'string',
            default: 'td',
            source: 'tag'
          }
        }
      }
    }
  };
}

var table_name = 'core/table';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Table'),
  description: Object(external_this_wp_i18n_["__"])('Insert a table — perfect for sharing charts and data.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M20 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h15c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 2v3H5V5h15zm-5 14h-5v-9h5v9zM5 10h3v9H5v-9zm12 9v-9h3v9h-3z"
  }))),
  category: 'formatting',
  attributes: {
    hasFixedLayout: {
      type: 'boolean',
      default: false
    },
    backgroundColor: {
      type: 'string'
    },
    head: getTableSectionAttributeSchema('head'),
    body: getTableSectionAttributeSchema('body'),
    foot: getTableSectionAttributeSchema('foot')
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
  transforms: {
    from: [{
      type: 'raw',
      selector: 'table',
      schema: tablePasteSchema
    }]
  },
  edit: edit,
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
              tag = _ref4.tag;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
            tagName: tag,
            value: content,
            key: cellIndex
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
};


/***/ }),
/* 239 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ video_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","blob"]}
var external_this_wp_blob_ = __webpack_require__(35);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(28);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

// EXTERNAL MODULE: ./node_modules/@wordpress/block-library/build-module/embed/util.js
var util = __webpack_require__(53);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/video/icon.js


/**
 * WordPress dependencies
 */

/* harmony default export */ var icon = (Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
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



var ALLOWED_MEDIA_TYPES = ['video'];
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
    _this.toggleAttribute = _this.toggleAttribute.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelectURL = _this.onSelectURL.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSelectPoster = _this.onSelectPoster.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onRemovePoster = _this.onRemovePoster.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(VideoEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          noticeOperations = _this$props.noticeOperations,
          setAttributes = _this$props.setAttributes;
      var id = attributes.id,
          _attributes$src = attributes.src,
          src = _attributes$src === void 0 ? '' : _attributes$src;

      if (!id && Object(external_this_wp_blob_["isBlobURL"])(src)) {
        var file = Object(external_this_wp_blob_["getBlobByURL"])(src);

        if (file) {
          Object(external_this_wp_editor_["mediaUpload"])({
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
            allowedTypes: ALLOWED_MEDIA_TYPES
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
        var embedBlock = Object(util["a" /* createUpgradedEmbedBlock */])({
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
    key: "render",
    value: function render() {
      var _this4 = this;

      var _this$props$attribute = this.props.attributes,
          autoplay = _this$props$attribute.autoplay,
          caption = _this$props$attribute.caption,
          controls = _this$props$attribute.controls,
          loop = _this$props$attribute.loop,
          muted = _this$props$attribute.muted,
          poster = _this$props$attribute.poster,
          preload = _this$props$attribute.preload,
          src = _this$props$attribute.src;
      var _this$props3 = this.props,
          setAttributes = _this$props3.setAttributes,
          isSelected = _this$props3.isSelected,
          className = _this$props3.className,
          noticeOperations = _this$props3.noticeOperations,
          noticeUI = _this$props3.noticeUI;
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
            icon: icon
          }),
          className: className,
          onSelect: onSelectVideo,
          onSelectURL: this.onSelectURL,
          accept: "video/*",
          allowedTypes: ALLOWED_MEDIA_TYPES,
          value: this.props.attributes,
          notices: noticeUI,
          onError: noticeOperations.createErrorNotice
        });
      }
      /* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */


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
        checked: autoplay
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
        className: "editor-video-poster-control",
        label: Object(external_this_wp_i18n_["__"])('Poster Image')
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
        title: Object(external_this_wp_i18n_["__"])('Select Poster Image'),
        onSelect: this.onSelectPoster,
        allowedTypes: VIDEO_POSTER_ALLOWED_MEDIA_TYPES,
        render: function render(_ref3) {
          var open = _ref3.open;
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
            isDefault: true,
            onClick: open,
            ref: _this4.posterImageButton
          }, !_this4.props.attributes.poster ? Object(external_this_wp_i18n_["__"])('Select Poster Image') : Object(external_this_wp_i18n_["__"])('Replace image'));
        }
      }), !!this.props.attributes.poster && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
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
      /* eslint-enable jsx-a11y/no-static-element-interactions, jsx-a11y/onclick-has-role, jsx-a11y/click-events-have-key-events */
    }
  }]);

  return VideoEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_components_["withNotices"])(edit_VideoEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/video/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var video_name = 'core/video';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Video'),
  description: Object(external_this_wp_i18n_["__"])('Embed a video from your media library or upload a new one.'),
  icon: icon,
  keywords: [Object(external_this_wp_i18n_["__"])('movie')],
  category: 'common',
  attributes: {
    autoplay: {
      type: 'boolean',
      source: 'attribute',
      selector: 'video',
      attribute: 'autoplay'
    },
    caption: {
      type: 'string',
      source: 'html',
      selector: 'figcaption'
    },
    controls: {
      type: 'boolean',
      source: 'attribute',
      selector: 'video',
      attribute: 'controls',
      default: true
    },
    id: {
      type: 'number'
    },
    loop: {
      type: 'boolean',
      source: 'attribute',
      selector: 'video',
      attribute: 'loop'
    },
    muted: {
      type: 'boolean',
      source: 'attribute',
      selector: 'video',
      attribute: 'muted'
    },
    poster: {
      type: 'string',
      source: 'attribute',
      selector: 'video',
      attribute: 'poster'
    },
    preload: {
      type: 'string',
      source: 'attribute',
      selector: 'video',
      attribute: 'preload',
      default: 'metadata'
    },
    src: {
      type: 'string',
      source: 'attribute',
      selector: 'video',
      attribute: 'src'
    }
  },
  transforms: {
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
            var src = _ref.named.src;
            return src;
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
  },
  supports: {
    align: true
  },
  edit: edit,
  save: function save(_ref6) {
    var attributes = _ref6.attributes;
    var autoplay = attributes.autoplay,
        caption = attributes.caption,
        controls = attributes.controls,
        loop = attributes.loop,
        muted = attributes.muted,
        poster = attributes.poster,
        preload = attributes.preload,
        src = attributes.src;
    return Object(external_this_wp_element_["createElement"])("figure", null, src && Object(external_this_wp_element_["createElement"])("video", {
      autoPlay: autoplay,
      controls: controls,
      loop: loop,
      muted: muted,
      poster: poster,
      preload: preload !== 'metadata' ? preload : undefined,
      src: src
    }), !external_this_wp_blockEditor_["RichText"].isEmpty(caption) && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"].Content, {
      tagName: "figcaption",
      value: caption
    }));
  }
};


/***/ }),
/* 240 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ archives_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/archives/edit.js


/**
 * WordPress dependencies
 */





function ArchivesEdit(_ref) {
  var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes;
  var align = attributes.align,
      showPostCounts = attributes.showPostCounts,
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
  }))), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockAlignmentToolbar"], {
    value: align,
    onChange: function onChange(nextAlign) {
      setAttributes({
        align: nextAlign
      });
    },
    controls: ['left', 'center', 'right']
  })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["ServerSideRender"], {
    block: "core/archives",
    attributes: attributes
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/archives/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var archives_name = 'core/archives';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Archives'),
  description: Object(external_this_wp_i18n_["__"])('Display a monthly archive of your posts.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M7 11h2v2H7v-2zm14-5v14c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2l.01-14c0-1.1.88-2 1.99-2h1V2h2v2h8V2h2v2h1c1.1 0 2 .9 2 2zM5 8h14V6H5v2zm14 12V10H5v10h14zm-4-7h2v-2h-2v2zm-4 0h2v-2h-2v2z"
  }))),
  category: 'widgets',
  supports: {
    html: false
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var align = attributes.align;

    if (['left', 'center', 'right'].includes(align)) {
      return {
        'data-align': align
      };
    }
  },
  edit: ArchivesEdit,
  save: function save() {
    // Handled by PHP.
    return null;
  }
};


/***/ }),
/* 241 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ button_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(6);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/button/edit.js









/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






var _window = window,
    getComputedStyle = _window.getComputedStyle;
var applyFallbackStyles = Object(external_this_wp_components_["withFallbackStyles"])(function (node, ownProps) {
  var textColor = ownProps.textColor,
      backgroundColor = ownProps.backgroundColor;
  var backgroundColorValue = backgroundColor && backgroundColor.color;
  var textColorValue = textColor && textColor.color; //avoid the use of querySelector if textColor color is known and verify if node is available.

  var textNode = !textColorValue && node ? node.querySelector('[contenteditable="true"]') : null;
  return {
    fallbackBackgroundColor: backgroundColorValue || !node ? undefined : getComputedStyle(node).backgroundColor,
    fallbackTextColor: textColorValue || !textNode ? undefined : getComputedStyle(textNode).color
  };
});

var edit_ButtonEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(ButtonEdit, _Component);

  function ButtonEdit() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, ButtonEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ButtonEdit).apply(this, arguments));
    _this.nodeRef = null;
    _this.bindRef = _this.bindRef.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
          isSelected = _this$props.isSelected,
          className = _this$props.className;
      var text = attributes.text,
          url = attributes.url,
          title = attributes.title;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("div", {
        className: className,
        title: title,
        ref: this.bindRef
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichText"], {
        placeholder: Object(external_this_wp_i18n_["__"])('Add text…'),
        value: text,
        onChange: function onChange(value) {
          return setAttributes({
            text: value
          });
        },
        formattingControls: ['bold', 'italic', 'strikethrough'],
        className: classnames_default()('wp-block-button__link', (_classnames = {
          'has-background': backgroundColor.color
        }, Object(defineProperty["a" /* default */])(_classnames, backgroundColor.class, backgroundColor.class), Object(defineProperty["a" /* default */])(_classnames, 'has-text-color', textColor.color), Object(defineProperty["a" /* default */])(_classnames, textColor.class, textColor.class), _classnames)),
        style: {
          backgroundColor: backgroundColor.color,
          color: textColor.color
        },
        keepPlaceholderOnFocus: true
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["PanelColorSettings"], {
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
      })))), isSelected && Object(external_this_wp_element_["createElement"])("form", {
        className: "block-library-button__inline-link",
        onSubmit: function onSubmit(event) {
          return event.preventDefault();
        }
      }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dashicon"], {
        icon: "admin-links"
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["URLInput"], {
        value: url,
        onChange: function onChange(value) {
          return setAttributes({
            url: value
          });
        }
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
        icon: "editor-break",
        label: Object(external_this_wp_i18n_["__"])('Apply'),
        type: "submit"
      })));
    }
  }]);

  return ButtonEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_blockEditor_["withColors"])('backgroundColor', {
  textColor: 'color'
}), applyFallbackStyles])(edit_ButtonEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/button/index.js




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var blockAttributes = {
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
};
var button_name = 'core/button';

var button_colorsMigration = function colorsMigration(attributes) {
  return Object(external_lodash_["omit"])(Object(objectSpread["a" /* default */])({}, attributes, {
    customTextColor: attributes.textColor && '#' === attributes.textColor[0] ? attributes.textColor : undefined,
    customBackgroundColor: attributes.color && '#' === attributes.color[0] ? attributes.color : undefined
  }), ['color', 'textColor']);
};

var settings = {
  title: Object(external_this_wp_i18n_["__"])('Button'),
  description: Object(external_this_wp_i18n_["__"])('Prompt visitors to take action with a button-style link.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M19 6H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 10H5V8h14v8z"
  }))),
  category: 'layout',
  keywords: [Object(external_this_wp_i18n_["__"])('link')],
  attributes: blockAttributes,
  supports: {
    align: true,
    alignWide: false
  },
  styles: [{
    name: 'default',
    label: Object(external_this_wp_i18n_["_x"])('Default', 'block style'),
    isDefault: true
  }, {
    name: 'outline',
    label: Object(external_this_wp_i18n_["__"])('Outline')
  }, {
    name: 'squared',
    label: Object(external_this_wp_i18n_["_x"])('Squared', 'block style')
  }],
  edit: edit,
  save: function save(_ref) {
    var _classnames;

    var attributes = _ref.attributes;
    var url = attributes.url,
        text = attributes.text,
        title = attributes.title,
        backgroundColor = attributes.backgroundColor,
        textColor = attributes.textColor,
        customBackgroundColor = attributes.customBackgroundColor,
        customTextColor = attributes.customTextColor;
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
      value: text
    }));
  },
  deprecated: [{
    attributes: Object(objectSpread["a" /* default */])({}, Object(external_lodash_["pick"])(blockAttributes, ['url', 'title', 'text']), {
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
    save: function save(_ref2) {
      var attributes = _ref2.attributes;
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
    migrate: button_colorsMigration
  }, {
    attributes: Object(objectSpread["a" /* default */])({}, Object(external_lodash_["pick"])(blockAttributes, ['url', 'title', 'text']), {
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
    migrate: button_colorsMigration
  }]
};


/***/ }),
/* 242 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ calendar_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external "moment"
var external_moment_ = __webpack_require__(29);
var external_moment_default = /*#__PURE__*/__webpack_require__.n(external_moment_);

// EXTERNAL MODULE: ./node_modules/memize/index.js
var memize = __webpack_require__(41);
var memize_default = /*#__PURE__*/__webpack_require__.n(memize);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

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
    _this.getYearMonth = memize_default()(_this.getYearMonth.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this))), {
      maxSize: 1
    });
    _this.getServerSideAttributes = memize_default()(_this.getServerSideAttributes.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this))), {
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
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ServerSideRender"], {
        block: "core/calendar",
        attributes: this.getServerSideAttributes(this.props.attributes, this.props.date)
      }));
    }
  }]);

  return CalendarEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/editor'),
      getEditedPostAttribute = _select.getEditedPostAttribute;

  var postType = getEditedPostAttribute('type'); // Dates are used to overwrite year and month used on the calendar.
  // This overwrite should only happen for 'post' post types.
  // For other post types the calendar always displays the current month.

  return {
    date: postType === 'post' ? getEditedPostAttribute('date') : undefined
  };
})(edit_CalendarEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/calendar/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var calendar_name = 'core/calendar';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Calendar'),
  description: Object(external_this_wp_i18n_["__"])('A calendar of your site’s posts.'),
  icon: 'calendar',
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('posts'), Object(external_this_wp_i18n_["__"])('archive')],
  supports: {
    align: true
  },
  edit: edit,
  save: function save() {
    return null;
  }
};


/***/ }),
/* 243 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ categories_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

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
    _this.toggleDisplayAsDropdown = _this.toggleDisplayAsDropdown.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.toggleShowPostCounts = _this.toggleShowPostCounts.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.toggleShowHierarchy = _this.toggleShowHierarchy.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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

/* harmony default export */ var edit = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select) {
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

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/categories/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var categories_name = 'core/categories';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Categories'),
  description: Object(external_this_wp_i18n_["__"])('Display a list of all categories.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
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
  })),
  category: 'widgets',
  attributes: {
    displayAsDropdown: {
      type: 'boolean',
      default: false
    },
    showHierarchy: {
      type: 'boolean',
      default: false
    },
    showPostCounts: {
      type: 'boolean',
      default: false
    }
  },
  supports: {
    align: true,
    alignWide: false,
    html: false
  },
  edit: edit,
  save: function save() {
    return null;
  }
};


/***/ }),
/* 244 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ code_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

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
    value: attributes.content,
    onChange: function onChange(content) {
      return setAttributes({
        content: content
      });
    },
    placeholder: Object(external_this_wp_i18n_["__"])('Write code…'),
    "aria-label": Object(external_this_wp_i18n_["__"])('Code')
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/code/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


var code_name = 'core/code';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Code'),
  description: Object(external_this_wp_i18n_["__"])('Display code snippets that respect your spacing and tabs.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M0,0h24v24H0V0z",
    fill: "none"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M9.4,16.6L4.8,12l4.6-4.6L8,6l-6,6l6,6L9.4,16.6z M14.6,16.6l4.6-4.6l-4.6-4.6L16,6l6,6l-6,6L14.6,16.6z"
  })),
  category: 'formatting',
  attributes: {
    content: {
      type: 'string',
      source: 'text',
      selector: 'code'
    }
  },
  supports: {
    html: false
  },
  transforms: {
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
  },
  edit: CodeEdit,
  save: function save(_ref) {
    var attributes = _ref.attributes;
    return Object(external_this_wp_element_["createElement"])("pre", null, Object(external_this_wp_element_["createElement"])("code", null, attributes.content));
  }
};


/***/ }),
/* 245 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ html_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(17);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

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
    _this.switchToHTML = _this.switchToHTML.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.switchToPreview = _this.switchToPreview.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(HTMLEdit, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var styles = this.props.styles; // Default styles used to unset some of the styles
      // that might be inherited from the editor style.

      var defaultStyles = "\n\t\t\thtml,body,:root {\n\t\t\t\tmargin: 0 !important;\n\t\t\t\tpadding: 0 !important;\n\t\t\t\toverflow: visible !important;\n\t\t\t\tmin-height: auto !important;\n\t\t\t}\n\t\t";
      this.setState({
        styles: [defaultStyles].concat(Object(toConsumableArray["a" /* default */])(Object(external_this_wp_editor_["transformStyles"])(styles)))
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

/* harmony default export */ var edit = (Object(external_this_wp_data_["withSelect"])(function (select) {
  var _select = select('core/block-editor'),
      getSettings = _select.getSettings;

  return {
    styles: getSettings().styles
  };
})(edit_HTMLEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/html/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var html_name = 'core/html';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Custom HTML'),
  description: Object(external_this_wp_i18n_["__"])('Add custom HTML code and preview it as you edit.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M4.5,11h-2V9H1v6h1.5v-2.5h2V15H6V9H4.5V11z M7,10.5h1.5V15H10v-4.5h1.5V9H7V10.5z M14.5,10l-1-1H12v6h1.5v-3.9  l1,1l1-1V15H17V9h-1.5L14.5,10z M19.5,13.5V9H18v6h5v-1.5H19.5z"
  })),
  category: 'formatting',
  keywords: [Object(external_this_wp_i18n_["__"])('embed')],
  supports: {
    customClassName: false,
    className: false,
    html: false
  },
  attributes: {
    content: {
      type: 'string',
      source: 'html'
    }
  },
  transforms: {
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
  },
  edit: edit,
  save: function save(_ref) {
    var attributes = _ref.attributes;
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, attributes.content);
  }
};


/***/ }),
/* 246 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ latest_comments_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(22);

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
    _this.setCommentsToShow = _this.setCommentsToShow.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this))); // Create toggles for each attribute; we create them here rather than
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
      }))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["ServerSideRender"], {
        block: "core/latest-comments",
        attributes: this.props.attributes
      })));
    }
  }]);

  return LatestComments;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (edit_LatestComments);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/latest-comments/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var latest_comments_name = 'core/latest-comments';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Latest Comments'),
  description: Object(external_this_wp_i18n_["__"])('Display a list of your most recent comments.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM20 4v13.17L18.83 16H4V4h16zM6 12h12v2H6zm0-3h12v2H6zm0-3h12v2H6z"
  }))),
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('recent comments')],
  supports: {
    align: true,
    html: false
  },
  edit: edit,
  save: function save() {
    return null;
  }
};


/***/ }),
/* 247 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ latest_posts_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(19);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(33);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(25);

// EXTERNAL MODULE: external {"this":["wp","date"]}
var external_this_wp_date_ = __webpack_require__(50);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

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
    _this.toggleDisplayPostDate = _this.toggleDisplayPostDate.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(LatestPostsEdit, [{
    key: "componentWillMount",
    value: function componentWillMount() {
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
    key: "toggleDisplayPostDate",
    value: function toggleDisplayPostDate() {
      var displayPostDate = this.props.attributes.displayPostDate;
      var setAttributes = this.props.setAttributes;
      setAttributes({
        displayPostDate: !displayPostDate
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes,
          latestPosts = _this$props.latestPosts;
      var categoriesList = this.state.categoriesList;
      var displayPostDate = attributes.displayPostDate,
          align = attributes.align,
          postLayout = attributes.postLayout,
          columns = attributes.columns,
          order = attributes.order,
          orderBy = attributes.orderBy,
          categories = attributes.categories,
          postsToShow = attributes.postsToShow;
      var inspectorControls = Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["InspectorControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["PanelBody"], {
        title: Object(external_this_wp_i18n_["__"])('Latest Posts Settings')
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
      })), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
        label: Object(external_this_wp_i18n_["__"])('Display post date'),
        checked: displayPostDate,
        onChange: this.toggleDisplayPostDate
      }), postLayout === 'grid' && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["RangeControl"], {
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
        title: Object(external_this_wp_i18n_["__"])('List View'),
        onClick: function onClick() {
          return setAttributes({
            postLayout: 'list'
          });
        },
        isActive: postLayout === 'list'
      }, {
        icon: 'grid-view',
        title: Object(external_this_wp_i18n_["__"])('Grid View'),
        onClick: function onClick() {
          return setAttributes({
            postLayout: 'grid'
          });
        },
        isActive: postLayout === 'grid'
      }];

      var dateFormat = Object(external_this_wp_date_["__experimentalGetSettings"])().formats.date;

      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, inspectorControls, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockControls"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["BlockAlignmentToolbar"], {
        value: align,
        onChange: function onChange(nextAlign) {
          setAttributes({
            align: nextAlign
          });
        }
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Toolbar"], {
        controls: layoutControls
      })), Object(external_this_wp_element_["createElement"])("ul", {
        className: classnames_default()(this.props.className, Object(defineProperty["a" /* default */])({
          'is-grid': postLayout === 'grid',
          'has-dates': displayPostDate
        }, "columns-".concat(columns), postLayout === 'grid'))
      }, displayPosts.map(function (post, i) {
        var titleTrimmed = post.title.rendered.trim();
        return Object(external_this_wp_element_["createElement"])("li", {
          key: i
        }, Object(external_this_wp_element_["createElement"])("a", {
          href: post.link,
          target: "_blank",
          rel: "noreferrer noopener"
        }, titleTrimmed ? Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, titleTrimmed) : Object(external_this_wp_i18n_["__"])('(Untitled)')), displayPostDate && post.date_gmt && Object(external_this_wp_element_["createElement"])("time", {
          dateTime: Object(external_this_wp_date_["format"])('c', post.date_gmt),
          className: "wp-block-latest-posts__post-date"
        }, Object(external_this_wp_date_["dateI18n"])(dateFormat, post.date_gmt)));
      })));
    }
  }]);

  return LatestPostsEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_data_["withSelect"])(function (select, props) {
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

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/latest-posts/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


var latest_posts_name = 'core/latest-posts';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Latest Posts'),
  description: Object(external_this_wp_i18n_["__"])('Display a list of your most recent posts.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
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
  })),
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('recent posts')],
  supports: {
    html: false
  },
  getEditWrapperProps: function getEditWrapperProps(attributes) {
    var align = attributes.align;

    if (['left', 'center', 'right', 'wide', 'full'].includes(align)) {
      return {
        'data-align': align
      };
    }
  },
  edit: edit,
  save: function save() {
    return null;
  }
};


/***/ }),
/* 248 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ more_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(18);

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
    _this.onChangeInput = _this.onChangeInput.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onKeyDown = _this.onKeyDown.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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



// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/more/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


var more_name = 'core/more';
var settings = {
  title: Object(external_this_wp_i18n_["_x"])('More', 'block name'),
  description: Object(external_this_wp_i18n_["__"])('Content before this block will be shown in the excerpt on your archives page.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    fill: "none",
    d: "M0 0h24v24H0V0z"
  }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M2 9v2h19V9H2zm0 6h5v-2H2v2zm7 0h5v-2H9v2zm7 0h5v-2h-5v2z"
  }))),
  category: 'layout',
  supports: {
    customClassName: false,
    className: false,
    html: false,
    multiple: false
  },
  attributes: {
    customText: {
      type: 'string'
    },
    noTeaser: {
      type: 'boolean',
      default: false
    }
  },
  transforms: {
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
  },
  edit: edit_MoreEdit,
  save: function save(_ref) {
    var attributes = _ref.attributes;
    var customText = attributes.customText,
        noTeaser = attributes.noTeaser;
    var moreTag = customText ? "<!--more ".concat(customText, "-->") : '<!--more-->';
    var noTeaserTag = noTeaser ? '<!--noteaser-->' : '';
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, Object(external_lodash_["compact"])([moreTag, noTeaserTag]).join('\n'));
  }
};


/***/ }),
/* 249 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ nextpage_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/nextpage/edit.js


/**
 * WordPress dependencies
 */

function NextPageEdit() {
  return Object(external_this_wp_element_["createElement"])("div", {
    className: "wp-block-nextpage"
  }, Object(external_this_wp_element_["createElement"])("span", null, Object(external_this_wp_i18n_["__"])('Page break')));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/nextpage/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var nextpage_name = 'core/nextpage';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Page Break'),
  description: Object(external_this_wp_i18n_["__"])('Separate your content into a multi-page experience.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["G"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
    d: "M9 12h6v-2H9zm-7 0h5v-2H2zm15 0h5v-2h-5zm3 2v2l-6 6H6a2 2 0 0 1-2-2v-6h2v6h6v-4a2 2 0 0 1 2-2h6zM4 8V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4h-2V4H6v4z"
  }))),
  category: 'layout',
  keywords: [Object(external_this_wp_i18n_["__"])('next page'), Object(external_this_wp_i18n_["__"])('pagination')],
  supports: {
    customClassName: false,
    className: false,
    html: false
  },
  attributes: {},
  transforms: {
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
  },
  edit: NextPageEdit,
  save: function save() {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["RawHTML"], null, '<!--nextpage-->');
  }
};


/***/ }),
/* 250 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ pullquote_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(7);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(16);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(19);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/pullquote/edit.js










/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




var SOLID_COLOR_STYLE_NAME = 'solid-color';
var SOLID_COLOR_CLASS = "is-style-".concat(SOLID_COLOR_STYLE_NAME);

var edit_PullQuoteEdit =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PullQuoteEdit, _Component);

  function PullQuoteEdit(props) {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PullQuoteEdit);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PullQuoteEdit).call(this, props));
    _this.wasTextColorAutomaticallyComputed = false;
    _this.pullQuoteMainColorSetter = _this.pullQuoteMainColorSetter.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.pullQuoteTextColorSetter = _this.pullQuoteTextColorSetter.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(PullQuoteEdit, [{
    key: "pullQuoteMainColorSetter",
    value: function pullQuoteMainColorSetter(colorValue) {
      var _this$props = this.props,
          colorUtils = _this$props.colorUtils,
          textColor = _this$props.textColor,
          setTextColor = _this$props.setTextColor,
          setMainColor = _this$props.setMainColor,
          className = _this$props.className;
      var isSolidColorStyle = Object(external_lodash_["includes"])(className, SOLID_COLOR_CLASS);
      var needTextColor = !textColor.color || this.wasTextColorAutomaticallyComputed;
      var shouldSetTextColor = isSolidColorStyle && needTextColor && colorValue;
      setMainColor(colorValue);

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
    key: "render",
    value: function render() {
      var _this$props2 = this.props,
          attributes = _this$props2.attributes,
          mainColor = _this$props2.mainColor,
          textColor = _this$props2.textColor,
          setAttributes = _this$props2.setAttributes,
          isSelected = _this$props2.isSelected,
          className = _this$props2.className;
      var value = attributes.value,
          citation = attributes.citation;
      var isSolidColorStyle = Object(external_lodash_["includes"])(className, SOLID_COLOR_CLASS);
      var figureStyle = isSolidColorStyle ? {
        backgroundColor: mainColor.color
      } : {
        borderColor: mainColor.color
      };
      var blockquoteStyle = {
        color: textColor.color
      };
      var blockquoteClasses = textColor.color ? classnames_default()('has-text-color', Object(defineProperty["a" /* default */])({}, textColor.class, textColor.class)) : undefined;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])("figure", {
        style: figureStyle,
        className: classnames_default()(className, Object(defineProperty["a" /* default */])({}, mainColor.class, isSolidColorStyle && mainColor.class))
      }, Object(external_this_wp_element_["createElement"])("blockquote", {
        style: blockquoteStyle,
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

/* harmony default export */ var edit = (Object(external_this_wp_blockEditor_["withColors"])({
  mainColor: 'background-color',
  textColor: 'color'
})(edit_PullQuoteEdit));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/pullquote/index.js




/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


var blockAttributes = {
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
var pullquote_name = 'core/pullquote';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Pullquote'),
  description: Object(external_this_wp_i18n_["__"])('Give special visual emphasis to a quote from your text.'),
  icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
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
  })),
  category: 'formatting',
  attributes: blockAttributes,
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
  edit: edit,
  save: function save(_ref) {
    var attributes = _ref.attributes;
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
  },
  deprecated: [{
    attributes: Object(objectSpread["a" /* default */])({}, blockAttributes),
    save: function save(_ref2) {
      var attributes = _ref2.attributes;
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
    attributes: Object(objectSpread["a" /* default */])({}, blockAttributes, {
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
    save: function save(_ref3) {
      var attributes = _ref3.attributes;
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
  }]
};


/***/ }),
/* 251 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ rss_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

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
    _this.toggleAttribute = _this.toggleAttribute.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onSubmitURL = _this.onSubmitURL.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
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
        title: Object(external_this_wp_i18n_["__"])('List View'),
        onClick: function onClick() {
          return setAttributes({
            blockLayout: 'list'
          });
        },
        isActive: blockLayout === 'list'
      }, {
        icon: 'grid-view',
        title: Object(external_this_wp_i18n_["__"])('Grid View'),
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
      }))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Disabled"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ServerSideRender"], {
        block: "core/rss",
        attributes: this.props.attributes
      })));
    }
  }]);

  return RSSEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (edit_RSSEdit);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/rss/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


var rss_name = 'core/rss';
var settings = {
  title: Object(external_this_wp_i18n_["__"])('RSS'),
  description: Object(external_this_wp_i18n_["__"])('Display entries from any RSS or Atom feed.'),
  icon: 'rss',
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('atom'), Object(external_this_wp_i18n_["__"])('feed')],
  supports: {
    html: false
  },
  edit: edit,
  save: function save() {
    return null;
  }
};


/***/ }),
/* 252 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ search_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

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
    keepPlaceholderOnFocus: true,
    formattingControls: [],
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
    keepPlaceholderOnFocus: true,
    formattingControls: [],
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
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Search'),
  description: Object(external_this_wp_i18n_["__"])('Help visitors find your content.'),
  icon: 'search',
  category: 'widgets',
  keywords: [Object(external_this_wp_i18n_["__"])('find')],
  edit: SearchEdit,
  save: function save() {
    return null;
  }
};


/***/ }),
/* 253 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "name", function() { return /* binding */ tag_cloud_name; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* binding */ settings; });

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(17);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(8);

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
    _this.setTaxonomy = _this.setTaxonomy.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.toggleShowTagCounts = _this.toggleShowTagCounts.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    return _this;
  }

  Object(createClass["a" /* default */])(TagCloudEdit, [{
    key: "getTaxonomyOptions",
    value: function getTaxonomyOptions() {
      var taxonomies = Object(external_lodash_["filter"])(this.props.taxonomies, 'show_cloud');
      var selectOption = {
        label: Object(external_this_wp_i18n_["__"])('- Select -'),
        value: ''
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
      return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, inspectorControls, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ServerSideRender"], {
        key: "tag-cloud",
        block: "core/tag-cloud",
        attributes: attributes
      }));
    }
  }]);

  return TagCloudEdit;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var edit = (Object(external_this_wp_data_["withSelect"])(function (select) {
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
var settings = {
  title: Object(external_this_wp_i18n_["__"])('Tag Cloud'),
  description: Object(external_this_wp_i18n_["__"])('A cloud of your most used tags.'),
  icon: 'tag',
  category: 'widgets',
  supports: {
    html: false,
    align: true
  },
  edit: edit,
  save: function save() {
    return null;
  }
};


/***/ }),
/* 254 */,
/* 255 */,
/* 256 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(process) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "registerCoreBlocks", function() { return registerCoreBlocks; });
/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(17);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(72);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(8);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(22);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(14);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _paragraph__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(138);
/* harmony import */ var _image__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(229);
/* harmony import */ var _heading__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(235);
/* harmony import */ var _quote__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(208);
/* harmony import */ var _gallery__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(231);
/* harmony import */ var _archives__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(240);
/* harmony import */ var _audio__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(236);
/* harmony import */ var _button__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(241);
/* harmony import */ var _calendar__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(242);
/* harmony import */ var _categories__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(243);
/* harmony import */ var _code__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(244);
/* harmony import */ var _columns__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(209);
/* harmony import */ var _columns_column__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(210);
/* harmony import */ var _cover__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(237);
/* harmony import */ var _embed__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(110);
/* harmony import */ var _file__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(232);
/* harmony import */ var _html__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(245);
/* harmony import */ var _media_text__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(230);
/* harmony import */ var _latest_comments__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(246);
/* harmony import */ var _latest_posts__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(247);
/* harmony import */ var _legacy_widget__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(233);
/* harmony import */ var _list__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(212);
/* harmony import */ var _missing__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(136);
/* harmony import */ var _more__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(248);
/* harmony import */ var _nextpage__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(249);
/* harmony import */ var _preformatted__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(213);
/* harmony import */ var _pullquote__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(250);
/* harmony import */ var _block__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(234);
/* harmony import */ var _rss__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(251);
/* harmony import */ var _search__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(252);
/* harmony import */ var _separator__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(214);
/* harmony import */ var _shortcode__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(215);
/* harmony import */ var _spacer__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(216);
/* harmony import */ var _subhead__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(217);
/* harmony import */ var _table__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(238);
/* harmony import */ var _template__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(218);
/* harmony import */ var _text_columns__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(219);
/* harmony import */ var _verse__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(220);
/* harmony import */ var _video__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(239);
/* harmony import */ var _tag_cloud__WEBPACK_IMPORTED_MODULE_44__ = __webpack_require__(253);
/* harmony import */ var _classic__WEBPACK_IMPORTED_MODULE_45__ = __webpack_require__(139);


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */










































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

var registerCoreBlocks = function registerCoreBlocks() {
  [// Common blocks are grouped at the top to prioritize their display
  // in various contexts — like the inserter and auto-complete components.
  _paragraph__WEBPACK_IMPORTED_MODULE_5__, _image__WEBPACK_IMPORTED_MODULE_6__, _heading__WEBPACK_IMPORTED_MODULE_7__, _gallery__WEBPACK_IMPORTED_MODULE_9__, _list__WEBPACK_IMPORTED_MODULE_26__, _quote__WEBPACK_IMPORTED_MODULE_8__, // Register all remaining core blocks.
  _shortcode__WEBPACK_IMPORTED_MODULE_36__, _archives__WEBPACK_IMPORTED_MODULE_10__, _audio__WEBPACK_IMPORTED_MODULE_11__, _button__WEBPACK_IMPORTED_MODULE_12__, _calendar__WEBPACK_IMPORTED_MODULE_13__, _categories__WEBPACK_IMPORTED_MODULE_14__, _code__WEBPACK_IMPORTED_MODULE_15__, _columns__WEBPACK_IMPORTED_MODULE_16__, _columns_column__WEBPACK_IMPORTED_MODULE_17__, _cover__WEBPACK_IMPORTED_MODULE_18__, _embed__WEBPACK_IMPORTED_MODULE_19__].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(_embed__WEBPACK_IMPORTED_MODULE_19__["common"]), Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(_embed__WEBPACK_IMPORTED_MODULE_19__["others"]), [_file__WEBPACK_IMPORTED_MODULE_20__, window.wp && window.wp.oldEditor ? _classic__WEBPACK_IMPORTED_MODULE_45__ : null, // Only add the classic block in WP Context
  _html__WEBPACK_IMPORTED_MODULE_21__, _media_text__WEBPACK_IMPORTED_MODULE_22__, _latest_comments__WEBPACK_IMPORTED_MODULE_23__, _latest_posts__WEBPACK_IMPORTED_MODULE_24__, process.env.GUTENBERG_PHASE === 2 ? _legacy_widget__WEBPACK_IMPORTED_MODULE_25__ : null, _missing__WEBPACK_IMPORTED_MODULE_27__, _more__WEBPACK_IMPORTED_MODULE_28__, _nextpage__WEBPACK_IMPORTED_MODULE_29__, _preformatted__WEBPACK_IMPORTED_MODULE_30__, _pullquote__WEBPACK_IMPORTED_MODULE_31__, _rss__WEBPACK_IMPORTED_MODULE_33__, _search__WEBPACK_IMPORTED_MODULE_34__, _separator__WEBPACK_IMPORTED_MODULE_35__, _block__WEBPACK_IMPORTED_MODULE_32__, _spacer__WEBPACK_IMPORTED_MODULE_37__, _subhead__WEBPACK_IMPORTED_MODULE_38__, _table__WEBPACK_IMPORTED_MODULE_39__, _tag_cloud__WEBPACK_IMPORTED_MODULE_44__, _template__WEBPACK_IMPORTED_MODULE_40__, _text_columns__WEBPACK_IMPORTED_MODULE_41__, _verse__WEBPACK_IMPORTED_MODULE_42__, _video__WEBPACK_IMPORTED_MODULE_43__]).forEach(function (block) {
    if (!block) {
      return;
    }

    var name = block.name,
        settings = block.settings;
    Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["registerBlockType"])(name, settings);
  });
  Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["setDefaultBlockName"])(_paragraph__WEBPACK_IMPORTED_MODULE_5__["name"]);

  if (window.wp && window.wp.oldEditor) {
    Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["setFreeformContentHandlerName"])(_classic__WEBPACK_IMPORTED_MODULE_45__["name"]);
  }

  Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["setUnregisteredTypeHandlerName"])(_missing__WEBPACK_IMPORTED_MODULE_27__["name"]);
};

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(257)))

/***/ }),
/* 257 */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ })
/******/ ]);