/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 677:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ stripTags)
/* harmony export */ });
function stripTags(settings, text) {
  return text.replace(settings.HTMLRegExp, "\n");
}



/***/ }),

/***/ 2125:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ transposeAstralsToCountableChar)
/* harmony export */ });
function transposeAstralsToCountableChar(settings, text) {
  return text.replace(settings.astralRegExp, "a");
}



/***/ }),

/***/ 3608:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ stripConnectors)
/* harmony export */ });
function stripConnectors(settings, text) {
  return text.replace(settings.connectorRegExp, " ");
}



/***/ }),

/***/ 4516:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ stripShortcodes)
/* harmony export */ });
function stripShortcodes(settings, text) {
  if (settings.shortcodesRegExp) {
    return text.replace(settings.shortcodesRegExp, "\n");
  }
  return text;
}



/***/ }),

/***/ 4579:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ stripHTMLComments)
/* harmony export */ });
function stripHTMLComments(settings, text) {
  return text.replace(settings.HTMLcommentRegExp, "");
}



/***/ }),

/***/ 4846:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ stripHTMLEntities)
/* harmony export */ });
function stripHTMLEntities(settings, text) {
  return text.replace(settings.HTMLEntityRegExp, "");
}



/***/ }),

/***/ 6019:
/***/ (() => {



/***/ }),

/***/ 6542:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ stripRemovables)
/* harmony export */ });
function stripRemovables(settings, text) {
  return text.replace(settings.removeRegExp, "");
}



/***/ }),

/***/ 7742:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   L: () => (/* binding */ defaultSettings)
/* harmony export */ });
const defaultSettings = {
  HTMLRegExp: /<\/?[a-z][^>]*?>/gi,
  HTMLcommentRegExp: /<!--[\s\S]*?-->/g,
  spaceRegExp: /&nbsp;|&#160;/gi,
  HTMLEntityRegExp: /&\S+?;/g,
  // \u2014 = em-dash.
  connectorRegExp: /--|\u2014/g,
  // Characters to be removed from input text.
  removeRegExp: new RegExp(
    [
      "[",
      // Basic Latin (extract)
      "!-/:-@[-`{-~",
      // Latin-1 Supplement (extract)
      "\x80-\xBF\xD7\xF7",
      /*
       * The following range consists of:
       * General Punctuation
       * Superscripts and Subscripts
       * Currency Symbols
       * Combining Diacritical Marks for Symbols
       * Letterlike Symbols
       * Number Forms
       * Arrows
       * Mathematical Operators
       * Miscellaneous Technical
       * Control Pictures
       * Optical Character Recognition
       * Enclosed Alphanumerics
       * Box Drawing
       * Block Elements
       * Geometric Shapes
       * Miscellaneous Symbols
       * Dingbats
       * Miscellaneous Mathematical Symbols-A
       * Supplemental Arrows-A
       * Braille Patterns
       * Supplemental Arrows-B
       * Miscellaneous Mathematical Symbols-B
       * Supplemental Mathematical Operators
       * Miscellaneous Symbols and Arrows
       */
      "\u2000-\u2BFF",
      // Supplemental Punctuation.
      "\u2E00-\u2E7F",
      "]"
    ].join(""),
    "g"
  ),
  // Remove UTF-16 surrogate points, see https://en.wikipedia.org/wiki/UTF-16#U.2BD800_to_U.2BDFFF
  astralRegExp: /[\uD800-\uDBFF][\uDC00-\uDFFF]/g,
  wordsRegExp: /\S\s+/g,
  characters_excluding_spacesRegExp: /\S/g,
  /*
   * Match anything that is not a formatting character, excluding:
   * \f = form feed
   * \n = new line
   * \r = carriage return
   * \t = tab
   * \v = vertical tab
   * \u00AD = soft hyphen
   * \u2028 = line separator
   * \u2029 = paragraph separator
   */
  characters_including_spacesRegExp: /[^\f\n\r\t\v\u00AD\u2028\u2029]/g,
  l10n: {
    type: "words"
  }
};



/***/ }),

/***/ 8026:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ transposeHTMLEntitiesToCountableChars)
/* harmony export */ });
function transposeHTMLEntitiesToCountableChars(settings, text) {
  return text.replace(settings.HTMLEntityRegExp, "a");
}



/***/ }),

/***/ 8511:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ stripSpaces)
/* harmony export */ });
function stripSpaces(settings, text) {
  return text.replace(settings.spaceRegExp, " ");
}



/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
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
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   count: () => (/* binding */ count)
/* harmony export */ });
/* harmony import */ var _defaultSettings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(7742);
/* harmony import */ var _stripTags__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(677);
/* harmony import */ var _transposeAstralsToCountableChar__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(2125);
/* harmony import */ var _stripHTMLEntities__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(4846);
/* harmony import */ var _stripConnectors__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(3608);
/* harmony import */ var _stripRemovables__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(6542);
/* harmony import */ var _stripHTMLComments__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(4579);
/* harmony import */ var _stripShortcodes__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(4516);
/* harmony import */ var _stripSpaces__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(8511);
/* harmony import */ var _transposeHTMLEntitiesToCountableChars__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(8026);
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(6019);
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_types__WEBPACK_IMPORTED_MODULE_10__);
/* harmony reexport (unknown) */ var __WEBPACK_REEXPORT_OBJECT__ = {};
/* harmony reexport (unknown) */ for(const __WEBPACK_IMPORT_KEY__ in _types__WEBPACK_IMPORTED_MODULE_10__) if(["default","count"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) __WEBPACK_REEXPORT_OBJECT__[__WEBPACK_IMPORT_KEY__] = () => _types__WEBPACK_IMPORTED_MODULE_10__[__WEBPACK_IMPORT_KEY__]
/* harmony reexport (unknown) */ __webpack_require__.d(__webpack_exports__, __WEBPACK_REEXPORT_OBJECT__);










function loadSettings(type = "words", userSettings = {}) {
  const mergedSettings = { ..._defaultSettings__WEBPACK_IMPORTED_MODULE_0__/* .defaultSettings */ .L, ...userSettings };
  const settings = {
    ...mergedSettings,
    type,
    shortcodes: []
  };
  settings.shortcodes = settings.l10n?.shortcodes ?? [];
  if (settings.shortcodes && settings.shortcodes.length) {
    settings.shortcodesRegExp = new RegExp(
      "\\[\\/?(?:" + settings.shortcodes.join("|") + ")[^\\]]*?\\]",
      "g"
    );
  }
  if (settings.type !== "characters_excluding_spaces" && settings.type !== "characters_including_spaces") {
    settings.type = "words";
  }
  return settings;
}
function countWords(text, regex, settings) {
  text = [
    _stripTags__WEBPACK_IMPORTED_MODULE_1__/* ["default"] */ .A.bind(null, settings),
    _stripHTMLComments__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A.bind(null, settings),
    _stripShortcodes__WEBPACK_IMPORTED_MODULE_3__/* ["default"] */ .A.bind(null, settings),
    _stripSpaces__WEBPACK_IMPORTED_MODULE_4__/* ["default"] */ .A.bind(null, settings),
    _stripHTMLEntities__WEBPACK_IMPORTED_MODULE_5__/* ["default"] */ .A.bind(null, settings),
    _stripConnectors__WEBPACK_IMPORTED_MODULE_6__/* ["default"] */ .A.bind(null, settings),
    _stripRemovables__WEBPACK_IMPORTED_MODULE_7__/* ["default"] */ .A.bind(null, settings)
  ].reduce((result, fn) => fn(result), text);
  text = text + "\n";
  return text.match(regex)?.length ?? 0;
}
function countCharacters(text, regex, settings) {
  text = [
    _stripTags__WEBPACK_IMPORTED_MODULE_1__/* ["default"] */ .A.bind(null, settings),
    _stripHTMLComments__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A.bind(null, settings),
    _stripShortcodes__WEBPACK_IMPORTED_MODULE_3__/* ["default"] */ .A.bind(null, settings),
    _transposeAstralsToCountableChar__WEBPACK_IMPORTED_MODULE_8__/* ["default"] */ .A.bind(null, settings),
    _stripSpaces__WEBPACK_IMPORTED_MODULE_4__/* ["default"] */ .A.bind(null, settings),
    _transposeHTMLEntitiesToCountableChars__WEBPACK_IMPORTED_MODULE_9__/* ["default"] */ .A.bind(null, settings)
  ].reduce((result, fn) => fn(result), text);
  text = text + "\n";
  return text.match(regex)?.length ?? 0;
}
function count(text, type, userSettings) {
  const settings = loadSettings(type, userSettings);
  let matchRegExp;
  switch (settings.type) {
    case "words":
      matchRegExp = settings.wordsRegExp;
      return countWords(text, matchRegExp, settings);
    case "characters_including_spaces":
      matchRegExp = settings.characters_including_spacesRegExp;
      return countCharacters(text, matchRegExp, settings);
    case "characters_excluding_spaces":
      matchRegExp = settings.characters_excluding_spacesRegExp;
      return countCharacters(text, matchRegExp, settings);
    default:
      return 0;
  }
}



})();

(window.wp = window.wp || {}).wordcount = __webpack_exports__;
/******/ })()
;