"use strict";
var wp;
(wp ||= {}).url = (() => {
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
      function matcher(match) {
        return characterMap[match];
      }
      var removeAccents2 = function(string) {
        return string.replace(allAccents, matcher);
      };
      var hasAccents = function(string) {
        return !!string.match(firstAccent);
      };
      module.exports = removeAccents2;
      module.exports.has = hasAccents;
      module.exports.remove = removeAccents2;
    }
  });

  // packages/url/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    addQueryArgs: () => addQueryArgs,
    buildQueryString: () => buildQueryString,
    cleanForSlug: () => cleanForSlug,
    filterURLForDisplay: () => filterURLForDisplay,
    getAuthority: () => getAuthority,
    getFilename: () => getFilename,
    getFragment: () => getFragment,
    getPath: () => getPath,
    getPathAndQueryString: () => getPathAndQueryString,
    getProtocol: () => getProtocol,
    getQueryArg: () => getQueryArg,
    getQueryArgs: () => getQueryArgs,
    getQueryString: () => getQueryString,
    hasQueryArg: () => hasQueryArg,
    isEmail: () => isEmail,
    isPhoneNumber: () => isPhoneNumber,
    isURL: () => isURL,
    isValidAuthority: () => isValidAuthority,
    isValidFragment: () => isValidFragment,
    isValidPath: () => isValidPath,
    isValidProtocol: () => isValidProtocol,
    isValidQueryString: () => isValidQueryString,
    normalizePath: () => normalizePath,
    prependHTTP: () => prependHTTP,
    prependHTTPS: () => prependHTTPS,
    removeQueryArgs: () => removeQueryArgs,
    safeDecodeURI: () => safeDecodeURI,
    safeDecodeURIComponent: () => safeDecodeURIComponent
  });

  // packages/url/build-module/is-url.js
  function isURL(url) {
    try {
      new URL(url);
      return true;
    } catch {
      return false;
    }
  }

  // packages/url/build-module/is-email.js
  var EMAIL_REGEXP = /^(mailto:)?[a-z0-9._%+-]+@[a-z0-9][a-z0-9.-]*\.[a-z]{2,63}$/i;
  function isEmail(email) {
    return EMAIL_REGEXP.test(email);
  }

  // packages/url/build-module/is-phone-number.js
  var PHONE_REGEXP = /^(tel:)?(\+)?\d{6,15}$/;
  function isPhoneNumber(phoneNumber) {
    phoneNumber = phoneNumber.replace(/[-.() ]/g, "");
    return PHONE_REGEXP.test(phoneNumber);
  }

  // packages/url/build-module/get-protocol.js
  function getProtocol(url) {
    const matches = /^([^\s:]+:)/.exec(url);
    if (matches) {
      return matches[1];
    }
  }

  // packages/url/build-module/is-valid-protocol.js
  function isValidProtocol(protocol) {
    if (!protocol) {
      return false;
    }
    return /^[a-z\-.\+]+[0-9]*:$/i.test(protocol);
  }

  // packages/url/build-module/get-authority.js
  function getAuthority(url) {
    const matches = /^[^\/\s:]+:(?:\/\/)?\/?([^\/\s#?]+)[\/#?]{0,1}\S*$/.exec(
      url
    );
    if (matches) {
      return matches[1];
    }
  }

  // packages/url/build-module/is-valid-authority.js
  function isValidAuthority(authority) {
    if (!authority) {
      return false;
    }
    return /^[^\s#?]+$/.test(authority);
  }

  // packages/url/build-module/get-path.js
  function getPath(url) {
    const matches = /^[^\/\s:]+:(?:\/\/)?[^\/\s#?]+[\/]([^\s#?]+)[#?]{0,1}\S*$/.exec(url);
    if (matches) {
      return matches[1];
    }
  }

  // packages/url/build-module/is-valid-path.js
  function isValidPath(path) {
    if (!path) {
      return false;
    }
    return /^[^\s#?]+$/.test(path);
  }

  // packages/url/build-module/get-query-string.js
  function getQueryString(url) {
    let query;
    try {
      query = new URL(url, "http://example.com").search.substring(1);
    } catch (error) {
    }
    if (query) {
      return query;
    }
  }

  // packages/url/build-module/build-query-string.js
  function buildQueryString(data) {
    let string = "";
    const stack = Object.entries(data);
    let pair;
    while (pair = stack.shift()) {
      let [key, value] = pair;
      const hasNestedData = Array.isArray(value) || value && value.constructor === Object;
      if (hasNestedData) {
        const valuePairs = Object.entries(value).reverse();
        for (const [member, memberValue] of valuePairs) {
          stack.unshift([`${key}[${member}]`, memberValue]);
        }
      } else if (value !== void 0) {
        if (value === null) {
          value = "";
        }
        string += "&" + [key, String(value)].map(encodeURIComponent).join("=");
      }
    }
    return string.substr(1);
  }

  // packages/url/build-module/is-valid-query-string.js
  function isValidQueryString(queryString) {
    if (!queryString) {
      return false;
    }
    return /^[^\s#?\/]+$/.test(queryString);
  }

  // packages/url/build-module/get-path-and-query-string.js
  function getPathAndQueryString(url) {
    const path = getPath(url);
    const queryString = getQueryString(url);
    let value = "/";
    if (path) {
      value += path;
    }
    if (queryString) {
      value += `?${queryString}`;
    }
    return value;
  }

  // packages/url/build-module/get-fragment.js
  function getFragment(url) {
    const matches = /^\S+?(#[^\s\?]*)/.exec(url);
    if (matches) {
      return matches[1];
    }
  }

  // packages/url/build-module/is-valid-fragment.js
  function isValidFragment(fragment) {
    if (!fragment) {
      return false;
    }
    return /^#[^\s#?\/]*$/.test(fragment);
  }

  // packages/url/build-module/safe-decode-uri-component.js
  function safeDecodeURIComponent(uriComponent) {
    try {
      return decodeURIComponent(uriComponent);
    } catch (uriComponentError) {
      return uriComponent;
    }
  }

  // packages/url/build-module/get-query-args.js
  function setPath(object, path, value) {
    const length = path.length;
    const lastIndex = length - 1;
    for (let i = 0; i < length; i++) {
      let key = path[i];
      if (!key && Array.isArray(object)) {
        key = object.length.toString();
      }
      key = ["__proto__", "constructor", "prototype"].includes(key) ? key.toUpperCase() : key;
      const isNextKeyArrayIndex = !isNaN(Number(path[i + 1]));
      object[key] = i === lastIndex ? (
        // If at end of path, assign the intended value.
        value
      ) : (
        // Otherwise, advance to the next object in the path, creating
        // it if it does not yet exist.
        object[key] || (isNextKeyArrayIndex ? [] : {})
      );
      if (Array.isArray(object[key]) && !isNextKeyArrayIndex) {
        object[key] = { ...object[key] };
      }
      object = object[key];
    }
  }
  function getQueryArgs(url) {
    return (getQueryString(url) || "").replace(/\+/g, "%20").split("&").reduce((accumulator, keyValue) => {
      const [key, value = ""] = keyValue.split("=").filter(Boolean).map(safeDecodeURIComponent);
      if (key) {
        const segments = key.replace(/\]/g, "").split("[");
        setPath(accumulator, segments, value);
      }
      return accumulator;
    }, /* @__PURE__ */ Object.create(null));
  }

  // packages/url/build-module/add-query-args.js
  function addQueryArgs(url = "", args) {
    if (!args || !Object.keys(args).length) {
      return url;
    }
    const fragment = getFragment(url) || "";
    let baseUrl = url.replace(fragment, "");
    const queryStringIndex = url.indexOf("?");
    if (queryStringIndex !== -1) {
      args = Object.assign(getQueryArgs(url), args);
      baseUrl = baseUrl.substr(0, queryStringIndex);
    }
    return baseUrl + "?" + buildQueryString(args) + fragment;
  }

  // packages/url/build-module/get-query-arg.js
  function getQueryArg(url, arg) {
    return getQueryArgs(url)[arg];
  }

  // packages/url/build-module/has-query-arg.js
  function hasQueryArg(url, arg) {
    return getQueryArg(url, arg) !== void 0;
  }

  // packages/url/build-module/remove-query-args.js
  function removeQueryArgs(url, ...args) {
    const fragment = url.replace(/^[^#]*/, "");
    url = url.replace(/#.*/, "");
    const queryStringIndex = url.indexOf("?");
    if (queryStringIndex === -1) {
      return url + fragment;
    }
    const query = getQueryArgs(url);
    const baseURL = url.substr(0, queryStringIndex);
    args.forEach((arg) => delete query[arg]);
    const queryString = buildQueryString(query);
    const updatedUrl = queryString ? baseURL + "?" + queryString : baseURL;
    return updatedUrl + fragment;
  }

  // packages/url/build-module/prepend-http.js
  var USABLE_HREF_REGEXP = /^(?:[a-z]+:|#|\?|\.|\/)/i;
  function prependHTTP(url) {
    if (!url) {
      return url;
    }
    url = url.trim();
    if (!USABLE_HREF_REGEXP.test(url) && !isEmail(url)) {
      return "http://" + url;
    }
    return url;
  }

  // packages/url/build-module/safe-decode-uri.js
  function safeDecodeURI(uri) {
    try {
      return decodeURI(uri);
    } catch (uriError) {
      return uri;
    }
  }

  // packages/url/build-module/filter-url-for-display.js
  function filterURLForDisplay(url, maxLength = null) {
    if (!url) {
      return "";
    }
    let filteredURL = url.replace(/^[a-z\-.\+]+[0-9]*:(\/\/)?/i, "").replace(/^www\./i, "");
    if (filteredURL.match(/^[^\/]+\/$/)) {
      filteredURL = filteredURL.replace("/", "");
    }
    const fileRegexp = /\/([^\/?]+)\.(?:[\w]+)(?=\?|$)/;
    if (!maxLength || filteredURL.length <= maxLength || !filteredURL.match(fileRegexp)) {
      return filteredURL;
    }
    filteredURL = filteredURL.split("?")[0];
    const urlPieces = filteredURL.split("/");
    const file = urlPieces[urlPieces.length - 1];
    if (file.length <= maxLength) {
      return "\u2026" + filteredURL.slice(-maxLength);
    }
    const index = file.lastIndexOf(".");
    const [fileName, extension] = [
      file.slice(0, index),
      file.slice(index + 1)
    ];
    const truncatedFile = fileName.slice(-3) + "." + extension;
    return file.slice(0, maxLength - truncatedFile.length - 1) + "\u2026" + truncatedFile;
  }

  // packages/url/build-module/clean-for-slug.js
  var import_remove_accents = __toESM(require_remove_accents());
  function cleanForSlug(string) {
    if (!string) {
      return "";
    }
    return (0, import_remove_accents.default)(string).replace(/(&nbsp;|&ndash;|&mdash;)/g, "-").replace(/[\s\./]+/g, "-").replace(/&\S+?;/g, "").replace(/[^\p{L}\p{N}_-]+/gu, "").toLowerCase().replace(/-+/g, "-").replace(/(^-+)|(-+$)/g, "");
  }

  // packages/url/build-module/get-filename.js
  function getFilename(url) {
    let filename;
    if (!url) {
      return;
    }
    try {
      filename = new URL(url, "http://example.com").pathname.split("/").pop();
    } catch (error) {
    }
    if (filename) {
      return filename;
    }
  }

  // packages/url/build-module/normalize-path.js
  function normalizePath(path) {
    const split = path.split("?");
    const query = split[1];
    const base = split[0];
    if (!query) {
      return base;
    }
    return base + "?" + query.split("&").map((entry) => entry.split("=")).map((pair) => pair.map(decodeURIComponent)).sort((a, b) => a[0].localeCompare(b[0])).map((pair) => pair.map(encodeURIComponent)).map((pair) => pair.join("=")).join("&");
  }

  // packages/url/build-module/prepend-https.js
  function prependHTTPS(url) {
    if (!url) {
      return url;
    }
    if (url.startsWith("http://")) {
      return url;
    }
    url = prependHTTP(url);
    return url.replace(/^http:/, "https:");
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
