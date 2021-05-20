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

/***/ "+Iak":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const resizeCornerNE = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12.5 4.2v1.6h4.7L5.8 17.2V12H4.2v7.8H12v-1.6H6.8L18.2 6.8v4.7h1.6V4.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (resizeCornerNE);


/***/ }),

/***/ "//Lo":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const blockDefault = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 8h-1V6h-5v2h-2V6H6v2H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm.5 10c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-8c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v8z"
}));
/* harmony default export */ __webpack_exports__["a"] = (blockDefault);


/***/ }),

/***/ "/DSv":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const crop = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M16.5 7.8v7H18v-7c0-1-.8-1.8-1.8-1.8h-7v1.5h7c.2 0 .3.1.3.3zm-8.7 8.7c-.1 0-.2-.1-.2-.2V2H6v4H2v1.5h4v8.8c0 1 .8 1.8 1.8 1.8h8.8v4H18v-4h4v-1.5H7.8z"
}));
/* harmony default export */ __webpack_exports__["a"] = (crop);


/***/ }),

/***/ "0Ene":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const plugins = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M10.5 4v4h3V4H15v4h1.5a1 1 0 011 1v4l-3 4v2a1 1 0 01-1 1h-3a1 1 0 01-1-1v-2l-3-4V9a1 1 0 011-1H9V4h1.5zm.5 12.5v2h2v-2l3-4v-3H8v3l3 4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (plugins);


/***/ }),

/***/ "0YTw":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const buttons = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M17 3H7c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm.5 6c0 .3-.2.5-.5.5H7c-.3 0-.5-.2-.5-.5V5c0-.3.2-.5.5-.5h10c.3 0 .5.2.5.5v4zm-8-1.2h5V6.2h-5v1.6zM17 13H7c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2v-4c0-1.1-.9-2-2-2zm.5 6c0 .3-.2.5-.5.5H7c-.3 0-.5-.2-.5-.5v-4c0-.3.2-.5.5-.5h10c.3 0 .5.2.5.5v4zm-8-1.2h5v-1.5h-5v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (buttons);


/***/ }),

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

/***/ "17x9":
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
  module.exports = __webpack_require__("16Al")();
}


/***/ }),

/***/ "1Yn1":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const code = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20.8 10.7l-4.3-4.3-1.1 1.1 4.3 4.3c.1.1.1.3 0 .4l-4.3 4.3 1.1 1.1 4.3-4.3c.7-.8.7-1.9 0-2.6zM4.2 11.8l4.3-4.3-1-1-4.3 4.3c-.7.7-.7 1.8 0 2.5l4.3 4.3 1.1-1.1-4.3-4.3c-.2-.1-.2-.3-.1-.4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (code);


/***/ }),

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "1iEr":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const chevronRight = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"
}));
/* harmony default export */ __webpack_exports__["a"] = (chevronRight);


/***/ }),

/***/ "2gm7":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const chevronLeft = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (chevronLeft);


/***/ }),

/***/ "3Nnr":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const blockTable = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 4.5h14c.3 0 .5.2.5.5v3.5h-15V5c0-.3.2-.5.5-.5zm8 5.5h6.5v3.5H13V10zm-1.5 3.5h-7V10h7v3.5zm-7 5.5v-4h7v4.5H5c-.3 0-.5-.2-.5-.5zm14.5.5h-6V15h6.5v4c0 .3-.2.5-.5.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (blockTable);


/***/ }),

/***/ "4DNc":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const table = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 6v11.5h16V6H4zm1.5 1.5h6V11h-6V7.5zm0 8.5v-3.5h6V16h-6zm13 0H13v-3.5h5.5V16zM13 11V7.5h5.5V11H13z"
}));
/* harmony default export */ __webpack_exports__["a"] = (table);


/***/ }),

/***/ "4S5K":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const positionLeft = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 9v6h14V9H4zm8-4.8H4v1.5h8V4.2zM4 19.8h8v-1.5H4v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (positionLeft);


/***/ }),

/***/ "51Zz":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["dataControls"]; }());

/***/ }),

/***/ "5SpR":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const fullscreen = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4.2 9h1.5V5.8H9V4.2H4.2V9zm14 9.2H15v1.5h4.8V15h-1.5v3.2zM15 4.2v1.5h3.2V9h1.5V4.2H15zM5.8 15H4.2v4.8H9v-1.5H5.8V15z"
}));
/* harmony default export */ __webpack_exports__["a"] = (fullscreen);


/***/ }),

/***/ "6aBm":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["mediaUtils"]; }());

/***/ }),

/***/ "6iTo":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const tag = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20.1 11.2l-6.7-6.7c-.1-.1-.3-.2-.5-.2H5c-.4-.1-.8.3-.8.7v7.8c0 .2.1.4.2.5l6.7 6.7c.2.2.5.4.7.5s.6.2.9.2c.3 0 .6-.1.9-.2.3-.1.5-.3.8-.5l5.6-5.6c.4-.4.7-1 .7-1.6.1-.6-.2-1.2-.6-1.6zM19 13.4L13.4 19c-.1.1-.2.1-.3.2-.2.1-.4.1-.6 0-.1 0-.2-.1-.3-.2l-6.5-6.5V5.8h6.8l6.5 6.5c.2.2.2.4.2.6 0 .1 0 .3-.2.5zM9 8c-.6 0-1 .4-1 1s.4 1 1 1 1-.4 1-1-.4-1-1-1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (tag);


/***/ }),

/***/ "7WSE":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const postExcerpt = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12.75 9.333c0 .521-.102.977-.327 1.354-.23.386-.555.628-.893.774-.545.234-1.183.227-1.544.222l-.12-.001v-1.5h.123c.414.001.715.002.948-.099a.395.395 0 00.199-.166c.05-.083.114-.253.114-.584V7.2H8.8V4h3.95v5.333zM7.95 9.333c0 .521-.102.977-.327 1.354-.23.386-.555.628-.893.774-.545.234-1.183.227-1.544.222l-.12-.001v-1.5h.123c.414.001.715.002.948-.099a.394.394 0 00.198-.166c.05-.083.115-.253.115-.584V7.2H4V4h3.95v5.333zM13 20H4v-1.5h9V20zM20 16H4v-1.5h16V16z"
}));
/* harmony default export */ __webpack_exports__["a"] = (postExcerpt);


/***/ }),

/***/ "7fqt":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["wordcount"]; }());

/***/ }),

/***/ "91dd":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const shortcode = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M16 4.2v1.5h2.5v12.5H16v1.5h4V4.2h-4zM4.2 19.8h4v-1.5H5.8V5.8h2.5V4.2h-4l-.1 15.6zm5.1-3.1l1.4.6 4-10-1.4-.6-4 10z"
}));
/* harmony default export */ __webpack_exports__["a"] = (shortcode);


/***/ }),

/***/ "9T6D":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const pullquote = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 8H6c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-4c0-1.1-.9-2-2-2zm.5 6c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5v-4c0-.3.2-.5.5-.5h12c.3 0 .5.2.5.5v4zM4 4v1.5h16V4H4zm0 16h16v-1.5H4V20z"
}));
/* harmony default export */ __webpack_exports__["a"] = (pullquote);


/***/ }),

/***/ "A2w8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const mapMarker = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "https://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12 9c-.8 0-1.5.7-1.5 1.5S11.2 12 12 12s1.5-.7 1.5-1.5S12.8 9 12 9zm0-5c-3.6 0-6.5 2.8-6.5 6.2 0 .8.3 1.8.9 3.1.5 1.1 1.2 2.3 2 3.6.7 1 3 3.8 3.2 3.9l.4.5.4-.5c.2-.2 2.6-2.9 3.2-3.9.8-1.2 1.5-2.5 2-3.6.6-1.3.9-2.3.9-3.1C18.5 6.8 15.6 4 12 4zm4.3 8.7c-.5 1-1.1 2.2-1.9 3.4-.5.7-1.7 2.2-2.4 3-.7-.8-1.9-2.3-2.4-3-.8-1.2-1.4-2.3-1.9-3.3-.6-1.4-.7-2.2-.7-2.5 0-2.6 2.2-4.7 5-4.7s5 2.1 5 4.7c0 .2-.1 1-.7 2.4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (mapMarker);


/***/ }),

/***/ "B/ly":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const cloudUpload = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M14.8 9c.1-.3.2-.6.2-1 0-2.2-1.8-4-4-4-1.5 0-2.9.9-3.5 2.2-.3-.1-.7-.2-1-.2C5.1 6 4 7.1 4 8.5c0 .2 0 .4.1.5-1.8.3-3.1 1.7-3.1 3.5C1 14.4 2.6 16 4.5 16H8v-3H5l4.5-4.5L14 13h-3v3h3.5c1.9 0 3.5-1.6 3.5-3.5 0-1.8-1.4-3.3-3.2-3.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (cloudUpload);


/***/ }),

/***/ "B9Az":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _pencil__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("L0kB");
/**
 * Internal dependencies
 */

/* harmony default export */ __webpack_exports__["a"] = (_pencil__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"]);


/***/ }),

/***/ "BRe6":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatOutdentRTL = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20 5.5H4V4H20V5.5ZM12 12.5H4V11H12V12.5ZM20 20V18.5H4V20H20ZM15.4697 14.9697L18.4393 12L15.4697 9.03033L16.5303 7.96967L20.0303 11.4697L20.5607 12L20.0303 12.5303L16.5303 16.0303L15.4697 14.9697Z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatOutdentRTL);


/***/ }),

/***/ "Bpkj":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const link = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M15.6 7.2H14v1.5h1.6c2 0 3.7 1.7 3.7 3.7s-1.7 3.7-3.7 3.7H14v1.5h1.6c2.8 0 5.2-2.3 5.2-5.2 0-2.9-2.3-5.2-5.2-5.2zM4.7 12.4c0-2 1.7-3.7 3.7-3.7H10V7.2H8.4c-2.9 0-5.2 2.3-5.2 5.2 0 2.9 2.3 5.2 5.2 5.2H10v-1.5H8.4c-2 0-3.7-1.7-3.7-3.7zm4.6.9h5.3v-1.5H9.3v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (link);


/***/ }),

/***/ "C5ux":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const tableColumnBefore = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6.4 3.776v3.648H2.752v1.792H6.4v3.648h1.728V9.216h3.712V7.424H8.128V3.776zM0 17.92V0h20.48v17.92H0zM12.8 1.28H1.28v14.08H12.8V1.28zm6.4 0h-5.12v3.84h5.12V1.28zm0 5.12h-5.12v3.84h5.12V6.4zm0 5.12h-5.12v3.84h5.12v-3.84z"
}));
/* harmony default export */ __webpack_exports__["a"] = (tableColumnBefore);


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

/***/ "CPWL":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const queryPagination = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Rect"], {
  x: "4",
  y: "10.5",
  width: "6",
  height: "3",
  rx: "1.5",
  fill: "#000"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Rect"], {
  x: "12",
  y: "10.5",
  width: "3",
  height: "3",
  rx: "1.5",
  fill: "#000"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Rect"], {
  x: "17",
  y: "10.5",
  width: "3",
  height: "3",
  rx: "1.5",
  fill: "#000"
}));
/* harmony default export */ __webpack_exports__["a"] = (queryPagination);


/***/ }),

/***/ "CQWb":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const category = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6 5.5h3a.5.5 0 01.5.5v3a.5.5 0 01-.5.5H6a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5zM4 6a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm11-.5h3a.5.5 0 01.5.5v3a.5.5 0 01-.5.5h-3a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5zM13 6a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2h-3a2 2 0 01-2-2V6zm5 8.5h-3a.5.5 0 00-.5.5v3a.5.5 0 00.5.5h3a.5.5 0 00.5-.5v-3a.5.5 0 00-.5-.5zM15 13a2 2 0 00-2 2v3a2 2 0 002 2h3a2 2 0 002-2v-3a2 2 0 00-2-2h-3zm-9 1.5h3a.5.5 0 01.5.5v3a.5.5 0 01-.5.5H6a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zM4 15a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H6a2 2 0 01-2-2v-3z",
  fillRule: "evenodd",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (category);


/***/ }),

/***/ "CfuN":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const postCategories = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20 4H4v1.5h16V4zm-2 9h-3c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h3c1.1 0 2-.9 2-2v-3c0-1.1-.9-2-2-2zm.5 5c0 .3-.2.5-.5.5h-3c-.3 0-.5-.2-.5-.5v-3c0-.3.2-.5.5-.5h3c.3 0 .5.2.5.5v3zM4 9.5h9V8H4v1.5zM9 13H6c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h3c1.1 0 2-.9 2-2v-3c0-1.1-.9-2-2-2zm.5 5c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5v-3c0-.3.2-.5.5-.5h3c.3 0 .5.2.5.5v3z",
  fillRule: "evenodd",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (postCategories);


/***/ }),

/***/ "Cg8A":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const cog = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  fillRule: "evenodd",
  d: "M10.289 4.836A1 1 0 0111.275 4h1.306a1 1 0 01.987.836l.244 1.466c.787.26 1.503.679 2.108 1.218l1.393-.522a1 1 0 011.216.437l.653 1.13a1 1 0 01-.23 1.273l-1.148.944a6.025 6.025 0 010 2.435l1.149.946a1 1 0 01.23 1.272l-.653 1.13a1 1 0 01-1.216.437l-1.394-.522c-.605.54-1.32.958-2.108 1.218l-.244 1.466a1 1 0 01-.987.836h-1.306a1 1 0 01-.986-.836l-.244-1.466a5.995 5.995 0 01-2.108-1.218l-1.394.522a1 1 0 01-1.217-.436l-.653-1.131a1 1 0 01.23-1.272l1.149-.946a6.026 6.026 0 010-2.435l-1.148-.944a1 1 0 01-.23-1.272l.653-1.131a1 1 0 011.217-.437l1.393.522a5.994 5.994 0 012.108-1.218l.244-1.466zM14.929 12a3 3 0 11-6 0 3 3 0 016 0z",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (cog);


/***/ }),

/***/ "Civd":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const layout = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (layout);


/***/ }),

/***/ "Crq9":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatStrikethrough = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M9.1 9v-.5c0-.6.2-1.1.7-1.4.5-.3 1.2-.5 2-.5.7 0 1.4.1 2.1.3.7.2 1.4.5 2.1.9l.2-1.9c-.6-.3-1.2-.5-1.9-.7-.8-.1-1.6-.2-2.4-.2-1.5 0-2.7.3-3.6 1-.8.7-1.2 1.5-1.2 2.6V9h2zM20 12H4v1h8.3c.3.1.6.2.8.3.5.2.9.5 1.1.8.3.3.4.7.4 1.2 0 .7-.2 1.1-.8 1.5-.5.3-1.2.5-2.1.5-.8 0-1.6-.1-2.4-.3-.8-.2-1.5-.5-2.2-.8L7 18.1c.5.2 1.2.4 2 .6.8.2 1.6.3 2.4.3 1.7 0 3-.3 3.9-1 .9-.7 1.3-1.6 1.3-2.8 0-.9-.2-1.7-.7-2.2H20v-1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatStrikethrough);


/***/ }),

/***/ "DUrk":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const list = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 4v1.5h16V4H4zm8 8.5h8V11h-8v1.5zM4 20h16v-1.5H4V20zm4-8c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2 2-.9 2-2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (list);


/***/ }),

/***/ "DmX/":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const justifyCenter = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20 9h-7.2V4h-1.6v5H4v6h7.2v5h1.6v-5H20z"
}));
/* harmony default export */ __webpack_exports__["a"] = (justifyCenter);


/***/ }),

/***/ "Dtd8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const columns = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 6H6c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2h13c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-4.1 1.5v10H10v-10h4.9zM5.5 17V8c0-.3.2-.5.5-.5h2.5v10H6c-.3 0-.5-.2-.5-.5zm14 0c0 .3-.2.5-.5.5h-2.6v-10H19c.3 0 .5.2.5.5v9z"
}));
/* harmony default export */ __webpack_exports__["a"] = (columns);


/***/ }),

/***/ "Egjd":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatIndent = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 7.2v1.5h16V7.2H4zm8 8.6h8v-1.5h-8v1.5zm-8-3.5l3 3-3 3 1 1 4-4-4-4-1 1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatIndent);


/***/ }),

/***/ "EgtK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const superscript = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M16.9 10.3l.8-1.3c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.2-.2-.4-.4-.7-.6-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.1 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3l-1.8 2.8h4.3v-1.2h-2.2zm-2.8-3.1h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z"
}));
/* harmony default export */ __webpack_exports__["a"] = (superscript);


/***/ }),

/***/ "EpZT":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const positionCenter = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5 15h14V9H5v6zm0 4.8h14v-1.5H5v1.5zM5 4.2v1.5h14V4.2H5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (positionCenter);


/***/ }),

/***/ "Esdn":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const separator = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20.2 7v4H3.8V7H2.2v9h1.6v-3.5h16.4V16h1.6V7z"
}));
/* harmony default export */ __webpack_exports__["a"] = (separator);


/***/ }),

/***/ "F636":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const pin = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M10.44 3.02l1.82-1.82 6.36 6.35-1.83 1.82c-1.05-.68-2.48-.57-3.41.36l-.75.75c-.92.93-1.04 2.35-.35 3.41l-1.83 1.82-2.41-2.41-2.8 2.79c-.42.42-3.38 2.71-3.8 2.29s1.86-3.39 2.28-3.81l2.79-2.79L4.1 9.36l1.83-1.82c1.05.69 2.48.57 3.4-.36l.75-.75c.93-.92 1.05-2.35.36-3.41z"
}));
/* harmony default export */ __webpack_exports__["a"] = (pin);


/***/ }),

/***/ "FAIV":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const rss = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5 10.2h-.8v1.5H5c1.9 0 3.8.8 5.1 2.1 1.4 1.4 2.1 3.2 2.1 5.1v.8h1.5V19c0-2.3-.9-4.5-2.6-6.2-1.6-1.6-3.8-2.6-6.1-2.6zm10.4-1.6C12.6 5.8 8.9 4.2 5 4.2h-.8v1.5H5c3.5 0 6.9 1.4 9.4 3.9s3.9 5.8 3.9 9.4v.8h1.5V19c0-3.9-1.6-7.6-4.4-10.4zM4 20h3v-3H4v3z"
}));
/* harmony default export */ __webpack_exports__["a"] = (rss);


/***/ }),

/***/ "FnAh":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const info = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (info);


/***/ }),

/***/ "FqII":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["date"]; }());

/***/ }),

/***/ "FtXe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const tableColumnDelete = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6.4 9.98L7.68 8.7v-.256L6.4 7.164V9.98zm6.4-1.532l1.28-1.28V9.92L12.8 8.64v-.192zm7.68 9.472V0H0v17.92h20.48zm-1.28-2.56h-5.12v-1.024l-.256.256-1.024-1.024v1.792H7.68v-1.792l-1.024 1.024-.256-.256v1.024H1.28V1.28H6.4v2.368l.704-.704.576.576V1.216h5.12V3.52l.96-.96.32.32V1.216h5.12V15.36zm-5.76-2.112l-3.136-3.136-3.264 3.264-1.536-1.536 3.264-3.264L5.632 5.44l1.536-1.536 3.136 3.136 3.2-3.2 1.536 1.536-3.2 3.2 3.136 3.136-1.536 1.536z"
}));
/* harmony default export */ __webpack_exports__["a"] = (tableColumnDelete);


/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "GemG":
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

/***/ "HSyU":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "I8zU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatOutdent = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 7.2v1.5h16V7.2H4zm8 8.6h8v-1.5h-8v1.5zm-4-4.6l-4 4 4 4 1-1-3-3 3-3-1-1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatOutdent);


/***/ }),

/***/ "ITpV":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const quote = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13 6v6h5.2v4c0 .8-.2 1.4-.5 1.7-.6.6-1.6.6-2.5.5h-.3v1.5h.5c1 0 2.3-.1 3.3-1 .6-.6 1-1.6 1-2.8V6H13zm-9 6h5.2v4c0 .8-.2 1.4-.5 1.7-.6.6-1.6.6-2.5.5h-.3v1.5h.5c1 0 2.3-.1 3.3-1 .6-.6 1-1.6 1-2.8V6H4v6z"
}));
/* harmony default export */ __webpack_exports__["a"] = (quote);


/***/ }),

/***/ "JREk":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["serverSideRender"]; }());

/***/ }),

/***/ "K+tz":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const external = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
}));
/* harmony default export */ __webpack_exports__["a"] = (external);


/***/ }),

/***/ "K2cm":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const redo = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (redo);


/***/ }),

/***/ "K8zD":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const globe = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M9 0C4.03 0 0 4.03 0 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zM1.11 9.68h2.51c.04.91.167 1.814.38 2.7H1.84c-.403-.85-.65-1.764-.73-2.7zm8.57-5.4V1.19c.964.366 1.756 1.08 2.22 2 .205.347.386.708.54 1.08l-2.76.01zm3.22 1.35c.232.883.37 1.788.41 2.7H9.68v-2.7h3.22zM8.32 1.19v3.09H5.56c.154-.372.335-.733.54-1.08.462-.924 1.255-1.64 2.22-2.01zm0 4.44v2.7H4.7c.04-.912.178-1.817.41-2.7h3.21zm-4.7 2.69H1.11c.08-.936.327-1.85.73-2.7H4c-.213.886-.34 1.79-.38 2.7zM4.7 9.68h3.62v2.7H5.11c-.232-.883-.37-1.788-.41-2.7zm3.63 4v3.09c-.964-.366-1.756-1.08-2.22-2-.205-.347-.386-.708-.54-1.08l2.76-.01zm1.35 3.09v-3.04h2.76c-.154.372-.335.733-.54 1.08-.464.92-1.256 1.634-2.22 2v-.04zm0-4.44v-2.7h3.62c-.04.912-.178 1.817-.41 2.7H9.68zm4.71-2.7h2.51c-.08.936-.327 1.85-.73 2.7H14c.21-.87.337-1.757.38-2.65l.01-.05zm0-1.35c-.046-.894-.176-1.78-.39-2.65h2.16c.403.85.65 1.764.73 2.7l-2.5-.05zm1-4H13.6c-.324-.91-.793-1.76-1.39-2.52 1.244.56 2.325 1.426 3.14 2.52h.04zm-9.6-2.52c-.597.76-1.066 1.61-1.39 2.52H2.65c.815-1.094 1.896-1.96 3.14-2.52zm-3.15 12H4.4c.324.91.793 1.76 1.39 2.52-1.248-.567-2.33-1.445-3.14-2.55l-.01.03zm9.56 2.52c.597-.76 1.066-1.61 1.39-2.52h1.76c-.82 1.08-1.9 1.933-3.14 2.48l-.01.04z"
}));
/* harmony default export */ __webpack_exports__["a"] = (globe);


/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "L0kB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const pencil = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20.1 5.1L16.9 2 6.2 12.7l-1.3 4.4 4.5-1.3L20.1 5.1zM4 20.8h8v-1.5H4v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (pencil);


/***/ }),

/***/ "LJOD":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const html = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4.8 11.4H2.1V9H1v6h1.1v-2.6h2.7V15h1.1V9H4.8v2.4zm1.9-1.3h1.7V15h1.1v-4.9h1.7V9H6.7v1.1zM16.2 9l-1.5 2.7L13.3 9h-.9l-.8 6h1.1l.5-4 1.5 2.8 1.5-2.8.5 4h1.1L17 9h-.8zm3.8 5V9h-1.1v6h3.6v-1H20z"
}));
/* harmony default export */ __webpack_exports__["a"] = (html);


/***/ }),

/***/ "LS+F":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const cloud = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M14.9 9c1.8.2 3.1 1.7 3.1 3.5 0 1.9-1.6 3.5-3.5 3.5h-10C2.6 16 1 14.4 1 12.5 1 10.7 2.3 9.3 4.1 9 4 8.9 4 8.7 4 8.5 4 7.1 5.1 6 6.5 6c.3 0 .7.1.9.2C8.1 4.9 9.4 4 11 4c2.2 0 4 1.8 4 4 0 .4-.1.7-.1 1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (cloud);


/***/ }),

/***/ "MOiY":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const queryPaginationNumbers = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Rect"], {
  x: "4",
  y: "10.5",
  width: "6",
  height: "3",
  rx: "1.5",
  fill: "#000"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13.5 14v-4l-1.5.5",
  stroke: "#1E1E1E"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19.266 9.805c-.473-.611-1.22-.51-1.702-.367a3.854 3.854 0 00-.718.307l.13 1.082c.192-.17.47-.422.782-.515.34-.1.578.025.668.141.21.27-.034.835-.16 1.055-.49.85-.93 1.594-1.45 2.492H19.5v-1h-.914c.277-.574.814-1.443.914-2.106.052-.343.02-.762-.234-1.09z",
  fill: "#1E1E1E"
}));
/* harmony default export */ __webpack_exports__["a"] = (queryPaginationNumbers);


/***/ }),

/***/ "Mmq9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["url"]; }());

/***/ }),

/***/ "Mp0b":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const linkOff = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M15.6 7.3h-.7l1.6-3.5-.9-.4-3.9 8.5H9v1.5h2l-1.3 2.8H8.4c-2 0-3.7-1.7-3.7-3.7s1.7-3.7 3.7-3.7H10V7.3H8.4c-2.9 0-5.2 2.3-5.2 5.2 0 2.9 2.3 5.2 5.2 5.2H9l-1.4 3.2.9.4 5.7-12.5h1.4c2 0 3.7 1.7 3.7 3.7s-1.7 3.7-3.7 3.7H14v1.5h1.6c2.9 0 5.2-2.3 5.2-5.2 0-2.9-2.4-5.2-5.2-5.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (linkOff);


/***/ }),

/***/ "NMb1":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["deprecated"]; }());

/***/ }),

/***/ "NTP4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const upload = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.5 15v3.5H13V6.7l4.5 4.1 1-1.1-6.2-5.8-5.8 5.8 1 1.1 4-4v11.7h-6V15H4v5h16v-5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (upload);


/***/ }),

/***/ "NWDH":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const chevronDown = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (chevronDown);


/***/ }),

/***/ "Ntru":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const undo = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"
}));
/* harmony default export */ __webpack_exports__["a"] = (undo);


/***/ }),

/***/ "O6Fj":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

exports.__esModule = true;
var TextareaAutosize_1 = __webpack_require__("CNgt");
exports["default"] = TextareaAutosize_1.TextareaAutosize;


/***/ }),

/***/ "OI5I":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const verse = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M17.8 2l-.9.3c-.1 0-3.6 1-5.2 2.1C10 5.5 9.3 6.5 8.9 7.1c-.6.9-1.7 4.7-1.7 6.3l-.9 2.3c-.2.4 0 .8.4 1 .1 0 .2.1.3.1.3 0 .6-.2.7-.5l.6-1.5c.3 0 .7-.1 1.2-.2.7-.1 1.4-.3 2.2-.5.8-.2 1.6-.5 2.4-.8.7-.3 1.4-.7 1.9-1.2s.8-1.2 1-1.9c.2-.7.3-1.6.4-2.4.1-.8.1-1.7.2-2.5 0-.8.1-1.5.2-2.1V2zm-1.9 5.6c-.1.8-.2 1.5-.3 2.1-.2.6-.4 1-.6 1.3-.3.3-.8.6-1.4.9-.7.3-1.4.5-2.2.8-.6.2-1.3.3-1.8.4L15 7.5c.3-.3.6-.7 1-1.1 0 .4 0 .8-.1 1.2zM6 20h8v-1.5H6V20z"
}));
/* harmony default export */ __webpack_exports__["a"] = (verse);


/***/ }),

/***/ "ONOV":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const tableRowAfter = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13.824 10.176h-2.88v-2.88H9.536v2.88h-2.88v1.344h2.88v2.88h1.408v-2.88h2.88zM0 17.92V0h20.48v17.92H0zM6.4 1.28H1.28v3.84H6.4V1.28zm6.4 0H7.68v3.84h5.12V1.28zm6.4 0h-5.12v3.84h5.12V1.28zm0 5.056H1.28v9.024H19.2V6.336z"
}));
/* harmony default export */ __webpack_exports__["a"] = (tableRowAfter);


/***/ }),

/***/ "OxzV":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatListNumberedRTL = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M3.8 15.8h8.9v-1.5H3.8v1.5zm0-7h8.9V7.2H3.8v1.6zm14.7-2.1V10h1V5.3l-2.2.7.3 1 .9-.3zm1.2 6.1c-.5-.6-1.2-.5-1.7-.4-.3.1-.5.2-.7.3l.1 1.1c.2-.2.5-.4.8-.5.3-.1.6 0 .7.1.2.3 0 .8-.2 1.1-.5.8-.9 1.6-1.4 2.5H20v-1h-.9c.3-.6.8-1.4.9-2.1 0-.3 0-.8-.3-1.1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatListNumberedRTL);


/***/ }),

/***/ "OzlF":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const listView = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13.8 5.2H3v1.5h10.8V5.2zm-3.6 12v1.5H21v-1.5H10.2zm7.2-6H6.6v1.5h10.8v-1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (listView);


/***/ }),

/***/ "PLxR":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "storeConfig", function() { return /* reexport */ storeConfig; });
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ store; });
__webpack_require__.d(__webpack_exports__, "userAutocompleter", function() { return /* reexport */ autocompleters_user; });
__webpack_require__.d(__webpack_exports__, "AutosaveMonitor", function() { return /* reexport */ autosave_monitor; });
__webpack_require__.d(__webpack_exports__, "DocumentOutline", function() { return /* reexport */ document_outline; });
__webpack_require__.d(__webpack_exports__, "DocumentOutlineCheck", function() { return /* reexport */ check; });
__webpack_require__.d(__webpack_exports__, "VisualEditorGlobalKeyboardShortcuts", function() { return /* reexport */ visual_editor_shortcuts; });
__webpack_require__.d(__webpack_exports__, "TextEditorGlobalKeyboardShortcuts", function() { return /* reexport */ TextEditorGlobalKeyboardShortcuts; });
__webpack_require__.d(__webpack_exports__, "EditorKeyboardShortcutsRegister", function() { return /* reexport */ register_shortcuts; });
__webpack_require__.d(__webpack_exports__, "EditorHistoryRedo", function() { return /* reexport */ editor_history_redo; });
__webpack_require__.d(__webpack_exports__, "EditorHistoryUndo", function() { return /* reexport */ editor_history_undo; });
__webpack_require__.d(__webpack_exports__, "EditorNotices", function() { return /* reexport */ editor_notices; });
__webpack_require__.d(__webpack_exports__, "EntitiesSavedStates", function() { return /* reexport */ EntitiesSavedStates; });
__webpack_require__.d(__webpack_exports__, "ErrorBoundary", function() { return /* reexport */ error_boundary; });
__webpack_require__.d(__webpack_exports__, "LocalAutosaveMonitor", function() { return /* reexport */ local_autosave_monitor; });
__webpack_require__.d(__webpack_exports__, "PageAttributesCheck", function() { return /* reexport */ page_attributes_check; });
__webpack_require__.d(__webpack_exports__, "PageAttributesOrder", function() { return /* reexport */ page_attributes_order; });
__webpack_require__.d(__webpack_exports__, "PageAttributesParent", function() { return /* reexport */ page_attributes_parent; });
__webpack_require__.d(__webpack_exports__, "PageTemplate", function() { return /* reexport */ post_template; });
__webpack_require__.d(__webpack_exports__, "PostAuthor", function() { return /* reexport */ post_author; });
__webpack_require__.d(__webpack_exports__, "PostAuthorCheck", function() { return /* reexport */ post_author_check; });
__webpack_require__.d(__webpack_exports__, "PostComments", function() { return /* reexport */ post_comments; });
__webpack_require__.d(__webpack_exports__, "PostExcerpt", function() { return /* reexport */ post_excerpt; });
__webpack_require__.d(__webpack_exports__, "PostExcerptCheck", function() { return /* reexport */ post_excerpt_check; });
__webpack_require__.d(__webpack_exports__, "PostFeaturedImage", function() { return /* reexport */ post_featured_image; });
__webpack_require__.d(__webpack_exports__, "PostFeaturedImageCheck", function() { return /* reexport */ post_featured_image_check; });
__webpack_require__.d(__webpack_exports__, "PostFormat", function() { return /* reexport */ PostFormat; });
__webpack_require__.d(__webpack_exports__, "PostFormatCheck", function() { return /* reexport */ post_format_check; });
__webpack_require__.d(__webpack_exports__, "PostLastRevision", function() { return /* reexport */ post_last_revision; });
__webpack_require__.d(__webpack_exports__, "PostLastRevisionCheck", function() { return /* reexport */ post_last_revision_check; });
__webpack_require__.d(__webpack_exports__, "PostLockedModal", function() { return /* reexport */ PostLockedModal; });
__webpack_require__.d(__webpack_exports__, "PostPendingStatus", function() { return /* reexport */ post_pending_status; });
__webpack_require__.d(__webpack_exports__, "PostPendingStatusCheck", function() { return /* reexport */ post_pending_status_check; });
__webpack_require__.d(__webpack_exports__, "PostPingbacks", function() { return /* reexport */ post_pingbacks; });
__webpack_require__.d(__webpack_exports__, "PostPreviewButton", function() { return /* reexport */ post_preview_button; });
__webpack_require__.d(__webpack_exports__, "PostPublishButton", function() { return /* reexport */ post_publish_button; });
__webpack_require__.d(__webpack_exports__, "PostPublishButtonLabel", function() { return /* reexport */ post_publish_button_label; });
__webpack_require__.d(__webpack_exports__, "PostPublishPanel", function() { return /* reexport */ post_publish_panel; });
__webpack_require__.d(__webpack_exports__, "PostSavedState", function() { return /* reexport */ PostSavedState; });
__webpack_require__.d(__webpack_exports__, "PostSchedule", function() { return /* reexport */ PostSchedule; });
__webpack_require__.d(__webpack_exports__, "PostScheduleCheck", function() { return /* reexport */ post_schedule_check; });
__webpack_require__.d(__webpack_exports__, "PostScheduleLabel", function() { return /* reexport */ post_schedule_label; });
__webpack_require__.d(__webpack_exports__, "PostSlug", function() { return /* reexport */ post_slug; });
__webpack_require__.d(__webpack_exports__, "PostSlugCheck", function() { return /* reexport */ PostSlugCheck; });
__webpack_require__.d(__webpack_exports__, "PostSticky", function() { return /* reexport */ post_sticky; });
__webpack_require__.d(__webpack_exports__, "PostStickyCheck", function() { return /* reexport */ post_sticky_check; });
__webpack_require__.d(__webpack_exports__, "PostSwitchToDraftButton", function() { return /* reexport */ post_switch_to_draft_button; });
__webpack_require__.d(__webpack_exports__, "PostTaxonomies", function() { return /* reexport */ post_taxonomies; });
__webpack_require__.d(__webpack_exports__, "PostTaxonomiesCheck", function() { return /* reexport */ post_taxonomies_check; });
__webpack_require__.d(__webpack_exports__, "PostTextEditor", function() { return /* reexport */ PostTextEditor; });
__webpack_require__.d(__webpack_exports__, "PostTitle", function() { return /* reexport */ PostTitle; });
__webpack_require__.d(__webpack_exports__, "PostTrash", function() { return /* reexport */ post_trash; });
__webpack_require__.d(__webpack_exports__, "PostTrashCheck", function() { return /* reexport */ post_trash_check; });
__webpack_require__.d(__webpack_exports__, "PostTypeSupportCheck", function() { return /* reexport */ post_type_support_check; });
__webpack_require__.d(__webpack_exports__, "PostVisibility", function() { return /* reexport */ post_visibility; });
__webpack_require__.d(__webpack_exports__, "PostVisibilityLabel", function() { return /* reexport */ post_visibility_label; });
__webpack_require__.d(__webpack_exports__, "PostVisibilityCheck", function() { return /* reexport */ post_visibility_check; });
__webpack_require__.d(__webpack_exports__, "TableOfContents", function() { return /* reexport */ table_of_contents; });
__webpack_require__.d(__webpack_exports__, "UnsavedChangesWarning", function() { return /* reexport */ UnsavedChangesWarning; });
__webpack_require__.d(__webpack_exports__, "WordCount", function() { return /* reexport */ WordCount; });
__webpack_require__.d(__webpack_exports__, "EditorProvider", function() { return /* reexport */ provider; });
__webpack_require__.d(__webpack_exports__, "ServerSideRender", function() { return /* reexport */ external_wp_serverSideRender_default.a; });
__webpack_require__.d(__webpack_exports__, "RichText", function() { return /* reexport */ RichText; });
__webpack_require__.d(__webpack_exports__, "Autocomplete", function() { return /* reexport */ Autocomplete; });
__webpack_require__.d(__webpack_exports__, "AlignmentToolbar", function() { return /* reexport */ AlignmentToolbar; });
__webpack_require__.d(__webpack_exports__, "BlockAlignmentToolbar", function() { return /* reexport */ BlockAlignmentToolbar; });
__webpack_require__.d(__webpack_exports__, "BlockControls", function() { return /* reexport */ BlockControls; });
__webpack_require__.d(__webpack_exports__, "BlockEdit", function() { return /* reexport */ deprecated_BlockEdit; });
__webpack_require__.d(__webpack_exports__, "BlockEditorKeyboardShortcuts", function() { return /* reexport */ BlockEditorKeyboardShortcuts; });
__webpack_require__.d(__webpack_exports__, "BlockFormatControls", function() { return /* reexport */ BlockFormatControls; });
__webpack_require__.d(__webpack_exports__, "BlockIcon", function() { return /* reexport */ BlockIcon; });
__webpack_require__.d(__webpack_exports__, "BlockInspector", function() { return /* reexport */ BlockInspector; });
__webpack_require__.d(__webpack_exports__, "BlockList", function() { return /* reexport */ BlockList; });
__webpack_require__.d(__webpack_exports__, "BlockMover", function() { return /* reexport */ BlockMover; });
__webpack_require__.d(__webpack_exports__, "BlockNavigationDropdown", function() { return /* reexport */ BlockNavigationDropdown; });
__webpack_require__.d(__webpack_exports__, "BlockSelectionClearer", function() { return /* reexport */ BlockSelectionClearer; });
__webpack_require__.d(__webpack_exports__, "BlockSettingsMenu", function() { return /* reexport */ BlockSettingsMenu; });
__webpack_require__.d(__webpack_exports__, "BlockTitle", function() { return /* reexport */ BlockTitle; });
__webpack_require__.d(__webpack_exports__, "BlockToolbar", function() { return /* reexport */ BlockToolbar; });
__webpack_require__.d(__webpack_exports__, "ColorPalette", function() { return /* reexport */ ColorPalette; });
__webpack_require__.d(__webpack_exports__, "ContrastChecker", function() { return /* reexport */ ContrastChecker; });
__webpack_require__.d(__webpack_exports__, "CopyHandler", function() { return /* reexport */ CopyHandler; });
__webpack_require__.d(__webpack_exports__, "DefaultBlockAppender", function() { return /* reexport */ DefaultBlockAppender; });
__webpack_require__.d(__webpack_exports__, "FontSizePicker", function() { return /* reexport */ FontSizePicker; });
__webpack_require__.d(__webpack_exports__, "Inserter", function() { return /* reexport */ Inserter; });
__webpack_require__.d(__webpack_exports__, "InnerBlocks", function() { return /* reexport */ InnerBlocks; });
__webpack_require__.d(__webpack_exports__, "InspectorAdvancedControls", function() { return /* reexport */ InspectorAdvancedControls; });
__webpack_require__.d(__webpack_exports__, "InspectorControls", function() { return /* reexport */ InspectorControls; });
__webpack_require__.d(__webpack_exports__, "PanelColorSettings", function() { return /* reexport */ PanelColorSettings; });
__webpack_require__.d(__webpack_exports__, "PlainText", function() { return /* reexport */ PlainText; });
__webpack_require__.d(__webpack_exports__, "RichTextShortcut", function() { return /* reexport */ RichTextShortcut; });
__webpack_require__.d(__webpack_exports__, "RichTextToolbarButton", function() { return /* reexport */ RichTextToolbarButton; });
__webpack_require__.d(__webpack_exports__, "__unstableRichTextInputEvent", function() { return /* reexport */ __unstableRichTextInputEvent; });
__webpack_require__.d(__webpack_exports__, "MediaPlaceholder", function() { return /* reexport */ MediaPlaceholder; });
__webpack_require__.d(__webpack_exports__, "MediaUpload", function() { return /* reexport */ MediaUpload; });
__webpack_require__.d(__webpack_exports__, "MediaUploadCheck", function() { return /* reexport */ MediaUploadCheck; });
__webpack_require__.d(__webpack_exports__, "MultiSelectScrollIntoView", function() { return /* reexport */ MultiSelectScrollIntoView; });
__webpack_require__.d(__webpack_exports__, "NavigableToolbar", function() { return /* reexport */ NavigableToolbar; });
__webpack_require__.d(__webpack_exports__, "ObserveTyping", function() { return /* reexport */ ObserveTyping; });
__webpack_require__.d(__webpack_exports__, "PreserveScrollInReorder", function() { return /* reexport */ PreserveScrollInReorder; });
__webpack_require__.d(__webpack_exports__, "SkipToSelectedBlock", function() { return /* reexport */ SkipToSelectedBlock; });
__webpack_require__.d(__webpack_exports__, "URLInput", function() { return /* reexport */ URLInput; });
__webpack_require__.d(__webpack_exports__, "URLInputButton", function() { return /* reexport */ URLInputButton; });
__webpack_require__.d(__webpack_exports__, "URLPopover", function() { return /* reexport */ URLPopover; });
__webpack_require__.d(__webpack_exports__, "Warning", function() { return /* reexport */ Warning; });
__webpack_require__.d(__webpack_exports__, "WritingFlow", function() { return /* reexport */ WritingFlow; });
__webpack_require__.d(__webpack_exports__, "createCustomColorsHOC", function() { return /* reexport */ createCustomColorsHOC; });
__webpack_require__.d(__webpack_exports__, "getColorClassName", function() { return /* reexport */ getColorClassName; });
__webpack_require__.d(__webpack_exports__, "getColorObjectByAttributeValues", function() { return /* reexport */ getColorObjectByAttributeValues; });
__webpack_require__.d(__webpack_exports__, "getColorObjectByColorValue", function() { return /* reexport */ getColorObjectByColorValue; });
__webpack_require__.d(__webpack_exports__, "getFontSize", function() { return /* reexport */ getFontSize; });
__webpack_require__.d(__webpack_exports__, "getFontSizeClass", function() { return /* reexport */ getFontSizeClass; });
__webpack_require__.d(__webpack_exports__, "withColorContext", function() { return /* reexport */ withColorContext; });
__webpack_require__.d(__webpack_exports__, "withColors", function() { return /* reexport */ withColors; });
__webpack_require__.d(__webpack_exports__, "withFontSizes", function() { return /* reexport */ withFontSizes; });
__webpack_require__.d(__webpack_exports__, "mediaUpload", function() { return /* reexport */ mediaUpload; });
__webpack_require__.d(__webpack_exports__, "cleanForSlug", function() { return /* reexport */ cleanForSlug; });
__webpack_require__.d(__webpack_exports__, "transformStyles", function() { return /* reexport */ external_wp_blockEditor_["transformStyles"]; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/editor/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "hasEditorUndo", function() { return hasEditorUndo; });
__webpack_require__.d(selectors_namespaceObject, "hasEditorRedo", function() { return hasEditorRedo; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostNew", function() { return selectors_isEditedPostNew; });
__webpack_require__.d(selectors_namespaceObject, "hasChangedContent", function() { return hasChangedContent; });
__webpack_require__.d(selectors_namespaceObject, "isEditedPostDirty", function() { return selectors_isEditedPostDirty; });
__webpack_require__.d(selectors_namespaceObject, "hasNonPostEntityChanges", function() { return selectors_hasNonPostEntityChanges; });
__webpack_require__.d(selectors_namespaceObject, "isCleanNewPost", function() { return selectors_isCleanNewPost; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPost", function() { return selectors_getCurrentPost; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostType", function() { return selectors_getCurrentPostType; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostId", function() { return selectors_getCurrentPostId; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostRevisionsCount", function() { return getCurrentPostRevisionsCount; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostLastRevisionId", function() { return getCurrentPostLastRevisionId; });
__webpack_require__.d(selectors_namespaceObject, "getPostEdits", function() { return selectors_getPostEdits; });
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
__webpack_require__.d(selectors_namespaceObject, "isPublishingPost", function() { return selectors_isPublishingPost; });
__webpack_require__.d(selectors_namespaceObject, "isPermalinkEditable", function() { return isPermalinkEditable; });
__webpack_require__.d(selectors_namespaceObject, "getPermalink", function() { return getPermalink; });
__webpack_require__.d(selectors_namespaceObject, "getEditedPostSlug", function() { return getEditedPostSlug; });
__webpack_require__.d(selectors_namespaceObject, "getPermalinkParts", function() { return getPermalinkParts; });
__webpack_require__.d(selectors_namespaceObject, "isPostLocked", function() { return isPostLocked; });
__webpack_require__.d(selectors_namespaceObject, "isPostSavingLocked", function() { return selectors_isPostSavingLocked; });
__webpack_require__.d(selectors_namespaceObject, "isPostAutosavingLocked", function() { return isPostAutosavingLocked; });
__webpack_require__.d(selectors_namespaceObject, "isPostLockTakeover", function() { return isPostLockTakeover; });
__webpack_require__.d(selectors_namespaceObject, "getPostLockUser", function() { return getPostLockUser; });
__webpack_require__.d(selectors_namespaceObject, "getActivePostLock", function() { return getActivePostLock; });
__webpack_require__.d(selectors_namespaceObject, "canUserUseUnfilteredHTML", function() { return selectors_canUserUseUnfilteredHTML; });
__webpack_require__.d(selectors_namespaceObject, "isPublishSidebarEnabled", function() { return selectors_isPublishSidebarEnabled; });
__webpack_require__.d(selectors_namespaceObject, "getEditorBlocks", function() { return getEditorBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getEditorSelectionStart", function() { return getEditorSelectionStart; });
__webpack_require__.d(selectors_namespaceObject, "getEditorSelectionEnd", function() { return getEditorSelectionEnd; });
__webpack_require__.d(selectors_namespaceObject, "getEditorSelection", function() { return selectors_getEditorSelection; });
__webpack_require__.d(selectors_namespaceObject, "__unstableIsEditorReady", function() { return selectors_unstableIsEditorReady; });
__webpack_require__.d(selectors_namespaceObject, "getEditorSettings", function() { return selectors_getEditorSettings; });
__webpack_require__.d(selectors_namespaceObject, "getStateBeforeOptimisticTransaction", function() { return getStateBeforeOptimisticTransaction; });
__webpack_require__.d(selectors_namespaceObject, "inSomeHistory", function() { return inSomeHistory; });
__webpack_require__.d(selectors_namespaceObject, "getBlockName", function() { return getBlockName; });
__webpack_require__.d(selectors_namespaceObject, "isBlockValid", function() { return isBlockValid; });
__webpack_require__.d(selectors_namespaceObject, "getBlockAttributes", function() { return getBlockAttributes; });
__webpack_require__.d(selectors_namespaceObject, "getBlock", function() { return getBlock; });
__webpack_require__.d(selectors_namespaceObject, "getBlocks", function() { return selectors_getBlocks; });
__webpack_require__.d(selectors_namespaceObject, "__unstableGetBlockWithoutInnerBlocks", function() { return __unstableGetBlockWithoutInnerBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getClientIdsOfDescendants", function() { return getClientIdsOfDescendants; });
__webpack_require__.d(selectors_namespaceObject, "getClientIdsWithDescendants", function() { return getClientIdsWithDescendants; });
__webpack_require__.d(selectors_namespaceObject, "getGlobalBlockCount", function() { return selectors_getGlobalBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "getBlocksByClientId", function() { return getBlocksByClientId; });
__webpack_require__.d(selectors_namespaceObject, "getBlockCount", function() { return getBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "getBlockSelectionStart", function() { return getBlockSelectionStart; });
__webpack_require__.d(selectors_namespaceObject, "getBlockSelectionEnd", function() { return getBlockSelectionEnd; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlockCount", function() { return getSelectedBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "hasSelectedBlock", function() { return hasSelectedBlock; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlockClientId", function() { return getSelectedBlockClientId; });
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
__webpack_require__.d(selectors_namespaceObject, "canInsertBlockType", function() { return canInsertBlockType; });
__webpack_require__.d(selectors_namespaceObject, "getInserterItems", function() { return getInserterItems; });
__webpack_require__.d(selectors_namespaceObject, "hasInserterItems", function() { return hasInserterItems; });
__webpack_require__.d(selectors_namespaceObject, "getBlockListSettings", function() { return getBlockListSettings; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetDefaultTemplateTypes", function() { return __experimentalGetDefaultTemplateTypes; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetDefaultTemplatePartAreas", function() { return __experimentalGetDefaultTemplatePartAreas; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetDefaultTemplateType", function() { return __experimentalGetDefaultTemplateType; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetTemplateInfo", function() { return __experimentalGetTemplateInfo; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/editor/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "setupEditor", function() { return actions_setupEditor; });
__webpack_require__.d(actions_namespaceObject, "__experimentalTearDownEditor", function() { return actions_experimentalTearDownEditor; });
__webpack_require__.d(actions_namespaceObject, "resetPost", function() { return resetPost; });
__webpack_require__.d(actions_namespaceObject, "resetAutosave", function() { return resetAutosave; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRequestPostUpdateStart", function() { return __experimentalRequestPostUpdateStart; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRequestPostUpdateFinish", function() { return __experimentalRequestPostUpdateFinish; });
__webpack_require__.d(actions_namespaceObject, "updatePost", function() { return updatePost; });
__webpack_require__.d(actions_namespaceObject, "setupEditorState", function() { return setupEditorState; });
__webpack_require__.d(actions_namespaceObject, "editPost", function() { return actions_editPost; });
__webpack_require__.d(actions_namespaceObject, "savePost", function() { return actions_savePost; });
__webpack_require__.d(actions_namespaceObject, "refreshPost", function() { return refreshPost; });
__webpack_require__.d(actions_namespaceObject, "trashPost", function() { return trashPost; });
__webpack_require__.d(actions_namespaceObject, "autosave", function() { return actions_autosave; });
__webpack_require__.d(actions_namespaceObject, "redo", function() { return actions_redo; });
__webpack_require__.d(actions_namespaceObject, "undo", function() { return actions_undo; });
__webpack_require__.d(actions_namespaceObject, "createUndoLevel", function() { return createUndoLevel; });
__webpack_require__.d(actions_namespaceObject, "updatePostLock", function() { return actions_updatePostLock; });
__webpack_require__.d(actions_namespaceObject, "enablePublishSidebar", function() { return enablePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "disablePublishSidebar", function() { return disablePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "lockPostSaving", function() { return lockPostSaving; });
__webpack_require__.d(actions_namespaceObject, "unlockPostSaving", function() { return unlockPostSaving; });
__webpack_require__.d(actions_namespaceObject, "lockPostAutosaving", function() { return lockPostAutosaving; });
__webpack_require__.d(actions_namespaceObject, "unlockPostAutosaving", function() { return unlockPostAutosaving; });
__webpack_require__.d(actions_namespaceObject, "resetEditorBlocks", function() { return actions_resetEditorBlocks; });
__webpack_require__.d(actions_namespaceObject, "updateEditorSettings", function() { return actions_updateEditorSettings; });
__webpack_require__.d(actions_namespaceObject, "resetBlocks", function() { return resetBlocks; });
__webpack_require__.d(actions_namespaceObject, "receiveBlocks", function() { return receiveBlocks; });
__webpack_require__.d(actions_namespaceObject, "updateBlock", function() { return updateBlock; });
__webpack_require__.d(actions_namespaceObject, "updateBlockAttributes", function() { return updateBlockAttributes; });
__webpack_require__.d(actions_namespaceObject, "selectBlock", function() { return actions_selectBlock; });
__webpack_require__.d(actions_namespaceObject, "startMultiSelect", function() { return startMultiSelect; });
__webpack_require__.d(actions_namespaceObject, "stopMultiSelect", function() { return stopMultiSelect; });
__webpack_require__.d(actions_namespaceObject, "multiSelect", function() { return multiSelect; });
__webpack_require__.d(actions_namespaceObject, "clearSelectedBlock", function() { return actions_clearSelectedBlock; });
__webpack_require__.d(actions_namespaceObject, "toggleSelection", function() { return toggleSelection; });
__webpack_require__.d(actions_namespaceObject, "replaceBlocks", function() { return replaceBlocks; });
__webpack_require__.d(actions_namespaceObject, "replaceBlock", function() { return replaceBlock; });
__webpack_require__.d(actions_namespaceObject, "moveBlocksDown", function() { return moveBlocksDown; });
__webpack_require__.d(actions_namespaceObject, "moveBlocksUp", function() { return moveBlocksUp; });
__webpack_require__.d(actions_namespaceObject, "moveBlockToPosition", function() { return moveBlockToPosition; });
__webpack_require__.d(actions_namespaceObject, "insertBlock", function() { return insertBlock; });
__webpack_require__.d(actions_namespaceObject, "insertBlocks", function() { return actions_insertBlocks; });
__webpack_require__.d(actions_namespaceObject, "showInsertionPoint", function() { return showInsertionPoint; });
__webpack_require__.d(actions_namespaceObject, "hideInsertionPoint", function() { return hideInsertionPoint; });
__webpack_require__.d(actions_namespaceObject, "setTemplateValidity", function() { return actions_setTemplateValidity; });
__webpack_require__.d(actions_namespaceObject, "synchronizeTemplate", function() { return actions_synchronizeTemplate; });
__webpack_require__.d(actions_namespaceObject, "mergeBlocks", function() { return mergeBlocks; });
__webpack_require__.d(actions_namespaceObject, "removeBlocks", function() { return removeBlocks; });
__webpack_require__.d(actions_namespaceObject, "removeBlock", function() { return removeBlock; });
__webpack_require__.d(actions_namespaceObject, "toggleBlockMode", function() { return toggleBlockMode; });
__webpack_require__.d(actions_namespaceObject, "startTyping", function() { return startTyping; });
__webpack_require__.d(actions_namespaceObject, "stopTyping", function() { return stopTyping; });
__webpack_require__.d(actions_namespaceObject, "enterFormattedText", function() { return enterFormattedText; });
__webpack_require__.d(actions_namespaceObject, "exitFormattedText", function() { return exitFormattedText; });
__webpack_require__.d(actions_namespaceObject, "insertDefaultBlock", function() { return actions_insertDefaultBlock; });
__webpack_require__.d(actions_namespaceObject, "updateBlockListSettings", function() { return updateBlockListSettings; });

// EXTERNAL MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_ = __webpack_require__("axFQ");

// EXTERNAL MODULE: external ["wp","coreData"]
var external_wp_coreData_ = __webpack_require__("jZUy");

// EXTERNAL MODULE: external ["wp","richText"]
var external_wp_richText_ = __webpack_require__("qRz9");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// EXTERNAL MODULE: external ["wp","hooks"]
var external_wp_hooks_ = __webpack_require__("g56x");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/hooks/custom-sources-backwards-compatibility.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
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

const createWithMetaAttributeSource = metaAttributes => Object(external_wp_compose_["createHigherOrderComponent"])(BlockEdit => ({
  attributes,
  setAttributes,
  ...props
}) => {
  const postType = Object(external_wp_data_["useSelect"])(select => select('core/editor').getCurrentPostType(), []);
  const [meta, setMeta] = Object(external_wp_coreData_["useEntityProp"])('postType', postType, 'meta');
  const mergedAttributes = Object(external_wp_element_["useMemo"])(() => ({ ...attributes,
    ...Object(external_lodash_["mapValues"])(metaAttributes, metaKey => meta[metaKey])
  }), [attributes, meta]);
  return Object(external_wp_element_["createElement"])(BlockEdit, Object(esm_extends["a" /* default */])({
    attributes: mergedAttributes,
    setAttributes: nextAttributes => {
      const nextMeta = Object(external_lodash_["mapKeys"])( // Filter to intersection of keys between the updated
      // attributes and those with an associated meta key.
      Object(external_lodash_["pickBy"])(nextAttributes, (value, key) => metaAttributes[key]), // Rename the keys to the expected meta key name.
      (value, attributeKey) => metaAttributes[attributeKey]);

      if (!Object(external_lodash_["isEmpty"])(nextMeta)) {
        setMeta(nextMeta);
      }

      setAttributes(nextAttributes);
    }
  }, props));
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
  /** @type {WPMetaAttributeMapping} */
  const metaAttributes = Object(external_lodash_["mapValues"])(Object(external_lodash_["pickBy"])(settings.attributes, {
    source: 'meta'
  }), 'meta');

  if (!Object(external_lodash_["isEmpty"])(metaAttributes)) {
    settings.edit = createWithMetaAttributeSource(metaAttributes)(settings.edit);
  }

  return settings;
}

Object(external_wp_hooks_["addFilter"])('blocks.registerBlockType', 'core/editor/custom-sources-backwards-compatibility/shim-attribute-source', shimAttributeSource); // The above filter will only capture blocks registered after the filter was
// added. There may already be blocks registered by this point, and those must
// be updated to apply the shim.
//
// The following implementation achieves this, albeit with a couple caveats:
// - Only blocks registered on the global store will be modified.
// - The block settings are directly mutated, since there is currently no
//   mechanism to update an existing block registration. This is the reason for
//   `getBlockType` separate from `getBlockTypes`, since the latter returns a
//   _copy_ of the block registration (i.e. the mutation would not affect the
//   actual registered block settings).
//
// `getBlockTypes` or `getBlockType` implementation could change in the future
// in regards to creating settings clones, but the corresponding end-to-end
// tests for meta blocks should cover against any potential regressions.
//
// In the future, we could support updating block settings, at which point this
// implementation could use that mechanism instead.

Object(external_wp_data_["select"])(external_wp_blocks_["store"]).getBlockTypes().map(({
  name
}) => Object(external_wp_data_["select"])(external_wp_blocks_["store"]).getBlockType(name)).forEach(shimAttributeSource);

// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__("ywyh");
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autocompleters/user.js


/**
 * WordPress dependencies
 */

/** @typedef {import('@wordpress/components').WPCompleter} WPCompleter */

/**
 * A user mentions completer.
 *
 * @type {WPCompleter}
 */

/* harmony default export */ var autocompleters_user = ({
  name: 'users',
  className: 'editor-autocompleters__user',
  triggerPrefix: '@',

  options(search) {
    let payload = '';

    if (search) {
      payload = '?search=' + encodeURIComponent(search);
    }

    return external_wp_apiFetch_default()({
      path: '/wp/v2/users' + payload
    });
  },

  isDebounced: true,

  getOptionKeywords(user) {
    return [user.slug, user.name];
  },

  getOptionLabel(user) {
    const avatar = user.avatar_urls && user.avatar_urls[24] ? Object(external_wp_element_["createElement"])("img", {
      key: "avatar",
      className: "editor-autocompleters__user-avatar",
      alt: "",
      src: user.avatar_urls[24]
    }) : Object(external_wp_element_["createElement"])("span", {
      className: "editor-autocompleters__no-avatar"
    });
    return [avatar, Object(external_wp_element_["createElement"])("span", {
      key: "name",
      className: "editor-autocompleters__user-name"
    }, user.name), Object(external_wp_element_["createElement"])("span", {
      key: "slug",
      className: "editor-autocompleters__user-slug"
    }, user.slug)];
  },

  getOptionCompletion(user) {
    return `@${user.slug}`;
  }

});

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



function setDefaultCompleters(completers = []) {
  // Provide copies so filters may directly modify them.
  completers.push(Object(external_lodash_["clone"])(autocompleters_user));
  return completers;
}

Object(external_wp_hooks_["addFilter"])('editor.Autocomplete.completers', 'editor/autocompleters/set-default-completers', setDefaultCompleters);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/hooks/index.js
/**
 * Internal dependencies
 */



// EXTERNAL MODULE: external ["wp","dataControls"]
var external_wp_dataControls_ = __webpack_require__("51Zz");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/defaults.js
/**
 * WordPress dependencies
 */

const PREFERENCES_DEFAULTS = {
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
 *  supportsLayout     boolean      Whether the editor supports layouts.
 */

const EDITOR_SETTINGS_DEFAULTS = { ...external_wp_blockEditor_["SETTINGS_DEFAULTS"],
  richEditingEnabled: true,
  codeEditingEnabled: true,
  enableCustomFields: false,
  supportsLayout: true
};

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
function reducer_postId(state = null, action) {
  switch (action.type) {
    case 'SETUP_EDITOR_STATE':
    case 'RESET_POST':
      return action.post.id;
  }

  return state;
}
function reducer_postType(state = null, action) {
  switch (action.type) {
    case 'SETUP_EDITOR_STATE':
    case 'RESET_POST':
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

function reducer_template(state = {
  isValid: true
}, action) {
  switch (action.type) {
    case 'SET_TEMPLATE_VALIDITY':
      return { ...state,
        isValid: action.isValid
      };
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

function preferences(state = PREFERENCES_DEFAULTS, action) {
  switch (action.type) {
    case 'ENABLE_PUBLISH_SIDEBAR':
      return { ...state,
        isPublishSidebarEnabled: true
      };

    case 'DISABLE_PUBLISH_SIDEBAR':
      return { ...state,
        isPublishSidebarEnabled: false
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
      return { ...state,
        [action.lockName]: true
      };

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
 * @param {PostLockState} state  Current state.
 * @param {Object}        action Dispatched action.
 *
 * @return {PostLockState} Updated state.
 */

function postAutosavingLock(state = {}, action) {
  switch (action.type) {
    case 'LOCK_POST_AUTOSAVING':
      return { ...state,
        [action.lockName]: true
      };

    case 'UNLOCK_POST_AUTOSAVING':
      return Object(external_lodash_["omit"])(state, action.lockName);
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

function reducer_isReady(state = false, action) {
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

function reducer_editorSettings(state = EDITOR_SETTINGS_DEFAULTS, action) {
  switch (action.type) {
    case 'UPDATE_EDITOR_SETTINGS':
      return { ...state,
        ...action.settings
      };
  }

  return state;
}
/* harmony default export */ var reducer = (Object(external_wp_data_["combineReducers"])({
  postId: reducer_postId,
  postType: reducer_postType,
  preferences,
  saving,
  postLock,
  template: reducer_template,
  postSavingLock,
  isReady: reducer_isReady,
  editorSettings: reducer_editorSettings,
  postAutosavingLock
}));

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// EXTERNAL MODULE: external ["wp","date"]
var external_wp_date_ = __webpack_require__("FqII");

// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__("Mmq9");

// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__("NMb1");
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/layout.js
var layout = __webpack_require__("Civd");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/constants.js
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
  return Object(external_wp_url_["addQueryArgs"])(page, query);
}
/**
 * Performs some basic cleanup of a string for use as a post slug
 *
 * This replicates some of what sanitize_title() does in WordPress core, but
 * is only designed to approximate what the slug will be.
 *
 * Converts Latin-1 Supplement and Latin Extended-A letters to basic Latin
 * letters. Removes combining diacritical marks. Converts whitespace, periods,
 * and forward slashes to hyphens. Removes any remaining non-word characters
 * except hyphens. Converts remaining string to lowercase. It does not account
 * for octets, HTML entities, or other encoded characters.
 *
 * @param {string} string Title or slug to be processed
 *
 * @return {string} Processed string
 */

function cleanForSlug(string) {
  if (!string) {
    return '';
  }

  return Object(external_lodash_["trim"])(Object(external_lodash_["deburr"])(string).replace(/[\s\./]+/g, '-').replace(/[^\w-]+/g, '').toLowerCase(), '-');
}

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/index.js + 71 modules
var build_module = __webpack_require__("b9KX");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/utils/get-template-part-icon.js
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
  return build_module[iconName] || layout["a" /* default */];
}

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

const EMPTY_OBJECT = {};
/**
 * Shared reference to an empty array for cases where it is important to avoid
 * returning a new array reference on every invocation, as in a connected or
 * other pure component which performs `shouldComponentUpdate` check on props.
 * This should be used as a last resort, since the normalized data should be
 * maintained by the reducer result in state.
 */

const EMPTY_ARRAY = [];
/**
 * Returns true if any past editor history snapshots exist, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether undo history exists.
 */

const hasEditorUndo = Object(external_wp_data_["createRegistrySelector"])(select => () => {
  return select('core').hasUndo();
});
/**
 * Returns true if any future editor history snapshots exist, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether redo history exists.
 */

const hasEditorRedo = Object(external_wp_data_["createRegistrySelector"])(select => () => {
  return select('core').hasRedo();
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
  const edits = selectors_getPostEdits(state);
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

const selectors_isEditedPostDirty = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  // Edits should contain only fields which differ from the saved post (reset
  // at initial load and save complete). Thus, a non-empty edits state can be
  // inferred to contain unsaved values.
  const postType = selectors_getCurrentPostType(state);
  const postId = selectors_getCurrentPostId(state);

  if (select('core').hasEditsForEntityRecord('postType', postType, postId)) {
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

const selectors_hasNonPostEntityChanges = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  const dirtyEntityRecords = select('core').__experimentalGetDirtyEntityRecords();

  const {
    type,
    id
  } = selectors_getCurrentPost(state);
  return Object(external_lodash_["some"])(dirtyEntityRecords, entityRecord => entityRecord.kind !== 'postType' || entityRecord.name !== type || entityRecord.key !== id);
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

const selectors_getCurrentPost = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  const postId = selectors_getCurrentPostId(state);
  const postType = selectors_getCurrentPostType(state);
  const post = select('core').getRawEntityRecord('postType', postType, postId);

  if (post) {
    return post;
  } // This exists for compatibility with the previous selector behavior
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

const selectors_getPostEdits = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  const postType = selectors_getCurrentPostType(state);
  const postId = selectors_getCurrentPostId(state);
  return select('core').getEntityRecordEdits('postType', postType, postId) || EMPTY_OBJECT;
});
/**
 * Returns a new reference when edited values have changed. This is useful in
 * inferring where an edit has been made between states by comparison of the
 * return values using strict equality.
 *
 * @deprecated since Gutenberg 6.5.0.
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

const getReferenceByDistinctEdits = Object(external_wp_data_["createRegistrySelector"])(select => () =>
/* state */
{
  external_wp_deprecated_default()("`wp.data.select( 'core/editor' ).getReferenceByDistinctEdits`", {
    since: '5.4',
    alternative: "`wp.data.select( 'core' ).getReferenceByDistinctEdits`"
  });
  return select('core').getReferenceByDistinctEdits();
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
      const post = selectors_getCurrentPost(state);

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

const getNestedEditedPostProperty = (state, attributeName) => {
  const edits = selectors_getPostEdits(state);

  if (!edits.hasOwnProperty(attributeName)) {
    return selectors_getCurrentPostAttribute(state, attributeName);
  }

  return { ...selectors_getCurrentPostAttribute(state, attributeName),
    ...edits[attributeName]
  };
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


  const edits = selectors_getPostEdits(state);

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
 * @deprecated since 5.6. Callers should use the `getAutosave( postType, postId, userId )` selector
 * 			   from the '@wordpress/core-data' package and access properties on the returned
 * 			   autosave object using getPostRawValue.
 *
 * @param {Object} state         Global application state.
 * @param {string} attributeName Autosave attribute name.
 *
 * @return {*} Autosave attribute value.
 */

const getAutosaveAttribute = Object(external_wp_data_["createRegistrySelector"])(select => (state, attributeName) => {
  if (!Object(external_lodash_["includes"])(AUTOSAVE_PROPERTIES, attributeName) && attributeName !== 'preview_link') {
    return;
  }

  const postType = selectors_getCurrentPostType(state);
  const postId = selectors_getCurrentPostId(state);
  const currentUserId = Object(external_lodash_["get"])(select('core').getCurrentUser(), ['id']);
  const autosave = select('core').getAutosave(postType, postId, currentUserId);

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

function selectors_getEditedPostVisibility(state) {
  const status = selectors_getEditedPostAttribute(state, 'status');

  if (status === 'private') {
    return 'private';
  }

  const password = selectors_getEditedPostAttribute(state, 'password');

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
  const post = currentPost || selectors_getCurrentPost(state);
  return ['publish', 'private'].indexOf(post.status) !== -1 || post.status === 'future' && !Object(external_wp_date_["isInTheFuture"])(new Date(Number(Object(external_wp_date_["getDate"])(post.date)) - ONE_MINUTE_IN_MS));
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
  const post = selectors_getCurrentPost(state); // TODO: Post being publishable should be superset of condition of post
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


  return !!selectors_getEditedPostAttribute(state, 'title') || !!selectors_getEditedPostAttribute(state, 'excerpt') || !isEditedPostEmpty(state) || external_wp_element_["Platform"].OS === 'native';
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
  const blocks = getEditorBlocks(state);

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


    const blockName = blocks[0].name;

    if (blockName !== Object(external_wp_blocks_["getDefaultBlockName"])() && blockName !== Object(external_wp_blocks_["getFreeformContentHandlerName"])()) {
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

const selectors_isEditedPostAutosaveable = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  // A post must contain a title, an excerpt, or non-empty content to be valid for autosaving.
  if (!selectors_isEditedPostSaveable(state)) {
    return false;
  } // A post is not autosavable when there is a post autosave lock.


  if (isPostAutosavingLocked(state)) {
    return false;
  }

  const postType = selectors_getCurrentPostType(state);
  const postId = selectors_getCurrentPostId(state);
  const hasFetchedAutosave = select('core').hasFetchedAutosaves(postType, postId);
  const currentUserId = Object(external_lodash_["get"])(select('core').getCurrentUser(), ['id']); // Disable reason - this line causes the side-effect of fetching the autosave
  // via a resolver, moving below the return would result in the autosave never
  // being fetched.
  // eslint-disable-next-line @wordpress/no-unused-vars-before-return

  const autosave = select('core').getAutosave(postType, postId, currentUserId); // If any existing autosaves have not yet been fetched, this function is
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


  return ['title', 'excerpt'].some(field => getPostRawValue(autosave[field]) !== selectors_getEditedPostAttribute(state, field));
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

const getAutosave = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  external_wp_deprecated_default()("`wp.data.select( 'core/editor' ).getAutosave()`", {
    since: '5.3',
    alternative: "`wp.data.select( 'core' ).getAutosave( postType, postId, userId )`"
  });
  const postType = selectors_getCurrentPostType(state);
  const postId = selectors_getCurrentPostId(state);
  const currentUserId = Object(external_lodash_["get"])(select('core').getCurrentUser(), ['id']);
  const autosave = select('core').getAutosave(postType, postId, currentUserId);
  return Object(external_lodash_["mapValues"])(Object(external_lodash_["pick"])(autosave, AUTOSAVE_PROPERTIES), getPostRawValue);
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

const hasAutosave = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  external_wp_deprecated_default()("`wp.data.select( 'core/editor' ).hasAutosave()`", {
    since: '5.3',
    alternative: "`!! wp.data.select( 'core' ).getAutosave( postType, postId, userId )`"
  });
  const postType = selectors_getCurrentPostType(state);
  const postId = selectors_getCurrentPostId(state);
  const currentUserId = Object(external_lodash_["get"])(select('core').getCurrentUser(), ['id']);
  return !!select('core').getAutosave(postType, postId, currentUserId);
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
  const date = selectors_getEditedPostAttribute(state, 'date'); // Offset the date by one minute (network latency)

  const checkedDate = new Date(Number(Object(external_wp_date_["getDate"])(date)) - ONE_MINUTE_IN_MS);
  return Object(external_wp_date_["isInTheFuture"])(checkedDate);
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
  const date = selectors_getEditedPostAttribute(state, 'date');
  const modified = selectors_getEditedPostAttribute(state, 'modified'); // This should be the status of the persisted post
  // It shouldn't use the "edited" status otherwise it breaks the
  // infered post data floating status
  // See https://github.com/WordPress/gutenberg/issues/28083

  const status = selectors_getCurrentPost(state).status;

  if (status === 'draft' || status === 'auto-draft' || status === 'pending') {
    return date === modified || date === null;
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

const selectors_isSavingPost = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  const postType = selectors_getCurrentPostType(state);
  const postId = selectors_getCurrentPostId(state);
  return select('core').isSavingEntityRecord('postType', postType, postId);
});
/**
 * Returns true if a previous post save was attempted successfully, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post was saved successfully.
 */

const didPostSaveRequestSucceed = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  const postType = selectors_getCurrentPostType(state);
  const postId = selectors_getCurrentPostId(state);
  return !select('core').getLastEntitySaveError('postType', postType, postId);
});
/**
 * Returns true if a previous post save was attempted but failed, or false
 * otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the post save failed.
 */

const didPostSaveRequestFail = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  const postType = selectors_getCurrentPostType(state);
  const postId = selectors_getCurrentPostId(state);
  return !!select('core').getLastEntitySaveError('postType', postType, postId);
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

  let previewLink = getAutosaveAttribute(state, 'preview_link');

  if (!previewLink) {
    previewLink = selectors_getEditedPostAttribute(state, 'link');

    if (previewLink) {
      previewLink = Object(external_wp_url_["addQueryArgs"])(previewLink, {
        preview: true
      });
    }
  }

  const featuredImageId = selectors_getEditedPostAttribute(state, 'featured_media');

  if (previewLink && featuredImageId) {
    return Object(external_wp_url_["addQueryArgs"])(previewLink, {
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
  const blocks = getEditorBlocks(state);
  if (blocks.length > 2) return null;
  let name; // If there is only one block in the content of the post grab its name
  // so we can derive a suitable post format from it.

  if (blocks.length === 1) {
    name = blocks[0].name; // check for core/embed `video` and `audio` eligible suggestions

    if (name === 'core/embed') {
      var _blocks$0$attributes;

      const provider = (_blocks$0$attributes = blocks[0].attributes) === null || _blocks$0$attributes === void 0 ? void 0 : _blocks$0$attributes.providerNameSlug;

      if (['youtube', 'vimeo'].includes(provider)) {
        name = 'core/video';
      } else if (['spotify', 'soundcloud'].includes(provider)) {
        name = 'core/audio';
      }
    }
  } // If there are two blocks in the content and the last one is a text blocks
  // grab the name of the first one to also suggest a post format from it.


  if (blocks.length === 2 && blocks[1].name === 'core/paragraph') {
    name = blocks[0].name;
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
      return 'video';

    case 'core/audio':
      return 'audio';

    default:
      return null;
  }
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
  external_wp_deprecated_default()('`core/editor` getBlocksForSerialization selector', {
    since: '5.3',
    alternative: 'getEditorBlocks',
    hint: 'Blocks serialization pre-processing occurs at save time'
  });
  const blocks = state.editor.present.blocks.value; // WARNING: Any changes to the logic of this function should be verified
  // against the implementation of isEditedPostEmpty, which bypasses this
  // function for performance' sake, in an assumption of this current logic
  // being irrelevant to the optimized condition of emptiness.
  // A single unmodified default block is assumed to be equivalent to an
  // empty post.

  const isSingleUnmodifiedDefaultBlock = blocks.length === 1 && Object(external_wp_blocks_["isUnmodifiedDefaultBlock"])(blocks[0]);

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

const getEditedPostContent = Object(external_wp_data_["createRegistrySelector"])(select => state => {
  const postId = selectors_getCurrentPostId(state);
  const postType = selectors_getCurrentPostType(state);
  const record = select('core').getEditedEntityRecord('postType', postType, postId);

  if (record) {
    if (typeof record.content === 'function') {
      return record.content(record);
    } else if (record.blocks) {
      return Object(external_wp_blocks_["__unstableSerializeAndClean"])(record.blocks);
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

function selectors_isPublishingPost(state) {
  return selectors_isSavingPost(state) && !selectors_isCurrentPostPublished(state) && selectors_getEditedPostAttribute(state, 'status') === 'publish';
}
/**
 * Returns whether the permalink is editable or not.
 *
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether or not the permalink is editable.
 */

function isPermalinkEditable(state) {
  const permalinkTemplate = selectors_getEditedPostAttribute(state, 'permalink_template');
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
  return selectors_getEditedPostAttribute(state, 'slug') || cleanForSlug(selectors_getEditedPostAttribute(state, 'title')) || selectors_getCurrentPostId(state);
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
  const permalinkTemplate = selectors_getEditedPostAttribute(state, 'permalink_template');

  if (!permalinkTemplate) {
    return null;
  }

  const postName = selectors_getEditedPostAttribute(state, 'slug') || selectors_getEditedPostAttribute(state, 'generated_slug');
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

function getEditorBlocks(state) {
  return selectors_getEditedPostAttribute(state, 'blocks') || EMPTY_ARRAY;
}
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
  var _getEditedPostAttribu;

  external_wp_deprecated_default()("select('core/editor').getEditorSelectionStart", {
    since: '10.0',
    plugin: 'Gutenberg',
    alternative: "select('core/editor').getEditorSelection"
  });
  return (_getEditedPostAttribu = selectors_getEditedPostAttribute(state, 'selection')) === null || _getEditedPostAttribu === void 0 ? void 0 : _getEditedPostAttribu.selectionStart;
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
  var _getEditedPostAttribu2;

  external_wp_deprecated_default()("select('core/editor').getEditorSelectionStart", {
    since: '10.0',
    plugin: 'Gutenberg',
    alternative: "select('core/editor').getEditorSelection"
  });
  return (_getEditedPostAttribu2 = selectors_getEditedPostAttribute(state, 'selection')) === null || _getEditedPostAttribu2 === void 0 ? void 0 : _getEditedPostAttribu2.selectionEnd;
}
/**
 * Returns the current selection.
 *
 * @param {Object} state
 * @return {WPBlockSelection} The selection end.
 */

function selectors_getEditorSelection(state) {
  return selectors_getEditedPostAttribute(state, 'selection');
}
/**
 * Is the editor ready
 *
 * @param {Object} state
 * @return {boolean} is Ready.
 */

function selectors_unstableIsEditorReady(state) {
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
  return Object(external_wp_data_["createRegistrySelector"])(select => (state, ...args) => {
    external_wp_deprecated_default()("`wp.data.select( 'core/editor' )." + name + '`', {
      since: '5.3',
      alternative: "`wp.data.select( 'core/block-editor' )." + name + '`'
    });
    return select('core/block-editor')[name](...args);
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

const selectors_getBlocks = getBlockEditorSelector('getBlocks');
/**
 * @see __unstableGetBlockWithoutInnerBlocks in core/block-editor store.
 */

const __unstableGetBlockWithoutInnerBlocks = getBlockEditorSelector('__unstableGetBlockWithoutInnerBlocks');
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

const selectors_getGlobalBlockCount = getBlockEditorSelector('getGlobalBlockCount');
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
  var _getEditorSettings;

  return (_getEditorSettings = selectors_getEditorSettings(state)) === null || _getEditorSettings === void 0 ? void 0 : _getEditorSettings.defaultTemplateTypes;
}
/**
 * Returns the default template part areas.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} The template part areas.
 */

const __experimentalGetDefaultTemplatePartAreas = Object(rememo["a" /* default */])(state => {
  var _getEditorSettings2;

  const areas = ((_getEditorSettings2 = selectors_getEditorSettings(state)) === null || _getEditorSettings2 === void 0 ? void 0 : _getEditorSettings2.defaultTemplatePartAreas) || [];
  return areas === null || areas === void 0 ? void 0 : areas.map(item => {
    return { ...item,
      icon: getTemplatePartIcon(item.icon)
    };
  });
}, state => {
  var _getEditorSettings3;

  return [(_getEditorSettings3 = selectors_getEditorSettings(state)) === null || _getEditorSettings3 === void 0 ? void 0 : _getEditorSettings3.defaultTemplatePartAreas];
});
/**
 * Returns a default template type searched by slug.
 *
 * @param {Object} state Global application state.
 * @param {string} slug The template type slug.
 *
 * @return {Object} The template type.
 */

const __experimentalGetDefaultTemplateType = Object(rememo["a" /* default */])((state, slug) => Object(external_lodash_["find"])(__experimentalGetDefaultTemplateTypes(state), {
  slug
}) || {}, (state, slug) => [__experimentalGetDefaultTemplateTypes(state), slug]);
/**
 * Given a template entity, return information about it which is ready to be
 * rendered, such as the title, description, and icon.
 *
 * @param {Object} state Global application state.
 * @param {Object} template The template for which we need information.
 * @return {Object} Information about the template, including title, description, and icon.
 */

function __experimentalGetTemplateInfo(state, template) {
  var _experimentalGetDefa;

  if (!template) {
    return {};
  }

  const {
    excerpt,
    slug,
    title,
    area
  } = template;

  const {
    title: defaultTitle,
    description: defaultDescription
  } = __experimentalGetDefaultTemplateType(state, slug);

  const templateTitle = Object(external_lodash_["isString"])(title) ? title : title === null || title === void 0 ? void 0 : title.rendered;
  const templateDescription = Object(external_lodash_["isString"])(excerpt) ? excerpt : excerpt === null || excerpt === void 0 ? void 0 : excerpt.raw;
  const templateIcon = ((_experimentalGetDefa = __experimentalGetDefaultTemplatePartAreas(state).find(item => area === item.area)) === null || _experimentalGetDefa === void 0 ? void 0 : _experimentalGetDefa.icon) || layout["a" /* default */];
  return {
    title: templateTitle && templateTitle !== slug ? templateTitle : defaultTitle || slug,
    description: templateDescription || defaultDescription,
    icon: templateIcon
  };
}

// EXTERNAL MODULE: external ["wp","notices"]
var external_wp_notices_ = __webpack_require__("onLe");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

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
  const {
    previousPost,
    post,
    postType
  } = data; // Autosaves are neither shown a notice nor redirected.

  if (Object(external_lodash_["get"])(data.options, ['isAutosave'])) {
    return [];
  }

  const publishStatus = ['publish', 'private', 'future'];
  const isPublished = Object(external_lodash_["includes"])(publishStatus, previousPost.status);
  const willPublish = Object(external_lodash_["includes"])(publishStatus, post.status);
  let noticeMessage;
  let shouldShowLink = Object(external_lodash_["get"])(postType, ['viewable'], false);

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
    const actions = [];

    if (shouldShowLink) {
      actions.push({
        label: postType.labels.view_item,
        url: post.link
      });
    }

    return [noticeMessage, {
      id: SAVE_POST_NOTICE_ID,
      type: 'snackbar',
      actions
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
  const isPublished = publishStatus.indexOf(post.status) !== -1; // If the post was being published, we show the corresponding publish error message
  // Unless we publish an "updating failed" message

  const messages = {
    publish: Object(external_wp_i18n_["__"])('Publishing failed.'),
    private: Object(external_wp_i18n_["__"])('Publishing failed.'),
    future: Object(external_wp_i18n_["__"])('Scheduling failed.')
  };
  let noticeMessage = !isPublished && publishStatus.indexOf(edits.status) !== -1 ? messages[edits.status] : Object(external_wp_i18n_["__"])('Updating failed.'); // Check if message string contains HTML. Notice text is currently only
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
  return [data.error.message && data.error.code !== 'unknown_error' ? data.error.message : Object(external_wp_i18n_["__"])('Trashing failed'), {
    id: TRASH_POST_NOTICE_ID
  }];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/actions.js
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
 * Returns an action generator used in signalling that editor has initialized with
 * the specified post object and editor settings.
 *
 * @param {Object} post      Post object.
 * @param {Object} edits     Initial edited attributes object.
 * @param {Array?} template  Block Template.
 */

function* actions_setupEditor(post, edits, template) {
  // In order to ensure maximum of a single parse during setup, edits are
  // included as part of editor setup action. Assume edited content as
  // canonical if provided, falling back to post.
  let content;

  if (Object(external_lodash_["has"])(edits, ['content'])) {
    content = edits.content;
  } else {
    content = post.content.raw;
  }

  let blocks = Object(external_wp_blocks_["parse"])(content); // Apply a template for new posts only, if exists.

  const isNewPost = post.status === 'auto-draft';

  if (isNewPost && template) {
    blocks = Object(external_wp_blocks_["synchronizeBlocksWithTemplate"])(blocks, template);
  }

  yield resetPost(post);
  yield {
    type: 'SETUP_EDITOR',
    post,
    edits,
    template
  };
  yield actions_resetEditorBlocks(blocks, {
    __unstableShouldCreateUndoLevel: false
  });
  yield setupEditorState(post);

  if (edits && Object.keys(edits).some(key => edits[key] !== (Object(external_lodash_["has"])(post, [key, 'raw']) ? post[key].raw : post[key]))) {
    yield actions_editPost(edits);
  }
}
/**
 * Returns an action object signalling that the editor is being destroyed and
 * that any necessary state or side-effect cleanup should occur.
 *
 * @return {Object} Action object.
 */

function actions_experimentalTearDownEditor() {
  return {
    type: 'TEAR_DOWN_EDITOR'
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
    post
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

function* resetAutosave(newAutosave) {
  external_wp_deprecated_default()('resetAutosave action (`core/editor` store)', {
    since: '5.3',
    alternative: 'receiveAutosaves action (`core` store)'
  });
  const postId = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPostId');
  yield external_wp_data_["controls"].dispatch('core', 'receiveAutosaves', postId, newAutosave);
  return {
    type: '__INERT__'
  };
}
/**
 * Action for dispatching that a post update request has started.
 *
 * @param {Object} options
 *
 * @return {Object} An action object
 */

function __experimentalRequestPostUpdateStart(options = {}) {
  return {
    type: 'REQUEST_POST_UPDATE_START',
    options
  };
}
/**
 * Action for dispatching that a post update request has finished.
 *
 * @param {Object} options
 *
 * @return {Object} An action object
 */

function __experimentalRequestPostUpdateFinish(options = {}) {
  return {
    type: 'REQUEST_POST_UPDATE_FINISH',
    options
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
    alternative: 'User the core entitires store instead'
  });
  return {
    type: 'DO_NOTHING'
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
    post
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

function* actions_editPost(edits, options) {
  const {
    id,
    type
  } = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPost');
  yield external_wp_data_["controls"].dispatch('core', 'editEntityRecord', 'postType', type, id, edits, options);
}
/**
 * Action generator for saving the current post in the editor.
 *
 * @param {Object} options
 */

function* actions_savePost(options = {}) {
  if (!(yield external_wp_data_["controls"].select(STORE_NAME, 'isEditedPostSaveable'))) {
    return;
  }

  let edits = {
    content: yield external_wp_data_["controls"].select(STORE_NAME, 'getEditedPostContent')
  };

  if (!options.isAutosave) {
    yield external_wp_data_["controls"].dispatch(STORE_NAME, 'editPost', edits, {
      undoIgnore: true
    });
  }

  yield __experimentalRequestPostUpdateStart(options);
  const previousRecord = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPost');
  edits = {
    id: previousRecord.id,
    ...(yield external_wp_data_["controls"].select('core', 'getEntityRecordNonTransientEdits', 'postType', previousRecord.type, previousRecord.id)),
    ...edits
  };
  yield external_wp_data_["controls"].dispatch('core', 'saveEntityRecord', 'postType', previousRecord.type, edits, options);
  yield __experimentalRequestPostUpdateFinish(options);
  const error = yield external_wp_data_["controls"].select('core', 'getLastEntitySaveError', 'postType', previousRecord.type, previousRecord.id);

  if (error) {
    const args = getNotificationArgumentsForSaveFail({
      post: previousRecord,
      edits,
      error
    });

    if (args.length) {
      yield external_wp_data_["controls"].dispatch(external_wp_notices_["store"], 'createErrorNotice', ...args);
    }
  } else {
    const updatedRecord = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPost');
    const args = getNotificationArgumentsForSaveSuccess({
      previousPost: previousRecord,
      post: updatedRecord,
      postType: yield external_wp_data_["controls"].resolveSelect('core', 'getPostType', updatedRecord.type),
      options
    });

    if (args.length) {
      yield external_wp_data_["controls"].dispatch(external_wp_notices_["store"], 'createSuccessNotice', ...args);
    } // Make sure that any edits after saving create an undo level and are
    // considered for change detection.


    if (!options.isAutosave) {
      yield external_wp_data_["controls"].dispatch('core/block-editor', '__unstableMarkLastChangeAsPersistent');
    }
  }
}
/**
 * Action generator for handling refreshing the current post.
 */

function* refreshPost() {
  const post = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPost');
  const postTypeSlug = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPostType');
  const postType = yield external_wp_data_["controls"].resolveSelect('core', 'getPostType', postTypeSlug);
  const newPost = yield Object(external_wp_dataControls_["apiFetch"])({
    // Timestamp arg allows caller to bypass browser caching, which is
    // expected for this specific function.
    path: `/wp/v2/${postType.rest_base}/${post.id}` + `?context=edit&_timestamp=${Date.now()}`
  });
  yield external_wp_data_["controls"].dispatch(STORE_NAME, 'resetPost', newPost);
}
/**
 * Action generator for trashing the current post in the editor.
 */

function* trashPost() {
  const postTypeSlug = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPostType');
  const postType = yield external_wp_data_["controls"].resolveSelect('core', 'getPostType', postTypeSlug);
  yield external_wp_data_["controls"].dispatch(external_wp_notices_["store"], 'removeNotice', TRASH_POST_NOTICE_ID);

  try {
    const post = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPost');
    yield Object(external_wp_dataControls_["apiFetch"])({
      path: `/wp/v2/${postType.rest_base}/${post.id}`,
      method: 'DELETE'
    });
    yield external_wp_data_["controls"].dispatch(STORE_NAME, 'savePost');
  } catch (error) {
    yield external_wp_data_["controls"].dispatch(external_wp_notices_["store"], 'createErrorNotice', ...getNotificationArgumentsForTrashFail({
      error
    }));
  }
}
/**
 * Action generator used in signalling that the post should autosave.  This
 * includes server-side autosaving (default) and client-side (a.k.a. local)
 * autosaving (e.g. on the Web, the post might be committed to Session
 * Storage).
 *
 * @param {Object?} options Extra flags to identify the autosave.
 */

function* actions_autosave({
  local = false,
  ...options
} = {}) {
  if (local) {
    const post = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPost');
    const isPostNew = yield external_wp_data_["controls"].select(STORE_NAME, 'isEditedPostNew');
    const title = yield external_wp_data_["controls"].select(STORE_NAME, 'getEditedPostAttribute', 'title');
    const content = yield external_wp_data_["controls"].select(STORE_NAME, 'getEditedPostAttribute', 'content');
    const excerpt = yield external_wp_data_["controls"].select(STORE_NAME, 'getEditedPostAttribute', 'excerpt');
    yield {
      type: 'LOCAL_AUTOSAVE_SET',
      postId: post.id,
      isPostNew,
      title,
      content,
      excerpt
    };
  } else {
    yield external_wp_data_["controls"].dispatch(STORE_NAME, 'savePost', {
      isAutosave: true,
      ...options
    });
  }
}
/**
 * Returns an action object used in signalling that undo history should
 * restore last popped state.
 *
 * @yield {Object} Action object.
 */

function* actions_redo() {
  yield external_wp_data_["controls"].dispatch('core', 'redo');
}
/**
 * Returns an action object used in signalling that undo history should pop.
 *
 * @yield {Object} Action object.
 */

function* actions_undo() {
  yield external_wp_data_["controls"].dispatch('core', 'undo');
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

function actions_updatePostLock(lock) {
  return {
    type: 'UPDATE_POST_LOCK',
    lock
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
    lockName
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
    lockName
  };
}
/**
 * Returns an action object used to signal that post autosaving is locked.
 *
 * @param  {string} lockName The lock name.
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
 * Returns an action object used to signal that post autosaving is unlocked.
 *
 * @param  {string} lockName The lock name.
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
 *
 * @yield {Object} Action object
 */

function* actions_resetEditorBlocks(blocks, options = {}) {
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
    } = yield external_wp_data_["controls"].select(STORE_NAME, 'getCurrentPost');
    const noChange = (yield external_wp_data_["controls"].select('core', 'getEditedEntityRecord', 'postType', type, id)).blocks === edits.blocks;

    if (noChange) {
      return yield external_wp_data_["controls"].dispatch('core', '__unstableCreateUndoLevel', 'postType', type, id);
    } // We create a new function here on every persistent edit
    // to make sure the edit makes the post dirty and creates
    // a new undo level.


    edits.content = ({
      blocks: blocksForSerialization = []
    }) => Object(external_wp_blocks_["__unstableSerializeAndClean"])(blocksForSerialization);
  }

  yield* actions_editPost(edits);
}
/*
 * Returns an action object used in signalling that the post editor settings have been updated.
 *
 * @param {Object} settings Updated settings
 *
 * @return {Object} Action object
 */

function actions_updateEditorSettings(settings) {
  return {
    type: 'UPDATE_EDITOR_SETTINGS',
    settings
  };
}
/**
 * Backward compatibility
 */

const getBlockEditorAction = name => function* (...args) {
  external_wp_deprecated_default()("`wp.data.dispatch( 'core/editor' )." + name + '`', {
    since: '5.3',
    alternative: "`wp.data.dispatch( 'core/block-editor' )." + name + '`'
  });
  yield external_wp_data_["controls"].dispatch('core/block-editor', name, ...args);
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

const actions_selectBlock = getBlockEditorAction('selectBlock');
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

const actions_clearSelectedBlock = getBlockEditorAction('clearSelectedBlock');
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

const actions_insertBlocks = getBlockEditorAction('insertBlocks');
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

const actions_setTemplateValidity = getBlockEditorAction('setTemplateValidity');
/**
 * @see synchronizeTemplate in core/block-editor store.
 */

const actions_synchronizeTemplate = getBlockEditorAction('synchronizeTemplate');
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

const actions_insertDefaultBlock = getBlockEditorAction('insertDefaultBlock');
/**
 * @see updateBlockListSettings in core/block-editor store.
 */

const updateBlockListSettings = getBlockEditorAction('updateBlockListSettings');

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/store/controls.js
/**
 * Function returning a sessionStorage key to set or retrieve a given post's
 * automatic session backup.
 *
 * Keys are crucially prefixed with 'wp-autosave-' so that wp-login.php's
 * `loggedout` handler can clear sessionStorage of any user-private content.
 *
 * @see https://github.com/WordPress/wordpress-develop/blob/6dad32d2aed47e6c0cf2aee8410645f6d7aba6bd/src/wp-login.php#L103
 *
 * @param {string}  postId     Post ID.
 * @param {boolean} isPostNew  Whether post new.
 * @return {string}            sessionStorage key
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
const controls = {
  LOCAL_AUTOSAVE_SET({
    postId,
    isPostNew,
    title,
    content,
    excerpt
  }) {
    localAutosaveSet(postId, isPostNew, title, content, excerpt);
  }

};
/* harmony default export */ var store_controls = (controls);

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
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#registerStore
 *
 * @type {Object}
 */

const storeConfig = {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject,
  controls: { ...external_wp_dataControls_["controls"],
    ...store_controls
  }
};
/**
 * Store definition for the editor namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, { ...storeConfig,
  persist: ['preferences']
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

Object(external_wp_data_["registerStore"])(STORE_NAME, { ...storeConfig,
  persist: ['preferences']
});

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autocompleters/index.js


// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/autosave-monitor/index.js
/**
 * WordPress dependencies
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

class autosave_monitor_AutosaveMonitor extends external_wp_element_["Component"] {
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
/* harmony default export */ var autosave_monitor = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])((select, ownProps) => {
  const {
    getReferenceByDistinctEdits
  } = select('core');
  const {
    isEditedPostDirty,
    isEditedPostAutosaveable,
    isAutosavingPost,
    getEditorSettings
  } = select('core/editor');
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
}), Object(external_wp_data_["withDispatch"])((dispatch, ownProps) => ({
  autosave() {
    const {
      autosave = dispatch('core/editor').autosave
    } = ownProps;
    autosave();
  }

}))])(autosave_monitor_AutosaveMonitor));

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-outline/item.js


/**
 * External dependencies
 */


const TableOfContentsItem = ({
  children,
  isValid,
  level,
  href,
  onSelect
}) => Object(external_wp_element_["createElement"])("li", {
  className: classnames_default()('document-outline__item', `is-${level.toLowerCase()}`, {
    'is-invalid': !isValid
  })
}, Object(external_wp_element_["createElement"])("a", {
  href: href,
  className: "document-outline__button",
  onClick: onSelect
}, Object(external_wp_element_["createElement"])("span", {
  className: "document-outline__emdash",
  "aria-hidden": "true"
}), Object(external_wp_element_["createElement"])("strong", {
  className: "document-outline__level"
}, level), Object(external_wp_element_["createElement"])("span", {
  className: "document-outline__item-content"
}, children)));

/* harmony default export */ var document_outline_item = (TableOfContentsItem);

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

const emptyHeadingContent = Object(external_wp_element_["createElement"])("em", null, Object(external_wp_i18n_["__"])('(Empty heading)'));
const incorrectLevelContent = [Object(external_wp_element_["createElement"])("br", {
  key: "incorrect-break"
}), Object(external_wp_element_["createElement"])("em", {
  key: "incorrect-message"
}, Object(external_wp_i18n_["__"])('(Incorrect heading level)'))];
const singleH1Headings = [Object(external_wp_element_["createElement"])("br", {
  key: "incorrect-break-h1"
}), Object(external_wp_element_["createElement"])("em", {
  key: "incorrect-message-h1"
}, Object(external_wp_i18n_["__"])('(Your theme may already use a H1 for the post title)'))];
const multipleH1Headings = [Object(external_wp_element_["createElement"])("br", {
  key: "incorrect-break-multiple-h1"
}), Object(external_wp_element_["createElement"])("em", {
  key: "incorrect-message-multiple-h1"
}, Object(external_wp_i18n_["__"])('(Multiple H1 headings are not recommended)'))];
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
  return Object(external_lodash_["flatMap"])(blocks, (block = {}) => {
    if (block.name === 'core/heading') {
      return { ...block,
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

  if (headings.length < 1) {
    return null;
  }

  let prevHeadingLevel = 1; // Not great but it's the simplest way to locate the title right now.

  const titleNode = document.querySelector('.editor-post-title__input');
  const hasTitle = isTitleSupported && title && titleNode;
  const countByLevel = Object(external_lodash_["countBy"])(headings, 'level');
  const hasMultipleH1 = countByLevel[1] > 1;
  return Object(external_wp_element_["createElement"])("div", {
    className: "document-outline"
  }, Object(external_wp_element_["createElement"])("ul", null, hasTitle && Object(external_wp_element_["createElement"])(document_outline_item, {
    level: Object(external_wp_i18n_["__"])('Title'),
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
    return Object(external_wp_element_["createElement"])(document_outline_item, {
      key: index,
      level: `H${item.level}`,
      isValid: isValid,
      isDisabled: hasOutlineItemsDisabled,
      href: `#block-${item.clientId}`,
      onSelect: onSelect
    }, item.isEmpty ? emptyHeadingContent : Object(external_wp_richText_["getTextContent"])(Object(external_wp_richText_["create"])({
      html: item.attributes.content
    })), isIncorrectLevel && incorrectLevelContent, item.level === 1 && hasMultipleH1 && multipleH1Headings, hasTitle && item.level === 1 && !hasMultipleH1 && singleH1Headings);
  })));
};
/* harmony default export */ var document_outline = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])(select => {
  const {
    getBlocks
  } = select(external_wp_blockEditor_["store"]);
  const {
    getEditedPostAttribute
  } = select('core/editor');
  const {
    getPostType
  } = select('core');
  const postType = getPostType(getEditedPostAttribute('type'));
  return {
    title: getEditedPostAttribute('title'),
    blocks: getBlocks(),
    isTitleSupported: Object(external_lodash_["get"])(postType, ['supports', 'title'], false)
  };
}))(DocumentOutline));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/document-outline/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function DocumentOutlineCheck({
  blocks,
  children
}) {
  const headings = Object(external_lodash_["filter"])(blocks, block => block.name === 'core/heading');

  if (headings.length < 1) {
    return null;
  }

  return children;
}

/* harmony default export */ var check = (Object(external_wp_data_["withSelect"])(select => ({
  blocks: select(external_wp_blockEditor_["store"]).getBlocks()
}))(DocumentOutlineCheck));

// EXTERNAL MODULE: external ["wp","keyboardShortcuts"]
var external_wp_keyboardShortcuts_ = __webpack_require__("hF7m");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/global-keyboard-shortcuts/save-shortcut.js
/**
 * WordPress dependencies
 */




function SaveShortcut({
  resetBlocksOnSave
}) {
  const {
    resetEditorBlocks,
    savePost
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  const {
    isEditedPostDirty,
    getPostEdits
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      isEditedPostDirty: _isEditedPostDirty,
      getPostEdits: _getPostEdits
    } = select('core/editor');
    return {
      isEditedPostDirty: _isEditedPostDirty,
      getPostEdits: _getPostEdits
    };
  }, []);
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/editor/save', event => {
    event.preventDefault(); // TODO: This should be handled in the `savePost` effect in
    // considering `isSaveable`. See note on `isEditedPostSaveable`
    // selector about dirtiness and meta-boxes.
    //
    // See: `isEditedPostSaveable`

    if (!isEditedPostDirty()) {
      return;
    } // The text editor requires that editor blocks are updated for a
    // save to work correctly. Usually this happens when the textarea
    // for the code editors blurs, but the shortcut can be used without
    // blurring the textarea.


    if (resetBlocksOnSave) {
      const postEdits = getPostEdits();

      if (postEdits.content && typeof postEdits.content === 'string') {
        const blocks = Object(external_wp_blocks_["parse"])(postEdits.content);
        resetEditorBlocks(blocks);
      }
    }

    savePost();
  }, {
    bindGlobal: true
  });
  return null;
}

/* harmony default export */ var save_shortcut = (SaveShortcut);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/global-keyboard-shortcuts/visual-editor-shortcuts.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function VisualEditorGlobalKeyboardShortcuts() {
  const {
    redo,
    undo
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/editor/undo', event => {
    undo();
    event.preventDefault();
  }, {
    bindGlobal: true
  });
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/editor/redo', event => {
    redo();
    event.preventDefault();
  }, {
    bindGlobal: true
  });
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockEditorKeyboardShortcuts"], null), Object(external_wp_element_["createElement"])(save_shortcut, null));
}

/* harmony default export */ var visual_editor_shortcuts = (VisualEditorGlobalKeyboardShortcuts);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/global-keyboard-shortcuts/text-editor-shortcuts.js


/**
 * Internal dependencies
 */

function TextEditorGlobalKeyboardShortcuts() {
  return Object(external_wp_element_["createElement"])(save_shortcut, {
    resetBlocksOnSave: true
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/global-keyboard-shortcuts/register-shortcuts.js


/**
 * WordPress dependencies
 */






function EditorKeyboardShortcutsRegister() {
  // Registering the shortcuts
  const {
    registerShortcut
  } = Object(external_wp_data_["useDispatch"])(external_wp_keyboardShortcuts_["store"]);
  Object(external_wp_element_["useEffect"])(() => {
    registerShortcut({
      name: 'core/editor/save',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Save your changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 's'
      }
    });
    registerShortcut({
      name: 'core/editor/undo',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Undo your last changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 'z'
      }
    });
    registerShortcut({
      name: 'core/editor/redo',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Redo your last undo.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: 'z'
      }
    });
  }, [registerShortcut]);
  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockEditorKeyboardShortcuts"].Register, null);
}

/* harmony default export */ var register_shortcuts = (EditorKeyboardShortcutsRegister);

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external ["wp","keycodes"]
var external_wp_keycodes_ = __webpack_require__("RxS6");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/redo.js
var library_redo = __webpack_require__("K2cm");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/undo.js
var library_undo = __webpack_require__("Ntru");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-history/redo.js



/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function EditorHistoryRedo(props, ref) {
  const hasRedo = Object(external_wp_data_["useSelect"])(select => select(store).hasEditorRedo(), []);
  const {
    redo
  } = Object(external_wp_data_["useDispatch"])(store);
  return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], Object(esm_extends["a" /* default */])({}, props, {
    ref: ref,
    icon: !Object(external_wp_i18n_["isRTL"])() ? library_redo["a" /* default */] : library_undo["a" /* default */]
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: Object(external_wp_i18n_["__"])('Redo'),
    shortcut: external_wp_keycodes_["displayShortcut"].primaryShift('z') // If there are no redo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasRedo,
    onClick: hasRedo ? redo : undefined,
    className: "editor-history__redo"
  }));
}

/* harmony default export */ var editor_history_redo = (Object(external_wp_element_["forwardRef"])(EditorHistoryRedo));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/editor-history/undo.js



/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function EditorHistoryUndo(props, ref) {
  const hasUndo = Object(external_wp_data_["useSelect"])(select => select(store).hasEditorUndo(), []);
  const {
    undo
  } = Object(external_wp_data_["useDispatch"])(store);
  return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], Object(esm_extends["a" /* default */])({}, props, {
    ref: ref,
    icon: !Object(external_wp_i18n_["isRTL"])() ? library_undo["a" /* default */] : library_redo["a" /* default */]
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: Object(external_wp_i18n_["__"])('Undo'),
    shortcut: external_wp_keycodes_["displayShortcut"].primary('z') // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasUndo,
    onClick: hasUndo ? undo : undefined,
    className: "editor-history__undo"
  }));
}

/* harmony default export */ var editor_history_undo = (Object(external_wp_element_["forwardRef"])(EditorHistoryUndo));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/template-validation-notice/index.js


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
    if ( // eslint-disable-next-line no-alert
    window.confirm(Object(external_wp_i18n_["__"])('Resetting the template may result in loss of content, do you want to continue?'))) {
      props.synchronizeTemplate();
    }
  };

  return Object(external_wp_element_["createElement"])(external_wp_components_["Notice"], {
    className: "editor-template-validation-notice",
    isDismissible: false,
    status: "warning",
    actions: [{
      label: Object(external_wp_i18n_["__"])('Keep it as is'),
      onClick: props.resetTemplateValidity
    }, {
      label: Object(external_wp_i18n_["__"])('Reset the template'),
      onClick: confirmSynchronization
    }]
  }, Object(external_wp_i18n_["__"])('The content of your post doesn’t match the template assigned to your post type.'));
}

/* harmony default export */ var template_validation_notice = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => ({
  isValid: select(external_wp_blockEditor_["store"]).isValidTemplate()
})), Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    setTemplateValidity,
    synchronizeTemplate
  } = dispatch(external_wp_blockEditor_["store"]);
  return {
    resetTemplateValidity: () => setTemplateValidity(true),
    synchronizeTemplate
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


function EditorNotices({
  notices,
  onRemove
}) {
  const dismissibleNotices = Object(external_lodash_["filter"])(notices, {
    isDismissible: true,
    type: 'default'
  });
  const nonDismissibleNotices = Object(external_lodash_["filter"])(notices, {
    isDismissible: false,
    type: 'default'
  });
  const snackbarNotices = Object(external_lodash_["filter"])(notices, {
    type: 'snackbar'
  });
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["NoticeList"], {
    notices: nonDismissibleNotices,
    className: "components-editor-notices__pinned"
  }), Object(external_wp_element_["createElement"])(external_wp_components_["NoticeList"], {
    notices: dismissibleNotices,
    className: "components-editor-notices__dismissible",
    onRemove: onRemove
  }, Object(external_wp_element_["createElement"])(template_validation_notice, null)), Object(external_wp_element_["createElement"])(external_wp_components_["SnackbarList"], {
    notices: snackbarNotices,
    className: "components-editor-notices__snackbar",
    onRemove: onRemove
  }));
}
/* harmony default export */ var editor_notices = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => ({
  notices: select(external_wp_notices_["store"]).getNotices()
})), Object(external_wp_data_["withDispatch"])(dispatch => ({
  onRemove: dispatch(external_wp_notices_["store"]).removeNotice
}))])(EditorNotices));

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js
var library_close = __webpack_require__("w95h");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/page.js
var library_page = __webpack_require__("Zm8Q");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/entities-saved-states/entity-record-item.js


/**
 * WordPress dependencies
 */





function EntityRecordItem({
  record,
  checked,
  onChange,
  closePanel
}) {
  const {
    name,
    kind,
    title,
    key
  } = record;
  const parentBlockId = Object(external_wp_data_["useSelect"])(select => {
    var _blocks$;

    // Get entity's blocks.
    const {
      blocks = []
    } = select('core').getEditedEntityRecord(kind, name, key); // Get parents of the entity's first block.

    const parents = select(external_wp_blockEditor_["store"]).getBlockParents((_blocks$ = blocks[0]) === null || _blocks$ === void 0 ? void 0 : _blocks$.clientId); // Return closest parent block's clientId.

    return parents[parents.length - 1];
  }, []); // Handle templates that might use default descriptive titles

  const entityRecordTitle = Object(external_wp_data_["useSelect"])(select => {
    if ('postType' !== kind || 'wp_template' !== name) {
      return title;
    }

    const template = select('core').getEditedEntityRecord(kind, name, key);
    return select('core/editor').__experimentalGetTemplateInfo(template).title;
  }, [name, kind, title, key]);
  const isSelected = Object(external_wp_data_["useSelect"])(select => {
    const selectedBlockId = select(external_wp_blockEditor_["store"]).getSelectedBlockClientId();
    return selectedBlockId === parentBlockId;
  }, [parentBlockId]);
  const isSelectedText = isSelected ? Object(external_wp_i18n_["__"])('Selected') : Object(external_wp_i18n_["__"])('Select');
  const {
    selectBlock
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  const selectParentBlock = Object(external_wp_element_["useCallback"])(() => selectBlock(parentBlockId), [parentBlockId]);
  const selectAndDismiss = Object(external_wp_element_["useCallback"])(() => {
    selectBlock(parentBlockId);
    closePanel();
  }, [parentBlockId]);
  return Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_components_["CheckboxControl"], {
    label: Object(external_wp_element_["createElement"])("strong", null, entityRecordTitle || Object(external_wp_i18n_["__"])('Untitled')),
    checked: checked,
    onChange: onChange
  }), parentBlockId ? Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    onClick: selectParentBlock,
    className: "entities-saved-states__find-entity",
    disabled: isSelected
  }, isSelectedText), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    onClick: selectAndDismiss,
    className: "entities-saved-states__find-entity-small",
    disabled: isSelected
  }, isSelectedText)) : null);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/entities-saved-states/entity-type-list.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


const ENTITY_NAME_ICONS = {
  site: layout["a" /* default */],
  page: library_page["a" /* default */]
};
function EntityTypeList({
  list,
  unselectedEntities,
  setUnselectedEntities,
  closePanel
}) {
  const firstRecord = list[0];
  const entity = Object(external_wp_data_["useSelect"])(select => select(external_wp_coreData_["store"]).getEntity(firstRecord.kind, firstRecord.name), [firstRecord.kind, firstRecord.name]); // Set icon based on type of entity.

  const {
    name
  } = firstRecord;
  const icon = ENTITY_NAME_ICONS[name];
  return Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    title: entity.label,
    initialOpen: true,
    icon: icon
  }, list.map(record => {
    return Object(external_wp_element_["createElement"])(EntityRecordItem, {
      key: record.key || record.property,
      record: record,
      checked: !Object(external_lodash_["some"])(unselectedEntities, elt => elt.kind === record.kind && elt.name === record.name && elt.key === record.key && elt.property === record.property),
      onChange: value => setUnselectedEntities(record, value),
      closePanel: closePanel
    });
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/entities-saved-states/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


const TRANSLATED_SITE_PROTPERTIES = {
  title: Object(external_wp_i18n_["__"])('Title'),
  description: Object(external_wp_i18n_["__"])('Tagline'),
  site_logo: Object(external_wp_i18n_["__"])('Logo'),
  show_on_front: Object(external_wp_i18n_["__"])('Show on front'),
  page_on_front: Object(external_wp_i18n_["__"])('Page on front')
};
function EntitiesSavedStates({
  close
}) {
  const saveButtonRef = Object(external_wp_element_["useRef"])();
  const {
    dirtyEntityRecords
  } = Object(external_wp_data_["useSelect"])(select => {
    const dirtyRecords = select(external_wp_coreData_["store"]).__experimentalGetDirtyEntityRecords(); // Remove site object and decouple into its edited pieces.


    const dirtyRecordsWithoutSite = dirtyRecords.filter(record => !(record.kind === 'root' && record.name === 'site'));
    const siteEdits = select(external_wp_coreData_["store"]).getEntityRecordEdits('root', 'site');
    const siteEditsAsEntities = [];

    for (const property in siteEdits) {
      siteEditsAsEntities.push({
        kind: 'root',
        name: 'site',
        title: TRANSLATED_SITE_PROTPERTIES[property] || property,
        property
      });
    }

    const dirtyRecordsWithSiteItems = [...dirtyRecordsWithoutSite, ...siteEditsAsEntities];
    return {
      dirtyEntityRecords: dirtyRecordsWithSiteItems
    };
  }, []);
  const {
    saveEditedEntityRecord,
    __experimentalSaveSpecifiedEntityEdits: saveSpecifiedEntityEdits
  } = Object(external_wp_data_["useDispatch"])(external_wp_coreData_["store"]); // To group entities by type.

  const partitionedSavables = Object.values(Object(external_lodash_["groupBy"])(dirtyEntityRecords, 'name')); // Unchecked entities to be ignored by save function.

  const [unselectedEntities, _setUnselectedEntities] = Object(external_wp_element_["useState"])([]);

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

  const saveCheckedEntities = () => {
    const entitiesToSave = dirtyEntityRecords.filter(({
      kind,
      name,
      key,
      property
    }) => {
      return !Object(external_lodash_["some"])(unselectedEntities, elt => elt.kind === kind && elt.name === name && elt.key === key && elt.property === property);
    });
    close(entitiesToSave);
    const siteItemsToSave = [];
    entitiesToSave.forEach(({
      kind,
      name,
      key,
      property
    }) => {
      if ('root' === kind && 'site' === name) {
        siteItemsToSave.push(property);
      } else {
        saveEditedEntityRecord(kind, name, key);
      }
    });
    saveSpecifiedEntityEdits('root', 'site', undefined, siteItemsToSave);
  }; // Explicitly define this with no argument passed.  Using `close` on
  // its own will use the event object in place of the expected saved entities.


  const dismissPanel = Object(external_wp_element_["useCallback"])(() => close(), [close]);
  const [saveDialogRef, saveDialogProps] = Object(external_wp_compose_["__experimentalUseDialog"])({
    onClose: () => dismissPanel()
  });
  return Object(external_wp_element_["createElement"])("div", Object(esm_extends["a" /* default */])({
    ref: saveDialogRef
  }, saveDialogProps, {
    className: "entities-saved-states__panel"
  }), Object(external_wp_element_["createElement"])("div", {
    className: "entities-saved-states__panel-header"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    ref: saveButtonRef,
    isPrimary: true,
    disabled: dirtyEntityRecords.length - unselectedEntities.length === 0,
    onClick: saveCheckedEntities,
    className: "editor-entities-saved-states__save-button"
  }, Object(external_wp_i18n_["__"])('Save')), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    icon: library_close["a" /* default */],
    onClick: dismissPanel,
    label: Object(external_wp_i18n_["__"])('Close panel')
  })), Object(external_wp_element_["createElement"])("div", {
    className: "entities-saved-states__text-prompt"
  }, Object(external_wp_element_["createElement"])("strong", null, Object(external_wp_i18n_["__"])('Select the changes you want to save')), Object(external_wp_element_["createElement"])("p", null, Object(external_wp_i18n_["__"])('Some changes may affect other areas of your site.'))), partitionedSavables.map(list => {
    return Object(external_wp_element_["createElement"])(EntityTypeList, {
      key: list[0].name,
      list: list,
      closePanel: dismissPanel,
      unselectedEntities: unselectedEntities,
      setUnselectedEntities: setUnselectedEntities
    });
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/error-boundary/index.js


/**
 * WordPress dependencies
 */







function CopyButton({
  text,
  children
}) {
  const ref = Object(external_wp_compose_["useCopyToClipboard"])(text);
  return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    isSecondary: true,
    ref: ref
  }, children);
}

class error_boundary_ErrorBoundary extends external_wp_element_["Component"] {
  constructor() {
    super(...arguments);
    this.reboot = this.reboot.bind(this);
    this.getContent = this.getContent.bind(this);
    this.state = {
      error: null
    };
  }

  componentDidCatch(error) {
    this.setState({
      error
    });
  }

  reboot() {
    this.props.onError();
  }

  getContent() {
    try {
      // While `select` in a component is generally discouraged, it is
      // used here because it (a) reduces the chance of data loss in the
      // case of additional errors by performing a direct retrieval and
      // (b) avoids the performance cost associated with unnecessary
      // content serialization throughout the lifetime of a non-erroring
      // application.
      return Object(external_wp_data_["select"])('core/editor').getEditedPostContent();
    } catch (error) {}
  }

  render() {
    const {
      error
    } = this.state;

    if (!error) {
      return this.props.children;
    }

    return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["Warning"], {
      className: "editor-error-boundary",
      actions: [Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
        key: "recovery",
        onClick: this.reboot,
        isSecondary: true
      }, Object(external_wp_i18n_["__"])('Attempt Recovery')), Object(external_wp_element_["createElement"])(CopyButton, {
        key: "copy-post",
        text: this.getContent
      }, Object(external_wp_i18n_["__"])('Copy Post Text')), Object(external_wp_element_["createElement"])(CopyButton, {
        key: "copy-error",
        text: error.stack
      }, Object(external_wp_i18n_["__"])('Copy Error'))]
    }, Object(external_wp_i18n_["__"])('The editor has encountered an unexpected error.'));
  }

}

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



const requestIdleCallback = window.requestIdleCallback ? window.requestIdleCallback : window.requestAnimationFrame;
/**
 * Function which returns true if the current environment supports browser
 * sessionStorage, or false otherwise. The result of this function is cached and
 * reused in subsequent invocations.
 */

const hasSessionStorageSupport = Object(external_lodash_["once"])(() => {
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
  const {
    postId,
    isEditedPostNew,
    hasRemoteAutosave
  } = Object(external_wp_data_["useSelect"])(select => ({
    postId: select('core/editor').getCurrentPostId(),
    isEditedPostNew: select('core/editor').isEditedPostNew(),
    getEditedPostAttribute: select('core/editor').getEditedPostAttribute,
    hasRemoteAutosave: !!select('core/editor').getEditorSettings().autosave
  }), []);
  const {
    getEditedPostAttribute
  } = Object(external_wp_data_["useSelect"])('core/editor');
  const {
    createWarningNotice,
    removeNotice
  } = Object(external_wp_data_["useDispatch"])(external_wp_notices_["store"]);
  const {
    editPost,
    resetEditorBlocks
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  Object(external_wp_element_["useEffect"])(() => {
    let localAutosave = localAutosaveGet(postId, isEditedPostNew);

    if (!localAutosave) {
      return;
    }

    try {
      localAutosave = JSON.parse(localAutosave);
    } catch (error) {
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

    const noticeId = Object(external_lodash_["uniqueId"])('wpEditorAutosaveRestore');
    createWarningNotice(Object(external_wp_i18n_["__"])('The backup of this post in your browser is different from the version below.'), {
      id: noticeId,
      actions: [{
        label: Object(external_wp_i18n_["__"])('Restore the backup'),

        onClick() {
          editPost(Object(external_lodash_["omit"])(edits, ['content']));
          resetEditorBlocks(Object(external_wp_blocks_["parse"])(edits.content));
          removeNotice(noticeId);
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
  } = Object(external_wp_data_["useSelect"])(select => ({
    postId: select('core/editor').getCurrentPostId(),
    isEditedPostNew: select('core/editor').isEditedPostNew(),
    isDirty: select('core/editor').isEditedPostDirty(),
    isAutosaving: select('core/editor').isAutosavingPost(),
    didError: select('core/editor').didPostSaveRequestFail()
  }), []);
  const lastIsDirty = Object(external_wp_element_["useRef"])(isDirty);
  const lastIsAutosaving = Object(external_wp_element_["useRef"])(isAutosaving);
  Object(external_wp_element_["useEffect"])(() => {
    if (!didError && (lastIsAutosaving.current && !isAutosaving || lastIsDirty.current && !isDirty)) {
      localAutosaveClear(postId, isEditedPostNew);
    }

    lastIsDirty.current = isDirty;
    lastIsAutosaving.current = isAutosaving;
  }, [isDirty, isAutosaving, didError]); // Once the isEditedPostNew changes from true to false, let's clear the auto-draft autosave.

  const wasEditedPostNew = Object(external_wp_compose_["usePrevious"])(isEditedPostNew);
  const prevPostId = Object(external_wp_compose_["usePrevious"])(postId);
  Object(external_wp_element_["useEffect"])(() => {
    if (prevPostId === postId && wasEditedPostNew && !isEditedPostNew) {
      localAutosaveClear(postId, true);
    }
  }, [isEditedPostNew, postId]);
}

function LocalAutosaveMonitor() {
  const {
    autosave
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  const deferedAutosave = Object(external_wp_element_["useCallback"])(() => {
    requestIdleCallback(() => autosave({
      local: true
    }));
  }, []);
  useAutosaveNotice();
  useAutosavePurge();
  const {
    localAutosaveInterval
  } = Object(external_wp_data_["useSelect"])(select => ({
    localAutosaveInterval: select('core/editor').getEditorSettings().__experimentalLocalAutosaveInterval
  }), []);
  return Object(external_wp_element_["createElement"])(autosave_monitor, {
    interval: localAutosaveInterval,
    autosave: deferedAutosave
  });
}

/* harmony default export */ var local_autosave_monitor = (Object(external_wp_compose_["ifCondition"])(hasSessionStorageSupport)(LocalAutosaveMonitor));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/page-attributes/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function PageAttributesCheck({
  children
}) {
  const postType = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditedPostAttribute
    } = select(store);
    const {
      getPostType
    } = select(external_wp_coreData_["store"]);
    return getPostType(getEditedPostAttribute('type'));
  }, []);
  const supportsPageAttributes = Object(external_lodash_["get"])(postType, ['supports', 'page-attributes'], false); // Only render fields if post type supports page attributes or available templates exist.

  if (!supportsPageAttributes) {
    return null;
  }

  return children;
}
/* harmony default export */ var page_attributes_check = (PageAttributesCheck);

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
 * @param {Object}    props             Props.
 * @param {string}    [props.postType]  Current post type.
 * @param {WPElement} props.children    Children to be rendered if post
 *                                                                   type supports.
 * @param {(string|string[])}                      props.supportKeys String or string array of keys
 *                                                                   to test.
 *
 * @return {WPComponent} The component to be rendered.
 */

function PostTypeSupportCheck({
  postType,
  children,
  supportKeys
}) {
  let isSupported = true;

  if (postType) {
    isSupported = Object(external_lodash_["some"])(Object(external_lodash_["castArray"])(supportKeys), key => !!postType.supports[key]);
  }

  if (!isSupported) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_type_support_check = (Object(external_wp_data_["withSelect"])(select => {
  const {
    getEditedPostAttribute
  } = select('core/editor');
  const {
    getPostType
  } = select('core');
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


const PageAttributesOrder = Object(external_wp_compose_["withState"])({
  orderInput: null
})(({
  onUpdateOrder,
  order = 0,
  orderInput,
  setState
}) => {
  const setUpdatedOrder = value => {
    setState({
      orderInput: value
    });
    const newOrder = Number(value);

    if (Number.isInteger(newOrder) && Object(external_lodash_["invoke"])(value, ['trim']) !== '') {
      onUpdateOrder(Number(value));
    }
  };

  const value = orderInput === null ? order : orderInput;
  return Object(external_wp_element_["createElement"])(external_wp_components_["TextControl"], {
    className: "editor-page-attributes__order",
    type: "number",
    label: Object(external_wp_i18n_["__"])('Order'),
    value: value,
    onChange: setUpdatedOrder,
    size: 6,
    onBlur: () => {
      setState({
        orderInput: null
      });
    }
  });
});

function PageAttributesOrderWithChecks(props) {
  return Object(external_wp_element_["createElement"])(post_type_support_check, {
    supportKeys: "page-attributes"
  }, Object(external_wp_element_["createElement"])(PageAttributesOrder, props));
}

/* harmony default export */ var page_attributes_order = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  return {
    order: select('core/editor').getEditedPostAttribute('menu_order')
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  onUpdateOrder(order) {
    dispatch('core/editor').editPost({
      menu_order: order
    });
  }

}))])(PageAttributesOrderWithChecks));

// EXTERNAL MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_ = __webpack_require__("rmEH");

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
  const flatTermsWithParentAndChildren = flatTerms.map(term => {
    return {
      children: [],
      parent: null,
      ...term
    };
  });
  const termsByParent = Object(external_lodash_["groupBy"])(flatTermsWithParentAndChildren, 'parent');

  if (termsByParent.null && termsByParent.null.length) {
    return flatTermsWithParentAndChildren;
  }

  const fillWithChildren = terms => {
    return terms.map(term => {
      const children = termsByParent[term.id];
      return { ...term,
        children: children && children.length ? fillWithChildren(children) : []
      };
    });
  };

  return fillWithChildren(termsByParent['0'] || []);
} // Lodash unescape function handles &#39; but not &#039; which may be return in some API requests.

const unescapeString = arg => {
  return Object(external_lodash_["unescape"])(arg.replace('&#039;', "'"));
};
/**
 * Returns a term object with name unescaped.
 * The unescape of the name property is done using lodash unescape function.
 *
 * @param {Object} term The term object to unescape.
 *
 * @return {Object} Term object with name property unescaped.
 */

const unescapeTerm = term => {
  return { ...term,
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
  return Object(external_lodash_["map"])(terms, unescapeTerm);
};

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



function getTitle(post) {
  var _post$title;

  return post !== null && post !== void 0 && (_post$title = post.title) !== null && _post$title !== void 0 && _post$title.rendered ? Object(external_wp_htmlEntities_["decodeEntities"])(post.title.rendered) : `#${post.id} (${Object(external_wp_i18n_["__"])('no title')})`;
}

const getItemPriority = (name, searchValue) => {
  const normalizedName = Object(external_lodash_["deburr"])(name).toLowerCase();
  const normalizedSearch = Object(external_lodash_["deburr"])(searchValue).toLowerCase();

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
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  const [fieldValue, setFieldValue] = Object(external_wp_element_["useState"])(false);
  const {
    parentPost,
    parentPostId,
    items,
    postType
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getPostType,
      getEntityRecords,
      getEntityRecord
    } = select('core');
    const {
      getCurrentPostId,
      getEditedPostAttribute
    } = select('core/editor');
    const postTypeSlug = getEditedPostAttribute('type');
    const pageId = getEditedPostAttribute('parent');
    const pType = getPostType(postTypeSlug);
    const postId = getCurrentPostId();
    const isHierarchical = Object(external_lodash_["get"])(pType, ['hierarchical'], false);
    const query = {
      per_page: 100,
      exclude: postId,
      parent_exclude: postId,
      orderby: 'menu_order',
      order: 'asc',
      _fields: 'id,title,parent'
    }; // Perform a search when the field is changed.

    if (!!fieldValue) {
      query.search = fieldValue;
    }

    return {
      parentPostId: pageId,
      parentPost: pageId ? getEntityRecord('postType', postTypeSlug, pageId) : null,
      items: isHierarchical ? getEntityRecords('postType', postTypeSlug, query) : [],
      postType: pType
    };
  }, [fieldValue]);
  const isHierarchical = Object(external_lodash_["get"])(postType, ['hierarchical'], false);
  const parentPageLabel = Object(external_lodash_["get"])(postType, ['labels', 'parent_item_colon']);
  const pageItems = items || [];
  const parentOptions = Object(external_wp_element_["useMemo"])(() => {
    const getOptionsFromTree = (tree, level = 0) => {
      const mappedNodes = tree.map(treeNode => [{
        value: treeNode.id,
        label: Object(external_lodash_["repeat"])('— ', level) + Object(external_lodash_["unescape"])(treeNode.name),
        rawName: treeNode.name
      }, ...getOptionsFromTree(treeNode.children || [], level + 1)]);
      const sortedNodes = mappedNodes.sort(([a], [b]) => {
        const priorityA = getItemPriority(a.rawName, fieldValue);
        const priorityB = getItemPriority(b.rawName, fieldValue);
        return priorityA >= priorityB ? 1 : -1;
      });
      return Object(external_lodash_["flatten"])(sortedNodes);
    };

    let tree = pageItems.map(item => ({
      id: item.id,
      parent: item.parent,
      name: getTitle(item)
    })); // Only build a hierarchical tree when not searching.

    if (!fieldValue) {
      tree = buildTermsTree(tree);
    }

    const opts = getOptionsFromTree(tree); // Ensure the current parent is in the options list.

    const optsHasParent = Object(external_lodash_["find"])(opts, item => item.value === parentPostId);

    if (parentPost && !optsHasParent) {
      opts.unshift({
        value: parentPostId,
        label: getTitle(parentPost)
      });
    }

    return opts;
  }, [pageItems, fieldValue]);

  if (!isHierarchical || !parentPageLabel) {
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

  return Object(external_wp_element_["createElement"])(external_wp_components_["ComboboxControl"], {
    className: "editor-page-attributes__parent",
    label: parentPageLabel,
    value: parentPostId,
    options: parentOptions,
    onFilterValueChange: Object(external_lodash_["debounce"])(handleKeydown, 300),
    onChange: handleChange
  });
}
/* harmony default export */ var page_attributes_parent = (PageAttributesParent);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-template/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function PostTemplate({}) {
  const {
    availableTemplates,
    selectedTemplate,
    isViewable
  } = Object(external_wp_data_["useSelect"])(select => {
    var _getPostType$viewable, _getPostType;

    const {
      getEditedPostAttribute,
      getEditorSettings,
      getCurrentPostType
    } = select(store);
    const {
      getPostType
    } = select(external_wp_coreData_["store"]);
    return {
      selectedTemplate: getEditedPostAttribute('template'),
      availableTemplates: getEditorSettings().availableTemplates,
      isViewable: (_getPostType$viewable = (_getPostType = getPostType(getCurrentPostType())) === null || _getPostType === void 0 ? void 0 : _getPostType.viewable) !== null && _getPostType$viewable !== void 0 ? _getPostType$viewable : false
    };
  }, []);
  const {
    editPost
  } = Object(external_wp_data_["useDispatch"])(store);

  if (!isViewable || Object(external_lodash_["isEmpty"])(availableTemplates)) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["SelectControl"], {
    label: Object(external_wp_i18n_["__"])('Template:'),
    value: selectedTemplate,
    onChange: templateSlug => {
      editPost({
        template: templateSlug || ''
      });
    },
    options: Object(external_lodash_["map"])(availableTemplates, (templateName, templateSlug) => ({
      value: templateSlug,
      label: templateName
    }))
  });
}
/* harmony default export */ var post_template = (PostTemplate);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/combobox.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






function PostAuthorCombobox() {
  const [fieldValue, setFieldValue] = Object(external_wp_element_["useState"])();
  const {
    authorId,
    isLoading,
    authors,
    postAuthor
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      __unstableGetAuthor,
      getAuthors,
      isResolving
    } = select('core');
    const {
      getEditedPostAttribute
    } = select('core/editor');

    const author = __unstableGetAuthor(getEditedPostAttribute('author'));

    const query = !fieldValue || '' === fieldValue ? {} : {
      search: fieldValue
    };
    return {
      authorId: getEditedPostAttribute('author'),
      postAuthor: author,
      authors: getAuthors(query),
      isLoading: isResolving('core', 'getAuthors', [query])
    };
  }, [fieldValue]);
  const {
    editPost
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  const authorOptions = Object(external_wp_element_["useMemo"])(() => {
    const fetchedAuthors = (authors !== null && authors !== void 0 ? authors : []).map(author => {
      return {
        value: author.id,
        label: author.name
      };
    }); // Ensure the current author is included in the dropdown list.

    const foundAuthor = fetchedAuthors.findIndex(({
      value
    }) => (postAuthor === null || postAuthor === void 0 ? void 0 : postAuthor.id) === value);

    if (foundAuthor < 0 && postAuthor) {
      return [{
        value: postAuthor.id,
        label: postAuthor.name
      }, ...fetchedAuthors];
    }

    return fetchedAuthors;
  }, [authors, postAuthor]); // Initializes the post author properly
  // Also ensures external changes are reflected.

  Object(external_wp_element_["useEffect"])(() => {
    if (postAuthor) {
      setFieldValue(postAuthor.name);
    }
  }, [postAuthor]);
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

  if (!postAuthor) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["ComboboxControl"], {
    label: Object(external_wp_i18n_["__"])('Author'),
    options: authorOptions,
    value: authorId,
    onFilterValueChange: Object(external_lodash_["debounce"])(handleKeydown, 300),
    onChange: handleSelect,
    isLoading: isLoading,
    allowReset: false
  });
}

/* harmony default export */ var combobox = (PostAuthorCombobox);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/select.js


/**
 * WordPress dependencies
 */





function PostAuthorSelect() {
  const {
    editPost
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  const {
    postAuthor,
    authors
  } = Object(external_wp_data_["useSelect"])(select => {
    const authorsFromAPI = select('core').getAuthors();
    return {
      postAuthor: select('core/editor').getEditedPostAttribute('author'),
      authors: authorsFromAPI.map(author => ({
        label: Object(external_wp_htmlEntities_["decodeEntities"])(author.name),
        value: author.id
      }))
    };
  }, []);

  const setAuthorId = value => {
    const author = Number(value);
    editPost({
      author
    });
  };

  return Object(external_wp_element_["createElement"])(external_wp_components_["SelectControl"], {
    className: "post-author-selector",
    label: Object(external_wp_i18n_["__"])('Author'),
    options: authors,
    onChange: setAuthorId,
    value: postAuthor
  });
}

/* harmony default export */ var post_author_select = (PostAuthorSelect);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-author/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const minimumUsersForCombobox = 25;

function PostAuthor() {
  const showCombobox = Object(external_wp_data_["useSelect"])(select => {
    // Not using `getUsers()` because it requires `list_users` capability.
    const authors = select(external_wp_coreData_["store"]).getAuthors();
    return (authors === null || authors === void 0 ? void 0 : authors.length) >= minimumUsersForCombobox;
  }, []);

  if (showCombobox) {
    return Object(external_wp_element_["createElement"])(combobox, null);
  }

  return Object(external_wp_element_["createElement"])(post_author_select, null);
}

/* harmony default export */ var post_author = (PostAuthor);

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


function PostAuthorCheck({
  hasAssignAuthorAction,
  authors,
  children
}) {
  if (!hasAssignAuthorAction || !authors || 1 >= authors.length) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(post_type_support_check, {
    supportKeys: "author"
  }, children);
}
/* harmony default export */ var post_author_check = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const post = select('core/editor').getCurrentPost();
  return {
    hasAssignAuthorAction: Object(external_lodash_["get"])(post, ['_links', 'wp:action-assign-author'], false),
    postType: select('core/editor').getCurrentPostType(),
    authors: select('core').getAuthors()
  };
}), external_wp_compose_["withInstanceId"]])(PostAuthorCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-comments/index.js


/**
 * WordPress dependencies
 */





function PostComments({
  commentStatus = 'open',
  ...props
}) {
  const onToggleComments = () => props.editPost({
    comment_status: commentStatus === 'open' ? 'closed' : 'open'
  });

  return Object(external_wp_element_["createElement"])(external_wp_components_["CheckboxControl"], {
    label: Object(external_wp_i18n_["__"])('Allow comments'),
    checked: commentStatus === 'open',
    onChange: onToggleComments
  });
}

/* harmony default export */ var post_comments = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  return {
    commentStatus: select('core/editor').getEditedPostAttribute('comment_status')
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  editPost: dispatch('core/editor').editPost
}))])(PostComments));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-excerpt/index.js


/**
 * WordPress dependencies
 */





function PostExcerpt({
  excerpt,
  onUpdateExcerpt
}) {
  return Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-excerpt"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["TextareaControl"], {
    label: Object(external_wp_i18n_["__"])('Write an excerpt (optional)'),
    className: "editor-post-excerpt__textarea",
    onChange: value => onUpdateExcerpt(value),
    value: excerpt
  }), Object(external_wp_element_["createElement"])(external_wp_components_["ExternalLink"], {
    href: Object(external_wp_i18n_["__"])('https://wordpress.org/support/article/excerpt/')
  }, Object(external_wp_i18n_["__"])('Learn more about manual excerpts')));
}

/* harmony default export */ var post_excerpt = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  return {
    excerpt: select('core/editor').getEditedPostAttribute('excerpt')
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  onUpdateExcerpt(excerpt) {
    dispatch('core/editor').editPost({
      excerpt
    });
  }

}))])(PostExcerpt));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-excerpt/check.js



/**
 * Internal dependencies
 */


function PostExcerptCheck(props) {
  return Object(external_wp_element_["createElement"])(post_type_support_check, Object(esm_extends["a" /* default */])({}, props, {
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


function ThemeSupportCheck({
  themeSupports,
  children,
  postType,
  supportKeys
}) {
  const isSupported = Object(external_lodash_["some"])(Object(external_lodash_["castArray"])(supportKeys), key => {
    const supported = Object(external_lodash_["get"])(themeSupports, [key], false); // 'post-thumbnails' can be boolean or an array of post types.
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
/* harmony default export */ var theme_support_check = (Object(external_wp_data_["withSelect"])(select => {
  const {
    getThemeSupports
  } = select('core');
  const {
    getEditedPostAttribute
  } = select('core/editor');
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
  return Object(external_wp_element_["createElement"])(theme_support_check, {
    supportKeys: "post-thumbnails"
  }, Object(external_wp_element_["createElement"])(post_type_support_check, Object(esm_extends["a" /* default */])({}, props, {
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


const ALLOWED_MEDIA_TYPES = ['image']; // Used when labels from post type were not yet loaded or when they are not present.

const DEFAULT_FEATURE_IMAGE_LABEL = Object(external_wp_i18n_["__"])('Featured image');

const DEFAULT_SET_FEATURE_IMAGE_LABEL = Object(external_wp_i18n_["__"])('Set featured image');

const DEFAULT_REMOVE_FEATURE_IMAGE_LABEL = Object(external_wp_i18n_["__"])('Remove image');

function PostFeaturedImage({
  currentPostId,
  featuredImageId,
  onUpdateImage,
  onDropImage,
  onRemoveImage,
  media,
  postType,
  noticeUI
}) {
  var _media$media_details$, _media$media_details$2;

  const postLabel = Object(external_lodash_["get"])(postType, ['labels'], {});
  const instructions = Object(external_wp_element_["createElement"])("p", null, Object(external_wp_i18n_["__"])('To edit the featured image, you need permission to upload media.'));
  let mediaWidth, mediaHeight, mediaSourceUrl;

  if (media) {
    const mediaSize = Object(external_wp_hooks_["applyFilters"])('editor.PostFeaturedImage.imageSize', 'post-thumbnail', media.id, currentPostId);

    if (Object(external_lodash_["has"])(media, ['media_details', 'sizes', mediaSize])) {
      // use mediaSize when available
      mediaWidth = media.media_details.sizes[mediaSize].width;
      mediaHeight = media.media_details.sizes[mediaSize].height;
      mediaSourceUrl = media.media_details.sizes[mediaSize].source_url;
    } else {
      // get fallbackMediaSize if mediaSize is not available
      const fallbackMediaSize = Object(external_wp_hooks_["applyFilters"])('editor.PostFeaturedImage.imageSize', 'thumbnail', media.id, currentPostId);

      if (Object(external_lodash_["has"])(media, ['media_details', 'sizes', fallbackMediaSize])) {
        // use fallbackMediaSize when mediaSize is not available
        mediaWidth = media.media_details.sizes[fallbackMediaSize].width;
        mediaHeight = media.media_details.sizes[fallbackMediaSize].height;
        mediaSourceUrl = media.media_details.sizes[fallbackMediaSize].source_url;
      } else {
        // use full image size when mediaFallbackSize and mediaSize are not available
        mediaWidth = media.media_details.width;
        mediaHeight = media.media_details.height;
        mediaSourceUrl = media.source_url;
      }
    }
  }

  return Object(external_wp_element_["createElement"])(post_featured_image_check, null, noticeUI, Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-featured-image"
  }, media && Object(external_wp_element_["createElement"])("div", {
    id: `editor-post-featured-image-${featuredImageId}-describedby`,
    className: "hidden"
  }, media.alt_text && Object(external_wp_i18n_["sprintf"])( // Translators: %s: The selected image alt text.
  Object(external_wp_i18n_["__"])('Current image: %s'), media.alt_text), !media.alt_text && Object(external_wp_i18n_["sprintf"])( // Translators: %s: The selected image filename.
  Object(external_wp_i18n_["__"])('The current image has no alternative text. The file name is: %s'), ((_media$media_details$ = media.media_details.sizes) === null || _media$media_details$ === void 0 ? void 0 : (_media$media_details$2 = _media$media_details$.full) === null || _media$media_details$2 === void 0 ? void 0 : _media$media_details$2.file) || media.slug)), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["MediaUploadCheck"], {
    fallback: instructions
  }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["MediaUpload"], {
    title: postLabel.featured_image || DEFAULT_FEATURE_IMAGE_LABEL,
    onSelect: onUpdateImage,
    unstableFeaturedImageFlow: true,
    allowedTypes: ALLOWED_MEDIA_TYPES,
    modalClass: "editor-post-featured-image__media-modal",
    render: ({
      open
    }) => Object(external_wp_element_["createElement"])("div", {
      className: "editor-post-featured-image__container"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      className: !featuredImageId ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview',
      onClick: open,
      "aria-label": !featuredImageId ? null : Object(external_wp_i18n_["__"])('Edit or update the image'),
      "aria-describedby": !featuredImageId ? null : `editor-post-featured-image-${featuredImageId}-describedby`
    }, !!featuredImageId && media && Object(external_wp_element_["createElement"])(external_wp_components_["ResponsiveWrapper"], {
      naturalWidth: mediaWidth,
      naturalHeight: mediaHeight,
      isInline: true
    }, Object(external_wp_element_["createElement"])("img", {
      src: mediaSourceUrl,
      alt: ""
    })), !!featuredImageId && !media && Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null), !featuredImageId && (postLabel.set_featured_image || DEFAULT_SET_FEATURE_IMAGE_LABEL)), Object(external_wp_element_["createElement"])(external_wp_components_["DropZone"], {
      onFilesDrop: onDropImage
    })),
    value: featuredImageId
  })), !!featuredImageId && media && !media.isLoading && Object(external_wp_element_["createElement"])(external_wp_blockEditor_["MediaUploadCheck"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["MediaUpload"], {
    title: postLabel.featured_image || DEFAULT_FEATURE_IMAGE_LABEL,
    onSelect: onUpdateImage,
    unstableFeaturedImageFlow: true,
    allowedTypes: ALLOWED_MEDIA_TYPES,
    modalClass: "editor-post-featured-image__media-modal",
    render: ({
      open
    }) => Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      onClick: open,
      isSecondary: true
    }, Object(external_wp_i18n_["__"])('Replace Image'))
  })), !!featuredImageId && Object(external_wp_element_["createElement"])(external_wp_blockEditor_["MediaUploadCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    onClick: onRemoveImage,
    isLink: true,
    isDestructive: true
  }, postLabel.remove_featured_image || DEFAULT_REMOVE_FEATURE_IMAGE_LABEL))));
}

const applyWithSelect = Object(external_wp_data_["withSelect"])(select => {
  const {
    getMedia,
    getPostType
  } = select('core');
  const {
    getCurrentPostId,
    getEditedPostAttribute
  } = select('core/editor');
  const featuredImageId = getEditedPostAttribute('featured_media');
  return {
    media: featuredImageId ? getMedia(featuredImageId) : null,
    currentPostId: getCurrentPostId(),
    postType: getPostType(getEditedPostAttribute('type')),
    featuredImageId
  };
});
const applyWithDispatch = Object(external_wp_data_["withDispatch"])((dispatch, {
  noticeOperations
}, {
  select
}) => {
  const {
    editPost
  } = dispatch('core/editor');
  return {
    onUpdateImage(image) {
      editPost({
        featured_media: image.id
      });
    },

    onDropImage(filesList) {
      select(external_wp_blockEditor_["store"]).getSettings().mediaUpload({
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
/* harmony default export */ var post_featured_image = (Object(external_wp_compose_["compose"])(external_wp_components_["withNotices"], applyWithSelect, applyWithDispatch, Object(external_wp_components_["withFilters"])('editor.PostFeaturedImage'))(PostFeaturedImage));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-format/check.js



/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function PostFormatCheck({
  disablePostFormats,
  ...props
}) {
  return !disablePostFormats && Object(external_wp_element_["createElement"])(post_type_support_check, Object(esm_extends["a" /* default */])({}, props, {
    supportKeys: "post-formats"
  }));
}

/* harmony default export */ var post_format_check = (Object(external_wp_data_["withSelect"])(select => {
  const editorSettings = select('core/editor').getEditorSettings();
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

 // All WP post formats, sorted alphabetically by translated name.

const POST_FORMATS = [{
  id: 'aside',
  caption: Object(external_wp_i18n_["__"])('Aside')
}, {
  id: 'audio',
  caption: Object(external_wp_i18n_["__"])('Audio')
}, {
  id: 'chat',
  caption: Object(external_wp_i18n_["__"])('Chat')
}, {
  id: 'gallery',
  caption: Object(external_wp_i18n_["__"])('Gallery')
}, {
  id: 'image',
  caption: Object(external_wp_i18n_["__"])('Image')
}, {
  id: 'link',
  caption: Object(external_wp_i18n_["__"])('Link')
}, {
  id: 'quote',
  caption: Object(external_wp_i18n_["__"])('Quote')
}, {
  id: 'standard',
  caption: Object(external_wp_i18n_["__"])('Standard')
}, {
  id: 'status',
  caption: Object(external_wp_i18n_["__"])('Status')
}, {
  id: 'video',
  caption: Object(external_wp_i18n_["__"])('Video')
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
  const instanceId = Object(external_wp_compose_["useInstanceId"])(PostFormat);
  const postFormatSelectorId = `post-format-selector-${instanceId}`;
  const {
    postFormat,
    suggestedFormat,
    supportedFormats
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditedPostAttribute,
      getSuggestedPostFormat
    } = select('core/editor');

    const _postFormat = getEditedPostAttribute('format');

    const themeSupports = select('core').getThemeSupports();
    return {
      postFormat: _postFormat !== null && _postFormat !== void 0 ? _postFormat : 'standard',
      suggestedFormat: getSuggestedPostFormat(),
      // Ensure current format is always in the set.
      // The current format may not be a format supported by the theme.
      supportedFormats: Object(external_lodash_["union"])([_postFormat], Object(external_lodash_["get"])(themeSupports, ['formats'], []))
    };
  }, []);
  const formats = POST_FORMATS.filter(format => Object(external_lodash_["includes"])(supportedFormats, format.id));
  const suggestion = Object(external_lodash_["find"])(formats, format => format.id === suggestedFormat);
  const {
    editPost
  } = Object(external_wp_data_["useDispatch"])('core/editor');

  const onUpdatePostFormat = format => editPost({
    format
  });

  return Object(external_wp_element_["createElement"])(post_format_check, null, Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-format"
  }, Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-format__content"
  }, Object(external_wp_element_["createElement"])("label", {
    htmlFor: postFormatSelectorId
  }, Object(external_wp_i18n_["__"])('Post Format')), Object(external_wp_element_["createElement"])(external_wp_components_["SelectControl"], {
    value: postFormat,
    onChange: format => onUpdatePostFormat(format),
    id: postFormatSelectorId,
    options: formats.map(format => ({
      label: format.caption,
      value: format.id
    }))
  })), suggestion && suggestion.id !== postFormat && Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-format__suggestion"
  }, Object(external_wp_i18n_["__"])('Suggestion:'), ' ', Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    isLink: true,
    onClick: () => onUpdatePostFormat(suggestion.id)
  }, suggestion.caption))));
}

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/backup.js
var backup = __webpack_require__("Y39O");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-last-revision/check.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


function PostLastRevisionCheck({
  lastRevisionId,
  revisionsCount,
  children
}) {
  if (!lastRevisionId || revisionsCount < 2) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(post_type_support_check, {
    supportKeys: "revisions"
  }, children);
}
/* harmony default export */ var post_last_revision_check = (Object(external_wp_data_["withSelect"])(select => {
  const {
    getCurrentPostLastRevisionId,
    getCurrentPostRevisionsCount
  } = select('core/editor');
  return {
    lastRevisionId: getCurrentPostLastRevisionId(),
    revisionsCount: getCurrentPostRevisionsCount()
  };
})(PostLastRevisionCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-last-revision/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




function LastRevision({
  lastRevisionId,
  revisionsCount
}) {
  return Object(external_wp_element_["createElement"])(post_last_revision_check, null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    href: getWPAdminURL('revision.php', {
      revision: lastRevisionId,
      gutenberg: true
    }),
    className: "editor-post-last-revision__title",
    icon: backup["a" /* default */]
  }, Object(external_wp_i18n_["sprintf"])(
  /* translators: %d: number of revisions */
  Object(external_wp_i18n_["_n"])('%d Revision', '%d Revisions', revisionsCount), revisionsCount)));
}

/* harmony default export */ var post_last_revision = (Object(external_wp_data_["withSelect"])(select => {
  const {
    getCurrentPostLastRevisionId,
    getCurrentPostRevisionsCount
  } = select('core/editor');
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
  let markup = Object(external_wp_element_["renderToString"])(Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-preview-button__interstitial-message"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["SVG"], {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 96 96"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Path"], {
    className: "outer",
    d: "M48 12c19.9 0 36 16.1 36 36S67.9 84 48 84 12 67.9 12 48s16.1-36 36-36",
    fill: "none"
  }), Object(external_wp_element_["createElement"])(external_wp_components_["Path"], {
    className: "inner",
    d: "M69.5 46.4c0-3.9-1.4-6.7-2.6-8.8-1.6-2.6-3.1-4.9-3.1-7.5 0-2.9 2.2-5.7 5.4-5.7h.4C63.9 19.2 56.4 16 48 16c-11.2 0-21 5.7-26.7 14.4h2.1c3.3 0 8.5-.4 8.5-.4 1.7-.1 1.9 2.4.2 2.6 0 0-1.7.2-3.7.3L40 67.5l7-20.9L42 33c-1.7-.1-3.3-.3-3.3-.3-1.7-.1-1.5-2.7.2-2.6 0 0 5.3.4 8.4.4 3.3 0 8.5-.4 8.5-.4 1.7-.1 1.9 2.4.2 2.6 0 0-1.7.2-3.7.3l11.5 34.3 3.3-10.4c1.6-4.5 2.4-7.8 2.4-10.5zM16.1 48c0 12.6 7.3 23.5 18 28.7L18.8 35c-1.7 4-2.7 8.4-2.7 13zm32.5 2.8L39 78.6c2.9.8 5.9 1.3 9 1.3 3.7 0 7.3-.6 10.6-1.8-.1-.1-.2-.3-.2-.4l-9.8-26.9zM76.2 36c0 3.2-.6 6.9-2.4 11.4L64 75.6c9.5-5.5 15.9-15.8 15.9-27.6 0-5.5-1.4-10.8-3.9-15.3.1 1 .2 2.1.2 3.3z",
    fill: "none"
  })), Object(external_wp_element_["createElement"])("p", null, Object(external_wp_i18n_["__"])('Generating preview…'))));
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

  markup = Object(external_wp_hooks_["applyFilters"])('editor.PostPreview.interstitialMarkup', markup);
  targetDocument.write(markup);
  targetDocument.title = Object(external_wp_i18n_["__"])('Generating preview…');
  targetDocument.close();
}

class post_preview_button_PostPreviewButton extends external_wp_element_["Component"] {
  constructor() {
    super(...arguments);
    this.buttonRef = Object(external_wp_element_["createRef"])();
    this.openPreviewWindow = this.openPreviewWindow.bind(this);
  }

  componentDidUpdate(prevProps) {
    const {
      previewLink
    } = this.props; // This relies on the window being responsible to unset itself when
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


  setPreviewWindowLink(url) {
    const {
      previewWindow
    } = this;

    if (previewWindow && !previewWindow.closed) {
      previewWindow.location = url;

      if (this.buttonRef.current) {
        this.buttonRef.current.focus();
      }
    }
  }

  getWindowTarget() {
    const {
      postId
    } = this.props;
    return `wp-preview-${postId}`;
  }

  openPreviewWindow(event) {
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

  render() {
    const {
      previewLink,
      currentPostLink,
      isSaveable,
      role
    } = this.props; // Link to the `?preview=true` URL if we have it, since this lets us see
    // changes that were autosaved since the post was last published. Otherwise,
    // just link to the post's URL.

    const href = previewLink || currentPostLink;
    const classNames = classnames_default()({
      'editor-post-preview': !this.props.className
    }, this.props.className);
    return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      isTertiary: !this.props.className,
      className: classNames,
      href: href,
      target: this.getWindowTarget(),
      disabled: !isSaveable,
      onClick: this.openPreviewWindow,
      ref: this.buttonRef,
      role: role
    }, this.props.textContent ? this.props.textContent : Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_i18n_["_x"])('Preview', 'imperative verb'), Object(external_wp_element_["createElement"])(external_wp_components_["VisuallyHidden"], {
      as: "span"
    },
    /* translators: accessibility text */
    Object(external_wp_i18n_["__"])('(opens in a new tab)'))));
  }

}
/* harmony default export */ var post_preview_button = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])((select, {
  forcePreviewLink,
  forceIsAutosaveable
}) => {
  const {
    getCurrentPostId,
    getCurrentPostAttribute,
    getEditedPostAttribute,
    isEditedPostSaveable,
    isEditedPostAutosaveable,
    getEditedPostPreviewLink
  } = select('core/editor');
  const {
    getPostType
  } = select('core');
  const previewLink = getEditedPostPreviewLink();
  const postType = getPostType(getEditedPostAttribute('type'));
  return {
    postId: getCurrentPostId(),
    currentPostLink: getCurrentPostAttribute('link'),
    previewLink: forcePreviewLink !== undefined ? forcePreviewLink : previewLink,
    isSaveable: isEditedPostSaveable(),
    isAutosaveable: forceIsAutosaveable || isEditedPostAutosaveable(),
    isViewable: Object(external_lodash_["get"])(postType, ['viewable'], false),
    isDraft: ['draft', 'auto-draft'].indexOf(getEditedPostAttribute('status')) !== -1
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  autosave: dispatch('core/editor').autosave,
  savePost: dispatch('core/editor').savePost
})), Object(external_wp_compose_["ifCondition"])(({
  isViewable
}) => isViewable)])(post_preview_button_PostPreviewButton));

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



function PostLockedModal() {
  const instanceId = Object(external_wp_compose_["useInstanceId"])(PostLockedModal);
  const hookName = 'core/editor/post-locked-modal-' + instanceId;
  const {
    autosave,
    updatePostLock
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  const {
    isLocked,
    isTakeover,
    user,
    postId,
    postLockUtils,
    activePostLock,
    postType
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      isPostLocked,
      isPostLockTakeover,
      getPostLockUser,
      getCurrentPostId,
      getActivePostLock,
      getEditedPostAttribute,
      getEditorSettings
    } = select('core/editor');
    const {
      getPostType
    } = select('core');
    return {
      isLocked: isPostLocked(),
      isTakeover: isPostLockTakeover(),
      user: getPostLockUser(),
      postId: getCurrentPostId(),
      postLockUtils: getEditorSettings().postLockUtils,
      activePostLock: getActivePostLock(),
      postType: getPostType(getEditedPostAttribute('type'))
    };
  });
  Object(external_wp_element_["useEffect"])(() => {
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
    } // Details on these events on the Heartbeat API docs
    // https://developer.wordpress.org/plugins/javascript/heartbeat-api/


    Object(external_wp_hooks_["addAction"])('heartbeat.send', hookName, sendPostLock);
    Object(external_wp_hooks_["addAction"])('heartbeat.tick', hookName, receivePostLock);
    window.addEventListener('beforeunload', releasePostLock);
    return () => {
      Object(external_wp_hooks_["removeAction"])('heartbeat.send', hookName);
      Object(external_wp_hooks_["removeAction"])('heartbeat.tick', hookName);
      window.removeEventListener('beforeunload', releasePostLock);
    };
  }, []);

  if (!isLocked) {
    return null;
  }

  const userDisplayName = user.name;
  const userAvatar = user.avatar;
  const unlockUrl = Object(external_wp_url_["addQueryArgs"])('post.php', {
    'get-post-lock': '1',
    lockKey: true,
    post: postId,
    action: 'edit',
    _wpnonce: postLockUtils.nonce
  });
  const allPostsUrl = getWPAdminURL('edit.php', {
    post_type: Object(external_lodash_["get"])(postType, ['slug'])
  });

  const allPostsLabel = Object(external_wp_i18n_["__"])('Exit the Editor');

  return Object(external_wp_element_["createElement"])(external_wp_components_["Modal"], {
    title: isTakeover ? Object(external_wp_i18n_["__"])('Someone else has taken over this post.') : Object(external_wp_i18n_["__"])('This post is already being edited.'),
    focusOnMount: true,
    shouldCloseOnClickOutside: false,
    shouldCloseOnEsc: false,
    isDismissible: false,
    className: "editor-post-locked-modal"
  }, !!userAvatar && Object(external_wp_element_["createElement"])("img", {
    src: userAvatar,
    alt: Object(external_wp_i18n_["__"])('Avatar'),
    className: "editor-post-locked-modal__avatar"
  }), !!isTakeover && Object(external_wp_element_["createElement"])("div", null, Object(external_wp_element_["createElement"])("div", null, userDisplayName ? Object(external_wp_i18n_["sprintf"])(
  /* translators: %s: user's display name */
  Object(external_wp_i18n_["__"])('%s now has editing control of this post. Don’t worry, your changes up to this moment have been saved.'), userDisplayName) : Object(external_wp_i18n_["__"])('Another user now has editing control of this post. Don’t worry, your changes up to this moment have been saved.')), Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-locked-modal__buttons"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    isPrimary: true,
    href: allPostsUrl
  }, allPostsLabel))), !isTakeover && Object(external_wp_element_["createElement"])("div", null, Object(external_wp_element_["createElement"])("div", null, userDisplayName ? Object(external_wp_i18n_["sprintf"])(
  /* translators: %s: user's display name */
  Object(external_wp_i18n_["__"])('%s is currently working on this post, which means you cannot make changes, unless you take over.'), userDisplayName) : Object(external_wp_i18n_["__"])('Another user is currently working on this post, which means you cannot make changes, unless you take over.')), Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-locked-modal__buttons"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    isSecondary: true,
    href: allPostsUrl
  }, allPostsLabel), Object(external_wp_element_["createElement"])(post_preview_button, null), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    isPrimary: true,
    href: unlockUrl
  }, Object(external_wp_i18n_["__"])('Take Over')))));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-pending-status/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PostPendingStatusCheck({
  hasPublishAction,
  isPublished,
  children
}) {
  if (isPublished || !hasPublishAction) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_pending_status_check = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])(select => {
  const {
    isCurrentPostPublished,
    getCurrentPostType,
    getCurrentPost
  } = select('core/editor');
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


function PostPendingStatus({
  status,
  onUpdateStatus
}) {
  const togglePendingStatus = () => {
    const updatedStatus = status === 'pending' ? 'draft' : 'pending';
    onUpdateStatus(updatedStatus);
  };

  return Object(external_wp_element_["createElement"])(post_pending_status_check, null, Object(external_wp_element_["createElement"])(external_wp_components_["CheckboxControl"], {
    label: Object(external_wp_i18n_["__"])('Pending review'),
    checked: status === 'pending',
    onChange: togglePendingStatus
  }));
}
/* harmony default export */ var post_pending_status = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])(select => ({
  status: select('core/editor').getEditedPostAttribute('status')
})), Object(external_wp_data_["withDispatch"])(dispatch => ({
  onUpdateStatus(status) {
    dispatch('core/editor').editPost({
      status
    });
  }

})))(PostPendingStatus));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-pingbacks/index.js


/**
 * WordPress dependencies
 */





function PostPingbacks({
  pingStatus = 'open',
  ...props
}) {
  const onTogglePingback = () => props.editPost({
    ping_status: pingStatus === 'open' ? 'closed' : 'open'
  });

  return Object(external_wp_element_["createElement"])(external_wp_components_["CheckboxControl"], {
    label: Object(external_wp_i18n_["__"])('Allow pingbacks & trackbacks'),
    checked: pingStatus === 'open',
    onChange: onTogglePingback
  });
}

/* harmony default export */ var post_pingbacks = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  return {
    pingStatus: select('core/editor').getEditedPostAttribute('ping_status')
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  editPost: dispatch('core/editor').editPost
}))])(PostPingbacks));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-publish-button/label.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
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
    return Object(external_wp_i18n_["__"])('Publishing…');
  } else if (isPublished && isSaving && !isAutosaving) {
    /* translators: button label text should, if possible, be under 16 characters. */
    return Object(external_wp_i18n_["__"])('Updating…');
  } else if (isBeingScheduled && isSaving && !isAutosaving) {
    /* translators: button label text should, if possible, be under 16 characters. */
    return Object(external_wp_i18n_["__"])('Scheduling…');
  }

  if (!hasPublishAction) {
    return hasNonPostEntityChanges ? Object(external_wp_i18n_["__"])('Submit for Review…') : Object(external_wp_i18n_["__"])('Submit for Review');
  } else if (isPublished) {
    return hasNonPostEntityChanges ? Object(external_wp_i18n_["__"])('Update…') : Object(external_wp_i18n_["__"])('Update');
  } else if (isBeingScheduled) {
    return hasNonPostEntityChanges ? Object(external_wp_i18n_["__"])('Schedule…') : Object(external_wp_i18n_["__"])('Schedule');
  }

  return Object(external_wp_i18n_["__"])('Publish');
}
/* harmony default export */ var post_publish_button_label = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])((select, {
  forceIsSaving
}) => {
  const {
    isCurrentPostPublished,
    isEditedPostBeingScheduled,
    isSavingPost,
    isPublishingPost,
    getCurrentPost,
    getCurrentPostType,
    isAutosavingPost
  } = select('core/editor');
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


class post_publish_button_PostPublishButton extends external_wp_element_["Component"] {
  constructor(props) {
    super(props);
    this.buttonNode = Object(external_wp_element_["createRef"])();
    this.createOnClick = this.createOnClick.bind(this);
    this.closeEntitiesSavedStates = this.closeEntitiesSavedStates.bind(this);
    this.state = {
      entitiesSavedStatesCallback: false
    };
  }

  componentDidMount() {
    if (this.props.focusOnMount) {
      this.buttonNode.current.focus();
    }
  }

  createOnClick(callback) {
    return (...args) => {
      const {
        hasNonPostEntityChanges
      } = this.props;

      if (hasNonPostEntityChanges) {
        // The modal for multiple entity saving will open,
        // hold the callback for saving/publishing the post
        // so that we can call it if the post entity is checked.
        this.setState({
          entitiesSavedStatesCallback: () => callback(...args)
        }); // Open the save panel by setting its callback.
        // To set a function on the useState hook, we must set it
        // with another function (() => myFunction). Passing the
        // function on its own will cause an error when called.

        this.props.setEntitiesSavedStatesCallback(() => this.closeEntitiesSavedStates);
        return external_lodash_["noop"];
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
      if (savedEntities && Object(external_lodash_["some"])(savedEntities, elt => elt.kind === 'postType' && elt.name === postType && elt.key === postId)) {
        // The post entity was checked, call the held callback from `createOnClick`.
        entitiesSavedStatesCallback();
      }
    });
  }

  render() {
    const {
      forceIsDirty,
      forceIsSaving,
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
      onSubmit = external_lodash_["noop"],
      onToggle,
      visibility,
      hasNonPostEntityChanges
    } = this.props;
    const isButtonDisabled = isSaving || forceIsSaving || !isSaveable || isPostSavingLocked || !isPublishable && !forceIsDirty;
    const isToggleDisabled = isPublished || isSaving || forceIsSaving || !isSaveable || !isPublishable && !forceIsDirty;
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
      'aria-disabled': isButtonDisabled && !hasNonPostEntityChanges,
      className: 'editor-post-publish-button',
      isBusy: !isAutoSaving && isSaving && isPublished,
      isPrimary: true,
      onClick: this.createOnClick(onClickButton)
    };
    const toggleProps = {
      'aria-disabled': isToggleDisabled && !hasNonPostEntityChanges,
      'aria-expanded': isOpen,
      className: 'editor-post-publish-panel__toggle',
      isBusy: isSaving && isPublished,
      isPrimary: true,
      onClick: this.createOnClick(onClickToggle)
    };
    const toggleChildren = isBeingScheduled ? Object(external_wp_i18n_["__"])('Schedule…') : Object(external_wp_i18n_["__"])('Publish');
    const buttonChildren = Object(external_wp_element_["createElement"])(post_publish_button_label, {
      forceIsSaving: forceIsSaving,
      hasNonPostEntityChanges: hasNonPostEntityChanges
    });
    const componentProps = isToggle ? toggleProps : buttonProps;
    const componentChildren = isToggle ? toggleChildren : buttonChildren;
    return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], Object(esm_extends["a" /* default */])({
      ref: this.buttonNode
    }, componentProps, {
      className: classnames_default()(componentProps.className, 'editor-post-publish-button__button', {
        'has-changes-dot': hasNonPostEntityChanges
      })
    }), componentChildren));
  }

}
/* harmony default export */ var post_publish_button = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
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
    hasNonPostEntityChanges
  } = select('core/editor');

  const _isAutoSaving = isAutosavingPost();

  return {
    isSaving: isSavingPost() || _isAutoSaving,
    isAutoSaving: _isAutoSaving,
    isBeingScheduled: isEditedPostBeingScheduled(),
    visibility: getEditedPostVisibility(),
    isSaveable: isEditedPostSaveable(),
    isPostSavingLocked: isPostSavingLocked(),
    isPublishable: isEditedPostPublishable(),
    isPublished: isCurrentPostPublished(),
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    postType: getCurrentPostType(),
    postId: getCurrentPostId(),
    hasNonPostEntityChanges: hasNonPostEntityChanges()
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    editPost,
    savePost
  } = dispatch('core/editor');
  return {
    onStatusChange: status => editPost({
      status
    }, {
      undoIgnore: true
    }),
    onSave: savePost
  };
})])(post_publish_button_PostPublishButton));

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close-small.js
var close_small = __webpack_require__("bWcr");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/wordpress.js
var wordpress = __webpack_require__("wduq");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/utils.js
/**
 * WordPress dependencies
 */

const visibilityOptions = [{
  value: 'public',
  label: Object(external_wp_i18n_["__"])('Public'),
  info: Object(external_wp_i18n_["__"])('Visible to everyone.')
}, {
  value: 'private',
  label: Object(external_wp_i18n_["__"])('Private'),
  info: Object(external_wp_i18n_["__"])('Only visible to site admins and editors.')
}, {
  value: 'password',
  label: Object(external_wp_i18n_["__"])('Password Protected'),
  info: Object(external_wp_i18n_["__"])('Protected with a password you choose. Only those with the password can view this post.')
}];

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


class post_visibility_PostVisibility extends external_wp_element_["Component"] {
  constructor(props) {
    super(...arguments);
    this.setPublic = this.setPublic.bind(this);
    this.setPrivate = this.setPrivate.bind(this);
    this.setPasswordProtected = this.setPasswordProtected.bind(this);
    this.updatePassword = this.updatePassword.bind(this);
    this.state = {
      hasPassword: !!props.password
    };
  }

  setPublic() {
    const {
      visibility,
      onUpdateVisibility,
      status
    } = this.props;
    onUpdateVisibility(visibility === 'private' ? 'draft' : status);
    this.setState({
      hasPassword: false
    });
  }

  setPrivate() {
    if ( // eslint-disable-next-line no-alert
    !window.confirm(Object(external_wp_i18n_["__"])('Would you like to privately publish this post now?'))) {
      return;
    }

    const {
      onUpdateVisibility,
      onSave
    } = this.props;
    onUpdateVisibility('private');
    this.setState({
      hasPassword: false
    });
    onSave();
  }

  setPasswordProtected() {
    const {
      visibility,
      onUpdateVisibility,
      status,
      password
    } = this.props;
    onUpdateVisibility(visibility === 'private' ? 'draft' : status, password || '');
    this.setState({
      hasPassword: true
    });
  }

  updatePassword(event) {
    const {
      status,
      onUpdateVisibility
    } = this.props;
    onUpdateVisibility(status, event.target.value);
  }

  render() {
    const {
      visibility,
      password,
      instanceId
    } = this.props;
    const visibilityHandlers = {
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
    return [Object(external_wp_element_["createElement"])("fieldset", {
      key: "visibility-selector",
      className: "editor-post-visibility__dialog-fieldset"
    }, Object(external_wp_element_["createElement"])("legend", {
      className: "editor-post-visibility__dialog-legend"
    }, Object(external_wp_i18n_["__"])('Post Visibility')), visibilityOptions.map(({
      value,
      label,
      info
    }) => Object(external_wp_element_["createElement"])("div", {
      key: value,
      className: "editor-post-visibility__choice"
    }, Object(external_wp_element_["createElement"])("input", {
      type: "radio",
      name: `editor-post-visibility__setting-${instanceId}`,
      value: value,
      onChange: visibilityHandlers[value].onSelect,
      checked: visibilityHandlers[value].checked,
      id: `editor-post-${value}-${instanceId}`,
      "aria-describedby": `editor-post-${value}-${instanceId}-description`,
      className: "editor-post-visibility__dialog-radio"
    }), Object(external_wp_element_["createElement"])("label", {
      htmlFor: `editor-post-${value}-${instanceId}`,
      className: "editor-post-visibility__dialog-label"
    }, label), Object(external_wp_element_["createElement"])("p", {
      id: `editor-post-${value}-${instanceId}-description`,
      className: "editor-post-visibility__dialog-info"
    }, info)))), this.state.hasPassword && Object(external_wp_element_["createElement"])("div", {
      className: "editor-post-visibility__dialog-password",
      key: "password-selector"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["VisuallyHidden"], {
      as: "label",
      htmlFor: `editor-post-visibility__dialog-password-input-${instanceId}`
    }, Object(external_wp_i18n_["__"])('Create password')), Object(external_wp_element_["createElement"])("input", {
      className: "editor-post-visibility__dialog-password-input",
      id: `editor-post-visibility__dialog-password-input-${instanceId}`,
      type: "text",
      onChange: this.updatePassword,
      value: password,
      placeholder: Object(external_wp_i18n_["__"])('Use a secure password')
    }))];
  }

}
/* harmony default export */ var post_visibility = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const {
    getEditedPostAttribute,
    getEditedPostVisibility
  } = select('core/editor');
  return {
    status: getEditedPostAttribute('status'),
    visibility: getEditedPostVisibility(),
    password: getEditedPostAttribute('password')
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    savePost,
    editPost
  } = dispatch('core/editor');
  return {
    onSave: savePost,

    onUpdateVisibility(status, password = '') {
      editPost({
        status,
        password
      });
    }

  };
}), external_wp_compose_["withInstanceId"]])(post_visibility_PostVisibility));

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



function PostVisibilityLabel({
  visibility
}) {
  const getVisibilityLabel = () => Object(external_lodash_["find"])(visibilityOptions, {
    value: visibility
  }).label;

  return getVisibilityLabel(visibility);
}

/* harmony default export */ var post_visibility_label = (Object(external_wp_data_["withSelect"])(select => ({
  visibility: select('core/editor').getEditedPostVisibility()
}))(PostVisibilityLabel));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function getDayOfTheMonth(date = new Date(), firstDay = true) {
  const d = new Date(date);
  return new Date(d.getFullYear(), d.getMonth() + (firstDay ? 0 : 1), firstDay ? 1 : 0).toISOString();
}

function PostSchedule() {
  const {
    postDate,
    postType
  } = Object(external_wp_data_["useSelect"])(select => ({
    postDate: select(store).getEditedPostAttribute('date'),
    postType: select(store).getCurrentPostType()
  }), []);
  const {
    editPost
  } = Object(external_wp_data_["useDispatch"])(store);

  const onUpdateDate = date => editPost({
    date
  });

  const [previewedMonth, setPreviewedMonth] = Object(external_wp_element_["useState"])(getDayOfTheMonth(postDate)); // Pick up published and schduled site posts.

  const eventsByPostType = Object(external_wp_data_["useSelect"])(select => select(external_wp_coreData_["store"]).getEntityRecords('postType', postType, {
    status: 'publish,future',
    after: getDayOfTheMonth(previewedMonth),
    before: getDayOfTheMonth(previewedMonth, false),
    exclude: [select(store).getCurrentPostId()]
  }), [previewedMonth, postType]);
  const events = Object(external_wp_element_["useMemo"])(() => (eventsByPostType || []).map(({
    title,
    type,
    date: eventDate
  }) => ({
    title: title === null || title === void 0 ? void 0 : title.rendered,
    type,
    date: new Date(eventDate)
  })), [eventsByPostType]);
  const ref = Object(external_wp_element_["useRef"])();

  const settings = Object(external_wp_date_["__experimentalGetSettings"])(); // To know if the current timezone is a 12 hour time with look for "a" in the time format
  // We also make sure this a is not escaped by a "/"


  const is12HourTime = /a(?!\\)/i.test(settings.formats.time.toLowerCase() // Test only the lower case a
  .replace(/\\\\/g, '') // Replace "//" with empty strings
  .split('').reverse().join('') // Reverse the string and test for "a" not followed by a slash
  );

  function onChange(newDate) {
    onUpdateDate(newDate);
    const {
      ownerDocument
    } = ref.current;
    ownerDocument.activeElement.blur();
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["DateTimePicker"], {
    ref: ref,
    currentDate: postDate,
    onChange: onChange,
    is12Hour: is12HourTime,
    events: events,
    onMonthPreviewed: setPreviewedMonth
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/label.js
/**
 * WordPress dependencies
 */



function PostScheduleLabel({
  date,
  isFloating
}) {
  const settings = Object(external_wp_date_["__experimentalGetSettings"])();

  return date && !isFloating ? Object(external_wp_date_["format"])(`${settings.formats.date} ${settings.formats.time}`, date) : Object(external_wp_i18n_["__"])('Immediately');
}
/* harmony default export */ var post_schedule_label = (Object(external_wp_data_["withSelect"])(select => {
  return {
    date: select('core/editor').getEditedPostAttribute('date'),
    isFloating: select('core/editor').isEditedPostDateFloating()
  };
})(PostScheduleLabel));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/most-used-terms.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


const MAX_MOST_USED_TERMS = 10;
const DEFAULT_QUERY = {
  per_page: MAX_MOST_USED_TERMS,
  orderby: 'count',
  order: 'desc',
  _fields: 'id,name,count'
};
function MostUsedTerms({
  onSelect,
  taxonomy
}) {
  const {
    _terms,
    showTerms
  } = Object(external_wp_data_["useSelect"])(select => {
    const mostUsedTerms = select(external_wp_coreData_["store"]).getEntityRecords('taxonomy', taxonomy.slug, DEFAULT_QUERY);
    return {
      _terms: mostUsedTerms,
      showTerms: (mostUsedTerms === null || mostUsedTerms === void 0 ? void 0 : mostUsedTerms.length) >= MAX_MOST_USED_TERMS
    };
  }, []);

  if (!showTerms) {
    return null;
  }

  const terms = unescapeTerms(_terms);
  const label = Object(external_lodash_["get"])(taxonomy, ['labels', 'most_used']);
  return Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-taxonomies__flat-term-most-used"
  }, Object(external_wp_element_["createElement"])("h3", {
    className: "editor-post-taxonomies__flat-term-most-used-label"
  }, label), Object(external_wp_element_["createElement"])("ul", {
    role: "list",
    className: "editor-post-taxonomies__flat-term-most-used-list"
  }, terms.map(term => Object(external_wp_element_["createElement"])("li", {
    key: term.id
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    isLink: true,
    onClick: () => onSelect(term)
  }, term.name)))));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-taxonomies/flat-term-selector.js


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

const MAX_TERMS_SUGGESTIONS = 20;
const flat_term_selector_DEFAULT_QUERY = {
  per_page: MAX_TERMS_SUGGESTIONS,
  orderby: 'count',
  order: 'desc',
  _fields: 'id,name,count'
};

const isSameTermName = (termA, termB) => unescapeString(termA).toLowerCase() === unescapeString(termB).toLowerCase();

const termNamesToIds = (names, terms) => {
  return names.map(termName => Object(external_lodash_["find"])(terms, term => isSameTermName(term.name, termName)).id);
};

class flat_term_selector_FlatTermSelector extends external_wp_element_["Component"] {
  constructor() {
    super(...arguments);
    this.onChange = this.onChange.bind(this);
    this.searchTerms = Object(external_lodash_["throttle"])(this.searchTerms.bind(this), 500);
    this.findOrCreateTerm = this.findOrCreateTerm.bind(this);
    this.appendTerm = this.appendTerm.bind(this);
    this.state = {
      loading: !Object(external_lodash_["isEmpty"])(this.props.terms),
      availableTerms: [],
      selectedTerms: []
    };
  }

  componentDidMount() {
    if (!Object(external_lodash_["isEmpty"])(this.props.terms)) {
      this.initRequest = this.fetchTerms({
        include: this.props.terms.join(','),
        per_page: -1
      });
      this.initRequest.then(() => {
        this.setState({
          loading: false
        });
      }, xhr => {
        if (xhr.statusText === 'abort') {
          return;
        }

        this.setState({
          loading: false
        });
      });
    }
  }

  componentWillUnmount() {
    Object(external_lodash_["invoke"])(this.initRequest, ['abort']);
    Object(external_lodash_["invoke"])(this.searchRequest, ['abort']);
  }

  componentDidUpdate(prevProps) {
    if (prevProps.terms !== this.props.terms) {
      this.updateSelectedTerms(this.props.terms);
    }
  }

  fetchTerms(params = {}) {
    const {
      taxonomy
    } = this.props;
    const query = { ...flat_term_selector_DEFAULT_QUERY,
      ...params
    };
    const request = external_wp_apiFetch_default()({
      path: Object(external_wp_url_["addQueryArgs"])(`/wp/v2/${taxonomy.rest_base}`, query)
    });
    request.then(unescapeTerms).then(terms => {
      this.setState(state => ({
        availableTerms: state.availableTerms.concat(terms.filter(term => !Object(external_lodash_["find"])(state.availableTerms, availableTerm => availableTerm.id === term.id)))
      }));
      this.updateSelectedTerms(this.props.terms);
    });
    return request;
  }

  updateSelectedTerms(terms = []) {
    const selectedTerms = terms.reduce((accumulator, termId) => {
      const termObject = Object(external_lodash_["find"])(this.state.availableTerms, term => term.id === termId);

      if (termObject) {
        accumulator.push(termObject.name);
      }

      return accumulator;
    }, []);
    this.setState({
      selectedTerms
    });
  }

  findOrCreateTerm(termName) {
    const {
      taxonomy
    } = this.props;
    const termNameEscaped = Object(external_lodash_["escape"])(termName); // Tries to create a term or fetch it if it already exists.

    return external_wp_apiFetch_default()({
      path: `/wp/v2/${taxonomy.rest_base}`,
      method: 'POST',
      data: {
        name: termNameEscaped
      }
    }).catch(error => {
      const errorCode = error.code;

      if (errorCode === 'term_exists') {
        // If the terms exist, fetch it instead of creating a new one.
        this.addRequest = external_wp_apiFetch_default()({
          path: Object(external_wp_url_["addQueryArgs"])(`/wp/v2/${taxonomy.rest_base}`, { ...flat_term_selector_DEFAULT_QUERY,
            search: termNameEscaped
          })
        }).then(unescapeTerms);
        return this.addRequest.then(searchResult => {
          return Object(external_lodash_["find"])(searchResult, result => isSameTermName(result.name, termName));
        });
      }

      return Promise.reject(error);
    }).then(unescapeTerm);
  }

  onChange(termNames) {
    const uniqueTerms = Object(external_lodash_["uniqBy"])(termNames, term => term.toLowerCase());
    this.setState({
      selectedTerms: uniqueTerms
    });
    const newTermNames = uniqueTerms.filter(termName => !Object(external_lodash_["find"])(this.state.availableTerms, term => isSameTermName(term.name, termName)));

    if (newTermNames.length === 0) {
      return this.props.onUpdateTerms(termNamesToIds(uniqueTerms, this.state.availableTerms), this.props.taxonomy.rest_base);
    }

    Promise.all(newTermNames.map(this.findOrCreateTerm)).then(newTerms => {
      const newAvailableTerms = this.state.availableTerms.concat(newTerms);
      this.setState({
        availableTerms: newAvailableTerms
      });
      return this.props.onUpdateTerms(termNamesToIds(uniqueTerms, newAvailableTerms), this.props.taxonomy.rest_base);
    });
  }

  searchTerms(search = '') {
    Object(external_lodash_["invoke"])(this.searchRequest, ['abort']);

    if (search.length >= 3) {
      this.searchRequest = this.fetchTerms({
        search
      });
    }
  }

  appendTerm(newTerm) {
    const {
      onUpdateTerms,
      taxonomy,
      terms = [],
      slug,
      speak
    } = this.props;

    if (terms.includes(newTerm.id)) {
      return;
    }

    const newTerms = [...terms, newTerm.id];
    const termAddedMessage = Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: term name. */
    Object(external_wp_i18n_["_x"])('%s added', 'term'), Object(external_lodash_["get"])(taxonomy, ['labels', 'singular_name'], slug === 'post_tag' ? Object(external_wp_i18n_["__"])('Tag') : Object(external_wp_i18n_["__"])('Term')));
    speak(termAddedMessage, 'assertive');
    this.setState({
      availableTerms: [...this.state.availableTerms, newTerm]
    });
    onUpdateTerms(newTerms, taxonomy.rest_base);
  }

  render() {
    const {
      slug,
      taxonomy,
      hasAssignAction
    } = this.props;

    if (!hasAssignAction) {
      return null;
    }

    const {
      loading,
      availableTerms,
      selectedTerms
    } = this.state;
    const termNames = availableTerms.map(term => term.name);
    const newTermLabel = Object(external_lodash_["get"])(taxonomy, ['labels', 'add_new_item'], slug === 'post_tag' ? Object(external_wp_i18n_["__"])('Add new tag') : Object(external_wp_i18n_["__"])('Add new Term'));
    const singularName = Object(external_lodash_["get"])(taxonomy, ['labels', 'singular_name'], slug === 'post_tag' ? Object(external_wp_i18n_["__"])('Tag') : Object(external_wp_i18n_["__"])('Term'));
    const termAddedLabel = Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: term name. */
    Object(external_wp_i18n_["_x"])('%s added', 'term'), singularName);
    const termRemovedLabel = Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: term name. */
    Object(external_wp_i18n_["_x"])('%s removed', 'term'), singularName);
    const removeTermLabel = Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: term name. */
    Object(external_wp_i18n_["_x"])('Remove %s', 'term'), singularName);
    return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["FormTokenField"], {
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
    }), Object(external_wp_element_["createElement"])(MostUsedTerms, {
      taxonomy: taxonomy,
      onSelect: this.appendTerm
    }));
  }

}

/* harmony default export */ var flat_term_selector = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])((select, {
  slug
}) => {
  const {
    getCurrentPost
  } = select(store);
  const {
    getTaxonomy
  } = select(external_wp_coreData_["store"]);
  const taxonomy = getTaxonomy(slug);
  return {
    hasCreateAction: taxonomy ? Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-create-' + taxonomy.rest_base], false) : false,
    hasAssignAction: taxonomy ? Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-assign-' + taxonomy.rest_base], false) : false,
    terms: taxonomy ? select(store).getEditedPostAttribute(taxonomy.rest_base) : [],
    taxonomy
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => {
  return {
    onUpdateTerms(terms, restBase) {
      dispatch(store).editPost({
        [restBase]: terms
      });
    }

  };
}), external_wp_components_["withSpokenMessages"], Object(external_wp_components_["withFilters"])('editor.PostTaxonomyType'))(flat_term_selector_FlatTermSelector));

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



const TagsPanel = () => {
  const panelBodyTitle = [Object(external_wp_i18n_["__"])('Suggestion:'), Object(external_wp_element_["createElement"])("span", {
    className: "editor-post-publish-panel__link",
    key: "label"
  }, Object(external_wp_i18n_["__"])('Add tags'))];
  return Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    initialOpen: false,
    title: panelBodyTitle
  }, Object(external_wp_element_["createElement"])("p", null, Object(external_wp_i18n_["__"])('Tags help users and search engines navigate your site and find your content. Add a few keywords to describe your post.')), Object(external_wp_element_["createElement"])(flat_term_selector, {
    slug: 'post_tag'
  }));
};

class maybe_tags_panel_MaybeTagsPanel extends external_wp_element_["Component"] {
  constructor(props) {
    super(props);
    this.state = {
      hadTagsWhenOpeningThePanel: props.hasTags
    };
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


  render() {
    if (!this.state.hadTagsWhenOpeningThePanel) {
      return Object(external_wp_element_["createElement"])(TagsPanel, null);
    }

    return null;
  }

}

/* harmony default export */ var maybe_tags_panel = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])(select => {
  const postType = select('core/editor').getCurrentPostType();
  const tagsTaxonomy = select('core').getTaxonomy('post_tag');
  const tags = tagsTaxonomy && select('core/editor').getEditedPostAttribute(tagsTaxonomy.rest_base);
  return {
    areTagsFetched: tagsTaxonomy !== undefined,
    isPostTypeSupported: tagsTaxonomy && Object(external_lodash_["some"])(tagsTaxonomy.types, type => type === postType),
    hasTags: tags && tags.length
  };
}), Object(external_wp_compose_["ifCondition"])(({
  areTagsFetched,
  isPostTypeSupported
}) => isPostTypeSupported && areTagsFetched))(maybe_tags_panel_MaybeTagsPanel));

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



const getSuggestion = (supportedFormats, suggestedPostFormat) => {
  const formats = POST_FORMATS.filter(format => Object(external_lodash_["includes"])(supportedFormats, format.id));
  return Object(external_lodash_["find"])(formats, format => format.id === suggestedPostFormat);
};

const PostFormatSuggestion = ({
  suggestedPostFormat,
  suggestionText,
  onUpdatePostFormat
}) => Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
  isLink: true,
  onClick: () => onUpdatePostFormat(suggestedPostFormat)
}, suggestionText);

function PostFormatPanel() {
  const {
    currentPostFormat,
    suggestion
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditedPostAttribute,
      getSuggestedPostFormat
    } = select('core/editor');
    const supportedFormats = Object(external_lodash_["get"])(select('core').getThemeSupports(), ['formats'], []);
    return {
      currentPostFormat: getEditedPostAttribute('format'),
      suggestion: getSuggestion(supportedFormats, getSuggestedPostFormat())
    };
  }, []);
  const {
    editPost
  } = Object(external_wp_data_["useDispatch"])('core/editor');

  const onUpdatePostFormat = format => editPost({
    format
  });

  const panelBodyTitle = [Object(external_wp_i18n_["__"])('Suggestion:'), Object(external_wp_element_["createElement"])("span", {
    className: "editor-post-publish-panel__link",
    key: "label"
  }, Object(external_wp_i18n_["__"])('Use a post format'))];

  if (!suggestion || suggestion.id === currentPostFormat) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    initialOpen: false,
    title: panelBodyTitle
  }, Object(external_wp_element_["createElement"])("p", null, Object(external_wp_i18n_["__"])('Your theme uses post formats to highlight different kinds of content, like images or videos. Apply a post format to see this special styling.')), Object(external_wp_element_["createElement"])("p", null, Object(external_wp_element_["createElement"])(PostFormatSuggestion, {
    onUpdatePostFormat: onUpdatePostFormat,
    suggestedPostFormat: suggestion.id,
    suggestionText: Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: post format */
    Object(external_wp_i18n_["__"])('Apply the "%1$s" format.'), suggestion.caption)
  })));
}

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
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      isResolving
    } = select('core/data');
    const {
      getCurrentPost,
      isEditedPostBeingScheduled
    } = select('core/editor');
    const {
      getEntityRecord
    } = select('core');
    const siteData = getEntityRecord('root', '__unstableBase', undefined) || {};
    return {
      hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
      isBeingScheduled: isEditedPostBeingScheduled(),
      isRequestingSiteIcon: isResolving('core', 'getEntityRecord', ['root', '__unstableBase', undefined]),
      siteIconUrl: siteData.site_icon_url,
      siteTitle: siteData.name,
      siteHome: siteData.home && Object(external_wp_url_["filterURLForDisplay"])(siteData.home)
    };
  }, []);
  let siteIcon = Object(external_wp_element_["createElement"])(external_wp_components_["Icon"], {
    className: "components-site-icon",
    size: "36px",
    icon: wordpress["a" /* default */]
  });

  if (siteIconUrl) {
    siteIcon = Object(external_wp_element_["createElement"])("img", {
      alt: Object(external_wp_i18n_["__"])('Site Icon'),
      className: "components-site-icon",
      src: siteIconUrl
    });
  }

  if (isRequestingSiteIcon) {
    siteIcon = null;
  }

  let prePublishTitle, prePublishBodyText;

  if (!hasPublishAction) {
    prePublishTitle = Object(external_wp_i18n_["__"])('Are you ready to submit for review?');
    prePublishBodyText = Object(external_wp_i18n_["__"])('When you’re ready, submit your work for review, and an Editor will be able to approve it for you.');
  } else if (isBeingScheduled) {
    prePublishTitle = Object(external_wp_i18n_["__"])('Are you ready to schedule?');
    prePublishBodyText = Object(external_wp_i18n_["__"])('Your work will be published at the specified date and time.');
  } else {
    prePublishTitle = Object(external_wp_i18n_["__"])('Are you ready to publish?');
    prePublishBodyText = Object(external_wp_i18n_["__"])('Double-check your settings before publishing.');
  }

  return Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-publish-panel__prepublish"
  }, Object(external_wp_element_["createElement"])("div", null, Object(external_wp_element_["createElement"])("strong", null, prePublishTitle)), Object(external_wp_element_["createElement"])("p", null, prePublishBodyText), Object(external_wp_element_["createElement"])("div", {
    className: "components-site-card"
  }, siteIcon, Object(external_wp_element_["createElement"])("div", {
    className: "components-site-info"
  }, Object(external_wp_element_["createElement"])("span", {
    className: "components-site-name"
  }, siteTitle || Object(external_wp_i18n_["__"])('(Untitled)')), Object(external_wp_element_["createElement"])("span", {
    className: "components-site-home"
  }, siteHome))), hasPublishAction && Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    initialOpen: false,
    title: [Object(external_wp_i18n_["__"])('Visibility:'), Object(external_wp_element_["createElement"])("span", {
      className: "editor-post-publish-panel__link",
      key: "label"
    }, Object(external_wp_element_["createElement"])(post_visibility_label, null))]
  }, Object(external_wp_element_["createElement"])(post_visibility, null)), Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    initialOpen: false,
    title: [Object(external_wp_i18n_["__"])('Publish:'), Object(external_wp_element_["createElement"])("span", {
      className: "editor-post-publish-panel__link",
      key: "label"
    }, Object(external_wp_element_["createElement"])(post_schedule_label, null))]
  }, Object(external_wp_element_["createElement"])(PostSchedule, null))), Object(external_wp_element_["createElement"])(PostFormatPanel, null), Object(external_wp_element_["createElement"])(maybe_tags_panel, null), children);
}

/* harmony default export */ var prepublish = (PostPublishPanelPrepublish);

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


const POSTNAME = '%postname%';
/**
 * Returns URL for a future post.
 *
 * @param {Object} post         Post object.
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

  return post.permalink_template;
};

function postpublish_CopyButton({
  text,
  onCopy,
  children
}) {
  const ref = Object(external_wp_compose_["useCopyToClipboard"])(text, onCopy);
  return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    isSecondary: true,
    ref: ref
  }, children);
}

class postpublish_PostPublishPanelPostpublish extends external_wp_element_["Component"] {
  constructor() {
    super(...arguments);
    this.state = {
      showCopyConfirmation: false
    };
    this.onCopy = this.onCopy.bind(this);
    this.onSelectInput = this.onSelectInput.bind(this);
    this.postLink = Object(external_wp_element_["createRef"])();
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
    const postLabel = Object(external_lodash_["get"])(postType, ['labels', 'singular_name']);
    const viewPostLabel = Object(external_lodash_["get"])(postType, ['labels', 'view_item']);
    const link = post.status === 'future' ? getFuturePostUrl(post) : post.link;
    const postPublishNonLinkHeader = isScheduled ? Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_i18n_["__"])('is now scheduled. It will go live on'), ' ', Object(external_wp_element_["createElement"])(post_schedule_label, null), ".") : Object(external_wp_i18n_["__"])('is now live.');
    return Object(external_wp_element_["createElement"])("div", {
      className: "post-publish-panel__postpublish"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
      className: "post-publish-panel__postpublish-header"
    }, Object(external_wp_element_["createElement"])("a", {
      ref: this.postLink,
      href: link
    }, Object(external_wp_htmlEntities_["decodeEntities"])(post.title) || Object(external_wp_i18n_["__"])('(no title)')), ' ', postPublishNonLinkHeader), Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], null, Object(external_wp_element_["createElement"])("p", {
      className: "post-publish-panel__postpublish-subheader"
    }, Object(external_wp_element_["createElement"])("strong", null, Object(external_wp_i18n_["__"])('What’s next?'))), Object(external_wp_element_["createElement"])(external_wp_components_["TextControl"], {
      className: "post-publish-panel__postpublish-post-address",
      readOnly: true,
      label: Object(external_wp_i18n_["sprintf"])(
      /* translators: %s: post type singular name */
      Object(external_wp_i18n_["__"])('%s address'), postLabel),
      value: Object(external_wp_url_["safeDecodeURIComponent"])(link),
      onFocus: this.onSelectInput
    }), Object(external_wp_element_["createElement"])("div", {
      className: "post-publish-panel__postpublish-buttons"
    }, !isScheduled && Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      isSecondary: true,
      href: link
    }, viewPostLabel), Object(external_wp_element_["createElement"])(postpublish_CopyButton, {
      text: link,
      onCopy: this.onCopy
    }, this.state.showCopyConfirmation ? Object(external_wp_i18n_["__"])('Copied!') : Object(external_wp_i18n_["__"])('Copy Link')))), children);
  }

}

/* harmony default export */ var postpublish = (Object(external_wp_data_["withSelect"])(select => {
  const {
    getEditedPostAttribute,
    getCurrentPost,
    isCurrentPostScheduled
  } = select('core/editor');
  const {
    getPostType
  } = select('core');
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




class post_publish_panel_PostPublishPanel extends external_wp_element_["Component"] {
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
      forceIsSaving,
      isBeingScheduled,
      isPublished,
      isPublishSidebarEnabled,
      isScheduled,
      isSaving,
      onClose,
      onTogglePublishSidebar,
      PostPublishExtension,
      PrePublishExtension,
      ...additionalProps
    } = this.props;
    const propsForPanel = Object(external_lodash_["omit"])(additionalProps, ['hasPublishAction', 'isDirty', 'isPostTypeViewable']);
    const isPublishedOrScheduled = isPublished || isScheduled && isBeingScheduled;
    const isPrePublish = !isPublishedOrScheduled && !isSaving;
    const isPostPublish = isPublishedOrScheduled && !isSaving;
    return Object(external_wp_element_["createElement"])("div", Object(esm_extends["a" /* default */])({
      className: "editor-post-publish-panel"
    }, propsForPanel), Object(external_wp_element_["createElement"])("div", {
      className: "editor-post-publish-panel__header"
    }, isPostPublish ? Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      onClick: onClose,
      icon: close_small["a" /* default */],
      label: Object(external_wp_i18n_["__"])('Close panel')
    }) : Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("div", {
      className: "editor-post-publish-panel__header-publish-button"
    }, Object(external_wp_element_["createElement"])(post_publish_button, {
      focusOnMount: true,
      onSubmit: this.onSubmit,
      forceIsDirty: forceIsDirty,
      forceIsSaving: forceIsSaving
    })), Object(external_wp_element_["createElement"])("div", {
      className: "editor-post-publish-panel__header-cancel-button"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      onClick: onClose,
      isSecondary: true
    }, Object(external_wp_i18n_["__"])('Cancel'))))), Object(external_wp_element_["createElement"])("div", {
      className: "editor-post-publish-panel__content"
    }, isPrePublish && Object(external_wp_element_["createElement"])(prepublish, null, PrePublishExtension && Object(external_wp_element_["createElement"])(PrePublishExtension, null)), isPostPublish && Object(external_wp_element_["createElement"])(postpublish, {
      focusOnMount: true
    }, PostPublishExtension && Object(external_wp_element_["createElement"])(PostPublishExtension, null)), isSaving && Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null)), Object(external_wp_element_["createElement"])("div", {
      className: "editor-post-publish-panel__footer"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["CheckboxControl"], {
      label: Object(external_wp_i18n_["__"])('Always show pre-publish checks.'),
      checked: isPublishSidebarEnabled,
      onChange: onTogglePublishSidebar
    })));
  }

}
/* harmony default export */ var post_publish_panel = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const {
    getPostType
  } = select('core');
  const {
    getCurrentPost,
    getEditedPostAttribute,
    isCurrentPostPublished,
    isCurrentPostScheduled,
    isEditedPostBeingScheduled,
    isEditedPostDirty,
    isSavingPost
  } = select('core/editor');
  const {
    isPublishSidebarEnabled
  } = select('core/editor');
  const postType = getPostType(getEditedPostAttribute('type'));
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
}), Object(external_wp_data_["withDispatch"])((dispatch, {
  isPublishSidebarEnabled
}) => {
  const {
    disablePublishSidebar,
    enablePublishSidebar
  } = dispatch('core/editor');
  return {
    onTogglePublishSidebar: () => {
      if (isPublishSidebarEnabled) {
        disablePublishSidebar();
      } else {
        enablePublishSidebar();
      }
    }
  };
}), external_wp_components_["withFocusReturn"], external_wp_components_["withConstrainedTabbing"]])(post_publish_panel_PostPublishPanel));

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
var build_module_icon = __webpack_require__("iClF");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/cloud.js
var cloud = __webpack_require__("LS+F");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js
var library_check = __webpack_require__("RMJe");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/cloud-upload.js
var cloud_upload = __webpack_require__("B/ly");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-switch-to-draft-button/index.js


/**
 * WordPress dependencies
 */





function PostSwitchToDraftButton({
  isSaving,
  isPublished,
  isScheduled,
  onClick
}) {
  const isMobileViewport = Object(external_wp_compose_["useViewportMatch"])('small', '<');

  if (!isPublished && !isScheduled) {
    return null;
  }

  const onSwitch = () => {
    let alertMessage;

    if (isPublished) {
      alertMessage = Object(external_wp_i18n_["__"])('Are you sure you want to unpublish this post?');
    } else if (isScheduled) {
      alertMessage = Object(external_wp_i18n_["__"])('Are you sure you want to unschedule this post?');
    } // eslint-disable-next-line no-alert


    if (window.confirm(alertMessage)) {
      onClick();
    }
  };

  return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "editor-post-switch-to-draft",
    onClick: onSwitch,
    disabled: isSaving,
    isTertiary: true
  }, isMobileViewport ? Object(external_wp_i18n_["__"])('Draft') : Object(external_wp_i18n_["__"])('Switch to draft'));
}

/* harmony default export */ var post_switch_to_draft_button = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const {
    isSavingPost,
    isCurrentPostPublished,
    isCurrentPostScheduled
  } = select('core/editor');
  return {
    isSaving: isSavingPost(),
    isPublished: isCurrentPostPublished(),
    isScheduled: isCurrentPostScheduled()
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    editPost,
    savePost
  } = dispatch('core/editor');
  return {
    onClick: () => {
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
 * Component showing whether the post is saved or not and providing save
 * buttons.
 *
 * @param {Object} props               Component props.
 * @param {?boolean} props.forceIsDirty  Whether to force the post to be marked
 * as dirty.
 * @param {?boolean} props.forceIsSaving Whether to force the post to be marked
 * as being saved.
 * @param {?boolean} props.showIconLabels Whether interface buttons show labels instead of icons
 * @return {import('@wordpress/element').WPComponent} The component.
 */

function PostSavedState({
  forceIsDirty,
  forceIsSaving,
  showIconLabels = false
}) {
  const [forceSavedMessage, setForceSavedMessage] = Object(external_wp_element_["useState"])(false);
  const isLargeViewport = Object(external_wp_compose_["useViewportMatch"])('small');
  const {
    isAutosaving,
    isDirty,
    isNew,
    isPending,
    isPublished,
    isSaveable,
    isSaving,
    isScheduled,
    hasPublishAction
  } = Object(external_wp_data_["useSelect"])(select => {
    var _getCurrentPost$_link, _getCurrentPost, _getCurrentPost$_link2;

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
    } = select('core/editor');
    return {
      isAutosaving: isAutosavingPost(),
      isDirty: forceIsDirty || isEditedPostDirty(),
      isNew: isEditedPostNew(),
      isPending: 'pending' === getEditedPostAttribute('status'),
      isPublished: isCurrentPostPublished(),
      isSaving: forceIsSaving || isSavingPost(),
      isSaveable: isEditedPostSaveable(),
      isScheduled: isCurrentPostScheduled(),
      hasPublishAction: (_getCurrentPost$_link = (_getCurrentPost = getCurrentPost()) === null || _getCurrentPost === void 0 ? void 0 : (_getCurrentPost$_link2 = _getCurrentPost._links) === null || _getCurrentPost$_link2 === void 0 ? void 0 : _getCurrentPost$_link2['wp:action-publish']) !== null && _getCurrentPost$_link !== void 0 ? _getCurrentPost$_link : false
    };
  }, [forceIsDirty, forceIsSaving]);
  const {
    savePost
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  const wasSaving = Object(external_wp_compose_["usePrevious"])(isSaving);
  Object(external_wp_element_["useEffect"])(() => {
    let timeoutId;

    if (wasSaving && !isSaving) {
      setForceSavedMessage(true);
      timeoutId = setTimeout(() => {
        setForceSavedMessage(false);
      }, 1000);
    }

    return () => clearTimeout(timeoutId);
  }, [isSaving]);

  if (isSaving) {
    // TODO: Classes generation should be common across all return
    // paths of this function, including proper naming convention for
    // the "Save Draft" button.
    const classes = classnames_default()('editor-post-saved-state', 'is-saving', Object(external_wp_components_["__unstableGetAnimateClassName"])({
      type: 'loading'
    }), {
      'is-autosaving': isAutosaving
    });
    return Object(external_wp_element_["createElement"])("span", {
      className: classes
    }, Object(external_wp_element_["createElement"])(build_module_icon["a" /* default */], {
      icon: cloud["a" /* default */]
    }), isAutosaving ? Object(external_wp_i18n_["__"])('Autosaving') : Object(external_wp_i18n_["__"])('Saving'));
  }

  if (isPublished || isScheduled) {
    return Object(external_wp_element_["createElement"])(post_switch_to_draft_button, null);
  }

  if (!isSaveable) {
    return null;
  }

  if (forceSavedMessage || !isNew && !isDirty) {
    return Object(external_wp_element_["createElement"])("span", {
      className: "editor-post-saved-state is-saved"
    }, Object(external_wp_element_["createElement"])(build_module_icon["a" /* default */], {
      icon: library_check["a" /* default */]
    }), Object(external_wp_i18n_["__"])('Saved'));
  } // Once the post has been submitted for review this button
  // is not needed for the contributor role.


  if (!hasPublishAction && isPending) {
    return null;
  }
  /* translators: button label text should, if possible, be under 16 characters. */


  const label = isPending ? Object(external_wp_i18n_["__"])('Save as pending') : Object(external_wp_i18n_["__"])('Save draft');
  /* translators: button label text should, if possible, be under 16 characters. */

  const shortLabel = Object(external_wp_i18n_["__"])('Save');

  if (!isLargeViewport) {
    return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      className: "editor-post-save-draft",
      label: label,
      onClick: () => savePost(),
      shortcut: external_wp_keycodes_["displayShortcut"].primary('s'),
      icon: cloud_upload["a" /* default */]
    }, showIconLabels && shortLabel);
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "editor-post-save-draft",
    onClick: () => savePost(),
    shortcut: external_wp_keycodes_["displayShortcut"].primary('s'),
    isTertiary: true
  }, label);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-schedule/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PostScheduleCheck({
  hasPublishAction,
  children
}) {
  if (!hasPublishAction) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_schedule_check = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const {
    getCurrentPost,
    getCurrentPostType
  } = select('core/editor');
  return {
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    postType: getCurrentPostType()
  };
})])(PostScheduleCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-slug/check.js


/**
 * Internal dependencies
 */

function PostSlugCheck({
  children
}) {
  return Object(external_wp_element_["createElement"])(post_type_support_check, {
    supportKeys: "slug"
  }, children);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-slug/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



class post_slug_PostSlug extends external_wp_element_["Component"] {
  constructor({
    postSlug,
    postTitle,
    postID
  }) {
    super(...arguments);
    this.state = {
      editedSlug: Object(external_wp_url_["safeDecodeURIComponent"])(postSlug) || cleanForSlug(postTitle) || postID
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
    const editedSlug = cleanForSlug(value);

    if (editedSlug === postSlug) {
      return;
    }

    onUpdateSlug(editedSlug);
  }

  render() {
    const {
      instanceId
    } = this.props;
    const {
      editedSlug
    } = this.state;
    const inputId = 'editor-post-slug-' + instanceId;
    return Object(external_wp_element_["createElement"])(PostSlugCheck, null, Object(external_wp_element_["createElement"])("label", {
      htmlFor: inputId
    }, Object(external_wp_i18n_["__"])('Slug')), Object(external_wp_element_["createElement"])("input", {
      type: "text",
      id: inputId,
      value: editedSlug,
      onChange: event => this.setState({
        editedSlug: event.target.value
      }),
      onBlur: this.setSlug,
      className: "editor-post-slug__input"
    }));
  }

}
/* harmony default export */ var post_slug = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const {
    getCurrentPost,
    getEditedPostAttribute
  } = select('core/editor');
  const {
    id
  } = getCurrentPost();
  return {
    postSlug: getEditedPostAttribute('slug'),
    postTitle: getEditedPostAttribute('title'),
    postID: id
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    editPost
  } = dispatch('core/editor');
  return {
    onUpdateSlug(slug) {
      editPost({
        slug
      });
    }

  };
}), external_wp_compose_["withInstanceId"]])(post_slug_PostSlug));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-sticky/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PostStickyCheck({
  hasStickyAction,
  postType,
  children
}) {
  if (postType !== 'post' || !hasStickyAction) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_sticky_check = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const post = select('core/editor').getCurrentPost();
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


function PostSticky({
  onUpdateSticky,
  postSticky = false
}) {
  return Object(external_wp_element_["createElement"])(post_sticky_check, null, Object(external_wp_element_["createElement"])(external_wp_components_["CheckboxControl"], {
    label: Object(external_wp_i18n_["__"])('Stick to the top of the blog'),
    checked: postSticky,
    onChange: () => onUpdateSticky(!postSticky)
  }));
}
/* harmony default export */ var post_sticky = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  return {
    postSticky: select('core/editor').getEditedPostAttribute('sticky')
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => {
  return {
    onUpdateSticky(postSticky) {
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

const hierarchical_term_selector_DEFAULT_QUERY = {
  per_page: -1,
  orderby: 'name',
  order: 'asc',
  _fields: 'id,name,parent'
};
const MIN_TERMS_COUNT_FOR_FILTER = 8;

class hierarchical_term_selector_HierarchicalTermSelector extends external_wp_element_["Component"] {
  constructor() {
    super(...arguments);
    this.findTerm = this.findTerm.bind(this);
    this.onChange = this.onChange.bind(this);
    this.onChangeFormName = this.onChangeFormName.bind(this);
    this.onChangeFormParent = this.onChangeFormParent.bind(this);
    this.onAddTerm = this.onAddTerm.bind(this);
    this.onToggleForm = this.onToggleForm.bind(this);
    this.setFilterValue = this.setFilterValue.bind(this);
    this.sortBySelected = this.sortBySelected.bind(this);
    this.state = {
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
  }

  onChange(termId) {
    const {
      onUpdateTerms,
      terms = [],
      taxonomy
    } = this.props;
    const hasTerm = terms.indexOf(termId) !== -1;
    const newTerms = hasTerm ? Object(external_lodash_["without"])(terms, termId) : [...terms, termId];
    onUpdateTerms(newTerms, taxonomy.rest_base);
  }

  onChangeFormName(event) {
    const newValue = event.target.value.trim() === '' ? '' : event.target.value;
    this.setState({
      formName: newValue
    });
  }

  onChangeFormParent(newParent) {
    this.setState({
      formParent: newParent
    });
  }

  onToggleForm() {
    this.setState(state => ({
      showForm: !state.showForm
    }));
  }

  findTerm(terms, parent, name) {
    return Object(external_lodash_["find"])(terms, term => {
      return (!term.parent && !parent || parseInt(term.parent) === parseInt(parent)) && term.name.toLowerCase() === name.toLowerCase();
    });
  }

  onAddTerm(event) {
    event.preventDefault();
    const {
      onUpdateTerms,
      taxonomy,
      terms,
      slug
    } = this.props;
    const {
      formName,
      formParent,
      adding,
      availableTerms
    } = this.state;

    if (formName === '' || adding) {
      return;
    } // check if the term we are adding already exists


    const existingTerm = this.findTerm(availableTerms, formParent, formName);

    if (existingTerm) {
      // if the term we are adding exists but is not selected select it
      if (!Object(external_lodash_["some"])(terms, term => term === existingTerm.id)) {
        onUpdateTerms([...terms, existingTerm.id], taxonomy.rest_base);
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
    this.addRequest = external_wp_apiFetch_default()({
      path: `/wp/v2/${taxonomy.rest_base}`,
      method: 'POST',
      data: {
        name: formName,
        parent: formParent ? formParent : undefined
      }
    }); // Tries to create a term or fetch it if it already exists

    const findOrCreatePromise = this.addRequest.catch(error => {
      const errorCode = error.code;

      if (errorCode === 'term_exists') {
        // search the new category created since last fetch
        this.addRequest = external_wp_apiFetch_default()({
          path: Object(external_wp_url_["addQueryArgs"])(`/wp/v2/${taxonomy.rest_base}`, { ...hierarchical_term_selector_DEFAULT_QUERY,
            parent: formParent || 0,
            search: formName
          })
        });
        return this.addRequest.then(searchResult => {
          return this.findTerm(searchResult, formParent, formName);
        });
      }

      return Promise.reject(error);
    });
    findOrCreatePromise.then(term => {
      const hasTerm = !!Object(external_lodash_["find"])(this.state.availableTerms, availableTerm => availableTerm.id === term.id);
      const newAvailableTerms = hasTerm ? this.state.availableTerms : [term, ...this.state.availableTerms];
      const termAddedMessage = Object(external_wp_i18n_["sprintf"])(
      /* translators: %s: taxonomy name */
      Object(external_wp_i18n_["_x"])('%s added', 'term'), Object(external_lodash_["get"])(this.props.taxonomy, ['labels', 'singular_name'], slug === 'category' ? Object(external_wp_i18n_["__"])('Category') : Object(external_wp_i18n_["__"])('Term')));
      this.props.speak(termAddedMessage, 'assertive');
      this.addRequest = null;
      this.setState({
        adding: false,
        formName: '',
        formParent: '',
        availableTerms: newAvailableTerms,
        availableTermsTree: this.sortBySelected(buildTermsTree(newAvailableTerms))
      });
      onUpdateTerms([...terms, term.id], taxonomy.rest_base);
    }, xhr => {
      if (xhr.statusText === 'abort') {
        return;
      }

      this.addRequest = null;
      this.setState({
        adding: false
      });
    });
  }

  componentDidMount() {
    this.fetchTerms();
  }

  componentWillUnmount() {
    Object(external_lodash_["invoke"])(this.fetchRequest, ['abort']);
    Object(external_lodash_["invoke"])(this.addRequest, ['abort']);
  }

  componentDidUpdate(prevProps) {
    if (this.props.taxonomy !== prevProps.taxonomy) {
      this.fetchTerms();
    }
  }

  fetchTerms() {
    const {
      taxonomy
    } = this.props;

    if (!taxonomy) {
      return;
    }

    this.fetchRequest = external_wp_apiFetch_default()({
      path: Object(external_wp_url_["addQueryArgs"])(`/wp/v2/${taxonomy.rest_base}`, hierarchical_term_selector_DEFAULT_QUERY)
    });
    this.fetchRequest.then(terms => {
      // resolve
      const availableTermsTree = this.sortBySelected(buildTermsTree(terms));
      this.fetchRequest = null;
      this.setState({
        loading: false,
        availableTermsTree,
        availableTerms: terms
      });
    }, xhr => {
      // reject
      if (xhr.statusText === 'abort') {
        return;
      }

      this.fetchRequest = null;
      this.setState({
        loading: false
      });
    });
  }

  sortBySelected(termsTree) {
    const {
      terms
    } = this.props;

    const treeHasSelection = termTree => {
      if (terms.indexOf(termTree.id) !== -1) {
        return true;
      }

      if (undefined === termTree.children) {
        return false;
      }

      const anyChildIsSelected = termTree.children.map(treeHasSelection).filter(child => child).length > 0;

      if (anyChildIsSelected) {
        return true;
      }

      return false;
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

    termsTree.sort(termOrChildIsSelected);
    return termsTree;
  }

  setFilterValue(event) {
    const {
      availableTermsTree
    } = this.state;
    const filterValue = event.target.value;
    const filteredTermsTree = availableTermsTree.map(this.getFilterMatcher(filterValue)).filter(term => term);

    const getResultCount = terms => {
      let count = 0;

      for (let i = 0; i < terms.length; i++) {
        count++;

        if (undefined !== terms[i].children) {
          count += getResultCount(terms[i].children);
        }
      }

      return count;
    };

    this.setState({
      filterValue,
      filteredTermsTree
    });
    const resultCount = getResultCount(filteredTermsTree);
    const resultsFoundMessage = Object(external_wp_i18n_["sprintf"])(
    /* translators: %d: number of results */
    Object(external_wp_i18n_["_n"])('%d result found.', '%d results found.', resultCount), resultCount);
    this.props.debouncedSpeak(resultsFoundMessage, 'assertive');
  }

  getFilterMatcher(filterValue) {
    const matchTermsForFilter = originalTerm => {
      if ('' === filterValue) {
        return originalTerm;
      } // Shallow clone, because we'll be filtering the term's children and
      // don't want to modify the original term.


      const term = { ...originalTerm
      }; // Map and filter the children, recursive so we deal with grandchildren
      // and any deeper levels.

      if (term.children.length > 0) {
        term.children = term.children.map(matchTermsForFilter).filter(child => child);
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

  renderTerms(renderedTerms) {
    const {
      terms = []
    } = this.props;
    return renderedTerms.map(term => {
      return Object(external_wp_element_["createElement"])("div", {
        key: term.id,
        className: "editor-post-taxonomies__hierarchical-terms-choice"
      }, Object(external_wp_element_["createElement"])(external_wp_components_["CheckboxControl"], {
        checked: terms.indexOf(term.id) !== -1,
        onChange: () => {
          const termId = parseInt(term.id, 10);
          this.onChange(termId);
        },
        label: Object(external_lodash_["unescape"])(term.name)
      }), !!term.children.length && Object(external_wp_element_["createElement"])("div", {
        className: "editor-post-taxonomies__hierarchical-terms-subchoices"
      }, this.renderTerms(term.children)));
    });
  }

  render() {
    const {
      slug,
      taxonomy,
      instanceId,
      hasCreateAction,
      hasAssignAction
    } = this.props;

    if (!hasAssignAction) {
      return null;
    }

    const {
      availableTermsTree,
      availableTerms,
      filteredTermsTree,
      formName,
      formParent,
      loading,
      showForm,
      filterValue
    } = this.state;

    const labelWithFallback = (labelProperty, fallbackIsCategory, fallbackIsNotCategory) => Object(external_lodash_["get"])(taxonomy, ['labels', labelProperty], slug === 'category' ? fallbackIsCategory : fallbackIsNotCategory);

    const newTermButtonLabel = labelWithFallback('add_new_item', Object(external_wp_i18n_["__"])('Add new category'), Object(external_wp_i18n_["__"])('Add new term'));
    const newTermLabel = labelWithFallback('new_item_name', Object(external_wp_i18n_["__"])('Add new category'), Object(external_wp_i18n_["__"])('Add new term'));
    const parentSelectLabel = labelWithFallback('parent_item', Object(external_wp_i18n_["__"])('Parent Category'), Object(external_wp_i18n_["__"])('Parent Term'));
    const noParentOption = `— ${parentSelectLabel} —`;
    const newTermSubmitLabel = newTermButtonLabel;
    const inputId = `editor-post-taxonomies__hierarchical-terms-input-${instanceId}`;
    const filterInputId = `editor-post-taxonomies__hierarchical-terms-filter-${instanceId}`;
    const filterLabel = Object(external_lodash_["get"])(this.props.taxonomy, ['labels', 'search_items'], Object(external_wp_i18n_["__"])('Search Terms'));
    const groupLabel = Object(external_lodash_["get"])(this.props.taxonomy, ['name'], Object(external_wp_i18n_["__"])('Terms'));
    const showFilter = availableTerms.length >= MIN_TERMS_COUNT_FOR_FILTER;
    return [showFilter && Object(external_wp_element_["createElement"])("label", {
      key: "filter-label",
      htmlFor: filterInputId
    }, filterLabel), showFilter && Object(external_wp_element_["createElement"])("input", {
      type: "search",
      id: filterInputId,
      value: filterValue,
      onChange: this.setFilterValue,
      className: "editor-post-taxonomies__hierarchical-terms-filter",
      key: "term-filter-input"
    }), Object(external_wp_element_["createElement"])("div", {
      className: "editor-post-taxonomies__hierarchical-terms-list",
      key: "term-list",
      tabIndex: "0",
      role: "group",
      "aria-label": groupLabel
    }, this.renderTerms('' !== filterValue ? filteredTermsTree : availableTermsTree)), !loading && hasCreateAction && Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      key: "term-add-button",
      onClick: this.onToggleForm,
      className: "editor-post-taxonomies__hierarchical-terms-add",
      "aria-expanded": showForm,
      isLink: true
    }, newTermButtonLabel), showForm && Object(external_wp_element_["createElement"])("form", {
      onSubmit: this.onAddTerm,
      key: "hierarchical-terms-form"
    }, Object(external_wp_element_["createElement"])("label", {
      htmlFor: inputId,
      className: "editor-post-taxonomies__hierarchical-terms-label"
    }, newTermLabel), Object(external_wp_element_["createElement"])("input", {
      type: "text",
      id: inputId,
      className: "editor-post-taxonomies__hierarchical-terms-input",
      value: formName,
      onChange: this.onChangeFormName,
      required: true
    }), !!availableTerms.length && Object(external_wp_element_["createElement"])(external_wp_components_["TreeSelect"], {
      label: parentSelectLabel,
      noOptionLabel: noParentOption,
      onChange: this.onChangeFormParent,
      selectedId: formParent,
      tree: availableTermsTree
    }), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      isSecondary: true,
      type: "submit",
      className: "editor-post-taxonomies__hierarchical-terms-submit"
    }, newTermSubmitLabel))];
  }

}

/* harmony default export */ var hierarchical_term_selector = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])((select, {
  slug
}) => {
  const {
    getCurrentPost
  } = select('core/editor');
  const {
    getTaxonomy
  } = select('core');
  const taxonomy = getTaxonomy(slug);
  return {
    hasCreateAction: taxonomy ? Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-create-' + taxonomy.rest_base], false) : false,
    hasAssignAction: taxonomy ? Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-assign-' + taxonomy.rest_base], false) : false,
    terms: taxonomy ? select('core/editor').getEditedPostAttribute(taxonomy.rest_base) : [],
    taxonomy
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  onUpdateTerms(terms, restBase) {
    dispatch('core/editor').editPost({
      [restBase]: terms
    });
  }

})), external_wp_components_["withSpokenMessages"], external_wp_compose_["withInstanceId"], Object(external_wp_components_["withFilters"])('editor.PostTaxonomyType')])(hierarchical_term_selector_HierarchicalTermSelector));

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



function PostTaxonomies({
  postType,
  taxonomies,
  taxonomyWrapper = external_lodash_["identity"]
}) {
  const availableTaxonomies = Object(external_lodash_["filter"])(taxonomies, taxonomy => Object(external_lodash_["includes"])(taxonomy.types, postType));
  const visibleTaxonomies = Object(external_lodash_["filter"])(availableTaxonomies, taxonomy => taxonomy.visibility.show_ui);
  return visibleTaxonomies.map(taxonomy => {
    const TaxonomyComponent = taxonomy.hierarchical ? hierarchical_term_selector : flat_term_selector;
    return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], {
      key: `taxonomy-${taxonomy.slug}`
    }, taxonomyWrapper(Object(external_wp_element_["createElement"])(TaxonomyComponent, {
      slug: taxonomy.slug
    }), taxonomy));
  });
}
/* harmony default export */ var post_taxonomies = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
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



function PostTaxonomiesCheck({
  postType,
  taxonomies,
  children
}) {
  const hasTaxonomies = Object(external_lodash_["some"])(taxonomies, taxonomy => Object(external_lodash_["includes"])(taxonomy.types, postType));

  if (!hasTaxonomies) {
    return null;
  }

  return children;
}
/* harmony default export */ var post_taxonomies_check = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  return {
    postType: select('core/editor').getCurrentPostType(),
    taxonomies: select('core').getTaxonomies({
      per_page: -1
    })
  };
})])(PostTaxonomiesCheck));

// EXTERNAL MODULE: ./node_modules/react-autosize-textarea/lib/index.js
var lib = __webpack_require__("O6Fj");
var lib_default = /*#__PURE__*/__webpack_require__.n(lib);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-text-editor/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







function PostTextEditor() {
  const postContent = Object(external_wp_data_["useSelect"])(select => select('core/editor').getEditedPostContent(), []);
  const {
    editPost,
    resetEditorBlocks
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  const [value, setValue] = Object(external_wp_element_["useState"])(postContent);
  const [isDirty, setIsDirty] = Object(external_wp_element_["useState"])(false);
  const instanceId = Object(external_wp_compose_["useInstanceId"])(PostTextEditor);

  if (!isDirty && value !== postContent) {
    setValue(postContent);
  }
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


  const onChange = event => {
    const newValue = event.target.value;
    editPost({
      content: newValue
    });
    setValue(newValue);
    setIsDirty(true);
  };
  /**
   * Function called when the user has completed their edits, responsible for
   * ensuring that changes, if made, are surfaced to the onPersist prop
   * callback and resetting dirty state.
   */


  const stopEditing = () => {
    if (isDirty) {
      const blocks = Object(external_wp_blocks_["parse"])(value);
      resetEditorBlocks(blocks);
      setIsDirty(false);
    }
  };

  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["VisuallyHidden"], {
    as: "label",
    htmlFor: `post-content-${instanceId}`
  }, Object(external_wp_i18n_["__"])('Type text or HTML')), Object(external_wp_element_["createElement"])(lib_default.a, {
    autoComplete: "off",
    dir: "auto",
    value: value,
    onChange: onChange,
    onBlur: stopEditing,
    className: "editor-post-text-editor",
    id: `post-content-${instanceId}`,
    placeholder: Object(external_wp_i18n_["__"])('Start writing with text or HTML')
  }));
}

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

const REGEXP_NEWLINES = /[\r\n]+/g;
function PostTitle() {
  const instanceId = Object(external_wp_compose_["useInstanceId"])(PostTitle);
  const ref = Object(external_wp_element_["useRef"])();
  const [isSelected, setIsSelected] = Object(external_wp_element_["useState"])(false);
  const {
    editPost
  } = Object(external_wp_data_["useDispatch"])('core/editor');
  const {
    insertDefaultBlock,
    clearSelectedBlock,
    insertBlocks
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  const {
    isCleanNewPost,
    title,
    placeholder,
    isFocusMode,
    hasFixedToolbar
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditedPostAttribute,
      isCleanNewPost: _isCleanNewPost
    } = select('core/editor');
    const {
      getSettings
    } = select(external_wp_blockEditor_["store"]);
    const {
      titlePlaceholder,
      focusMode,
      hasFixedToolbar: _hasFixedToolbar
    } = getSettings();
    return {
      isCleanNewPost: _isCleanNewPost(),
      title: getEditedPostAttribute('title'),
      placeholder: titlePlaceholder,
      isFocusMode: focusMode,
      hasFixedToolbar: _hasFixedToolbar
    };
  });
  Object(external_wp_element_["useEffect"])(() => {
    if (!ref.current) {
      return;
    }

    const {
      ownerDocument
    } = ref.current;
    const {
      activeElement,
      body
    } = ownerDocument; // Only autofocus the title when the post is entirely empty. This should
    // only happen for a new post, which means we focus the title on new
    // post so the author can start typing right away, without needing to
    // click anything.

    if (isCleanNewPost && (!activeElement || body === activeElement)) {
      ref.current.focus();
    }
  }, [isCleanNewPost]);

  function onEnterPress() {
    insertDefaultBlock(undefined, undefined, 0);
  }

  function onInsertBlockAfter(blocks) {
    insertBlocks(blocks, 0);
  }

  function onUpdate(newTitle) {
    editPost({
      title: newTitle
    });
  }

  function onSelect() {
    setIsSelected(true);
    clearSelectedBlock();
  }

  function onUnselect() {
    setIsSelected(false);
  }

  function onChange(event) {
    onUpdate(event.target.value.replace(REGEXP_NEWLINES, ' '));
  }

  function onKeyDown(event) {
    if (event.keyCode === external_wp_keycodes_["ENTER"]) {
      event.preventDefault();
      onEnterPress();
    }
  }

  function onPaste(event) {
    const clipboardData = event.clipboardData;
    let plainText = '';
    let html = ''; // IE11 only supports `Text` as an argument for `getData` and will
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
    } // Allows us to ask for this information when we get a report.


    window.console.log('Received HTML:\n\n', html);
    window.console.log('Received plain text:\n\n', plainText);
    const content = Object(external_wp_blocks_["pasteHandler"])({
      HTML: html,
      plainText
    });

    if (typeof content !== 'string' && content.length) {
      event.preventDefault();
      const [firstBlock] = content;

      if (!title && (firstBlock.name === 'core/heading' || firstBlock.name === 'core/paragraph')) {
        onUpdate(firstBlock.attributes.content);
        onInsertBlockAfter(content.slice(1));
      } else {
        onInsertBlockAfter(content);
      }
    }
  } // The wp-block className is important for editor styles.
  // This same block is used in both the visual and the code editor.


  const className = classnames_default()('wp-block editor-post-title editor-post-title__block', {
    'is-selected': isSelected,
    'is-focus-mode': isFocusMode,
    'has-fixed-toolbar': hasFixedToolbar
  });
  const decodedPlaceholder = Object(external_wp_htmlEntities_["decodeEntities"])(placeholder);
  return Object(external_wp_element_["createElement"])(post_type_support_check, {
    supportKeys: "title"
  }, Object(external_wp_element_["createElement"])("div", {
    className: className
  }, Object(external_wp_element_["createElement"])(external_wp_components_["VisuallyHidden"], {
    as: "label",
    htmlFor: `post-title-${instanceId}`
  }, decodedPlaceholder || Object(external_wp_i18n_["__"])('Add title')), Object(external_wp_element_["createElement"])(lib_default.a, {
    ref: ref,
    id: `post-title-${instanceId}`,
    className: "editor-post-title__input",
    value: title,
    onChange: onChange,
    placeholder: decodedPlaceholder || Object(external_wp_i18n_["__"])('Add title'),
    onFocus: onSelect,
    onBlur: onUnselect,
    onKeyDown: onKeyDown,
    onKeyPress: onUnselect,
    onPaste: onPaste
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-trash/index.js


/**
 * WordPress dependencies
 */





function PostTrash({
  isNew,
  postId,
  postType,
  ...props
}) {
  if (isNew || !postId) {
    return null;
  }

  const onClick = () => props.trashPost(postId, postType);

  return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "editor-post-trash",
    isDestructive: true,
    isTertiary: true,
    onClick: onClick
  }, Object(external_wp_i18n_["__"])('Move to trash'));
}

/* harmony default export */ var post_trash = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const {
    isEditedPostNew,
    getCurrentPostId,
    getCurrentPostType
  } = select('core/editor');
  return {
    isNew: isEditedPostNew(),
    postId: getCurrentPostId(),
    postType: getCurrentPostType()
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  trashPost: dispatch('core/editor').trashPost
}))])(PostTrash));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-trash/check.js
/**
 * WordPress dependencies
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

/* harmony default export */ var post_trash_check = (Object(external_wp_data_["withSelect"])(select => {
  const {
    isEditedPostNew,
    getCurrentPostId,
    getCurrentPostType
  } = select('core/editor');
  const {
    getPostType,
    canUser
  } = select('core');
  const postId = getCurrentPostId();
  const postType = getPostType(getCurrentPostType());
  const resource = (postType === null || postType === void 0 ? void 0 : postType.rest_base) || ''; // eslint-disable-line camelcase

  return {
    isNew: isEditedPostNew(),
    postId,
    canUserDelete: postId && resource ? canUser('delete', resource, postId) : false
  };
})(PostTrashCheck));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/post-visibility/check.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PostVisibilityCheck({
  hasPublishAction,
  render
}) {
  const canEdit = hasPublishAction;
  return render({
    canEdit
  });
}
/* harmony default export */ var post_visibility_check = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const {
    getCurrentPost,
    getCurrentPostType
  } = select('core/editor');
  return {
    hasPublishAction: Object(external_lodash_["get"])(getCurrentPost(), ['_links', 'wp:action-publish'], false),
    postType: getCurrentPostType()
  };
})])(PostVisibilityCheck));

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/info.js
var library_info = __webpack_require__("FnAh");

// EXTERNAL MODULE: external ["wp","wordcount"]
var external_wp_wordcount_ = __webpack_require__("7fqt");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/word-count/index.js


/**
 * WordPress dependencies
 */



function WordCount() {
  const content = Object(external_wp_data_["useSelect"])(select => select('core/editor').getEditedPostAttribute('content'));
  /*
   * translators: If your word count is based on single characters (e.g. East Asian characters),
   * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
   * Do not translate into your own language.
   */

  const wordCountType = Object(external_wp_i18n_["_x"])('words', 'Word count type. Do not translate!');

  return Object(external_wp_element_["createElement"])("span", {
    className: "word-count"
  }, Object(external_wp_wordcount_["count"])(content, wordCountType));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/character-count/index.js
/**
 * WordPress dependencies
 */


function CharacterCount() {
  const content = Object(external_wp_data_["useSelect"])(select => select('core/editor').getEditedPostAttribute('content'));
  return Object(external_wp_wordcount_["count"])(content, 'characters_including_spaces');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/table-of-contents/panel.js


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
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getGlobalBlockCount
    } = select(external_wp_blockEditor_["store"]);
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
    Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("div", {
      className: "table-of-contents__wrapper",
      role: "note",
      "aria-label": Object(external_wp_i18n_["__"])('Document Statistics'),
      tabIndex: "0"
    }, Object(external_wp_element_["createElement"])("ul", {
      role: "list",
      className: "table-of-contents__counts"
    }, Object(external_wp_element_["createElement"])("li", {
      className: "table-of-contents__count"
    }, Object(external_wp_i18n_["__"])('Characters'), Object(external_wp_element_["createElement"])("span", {
      className: "table-of-contents__number"
    }, Object(external_wp_element_["createElement"])(CharacterCount, null))), Object(external_wp_element_["createElement"])("li", {
      className: "table-of-contents__count"
    }, Object(external_wp_i18n_["__"])('Words'), Object(external_wp_element_["createElement"])(WordCount, null)), Object(external_wp_element_["createElement"])("li", {
      className: "table-of-contents__count"
    }, Object(external_wp_i18n_["__"])('Headings'), Object(external_wp_element_["createElement"])("span", {
      className: "table-of-contents__number"
    }, headingCount)), Object(external_wp_element_["createElement"])("li", {
      className: "table-of-contents__count"
    }, Object(external_wp_i18n_["__"])('Paragraphs'), Object(external_wp_element_["createElement"])("span", {
      className: "table-of-contents__number"
    }, paragraphCount)), Object(external_wp_element_["createElement"])("li", {
      className: "table-of-contents__count"
    }, Object(external_wp_i18n_["__"])('Blocks'), Object(external_wp_element_["createElement"])("span", {
      className: "table-of-contents__number"
    }, numberOfBlocks)))), headingCount > 0 && Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("hr", null), Object(external_wp_element_["createElement"])("h2", {
      className: "table-of-contents__title"
    }, Object(external_wp_i18n_["__"])('Document Outline')), Object(external_wp_element_["createElement"])(document_outline, {
      onSelect: onRequestClose,
      hasOutlineItemsDisabled: hasOutlineItemsDisabled
    })))
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
}

/* harmony default export */ var panel = (TableOfContentsPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/table-of-contents/index.js



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
  const hasBlocks = Object(external_wp_data_["useSelect"])(select => !!select(external_wp_blockEditor_["store"]).getBlockCount(), []);
  return Object(external_wp_element_["createElement"])(external_wp_components_["Dropdown"], {
    position: repositionDropdown ? 'middle right right' : 'bottom',
    className: "table-of-contents",
    contentClassName: "table-of-contents__popover",
    renderToggle: ({
      isOpen,
      onToggle
    }) => Object(external_wp_element_["createElement"])(external_wp_components_["Button"], Object(esm_extends["a" /* default */])({}, props, {
      ref: ref,
      onClick: hasBlocks ? onToggle : undefined,
      icon: library_info["a" /* default */],
      "aria-expanded": isOpen,
      "aria-haspopup": "true"
      /* translators: button label text should, if possible, be under 16 characters. */
      ,
      label: Object(external_wp_i18n_["__"])('Details'),
      tooltipPosition: "bottom",
      "aria-disabled": !hasBlocks
    })),
    renderContent: ({
      onClose
    }) => Object(external_wp_element_["createElement"])(panel, {
      onRequestClose: onClose,
      hasOutlineItemsDisabled: hasOutlineItemsDisabled
    })
  });
}

/* harmony default export */ var table_of_contents = (Object(external_wp_element_["forwardRef"])(TableOfContents));

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/unsaved-changes-warning/index.js
/**
 * WordPress dependencies
 */



/**
 * Warns the user if there are unsaved changes before leaving the editor.
 * Compatible with Post Editor and Site Editor.
 *
 * @return {WPComponent} The component.
 */

function UnsavedChangesWarning() {
  const isDirty = Object(external_wp_data_["useSelect"])(select => {
    return () => {
      const {
        __experimentalGetDirtyEntityRecords
      } = select('core');

      const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();

      return dirtyEntityRecords.length > 0;
    };
  }, []);
  /**
   * Warns the user if there are unsaved changes before leaving the editor.
   *
   * @param {Event} event `beforeunload` event.
   *
   * @return {?string} Warning prompt message, if unsaved changes exist.
   */

  const warnIfUnsavedChanges = event => {
    // We need to call the selector directly in the listener to avoid race
    // conditions with `BrowserURL` where `componentDidUpdate` gets the
    // new value of `isEditedPostDirty` before this component does,
    // causing this component to incorrectly think a trashed post is still dirty.
    if (isDirty()) {
      event.returnValue = Object(external_wp_i18n_["__"])('You have unsaved changes. If you proceed, they will be lost.');
      return event.returnValue;
    }
  };

  Object(external_wp_element_["useEffect"])(() => {
    window.addEventListener('beforeunload', warnIfUnsavedChanges);
    return () => {
      window.removeEventListener('beforeunload', warnIfUnsavedChanges);
    };
  }, []);
  return null;
}

// EXTERNAL MODULE: external ["wp","reusableBlocks"]
var external_wp_reusableBlocks_ = __webpack_require__("diJD");

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/with-registry-provider.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


const withRegistryProvider = Object(external_wp_compose_["createHigherOrderComponent"])(WrappedComponent => Object(external_wp_data_["withRegistry"])(props => {
  const {
    useSubRegistry = true,
    registry,
    ...additionalProps
  } = props;

  if (!useSubRegistry) {
    return Object(external_wp_element_["createElement"])(WrappedComponent, additionalProps);
  }

  const [subRegistry, setSubRegistry] = Object(external_wp_element_["useState"])(null);
  Object(external_wp_element_["useEffect"])(() => {
    const newRegistry = Object(external_wp_data_["createRegistry"])({
      'core/block-editor': external_wp_blockEditor_["storeConfig"]
    }, registry);
    newRegistry.registerStore('core/editor', storeConfig);
    setSubRegistry(newRegistry);
  }, [registry]);

  if (!subRegistry) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_data_["RegistryProvider"], {
    value: subRegistry
  }, Object(external_wp_element_["createElement"])(WrappedComponent, additionalProps));
}), 'withRegistryProvider');
/* harmony default export */ var with_registry_provider = (withRegistryProvider);

// EXTERNAL MODULE: external ["wp","mediaUtils"]
var external_wp_mediaUtils_ = __webpack_require__("6aBm");

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

function mediaUpload({
  additionalData = {},
  allowedTypes,
  filesList,
  maxUploadFileSize,
  onError = external_lodash_["noop"],
  onFileChange
}) {
  const {
    getCurrentPostId,
    getEditorSettings
  } = Object(external_wp_data_["select"])('core/editor');
  const wpAllowedMimeTypes = getEditorSettings().allowedMimeTypes;
  maxUploadFileSize = maxUploadFileSize || getEditorSettings().maxUploadFileSize;
  Object(external_wp_mediaUtils_["uploadMedia"])({
    allowedTypes,
    filesList,
    onFileChange,
    additionalData: {
      post: getCurrentPostId(),
      ...additionalData
    },
    maxUploadFileSize,
    onError: ({
      message
    }) => onError(message),
    wpAllowedMimeTypes
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/use-block-editor-settings.js
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
 * React hook used to compute the block editor settings to use for the post editor.
 *
 * @param {Object}  settings    EditorProvider settings prop.
 * @param {boolean} hasTemplate Whether template mode is enabled.
 *
 * @return {Object} Block Editor Settings.
 */

function useBlockEditorSettings(settings, hasTemplate) {
  const {
    reusableBlocks,
    hasUploadPermissions,
    canUseUnfilteredHTML,
    isTitleSelected
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      canUserUseUnfilteredHTML,
      isPostTitleSelected
    } = select(store);
    const {
      canUser
    } = select(external_wp_coreData_["store"]);
    return {
      canUseUnfilteredHTML: canUserUseUnfilteredHTML(),
      reusableBlocks: select(external_wp_coreData_["store"]).getEntityRecords('postType', 'wp_block',
      /**
       * Unbounded queries are not supported on native so as a workaround, we set per_page with the maximum value that native version can handle.
       * Related issue: https://github.com/wordpress-mobile/gutenberg-mobile/issues/2661
       */
      {
        per_page: external_wp_element_["Platform"].select({
          web: -1,
          native: 10
        })
      }),
      hasUploadPermissions: Object(external_lodash_["defaultTo"])(canUser('create', 'media'), true),
      // This selector is only defined on mobile.
      isTitleSelected: isPostTitleSelected && isPostTitleSelected()
    };
  }, []);
  const {
    undo
  } = Object(external_wp_data_["useDispatch"])(store);
  return Object(external_wp_element_["useMemo"])(() => ({ ...Object(external_lodash_["pick"])(settings, ['__experimentalBlockDirectory', '__experimentalBlockPatternCategories', '__experimentalBlockPatterns', '__experimentalFeatures', '__experimentalGlobalStylesBaseStyles', '__experimentalGlobalStylesUserEntityId', '__experimentalPreferredStyleVariations', '__experimentalSetIsInserterOpened', 'alignWide', 'allowedBlockTypes', 'bodyPlaceholder', 'codeEditingEnabled', 'colors', 'disableCustomColors', 'disableCustomFontSizes', 'disableCustomGradients', 'enableCustomLineHeight', 'enableCustomSpacing', 'enableCustomUnits', 'focusMode', 'fontSizes', 'gradients', 'hasFixedToolbar', 'hasReducedUI', 'imageDefaultSize', 'imageDimensions', 'imageEditing', 'imageSizes', 'isRTL', 'keepCaretInsideBlock', 'maxWidth', 'onUpdateDefaultBlockStyles', 'styles', 'template', 'templateLock', 'titlePlaceholder', 'supportsLayout', 'widgetTypesToHideFromLegacyWidgetBlock']),
    mediaUpload: hasUploadPermissions ? mediaUpload : undefined,
    __experimentalReusableBlocks: reusableBlocks,
    __experimentalFetchLinkSuggestions: (search, searchOptions) => Object(external_wp_coreData_["__experimentalFetchLinkSuggestions"])(search, searchOptions, settings),
    __experimentalFetchRemoteUrlData: url => Object(external_wp_coreData_["__experimentalFetchRemoteUrlData"])(url),
    __experimentalCanUserUseUnfilteredHTML: canUseUnfilteredHTML,
    __experimentalUndo: undo,
    __experimentalShouldInsertAtTheTop: isTitleSelected,
    outlineMode: hasTemplate
  }), [settings, hasUploadPermissions, reusableBlocks, canUseUnfilteredHTML, undo, isTitleSelected, hasTemplate]);
}

/* harmony default export */ var use_block_editor_settings = (useBlockEditorSettings);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/provider/index.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */





function EditorProvider({
  __unstableTemplate,
  post,
  settings,
  recovery,
  initialEdits,
  children
}) {
  const defaultBlockContext = Object(external_wp_element_["useMemo"])(() => {
    if (post.type === 'wp_template') {
      return {};
    }

    return {
      postId: post.id,
      postType: post.type
    };
  }, [post.id, post.type]);
  const {
    selection,
    isReady
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditorSelection,
      __unstableIsEditorReady
    } = select(store);
    return {
      isReady: __unstableIsEditorReady(),
      selection: getEditorSelection()
    };
  }, []);
  const {
    id,
    type
  } = __unstableTemplate !== null && __unstableTemplate !== void 0 ? __unstableTemplate : post;
  const [blocks, onInput, onChange] = Object(external_wp_coreData_["useEntityBlockEditor"])('postType', type, {
    id
  });
  const editorSettings = use_block_editor_settings(settings, !!__unstableTemplate);
  const {
    updatePostLock,
    setupEditor,
    updateEditorSettings,
    __experimentalTearDownEditor
  } = Object(external_wp_data_["useDispatch"])(store);
  const {
    createWarningNotice
  } = Object(external_wp_data_["useDispatch"])(external_wp_notices_["store"]); // Iniitialize and tear down the editor.
  // Ideally this should be synced on each change and not just something you do once.

  Object(external_wp_element_["useLayoutEffect"])(() => {
    // Assume that we don't need to initialize in the case of an error recovery.
    if (recovery) {
      return;
    }

    updatePostLock(settings.postLock);
    setupEditor(post, initialEdits, settings.template);

    if (settings.autosave) {
      createWarningNotice(Object(external_wp_i18n_["__"])('There is an autosave of this post that is more recent than the version below.'), {
        id: 'autosave-exists',
        actions: [{
          label: Object(external_wp_i18n_["__"])('View the autosave'),
          url: settings.autosave.editLink
        }]
      });
    }

    return () => {
      __experimentalTearDownEditor();
    };
  }, []); // Synchronize the editor settings as they change

  Object(external_wp_element_["useEffect"])(() => {
    updateEditorSettings(settings);
  }, [settings]);

  if (!isReady) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_coreData_["EntityProvider"], {
    kind: "root",
    type: "site"
  }, Object(external_wp_element_["createElement"])(external_wp_coreData_["EntityProvider"], {
    kind: "postType",
    type: post.type,
    id: post.id
  }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockContextProvider"], {
    value: defaultBlockContext
  }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockEditorProvider"], {
    value: blocks,
    onChange: onChange,
    onInput: onInput,
    selection: selection,
    settings: editorSettings,
    useSubRegistry: false
  }, children, Object(external_wp_element_["createElement"])(external_wp_reusableBlocks_["ReusableBlocksMenuItems"], null)))));
}

/* harmony default export */ var provider = (with_registry_provider(EditorProvider));

// EXTERNAL MODULE: external ["wp","serverSideRender"]
var external_wp_serverSideRender_ = __webpack_require__("JREk");
var external_wp_serverSideRender_default = /*#__PURE__*/__webpack_require__.n(external_wp_serverSideRender_);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/deprecated.js


// Block Creation Components

/**
 * WordPress dependencies
 */





function deprecateComponent(name, Wrapped, staticsToHoist = []) {
  const Component = Object(external_wp_element_["forwardRef"])((props, ref) => {
    external_wp_deprecated_default()('wp.editor.' + name, {
      since: '5.3',
      alternative: 'wp.blockEditor.' + name
    });
    return Object(external_wp_element_["createElement"])(Wrapped, Object(esm_extends["a" /* default */])({
      ref: ref
    }, props));
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
      alternative: 'wp.blockEditor.' + name
    });
    return func(...args);
  };
}

const RichText = deprecateComponent('RichText', external_wp_blockEditor_["RichText"], ['Content']);
RichText.isEmpty = deprecateFunction('RichText.isEmpty', external_wp_blockEditor_["RichText"].isEmpty);

const Autocomplete = deprecateComponent('Autocomplete', external_wp_blockEditor_["Autocomplete"]);
const AlignmentToolbar = deprecateComponent('AlignmentToolbar', external_wp_blockEditor_["AlignmentToolbar"]);
const BlockAlignmentToolbar = deprecateComponent('BlockAlignmentToolbar', external_wp_blockEditor_["BlockAlignmentToolbar"]);
const BlockControls = deprecateComponent('BlockControls', external_wp_blockEditor_["BlockControls"], ['Slot']);
const deprecated_BlockEdit = deprecateComponent('BlockEdit', external_wp_blockEditor_["BlockEdit"]);
const BlockEditorKeyboardShortcuts = deprecateComponent('BlockEditorKeyboardShortcuts', external_wp_blockEditor_["BlockEditorKeyboardShortcuts"]);
const BlockFormatControls = deprecateComponent('BlockFormatControls', external_wp_blockEditor_["BlockFormatControls"], ['Slot']);
const BlockIcon = deprecateComponent('BlockIcon', external_wp_blockEditor_["BlockIcon"]);
const BlockInspector = deprecateComponent('BlockInspector', external_wp_blockEditor_["BlockInspector"]);
const BlockList = deprecateComponent('BlockList', external_wp_blockEditor_["BlockList"]);
const BlockMover = deprecateComponent('BlockMover', external_wp_blockEditor_["BlockMover"]);
const BlockNavigationDropdown = deprecateComponent('BlockNavigationDropdown', external_wp_blockEditor_["BlockNavigationDropdown"]);
const BlockSelectionClearer = deprecateComponent('BlockSelectionClearer', external_wp_blockEditor_["BlockSelectionClearer"]);
const BlockSettingsMenu = deprecateComponent('BlockSettingsMenu', external_wp_blockEditor_["BlockSettingsMenu"]);
const BlockTitle = deprecateComponent('BlockTitle', external_wp_blockEditor_["BlockTitle"]);
const BlockToolbar = deprecateComponent('BlockToolbar', external_wp_blockEditor_["BlockToolbar"]);
const ColorPalette = deprecateComponent('ColorPalette', external_wp_blockEditor_["ColorPalette"]);
const ContrastChecker = deprecateComponent('ContrastChecker', external_wp_blockEditor_["ContrastChecker"]);
const CopyHandler = deprecateComponent('CopyHandler', external_wp_blockEditor_["CopyHandler"]);
const DefaultBlockAppender = deprecateComponent('DefaultBlockAppender', external_wp_blockEditor_["DefaultBlockAppender"]);
const FontSizePicker = deprecateComponent('FontSizePicker', external_wp_blockEditor_["FontSizePicker"]);
const Inserter = deprecateComponent('Inserter', external_wp_blockEditor_["Inserter"]);
const InnerBlocks = deprecateComponent('InnerBlocks', external_wp_blockEditor_["InnerBlocks"], ['ButtonBlockAppender', 'DefaultBlockAppender', 'Content']);
const InspectorAdvancedControls = deprecateComponent('InspectorAdvancedControls', external_wp_blockEditor_["InspectorAdvancedControls"], ['Slot']);
const InspectorControls = deprecateComponent('InspectorControls', external_wp_blockEditor_["InspectorControls"], ['Slot']);
const PanelColorSettings = deprecateComponent('PanelColorSettings', external_wp_blockEditor_["PanelColorSettings"]);
const PlainText = deprecateComponent('PlainText', external_wp_blockEditor_["PlainText"]);
const RichTextShortcut = deprecateComponent('RichTextShortcut', external_wp_blockEditor_["RichTextShortcut"]);
const RichTextToolbarButton = deprecateComponent('RichTextToolbarButton', external_wp_blockEditor_["RichTextToolbarButton"]);
const __unstableRichTextInputEvent = deprecateComponent('__unstableRichTextInputEvent', external_wp_blockEditor_["__unstableRichTextInputEvent"]);
const MediaPlaceholder = deprecateComponent('MediaPlaceholder', external_wp_blockEditor_["MediaPlaceholder"]);
const MediaUpload = deprecateComponent('MediaUpload', external_wp_blockEditor_["MediaUpload"]);
const MediaUploadCheck = deprecateComponent('MediaUploadCheck', external_wp_blockEditor_["MediaUploadCheck"]);
const MultiSelectScrollIntoView = deprecateComponent('MultiSelectScrollIntoView', external_wp_blockEditor_["MultiSelectScrollIntoView"]);
const NavigableToolbar = deprecateComponent('NavigableToolbar', external_wp_blockEditor_["NavigableToolbar"]);
const ObserveTyping = deprecateComponent('ObserveTyping', external_wp_blockEditor_["ObserveTyping"]);
const PreserveScrollInReorder = deprecateComponent('PreserveScrollInReorder', external_wp_blockEditor_["PreserveScrollInReorder"]);
const SkipToSelectedBlock = deprecateComponent('SkipToSelectedBlock', external_wp_blockEditor_["SkipToSelectedBlock"]);
const URLInput = deprecateComponent('URLInput', external_wp_blockEditor_["URLInput"]);
const URLInputButton = deprecateComponent('URLInputButton', external_wp_blockEditor_["URLInputButton"]);
const URLPopover = deprecateComponent('URLPopover', external_wp_blockEditor_["URLPopover"]);
const Warning = deprecateComponent('Warning', external_wp_blockEditor_["Warning"]);
const WritingFlow = deprecateComponent('WritingFlow', external_wp_blockEditor_["WritingFlow"]);
const createCustomColorsHOC = deprecateFunction('createCustomColorsHOC', external_wp_blockEditor_["createCustomColorsHOC"]);
const getColorClassName = deprecateFunction('getColorClassName', external_wp_blockEditor_["getColorClassName"]);
const getColorObjectByAttributeValues = deprecateFunction('getColorObjectByAttributeValues', external_wp_blockEditor_["getColorObjectByAttributeValues"]);
const getColorObjectByColorValue = deprecateFunction('getColorObjectByColorValue', external_wp_blockEditor_["getColorObjectByColorValue"]);
const getFontSize = deprecateFunction('getFontSize', external_wp_blockEditor_["getFontSize"]);
const getFontSizeClass = deprecateFunction('getFontSizeClass', external_wp_blockEditor_["getFontSizeClass"]);
const withColorContext = deprecateFunction('withColorContext', external_wp_blockEditor_["withColorContext"]);
const withColors = deprecateFunction('withColors', external_wp_blockEditor_["withColors"]);
const withFontSizes = deprecateFunction('withFontSizes', external_wp_blockEditor_["withFontSizes"]);

// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/components/index.js
// Block Creation Components
 // Post Related Components

























































 // State Related Components




// CONCATENATED MODULE: ./node_modules/@wordpress/editor/build-module/utils/index.js
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





/*
 * Backward compatibility
 */




/***/ }),

/***/ "PUI2":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const postContent = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "https://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 20h16v-1.5H4V20zm0-4.8h16v-1.5H4v1.5zm0-6.4v1.5h16V8.8H4zM16 4H4v1.5h12V4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (postContent);


/***/ }),

/***/ "Q4Sy":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const plus = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z"
}));
/* harmony default export */ __webpack_exports__["a"] = (plus);


/***/ }),

/***/ "QHle":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const overlayText = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12-9.8c.4 0 .8-.3.9-.7l1.1-3h3.6l.5 1.7h1.9L13 9h-2.2l-3.4 9.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h12c.3 0 .5.2.5.5v12H20V6c0-1.1-.9-2-2-2zm-6 7l1.4 3.9h-2.7L12 11z"
}));
/* harmony default export */ __webpack_exports__["a"] = (overlayText);


/***/ }),

/***/ "Qtmx":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const video = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.7 3H5.3C4 3 3 4 3 5.3v13.4C3 20 4 21 5.3 21h13.4c1.3 0 2.3-1 2.3-2.3V5.3C21 4 20 3 18.7 3zm.8 15.7c0 .4-.4.8-.8.8H5.3c-.4 0-.8-.4-.8-.8V5.3c0-.4.4-.8.8-.8h13.4c.4 0 .8.4.8.8v13.4zM10 15l5-3-5-3v6z"
}));
/* harmony default export */ __webpack_exports__["a"] = (video);


/***/ }),

/***/ "R7Ei":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const aspectRatio = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.5 5.5h-13c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2h13c1.1 0 2-.9 2-2v-9c0-1.1-.9-2-2-2zm.5 11c0 .3-.2.5-.5.5h-13c-.3 0-.5-.2-.5-.5v-9c0-.3.2-.5.5-.5h13c.3 0 .5.2.5.5v9zM6.5 12H8v-2h2V8.5H6.5V12zm9.5 2h-2v1.5h3.5V12H16v2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (aspectRatio);


/***/ }),

/***/ "RD9Y":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const preformatted = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h12c.3 0 .5.2.5.5v12zM7 16.5h6V15H7v1.5zm4-4h6V11h-6v1.5zM9 11H7v1.5h2V11zm6 5.5h2V15h-2v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (preformatted);


/***/ }),

/***/ "RMJe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const check = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.3 5.6L9.9 16.9l-4.6-3.4-.9 1.2 5.8 4.3 9.3-12.6z"
}));
/* harmony default export */ __webpack_exports__["a"] = (check);


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

/***/ "Rq5i":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatUppercase = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6.1 6.8L2.1 18h1.6l1.1-3h4.3l1.1 3h1.6l-4-11.2H6.1zm-.8 6.8L7 8.9l1.7 4.7H5.3zm15.1-.7c-.4-.5-.9-.8-1.6-1 .4-.2.7-.5.8-.9.2-.4.3-.9.3-1.4 0-.9-.3-1.6-.8-2-.6-.5-1.3-.7-2.4-.7h-3.5V18h4.2c1.1 0 2-.3 2.6-.8.6-.6 1-1.4 1-2.4-.1-.8-.3-1.4-.6-1.9zm-5.7-4.7h1.8c.6 0 1.1.1 1.4.4.3.2.5.7.5 1.3 0 .6-.2 1.1-.5 1.3-.3.2-.8.4-1.4.4h-1.8V8.2zm4 8c-.4.3-.9.5-1.5.5h-2.6v-3.8h2.6c1.4 0 2 .6 2 1.9.1.6-.1 1-.5 1.4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatUppercase);


/***/ }),

/***/ "RxS6":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["keycodes"]; }());

/***/ }),

/***/ "S1+T":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const subscript = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M16.9 18.3l.8-1.2c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.1-.3-.4-.5-.6-.7-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.2 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3L15 19.4h4.3v-1.2h-2.4zM14.1 7.2h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z"
}));
/* harmony default export */ __webpack_exports__["a"] = (subscript);


/***/ }),

/***/ "SRG5":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const reusableBlock = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M7 7.2h8.2L13.5 9l1.1 1.1 3.6-3.6-3.5-4-1.1 1 1.9 2.3H7c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.2-.5zm13.8 4V11h-1.5v.3c0 1.1 0 3.5-1 4.5-.3.3-.7.5-1.3.5H8.8l1.7-1.7-1.1-1.1L5.9 17l3.5 4 1.1-1-1.9-2.3H17c.9 0 1.7-.3 2.3-.9 1.5-1.4 1.5-4.2 1.5-5.6z"
}));
/* harmony default export */ __webpack_exports__["a"] = (reusableBlock);


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

/***/ "Ti6x":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const classic = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20 6H4c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm.5 11c0 .3-.2.5-.5.5H4c-.3 0-.5-.2-.5-.5V8c0-.3.2-.5.5-.5h16c.3 0 .5.2.5.5v9zM10 10H8v2h2v-2zm-5 2h2v-2H5v2zm8-2h-2v2h2v-2zm-5 6h8v-2H8v2zm6-4h2v-2h-2v2zm3 0h2v-2h-2v2zm0 4h2v-2h-2v2zM5 16h2v-2H5v2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (classic);


/***/ }),

/***/ "Tj2E":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const queryPaginationNext = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Rect"], {
  x: "6",
  y: "10.5",
  width: "3",
  height: "3",
  rx: "1.5",
  fill: "#000"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Rect"], {
  x: "11",
  y: "10.5",
  width: "3",
  height: "3",
  rx: "1.5",
  fill: "#000"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M16.5 9.5L19 12l-2.5 2.5",
  stroke: "#1E1E1E",
  strokeWidth: "1.5"
}));
/* harmony default export */ __webpack_exports__["a"] = (queryPaginationNext);


/***/ }),

/***/ "Tqx9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["primitives"]; }());

/***/ }),

/***/ "URob":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const starFilled = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"
}));
/* harmony default export */ __webpack_exports__["a"] = (starFilled);


/***/ }),

/***/ "UXLW":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const audio = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M17.7 4.3c-1.2 0-2.8 0-3.8 1-.6.6-.9 1.5-.9 2.6V14c-.6-.6-1.5-1-2.5-1C8.6 13 7 14.6 7 16.5S8.6 20 10.5 20c1.5 0 2.8-1 3.3-2.3.5-.8.7-1.8.7-2.5V7.9c0-.7.2-1.2.5-1.6.6-.6 1.8-.6 2.8-.6h.3V4.3h-.4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (audio);


/***/ }),

/***/ "UXXq":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const ungroup = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 4h-7c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h7c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 9c0 .3-.2.5-.5.5h-7c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h7c.3 0 .5.2.5.5v7zm-5 5c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5v-7c0-.3.2-.5.5-.5h1V9H6c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h7c1.1 0 2-.9 2-2v-1h-1.5v1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (ungroup);


/***/ }),

/***/ "Ud6t":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatListBullets = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11.1 15.8H20v-1.5h-8.9v1.5zm0-8.6v1.5H20V7.2h-8.9zM6 13c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-7c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatListBullets);


/***/ }),

/***/ "VKE3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const moreVertical = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (moreVertical);


/***/ }),

/***/ "WI8J":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export gallery */
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const gallery = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20.2 8v11c0 .7-.6 1.2-1.2 1.2H6v1.5h13c1.5 0 2.7-1.2 2.7-2.8V8h-1.5zM18 16.4V4.6c0-.9-.7-1.6-1.6-1.6H4.6C3.7 3 3 3.7 3 4.6v11.8c0 .9.7 1.6 1.6 1.6h11.8c.9 0 1.6-.7 1.6-1.6zM4.5 4.6c0-.1.1-.1.1-.1h11.8c.1 0 .1.1.1.1V12l-2.3-1.7c-.3-.2-.6-.2-.9 0l-2.9 2.1L8 11.3c-.2-.1-.5-.1-.7 0l-2.9 1.5V4.6zm0 11.8v-1.8l3.2-1.7 2.4 1.2c.2.1.5.1.8-.1l2.8-2 2.8 2v2.5c0 .1-.1.1-.1.1H4.6c0-.1-.1-.2-.1-.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (gallery);


/***/ }),

/***/ "WIVa":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const starHalf = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M9.518 8.783a.25.25 0 00.188-.137l2.069-4.192a.25.25 0 01.448 0l2.07 4.192a.25.25 0 00.187.137l4.626.672a.25.25 0 01.139.427l-3.347 3.262a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.363.264l-4.137-2.176a.25.25 0 00-.233 0l-4.138 2.175a.25.25 0 01-.362-.263l.79-4.607a.25.25 0 00-.072-.222L4.753 9.882a.25.25 0 01.14-.427l4.625-.672zM12 14.533c.28 0 .559.067.814.2l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39v7.143z"
}));
/* harmony default export */ __webpack_exports__["a"] = (starHalf);


/***/ }),

/***/ "WKtA":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const heading = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6.2 5.2v13.4l5.8-4.8 5.8 4.8V5.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (heading);


/***/ }),

/***/ "WR/0":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const pullRight = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M14 6v12h6V6h-6zM4 10h7V8.5H4V10zm0 5.5h7V14H4v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (pullRight);


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

/***/ "Wdov":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatItalic = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12.5 5L10 19h1.9l2.5-14z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatItalic);


/***/ }),

/***/ "X3CL":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const image = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 4.5h14c.3 0 .5.2.5.5v8.4l-3-2.9c-.3-.3-.8-.3-1 0L11.9 14 9 12c-.3-.2-.6-.2-.8 0l-3.6 2.6V5c-.1-.3.1-.5.4-.5zm14 15H5c-.3 0-.5-.2-.5-.5v-2.4l4.1-3 3 1.9c.3.2.7.2.9-.1L16 12l3.5 3.4V19c0 .3-.2.5-.5.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (image);


/***/ }),

/***/ "XNw2":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const stretchFullWidth = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5 4v11h14V4H5zm3 15.8h8v-1.5H8v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (stretchFullWidth);


/***/ }),

/***/ "XgzB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const chevronUp = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (chevronUp);


/***/ }),

/***/ "Xxwi":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const starEmpty = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  fillRule: "evenodd",
  d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (starEmpty);


/***/ }),

/***/ "XyZy":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const rotateRight = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M15.1 4.8l-3-2.5V4c-4.4 0-8 3.6-8 8 0 3.7 2.5 6.9 6 7.7.3.1.6.1 1 .2l.2-1.5c-.4 0-.7-.1-1.1-.2l-.1.2v-.2c-2.6-.8-4.5-3.3-4.5-6.2 0-3.6 2.9-6.5 6.5-6.5v1.8l3-2.5zM20 11c-.2-1.4-.7-2.7-1.6-3.8l-1.2.8c.7.9 1.1 2 1.3 3.1L20 11zm-1.5 1.8c-.1.5-.2 1.1-.4 1.6s-.5 1-.8 1.5l1.2.9c.4-.5.8-1.1 1-1.8s.5-1.3.5-2l-1.5-.2zm-5.6 5.6l.2 1.5c1.4-.2 2.7-.7 3.8-1.6l-.9-1.1c-.9.7-2 1.1-3.1 1.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (rotateRight);


/***/ }),

/***/ "Y39O":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const backup = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5.5 12h1.75l-2.5 3-2.5-3H4a8 8 0 113.134 6.35l.907-1.194A6.5 6.5 0 105.5 12zm9.53 1.97l-2.28-2.28V8.5a.75.75 0 00-1.5 0V12a.747.747 0 00.218.529l1.282-.84-1.28.842 2.5 2.5a.75.75 0 101.06-1.061z"
}));
/* harmony default export */ __webpack_exports__["a"] = (backup);


/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "Z9je":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatIndentRTL = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20 5.5H4V4H20V5.5ZM12 12.5H4V11H12V12.5ZM20 20V18.5H4V20H20ZM20.0303 9.03033L17.0607 12L20.0303 14.9697L18.9697 16.0303L15.4697 12.5303L14.9393 12L15.4697 11.4697L18.9697 7.96967L20.0303 9.03033Z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatIndentRTL);


/***/ }),

/***/ "ZWpV":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const comment = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 4H6c-1.1 0-2 .9-2 2v12.9c0 .6.5 1.1 1.1 1.1.3 0 .5-.1.8-.3L8.5 17H18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 11c0 .3-.2.5-.5.5H7.9l-2.4 2.4V6c0-.3.2-.5.5-.5h12c.3 0 .5.2.5.5v9z"
}));
/* harmony default export */ __webpack_exports__["a"] = (comment);


/***/ }),

/***/ "Zm8Q":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const page = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M7 5.5h10a.5.5 0 01.5.5v12a.5.5 0 01-.5.5H7a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5zM17 4H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2zm-1 3.75H8v1.5h8v-1.5zM8 11h8v1.5H8V11zm6 3.25H8v1.5h6v-1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (page);


/***/ }),

/***/ "axFQ":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockEditor"]; }());

/***/ }),

/***/ "b2RC":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const grid = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7.8 16.5H5c-.3 0-.5-.2-.5-.5v-6.2h6.8v6.7zm0-8.3H4.5V5c0-.3.2-.5.5-.5h6.2v6.7zm8.3 7.8c0 .3-.2.5-.5.5h-6.2v-6.8h6.8V19zm0-7.8h-6.8V4.5H19c.3 0 .5.2.5.5v6.2z",
  fillRule: "evenodd",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (grid);


/***/ }),

/***/ "b9KX":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "Icon", function() { return /* reexport */ icon["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "addSubmenu", function() { return /* reexport */ add_submenu; });
__webpack_require__.d(__webpack_exports__, "alignCenter", function() { return /* reexport */ align_center["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "alignJustify", function() { return /* reexport */ align_justify; });
__webpack_require__.d(__webpack_exports__, "alignLeft", function() { return /* reexport */ align_left["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "alignRight", function() { return /* reexport */ align_right["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "archive", function() { return /* reexport */ archive["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "archiveTitle", function() { return /* reexport */ archive_title["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "arrowDown", function() { return /* reexport */ arrow_down; });
__webpack_require__.d(__webpack_exports__, "arrowLeft", function() { return /* reexport */ arrow_left["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "arrowRight", function() { return /* reexport */ arrow_right; });
__webpack_require__.d(__webpack_exports__, "arrowUp", function() { return /* reexport */ arrow_up; });
__webpack_require__.d(__webpack_exports__, "atSymbol", function() { return /* reexport */ at_symbol; });
__webpack_require__.d(__webpack_exports__, "aspectRatio", function() { return /* reexport */ aspect_ratio["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "audio", function() { return /* reexport */ audio["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "backup", function() { return /* reexport */ backup["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "blockDefault", function() { return /* reexport */ block_default["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "blockTable", function() { return /* reexport */ block_table["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "box", function() { return /* reexport */ library_box; });
__webpack_require__.d(__webpack_exports__, "brush", function() { return /* reexport */ library_brush; });
__webpack_require__.d(__webpack_exports__, "button", function() { return /* reexport */ library_button["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "buttons", function() { return /* reexport */ buttons["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "calendar", function() { return /* reexport */ calendar["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "camera", function() { return /* reexport */ library_camera; });
__webpack_require__.d(__webpack_exports__, "cancelCircleFilled", function() { return /* reexport */ cancel_circle_filled; });
__webpack_require__.d(__webpack_exports__, "capturePhoto", function() { return /* reexport */ capture_photo; });
__webpack_require__.d(__webpack_exports__, "captureVideo", function() { return /* reexport */ capture_video; });
__webpack_require__.d(__webpack_exports__, "category", function() { return /* reexport */ category["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "chartBar", function() { return /* reexport */ chart_bar; });
__webpack_require__.d(__webpack_exports__, "chartLine", function() { return /* reexport */ chart_line; });
__webpack_require__.d(__webpack_exports__, "check", function() { return /* reexport */ check["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "chevronDown", function() { return /* reexport */ chevron_down["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "chevronLeft", function() { return /* reexport */ chevron_left["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "chevronRight", function() { return /* reexport */ chevron_right["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "chevronUp", function() { return /* reexport */ chevron_up["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "classic", function() { return /* reexport */ classic["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "closeCircleFilled", function() { return /* reexport */ close_circle_filled; });
__webpack_require__.d(__webpack_exports__, "close", function() { return /* reexport */ library_close["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "closeSmall", function() { return /* reexport */ close_small["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "cloudUpload", function() { return /* reexport */ cloud_upload["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "cloud", function() { return /* reexport */ cloud["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "code", function() { return /* reexport */ code["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "cog", function() { return /* reexport */ cog["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "column", function() { return /* reexport */ column["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "columns", function() { return /* reexport */ columns["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "comment", function() { return /* reexport */ comment["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "controlsRepeat", function() { return /* reexport */ controls_repeat; });
__webpack_require__.d(__webpack_exports__, "cover", function() { return /* reexport */ cover["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "create", function() { return /* reexport */ library_create; });
__webpack_require__.d(__webpack_exports__, "crop", function() { return /* reexport */ crop["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "currencyDollar", function() { return /* reexport */ currency_dollar; });
__webpack_require__.d(__webpack_exports__, "currencyEuro", function() { return /* reexport */ currency_euro; });
__webpack_require__.d(__webpack_exports__, "currencyPound", function() { return /* reexport */ currency_pound; });
__webpack_require__.d(__webpack_exports__, "customPostType", function() { return /* reexport */ custom_post_type; });
__webpack_require__.d(__webpack_exports__, "desktop", function() { return /* reexport */ library_desktop; });
__webpack_require__.d(__webpack_exports__, "dragHandle", function() { return /* reexport */ drag_handle["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "download", function() { return /* reexport */ library_download; });
__webpack_require__.d(__webpack_exports__, "edit", function() { return /* reexport */ edit["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "expand", function() { return /* reexport */ library_expand; });
__webpack_require__.d(__webpack_exports__, "external", function() { return /* reexport */ external["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "file", function() { return /* reexport */ file["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "flipHorizontal", function() { return /* reexport */ flip_horizontal; });
__webpack_require__.d(__webpack_exports__, "flipVertical", function() { return /* reexport */ flip_vertical; });
__webpack_require__.d(__webpack_exports__, "formatBold", function() { return /* reexport */ format_bold["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatCapitalize", function() { return /* reexport */ format_capitalize["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatIndent", function() { return /* reexport */ format_indent["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatIndentRTL", function() { return /* reexport */ format_indent_rtl["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatItalic", function() { return /* reexport */ format_italic["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatListBullets", function() { return /* reexport */ format_list_bullets["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatListBulletsRTL", function() { return /* reexport */ format_list_bullets_rtl["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatListNumbered", function() { return /* reexport */ format_list_numbered["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatListNumberedRTL", function() { return /* reexport */ format_list_numbered_rtl["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatLtr", function() { return /* reexport */ format_ltr["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatLowercase", function() { return /* reexport */ format_lowercase["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatOutdent", function() { return /* reexport */ format_outdent["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatOutdentRTL", function() { return /* reexport */ format_outdent_rtl["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatRtl", function() { return /* reexport */ format_rtl; });
__webpack_require__.d(__webpack_exports__, "formatStrikethrough", function() { return /* reexport */ format_strikethrough["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatUnderline", function() { return /* reexport */ format_underline["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "formatUppercase", function() { return /* reexport */ format_uppercase["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "fullscreen", function() { return /* reexport */ fullscreen["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "gallery", function() { return /* reexport */ gallery["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "globe", function() { return /* reexport */ globe["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "grid", function() { return /* reexport */ grid["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "group", function() { return /* reexport */ group["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "handle", function() { return /* reexport */ library_handle; });
__webpack_require__.d(__webpack_exports__, "heading", function() { return /* reexport */ heading["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "help", function() { return /* reexport */ library_help; });
__webpack_require__.d(__webpack_exports__, "inbox", function() { return /* reexport */ library_inbox; });
__webpack_require__.d(__webpack_exports__, "institution", function() { return /* reexport */ institution; });
__webpack_require__.d(__webpack_exports__, "home", function() { return /* reexport */ library_home; });
__webpack_require__.d(__webpack_exports__, "html", function() { return /* reexport */ html["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "image", function() { return /* reexport */ library_image["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "info", function() { return /* reexport */ info["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "insertAfter", function() { return /* reexport */ insert_after; });
__webpack_require__.d(__webpack_exports__, "insertBefore", function() { return /* reexport */ insert_before; });
__webpack_require__.d(__webpack_exports__, "justifyLeft", function() { return /* reexport */ justify_left["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "justifyCenter", function() { return /* reexport */ justify_center["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "justifyRight", function() { return /* reexport */ justify_right["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "justifySpaceBetween", function() { return /* reexport */ justify_space_between["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "keyboardClose", function() { return /* reexport */ keyboard_close; });
__webpack_require__.d(__webpack_exports__, "keyboardReturn", function() { return /* reexport */ keyboard_return["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "layout", function() { return /* reexport */ layout["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "lifesaver", function() { return /* reexport */ library_lifesaver; });
__webpack_require__.d(__webpack_exports__, "link", function() { return /* reexport */ library_link["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "linkOff", function() { return /* reexport */ link_off["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "list", function() { return /* reexport */ list["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "listView", function() { return /* reexport */ list_view["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "lock", function() { return /* reexport */ library_lock; });
__webpack_require__.d(__webpack_exports__, "login", function() { return /* reexport */ login["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "loop", function() { return /* reexport */ loop["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "mapMarker", function() { return /* reexport */ map_marker["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "media", function() { return /* reexport */ media["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "mediaAndText", function() { return /* reexport */ media_and_text["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "megaphone", function() { return /* reexport */ library_megaphone; });
__webpack_require__.d(__webpack_exports__, "menu", function() { return /* reexport */ menu["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "minus", function() { return /* reexport */ library_minus; });
__webpack_require__.d(__webpack_exports__, "mobile", function() { return /* reexport */ mobile; });
__webpack_require__.d(__webpack_exports__, "more", function() { return /* reexport */ more["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "moreHorizontal", function() { return /* reexport */ more_horizontal["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "moreHorizontalMobile", function() { return /* reexport */ more_horizontal_mobile; });
__webpack_require__.d(__webpack_exports__, "moreVertical", function() { return /* reexport */ more_vertical["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "moveTo", function() { return /* reexport */ move_to; });
__webpack_require__.d(__webpack_exports__, "navigation", function() { return /* reexport */ library_navigation; });
__webpack_require__.d(__webpack_exports__, "overlayText", function() { return /* reexport */ overlay_text["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "pageBreak", function() { return /* reexport */ page_break["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "customLink", function() { return /* reexport */ custom_link; });
__webpack_require__.d(__webpack_exports__, "page", function() { return /* reexport */ page["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "pages", function() { return /* reexport */ pages["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "paragraph", function() { return /* reexport */ paragraph["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "payment", function() { return /* reexport */ library_payment; });
__webpack_require__.d(__webpack_exports__, "percent", function() { return /* reexport */ library_percent; });
__webpack_require__.d(__webpack_exports__, "positionCenter", function() { return /* reexport */ position_center["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "positionLeft", function() { return /* reexport */ position_left["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "positionRight", function() { return /* reexport */ position_right["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "pencil", function() { return /* reexport */ pencil["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "people", function() { return /* reexport */ library_people; });
__webpack_require__.d(__webpack_exports__, "pin", function() { return /* reexport */ pin["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "plugins", function() { return /* reexport */ plugins["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "plusCircleFilled", function() { return /* reexport */ plus_circle_filled; });
__webpack_require__.d(__webpack_exports__, "plusCircle", function() { return /* reexport */ plus_circle; });
__webpack_require__.d(__webpack_exports__, "plus", function() { return /* reexport */ plus["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "postCategories", function() { return /* reexport */ post_categories["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "postContent", function() { return /* reexport */ post_content["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "postComments", function() { return /* reexport */ post_comments; });
__webpack_require__.d(__webpack_exports__, "postCommentsCount", function() { return /* reexport */ post_comments_count; });
__webpack_require__.d(__webpack_exports__, "postCommentsForm", function() { return /* reexport */ post_comments_form; });
__webpack_require__.d(__webpack_exports__, "postDate", function() { return /* reexport */ post_date["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "postExcerpt", function() { return /* reexport */ post_excerpt["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "postFeaturedImage", function() { return /* reexport */ post_featured_image["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "postList", function() { return /* reexport */ post_list["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "postTitle", function() { return /* reexport */ post_title["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "previous", function() { return /* reexport */ library_previous; });
__webpack_require__.d(__webpack_exports__, "next", function() { return /* reexport */ library_next; });
__webpack_require__.d(__webpack_exports__, "preformatted", function() { return /* reexport */ preformatted["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "pullLeft", function() { return /* reexport */ pull_left["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "pullRight", function() { return /* reexport */ pull_right["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "pullquote", function() { return /* reexport */ pullquote["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "queryPagination", function() { return /* reexport */ query_pagination["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "queryPaginationNext", function() { return /* reexport */ query_pagination_next["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "queryPaginationNumbers", function() { return /* reexport */ query_pagination_numbers["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "queryPaginationPrevious", function() { return /* reexport */ query_pagination_previous["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "quote", function() { return /* reexport */ quote["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "receipt", function() { return /* reexport */ library_receipt; });
__webpack_require__.d(__webpack_exports__, "redo", function() { return /* reexport */ redo["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "replace", function() { return /* reexport */ library_replace; });
__webpack_require__.d(__webpack_exports__, "resizeCornerNE", function() { return /* reexport */ resize_corner_n_e["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "reusableBlock", function() { return /* reexport */ reusable_block["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "rotateLeft", function() { return /* reexport */ rotate_left; });
__webpack_require__.d(__webpack_exports__, "rotateRight", function() { return /* reexport */ rotate_right["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "rss", function() { return /* reexport */ rss["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "search", function() { return /* reexport */ search["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "separator", function() { return /* reexport */ separator["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "settings", function() { return /* reexport */ settings["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "share", function() { return /* reexport */ share["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "siteLogo", function() { return /* reexport */ site_logo["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "shortcode", function() { return /* reexport */ shortcode["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "stack", function() { return /* reexport */ stack["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "starEmpty", function() { return /* reexport */ star_empty["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "starFilled", function() { return /* reexport */ star_filled["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "starHalf", function() { return /* reexport */ star_half["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ library_store; });
__webpack_require__.d(__webpack_exports__, "stretchFullWidth", function() { return /* reexport */ stretch_full_width["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "shipping", function() { return /* reexport */ library_shipping; });
__webpack_require__.d(__webpack_exports__, "stretchWide", function() { return /* reexport */ stretch_wide["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "subscript", function() { return /* reexport */ subscript["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "superscript", function() { return /* reexport */ superscript["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "swatch", function() { return /* reexport */ swatch["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "tableColumnAfter", function() { return /* reexport */ table_column_after["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "tableColumnBefore", function() { return /* reexport */ table_column_before["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "tableColumnDelete", function() { return /* reexport */ table_column_delete["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "tableRowAfter", function() { return /* reexport */ table_row_after["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "tableRowBefore", function() { return /* reexport */ table_row_before["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "tableRowDelete", function() { return /* reexport */ table_row_delete["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "table", function() { return /* reexport */ table["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "tag", function() { return /* reexport */ tag["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "termDescription", function() { return /* reexport */ term_description; });
__webpack_require__.d(__webpack_exports__, "footer", function() { return /* reexport */ library_footer; });
__webpack_require__.d(__webpack_exports__, "header", function() { return /* reexport */ library_header; });
__webpack_require__.d(__webpack_exports__, "sidebar", function() { return /* reexport */ library_sidebar; });
__webpack_require__.d(__webpack_exports__, "textColor", function() { return /* reexport */ text_color["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "tablet", function() { return /* reexport */ library_tablet; });
__webpack_require__.d(__webpack_exports__, "title", function() { return /* reexport */ library_title; });
__webpack_require__.d(__webpack_exports__, "tool", function() { return /* reexport */ library_tool; });
__webpack_require__.d(__webpack_exports__, "trash", function() { return /* reexport */ library_trash; });
__webpack_require__.d(__webpack_exports__, "typography", function() { return /* reexport */ library_typography; });
__webpack_require__.d(__webpack_exports__, "undo", function() { return /* reexport */ undo["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "ungroup", function() { return /* reexport */ ungroup["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "update", function() { return /* reexport */ library_update; });
__webpack_require__.d(__webpack_exports__, "upload", function() { return /* reexport */ upload["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "verse", function() { return /* reexport */ verse["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "video", function() { return /* reexport */ video["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "warning", function() { return /* reexport */ library_warning; });
__webpack_require__.d(__webpack_exports__, "widget", function() { return /* reexport */ library_widget; });
__webpack_require__.d(__webpack_exports__, "wordpress", function() { return /* reexport */ wordpress["a" /* default */]; });

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
var icon = __webpack_require__("iClF");

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","primitives"]
var external_wp_primitives_ = __webpack_require__("Tqx9");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/add-submenu.js


/**
 * WordPress dependencies
 */

const addSubmenu = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M2 12c0 3.6 2.4 5.5 6 5.5h.5V19l3-2.5-3-2.5v2H8c-2.5 0-4.5-1.5-4.5-4s2-4.5 4.5-4.5h3.5V6H8c-3.6 0-6 2.4-6 6zm19.5-1h-8v1.5h8V11zm0 5h-8v1.5h8V16zm0-10h-8v1.5h8V6z"
}));
/* harmony default export */ var add_submenu = (addSubmenu);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/align-center.js
var align_center = __webpack_require__("plpT");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/align-justify.js


/**
 * WordPress dependencies
 */

const alignJustify = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "https://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M4 12.8h16v-1.5H4v1.5zm0 7h12.4v-1.5H4v1.5zM4 4.3v1.5h16V4.3H4z"
}));
/* harmony default export */ var align_justify = (alignJustify);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/align-left.js
var align_left = __webpack_require__("fPbg");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/align-right.js
var align_right = __webpack_require__("ziDm");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/archive.js
var archive = __webpack_require__("f88B");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/archive-title.js
var archive_title = __webpack_require__("lvzv");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/arrow-down.js


/**
 * WordPress dependencies
 */

const arrowDown = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M9 2h2v12l4-4 2 1-7 7-7-7 2-1 4 4V2z"
}));
/* harmony default export */ var arrow_down = (arrowDown);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/arrow-left.js
var arrow_left = __webpack_require__("cjQ8");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/arrow-right.js


/**
 * WordPress dependencies
 */

const arrowRight = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M2 11V9h12l-4-4 1-2 7 7-7 7-1-2 4-4H2z"
}));
/* harmony default export */ var arrow_right = (arrowRight);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/arrow-up.js


/**
 * WordPress dependencies
 */

const arrowUp = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M11 18H9V6l-4 4-2-1 7-7 7 7-2 1-4-4v12z"
}));
/* harmony default export */ var arrow_up = (arrowUp);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/at-symbol.js


/**
 * WordPress dependencies
 */

const atSymbol = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  width: "24",
  height: "24",
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M12.5939 21C14.1472 21 16.1269 20.5701 17.0711 20.1975L16.6447 18.879C16.0964 19.051 14.3299 19.6242 12.6548 19.6242C7.4467 19.6242 4.67513 16.8726 4.67513 12C4.67513 7.21338 7.50762 4.34713 12.2893 4.34713C17.132 4.34713 19.4162 7.55732 19.4162 10.7675C19.4162 14.035 19.0508 15.4968 17.4975 15.4968C16.5838 15.4968 16.0964 14.7803 16.0964 13.9777V7.5H14.4822V8.30255H14.3909C14.1777 7.67198 12.9898 7.12739 11.467 7.2707C9.18274 7.5 7.4467 9.27707 7.4467 11.8567C7.4467 14.5796 8.81726 16.672 11.467 16.758C13.203 16.8153 14.1168 16.0127 14.4822 15.1815H14.5736C14.7563 16.414 16.401 16.8439 17.467 16.8439C20.6954 16.8439 21 13.5764 21 10.7962C21 6.86943 18.0761 3 12.3807 3C6.50254 3 3 6.3535 3 11.9427C3 17.7325 6.38071 21 12.5939 21ZM11.7107 15.2962C9.73096 15.2962 9.03046 13.6051 9.03046 11.7707C9.03046 10.1083 10.0355 8.67516 11.7716 8.67516C13.599 8.67516 14.5736 9.36306 14.5736 11.7707C14.5736 14.1497 13.7513 15.2962 11.7107 15.2962Z"
}));
/* harmony default export */ var at_symbol = (atSymbol);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/aspect-ratio.js
var aspect_ratio = __webpack_require__("R7Ei");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/audio.js
var audio = __webpack_require__("UXLW");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/backup.js
var backup = __webpack_require__("Y39O");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/block-default.js
var block_default = __webpack_require__("//Lo");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/block-table.js
var block_table = __webpack_require__("3Nnr");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/box.js


/**
 * WordPress dependencies
 */

const box = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M5 5.5h14a.5.5 0 01.5.5v1.5a.5.5 0 01-.5.5H5a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5zM4 9.232A2 2 0 013 7.5V6a2 2 0 012-2h14a2 2 0 012 2v1.5a2 2 0 01-1 1.732V18a2 2 0 01-2 2H6a2 2 0 01-2-2V9.232zm1.5.268V18a.5.5 0 00.5.5h12a.5.5 0 00.5-.5V9.5h-13z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_box = (box);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/brush.js


/**
 * WordPress dependencies
 */

const brush = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M18.33 3.57s.27-.8-.31-1.36c-.53-.52-1.22-.24-1.22-.24-.61.3-5.76 3.47-7.67 5.57-.86.96-2.06 3.79-1.09 4.82.92.98 3.96-.17 4.79-1 2.06-2.06 5.21-7.17 5.5-7.79zM1.4 17.65c2.37-1.56 1.46-3.41 3.23-4.64.93-.65 2.22-.62 3.08.29.63.67.8 2.57-.16 3.46-1.57 1.45-4 1.55-6.15.89z"
}));
/* harmony default export */ var library_brush = (brush);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/button.js
var library_button = __webpack_require__("oMoS");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/buttons.js
var buttons = __webpack_require__("0YTw");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/calendar.js
var calendar = __webpack_require__("oOn3");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/camera.js


/**
 * WordPress dependencies
 */

const camera = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M6 5V3H3v2h3zm12 10V4H9L7 6H2v9h16zm-7-8c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3z"
}));
/* harmony default export */ var library_camera = (camera);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/cancel-circle-filled.js


/**
 * WordPress dependencies
 */

const cancelCircleFilled = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21ZM15.5303 8.46967C15.8232 8.76256 15.8232 9.23744 15.5303 9.53033L13.0607 12L15.5303 14.4697C15.8232 14.7626 15.8232 15.2374 15.5303 15.5303C15.2374 15.8232 14.7626 15.8232 14.4697 15.5303L12 13.0607L9.53033 15.5303C9.23744 15.8232 8.76256 15.8232 8.46967 15.5303C8.17678 15.2374 8.17678 14.7626 8.46967 14.4697L10.9393 12L8.46967 9.53033C8.17678 9.23744 8.17678 8.76256 8.46967 8.46967C8.76256 8.17678 9.23744 8.17678 9.53033 8.46967L12 10.9393L14.4697 8.46967C14.7626 8.17678 15.2374 8.17678 15.5303 8.46967Z"
}));
/* harmony default export */ var cancel_circle_filled = (cancelCircleFilled);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/capture-photo.js


/**
 * WordPress dependencies
 */

const capturePhoto = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M12 9.2c-2.2 0-3.9 1.8-3.9 4s1.8 4 3.9 4 4-1.8 4-4-1.8-4-4-4zm0 6.5c-1.4 0-2.4-1.1-2.4-2.5s1.1-2.5 2.4-2.5 2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5zM20.2 8c-.1 0-.3 0-.5-.1l-2.5-.8c-.4-.1-.8-.4-1.1-.8l-1-1.5c-.4-.5-1-.9-1.7-.9h-2.9c-.6.1-1.2.4-1.6 1l-1 1.5c-.3.3-.6.6-1.1.7l-2.5.8c-.2.1-.4.1-.6.1-1 .2-1.7.9-1.7 1.9v8.3c0 1 .9 1.9 2 1.9h16c1.1 0 2-.8 2-1.9V9.9c0-1-.7-1.7-1.8-1.9zm.3 10.1c0 .2-.2.4-.5.4H4c-.3 0-.5-.2-.5-.4V9.9c0-.1.2-.3.5-.4.2 0 .5-.1.8-.2l2.5-.8c.7-.2 1.4-.6 1.8-1.3l1-1.5c.1-.1.2-.2.4-.2h2.9c.2 0 .3.1.4.2l1 1.5c.4.7 1.1 1.1 1.9 1.4l2.5.8c.3.1.6.1.8.2.3 0 .4.2.4.4v8.1z"
}));
/* harmony default export */ var capture_photo = (capturePhoto);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/capture-video.js


/**
 * WordPress dependencies
 */

const captureVideo = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M14 5H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm.5 12c0 .3-.2.5-.5.5H4c-.3 0-.5-.2-.5-.5V7c0-.3.2-.5.5-.5h10c.3 0 .5.2.5.5v10zm2.5-7v4l5 3V7l-5 3zm3.5 4.4l-2-1.2v-2.3l2-1.2v4.7z"
}));
/* harmony default export */ var capture_video = (captureVideo);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/category.js
var category = __webpack_require__("CQWb");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chart-bar.js


/**
 * WordPress dependencies
 */

const chartBar = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M11.25 5h1.5v15h-1.5V5zM6 10h1.5v10H6V10zm12 4h-1.5v6H18v-6z",
  clipRule: "evenodd"
}));
/* harmony default export */ var chart_bar = (chartBar);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chart-line.js


/**
 * WordPress dependencies
 */

const chartLine = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M18 3.5c0 .62-.38 1.16-.92 1.38v13.11H1.99l4.22-6.73c-.13-.23-.21-.48-.21-.76C6 9.67 6.67 9 7.5 9S9 9.67 9 10.5c0 .13-.02.25-.05.37l1.44.63c.27-.3.67-.5 1.11-.5.18 0 .35.04.51.09l3.58-6.41c-.36-.27-.59-.7-.59-1.18 0-.83.67-1.5 1.5-1.5.19 0 .36.04.53.1l.05-.09v.11c.54.22.92.76.92 1.38zm-1.92 13.49V5.85l-3.29 5.89c.13.23.21.48.21.76 0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5l.01-.07-1.63-.72c-.25.18-.55.29-.88.29-.18 0-.35-.04-.51-.1l-3.2 5.09h12.29z"
}));
/* harmony default export */ var chart_line = (chartLine);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js
var check = __webpack_require__("RMJe");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-down.js
var chevron_down = __webpack_require__("NWDH");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-left.js
var chevron_left = __webpack_require__("2gm7");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-right.js
var chevron_right = __webpack_require__("1iEr");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-up.js
var chevron_up = __webpack_require__("XgzB");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/classic.js
var classic = __webpack_require__("Ti6x");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close-circle-filled.js


/**
 * WordPress dependencies
 */

const closeCircleFilled = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm5 11l-3-3 3-3-2-2-3 3-3-3-2 2 3 3-3 3 2 2 3-3 3 3z"
}));
/* harmony default export */ var close_circle_filled = (closeCircleFilled);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js
var library_close = __webpack_require__("w95h");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close-small.js
var close_small = __webpack_require__("bWcr");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/cloud-upload.js
var cloud_upload = __webpack_require__("B/ly");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/cloud.js
var cloud = __webpack_require__("LS+F");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/code.js
var code = __webpack_require__("1Yn1");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/cog.js
var cog = __webpack_require__("Cg8A");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/column.js
var column = __webpack_require__("hV+6");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/columns.js
var columns = __webpack_require__("Dtd8");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/comment.js
var comment = __webpack_require__("ZWpV");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/controls-repeat.js


/**
 * WordPress dependencies
 */

const controlsRepeat = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M5 7v3l-2 1.5V5h11V3l4 3.01L14 9V7H5zm10 6v-3l2-1.5V15H6v2l-4-3.01L6 11v2h9z"
}));
/* harmony default export */ var controls_repeat = (controlsRepeat);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/cover.js
var cover = __webpack_require__("sEn/");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/create.js


/**
 * WordPress dependencies
 */

const create = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M16 11.2h-3.2V8h-1.6v3.2H8v1.6h3.2V16h1.6v-3.2H16z"
}));
/* harmony default export */ var library_create = (create);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/crop.js
var crop = __webpack_require__("/DSv");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/currency-dollar.js


/**
 * WordPress dependencies
 */

const currencyDollar = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zm-1.338 4.877c-.314.22-.412.452-.412.623 0 .171.098.403.412.623.312.218.783.377 1.338.377.825 0 1.605.233 2.198.648.59.414 1.052 1.057 1.052 1.852 0 .795-.461 1.438-1.052 1.852-.41.286-.907.486-1.448.582v.316a.75.75 0 01-1.5 0v-.316a3.64 3.64 0 01-1.448-.582c-.59-.414-1.052-1.057-1.052-1.852a.75.75 0 011.5 0c0 .171.098.403.412.623.312.218.783.377 1.338.377s1.026-.159 1.338-.377c.314-.22.412-.452.412-.623 0-.171-.098-.403-.412-.623-.312-.218-.783-.377-1.338-.377-.825 0-1.605-.233-2.198-.648-.59-.414-1.052-1.057-1.052-1.852 0-.795.461-1.438 1.052-1.852a3.64 3.64 0 011.448-.582V7.5a.75.75 0 011.5 0v.316c.54.096 1.039.296 1.448.582.59.414 1.052 1.057 1.052 1.852a.75.75 0 01-1.5 0c0-.171-.098-.403-.412-.623-.312-.218-.783-.377-1.338-.377s-1.026.159-1.338.377z"
}));
/* harmony default export */ var currency_dollar = (currencyDollar);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/currency-euro.js


/**
 * WordPress dependencies
 */

const currencyEuro = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M12 3.25a8.75 8.75 0 100 17.5 8.75 8.75 0 000-17.5zM4.75 12a7.25 7.25 0 1114.5 0 7.25 7.25 0 01-14.5 0zm9.195 1.944a2.75 2.75 0 01-4.066-.194h.621a.75.75 0 000-1.5H9.262a2.767 2.767 0 010-.5H11.5a.75.75 0 000-1.5H9.88a2.75 2.75 0 014.066-.194.75.75 0 001.06-1.061 4.25 4.25 0 00-6.88 1.255H7.5a.75.75 0 000 1.5h.258c-.01.166-.01.334 0 .5H7.5a.75.75 0 000 1.5h.626a4.25 4.25 0 006.88 1.255.75.75 0 00-1.06-1.06z"
}));
/* harmony default export */ var currency_euro = (currencyEuro);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/currency-pound.js


/**
 * WordPress dependencies
 */

const currencyPound = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zm.25 4c-.787 0-1.425.638-1.425 1.425 0 .058.014.147.069.3.04.113.088.223.147.36a26.094 26.094 0 01.173.415H12.5a.75.75 0 010 1.5h-.953c.002.047.003.095.003.144 0 .617-.236 1.168-.511 1.606h3.386a.75.75 0 010 1.5H9.35a.75.75 0 01-.452-1.349l.007-.005a4.417 4.417 0 00.596-.581c.328-.39.549-.806.549-1.171 0-.05-.002-.097-.004-.144H9.5a.75.75 0 010-1.5h.088a5.875 5.875 0 01-.106-.27 2.382 2.382 0 01-.157-.805 2.925 2.925 0 015.637-1.097.75.75 0 01-1.39.563 1.426 1.426 0 00-1.322-.891zm-3.35 5.9l.45.6-.45-.6z"
}));
/* harmony default export */ var currency_pound = (currencyPound);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/custom-post-type.js


/**
 * WordPress dependencies
 */

const customPostType = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M4 20h9v-1.5H4V20zm0-5.5V16h16v-1.5H4zm.8-4l.7.7 2-2V12h1V9.2l2 2 .7-.7-2-2H12v-1H9.2l2-2-.7-.7-2 2V4h-1v2.8l-2-2-.7.7 2 2H4v1h2.8l-2 2z"
}));
/* harmony default export */ var custom_post_type = (customPostType);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/desktop.js


/**
 * WordPress dependencies
 */

const desktop = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M20.5 16h-.7V8c0-1.1-.9-2-2-2H6.2c-1.1 0-2 .9-2 2v8h-.7c-.8 0-1.5.7-1.5 1.5h20c0-.8-.7-1.5-1.5-1.5zM5.7 8c0-.3.2-.5.5-.5h11.6c.3 0 .5.2.5.5v7.6H5.7V8z"
}));
/* harmony default export */ var library_desktop = (desktop);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/drag-handle.js
var drag_handle = __webpack_require__("xN+R");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/download.js


/**
 * WordPress dependencies
 */

const download = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M18 11.3l-1-1.1-4 4V3h-1.5v11.3L7 10.2l-1 1.1 6.2 5.8 5.8-5.8zm.5 3.7v3.5h-13V15H4v5h16v-5h-1.5z"
}));
/* harmony default export */ var library_download = (download);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/edit.js
var edit = __webpack_require__("B9Az");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/expand.js


/**
 * WordPress dependencies
 */

const expand = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M7 8h6v4H7zm-5 5v4h4l-1.2-1.2L7 12l-3.8 2.2M14 17h4v-4l-1.2 1.2L13 12l2.2 3.8M14 3l1.3 1.3L13 8l3.8-2.2L18 7V3M6 3H2v4l1.2-1.2L7 8 4.7 4.3"
}));
/* harmony default export */ var library_expand = (expand);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/external.js
var external = __webpack_require__("K+tz");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/file.js
var file = __webpack_require__("fv7J");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/flip-horizontal.js


/**
 * WordPress dependencies
 */

const flipHorizontal = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M4 6v12c0 1.1.9 2 2 2h3v-1.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h3V4H6c-1.1 0-2 .9-2 2zm7.2 16h1.5V2h-1.5v20zM15 5.5h1.5V4H15v1.5zm3.5.5H20c0-1.1-.9-2-2-2v1.5c.3 0 .5.2.5.5zm0 10.5H20v-2h-1.5v2zm0-3.5H20v-2h-1.5v2zm-.5 5.5V20c1.1 0 2-.9 2-2h-1.5c0 .3-.2.5-.5.5zM15 20h1.5v-1.5H15V20zm3.5-10.5H20v-2h-1.5v2z"
}));
/* harmony default export */ var flip_horizontal = (flipHorizontal);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/flip-vertical.js


/**
 * WordPress dependencies
 */

const flipVertical = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M2 11.2v1.5h20v-1.5H2zM5.5 6c0-.3.2-.5.5-.5h12c.3 0 .5.2.5.5v3H20V6c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v3h1.5V6zm2 14h2v-1.5h-2V20zm3.5 0h2v-1.5h-2V20zm7-1.5V20c1.1 0 2-.9 2-2h-1.5c0 .3-.2.5-.5.5zm.5-2H20V15h-1.5v1.5zM5.5 18H4c0 1.1.9 2 2 2v-1.5c-.3 0-.5-.2-.5-.5zm0-3H4v1.5h1.5V15zm9 5h2v-1.5h-2V20z"
}));
/* harmony default export */ var flip_vertical = (flipVertical);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-bold.js
var format_bold = __webpack_require__("sMMC");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-capitalize.js
var format_capitalize = __webpack_require__("uKyT");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-indent.js
var format_indent = __webpack_require__("Egjd");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-indent-rtl.js
var format_indent_rtl = __webpack_require__("Z9je");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-italic.js
var format_italic = __webpack_require__("Wdov");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-list-bullets.js
var format_list_bullets = __webpack_require__("Ud6t");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-list-bullets-rtl.js
var format_list_bullets_rtl = __webpack_require__("lpUD");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-list-numbered.js
var format_list_numbered = __webpack_require__("lpt+");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-list-numbered-rtl.js
var format_list_numbered_rtl = __webpack_require__("OxzV");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-ltr.js
var format_ltr = __webpack_require__("v3/P");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-lowercase.js
var format_lowercase = __webpack_require__("z4qt");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-outdent.js
var format_outdent = __webpack_require__("I8zU");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-outdent-rtl.js
var format_outdent_rtl = __webpack_require__("BRe6");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/format-rtl.js


/**
 * WordPress dependencies
 */

const formatRtl = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M5.52 2h7.43c.55 0 1 .45 1 1s-.45 1-1 1h-1v13c0 .55-.45 1-1 1s-1-.45-1-1V5c0-.55-.45-1-1-1s-1 .45-1 1v12c0 .55-.45 1-1 1s-1-.45-1-1v-5.96h-.43C3.02 11.04 1 9.02 1 6.52S3.02 2 5.52 2zM19 6l-5 4 5 4V6zM5.52 2h7.43c.55 0 1 .45 1 1s-.45 1-1 1h-1v13c0 .55-.45 1-1 1s-1-.45-1-1V5c0-.55-.45-1-1-1s-1 .45-1 1v12c0 .55-.45 1-1 1s-1-.45-1-1v-5.96h-.43C3.02 11.04 1 9.02 1 6.52S3.02 2 5.52 2zM19 6l-5 4 5 4V6z"
}));
/* harmony default export */ var format_rtl = (formatRtl);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-strikethrough.js
var format_strikethrough = __webpack_require__("Crq9");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-underline.js
var format_underline = __webpack_require__("nzH+");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/format-uppercase.js
var format_uppercase = __webpack_require__("Rq5i");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/fullscreen.js
var fullscreen = __webpack_require__("5SpR");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/gallery.js
var gallery = __webpack_require__("WI8J");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/globe.js
var globe = __webpack_require__("K8zD");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/grid.js
var grid = __webpack_require__("b2RC");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/group.js
var group = __webpack_require__("u6za");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/handle.js


/**
 * WordPress dependencies
 */

const handle = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M7 16.5h10V15H7v1.5zm0-9V9h10V7.5H7z"
}));
/* harmony default export */ var library_handle = (handle);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/heading.js
var heading = __webpack_require__("WKtA");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/help.js


/**
 * WordPress dependencies
 */

const help = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zM3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 8.75a1.5 1.5 0 01.167 2.99c-.465.052-.917.44-.917 1.01V14h1.5v-.845A3 3 0 109 10.25h1.5a1.5 1.5 0 011.5-1.5zM11.25 15v1.5h1.5V15h-1.5z"
}));
/* harmony default export */ var library_help = (help);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/inbox.js


/**
 * WordPress dependencies
 */

const inbox = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M6 5.5h12a.5.5 0 01.5.5v7H14a2 2 0 11-4 0H5.5V6a.5.5 0 01.5-.5zm-.5 9V18a.5.5 0 00.5.5h12a.5.5 0 00.5-.5v-3.5h-3.337a3.5 3.5 0 01-6.326 0H5.5zM4 13V6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2v-5z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_inbox = (inbox);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/institution.js


/**
 * WordPress dependencies
 */

const institute = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M18.646 9H20V8l-1-.5L12 4 5 7.5 4 8v1h14.646zm-3-1.5L12 5.677 8.354 7.5h7.292zm-7.897 9.44v-6.5h-1.5v6.5h1.5zm5-6.5v6.5h-1.5v-6.5h1.5zm5 0v6.5h-1.5v-6.5h1.5zm2.252 8.81c0 .414-.334.75-.748.75H4.752a.75.75 0 010-1.5h14.5a.75.75 0 01.749.75z",
  clipRule: "evenodd"
}));
/* harmony default export */ var institution = (institute);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/home.js


/**
 * WordPress dependencies
 */

const home = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M12 4L4 7.9V20h16V7.9L12 4zm6.5 14.5H14V13h-4v5.5H5.5V8.8L12 5.7l6.5 3.1v9.7z"
}));
/* harmony default export */ var library_home = (home);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/html.js
var html = __webpack_require__("LJOD");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/image.js
var library_image = __webpack_require__("X3CL");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/info.js
var info = __webpack_require__("FnAh");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/insert-after.js


/**
 * WordPress dependencies
 */

const insertAfter = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M9 12h2v-2h2V8h-2V6H9v2H7v2h2v2zm1 4c3.9 0 7-3.1 7-7s-3.1-7-7-7-7 3.1-7 7 3.1 7 7 7zm0-12c2.8 0 5 2.2 5 5s-2.2 5-5 5-5-2.2-5-5 2.2-5 5-5zM3 19h14v-2H3v2z"
}));
/* harmony default export */ var insert_after = (insertAfter);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/insert-before.js


/**
 * WordPress dependencies
 */

const insertBefore = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M11 8H9v2H7v2h2v2h2v-2h2v-2h-2V8zm-1-4c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 12c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zM3 1v2h14V1H3z"
}));
/* harmony default export */ var insert_before = (insertBefore);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/justify-left.js
var justify_left = __webpack_require__("kWXm");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/justify-center.js
var justify_center = __webpack_require__("DmX/");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/justify-right.js
var justify_right = __webpack_require__("suLj");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/justify-space-between.js
var justify_space_between = __webpack_require__("bTSr");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/keyboard-close.js


/**
 * WordPress dependencies
 */

const keyboardClose = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M18,0 L2,0 C0.9,0 0.01,0.9 0.01,2 L0,12 C0,13.1 0.9,14 2,14 L18,14 C19.1,14 20,13.1 20,12 L20,2 C20,0.9 19.1,0 18,0 Z M18,12 L2,12 L2,2 L18,2 L18,12 Z M9,3 L11,3 L11,5 L9,5 L9,3 Z M9,6 L11,6 L11,8 L9,8 L9,6 Z M6,3 L8,3 L8,5 L6,5 L6,3 Z M6,6 L8,6 L8,8 L6,8 L6,6 Z M3,6 L5,6 L5,8 L3,8 L3,6 Z M3,3 L5,3 L5,5 L3,5 L3,3 Z M6,9 L14,9 L14,11 L6,11 L6,9 Z M12,6 L14,6 L14,8 L12,8 L12,6 Z M12,3 L14,3 L14,5 L12,5 L12,3 Z M15,6 L17,6 L17,8 L15,8 L15,6 Z M15,3 L17,3 L17,5 L15,5 L15,3 Z M10,20 L14,16 L6,16 L10,20 Z"
}));
/* harmony default export */ var keyboard_close = (keyboardClose);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/keyboard-return.js
var keyboard_return = __webpack_require__("btIw");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/layout.js
var layout = __webpack_require__("Civd");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/lifesaver.js


/**
 * WordPress dependencies
 */

const lifesaver = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M17.375 15.656A6.47 6.47 0 0018.5 12a6.47 6.47 0 00-.943-3.374l-1.262.813c.448.749.705 1.625.705 2.561a4.977 4.977 0 01-.887 2.844l1.262.813zm-1.951 1.87l-.813-1.261A4.976 4.976 0 0112 17c-.958 0-1.852-.27-2.613-.736l-.812 1.261A6.47 6.47 0 0012 18.5a6.47 6.47 0 003.424-.974zm-8.8-1.87A6.47 6.47 0 015.5 12c0-1.235.344-2.39.943-3.373l1.261.812A4.977 4.977 0 007 12c0 1.056.328 2.036.887 2.843l-1.262.813zm2.581-7.803A4.977 4.977 0 0112 7c1.035 0 1.996.314 2.794.853l.812-1.262A6.47 6.47 0 0012 5.5a6.47 6.47 0 00-3.607 1.092l.812 1.261zM12 20a8 8 0 100-16 8 8 0 000 16zm0-4.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_lifesaver = (lifesaver);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/link.js
var library_link = __webpack_require__("Bpkj");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/link-off.js
var link_off = __webpack_require__("Mp0b");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/list.js
var list = __webpack_require__("DUrk");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/list-view.js
var list_view = __webpack_require__("OzlF");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/lock.js


/**
 * WordPress dependencies
 */

const lock = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M8 5C8 3.34315 9.34315 2 11 2H13C14.6569 2 16 3.34315 16 5V9H18C19.2624 9 20 9.9804 20 11V19C20 20.0196 19.2624 21 18 21H6C4.73763 21 4 20.0196 4 19V11C4 9.9804 4.73763 9 6 9H8V5ZM9.5 9H14.5V5C14.5 4.17157 13.8284 3.5 13 3.5H11C10.1716 3.5 9.5 4.17157 9.5 5V9Z"
}));
/* harmony default export */ var library_lock = (lock);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/login.js
var login = __webpack_require__("m1d2");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/loop.js
var loop = __webpack_require__("zI3a");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/map-marker.js
var map_marker = __webpack_require__("A2w8");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/media.js
var media = __webpack_require__("rH4q");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/media-and-text.js
var media_and_text = __webpack_require__("lRGi");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/megaphone.js


/**
 * WordPress dependencies
 */

const megaphone = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M6.863 13.644L5 13.25h-.5a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5H5L18 6.5h2V16h-2l-3.854-.815.026.008a3.75 3.75 0 01-7.31-1.549zm1.477.313a2.251 2.251 0 004.356.921l-4.356-.921zm-2.84-3.28L18.157 8h.343v6.5h-.343L5.5 11.823v-1.146z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_megaphone = (megaphone);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/menu.js
var menu = __webpack_require__("cHUN");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/minus.js


/**
 * WordPress dependencies
 */

const minus = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M4 9h12v2H4V9z"
}));
/* harmony default export */ var library_minus = (minus);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/mobile.js


/**
 * WordPress dependencies
 */

const mobile_desktop = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M15 4H9c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H9c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h6c.3 0 .5.2.5.5v12zm-4.5-.5h2V16h-2v1.5z"
}));
/* harmony default export */ var mobile = (mobile_desktop);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/more.js
var more = __webpack_require__("j5fK");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/more-horizontal.js
var more_horizontal = __webpack_require__("fZpB");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/more-horizontal-mobile.js


/**
 * WordPress dependencies
 */

const moreHorizontalMobile = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M7.5 12C7.5 11.1716 6.82843 10.5 6 10.5C5.17157 10.5 4.5 11.1716 4.5 12C4.5 12.8284 5.17157 13.5 6 13.5C6.82843 13.5 7.5 12.8284 7.5 12Z"
}), Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M13.5 12C13.5 11.1716 12.8284 10.5 12 10.5C11.1716 10.5 10.5 11.1716 10.5 12C10.5 12.8284 11.1716 13.5 12 13.5C12.8284 13.5 13.5 12.8284 13.5 12Z"
}), Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M19.5 12C19.5 11.1716 18.8284 10.5 18 10.5C17.1716 10.5 16.5 11.1716 16.5 12C16.5 12.8284 17.1716 13.5 18 13.5C18.8284 13.5 19.5 12.8284 19.5 12Z"
}));
/* harmony default export */ var more_horizontal_mobile = (moreHorizontalMobile);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/more-vertical.js
var more_vertical = __webpack_require__("VKE3");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/move-to.js


/**
 * WordPress dependencies
 */

const moveTo = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M19.75 9c0-1.257-.565-2.197-1.39-2.858-.797-.64-1.827-1.017-2.815-1.247-1.802-.42-3.703-.403-4.383-.396L11 4.5V6l.177-.001c.696-.006 2.416-.02 4.028.356.887.207 1.67.518 2.216.957.52.416.829.945.829 1.688 0 .592-.167.966-.407 1.23-.255.281-.656.508-1.236.674-1.19.34-2.82.346-4.607.346h-.077c-1.692 0-3.527 0-4.942.404-.732.209-1.424.545-1.935 1.108-.526.579-.796 1.33-.796 2.238 0 1.257.565 2.197 1.39 2.858.797.64 1.827 1.017 2.815 1.247 1.802.42 3.703.403 4.383.396L13 19.5h.714V22L18 18.5 13.714 15v3H13l-.177.001c-.696.006-2.416.02-4.028-.356-.887-.207-1.67-.518-2.216-.957-.52-.416-.829-.945-.829-1.688 0-.592.167-.966.407-1.23.255-.281.656-.508 1.237-.674 1.189-.34 2.819-.346 4.606-.346h.077c1.692 0 3.527 0 4.941-.404.732-.209 1.425-.545 1.936-1.108.526-.579.796-1.33.796-2.238z"
}));
/* harmony default export */ var move_to = (moveTo);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/navigation.js


/**
 * WordPress dependencies
 */

const navigation = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M12 4c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14.5c-3.6 0-6.5-2.9-6.5-6.5S8.4 5.5 12 5.5s6.5 2.9 6.5 6.5-2.9 6.5-6.5 6.5zM9 16l4.5-3L15 8.4l-4.5 3L9 16z"
}));
/* harmony default export */ var library_navigation = (navigation);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/overlay-text.js
var overlay_text = __webpack_require__("QHle");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/page-break.js
var page_break = __webpack_require__("c9wC");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/custom-link.js


/**
 * WordPress dependencies
 */

const customLink = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "https://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M12.5 14.5h-1V16h1c2.2 0 4-1.8 4-4s-1.8-4-4-4h-1v1.5h1c1.4 0 2.5 1.1 2.5 2.5s-1.1 2.5-2.5 2.5zm-4 1.5v-1.5h-1C6.1 14.5 5 13.4 5 12s1.1-2.5 2.5-2.5h1V8h-1c-2.2 0-4 1.8-4 4s1.8 4 4 4h1zm-1-3.2h5v-1.5h-5v1.5zM18 4H9c-1.1 0-2 .9-2 2v.5h1.5V6c0-.3.2-.5.5-.5h9c.3 0 .5.2.5.5v12c0 .3-.2.5-.5.5H9c-.3 0-.5-.2-.5-.5v-.5H7v.5c0 1.1.9 2 2 2h9c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z"
}));
/* harmony default export */ var custom_link = (customLink);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/page.js
var page = __webpack_require__("Zm8Q");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/pages.js
var pages = __webpack_require__("uVe1");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/paragraph.js
var paragraph = __webpack_require__("vlZU");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/payment.js


/**
 * WordPress dependencies
 */

const payment = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M5.5 9.5v-2h13v2h-13zm0 3v4h13v-4h-13zM4 7a1 1 0 011-1h14a1 1 0 011 1v10a1 1 0 01-1 1H5a1 1 0 01-1-1V7z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_payment = (payment);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/percent.js


/**
 * WordPress dependencies
 */

const percent = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M6.5 8a1.5 1.5 0 103 0 1.5 1.5 0 00-3 0zM8 5a3 3 0 100 6 3 3 0 000-6zm6.5 11a1.5 1.5 0 103 0 1.5 1.5 0 00-3 0zm1.5-3a3 3 0 100 6 3 3 0 000-6zM5.47 17.41a.75.75 0 001.06 1.06L18.47 6.53a.75.75 0 10-1.06-1.06L5.47 17.41z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_percent = (percent);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/position-center.js
var position_center = __webpack_require__("EpZT");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/position-left.js
var position_left = __webpack_require__("4S5K");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/position-right.js
var position_right = __webpack_require__("wa2G");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/pencil.js
var pencil = __webpack_require__("L0kB");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/people.js


/**
 * WordPress dependencies
 */

const people = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M17.5 9a2 2 0 11-4 0 2 2 0 014 0zm-4.25 8v-2a2.75 2.75 0 00-2.75-2.75h-4A2.75 2.75 0 003.75 15v2h1.5v-2c0-.69.56-1.25 1.25-1.25h4c.69 0 1.25.56 1.25 1.25v2h1.5zm7-2v2h-1.5v-2c0-.69-.56-1.25-1.25-1.25H15v-1.5h2.5A2.75 2.75 0 0120.25 15zM8.5 11a2 2 0 100-4 2 2 0 000 4z"
}));
/* harmony default export */ var library_people = (people);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/pin.js
var pin = __webpack_require__("F636");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/plugins.js
var plugins = __webpack_require__("0Ene");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/plus-circle-filled.js


/**
 * WordPress dependencies
 */

const plusCircleFilled = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M2 12C2 6.44444 6.44444 2 12 2C17.5556 2 22 6.44444 22 12C22 17.5556 17.5556 22 12 22C6.44444 22 2 17.5556 2 12ZM13 11V7H11V11H7V13H11V17H13V13H17V11H13Z"
}));
/* harmony default export */ var plus_circle_filled = (plusCircleFilled);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/plus-circle.js


/**
 * WordPress dependencies
 */

const plusCircle = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M10 1c-5 0-9 4-9 9s4 9 9 9 9-4 9-9-4-9-9-9zm0 16c-3.9 0-7-3.1-7-7s3.1-7 7-7 7 3.1 7 7-3.1 7-7 7zm1-11H9v3H6v2h3v3h2v-3h3V9h-3V6zM10 1c-5 0-9 4-9 9s4 9 9 9 9-4 9-9-4-9-9-9zm0 16c-3.9 0-7-3.1-7-7s3.1-7 7-7 7 3.1 7 7-3.1 7-7 7zm1-11H9v3H6v2h3v3h2v-3h3V9h-3V6z"
}));
/* harmony default export */ var plus_circle = (plusCircle);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/plus.js
var plus = __webpack_require__("Q4Sy");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/post-categories.js
var post_categories = __webpack_require__("CfuN");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/post-content.js
var post_content = __webpack_require__("PUI2");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/post-comments.js


/**
 * WordPress dependencies
 */

const postComments = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M14 10.1V4c0-.6-.4-1-1-1H5c-.6 0-1 .4-1 1v8.3c0 .3.2.7.6.8.1.1.2.1.3.1.2 0 .5-.1.6-.3l1.8-1.8H13c.6 0 1-.4 1-1zm-1.5-.5H6.7l-1.2 1.2V4.5h7v5.1zM19 12h-8c-.6 0-1 .4-1 1v6.1c0 .6.4 1 1 1h5.7l1.8 1.8c.1.2.4.3.6.3.1 0 .2 0 .3-.1.4-.1.6-.5.6-.8V13c0-.6-.4-1-1-1zm-.5 7.8l-1.2-1.2h-5.8v-5.1h7v6.3z"
}));
/* harmony default export */ var post_comments = (postComments);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/post-comments-count.js


/**
 * WordPress dependencies
 */

const postCommentsCount = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M13 8H4v1.5h9V8zM4 4v1.5h16V4H4zm9 8H5c-.6 0-1 .4-1 1v8.3c0 .3.2.7.6.8.1.1.2.1.3.1.2 0 .5-.1.6-.3l1.8-1.8H13c.6 0 1-.4 1-1V13c0-.6-.4-1-1-1zm-2.2 6.6H7l1.6-2.2c.3-.4.5-.7.6-.9.1-.2.2-.4.2-.5 0-.2-.1-.3-.1-.4-.1-.1-.2-.1-.4-.1s-.4 0-.6.1c-.3.1-.5.3-.7.4l-.2.2-.2-1.2.1-.1c.3-.2.5-.3.8-.4.3-.1.6-.1.9-.1.3 0 .6.1.9.2.2.1.4.3.6.5.1.2.2.5.2.7 0 .3-.1.6-.2.9-.1.3-.4.7-.7 1.1l-.5.6h1.6v1.2z"
}));
/* harmony default export */ var post_comments_count = (postCommentsCount);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/post-comments-form.js


/**
 * WordPress dependencies
 */

const postCommentsForm = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M13 8H4v1.5h9V8zM4 4v1.5h16V4H4zm9 8H5c-.6 0-1 .4-1 1v8.3c0 .3.2.7.6.8.1.1.2.1.3.1.2 0 .5-.1.6-.3l1.8-1.8H13c.6 0 1-.4 1-1V13c0-.6-.4-1-1-1zm-.5 6.6H6.7l-1.2 1.2v-6.3h7v5.1z"
}));
/* harmony default export */ var post_comments_form = (postCommentsForm);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/post-date.js
var post_date = __webpack_require__("eZYK");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/post-excerpt.js
var post_excerpt = __webpack_require__("7WSE");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/post-featured-image.js
var post_featured_image = __webpack_require__("uxcH");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/post-list.js
var post_list = __webpack_require__("skgM");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/post-title.js
var post_title = __webpack_require__("d48J");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/previous.js


/**
 * WordPress dependencies
 */

const previous = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M11.6 7l-1.1-1L5 12l5.5 6 1.1-1L7 12l4.6-5zm6 0l-1.1-1-5.5 6 5.5 6 1.1-1-4.6-5 4.6-5z"
}));
/* harmony default export */ var library_previous = (previous);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/next.js


/**
 * WordPress dependencies
 */

const next = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M6.6 6L5.4 7l4.5 5-4.5 5 1.1 1 5.5-6-5.4-6zm6 0l-1.1 1 4.5 5-4.5 5 1.1 1 5.5-6-5.5-6z"
}));
/* harmony default export */ var library_next = (next);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/preformatted.js
var preformatted = __webpack_require__("RD9Y");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/pull-left.js
var pull_left = __webpack_require__("yRk8");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/pull-right.js
var pull_right = __webpack_require__("WR/0");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/pullquote.js
var pullquote = __webpack_require__("9T6D");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/query-pagination.js
var query_pagination = __webpack_require__("CPWL");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/query-pagination-next.js
var query_pagination_next = __webpack_require__("Tj2E");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/query-pagination-numbers.js
var query_pagination_numbers = __webpack_require__("MOiY");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/query-pagination-previous.js
var query_pagination_previous = __webpack_require__("tK8V");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/quote.js
var quote = __webpack_require__("ITpV");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/receipt.js


/**
 * WordPress dependencies
 */

const receipt = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M16.83 6.342l.602.3.625-.25.443-.176v12.569l-.443-.178-.625-.25-.603.301-1.444.723-2.41-.804-.475-.158-.474.158-2.41.803-1.445-.722-.603-.3-.625.25-.443.177V6.215l.443.178.625.25.603-.301 1.444-.722 2.41.803.475.158.474-.158 2.41-.803 1.445.722zM20 4l-1.5.6-1 .4-2-1-3 1-3-1-2 1-1-.4L5 4v17l1.5-.6 1-.4 2 1 3-1 3 1 2-1 1 .4 1.5.6V4zm-3.5 6.25v-1.5h-8v1.5h8zm0 3v-1.5h-8v1.5h8zm-8 3v-1.5h8v1.5h-8z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_receipt = (receipt);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/redo.js
var redo = __webpack_require__("K2cm");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/replace.js


/**
 * WordPress dependencies
 */

const replace = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M16 10h4c.6 0 1-.4 1-1V5c0-.6-.4-1-1-1h-4c-.6 0-1 .4-1 1v4c0 .6.4 1 1 1zm-8 4H4c-.6 0-1 .4-1 1v4c0 .6.4 1 1 1h4c.6 0 1-.4 1-1v-4c0-.6-.4-1-1-1zm10-2.6L14.5 15l1.1 1.1 1.7-1.7c-.1 1.1-.3 2.3-.9 2.9-.3.3-.7.5-1.3.5h-4.5v1.5H15c.9 0 1.7-.3 2.3-.9 1-1 1.3-2.7 1.4-4l1.8 1.8 1.1-1.1-3.6-3.7zM6.8 9.7c.1-1.1.3-2.3.9-2.9.4-.4.8-.6 1.3-.6h4.5V4.8H9c-.9 0-1.7.3-2.3.9-1 1-1.3 2.7-1.4 4L3.5 8l-1 1L6 12.6 9.5 9l-1-1-1.7 1.7z"
}));
/* harmony default export */ var library_replace = (replace);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/resize-corner-n-e.js
var resize_corner_n_e = __webpack_require__("+Iak");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/reusable-block.js
var reusable_block = __webpack_require__("SRG5");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/rotate-left.js


/**
 * WordPress dependencies
 */

const rotateLeft = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M12 4V2.2L9 4.8l3 2.5V5.5c3.6 0 6.5 2.9 6.5 6.5 0 2.9-1.9 5.3-4.5 6.2v.2l-.1-.2c-.4.1-.7.2-1.1.2l.2 1.5c.3 0 .6-.1 1-.2 3.5-.9 6-4 6-7.7 0-4.4-3.6-8-8-8zm-7.9 7l1.5.2c.1-1.2.5-2.3 1.2-3.2l-1.1-.9C4.8 8.2 4.3 9.6 4.1 11zm1.5 1.8l-1.5.2c.1.7.3 1.4.5 2 .3.7.6 1.3 1 1.8l1.2-.8c-.3-.5-.6-1-.8-1.5s-.4-1.1-.4-1.7zm1.5 5.5c1.1.9 2.4 1.4 3.8 1.6l.2-1.5c-1.1-.1-2.2-.5-3.1-1.2l-.9 1.1z"
}));
/* harmony default export */ var rotate_left = (rotateLeft);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/rotate-right.js
var rotate_right = __webpack_require__("XyZy");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/rss.js
var rss = __webpack_require__("FAIV");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/search.js
var search = __webpack_require__("cGtP");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/separator.js
var separator = __webpack_require__("Esdn");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/settings.js
var settings = __webpack_require__("mi6C");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/share.js
var share = __webpack_require__("jMvP");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/site-logo.js
var site_logo = __webpack_require__("wszK");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/shortcode.js
var shortcode = __webpack_require__("91dd");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/stack.js
var stack = __webpack_require__("wzfx");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/star-empty.js
var star_empty = __webpack_require__("Xxwi");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/star-filled.js
var star_filled = __webpack_require__("URob");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/star-half.js
var star_half = __webpack_require__("WIVa");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/store.js


/**
 * WordPress dependencies
 */

const store = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M19.75 11H21V8.667L19.875 4H4.125L3 8.667V11h1.25v8.75h15.5V11zm-1.5 0H5.75v7.25H10V13h4v5.25h4.25V11zm-5.5-5.5h2.067l.486 3.24.028.76H12.75v-4zm-3.567 0h2.067v4H8.669l.028-.76.486-3.24zm7.615 3.1l-.464-3.1h2.36l.806 3.345V9.5h-2.668l-.034-.9zM7.666 5.5h-2.36L4.5 8.845V9.5h2.668l.034-.9.464-3.1z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_store = (store);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/stretch-full-width.js
var stretch_full_width = __webpack_require__("XNw2");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/shipping.js


/**
 * WordPress dependencies
 */

const shipping = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M3 6.75C3 5.784 3.784 5 4.75 5H15V7.313l.05.027 5.056 2.73.394.212v3.468a1.75 1.75 0 01-1.75 1.75h-.012a2.5 2.5 0 11-4.975 0H9.737a2.5 2.5 0 11-4.975 0H3V6.75zM13.5 14V6.5H4.75a.25.25 0 00-.25.25V14h.965a2.493 2.493 0 011.785-.75c.7 0 1.332.287 1.785.75H13.5zm4.535 0h.715a.25.25 0 00.25-.25v-2.573l-4-2.16v4.568a2.487 2.487 0 011.25-.335c.7 0 1.332.287 1.785.75zM6.282 15.5a1.002 1.002 0 00.968 1.25 1 1 0 10-.968-1.25zm9 0a1 1 0 101.937.498 1 1 0 00-1.938-.498z"
}));
/* harmony default export */ var library_shipping = (shipping);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/stretch-wide.js
var stretch_wide = __webpack_require__("beZb");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/subscript.js
var subscript = __webpack_require__("S1+T");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/superscript.js
var superscript = __webpack_require__("EgtK");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/swatch.js
var swatch = __webpack_require__("qE/0");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/table-column-after.js
var table_column_after = __webpack_require__("nGWI");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/table-column-before.js
var table_column_before = __webpack_require__("C5ux");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/table-column-delete.js
var table_column_delete = __webpack_require__("FtXe");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/table-row-after.js
var table_row_after = __webpack_require__("ONOV");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/table-row-before.js
var table_row_before = __webpack_require__("dFuC");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/table-row-delete.js
var table_row_delete = __webpack_require__("eRGe");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/table.js
var table = __webpack_require__("4DNc");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/tag.js
var tag = __webpack_require__("6iTo");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/term-description.js


/**
 * WordPress dependencies
 */

const term_description_tag = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  stroke: "#1E1E1E",
  strokeWidth: "1.5",
  d: "M9 19.25h6M4 19.25h4M12 15.25h8M4 15.25h7"
}), Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M8.994 10.103H6.08L5.417 12H4l2.846-8h1.383l2.845 8H9.657l-.663-1.897zm-.457-1.28l-.994-2.857-1.006 2.857h2z",
  fill: "#1E1E1E"
}));
/* harmony default export */ var term_description = (term_description_tag);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/footer.js


/**
 * WordPress dependencies
 */

const footer = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M18 5.5h-8v8h8.5V6a.5.5 0 00-.5-.5zm-9.5 8h-3V6a.5.5 0 01.5-.5h2.5v8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ var library_footer = (footer);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/header.js


/**
 * WordPress dependencies
 */

const header = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M18.5 10.5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ var library_header = (header);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/sidebar.js


/**
 * WordPress dependencies
 */

const sidebar = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ var library_sidebar = (sidebar);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/text-color.js
var text_color = __webpack_require__("uGfJ");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/tablet.js


/**
 * WordPress dependencies
 */

const tablet = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M17 4H7c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H7c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h10c.3 0 .5.2.5.5v12zm-7.5-.5h4V16h-4v1.5z"
}));
/* harmony default export */ var library_tablet = (tablet);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/title.js


/**
 * WordPress dependencies
 */

const title = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "https://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M5 4v3h5.5v12h3V7H19V4H5z"
}));
/* harmony default export */ var library_title = (title);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/tool.js


/**
 * WordPress dependencies
 */

const tool = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M14.103 7.128l2.26-2.26a4 4 0 00-5.207 4.804L5.828 15a2 2 0 102.828 2.828l5.329-5.328a4 4 0 004.804-5.208l-2.261 2.26-1.912-.512-.513-1.912zm-7.214 9.64a.5.5 0 11.707-.707.5.5 0 01-.707.707z"
}));
/* harmony default export */ var library_tool = (tool);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/trash.js


/**
 * WordPress dependencies
 */

const trash = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M20 5h-5.7c0-1.3-1-2.3-2.3-2.3S9.7 3.7 9.7 5H4v2h1.5v.3l1.7 11.1c.1 1 1 1.7 2 1.7h5.7c1 0 1.8-.7 2-1.7l1.7-11.1V7H20V5zm-3.2 2l-1.7 11.1c0 .1-.1.2-.3.2H9.1c-.1 0-.3-.1-.3-.2L7.2 7h9.6z"
}));
/* harmony default export */ var library_trash = (trash);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/typography.js


/**
 * WordPress dependencies
 */

const typography = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M6.9 7L3 17.8h1.7l1-2.8h4.1l1 2.8h1.7L8.6 7H6.9zm-.7 6.6l1.5-4.3 1.5 4.3h-3zM21.6 17c-.1.1-.2.2-.3.2-.1.1-.2.1-.4.1s-.3-.1-.4-.2c-.1-.1-.1-.3-.1-.6V12c0-.5 0-1-.1-1.4-.1-.4-.3-.7-.5-1-.2-.2-.5-.4-.9-.5-.4 0-.8-.1-1.3-.1s-1 .1-1.4.2c-.4.1-.7.3-1 .4-.2.2-.4.3-.6.5-.1.2-.2.4-.2.7 0 .3.1.5.2.8.2.2.4.3.8.3.3 0 .6-.1.8-.3.2-.2.3-.4.3-.7 0-.3-.1-.5-.2-.7-.2-.2-.4-.3-.6-.4.2-.2.4-.3.7-.4.3-.1.6-.1.8-.1.3 0 .6 0 .8.1.2.1.4.3.5.5.1.2.2.5.2.9v1.1c0 .3-.1.5-.3.6-.2.2-.5.3-.9.4-.3.1-.7.3-1.1.4-.4.1-.8.3-1.1.5-.3.2-.6.4-.8.7-.2.3-.3.7-.3 1.2 0 .6.2 1.1.5 1.4.3.4.9.5 1.6.5.5 0 1-.1 1.4-.3.4-.2.8-.6 1.1-1.1 0 .4.1.7.3 1 .2.3.6.4 1.2.4.4 0 .7-.1.9-.2.2-.1.5-.3.7-.4h-.3zm-3-.9c-.2.4-.5.7-.8.8-.3.2-.6.2-.8.2-.4 0-.6-.1-.9-.3-.2-.2-.3-.6-.3-1.1 0-.5.1-.9.3-1.2s.5-.5.8-.7c.3-.2.7-.3 1-.5.3-.1.6-.3.7-.6v3.4z"
}));
/* harmony default export */ var library_typography = (typography);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/undo.js
var undo = __webpack_require__("Ntru");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/ungroup.js
var ungroup = __webpack_require__("UXXq");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/update.js


/**
 * WordPress dependencies
 */

const update = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M10.2 3.28c3.53 0 6.43 2.61 6.92 6h2.08l-3.5 4-3.5-4h2.32c-.45-1.97-2.21-3.45-4.32-3.45-1.45 0-2.73.71-3.54 1.78L4.95 5.66C6.23 4.2 8.11 3.28 10.2 3.28zm-.4 13.44c-3.52 0-6.43-2.61-6.92-6H.8l3.5-4c1.17 1.33 2.33 2.67 3.5 4H5.48c.45 1.97 2.21 3.45 4.32 3.45 1.45 0 2.73-.71 3.54-1.78l1.71 1.95c-1.28 1.46-3.15 2.38-5.25 2.38z"
}));
/* harmony default export */ var library_update = (update);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/upload.js
var upload = __webpack_require__("NTP4");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/verse.js
var verse = __webpack_require__("OI5I");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/video.js
var video = __webpack_require__("Qtmx");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/warning.js


/**
 * WordPress dependencies
 */

const warning = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm1.13 9.38l.35-6.46H8.52l.35 6.46h2.26zm-.09 3.36c.24-.23.37-.55.37-.96 0-.42-.12-.74-.36-.97s-.59-.35-1.06-.35-.82.12-1.07.35-.37.55-.37.97c0 .41.13.73.38.96.26.23.61.34 1.06.34s.8-.11 1.05-.34z"
}));
/* harmony default export */ var library_warning = (warning);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/widget.js


/**
 * WordPress dependencies
 */

const widget = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M7 11h2v2H7v-2zm14-5v14l-2 2H5l-2-2V6l2-2h1V2h2v2h8V2h2v2h1l2 2zM5 8h14V6H5v2zm14 12V10H5v10h14zm-4-7h2v-2h-2v2zm-4 0h2v-2h-2v2z"
}));
/* harmony default export */ var library_widget = (widget);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/wordpress.js
var wordpress = __webpack_require__("wduq");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/index.js

























































































































































































































/***/ }),

/***/ "bTSr":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const justifySpaceBetween = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M9 15h6V9H9v6zm-5 5h1.5V4H4v16zM18.5 4v16H20V4h-1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (justifySpaceBetween);


/***/ }),

/***/ "bWcr":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const closeSmall = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
}));
/* harmony default export */ __webpack_exports__["a"] = (closeSmall);


/***/ }),

/***/ "beZb":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const stretchWide = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5 9v6h14V9H5zm11-4.8H8v1.5h8V4.2zM8 19.8h8v-1.5H8v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (stretchWide);


/***/ }),

/***/ "btIw":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const keyboardReturn = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6.734 16.106l2.176-2.38-1.093-1.028-3.846 4.158 3.846 4.157 1.093-1.027-2.176-2.38h2.811c1.125 0 2.25.03 3.374 0 1.428-.001 3.362-.25 4.963-1.277 1.66-1.065 2.868-2.906 2.868-5.859 0-2.479-1.327-4.896-3.65-5.93-1.82-.813-3.044-.8-4.806-.788l-.567.002v1.5c.184 0 .368 0 .553-.002 1.82-.007 2.704-.014 4.21.657 1.854.827 2.76 2.657 2.76 4.561 0 2.472-.973 3.824-2.178 4.596-1.258.807-2.864 1.04-4.163 1.04h-.02c-1.115.03-2.229 0-3.344 0H6.734z"
}));
/* harmony default export */ __webpack_exports__["a"] = (keyboardReturn);


/***/ }),

/***/ "c9wC":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const pageBreak = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M7.8 6c0-.7.6-1.2 1.2-1.2h6c.7 0 1.2.6 1.2 1.2v3h1.5V6c0-1.5-1.2-2.8-2.8-2.8H9C7.5 3.2 6.2 4.5 6.2 6v3h1.5V6zm8.4 11c0 .7-.6 1.2-1.2 1.2H9c-.7 0-1.2-.6-1.2-1.2v-3H6.2v3c0 1.5 1.2 2.8 2.8 2.8h6c1.5 0 2.8-1.2 2.8-2.8v-3h-1.5v3zM4 11v1h16v-1H4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (pageBreak);


/***/ }),

/***/ "cDcd":
/***/ (function(module, exports) {

(function() { module.exports = window["React"]; }());

/***/ }),

/***/ "cGtP":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const search = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13.5 6C10.5 6 8 8.5 8 11.5c0 1.1.3 2.1.9 3l-3.4 3 1 1.1 3.4-2.9c1 .9 2.2 1.4 3.6 1.4 3 0 5.5-2.5 5.5-5.5C19 8.5 16.5 6 13.5 6zm0 9.5c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (search);


/***/ }),

/***/ "cHUN":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const menu = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5 5.5V7h14V5.5H5zM5 13h14v-1.5H5V13zm0 6h14v-1.5H5V19z"
}));
/* harmony default export */ __webpack_exports__["a"] = (menu);


/***/ }),

/***/ "cjQ8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const arrowLeft = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M9.737 18.011L3.98 12.255l5.734-6.28 1.107 1.012-4.103 4.494h13.3v1.5H6.828l3.97 3.97-1.06 1.06z"
}));
/* harmony default export */ __webpack_exports__["a"] = (arrowLeft);


/***/ }),

/***/ "d48J":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const postTitle = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 14.5h16V16H4zM4 18.5h9V20H4zM4 4h3c2 0 3 .86 3 2.583 0 .891-.253 1.554-.76 1.988-.505.435-1.24.652-2.204.652H5.542V12H4V4zm2.855 4c.53 0 .924-.114 1.18-.343.266-.228.398-.579.398-1.051 0-.473-.132-.82-.397-1.04-.265-.229-.67-.343-1.217-.343H5.542V8h1.313z"
}));
/* harmony default export */ __webpack_exports__["a"] = (postTitle);


/***/ }),

/***/ "dFuC":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const tableRowBefore = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6.656 6.464h2.88v2.88h1.408v-2.88h2.88V5.12h-2.88V2.24H9.536v2.88h-2.88zM0 17.92V0h20.48v17.92H0zm7.68-2.56h5.12v-3.84H7.68v3.84zm-6.4 0H6.4v-3.84H1.28v3.84zM19.2 1.28H1.28v9.024H19.2V1.28zm0 10.24h-5.12v3.84h5.12v-3.84zM6.656 6.464h2.88v2.88h1.408v-2.88h2.88V5.12h-2.88V2.24H9.536v2.88h-2.88zM0 17.92V0h20.48v17.92H0zm7.68-2.56h5.12v-3.84H7.68v3.84zm-6.4 0H6.4v-3.84H1.28v3.84zM19.2 1.28H1.28v9.024H19.2V1.28zm0 10.24h-5.12v3.84h5.12v-3.84z"
}));
/* harmony default export */ __webpack_exports__["a"] = (tableRowBefore);


/***/ }),

/***/ "diJD":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["reusableBlocks"]; }());

/***/ }),

/***/ "eRGe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const tableRowDelete = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M17.728 11.456L14.592 8.32l3.2-3.2-1.536-1.536-3.2 3.2L9.92 3.648 8.384 5.12l3.2 3.2-3.264 3.264 1.536 1.536 3.264-3.264 3.136 3.136 1.472-1.536zM0 17.92V0h20.48v17.92H0zm19.2-6.4h-.448l-1.28-1.28H19.2V6.4h-1.792l1.28-1.28h.512V1.28H1.28v3.84h6.208l1.28 1.28H1.28v3.84h7.424l-1.28 1.28H1.28v3.84H19.2v-3.84z"
}));
/* harmony default export */ __webpack_exports__["a"] = (tableRowDelete);


/***/ }),

/***/ "eZYK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const postDate = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11.696 13.972c.356-.546.599-.958.728-1.235a1.79 1.79 0 00.203-.783c0-.264-.077-.47-.23-.618-.148-.153-.354-.23-.618-.23-.295 0-.569.07-.82.212a3.413 3.413 0 00-.738.571l-.147-1.188c.289-.234.59-.41.903-.526.313-.117.66-.175 1.041-.175.375 0 .695.08.959.24.264.153.46.362.59.626.135.265.203.556.203.876 0 .362-.08.734-.24 1.115-.154.381-.427.87-.82 1.466l-.756 1.152H14v1.106h-4l1.696-2.609z"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19.5 7h-15v12a.5.5 0 00.5.5h14a.5.5 0 00.5-.5V7zM3 7V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"
}));
/* harmony default export */ __webpack_exports__["a"] = (postDate);


/***/ }),

/***/ "f88B":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const archive = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 6.2h-5.9l-.6-1.1c-.3-.7-1-1.1-1.8-1.1H5c-1.1 0-2 .9-2 2v11.8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8.2c0-1.1-.9-2-2-2zm.5 11.6c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h5.8c.2 0 .4.1.4.3l1 2H19c.3 0 .5.2.5.5v9.5zM8 12.8h8v-1.5H8v1.5zm0 3h8v-1.5H8v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (archive);


/***/ }),

/***/ "fPbg":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const alignLeft = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 19.8h8.9v-1.5H4v1.5zm8.9-15.6H4v1.5h8.9V4.2zm-8.9 7v1.5h16v-1.5H4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (alignLeft);


/***/ }),

/***/ "fZpB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const moreHorizontal = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11 13h2v-2h-2v2zm-6 0h2v-2H5v2zm12-2v2h2v-2h-2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (moreHorizontal);


/***/ }),

/***/ "fv7J":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const file = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 6.2h-5.9l-.6-1.1c-.3-.7-1-1.1-1.8-1.1H5c-1.1 0-2 .9-2 2v11.8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8.2c0-1.1-.9-2-2-2zm.5 11.6c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h5.8c.2 0 .4.1.4.3l1 2H19c.3 0 .5.2.5.5v9.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (file);


/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["hooks"]; }());

/***/ }),

/***/ "hF7m":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["keyboardShortcuts"]; }());

/***/ }),

/***/ "hV+6":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const column = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 6H6c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2h13c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM6 17.5c-.3 0-.5-.2-.5-.5V8c0-.3.2-.5.5-.5h3v10H6zm13.5-.5c0 .3-.2.5-.5.5h-3v-10h3c.3 0 .5.2.5.5v9z"
}));
/* harmony default export */ __webpack_exports__["a"] = (column);


/***/ }),

/***/ "iClF":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/**
 * WordPress dependencies
 */

/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps} props icon is the SVG component to render
 *                          size is a number specifiying the icon size in pixels
 *                          Other props will be passed to wrapped SVG component
 *
 * @return {JSX.Element}  Icon component
 */

function Icon({
  icon,
  size = 24,
  ...props
}) {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["cloneElement"])(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ __webpack_exports__["a"] = (Icon);


/***/ }),

/***/ "j5fK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const more = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 9v1.5h16V9H4zm12 5.5h4V13h-4v1.5zm-6 0h4V13h-4v1.5zm-6 0h4V13H4v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (more);


/***/ }),

/***/ "jMvP":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const share = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M9 11.8l6.1-4.5c.1.4.4.7.9.7h2c.6 0 1-.4 1-1V5c0-.6-.4-1-1-1h-2c-.6 0-1 .4-1 1v.4l-6.4 4.8c-.2-.1-.4-.2-.6-.2H6c-.6 0-1 .4-1 1v2c0 .6.4 1 1 1h2c.2 0 .4-.1.6-.2l6.4 4.8v.4c0 .6.4 1 1 1h2c.6 0 1-.4 1-1v-2c0-.6-.4-1-1-1h-2c-.5 0-.8.3-.9.7L9 12.2v-.4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (share);


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

(function() { module.exports = window["wp"]["coreData"]; }());

/***/ }),

/***/ "kWXm":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const justifyLeft = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M9 9v6h11V9H9zM4 20h1.5V4H4v16z"
}));
/* harmony default export */ __webpack_exports__["a"] = (justifyLeft);


/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "lRGi":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const mediaAndText = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M3 18h8V6H3v12zM14 7.5V9h7V7.5h-7zm0 5.3h7v-1.5h-7v1.5zm0 3.7h7V15h-7v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (mediaAndText);


/***/ }),

/***/ "lpUD":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatListBulletsRTL = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 8.8h8.9V7.2H4v1.6zm0 7h8.9v-1.5H4v1.5zM18 13c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatListBulletsRTL);


/***/ }),

/***/ "lpt+":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatListNumbered = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11.1 15.8H20v-1.5h-8.9v1.5zm0-8.6v1.5H20V7.2h-8.9zM5 6.7V10h1V5.3L3.8 6l.4 1 .8-.3zm-.4 5.7c-.3.1-.5.2-.7.3l.1 1.1c.2-.2.5-.4.8-.5.3-.1.6 0 .7.1.2.3 0 .8-.2 1.1-.5.8-.9 1.6-1.4 2.5h2.7v-1h-1c.3-.6.8-1.4.9-2.1.1-.3 0-.8-.2-1.1-.5-.6-1.3-.5-1.7-.4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatListNumbered);


/***/ }),

/***/ "lvzv":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const archiveTitle = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  stroke: "#1E1E1E",
  strokeWidth: "1.5",
  d: "M4 19.25h9M4 15.25h16"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M8.994 10.103H6.08L5.417 12H4l2.846-8h1.383l2.845 8H9.657l-.663-1.897zm-.457-1.28l-.994-2.857-1.006 2.857h2z",
  fill: "#1E1E1E"
}));
/* harmony default export */ __webpack_exports__["a"] = (archiveTitle);


/***/ }),

/***/ "m1d2":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const login = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11 14.5l1.1 1.1 3-3 .5-.5-.6-.6-3-3-1 1 1.7 1.7H5v1.5h7.7L11 14.5zM16.8 5h-7c-1.1 0-2 .9-2 2v1.5h1.5V7c0-.3.2-.5.5-.5h7c.3 0 .5.2.5.5v10c0 .3-.2.5-.5.5h-7c-.3 0-.5-.2-.5-.5v-1.5H7.8V17c0 1.1.9 2 2 2h7c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (login);


/***/ }),

/***/ "mi6C":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const settings = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M17 4h-2v4.5h2V7h3V5.5h-3V4zM4 5.5h9V7H4V5.5zm16 5.75h-9v1.5h9v-1.5zm-16 0h3V10h2v4.25H7v-1.5H4v-1.5zM9 17H4v1.5h5V17zm4 0h7v1.5h-7V20h-2v-4.25h2V17z"
}));
/* harmony default export */ __webpack_exports__["a"] = (settings);


/***/ }),

/***/ "nGWI":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const tableColumnAfter = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M14.08 12.864V9.216h3.648V7.424H14.08V3.776h-1.728v3.648H8.64v1.792h3.712v3.648zM0 17.92V0h20.48v17.92H0zM6.4 1.28H1.28v3.84H6.4V1.28zm0 5.12H1.28v3.84H6.4V6.4zm0 5.12H1.28v3.84H6.4v-3.84zM19.2 1.28H7.68v14.08H19.2V1.28z"
}));
/* harmony default export */ __webpack_exports__["a"] = (tableColumnAfter);


/***/ }),

/***/ "nzH+":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatUnderline = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M7 18v1h10v-1H7zm5-2c1.5 0 2.6-.4 3.4-1.2.8-.8 1.1-2 1.1-3.5V5H15v5.8c0 1.2-.2 2.1-.6 2.8-.4.7-1.2 1-2.4 1s-2-.3-2.4-1c-.4-.7-.6-1.6-.6-2.8V5H7.5v6.2c0 1.5.4 2.7 1.1 3.5.8.9 1.9 1.3 3.4 1.3z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatUnderline);


/***/ }),

/***/ "oMoS":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const button = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 6.5H5c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7c0-1.1-.9-2-2-2zm.5 9c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-7c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v7zM8 12.8h8v-1.5H8v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (button);


/***/ }),

/***/ "oOn3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const calendar = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm.5 16c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5V7h15v12zM9 10H7v2h2v-2zm0 4H7v2h2v-2zm4-4h-2v2h2v-2zm4 0h-2v2h2v-2zm-4 4h-2v2h2v-2zm4 0h-2v2h2v-2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (calendar);


/***/ }),

/***/ "onLe":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["notices"]; }());

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

/***/ "plpT":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const alignCenter = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M16.4 4.2H7.6v1.5h8.9V4.2zM4 11.2v1.5h16v-1.5H4zm3.6 8.6h8.9v-1.5H7.6v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (alignCenter);


/***/ }),

/***/ "qE/0":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const swatch = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5 17.7c.4.5.8.9 1.2 1.2l1.1-1.4c-.4-.3-.7-.6-1-1L5 17.7zM5 6.3l1.4 1.1c.3-.4.6-.7 1-1L6.3 5c-.5.4-.9.8-1.3 1.3zm.1 7.8l-1.7.5c.2.6.4 1.1.7 1.6l1.5-.8c-.2-.4-.4-.8-.5-1.3zM4.8 12v-.7L3 11.1v1.8l1.7-.2c.1-.2.1-.5.1-.7zm3 7.9c.5.3 1.1.5 1.6.7l.5-1.7c-.5-.1-.9-.3-1.3-.5l-.8 1.5zM19 6.3c-.4-.5-.8-.9-1.2-1.2l-1.1 1.4c.4.3.7.6 1 1L19 6.3zm-.1 3.6l1.7-.5c-.2-.6-.4-1.1-.7-1.6l-1.5.8c.2.4.4.8.5 1.3zM5.6 8.6l-1.5-.8c-.3.5-.5 1-.7 1.6l1.7.5c.1-.5.3-.9.5-1.3zm2.2-4.5l.8 1.5c.4-.2.8-.4 1.3-.5l-.5-1.7c-.6.2-1.1.4-1.6.7zm8.8 13.5l1.1 1.4c.5-.4.9-.8 1.2-1.2l-1.4-1.1c-.2.3-.5.6-.9.9zm1.8-2.2l1.5.8c.3-.5.5-1.1.7-1.6l-1.7-.5c-.1.5-.3.9-.5 1.3zm2.6-4.3l-1.7.2v1.4l1.7.2V12v-.9zM11.1 3l.2 1.7h1.4l.2-1.7h-1.8zm3 2.1c.5.1.9.3 1.3.5l.8-1.5c-.5-.3-1.1-.5-1.6-.7l-.5 1.7zM12 19.2h-.7l-.2 1.8h1.8l-.2-1.7c-.2-.1-.5-.1-.7-.1zm2.1-.3l.5 1.7c.6-.2 1.1-.4 1.6-.7l-.8-1.5c-.4.2-.8.4-1.3.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (swatch);


/***/ }),

/***/ "qRz9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["richText"]; }());

/***/ }),

/***/ "rH4q":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const media = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.7 3H5.3C4 3 3 4 3 5.3v13.4C3 20 4 21 5.3 21h13.4c1.3 0 2.3-1 2.3-2.3V5.3C21 4 20 3 18.7 3zm.8 15.7c0 .4-.4.8-.8.8H5.3c-.4 0-.8-.4-.8-.8V5.3c0-.4.4-.8.8-.8h13.4c.4 0 .8.4.8.8v13.4zM10 15l5-3-5-3v6z"
}));
/* harmony default export */ __webpack_exports__["a"] = (media);


/***/ }),

/***/ "rmEH":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["htmlEntities"]; }());

/***/ }),

/***/ "sEn/":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const cover = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.7 3H5.3C4 3 3 4 3 5.3v13.4C3 20 4 21 5.3 21h13.4c1.3 0 2.3-1 2.3-2.3V5.3C21 4 20 3 18.7 3zm.8 15.7c0 .4-.4.8-.8.8H5.3c-.4 0-.8-.4-.8-.8V5.3c0-.4.4-.8.8-.8h6.2v8.9l2.5-3.1 2.5 3.1V4.5h2.2c.4 0 .8.4.8.8v13.4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (cover);


/***/ }),

/***/ "sMMC":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatBold = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M14.7 11.3c1-.6 1.5-1.6 1.5-3 0-2.3-1.3-3.4-4-3.4H7v14h5.8c1.4 0 2.5-.3 3.3-1 .8-.7 1.2-1.7 1.2-2.9.1-1.9-.8-3.1-2.6-3.7zm-5.1-4h2.3c.6 0 1.1.1 1.4.4.3.3.5.7.5 1.2s-.2 1-.5 1.2c-.3.3-.8.4-1.4.4H9.6V7.3zm4.6 9c-.4.3-1 .4-1.7.4H9.6v-3.9h2.9c.7 0 1.3.2 1.7.5.4.3.6.8.6 1.5s-.2 1.2-.6 1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatBold);


/***/ }),

/***/ "skgM":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const postList = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h12c.3 0 .5.2.5.5v12zM7 11h2V9H7v2zm0 4h2v-2H7v2zm3-4h7V9h-7v2zm0 4h7v-2h-7v2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (postList);


/***/ }),

/***/ "suLj":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const justifyRight = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 15h11V9H4v6zM18.5 4v16H20V4h-1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (justifyRight);


/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "tK8V":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const queryPaginationPrevious = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Rect"], {
  x: "18",
  y: "13.5",
  width: "3",
  height: "3",
  rx: "1.5",
  transform: "rotate(-180 18 13.5)",
  fill: "#000"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Rect"], {
  x: "13",
  y: "13.5",
  width: "3",
  height: "3",
  rx: "1.5",
  transform: "rotate(-180 13 13.5)",
  fill: "#000"
}), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M7.5 14.5L5 12l2.5-2.5",
  stroke: "#1E1E1E",
  strokeWidth: "1.5"
}));
/* harmony default export */ __webpack_exports__["a"] = (queryPaginationPrevious);


/***/ }),

/***/ "u6za":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const group = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 4h-7c-1.1 0-2 .9-2 2v3H6c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h7c1.1 0 2-.9 2-2v-3h3c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4.5 14c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5v-7c0-.3.2-.5.5-.5h3V13c0 1.1.9 2 2 2h2.5v3zm0-4.5H11c-.3 0-.5-.2-.5-.5v-2.5H13c.3 0 .5.2.5.5v2.5zm5-.5c0 .3-.2.5-.5.5h-3V11c0-1.1-.9-2-2-2h-2.5V6c0-.3.2-.5.5-.5h7c.3 0 .5.2.5.5v7z"
}));
/* harmony default export */ __webpack_exports__["a"] = (group);


/***/ }),

/***/ "uGfJ":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const textColor = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12.9 6h-2l-4 11h1.9l1.1-3h4.2l1.1 3h1.9L12.9 6zm-2.5 6.5l1.5-4.9 1.7 4.9h-3.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (textColor);


/***/ }),

/***/ "uKyT":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatCapitalize = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M7.1 6.8L3.1 18h1.6l1.1-3h4.3l1.1 3h1.6l-4-11.2H7.1zm-.8 6.8L8 8.9l1.7 4.7H6.3zm14.5-1.5c-.3-.6-.7-1.1-1.2-1.5-.6-.4-1.2-.6-1.9-.6-.5 0-.9.1-1.4.3-.4.2-.8.5-1.1.8V6h-1.4v12h1.3l.2-1c.2.4.6.6 1 .8.4.2.9.3 1.4.3.7 0 1.2-.2 1.8-.5.5-.4 1-.9 1.3-1.5.3-.6.5-1.3.5-2.1-.1-.6-.2-1.3-.5-1.9zm-1.7 4c-.4.5-.9.8-1.6.8s-1.2-.2-1.7-.7c-.4-.5-.7-1.2-.7-2.1 0-.9.2-1.6.7-2.1.4-.5 1-.7 1.7-.7s1.2.3 1.6.8c.4.5.6 1.2.6 2 .1.8-.2 1.4-.6 2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatCapitalize);


/***/ }),

/***/ "uVe1":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const pages = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M7 13.8h6v-1.5H7v1.5zM18 16V4c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2zM5.5 16V4c0-.3.2-.5.5-.5h10c.3 0 .5.2.5.5v12c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5zM7 10.5h8V9H7v1.5zm0-3.3h8V5.8H7v1.4zM20.2 6v13c0 .7-.6 1.2-1.2 1.2H8v1.5h11c1.5 0 2.7-1.2 2.7-2.8V6h-1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (pages);


/***/ }),

/***/ "uxcH":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const postFeaturedImage = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 3H5c-.6 0-1 .4-1 1v7c0 .5.4 1 1 1h14c.5 0 1-.4 1-1V4c0-.6-.4-1-1-1zM5.5 10.5v-.4l1.8-1.3 1.3.8c.3.2.7.2.9-.1L11 8.1l2.4 2.4H5.5zm13 0h-2.9l-4-4c-.3-.3-.8-.3-1.1 0L8.9 8l-1.2-.8c-.3-.2-.6-.2-.9 0l-1.3 1V4.5h13v6zM4 20h9v-1.5H4V20zm0-4h16v-1.5H4V16z"
}));
/* harmony default export */ __webpack_exports__["a"] = (postFeaturedImage);


/***/ }),

/***/ "v3/P":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatLtr = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5.52 2h7.43c.55 0 1 .45 1 1s-.45 1-1 1h-1v13c0 .55-.45 1-1 1s-1-.45-1-1V5c0-.55-.45-1-1-1s-1 .45-1 1v12c0 .55-.45 1-1 1s-1-.45-1-1v-5.96h-.43C3.02 11.04 1 9.02 1 6.52S3.02 2 5.52 2zM14 14l5-4-5-4v8z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatLtr);


/***/ }),

/***/ "vlZU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const paragraph = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.3 4H9.9v-.1l-.9.2c-2.3.4-4 2.4-4 4.8s1.7 4.4 4 4.8l.7.1V20h1.5V5.5h2.9V20h1.5V5.5h2.7V4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (paragraph);


/***/ }),

/***/ "w95h":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const close = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (close);


/***/ }),

/***/ "wa2G":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const positionRight = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6 15h14V9H6v6zm6-10.8v1.5h8V4.2h-8zm0 15.6h8v-1.5h-8v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (positionRight);


/***/ }),

/***/ "wduq":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const wordpress = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (wordpress);


/***/ }),

/***/ "wszK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const siteLogo = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12 3c-5 0-9 4-9 9s4 9 9 9 9-4 9-9-4-9-9-9zm0 1.5c4.1 0 7.5 3.4 7.5 7.5v.1c-1.4-.8-3.3-1.7-3.4-1.8-.2-.1-.5-.1-.8.1l-2.9 2.1L9 11.3c-.2-.1-.4 0-.6.1l-3.7 2.2c-.1-.5-.2-1-.2-1.5 0-4.2 3.4-7.6 7.5-7.6zm0 15c-3.1 0-5.7-1.9-6.9-4.5l3.7-2.2 3.5 1.2c.2.1.5 0 .7-.1l2.9-2.1c.8.4 2.5 1.2 3.5 1.9-.9 3.3-3.9 5.8-7.4 5.8z"
}));
/* harmony default export */ __webpack_exports__["a"] = (siteLogo);


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

/***/ "wzfx":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const stack = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20.2 8v11c0 .7-.6 1.2-1.2 1.2H6v1.5h13c1.5 0 2.7-1.2 2.7-2.8V8zM18 16.4V4.6c0-.9-.7-1.6-1.6-1.6H4.6C3.7 3 3 3.7 3 4.6v11.8c0 .9.7 1.6 1.6 1.6h11.8c.9 0 1.6-.7 1.6-1.6zm-13.5 0V4.6c0-.1.1-.1.1-.1h11.8c.1 0 .1.1.1.1v11.8c0 .1-.1.1-.1.1H4.6l-.1-.1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (stack);


/***/ }),

/***/ "xN+R":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const dragHandle = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  width: "18",
  height: "18",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 18 18"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M5 4h2V2H5v2zm6-2v2h2V2h-2zm-6 8h2V8H5v2zm6 0h2V8h-2v2zm-6 6h2v-2H5v2zm6 0h2v-2h-2v2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (dragHandle);


/***/ }),

/***/ "yRk8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const pullLeft = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M4 18h6V6H4v12zm9-9.5V10h7V8.5h-7zm0 7h7V14h-7v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (pullLeft);


/***/ }),

/***/ "ywyh":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["apiFetch"]; }());

/***/ }),

/***/ "z4qt":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const formatLowercase = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11 16.8c-.1-.1-.2-.3-.3-.5v-2.6c0-.9-.1-1.7-.3-2.2-.2-.5-.5-.9-.9-1.2-.4-.2-.9-.3-1.6-.3-.5 0-1 .1-1.5.2s-.9.3-1.2.6l.2 1.2c.4-.3.7-.4 1.1-.5.3-.1.7-.2 1-.2.6 0 1 .1 1.3.4.3.2.4.7.4 1.4-1.2 0-2.3.2-3.3.7s-1.4 1.1-1.4 2.1c0 .7.2 1.2.7 1.6.4.4 1 .6 1.8.6.9 0 1.7-.4 2.4-1.2.1.3.2.5.4.7.1.2.3.3.6.4.3.1.6.1 1.1.1h.1l.2-1.2h-.1c-.4.1-.6 0-.7-.1zM9.2 16c-.2.3-.5.6-.9.8-.3.1-.7.2-1.1.2-.4 0-.7-.1-.9-.3-.2-.2-.3-.5-.3-.9 0-.6.2-1 .7-1.3.5-.3 1.3-.4 2.5-.5v2zm10.6-3.9c-.3-.6-.7-1.1-1.2-1.5-.6-.4-1.2-.6-1.9-.6-.5 0-.9.1-1.4.3-.4.2-.8.5-1.1.8V6h-1.4v12h1.3l.2-1c.2.4.6.6 1 .8.4.2.9.3 1.4.3.7 0 1.2-.2 1.8-.5.5-.4 1-.9 1.3-1.5.3-.6.5-1.3.5-2.1-.1-.6-.2-1.3-.5-1.9zm-1.7 4c-.4.5-.9.8-1.6.8s-1.2-.2-1.7-.7c-.4-.5-.7-1.2-.7-2.1 0-.9.2-1.6.7-2.1.4-.5 1-.7 1.7-.7s1.2.3 1.6.8c.4.5.6 1.2.6 2s-.2 1.4-.6 2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatLowercase);


/***/ }),

/***/ "zI3a":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const loop = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.1823 11.6392C18.1823 13.0804 17.0139 14.2487 15.5727 14.2487C14.3579 14.2487 13.335 13.4179 13.0453 12.2922L13.0377 12.2625L13.0278 12.2335L12.3985 10.377L12.3942 10.3785C11.8571 8.64997 10.246 7.39405 8.33961 7.39405C5.99509 7.39405 4.09448 9.29465 4.09448 11.6392C4.09448 13.9837 5.99509 15.8843 8.33961 15.8843C8.88499 15.8843 9.40822 15.781 9.88943 15.5923L9.29212 14.0697C8.99812 14.185 8.67729 14.2487 8.33961 14.2487C6.89838 14.2487 5.73003 13.0804 5.73003 11.6392C5.73003 10.1979 6.89838 9.02959 8.33961 9.02959C9.55444 9.02959 10.5773 9.86046 10.867 10.9862L10.8772 10.9836L11.4695 12.7311C11.9515 14.546 13.6048 15.8843 15.5727 15.8843C17.9172 15.8843 19.8178 13.9837 19.8178 11.6392C19.8178 9.29465 17.9172 7.39404 15.5727 7.39404C15.0287 7.39404 14.5066 7.4968 14.0264 7.6847L14.6223 9.20781C14.9158 9.093 15.2358 9.02959 15.5727 9.02959C17.0139 9.02959 18.1823 10.1979 18.1823 11.6392Z"
}));
/* harmony default export */ __webpack_exports__["a"] = (loop);


/***/ }),

/***/ "ziDm":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const alignRight = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11.1 19.8H20v-1.5h-8.9v1.5zm0-15.6v1.5H20V4.2h-8.9zM4 12.8h16v-1.5H4v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (alignRight);


/***/ })

/******/ });