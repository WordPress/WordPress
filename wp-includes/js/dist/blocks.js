var wp;
(wp ||= {}).blocks = (() => {
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

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/dom
  var require_dom = __commonJS({
    "package-external:@wordpress/dom"(exports, module) {
      module.exports = window.wp.dom;
    }
  });

  // package-external:@wordpress/rich-text
  var require_rich_text = __commonJS({
    "package-external:@wordpress/rich-text"(exports, module) {
      module.exports = window.wp.richText;
    }
  });

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // package-external:@wordpress/warning
  var require_warning = __commonJS({
    "package-external:@wordpress/warning"(exports, module) {
      module.exports = window.wp.warning;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // node_modules/remove-accents/index.js
  var require_remove_accents = __commonJS({
    "node_modules/remove-accents/index.js"(exports, module) {
      var characterMap = {
        "\xC0": "A",
        "\xC1": "A",
        "\xC2": "A",
        "\xC3": "A",
        "\xC4": "A",
        "\xC5": "A",
        "\u1EA4": "A",
        "\u1EAE": "A",
        "\u1EB2": "A",
        "\u1EB4": "A",
        "\u1EB6": "A",
        "\xC6": "AE",
        "\u1EA6": "A",
        "\u1EB0": "A",
        "\u0202": "A",
        "\u1EA2": "A",
        "\u1EA0": "A",
        "\u1EA8": "A",
        "\u1EAA": "A",
        "\u1EAC": "A",
        "\xC7": "C",
        "\u1E08": "C",
        "\xC8": "E",
        "\xC9": "E",
        "\xCA": "E",
        "\xCB": "E",
        "\u1EBE": "E",
        "\u1E16": "E",
        "\u1EC0": "E",
        "\u1E14": "E",
        "\u1E1C": "E",
        "\u0206": "E",
        "\u1EBA": "E",
        "\u1EBC": "E",
        "\u1EB8": "E",
        "\u1EC2": "E",
        "\u1EC4": "E",
        "\u1EC6": "E",
        "\xCC": "I",
        "\xCD": "I",
        "\xCE": "I",
        "\xCF": "I",
        "\u1E2E": "I",
        "\u020A": "I",
        "\u1EC8": "I",
        "\u1ECA": "I",
        "\xD0": "D",
        "\xD1": "N",
        "\xD2": "O",
        "\xD3": "O",
        "\xD4": "O",
        "\xD5": "O",
        "\xD6": "O",
        "\xD8": "O",
        "\u1ED0": "O",
        "\u1E4C": "O",
        "\u1E52": "O",
        "\u020E": "O",
        "\u1ECE": "O",
        "\u1ECC": "O",
        "\u1ED4": "O",
        "\u1ED6": "O",
        "\u1ED8": "O",
        "\u1EDC": "O",
        "\u1EDE": "O",
        "\u1EE0": "O",
        "\u1EDA": "O",
        "\u1EE2": "O",
        "\xD9": "U",
        "\xDA": "U",
        "\xDB": "U",
        "\xDC": "U",
        "\u1EE6": "U",
        "\u1EE4": "U",
        "\u1EEC": "U",
        "\u1EEE": "U",
        "\u1EF0": "U",
        "\xDD": "Y",
        "\xE0": "a",
        "\xE1": "a",
        "\xE2": "a",
        "\xE3": "a",
        "\xE4": "a",
        "\xE5": "a",
        "\u1EA5": "a",
        "\u1EAF": "a",
        "\u1EB3": "a",
        "\u1EB5": "a",
        "\u1EB7": "a",
        "\xE6": "ae",
        "\u1EA7": "a",
        "\u1EB1": "a",
        "\u0203": "a",
        "\u1EA3": "a",
        "\u1EA1": "a",
        "\u1EA9": "a",
        "\u1EAB": "a",
        "\u1EAD": "a",
        "\xE7": "c",
        "\u1E09": "c",
        "\xE8": "e",
        "\xE9": "e",
        "\xEA": "e",
        "\xEB": "e",
        "\u1EBF": "e",
        "\u1E17": "e",
        "\u1EC1": "e",
        "\u1E15": "e",
        "\u1E1D": "e",
        "\u0207": "e",
        "\u1EBB": "e",
        "\u1EBD": "e",
        "\u1EB9": "e",
        "\u1EC3": "e",
        "\u1EC5": "e",
        "\u1EC7": "e",
        "\xEC": "i",
        "\xED": "i",
        "\xEE": "i",
        "\xEF": "i",
        "\u1E2F": "i",
        "\u020B": "i",
        "\u1EC9": "i",
        "\u1ECB": "i",
        "\xF0": "d",
        "\xF1": "n",
        "\xF2": "o",
        "\xF3": "o",
        "\xF4": "o",
        "\xF5": "o",
        "\xF6": "o",
        "\xF8": "o",
        "\u1ED1": "o",
        "\u1E4D": "o",
        "\u1E53": "o",
        "\u020F": "o",
        "\u1ECF": "o",
        "\u1ECD": "o",
        "\u1ED5": "o",
        "\u1ED7": "o",
        "\u1ED9": "o",
        "\u1EDD": "o",
        "\u1EDF": "o",
        "\u1EE1": "o",
        "\u1EDB": "o",
        "\u1EE3": "o",
        "\xF9": "u",
        "\xFA": "u",
        "\xFB": "u",
        "\xFC": "u",
        "\u1EE7": "u",
        "\u1EE5": "u",
        "\u1EED": "u",
        "\u1EEF": "u",
        "\u1EF1": "u",
        "\xFD": "y",
        "\xFF": "y",
        "\u0100": "A",
        "\u0101": "a",
        "\u0102": "A",
        "\u0103": "a",
        "\u0104": "A",
        "\u0105": "a",
        "\u0106": "C",
        "\u0107": "c",
        "\u0108": "C",
        "\u0109": "c",
        "\u010A": "C",
        "\u010B": "c",
        "\u010C": "C",
        "\u010D": "c",
        "C\u0306": "C",
        "c\u0306": "c",
        "\u010E": "D",
        "\u010F": "d",
        "\u0110": "D",
        "\u0111": "d",
        "\u0112": "E",
        "\u0113": "e",
        "\u0114": "E",
        "\u0115": "e",
        "\u0116": "E",
        "\u0117": "e",
        "\u0118": "E",
        "\u0119": "e",
        "\u011A": "E",
        "\u011B": "e",
        "\u011C": "G",
        "\u01F4": "G",
        "\u011D": "g",
        "\u01F5": "g",
        "\u011E": "G",
        "\u011F": "g",
        "\u0120": "G",
        "\u0121": "g",
        "\u0122": "G",
        "\u0123": "g",
        "\u0124": "H",
        "\u0125": "h",
        "\u0126": "H",
        "\u0127": "h",
        "\u1E2A": "H",
        "\u1E2B": "h",
        "\u0128": "I",
        "\u0129": "i",
        "\u012A": "I",
        "\u012B": "i",
        "\u012C": "I",
        "\u012D": "i",
        "\u012E": "I",
        "\u012F": "i",
        "\u0130": "I",
        "\u0131": "i",
        "\u0132": "IJ",
        "\u0133": "ij",
        "\u0134": "J",
        "\u0135": "j",
        "\u0136": "K",
        "\u0137": "k",
        "\u1E30": "K",
        "\u1E31": "k",
        "K\u0306": "K",
        "k\u0306": "k",
        "\u0139": "L",
        "\u013A": "l",
        "\u013B": "L",
        "\u013C": "l",
        "\u013D": "L",
        "\u013E": "l",
        "\u013F": "L",
        "\u0140": "l",
        "\u0141": "l",
        "\u0142": "l",
        "\u1E3E": "M",
        "\u1E3F": "m",
        "M\u0306": "M",
        "m\u0306": "m",
        "\u0143": "N",
        "\u0144": "n",
        "\u0145": "N",
        "\u0146": "n",
        "\u0147": "N",
        "\u0148": "n",
        "\u0149": "n",
        "N\u0306": "N",
        "n\u0306": "n",
        "\u014C": "O",
        "\u014D": "o",
        "\u014E": "O",
        "\u014F": "o",
        "\u0150": "O",
        "\u0151": "o",
        "\u0152": "OE",
        "\u0153": "oe",
        "P\u0306": "P",
        "p\u0306": "p",
        "\u0154": "R",
        "\u0155": "r",
        "\u0156": "R",
        "\u0157": "r",
        "\u0158": "R",
        "\u0159": "r",
        "R\u0306": "R",
        "r\u0306": "r",
        "\u0212": "R",
        "\u0213": "r",
        "\u015A": "S",
        "\u015B": "s",
        "\u015C": "S",
        "\u015D": "s",
        "\u015E": "S",
        "\u0218": "S",
        "\u0219": "s",
        "\u015F": "s",
        "\u0160": "S",
        "\u0161": "s",
        "\u0162": "T",
        "\u0163": "t",
        "\u021B": "t",
        "\u021A": "T",
        "\u0164": "T",
        "\u0165": "t",
        "\u0166": "T",
        "\u0167": "t",
        "T\u0306": "T",
        "t\u0306": "t",
        "\u0168": "U",
        "\u0169": "u",
        "\u016A": "U",
        "\u016B": "u",
        "\u016C": "U",
        "\u016D": "u",
        "\u016E": "U",
        "\u016F": "u",
        "\u0170": "U",
        "\u0171": "u",
        "\u0172": "U",
        "\u0173": "u",
        "\u0216": "U",
        "\u0217": "u",
        "V\u0306": "V",
        "v\u0306": "v",
        "\u0174": "W",
        "\u0175": "w",
        "\u1E82": "W",
        "\u1E83": "w",
        "X\u0306": "X",
        "x\u0306": "x",
        "\u0176": "Y",
        "\u0177": "y",
        "\u0178": "Y",
        "Y\u0306": "Y",
        "y\u0306": "y",
        "\u0179": "Z",
        "\u017A": "z",
        "\u017B": "Z",
        "\u017C": "z",
        "\u017D": "Z",
        "\u017E": "z",
        "\u017F": "s",
        "\u0192": "f",
        "\u01A0": "O",
        "\u01A1": "o",
        "\u01AF": "U",
        "\u01B0": "u",
        "\u01CD": "A",
        "\u01CE": "a",
        "\u01CF": "I",
        "\u01D0": "i",
        "\u01D1": "O",
        "\u01D2": "o",
        "\u01D3": "U",
        "\u01D4": "u",
        "\u01D5": "U",
        "\u01D6": "u",
        "\u01D7": "U",
        "\u01D8": "u",
        "\u01D9": "U",
        "\u01DA": "u",
        "\u01DB": "U",
        "\u01DC": "u",
        "\u1EE8": "U",
        "\u1EE9": "u",
        "\u1E78": "U",
        "\u1E79": "u",
        "\u01FA": "A",
        "\u01FB": "a",
        "\u01FC": "AE",
        "\u01FD": "ae",
        "\u01FE": "O",
        "\u01FF": "o",
        "\xDE": "TH",
        "\xFE": "th",
        "\u1E54": "P",
        "\u1E55": "p",
        "\u1E64": "S",
        "\u1E65": "s",
        "X\u0301": "X",
        "x\u0301": "x",
        "\u0403": "\u0413",
        "\u0453": "\u0433",
        "\u040C": "\u041A",
        "\u045C": "\u043A",
        "A\u030B": "A",
        "a\u030B": "a",
        "E\u030B": "E",
        "e\u030B": "e",
        "I\u030B": "I",
        "i\u030B": "i",
        "\u01F8": "N",
        "\u01F9": "n",
        "\u1ED2": "O",
        "\u1ED3": "o",
        "\u1E50": "O",
        "\u1E51": "o",
        "\u1EEA": "U",
        "\u1EEB": "u",
        "\u1E80": "W",
        "\u1E81": "w",
        "\u1EF2": "Y",
        "\u1EF3": "y",
        "\u0200": "A",
        "\u0201": "a",
        "\u0204": "E",
        "\u0205": "e",
        "\u0208": "I",
        "\u0209": "i",
        "\u020C": "O",
        "\u020D": "o",
        "\u0210": "R",
        "\u0211": "r",
        "\u0214": "U",
        "\u0215": "u",
        "B\u030C": "B",
        "b\u030C": "b",
        "\u010C\u0323": "C",
        "\u010D\u0323": "c",
        "\xCA\u030C": "E",
        "\xEA\u030C": "e",
        "F\u030C": "F",
        "f\u030C": "f",
        "\u01E6": "G",
        "\u01E7": "g",
        "\u021E": "H",
        "\u021F": "h",
        "J\u030C": "J",
        "\u01F0": "j",
        "\u01E8": "K",
        "\u01E9": "k",
        "M\u030C": "M",
        "m\u030C": "m",
        "P\u030C": "P",
        "p\u030C": "p",
        "Q\u030C": "Q",
        "q\u030C": "q",
        "\u0158\u0329": "R",
        "\u0159\u0329": "r",
        "\u1E66": "S",
        "\u1E67": "s",
        "V\u030C": "V",
        "v\u030C": "v",
        "W\u030C": "W",
        "w\u030C": "w",
        "X\u030C": "X",
        "x\u030C": "x",
        "Y\u030C": "Y",
        "y\u030C": "y",
        "A\u0327": "A",
        "a\u0327": "a",
        "B\u0327": "B",
        "b\u0327": "b",
        "\u1E10": "D",
        "\u1E11": "d",
        "\u0228": "E",
        "\u0229": "e",
        "\u0190\u0327": "E",
        "\u025B\u0327": "e",
        "\u1E28": "H",
        "\u1E29": "h",
        "I\u0327": "I",
        "i\u0327": "i",
        "\u0197\u0327": "I",
        "\u0268\u0327": "i",
        "M\u0327": "M",
        "m\u0327": "m",
        "O\u0327": "O",
        "o\u0327": "o",
        "Q\u0327": "Q",
        "q\u0327": "q",
        "U\u0327": "U",
        "u\u0327": "u",
        "X\u0327": "X",
        "x\u0327": "x",
        "Z\u0327": "Z",
        "z\u0327": "z",
        "\u0439": "\u0438",
        "\u0419": "\u0418",
        "\u0451": "\u0435",
        "\u0401": "\u0415"
      };
      var chars = Object.keys(characterMap).join("|");
      var allAccents = new RegExp(chars, "g");
      var firstAccent = new RegExp(chars, "");
      function matcher3(match) {
        return characterMap[match];
      }
      var removeAccents2 = function(string) {
        return string.replace(allAccents, matcher3);
      };
      var hasAccents = function(string) {
        return !!string.match(firstAccent);
      };
      module.exports = removeAccents2;
      module.exports.has = hasAccents;
      module.exports.remove = removeAccents2;
    }
  });

  // packages/blocks/node_modules/react-is/cjs/react-is.development.js
  var require_react_is_development = __commonJS({
    "packages/blocks/node_modules/react-is/cjs/react-is.development.js"(exports) {
      "use strict";
      if (true) {
        (function() {
          "use strict";
          var REACT_ELEMENT_TYPE = Symbol.for("react.element");
          var REACT_PORTAL_TYPE = Symbol.for("react.portal");
          var REACT_FRAGMENT_TYPE = Symbol.for("react.fragment");
          var REACT_STRICT_MODE_TYPE = Symbol.for("react.strict_mode");
          var REACT_PROFILER_TYPE = Symbol.for("react.profiler");
          var REACT_PROVIDER_TYPE = Symbol.for("react.provider");
          var REACT_CONTEXT_TYPE = Symbol.for("react.context");
          var REACT_SERVER_CONTEXT_TYPE = Symbol.for("react.server_context");
          var REACT_FORWARD_REF_TYPE = Symbol.for("react.forward_ref");
          var REACT_SUSPENSE_TYPE = Symbol.for("react.suspense");
          var REACT_SUSPENSE_LIST_TYPE = Symbol.for("react.suspense_list");
          var REACT_MEMO_TYPE = Symbol.for("react.memo");
          var REACT_LAZY_TYPE = Symbol.for("react.lazy");
          var REACT_OFFSCREEN_TYPE = Symbol.for("react.offscreen");
          var enableScopeAPI = false;
          var enableCacheElement = false;
          var enableTransitionTracing = false;
          var enableLegacyHidden = false;
          var enableDebugTracing = false;
          var REACT_MODULE_REFERENCE;
          {
            REACT_MODULE_REFERENCE = Symbol.for("react.module.reference");
          }
          function isValidElementType2(type) {
            if (typeof type === "string" || typeof type === "function") {
              return true;
            }
            if (type === REACT_FRAGMENT_TYPE || type === REACT_PROFILER_TYPE || enableDebugTracing || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || enableLegacyHidden || type === REACT_OFFSCREEN_TYPE || enableScopeAPI || enableCacheElement || enableTransitionTracing) {
              return true;
            }
            if (typeof type === "object" && type !== null) {
              if (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || // This needs to include all possible module reference object
              // types supported by any Flight configuration anywhere since
              // we don't know which Flight build this will end up being used
              // with.
              type.$$typeof === REACT_MODULE_REFERENCE || type.getModuleId !== void 0) {
                return true;
              }
            }
            return false;
          }
          function typeOf(object) {
            if (typeof object === "object" && object !== null) {
              var $$typeof = object.$$typeof;
              switch ($$typeof) {
                case REACT_ELEMENT_TYPE:
                  var type = object.type;
                  switch (type) {
                    case REACT_FRAGMENT_TYPE:
                    case REACT_PROFILER_TYPE:
                    case REACT_STRICT_MODE_TYPE:
                    case REACT_SUSPENSE_TYPE:
                    case REACT_SUSPENSE_LIST_TYPE:
                      return type;
                    default:
                      var $$typeofType = type && type.$$typeof;
                      switch ($$typeofType) {
                        case REACT_SERVER_CONTEXT_TYPE:
                        case REACT_CONTEXT_TYPE:
                        case REACT_FORWARD_REF_TYPE:
                        case REACT_LAZY_TYPE:
                        case REACT_MEMO_TYPE:
                        case REACT_PROVIDER_TYPE:
                          return $$typeofType;
                        default:
                          return $$typeof;
                      }
                  }
                case REACT_PORTAL_TYPE:
                  return $$typeof;
              }
            }
            return void 0;
          }
          var ContextConsumer = REACT_CONTEXT_TYPE;
          var ContextProvider = REACT_PROVIDER_TYPE;
          var Element = REACT_ELEMENT_TYPE;
          var ForwardRef = REACT_FORWARD_REF_TYPE;
          var Fragment = REACT_FRAGMENT_TYPE;
          var Lazy = REACT_LAZY_TYPE;
          var Memo = REACT_MEMO_TYPE;
          var Portal = REACT_PORTAL_TYPE;
          var Profiler = REACT_PROFILER_TYPE;
          var StrictMode = REACT_STRICT_MODE_TYPE;
          var Suspense = REACT_SUSPENSE_TYPE;
          var SuspenseList = REACT_SUSPENSE_LIST_TYPE;
          var hasWarnedAboutDeprecatedIsAsyncMode = false;
          var hasWarnedAboutDeprecatedIsConcurrentMode = false;
          function isAsyncMode(object) {
            {
              if (!hasWarnedAboutDeprecatedIsAsyncMode) {
                hasWarnedAboutDeprecatedIsAsyncMode = true;
                console["warn"]("The ReactIs.isAsyncMode() alias has been deprecated, and will be removed in React 18+.");
              }
            }
            return false;
          }
          function isConcurrentMode(object) {
            {
              if (!hasWarnedAboutDeprecatedIsConcurrentMode) {
                hasWarnedAboutDeprecatedIsConcurrentMode = true;
                console["warn"]("The ReactIs.isConcurrentMode() alias has been deprecated, and will be removed in React 18+.");
              }
            }
            return false;
          }
          function isContextConsumer(object) {
            return typeOf(object) === REACT_CONTEXT_TYPE;
          }
          function isContextProvider(object) {
            return typeOf(object) === REACT_PROVIDER_TYPE;
          }
          function isElement(object) {
            return typeof object === "object" && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
          }
          function isForwardRef(object) {
            return typeOf(object) === REACT_FORWARD_REF_TYPE;
          }
          function isFragment(object) {
            return typeOf(object) === REACT_FRAGMENT_TYPE;
          }
          function isLazy(object) {
            return typeOf(object) === REACT_LAZY_TYPE;
          }
          function isMemo(object) {
            return typeOf(object) === REACT_MEMO_TYPE;
          }
          function isPortal(object) {
            return typeOf(object) === REACT_PORTAL_TYPE;
          }
          function isProfiler(object) {
            return typeOf(object) === REACT_PROFILER_TYPE;
          }
          function isStrictMode(object) {
            return typeOf(object) === REACT_STRICT_MODE_TYPE;
          }
          function isSuspense(object) {
            return typeOf(object) === REACT_SUSPENSE_TYPE;
          }
          function isSuspenseList(object) {
            return typeOf(object) === REACT_SUSPENSE_LIST_TYPE;
          }
          exports.ContextConsumer = ContextConsumer;
          exports.ContextProvider = ContextProvider;
          exports.Element = Element;
          exports.ForwardRef = ForwardRef;
          exports.Fragment = Fragment;
          exports.Lazy = Lazy;
          exports.Memo = Memo;
          exports.Portal = Portal;
          exports.Profiler = Profiler;
          exports.StrictMode = StrictMode;
          exports.Suspense = Suspense;
          exports.SuspenseList = SuspenseList;
          exports.isAsyncMode = isAsyncMode;
          exports.isConcurrentMode = isConcurrentMode;
          exports.isContextConsumer = isContextConsumer;
          exports.isContextProvider = isContextProvider;
          exports.isElement = isElement;
          exports.isForwardRef = isForwardRef;
          exports.isFragment = isFragment;
          exports.isLazy = isLazy;
          exports.isMemo = isMemo;
          exports.isPortal = isPortal;
          exports.isProfiler = isProfiler;
          exports.isStrictMode = isStrictMode;
          exports.isSuspense = isSuspense;
          exports.isSuspenseList = isSuspenseList;
          exports.isValidElementType = isValidElementType2;
          exports.typeOf = typeOf;
        })();
      }
    }
  });

  // packages/blocks/node_modules/react-is/index.js
  var require_react_is = __commonJS({
    "packages/blocks/node_modules/react-is/index.js"(exports, module) {
      "use strict";
      if (false) {
        module.exports = null;
      } else {
        module.exports = require_react_is_development();
      }
    }
  });

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // package-external:@wordpress/block-serialization-default-parser
  var require_block_serialization_default_parser = __commonJS({
    "package-external:@wordpress/block-serialization-default-parser"(exports, module) {
      module.exports = window.wp.blockSerializationDefaultParser;
    }
  });

  // package-external:@wordpress/autop
  var require_autop = __commonJS({
    "package-external:@wordpress/autop"(exports, module) {
      module.exports = window.wp.autop;
    }
  });

  // package-external:@wordpress/is-shallow-equal
  var require_is_shallow_equal = __commonJS({
    "package-external:@wordpress/is-shallow-equal"(exports, module) {
      module.exports = window.wp.isShallowEqual;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // node_modules/fast-deep-equal/es6/index.js
  var require_es6 = __commonJS({
    "node_modules/fast-deep-equal/es6/index.js"(exports, module) {
      "use strict";
      module.exports = function equal(a2, b2) {
        if (a2 === b2) return true;
        if (a2 && b2 && typeof a2 == "object" && typeof b2 == "object") {
          if (a2.constructor !== b2.constructor) return false;
          var length, i2, keys;
          if (Array.isArray(a2)) {
            length = a2.length;
            if (length != b2.length) return false;
            for (i2 = length; i2-- !== 0; )
              if (!equal(a2[i2], b2[i2])) return false;
            return true;
          }
          if (a2 instanceof Map && b2 instanceof Map) {
            if (a2.size !== b2.size) return false;
            for (i2 of a2.entries())
              if (!b2.has(i2[0])) return false;
            for (i2 of a2.entries())
              if (!equal(i2[1], b2.get(i2[0]))) return false;
            return true;
          }
          if (a2 instanceof Set && b2 instanceof Set) {
            if (a2.size !== b2.size) return false;
            for (i2 of a2.entries())
              if (!b2.has(i2[0])) return false;
            return true;
          }
          if (ArrayBuffer.isView(a2) && ArrayBuffer.isView(b2)) {
            length = a2.length;
            if (length != b2.length) return false;
            for (i2 = length; i2-- !== 0; )
              if (a2[i2] !== b2[i2]) return false;
            return true;
          }
          if (a2.constructor === RegExp) return a2.source === b2.source && a2.flags === b2.flags;
          if (a2.valueOf !== Object.prototype.valueOf) return a2.valueOf() === b2.valueOf();
          if (a2.toString !== Object.prototype.toString) return a2.toString() === b2.toString();
          keys = Object.keys(a2);
          length = keys.length;
          if (length !== Object.keys(b2).length) return false;
          for (i2 = length; i2-- !== 0; )
            if (!Object.prototype.hasOwnProperty.call(b2, keys[i2])) return false;
          for (i2 = length; i2-- !== 0; ) {
            var key = keys[i2];
            if (!equal(a2[key], b2[key])) return false;
          }
          return true;
        }
        return a2 !== a2 && b2 !== b2;
      };
    }
  });

  // package-external:@wordpress/html-entities
  var require_html_entities = __commonJS({
    "package-external:@wordpress/html-entities"(exports, module) {
      module.exports = window.wp.htmlEntities;
    }
  });

  // package-external:@wordpress/shortcode
  var require_shortcode = __commonJS({
    "package-external:@wordpress/shortcode"(exports, module) {
      module.exports = window.wp.shortcode;
    }
  });

  // package-external:@wordpress/blob
  var require_blob = __commonJS({
    "package-external:@wordpress/blob"(exports, module) {
      module.exports = window.wp.blob;
    }
  });

  // node_modules/showdown/dist/showdown.js
  var require_showdown = __commonJS({
    "node_modules/showdown/dist/showdown.js"(exports, module) {
      (function() {
        function getDefaultOpts(simple) {
          "use strict";
          var defaultOptions = {
            omitExtraWLInCodeBlocks: {
              defaultValue: false,
              describe: "Omit the default extra whiteline added to code blocks",
              type: "boolean"
            },
            noHeaderId: {
              defaultValue: false,
              describe: "Turn on/off generated header id",
              type: "boolean"
            },
            prefixHeaderId: {
              defaultValue: false,
              describe: "Add a prefix to the generated header ids. Passing a string will prefix that string to the header id. Setting to true will add a generic 'section-' prefix",
              type: "string"
            },
            rawPrefixHeaderId: {
              defaultValue: false,
              describe: 'Setting this option to true will prevent showdown from modifying the prefix. This might result in malformed IDs (if, for instance, the " char is used in the prefix)',
              type: "boolean"
            },
            ghCompatibleHeaderId: {
              defaultValue: false,
              describe: "Generate header ids compatible with github style (spaces are replaced with dashes, a bunch of non alphanumeric chars are removed)",
              type: "boolean"
            },
            rawHeaderId: {
              defaultValue: false,
              describe: `Remove only spaces, ' and " from generated header ids (including prefixes), replacing them with dashes (-). WARNING: This might result in malformed ids`,
              type: "boolean"
            },
            headerLevelStart: {
              defaultValue: false,
              describe: "The header blocks level start",
              type: "integer"
            },
            parseImgDimensions: {
              defaultValue: false,
              describe: "Turn on/off image dimension parsing",
              type: "boolean"
            },
            simplifiedAutoLink: {
              defaultValue: false,
              describe: "Turn on/off GFM autolink style",
              type: "boolean"
            },
            excludeTrailingPunctuationFromURLs: {
              defaultValue: false,
              describe: "Excludes trailing punctuation from links generated with autoLinking",
              type: "boolean"
            },
            literalMidWordUnderscores: {
              defaultValue: false,
              describe: "Parse midword underscores as literal underscores",
              type: "boolean"
            },
            literalMidWordAsterisks: {
              defaultValue: false,
              describe: "Parse midword asterisks as literal asterisks",
              type: "boolean"
            },
            strikethrough: {
              defaultValue: false,
              describe: "Turn on/off strikethrough support",
              type: "boolean"
            },
            tables: {
              defaultValue: false,
              describe: "Turn on/off tables support",
              type: "boolean"
            },
            tablesHeaderId: {
              defaultValue: false,
              describe: "Add an id to table headers",
              type: "boolean"
            },
            ghCodeBlocks: {
              defaultValue: true,
              describe: "Turn on/off GFM fenced code blocks support",
              type: "boolean"
            },
            tasklists: {
              defaultValue: false,
              describe: "Turn on/off GFM tasklist support",
              type: "boolean"
            },
            smoothLivePreview: {
              defaultValue: false,
              describe: "Prevents weird effects in live previews due to incomplete input",
              type: "boolean"
            },
            smartIndentationFix: {
              defaultValue: false,
              description: "Tries to smartly fix indentation in es6 strings",
              type: "boolean"
            },
            disableForced4SpacesIndentedSublists: {
              defaultValue: false,
              description: "Disables the requirement of indenting nested sublists by 4 spaces",
              type: "boolean"
            },
            simpleLineBreaks: {
              defaultValue: false,
              description: "Parses simple line breaks as <br> (GFM Style)",
              type: "boolean"
            },
            requireSpaceBeforeHeadingText: {
              defaultValue: false,
              description: "Makes adding a space between `#` and the header text mandatory (GFM Style)",
              type: "boolean"
            },
            ghMentions: {
              defaultValue: false,
              description: "Enables github @mentions",
              type: "boolean"
            },
            ghMentionsLink: {
              defaultValue: "https://github.com/{u}",
              description: "Changes the link generated by @mentions. Only applies if ghMentions option is enabled.",
              type: "string"
            },
            encodeEmails: {
              defaultValue: true,
              description: "Encode e-mail addresses through the use of Character Entities, transforming ASCII e-mail addresses into its equivalent decimal entities",
              type: "boolean"
            },
            openLinksInNewWindow: {
              defaultValue: false,
              description: "Open all links in new windows",
              type: "boolean"
            },
            backslashEscapesHTMLTags: {
              defaultValue: false,
              description: "Support for HTML Tag escaping. ex: <div>foo</div>",
              type: "boolean"
            },
            emoji: {
              defaultValue: false,
              description: "Enable emoji support. Ex: `this is a :smile: emoji`",
              type: "boolean"
            },
            underline: {
              defaultValue: false,
              description: "Enable support for underline. Syntax is double or triple underscores: `__underline word__`. With this option enabled, underscores no longer parses into `<em>` and `<strong>`",
              type: "boolean"
            },
            completeHTMLDocument: {
              defaultValue: false,
              description: "Outputs a complete html document, including `<html>`, `<head>` and `<body>` tags",
              type: "boolean"
            },
            metadata: {
              defaultValue: false,
              description: "Enable support for document metadata (defined at the top of the document between `\xAB\xAB\xAB` and `\xBB\xBB\xBB` or between `---` and `---`).",
              type: "boolean"
            },
            splitAdjacentBlockquotes: {
              defaultValue: false,
              description: "Split adjacent blockquote blocks",
              type: "boolean"
            }
          };
          if (simple === false) {
            return JSON.parse(JSON.stringify(defaultOptions));
          }
          var ret = {};
          for (var opt in defaultOptions) {
            if (defaultOptions.hasOwnProperty(opt)) {
              ret[opt] = defaultOptions[opt].defaultValue;
            }
          }
          return ret;
        }
        function allOptionsOn() {
          "use strict";
          var options = getDefaultOpts(true), ret = {};
          for (var opt in options) {
            if (options.hasOwnProperty(opt)) {
              ret[opt] = true;
            }
          }
          return ret;
        }
        var showdown2 = {}, parsers = {}, extensions = {}, globalOptions = getDefaultOpts(true), setFlavor = "vanilla", flavor = {
          github: {
            omitExtraWLInCodeBlocks: true,
            simplifiedAutoLink: true,
            excludeTrailingPunctuationFromURLs: true,
            literalMidWordUnderscores: true,
            strikethrough: true,
            tables: true,
            tablesHeaderId: true,
            ghCodeBlocks: true,
            tasklists: true,
            disableForced4SpacesIndentedSublists: true,
            simpleLineBreaks: true,
            requireSpaceBeforeHeadingText: true,
            ghCompatibleHeaderId: true,
            ghMentions: true,
            backslashEscapesHTMLTags: true,
            emoji: true,
            splitAdjacentBlockquotes: true
          },
          original: {
            noHeaderId: true,
            ghCodeBlocks: false
          },
          ghost: {
            omitExtraWLInCodeBlocks: true,
            parseImgDimensions: true,
            simplifiedAutoLink: true,
            excludeTrailingPunctuationFromURLs: true,
            literalMidWordUnderscores: true,
            strikethrough: true,
            tables: true,
            tablesHeaderId: true,
            ghCodeBlocks: true,
            tasklists: true,
            smoothLivePreview: true,
            simpleLineBreaks: true,
            requireSpaceBeforeHeadingText: true,
            ghMentions: false,
            encodeEmails: true
          },
          vanilla: getDefaultOpts(true),
          allOn: allOptionsOn()
        };
        showdown2.helper = {};
        showdown2.extensions = {};
        showdown2.setOption = function(key, value) {
          "use strict";
          globalOptions[key] = value;
          return this;
        };
        showdown2.getOption = function(key) {
          "use strict";
          return globalOptions[key];
        };
        showdown2.getOptions = function() {
          "use strict";
          return globalOptions;
        };
        showdown2.resetOptions = function() {
          "use strict";
          globalOptions = getDefaultOpts(true);
        };
        showdown2.setFlavor = function(name) {
          "use strict";
          if (!flavor.hasOwnProperty(name)) {
            throw Error(name + " flavor was not found");
          }
          showdown2.resetOptions();
          var preset = flavor[name];
          setFlavor = name;
          for (var option in preset) {
            if (preset.hasOwnProperty(option)) {
              globalOptions[option] = preset[option];
            }
          }
        };
        showdown2.getFlavor = function() {
          "use strict";
          return setFlavor;
        };
        showdown2.getFlavorOptions = function(name) {
          "use strict";
          if (flavor.hasOwnProperty(name)) {
            return flavor[name];
          }
        };
        showdown2.getDefaultOptions = function(simple) {
          "use strict";
          return getDefaultOpts(simple);
        };
        showdown2.subParser = function(name, func) {
          "use strict";
          if (showdown2.helper.isString(name)) {
            if (typeof func !== "undefined") {
              parsers[name] = func;
            } else {
              if (parsers.hasOwnProperty(name)) {
                return parsers[name];
              } else {
                throw Error("SubParser named " + name + " not registered!");
              }
            }
          }
        };
        showdown2.extension = function(name, ext) {
          "use strict";
          if (!showdown2.helper.isString(name)) {
            throw Error("Extension 'name' must be a string");
          }
          name = showdown2.helper.stdExtName(name);
          if (showdown2.helper.isUndefined(ext)) {
            if (!extensions.hasOwnProperty(name)) {
              throw Error("Extension named " + name + " is not registered!");
            }
            return extensions[name];
          } else {
            if (typeof ext === "function") {
              ext = ext();
            }
            if (!showdown2.helper.isArray(ext)) {
              ext = [ext];
            }
            var validExtension = validate(ext, name);
            if (validExtension.valid) {
              extensions[name] = ext;
            } else {
              throw Error(validExtension.error);
            }
          }
        };
        showdown2.getAllExtensions = function() {
          "use strict";
          return extensions;
        };
        showdown2.removeExtension = function(name) {
          "use strict";
          delete extensions[name];
        };
        showdown2.resetExtensions = function() {
          "use strict";
          extensions = {};
        };
        function validate(extension, name) {
          "use strict";
          var errMsg = name ? "Error in " + name + " extension->" : "Error in unnamed extension", ret = {
            valid: true,
            error: ""
          };
          if (!showdown2.helper.isArray(extension)) {
            extension = [extension];
          }
          for (var i2 = 0; i2 < extension.length; ++i2) {
            var baseMsg = errMsg + " sub-extension " + i2 + ": ", ext = extension[i2];
            if (typeof ext !== "object") {
              ret.valid = false;
              ret.error = baseMsg + "must be an object, but " + typeof ext + " given";
              return ret;
            }
            if (!showdown2.helper.isString(ext.type)) {
              ret.valid = false;
              ret.error = baseMsg + 'property "type" must be a string, but ' + typeof ext.type + " given";
              return ret;
            }
            var type = ext.type = ext.type.toLowerCase();
            if (type === "language") {
              type = ext.type = "lang";
            }
            if (type === "html") {
              type = ext.type = "output";
            }
            if (type !== "lang" && type !== "output" && type !== "listener") {
              ret.valid = false;
              ret.error = baseMsg + "type " + type + ' is not recognized. Valid values: "lang/language", "output/html" or "listener"';
              return ret;
            }
            if (type === "listener") {
              if (showdown2.helper.isUndefined(ext.listeners)) {
                ret.valid = false;
                ret.error = baseMsg + '. Extensions of type "listener" must have a property called "listeners"';
                return ret;
              }
            } else {
              if (showdown2.helper.isUndefined(ext.filter) && showdown2.helper.isUndefined(ext.regex)) {
                ret.valid = false;
                ret.error = baseMsg + type + ' extensions must define either a "regex" property or a "filter" method';
                return ret;
              }
            }
            if (ext.listeners) {
              if (typeof ext.listeners !== "object") {
                ret.valid = false;
                ret.error = baseMsg + '"listeners" property must be an object but ' + typeof ext.listeners + " given";
                return ret;
              }
              for (var ln in ext.listeners) {
                if (ext.listeners.hasOwnProperty(ln)) {
                  if (typeof ext.listeners[ln] !== "function") {
                    ret.valid = false;
                    ret.error = baseMsg + '"listeners" property must be an hash of [event name]: [callback]. listeners.' + ln + " must be a function but " + typeof ext.listeners[ln] + " given";
                    return ret;
                  }
                }
              }
            }
            if (ext.filter) {
              if (typeof ext.filter !== "function") {
                ret.valid = false;
                ret.error = baseMsg + '"filter" must be a function, but ' + typeof ext.filter + " given";
                return ret;
              }
            } else if (ext.regex) {
              if (showdown2.helper.isString(ext.regex)) {
                ext.regex = new RegExp(ext.regex, "g");
              }
              if (!(ext.regex instanceof RegExp)) {
                ret.valid = false;
                ret.error = baseMsg + '"regex" property must either be a string or a RegExp object, but ' + typeof ext.regex + " given";
                return ret;
              }
              if (showdown2.helper.isUndefined(ext.replace)) {
                ret.valid = false;
                ret.error = baseMsg + '"regex" extensions must implement a replace string or function';
                return ret;
              }
            }
          }
          return ret;
        }
        showdown2.validateExtension = function(ext) {
          "use strict";
          var validateExtension = validate(ext, null);
          if (!validateExtension.valid) {
            console.warn(validateExtension.error);
            return false;
          }
          return true;
        };
        if (!showdown2.hasOwnProperty("helper")) {
          showdown2.helper = {};
        }
        showdown2.helper.isString = function(a2) {
          "use strict";
          return typeof a2 === "string" || a2 instanceof String;
        };
        showdown2.helper.isFunction = function(a2) {
          "use strict";
          var getType = {};
          return a2 && getType.toString.call(a2) === "[object Function]";
        };
        showdown2.helper.isArray = function(a2) {
          "use strict";
          return Array.isArray(a2);
        };
        showdown2.helper.isUndefined = function(value) {
          "use strict";
          return typeof value === "undefined";
        };
        showdown2.helper.forEach = function(obj, callback) {
          "use strict";
          if (showdown2.helper.isUndefined(obj)) {
            throw new Error("obj param is required");
          }
          if (showdown2.helper.isUndefined(callback)) {
            throw new Error("callback param is required");
          }
          if (!showdown2.helper.isFunction(callback)) {
            throw new Error("callback param must be a function/closure");
          }
          if (typeof obj.forEach === "function") {
            obj.forEach(callback);
          } else if (showdown2.helper.isArray(obj)) {
            for (var i2 = 0; i2 < obj.length; i2++) {
              callback(obj[i2], i2, obj);
            }
          } else if (typeof obj === "object") {
            for (var prop2 in obj) {
              if (obj.hasOwnProperty(prop2)) {
                callback(obj[prop2], prop2, obj);
              }
            }
          } else {
            throw new Error("obj does not seem to be an array or an iterable object");
          }
        };
        showdown2.helper.stdExtName = function(s2) {
          "use strict";
          return s2.replace(/[_?*+\/\\.^-]/g, "").replace(/\s/g, "").toLowerCase();
        };
        function escapeCharactersCallback(wholeMatch, m1) {
          "use strict";
          var charCodeToEscape = m1.charCodeAt(0);
          return "\xA8E" + charCodeToEscape + "E";
        }
        showdown2.helper.escapeCharactersCallback = escapeCharactersCallback;
        showdown2.helper.escapeCharacters = function(text2, charsToEscape, afterBackslash) {
          "use strict";
          var regexString = "([" + charsToEscape.replace(/([\[\]\\])/g, "\\$1") + "])";
          if (afterBackslash) {
            regexString = "\\\\" + regexString;
          }
          var regex = new RegExp(regexString, "g");
          text2 = text2.replace(regex, escapeCharactersCallback);
          return text2;
        };
        showdown2.helper.unescapeHTMLEntities = function(txt) {
          "use strict";
          return txt.replace(/&quot;/g, '"').replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&amp;/g, "&");
        };
        var rgxFindMatchPos = function(str, left, right, flags) {
          "use strict";
          var f2 = flags || "", g2 = f2.indexOf("g") > -1, x2 = new RegExp(left + "|" + right, "g" + f2.replace(/g/g, "")), l2 = new RegExp(left, f2.replace(/g/g, "")), pos = [], t3, s2, m2, start, end;
          do {
            t3 = 0;
            while (m2 = x2.exec(str)) {
              if (l2.test(m2[0])) {
                if (!t3++) {
                  s2 = x2.lastIndex;
                  start = s2 - m2[0].length;
                }
              } else if (t3) {
                if (!--t3) {
                  end = m2.index + m2[0].length;
                  var obj = {
                    left: { start, end: s2 },
                    match: { start: s2, end: m2.index },
                    right: { start: m2.index, end },
                    wholeMatch: { start, end }
                  };
                  pos.push(obj);
                  if (!g2) {
                    return pos;
                  }
                }
              }
            }
          } while (t3 && (x2.lastIndex = s2));
          return pos;
        };
        showdown2.helper.matchRecursiveRegExp = function(str, left, right, flags) {
          "use strict";
          var matchPos = rgxFindMatchPos(str, left, right, flags), results = [];
          for (var i2 = 0; i2 < matchPos.length; ++i2) {
            results.push([
              str.slice(matchPos[i2].wholeMatch.start, matchPos[i2].wholeMatch.end),
              str.slice(matchPos[i2].match.start, matchPos[i2].match.end),
              str.slice(matchPos[i2].left.start, matchPos[i2].left.end),
              str.slice(matchPos[i2].right.start, matchPos[i2].right.end)
            ]);
          }
          return results;
        };
        showdown2.helper.replaceRecursiveRegExp = function(str, replacement, left, right, flags) {
          "use strict";
          if (!showdown2.helper.isFunction(replacement)) {
            var repStr = replacement;
            replacement = function() {
              return repStr;
            };
          }
          var matchPos = rgxFindMatchPos(str, left, right, flags), finalStr = str, lng = matchPos.length;
          if (lng > 0) {
            var bits = [];
            if (matchPos[0].wholeMatch.start !== 0) {
              bits.push(str.slice(0, matchPos[0].wholeMatch.start));
            }
            for (var i2 = 0; i2 < lng; ++i2) {
              bits.push(
                replacement(
                  str.slice(matchPos[i2].wholeMatch.start, matchPos[i2].wholeMatch.end),
                  str.slice(matchPos[i2].match.start, matchPos[i2].match.end),
                  str.slice(matchPos[i2].left.start, matchPos[i2].left.end),
                  str.slice(matchPos[i2].right.start, matchPos[i2].right.end)
                )
              );
              if (i2 < lng - 1) {
                bits.push(str.slice(matchPos[i2].wholeMatch.end, matchPos[i2 + 1].wholeMatch.start));
              }
            }
            if (matchPos[lng - 1].wholeMatch.end < str.length) {
              bits.push(str.slice(matchPos[lng - 1].wholeMatch.end));
            }
            finalStr = bits.join("");
          }
          return finalStr;
        };
        showdown2.helper.regexIndexOf = function(str, regex, fromIndex) {
          "use strict";
          if (!showdown2.helper.isString(str)) {
            throw "InvalidArgumentError: first parameter of showdown.helper.regexIndexOf function must be a string";
          }
          if (regex instanceof RegExp === false) {
            throw "InvalidArgumentError: second parameter of showdown.helper.regexIndexOf function must be an instance of RegExp";
          }
          var indexOf = str.substring(fromIndex || 0).search(regex);
          return indexOf >= 0 ? indexOf + (fromIndex || 0) : indexOf;
        };
        showdown2.helper.splitAtIndex = function(str, index) {
          "use strict";
          if (!showdown2.helper.isString(str)) {
            throw "InvalidArgumentError: first parameter of showdown.helper.regexIndexOf function must be a string";
          }
          return [str.substring(0, index), str.substring(index)];
        };
        showdown2.helper.encodeEmailAddress = function(mail) {
          "use strict";
          var encode = [
            function(ch) {
              return "&#" + ch.charCodeAt(0) + ";";
            },
            function(ch) {
              return "&#x" + ch.charCodeAt(0).toString(16) + ";";
            },
            function(ch) {
              return ch;
            }
          ];
          mail = mail.replace(/./g, function(ch) {
            if (ch === "@") {
              ch = encode[Math.floor(Math.random() * 2)](ch);
            } else {
              var r2 = Math.random();
              ch = r2 > 0.9 ? encode[2](ch) : r2 > 0.45 ? encode[1](ch) : encode[0](ch);
            }
            return ch;
          });
          return mail;
        };
        showdown2.helper.padEnd = function padEnd(str, targetLength, padString) {
          "use strict";
          targetLength = targetLength >> 0;
          padString = String(padString || " ");
          if (str.length > targetLength) {
            return String(str);
          } else {
            targetLength = targetLength - str.length;
            if (targetLength > padString.length) {
              padString += padString.repeat(targetLength / padString.length);
            }
            return String(str) + padString.slice(0, targetLength);
          }
        };
        if (typeof console === "undefined") {
          console = {
            warn: function(msg) {
              "use strict";
              alert(msg);
            },
            log: function(msg) {
              "use strict";
              alert(msg);
            },
            error: function(msg) {
              "use strict";
              throw msg;
            }
          };
        }
        showdown2.helper.regexes = {
          asteriskDashAndColon: /([*_:~])/g
        };
        showdown2.helper.emojis = {
          "+1": "\u{1F44D}",
          "-1": "\u{1F44E}",
          "100": "\u{1F4AF}",
          "1234": "\u{1F522}",
          "1st_place_medal": "\u{1F947}",
          "2nd_place_medal": "\u{1F948}",
          "3rd_place_medal": "\u{1F949}",
          "8ball": "\u{1F3B1}",
          "a": "\u{1F170}\uFE0F",
          "ab": "\u{1F18E}",
          "abc": "\u{1F524}",
          "abcd": "\u{1F521}",
          "accept": "\u{1F251}",
          "aerial_tramway": "\u{1F6A1}",
          "airplane": "\u2708\uFE0F",
          "alarm_clock": "\u23F0",
          "alembic": "\u2697\uFE0F",
          "alien": "\u{1F47D}",
          "ambulance": "\u{1F691}",
          "amphora": "\u{1F3FA}",
          "anchor": "\u2693\uFE0F",
          "angel": "\u{1F47C}",
          "anger": "\u{1F4A2}",
          "angry": "\u{1F620}",
          "anguished": "\u{1F627}",
          "ant": "\u{1F41C}",
          "apple": "\u{1F34E}",
          "aquarius": "\u2652\uFE0F",
          "aries": "\u2648\uFE0F",
          "arrow_backward": "\u25C0\uFE0F",
          "arrow_double_down": "\u23EC",
          "arrow_double_up": "\u23EB",
          "arrow_down": "\u2B07\uFE0F",
          "arrow_down_small": "\u{1F53D}",
          "arrow_forward": "\u25B6\uFE0F",
          "arrow_heading_down": "\u2935\uFE0F",
          "arrow_heading_up": "\u2934\uFE0F",
          "arrow_left": "\u2B05\uFE0F",
          "arrow_lower_left": "\u2199\uFE0F",
          "arrow_lower_right": "\u2198\uFE0F",
          "arrow_right": "\u27A1\uFE0F",
          "arrow_right_hook": "\u21AA\uFE0F",
          "arrow_up": "\u2B06\uFE0F",
          "arrow_up_down": "\u2195\uFE0F",
          "arrow_up_small": "\u{1F53C}",
          "arrow_upper_left": "\u2196\uFE0F",
          "arrow_upper_right": "\u2197\uFE0F",
          "arrows_clockwise": "\u{1F503}",
          "arrows_counterclockwise": "\u{1F504}",
          "art": "\u{1F3A8}",
          "articulated_lorry": "\u{1F69B}",
          "artificial_satellite": "\u{1F6F0}",
          "astonished": "\u{1F632}",
          "athletic_shoe": "\u{1F45F}",
          "atm": "\u{1F3E7}",
          "atom_symbol": "\u269B\uFE0F",
          "avocado": "\u{1F951}",
          "b": "\u{1F171}\uFE0F",
          "baby": "\u{1F476}",
          "baby_bottle": "\u{1F37C}",
          "baby_chick": "\u{1F424}",
          "baby_symbol": "\u{1F6BC}",
          "back": "\u{1F519}",
          "bacon": "\u{1F953}",
          "badminton": "\u{1F3F8}",
          "baggage_claim": "\u{1F6C4}",
          "baguette_bread": "\u{1F956}",
          "balance_scale": "\u2696\uFE0F",
          "balloon": "\u{1F388}",
          "ballot_box": "\u{1F5F3}",
          "ballot_box_with_check": "\u2611\uFE0F",
          "bamboo": "\u{1F38D}",
          "banana": "\u{1F34C}",
          "bangbang": "\u203C\uFE0F",
          "bank": "\u{1F3E6}",
          "bar_chart": "\u{1F4CA}",
          "barber": "\u{1F488}",
          "baseball": "\u26BE\uFE0F",
          "basketball": "\u{1F3C0}",
          "basketball_man": "\u26F9\uFE0F",
          "basketball_woman": "\u26F9\uFE0F&zwj;\u2640\uFE0F",
          "bat": "\u{1F987}",
          "bath": "\u{1F6C0}",
          "bathtub": "\u{1F6C1}",
          "battery": "\u{1F50B}",
          "beach_umbrella": "\u{1F3D6}",
          "bear": "\u{1F43B}",
          "bed": "\u{1F6CF}",
          "bee": "\u{1F41D}",
          "beer": "\u{1F37A}",
          "beers": "\u{1F37B}",
          "beetle": "\u{1F41E}",
          "beginner": "\u{1F530}",
          "bell": "\u{1F514}",
          "bellhop_bell": "\u{1F6CE}",
          "bento": "\u{1F371}",
          "biking_man": "\u{1F6B4}",
          "bike": "\u{1F6B2}",
          "biking_woman": "\u{1F6B4}&zwj;\u2640\uFE0F",
          "bikini": "\u{1F459}",
          "biohazard": "\u2623\uFE0F",
          "bird": "\u{1F426}",
          "birthday": "\u{1F382}",
          "black_circle": "\u26AB\uFE0F",
          "black_flag": "\u{1F3F4}",
          "black_heart": "\u{1F5A4}",
          "black_joker": "\u{1F0CF}",
          "black_large_square": "\u2B1B\uFE0F",
          "black_medium_small_square": "\u25FE\uFE0F",
          "black_medium_square": "\u25FC\uFE0F",
          "black_nib": "\u2712\uFE0F",
          "black_small_square": "\u25AA\uFE0F",
          "black_square_button": "\u{1F532}",
          "blonde_man": "\u{1F471}",
          "blonde_woman": "\u{1F471}&zwj;\u2640\uFE0F",
          "blossom": "\u{1F33C}",
          "blowfish": "\u{1F421}",
          "blue_book": "\u{1F4D8}",
          "blue_car": "\u{1F699}",
          "blue_heart": "\u{1F499}",
          "blush": "\u{1F60A}",
          "boar": "\u{1F417}",
          "boat": "\u26F5\uFE0F",
          "bomb": "\u{1F4A3}",
          "book": "\u{1F4D6}",
          "bookmark": "\u{1F516}",
          "bookmark_tabs": "\u{1F4D1}",
          "books": "\u{1F4DA}",
          "boom": "\u{1F4A5}",
          "boot": "\u{1F462}",
          "bouquet": "\u{1F490}",
          "bowing_man": "\u{1F647}",
          "bow_and_arrow": "\u{1F3F9}",
          "bowing_woman": "\u{1F647}&zwj;\u2640\uFE0F",
          "bowling": "\u{1F3B3}",
          "boxing_glove": "\u{1F94A}",
          "boy": "\u{1F466}",
          "bread": "\u{1F35E}",
          "bride_with_veil": "\u{1F470}",
          "bridge_at_night": "\u{1F309}",
          "briefcase": "\u{1F4BC}",
          "broken_heart": "\u{1F494}",
          "bug": "\u{1F41B}",
          "building_construction": "\u{1F3D7}",
          "bulb": "\u{1F4A1}",
          "bullettrain_front": "\u{1F685}",
          "bullettrain_side": "\u{1F684}",
          "burrito": "\u{1F32F}",
          "bus": "\u{1F68C}",
          "business_suit_levitating": "\u{1F574}",
          "busstop": "\u{1F68F}",
          "bust_in_silhouette": "\u{1F464}",
          "busts_in_silhouette": "\u{1F465}",
          "butterfly": "\u{1F98B}",
          "cactus": "\u{1F335}",
          "cake": "\u{1F370}",
          "calendar": "\u{1F4C6}",
          "call_me_hand": "\u{1F919}",
          "calling": "\u{1F4F2}",
          "camel": "\u{1F42B}",
          "camera": "\u{1F4F7}",
          "camera_flash": "\u{1F4F8}",
          "camping": "\u{1F3D5}",
          "cancer": "\u264B\uFE0F",
          "candle": "\u{1F56F}",
          "candy": "\u{1F36C}",
          "canoe": "\u{1F6F6}",
          "capital_abcd": "\u{1F520}",
          "capricorn": "\u2651\uFE0F",
          "car": "\u{1F697}",
          "card_file_box": "\u{1F5C3}",
          "card_index": "\u{1F4C7}",
          "card_index_dividers": "\u{1F5C2}",
          "carousel_horse": "\u{1F3A0}",
          "carrot": "\u{1F955}",
          "cat": "\u{1F431}",
          "cat2": "\u{1F408}",
          "cd": "\u{1F4BF}",
          "chains": "\u26D3",
          "champagne": "\u{1F37E}",
          "chart": "\u{1F4B9}",
          "chart_with_downwards_trend": "\u{1F4C9}",
          "chart_with_upwards_trend": "\u{1F4C8}",
          "checkered_flag": "\u{1F3C1}",
          "cheese": "\u{1F9C0}",
          "cherries": "\u{1F352}",
          "cherry_blossom": "\u{1F338}",
          "chestnut": "\u{1F330}",
          "chicken": "\u{1F414}",
          "children_crossing": "\u{1F6B8}",
          "chipmunk": "\u{1F43F}",
          "chocolate_bar": "\u{1F36B}",
          "christmas_tree": "\u{1F384}",
          "church": "\u26EA\uFE0F",
          "cinema": "\u{1F3A6}",
          "circus_tent": "\u{1F3AA}",
          "city_sunrise": "\u{1F307}",
          "city_sunset": "\u{1F306}",
          "cityscape": "\u{1F3D9}",
          "cl": "\u{1F191}",
          "clamp": "\u{1F5DC}",
          "clap": "\u{1F44F}",
          "clapper": "\u{1F3AC}",
          "classical_building": "\u{1F3DB}",
          "clinking_glasses": "\u{1F942}",
          "clipboard": "\u{1F4CB}",
          "clock1": "\u{1F550}",
          "clock10": "\u{1F559}",
          "clock1030": "\u{1F565}",
          "clock11": "\u{1F55A}",
          "clock1130": "\u{1F566}",
          "clock12": "\u{1F55B}",
          "clock1230": "\u{1F567}",
          "clock130": "\u{1F55C}",
          "clock2": "\u{1F551}",
          "clock230": "\u{1F55D}",
          "clock3": "\u{1F552}",
          "clock330": "\u{1F55E}",
          "clock4": "\u{1F553}",
          "clock430": "\u{1F55F}",
          "clock5": "\u{1F554}",
          "clock530": "\u{1F560}",
          "clock6": "\u{1F555}",
          "clock630": "\u{1F561}",
          "clock7": "\u{1F556}",
          "clock730": "\u{1F562}",
          "clock8": "\u{1F557}",
          "clock830": "\u{1F563}",
          "clock9": "\u{1F558}",
          "clock930": "\u{1F564}",
          "closed_book": "\u{1F4D5}",
          "closed_lock_with_key": "\u{1F510}",
          "closed_umbrella": "\u{1F302}",
          "cloud": "\u2601\uFE0F",
          "cloud_with_lightning": "\u{1F329}",
          "cloud_with_lightning_and_rain": "\u26C8",
          "cloud_with_rain": "\u{1F327}",
          "cloud_with_snow": "\u{1F328}",
          "clown_face": "\u{1F921}",
          "clubs": "\u2663\uFE0F",
          "cocktail": "\u{1F378}",
          "coffee": "\u2615\uFE0F",
          "coffin": "\u26B0\uFE0F",
          "cold_sweat": "\u{1F630}",
          "comet": "\u2604\uFE0F",
          "computer": "\u{1F4BB}",
          "computer_mouse": "\u{1F5B1}",
          "confetti_ball": "\u{1F38A}",
          "confounded": "\u{1F616}",
          "confused": "\u{1F615}",
          "congratulations": "\u3297\uFE0F",
          "construction": "\u{1F6A7}",
          "construction_worker_man": "\u{1F477}",
          "construction_worker_woman": "\u{1F477}&zwj;\u2640\uFE0F",
          "control_knobs": "\u{1F39B}",
          "convenience_store": "\u{1F3EA}",
          "cookie": "\u{1F36A}",
          "cool": "\u{1F192}",
          "policeman": "\u{1F46E}",
          "copyright": "\xA9\uFE0F",
          "corn": "\u{1F33D}",
          "couch_and_lamp": "\u{1F6CB}",
          "couple": "\u{1F46B}",
          "couple_with_heart_woman_man": "\u{1F491}",
          "couple_with_heart_man_man": "\u{1F468}&zwj;\u2764\uFE0F&zwj;\u{1F468}",
          "couple_with_heart_woman_woman": "\u{1F469}&zwj;\u2764\uFE0F&zwj;\u{1F469}",
          "couplekiss_man_man": "\u{1F468}&zwj;\u2764\uFE0F&zwj;\u{1F48B}&zwj;\u{1F468}",
          "couplekiss_man_woman": "\u{1F48F}",
          "couplekiss_woman_woman": "\u{1F469}&zwj;\u2764\uFE0F&zwj;\u{1F48B}&zwj;\u{1F469}",
          "cow": "\u{1F42E}",
          "cow2": "\u{1F404}",
          "cowboy_hat_face": "\u{1F920}",
          "crab": "\u{1F980}",
          "crayon": "\u{1F58D}",
          "credit_card": "\u{1F4B3}",
          "crescent_moon": "\u{1F319}",
          "cricket": "\u{1F3CF}",
          "crocodile": "\u{1F40A}",
          "croissant": "\u{1F950}",
          "crossed_fingers": "\u{1F91E}",
          "crossed_flags": "\u{1F38C}",
          "crossed_swords": "\u2694\uFE0F",
          "crown": "\u{1F451}",
          "cry": "\u{1F622}",
          "crying_cat_face": "\u{1F63F}",
          "crystal_ball": "\u{1F52E}",
          "cucumber": "\u{1F952}",
          "cupid": "\u{1F498}",
          "curly_loop": "\u27B0",
          "currency_exchange": "\u{1F4B1}",
          "curry": "\u{1F35B}",
          "custard": "\u{1F36E}",
          "customs": "\u{1F6C3}",
          "cyclone": "\u{1F300}",
          "dagger": "\u{1F5E1}",
          "dancer": "\u{1F483}",
          "dancing_women": "\u{1F46F}",
          "dancing_men": "\u{1F46F}&zwj;\u2642\uFE0F",
          "dango": "\u{1F361}",
          "dark_sunglasses": "\u{1F576}",
          "dart": "\u{1F3AF}",
          "dash": "\u{1F4A8}",
          "date": "\u{1F4C5}",
          "deciduous_tree": "\u{1F333}",
          "deer": "\u{1F98C}",
          "department_store": "\u{1F3EC}",
          "derelict_house": "\u{1F3DA}",
          "desert": "\u{1F3DC}",
          "desert_island": "\u{1F3DD}",
          "desktop_computer": "\u{1F5A5}",
          "male_detective": "\u{1F575}\uFE0F",
          "diamond_shape_with_a_dot_inside": "\u{1F4A0}",
          "diamonds": "\u2666\uFE0F",
          "disappointed": "\u{1F61E}",
          "disappointed_relieved": "\u{1F625}",
          "dizzy": "\u{1F4AB}",
          "dizzy_face": "\u{1F635}",
          "do_not_litter": "\u{1F6AF}",
          "dog": "\u{1F436}",
          "dog2": "\u{1F415}",
          "dollar": "\u{1F4B5}",
          "dolls": "\u{1F38E}",
          "dolphin": "\u{1F42C}",
          "door": "\u{1F6AA}",
          "doughnut": "\u{1F369}",
          "dove": "\u{1F54A}",
          "dragon": "\u{1F409}",
          "dragon_face": "\u{1F432}",
          "dress": "\u{1F457}",
          "dromedary_camel": "\u{1F42A}",
          "drooling_face": "\u{1F924}",
          "droplet": "\u{1F4A7}",
          "drum": "\u{1F941}",
          "duck": "\u{1F986}",
          "dvd": "\u{1F4C0}",
          "e-mail": "\u{1F4E7}",
          "eagle": "\u{1F985}",
          "ear": "\u{1F442}",
          "ear_of_rice": "\u{1F33E}",
          "earth_africa": "\u{1F30D}",
          "earth_americas": "\u{1F30E}",
          "earth_asia": "\u{1F30F}",
          "egg": "\u{1F95A}",
          "eggplant": "\u{1F346}",
          "eight_pointed_black_star": "\u2734\uFE0F",
          "eight_spoked_asterisk": "\u2733\uFE0F",
          "electric_plug": "\u{1F50C}",
          "elephant": "\u{1F418}",
          "email": "\u2709\uFE0F",
          "end": "\u{1F51A}",
          "envelope_with_arrow": "\u{1F4E9}",
          "euro": "\u{1F4B6}",
          "european_castle": "\u{1F3F0}",
          "european_post_office": "\u{1F3E4}",
          "evergreen_tree": "\u{1F332}",
          "exclamation": "\u2757\uFE0F",
          "expressionless": "\u{1F611}",
          "eye": "\u{1F441}",
          "eye_speech_bubble": "\u{1F441}&zwj;\u{1F5E8}",
          "eyeglasses": "\u{1F453}",
          "eyes": "\u{1F440}",
          "face_with_head_bandage": "\u{1F915}",
          "face_with_thermometer": "\u{1F912}",
          "fist_oncoming": "\u{1F44A}",
          "factory": "\u{1F3ED}",
          "fallen_leaf": "\u{1F342}",
          "family_man_woman_boy": "\u{1F46A}",
          "family_man_boy": "\u{1F468}&zwj;\u{1F466}",
          "family_man_boy_boy": "\u{1F468}&zwj;\u{1F466}&zwj;\u{1F466}",
          "family_man_girl": "\u{1F468}&zwj;\u{1F467}",
          "family_man_girl_boy": "\u{1F468}&zwj;\u{1F467}&zwj;\u{1F466}",
          "family_man_girl_girl": "\u{1F468}&zwj;\u{1F467}&zwj;\u{1F467}",
          "family_man_man_boy": "\u{1F468}&zwj;\u{1F468}&zwj;\u{1F466}",
          "family_man_man_boy_boy": "\u{1F468}&zwj;\u{1F468}&zwj;\u{1F466}&zwj;\u{1F466}",
          "family_man_man_girl": "\u{1F468}&zwj;\u{1F468}&zwj;\u{1F467}",
          "family_man_man_girl_boy": "\u{1F468}&zwj;\u{1F468}&zwj;\u{1F467}&zwj;\u{1F466}",
          "family_man_man_girl_girl": "\u{1F468}&zwj;\u{1F468}&zwj;\u{1F467}&zwj;\u{1F467}",
          "family_man_woman_boy_boy": "\u{1F468}&zwj;\u{1F469}&zwj;\u{1F466}&zwj;\u{1F466}",
          "family_man_woman_girl": "\u{1F468}&zwj;\u{1F469}&zwj;\u{1F467}",
          "family_man_woman_girl_boy": "\u{1F468}&zwj;\u{1F469}&zwj;\u{1F467}&zwj;\u{1F466}",
          "family_man_woman_girl_girl": "\u{1F468}&zwj;\u{1F469}&zwj;\u{1F467}&zwj;\u{1F467}",
          "family_woman_boy": "\u{1F469}&zwj;\u{1F466}",
          "family_woman_boy_boy": "\u{1F469}&zwj;\u{1F466}&zwj;\u{1F466}",
          "family_woman_girl": "\u{1F469}&zwj;\u{1F467}",
          "family_woman_girl_boy": "\u{1F469}&zwj;\u{1F467}&zwj;\u{1F466}",
          "family_woman_girl_girl": "\u{1F469}&zwj;\u{1F467}&zwj;\u{1F467}",
          "family_woman_woman_boy": "\u{1F469}&zwj;\u{1F469}&zwj;\u{1F466}",
          "family_woman_woman_boy_boy": "\u{1F469}&zwj;\u{1F469}&zwj;\u{1F466}&zwj;\u{1F466}",
          "family_woman_woman_girl": "\u{1F469}&zwj;\u{1F469}&zwj;\u{1F467}",
          "family_woman_woman_girl_boy": "\u{1F469}&zwj;\u{1F469}&zwj;\u{1F467}&zwj;\u{1F466}",
          "family_woman_woman_girl_girl": "\u{1F469}&zwj;\u{1F469}&zwj;\u{1F467}&zwj;\u{1F467}",
          "fast_forward": "\u23E9",
          "fax": "\u{1F4E0}",
          "fearful": "\u{1F628}",
          "feet": "\u{1F43E}",
          "female_detective": "\u{1F575}\uFE0F&zwj;\u2640\uFE0F",
          "ferris_wheel": "\u{1F3A1}",
          "ferry": "\u26F4",
          "field_hockey": "\u{1F3D1}",
          "file_cabinet": "\u{1F5C4}",
          "file_folder": "\u{1F4C1}",
          "film_projector": "\u{1F4FD}",
          "film_strip": "\u{1F39E}",
          "fire": "\u{1F525}",
          "fire_engine": "\u{1F692}",
          "fireworks": "\u{1F386}",
          "first_quarter_moon": "\u{1F313}",
          "first_quarter_moon_with_face": "\u{1F31B}",
          "fish": "\u{1F41F}",
          "fish_cake": "\u{1F365}",
          "fishing_pole_and_fish": "\u{1F3A3}",
          "fist_raised": "\u270A",
          "fist_left": "\u{1F91B}",
          "fist_right": "\u{1F91C}",
          "flags": "\u{1F38F}",
          "flashlight": "\u{1F526}",
          "fleur_de_lis": "\u269C\uFE0F",
          "flight_arrival": "\u{1F6EC}",
          "flight_departure": "\u{1F6EB}",
          "floppy_disk": "\u{1F4BE}",
          "flower_playing_cards": "\u{1F3B4}",
          "flushed": "\u{1F633}",
          "fog": "\u{1F32B}",
          "foggy": "\u{1F301}",
          "football": "\u{1F3C8}",
          "footprints": "\u{1F463}",
          "fork_and_knife": "\u{1F374}",
          "fountain": "\u26F2\uFE0F",
          "fountain_pen": "\u{1F58B}",
          "four_leaf_clover": "\u{1F340}",
          "fox_face": "\u{1F98A}",
          "framed_picture": "\u{1F5BC}",
          "free": "\u{1F193}",
          "fried_egg": "\u{1F373}",
          "fried_shrimp": "\u{1F364}",
          "fries": "\u{1F35F}",
          "frog": "\u{1F438}",
          "frowning": "\u{1F626}",
          "frowning_face": "\u2639\uFE0F",
          "frowning_man": "\u{1F64D}&zwj;\u2642\uFE0F",
          "frowning_woman": "\u{1F64D}",
          "middle_finger": "\u{1F595}",
          "fuelpump": "\u26FD\uFE0F",
          "full_moon": "\u{1F315}",
          "full_moon_with_face": "\u{1F31D}",
          "funeral_urn": "\u26B1\uFE0F",
          "game_die": "\u{1F3B2}",
          "gear": "\u2699\uFE0F",
          "gem": "\u{1F48E}",
          "gemini": "\u264A\uFE0F",
          "ghost": "\u{1F47B}",
          "gift": "\u{1F381}",
          "gift_heart": "\u{1F49D}",
          "girl": "\u{1F467}",
          "globe_with_meridians": "\u{1F310}",
          "goal_net": "\u{1F945}",
          "goat": "\u{1F410}",
          "golf": "\u26F3\uFE0F",
          "golfing_man": "\u{1F3CC}\uFE0F",
          "golfing_woman": "\u{1F3CC}\uFE0F&zwj;\u2640\uFE0F",
          "gorilla": "\u{1F98D}",
          "grapes": "\u{1F347}",
          "green_apple": "\u{1F34F}",
          "green_book": "\u{1F4D7}",
          "green_heart": "\u{1F49A}",
          "green_salad": "\u{1F957}",
          "grey_exclamation": "\u2755",
          "grey_question": "\u2754",
          "grimacing": "\u{1F62C}",
          "grin": "\u{1F601}",
          "grinning": "\u{1F600}",
          "guardsman": "\u{1F482}",
          "guardswoman": "\u{1F482}&zwj;\u2640\uFE0F",
          "guitar": "\u{1F3B8}",
          "gun": "\u{1F52B}",
          "haircut_woman": "\u{1F487}",
          "haircut_man": "\u{1F487}&zwj;\u2642\uFE0F",
          "hamburger": "\u{1F354}",
          "hammer": "\u{1F528}",
          "hammer_and_pick": "\u2692",
          "hammer_and_wrench": "\u{1F6E0}",
          "hamster": "\u{1F439}",
          "hand": "\u270B",
          "handbag": "\u{1F45C}",
          "handshake": "\u{1F91D}",
          "hankey": "\u{1F4A9}",
          "hatched_chick": "\u{1F425}",
          "hatching_chick": "\u{1F423}",
          "headphones": "\u{1F3A7}",
          "hear_no_evil": "\u{1F649}",
          "heart": "\u2764\uFE0F",
          "heart_decoration": "\u{1F49F}",
          "heart_eyes": "\u{1F60D}",
          "heart_eyes_cat": "\u{1F63B}",
          "heartbeat": "\u{1F493}",
          "heartpulse": "\u{1F497}",
          "hearts": "\u2665\uFE0F",
          "heavy_check_mark": "\u2714\uFE0F",
          "heavy_division_sign": "\u2797",
          "heavy_dollar_sign": "\u{1F4B2}",
          "heavy_heart_exclamation": "\u2763\uFE0F",
          "heavy_minus_sign": "\u2796",
          "heavy_multiplication_x": "\u2716\uFE0F",
          "heavy_plus_sign": "\u2795",
          "helicopter": "\u{1F681}",
          "herb": "\u{1F33F}",
          "hibiscus": "\u{1F33A}",
          "high_brightness": "\u{1F506}",
          "high_heel": "\u{1F460}",
          "hocho": "\u{1F52A}",
          "hole": "\u{1F573}",
          "honey_pot": "\u{1F36F}",
          "horse": "\u{1F434}",
          "horse_racing": "\u{1F3C7}",
          "hospital": "\u{1F3E5}",
          "hot_pepper": "\u{1F336}",
          "hotdog": "\u{1F32D}",
          "hotel": "\u{1F3E8}",
          "hotsprings": "\u2668\uFE0F",
          "hourglass": "\u231B\uFE0F",
          "hourglass_flowing_sand": "\u23F3",
          "house": "\u{1F3E0}",
          "house_with_garden": "\u{1F3E1}",
          "houses": "\u{1F3D8}",
          "hugs": "\u{1F917}",
          "hushed": "\u{1F62F}",
          "ice_cream": "\u{1F368}",
          "ice_hockey": "\u{1F3D2}",
          "ice_skate": "\u26F8",
          "icecream": "\u{1F366}",
          "id": "\u{1F194}",
          "ideograph_advantage": "\u{1F250}",
          "imp": "\u{1F47F}",
          "inbox_tray": "\u{1F4E5}",
          "incoming_envelope": "\u{1F4E8}",
          "tipping_hand_woman": "\u{1F481}",
          "information_source": "\u2139\uFE0F",
          "innocent": "\u{1F607}",
          "interrobang": "\u2049\uFE0F",
          "iphone": "\u{1F4F1}",
          "izakaya_lantern": "\u{1F3EE}",
          "jack_o_lantern": "\u{1F383}",
          "japan": "\u{1F5FE}",
          "japanese_castle": "\u{1F3EF}",
          "japanese_goblin": "\u{1F47A}",
          "japanese_ogre": "\u{1F479}",
          "jeans": "\u{1F456}",
          "joy": "\u{1F602}",
          "joy_cat": "\u{1F639}",
          "joystick": "\u{1F579}",
          "kaaba": "\u{1F54B}",
          "key": "\u{1F511}",
          "keyboard": "\u2328\uFE0F",
          "keycap_ten": "\u{1F51F}",
          "kick_scooter": "\u{1F6F4}",
          "kimono": "\u{1F458}",
          "kiss": "\u{1F48B}",
          "kissing": "\u{1F617}",
          "kissing_cat": "\u{1F63D}",
          "kissing_closed_eyes": "\u{1F61A}",
          "kissing_heart": "\u{1F618}",
          "kissing_smiling_eyes": "\u{1F619}",
          "kiwi_fruit": "\u{1F95D}",
          "koala": "\u{1F428}",
          "koko": "\u{1F201}",
          "label": "\u{1F3F7}",
          "large_blue_circle": "\u{1F535}",
          "large_blue_diamond": "\u{1F537}",
          "large_orange_diamond": "\u{1F536}",
          "last_quarter_moon": "\u{1F317}",
          "last_quarter_moon_with_face": "\u{1F31C}",
          "latin_cross": "\u271D\uFE0F",
          "laughing": "\u{1F606}",
          "leaves": "\u{1F343}",
          "ledger": "\u{1F4D2}",
          "left_luggage": "\u{1F6C5}",
          "left_right_arrow": "\u2194\uFE0F",
          "leftwards_arrow_with_hook": "\u21A9\uFE0F",
          "lemon": "\u{1F34B}",
          "leo": "\u264C\uFE0F",
          "leopard": "\u{1F406}",
          "level_slider": "\u{1F39A}",
          "libra": "\u264E\uFE0F",
          "light_rail": "\u{1F688}",
          "link": "\u{1F517}",
          "lion": "\u{1F981}",
          "lips": "\u{1F444}",
          "lipstick": "\u{1F484}",
          "lizard": "\u{1F98E}",
          "lock": "\u{1F512}",
          "lock_with_ink_pen": "\u{1F50F}",
          "lollipop": "\u{1F36D}",
          "loop": "\u27BF",
          "loud_sound": "\u{1F50A}",
          "loudspeaker": "\u{1F4E2}",
          "love_hotel": "\u{1F3E9}",
          "love_letter": "\u{1F48C}",
          "low_brightness": "\u{1F505}",
          "lying_face": "\u{1F925}",
          "m": "\u24C2\uFE0F",
          "mag": "\u{1F50D}",
          "mag_right": "\u{1F50E}",
          "mahjong": "\u{1F004}\uFE0F",
          "mailbox": "\u{1F4EB}",
          "mailbox_closed": "\u{1F4EA}",
          "mailbox_with_mail": "\u{1F4EC}",
          "mailbox_with_no_mail": "\u{1F4ED}",
          "man": "\u{1F468}",
          "man_artist": "\u{1F468}&zwj;\u{1F3A8}",
          "man_astronaut": "\u{1F468}&zwj;\u{1F680}",
          "man_cartwheeling": "\u{1F938}&zwj;\u2642\uFE0F",
          "man_cook": "\u{1F468}&zwj;\u{1F373}",
          "man_dancing": "\u{1F57A}",
          "man_facepalming": "\u{1F926}&zwj;\u2642\uFE0F",
          "man_factory_worker": "\u{1F468}&zwj;\u{1F3ED}",
          "man_farmer": "\u{1F468}&zwj;\u{1F33E}",
          "man_firefighter": "\u{1F468}&zwj;\u{1F692}",
          "man_health_worker": "\u{1F468}&zwj;\u2695\uFE0F",
          "man_in_tuxedo": "\u{1F935}",
          "man_judge": "\u{1F468}&zwj;\u2696\uFE0F",
          "man_juggling": "\u{1F939}&zwj;\u2642\uFE0F",
          "man_mechanic": "\u{1F468}&zwj;\u{1F527}",
          "man_office_worker": "\u{1F468}&zwj;\u{1F4BC}",
          "man_pilot": "\u{1F468}&zwj;\u2708\uFE0F",
          "man_playing_handball": "\u{1F93E}&zwj;\u2642\uFE0F",
          "man_playing_water_polo": "\u{1F93D}&zwj;\u2642\uFE0F",
          "man_scientist": "\u{1F468}&zwj;\u{1F52C}",
          "man_shrugging": "\u{1F937}&zwj;\u2642\uFE0F",
          "man_singer": "\u{1F468}&zwj;\u{1F3A4}",
          "man_student": "\u{1F468}&zwj;\u{1F393}",
          "man_teacher": "\u{1F468}&zwj;\u{1F3EB}",
          "man_technologist": "\u{1F468}&zwj;\u{1F4BB}",
          "man_with_gua_pi_mao": "\u{1F472}",
          "man_with_turban": "\u{1F473}",
          "tangerine": "\u{1F34A}",
          "mans_shoe": "\u{1F45E}",
          "mantelpiece_clock": "\u{1F570}",
          "maple_leaf": "\u{1F341}",
          "martial_arts_uniform": "\u{1F94B}",
          "mask": "\u{1F637}",
          "massage_woman": "\u{1F486}",
          "massage_man": "\u{1F486}&zwj;\u2642\uFE0F",
          "meat_on_bone": "\u{1F356}",
          "medal_military": "\u{1F396}",
          "medal_sports": "\u{1F3C5}",
          "mega": "\u{1F4E3}",
          "melon": "\u{1F348}",
          "memo": "\u{1F4DD}",
          "men_wrestling": "\u{1F93C}&zwj;\u2642\uFE0F",
          "menorah": "\u{1F54E}",
          "mens": "\u{1F6B9}",
          "metal": "\u{1F918}",
          "metro": "\u{1F687}",
          "microphone": "\u{1F3A4}",
          "microscope": "\u{1F52C}",
          "milk_glass": "\u{1F95B}",
          "milky_way": "\u{1F30C}",
          "minibus": "\u{1F690}",
          "minidisc": "\u{1F4BD}",
          "mobile_phone_off": "\u{1F4F4}",
          "money_mouth_face": "\u{1F911}",
          "money_with_wings": "\u{1F4B8}",
          "moneybag": "\u{1F4B0}",
          "monkey": "\u{1F412}",
          "monkey_face": "\u{1F435}",
          "monorail": "\u{1F69D}",
          "moon": "\u{1F314}",
          "mortar_board": "\u{1F393}",
          "mosque": "\u{1F54C}",
          "motor_boat": "\u{1F6E5}",
          "motor_scooter": "\u{1F6F5}",
          "motorcycle": "\u{1F3CD}",
          "motorway": "\u{1F6E3}",
          "mount_fuji": "\u{1F5FB}",
          "mountain": "\u26F0",
          "mountain_biking_man": "\u{1F6B5}",
          "mountain_biking_woman": "\u{1F6B5}&zwj;\u2640\uFE0F",
          "mountain_cableway": "\u{1F6A0}",
          "mountain_railway": "\u{1F69E}",
          "mountain_snow": "\u{1F3D4}",
          "mouse": "\u{1F42D}",
          "mouse2": "\u{1F401}",
          "movie_camera": "\u{1F3A5}",
          "moyai": "\u{1F5FF}",
          "mrs_claus": "\u{1F936}",
          "muscle": "\u{1F4AA}",
          "mushroom": "\u{1F344}",
          "musical_keyboard": "\u{1F3B9}",
          "musical_note": "\u{1F3B5}",
          "musical_score": "\u{1F3BC}",
          "mute": "\u{1F507}",
          "nail_care": "\u{1F485}",
          "name_badge": "\u{1F4DB}",
          "national_park": "\u{1F3DE}",
          "nauseated_face": "\u{1F922}",
          "necktie": "\u{1F454}",
          "negative_squared_cross_mark": "\u274E",
          "nerd_face": "\u{1F913}",
          "neutral_face": "\u{1F610}",
          "new": "\u{1F195}",
          "new_moon": "\u{1F311}",
          "new_moon_with_face": "\u{1F31A}",
          "newspaper": "\u{1F4F0}",
          "newspaper_roll": "\u{1F5DE}",
          "next_track_button": "\u23ED",
          "ng": "\u{1F196}",
          "no_good_man": "\u{1F645}&zwj;\u2642\uFE0F",
          "no_good_woman": "\u{1F645}",
          "night_with_stars": "\u{1F303}",
          "no_bell": "\u{1F515}",
          "no_bicycles": "\u{1F6B3}",
          "no_entry": "\u26D4\uFE0F",
          "no_entry_sign": "\u{1F6AB}",
          "no_mobile_phones": "\u{1F4F5}",
          "no_mouth": "\u{1F636}",
          "no_pedestrians": "\u{1F6B7}",
          "no_smoking": "\u{1F6AD}",
          "non-potable_water": "\u{1F6B1}",
          "nose": "\u{1F443}",
          "notebook": "\u{1F4D3}",
          "notebook_with_decorative_cover": "\u{1F4D4}",
          "notes": "\u{1F3B6}",
          "nut_and_bolt": "\u{1F529}",
          "o": "\u2B55\uFE0F",
          "o2": "\u{1F17E}\uFE0F",
          "ocean": "\u{1F30A}",
          "octopus": "\u{1F419}",
          "oden": "\u{1F362}",
          "office": "\u{1F3E2}",
          "oil_drum": "\u{1F6E2}",
          "ok": "\u{1F197}",
          "ok_hand": "\u{1F44C}",
          "ok_man": "\u{1F646}&zwj;\u2642\uFE0F",
          "ok_woman": "\u{1F646}",
          "old_key": "\u{1F5DD}",
          "older_man": "\u{1F474}",
          "older_woman": "\u{1F475}",
          "om": "\u{1F549}",
          "on": "\u{1F51B}",
          "oncoming_automobile": "\u{1F698}",
          "oncoming_bus": "\u{1F68D}",
          "oncoming_police_car": "\u{1F694}",
          "oncoming_taxi": "\u{1F696}",
          "open_file_folder": "\u{1F4C2}",
          "open_hands": "\u{1F450}",
          "open_mouth": "\u{1F62E}",
          "open_umbrella": "\u2602\uFE0F",
          "ophiuchus": "\u26CE",
          "orange_book": "\u{1F4D9}",
          "orthodox_cross": "\u2626\uFE0F",
          "outbox_tray": "\u{1F4E4}",
          "owl": "\u{1F989}",
          "ox": "\u{1F402}",
          "package": "\u{1F4E6}",
          "page_facing_up": "\u{1F4C4}",
          "page_with_curl": "\u{1F4C3}",
          "pager": "\u{1F4DF}",
          "paintbrush": "\u{1F58C}",
          "palm_tree": "\u{1F334}",
          "pancakes": "\u{1F95E}",
          "panda_face": "\u{1F43C}",
          "paperclip": "\u{1F4CE}",
          "paperclips": "\u{1F587}",
          "parasol_on_ground": "\u26F1",
          "parking": "\u{1F17F}\uFE0F",
          "part_alternation_mark": "\u303D\uFE0F",
          "partly_sunny": "\u26C5\uFE0F",
          "passenger_ship": "\u{1F6F3}",
          "passport_control": "\u{1F6C2}",
          "pause_button": "\u23F8",
          "peace_symbol": "\u262E\uFE0F",
          "peach": "\u{1F351}",
          "peanuts": "\u{1F95C}",
          "pear": "\u{1F350}",
          "pen": "\u{1F58A}",
          "pencil2": "\u270F\uFE0F",
          "penguin": "\u{1F427}",
          "pensive": "\u{1F614}",
          "performing_arts": "\u{1F3AD}",
          "persevere": "\u{1F623}",
          "person_fencing": "\u{1F93A}",
          "pouting_woman": "\u{1F64E}",
          "phone": "\u260E\uFE0F",
          "pick": "\u26CF",
          "pig": "\u{1F437}",
          "pig2": "\u{1F416}",
          "pig_nose": "\u{1F43D}",
          "pill": "\u{1F48A}",
          "pineapple": "\u{1F34D}",
          "ping_pong": "\u{1F3D3}",
          "pisces": "\u2653\uFE0F",
          "pizza": "\u{1F355}",
          "place_of_worship": "\u{1F6D0}",
          "plate_with_cutlery": "\u{1F37D}",
          "play_or_pause_button": "\u23EF",
          "point_down": "\u{1F447}",
          "point_left": "\u{1F448}",
          "point_right": "\u{1F449}",
          "point_up": "\u261D\uFE0F",
          "point_up_2": "\u{1F446}",
          "police_car": "\u{1F693}",
          "policewoman": "\u{1F46E}&zwj;\u2640\uFE0F",
          "poodle": "\u{1F429}",
          "popcorn": "\u{1F37F}",
          "post_office": "\u{1F3E3}",
          "postal_horn": "\u{1F4EF}",
          "postbox": "\u{1F4EE}",
          "potable_water": "\u{1F6B0}",
          "potato": "\u{1F954}",
          "pouch": "\u{1F45D}",
          "poultry_leg": "\u{1F357}",
          "pound": "\u{1F4B7}",
          "rage": "\u{1F621}",
          "pouting_cat": "\u{1F63E}",
          "pouting_man": "\u{1F64E}&zwj;\u2642\uFE0F",
          "pray": "\u{1F64F}",
          "prayer_beads": "\u{1F4FF}",
          "pregnant_woman": "\u{1F930}",
          "previous_track_button": "\u23EE",
          "prince": "\u{1F934}",
          "princess": "\u{1F478}",
          "printer": "\u{1F5A8}",
          "purple_heart": "\u{1F49C}",
          "purse": "\u{1F45B}",
          "pushpin": "\u{1F4CC}",
          "put_litter_in_its_place": "\u{1F6AE}",
          "question": "\u2753",
          "rabbit": "\u{1F430}",
          "rabbit2": "\u{1F407}",
          "racehorse": "\u{1F40E}",
          "racing_car": "\u{1F3CE}",
          "radio": "\u{1F4FB}",
          "radio_button": "\u{1F518}",
          "radioactive": "\u2622\uFE0F",
          "railway_car": "\u{1F683}",
          "railway_track": "\u{1F6E4}",
          "rainbow": "\u{1F308}",
          "rainbow_flag": "\u{1F3F3}\uFE0F&zwj;\u{1F308}",
          "raised_back_of_hand": "\u{1F91A}",
          "raised_hand_with_fingers_splayed": "\u{1F590}",
          "raised_hands": "\u{1F64C}",
          "raising_hand_woman": "\u{1F64B}",
          "raising_hand_man": "\u{1F64B}&zwj;\u2642\uFE0F",
          "ram": "\u{1F40F}",
          "ramen": "\u{1F35C}",
          "rat": "\u{1F400}",
          "record_button": "\u23FA",
          "recycle": "\u267B\uFE0F",
          "red_circle": "\u{1F534}",
          "registered": "\xAE\uFE0F",
          "relaxed": "\u263A\uFE0F",
          "relieved": "\u{1F60C}",
          "reminder_ribbon": "\u{1F397}",
          "repeat": "\u{1F501}",
          "repeat_one": "\u{1F502}",
          "rescue_worker_helmet": "\u26D1",
          "restroom": "\u{1F6BB}",
          "revolving_hearts": "\u{1F49E}",
          "rewind": "\u23EA",
          "rhinoceros": "\u{1F98F}",
          "ribbon": "\u{1F380}",
          "rice": "\u{1F35A}",
          "rice_ball": "\u{1F359}",
          "rice_cracker": "\u{1F358}",
          "rice_scene": "\u{1F391}",
          "right_anger_bubble": "\u{1F5EF}",
          "ring": "\u{1F48D}",
          "robot": "\u{1F916}",
          "rocket": "\u{1F680}",
          "rofl": "\u{1F923}",
          "roll_eyes": "\u{1F644}",
          "roller_coaster": "\u{1F3A2}",
          "rooster": "\u{1F413}",
          "rose": "\u{1F339}",
          "rosette": "\u{1F3F5}",
          "rotating_light": "\u{1F6A8}",
          "round_pushpin": "\u{1F4CD}",
          "rowing_man": "\u{1F6A3}",
          "rowing_woman": "\u{1F6A3}&zwj;\u2640\uFE0F",
          "rugby_football": "\u{1F3C9}",
          "running_man": "\u{1F3C3}",
          "running_shirt_with_sash": "\u{1F3BD}",
          "running_woman": "\u{1F3C3}&zwj;\u2640\uFE0F",
          "sa": "\u{1F202}\uFE0F",
          "sagittarius": "\u2650\uFE0F",
          "sake": "\u{1F376}",
          "sandal": "\u{1F461}",
          "santa": "\u{1F385}",
          "satellite": "\u{1F4E1}",
          "saxophone": "\u{1F3B7}",
          "school": "\u{1F3EB}",
          "school_satchel": "\u{1F392}",
          "scissors": "\u2702\uFE0F",
          "scorpion": "\u{1F982}",
          "scorpius": "\u264F\uFE0F",
          "scream": "\u{1F631}",
          "scream_cat": "\u{1F640}",
          "scroll": "\u{1F4DC}",
          "seat": "\u{1F4BA}",
          "secret": "\u3299\uFE0F",
          "see_no_evil": "\u{1F648}",
          "seedling": "\u{1F331}",
          "selfie": "\u{1F933}",
          "shallow_pan_of_food": "\u{1F958}",
          "shamrock": "\u2618\uFE0F",
          "shark": "\u{1F988}",
          "shaved_ice": "\u{1F367}",
          "sheep": "\u{1F411}",
          "shell": "\u{1F41A}",
          "shield": "\u{1F6E1}",
          "shinto_shrine": "\u26E9",
          "ship": "\u{1F6A2}",
          "shirt": "\u{1F455}",
          "shopping": "\u{1F6CD}",
          "shopping_cart": "\u{1F6D2}",
          "shower": "\u{1F6BF}",
          "shrimp": "\u{1F990}",
          "signal_strength": "\u{1F4F6}",
          "six_pointed_star": "\u{1F52F}",
          "ski": "\u{1F3BF}",
          "skier": "\u26F7",
          "skull": "\u{1F480}",
          "skull_and_crossbones": "\u2620\uFE0F",
          "sleeping": "\u{1F634}",
          "sleeping_bed": "\u{1F6CC}",
          "sleepy": "\u{1F62A}",
          "slightly_frowning_face": "\u{1F641}",
          "slightly_smiling_face": "\u{1F642}",
          "slot_machine": "\u{1F3B0}",
          "small_airplane": "\u{1F6E9}",
          "small_blue_diamond": "\u{1F539}",
          "small_orange_diamond": "\u{1F538}",
          "small_red_triangle": "\u{1F53A}",
          "small_red_triangle_down": "\u{1F53B}",
          "smile": "\u{1F604}",
          "smile_cat": "\u{1F638}",
          "smiley": "\u{1F603}",
          "smiley_cat": "\u{1F63A}",
          "smiling_imp": "\u{1F608}",
          "smirk": "\u{1F60F}",
          "smirk_cat": "\u{1F63C}",
          "smoking": "\u{1F6AC}",
          "snail": "\u{1F40C}",
          "snake": "\u{1F40D}",
          "sneezing_face": "\u{1F927}",
          "snowboarder": "\u{1F3C2}",
          "snowflake": "\u2744\uFE0F",
          "snowman": "\u26C4\uFE0F",
          "snowman_with_snow": "\u2603\uFE0F",
          "sob": "\u{1F62D}",
          "soccer": "\u26BD\uFE0F",
          "soon": "\u{1F51C}",
          "sos": "\u{1F198}",
          "sound": "\u{1F509}",
          "space_invader": "\u{1F47E}",
          "spades": "\u2660\uFE0F",
          "spaghetti": "\u{1F35D}",
          "sparkle": "\u2747\uFE0F",
          "sparkler": "\u{1F387}",
          "sparkles": "\u2728",
          "sparkling_heart": "\u{1F496}",
          "speak_no_evil": "\u{1F64A}",
          "speaker": "\u{1F508}",
          "speaking_head": "\u{1F5E3}",
          "speech_balloon": "\u{1F4AC}",
          "speedboat": "\u{1F6A4}",
          "spider": "\u{1F577}",
          "spider_web": "\u{1F578}",
          "spiral_calendar": "\u{1F5D3}",
          "spiral_notepad": "\u{1F5D2}",
          "spoon": "\u{1F944}",
          "squid": "\u{1F991}",
          "stadium": "\u{1F3DF}",
          "star": "\u2B50\uFE0F",
          "star2": "\u{1F31F}",
          "star_and_crescent": "\u262A\uFE0F",
          "star_of_david": "\u2721\uFE0F",
          "stars": "\u{1F320}",
          "station": "\u{1F689}",
          "statue_of_liberty": "\u{1F5FD}",
          "steam_locomotive": "\u{1F682}",
          "stew": "\u{1F372}",
          "stop_button": "\u23F9",
          "stop_sign": "\u{1F6D1}",
          "stopwatch": "\u23F1",
          "straight_ruler": "\u{1F4CF}",
          "strawberry": "\u{1F353}",
          "stuck_out_tongue": "\u{1F61B}",
          "stuck_out_tongue_closed_eyes": "\u{1F61D}",
          "stuck_out_tongue_winking_eye": "\u{1F61C}",
          "studio_microphone": "\u{1F399}",
          "stuffed_flatbread": "\u{1F959}",
          "sun_behind_large_cloud": "\u{1F325}",
          "sun_behind_rain_cloud": "\u{1F326}",
          "sun_behind_small_cloud": "\u{1F324}",
          "sun_with_face": "\u{1F31E}",
          "sunflower": "\u{1F33B}",
          "sunglasses": "\u{1F60E}",
          "sunny": "\u2600\uFE0F",
          "sunrise": "\u{1F305}",
          "sunrise_over_mountains": "\u{1F304}",
          "surfing_man": "\u{1F3C4}",
          "surfing_woman": "\u{1F3C4}&zwj;\u2640\uFE0F",
          "sushi": "\u{1F363}",
          "suspension_railway": "\u{1F69F}",
          "sweat": "\u{1F613}",
          "sweat_drops": "\u{1F4A6}",
          "sweat_smile": "\u{1F605}",
          "sweet_potato": "\u{1F360}",
          "swimming_man": "\u{1F3CA}",
          "swimming_woman": "\u{1F3CA}&zwj;\u2640\uFE0F",
          "symbols": "\u{1F523}",
          "synagogue": "\u{1F54D}",
          "syringe": "\u{1F489}",
          "taco": "\u{1F32E}",
          "tada": "\u{1F389}",
          "tanabata_tree": "\u{1F38B}",
          "taurus": "\u2649\uFE0F",
          "taxi": "\u{1F695}",
          "tea": "\u{1F375}",
          "telephone_receiver": "\u{1F4DE}",
          "telescope": "\u{1F52D}",
          "tennis": "\u{1F3BE}",
          "tent": "\u26FA\uFE0F",
          "thermometer": "\u{1F321}",
          "thinking": "\u{1F914}",
          "thought_balloon": "\u{1F4AD}",
          "ticket": "\u{1F3AB}",
          "tickets": "\u{1F39F}",
          "tiger": "\u{1F42F}",
          "tiger2": "\u{1F405}",
          "timer_clock": "\u23F2",
          "tipping_hand_man": "\u{1F481}&zwj;\u2642\uFE0F",
          "tired_face": "\u{1F62B}",
          "tm": "\u2122\uFE0F",
          "toilet": "\u{1F6BD}",
          "tokyo_tower": "\u{1F5FC}",
          "tomato": "\u{1F345}",
          "tongue": "\u{1F445}",
          "top": "\u{1F51D}",
          "tophat": "\u{1F3A9}",
          "tornado": "\u{1F32A}",
          "trackball": "\u{1F5B2}",
          "tractor": "\u{1F69C}",
          "traffic_light": "\u{1F6A5}",
          "train": "\u{1F68B}",
          "train2": "\u{1F686}",
          "tram": "\u{1F68A}",
          "triangular_flag_on_post": "\u{1F6A9}",
          "triangular_ruler": "\u{1F4D0}",
          "trident": "\u{1F531}",
          "triumph": "\u{1F624}",
          "trolleybus": "\u{1F68E}",
          "trophy": "\u{1F3C6}",
          "tropical_drink": "\u{1F379}",
          "tropical_fish": "\u{1F420}",
          "truck": "\u{1F69A}",
          "trumpet": "\u{1F3BA}",
          "tulip": "\u{1F337}",
          "tumbler_glass": "\u{1F943}",
          "turkey": "\u{1F983}",
          "turtle": "\u{1F422}",
          "tv": "\u{1F4FA}",
          "twisted_rightwards_arrows": "\u{1F500}",
          "two_hearts": "\u{1F495}",
          "two_men_holding_hands": "\u{1F46C}",
          "two_women_holding_hands": "\u{1F46D}",
          "u5272": "\u{1F239}",
          "u5408": "\u{1F234}",
          "u55b6": "\u{1F23A}",
          "u6307": "\u{1F22F}\uFE0F",
          "u6708": "\u{1F237}\uFE0F",
          "u6709": "\u{1F236}",
          "u6e80": "\u{1F235}",
          "u7121": "\u{1F21A}\uFE0F",
          "u7533": "\u{1F238}",
          "u7981": "\u{1F232}",
          "u7a7a": "\u{1F233}",
          "umbrella": "\u2614\uFE0F",
          "unamused": "\u{1F612}",
          "underage": "\u{1F51E}",
          "unicorn": "\u{1F984}",
          "unlock": "\u{1F513}",
          "up": "\u{1F199}",
          "upside_down_face": "\u{1F643}",
          "v": "\u270C\uFE0F",
          "vertical_traffic_light": "\u{1F6A6}",
          "vhs": "\u{1F4FC}",
          "vibration_mode": "\u{1F4F3}",
          "video_camera": "\u{1F4F9}",
          "video_game": "\u{1F3AE}",
          "violin": "\u{1F3BB}",
          "virgo": "\u264D\uFE0F",
          "volcano": "\u{1F30B}",
          "volleyball": "\u{1F3D0}",
          "vs": "\u{1F19A}",
          "vulcan_salute": "\u{1F596}",
          "walking_man": "\u{1F6B6}",
          "walking_woman": "\u{1F6B6}&zwj;\u2640\uFE0F",
          "waning_crescent_moon": "\u{1F318}",
          "waning_gibbous_moon": "\u{1F316}",
          "warning": "\u26A0\uFE0F",
          "wastebasket": "\u{1F5D1}",
          "watch": "\u231A\uFE0F",
          "water_buffalo": "\u{1F403}",
          "watermelon": "\u{1F349}",
          "wave": "\u{1F44B}",
          "wavy_dash": "\u3030\uFE0F",
          "waxing_crescent_moon": "\u{1F312}",
          "wc": "\u{1F6BE}",
          "weary": "\u{1F629}",
          "wedding": "\u{1F492}",
          "weight_lifting_man": "\u{1F3CB}\uFE0F",
          "weight_lifting_woman": "\u{1F3CB}\uFE0F&zwj;\u2640\uFE0F",
          "whale": "\u{1F433}",
          "whale2": "\u{1F40B}",
          "wheel_of_dharma": "\u2638\uFE0F",
          "wheelchair": "\u267F\uFE0F",
          "white_check_mark": "\u2705",
          "white_circle": "\u26AA\uFE0F",
          "white_flag": "\u{1F3F3}\uFE0F",
          "white_flower": "\u{1F4AE}",
          "white_large_square": "\u2B1C\uFE0F",
          "white_medium_small_square": "\u25FD\uFE0F",
          "white_medium_square": "\u25FB\uFE0F",
          "white_small_square": "\u25AB\uFE0F",
          "white_square_button": "\u{1F533}",
          "wilted_flower": "\u{1F940}",
          "wind_chime": "\u{1F390}",
          "wind_face": "\u{1F32C}",
          "wine_glass": "\u{1F377}",
          "wink": "\u{1F609}",
          "wolf": "\u{1F43A}",
          "woman": "\u{1F469}",
          "woman_artist": "\u{1F469}&zwj;\u{1F3A8}",
          "woman_astronaut": "\u{1F469}&zwj;\u{1F680}",
          "woman_cartwheeling": "\u{1F938}&zwj;\u2640\uFE0F",
          "woman_cook": "\u{1F469}&zwj;\u{1F373}",
          "woman_facepalming": "\u{1F926}&zwj;\u2640\uFE0F",
          "woman_factory_worker": "\u{1F469}&zwj;\u{1F3ED}",
          "woman_farmer": "\u{1F469}&zwj;\u{1F33E}",
          "woman_firefighter": "\u{1F469}&zwj;\u{1F692}",
          "woman_health_worker": "\u{1F469}&zwj;\u2695\uFE0F",
          "woman_judge": "\u{1F469}&zwj;\u2696\uFE0F",
          "woman_juggling": "\u{1F939}&zwj;\u2640\uFE0F",
          "woman_mechanic": "\u{1F469}&zwj;\u{1F527}",
          "woman_office_worker": "\u{1F469}&zwj;\u{1F4BC}",
          "woman_pilot": "\u{1F469}&zwj;\u2708\uFE0F",
          "woman_playing_handball": "\u{1F93E}&zwj;\u2640\uFE0F",
          "woman_playing_water_polo": "\u{1F93D}&zwj;\u2640\uFE0F",
          "woman_scientist": "\u{1F469}&zwj;\u{1F52C}",
          "woman_shrugging": "\u{1F937}&zwj;\u2640\uFE0F",
          "woman_singer": "\u{1F469}&zwj;\u{1F3A4}",
          "woman_student": "\u{1F469}&zwj;\u{1F393}",
          "woman_teacher": "\u{1F469}&zwj;\u{1F3EB}",
          "woman_technologist": "\u{1F469}&zwj;\u{1F4BB}",
          "woman_with_turban": "\u{1F473}&zwj;\u2640\uFE0F",
          "womans_clothes": "\u{1F45A}",
          "womans_hat": "\u{1F452}",
          "women_wrestling": "\u{1F93C}&zwj;\u2640\uFE0F",
          "womens": "\u{1F6BA}",
          "world_map": "\u{1F5FA}",
          "worried": "\u{1F61F}",
          "wrench": "\u{1F527}",
          "writing_hand": "\u270D\uFE0F",
          "x": "\u274C",
          "yellow_heart": "\u{1F49B}",
          "yen": "\u{1F4B4}",
          "yin_yang": "\u262F\uFE0F",
          "yum": "\u{1F60B}",
          "zap": "\u26A1\uFE0F",
          "zipper_mouth_face": "\u{1F910}",
          "zzz": "\u{1F4A4}",
          /* special emojis :P */
          "octocat": '<img alt=":octocat:" height="20" width="20" align="absmiddle" src="https://assets-cdn.github.com/images/icons/emoji/octocat.png">',
          "showdown": `<span style="font-family: 'Anonymous Pro', monospace; text-decoration: underline; text-decoration-style: dashed; text-decoration-color: #3e8b8a;text-underline-position: under;">S</span>`
        };
        showdown2.Converter = function(converterOptions) {
          "use strict";
          var options = {}, langExtensions = [], outputModifiers = [], listeners = {}, setConvFlavor = setFlavor, metadata = {
            parsed: {},
            raw: "",
            format: ""
          };
          _constructor();
          function _constructor() {
            converterOptions = converterOptions || {};
            for (var gOpt in globalOptions) {
              if (globalOptions.hasOwnProperty(gOpt)) {
                options[gOpt] = globalOptions[gOpt];
              }
            }
            if (typeof converterOptions === "object") {
              for (var opt in converterOptions) {
                if (converterOptions.hasOwnProperty(opt)) {
                  options[opt] = converterOptions[opt];
                }
              }
            } else {
              throw Error("Converter expects the passed parameter to be an object, but " + typeof converterOptions + " was passed instead.");
            }
            if (options.extensions) {
              showdown2.helper.forEach(options.extensions, _parseExtension);
            }
          }
          function _parseExtension(ext, name) {
            name = name || null;
            if (showdown2.helper.isString(ext)) {
              ext = showdown2.helper.stdExtName(ext);
              name = ext;
              if (showdown2.extensions[ext]) {
                console.warn("DEPRECATION WARNING: " + ext + " is an old extension that uses a deprecated loading method.Please inform the developer that the extension should be updated!");
                legacyExtensionLoading(showdown2.extensions[ext], ext);
                return;
              } else if (!showdown2.helper.isUndefined(extensions[ext])) {
                ext = extensions[ext];
              } else {
                throw Error('Extension "' + ext + '" could not be loaded. It was either not found or is not a valid extension.');
              }
            }
            if (typeof ext === "function") {
              ext = ext();
            }
            if (!showdown2.helper.isArray(ext)) {
              ext = [ext];
            }
            var validExt = validate(ext, name);
            if (!validExt.valid) {
              throw Error(validExt.error);
            }
            for (var i2 = 0; i2 < ext.length; ++i2) {
              switch (ext[i2].type) {
                case "lang":
                  langExtensions.push(ext[i2]);
                  break;
                case "output":
                  outputModifiers.push(ext[i2]);
                  break;
              }
              if (ext[i2].hasOwnProperty("listeners")) {
                for (var ln in ext[i2].listeners) {
                  if (ext[i2].listeners.hasOwnProperty(ln)) {
                    listen(ln, ext[i2].listeners[ln]);
                  }
                }
              }
            }
          }
          function legacyExtensionLoading(ext, name) {
            if (typeof ext === "function") {
              ext = ext(new showdown2.Converter());
            }
            if (!showdown2.helper.isArray(ext)) {
              ext = [ext];
            }
            var valid = validate(ext, name);
            if (!valid.valid) {
              throw Error(valid.error);
            }
            for (var i2 = 0; i2 < ext.length; ++i2) {
              switch (ext[i2].type) {
                case "lang":
                  langExtensions.push(ext[i2]);
                  break;
                case "output":
                  outputModifiers.push(ext[i2]);
                  break;
                default:
                  throw Error("Extension loader error: Type unrecognized!!!");
              }
            }
          }
          function listen(name, callback) {
            if (!showdown2.helper.isString(name)) {
              throw Error("Invalid argument in converter.listen() method: name must be a string, but " + typeof name + " given");
            }
            if (typeof callback !== "function") {
              throw Error("Invalid argument in converter.listen() method: callback must be a function, but " + typeof callback + " given");
            }
            if (!listeners.hasOwnProperty(name)) {
              listeners[name] = [];
            }
            listeners[name].push(callback);
          }
          function rTrimInputText(text2) {
            var rsp = text2.match(/^\s*/)[0].length, rgx = new RegExp("^\\s{0," + rsp + "}", "gm");
            return text2.replace(rgx, "");
          }
          this._dispatch = function dispatch3(evtName, text2, options2, globals) {
            if (listeners.hasOwnProperty(evtName)) {
              for (var ei = 0; ei < listeners[evtName].length; ++ei) {
                var nText = listeners[evtName][ei](evtName, text2, this, options2, globals);
                if (nText && typeof nText !== "undefined") {
                  text2 = nText;
                }
              }
            }
            return text2;
          };
          this.listen = function(name, callback) {
            listen(name, callback);
            return this;
          };
          this.makeHtml = function(text2) {
            if (!text2) {
              return text2;
            }
            var globals = {
              gHtmlBlocks: [],
              gHtmlMdBlocks: [],
              gHtmlSpans: [],
              gUrls: {},
              gTitles: {},
              gDimensions: {},
              gListLevel: 0,
              hashLinkCounts: {},
              langExtensions,
              outputModifiers,
              converter: this,
              ghCodeBlocks: [],
              metadata: {
                parsed: {},
                raw: "",
                format: ""
              }
            };
            text2 = text2.replace(/¨/g, "\xA8T");
            text2 = text2.replace(/\$/g, "\xA8D");
            text2 = text2.replace(/\r\n/g, "\n");
            text2 = text2.replace(/\r/g, "\n");
            text2 = text2.replace(/\u00A0/g, "&nbsp;");
            if (options.smartIndentationFix) {
              text2 = rTrimInputText(text2);
            }
            text2 = "\n\n" + text2 + "\n\n";
            text2 = showdown2.subParser("detab")(text2, options, globals);
            text2 = text2.replace(/^[ \t]+$/mg, "");
            showdown2.helper.forEach(langExtensions, function(ext) {
              text2 = showdown2.subParser("runExtension")(ext, text2, options, globals);
            });
            text2 = showdown2.subParser("metadata")(text2, options, globals);
            text2 = showdown2.subParser("hashPreCodeTags")(text2, options, globals);
            text2 = showdown2.subParser("githubCodeBlocks")(text2, options, globals);
            text2 = showdown2.subParser("hashHTMLBlocks")(text2, options, globals);
            text2 = showdown2.subParser("hashCodeTags")(text2, options, globals);
            text2 = showdown2.subParser("stripLinkDefinitions")(text2, options, globals);
            text2 = showdown2.subParser("blockGamut")(text2, options, globals);
            text2 = showdown2.subParser("unhashHTMLSpans")(text2, options, globals);
            text2 = showdown2.subParser("unescapeSpecialChars")(text2, options, globals);
            text2 = text2.replace(/¨D/g, "$$");
            text2 = text2.replace(/¨T/g, "\xA8");
            text2 = showdown2.subParser("completeHTMLDocument")(text2, options, globals);
            showdown2.helper.forEach(outputModifiers, function(ext) {
              text2 = showdown2.subParser("runExtension")(ext, text2, options, globals);
            });
            metadata = globals.metadata;
            return text2;
          };
          this.makeMarkdown = this.makeMd = function(src, HTMLParser) {
            src = src.replace(/\r\n/g, "\n");
            src = src.replace(/\r/g, "\n");
            src = src.replace(/>[ \t]+</, ">\xA8NBSP;<");
            if (!HTMLParser) {
              if (window && window.document) {
                HTMLParser = window.document;
              } else {
                throw new Error("HTMLParser is undefined. If in a webworker or nodejs environment, you need to provide a WHATWG DOM and HTML such as JSDOM");
              }
            }
            var doc = HTMLParser.createElement("div");
            doc.innerHTML = src;
            var globals = {
              preList: substitutePreCodeTags(doc)
            };
            clean(doc);
            var nodes = doc.childNodes, mdDoc = "";
            for (var i2 = 0; i2 < nodes.length; i2++) {
              mdDoc += showdown2.subParser("makeMarkdown.node")(nodes[i2], globals);
            }
            function clean(node) {
              for (var n2 = 0; n2 < node.childNodes.length; ++n2) {
                var child = node.childNodes[n2];
                if (child.nodeType === 3) {
                  if (!/\S/.test(child.nodeValue)) {
                    node.removeChild(child);
                    --n2;
                  } else {
                    child.nodeValue = child.nodeValue.split("\n").join(" ");
                    child.nodeValue = child.nodeValue.replace(/(\s)+/g, "$1");
                  }
                } else if (child.nodeType === 1) {
                  clean(child);
                }
              }
            }
            function substitutePreCodeTags(doc2) {
              var pres = doc2.querySelectorAll("pre"), presPH = [];
              for (var i3 = 0; i3 < pres.length; ++i3) {
                if (pres[i3].childElementCount === 1 && pres[i3].firstChild.tagName.toLowerCase() === "code") {
                  var content = pres[i3].firstChild.innerHTML.trim(), language = pres[i3].firstChild.getAttribute("data-language") || "";
                  if (language === "") {
                    var classes = pres[i3].firstChild.className.split(" ");
                    for (var c2 = 0; c2 < classes.length; ++c2) {
                      var matches = classes[c2].match(/^language-(.+)$/);
                      if (matches !== null) {
                        language = matches[1];
                        break;
                      }
                    }
                  }
                  content = showdown2.helper.unescapeHTMLEntities(content);
                  presPH.push(content);
                  pres[i3].outerHTML = '<precode language="' + language + '" precodenum="' + i3.toString() + '"></precode>';
                } else {
                  presPH.push(pres[i3].innerHTML);
                  pres[i3].innerHTML = "";
                  pres[i3].setAttribute("prenum", i3.toString());
                }
              }
              return presPH;
            }
            return mdDoc;
          };
          this.setOption = function(key, value) {
            options[key] = value;
          };
          this.getOption = function(key) {
            return options[key];
          };
          this.getOptions = function() {
            return options;
          };
          this.addExtension = function(extension, name) {
            name = name || null;
            _parseExtension(extension, name);
          };
          this.useExtension = function(extensionName) {
            _parseExtension(extensionName);
          };
          this.setFlavor = function(name) {
            if (!flavor.hasOwnProperty(name)) {
              throw Error(name + " flavor was not found");
            }
            var preset = flavor[name];
            setConvFlavor = name;
            for (var option in preset) {
              if (preset.hasOwnProperty(option)) {
                options[option] = preset[option];
              }
            }
          };
          this.getFlavor = function() {
            return setConvFlavor;
          };
          this.removeExtension = function(extension) {
            if (!showdown2.helper.isArray(extension)) {
              extension = [extension];
            }
            for (var a2 = 0; a2 < extension.length; ++a2) {
              var ext = extension[a2];
              for (var i2 = 0; i2 < langExtensions.length; ++i2) {
                if (langExtensions[i2] === ext) {
                  langExtensions[i2].splice(i2, 1);
                }
              }
              for (var ii = 0; ii < outputModifiers.length; ++i2) {
                if (outputModifiers[ii] === ext) {
                  outputModifiers[ii].splice(i2, 1);
                }
              }
            }
          };
          this.getAllExtensions = function() {
            return {
              language: langExtensions,
              output: outputModifiers
            };
          };
          this.getMetadata = function(raw) {
            if (raw) {
              return metadata.raw;
            } else {
              return metadata.parsed;
            }
          };
          this.getMetadataFormat = function() {
            return metadata.format;
          };
          this._setMetadataPair = function(key, value) {
            metadata.parsed[key] = value;
          };
          this._setMetadataFormat = function(format) {
            metadata.format = format;
          };
          this._setMetadataRaw = function(raw) {
            metadata.raw = raw;
          };
        };
        showdown2.subParser("anchors", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("anchors.before", text2, options, globals);
          var writeAnchorTag = function(wholeMatch, linkText, linkId, url, m5, m6, title) {
            if (showdown2.helper.isUndefined(title)) {
              title = "";
            }
            linkId = linkId.toLowerCase();
            if (wholeMatch.search(/\(<?\s*>? ?(['"].*['"])?\)$/m) > -1) {
              url = "";
            } else if (!url) {
              if (!linkId) {
                linkId = linkText.toLowerCase().replace(/ ?\n/g, " ");
              }
              url = "#" + linkId;
              if (!showdown2.helper.isUndefined(globals.gUrls[linkId])) {
                url = globals.gUrls[linkId];
                if (!showdown2.helper.isUndefined(globals.gTitles[linkId])) {
                  title = globals.gTitles[linkId];
                }
              } else {
                return wholeMatch;
              }
            }
            url = url.replace(showdown2.helper.regexes.asteriskDashAndColon, showdown2.helper.escapeCharactersCallback);
            var result = '<a href="' + url + '"';
            if (title !== "" && title !== null) {
              title = title.replace(/"/g, "&quot;");
              title = title.replace(showdown2.helper.regexes.asteriskDashAndColon, showdown2.helper.escapeCharactersCallback);
              result += ' title="' + title + '"';
            }
            if (options.openLinksInNewWindow && !/^#/.test(url)) {
              result += ' rel="noopener noreferrer" target="\xA8E95Eblank"';
            }
            result += ">" + linkText + "</a>";
            return result;
          };
          text2 = text2.replace(/\[((?:\[[^\]]*]|[^\[\]])*)] ?(?:\n *)?\[(.*?)]()()()()/g, writeAnchorTag);
          text2 = text2.replace(
            /\[((?:\[[^\]]*]|[^\[\]])*)]()[ \t]*\([ \t]?<([^>]*)>(?:[ \t]*((["'])([^"]*?)\5))?[ \t]?\)/g,
            writeAnchorTag
          );
          text2 = text2.replace(
            /\[((?:\[[^\]]*]|[^\[\]])*)]()[ \t]*\([ \t]?<?([\S]+?(?:\([\S]*?\)[\S]*?)?)>?(?:[ \t]*((["'])([^"]*?)\5))?[ \t]?\)/g,
            writeAnchorTag
          );
          text2 = text2.replace(/\[([^\[\]]+)]()()()()()/g, writeAnchorTag);
          if (options.ghMentions) {
            text2 = text2.replace(/(^|\s)(\\)?(@([a-z\d]+(?:[a-z\d.-]+?[a-z\d]+)*))/gmi, function(wm, st, escape, mentions, username) {
              if (escape === "\\") {
                return st + mentions;
              }
              if (!showdown2.helper.isString(options.ghMentionsLink)) {
                throw new Error("ghMentionsLink option must be a string");
              }
              var lnk = options.ghMentionsLink.replace(/\{u}/g, username), target = "";
              if (options.openLinksInNewWindow) {
                target = ' rel="noopener noreferrer" target="\xA8E95Eblank"';
              }
              return st + '<a href="' + lnk + '"' + target + ">" + mentions + "</a>";
            });
          }
          text2 = globals.converter._dispatch("anchors.after", text2, options, globals);
          return text2;
        });
        var simpleURLRegex = /([*~_]+|\b)(((https?|ftp|dict):\/\/|www\.)[^'">\s]+?\.[^'">\s]+?)()(\1)?(?=\s|$)(?!["<>])/gi, simpleURLRegex2 = /([*~_]+|\b)(((https?|ftp|dict):\/\/|www\.)[^'">\s]+\.[^'">\s]+?)([.!?,()\[\]])?(\1)?(?=\s|$)(?!["<>])/gi, delimUrlRegex = /()<(((https?|ftp|dict):\/\/|www\.)[^'">\s]+)()>()/gi, simpleMailRegex = /(^|\s)(?:mailto:)?([A-Za-z0-9!#$%&'*+-/=?^_`{|}~.]+@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]+)(?=$|\s)/gmi, delimMailRegex = /<()(?:mailto:)?([-.\w]+@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]+)>/gi, replaceLink = function(options) {
          "use strict";
          return function(wm, leadingMagicChars, link, m2, m3, trailingPunctuation, trailingMagicChars) {
            link = link.replace(showdown2.helper.regexes.asteriskDashAndColon, showdown2.helper.escapeCharactersCallback);
            var lnkTxt = link, append = "", target = "", lmc = leadingMagicChars || "", tmc = trailingMagicChars || "";
            if (/^www\./i.test(link)) {
              link = link.replace(/^www\./i, "http://www.");
            }
            if (options.excludeTrailingPunctuationFromURLs && trailingPunctuation) {
              append = trailingPunctuation;
            }
            if (options.openLinksInNewWindow) {
              target = ' rel="noopener noreferrer" target="\xA8E95Eblank"';
            }
            return lmc + '<a href="' + link + '"' + target + ">" + lnkTxt + "</a>" + append + tmc;
          };
        }, replaceMail = function(options, globals) {
          "use strict";
          return function(wholeMatch, b2, mail) {
            var href = "mailto:";
            b2 = b2 || "";
            mail = showdown2.subParser("unescapeSpecialChars")(mail, options, globals);
            if (options.encodeEmails) {
              href = showdown2.helper.encodeEmailAddress(href + mail);
              mail = showdown2.helper.encodeEmailAddress(mail);
            } else {
              href = href + mail;
            }
            return b2 + '<a href="' + href + '">' + mail + "</a>";
          };
        };
        showdown2.subParser("autoLinks", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("autoLinks.before", text2, options, globals);
          text2 = text2.replace(delimUrlRegex, replaceLink(options));
          text2 = text2.replace(delimMailRegex, replaceMail(options, globals));
          text2 = globals.converter._dispatch("autoLinks.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("simplifiedAutoLinks", function(text2, options, globals) {
          "use strict";
          if (!options.simplifiedAutoLink) {
            return text2;
          }
          text2 = globals.converter._dispatch("simplifiedAutoLinks.before", text2, options, globals);
          if (options.excludeTrailingPunctuationFromURLs) {
            text2 = text2.replace(simpleURLRegex2, replaceLink(options));
          } else {
            text2 = text2.replace(simpleURLRegex, replaceLink(options));
          }
          text2 = text2.replace(simpleMailRegex, replaceMail(options, globals));
          text2 = globals.converter._dispatch("simplifiedAutoLinks.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("blockGamut", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("blockGamut.before", text2, options, globals);
          text2 = showdown2.subParser("blockQuotes")(text2, options, globals);
          text2 = showdown2.subParser("headers")(text2, options, globals);
          text2 = showdown2.subParser("horizontalRule")(text2, options, globals);
          text2 = showdown2.subParser("lists")(text2, options, globals);
          text2 = showdown2.subParser("codeBlocks")(text2, options, globals);
          text2 = showdown2.subParser("tables")(text2, options, globals);
          text2 = showdown2.subParser("hashHTMLBlocks")(text2, options, globals);
          text2 = showdown2.subParser("paragraphs")(text2, options, globals);
          text2 = globals.converter._dispatch("blockGamut.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("blockQuotes", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("blockQuotes.before", text2, options, globals);
          text2 = text2 + "\n\n";
          var rgx = /(^ {0,3}>[ \t]?.+\n(.+\n)*\n*)+/gm;
          if (options.splitAdjacentBlockquotes) {
            rgx = /^ {0,3}>[\s\S]*?(?:\n\n)/gm;
          }
          text2 = text2.replace(rgx, function(bq) {
            bq = bq.replace(/^[ \t]*>[ \t]?/gm, "");
            bq = bq.replace(/¨0/g, "");
            bq = bq.replace(/^[ \t]+$/gm, "");
            bq = showdown2.subParser("githubCodeBlocks")(bq, options, globals);
            bq = showdown2.subParser("blockGamut")(bq, options, globals);
            bq = bq.replace(/(^|\n)/g, "$1  ");
            bq = bq.replace(/(\s*<pre>[^\r]+?<\/pre>)/gm, function(wholeMatch, m1) {
              var pre = m1;
              pre = pre.replace(/^  /mg, "\xA80");
              pre = pre.replace(/¨0/g, "");
              return pre;
            });
            return showdown2.subParser("hashBlock")("<blockquote>\n" + bq + "\n</blockquote>", options, globals);
          });
          text2 = globals.converter._dispatch("blockQuotes.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("codeBlocks", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("codeBlocks.before", text2, options, globals);
          text2 += "\xA80";
          var pattern = /(?:\n\n|^)((?:(?:[ ]{4}|\t).*\n+)+)(\n*[ ]{0,3}[^ \t\n]|(?=¨0))/g;
          text2 = text2.replace(pattern, function(wholeMatch, m1, m2) {
            var codeblock = m1, nextChar = m2, end = "\n";
            codeblock = showdown2.subParser("outdent")(codeblock, options, globals);
            codeblock = showdown2.subParser("encodeCode")(codeblock, options, globals);
            codeblock = showdown2.subParser("detab")(codeblock, options, globals);
            codeblock = codeblock.replace(/^\n+/g, "");
            codeblock = codeblock.replace(/\n+$/g, "");
            if (options.omitExtraWLInCodeBlocks) {
              end = "";
            }
            codeblock = "<pre><code>" + codeblock + end + "</code></pre>";
            return showdown2.subParser("hashBlock")(codeblock, options, globals) + nextChar;
          });
          text2 = text2.replace(/¨0/, "");
          text2 = globals.converter._dispatch("codeBlocks.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("codeSpans", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("codeSpans.before", text2, options, globals);
          if (typeof text2 === "undefined") {
            text2 = "";
          }
          text2 = text2.replace(
            /(^|[^\\])(`+)([^\r]*?[^`])\2(?!`)/gm,
            function(wholeMatch, m1, m2, m3) {
              var c2 = m3;
              c2 = c2.replace(/^([ \t]*)/g, "");
              c2 = c2.replace(/[ \t]*$/g, "");
              c2 = showdown2.subParser("encodeCode")(c2, options, globals);
              c2 = m1 + "<code>" + c2 + "</code>";
              c2 = showdown2.subParser("hashHTMLSpans")(c2, options, globals);
              return c2;
            }
          );
          text2 = globals.converter._dispatch("codeSpans.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("completeHTMLDocument", function(text2, options, globals) {
          "use strict";
          if (!options.completeHTMLDocument) {
            return text2;
          }
          text2 = globals.converter._dispatch("completeHTMLDocument.before", text2, options, globals);
          var doctype = "html", doctypeParsed = "<!DOCTYPE HTML>\n", title = "", charset = '<meta charset="utf-8">\n', lang = "", metadata = "";
          if (typeof globals.metadata.parsed.doctype !== "undefined") {
            doctypeParsed = "<!DOCTYPE " + globals.metadata.parsed.doctype + ">\n";
            doctype = globals.metadata.parsed.doctype.toString().toLowerCase();
            if (doctype === "html" || doctype === "html5") {
              charset = '<meta charset="utf-8">';
            }
          }
          for (var meta in globals.metadata.parsed) {
            if (globals.metadata.parsed.hasOwnProperty(meta)) {
              switch (meta.toLowerCase()) {
                case "doctype":
                  break;
                case "title":
                  title = "<title>" + globals.metadata.parsed.title + "</title>\n";
                  break;
                case "charset":
                  if (doctype === "html" || doctype === "html5") {
                    charset = '<meta charset="' + globals.metadata.parsed.charset + '">\n';
                  } else {
                    charset = '<meta name="charset" content="' + globals.metadata.parsed.charset + '">\n';
                  }
                  break;
                case "language":
                case "lang":
                  lang = ' lang="' + globals.metadata.parsed[meta] + '"';
                  metadata += '<meta name="' + meta + '" content="' + globals.metadata.parsed[meta] + '">\n';
                  break;
                default:
                  metadata += '<meta name="' + meta + '" content="' + globals.metadata.parsed[meta] + '">\n';
              }
            }
          }
          text2 = doctypeParsed + "<html" + lang + ">\n<head>\n" + title + charset + metadata + "</head>\n<body>\n" + text2.trim() + "\n</body>\n</html>";
          text2 = globals.converter._dispatch("completeHTMLDocument.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("detab", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("detab.before", text2, options, globals);
          text2 = text2.replace(/\t(?=\t)/g, "    ");
          text2 = text2.replace(/\t/g, "\xA8A\xA8B");
          text2 = text2.replace(/¨B(.+?)¨A/g, function(wholeMatch, m1) {
            var leadingText = m1, numSpaces = 4 - leadingText.length % 4;
            for (var i2 = 0; i2 < numSpaces; i2++) {
              leadingText += " ";
            }
            return leadingText;
          });
          text2 = text2.replace(/¨A/g, "    ");
          text2 = text2.replace(/¨B/g, "");
          text2 = globals.converter._dispatch("detab.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("ellipsis", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("ellipsis.before", text2, options, globals);
          text2 = text2.replace(/\.\.\./g, "\u2026");
          text2 = globals.converter._dispatch("ellipsis.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("emoji", function(text2, options, globals) {
          "use strict";
          if (!options.emoji) {
            return text2;
          }
          text2 = globals.converter._dispatch("emoji.before", text2, options, globals);
          var emojiRgx = /:([\S]+?):/g;
          text2 = text2.replace(emojiRgx, function(wm, emojiCode) {
            if (showdown2.helper.emojis.hasOwnProperty(emojiCode)) {
              return showdown2.helper.emojis[emojiCode];
            }
            return wm;
          });
          text2 = globals.converter._dispatch("emoji.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("encodeAmpsAndAngles", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("encodeAmpsAndAngles.before", text2, options, globals);
          text2 = text2.replace(/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/g, "&amp;");
          text2 = text2.replace(/<(?![a-z\/?$!])/gi, "&lt;");
          text2 = text2.replace(/</g, "&lt;");
          text2 = text2.replace(/>/g, "&gt;");
          text2 = globals.converter._dispatch("encodeAmpsAndAngles.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("encodeBackslashEscapes", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("encodeBackslashEscapes.before", text2, options, globals);
          text2 = text2.replace(/\\(\\)/g, showdown2.helper.escapeCharactersCallback);
          text2 = text2.replace(/\\([`*_{}\[\]()>#+.!~=|-])/g, showdown2.helper.escapeCharactersCallback);
          text2 = globals.converter._dispatch("encodeBackslashEscapes.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("encodeCode", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("encodeCode.before", text2, options, globals);
          text2 = text2.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/([*_{}\[\]\\=~-])/g, showdown2.helper.escapeCharactersCallback);
          text2 = globals.converter._dispatch("encodeCode.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("escapeSpecialCharsWithinTagAttributes", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("escapeSpecialCharsWithinTagAttributes.before", text2, options, globals);
          var tags = /<\/?[a-z\d_:-]+(?:[\s]+[\s\S]+?)?>/gi, comments = /<!(--(?:(?:[^>-]|-[^>])(?:[^-]|-[^-])*)--)>/gi;
          text2 = text2.replace(tags, function(wholeMatch) {
            return wholeMatch.replace(/(.)<\/?code>(?=.)/g, "$1`").replace(/([\\`*_~=|])/g, showdown2.helper.escapeCharactersCallback);
          });
          text2 = text2.replace(comments, function(wholeMatch) {
            return wholeMatch.replace(/([\\`*_~=|])/g, showdown2.helper.escapeCharactersCallback);
          });
          text2 = globals.converter._dispatch("escapeSpecialCharsWithinTagAttributes.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("githubCodeBlocks", function(text2, options, globals) {
          "use strict";
          if (!options.ghCodeBlocks) {
            return text2;
          }
          text2 = globals.converter._dispatch("githubCodeBlocks.before", text2, options, globals);
          text2 += "\xA80";
          text2 = text2.replace(/(?:^|\n)(?: {0,3})(```+|~~~+)(?: *)([^\s`~]*)\n([\s\S]*?)\n(?: {0,3})\1/g, function(wholeMatch, delim, language, codeblock) {
            var end = options.omitExtraWLInCodeBlocks ? "" : "\n";
            codeblock = showdown2.subParser("encodeCode")(codeblock, options, globals);
            codeblock = showdown2.subParser("detab")(codeblock, options, globals);
            codeblock = codeblock.replace(/^\n+/g, "");
            codeblock = codeblock.replace(/\n+$/g, "");
            codeblock = "<pre><code" + (language ? ' class="' + language + " language-" + language + '"' : "") + ">" + codeblock + end + "</code></pre>";
            codeblock = showdown2.subParser("hashBlock")(codeblock, options, globals);
            return "\n\n\xA8G" + (globals.ghCodeBlocks.push({ text: wholeMatch, codeblock }) - 1) + "G\n\n";
          });
          text2 = text2.replace(/¨0/, "");
          return globals.converter._dispatch("githubCodeBlocks.after", text2, options, globals);
        });
        showdown2.subParser("hashBlock", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("hashBlock.before", text2, options, globals);
          text2 = text2.replace(/(^\n+|\n+$)/g, "");
          text2 = "\n\n\xA8K" + (globals.gHtmlBlocks.push(text2) - 1) + "K\n\n";
          text2 = globals.converter._dispatch("hashBlock.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("hashCodeTags", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("hashCodeTags.before", text2, options, globals);
          var repFunc = function(wholeMatch, match, left, right) {
            var codeblock = left + showdown2.subParser("encodeCode")(match, options, globals) + right;
            return "\xA8C" + (globals.gHtmlSpans.push(codeblock) - 1) + "C";
          };
          text2 = showdown2.helper.replaceRecursiveRegExp(text2, repFunc, "<code\\b[^>]*>", "</code>", "gim");
          text2 = globals.converter._dispatch("hashCodeTags.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("hashElement", function(text2, options, globals) {
          "use strict";
          return function(wholeMatch, m1) {
            var blockText = m1;
            blockText = blockText.replace(/\n\n/g, "\n");
            blockText = blockText.replace(/^\n/, "");
            blockText = blockText.replace(/\n+$/g, "");
            blockText = "\n\n\xA8K" + (globals.gHtmlBlocks.push(blockText) - 1) + "K\n\n";
            return blockText;
          };
        });
        showdown2.subParser("hashHTMLBlocks", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("hashHTMLBlocks.before", text2, options, globals);
          var blockTags = [
            "pre",
            "div",
            "h1",
            "h2",
            "h3",
            "h4",
            "h5",
            "h6",
            "blockquote",
            "table",
            "dl",
            "ol",
            "ul",
            "script",
            "noscript",
            "form",
            "fieldset",
            "iframe",
            "math",
            "style",
            "section",
            "header",
            "footer",
            "nav",
            "article",
            "aside",
            "address",
            "audio",
            "canvas",
            "figure",
            "hgroup",
            "output",
            "video",
            "p"
          ], repFunc = function(wholeMatch, match, left, right) {
            var txt = wholeMatch;
            if (left.search(/\bmarkdown\b/) !== -1) {
              txt = left + globals.converter.makeHtml(match) + right;
            }
            return "\n\n\xA8K" + (globals.gHtmlBlocks.push(txt) - 1) + "K\n\n";
          };
          if (options.backslashEscapesHTMLTags) {
            text2 = text2.replace(/\\<(\/?[^>]+?)>/g, function(wm, inside) {
              return "&lt;" + inside + "&gt;";
            });
          }
          for (var i2 = 0; i2 < blockTags.length; ++i2) {
            var opTagPos, rgx1 = new RegExp("^ {0,3}(<" + blockTags[i2] + "\\b[^>]*>)", "im"), patLeft = "<" + blockTags[i2] + "\\b[^>]*>", patRight = "</" + blockTags[i2] + ">";
            while ((opTagPos = showdown2.helper.regexIndexOf(text2, rgx1)) !== -1) {
              var subTexts = showdown2.helper.splitAtIndex(text2, opTagPos), newSubText1 = showdown2.helper.replaceRecursiveRegExp(subTexts[1], repFunc, patLeft, patRight, "im");
              if (newSubText1 === subTexts[1]) {
                break;
              }
              text2 = subTexts[0].concat(newSubText1);
            }
          }
          text2 = text2.replace(
            /(\n {0,3}(<(hr)\b([^<>])*?\/?>)[ \t]*(?=\n{2,}))/g,
            showdown2.subParser("hashElement")(text2, options, globals)
          );
          text2 = showdown2.helper.replaceRecursiveRegExp(text2, function(txt) {
            return "\n\n\xA8K" + (globals.gHtmlBlocks.push(txt) - 1) + "K\n\n";
          }, "^ {0,3}<!--", "-->", "gm");
          text2 = text2.replace(
            /(?:\n\n)( {0,3}(?:<([?%])[^\r]*?\2>)[ \t]*(?=\n{2,}))/g,
            showdown2.subParser("hashElement")(text2, options, globals)
          );
          text2 = globals.converter._dispatch("hashHTMLBlocks.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("hashHTMLSpans", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("hashHTMLSpans.before", text2, options, globals);
          function hashHTMLSpan(html2) {
            return "\xA8C" + (globals.gHtmlSpans.push(html2) - 1) + "C";
          }
          text2 = text2.replace(/<[^>]+?\/>/gi, function(wm) {
            return hashHTMLSpan(wm);
          });
          text2 = text2.replace(/<([^>]+?)>[\s\S]*?<\/\1>/g, function(wm) {
            return hashHTMLSpan(wm);
          });
          text2 = text2.replace(/<([^>]+?)\s[^>]+?>[\s\S]*?<\/\1>/g, function(wm) {
            return hashHTMLSpan(wm);
          });
          text2 = text2.replace(/<[^>]+?>/gi, function(wm) {
            return hashHTMLSpan(wm);
          });
          text2 = globals.converter._dispatch("hashHTMLSpans.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("unhashHTMLSpans", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("unhashHTMLSpans.before", text2, options, globals);
          for (var i2 = 0; i2 < globals.gHtmlSpans.length; ++i2) {
            var repText = globals.gHtmlSpans[i2], limit = 0;
            while (/¨C(\d+)C/.test(repText)) {
              var num = RegExp.$1;
              repText = repText.replace("\xA8C" + num + "C", globals.gHtmlSpans[num]);
              if (limit === 10) {
                console.error("maximum nesting of 10 spans reached!!!");
                break;
              }
              ++limit;
            }
            text2 = text2.replace("\xA8C" + i2 + "C", repText);
          }
          text2 = globals.converter._dispatch("unhashHTMLSpans.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("hashPreCodeTags", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("hashPreCodeTags.before", text2, options, globals);
          var repFunc = function(wholeMatch, match, left, right) {
            var codeblock = left + showdown2.subParser("encodeCode")(match, options, globals) + right;
            return "\n\n\xA8G" + (globals.ghCodeBlocks.push({ text: wholeMatch, codeblock }) - 1) + "G\n\n";
          };
          text2 = showdown2.helper.replaceRecursiveRegExp(text2, repFunc, "^ {0,3}<pre\\b[^>]*>\\s*<code\\b[^>]*>", "^ {0,3}</code>\\s*</pre>", "gim");
          text2 = globals.converter._dispatch("hashPreCodeTags.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("headers", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("headers.before", text2, options, globals);
          var headerLevelStart = isNaN(parseInt(options.headerLevelStart)) ? 1 : parseInt(options.headerLevelStart), setextRegexH1 = options.smoothLivePreview ? /^(.+)[ \t]*\n={2,}[ \t]*\n+/gm : /^(.+)[ \t]*\n=+[ \t]*\n+/gm, setextRegexH2 = options.smoothLivePreview ? /^(.+)[ \t]*\n-{2,}[ \t]*\n+/gm : /^(.+)[ \t]*\n-+[ \t]*\n+/gm;
          text2 = text2.replace(setextRegexH1, function(wholeMatch, m1) {
            var spanGamut = showdown2.subParser("spanGamut")(m1, options, globals), hID = options.noHeaderId ? "" : ' id="' + headerId(m1) + '"', hLevel = headerLevelStart, hashBlock = "<h" + hLevel + hID + ">" + spanGamut + "</h" + hLevel + ">";
            return showdown2.subParser("hashBlock")(hashBlock, options, globals);
          });
          text2 = text2.replace(setextRegexH2, function(matchFound, m1) {
            var spanGamut = showdown2.subParser("spanGamut")(m1, options, globals), hID = options.noHeaderId ? "" : ' id="' + headerId(m1) + '"', hLevel = headerLevelStart + 1, hashBlock = "<h" + hLevel + hID + ">" + spanGamut + "</h" + hLevel + ">";
            return showdown2.subParser("hashBlock")(hashBlock, options, globals);
          });
          var atxStyle = options.requireSpaceBeforeHeadingText ? /^(#{1,6})[ \t]+(.+?)[ \t]*#*\n+/gm : /^(#{1,6})[ \t]*(.+?)[ \t]*#*\n+/gm;
          text2 = text2.replace(atxStyle, function(wholeMatch, m1, m2) {
            var hText = m2;
            if (options.customizedHeaderId) {
              hText = m2.replace(/\s?\{([^{]+?)}\s*$/, "");
            }
            var span = showdown2.subParser("spanGamut")(hText, options, globals), hID = options.noHeaderId ? "" : ' id="' + headerId(m2) + '"', hLevel = headerLevelStart - 1 + m1.length, header = "<h" + hLevel + hID + ">" + span + "</h" + hLevel + ">";
            return showdown2.subParser("hashBlock")(header, options, globals);
          });
          function headerId(m2) {
            var title, prefix;
            if (options.customizedHeaderId) {
              var match = m2.match(/\{([^{]+?)}\s*$/);
              if (match && match[1]) {
                m2 = match[1];
              }
            }
            title = m2;
            if (showdown2.helper.isString(options.prefixHeaderId)) {
              prefix = options.prefixHeaderId;
            } else if (options.prefixHeaderId === true) {
              prefix = "section-";
            } else {
              prefix = "";
            }
            if (!options.rawPrefixHeaderId) {
              title = prefix + title;
            }
            if (options.ghCompatibleHeaderId) {
              title = title.replace(/ /g, "-").replace(/&amp;/g, "").replace(/¨T/g, "").replace(/¨D/g, "").replace(/[&+$,\/:;=?@"#{}|^¨~\[\]`\\*)(%.!'<>]/g, "").toLowerCase();
            } else if (options.rawHeaderId) {
              title = title.replace(/ /g, "-").replace(/&amp;/g, "&").replace(/¨T/g, "\xA8").replace(/¨D/g, "$").replace(/["']/g, "-").toLowerCase();
            } else {
              title = title.replace(/[^\w]/g, "").toLowerCase();
            }
            if (options.rawPrefixHeaderId) {
              title = prefix + title;
            }
            if (globals.hashLinkCounts[title]) {
              title = title + "-" + globals.hashLinkCounts[title]++;
            } else {
              globals.hashLinkCounts[title] = 1;
            }
            return title;
          }
          text2 = globals.converter._dispatch("headers.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("horizontalRule", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("horizontalRule.before", text2, options, globals);
          var key = showdown2.subParser("hashBlock")("<hr />", options, globals);
          text2 = text2.replace(/^ {0,2}( ?-){3,}[ \t]*$/gm, key);
          text2 = text2.replace(/^ {0,2}( ?\*){3,}[ \t]*$/gm, key);
          text2 = text2.replace(/^ {0,2}( ?_){3,}[ \t]*$/gm, key);
          text2 = globals.converter._dispatch("horizontalRule.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("images", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("images.before", text2, options, globals);
          var inlineRegExp = /!\[([^\]]*?)][ \t]*()\([ \t]?<?([\S]+?(?:\([\S]*?\)[\S]*?)?)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*(?:(["'])([^"]*?)\6)?[ \t]?\)/g, crazyRegExp = /!\[([^\]]*?)][ \t]*()\([ \t]?<([^>]*)>(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*(?:(?:(["'])([^"]*?)\6))?[ \t]?\)/g, base64RegExp = /!\[([^\]]*?)][ \t]*()\([ \t]?<?(data:.+?\/.+?;base64,[A-Za-z0-9+/=\n]+?)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*(?:(["'])([^"]*?)\6)?[ \t]?\)/g, referenceRegExp = /!\[([^\]]*?)] ?(?:\n *)?\[([\s\S]*?)]()()()()()/g, refShortcutRegExp = /!\[([^\[\]]+)]()()()()()/g;
          function writeImageTagBase64(wholeMatch, altText, linkId, url, width, height, m5, title) {
            url = url.replace(/\s/g, "");
            return writeImageTag(wholeMatch, altText, linkId, url, width, height, m5, title);
          }
          function writeImageTag(wholeMatch, altText, linkId, url, width, height, m5, title) {
            var gUrls = globals.gUrls, gTitles = globals.gTitles, gDims = globals.gDimensions;
            linkId = linkId.toLowerCase();
            if (!title) {
              title = "";
            }
            if (wholeMatch.search(/\(<?\s*>? ?(['"].*['"])?\)$/m) > -1) {
              url = "";
            } else if (url === "" || url === null) {
              if (linkId === "" || linkId === null) {
                linkId = altText.toLowerCase().replace(/ ?\n/g, " ");
              }
              url = "#" + linkId;
              if (!showdown2.helper.isUndefined(gUrls[linkId])) {
                url = gUrls[linkId];
                if (!showdown2.helper.isUndefined(gTitles[linkId])) {
                  title = gTitles[linkId];
                }
                if (!showdown2.helper.isUndefined(gDims[linkId])) {
                  width = gDims[linkId].width;
                  height = gDims[linkId].height;
                }
              } else {
                return wholeMatch;
              }
            }
            altText = altText.replace(/"/g, "&quot;").replace(showdown2.helper.regexes.asteriskDashAndColon, showdown2.helper.escapeCharactersCallback);
            url = url.replace(showdown2.helper.regexes.asteriskDashAndColon, showdown2.helper.escapeCharactersCallback);
            var result = '<img src="' + url + '" alt="' + altText + '"';
            if (title && showdown2.helper.isString(title)) {
              title = title.replace(/"/g, "&quot;").replace(showdown2.helper.regexes.asteriskDashAndColon, showdown2.helper.escapeCharactersCallback);
              result += ' title="' + title + '"';
            }
            if (width && height) {
              width = width === "*" ? "auto" : width;
              height = height === "*" ? "auto" : height;
              result += ' width="' + width + '"';
              result += ' height="' + height + '"';
            }
            result += " />";
            return result;
          }
          text2 = text2.replace(referenceRegExp, writeImageTag);
          text2 = text2.replace(base64RegExp, writeImageTagBase64);
          text2 = text2.replace(crazyRegExp, writeImageTag);
          text2 = text2.replace(inlineRegExp, writeImageTag);
          text2 = text2.replace(refShortcutRegExp, writeImageTag);
          text2 = globals.converter._dispatch("images.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("italicsAndBold", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("italicsAndBold.before", text2, options, globals);
          function parseInside(txt, left, right) {
            return left + txt + right;
          }
          if (options.literalMidWordUnderscores) {
            text2 = text2.replace(/\b___(\S[\s\S]*?)___\b/g, function(wm, txt) {
              return parseInside(txt, "<strong><em>", "</em></strong>");
            });
            text2 = text2.replace(/\b__(\S[\s\S]*?)__\b/g, function(wm, txt) {
              return parseInside(txt, "<strong>", "</strong>");
            });
            text2 = text2.replace(/\b_(\S[\s\S]*?)_\b/g, function(wm, txt) {
              return parseInside(txt, "<em>", "</em>");
            });
          } else {
            text2 = text2.replace(/___(\S[\s\S]*?)___/g, function(wm, m2) {
              return /\S$/.test(m2) ? parseInside(m2, "<strong><em>", "</em></strong>") : wm;
            });
            text2 = text2.replace(/__(\S[\s\S]*?)__/g, function(wm, m2) {
              return /\S$/.test(m2) ? parseInside(m2, "<strong>", "</strong>") : wm;
            });
            text2 = text2.replace(/_([^\s_][\s\S]*?)_/g, function(wm, m2) {
              return /\S$/.test(m2) ? parseInside(m2, "<em>", "</em>") : wm;
            });
          }
          if (options.literalMidWordAsterisks) {
            text2 = text2.replace(/([^*]|^)\B\*\*\*(\S[\s\S]*?)\*\*\*\B(?!\*)/g, function(wm, lead, txt) {
              return parseInside(txt, lead + "<strong><em>", "</em></strong>");
            });
            text2 = text2.replace(/([^*]|^)\B\*\*(\S[\s\S]*?)\*\*\B(?!\*)/g, function(wm, lead, txt) {
              return parseInside(txt, lead + "<strong>", "</strong>");
            });
            text2 = text2.replace(/([^*]|^)\B\*(\S[\s\S]*?)\*\B(?!\*)/g, function(wm, lead, txt) {
              return parseInside(txt, lead + "<em>", "</em>");
            });
          } else {
            text2 = text2.replace(/\*\*\*(\S[\s\S]*?)\*\*\*/g, function(wm, m2) {
              return /\S$/.test(m2) ? parseInside(m2, "<strong><em>", "</em></strong>") : wm;
            });
            text2 = text2.replace(/\*\*(\S[\s\S]*?)\*\*/g, function(wm, m2) {
              return /\S$/.test(m2) ? parseInside(m2, "<strong>", "</strong>") : wm;
            });
            text2 = text2.replace(/\*([^\s*][\s\S]*?)\*/g, function(wm, m2) {
              return /\S$/.test(m2) ? parseInside(m2, "<em>", "</em>") : wm;
            });
          }
          text2 = globals.converter._dispatch("italicsAndBold.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("lists", function(text2, options, globals) {
          "use strict";
          function processListItems(listStr, trimTrailing) {
            globals.gListLevel++;
            listStr = listStr.replace(/\n{2,}$/, "\n");
            listStr += "\xA80";
            var rgx = /(\n)?(^ {0,3})([*+-]|\d+[.])[ \t]+((\[(x|X| )?])?[ \t]*[^\r]+?(\n{1,2}))(?=\n*(¨0| {0,3}([*+-]|\d+[.])[ \t]+))/gm, isParagraphed = /\n[ \t]*\n(?!¨0)/.test(listStr);
            if (options.disableForced4SpacesIndentedSublists) {
              rgx = /(\n)?(^ {0,3})([*+-]|\d+[.])[ \t]+((\[(x|X| )?])?[ \t]*[^\r]+?(\n{1,2}))(?=\n*(¨0|\2([*+-]|\d+[.])[ \t]+))/gm;
            }
            listStr = listStr.replace(rgx, function(wholeMatch, m1, m2, m3, m4, taskbtn, checked) {
              checked = checked && checked.trim() !== "";
              var item = showdown2.subParser("outdent")(m4, options, globals), bulletStyle = "";
              if (taskbtn && options.tasklists) {
                bulletStyle = ' class="task-list-item" style="list-style-type: none;"';
                item = item.replace(/^[ \t]*\[(x|X| )?]/m, function() {
                  var otp = '<input type="checkbox" disabled style="margin: 0px 0.35em 0.25em -1.6em; vertical-align: middle;"';
                  if (checked) {
                    otp += " checked";
                  }
                  otp += ">";
                  return otp;
                });
              }
              item = item.replace(/^([-*+]|\d\.)[ \t]+[\S\n ]*/g, function(wm2) {
                return "\xA8A" + wm2;
              });
              if (m1 || item.search(/\n{2,}/) > -1) {
                item = showdown2.subParser("githubCodeBlocks")(item, options, globals);
                item = showdown2.subParser("blockGamut")(item, options, globals);
              } else {
                item = showdown2.subParser("lists")(item, options, globals);
                item = item.replace(/\n$/, "");
                item = showdown2.subParser("hashHTMLBlocks")(item, options, globals);
                item = item.replace(/\n\n+/g, "\n\n");
                if (isParagraphed) {
                  item = showdown2.subParser("paragraphs")(item, options, globals);
                } else {
                  item = showdown2.subParser("spanGamut")(item, options, globals);
                }
              }
              item = item.replace("\xA8A", "");
              item = "<li" + bulletStyle + ">" + item + "</li>\n";
              return item;
            });
            listStr = listStr.replace(/¨0/g, "");
            globals.gListLevel--;
            if (trimTrailing) {
              listStr = listStr.replace(/\s+$/, "");
            }
            return listStr;
          }
          function styleStartNumber(list, listType) {
            if (listType === "ol") {
              var res = list.match(/^ *(\d+)\./);
              if (res && res[1] !== "1") {
                return ' start="' + res[1] + '"';
              }
            }
            return "";
          }
          function parseConsecutiveLists(list, listType, trimTrailing) {
            var olRgx = options.disableForced4SpacesIndentedSublists ? /^ ?\d+\.[ \t]/gm : /^ {0,3}\d+\.[ \t]/gm, ulRgx = options.disableForced4SpacesIndentedSublists ? /^ ?[*+-][ \t]/gm : /^ {0,3}[*+-][ \t]/gm, counterRxg = listType === "ul" ? olRgx : ulRgx, result = "";
            if (list.search(counterRxg) !== -1) {
              (function parseCL(txt) {
                var pos = txt.search(counterRxg), style2 = styleStartNumber(list, listType);
                if (pos !== -1) {
                  result += "\n\n<" + listType + style2 + ">\n" + processListItems(txt.slice(0, pos), !!trimTrailing) + "</" + listType + ">\n";
                  listType = listType === "ul" ? "ol" : "ul";
                  counterRxg = listType === "ul" ? olRgx : ulRgx;
                  parseCL(txt.slice(pos));
                } else {
                  result += "\n\n<" + listType + style2 + ">\n" + processListItems(txt, !!trimTrailing) + "</" + listType + ">\n";
                }
              })(list);
            } else {
              var style = styleStartNumber(list, listType);
              result = "\n\n<" + listType + style + ">\n" + processListItems(list, !!trimTrailing) + "</" + listType + ">\n";
            }
            return result;
          }
          text2 = globals.converter._dispatch("lists.before", text2, options, globals);
          text2 += "\xA80";
          if (globals.gListLevel) {
            text2 = text2.replace(
              /^(( {0,3}([*+-]|\d+[.])[ \t]+)[^\r]+?(¨0|\n{2,}(?=\S)(?![ \t]*(?:[*+-]|\d+[.])[ \t]+)))/gm,
              function(wholeMatch, list, m2) {
                var listType = m2.search(/[*+-]/g) > -1 ? "ul" : "ol";
                return parseConsecutiveLists(list, listType, true);
              }
            );
          } else {
            text2 = text2.replace(
              /(\n\n|^\n?)(( {0,3}([*+-]|\d+[.])[ \t]+)[^\r]+?(¨0|\n{2,}(?=\S)(?![ \t]*(?:[*+-]|\d+[.])[ \t]+)))/gm,
              function(wholeMatch, m1, list, m3) {
                var listType = m3.search(/[*+-]/g) > -1 ? "ul" : "ol";
                return parseConsecutiveLists(list, listType, false);
              }
            );
          }
          text2 = text2.replace(/¨0/, "");
          text2 = globals.converter._dispatch("lists.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("metadata", function(text2, options, globals) {
          "use strict";
          if (!options.metadata) {
            return text2;
          }
          text2 = globals.converter._dispatch("metadata.before", text2, options, globals);
          function parseMetadataContents(content) {
            globals.metadata.raw = content;
            content = content.replace(/&/g, "&amp;").replace(/"/g, "&quot;");
            content = content.replace(/\n {4}/g, " ");
            content.replace(/^([\S ]+): +([\s\S]+?)$/gm, function(wm, key, value) {
              globals.metadata.parsed[key] = value;
              return "";
            });
          }
          text2 = text2.replace(/^\s*«««+(\S*?)\n([\s\S]+?)\n»»»+\n/, function(wholematch, format, content) {
            parseMetadataContents(content);
            return "\xA8M";
          });
          text2 = text2.replace(/^\s*---+(\S*?)\n([\s\S]+?)\n---+\n/, function(wholematch, format, content) {
            if (format) {
              globals.metadata.format = format;
            }
            parseMetadataContents(content);
            return "\xA8M";
          });
          text2 = text2.replace(/¨M/g, "");
          text2 = globals.converter._dispatch("metadata.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("outdent", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("outdent.before", text2, options, globals);
          text2 = text2.replace(/^(\t|[ ]{1,4})/gm, "\xA80");
          text2 = text2.replace(/¨0/g, "");
          text2 = globals.converter._dispatch("outdent.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("paragraphs", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("paragraphs.before", text2, options, globals);
          text2 = text2.replace(/^\n+/g, "");
          text2 = text2.replace(/\n+$/g, "");
          var grafs = text2.split(/\n{2,}/g), grafsOut = [], end = grafs.length;
          for (var i2 = 0; i2 < end; i2++) {
            var str = grafs[i2];
            if (str.search(/¨(K|G)(\d+)\1/g) >= 0) {
              grafsOut.push(str);
            } else if (str.search(/\S/) >= 0) {
              str = showdown2.subParser("spanGamut")(str, options, globals);
              str = str.replace(/^([ \t]*)/g, "<p>");
              str += "</p>";
              grafsOut.push(str);
            }
          }
          end = grafsOut.length;
          for (i2 = 0; i2 < end; i2++) {
            var blockText = "", grafsOutIt = grafsOut[i2], codeFlag = false;
            while (/¨(K|G)(\d+)\1/.test(grafsOutIt)) {
              var delim = RegExp.$1, num = RegExp.$2;
              if (delim === "K") {
                blockText = globals.gHtmlBlocks[num];
              } else {
                if (codeFlag) {
                  blockText = showdown2.subParser("encodeCode")(globals.ghCodeBlocks[num].text, options, globals);
                } else {
                  blockText = globals.ghCodeBlocks[num].codeblock;
                }
              }
              blockText = blockText.replace(/\$/g, "$$$$");
              grafsOutIt = grafsOutIt.replace(/(\n\n)?¨(K|G)\d+\2(\n\n)?/, blockText);
              if (/^<pre\b[^>]*>\s*<code\b[^>]*>/.test(grafsOutIt)) {
                codeFlag = true;
              }
            }
            grafsOut[i2] = grafsOutIt;
          }
          text2 = grafsOut.join("\n");
          text2 = text2.replace(/^\n+/g, "");
          text2 = text2.replace(/\n+$/g, "");
          return globals.converter._dispatch("paragraphs.after", text2, options, globals);
        });
        showdown2.subParser("runExtension", function(ext, text2, options, globals) {
          "use strict";
          if (ext.filter) {
            text2 = ext.filter(text2, globals.converter, options);
          } else if (ext.regex) {
            var re = ext.regex;
            if (!(re instanceof RegExp)) {
              re = new RegExp(re, "g");
            }
            text2 = text2.replace(re, ext.replace);
          }
          return text2;
        });
        showdown2.subParser("spanGamut", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("spanGamut.before", text2, options, globals);
          text2 = showdown2.subParser("codeSpans")(text2, options, globals);
          text2 = showdown2.subParser("escapeSpecialCharsWithinTagAttributes")(text2, options, globals);
          text2 = showdown2.subParser("encodeBackslashEscapes")(text2, options, globals);
          text2 = showdown2.subParser("images")(text2, options, globals);
          text2 = showdown2.subParser("anchors")(text2, options, globals);
          text2 = showdown2.subParser("autoLinks")(text2, options, globals);
          text2 = showdown2.subParser("simplifiedAutoLinks")(text2, options, globals);
          text2 = showdown2.subParser("emoji")(text2, options, globals);
          text2 = showdown2.subParser("underline")(text2, options, globals);
          text2 = showdown2.subParser("italicsAndBold")(text2, options, globals);
          text2 = showdown2.subParser("strikethrough")(text2, options, globals);
          text2 = showdown2.subParser("ellipsis")(text2, options, globals);
          text2 = showdown2.subParser("hashHTMLSpans")(text2, options, globals);
          text2 = showdown2.subParser("encodeAmpsAndAngles")(text2, options, globals);
          if (options.simpleLineBreaks) {
            if (!/\n\n¨K/.test(text2)) {
              text2 = text2.replace(/\n+/g, "<br />\n");
            }
          } else {
            text2 = text2.replace(/  +\n/g, "<br />\n");
          }
          text2 = globals.converter._dispatch("spanGamut.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("strikethrough", function(text2, options, globals) {
          "use strict";
          function parseInside(txt) {
            if (options.simplifiedAutoLink) {
              txt = showdown2.subParser("simplifiedAutoLinks")(txt, options, globals);
            }
            return "<del>" + txt + "</del>";
          }
          if (options.strikethrough) {
            text2 = globals.converter._dispatch("strikethrough.before", text2, options, globals);
            text2 = text2.replace(/(?:~){2}([\s\S]+?)(?:~){2}/g, function(wm, txt) {
              return parseInside(txt);
            });
            text2 = globals.converter._dispatch("strikethrough.after", text2, options, globals);
          }
          return text2;
        });
        showdown2.subParser("stripLinkDefinitions", function(text2, options, globals) {
          "use strict";
          var regex = /^ {0,3}\[(.+)]:[ \t]*\n?[ \t]*<?([^>\s]+)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*\n?[ \t]*(?:(\n*)["|'(](.+?)["|')][ \t]*)?(?:\n+|(?=¨0))/gm, base64Regex = /^ {0,3}\[(.+)]:[ \t]*\n?[ \t]*<?(data:.+?\/.+?;base64,[A-Za-z0-9+/=\n]+?)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*\n?[ \t]*(?:(\n*)["|'(](.+?)["|')][ \t]*)?(?:\n\n|(?=¨0)|(?=\n\[))/gm;
          text2 += "\xA80";
          var replaceFunc = function(wholeMatch, linkId, url, width, height, blankLines, title) {
            linkId = linkId.toLowerCase();
            if (url.match(/^data:.+?\/.+?;base64,/)) {
              globals.gUrls[linkId] = url.replace(/\s/g, "");
            } else {
              globals.gUrls[linkId] = showdown2.subParser("encodeAmpsAndAngles")(url, options, globals);
            }
            if (blankLines) {
              return blankLines + title;
            } else {
              if (title) {
                globals.gTitles[linkId] = title.replace(/"|'/g, "&quot;");
              }
              if (options.parseImgDimensions && width && height) {
                globals.gDimensions[linkId] = {
                  width,
                  height
                };
              }
            }
            return "";
          };
          text2 = text2.replace(base64Regex, replaceFunc);
          text2 = text2.replace(regex, replaceFunc);
          text2 = text2.replace(/¨0/, "");
          return text2;
        });
        showdown2.subParser("tables", function(text2, options, globals) {
          "use strict";
          if (!options.tables) {
            return text2;
          }
          var tableRgx = /^ {0,3}\|?.+\|.+\n {0,3}\|?[ \t]*:?[ \t]*(?:[-=]){2,}[ \t]*:?[ \t]*\|[ \t]*:?[ \t]*(?:[-=]){2,}[\s\S]+?(?:\n\n|¨0)/gm, singeColTblRgx = /^ {0,3}\|.+\|[ \t]*\n {0,3}\|[ \t]*:?[ \t]*(?:[-=]){2,}[ \t]*:?[ \t]*\|[ \t]*\n( {0,3}\|.+\|[ \t]*\n)*(?:\n|¨0)/gm;
          function parseStyles(sLine) {
            if (/^:[ \t]*--*$/.test(sLine)) {
              return ' style="text-align:left;"';
            } else if (/^--*[ \t]*:[ \t]*$/.test(sLine)) {
              return ' style="text-align:right;"';
            } else if (/^:[ \t]*--*[ \t]*:$/.test(sLine)) {
              return ' style="text-align:center;"';
            } else {
              return "";
            }
          }
          function parseHeaders(header, style) {
            var id = "";
            header = header.trim();
            if (options.tablesHeaderId || options.tableHeaderId) {
              id = ' id="' + header.replace(/ /g, "_").toLowerCase() + '"';
            }
            header = showdown2.subParser("spanGamut")(header, options, globals);
            return "<th" + id + style + ">" + header + "</th>\n";
          }
          function parseCells(cell, style) {
            var subText = showdown2.subParser("spanGamut")(cell, options, globals);
            return "<td" + style + ">" + subText + "</td>\n";
          }
          function buildTable(headers, cells) {
            var tb = "<table>\n<thead>\n<tr>\n", tblLgn = headers.length;
            for (var i2 = 0; i2 < tblLgn; ++i2) {
              tb += headers[i2];
            }
            tb += "</tr>\n</thead>\n<tbody>\n";
            for (i2 = 0; i2 < cells.length; ++i2) {
              tb += "<tr>\n";
              for (var ii = 0; ii < tblLgn; ++ii) {
                tb += cells[i2][ii];
              }
              tb += "</tr>\n";
            }
            tb += "</tbody>\n</table>\n";
            return tb;
          }
          function parseTable(rawTable) {
            var i2, tableLines = rawTable.split("\n");
            for (i2 = 0; i2 < tableLines.length; ++i2) {
              if (/^ {0,3}\|/.test(tableLines[i2])) {
                tableLines[i2] = tableLines[i2].replace(/^ {0,3}\|/, "");
              }
              if (/\|[ \t]*$/.test(tableLines[i2])) {
                tableLines[i2] = tableLines[i2].replace(/\|[ \t]*$/, "");
              }
              tableLines[i2] = showdown2.subParser("codeSpans")(tableLines[i2], options, globals);
            }
            var rawHeaders = tableLines[0].split("|").map(function(s2) {
              return s2.trim();
            }), rawStyles = tableLines[1].split("|").map(function(s2) {
              return s2.trim();
            }), rawCells = [], headers = [], styles = [], cells = [];
            tableLines.shift();
            tableLines.shift();
            for (i2 = 0; i2 < tableLines.length; ++i2) {
              if (tableLines[i2].trim() === "") {
                continue;
              }
              rawCells.push(
                tableLines[i2].split("|").map(function(s2) {
                  return s2.trim();
                })
              );
            }
            if (rawHeaders.length < rawStyles.length) {
              return rawTable;
            }
            for (i2 = 0; i2 < rawStyles.length; ++i2) {
              styles.push(parseStyles(rawStyles[i2]));
            }
            for (i2 = 0; i2 < rawHeaders.length; ++i2) {
              if (showdown2.helper.isUndefined(styles[i2])) {
                styles[i2] = "";
              }
              headers.push(parseHeaders(rawHeaders[i2], styles[i2]));
            }
            for (i2 = 0; i2 < rawCells.length; ++i2) {
              var row = [];
              for (var ii = 0; ii < headers.length; ++ii) {
                if (showdown2.helper.isUndefined(rawCells[i2][ii])) {
                }
                row.push(parseCells(rawCells[i2][ii], styles[ii]));
              }
              cells.push(row);
            }
            return buildTable(headers, cells);
          }
          text2 = globals.converter._dispatch("tables.before", text2, options, globals);
          text2 = text2.replace(/\\(\|)/g, showdown2.helper.escapeCharactersCallback);
          text2 = text2.replace(tableRgx, parseTable);
          text2 = text2.replace(singeColTblRgx, parseTable);
          text2 = globals.converter._dispatch("tables.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("underline", function(text2, options, globals) {
          "use strict";
          if (!options.underline) {
            return text2;
          }
          text2 = globals.converter._dispatch("underline.before", text2, options, globals);
          if (options.literalMidWordUnderscores) {
            text2 = text2.replace(/\b___(\S[\s\S]*?)___\b/g, function(wm, txt) {
              return "<u>" + txt + "</u>";
            });
            text2 = text2.replace(/\b__(\S[\s\S]*?)__\b/g, function(wm, txt) {
              return "<u>" + txt + "</u>";
            });
          } else {
            text2 = text2.replace(/___(\S[\s\S]*?)___/g, function(wm, m2) {
              return /\S$/.test(m2) ? "<u>" + m2 + "</u>" : wm;
            });
            text2 = text2.replace(/__(\S[\s\S]*?)__/g, function(wm, m2) {
              return /\S$/.test(m2) ? "<u>" + m2 + "</u>" : wm;
            });
          }
          text2 = text2.replace(/(_)/g, showdown2.helper.escapeCharactersCallback);
          text2 = globals.converter._dispatch("underline.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("unescapeSpecialChars", function(text2, options, globals) {
          "use strict";
          text2 = globals.converter._dispatch("unescapeSpecialChars.before", text2, options, globals);
          text2 = text2.replace(/¨E(\d+)E/g, function(wholeMatch, m1) {
            var charCodeToReplace = parseInt(m1);
            return String.fromCharCode(charCodeToReplace);
          });
          text2 = globals.converter._dispatch("unescapeSpecialChars.after", text2, options, globals);
          return text2;
        });
        showdown2.subParser("makeMarkdown.blockquote", function(node, globals) {
          "use strict";
          var txt = "";
          if (node.hasChildNodes()) {
            var children = node.childNodes, childrenLength = children.length;
            for (var i2 = 0; i2 < childrenLength; ++i2) {
              var innerTxt = showdown2.subParser("makeMarkdown.node")(children[i2], globals);
              if (innerTxt === "") {
                continue;
              }
              txt += innerTxt;
            }
          }
          txt = txt.trim();
          txt = "> " + txt.split("\n").join("\n> ");
          return txt;
        });
        showdown2.subParser("makeMarkdown.codeBlock", function(node, globals) {
          "use strict";
          var lang = node.getAttribute("language"), num = node.getAttribute("precodenum");
          return "```" + lang + "\n" + globals.preList[num] + "\n```";
        });
        showdown2.subParser("makeMarkdown.codeSpan", function(node) {
          "use strict";
          return "`" + node.innerHTML + "`";
        });
        showdown2.subParser("makeMarkdown.emphasis", function(node, globals) {
          "use strict";
          var txt = "";
          if (node.hasChildNodes()) {
            txt += "*";
            var children = node.childNodes, childrenLength = children.length;
            for (var i2 = 0; i2 < childrenLength; ++i2) {
              txt += showdown2.subParser("makeMarkdown.node")(children[i2], globals);
            }
            txt += "*";
          }
          return txt;
        });
        showdown2.subParser("makeMarkdown.header", function(node, globals, headerLevel) {
          "use strict";
          var headerMark = new Array(headerLevel + 1).join("#"), txt = "";
          if (node.hasChildNodes()) {
            txt = headerMark + " ";
            var children = node.childNodes, childrenLength = children.length;
            for (var i2 = 0; i2 < childrenLength; ++i2) {
              txt += showdown2.subParser("makeMarkdown.node")(children[i2], globals);
            }
          }
          return txt;
        });
        showdown2.subParser("makeMarkdown.hr", function() {
          "use strict";
          return "---";
        });
        showdown2.subParser("makeMarkdown.image", function(node) {
          "use strict";
          var txt = "";
          if (node.hasAttribute("src")) {
            txt += "![" + node.getAttribute("alt") + "](";
            txt += "<" + node.getAttribute("src") + ">";
            if (node.hasAttribute("width") && node.hasAttribute("height")) {
              txt += " =" + node.getAttribute("width") + "x" + node.getAttribute("height");
            }
            if (node.hasAttribute("title")) {
              txt += ' "' + node.getAttribute("title") + '"';
            }
            txt += ")";
          }
          return txt;
        });
        showdown2.subParser("makeMarkdown.links", function(node, globals) {
          "use strict";
          var txt = "";
          if (node.hasChildNodes() && node.hasAttribute("href")) {
            var children = node.childNodes, childrenLength = children.length;
            txt = "[";
            for (var i2 = 0; i2 < childrenLength; ++i2) {
              txt += showdown2.subParser("makeMarkdown.node")(children[i2], globals);
            }
            txt += "](";
            txt += "<" + node.getAttribute("href") + ">";
            if (node.hasAttribute("title")) {
              txt += ' "' + node.getAttribute("title") + '"';
            }
            txt += ")";
          }
          return txt;
        });
        showdown2.subParser("makeMarkdown.list", function(node, globals, type) {
          "use strict";
          var txt = "";
          if (!node.hasChildNodes()) {
            return "";
          }
          var listItems = node.childNodes, listItemsLenght = listItems.length, listNum = node.getAttribute("start") || 1;
          for (var i2 = 0; i2 < listItemsLenght; ++i2) {
            if (typeof listItems[i2].tagName === "undefined" || listItems[i2].tagName.toLowerCase() !== "li") {
              continue;
            }
            var bullet = "";
            if (type === "ol") {
              bullet = listNum.toString() + ". ";
            } else {
              bullet = "- ";
            }
            txt += bullet + showdown2.subParser("makeMarkdown.listItem")(listItems[i2], globals);
            ++listNum;
          }
          txt += "\n<!-- -->\n";
          return txt.trim();
        });
        showdown2.subParser("makeMarkdown.listItem", function(node, globals) {
          "use strict";
          var listItemTxt = "";
          var children = node.childNodes, childrenLenght = children.length;
          for (var i2 = 0; i2 < childrenLenght; ++i2) {
            listItemTxt += showdown2.subParser("makeMarkdown.node")(children[i2], globals);
          }
          if (!/\n$/.test(listItemTxt)) {
            listItemTxt += "\n";
          } else {
            listItemTxt = listItemTxt.split("\n").join("\n    ").replace(/^ {4}$/gm, "").replace(/\n\n+/g, "\n\n");
          }
          return listItemTxt;
        });
        showdown2.subParser("makeMarkdown.node", function(node, globals, spansOnly) {
          "use strict";
          spansOnly = spansOnly || false;
          var txt = "";
          if (node.nodeType === 3) {
            return showdown2.subParser("makeMarkdown.txt")(node, globals);
          }
          if (node.nodeType === 8) {
            return "<!--" + node.data + "-->\n\n";
          }
          if (node.nodeType !== 1) {
            return "";
          }
          var tagName = node.tagName.toLowerCase();
          switch (tagName) {
            //
            // BLOCKS
            //
            case "h1":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.header")(node, globals, 1) + "\n\n";
              }
              break;
            case "h2":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.header")(node, globals, 2) + "\n\n";
              }
              break;
            case "h3":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.header")(node, globals, 3) + "\n\n";
              }
              break;
            case "h4":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.header")(node, globals, 4) + "\n\n";
              }
              break;
            case "h5":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.header")(node, globals, 5) + "\n\n";
              }
              break;
            case "h6":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.header")(node, globals, 6) + "\n\n";
              }
              break;
            case "p":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.paragraph")(node, globals) + "\n\n";
              }
              break;
            case "blockquote":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.blockquote")(node, globals) + "\n\n";
              }
              break;
            case "hr":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.hr")(node, globals) + "\n\n";
              }
              break;
            case "ol":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.list")(node, globals, "ol") + "\n\n";
              }
              break;
            case "ul":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.list")(node, globals, "ul") + "\n\n";
              }
              break;
            case "precode":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.codeBlock")(node, globals) + "\n\n";
              }
              break;
            case "pre":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.pre")(node, globals) + "\n\n";
              }
              break;
            case "table":
              if (!spansOnly) {
                txt = showdown2.subParser("makeMarkdown.table")(node, globals) + "\n\n";
              }
              break;
            //
            // SPANS
            //
            case "code":
              txt = showdown2.subParser("makeMarkdown.codeSpan")(node, globals);
              break;
            case "em":
            case "i":
              txt = showdown2.subParser("makeMarkdown.emphasis")(node, globals);
              break;
            case "strong":
            case "b":
              txt = showdown2.subParser("makeMarkdown.strong")(node, globals);
              break;
            case "del":
              txt = showdown2.subParser("makeMarkdown.strikethrough")(node, globals);
              break;
            case "a":
              txt = showdown2.subParser("makeMarkdown.links")(node, globals);
              break;
            case "img":
              txt = showdown2.subParser("makeMarkdown.image")(node, globals);
              break;
            default:
              txt = node.outerHTML + "\n\n";
          }
          return txt;
        });
        showdown2.subParser("makeMarkdown.paragraph", function(node, globals) {
          "use strict";
          var txt = "";
          if (node.hasChildNodes()) {
            var children = node.childNodes, childrenLength = children.length;
            for (var i2 = 0; i2 < childrenLength; ++i2) {
              txt += showdown2.subParser("makeMarkdown.node")(children[i2], globals);
            }
          }
          txt = txt.trim();
          return txt;
        });
        showdown2.subParser("makeMarkdown.pre", function(node, globals) {
          "use strict";
          var num = node.getAttribute("prenum");
          return "<pre>" + globals.preList[num] + "</pre>";
        });
        showdown2.subParser("makeMarkdown.strikethrough", function(node, globals) {
          "use strict";
          var txt = "";
          if (node.hasChildNodes()) {
            txt += "~~";
            var children = node.childNodes, childrenLength = children.length;
            for (var i2 = 0; i2 < childrenLength; ++i2) {
              txt += showdown2.subParser("makeMarkdown.node")(children[i2], globals);
            }
            txt += "~~";
          }
          return txt;
        });
        showdown2.subParser("makeMarkdown.strong", function(node, globals) {
          "use strict";
          var txt = "";
          if (node.hasChildNodes()) {
            txt += "**";
            var children = node.childNodes, childrenLength = children.length;
            for (var i2 = 0; i2 < childrenLength; ++i2) {
              txt += showdown2.subParser("makeMarkdown.node")(children[i2], globals);
            }
            txt += "**";
          }
          return txt;
        });
        showdown2.subParser("makeMarkdown.table", function(node, globals) {
          "use strict";
          var txt = "", tableArray = [[], []], headings = node.querySelectorAll("thead>tr>th"), rows = node.querySelectorAll("tbody>tr"), i2, ii;
          for (i2 = 0; i2 < headings.length; ++i2) {
            var headContent = showdown2.subParser("makeMarkdown.tableCell")(headings[i2], globals), allign = "---";
            if (headings[i2].hasAttribute("style")) {
              var style = headings[i2].getAttribute("style").toLowerCase().replace(/\s/g, "");
              switch (style) {
                case "text-align:left;":
                  allign = ":---";
                  break;
                case "text-align:right;":
                  allign = "---:";
                  break;
                case "text-align:center;":
                  allign = ":---:";
                  break;
              }
            }
            tableArray[0][i2] = headContent.trim();
            tableArray[1][i2] = allign;
          }
          for (i2 = 0; i2 < rows.length; ++i2) {
            var r2 = tableArray.push([]) - 1, cols = rows[i2].getElementsByTagName("td");
            for (ii = 0; ii < headings.length; ++ii) {
              var cellContent = " ";
              if (typeof cols[ii] !== "undefined") {
                cellContent = showdown2.subParser("makeMarkdown.tableCell")(cols[ii], globals);
              }
              tableArray[r2].push(cellContent);
            }
          }
          var cellSpacesCount = 3;
          for (i2 = 0; i2 < tableArray.length; ++i2) {
            for (ii = 0; ii < tableArray[i2].length; ++ii) {
              var strLen = tableArray[i2][ii].length;
              if (strLen > cellSpacesCount) {
                cellSpacesCount = strLen;
              }
            }
          }
          for (i2 = 0; i2 < tableArray.length; ++i2) {
            for (ii = 0; ii < tableArray[i2].length; ++ii) {
              if (i2 === 1) {
                if (tableArray[i2][ii].slice(-1) === ":") {
                  tableArray[i2][ii] = showdown2.helper.padEnd(tableArray[i2][ii].slice(-1), cellSpacesCount - 1, "-") + ":";
                } else {
                  tableArray[i2][ii] = showdown2.helper.padEnd(tableArray[i2][ii], cellSpacesCount, "-");
                }
              } else {
                tableArray[i2][ii] = showdown2.helper.padEnd(tableArray[i2][ii], cellSpacesCount);
              }
            }
            txt += "| " + tableArray[i2].join(" | ") + " |\n";
          }
          return txt.trim();
        });
        showdown2.subParser("makeMarkdown.tableCell", function(node, globals) {
          "use strict";
          var txt = "";
          if (!node.hasChildNodes()) {
            return "";
          }
          var children = node.childNodes, childrenLength = children.length;
          for (var i2 = 0; i2 < childrenLength; ++i2) {
            txt += showdown2.subParser("makeMarkdown.node")(children[i2], globals, true);
          }
          return txt.trim();
        });
        showdown2.subParser("makeMarkdown.txt", function(node) {
          "use strict";
          var txt = node.nodeValue;
          txt = txt.replace(/ +/g, " ");
          txt = txt.replace(/¨NBSP;/g, " ");
          txt = showdown2.helper.unescapeHTMLEntities(txt);
          txt = txt.replace(/([*_~|`])/g, "\\$1");
          txt = txt.replace(/^(\s*)>/g, "\\$1>");
          txt = txt.replace(/^#/gm, "\\#");
          txt = txt.replace(/^(\s*)([-=]{3,})(\s*)$/, "$1\\$2$3");
          txt = txt.replace(/^( {0,3}\d+)\./gm, "$1\\.");
          txt = txt.replace(/^( {0,3})([+-])/gm, "$1\\$2");
          txt = txt.replace(/]([\s]*)\(/g, "\\]$1\\(");
          txt = txt.replace(/^ {0,3}\[([\S \t]*?)]:/gm, "\\[$1]:");
          return txt;
        });
        var root = this;
        if (typeof define === "function" && define.amd) {
          define(function() {
            "use strict";
            return showdown2;
          });
        } else if (typeof module !== "undefined" && module.exports) {
          module.exports = showdown2;
        } else {
          root.showdown = showdown2;
        }
      }).call(exports);
    }
  });

  // packages/blocks/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    __EXPERIMENTAL_ELEMENTS: () => __EXPERIMENTAL_ELEMENTS,
    __EXPERIMENTAL_PATHS_WITH_OVERRIDE: () => __EXPERIMENTAL_PATHS_WITH_OVERRIDE,
    __EXPERIMENTAL_STYLE_PROPERTY: () => __EXPERIMENTAL_STYLE_PROPERTY,
    __experimentalCloneSanitizedBlock: () => __experimentalCloneSanitizedBlock,
    __experimentalGetAccessibleBlockLabel: () => getAccessibleBlockLabel,
    __experimentalGetBlockAttributesNamesByRole: () => __experimentalGetBlockAttributesNamesByRole,
    __experimentalGetBlockLabel: () => getBlockLabel,
    __experimentalSanitizeBlockAttributes: () => __experimentalSanitizeBlockAttributes,
    __unstableGetBlockProps: () => getBlockProps,
    __unstableGetInnerBlocksProps: () => getInnerBlocksProps,
    __unstableSerializeAndClean: () => __unstableSerializeAndClean,
    children: () => children_default,
    cloneBlock: () => cloneBlock,
    createBlock: () => createBlock,
    createBlocksFromInnerBlocksTemplate: () => createBlocksFromInnerBlocksTemplate,
    doBlocksMatchTemplate: () => doBlocksMatchTemplate,
    findTransform: () => findTransform,
    getBlockAttributes: () => getBlockAttributes,
    getBlockAttributesNamesByRole: () => getBlockAttributesNamesByRole,
    getBlockBindingsSource: () => getBlockBindingsSource,
    getBlockBindingsSources: () => getBlockBindingsSources,
    getBlockContent: () => getBlockInnerHTML,
    getBlockDefaultClassName: () => getBlockDefaultClassName,
    getBlockFromExample: () => getBlockFromExample,
    getBlockMenuDefaultClassName: () => getBlockMenuDefaultClassName,
    getBlockSupport: () => getBlockSupport,
    getBlockTransforms: () => getBlockTransforms,
    getBlockType: () => getBlockType,
    getBlockTypes: () => getBlockTypes,
    getBlockVariations: () => getBlockVariations,
    getCategories: () => getCategories2,
    getChildBlockNames: () => getChildBlockNames,
    getDefaultBlockName: () => getDefaultBlockName,
    getFreeformContentHandlerName: () => getFreeformContentHandlerName,
    getGroupingBlockName: () => getGroupingBlockName,
    getPhrasingContentSchema: () => deprecatedGetPhrasingContentSchema,
    getPossibleBlockTransformations: () => getPossibleBlockTransformations,
    getSaveContent: () => getSaveContent,
    getSaveElement: () => getSaveElement,
    getUnregisteredTypeHandlerName: () => getUnregisteredTypeHandlerName,
    hasBlockSupport: () => hasBlockSupport,
    hasChildBlocks: () => hasChildBlocks,
    hasChildBlocksWithInserterSupport: () => hasChildBlocksWithInserterSupport,
    isReusableBlock: () => isReusableBlock,
    isTemplatePart: () => isTemplatePart,
    isUnmodifiedBlock: () => isUnmodifiedBlock,
    isUnmodifiedDefaultBlock: () => isUnmodifiedDefaultBlock,
    isValidBlockContent: () => isValidBlockContent,
    isValidIcon: () => isValidIcon,
    node: () => node_default,
    normalizeIconObject: () => normalizeIconObject,
    parse: () => parse2,
    parseWithAttributeSchema: () => parseWithAttributeSchema,
    pasteHandler: () => pasteHandler,
    privateApis: () => privateApis,
    rawHandler: () => rawHandler,
    registerBlockBindingsSource: () => registerBlockBindingsSource,
    registerBlockCollection: () => registerBlockCollection,
    registerBlockStyle: () => registerBlockStyle,
    registerBlockType: () => registerBlockType,
    registerBlockVariation: () => registerBlockVariation,
    serialize: () => serialize,
    serializeRawBlock: () => serializeRawBlock,
    setCategories: () => setCategories2,
    setDefaultBlockName: () => setDefaultBlockName,
    setFreeformContentHandlerName: () => setFreeformContentHandlerName,
    setGroupingBlockName: () => setGroupingBlockName,
    setUnregisteredTypeHandlerName: () => setUnregisteredTypeHandlerName,
    store: () => store,
    switchToBlockType: () => switchToBlockType,
    synchronizeBlocksWithTemplate: () => synchronizeBlocksWithTemplate,
    unregisterBlockBindingsSource: () => unregisterBlockBindingsSource,
    unregisterBlockStyle: () => unregisterBlockStyle,
    unregisterBlockType: () => unregisterBlockType,
    unregisterBlockVariation: () => unregisterBlockVariation,
    unstable__bootstrapServerSideBlockDefinitions: () => unstable__bootstrapServerSideBlockDefinitions,
    updateCategory: () => updateCategory2,
    validateBlock: () => validateBlock,
    withBlockContentContext: () => withBlockContentContext
  });

  // packages/blocks/build-module/store/index.js
  var import_data5 = __toESM(require_data());

  // node_modules/tslib/tslib.es6.mjs
  var __assign = function() {
    __assign = Object.assign || function __assign2(t3) {
      for (var s2, i2 = 1, n2 = arguments.length; i2 < n2; i2++) {
        s2 = arguments[i2];
        for (var p2 in s2) if (Object.prototype.hasOwnProperty.call(s2, p2)) t3[p2] = s2[p2];
      }
      return t3;
    };
    return __assign.apply(this, arguments);
  };

  // node_modules/lower-case/dist.es2015/index.js
  function lowerCase(str) {
    return str.toLowerCase();
  }

  // node_modules/no-case/dist.es2015/index.js
  var DEFAULT_SPLIT_REGEXP = [/([a-z0-9])([A-Z])/g, /([A-Z])([A-Z][a-z])/g];
  var DEFAULT_STRIP_REGEXP = /[^A-Z0-9]+/gi;
  function noCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    var _a = options.splitRegexp, splitRegexp = _a === void 0 ? DEFAULT_SPLIT_REGEXP : _a, _b = options.stripRegexp, stripRegexp = _b === void 0 ? DEFAULT_STRIP_REGEXP : _b, _c = options.transform, transform = _c === void 0 ? lowerCase : _c, _d = options.delimiter, delimiter = _d === void 0 ? " " : _d;
    var result = replace(replace(input, splitRegexp, "$1\0$2"), stripRegexp, "\0");
    var start = 0;
    var end = result.length;
    while (result.charAt(start) === "\0")
      start++;
    while (result.charAt(end - 1) === "\0")
      end--;
    return result.slice(start, end).split("\0").map(transform).join(delimiter);
  }
  function replace(input, re, value) {
    if (re instanceof RegExp)
      return input.replace(re, value);
    return re.reduce(function(input2, re2) {
      return input2.replace(re2, value);
    }, input);
  }

  // node_modules/pascal-case/dist.es2015/index.js
  function pascalCaseTransform(input, index) {
    var firstChar = input.charAt(0);
    var lowerChars = input.substr(1).toLowerCase();
    if (index > 0 && firstChar >= "0" && firstChar <= "9") {
      return "_" + firstChar + lowerChars;
    }
    return "" + firstChar.toUpperCase() + lowerChars;
  }
  function pascalCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return noCase(input, __assign({ delimiter: "", transform: pascalCaseTransform }, options));
  }

  // node_modules/camel-case/dist.es2015/index.js
  function camelCaseTransform(input, index) {
    if (index === 0)
      return input.toLowerCase();
    return pascalCaseTransform(input, index);
  }
  function camelCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return pascalCase(input, __assign({ transform: camelCaseTransform }, options));
  }

  // packages/blocks/build-module/store/reducer.js
  var import_data2 = __toESM(require_data());
  var import_i18n3 = __toESM(require_i18n());

  // node_modules/colord/index.mjs
  var r = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) };
  var t = function(r2) {
    return "string" == typeof r2 ? r2.length > 0 : "number" == typeof r2;
  };
  var n = function(r2, t3, n2) {
    return void 0 === t3 && (t3 = 0), void 0 === n2 && (n2 = Math.pow(10, t3)), Math.round(n2 * r2) / n2 + 0;
  };
  var e = function(r2, t3, n2) {
    return void 0 === t3 && (t3 = 0), void 0 === n2 && (n2 = 1), r2 > n2 ? n2 : r2 > t3 ? r2 : t3;
  };
  var u = function(r2) {
    return (r2 = isFinite(r2) ? r2 % 360 : 0) > 0 ? r2 : r2 + 360;
  };
  var a = function(r2) {
    return { r: e(r2.r, 0, 255), g: e(r2.g, 0, 255), b: e(r2.b, 0, 255), a: e(r2.a) };
  };
  var o = function(r2) {
    return { r: n(r2.r), g: n(r2.g), b: n(r2.b), a: n(r2.a, 3) };
  };
  var i = /^#([0-9a-f]{3,8})$/i;
  var s = function(r2) {
    var t3 = r2.toString(16);
    return t3.length < 2 ? "0" + t3 : t3;
  };
  var h = function(r2) {
    var t3 = r2.r, n2 = r2.g, e2 = r2.b, u2 = r2.a, a2 = Math.max(t3, n2, e2), o3 = a2 - Math.min(t3, n2, e2), i2 = o3 ? a2 === t3 ? (n2 - e2) / o3 : a2 === n2 ? 2 + (e2 - t3) / o3 : 4 + (t3 - n2) / o3 : 0;
    return { h: 60 * (i2 < 0 ? i2 + 6 : i2), s: a2 ? o3 / a2 * 100 : 0, v: a2 / 255 * 100, a: u2 };
  };
  var b = function(r2) {
    var t3 = r2.h, n2 = r2.s, e2 = r2.v, u2 = r2.a;
    t3 = t3 / 360 * 6, n2 /= 100, e2 /= 100;
    var a2 = Math.floor(t3), o3 = e2 * (1 - n2), i2 = e2 * (1 - (t3 - a2) * n2), s2 = e2 * (1 - (1 - t3 + a2) * n2), h2 = a2 % 6;
    return { r: 255 * [e2, i2, o3, o3, s2, e2][h2], g: 255 * [s2, e2, e2, i2, o3, o3][h2], b: 255 * [o3, o3, s2, e2, e2, i2][h2], a: u2 };
  };
  var g = function(r2) {
    return { h: u(r2.h), s: e(r2.s, 0, 100), l: e(r2.l, 0, 100), a: e(r2.a) };
  };
  var d = function(r2) {
    return { h: n(r2.h), s: n(r2.s), l: n(r2.l), a: n(r2.a, 3) };
  };
  var f = function(r2) {
    return b((n2 = (t3 = r2).s, { h: t3.h, s: (n2 *= ((e2 = t3.l) < 50 ? e2 : 100 - e2) / 100) > 0 ? 2 * n2 / (e2 + n2) * 100 : 0, v: e2 + n2, a: t3.a }));
    var t3, n2, e2;
  };
  var c = function(r2) {
    return { h: (t3 = h(r2)).h, s: (u2 = (200 - (n2 = t3.s)) * (e2 = t3.v) / 100) > 0 && u2 < 200 ? n2 * e2 / 100 / (u2 <= 100 ? u2 : 200 - u2) * 100 : 0, l: u2 / 2, a: t3.a };
    var t3, n2, e2, u2;
  };
  var l = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
  var p = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
  var v = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
  var m = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
  var y = { string: [[function(r2) {
    var t3 = i.exec(r2);
    return t3 ? (r2 = t3[1]).length <= 4 ? { r: parseInt(r2[0] + r2[0], 16), g: parseInt(r2[1] + r2[1], 16), b: parseInt(r2[2] + r2[2], 16), a: 4 === r2.length ? n(parseInt(r2[3] + r2[3], 16) / 255, 2) : 1 } : 6 === r2.length || 8 === r2.length ? { r: parseInt(r2.substr(0, 2), 16), g: parseInt(r2.substr(2, 2), 16), b: parseInt(r2.substr(4, 2), 16), a: 8 === r2.length ? n(parseInt(r2.substr(6, 2), 16) / 255, 2) : 1 } : null : null;
  }, "hex"], [function(r2) {
    var t3 = v.exec(r2) || m.exec(r2);
    return t3 ? t3[2] !== t3[4] || t3[4] !== t3[6] ? null : a({ r: Number(t3[1]) / (t3[2] ? 100 / 255 : 1), g: Number(t3[3]) / (t3[4] ? 100 / 255 : 1), b: Number(t3[5]) / (t3[6] ? 100 / 255 : 1), a: void 0 === t3[7] ? 1 : Number(t3[7]) / (t3[8] ? 100 : 1) }) : null;
  }, "rgb"], [function(t3) {
    var n2 = l.exec(t3) || p.exec(t3);
    if (!n2) return null;
    var e2, u2, a2 = g({ h: (e2 = n2[1], u2 = n2[2], void 0 === u2 && (u2 = "deg"), Number(e2) * (r[u2] || 1)), s: Number(n2[3]), l: Number(n2[4]), a: void 0 === n2[5] ? 1 : Number(n2[5]) / (n2[6] ? 100 : 1) });
    return f(a2);
  }, "hsl"]], object: [[function(r2) {
    var n2 = r2.r, e2 = r2.g, u2 = r2.b, o3 = r2.a, i2 = void 0 === o3 ? 1 : o3;
    return t(n2) && t(e2) && t(u2) ? a({ r: Number(n2), g: Number(e2), b: Number(u2), a: Number(i2) }) : null;
  }, "rgb"], [function(r2) {
    var n2 = r2.h, e2 = r2.s, u2 = r2.l, a2 = r2.a, o3 = void 0 === a2 ? 1 : a2;
    if (!t(n2) || !t(e2) || !t(u2)) return null;
    var i2 = g({ h: Number(n2), s: Number(e2), l: Number(u2), a: Number(o3) });
    return f(i2);
  }, "hsl"], [function(r2) {
    var n2 = r2.h, a2 = r2.s, o3 = r2.v, i2 = r2.a, s2 = void 0 === i2 ? 1 : i2;
    if (!t(n2) || !t(a2) || !t(o3)) return null;
    var h2 = (function(r3) {
      return { h: u(r3.h), s: e(r3.s, 0, 100), v: e(r3.v, 0, 100), a: e(r3.a) };
    })({ h: Number(n2), s: Number(a2), v: Number(o3), a: Number(s2) });
    return b(h2);
  }, "hsv"]] };
  var N = function(r2, t3) {
    for (var n2 = 0; n2 < t3.length; n2++) {
      var e2 = t3[n2][0](r2);
      if (e2) return [e2, t3[n2][1]];
    }
    return [null, void 0];
  };
  var x = function(r2) {
    return "string" == typeof r2 ? N(r2.trim(), y.string) : "object" == typeof r2 && null !== r2 ? N(r2, y.object) : [null, void 0];
  };
  var M = function(r2, t3) {
    var n2 = c(r2);
    return { h: n2.h, s: e(n2.s + 100 * t3, 0, 100), l: n2.l, a: n2.a };
  };
  var H = function(r2) {
    return (299 * r2.r + 587 * r2.g + 114 * r2.b) / 1e3 / 255;
  };
  var $ = function(r2, t3) {
    var n2 = c(r2);
    return { h: n2.h, s: n2.s, l: e(n2.l + 100 * t3, 0, 100), a: n2.a };
  };
  var j = (function() {
    function r2(r3) {
      this.parsed = x(r3)[0], this.rgba = this.parsed || { r: 0, g: 0, b: 0, a: 1 };
    }
    return r2.prototype.isValid = function() {
      return null !== this.parsed;
    }, r2.prototype.brightness = function() {
      return n(H(this.rgba), 2);
    }, r2.prototype.isDark = function() {
      return H(this.rgba) < 0.5;
    }, r2.prototype.isLight = function() {
      return H(this.rgba) >= 0.5;
    }, r2.prototype.toHex = function() {
      return r3 = o(this.rgba), t3 = r3.r, e2 = r3.g, u2 = r3.b, i2 = (a2 = r3.a) < 1 ? s(n(255 * a2)) : "", "#" + s(t3) + s(e2) + s(u2) + i2;
      var r3, t3, e2, u2, a2, i2;
    }, r2.prototype.toRgb = function() {
      return o(this.rgba);
    }, r2.prototype.toRgbString = function() {
      return r3 = o(this.rgba), t3 = r3.r, n2 = r3.g, e2 = r3.b, (u2 = r3.a) < 1 ? "rgba(" + t3 + ", " + n2 + ", " + e2 + ", " + u2 + ")" : "rgb(" + t3 + ", " + n2 + ", " + e2 + ")";
      var r3, t3, n2, e2, u2;
    }, r2.prototype.toHsl = function() {
      return d(c(this.rgba));
    }, r2.prototype.toHslString = function() {
      return r3 = d(c(this.rgba)), t3 = r3.h, n2 = r3.s, e2 = r3.l, (u2 = r3.a) < 1 ? "hsla(" + t3 + ", " + n2 + "%, " + e2 + "%, " + u2 + ")" : "hsl(" + t3 + ", " + n2 + "%, " + e2 + "%)";
      var r3, t3, n2, e2, u2;
    }, r2.prototype.toHsv = function() {
      return r3 = h(this.rgba), { h: n(r3.h), s: n(r3.s), v: n(r3.v), a: n(r3.a, 3) };
      var r3;
    }, r2.prototype.invert = function() {
      return w({ r: 255 - (r3 = this.rgba).r, g: 255 - r3.g, b: 255 - r3.b, a: r3.a });
      var r3;
    }, r2.prototype.saturate = function(r3) {
      return void 0 === r3 && (r3 = 0.1), w(M(this.rgba, r3));
    }, r2.prototype.desaturate = function(r3) {
      return void 0 === r3 && (r3 = 0.1), w(M(this.rgba, -r3));
    }, r2.prototype.grayscale = function() {
      return w(M(this.rgba, -1));
    }, r2.prototype.lighten = function(r3) {
      return void 0 === r3 && (r3 = 0.1), w($(this.rgba, r3));
    }, r2.prototype.darken = function(r3) {
      return void 0 === r3 && (r3 = 0.1), w($(this.rgba, -r3));
    }, r2.prototype.rotate = function(r3) {
      return void 0 === r3 && (r3 = 15), this.hue(this.hue() + r3);
    }, r2.prototype.alpha = function(r3) {
      return "number" == typeof r3 ? w({ r: (t3 = this.rgba).r, g: t3.g, b: t3.b, a: r3 }) : n(this.rgba.a, 3);
      var t3;
    }, r2.prototype.hue = function(r3) {
      var t3 = c(this.rgba);
      return "number" == typeof r3 ? w({ h: r3, s: t3.s, l: t3.l, a: t3.a }) : n(t3.h);
    }, r2.prototype.isEqual = function(r3) {
      return this.toHex() === w(r3).toHex();
    }, r2;
  })();
  var w = function(r2) {
    return r2 instanceof j ? r2 : new j(r2);
  };
  var S = [];
  var k = function(r2) {
    r2.forEach(function(r3) {
      S.indexOf(r3) < 0 && (r3(j, y), S.push(r3));
    });
  };

  // node_modules/colord/plugins/names.mjs
  function names_default(e2, f2) {
    var a2 = { white: "#ffffff", bisque: "#ffe4c4", blue: "#0000ff", cadetblue: "#5f9ea0", chartreuse: "#7fff00", chocolate: "#d2691e", coral: "#ff7f50", antiquewhite: "#faebd7", aqua: "#00ffff", azure: "#f0ffff", whitesmoke: "#f5f5f5", papayawhip: "#ffefd5", plum: "#dda0dd", blanchedalmond: "#ffebcd", black: "#000000", gold: "#ffd700", goldenrod: "#daa520", gainsboro: "#dcdcdc", cornsilk: "#fff8dc", cornflowerblue: "#6495ed", burlywood: "#deb887", aquamarine: "#7fffd4", beige: "#f5f5dc", crimson: "#dc143c", cyan: "#00ffff", darkblue: "#00008b", darkcyan: "#008b8b", darkgoldenrod: "#b8860b", darkkhaki: "#bdb76b", darkgray: "#a9a9a9", darkgreen: "#006400", darkgrey: "#a9a9a9", peachpuff: "#ffdab9", darkmagenta: "#8b008b", darkred: "#8b0000", darkorchid: "#9932cc", darkorange: "#ff8c00", darkslateblue: "#483d8b", gray: "#808080", darkslategray: "#2f4f4f", darkslategrey: "#2f4f4f", deeppink: "#ff1493", deepskyblue: "#00bfff", wheat: "#f5deb3", firebrick: "#b22222", floralwhite: "#fffaf0", ghostwhite: "#f8f8ff", darkviolet: "#9400d3", magenta: "#ff00ff", green: "#008000", dodgerblue: "#1e90ff", grey: "#808080", honeydew: "#f0fff0", hotpink: "#ff69b4", blueviolet: "#8a2be2", forestgreen: "#228b22", lawngreen: "#7cfc00", indianred: "#cd5c5c", indigo: "#4b0082", fuchsia: "#ff00ff", brown: "#a52a2a", maroon: "#800000", mediumblue: "#0000cd", lightcoral: "#f08080", darkturquoise: "#00ced1", lightcyan: "#e0ffff", ivory: "#fffff0", lightyellow: "#ffffe0", lightsalmon: "#ffa07a", lightseagreen: "#20b2aa", linen: "#faf0e6", mediumaquamarine: "#66cdaa", lemonchiffon: "#fffacd", lime: "#00ff00", khaki: "#f0e68c", mediumseagreen: "#3cb371", limegreen: "#32cd32", mediumspringgreen: "#00fa9a", lightskyblue: "#87cefa", lightblue: "#add8e6", midnightblue: "#191970", lightpink: "#ffb6c1", mistyrose: "#ffe4e1", moccasin: "#ffe4b5", mintcream: "#f5fffa", lightslategray: "#778899", lightslategrey: "#778899", navajowhite: "#ffdead", navy: "#000080", mediumvioletred: "#c71585", powderblue: "#b0e0e6", palegoldenrod: "#eee8aa", oldlace: "#fdf5e6", paleturquoise: "#afeeee", mediumturquoise: "#48d1cc", mediumorchid: "#ba55d3", rebeccapurple: "#663399", lightsteelblue: "#b0c4de", mediumslateblue: "#7b68ee", thistle: "#d8bfd8", tan: "#d2b48c", orchid: "#da70d6", mediumpurple: "#9370db", purple: "#800080", pink: "#ffc0cb", skyblue: "#87ceeb", springgreen: "#00ff7f", palegreen: "#98fb98", red: "#ff0000", yellow: "#ffff00", slateblue: "#6a5acd", lavenderblush: "#fff0f5", peru: "#cd853f", palevioletred: "#db7093", violet: "#ee82ee", teal: "#008080", slategray: "#708090", slategrey: "#708090", aliceblue: "#f0f8ff", darkseagreen: "#8fbc8f", darkolivegreen: "#556b2f", greenyellow: "#adff2f", seagreen: "#2e8b57", seashell: "#fff5ee", tomato: "#ff6347", silver: "#c0c0c0", sienna: "#a0522d", lavender: "#e6e6fa", lightgreen: "#90ee90", orange: "#ffa500", orangered: "#ff4500", steelblue: "#4682b4", royalblue: "#4169e1", turquoise: "#40e0d0", yellowgreen: "#9acd32", salmon: "#fa8072", saddlebrown: "#8b4513", sandybrown: "#f4a460", rosybrown: "#bc8f8f", darksalmon: "#e9967a", lightgoldenrodyellow: "#fafad2", snow: "#fffafa", lightgrey: "#d3d3d3", lightgray: "#d3d3d3", dimgray: "#696969", dimgrey: "#696969", olivedrab: "#6b8e23", olive: "#808000" }, r2 = {};
    for (var d2 in a2) r2[a2[d2]] = d2;
    var l2 = {};
    e2.prototype.toName = function(f3) {
      if (!(this.rgba.a || this.rgba.r || this.rgba.g || this.rgba.b)) return "transparent";
      var d3, i2, n2 = r2[this.toHex()];
      if (n2) return n2;
      if (null == f3 ? void 0 : f3.closest) {
        var o3 = this.toRgb(), t3 = 1 / 0, b2 = "black";
        if (!l2.length) for (var c2 in a2) l2[c2] = new e2(a2[c2]).toRgb();
        for (var g2 in a2) {
          var u2 = (d3 = o3, i2 = l2[g2], Math.pow(d3.r - i2.r, 2) + Math.pow(d3.g - i2.g, 2) + Math.pow(d3.b - i2.b, 2));
          u2 < t3 && (t3 = u2, b2 = g2);
        }
        return b2;
      }
    };
    f2.string.push([function(f3) {
      var r3 = f3.toLowerCase(), d3 = "transparent" === r3 ? "#0000" : a2[r3];
      return d3 ? new e2(d3).toRgb() : null;
    }, "name"]);
  }

  // node_modules/colord/plugins/a11y.mjs
  var o2 = function(o3) {
    var t3 = o3 / 255;
    return t3 < 0.04045 ? t3 / 12.92 : Math.pow((t3 + 0.055) / 1.055, 2.4);
  };
  var t2 = function(t3) {
    return 0.2126 * o2(t3.r) + 0.7152 * o2(t3.g) + 0.0722 * o2(t3.b);
  };
  function a11y_default(o3) {
    o3.prototype.luminance = function() {
      return o4 = t2(this.rgba), void 0 === (r2 = 2) && (r2 = 0), void 0 === n2 && (n2 = Math.pow(10, r2)), Math.round(n2 * o4) / n2 + 0;
      var o4, r2, n2;
    }, o3.prototype.contrast = function(r2) {
      void 0 === r2 && (r2 = "#FFF");
      var n2, a2, i2, e2, v2, u2, d2, c2 = r2 instanceof o3 ? r2 : new o3(r2);
      return e2 = this.rgba, v2 = c2.toRgb(), u2 = t2(e2), d2 = t2(v2), n2 = u2 > d2 ? (u2 + 0.05) / (d2 + 0.05) : (d2 + 0.05) / (u2 + 0.05), void 0 === (a2 = 2) && (a2 = 0), void 0 === i2 && (i2 = Math.pow(10, a2)), Math.floor(i2 * n2) / i2 + 0;
    }, o3.prototype.isReadable = function(o4, t3) {
      return void 0 === o4 && (o4 = "#FFF"), void 0 === t3 && (t3 = {}), this.contrast(o4) >= (e2 = void 0 === (i2 = (r2 = t3).size) ? "normal" : i2, "AAA" === (a2 = void 0 === (n2 = r2.level) ? "AA" : n2) && "normal" === e2 ? 7 : "AA" === a2 && "large" === e2 ? 3 : 4.5);
      var r2, n2, a2, i2, e2;
    };
  }

  // packages/blocks/build-module/api/utils.js
  var import_element = __toESM(require_element());
  var import_i18n2 = __toESM(require_i18n());
  var import_dom = __toESM(require_dom());
  var import_rich_text = __toESM(require_rich_text());
  var import_deprecated = __toESM(require_deprecated());

  // packages/blocks/build-module/api/constants.js
  var BLOCK_ICON_DEFAULT = "block-default";
  var DEPRECATED_ENTRY_KEYS = [
    "attributes",
    "supports",
    "save",
    "migrate",
    "isEligible",
    "apiVersion"
  ];
  var __EXPERIMENTAL_STYLE_PROPERTY = {
    // Kept for back-compatibility purposes.
    "--wp--style--color--link": {
      value: ["color", "link"],
      support: ["color", "link"]
    },
    aspectRatio: {
      value: ["dimensions", "aspectRatio"],
      support: ["dimensions", "aspectRatio"],
      useEngine: true
    },
    background: {
      value: ["color", "gradient"],
      support: ["color", "gradients"],
      useEngine: true
    },
    backgroundColor: {
      value: ["color", "background"],
      support: ["color", "background"],
      requiresOptOut: true,
      useEngine: true
    },
    backgroundImage: {
      value: ["background", "backgroundImage"],
      support: ["background", "backgroundImage"],
      useEngine: true
    },
    backgroundRepeat: {
      value: ["background", "backgroundRepeat"],
      support: ["background", "backgroundRepeat"],
      useEngine: true
    },
    backgroundSize: {
      value: ["background", "backgroundSize"],
      support: ["background", "backgroundSize"],
      useEngine: true
    },
    backgroundPosition: {
      value: ["background", "backgroundPosition"],
      support: ["background", "backgroundPosition"],
      useEngine: true
    },
    borderColor: {
      value: ["border", "color"],
      support: ["__experimentalBorder", "color"],
      useEngine: true
    },
    borderRadius: {
      value: ["border", "radius"],
      support: ["__experimentalBorder", "radius"],
      properties: {
        borderTopLeftRadius: "topLeft",
        borderTopRightRadius: "topRight",
        borderBottomLeftRadius: "bottomLeft",
        borderBottomRightRadius: "bottomRight"
      },
      useEngine: true
    },
    borderStyle: {
      value: ["border", "style"],
      support: ["__experimentalBorder", "style"],
      useEngine: true
    },
    borderWidth: {
      value: ["border", "width"],
      support: ["__experimentalBorder", "width"],
      useEngine: true
    },
    borderTopColor: {
      value: ["border", "top", "color"],
      support: ["__experimentalBorder", "color"],
      useEngine: true
    },
    borderTopStyle: {
      value: ["border", "top", "style"],
      support: ["__experimentalBorder", "style"],
      useEngine: true
    },
    borderTopWidth: {
      value: ["border", "top", "width"],
      support: ["__experimentalBorder", "width"],
      useEngine: true
    },
    borderRightColor: {
      value: ["border", "right", "color"],
      support: ["__experimentalBorder", "color"],
      useEngine: true
    },
    borderRightStyle: {
      value: ["border", "right", "style"],
      support: ["__experimentalBorder", "style"],
      useEngine: true
    },
    borderRightWidth: {
      value: ["border", "right", "width"],
      support: ["__experimentalBorder", "width"],
      useEngine: true
    },
    borderBottomColor: {
      value: ["border", "bottom", "color"],
      support: ["__experimentalBorder", "color"],
      useEngine: true
    },
    borderBottomStyle: {
      value: ["border", "bottom", "style"],
      support: ["__experimentalBorder", "style"],
      useEngine: true
    },
    borderBottomWidth: {
      value: ["border", "bottom", "width"],
      support: ["__experimentalBorder", "width"],
      useEngine: true
    },
    borderLeftColor: {
      value: ["border", "left", "color"],
      support: ["__experimentalBorder", "color"],
      useEngine: true
    },
    borderLeftStyle: {
      value: ["border", "left", "style"],
      support: ["__experimentalBorder", "style"],
      useEngine: true
    },
    borderLeftWidth: {
      value: ["border", "left", "width"],
      support: ["__experimentalBorder", "width"],
      useEngine: true
    },
    color: {
      value: ["color", "text"],
      support: ["color", "text"],
      requiresOptOut: true,
      useEngine: true
    },
    columnCount: {
      value: ["typography", "textColumns"],
      support: ["typography", "textColumns"],
      useEngine: true
    },
    filter: {
      value: ["filter", "duotone"],
      support: ["filter", "duotone"]
    },
    linkColor: {
      value: ["elements", "link", "color", "text"],
      support: ["color", "link"]
    },
    captionColor: {
      value: ["elements", "caption", "color", "text"],
      support: ["color", "caption"]
    },
    buttonColor: {
      value: ["elements", "button", "color", "text"],
      support: ["color", "button"]
    },
    buttonBackgroundColor: {
      value: ["elements", "button", "color", "background"],
      support: ["color", "button"]
    },
    headingColor: {
      value: ["elements", "heading", "color", "text"],
      support: ["color", "heading"]
    },
    headingBackgroundColor: {
      value: ["elements", "heading", "color", "background"],
      support: ["color", "heading"]
    },
    fontFamily: {
      value: ["typography", "fontFamily"],
      support: ["typography", "__experimentalFontFamily"],
      useEngine: true
    },
    fontSize: {
      value: ["typography", "fontSize"],
      support: ["typography", "fontSize"],
      useEngine: true
    },
    fontStyle: {
      value: ["typography", "fontStyle"],
      support: ["typography", "__experimentalFontStyle"],
      useEngine: true
    },
    fontWeight: {
      value: ["typography", "fontWeight"],
      support: ["typography", "__experimentalFontWeight"],
      useEngine: true
    },
    lineHeight: {
      value: ["typography", "lineHeight"],
      support: ["typography", "lineHeight"],
      useEngine: true
    },
    margin: {
      value: ["spacing", "margin"],
      support: ["spacing", "margin"],
      properties: {
        marginTop: "top",
        marginRight: "right",
        marginBottom: "bottom",
        marginLeft: "left"
      },
      useEngine: true
    },
    minHeight: {
      value: ["dimensions", "minHeight"],
      support: ["dimensions", "minHeight"],
      useEngine: true
    },
    height: {
      value: ["dimensions", "height"],
      support: ["dimensions", "height"],
      useEngine: true
    },
    width: {
      value: ["dimensions", "width"],
      support: ["dimensions", "width"],
      useEngine: true
    },
    padding: {
      value: ["spacing", "padding"],
      support: ["spacing", "padding"],
      properties: {
        paddingTop: "top",
        paddingRight: "right",
        paddingBottom: "bottom",
        paddingLeft: "left"
      },
      useEngine: true
    },
    textAlign: {
      value: ["typography", "textAlign"],
      support: ["typography", "textAlign"],
      useEngine: false
    },
    textDecoration: {
      value: ["typography", "textDecoration"],
      support: ["typography", "__experimentalTextDecoration"],
      useEngine: true
    },
    textTransform: {
      value: ["typography", "textTransform"],
      support: ["typography", "__experimentalTextTransform"],
      useEngine: true
    },
    letterSpacing: {
      value: ["typography", "letterSpacing"],
      support: ["typography", "__experimentalLetterSpacing"],
      useEngine: true
    },
    writingMode: {
      value: ["typography", "writingMode"],
      support: ["typography", "__experimentalWritingMode"],
      useEngine: true
    },
    "--wp--style--root--padding": {
      value: ["spacing", "padding"],
      support: ["spacing", "padding"],
      properties: {
        "--wp--style--root--padding-top": "top",
        "--wp--style--root--padding-right": "right",
        "--wp--style--root--padding-bottom": "bottom",
        "--wp--style--root--padding-left": "left"
      },
      rootOnly: true
    }
  };
  var __EXPERIMENTAL_ELEMENTS = {
    link: "a:where(:not(.wp-element-button))",
    heading: "h1, h2, h3, h4, h5, h6",
    h1: "h1",
    h2: "h2",
    h3: "h3",
    h4: "h4",
    h5: "h5",
    h6: "h6",
    button: ".wp-element-button, .wp-block-button__link",
    caption: ".wp-element-caption, .wp-block-audio figcaption, .wp-block-embed figcaption, .wp-block-gallery figcaption, .wp-block-image figcaption, .wp-block-table figcaption, .wp-block-video figcaption",
    cite: "cite",
    select: "select",
    textInput: "textarea, input:where([type=email],[type=number],[type=password],[type=search],[type=tel],[type=text],[type=url])"
  };
  var __EXPERIMENTAL_PATHS_WITH_OVERRIDE = {
    "color.duotone": true,
    "color.gradients": true,
    "color.palette": true,
    "dimensions.aspectRatios": true,
    "typography.fontSizes": true,
    "spacing.spacingSizes": true
  };

  // packages/blocks/build-module/api/registration.js
  var import_data = __toESM(require_data());
  var import_i18n = __toESM(require_i18n());
  var import_warning = __toESM(require_warning());

  // packages/blocks/build-module/api/i18n-block.json
  var i18n_block_default = {
    title: "block title",
    description: "block description",
    keywords: ["block keyword"],
    styles: [
      {
        label: "block style label"
      }
    ],
    variations: [
      {
        title: "block variation title",
        description: "block variation description",
        keywords: ["block variation keyword"]
      }
    ]
  };

  // packages/blocks/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/blocks"
  );

  // packages/blocks/build-module/api/registration.js
  function isObject(object) {
    return object !== null && typeof object === "object";
  }
  function unstable__bootstrapServerSideBlockDefinitions(definitions) {
    const { addBootstrappedBlockType: addBootstrappedBlockType2 } = unlock((0, import_data.dispatch)(store));
    for (const [name, blockType] of Object.entries(definitions)) {
      addBootstrappedBlockType2(name, blockType);
    }
  }
  function getBlockSettingsFromMetadata({ textdomain, ...metadata }) {
    const allowedFields = [
      "apiVersion",
      "title",
      "category",
      "parent",
      "ancestor",
      "icon",
      "description",
      "keywords",
      "attributes",
      "providesContext",
      "usesContext",
      "selectors",
      "supports",
      "styles",
      "example",
      "variations",
      "blockHooks",
      "allowedBlocks"
    ];
    const settings = Object.fromEntries(
      Object.entries(metadata).filter(
        ([key]) => allowedFields.includes(key)
      )
    );
    if (textdomain) {
      Object.keys(i18n_block_default).forEach((key) => {
        if (!settings[key]) {
          return;
        }
        settings[key] = translateBlockSettingUsingI18nSchema(
          i18n_block_default[key],
          settings[key],
          textdomain
        );
      });
    }
    return settings;
  }
  function registerBlockType(blockNameOrMetadata, settings) {
    const name = isObject(blockNameOrMetadata) ? blockNameOrMetadata.name : blockNameOrMetadata;
    if (typeof name !== "string") {
      (0, import_warning.default)("Block names must be strings.");
      return;
    }
    if (!/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/.test(name)) {
      (0, import_warning.default)(
        "Block names must contain a namespace prefix, include only lowercase alphanumeric characters or dashes, and start with a letter. Example: my-plugin/my-custom-block"
      );
      return;
    }
    if ((0, import_data.select)(store).getBlockType(name)) {
      (0, import_warning.default)('Block "' + name + '" is already registered.');
      return;
    }
    const { addBootstrappedBlockType: addBootstrappedBlockType2, addUnprocessedBlockType: addUnprocessedBlockType2 } = unlock(
      (0, import_data.dispatch)(store)
    );
    if (isObject(blockNameOrMetadata)) {
      const metadata = getBlockSettingsFromMetadata(blockNameOrMetadata);
      addBootstrappedBlockType2(name, metadata);
    }
    addUnprocessedBlockType2(name, settings);
    return (0, import_data.select)(store).getBlockType(name);
  }
  function translateBlockSettingUsingI18nSchema(i18nSchema, settingValue, textdomain) {
    if (typeof i18nSchema === "string" && typeof settingValue === "string") {
      return (0, import_i18n._x)(settingValue, i18nSchema, textdomain);
    }
    if (Array.isArray(i18nSchema) && i18nSchema.length && Array.isArray(settingValue)) {
      return settingValue.map(
        (value) => translateBlockSettingUsingI18nSchema(
          i18nSchema[0],
          value,
          textdomain
        )
      );
    }
    if (isObject(i18nSchema) && Object.entries(i18nSchema).length && isObject(settingValue)) {
      return Object.keys(settingValue).reduce((accumulator, key) => {
        if (!i18nSchema[key]) {
          accumulator[key] = settingValue[key];
          return accumulator;
        }
        accumulator[key] = translateBlockSettingUsingI18nSchema(
          i18nSchema[key],
          settingValue[key],
          textdomain
        );
        return accumulator;
      }, {});
    }
    return settingValue;
  }
  function registerBlockCollection(namespace, { title, icon }) {
    (0, import_data.dispatch)(store).addBlockCollection(namespace, title, icon);
  }
  function unregisterBlockType(name) {
    const oldBlock = (0, import_data.select)(store).getBlockType(name);
    if (!oldBlock) {
      (0, import_warning.default)('Block "' + name + '" is not registered.');
      return;
    }
    (0, import_data.dispatch)(store).removeBlockTypes(name);
    return oldBlock;
  }
  function setFreeformContentHandlerName(blockName) {
    (0, import_data.dispatch)(store).setFreeformFallbackBlockName(blockName);
  }
  function getFreeformContentHandlerName() {
    return (0, import_data.select)(store).getFreeformFallbackBlockName();
  }
  function getGroupingBlockName() {
    return (0, import_data.select)(store).getGroupingBlockName();
  }
  function setUnregisteredTypeHandlerName(blockName) {
    (0, import_data.dispatch)(store).setUnregisteredFallbackBlockName(blockName);
  }
  function getUnregisteredTypeHandlerName() {
    return (0, import_data.select)(store).getUnregisteredFallbackBlockName();
  }
  function setDefaultBlockName(name) {
    (0, import_data.dispatch)(store).setDefaultBlockName(name);
  }
  function setGroupingBlockName(name) {
    (0, import_data.dispatch)(store).setGroupingBlockName(name);
  }
  function getDefaultBlockName() {
    return (0, import_data.select)(store).getDefaultBlockName();
  }
  function getBlockType(name) {
    return (0, import_data.select)(store)?.getBlockType(name);
  }
  function getBlockTypes() {
    return (0, import_data.select)(store).getBlockTypes();
  }
  function getBlockSupport(nameOrType, feature, defaultSupports) {
    return (0, import_data.select)(store).getBlockSupport(
      nameOrType,
      feature,
      defaultSupports
    );
  }
  function hasBlockSupport(nameOrType, feature, defaultSupports) {
    return (0, import_data.select)(store).hasBlockSupport(
      nameOrType,
      feature,
      defaultSupports
    );
  }
  function isReusableBlock(blockOrType) {
    return blockOrType?.name === "core/block";
  }
  function isTemplatePart(blockOrType) {
    return blockOrType?.name === "core/template-part";
  }
  var getChildBlockNames = (blockName) => {
    return (0, import_data.select)(store).getChildBlockNames(blockName);
  };
  var hasChildBlocks = (blockName) => {
    return (0, import_data.select)(store).hasChildBlocks(blockName);
  };
  var hasChildBlocksWithInserterSupport = (blockName) => {
    return (0, import_data.select)(store).hasChildBlocksWithInserterSupport(blockName);
  };
  var registerBlockStyle = (blockNames, styleVariation) => {
    (0, import_data.dispatch)(store).addBlockStyles(blockNames, styleVariation);
  };
  var unregisterBlockStyle = (blockName, styleVariationName) => {
    (0, import_data.dispatch)(store).removeBlockStyles(blockName, styleVariationName);
  };
  var getBlockVariations = (blockName, scope) => {
    return (0, import_data.select)(store).getBlockVariations(blockName, scope);
  };
  var registerBlockVariation = (blockName, variation) => {
    if (typeof variation.name !== "string") {
      (0, import_warning.default)("Variation names must be unique strings.");
    }
    (0, import_data.dispatch)(store).addBlockVariations(blockName, variation);
  };
  var unregisterBlockVariation = (blockName, variationName) => {
    (0, import_data.dispatch)(store).removeBlockVariations(blockName, variationName);
  };
  var registerBlockBindingsSource = (source) => {
    const {
      name,
      label,
      usesContext,
      getValues,
      setValues,
      canUserEditValue,
      getFieldsList
    } = source;
    const existingSource = unlock(
      (0, import_data.select)(store)
    ).getBlockBindingsSource(name);
    const serverProps = ["label", "usesContext"];
    for (const prop2 in existingSource) {
      if (!serverProps.includes(prop2) && existingSource[prop2]) {
        (0, import_warning.default)(
          'Block bindings source "' + name + '" is already registered.'
        );
        return;
      }
    }
    if (!name) {
      (0, import_warning.default)("Block bindings source must contain a name.");
      return;
    }
    if (typeof name !== "string") {
      (0, import_warning.default)("Block bindings source name must be a string.");
      return;
    }
    if (/[A-Z]+/.test(name)) {
      (0, import_warning.default)(
        "Block bindings source name must not contain uppercase characters."
      );
      return;
    }
    if (!/^[a-z0-9/-]+$/.test(name)) {
      (0, import_warning.default)(
        "Block bindings source name must contain only valid characters: lowercase characters, hyphens, or digits. Example: my-plugin/my-custom-source."
      );
      return;
    }
    if (!/^[a-z0-9-]+\/[a-z0-9-]+$/.test(name)) {
      (0, import_warning.default)(
        "Block bindings source name must contain a namespace and valid characters. Example: my-plugin/my-custom-source."
      );
      return;
    }
    if (!label && !existingSource?.label) {
      (0, import_warning.default)("Block bindings source must contain a label.");
      return;
    }
    if (label && typeof label !== "string") {
      (0, import_warning.default)("Block bindings source label must be a string.");
      return;
    }
    if (label && existingSource?.label && label !== existingSource?.label) {
      (0, import_warning.default)('Block bindings "' + name + '" source label was overridden.');
    }
    if (usesContext && !Array.isArray(usesContext)) {
      (0, import_warning.default)("Block bindings source usesContext must be an array.");
      return;
    }
    if (getValues && typeof getValues !== "function") {
      (0, import_warning.default)("Block bindings source getValues must be a function.");
      return;
    }
    if (setValues && typeof setValues !== "function") {
      (0, import_warning.default)("Block bindings source setValues must be a function.");
      return;
    }
    if (canUserEditValue && typeof canUserEditValue !== "function") {
      (0, import_warning.default)("Block bindings source canUserEditValue must be a function.");
      return;
    }
    if (getFieldsList && typeof getFieldsList !== "function") {
      (0, import_warning.default)("Block bindings source getFieldsList must be a function.");
      return;
    }
    return unlock((0, import_data.dispatch)(store)).addBlockBindingsSource(source);
  };
  function unregisterBlockBindingsSource(name) {
    const oldSource = getBlockBindingsSource(name);
    if (!oldSource) {
      (0, import_warning.default)('Block bindings source "' + name + '" is not registered.');
      return;
    }
    unlock((0, import_data.dispatch)(store)).removeBlockBindingsSource(name);
  }
  function getBlockBindingsSource(name) {
    return unlock((0, import_data.select)(store)).getBlockBindingsSource(name);
  }
  function getBlockBindingsSources() {
    return unlock((0, import_data.select)(store)).getAllBlockBindingsSources();
  }

  // packages/blocks/build-module/api/utils.js
  k([names_default, a11y_default]);
  var ICON_COLORS = ["#191e23", "#f8f9f9"];
  function isUnmodifiedBlock(block, role) {
    const blockAttributes = getBlockType(block.name)?.attributes ?? {};
    const attributesByRole = role ? Object.entries(blockAttributes).filter(([key, definition]) => {
      if (role === "content" && key === "metadata") {
        return Object.keys(block.attributes[key]?.bindings ?? {}).length > 0;
      }
      return definition.role === role || definition.__experimentalRole === role;
    }) : [];
    const attributesToCheck = !!attributesByRole.length ? attributesByRole : Object.entries(blockAttributes);
    return attributesToCheck.every(([key, definition]) => {
      const value = block.attributes[key];
      if (definition.hasOwnProperty("default")) {
        return value === definition.default;
      }
      if (definition.type === "rich-text") {
        return !value?.length;
      }
      return value === void 0;
    });
  }
  function isUnmodifiedDefaultBlock(block, role) {
    return block.name === getDefaultBlockName() && isUnmodifiedBlock(block, role);
  }
  function isValidIcon(icon) {
    return !!icon && (typeof icon === "string" || (0, import_element.isValidElement)(icon) || typeof icon === "function" || icon instanceof import_element.Component);
  }
  function normalizeIconObject(icon) {
    icon = icon || BLOCK_ICON_DEFAULT;
    if (isValidIcon(icon)) {
      return { src: icon };
    }
    if ("background" in icon) {
      const colordBgColor = w(icon.background);
      const getColorContrast = (iconColor) => colordBgColor.contrast(iconColor);
      const maxContrast = Math.max(...ICON_COLORS.map(getColorContrast));
      return {
        ...icon,
        foreground: icon.foreground ? icon.foreground : ICON_COLORS.find(
          (iconColor) => getColorContrast(iconColor) === maxContrast
        ),
        shadowColor: colordBgColor.alpha(0.3).toRgbString()
      };
    }
    return icon;
  }
  function normalizeBlockType(blockTypeOrName) {
    if (typeof blockTypeOrName === "string") {
      return getBlockType(blockTypeOrName);
    }
    return blockTypeOrName;
  }
  function getBlockLabel(blockType, attributes, context = "visual") {
    const { __experimentalLabel: getLabel, title } = blockType;
    const label = getLabel && getLabel(attributes, { context });
    if (!label) {
      return title;
    }
    if (label.toPlainText) {
      return label.toPlainText();
    }
    return (0, import_dom.__unstableStripHTML)(label);
  }
  function getAccessibleBlockLabel(blockType, attributes, position, direction = "vertical") {
    const title = blockType?.title;
    const label = blockType ? getBlockLabel(blockType, attributes, "accessibility") : "";
    const hasPosition = position !== void 0;
    const hasLabel = label && label !== title;
    if (hasPosition && direction === "vertical") {
      if (hasLabel) {
        return (0, import_i18n2.sprintf)(
          /* translators: accessibility text. 1: The block title. 2: The block row number. 3: The block label.. */
          (0, import_i18n2.__)("%1$s Block. Row %2$d. %3$s"),
          title,
          position,
          label
        );
      }
      return (0, import_i18n2.sprintf)(
        /* translators: accessibility text. 1: The block title. 2: The block row number. */
        (0, import_i18n2.__)("%1$s Block. Row %2$d"),
        title,
        position
      );
    } else if (hasPosition && direction === "horizontal") {
      if (hasLabel) {
        return (0, import_i18n2.sprintf)(
          /* translators: accessibility text. 1: The block title. 2: The block column number. 3: The block label.. */
          (0, import_i18n2.__)("%1$s Block. Column %2$d. %3$s"),
          title,
          position,
          label
        );
      }
      return (0, import_i18n2.sprintf)(
        /* translators: accessibility text. 1: The block title. 2: The block column number. */
        (0, import_i18n2.__)("%1$s Block. Column %2$d"),
        title,
        position
      );
    }
    if (hasLabel) {
      return (0, import_i18n2.sprintf)(
        /* translators: accessibility text. 1: The block title. 2: The block label. */
        (0, import_i18n2.__)("%1$s Block. %2$s"),
        title,
        label
      );
    }
    return (0, import_i18n2.sprintf)(
      /* translators: accessibility text. %s: The block title. */
      (0, import_i18n2.__)("%s Block"),
      title
    );
  }
  function getDefault(attributeSchema) {
    if (attributeSchema.default !== void 0) {
      return attributeSchema.default;
    }
    if (attributeSchema.type === "rich-text") {
      return new import_rich_text.RichTextData();
    }
  }
  function isBlockRegistered(name) {
    return getBlockType(name) !== void 0;
  }
  function __experimentalSanitizeBlockAttributes(name, attributes) {
    const blockType = getBlockType(name);
    if (void 0 === blockType) {
      throw new Error(`Block type '${name}' is not registered.`);
    }
    return Object.entries(blockType.attributes).reduce(
      (accumulator, [key, schema]) => {
        const value = attributes[key];
        if (void 0 !== value) {
          if (schema.type === "rich-text") {
            if (value instanceof import_rich_text.RichTextData) {
              accumulator[key] = value;
            } else if (typeof value === "string") {
              accumulator[key] = import_rich_text.RichTextData.fromHTMLString(value);
            }
          } else if (schema.type === "string" && value instanceof import_rich_text.RichTextData) {
            accumulator[key] = value.toHTMLString();
          } else {
            accumulator[key] = value;
          }
        } else {
          const _default = getDefault(schema);
          if (void 0 !== _default) {
            accumulator[key] = _default;
          }
        }
        if (["node", "children"].indexOf(schema.source) !== -1) {
          if (typeof accumulator[key] === "string") {
            accumulator[key] = [accumulator[key]];
          } else if (!Array.isArray(accumulator[key])) {
            accumulator[key] = [];
          }
        }
        return accumulator;
      },
      {}
    );
  }
  function getBlockAttributesNamesByRole(name, role) {
    const attributes = getBlockType(name)?.attributes;
    if (!attributes) {
      return [];
    }
    const attributesNames = Object.keys(attributes);
    if (!role) {
      return attributesNames;
    }
    return attributesNames.filter((attributeName) => {
      const attribute = attributes[attributeName];
      if (attribute?.role === role) {
        return true;
      }
      if (attribute?.__experimentalRole === role) {
        (0, import_deprecated.default)("__experimentalRole attribute", {
          since: "6.7",
          version: "6.8",
          alternative: "role attribute",
          hint: `Check the block.json of the ${name} block.`
        });
        return true;
      }
      return false;
    });
  }
  var __experimentalGetBlockAttributesNamesByRole = (...args) => {
    (0, import_deprecated.default)("__experimentalGetBlockAttributesNamesByRole", {
      since: "6.7",
      version: "6.8",
      alternative: "getBlockAttributesNamesByRole"
    });
    return getBlockAttributesNamesByRole(...args);
  };
  function isContentBlock(name) {
    const blockType = getBlockType(name);
    const attributes = blockType?.attributes;
    const supportsContentRole = blockType?.supports?.contentRole;
    if (supportsContentRole) {
      return true;
    }
    if (!attributes) {
      return false;
    }
    return !!Object.keys(attributes)?.some((attributeKey) => {
      const attribute = attributes[attributeKey];
      return attribute?.role === "content" || attribute?.__experimentalRole === "content";
    });
  }
  function omit(object, keys) {
    return Object.fromEntries(
      Object.entries(object).filter(([key]) => !keys.includes(key))
    );
  }

  // packages/blocks/build-module/store/reducer.js
  var DEFAULT_CATEGORIES = [
    { slug: "text", title: (0, import_i18n3.__)("Text") },
    { slug: "media", title: (0, import_i18n3.__)("Media") },
    { slug: "design", title: (0, import_i18n3.__)("Design") },
    { slug: "widgets", title: (0, import_i18n3.__)("Widgets") },
    { slug: "theme", title: (0, import_i18n3.__)("Theme") },
    { slug: "embed", title: (0, import_i18n3.__)("Embeds") },
    { slug: "reusable", title: (0, import_i18n3.__)("Reusable blocks") }
  ];
  function keyBlockTypesByName(types) {
    return types.reduce(
      (newBlockTypes, block) => ({
        ...newBlockTypes,
        [block.name]: block
      }),
      {}
    );
  }
  function getUniqueItemsByName(items) {
    return items.reduce((acc, currentItem) => {
      if (!acc.some((item) => item.name === currentItem.name)) {
        acc.push(currentItem);
      }
      return acc;
    }, []);
  }
  function bootstrappedBlockTypes(state = {}, action) {
    switch (action.type) {
      case "ADD_BOOTSTRAPPED_BLOCK_TYPE":
        const { name, blockType } = action;
        const serverDefinition = state[name];
        if (serverDefinition) {
          return state;
        }
        const newDefinition = Object.fromEntries(
          Object.entries(blockType).filter(
            ([, value]) => value !== null && value !== void 0
          ).map(([key, value]) => [camelCase(key), value])
        );
        newDefinition.name = name;
        return {
          ...state,
          [name]: newDefinition
        };
      case "REMOVE_BLOCK_TYPES":
        return omit(state, action.names);
    }
    return state;
  }
  function unprocessedBlockTypes(state = {}, action) {
    switch (action.type) {
      case "ADD_UNPROCESSED_BLOCK_TYPE":
        return {
          ...state,
          [action.name]: action.blockType
        };
      case "REMOVE_BLOCK_TYPES":
        return omit(state, action.names);
    }
    return state;
  }
  function blockTypes(state = {}, action) {
    switch (action.type) {
      case "ADD_BLOCK_TYPES":
        return {
          ...state,
          ...keyBlockTypesByName(action.blockTypes)
        };
      case "REMOVE_BLOCK_TYPES":
        return omit(state, action.names);
    }
    return state;
  }
  function blockStyles(state = {}, action) {
    switch (action.type) {
      case "ADD_BLOCK_TYPES":
        return {
          ...state,
          ...Object.fromEntries(
            Object.entries(
              keyBlockTypesByName(action.blockTypes)
            ).map(([name, blockType]) => [
              name,
              getUniqueItemsByName([
                ...(blockType.styles ?? []).map((style) => ({
                  ...style,
                  source: "block"
                })),
                ...(state[blockType.name] ?? []).filter(
                  ({ source }) => "block" !== source
                )
              ])
            ])
          )
        };
      case "ADD_BLOCK_STYLES":
        const updatedStyles = {};
        action.blockNames.forEach((blockName) => {
          updatedStyles[blockName] = getUniqueItemsByName([
            ...state[blockName] ?? [],
            ...action.styles
          ]);
        });
        return { ...state, ...updatedStyles };
      case "REMOVE_BLOCK_STYLES":
        return {
          ...state,
          [action.blockName]: (state[action.blockName] ?? []).filter(
            (style) => action.styleNames.indexOf(style.name) === -1
          )
        };
    }
    return state;
  }
  function blockVariations(state = {}, action) {
    switch (action.type) {
      case "ADD_BLOCK_TYPES":
        return {
          ...state,
          ...Object.fromEntries(
            Object.entries(
              keyBlockTypesByName(action.blockTypes)
            ).map(([name, blockType]) => {
              return [
                name,
                getUniqueItemsByName([
                  ...(blockType.variations ?? []).map(
                    (variation) => ({
                      ...variation,
                      source: "block"
                    })
                  ),
                  ...(state[blockType.name] ?? []).filter(
                    ({ source }) => "block" !== source
                  )
                ])
              ];
            })
          )
        };
      case "ADD_BLOCK_VARIATIONS":
        return {
          ...state,
          [action.blockName]: getUniqueItemsByName([
            ...state[action.blockName] ?? [],
            ...action.variations
          ])
        };
      case "REMOVE_BLOCK_VARIATIONS":
        return {
          ...state,
          [action.blockName]: (state[action.blockName] ?? []).filter(
            (variation) => action.variationNames.indexOf(variation.name) === -1
          )
        };
    }
    return state;
  }
  function createBlockNameSetterReducer(setActionType) {
    return (state = null, action) => {
      switch (action.type) {
        case "REMOVE_BLOCK_TYPES":
          if (action.names.indexOf(state) !== -1) {
            return null;
          }
          return state;
        case setActionType:
          return action.name || null;
      }
      return state;
    };
  }
  var defaultBlockName = createBlockNameSetterReducer(
    "SET_DEFAULT_BLOCK_NAME"
  );
  var freeformFallbackBlockName = createBlockNameSetterReducer(
    "SET_FREEFORM_FALLBACK_BLOCK_NAME"
  );
  var unregisteredFallbackBlockName = createBlockNameSetterReducer(
    "SET_UNREGISTERED_FALLBACK_BLOCK_NAME"
  );
  var groupingBlockName = createBlockNameSetterReducer(
    "SET_GROUPING_BLOCK_NAME"
  );
  function categories(state = DEFAULT_CATEGORIES, action) {
    switch (action.type) {
      case "SET_CATEGORIES":
        const uniqueCategories = /* @__PURE__ */ new Map();
        (action.categories || []).forEach((category) => {
          uniqueCategories.set(category.slug, category);
        });
        return [...uniqueCategories.values()];
      case "UPDATE_CATEGORY": {
        if (!action.category || !Object.keys(action.category).length) {
          return state;
        }
        const categoryToChange = state.find(
          ({ slug }) => slug === action.slug
        );
        if (categoryToChange) {
          return state.map((category) => {
            if (category.slug === action.slug) {
              return {
                ...category,
                ...action.category
              };
            }
            return category;
          });
        }
      }
    }
    return state;
  }
  function collections(state = {}, action) {
    switch (action.type) {
      case "ADD_BLOCK_COLLECTION":
        return {
          ...state,
          [action.namespace]: {
            title: action.title,
            icon: action.icon
          }
        };
      case "REMOVE_BLOCK_COLLECTION":
        return omit(state, action.namespace);
    }
    return state;
  }
  function getMergedUsesContext(existingUsesContext = [], newUsesContext = []) {
    const mergedArrays = Array.from(
      new Set(existingUsesContext.concat(newUsesContext))
    );
    return mergedArrays.length > 0 ? mergedArrays : void 0;
  }
  function blockBindingsSources(state = {}, action) {
    switch (action.type) {
      case "ADD_BLOCK_BINDINGS_SOURCE":
        return {
          ...state,
          [action.name]: {
            label: action.label || state[action.name]?.label,
            usesContext: getMergedUsesContext(
              state[action.name]?.usesContext,
              action.usesContext
            ),
            getValues: action.getValues,
            setValues: action.setValues,
            // Only set `canUserEditValue` if `setValues` is also defined.
            canUserEditValue: action.setValues && action.canUserEditValue,
            getFieldsList: action.getFieldsList
          }
        };
      case "REMOVE_BLOCK_BINDINGS_SOURCE":
        return omit(state, action.name);
    }
    return state;
  }
  var reducer_default = (0, import_data2.combineReducers)({
    bootstrappedBlockTypes,
    unprocessedBlockTypes,
    blockTypes,
    blockStyles,
    blockVariations,
    defaultBlockName,
    freeformFallbackBlockName,
    unregisteredFallbackBlockName,
    groupingBlockName,
    categories,
    collections,
    blockBindingsSources
  });

  // packages/blocks/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    __experimentalHasContentRoleAttribute: () => __experimentalHasContentRoleAttribute,
    getActiveBlockVariation: () => getActiveBlockVariation,
    getBlockStyles: () => getBlockStyles,
    getBlockSupport: () => getBlockSupport2,
    getBlockType: () => getBlockType2,
    getBlockTypes: () => getBlockTypes2,
    getBlockVariations: () => getBlockVariations2,
    getCategories: () => getCategories,
    getChildBlockNames: () => getChildBlockNames2,
    getCollections: () => getCollections,
    getDefaultBlockName: () => getDefaultBlockName2,
    getDefaultBlockVariation: () => getDefaultBlockVariation,
    getFreeformFallbackBlockName: () => getFreeformFallbackBlockName,
    getGroupingBlockName: () => getGroupingBlockName2,
    getUnregisteredFallbackBlockName: () => getUnregisteredFallbackBlockName,
    hasBlockSupport: () => hasBlockSupport2,
    hasChildBlocks: () => hasChildBlocks2,
    hasChildBlocksWithInserterSupport: () => hasChildBlocksWithInserterSupport2,
    isMatchingSearchTerm: () => isMatchingSearchTerm
  });
  var import_remove_accents = __toESM(require_remove_accents());
  var import_data4 = __toESM(require_data());
  var import_rich_text2 = __toESM(require_rich_text());
  var import_deprecated3 = __toESM(require_deprecated());

  // packages/blocks/build-module/store/utils.js
  var getValueFromObjectPath = (object, path, defaultValue) => {
    const normalizedPath = Array.isArray(path) ? path : path.split(".");
    let value = object;
    normalizedPath.forEach((fieldName) => {
      value = value?.[fieldName];
    });
    return value ?? defaultValue;
  };
  function isObject2(candidate) {
    return typeof candidate === "object" && candidate.constructor === Object && candidate !== null;
  }
  function matchesAttributes(blockAttributes, variationAttributes) {
    if (isObject2(blockAttributes) && isObject2(variationAttributes)) {
      return Object.entries(variationAttributes).every(
        ([key, value]) => matchesAttributes(blockAttributes?.[key], value)
      );
    }
    return blockAttributes === variationAttributes;
  }

  // packages/blocks/build-module/store/private-selectors.js
  var private_selectors_exports = {};
  __export(private_selectors_exports, {
    getAllBlockBindingsSources: () => getAllBlockBindingsSources,
    getBlockBindingsSource: () => getBlockBindingsSource2,
    getBlockBindingsSourceFieldsList: () => getBlockBindingsSourceFieldsList,
    getBootstrappedBlockType: () => getBootstrappedBlockType,
    getSupportedStyles: () => getSupportedStyles,
    getUnprocessedBlockTypes: () => getUnprocessedBlockTypes,
    hasContentRoleAttribute: () => hasContentRoleAttribute
  });
  var import_data3 = __toESM(require_data());
  var import_deprecated2 = __toESM(require_deprecated());
  var ROOT_BLOCK_SUPPORTS = [
    "background",
    "backgroundColor",
    "color",
    "linkColor",
    "captionColor",
    "buttonColor",
    "headingColor",
    "fontFamily",
    "fontSize",
    "fontStyle",
    "fontWeight",
    "lineHeight",
    "padding",
    "contentSize",
    "wideSize",
    "blockGap",
    "textAlign",
    "textDecoration",
    "textTransform",
    "letterSpacing"
  ];
  function filterElementBlockSupports(blockSupports, name, element) {
    return blockSupports.filter((support) => {
      if (support === "fontSize" && element === "heading") {
        return false;
      }
      if (support === "textDecoration" && !name && element !== "link") {
        return false;
      }
      if (support === "textTransform" && !name && !(["heading", "h1", "h2", "h3", "h4", "h5", "h6"].includes(
        element
      ) || element === "button" || element === "caption" || element === "text")) {
        return false;
      }
      if (support === "letterSpacing" && !name && !(["heading", "h1", "h2", "h3", "h4", "h5", "h6"].includes(
        element
      ) || element === "button" || element === "caption" || element === "text")) {
        return false;
      }
      if (support === "textColumns" && !name) {
        return false;
      }
      return true;
    });
  }
  var getSupportedStyles = (0, import_data3.createSelector)(
    (state, name, element) => {
      if (!name) {
        return filterElementBlockSupports(
          ROOT_BLOCK_SUPPORTS,
          name,
          element
        );
      }
      const blockType = getBlockType2(state, name);
      if (!blockType) {
        return [];
      }
      const supportKeys = [];
      if (blockType?.supports?.spacing?.blockGap) {
        supportKeys.push("blockGap");
      }
      if (blockType?.supports?.shadow) {
        supportKeys.push("shadow");
      }
      Object.keys(__EXPERIMENTAL_STYLE_PROPERTY).forEach((styleName) => {
        if (!__EXPERIMENTAL_STYLE_PROPERTY[styleName].support) {
          return;
        }
        if (__EXPERIMENTAL_STYLE_PROPERTY[styleName].requiresOptOut) {
          if (__EXPERIMENTAL_STYLE_PROPERTY[styleName].support[0] in blockType.supports && getValueFromObjectPath(
            blockType.supports,
            __EXPERIMENTAL_STYLE_PROPERTY[styleName].support
          ) !== false) {
            supportKeys.push(styleName);
            return;
          }
        }
        if (getValueFromObjectPath(
          blockType.supports,
          __EXPERIMENTAL_STYLE_PROPERTY[styleName].support,
          false
        )) {
          supportKeys.push(styleName);
        }
      });
      return filterElementBlockSupports(supportKeys, name, element);
    },
    (state, name) => [state.blockTypes[name]]
  );
  function getBootstrappedBlockType(state, name) {
    return state.bootstrappedBlockTypes[name];
  }
  function getUnprocessedBlockTypes(state) {
    return state.unprocessedBlockTypes;
  }
  function getAllBlockBindingsSources(state) {
    return state.blockBindingsSources;
  }
  function getBlockBindingsSource2(state, sourceName) {
    return state.blockBindingsSources[sourceName];
  }
  var getBlockBindingsSourceFieldsList = (0, import_data3.createRegistrySelector)(
    (select3) => (0, import_data3.createSelector)(
      (state, source, blockContext) => {
        if (!source.getFieldsList) {
          return [];
        }
        const context = {};
        if (source?.usesContext?.length) {
          for (const key of source.usesContext) {
            context[key] = blockContext[key];
          }
        }
        return source.getFieldsList({ select: select3, context });
      },
      (state, source, blockContext) => [
        source.getFieldsList,
        source.usesContext,
        blockContext
      ]
    )
  );
  var hasContentRoleAttribute = (state, blockTypeName) => {
    const blockType = getBlockType2(state, blockTypeName);
    if (!blockType) {
      return false;
    }
    return Object.values(blockType.attributes).some(
      ({ role, __experimentalRole }) => {
        if (role === "content") {
          return true;
        }
        if (__experimentalRole === "content") {
          (0, import_deprecated2.default)("__experimentalRole attribute", {
            since: "6.7",
            version: "6.8",
            alternative: "role attribute",
            hint: `Check the block.json of the ${blockTypeName} block.`
          });
          return true;
        }
        return false;
      }
    );
  };

  // packages/blocks/build-module/store/selectors.js
  var getNormalizedBlockType = (state, nameOrType) => "string" === typeof nameOrType ? getBlockType2(state, nameOrType) : nameOrType;
  var getBlockTypes2 = (0, import_data4.createSelector)(
    (state) => Object.values(state.blockTypes),
    (state) => [state.blockTypes]
  );
  function getBlockType2(state, name) {
    return state.blockTypes[name];
  }
  function getBlockStyles(state, name) {
    return state.blockStyles[name];
  }
  var getBlockVariations2 = (0, import_data4.createSelector)(
    (state, blockName, scope) => {
      const variations = state.blockVariations[blockName];
      if (!variations || !scope) {
        return variations;
      }
      return variations.filter((variation) => {
        return (variation.scope || ["block", "inserter"]).includes(
          scope
        );
      });
    },
    (state, blockName) => [state.blockVariations[blockName]]
  );
  function getActiveBlockVariation(state, blockName, attributes, scope) {
    const variations = getBlockVariations2(state, blockName, scope);
    if (!variations) {
      return variations;
    }
    const blockType = getBlockType2(state, blockName);
    const attributeKeys = Object.keys(blockType?.attributes || {});
    let match;
    let maxMatchedAttributes = 0;
    for (const variation of variations) {
      if (Array.isArray(variation.isActive)) {
        const definedAttributes = variation.isActive.filter(
          (attribute) => {
            const topLevelAttribute = attribute.split(".")[0];
            return attributeKeys.includes(topLevelAttribute);
          }
        );
        const definedAttributesLength = definedAttributes.length;
        if (definedAttributesLength === 0) {
          continue;
        }
        const isMatch = definedAttributes.every((attribute) => {
          const variationAttributeValue = getValueFromObjectPath(
            variation.attributes,
            attribute
          );
          if (variationAttributeValue === void 0) {
            return false;
          }
          let blockAttributeValue = getValueFromObjectPath(
            attributes,
            attribute
          );
          if (blockAttributeValue instanceof import_rich_text2.RichTextData) {
            blockAttributeValue = blockAttributeValue.toHTMLString();
          }
          return matchesAttributes(
            blockAttributeValue,
            variationAttributeValue
          );
        });
        if (isMatch && definedAttributesLength > maxMatchedAttributes) {
          match = variation;
          maxMatchedAttributes = definedAttributesLength;
        }
      } else if (variation.isActive?.(attributes, variation.attributes)) {
        return match || variation;
      }
    }
    if (!match && ["block", "transform"].includes(scope)) {
      match = variations.find(
        (variation) => variation?.isDefault && !Object.hasOwn(variation, "isActive")
      );
    }
    return match;
  }
  function getDefaultBlockVariation(state, blockName, scope) {
    const variations = getBlockVariations2(state, blockName, scope);
    const defaultVariation = [...variations].reverse().find(({ isDefault }) => !!isDefault);
    return defaultVariation || variations[0];
  }
  function getCategories(state) {
    return state.categories;
  }
  function getCollections(state) {
    return state.collections;
  }
  function getDefaultBlockName2(state) {
    return state.defaultBlockName;
  }
  function getFreeformFallbackBlockName(state) {
    return state.freeformFallbackBlockName;
  }
  function getUnregisteredFallbackBlockName(state) {
    return state.unregisteredFallbackBlockName;
  }
  function getGroupingBlockName2(state) {
    return state.groupingBlockName;
  }
  var getChildBlockNames2 = (0, import_data4.createSelector)(
    (state, blockName) => {
      return getBlockTypes2(state).filter((blockType) => {
        return blockType.parent?.includes(blockName);
      }).map(({ name }) => name);
    },
    (state) => [state.blockTypes]
  );
  var getBlockSupport2 = (state, nameOrType, feature, defaultSupports) => {
    const blockType = getNormalizedBlockType(state, nameOrType);
    if (!blockType?.supports) {
      return defaultSupports;
    }
    return getValueFromObjectPath(
      blockType.supports,
      feature,
      defaultSupports
    );
  };
  function hasBlockSupport2(state, nameOrType, feature, defaultSupports) {
    return !!getBlockSupport2(state, nameOrType, feature, defaultSupports);
  }
  function getNormalizedSearchTerm(term) {
    return (0, import_remove_accents.default)(term ?? "").toLowerCase().trim();
  }
  function isMatchingSearchTerm(state, nameOrType, searchTerm = "") {
    const blockType = getNormalizedBlockType(state, nameOrType);
    const normalizedSearchTerm = getNormalizedSearchTerm(searchTerm);
    const isSearchMatch = (candidate) => getNormalizedSearchTerm(candidate).includes(normalizedSearchTerm);
    return isSearchMatch(blockType.title) || blockType.keywords?.some(isSearchMatch) || isSearchMatch(blockType.category) || typeof blockType.description === "string" && isSearchMatch(blockType.description);
  }
  var hasChildBlocks2 = (state, blockName) => {
    return getChildBlockNames2(state, blockName).length > 0;
  };
  var hasChildBlocksWithInserterSupport2 = (state, blockName) => {
    return getChildBlockNames2(state, blockName).some((childBlockName) => {
      return hasBlockSupport2(state, childBlockName, "inserter", true);
    });
  };
  var __experimentalHasContentRoleAttribute = (...args) => {
    (0, import_deprecated3.default)("__experimentalHasContentRoleAttribute", {
      since: "6.7",
      version: "6.8",
      hint: "This is a private selector."
    });
    return hasContentRoleAttribute(...args);
  };

  // packages/blocks/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    __experimentalReapplyBlockFilters: () => __experimentalReapplyBlockFilters,
    addBlockCollection: () => addBlockCollection,
    addBlockStyles: () => addBlockStyles,
    addBlockTypes: () => addBlockTypes,
    addBlockVariations: () => addBlockVariations,
    reapplyBlockTypeFilters: () => reapplyBlockTypeFilters,
    removeBlockCollection: () => removeBlockCollection,
    removeBlockStyles: () => removeBlockStyles,
    removeBlockTypes: () => removeBlockTypes,
    removeBlockVariations: () => removeBlockVariations,
    setCategories: () => setCategories,
    setDefaultBlockName: () => setDefaultBlockName2,
    setFreeformFallbackBlockName: () => setFreeformFallbackBlockName,
    setGroupingBlockName: () => setGroupingBlockName2,
    setUnregisteredFallbackBlockName: () => setUnregisteredFallbackBlockName,
    updateCategory: () => updateCategory
  });
  var import_deprecated5 = __toESM(require_deprecated());

  // node_modules/is-plain-object/dist/is-plain-object.mjs
  function isObject3(o3) {
    return Object.prototype.toString.call(o3) === "[object Object]";
  }
  function isPlainObject(o3) {
    var ctor, prot;
    if (isObject3(o3) === false) return false;
    ctor = o3.constructor;
    if (ctor === void 0) return true;
    prot = ctor.prototype;
    if (isObject3(prot) === false) return false;
    if (prot.hasOwnProperty("isPrototypeOf") === false) {
      return false;
    }
    return true;
  }

  // packages/blocks/build-module/store/process-block-type.js
  var import_react_is = __toESM(require_react_is());
  var import_deprecated4 = __toESM(require_deprecated());
  var import_hooks = __toESM(require_hooks());
  var import_warning2 = __toESM(require_warning());
  var LEGACY_CATEGORY_MAPPING = {
    common: "text",
    formatting: "text",
    layout: "design"
  };
  function mergeBlockVariations(bootstrappedVariations = [], clientVariations = []) {
    const result = [...bootstrappedVariations];
    clientVariations.forEach((clientVariation) => {
      const index = result.findIndex(
        (bootstrappedVariation) => bootstrappedVariation.name === clientVariation.name
      );
      if (index !== -1) {
        result[index] = { ...result[index], ...clientVariation };
      } else {
        result.push(clientVariation);
      }
    });
    return result;
  }
  var processBlockType = (name, blockSettings) => ({ select: select3 }) => {
    const bootstrappedBlockType = select3.getBootstrappedBlockType(name);
    const blockType = {
      apiVersion: 1,
      name,
      icon: BLOCK_ICON_DEFAULT,
      keywords: [],
      attributes: {},
      providesContext: {},
      usesContext: [],
      selectors: {},
      supports: {},
      styles: [],
      blockHooks: {},
      save: () => null,
      ...bootstrappedBlockType,
      ...blockSettings,
      // blockType.variations can be defined as a filePath.
      variations: mergeBlockVariations(
        Array.isArray(bootstrappedBlockType?.variations) ? bootstrappedBlockType.variations : [],
        Array.isArray(blockSettings?.variations) ? blockSettings.variations : []
      )
    };
    if (!blockType.attributes || typeof blockType.attributes !== "object") {
      (0, import_warning2.default)(
        'The block "' + name + '" is registering attributes as `null` or `undefined`. Use an empty object (`attributes: {}`) or exclude the `attributes` key.'
      );
      blockType.attributes = {};
    }
    const settings = (0, import_hooks.applyFilters)(
      "blocks.registerBlockType",
      blockType,
      name,
      null
    );
    if (settings.apiVersion <= 2) {
      (0, import_warning2.default)(
        `The block "${name}" is registered with API version 2 or lower. This means that the post editor may work as a non-iframe editor.
Since all editors are planned to work as iframes in the future, set the \`apiVersion\` field to 3 and test the block inside the iframe editor.
See: https://developer.wordpress.org/block-editor/reference-guides/block-api/block-api-versions/#version-3-wordpress-6-3`
      );
    }
    if (settings.description && typeof settings.description !== "string") {
      (0, import_deprecated4.default)("Declaring non-string block descriptions", {
        since: "6.2"
      });
    }
    if (settings.deprecated) {
      settings.deprecated = settings.deprecated.map(
        (deprecation) => Object.fromEntries(
          Object.entries(
            // Only keep valid deprecation keys.
            (0, import_hooks.applyFilters)(
              "blocks.registerBlockType",
              // Merge deprecation keys with pre-filter settings
              // so that filters that depend on specific keys being
              // present don't fail.
              {
                // Omit deprecation keys here so that deprecations
                // can opt out of specific keys like "supports".
                ...omit(blockType, DEPRECATED_ENTRY_KEYS),
                ...deprecation
              },
              blockType.name,
              deprecation
            )
          ).filter(
            ([key]) => DEPRECATED_ENTRY_KEYS.includes(key)
          )
        )
      );
    }
    if (!isPlainObject(settings)) {
      (0, import_warning2.default)("Block settings must be a valid object.");
      return;
    }
    if (typeof settings.save !== "function") {
      (0, import_warning2.default)('The "save" property must be a valid function.');
      return;
    }
    if ("edit" in settings && !(0, import_react_is.isValidElementType)(settings.edit)) {
      (0, import_warning2.default)('The "edit" property must be a valid component.');
      return;
    }
    if (LEGACY_CATEGORY_MAPPING.hasOwnProperty(settings.category)) {
      settings.category = LEGACY_CATEGORY_MAPPING[settings.category];
    }
    if ("category" in settings && !select3.getCategories().some(({ slug }) => slug === settings.category)) {
      (0, import_warning2.default)(
        'The block "' + name + '" is registered with an invalid category "' + settings.category + '".'
      );
      delete settings.category;
    }
    if (!("title" in settings) || settings.title === "") {
      (0, import_warning2.default)('The block "' + name + '" must have a title.');
      return;
    }
    if (typeof settings.title !== "string") {
      (0, import_warning2.default)("Block titles must be strings.");
      return;
    }
    settings.icon = normalizeIconObject(settings.icon);
    if (!isValidIcon(settings.icon.src)) {
      (0, import_warning2.default)(
        "The icon passed is invalid. The icon should be a string, an element, a function, or an object following the specifications documented in https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#icon-optional"
      );
      return;
    }
    if (typeof settings?.parent === "string" || settings?.parent instanceof String) {
      settings.parent = [settings.parent];
      (0, import_warning2.default)(
        "Parent must be undefined or an array of strings (block types), but it is a string."
      );
    }
    if (!Array.isArray(settings?.parent) && settings?.parent !== void 0) {
      (0, import_warning2.default)(
        "Parent must be undefined or an array of block types, but it is ",
        settings.parent
      );
      return;
    }
    if (1 === settings?.parent?.length && name === settings.parent[0]) {
      (0, import_warning2.default)(
        'Block "' + name + '" cannot be a parent of itself. Please remove the block name from the parent list.'
      );
      return;
    }
    return settings;
  };

  // packages/blocks/build-module/store/actions.js
  function addBlockTypes(blockTypes2) {
    return {
      type: "ADD_BLOCK_TYPES",
      blockTypes: Array.isArray(blockTypes2) ? blockTypes2 : [blockTypes2]
    };
  }
  function reapplyBlockTypeFilters() {
    return ({ dispatch: dispatch3, select: select3 }) => {
      const processedBlockTypes = [];
      for (const [name, settings] of Object.entries(
        select3.getUnprocessedBlockTypes()
      )) {
        const result = dispatch3(processBlockType(name, settings));
        if (result) {
          processedBlockTypes.push(result);
        }
      }
      if (!processedBlockTypes.length) {
        return;
      }
      dispatch3.addBlockTypes(processedBlockTypes);
    };
  }
  function __experimentalReapplyBlockFilters() {
    (0, import_deprecated5.default)(
      'wp.data.dispatch( "core/blocks" ).__experimentalReapplyBlockFilters',
      {
        since: "6.4",
        alternative: "reapplyBlockFilters"
      }
    );
    return reapplyBlockTypeFilters();
  }
  function removeBlockTypes(names) {
    return {
      type: "REMOVE_BLOCK_TYPES",
      names: Array.isArray(names) ? names : [names]
    };
  }
  function addBlockStyles(blockNames, styles) {
    return {
      type: "ADD_BLOCK_STYLES",
      styles: Array.isArray(styles) ? styles : [styles],
      blockNames: Array.isArray(blockNames) ? blockNames : [blockNames]
    };
  }
  function removeBlockStyles(blockName, styleNames) {
    return {
      type: "REMOVE_BLOCK_STYLES",
      styleNames: Array.isArray(styleNames) ? styleNames : [styleNames],
      blockName
    };
  }
  function addBlockVariations(blockName, variations) {
    return {
      type: "ADD_BLOCK_VARIATIONS",
      variations: Array.isArray(variations) ? variations : [variations],
      blockName
    };
  }
  function removeBlockVariations(blockName, variationNames) {
    return {
      type: "REMOVE_BLOCK_VARIATIONS",
      variationNames: Array.isArray(variationNames) ? variationNames : [variationNames],
      blockName
    };
  }
  function setDefaultBlockName2(name) {
    return {
      type: "SET_DEFAULT_BLOCK_NAME",
      name
    };
  }
  function setFreeformFallbackBlockName(name) {
    return {
      type: "SET_FREEFORM_FALLBACK_BLOCK_NAME",
      name
    };
  }
  function setUnregisteredFallbackBlockName(name) {
    return {
      type: "SET_UNREGISTERED_FALLBACK_BLOCK_NAME",
      name
    };
  }
  function setGroupingBlockName2(name) {
    return {
      type: "SET_GROUPING_BLOCK_NAME",
      name
    };
  }
  function setCategories(categories2) {
    return {
      type: "SET_CATEGORIES",
      categories: categories2
    };
  }
  function updateCategory(slug, category) {
    return {
      type: "UPDATE_CATEGORY",
      slug,
      category
    };
  }
  function addBlockCollection(namespace, title, icon) {
    return {
      type: "ADD_BLOCK_COLLECTION",
      namespace,
      title,
      icon
    };
  }
  function removeBlockCollection(namespace) {
    return {
      type: "REMOVE_BLOCK_COLLECTION",
      namespace
    };
  }

  // packages/blocks/build-module/store/private-actions.js
  var private_actions_exports = {};
  __export(private_actions_exports, {
    addBlockBindingsSource: () => addBlockBindingsSource,
    addBootstrappedBlockType: () => addBootstrappedBlockType,
    addUnprocessedBlockType: () => addUnprocessedBlockType,
    removeBlockBindingsSource: () => removeBlockBindingsSource
  });
  function addBootstrappedBlockType(name, blockType) {
    return {
      type: "ADD_BOOTSTRAPPED_BLOCK_TYPE",
      name,
      blockType
    };
  }
  function addUnprocessedBlockType(name, blockType) {
    return ({ dispatch: dispatch3 }) => {
      dispatch3({ type: "ADD_UNPROCESSED_BLOCK_TYPE", name, blockType });
      const processedBlockType = dispatch3(
        processBlockType(name, blockType)
      );
      if (!processedBlockType) {
        return;
      }
      dispatch3.addBlockTypes(processedBlockType);
    };
  }
  function addBlockBindingsSource(source) {
    return {
      type: "ADD_BLOCK_BINDINGS_SOURCE",
      name: source.name,
      label: source.label,
      usesContext: source.usesContext,
      getValues: source.getValues,
      setValues: source.setValues,
      canUserEditValue: source.canUserEditValue,
      getFieldsList: source.getFieldsList
    };
  }
  function removeBlockBindingsSource(name) {
    return {
      type: "REMOVE_BLOCK_BINDINGS_SOURCE",
      name
    };
  }

  // packages/blocks/build-module/store/constants.js
  var STORE_NAME = "core/blocks";

  // packages/blocks/build-module/store/index.js
  var store = (0, import_data5.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  });
  (0, import_data5.register)(store);
  unlock(store).registerPrivateSelectors(private_selectors_exports);
  unlock(store).registerPrivateActions(private_actions_exports);

  // node_modules/uuid/dist/esm-browser/rng.js
  var getRandomValues;
  var rnds8 = new Uint8Array(16);
  function rng() {
    if (!getRandomValues) {
      getRandomValues = typeof crypto !== "undefined" && crypto.getRandomValues && crypto.getRandomValues.bind(crypto);
      if (!getRandomValues) {
        throw new Error("crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported");
      }
    }
    return getRandomValues(rnds8);
  }

  // node_modules/uuid/dist/esm-browser/stringify.js
  var byteToHex = [];
  for (let i2 = 0; i2 < 256; ++i2) {
    byteToHex.push((i2 + 256).toString(16).slice(1));
  }
  function unsafeStringify(arr, offset = 0) {
    return byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + "-" + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + "-" + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + "-" + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + "-" + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]];
  }

  // node_modules/uuid/dist/esm-browser/native.js
  var randomUUID = typeof crypto !== "undefined" && crypto.randomUUID && crypto.randomUUID.bind(crypto);
  var native_default = {
    randomUUID
  };

  // node_modules/uuid/dist/esm-browser/v4.js
  function v4(options, buf, offset) {
    if (native_default.randomUUID && !buf && !options) {
      return native_default.randomUUID();
    }
    options = options || {};
    const rnds = options.random || (options.rng || rng)();
    rnds[6] = rnds[6] & 15 | 64;
    rnds[8] = rnds[8] & 63 | 128;
    if (buf) {
      offset = offset || 0;
      for (let i2 = 0; i2 < 16; ++i2) {
        buf[offset + i2] = rnds[i2];
      }
      return buf;
    }
    return unsafeStringify(rnds);
  }
  var v4_default = v4;

  // packages/blocks/build-module/api/factory.js
  var import_hooks2 = __toESM(require_hooks());
  function createBlock(name, attributes = {}, innerBlocks = []) {
    if (!isBlockRegistered(name)) {
      return createBlock("core/missing", {
        originalName: name,
        originalContent: "",
        originalUndelimitedContent: ""
      });
    }
    const sanitizedAttributes = __experimentalSanitizeBlockAttributes(
      name,
      attributes
    );
    const clientId = v4_default();
    return {
      clientId,
      name,
      isValid: true,
      attributes: sanitizedAttributes,
      innerBlocks
    };
  }
  function createBlocksFromInnerBlocksTemplate(innerBlocksOrTemplate = []) {
    return innerBlocksOrTemplate.map((innerBlock) => {
      const innerBlockTemplate = Array.isArray(innerBlock) ? innerBlock : [
        innerBlock.name,
        innerBlock.attributes,
        innerBlock.innerBlocks
      ];
      const [name, attributes, innerBlocks = []] = innerBlockTemplate;
      return createBlock(
        name,
        attributes,
        createBlocksFromInnerBlocksTemplate(innerBlocks)
      );
    });
  }
  function __experimentalCloneSanitizedBlock(block, mergeAttributes = {}, newInnerBlocks) {
    const { name } = block;
    if (!isBlockRegistered(name)) {
      return createBlock("core/missing", {
        originalName: name,
        originalContent: "",
        originalUndelimitedContent: ""
      });
    }
    const clientId = v4_default();
    const sanitizedAttributes = __experimentalSanitizeBlockAttributes(name, {
      ...block.attributes,
      ...mergeAttributes
    });
    return {
      ...block,
      clientId,
      attributes: sanitizedAttributes,
      innerBlocks: newInnerBlocks || block.innerBlocks.map(
        (innerBlock) => __experimentalCloneSanitizedBlock(innerBlock)
      )
    };
  }
  function cloneBlock(block, mergeAttributes = {}, newInnerBlocks) {
    const clientId = v4_default();
    return {
      ...block,
      clientId,
      attributes: {
        ...block.attributes,
        ...mergeAttributes
      },
      innerBlocks: newInnerBlocks || block.innerBlocks.map((innerBlock) => cloneBlock(innerBlock))
    };
  }
  var isPossibleTransformForSource = (transform, direction, blocks) => {
    if (!blocks.length) {
      return false;
    }
    const isMultiBlock = blocks.length > 1;
    const firstBlockName = blocks[0].name;
    const isValidForMultiBlocks = isWildcardBlockTransform(transform) || !isMultiBlock || transform.isMultiBlock;
    if (!isValidForMultiBlocks) {
      return false;
    }
    if (!isWildcardBlockTransform(transform) && !blocks.every((block) => block.name === firstBlockName)) {
      return false;
    }
    const isBlockType = transform.type === "block";
    if (!isBlockType) {
      return false;
    }
    const sourceBlock = blocks[0];
    const hasMatchingName = direction !== "from" || transform.blocks.indexOf(sourceBlock.name) !== -1 || isWildcardBlockTransform(transform);
    if (!hasMatchingName) {
      return false;
    }
    if (!isMultiBlock && direction === "from" && isContainerGroupBlock(sourceBlock.name) && isContainerGroupBlock(transform.blockName)) {
      return false;
    }
    if (!maybeCheckTransformIsMatch(transform, blocks)) {
      return false;
    }
    return true;
  };
  var getBlockTypesForPossibleFromTransforms = (blocks) => {
    if (!blocks.length) {
      return [];
    }
    const allBlockTypes = getBlockTypes();
    const blockTypesWithPossibleFromTransforms = allBlockTypes.filter(
      (blockType) => {
        const fromTransforms = getBlockTransforms("from", blockType.name);
        return !!findTransform(fromTransforms, (transform) => {
          return isPossibleTransformForSource(
            transform,
            "from",
            blocks
          );
        });
      }
    );
    return blockTypesWithPossibleFromTransforms;
  };
  var getBlockTypesForPossibleToTransforms = (blocks) => {
    if (!blocks.length) {
      return [];
    }
    const sourceBlock = blocks[0];
    const blockType = getBlockType(sourceBlock.name);
    const transformsTo = blockType ? getBlockTransforms("to", blockType.name) : [];
    const possibleTransforms = transformsTo.filter((transform) => {
      return transform && isPossibleTransformForSource(transform, "to", blocks);
    });
    const blockNames = possibleTransforms.map((transformation) => transformation.blocks).flat();
    return blockNames.map(getBlockType);
  };
  var isWildcardBlockTransform = (t3) => t3 && t3.type === "block" && Array.isArray(t3.blocks) && t3.blocks.includes("*");
  var isContainerGroupBlock = (name) => name === getGroupingBlockName();
  function getPossibleBlockTransformations(blocks) {
    if (!blocks.length) {
      return [];
    }
    const blockTypesForFromTransforms = getBlockTypesForPossibleFromTransforms(blocks);
    const blockTypesForToTransforms = getBlockTypesForPossibleToTransforms(blocks);
    return [
      .../* @__PURE__ */ new Set([
        ...blockTypesForFromTransforms,
        ...blockTypesForToTransforms
      ])
    ];
  }
  function findTransform(transforms, predicate) {
    const hooks = (0, import_hooks2.createHooks)();
    for (let i2 = 0; i2 < transforms.length; i2++) {
      const candidate = transforms[i2];
      if (predicate(candidate)) {
        hooks.addFilter(
          "transform",
          "transform/" + i2.toString(),
          (result) => result ? result : candidate,
          candidate.priority
        );
      }
    }
    return hooks.applyFilters("transform", null);
  }
  function getBlockTransforms(direction, blockTypeOrName) {
    if (blockTypeOrName === void 0) {
      return getBlockTypes().map(({ name }) => getBlockTransforms(direction, name)).flat();
    }
    const blockType = normalizeBlockType(blockTypeOrName);
    const { name: blockName, transforms } = blockType || {};
    if (!transforms || !Array.isArray(transforms[direction])) {
      return [];
    }
    const usingMobileTransformations = transforms.supportedMobileTransforms && Array.isArray(transforms.supportedMobileTransforms);
    const filteredTransforms = usingMobileTransformations ? transforms[direction].filter((t3) => {
      if (t3.type === "raw") {
        return true;
      }
      if (t3.type === "prefix") {
        return true;
      }
      if (!t3.blocks || !t3.blocks.length) {
        return false;
      }
      if (isWildcardBlockTransform(t3)) {
        return true;
      }
      return t3.blocks.every(
        (transformBlockName) => transforms.supportedMobileTransforms.includes(
          transformBlockName
        )
      );
    }) : transforms[direction];
    return filteredTransforms.map((transform) => ({
      ...transform,
      blockName,
      usingMobileTransformations
    }));
  }
  function maybeCheckTransformIsMatch(transform, blocks) {
    if (typeof transform.isMatch !== "function") {
      return true;
    }
    const sourceBlock = blocks[0];
    const attributes = transform.isMultiBlock ? blocks.map((block2) => block2.attributes) : sourceBlock.attributes;
    const block = transform.isMultiBlock ? blocks : sourceBlock;
    return transform.isMatch(attributes, block);
  }
  function switchToBlockType(blocks, name) {
    const blocksArray = Array.isArray(blocks) ? blocks : [blocks];
    const isMultiBlock = blocksArray.length > 1;
    const firstBlock = blocksArray[0];
    const sourceName = firstBlock.name;
    const transformationsFrom = getBlockTransforms("from", name);
    const transformationsTo = getBlockTransforms("to", sourceName);
    const transformation = findTransform(
      transformationsTo,
      (t3) => t3.type === "block" && (isWildcardBlockTransform(t3) || t3.blocks.indexOf(name) !== -1) && (!isMultiBlock || t3.isMultiBlock) && maybeCheckTransformIsMatch(t3, blocksArray)
    ) || findTransform(
      transformationsFrom,
      (t3) => t3.type === "block" && (isWildcardBlockTransform(t3) || t3.blocks.indexOf(sourceName) !== -1) && (!isMultiBlock || t3.isMultiBlock) && maybeCheckTransformIsMatch(t3, blocksArray)
    );
    if (!transformation) {
      return null;
    }
    let transformationResults;
    if (transformation.isMultiBlock) {
      if ("__experimentalConvert" in transformation) {
        transformationResults = transformation.__experimentalConvert(blocksArray);
      } else {
        transformationResults = transformation.transform(
          blocksArray.map((currentBlock) => currentBlock.attributes),
          blocksArray.map((currentBlock) => currentBlock.innerBlocks)
        );
      }
    } else if ("__experimentalConvert" in transformation) {
      transformationResults = transformation.__experimentalConvert(firstBlock);
    } else {
      transformationResults = transformation.transform(
        firstBlock.attributes,
        firstBlock.innerBlocks
      );
    }
    if (transformationResults === null || typeof transformationResults !== "object") {
      return null;
    }
    transformationResults = Array.isArray(transformationResults) ? transformationResults : [transformationResults];
    if (transformationResults.some(
      (result) => !getBlockType(result.name)
    )) {
      return null;
    }
    const hasSwitchedBlock = transformationResults.some(
      (result) => result.name === name
    );
    if (!hasSwitchedBlock) {
      return null;
    }
    const ret = transformationResults.map((result, index, results) => {
      return (0, import_hooks2.applyFilters)(
        "blocks.switchToBlockType.transformedBlock",
        result,
        blocks,
        index,
        results
      );
    });
    return ret;
  }
  var getBlockFromExample = (name, example) => createBlock(
    name,
    example.attributes,
    (example.innerBlocks ?? []).map(
      (innerBlock) => getBlockFromExample(innerBlock.name, innerBlock)
    )
  );

  // packages/blocks/build-module/api/parser/index.js
  var import_block_serialization_default_parser = __toESM(require_block_serialization_default_parser());
  var import_autop2 = __toESM(require_autop());

  // packages/blocks/build-module/api/serializer.js
  var import_element2 = __toESM(require_element());
  var import_hooks3 = __toESM(require_hooks());
  var import_is_shallow_equal = __toESM(require_is_shallow_equal());
  var import_autop = __toESM(require_autop());
  var import_deprecated6 = __toESM(require_deprecated());

  // packages/blocks/build-module/api/parser/serialize-raw-block.js
  function serializeRawBlock(rawBlock, options = {}) {
    const { isCommentDelimited = true } = options;
    const {
      blockName,
      attrs = {},
      innerBlocks = [],
      innerContent = []
    } = rawBlock;
    let childIndex = 0;
    const content = innerContent.map(
      (item) => (
        // `null` denotes a nested block, otherwise we have an HTML fragment.
        item !== null ? item : serializeRawBlock(innerBlocks[childIndex++], options)
      )
    ).join("\n").replace(/\n+/g, "\n").trim();
    return isCommentDelimited ? getCommentDelimitedContent(blockName, attrs, content) : content;
  }

  // packages/blocks/build-module/api/serializer.js
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  function getBlockDefaultClassName(blockName) {
    const className = "wp-block-" + blockName.replace(/\//, "-").replace(/^core-/, "");
    return (0, import_hooks3.applyFilters)(
      "blocks.getBlockDefaultClassName",
      className,
      blockName
    );
  }
  function getBlockMenuDefaultClassName(blockName) {
    const className = "editor-block-list-item-" + blockName.replace(/\//, "-").replace(/^core-/, "");
    return (0, import_hooks3.applyFilters)(
      "blocks.getBlockMenuDefaultClassName",
      className,
      blockName
    );
  }
  var blockPropsProvider = {};
  var innerBlocksPropsProvider = {};
  function getBlockProps(props = {}) {
    const { blockType, attributes } = blockPropsProvider;
    return getBlockProps.skipFilters ? props : (0, import_hooks3.applyFilters)(
      "blocks.getSaveContent.extraProps",
      { ...props },
      blockType,
      attributes
    );
  }
  function getInnerBlocksProps(props = {}) {
    const { innerBlocks } = innerBlocksPropsProvider;
    if (!Array.isArray(innerBlocks)) {
      return { ...props, children: innerBlocks };
    }
    const html2 = serialize(innerBlocks, { isInnerBlocks: true });
    const children = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_element2.RawHTML, { children: html2 });
    return { ...props, children };
  }
  function getSaveElement(blockTypeOrName, attributes, innerBlocks = []) {
    const blockType = normalizeBlockType(blockTypeOrName);
    if (!blockType?.save) {
      return null;
    }
    let { save } = blockType;
    if (save.prototype instanceof import_element2.Component) {
      const instance = new save({ attributes });
      save = instance.render.bind(instance);
    }
    blockPropsProvider.blockType = blockType;
    blockPropsProvider.attributes = attributes;
    innerBlocksPropsProvider.innerBlocks = innerBlocks;
    let element = save({ attributes, innerBlocks });
    if (element !== null && typeof element === "object" && (0, import_hooks3.hasFilter)("blocks.getSaveContent.extraProps") && !(blockType.apiVersion > 1)) {
      const props = (0, import_hooks3.applyFilters)(
        "blocks.getSaveContent.extraProps",
        { ...element.props },
        blockType,
        attributes
      );
      if (!(0, import_is_shallow_equal.default)(props, element.props)) {
        element = (0, import_element2.cloneElement)(element, props);
      }
    }
    return (0, import_hooks3.applyFilters)(
      "blocks.getSaveElement",
      element,
      blockType,
      attributes
    );
  }
  function getSaveContent(blockTypeOrName, attributes, innerBlocks) {
    const blockType = normalizeBlockType(blockTypeOrName);
    return (0, import_element2.renderToString)(
      getSaveElement(blockType, attributes, innerBlocks)
    );
  }
  function getCommentAttributes(blockType, attributes) {
    return Object.entries(blockType.attributes ?? {}).reduce(
      (accumulator, [key, attributeSchema]) => {
        const value = attributes[key];
        if (void 0 === value) {
          return accumulator;
        }
        if (attributeSchema.source !== void 0) {
          return accumulator;
        }
        if (attributeSchema.role === "local") {
          return accumulator;
        }
        if (attributeSchema.__experimentalRole === "local") {
          (0, import_deprecated6.default)("__experimentalRole attribute", {
            since: "6.7",
            version: "6.8",
            alternative: "role attribute",
            hint: `Check the block.json of the ${blockType?.name} block.`
          });
          return accumulator;
        }
        if ("default" in attributeSchema && JSON.stringify(attributeSchema.default) === JSON.stringify(value)) {
          return accumulator;
        }
        accumulator[key] = value;
        return accumulator;
      },
      {}
    );
  }
  function serializeAttributes(attributes) {
    return JSON.stringify(attributes).replaceAll("\\\\", "\\u005c").replaceAll("--", "\\u002d\\u002d").replaceAll("<", "\\u003c").replaceAll(">", "\\u003e").replaceAll("&", "\\u0026").replaceAll('\\"', "\\u0022");
  }
  function getBlockInnerHTML(block) {
    let saveContent = block.originalContent;
    if (block.isValid || block.innerBlocks.length) {
      try {
        saveContent = getSaveContent(
          block.name,
          block.attributes,
          block.innerBlocks
        );
      } catch (error) {
      }
    }
    return saveContent;
  }
  function getCommentDelimitedContent(rawBlockName, attributes, content) {
    const serializedAttributes = attributes && Object.entries(attributes).length ? serializeAttributes(attributes) + " " : "";
    const blockName = rawBlockName?.startsWith("core/") ? rawBlockName.slice(5) : rawBlockName;
    if (!content) {
      return `<!-- wp:${blockName} ${serializedAttributes}/-->`;
    }
    return `<!-- wp:${blockName} ${serializedAttributes}-->
` + content + `
<!-- /wp:${blockName} -->`;
  }
  function serializeBlock(block, { isInnerBlocks = false } = {}) {
    if (!block.isValid && block.__unstableBlockSource) {
      return serializeRawBlock(block.__unstableBlockSource);
    }
    const blockName = block.name;
    const saveContent = getBlockInnerHTML(block);
    if (blockName === getUnregisteredTypeHandlerName() || !isInnerBlocks && blockName === getFreeformContentHandlerName()) {
      return saveContent;
    }
    const blockType = getBlockType(blockName);
    if (!blockType) {
      return saveContent;
    }
    const saveAttributes = getCommentAttributes(blockType, block.attributes);
    return getCommentDelimitedContent(blockName, saveAttributes, saveContent);
  }
  function __unstableSerializeAndClean(blocks) {
    if (blocks.length === 1 && isUnmodifiedDefaultBlock(blocks[0])) {
      blocks = [];
    }
    let content = serialize(blocks);
    if (blocks.length === 1 && blocks[0].name === getFreeformContentHandlerName() && blocks[0].name === "core/freeform") {
      content = (0, import_autop.removep)(content);
    }
    return content;
  }
  function serialize(blocks, options) {
    const blocksArray = Array.isArray(blocks) ? blocks : [blocks];
    return blocksArray.map((block) => serializeBlock(block, options)).join("\n\n");
  }

  // node_modules/simple-html-tokenizer/dist/es6/index.js
  var HEXCHARCODE = /^#[xX]([A-Fa-f0-9]+)$/;
  var CHARCODE = /^#([0-9]+)$/;
  var NAMED = /^([A-Za-z0-9]+)$/;
  var EntityParser = (
    /** @class */
    (function() {
      function EntityParser2(named) {
        this.named = named;
      }
      EntityParser2.prototype.parse = function(entity) {
        if (!entity) {
          return;
        }
        var matches = entity.match(HEXCHARCODE);
        if (matches) {
          return String.fromCharCode(parseInt(matches[1], 16));
        }
        matches = entity.match(CHARCODE);
        if (matches) {
          return String.fromCharCode(parseInt(matches[1], 10));
        }
        matches = entity.match(NAMED);
        if (matches) {
          return this.named[matches[1]];
        }
      };
      return EntityParser2;
    })()
  );
  var WSP = /[\t\n\f ]/;
  var ALPHA = /[A-Za-z]/;
  var CRLF = /\r\n?/g;
  function isSpace(char) {
    return WSP.test(char);
  }
  function isAlpha(char) {
    return ALPHA.test(char);
  }
  function preprocessInput(input) {
    return input.replace(CRLF, "\n");
  }
  var EventedTokenizer = (
    /** @class */
    (function() {
      function EventedTokenizer2(delegate, entityParser) {
        this.delegate = delegate;
        this.entityParser = entityParser;
        this.state = "beforeData";
        this.line = -1;
        this.column = -1;
        this.input = "";
        this.index = -1;
        this.tagNameBuffer = "";
        this.states = {
          beforeData: function() {
            var char = this.peek();
            if (char === "<") {
              this.transitionTo(
                "tagOpen"
                /* tagOpen */
              );
              this.markTagStart();
              this.consume();
            } else {
              if (char === "\n") {
                var tag = this.tagNameBuffer.toLowerCase();
                if (tag === "pre" || tag === "textarea") {
                  this.consume();
                }
              }
              this.transitionTo(
                "data"
                /* data */
              );
              this.delegate.beginData();
            }
          },
          data: function() {
            var char = this.peek();
            if (char === "<") {
              this.delegate.finishData();
              this.transitionTo(
                "tagOpen"
                /* tagOpen */
              );
              this.markTagStart();
              this.consume();
            } else if (char === "&") {
              this.consume();
              this.delegate.appendToData(this.consumeCharRef() || "&");
            } else {
              this.consume();
              this.delegate.appendToData(char);
            }
          },
          tagOpen: function() {
            var char = this.consume();
            if (char === "!") {
              this.transitionTo(
                "markupDeclarationOpen"
                /* markupDeclarationOpen */
              );
            } else if (char === "/") {
              this.transitionTo(
                "endTagOpen"
                /* endTagOpen */
              );
            } else if (char === "@" || char === ":" || isAlpha(char)) {
              this.transitionTo(
                "tagName"
                /* tagName */
              );
              this.tagNameBuffer = "";
              this.delegate.beginStartTag();
              this.appendToTagName(char);
            }
          },
          markupDeclarationOpen: function() {
            var char = this.consume();
            if (char === "-" && this.input.charAt(this.index) === "-") {
              this.consume();
              this.transitionTo(
                "commentStart"
                /* commentStart */
              );
              this.delegate.beginComment();
            }
          },
          commentStart: function() {
            var char = this.consume();
            if (char === "-") {
              this.transitionTo(
                "commentStartDash"
                /* commentStartDash */
              );
            } else if (char === ">") {
              this.delegate.finishComment();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else {
              this.delegate.appendToCommentData(char);
              this.transitionTo(
                "comment"
                /* comment */
              );
            }
          },
          commentStartDash: function() {
            var char = this.consume();
            if (char === "-") {
              this.transitionTo(
                "commentEnd"
                /* commentEnd */
              );
            } else if (char === ">") {
              this.delegate.finishComment();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else {
              this.delegate.appendToCommentData("-");
              this.transitionTo(
                "comment"
                /* comment */
              );
            }
          },
          comment: function() {
            var char = this.consume();
            if (char === "-") {
              this.transitionTo(
                "commentEndDash"
                /* commentEndDash */
              );
            } else {
              this.delegate.appendToCommentData(char);
            }
          },
          commentEndDash: function() {
            var char = this.consume();
            if (char === "-") {
              this.transitionTo(
                "commentEnd"
                /* commentEnd */
              );
            } else {
              this.delegate.appendToCommentData("-" + char);
              this.transitionTo(
                "comment"
                /* comment */
              );
            }
          },
          commentEnd: function() {
            var char = this.consume();
            if (char === ">") {
              this.delegate.finishComment();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else {
              this.delegate.appendToCommentData("--" + char);
              this.transitionTo(
                "comment"
                /* comment */
              );
            }
          },
          tagName: function() {
            var char = this.consume();
            if (isSpace(char)) {
              this.transitionTo(
                "beforeAttributeName"
                /* beforeAttributeName */
              );
            } else if (char === "/") {
              this.transitionTo(
                "selfClosingStartTag"
                /* selfClosingStartTag */
              );
            } else if (char === ">") {
              this.delegate.finishTag();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else {
              this.appendToTagName(char);
            }
          },
          beforeAttributeName: function() {
            var char = this.peek();
            if (isSpace(char)) {
              this.consume();
              return;
            } else if (char === "/") {
              this.transitionTo(
                "selfClosingStartTag"
                /* selfClosingStartTag */
              );
              this.consume();
            } else if (char === ">") {
              this.consume();
              this.delegate.finishTag();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else if (char === "=") {
              this.delegate.reportSyntaxError("attribute name cannot start with equals sign");
              this.transitionTo(
                "attributeName"
                /* attributeName */
              );
              this.delegate.beginAttribute();
              this.consume();
              this.delegate.appendToAttributeName(char);
            } else {
              this.transitionTo(
                "attributeName"
                /* attributeName */
              );
              this.delegate.beginAttribute();
            }
          },
          attributeName: function() {
            var char = this.peek();
            if (isSpace(char)) {
              this.transitionTo(
                "afterAttributeName"
                /* afterAttributeName */
              );
              this.consume();
            } else if (char === "/") {
              this.delegate.beginAttributeValue(false);
              this.delegate.finishAttributeValue();
              this.consume();
              this.transitionTo(
                "selfClosingStartTag"
                /* selfClosingStartTag */
              );
            } else if (char === "=") {
              this.transitionTo(
                "beforeAttributeValue"
                /* beforeAttributeValue */
              );
              this.consume();
            } else if (char === ">") {
              this.delegate.beginAttributeValue(false);
              this.delegate.finishAttributeValue();
              this.consume();
              this.delegate.finishTag();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else if (char === '"' || char === "'" || char === "<") {
              this.delegate.reportSyntaxError(char + " is not a valid character within attribute names");
              this.consume();
              this.delegate.appendToAttributeName(char);
            } else {
              this.consume();
              this.delegate.appendToAttributeName(char);
            }
          },
          afterAttributeName: function() {
            var char = this.peek();
            if (isSpace(char)) {
              this.consume();
              return;
            } else if (char === "/") {
              this.delegate.beginAttributeValue(false);
              this.delegate.finishAttributeValue();
              this.consume();
              this.transitionTo(
                "selfClosingStartTag"
                /* selfClosingStartTag */
              );
            } else if (char === "=") {
              this.consume();
              this.transitionTo(
                "beforeAttributeValue"
                /* beforeAttributeValue */
              );
            } else if (char === ">") {
              this.delegate.beginAttributeValue(false);
              this.delegate.finishAttributeValue();
              this.consume();
              this.delegate.finishTag();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else {
              this.delegate.beginAttributeValue(false);
              this.delegate.finishAttributeValue();
              this.transitionTo(
                "attributeName"
                /* attributeName */
              );
              this.delegate.beginAttribute();
              this.consume();
              this.delegate.appendToAttributeName(char);
            }
          },
          beforeAttributeValue: function() {
            var char = this.peek();
            if (isSpace(char)) {
              this.consume();
            } else if (char === '"') {
              this.transitionTo(
                "attributeValueDoubleQuoted"
                /* attributeValueDoubleQuoted */
              );
              this.delegate.beginAttributeValue(true);
              this.consume();
            } else if (char === "'") {
              this.transitionTo(
                "attributeValueSingleQuoted"
                /* attributeValueSingleQuoted */
              );
              this.delegate.beginAttributeValue(true);
              this.consume();
            } else if (char === ">") {
              this.delegate.beginAttributeValue(false);
              this.delegate.finishAttributeValue();
              this.consume();
              this.delegate.finishTag();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else {
              this.transitionTo(
                "attributeValueUnquoted"
                /* attributeValueUnquoted */
              );
              this.delegate.beginAttributeValue(false);
              this.consume();
              this.delegate.appendToAttributeValue(char);
            }
          },
          attributeValueDoubleQuoted: function() {
            var char = this.consume();
            if (char === '"') {
              this.delegate.finishAttributeValue();
              this.transitionTo(
                "afterAttributeValueQuoted"
                /* afterAttributeValueQuoted */
              );
            } else if (char === "&") {
              this.delegate.appendToAttributeValue(this.consumeCharRef() || "&");
            } else {
              this.delegate.appendToAttributeValue(char);
            }
          },
          attributeValueSingleQuoted: function() {
            var char = this.consume();
            if (char === "'") {
              this.delegate.finishAttributeValue();
              this.transitionTo(
                "afterAttributeValueQuoted"
                /* afterAttributeValueQuoted */
              );
            } else if (char === "&") {
              this.delegate.appendToAttributeValue(this.consumeCharRef() || "&");
            } else {
              this.delegate.appendToAttributeValue(char);
            }
          },
          attributeValueUnquoted: function() {
            var char = this.peek();
            if (isSpace(char)) {
              this.delegate.finishAttributeValue();
              this.consume();
              this.transitionTo(
                "beforeAttributeName"
                /* beforeAttributeName */
              );
            } else if (char === "/") {
              this.delegate.finishAttributeValue();
              this.consume();
              this.transitionTo(
                "selfClosingStartTag"
                /* selfClosingStartTag */
              );
            } else if (char === "&") {
              this.consume();
              this.delegate.appendToAttributeValue(this.consumeCharRef() || "&");
            } else if (char === ">") {
              this.delegate.finishAttributeValue();
              this.consume();
              this.delegate.finishTag();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else {
              this.consume();
              this.delegate.appendToAttributeValue(char);
            }
          },
          afterAttributeValueQuoted: function() {
            var char = this.peek();
            if (isSpace(char)) {
              this.consume();
              this.transitionTo(
                "beforeAttributeName"
                /* beforeAttributeName */
              );
            } else if (char === "/") {
              this.consume();
              this.transitionTo(
                "selfClosingStartTag"
                /* selfClosingStartTag */
              );
            } else if (char === ">") {
              this.consume();
              this.delegate.finishTag();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else {
              this.transitionTo(
                "beforeAttributeName"
                /* beforeAttributeName */
              );
            }
          },
          selfClosingStartTag: function() {
            var char = this.peek();
            if (char === ">") {
              this.consume();
              this.delegate.markTagAsSelfClosing();
              this.delegate.finishTag();
              this.transitionTo(
                "beforeData"
                /* beforeData */
              );
            } else {
              this.transitionTo(
                "beforeAttributeName"
                /* beforeAttributeName */
              );
            }
          },
          endTagOpen: function() {
            var char = this.consume();
            if (char === "@" || char === ":" || isAlpha(char)) {
              this.transitionTo(
                "tagName"
                /* tagName */
              );
              this.tagNameBuffer = "";
              this.delegate.beginEndTag();
              this.appendToTagName(char);
            }
          }
        };
        this.reset();
      }
      EventedTokenizer2.prototype.reset = function() {
        this.transitionTo(
          "beforeData"
          /* beforeData */
        );
        this.input = "";
        this.index = 0;
        this.line = 1;
        this.column = 0;
        this.delegate.reset();
      };
      EventedTokenizer2.prototype.transitionTo = function(state) {
        this.state = state;
      };
      EventedTokenizer2.prototype.tokenize = function(input) {
        this.reset();
        this.tokenizePart(input);
        this.tokenizeEOF();
      };
      EventedTokenizer2.prototype.tokenizePart = function(input) {
        this.input += preprocessInput(input);
        while (this.index < this.input.length) {
          var handler = this.states[this.state];
          if (handler !== void 0) {
            handler.call(this);
          } else {
            throw new Error("unhandled state " + this.state);
          }
        }
      };
      EventedTokenizer2.prototype.tokenizeEOF = function() {
        this.flushData();
      };
      EventedTokenizer2.prototype.flushData = function() {
        if (this.state === "data") {
          this.delegate.finishData();
          this.transitionTo(
            "beforeData"
            /* beforeData */
          );
        }
      };
      EventedTokenizer2.prototype.peek = function() {
        return this.input.charAt(this.index);
      };
      EventedTokenizer2.prototype.consume = function() {
        var char = this.peek();
        this.index++;
        if (char === "\n") {
          this.line++;
          this.column = 0;
        } else {
          this.column++;
        }
        return char;
      };
      EventedTokenizer2.prototype.consumeCharRef = function() {
        var endIndex = this.input.indexOf(";", this.index);
        if (endIndex === -1) {
          return;
        }
        var entity = this.input.slice(this.index, endIndex);
        var chars = this.entityParser.parse(entity);
        if (chars) {
          var count = entity.length;
          while (count) {
            this.consume();
            count--;
          }
          this.consume();
          return chars;
        }
      };
      EventedTokenizer2.prototype.markTagStart = function() {
        this.delegate.tagOpen();
      };
      EventedTokenizer2.prototype.appendToTagName = function(char) {
        this.tagNameBuffer += char;
        this.delegate.appendToTagName(char);
      };
      return EventedTokenizer2;
    })()
  );
  var Tokenizer = (
    /** @class */
    (function() {
      function Tokenizer2(entityParser, options) {
        if (options === void 0) {
          options = {};
        }
        this.options = options;
        this.token = null;
        this.startLine = 1;
        this.startColumn = 0;
        this.tokens = [];
        this.tokenizer = new EventedTokenizer(this, entityParser);
        this._currentAttribute = void 0;
      }
      Tokenizer2.prototype.tokenize = function(input) {
        this.tokens = [];
        this.tokenizer.tokenize(input);
        return this.tokens;
      };
      Tokenizer2.prototype.tokenizePart = function(input) {
        this.tokens = [];
        this.tokenizer.tokenizePart(input);
        return this.tokens;
      };
      Tokenizer2.prototype.tokenizeEOF = function() {
        this.tokens = [];
        this.tokenizer.tokenizeEOF();
        return this.tokens[0];
      };
      Tokenizer2.prototype.reset = function() {
        this.token = null;
        this.startLine = 1;
        this.startColumn = 0;
      };
      Tokenizer2.prototype.current = function() {
        var token = this.token;
        if (token === null) {
          throw new Error("token was unexpectedly null");
        }
        if (arguments.length === 0) {
          return token;
        }
        for (var i2 = 0; i2 < arguments.length; i2++) {
          if (token.type === arguments[i2]) {
            return token;
          }
        }
        throw new Error("token type was unexpectedly " + token.type);
      };
      Tokenizer2.prototype.push = function(token) {
        this.token = token;
        this.tokens.push(token);
      };
      Tokenizer2.prototype.currentAttribute = function() {
        return this._currentAttribute;
      };
      Tokenizer2.prototype.addLocInfo = function() {
        if (this.options.loc) {
          this.current().loc = {
            start: {
              line: this.startLine,
              column: this.startColumn
            },
            end: {
              line: this.tokenizer.line,
              column: this.tokenizer.column
            }
          };
        }
        this.startLine = this.tokenizer.line;
        this.startColumn = this.tokenizer.column;
      };
      Tokenizer2.prototype.beginData = function() {
        this.push({
          type: "Chars",
          chars: ""
        });
      };
      Tokenizer2.prototype.appendToData = function(char) {
        this.current(
          "Chars"
          /* Chars */
        ).chars += char;
      };
      Tokenizer2.prototype.finishData = function() {
        this.addLocInfo();
      };
      Tokenizer2.prototype.beginComment = function() {
        this.push({
          type: "Comment",
          chars: ""
        });
      };
      Tokenizer2.prototype.appendToCommentData = function(char) {
        this.current(
          "Comment"
          /* Comment */
        ).chars += char;
      };
      Tokenizer2.prototype.finishComment = function() {
        this.addLocInfo();
      };
      Tokenizer2.prototype.tagOpen = function() {
      };
      Tokenizer2.prototype.beginStartTag = function() {
        this.push({
          type: "StartTag",
          tagName: "",
          attributes: [],
          selfClosing: false
        });
      };
      Tokenizer2.prototype.beginEndTag = function() {
        this.push({
          type: "EndTag",
          tagName: ""
        });
      };
      Tokenizer2.prototype.finishTag = function() {
        this.addLocInfo();
      };
      Tokenizer2.prototype.markTagAsSelfClosing = function() {
        this.current(
          "StartTag"
          /* StartTag */
        ).selfClosing = true;
      };
      Tokenizer2.prototype.appendToTagName = function(char) {
        this.current(
          "StartTag",
          "EndTag"
          /* EndTag */
        ).tagName += char;
      };
      Tokenizer2.prototype.beginAttribute = function() {
        this._currentAttribute = ["", "", false];
      };
      Tokenizer2.prototype.appendToAttributeName = function(char) {
        this.currentAttribute()[0] += char;
      };
      Tokenizer2.prototype.beginAttributeValue = function(isQuoted) {
        this.currentAttribute()[2] = isQuoted;
      };
      Tokenizer2.prototype.appendToAttributeValue = function(char) {
        this.currentAttribute()[1] += char;
      };
      Tokenizer2.prototype.finishAttributeValue = function() {
        this.current(
          "StartTag"
          /* StartTag */
        ).attributes.push(this._currentAttribute);
      };
      Tokenizer2.prototype.reportSyntaxError = function(message) {
        this.current().syntaxError = message;
      };
      return Tokenizer2;
    })()
  );

  // packages/blocks/build-module/api/validation/index.js
  var import_es6 = __toESM(require_es6());
  var import_deprecated7 = __toESM(require_deprecated());
  var import_html_entities = __toESM(require_html_entities());

  // packages/blocks/build-module/api/validation/logger.js
  function createLogger() {
    function createLogHandler(logger) {
      return (message, ...args) => logger("Block validation: " + message, ...args);
    }
    return {
      // eslint-disable-next-line no-console
      error: createLogHandler(console.error),
      // eslint-disable-next-line no-console
      warning: createLogHandler(console.warn),
      getItems() {
        return [];
      }
    };
  }
  function createQueuedLogger() {
    const queue = [];
    const logger = createLogger();
    return {
      error(...args) {
        queue.push({ log: logger.error, args });
      },
      warning(...args) {
        queue.push({ log: logger.warning, args });
      },
      getItems() {
        return queue;
      }
    };
  }

  // packages/blocks/build-module/api/validation/index.js
  var identity = (x2) => x2;
  var REGEXP_WHITESPACE = /[\t\n\r\v\f ]+/g;
  var REGEXP_ONLY_WHITESPACE = /^[\t\n\r\v\f ]*$/;
  var REGEXP_STYLE_URL_TYPE = /^url\s*\(['"\s]*(.*?)['"\s]*\)$/;
  var BOOLEAN_ATTRIBUTES = [
    "allowfullscreen",
    "allowpaymentrequest",
    "allowusermedia",
    "async",
    "autofocus",
    "autoplay",
    "checked",
    "controls",
    "default",
    "defer",
    "disabled",
    "download",
    "formnovalidate",
    "hidden",
    "ismap",
    "itemscope",
    "loop",
    "multiple",
    "muted",
    "nomodule",
    "novalidate",
    "open",
    "playsinline",
    "readonly",
    "required",
    "reversed",
    "selected",
    "typemustmatch"
  ];
  var ENUMERATED_ATTRIBUTES = [
    "autocapitalize",
    "autocomplete",
    "charset",
    "contenteditable",
    "crossorigin",
    "decoding",
    "dir",
    "draggable",
    "enctype",
    "formenctype",
    "formmethod",
    "http-equiv",
    "inputmode",
    "kind",
    "method",
    "preload",
    "scope",
    "shape",
    "spellcheck",
    "translate",
    "type",
    "wrap"
  ];
  var MEANINGFUL_ATTRIBUTES = [
    ...BOOLEAN_ATTRIBUTES,
    ...ENUMERATED_ATTRIBUTES
  ];
  var TEXT_NORMALIZATIONS = [identity, getTextWithCollapsedWhitespace];
  var REGEXP_NAMED_CHARACTER_REFERENCE = /^[\da-z]+$/i;
  var REGEXP_DECIMAL_CHARACTER_REFERENCE = /^#\d+$/;
  var REGEXP_HEXADECIMAL_CHARACTER_REFERENCE = /^#x[\da-f]+$/i;
  function isValidCharacterReference(text2) {
    return REGEXP_NAMED_CHARACTER_REFERENCE.test(text2) || REGEXP_DECIMAL_CHARACTER_REFERENCE.test(text2) || REGEXP_HEXADECIMAL_CHARACTER_REFERENCE.test(text2);
  }
  var DecodeEntityParser = class {
    /**
     * Returns a substitute string for an entity string sequence between `&`
     * and `;`, or undefined if no substitution should occur.
     *
     * @param {string} entity Entity fragment discovered in HTML.
     *
     * @return {string | undefined} Entity substitute value.
     */
    parse(entity) {
      if (isValidCharacterReference(entity)) {
        return (0, import_html_entities.decodeEntities)("&" + entity + ";");
      }
    }
  };
  function getTextPiecesSplitOnWhitespace(text2) {
    return text2.trim().split(REGEXP_WHITESPACE);
  }
  function getTextWithCollapsedWhitespace(text2) {
    return getTextPiecesSplitOnWhitespace(text2).join(" ");
  }
  function getMeaningfulAttributePairs(token) {
    return token.attributes.filter((pair) => {
      const [key, value] = pair;
      return value || key.indexOf("data-") === 0 || MEANINGFUL_ATTRIBUTES.includes(key);
    });
  }
  function isEquivalentTextTokens(actual, expected, logger = createLogger()) {
    let actualChars = actual.chars;
    let expectedChars = expected.chars;
    for (let i2 = 0; i2 < TEXT_NORMALIZATIONS.length; i2++) {
      const normalize = TEXT_NORMALIZATIONS[i2];
      actualChars = normalize(actualChars);
      expectedChars = normalize(expectedChars);
      if (actualChars === expectedChars) {
        return true;
      }
    }
    logger.warning(
      "Expected text `%s`, saw `%s`.",
      expected.chars,
      actual.chars
    );
    return false;
  }
  function getNormalizedLength(value) {
    if (0 === parseFloat(value)) {
      return "0";
    }
    if (value.indexOf(".") === 0) {
      return "0" + value;
    }
    return value;
  }
  function getNormalizedStyleValue(value) {
    const textPieces = getTextPiecesSplitOnWhitespace(value);
    const normalizedPieces = textPieces.map(getNormalizedLength);
    const result = normalizedPieces.join(" ");
    return result.replace(REGEXP_STYLE_URL_TYPE, "url($1)");
  }
  function getStyleProperties(text2) {
    const pairs = text2.replace(/;?\s*$/, "").split(";").map((style) => {
      const [key, ...valueParts] = style.split(":");
      const value = valueParts.join(":");
      return [key.trim(), getNormalizedStyleValue(value.trim())];
    });
    return Object.fromEntries(pairs);
  }
  var isEqualAttributesOfName = {
    class: (actual, expected) => {
      const [actualPieces, expectedPieces] = [actual, expected].map(
        getTextPiecesSplitOnWhitespace
      );
      const actualDiff = actualPieces.filter(
        (c2) => !expectedPieces.includes(c2)
      );
      const expectedDiff = expectedPieces.filter(
        (c2) => !actualPieces.includes(c2)
      );
      return actualDiff.length === 0 && expectedDiff.length === 0;
    },
    style: (actual, expected) => {
      return (0, import_es6.default)(
        ...[actual, expected].map(getStyleProperties)
      );
    },
    // For each boolean attribute, mere presence of attribute in both is enough
    // to assume equivalence.
    ...Object.fromEntries(
      BOOLEAN_ATTRIBUTES.map((attribute) => [attribute, () => true])
    )
  };
  function isEqualTagAttributePairs(actual, expected, logger = createLogger()) {
    if (actual.length !== expected.length) {
      logger.warning(
        "Expected attributes %o, instead saw %o.",
        expected,
        actual
      );
      return false;
    }
    const expectedAttributes = {};
    for (let i2 = 0; i2 < expected.length; i2++) {
      expectedAttributes[expected[i2][0].toLowerCase()] = expected[i2][1];
    }
    for (let i2 = 0; i2 < actual.length; i2++) {
      const [name, actualValue] = actual[i2];
      const nameLower = name.toLowerCase();
      if (!expectedAttributes.hasOwnProperty(nameLower)) {
        logger.warning("Encountered unexpected attribute `%s`.", name);
        return false;
      }
      const expectedValue = expectedAttributes[nameLower];
      const isEqualAttributes = isEqualAttributesOfName[nameLower];
      if (isEqualAttributes) {
        if (!isEqualAttributes(actualValue, expectedValue)) {
          logger.warning(
            "Expected attribute `%s` of value `%s`, saw `%s`.",
            name,
            expectedValue,
            actualValue
          );
          return false;
        }
      } else if (actualValue !== expectedValue) {
        logger.warning(
          "Expected attribute `%s` of value `%s`, saw `%s`.",
          name,
          expectedValue,
          actualValue
        );
        return false;
      }
    }
    return true;
  }
  var isEqualTokensOfType = {
    StartTag: (actual, expected, logger = createLogger()) => {
      if (actual.tagName !== expected.tagName && // Optimization: Use short-circuit evaluation to defer case-
      // insensitive check on the assumption that the majority case will
      // have exactly equal tag names.
      actual.tagName.toLowerCase() !== expected.tagName.toLowerCase()) {
        logger.warning(
          "Expected tag name `%s`, instead saw `%s`.",
          expected.tagName,
          actual.tagName
        );
        return false;
      }
      return isEqualTagAttributePairs(
        ...[actual, expected].map(getMeaningfulAttributePairs),
        logger
      );
    },
    Chars: isEquivalentTextTokens,
    Comment: isEquivalentTextTokens
  };
  function getNextNonWhitespaceToken(tokens) {
    let token;
    while (token = tokens.shift()) {
      if (token.type !== "Chars") {
        return token;
      }
      if (!REGEXP_ONLY_WHITESPACE.test(token.chars)) {
        return token;
      }
    }
  }
  function getHTMLTokens(html2, logger = createLogger()) {
    try {
      return new Tokenizer(new DecodeEntityParser()).tokenize(html2);
    } catch (e2) {
      logger.warning("Malformed HTML detected: %s", html2);
    }
    return null;
  }
  function isClosedByToken(currentToken, nextToken) {
    if (!currentToken.selfClosing) {
      return false;
    }
    if (nextToken && nextToken.tagName === currentToken.tagName && nextToken.type === "EndTag") {
      return true;
    }
    return false;
  }
  function isEquivalentHTML(actual, expected, logger = createLogger()) {
    if (actual === expected) {
      return true;
    }
    const [actualTokens, expectedTokens] = [actual, expected].map(
      (html2) => getHTMLTokens(html2, logger)
    );
    if (!actualTokens || !expectedTokens) {
      return false;
    }
    let actualToken, expectedToken;
    while (actualToken = getNextNonWhitespaceToken(actualTokens)) {
      expectedToken = getNextNonWhitespaceToken(expectedTokens);
      if (!expectedToken) {
        logger.warning(
          "Expected end of content, instead saw %o.",
          actualToken
        );
        return false;
      }
      if (actualToken.type !== expectedToken.type) {
        logger.warning(
          "Expected token of type `%s` (%o), instead saw `%s` (%o).",
          expectedToken.type,
          expectedToken,
          actualToken.type,
          actualToken
        );
        return false;
      }
      const isEqualTokens = isEqualTokensOfType[actualToken.type];
      if (isEqualTokens && !isEqualTokens(actualToken, expectedToken, logger)) {
        return false;
      }
      if (isClosedByToken(actualToken, expectedTokens[0])) {
        getNextNonWhitespaceToken(expectedTokens);
      } else if (isClosedByToken(expectedToken, actualTokens[0])) {
        getNextNonWhitespaceToken(actualTokens);
      }
    }
    if (expectedToken = getNextNonWhitespaceToken(expectedTokens)) {
      logger.warning(
        "Expected %o, instead saw end of content.",
        expectedToken
      );
      return false;
    }
    return true;
  }
  function validateBlock(block, blockTypeOrName = block.name) {
    const isFallbackBlock = block.name === getFreeformContentHandlerName() || block.name === getUnregisteredTypeHandlerName();
    if (isFallbackBlock) {
      return [true, []];
    }
    const logger = createQueuedLogger();
    const blockType = normalizeBlockType(blockTypeOrName);
    let generatedBlockContent;
    try {
      generatedBlockContent = getSaveContent(blockType, block.attributes);
    } catch (error) {
      logger.error(
        "Block validation failed because an error occurred while generating block content:\n\n%s",
        error.toString()
      );
      return [false, logger.getItems()];
    }
    const isValid = isEquivalentHTML(
      block.originalContent,
      generatedBlockContent,
      logger
    );
    if (!isValid) {
      logger.error(
        "Block validation failed for `%s` (%o).\n\nContent generated by `save` function:\n\n%s\n\nContent retrieved from post body:\n\n%s",
        blockType.name,
        blockType,
        generatedBlockContent,
        block.originalContent
      );
    }
    return [isValid, logger.getItems()];
  }
  function isValidBlockContent(blockTypeOrName, attributes, originalBlockContent) {
    (0, import_deprecated7.default)("isValidBlockContent introduces opportunity for data loss", {
      since: "12.6",
      plugin: "Gutenberg",
      alternative: "validateBlock"
    });
    const blockType = normalizeBlockType(blockTypeOrName);
    const block = {
      name: blockType.name,
      attributes,
      innerBlocks: [],
      originalContent: originalBlockContent
    };
    const [isValid] = validateBlock(block, blockType);
    return isValid;
  }

  // packages/blocks/build-module/api/parser/convert-legacy-block.js
  function convertLegacyBlockNameAndAttributes(name, attributes) {
    const newAttributes = { ...attributes };
    if ("core/cover-image" === name) {
      name = "core/cover";
    }
    if ("core/text" === name || "core/cover-text" === name) {
      name = "core/paragraph";
    }
    if (name && name.indexOf("core/social-link-") === 0) {
      newAttributes.service = name.substring(17);
      name = "core/social-link";
    }
    if (name && name.indexOf("core-embed/") === 0) {
      const providerSlug = name.substring(11);
      const deprecated12 = {
        speaker: "speaker-deck",
        polldaddy: "crowdsignal"
      };
      newAttributes.providerNameSlug = providerSlug in deprecated12 ? deprecated12[providerSlug] : providerSlug;
      if (!["amazon-kindle", "wordpress"].includes(providerSlug)) {
        newAttributes.responsive = true;
      }
      name = "core/embed";
    }
    if (name === "core/post-comment-author") {
      name = "core/comment-author-name";
    }
    if (name === "core/post-comment-content") {
      name = "core/comment-content";
    }
    if (name === "core/post-comment-date") {
      name = "core/comment-date";
    }
    if (name === "core/comments-query-loop") {
      name = "core/comments";
      const { className = "" } = newAttributes;
      if (!className.includes("wp-block-comments-query-loop")) {
        newAttributes.className = [
          "wp-block-comments-query-loop",
          className
        ].join(" ");
      }
    }
    if (name === "core/post-comments") {
      name = "core/comments";
      newAttributes.legacy = true;
    }
    if (attributes.layout?.type === "grid" && typeof attributes.layout?.columnCount === "string") {
      newAttributes.layout = {
        ...newAttributes.layout,
        columnCount: parseInt(attributes.layout.columnCount, 10)
      };
    }
    if (typeof attributes.style?.layout?.columnSpan === "string") {
      const columnSpanNumber = parseInt(
        attributes.style.layout.columnSpan,
        10
      );
      newAttributes.style = {
        ...newAttributes.style,
        layout: {
          ...newAttributes.style.layout,
          columnSpan: isNaN(columnSpanNumber) ? void 0 : columnSpanNumber
        }
      };
    }
    if (typeof attributes.style?.layout?.rowSpan === "string") {
      const rowSpanNumber = parseInt(attributes.style.layout.rowSpan, 10);
      newAttributes.style = {
        ...newAttributes.style,
        layout: {
          ...newAttributes.style.layout,
          rowSpan: isNaN(rowSpanNumber) ? void 0 : rowSpanNumber
        }
      };
    }
    return [name, newAttributes];
  }

  // node_modules/hpq/es/get-path.js
  function getPath(object, path) {
    var segments = path.split(".");
    var segment;
    while (segment = segments.shift()) {
      if (!(segment in object)) {
        return;
      }
      object = object[segment];
    }
    return object;
  }

  // node_modules/hpq/es/index.js
  var getDocument = /* @__PURE__ */ (function() {
    var doc;
    return function() {
      if (!doc) {
        doc = document.implementation.createHTMLDocument("");
      }
      return doc;
    };
  })();
  function parse(source, matchers) {
    if (!matchers) {
      return;
    }
    if ("string" === typeof source) {
      var doc = getDocument();
      doc.body.innerHTML = source;
      source = doc.body;
    }
    if ("function" === typeof matchers) {
      return matchers(source);
    }
    if (Object !== matchers.constructor) {
      return;
    }
    return Object.keys(matchers).reduce(function(memo, key) {
      memo[key] = parse(source, matchers[key]);
      return memo;
    }, {});
  }
  function prop(selector, name) {
    if (1 === arguments.length) {
      name = selector;
      selector = void 0;
    }
    return function(node) {
      var match = node;
      if (selector) {
        match = node.querySelector(selector);
      }
      if (match) {
        return getPath(match, name);
      }
    };
  }
  function attr(selector, name) {
    if (1 === arguments.length) {
      name = selector;
      selector = void 0;
    }
    return function(node) {
      var attributes = prop(selector, "attributes")(node);
      if (attributes && attributes.hasOwnProperty(name)) {
        return attributes[name].value;
      }
    };
  }
  function text(selector) {
    return prop(selector, "textContent");
  }
  function query(selector, matchers) {
    return function(node) {
      var matches = node.querySelectorAll(selector);
      return [].map.call(matches, function(match) {
        return parse(match, matchers);
      });
    };
  }

  // node_modules/memize/dist/index.js
  function memize(fn, options) {
    var size = 0;
    var head;
    var tail;
    options = options || {};
    function memoized() {
      var node = head, len = arguments.length, args, i2;
      searchCache: while (node) {
        if (node.args.length !== arguments.length) {
          node = node.next;
          continue;
        }
        for (i2 = 0; i2 < len; i2++) {
          if (node.args[i2] !== arguments[i2]) {
            node = node.next;
            continue searchCache;
          }
        }
        if (node !== head) {
          if (node === tail) {
            tail = node.prev;
          }
          node.prev.next = node.next;
          if (node.next) {
            node.next.prev = node.prev;
          }
          node.next = head;
          node.prev = null;
          head.prev = node;
          head = node;
        }
        return node.val;
      }
      args = new Array(len);
      for (i2 = 0; i2 < len; i2++) {
        args[i2] = arguments[i2];
      }
      node = {
        args,
        // Generate the result from original function
        val: fn.apply(null, args)
      };
      if (head) {
        head.prev = node;
        node.next = head;
      } else {
        tail = node;
      }
      if (size === /** @type {MemizeOptions} */
      options.maxSize) {
        tail = /** @type {MemizeCacheNode} */
        tail.prev;
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
    return memoized;
  }

  // packages/blocks/build-module/api/parser/get-block-attributes.js
  var import_hooks4 = __toESM(require_hooks());
  var import_rich_text4 = __toESM(require_rich_text());

  // packages/blocks/build-module/api/matchers.js
  var import_rich_text3 = __toESM(require_rich_text());

  // packages/blocks/build-module/api/node.js
  var import_deprecated9 = __toESM(require_deprecated());

  // packages/blocks/build-module/api/children.js
  var import_element3 = __toESM(require_element());
  var import_deprecated8 = __toESM(require_deprecated());
  function getSerializeCapableElement(children) {
    return children;
  }
  function getChildrenArray(children) {
    (0, import_deprecated8.default)("wp.blocks.children.getChildrenArray", {
      since: "6.1",
      version: "6.3",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    return children;
  }
  function concat(...blockNodes) {
    (0, import_deprecated8.default)("wp.blocks.children.concat", {
      since: "6.1",
      version: "6.3",
      alternative: "wp.richText.concat",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    const result = [];
    for (let i2 = 0; i2 < blockNodes.length; i2++) {
      const blockNode = Array.isArray(blockNodes[i2]) ? blockNodes[i2] : [blockNodes[i2]];
      for (let j2 = 0; j2 < blockNode.length; j2++) {
        const child = blockNode[j2];
        const canConcatToPreviousString = typeof child === "string" && typeof result[result.length - 1] === "string";
        if (canConcatToPreviousString) {
          result[result.length - 1] += child;
        } else {
          result.push(child);
        }
      }
    }
    return result;
  }
  function fromDOM22(domNodes) {
    (0, import_deprecated8.default)("wp.blocks.children.fromDOM", {
      since: "6.1",
      version: "6.3",
      alternative: "wp.richText.create",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    const result = [];
    for (let i2 = 0; i2 < domNodes.length; i2++) {
      try {
        result.push(fromDOM2(domNodes[i2]));
      } catch (error) {
      }
    }
    return result;
  }
  function toHTML(children) {
    (0, import_deprecated8.default)("wp.blocks.children.toHTML", {
      since: "6.1",
      version: "6.3",
      alternative: "wp.richText.toHTMLString",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    const element = getSerializeCapableElement(children);
    return (0, import_element3.renderToString)(element);
  }
  function matcher(selector) {
    (0, import_deprecated8.default)("wp.blocks.children.matcher", {
      since: "6.1",
      version: "6.3",
      alternative: "html source",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    return (domNode) => {
      let match = domNode;
      if (selector) {
        match = domNode.querySelector(selector);
      }
      if (match) {
        return fromDOM22(match.childNodes);
      }
      return [];
    };
  }
  var children_default = {
    concat,
    getChildrenArray,
    fromDOM: fromDOM22,
    toHTML,
    matcher
  };

  // packages/blocks/build-module/api/node.js
  function isNodeOfType(node, type) {
    (0, import_deprecated9.default)("wp.blocks.node.isNodeOfType", {
      since: "6.1",
      version: "6.3",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    return node && node.type === type;
  }
  function getNamedNodeMapAsObject(nodeMap) {
    const result = {};
    for (let i2 = 0; i2 < nodeMap.length; i2++) {
      const { name, value } = nodeMap[i2];
      result[name] = value;
    }
    return result;
  }
  function fromDOM2(domNode) {
    (0, import_deprecated9.default)("wp.blocks.node.fromDOM", {
      since: "6.1",
      version: "6.3",
      alternative: "wp.richText.create",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    if (domNode.nodeType === domNode.TEXT_NODE) {
      return domNode.nodeValue;
    }
    if (domNode.nodeType !== domNode.ELEMENT_NODE) {
      throw new TypeError(
        "A block node can only be created from a node of type text or element."
      );
    }
    return {
      type: domNode.nodeName.toLowerCase(),
      props: {
        ...getNamedNodeMapAsObject(domNode.attributes),
        children: fromDOM22(domNode.childNodes)
      }
    };
  }
  function toHTML2(node) {
    (0, import_deprecated9.default)("wp.blocks.node.toHTML", {
      since: "6.1",
      version: "6.3",
      alternative: "wp.richText.toHTMLString",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    return toHTML([node]);
  }
  function matcher2(selector) {
    (0, import_deprecated9.default)("wp.blocks.node.matcher", {
      since: "6.1",
      version: "6.3",
      alternative: "html source",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    return (domNode) => {
      let match = domNode;
      if (selector) {
        match = domNode.querySelector(selector);
      }
      try {
        return fromDOM2(match);
      } catch (error) {
        return null;
      }
    };
  }
  var node_default = {
    isNodeOfType,
    fromDOM: fromDOM2,
    toHTML: toHTML2,
    matcher: matcher2
  };

  // packages/blocks/build-module/api/matchers.js
  function html(selector, multilineTag) {
    return (domNode) => {
      let match = domNode;
      if (selector) {
        match = domNode.querySelector(selector);
      }
      if (!match) {
        return "";
      }
      if (multilineTag) {
        let value = "";
        const length = match.children.length;
        for (let index = 0; index < length; index++) {
          const child = match.children[index];
          if (child.nodeName.toLowerCase() !== multilineTag) {
            continue;
          }
          value += child.outerHTML;
        }
        return value;
      }
      return match.innerHTML;
    };
  }
  var richText = (selector, preserveWhiteSpace) => (el) => {
    const target = selector ? el.querySelector(selector) : el;
    return target ? import_rich_text3.RichTextData.fromHTMLElement(target, { preserveWhiteSpace }) : import_rich_text3.RichTextData.empty();
  };

  // packages/blocks/build-module/api/parser/get-block-attributes.js
  var toBooleanAttributeMatcher = (matcher3) => (value) => matcher3(value) !== void 0;
  function isOfType(value, type) {
    switch (type) {
      case "rich-text":
        return value instanceof import_rich_text4.RichTextData;
      case "string":
        return typeof value === "string";
      case "boolean":
        return typeof value === "boolean";
      case "object":
        return !!value && value.constructor === Object;
      case "null":
        return value === null;
      case "array":
        return Array.isArray(value);
      case "integer":
      case "number":
        return typeof value === "number";
    }
    return true;
  }
  function isOfTypes(value, types) {
    return types.some((type) => isOfType(value, type));
  }
  function getBlockAttribute(attributeKey, attributeSchema, innerDOM, commentAttributes, innerHTML) {
    let value;
    switch (attributeSchema.source) {
      // An undefined source means that it's an attribute serialized to the
      // block's "comment".
      case void 0:
        value = commentAttributes ? commentAttributes[attributeKey] : void 0;
        break;
      // raw source means that it's the original raw block content.
      case "raw":
        value = innerHTML;
        break;
      case "attribute":
      case "property":
      case "html":
      case "text":
      case "rich-text":
      case "children":
      case "node":
      case "query":
      case "tag":
        value = parseWithAttributeSchema(innerDOM, attributeSchema);
        break;
    }
    if (!isValidByType(value, attributeSchema.type) || !isValidByEnum(value, attributeSchema.enum)) {
      value = void 0;
    }
    if (value === void 0) {
      value = getDefault(attributeSchema);
    }
    return value;
  }
  function isValidByType(value, type) {
    return type === void 0 || isOfTypes(value, Array.isArray(type) ? type : [type]);
  }
  function isValidByEnum(value, enumSet) {
    return !Array.isArray(enumSet) || enumSet.includes(value);
  }
  var matcherFromSource = memize((sourceConfig) => {
    switch (sourceConfig.source) {
      case "attribute": {
        let matcher3 = attr(sourceConfig.selector, sourceConfig.attribute);
        if (sourceConfig.type === "boolean") {
          matcher3 = toBooleanAttributeMatcher(matcher3);
        }
        return matcher3;
      }
      case "html":
        return html(sourceConfig.selector, sourceConfig.multiline);
      case "text":
        return text(sourceConfig.selector);
      case "rich-text":
        return richText(
          sourceConfig.selector,
          sourceConfig.__unstablePreserveWhiteSpace
        );
      case "children":
        return matcher(sourceConfig.selector);
      case "node":
        return matcher2(sourceConfig.selector);
      case "query":
        const subMatchers = Object.fromEntries(
          Object.entries(sourceConfig.query).map(
            ([key, subSourceConfig]) => [
              key,
              matcherFromSource(subSourceConfig)
            ]
          )
        );
        return query(sourceConfig.selector, subMatchers);
      case "tag": {
        const matcher3 = prop(sourceConfig.selector, "nodeName");
        return (domNode) => matcher3(domNode)?.toLowerCase();
      }
      default:
        console.error(`Unknown source type "${sourceConfig.source}"`);
    }
  });
  function parseHtml(innerHTML) {
    return parse(innerHTML, (h2) => h2);
  }
  function parseWithAttributeSchema(innerHTML, attributeSchema) {
    return matcherFromSource(attributeSchema)(parseHtml(innerHTML));
  }
  function getBlockAttributes(blockTypeOrName, innerHTML, attributes = {}) {
    const doc = parseHtml(innerHTML);
    const blockType = normalizeBlockType(blockTypeOrName);
    const blockAttributes = Object.fromEntries(
      Object.entries(blockType.attributes ?? {}).map(
        ([key, schema]) => [
          key,
          getBlockAttribute(key, schema, doc, attributes, innerHTML)
        ]
      )
    );
    return (0, import_hooks4.applyFilters)(
      "blocks.getBlockAttributes",
      blockAttributes,
      blockType,
      innerHTML,
      attributes
    );
  }

  // packages/blocks/build-module/api/parser/fix-custom-classname.js
  var CLASS_ATTR_SCHEMA = {
    type: "string",
    source: "attribute",
    selector: "[data-custom-class-name] > *",
    attribute: "class"
  };
  function getHTMLRootElementClasses(innerHTML) {
    const parsed = parseWithAttributeSchema(
      `<div data-custom-class-name>${innerHTML}</div>`,
      CLASS_ATTR_SCHEMA
    );
    return parsed ? parsed.trim().split(/\s+/) : [];
  }
  function fixCustomClassname(blockAttributes, blockType, innerHTML) {
    if (!hasBlockSupport(blockType, "customClassName", true)) {
      return blockAttributes;
    }
    const modifiedBlockAttributes = { ...blockAttributes };
    const { className: omittedClassName, ...attributesSansClassName } = modifiedBlockAttributes;
    const serialized = getSaveContent(blockType, attributesSansClassName);
    const defaultClasses = getHTMLRootElementClasses(serialized);
    const actualClasses = getHTMLRootElementClasses(innerHTML);
    const customClasses = actualClasses.filter(
      (className) => !defaultClasses.includes(className)
    );
    if (customClasses.length) {
      modifiedBlockAttributes.className = customClasses.join(" ");
    } else if (serialized) {
      delete modifiedBlockAttributes.className;
    }
    return modifiedBlockAttributes;
  }

  // packages/blocks/build-module/api/parser/fix-global-attribute.js
  function getHTMLRootElement(innerHTML, dataAttribute, attributeSchema) {
    const parsed = parseWithAttributeSchema(
      `<div ${dataAttribute}>${innerHTML}</div>`,
      attributeSchema
    );
    return parsed;
  }
  function fixGlobalAttribute(blockAttributes, blockType, innerHTML, supportKey, dataAttribute, attributeSchema) {
    if (!hasBlockSupport(blockType, supportKey, false)) {
      return blockAttributes;
    }
    const modifiedBlockAttributes = { ...blockAttributes };
    const attributeValue = getHTMLRootElement(
      innerHTML,
      dataAttribute,
      attributeSchema
    );
    if (attributeValue) {
      modifiedBlockAttributes[supportKey] = attributeValue;
    }
    return modifiedBlockAttributes;
  }

  // packages/blocks/build-module/api/parser/apply-built-in-validation-fixes.js
  var ARIA_LABEL_ATTR_SCHEMA = {
    type: "string",
    source: "attribute",
    selector: "[data-aria-label] > *",
    attribute: "aria-label"
  };
  var ANCHOR_ATTR_SCHEMA = {
    type: "string",
    source: "attribute",
    selector: "[data-anchor] > *",
    attribute: "id"
  };
  function applyBuiltInValidationFixes(block, blockType) {
    const { attributes, originalContent } = block;
    let updatedBlockAttributes = attributes;
    updatedBlockAttributes = fixCustomClassname(
      attributes,
      blockType,
      originalContent
    );
    updatedBlockAttributes = fixGlobalAttribute(
      updatedBlockAttributes,
      blockType,
      originalContent,
      "ariaLabel",
      "data-aria-label",
      ARIA_LABEL_ATTR_SCHEMA
    );
    updatedBlockAttributes = fixGlobalAttribute(
      updatedBlockAttributes,
      blockType,
      originalContent,
      "anchor",
      "data-anchor",
      ANCHOR_ATTR_SCHEMA
    );
    return {
      ...block,
      attributes: updatedBlockAttributes
    };
  }

  // packages/blocks/build-module/api/parser/apply-block-deprecated-versions.js
  function stubFalse() {
    return false;
  }
  function applyBlockDeprecatedVersions(block, rawBlock, blockType) {
    const parsedAttributes = rawBlock.attrs;
    const { deprecated: deprecatedDefinitions } = blockType;
    if (!deprecatedDefinitions || !deprecatedDefinitions.length) {
      return block;
    }
    for (let i2 = 0; i2 < deprecatedDefinitions.length; i2++) {
      const { isEligible = stubFalse } = deprecatedDefinitions[i2];
      if (block.isValid && !isEligible(parsedAttributes, block.innerBlocks, {
        blockNode: rawBlock,
        block
      })) {
        continue;
      }
      const deprecatedBlockType = Object.assign(
        omit(blockType, DEPRECATED_ENTRY_KEYS),
        deprecatedDefinitions[i2]
      );
      let migratedBlock = {
        ...block,
        attributes: getBlockAttributes(
          deprecatedBlockType,
          block.originalContent,
          parsedAttributes
        )
      };
      let [isValid] = validateBlock(migratedBlock, deprecatedBlockType);
      if (!isValid) {
        migratedBlock = applyBuiltInValidationFixes(
          migratedBlock,
          deprecatedBlockType
        );
        [isValid] = validateBlock(migratedBlock, deprecatedBlockType);
      }
      if (!isValid) {
        continue;
      }
      let migratedInnerBlocks = migratedBlock.innerBlocks;
      let migratedAttributes = migratedBlock.attributes;
      const { migrate } = deprecatedBlockType;
      if (migrate) {
        let migrated = migrate(migratedAttributes, block.innerBlocks);
        if (!Array.isArray(migrated)) {
          migrated = [migrated];
        }
        [
          migratedAttributes = parsedAttributes,
          migratedInnerBlocks = block.innerBlocks
        ] = migrated;
      }
      block = {
        ...block,
        attributes: migratedAttributes,
        innerBlocks: migratedInnerBlocks,
        isValid: true,
        validationIssues: []
      };
    }
    return block;
  }

  // packages/blocks/build-module/api/parser/index.js
  function convertLegacyBlocks(rawBlock) {
    const [correctName, correctedAttributes] = convertLegacyBlockNameAndAttributes(
      rawBlock.blockName,
      rawBlock.attrs
    );
    return {
      ...rawBlock,
      blockName: correctName,
      attrs: correctedAttributes
    };
  }
  function normalizeRawBlock(rawBlock, options) {
    const fallbackBlockName = getFreeformContentHandlerName();
    const rawBlockName = rawBlock.blockName || getFreeformContentHandlerName();
    const rawAttributes = rawBlock.attrs || {};
    const rawInnerBlocks = rawBlock.innerBlocks || [];
    let rawInnerHTML = rawBlock.innerHTML.trim();
    if (rawBlockName === fallbackBlockName && rawBlockName === "core/freeform" && !options?.__unstableSkipAutop) {
      rawInnerHTML = (0, import_autop2.autop)(rawInnerHTML).trim();
    }
    return {
      ...rawBlock,
      blockName: rawBlockName,
      attrs: rawAttributes,
      innerHTML: rawInnerHTML,
      innerBlocks: rawInnerBlocks
    };
  }
  function createMissingBlockType(rawBlock) {
    const unregisteredFallbackBlock = getUnregisteredTypeHandlerName() || getFreeformContentHandlerName();
    const originalUndelimitedContent = serializeRawBlock(rawBlock, {
      isCommentDelimited: false
    });
    const originalContent = serializeRawBlock(rawBlock, {
      isCommentDelimited: true
    });
    return {
      blockName: unregisteredFallbackBlock,
      attrs: {
        originalName: rawBlock.blockName,
        originalContent,
        originalUndelimitedContent
      },
      innerHTML: rawBlock.blockName ? originalContent : rawBlock.innerHTML,
      innerBlocks: rawBlock.innerBlocks,
      innerContent: rawBlock.innerContent
    };
  }
  function applyBlockValidation(unvalidatedBlock, blockType) {
    const [isValid] = validateBlock(unvalidatedBlock, blockType);
    if (isValid) {
      return { ...unvalidatedBlock, isValid, validationIssues: [] };
    }
    const fixedBlock = applyBuiltInValidationFixes(
      unvalidatedBlock,
      blockType
    );
    const [isFixedValid, validationIssues] = validateBlock(
      fixedBlock,
      blockType
    );
    return { ...fixedBlock, isValid: isFixedValid, validationIssues };
  }
  function parseRawBlock(rawBlock, options) {
    let normalizedBlock = normalizeRawBlock(rawBlock, options);
    normalizedBlock = convertLegacyBlocks(normalizedBlock);
    let blockType = getBlockType(normalizedBlock.blockName);
    if (!blockType) {
      normalizedBlock = createMissingBlockType(normalizedBlock);
      blockType = getBlockType(normalizedBlock.blockName);
    }
    const isFallbackBlock = normalizedBlock.blockName === getFreeformContentHandlerName() || normalizedBlock.blockName === getUnregisteredTypeHandlerName();
    if (!blockType || !normalizedBlock.innerHTML && isFallbackBlock) {
      return;
    }
    const parsedInnerBlocks = normalizedBlock.innerBlocks.map((innerBlock) => parseRawBlock(innerBlock, options)).filter((innerBlock) => !!innerBlock);
    const parsedBlock = createBlock(
      normalizedBlock.blockName,
      getBlockAttributes(
        blockType,
        normalizedBlock.innerHTML,
        normalizedBlock.attrs
      ),
      parsedInnerBlocks
    );
    parsedBlock.originalContent = normalizedBlock.innerHTML;
    const validatedBlock = applyBlockValidation(parsedBlock, blockType);
    const { validationIssues } = validatedBlock;
    const updatedBlock = applyBlockDeprecatedVersions(
      validatedBlock,
      normalizedBlock,
      blockType
    );
    if (!updatedBlock.isValid) {
      updatedBlock.__unstableBlockSource = rawBlock;
    }
    if (!validatedBlock.isValid && updatedBlock.isValid && !options?.__unstableSkipMigrationLogs) {
      console.groupCollapsed("Updated Block: %s", blockType.name);
      console.info(
        "Block successfully updated for `%s` (%o).\n\nNew content generated by `save` function:\n\n%s\n\nContent retrieved from post body:\n\n%s",
        blockType.name,
        blockType,
        getSaveContent(blockType, updatedBlock.attributes),
        updatedBlock.originalContent
      );
      console.groupEnd();
    } else if (!validatedBlock.isValid && !updatedBlock.isValid) {
      validationIssues.forEach(({ log: log2, args }) => log2(...args));
    }
    return updatedBlock;
  }
  function parse2(content, options) {
    return (0, import_block_serialization_default_parser.parse)(content).reduce((accumulator, rawBlock) => {
      const block = parseRawBlock(rawBlock, options);
      if (block) {
        accumulator.push(block);
      }
      return accumulator;
    }, []);
  }

  // packages/blocks/build-module/api/raw-handling/index.js
  var import_deprecated10 = __toESM(require_deprecated());
  var import_dom12 = __toESM(require_dom());

  // packages/blocks/build-module/api/raw-handling/html-to-blocks.js
  var import_element4 = __toESM(require_element());

  // packages/blocks/build-module/api/raw-handling/get-raw-transforms.js
  function getRawTransforms() {
    return getBlockTransforms("from").filter(({ type }) => type === "raw").map((transform) => {
      return transform.isMatch ? transform : {
        ...transform,
        isMatch: (node) => transform.selector && node.matches(transform.selector)
      };
    });
  }

  // packages/blocks/build-module/api/raw-handling/html-to-blocks.js
  function htmlToBlocks(html2, handler) {
    const doc = document.implementation.createHTMLDocument("");
    doc.body.innerHTML = html2;
    return Array.from(doc.body.children).flatMap((node) => {
      const rawTransform = findTransform(
        getRawTransforms(),
        ({ isMatch }) => isMatch(node)
      );
      if (!rawTransform) {
        if (import_element4.Platform.isNative) {
          return parse2(
            `<!-- wp:html -->${node.outerHTML}<!-- /wp:html -->`
          );
        }
        return createBlock(
          // Should not be hardcoded.
          "core/html",
          getBlockAttributes("core/html", node.outerHTML)
        );
      }
      const { transform, blockName } = rawTransform;
      if (transform) {
        const block = transform(node, handler);
        if (node.hasAttribute("class")) {
          block.attributes.className = node.getAttribute("class");
        }
        return block;
      }
      return createBlock(
        blockName,
        getBlockAttributes(blockName, node.outerHTML)
      );
    });
  }

  // packages/blocks/build-module/api/raw-handling/normalise-blocks.js
  var import_dom2 = __toESM(require_dom());
  function normaliseBlocks(HTML, options = {}) {
    const decuDoc = document.implementation.createHTMLDocument("");
    const accuDoc = document.implementation.createHTMLDocument("");
    const decu = decuDoc.body;
    const accu = accuDoc.body;
    decu.innerHTML = HTML;
    while (decu.firstChild) {
      const node = decu.firstChild;
      if (node.nodeType === node.TEXT_NODE) {
        if ((0, import_dom2.isEmpty)(node)) {
          decu.removeChild(node);
        } else {
          if (!accu.lastChild || accu.lastChild.nodeName !== "P") {
            accu.appendChild(accuDoc.createElement("P"));
          }
          accu.lastChild.appendChild(node);
        }
      } else if (node.nodeType === node.ELEMENT_NODE) {
        if (node.nodeName === "BR") {
          if (node.nextSibling && node.nextSibling.nodeName === "BR") {
            accu.appendChild(accuDoc.createElement("P"));
            decu.removeChild(node.nextSibling);
          }
          if (accu.lastChild && accu.lastChild.nodeName === "P" && accu.lastChild.hasChildNodes()) {
            accu.lastChild.appendChild(node);
          } else {
            decu.removeChild(node);
          }
        } else if (node.nodeName === "P") {
          if ((0, import_dom2.isEmpty)(node) && !options.raw) {
            decu.removeChild(node);
          } else {
            accu.appendChild(node);
          }
        } else if ((0, import_dom2.isPhrasingContent)(node)) {
          if (!accu.lastChild || accu.lastChild.nodeName !== "P") {
            accu.appendChild(accuDoc.createElement("P"));
          }
          accu.lastChild.appendChild(node);
        } else {
          accu.appendChild(node);
        }
      } else {
        decu.removeChild(node);
      }
    }
    return accu.innerHTML;
  }

  // packages/blocks/build-module/api/raw-handling/special-comment-converter.js
  var import_dom3 = __toESM(require_dom());
  function specialCommentConverter(node, doc) {
    if (node.nodeType !== node.COMMENT_NODE) {
      return;
    }
    if (node.nodeValue !== "nextpage" && node.nodeValue.indexOf("more") !== 0) {
      return;
    }
    const block = createBlock2(node, doc);
    if (!node.parentNode || node.parentNode.nodeName !== "P") {
      (0, import_dom3.replace)(node, block);
    } else {
      const childNodes = Array.from(node.parentNode.childNodes);
      const nodeIndex = childNodes.indexOf(node);
      const wrapperNode = node.parentNode.parentNode || doc.body;
      const paragraphBuilder = (acc, child) => {
        if (!acc) {
          acc = doc.createElement("p");
        }
        acc.appendChild(child);
        return acc;
      };
      [
        childNodes.slice(0, nodeIndex).reduce(paragraphBuilder, null),
        block,
        childNodes.slice(nodeIndex + 1).reduce(paragraphBuilder, null)
      ].forEach(
        (element) => element && wrapperNode.insertBefore(element, node.parentNode)
      );
      (0, import_dom3.remove)(node.parentNode);
    }
  }
  function createBlock2(commentNode, doc) {
    if (commentNode.nodeValue === "nextpage") {
      return createNextpage(doc);
    }
    const customText = commentNode.nodeValue.slice(4).trim();
    let sibling = commentNode;
    let noTeaser = false;
    while (sibling = sibling.nextSibling) {
      if (sibling.nodeType === sibling.COMMENT_NODE && sibling.nodeValue === "noteaser") {
        noTeaser = true;
        (0, import_dom3.remove)(sibling);
        break;
      }
    }
    return createMore(customText, noTeaser, doc);
  }
  function createMore(customText, noTeaser, doc) {
    const node = doc.createElement("wp-block");
    node.dataset.block = "core/more";
    if (customText) {
      node.dataset.customText = customText;
    }
    if (noTeaser) {
      node.dataset.noTeaser = "";
    }
    return node;
  }
  function createNextpage(doc) {
    const node = doc.createElement("wp-block");
    node.dataset.block = "core/nextpage";
    return node;
  }

  // packages/blocks/build-module/api/raw-handling/list-reducer.js
  var import_dom4 = __toESM(require_dom());
  function isList(node) {
    return node.nodeName === "OL" || node.nodeName === "UL";
  }
  function shallowTextContent(element) {
    return Array.from(element.childNodes).map(({ nodeValue = "" }) => nodeValue).join("");
  }
  function listReducer(node) {
    if (!isList(node)) {
      return;
    }
    const list = node;
    const prevElement = node.previousElementSibling;
    if (prevElement && prevElement.nodeName === node.nodeName && list.children.length === 1) {
      while (list.firstChild) {
        prevElement.appendChild(list.firstChild);
      }
      list.parentNode.removeChild(list);
    }
    const parentElement = node.parentNode;
    if (parentElement && parentElement.nodeName === "LI" && parentElement.children.length === 1 && !/\S/.test(shallowTextContent(parentElement))) {
      const parentListItem = parentElement;
      const prevListItem = parentListItem.previousElementSibling;
      const parentList = parentListItem.parentNode;
      if (prevListItem) {
        prevListItem.appendChild(list);
        parentList.removeChild(parentListItem);
      }
    }
    if (parentElement && isList(parentElement)) {
      const prevListItem = node.previousElementSibling;
      if (prevListItem) {
        prevListItem.appendChild(node);
      } else {
        (0, import_dom4.unwrap)(node);
      }
    }
  }

  // packages/blocks/build-module/api/raw-handling/blockquote-normaliser.js
  function blockquoteNormaliser(options) {
    return (node) => {
      if (node.nodeName !== "BLOCKQUOTE") {
        return;
      }
      node.innerHTML = normaliseBlocks(node.innerHTML, options);
    };
  }

  // packages/blocks/build-module/api/raw-handling/figure-content-reducer.js
  var import_dom5 = __toESM(require_dom());
  function isFigureContent(node, schema) {
    const tag = node.nodeName.toLowerCase();
    if (tag === "figcaption" || (0, import_dom5.isTextContent)(node)) {
      return false;
    }
    return tag in (schema?.figure?.children ?? {});
  }
  function canHaveAnchor(node, schema) {
    const tag = node.nodeName.toLowerCase();
    return tag in (schema?.figure?.children?.a?.children ?? {});
  }
  function wrapFigureContent(element, beforeElement = element) {
    const figure = element.ownerDocument.createElement("figure");
    beforeElement.parentNode.insertBefore(figure, beforeElement);
    figure.appendChild(element);
  }
  function figureContentReducer(node, doc, schema) {
    if (!isFigureContent(node, schema)) {
      return;
    }
    let nodeToInsert = node;
    const parentNode = node.parentNode;
    if (canHaveAnchor(node, schema) && parentNode.nodeName === "A" && parentNode.childNodes.length === 1) {
      nodeToInsert = node.parentNode;
    }
    const wrapper = nodeToInsert.closest("p,div");
    if (wrapper) {
      if (!node.classList) {
        wrapFigureContent(nodeToInsert, wrapper);
      } else if (node.classList.contains("alignright") || node.classList.contains("alignleft") || node.classList.contains("aligncenter") || !wrapper.textContent.trim()) {
        wrapFigureContent(nodeToInsert, wrapper);
      }
    } else {
      wrapFigureContent(nodeToInsert);
    }
  }

  // packages/blocks/build-module/api/raw-handling/shortcode-converter.js
  var import_shortcode = __toESM(require_shortcode());
  var castArray = (maybeArray) => Array.isArray(maybeArray) ? maybeArray : [maybeArray];
  var beforeLineRegexp = /(\n|<p>)\s*$/;
  var afterLineRegexp = /^\s*(\n|<\/p>)/;
  function segmentHTMLToShortcodeBlock(HTML, lastIndex = 0, excludedBlockNames = []) {
    const transformsFrom = getBlockTransforms("from");
    const transformation = findTransform(
      transformsFrom,
      (transform) => excludedBlockNames.indexOf(transform.blockName) === -1 && transform.type === "shortcode" && castArray(transform.tag).some(
        (tag) => (0, import_shortcode.regexp)(tag).test(HTML)
      )
    );
    if (!transformation) {
      return [HTML];
    }
    const transformTags = castArray(transformation.tag);
    const transformTag = transformTags.find(
      (tag) => (0, import_shortcode.regexp)(tag).test(HTML)
    );
    let match;
    const previousIndex = lastIndex;
    if (match = (0, import_shortcode.next)(transformTag, HTML, lastIndex)) {
      lastIndex = match.index + match.content.length;
      const beforeHTML = HTML.substr(0, match.index);
      const afterHTML = HTML.substr(lastIndex);
      if (!match.shortcode.content?.includes("<") && !(beforeLineRegexp.test(beforeHTML) && afterLineRegexp.test(afterHTML))) {
        return segmentHTMLToShortcodeBlock(HTML, lastIndex);
      }
      if (transformation.isMatch && !transformation.isMatch(match.shortcode.attrs)) {
        return segmentHTMLToShortcodeBlock(HTML, previousIndex, [
          ...excludedBlockNames,
          transformation.blockName
        ]);
      }
      let blocks = [];
      if (typeof transformation.transform === "function") {
        blocks = [].concat(
          transformation.transform(match.shortcode.attrs, match)
        );
        blocks = blocks.map((block) => {
          block.originalContent = match.shortcode.content;
          return applyBuiltInValidationFixes(
            block,
            getBlockType(block.name)
          );
        });
      } else {
        const attributes = Object.fromEntries(
          Object.entries(transformation.attributes).filter(([, schema]) => schema.shortcode).map(([key, schema]) => [
            key,
            schema.shortcode(match.shortcode.attrs, match)
          ])
        );
        const blockType = getBlockType(transformation.blockName);
        if (!blockType) {
          return [HTML];
        }
        const transformationBlockType = {
          ...blockType,
          attributes: transformation.attributes
        };
        let block = createBlock(
          transformation.blockName,
          getBlockAttributes(
            transformationBlockType,
            match.shortcode.content,
            attributes
          )
        );
        block.originalContent = match.shortcode.content;
        block = applyBuiltInValidationFixes(
          block,
          transformationBlockType
        );
        blocks = [block];
      }
      return [
        ...segmentHTMLToShortcodeBlock(
          beforeHTML.replace(beforeLineRegexp, "")
        ),
        ...blocks,
        ...segmentHTMLToShortcodeBlock(
          afterHTML.replace(afterLineRegexp, "")
        )
      ];
    }
    return [HTML];
  }
  var shortcode_converter_default = segmentHTMLToShortcodeBlock;

  // packages/blocks/build-module/api/raw-handling/utils.js
  var import_dom6 = __toESM(require_dom());
  function getBlockContentSchemaFromTransforms(transforms, context) {
    const phrasingContentSchema = (0, import_dom6.getPhrasingContentSchema)(context);
    const schemaArgs = { phrasingContentSchema, isPaste: context === "paste" };
    const schemas = transforms.map(({ isMatch, blockName, schema }) => {
      const hasAnchorSupport = hasBlockSupport(blockName, "anchor");
      schema = typeof schema === "function" ? schema(schemaArgs) : schema;
      if (!hasAnchorSupport && !isMatch) {
        return schema;
      }
      if (!schema) {
        return {};
      }
      return Object.fromEntries(
        Object.entries(schema).map(([key, value]) => {
          let attributes = value.attributes || [];
          if (hasAnchorSupport) {
            attributes = [...attributes, "id"];
          }
          return [
            key,
            {
              ...value,
              attributes,
              isMatch: isMatch ? isMatch : void 0
            }
          ];
        })
      );
    });
    function mergeTagNameSchemaProperties(objValue, srcValue, key) {
      switch (key) {
        case "children": {
          if (objValue === "*" || srcValue === "*") {
            return "*";
          }
          return { ...objValue, ...srcValue };
        }
        case "attributes":
        case "require": {
          return [...objValue || [], ...srcValue || []];
        }
        case "isMatch": {
          if (!objValue || !srcValue) {
            return void 0;
          }
          return (...args) => {
            return objValue(...args) || srcValue(...args);
          };
        }
      }
    }
    function mergeTagNameSchemas(a2, b2) {
      for (const key in b2) {
        a2[key] = a2[key] ? mergeTagNameSchemaProperties(a2[key], b2[key], key) : { ...b2[key] };
      }
      return a2;
    }
    function mergeSchemas(a2, b2) {
      for (const key in b2) {
        a2[key] = a2[key] ? mergeTagNameSchemas(a2[key], b2[key]) : { ...b2[key] };
      }
      return a2;
    }
    return schemas.reduce(mergeSchemas, {});
  }
  function getBlockContentSchema(context) {
    return getBlockContentSchemaFromTransforms(getRawTransforms(), context);
  }
  function isPlain(HTML) {
    if (!/<(?!br[ />])/i.test(HTML)) {
      return true;
    }
    const doc = document.implementation.createHTMLDocument("");
    doc.body.innerHTML = HTML;
    if (doc.body.children.length !== 1) {
      return false;
    }
    const wrapper = doc.body.children.item(0);
    const descendants = wrapper.getElementsByTagName("*");
    for (let i2 = 0; i2 < descendants.length; i2++) {
      if (descendants.item(i2).tagName !== "BR") {
        return false;
      }
    }
    if (wrapper.tagName !== "SPAN") {
      return false;
    }
    return true;
  }
  function deepFilterNodeList(nodeList, filters, doc, schema) {
    Array.from(nodeList).forEach((node) => {
      deepFilterNodeList(node.childNodes, filters, doc, schema);
      filters.forEach((item) => {
        if (!doc.contains(node)) {
          return;
        }
        item(node, doc, schema);
      });
    });
  }
  function deepFilterHTML(HTML, filters = [], schema) {
    const doc = document.implementation.createHTMLDocument("");
    doc.body.innerHTML = HTML;
    deepFilterNodeList(doc.body.childNodes, filters, doc, schema);
    return doc.body.innerHTML;
  }
  function getSibling(node, which) {
    const sibling = node[`${which}Sibling`];
    if (sibling && (0, import_dom6.isPhrasingContent)(sibling)) {
      return sibling;
    }
    const { parentNode } = node;
    if (!parentNode || !(0, import_dom6.isPhrasingContent)(parentNode)) {
      return;
    }
    return getSibling(parentNode, which);
  }

  // packages/blocks/build-module/api/raw-handling/paste-handler.js
  var import_dom11 = __toESM(require_dom());

  // packages/blocks/build-module/api/raw-handling/comment-remover.js
  var import_dom7 = __toESM(require_dom());
  function commentRemover(node) {
    if (node.nodeType === node.COMMENT_NODE) {
      (0, import_dom7.remove)(node);
    }
  }

  // packages/blocks/build-module/api/raw-handling/is-inline-content.js
  var import_dom8 = __toESM(require_dom());
  function isInline(node, contextTag) {
    if ((0, import_dom8.isTextContent)(node)) {
      return true;
    }
    if (!contextTag) {
      return false;
    }
    const tag = node.nodeName.toLowerCase();
    const inlineAllowedTagGroups = [
      ["ul", "li", "ol"],
      ["h1", "h2", "h3", "h4", "h5", "h6"]
    ];
    return inlineAllowedTagGroups.some(
      (tagGroup) => [tag, contextTag].filter((t3) => !tagGroup.includes(t3)).length === 0
    );
  }
  function deepCheck(nodes, contextTag) {
    return nodes.every(
      (node) => isInline(node, contextTag) && deepCheck(Array.from(node.children), contextTag)
    );
  }
  function isDoubleBR(node) {
    return node.nodeName === "BR" && node.previousSibling && node.previousSibling.nodeName === "BR";
  }
  function isInlineContent(HTML, contextTag) {
    const doc = document.implementation.createHTMLDocument("");
    doc.body.innerHTML = HTML;
    const nodes = Array.from(doc.body.children);
    return !nodes.some(isDoubleBR) && deepCheck(nodes, contextTag);
  }

  // packages/blocks/build-module/api/raw-handling/phrasing-content-reducer.js
  var import_dom9 = __toESM(require_dom());
  function phrasingContentReducer(node, doc) {
    if (node.nodeName === "SPAN" && node.style) {
      const {
        fontWeight,
        fontStyle,
        textDecorationLine,
        textDecoration,
        verticalAlign
      } = node.style;
      if (fontWeight === "bold" || fontWeight === "700") {
        (0, import_dom9.wrap)(doc.createElement("strong"), node);
      }
      if (fontStyle === "italic") {
        (0, import_dom9.wrap)(doc.createElement("em"), node);
      }
      if (textDecorationLine === "line-through" || textDecoration.includes("line-through")) {
        (0, import_dom9.wrap)(doc.createElement("s"), node);
      }
      if (verticalAlign === "super") {
        (0, import_dom9.wrap)(doc.createElement("sup"), node);
      } else if (verticalAlign === "sub") {
        (0, import_dom9.wrap)(doc.createElement("sub"), node);
      }
    } else if (node.nodeName === "B") {
      node = (0, import_dom9.replaceTag)(node, "strong");
    } else if (node.nodeName === "I") {
      node = (0, import_dom9.replaceTag)(node, "em");
    } else if (node.nodeName === "A") {
      if (node.target && node.target.toLowerCase() === "_blank") {
        node.rel = "noreferrer noopener";
      } else {
        node.removeAttribute("target");
        node.removeAttribute("rel");
      }
      if (node.name && !node.id) {
        node.id = node.name;
      }
      if (node.id && !node.ownerDocument.querySelector(`[href="#${node.id}"]`)) {
        node.removeAttribute("id");
      }
    }
  }

  // packages/blocks/build-module/api/raw-handling/head-remover.js
  function headRemover(node) {
    if (node.nodeName !== "SCRIPT" && node.nodeName !== "NOSCRIPT" && node.nodeName !== "TEMPLATE" && node.nodeName !== "STYLE") {
      return;
    }
    node.parentNode.removeChild(node);
  }

  // packages/blocks/build-module/api/raw-handling/ms-list-ignore.js
  function msListIgnore(node) {
    if (node.nodeType !== node.ELEMENT_NODE) {
      return;
    }
    const style = node.getAttribute("style");
    if (!style || !style.includes("mso-list")) {
      return;
    }
    const rules = style.split(";").reduce((acc, rule) => {
      const [key, value] = rule.split(":");
      if (key && value) {
        acc[key.trim().toLowerCase()] = value.trim().toLowerCase();
      }
      return acc;
    }, {});
    if (rules["mso-list"] === "ignore") {
      node.remove();
    }
  }

  // packages/blocks/build-module/api/raw-handling/ms-list-converter.js
  function isList2(node) {
    return node.nodeName === "OL" || node.nodeName === "UL";
  }
  function msListConverter(node, doc) {
    if (node.nodeName !== "P") {
      return;
    }
    const style = node.getAttribute("style");
    if (!style || !style.includes("mso-list")) {
      return;
    }
    const prevNode = node.previousElementSibling;
    if (!prevNode || !isList2(prevNode)) {
      const type = node.textContent.trim().slice(0, 1);
      const isNumeric = /[1iIaA]/.test(type);
      const newListNode = doc.createElement(isNumeric ? "ol" : "ul");
      if (isNumeric) {
        newListNode.setAttribute("type", type);
      }
      node.parentNode.insertBefore(newListNode, node);
    }
    const listNode = node.previousElementSibling;
    const listType = listNode.nodeName;
    const listItem = doc.createElement("li");
    let receivingNode = listNode;
    listItem.innerHTML = deepFilterHTML(node.innerHTML, [msListIgnore]);
    const matches = /mso-list\s*:[^;]+level([0-9]+)/i.exec(style);
    let level = matches ? parseInt(matches[1], 10) - 1 || 0 : 0;
    while (level--) {
      receivingNode = receivingNode.lastChild || receivingNode;
      if (isList2(receivingNode)) {
        receivingNode = receivingNode.lastChild || receivingNode;
      }
    }
    if (!isList2(receivingNode)) {
      receivingNode = receivingNode.appendChild(
        doc.createElement(listType)
      );
    }
    receivingNode.appendChild(listItem);
    node.parentNode.removeChild(node);
  }

  // packages/blocks/build-module/api/raw-handling/image-corrector.js
  var import_blob = __toESM(require_blob());
  function imageCorrector(node) {
    if (node.nodeName !== "IMG") {
      return;
    }
    if (node.src.indexOf("file:") === 0) {
      node.src = "";
    }
    if (node.src.indexOf("data:") === 0) {
      const [properties, data] = node.src.split(",");
      const [type] = properties.slice(5).split(";");
      if (!data || !type) {
        node.src = "";
        return;
      }
      let decoded;
      try {
        decoded = atob(data);
      } catch (e2) {
        node.src = "";
        return;
      }
      const uint8Array = new Uint8Array(decoded.length);
      for (let i2 = 0; i2 < uint8Array.length; i2++) {
        uint8Array[i2] = decoded.charCodeAt(i2);
      }
      const name = type.replace("/", ".");
      const file = new window.File([uint8Array], name, { type });
      node.src = (0, import_blob.createBlobURL)(file);
    }
    if (node.height === 1 || node.width === 1) {
      node.parentNode.removeChild(node);
    }
  }

  // packages/blocks/build-module/api/raw-handling/div-normaliser.js
  function divNormaliser(node) {
    if (node.nodeName !== "DIV") {
      return;
    }
    node.innerHTML = normaliseBlocks(node.innerHTML);
  }

  // packages/blocks/build-module/api/raw-handling/markdown-converter.js
  var import_showdown = __toESM(require_showdown());
  var converter = new import_showdown.default.Converter({
    noHeaderId: true,
    tables: true,
    literalMidWordUnderscores: true,
    omitExtraWLInCodeBlocks: true,
    simpleLineBreaks: true,
    strikethrough: true
  });
  function slackMarkdownVariantCorrector(text2) {
    return text2.replace(
      /((?:^|\n)```)([^\n`]+)(```(?:$|\n))/,
      (match, p1, p2, p3) => `${p1}
${p2}
${p3}`
    );
  }
  function bulletsToAsterisks(text2) {
    return text2.replace(/(^|\n)•( +)/g, "$1*$2");
  }
  function markdownConverter(text2) {
    return converter.makeHtml(
      slackMarkdownVariantCorrector(bulletsToAsterisks(text2))
    );
  }

  // packages/blocks/build-module/api/raw-handling/iframe-remover.js
  function iframeRemover(node) {
    if (node.nodeName === "IFRAME") {
      const text2 = node.ownerDocument.createTextNode(node.src);
      node.parentNode.replaceChild(text2, node);
    }
  }

  // packages/blocks/build-module/api/raw-handling/google-docs-uid-remover.js
  var import_dom10 = __toESM(require_dom());
  function googleDocsUIdRemover(node) {
    if (!node.id || node.id.indexOf("docs-internal-guid-") !== 0) {
      return;
    }
    if (node.tagName === "B") {
      (0, import_dom10.unwrap)(node);
    } else {
      node.removeAttribute("id");
    }
  }

  // packages/blocks/build-module/api/raw-handling/html-formatting-remover.js
  function isFormattingSpace(character) {
    return character === " " || character === "\r" || character === "\n" || character === "	";
  }
  function htmlFormattingRemover(node) {
    if (node.nodeType !== node.TEXT_NODE) {
      return;
    }
    let parent = node;
    while (parent = parent.parentNode) {
      if (parent.nodeType === parent.ELEMENT_NODE && parent.nodeName === "PRE") {
        return;
      }
    }
    let newData = node.data.replace(/[ \r\n\t]+/g, " ");
    if (newData[0] === " ") {
      const previousSibling = getSibling(node, "previous");
      if (!previousSibling || previousSibling.nodeName === "BR" || previousSibling.textContent.slice(-1) === " ") {
        newData = newData.slice(1);
      }
    }
    if (newData[newData.length - 1] === " ") {
      const nextSibling = getSibling(node, "next");
      if (!nextSibling || nextSibling.nodeName === "BR" || nextSibling.nodeType === nextSibling.TEXT_NODE && isFormattingSpace(nextSibling.textContent[0])) {
        newData = newData.slice(0, -1);
      }
    }
    if (!newData) {
      node.parentNode.removeChild(node);
    } else {
      node.data = newData;
    }
  }

  // packages/blocks/build-module/api/raw-handling/br-remover.js
  function brRemover(node) {
    if (node.nodeName !== "BR") {
      return;
    }
    if (getSibling(node, "next")) {
      return;
    }
    node.parentNode.removeChild(node);
  }

  // packages/blocks/build-module/api/raw-handling/empty-paragraph-remover.js
  function emptyParagraphRemover(node) {
    if (node.nodeName !== "P") {
      return;
    }
    if (node.hasChildNodes()) {
      return;
    }
    node.parentNode.removeChild(node);
  }

  // packages/blocks/build-module/api/raw-handling/slack-paragraph-corrector.js
  function slackParagraphCorrector(node) {
    if (node.nodeName !== "SPAN") {
      return;
    }
    if (node.getAttribute("data-stringify-type") !== "paragraph-break") {
      return;
    }
    const { parentNode } = node;
    parentNode.insertBefore(node.ownerDocument.createElement("br"), node);
    parentNode.insertBefore(node.ownerDocument.createElement("br"), node);
    parentNode.removeChild(node);
  }

  // packages/blocks/build-module/api/raw-handling/latex-to-math.js
  function isLatexMathMode(text2) {
    const lettersRegex = /[\p{L}\s]+/gu;
    let match;
    while (match = lettersRegex.exec(text2)) {
      if (text2[match.index - 1] === "{") {
        continue;
      }
      let sequence = match[0];
      if (text2[match.index - 1] === "\\") {
        sequence = sequence.replace(/^[a-zA-Z]+/, "");
      }
      if (sequence.length < 6) {
        continue;
      }
      return false;
    }
    if (/\\[a-zA-Z]+\s*\{/g.test(text2)) {
      return true;
    }
    const softClues = [
      (t3) => t3.includes("^") && !t3.startsWith("^"),
      (t3) => ["=", "+", "-", "/", "*"].some(
        (operator) => t3.includes(operator)
      ),
      (t3) => /\\[a-zA-Z]+/g.test(t3)
    ];
    if (softClues.filter((clue) => clue(text2)).length >= 2) {
      return true;
    }
    return false;
  }

  // packages/blocks/build-module/api/raw-handling/heading-transformer.js
  function headingTransformer(node) {
    if (node.nodeType !== node.ELEMENT_NODE) {
      return;
    }
    if (node.tagName === "P" && node.getAttribute("role") === "heading" && node.hasAttribute("aria-level")) {
      const level = parseInt(node.getAttribute("aria-level"), 10);
      if (level >= 1 && level <= 6) {
        const headingTag = `H${level}`;
        const newHeading = node.ownerDocument.createElement(headingTag);
        Array.from(node.attributes).forEach((attr2) => {
          if (attr2.name !== "role" && attr2.name !== "aria-level") {
            newHeading.setAttribute(attr2.name, attr2.value);
          }
        });
        while (node.firstChild) {
          newHeading.appendChild(node.firstChild);
        }
        node.parentNode.replaceChild(newHeading, node);
      }
    }
  }

  // packages/blocks/build-module/api/raw-handling/paste-handler.js
  var log = (...args) => window?.console?.log?.(...args);
  function filterInlineHTML(HTML) {
    HTML = deepFilterHTML(HTML, [
      headRemover,
      googleDocsUIdRemover,
      msListIgnore,
      phrasingContentReducer,
      commentRemover
    ]);
    HTML = (0, import_dom11.removeInvalidHTML)(HTML, (0, import_dom11.getPhrasingContentSchema)("paste"), {
      inline: true
    });
    HTML = deepFilterHTML(HTML, [htmlFormattingRemover, brRemover]);
    log("Processed inline HTML:\n\n", HTML);
    return HTML;
  }
  function pasteHandler({
    HTML = "",
    plainText = "",
    mode = "AUTO",
    tagName
  }) {
    HTML = HTML.replace(/<meta[^>]+>/g, "");
    HTML = HTML.replace(
      /^\s*<html[^>]*>\s*<body[^>]*>(?:\s*<!--\s*StartFragment\s*-->)?/i,
      ""
    );
    HTML = HTML.replace(
      /(?:<!--\s*EndFragment\s*-->\s*)?<\/body>\s*<\/html>\s*$/i,
      ""
    );
    if (mode !== "INLINE") {
      const content = HTML ? HTML : plainText;
      if (content.indexOf("<!-- wp:") !== -1) {
        const parseResult = parse2(content);
        const isSingleFreeFormBlock = parseResult.length === 1 && parseResult[0].name === "core/freeform";
        if (!isSingleFreeFormBlock) {
          return parseResult;
        }
      }
    }
    if (String.prototype.normalize) {
      HTML = HTML.normalize();
    }
    HTML = deepFilterHTML(HTML, [slackParagraphCorrector]);
    const isPlainText = plainText && (!HTML || isPlain(HTML));
    if (isPlainText && isLatexMathMode(plainText)) {
      return [createBlock("core/math", { latex: plainText })];
    }
    if (isPlainText) {
      HTML = plainText;
      if (!/^\s+$/.test(plainText)) {
        HTML = markdownConverter(HTML);
      }
    }
    const pieces = shortcode_converter_default(HTML);
    const hasShortcodes = pieces.length > 1;
    if (isPlainText && !hasShortcodes) {
      if (mode === "AUTO" && plainText.indexOf("\n") === -1 && plainText.indexOf("<p>") !== 0 && HTML.indexOf("<p>") === 0) {
        mode = "INLINE";
      }
    }
    if (mode === "INLINE") {
      return filterInlineHTML(HTML);
    }
    if (mode === "AUTO" && !hasShortcodes && isInlineContent(HTML, tagName)) {
      return filterInlineHTML(HTML);
    }
    const phrasingContentSchema = (0, import_dom11.getPhrasingContentSchema)("paste");
    const blockContentSchema = getBlockContentSchema("paste");
    const blocks = pieces.map((piece) => {
      if (typeof piece !== "string") {
        return piece;
      }
      const filters = [
        googleDocsUIdRemover,
        msListConverter,
        headRemover,
        listReducer,
        imageCorrector,
        phrasingContentReducer,
        specialCommentConverter,
        commentRemover,
        iframeRemover,
        figureContentReducer,
        blockquoteNormaliser(),
        divNormaliser,
        headingTransformer
      ];
      const schema = {
        ...blockContentSchema,
        // Keep top-level phrasing content, normalised by `normaliseBlocks`.
        ...phrasingContentSchema
      };
      piece = deepFilterHTML(piece, filters, blockContentSchema);
      piece = (0, import_dom11.removeInvalidHTML)(piece, schema);
      piece = normaliseBlocks(piece);
      piece = deepFilterHTML(
        piece,
        [htmlFormattingRemover, brRemover, emptyParagraphRemover],
        blockContentSchema
      );
      log("Processed HTML piece:\n\n", piece);
      return htmlToBlocks(piece, pasteHandler);
    }).flat().filter(Boolean);
    if (mode === "AUTO" && blocks.length === 1 && hasBlockSupport(blocks[0].name, "__unstablePasteTextInline", false)) {
      const trimRegex = /^[\n]+|[\n]+$/g;
      const trimmedPlainText = plainText.replace(trimRegex, "");
      if (trimmedPlainText !== "" && trimmedPlainText.indexOf("\n") === -1) {
        return (0, import_dom11.removeInvalidHTML)(
          getBlockInnerHTML(blocks[0]),
          phrasingContentSchema
        ).replace(trimRegex, "");
      }
    }
    return blocks;
  }

  // packages/blocks/build-module/api/raw-handling/index.js
  function deprecatedGetPhrasingContentSchema(context) {
    (0, import_deprecated10.default)("wp.blocks.getPhrasingContentSchema", {
      since: "5.6",
      alternative: "wp.dom.getPhrasingContentSchema"
    });
    return (0, import_dom12.getPhrasingContentSchema)(context);
  }
  function rawHandler({ HTML = "" }) {
    if (HTML.indexOf("<!-- wp:") !== -1) {
      const parseResult = parse2(HTML);
      const isSingleFreeFormBlock = parseResult.length === 1 && parseResult[0].name === "core/freeform";
      if (!isSingleFreeFormBlock) {
        return parseResult;
      }
    }
    const pieces = shortcode_converter_default(HTML);
    const blockContentSchema = getBlockContentSchema();
    return pieces.map((piece) => {
      if (typeof piece !== "string") {
        return piece;
      }
      const filters = [
        // Needed to adjust invalid lists.
        listReducer,
        // Needed to create more and nextpage blocks.
        specialCommentConverter,
        // Needed to create media blocks.
        figureContentReducer,
        // Needed to create the quote block, which cannot handle text
        // without wrapper paragraphs.
        blockquoteNormaliser({ raw: true })
      ];
      piece = deepFilterHTML(piece, filters, blockContentSchema);
      piece = normaliseBlocks(piece, { raw: true });
      return htmlToBlocks(piece, rawHandler);
    }).flat().filter(Boolean);
  }

  // packages/blocks/build-module/api/categories.js
  var import_data6 = __toESM(require_data());
  function getCategories2() {
    return (0, import_data6.select)(store).getCategories();
  }
  function setCategories2(categories2) {
    (0, import_data6.dispatch)(store).setCategories(categories2);
  }
  function updateCategory2(slug, category) {
    (0, import_data6.dispatch)(store).updateCategory(slug, category);
  }

  // packages/blocks/build-module/api/templates.js
  var import_element5 = __toESM(require_element());
  function doBlocksMatchTemplate(blocks = [], template = []) {
    return blocks.length === template.length && template.every(([name, , innerBlocksTemplate], index) => {
      const block = blocks[index];
      return name === block.name && doBlocksMatchTemplate(block.innerBlocks, innerBlocksTemplate);
    });
  }
  var isHTMLAttribute = (attributeDefinition) => attributeDefinition?.source === "html";
  var isQueryAttribute = (attributeDefinition) => attributeDefinition?.source === "query";
  function normalizeAttributes(schema, values) {
    if (!values) {
      return {};
    }
    return Object.fromEntries(
      Object.entries(values).map(([key, value]) => [
        key,
        normalizeAttribute(schema[key], value)
      ])
    );
  }
  function normalizeAttribute(definition, value) {
    if (isHTMLAttribute(definition) && Array.isArray(value)) {
      return (0, import_element5.renderToString)(value);
    }
    if (isQueryAttribute(definition) && value) {
      return value.map((subValues) => {
        return normalizeAttributes(definition.query, subValues);
      });
    }
    return value;
  }
  function synchronizeBlocksWithTemplate(blocks = [], template) {
    if (!template) {
      return blocks;
    }
    return template.map(
      ([name, attributes, innerBlocksTemplate], index) => {
        const block = blocks[index];
        if (block && block.name === name) {
          const innerBlocks = synchronizeBlocksWithTemplate(
            block.innerBlocks,
            innerBlocksTemplate
          );
          return { ...block, innerBlocks };
        }
        const blockType = getBlockType(name);
        const normalizedAttributes = normalizeAttributes(
          blockType?.attributes ?? {},
          attributes
        );
        const [blockName, blockAttributes] = convertLegacyBlockNameAndAttributes(
          name,
          normalizedAttributes
        );
        return createBlock(
          blockName,
          blockAttributes,
          synchronizeBlocksWithTemplate([], innerBlocksTemplate)
        );
      }
    );
  }

  // packages/blocks/build-module/api/index.js
  var fieldsKey = Symbol("fields");
  var formKey = Symbol("form");
  var privateApis = {};
  lock(privateApis, { isContentBlock, fieldsKey, formKey });

  // packages/blocks/build-module/deprecated.js
  var import_deprecated11 = __toESM(require_deprecated());
  function withBlockContentContext(OriginalComponent) {
    (0, import_deprecated11.default)("wp.blocks.withBlockContentContext", {
      since: "6.1"
    });
    return OriginalComponent;
  }
  return __toCommonJS(index_exports);
})();
/*! Bundled license information:

react-is/cjs/react-is.development.js:
  (**
   * @license React
   * react-is.development.js
   *
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   *)

showdown/dist/showdown.js:
  (*! showdown v 1.9.1 - 02-11-2019 *)

is-plain-object/dist/is-plain-object.mjs:
  (*!
   * is-plain-object <https://github.com/jonschlinkert/is-plain-object>
   *
   * Copyright (c) 2014-2017, Jon Schlinkert.
   * Released under the MIT License.
   *)
*/