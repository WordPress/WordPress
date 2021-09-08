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
/******/ 	return __webpack_require__(__webpack_require__.s = "z7pY");
/******/ })
/************************************************************************/
/******/ ({

/***/ "25BE":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

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

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

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

/***/ "z7pY":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "BACKSPACE", function() { return /* binding */ BACKSPACE; });
__webpack_require__.d(__webpack_exports__, "TAB", function() { return /* binding */ TAB; });
__webpack_require__.d(__webpack_exports__, "ENTER", function() { return /* binding */ ENTER; });
__webpack_require__.d(__webpack_exports__, "ESCAPE", function() { return /* binding */ ESCAPE; });
__webpack_require__.d(__webpack_exports__, "SPACE", function() { return /* binding */ SPACE; });
__webpack_require__.d(__webpack_exports__, "LEFT", function() { return /* binding */ LEFT; });
__webpack_require__.d(__webpack_exports__, "UP", function() { return /* binding */ UP; });
__webpack_require__.d(__webpack_exports__, "RIGHT", function() { return /* binding */ RIGHT; });
__webpack_require__.d(__webpack_exports__, "DOWN", function() { return /* binding */ DOWN; });
__webpack_require__.d(__webpack_exports__, "DELETE", function() { return /* binding */ DELETE; });
__webpack_require__.d(__webpack_exports__, "F10", function() { return /* binding */ F10; });
__webpack_require__.d(__webpack_exports__, "ALT", function() { return /* binding */ ALT; });
__webpack_require__.d(__webpack_exports__, "CTRL", function() { return /* binding */ CTRL; });
__webpack_require__.d(__webpack_exports__, "COMMAND", function() { return /* binding */ COMMAND; });
__webpack_require__.d(__webpack_exports__, "SHIFT", function() { return /* binding */ SHIFT; });
__webpack_require__.d(__webpack_exports__, "modifiers", function() { return /* binding */ modifiers; });
__webpack_require__.d(__webpack_exports__, "rawShortcut", function() { return /* binding */ rawShortcut; });
__webpack_require__.d(__webpack_exports__, "displayShortcutList", function() { return /* binding */ displayShortcutList; });
__webpack_require__.d(__webpack_exports__, "displayShortcut", function() { return /* binding */ displayShortcut; });
__webpack_require__.d(__webpack_exports__, "shortcutAriaLabel", function() { return /* binding */ shortcutAriaLabel; });
__webpack_require__.d(__webpack_exports__, "isKeyboardEvent", function() { return /* binding */ isKeyboardEvent; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("rePB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__("KQm4");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/keycodes/build-module/platform.js
/**
 * External dependencies
 */

/**
 * Return true if platform is MacOS.
 *
 * @param {Object} _window   window object by default; used for DI testing.
 *
 * @return {boolean}         True if MacOS; false otherwise.
 */

function isAppleOS() {
  var _window = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : window;

  var platform = _window.navigator.platform;
  return platform.indexOf('Mac') !== -1 || Object(external_lodash_["includes"])(['iPad', 'iPhone'], platform);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/keycodes/build-module/index.js



/**
 * Note: The order of the modifier keys in many of the [foo]Shortcut()
 * functions in this file are intentional and should not be changed. They're
 * designed to fit with the standard menu keyboard shortcuts shown in the
 * user's platform.
 *
 * For example, on MacOS menu shortcuts will place Shift before Command, but
 * on Windows Control will usually come first. So don't provide your own
 * shortcut combos directly to keyboardShortcut().
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


var BACKSPACE = 8;
var TAB = 9;
var ENTER = 13;
var ESCAPE = 27;
var SPACE = 32;
var LEFT = 37;
var UP = 38;
var RIGHT = 39;
var DOWN = 40;
var DELETE = 46;
var F10 = 121;
var ALT = 'alt';
var CTRL = 'ctrl'; // Understood in both Mousetrap and TinyMCE.

var COMMAND = 'meta';
var SHIFT = 'shift';
var modifiers = {
  primary: function primary(_isApple) {
    return _isApple() ? [COMMAND] : [CTRL];
  },
  primaryShift: function primaryShift(_isApple) {
    return _isApple() ? [SHIFT, COMMAND] : [CTRL, SHIFT];
  },
  primaryAlt: function primaryAlt(_isApple) {
    return _isApple() ? [ALT, COMMAND] : [CTRL, ALT];
  },
  secondary: function secondary(_isApple) {
    return _isApple() ? [SHIFT, ALT, COMMAND] : [CTRL, SHIFT, ALT];
  },
  access: function access(_isApple) {
    return _isApple() ? [CTRL, ALT] : [SHIFT, ALT];
  },
  ctrl: function ctrl() {
    return [CTRL];
  },
  alt: function alt() {
    return [ALT];
  },
  ctrlShift: function ctrlShift() {
    return [CTRL, SHIFT];
  },
  shift: function shift() {
    return [SHIFT];
  },
  shiftAlt: function shiftAlt() {
    return [SHIFT, ALT];
  }
};
/**
 * An object that contains functions to get raw shortcuts.
 * E.g. rawShortcut.primary( 'm' ) will return 'meta+m' on Mac.
 * These are intended for user with the KeyboardShortcuts component or TinyMCE.
 *
 * @type {Object} Keyed map of functions to raw shortcuts.
 */

var rawShortcut = Object(external_lodash_["mapValues"])(modifiers, function (modifier) {
  return function (character) {
    var _isApple = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : isAppleOS;

    return Object(toConsumableArray["a" /* default */])(modifier(_isApple)).concat([character.toLowerCase()]).join('+');
  };
});
/**
 * Return an array of the parts of a keyboard shortcut chord for display
 * E.g displayShortcutList.primary( 'm' ) will return [ '⌘', 'M' ] on Mac.
 *
 * @type {Object} keyed map of functions to shortcut sequences
 */

var displayShortcutList = Object(external_lodash_["mapValues"])(modifiers, function (modifier) {
  return function (character) {
    var _replacementKeyMap;

    var _isApple = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : isAppleOS;

    var isApple = _isApple();

    var replacementKeyMap = (_replacementKeyMap = {}, Object(defineProperty["a" /* default */])(_replacementKeyMap, ALT, isApple ? '⌥' : 'Alt'), Object(defineProperty["a" /* default */])(_replacementKeyMap, CTRL, isApple ? '^' : 'Ctrl'), Object(defineProperty["a" /* default */])(_replacementKeyMap, COMMAND, '⌘'), Object(defineProperty["a" /* default */])(_replacementKeyMap, SHIFT, isApple ? '⇧' : 'Shift'), _replacementKeyMap);
    var modifierKeys = modifier(_isApple).reduce(function (accumulator, key) {
      var replacementKey = Object(external_lodash_["get"])(replacementKeyMap, key, key); // If on the Mac, adhere to platform convention and don't show plus between keys.

      if (isApple) {
        return Object(toConsumableArray["a" /* default */])(accumulator).concat([replacementKey]);
      }

      return Object(toConsumableArray["a" /* default */])(accumulator).concat([replacementKey, '+']);
    }, []);
    var capitalizedCharacter = Object(external_lodash_["capitalize"])(character);
    return Object(toConsumableArray["a" /* default */])(modifierKeys).concat([capitalizedCharacter]);
  };
});
/**
 * An object that contains functions to display shortcuts.
 * E.g. displayShortcut.primary( 'm' ) will return '⌘M' on Mac.
 *
 * @type {Object} Keyed map of functions to display shortcuts.
 */

var displayShortcut = Object(external_lodash_["mapValues"])(displayShortcutList, function (shortcutList) {
  return function (character) {
    var _isApple = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : isAppleOS;

    return shortcutList(character, _isApple).join('');
  };
});
/**
 * An object that contains functions to return an aria label for a keyboard shortcut.
 * E.g. shortcutAriaLabel.primary( '.' ) will return 'Command + Period' on Mac.
 */

var shortcutAriaLabel = Object(external_lodash_["mapValues"])(modifiers, function (modifier) {
  return function (character) {
    var _replacementKeyMap2;

    var _isApple = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : isAppleOS;

    var isApple = _isApple();

    var replacementKeyMap = (_replacementKeyMap2 = {}, Object(defineProperty["a" /* default */])(_replacementKeyMap2, SHIFT, 'Shift'), Object(defineProperty["a" /* default */])(_replacementKeyMap2, COMMAND, isApple ? 'Command' : 'Control'), Object(defineProperty["a" /* default */])(_replacementKeyMap2, CTRL, 'Control'), Object(defineProperty["a" /* default */])(_replacementKeyMap2, ALT, isApple ? 'Option' : 'Alt'), Object(defineProperty["a" /* default */])(_replacementKeyMap2, ',', Object(external_this_wp_i18n_["__"])('Comma')), Object(defineProperty["a" /* default */])(_replacementKeyMap2, '.', Object(external_this_wp_i18n_["__"])('Period')), Object(defineProperty["a" /* default */])(_replacementKeyMap2, '`', Object(external_this_wp_i18n_["__"])('Backtick')), _replacementKeyMap2);
    return Object(toConsumableArray["a" /* default */])(modifier(_isApple)).concat([character]).map(function (key) {
      return Object(external_lodash_["capitalize"])(Object(external_lodash_["get"])(replacementKeyMap, key, key));
    }).join(isApple ? ' ' : ' + ');
  };
});
/**
 * An object that contains functions to check if a keyboard event matches a
 * predefined shortcut combination.
 * E.g. isKeyboardEvent.primary( event, 'm' ) will return true if the event
 * signals pressing ⌘M.
 *
 * @type {Object} Keyed map of functions to match events.
 */

var isKeyboardEvent = Object(external_lodash_["mapValues"])(modifiers, function (getModifiers) {
  return function (event, character) {
    var _isApple = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : isAppleOS;

    var mods = getModifiers(_isApple);

    if (!mods.every(function (key) {
      return event["".concat(key, "Key")];
    })) {
      return false;
    }

    if (!character) {
      return Object(external_lodash_["includes"])(mods, event.key.toLowerCase());
    }

    return event.key === character;
  };
});


/***/ })

/******/ });