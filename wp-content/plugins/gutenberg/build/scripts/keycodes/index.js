"use strict";
var wp;
(wp ||= {}).keycodes = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
    // If the importer is in node compatibility mode or this is not an ESM
    // file that has been converted to a CommonJS file using a Babel-
    // compatible transform (i.e. "__esModule" has not been set), then set
    // "default" to the CommonJS "module.exports" for node compatibility.
    isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
    mod
  ));
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // packages/keycodes/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    ALT: () => ALT,
    BACKSPACE: () => BACKSPACE,
    COMMAND: () => COMMAND,
    CTRL: () => CTRL,
    DELETE: () => DELETE,
    DOWN: () => DOWN,
    END: () => END,
    ENTER: () => ENTER,
    ESCAPE: () => ESCAPE,
    F10: () => F10,
    HOME: () => HOME,
    LEFT: () => LEFT,
    PAGEDOWN: () => PAGEDOWN,
    PAGEUP: () => PAGEUP,
    RIGHT: () => RIGHT,
    SHIFT: () => SHIFT,
    SPACE: () => SPACE,
    TAB: () => TAB,
    UP: () => UP,
    ZERO: () => ZERO,
    displayShortcut: () => displayShortcut,
    displayShortcutList: () => displayShortcutList,
    isAppleOS: () => isAppleOS,
    isKeyboardEvent: () => isKeyboardEvent,
    modifiers: () => modifiers,
    rawShortcut: () => rawShortcut,
    shortcutAriaLabel: () => shortcutAriaLabel
  });
  var import_i18n = __toESM(require_i18n());

  // packages/keycodes/build-module/platform.js
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

  // packages/keycodes/build-module/index.js
  var BACKSPACE = 8;
  var TAB = 9;
  var ENTER = 13;
  var ESCAPE = 27;
  var SPACE = 32;
  var PAGEUP = 33;
  var PAGEDOWN = 34;
  var END = 35;
  var HOME = 36;
  var LEFT = 37;
  var UP = 38;
  var RIGHT = 39;
  var DOWN = 40;
  var DELETE = 46;
  var F10 = 121;
  var ALT = "alt";
  var CTRL = "ctrl";
  var COMMAND = "meta";
  var SHIFT = "shift";
  var ZERO = 48;
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
  var modifiers = {
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
  var rawShortcut = /* @__PURE__ */ mapValues(modifiers, (modifier) => {
    return (character, _isApple = isAppleOS) => {
      return [...modifier(_isApple), character.toLowerCase()].join(
        "+"
      );
    };
  });
  var displayShortcutList = /* @__PURE__ */ mapValues(
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
  var displayShortcut = /* @__PURE__ */ mapValues(
    displayShortcutList,
    (shortcutList) => {
      return (character, _isApple = isAppleOS) => shortcutList(character, _isApple).join("");
    }
  );
  var shortcutAriaLabel = /* @__PURE__ */ mapValues(modifiers, (modifier) => {
    return (character, _isApple = isAppleOS) => {
      const isApple = _isApple();
      const replacementKeyMap = {
        [SHIFT]: "Shift",
        [COMMAND]: isApple ? "Command" : "Control",
        [CTRL]: "Control",
        [ALT]: isApple ? "Option" : "Alt",
        /* translators: comma as in the character ',' */
        ",": (0, import_i18n.__)("Comma"),
        /* translators: period as in the character '.' */
        ".": (0, import_i18n.__)("Period"),
        /* translators: backtick as in the character '`' */
        "`": (0, import_i18n.__)("Backtick"),
        /* translators: tilde as in the character '~' */
        "~": (0, import_i18n.__)("Tilde")
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
  var isKeyboardEvent = /* @__PURE__ */ mapValues(modifiers, (getModifiers) => {
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
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
