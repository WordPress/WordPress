(function() {
"use strict";
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

  // node_modules/fast-deep-equal/es6/index.js
  var require_es6 = __commonJS({
    "node_modules/fast-deep-equal/es6/index.js"(exports, module) {
      "use strict";
      module.exports = function equal(a, b2) {
        if (a === b2) return true;
        if (a && b2 && typeof a == "object" && typeof b2 == "object") {
          if (a.constructor !== b2.constructor) return false;
          var length, i, keys;
          if (Array.isArray(a)) {
            length = a.length;
            if (length != b2.length) return false;
            for (i = length; i-- !== 0; )
              if (!equal(a[i], b2[i])) return false;
            return true;
          }
          if (a instanceof Map && b2 instanceof Map) {
            if (a.size !== b2.size) return false;
            for (i of a.entries())
              if (!b2.has(i[0])) return false;
            for (i of a.entries())
              if (!equal(i[1], b2.get(i[0]))) return false;
            return true;
          }
          if (a instanceof Set && b2 instanceof Set) {
            if (a.size !== b2.size) return false;
            for (i of a.entries())
              if (!b2.has(i[0])) return false;
            return true;
          }
          if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b2)) {
            length = a.length;
            if (length != b2.length) return false;
            for (i = length; i-- !== 0; )
              if (a[i] !== b2[i]) return false;
            return true;
          }
          if (a.constructor === RegExp) return a.source === b2.source && a.flags === b2.flags;
          if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b2.valueOf();
          if (a.toString !== Object.prototype.toString) return a.toString() === b2.toString();
          keys = Object.keys(a);
          length = keys.length;
          if (length !== Object.keys(b2).length) return false;
          for (i = length; i-- !== 0; )
            if (!Object.prototype.hasOwnProperty.call(b2, keys[i])) return false;
          for (i = length; i-- !== 0; ) {
            var key = keys[i];
            if (!equal(a[key], b2[key])) return false;
          }
          return true;
        }
        return a !== a && b2 !== b2;
      };
    }
  });

  // packages/blocks/node_modules/react-is/cjs/react-is.development.js
  var require_react_is_development = __commonJS({
    "packages/blocks/node_modules/react-is/cjs/react-is.development.js"(exports) {
      "use strict";
      if (true) {
        (function() {
          "use strict";
          var REACT_ELEMENT_TYPE = /* @__PURE__ */ Symbol.for("react.element");
          var REACT_PORTAL_TYPE = /* @__PURE__ */ Symbol.for("react.portal");
          var REACT_FRAGMENT_TYPE = /* @__PURE__ */ Symbol.for("react.fragment");
          var REACT_STRICT_MODE_TYPE = /* @__PURE__ */ Symbol.for("react.strict_mode");
          var REACT_PROFILER_TYPE = /* @__PURE__ */ Symbol.for("react.profiler");
          var REACT_PROVIDER_TYPE = /* @__PURE__ */ Symbol.for("react.provider");
          var REACT_CONTEXT_TYPE = /* @__PURE__ */ Symbol.for("react.context");
          var REACT_SERVER_CONTEXT_TYPE = /* @__PURE__ */ Symbol.for("react.server_context");
          var REACT_FORWARD_REF_TYPE = /* @__PURE__ */ Symbol.for("react.forward_ref");
          var REACT_SUSPENSE_TYPE = /* @__PURE__ */ Symbol.for("react.suspense");
          var REACT_SUSPENSE_LIST_TYPE = /* @__PURE__ */ Symbol.for("react.suspense_list");
          var REACT_MEMO_TYPE = /* @__PURE__ */ Symbol.for("react.memo");
          var REACT_LAZY_TYPE = /* @__PURE__ */ Symbol.for("react.lazy");
          var REACT_OFFSCREEN_TYPE = /* @__PURE__ */ Symbol.for("react.offscreen");
          var enableScopeAPI = false;
          var enableCacheElement = false;
          var enableTransitionTracing = false;
          var enableLegacyHidden = false;
          var enableDebugTracing = false;
          var REACT_MODULE_REFERENCE;
          {
            REACT_MODULE_REFERENCE = /* @__PURE__ */ Symbol.for("react.module.reference");
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

  // packages/blocks/build-module/index.mjs
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
    cloneSanitizedBlock: () => cloneSanitizedBlock,
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
    sanitizeBlockAttributes: () => sanitizeBlockAttributes,
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

  // packages/blocks/build-module/store/index.mjs
  var import_data5 = __toESM(require_data(), 1);

  // node_modules/tslib/tslib.es6.mjs
  var __assign = function() {
    __assign = Object.assign || function __assign2(t2) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
        s = arguments[i];
        for (var p2 in s) if (Object.prototype.hasOwnProperty.call(s, p2)) t2[p2] = s[p2];
      }
      return t2;
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

  // packages/blocks/build-module/store/reducer.mjs
  var import_data2 = __toESM(require_data(), 1);
  var import_i18n3 = __toESM(require_i18n(), 1);

  // node_modules/colord/index.mjs
  for (r2 = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) }, t2 = function(r3) {
    return "string" == typeof r3 ? r3.length > 0 : "number" == typeof r3;
  }, n = function(r3, t3, n2) {
    return void 0 === t3 && (t3 = 0), void 0 === n2 && (n2 = Math.pow(10, t3)), Math.round(n2 * r3) / n2 + 0;
  }, u = function(r3, t3, n2) {
    return void 0 === t3 && (t3 = 0), void 0 === n2 && (n2 = 1), r3 > n2 ? n2 : r3 > t3 ? r3 : t3;
  }, e2 = function(r3) {
    return (r3 = isFinite(r3) ? r3 % 360 : 0) < 0 ? r3 + 360 : r3;
  }, o2 = function(r3, t3) {
    return void 0 === t3 && (t3 = 0), n(r3, t3) % 360;
  }, a = function(r3) {
    return { r: u(r3.r, 0, 255), g: u(r3.g, 0, 255), b: u(r3.b, 0, 255), a: u(r3.a) };
  }, i = function(r3) {
    return { r: n(r3.r), g: n(r3.g), b: n(r3.b), a: n(r3.a, 3) };
  }, s = /^#([0-9a-f]{3,8})$/i, d2 = function(r3, t3) {
    var n2 = r3.charCodeAt(t3);
    return (15 & n2) + 9 * (n2 >> 6);
  }, h = function(r3, t3) {
    return d2(r3, t3) << 4 | d2(r3, t3 + 1);
  }, b2 = [], f2 = 0; f2 < 256; f2++) b2.push((f2 < 16 ? "0" : "") + f2.toString(16));
  var r2;
  var t2;
  var n;
  var u;
  var e2;
  var o2;
  var a;
  var i;
  var s;
  var d2;
  var h;
  var b2;
  var f2;
  var g = function(r2) {
    return b2[u(r2, 0, 255)];
  };
  var c = function(r2) {
    var t2 = r2.r, n = r2.g, u = r2.b, e2 = r2.a, o2 = Math.max(t2, n, u), a = o2 - Math.min(t2, n, u), i = a ? o2 === t2 ? (n - u) / a : o2 === n ? 2 + (u - t2) / a : 4 + (t2 - n) / a : 0;
    return { h: 60 * (i < 0 ? i + 6 : i), s: o2 ? a / o2 * 100 : 0, v: o2 / 255 * 100, a: e2 };
  };
  var v = function(r2) {
    var t2 = r2.h, n = r2.s, u = r2.v, e2 = r2.a;
    t2 = t2 / 360 * 6, n /= 100, u /= 100;
    var o2 = Math.floor(t2), a = u * (1 - n), i = u * (1 - (t2 - o2) * n), s = u * (1 - (1 - t2 + o2) * n), d2 = o2 % 6;
    return { r: 255 * [u, i, a, a, s, u][d2], g: 255 * [s, u, u, i, a, a][d2], b: 255 * [a, a, s, u, u, i][d2], a: e2 };
  };
  var l = function(r2) {
    return { h: e2(r2.h), s: u(r2.s, 0, 100), l: u(r2.l, 0, 100), a: u(r2.a) };
  };
  var p = function(r2) {
    return { h: o2(r2.h), s: n(r2.s), l: n(r2.l), a: n(r2.a, 3) };
  };
  var m = function(r2) {
    return v((n = (t2 = r2).s, { h: t2.h, s: (n *= ((u = t2.l) < 50 ? u : 100 - u) / 100) > 0 ? 2 * n / (u + n) * 100 : 0, v: u + n, a: t2.a }));
    var t2, n, u;
  };
  var y = function(r2) {
    return { h: (t2 = c(r2)).h, s: (e2 = (200 - (n = t2.s)) * (u = t2.v) / 100) > 0 && e2 < 200 ? n * u / 100 / (e2 <= 100 ? e2 : 200 - e2) * 100 : 0, l: e2 / 2, a: t2.a };
    var t2, n, u, e2;
  };
  var N = /^hsla?\(\s*([+-]?(?:\d*\.\d+|\d+))(deg|rad|grad|turn)?\s*,\s*([+-]?(?:\d*\.\d+|\d+))%\s*,\s*([+-]?(?:\d*\.\d+|\d+))%\s*(?:,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
  var x = /^hsla?\(\s*([+-]?(?:\d*\.\d+|\d+))(deg|rad|grad|turn)?\s+([+-]?(?:\d*\.\d+|\d+))%\s+([+-]?(?:\d*\.\d+|\d+))%\s*(?:\/\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
  var M = /^rgba?\(\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*(?:,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
  var H = /^rgba?\(\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s+([+-]?(?:\d*\.\d+|\d+))(%)?\s+([+-]?(?:\d*\.\d+|\d+))(%)?\s*(?:\/\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
  var $ = { string: [[function(r2) {
    if (!s.test(r2)) return null;
    var t2 = r2.length;
    return t2 <= 5 ? { r: 17 * d2(r2, 1), g: 17 * d2(r2, 2), b: 17 * d2(r2, 3), a: 5 === t2 ? n(17 * d2(r2, 4) / 255, 2) : 1 } : 7 === t2 || 9 === t2 ? { r: h(r2, 1), g: h(r2, 3), b: h(r2, 5), a: 9 === t2 ? n(h(r2, 7) / 255, 2) : 1 } : null;
  }, "hex"], [function(r2) {
    var t2 = M.exec(r2) || H.exec(r2);
    return t2 ? t2[2] !== t2[4] || t2[4] !== t2[6] ? null : a({ r: Number(t2[1]) / (t2[2] ? 100 / 255 : 1), g: Number(t2[3]) / (t2[4] ? 100 / 255 : 1), b: Number(t2[5]) / (t2[6] ? 100 / 255 : 1), a: void 0 === t2[7] ? 1 : Number(t2[7]) / (t2[8] ? 100 : 1) }) : null;
  }, "rgb"], [function(t2) {
    var n = N.exec(t2) || x.exec(t2);
    if (!n) return null;
    var u, e2, o2 = l({ h: (u = n[1], e2 = n[2], void 0 === e2 && (e2 = "deg"), Number(u) * (r2[e2] || 1)), s: Number(n[3]), l: Number(n[4]), a: void 0 === n[5] ? 1 : Number(n[5]) / (n[6] ? 100 : 1) });
    return m(o2);
  }, "hsl"]], object: [[function(r2) {
    var n = r2.r, u = r2.g, e2 = r2.b, o2 = r2.a, i = void 0 === o2 ? 1 : o2;
    return t2(n) && t2(u) && t2(e2) ? a({ r: Number(n), g: Number(u), b: Number(e2), a: Number(i) }) : null;
  }, "rgb"], [function(r2) {
    var n = r2.h, u = r2.s, e2 = r2.l, o2 = r2.a, a = void 0 === o2 ? 1 : o2;
    if (!t2(n) || !t2(u) || !t2(e2)) return null;
    var i = l({ h: Number(n), s: Number(u), l: Number(e2), a: Number(a) });
    return m(i);
  }, "hsl"], [function(r2) {
    var n = r2.h, o2 = r2.s, a = r2.v, i = r2.a, s = void 0 === i ? 1 : i;
    if (!t2(n) || !t2(o2) || !t2(a)) return null;
    var d2 = (function(r3) {
      return { h: e2(r3.h), s: u(r3.s, 0, 100), v: u(r3.v, 0, 100), a: u(r3.a) };
    })({ h: Number(n), s: Number(o2), v: Number(a), a: Number(s) });
    return v(d2);
  }, "hsv"]] };
  var j = function(r2, t2) {
    for (var n = 0; n < t2.length; n++) {
      var u = t2[n][0](r2);
      if (u) return [u, t2[n][1]];
    }
    return [null, void 0];
  };
  var w = function(r2) {
    return "string" == typeof r2 ? j(r2.trim(), $.string) : "object" == typeof r2 && null !== r2 ? j(r2, $.object) : [null, void 0];
  };
  var k = function(r2, t2) {
    var n = y(r2);
    return { h: n.h, s: u(n.s + 100 * t2, 0, 100), l: n.l, a: n.a };
  };
  var E = function(r2) {
    return (299 * r2.r + 587 * r2.g + 114 * r2.b) / 1e3 / 255;
  };
  var R = function(r2, t2) {
    var n = y(r2);
    return { h: n.h, s: n.s, l: u(n.l + 100 * t2, 0, 100), a: n.a };
  };
  var q = (function() {
    function r2(r3) {
      this.parsed = w(r3)[0], this.rgba = this.parsed || { r: 0, g: 0, b: 0, a: 1 };
    }
    return r2.prototype.isValid = function() {
      return null !== this.parsed;
    }, r2.prototype.brightness = function() {
      return n(E(this.rgba), 2);
    }, r2.prototype.isDark = function() {
      return E(this.rgba) < 0.5;
    }, r2.prototype.isLight = function() {
      return E(this.rgba) >= 0.5;
    }, r2.prototype.toHex = function() {
      return r3 = i(this.rgba), t2 = r3.r, u = r3.g, e2 = r3.b, a = (o2 = r3.a) < 1 ? g(n(255 * o2)) : "", "#" + g(t2) + g(u) + g(e2) + a;
      var r3, t2, u, e2, o2, a;
    }, r2.prototype.toRgb = function() {
      return i(this.rgba);
    }, r2.prototype.toRgbString = function() {
      return r3 = i(this.rgba), t2 = r3.r, n = r3.g, u = r3.b, (e2 = r3.a) < 1 ? "rgba(" + t2 + ", " + n + ", " + u + ", " + e2 + ")" : "rgb(" + t2 + ", " + n + ", " + u + ")";
      var r3, t2, n, u, e2;
    }, r2.prototype.toHsl = function() {
      return p(y(this.rgba));
    }, r2.prototype.toHslString = function() {
      return r3 = p(y(this.rgba)), t2 = r3.h, n = r3.s, u = r3.l, (e2 = r3.a) < 1 ? "hsla(" + t2 + ", " + n + "%, " + u + "%, " + e2 + ")" : "hsl(" + t2 + ", " + n + "%, " + u + "%)";
      var r3, t2, n, u, e2;
    }, r2.prototype.toHsv = function() {
      return r3 = c(this.rgba), { h: o2(r3.h), s: n(r3.s), v: n(r3.v), a: n(r3.a, 3) };
      var r3;
    }, r2.prototype.invert = function() {
      return A({ r: 255 - (r3 = this.rgba).r, g: 255 - r3.g, b: 255 - r3.b, a: r3.a });
      var r3;
    }, r2.prototype.saturate = function(r3) {
      return void 0 === r3 && (r3 = 0.1), A(k(this.rgba, r3));
    }, r2.prototype.desaturate = function(r3) {
      return void 0 === r3 && (r3 = 0.1), A(k(this.rgba, -r3));
    }, r2.prototype.grayscale = function() {
      return A(k(this.rgba, -1));
    }, r2.prototype.lighten = function(r3) {
      return void 0 === r3 && (r3 = 0.1), A(R(this.rgba, r3));
    }, r2.prototype.darken = function(r3) {
      return void 0 === r3 && (r3 = 0.1), A(R(this.rgba, -r3));
    }, r2.prototype.rotate = function(r3) {
      return void 0 === r3 && (r3 = 15), this.hue(y(this.rgba).h + r3);
    }, r2.prototype.alpha = function(r3) {
      return "number" == typeof r3 ? A({ r: (t2 = this.rgba).r, g: t2.g, b: t2.b, a: r3 }) : n(this.rgba.a, 3);
      var t2;
    }, r2.prototype.hue = function(r3) {
      var t2 = y(this.rgba);
      return "number" == typeof r3 ? A({ h: r3, s: t2.s, l: t2.l, a: t2.a }) : o2(t2.h);
    }, r2.prototype.isEqual = function(r3) {
      return this.toHex() === A(r3).toHex();
    }, r2;
  })();
  var A = function(r2) {
    return r2 instanceof q ? r2 : new q(r2);
  };
  var C = [];
  var D = function(r2) {
    r2.forEach(function(r3) {
      C.indexOf(r3) < 0 && (r3(q, $), C.push(r3));
    });
  };

  // node_modules/colord/plugins/names.mjs
  var e = function(e2, f2) {
    var a = { white: "#ffffff", bisque: "#ffe4c4", blue: "#0000ff", cadetblue: "#5f9ea0", chartreuse: "#7fff00", chocolate: "#d2691e", coral: "#ff7f50", antiquewhite: "#faebd7", aqua: "#00ffff", azure: "#f0ffff", whitesmoke: "#f5f5f5", papayawhip: "#ffefd5", plum: "#dda0dd", blanchedalmond: "#ffebcd", black: "#000000", gold: "#ffd700", goldenrod: "#daa520", gainsboro: "#dcdcdc", cornsilk: "#fff8dc", cornflowerblue: "#6495ed", burlywood: "#deb887", aquamarine: "#7fffd4", beige: "#f5f5dc", crimson: "#dc143c", cyan: "#00ffff", darkblue: "#00008b", darkcyan: "#008b8b", darkgoldenrod: "#b8860b", darkkhaki: "#bdb76b", darkgray: "#a9a9a9", darkgreen: "#006400", darkgrey: "#a9a9a9", peachpuff: "#ffdab9", darkmagenta: "#8b008b", darkred: "#8b0000", darkorchid: "#9932cc", darkorange: "#ff8c00", darkslateblue: "#483d8b", gray: "#808080", darkslategray: "#2f4f4f", darkslategrey: "#2f4f4f", deeppink: "#ff1493", deepskyblue: "#00bfff", wheat: "#f5deb3", firebrick: "#b22222", floralwhite: "#fffaf0", ghostwhite: "#f8f8ff", darkviolet: "#9400d3", magenta: "#ff00ff", green: "#008000", dodgerblue: "#1e90ff", grey: "#808080", honeydew: "#f0fff0", hotpink: "#ff69b4", blueviolet: "#8a2be2", forestgreen: "#228b22", lawngreen: "#7cfc00", indianred: "#cd5c5c", indigo: "#4b0082", fuchsia: "#ff00ff", brown: "#a52a2a", maroon: "#800000", mediumblue: "#0000cd", lightcoral: "#f08080", darkturquoise: "#00ced1", lightcyan: "#e0ffff", ivory: "#fffff0", lightyellow: "#ffffe0", lightsalmon: "#ffa07a", lightseagreen: "#20b2aa", linen: "#faf0e6", mediumaquamarine: "#66cdaa", lemonchiffon: "#fffacd", lime: "#00ff00", khaki: "#f0e68c", mediumseagreen: "#3cb371", limegreen: "#32cd32", mediumspringgreen: "#00fa9a", lightskyblue: "#87cefa", lightblue: "#add8e6", midnightblue: "#191970", lightpink: "#ffb6c1", mistyrose: "#ffe4e1", moccasin: "#ffe4b5", mintcream: "#f5fffa", lightslategray: "#778899", lightslategrey: "#778899", navajowhite: "#ffdead", navy: "#000080", mediumvioletred: "#c71585", powderblue: "#b0e0e6", palegoldenrod: "#eee8aa", oldlace: "#fdf5e6", paleturquoise: "#afeeee", mediumturquoise: "#48d1cc", mediumorchid: "#ba55d3", rebeccapurple: "#663399", lightsteelblue: "#b0c4de", mediumslateblue: "#7b68ee", thistle: "#d8bfd8", tan: "#d2b48c", orchid: "#da70d6", mediumpurple: "#9370db", purple: "#800080", pink: "#ffc0cb", skyblue: "#87ceeb", springgreen: "#00ff7f", palegreen: "#98fb98", red: "#ff0000", yellow: "#ffff00", slateblue: "#6a5acd", lavenderblush: "#fff0f5", peru: "#cd853f", palevioletred: "#db7093", violet: "#ee82ee", teal: "#008080", slategray: "#708090", slategrey: "#708090", aliceblue: "#f0f8ff", darkseagreen: "#8fbc8f", darkolivegreen: "#556b2f", greenyellow: "#adff2f", seagreen: "#2e8b57", seashell: "#fff5ee", tomato: "#ff6347", silver: "#c0c0c0", sienna: "#a0522d", lavender: "#e6e6fa", lightgreen: "#90ee90", orange: "#ffa500", orangered: "#ff4500", steelblue: "#4682b4", royalblue: "#4169e1", turquoise: "#40e0d0", yellowgreen: "#9acd32", salmon: "#fa8072", saddlebrown: "#8b4513", sandybrown: "#f4a460", rosybrown: "#bc8f8f", darksalmon: "#e9967a", lightgoldenrodyellow: "#fafad2", snow: "#fffafa", lightgrey: "#d3d3d3", lightgray: "#d3d3d3", dimgray: "#696969", dimgrey: "#696969", olivedrab: "#6b8e23", olive: "#808000" }, r2 = {};
    for (var d2 in a) r2[a[d2]] = d2;
    var l4 = {};
    e2.prototype.toName = function(f3) {
      if (!(this.rgba.a || this.rgba.r || this.rgba.g || this.rgba.b)) return "transparent";
      var d3, i, n = r2[this.toHex()];
      if (n) return n;
      if (null == f3 ? void 0 : f3.closest) {
        var o2 = this.toRgb(), t2 = 1 / 0, b2 = "black";
        if (!l4.length) for (var c2 in a) l4[c2] = new e2(a[c2]).toRgb();
        for (var g2 in a) {
          var u = (d3 = o2, i = l4[g2], Math.pow(d3.r - i.r, 2) + Math.pow(d3.g - i.g, 2) + Math.pow(d3.b - i.b, 2));
          u < t2 && (t2 = u, b2 = g2);
        }
        return b2;
      }
    };
    f2.string.push([function(f3) {
      var r3 = f3.toLowerCase(), d3 = "transparent" === r3 ? "#0000" : a[r3];
      return d3 ? new e2(d3).toRgb() : null;
    }, "name"]);
  };

  // node_modules/colord/plugins/a11y.mjs
  var o = function(o2) {
    var t2 = o2 / 255;
    return t2 < 0.04045 ? t2 / 12.92 : Math.pow((t2 + 0.055) / 1.055, 2.4);
  };
  var t = function(t2) {
    return 0.2126 * o(t2.r) + 0.7152 * o(t2.g) + 0.0722 * o(t2.b);
  };
  var r = function(o2) {
    o2.prototype.luminance = function() {
      return o3 = t(this.rgba), void 0 === (r2 = 2) && (r2 = 0), void 0 === n && (n = Math.pow(10, r2)), Math.round(n * o3) / n + 0;
      var o3, r2, n;
    }, o2.prototype.contrast = function(r2) {
      void 0 === r2 && (r2 = "#FFF");
      var n, a, i, e2, v3, u, d2, c2 = r2 instanceof o2 ? r2 : new o2(r2);
      return e2 = this.rgba, v3 = c2.toRgb(), u = t(e2), d2 = t(v3), n = u > d2 ? (u + 0.05) / (d2 + 0.05) : (d2 + 0.05) / (u + 0.05), void 0 === (a = 2) && (a = 0), void 0 === i && (i = Math.pow(10, a)), Math.floor(i * n) / i + 0;
    }, o2.prototype.isReadable = function(o3, t2) {
      return void 0 === o3 && (o3 = "#FFF"), void 0 === t2 && (t2 = {}), this.contrast(o3) >= (e2 = void 0 === (i = (r2 = t2).size) ? "normal" : i, "AAA" === (a = void 0 === (n = r2.level) ? "AA" : n) && "normal" === e2 ? 7 : "AA" === a && "large" === e2 ? 3 : 4.5);
      var r2, n, a, i, e2;
    };
  };

  // packages/blocks/build-module/api/utils.mjs
  var import_element = __toESM(require_element(), 1);
  var import_i18n2 = __toESM(require_i18n(), 1);
  var import_dom = __toESM(require_dom(), 1);
  var import_rich_text = __toESM(require_rich_text(), 1);
  var import_deprecated = __toESM(require_deprecated(), 1);

  // packages/blocks/build-module/api/constants.mjs
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
    backgroundGradient: {
      value: ["background", "gradient"],
      support: ["background", "gradient"],
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
    minWidth: {
      value: ["dimensions", "minWidth"],
      support: ["dimensions", "minWidth"],
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
    textIndent: {
      value: ["typography", "textIndent"],
      support: ["typography", "textIndent"],
      useEngine: true
    },
    textShadow: {
      value: ["typography", "textShadow"],
      support: ["typography", "textShadow"],
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
    label: "label",
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

  // packages/blocks/build-module/api/registration.mjs
  var import_data = __toESM(require_data(), 1);
  var import_i18n = __toESM(require_i18n(), 1);
  var import_warning = __toESM(require_warning(), 1);

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

  // packages/blocks/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/blocks"
  );

  // packages/blocks/build-module/api/registration.mjs
  function isObject(object) {
    return object !== null && typeof object === "object";
  }
  function unstable__bootstrapServerSideBlockDefinitions(definitions) {
    const { addBootstrappedBlockType: addBootstrappedBlockType2 } = unlock((0, import_data.dispatch)(store));
    for (const [name, blockType] of Object.entries(definitions)) {
      addBootstrappedBlockType2(name, blockType);
    }
  }
  function getBlockSettingsFromMetadata({
    textdomain,
    ...metadata
  }) {
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
      return Object.keys(settingValue).reduce(
        (accumulator, key) => {
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
        },
        {}
      );
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
    if (Array.isArray(variation)) {
      for (const v3 of variation) {
        if (typeof v3.name !== "string") {
          (0, import_warning.default)("Variation names must be unique strings.");
        }
      }
    } else if (typeof variation.name !== "string") {
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

  // packages/blocks/build-module/api/utils.mjs
  D([e, r]);
  var ICON_COLORS = ["#191e23", "#f8f9f9"];
  function isUnmodifiedBlock(block, role) {
    const blockAttributes = getBlockType(block.name)?.attributes ?? {};
    const attributesByRole = role ? Object.entries(blockAttributes).filter(([key, definition]) => {
      if (role === "content" && key === "metadata") {
        return Object.keys(
          block.attributes[key]?.bindings ?? {}
        ).length > 0;
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
    const resolvedIcon = icon || BLOCK_ICON_DEFAULT;
    if (isValidIcon(resolvedIcon)) {
      return { src: resolvedIcon };
    }
    const iconDescriptor = resolvedIcon;
    if ("background" in iconDescriptor) {
      const colordBgColor = A(iconDescriptor.background);
      const getColorContrast = (iconColor) => colordBgColor.contrast(iconColor);
      const maxContrast = Math.max(...ICON_COLORS.map(getColorContrast));
      return {
        ...iconDescriptor,
        foreground: iconDescriptor.foreground ? iconDescriptor.foreground : ICON_COLORS.find(
          (iconColor) => getColorContrast(iconColor) === maxContrast
        ),
        shadowColor: colordBgColor.alpha(0.3).toRgbString()
      };
    }
    return iconDescriptor;
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
    const title = blockType?.title ?? "";
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
    return void 0;
  }
  function isBlockRegistered(name) {
    return getBlockType(name) !== void 0;
  }
  function sanitizeBlockAttributes(name, attributes) {
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
        if (["node", "children"].indexOf(schema.source ?? "") !== -1) {
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
  function __experimentalSanitizeBlockAttributes(name, attributes) {
    (0, import_deprecated.default)("__experimentalSanitizeBlockAttributes", {
      since: "7.1",
      alternative: "sanitizeBlockAttributes"
    });
    return sanitizeBlockAttributes(name, attributes);
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
    const keysArray = Array.isArray(keys) ? keys : [keys];
    return Object.fromEntries(
      Object.entries(object).filter(
        ([key]) => !keysArray.includes(key)
      )
    );
  }

  // packages/blocks/build-module/store/reducer.mjs
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

  // packages/blocks/build-module/store/selectors.mjs
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
  var import_remove_accents = __toESM(require_remove_accents(), 1);
  var import_es6 = __toESM(require_es6(), 1);
  var import_data4 = __toESM(require_data(), 1);
  var import_rich_text2 = __toESM(require_rich_text(), 1);
  var import_deprecated3 = __toESM(require_deprecated(), 1);

  // packages/blocks/build-module/store/utils.mjs
  var getValueFromObjectPath = (object, path, defaultValue) => {
    const normalizedPath = Array.isArray(path) ? path : path.split(".");
    let value = object;
    normalizedPath.forEach((fieldName) => {
      value = value?.[fieldName];
    });
    return value ?? defaultValue;
  };
  function isObject2(candidate) {
    return typeof candidate === "object" && candidate !== null && candidate.constructor === Object;
  }
  function matchesAttributes(blockAttributes, variationAttributes) {
    if (isObject2(blockAttributes) && isObject2(variationAttributes)) {
      return Object.entries(variationAttributes).every(
        ([key, value]) => matchesAttributes(blockAttributes?.[key], value)
      );
    }
    return blockAttributes === variationAttributes;
  }

  // packages/blocks/build-module/store/private-selectors.mjs
  var private_selectors_exports = {};
  __export(private_selectors_exports, {
    getAllBlockBindingsSources: () => getAllBlockBindingsSources,
    getBlockBindingsSource: () => getBlockBindingsSource2,
    getBlockBindingsSourceFieldsList: () => getBlockBindingsSourceFieldsList,
    getBlockKeyboardShortcuts: () => getBlockKeyboardShortcuts,
    getBootstrappedBlockType: () => getBootstrappedBlockType,
    getSupportedStyles: () => getSupportedStyles,
    getUnprocessedBlockTypes: () => getUnprocessedBlockTypes,
    hasContentRoleAttribute: () => hasContentRoleAttribute
  });
  var import_data3 = __toESM(require_data(), 1);
  var import_deprecated2 = __toESM(require_deprecated(), 1);
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
    "textIndent",
    "textTransform",
    "textShadow",
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
      if (support === "textIndent" && !name) {
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
      const supports = blockType?.supports;
      if (supports?.spacing?.blockGap) {
        supportKeys.push("blockGap");
      }
      if (supports?.shadow) {
        supportKeys.push("shadow");
      }
      const stylePropertyMap = __EXPERIMENTAL_STYLE_PROPERTY;
      Object.keys(stylePropertyMap).forEach((styleName) => {
        if (!stylePropertyMap[styleName].support) {
          return;
        }
        if (stylePropertyMap[styleName].requiresOptOut) {
          if (supports && stylePropertyMap[styleName].support[0] in supports && getValueFromObjectPath(
            supports,
            stylePropertyMap[styleName].support
          ) !== false) {
            supportKeys.push(styleName);
            return;
          }
        }
        if (supports && getValueFromObjectPath(
          supports,
          stylePropertyMap[styleName].support,
          false
        )) {
          supportKeys.push(styleName);
        }
      });
      return filterElementBlockSupports(supportKeys, name, element);
    },
    (state, name) => [
      state.blockTypes[name]
    ]
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
      (state, source, blockContext) => [source.getFieldsList, source.usesContext, blockContext]
    )
  );
  var getBlockKeyboardShortcuts = (0, import_data3.createSelector)(
    (state) => {
      const shortcuts = [];
      for (const blockName of Object.keys(state.blockTypes)) {
        for (const variation of state.blockVariations[blockName] ?? []) {
          if (!variation.shortcut) {
            continue;
          }
          shortcuts.push({
            ...variation.shortcut,
            targetBlockName: blockName,
            blockNames: [blockName],
            variationName: variation.name
          });
        }
        const transforms = state.blockTypes[blockName]?.transforms;
        for (const transform of transforms?.to ?? []) {
          if (transform.type !== "block") {
            continue;
          }
          const targetBlockName = transform.blocks?.[0];
          if (!targetBlockName) {
            continue;
          }
          for (const shortcut of transform.shortcuts ?? []) {
            shortcuts.push({
              ...shortcut,
              targetBlockName,
              blockNames: [blockName],
              variationName: shortcut.variationName ?? transform.variationName
            });
          }
        }
        for (const transform of transforms?.from ?? []) {
          if (transform.type !== "block" || !transform.blocks?.length) {
            continue;
          }
          for (const shortcut of transform.shortcuts ?? []) {
            shortcuts.push({
              ...shortcut,
              targetBlockName: blockName,
              blockNames: transform.blocks,
              variationName: shortcut.variationName ?? transform.variationName
            });
          }
        }
      }
      return shortcuts;
    },
    (state) => [state.blockTypes, state.blockVariations]
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

  // packages/blocks/build-module/store/selectors.mjs
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
    (state, blockName) => [
      state.blockVariations[blockName]
    ]
  );
  function getActiveBlockVariation(state, blockName, attributes, scope, innerContent) {
    const variations = getBlockVariations2(state, blockName, scope);
    if (!variations) {
      return variations;
    }
    if (innerContent) {
      const innerContentMatch = variations.find(
        (variation) => variation.innerContent && (0, import_es6.default)(variation.innerContent, innerContent)
      );
      if (innerContentMatch) {
        return innerContentMatch;
      }
    }
    const blockType = getBlockType2(state, blockName);
    const attributeKeys = Object.keys(blockType?.attributes || {});
    let match;
    let maxMatchedAttributes = 0;
    for (const variation of variations) {
      if (variation.innerContent) {
        continue;
      }
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
      } else if (variation.isActive?.(
        attributes,
        variation.attributes
      )) {
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
    const defaultVariation = [...variations || []].reverse().find(({ isDefault }) => !!isDefault);
    return defaultVariation || variations?.[0];
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
    return isSearchMatch(blockType?.title) || blockType?.keywords?.some(isSearchMatch) || isSearchMatch(blockType?.category) || typeof blockType?.description === "string" && isSearchMatch(blockType.description);
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

  // packages/blocks/build-module/store/actions.mjs
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
  var import_deprecated5 = __toESM(require_deprecated(), 1);

  // node_modules/is-plain-object/dist/is-plain-object.mjs
  function isObject3(o2) {
    return Object.prototype.toString.call(o2) === "[object Object]";
  }
  function isPlainObject(o2) {
    var ctor, prot;
    if (isObject3(o2) === false) return false;
    ctor = o2.constructor;
    if (ctor === void 0) return true;
    prot = ctor.prototype;
    if (isObject3(prot) === false) return false;
    if (prot.hasOwnProperty("isPrototypeOf") === false) {
      return false;
    }
    return true;
  }

  // packages/blocks/build-module/store/process-block-type.mjs
  var import_react_is = __toESM(require_react_is(), 1);
  var import_deprecated4 = __toESM(require_deprecated(), 1);
  var import_hooks = __toESM(require_hooks(), 1);
  var import_warning2 = __toESM(require_warning(), 1);
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
  var processBlockType = (name, blockSettings) => ({
    select: select3
  }) => {
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
      (0, import_deprecated4.default)("Block with API version 2 or lower", {
        since: "6.9",
        hint: `The block "${name}" is registered with API version ${settings.apiVersion}. This means that the post editor may work as a non-iframe editor. Since all editors are planned to work as iframes in the future, set the \`apiVersion\` field to 3 and test the block inside the iframe editor.`,
        link: "https://developer.wordpress.org/block-editor/reference-guides/block-api/block-api-versions/block-migration-for-iframe-editor-compatibility/"
      });
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
    if (typeof settings.category === "string" && LEGACY_CATEGORY_MAPPING.hasOwnProperty(settings.category)) {
      settings.category = LEGACY_CATEGORY_MAPPING[settings.category];
    }
    if ("category" in settings && !select3.getCategories().some(
      ({ slug }) => slug === settings.category
    )) {
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
    const icon = normalizeIconObject(settings.icon);
    settings.icon = icon;
    if (!isValidIcon(icon.src)) {
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
        "Parent must be undefined or an array of block types, but it is " + settings.parent
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

  // packages/blocks/build-module/store/actions.mjs
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

  // packages/blocks/build-module/store/private-actions.mjs
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

  // packages/blocks/build-module/store/constants.mjs
  var STORE_NAME = "core/blocks";

  // packages/blocks/build-module/store/index.mjs
  var store = (0, import_data5.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  });
  (0, import_data5.register)(store);
  unlock(store).registerPrivateSelectors(private_selectors_exports);
  unlock(store).registerPrivateActions(private_actions_exports);

  // node_modules/uuid/dist/stringify.js
  var byteToHex = [];
  for (let i = 0; i < 256; ++i) {
    byteToHex.push((i + 256).toString(16).slice(1));
  }
  function unsafeStringify(arr, offset = 0) {
    return (byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + "-" + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + "-" + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + "-" + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + "-" + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]]).toLowerCase();
  }

  // node_modules/uuid/dist/rng.js
  var rnds8 = new Uint8Array(16);
  function rng() {
    return crypto.getRandomValues(rnds8);
  }

  // node_modules/uuid/dist/v4.js
  function v4(options, buf, offset) {
    if (!buf && !options && crypto.randomUUID) {
      return crypto.randomUUID();
    }
    return _v4(options, buf, offset);
  }
  function _v4(options, buf, offset) {
    options = options || {};
    const rnds = options.random ?? options.rng?.() ?? rng();
    if (rnds.length < 16) {
      throw new Error("Random bytes length must be >= 16");
    }
    rnds[6] = rnds[6] & 15 | 64;
    rnds[8] = rnds[8] & 63 | 128;
    if (buf) {
      offset = offset || 0;
      if (offset < 0 || offset + 16 > buf.length) {
        throw new RangeError(`UUID byte range ${offset}:${offset + 15} is out of buffer bounds`);
      }
      for (let i = 0; i < 16; ++i) {
        buf[offset + i] = rnds[i];
      }
      return buf;
    }
    return unsafeStringify(rnds);
  }
  var v4_default = v4;

  // packages/blocks/build-module/api/factory.mjs
  var import_hooks2 = __toESM(require_hooks(), 1);
  var import_deprecated6 = __toESM(require_deprecated(), 1);
  var import_warning3 = __toESM(require_warning(), 1);
  var isBlockTypeTransform = (transform) => transform?.type === "block";
  var getBlockTypeWithTransformMetadata = (blockType, transform) => transform.variationName ? { ...blockType, variationName: transform.variationName } : blockType;
  function createBlock(name, attributes = {}, innerBlocks = [], innerContent) {
    if (!isBlockRegistered(name)) {
      return createBlock("core/missing", {
        originalName: name,
        originalContent: "",
        originalUndelimitedContent: ""
      });
    }
    const sanitizedAttributes = sanitizeBlockAttributes(name, attributes);
    const clientId = v4_default();
    const block = {
      clientId,
      name,
      isValid: true,
      attributes: sanitizedAttributes,
      innerBlocks
    };
    if (innerContent) {
      if (name === "core/html") {
        block.innerContent = innerContent;
      } else {
        (0, import_warning3.default)(
          `The innerContent argument passed to createBlock for the "${name}" block was ignored. Only the Custom HTML block stores static inner content.`
        );
      }
    }
    return block;
  }
  function createBlocksFromInnerBlocksTemplate(innerBlocksOrTemplate = []) {
    return innerBlocksOrTemplate.map((innerBlock) => {
      const innerBlockTemplate = Array.isArray(innerBlock) ? innerBlock : [
        innerBlock.name,
        innerBlock.attributes,
        innerBlock.innerBlocks,
        innerBlock.innerContent
      ];
      const [name, attributes, innerBlocks = [], innerContent] = innerBlockTemplate;
      return createBlock(
        name,
        attributes,
        createBlocksFromInnerBlocksTemplate(
          innerBlocks
        ),
        innerContent
      );
    });
  }
  function cloneSanitizedBlock(block, mergeAttributes = {}, newInnerBlocks) {
    const { name } = block;
    if (!isBlockRegistered(name)) {
      return createBlock("core/missing", {
        originalName: name,
        originalContent: "",
        originalUndelimitedContent: ""
      });
    }
    const clientId = v4_default();
    const sanitizedAttributes = sanitizeBlockAttributes(name, {
      ...block.attributes,
      ...mergeAttributes
    });
    return {
      ...block,
      clientId,
      attributes: sanitizedAttributes,
      innerBlocks: newInnerBlocks || block.innerBlocks.map(
        (innerBlock) => cloneSanitizedBlock(innerBlock)
      )
    };
  }
  function __experimentalCloneSanitizedBlock(block, mergeAttributes = {}, newInnerBlocks) {
    (0, import_deprecated6.default)("__experimentalCloneSanitizedBlock", {
      since: "7.1",
      alternative: "cloneSanitizedBlock"
    });
    return cloneSanitizedBlock(block, mergeAttributes, newInnerBlocks);
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
    const blockTypesWithPossibleFromTransforms = allBlockTypes.flatMap(
      (blockType) => {
        const fromTransforms = getBlockTransforms("from", blockType.name);
        return fromTransforms.filter(isBlockTypeTransform).filter(
          (transform) => isPossibleTransformForSource(transform, "from", blocks)
        ).map(
          (transform) => getBlockTypeWithTransformMetadata(blockType, transform)
        );
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
    const possibleTransforms = transformsTo.filter(isBlockTypeTransform).filter(
      (transform) => isPossibleTransformForSource(transform, "to", blocks)
    );
    return possibleTransforms.flatMap((transformation) => {
      return (transformation.blocks ?? []).map((name) => {
        const transformedBlockType = getBlockType(name);
        return transformedBlockType ? getBlockTypeWithTransformMetadata(
          transformedBlockType,
          transformation
        ) : void 0;
      });
    }).filter((bt2) => !!bt2);
  };
  var isWildcardBlockTransform = (t2) => isBlockTypeTransform(t2) && Array.isArray(t2.blocks) && t2.blocks.includes("*");
  var isContainerGroupBlock = (name) => name === getGroupingBlockName();
  function getPossibleBlockTransformations(blocks) {
    if (!blocks.length) {
      return [];
    }
    const blockTypesForFromTransforms = getBlockTypesForPossibleFromTransforms(blocks);
    const blockTypesForToTransforms = getBlockTypesForPossibleToTransforms(blocks);
    const blockTypesByNameAndVariation = /* @__PURE__ */ new Map();
    for (const blockType of [
      ...blockTypesForFromTransforms,
      ...blockTypesForToTransforms
    ]) {
      const key = blockType.variationName ? `${blockType.name}/${blockType.variationName}` : blockType.name;
      if (!blockTypesByNameAndVariation.has(key)) {
        blockTypesByNameAndVariation.set(key, blockType);
      }
    }
    return [...blockTypesByNameAndVariation.values()];
  }
  function findTransform(transforms, predicate) {
    const hooks = (0, import_hooks2.createHooks)();
    for (let i = 0; i < transforms.length; i++) {
      const candidate = transforms[i];
      if (predicate(candidate)) {
        hooks.addFilter(
          "transform",
          "transform/" + i.toString(),
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
    const directionTransforms = blockType?.transforms?.[direction];
    if (!blockType || !Array.isArray(directionTransforms)) {
      return [];
    }
    return directionTransforms.map((transform) => ({
      ...transform,
      blockName: blockType.name
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
  function switchToBlockType(blocks, name, variationName) {
    const blocksArray = Array.isArray(blocks) ? blocks : [blocks];
    const isMultiBlock = blocksArray.length > 1;
    const firstBlock = blocksArray[0];
    const sourceName = firstBlock.name;
    const transformationsFrom = getBlockTransforms("from", name).filter(
      isBlockTypeTransform
    );
    const transformationsTo = getBlockTransforms("to", sourceName).filter(
      isBlockTypeTransform
    );
    const isMatchingVariation = (t2) => variationName ? t2.variationName === variationName : !t2.variationName;
    const transformation = findTransform(
      transformationsTo,
      (t2) => isMatchingVariation(t2) && (isWildcardBlockTransform(t2) || t2.blocks.indexOf(name) !== -1) && (!isMultiBlock || !!t2.isMultiBlock) && maybeCheckTransformIsMatch(t2, blocksArray)
    ) || findTransform(
      transformationsFrom,
      (t2) => isMatchingVariation(t2) && (isWildcardBlockTransform(t2) || t2.blocks.indexOf(sourceName) !== -1) && (!isMultiBlock || !!t2.isMultiBlock) && maybeCheckTransformIsMatch(t2, blocksArray)
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
    ),
    example.innerContent
  );

  // packages/blocks/build-module/api/parser/index.mjs
  var import_block_serialization_default_parser = __toESM(require_block_serialization_default_parser(), 1);
  var import_autop2 = __toESM(require_autop(), 1);

  // packages/blocks/build-module/api/serializer.mjs
  var import_element2 = __toESM(require_element(), 1);
  var import_hooks3 = __toESM(require_hooks(), 1);
  var import_is_shallow_equal = __toESM(require_is_shallow_equal(), 1);
  var import_autop = __toESM(require_autop(), 1);
  var import_deprecated7 = __toESM(require_deprecated(), 1);

  // packages/blocks/build-module/api/parser/serialize-raw-block.mjs
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
    return isCommentDelimited ? getCommentDelimitedContent(blockName ?? void 0, attrs, content) : content;
  }

  // packages/blocks/build-module/api/serializer.mjs
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
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
    let save = blockType.save;
    if (save.prototype instanceof import_element2.Component) {
      const SaveClass = save;
      const instance = new SaveClass({ attributes });
      save = instance.render.bind(instance);
    }
    blockPropsProvider.blockType = blockType;
    blockPropsProvider.attributes = attributes;
    innerBlocksPropsProvider.innerBlocks = innerBlocks;
    let element = save({
      attributes,
      innerBlocks
    });
    if (element !== null && typeof element === "object" && (0, import_hooks3.hasFilter)("blocks.getSaveContent.extraProps") && !((blockType.apiVersion ?? 0) > 1)) {
      const props = (0, import_hooks3.applyFilters)(
        "blocks.getSaveContent.extraProps",
        { ...element.props },
        blockType,
        attributes
      );
      if (!(0, import_is_shallow_equal.isShallowEqual)(props, element.props)) {
        element = (0, import_element2.cloneElement)(
          element,
          props
        );
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
    const blockType = normalizeBlockType(
      blockTypeOrName
    );
    if (!blockType) {
      return "";
    }
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
          (0, import_deprecated7.default)("__experimentalRole attribute", {
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
  function serializeInnerContent(innerContent, innerBlocks) {
    let childIndex = 0;
    const parts = innerContent.map((item) => {
      if (item !== null) {
        return item;
      }
      const innerBlock = innerBlocks[childIndex++];
      return innerBlock ? serializeBlock(innerBlock, { isInnerBlocks: true }) : "";
    });
    for (; childIndex < innerBlocks.length; childIndex++) {
      parts.push(
        serializeBlock(innerBlocks[childIndex], { isInnerBlocks: true })
      );
    }
    return parts.join("").trim();
  }
  function getBlockInnerHTML(block) {
    if (block.innerContent && block.name === "core/html") {
      return serializeInnerContent(block.innerContent, block.innerBlocks);
    }
    let saveContent = block.originalContent ?? "";
    if (block.isValid || block.innerBlocks.length) {
      try {
        saveContent = getSaveContent(
          block.name,
          block.attributes,
          block.innerBlocks
        );
      } catch {
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
  var __unstableSerializeAndClean = /* @__PURE__ */ (() => {
    const cache = /* @__PURE__ */ new WeakMap();
    return (blocks) => {
      const cached = cache.get(blocks);
      if (cached !== void 0) {
        return cached;
      }
      let effectiveBlocks = blocks;
      if (effectiveBlocks.length === 1 && isUnmodifiedDefaultBlock(effectiveBlocks[0])) {
        effectiveBlocks = [];
      }
      let content = serialize(effectiveBlocks);
      if (effectiveBlocks.length === 1 && effectiveBlocks[0].name === getFreeformContentHandlerName() && effectiveBlocks[0].name === "core/freeform") {
        content = (0, import_autop.removep)(content);
      }
      cache.set(blocks, content);
      return content;
    };
  })();
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
        for (var i = 0; i < arguments.length; i++) {
          if (token.type === arguments[i]) {
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

  // packages/blocks/build-module/api/validation/index.mjs
  var import_es62 = __toESM(require_es6(), 1);
  var import_deprecated8 = __toESM(require_deprecated(), 1);
  var import_html_entities = __toESM(require_html_entities(), 1);

  // packages/blocks/build-module/api/validation/logger.mjs
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

  // packages/blocks/build-module/api/validation/index.mjs
  var identity = (x3) => x3;
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
  var TEXT_NORMALIZATIONS = [
    identity,
    getTextWithCollapsedWhitespace
  ];
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
     * @param entity Entity fragment discovered in HTML.
     *
     * @return Entity substitute value.
     */
    parse(entity) {
      if (isValidCharacterReference(entity)) {
        return (0, import_html_entities.decodeEntities)("&" + entity + ";");
      }
      return void 0;
    }
  };
  function getTextPiecesSplitOnWhitespace(text2) {
    return text2.trim().split(REGEXP_WHITESPACE);
  }
  function getTextWithCollapsedWhitespace(text2) {
    return getTextPiecesSplitOnWhitespace(text2).join(" ");
  }
  function getMeaningfulAttributePairs(token) {
    return (token.attributes ?? []).filter((pair) => {
      const [key, value] = pair;
      return value || key.indexOf("data-") === 0 || MEANINGFUL_ATTRIBUTES.includes(key);
    });
  }
  function isEquivalentTextTokens(actual, expected, logger = createLogger()) {
    let actualChars = actual.chars;
    let expectedChars = expected.chars;
    for (let i = 0; i < TEXT_NORMALIZATIONS.length; i++) {
      const normalize = TEXT_NORMALIZATIONS[i];
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
      return (0, import_es62.default)(
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
    for (let i = 0; i < expected.length; i++) {
      expectedAttributes[expected[i][0].toLowerCase()] = expected[i][1];
    }
    for (let i = 0; i < actual.length; i++) {
      const [name, actualValue] = actual[i];
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
    return void 0;
  }
  function getHTMLTokens(html2, logger = createLogger()) {
    try {
      return new Tokenizer(new DecodeEntityParser()).tokenize(
        html2
      );
    } catch {
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
    (0, import_deprecated8.default)("isValidBlockContent introduces opportunity for data loss", {
      since: "12.6",
      plugin: "Gutenberg",
      alternative: "validateBlock"
    });
    const blockType = normalizeBlockType(blockTypeOrName);
    const block = {
      clientId: "",
      name: blockType.name,
      isValid: true,
      attributes,
      innerBlocks: [],
      originalContent: originalBlockContent
    };
    const [isValid] = validateBlock(block, blockType);
    return isValid;
  }

  // packages/blocks/build-module/api/parser/convert-legacy-block.mjs
  function normalizeStyleStateAliases(style) {
    if (true) {
      return style;
    }
    if (!style || typeof style !== "object" || Array.isArray(style)) {
      return style;
    }
    let normalizedStyle = style;
    const viewportAliases = {
      "@mobile": "mobile",
      "@tablet": "tablet"
    };
    Object.entries(viewportAliases).forEach(([state, legacyState]) => {
      if (!Object.hasOwn(normalizedStyle, legacyState)) {
        return;
      }
      if (normalizedStyle === style) {
        normalizedStyle = { ...normalizedStyle };
      }
      if (!Object.hasOwn(normalizedStyle, state)) {
        normalizedStyle[state] = normalizedStyle[legacyState];
      }
      delete normalizedStyle[legacyState];
    });
    return normalizedStyle;
  }
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
      const deprecated13 = {
        speaker: "speaker-deck",
        polldaddy: "crowdsignal"
      };
      newAttributes.providerNameSlug = providerSlug in deprecated13 ? deprecated13[providerSlug] : providerSlug;
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
      const className = newAttributes.className ?? "";
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
    const layout = attributes.layout;
    if (layout?.type === "grid" && typeof layout?.columnCount === "string") {
      newAttributes.layout = {
        ...newAttributes.layout,
        columnCount: parseInt(layout.columnCount, 10)
      };
    }
    const style = attributes.style;
    const styleLayout = style?.layout;
    if (typeof styleLayout?.columnSpan === "string") {
      const columnSpanNumber = parseInt(styleLayout.columnSpan, 10);
      newAttributes.style = {
        ...newAttributes.style,
        layout: {
          ...newAttributes.style?.layout,
          columnSpan: isNaN(columnSpanNumber) ? void 0 : columnSpanNumber
        }
      };
    }
    if (typeof styleLayout?.rowSpan === "string") {
      const rowSpanNumber = parseInt(styleLayout.rowSpan, 10);
      newAttributes.style = {
        ...newAttributes.style,
        layout: {
          ...newAttributes.style?.layout,
          rowSpan: isNaN(rowSpanNumber) ? void 0 : rowSpanNumber
        }
      };
    }
    const normalizedStyle = normalizeStyleStateAliases(newAttributes.style);
    if (normalizedStyle !== newAttributes.style) {
      newAttributes.style = normalizedStyle;
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
    if (typeof matchers === "function") {
      return matchers(source);
    }
    if (Object !== matchers.constructor) {
      return;
    }
    return Object.keys(matchers).reduce(function(memo, key) {
      var inner = matchers[key];
      memo[key] = parse(source, inner);
      return memo;
    }, {});
  }
  function prop(arg1, arg2) {
    var name;
    var selector;
    if (1 === arguments.length) {
      name = arg1;
      selector = void 0;
    } else {
      name = arg2;
      selector = arg1;
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
  function attr(arg1, arg2) {
    var name;
    var selector;
    if (1 === arguments.length) {
      name = arg1;
      selector = void 0;
    } else {
      name = arg2;
      selector = arg1;
    }
    return function(node) {
      var attributes = prop(selector, "attributes")(node);
      if (attributes && Object.prototype.hasOwnProperty.call(attributes, name)) {
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
      var node = head, len = arguments.length, args, i;
      searchCache: while (node) {
        if (node.args.length !== arguments.length) {
          node = node.next;
          continue;
        }
        for (i = 0; i < len; i++) {
          if (node.args[i] !== arguments[i]) {
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
      for (i = 0; i < len; i++) {
        args[i] = arguments[i];
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

  // packages/blocks/build-module/api/parser/get-block-attributes.mjs
  var import_hooks4 = __toESM(require_hooks(), 1);
  var import_rich_text4 = __toESM(require_rich_text(), 1);

  // packages/blocks/build-module/api/matchers.mjs
  var import_rich_text3 = __toESM(require_rich_text(), 1);

  // packages/blocks/build-module/api/node.mjs
  var import_deprecated10 = __toESM(require_deprecated(), 1);

  // packages/blocks/build-module/api/children.mjs
  var import_element3 = __toESM(require_element(), 1);
  var import_deprecated9 = __toESM(require_deprecated(), 1);
  function getSerializeCapableElement(children) {
    return children;
  }
  function getChildrenArray(children) {
    (0, import_deprecated9.default)("wp.blocks.children.getChildrenArray", {
      since: "6.1",
      version: "6.3",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    return children;
  }
  function concat(...blockNodes) {
    (0, import_deprecated9.default)("wp.blocks.children.concat", {
      since: "6.1",
      version: "6.3",
      alternative: "wp.richText.concat",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    const result = [];
    for (let i = 0; i < blockNodes.length; i++) {
      const blockNode = Array.isArray(blockNodes[i]) ? blockNodes[i] : [blockNodes[i]];
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
    (0, import_deprecated9.default)("wp.blocks.children.fromDOM", {
      since: "6.1",
      version: "6.3",
      alternative: "wp.richText.create",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    const result = [];
    for (let i = 0; i < domNodes.length; i++) {
      try {
        result.push(fromDOM2(domNodes[i]));
      } catch {
      }
    }
    return result;
  }
  function toHTML(children) {
    (0, import_deprecated9.default)("wp.blocks.children.toHTML", {
      since: "6.1",
      version: "6.3",
      alternative: "wp.richText.toHTMLString",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    const element = getSerializeCapableElement(children);
    return (0, import_element3.renderToString)(element);
  }
  function matcher(selector) {
    (0, import_deprecated9.default)("wp.blocks.children.matcher", {
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

  // packages/blocks/build-module/api/node.mjs
  function isNodeOfType(node, type) {
    (0, import_deprecated10.default)("wp.blocks.node.isNodeOfType", {
      since: "6.1",
      version: "6.3",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    return typeof node !== "string" && node?.type === type;
  }
  function getNamedNodeMapAsObject(nodeMap) {
    const result = {};
    for (let i = 0; i < nodeMap.length; i++) {
      const { name, value } = nodeMap[i];
      result[name] = value;
    }
    return result;
  }
  function fromDOM2(domNode) {
    (0, import_deprecated10.default)("wp.blocks.node.fromDOM", {
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
    (0, import_deprecated10.default)("wp.blocks.node.toHTML", {
      since: "6.1",
      version: "6.3",
      alternative: "wp.richText.toHTMLString",
      link: "https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/introducing-attributes-and-editable-fields/"
    });
    return toHTML([node]);
  }
  function matcher2(selector) {
    (0, import_deprecated10.default)("wp.blocks.node.matcher", {
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
      } catch {
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

  // packages/blocks/build-module/api/matchers.mjs
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
    return target ? import_rich_text3.RichTextData.fromHTMLElement(target, {
      preserveWhiteSpace
    }) : import_rich_text3.RichTextData.empty();
  };

  // packages/blocks/build-module/api/parser/get-block-attributes.mjs
  var toBooleanAttributeMatcher = (matcher3) => (domNode) => matcher3(domNode) !== void 0;
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
  var matcherFromSource = memize(
    (sourceConfig) => {
      switch (sourceConfig.source) {
        case "attribute": {
          const matcher3 = attr(
            sourceConfig.selector,
            sourceConfig.attribute
          );
          if (sourceConfig.type === "boolean") {
            return toBooleanAttributeMatcher(matcher3);
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
          console.error(
            `Unknown source type "${sourceConfig.source}"`
          );
          return void 0;
      }
    }
  );
  function parseHtml(innerHTML) {
    return parse(
      innerHTML ?? "",
      (h) => h
    );
  }
  function parseWithAttributeSchema(innerHTML, attributeSchema) {
    return matcherFromSource(attributeSchema)(
      parseHtml(innerHTML)
    );
  }
  function getBlockAttributes(blockTypeOrName, innerHTML, attributes = {}) {
    const doc = parseHtml(innerHTML);
    const blockType = normalizeBlockType(blockTypeOrName);
    const blockAttributes = Object.fromEntries(
      Object.entries(blockType?.attributes ?? {}).map(
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

  // packages/blocks/build-module/api/parser/fix-custom-classname.mjs
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

  // packages/blocks/build-module/api/parser/fix-global-attribute.mjs
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

  // packages/blocks/build-module/api/parser/apply-built-in-validation-fixes.mjs
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
      originalContent ?? ""
    );
    updatedBlockAttributes = fixGlobalAttribute(
      updatedBlockAttributes,
      blockType,
      originalContent ?? "",
      "ariaLabel",
      "data-aria-label",
      ARIA_LABEL_ATTR_SCHEMA
    );
    updatedBlockAttributes = fixGlobalAttribute(
      updatedBlockAttributes,
      blockType,
      originalContent ?? "",
      "anchor",
      "data-anchor",
      ANCHOR_ATTR_SCHEMA
    );
    return {
      ...block,
      attributes: updatedBlockAttributes
    };
  }

  // packages/blocks/build-module/api/parser/apply-block-deprecated-versions.mjs
  function stubFalse() {
    return false;
  }
  function applyBlockDeprecatedVersions(block, rawBlock, blockType) {
    const parsedAttributes = rawBlock.attrs ?? {};
    const { deprecated: deprecatedDefinitions } = blockType;
    if (!deprecatedDefinitions || !deprecatedDefinitions.length) {
      return block;
    }
    for (let i = 0; i < deprecatedDefinitions.length; i++) {
      const { isEligible = stubFalse } = deprecatedDefinitions[i];
      if (block.isValid && !isEligible(parsedAttributes, block.innerBlocks, {
        blockNode: rawBlock,
        block
      })) {
        continue;
      }
      const deprecatedBlockType = Object.assign(
        omit(
          blockType,
          DEPRECATED_ENTRY_KEYS
        ),
        deprecatedDefinitions[i]
      );
      let migratedBlock = {
        ...block,
        attributes: getBlockAttributes(
          deprecatedBlockType,
          block.originalContent ?? "",
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

  // packages/blocks/build-module/api/parser/index.mjs
  function convertLegacyBlocks(rawBlock) {
    const [correctName, correctedAttributes] = convertLegacyBlockNameAndAttributes(
      rawBlock.blockName,
      rawBlock.attrs ?? {}
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
    if (blockType.name === "core/html") {
      parsedBlock.innerContent = normalizedBlock.innerContent ?? [
        normalizedBlock.innerHTML
      ];
      parsedBlock.isValid = true;
      parsedBlock.validationIssues = [];
      return parsedBlock;
    }
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
    return (0, import_block_serialization_default_parser.parse)(content).reduce(
      (accumulator, rawBlock) => {
        const block = parseRawBlock(
          rawBlock,
          options
        );
        if (block) {
          accumulator.push(block);
        }
        return accumulator;
      },
      []
    );
  }

  // packages/blocks/build-module/api/raw-handling/index.mjs
  var import_deprecated11 = __toESM(require_deprecated(), 1);
  var import_dom13 = __toESM(require_dom(), 1);

  // packages/blocks/build-module/api/raw-handling/get-raw-transforms.mjs
  function getRawTransforms() {
    return getBlockTransforms("from").filter(
      (transform) => transform.type === "raw"
    ).map((transform) => ({
      ...transform,
      isMatch: transform.isMatch ?? ((node) => !!transform.selector && node.matches(transform.selector))
    }));
  }

  // packages/blocks/build-module/api/raw-handling/html-to-blocks.mjs
  function htmlToBlocks(html2, handler) {
    const doc = document.implementation.createHTMLDocument("");
    doc.body.innerHTML = html2;
    return Array.from(doc.body.children).flatMap((node) => {
      const transforms = getRawTransforms();
      const rawTransform = findTransform(
        transforms,
        (transform2) => transform2.isMatch(node)
      );
      if (!rawTransform) {
        return createBlock(
          // Should not be hardcoded.
          "core/html",
          {},
          [],
          [node.outerHTML]
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

  // packages/blocks/build-module/api/raw-handling/normalise-blocks.mjs
  var import_dom2 = __toESM(require_dom(), 1);
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

  // packages/blocks/build-module/api/raw-handling/special-comment-converter.mjs
  var import_dom3 = __toESM(require_dom(), 1);
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

  // packages/blocks/build-module/api/raw-handling/list-reducer.mjs
  var import_dom4 = __toESM(require_dom(), 1);
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
    const prevElement = list.previousElementSibling;
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
      const prevListItem = list.previousElementSibling;
      if (prevListItem) {
        prevListItem.appendChild(node);
      } else {
        (0, import_dom4.unwrap)(node);
      }
    }
  }

  // packages/blocks/build-module/api/raw-handling/blockquote-normaliser.mjs
  function blockquoteNormaliser(options = {}) {
    return (bq) => {
      if (bq.nodeName !== "BLOCKQUOTE") {
        return;
      }
      const node = bq;
      node.innerHTML = normaliseBlocks(node.innerHTML, options);
    };
  }

  // packages/blocks/build-module/api/raw-handling/figure-content-reducer.mjs
  var import_dom5 = __toESM(require_dom(), 1);
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
    if (!schema || !isFigureContent(node, schema)) {
      return;
    }
    let nodeToInsert = node;
    const parentNode = node.parentNode;
    if (canHaveAnchor(node, schema) && parentNode.nodeName === "A" && parentNode.childNodes.length === 1) {
      nodeToInsert = node.parentNode;
    }
    const wrapper = nodeToInsert.closest("p,div");
    if (wrapper) {
      const element = node;
      if (!element.classList) {
        wrapFigureContent(nodeToInsert, wrapper);
      } else if (element.classList.contains("alignright") || element.classList.contains("alignleft") || element.classList.contains("aligncenter") || !wrapper.textContent.trim()) {
        wrapFigureContent(nodeToInsert, wrapper);
      }
    } else {
      wrapFigureContent(nodeToInsert);
    }
  }

  // packages/blocks/build-module/api/raw-handling/shortcode-converter.mjs
  var import_shortcode = __toESM(require_shortcode(), 1);
  var castArray = (maybeArray) => Array.isArray(maybeArray) ? maybeArray : [maybeArray];
  var beforeLineRegexp = /(\n|<p>|<br\s*\/?>)\s*$/;
  var afterLineRegexp = /^\s*(\n|<\/p>|<br\s*\/?>)/;
  function segmentHTMLToShortcodeBlock(HTML, lastIndex = 0, excludedBlockNames = []) {
    const transformsFrom = getBlockTransforms("from").filter(
      (transform) => transform.type === "shortcode"
    );
    const transformation = findTransform(
      transformsFrom,
      (transform) => excludedBlockNames.indexOf(transform.blockName) === -1 && castArray(transform.tag).some(
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
    const previousIndex = lastIndex;
    const match = (0, import_shortcode.next)(transformTag, HTML, lastIndex);
    if (!match) {
      return [HTML];
    }
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
      const blockType = getBlockType(transformation.blockName);
      if (!blockType) {
        return [HTML];
      }
      const transformAttributes = transformation.attributes ?? blockType.attributes;
      const attributes = Object.fromEntries(
        Object.entries(transformAttributes).filter(([, schema]) => schema.shortcode).map(([key, schema]) => [
          key,
          schema.shortcode(match.shortcode.attrs, match)
        ])
      );
      const transformationBlockType = {
        ...blockType,
        attributes: transformAttributes
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
      block = applyBuiltInValidationFixes(block, transformationBlockType);
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
  var shortcode_converter_default = segmentHTMLToShortcodeBlock;

  // packages/blocks/build-module/api/raw-handling/utils.mjs
  var import_dom6 = __toESM(require_dom(), 1);
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
          return mergeSchemas(
            { ...objValue || {} },
            srcValue || {}
          );
        }
        case "attributes":
        case "require": {
          return Array.from(
            /* @__PURE__ */ new Set([...objValue || [], ...srcValue || []])
          );
        }
        case "isMatch": {
          if (!objValue || !srcValue) {
            return void 0;
          }
          return (...args) => {
            return objValue(...args) || srcValue(...args);
          };
        }
        case "classes": {
          if ((objValue || []).includes("*") || (srcValue || []).includes("*")) {
            return ["*"];
          }
          return [...objValue || [], ...srcValue || []];
        }
      }
    }
    function mergeTagNameSchemas(a, b2) {
      if (a === b2) {
        return a;
      }
      for (const key in b2) {
        if (a[key]) {
          a[key] = mergeTagNameSchemaProperties(
            a[key],
            b2[key],
            key
          );
        } else if (Array.isArray(b2[key])) {
          a[key] = b2[key].slice();
        } else {
          a[key] = { ...b2[key] };
        }
      }
      return a;
    }
    function mergeSchemas(a, b2) {
      if (a === b2) {
        return a;
      }
      for (const key in b2) {
        if (a[key]) {
          a[key] = mergeTagNameSchemas(a[key], b2[key]);
        } else if (Array.isArray(b2[key])) {
          a[key] = b2[key].slice();
        } else {
          a[key] = { ...b2[key] };
        }
      }
      return a;
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
    for (let i = 0; i < descendants.length; i++) {
      if (descendants.item(i).tagName !== "BR") {
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

  // packages/blocks/build-module/api/raw-handling/paste-handler.mjs
  var import_dom12 = __toESM(require_dom(), 1);

  // packages/blocks/build-module/api/raw-handling/comment-remover.mjs
  var import_dom7 = __toESM(require_dom(), 1);
  function commentRemover(node) {
    if (node.nodeType === node.COMMENT_NODE) {
      (0, import_dom7.remove)(node);
    }
  }

  // packages/blocks/build-module/api/raw-handling/is-inline-content.mjs
  var import_dom8 = __toESM(require_dom(), 1);
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
      (tagGroup) => [tag, contextTag].filter((t2) => !tagGroup.includes(t2)).length === 0
    );
  }
  function deepCheck(nodes, contextTag) {
    return nodes.every(
      (node) => isInline(node, contextTag) && deepCheck(Array.from(node.children), contextTag)
    );
  }
  function isDoubleBR(node) {
    return node.nodeName === "BR" && !!node.previousSibling && node.previousSibling.nodeName === "BR";
  }
  function isInlineContent(HTML, contextTag) {
    const doc = document.implementation.createHTMLDocument("");
    doc.body.innerHTML = HTML;
    const nodes = Array.from(doc.body.children);
    return !nodes.some(isDoubleBR) && deepCheck(nodes, contextTag);
  }

  // packages/blocks/build-module/api/raw-handling/phrasing-content-reducer.mjs
  var import_dom9 = __toESM(require_dom(), 1);
  function phrasingContentReducer(node, doc) {
    if (node.nodeName === "SPAN" && node.style) {
      const {
        fontWeight,
        fontStyle,
        textDecorationLine,
        textDecoration,
        verticalAlign
      } = node.style;
      const element = node;
      if (fontWeight === "bold" || fontWeight === "700") {
        (0, import_dom9.wrap)(doc.createElement("strong"), element);
      }
      if (fontStyle === "italic") {
        (0, import_dom9.wrap)(doc.createElement("em"), element);
      }
      if (textDecorationLine === "line-through" || textDecoration.includes("line-through")) {
        (0, import_dom9.wrap)(doc.createElement("s"), element);
      }
      if (verticalAlign === "super") {
        (0, import_dom9.wrap)(doc.createElement("sup"), element);
      } else if (verticalAlign === "sub") {
        (0, import_dom9.wrap)(doc.createElement("sub"), element);
      }
    } else if (node.nodeName === "B") {
      (0, import_dom9.replaceTag)(node, "strong");
    } else if (node.nodeName === "I") {
      (0, import_dom9.replaceTag)(node, "em");
    } else if (node.nodeName === "A") {
      const anchor = node;
      if (anchor.target && anchor.target.toLowerCase() === "_blank") {
        anchor.rel = "noopener";
      } else {
        anchor.removeAttribute("target");
        anchor.removeAttribute("rel");
      }
      if (anchor.name && !anchor.id) {
        anchor.id = anchor.name;
      }
      if (anchor.id && !anchor.ownerDocument.querySelector(`[href="#${anchor.id}"]`)) {
        anchor.removeAttribute("id");
      }
    }
  }

  // packages/blocks/build-module/api/raw-handling/head-remover.mjs
  function headRemover(node) {
    if (node.nodeName !== "SCRIPT" && node.nodeName !== "NOSCRIPT" && node.nodeName !== "TEMPLATE" && node.nodeName !== "STYLE") {
      return;
    }
    node.parentNode.removeChild(node);
  }

  // packages/blocks/build-module/api/raw-handling/ms-list-ignore.mjs
  function msListIgnore(node) {
    if (node.nodeType !== node.ELEMENT_NODE) {
      return;
    }
    const el = node;
    const style = el.getAttribute("style");
    if (!style || !style.includes("mso-list")) {
      return;
    }
    const rules = style.split(";").reduce(
      (acc, rule) => {
        const [key, value] = rule.split(":");
        if (key && value) {
          acc[key.trim().toLowerCase()] = value.trim().toLowerCase();
        }
        return acc;
      },
      {}
    );
    if (rules["mso-list"] === "ignore") {
      el.remove();
    }
  }

  // packages/blocks/build-module/api/raw-handling/ms-list-converter.mjs
  function isList2(node) {
    return node.nodeName === "OL" || node.nodeName === "UL";
  }
  function msListConverter(node, doc) {
    if (node.nodeName !== "P") {
      return;
    }
    const element = node;
    const style = element.getAttribute("style");
    if (!style || !style.includes("mso-list")) {
      return;
    }
    const prevNode = element.previousElementSibling;
    if (!prevNode || !isList2(prevNode)) {
      const type = element.textContent.trim().slice(0, 1);
      const isNumeric = /[1iIaA]/.test(type);
      const newListNode = doc.createElement(isNumeric ? "ol" : "ul");
      if (isNumeric) {
        newListNode.setAttribute("type", type);
      }
      element.parentNode.insertBefore(newListNode, element);
    }
    const listNode = element.previousElementSibling;
    const listType = listNode.nodeName;
    const listItem = doc.createElement("li");
    let receivingNode = listNode;
    listItem.innerHTML = deepFilterHTML(element.innerHTML, [msListIgnore]);
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
    element.parentNode.removeChild(element);
  }

  // packages/blocks/build-module/api/raw-handling/image-corrector.mjs
  var import_blob = __toESM(require_blob(), 1);
  function imageCorrector(img) {
    if (img.nodeName !== "IMG") {
      return;
    }
    const node = img;
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
      } catch {
        node.src = "";
        return;
      }
      const uint8Array = new Uint8Array(decoded.length);
      for (let i = 0; i < uint8Array.length; i++) {
        uint8Array[i] = decoded.charCodeAt(i);
      }
      const name = type.replace("/", ".");
      const file = new window.File([uint8Array], name, { type });
      node.src = (0, import_blob.createBlobURL)(file);
    }
    if (node.height === 1 || node.width === 1) {
      node.parentNode.removeChild(node);
    }
  }

  // packages/blocks/build-module/api/raw-handling/div-normaliser.mjs
  function divNormaliser(div) {
    if (div.nodeName !== "DIV") {
      return;
    }
    const node = div;
    node.innerHTML = normaliseBlocks(node.innerHTML);
  }

  // node_modules/marked/lib/marked.esm.js
  function A2() {
    return { async: false, breaks: false, extensions: null, gfm: true, hooks: null, pedantic: false, renderer: null, silent: false, tokenizer: null, walkTokens: null };
  }
  var T = A2();
  function U(l4) {
    T = l4;
  }
  var E2 = { exec: () => null };
  function I(l4) {
    let e2 = [];
    return (t2) => {
      let n = Math.max(0, Math.min(3, t2 - 1)), i = e2[n];
      return i || (i = l4(n), e2[n] = i), i;
    };
  }
  function d(l4, e2 = "") {
    let t2 = typeof l4 == "string" ? l4 : l4.source, n = { replace: (i, r2) => {
      let o2 = typeof r2 == "string" ? r2 : r2.source;
      return o2 = o2.replace(m2.caret, "$1"), t2 = t2.replace(i, o2), n;
    }, getRegex: () => new RegExp(t2, e2) };
    return n;
  }
  var we = ((l4 = "") => {
    try {
      return !!new RegExp("(?<=1)(?<!1)" + l4);
    } catch {
      return false;
    }
  })();
  var m2 = { codeRemoveIndent: /^(?: {0,3}\t| {1,4})/gm, outputLinkReplace: /\\([\[\]])/g, indentCodeCompensation: /^(\s+)(?:```)/, beginningSpace: /^\s+/, endingHash: /#$/, startingSpaceChar: /^ /, endingSpaceChar: / $/, endingSpaceTabChar: /[ \t]$/, nonSpaceChar: /[^ ]/, newLineCharGlobal: /\n/g, tabCharGlobal: /\t/g, multipleSpaceGlobal: /\s+/g, blankLine: /^[ \t]*$/, doubleBlankLine: /\n[ \t]*\n[ \t]*$/, blockquoteStart: /^ {0,3}>/, blockquoteSetextReplace: /\n {0,3}((?:=+|-+) *)(?=\n|$)/g, blockquoteSetextReplace2: /^ {0,3}>[ \t]?/gm, listReplaceNesting: /^ {1,4}(?=( {4})*[^ ])/g, listIsTask: /^\[[ xX]\] +\S/, listReplaceTask: /^\[[ xX]\] +/, listTaskCheckbox: /\[[ xX]\]/, anyLine: /\n.*\n/, hrefBrackets: /^<(.*)>$/, tableDelimiter: /[:|]/, tableAlignChars: /^\||\| *$/g, tableRowBlankLine: /\n[ \t]*$/, tableAlignRight: /^ *-+: *$/, tableAlignCenter: /^ *:-+: *$/, tableAlignLeft: /^ *:-+ *$/, startATag: /^<a /i, endATag: /^<\/a>/i, startPreScriptTag: /^<(pre|code|kbd|script)(\s|>)/i, endPreScriptTag: /^<\/(pre|code|kbd|script)(\s|>)/i, startAngleBracket: /^</, endAngleBracket: />$/, pedanticHrefTitle: /^([^'"]*[^\s])\s+(['"])(.*)\2/, unicodeAlphaNumeric: /[\p{L}\p{N}]/u, escapeTest: /[&<>"']/, escapeReplace: /[&<>"']/g, escapeTestNoEncode: /[<>"']|&(?!(#\d{1,7}|#[Xx][a-fA-F0-9]{1,6}|\w+);)/, escapeReplaceNoEncode: /[<>"']|&(?!(#\d{1,7}|#[Xx][a-fA-F0-9]{1,6}|\w+);)/g, caret: /(^|[^\[])\^/g, percentDecode: /%25/g, findPipe: /\|/g, splitPipe: / \|/, slashPipe: /\\\|/g, carriageReturn: /\r\n|\r/g, spaceLine: /^ +$/gm, notSpaceStart: /^\S*/, endingNewline: /\n$/, listItemRegex: (l4) => new RegExp(`^( {0,3}${l4})((?:[	 ][^\\n]*)?(?:\\n|$))`), nextBulletRegex: I((l4) => new RegExp(`^ {0,${l4}}(?:[*+-]|\\d{1,9}[.)])((?:[ 	][^\\n]*)?(?:\\n|$))`)), hrRegex: I((l4) => new RegExp(`^ {0,${l4}}((?:-[ 	]*){3,}|(?:_[ 	]*){3,}|(?:\\*[ 	]*){3,})(?:\\n+|$)`)), fencesBeginRegex: I((l4) => new RegExp(`^ {0,${l4}}(?:\`\`\`|~~~)`)), headingBeginRegex: I((l4) => new RegExp(`^ {0,${l4}}#`)), htmlBeginRegex: I((l4) => new RegExp(`^ {0,${l4}}(?:</?(?:${H2})(?: +|$|/?>)|<(?:script|pre|style|textarea|!--))`, "i")), blockquoteBeginRegex: I((l4) => new RegExp(`^ {0,${l4}}>`)) };
  var ye = /^(?:[ \t]*(?:\n|$))+/;
  var Pe = /^((?: {4}| {0,3}\t)[^\n]+(?:\n(?:[ \t]*(?:\n|$))*)?)+/;
  var Se = /^ {0,3}(`{3,}(?=[^`\n]*(?:\n|$))|~{3,})([^\n]*)(?:\n|$)(?:|([\s\S]*?)(?:\n|$))(?: {0,3}\1[~`]* *(?=\n|$)|$)/;
  var v2 = /^ {0,3}((?:-[\t ]*){3,}|(?:_[ \t]*){3,}|(?:\*[ \t]*){3,})(?:\n+|$)/;
  var _e = /^ {0,3}(#{1,6})(?=\s|$)(.*)(?:\n+|$)/;
  var K = / {0,3}(?:[*+-]|\d{1,9}[.)])/;
  var le = /^(?!bull |blockCode|fences|blockquote|heading|html|table)((?:.|\n(?!\s*?\n|bull |blockCode|fences|blockquote|heading|html|table))+?)\n {0,3}(=+|-+) *(?:\n+|$)/;
  var ue = d(le).replace(/bull/g, K).replace(/blockCode/g, /(?: {4}| {0,3}\t)/).replace(/fences/g, / {0,3}(?:`{3,}|~{3,})/).replace(/blockquote/g, / {0,3}>/).replace(/heading/g, / {0,3}#{1,6}(?:\s|$)/).replace(/html/g, / {0,3}<[^\n>]+>\n/).replace(/\|table/g, "").getRegex();
  var $e = d(le).replace(/bull/g, K).replace(/blockCode/g, /(?: {4}| {0,3}\t)/).replace(/fences/g, / {0,3}(?:`{3,}|~{3,})/).replace(/blockquote/g, / {0,3}>/).replace(/heading/g, / {0,3}#{1,6}(?:\s|$)/).replace(/html/g, / {0,3}<[^\n>]+>\n/).replace(/table/g, / {0,3}\|?(?:[:\- ]*\|)+[\:\- ]*\n/).getRegex();
  var W = /^([^\n]+(?:\n(?!hr|heading|lheading|blockquote|fences|list|html|table|[ \t]+\n)[^\n]+)*)/;
  var Le = /^[^\n]+/;
  var X = /(?!\s*\])(?:\\[\s\S]|[^\[\]\\])+/;
  var ze = d(/^ {0,3}\[(label)\]: *(?:\n[ \t]*)?([^<\s][^\s]*|<.*?>)(?:(?: +(?:\n[ \t]*)?| *\n[ \t]*)(title))? *(?:\n+|$)/).replace("label", X).replace("title", /(?:"(?:\\"?|[^"\\])*"|'[^'\n]*(?:\n[^'\n]+)*\n?'|\([^()]*\))/).getRegex();
  var Ee = d(/^(bull)([ \t][^\n]*?)?(?:\n|$)/).replace(/bull/g, K).getRegex();
  var H2 = "address|article|aside|base|basefont|blockquote|body|caption|center|col|colgroup|dd|details|dialog|dir|div|dl|dt|fieldset|figcaption|figure|footer|form|frame|frameset|h[1-6]|head|header|hr|html|iframe|legend|li|link|main|menu|menuitem|meta|nav|noframes|ol|optgroup|option|p|param|search|section|summary|table|tbody|td|tfoot|th|thead|title|tr|track|ul";
  var J = /<!--(?:-?>|[\s\S]*?(?:-->|$))/;
  var Me = d("^ {0,3}(?:<(script|pre|style|textarea)[\\s>][\\s\\S]*?(?:</\\1>[^\\n]*\\n*|$)|comment[^\\n]*(\\n+|$)|<\\?[\\s\\S]*?(?:\\?>[^\\n]*\\n*|$)|<![A-Z][\\s\\S]*?(?:>[^\\n]*\\n*|$)|<!\\[CDATA\\[[\\s\\S]*?(?:\\]\\]>[^\\n]*\\n*|$)|</?(tag)(?: +|\\n|/?>)[\\s\\S]*?(?:(?:\\n[ 	]*)+\\n|$)|<(?!script|pre|style|textarea)([a-z][a-z0-9-]*)(?:attribute)*? */?>(?=[ \\t]*(?:\\n|$))[\\s\\S]*?(?:(?:\\n[ 	]*)+\\n|$)|</(?!script|pre|style|textarea)[a-z][a-z0-9-]*\\s*>(?=[ \\t]*(?:\\n|$))[\\s\\S]*?(?:(?:\\n[ 	]*)+\\n|$))", "i").replace("comment", J).replace("tag", H2).replace("attribute", / +[a-zA-Z:_][\w.:-]*(?: *= *"[^"\n]*"| *= *'[^'\n]*'| *= *[^\s"'=<>`]+)?/).getRegex();
  var pe = (l4) => d(W).replace("hr", v2).replace("heading", " {0,3}#{1,6}(?:\\s|$)").replace("|lheading", "").replace("|table", "").replace("blockquote", " {0,3}>").replace("fences", " {0,3}(?:`{3,}(?=[^`\\n]*(?:\\n|$))|~~~)[^\\n]*(?:\\n|$)").replace("list", l4).replace("html", "</?(?:tag)(?: +|\\n|/?>)|<(?:script|pre|style|textarea|!--)").replace("tag", H2).getRegex();
  var Ae = pe(/ {0,3}(?:[*+-]|1[.)])[ \t]+[^ \t\n]/);
  var Ie = pe(/ {0,3}(?:[*+-]|\d{1,9}[.)])(?:[ \t]|\n|$)/);
  var Ce = d(/^( {0,3}> ?(paragraph|[^\n]*)(?:\n|$))+/).replace("paragraph", Ie).getRegex();
  var V = { blockquote: Ce, code: Pe, def: ze, fences: Se, heading: _e, hr: v2, html: Me, lheading: ue, list: Ee, newline: ye, paragraph: Ae, table: E2, text: Le };
  var ie = d("^ *([^\\n ].*)\\n {0,3}((?:\\| *)?:?-+:? *(?:\\| *:?-+:? *)*(?:\\| *)?)(?:\\n((?:(?! *\\n|hr|heading|blockquote|code|fences|list|html).*(?:\\n|$))*)\\n*|$)").replace("hr", v2).replace("heading", " {0,3}#{1,6}(?:\\s|$)").replace("blockquote", " {0,3}>").replace("code", "(?: {4}| {0,3}	)[^\\n]").replace("fences", " {0,3}(?:`{3,}(?=[^`\\n]*(?:\\n|$))|~~~)[^\\n]*(?:\\n|$)").replace("list", " {0,3}(?:[*+-]|1[.)])[ \\t]").replace("html", "</?(?:tag)(?: +|\\n|/?>)|<(?:script|pre|style|textarea|!--)").replace("tag", H2).getRegex();
  var Be = { ...V, lheading: $e, table: ie, paragraph: d(W).replace("hr", v2).replace("heading", " {0,3}#{1,6}(?:\\s|$)").replace("|lheading", "").replace("table", ie).replace("blockquote", " {0,3}>").replace("fences", " {0,3}(?:`{3,}(?=[^`\\n]*(?:\\n|$))|~~~)[^\\n]*(?:\\n|$)").replace("list", " {0,3}(?:[*+-]|1[.)])[ \\t]+[^ \\t\\n]").replace("html", "</?(?:tag)(?: +|\\n|/?>)|<(?:script|pre|style|textarea|!--)").replace("tag", H2).getRegex() };
  var De = { ...V, html: d(`^ *(?:comment *(?:\\n|\\s*$)|<(tag)[\\s\\S]+?</\\1> *(?:\\n{2,}|\\s*$)|<tag(?:"[^"]*"|'[^']*'|\\s[^'"/>\\s]*)*?/?> *(?:\\n{2,}|\\s*$))`).replace("comment", J).replace(/tag/g, "(?!(?:a|em|strong|small|s|cite|q|dfn|abbr|data|time|code|var|samp|kbd|sub|sup|i|b|u|mark|ruby|rt|rp|bdi|bdo|span|br|wbr|ins|del|img)\\b)\\w+(?!:|[^\\w\\s@]*@)\\b").getRegex(), def: /^ *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +(["(][^\n]+[")]))? *(?:\n+|$)/, heading: /^(#{1,6})(.*)(?:\n+|$)/, fences: E2, lheading: /^(.+?)\n {0,3}(=+|-+) *(?:\n+|$)/, paragraph: d(W).replace("hr", v2).replace("heading", ` *#{1,6} *[^
]`).replace("lheading", ue).replace("|table", "").replace("blockquote", " {0,3}>").replace("|fences", "").replace("|list", "").replace("|html", "").replace("|tag", "").getRegex() };
  var qe = /^\\([!"#$%&'()*+,\-./:;<=>?@\[\]\\^_`{|}~])/;
  var ve = /^(`+)([^`]|[^`][\s\S]*?[^`])\1(?!`)/;
  var ce = /^( {2,}|\\)\n(?!\s*$)[ \t]*/;
  var He = /^(`+|[^`])(?:(?= {2,}\n)|[\s\S]*?(?:(?=[\\<!\[`*_]|\b_|$)|[^ ](?= {2,}\n)))/;
  var _ = /[\p{P}\p{S}]/u;
  var C2 = /[\s\p{P}\p{S}]/u;
  var Z = /[^\s\p{P}\p{S}]/u;
  var Ze = d(/^((?![*_])punctSpace)/, "u").replace(/punctSpace/g, C2).getRegex();
  var Ge = /[\p{Pi}\p{Ps}"']/u;
  var he = /(?!~)[\p{P}\p{S}]/u;
  var Qe = /(?!~)[\s\p{P}\p{S}]/u;
  var Ne = /(?:[^\s\p{P}\p{S}]|~)/u;
  var je = d(/link|precode-code|html/, "g").replace("link", /\[(?:[^\[\]`]|(?<a>`+)[^`]+\k<a>(?!`))*?\]\((?:\\[\s\S]|[^\\\(\)]|\((?:\\[\s\S]|[^\\\(\)])*\))*\)/).replace("precode-", we ? "(?<!`)()" : "(^^|[^`])").replace("code", /(?<b>`+)[^`]+\k<b>(?!`)/).replace("html", /<(?! )[^<>]*?>/).getRegex();
  var de = /^(?:\*+(?:((?!\*)punct)|([^\s*]))?)|^_+(?:((?!_)punct)|([^\s_]))?/;
  var Ue = d(de, "u").replace(/punct/g, _).getRegex();
  var Fe = d(de, "u").replace(/punct/g, he).getRegex();
  var Ke = /^(?:\*+(?:((?!\*)(?!openQuote)punct)|([^\s*]))?)|^_+(?:((?!_)(?!openQuote)punct)|([^\s_]))?/;
  var We = d(Ke, "u").replace(/openQuote/g, Ge).replace(/punct/g, _).getRegex();
  var ke = "^[^_*]*?__[^_*]*?\\*[^_*]*?(?=__)|[^*]+(?=[^*])|(?!\\*)punct(\\*+)(?=[\\s]|$)|notPunctSpace(\\*+)(?!\\*)(?=punctSpace|$)|(?!\\*)punctSpace(\\*+)(?=notPunctSpace)|[\\s](\\*+)(?!\\*)(?=punct)|(?!\\*)punct(\\*+)(?!\\*)(?=punct)|notPunctSpace(\\*+)(?=notPunctSpace)";
  var Xe = d(ke, "gu").replace(/notPunctSpace/g, Z).replace(/punctSpace/g, C2).replace(/punct/g, _).getRegex();
  var Je = d(ke, "gu").replace(/notPunctSpace/g, Ne).replace(/punctSpace/g, Qe).replace(/punct/g, he).getRegex();
  var Ve = "^[^_*]*?__[^_*]*?\\*[^_*]*?(?=__)|[^*]+(?=[^*])|(?!\\*)punct(\\*+)(?=[\\s]|$)|notPunctSpace(\\*+)(?!\\*)(?=punctSpace|$)|(?!\\*)[\\s](\\*+)(?=notPunctSpace)|[\\s](\\*+)(?!\\*)(?=punct)|(?!\\*)punct(\\*+)(?!\\*)(?=punct)|(?:(?!\\*)punct|notPunctSpace)(\\*+)(?!\\*)(?=notPunctSpace)";
  var Ye = d(Ve, "gu").replace(/notPunctSpace/g, Z).replace(/punctSpace/g, C2).replace(/punct/g, _).getRegex();
  var et = d("^[^_*]*?\\*\\*[^_*]*?_[^_*]*?(?=\\*\\*)|[^_]+(?=[^_])|(?!_)punct(_+)(?=[\\s]|$)|notPunctSpace(_+)(?!_)(?=punctSpace|$)|(?!_)punctSpace(_+)(?=notPunctSpace)|[\\s](_+)(?!_)(?=punct)|(?!_)punct(_+)(?!_)(?=punct)", "gu").replace(/notPunctSpace/g, Z).replace(/punctSpace/g, C2).replace(/punct/g, _).getRegex();
  var tt = "^[^_*]*?\\*\\*[^_*]*?_[^_*]*?(?=\\*\\*)|[^_]+(?=[^_])|(?!_)punct(_+)(?=[\\s]|$)|notPunctSpace(_+)(?!_)(?=punctSpace|$)|(?!_)[\\s](_+)(?=notPunctSpace)|[\\s](_+)(?!_)(?=punct)|(?!_)punct(_+)(?!_)(?=punct)|(?:(?!_)punct|notPunctSpace)(_+)(?!_)(?=notPunctSpace)";
  var nt = d(tt, "gu").replace(/notPunctSpace/g, Z).replace(/punctSpace/g, C2).replace(/punct/g, _).getRegex();
  var rt = d(/^~~?(?:((?!~)punct)|[^\s~])/, "u").replace(/punct/g, _).getRegex();
  var st = "^[^~]+(?=[^~])|(?!~)punct(~~?)(?=[\\s]|$)|notPunctSpace(~~?)(?!~)(?=punctSpace|$)|(?!~)punctSpace(~~?)(?=notPunctSpace)|[\\s](~~?)(?!~)(?=punct)|(?!~)punct(~~?)(?!~)(?=punct)|notPunctSpace(~~?)(?=notPunctSpace)";
  var it = d(st, "gu").replace(/notPunctSpace/g, Z).replace(/punctSpace/g, C2).replace(/punct/g, _).getRegex();
  var ot = d(/\\(punct)/, "gu").replace(/punct/g, _).getRegex();
  var at = d(/^<(scheme:[^\s\x00-\x1f<>]*|email)>/).replace("scheme", /[a-zA-Z][a-zA-Z0-9+.-]{1,31}/).replace("email", /[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+(@)[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+(?![-_])/).getRegex();
  var lt = d(J).replace("(?:-->|$)", "-->").getRegex();
  var ut = d("^comment|^</[a-zA-Z][a-zA-Z0-9-]*\\s*>|^<[a-zA-Z][a-zA-Z0-9-]*(?:attribute)*?\\s*/?>|^<\\?[\\s\\S]*?\\?>|^<![a-zA-Z]+\\s[\\s\\S]*?>|^<!\\[CDATA\\[[\\s\\S]*?\\]\\]>").replace("comment", lt).replace("attribute", /\s+[a-zA-Z:_][\w.:-]*(?:\s*=\s*"[^"]*"|\s*=\s*'[^']*'|\s*=\s*[^\s"'=<>`]+)?/).getRegex();
  var ge = /\[(?:\\[\s\S]|[^\[\]\\])*\]/;
  var N2 = d(/(?:\[(?:brackets|\\[\s\S]|[^\[\]\\])*\]|\\[\s\S]|`+(?!`)[^`]*?`+(?!`)|``+(?=\])|[^\[\]\\`])*?/).replace("brackets", ge).getRegex();
  var pt = d(/^!?\[(label)\]\(\s*(href)(?:(?:[ \t]+(?:\n[ \t]*)?|\n[ \t]*)(title))?\s*\)/).replace("label", N2).replace("href", /<(?:\\.|[^\n<>\\])+>|[^ \t\n\x00-\x1f]+|(?=\))/).replace("title", /"(?:\\"?|[^"\\])*"|'(?:\\'?|[^'\\])*'|\((?:\\\)?|[^)\\])*\)/).getRegex();
  var ct = d(/^!?\[(label)\]\[(ref)\]/).replace("label", N2).replace("ref", X).getRegex();
  var ht = d(/^!?\[(ref)\](?:\[\])?/).replace("ref", X).getRegex();
  var oe = /(?!\s*\])(?:\\[\s\S]|[^\[\]\\]){1,999}/;
  var dt = d(/(?:[^\[\]\\`]*(?:\[(?:brackets|\\[\s\S]|[^\[\]\\])*\]|\\[\s\S]|`+(?!`)[^`]*?`+(?!`)|``+(?=\]))){0,999}?[^\[\]\\`]*?/).replace("brackets", ge).getRegex();
  var kt = d("reflink|nolink(?!\\()", "g").replace("reflink", d(/^!?\[(label)\]\[(ref)\]/).replace("label", dt).replace("ref", oe).getRegex()).replace("nolink", d(/^!?\[(ref)\](?:\[\])?/).replace("ref", oe).getRegex()).getRegex();
  var ae = /[hH][tT][tT][pP][sS]?|[fF][tT][pP]/;
  var Y = { _backpedal: E2, anyPunctuation: ot, autolink: at, blockSkip: je, br: ce, code: ve, del: E2, delLDelim: E2, delRDelim: E2, emStrongLDelim: Ue, emStrongRDelimAst: Xe, emStrongRDelimUnd: et, escape: qe, link: pt, nolink: ht, punctuation: Ze, reflink: ct, reflinkSearch: kt, tag: ut, text: He, url: E2 };
  var gt = { ...Y, emStrongLDelim: We, emStrongRDelimAst: Ye, emStrongRDelimUnd: nt, link: d(/^!?\[(label)\]\((.*?)\)/).replace("label", N2).getRegex(), reflink: d(/^!?\[(label)\]\s*\[([^\]]*)\]/).replace("label", N2).getRegex() };
  var F = { ...Y, emStrongRDelimAst: Je, emStrongLDelim: Fe, delLDelim: rt, delRDelim: it, url: d(/^((?:protocol):\/\/|www\.)(?:[a-zA-Z0-9\-]+\.?)+[^\s<]*|^email/).replace("protocol", ae).replace("email", /[A-Za-z0-9._+-]+(@)[a-zA-Z0-9-_]+(?:\.[a-zA-Z0-9-_]*[a-zA-Z0-9])+(?![\w-])/).getRegex(), _backpedal: /(?:[^?!.,:;*_'"~()&]+|\([^)]*\)|&(?![a-zA-Z0-9]+;$)|[?!.,:;*_'"~)]+(?!$))+/, del: /^(~~?)(?=[^\s~])((?:\\[\s\S]|[^\\])*?(?:\\[\s\S]|[^\s~\\]))\1(?=[^~]|$)/, text: d(/^(`+|~+|[^`~])(?:(?=[`~])|(?= {2,}\n)|(?=[a-zA-Z0-9.!#$%&'*+\/=?_`{\|}~-]+@)|[\s\S]*?(?:(?=[\\<!\[`*~_]|\b_|protocol:\/\/|www\.|$)|[^ ](?= {2,}\n)|[^a-zA-Z0-9.!#$%&'*+\/=?_`{\|}~-](?=[a-zA-Z0-9.!#$%&'*+\/=?_`{\|}~-]+@)))/).replace("protocol", ae).getRegex() };
  var ft = { ...F, br: d(ce).replace("{2,}", "*").getRegex(), text: d(F.text).replace("\\b_", "\\b_| {2,}\\n").replace(/\{2,\}/g, "*").getRegex() };
  var G = { normal: V, gfm: Be, pedantic: De };
  var B = { normal: Y, gfm: F, breaks: ft, pedantic: gt };
  var mt = { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" };
  var fe = (l4) => mt[l4];
  function R2(l4, e2) {
    if (e2) {
      if (m2.escapeTest.test(l4)) return l4.replace(m2.escapeReplace, fe);
    } else if (m2.escapeTestNoEncode.test(l4)) return l4.replace(m2.escapeReplaceNoEncode, fe);
    return l4;
  }
  function ee(l4) {
    try {
      l4 = encodeURI(l4).replace(m2.percentDecode, "%");
    } catch {
      return null;
    }
    return l4;
  }
  function te(l4, e2) {
    let t2 = l4.replace(m2.findPipe, (r2, o2, s) => {
      let u = false, a = o2;
      for (; --a >= 0 && s[a] === "\\"; ) u = !u;
      return u ? "|" : " |";
    }), n = t2.split(m2.splitPipe), i = 0;
    if (n[0].trim() || n.shift(), n.length > 0 && !n.at(-1)?.trim() && n.pop(), e2) if (n.length > e2) n.splice(e2);
    else for (; n.length < e2; ) n.push("");
    for (; i < n.length; i++) n[i] = n[i].trim().replace(m2.slashPipe, "|");
    return n;
  }
  function $2(l4, e2, t2) {
    let n = l4.length;
    if (n === 0) return "";
    let i = 0;
    for (; i < n; ) {
      let r2 = l4.charAt(n - i - 1);
      if (r2 === e2 && !t2) i++;
      else if (r2 !== e2 && t2) i++;
      else break;
    }
    return l4.slice(0, n - i);
  }
  function ne(l4) {
    let e2 = l4.split(`
`), t2 = e2.length - 1;
    for (; t2 >= 0 && m2.blankLine.test(e2[t2]); ) t2--;
    return e2.length - t2 <= 2 ? l4 : e2.slice(0, t2 + 1).join(`
`);
  }
  function D2(l4) {
    return l4.toLowerCase().toUpperCase().toLowerCase();
  }
  function me(l4, e2) {
    if (l4.indexOf(e2[1]) === -1) return -1;
    let t2 = 0;
    for (let n = 0; n < l4.length; n++) if (l4[n] === "\\") n++;
    else if (l4[n] === e2[0]) t2++;
    else if (l4[n] === e2[1] && (t2--, t2 < 0)) return n;
    return t2 > 0 ? -2 : -1;
  }
  function xe(l4, e2 = 0) {
    let t2 = e2, n = "";
    for (let i of l4) if (i === "	") {
      let r2 = 4 - t2 % 4;
      n += " ".repeat(r2), t2 += r2;
    } else n += i, t2++;
    return n;
  }
  function be(l4, e2, t2, n, i) {
    let r2 = e2.href, o2 = e2.title || null, s = l4[1].replace(i.other.outputLinkReplace, "$1"), u = l4[0].charAt(0) === "!";
    n.state.inLink = true;
    let a = n.state.linkEmitted, p2 = n.state.inRawBlock;
    n.state.linkEmitted = false;
    let c2 = n.inlineTokens(s), h = n.state.linkEmitted;
    if (n.state.linkEmitted = a, n.state.inLink = false, !u) {
      if (h) {
        n.state.inRawBlock = p2;
        return;
      }
      n.state.linkEmitted = true;
    }
    return { type: u ? "image" : "link", raw: t2, href: r2, title: o2, text: s, tokens: c2 };
  }
  function xt(l4, e2, t2) {
    let n = l4.match(t2.other.indentCodeCompensation);
    if (n === null) return e2;
    let i = n[1];
    return e2.split(`
`).map((r2) => {
      let o2 = r2.match(t2.other.beginningSpace);
      if (o2 === null) return r2;
      let [s] = o2;
      return r2.slice(Math.min(s.length, i.length));
    }).join(`
`);
  }
  function Re(l4, e2, t2, n) {
    if (!e2.includes("<")) return false;
    for (let i = 0; i < e2.length; i++) {
      if (e2[i] === "\\") {
        i++;
        continue;
      }
      if (e2[i] === "`") {
        let s = n.inline.code.exec(e2.slice(i));
        if (s) {
          i += s[0].length - 1;
          continue;
        }
      }
      if (e2[i] !== "<") continue;
      let r2 = l4.slice(t2 + i), o2 = n.inline.tag.exec(r2) || n.inline.autolink.exec(r2);
      if (o2) {
        if (o2[0].length > e2.length - i) return true;
        i += o2[0].length - 1;
      }
    }
    return false;
  }
  var y2 = class {
    options;
    rules;
    lexer;
    constructor(e2) {
      this.options = e2 || T;
    }
    space(e2) {
      let t2 = this.rules.block.newline.exec(e2);
      if (t2 && t2[0].length > 0) return { type: "space", raw: t2[0] };
    }
    code(e2) {
      let t2 = this.rules.block.code.exec(e2);
      if (t2) {
        let n = this.options.pedantic ? t2[0] : ne(t2[0]), i = n.replace(this.rules.other.codeRemoveIndent, "");
        return { type: "code", raw: n, codeBlockStyle: "indented", text: i };
      }
    }
    fences(e2) {
      let t2 = this.rules.block.fences.exec(e2);
      if (t2) {
        let n = t2[0], i = xt(n, t2[3] || "", this.rules);
        return { type: "code", raw: n, lang: t2[2] ? t2[2].trim().replace(this.rules.inline.anyPunctuation, "$1") : t2[2], text: i };
      }
    }
    heading(e2) {
      let t2 = this.rules.block.heading.exec(e2);
      if (t2) {
        let n = t2[2].trim();
        if (this.rules.other.endingHash.test(n)) {
          let i = $2(n, "#");
          (this.options.pedantic || !i || this.rules.other.endingSpaceTabChar.test(i)) && (n = i.trim());
        }
        return { type: "heading", raw: $2(t2[0], `
`), depth: t2[1].length, text: n, tokens: this.lexer.inline(n) };
      }
    }
    hr(e2) {
      let t2 = this.rules.block.hr.exec(e2);
      if (t2) return { type: "hr", raw: $2(t2[0], `
`) };
    }
    blockquote(e2) {
      let t2 = this.rules.block.blockquote.exec(e2);
      if (t2) {
        let n = $2(t2[0], `
`).split(`
`), i = "", r2 = "", o2 = [];
        for (; n.length > 0; ) {
          let s = false, u = [], a;
          for (a = 0; a < n.length; a++) if (this.rules.other.blockquoteStart.test(n[a])) u.push(n[a]), s = true;
          else if (!s) u.push(n[a]);
          else break;
          n = n.slice(a);
          let p2 = u.join(`
`), c2 = p2.replace(this.rules.other.blockquoteSetextReplace, `
    $1`).replace(this.rules.other.blockquoteSetextReplace2, "");
          i = i ? `${i}
${p2}` : p2, r2 = r2 ? `${r2}
${c2}` : c2;
          let h = this.lexer.state.top;
          if (this.lexer.state.top = true, this.lexer.blockTokens(c2, o2, true), this.lexer.state.top = h, n.length === 0) break;
          let k2 = o2.at(-1);
          if (k2?.type === "code") break;
          if (k2?.type === "blockquote") {
            let O = k2, g2 = n.join(`
`), w2 = O.raw + `
` + g2.replace(this.rules.other.blockquoteSetextReplace2, ""), z = this.blockquote(w2);
            o2[o2.length - 1] = z, i = `${i}
${g2}`, r2 = r2.substring(0, r2.length - O.text.length) + z.text;
            break;
          } else if (k2?.type === "list") {
            let O = k2, g2 = O.raw + `
` + n.join(`
`), w2 = this.list(g2);
            o2[o2.length - 1] = w2, i = i.substring(0, i.length - k2.raw.length) + w2.raw, r2 = r2.substring(0, r2.length - O.raw.length) + w2.raw, n = g2.substring(o2.at(-1).raw.length).split(`
`);
            continue;
          }
        }
        return { type: "blockquote", raw: i, tokens: o2, text: r2 };
      }
    }
    list(e2) {
      let t2 = this.rules.block.list.exec(e2);
      if (t2) {
        let n = t2[1].trim(), i = n.length > 1, r2 = { type: "list", raw: "", ordered: i, start: i ? +n.slice(0, -1) : "", loose: false, items: [] };
        n = i ? `\\d{1,9}\\${n.slice(-1)}` : `\\${n}`, this.options.pedantic && (n = i ? n : "[*+-]");
        let o2 = this.rules.other.listItemRegex(n), s = false;
        for (; e2; ) {
          let a = false, p2 = "", c2 = "";
          if (!(t2 = o2.exec(e2)) || this.rules.block.hr.test(e2)) break;
          p2 = t2[0], e2 = e2.substring(p2.length);
          let h = xe(t2[2].split(`
`, 1)[0], t2[1].length), k2 = e2.split(`
`, 1)[0], O = !h.trim(), g2 = 0;
          if (this.options.pedantic ? (g2 = 2, c2 = h.trimStart()) : O ? g2 = t2[1].length + 1 : (g2 = h.search(this.rules.other.nonSpaceChar), g2 = g2 > 4 ? 1 : g2, c2 = h.slice(g2), g2 += t2[1].length), O && this.rules.other.blankLine.test(k2) && (p2 += k2 + `
`, e2 = e2.substring(k2.length + 1), a = true), !a) {
            let w2 = this.rules.other.nextBulletRegex(g2), z = this.rules.other.hrRegex(g2), re = this.rules.other.fencesBeginRegex(g2), se = this.rules.other.headingBeginRegex(g2), Te = this.rules.other.htmlBeginRegex(g2), Oe = this.rules.other.blockquoteBeginRegex(g2);
            for (; e2; ) {
              let j2 = e2.split(`
`, 1)[0], q2;
              if (k2 = j2, this.options.pedantic ? (k2 = k2.replace(this.rules.other.listReplaceNesting, "  "), q2 = k2) : q2 = k2.replace(this.rules.other.tabCharGlobal, "    "), re.test(k2) || se.test(k2) || Te.test(k2) || Oe.test(k2) || w2.test(k2) || z.test(k2)) break;
              if (q2.search(this.rules.other.nonSpaceChar) >= g2 || !k2.trim()) c2 += `
` + q2.slice(g2);
              else {
                if (O || h.replace(this.rules.other.tabCharGlobal, "    ").search(this.rules.other.nonSpaceChar) >= 4 || re.test(h) || se.test(h) || z.test(h)) break;
                c2 += `
` + k2;
              }
              O = !k2.trim(), p2 += j2 + `
`, e2 = e2.substring(j2.length + 1), h = q2.slice(g2);
            }
          }
          r2.loose || (s ? r2.loose = true : this.rules.other.doubleBlankLine.test(p2) && (s = true)), r2.items.push({ type: "list_item", raw: p2, task: !!this.options.gfm && this.rules.other.listIsTask.test(c2), loose: false, text: c2, tokens: [] }), r2.raw += p2;
        }
        let u = r2.items.at(-1);
        if (u) u.raw = u.raw.trimEnd(), u.text = u.text.trimEnd();
        else return;
        r2.raw = r2.raw.trimEnd();
        for (let a of r2.items) if (this.lexer.state.top = false, a.tokens = this.lexer.blockTokens(a.text, []), !r2.loose) {
          let p2 = a.tokens.filter((h) => h.type === "space"), c2 = p2.length > 0 && p2.some((h) => this.rules.other.anyLine.test(h.raw));
          r2.loose = c2;
        }
        for (let a of r2.items) {
          let p2 = a.tokens[0];
          if (a.task && (p2?.type === "text" || p2?.type === "paragraph")) {
            a.text = a.text.replace(this.rules.other.listReplaceTask, ""), p2.raw = p2.raw.replace(this.rules.other.listReplaceTask, ""), p2.text = p2.text.replace(this.rules.other.listReplaceTask, "");
            for (let h = this.lexer.inlineQueue.length - 1; h >= 0; h--) if (this.rules.other.listIsTask.test(this.lexer.inlineQueue[h].src)) {
              this.lexer.inlineQueue[h].src = this.lexer.inlineQueue[h].src.replace(this.rules.other.listReplaceTask, "");
              break;
            }
            let c2 = this.rules.other.listTaskCheckbox.exec(a.raw);
            if (c2) {
              let h = { type: "checkbox", raw: c2[0] + " ", checked: c2[0] !== "[ ]" };
              a.checked = h.checked, r2.loose ? a.tokens[0] && ["paragraph", "text"].includes(a.tokens[0].type) && "tokens" in a.tokens[0] && a.tokens[0].tokens ? (a.tokens[0].raw = h.raw + a.tokens[0].raw, a.tokens[0].text = h.raw + a.tokens[0].text, a.tokens[0].tokens.unshift(h)) : a.tokens.unshift({ type: "paragraph", raw: h.raw, text: h.raw, tokens: [h] }) : a.tokens.unshift(h);
            }
          } else a.task && (a.task = false);
        }
        if (r2.loose) for (let a of r2.items) {
          a.loose = true;
          for (let p2 of a.tokens) p2.type === "text" && (p2.type = "paragraph");
        }
        return r2;
      }
    }
    html(e2) {
      let t2 = this.rules.block.html.exec(e2);
      if (t2) {
        let n = ne(t2[0]);
        return { type: "html", block: true, raw: n, pre: t2[1] === "pre" || t2[1] === "script" || t2[1] === "style", text: n };
      }
    }
    def(e2) {
      let t2 = this.rules.block.def.exec(e2);
      if (t2) {
        let n = D2(t2[1]).replace(this.rules.other.multipleSpaceGlobal, " "), i = t2[2] ? t2[2].replace(this.rules.other.hrefBrackets, "$1").replace(this.rules.inline.anyPunctuation, "$1") : "", r2 = t2[3] ? t2[3].substring(1, t2[3].length - 1).replace(this.rules.inline.anyPunctuation, "$1") : t2[3];
        return { type: "def", tag: n, raw: $2(t2[0], `
`), href: i, title: r2 };
      }
    }
    table(e2) {
      let t2 = this.rules.block.table.exec(e2);
      if (!t2 || !this.rules.other.tableDelimiter.test(t2[2])) return;
      let n = te(t2[1]), i = t2[2].replace(this.rules.other.tableAlignChars, "").split("|"), r2 = t2[3]?.trim() ? t2[3].replace(this.rules.other.tableRowBlankLine, "").split(`
`) : [], o2 = { type: "table", raw: $2(t2[0], `
`), header: [], align: [], rows: [] };
      if (n.length === i.length) {
        for (let s of i) this.rules.other.tableAlignRight.test(s) ? o2.align.push("right") : this.rules.other.tableAlignCenter.test(s) ? o2.align.push("center") : this.rules.other.tableAlignLeft.test(s) ? o2.align.push("left") : o2.align.push(null);
        for (let s = 0; s < n.length; s++) o2.header.push({ text: n[s], tokens: this.lexer.inline(n[s]), header: true, align: o2.align[s] });
        for (let s of r2) o2.rows.push(te(s, o2.header.length).map((u, a) => ({ text: u, tokens: this.lexer.inline(u), header: false, align: o2.align[a] })));
        return o2;
      }
    }
    lheading(e2) {
      let t2 = this.rules.block.lheading.exec(e2);
      if (t2) {
        let n = t2[1].trim();
        return { type: "heading", raw: $2(t2[0], `
`), depth: t2[2].charAt(0) === "=" ? 1 : 2, text: n, tokens: this.lexer.inline(n) };
      }
    }
    paragraph(e2) {
      let t2 = this.rules.block.paragraph.exec(e2);
      if (t2) {
        let n = t2[1].charAt(t2[1].length - 1) === `
` ? t2[1].slice(0, -1) : t2[1];
        return { type: "paragraph", raw: t2[0], text: n, tokens: this.lexer.inline(n) };
      }
    }
    text(e2) {
      let t2 = this.rules.block.text.exec(e2);
      if (t2) return { type: "text", raw: t2[0], text: t2[0], tokens: this.lexer.inline(t2[0]) };
    }
    escape(e2) {
      let t2 = this.rules.inline.escape.exec(e2);
      if (t2) return { type: "escape", raw: t2[0], text: t2[1] };
    }
    tag(e2) {
      let t2 = this.rules.inline.tag.exec(e2);
      if (t2) return !this.lexer.state.inLink && this.rules.other.startATag.test(t2[0]) ? this.lexer.state.inLink = true : this.lexer.state.inLink && this.rules.other.endATag.test(t2[0]) && (this.lexer.state.inLink = false), !this.lexer.state.inRawBlock && this.rules.other.startPreScriptTag.test(t2[0]) ? this.lexer.state.inRawBlock = true : this.lexer.state.inRawBlock && this.rules.other.endPreScriptTag.test(t2[0]) && (this.lexer.state.inRawBlock = false), { type: "html", raw: t2[0], inLink: this.lexer.state.inLink, inRawBlock: this.lexer.state.inRawBlock, block: false, text: t2[0] };
    }
    link(e2) {
      let t2 = this.rules.inline.link.exec(e2);
      if (t2) {
        let n = t2[0].charAt(0) === "!" ? 2 : 1;
        if (!this.options.pedantic && Re(e2, t2[1], n, this.rules)) return;
        let i = t2[2].trim();
        if (!this.options.pedantic && this.rules.other.startAngleBracket.test(i)) {
          if (!this.rules.other.endAngleBracket.test(i)) return;
          let s = $2(i.slice(0, -1), "\\");
          if ((i.length - s.length) % 2 === 0) return;
        } else {
          let s = me(t2[2], "()");
          if (s === -2) return;
          if (s > -1) {
            let a = (t2[0].indexOf("!") === 0 ? 5 : 4) + t2[1].length + s;
            t2[2] = t2[2].substring(0, s), t2[0] = t2[0].substring(0, a).trim(), t2[3] = "";
          }
        }
        let r2 = t2[2], o2 = "";
        if (this.options.pedantic) {
          let s = this.rules.other.pedanticHrefTitle.exec(r2);
          s && (r2 = s[1], o2 = s[3]);
        } else o2 = t2[3] ? t2[3].slice(1, -1) : "";
        return r2 = r2.trim(), this.rules.other.startAngleBracket.test(r2) && (this.options.pedantic && !this.rules.other.endAngleBracket.test(i) ? r2 = r2.slice(1) : r2 = r2.slice(1, -1)), be(t2, { href: r2 && r2.replace(this.rules.inline.anyPunctuation, "$1"), title: o2 && o2.replace(this.rules.inline.anyPunctuation, "$1") }, t2[0], this.lexer, this.rules);
      }
    }
    reflink(e2, t2) {
      let n;
      if ((n = this.rules.inline.reflink.exec(e2)) || (n = this.rules.inline.nolink.exec(e2))) {
        let i = n[0].charAt(0) === "!" ? 2 : 1;
        if (!this.options.pedantic && Re(e2, n[1], i, this.rules)) return;
        let r2 = (n[2] || n[1]).replace(this.rules.other.multipleSpaceGlobal, " "), o2 = t2[D2(r2)];
        if (!o2) {
          let s = n[0].charAt(0);
          return { type: "text", raw: s, text: s };
        }
        return be(n, o2, n[0], this.lexer, this.rules);
      }
    }
    emStrong(e2, t2, n = "") {
      let i = this.rules.inline.emStrongLDelim.exec(e2);
      if (!i || !i[1] && !i[2] && !i[3] && !i[4] || i[4] && n.match(this.rules.other.unicodeAlphaNumeric)) return;
      if (!(i[1] || i[3] || "") || !n || this.rules.inline.punctuation.exec(n)) {
        let o2 = [...i[0]].length - 1, s, u, a = o2, p2 = 0, c2 = i[0][0], h = n === c2, k2 = c2 === "*" ? this.rules.inline.emStrongRDelimAst : this.rules.inline.emStrongRDelimUnd;
        for (k2.lastIndex = 0, t2 = t2.slice(-1 * e2.length + o2); (i = k2.exec(t2)) !== null; ) {
          if (s = i[1] || i[2] || i[3] || i[4] || i[5] || i[6], !s) continue;
          if (u = [...s].length, i[3] || i[4]) {
            a += u;
            continue;
          } else if (i[5] || i[6]) {
            if (o2 % 3 && !((o2 + u) % 3)) {
              p2 += u;
              continue;
            }
            if (h) break;
          }
          if (a -= u, a > 0) continue;
          u = Math.min(u, u + a + p2);
          let O = [...i[0]][0].length, g2 = e2.slice(0, o2 + i.index + O + u);
          if (Math.min(o2, u) % 2) {
            let z = g2.slice(1, -1);
            return { type: "em", raw: g2, text: z, tokens: this.lexer.inlineTokens(z) };
          }
          let w2 = g2.slice(2, -2);
          return { type: "strong", raw: g2, text: w2, tokens: this.lexer.inlineTokens(w2) };
        }
      }
    }
    codespan(e2) {
      let t2 = this.rules.inline.code.exec(e2);
      if (t2) {
        let n = t2[2].replace(this.rules.other.newLineCharGlobal, " "), i = this.rules.other.nonSpaceChar.test(n), r2 = this.rules.other.startingSpaceChar.test(n) && this.rules.other.endingSpaceChar.test(n);
        return i && r2 && (n = n.substring(1, n.length - 1)), { type: "codespan", raw: t2[0], text: n };
      }
    }
    br(e2) {
      let t2 = this.rules.inline.br.exec(e2);
      if (t2) return { type: "br", raw: t2[0] };
    }
    del(e2, t2, n = "") {
      let i = this.rules.inline.delLDelim.exec(e2);
      if (!i) return;
      if (!(i[1] || "") || !n || this.rules.inline.punctuation.exec(n)) {
        let o2 = [...i[0]].length - 1, s, u, a = o2, p2 = this.rules.inline.delRDelim;
        for (p2.lastIndex = 0, t2 = t2.slice(-1 * e2.length + o2); (i = p2.exec(t2)) !== null; ) {
          if (s = i[1] || i[2] || i[3] || i[4] || i[5] || i[6], !s || (u = [...s].length, u !== o2)) continue;
          if (i[3] || i[4]) {
            a += u;
            continue;
          }
          if (a -= u, a > 0) continue;
          u = Math.min(u, u + a);
          let c2 = [...i[0]][0].length, h = e2.slice(0, o2 + i.index + c2 + u), k2 = h.slice(o2, -o2);
          return { type: "del", raw: h, text: k2, tokens: this.lexer.inlineTokens(k2) };
        }
      }
    }
    autolink(e2) {
      let t2 = this.rules.inline.autolink.exec(e2);
      if (t2) {
        let n, i;
        return t2[2] === "@" ? (n = t2[1], i = "mailto:" + n) : (n = t2[1], i = n), { type: "link", raw: t2[0], text: n, href: i, autolink: true, tokens: [{ type: "text", raw: n, text: n }] };
      }
    }
    url(e2) {
      let t2;
      if (t2 = this.rules.inline.url.exec(e2)) {
        let n, i;
        if (t2[2] === "@") n = t2[0], i = "mailto:" + n;
        else {
          let r2;
          do
            r2 = t2[0], t2[0] = this.rules.inline._backpedal.exec(t2[0])?.[0] ?? "";
          while (r2 !== t2[0]);
          n = t2[0], t2[1] === "www." ? i = "http://" + t2[0] : i = t2[0];
        }
        return { type: "link", raw: t2[0], text: n, href: i, autolink: true, tokens: [{ type: "text", raw: n, text: n }] };
      }
    }
    inlineText(e2) {
      let t2 = this.rules.inline.text.exec(e2);
      if (t2) {
        let n = this.lexer.state.inRawBlock;
        return { type: "text", raw: t2[0], text: t2[0], escaped: n };
      }
    }
  };
  var x2 = class l2 {
    tokens;
    options;
    state;
    inlineQueue;
    tokenizer;
    constructor(e2) {
      this.tokens = [], this.tokens.links = /* @__PURE__ */ Object.create(null), this.options = e2 || T, this.options.tokenizer = this.options.tokenizer || new y2(), this.tokenizer = this.options.tokenizer, this.tokenizer.options = this.options, this.tokenizer.lexer = this, this.inlineQueue = [], this.state = { inLink: false, inRawBlock: false, linkEmitted: false, top: true };
      let t2 = { other: m2, block: G.normal, inline: B.normal };
      this.options.pedantic ? (t2.block = G.pedantic, t2.inline = B.pedantic) : this.options.gfm && (t2.block = G.gfm, this.options.breaks ? t2.inline = B.breaks : t2.inline = B.gfm), this.tokenizer.rules = t2;
    }
    static get rules() {
      return { block: G, inline: B };
    }
    static lex(e2, t2) {
      return new l2(t2).lex(e2);
    }
    static lexInline(e2, t2) {
      return new l2(t2).inlineTokens(e2);
    }
    lex(e2) {
      e2 = e2.replace(m2.carriageReturn, `
`), this.blockTokens(e2, this.tokens);
      for (let t2 = 0; t2 < this.inlineQueue.length; t2++) {
        let n = this.inlineQueue[t2];
        this.inlineTokens(n.src, n.tokens);
      }
      return this.inlineQueue = [], this.tokens;
    }
    blockTokens(e2, t2 = [], n = false) {
      this.tokenizer.lexer = this, this.options.pedantic && (e2 = e2.replace(m2.tabCharGlobal, "    ").replace(m2.spaceLine, ""));
      let i = 1 / 0;
      for (; e2; ) {
        if (e2.length < i) i = e2.length;
        else {
          this.infiniteLoopError(e2.charCodeAt(0));
          break;
        }
        let r2;
        if (this.options.extensions?.block?.some((s) => (r2 = s.call({ lexer: this }, e2, t2)) ? (e2 = e2.substring(r2.raw.length), t2.push(r2), true) : false)) continue;
        if (r2 = this.tokenizer.space(e2)) {
          e2 = e2.substring(r2.raw.length);
          let s = t2.at(-1);
          r2.raw.length === 1 && s !== void 0 ? s.raw += `
` : t2.push(r2);
          continue;
        }
        if (r2 = this.tokenizer.code(e2)) {
          e2 = e2.substring(r2.raw.length);
          let s = t2.at(-1);
          s?.type === "paragraph" || s?.type === "text" ? (s.raw += (s.raw.endsWith(`
`) ? "" : `
`) + r2.raw, s.text += `
` + r2.text, this.inlineQueue.at(-1).src = s.text) : t2.push(r2);
          continue;
        }
        if (r2 = this.tokenizer.fences(e2)) {
          e2 = e2.substring(r2.raw.length), t2.push(r2);
          continue;
        }
        if (r2 = this.tokenizer.heading(e2)) {
          e2 = e2.substring(r2.raw.length), t2.push(r2);
          continue;
        }
        if (r2 = this.tokenizer.hr(e2)) {
          e2 = e2.substring(r2.raw.length), t2.push(r2);
          continue;
        }
        if (r2 = this.tokenizer.blockquote(e2)) {
          e2 = e2.substring(r2.raw.length), t2.push(r2);
          continue;
        }
        if (r2 = this.tokenizer.list(e2)) {
          e2 = e2.substring(r2.raw.length), t2.push(r2);
          continue;
        }
        if (r2 = this.tokenizer.html(e2)) {
          e2 = e2.substring(r2.raw.length), t2.push(r2);
          continue;
        }
        if (r2 = this.tokenizer.def(e2)) {
          e2 = e2.substring(r2.raw.length);
          let s = t2.at(-1);
          s?.type === "paragraph" || s?.type === "text" ? (s.raw += (s.raw.endsWith(`
`) ? "" : `
`) + r2.raw, s.text += `
` + r2.raw, this.inlineQueue.at(-1).src = s.text) : this.tokens.links[r2.tag] || (this.tokens.links[r2.tag] = { href: r2.href, title: r2.title }, t2.push(r2));
          continue;
        }
        if (r2 = this.tokenizer.table(e2)) {
          e2 = e2.substring(r2.raw.length), t2.push(r2);
          continue;
        }
        if (r2 = this.tokenizer.lheading(e2)) {
          e2 = e2.substring(r2.raw.length), t2.push(r2);
          continue;
        }
        let o2 = e2;
        if (this.options.extensions?.startBlock) {
          let s = 1 / 0, u = e2.slice(1), a;
          this.options.extensions.startBlock.forEach((p2) => {
            a = p2.call({ lexer: this }, u), typeof a == "number" && a >= 0 && (s = Math.min(s, a));
          }), s < 1 / 0 && s >= 0 && (o2 = e2.substring(0, s + 1));
        }
        if (this.state.top && (r2 = this.tokenizer.paragraph(o2))) {
          let s = t2.at(-1);
          n && s?.type === "paragraph" ? (s.raw += (s.raw.endsWith(`
`) ? "" : `
`) + r2.raw, s.text += `
` + r2.text, this.inlineQueue.pop(), this.inlineQueue.at(-1).src = s.text) : t2.push(r2), n = o2.length !== e2.length, e2 = e2.substring(r2.raw.length);
          continue;
        }
        if (r2 = this.tokenizer.text(e2)) {
          e2 = e2.substring(r2.raw.length);
          let s = t2.at(-1);
          s?.type === "text" ? (s.raw += (s.raw.endsWith(`
`) ? "" : `
`) + r2.raw, s.text += `
` + r2.text, this.inlineQueue.pop(), this.inlineQueue.at(-1).src = s.text) : t2.push(r2);
          continue;
        }
        if (e2) {
          this.infiniteLoopError(e2.charCodeAt(0));
          break;
        }
      }
      return this.state.top = true, t2;
    }
    inline(e2, t2 = []) {
      return this.inlineQueue.push({ src: e2, tokens: t2 }), t2;
    }
    linkInText(e2) {
      if (!e2.includes("[")) return false;
      let t2 = this.tokenizer.rules.inline.link;
      for (let n of e2.matchAll(this.tokenizer.rules.inline.blockSkip)) if (t2.test(n[0]) && e2.charAt(n.index - 1) !== "!") return true;
      for (let n of e2.matchAll(this.tokenizer.rules.inline.reflinkSearch)) {
        let i = n[0], r2 = i.lastIndexOf("[");
        if (!(i.charAt(0) === "!" || !Object.hasOwn(this.tokens.links, D2(i.slice(r2 + 1, -1)))) && !(r2 > 1 && this.linkInText(i.slice(1, r2 - 1)))) return true;
      }
      return false;
    }
    inlineTokens(e2, t2 = []) {
      this.tokenizer.lexer = this;
      let n = e2;
      if (this.tokens.links && e2.includes("[")) {
        let s = this.tokenizer.rules.inline.reflinkSearch, u = (a) => {
          let p2 = a.lastIndexOf("[");
          if (!Object.hasOwn(this.tokens.links, D2(a.slice(p2 + 1, -1)))) return a;
          if (p2 > 1 && a.charAt(0) !== "!") {
            let c2 = a.slice(1, p2 - 1);
            if (this.linkInText(c2)) return "[" + c2.replace(s, u) + "][" + "a".repeat(a.length - p2 - 2) + "]";
          }
          return "[" + "a".repeat(a.length - 2) + "]";
        };
        n = n.replace(s, u);
      }
      n = n.replace(this.tokenizer.rules.inline.anyPunctuation, (s) => "+".repeat(s.length)), n = n.replace(this.tokenizer.rules.inline.blockSkip, (s, u, a) => {
        let p2 = a ? a.length : 0;
        return s.slice(0, p2) + "[" + "a".repeat(s.length - p2 - 2) + "]";
      }), n = this.options.hooks?.emStrongMask?.call({ lexer: this }, n) ?? n;
      let i = false, r2 = "", o2 = 1 / 0;
      for (; e2; ) {
        if (e2.length < o2) o2 = e2.length;
        else {
          this.infiniteLoopError(e2.charCodeAt(0));
          break;
        }
        i || (r2 = ""), i = false;
        let s;
        if (this.options.extensions?.inline?.some((a) => (s = a.call({ lexer: this }, e2, t2)) ? (e2 = e2.substring(s.raw.length), t2.push(s), true) : false)) continue;
        if (s = this.tokenizer.escape(e2)) {
          e2 = e2.substring(s.raw.length), t2.push(s);
          continue;
        }
        if (s = this.tokenizer.tag(e2)) {
          e2 = e2.substring(s.raw.length), t2.push(s);
          continue;
        }
        if (s = this.tokenizer.link(e2)) {
          e2 = e2.substring(s.raw.length), t2.push(s);
          continue;
        }
        if (s = this.tokenizer.reflink(e2, this.tokens.links)) {
          e2 = e2.substring(s.raw.length);
          let a = t2.at(-1);
          s.type === "text" && a?.type === "text" ? (a.raw += s.raw, a.text += s.text) : t2.push(s);
          continue;
        }
        if (s = this.tokenizer.emStrong(e2, n, r2)) {
          e2 = e2.substring(s.raw.length), t2.push(s);
          continue;
        }
        if (s = this.tokenizer.codespan(e2)) {
          e2 = e2.substring(s.raw.length), t2.push(s);
          continue;
        }
        if (s = this.tokenizer.br(e2)) {
          e2 = e2.substring(s.raw.length), t2.push(s);
          continue;
        }
        if (s = this.tokenizer.del(e2, n, r2)) {
          e2 = e2.substring(s.raw.length), t2.push(s);
          continue;
        }
        if (s = this.tokenizer.autolink(e2)) {
          e2 = e2.substring(s.raw.length), t2.push(s);
          continue;
        }
        if (!this.state.inLink && (s = this.tokenizer.url(e2))) {
          e2 = e2.substring(s.raw.length), t2.push(s);
          continue;
        }
        let u = e2;
        if (this.options.extensions?.startInline) {
          let a = 1 / 0, p2 = e2.slice(1), c2;
          this.options.extensions.startInline.forEach((h) => {
            c2 = h.call({ lexer: this }, p2), typeof c2 == "number" && c2 >= 0 && (a = Math.min(a, c2));
          }), a < 1 / 0 && a >= 0 && (u = e2.substring(0, a + 1));
        }
        if (s = this.tokenizer.inlineText(u)) {
          e2 = e2.substring(s.raw.length), s.raw.slice(-1) !== "_" && (r2 = s.raw.slice(-1)), i = true;
          let a = t2.at(-1);
          a?.type === "text" ? (a.raw += s.raw, a.text += s.text) : t2.push(s);
          continue;
        }
        if (e2) {
          this.infiniteLoopError(e2.charCodeAt(0));
          break;
        }
      }
      return t2;
    }
    infiniteLoopError(e2) {
      let t2 = "Infinite loop on byte: " + e2;
      if (this.options.silent) console.error(t2);
      else throw new Error(t2);
    }
  };
  var P = class {
    options;
    parser;
    constructor(e2) {
      this.options = e2 || T;
    }
    space(e2) {
      return "";
    }
    code({ text: e2, lang: t2, escaped: n }) {
      let i = (t2 || "").match(m2.notSpaceStart)?.[0], r2 = e2 ? e2.replace(m2.endingNewline, "") + `
` : "";
      return i ? '<pre><code class="language-' + R2(i) + '">' + (n ? r2 : R2(r2, true)) + `</code></pre>
` : "<pre><code>" + (n ? r2 : R2(r2, true)) + `</code></pre>
`;
    }
    blockquote({ tokens: e2 }) {
      return `<blockquote>
${this.parser.parse(e2)}</blockquote>
`;
    }
    html({ text: e2 }) {
      return e2;
    }
    def(e2) {
      return "";
    }
    heading({ tokens: e2, depth: t2 }) {
      return `<h${t2}>${this.parser.parseInline(e2)}</h${t2}>
`;
    }
    hr(e2) {
      return `<hr>
`;
    }
    list(e2) {
      let t2 = e2.ordered, n = e2.start, i = "";
      for (let s = 0; s < e2.items.length; s++) {
        let u = e2.items[s];
        i += this.listitem(u);
      }
      let r2 = t2 ? "ol" : "ul", o2 = t2 && n !== 1 ? ' start="' + n + '"' : "";
      return "<" + r2 + o2 + `>
` + i + "</" + r2 + `>
`;
    }
    listitem(e2) {
      return `<li>${this.parser.parse(e2.tokens)}</li>
`;
    }
    checkbox({ checked: e2 }) {
      return "<input " + (e2 ? 'checked="" ' : "") + 'disabled="" type="checkbox"> ';
    }
    paragraph({ tokens: e2 }) {
      return `<p>${this.parser.parseInline(e2)}</p>
`;
    }
    table(e2) {
      let t2 = "", n = "";
      for (let r2 = 0; r2 < e2.header.length; r2++) n += this.tablecell(e2.header[r2]);
      t2 += this.tablerow({ text: n });
      let i = "";
      for (let r2 = 0; r2 < e2.rows.length; r2++) {
        let o2 = e2.rows[r2];
        n = "";
        for (let s = 0; s < o2.length; s++) n += this.tablecell(o2[s]);
        i += this.tablerow({ text: n });
      }
      return i && (i = `<tbody>${i}</tbody>`), `<table>
<thead>
` + t2 + `</thead>
` + i + `</table>
`;
    }
    tablerow({ text: e2 }) {
      return `<tr>
${e2}</tr>
`;
    }
    tablecell(e2) {
      let t2 = this.parser.parseInline(e2.tokens), n = e2.header ? "th" : "td";
      return (e2.align ? `<${n} align="${e2.align}">` : `<${n}>`) + t2 + `</${n}>
`;
    }
    strong({ tokens: e2 }) {
      return `<strong>${this.parser.parseInline(e2)}</strong>`;
    }
    em({ tokens: e2 }) {
      return `<em>${this.parser.parseInline(e2)}</em>`;
    }
    codespan({ text: e2 }) {
      return `<code>${R2(e2, true)}</code>`;
    }
    br(e2) {
      return "<br>";
    }
    del({ tokens: e2 }) {
      return `<del>${this.parser.parseInline(e2)}</del>`;
    }
    link({ href: e2, title: t2, text: n, tokens: i, autolink: r2 }) {
      let o2 = r2 ? R2(n, true) : this.parser.parseInline(i), s = ee(e2);
      if (s === null) return o2;
      e2 = R2(s, r2);
      let u = '<a href="' + e2 + '"';
      return t2 && (u += ' title="' + R2(t2) + '"'), u += ">" + o2 + "</a>", u;
    }
    image({ href: e2, title: t2, text: n, tokens: i }) {
      i && (n = this.parser.parseInline(i, this.parser.textRenderer));
      let r2 = ee(e2);
      if (r2 === null) return R2(n);
      e2 = r2;
      let o2 = `<img src="${R2(e2)}" alt="${R2(n)}"`;
      return t2 && (o2 += ` title="${R2(t2)}"`), o2 += ">", o2;
    }
    text(e2) {
      return "tokens" in e2 && e2.tokens ? this.parser.parseInline(e2.tokens) : "escaped" in e2 && e2.escaped ? e2.text : R2(e2.text);
    }
  };
  var L = class {
    strong({ text: e2 }) {
      return e2;
    }
    em({ text: e2 }) {
      return e2;
    }
    codespan({ text: e2 }) {
      return e2;
    }
    del({ text: e2 }) {
      return e2;
    }
    html({ text: e2 }) {
      return e2;
    }
    text({ text: e2 }) {
      return e2;
    }
    link({ text: e2 }) {
      return "" + e2;
    }
    image({ text: e2 }) {
      return "" + e2;
    }
    br() {
      return "";
    }
    checkbox({ raw: e2 }) {
      return e2;
    }
  };
  var b = class l3 {
    options;
    renderer;
    textRenderer;
    constructor(e2) {
      this.options = e2 || T, this.options.renderer = this.options.renderer || new P(), this.renderer = this.options.renderer, this.renderer.options = this.options, this.renderer.parser = this, this.textRenderer = new L();
    }
    static parse(e2, t2) {
      return new l3(t2).parse(e2);
    }
    static parseInline(e2, t2) {
      return new l3(t2).parseInline(e2);
    }
    parse(e2) {
      this.renderer.parser = this;
      let t2 = "";
      for (let n = 0; n < e2.length; n++) {
        let i = e2[n];
        if (this.options.extensions?.renderers?.[i.type]) {
          let o2 = i, s = this.options.extensions.renderers[o2.type].call({ parser: this }, o2);
          if (s !== false || !["space", "hr", "heading", "code", "table", "blockquote", "list", "checkbox", "html", "def", "paragraph", "text"].includes(o2.type)) {
            t2 += s || "";
            continue;
          }
        }
        let r2 = i;
        switch (r2.type) {
          case "space": {
            t2 += this.renderer.space(r2);
            break;
          }
          case "hr": {
            t2 += this.renderer.hr(r2);
            break;
          }
          case "heading": {
            t2 += this.renderer.heading(r2);
            break;
          }
          case "code": {
            t2 += this.renderer.code(r2);
            break;
          }
          case "table": {
            t2 += this.renderer.table(r2);
            break;
          }
          case "blockquote": {
            t2 += this.renderer.blockquote(r2);
            break;
          }
          case "list": {
            t2 += this.renderer.list(r2);
            break;
          }
          case "checkbox": {
            t2 += this.renderer.checkbox(r2);
            break;
          }
          case "html": {
            t2 += this.renderer.html(r2);
            break;
          }
          case "def": {
            t2 += this.renderer.def(r2);
            break;
          }
          case "paragraph": {
            t2 += this.renderer.paragraph(r2);
            break;
          }
          case "text": {
            t2 += this.renderer.text(r2);
            break;
          }
          default: {
            let o2 = 'Token with "' + r2.type + '" type was not found.';
            if (this.options.silent) return console.error(o2), "";
            throw new Error(o2);
          }
        }
      }
      return t2;
    }
    parseInline(e2, t2 = this.renderer) {
      this.renderer.parser = this;
      let n = "";
      for (let i = 0; i < e2.length; i++) {
        let r2 = e2[i];
        if (this.options.extensions?.renderers?.[r2.type]) {
          let s = this.options.extensions.renderers[r2.type].call({ parser: this }, r2);
          if (s !== false || !["escape", "html", "link", "image", "checkbox", "strong", "em", "codespan", "br", "del", "text"].includes(r2.type)) {
            n += s || "";
            continue;
          }
        }
        let o2 = r2;
        switch (o2.type) {
          case "escape": {
            n += t2.text(o2);
            break;
          }
          case "html": {
            n += t2.html(o2);
            break;
          }
          case "link": {
            n += t2.link(o2);
            break;
          }
          case "image": {
            n += t2.image(o2);
            break;
          }
          case "checkbox": {
            n += t2.checkbox(o2);
            break;
          }
          case "strong": {
            n += t2.strong(o2);
            break;
          }
          case "em": {
            n += t2.em(o2);
            break;
          }
          case "codespan": {
            n += t2.codespan(o2);
            break;
          }
          case "br": {
            n += t2.br(o2);
            break;
          }
          case "del": {
            n += t2.del(o2);
            break;
          }
          case "text": {
            n += t2.text(o2);
            break;
          }
          default: {
            let s = 'Token with "' + o2.type + '" type was not found.';
            if (this.options.silent) return console.error(s), "";
            throw new Error(s);
          }
        }
      }
      return n;
    }
  };
  var S = class {
    options;
    block;
    constructor(e2) {
      this.options = e2 || T;
    }
    static passThroughHooks = /* @__PURE__ */ new Set(["preprocess", "postprocess", "processAllTokens", "emStrongMask"]);
    static passThroughHooksRespectAsync = /* @__PURE__ */ new Set(["preprocess", "postprocess", "processAllTokens"]);
    preprocess(e2) {
      return e2;
    }
    postprocess(e2) {
      return e2;
    }
    processAllTokens(e2) {
      return e2;
    }
    emStrongMask(e2) {
      return e2;
    }
    provideLexer(e2 = this.block) {
      return e2 ? x2.lex : x2.lexInline;
    }
    provideParser(e2 = this.block) {
      return e2 ? b.parse : b.parseInline;
    }
  };
  var Q = class {
    defaults = A2();
    options = this.setOptions;
    parse = this.parseMarkdown(true);
    parseInline = this.parseMarkdown(false);
    Parser = b;
    Renderer = P;
    TextRenderer = L;
    Lexer = x2;
    Tokenizer = y2;
    Hooks = S;
    constructor(...e2) {
      this.use(...e2);
    }
    walkTokens(e2, t2) {
      let n = [];
      for (let i of e2) switch (n = n.concat(t2.call(this, i)), i.type) {
        case "table": {
          let r2 = i;
          for (let o2 of r2.header) n = n.concat(this.walkTokens(o2.tokens, t2));
          for (let o2 of r2.rows) for (let s of o2) n = n.concat(this.walkTokens(s.tokens, t2));
          break;
        }
        case "list": {
          let r2 = i;
          n = n.concat(this.walkTokens(r2.items, t2));
          break;
        }
        default: {
          let r2 = i;
          this.defaults.extensions?.childTokens?.[r2.type] ? this.defaults.extensions.childTokens[r2.type].forEach((o2) => {
            let s = r2[o2].flat(1 / 0);
            n = n.concat(this.walkTokens(s, t2));
          }) : r2.tokens && (n = n.concat(this.walkTokens(r2.tokens, t2)));
        }
      }
      return n;
    }
    use(...e2) {
      let t2 = this.defaults.extensions || { renderers: {}, childTokens: {} };
      return e2.forEach((n) => {
        let i = { ...n };
        if (i.async = this.defaults.async || i.async || false, n.extensions && (n.extensions.forEach((r2) => {
          if (!r2.name) throw new Error("extension name required");
          if ("renderer" in r2) {
            let o2 = t2.renderers[r2.name];
            o2 ? t2.renderers[r2.name] = function(...s) {
              let u = r2.renderer.apply(this, s);
              return u === false && (u = o2.apply(this, s)), u;
            } : t2.renderers[r2.name] = r2.renderer;
          }
          if ("tokenizer" in r2) {
            if (!r2.level || r2.level !== "block" && r2.level !== "inline") throw new Error("extension level must be 'block' or 'inline'");
            let o2 = t2[r2.level];
            o2 ? o2.unshift(r2.tokenizer) : t2[r2.level] = [r2.tokenizer], r2.start && (r2.level === "block" ? t2.startBlock ? t2.startBlock.push(r2.start) : t2.startBlock = [r2.start] : r2.level === "inline" && (t2.startInline ? t2.startInline.push(r2.start) : t2.startInline = [r2.start]));
          }
          "childTokens" in r2 && r2.childTokens && (t2.childTokens[r2.name] = r2.childTokens);
        }), i.extensions = t2), n.renderer) {
          let r2 = this.defaults.renderer || new P(this.defaults);
          for (let o2 in n.renderer) {
            if (!(o2 in r2)) throw new Error(`renderer '${o2}' does not exist`);
            if (["options", "parser"].includes(o2)) continue;
            let s = o2, u = n.renderer[s], a = r2[s];
            r2[s] = (...p2) => {
              let c2 = u.apply(r2, p2);
              return c2 === false && (c2 = a.apply(r2, p2)), c2 || "";
            };
          }
          i.renderer = r2;
        }
        if (n.tokenizer) {
          let r2 = this.defaults.tokenizer || new y2(this.defaults);
          for (let o2 in n.tokenizer) {
            if (!(o2 in r2)) throw new Error(`tokenizer '${o2}' does not exist`);
            if (["options", "rules", "lexer"].includes(o2)) continue;
            let s = o2, u = n.tokenizer[s], a = r2[s];
            r2[s] = (...p2) => {
              let c2 = u.apply(r2, p2);
              return c2 === false && (c2 = a.apply(r2, p2)), c2;
            };
          }
          i.tokenizer = r2;
        }
        if (n.hooks) {
          let r2 = this.defaults.hooks || new S();
          for (let o2 in n.hooks) {
            if (!(o2 in r2)) throw new Error(`hook '${o2}' does not exist`);
            if (["options", "block"].includes(o2)) continue;
            let s = o2, u = n.hooks[s], a = r2[s];
            S.passThroughHooks.has(o2) ? r2[s] = (p2) => {
              if (this.defaults.async && S.passThroughHooksRespectAsync.has(o2)) return (async () => {
                let h = await u.call(r2, p2);
                return a.call(r2, h);
              })();
              let c2 = u.call(r2, p2);
              return a.call(r2, c2);
            } : r2[s] = (...p2) => {
              if (this.defaults.async) return (async () => {
                let h = await u.apply(r2, p2);
                return h === false && (h = await a.apply(r2, p2)), h;
              })();
              let c2 = u.apply(r2, p2);
              return c2 === false && (c2 = a.apply(r2, p2)), c2;
            };
          }
          i.hooks = r2;
        }
        if (n.walkTokens) {
          let r2 = this.defaults.walkTokens, o2 = n.walkTokens;
          i.walkTokens = function(s) {
            let u = [];
            return u.push(o2.call(this, s)), r2 && (u = u.concat(r2.call(this, s))), u;
          };
        }
        this.defaults = { ...this.defaults, ...i };
      }), this;
    }
    setOptions(e2) {
      return this.defaults = { ...this.defaults, ...e2 }, this;
    }
    lexer(e2, t2) {
      return x2.lex(e2, t2 ?? this.defaults);
    }
    parser(e2, t2) {
      return b.parse(e2, t2 ?? this.defaults);
    }
    parseMarkdown(e2) {
      return (n, i) => {
        let r2 = { ...i }, o2 = { ...this.defaults, ...r2 }, s = this.onError(!!o2.silent, !!o2.async);
        if (this.defaults.async === true && r2.async === false) return s(new Error("marked(): The async option was set to true by an extension. Remove async: false from the parse options object to return a Promise."));
        if (typeof n > "u" || n === null) return s(new Error("marked(): input parameter is undefined or null"));
        if (typeof n != "string") return s(new Error("marked(): input parameter is of type " + Object.prototype.toString.call(n) + ", string expected"));
        if (o2.hooks && (o2.hooks.options = o2, o2.hooks.block = e2), o2.async) return (async () => {
          let u = o2.hooks ? await o2.hooks.preprocess(n) : n, p2 = await (o2.hooks ? await o2.hooks.provideLexer(e2) : e2 ? x2.lex : x2.lexInline)(u, o2), c2 = o2.hooks ? await o2.hooks.processAllTokens(p2) : p2;
          o2.walkTokens && await Promise.all(this.walkTokens(c2, o2.walkTokens));
          let k2 = await (o2.hooks ? await o2.hooks.provideParser(e2) : e2 ? b.parse : b.parseInline)(c2, o2);
          return o2.hooks ? await o2.hooks.postprocess(k2) : k2;
        })().catch(s);
        try {
          o2.hooks && (n = o2.hooks.preprocess(n));
          let a = (o2.hooks ? o2.hooks.provideLexer(e2) : e2 ? x2.lex : x2.lexInline)(n, o2);
          o2.hooks && (a = o2.hooks.processAllTokens(a)), o2.walkTokens && this.walkTokens(a, o2.walkTokens);
          let c2 = (o2.hooks ? o2.hooks.provideParser(e2) : e2 ? b.parse : b.parseInline)(a, o2);
          return o2.hooks && (c2 = o2.hooks.postprocess(c2)), c2;
        } catch (u) {
          return s(u);
        }
      };
    }
    onError(e2, t2) {
      return (n) => {
        if (n.message += `
Please report this to https://github.com/markedjs/marked.`, e2) {
          let i = "<p>An error occurred:</p><pre>" + R2(n.message + "", true) + "</pre>";
          return t2 ? Promise.resolve(i) : i;
        }
        if (t2) return Promise.reject(n);
        throw n;
      };
    }
  };
  var M2 = new Q();
  function f(l4, e2) {
    return M2.parse(l4, e2);
  }
  f.options = f.setOptions = function(l4) {
    return M2.setOptions(l4), f.defaults = M2.defaults, U(f.defaults), f;
  };
  f.getDefaults = A2;
  f.defaults = T;
  function bt(...l4) {
    return M2.use(...l4), f.defaults = M2.defaults, U(f.defaults), f;
  }
  f.use = bt;
  f.walkTokens = function(l4, e2) {
    return M2.walkTokens(l4, e2);
  };
  f.parseInline = M2.parseInline;
  f.Parser = b;
  f.parser = b.parse;
  f.Renderer = P;
  f.TextRenderer = L;
  f.Lexer = x2;
  f.lexer = x2.lex;
  f.Tokenizer = y2;
  f.Hooks = S;
  f.parse = f;
  var un = f.options;
  var pn = f.setOptions;
  var cn = f.walkTokens;
  var hn = f.parseInline;
  var kn = b.parse;
  var gn = x2.lex;

  // packages/blocks/build-module/api/raw-handling/markdown-converter.mjs
  function escapeBodyText(value) {
    return value.replace(/&(?!#?\w+;)/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }
  var converter = new Q({
    gfm: true,
    breaks: true,
    renderer: {
      // Match showdown's `omitExtraWLInCodeBlocks`: marked appends `\n`
      // before `</code>`, which leaks into the Code block's content as a
      // trailing blank line.
      code({ text: text2, lang }) {
        const language = (lang || "").match(/^\S*/)?.[0];
        const cls = language ? ` class="${language} language-${language}"` : "";
        return `<pre><code${cls}>${escapeBodyText(
          text2
        )}</code></pre>`;
      },
      text(token) {
        if ("tokens" in token && token.tokens || "escaped" in token && token.escaped) {
          return false;
        }
        return escapeBodyText(token.text);
      }
    }
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
  function escapeSingleLineOrderedListMarker(text2) {
    if (text2.includes("\n")) {
      return text2;
    }
    return text2.replace(/^(\d+)\.(\s)/, "$1\\.$2");
  }
  var correctors = [
    escapeSingleLineOrderedListMarker,
    bulletsToAsterisks,
    slackMarkdownVariantCorrector
  ];
  function markdownConverter(text2) {
    return converter.parse(
      correctors.reduce(
        (current, corrector) => corrector(current),
        text2
      ),
      { async: false }
    );
  }

  // packages/blocks/build-module/api/raw-handling/iframe-remover.mjs
  function iframeRemover(iframe) {
    if (iframe.nodeName === "IFRAME") {
      const node = iframe;
      const text2 = node.ownerDocument.createTextNode(node.src);
      node.parentNode.replaceChild(text2, node);
    }
  }

  // packages/blocks/build-module/api/raw-handling/google-docs-uid-remover.mjs
  var import_dom10 = __toESM(require_dom(), 1);
  function googleDocsUIdRemover(node) {
    const el = node;
    if (!el.id || el.id.indexOf("docs-internal-guid-") !== 0) {
      return;
    }
    if (el.tagName === "B") {
      (0, import_dom10.unwrap)(node);
    } else {
      el.removeAttribute("id");
    }
  }

  // packages/blocks/build-module/api/raw-handling/html-formatting-remover.mjs
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
    const textNode = node;
    let newData = textNode.data.replace(/[ \r\n\t]+/g, " ");
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
      textNode.data = newData;
    }
  }

  // packages/blocks/build-module/api/raw-handling/format-space-corrector.mjs
  var import_dom11 = __toESM(require_dom(), 1);
  function moveEdgeSpace(node, doc, isLeading) {
    const child = isLeading ? node.firstChild : node.lastChild;
    if (!child || child.nodeType !== node.TEXT_NODE) {
      return;
    }
    const text2 = child;
    const edgeIndex = isLeading ? 0 : text2.data.length - 1;
    if (text2.data[edgeIndex] !== " ") {
      return;
    }
    text2.data = isLeading ? text2.data.slice(1) : text2.data.slice(0, -1);
    if (!text2.data) {
      node.removeChild(text2);
    }
    node.parentNode.insertBefore(
      doc.createTextNode(" "),
      isLeading ? node : node.nextSibling
    );
  }
  function formatSpaceCorrector(node, doc) {
    if (node.nodeType !== node.ELEMENT_NODE || !(0, import_dom11.isPhrasingContent)(node)) {
      return;
    }
    moveEdgeSpace(node, doc, true);
    moveEdgeSpace(node, doc, false);
  }

  // packages/blocks/build-module/api/raw-handling/br-remover.mjs
  function brRemover(node) {
    if (node.nodeName !== "BR") {
      return;
    }
    if (getSibling(node, "next")) {
      return;
    }
    node.parentNode.removeChild(node);
  }

  // packages/blocks/build-module/api/raw-handling/empty-paragraph-remover.mjs
  function emptyParagraphRemover(node) {
    if (node.nodeName !== "P") {
      return;
    }
    if (node.hasChildNodes()) {
      return;
    }
    node.parentNode.removeChild(node);
  }

  // packages/blocks/build-module/api/raw-handling/slack-paragraph-corrector.mjs
  function slackParagraphCorrector(node) {
    if (node.nodeName !== "SPAN") {
      return;
    }
    if (node.getAttribute("data-stringify-type") !== "paragraph-break") {
      return;
    }
    const parentNode = node.parentNode;
    parentNode.insertBefore(node.ownerDocument.createElement("br"), node);
    parentNode.insertBefore(node.ownerDocument.createElement("br"), node);
    parentNode.removeChild(node);
  }

  // packages/blocks/build-module/api/raw-handling/latex-to-math.mjs
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
      (t2) => t2.includes("^") && !t2.startsWith("^"),
      (t2) => ["=", "+", "-", "/", "*"].some(
        (operator) => t2.includes(operator)
      ),
      (t2) => /\\[a-zA-Z]+/g.test(t2)
    ];
    if (softClues.filter((clue) => clue(text2)).length >= 2) {
      return true;
    }
    return false;
  }

  // packages/blocks/build-module/api/raw-handling/heading-transformer.mjs
  function headingTransformer(node) {
    if (node.nodeType !== node.ELEMENT_NODE) {
      return;
    }
    const element = node;
    if (element.tagName === "P" && element.getAttribute("role") === "heading" && element.hasAttribute("aria-level")) {
      const level = parseInt(element.getAttribute("aria-level"), 10);
      if (level >= 1 && level <= 6) {
        const headingTag = `H${level}`;
        const newHeading = element.ownerDocument.createElement(headingTag);
        Array.from(element.attributes).forEach((attr2) => {
          if (attr2.name !== "role" && attr2.name !== "aria-level") {
            newHeading.setAttribute(attr2.name, attr2.value);
          }
        });
        while (element.firstChild) {
          newHeading.appendChild(element.firstChild);
        }
        element.parentNode.replaceChild(newHeading, element);
      }
    }
  }

  // packages/blocks/build-module/api/raw-handling/paste-handler.mjs
  var log = (...args) => window?.console?.log?.(...args);
  function filterInlineHTML(HTML) {
    HTML = deepFilterHTML(HTML, [
      headRemover,
      googleDocsUIdRemover,
      msListIgnore,
      phrasingContentReducer,
      commentRemover
    ]);
    HTML = (0, import_dom12.removeInvalidHTML)(
      HTML,
      (0, import_dom12.getPhrasingContentSchema)("paste"),
      true
    );
    HTML = deepFilterHTML(HTML, [
      htmlFormattingRemover,
      formatSpaceCorrector,
      brRemover
    ]);
    log("Processed inline HTML:\n\n", HTML);
    return HTML;
  }
  function pasteHandler({
    HTML = "",
    plainText = "",
    mode = "AUTO",
    tagName
  }) {
    log("Received HTML (pasteHandler):\n\n", HTML);
    log("Received plain text (pasteHandler):\n\n", plainText);
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
    HTML = HTML.normalize();
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
    const phrasingContentSchema = (0, import_dom12.getPhrasingContentSchema)("paste");
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
      piece = (0, import_dom12.removeInvalidHTML)(piece, schema, false);
      piece = normaliseBlocks(piece);
      piece = deepFilterHTML(
        piece,
        [
          htmlFormattingRemover,
          formatSpaceCorrector,
          brRemover,
          emptyParagraphRemover
        ],
        blockContentSchema
      );
      log("Processed HTML piece:\n\n", piece);
      return htmlToBlocks(piece, pasteHandler);
    }).flat().filter(Boolean);
    if (mode === "AUTO" && blocks.length === 1 && hasBlockSupport(blocks[0].name, "__unstablePasteTextInline", false)) {
      const trimRegex = /^[\n]+|[\n]+$/g;
      const trimmedPlainText = plainText.replace(trimRegex, "");
      if (trimmedPlainText !== "" && trimmedPlainText.indexOf("\n") === -1) {
        return (0, import_dom12.removeInvalidHTML)(
          getBlockInnerHTML(blocks[0]),
          phrasingContentSchema,
          false
        ).replace(trimRegex, "");
      }
    }
    return blocks;
  }

  // packages/blocks/build-module/api/raw-handling/index.mjs
  function deprecatedGetPhrasingContentSchema(context) {
    (0, import_deprecated11.default)("wp.blocks.getPhrasingContentSchema", {
      since: "5.6",
      alternative: "wp.dom.getPhrasingContentSchema"
    });
    return (0, import_dom13.getPhrasingContentSchema)(context);
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

  // packages/blocks/build-module/api/categories.mjs
  var import_data6 = __toESM(require_data(), 1);
  function getCategories2() {
    return (0, import_data6.select)(store).getCategories();
  }
  function setCategories2(categories2) {
    (0, import_data6.dispatch)(store).setCategories(categories2);
  }
  function updateCategory2(slug, category) {
    (0, import_data6.dispatch)(store).updateCategory(slug, category);
  }

  // packages/blocks/build-module/api/templates.mjs
  var import_element4 = __toESM(require_element(), 1);
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
      return (0, import_element4.renderToString)(value);
    }
    if (isQueryAttribute(definition) && value) {
      return value.map(
        (subValues) => {
          return normalizeAttributes(definition.query, subValues);
        }
      );
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

  // packages/blocks/build-module/api/index.mjs
  var editableRootKey = /* @__PURE__ */ Symbol("editableRoot");
  var privateApis = {};
  lock(privateApis, {
    isContentBlock,
    editableRootKey,
    parseRawBlock
  });

  // packages/blocks/build-module/deprecated.mjs
  var import_deprecated12 = __toESM(require_deprecated(), 1);
  function withBlockContentContext(OriginalComponent) {
    (0, import_deprecated12.default)("wp.blocks.withBlockContentContext", {
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

is-plain-object/dist/is-plain-object.mjs:
  (*!
   * is-plain-object <https://github.com/jonschlinkert/is-plain-object>
   *
   * Copyright (c) 2014-2017, Jon Schlinkert.
   * Released under the MIT License.
   *)
*/
(window.wp ||= {}).blocks = wp.blocks;
})();
