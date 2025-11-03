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

;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./node_modules/@wordpress/keycodes/build-module/platform.js
function isAppleOS(_window) {
  if (!_window) {
    if (typeof window === "undefined") {
      return false;
    }
    _window = window;
  }
  const { platform } = _window.navigator;
  return platform.indexOf("Mac") !== -1 || ["iPad", "iPhone"].includes(platform);
}


;// ./node_modules/@wordpress/keycodes/build-module/index.js


const BACKSPACE = 8;
const TAB = 9;
const ENTER = 13;
const ESCAPE = 27;
const SPACE = 32;
const PAGEUP = 33;
const PAGEDOWN = 34;
const END = 35;
const HOME = 36;
const LEFT = 37;
const UP = 38;
const RIGHT = 39;
const DOWN = 40;
const DELETE = 46;
const F10 = 121;
const ALT = "alt";
const CTRL = "ctrl";
const COMMAND = "meta";
const SHIFT = "shift";
const ZERO = 48;
function capitaliseFirstCharacter(string) {
  return string.length < 2 ? string.toUpperCase() : string.charAt(0).toUpperCase() + string.slice(1);
}
function mapValues(object, mapFn) {
  return Object.fromEntries(
    Object.entries(object).map(([key, value]) => [
      key,
      mapFn(value)
    ])
  );
}
const modifiers = {
  primary: (_isApple) => _isApple() ? [COMMAND] : [CTRL],
  primaryShift: (_isApple) => _isApple() ? [SHIFT, COMMAND] : [CTRL, SHIFT],
  primaryAlt: (_isApple) => _isApple() ? [ALT, COMMAND] : [CTRL, ALT],
  secondary: (_isApple) => _isApple() ? [SHIFT, ALT, COMMAND] : [CTRL, SHIFT, ALT],
  access: (_isApple) => _isApple() ? [CTRL, ALT] : [SHIFT, ALT],
  ctrl: () => [CTRL],
  alt: () => [ALT],
  ctrlShift: () => [CTRL, SHIFT],
  shift: () => [SHIFT],
  shiftAlt: () => [SHIFT, ALT],
  undefined: () => []
};
const rawShortcut = /* @__PURE__ */ mapValues(modifiers, (modifier) => {
  return (character, _isApple = isAppleOS) => {
    return [...modifier(_isApple), character.toLowerCase()].join(
      "+"
    );
  };
});
const displayShortcutList = /* @__PURE__ */ mapValues(
  modifiers,
  (modifier) => {
    return (character, _isApple = isAppleOS) => {
      const isApple = _isApple();
      const replacementKeyMap = {
        [ALT]: isApple ? "\u2325" : "Alt",
        [CTRL]: isApple ? "\u2303" : "Ctrl",
        // Make sure ⌃ is the U+2303 UP ARROWHEAD unicode character and not the caret character.
        [COMMAND]: "\u2318",
        [SHIFT]: isApple ? "\u21E7" : "Shift"
      };
      const modifierKeys = modifier(_isApple).reduce(
        (accumulator, key) => {
          const replacementKey = replacementKeyMap[key] ?? key;
          if (isApple) {
            return [...accumulator, replacementKey];
          }
          return [...accumulator, replacementKey, "+"];
        },
        []
      );
      return [
        ...modifierKeys,
        capitaliseFirstCharacter(character)
      ];
    };
  }
);
const displayShortcut = /* @__PURE__ */ mapValues(
  displayShortcutList,
  (shortcutList) => {
    return (character, _isApple = isAppleOS) => shortcutList(character, _isApple).join("");
  }
);
const shortcutAriaLabel = /* @__PURE__ */ mapValues(modifiers, (modifier) => {
  return (character, _isApple = isAppleOS) => {
    const isApple = _isApple();
    const replacementKeyMap = {
      [SHIFT]: "Shift",
      [COMMAND]: isApple ? "Command" : "Control",
      [CTRL]: "Control",
      [ALT]: isApple ? "Option" : "Alt",
      /* translators: comma as in the character ',' */
      ",": (0,external_wp_i18n_namespaceObject.__)("Comma"),
      /* translators: period as in the character '.' */
      ".": (0,external_wp_i18n_namespaceObject.__)("Period"),
      /* translators: backtick as in the character '`' */
      "`": (0,external_wp_i18n_namespaceObject.__)("Backtick"),
      /* translators: tilde as in the character '~' */
      "~": (0,external_wp_i18n_namespaceObject.__)("Tilde")
    };
    return [...modifier(_isApple), character].map(
      (key) => capitaliseFirstCharacter(replacementKeyMap[key] ?? key)
    ).join(isApple ? " " : " + ");
  };
});
function getEventModifiers(event) {
  return [ALT, CTRL, COMMAND, SHIFT].filter(
    (key) => event[`${key}Key`]
  );
}
const isKeyboardEvent = /* @__PURE__ */ mapValues(modifiers, (getModifiers) => {
  return (event, character, _isApple = isAppleOS) => {
    const mods = getModifiers(_isApple);
    const eventMods = getEventModifiers(event);
    const replacementWithShiftKeyMap = {
      Comma: ",",
      Backslash: "\\",
      // Windows returns `\` for both IntlRo and IntlYen.
      IntlRo: "\\",
      IntlYen: "\\"
    };
    const modsDiff = mods.filter(
      (mod) => !eventMods.includes(mod)
    );
    const eventModsDiff = eventMods.filter(
      (mod) => !mods.includes(mod)
    );
    if (modsDiff.length > 0 || eventModsDiff.length > 0) {
      return false;
    }
    let key = event.key.toLowerCase();
    if (!character) {
      return mods.includes(key);
    }
    if (event.altKey && character.length === 1) {
      key = String.fromCharCode(event.keyCode).toLowerCase();
    }
    if (event.shiftKey && character.length === 1 && replacementWithShiftKeyMap[event.code]) {
      key = replacementWithShiftKeyMap[event.code];
    }
    if (character === "del") {
      character = "delete";
    }
    return key === character.toLowerCase();
  };
});


(window.wp = window.wp || {}).keycodes = __webpack_exports__;
/******/ })()
;