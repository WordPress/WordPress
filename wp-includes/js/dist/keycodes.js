this["wp"] = this["wp"] || {}; this["wp"]["keycodes"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/keycodes/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _arrayWithoutHoles; });\nfunction _arrayWithoutHoles(arr) {\n  if (Array.isArray(arr)) {\n    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {\n      arr2[i] = arr[i];\n    }\n\n    return arr2;\n  }\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _defineProperty; });\nfunction _defineProperty(obj, key, value) {\n  if (key in obj) {\n    Object.defineProperty(obj, key, {\n      value: value,\n      enumerable: true,\n      configurable: true,\n      writable: true\n    });\n  } else {\n    obj[key] = value;\n  }\n\n  return obj;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/defineProperty.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _iterableToArray; });\nfunction _iterableToArray(iter) {\n  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/iterableToArray.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _nonIterableSpread; });\nfunction _nonIterableSpread() {\n  throw new TypeError(\"Invalid attempt to spread non-iterable instance\");\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _toConsumableArray; });\n/* harmony import */ var _arrayWithoutHoles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithoutHoles */ \"./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js\");\n/* harmony import */ var _iterableToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArray */ \"./node_modules/@babel/runtime/helpers/esm/iterableToArray.js\");\n/* harmony import */ var _nonIterableSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./nonIterableSpread */ \"./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js\");\n\n\n\nfunction _toConsumableArray(arr) {\n  return Object(_arrayWithoutHoles__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(arr) || Object(_iterableToArray__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(arr) || Object(_nonIterableSpread__WEBPACK_IMPORTED_MODULE_2__[\"default\"])();\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js?");

/***/ }),

/***/ "./node_modules/@wordpress/keycodes/build-module/index.js":
/*!****************************************************************!*\
  !*** ./node_modules/@wordpress/keycodes/build-module/index.js ***!
  \****************************************************************/
/*! exports provided: BACKSPACE, TAB, ENTER, ESCAPE, SPACE, LEFT, UP, RIGHT, DOWN, DELETE, F10, ALT, CTRL, COMMAND, SHIFT, modifiers, rawShortcut, displayShortcutList, displayShortcut, shortcutAriaLabel, isKeyboardEvent */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"BACKSPACE\", function() { return BACKSPACE; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"TAB\", function() { return TAB; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"ENTER\", function() { return ENTER; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"ESCAPE\", function() { return ESCAPE; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"SPACE\", function() { return SPACE; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"LEFT\", function() { return LEFT; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"UP\", function() { return UP; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"RIGHT\", function() { return RIGHT; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"DOWN\", function() { return DOWN; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"DELETE\", function() { return DELETE; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"F10\", function() { return F10; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"ALT\", function() { return ALT; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"CTRL\", function() { return CTRL; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"COMMAND\", function() { return COMMAND; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"SHIFT\", function() { return SHIFT; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"modifiers\", function() { return modifiers; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"rawShortcut\", function() { return rawShortcut; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"displayShortcutList\", function() { return displayShortcutList; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"displayShortcut\", function() { return displayShortcut; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"shortcutAriaLabel\", function() { return shortcutAriaLabel; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isKeyboardEvent\", function() { return isKeyboardEvent; });\n/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ \"./node_modules/@babel/runtime/helpers/esm/defineProperty.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ \"./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _platform__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./platform */ \"./node_modules/@wordpress/keycodes/build-module/platform.js\");\n\n\n\n/**\n * Note: The order of the modifier keys in many of the [foo]Shortcut()\n * functions in this file are intentional and should not be changed. They're\n * designed to fit with the standard menu keyboard shortcuts shown in the\n * user's platform.\n *\n * For example, on MacOS menu shortcuts will place Shift before Command, but\n * on Windows Control will usually come first. So don't provide your own\n * shortcut combos directly to keyboardShortcut().\n */\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\nvar BACKSPACE = 8;\nvar TAB = 9;\nvar ENTER = 13;\nvar ESCAPE = 27;\nvar SPACE = 32;\nvar LEFT = 37;\nvar UP = 38;\nvar RIGHT = 39;\nvar DOWN = 40;\nvar DELETE = 46;\nvar F10 = 121;\nvar ALT = 'alt';\nvar CTRL = 'ctrl'; // Understood in both Mousetrap and TinyMCE.\n\nvar COMMAND = 'meta';\nvar SHIFT = 'shift';\nvar modifiers = {\n  primary: function primary(_isApple) {\n    return _isApple() ? [COMMAND] : [CTRL];\n  },\n  primaryShift: function primaryShift(_isApple) {\n    return _isApple() ? [SHIFT, COMMAND] : [CTRL, SHIFT];\n  },\n  primaryAlt: function primaryAlt(_isApple) {\n    return _isApple() ? [ALT, COMMAND] : [CTRL, ALT];\n  },\n  secondary: function secondary(_isApple) {\n    return _isApple() ? [SHIFT, ALT, COMMAND] : [CTRL, SHIFT, ALT];\n  },\n  access: function access(_isApple) {\n    return _isApple() ? [CTRL, ALT] : [SHIFT, ALT];\n  },\n  ctrl: function ctrl() {\n    return [CTRL];\n  },\n  alt: function alt() {\n    return [ALT];\n  },\n  ctrlShift: function ctrlShift() {\n    return [CTRL, SHIFT];\n  },\n  shift: function shift() {\n    return [SHIFT];\n  },\n  shiftAlt: function shiftAlt() {\n    return [SHIFT, ALT];\n  }\n};\n/**\n * An object that contains functions to get raw shortcuts.\n * E.g. rawShortcut.primary( 'm' ) will return 'meta+m' on Mac.\n * These are intended for user with the KeyboardShortcuts component or TinyMCE.\n *\n * @type {Object} Keyed map of functions to raw shortcuts.\n */\n\nvar rawShortcut = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"mapValues\"])(modifiers, function (modifier) {\n  return function (character) {\n    var _isApple = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : _platform__WEBPACK_IMPORTED_MODULE_4__[\"isAppleOS\"];\n\n    return Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(modifier(_isApple)).concat([character.toLowerCase()]).join('+');\n  };\n});\n/**\n * Return an array of the parts of a keyboard shortcut chord for display\n * E.g displayShortcutList.primary( 'm' ) will return [ '⌘', 'M' ] on Mac.\n *\n * @type {Object} keyed map of functions to shortcut sequences\n */\n\nvar displayShortcutList = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"mapValues\"])(modifiers, function (modifier) {\n  return function (character) {\n    var _replacementKeyMap;\n\n    var _isApple = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : _platform__WEBPACK_IMPORTED_MODULE_4__[\"isAppleOS\"];\n\n    var isApple = _isApple();\n\n    var replacementKeyMap = (_replacementKeyMap = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap, ALT, isApple ? '⌥' : 'Alt'), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap, CTRL, isApple ? '^' : 'Ctrl'), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap, COMMAND, '⌘'), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap, SHIFT, isApple ? '⇧' : 'Shift'), _replacementKeyMap);\n    var modifierKeys = modifier(_isApple).reduce(function (accumulator, key) {\n      var replacementKey = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"get\"])(replacementKeyMap, key, key); // If on the Mac, adhere to platform convention and don't show plus between keys.\n\n      if (isApple) {\n        return Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(accumulator).concat([replacementKey]);\n      }\n\n      return Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(accumulator).concat([replacementKey, '+']);\n    }, []);\n    var capitalizedCharacter = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"capitalize\"])(character);\n    return Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(modifierKeys).concat([capitalizedCharacter]);\n  };\n});\n/**\n * An object that contains functions to display shortcuts.\n * E.g. displayShortcut.primary( 'm' ) will return '⌘M' on Mac.\n *\n * @type {Object} Keyed map of functions to display shortcuts.\n */\n\nvar displayShortcut = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"mapValues\"])(displayShortcutList, function (shortcutList) {\n  return function (character) {\n    var _isApple = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : _platform__WEBPACK_IMPORTED_MODULE_4__[\"isAppleOS\"];\n\n    return shortcutList(character, _isApple).join('');\n  };\n});\n/**\n * An object that contains functions to return an aria label for a keyboard shortcut.\n * E.g. shortcutAriaLabel.primary( '.' ) will return 'Command + Period' on Mac.\n */\n\nvar shortcutAriaLabel = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"mapValues\"])(modifiers, function (modifier) {\n  return function (character) {\n    var _replacementKeyMap2;\n\n    var _isApple = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : _platform__WEBPACK_IMPORTED_MODULE_4__[\"isAppleOS\"];\n\n    var isApple = _isApple();\n\n    var replacementKeyMap = (_replacementKeyMap2 = {}, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap2, SHIFT, 'Shift'), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap2, COMMAND, isApple ? 'Command' : 'Control'), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap2, CTRL, 'Control'), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap2, ALT, isApple ? 'Option' : 'Alt'), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap2, ',', Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__[\"__\"])('Comma')), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap2, '.', Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__[\"__\"])('Period')), Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(_replacementKeyMap2, '`', Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__[\"__\"])('Backtick')), _replacementKeyMap2);\n    return Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(modifier(_isApple)).concat([character]).map(function (key) {\n      return Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"capitalize\"])(Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"get\"])(replacementKeyMap, key, key));\n    }).join(isApple ? ' ' : ' + ');\n  };\n});\n/**\n * An object that contains functions to check if a keyboard event matches a\n * predefined shortcut combination.\n * E.g. isKeyboardEvent.primary( event, 'm' ) will return true if the event\n * signals pressing ⌘M.\n *\n * @type {Object} Keyed map of functions to match events.\n */\n\nvar isKeyboardEvent = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"mapValues\"])(modifiers, function (getModifiers) {\n  return function (event, character) {\n    var _isApple = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : _platform__WEBPACK_IMPORTED_MODULE_4__[\"isAppleOS\"];\n\n    var mods = getModifiers(_isApple);\n\n    if (!mods.every(function (key) {\n      return event[\"\".concat(key, \"Key\")];\n    })) {\n      return false;\n    }\n\n    if (!character) {\n      return Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"includes\"])(mods, event.key.toLowerCase());\n    }\n\n    return event.key === character;\n  };\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/keycodes/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/keycodes/build-module/platform.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/keycodes/build-module/platform.js ***!
  \*******************************************************************/
/*! exports provided: isAppleOS */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isAppleOS\", function() { return isAppleOS; });\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * External dependencies\n */\n\n/**\n * Return true if platform is MacOS.\n *\n * @param {Object} _window   window object by default; used for DI testing.\n *\n * @return {boolean}         True if MacOS; false otherwise.\n */\n\nfunction isAppleOS() {\n  var _window = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : window;\n\n  var platform = _window.navigator.platform;\n  return platform.indexOf('Mac') !== -1 || Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"includes\"])(['iPad', 'iPhone'], platform);\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/keycodes/build-module/platform.js?");

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"i18n\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22i18n%22%5D%7D?");

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22lodash%22?");

/***/ })

/******/ });