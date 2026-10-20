(function() {
"use strict";
var wp;
(wp ||= {}).url = (() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
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
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // packages/url/build-module/index.mjs
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

  // packages/url/build-module/is-url.mjs
  function isURL(url) {
    try {
      new URL(url);
      return true;
    } catch {
      return false;
    }
  }

  // packages/url/build-module/is-email.mjs
  var EMAIL_REGEXP = /^(mailto:)?[a-z0-9._%+-]+@[a-z0-9][a-z0-9.-]*\.[a-z]{2,63}$/i;
  function isEmail(email) {
    return EMAIL_REGEXP.test(email);
  }

  // packages/url/build-module/is-phone-number.mjs
  var PHONE_REGEXP = /^(tel:)?(\+)?\d{6,15}$/;
  function isPhoneNumber(phoneNumber) {
    phoneNumber = phoneNumber.replace(/[-.() ]/g, "");
    return PHONE_REGEXP.test(phoneNumber);
  }

  // packages/url/build-module/get-protocol.mjs
  function getProtocol(url) {
    const matches = /^([^\s:]+:)/.exec(url);
    if (matches) {
      return matches[1];
    }
  }

  // packages/url/build-module/is-valid-protocol.mjs
  function isValidProtocol(protocol) {
    if (!protocol) {
      return false;
    }
    return /^[a-z\-.\+]+[0-9]*:$/i.test(protocol);
  }

  // packages/url/build-module/get-authority.mjs
  function getAuthority(url) {
    const matches = /^[^\/\s:]+:(?:\/\/)?\/?([^\/\s#?]+)[\/#?]{0,1}\S*$/.exec(
      url
    );
    if (matches) {
      return matches[1];
    }
  }

  // packages/url/build-module/is-valid-authority.mjs
  function isValidAuthority(authority) {
    if (!authority) {
      return false;
    }
    return /^[^\s#?]+$/.test(authority);
  }

  // packages/url/build-module/get-path.mjs
  function getPath(url) {
    const matches = /^[^\/\s:]+:(?:\/\/)?[^\/\s#?]+[\/]([^\s#?]+)[#?]{0,1}\S*$/.exec(url);
    if (matches) {
      return matches[1];
    }
  }

  // packages/url/build-module/is-valid-path.mjs
  function isValidPath(path) {
    if (!path) {
      return false;
    }
    return /^[^\s#?]+$/.test(path);
  }

  // packages/url/build-module/get-query-string.mjs
  function getQueryString(url) {
    let query;
    try {
      query = new URL(url, "http://example.com").search.substring(1);
    } catch {
    }
    if (query) {
      return query;
    }
  }

  // packages/url/build-module/build-query-string.mjs
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

  // packages/url/build-module/is-valid-query-string.mjs
  function isValidQueryString(queryString) {
    if (!queryString) {
      return false;
    }
    return /^[^\s#?\/]+$/.test(queryString);
  }

  // packages/url/build-module/get-path-and-query-string.mjs
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

  // packages/url/build-module/get-fragment.mjs
  function getFragment(url) {
    const matches = /^\S+?(#[^\s\?]*)/.exec(url);
    if (matches) {
      return matches[1];
    }
  }

  // packages/url/build-module/is-valid-fragment.mjs
  function isValidFragment(fragment) {
    if (!fragment) {
      return false;
    }
    return /^#[^\s#?\/]*$/.test(fragment);
  }

  // packages/url/build-module/safe-decode-uri-component.mjs
  function safeDecodeURIComponent(uriComponent) {
    try {
      return decodeURIComponent(uriComponent);
    } catch {
      return uriComponent;
    }
  }

  // packages/url/build-module/get-query-args.mjs
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
      const separatorIndex = keyValue.indexOf("=");
      const hasValue = separatorIndex !== -1;
      const key = safeDecodeURIComponent(
        hasValue ? keyValue.slice(0, separatorIndex) : keyValue
      );
      if (key) {
        const value = hasValue ? safeDecodeURIComponent(
          keyValue.slice(separatorIndex + 1)
        ) : "";
        const segments = key.replace(/\]/g, "").split("[");
        setPath(accumulator, segments, value);
      }
      return accumulator;
    }, /* @__PURE__ */ Object.create(null));
  }

  // packages/url/build-module/add-query-args.mjs
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

  // packages/url/build-module/get-query-arg.mjs
  function getQueryArg(url, arg) {
    return getQueryArgs(url)[arg];
  }

  // packages/url/build-module/has-query-arg.mjs
  function hasQueryArg(url, arg) {
    return getQueryArg(url, arg) !== void 0;
  }

  // packages/url/build-module/remove-query-args.mjs
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

  // packages/url/build-module/prepend-http.mjs
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

  // packages/url/build-module/safe-decode-uri.mjs
  function safeDecodeURI(uri) {
    try {
      return decodeURI(uri);
    } catch {
      return uri;
    }
  }

  // packages/url/build-module/filter-url-for-display.mjs
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
    return (
      // A negative end would be read as an offset from the end of the string,
      // keeping most of the file name and returning more than `maxLength`.
      file.slice(0, Math.max(0, maxLength - truncatedFile.length - 1)) + "\u2026" + truncatedFile
    );
  }

  // packages/url/build-module/remove-accents.mjs
  var CHARS = {
    // Decompositions for Latin-1 Supplement.
    "\xAA": "a",
    "\xBA": "o",
    "\xC0": "A",
    "\xC1": "A",
    "\xC2": "A",
    "\xC3": "A",
    "\xC4": "A",
    "\xC5": "A",
    "\xC6": "AE",
    "\xC7": "C",
    "\xC8": "E",
    "\xC9": "E",
    "\xCA": "E",
    "\xCB": "E",
    "\xCC": "I",
    "\xCD": "I",
    "\xCE": "I",
    "\xCF": "I",
    "\xD0": "D",
    "\xD1": "N",
    "\xD2": "O",
    "\xD3": "O",
    "\xD4": "O",
    "\xD5": "O",
    "\xD6": "O",
    "\xD9": "U",
    "\xDA": "U",
    "\xDB": "U",
    "\xDC": "U",
    "\xDD": "Y",
    "\xDE": "TH",
    "\xDF": "s",
    "\xE0": "a",
    "\xE1": "a",
    "\xE2": "a",
    "\xE3": "a",
    "\xE4": "a",
    "\xE5": "a",
    "\xE6": "ae",
    "\xE7": "c",
    "\xE8": "e",
    "\xE9": "e",
    "\xEA": "e",
    "\xEB": "e",
    "\xEC": "i",
    "\xED": "i",
    "\xEE": "i",
    "\xEF": "i",
    "\xF0": "d",
    "\xF1": "n",
    "\xF2": "o",
    "\xF3": "o",
    "\xF4": "o",
    "\xF5": "o",
    "\xF6": "o",
    "\xF8": "o",
    "\xF9": "u",
    "\xFA": "u",
    "\xFB": "u",
    "\xFC": "u",
    "\xFD": "y",
    "\xFE": "th",
    "\xFF": "y",
    "\xD8": "O",
    // Decompositions for Latin Extended-A.
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
    "\u011D": "g",
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
    "\u0138": "k",
    "\u0139": "L",
    "\u013A": "l",
    "\u013B": "L",
    "\u013C": "l",
    "\u013D": "L",
    "\u013E": "l",
    "\u013F": "L",
    "\u0140": "l",
    "\u0141": "L",
    "\u0142": "l",
    "\u0143": "N",
    "\u0144": "n",
    "\u0145": "N",
    "\u0146": "n",
    "\u0147": "N",
    "\u0148": "n",
    "\u0149": "n",
    "\u014A": "N",
    "\u014B": "n",
    "\u014C": "O",
    "\u014D": "o",
    "\u014E": "O",
    "\u014F": "o",
    "\u0150": "O",
    "\u0151": "o",
    "\u0152": "OE",
    "\u0153": "oe",
    "\u0154": "R",
    "\u0155": "r",
    "\u0156": "R",
    "\u0157": "r",
    "\u0158": "R",
    "\u0159": "r",
    "\u015A": "S",
    "\u015B": "s",
    "\u015C": "S",
    "\u015D": "s",
    "\u015E": "S",
    "\u015F": "s",
    "\u0160": "S",
    "\u0161": "s",
    "\u0162": "T",
    "\u0163": "t",
    "\u0164": "T",
    "\u0165": "t",
    "\u0166": "T",
    "\u0167": "t",
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
    "\u0174": "W",
    "\u0175": "w",
    "\u0176": "Y",
    "\u0177": "y",
    "\u0178": "Y",
    "\u0179": "Z",
    "\u017A": "z",
    "\u017B": "Z",
    "\u017C": "z",
    "\u017D": "Z",
    "\u017E": "z",
    "\u017F": "s",
    // Decompositions for Latin Extended-B.
    "\u018F": "E",
    "\u01DD": "e",
    "\u0218": "S",
    "\u0219": "s",
    "\u021A": "T",
    "\u021B": "t",
    // Euro sign.
    "\u20AC": "E",
    // GBP (Pound) sign.
    "\xA3": "",
    // Vowels with diacritic (Vietnamese). Unmarked.
    "\u01A0": "O",
    "\u01A1": "o",
    "\u01AF": "U",
    "\u01B0": "u",
    // Grave accent.
    "\u1EA6": "A",
    "\u1EA7": "a",
    "\u1EB0": "A",
    "\u1EB1": "a",
    "\u1EC0": "E",
    "\u1EC1": "e",
    "\u1ED2": "O",
    "\u1ED3": "o",
    "\u1EDC": "O",
    "\u1EDD": "o",
    "\u1EEA": "U",
    "\u1EEB": "u",
    "\u1EF2": "Y",
    "\u1EF3": "y",
    // Hook.
    "\u1EA2": "A",
    "\u1EA3": "a",
    "\u1EA8": "A",
    "\u1EA9": "a",
    "\u1EB2": "A",
    "\u1EB3": "a",
    "\u1EBA": "E",
    "\u1EBB": "e",
    "\u1EC2": "E",
    "\u1EC3": "e",
    "\u1EC8": "I",
    "\u1EC9": "i",
    "\u1ECE": "O",
    "\u1ECF": "o",
    "\u1ED4": "O",
    "\u1ED5": "o",
    "\u1EDE": "O",
    "\u1EDF": "o",
    "\u1EE6": "U",
    "\u1EE7": "u",
    "\u1EEC": "U",
    "\u1EED": "u",
    "\u1EF6": "Y",
    "\u1EF7": "y",
    // Tilde.
    "\u1EAA": "A",
    "\u1EAB": "a",
    "\u1EB4": "A",
    "\u1EB5": "a",
    "\u1EBC": "E",
    "\u1EBD": "e",
    "\u1EC4": "E",
    "\u1EC5": "e",
    "\u1ED6": "O",
    "\u1ED7": "o",
    "\u1EE0": "O",
    "\u1EE1": "o",
    "\u1EEE": "U",
    "\u1EEF": "u",
    "\u1EF8": "Y",
    "\u1EF9": "y",
    // Acute accent.
    "\u1EA4": "A",
    "\u1EA5": "a",
    "\u1EAE": "A",
    "\u1EAF": "a",
    "\u1EBE": "E",
    "\u1EBF": "e",
    "\u1ED0": "O",
    "\u1ED1": "o",
    "\u1EDA": "O",
    "\u1EDB": "o",
    "\u1EE8": "U",
    "\u1EE9": "u",
    // Dot below.
    "\u1EA0": "A",
    "\u1EA1": "a",
    "\u1EAC": "A",
    "\u1EAD": "a",
    "\u1EB6": "A",
    "\u1EB7": "a",
    "\u1EB8": "E",
    "\u1EB9": "e",
    "\u1EC6": "E",
    "\u1EC7": "e",
    "\u1ECA": "I",
    "\u1ECB": "i",
    "\u1ECC": "O",
    "\u1ECD": "o",
    "\u1ED8": "O",
    "\u1ED9": "o",
    "\u1EE2": "O",
    "\u1EE3": "o",
    "\u1EE4": "U",
    "\u1EE5": "u",
    "\u1EF0": "U",
    "\u1EF1": "u",
    "\u1EF4": "Y",
    "\u1EF5": "y",
    // Vowels with diacritic (Chinese, Hanyu Pinyin).
    "\u0251": "a",
    // Macron.
    "\u01D5": "U",
    "\u01D6": "u",
    // Acute accent.
    "\u01D7": "U",
    "\u01D8": "u",
    // Caron.
    "\u01CD": "A",
    "\u01CE": "a",
    "\u01CF": "I",
    "\u01D0": "i",
    "\u01D1": "O",
    "\u01D2": "o",
    "\u01D3": "U",
    "\u01D4": "u",
    "\u01D9": "U",
    "\u01DA": "u",
    // Grave accent.
    "\u01DB": "U",
    "\u01DC": "u"
  };
  var CHARS_REGEXP = new RegExp(
    `[${Object.keys(CHARS).join("")}]`,
    "g"
  );
  function removeAccents(string) {
    return string.normalize("NFC").replace(CHARS_REGEXP, (char) => CHARS[char]);
  }

  // packages/url/build-module/clean-for-slug.mjs
  function stripTags(string) {
    let result = "";
    let depth = 0;
    let quote = "";
    for (let i = 0; i < string.length; i++) {
      const char = string[i];
      if (depth === 0) {
        if (char === "<" && !/[ \t\n\v\f\r]/.test(string[i + 1] ?? "")) {
          if (string.startsWith("<!--", i)) {
            const end = string.indexOf("-->", i + 2);
            if (end === -1) {
              break;
            }
            i = end + 2;
          } else {
            depth = 1;
          }
        } else {
          result += char;
        }
      } else if (quote) {
        if (char === quote) {
          quote = "";
        }
      } else if (char === '"' || char === "'") {
        quote = char;
      } else if (char === "<") {
        depth++;
      } else if (char === ">") {
        depth--;
      }
    }
    return result;
  }
  function truncateEncoded(string, length) {
    let result = "";
    let encodedLength = 0;
    for (const char of string) {
      const codePoint = char.codePointAt(0) ?? 0;
      let charLength = 12;
      if (codePoint < 128) {
        charLength = 1;
      } else if (codePoint < 2048) {
        charLength = 6;
      } else if (codePoint < 65536) {
        charLength = 9;
      }
      if (encodedLength + charLength > length) {
        break;
      }
      result += char;
      encodedLength += charLength;
    }
    return result;
  }
  function cleanForSlug(string) {
    if (!string) {
      return "";
    }
    const lowercase = stripTags(removeAccents(string)).replace(/%(?![0-9a-fA-F]{2})/g, "").toLowerCase();
    return (
      // Like WordPress, keep what fits in 200 percent-encoded characters,
      // the length of the slug column.
      truncateEncoded(lowercase, 200).replace(/[\u00a0\u2011\u2013\u2014/]/g, "-").replace(
        /&nbsp;|&#8209;|&#160;|&ndash;|&#8211;|&mdash;|&#8212;/g,
        "-"
      ).replace(
        /[\u00ad\u00a1\u00bf\u00ab\u00bb\u2039\u203a\u2018-\u201f\u2022\u00a9\u00ae\u00b0\u2026\u2122\u00b4\u02ca\u0301\u0341\u0300\u0304\u030c\u200b-\u200f\u202a-\u202e\ufeff\ufffc]/g,
        ""
      ).replace(/[\u2000-\u200a\u2028\u2029\u202f]/g, "-").replace(/\u00d7/g, "x").replace(/&.+?;/g, "").replace(/\./g, "-").replace(/[^%a-z0-9 _\-\u0080-\uffff]/g, "").replace(/ +/g, "-").replace(/-+/g, "-").replace(/(^-+)|(-+$)/g, "")
    );
  }

  // packages/url/build-module/get-filename.mjs
  function getFilename(url) {
    let filename;
    if (!url) {
      return;
    }
    try {
      filename = new URL(url, "http://example.com").pathname.split("/").pop();
    } catch {
    }
    if (filename) {
      return filename;
    }
  }

  // packages/url/build-module/normalize-path.mjs
  function normalizePath(path) {
    const separatorIndex = path.indexOf("?");
    if (separatorIndex === -1) {
      return path;
    }
    const base = path.slice(0, separatorIndex);
    const query = path.slice(separatorIndex + 1);
    if (!query) {
      return base;
    }
    return base + "?" + query.split("&").map((entry) => entry.split("=")).map((pair) => pair.map(safeDecodeURIComponent)).sort((a, b) => a[0].localeCompare(b[0])).map((pair) => pair.map(encodeURIComponent)).map((pair) => pair.join("=")).join("&");
  }

  // packages/url/build-module/prepend-https.mjs
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
(window.wp ||= {}).url = wp.url;
})();
