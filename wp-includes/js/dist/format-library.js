this["wp"] = this["wp"] || {}; this["wp"]["formatLibrary"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 430);
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

/***/ 159:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(9);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

var link = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M17.74 2.76c1.68 1.69 1.68 4.41 0 6.1l-1.53 1.52c-1.12 1.12-2.7 1.47-4.14 1.09l2.62-2.61.76-.77.76-.76c.84-.84.84-2.2 0-3.04-.84-.85-2.2-.85-3.04 0l-.77.76-3.38 3.38c-.37-1.44-.02-3.02 1.1-4.14l1.52-1.53c1.69-1.68 4.42-1.68 6.1 0zM8.59 13.43l5.34-5.34c.42-.42.42-1.1 0-1.52-.44-.43-1.13-.39-1.53 0l-5.33 5.34c-.42.42-.42 1.1 0 1.52.44.43 1.13.39 1.52 0zm-.76 2.29l4.14-4.15c.38 1.44.03 3.02-1.09 4.14l-1.52 1.53c-1.69 1.68-4.41 1.68-6.1 0-1.68-1.68-1.68-4.42 0-6.1l1.53-1.52c1.12-1.12 2.7-1.47 4.14-1.1l-4.14 4.15c-.85.84-.85 2.2 0 3.05.84.84 2.2.84 3.04 0z"
}));
/* harmony default export */ __webpack_exports__["a"] = (link);


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

/***/ 192:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(9);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

var keyboardReturn = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M16 4h2v9H7v3l-5-4 5-4v3h9V4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (keyboardReturn);


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

/***/ 24:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["richText"]; }());

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

/***/ 28:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["dom"]; }());

/***/ }),

/***/ 290:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(9);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

var code = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M9.4,16.6L4.8,12l4.6-4.6L8,6l-6,6l6,6L9.4,16.6z M14.6,16.6l4.6-4.6l-4.6-4.6L16,6l6,6l-6,6L14.6,16.6z"
}));
/* harmony default export */ __webpack_exports__["a"] = (code);


/***/ }),

/***/ 3:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ 30:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

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

/***/ 430:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js
var objectWithoutProperties = __webpack_require__(19);

// EXTERNAL MODULE: external {"this":["wp","richText"]}
var external_this_wp_richText_ = __webpack_require__(24);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(6);

// EXTERNAL MODULE: external {"this":["wp","primitives"]}
var external_this_wp_primitives_ = __webpack_require__(9);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/format-bold.js


/**
 * WordPress dependencies
 */

var formatBold = Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["Path"], {
  d: "M6 4v13h4.54c1.37 0 2.46-.33 3.26-1 .8-.66 1.2-1.58 1.2-2.77 0-.84-.17-1.51-.51-2.01s-.9-.85-1.67-1.03v-.09c.57-.1 1.02-.4 1.36-.9s.51-1.13.51-1.91c0-1.14-.39-1.98-1.17-2.5C12.75 4.26 11.5 4 9.78 4H6zm2.57 5.15V6.26h1.36c.73 0 1.27.11 1.61.32.34.22.51.58.51 1.07 0 .54-.16.92-.47 1.15s-.82.35-1.51.35h-1.5zm0 2.19h1.6c1.44 0 2.16.53 2.16 1.61 0 .6-.17 1.05-.51 1.34s-.86.43-1.57.43H8.57v-3.38z"
}));
/* harmony default export */ var format_bold = (formatBold);

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/bold/index.js


/**
 * WordPress dependencies
 */




var bold_name = 'core/bold';

var title = Object(external_this_wp_i18n_["__"])('Bold');

var bold = {
  name: bold_name,
  title: title,
  tagName: 'strong',
  className: null,
  edit: function edit(_ref) {
    var isActive = _ref.isActive,
        value = _ref.value,
        onChange = _ref.onChange,
        onFocus = _ref.onFocus;

    function onToggle() {
      onChange(Object(external_this_wp_richText_["toggleFormat"])(value, {
        type: bold_name
      }));
    }

    function onClick() {
      onToggle();
      onFocus();
    }

    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextShortcut"], {
      type: "primary",
      character: "b",
      onUse: onToggle
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextToolbarButton"], {
      name: "bold",
      icon: format_bold,
      title: title,
      onClick: onClick,
      isActive: isActive,
      shortcutType: "primary",
      shortcutCharacter: "b"
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__unstableRichTextInputEvent"], {
      inputType: "formatBold",
      onInput: onToggle
    }));
  }
};

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/code.js
var code = __webpack_require__(290);

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/code/index.js


/**
 * WordPress dependencies
 */




var code_name = 'core/code';

var code_title = Object(external_this_wp_i18n_["__"])('Inline Code');

var code_code = {
  name: code_name,
  title: code_title,
  tagName: 'code',
  className: null,
  __unstableInputRule: function __unstableInputRule(value) {
    var BACKTICK = '`';
    var _value = value,
        start = _value.start,
        text = _value.text;
    var characterBefore = text.slice(start - 1, start); // Quick check the text for the necessary character.

    if (characterBefore !== BACKTICK) {
      return value;
    }

    var textBefore = text.slice(0, start - 1);
    var indexBefore = textBefore.lastIndexOf(BACKTICK);

    if (indexBefore === -1) {
      return value;
    }

    var startIndex = indexBefore;
    var endIndex = start - 2;

    if (startIndex === endIndex) {
      return value;
    }

    value = Object(external_this_wp_richText_["remove"])(value, startIndex, startIndex + 1);
    value = Object(external_this_wp_richText_["remove"])(value, endIndex, endIndex + 1);
    value = Object(external_this_wp_richText_["applyFormat"])(value, {
      type: code_name
    }, startIndex, endIndex);
    return value;
  },
  edit: function edit(_ref) {
    var value = _ref.value,
        onChange = _ref.onChange,
        onFocus = _ref.onFocus,
        isActive = _ref.isActive;

    function onClick() {
      onChange(Object(external_this_wp_richText_["toggleFormat"])(value, {
        type: code_name
      }));
      onFocus();
    }

    return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextToolbarButton"], {
      icon: code["a" /* default */],
      title: code_title,
      onClick: onClick,
      isActive: isActive
    });
  }
};

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(5);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(14);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(15);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(16);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(7);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(17);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(3);

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(22);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/keyboard-return.js
var keyboard_return = __webpack_require__(192);

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/image/index.js









function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * WordPress dependencies
 */







var ALLOWED_MEDIA_TYPES = ['image'];
var image_name = 'core/image';

var image_title = Object(external_this_wp_i18n_["__"])('Inline image');

var stopKeyPropagation = function stopKeyPropagation(event) {
  return event.stopPropagation();
};

function getRange() {
  var selection = window.getSelection();
  return selection.rangeCount ? selection.getRangeAt(0) : null;
}

var image_image = {
  name: image_name,
  title: image_title,
  keywords: [Object(external_this_wp_i18n_["__"])('photo'), Object(external_this_wp_i18n_["__"])('media')],
  object: true,
  tagName: 'img',
  className: null,
  attributes: {
    className: 'class',
    style: 'style',
    url: 'src',
    alt: 'alt'
  },
  edit:
  /*#__PURE__*/
  function (_Component) {
    Object(inherits["a" /* default */])(ImageEdit, _Component);

    function ImageEdit() {
      var _this;

      Object(classCallCheck["a" /* default */])(this, ImageEdit);

      _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ImageEdit).apply(this, arguments));
      _this.onChange = _this.onChange.bind(Object(assertThisInitialized["a" /* default */])(_this));
      _this.onKeyDown = _this.onKeyDown.bind(Object(assertThisInitialized["a" /* default */])(_this));
      _this.openModal = _this.openModal.bind(Object(assertThisInitialized["a" /* default */])(_this));
      _this.closeModal = _this.closeModal.bind(Object(assertThisInitialized["a" /* default */])(_this));
      _this.anchorRef = null;
      _this.state = {
        modal: false
      };
      return _this;
    }

    Object(createClass["a" /* default */])(ImageEdit, [{
      key: "onChange",
      value: function onChange(width) {
        this.setState({
          width: width
        });
      }
    }, {
      key: "onKeyDown",
      value: function onKeyDown(event) {
        if ([external_this_wp_keycodes_["LEFT"], external_this_wp_keycodes_["DOWN"], external_this_wp_keycodes_["RIGHT"], external_this_wp_keycodes_["UP"], external_this_wp_keycodes_["BACKSPACE"], external_this_wp_keycodes_["ENTER"]].indexOf(event.keyCode) > -1) {
          // Stop the key event from propagating up to ObserveTyping.startTypingInTextField.
          event.stopPropagation();
        }
      }
    }, {
      key: "openModal",
      value: function openModal() {
        this.setState({
          modal: true
        });
      }
    }, {
      key: "closeModal",
      value: function closeModal() {
        this.setState({
          modal: false
        });
      }
    }, {
      key: "componentDidMount",
      value: function componentDidMount() {
        this.anchorRef = getRange();
      }
    }, {
      key: "componentDidUpdate",
      value: function componentDidUpdate(prevProps) {
        // When the popover is open or when the selected image changes,
        // update the anchorRef.
        if (!prevProps.isObjectActive && this.props.isObjectActive || prevProps.activeObjectAttributes.url !== this.props.activeObjectAttributes.url) {
          this.anchorRef = getRange();
        }
      }
    }, {
      key: "render",
      value: function render() {
        var _this2 = this;

        var _this$props = this.props,
            value = _this$props.value,
            onChange = _this$props.onChange,
            onFocus = _this$props.onFocus,
            isObjectActive = _this$props.isObjectActive,
            activeObjectAttributes = _this$props.activeObjectAttributes;
        return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextToolbarButton"], {
          icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
            xmlns: "http://www.w3.org/2000/svg",
            viewBox: "0 0 24 24"
          }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
            d: "M4 16h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2zM4 5h10v9H4V5zm14 9v2h4v-2h-4zM2 20h20v-2H2v2zm6.4-8.8L7 9.4 5 12h8l-2.6-3.4-2 2.6z"
          })),
          title: image_title,
          onClick: this.openModal,
          isActive: isObjectActive
        }), this.state.modal && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["MediaUpload"], {
          allowedTypes: ALLOWED_MEDIA_TYPES,
          onSelect: function onSelect(_ref) {
            var id = _ref.id,
                url = _ref.url,
                alt = _ref.alt,
                width = _ref.width;

            _this2.closeModal();

            onChange(Object(external_this_wp_richText_["insertObject"])(value, {
              type: image_name,
              attributes: {
                className: "wp-image-".concat(id),
                style: "width: ".concat(Math.min(width, 150), "px;"),
                url: url,
                alt: alt
              }
            }));
            onFocus();
          },
          onClose: this.closeModal,
          render: function render(_ref2) {
            var open = _ref2.open;
            open();
            return null;
          }
        }), isObjectActive && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Popover"], {
          position: "bottom center",
          focusOnMount: false,
          anchorRef: this.anchorRef
        }, Object(external_this_wp_element_["createElement"])("form", {
          className: "block-editor-format-toolbar__image-container-content",
          onKeyPress: stopKeyPropagation,
          onKeyDown: this.onKeyDown,
          onSubmit: function onSubmit(event) {
            var newReplacements = value.replacements.slice();
            newReplacements[value.start] = {
              type: image_name,
              attributes: _objectSpread({}, activeObjectAttributes, {
                style: "width: ".concat(_this2.state.width, "px;")
              })
            };
            onChange(_objectSpread({}, value, {
              replacements: newReplacements
            }));
            event.preventDefault();
          }
        }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["TextControl"], {
          className: "block-editor-format-toolbar__image-container-value",
          type: "number",
          label: Object(external_this_wp_i18n_["__"])('Width'),
          value: this.state.width,
          min: 1,
          onChange: this.onChange
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
          icon: keyboard_return["a" /* default */],
          label: Object(external_this_wp_i18n_["__"])('Apply'),
          type: "submit"
        }))));
      }
    }], [{
      key: "getDerivedStateFromProps",
      value: function getDerivedStateFromProps(props, state) {
        var style = props.activeObjectAttributes.style;

        if (style === state.previousStyle) {
          return null;
        }

        if (!style) {
          return {
            width: undefined,
            previousStyle: style
          };
        }

        return {
          width: style.replace(/\D/g, ''),
          previousStyle: style
        };
      }
    }]);

    return ImageEdit;
  }(external_this_wp_element_["Component"])
};

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/format-italic.js


/**
 * WordPress dependencies
 */

var formatItalic = Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["Path"], {
  d: "M14.78 6h-2.13l-2.8 9h2.12l-.62 2H4.6l.62-2h2.14l2.8-9H8.03l.62-2h6.75z"
}));
/* harmony default export */ var format_italic = (formatItalic);

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/italic/index.js


/**
 * WordPress dependencies
 */




var italic_name = 'core/italic';

var italic_title = Object(external_this_wp_i18n_["__"])('Italic');

var italic = {
  name: italic_name,
  title: italic_title,
  tagName: 'em',
  className: null,
  edit: function edit(_ref) {
    var isActive = _ref.isActive,
        value = _ref.value,
        onChange = _ref.onChange,
        onFocus = _ref.onFocus;

    function onToggle() {
      onChange(Object(external_this_wp_richText_["toggleFormat"])(value, {
        type: italic_name
      }));
    }

    function onClick() {
      onToggle();
      onFocus();
    }

    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextShortcut"], {
      type: "primary",
      character: "i",
      onUse: onToggle
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextToolbarButton"], {
      name: "italic",
      icon: format_italic,
      title: italic_title,
      onClick: onClick,
      isActive: isActive,
      shortcutType: "primary",
      shortcutCharacter: "i"
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__unstableRichTextInputEvent"], {
      inputType: "formatItalic",
      onInput: onToggle
    }));
  }
};

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(30);

// EXTERNAL MODULE: external {"this":["wp","htmlEntities"]}
var external_this_wp_htmlEntities_ = __webpack_require__(58);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/link-off.js


/**
 * WordPress dependencies
 */

var linkOff = Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["Path"], {
  d: "M17.74 2.26c1.68 1.69 1.68 4.41 0 6.1l-1.53 1.52c-.32.33-.69.58-1.08.77L13 10l1.69-1.64.76-.77.76-.76c.84-.84.84-2.2 0-3.04-.84-.85-2.2-.85-3.04 0l-.77.76-.76.76L10 7l-.65-2.14c.19-.38.44-.75.77-1.07l1.52-1.53c1.69-1.68 4.42-1.68 6.1 0zM2 4l8 6-6-8zm4-2l4 8-2-8H6zM2 6l8 4-8-2V6zm7.36 7.69L10 13l.74 2.35-1.38 1.39c-1.69 1.68-4.41 1.68-6.1 0-1.68-1.68-1.68-4.42 0-6.1l1.39-1.38L7 10l-.69.64-1.52 1.53c-.85.84-.85 2.2 0 3.04.84.85 2.2.85 3.04 0zM18 16l-8-6 6 8zm-4 2l-4-8 2 8h2zm4-4l-8-4 8 2v2z"
}));
/* harmony default export */ var link_off = (linkOff);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/link.js
var library_link = __webpack_require__(159);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(20);

// EXTERNAL MODULE: external {"this":"lodash"}
var external_this_lodash_ = __webpack_require__(2);

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/utils.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Check for issues with the provided href.
 *
 * @param {string} href The href.
 *
 * @return {boolean} Is the href invalid?
 */

function isValidHref(href) {
  if (!href) {
    return false;
  }

  var trimmedHref = href.trim();

  if (!trimmedHref) {
    return false;
  } // Does the href start with something that looks like a URL protocol?


  if (/^\S+:/.test(trimmedHref)) {
    var protocol = Object(external_this_wp_url_["getProtocol"])(trimmedHref);

    if (!Object(external_this_wp_url_["isValidProtocol"])(protocol)) {
      return false;
    } // Add some extra checks for http(s) URIs, since these are the most common use-case.
    // This ensures URIs with an http protocol have exactly two forward slashes following the protocol.


    if (Object(external_this_lodash_["startsWith"])(protocol, 'http') && !/^https?:\/\/[^\/\s]/i.test(trimmedHref)) {
      return false;
    }

    var authority = Object(external_this_wp_url_["getAuthority"])(trimmedHref);

    if (!Object(external_this_wp_url_["isValidAuthority"])(authority)) {
      return false;
    }

    var path = Object(external_this_wp_url_["getPath"])(trimmedHref);

    if (path && !Object(external_this_wp_url_["isValidPath"])(path)) {
      return false;
    }

    var queryString = Object(external_this_wp_url_["getQueryString"])(trimmedHref);

    if (queryString && !Object(external_this_wp_url_["isValidQueryString"])(queryString)) {
      return false;
    }

    var fragment = Object(external_this_wp_url_["getFragment"])(trimmedHref);

    if (fragment && !Object(external_this_wp_url_["isValidFragment"])(fragment)) {
      return false;
    }
  } // Validate anchor links.


  if (Object(external_this_lodash_["startsWith"])(trimmedHref, '#') && !Object(external_this_wp_url_["isValidFragment"])(trimmedHref)) {
    return false;
  }

  return true;
}
/**
 * Generates the format object that will be applied to the link text.
 *
 * @param {Object}  options
 * @param {string}  options.url              The href of the link.
 * @param {boolean} options.opensInNewWindow Whether this link will open in a new window.
 * @param {Object}  options.text             The text that is being hyperlinked.
 *
 * @return {Object} The final format object.
 */

function createLinkFormat(_ref) {
  var url = _ref.url,
      opensInNewWindow = _ref.opensInNewWindow;
  var format = {
    type: 'core/link',
    attributes: {
      url: url
    }
  };

  if (opensInNewWindow) {
    format.attributes.target = '_blank';
    format.attributes.rel = 'noreferrer noopener';
  }

  return format;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/inline.js




function inline_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function inline_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { inline_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { inline_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



function InlineLinkUI(_ref) {
  var isActive = _ref.isActive,
      activeAttributes = _ref.activeAttributes,
      addingLink = _ref.addingLink,
      value = _ref.value,
      onChange = _ref.onChange,
      speak = _ref.speak,
      stopAddingLink = _ref.stopAddingLink;

  /**
   * A unique key is generated when switching between editing and not editing
   * a link, based on:
   *
   * - This component may be rendered _either_ when a link is active _or_
   *   when adding or editing a link.
   * - It's only desirable to shift focus into the Popover when explicitly
   *   adding or editing a link, not when in the inline boundary of a link.
   * - Focus behavior can only be controlled on a Popover at the time it
   *   mounts, so a new instance of the component must be mounted to
   *   programmatically enact the focusOnMount behavior.
   *
   * @type {string}
   */
  var mountingKey = Object(external_this_wp_element_["useMemo"])(external_this_lodash_["uniqueId"], [addingLink]);
  /**
   * Pending settings to be applied to the next link. When inserting a new
   * link, toggle values cannot be applied immediately, because there is not
   * yet a link for them to apply to. Thus, they are maintained in a state
   * value until the time that the link can be inserted or edited.
   *
   * @type {[Object|undefined,Function]}
   */

  var _useState = Object(external_this_wp_element_["useState"])(),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      nextLinkValue = _useState2[0],
      setNextLinkValue = _useState2[1];

  var anchorRef = Object(external_this_wp_element_["useMemo"])(function () {
    var selection = window.getSelection();

    if (!selection.rangeCount) {
      return;
    }

    var range = selection.getRangeAt(0);

    if (addingLink && !isActive) {
      return range;
    }

    var element = range.startContainer; // If the caret is right before the element, select the next element.

    element = element.nextElementSibling || element;

    while (element.nodeType !== window.Node.ELEMENT_NODE) {
      element = element.parentNode;
    }

    return element.closest('a');
  }, [addingLink, value.start, value.end]);

  var linkValue = inline_objectSpread({
    url: activeAttributes.url,
    opensInNewTab: activeAttributes.target === '_blank'
  }, nextLinkValue);

  function onChangeLink(nextValue) {
    // Merge with values from state, both for the purpose of assigning the
    // next state value, and for use in constructing the new link format if
    // the link is ready to be applied.
    nextValue = inline_objectSpread({}, nextLinkValue, {}, nextValue); // LinkControl calls `onChange` immediately upon the toggling a setting.

    var didToggleSetting = linkValue.opensInNewTab !== nextValue.opensInNewTab && linkValue.url === nextValue.url; // If change handler was called as a result of a settings change during
    // link insertion, it must be held in state until the link is ready to
    // be applied.

    var didToggleSettingForNewLink = didToggleSetting && nextValue.url === undefined; // If link will be assigned, the state value can be considered flushed.
    // Otherwise, persist the pending changes.

    setNextLinkValue(didToggleSettingForNewLink ? nextValue : undefined);

    if (didToggleSettingForNewLink) {
      return;
    }

    var newUrl = Object(external_this_wp_url_["prependHTTP"])(nextValue.url);
    var format = createLinkFormat({
      url: newUrl,
      opensInNewWindow: nextValue.opensInNewTab
    });

    if (Object(external_this_wp_richText_["isCollapsed"])(value) && !isActive) {
      var toInsert = Object(external_this_wp_richText_["applyFormat"])(Object(external_this_wp_richText_["create"])({
        text: newUrl
      }), format, 0, newUrl.length);
      onChange(Object(external_this_wp_richText_["insert"])(value, toInsert));
    } else {
      onChange(Object(external_this_wp_richText_["applyFormat"])(value, format));
    } // Focus should only be shifted back to the formatted segment when the
    // URL is submitted.


    if (!didToggleSetting) {
      stopAddingLink();
    }

    if (!isValidHref(newUrl)) {
      speak(Object(external_this_wp_i18n_["__"])('Warning: the link has been inserted but may have errors. Please test it.'), 'assertive');
    } else if (isActive) {
      speak(Object(external_this_wp_i18n_["__"])('Link edited.'), 'assertive');
    } else {
      speak(Object(external_this_wp_i18n_["__"])('Link inserted.'), 'assertive');
    }
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Popover"], {
    key: mountingKey,
    anchorRef: anchorRef,
    focusOnMount: addingLink ? 'firstElement' : false,
    onClose: stopAddingLink,
    position: "bottom center"
  }, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__experimentalLinkControl"], {
    value: linkValue,
    onChange: onChangeLink,
    forceIsEditingLink: addingLink
  }));
}

/* harmony default export */ var inline = (Object(external_this_wp_components_["withSpokenMessages"])(InlineLinkUI));

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/index.js








/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


var link_name = 'core/link';

var link_title = Object(external_this_wp_i18n_["__"])('Link');

var link_link = {
  name: link_name,
  title: link_title,
  tagName: 'a',
  className: null,
  attributes: {
    url: 'href',
    target: 'target'
  },
  __unstablePasteRule: function __unstablePasteRule(value, _ref) {
    var html = _ref.html,
        plainText = _ref.plainText;

    if (Object(external_this_wp_richText_["isCollapsed"])(value)) {
      return value;
    }

    var pastedText = (html || plainText).replace(/<[^>]+>/g, '').trim(); // A URL was pasted, turn the selection into a link

    if (!Object(external_this_wp_url_["isURL"])(pastedText)) {
      return value;
    } // Allows us to ask for this information when we get a report.


    window.console.log('Created link:\n\n', pastedText);
    return Object(external_this_wp_richText_["applyFormat"])(value, {
      type: link_name,
      attributes: {
        url: Object(external_this_wp_htmlEntities_["decodeEntities"])(pastedText)
      }
    });
  },
  edit: Object(external_this_wp_components_["withSpokenMessages"])(
  /*#__PURE__*/
  function (_Component) {
    Object(inherits["a" /* default */])(LinkEdit, _Component);

    function LinkEdit() {
      var _this;

      Object(classCallCheck["a" /* default */])(this, LinkEdit);

      _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(LinkEdit).apply(this, arguments));
      _this.addLink = _this.addLink.bind(Object(assertThisInitialized["a" /* default */])(_this));
      _this.stopAddingLink = _this.stopAddingLink.bind(Object(assertThisInitialized["a" /* default */])(_this));
      _this.onRemoveFormat = _this.onRemoveFormat.bind(Object(assertThisInitialized["a" /* default */])(_this));
      _this.state = {
        addingLink: false
      };
      return _this;
    }

    Object(createClass["a" /* default */])(LinkEdit, [{
      key: "addLink",
      value: function addLink() {
        var _this$props = this.props,
            value = _this$props.value,
            onChange = _this$props.onChange;
        var text = Object(external_this_wp_richText_["getTextContent"])(Object(external_this_wp_richText_["slice"])(value));

        if (text && Object(external_this_wp_url_["isURL"])(text)) {
          onChange(Object(external_this_wp_richText_["applyFormat"])(value, {
            type: link_name,
            attributes: {
              url: text
            }
          }));
        } else if (text && Object(external_this_wp_url_["isEmail"])(text)) {
          onChange(Object(external_this_wp_richText_["applyFormat"])(value, {
            type: link_name,
            attributes: {
              url: "mailto:".concat(text)
            }
          }));
        } else {
          this.setState({
            addingLink: true
          });
        }
      }
    }, {
      key: "stopAddingLink",
      value: function stopAddingLink() {
        this.setState({
          addingLink: false
        });
        this.props.onFocus();
      }
    }, {
      key: "onRemoveFormat",
      value: function onRemoveFormat() {
        var _this$props2 = this.props,
            value = _this$props2.value,
            onChange = _this$props2.onChange,
            speak = _this$props2.speak;
        onChange(Object(external_this_wp_richText_["removeFormat"])(value, link_name));
        speak(Object(external_this_wp_i18n_["__"])('Link removed.'), 'assertive');
      }
    }, {
      key: "render",
      value: function render() {
        var _this$props3 = this.props,
            isActive = _this$props3.isActive,
            activeAttributes = _this$props3.activeAttributes,
            value = _this$props3.value,
            onChange = _this$props3.onChange;
        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextShortcut"], {
          type: "primary",
          character: "k",
          onUse: this.addLink
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextShortcut"], {
          type: "primaryShift",
          character: "k",
          onUse: this.onRemoveFormat
        }), isActive && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextToolbarButton"], {
          name: "link",
          icon: link_off,
          title: Object(external_this_wp_i18n_["__"])('Unlink'),
          onClick: this.onRemoveFormat,
          isActive: isActive,
          shortcutType: "primaryShift",
          shortcutCharacter: "k"
        }), !isActive && Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextToolbarButton"], {
          name: "link",
          icon: library_link["a" /* default */],
          title: link_title,
          onClick: this.addLink,
          isActive: isActive,
          shortcutType: "primary",
          shortcutCharacter: "k"
        }), (this.state.addingLink || isActive) && Object(external_this_wp_element_["createElement"])(inline, {
          addingLink: this.state.addingLink,
          stopAddingLink: this.stopAddingLink,
          isActive: isActive,
          activeAttributes: activeAttributes,
          value: value,
          onChange: onChange
        }));
      }
    }]);

    return LinkEdit;
  }(external_this_wp_element_["Component"]))
};

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/format-strikethrough.js


/**
 * WordPress dependencies
 */

var formatStrikethrough = Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_this_wp_element_["createElement"])(external_this_wp_primitives_["Path"], {
  d: "M15.82 12.25c.26 0 .5-.02.74-.07.23-.05.48-.12.73-.2v.84c-.46.17-.99.26-1.58.26-.88 0-1.54-.26-2.01-.79-.39-.44-.62-1.04-.68-1.79h-.94c.12.21.18.48.18.79 0 .54-.18.95-.55 1.26-.38.3-.9.45-1.56.45H8v-2.5H6.59l.93 2.5H6.49l-.59-1.67H3.62L3.04 13H2l.93-2.5H2v-1h1.31l.93-2.49H5.3l.92 2.49H8V7h1.77c1 0 1.41.17 1.77.41.37.24.55.62.55 1.13 0 .35-.09.64-.27.87l-.08.09h1.29c.05-.4.15-.77.31-1.1.23-.46.55-.82.98-1.06.43-.25.93-.37 1.51-.37.61 0 1.17.12 1.69.38l-.35.81c-.2-.1-.42-.18-.64-.25s-.46-.11-.71-.11c-.55 0-.99.2-1.31.59-.23.29-.38.66-.44 1.11H17v1h-2.95c.06.5.2.9.44 1.19.3.37.75.56 1.33.56zM4.44 8.96l-.18.54H5.3l-.22-.61c-.04-.11-.09-.28-.17-.51-.07-.24-.12-.41-.14-.51-.08.33-.18.69-.33 1.09zm4.53-1.09V9.5h1.19c.28-.02.49-.09.64-.18.19-.13.28-.35.28-.66 0-.28-.1-.48-.3-.61-.2-.12-.53-.18-.97-.18h-.84zm-3.33 2.64v-.01H3.91v.01h1.73zm5.28.01l-.03-.02H8.97v1.68h1.04c.4 0 .71-.08.92-.23.21-.16.31-.4.31-.74 0-.31-.11-.54-.32-.69z"
}));
/* harmony default export */ var format_strikethrough = (formatStrikethrough);

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/strikethrough/index.js


/**
 * WordPress dependencies
 */




var strikethrough_name = 'core/strikethrough';

var strikethrough_title = Object(external_this_wp_i18n_["__"])('Strikethrough');

var strikethrough = {
  name: strikethrough_name,
  title: strikethrough_title,
  tagName: 's',
  className: null,
  edit: function edit(_ref) {
    var isActive = _ref.isActive,
        value = _ref.value,
        onChange = _ref.onChange,
        onFocus = _ref.onFocus;

    function onClick() {
      onChange(Object(external_this_wp_richText_["toggleFormat"])(value, {
        type: strikethrough_name
      }));
      onFocus();
    }

    return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextToolbarButton"], {
      icon: format_strikethrough,
      title: strikethrough_title,
      onClick: onClick,
      isActive: isActive
    });
  }
};

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/underline/index.js


/**
 * WordPress dependencies
 */



var underline_name = 'core/underline';
var underline = {
  name: underline_name,
  title: Object(external_this_wp_i18n_["__"])('Underline'),
  tagName: 'span',
  className: null,
  attributes: {
    style: 'style'
  },
  edit: function edit(_ref) {
    var value = _ref.value,
        onChange = _ref.onChange;

    var onToggle = function onToggle() {
      onChange(Object(external_this_wp_richText_["toggleFormat"])(value, {
        type: underline_name,
        attributes: {
          style: 'text-decoration: underline;'
        }
      }));
    };

    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextShortcut"], {
      type: "primary",
      character: "u",
      onUse: onToggle
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["__unstableRichTextInputEvent"], {
      inputType: "formatUnderline",
      onInput: onToggle
    }));
  }
};

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(12);

// EXTERNAL MODULE: external {"this":["wp","dom"]}
var external_this_wp_dom_ = __webpack_require__(28);

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/text-color/inline.js




/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







function getActiveColor(formatName, formatValue, colors) {
  var activeColorFormat = Object(external_this_wp_richText_["getActiveFormat"])(formatValue, formatName);

  if (!activeColorFormat) {
    return;
  }

  var styleColor = activeColorFormat.attributes.style;

  if (styleColor) {
    return styleColor.replace(new RegExp("^color:\\s*"), '');
  }

  var currentClass = activeColorFormat.attributes.class;

  if (currentClass) {
    var colorSlug = currentClass.replace(/.*has-(.*?)-color.*/, '$1');
    return Object(external_this_wp_blockEditor_["getColorObjectByAttributeValues"])(colors, colorSlug).color;
  }
}

var inline_ColorPopoverAtLink = function ColorPopoverAtLink(_ref) {
  var addingColor = _ref.addingColor,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["addingColor"]);

  // There is no way to open a text formatter popover when another one is mounted.
  // The first popover will always be dismounted when a click outside happens, so we can store the
  // anchor Rect during the lifetime of the component.
  var anchorRect = Object(external_this_wp_element_["useMemo"])(function () {
    var selection = window.getSelection();
    var range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;

    if (!range) {
      return;
    }

    if (addingColor) {
      return Object(external_this_wp_dom_["getRectangleFromRange"])(range);
    }

    var element = range.startContainer; // If the caret is right before the element, select the next element.

    element = element.nextElementSibling || element;

    while (element.nodeType !== window.Node.ELEMENT_NODE) {
      element = element.parentNode;
    }

    var closest = element.closest('span');

    if (closest) {
      return closest.getBoundingClientRect();
    }
  }, []);

  if (!anchorRect) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["URLPopover"], Object(esm_extends["a" /* default */])({
    anchorRect: anchorRect
  }, props));
};

var inline_ColorPicker = function ColorPicker(_ref2) {
  var name = _ref2.name,
      value = _ref2.value,
      onChange = _ref2.onChange;
  var colors = Object(external_this_wp_data_["useSelect"])(function (select) {
    var _select = select('core/block-editor'),
        getSettings = _select.getSettings;

    return Object(external_this_lodash_["get"])(getSettings(), ['colors'], []);
  });
  var onColorChange = Object(external_this_wp_element_["useCallback"])(function (color) {
    if (color) {
      var colorObject = Object(external_this_wp_blockEditor_["getColorObjectByColorValue"])(colors, color);
      onChange(Object(external_this_wp_richText_["applyFormat"])(value, {
        type: name,
        attributes: colorObject ? {
          class: Object(external_this_wp_blockEditor_["getColorClassName"])('color', colorObject.slug)
        } : {
          style: "color:".concat(color)
        }
      }));
    } else {
      onChange(Object(external_this_wp_richText_["removeFormat"])(value, name));
    }
  }, [colors, onChange]);
  var activeColor = Object(external_this_wp_element_["useMemo"])(function () {
    return getActiveColor(name, value, colors);
  }, [name, value, colors]);
  return Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["ColorPalette"], {
    value: activeColor,
    onChange: onColorChange
  });
};

var inline_InlineColorUI = function InlineColorUI(_ref3) {
  var name = _ref3.name,
      value = _ref3.value,
      onChange = _ref3.onChange,
      onClose = _ref3.onClose,
      isActive = _ref3.isActive,
      addingColor = _ref3.addingColor;
  return Object(external_this_wp_element_["createElement"])(inline_ColorPopoverAtLink, {
    value: value,
    isActive: isActive,
    addingColor: addingColor,
    onClose: onClose,
    className: "components-inline-color-popover"
  }, Object(external_this_wp_element_["createElement"])(inline_ColorPicker, {
    name: name,
    value: value,
    onChange: onChange
  }));
};

/* harmony default export */ var text_color_inline = (Object(external_this_wp_components_["withSpokenMessages"])(inline_InlineColorUI));

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/text-color/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


var text_color_name = 'core/text-color';

var text_color_title = Object(external_this_wp_i18n_["__"])('Text Color');

var EMPTY_ARRAY = [];

function TextColorEdit(_ref) {
  var value = _ref.value,
      onChange = _ref.onChange,
      isActive = _ref.isActive,
      activeAttributes = _ref.activeAttributes;

  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
    var blockEditorSelect = select('core/block-editor');
    var settings;

    if (blockEditorSelect && blockEditorSelect.getSettings) {
      settings = blockEditorSelect.getSettings();
    } else {
      settings = {};
    }

    return {
      colors: Object(external_this_lodash_["get"])(settings, ['colors'], EMPTY_ARRAY),
      disableCustomColors: settings.disableCustomColors
    };
  }),
      colors = _useSelect.colors,
      disableCustomColors = _useSelect.disableCustomColors;

  var _useState = Object(external_this_wp_element_["useState"])(false),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      isAddingColor = _useState2[0],
      setIsAddingColor = _useState2[1];

  var enableIsAddingColor = Object(external_this_wp_element_["useCallback"])(function () {
    return setIsAddingColor(true);
  }, [setIsAddingColor]);
  var disableIsAddingColor = Object(external_this_wp_element_["useCallback"])(function () {
    return setIsAddingColor(false);
  }, [setIsAddingColor]);
  var colorIndicatorStyle = Object(external_this_wp_element_["useMemo"])(function () {
    var activeColor = getActiveColor(text_color_name, value, colors);

    if (!activeColor) {
      return undefined;
    }

    return {
      backgroundColor: activeColor
    };
  }, [value, colors]);
  var hasColorsToChoose = !Object(external_this_lodash_["isEmpty"])(colors) || disableCustomColors !== true;

  if (!hasColorsToChoose && !isActive) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_blockEditor_["RichTextToolbarButton"], {
    key: isActive ? 'text-color' : 'text-color-not-active',
    className: "format-library-text-color-button",
    name: isActive ? 'text-color' : undefined,
    icon: Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dashicon"], {
      icon: "editor-textcolor"
    }), isActive && Object(external_this_wp_element_["createElement"])("span", {
      className: "format-library-text-color-button__indicator",
      style: colorIndicatorStyle
    })),
    title: text_color_title // If has no colors to choose but a color is active remove the color onClick
    ,
    onClick: hasColorsToChoose ? enableIsAddingColor : function () {
      return onChange(Object(external_this_wp_richText_["removeFormat"])(value, text_color_name));
    }
  }), isAddingColor && Object(external_this_wp_element_["createElement"])(text_color_inline, {
    name: text_color_name,
    addingColor: isAddingColor,
    onClose: disableIsAddingColor,
    isActive: isActive,
    activeAttributes: activeAttributes,
    value: value,
    onChange: onChange
  }));
}

var textColor = {
  name: text_color_name,
  title: text_color_title,
  tagName: 'span',
  className: 'has-inline-color',
  attributes: {
    style: 'style',
    class: 'class'
  },
  edit: TextColorEdit
};

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/default-formats.js
/**
 * Internal dependencies
 */








/* harmony default export */ var default_formats = ([bold, code_code, image_image, italic, link_link, strikethrough, underline, textColor]);

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


default_formats.forEach(function (_ref) {
  var name = _ref.name,
      settings = Object(objectWithoutProperties["a" /* default */])(_ref, ["name"]);

  return Object(external_this_wp_richText_["registerFormatType"])(name, settings);
});


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

/***/ 58:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["htmlEntities"]; }());

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

/***/ 9:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["primitives"]; }());

/***/ })

/******/ });