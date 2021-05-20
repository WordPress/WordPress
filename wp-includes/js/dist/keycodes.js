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

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

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
__webpack_require__.d(__webpack_exports__, "ZERO", function() { return /* binding */ ZERO; });
__webpack_require__.d(__webpack_exports__, "modifiers", function() { return /* binding */ modifiers; });
__webpack_require__.d(__webpack_exports__, "rawShortcut", function() { return /* binding */ rawShortcut; });
__webpack_require__.d(__webpack_exports__, "displayShortcutList", function() { return /* binding */ displayShortcutList; });
__webpack_require__.d(__webpack_exports__, "displayShortcut", function() { return /* binding */ displayShortcut; });
__webpack_require__.d(__webpack_exports__, "shortcutAriaLabel", function() { return /* binding */ shortcutAriaLabel; });
__webpack_require__.d(__webpack_exports__, "isKeyboardEvent", function() { return /* binding */ isKeyboardEvent; });

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/keycodes/build-module/platform.js
/**
 * External dependencies
 */

/**
 * Return true if platform is MacOS.
 *
 * @param {Window?} _window window object by default; used for DI testing.
 *
 * @return {boolean} True if MacOS; false otherwise.
 */

function isAppleOS(_window = null) {
  if (!_window) {
    if (typeof window === 'undefined') {
      return false;
    }

    _window = window;
  }

  const {
    platform
  } = _window.navigator;
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


/** @typedef {typeof ALT | CTRL | COMMAND | SHIFT } WPModifierPart */

/** @typedef {'primary' | 'primaryShift' | 'primaryAlt' | 'secondary' | 'access' | 'ctrl' | 'alt' | 'ctrlShift' | 'shift' | 'shiftAlt'} WPKeycodeModifier */

/**
 * An object of handler functions for each of the possible modifier
 * combinations. A handler will return a value for a given key.
 *
 * @template T
 *
 * @typedef {Record<WPKeycodeModifier, T>} WPModifierHandler
 */

/**
 * @template T
 *
 * @typedef {(character: string, isApple?: () => boolean) => T} WPKeyHandler
 */

/** @typedef {(event: KeyboardEvent, character: string, isApple?: () => boolean) => boolean} WPEventKeyHandler */

/**
 * Keycode for BACKSPACE key.
 */

const BACKSPACE = 8;
/**
 * Keycode for TAB key.
 */

const TAB = 9;
/**
 * Keycode for ENTER key.
 */

const ENTER = 13;
/**
 * Keycode for ESCAPE key.
 */

const ESCAPE = 27;
/**
 * Keycode for SPACE key.
 */

const SPACE = 32;
/**
 * Keycode for LEFT key.
 */

const LEFT = 37;
/**
 * Keycode for UP key.
 */

const UP = 38;
/**
 * Keycode for RIGHT key.
 */

const RIGHT = 39;
/**
 * Keycode for DOWN key.
 */

const DOWN = 40;
/**
 * Keycode for DELETE key.
 */

const DELETE = 46;
/**
 * Keycode for F10 key.
 */

const F10 = 121;
/**
 * Keycode for ALT key.
 */

const ALT = 'alt';
/**
 * Keycode for CTRL key.
 */

const CTRL = 'ctrl';
/**
 * Keycode for COMMAND/META key.
 */

const COMMAND = 'meta';
/**
 * Keycode for SHIFT key.
 */

const SHIFT = 'shift';
/**
 * Keycode for ZERO key.
 */

const ZERO = 48;
/**
 * Object that contains functions that return the available modifier
 * depending on platform.
 *
 * @type {WPModifierHandler< ( isApple: () => boolean ) => WPModifierPart[]>}
 */

const modifiers = {
  primary: _isApple => _isApple() ? [COMMAND] : [CTRL],
  primaryShift: _isApple => _isApple() ? [SHIFT, COMMAND] : [CTRL, SHIFT],
  primaryAlt: _isApple => _isApple() ? [ALT, COMMAND] : [CTRL, ALT],
  secondary: _isApple => _isApple() ? [SHIFT, ALT, COMMAND] : [CTRL, SHIFT, ALT],
  access: _isApple => _isApple() ? [CTRL, ALT] : [SHIFT, ALT],
  ctrl: () => [CTRL],
  alt: () => [ALT],
  ctrlShift: () => [CTRL, SHIFT],
  shift: () => [SHIFT],
  shiftAlt: () => [SHIFT, ALT]
};
/**
 * An object that contains functions to get raw shortcuts.
 *
 * These are intended for user with the KeyboardShortcuts.
 *
 * @example
 * ```js
 * // Assuming macOS:
 * rawShortcut.primary( 'm' )
 * // "meta+m""
 * ```
 *
 * @type {WPModifierHandler<WPKeyHandler<string>>} Keyed map of functions to raw
 *                                                 shortcuts.
 */

const rawShortcut = Object(external_lodash_["mapValues"])(modifiers, modifier => {
  return (
    /** @type {WPKeyHandler<string>} */
    (character, _isApple = isAppleOS) => {
      return [...modifier(_isApple), character.toLowerCase()].join('+');
    }
  );
});
/**
 * Return an array of the parts of a keyboard shortcut chord for display.
 *
 * @example
 * ```js
 * // Assuming macOS:
 * displayShortcutList.primary( 'm' );
 * // [ "⌘", "M" ]
 * ```
 *
 * @type {WPModifierHandler<WPKeyHandler<string[]>>} Keyed map of functions to
 *                                                   shortcut sequences.
 */

const displayShortcutList = Object(external_lodash_["mapValues"])(modifiers, modifier => {
  return (
    /** @type {WPKeyHandler<string[]>} */
    (character, _isApple = isAppleOS) => {
      const isApple = _isApple();

      const replacementKeyMap = {
        [ALT]: isApple ? '⌥' : 'Alt',
        [CTRL]: isApple ? '⌃' : 'Ctrl',
        // Make sure ⌃ is the U+2303 UP ARROWHEAD unicode character and not the caret character.
        [COMMAND]: '⌘',
        [SHIFT]: isApple ? '⇧' : 'Shift'
      };
      const modifierKeys = modifier(_isApple).reduce((accumulator, key) => {
        const replacementKey = Object(external_lodash_["get"])(replacementKeyMap, key, key); // If on the Mac, adhere to platform convention and don't show plus between keys.

        if (isApple) {
          return [...accumulator, replacementKey];
        }

        return [...accumulator, replacementKey, '+'];
      },
      /** @type {string[]} */
      []);
      const capitalizedCharacter = Object(external_lodash_["capitalize"])(character);
      return [...modifierKeys, capitalizedCharacter];
    }
  );
});
/**
 * An object that contains functions to display shortcuts.
 *
 * @example
 * ```js
 * // Assuming macOS:
 * displayShortcut.primary( 'm' );
 * // "⌘M"
 * ```
 *
 * @type {WPModifierHandler<WPKeyHandler<string>>} Keyed map of functions to
 *                                                 display shortcuts.
 */

const displayShortcut = Object(external_lodash_["mapValues"])(displayShortcutList, shortcutList => {
  return (
    /** @type {WPKeyHandler<string>} */
    (character, _isApple = isAppleOS) => shortcutList(character, _isApple).join('')
  );
});
/**
 * An object that contains functions to return an aria label for a keyboard
 * shortcut.
 *
 * @example
 * ```js
 * // Assuming macOS:
 * shortcutAriaLabel.primary( '.' );
 * // "Command + Period"
 * ```
 *
 * @type {WPModifierHandler<WPKeyHandler<string>>} Keyed map of functions to
 *                                                 shortcut ARIA labels.
 */

const shortcutAriaLabel = Object(external_lodash_["mapValues"])(modifiers, modifier => {
  return (
    /** @type {WPKeyHandler<string>} */
    (character, _isApple = isAppleOS) => {
      const isApple = _isApple();

      const replacementKeyMap = {
        [SHIFT]: 'Shift',
        [COMMAND]: isApple ? 'Command' : 'Control',
        [CTRL]: 'Control',
        [ALT]: isApple ? 'Option' : 'Alt',

        /* translators: comma as in the character ',' */
        ',': Object(external_wp_i18n_["__"])('Comma'),

        /* translators: period as in the character '.' */
        '.': Object(external_wp_i18n_["__"])('Period'),

        /* translators: backtick as in the character '`' */
        '`': Object(external_wp_i18n_["__"])('Backtick')
      };
      return [...modifier(_isApple), character].map(key => Object(external_lodash_["capitalize"])(Object(external_lodash_["get"])(replacementKeyMap, key, key))).join(isApple ? ' ' : ' + ');
    }
  );
});
/**
 * From a given KeyboardEvent, returns an array of active modifier constants for
 * the event.
 *
 * @param {KeyboardEvent} event Keyboard event.
 *
 * @return {Array<WPModifierPart>} Active modifier constants.
 */

function getEventModifiers(event) {
  return (
    /** @type {WPModifierPart[]} */
    [ALT, CTRL, COMMAND, SHIFT].filter(key => event[
    /** @type {'altKey' | 'ctrlKey' | 'metaKey' | 'shiftKey'} */
    `${key}Key`])
  );
}
/**
 * An object that contains functions to check if a keyboard event matches a
 * predefined shortcut combination.
 *
 * @example
 * ```js
 * // Assuming an event for ⌘M key press:
 * isKeyboardEvent.primary( event, 'm' );
 * // true
 * ```
 *
 * @type {WPModifierHandler<WPEventKeyHandler>} Keyed map of functions
 *                                                       to match events.
 */


const isKeyboardEvent = Object(external_lodash_["mapValues"])(modifiers, getModifiers => {
  return (
    /** @type {WPEventKeyHandler} */
    (event, character, _isApple = isAppleOS) => {
      const mods = getModifiers(_isApple);
      const eventMods = getEventModifiers(event);

      if (Object(external_lodash_["xor"])(mods, eventMods).length) {
        return false;
      }

      if (!character) {
        return Object(external_lodash_["includes"])(mods, event.key.toLowerCase());
      }

      return event.key === character;
    }
  );
});


/***/ })

/******/ });