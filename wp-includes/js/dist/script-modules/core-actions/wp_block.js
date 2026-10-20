var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __commonJS = (cb, mod) => function __require() {
  return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
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

// package-external:@wordpress/i18n
var require_i18n = __commonJS({
  "package-external:@wordpress/i18n"(exports, module) {
    module.exports = window.wp.i18n;
  }
});

// package-external:@wordpress/patterns
var require_patterns = __commonJS({
  "package-external:@wordpress/patterns"(exports, module) {
    module.exports = window.wp.patterns;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
  }
});

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
  }
});

// node_modules/.store/remove-accents@0.5.0-S_lnImruWgc6kBSjIJlPgw/node_modules/remove-accents/index.js
var require_remove_accents = __commonJS({
  "node_modules/.store/remove-accents@0.5.0-S_lnImruWgc6kBSjIJlPgw/node_modules/remove-accents/index.js"(exports, module) {
    var characterMap = {
      "À": "A",
      "Á": "A",
      "Â": "A",
      "Ã": "A",
      "Ä": "A",
      "Å": "A",
      "Ấ": "A",
      "Ắ": "A",
      "Ẳ": "A",
      "Ẵ": "A",
      "Ặ": "A",
      "Æ": "AE",
      "Ầ": "A",
      "Ằ": "A",
      "Ȃ": "A",
      "Ả": "A",
      "Ạ": "A",
      "Ẩ": "A",
      "Ẫ": "A",
      "Ậ": "A",
      "Ç": "C",
      "Ḉ": "C",
      "È": "E",
      "É": "E",
      "Ê": "E",
      "Ë": "E",
      "Ế": "E",
      "Ḗ": "E",
      "Ề": "E",
      "Ḕ": "E",
      "Ḝ": "E",
      "Ȇ": "E",
      "Ẻ": "E",
      "Ẽ": "E",
      "Ẹ": "E",
      "Ể": "E",
      "Ễ": "E",
      "Ệ": "E",
      "Ì": "I",
      "Í": "I",
      "Î": "I",
      "Ï": "I",
      "Ḯ": "I",
      "Ȋ": "I",
      "Ỉ": "I",
      "Ị": "I",
      "Ð": "D",
      "Ñ": "N",
      "Ò": "O",
      "Ó": "O",
      "Ô": "O",
      "Õ": "O",
      "Ö": "O",
      "Ø": "O",
      "Ố": "O",
      "Ṍ": "O",
      "Ṓ": "O",
      "Ȏ": "O",
      "Ỏ": "O",
      "Ọ": "O",
      "Ổ": "O",
      "Ỗ": "O",
      "Ộ": "O",
      "Ờ": "O",
      "Ở": "O",
      "Ỡ": "O",
      "Ớ": "O",
      "Ợ": "O",
      "Ù": "U",
      "Ú": "U",
      "Û": "U",
      "Ü": "U",
      "Ủ": "U",
      "Ụ": "U",
      "Ử": "U",
      "Ữ": "U",
      "Ự": "U",
      "Ý": "Y",
      "à": "a",
      "á": "a",
      "â": "a",
      "ã": "a",
      "ä": "a",
      "å": "a",
      "ấ": "a",
      "ắ": "a",
      "ẳ": "a",
      "ẵ": "a",
      "ặ": "a",
      "æ": "ae",
      "ầ": "a",
      "ằ": "a",
      "ȃ": "a",
      "ả": "a",
      "ạ": "a",
      "ẩ": "a",
      "ẫ": "a",
      "ậ": "a",
      "ç": "c",
      "ḉ": "c",
      "è": "e",
      "é": "e",
      "ê": "e",
      "ë": "e",
      "ế": "e",
      "ḗ": "e",
      "ề": "e",
      "ḕ": "e",
      "ḝ": "e",
      "ȇ": "e",
      "ẻ": "e",
      "ẽ": "e",
      "ẹ": "e",
      "ể": "e",
      "ễ": "e",
      "ệ": "e",
      "ì": "i",
      "í": "i",
      "î": "i",
      "ï": "i",
      "ḯ": "i",
      "ȋ": "i",
      "ỉ": "i",
      "ị": "i",
      "ð": "d",
      "ñ": "n",
      "ò": "o",
      "ó": "o",
      "ô": "o",
      "õ": "o",
      "ö": "o",
      "ø": "o",
      "ố": "o",
      "ṍ": "o",
      "ṓ": "o",
      "ȏ": "o",
      "ỏ": "o",
      "ọ": "o",
      "ổ": "o",
      "ỗ": "o",
      "ộ": "o",
      "ờ": "o",
      "ở": "o",
      "ỡ": "o",
      "ớ": "o",
      "ợ": "o",
      "ù": "u",
      "ú": "u",
      "û": "u",
      "ü": "u",
      "ủ": "u",
      "ụ": "u",
      "ử": "u",
      "ữ": "u",
      "ự": "u",
      "ý": "y",
      "ÿ": "y",
      "Ā": "A",
      "ā": "a",
      "Ă": "A",
      "ă": "a",
      "Ą": "A",
      "ą": "a",
      "Ć": "C",
      "ć": "c",
      "Ĉ": "C",
      "ĉ": "c",
      "Ċ": "C",
      "ċ": "c",
      "Č": "C",
      "č": "c",
      "C̆": "C",
      "c̆": "c",
      "Ď": "D",
      "ď": "d",
      "Đ": "D",
      "đ": "d",
      "Ē": "E",
      "ē": "e",
      "Ĕ": "E",
      "ĕ": "e",
      "Ė": "E",
      "ė": "e",
      "Ę": "E",
      "ę": "e",
      "Ě": "E",
      "ě": "e",
      "Ĝ": "G",
      "Ǵ": "G",
      "ĝ": "g",
      "ǵ": "g",
      "Ğ": "G",
      "ğ": "g",
      "Ġ": "G",
      "ġ": "g",
      "Ģ": "G",
      "ģ": "g",
      "Ĥ": "H",
      "ĥ": "h",
      "Ħ": "H",
      "ħ": "h",
      "Ḫ": "H",
      "ḫ": "h",
      "Ĩ": "I",
      "ĩ": "i",
      "Ī": "I",
      "ī": "i",
      "Ĭ": "I",
      "ĭ": "i",
      "Į": "I",
      "į": "i",
      "İ": "I",
      "ı": "i",
      "Ĳ": "IJ",
      "ĳ": "ij",
      "Ĵ": "J",
      "ĵ": "j",
      "Ķ": "K",
      "ķ": "k",
      "Ḱ": "K",
      "ḱ": "k",
      "K̆": "K",
      "k̆": "k",
      "Ĺ": "L",
      "ĺ": "l",
      "Ļ": "L",
      "ļ": "l",
      "Ľ": "L",
      "ľ": "l",
      "Ŀ": "L",
      "ŀ": "l",
      "Ł": "l",
      "ł": "l",
      "Ḿ": "M",
      "ḿ": "m",
      "M̆": "M",
      "m̆": "m",
      "Ń": "N",
      "ń": "n",
      "Ņ": "N",
      "ņ": "n",
      "Ň": "N",
      "ň": "n",
      "ŉ": "n",
      "N̆": "N",
      "n̆": "n",
      "Ō": "O",
      "ō": "o",
      "Ŏ": "O",
      "ŏ": "o",
      "Ő": "O",
      "ő": "o",
      "Œ": "OE",
      "œ": "oe",
      "P̆": "P",
      "p̆": "p",
      "Ŕ": "R",
      "ŕ": "r",
      "Ŗ": "R",
      "ŗ": "r",
      "Ř": "R",
      "ř": "r",
      "R̆": "R",
      "r̆": "r",
      "Ȓ": "R",
      "ȓ": "r",
      "Ś": "S",
      "ś": "s",
      "Ŝ": "S",
      "ŝ": "s",
      "Ş": "S",
      "Ș": "S",
      "ș": "s",
      "ş": "s",
      "Š": "S",
      "š": "s",
      "Ţ": "T",
      "ţ": "t",
      "ț": "t",
      "Ț": "T",
      "Ť": "T",
      "ť": "t",
      "Ŧ": "T",
      "ŧ": "t",
      "T̆": "T",
      "t̆": "t",
      "Ũ": "U",
      "ũ": "u",
      "Ū": "U",
      "ū": "u",
      "Ŭ": "U",
      "ŭ": "u",
      "Ů": "U",
      "ů": "u",
      "Ű": "U",
      "ű": "u",
      "Ų": "U",
      "ų": "u",
      "Ȗ": "U",
      "ȗ": "u",
      "V̆": "V",
      "v̆": "v",
      "Ŵ": "W",
      "ŵ": "w",
      "Ẃ": "W",
      "ẃ": "w",
      "X̆": "X",
      "x̆": "x",
      "Ŷ": "Y",
      "ŷ": "y",
      "Ÿ": "Y",
      "Y̆": "Y",
      "y̆": "y",
      "Ź": "Z",
      "ź": "z",
      "Ż": "Z",
      "ż": "z",
      "Ž": "Z",
      "ž": "z",
      "ſ": "s",
      "ƒ": "f",
      "Ơ": "O",
      "ơ": "o",
      "Ư": "U",
      "ư": "u",
      "Ǎ": "A",
      "ǎ": "a",
      "Ǐ": "I",
      "ǐ": "i",
      "Ǒ": "O",
      "ǒ": "o",
      "Ǔ": "U",
      "ǔ": "u",
      "Ǖ": "U",
      "ǖ": "u",
      "Ǘ": "U",
      "ǘ": "u",
      "Ǚ": "U",
      "ǚ": "u",
      "Ǜ": "U",
      "ǜ": "u",
      "Ứ": "U",
      "ứ": "u",
      "Ṹ": "U",
      "ṹ": "u",
      "Ǻ": "A",
      "ǻ": "a",
      "Ǽ": "AE",
      "ǽ": "ae",
      "Ǿ": "O",
      "ǿ": "o",
      "Þ": "TH",
      "þ": "th",
      "Ṕ": "P",
      "ṕ": "p",
      "Ṥ": "S",
      "ṥ": "s",
      "X́": "X",
      "x́": "x",
      "Ѓ": "Г",
      "ѓ": "г",
      "Ќ": "К",
      "ќ": "к",
      "A̋": "A",
      "a̋": "a",
      "E̋": "E",
      "e̋": "e",
      "I̋": "I",
      "i̋": "i",
      "Ǹ": "N",
      "ǹ": "n",
      "Ồ": "O",
      "ồ": "o",
      "Ṑ": "O",
      "ṑ": "o",
      "Ừ": "U",
      "ừ": "u",
      "Ẁ": "W",
      "ẁ": "w",
      "Ỳ": "Y",
      "ỳ": "y",
      "Ȁ": "A",
      "ȁ": "a",
      "Ȅ": "E",
      "ȅ": "e",
      "Ȉ": "I",
      "ȉ": "i",
      "Ȍ": "O",
      "ȍ": "o",
      "Ȑ": "R",
      "ȑ": "r",
      "Ȕ": "U",
      "ȕ": "u",
      "B̌": "B",
      "b̌": "b",
      "Č̣": "C",
      "č̣": "c",
      "Ê̌": "E",
      "ê̌": "e",
      "F̌": "F",
      "f̌": "f",
      "Ǧ": "G",
      "ǧ": "g",
      "Ȟ": "H",
      "ȟ": "h",
      "J̌": "J",
      "ǰ": "j",
      "Ǩ": "K",
      "ǩ": "k",
      "M̌": "M",
      "m̌": "m",
      "P̌": "P",
      "p̌": "p",
      "Q̌": "Q",
      "q̌": "q",
      "Ř̩": "R",
      "ř̩": "r",
      "Ṧ": "S",
      "ṧ": "s",
      "V̌": "V",
      "v̌": "v",
      "W̌": "W",
      "w̌": "w",
      "X̌": "X",
      "x̌": "x",
      "Y̌": "Y",
      "y̌": "y",
      "A̧": "A",
      "a̧": "a",
      "B̧": "B",
      "b̧": "b",
      "Ḑ": "D",
      "ḑ": "d",
      "Ȩ": "E",
      "ȩ": "e",
      "Ɛ̧": "E",
      "ɛ̧": "e",
      "Ḩ": "H",
      "ḩ": "h",
      "I̧": "I",
      "i̧": "i",
      "Ɨ̧": "I",
      "ɨ̧": "i",
      "M̧": "M",
      "m̧": "m",
      "O̧": "O",
      "o̧": "o",
      "Q̧": "Q",
      "q̧": "q",
      "U̧": "U",
      "u̧": "u",
      "X̧": "X",
      "x̧": "x",
      "Z̧": "Z",
      "z̧": "z",
      "й": "и",
      "Й": "И",
      "ё": "е",
      "Ё": "Е"
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

// package-external:@wordpress/blob
var require_blob = __commonJS({
  "package-external:@wordpress/blob"(exports, module) {
    module.exports = window.wp.blob;
  }
});

// package-external:@wordpress/primitives
var require_primitives = __commonJS({
  "package-external:@wordpress/primitives"(exports, module) {
    module.exports = window.wp.primitives;
  }
});

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// packages/core-actions/build-module/wp_block/duplicate-pattern/action.mjs
var import_i18n = __toESM(require_i18n(), 1);
var import_patterns = __toESM(require_patterns(), 1);

// packages/core-actions/build-module/lock-unlock.mjs
var import_private_apis = __toESM(require_private_apis(), 1);
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/core-actions"
);

// packages/core-actions/build-module/wp_block/duplicate-pattern/action.mjs
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
var { CreatePatternModalContents, useDuplicatePatternProps } = unlock(import_patterns.privateApis);
var duplicatePattern = {
  isEligible: (item) => item.type !== "wp_template_part",
  RenderModal: ({ items, closeModal }) => {
    const [item] = items;
    const duplicatedProps = useDuplicatePatternProps({
      pattern: item,
      onSuccess: () => closeModal?.()
    });
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      CreatePatternModalContents,
      {
        onClose: closeModal,
        confirmLabel: (0, import_i18n._x)("Duplicate", "action label"),
        ...duplicatedProps
      }
    );
  }
};
var action_default = duplicatePattern;

// node_modules/.store/tslib@2.8.1-LT-dxu1EkVy7OUDyVf5glQ/node_modules/tslib/tslib.es6.mjs
var __assign = function() {
  __assign = Object.assign || function __assign2(t2) {
    for (var s2, i2 = 1, n2 = arguments.length; i2 < n2; i2++) {
      s2 = arguments[i2];
      for (var p2 in s2) if (Object.prototype.hasOwnProperty.call(s2, p2)) t2[p2] = s2[p2];
    }
    return t2;
  };
  return __assign.apply(this, arguments);
};

// node_modules/.store/lower-case@2.0.2-5YZP5nczP5_f_8Vt5KBPjA/node_modules/lower-case/dist.es2015/index.js
function lowerCase(str) {
  return str.toLowerCase();
}

// node_modules/.store/no-case@3.0.4-R4Hzt6qn-erEsTak7ojDpg/node_modules/no-case/dist.es2015/index.js
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

// node_modules/.store/dot-case@3.0.4-hi-rLWlWmiZ169n4dWcbUA/node_modules/dot-case/dist.es2015/index.js
function dotCase(input, options) {
  if (options === void 0) {
    options = {};
  }
  return noCase(input, __assign({ delimiter: "." }, options));
}

// node_modules/.store/param-case@3.0.4-rdAht_VwpGpefPRylN2bTA/node_modules/param-case/dist.es2015/index.js
function paramCase(input, options) {
  if (options === void 0) {
    options = {};
  }
  return dotCase(input, __assign({ delimiter: "-" }, options));
}

// node_modules/.store/client-zip@2.4.5-5vcV2f6Upgr_SIgmkeGQpA/node_modules/client-zip/index.js
"stream" in Blob.prototype || Object.defineProperty(Blob.prototype, "stream", { value() {
  return new Response(this).body;
} }), "setBigUint64" in DataView.prototype || Object.defineProperty(DataView.prototype, "setBigUint64", { value(e2, n2, t2) {
  const i2 = Number(0xffffffffn & n2), r2 = Number(n2 >> 32n);
  this.setUint32(e2 + (t2 ? 0 : 4), i2, t2), this.setUint32(e2 + (t2 ? 4 : 0), r2, t2);
} });
var e = (e2) => new DataView(new ArrayBuffer(e2));
var n = (e2) => new Uint8Array(e2.buffer || e2);
var t = (e2) => new TextEncoder().encode(String(e2));
var i = (e2) => Math.min(4294967295, Number(e2));
var r = (e2) => Math.min(65535, Number(e2));
function f(e2, i2) {
  if (void 0 === i2 || i2 instanceof Date || (i2 = new Date(i2)), e2 instanceof File) return { isFile: 1, t: i2 || new Date(e2.lastModified), i: e2.stream() };
  if (e2 instanceof Response) return { isFile: 1, t: i2 || new Date(e2.headers.get("Last-Modified") || Date.now()), i: e2.body };
  if (void 0 === i2) i2 = /* @__PURE__ */ new Date();
  else if (isNaN(i2)) throw new Error("Invalid modification date.");
  if (void 0 === e2) return { isFile: 0, t: i2 };
  if ("string" == typeof e2) return { isFile: 1, t: i2, i: t(e2) };
  if (e2 instanceof Blob) return { isFile: 1, t: i2, i: e2.stream() };
  if (e2 instanceof Uint8Array || e2 instanceof ReadableStream) return { isFile: 1, t: i2, i: e2 };
  if (e2 instanceof ArrayBuffer || ArrayBuffer.isView(e2)) return { isFile: 1, t: i2, i: n(e2) };
  if (Symbol.asyncIterator in e2) return { isFile: 1, t: i2, i: o(e2[Symbol.asyncIterator]()) };
  throw new TypeError("Unsupported input format.");
}
function o(e2, n2 = e2) {
  return new ReadableStream({ async pull(n3) {
    let t2 = 0;
    for (; n3.desiredSize > t2; ) {
      const i2 = await e2.next();
      if (!i2.value) {
        n3.close();
        break;
      }
      {
        const e3 = a(i2.value);
        n3.enqueue(e3), t2 += e3.byteLength;
      }
    }
  }, cancel(e3) {
    n2.throw?.(e3);
  } });
}
function a(e2) {
  return "string" == typeof e2 ? t(e2) : e2 instanceof Uint8Array ? e2 : n(e2);
}
function s(e2, i2, r2) {
  let [f2, o2] = (function(e3) {
    return e3 ? e3 instanceof Uint8Array ? [e3, 1] : ArrayBuffer.isView(e3) || e3 instanceof ArrayBuffer ? [n(e3), 1] : [t(e3), 0] : [void 0, 0];
  })(i2);
  if (e2 instanceof File) return { o: d(f2 || t(e2.name)), u: BigInt(e2.size), l: o2 };
  if (e2 instanceof Response) {
    const n2 = e2.headers.get("content-disposition"), i3 = n2 && n2.match(/;\s*filename\*?=["']?(.*?)["']?$/i), a2 = i3 && i3[1] || e2.url && new URL(e2.url).pathname.split("/").findLast(Boolean), s2 = a2 && decodeURIComponent(a2), u2 = r2 || +e2.headers.get("content-length");
    return { o: d(f2 || t(s2)), u: BigInt(u2), l: o2 };
  }
  return f2 = d(f2, void 0 !== e2 || void 0 !== r2), "string" == typeof e2 ? { o: f2, u: BigInt(t(e2).length), l: o2 } : e2 instanceof Blob ? { o: f2, u: BigInt(e2.size), l: o2 } : e2 instanceof ArrayBuffer || ArrayBuffer.isView(e2) ? { o: f2, u: BigInt(e2.byteLength), l: o2 } : { o: f2, u: u(e2, r2), l: o2 };
}
function u(e2, n2) {
  return n2 > -1 ? BigInt(n2) : e2 ? void 0 : 0n;
}
function d(e2, n2 = 1) {
  if (!e2 || e2.every(((c) => 47 === c))) throw new Error("The file must have a name.");
  if (n2) for (; 47 === e2[e2.length - 1]; ) e2 = e2.subarray(0, -1);
  else 47 !== e2[e2.length - 1] && (e2 = new Uint8Array([...e2, 47]));
  return e2;
}
var l = new Uint32Array(256);
for (let e2 = 0; e2 < 256; ++e2) {
  let n2 = e2;
  for (let e3 = 0; e3 < 8; ++e3) n2 = n2 >>> 1 ^ (1 & n2 && 3988292384);
  l[e2] = n2;
}
function y(e2, n2 = 0) {
  n2 ^= -1;
  for (var t2 = 0, i2 = e2.length; t2 < i2; t2++) n2 = n2 >>> 8 ^ l[255 & n2 ^ e2[t2]];
  return (-1 ^ n2) >>> 0;
}
function w(e2, n2, t2 = 0) {
  const i2 = e2.getSeconds() >> 1 | e2.getMinutes() << 5 | e2.getHours() << 11, r2 = e2.getDate() | e2.getMonth() + 1 << 5 | e2.getFullYear() - 1980 << 9;
  n2.setUint16(t2, i2, 1), n2.setUint16(t2 + 2, r2, 1);
}
function B({ o: e2, l: n2 }, t2) {
  return 8 * (!n2 || (t2 ?? (function(e3) {
    try {
      b.decode(e3);
    } catch {
      return 0;
    }
    return 1;
  })(e2)));
}
var b = new TextDecoder("utf8", { fatal: 1 });
function p(t2, i2 = 0) {
  const r2 = e(30);
  return r2.setUint32(0, 1347093252), r2.setUint32(4, 754976768 | i2), w(t2.t, r2, 10), r2.setUint16(26, t2.o.length, 1), n(r2);
}
async function* g(e2) {
  let { i: n2 } = e2;
  if ("then" in n2 && (n2 = await n2), n2 instanceof Uint8Array) yield n2, e2.m = y(n2, 0), e2.u = BigInt(n2.length);
  else {
    e2.u = 0n;
    const t2 = n2.getReader();
    for (; ; ) {
      const { value: n3, done: i2 } = await t2.read();
      if (i2) break;
      e2.m = y(n3, e2.m), e2.u += BigInt(n3.length), yield n3;
    }
  }
}
function I(t2, r2) {
  const f2 = e(16 + (r2 ? 8 : 0));
  return f2.setUint32(0, 1347094280), f2.setUint32(4, t2.isFile ? t2.m : 0, 1), r2 ? (f2.setBigUint64(8, t2.u, 1), f2.setBigUint64(16, t2.u, 1)) : (f2.setUint32(8, i(t2.u), 1), f2.setUint32(12, i(t2.u), 1)), n(f2);
}
function v(t2, r2, f2 = 0, o2 = 0) {
  const a2 = e(46);
  return a2.setUint32(0, 1347092738), a2.setUint32(4, 755182848), a2.setUint16(8, 2048 | f2), w(t2.t, a2, 12), a2.setUint32(16, t2.isFile ? t2.m : 0, 1), a2.setUint32(20, i(t2.u), 1), a2.setUint32(24, i(t2.u), 1), a2.setUint16(28, t2.o.length, 1), a2.setUint16(30, o2, 1), a2.setUint16(40, t2.isFile ? 33204 : 16893, 1), a2.setUint32(42, i(r2), 1), n(a2);
}
function h(t2, i2, r2) {
  const f2 = e(r2);
  return f2.setUint16(0, 1, 1), f2.setUint16(2, r2 - 4, 1), 16 & r2 && (f2.setBigUint64(4, t2.u, 1), f2.setBigUint64(12, t2.u, 1)), f2.setBigUint64(r2 - 8, i2, 1), n(f2);
}
function D(e2) {
  return e2 instanceof File || e2 instanceof Response ? [[e2], [e2]] : [[e2.input, e2.name, e2.size], [e2.input, e2.lastModified]];
}
var S = (e2) => (function(e3) {
  let n2 = BigInt(22), t2 = 0n, i2 = 0;
  for (const r2 of e3) {
    if (!r2.o) throw new Error("Every file must have a non-empty name.");
    if (void 0 === r2.u) throw new Error(`Missing size for file "${new TextDecoder().decode(r2.o)}".`);
    const e4 = r2.u >= 0xffffffffn, f2 = t2 >= 0xffffffffn;
    t2 += BigInt(46 + r2.o.length + (e4 && 8)) + r2.u, n2 += BigInt(r2.o.length + 46 + (12 * f2 | 28 * e4)), i2 || (i2 = e4);
  }
  return (i2 || t2 >= 0xffffffffn) && (n2 += BigInt(76)), n2 + t2;
})((function* (e3) {
  for (const n2 of e3) yield s(...D(n2)[0]);
})(e2));
function A(e2, n2 = {}) {
  const t2 = { "Content-Type": "application/zip", "Content-Disposition": "attachment" };
  return ("bigint" == typeof n2.length || Number.isInteger(n2.length)) && n2.length > 0 && (t2["Content-Length"] = String(n2.length)), n2.metadata && (t2["Content-Length"] = String(S(n2.metadata))), new Response(N(e2, n2), { headers: t2 });
}
function N(t2, a2 = {}) {
  const u2 = (function(e2) {
    const n2 = e2[Symbol.iterator in e2 ? Symbol.iterator : Symbol.asyncIterator]();
    return { async next() {
      const e3 = await n2.next();
      if (e3.done) return e3;
      const [t3, i2] = D(e3.value);
      return { done: 0, value: Object.assign(f(...i2), s(...t3)) };
    }, throw: n2.throw?.bind(n2), [Symbol.asyncIterator]() {
      return this;
    } };
  })(t2);
  return o((async function* (t3, f2) {
    const o2 = [];
    let a3 = 0n, s2 = 0n, u3 = 0;
    for await (const e2 of t3) {
      const n2 = B(e2, f2.buffersAreUTF8);
      yield p(e2, n2), yield new Uint8Array(e2.o), e2.isFile && (yield* g(e2));
      const t4 = e2.u >= 0xffffffffn, i2 = 12 * (a3 >= 0xffffffffn) | 28 * t4;
      yield I(e2, t4), o2.push(v(e2, a3, n2, i2)), o2.push(e2.o), i2 && o2.push(h(e2, a3, i2)), t4 && (a3 += 8n), s2++, a3 += BigInt(46 + e2.o.length) + e2.u, u3 || (u3 = t4);
    }
    let d2 = 0n;
    for (const e2 of o2) yield e2, d2 += BigInt(e2.length);
    if (u3 || a3 >= 0xffffffffn) {
      const t4 = e(76);
      t4.setUint32(0, 1347094022), t4.setBigUint64(4, BigInt(44), 1), t4.setUint32(12, 755182848), t4.setBigUint64(24, s2, 1), t4.setBigUint64(32, s2, 1), t4.setBigUint64(40, d2, 1), t4.setBigUint64(48, a3, 1), t4.setUint32(56, 1347094023), t4.setBigUint64(64, a3 + d2, 1), t4.setUint32(72, 1, 1), yield n(t4);
    }
    const l2 = e(22);
    l2.setUint32(0, 1347093766), l2.setUint16(8, r(s2), 1), l2.setUint16(10, r(s2), 1), l2.setUint32(12, i(d2), 1), l2.setUint32(16, i(a3), 1), yield n(l2);
  })(u2, a2), u2);
}

// packages/core-actions/build-module/wp_block/export-pattern/action.mjs
var import_remove_accents = __toESM(require_remove_accents(), 1);
var import_blob = __toESM(require_blob(), 1);
var import_i18n3 = __toESM(require_i18n(), 1);

// packages/icons/build-module/library/download.mjs
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var download_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.Path, { d: "M12 3V16M6 10.5L12 16L17.5 10.5M4.75 15V19.25H19.25V15" }) });

// packages/core-actions/build-module/shared/utils.mjs
var import_html_entities = __toESM(require_html_entities(), 1);
var import_i18n2 = __toESM(require_i18n(), 1);
function getItemTitle(item, fallback = (0, import_i18n2.__)("(no title)")) {
  let title = "";
  if (typeof item.title === "string") {
    title = (0, import_html_entities.decodeEntities)(item.title);
  } else if (item.title && "rendered" in item.title) {
    title = (0, import_html_entities.decodeEntities)(item.title.rendered);
  } else if (item.title && "raw" in item.title) {
    title = (0, import_html_entities.decodeEntities)(item.title.raw);
  }
  return title || fallback;
}

// packages/core-actions/build-module/wp_block/export-pattern/action.mjs
function getJsonFromItem(item) {
  return JSON.stringify(
    {
      __file: item.type,
      title: getItemTitle(item),
      content: typeof item.content === "string" ? item.content : item.content?.raw,
      syncStatus: item.wp_pattern_sync_status
    },
    null,
    2
  );
}
var exportPattern = {
  icon: download_default,
  isEligible: (item) => item.type === "wp_block",
  callback: async (items) => {
    if (items.length === 1) {
      return (0, import_blob.downloadBlob)(
        `${paramCase(
          (0, import_remove_accents.default)(
            getItemTitle(items[0]) || items[0].slug
          )
        )}.json`,
        getJsonFromItem(items[0]),
        "application/json"
      );
    }
    const nameCount = {};
    const filesToZip = items.map((item) => {
      const name = paramCase(
        (0, import_remove_accents.default)(getItemTitle(item) || item.slug)
      );
      nameCount[name] = (nameCount[name] || 0) + 1;
      return {
        name: `${name + (nameCount[name] > 1 ? "-" + (nameCount[name] - 1) : "")}.json`,
        lastModified: /* @__PURE__ */ new Date(),
        input: getJsonFromItem(item)
      };
    });
    return (0, import_blob.downloadBlob)(
      (0, import_i18n3.__)("patterns-export") + ".zip",
      await A(filesToZip).blob(),
      "application/zip"
    );
  }
};
var action_default2 = exportPattern;

// packages/core-actions/build-module/wp_block/index.mjs
var actions = {
  "duplicate-pattern": action_default,
  "export-pattern": action_default2
};
var wp_block_default = actions;
export {
  wp_block_default as default
};
