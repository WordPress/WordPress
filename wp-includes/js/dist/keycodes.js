/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  ALT: () => (/* binding */ ALT),
  BACKSPACE: () => (/* binding */ BACKSPACE),
  COMMAND: () => (/* binding */ COMMAND),
  CTRL: () => (/* binding */ CTRL),
  DELETE: () => (/* binding */ DELETE),
  DOWN: () => (/* binding */ DOWN),
  END: () => (/* binding */ END),
  ENTER: () => (/* binding */ ENTER),
  ESCAPE: () => (/* binding */ ESCAPE),
  F10: () => (/* binding */ F10),
  HOME: () => (/* binding */ HOME),
  LEFT: () => (/* binding */ LEFT),
  PAGEDOWN: () => (/* binding */ PAGEDOWN),
  PAGEUP: () => (/* binding */ PAGEUP),
  RIGHT: () => (/* binding */ RIGHT),
  SHIFT: () => (/* binding */ SHIFT),
  SPACE: () => (/* binding */ SPACE),
  TAB: () => (/* binding */ TAB),
  UP: () => (/* binding */ UP),
  ZERO: () => (/* binding */ ZERO),
  displayShortcut: () => (/* binding */ displayShortcut),
  displayShortcutList: () => (/* binding */ displayShortcutList),
  isAppleOS: () => (/* reexport */ isAppleOS),
  isKeyboardEvent: () => (/* binding */ isKeyboardEvent),
  modifiers: () => (/* binding */ modifiers),
  rawShortcut: () => (/* binding */ rawShortcut),
  shortcutAriaLabel: () => (/* binding */ shortcutAriaLabel)
});

;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/keycodes/build-module/platform.js
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
  return platform.indexOf('Mac') !== -1 || ['iPad', 'iPhone'].includes(platform);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/keycodes/build-module/index.js
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
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/** @typedef {typeof ALT | CTRL | COMMAND | SHIFT } WPModifierPart */

/** @typedef {'primary' | 'primaryShift' | 'primaryAlt' | 'secondary' | 'access' | 'ctrl' | 'alt' | 'ctrlShift' | 'shift' | 'shiftAlt' | 'undefined'} WPKeycodeModifier */

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
/** @typedef {(event: import('react').KeyboardEvent<HTMLElement> | KeyboardEvent, character: string, isApple?: () => boolean) => boolean} WPEventKeyHandler */

/** @typedef {( isApple: () => boolean ) => WPModifierPart[]} WPModifier */

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
 * Keycode for PAGEUP key.
 */
const PAGEUP = 33;

/**
 * Keycode for PAGEDOWN key.
 */
const PAGEDOWN = 34;

/**
 * Keycode for END key.
 */
const END = 35;

/**
 * Keycode for HOME key.
 */
const HOME = 36;

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
 * Capitalise the first character of a string.
 * @param {string} string String to capitalise.
 * @return {string} Capitalised string.
 */
function capitaliseFirstCharacter(string) {
  return string.length < 2 ? string.toUpperCase() : string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * Map the values of an object with a specified callback and return the result object.
 *
 * @template {{ [s: string]: any; } | ArrayLike<any>} T
 *
 * @param {T}                     object Object to map values of.
 * @param {( value: any ) => any} mapFn  Mapping function
 *
 * @return {any} Active modifier constants.
 */
function mapValues(object, mapFn) {
  return Object.fromEntries(Object.entries(object).map(([key, value]) => [key, mapFn(value)]));
}

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
  shiftAlt: () => [SHIFT, ALT],
  undefined: () => []
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
const rawShortcut = mapValues(modifiers, ( /** @type {WPModifier} */modifier) => {
  return /** @type {WPKeyHandler<string>} */(character, _isApple = isAppleOS) => {
    return [...modifier(_isApple), character.toLowerCase()].join('+');
  };
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
const displayShortcutList = mapValues(modifiers, ( /** @type {WPModifier} */modifier) => {
  return /** @type {WPKeyHandler<string[]>} */(character, _isApple = isAppleOS) => {
    const isApple = _isApple();
    const replacementKeyMap = {
      [ALT]: isApple ? '⌥' : 'Alt',
      [CTRL]: isApple ? '⌃' : 'Ctrl',
      // Make sure ⌃ is the U+2303 UP ARROWHEAD unicode character and not the caret character.
      [COMMAND]: '⌘',
      [SHIFT]: isApple ? '⇧' : 'Shift'
    };
    const modifierKeys = modifier(_isApple).reduce((accumulator, key) => {
      var _replacementKeyMap$ke;
      const replacementKey = (_replacementKeyMap$ke = replacementKeyMap[key]) !== null && _replacementKeyMap$ke !== void 0 ? _replacementKeyMap$ke : key;
      // If on the Mac, adhere to platform convention and don't show plus between keys.
      if (isApple) {
        return [...accumulator, replacementKey];
      }
      return [...accumulator, replacementKey, '+'];
    }, /** @type {string[]} */[]);
    return [...modifierKeys, capitaliseFirstCharacter(character)];
  };
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
const displayShortcut = mapValues(displayShortcutList, ( /** @type {WPKeyHandler<string[]>} */shortcutList) => {
  return /** @type {WPKeyHandler<string>} */(character, _isApple = isAppleOS) => shortcutList(character, _isApple).join('');
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
const shortcutAriaLabel = mapValues(modifiers, ( /** @type {WPModifier} */modifier) => {
  return /** @type {WPKeyHandler<string>} */(character, _isApple = isAppleOS) => {
    const isApple = _isApple();
    /** @type {Record<string,string>} */
    const replacementKeyMap = {
      [SHIFT]: 'Shift',
      [COMMAND]: isApple ? 'Command' : 'Control',
      [CTRL]: 'Control',
      [ALT]: isApple ? 'Option' : 'Alt',
      /* translators: comma as in the character ',' */
      ',': (0,external_wp_i18n_namespaceObject.__)('Comma'),
      /* translators: period as in the character '.' */
      '.': (0,external_wp_i18n_namespaceObject.__)('Period'),
      /* translators: backtick as in the character '`' */
      '`': (0,external_wp_i18n_namespaceObject.__)('Backtick'),
      /* translators: tilde as in the character '~' */
      '~': (0,external_wp_i18n_namespaceObject.__)('Tilde')
    };
    return [...modifier(_isApple), character].map(key => {
      var _replacementKeyMap$ke2;
      return capitaliseFirstCharacter((_replacementKeyMap$ke2 = replacementKeyMap[key]) !== null && _replacementKeyMap$ke2 !== void 0 ? _replacementKeyMap$ke2 : key);
    }).join(isApple ? ' ' : ' + ');
  };
});

/**
 * From a given KeyboardEvent, returns an array of active modifier constants for
 * the event.
 *
 * @param {import('react').KeyboardEvent<HTMLElement> | KeyboardEvent} event Keyboard event.
 *
 * @return {Array<WPModifierPart>} Active modifier constants.
 */
function getEventModifiers(event) {
  return /** @type {WPModifierPart[]} */[ALT, CTRL, COMMAND, SHIFT].filter(key => event[( /** @type {'altKey' | 'ctrlKey' | 'metaKey' | 'shiftKey'} */
  `${key}Key`)]);
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
const isKeyboardEvent = mapValues(modifiers, ( /** @type {WPModifier} */getModifiers) => {
  return /** @type {WPEventKeyHandler} */(event, character, _isApple = isAppleOS) => {
    const mods = getModifiers(_isApple);
    const eventMods = getEventModifiers(event);
    /** @type {Record<string,string>} */
    const replacementWithShiftKeyMap = {
      Comma: ',',
      Backslash: '\\',
      // Windows returns `\` for both IntlRo and IntlYen.
      IntlRo: '\\',
      IntlYen: '\\'
    };
    const modsDiff = mods.filter(mod => !eventMods.includes(mod));
    const eventModsDiff = eventMods.filter(mod => !mods.includes(mod));
    if (modsDiff.length > 0 || eventModsDiff.length > 0) {
      return false;
    }
    let key = event.key.toLowerCase();
    if (!character) {
      return mods.includes( /** @type {WPModifierPart} */key);
    }
    if (event.altKey && character.length === 1) {
      key = String.fromCharCode(event.keyCode).toLowerCase();
    }

    // `event.key` returns the value of the key pressed, taking into the state of
    // modifier keys such as `Shift`. If the shift key is pressed, a different
    // value may be returned depending on the keyboard layout. It is necessary to
    // convert to the physical key value that don't take into account keyboard
    // layout or modifier key state.
    if (event.shiftKey && character.length === 1 && replacementWithShiftKeyMap[event.code]) {
      key = replacementWithShiftKeyMap[event.code];
    }

    // For backwards compatibility.
    if (character === 'del') {
      character = 'delete';
    }
    return key === character.toLowerCase();
  };
});

(window.wp = window.wp || {}).keycodes = __webpack_exports__;
/******/ })()
;